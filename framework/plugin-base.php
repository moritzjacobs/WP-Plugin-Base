<?php

/***************************************************
*
*  Plugin Base class with minimum functionality
*
****************************************************

- render(): render template files in views/**.php
	with provided template data as parameter
	
- add_settings_page(): Add a page to the backend
	under "Settings"; load_settings_page() as a 
	default callback.
	
- add_cpt_page(): Add a page to the backend under
	the main menu for a CPT; load_cpt_page() as a 
	default callback.
	
- custom_post_types(): Instantiate a custom post
	type for your plugin
	
- admin_css()/js() / public_css()/js(): helper
	functions for registering scripts and styles

****************************************************/


include_once 'plugin-base-utils.php';

class PluginBase {
	
	public $slug = '';
	public $root_dir = '';
	public $vendor_dir = '';
	
	public function __construct($prefs, $base_location) {
	
		$this->slug = get_called_class();
		$this->root_dir = $base_location;
		$this->vendor_dir = $base_location."/framework/vendor/";
	
		foreach($prefs as $key=>$pref) {
			if (method_exists($this, $key)) {
				call_user_func_array(array($this, $key), array($pref));
			}
		}
	}
	
	
	
	public function render($file, $data=array()) {
		extract($data);
		include($this->root_dir . "/" . $file);
	}
	
	
	
	protected function add_settings_page($title=false, $callback=false) {
		if (!$title) {
			$title = get_called_class() . " " . __("Settings");
		}
		
		if(!$callback) {
			$callback = array($this, "load_settings_page");
		}

		add_action('admin_menu', function() use($title, $callback){
			add_options_page(
				$title,
				$title,
				'manage_options',
				PBUtils::sluggify($title),
				$callback
			);
		});
	}
	
	protected function add_cpt_page($cpt_slug, $title=false, $callback=false) {
		if (!$title) {
			$title = $cpt_slug . " " . __("Settings");
		}
		
		if(!$callback) {
			$callback = array($this, "load_cpt_page");
		}

		add_action('admin_menu', function() use($cpt_slug, $title, $callback){
			add_submenu_page(
				'edit.php?post_type='.$cpt_slug,
				$title,
				$title,
				'manage_options',
				PBUtils::sluggify($title),
				$callback 
			);
		});
	}



	public function load_settings_page() {
		$this->render("views/admin/default.php", array('title'=>get_called_class()));
	}

	public function load_cpt_page() {
		$this->render("views/admin/cpt.php", array('title'=>get_called_class()));
	}

	
	
	private function custom_post_types($cpts) {
		foreach ($cpts as $slug=>$prefs) {
			PBUtils::registerCPT($slug, $prefs);

			$cpt_class = $this->root_dir . "/classes/" . $slug . ".php";
			if (file_exists($cpt_class)) {
				include_once("plugin-base-cpt.php");
				require_once($cpt_class);
			}	
		}
	}
	
	
	private function admin_css($files) {
		foreach($files as $file) {
			add_action( 'admin_enqueue_scripts', function() use ($file){
				wp_enqueue_style($this->slug .'-admin-styles', plugins_url($file, $this->root_dir), array(), $this->version);
			});
		}
	}
	
	
	
	private function admin_js($files) {
		foreach($files as $file) {
			add_action( 'admin_enqueue_scripts', function() use ($file){
				wp_enqueue_script($this->slug .'-admin-script', plugins_url($file, $this->root_dir), array(), $this->version);
			});
		}
	}
	
	
	
	private function public_css($files) {
		foreach($files as $file) {
			add_action( 'wp_enqueue_scripts', function() use ($file){
				wp_enqueue_style($this->slug .'-admin-styles', plugins_url($file, $this->root_dir), array(), $this->version);
			});
		}
	}
	
	
	
	private function public_js($files) {
		foreach($files as $file) {
			add_action( 'wp_enqueue_scripts', function() use ($file){
				wp_enqueue_script($this->slug .'-admin-script', plugins_url($file, $this->root_dir), array(), $this->version);
			});
		}
	}
	
	
}
