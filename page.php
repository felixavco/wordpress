<?php
get_header();

while (have_posts()): the_post();

  pageBanner();
?>
      <div class="container container--narrow page-section">

    <?php
      $parent = wp_get_post_parent_id(get_the_ID());
      if ($parent):
    ?>

    <div class="metabox metabox--position-up metabox--with-home-link">
      <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($parent)?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($parent) ?></a> <span class="metabox__main"><?php the_title()?></span></p>
    </div>

    <?php
    endif;
    ?>


  <?php 
    $notParent_notChild = get_pages(array('child_of' => get_the_ID()));

    if($parent or $notParent_notChild) { ?>
      <div class="page-links">
        <h2 class="page-links__title"><a href="<?php echo get_permalink($parent) ?>"><?php echo get_the_title($parent)?></a></h2>
        <ul class="min-list">
          <?php 
            if($parent) {
              $findChildrenOf =  $parent; // if a page has a children use this ID
            } else {
              $findChildrenOf = get_the_ID(); //if the page does not have a children get the ID of the page
            }
            wp_list_pages(array(
              'title_li' => null,
              'child_of' => $findChildrenOf
            ));
          ?>
        </ul>
      </div>
  <?php } ?>

    <div class="generic-content">
      <?php the_content();?>
    </div>

  </div>

<?php
endwhile;

get_footer();
?>


