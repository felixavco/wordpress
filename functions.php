<?php 

  require get_theme_file_path('/inc/like-route.php');
  require get_theme_file_path('/inc/search-route.php');

  function university_custom_rest (){
    register_rest_field('post', 'authorName', array(
      'get_callback' => function() {
        return get_the_author();
      }
    ));

    register_rest_field('note', 'userNoteCount', array(
      'get_callback' => function() {
        return count_user_posts(get_current_user_id(), 'note');
      }
    ));
  }

  add_action('rest_api_init', 'university_custom_rest');

  function pageBanner($args = NULL) {
    
    if(!$args['title']) $args['title'] = get_the_title();
    
    if(!$args['subtitle']) $args['subtitle'] = get_field('page_banner_subtitle');
    
    if(!$args['photo']):
      if(get_field('page_banner_background_image')):
        $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
      else:
        $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
      endif;
    endif;

?>
    <div class="page-banner">
      <div class="page-banner__bg-image" style="background-image: url(
      <?php 
        echo $args['photo'];
      ?>"></div>
      <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php echo $args['title'];?></h1>
        <div class="page-banner__intro">
          <p><?php echo $args['subtitle']; ?></p>
        </div>
      </div>
    </div>
<?php
  }

  //load css and js files into the document
  function university_files() {
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyCBQI-G24csNMz--I157NExIYHZhkEwL_8', NULL, true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/js/scripts-bundled.js'), NULL,  microtime(), true);
    //the script requires these params(name-for-reference, file-path, dependency, version, position)
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
    //adds the root URL to a variable to be used in JS
    wp_localize_script('main-university-js', 'universityData', array(
      'root_url' => get_site_url(),
      'nonce' => wp_create_nonce('wp_rest')
    ));
  }

  add_action('wp_enqueue_scripts', 'university_files');

  //creates the dynamic navs
  function university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('pofessorLandscape', 300, 200, true);
    add_image_size('pofessorPortait', 380, 450, true);
    add_image_size('pageBanner', 1600, 350, true);
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
      $query->set('posts_per_page', -1);
      $query->set('orderby', 'title');
      $query->set('order', 'ASC');
    }


    if(!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()){
      $query->set('posts_per_page', -1);
    }


  }

  add_action('pre_get_posts', 'university_adjust_queries');

  function universityMapKey($api){
    $api['key'] = 'AIzaSyCBQI-G24csNMz--I157NExIYHZhkEwL_8';
    return $api;
  }

  add_filter('acf/fields/google_map/api', 'universityMapKey');


  //Redirect subs accounts out admin to home page
  add_action('admin_init', 'redirectSubsToFrontend');

  function redirectSubsToFrontend() {

    $currentUser =  wp_get_current_user();
    if(count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber'){
      wp_redirect(site_url('/'));
      exit;
    }
  }

  add_action('wp_loaded', 'noSubsAdminBar');

  function noSubsAdminBar() {

    $currentUser =  wp_get_current_user();
    if(count($currentUser->roles) == 1 AND $currentUser->roles[0] == 'subscriber'){
      show_admin_bar( false );
    }
  }

  //Customize login screen
  add_filter('login_headerurl', 'ourHeaderUrl');

  function ourHeaderUrl() {
    return esc_url(site_url('/'));
  }

  add_action('login_enqueue_scripts', 'ourLoginCSS');

  function ourLoginCSS() {
    wp_enqueue_style('university_main_styles', get_stylesheet_uri());
    wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  }

  add_filter('login_headertitle', 'ourLoginTitle');

  function ourLoginTitle(){
    return get_bloginfo('name');
  }

  //Force note post to be private
  add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);

  function makeNotePrivate($data, $postarr) {

    if($data['post_type'] == 'note') {

      if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
        die("You have reached your Notes limit of 5");
      }

      $data['post_content'] = sanitize_textarea_field( $data['post_content'] );
      $data['post_title'] = sanitize_text_field( $data['post_title'] );
    }

    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash'){
      $data['post_status'] = 'private';
    }
    
    return $data;
  }

  //