<?php
/*
Plugin Name: Where I've Been Map
Plugin URI: http://www.bigblogmap.com/your-map
Description: Easily show where you've visited and link to those pages
Version: 0.7
Author: Ben Jones
Author URI: http://www.benjaminpeterjones.com
License: GPLv2
*/
?><?php
include_once "config.php";
// load the controller class
include_once "whereivebeencontroller.class.php" ;

// Get profile_id
if(isset($_GET["pid"])){
	$pid = $_GET["pid"];
}else if(isset($_POST["pid"]) ){
	$pid = $_POST["pid"];	
}else{
	$pid = 1;
}

// Grab here the id of the WP user
if(isset($pid) && $pid>0){
	$user_id = $pid;
}else{
	$user_id = wiveb_user_id();	
}


// Get Mode
if(is_admin() ){
	$mode = "admin_panel";	
}else{
	$mode = "public_profile";
}

$wiveb_controller = new WhereIveBeenController($user_id, $mode);

function wiveb_addHeaderHtml(){
	global $wiveb_controller,$width_param;

	print '<link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />';
	print '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/whereivebeen/css/styles.css" />';

	print '	<!-- jQuery -->';
	print '	<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>';
  	print '	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>';

	print '	<!--Load the AJAX API-->';
	print '	<script type="text/javascript" src="https://www.google.com/jsapi"></script>';
	print '	<script type="text/javascript">';
	print 'var main_url = "'.  get_bloginfo('wpurl') . '";';
	print '</script>';

	$wiveb_controller->views->get_head_js();

	print '	<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/whereivebeen/js/wiveb-custom.js"></script>';
}

function wiveb_addAdminHeaderHtml($hook){
	global $wiveb_controller,$width_param;
	if( 'settings_page_WHEREIVEBEEN_SETTINGS' != $hook ) return;

	wiveb_addHeaderHtml();
	print '	<script type="text/javascript">';
	print 'var ajax_url = "'.  admin_url( 'admin-ajax.php' ) . '";';
	print '</script>';
	print '	<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/whereivebeen/js/wiveb-admin.js"></script>';
}

function wiveb_admin_menu() {
	if( is_admin() ){
		add_options_page(WHEREIVEBEEN, WHEREIVEBEEN_TITLE, 'administrator','WHEREIVEBEEN_SETTINGS', 'wiveb_admin_page');
	}
}

function wiveb_admin_page($atts){
	global $wiveb_controller;
	$output = "";

	extract( shortcode_atts( array(
		'width' => '556',
		'height' => '347',
	), $atts ) ); 

	$output .= "<div>";
	$output .= "<h2>" . WHEREIVEBEEN_TITLE . " - Admin </h2>";
	$output .= $wiveb_controller->display($width,$height);
	$output .= "</div>";

	echo $output;

	return $output;	
}

function wiveb_public_profile($atts){
	global $wiveb_controller;
	$output = "";

	extract( shortcode_atts( array(
		'width' => '556',
		'height' => '347',
	), $atts ) ); 

	// Find details of the user
	$user = get_user_by( 'id', $wiveb_controller->model->user_id );	
	$username =  $user->user_login;	

	$output .= "<div>";	
	$output .= $wiveb_controller->display($width,$height);
	$output .= "</div>";

	return $output;
}

