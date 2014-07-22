<?php

# set to 4MB the maximum post data size
$config['max_post_data'] = 4*1024*1024;

# use bootstrap/jquery CDN, true or false
$config['cdn'] = false;

# title and head of window
$config['title'] = 'Modern Contact Form v1.0';
$config['head'] = 'Contact Us';

# theme: green yellowblue blue pink dark
$config['theme'] = 'yellowblue';

# bottom info ( &#8226; is a "*" )
$config['info'] = '* once contacted, we will call or email you shortly';

# message sent success
$config['msg-sent'] = 'Message sent, thank you, we will contact you shortly.';

# true or false to use or not captcha so you can stop spammers
$config['captcha'] = true ;


### START CUSTOM INPUTS CONFIGURATION ###
$config['inputs'][] = array(
	'type' => 'name', 					 # type of input: name|mail|phone|textarea|custom|checkbox
	'placeholder' => 'Name', 			 # input box placeholder
	'required' => false,				 # required as a field or not| if set true it will be validated o both client and server side
	'style' => 'max-width:220px;',		 # extra style in css for this input
	'name'=> 'Name',					 # use this name as defined when notified. Default example: cnts-name-0
	'icon' => 'glyphicon glyphicon-user',# icon class, you can get ready class names here: http://getbootstrap.com/components/#glyphicons
	'regex' => '/^([\w]+)$/i',			 # a custom regex
);

// sample mail example
$config['inputs'][] = array(
	'type'=>'mail',
	'required'=>true,
	'placeholder'=>'Your email',
	'name'=>'Contact email',
);

// sample custom example, validate with custom regex
$config['inputs'][] = array(
	'type'=>'custom',
	'required'=>true,
	'placeholder'=>'Reference number',
	'regex'=>'/^([\d]+)$/', #only numbers allowed in this input
	'name'=>'Reference #',
);

//sample phone input data
$config['inputs'][] = array(
	'type'=>'phone',
	'required'=>true,
	'placeholder'=>'Mobile',
	'name'=>'Phone Number',
);

//custom number
$config['inputs'][] = array(
	'type'=>'number',
	'required'=>true,
	'placeholder'=>'Your ID',
	'name'=>'Client ID',
);

//custom checkboxes
$config['inputs'][] = array(
	'type'=>'checkbox',
	'placeholder'=>'My choices :  &nbsp;',
	'checkboxes'=>array('Check1','Check2','Check3'),
	'name'=>'Checked'
);

//custom textarea, will not be validated
$config['inputs'][] = array(
	'type'=>'textarea',
	'required'=>false,
	'placeholder'=>'Type a message',
	'name'=>'Message',
);

### END CUSTOM INPUTS CONFIGURATION ###



### STYLE configuration
$config['icons'] = true ;		# true or false to show or hide icons for all inputs
$config['width'] = '500px' ; 	# window max width in px or in % . Examples: 490px , 800px , 100% etc.
$config['min-width'] = '300px' ;# window min width in px
$config['centered'] = true ;	# true or false if you want the contact holder centered



