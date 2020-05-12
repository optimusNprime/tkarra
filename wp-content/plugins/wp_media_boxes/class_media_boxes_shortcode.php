<?php

/**
 *
 * Media Boxes by David Blanco
 *
 * @package Media Boxes by David Blanco
 * @author  David Blanco
 * 
 */

class Media_Boxes_Shortcode{

/* ====================================================================== *
      INIT SHORTCODE
 * ====================================================================== */

	public function __construct(){

		/* Shortcode */
		add_shortcode( 'media_boxes', array( $this, 'media_boxes_shortcode' ) );

	}

/* ====================================================================== *
      MEDIA BOXES SHORTCODE
 * ====================================================================== */

	public function media_boxes_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array( 
			'id' 	=> null,
		 ), $atts ) );

		$shortcode_return = null;

		/* Check if the shortcode has an ID */
		if( isset( $id ) == false ){
			$shortcode_return = '<div class="media_boxes_warning">
									<h4>You need to specify an id for the portfolio.</h4>
									<p>For example: <code>[media_boxes id="my_first_portfolio"]</code> </p>
								 </div>';
		}else{
			$portfolios 		= get_option( MEDIA_BOXES_PREFIX . '_portfolios' );
			$current_portfolio 	= array();
			$current_skin 		= array();
			$portfolio_exist 	= false;

			foreach ( $portfolios as $portfolio_key => $portfolio ) {
				/* Check if given id exist in plugin db */
				if ( $portfolio['shortcode_id'] == $id ) {
					$current_portfolio 	= $portfolio;
					$portfolio_exist 	= true;
					$portfolio_skin 	= isset( $portfolio['skin'] ) ? $portfolio['skin'] : '-1';
					$current_skin 		= $this->get_skin($portfolio_skin);
				}
			}

			/* If a portfolio with that ID is not defined in the DB */
			if( $portfolio_exist == false ){
				$shortcode_return = '<div class="media_boxes_warning">
										<h4>This portfolio does not exist.</h4>
										<p>The portfolio with an id of <code>'.$id.'</code> is not defined in the admin page. </p>
									 </div>';
			}

			/* If the skin of the given portfolio doesn't exists */
			else if( count($current_skin) <= 0 ){
				$shortcode_return = '<div class="media_boxes_warning">
										<h4>This portfolio does not have a skin, or the skin choosen does not exists anymore.</h4>
									 </div>';	
			}

			if( $portfolio_exist && count($current_skin) > 0 ){
				$shortcode_return = $this->create_portfolio($current_portfolio, $current_skin);
			}
		}

		/* Return the content of the shortcode */
		return $shortcode_return;
	}

/* ====================================================================== *
      GET SKIN BY ID
 * ====================================================================== */		

    public function get_skin($skin_id){
    	$skins 				= get_option( MEDIA_BOXES_PREFIX . '_skins' );
    	$skin_output 		= array();

    	if(isset($skins)){
	    	foreach ($skins as $skin_key => $skin_value) {
				if( $skin_value['uniqid'] == $skin_id ){
					$skin_output = $skin_value;
				}
			}
		}

		return $skin_output;
    }

