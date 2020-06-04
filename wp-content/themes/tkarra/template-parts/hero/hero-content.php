<?php ?>
    <div class="hero__content" style="color: <?php the_sub_field('text_color'); ?>;">
        <div class="hero__content__top">
          <?php if (get_sub_field('title')): ?>
              <h1 class="h1 hero__title inview--not" data-module="inview">
                <?php the_sub_field('title'); ?>
              </h1>
          <?php endif; ?>

          <?php if (get_sub_field('text')): ?>
              <div class="hero__subtitle inview--not inview--1" data-module="inview">
                <?php the_sub_field('text'); ?>
              </div>
          <?php endif; ?>
        </div>
      <?php if (get_sub_field('image')): ?> 
        <div class="hero__container-image inview--not inview--2" data-module="inview" data-inview="hero-image">
          <img class="hero__image" src="<?php the_sub_field('image'); ?>" alt="">
        </div>
      <?php endif; ?>
    </div>
<?php ?>
