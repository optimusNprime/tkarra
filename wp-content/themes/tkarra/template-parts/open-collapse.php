<?php ?>
    <div class="open-collapse">
    <?php
    // Check rows exists.
    if( have_rows('open-collapse-repeater') ):
        // Loop through rows.
        while( have_rows('open-collapse-repeater') ) : the_row();
    ?>
        <div class="open-collapse__drawer" data-module="openCollapse">
            <button class="open-collapse__drawer__button">
                <?php the_sub_field('title'); ?>
            </button>
            <div class="open-collapse__drawer__content-container text-box">
                <div class="open-collapse__drawer__content text-box__text">
                    <?php the_sub_field('content'); ?>
                </div>
            </div>
        </div>

    <?php
        // End loop.
        endwhile;

    // No value.
    else :
        // Do something...
    endif;
    ?>
    </div>
<?php ?>