/* ====================================================================== *
      CREATE PORTFOLIO
 * ====================================================================== */	

	public function create_portfolio($portfolio, $skin){

/* ====================================================================== *
      BUILD MEDIA BOXES
 * ====================================================================== */					

		$post_type 			= $portfolio['post_type'];
		$uniqid 			= $portfolio['uniqid'];	
		$skin_css_js_extra 	= array();

	/* ====================================================================== *
	      THE ARGS OF THE QUERY
	 * ====================================================================== */	
		
		$args 	= 	array(
						'post_type' 			=> $post_type,
						'posts_per_page' 		=> isset( $portfolio['number_posts'] ) && !empty( $portfolio['number_posts'] ) ? $portfolio['number_posts'] : '-1',
						'cache_results' 		=> false,
						'post_status' 			=> $post_type == 'attachment' ? 'inherit' : 'publish',
						'ignore_sticky_posts' 	=> true,
						'orderby' 				=> $portfolio['order_by'],
						'order' 				=> $portfolio['order'],
					);

	    /* Exclude the current post from the query */

		if( isset( $portfolio['exclude_current_post'] ) ){
			$currentID = get_the_id();
			$args['post__not_in'] = array($currentID);
		}

	 	/* Custom media gallery */		

		if ( $post_type == 'custom-media-gallery' ) {
			$items = [];
			if(isset($portfolio['custom-media-gallery-items']['attachment'])){
				foreach ( $portfolio['custom-media-gallery-items']['attachment'] as $key => $item ) {
					$items[] = $key;
				}
			}else{
				$items[] = -1;
			}
			$args['post__in'] 		= $items;
			$args['orderby'] 		= 'post__in';
			$args['post_type'] 		= 'attachment';
			$args['post_status'] 	= 'inherit';
			$post_type 				= 'attachment';
		}

	/* ====================================================================== *
	      QUERY THE POSTS
	 * ====================================================================== */	

		$new_wp_query 	= new WP_Query($args);
		$media_boxes 	= '';

		global $post;

		while( $new_wp_query->have_posts() ){ 
			$new_wp_query->the_post();

			$aspect_ratio 		= "";
			$matches 			= null;
			preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_the_content(), $matches);

			$post_id 	= get_the_id();

			/* Metabox variables */
			$metabox_thumbnail_url 			= get_post_meta($post_id, 'media_boxes_thumbnail_url', true);
			$metabox_thumbnail_ratio_width 	= get_post_meta($post_id, 'media_boxes_thumbnail_ratio_width', true);
			$metabox_thumbnail_ratio_height = get_post_meta($post_id, 'media_boxes_thumbnail_ratio_height', true);
			$metabox_overlay_effect 		= get_post_meta($post_id, 'media_boxes_overlay_effect', true);
			$metabox_popup_type 			= get_post_meta($post_id, 'media_boxes_popup_type', true);
			$metabox_popup_url 				= get_post_meta($post_id, 'media_boxes_popup_url', true);
			$metabox_popup_title 			= get_post_meta($post_id, 'media_boxes_popup_title', true);
			$metabox_popup_thumb 			= get_post_meta($post_id, 'media_boxes_popup_thumb', true);
			$metabox_iframe_on_thumbnail 	= get_post_meta($post_id, 'media_boxes_iframe_on_thumbnail', true);
			$metabox_post_link 				= get_post_meta($post_id, 'media_boxes_post_link', true);
			$metabox_post_title 			= get_post_meta($post_id, 'media_boxes_post_title', true);
			$metabox_post_content 			= get_post_meta($post_id, 'media_boxes_post_content', true);
			$metabox_how_many_columns 		= get_post_meta($post_id, 'media_boxes_how_many_columns', true);
			$metabox_skin 					= get_post_meta($post_id, 'media_boxes_skin', true);

			if(!isset($metabox_how_many_columns) || $metabox_how_many_columns==''){
				$metabox_how_many_columns = '1';				
			}

			if(!isset($metabox_popup_title)){
				$metabox_popup_title = '';					
			}

			if(!isset($metabox_popup_thumb)){
				$metabox_popup_thumb = '';					
			}

			if(!isset($metabox_overlay_effect) || $metabox_overlay_effect==''){
				$metabox_overlay_effect = 'default';
			}

			if(!isset($metabox_skin) || $metabox_skin==''){
				$metabox_skin = 'use_default';
			}

			$data_columns 					= $metabox_how_many_columns != "1" ? "data-columns='".$metabox_how_many_columns."'" : "";

			/* 0. Iframe on grid */
			$iframe_on_grid = '';
			
			if(isset($metabox_iframe_on_thumbnail) && $metabox_iframe_on_thumbnail=='yes'){
				$iframe_on_grid = 'iframe-on-grid';
			}

			/* 1. The post title */ 		
			$post_title = esc_attr( trim( get_the_title() ) );
			$post_title = (isset($metabox_post_title) && $metabox_post_title!='') ? $metabox_post_title : $post_title;

			$title_length = floatval( $portfolio['title_max_length'] );
			if ( isset( $title_length ) && !empty( $title_length ) ) {
				if ( mb_strlen( $post_title ) > $title_length ) { $post_title = mb_substr ( $post_title, 0,  $title_length ).''; }
			}

			/* 2. The post link */
			$post_link = get_permalink();
			$post_link = (isset($metabox_post_link) && $metabox_post_link!='') ? $metabox_post_link : $post_link;

			/* 3. The thumbnail SRC */
			$post_thumbnail_src 	= '';

			if($portfolio['thumbnail_url'] == 'featured_image' || $post_type == 'attachment'){ // Use the featured image as thumbnail
				if( has_post_thumbnail() || $post_type == 'attachment' ){
					$post_thumbnail 	= wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $portfolio['thumbnail_size'] );
					$post_thumbnail_src	= $post_thumbnail[0];
					$post_thumbnail_w	= $post_thumbnail[1]; // thumbnail's width
					$post_thumbnail_h	= $post_thumbnail[2]; // thumbnail's height	
					$aspect_ratio = " data-width='".$post_thumbnail_w."' data-height='".$post_thumbnail_h."'  ";
				}
			}else if($portfolio['thumbnail_url'] == 'first'){ // Use the first image in the post content
				if(isset( $matches ) && !empty( $matches )){
					$post_thumbnail_src = $matches[1];
				}
			}else if( $portfolio['thumbnail_url'] == 'something'){ //If there's no "featured image" use the first image of the content
				if( has_post_thumbnail() ){
					$post_thumbnail 	= wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $portfolio['thumbnail_size'] );
					$post_thumbnail_src	= $post_thumbnail[0];
					$post_thumbnail_w	= $post_thumbnail[1]; // thumbnail's width
					$post_thumbnail_h	= $post_thumbnail[2]; // thumbnail's height	
					$aspect_ratio = " data-width='".$post_thumbnail_w."' data-height='".$post_thumbnail_h."'  ";
				}else if(isset( $matches ) && !empty( $matches )){
					$post_thumbnail_src = $matches[1];
				}
			}

			if(isset($metabox_thumbnail_url) && $metabox_thumbnail_url!=''){
				$post_thumbnail_src = $metabox_thumbnail_url;
			}
			
			/* 4. the popup image SRC */
			$popup_src 	= '';
			$popup_type = 'image';

			if($portfolio['thumbnail_url'] == 'featured_image' || $post_type == 'attachment'){ // Use the featured image as thumbnail
				if( has_post_thumbnail() || $post_type == 'attachment' ){
					$post_image 		= wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $portfolio['popup_size'] );
					$popup_src			= $post_image[0];
				}
			}else{
				$popup_src = $post_thumbnail_src;
			}

			if(isset($metabox_popup_url) && $metabox_popup_url!=''){
				$popup_src = $metabox_popup_url;
			}

			if(isset($metabox_popup_type) && $metabox_popup_type!=''){
				$popup_type = $metabox_popup_type;
			}

			/* 5. The aspect ratio */
			if( $portfolio['thumbnail_width'] != '' && $portfolio['thumbnail_height'] != '' ){
				$aspect_ratio = " data-width='".$portfolio['thumbnail_width']."' data-height='".$portfolio['thumbnail_height']."'  ";
			}

			if(isset($metabox_thumbnail_ratio_width) && $metabox_thumbnail_ratio_width!='' && isset($metabox_thumbnail_ratio_height) && $metabox_thumbnail_ratio_height!=''){
				$aspect_ratio = " data-width='$metabox_thumbnail_ratio_width' data-height='$metabox_thumbnail_ratio_height'  ";
			}

			/* 6. Popup title */
			$popup_title = '';
			if( isset( $portfolio['popup_title'] ) && $portfolio['popup_title'] == 'post_title' ){
				$popup_title = $post_title;
			}

			if($metabox_popup_title != ''){
				$popup_title = $metabox_popup_title;
			}

			/* 6.5 Popup thumb */
			$popup_thumb = '';

			if($metabox_popup_thumb != ''){
				$popup_thumb = "data-thumb='$metabox_popup_thumb'";
			}

			/* 7. The thumbnail link */
			$thumbnail_link = $portfolio['thumbnail_link'];
			$link_to_post_page    	= '';
			$open_popup    			= '';
			$open_popup_extra 		= '';
			if($thumbnail_link == "to_post_page"){
				$link_to_post_page = "onclick=\"location.href='$post_link';\" style=\"cursor:pointer;\"";
			}else if($thumbnail_link == "to_popup"){
				$open_popup 		= 'mb-open-popup';
				$open_popup_extra 	= " data-type='$popup_type' data-src='$popup_src' data-title='$popup_title' $popup_thumb ";

				if($iframe_on_grid!=''){
					$open_popup = $iframe_on_grid;
				}
			}

			/* 8. The post date */
			$post_date = date_i18n( get_option( 'date_format' ), get_post_time( 'U', true ) );

			/* 9. The post content and excerpt */
			$post_content 			= $post->post_content;
			$post_content 			= (isset($metabox_post_content) && $metabox_post_content!='') ? $metabox_post_content : $post_content;

			$strip_tags 			= isset( $portfolio['excerpt_strip_tags'] ) ? true : false;
			$except_this_tags		= isset( $portfolio['excerpt_except_this_tags'] ) ? trim( $portfolio['excerpt_except_this_tags'] ) : '';
			$remove_shortcodes 		= isset( $portfolio['excerpt_remove_shortcodes'] ) ? true : false;
			$string_end 			= isset( $portfolio['excerpt_string_end'] ) ? trim( $portfolio['excerpt_string_end'] ) : '...';
			$max_words 				= isset( $portfolio['excerpt_max_words'] ) && $portfolio['excerpt_max_words'] != '' ? $portfolio['excerpt_max_words'] : null;
			
			/* If it has the <!--more--> tag */
			if ( trim($portfolio['content_delimiter'])!='' && strpos( $post_content, $portfolio['content_delimiter']) ) {
				$post_content = substr( $post_content, 0, strpos( $post_content, $portfolio['content_delimiter'] ) );
			}
			$post_content = $this->media_boxes_fix_content( $post_content, $max_words, $string_end, $remove_shortcodes, $strip_tags, $except_this_tags );		

			/* 10. Post categories */
			$post_categories 			= isset( $portfolio['post_categories'] ) ? $portfolio['post_categories'] : array();
			$taxonomies 				= get_object_taxonomies( $post_type );
			$post_categories_CSSclass 	= "";
			$show 						= false;
			foreach ($taxonomies as $key => $taxonomy) {
				$post_terms = get_the_terms($post_id, $taxonomy);
				if(is_array($post_terms) && !empty($post_terms)){
					foreach($post_terms as $term){
						
						if(in_array($term->taxonomy."||".$term->term_id, $post_categories)){
							$show = true;
						}
						$post_categories_CSSclass .= " category_".$term->taxonomy."-".$term->term_id;

					}
				}
			}

			if($show == false && count($post_categories)>0){
				continue;
			}

			if($post_type == 'attachment'){
				$post_categories_CSSclass 	= "";

				if(isset($portfolio['custom-media-gallery-items']['attachment'])){
					foreach ($portfolio['custom-media-gallery-items']['attachment'] as $key_attachment => $value_attachment) {
						if($post_id == $key_attachment && isset($value_attachment['categories'])){
							
							foreach ($value_attachment['categories'] as $row_category) {
								$post_categories_CSSclass .= " category_".$row_category;
							}

						}
					}
				}
			}

			/* 11. Post author */
			$post_author 			= get_the_author();

			/* 12. Post name/slug */
			$post_slug 				= get_post_field( 'post_name', get_post() );

			/* 13. Post modified date */
			$post_modified_date 	= date_i18n( get_option( 'date_format' ), get_the_modified_time( 'U' ) );

			/* 14. Post comment number */
			$post_comment_number 	= get_comments_number( $post_id );

			/* 15. Overlay always visible */
			$overlay_always_visible = isset( $portfolio['overlay_always_visible'] ) ? 'overlay-always-visible' : '';

			/* 16. Replace the default overlay effect */
			$overlay_effect 		= $metabox_overlay_effect == 'default' ? '' : "data-overlay-effect='$metabox_overlay_effect'";

			/* Check which skin to use */

			$skin_css_class = '';
			$skin_current 	= array(); 
			if($metabox_skin == 'use_default'){
				$skin_css_class = 'mb_global_skin';
				$skin_current 	= $skin;
			}else{
				$skin_css_class = 'mb_skin_'.$metabox_skin;
				$skin_current 	= $this->get_skin($metabox_skin);

				if(count($skin_current) > 0){ // if the choosen skin exists then use it, if not then use the default
					if(!array_key_exists($metabox_skin, $skin_css_js_extra)){
						$skin_css_js_extra[$metabox_skin] = $skin_current;
					}	
				}else{
					$skin_css_class = 'mb_global_skin';
					$skin_current 	= $skin;	
				}
			}

			/* Content from skin */

			$overlay 				= $this->getContentFromSkin('drop_zone_overlay', $skin_current, $popup_type, $popup_src, $popup_title, $popup_thumb, $iframe_on_grid, $skin_css_class);
			$content 				= $this->getContentFromSkin('drop_zone_content', $skin_current, $popup_type, $popup_src, $popup_title, $popup_thumb, $iframe_on_grid, $skin_css_class);

			/* Replace variables */

			$overlay 				= str_replace('{{post_date}}', $post_date, $overlay);
			$overlay 				= str_replace('{{post_author}}', $post_author, $overlay);
			$overlay 				= str_replace('{{post_id}}', $post_id, $overlay);
			$overlay 				= str_replace('{{post_title}}', $post_title, $overlay);
			$overlay 				= str_replace('{{post_content}}', $post_content, $overlay);
			$overlay 				= str_replace('{{post_link}}', $post_link, $overlay);
			$overlay 				= str_replace('{{post_slug}}', $post_slug, $overlay);
			$overlay 				= str_replace('{{post_modified_date}}', $post_modified_date, $overlay);
			$overlay 				= str_replace('{{post_comment_number}}', $post_comment_number, $overlay);

			$content 				= str_replace('{{post_date}}', $post_date, $content);
			$content 				= str_replace('{{post_author}}', $post_author, $content);
			$content 				= str_replace('{{post_id}}', $post_id, $content);
			$content 				= str_replace('{{post_title}}', $post_title, $content);
			$content 				= str_replace('{{post_content}}', $post_content, $content);
			$content 				= str_replace('{{post_link}}', $post_link, $content);
			$content 				= str_replace('{{post_slug}}', $post_slug, $content);
			$content 				= str_replace('{{post_modified_date}}', $post_modified_date, $content);
			$content 				= str_replace('{{post_comment_number}}', $post_comment_number, $content);

			/* Create HTML */

			$media_boxes_thumbnail 	= "
										<div class='media-box-image $open_popup' $open_popup_extra $link_to_post_page $overlay_effect>
					                		<div $aspect_ratio data-thumbnail='$post_thumbnail_src'></div>
					                
					                		<div class='thumbnail-overlay $overlay_always_visible $skin_css_class'>
					                			$overlay
					                		</div>
					            		</div>
									";

			if($post_thumbnail_src == ""){
				$media_boxes_thumbnail 	= ""; // if there's no featured image then don't add the image
			}

			$sorting_targets = '';
			if(isset($portfolio['sortings'])){
				$sorting_targets = "<div class='media-box-sort-items' style='display: none;'>
										<div class='media-box-sort-date'>$post_date</div>
										<div class='media-box-sort-author'>$post_author</div>
										<div class='media-box-sort-id'>$post_id</div>
										<div class='media-box-sort-title'>$post_title</div>
										<div class='media-box-sort-name'>$post_slug</div>
										<div class='media-box-sort-modified'>$post_modified_date</div>
										<div class='media-box-sort-comment_count'>$post_comment_number</div>
						            </div>";
			}

			$media_boxes .= "    
							<div class='media-box $post_categories_CSSclass' $data_columns>
					            $media_boxes_thumbnail

					            <div class='media-box-content $skin_css_class'>
					            	$content
					            </div>
									
					            $sorting_targets
					        </div>
					     ";
		}
		
		wp_reset_query();

