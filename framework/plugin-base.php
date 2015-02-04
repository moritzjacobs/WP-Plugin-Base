<?php

/***************************************************
*
*  Plugin Base class with minimum functionality
*
****************************************************/


include_once 'plugin-base-utils.php';

class PluginBase {
		
	/**
	 * Constructor.
	 * 
	 * @access public
	 * @param mixed $prefs
	 * @param mixed $base_location
	 * @return void
	 */
	public function __construct($prefs, $base_location) {
	
		$this->slug = PBUtils::sluggify(get_called_class());
		$this->root_dir = $base_location;
		$this->vendor_dir = $base_location."/framework/vendor/";
		$this->prefs = $prefs;
		
		// make preferences via function calls
		foreach($prefs as $key=>$pref) {
			if (method_exists($this, $key)) {
				call_user_func_array(array($this, $key), array($pref));
			}
		}
	}
	
	
	/**
	 * determine, if the current page belongs to this plugin.
	 * 
	 * @access public
	 * @return void
	 */
	public function is_plugin_screen() {
		$purl = parse_url($_SERVER["REQUEST_URI"]);
		parse_str(@$purl["query"], $query);
		
		$is_menu_page = strpos(@$query["page"], "menu-".$this->slug) === 0;
		$is_settings_page = @$query["page"] == "settings-".$this->slug;
		$is_cpt_page = (@$query["page"] == "cpt-page-".$this->slug) && array_key_exists($query["post_type"], $this->prefs["custom_post_types"]);

		return $is_menu_page || $is_cpt_page || $is_settings_page;
	}
	
	
	
	/**
	 * render views.
	 * 
	 * @access public
	 * @param mixed $file
	 * @param array $data (default: array())
	 * @return void
	 */
	public function render($file, $data=array()) {
		extract($data);
		include($this->root_dir . "/" . $file);
	}
	
	
	/**
	 * add page to sidebar menu.
	 * 
	 * @access protected
	 * @param bool $title (default: false)
	 * @param bool $callback (default: false)
	 * @param int $pos (default: 79)
	 * @return void
	 */
	protected function add_menu_page($title=false, $callback=false, $pos=79) {
		if (!$title) {
			$title = get_called_class() . " " . __("Settings");
		}
		
		if(!$callback) {
			$callback = array($this, "load_menu_page");
		}
		
		$slug = $this->slug;
		add_action('admin_menu', function() use($title, $callback, $pos, $slug){
			add_menu_page(
				$title,
				$title,
				'manage_options',
				'menu-'.$slug,
				$callback,
				'',
				$pos
			);
		});
		
	}
	//  add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	
	
		
	/**
	 * add submenu page to sidebar menu.
	 * 
	 * @access protected
	 * @param bool $title (default: false)
	 * @param bool $callback (default: false)
	 * @param int $pos (default: 79)
	 * @return void
	 */
	protected function add_submenu_page($title=false, $callback=false, $pos=79) {
		if (!$title) {
			$title = get_called_class() . " " . __("Settings");
		}
		
		if(!$callback) {
			$callback = array($this, "load_submenu_page");
		}
		
		$slug = $this->slug;
		$subslug = PBUtils::sluggify($title);
		add_action('admin_menu', function() use($title, $callback, $pos, $slug, $subslug){
			add_submenu_page(
				'menu-'.$slug,
				$title,
				$title,
				'manage_options',
				'menu-'.$slug."-".$subslug,
				$callback
			);
		});
		
	}
	
	
	/**
	 * add page to settings menu.
	 * 
	 * @access protected
	 * @param bool $title (default: false)
	 * @param bool $callback (default: false)
	 * @return void
	 */
	protected function add_settings_page($title=false, $callback=false) {
		if (!$title) {
			$title = get_called_class() . " " . __("Settings");
		}
		
		if(!$callback) {
			$callback = array($this, "load_settings_page");
		}
		
		$slug = $this->slug;
		add_action('admin_menu', function() use($title, $callback, $slug){
			add_options_page(
				$title,
				$title,
				'manage_options',
				'settings-'.$slug,
				$callback
			);
		});
	}
	
	
	
