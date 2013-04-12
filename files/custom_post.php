<?php
class design_awards_post_type
{
    public function __construct()
    {
        $this->set_post();
    }

    public function set_post()
    {        
        $labels = array(
            'name' => __('Design Awards'),
            'singular_name' => __('Design Awards'),
            'add_new' => __('Add new nomination'),
            'add_new_item' => __('Add new Nomination'),
            'update_item' => __('Update Nomination'),
            'edit_item' => __('Edit Nomination'),
            'new_item' => __('New Nomination'),
            'all_items' => __('All Nominations'),
            'view_item' => __('View Nomination'),
            'search_items' => __('Search Nominations'),
            'not_found' =>  __('No nominations found'),
            'not_found_in_trash' => __('No nomination found in Trash'),
            'menu_name' => __('Design Awards')
        );

        /*if permalink doesn't work
         * try
         * 'rewrite' => array( 'slug' => $post['url_query'].'/' ),
         * or
         * 'rewrite' => array( 'slug' => $post['url_query'],'with_front' => FALSE),
         */
        $args = array(
            'labels' => $labels,
            'show_ui' => true,
            'public' => true,
            'show_in_menu' => true,
            'show_in_admin_bar'=> false,
            'query_var' => 'designawards',            
            'rewrite' => false,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 25,
            'menu_icon' => PLUGIN_PATH."images/desaw_menu_icon.png",
            'supports' => array( 'title', 'author','editor', 'comments' )
        );

        register_post_type('design_awards', $args);
    }
}


?>