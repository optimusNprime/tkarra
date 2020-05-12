<?php
/**
 * Created by PhpStorm.
 * User: ntrudel
 * Date: 2019-02-07
 * Time: 21:40
 */
?>

<div class="content content--simple" style="background-color: <?php the_sub_field('color') ?>; color: <?php the_sub_field('text_color') ?>;">
  <div class="box">
    <?php if (get_sub_field('title')): ?>
      <h3 class="title inview--not" data-module="inview">
        <?php the_sub_field('title'); ?>
      </h3>
    <?php endif; ?>

    <?php if (get_sub_field('text')): ?>
      <div class="text inview--not" data-module="inview">
        <?php the_sub_field('text'); ?>
      </div>
    <?php endif; ?>

    <?php
    $link = get_sub_field('link');

    if ($link):
      $link_url = $link['url'];
      $link_title = $link['title'];
      $link_target = $link['target'] ? $link['target'] : '_self';
      ?>

        <div class="link inview--not" data-module="inview">
            <a href="<?php echo $link_url; ?>" class="link read-more" target="<?php echo $link_target; ?>">
              <?php echo $link_title; ?>
            </a>
        </div>
    <?php endif; ?>
  </div>
</div>
