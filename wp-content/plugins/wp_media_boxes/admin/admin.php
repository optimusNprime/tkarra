<?php

/**
 *
 * Main menu page
 *
 * @package Media Boxes by castlecode
 * @author  castlecode
 * 
 */
	
	//delete_option( MEDIA_BOXES_PREFIX . '_portfolios' );

	/* ### GET PORTFOLIOS FROM DB ### */

	$portfolios = get_option( MEDIA_BOXES_PREFIX . '_portfolios' );
	
	if(is_array($portfolios) == false){
		$portfolios = array();
	}

	/* ### SUBPAGE, CURRENTLY THERE ARE 2 PAGES, THE MAIN ONE AND WHERE YOU ADD/EDIT A PORTFOLIO ### */
	
	$subpage  = "";
	if( isset($_GET['subpage']) ){
		$subpage = $_GET['subpage'];
	}else{
		$subpage = 'main';
	}

?>

<div class="media_boxes_options_page_loader"><span class="fa fa-cog fa-spin fa-3x fa-fw"></span></div>

<div class="media_boxes_options_page media_boxes_admin">

	<div class="media_boxes_admin_title">
		<img style="height:40px;" src="<?php echo MEDIA_BOXES_URI; ?>/admin/includes/images/media-boxes.png" alt="Media Boxes">
		&nbsp;
		Portfolio
	</div>

<?php

	/* ====================================================================== *
	      MAIN PAGE (THE PORTFOLIO'S LIST)
	 * ====================================================================== */

	if( $subpage == 'main' ){
?>

		<?php 

			/* CLONE A PORTFOLIO */

			if( isset($_POST['action']) && $_POST['action'] == 'clone' ){

				$new_portfolios 				= $portfolios; /* Make sure to put the portfolios that are already in the DB */
				$new_uniqid 					= uniqid();
				$new_portfolios[$new_uniqid] 	= $new_portfolios[$_POST['uniqid']];

				$new_portfolios[$new_uniqid]['uniqid'] 			= $new_uniqid;
				$new_portfolios[$new_uniqid]['name'] 			= $new_portfolios[$new_uniqid]['name'] . ' clone ' . $new_uniqid;
				$new_portfolios[$new_uniqid]['shortcode_id'] 	= $new_portfolios[$new_uniqid]['shortcode_id'] . '_clone_' . $new_uniqid;

				update_option ( MEDIA_BOXES_PREFIX . '_portfolios', $new_portfolios );

				/* Get portfolios from db again after we delete one */
				$portfolios = get_option( MEDIA_BOXES_PREFIX . '_portfolios' );

				echo '<div id="result" class="updated"><strong>The Portfolio has been successfully cloned</strong></div>';
			}
			
			/* DELTE A PORTFOLIO */

			else if( isset($_POST['action']) && $_POST['action'] == 'delete' ){

				$new_portfolios = $portfolios; /* grab all the portfolios from the DB */

				unset( $new_portfolios[$_POST['uniqid']] ); /* delete the chosen portfolio from the list */
				$subpage = 'main'; /* Send us back to the portfolio list */

				update_option ( MEDIA_BOXES_PREFIX . '_portfolios', $new_portfolios );
				
				/* Get portfolios from db again after we delete one */
				$portfolios = get_option( MEDIA_BOXES_PREFIX . '_portfolios' );

				echo '<div id="result" class="updated"><strong>The Portfolio has been successfully deleted</strong></div>';
			}
		?>
		
		<p>
			<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=new') ); ?>" class="button-primary blue">
				<span class="fa fa-plus"></span>&nbsp; Create a new portfolio
			</a>
		</p>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="form">
			<input name="action" type="hidden" />
			<input name="uniqid" type="hidden" />

			<table class="widefat media_boxes_table">
				<thead>
					<tr>
						<th>#</th>
						<th>Portfolio name</th>
						<th>Shortcode</th>
						<th>Actions</th>
						<th>Post type</th>
					</tr>
				</thead>

				<tbody>
					<?php if( count($portfolios) == 0 || empty($portfolios)){ ?>
						<tr>
							<td colspan="3">You don't have any portfolio yet, <a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=new') ); ?>">create a new one here!</a></td>
						</tr>
					<?php } ?>

					<?php $cont=0; ?>
					<?php foreach ($portfolios as $key => $value) { ?>
						<?php $cont++; ?>
						<tr>
							<td><?php echo $cont; ?></td>
							<td>
								<strong><?php echo $value['name']; ?></strong>	
							</td>
							<td>
								[media_boxes id="<?php echo $value['shortcode_id']; ?>"]
							</td>
							<td>
								<div>
									<span>
										<a class="button-primary blue" href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=edit&uniqid='.$value['uniqid']) ); ?>" title="Edit the options of this portfolio">
											<span class="fa fa-pencil"></span>&nbsp;
											Edit
										</a>
									</span>
									<span>
									 	<a class="button-primary green" href="javascript:clone_portfolio('<?php echo $value['uniqid']; ?>');" title="Clone this portfolio">
									 		<span class="fa fa-clone"></span>&nbsp;
									 		Clone
									 	</a>
									 </span>
									 <span>
									 	<a class="button-primary red" href="javascript:delete_portfolio('<?php echo $value['uniqid']; ?>');" title="Delete this portfolio">
									 		<span class="fa fa-trash-o"></span>&nbsp;
									 		Delete
									 	</a>
									 </span>
								 </div>
							</td>
							<td>
								<div class="description" style="display: block;">
									<?php 
										$obj = get_post_type_object( $value['post_type'] );
										if( empty($obj) && $value['post_type'] != 'custom-media-gallery' ){
											echo '<div class="post_type_error">The post type with the key "'.$value['post_type'].'" is not longer available <br> please change it for something else.</div>';
										}else{
											if( $value['post_type'] == 'custom-media-gallery' ){
												echo "Custom Media Gallery";
											}else{
												echo $obj->labels->name;
											}
										}
									?>
								</div>
							</td>
						</tr>
					<?php } ?>				
				</tbody>
			</table>
		</form>

		<script>
			function clone_portfolio(uniqid){
				if(confirm("Are you sure?")){
					document.form.action.value = 'clone';
					document.form.uniqid.value = uniqid;
					document.form.submit();
				}
			}
			function delete_portfolio(uniqid){
				if(confirm("Are you sure? this action can't be undone!")){
					document.form.action.value = 'delete';
					document.form.uniqid.value = uniqid;
					document.form.submit();
				}
			}
		</script>
