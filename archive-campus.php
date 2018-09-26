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
        <div class="marker" data-lat="<?php echo $lat ?>" data-lng="<?php echo $lng ?>">
          <a href="<?php the_permalink() ?>"><h3><?php the_title() ?></h3></a>
          <?php echo $mapLocation['address']?>
        </div>
      <?php
        endwhile;
      ?>
    </div>
  </div>
  

<?php
get_footer();
?>


