<?php
  function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = array($r, $g, $b);

    return $rgb; // returns an array with the rgb values
  }

  $bgcolor = get_sub_field('background_color');
  $color = get_sub_field('color');

  $RGB_color = hex2rgb($color);
  $Final_Rgb_color = implode(", ", $RGB_color);
?>
<div class="flex-row">
  <div class="flex-col--12" style="background-color: <?php echo $bgcolor; ?>; color: <?php echo $color; ?>;">
    <h2 class="team-member__section-title inview--not"  data-module="inview">
      <?php echo the_sub_field('title'); ?>
    </h2>
  </div>
</div>
<div class="flex-row">
  <?php

  // check if the repeater field has rows of data
  if( have_rows('team') ):

    // loop through the rows of data
    while ( have_rows('team') ) : the_row(); ?>

    <div class="flex-col--12 flex-col--4 flex-vertical" style="background-color: <?php echo $color;?>; color: <?php echo $bgcolor; ?>;">
      <?php
      $membername = get_sub_field('nom');
      $membertitle = get_sub_field('title');
      ?>

      <div class="team-member">
        <div class="team-member__content" style="background-image: url(<?php the_sub_field('image'); ?>);">
          <div class="team-member__text" style="background-color: rgba(<?php echo $Final_Rgb_color; ?>, 0.89);">
            <span>
              <?php the_sub_field('text'); ?>
            </span>
          </div>
        </div>
        <div class="team-member__info" style="background-color: <?php echo $bgcolor; ?>; color: <?php echo $color; ?>;" >
          <?php if($membername): ?>
            <div class="team-member__name inview--not" data-module="inview">
              <?php echo $membername; ?>
            </div>
          <?php endif; ?>

          <?php if($membertitle): ?>
            <div class="team-member__title inview--not" data-module="inview">
              <?php echo $membertitle; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  <?php endwhile;
  else :
  endif;

  ?>
  <?php if (get_sub_field('team_presentation') || get_sub_field('link title') || get_sub_field('link_subtitle')): ?>
    <div class="flex-col--12 flex-col--4 flex-vertical" style="background-color: <?php echo $bgcolor; ?>; color: <?php echo $color; ?>;">
      <div class="team-member__presentation" style="background-color: <?php echo $color; ?>; color: <?php echo $bgcolor; ?>;">
        <span class="inview--not" data-module="inview">
          <?php the_sub_field('team_presentation'); ?>
        </span>
      </div>
      <a href="<?php the_sub_field('link'); ?>" class="team-member__info" style="background-color: <?php echo $bgcolor; ?>; color: <?php echo $color; ?>;">
        <div class="team-member__name inview--not" data-module="inview">
          <?php the_sub_field('link title') ?>
        </div>

        <div class="team-member__title inview--not" data-module="inview">
          <?php the_sub_field('link_subtitle') ?>
        </div>
      </a>
    </div>
 <?php endif; ?>
</div>