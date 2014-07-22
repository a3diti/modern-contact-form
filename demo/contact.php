<?php

# debug ~ show errors
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);

session_start();
include 'functions.php';
include 'config-demo.php';

?><!doctype html>
<html>
<head>
    <title><?php echo $config['title'];?></title>
    <meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="expires" content="-1">
	<script src="<?php echo (($config['cdn'])?'http://code.jquery.com':'inc/vendor');?>/jquery-1.11.0.min.js"></script>
	<link rel="stylesheet" href="<?php echo (($config['cdn'])?'http://netdna.bootstrapcdn.com/bootstrap/3.1.1':'inc/vendor/bootstrap-3.1.1-dist');?>/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (($config['cdn'])?'http://netdna.bootstrapcdn.com/bootstrap/3.1.1':'inc/vendor/bootstrap-3.1.1-dist');?>/css/bootstrap-theme.min.css">
	<script src="<?php echo (($config['cdn'])?'http://netdna.bootstrapcdn.com/bootstrap/3.1.1':'inc/vendor/bootstrap-3.1.1-dist');?>/js/bootstrap.min.js"></script>
	<link href="<?php echo (($config['cdn'])?'http://fonts.googleapis.com/css?family=Sniglet':'inc/vendor/google.font.sniglet/font.css');?>" rel="stylesheet" type="text/css">
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
<script>
var phpSelf="<?php echo $_SERVER['PHP_SELF'];?>";
$(function(){
	if(window==window.top){$('.close').hide()}
	var useCaptcha=$('#cnts-captcha').length;
	if(useCaptcha){_loadCaptcha()}
	$('[title]').tooltip();
    $('.cnts-send').click(function(){
        var inputs={},send={},stop=false;
        $('.cnts-input').each(function(){
        	var t=$(this);
        	if( (typeof t.attr('required-data')!=='undefined') && ( _validateThis(t)===false ) ){
        		stop=true;
        	}
			inputs[ t.attr('id') ] = (t.attr('type')=='checkbox')?t.find('span.active').map(function(){return $(this).text()}).get().join(','):t.val();
        });
        if(stop){return false}
        send['cnts'] = JSON.stringify(inputs);
        if(useCaptcha){send['captcha'] = $('#cnts-captcha').val();}
        $.ajax({
        	url:phpSelf,
        	type:'post',
        	data:send,
        	success:function(data){
        		if(data==='captcha error'){
        			_loadCaptcha();
        			$('#cnts-captcha').val('').focus().css('border','1px solid rgba(255, 66, 66, 0.7)');
        		}else if(data==='ok'){
        			_contactAlert(1);
        		}else{
        			_contactAlert(6,'Error <br/>'+data+"<br/>Dismissing in 6 seconds");
        			console.log(data);
        		}
        	}
		});
    });
	$('.cnts-input[required-data]').keypress(function(){
		var t=$(this);
		_validateThis(t);
		setTimeout(function(){_validateThis(t)},500);
	});
	$('.cnts-alert').click(function(){
		$(this).fadeOut();
		$('.cnts-data input,.cnts-data textarea').val('');
		if(useCaptcha){ _loadCaptcha(); }
		$('.glyphicon.form-control-feedback').attr('class','glyphicon form-control-feedback');
		$('#cnts-captcha').css('border','0');
	});
	$('.cnts-input[type="checkbox"] span').click(function(){
		var chkBox=$(this).find('input');
		$(this).toggleClass("active");
		chkBox.prop("checked", !chkBox.prop("checked"));
	}).find('input[type="checkbox"]').click(function(){$(this).parent().click().toggleClass("active");});
});

