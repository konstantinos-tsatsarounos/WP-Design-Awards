<?php
class Settings_page
{
    public  $options;

    public function __construct()
    {
        $this->options = get_option( 'options_design_awards' );
        $this->add_options();
        $this->register_fields();
    }

    public function add_options()
    {
        add_options_page('options_design_awards', PLUGINS_NAME, 'administrator', __FILE__, array($this, 'display_settings_page'));
    }

    public function display_settings_page()
    {
       echo "<div class='icon'><br/></div>";
     
       wp_enqueue_style( 'aw_options_page_style');

       echo "<h1>".PLUGINS_NAME." Settings</h1>";
       echo "<h4 style='display: inline-block; position: relative;'><a href='http://www.e-xtnd.it/wp-design-awards/'>Do you want more options? Customizable rating with few clicks?  Widgets? Support for your website? Buy the premium!</a></h4>";
      ?>
      <p class="clear"></p>
      <form method="post" action="options.php" enctype="multipart/form-data" >
      <?php settings_fields( 'options_design_awards' ); ?>
      <?php do_settings_sections(__FILE__); ?>
      <p>
          <input type="submit" name="submit" value="Save Changes" class="button-primary">          
      </p>
      </form>        
        <script type="text/javascript" >
            jQuery(document).ready(function() {               

                jQuery('.form-table').css({
                    position : 'relative'
                });
               jQuery('.form-table')
                       .eq(2).children('tbody')
                       .children('tr')
                       .eq(2)
                       .css({ height : 60})
                       .children('td')
                       .children('div')
                       .css({ position: 'absolute', bottom : 10, left: 10});

                jQuery('.form-table')
                        .eq(2)
                        .children('tbody')
                        .children('tr')
                        .children('th')
                        .eq(2)
                        .css({ opacity: 0 });
                jQuery('.form-table')
                        .eq(2)
                        .children('tbody')
                        .children('tr')
                        .children('th')
                        .eq(1)
                        .css({ fontSize: 15, fontWeight: 'bold' });

                jQuery('h3').eq(3).css({marginBottom : 50});

            });

        </script>
      <?php
    }

    public function register_fields()
    {
        register_setting( 'options_design_awards',  'options_design_awards', array( $this, 'validate_options'));

        add_settings_section('basic_settings', 'Basic settings', array($this, 'basic_section_cb'), __FILE__);        
        

        //Basic Settings
        add_settings_field('image_width', 'Image width', array($this,'image_width'), __FILE__, 'basic_settings');
        add_settings_field('image_height', 'Image height', array($this,'image_height'), __FILE__, 'basic_settings');
        add_settings_field('thumbnail_factor', 'Thumbnail Shrink Factor', array($this,'thumbnail_factor'), __FILE__, 'basic_settings');
        add_settings_field('image_type', 'Thumbnail image type', array($this,'image_type'), __FILE__, 'basic_settings');        
        add_settings_field('post_default_taxonomy_term', 'Default Category', array($this,'post_default_taxonomy_term'), __FILE__, 'basic_settings');
        add_settings_field('plugin_lang', 'Language', array($this,'plugin_lang'), __FILE__, 'basic_settings');
        add_settings_field('website_category', 'Website category', array($this,'website_category'), __FILE__, 'basic_settings');

        
    }

    public function validate_options($options)
    {        
        
        $thumbnail_factor = 100;
        if(!is_numeric($options['thumbnail_factor']) || $options['thumbnail_factor']==0) $options['thumbnail_factor'] = 100;
        if(!is_numeric($options['image_width']) || $options['image_width']<50) $options['image_width'] = 300;
        if(!is_numeric($options['image_height']) || $options['image_height']<50) $options['image_height'] = 200;
        
        $thumbnail_factor = round((100/$options['thumbnail_factor']),2);
        $options['thumbnail_factor'] = $thumbnail_factor;
        
        $options['image_width'] = round($options['image_width']);
        $options['image_height'] = round($options['image_height']);

       

        return $options;
    }

