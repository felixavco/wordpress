<?php 
  while(have_posts()){
    the_post(); 
?> 
  <h1>this is a page</h1>
  <h2><?php the_title(); ?></h2>
  <p><?php the_content() ?></p>
<?php
  }
?>