function _loadCaptcha(){
	$.post(phpSelf,{'getCaptcha':true},function(d){$('.cnts-img').attr('src',d);});
}
function regTest(id,raw){
	var r={};<?php
	foreach ($config['inputs'] as $id => $in) {
		if($in['type']!=='checkbox'){
			$regex = (isset($config['inputs'][ $id ]['regex'])) ? $config['inputs'][ $id ]['regex'] : $config['defaults'][ $in['type'] ]['regex'];
			echo 'r[\'cnts-'.$in['type'].'-'.$id.'\']='.$regex.';';
		}
	} ?>
	return r[id].test(raw);
}
function _validateThis(that) {
	var value = that.val();
	var parent = that.parent();
	var feedback = that.next('.form-control-feedback');
	var valid = regTest( that.attr('id'), value );
    if ( value !== '' && valid ){
        parent.removeClass('has-error').addClass('has-success');
        feedback.removeClass('glyphicon-remove').addClass('glyphicon-ok');
        return true;
    } else {
        parent.removeClass('has-success').addClass('has-error');
        feedback.removeClass('glyphicon-ok').addClass('glyphicon-remove');
        return false;
    }
}
function _contactAlert(t,m){
	$('.cnts-alert .center').html((typeof m!=='undefined'?m:"<?php echo $config['msg-sent']; ?>")).css('border-color',(t===1?'#0AC400':'red'));3
	$('.cnts-alert').fadeIn().css('display','table');
	if(t!==1){setTimeout(function(){$('.cnts-alert').fadeOut()},t*1000)}
}
</script>
</head>
<body>
	
	<button type="button" class="close" style="padding:8px" title="Close" data-placement="left"><span aria-hidden="true">&times;</span></button>
	<div class="cnts-holder theme-<?php echo $config['theme'];?>">
		<div class="cnts-data">
			<p class="cnts-head"><?php echo $config['head'];?></p>
			<?php
			foreach ($config['inputs'] as $key => $in) {
				_inputHTML($in,$key);
			}
			if($config['captcha']){ ?>
				<div class="form-group" style="clear:right">
					<img class="cnts-img"<?php echo (($config['icons'])?' style="margin-left: 34px;"':'');?>>
					<span class="glyphicon glyphicon-refresh newCaptcha" onclick="_loadCaptcha()" title="Load new captcha"></span>
					<input type="text" class="form-control input-sm" id="cnts-captcha" placeholder="type what you see" title="type what you see in the image">
				</div>
			<?php } ?>
			<div class="cnts-send-class">
				<button class="btn btn-primary btn-sm cnts-send"><span class="glyphicon glyphicon-send"></span> &nbsp; Send</button>
			</div>
			<?php if(!empty($config['info'])&&$config['info']!==false){ ?><p class="tail-info"> &nbsp; <?php echo htmlspecialchars($config['info']);?></p><?php } ?>
		</div>
        <div class="cnts-alert"><div class="middle"><div class="center">Message sent, thankyou</div></div></div>
	</div>