/* ====================================================================== *
      BUILD HTML
 * ====================================================================== */			

		$drop_zone_items = array();	      

	/* ====================================================================== *
	      GET THE FILTERS
	 * ====================================================================== */		

		if(isset($portfolio['filters'])){
			foreach ($portfolio['filters'] as $key => $value) {
				$filter_id 				= $value['filter_id'];
				$filter_all_word 		= $value['filter_all_word'];
				$filter_layout 			= $value['filter_layout'];
                $filter_selected_item 	= $value['filter_selected_item'];
                $selected_all 			= $filter_selected_item=="*"?"class='selected'":"";
                $filter_items 			= isset($value['filter_items'])?$value['filter_items']:array();
                $drop_down_event 		= $portfolio['drop_down_event'];
                $current_filter 		= "";

                $html_items 			= "";

                foreach ($filter_items as $key_inner => $value_inner) {

                	$category_id 		= '';
                	$category_name 		= '';
                	$selected 			= $filter_selected_item==$value_inner?"class='selected'":"";

                	if($post_type == 'attachment'){
                		$category_id 	= $value_inner;
                		$category_name 	= '';

                		if(isset($portfolio['custom-media-gallery-items']['categories'])){
                			foreach ($portfolio['custom-media-gallery-items']['categories'] as $row_category) {
                				if($row_category['id'] == $category_id){
                					$category_name = $row_category['category'];
                				}
                			}
                		}
                	}else{
                		$split 			= explode('||', $value_inner);
	                	$taxonomy 		= $split[0];
	                	$term_id 		= $split[1];
	                	$term 			= get_term( $term_id, $taxonomy );

	                	$category_id 	= "$taxonomy-$term_id";
	                	$category_name 	= $term->name;
                	}

                	$html_items .= "<li><a href='#' $selected data-filter='.category_$category_id'>".$category_name."</a></li>";
                }

				if($filter_layout === 'dropdown'){
					$current_filter = "
						<div class='media-boxes-drop-down' data-event='$drop_down_event'>
			                <div class='media-boxes-drop-down-header'></div>
			                <ul class='media-boxes-drop-down-menu media-boxes-filters-$uniqid' data-id='$key'>
			                  <li><a href='#' $selected_all data-filter='*'>$filter_all_word</a></li>
			                  $html_items
			                </ul>
			            </div>
					";
				}else if($filter_layout === 'inline'){
					$current_filter = "
		                <ul class='media-boxes-filter media-boxes-filters-$uniqid' data-id='$key'>
		                  <li><a href='#' $selected_all data-filter='*'>$filter_all_word</a></li>
		                  $html_items
		                </ul>
					";
				}

				$drop_zone_items["filter_$filter_id"] = $current_filter;
			}
		}

	/* ====================================================================== *
	      GET THE SORT
	 * ====================================================================== */		

		if(isset($portfolio['sortings'])){

			$html_items 			= "";
            foreach ($portfolio['sortings'] as $row) {
            	$order_by 				= $row=='rand' ? 'random' : $row;
            	$order_by_description 	= $portfolio['all_sortings'][$row];
            	$sort_by_text 			= $portfolio['sort_by_text'];
            	$default_sorting_order 	= $portfolio['default_sorting_order'];
            	$default_sorting 		= $portfolio['default_sorting'];
            	$selected 				= $default_sorting==$order_by ? 'selected' : '';
            	$html_items .= "<li><a href='#' data-sort-by='$order_by' class='$selected'>$sort_by_text $order_by_description</a></li>";
            }

			$sort = " 
						<div class='media-boxes-sort'>
							<div class='media-boxes-drop-down media-boxes-sort-$uniqid'>
								<div class='media-boxes-drop-down-header'></div>
								<ul class='media-boxes-drop-down-menu'>
									$html_items
								</ul>
							</div>

							<div class='media-boxes-sort-order'>
								<span class='fa fa-chevron-up ".($default_sorting_order=='ascending'?'selected':'')."' data-sort-ascending='true'></span>
								<span class='fa fa-chevron-down ".($default_sorting_order=='descending'?'selected':'')."' data-sort-ascending='false'></span>
							</div>
						</div>	
					";

			$drop_zone_items["sorting"] = $sort;		
		}

	/* ====================================================================== *
	      GET THE SEARCH
	 * ====================================================================== */		

	    $search_default_text = $portfolio['search_default_text'];

		$search = " 
					<div class='media-boxes-search'>
						<span class='media-boxes-icon fa fa-search'></span>
						<input type='text' class='media-boxes-search-$uniqid' placeholder='$search_default_text'>
						<span class='media-boxes-clear fa fa-close'></span>
					</div>
				";

		$drop_zone_items["search"] = $search;			

	/* ====================================================================== *
	      DROP ZONES
	 * ====================================================================== */	

	 	$drop_zone_1_items	= "";
	 	if(isset($portfolio['drop_zone_1'])){
	 		foreach ($portfolio['drop_zone_1'] as $item_id){
	 			if(isset($drop_zone_items[$item_id])){
	 				$drop_zone_1_items .= $drop_zone_items[$item_id];
	 			}
	 		}
	 	}

	 	$drop_zone_2_items	= "";		
	 	if(isset($portfolio['drop_zone_2'])){
		 	foreach ($portfolio['drop_zone_2'] as $item_id){
		 		if(isset($drop_zone_items[$item_id])){
		 			$drop_zone_2_items .= $drop_zone_items[$item_id];
		 		}
		 	}
		 }

	    $drop_zone_1 		= "<div class='media-boxes-filters-container drop_zone_1'>$drop_zone_1_items</div>";  
		$drop_zone_2 		= "<div class='media-boxes-filters-container drop_zone_2'>$drop_zone_2_items</div>";

	/* ====================================================================== *
	      SKIN CSS AND JS
	 * ====================================================================== */	

		$skin_css 			= $this->get_skin_css($skin, "#media-boxes-container-$uniqid", ".mb_global_skin");
	    $skin_js 			= $this->get_skin_js($skin, ".mb_global_skin");

	    foreach ($skin_css_js_extra as $skin_id => $skin_value) {
	    	$skin_css 		.= "\n" . $this->get_skin_css($skin_value, "#media-boxes-container-$uniqid", ".mb_skin_".$skin_id);
	    	$skin_js 		= array_merge($skin_js, $this->get_skin_js($skin_value, ".mb_skin_".$skin_id));
	    }

	/* ====================================================================== *
	      SOME CSS ADJUSTMENTS
	 * ====================================================================== */	

	 	$some_adjustments_css 	= "<style> \n";

	    $some_adjustments_css	.= " #media-boxes-container-$uniqid .mb_hide_if_empty:empty{";
		$some_adjustments_css		.= " display: none !important; \n";
	    $some_adjustments_css 	.= " } \n";

	    $some_adjustments_css 	.= " </style> \n"; 	      

	/* ====================================================================== *
	      SOME CSS STYLE
	 * ====================================================================== */		

		$load_more_button_css 		= stripslashes( $portfolio['load_more_button_css'] );
		$load_more_button_css 		= str_replace("@media_boxes", "#media-boxes-container-$uniqid", $load_more_button_css);

		$css 						= stripslashes( $portfolio['css'] );
		$css 						= str_replace("@media_boxes", "#media-boxes-container-$uniqid", $css);

		$some_css 					= "
										<style>
											$load_more_button_css
											$css
										</style>
									";

	/* ====================================================================== *
	      GENERATE HTML
	 * ====================================================================== */	

	    $settings 		= $this->media_boxes_js_settings($uniqid, $portfolio, $skin_js);  
		$html 			= " $skin_css $some_adjustments_css $some_css
	    					<div id='media-boxes-container-$uniqid'>
	    						$drop_zone_1 
	    						$drop_zone_2
    							<div id='mediaboxes-grid-$uniqid' class='media-boxes-grid' data-settings='$settings'>
    								$media_boxes
    							</div>
    						</div> ";

    	return $html;

	}

