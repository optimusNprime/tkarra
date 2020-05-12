<?php

/**
 *
 * Media Boxes by David Blanco
 *
 * @package Media Boxes by David Blanco
 * @author  David Blanco
 * 
 */

class Media_Boxes{

/* ====================================================================== *
      VARIABLES
 * ====================================================================== */	

	protected $menu_pages 				= null;

/* ====================================================================== *
      INIT PLUGIN
 * ====================================================================== */

	public function __construct(){

		/* Add the options pages to the side bar menu */
		add_action( 'admin_menu', array( $this, 'register_menu_pages' ) );

		/* Load admin css and js */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_global_admin_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_js' ) );

		/* Load plugin styles and js (public) */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ) );

		/* Save options throught Ajax */
		add_action( 'wp_ajax_media_boxes_save_admin_options', array( $this, 'media_boxes_save_admin_options') );
		add_action( 'wp_ajax_save_skin_editor_options', array( $this, 'save_skin_editor_options') );

		/* Import/Export options */
		add_action( 'admin_init', array( $this, 'import_options') );
		add_action( 'admin_init', array( $this, 'export_options') );

		/* Create meta box */
		add_action( 'init',  array( $this, 'create_meta_box' ), 9999 );
	}

/* ====================================================================== *
      SAVE ADMIN OPTIONS VIA AJAX
 * ====================================================================== */

	public static function media_boxes_save_admin_options() { 
			
		$portfolios = get_option( MEDIA_BOXES_PREFIX . '_portfolios' );

		$uniqid = $_POST['uniqid']; /* Get the uniqid stored in the form */

		/* ==  Check if the shortcode_id is not already in use == */
		$shortcode_id_in_use = false;
		foreach ($portfolios as $key => $value) {
			if( $value['shortcode_id'] == $_POST['shortcode_id'] && $value['uniqid'] != $uniqid ){ /* it doesn't count the current one (when you are editing) */
				$shortcode_id_in_use = true;
				break;
			}
		}

		if( $shortcode_id_in_use == true ){
			echo "in_use";
		}else{

			/* == Save the options == */
			$new_portfolios = $portfolios; /* Make sure to put the portfolios that are already in the DB */

			if( $uniqid == 'none' ){
				$new_uniqid = uniqid();
				$_POST['uniqid'] = $new_uniqid;	/* Set a new uniqid if it is new */
				$uniqid = $new_uniqid;
			}

			unset($_POST['action']); /* Remove the action property so it doesn't get saved into the DB */

			$new_portfolios[$uniqid] = self::clean_post($_POST, array()); /* Add a new portfolio with its ID */

			update_option ( MEDIA_BOXES_PREFIX . '_portfolios', $new_portfolios );
			echo $uniqid;

		}

		die();
	}

/* ====================================================================== *
      SAVE SKIN EDITOR OPTIONS VIA AJAX
 * ====================================================================== */

	public static function save_skin_editor_options() { 
			
		$skins = get_option( MEDIA_BOXES_PREFIX . '_skins' );

		$uniqid = $_POST['uniqid']; /* Get the uniqid stored in the form */

		/* == Save the options == */
		$new_skins = $skins; /* Make sure to put the portfolios that are already in the DB */

		if( $uniqid == 'none' ){
			$new_uniqid = uniqid();
			$_POST['uniqid'] = $new_uniqid;	/* Set a new uniqid if it is new */
			$uniqid = $new_uniqid;
		}

		unset($_POST['action']); /* Remove the action property so it doesn't get saved into the DB */

		$new_skins[$uniqid] = self::clean_post($_POST, array( 'style_editor_css' )); /* Add a new skin with its ID */

		update_option ( MEDIA_BOXES_PREFIX . '_skins', $new_skins );
		echo $uniqid;

		die();
	}

