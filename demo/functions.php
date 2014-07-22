<?php
_strip_slashes(); # used if server has get_magic_quotes_gpc() enabled ( autodetected )

if(isset($_POST['getCaptcha'])){ # generate captcha
	include("inc/captcha/captcha.php");
	$_SESSION['captcha'] = simple_php_captcha( array(
		'min_length' => 3,
		'max_length' => 3,
		'fonts' => array('fonts/1.TTF','fonts/2.TTF','fonts/3.TTF','fonts/4.TTF'),
		'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789',
		'min_font_size' => 30,
		'max_font_size' => 30,
		'color' => '#104D82',
		'angle_min' => 0,
		'angle_max' => 40,
		'shadow' => true,
		'shadow_color' => '#FFF',
		'shadow_offset_x' => -1,
		'shadow_offset_y' => 1
	) );
	die($_SESSION['captcha']['image_src']);
}

function _inputHTML($in,$id){
	global $config;
	$placeholder = (!isset($in['placeholder'])?$config['defaults'][$in['type']]['placeholder']:$in['placeholder']);
	$style = (!isset($in['style'])?$config['defaults'][$in['type']]['style']:$in['style']);
	$icon = (($config['icons'])?'<span class="'.(isset($in['icon'])?$in['icon']:$config['defaults'][$in['type']]['icon']).' cnts-before"></span>':'');
	$required = @( (isset($in['required'])?$in['required']:$config['defaults'][$in['type']]['required']) ? ' required-data="'.$in['type'].'"' : '' );
	switch ($in['type']) {
		case 'name':
			echo '<div class="form-group">'
					.$icon.'<input type="text"'.$required.' class="form-control input-sm cnts-input" id="cnts-name-'.$id.'" placeholder="'.$placeholder.'" style="'.$style.'">'
					.'<span class="glyphicon form-control-feedback">&nbsp;</span>'
				.'</div>';
			break;
		case 'custom':
			echo '<div class="form-group">'
					.$icon.'<input type="text"'.$required.' class="form-control input-sm cnts-input" id="cnts-custom-'.$id.'" placeholder="'.$placeholder.'" style="'.$style.'">'
					.'<span class="glyphicon form-control-feedback">&nbsp;</span>'
				.'</div>';
			break;
		case 'mail':
			echo '<div class="form-group">'
					.$icon.'<input type="text"'.$required.' class="form-control input-sm cnts-input" id="cnts-mail-'.$id.'" placeholder="'.$placeholder.'" style="'.$style.'">'
					.'<span class="glyphicon form-control-feedback">&nbsp;</span>'
				.'</div>';
			break;
		case 'phone':
			echo '<div class="form-group">'
					.$icon.'<input type="text"'.$required.' class="form-control input-sm cnts-input" id="cnts-phone-'.$id.'" placeholder="'.$placeholder.'" style="'.$style.'">'
					.'<span class="glyphicon form-control-feedback">&nbsp;</span>'
				.'</div>';
			break;
		case 'textarea':
			echo '<div class="form-group">'
					.$icon.'<textarea class="form-control cnts-input"'.$required.' id="cnts-textarea-'.$id.'" placeholder="'.$placeholder.'" style="'.$style.'"></textarea>'
				.'</div>';
			break;
		case 'checkbox':
			echo '<div class="form-group">'
					.$icon.'<span class="cnts-input" type="checkbox" id="cnts-checkbox-'.$id.'" style="'.$style.'">'
  						.$placeholder.'<span><input type="checkbox"> '.implode('</span> <span><input type="checkbox"> ', $in['checkboxes']).'</span>'
  					.'</span>'
  				.'</div>';
			break;
		case 'number':
			echo '<div class="form-group">'
					.$icon.'<input type="text"'.$required.' class="form-control input-sm cnts-input" id="cnts-number-'.$id.'" placeholder="'.$placeholder.'" style="'.$style.'">'
					.'<span class="glyphicon form-control-feedback">&nbsp;</span>'
				.'</div>';
			break;
	}
}

