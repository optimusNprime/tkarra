<?php
@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title' 	=> 'Champs du thème',
        'menu_title'	=> 'Champs du thème',
        'menu_slug' 	=> 'fields-costum',
        'capability'	=> 'edit_posts',
        'redirect'		=> false
    ));
}

add_action( 'wp_enqueue_scripts', 'add_my_script' );
function add_my_script() {
  wp_enqueue_script(
    'slider', // name your script so that you can attach other scripts and de-register, etc.
    get_template_directory_uri() . '/scripts/js/slider.js', // this is the location of your script file
    array('jquery') // this array lists the scripts upon which your script depends
  );

  wp_enqueue_script(
    'inview',
    get_template_directory_uri() . '/scripts/js/inview.js',
    array('jquery')
  );
}

/**
 * Create main menu.
 */
register_nav_menus( array(
  'main_menu' => 'Main menu'
));

add_theme_support('post-thumbnails');
add_post_type_support( 'works', 'thumbnail' );

function create_post_type() {
  register_post_type( 'works',
    array(
      'labels' => array(
        'name' => __( 'Works' ),
        'singular_name' => __( 'Works' )
      ),
      'public' => true,
      'supports' => array('title', 'editor', 'thumbnail'),
    )
  );
}
add_action( 'init', 'create_post_type' );
?>