<?php

function register_custom_post()

{

    new design_awards_post_type();

}



function register_custom_metaboxes()

{

    new Metaboxes();

}



function register_taxonomies()

{

    new Participants();

}



function register_styles()

{

    wp_register_style( 'kt_main_plugin_style', PLUGIN_PATH.'css/style.css', false, false, 'all' );

    wp_register_style( 'kt_rating_css', PLUGIN_PATH.'css/rating.css', false, false, 'all' );

    wp_register_style( 'colorpicker_css', PLUGIN_PATH.'css/farbtastic.css', false, false, 'all' );

    wp_register_style( 'aw_options_page_style', PLUGIN_PATH.'css/options_page.css', false, false, 'all' );    



    return false;

}



function register_scripts()
{           

    wp_register_script( 'upload_thumb', PLUGIN_PATH.'js/upload_thumbnail.js', false, false, false);
    
    wp_register_script( 'store', PLUGIN_PATH.'js/store.min.js',false, false, false);

    wp_register_script( 'rating', PLUGIN_PATH.'js/rating.js', array('store','jquery'), false, false);       
    
    wp_register_script( 'kt_exec', PLUGIN_PATH.'js/main.js', array('rating','store'), false, false);
}


function enqueue_scripts()

{

    

    $image_rating = get_bloginfo('url').'/wp-content/plugins/design_awards/js/stars/stars_1.png';
         

    $inactive_star_color = '#000';

    $average_rating_color = '#ff9900';

    $active_rating_color = '#dd4918';



    $options = get_option('options_'.PLUGINS_QUERY);



    if(!empty($options) && isset($options['custom_rating_image'])):

        $image_rating = $options['custom_rating_image'];

    endif;   



    if(!empty($options) && isset($options['stars_background_color'])):

        $inactive_star_color = $options['stars_background_color'];

    endif;



    if(!empty($options) && isset($options['average_rating_color'])):

        $average_rating_color = $options['average_rating_color'];

    endif;



    if(!empty($options) && isset($options['active_rating_color'])):

        $active_rating_color = $options['active_rating_color'];

    endif;
    

    

    wp_enqueue_script("jquery");

    wp_enqueue_script("jquery-ui-core");

    

    wp_enqueue_script("rating");      

    $rating_vars = array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => "MMH$6<qjvQq5WeR",
        'image_rating' => $image_rating,      
    );
    
     wp_localize_script('rating', 'ajax_var', $rating_vars); 

  

    

    wp_enqueue_script("upload_thumb");
    wp_enqueue_script("store");
    

    wp_enqueue_script("kt_exec");
    
    $main_vars = array(    
    		'url' => admin_url('admin-ajax.php'),    
    		'nonce' => "&4iFJK+c)+^DD5wf",    
    		'inactive_star_color' => $inactive_star_color,    
    		'average_rating_color' => $average_rating_color,    
    		'active_rating_color' => $active_rating_color    
    );

    wp_localize_script('kt_exec', 'main_var', $main_vars);    

   

}





function register_settings_page()

{

   new Settings_page;



}



function register_awards_widget()

{

    register_widget("Awards_widget");

}



function get_url_fraction($url, $key)

{

    $url = parse_url($url);

    if(isset($url[$key])):

        return $url[$key];

    else: return false;

    endif;

}

function post_add_meta($id, array $meta)

{

    foreach($meta as $meta_key => $meta_value):

        update_post_meta($id, $meta_key, $meta_value);

    endforeach;

}



function check_values($value, $type)

{

    $url_reg = "@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@";

    $email_reg = "/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/";

    if(is_string($value) && is_string($type)):

        switch($type)

        {

            case 'email': preg_match($email_reg,$value,$value); return isset($value[0])?$value[0]:false;

            case 'url': preg_match($url_reg,$value,$value); return isset($value[0])?$value[0]:false;

        }

    endif;

    return false;

}



