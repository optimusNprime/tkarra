<?php

/**
 *
 * Sking editor page
 *
 * @package Media Boxes by castlecode
 * @author  castlecode
 * 
 */
	
	//delete_option( $this->plugin_prefix . '_skins' );

	/* ### GET SKINS FROM DB ### */

	$skins = get_option( MEDIA_BOXES_PREFIX . '_skins' );

	if(is_array($skins) == false){
		$skins = array();
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
		Skin Editor
	</div>

<?php

	/* ====================================================================== *
	      MAIN PAGE (THE PORTFOLIO'S LIST)
	 * ====================================================================== */

	if( $subpage == 'main' ){
?>
		
		<?php 

			/* CLONE A SKIN */

			if( isset($_POST['action']) && $_POST['action'] == 'clone' ){

				$new_skins 					= $skins; /* grab all the skins from the DB */
				$new_uniqid 				= uniqid();
				$new_skins[$new_uniqid] 	= $new_skins[$_POST['uniqid']];

				$new_skins[$new_uniqid]['uniqid'] 			= $new_uniqid;
				$new_skins[$new_uniqid]['name'] 			= $new_skins[$new_uniqid]['name'] . ' clone ' . $new_uniqid;

				update_option ( MEDIA_BOXES_PREFIX . '_skins', $new_skins );

				/* Get skins from db again after we delete one */
				$skins = get_option( MEDIA_BOXES_PREFIX . '_skins' );

				echo '<div id="result" class="updated"><strong>The skin has been successfully cloned</strong></div>';
			}
			
			/* DELTE A SKIN */

			if( isset($_POST['action']) && $_POST['action'] == 'delete' ){

				$new_skins = $skins; /* grab all the skins from the DB */

				unset( $new_skins[$_POST['uniqid']] ); /* delete the chosen skin from the list */
				$subpage = 'main'; /* Send us back to the skin list */

				update_option ( MEDIA_BOXES_PREFIX . '_skins', $new_skins );
				
				/* Get skins from db again after we delete one */
				$skins = get_option( MEDIA_BOXES_PREFIX . '_skins' );

				echo '<div id="result" class="updated"><strong>The skin has been successfully deleted</strong></div>';
			}
		?>

		<p>
			<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=new') ); ?>" class="button-primary blue">
				<span class="fa fa-plus"></span>&nbsp; Create a new skin
			</a>
		</p>

		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="form">
			<input name="action" type="hidden" />
			<input name="uniqid" type="hidden" />

			<table class="widefat media_boxes_table">
				<thead>
					<tr>
						<th>#</th>
						<th>Skin Name</th>
						<th>Actions</th>
					</tr>
				</thead>

				<tbody>
					<?php if( count($skins) == 0 || empty($skins)){ ?>
						<tr>
							<td colspan="3">You don't have any skins yet, <a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=new') ); ?>">create a new one here!</a></td>
						</tr>
					<?php } ?>

					<?php $cont=0; ?>
					<?php foreach ($skins as $key => $value) { ?>
						<?php $cont++; ?>
						<tr>
							<td><?php echo $cont; ?></td>
							<td>
								<strong><?php echo $value['name']; ?></strong>	
							</td>
							<td>
								<div>
									 <span>
										<a class="button-primary blue" href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=edit&uniqid='.$value['uniqid']) ); ?>" title="Edit this skin">
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
									 	<a class="button-primary red" href="javascript:delete_skin('<?php echo $value['uniqid']; ?>');" title="Delete this skin">
									 		<span class="fa fa-trash-o"></span>&nbsp;
									 		Delete
									 	</a>
									 </span>
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
			function delete_skin(uniqid){
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

		/* ### THE DEFAULT STUFF ### */

		$form_version 					= MEDIA_BOXES_VERSION;
		$form_name 						= '';
		$form_drop_zone_overlay 		= array();
		$form_drop_zone_content 		= array();
		$form_style_editor_css 			= '';
		$form_overlay_show 				= 'on';
		$form_overlay_background_color 	= 'rgba(105, 105, 105, 0.85)';
		$form_overlay_padding_top 		= '0';
		$form_overlay_padding_right 	= '0';
		$form_overlay_padding_bottom 	= '0';
		$form_overlay_padding_left 		= '0';
		$form_overlay_text_align 		= 'center';
		$form_overlay_vertical_align 	= 'middle';
		$form_content_show 				= 'on';
		$form_content_background_color 	= '#ffffff';
		$form_content_padding_top 		= '20';
		$form_content_padding_right 	= '20';
		$form_content_padding_bottom 	= '20';
		$form_content_padding_left 		= '20';
		$form_content_text_align 		= 'left';

		if( $subpage == 'edit' ){
			$edit_skins 					= $skins[$uniqid];

			//print_r($edit_skins);
			
			$form_version 					= isset( $edit_portfolio['version'] ) ? $edit_portfolio['version'] : '1';
			$form_name 						= $edit_skins['name'];
			$form_drop_zone_overlay 		= isset($edit_skins['drop_zone_overlay']) ? $edit_skins['drop_zone_overlay'] : array();
			$form_drop_zone_content 		= isset($edit_skins['drop_zone_content']) ? $edit_skins['drop_zone_content'] : array();
			$form_style_editor_css 			= stripslashes($edit_skins['style_editor_css']);
			$form_overlay_show 				= isset( $edit_skins['overlay_show'] ) ? $edit_skins['overlay_show'] : null;
			$form_overlay_background_color 	= $edit_skins['overlay_background_color'];
			$form_overlay_padding_top 		= $edit_skins['overlay_padding_top'];
			$form_overlay_padding_right 	= $edit_skins['overlay_padding_right'];
			$form_overlay_padding_bottom 	= $edit_skins['overlay_padding_bottom'];
			$form_overlay_padding_left 		= $edit_skins['overlay_padding_left'];
			$form_overlay_text_align 		= $edit_skins['overlay_text_align'];
			$form_overlay_vertical_align 	= $edit_skins['overlay_vertical_align'];
			$form_content_show 				= isset( $edit_skins['content_show'] ) ? $edit_skins['content_show'] : null;
			$form_content_background_color 	= $edit_skins['content_background_color'];
			$form_content_padding_top 		= $edit_skins['content_padding_top'];
			$form_content_padding_right 	= $edit_skins['content_padding_right'];
			$form_content_padding_bottom 	= $edit_skins['content_padding_bottom'];
			$form_content_padding_left 		= $edit_skins['content_padding_left'];
			$form_content_text_align 		= $edit_skins['content_text_align'];

		/* FIXES FOR DIFFERENT VERSIONS */	

			// Fixes for v1
			if($form_version == '1'){ 
				$form_version = MEDIA_BOXES_VERSION; // no fixes for v1, just change the version
			}else if($form_version == '1.1'){
				$form_version = MEDIA_BOXES_VERSION; // no fixes for v1.1, just change the version
			}
		}

?>	

		<form method="post" class="media_boxes_options_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<input name="action" type="hidden" value="save_skin_editor_options">
			<input name="uniqid" type="hidden" value="<?php echo $uniqid; ?>" /> <!-- used when you are going to edit a portfolio -->
			<input name="version" type="hidden" value="<?php echo $form_version; ?>">
			<input name="media_boxes_skin" value="yes" type="hidden">

			<div class="media_boxes_tabs">
			  	<ul>
			    	<li><a href="#tabs-1"><span class="fa fa-window-restore"></span> &nbsp; Build Your Template</a></li>
			    	<li><a href="#tabs-2"><span class="fa fa-paint-brush"></span> &nbsp; Apply Some Style</a></li>
			  	</ul>

<!--
	/* ====================================================================== *
	/* ====================================================================== *
	/* ====================================================================== *


	      BUILD YOUR TEMPLATE 


	 * ====================================================================== */	
	 * ====================================================================== */	
	 * ====================================================================== */	
-->			

			  	<div id="tabs-1">

			  		<p>
			  			<div class="title">Skin Name</div>
						<input name="name" type="text" value="<?php echo esc_attr($form_name); ?>" title="This is used only in the skin settings, just for identification" />
			  		</p>

					<div class="grid">
						<div class="col-400">

						<!--
							/* ====================================================================== *
							      AVAILABLE ITEMS TO DRAG
							 * ====================================================================== */	
						-->
							
							<?php 
					  			$available_items = array(
					  				"post_date" 			=> array( "type" => "variable", 	"name" => "Post Date", 				"output" => "{{post_date}}" ),
					  				"post_author" 			=> array( "type" => "variable", 	"name" => "Post Author", 			"output" => "{{post_author}}" ),
					  				"post_id" 				=> array( "type" => "variable", 	"name" => "Post ID", 				"output" => "{{post_id}}" ),
					  				"post_title" 			=> array( "type" => "variable", 	"name" => "Post Title", 			"output" => "{{post_title}}" ),
					  				"post_content" 			=> array( "type" => "variable", 	"name" => "Post Content", 			"output" => "{{post_content}}" ),
					  				"post_link" 			=> array( "type" => "variable", 	"name" => "Post Link", 				"output" => "{{post_link}}" ),
					  				"post_slug" 			=> array( "type" => "variable", 	"name" => "Post Slug", 				"output" => "{{post_slug}}" ),
					  				"post_modified_date" 	=> array( "type" => "variable", 	"name" => "Post Modified Date", 	"output" => "{{post_modified_date}}" ),
					  				"post_comment_number" 	=> array( "type" => "variable", 	"name" => "Post Comment Number", 	"output" => "{{post_comment_number}}" ),
					  			);

					  			$html_items = array(
					  				"icon" 					=> array( "type" => "icon", 		"name" => "Icon", 					"output" => "fa-search" ),
					  				"text" 					=> array( "type" => "text", 		"name" => "Text", 					"output" => "Some Text" ),
					  			);
					  		?>

							<div class="skin_container">

								<div class="title">Available Items</div>

								<div class="skin_draggable_ui">
									<?php foreach ($available_items as $key => $row) { ?>
										<div class="skin_item">
											<?php echo $row['name']; ?>

											<div class="skin_item_controls">
												<span title="Edit Action" class="button-primary mini fa fa-link edit_action_skin_item"></span>
												<span title="Delete Element" class="button-primary red mini fa fa-trash-o"></span>
											</div> 

											<input type="hidden" data-name="drop_zone[][key]" class="key" value="<?php echo $key; ?>"> <!-- it will increment if its dragged multiple times -->
											<input type="hidden" data-name="drop_zone[][name]" class="name" value="<?php echo $row['name']; ?>">
											<input type="hidden" data-name="drop_zone[][output]" class="output" value="<?php echo $row['output']; ?>">
											<input type="hidden" data-name="drop_zone[][type]" class="type" value="<?php echo $row['type']; ?>">

											<input type="hidden" data-name="drop_zone[][action]" class="action" value="nothing">
											<input type="hidden" data-name="drop_zone[][link_target]" class="link_target" value="_self">
											<input type="hidden" data-name="drop_zone[][link_to]" class="link_to" value="post_page">
											<input type="hidden" data-name="drop_zone[][custom_url]" class="custom_url" value="">
										</div>
				   					<?php } ?>
			   					</div>

			   					<br>

			   					<div class="title">Extra Items</div>

								<div class="skin_draggable_ui">
									<?php foreach ($html_items as $key => $row) { ?>
										<div class="skin_item">
											<?php echo $row['name']; ?>
											<?php if($row['type'] == 'icon'){ ?>
												<span class="skin_item_icon">
													<span class="fa <?php echo $row['output']; ?>"></span>
												</span>
											<?php } ?>

											<div class="skin_item_controls">
												<?php if($row['type'] == 'icon'){ ?>
													<span title="Edit Icon" class="button-primary mini fa fa-pencil edit_icon_skin_item"></span>
												<?php } ?>
												<?php if($row['type'] == 'text'){ ?>
													<span title="Edit Text" class="button-primary mini fa fa-pencil edit_text_skin_item"></span>
												<?php } ?>
												<span title="Edit Action" class="button-primary mini fa fa-link edit_action_skin_item"></span>
												<span title="Delete Element" class="button-primary red mini fa fa-trash-o"></span>
											</div> 

											<input type="hidden" data-name="drop_zone[][key]" class="key" value="<?php echo $key; ?>"> <!-- it will increment if its dragged multiple times -->
											<input type="hidden" data-name="drop_zone[][name]" class="name" value="<?php echo $row['name']; ?>">
											<input type="hidden" data-name="drop_zone[][output]" class="output" value="<?php echo $row['output']; ?>">
											<input type="hidden" data-name="drop_zone[][type]" class="type" value="<?php echo $row['type']; ?>">

											<input type="hidden" data-name="drop_zone[][action]" class="action" value="nothing">
											<input type="hidden" data-name="drop_zone[][link_target]" class="link_target" value="_self">
											<input type="hidden" data-name="drop_zone[][link_to]" class="link_to" value="post_page">
											<input type="hidden" data-name="drop_zone[][custom_url]" class="custom_url" value="">
										</div>
				   					<?php } ?>
			   					</div>

							</div>

						</div>
						<div class="col-30">&nbsp;</div>
						<div class="col-400">

						<!--
							/* ====================================================================== *
							      DRAG ITEMS TO THE OVERLAY OR TO THE CONTENT AREA
							 * ====================================================================== */	
						-->	

							<div class="skin_container">
																
								<div class="title">Thumbnail Overlay</div>
								
								<div class="drop_zone_overlay skin_sortable_ui" data-drop_zone_id="drop_zone_overlay">
									
									<span class="drop_zone_placeholder_skin">DROP ZONE</span>

									<?php foreach ($form_drop_zone_overlay as $row) { ?>
										<div class="skin_item">
											<?php echo $row['name']; ?>
											<?php if($row['type'] == 'icon'){ ?>
												<span class="skin_item_icon">
													<span class="fa <?php echo $row['output']; ?>"></span>
												</span>
											<?php } ?>

											<div class="skin_item_controls">
												<?php if($row['type'] == 'icon'){ ?>
													<span title="Edit Icon" class="button-primary mini fa fa-pencil edit_icon_skin_item"></span>
												<?php } ?>
												<?php if($row['type'] == 'text'){ ?>
													<span title="Edit Text" class="button-primary mini fa fa-pencil edit_text_skin_item"></span>
												<?php } ?>
												<span title="Edit Action" class="button-primary mini fa fa-link edit_action_skin_item"></span>
												<span title="Delete Element" class="button-primary red mini fa fa-trash-o"></span>
											</div> 

											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][key]" class="key" value="<?php echo esc_attr($row['key']); ?>">
											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][name]" class="name" value="<?php echo esc_attr($row['name']); ?>">
											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][output]" class="output" value="<?php echo esc_attr($row['output']); ?>">
											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][type]" class="type" value="<?php echo esc_attr($row['type']); ?>">

											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][action]" class="action" value="<?php echo esc_attr($row['action']); ?>">
											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][link_target]" class="link_target" value="<?php echo esc_attr($row['link_target']); ?>">
											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][link_to]" class="link_to" value="<?php echo esc_attr($row['link_to']); ?>">
											<input type="hidden" name="drop_zone_overlay[<?php echo $row['key']; ?>][custom_url]" class="custom_url" value="<?php echo esc_attr($row['custom_url']); ?>">
										</div>
				   					<?php } ?>
								</div>

								<div class="title">Content</div>
								
								<div class="drop_zone_overlay skin_sortable_ui" data-drop_zone_id="drop_zone_content">

									<span class="drop_zone_placeholder_skin">DROP ZONE</span>
	
									<?php foreach ($form_drop_zone_content as $row) { ?>
										<div class="skin_item">
											<?php echo $row['name']; ?>
											<?php if($row['type'] == 'icon'){ ?>
												<span class="skin_item_icon">
													<span class="fa <?php echo $row['output']; ?>"></span>
												</span>
											<?php } ?>

											<div class="skin_item_controls">
												<?php if($row['type'] == 'icon'){ ?>
													<span title="Edit Icon" class="button-primary mini fa fa-pencil edit_icon_skin_item"></span>
												<?php } ?>
												<?php if($row['type'] == 'text'){ ?>
													<span title="Edit Text" class="button-primary mini fa fa-pencil edit_text_skin_item"></span>
												<?php } ?>
												<span title="Edit Action" class="button-primary mini fa fa-link edit_action_skin_item"></span>
												<span title="Delete Element" class="button-primary red mini fa fa-trash-o"></span>
											</div> 

											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][key]" class="key" value="<?php echo esc_attr($row['key']); ?>">
											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][name]" class="name" value="<?php echo esc_attr($row['name']); ?>">
											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][output]" class="output" value="<?php echo esc_attr($row['output']); ?>">
											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][type]" class="type" value="<?php echo esc_attr($row['type']); ?>">

											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][action]" class="action" value="<?php echo esc_attr($row['action']); ?>">
											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][link_target]" class="link_target" value="<?php echo esc_attr($row['link_target']); ?>">
											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][link_to]" class="link_to" value="<?php echo esc_attr($row['link_to']); ?>">
											<input type="hidden" name="drop_zone_content[<?php echo $row['key']; ?>][custom_url]" class="custom_url" value="<?php echo esc_attr($row['custom_url']); ?>">
										</div>
				   					<?php } ?>
								</div>

							</div>

						</div>
					</div>

					<!--
						/* ====================================================================== *
						      ACTION SKIN ITEM
						 * ====================================================================== */	
					-->

					<div class="action_skin_item">
						<div class="media_boxes_admin">
							<p>
								<label for="">Action</label>
								<select class="action">
									<option value="nothing">Nothing</option>
									<option value="link_to">Link to</option>
									<option value="open_popup">Open popup/lightbox</option>
								</select>
							</p>

							<p class="link_to">
								<label for="">Link Target</label>
								<select class="link_target">
									<option value="_self">_self</option>
									<option value="_blank">_blank</option>
								</select>
							</p>

							<p class="link_to">
								<label for="">Link to</label>
								<select class="link_to">
									<option value="post_page">Post Page</option>
									<option value="custom_url">Custom url</option>
								</select>
							</p>

							<p class="custom_url">
								<label for="">Custom url</label>
								<input class="custom_url" type="text" value="" />
							</p>

							<br>

							<div class="form-controls">
								<button class="button-primary save_action_skin_item"><span class="fa fa-check"></span> &nbsp;OK</button>
							</div>
						</div>
					</div>

					<!--
						/* ====================================================================== *
						      ICON SKIN ITEM
						 * ====================================================================== */	
					-->

					<div class="icon_skin_item">
						
						<div class="icons_container">
							<?php require_once( MEDIA_BOXES_DIR . 'admin/skin_editor_icons.php' ); ?>
						</div>	

					</div>

					<!--
						/* ====================================================================== *
						      TEXT SKIN ITEM
						 * ====================================================================== */	
					-->

					<div class="text_skin_item">
						
						<textarea class="text" style="width: 100%;" rows="4"></textarea>
							
						<br>
							
						<div class="form-controls">
							<button class="button-primary save_text_skin_item"><span class="fa fa-check"></span> &nbsp;OK</button>
						</div>

					</div>

					<!--
						/* ====================================================================== *
						      SAVE
						 * ====================================================================== */	
					-->
					
					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>

			  	</div>