	/**
	 * add page to a CPT menu.
	 * 
	 * @access protected
	 * @param mixed $cpt_slug
	 * @param bool $title (default: false)
	 * @param bool $callback (default: false)
	 * @return void
	 */
	protected function add_cpt_page($cpt_slug, $title=false, $callback=false) {
		if (!$title) {
			$title = $cpt_slug . " " . __("Settings");
		}
		
		if(!$callback) {
			$callback = array($this, "load_cpt_page");
		}
		$slug = $this->slug;
		add_action('admin_menu', function() use($cpt_slug, $title, $callback, $slug){
			add_submenu_page(
				'edit.php?post_type='.$cpt_slug,
				$title,
				$title,
				'manage_options',
				'cpt-page-'.$slug,
				$callback 
			);
		});
	}



	/**
	 * load default settings view.
	 * 
	 * @access public
	 * @return void
	 */
	public function load_settings_page() {
		$this->render("views/admin/default.php", array('title'=>get_called_class()));
	}


	/**
	 * load default menu view.
	 * 
	 * @access public
	 * @return void
	 */
	public function load_menu_page() {
		$this->render("views/admin/default.php", array('title'=>get_called_class()));
	}
	
	/**
	 * load default submenu view.
	 * 
	 * @access public
	 * @return void
	 */
	public function load_submenu_page() {
		$this->render("views/admin/default.php", array('title'=>get_called_class()));
	}

	/**
	 * load default CPT submenu view.
	 * 
	 * @access public
	 * @return void
	 */
	public function load_cpt_page() {
		$this->render("views/admin/cpt.php", array('title'=>get_called_class()));
	}

	
	
	/**
	 * register preferenced custom post types.
	 * 
	 * @access private
	 * @param mixed $cpts
	 * @return void
	 */
	private function custom_post_types($cpts) {
		foreach ($cpts as $slug=>$prefs) {
			PBUtils::registerCPT($slug, $prefs);

			$cpt_class = $this->root_dir . "/classes/cpt-" . $slug . ".php";
			if (file_exists($cpt_class)) {
				include_once("plugin-base-cpt.php");
				require_once($cpt_class);
			}	
		}
	}
	
	
	/**
	 * enqueue assets for admin and page.
	 * 
	 * @access private
	 * @param mixed $files
	 * @return void
	 */
	private function admin_css($files) {
		$slug = $this->slug;
		$version = $this->version;
		foreach($files as $file) {
			add_action( 'admin_enqueue_scripts', function() use ($file, $slug, $version){
				wp_enqueue_style($slug .'-admin-styles', plugins_url($file, dirname(__FILE__)), array(), $version);
			});
		}
	}
	
	
	private function admin_js($files) {
		$slug = $this->slug;
		$version = $this->version;

		if ($this->is_plugin_screen()) {
			foreach($files as $file) {
				add_action( 'admin_enqueue_scripts', function() use ($file, $slug, $version){
					wp_enqueue_script($slug .'-admin-script', plugins_url($file, dirname(__FILE__)), array(), $version);
				});
			}
		}
	}
	
	
	
	private function public_css($files) {
		$slug = $this->slug;
		$version = $this->version;
		foreach($files as $file) {
			add_action( 'wp_enqueue_scripts', function() use ($file, $slug, $version){
				wp_enqueue_style($slug .'-admin-styles', plugins_url($file, dirname(__FILE__)), array(), $version);
			});
		}
	}
	
	
	
	private function public_js($files) {
		$slug = $this->slug;
		$version = $this->version;
		foreach($files as $file) {
			add_action( 'wp_enqueue_scripts', function() use ($file, $slug, $version){
				wp_enqueue_script($slug .'-admin-script', plugins_url($file, dirname(__FILE__)), array(), $version);
			});
		}
	}
	
	
}