/* ====================================================================== *
      CLEAN INPUT FIELDS FROM POST
 * ====================================================================== */	

    public static function clean_post( $post, $except_these_keys ){

    	foreach ($post as $key => $value) {
    		if(is_array($value)){
    			$post[$key] = self::clean_post($value, $except_these_keys);
    		}else{
    			if(!in_array( $key, $except_these_keys )){
    				$post[$key] = stripslashes( trim( $value ) );
    			}
    		}
    	}

    	return $post;
    }

/* ====================================================================== *
      REGISTER ADMIN MENU PAGES
 * ====================================================================== */

	public function register_menu_pages() {

		/* Main page */
		$this->menu_pages[] = add_menu_page(
			'Media Boxes',
			'Media Boxes',
			'manage_options',
			MEDIA_BOXES_PREFIX, 
			array( $this, 'media_boxes_admin' ),
			MEDIA_BOXES_URI . 'admin/includes/images/media-boxes-mini-logo.png'
		);

		/* Skin editor */
		$this->menu_pages[] = add_submenu_page( 
			MEDIA_BOXES_PREFIX,
			'Skin Editor',
			'Skin Editor',
			'manage_options',
			MEDIA_BOXES_PREFIX . '-skin-editor', 
			array( $this, 'plugin_submenu_page_skin_editor' )
		);

		/* Import/Export page */
		$this->menu_pages[] = add_submenu_page( 
			MEDIA_BOXES_PREFIX,
			'Import/Export',
			'Import/Export',
			'manage_options',
			MEDIA_BOXES_PREFIX . '-import-export', 
			array( $this, 'plugin_submenu_page_import_export' )
		);

	}

	public function media_boxes_admin() {
		include_once( MEDIA_BOXES_ADMIN . 'admin.php' );
	}
	 
	public function plugin_submenu_page_skin_editor() {
		include_once( MEDIA_BOXES_ADMIN. 'skin_editor.php' );
	}
	 
	public function plugin_submenu_page_import_export() {
		include_once( MEDIA_BOXES_ADMIN . 'import_export.php' );
	}

/* ====================================================================== *
      REGISTER GLOBAL ADMIN CSS
 * ====================================================================== */

	public function enqueue_global_admin_css(){
		wp_enqueue_style( MEDIA_BOXES_PREFIX .'-global-css', MEDIA_BOXES_URI . 'admin/includes/css/global.css', array(), MEDIA_BOXES_VERSION );
	}


/* ====================================================================== *
      REGISTER ADMIN CSS
 * ====================================================================== */
	 
	public function enqueue_admin_css() {

		if ( ! isset( $this->menu_pages ) ) { return; }

		$screen = get_current_screen();
		
		if ( in_array( $screen->id, $this->menu_pages ) ) {
			
			global $wp_version;
			
			/* Load color picker */
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_style('wp-jquery-ui-dialog');

			/* Load plugin CSS styles for the admin area */
			wp_enqueue_style( MEDIA_BOXES_PREFIX .'-admin-css', MEDIA_BOXES_URI . 'admin/includes/css/admin.css', array(), MEDIA_BOXES_VERSION );
			wp_enqueue_style( MEDIA_BOXES_PREFIX .'-skin-editor-css', MEDIA_BOXES_URI . 'admin/includes/css/skin_editor.css', array(), MEDIA_BOXES_VERSION );
			wp_enqueue_style( MEDIA_BOXES_PREFIX .'-import-export-css', MEDIA_BOXES_URI . 'admin/includes/css/import_export.css', array(), MEDIA_BOXES_VERSION );
			wp_enqueue_style( MEDIA_BOXES_PREFIX .'-font-awesome-.css', MEDIA_BOXES_URI . 'admin/includes/components/Font%20Awesome/css/font-awesome.min.css', array(), MEDIA_BOXES_VERSION );

		}
	}