function form_validation(){

    global $custom_post,$lang;

    $options =get_option('options_'.PLUGINS_QUERY);

    if(!empty($_POST['nominee_form']) && wp_verify_nonce($_POST['nominee_form'],'nominee')):

        //includes necessary functions

        if(!function_exists('wp_get_current_user')) include ABSPATH.'wp-includes/pluggable.php';

        if(!function_exists('wp_handle_upload')) include ABSPATH.'wp-admin/includes/file.php';

        if(!function_exists('dbDelta')) require_once ABSPATH."wp-admin/includes/upgrade.php";





        //necessary variables for the process

        $fields = array(

            'website title' => 'website_title',

            'website url' => 'website_url',

            'designers name' => 'designers_name',

            'designers email' => 'designers_email',

            'designers url' => 'designers_url',

            'website description' => 'website_desc',

            'is redesign' => 'is_redesign',

            'website category' => 'website_category'

        );

        $wrong_input = false;

        $checked_fields = array();

        $error = array();

        $image = array();

        $double_post = false;



        //Image upload section

        if(isset($_FILES['website_screenshot']) && !empty($_FILES['website_screenshot']['tmp_name'])):



            list($width, $height) = getimagesize($_FILES['website_screenshot']['tmp_name']);

            $img = getimagesize($_FILES['website_screenshot']['tmp_name']);

            $image_type = ($options!=false)? 'image/'.$options['image_type'] : $img['mime'];



            if($width == THUBNAIL_WIDTH && $height == THUBNAIL_HEIGHT):

                if($options!=false && ($image_type == $img['mime'] || $image_type == 'image/*')):

                $image = array(

                    'file' => $_FILES['website_screenshot'],

                    'width' => $width,

                    'height' => $height,

                    'type' => $img['mime']

                );

                else: $error[] = $lang['no_image_accepted_type']." $image_type";

                endif;

               else: $error[] = $lang['no_image_accepted_dim'];

            endif;

        else :

            $error[] = $lang['no_image_error'];

        endif;



        //it checks every posted field if it exists or empty

        foreach ($fields as $key => $field)

       {

            if($field == 'is_redesign' ) continue;



            if($field == 'website_category'

                && isset($options['website_category'])

                && empty($options['website_category'])):

                $checked_fields[$key] = 'No category';

                continue;

            endif;



            if(isset ($_POST[$field]) && !empty($_POST[$field]))

            {

                $checked_fields[$key] = esc_attr($_POST[$field]);



                if($key=='website url'):

                    if(check_values($checked_fields[$key],'url')==false):

                        $wrong_input = true;

                        $error[]= $lang['something_mispell']." ".$lang[$key];

                    endif;

                endif;

                if($key=='designers url'):

                    if(check_values($checked_fields[$key],'url')==false):

                        $wrong_input = true;

                        $error[]= $lang['something_mispell']." ".$lang[$key];

                    endif;

                endif;

                if($key=='designers email'):

                    if(check_values($checked_fields[$key],'email')==false):

                        $wrong_input = true;

                        $error[]= $lang['something_mispell']." ".$lang[$key];

                    endif;

                endif;

                if($key=='website title'):

                    if(mb_strlen($checked_fields[$key])>25):

                        $wrong_input = true;

                        $message = ($lang['lang'] == 'greek') ? 'O τίτλος '.$lang['too_long'] :ucfirst($lang[$key]).$lang['too_long'] ;

                        $error[]= $message;

                    endif;

                endif;

            }

            else

            {

                $wrong_input = true;

                $error[] = $lang['forget_to_fill'].$lang[$key].'?';

            }

        }



        $loop = new WP_Query(

            array(

                'post_type' => $custom_post['post_id']

            )

        );



        while($loop->have_posts())

        {

            $loop->the_post();

            $title = get_the_title();



            if(isset($checked_fields['website url']) && $title == get_url_fraction($checked_fields['website url'], 'host') && !isset($_POST['is_redesign'])):

                $double_post = true;

                $error[] = $lang['another_site'];

            else:  break;

            endif;

        }



        //Handles the post and image upload

        if(!$wrong_input && isset($image['file']) && !$double_post)

        {

            $term_id = $options['default_category'];



            global $custom_post;



            $post['post_title'] =  get_url_fraction($checked_fields['website url'], 'host');

            $post['post_content'] = $checked_fields['website description'];

            $post['post_status'] = 'pending' ;

            $post['tax_input'] = array('Participants' => $term_id);

            $post['post_type'] = $custom_post['post_id'];







            $pid = wp_insert_post($post);

            if($pid!=false)

            {



                $file = wp_handle_upload($image['file'],array('test_form' => FALSE));



                insert_awards_reference($pid, $checked_fields);



                post_add_meta($pid, array(

                    'kt_website_title' => $checked_fields['website title'],

                    'kt_website_url' => $checked_fields['website url'],

                    'kt_designers_name' => $checked_fields['designers name'],

                    'kt_designers_website'=> $checked_fields['designers url'],

                    'kt_designers_email' => $checked_fields['designers email'],

                    'kt_website_thumbnail' => $file['url'],

                    'kt_website_category' => $checked_fields['website category']

                ));



            }



        }

        else

        {

            $_POST['error'] =  '<p>'.implode('</p><p>',$error).'</p>';

        }



    endif;

}





function install_database()

