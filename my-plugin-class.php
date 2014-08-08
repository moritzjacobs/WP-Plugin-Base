<?php

class MyPlugin extends PluginBase {

	function __construct() {
		parent::__construct($this->preferences, __DIR__);
	}
	
	
	/***************************************************
	*
	*  Boilerplate starts here:
	*
	****************************************************
	
	Default settings API overview:
	
	- admin_css: Array of paths to CSS files for the admin
		interface
	
	- public_css: Array of paths to CSS files for the site
	
	- admin_js: Array of paths to JS files for the admin
		interface
	
	- public_js: Array of paths to JS files for the site 
	
	- custom_post_types: associative array of post types
		to register. Value equals options parameter for
		register_post_type(); see:
		http://codex.wordpress.org/Function_Reference/register_post_type
		
		array("my-post-type-slug" => $options)
	
	****************************************************/
	

	private $preferences = array(
		'admin_css' => array("css/admin.css"),
		'public_css' => array("css/public.css"),
		'admin_js' => array("js/admin.js"),
		'public_js' => array("js/public.js"),
		
		'custom_post_types' => array(
			'foobar' => array(
				'labels' => array(
					'name' 				=> 'Foobars',
					'singular_name'		=> 'Foobar',
					'add_new' 			=> 'Add',
					'menu_name'			=> 'My Plugin',
				),
			),
		),
	);



	public $version = '1.0';


	/***************************************************
	*
	*  This function is called right after a plugin is
	*  activated, but right before a page redirect. It
	*  must not create output!
	*
	****************************************************/
	
	public function activate() {
	

		
	}

	/***************************************************
	*
	*  Init function, add hooks, actions etc. here
	*
	****************************************************/

	public function initialize() {
		$this->add_settings_page("MyPlugin Settings", array($this, "load_settings_page"));
		$this->add_cpt_page("foobar", "Foobar Settings", array($this, "load_cpt_page"));
	}





	/***************************************************
	*
	* Examples for action callbacks for PluginBase
	* helper functions 
	*
	****************************************************/

	public function load_settings_page() {
	
		// stupid dog example
		$dogs = array(
		    array("name"=>"Fido", "age"=>4, "color"=>"brown"),
		    array("name"=>"Rex", "age"=>6, "color"=>"black"),
		    array("name"=>"Snoopy", "age"=>2, "color"=>"white"),
		    array("name"=>"Lassie", "age"=>5, "color"=>"golden"),
		);
		
		$this->render("views/admin/dogs.php", array('title'=>"Welcome!", 'dogs'=>$dogs));
	}
	
	public function load_cpt_page() {
		// render view template and extract second parameter array
		$this->render("views/admin/cpt.php", array('title'=>"Foobar Settings"));
	}


}
