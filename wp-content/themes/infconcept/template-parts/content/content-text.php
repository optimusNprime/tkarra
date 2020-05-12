<?php ?>
    <div class="text-box" style="color: <?php the_sub_field('text_color') ?>;">
      <?php if (get_sub_field('subtitle')): ?>
          <div class="text-box__subtitle inview--not" data-module="inview">
            <?php the_sub_field('subtitle'); ?>
          </div>
      <?php endif; ?>

      <?php if (get_sub_field('title')): ?>
          <div class="text-box__title inview--not" data-module="inview">
            <?php the_sub_field('title'); ?>
          </div>
      <?php endif; ?>

      <?php if (get_sub_field('text')): ?>
          <div class="text-box__text inview--not" data-module="inview">
            <?php the_sub_field('text'); ?>
          </div>
      <?php endif; ?>

      <?php if (have_rows('list')):
        while (have_rows('list')) : the_row();
          ?>
            <div class="text-box__list list">
              <?php if (get_sub_field('list_title')): ?>
                  <p class="list__title inview--not" data-module="inview">
                    <?php the_sub_field('list_title'); ?>
                  </p>
              <?php endif; ?>

              <?php
              $twoList = get_sub_field('list_right') ? 'list__items--two' : '';
              if (get_sub_field('list_left')): ?>
                  <div class="list__items inview--not <?php echo $twoList; ?>" data-module="inview">
                    <?php the_sub_field('list_left'); ?>

                    <?php if (get_sub_field('list_right')):
                      the_sub_field('list_right');
                    endif; ?>
                  </div>
              <?php endif; ?>
            </div>
        <?php endwhile; ?>
      <?php endif; ?>

      <?php if (have_rows('files')):
        while (have_rows('files')) : the_row();
          $counter = 1;
          $file = get_sub_field('file');
          $url = $file['url'];
          if ($url) :
            ?>
              <div class="button-container inview--not <?php echo $counter; ?> " data-module="inview">
                  <a class="button button--file" href="<?php echo $url; ?>" target="_blank"
                     style="background-color: <?php the_sub_field('color'); ?>"><?php the_sub_field('name') ?>
                      <span class="button__icons">
                      <i class="fas button__arrow fa-long-arrow-alt-down"></i>
                      <i class="far button__bar fa-window-minimize"></i>
                  </span>
                  </a>
              </div>
          <?php
          endif;
          $counter++;
        endwhile;
      endif;
      ?>

      <?php
      $link = get_sub_field('link');

      if ($link):
        $link_url = $link['url'];
        $link_title = $link['title'];
        $link_target = $link['target'] ? $link['target'] : '_self';
        ?>

          <div class="text-box__link inview--not" data-module="inview">
              <a href="<?php echo $link_url; ?>" class="link read-more" target="<?php echo $link_target; ?>">
                <?php echo $link_title; ?>
              </a>
          </div>
      <?php endif; ?>
    </div>
<?php ?>