<?php
$foobar = new Foobar("foobar", $this->root_dir);
class Foobar extends PluginBaseCPT {


	public function __construct($cpt_slug, $base_dir) {
		parent::__construct($cpt_slug, $base_dir, $this->preferences);
		$this->init();
	}
	

	/***************************************************
	*
	*  Boilerplate starts here:
	*
	****************************************************
	
	...
		
	****************************************************/	
	
	
	private $preferences = array(
		'templatable' => true
	);
	
	
	
	
	
	
	
	public function init() {
			
	
		$this->add_acf(array(
			'id' => 'acf_foobar',
			'title' => 'Foobar',
			'fields' => array (
				array (
					'key' => 'field_53e396324e719',
					'label' => 'Barfoo',
					'name' => 'barfoo',
					'type' => 'text',
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'formatting' => 'html',
					'maxlength' => '',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'foobar',
						'order_no' => 0,
						'group_no' => 0,
					),
				),
			),
			'options' => array (
				'position' => 'normal',
				'layout' => 'no_box',
				'hide_on_screen' => array (
				),
			),
			'menu_order' => 0,
		)
		);
	}
	
	
	
	
	
	
	
}