/* ====================================================================== *
      GET SKIN JS
 * ====================================================================== */		

	function get_skin_js($skin, $css_class){
		$skin_css_json 	= json_decode(stripslashes($skin['style_editor_css']));
		$output			= array();

        foreach ($skin_css_json as $item_key => $item) {
	    	foreach ($item as $css_key => $css_value) {
	    		if($css_key == 'animation-on-thumbnail-overlay'){
	    			if($css_value=='from-top' || $css_value=='from-bottom' || $css_value=='from-left' || $css_value=='from-right' || $css_value=='zoom-out' || $css_value=='zoom-in'){

	    				$output[] = [ 'item' => "$css_class.$item_key", 'animation' => $css_value ];

	    			}
	    		}
	    	}
	    }

	    return $output;
	}

/* ====================================================================== *
      GET SKIN CSS
 * ====================================================================== */	

	function get_skin_css($skin, $css_id, $css_class){

	/* ====================================================================== *
	      SKIN CSS STYLE
	 * ====================================================================== */		

	    $skin_css_json 	= json_decode(stripslashes($skin['style_editor_css']));
	    $skin_css 		= "<style> \n";

	    foreach ($skin_css_json as $item_key => $item) {

	    	// IDLE STATE

	    	$skin_css 	.= " $css_id $css_class.$item_key { \n";
	    	foreach ($item as $css_key => $css_value) {
	    		if(strpos($css_key, '-unit') !== false) continue; // don't add unit properties, they are only for visual editor
	    		if($css_value == '' || $css_value == 'px') continue; // don't add rules that are empty
	    		if($css_key == 'custom-css-onhover') continue; // don't add the custom css onhover, that would be added after
	    		if($css_key == 'animation-on-thumbnail-overlay') continue; // don't add animation on thumbnail, they are when thumbnail-overlay gets triggered

	    		if($css_key == 'custom-css'){ // custom css
	    			$skin_css .= $css_value;
	    		}else{ // normal
	    			$skin_css .= " $css_key : $css_value !important; \n";
	    		}
	    	}
	    	$skin_css 	.= " } \n";

	    	// HOVER STATE

	    	$skin_css    .= " $css_id $css_class.$item_key:hover { \n";
	        foreach ($item as $css_key => $css_value) {
	            if($css_key == 'custom-css-onhover'){ // custom css onhover
	                $skin_css .= $css_value;
	            }
	        }
	        $skin_css 	.= "} \n";
	    }

	    $skin_css 		.= " </style> \n";   

	/* ====================================================================== *
	      OVERLAY & CONTENT CSS
	 * ====================================================================== */		      

	    $overlay_content_css 	= "<style> \n";

	    $overlay_content_css	.= " $css_id $css_class.thumbnail-overlay { \n";
	    $overlay_content_css        .= ( !isset($skin['overlay_show']) ? " display : none !important; " : "") ." \n";
		$overlay_content_css		.= " background-color    : ".$skin['overlay_background_color']." !important; \n";
        $overlay_content_css		.= " padding-top         : ".$skin['overlay_padding_top']."px !important; \n";
        $overlay_content_css		.= " padding-right       : ".$skin['overlay_padding_right']."px !important; \n";
        $overlay_content_css		.= " padding-bottom      : ".$skin['overlay_padding_bottom']."px !important; \n";
        $overlay_content_css		.= " padding-left        : ".$skin['overlay_padding_left']."px !important; \n";	
        $overlay_content_css		.= " text-align          : ".$skin['overlay_text_align']." !important; \n";
        $overlay_content_css		.= " vertical-align      : ".$skin['overlay_vertical_align']." !important; \n";
	    $overlay_content_css 	.= " } \n";

	    $overlay_content_css	.= " $css_id $css_class.media-box-content { \n";
	    $overlay_content_css        .= ( !isset($skin['content_show']) ? " display : none !important; " : "") ." \n";
		$overlay_content_css		.= " background-color    : ".$skin['content_background_color']." !important; \n";
        $overlay_content_css		.= " padding-top         : ".$skin['content_padding_top']."px !important; \n";
        $overlay_content_css		.= " padding-right       : ".$skin['content_padding_right']."px !important; \n";
        $overlay_content_css		.= " padding-bottom      : ".$skin['content_padding_bottom']."px !important; \n";
        $overlay_content_css		.= " padding-left        : ".$skin['content_padding_left']."px !important; \n";
        $overlay_content_css		.= " text-align          : ".$skin['content_text_align']." !important; \n";
	    $overlay_content_css 	.= " } \n";

	    $overlay_content_css 	.= " </style> \n";

	    return "$skin_css \n $overlay_content_css";
	}

