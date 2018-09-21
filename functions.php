<?php 

  //load css and js files into the document
  function university_files() {
    wp_enqueue_script('main-university-js', get_theme_file_uri('/js/scripts-bundled.js'), NULL,  microtime(), true);
    //the script requires these params(name-for-reference, file-path, dependency, version, position)
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
  }

  add_action('wp_enqueue_scripts', 'university_files');

  //creates the dynamic navs
  function university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('pofessorLandscape', 300, 200, true);
    add_image_size('pofessorPortait', 380, 450, true);
    //Create Menus
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footerLocation', 'Footer Menu Location');
    register_nav_menu('footerLocation2', 'Header Menu Location 2');
  }

  add_action('after_setup_theme', 'university_features');

  function university_adjust_queries($query){
   
    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
      $today = date('Ymd');
      $query->set('meta_key', 'event_date');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'ASC');
      $query->set(
        'meta_query', array(
          array(
            'key' => 'event_date',
            'compare' => '>=',
            'value' => $today,
            'type' => 'numeric'
          )
        )
      );
    }

    if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()){
      $query->set('events_per_page', -1);
      $query->set('orderby', 'title');
      $query->set('order', 'ASC');
    }


  }

  add_action('pre_get_posts', 'university_adjust_queries');

