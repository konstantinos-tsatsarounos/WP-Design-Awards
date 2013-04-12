<?php

function query_slug($slug = false)
{   
    global $custom_post;
    $post_type = $custom_post['post_id'];
    if($slug!=false && is_string($slug)){
        return new WP_Query( "taxonomy=Participants&post_type=$post_type&term=$slug&orderby=date" );}
    else {
        return null;
    }
}

function get_stats($table_name, $id)
{
    global $wpdb;
    
    $stats = $wpdb->get_row("SELECT rating,votes_count FROM $table_name where post_id = $id");

    //Calculate average rating
    $average = ($stats->votes_count!=0)? round($stats->rating/$stats->votes_count,2): 0;
    $rating = $stats->rating;
    $votes = $stats->votes_count;
    
    return array(
        'average' => $average,
        'rating' => $rating,
        'votes' => $votes
    );
}

function item_gallery_template(array $arr)
{
    $id= null;
    $title = null;
    $description = null;
    $link = null;
    $site_link = null;
    $view_site = null;
    $details = null;
    $post_width = null;
    $post_height = null;
    $post_background = null;
    $average = null;
    $votes = null; 
    $rating = null;
    
    $post_url = PLUGIN_PATH.'css/infos.png';
    $site_url  = PLUGIN_PATH.'css/link.png';
    
     $vars = array(
         'id' => '',
         'title' => 'title',
         'site_link' => '.',
         'description' => 'description',
         'link' => '.',
         'view_site' => 'view site',
         'details' => 'details',
         'post_width' => THUBNAIL_WIDTH / THUBNAIL_FACTOR,
         'post_height' => THUBNAIL_HEIGHT / THUBNAIL_FACTOR,
         'post_background' => '.',
         'average' => '0',
         'votes' => '0',
         'rating' => '0',
     );
    
     $arr = array_merge($vars, $arr);     
     extract($arr);     
   
     
     $output = <<<TEMPLATE
              <div class='aw_post'  style='background: #fff; width: {$post_width}px; height: {$post_height}px; '>
                <h2 class='title'>$title</h2>
                <p class='description'>$description</p>
                <div class='aw_buttons' >
                    <a target='_blank' class='aw_link aw_site' style="background: url('$site_url') no-repeat; zoom:.9;" href='$site_link' alt='$view_site' title='$view_site'></a>                  
                    <a  class="aw_link aw_permalink" style="background: url('$post_url') no-repeat; zoom:.9;" href='$link' alt='$details' title='$details'></a>
                </div>
                <div class=rating-stats>$average / $votes</div>
                <div class='rating-box' style='width: {$post_width}px;'>
                    <div class='rating' data-average='$average' data-rating='$rating' data-votes='$votes'  data-id='$id'></div>
                </div>
                <div class='cover'></div>
                <div class='aw-post-shadow'  style='width: {$post_width}px; height: {$post_height}px; '></div>
                <img class='aw-background' src='{$post_background}' width='{$post_width}' height='{$post_height}' />
              </div>
TEMPLATE;
     return $output;
}


