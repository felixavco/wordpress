<?php
  get_header();

  pageBanner(array(
    'title' => 'All Events',
    'subtitle' => 'See our up coming events...'
  ));
?>

  <div class="container container--narrow page-section">
  <?php 
    while (have_posts()): the_post();

    get_template_part('./template_parts/content', 'event');
    
    endwhile;
    echo paginate_links();
  ?>
    <hr class="section-break">
    <p>To see our Past Events click <a href="<?php echo site_url('/past-events') ?>">HERE</a> </p>
  </div>
  

<?php
get_footer();
?>