<style>
	body{background-color: transparent;}
	.cnts-holder{min-width:<?php echo $config['min-width'];?>;max-width:<?php echo $config['width'];?>;margin:<?php echo ($config['centered']?'0 auto':'0');?>;}
	.cnts-data{<?php echo (($config['centered'])?'width:100%;margin:0 auto;':'');?>}
	.cnts-data input{display:inline-block;}
	.cnts-data .form-group{margin-bottom: 10px !important}
	.cnts-data{position:relative;z-index:1;}
	.cnts-data .form-control-feedback{text-shadow:0 1px 0px black}
	.cnts-holder{background-clip: border-box;background-origin: padding-box;background-position: 0 0;font-family:'Sniglet', cursive;padding: 15px;border-radius: 3px;box-shadow: 0 0 3px #000;}
	.cnts-holder input,.cnts-holder textarea{border-radius: 2px;border: 0 none;box-shadow: 0 0 1px 1px #000000 inset;font-size: 13px;color:#333;}
	.cnts-holder .form-group,.cnts-holder p{margin:0}
	.cnts-head{padding:7px 0;font-size: 16pt;text-align: center;color:#FFF;text-shadow: 0 0 1px #000000;}
	.cnts-send{border-bottom: 3px solid #104D82;padding-bottom: -3px;line-height: 1.4;padding: 4px 10px 3px 10px;}
	.cnts-send:hover,.cnts-send:focus{border-bottom: 3px solid #00335E;}
	#cnts-captcha{max-width:130px;display: inline-block;}
	.cnts-img{float:left;margin-right:3px;width:160px;height:75px;}
	.newCaptcha{cursor:pointer !important;color:#E9E9E9}
	.newCaptcha:hover,.newCaptcha:focus{color:black;}
	.cnts-before{display:inline-block;line-height:20px;padding:5px 10px;color:#FFF;float:left;cursor:default;}
	.cnts-send-class{clear:both;text-align:right;}
	.cnts-alert{z-index:26;position:absolute;display:none;top:0;left:0;width:100%;height:100%;background-color:rgba(0, 0, 0, 0.6);}
	.cnts-alert .middle{display: table-cell;vertical-align: middle;}
	.cnts-alert .center{width:220px;padding: 25px;border-radius: 5px;text-align:center;margin:0 auto;background-color:white;color:#333;border: 2px solid green;box-shadow:0 0px 4px black;}
	.cnts-input[type="checkbox"]{color:white;text-shadow:0 0 1px black;line-height: 34px;padding-left: 4px}
	.cnts-input[type="checkbox"] span{padding:1px 5px;border-radius:3px;cursor: pointer;}
	.cnts-input[type="checkbox"] span.active{box-shadow: 0 0 1px 0 white}
	.glyphicon{cursor: default}
	.has-error .form-control{box-shadow: 0 0 2px 1px rgba(255,66,66,0.7) inset !important}
	.has-success .form-control{box-shadow: 0 0 5px 2px rgba(0,240,4,0.5) inset !important}
	.form-control-feedback{content:" ";position: relative; display: inline-block !important; font-size: 16px;margin-left: 5px;}
	.form-control-feedback.glyphicon-remove{color:#FF4242}
	.form-control-feedback.glyphicon-ok{color:#00F004}
	.form-group .glyphicon{top:3px;}
	.tail-info{color:#DBDBDB;clear:both;text-shadow:0 0 1px black}
	.theme-green{background: linear-gradient(to right bottom, #185396, #008000) repeat scroll 0 0 rgba(0, 0, 0, 0);border-left:3px solid green;}
	.theme-yellowblue{background: radial-gradient(closest-corner, rgba(0,0,0,0) 60%, rgba(27, 43, 69, 0.2)), -moz-linear-gradient(108deg, #E5EBB5 10%, #003C75 90%) repeat scroll 0 0 #003C75;}
	.theme-yellowblue input,.theme-yellowblue textarea{box-shadow:0 0 1px 1px rgba(0,0,0,0.85) inset !important;}.theme-yellowblue .tail-info{color:#fff;}
	.theme-pink{background: radial-gradient(circle farthest-side at right bottom , #FFD4E1, #1E3075 100%, #0C1545) repeat scroll 0 0 #0C1545;}
	.theme-blue{background: radial-gradient(closest-corner, rgba(0, 0, 0, 0) 60%, rgba(27, 43, 69, 0.2)) repeat scroll 0 0%, -moz-linear-gradient(108deg, #00DDFF 10%, #003c75 90%) repeat scroll 0 0 #003c75;}
	.theme-blue input,.theme-blue textarea{box-shadow:0 0 1px 1px rgba(0,0,0,0.85) inset !important;}.theme-blue .tail-info{color:#fff;}
	.theme-dark{background:radial-gradient(closest-corner, rgba(0, 0, 0, 0) 60%, rgba(140, 140, 140, 0.4)) repeat scroll 0 0%, -moz-linear-gradient(100deg, #999 30%, #333 70%) repeat scroll 0 0 #333;}
	.theme-dark input,.theme-dark textarea{box-shadow:0 0 1px 1px rgba(0,0,0,0.85) inset !important;}.theme-dark .tail-info{color:#fff;}
	@media screen and (max-width: 320px){body{-webkit-text-size-adjust:none;font-family:Helvetica, Arial, Verdana, sans-serif;}.cnts-holder{min-width:auto !important;max-width:auto !important;padding:4px !important;}.cnts-data textarea.cnts-input{max-width:250px !important;}.cnts-input[type="checkbox"]{text-align:left !important;}.cnts-input[type="checkbox"] span{display:block !important;margin-left: 44px;}#cnts-captcha{display: block !important;margin:5px auto 0 auto;}img.cnts-img{float:none!important;}}
	@media screen and (max-width: 240px){.cnts-holder{min-width:auto !important;max-width:auto !important;padding:4px !important;}.cnts-input{max-width:180px!important;}textarea.cnts-input{width:180px;max-width: auto;}.glyphicon.form-control-feedback{display: none!important}}
</style>

</body>
</html>