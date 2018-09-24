<?php
  get_header();
  pageBanner(array(
    'title' => 'Our Cumpuses',
    'subtitle' => 'See our Campuses...'
  ));
?>

  <div class="container container--narrow page-section">
    <div class="acf-map">
      <?php 
        while (have_posts()): the_post();
        $mapLocation = get_field('map_location');
        $lat = $mapLocation['lat'];
        $lng = $mapLocation['lng'];
      ?>
        <div class="marker" data-lat="<?php echo $lat ?>" data-lng="<?php echo $lng ?>"></div>
      <?php
        endwhile;
        echo paginate_links();
      ?>
    </div>
  </div>
  

<?php
get_footer();
?>


