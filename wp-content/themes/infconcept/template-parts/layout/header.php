<?php
  $colorPage = get_field('color_page');
?>
<header>
  <div class="menu-container js-menu">
    <a href="<?php echo get_home_url(); ?>" style="text-decoration: none; color: transparent;">
      <?php if (get_field('logo_variation')) : ?>
        <img class="menu__logo" src="<?php the_field('logo_variation'); ?>" style="background-color: <?php echo $colorPage; ?>">
      <?php elseif  (get_field('logo_website_header', 'option')): ?>
        <img class="menu__logo" src="<?php the_field('logo_website_header', 'option'); ?>" style="background-color: <?php echo $colorPage; ?>">
      <?php endif; ?>
    </a>

    <div class="menu__content">
      <?php wp_nav_menu(); ?>

      <div class="menu__right">
        <?php

        $link = get_field('estimation_lien', 'option');

        if( $link ):
          $link_url = $link['url'];
          $link_title = $link['title'];
          $link_target = $link['target'] ? $link['target'] : '_self';
          ?>
          <div class="menu__estimation">
            <a class="menu__estimation__text" href="<?php echo $link_url; ?>"><?php echo $link_title; ?></a>

            <?php if (get_field('estimation_phone', 'option')): ?>
              <a class="menu__estimation__phone" href="tel:<?php the_field('estimation_phone', 'option'); ?>">
                <?php the_field('estimation_phone', 'option'); ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

          <?php do_action('wpml_add_language_selector'); ?>
      </div>
    </div>
      <div class="menu__burger">
          <i class="burger fas fa-bars"></i>
      </div>
  </div>
</header>
<?php ?>
