<?php
/**
 * Created by PhpStorm.
 * User: ntrudel
 * Date: 2018-12-17
 * Time: 21:11
 */
?>

<?php get_header(); 
$nbRow = 0;
?>

<div class="page" style="color: <?php if(get_field('color_page')) { the_field('color_page'); } else { echo '#1a283c'; } ?>">
  <?php get_template_part('template-parts/layout/header'); ?>
  <div class="main">
    <div class="flex-container">
      <?php

      // check if the flexible content field has rows of data
      if (have_rows('content')):

        // loop through the rows of data
        while (have_rows('content')) :
          the_row(); ?>

          <?php if (get_row_layout() == 'hero'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="hero flex-row" style="background-color: <?php the_sub_field('color');?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/hero/hero', 'content'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_image_box'); ?>">
              <?php get_template_part('template-parts/carrousel/carrousel'); ?>
            </div>
          </div>


        <?php elseif (get_row_layout() == 'text_image'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row" style="background-color: <?php the_sub_field('color'); ?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'text'); ?>
            </div>
        <div class="flex-col--12 flex-col--<?php the_sub_field('width_image_box'); ?><?php if ($nbRow == 0): ?> image-box--in-first-row<?php endif; ?>">
              <?php get_template_part('template-parts/content/content', 'image'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'image_text'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row flex-row--reverse" style="background-color: <?php the_sub_field('color'); ?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_image_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'image'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'text'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'text_team'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row" style="background-color: <?php the_sub_field('color'); ?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'text'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_image_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'team'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'text_map'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row" style="background-color: <?php the_sub_field('color'); ?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'text'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_map_box'); ?>">
              <?php get_template_part('template-parts/map'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'text_carrousel'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row" style="background-color: <?php the_sub_field('color'); ?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'text'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_carrousel_box'); ?>">
              <?php get_template_part('template-parts/carrousel/carrousel', 'arrow'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'carrousel_text'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row flex-row--reverse" style="background-color: <?php the_sub_field('color'); ?>; color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_carrousel_box'); ?>">
              <?php get_template_part('template-parts/carrousel/carrousel', 'arrow'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_text_box'); ?>">
              <?php get_template_part('template-parts/content/content', 'text'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'simple_text'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row">
            <div class="flex-col--12">
              <?php get_template_part('template-parts/content/content', 'simple'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() == 'zone_team_presentation'):
          get_template_part('template-parts/content/content', 'presentation');
          ?>

        <?php elseif (get_row_layout() === 'portfolio'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row">
            <div class="flex-col--12">
              <?php get_template_part('template-parts/portfolio/portfolio'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() === 'contact'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="flex-row" style="color: <?php the_sub_field('text_color'); ?>;">
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_zone_contact'); ?>" style="background-color: <?php the_sub_field('color_zone_contact'); ?>">
              <?php get_template_part('template-parts/contact/contact', 'content'); ?>
            </div>
            <div class="flex-col--12 flex-col--<?php the_sub_field('width_zone_form'); ?>">
              <?php get_template_part('template-parts/contact/contact', 'form'); ?>
            </div>
          </div>

        <?php elseif (get_row_layout() === 'full_map'): ?>
          <div id="<?php the_sub_field('id'); ?>" class="map-container">
            <?php get_template_part('template-parts/map'); ?>
          </div>
        <?php endif; ?>
        <?php 

        $nbRow += 1;
        endwhile;

      else :

        // no layouts found

      endif;

      ?>
    </div>
  </div>
  <?php get_template_part('template-parts/layout/footer'); ?>
</div>
<?php get_footer(); ?>
