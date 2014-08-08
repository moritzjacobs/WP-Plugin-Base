<?php

/***************************************************
*
*  Utility helper functions
*
****************************************************/

class PBUtils {
	
		
	/***************************************************
	*
	*  Make a clean URL slug from a string
	*
	****************************************************/

	public static function sluggify($text) {
		$text = trim($text, '- ');
		$text = str_replace(" ", "-", $text);
		$text = preg_replace('~[^\\pL\d_]+~u', '-', $text);
		
		$text = strtolower($text);
		$text = str_replace("ä", "ae", $text);
		$text = str_replace("ö", "oe", $text);
		$text = str_replace("ü", "ue", $text);
		$text = str_replace("ß", "ss", $text);
		
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		
		if (empty($text)) {
			return 'n-a';
		}
		
		return $text;
	}
	
	
	public static function registerCPT($slug, $prefs) {

		$post_type_args = array(
			'labels' 			=> array(
				'name' 				=> ucfirst($slug),
				'singular_name'		=> substr(ucfirst($slug), 0, -1),
				'add_new' 			=> __('Add'),
				'menu_name'			=> ucfirst($slug)
			),
			'singular_label' 	=> substr(ucfirst($slug), 0, -1),
			'public' 			=> true,
			'show_ui' 			=> true,
			'publicly_queryable'=> true,
			'query_var'			=> true,
			'exclude_from_search'=> false,
			'show_in_nav_menus'	=> true,
			'capability_type' 	=> 'post',
			'has_archive' 		=> true,
			'hierarchical' 		=> false,
			'rewrite' 			=> array('slug' => strtolower($slug), 'with_front' => false ),
			'supports' 			=> array('title', 'author', 'revisions', 'page-attributes', 'comments'),
			'menu_position' 	=> 5,
			'menu_icon' 		=> false,
			'taxonomies'		=> array()
		 );
		
		 $options = array_merge($post_type_args, $prefs);
		
		
		add_action('init', function() use ($slug, $options) {
			register_post_type($slug, $options);
		});

	}
	
}
