<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| FACEBOOK APP ID
|--------------------------------------------------------------------------
|
| Facebook App Id is used for facebook plugins
|
|
|
*/
$config['facebookAppId'] = '313390542085484';

/*
|--------------------------------------------------------------------------
| Facebook Profile Id 
|--------------------------------------------------------------------------
|
| Login with facebook ?
|
|	
|
*/
$config['facebookProfileId'] = '401026556600035';

/*
|--------------------------------------------------------------------------
| Facebook Secret  
|--------------------------------------------------------------------------
|
| Login with facebook ?
|
|	
|
*/
$config['facebookSecret'] = '5a9bf54b72761eaf2afd14659bdc595a';

/*
|--------------------------------------------------------------------------
| Google Map API Key  
|--------------------------------------------------------------------------
|
| Want to display google maps ?
|
|	
|
*/
$config['gmapApiKey'] = 'AIzaSyCIUqkFup5Na4Q0XrZFt6BKVWLDjBXif2k';

/*
|--------------------------------------------------------------------------
| Facebook Secret  
|--------------------------------------------------------------------------
|
| Login with facebook ?
|
|	
|
*/
$config['defaultAvailableTonightTime'] = '20:00:00';


/*--------------------- cache time limits  ------------------------*/

$config['cache_main'] = 3600*24*30;

$config['cache_for_day'] = 3600*24;

/*---------------------------------------------*/

$config['review_points']					=	20;
$config['foodie_board_question_points']		=	10;

/*---------------------Multi language --------------------------*/

$config['userPhotoDir']	=	'media/userphoto/';
$config['restPhotoDir']	=	'media/restphoto/';

$config['thumbWidth']	= 128;
$config['thumbHeight']	= 128;

$config['profilePWidth']	= 400;
$config['profilePHeight']	= 320;

$config['showingRestFinder']=true;

$config['fiwareClientId'] = '3f32e62192ed4d5cad07b5457ff1af81';

$config['fiwareSecret'] = '4f0ccd9464124874bc8aa3b68432849f';