{

    if(!function_exists('dbDelta')) require_once ABSPATH."wp-admin/includes/upgrade.php";



    global $wpdb;

    $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;

    $installed_ver = get_option( DATABASE_TABLE_NAME.'_database' );



    $query = "CREATE TABLE if not exists $table_name(

    id int(10) NOT NULL AUTO_INCREMENT,

    website_name VARCHAR(55) NOT NULL,

    post_id int(10) NOT NULL,

    rating int(10) NOT NULL,

    votes_count int(10) NOT NULL,

    UNIQUE KEY id (id)

    );";



    if(!$installed_ver || $installed_ver!= DATABASE_TABLE_VERSION):

        dbDelta($query);

    endif;



    add_option(DATABASE_TABLE_NAME.'_database', DATABASE_TABLE_VERSION);

}



function insert_awards_reference($pid, $data_array)

{

    global $wpdb;

    $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;



    $wpdb->insert( $table_name, array(

        'website_name' => $data_array['website url'],

        'post_id' => $pid,

        'rating' => 0,

        'votes_count' => 0

    ));

}



function kt_on_save_post($pid)

{

    global $custom_post;

    global $post;

    global $wpdb;



    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):

        return;

    endif;    

    

    if(isset($_POST['post_type'])):

        if ( $custom_post['post_id'] == $_POST['post_type'] ):



            if ( !current_user_can( 'edit_post', $post->ID ) ):

                return;

            endif;



                if ( !wp_is_post_revision( $post->ID ) ):

                    $title = get_the_title($post->ID);

                    $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;





                    $q = $wpdb->query(" select * from ".$table_name." where post_id = ".$post->ID."");



                        if(!$q):

                        insert_awards_reference($post->ID, array(

                            'website url' => $title

                        ));

                        endif;

                endif;

        endif;       

    endif;

    

    return $pid;

}



function column_head($defaults) {
	global $post;	
	if($post->post_type == 'design_awards') {
	
    $defaults['kt_designers_name'] = 'Designers name';

    $defaults['votes'] = "Votes";

    $defaults['average_rating'] = "Average Rating";
	}
    return $defaults;

}



function columns_content($column_name, $pid) {

	$type = get_post_type( $pid );	
	if($type != 'design_awards'){ return; }
	
    global $wpdb;

    $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;

    $stats = $wpdb->get_row("SELECT rating,votes_count FROM $table_name where post_id = $pid");
	
	
    if ($column_name == 'kt_designers_name') {

        $website = get_post_meta($pid);

        if ($website && isset($website['kt_designers_name'][0])) {

            echo "<span>".$website['kt_designers_name'][0]."</span>";

        }

    }

    if ($column_name == 'votes' && !empty($stats)) {

        echo "<span>".$stats->votes_count."</span>";

    }

    if ($column_name == 'average_rating' && !empty($stats)) {



        $average = ($stats->votes_count>0)?round($stats->rating/$stats->votes_count,2): "no rating available";

        echo "<span>".$average."</span>";

    }

}







function site_rating()

{

    // Check for nonce security

    $nonce = $_POST['nonce'];

    $post_id = isset($_POST['post_id'])? $_POST['post_id']: false;

    $name= 'aw_awards_cookie';



    if ( $nonce != "MMH$6<qjvQq5WeR" )

        die ( 'Busted!');







    if(isset($_POST['post_rate']))

    {

        global $wpdb;

        $rating = $_POST['post_rate'];

        $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;





        $wpdb->query("UPDATE ".$table_name." set rating = rating+".$rating." , votes_count= votes_count+1 where post_id =".$post_id);

    }

    exit;

}



function post_display($content)

