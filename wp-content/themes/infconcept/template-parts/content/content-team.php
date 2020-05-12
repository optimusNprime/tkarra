<?php ?>
  <div class="team-box">
    <?php

    // check if the repeater field has rows of data
    if( have_rows('team') ):

      // loop through the rows of data
      while ( have_rows('team') ) : the_row(); ?>

      <div class="team">

        <?php if(get_sub_field('image')): ?>
          <div class="team__image inview--not" style="background-image: url(<?php the_sub_field('image') ?>);" data-module="inview"></div>
        <?php endif; ?>

        <?php if( get_sub_field('name')): ?>
          <h3 class="team__name inview--not" data-module="inview">
            <?php the_sub_field('name'); ?>
          </h3>
        <?php endif; ?>

        <?php if( get_sub_field('title')): ?>
          <p class="team__title inview--not" data-module="inview">
            <?php the_sub_field('title'); ?>
          </p>
        <?php endif; ?>

      </div>

      <?php endwhile;

    else :

      // no rows found

    endif;

    ?>
  </div>
<?php ?>