<?php

	/* ====================================================================== *
	      ADD/EDIT A PORTFOLIO
	 * ====================================================================== */

	}else if( $subpage == 'new' || $subpage == 'edit' ){

		/* ### THE UNIQIQ ID ### */
		$uniqid = 'none'; /* this is used for new portfolios */
		if( isset($_GET['uniqid']) ){
			$uniqid = $_GET['uniqid']; /* this is used to edit an existing portfolio */
		}

		$load_more_css = "
@media_boxes .media-boxes-load-more-button{

    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    cursor: pointer;
    width: 150px;
    text-align: center;
    color: white;
    background-color: #333333;
    font-size: 14px !important;
    height: 15px;
    padding: 10px 15px 10px 15px;
    margin: 0px auto;
    line-height: 15px;

    -webkit-box-shadow: 0 1px 1px rgba(34,25,25,0.2);
       -moz-box-shadow: 0 1px 1px rgba(34,25,25,0.2);
         -o-box-shadow: 0 1px 1px rgba(34,25,25,0.2);
        -ms-box-shadow: 0 1px 1px rgba(34,25,25,0.2);
            box-shadow: 0 1px 1px rgba(34,25,25,0.2);

    -webkit-box-sizing: content-box !important;
       -moz-box-sizing: content-box !important;
            box-sizing: content-box !important;

}

@media_boxes .media-boxes-loading{

}

@media_boxes .media-boxes-no-more-entries{

    filter: alpha(opacity=20);
    opacity: 0.2;

}
";	

		/* ### THE DEFAULT STUFF ### */
		
		$form_version 					= MEDIA_BOXES_VERSION;
		$form_name 						= '';
		$form_shortcode_id 				= '';
		$form_post_type					= '';
		$form_custom_media_gallery_cat 	= array();
		$form_custom_media_gallery      = array();
		$form_post_categories  			= array();
		$form_number_posts  			= '';
		$form_order_by      			= '';
		$form_order 					= '';
		$form_exclude_current_post		= null;
		$form_boxes_to_load_start  		= '8';
		$form_boxes_to_load  			= '4';
		$form_min_boxes_per_filter		= '0';
		$form_lazy_load  				= 'on';
		$form_load_more_word			= 'Load More';
		$form_loading_word  			= 'Loading...';
		$form_no_more_entries_word		= 'No More Entries';
		$form_load_more_button_css		= $load_more_css;
		$form_columns					= '4';
		$form_horizontal_space 			= '15';
		$form_vertical_space 			= '15';
		$form_enable_responsivity 		= 'on';
		$form_enable_spacing 			= null;
		$form_maximum_width_first 		= '1440';
		$form_resolutions 				= array();
		$form_thumbnail_size 			= '';
		$form_thumbnail_width			= '';
		$form_thumbnail_height			= '';
		$form_thumbnail_link			= '';
		$form_thumbnail_url				= '';
		$form_show_loading_progress		= null;
		$form_preload_all_thumbnails	= null;
		//$form_thumbnail_overlay			= 'on'; // this has been moved to the skin editor page via CSS
		$form_overlay_effect			= 'fade';
		$form_overlay_always_visible 	= null;
		$form_overlay_speed				= '200';
		$form_overlay_easing			= 'default';
		$form_popup_size	 			= '';
		$form_show_only_loaded_boxes	= null;
		$form_consider_filtering		= 'on';
		$form_deep_linking_popup		= 'on';
		$form_popup						= 'fancybox';
		$form_popup_title 				= 'none';
		$form_fancyb_loop 				= null;
		$form_fancyb_margin 			= '44, 0';
		$form_fancyb_keyboard 			= 'on';
		$form_fancyb_arrows 			= 'on';
		$form_fancyb_infobar 			= null;
		$form_fancyb_toolbar 			= 'on';
		$form_fancyb_btn_slideshow 		= 'on';
		$form_fancyb_btn_fullscreen 	= 'on';
		$form_fancyb_btn_thumbs 		= 'on';
		$form_fancyb_btn_close 			= 'on';
		$form_fancyb_idle_time 			= '3';
		$form_fancyb_protect 			= null;
		$form_fancyb_animation_effect 	= 'zoom';
		$form_fancyb_animation_duration = '330';
		$form_fancyb_transition_effect 	= 'fade';
		$form_fancyb_transition_duration= '330';
		$form_fancyb_slideshow_autostart= null;
		$form_fancyb_fullScreen_autostart= null;
		$form_fancyb_thumbs_autostart	= null;
		$form_fancyb_touch				= 'on';
		$form_mp_align_top				= null;
		$form_mp_enable_gallery 		= 'on';
		$form_mp_preload_before 		= '0';
		$form_mp_preload_after 			= '3';
		$form_title_max_length			= '';
		$form_content_delimiter 		= '<!--mb_delimiter-->';
		$form_excerpt_max_words			= '30';
		$form_excerpt_string_end		= '...';
		$form_excerpt_remove_shortcodes = 'on';
		$form_excerpt_strip_tags		= 'on';
		$form_excerpt_except_this_tags 	= '';
		$form_deep_linking_filter		= null;
		$form_multiple_filter_logic		= 'AND';
		$form_drop_down_event			= 'hover';
		$form_filters 					= array();
		$form_default_sorting 			= '';
		$form_sortings 					= array();
		$form_default_sorting_order 	= 'ascending';
		$form_sort_by_text 				= 'Sort by';
		$form_deep_linking_search		= null;
		$form_search_default_text 		= 'Search';
		$form_skin 						= '';
		$form_drop_zone_1 				= array();
		$form_drop_zone_2 				= array();
		$form_css 						= '';
		//$form_show_title 				= 'on';
		//$form_excerpt_source			= 'post_content';
		
		/* ### THE SAVED STUFF ### */
		if( $subpage == 'edit' ){
			$edit_portfolio 				= $portfolios[$uniqid];

			//print_r($edit_portfolio);
			
			$form_version 					= isset( $edit_portfolio['version'] ) ? $edit_portfolio['version'] : '1';
			$form_name 						= $edit_portfolio['name'];
			$form_shortcode_id				= $edit_portfolio['shortcode_id'];
			$form_post_type     			= $edit_portfolio['post_type'];
			$form_custom_media_gallery_cat 	= isset( $edit_portfolio['custom-media-gallery-items']['categories'] ) ? $edit_portfolio['custom-media-gallery-items']['categories'] : array();
			$form_custom_media_gallery		= isset( $edit_portfolio['custom-media-gallery-items'] ) ? $edit_portfolio['custom-media-gallery-items']['attachment'] : array();
			$form_post_categories           = isset( $edit_portfolio['post_categories'] ) ? $edit_portfolio['post_categories'] : array();
			$form_number_posts  			= $edit_portfolio['number_posts'];
			$form_order_by  				= $edit_portfolio['order_by'];
			$form_order  					= $edit_portfolio['order'];
			$form_exclude_current_post 		= isset( $edit_portfolio['exclude_current_post'] ) ? $edit_portfolio['exclude_current_post'] : null;
			$form_boxes_to_load_start  		= $edit_portfolio['boxes_to_load_start'];
			$form_boxes_to_load  			= $edit_portfolio['boxes_to_load'];
			$form_min_boxes_per_filter		= $edit_portfolio['min_boxes_per_filter'];
			$form_lazy_load  				= isset( $edit_portfolio['lazy_load'] ) ? $edit_portfolio['lazy_load'] : null;
			$form_load_more_word			= $edit_portfolio['load_more_word'];
			$form_loading_word 				= $edit_portfolio['loading_word'];
			$form_no_more_entries_word		= $edit_portfolio['no_more_entries_word'];
			$form_load_more_button_css 		= $edit_portfolio['load_more_button_css'];
			$form_columns 					= $edit_portfolio['columns'];
			$form_horizontal_space 			= $edit_portfolio['horizontal_space'];
			$form_vertical_space			= $edit_portfolio['vertical_space'];
			$form_enable_responsivity		= isset( $edit_portfolio['enable_responsivity'] ) ? $edit_portfolio['enable_responsivity'] : null;
			$form_enable_spacing			= isset( $edit_portfolio['enable_spacing'] ) ? $edit_portfolio['enable_spacing'] : null;
			$form_resolutions				= isset( $edit_portfolio['resolutions'] ) ? $edit_portfolio['resolutions'] : array();
			$form_thumbnail_size			= $edit_portfolio['thumbnail_size'];
			$form_thumbnail_width			= $edit_portfolio['thumbnail_width'];
			$form_thumbnail_height			= $edit_portfolio['thumbnail_height'];
			$form_thumbnail_link			= $edit_portfolio['thumbnail_link'];
			$form_thumbnail_url				= $edit_portfolio['thumbnail_url'];
			$form_show_loading_progress		= isset( $edit_portfolio['show_loading_progress'] ) ? $edit_portfolio['show_loading_progress'] : null;
			$form_preload_all_thumbnails	= isset( $edit_portfolio['preload_all_thumbnails'] ) ? $edit_portfolio['preload_all_thumbnails'] : null;
			//$form_thumbnail_overlay			= isset( $edit_portfolio['thumbnail_overlay'] ) ? $edit_portfolio['thumbnail_overlay'] : null;
			$form_overlay_effect			= $edit_portfolio['overlay_effect'];
			$form_overlay_always_visible 	= isset( $edit_portfolio['overlay_always_visible'] ) ? $edit_portfolio['overlay_always_visible'] : null;
			$form_overlay_speed				= $edit_portfolio['overlay_speed'];
			$form_overlay_easing			= $edit_portfolio['overlay_easing'];
			$form_popup_size				= $edit_portfolio['popup_size'];
			$form_show_only_loaded_boxes	= isset( $edit_portfolio['show_only_loaded_boxes'] ) ? $edit_portfolio['show_only_loaded_boxes'] : null;
			$form_consider_filtering		= isset( $edit_portfolio['consider_filtering'] ) ? $edit_portfolio['consider_filtering'] : null;
			$form_deep_linking_popup 		= isset( $edit_portfolio['deep_linking_popup'] ) ? $edit_portfolio['deep_linking_popup'] : null;
			$form_popup 					= $edit_portfolio['popup'];
			$form_popup_title 				= isset( $edit_portfolio['popup_title'] ) ? $edit_portfolio['popup_title'] : 'none';
			$form_fancyb_loop 				= isset( $edit_portfolio['fancyb_loop'] ) ? $edit_portfolio['fancyb_loop'] : null;
			$form_fancyb_margin 			= $edit_portfolio['fancyb_margin'];
			$form_fancyb_keyboard 			= isset( $edit_portfolio['fancyb_keyboard'] ) ? $edit_portfolio['fancyb_keyboard'] : null;
			$form_fancyb_arrows 			= isset( $edit_portfolio['fancyb_arrows'] ) ? $edit_portfolio['fancyb_arrows'] : null;
			$form_fancyb_infobar 			= isset( $edit_portfolio['fancyb_infobar'] ) ? $edit_portfolio['fancyb_infobar'] : null;
			$form_fancyb_toolbar 			= isset( $edit_portfolio['fancyb_toolbar'] ) ? $edit_portfolio['fancyb_toolbar'] : null;
			$form_fancyb_btn_slideshow 		= isset( $edit_portfolio['fancyb_btn_slideshow'] ) ? $edit_portfolio['fancyb_btn_slideshow'] : null;
			$form_fancyb_btn_fullscreen 	= isset( $edit_portfolio['fancyb_btn_fullscreen'] ) ? $edit_portfolio['fancyb_btn_fullscreen'] : null;
			$form_fancyb_btn_thumbs 		= isset( $edit_portfolio['fancyb_btn_thumbs'] ) ? $edit_portfolio['fancyb_btn_thumbs'] : null;
			$form_fancyb_btn_close 			= isset( $edit_portfolio['fancyb_btn_close'] ) ? $edit_portfolio['fancyb_btn_close'] : null;
			$form_fancyb_idle_time 			= $edit_portfolio['fancyb_idle_time'];
			$form_fancyb_protect 			= isset( $edit_portfolio['fancyb_protect'] ) ? $edit_portfolio['fancyb_protect'] : null;
			$form_fancyb_animation_effect 	= $edit_portfolio['fancyb_animation_effect'];
			$form_fancyb_animation_duration = $edit_portfolio['fancyb_animation_duration'];
			$form_fancyb_transition_effect 	= $edit_portfolio['fancyb_transition_effect'];
			$form_fancyb_transition_duration= $edit_portfolio['fancyb_transition_duration'];
			$form_fancyb_slideshow_autostart= isset( $edit_portfolio['fancyb_slideshow_autostart'] ) ? $edit_portfolio['fancyb_slideshow_autostart'] : null;
			$form_fancyb_fullScreen_autostart= isset( $edit_portfolio['fancyb_fullScreen_autostart'] ) ? $edit_portfolio['fancyb_fullScreen_autostart'] : null;
			$form_fancyb_thumbs_autostart 	= isset( $edit_portfolio['fancyb_thumbs_autostart'] ) ? $edit_portfolio['fancyb_thumbs_autostart'] : null;
			$form_fancyb_touch 				= isset( $edit_portfolio['fancyb_touch'] ) ? $edit_portfolio['fancyb_touch'] : null;
			$form_mp_align_top				= isset( $edit_portfolio['mp_align_top'] ) ? $edit_portfolio['mp_align_top'] : null;
			$form_mp_enable_gallery			= isset( $edit_portfolio['mp_enable_gallery'] ) ? $edit_portfolio['mp_enable_gallery'] : null;
			$form_mp_preload_before			= $edit_portfolio['mp_preload_before'];
			$form_mp_preload_after			= $edit_portfolio['mp_preload_after'];
			$form_title_max_length			= $edit_portfolio['title_max_length'];
			$form_content_delimiter 		= $edit_portfolio['content_delimiter'];
			$form_excerpt_max_words			= $edit_portfolio['excerpt_max_words'];
			$form_excerpt_string_end		= $edit_portfolio['excerpt_string_end'];
			$form_excerpt_remove_shortcodes = $edit_portfolio['excerpt_remove_shortcodes'];
			$form_excerpt_strip_tags		= $edit_portfolio['excerpt_strip_tags'];
			$form_excerpt_except_this_tags  = $edit_portfolio['excerpt_except_this_tags'];
			$form_deep_linking_filter 		= isset( $edit_portfolio['deep_linking_filter'] ) ? $edit_portfolio['deep_linking_filter'] : null;
			$form_multiple_filter_logic 	= $edit_portfolio['multiple_filter_logic'];
			$form_drop_down_event 			= $edit_portfolio['drop_down_event'];
			$form_filters					= isset( $edit_portfolio['filters'] ) ? $edit_portfolio['filters'] : array();
			$form_default_sorting 			= $edit_portfolio['default_sorting'];
			$form_sortings 					= isset( $edit_portfolio['sortings'] ) ? $edit_portfolio['sortings'] : array();
			$form_default_sorting_order 	= $edit_portfolio['default_sorting_order'];
			$form_sort_by_text 				= $edit_portfolio['sort_by_text'];
			$form_deep_linking_search 		= isset( $edit_portfolio['deep_linking_search'] ) ? $edit_portfolio['deep_linking_search'] : null;
			$form_search_default_text 		= $edit_portfolio['search_default_text'];
			$form_skin 						= isset($edit_portfolio['skin']) ? $edit_portfolio['skin'] : array();
			$form_drop_zone_1 				= isset($edit_portfolio['drop_zone_1']) ? $edit_portfolio['drop_zone_1'] : array();
			$form_drop_zone_2 				= isset($edit_portfolio['drop_zone_2']) ? $edit_portfolio['drop_zone_2'] : array();
			$form_css 						= $edit_portfolio['css'];
			//$form_show_title				= $edit_portfolio['show_title'];
			//$form_excerpt_source			= $edit_portfolio['excerpt_source'];

		/* FIXES FOR DIFFERENT VERSIONS */

			// Fixes for v1
			if($form_version == '1'){

				// Fix custom media gallery
				foreach ($form_custom_media_gallery as $key_gallery => $value_gallery) {
					$img = $value_gallery;

					$form_custom_media_gallery[$key_gallery] 		= array();
					$form_custom_media_gallery[$key_gallery]['img'] = $img;
				}

				$form_version = MEDIA_BOXES_VERSION;

			}else if($form_version == '1.1'){
				$form_version = MEDIA_BOXES_VERSION; // no fixes for v1.1, just change the version				
			}

		}

		$order_by_items = array(
			array("id" => "date", 			"description" => "Date"),
			array("id" => "author", 		"description" => "Author"),
			array("id" => "ID", 			"description" => "ID"),
			array("id" => "title", 			"description" => "Title"),
			array("id" => "name", 			"description" => "Name"),
			array("id" => "modified", 		"description" => "Modified date"),
			array("id" => "comment_count", 	"description" => "Number of comment"),
			array("id" => "rand", 			"description" => "Random")
		);
?>	

		<form method="post" class="media_boxes_options_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			
			<input name="action" type="hidden" value="media_boxes_save_admin_options">
			<input name="uniqid" type="hidden" value="<?php echo $uniqid; ?>" /> <!-- used when you are going to edit a portfolio -->
			<input name="version" type="hidden" value="<?php echo $form_version; ?>">
			<input name="media_boxes_portfolio" value="yes" type="hidden">

			<div class="media_boxes_tabs">
			  	<ul>
			    	<li><a href="#tabs-1"><span class="fa fa-cog"></span> &nbsp; Shortcode</a></li>
				    <li><a href="#tabs-2"><span class="fa fa-folder"></span> &nbsp; Source</a></li>
				    <li><a href="#tabs-3"><span class="fa fa-th-large"></span> &nbsp; Grid </a></li>
				    <li><a href="#tabs-4"><span class="fa fa-picture-o"></span> &nbsp; Thumbnail</a></li>
				    <li><a href="#tabs-5"><span class="fa fa-arrows-alt"></span> &nbsp; Popup</a></li>
				    <li><a href="#tabs-6"><span class="fa fa-file-text"></span> &nbsp; Content</a></li>
				    <li><a href="#tabs-7"><span class="fa fa-filter"></span> &nbsp; Filter-Search-Sort</a></li>
				    <li><a href="#tabs-8"><span class="fa fa-object-group"></span> &nbsp; Layout</a></li>
				    <li><a href="#tabs-9"><span class="fa fa-css3"></span> &nbsp; CSS</a></li>
			  	</ul>
			  	<div id="tabs-1">

			  		<!-- ====================================================================== --
					      	SHORTCODE
					 !-- ====================================================================== -->
					
					<p>
						<label for="">Portfolio name</label>
						<input name="name" type="text" value="<?php echo esc_attr($form_name); ?>" title="This is used only in the portfolio settings, just for identification" />
					</p>
					
					<p>
						<label for="">Shortcode ID</label>
						<input class="shortcode_id" name="shortcode_id" type="text" value="<?php echo esc_attr($form_shortcode_id); ?>" autocomplete="off" title="ID used for your shortcode.  It must be unique for each portfolio and  you can only use lowercase letters, numbers and underscores" />
					</p>

					<p>
						<label for="">Shortcode</label>
						<input style="width: 300px;" class="shortcode" name="shortcode" type="text" value='[media_boxes id="lala"]' readonly title="Copy and paste this shortcode into your pages or posts" />
					</p>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-2">
			    
					<!-- ====================================================================== --
					      SOURCE
					 !-- ====================================================================== -->

					<p>
						<label for="">Post type</label>
						<select name="post_type" class="post-types" title="Select a post type or custom post type for the portfolio">
							<optgroup label="Post types"></optgroup>
							<?php 

								$all_post_types = array();

								/*
								 *	Default post types
								 */
								$args = array(
								   'public'   			=> true,
								   //'capability_type' 	=> 'post',
								   '_builtin' 			=> true,
								);
								$output = 'objects'; // names or objects

								$post_types = get_post_types( $args, $output ); 
								foreach ($post_types as $key => $post_type) {
									$all_post_types[] = $key;
							?>
									<option value="<?php echo esc_attr($key); ?>" <?php if($form_post_type==$key){echo "selected";} ?> ><?php echo $post_type->labels->name; ?></option>
							<?php 
								}	

								if( empty( $post_types ) ){
							?>
									<option value="">- No post types found -</option>
							<?php
								}
							?>
	
							<optgroup label="Custom post types"></optgroup>
							<?php
								/*
								 *	Custom post types
								 */
								$args = array(
								   'public'   			=> true,  
								   '_builtin' 			=> false,
								);
								$output = 'objects'; // names or objects

								$post_types = get_post_types( $args, $output ); 
								foreach ($post_types as $key => $post_type) {
									$all_post_types[] = $key;
							?>
									<option value="<?php echo esc_attr($key); ?>" <?php if($form_post_type==$key){echo "selected";} ?> ><?php echo $post_type->labels->name; ?></option>	
							<?php
								}

								$all_post_types[] = 'custom-media-gallery';
							?>
								<option value="custom-media-gallery" <?php if($form_post_type=='custom-media-gallery'){echo "selected";} ?>>Custom Media Gallery</option>
							
						</select>
					</p>
				
					<!-- -------------------- CUSTOM MEDIA GALLERY -------------------- -->

					<div class="custom-media-gallery">

						<div class="section_title">
							<span title="Configure your media gallery">
								Custom media gallery
							</span>
						</div>

						<div class="title">Categories</div>

						<p>
							<input type="text" style="width: 150px;"> <input type="button" class="button-primary button-sm blue custom-new-category-button" value="Add New Category"/>
						</p>

						<div class="custom-gallery-categories">
							<?php 
								if(!empty( $form_custom_media_gallery_cat)){
									foreach ( $form_custom_media_gallery_cat as $key => $item ){
							?>
										<div class="custom-gallery-category">
											<input type="hidden" name="custom-media-gallery-items[categories][<?php echo $item['id']; ?>][id]" class="category_id" value="<?php echo $item['id']; ?>" />
											<input type="hidden" name="custom-media-gallery-items[categories][<?php echo $item['id']; ?>][category]" class="category" value="<?php echo $item['category']; ?>" />

											<span class="fa fa-trash"></span>&nbsp; <?php echo $item['category']; ?>
										</div>
							<?php 
									}
								}
							?>
						</div>

						<hr>

						<div class="custom-gallery-buttons">
							<!--
								<input type="button" class="button-primary custom-edit-button" value="Edit" style="display:none;" />
								<input type="button" class="button-primary red custom-remove-button" value="Remove" style="display:none;" />
							-->

							<input type="button" class="button-primary blue custom-add-button" value="Add Images"/>
							<input type="button" class="button-primary red custom-remove-all-button" value="Remove All Images" />
						</div>	
						<div class="custom-gallery-container">

							<?php 
								if(!empty( $form_custom_media_gallery)){
									foreach ( $form_custom_media_gallery as $key => $item ){
										$post_exist = get_post_type ( $key );
										if ( $post_exist ){
							?>
											<div class="custom-gallery-item" data-id="<?php echo esc_attr( $key ); ?>">
												<input type="hidden" name="custom-media-gallery-items[attachment][<?php echo esc_attr( $key ); ?>][img]" value="<?php echo esc_attr( $item['img'] ); ?>" />
												
												<div class="custom-gallery-item-categories">
													<?php 
														if(!empty( $item['categories'])){
															foreach ( $item['categories'] as $key2 => $item2 ){
													?>
																<input 
																	type="hidden" 
																	name="custom-media-gallery-items[attachment][<?php echo esc_attr( $key ); ?>][categories][<?php echo $item2; ?>]" 
																	class="custom-gallery-item-category"
																	value="<?php echo esc_attr( $item2 ); ?>" 
																/>
													<?php 
															}
														}
													?>
												</div>

												<img src="<?php echo esc_attr( $item['img'] ); ?>">

												<div class="custom-gallery-item-buttons">
													<div class="custom-gallery-item-remove"><span class="fa fa-trash"></span></div>
													<div class="custom-gallery-item-add-category"><span class="fa fa-filter"></span></div>
													<div class="custom-gallery-item-edit"><span class="fa fa-pencil"></span></div>
												</div>	
											</div>
							<?php 
										}
									} 
								}
							?>
							<div class="custom-gallery-empty custom-gallery-item" <?php if ( !empty( $form_custom_media_gallery ) ){ echo "style='display:none;';"; }?>>Add Images!</div>

						</div>

						<!-- FILTERS ON CUSTOM MEDIA GALLERY ITEMS -->

						<div class="gallery-item-categories-popup">
							
							<br>

							<div class="gallery-item-categories"></div>
								
							<br>
							<br>
								
							<div class="form-controls">
								<button class="button-primary gallery-item-categories-save"><span class="fa fa-check"></span> &nbsp;OK</button>
							</div>

						</div>
	
					</div>

					<!-- -------------------- QUERY SETTINGS -------------------- -->

					<div class="query-options">

						<?php 

							$default_post_categories = array();

						?>

						<?php foreach ($all_post_types as $post_type) { ?>

							<div class="post_categories post-category-<?php echo $post_type; ?>">

								<?php 
									$hide_pages_categories = false;

									if($post_type == 'page'){
										$hide_pages_categories 	= true;
										
										if(count(get_object_taxonomies( $post_type )) > 0){
											$hide_pages_categories = false;
										}
									}

									if($hide_pages_categories || $post_type == 'attachment' || $post_type == 'custom-media-gallery'){
										echo " <select class='select_post_categories' name='post_categories[]' style='display:none;'></select> ";
										// Ignore pages, attachments (media gallery) and custom media gallery
									}else{
								?>
										<p>
											<label for="" style="float:left;">
												Post categories 
												<br>
												<small>Multiple selection allowed</small>
												<br>
												<small>Using the CTRL or CMD Key</small>
											</label>

											<a class="select_all_select_post_categories" href="">Select All</a> 
											<a class="clear_all_select_post_categories" href="">Clear All</a>
											<br>
											<select class="select_post_categories" name="post_categories[]" multiple="multiple" style="min-height: 150px;">
												<?php 
													$taxonomies = get_object_taxonomies( $post_type );

						   							foreach ($taxonomies as $key => $taxonomy) {
						   								$taxonomy_info 	= get_taxonomy( $taxonomy );
						   								$terms 			= get_terms( $taxonomy );

						   								if(count($terms)<=0){continue;}
						   						?>
						   								<option value="" disabled="disabled">---- <?php echo $taxonomy_info->labels->name; ?> ----</option>
						   								<?php 
						   									foreach ($terms as $key => $term) { 

						   										$term_id 		= $term->term_id;
						   										$term_name 		= $term->name." (".$term->count." item)";

						   										if($post_type == $form_post_type && in_array($taxonomy."||".$term_id, $form_post_categories)){
						   											$default_post_categories[$taxonomy."||".$term_id] = $term_name;
						   										}
						   								?>
																	<option value="<?php echo esc_attr($taxonomy."||".$term_id); ?>" <?php if(in_array($taxonomy."||".$term_id, $form_post_categories) || $post_type != $form_post_type){echo "selected";} ?>> <?php echo $term_name; ?> </option>
						   								<?php 
						   									} 
						   								?>
						   						<?php 		
						   							}
												?>
											</select>
											<!--<i class="fa fa-remove clear_select_post_categories"></i>-->
										</p>
								<?php 
									}
								?>
							</div>

						<?php }?>
						
						<p>
							<label for="">Number of posts</label>
							<input name="number_posts" type="text" value="<?php echo esc_attr($form_number_posts); ?>" title="Number of posts to be shown. Leave empty if you wouldn't like to limit number of posts" />
						</p>

						<p>
							<label for="">Order by</label>
							<select name="order_by" title="Set the order of the posts">
								<?php foreach ($order_by_items as $row) { ?>
									<option value="<?php echo esc_attr($row['id']); ?>" <?php if($row['id']==$form_order_by){echo "selected";} ?>><?php echo $row['description']; ?></option>
								<?php } ?>
							</select>
						</p>
							
						<p>
							<label for="">Order</label>
							<select name="order" title='Set the ascending or descending order of the "order by" option'>
								<option value="ASC" <?php if($form_order=='ASC'){echo "selected";} ?> >Ascending</option>
								<option value="DESC" <?php if($form_order=='DESC'){echo "selected";} ?> >Descending</option>
							</select>
						</p>
							
						<p>
							<label for="">Exclude current post</label>
							<input type="checkbox" name="exclude_current_post" <?php echo isset( $form_exclude_current_post ) ? 'value="1" checked="checked"' : ''; ?> title="If you would like to exclude the current post where you place the shortcode" />
						</p>
					
					</div>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-3">
			    
					<!-- ====================================================================== --
					    	GRID 
					 !-- ====================================================================== -->
					
					
					<!-- -------------------- Loading options -------------------- -->
					
					<div class="section_title">
						<span title="Configure the different loading settings">
							Loading options
						</span>
					</div>

					<p>
						<label for="">Number of boxes to load at the beginning</label>
						<span class="slider" data-max="20" data-min="0"></span>
						<input name="boxes_to_load_start" type="text" value="<?php echo esc_attr($form_boxes_to_load_start); ?>" title="The number of boxes to load when the portafolio loads at the beginning" />
					</p>

					<p>
						<label for="">Number of boxes to load</label>
						<span class="slider" data-max="20" data-min="0"></span>
						<input name="boxes_to_load" type="text" value="<?php echo esc_attr($form_boxes_to_load); ?>" title='The number of boxes to load when you click the load more button or when the "lazzy load" mode is triggered' />
					</p>
					

					<p>
						<label for="">Minimum of boxes per filter</label>
						<span class="slider" data-max="20" data-min="0"></span>
						<input name="min_boxes_per_filter" type="text" value="<?php echo esc_attr($form_min_boxes_per_filter); ?>" title="The minimum of boxes per filter. If the number of boxes in a filter is less than the number specified here it will try to load more boxes that match that filter" />
					</p>

					<p>
						<label for="">Lazy load</label>
						<input type="checkbox" name="lazy_load" <?php echo isset( $form_lazy_load ) ? 'value="1" checked="checked"' : ''; ?> title="If you would like to activate the lazy load feature, so when you scroll down and reach the bottom of the portfolio it will go ahead and load more boxes automatically" />
					</p>

					<p>
						<label for="">Load more word</label>
						<input type="text" name="load_more_word" value="<?php echo esc_attr($form_load_more_word); ?>" title="The load more word" />
					</p>
					
					<p>
						<label for="">Loading word</label>
						<input type="text" name="loading_word" value="<?php echo esc_attr($form_loading_word); ?>" title="The loading word" />
					</p>

					<p>
						<label for="">No more entries word</label>
						<input type="text" name="no_more_entries_word" value="<?php echo esc_attr($form_no_more_entries_word); ?>" title="The no more entries word" />
					</p>
					
					<p>
						<label for="">Load more button style</label>
						<a class="button-primary blue show_load_more_button_css" title="Show or Hide the CSS so you can modify it"><span class="fa fa-code"></span>&nbsp; Show CSS code</a>
						<textarea class="load_more_button_css" name="load_more_button_css" id="load_more_button_css" style="width:100%;height:300px;"><?php echo stripslashes( $form_load_more_button_css ); ?></textarea>
					</p>

					<!-- -------------------- Loading options -------------------- -->
					
					<div class="section_title">
						<span title="Configure the different loading settings">
							GRID LAYOUT
						</span>
					</div>

					<p>
						<label for="">Columns</label>
						<select name="columns" title="How many boxes you would like in a row">
							<?php for ($i=1; $i <= 10; $i++) {  ?>
								<option value="<?php echo esc_attr($i); ?>" <?php if($form_columns==$i){echo "selected";} ?> ><?php echo $i; ?> column per row</option>
							<?php } ?>
						</select>
					</p>

					<p>
						<label for="">Horizontal space</label>
						<span class="slider" data-max="99" data-min="0"></span>
						<input name="horizontal_space" type="text" value="<?php echo esc_attr($form_horizontal_space); ?>" title="Horizontal space between boxes (pixels)" />
					</p>

					<p>
						<label for="">Vertical space</label>
						<span class="slider" data-max="99" data-min="0"></span>
						<input name="vertical_space" type="text" value="<?php echo esc_attr($form_vertical_space); ?>" title="Vertical space between boxes (pixels)" />
					</p>

					<!-- -------------------- DIFFERENT RESOLUTIONS -------------------- -->
					
					<div class="section_title">
						<span title="The number of columns according to its maximum width. If the browser's width (including the scroll bar) is equal or lees than the maximum width (pixels) the number of columns will be applied. Important: Leave the maximum width empty if you don't want the portfolio to use it">
							Grid Layout for different resolutions
						</span>
					</div>
					
					<p>
						<label for="">Enable layout for different resolutions</label>
						<input type="checkbox" name="enable_responsivity" <?php echo isset( $form_enable_responsivity ) ? 'value="1" checked="checked"' : ''; ?> title="Enable the responsive settings below for different resolutions" />
					</p>

					<p>
						<label for="">Enable spacing between boxes for different resolutions</label>
						<input type="checkbox" name="enable_spacing" <?php echo isset( $form_enable_spacing ) ? 'value="1" checked="checked"' : ''; ?> title="Enable the responsive settings below for different resolutions" />
					</p>
					
					<div class="all_resolutions">
					<?php
						$default_resolutions = array(
							array(
								"icon" 				=> "fa-desktop", 
								"name" 				=> "Desktop large", 
								"maximum_width" 	=>"1440", 
								"columns" 			=> "3",
								"horizontal_space" 	=> "15",
								"vertical_space" 	=> "15"
							),
							array(
								"icon" 				=> "fa-desktop", 
								"name" 				=> "Desktop medium", 
								"maximum_width" 	=>"1280", 
								"columns" 			=> "3",
								"horizontal_space" 	=> "15",
								"vertical_space" 	=> "15"
							), 
							array(
								"icon" 				=> "fa-desktop", 
								"name" 				=> "Desktop small", 
								"maximum_width" 	=>"1024", 
								"columns" 			=> "3",
								"horizontal_space" 	=> "15",
								"vertical_space" 	=> "15"
							),
							array(
								"icon" 				=> "fa-tablet fa-rotate-270", 
								"name" 				=> "Tablet landscape", 
								"maximum_width" 	=>"966", 
								"columns" 			=> "3",
								"horizontal_space" 	=> "15",
								"vertical_space" 	=> "15"
							),
							array(
								"icon" 				=> "fa-tablet", 
								"name" 				=> "Tablet", 
								"maximum_width" 	=> "768", 
								"columns" 			=> "2",
								"horizontal_space" 	=> "10",
								"vertical_space" 	=> "10"
							),
							array(
								"icon" 				=> "fa-mobile fa-rotate-270", 
								"name" 				=> "Mobile landscape", 
								"maximum_width" 	=>"640", 
								"columns" 			=> "2",
								"horizontal_space" 	=> "10",
								"vertical_space" 	=> "10"
							),
							array(
								"icon" 				=> "fa-mobile", 
								"name" 				=> "Mobile", 
								"maximum_width" 	=>"480", 
								"columns" 			=> "1",
								"horizontal_space" 	=> "10",
								"vertical_space" 	=> "10"
							),
						);

						foreach ($default_resolutions as $id => $value) { 
							$maximum_width 	= isset($form_resolutions[$id]['maximum_width']) ? $form_resolutions[$id]['maximum_width'] : $value['maximum_width'];
							$columns 		= isset($form_resolutions[$id]['columns']) ? $form_resolutions[$id]['columns'] : $value['columns'];
							$h_space 		= isset($form_resolutions[$id]['horizontal_space']) ? $form_resolutions[$id]['horizontal_space'] : $value['horizontal_space'];
							$v_space 		= isset($form_resolutions[$id]['vertical_space']) ? $form_resolutions[$id]['vertical_space'] : $value['vertical_space'];
					?>
							<p>
								<label for="">
									<span class="fa <?php echo $value['icon']; ?>"></span> &nbsp; <?php echo $value['name']; ?>
								</label>

								<input name="resolutions[<?php echo $id; ?>][maximum_width]" type="text" value="<?php echo esc_attr($maximum_width); ?>" style="width:50px;" title="Maximum width (pixels)" /> px 
								&nbsp; &nbsp;

								<select name="resolutions[<?php echo $id; ?>][columns]" >
									<?php for ($i=1; $i <= 10; $i++) {  ?>
										<option value="<?php echo esc_attr($i); ?>" <?php if($columns==$i){echo "selected";} ?> ><?php echo $i; ?> <?php if($i == 1){ echo "column"; }else{ echo "columns"; } ?> per row</option>
									<?php } ?>
								</select>
								&nbsp; &nbsp;
								&nbsp; &nbsp;
								&nbsp; &nbsp;
								&nbsp; &nbsp;
								
								<span class="spacing_for_resolutions">
									Horizontal space
									<input name="resolutions[<?php echo $id; ?>][horizontal_space]" type="text" value="<?php echo esc_attr($h_space); ?>" style="width:50px;" title="Horizontal space between boxes (pixels)" />
									&nbsp; &nbsp;

									Vertical space
									<input name="resolutions[<?php echo $id; ?>][vertical_space]" type="text" value="<?php echo esc_attr($v_space); ?>" style="width:50px;" title="Vertical space between boxes (pixels)" />
								</span>
							</p>
   					<?php 
   						} 
   					?>
   					</div>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-4">

					<!-- ====================================================================== --
							THUMBNAIL
					 !-- ====================================================================== -->

					<!-- -------------------- THUMBNAILS -------------------- -->

					<div class="section_title">
						<span title="Configure the thumbnails">
							THUMBNAILS
						</span>
					</div>
				
					<p>
						<label for="">Thumbnail size</label>
						<?php
						global $_wp_additional_image_sizes;
						$sizes  = array();
						foreach( get_intermediate_image_sizes() as $index ){
							$sizes [$index] = array( 0, 0 );
							if ( in_array( $index, array( 'thumbnail', 'medium', 'large' ) ) ) {
								$sizes [$index][0] = get_option( $index . '_size_w' );
								$sizes [$index][1] = get_option( $index . '_size_h' );
							} else {
								if ( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[$index] ) ){ 
									$sizes[$index] = array( $_wp_additional_image_sizes[ $index ]['width'], $_wp_additional_image_sizes[$index]['height'] ); 
								}
							}
						}
						?>
						<select name="thumbnail_size" title="The resolution for the thumbnail. <br> The original resolution is loaded if a thumbnail size doesn't exist">	
							<option value="full" <?php if($form_thumbnail_size == 'full'){echo "selected";} ?>> 
								Original resolution
							</option>
							<?php
								if ( isset($sizes) && !empty( $sizes ) ){
									foreach ( $sizes as $thumb_key => $thumb_size ){
							?>
										<option value="<?php echo esc_attr( $thumb_key ); ?>"<?php if($form_thumbnail_size == $thumb_key){echo "selected";} ?>> <?php echo $thumb_key . ' (' . $thumb_size[0] . 'x' . $thumb_size[1] . ')'; ?></option>
							<?php
									}
								}
							?>							
						</select>
					</p>

					<p>
						<label for="">Ratio</label>
						<input style="width:50px;" name="thumbnail_width" type="text" value="<?php echo esc_attr($form_thumbnail_width); ?>" /> : 
						<input style="width:50px;" name="thumbnail_height" type="text" value="<?php echo esc_attr($form_thumbnail_height); ?>" />
						&nbsp;
						<span class="fa fa-question-circle" title="You can specify that for certain width you want certain height and the plugin will cut the height for you (depending on the resolution), this works as the aspect ratio. <br> Leave these fields empty if you would like to use the default dimensions of the thumbnails. <br> <strong>Important:</strong> If you are using the featured image as thumbnail then the plugin will set the default ratio if these fields are empty (so the featured image will always have a ratio specified)"></span>
					</p>
						
					<p>
						<label for="">Thumbnail link</label>
						<select name="thumbnail_link" title="Make the whole thumbnail a link, you can open the popup or link to an URL.">
							<option value="to_nothing" <?php if($form_thumbnail_link == 'to_nothing'){echo "selected";} ?>>Link to nothing (not link)</option>
							<option value="to_popup" <?php if($form_thumbnail_link == 'to_popup'){echo "selected";} ?>>Open the popup</option>
							<option value="to_post_page" <?php if($form_thumbnail_link == 'to_post_page'){echo "selected";} ?>> Link to post page</option>
						</select>
					</p>

					<p>
						<label for="">Thumbnail URL</label>
						<select name="thumbnail_url" title="<strong>Featured image:</strong> the thumbnail will be the 'Featured image'. <br><strong>First one it finds:</strong> The first image in the post content will be set as the thumbnail. <br><strong>Find something:</strong> Only if there's no 'Featured image' thumbnail set the first image in the post content as the thumbnail">
							<option value="featured_image" <?php if($form_thumbnail_url == 'featured_image'){echo "selected";} ?>>Featured image</option>
							<option value="first" <?php if($form_thumbnail_url == 'first'){echo "selected";} ?>>First one it finds</option>
							<option value="something" <?php if($form_thumbnail_url == 'something'){echo "selected";} ?>>Find something</option>
						</select>
					</p>

					<p>
						<label for="">Show loading progress</label>
						<input type="checkbox" name="show_loading_progress" <?php echo isset( $form_show_loading_progress ) ? 'value="1" checked="checked"' : ''; ?> title="If the thumbnails got the ratio specified (width and height) show the thumbnail  loading progress. <br> It will render and show the thumbnail while the data is being received" />
					</p>
						
					<p>
						<label for="">Preload all thumbnails before displaying them</label>
						<input type="checkbox" name="preload_all_thumbnails" <?php echo isset( $form_preload_all_thumbnails ) ? 'value="1" checked="checked"' : ''; ?> title="If the thumbnails got the ratio specified (width and height) wait until all thumbnails are loaded and then display them" />
					</p>

					<!-- -------------------- OVERLAY -------------------- -->

					<div class="section_title">
						<span title="Configure thumbnails overlay">
							OVERLAY
						</span>
					</div>		
						
					<!--
					<p>
						<label for="">Thumbnail overlay</label>
						<input type="checkbox" name="thumbnail_overlay" <?php echo isset( $form_thumbnail_overlay ) ? 'value="1" checked="checked"' : ''; ?> title="Activate or deactivate the thumbnail overlay" />
					</p>
					-->		

					<p>
						<label for="">Always visible</label>
						<input type="checkbox" name="overlay_always_visible" <?php echo isset( $form_overlay_always_visible ) ? 'value="1" checked="checked"' : ''; ?> title="Always show the overlay effect, you must select 'Fade' as the overlay effect" />
					</p>

					<p>
						<label for="">Overlay effect</label>
						<select name="overlay_effect" title="The effect of the thumbnail overlay">
							<option value="fade" <?php if($form_overlay_effect == 'fade'){echo "selected";} ?>>Fade</option>
							<option value="push-up" <?php if($form_overlay_effect == 'push-up'){echo "selected";} ?>>Push Up</option>
							<option value="push-down" <?php if($form_overlay_effect == 'push-down'){echo "selected";} ?>>Push Down</option>
							<option value="push-up-100%" <?php if($form_overlay_effect == 'push-up-100%'){echo "selected";} ?>>Push Up 100%</option>
							<option value="push-down-100%" <?php if($form_overlay_effect == 'push-down-100%'){echo "selected";} ?>>Push Down 100%</option>
							<option value="reveal-top" <?php if($form_overlay_effect == 'reveal-top'){echo "selected";} ?>>Reveal Top</option>
							<option value="reveal-bottom" <?php if($form_overlay_effect == 'reveal-bottom'){echo "selected";} ?>>Reveal Bottom</option>
							<option value="reveal-top-100%" <?php if($form_overlay_effect == 'reveal-top-100%'){echo "selected";} ?>>Reveal Top 100%</option>
							<option value="reveal-bottom-100%" <?php if($form_overlay_effect == 'reveal-bottom-100%'){echo "selected";} ?>>Reveal Bottom 100%</option>
							<option value="direction-aware" <?php if($form_overlay_effect == 'direction-aware'){echo "selected";} ?>>Direction Aware</option>
							<option value="direction-aware-fade" <?php if($form_overlay_effect == 'direction-aware-fade'){echo "selected";} ?>>Direction Aware Fade</option>
							<option value="direction-right" <?php if($form_overlay_effect == 'direction-right'){echo "selected";} ?>>Direction Right</option>
							<option value="direction-left" <?php if($form_overlay_effect == 'direction-left'){echo "selected";} ?>>Direction Left</option>
							<option value="direction-top" <?php if($form_overlay_effect == 'direction-top'){echo "selected";} ?>>Direction Top</option>
							<option value="direction-bottom" <?php if($form_overlay_effect == 'direction-bottom'){echo "selected";} ?>>Direction Bottom</option>
						</select>
					</p>		

					<p>
						<label for="">Overlay Speed</label>
						<input type="text" name="overlay_speed" value="<?php echo esc_attr($form_overlay_speed); ?>" title="The speed of the thumbnail overlay effect" />
					</p>

					<p>
						<label for="">Overlay Easing</label>
						<input type="text" name="overlay_easing" value="<?php echo esc_attr($form_overlay_easing); ?>" title="The easing of the thumbnail overlay effect, you can check all the easings here: https://api.jqueryui.com/easings/" />
					</p>								
								
					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-5">

					<!-- ====================================================================== --
							POPUP
					 !-- ====================================================================== -->
					
					<!-- -------------------- POPUP -------------------- -->

					<div class="section_title">
						<span title="Configure the popup">
							POPUP
						</span>
					</div>

					<p>
						<label for="">Popup image size</label>
						<select name="popup_size" title="The resolution for the popup image. The original resolution is loaded if an image size doesn't exist">	
							<option value="full" <?php if($form_popup_size == 'full'){echo "selected";} ?>> 
								Original resolution
							</option>
							<?php
								if ( isset($sizes) && !empty( $sizes ) ){
									foreach ( $sizes as $thumb_key => $thumb_size ){
							?>
										<option value="<?php echo esc_attr( $thumb_key ); ?>"<?php if($form_popup_size == $thumb_key){echo "selected";} ?>> <?php echo $thumb_key . ' (' . $thumb_size[0] . 'x' . $thumb_size[1] . ')'; ?></option>
							<?php
									}
								}
							?>							
						</select>
					</p>
					
					<p>
						<label for="">Show only loaded images in the popup</label>
						<input type="checkbox" name="show_only_loaded_boxes" <?php echo isset( $form_show_only_loaded_boxes ) ? 'value="1" checked="checked"' : ''; ?> title="Show only the images loaded (the ones in the grid) in the popup gallery.<br><strong>Important:</strong> this only works if you have activated the gallery option." />
					</p>	
					
					<p>
						<label for="">Consider the filtering in the popup</label>
						<input type="checkbox" name="consider_filtering" <?php echo isset( $form_consider_filtering ) ? 'value="1" checked="checked"' : ''; ?> title="Consider the filtering in the popup gallery so it only shows filtered boxes (this includes searching and sorting).<br><strong>Important:</strong> this only works if you have activated the gallery option." />
					</p>
					
					<p>
						<label for="">Enable deep linking in the popup</label>
						<input type="checkbox" name="deep_linking_popup" <?php echo isset( $form_deep_linking_popup ) ? 'value="1" checked="checked"' : ''; ?> title="Enable deep linking in the popup" />
					</p>	

					<p>
						<label for="">Popup</label>
						<select name="popup" title="Choose which popup plugin you would like to use">
							<option value="fancybox" <?php if($form_popup == 'fancybox'){echo "selected";} ?>>Fancybox</option>
							<option value="magnificpopup" <?php if($form_popup == 'magnificpopup'){echo "selected";} ?>>Magnific Popup</option>
							<option value="none" <?php if($form_popup == 'none'){echo "selected";} ?>>None</option>
						</select>
					</p>

					<p>
						<label for="">Popup Title</label>
						<select name="popup_title" title="What to use for the popup title (the text that's below the popup image)">
							<option value="none" <?php if($form_popup_title == 'none'){echo "selected";} ?>>None</option>
							<option value="post_title" <?php if($form_popup_title == 'post_title'){echo "selected";} ?>>Post Title</option>
						</select>
					</p>

					<!-- -------------------- FANCYBOX -------------------- -->
					
					<div class="section_title">
						<span title="Configure the fancybox options">
							FANCYBOX
						</span>
					</div>	

					<p>
						<label for="">Loop</label>
						<input type="checkbox" name="fancyb_loop" <?php echo isset( $form_fancyb_loop ) ? 'value="1" checked="checked"' : ''; ?> title="Enable infinite gallery navigation" />
					</p>

					<p>
						<label for="">Margin</label>
						<input type="text" name="fancyb_margin" value="<?php echo esc_attr($form_fancyb_margin); ?>" title="Space around image, ignored if zoomed-in or viewport smaller than 800px" />
					</p>

					<p>
						<label for="">Keyboard</label>
						<input type="checkbox" name="fancyb_keyboard" <?php echo isset( $form_fancyb_keyboard ) ? 'value="1" checked="checked"' : ''; ?> title="Enable keyboard navigation" />
					</p>

					<p>
						<label for="">Arrows</label>
						<input type="checkbox" name="fancyb_arrows" <?php echo isset( $form_fancyb_arrows ) ? 'value="1" checked="checked"' : ''; ?> title="Should display navigation arrows at the screen edges" />
					</p>

					<p>
						<label for="">Infobar</label>
						<input type="checkbox" name="fancyb_infobar" <?php echo isset( $form_fancyb_infobar ) ? 'value="1" checked="checked"' : ''; ?> title="Should display infobar (counter and arrows at the top)" />
					</p>

					<p>
						<label for="">Toolbar</label>
						<input type="checkbox" name="fancyb_toolbar" <?php echo isset( $form_fancyb_toolbar ) ? 'value="1" checked="checked"' : ''; ?> title="Should display toolbar (buttons at the top)" />
					</p>

					<p>
						<label for="">Buttons</label>
						<input type="checkbox" name="fancyb_btn_slideshow" <?php echo isset( $form_fancyb_btn_slideshow ) ? 'value="1" checked="checked"' : ''; ?> /> Slideshow &nbsp;&nbsp;
						<input type="checkbox" name="fancyb_btn_fullscreen" <?php echo isset( $form_fancyb_btn_fullscreen ) ? 'value="1" checked="checked"' : ''; ?> /> Fullscreen &nbsp;&nbsp;
						<input type="checkbox" name="fancyb_btn_thumbs" <?php echo isset( $form_fancyb_btn_thumbs ) ? 'value="1" checked="checked"' : ''; ?> /> Thumbs &nbsp;&nbsp;
						<input type="checkbox" name="fancyb_btn_close" <?php echo isset( $form_fancyb_btn_close ) ? 'value="1" checked="checked"' : ''; ?> /> Close &nbsp;&nbsp;
					</p>

					<p>
						<label for="">Idle time</label>
						<input type="text" name="fancyb_idle_time" value="<?php echo esc_attr($form_fancyb_idle_time); ?>" title="Detect 'idle' time in seconds" />
					</p>

					<p>
						<label for="">Protect</label>
						<input type="checkbox" name="fancyb_protect" <?php echo isset( $form_fancyb_protect ) ? 'value="1" checked="checked"' : ''; ?> title="Disable right-click and use simple image protection for images" />
					</p>

					<p>
						<label for="">Animation effect</label>
						<select name="fancyb_animation_effect" title="Open/close animation type">
							<option value="zoom" <?php if($form_fancyb_animation_effect == 'zoom'){echo "selected";} ?>>Zoom</option>
							<option value="fade" <?php if($form_fancyb_animation_effect == 'fade'){echo "selected";} ?>>Fade</option>
							<option value="zoom-in-out" <?php if($form_fancyb_animation_effect == 'zoom-in-out'){echo "selected";} ?>>Zoom In Out</option>
							<option value="false" <?php if($form_fancyb_animation_effect == 'false'){echo "selected";} ?>>None</option>
						</select>
					</p>

					<p>
						<label for="">Animation duration</label>
						<input type="text" name="fancyb_animation_duration" value="<?php echo esc_attr($form_fancyb_animation_duration); ?>" title="Duration in ms for open/close animation" />
					</p>

					<p>
						<label for="">Transition effect</label>
						<select name="fancyb_transition_effect" title="Open/close animation type">
							<option value="fade" <?php if($form_fancyb_transition_effect == 'fade'){echo "selected";} ?>>Fade</option>
							<option value="slide" <?php if($form_fancyb_transition_effect == 'slide'){echo "selected";} ?>>Slide</option>
							<option value="circular" <?php if($form_fancyb_transition_effect == 'circular'){echo "selected";} ?>>Circular</option>
							<option value="tube" <?php if($form_fancyb_transition_effect == 'tube'){echo "selected";} ?>>Tube</option>
							<option value="zoom-in-out" <?php if($form_fancyb_transition_effect == 'zoom-in-out'){echo "selected";} ?>>Zoom In Out</option>
							<option value="rotate" <?php if($form_fancyb_transition_effect == 'rotate'){echo "selected";} ?>>Rotate</option>
							<option value="false" <?php if($form_fancyb_transition_effect == 'false'){echo "selected";} ?>>None</option>
						</select>
					</p>

					<p>
						<label for="">Transition duration</label>
						<input type="text" name="fancyb_transition_duration" value="<?php echo esc_attr($form_fancyb_transition_duration); ?>" title="Duration in ms for transition animation" />
					</p>

					<p>
						<label for="">Auto start slide show</label>
						<input type="checkbox" name="fancyb_slideshow_autostart" <?php echo isset( $form_fancyb_slideshow_autostart ) ? 'value="1" checked="checked"' : ''; ?> title="Autostart slideshow when fancybox opens" />
					</p>

					<p>
						<label for="">Auto start in fullscreen</label>
						<input type="checkbox" name="fancyb_fullscreen_autostart" <?php echo isset( $form_fancyb_fullscreen_autostart ) ? 'value="1" checked="checked"' : ''; ?> title="activate or deactivate fullscreen when open" />
					</p>

					<p>
						<label for="">Auto start thumbs</label>
						<input type="checkbox" name="fancyb_thumbs_autostart" <?php echo isset( $form_fancyb_thumbs_autostart ) ? 'value="1" checked="checked"' : ''; ?> title="Display thumbnails on opening/closing" />
					</p>

					<p>
						<label for="">Touch</label>
						<input type="checkbox" name="fancyb_touch" <?php echo isset( $form_fancyb_touch ) ? 'value="1" checked="checked"' : ''; ?> title="Allow to drag content" />
					</p>

					<!-- -------------------- MAGNIFIC POPUP -------------------- -->
					
					<div class="section_title">
						<span title="Configure the magnific popup options">
							MAGNIFIC POPUP
						</span>
					</div>

					<p>
						<label for="">Number of items to preload before</label>
						<span class="slider" data-max="15" data-min="0"></span>
						<input name="mp_preload_before" type="text" value="<?php echo esc_attr($form_mp_preload_before); ?>" title="Preloads nearby items. Number of items to preload before the current. <br> These values are automatically switched based on direction of movement" />
					</p>

					<p>
						<label for="">Number of items to preload after</label>
						<span class="slider" data-max="15" data-min="0"></span>
						<input name="mp_preload_after" type="text" value="<?php echo esc_attr($form_mp_preload_after); ?>" title="Preloads nearby items. Number of items to preload after the current. <br> These values are automatically switched based on direction of movement" />
					</p>

					<p>
						<label for="">Align top</label>
						<input type="checkbox" name="mp_align_top" <?php echo isset( $form_mp_align_top ) ? 'value="1" checked="checked"' : ''; ?> title="Aligned to top instead of to center" />
					</p>

					<p>
						<label for="">Enable gallery for popup</label>
						<input type="checkbox" name="mp_enable_gallery" <?php echo isset( $form_mp_enable_gallery ) ? 'value="1" checked="checked"' : ''; ?> title="Enable gallery option for popup" />
					</p>								
							
					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-6">

					<!-- ====================================================================== --
							CONTENT
					 !-- ====================================================================== -->

					<p>
						<label>Title maximum length</label>
						<input name="title_max_length" type="text" value="<?php echo esc_attr($form_title_max_length); ?>" title="The maximum number of characters in the title. <br><strong>Important</strong> Leave it empty if you don't want to restrict the length." />
					</p>

					<p>
						<label>Content delimiter</label>
						<input name="content_delimiter" type="text" value="<?php echo esc_attr($form_content_delimiter); ?>" title="If you don't want the plugin to take all the content from your post you can limit it, so you can put this code inside your post content in order to establish the limit." />
					</p>

					<p>
						<label>Maximum words of the content or excerpt</label>
						<input name="excerpt_max_words" type="text" value="<?php echo esc_attr($form_excerpt_max_words); ?>" title="The maximum number of words to show in the post content or excerpt, the rest will be cut off. <br><strong>Important</strong> Leave it empty if you don't want to restrict the number of words." />
					</p>

					<p>
						<label>String at the end of content or excerpt</label>
						<input name="excerpt_string_end" type="text" value="<?php echo esc_attr($form_excerpt_string_end); ?>" title="The string at the end of the content or excerpt when it is cut off." />
					</p>
							
					<p>
						<label>Remove shortcodes from content or excerpt</label>
						<input type="checkbox" name="excerpt_remove_shortcodes" <?php echo isset( $form_excerpt_remove_shortcodes ) ? 'value="1" checked="checked"' : ''; ?> title="Would you like to remove any shortcode from the content or excerpt" />
					</p>

					<p>
						<label>Strip HTML tags from content or excerpt</label>
						<input type="checkbox" name="excerpt_strip_tags" <?php echo isset( $form_excerpt_strip_tags ) ? 'value="1" checked="checked"' : ''; ?> title="Would you like to strip HTML tags from the content or excerpt?" />
					</p>

					<p>
						<label>Except this HTML tags</label>
						<input name="excerpt_except_this_tags" type="text" value="<?php echo esc_attr($form_excerpt_except_this_tags); ?>" placeholder="i.e. &#60;em>&#60;strong>&#60;i>&#60;b>" title="If the option above is active then you can define some HTML tags allowed in the excerpt. <br>Don't put any space or extra character between tags." />
					</p>
						

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-7">

			  		<!-- ====================================================================== --
							FILTER-SEARCH-SORT
					 !-- ====================================================================== -->

					<div class="section_title">
						<span title="Specify the filters that you want for the portfolio">
							Create your filters
						</span>
					</div>

					<p>
						<label for="">Enable deep linking in the filter</label>
						<input type="checkbox" name="deep_linking_filter" <?php echo isset( $form_deep_linking_filter ) ? 'value="1" checked="checked"' : ''; ?> title="Enable deep linking in the filter" />
					</p>

					<p>
						<label for="">Multiple filter logic</label>
						<select name="multiple_filter_logic" title="When using multiple filters the logic that the plugin will use">
							<option value="AND" <?php if($form_multiple_filter_logic=='AND'){echo "selected";} ?> >AND</option>
							<option value="OR" <?php if($form_multiple_filter_logic=='OR'){echo "selected";} ?> >OR</option>
						</select>
					</p>

					<p>
						<label for="">Drop down event</label>
						<select name="drop_down_event" title="When using a drop down the filter items will be shown on">
							<option value="hover" <?php if($form_drop_down_event=='hover'){echo "selected";} ?> >Hover</option>
							<option value="click" <?php if($form_drop_down_event=='click'){echo "click";} ?> >Click</option>
						</select>
					</p>

					<p>
						<a href="http://localhost:8888/wordpress/wp-admin/admin.php?page=media-boxes&amp;subpage=new" class="button-primary blue add_new_filter">
							<span class="fa fa-plus"></span>&nbsp; Add a new filter group
						</a>
					</p>

					<?php if($subpage == 'new'){ ?>
						<script>
							jQuery(document).ready(function(){
								jQuery('.add_new_filter').trigger('click');
							});
						</script>
					<?php } ?>
					
					<div class="filter_group_container">

						<?php 
							function sortArrayByArray(Array $array, Array $orderArray) {
							    $ordered = array();
							    foreach($orderArray as $key) {
							        if(array_key_exists($key,$array)) {
							            $ordered[$key] = $array[$key];
							            unset($array[$key]);
							        }
							    }
							    return $ordered + $array;
							}

						?>

						<?php foreach ($form_filters as $key => $value) { ?>
							<div class="filter_group">
						
								<input class="filterId" type="hidden" name="filters[<?php echo $key; ?>][filter_id]" value="<?php echo esc_attr($value['filter_id']); ?>">

								<div class="filter_title">Filter Group <?php echo $value['filter_id']; ?> <a class="remove_filter" href="#"><i class="fa fa-times"></i></a></div>
								<div class="filter_option">
									<label for="">"All" word</label>
									<input type="text" name="filters[<?php echo $key; ?>][filter_all_word]" title="The All word in the filter" value="<?php echo esc_attr($value['filter_all_word']); ?>" />
								</div>
								<div class="filter_option">
									<label for="">Layout</label>
									<select name="filters[<?php echo $key; ?>][filter_layout]" title="The layout of the filter">
										<option value="inline" <?php if($value['filter_layout'] == 'inline'){echo "selected";} ?>>In line</option>
										<option value="dropdown" <?php if($value['filter_layout'] == 'dropdown'){echo "selected";} ?>>Dropdown</option>
									</select>
								</div>
								<div class="filter_option">
									<label for="">Default selected filter item</label>
									<select class="selected_item" name="filters[<?php echo $key; ?>][filter_selected_item]" title="The selected filter item by default">
										<option value="*" <?php if($value['filter_selected_item'] == '*'){echo "selected";} ?>>All</option>
										<?php foreach ($default_post_categories as $id => $name) { ?>
											<option value="<?php echo esc_attr($id); ?>" <?php if($value['filter_selected_item'] == $id){echo "selected";} ?>> <?php echo $name; ?> </option>
					   					<?php } ?>
									</select>
								</div>
								<div class="filter_option">
									<label for="">Available filter items</label>
									<div class="sort_filter_items">
										<?php
											$filter_items 	= isset( $value['filter_items'] ) ? $value['filter_items'] : array();

											if($form_post_type=='custom-media-gallery'){
												$default_post_categories = array();
												foreach ($form_custom_media_gallery_cat as $row_cat) {
													$default_post_categories[ $row_cat['id'] ] = $row_cat['category'];
												}
											}

											$new_array 		= sortArrayByArray($default_post_categories, $filter_items);

											foreach ($new_array as $id => $name) { 
										?>
												<div>
													<input type="checkbox" name="filters[<?php echo $key; ?>][filter_items][]" value="<?php echo esc_attr($id); ?>" <?php if( in_array($id, $filter_items) ){ echo "checked"; } ?> /> <?php echo $name; ?>
												</div>
					   					<?php 
					   						} 
					   					?>
									</div>

								</div>
							</div>
						<?php } ?>
					</div>
					
					<div class="section_title">
						<span title="Configure the sorting feature for your portfolio">
							Configure the sorting
						</span>
					</div>

					<p>
						<label for="">Default sorting</label>
						<select name="default_sorting" title="Start sorting by">
							<option value="none" <?php if($form_default_sorting=='none'){echo "selected";} ?>>None</option>
							<?php foreach ($order_by_items as $row) { ?>
								<option value="<?php echo esc_attr($row['id']); ?>" <?php if($row['id']==$form_default_sorting){echo "selected";} ?>><?php echo $row['description']; ?></option>
							<?php } ?>
						</select>
					</p>

					<p>
						<label for="" style="float:left;">Sortings <br><small>Multiple selection allowed</small></label>
						<select name="sortings[]" multiple="multiple" style="min-height:150px;">
							<option value="original-order" <?php if(in_array('original-order', $form_sortings)){echo "selected";} ?>>None</option>
							<?php foreach ($order_by_items as $row) { ?> 
								<option value="<?php echo esc_attr($row['id']); ?>" <?php if(in_array($row['id'], $form_sortings)){echo "selected";} ?>><?php echo $row['description']; ?></option>
							<?php } ?>
						</select>

						<!-- ALL SORTINGS OPTIONS, WE NEED TO TO PRESENT THE NAME OF THE SORTING-KEY/ID -->
						
						<input type="hidden" name="all_sortings[original-order]" value="None">		
						<?php foreach ($order_by_items as $row) { ?> 
							<input type="hidden" name="all_sortings[<?php echo $row['id']; ?>]" value="<?php echo esc_attr($row['description']); ?>">
						<?php } ?>
					</p>

					<p>
						<label for="">Default sorting order</label>
						<select name="default_sorting_order" title="Sorting order">
							<option value="ascending" <?php if($form_default_sorting_order=='ascending'){echo "selected";} ?>>Ascending</option>
							<option value="descending" <?php if($form_default_sorting_order=='descending'){echo "selected";} ?>>Descending</option>
						</select>
					</p>

					<p>
						<label>Sort by text</label>
						<input name="sort_by_text" type="text" value="<?php echo esc_attr($form_sort_by_text); ?>" title="The text before each sorting option." />
					</p>

					<div class="section_title">
						<span title="Configure the search feature for your portfolio">
							Search settings
						</span>
					</div>

					<p>
						<label for="">Enable deep linking in the search</label>
						<input type="checkbox" name="deep_linking_search" <?php echo isset( $form_deep_linking_search ) ? 'value="1" checked="checked"' : ''; ?> title="Enable deep linking in the search" />
					</p>

					<p>
						<label>Search default text</label>
						<input name="search_default_text" type="text" value="<?php echo esc_attr($form_search_default_text); ?>" title="Placeholder text of search input text field." />
					</p>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
			  	<div id="tabs-8">

			  		<!-- ====================================================================== --
							LAYOUT
					 !-- ====================================================================== -->

					<!-- -------------------- skin -------------------- -->
					
					<div class="section_title">
						<span title="Select a skin made in the skin editor page">
							Select a skin
						</span>
					</div>

					<?php 
						/* Get skins from db again after we delete one */
						$skins = get_option( MEDIA_BOXES_PREFIX . '_skins' );
					?>

					<?php if( count($skins) == 0 || empty($skins)){ ?>
						<div class="media_boxes_warning">
							You don't have any skins yet, <a href="<?php echo esc_attr( admin_url('admin.php?page=media_boxes-skin-editor') ); ?>">create a new one here!</a>
						</div>
					<?php }else{ ?>
						<select name="skin">
							<?php if( $subpage == 'edit' ){ ?>
								<option value=""></option>
							<?php } ?>
							<?php foreach ($skins as $key => $value) { ?>
								<option value="<?php echo esc_attr($value['uniqid']); ?>" <?php if($value['uniqid']==$form_skin){echo "selected";} ?>><?php echo $value['name']; ?></option>
							<?php } ?>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="<?php echo esc_attr( admin_url('admin.php?page=media_boxes-skin-editor') ); ?>">edit your skins here!</a>
					<?php } ?>

					<!-- -------------------- Loading options -------------------- -->

					<div class="section_title">
						<span title="Choose where to place the filters, search & sort">
							Position filters, search & sort
						</span>
					</div>

					<?php 
						$default_filtering_elements 	= 	array(
																'sorting' 	=> '<i class="fa fa-sort-alpha-asc"></i>&nbsp; Sorting', 
																'search' 	=> '<i class="fa fa-search"></i>&nbsp; Search'
															);

						foreach ($form_filters as $key => $value) {
							$default_filtering_elements['filter_'.$value['filter_id']] = '<i class="fa fa-filter"></i>&nbsp; Filter Group '.$value['filter_id'];
						}
					?>

					<div class="grid">
						<div class="col-180">

							<div class="title text-center">Available Items</div>
							<div class="available_items layout_sortable_ui">
								<?php foreach ($default_filtering_elements as $id => $name) { ?>
									<?php if(in_array($id, $form_drop_zone_1) || in_array($id, $form_drop_zone_2))continue; ?>

									<div class="layout_sortable_ui_item" data-id="<?php echo $id; ?>">
										<?php echo $name; ?>
										<input type="hidden" value="<?php echo $id; ?>">
									</div>
			   					<?php } ?>
		   					</div>

						</div>
						<div class="col-500">

							<div class="title text-center">Current Grid</div>
							<div class="current_grid">
								
								<div class="drop_zone_1 layout_sortable_ui" data-drop_zone_id="drop_zone_1">
									
									<!--<div class="configure_drop_zone" data-open=".drop_zone_1_config"><span class="fa fa-cog"></span></div>-->
									<span class="drop_zone_placeholder">DROP ZONE 1</span>

									<?php foreach ($form_drop_zone_1 as $id) { ?>
										<?php if(!isset($default_filtering_elements[$id]))continue; ?>

										<div class="layout_sortable_ui_item" data-id="<?php echo $id; ?>">
											<?php echo $default_filtering_elements[$id]; ?>
											<input type="hidden" value="<?php echo $id; ?>">
										</div>
				   					<?php } ?>
								</div>

								<div class="drop_zone_2 layout_sortable_ui" data-drop_zone_id="drop_zone_2">

									<!--<div class="configure_drop_zone" data-open=".drop_zone_1_config"><span class="fa fa-cog"></span></div>-->
									<span class="drop_zone_placeholder">DROP ZONE 2</span>
	
									<?php foreach ($form_drop_zone_2 as $id) { ?>
										<?php if(!isset($default_filtering_elements[$id]))continue; ?>

										<div class="layout_sortable_ui_item" data-id="<?php echo $id; ?>">
											<?php echo $default_filtering_elements[$id]; ?>
											<input type="hidden" value="<?php echo $id; ?>">
										</div>
				   					<?php } ?>
								</div>

								<div class="grid">
									<div class="col-25p"><div class="item-fake"></div></div>
									<div class="col-25p"><div class="item-fake"></div></div>
									<div class="col-25p"><div class="item-fake"></div></div>
									<div class="col-25p"><div class="item-fake"></div></div>

									<div class="col-25p"><div class="item-fake"></div></div>
									<div class="col-25p"><div class="item-fake"></div></div>
									<div class="col-25p"><div class="item-fake"></div></div>
									<div class="col-25p"><div class="item-fake"></div></div>
								</div>

		   					</div>
							
						</div>
					</div>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>
				<div id="tabs-9">

			  		<!-- ====================================================================== --
							CSS
					 !-- ====================================================================== -->

					<div class="section_title">
						<span title="Add some CSS to the portfolio">
							CSS
						</span>
					</div>

					<p>
						<textarea name="css" style="width:100%;height:270px;"><?php echo $form_css; ?></textarea>
						<p>
							You can use '@media_boxes' before your CSS selectors so it only affects this portfolio (and no other ones in the same page), for example: 
						</p>
						<pre>
@media_boxes .media-boxes-no-more-entries{
	display: none;
}
						</pre>
						
					</p>

					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

				</div>

			</div><!-- end of the container of all tabs -->

		</form>
<?php 
	}
?>


</div><!-- end wrap -->