    public function basic_section_cb()
    {

    }

    public function rating_section_cb()
    {

    }

    public function icon_section_cb()
    {

    }

    public function color_section_cb()
    {

    }

    //Basic Section Setting functions
    public function image_width()
    {
        $image_width = 0;
        if(!empty($this->options) && isset($this->options['image_width']))  $image_width  = $this->options['image_width'];
        echo "<input name='options_design_awards[image_width]' type='number' value='$image_width' />";
    }
    public function image_height()
    {
        $image_height = 0;
        if(!empty($this->options) && isset($this->options['image_height']))  $image_height  = $this->options['image_height'];
        echo "<input name='options_design_awards[image_height]' type='number' value='$image_height' />";
    }
    public function thumbnail_factor()
    {
        $thumbnail_factor = 100;
        if(!empty($this->options) && isset($this->options['thumbnail_factor']))  $thumbnail_factor  = $this->options['thumbnail_factor'];
        $thumbnail_factor = round(100/$thumbnail_factor);
        echo "<input name='options_design_awards[thumbnail_factor]' type='number' max='300' value='$thumbnail_factor' />%";
    }
    public function image_type()
    {
        $image_type = 'png';
        $types = array('png', 'jpeg', 'gif', 'all types');
        if(!empty($this->options) && isset($this->options['image_type']))  $image_type  = $this->options['image_type'];

        echo "<select name='options_design_awards[image_type]'>";
        foreach ($types as $type):
            $selected = ($image_type == $type ) ? " selected='selected' " : "";
            if($type == 'all types'):
                $selected =($image_type == '*')? " selected='selected' " : "";
                echo "<option value='*' $selected >$type</option>";
            else:
                echo "<option value='$type' $selected >$type</option>";
            endif;
        endforeach;
        echo "</select>";

    }   

    public function  post_default_taxonomy_term()
    {
        global $wpdb;

        $default_category = false;
        if(!empty($this->options) && isset($this->options['default_category']))  $default_category  = $this->options['default_category'];

        $table_taxonomy = $wpdb->prefix . 'term_taxonomy';
        $table_terms = $wpdb->prefix . 'terms';

        $term_id_array = $wpdb->get_results("select term_id from $table_taxonomy where taxonomy = 'Participants'");
        $terms_array = $wpdb->get_results("select * from $table_terms ");
        $selected = array();
        $count =0;
        foreach($term_id_array as $key => $id)
        {
            $term_id_array[$key] = $id->term_id;
        }
        foreach($terms_array as $key => $value)
        {
           if(in_array($value->term_id,$term_id_array )):
               $selected[$count++] = $value;
           endif;
        }

        ?>
        <?php
        echo "<select name='options_design_awards[default_category]'>";
        foreach($selected as $term):
            $selected = '';
            if($default_category!=false && $term->term_id == $default_category) $selected = 'selected="selected"';
            echo "<option value='$term->term_id' $selected>".ucwords($term->name)."</option>";
        endforeach;
        echo "</select>";
    }

    public function plugin_lang()
    {
        $selected_lang = 'english';
        if(!empty($this->options) && isset($this->options['plugin_lang']))  $selected_lang  = $this->options['plugin_lang'];

        $langs = scandir(PLUGIN_ROOT_FOLDER.'/language');
        $forbitten = array('.','..','index.html');
        echo "<select name='options_design_awards[plugin_lang]'>";
        foreach ($langs as $lang):
            if(in_array($lang,$forbitten)) continue;
            $lang = str_replace('.php','',$lang);
            $selected = ($selected_lang == $lang)? 'selected="selected"' : '';
            echo "<option value='$lang' $selected >$lang</option>";
        endforeach;
        echo "</select>";
    }

    public function  website_category()
    {
        $website_category = '';
        if(!empty($this->options) && isset($this->options['website_category']))  $website_category = $this->options['website_category'];
        echo "<textarea name='options_design_awards[website_category]' cols='40' rows='3' type='text' >$website_category</textarea>";
    }



}

?>