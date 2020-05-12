    
/* ======================================================= 
 *
 *  	Media Boxes Admin
 *  	Version: 1.0
 *  	By David Blanco
 *
 *  	Contact: http://codecanyon.net/user/castlecode
 *  	Created: July 20, 2014
 *
 *  	Copyright (c) 2013, David Blanco. All rights reserved.
 *  	Available only in http://codecanyon.net/
 *      
 * ======================================================= */

(function($){
	$( document ).ready(function() {	

	/* ====================================================================== *
      		SLIDER
 	 * ====================================================================== */

 		$( '.slider' ).each(function(){
 			var $this = $(this);
 			var input = $this.siblings('input');

 			$this.slider({
				value: input.val(),
				min: $this.data('min'),
				max: $this.data('max'),
				step: 1,
				range: "min",
				slide: function( event, ui ) {
					input.val( ui.value );
				},
			});

			input.keyup(function() {
			    $this.slider( "value", input.val() );
			});

 		});	

	/* ====================================================================== *
	      	SHORTCODE FUNCTIONALITY
	 * ====================================================================== */

 		$('.shortcode_id').on('keyup', function(){
			$this = $(this);

			$('.shortcode').val( '[media_boxes id="'+$this.val()+'"]' );
		});

 		$('.shortcode_id').trigger('keyup');	

 	/* ====================================================================== *
	      	CLEAR POST CATEGORIES
	 * ====================================================================== */

 		$('body').on('click', '.clear_all_select_post_categories', function(e){
 			e.preventDefault();
 			$(this).siblings('select.select_post_categories').find('option:selected').prop("selected", false).trigger('click');
 		})

 		$('body').on('click', '.select_all_select_post_categories', function(e){
 			e.preventDefault();
 			$(this).siblings('select.select_post_categories').find('option:not([disabled])').prop("selected", true).trigger('click');
 		})

	/* ====================================================================== *
      		ENABLE RESOLUTIONS
 	 * ====================================================================== */

		function enable_responsivity(){
			if($('*[name="enable_responsivity"]').is(':checked')){
				$('.all_resolutions').fadeIn(300);
			}else{
				$('.all_resolutions').fadeOut(300);
			}
		}
		enable_responsivity();

		$('*[name="enable_responsivity"]').on('click', function(){
			enable_responsivity();
		});

	/* ====================================================================== *
      		ENABLE SPACING FOR DIFFERENT RESOLUTIONS
 	 * ====================================================================== */

		function enable_spacing(){
			if($('*[name="enable_spacing"]').is(':checked')){
				$('.spacing_for_resolutions').fadeIn(300);
			}else{
				$('.spacing_for_resolutions').fadeOut(300);
			}
		}
		enable_spacing();

		$('*[name="enable_spacing"]').on('click', function(){
			enable_spacing();
		});

	/* ====================================================================== *
      		SHOW LOAD MORE BUTTON SOURCE CODE
 	 * ====================================================================== */

		$('.show_load_more_button_css').on('click', function(e){
			e.preventDefault();

			$(this).siblings('.load_more_button_css').fadeToggle();
		});

	/* ====================================================================== *
      		SHOW / HIDE THE POST CATEGORIES ACCORDING TO THE POST TYPE
 	 * ====================================================================== */

 		function changePostType(postType, isHuman){
 			$('.post_categories').hide().insertAfter( $('.media_boxes_options_form') ); // move them outside so they are not visible for the JS later on

 			$('.post_categories').filter('.post-category-'+postType).show().prependTo( $('.query-options') ); // only put back the correct one

 			if( isHuman ){
 				updateAllPostCategoriesFromFilters();
 			}
 		}

	/* ====================================================================== *
      		FILTERS
 	 * ====================================================================== */

 		$('.filter_group_container').on('click', '.remove_filter', function(e){
 			e.preventDefault();

 			if(confirm("Are you sure? this action can't be undone")){
 				$(this).parents('.filter_group').remove();

 				// Remove it from the layout GUI
				remove_sortable_ui_item( $(this).parents('.filter_group').find('.filterId').val() );

 			}
 		});

 		$('.add_new_filter').on('click', function(e){
 			e.preventDefault();

 			/* GET THE MAX FILTER_ID + 1 */
 			var filterId = 0;
 			$('.filter_group_container').find('.filter_group').each(function(){
 				
 				var current_filter_id = parseInt( $(this).find('.filterId').val() );
 				if(current_filter_id > filterId){
 					filterId = current_filter_id;
 				}

 			});
 			filterId++;

 			var newFilter = $(''+
	 			'<div class="filter_group">'+
					'<input class="filterId" type="hidden" name="filters[filter_'+filterId+'][filter_id]" value="'+filterId+'">'+
					'<div class="filter_title">Filter Group '+filterId+' <a class="remove_filter" href="#"><i class="fa fa-times"></i></a></div>'+
					'<div class="filter_option">'+
						'<label for="">"All" word</label>'+
						'<input type="text" name="filters[filter_'+filterId+'][filter_all_word]" title="The All word in the filter" value="All" />'+
					'</div>'+
					'<div class="filter_option">'+
						'<label for="">Layout</label>'+
						'<select name="filters[filter_'+filterId+'][filter_layout]" title="The layout of the filter">'+
							'<option value="inline">In Line</option>'+
							'<option value="dropdown">Dropdown</option>'+
						'</select>'+
					'</div>'+
					'<div class="filter_option">'+
						'<label for="">Default selected filter item</label>'+
						'<select class="selected_item" name="filters[filter_'+filterId+'][filter_selected_item]" title="The selected filter item by default">'+
							getCategoriesOptions()+
						'</select>'+
					'</div>'+
					'<div class="filter_option">'+
						'<label for="">Available filter items</label>'+
						'<div class="sort_filter_items">'+
							getCategoriesItems(filterId)+
						'</div>'+
					'</div>'+
				'</div>');

 			$('.filter_group_container').append( newFilter );

 			newFilter.find('.sort_filter_items').sortable().disableSelection();

 			// Add it to the layout GUI
 			add_new_sortable_ui_item(filterId)
 		});

    	function getPostCategories(){
    		var postCategories	= [];

    		if($('.post-types').val() == 'custom-media-gallery'){
    			$('.custom-gallery-category').each(function(){
        			var $this 			= $(this);
        			postCategories.push( { "id" : $this.find('.category_id').val(), "name" : $this.find('.category').val() } );
        		});
    		}else{
    			$('.media_boxes_options_form').find('select.select_post_categories option:selected').each(function(){
					var $this = $(this);
	 				postCategories.push( { "id" : $this.val(), "name" : $this.text() } );
	 			});
    		}
			
 			return postCategories;
    	}

    	function getCategoriesOptions(){
    		var categoriesOptions = '<option value="*">All</option>';

    		for(var i=0; i<getPostCategories().length; i++){
				categoriesOptions += '<option value="'+getPostCategories()[i].id+'"> '+ getPostCategories()[i].name +' </option>';
			}

			return categoriesOptions;
    	}

    	function getCategoriesItems(filterId){
    		var categoriesItems = '';

    		for(var i=0; i<getPostCategories().length; i++){
				categoriesItems += '<div><input checked="checked" type="checkbox" name="filters[filter_'+filterId+'][filter_items][]" value="'+getPostCategories()[i].id+'" /> '+ getPostCategories()[i].name+'</div>';
			}

			return categoriesItems;
    	}

 		function updateAllPostCategoriesFromFilters(){
 			$('.filter_group_container').find('.filter_group').each(function(){
 				var $this 		= $(this);
 				var filterId 	= $this.find('.filterId').val();

 				$this.find('.selected_item').html( getCategoriesOptions() );
 				$this.find('.sort_filter_items').html( getCategoriesItems(filterId) ).sortable().disableSelection();
 			});
 		}

 		$('.media_boxes_options_form').on('click', 'select.select_post_categories', function(){
 			updateAllPostCategoriesFromFilters();
 		});

 		/* SORT THE FILTER ITEMS */

 		$('.sort_filter_items').sortable().disableSelection();

	/* ====================================================================== *
      		SHOW / HIDE CUSTOM GALLERY 
 	 * ====================================================================== */

		$('.post-types').on('change', function(e, isHuman){
			var $this = $(this);
			
			if($this.val() == 'custom-media-gallery'){
				$('.custom-media-gallery').show();
				$('.query-options').hide();
			}else{
				$('.custom-media-gallery').hide();
				$('.query-options').show();
			}

			if(isHuman === undefined){
				isHuman = true;
			}
			changePostType($this.val(), isHuman);
		});

		$('.post-types').trigger('change', [false]);

	/* ====================================================================== *
      		CUSTOM GALLERY (CATEGORIES)
 	 * ====================================================================== */

 	 	$('body').on('click', '.custom-new-category-button', function(){
 	 		var category 		= $(this).siblings('input').val();
 	 		var index 			= 1;

 	 		if(category == ''){ 
 	 			$(this).siblings('input').focus()
 	 			return;
 	 		}

 	 		// Get max index
 	 		$('.custom-gallery-category').each(function(){
 	 			var current_index = parseFloat( $(this).find('.category_id').val() );
 	 			if(current_index > index){
 	 				index = current_index;
 	 			}
 	 		});

 	 		index++;

 	 		// New category
 	 		var new_category 	= 	'<div class="custom-gallery-category">'+
										'<input type="hidden" name="custom-media-gallery-items[categories]['+index+'][id]" class="category_id" value="'+index+'" />'+
										'<input type="hidden" name="custom-media-gallery-items[categories]['+index+'][category]" class="category" value="'+category+'" />'+
										'<span class="fa fa-trash"></span>&nbsp; '+category
									'</div>';

 	 		$('.custom-gallery-categories').append(new_category);

 	 		$(this).siblings('input').val('').focus();
 	 		updateAllPostCategoriesFromFilters();
 	 	})

 	 	$('body').on('click', '.custom-gallery-category .fa-trash', function(){
 	 		var $this 		= $(this)
 	 		var category_id = $this.siblings('.category_id').val();

 	 		$('.custom-gallery-item-category[value="'+category_id+'"]').remove();
 	 		$this.parents('.custom-gallery-category').remove();
 	 		updateAllPostCategoriesFromFilters();
 	 	});

	/* ====================================================================== *
      		CUSTOM GALLERY (OPEN MEDIA AND ADD IMAGE)
 	 * ====================================================================== */

		var $gallery 		= $('.custom-gallery-container');
		
		$gallery.sortable({
			item: 'custom-gallery-item',
			opacity:0.8,
			placeholder: 'custom-gallery-item-placeholder',
			scrollSensitivity: 100,
		});	

		/* FUNCTIONS */

		function addImage(imgId, imgSrc){
    		var newImage = '<div class="custom-gallery-item" data-id="'+imgId+'">'+
								'<input type="hidden" name="custom-media-gallery-items[attachment]['+imgId+'][img]" value="'+imgSrc+'" />'+
								
								'<div class="custom-gallery-item-categories"></div>'+

								'<img src="'+imgSrc+'">'+

								'<div class="custom-gallery-item-buttons">'+
									'<div class="custom-gallery-item-edit"><span class="fa fa-pencil"></span></div>'+
									'<div class="custom-gallery-item-add-category"><span class="fa fa-filter"></span></div>'+
									'<div class="custom-gallery-item-remove"><span class="fa fa-trash"></span></div>'+
								'</div>	'
							'</div>';

			if($gallery.find('.custom-gallery-item[data-id="'+imgId+'"]')[0] === undefined){
				$gallery.append(newImage);
			}
    	}

    	function openMedia_new(){
    		/* New media uploader wp3.5+ */
			if(typeof wp != 'undefined' && wp != undefined){
				 if( typeof file_frame != 'undefined' ){
        			file_frame.close();
    			}

				// create and open new file frame
				var file_frame = wp.media({
					title: 'Select an Image',
					library: {
						type: 'image'
					},
					button: {
						//Button text
						text: 'Add Images'
					},
					multiple: true,
				});
				
				//callback for selected image
				file_frame.on('select', function() {
					var selected = [];
					var selection = file_frame.state().get('selection');
					
					selection.map(function(file) {
						selected.push(file.toJSON());
					});

					for (var index in selected) {
						addImage(selected[index].id, selected[index].url);
					}		

					checkIfEmpty();

					$gallery.sortable({
						item: 'custom-gallery-item',
						opacity: 0.8,
						placeholder: 'custom-gallery-item-placeholder',
						scrollSensitivity: 100,
					});	
				});
				
				file_frame.open();
			
			}
    	}

    	function openMedia_edit(media_id){
    		/* New media uploader wp3.5+ */
			if(typeof wp != 'undefined' && wp != undefined){
				 if( typeof file_frame != 'undefined' ){
        			file_frame.close();
    			}

				// create and open new file frame
				var file_frame = wp.media({
					title: 'Edit an Image',
					library: {
						type: 'image'
					},
					button: {
						//Button text
						text: 'OK'
					},
					multiple: false,
				});

				//when open the gallery
				file_frame.on('open',function() {
				  	var selection 	= file_frame.state().get('selection');
				  	var attachment 	= wp.media.attachment(media_id);

				  	attachment.fetch();
				  	selection.add( attachment ? [ attachment ] : [] );
				});
				
				file_frame.open();
			
			}
    	}

   	/* ====================================================================== *
      		CUSTOM GALLERY (EVENTS)
 	 * ====================================================================== */

    	function checkIfEmpty(){
    		var items = $gallery.find('.custom-gallery-item:visible');

    		if( items.length > 0 ){
    			$('.custom-gallery-empty').hide();
    		}else{
    			$('.custom-gallery-empty').show();
    		}
    	}

		$('.custom-add-button').on('click', function(e){
			e.preventDefault();
			openMedia_new();		
		});

		$gallery.on('click', '.custom-gallery-empty', function(){
			openMedia_new();	
		});
		
		$('.custom-remove-all-button').on('click', function(){
			$gallery.find('.custom-gallery-item:not(.custom-gallery-empty)').remove();
			checkIfEmpty();
		});

		// Item buttons

		$('body').on('click', '.custom-gallery-item-edit', function(){
			openMedia_edit( $(this).parents('.custom-gallery-item').data('id') );	
		});

		$('body').on('click', '.custom-gallery-item-add-category', function(){
			open_itemCategories( $(this).parents('.custom-gallery-item') );
		});

		$('body').on('click', '.custom-gallery-item-remove', function(){
			$(this).parents('.custom-gallery-item').remove();
			checkIfEmpty();
		});
		

	/* ====================================================================== *
      		CUSTOM GALLERY (ITEM CATEGORIES)
 	 * ====================================================================== */

 	 	// init dialog/popup

        var gallery_item_categories_popup = $('.gallery-item-categories-popup').dialog({
            autoOpen: false,
            //height: 300,
            width: 400,
            modal: true,
            draggable:false,
            resizable:false,
            title: "Add categories to image",
        });

        // open init dialog/popup

        var current_item   	= '';

        function open_itemCategories(item){
        	// All categories
        	var all_categories 	= '';
        	$('.custom-gallery-category').each(function(){
        		var $this 			= $(this);
        		var checked 		= item.find('.custom-gallery-item-category[value="'+$this.find('.category_id').val()+'"]')[0] !== undefined;
        		var checked_html 	= checked ? 'checked="checked"' : '';

        		all_categories 	+= '<div class="gallery-item-category" data-id="'+$this.find('.category_id').val()+'">'+
        								'<input type="checkbox" '+checked_html+' /> '+$this.find('.category').val()+
        							'</div>';
        	});

        	$('.gallery-item-categories').html(all_categories);

        	// Open popup
            current_item 	= item;
            gallery_item_categories_popup.dialog( 'open' );
        }

        // save dialog/popup

        $('body').on('click', '.gallery-item-categories-save', function(){

        	var new_categories = '';
        	$('.gallery-item-category').each(function(){
        		var $this = $(this);

        		if($this.find('input[type="checkbox"]').is(':checked')){
        			new_categories += 	' <input type="hidden" '+
											' name="custom-media-gallery-items[attachment]['+current_item.data('id')+'][categories]['+$this.data('id')+']"'+ 
											' class="custom-gallery-item-category" '+
											' value="'+$this.data('id')+'" '+ 
										' /> ';
        		}
        	});

        	current_item.find('.custom-gallery-item-categories').html(new_categories);
            
            gallery_item_categories_popup.dialog( 'close' );
        });

	/* ====================================================================== *
      		LAYOUT OF FILTER-SEARCH-SORT
 	 * ====================================================================== */	

 	 	/* Add item, when a new filter is added */

 	 	function add_new_sortable_ui_item(filter_id){
 	 		var new_item = 	'<div class="layout_sortable_ui_item" data-id="filter_'+filter_id+'">'+
								'<i class="fa fa-filter"></i>&nbsp; Filter Group '+filter_id+
								'<input type="hidden" value="filter_'+filter_id+'">'+
							'</div>';

 	 		$('.available_items.layout_sortable_ui').append(new_item);
 			$('.layout_sortable_ui').sortable( "refresh" );
 	 	}

 	 	/* Remove item, when a filter gets deleted */

 	 	function remove_sortable_ui_item(filter_id){
 	 		$('.layout_sortable_ui').find('.layout_sortable_ui_item').filter('[data-id="filter_'+filter_id+'"]').remove();
 			$('.layout_sortable_ui').sortable( "refresh" );
 	 	}

 	 	/* The place holder is the "DROP ZONE 1" & "DROP ZONE 2" text  */

 	 	function show_hide_placeholder(){ 
 	 		$('.layout_sortable_ui').each(function(){
 	 			var sortable_ui 	= $(this); 
		        var sortable_span 	= sortable_ui.find('.drop_zone_placeholder');

		        if( sortable_ui.find('.layout_sortable_ui_item')[0] === undefined ){
		        	sortable_span.fadeIn(300);
		        }else{
		        	sortable_span.hide();
		        }
 	 		});
 	 	}
 	 	show_hide_placeholder();

 	 	/* Add the "dropzone id" to the hidden input (Depending if it goes to dropzone 1 or 2) and remove it when the items go back to the "Available Items" section */

 	 	function refresh_input_name(){
 	 		$('.layout_sortable_ui').each(function(){
 	 			var sortable_ui 		= $(this); 
 	 			var sortable_ui_dz_id 	= sortable_ui.attr('data-drop_zone_id');
		        var sortable_ui_item 	= sortable_ui.find('.layout_sortable_ui_item');
		        var inputs 				= sortable_ui_item.find('input[type="hidden"]');
		        
		        if(sortable_ui_dz_id === undefined){
		        	inputs.removeAttr('name');
		        }else{
		        	inputs.attr('name', sortable_ui_dz_id+'[]');
		        }
 	 		});
 	 	}
 	 	refresh_input_name();

 	 	/* Init sortable */

 	 	$('.layout_sortable_ui').sortable({
            connectWith: '.layout_sortable_ui',
            items : '.layout_sortable_ui_item',
            stop : function(event, ui) {
             	show_hide_placeholder();
             	refresh_input_name();
			},
        }).disableSelection();	

 	/* ====================================================================== *
      		DROP-ZONE CONFIG DIALOG (POPUP/MODAL)
 	 * ====================================================================== */	 	
 	/*

		// I was going to add a dialog (popup) for adding some paddings and margins for the drop-zones (but better add it in the "extra-CSS" section )

        $( '.drop_zone_1_config, .drop_zone_2_config' ).dialog({
	    	autoOpen: false,
	      	//height: 500,
	      	width: 750,
	      	modal: true,
	      	draggable:true,
			resizable:true,
	      	title: "Drop zone style",
    	}).parent().appendTo($(".media_boxes_options_form"));

    	// open dialog

    	$('.configure_drop_zone').on('click', function(){
    		var $this 			= $(this);
    		var drop_zone_id 	= $(this).attr('data-open');
    		

    		$(drop_zone_id).dialog( 'open' );
    	});

    	// close when saving

    	$('.drop_zone_1_config, .drop_zone_2_config').on('click', '.button-primary', function(){
    		var $this 			= $(this);
    		var drop_zone_id 	= $this.attr('data-close');
    		
    		$(drop_zone_id).dialog( 'close' );
    	});

    	<div class="drop_zone_1_config">

			<h4>Margins</h4>

			<div class="grid">
				<div class="col-50p">
					<label>Margin top</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>

				<div class="col-50p">
					<label>Margin bottom</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>
			</div>

			<div class="grid">
				<div class="col-50p">
					<label>Margin left</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>

				<div class="col-50p">
					<label>Margin right</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>
			</div>

			<br>

			<h4>Paddings</h4>

			<div class="grid">
				<div class="col-50p">
					<label>Margin top</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>

				<div class="col-50p">
					<label>Margin bottom</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>
			</div>

			<div class="grid">
				<div class="col-50p">
					<label>Margin left</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>

				<div class="col-50p">
					<label>Margin right</label>
					<input name="drop_zone_1_margin-top" type="text" value="<?php echo $form_sort_by_text; ?>"  />
					px		
				</div>
			</div>

			<div class="form-controls">
				<button class="button-primary green" data-close=".drop_zone_1_config"><span class="fa fa-check"></span> &nbsp;Save</button>
			</div>

		</div>
	*/

	});
})(jQuery);