<?php ?>
<div class="contact__content contact__zone">
  <?php if (get_sub_field('title')): ?>
    <h1 class="contact__title inview--not" data-module="inview">
      <?php the_sub_field('title'); ?>
    </h1>
    <div class="contact__text inview--not" data-module="inview">
      <?php the_sub_field('text'); ?>
    </div>
  <?php endif; ?>
    <div class="contact__details">
        <?php if (get_sub_field('title_telephone')): ?>
            <div class="contact__detail">
            <h3 class="contact__detail__title inview--not" data-module="inview">
                <?php the_sub_field('title_telephone'); ?>
            </h3>
          <?php

          // check if the repeater field has rows of data
          if( have_rows('telephones') ):

            // loop through the rows of data
            while ( have_rows('telephones') ) : the_row(); ?>

            <div class="contact__detail__info inview--not"  data-module="inview">
                <p class="contact__detail__name">
                    <?php the_sub_field('name'); ?>
                </p>
                <a class="contact__detail__link" href="tel:<?php the_sub_field('number'); ?>">
                  <?php the_sub_field('number'); ?>
                </a>
            </div>

            <?php endwhile;

          else :

            // no rows found

          endif;

          ?>
        </div>
        <?php endif; ?>
        <?php if (get_sub_field('titre_courriels')): ?>
            <div class="contact__detail">
                <h3 class="contact__detail__title inview--not"  data-module="inview">
                  <?php the_sub_field('titre_courriels'); ?>
                </h3>
              <?php

              // check if the repeater field has rows of data
              if( have_rows('emails') ):

                // loop through the rows of data
                while ( have_rows('emails') ) : the_row(); ?>

                    <div class="contact__detail__info inview--not"  data-module="inview">
                        <a  class="contact__detail__link" href="mailto:<?php the_sub_field('email'); ?>">
                          <?php the_sub_field('email'); ?>
                        </a>
                    </div>

                <?php endwhile;

              else :

                // no rows found

              endif;

              ?>
            </div>
        <?php endif; ?>
    </div>
</div>
