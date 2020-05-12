<?php ?>
<footer class="footer" style="background-color: <?php if(get_field('color_page')) { the_field('color_page'); } else { echo '#1a283c'; } ?>">
    <div class="footer__item footer__copy">
        <img class="footer__logo" src="<?php the_field('logo_website_header', 'option'); ?>">
        <span class="footer__copyright">
            <?php the_field('copyright', 'option'); ?>
        </span>
    </div>
    <div class="footer__item">
      <?php wp_nav_menu(); ?>
    </div>
    <div class="footer__item footer__follow">
        <div class="footer__follow__text">
                <?php the_field('text_social_media', 'option'); ?>
        </div>
        <div class="footer__follow__icons">
          <?php

          // check if the repeater field has rows of data
          if( have_rows('social_media', 'option') ):

            // loop through the rows of data
            while ( have_rows('social_media', 'option') ) : the_row(); ?>
                <a class="footer__follow__icon" href="<?php the_sub_field('lien') ?>" target="_blank">
                    <div link="<?php the_sub_field('lien') ?>">
                    </div>
                </a>
          <?php  endwhile;

          else :

            // no rows found

          endif;

          ?>
        </div>
    </div>
    <div class="footer__item">
        <div data-module="to-top">
            <?php the_field('back_to_top', 'option'); ?>
        </div>
    </div>
</footer>