<?php

class Metaboxes
{
    public $options;

    public function __construct()
    {
        $this->options = get_option('options_'.PLUGINS_QUERY);
        add_action('add_meta_boxes', array($this,'set_meta'));
        $this->save_meta();       
    }

     public function set_meta()
     {
         global $custom_post;
         $post = $custom_post;
         // add_meta_box(id, title, callback, page or post type, priority*, arguments*)
         add_meta_box('kt_website_title', 'Website title', array($this, 'website_title'), $post['post_id']);
         add_meta_box('kt_website_url', 'Website url', array($this, 'website_url'), $post['post_id']);
         add_meta_box('kt_website_designers_name', 'Designers name', array($this, 'designers_name'), $post['post_id']);
         add_meta_box('kt_website_designers_website', 'Designers Website', array($this, 'designers_website'), $post['post_id']);
         add_meta_box('kt_website_designers_email', 'Designers Email address', array($this, 'designers_email'), $post['post_id']);
         add_meta_box('kt_website_thumbnail', 'Website Thumbnail', array($this, 'website_thumbnail'), $post['post_id']);
         add_meta_box('kt_website_category', 'Website Category', array($this, 'website_category'), $post['post_id']);
         
         return;
     }
     
     public function save_meta()
     {
         add_action('save_post', array($this,'save_website_title'));
         add_action('save_post', array($this,'save_website_url'));
         add_action('save_post', array($this,'save_designers_name'));
         add_action('save_post', array($this,'save_designers_website'));
         add_action('save_post', array($this,'save_designers_email'));
         add_action('save_post', array($this,'save_website_thumbnail'));
         add_action('save_post', array($this,'save_website_category'));
         
         return;
     }

    //Metaboxes
    public function website_title($post)
    {
        $title = get_post_meta($post->ID, 'kt_website_title', true);
        ?>
        <p>
            <input type="text" class="widefat" id="kt_website_title" name="kt_website_title" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

     public function website_url($post)
     {
        $url = get_post_meta($post->ID, 'kt_website_url', true);
        ?>
         <p>
             <input type="text" class="widefat" id="kt_website_url" name="kt_website_url" value="<?php echo esc_attr($url); ?>">
         </p>
         <?php
     }

     public function designers_name($post)
     {
        $name = get_post_meta($post->ID, 'kt_designers_name', true);
        ?>
        <p>
            <input type="text" class="widefat" id="kt_designers_name" name="kt_designers_name" value="<?php echo esc_attr($name); ?>" />
         </p>
         <?php
     }
     public function designers_website($post)
     {
         $url = get_post_meta($post->ID, 'kt_designers_website', true);
        ?>
         <p>
             <input type="text" class="widefat" id="kt_designers_website" name="kt_designers_website" value="<?php echo esc_attr($url); ?>" />
        </p>
     <?php
     }
     public function designers_email($post)
     {
        $email = get_post_meta($post->ID, 'kt_designers_email', true);
        ?>
         <p>
             <input type="text" class="widefat" id="kt_designers_email" name="kt_designers_email" value="<?php echo esc_attr($email); ?>" />
         </p>
         <?php
     }
    public function website_thumbnail($post)
    {
        $thumbnail = get_post_meta($post->ID, 'kt_website_thumbnail', true);
        ?>
        <p>
            <?php if(check_values($thumbnail, 'url')!=false):?>
            <img src="<?php echo $thumbnail; ?>" width="100%" style="display: block; margin: 0 auto;" />

            <?php endif; ?>
            <input type="text" class="widefat" id="kt_website_thumbnail" name="kt_website_thumbnail" value="<?php echo esc_attr($thumbnail); ?>" />
            <label for="adm_thumb" class="button-primary">Upload Thumbnail</label>
            <input style="display: none" type='file' id='adm_thumb' name='thumbnail_upload' accept='image/*' />
        </p>
        <?php
    }
    public function website_category($post)
    {
        $website_category = get_post_meta($post->ID, 'kt_website_category', true);

        if(!!$this->options && $this->options['website_category'] && is_string($this->options['website_category'])):
            $categories = explode(',',$this->options['website_category']);
            echo "<label for='website_category'>Category</label>";
            echo "<select name='kt_website_category' id='kt_website_category' class='widefat' >";
            echo "<option value='no_category' >No Category</option>";
            foreach($categories as $category):
                $selected = ($category==$website_category)? 'selected="selected"': '';
                echo "<option value='$category' $selected >".ucfirst($category)."</option>";
            endforeach;
            echo "</select>";
        elseif(!!$this->options && isset($website_category)):
            echo "<label>The category field is not set in options page, this is a default value</label><input class='widefat' type='text' name='kt_website_category' value='$website_category'/>";

        endif;

    }

    //Save metaboxes functions
    public function save_website_title($id)
    {
    	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    		return;
    	
        $title= "title doesn't exists";
        if(isset($_POST['kt_website_title']))
        {
            if(mb_strlen($_POST['kt_website_title']) <= 25){ $title =  strip_tags($_POST['kt_website_title']); }
        }
        update_post_meta($id, 'kt_website_title', $title);
    }

     public function save_website_url($id)
     {
     	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
     		return;
     	
         $url= "url doesn't exists";
         if(isset($_POST['kt_website_url']))
         {
             preg_match("@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@",$_POST['kt_website_url'],$test );
             if(isset($test[0])){ $url =  strip_tags($_POST['kt_website_url']); }
         }
         update_post_meta($id, 'kt_website_url', $url);
     }
     public function save_designers_name($id)
     {
     	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
     		return;
     	
          $name = 'name doesn\'t exists';
          if(isset($_POST['kt_designers_name'])) $name = strip_tags($_POST['kt_designers_name']);
          update_post_meta($id, 'kt_designers_name', $name);
     }
     public function save_designers_website($id)
     {
     	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
     		return;
     	
        $url= "url doesn't exists";
        if(isset($_POST['kt_designers_website']))
        {
            preg_match("@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@",$_POST['kt_designers_website'],$test );
            if(isset($test[0])){ $url =  strip_tags($_POST['kt_designers_website']); }
        }
        update_post_meta($id, 'kt_designers_website', $url);
     }
     public function save_designers_email($id)
     {
     	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
     		return;
     	
         $email= "email doesn't exists";
         if(isset($_POST['kt_designers_email']))
         {
             preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/",$_POST['kt_designers_email'],$test );
             if(isset($test[0])){ $email =  strip_tags($_POST['kt_designers_email']); }
         }
         update_post_meta($id, 'kt_designers_email', $email);
     }

     public function save_website_thumbnail($id)
     {
     	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
     		return;
     	
         $thumbnail = 'thumbnail not exists';
         if(isset($_POST['kt_website_thumbnail']))
         {
             $thumbnail = $_POST['kt_website_thumbnail'];
         }

         if(isset($_FILES['thumbnail_upload']) && !empty($_FILES['thumbnail_upload']['tmp_name']))
         {
             $file = wp_handle_upload($_FILES['thumbnail_upload'],array('test_form' => FALSE));
             $thumbnail = $file['url'];
         }
         update_post_meta($id, 'kt_website_thumbnail', $thumbnail);
     }

    public function save_website_category($id)
    {
    	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    		return;
    	
        $website_category = 'No Category';
        if(isset($_POST['kt_website_category']))
        {
            $website_category = $_POST['kt_website_category'];
        }
        update_post_meta($id, 'kt_website_category', $website_category);
    }

}