function wiveb_Install( ){
	global $wpdb;
	// Countries
	$table_name = $wpdb->prefix . "whereivebeen_countries";

	$sql =  "CREATE TABLE IF NOT EXISTS $table_name (
  	`id` int(11) NOT NULL AUTO_INCREMENT,
  	`title` varchar(255) NOT NULL,
	PRIMARY KEY(id)
	);";
	@$wpdb->query($sql);	

	$sql =  "INSERT INTO `$table_name` (`id`, `title`) VALUES
	(1, 'Afghanistan'),
	(2, 'Albania'),
	(3, 'Algeria'),
	(4, 'American Samoa'),
	(5, 'Andorra'),
	(6, 'Angola'),
	(7, 'Anguilla'),
	(8, 'Antarctica'),
	(9, 'Antigua and Barbuda'),
	(10, 'Argentina'),
	(11, 'Armenia'),
	(12, 'Armenia'),
	(13, 'Aruba'),
	(14, 'Australia'),
	(15, 'Austria'),
	(16, 'Azerbaijan'),
	(17, 'Azerbaijan'),
	(18, 'Bahamas'),
	(19, 'Bahrain'),
	(20, 'Bangladesh'),
	(21, 'Barbados'),
	(22, 'Belarus'),
	(23, 'Belgium'),
	(24, 'Belize'),
	(25, 'Benin'),
	(26, 'Bermuda'),
	(27, 'Bhutan'),
	(28, 'Bolivia'),
	(29, 'Bosnia and Herzegovina'),
	(30, 'Botswana'),
	(31, 'Bouvet Island'),
	(32, 'Brazil'),
	(33, 'British Indian Ocean Territory'),
	(34, 'Brunei Darussalam'),
	(35, 'Bulgaria'),
	(36, 'Burkina Faso'),
	(37, 'Burundi'),
	(38, 'Cambodia'),
	(39, 'Cameroon'),
	(40, 'Canada'),
	(41, 'Cape Verde'),
	(42, 'Cayman Islands'),
	(43, 'Central African Republic'),
	(44, 'Chad'),
	(45, 'Chile'),
	(46, 'China'),
	(47, 'Christmas Island'),
	(48, 'Cocos (Keeling) Islands'),
	(49, 'Colombia'),
	(50, 'Comoros'),
	(51, 'Congo'),
	(52, 'Congo, The Democratic Republic of The'),
	(53, 'Cook Islands'),
	(54, 'Costa Rica'),
	(55, 'Cote D''ivoire'),
	(56, 'Croatia'),
	(57, 'Cuba'),
	(58, 'Cyprus'),
	(60, 'Czech Republic'),
	(61, 'Denmark'),
	(62, 'Djibouti'),
	(63, 'Dominica'),
	(64, 'Dominican Republic'),
	(65, 'Easter Island'),
	(66, 'Ecuador'),
	(67, 'Egypt'),
	(68, 'El Salvador'),
	(69, 'Equatorial Guinea'),
	(70, 'Eritrea'),
	(71, 'Estonia'),
	(72, 'Ethiopia'),
	(73, 'Falkland Islands (Malvinas)'),
	(74, 'Faroe Islands'),
	(75, 'Fiji'),
	(76, 'Finland'),
	(77, 'France'),
	(78, 'French Guiana'),
	(79, 'French Polynesia'),
	(80, 'French Southern Territories'),
	(81, 'Gabon'),
	(82, 'Gambia'),
	(83, 'Georgia'),
	(85, 'Germany'),
	(86, 'Ghana'),
	(87, 'Gibraltar'),
	(88, 'Greece'),
	(89, 'Greenland'),
	(91, 'Grenada'),
	(92, 'Guadeloupe'),
	(93, 'Guam'),
	(94, 'Guatemala'),
	(95, 'Guinea'),
	(96, 'Guinea-bissau'),
	(97, 'Guyana'),
	(98, 'Haiti'),
	(99, 'Heard Island and Mcdonald Islands'),
	(100, 'Honduras'),
	(101, 'Hong Kong'),
	(102, 'Hungary'),
	(103, 'Iceland'),
	(104, 'India'),
	(105, 'Indonesia'),
	(106, 'Indonesia'),
	(107, 'Iran'),
	(108, 'Iraq'),
	(109, 'Ireland'),
	(110, 'Israel'),
	(111, 'Italy'),
	(112, 'Jamaica'),
	(113, 'Japan'),
	(114, 'Jordan'),
	(115, 'Kazakhstan'),
	(116, 'Kazakhstan'),
	(117, 'Kenya'),
	(118, 'Kiribati'),
	(119, 'Korea, North'),
	(120, 'Korea, South'),
	(121, 'Kosovo'),
	(122, 'Kuwait'),
	(123, 'Kyrgyzstan'),
	(124, 'Laos'),
	(125, 'Latvia'),
	(126, 'Lebanon'),
	(127, 'Lesotho'),
	(128, 'Liberia'),
	(129, 'Libyan Arab Jamahiriya'),
	(130, 'Liechtenstein'),
	(131, 'Lithuania'),
	(132, 'Luxembourg'),
	(133, 'Macau'),
	(134, 'Macedonia'),
	(135, 'Madagascar'),
	(136, 'Malawi'),
	(137, 'Malaysia'),
	(138, 'Maldives'),
	(139, 'Mali'),
	(140, 'Malta'),
	(141, 'Marshall Islands'),
	(142, 'Martinique'),
	(143, 'Mauritania'),
	(144, 'Mauritius'),
	(145, 'Mayotte'),
	(146, 'Mexico'),
	(147, 'Micronesia, Federated States of'),
	(148, 'Moldova, Republic of'),
	(149, 'Monaco'),
	(150, 'Mongolia'),
	(151, 'Montenegro'),
	(152, 'Montserrat'),
	(153, 'Morocco'),
	(154, 'Mozambique'),
	(155, 'Myanmar'),
	(156, 'Namibia'),
	(157, 'Nauru'),
	(158, 'Nepal'),
	(159, 'Netherlands'),
	(160, 'Netherlands Antilles'),
	(161, 'New Caledonia'),
	(162, 'New Zealand'),
	(163, 'Nicaragua'),
	(164, 'Niger'),
	(165, 'Nigeria'),
	(166, 'Niue'),
	(167, 'Norfolk Island'),
	(168, 'Northern Mariana Islands'),
	(169, 'Norway'),
	(170, 'Oman'),
	(171, 'Pakistan'),
	(172, 'Palau'),
	(173, 'Palestinian Territory'),
	(174, 'Panama'),
	(175, 'Papua New Guinea'),
	(176, 'Paraguay'),
	(177, 'Peru'),
	(178, 'Philippines'),
	(179, 'Pitcairn'),
	(180, 'Poland'),
	(181, 'Portugal'),
	(182, 'Puerto Rico'),
	(183, 'Qatar'),
	(184, 'Reunion'),
	(185, 'Romania'),
	(186, 'Russia'),
	(187, 'Russia'),
	(188, 'Rwanda'),
	(189, 'Saint Helena'),
	(190, 'Saint Kitts and Nevis'),
	(191, 'Saint Lucia'),
	(192, 'Saint Pierre and Miquelon'),
	(193, 'Saint Vincent and The Grenadines'),
	(194, 'Samoa'),
	(195, 'San Marino'),
	(196, 'Sao Tome and Principe'),
	(197, 'Saudi Arabia'),
	(198, 'Senegal'),
	(199, 'Serbia and Montenegro'),
	(200, 'Seychelles'),
	(201, 'Sierra Leone'),
	(202, 'Singapore'),
	(203, 'Slovakia'),
	(204, 'Slovenia'),
	(205, 'Solomon Islands'),
	(206, 'Somalia'),
	(207, 'South Africa'),
	(208, 'South Georgia and The South Sandwich Islands'),
	(209, 'Spain'),
	(210, 'Sri Lanka'),
	(211, 'Sudan'),
	(212, 'Suriname'),
	(213, 'Svalbard and Jan Mayen'),
	(214, 'Swaziland'),
	(215, 'Sweden'),
	(216, 'Switzerland'),
	(217, 'Syria'),
	(218, 'Taiwan'),
	(219, 'Tajikistan'),
	(220, 'Tanzania, United Republic of'),
	(221, 'Thailand'),
	(222, 'Timor-leste'),
	(223, 'Togo'),
	(224, 'Tokelau'),
	(225, 'Tonga'),
	(226, 'Trinidad and Tobago'),
	(227, 'Tunisia'),
	(228, 'Turkey'),
	(229, 'Turkey'),
	(230, 'Turkmenistan'),
	(231, 'Turks and Caicos Islands'),
	(232, 'Tuvalu'),
	(233, 'Uganda'),
	(234, 'Ukraine'),
	(235, 'United Arab Emirates'),
	(236, 'United Kingdom'),
	(237, 'United States'),
	(238, 'United States Minor Outlying Islands'),
	(239, 'Uruguay'),
	(240, 'Uzbekistan'),
	(241, 'Vanuatu'),
	(242, 'Vatican City'),
	(243, 'Venezuela'),
	(244, 'Vietnam'),
	(245, 'Virgin Islands, British'),
	(246, 'Virgin Islands, U.S.'),
	(247, 'Wallis and Futuna'),
	(248, 'Western Sahara'),
	(249, 'Yemen'),
	(250, 'Yemen'),
	(251, 'Zambia'),
	(252, 'Zimbabwe');";
	@$wpdb->query($sql);	
	
	// whereivebeen_user_countries
	$table_name = $wpdb->prefix . "whereivebeen_user_countries";
	$sql =  "CREATE TABLE IF NOT EXISTS $table_name (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) NOT NULL,
	  `country_id` int(11) NOT NULL,
	  `status_id` int(11) NOT NULL DEFAULT '-1',
	  `url` varchar(255) NOT NULL,
	  `new_window` int(11) NOT NULL DEFAULT '0',
	  `created` datetime NOT NULL,
	PRIMARY KEY(id)
	);";
	@$wpdb->query($sql);		

	// whereivebeen_user_settings
	$table_name = $wpdb->prefix . "whereivebeen_settings";
	$sql =  "CREATE TABLE IF NOT EXISTS $table_name (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `user_id` int(11) NOT NULL,
	  `setting` varchar(255) NOT NULL,
	  `setting_value` varchar(255) NOT NULL,
	PRIMARY KEY(id)
	);";
	@$wpdb->query($sql);		

	$sql =  "INSERT INTO `$table_name` (`id`, `user_id`, `setting`, `setting_value`) VALUES
	(1,1, 'color_visited','#00ff00'),
	(2,1, 'color_tovisit','#ff0000'),
	(3,1, 'color_inactive','#dddddd'),
	(4,1, 'message','put your message here');";
	@$wpdb->query($sql);	


}