function display_gallery($atts)
{    
    $nominations_slug = false;   
    $sites_per_page = 9;
    $already_used = array();
    $no_pagination = false;
    
    extract($atts);
    global $custom_post, $wpdb, $lang;

    wp_enqueue_style( 'kt_main_plugin_style');
    wp_enqueue_style( 'kt_rating_css');    
  

    if(!isset($category_slug)) {
        return 'there is no category';
    }
    
    $term = $wpdb->prefix . 'terms';
    
    if($category_id = $wpdb->get_results("select term_id from $term where slug = '$category_slug'"))
    {

        $table_name = $wpdb->prefix . DATABASE_TABLE_NAME;
        $related = $wpdb->prefix . 'term_relationships';
        $counter = 0;

        $output = "<div class='css-gallery' >";
        $category_id = (isset($category_id))? $category_id : 0;

           
        $post_type = $custom_post['post_id'];
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;        
       
        $p = query_posts(
                array(              
                'post_type' => $post_type,
                'posts_per_page' => $sites_per_page,
                'post__not_in' => $already_used,
                'paged' => $paged
                ));
        
       $page_title = (strpos($category_slug, 'gallery') == false )? $lang[$category_slug] : $lang['gallery'];  
       
       
       $output .= '<h3 class="entry-title winner-header">'.ucfirst ($page_title).'</h3>';
       
        while(have_posts())
        {
            the_post();

            //Values
            $id = get_the_ID();           
            $description = get_the_content();
            $link = get_permalink();
            $status =  get_post_status( $id );
            $terms = wp_get_post_terms($id, 'Participants', array("fields" => "all") );
            $short_terms = array();
            $count =0;

            foreach($terms as  $cat):
                $short_terms[$count++] = $cat->slug;
            endforeach;

            if(!in_array($category_slug, $short_terms) || $status != 'publish' ) continue;
            $counter++;

            //Set width
            $post_width = THUBNAIL_WIDTH / THUBNAIL_FACTOR;
            //Set height
            $post_height = THUBNAIL_HEIGHT / THUBNAIL_FACTOR;
          
            //language
            $view_site = $lang['view_site'];
            $details = $lang['details'];


            //Get thumbnail
            $meta_values = get_post_meta($id,'kt_website_thumbnail');
            $title = get_post_meta($id,'kt_website_title');
            $site_url = get_post_meta($id,'kt_website_url');
            //Get Post Stats
            $average = false;
            $votes = false;
            $rating = false;
                   
            $stats = get_stats($table_name, $id);
            extract($stats);

            $vars = array(
                'id' => $id,
                'title' => $title[0],
                'description' => $description,
                'site_link' => $site_url[0],
                'link' => $link,
                'view_site' => $view_site,
                'details' => $details,
                'post_width' => $post_width,
                'post_height' => $post_height,
                'post_background' => $meta_values[0],
                'average' => $average,
                'votes' => $votes,
                'rating' => $rating
            );

            //Template
            if(in_array($id, $already_used)) continue;
            $already_used[]  = $id;
            $output .= item_gallery_template($vars);
    }
    if($extended!=false)
    if($counter > 0) $output .= '<h4 class="winner-header"><a  href="'.$gallery_link.'" >'.ucfirst ($lang['gallery']).'</a></h4>'; 
    if($no_pagination==false)
    $output .= kriesi_pagination();
    
    wp_reset_query();   
   
     
    $output .= ($counter == 0) ? "<h3 style='color: #000'>no results to see!</h3>": '';    
    $output .= "</div>";
     }
    else{
        return "<h3 style='color: #000'>there is not a category with this slug!</h3>";
    }
    $output .= "<style type='text/css'> .home-loop-titles { display:none;}</style>";
    return $output;

}

function kriesi_pagination($pages = '', $range = 2)
{  
     $output = '';
     $showitems = ($range * 2)+1;  

     global $paged;
     if(empty($paged)) $paged = 1;

     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   

     if(1 != $pages)
     {
         $output .= "<div class='pagination wp-paginate' id='paganition'>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) $output .= "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
         if($paged > 1 && $showitems < $pages) $output .= "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";

         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 $output .= ($paged == $i)? "<span class='current'>".$i."</span>":"<a  class='page' href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
             }
         }

         if ($paged < $pages && $showitems < $pages) $output .= "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) $output .= "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
         $output .= "</div>\n";
     }
     return $output;
}

function simple_pagination()
{
    global $wp_query;
    $output = '';
    $total_pages = $wp_query->max_num_pages;
    if($total_pages>1):
        if ( !$current_page = get_query_var('paged') ) $current_page = 1;
       
        $permalink_structure = get_option('permalink_structure');
        $format = empty($permalink_structure) ? '&page=%#%' : 'page/%#%/';
        $output .= paginate_links(array(         
          'base' => get_pagenum_link(1) . '%_%',
          'format' => $format,
          'current' => $current_page,
          'total' => $total_pages,
          'mid_size' => 4,
          'type' => 'list'
     ));
    endif;
  return $output;
}

