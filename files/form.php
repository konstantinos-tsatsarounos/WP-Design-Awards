<?php

function nominee_form()
{

    //Enqueue the css for the form
    wp_enqueue_style( 'kt_main_plugin_style');

    $options = get_option('options_'.PLUGINS_QUERY);
    global $lang;
    
    $preview_height = THUBNAIL_HEIGHT*0.84;
    $preview_width = THUBNAIL_WIDTH*0.84;
    $default_preview = PLUGIN_PATH .'images/default_image_preview.png';   
   
    if(!$options) $options = array('image_type'=> 'png');

    //Detect if there is a value for each box in global POST, if there is it return it!
    $website_title = isset($_POST['website_title'])?$_POST['website_title']:'';
    $website_url = isset($_POST['website_url'])?$_POST['website_url']:'';
    $designers_name = isset($_POST['designers_name'])?$_POST['designers_name']:'';
    $designers_email = isset($_POST['designers_email'])?$_POST['designers_email']:'';
    $designers_url = isset($_POST['designers_url'])?$_POST['designers_url']:'';
    $website_desc = isset($_POST['website_desc'])?$_POST['website_desc']:'';
    $is_redesign = isset($_POST['is_redesign'])? "checked='checked'": '';
    $website_category = isset($_POST['website_category'])? $_POST['website_category']: '';
    $image_type = (isset($options['image_type']) && $options['image_type']!='*')? $options['image_type'] : 'any type';
    $accept = 'image/'.$options['image_type'];
    $nonce = wp_nonce_field('upload_image_form','upload_image_form', true, false );

    //Generates the form html
    $output = "<form action='' class='nomineeForm' method='post' name='nominee' id='nominee' enctype='multipart/form-data' >";
    $output .= "<h4>".$lang['form_header']."</h4>";
    $output .= "<p></p>";
    $output .= "<p>";     
    $output .= "<label for='website_url'>".$lang['website_title']."</label>";
    $output .= "<input class='text' type='text' id='website_title'  name='website_title' value='$website_title' />";

    $output .= "<label for='website_url'>".$lang['website_url']."</label>";
    $output .= "<input class='text' type='text' id='website_url'  name='website_url' value='$website_url' />";
    
    if(!!$options && isset($options['website_category']) && $options['website_category'] && is_string($options['website_category'])):
        $categories = explode(',',$options['website_category']);
        $output .="<label for='website_category'>".$lang['category']."</label>";
        $output .= "<select name='website_category' id='website_category' >";
        $output .= "<option value='no_category' default=>".$lang['no_category']."</option>";
        foreach($categories as $category):
            $selected = ($category==$website_category)? 'selected="selected"': '';
            $output .= "<option value='$category' $selected >".ucfirst($category)."</option>";
        endforeach;
        $output .= "</select>";
    endif;   
    $output .= "<label for='is_redesign'>".$lang['is_redesign']."</label>";
    $output .= "<input type='checkbox' $is_redesign id='is_redesign' name='is_redesign'/>";    
    
    $output .= "<img id='nominee_image_preview' src='$default_preview' class='image_preview' width='$preview_width' height='$preview_height' />";
       
    //$output .= "<label for='kt_file_send_button' class='button'>".$lang['submit_thumbnail'];
    //$output .= "<div class='hide-more'>";
    $output .= "<input id='kt_file_send_button' type='file' name='website_screenshot' accept='$accept' />";
    //$output .= "<span>".$lang['submit_thumbnail']."</span>";
    //$output .= "</div>";
     
    $output .= "<label for='designers_name'>".$lang['designers_name']."</label>";
    $output .= "<input class='text' type='text' id='designers_name' name='designers_name' value='$designers_name' />";

    $output .= "<label for='designers_email'>".$lang['designers_email']."</label>";
    $output .= "<input class='text' type='email' id='designers_email' name='designers_email' value='$designers_email' />";

    $output .= "<label for='designers_url'>".$lang['designers_url']."</label>";
    $output .= "<input class='text' type='text' id='designers_url' name='designers_url' value='$designers_url' />";

    $output .= "<label for='website_desc'>".$lang['description']."</label>";
    $output .= "<textarea type='text' class='text' id='website_desc' name='website_desc'>$website_desc</textarea>";
    
    $output .= "</p>"; 
      
    $output .= "<input class='button not-first' type='submit' name='submit_website' value='".$lang['submit_your_website']."' />";
  
    $output .= wp_nonce_field('nominee','nominee_form', true, false );
    $output .= "</form>";
    $output .= "<span class='notes'>image size must be ".$image_type.' '.THUBNAIL_WIDTH.'x'.THUBNAIL_HEIGHT." px</span>";

    //If an error returned from the validation function, this display the content!
    if(isset($_POST['error']) && !empty($_POST['error'])):
        $output .= "<div class='error'>". $_POST['error'] ."</div>";
    elseif(!isset($_POST['error']) && isset($_POST['website_url'])): $output .= "<div class='success'><p>".$lang['site_submitted']."</p></div>";
    endif;  
    
    $output .= <<<SCRIPT
   <script type='text/javascript' >
    var reader = new FileReader(),
        file = document.getElementById('kt_file_send_button'),
        img = document.getElementById('nominee_image_preview');
              

        file.onchange = function(){
                reader.onloadend = function() {                
                    img.src = reader.result;
                   
                }
               
                reader.readAsDataURL(file.files[0]);
                
            }
var fileLabel =  document.querySelector('label[for=kt_file_send_button]');

var fileOpen = false;
if(navigator.userAgent.indexOf("Firefox")!=-1 && !fileOpen){
    fileLabel.ondblclick = openFileDialog;

fileOpen = true;
}

function openFileDialog(e){
	var file = document.getElementById('kt_file_send_button');
	file.click();	
	return false;
} 
   </script>
   <style type='text/css'> .home-loop-titles { display:none;} h4 { padding-top:0; margin-top:0;}</style>
SCRIPT;
    
    return $output;
}


?>