function wiveb_Uninstall(){
	global $wpdb;
	
	@$wpdb->query("drop table " . $wpdb->prefix . "whereivebeen_countries");
	@$wpdb->query("drop table " . $wpdb->prefix . "whereivebeen_user_countries");
	@$wpdb->query("drop table " . $wpdb->prefix . "whereivebeen_settings");
}

function wiveb_getusername(){
	global $current_user;
    get_currentuserinfo();
	
	return $current_user->user_login;
}

function wiveb_user_id(){
	if ( !function_exists('wp_get_current_user') ) {
		require (ABSPATH . WPINC . '/pluggable.php');
	}	
	global $current_user;
    get_currentuserinfo();

	if($current_user->ID >0){
		return $current_user->ID;
	}else{
		return -1;
	}
}

// These are admin ajax functions
function wiveb_add_country_callback(){
	// Set Mode
	$mode = "admin_create";

	// Get Country ID
	$user_id 	= $_POST["user_id"];
	$country_id = $_POST["country_id"];
	$status_id 	= $_POST["status_id"];
	$url 		= $_POST["url"];

	if( isset($_POST["new_window"]) && $_POST["new_window"]=="1"){
		$new_window	= "1";
	}else{
		$new_window	= "0";
	}


	$controller = new WhereIveBeenController($user_id, $mode);
	$controller->model->add_country($user_id, $country_id, $status_id, $url, $new_window);
	$output = $controller->display();
	echo $output;	
	die();
}