/* ====================================================================== *
      FIX CONTENT
 * ====================================================================== */	

	function media_boxes_fix_content( $text, $excerpt_max_word=55,  $excerpt_end, $remove_shortcodes, $strip_tags, $except_this_tags ) {
			
		$text = $remove_shortcodes == true ? strip_shortcodes( $text ) : $text; 
		//$text = apply_filters( 'the_content', $text ); // this is for executing the shortcodes, but it causes errors, if you want to execute shortcodes then use do_shortcoe($text)
		$text = str_replace( ']]>', ']]&gt;', $text );
	 	$text = $strip_tags == true ? strip_tags( $text, $except_this_tags ) : $text; 
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); 
		//$excerpt_length = apply_filters('excerpt_length', $excerpt_max_word);

		if($excerpt_max_word!=null && $excerpt_max_word!=''){
			$words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_max_word + 1, PREG_SPLIT_NO_EMPTY );
			if ( count( $words ) > $excerpt_max_word ) {
				array_pop( $words );
				$text = implode( ' ', $words );
				$text = $text . $excerpt_end;
			} else {
				$text = implode( ' ', $words );
			}
		}

		return $text;
	}

/* ====================================================================== *
      JS SETTINGS
 * ====================================================================== */	

	function media_boxes_js_settings($uniqid, $portfolio, $skin_js){

		$settings 											= array();  

		/* Resolutions options */

		$resolutions 										= array();

	    if(isset($portfolio['enable_responsivity'])){
			foreach ($portfolio['resolutions'] as $row) {
				$new_row 									= array();

				$new_row['columnWidth'] 					= 'auto';
				$new_row['maxWidth'] 						= floatval($row['maximum_width']);
				$new_row['columns'] 						= floatval($row['columns']);

				if(isset($portfolio['enable_spacing'])){
					$new_row['horizontalSpaceBetweenBoxes'] = floatval($row['horizontal_space']);
					$new_row['verticalSpaceBetweenBoxes'] 	= floatval($row['vertical_space']);
				}

				$resolutions[] 								= $new_row;
			}
		}

	    /* Grid options */

	    $settings['boxesToLoadStart'] 						= floatval($portfolio['boxes_to_load_start']);
	    $settings['boxesToLoad'] 							= floatval($portfolio['boxes_to_load']);
	    $settings['minBoxesPerFilter'] 						= floatval($portfolio['min_boxes_per_filter']);
	    $settings['lazyLoad'] 								= isset($portfolio['lazy_load']);
	    $settings['horizontalSpaceBetweenBoxes'] 			= floatval($portfolio['horizontal_space']);
	    $settings['verticalSpaceBetweenBoxes'] 				= floatval($portfolio['vertical_space']);
	    $settings['columns'] 								= floatval($portfolio['columns']);
	    $settings['resolutions'] 							= $resolutions;
	    $settings['waitUntilThumbWithRatioLoads'] 			= isset($portfolio['show_loading_progress']);
	    $settings['waitForAllThumbsNoMatterWhat'] 			= isset($portfolio['preload_all_thumbnails']);
	    $settings['LoadingWord'] 		            		= $portfolio['loading_word'];
	    $settings['loadMoreWord'] 		            		= $portfolio['load_more_word'];
	    $settings['noMoreEntriesWord'] 		        		= $portfolio['no_more_entries_word'];

	    /* Overlay options */

	    $settings['thumbnailOverlay'] 						= true; //isset($portfolio['thumbnail_overlay']);
	    $settings['overlayEffect'] 							= $portfolio['overlay_effect'];
	    $settings['overlaySpeed'] 							= floatval($portfolio['overlay_speed']);
	    $settings['overlayEasing'] 							= $portfolio['overlay_easing'];

	    /* Filtering options */

	    $settings['deepLinkingOnFilter'] 		    		= isset($portfolio['deep_linking_filter']);
	    $settings['multipleFilterLogic'] 					= $portfolio['multiple_filter_logic'];
	    $settings['filterContainer'] 						= ".media-boxes-filters-$uniqid";

	    $settings['deepLinkingOnSearch'] 		    		= isset($portfolio['deep_linking_search']);
	    $settings['search'] 		    					= ".media-boxes-search-$uniqid";
	    $settings['searchTarget'] 							= ".media-box-content, .thumbnail-overlay";

	    $settings['sortContainer'] 							= ".media-boxes-sort-$uniqid";

	    /* Popup options */

	    $settings['popup'] 		                    		= $portfolio['popup'];
	    $settings['showOnlyVisibleBoxesInPopup'] 			= isset($portfolio['show_only_loaded_boxes']);
	    $settings['considerFilteringInPopup'] 				= isset($portfolio['consider_filtering']);
	    $settings['deepLinkingOnPopup'] 		    		= isset($portfolio['deep_linking_popup']);

	    /* Magnific Popup options */

	    $settings['magnificpopup']['gallery'] 				= isset($portfolio['mp_enable_gallery']);
	    $settings['magnificpopup']['alignTop'] 				= isset($portfolio['mp_align_top']);
	    $settings['magnificpopup']['preload'] 				= 	array( 
	    															floatval($portfolio['mp_preload_before']), 
	    															floatval($portfolio['mp_preload_after']) 
	    														);

	    /* Fancybox options */

	    $settings['fancybox']['loop'] 						= isset($portfolio['fancyb_loop']);
	    $settings['fancybox']['margin'] 					= $this->strArrayToFloatArray( explode(',', $portfolio['fancyb_margin']) );
	    $settings['fancybox']['keyboard'] 					= isset($portfolio['fancyb_keyboard']);
	    $settings['fancybox']['arrows'] 					= isset($portfolio['fancyb_arrows']);
	    $settings['fancybox']['infobar'] 					= isset($portfolio['fancyb_infobar']);
	    $settings['fancybox']['toolbar'] 					= isset($portfolio['fancyb_toolbar']);
	    
	    $settings['fancybox']['buttons'] 					= 	array(
																	isset($portfolio['fancyb_btn_slideshow']) ? 'slideShow' : '',
																	isset($portfolio['fancyb_btn_fullscreen']) ? 'fullScreen' : '',
																	isset($portfolio['fancyb_btn_thumbs']) ? 'thumbs' : '',
																	isset($portfolio['fancyb_btn_close']) ? 'close' : ''
			    												);

		$settings['fancybox']['idleTime'] 					= floatval($portfolio['fancyb_idle_time']);
        $settings['fancybox']['protect'] 					= isset($portfolio['fancyb_protect']);
        $settings['fancybox']['animationEffect'] 			= $portfolio['fancyb_animation_effect'];
        $settings['fancybox']['animationDuration'] 			= floatval($portfolio['fancyb_animation_duration']);
        $settings['fancybox']['transitionEffect'] 			= $portfolio['fancyb_transition_effect'];
        $settings['fancybox']['transitionDuration'] 		= floatval($portfolio['fancyb_transition_duration']);
        $settings['fancybox']['slideShow'] 					= [ 'autoStart' => isset($portfolio['fancyb_slideshow_autostart']), 'speed' => 40000 ];
        $settings['fancybox']['fullScreen'] 				= [ 'autoStart' => isset($portfolio['fancyb_fullscreen_autostart']) ];
        $settings['fancybox']['thumbs'] 					= [ 'autoStart' => isset($portfolio['fancyb_thumbs_autostart']), 'hideOnClose' => true ];
        $settings['fancybox']['touch'] 						= [ 'vertical' => isset($portfolio['fancyb_touch']), 'momentum' => isset($portfolio['fancyb_touch']) ];

        /* Animation on the items inside the thumbnail-overlay */

        $settings['animation_on_thumbnail_overlay_hover'] 	= $skin_js;

        return htmlspecialchars(json_encode($settings), ENT_QUOTES, 'UTF-8');
	}

