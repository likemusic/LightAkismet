<?php

	require_once('LightAkismet/AkismetServiceSingleton.php');
	require_once('LightAkismet/AkismetService.php');
	
	$ApiKey = 'c6de7b25471b';
	$HttpUserAgent = 'LightAkismet/0.1 | LightAkismet/0.1';
	
	$blog = 'http://LightAkismet.net/';
	$user_ip = '127.0.0.1';
	$user_agent = 'HttpAkismetKlient';
	$referrer = 'Localhost';
	$permalink = 'http://LightAkismet.net/Test.php';
	$comment_type = 'Test';
	$comment_author = 'tewta-test-1232222';
	$comment_author_email = 'viagra-test-123@email.com';
	$comment_author_url = 'http://LightAkismet.net/';
	$comment_content = 'Viagra Test';

	$Params = array(
		//'blog' => $blog,//not set - in constructor
		//'user_ip' => $user_ip, // from current request | or from recent changes table for seleted user
		//'user_agent' => $user_agent, //from current request or none
		'referrer' => $referrer, //from current request or none
		'permalink' => $permalink, //from PageTitle
		'comment_type' => $comment_type, //const
		'comment_author' => $comment_author,
		'comment_author_email' => $comment_author_email,
		'comment_author_url' => $comment_author_url,
		'comment_content' => $comment_content
	);
	
	$AkismetServiceSingleton = AkismetServiceSingleton::getInstance();
	//$Res = $AkismetServiceSingleton->checkComment( $ApiKey, $HttpUserAgent, $Params, $blog = null );
	$Res = $AkismetServiceSingleton->submitSpam( $ApiKey, $HttpUserAgent, $Params, $blog = null );
	$Res = $AkismetServiceSingleton->submitHam( $ApiKey, $HttpUserAgent, $Params, $blog = null );
	
	/*
	$Comment = new AkismetComment();
	$Comment->blog = $blog;
	$Comment->user_ip = $user_ip;
	$Comment->user_agent = $user_agent;
	$Comment->refferer = $refferer;
	$Comment->permalink = $permalink;
	$Comment->comment_type = $comment_type;
	$Comment->comment_author = $comment_author;
	$Comment->comment_author_email = $comment_author_email;
	$Comment->comment_author_url = $comment_author_url;
	$Comment->comment_content = $comment_content;
    */

    $AkismetServiceObj = new AkismetService($ApiKey,$HttpUserAgent,$blog);
    $AkismetServiceObj->submitSpam( $Params );
    
?>