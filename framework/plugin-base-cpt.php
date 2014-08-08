<?php

/***************************************************
*
*  Base Class for plugin CPTs
*
****************************************************

Provides template cascading for themes and ACF
integration (export voa ACF frontend and use
add_acf().

****************************************************/


class PluginBaseCPT {

	public $slug = '';
	public $base_dir = '';

	public function __construct($slug, $base_dir, $prefs=array()) {
		$this->slug = $slug;
		$this->base_dir = $base_dir;
		
		$defaults = array(
			'templatable' => false,
		);
		
		$options = array_merge($defaults, $prefs);
		
		foreach($options as $func=>$param) {
			if (method_exists($this, $func) && $param) {
				call_user_func_array(array($this, $func), array($param));
			}
		}
	}
	
	
	/***************************************************
	*
	*  cascading templates
	*
	****************************************************
	
	Hook into template locating.
	Hierarchy is
		my_child_theme/templates/my_post_type/single.php
		> my_theme/templates/my_post_type/single.php
		> plugin/views/my_post_type/single.php
		> my_child_theme/single.php
		> my_theme/single.php
		
		… and accordingly for archive
	
	****************************************************/
		
	private function templatable() {
		add_filter( 'template_include', function($default){
			if(get_post_type() === $this->slug) {

				$tpl_type = is_archive() ? "archive" : "single";
				$tpl = locate_template("templates/".$this->slug."/".$tpl_type.".php", false, false);

				if (!empty($tpl)) {
					return $tpl;
				} else {
					$tpl = $this->base_dir."/views/".$this->slug."/".$tpl_type.".php";
					if (file_exists($tpl)) { return $tpl; }
				}

			}
			
			return $default;
		}, 99 );		
	}
	

	/***************************************************
	*
	*  Register ACF fields and ensure ACF as a
	*  dependency.
	*
	****************************************************/	
	
	public function add_acf($code) {
		if(function_exists("register_field_group")){
			register_field_group($code);
		} else {
			include_once 'vendor/elliotcondon/acf/acf.php';
			register_field_group($code);
		}

	}

}