{

    global $custom_post, $lang, $wpdb;   

   



    wp_enqueue_style( 'kt_main_plugin_style');

    wp_enqueue_style( 'kt_rating_css');

    

    //Language vars

    $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;

    $designers_name = $lang['designers_name'];

    $designers_site = $lang['designers_url'];

    $designers_email = $lang['designers_email'];

    $description = $lang['description'];
    $category = $lang['category'];

    $pfx_date = get_the_date( 'd/m/y' ); 

   



    $img_width = THUBNAIL_WIDTH / THUBNAIL_FACTOR;

    $img_height = THUBNAIL_HEIGHT / THUBNAIL_FACTOR;

    $profile_width = 590-$img_width;

    $id = false;

    if($id = get_the_id()):

    $type = get_post_type($id);

    $website_url = get_post_meta($id, 'kt_website_url');

    $website_url = $website_url[0];

    $terms = wp_get_post_terms( $id, 'Participants');

    $t = array();

    $c=0;

    foreach ($terms as $term):

        $t[$c] = $term->slug;

        $c++;

    endforeach;

    $terms = $t;



    if($custom_post['post_id'] == $type){

  

        $meta_values = get_post_meta($id);

        $excerpt = get_the_content($id);



         $stats = $wpdb->get_row("SELECT rating,votes_count FROM $table_name where post_id = $id");

       

         //Calculate average rating

        $average = ($stats->votes_count!=0)? round($stats->rating/$stats->votes_count,2): 0;

        $rating = $stats->rating;

        $votes = $stats->votes_count;

        

        //images

        $designer_thumb = PLUGIN_PATH . 'css/designer.png';

        $site_thumb = PLUGIN_PATH . 'css/site.png';

        $email_thumb = PLUGIN_PATH . 'css/email.png';

        $category_thumb = PLUGIN_PATH . 'css/category.png';
      


        wp_enqueue_style( 'kt_main_plugin_style');

        $content = <<<TEMPLATE

        <div class='site_profile'>

            <div style="position:relative; overflow: hidden;">

                <div class='rate'>$average / $votes</div>             

                <a target='_blank' href="$website_url"><img src="{$meta_values['kt_website_thumbnail'][0]}" width="100%;" /></a>

                <span class='date'>$pfx_date</span>

                <h1 class='title'>{$meta_values['kt_website_title'][0]}

                    <div class='aw-post-rating'>

                        <div class='rating-from-post' data-average='$average' data-rating='$rating' data-votes='$votes'  data-id='$id'></div>

                    </div>

                </h1>            

            </div>

            <div class='aw_info' >

            <p>

                 <img src='$designer_thumb' width='45' height='45'/>

                 <div>

                    <label><h4>$designers_name:</h4></label>

                    <span>{$meta_values['kt_designers_name'][0]}</span>               

                 </div>

            </p>

            <p>

                <img src='$site_thumb' width='45' height='45'/>

                <div>

                    <label><h4>$designers_site:</h4></label>

                    <a target='_blank' href='{$meta_values['kt_designers_website'][0]}'>{$meta_values['kt_designers_website'][0]}</a>

                </div>        

            </p>

            <p>

                <img src='$email_thumb' width='45' height='45'/>

                <div>

                    <label><h4>$designers_email:</h4></label>

                    <a href="mailto:{$meta_values['kt_designers_email'][0]}?Subject=Design%20Awards" >{$meta_values['kt_designers_email'][0]}</a>

                </div>

            </p>

            <p>

                <img src='$category_thumb' width='45' height='45'/>

                <div>

                    <label><h4>$category:</h4></label>

                    <span>{$meta_values['kt_website_category'][0]}</span>

                </div>

            </p> 

            <div class='margin'></div>           

            <p>                

                <div class='hr'></div>

                <p class='description'>$excerpt</p>

            </p>
           

            </div>

        </div>

TEMPLATE;



    

    $content .= <<<SCRIPT

    <script type="text/javascript">  

	db= false;
	if(window.openDatabase){
    var db = openDatabase('aw_design_awards',1,'aw_design_awards',10000000);
	}
    if(db){

     db.transaction(function(transaction){

        var host = window.location.host;
		transaction.executeSql("select postID from design_awards_data where site=?",[host],function(transaction,results){

             for (var i=0; i < results.rows.length; i++){
				row = results.rows.item(i);

                 postID = row.postID;

                 if($id == postID){
			            var vote = document.getElementsByClassName('aw-post-rating');
						vote[0].innerHTML = 'You have already voted';
						vote[0].setAttribute('style' , 'font-size: 12px; right: 20px;')
                 }                

             }

         });

     });

 }
                 		
   if(localStorage){
    	var daw = localStorage.getItem('daw_');
    	if( typeof(daw) == 'string' && typeof(JSON.parse(JSON.parse(daw))) == 'object'){
        	daw = JSON.parse(JSON.parse(daw));
        	
        	for(var ii = 0;ii<daw.length; ii++){
        		if($id == daw[ii]){
			            var vote = document.getElementsByClassName('aw-post-rating');
						vote[0].innerHTML = 'You have already voted';
						vote[0].setAttribute('style' , 'font-size: 12px; right: 20px;')
                 }
        	}
        }
    }
    else{
    	console.log('store not working');
    }



    </script> 

<style type="text/css">

	.ratingblock {display:none;}

	.small.dark {display:none;}	

	.ttlComments+a {display:none;}

	.ttlComments {display:none;}

	.home-loop-titles {display:none;}

</style>  

SCRIPT;

    }

    endif;

    return $content;

}



function register_upload_thumbnail()

{

    wp_enqueue_script("upload_thumb");

}



?>