### DEFAULTS ###
# defaults for inputs, if you dont know what this is then don't modify anything
$config['defaults'] = array(
	'name' => array(
		'icon'=>'glyphicon glyphicon-user', # icon css class
		'placeholder'=>'name',
		'style'=>'max-width:220px;',
		'required'=>true,
		'regex'=>'/^([A-Za-z0-9\s\.\-_]+)$/'
	),
	'mail' => array(
		'icon'=>'glyphicon glyphicon-envelope',
		'placeholder'=>'email address',
		'style'=>'max-width:220px;',
		'required'=>true,
		'regex'=>'/^([\s]*[A-Za-z0-9._-]+[@][A-Za-z0-9.-]+\.[A-Za-z]{2,4}[\s]*)$/'
	),
	'phone' => array(
		'icon'=>'glyphicon glyphicon-earphone',
		'placeholder'=>'phone/mobile number',
		'style'=>'max-width:220px;',
		'required'=>true,
		'regex'=>'/^([0-9\s\t\+\-\(\)]{5,})$/'
	),
	'textarea'=> array(
		'icon'=>'glyphicon glyphicon-align-justify',
		'placeholder'=>'message',
		'style'=>'max-width:310px;min-height:100px;',
		'required'=>false,
		'regex'=>'/^(.*)$/i'
	),
	'checkbox'=>array(
		'icon'=>'glyphicon glyphicon-check',
		'placeholder'=>'Make your choice',
		'style'=>''
	),
	'number' => array(
		'icon'=>'glyphicon glyphicon-align-center',
		'placeholder'=>'number',
		'style'=>'max-width:220px;',
		'required'=>false,
		'regex'=>'/^([0-9]{1,})$/i'
	),
	'custom' => array(
		'icon'=>'glyphicon glyphicon-align-center',
		'placeholder'=>'custom input',
		'style'=>'max-width:220px;',
		'required'=>false,
		'regex'=>'/^(.*)$/i'
	)
);

### START NOTIFICATION CONFIGURATION ###
# notifications type can be set to email , database or file as many as you want
$config['notifications'] = array( # you can use one or multiple notification methods simultaneously
	'notify1' => array( # send to email (note: to make this work you need to put the script on a public server)
		'type' => 'email',
		'config' => array(
			'email' => 'example@mail.com', # set primary destination email address
			'cc' => false, # set false or type one or more emails seperated by "," to send notification to other email/s
			'bcc' => false, # set false or type one or more emails seperated by "," to send notification to other email/s
			'subject' => '*NEW NOTIFICATION ~ website.com',
			'reply' => true,  # true: use the email contact defined by users when contact form was filled as a REPLY email
							  # false: use the 'from' field defined by you below
			'from' => 'info@website.com' # set from which the email is sent
		)
	),
	// 'notify2' => array( # send to database in JSON format
	// 	'type' => 'database',
	// 	'config' => array(
	// 		'connection' => array('mysql:host=localhost;dbname=notifications;charset=latin1;','root','1234'), # array( connInfo, username, password ) ~ only with PDO
	// 		'show_errors'=> false, # true or false to show or not database errors
	// 		'table' => 'data', # table name to insert data
	// 		'column' => 'notification' # column name to insert data
	// 	)
	// ),
	// 'notify3' => array( # sent messages by appending them in a text file in JSON format in each line.
	// 	'type' => 'file',
	// 	'config' => array(
	// 		'file' => 'myNotifications.txt' # filename where to save with desired location if needed
	// 	)
	// ),
	/* ### EXAMPLE
	'notify1' => array( # send to email (note: to make this work you need to put the script on a public server)
		'type' => 'email',
		'config' => array(
			'email' => 'example@mail.com', # set destination email
			'subject' => '*NEW NOTIFICATION ~ website.com',
			'reply' => true,  # true: use the email contact defined by users when contact form was filled as a REPLY email
							  # false: use the 'from' field defined by you below
			'from' => 'info@website.com' # set from which the email is sent
		)
	),
	'notify2' => array( # send to database in JSON format
		'type' => 'database',
		'config' => array(
			'connection' => array('mysql:host=localhost;dbname=notifications;charset=latin1;','root','1234'), # array( connInfo, username, password ) ~ only with PDO
			'show_errors'=> false, # true or false to show or not database errors
			'table' => 'data', # table name to insert data
			'column' => 'notification' # column name to insert data
		)
	),
	'notify3' => array( # sent messages by appending them in a text file in JSON format in each line.
		'type' => 'file',
		'config' => array(
			'file' => 'myNotifications.txt' # filename where to save with desired location if needed
		)
	),
	### */
);
### END NOTIFICATION CONFIGURATION ###

_init_notifications(); # initialize notification system

/*
# Create your database and import this SQL text as input at phpmyadmin or mysql command-line

CREATE TABLE IF NOT EXISTS `data` (
	`id` int(11) NOT NULL PRIMARY KEY,
	`notification` text NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

*/	
