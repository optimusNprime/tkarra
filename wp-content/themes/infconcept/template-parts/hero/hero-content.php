<?php ?>
    <div class="hero__content" style="color: <?php the_sub_field('text_color'); ?>;">
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
        <?php if (get_sub_field('logo')): ?>
            <img class="hero__logo" src="<?php the_sub_field('logo') ?>">
        <?php endif; ?>
    </div>
<?php ?>
