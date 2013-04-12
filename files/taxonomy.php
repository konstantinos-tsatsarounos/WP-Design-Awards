<?php

class Participants
{
    public function __construct()
    {
        $this->set_taxonomy();
    }

    public function set_taxonomy()
    {       
        $taxonomy = array(
            'query_var' => 'participants',
            'hierarchical' => true,
            'rewrite' => array(
                'slug' => 'design_awards/Participants'
            ),
            'labels' => array(
                'name' => __('Participants'),
                'singular_name' => __('Participants'),
                'add_new' => __('Add New Participant Category'),
                'add_new_item' => __('Add New Category'),
                'edit_item' => __('Edit Category'),
                'update_item' => __('Update Category'),
                'new_item' => __('New Category'),
                'all_items' => __('All Category'),
                'view_item' => __('View Category'),
                'search_items' => __('Search Category'),
                'not_found' =>  __('No category found'),
                'not_found_in_trash' => __('No category found in Trash'),
                'parent_item_colon' => '',
                'choose_from_most_used' => __('Choose from the most used'),
                'menu_name' => __('Participants')
            )
        );

        register_taxonomy('Participants', 'design_awards', $taxonomy);

    }

}
?>