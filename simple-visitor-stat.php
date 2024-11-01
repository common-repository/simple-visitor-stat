<?php
/*
	Plugin Name: Simple visitor stat
	Plugin URI: www.wpcue.com
	Description: Simply Keep track of your site's visitor. It’s fast and light.
	Version: 1.0
	Author: digontoahsan
	Author URI: www.wpcue.com
	License: GPL2
*/

	function smpvstat_modify_menu(){
		add_menu_page( 'Simple visitor stat', 'Visitor stat', 'manage_options', 'smpv-stat', 'smpvstat_options' );
	}
	
	add_action('admin_menu','smpvstat_modify_menu');

	function smpvstat_options(){
		include('smpvstat-admin.php');
	}
	
	define('smpvstat_url',WP_PLUGIN_URL."/simple-visitor-stat/");
	
	register_activation_hook(WP_PLUGIN_DIR.'/simple-visitor-stat/simple-visitor-stat.php','set_simpvstat_options');
	
	function set_simpvstat_options(){
		global $wpdb;
		$ins_q = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."svisitor_stat (		
			`id` int(11) NOT NULL AUTO_INCREMENT,
  			`ip` varchar(100) NOT NULL,
  			`referrer` varchar(5000) NOT NULL,
  			`ua` varchar(5000) NOT NULL,
  			`page` varchar(5000) NOT NULL,
  			`vcount` int(10) NOT NULL,
  			`vtime` datetime NOT NULL,
  			PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
		$wpdb->query($ins_q);
	}
	
	function smpvstat_enqueue() {	
		wp_enqueue_script( 'smpvstat-js-script', smpvstat_url . 'simpvstat.script.js', array( 'jquery' ));
		wp_localize_script( 'smpvstat-js-script', 'smpvstatajx', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'checkReq' => wp_create_nonce( 'svstatauthrequst58qa' )));
	}
	add_action( 'wp_enqueue_scripts', 'smpvstat_enqueue' );
	
	add_action( "wp_ajax_smpvstat_add", "smpvstat_add" );
	add_action( "wp_ajax_nopriv_smpvstat_add", "smpvstat_add" );
	
	
	function smpvstat_add(){
		
		if(!isset($_POST['checkReq']) || !wp_verify_nonce( $_POST['checkReq'], 'svstatauthrequst58qa' )){
			exit;
		}
		$page = esc_url($_POST['path']);
		
		global $wpdb;
	
		$u_ip = $_SERVER['REMOTE_ADDR'];
		$u_ua = $_SERVER['HTTP_USER_AGENT'];
		$referrer = $_SERVER['HTTP_REFERER'];
		
		$check = $wpdb->get_results("select id from ".$wpdb->prefix."svisitor_stat where ip = '".$u_ip."' and page = '".$page."'");
		if($check){
			$query = "update ".$wpdb->prefix."svisitor_stat set vcount = vcount+1,vtime = now() where ip = '".$u_ip."' and page = '".$page."'";
			$wpdb->query($query);
		}
		else
		{
			$query = "insert into ".$wpdb->prefix."svisitor_stat (ip,referrer,ua,page,vcount,vtime) values('".$u_ip."','".$referrer."','".$u_ua."','".$page."',1,now())";
			$wpdb->query($query);
		}
		exit;
	}