/* ====================================================================== *
      STRING ARRAY TO REAL FLOAT ARRAY
 * ====================================================================== */		

	public function strArrayToFloatArray($strArray){
		$floatArray = array();

		foreach ($strArray as $row) {
			$floatArray[] = floatval($row);
		}

		return $floatArray;
	}

/* ====================================================================== *
      GET CONTENT FROM SKIN
 * ====================================================================== */		

    public function getContentFromSkin($drop_zone, $skin, $popup_type, $popup_src, $popup_title, $popup_thumb, $iframe_on_grid, $css_class){
    	$return = "";

    	if(isset($skin[$drop_zone])){
			foreach ($skin[$drop_zone] as $key => $value) {

				$key 				= $value['key'];
				$output 			= $value['output'];
				$icon 				= '';
				$onclick 			= '';
				$open_popup 		= '';
				$open_popup_extra 	= '';
				$hide_if_empty 		= 'mb_hide_if_empty';

				if($value['action'] == 'link_to'){
					$url 			= $value['link_to'] 	== 'post_page' 	? '{{post_link}}' 			: $value['custom_url'];
					$onclickjs 		= $value['link_target'] == '_self' 		? "location.href='$url';" 	: "window.open('$url','_blank');";

					$onclick 		= " onclick=\"$onclickjs\" ";
				}else if($value['action'] == 'open_popup'){
					$open_popup 		= 'mb-open-popup';
					$open_popup_extra 	= " data-type='$popup_type' data-src='$popup_src' data-title='$popup_title' $popup_thumb ";

					if($iframe_on_grid!=''){
						$open_popup = $iframe_on_grid;
					}
				}

				if($value['type'] == 'icon'){
					$icon 			= $output;
					$output 		= '';
					$hide_if_empty 	= '';
				}

				$return .= " <div class='$key $open_popup $icon $hide_if_empty $css_class' $open_popup_extra $onclick>$output</div> ";
			}
		}

		return $return;
    }

}