/* ====================================================================== *
      REGISTER ADMIN JS
 * ====================================================================== */
	 
	public function enqueue_admin_js() {

		if ( ! isset( $this->menu_pages ) ) { return; }

		$screen = get_current_screen();
		
		if ( in_array( $screen->id, $this->menu_pages ) ) {
    		
			global $wp_version;
			
			/* Load color picker and media */
			wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker' );

			/* Load some jquery ui goodies */
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-draggable' );
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-ui-tooltip');
			wp_enqueue_script('jquery-ui-slider');
			wp_enqueue_script('jquery-ui-dialog');

			/* Load plugin JS for the admin area */
			wp_enqueue_script( MEDIA_BOXES_PREFIX .'-wp-color-picker-alpha', MEDIA_BOXES_URI . 'admin/includes/components/wp-color-picker-alpha/wp-color-picker-alpha.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
			wp_enqueue_script( MEDIA_BOXES_PREFIX .'-global-js', MEDIA_BOXES_URI . 'admin/includes/js/global.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
			wp_enqueue_script( MEDIA_BOXES_PREFIX .'-admin-js', MEDIA_BOXES_URI . 'admin/includes/js/admin.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
			wp_enqueue_script( MEDIA_BOXES_PREFIX .'-skin-editor-js', MEDIA_BOXES_URI . 'admin/includes/js/skin_editor.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
			
		}
		
	}

/* ====================================================================== *
      REGISTER PLUGIN CSS
 * ====================================================================== */
	 
	public function enqueue_css() {
		wp_enqueue_style( MEDIA_BOXES_PREFIX .'', MEDIA_BOXES_URI . 'plugin/css/mediaBoxes.css', array(), MEDIA_BOXES_VERSION );
		wp_enqueue_style( MEDIA_BOXES_PREFIX .'-font-awesome', MEDIA_BOXES_URI . 'plugin/components/Font%20Awesome/css/font-awesome.min.css', array(), MEDIA_BOXES_VERSION );
		wp_enqueue_style( MEDIA_BOXES_PREFIX .'-fancybox', MEDIA_BOXES_URI . 'plugin/components/Fancybox/jquery.fancybox.min.css', array(), MEDIA_BOXES_VERSION );
		wp_enqueue_style( MEDIA_BOXES_PREFIX .'-magnific-popup', MEDIA_BOXES_URI . 'plugin/components/Magnific%20Popup/magnific-popup.css', array(), MEDIA_BOXES_VERSION );
		wp_enqueue_style( MEDIA_BOXES_PREFIX .'-extra-style', MEDIA_BOXES_URI . 'plugin_extra/extra_style.css', array(), MEDIA_BOXES_VERSION );
	}

/* ====================================================================== *
      REGISTER PLUGIN JS
 * ====================================================================== */
	 
	public function enqueue_js() {
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-isotope-js', MEDIA_BOXES_URI . 'plugin/components/Isotope/jquery.isotope.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-images-loaded-js', MEDIA_BOXES_URI . 'plugin/components/imagesLoaded/jquery.imagesLoaded.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-transit-js', MEDIA_BOXES_URI . 'plugin/components/Transit/jquery.transit.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-easing-js', MEDIA_BOXES_URI . 'plugin/components/jQuery%20Easing/jquery.easing.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-waypoints-js', MEDIA_BOXES_URI . 'plugin/components/Waypoints/waypoints.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-visible-js', MEDIA_BOXES_URI . 'plugin/components/jQuery%20Visible/jquery.visible.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-fancybox-js', MEDIA_BOXES_URI . 'plugin/components/Fancybox/jquery.fancybox.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-modernizr-custom-js', MEDIA_BOXES_URI . 'plugin/components/Modernizr/modernizr.custom.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-magnific-popup-js', MEDIA_BOXES_URI . 'plugin/components/Magnific%20Popup/jquery.magnific-popup.min.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-media-boxes-dropdown-js', MEDIA_BOXES_URI . 'plugin/js/jquery.mediaBoxes.dropdown.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-media-boxes-js', MEDIA_BOXES_URI . 'plugin/js/jquery.mediaBoxes.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
		wp_enqueue_script( MEDIA_BOXES_PREFIX .'-media-boxes-js-init', MEDIA_BOXES_URI . 'plugin_extra/init.js', array( 'jquery' ), MEDIA_BOXES_VERSION );
	}

/* ====================================================================== *
      CREATE META BOX
 * ====================================================================== */	

    public function create_meta_box(){

    	add_action( 'admin_menu', array( $this, 'add_custom_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_custom_meta_box' ) );
		add_action( 'edit_attachment', array( $this, 'save_custom_meta_box' ) );

    }

    public function add_custom_meta_box() {

    	$args = array(
		   'public'   			=> true,
		   //'_builtin' 			=> true,
		);
		$output = 'objects'; // names or objects

		$post_types = get_post_types( $args, $output ); 
		foreach ($post_types as $post_type_key => $post_type) {
    		add_meta_box( MEDIA_BOXES_PREFIX.'-meta-boxes', 'Media Boxes Settings', array( $this, 'print_custom_meta_box' ), $post_type_key, 'normal', 'default');
    	}

	}

	public function print_custom_meta_box( $post ){

		$skins 			= get_option( MEDIA_BOXES_PREFIX . '_skins' );
		$skins_options 	= '';
		foreach ($skins as $key => $value) {
			$skins_options .= 	'<option value="'.esc_attr($value['uniqid']).'" '.(get_post_meta($post->ID, 'media_boxes_skin', true)==$value['uniqid']?'selected':'').'>
									'.$value['name'].'
								</option>';
		}

		$html 	= 	'';
		$html 	.= 	' 
						<div class="media_boxes_admin">
							
							<input type="hidden" name="'.MEDIA_BOXES_PREFIX.'_nonce" value="'.wp_create_nonce( plugin_basename( __FILE__ ) ).'" />
							<br>
							<h3>Thumbnail</h3>
							<p>
								<label>Thumbnail URL</label>
								<input name="media_boxes_thumbnail_url" type="text" value="'.get_post_meta($post->ID, 'media_boxes_thumbnail_url', true).'" />
								<span class="media_boxes_admin_help_text">If you specify something here it will replace the featured image<span>
							</p> 
							<p>
								<label>Thumbnail Ratio</label>
								<input style="width:50px;" name="media_boxes_thumbnail_ratio_width" type="text" value="'.get_post_meta($post->ID, 'media_boxes_thumbnail_ratio_width', true).'" />
								:
								<input style="width:50px;" name="media_boxes_thumbnail_ratio_height" type="text" value="'.get_post_meta($post->ID, 'media_boxes_thumbnail_ratio_height', true).'" />
								<span class="media_boxes_admin_help_text">Replace the default ratio specified in the admin section for this thumbnail<span>
							</p> 
							<p>
								<label>Overlay Effect</label>
								<select name="media_boxes_overlay_effect" title="The effect of the thumbnail overlay">
									<option value="default" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='default'?'selected':'') .'>Use the Default</option>
									<option value="fade" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='fade'?'selected':'') .'>Fade</option>
									<option value="push-up" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='push-up'?'selected':'') .'>Push Up</option>
									<option value="push-down" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='push-down'?'selected':'') .'>Push Down</option>
									<option value="push-up-100%" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='push-up-100%'?'selected':'') .'>Push Up 100%</option>
									<option value="push-down-100%" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='push-down-100%'?'selected':'') .'>Push Down 100%</option>
									<option value="reveal-top" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='reveal-top'?'selected':'') .'>Reveal Top</option>
									<option value="reveal-bottom" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='reveal-bottom'?'selected':'') .'>Reveal Bottom</option>
									<option value="reveal-top-100%" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='reveal-top-100%'?'selected':'') .'>Reveal Top 100%</option>
									<option value="reveal-bottom-100%" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='reveal-bottom-100%'?'selected':'') .'>Reveal Bottom 100%</option>
									<option value="direction-aware" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='direction-aware'?'selected':'') .'>Direction Aware</option>
									<option value="direction-aware-fade" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='direction-aware-fade'?'selected':'') .'>Direction Aware Fade</option>
									<option value="direction-right" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='direction-right'?'selected':'') .'>Direction Right</option>
									<option value="direction-left" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='direction-left'?'selected':'') .'>Direction Left</option>
									<option value="direction-top" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='direction-top'?'selected':'') .'>Direction Top</option>
									<option value="direction-bottom" '. (get_post_meta($post->ID, 'media_boxes_overlay_effect', true)=='direction-bottom'?'selected':'') .'>Direction Bottom</option>
								</select>
								<span class="media_boxes_admin_help_text">Replace the default overlay effect specified in the admin section for this thumbnail<span>
							</p> 
							<h3>Popup</h3>
							<p>
								<label>Popup Type</label>
								<select name="media_boxes_popup_type">
									<option value="image" '. (get_post_meta($post->ID, 'media_boxes_popup_type', true)=='image'?'selected':'') .'>Image</option>
									<option value="iframe" '. (get_post_meta($post->ID, 'media_boxes_popup_type', true)=='iframe'?'selected':'') .'>Iframe</option>
								</select>
								<span class="media_boxes_admin_help_text">Choose the type of popup, if need a youtube/vimeo video choose iframe</span>
							</p> 
							<p>
								<label>Popup URL</label>
								<input name="media_boxes_popup_url" type="text" value="'.get_post_meta($post->ID, 'media_boxes_popup_url', true).'" />
								<span class="media_boxes_admin_help_text">Here you can add an image URL or a youtube video URL<span>
							</p>
							<p>
								<label>Popup Title</label>
								<input name="media_boxes_popup_title" type="text" value="'.get_post_meta($post->ID, 'media_boxes_popup_title', true).'" />
								<span class="media_boxes_admin_help_text">Here you can add a the popup title (which will be shown below the image in the popup)<span>
							</p>
							<p>
								<label>Popup Thumbnail</label>
								<input name="media_boxes_popup_thumb" type="text" value="'.get_post_meta($post->ID, 'media_boxes_popup_thumb', true).'" />
								<span class="media_boxes_admin_help_text">Here you can add a popup thumbnail (which will be used by fancybox in the gallery grid at the right)<span>
							</p>
							<p>
								<label>Iframe video on thumbnail</label>
								<select name="media_boxes_iframe_on_thumbnail">
									<option value="no" '. (get_post_meta($post->ID, 'media_boxes_iframe_on_thumbnail', true)=='no'?'selected':'') .'>No</option>
									<option value="yes" '. (get_post_meta($post->ID, 'media_boxes_iframe_on_thumbnail', true)=='yes'?'selected':'') .'>Yes</option>
								</select>
								<span class="media_boxes_admin_help_text">Instead of loading the iframe on the popup, load it instead of the thumbnail</span>
							</p> 
							<h3>Post</h3>
							<p>
								<label>Post Link</label>
								<input name="media_boxes_post_link" type="text" value="'.get_post_meta($post->ID, 'media_boxes_post_link', true).'" />
								<span class="media_boxes_admin_help_text">This will replace the default post link<span>
							</p> 
							<p>
								<label>Post Title</label>
								<input name="media_boxes_post_title" type="text" value="'.get_post_meta($post->ID, 'media_boxes_post_title', true).'" />
								<span class="media_boxes_admin_help_text">This will replace the default post title<span>
							</p> 
							<p>
								<label>Post Content</label>
								<textarea name="media_boxes_post_content" style="width: 100%; display: block;" rows="5">'.get_post_meta($post->ID, 'media_boxes_post_content', true).'</textarea>
								<span class="media_boxes_admin_help_text">This will replace the default post content<span>
							</p> 
							<h3>Layout</h3>
							<p>
								<label>How many columns</label>
								<select name="media_boxes_how_many_columns">
									<option value="1" '. (get_post_meta($post->ID, 'media_boxes_how_many_columns', true)=='1'?'selected':'') .'>1</option>
									<option value="2" '. (get_post_meta($post->ID, 'media_boxes_how_many_columns', true)=='2'?'selected':'') .'>2</option>
									<option value="3" '. (get_post_meta($post->ID, 'media_boxes_how_many_columns', true)=='3'?'selected':'') .'>3</option>
									<option value="4" '. (get_post_meta($post->ID, 'media_boxes_how_many_columns', true)=='4'?'selected':'') .'>4</option>
									<option value="5" '. (get_post_meta($post->ID, 'media_boxes_how_many_columns', true)=='5'?'selected':'') .'>5</option>
									<option value="6" '. (get_post_meta($post->ID, 'media_boxes_how_many_columns', true)=='6'?'selected':'') .'>6</option>
								</select>
								<span class="media_boxes_admin_help_text">If you want this item/post to be 2 or 3 columns wide then you can define that here.</span>
							</p> 
							<p>
								<label>Skin</label>
								<select name="media_boxes_skin">
									<option value="use_default" '. (get_post_meta($post->ID, 'media_boxes_skin', true)=='1'?'use_default':'') .'>Use Default</option>
									'.$skins_options.'
								</select>
								<span class="media_boxes_admin_help_text">You can define an specific skin for this post/item.</span>
							</p> 
						</div>
					';

		echo $html;
	}

	public function save_custom_meta_box($post_id){
		if( $this->user_can_save_meta_box($post_id) ){

			$this->update_field($post_id, 'media_boxes_thumbnail_url', $_POST['media_boxes_thumbnail_url']);
			$this->update_field($post_id, 'media_boxes_thumbnail_ratio_width', $_POST['media_boxes_thumbnail_ratio_width']);	
			$this->update_field($post_id, 'media_boxes_thumbnail_ratio_height', $_POST['media_boxes_thumbnail_ratio_height']);	
			$this->update_field($post_id, 'media_boxes_overlay_effect', $_POST['media_boxes_overlay_effect']);	
			$this->update_field($post_id, 'media_boxes_popup_type', $_POST['media_boxes_popup_type']);
			$this->update_field($post_id, 'media_boxes_popup_url', $_POST['media_boxes_popup_url']);
			$this->update_field($post_id, 'media_boxes_popup_title', $_POST['media_boxes_popup_title']);
			$this->update_field($post_id, 'media_boxes_popup_thumb', $_POST['media_boxes_popup_thumb']);
			$this->update_field($post_id, 'media_boxes_iframe_on_thumbnail', $_POST['media_boxes_iframe_on_thumbnail']);
			$this->update_field($post_id, 'media_boxes_post_link', $_POST['media_boxes_post_link']);
			$this->update_field($post_id, 'media_boxes_post_title', $_POST['media_boxes_post_title']);
			$this->update_field($post_id, 'media_boxes_post_content', $_POST['media_boxes_post_content']);
			$this->update_field($post_id, 'media_boxes_how_many_columns', $_POST['media_boxes_how_many_columns']);
			$this->update_field($post_id, 'media_boxes_skin', $_POST['media_boxes_skin']);

		}
	}

	public function update_field($post_id, $key, $new_value){
		$old_value = get_post_meta( $post_id, $key, true );

		if($new_value != '' && $old_value != $new_value){
			update_post_meta($post_id, $key, $new_value);
		}else if($new_value == '' && $old_value){
			delete_post_meta($post_id, $key, $old_value);
		}
	}

	public function user_can_save_meta_box($post_id){
		$is_autosave 	= wp_is_post_autosave($post_id);
		$is_revision 	= wp_is_post_revision($post_id);
		$is_valid_nonce = ( isset($_POST[MEDIA_BOXES_PREFIX.'_nonce']) && wp_verify_nonce($_POST[MEDIA_BOXES_PREFIX.'_nonce'], plugin_basename(__FILE__)) );

		return !($is_autosave || $is_revision) && $is_valid_nonce;
	}