function _init_notifications(){
	if(!isset($_POST['cnts'])){return false;} # initialize only on post contact data
	header('Content-Type: text/plain'); # set output as text only
	_check_captcha(); #check captcha error
	global $config;
	if(strlen(json_encode($_POST['cnts']))>intval($config['max_post_data'])){die('max size extended');}
	$cnts = json_decode($_POST['cnts'],true);if($cnts===null||empty($cnts)){die('error json');}
	$inputs = _init_validations($cnts);
	foreach ($config['notifications'] as $c) {
		switch ($c['type']) {
			case 'email': # $c['config'] = array(email,subject,from,reply)
				$from = $c['config']['from'] ;
				$message = '
					<table border="0" align="left" cellspacing="0" cellpadding="5" style="font-size:11pt;">
						<thead>
							<tr>
								<th style="width:100px"></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
				';
				foreach ($inputs as $v) {
					if(preg_match('/(mail)/i', $v[0])){ $from = $v[1]; }
					$message .= '<tr><td style="border-top:1px solid black;"><b>'.$v[0].'</b></td><td style="border-top:1px solid black;">'.$v[1].'</td></tr>';
				}
				$message .= '</tbody></table>';
				$headers = "From: ".$from."\r\n".
						   "Reply-To: ".$from."\r\n".
						   // "BCC: 123@123.com,1235@123.com\r\n". # use this for multiple email notifications
						   "MIME-Version: 1.0\r\n".
						   "Content-Type: text/html; charset=ISO-8859-1\r\n";
				mail( $c['config']['email'], $c['config']['subject'], $message, $headers );
				break;
			case 'database': # $c['config'] = array( connection=>array(connInfo,user,pass), show_errors, table, column )
					try{
						$conn = new PDO($c['config']['connection'][0],$c['config']['connection'][1],$c['config']['connection'][2]);
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$st=$conn->prepare('INSERT INTO `'.$c['config']['table'].'` (`'.$c['config']['column'].'`) VALUES (?)');
						$conn->beginTransaction();
						$st->execute(array(json_encode($inputs)));
						$conn->commit();
					}catch(PDOException $e){
						// $conn->rollBack();
						if($c['config']['show_errors']){print_r($e->getMessage());}
						die('db error');
					}
				break;
			case 'file':
				file_put_contents( $c['config']['file'] , json_encode($inputs)."\n" , FILE_APPEND );
				break;
		}
	}
	die('ok');
}

function _init_validations($cnts){
	global $config;
	foreach ($cnts as $key => $value) {
		$reg = '#^(cnts-('.implode('|', array_keys($config['defaults']) ).')-([0-9]*))$#i';
		preg_match($reg, $key, $t);
		if(empty($t)){die('error on keys');}
		list($id, $type) = array(intval($t[3]),$t[2]);
		if(!isset($config['inputs'][ $id ])){print_r($id);print_r($type);die('error on id');}
		$required = (isset($config['inputs'][ $id ]['required'])) ? $config['inputs'][ $id ]['required'] : ((isset($config['defaults'][ $type ]['required']))?$config['defaults'][ $type ]['required']:false);
		$regex = (isset($config['inputs'][ $id ]['regex'])) ? $config['inputs'][ $id ]['regex'] : @$config['defaults'][  $type ]['regex'];
		if( $required && !preg_match($regex,$value) ){
			header("Content-Type: text/plain");die('validation error');
		}else{
			$inKey = (isset($config['inputs'][ $id ]['name'])) ? $config['inputs'][ $id ]['name'] : $type.'-'.$id;
			$r[] = array( $inKey, htmlspecialchars($value) );
		}
	}
	if(!isset($r)){die('err, no inputs!');}
	return $r;
}

function _check_captcha(){
	global $config;
	if( $config['captcha']
		&& isset($_POST['captcha'])
		&& ( strtolower(trim($_POST['captcha'])) !== strtolower($_SESSION['captcha']['code']) )
	){
		die('captcha error');
	}
}

function _strip_slashes(){
	if (get_magic_quotes_gpc()) {
	    function stripslashes_array(&$arr) {
	        foreach ($arr as $k => &$v) {
	            $nk = stripslashes($k);
	            if ($nk != $k) {
	                $arr[$nk] = &$v;
	                unset($arr[$k]);
	            }
	            if (is_array($v)) {
	                stripslashes_array($v);
	            } else {
	                $arr[$nk] = stripslashes($v);
	            }
	        }
	    }
	    stripslashes_array($_POST);
	    stripslashes_array($_GET);
	    stripslashes_array($_REQUEST);
	    stripslashes_array($_COOKIE);
	}
}