<?php

/*
Plugin Name: WP Design Awards
Plugin URI: http://www.e-xtnd.it/wp-design-awards/
Description: This plugin creates the functionality of a website design contest!
Version: 1
Author: Konstantinos Tsatsarounos
Author URI: http://www.infogeek.gr
License: GPL2
*/

/*  Copyright 2013  Konstantinos Tsatsarounos  (email : konstantinos.tsatsarounos@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//General Plugin Constants
define('PLUGIN_PATH', plugin_dir_url( __FILE__ ));
define('PLUGIN_ROOT_FOLDER' , dirname(__FILE__) );
define('TEMPLATES_FOLDER', PLUGIN_PATH.'templates');
define('PLUGINS_NAME', 'Design Awards');
define('PLUGINS_QUERY', 'design_awards' );


$options = get_option('options_'.PLUGINS_QUERY);
$lang = 'english';

if(isset($options['plugin_lang'])):
    $lang= $options['plugin_lang'];
endif;

$custom_post = array(
    'post_id' => 'design_awards',
    'name' => 'Design Awards',
    'post_name' => 'Nomination',
    'post_name_plural' => 'Nominations',
    'url_query' => 'designawards',
);


//Image Constants
define('THUBNAIL_WIDTH', isset($options['image_width'])? $options['image_width']: 300 );
define('THUBNAIL_HEIGHT', isset($options['image_height'])? $options['image_height']: 200);
define('THUBNAIL_FACTOR', isset($options['thumbnail_factor'])? $options['thumbnail_factor']: 1);

//Database Constants
define('DATABASE_TABLE_NAME', 'design_awards' );
define('DATABASE_TABLE_VERSION', '1.0' );

//Include the necessary php files
require_once(PLUGIN_ROOT_FOLDER."/language/{$lang}.php"); //file for common functions
require_once(PLUGIN_ROOT_FOLDER.'/files/functions.php'); //file for common functions
require_once(PLUGIN_ROOT_FOLDER.'/files/settings_page.php'); //file for settings
require_once(PLUGIN_ROOT_FOLDER.'/files/custom_post.php'); //file for the post type
require_once(PLUGIN_ROOT_FOLDER.'/files/taxonomy.php'); //file for the taxonomy
require_once(PLUGIN_ROOT_FOLDER.'/files/metaboxes.php'); //file for metaboxes
require_once(PLUGIN_ROOT_FOLDER.'/files/form.php'); //form's shortcode for custom post
require_once(PLUGIN_ROOT_FOLDER.'/files/display_gallery.php'); //shortcode for gallery!

 

//register css styles
register_styles();

//register javascript
add_action('init', 'register_scripts',4);


//form shortcode
add_shortcode('nominee_form', 'nominee_form' ); //defined in form.php
add_shortcode('css_gallery', 'display_gallery' ); //defined in form.php

//Actions
add_action('init', 'register_custom_post',1);
add_action('init', 'register_custom_metaboxes',3);
add_action('init', 'register_taxonomies',2);

add_action('wp_footer', 'enqueue_scripts',2);

add_action('admin_menu', 'register_settings_page',0);

add_action('admin_menu', 'register_upload_thumbnail',3);
add_action('wp_loaded','form_validation',4);
add_action('save_post', 'kt_on_save_post',3);


add_filter('manage_posts_columns', 'column_head');
add_action('manage_posts_custom_column', 'columns_content', 10, 2);


add_action('wp_ajax_nopriv_site_rating', 'site_rating');
add_action('wp_ajax_site_rating', 'site_rating');



//filters
add_filter('the_content', 'post_display');

//Activation HOOKS
register_activation_hook(__FILE__,'install_database');