/* ====================================================================== *
      EXPORT OPTIONS
 * ====================================================================== */

    public function export_options(){
		if(isset($_POST['action']) && ($_POST['action'] == 'media_boxes_export_portfolios' || $_POST['action'] == 'media_boxes_export_skins')){

			/* ### GET DATA FROM DB ### */

			$items 				= array();
			$filename_prefix	= '';
			
			if($_POST['action'] == 'media_boxes_export_portfolios'){
				$items 				= get_option( MEDIA_BOXES_PREFIX . '_portfolios' );
				$filename_prefix	= 'portfolios';
			}else if($_POST['action'] == 'media_boxes_export_skins'){
				$items = get_option( MEDIA_BOXES_PREFIX . '_skins' );
				$filename_prefix	= 'skins';
			}

			$output = array();
			foreach ($items as $row) {
				if( isset($_POST['item_'.$row['uniqid']]) ){
					$output[] = $row;
				}
			}

			/* ### EXPORTING... ### */

			if(count($output) > 0){
				header( 'Content-Type: application/json; charset=utf-8' );
				header( 'Content-Disposition: attachment; filename=media-boxes-'.$filename_prefix.'- ' . date( 'm-d-Y' ) . '.json' );
				header( "Expires: 0" );
				 
				echo json_encode( $output );
				exit;
			}
		}
    }

