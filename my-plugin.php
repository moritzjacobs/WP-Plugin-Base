<?php
/*
Plugin Name: MyPlugin
Plugin URI: http://myplugin.example
Description: …
Version: 1.0
Author: Your name
Author URI: http://mywebsite.example
*/


require_once('framework/plugin-base.php');
require_once('my-plugin-class.php');

// Initalize
$MyPlugin = new MyPlugin();

// Add an activation hook
register_activation_hook(__FILE__, array(&$MyPlugin, 'activate'));

// Run the plugins initialization method
add_action('init', array(&$MyPlugin, 'initialize'));