function wiveb_delete_country_callback(){
	// Set Mode
	$mode = "admin_delete";

	// Get Country ID
	$user_id 	= $_GET["user_id"];
	$id 		= $_GET["id"];

	$controller = new WhereIveBeenController($user_id, $mode);
	$controller->model->delete_country($id);
	$output = $controller->display();
	echo $output;	
	die();	
}

function wiveb_update_settings_callback(){
	// Set Mode
	$mode = "save_settings";

	// Get data
	$user_id 		= $_POST["user_id"];
	$visited_color 	= $_POST["visited_color"];
	$tovisit_color 	= $_POST["tovisit_color"];
	$inactive_color = $_POST["inactive_color"];
	$message 		= $_POST["message"];

	$controller = new WhereIveBeenController($user_id, $mode);
	$controller->model->update_settings($user_id, $visited_color, $tovisit_color, $inactive_color, $message);
	$output = $controller->display();	
	die();
}

function wiveb_reload_settings_callback(){
	// Grab here the id of the WP user
	$user_id 	= $_GET["user_id"];

	// Set Mode
	$mode = "load_settings";

	$controller = new WhereIveBeenController($user_id, $mode);
	$controller->display();	
	die();		
}

function wiveb_reload_countries_callback(){
	// Grab here the id of the WP user
	$user_id 	= $_GET["user_id"];

	// Set Mode
	$mode = "load_data_country";

	$controller = new WhereIveBeenController($user_id, $mode);
	$output = $controller->display();	
	echo $output;
	die();	
}

function wiveb_reload_links_callback(){
	// Grab here the id of the WP user
	$user_id 	= $_GET["user_id"];

	// Set Mode
	$mode = "load_data_link";

	$controller = new WhereIveBeenController($user_id, $mode);
	$output = $controller->display();	
	echo $output;
	die();
}

// Register Actions
if ( is_admin() ){
	// The menu
	add_action('admin_menu', 'wiveb_admin_menu');

	// Ajax actions
	add_action('wp_ajax_wiveb_add_country'		, 'wiveb_add_country_callback');
	add_action('wp_ajax_wiveb_delete_country'	, 'wiveb_delete_country_callback');
	add_action('wp_ajax_wiveb_reload_countries'	, 'wiveb_reload_countries_callback');
	add_action('wp_ajax_wiveb_reload_links'		, 'wiveb_reload_links_callback');
	add_action('wp_ajax_wiveb_reload_settings'	, 'wiveb_reload_settings_callback');
	add_action('wp_ajax_wiveb_update_settings'	, 'wiveb_update_settings_callback');

	// Load header files only at the settings page
	add_action('admin_enqueue_scripts', 'wiveb_addAdminHeaderHtml');

}else{
	add_action('wp_head', 'wiveb_addHeaderHtml');
}


// Register Hooks
register_activation_hook(	__FILE__, 'wiveb_Install');
register_deactivation_hook(	__FILE__, 'wiveb_Uninstall');

// add the short_code hook
add_shortcode( 'where-been-map', 'wiveb_public_profile' );
?>