/* ====================================================================== *
      IMPORT OPTIONS
 * ====================================================================== */    

	public function import_options(){
		if(isset($_POST['action']) && ($_POST['action'] == 'media_boxes_import_portfolios' || $_POST['action'] == 'media_boxes_import_skins')){

			/* Check Extension */

			$explode 	= explode( '.', $_FILES['import_file']['name'] );
			$extension 	= end( $explode );
			if( $extension != 'json' ) {
				wp_die( __( 'Please upload a valid .json file: '.$_FILES['import_file']['name'] ) );
			}

			/* Check import file */

			$import_file = $_FILES['import_file']['tmp_name'];
			if( empty( $import_file ) ) {
				wp_die( __( 'Please upload a file to import' ) );
			}

			/* Get json from import file */

			$json_file = (array) json_decode( file_get_contents( $import_file ), true );

			/* Current items */

			$current_items 		= array();
			if($_POST['action'] == 'media_boxes_import_portfolios'){
				$current_items 	= get_option( MEDIA_BOXES_PREFIX . '_portfolios' );
			}else if($_POST['action'] == 'media_boxes_import_skins'){
				$current_items 	= get_option( MEDIA_BOXES_PREFIX . '_skins' );
			}

			/* Save in DB */

			$new_items = $current_items; /* Make sure to put the current items that are already in the DB */

			foreach ($json_file as $row) {
				$new_uniqid 				= uniqid();
				$row['uniqid'] 				= $new_uniqid;

				if($_POST['action'] == 'media_boxes_import_portfolios' && isset($row['media_boxes_portfolio'])){
					$new_items[$new_uniqid] 	= $row;	
				}
				if($_POST['action'] == 'media_boxes_import_skins' && isset($row['media_boxes_skin'])){
					$new_items[$new_uniqid] 	= $row;		
				}
			}

			if($_POST['action'] == 'media_boxes_import_portfolios'){
				update_option ( MEDIA_BOXES_PREFIX . '_portfolios', $new_items );	
			}else if($_POST['action'] == 'media_boxes_import_skins'){
				update_option ( MEDIA_BOXES_PREFIX . '_skins', $new_items );	
			}
			
		}
	}	

}