<!--
	/* ====================================================================== *
	/* ====================================================================== *
	/* ====================================================================== *


	      APPLY SOME STYLE (CSS)


	 * ====================================================================== */	
	 * ====================================================================== */	
	 * ====================================================================== */	
-->

			  	<div id="tabs-2">

			  		<!--
						/* ====================================================================== *
						      STYLE EDITOR
						 * ====================================================================== */	
					-->
			    
			    	<div class="style_editor_container">
						
						<!--
						<div class="states_container">
					  		<a class="active">Idle</a>
					  		<a><input type="checkbox"> Hover</a>
					  	</div>
					  -->

						<div class="style_editor">
							<ul>
								<li><a href="#tab_1"><span class="fa fa-font"></span> Text</a></li>
								<li><a href="#tab_2"><span class="fa fa-arrows-alt"></span> Position</a></li>								
								<li><a href="#tab_3"><span class="fa fa-bars"></span> Border</a></li>
								<li><a href="#tab_4"><span class="fa fa-file-image-o"></span> Background</a></li>
								<li><a href="#tab_5"><span class="fa fa-paint-brush"></span> Custom</a></li>
								<li><a href="#tab_6"><span class="fa fa-window-restore"></span> Overlay &<br>Content</a></li>
							</ul>
						  	<div>

							<!--
								/* ====================================================================== *
								      TEXT
								 * ====================================================================== */	
							-->

							  	<div id="tab_1">
								    <p>
										<label for="">Font Size</label>
										<span class="slider slider-theme-editor" data-max="99" data-min="1" aria-disabled="false"></span>
										<input class="font_size" type="text" value="15" title="Font size in pixels">
										px
									</p>

									<p>
										<label for="">Line Height</label>
										<span class="slider slider-theme-editor" data-max="99" data-min="1" aria-disabled="false"></span>
										<input class="line_height" type="text" value="15" title="Line height in pixels">
										px
									</p>

									<p>
										<label for="">Font Color</label>
										<span style="display: inline-block;">
											<input class="font_color colorpicker-input" type="text" title="Font color">
										</span>
									</p>

									<p>
										<label for="">Font Familiy</label>
										<input class="font_family" type="text" title="Font Familiy, i.e. Arial, Helvetica, sans-serif">
									</p>

									<p>
										<label for="">Font Weight</label>
										<select class="font_weight" title="The font weight">
											<option value="100">100</option>
											<option value="200">200</option>
											<option value="300">300</option>
											<option value="400">400</option>
											<option value="500">500</option>
											<option value="600">600</option>
											<option value="700">700</option>
											<option value="800">800</option>
											<option value="900">900</option>
										</select>
									</p>

									<p>
										<label for="">Text Decoration</label>
										<select class="text_decoration" title="The text decoration">
											<option value="none">None</option>
											<option value="underline">Underline</option>
											<option value="overline">Overline</option>
											<option value="line-through">Line Through</option>
										</select>
									</p>

									<p>
										<label for="">Font Style</label>
										<select class="font_style" title="The text decoration">
											<option value="normal">Normal</option>
											<option value="italic">Italic</option>
										</select>
									</p>

									<p>
										<label for="">Text Transform</label>
										<select class="text_transform" title="The text decoration">
											<option value="none">None</option>
											<option value="capitalize">Capitalize</option>
											<option value="uppercase">Uppercase</option>
											<option value="lowercase">Lowercase</option>
										</select>
									</p>

									<p>
										<label for="">Text Alignment</label>
										<select class="text_align" title="The text alignment">
											<option value="inherit">Inherit</option>
											<option value="center">Center</option>
											<option value="left">Left</option>
											<option value="right">Right</option>
											<option value="justify">Justify</option>
										</select>
									</p>

									<p>
										<label for="">Letter Spacing</label>
										<span class="slider slider-theme-editor" data-max="99" data-min="1" aria-disabled="false"></span>
										<input class="letter_spacing" type="text" title="Space between letters">
										px
									</p>

									<p>
										<label for="">Word Spacing</label>
										<span class="slider slider-theme-editor" data-max="99" data-min="1" aria-disabled="false"></span>
										<input class="word_spacing" type="text" title="Space between words">
										px
									</p>
							  	</div>

							<!--
								/* ====================================================================== *
								      POSITION
								 * ====================================================================== */	
							-->

							  	<div id="tab_2">
							    	<p>
										<label for="">Position</label>
										<select class="position" title="The position of the element">
											<option value="relative">Relative</option>
											<option value="absolute">Absolute</option>
										</select>
									</p>

									<p>
										<label for="">Positions</label>
										<input class="top small_input tooltip_at_top" type="text" title="Top">
										<input class="right small_input tooltip_at_top" type="text" title="Right">
										<input class="bottom small_input tooltip_at_top" type="text" title="Bottom">
										<input class="left small_input tooltip_at_top" type="text" title="Left">
										<select class="position_unit" style="margin-top: -3px;">
											<option value="px">px</option>
											<option value="%">%</option>
										</select>
									</p>

									<p>
										<label for="">Width</label>
										<input class="width small_input" type="text" title="Element width">
										<select class="width_unit" style="margin-top: -3px;">
											<option value="px">px</option>
											<option value="%">%</option>
										</select>
									</p>

									<p>
										<label for="">Height</label>
										<input class="height small_input" type="text" title="Element height">
										<select class="height_unit" style="margin-top: -3px;">
											<option value="px">px</option>
											<option value="%">%</option>
										</select>
									</p>

									<p>
										<label for="">Display</label>
										<select class="display" title="The kind of display">
											<option value="block">Block</option>
											<option value="inline">Inline</option>
											<option value="inline-block">Inline-Block</option>
										</select>
									</p>

									<p>
										<label for="">Overflow</label>
										<select class="overflow" title="The overflow">
											<option value="visible">Visible</option>
											<option value="hidden">Hidden</option>
										</select>
									</p>

									<p>
										<label for="">Float</label>
										<select class="float" title="Float element">
											<option value="none">none</option>
											<option value="right">Right</option>
											<option value="left">Left</option>
										</select>
									</p>

									<p>
										<label for="">Clear</label>
										<select class="clear" title="Clear element">
											<option value="none">none</option>
											<option value="right">Right</option>
											<option value="left">Left</option>
											<option value="both">Both</option>
										</select>
									</p>

									<p>
										<label for="">Margin</label>
										<input class="margin_top small_input tooltip_at_top" type="text" title="Top">
										<input class="margin_right small_input tooltip_at_top" type="text" title="Right">
										<input class="margin_bottom small_input tooltip_at_top" type="text" title="Bottom">
										<input class="margin_left small_input tooltip_at_top" type="text" title="Left">
										px
									</p>

									<p>
										<label for="">Padding</label>
										<input class="padding_top small_input tooltip_at_top" type="text" title="Top">
										<input class="padding_right small_input tooltip_at_top" type="text" title="Right">
										<input class="padding_bottom small_input tooltip_at_top" type="text" title="Bottom">
										<input class="padding_left small_input tooltip_at_top" type="text" title="Left">
										px
									</p>
							  	</div>

							<!--
								/* ====================================================================== *
								      BORDER
								 * ====================================================================== */	
							-->

							  	<div id="tab_3">
							    	<p>
										<label for="">Border Width</label>
										<input class="border_top_width small_input tooltip_at_top" type="text" title="Top">
										<input class="border_right_width small_input tooltip_at_top" type="text" title="Right">
										<input class="border_bottom_width small_input tooltip_at_top" type="text" title="Bottom">
										<input class="border_left_width small_input tooltip_at_top" type="text" title="Left">
										px
									</p>

									<p>
										<label for="">Border Radius</label>
										<input class="border_top_left_radius small_input tooltip_at_top" type="text" title="Top Left">
										<input class="border_top_right_radius small_input tooltip_at_top" type="text" title="Top Right">
										<input class="border_bottom_left_radius small_input tooltip_at_top" type="text" title="Bottom Left">
										<input class="border_bottom_right_radius small_input tooltip_at_top" type="text" title="Bottom Right">
										px
									</p>

									<p>
										<label for="">Border Color</label>
										<span style="display: inline-block;">
											<input class="border_color colorpicker-input" type="text" title="Border color">
										</span>
									</p>

							    	<p>
										<label for="">Border Style</label>
										<select class="border_style" title="Border style">
											<option value="none">none</option>
											<option value="solid">Solid</option>
											<option value="dotted">Dotted</option>
											<option value="dashed">Dashed</option>
											<option value="double">Double</option>
										</select>
									</p>
							  	</div>

							<!--
								/* ====================================================================== *
								      BACKGROUND
								 * ====================================================================== */	
							-->

								<div id="tab_4">
									<p>
										<label for="">Background Color</label>
										<span style="display: inline-block;">
											<input class="background_color colorpicker-input" data-alpha="true" type="text" title="Background color">
										</span>
									</p>
								</div>		

							<!--
								/* ====================================================================== *
								      CUSTOM CSS
								 * ====================================================================== */	
							-->

								<div id="tab_5">
									<p>
										<label style="vertical-align: top;" for="">Animation on thumbnail overlay hover</label>
										<select class="animation_on_thumbnail_overlay" title="Animate the current item when the thumbnail-overlay gets triggered">
											<option value="none">none</option>
											<option value="from-top">From Top</option>
											<option value="from-bottom">From Bottom</option>
											<option value="from-left">From Left</option>
											<option value="from-right">From Right</option>
											<option value="zoom-out">Zoom Out</option>
											<option value="zoom-in">Zoom In</option>
										</select>
									</p>
									<p>
										<label style="vertical-align: top;" for="">Custom CSS</label>
										<textarea class="custom_css" title="Custom CSS" rows="5" style="width: 240px;"></textarea>
									</p>
									<p>
										<label style="vertical-align: top;" for="">Custom CSS on hover</label>
										<textarea class="custom_css_onhover" title="Custom CSS" rows="5" style="width: 240px;"></textarea>
									</p>
									<p style="margin-left: 175px !important;">
										Here you can add your custom CSS! 
										<br>
										Don't forget to add <strong>!important</strong>
										<br>
										<br>
										For example:
										<br>
										<strong>
											border-radius: 20px !important; 
											<br>
											border: 1px solid red !important;
										</strong>
									</p>
								</div>		

							<!--
								/* ====================================================================== *
								      OVERLAY & CONTENT
								 * ====================================================================== */	
							-->

								<div id="tab_6">
									<div class="section_title">
										<input type="checkbox" name="overlay_show" <?php echo isset( $form_overlay_show ) ? 'value="1" checked="checked"' : ''; ?> class="overlay_show overlay_and_content_css tooltip_at_top" title="If you would like to show the thumbnail overlay" />
										Thumbnail Overlay
									</div>
									<p>
										<label for="">Background Color</label>
										<span style="display: inline-block;">
											<input name="overlay_background_color" value="<?php echo esc_attr($form_overlay_background_color); ?>" class="overlay_background_color overlay_and_content_css colorpicker-input" data-alpha="true" type="text" title="Background color">
										</span>
									</p>
									<p>
										<label for="">Padding</label>
										<input name="overlay_padding_top" value="<?php echo esc_attr($form_overlay_padding_top); ?>" class="overlay_padding_top overlay_and_content_css small_input tooltip_at_top" type="text" title="Top">
										<input name="overlay_padding_right" value="<?php echo esc_attr($form_overlay_padding_right); ?>" class="overlay_padding_right overlay_and_content_css small_input tooltip_at_top" type="text" title="Right">
										<input name="overlay_padding_bottom" value="<?php echo esc_attr($form_overlay_padding_bottom); ?>" class="overlay_padding_bottom overlay_and_content_css small_input tooltip_at_top" type="text" title="Bottom">
										<input name="overlay_padding_left" value="<?php echo esc_attr($form_overlay_padding_left); ?>" class="overlay_padding_left overlay_and_content_css small_input tooltip_at_top" type="text" title="Left">
										px
									</p>
									<p>
										<label for="">Text Alignment</label>
										<select name="overlay_text_align" class="overlay_text_align overlay_and_content_css" title="The text alignment">
											<option value="left" <?php echo $form_overlay_text_align == 'left' ? 'selected' : ''; ?>>Left</option>
											<option value="center" <?php echo $form_overlay_text_align == 'center' ? 'selected' : ''; ?>>Center</option>
											<option value="right" <?php echo $form_overlay_text_align == 'right' ? 'selected' : ''; ?>>Right</option>
											<option value="justify" <?php echo $form_overlay_text_align == 'justify' ? 'selected' : ''; ?>>Justify</option>
										</select>
									</p>
									<p>
										<label for="">Vertical Alignment</label>
										<select name="overlay_vertical_align" class="overlay_vertical_align overlay_and_content_css" title="The vertical alignment">
											<option value="top" <?php echo $form_overlay_vertical_align == 'top' ? 'selected' : ''; ?>>Top</option>
											<option value="middle" <?php echo $form_overlay_vertical_align == 'middle' ? 'selected' : ''; ?>>Middle</option>
											<option value="bottom" <?php echo $form_overlay_vertical_align == 'bottom' ? 'selected' : ''; ?>>Bottom</option>
										</select>
									</p>

									<div class="section_title">
										<input type="checkbox" name="content_show" <?php echo isset( $form_content_show ) ? 'value="1" checked="checked"' : ''; ?> class="content_show overlay_and_content_css tooltip_at_top" title="If you would like to show the content" />
										Content
									</div>
									<p>
										<label for="">Background Color</label>
										<span style="display: inline-block;">
											<input name="content_background_color" value="<?php echo esc_attr($form_content_background_color); ?>" class="content_background_color overlay_and_content_css colorpicker-input" data-alpha="true" type="text" title="Background color">
										</span>
									</p>
									<p>
										<label for="">Padding</label>
										<input name="content_padding_top" value="<?php echo esc_attr($form_content_padding_top); ?>" class="content_padding_top overlay_and_content_css small_input tooltip_at_top" type="text" title="Top">
										<input name="content_padding_right" value="<?php echo esc_attr($form_content_padding_right); ?>" class="content_padding_right overlay_and_content_css small_input tooltip_at_top" type="text" title="Right">
										<input name="content_padding_bottom" value="<?php echo esc_attr($form_content_padding_bottom); ?>" class="content_padding_bottom overlay_and_content_css small_input tooltip_at_top" type="text" title="Bottom">
										<input name="content_padding_left" value="<?php echo esc_attr($form_content_padding_left); ?>" class="content_padding_left overlay_and_content_css small_input tooltip_at_top" type="text" title="Left">
										px
									</p>
									<p>
										<label for="">Text Alignment</label>
										<select name="content_text_align" class="content_text_align overlay_and_content_css" title="The text alignment">
											<option value="left" <?php echo $form_content_text_align == 'left' ? 'selected' : ''; ?>>Left</option>
											<option value="center" <?php echo $form_content_text_align == 'center' ? 'selected' : ''; ?>>Center</option>
											<option value="right" <?php echo $form_content_text_align == 'right' ? 'selected' : ''; ?>>Right</option>
											<option value="justify" <?php echo $form_content_text_align == 'justify' ? 'selected' : ''; ?>>Justify</option>
										</select>
									</p>
								</div>		

							</div>
						</div> <!-- end style editor -->

						<textarea class="style_editor_css" name="style_editor_css" id="" cols="30" rows="10" style="display: none;"><?php echo $form_style_editor_css; ?></textarea>

					</div>	<!-- end style editor container -->

					<!--
						/* ====================================================================== *
						      PREVIEW
						 * ====================================================================== */	
					-->

					<div class="style_editor_preview_container">

						<div class="style_editor_preview_select">
							<select id="style_editor_preview_select">
				    			<option value="">Post Name</option>
				    		</select>
						</div>

						<div class="style_editor_preview">

							<div id="style_editor_preview">
					            <div class="media-box">
					                <div class="media-box-image">
					                    <div data-thumbnail="<?php echo MEDIA_BOXES_URI; ?>admin/includes/images/Blur.jpg" data-width="752" data-height="500"></div>
					                    <div data-popup="<?php echo MEDIA_BOXES_URI; ?>admin/includes/images/Blur.jpg" ></div>
					                    
					                    <div class="thumbnail-overlay"></div>
					                </div>
					                 <div class="media-box-content"></div>
					            </div>
					        </div>

				    	</div> <!-- end style editor preview -->   

				    </div>	<!-- end style editor preview container -->
					
					<br><br>

					<!--
						/* ====================================================================== *
						      SAVE
						 * ====================================================================== */	
					-->
					
					<div class="form-controls">
						<button class="button-primary green"><span class="fa fa-check"></span> &nbsp;Save</button>
						<a href="<?php echo esc_attr( admin_url('admin.php?page='.$_GET['page'].'&subpage=main') ); ?>" class="button-primary gray">
							<span class="fa fa-times"></span> &nbsp;Close
						</a>
					</div>    

			  	</div> <!-- end tab from skin editor -->
			</div> <!-- end tabs container -->
		</form>
<?php 
	}
?>


</div>	