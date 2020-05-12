<?php ?>
    <div class="carrousel carrousel-blue carrousel--arrows" data-arrows="true" data-auto="true" data-infinite="true" data-fade="false">
      <?php
      // check if the repeater field has rows of data
      if( have_rows('carrousel') ):

        // loop through the rows of data
          while ( have_rows('carrousel') ) : the_row();
      ?>
          <div class="carrousel__image" style="background-image: url(<?php the_sub_field('image'); ?>);">

          </div>
      <?php
          endwhile;

      else :

          // no rows found

      endif;

      ?>
    </div>
<?php ?>