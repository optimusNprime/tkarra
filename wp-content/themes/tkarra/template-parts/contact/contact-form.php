<?php ?>
<div class="contact__form contact__zone">
  <?php if (get_sub_field('title_form')): ?>
    <h1 class="contact__title inview--not"  data-module="inview">
      <?php the_sub_field('title_form'); ?>
    </h1>
  <?php endif; ?>
  <div>
    <?php if (get_sub_field('text_file')): ?>
      <div class="contact__form__text">
        <?php the_sub_field('text_file'); ?>
      </div>
    <?php endif; ?>
    <?php
    $file = get_sub_field('file');

    if( $file ): ?>
      <a class="read-more contact__form__file" href="<?php echo $file['url']; ?>" target="_blank"><?php echo $file['title']; ?></a>
    <?php endif; ?>
  </div>
    <div class="form"  data-module="inview">
      <?php the_sub_field('form'); ?>
  </div>
</div>
