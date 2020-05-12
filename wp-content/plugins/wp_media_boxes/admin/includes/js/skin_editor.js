

    
/* ======================================================= 
 *
 *    Media Boxes Skin Editor
 *    Version: 1.0
 *    By David Blanco
 *
 *    Contact: http://codecanyon.net/user/castlecode
 *    Created: July 20, 2018
 *
 *    Copyright (c) 2018, David Blanco. All rights reserved.
 *    Available only in http://codecanyon.net/
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
                    edit_single_item_css();
                },
            });

            input.keyup(function() {
                $this.slider( "value", input.val() );
                edit_single_item_css();
            });

            input.on('refresh_slider', function() {
                $this.slider( "value", input.val() );
            });

        });    

    /* ====================================================================== *
          COLOR PICKER
     * ====================================================================== */        

            var timer;
            
            var options = {
                  color:true,
                  change:function(event,ui) {   
                        clearTimeout(timer);
                        timer = setTimeout(function() {
                            edit_single_item_css();
                            edit_overlay_and_content_css();
                        }, 10);
                  }
            }

            /* Set up colorpicker */
            $('.colorpicker-input').wpColorPicker(options); 

/* ====================================================================== *
 * ====================================================================== *
 * ====================================================================== *


      BUILD YOUR TEMPLATE 


 * ====================================================================== *    
 * ====================================================================== *    
 * ====================================================================== */      

        /* The place holder is the "DROP ZONE 1" & "DROP ZONE 2" text  */

        function show_hide_placeholder(){ 
            $('.skin_sortable_ui').each(function(){
                var sortable_ui     = $(this); 
                var sortable_span   = sortable_ui.find('.drop_zone_placeholder_skin');

                if( sortable_ui.find('.skin_item')[0] === undefined ){
                    sortable_span.fadeIn(300);
                }else{
                    sortable_span.hide();
                }
            });
        }
        show_hide_placeholder();

        /* Add the "dropzone id" and the "index" */

        function refresh_inputs(){
            $('.skin_sortable_ui').each(function(){
                var skin_sortable_ui    = $(this)
                var sortable_ui_dz_id   = skin_sortable_ui.attr('data-drop_zone_id');

                skin_sortable_ui.find('.skin_item').each(function(){
                    var current_item    = $(this);
                    var current_key     = current_item.find('input.key').val();
                    var other_items     = $().add( current_item.siblings('.skin_item') ).add( skin_sortable_ui.siblings('.skin_sortable_ui').find('.skin_item') ); // other items from current drop zone and from the other drop zone

                    /* IF THIS CURRENT ITEM ALREADY HAS AN INDEX */

                    if(current_key.split("-")[1] !== undefined){

                        // just check if the dropzone id needs to be updated (in case it was changes from content to overlay or overlay to content)

                        var inputs = current_item.find('input[type="hidden"]');
                        inputs.each(function(){
                            var input           = $(this);
                            var input_dropzone  = input.attr('name').indexOf('drop_zone_overlay') !== -1 ? 'drop_zone_overlay' : 'drop_zone_content';

                            if(input_dropzone != sortable_ui_dz_id){ // it changes from dropzone, then update!
                                input.attr( 'name', input.attr('name').split(input_dropzone+'[').join(sortable_ui_dz_id+'[') );
                            }
                        });

                    }else{

                        /* GET MAX INDEX */

                        var index = 0;
                        other_items.each(function(){
                            var other_item  = $(this);
                            var other_key   = other_item.find('input.key').val().split("-")[0];
                            var other_index = other_item.find('input.key').val().split("-")[1];
                            other_index     = parseInt(other_index);

                            if(other_key == current_key){
                                if( other_index > index ){
                                    index = other_index;
                                }
                            }
                        });
                        index++; 

                        /* SET INDEX */

                        current_key = current_key+"-"+index;
                        current_item.find('input.key').val(current_key);

                        /* SHOW INDEX NEXT TO THE NAME */

                        var name        = current_item.find('input.name').val();
                        var new_name    = index > 1 ? name+" ("+index+")" : name;
                        var cache       = current_item.children(); // save children
                        current_item.text(new_name).append(cache); // append children back
                        current_item.find('input.name').val(new_name); // change input name also (so it gets saved in the DB)
                            
                        /* ADD NAME & DROPZONE NAME */

                        var inputs = current_item.find('input[type="hidden"]');
                        inputs.each(function(){
                            var input = $(this);
                            
                            input.attr( 'name', input.attr('data-name').split('drop_zone[]').join(sortable_ui_dz_id+'['+current_key+']') );
                        });

                    }
                    
                });
            });
        }
        refresh_inputs();

        /* init draggable */

        $('.skin_draggable_ui').find( '.skin_item' ).draggable({
            connectToSortable: '.skin_sortable_ui',
            helper: 'clone',
        });

        /* Init sortable */

        $('.skin_sortable_ui').sortable({
            connectWith: '.skin_sortable_ui',
            items : '.skin_item',
            stop : function(event, ui) {
                show_hide_placeholder();
                refresh_inputs();
                refresh_preview();
                refresh_all_css();
            },
        }).disableSelection();  

        /* Remove item */

        $('.skin_container').on('click', '.skin_item_controls .fa-trash-o', function(){
            $(this).parents('.skin_item').remove();
            show_hide_placeholder();
            refresh_preview();
            refresh_all_css();
        })

    /* ====================================================================== *
            EDIT ACTION ON "SKIN ITEM" (POPUP)
    * ====================================================================== */    

        // init dialog/popup

        var action_skin_item = $('.action_skin_item').dialog({
            autoOpen: false,
            //height: 300,
            width: 400,
            modal: true,
            draggable:false,
            resizable:false,
            title: "Edit Action",
        });

        // open init dialog/popup

        var current_skin_item = '';

        $('body').on('click', '.edit_action_skin_item', function(){
            current_skin_item = $(this).parents('.skin_item');

            set_action_fields();
            refresh_action_fields();
            action_skin_item.dialog( 'open' );
        });

        // set values

        function set_action_fields(){
            $('select.action').val( current_skin_item.find('.action').val() );
            $('select.link_target').val( current_skin_item.find('.link_target').val() );
            $('select.link_to').val( current_skin_item.find('.link_to').val() );
            $('input.custom_url').val( current_skin_item.find('.custom_url').val());
        }


        // Refresh fields

        $('select.action').change(function(){
            refresh_action_fields();
        });

        $('select.link_to').change(function(){
            refresh_action_fields();
        });

        function refresh_action_fields(){
            if($('select.action').val() == 'link_to'){
                $('.link_to').css('visibility', 'visible');
            }else{
                $('.link_to').css('visibility', 'hidden');
            }

            if($('select.action').val() == 'link_to' && $('select.link_to').val() == 'custom_url'){
                $('.custom_url').css('visibility', 'visible');
            }else{
                $('.custom_url').css('visibility', 'hidden');
            }
        }

        // save dialog/popup

        $('body').on('click', '.save_action_skin_item', function(){
            current_skin_item.find('.action').val( $('select.action').val() );
            current_skin_item.find('.link_target').val( $('select.link_target').val() );
            current_skin_item.find('.link_to').val( $('select.link_to').val() );
            current_skin_item.find('.custom_url').val( $('input.custom_url').val() );

            action_skin_item.dialog( 'close' );
        });

    /* ====================================================================== *
            EDIT ICON ON "SKIN ITEM" (POPUP)
    * ====================================================================== */    

        // allow html in title on the dialog plugin

        $.widget('ui.dialog', $.extend({}, $.ui.dialog.prototype, {
            _title: function(title) {
                if (!this.options.title ) {
                    title.html('&#160;');
                } else {
                    title.html(this.options.title);
                }
            }
        }));

        // init dialog/popup

        var icon_skin_item = $('.icon_skin_item').dialog({
            autoOpen: false,
            //height: 300,
            width: 1000,
            modal: true,
            draggable:false,
            resizable:false,
            title: "<input type='text' placeholder='filter the icons' class='search_icons' />",
        });

        // search icons

        $( '.ui-dialog' ).on('keyup', '.search_icons', function(){
            var $this   = $(this);
            var val     = $this.val();  

            $('.icons_container').find('.fa').each(function(){
                var $this = $(this);
                if( $this.attr('class').substr(6).toLowerCase().indexOf(val.toLowerCase()) !== -1 ){
                    $this.show();
                }else{
                    $this.hide();
                }
            });


        });

        // open init dialog/popup

        var current_skin_item   = '';

        $('body').on('click', '.edit_icon_skin_item', function(){
            current_skin_item   = $(this).parents('.skin_item');
            var icon            = current_skin_item.find('.output').val();

            $('.icons_container').find('.fa').removeClass('icon-selected');
            $('.icons_container').find('.'+icon).addClass('icon-selected');

            $('.search_icons').val('').trigger('keyup');

            icon_skin_item.dialog( 'open' );
        });

        // save dialog/popup

        $('body').on('click', '.icons_container .fa', function(){
            current_skin_item.find('.output').val( $(this).attr('data-class') );
            current_skin_item.find('.skin_item_icon').html(' <span class="fa '+$(this).attr('data-class')+'"></span> ')
            refresh_preview();
            icon_skin_item.dialog( 'close' );
        });

    /* ====================================================================== *
            EDIT TEXT ON "SKIN ITEM" (POPUP)
    * ====================================================================== */    

        // init dialog/popup

        var text_skin_item = $('.text_skin_item').dialog({
            autoOpen: false,
            //height: 300,
            width: 400,
            modal: true,
            draggable:false,
            resizable:false,
            title: "Edit Text",
        });

        // open init dialog/popup

        var current_skin_item   = '';

        $('body').on('click', '.edit_text_skin_item', function(){
            current_skin_item   = $(this).parents('.skin_item');

            text_skin_item.find('textarea.text').val( current_skin_item.find('.output').val() );

            text_skin_item.dialog( 'open' );
        });

        // save dialog/popup

        $('body').on('click', '.save_text_skin_item', function(){
            current_skin_item.find('.output').val( text_skin_item.find('textarea.text').val() );
            refresh_preview();
            text_skin_item.dialog( 'close' );
        });

/* ====================================================================== *
 * ====================================================================== *
 * ====================================================================== *


      APPLY SOME STYLE


 * ====================================================================== *    
 * ====================================================================== *    
 * ====================================================================== */  

    /* ====================================================================== *
            GET ALL ITEMS
    * ====================================================================== */         

        function get_all_items(){
            var all_items = [];
            $('.drop_zone_overlay').find('.skin_item').each(function(){
                var item            = $(this);
                
                all_items.push({ 
                    'key'           : item.find('input.key').val(), 
                    'name'          : item.find('input.name').val(), 
                    'dropzoneid'    : item.parents('.drop_zone_overlay').attr('data-drop_zone_id'), 
                    'output'        : item.find('input.output').val(),
                    'type'          : item.find('input.type').val(),
                });
            });

            return all_items;
        }

    /* ====================================================================== *
            PREVIEW
    * ====================================================================== */     

        function refresh_preview(){

        /* ## GET ALL ITEMS ## */

            var all_items = get_all_items();

        /* ## REFRESH PREVIEW SELECT BOX ## */

            var preview_select = $('select#style_editor_preview_select').empty();

            all_items.forEach(function(row){
                preview_select.append( $('<option value="'+row.key+'">'+row.name+'</option>') );
            });

        /* ## REFRESH OVERLAY AND CONTENT ## */

            var thumbnail_overlay   = $('#style_editor_preview .thumbnail-overlay').children('.aligment').children('.aligment').html('');
            var content             = $('#style_editor_preview .media-box-content').html('');

            // add new items
            all_items.forEach(function(row){

                // Create item

                var name        = row.name;
                var icon_class  = '';

                if(row.type=='icon'){
                    name        = '';
                    icon_class  = row.output;
                }else if(row.type=='text'){
                    name = row.output;
                }

                var item = ' <div class="'+row.key+' '+icon_class+'">'+name+'</div> ';

                // Append item to a zone

                if(row.dropzoneid == 'drop_zone_overlay'){
                    thumbnail_overlay.append(item);
                }else{
                    content.append(item);
                }
            });
        }

        $('#style_editor_preview').mediaBoxes({
            columns: 1,
            filterContainer: '',
            search: '',
            boxesToLoadStart: 1,
            overlayEffect: 'direction-aware',
            resolutions : [
                {
                    maxWidth: 960,
                    columnWidth: 'auto',
                    columns: 1,
                },
                {
                    maxWidth: 650,
                    columnWidth: 'auto',
                    columns: 1,
                    horizontalSpaceBetweenBoxes: 10,
                    verticalSpaceBetweenBoxes: 10,
                },
                {
                    maxWidth: 450,
                    columnWidth: 'auto',
                    columns: 1,
                    horizontalSpaceBetweenBoxes: 10,
                    verticalSpaceBetweenBoxes: 10,
                },
            ],
        });

        setTimeout(function(){ // wait until the media boxes is initialized (so the alignment divs are added to the thumbnail-overlay)
            refresh_preview();
            refresh_all_css();
        }, 300);

    /* ====================================================================== *
            STYLE EDITOR
    * ====================================================================== */    

        var style_editor    = $('.style_editor');
        var textarea_css    = $('.style_editor_css');
        var json_css        = textarea_css.val() == '' || textarea_css[0] == undefined ? {} : jQuery.parseJSON(textarea_css.val()) ;

        // when adding a new "css property" you must add it below, in the update_style_editor() method and in the edit_single_item_css() method

        var default_css     = {
            'font-size'                     : '13px', 
            'line-height'                   : '13px',
            'color'                         : '#484848',
            'font-family'                   : 'Arial, Helvetica, sans-serif',
            'font-weight'                   : '400',
            'text-decoration'               : 'none',
            'font-style'                    : 'normal',
            'text-transform'                : 'none',
            'text-align'                    : 'inherit',
            'letter-spacing'                : '0px',
            'word-spacing'                  : '0px',

            'position'                      : 'relative',
            'position-unit'                 : 'px',
            'top'                           : '0',
            'right'                         : '',
            'bottom'                        : '',
            'left'                          : '0',
            'width'                         : '',
            'width-unit'                    : 'px',
            'height'                        : '',
            'height-unit'                   : 'px',
            'display'                       : 'block',
            'overflow'                      : 'visible',
            'float'                         : 'none',
            'clear'                         : 'none',
            'margin-top'                    : '0px',
            'margin-right'                  : '0px',
            'margin-bottom'                 : '0px',
            'margin-left'                   : '0px',
            'padding-top'                   : '0px',
            'padding-right'                 : '0px',
            'padding-bottom'                : '0px',
            'padding-left'                  : '0px',

            'border-top-width'              : '0px',
            'border-right-width'            : '0px',
            'border-bottom-width'           : '0px',
            'border-left-width'             : '0px',
            'border-top-left-radius'        : '0px',
            'border-top-right-radius'       : '0px',
            'border-bottom-left-radius'     : '0px',
            'border-bottom-right-radius'    : '0px',
            'border-color'                  : '#ffffff',
            'border-style'                  : 'none',

            'background-color'              : 'rgba(0,0,0,0.0)', 

            'animation-on-thumbnail-overlay': 'none',
            'custom-css'                    : '',
            'custom-css-onhover'            : '',
        }

        var default_css_icon  = {
            'font-size'                     : '15px', 
            'line-height'                   : '50px',
            'color'                         : '#e54e53',
            'font-family'                   : 'FontAwesome',
            'font-weight'                   : '400',
            'text-decoration'               : 'none',
            'font-style'                    : 'normal',
            'text-transform'                : 'none',
            'text-align'                    : 'center',
            'letter-spacing'                : '0px',
            'word-spacing'                  : '0px',

            'position'                      : 'relative',
            'position-unit'                 : 'px',
            'top'                           : '0',
            'right'                         : '',
            'bottom'                        : '',
            'left'                          : '0',
            'width'                         : '50px',
            'width-unit'                    : 'px',
            'height'                        : '50px',
            'height-unit'                   : 'px',
            'display'                       : 'inline-block',
            'overflow'                      : 'visible',
            'float'                         : 'none',
            'clear'                         : 'none',
            'margin-top'                    : '0px',
            'margin-right'                  : '0px',
            'margin-bottom'                 : '0px',
            'margin-left'                   : '0px',
            'padding-top'                   : '0px',
            'padding-right'                 : '0px',
            'padding-bottom'                : '0px',
            'padding-left'                  : '0px',

            'border-top-width'              : '0px',
            'border-right-width'            : '0px',
            'border-bottom-width'           : '0px',
            'border-left-width'             : '0px',
            'border-top-left-radius'        : '0px',
            'border-top-right-radius'       : '0px',
            'border-bottom-left-radius'     : '0px',
            'border-bottom-right-radius'    : '0px',
            'border-color'                  : '#ffffff',
            'border-style'                  : 'none',

            'background-color'              : '#ffffff', 

            'animation-on-thumbnail-overlay': 'none',
            'custom-css'                    : 'border-radius: 50% !important;',
            'custom-css-onhover'            : '',
        }   

        style_editor.tabs();

    /* ====================================================================== *
            BUILD CSS
    * ====================================================================== */            

        function build_css(item_key, json_css){
            var css = '';

            css     += '<style class="media_boxes_style_editor" id="media_boxes_item_'+item_key+'">\n';

            // IDLE STATE
            
            css     += '.style_editor_preview .'+item_key+' {\n'
            for(var key in json_css[item_key]){
                var value = json_css[item_key][key];

                if(key.indexOf("-unit") >= 0) continue; // don't add unit properties, they are only for visual editor
                if(value == '' || value == 'px') continue; // don't add rules that are empty
                if(key == 'custom-css-onhover') continue; // don't add the custom css onhover, that would be added after
                if(key == 'animation-on-thumbnail-overlay') continue; // don't add animation on thumbnail, they are when thumbnail-overlay gets triggered

                if(key == 'custom-css'){ // custom css
                    css += value;
                }else{ // normal
                    css += key+':'+value+' !important; \n';
                }
            }
            css += '}\n';

            // HOVER STATE

            css     += '.style_editor_preview .'+item_key+':hover {\n'
            for(var key in json_css[item_key]){
                if(key == 'custom-css-onhover'){ // custom css onhover
                    css += json_css[item_key][key];
                }
            }
            css += '}\n';

            css += '</style>\n';

            return css;
        }

    /* ====================================================================== *
            REFRESH ALL CSS
    * ====================================================================== */        

        function refresh_all_css(){

        /* ## GET ALL ITEMS ## */

            var all_items = get_all_items();   

        /* ##  CHECK WHICH ITEMS SHOULD NOT BE IN THE CSS AND ERASE THEM ## */

            for (var item_key in json_css) {
                var found = all_items.filter(function(row){ return row.key == item_key; })[0];
                if( found == undefined ){
                    delete json_css[item_key];
                }
            };

        /* ## CHECK WHICH ITEMS ARE MISSING IN THE CSS AND ADD THEM, ALSO CHECK IF SOME OF THE CSS PROPERTIES ARE MISSING (IN CASE A NEW ONE IS ADDED) ## */
            
            all_items.forEach(function(row){
                var current_default_css = row.type=='icon' ? default_css_icon : default_css;

                if(row.key in json_css){ // edit
                    for (var css_propety in current_default_css) {
                        if(css_propety in json_css[row.key] && json_css[row.key][css_propety] != ''){
                            // the css property exists and is different than '', so nothing to do, all good
                        }else{
                            // the css property is missing, so add the default one
                            json_css[row.key][css_propety] = current_default_css[css_propety];
                        }
                    }
                }else{ // new
                    json_css[row.key] = current_default_css;
                }
            });

        /* ##  NOW UPDATE ALL CSS ## */

            $('.media_boxes_style_editor').remove();

            for (var item_key in json_css) {           
                
                $('body').append( build_css(item_key, json_css) );

            };

        /* ##  PLACE CSS INTO TEXTAREA ## */

            textarea_css.val(JSON.stringify(json_css));

            update_style_editor();

        }

    /* ====================================================================== *
            UPDATE STYLE EDITOR
    * ====================================================================== */   

        function update_style_editor(){
            var current_item    = $('select#style_editor_preview_select').val();

            if(json_css[current_item] == undefined)return;

            if(current_item.substr(0,4) == 'icon'){
                style_editor.find('input.font_family').attr('readonly', true);
            }else{
                style_editor.find('input.font_family').attr('readonly', false);
            }

            var position_unit       = json_css[current_item]['position-unit'];
            var width_unit          = json_css[current_item]['width-unit'];
            var height_unit         = json_css[current_item]['height-unit'];
            var animation_overlay   = json_css[current_item]['animation-on-thumbnail-overlay'];

            style_editor.find('input.font_size'                         ).val( json_css[current_item]['font-size'].replace('px','')                     ).trigger('refresh_slider');
            style_editor.find('input.line_height'                       ).val( json_css[current_item]['line-height'].replace('px','')                   ).trigger('refresh_slider');
            style_editor.find('input.font_color'                        ).val( json_css[current_item]['color']                                          ).wpColorPicker('color', json_css[current_item]['color']);
            style_editor.find('input.font_family'                       ).val( json_css[current_item]['font-family']                                    );
            style_editor.find('select.font_weight'                      ).val( json_css[current_item]['font-weight']                                    );
            style_editor.find('select.text_decoration'                  ).val( json_css[current_item]['text-decoration']                                );
            style_editor.find('select.font_style'                       ).val( json_css[current_item]['font-style']                                     );
            style_editor.find('select.text_transform'                   ).val( json_css[current_item]['text-transform']                                 );
            style_editor.find('select.text_align'                       ).val( json_css[current_item]['text-align']                                     );
            style_editor.find('input.letter_spacing'                    ).val( json_css[current_item]['letter-spacing'].replace('px','')                ).trigger('refresh_slider');
            style_editor.find('input.word_spacing'                      ).val( json_css[current_item]['word-spacing'].replace('px','')                  ).trigger('refresh_slider');

            style_editor.find('select.position'                         ).val( json_css[current_item]['position']                                       );
            style_editor.find('select.position_unit'                    ).val( position_unit                                                            );
            style_editor.find('input.top'                               ).val( json_css[current_item]['top'].replace(position_unit,'')                  );
            style_editor.find('input.right'                             ).val( json_css[current_item]['right'].replace(position_unit,'')                );
            style_editor.find('input.bottom'                            ).val( json_css[current_item]['bottom'].replace(position_unit,'')               );
            style_editor.find('input.left'                              ).val( json_css[current_item]['left'].replace(position_unit,'')                 );
            style_editor.find('select.width_unit'                       ).val( width_unit                                                               );
            style_editor.find('input.width'                             ).val( json_css[current_item]['width'].replace(width_unit,'')                   );
            style_editor.find('select.height_unit'                      ).val( height_unit                                                              );
            style_editor.find('input.height'                            ).val( json_css[current_item]['height'].replace(height_unit,'')                 );
            style_editor.find('select.display'                          ).val( json_css[current_item]['display']                                        );
            style_editor.find('select.overflow'                         ).val( json_css[current_item]['overflow']                                       );
            style_editor.find('select.float'                            ).val( json_css[current_item]['float']                                          );
            style_editor.find('select.clear'                            ).val( json_css[current_item]['clear']                                          );
            style_editor.find('input.margin_top'                        ).val( json_css[current_item]['margin-top'].replace('px','')                    );
            style_editor.find('input.margin_right'                      ).val( json_css[current_item]['margin-right'].replace('px','')                  );
            style_editor.find('input.margin_bottom'                     ).val( json_css[current_item]['margin-bottom'].replace('px','')                 );
            style_editor.find('input.margin_left'                       ).val( json_css[current_item]['margin-left'].replace('px','')                   );
            style_editor.find('input.padding_top'                       ).val( json_css[current_item]['padding-top'].replace('px','')                   );
            style_editor.find('input.padding_right'                     ).val( json_css[current_item]['padding-right'].replace('px','')                 );
            style_editor.find('input.padding_bottom'                    ).val( json_css[current_item]['padding-bottom'].replace('px','')                );
            style_editor.find('input.padding_left'                      ).val( json_css[current_item]['padding-left'].replace('px','')                  );

            style_editor.find('input.border_top_width'                  ).val( json_css[current_item]['border-top-width'].replace('px','')              );
            style_editor.find('input.border_right_width'                ).val( json_css[current_item]['border-right-width'].replace('px','')            );
            style_editor.find('input.border_bottom_width'               ).val( json_css[current_item]['border-bottom-width'].replace('px','')           );
            style_editor.find('input.border_left_width'                 ).val( json_css[current_item]['border-left-width'].replace('px','')             );
            style_editor.find('input.border_top_left_radius'            ).val( json_css[current_item]['border-top-left-radius'].replace('px','')        );
            style_editor.find('input.border_top_right_radius'           ).val( json_css[current_item]['border-top-right-radius'].replace('px','')       );
            style_editor.find('input.border_bottom_left_radius'         ).val( json_css[current_item]['border-bottom-left-radius'].replace('px','')     );
            style_editor.find('input.border_bottom_right_radius'        ).val( json_css[current_item]['border-bottom-right-radius'].replace('px','')    );
            style_editor.find('input.border_color'                      ).val( json_css[current_item]['border-color']                                   ).wpColorPicker('color', json_css[current_item]['border-color']);;
            style_editor.find('select.border_style'                     ).val( json_css[current_item]['border-style']                                   );

            style_editor.find('input.background_color'                  ).val( json_css[current_item]['background-color']                               ).wpColorPicker('color', json_css[current_item]['background-color']).trigger('update-alpha');

            style_editor.find('select.animation_on_thumbnail_overlay'   ).val( animation_overlay                                                        );
            style_editor.find('textarea.custom_css'                     ).val( json_css[current_item]['custom-css']                                     );
            style_editor.find('textarea.custom_css_onhover'             ).val( json_css[current_item]['custom-css-onhover']                             );

            edit_single_item_css();
        }


        $('select#style_editor_preview_select').on('change', function(){
            update_style_editor();
        });    

    /* ====================================================================== *
            EDIT SINGLE ITEM CSS
    * ====================================================================== */       

        function edit_single_item_css(){
            
            var item_key        = $('select#style_editor_preview_select').val();
 
            var position_unit   = style_editor.find('select.position_unit').val();
            var width_unit      = style_editor.find('select.width_unit').val();
            var height_unit     = style_editor.find('select.height_unit').val();

            json_css[item_key] = { 
                'font-size'                         : style_editor.find('input.font_size').val()+'px', 
                'line-height'                       : style_editor.find('input.line_height').val()+'px',
                'color'                             : style_editor.find('input.font_color').val(),
                'font-family'                       : style_editor.find('input.font_family').val(),
                'font-weight'                       : style_editor.find('select.font_weight').val(),
                'text-decoration'                   : style_editor.find('select.text_decoration').val(),
                'font-style'                        : style_editor.find('select.font_style').val(),
                'text-transform'                    : style_editor.find('select.text_transform').val(),
                'text-align'                        : style_editor.find('select.text_align').val(),
                'letter-spacing'                    : style_editor.find('input.letter_spacing').val()+'px',
                'word-spacing'                      : style_editor.find('input.word_spacing').val()+'px',

                'position'                          : style_editor.find('select.position').val(),
                'position-unit'                     : position_unit,
                'top'                               : style_editor.find('input.top').val()+position_unit,
                'right'                             : style_editor.find('input.right').val()+position_unit,
                'bottom'                            : style_editor.find('input.bottom').val()+position_unit,
                'left'                              : style_editor.find('input.left').val()+position_unit,
                'width'                             : style_editor.find('input.width').val()+width_unit,
                'width-unit'                        : width_unit,
                'height'                            : style_editor.find('input.height').val()+height_unit,
                'height-unit'                       : height_unit,
                'display'                           : style_editor.find('select.display').val(),
                'overflow'                          : style_editor.find('select.overflow').val(),
                'float'                             : style_editor.find('select.float').val(),
                'clear'                             : style_editor.find('select.clear').val(),
                'margin-top'                        : style_editor.find('input.margin_top').val()+'px',
                'margin-right'                      : style_editor.find('input.margin_right').val()+'px',
                'margin-bottom'                     : style_editor.find('input.margin_bottom').val()+'px',
                'margin-left'                       : style_editor.find('input.margin_left').val()+'px',
                'padding-top'                       : style_editor.find('input.padding_top').val()+'px',
                'padding-right'                     : style_editor.find('input.padding_right').val()+'px',
                'padding-bottom'                    : style_editor.find('input.padding_bottom').val()+'px',
                'padding-left'                      : style_editor.find('input.padding_left').val()+'px',

                'border-top-width'                  : style_editor.find('input.border_top_width').val()+'px',
                'border-right-width'                : style_editor.find('input.border_right_width').val()+'px',
                'border-bottom-width'               : style_editor.find('input.border_bottom_width').val()+'px',
                'border-left-width'                 : style_editor.find('input.border_left_width').val()+'px',
                'border-top-left-radius'            : style_editor.find('input.border_top_left_radius').val()+'px',
                'border-top-right-radius'           : style_editor.find('input.border_top_right_radius').val()+'px',
                'border-bottom-left-radius'         : style_editor.find('input.border_bottom_left_radius').val()+'px',
                'border-bottom-right-radius'        : style_editor.find('input.border_bottom_right_radius').val()+'px',
                'border-color'                      : style_editor.find('input.border_color').val(),
                'border-style'                      : style_editor.find('select.border_style').val(),

                'background-color'                  : style_editor.find('input.background_color').val(),

                'animation-on-thumbnail-overlay'    : style_editor.find('select.animation_on_thumbnail_overlay').val(),                
                'custom-css'                        : style_editor.find('textarea.custom_css').val(),
                'custom-css-onhover'                : style_editor.find('textarea.custom_css_onhover').val(),
            };

            $('#media_boxes_item_'+item_key).remove();

            $('body').append( build_css(item_key, json_css) );

            textarea_css.val(JSON.stringify(json_css));
        }        

        var inputs  =   style_editor.find('input, textarea').filter(function() {
                            return !$(this).siblings('.slider').length && !$(this).hasClass('overlay_and_content_css');
                        });

        inputs.keyup(function(){
            edit_single_item_css();
        }); 

        style_editor.find('select').change(function(){
            edit_single_item_css();
        }); 

/* ====================================================================== *
 * ====================================================================== *
 * ====================================================================== *


      OVERLAY & CONTENT


 * ====================================================================== *    
 * ====================================================================== *    
 * ====================================================================== */          

        function edit_overlay_and_content_css(item_key, json_css){

            $('.media_boxes_overlay_and_content').remove();

            var css = '';

            css     += '<style class="media_boxes_overlay_and_content">\n';

            css         += '.style_editor_preview .thumbnail-overlay {\n';
            css             += ( !$('.overlay_show').is(':checked') ? " display : none !important; " : "") +" \n";
            css             += 'background-color    : '+$('.overlay_background_color').val()+' !important; \n';
            css             += 'padding-top         : '+$('.overlay_padding_top').val()+'px !important; \n';
            css             += 'padding-right       : '+$('.overlay_padding_right').val()+'px !important; \n';
            css             += 'padding-bottom      : '+$('.overlay_padding_bottom').val()+'px !important; \n';
            css             += 'padding-left        : '+$('.overlay_padding_left').val()+'px !important; \n';
            css             += 'text-align          : '+$('.overlay_text_align').val()+' !important; \n';
            css             += 'vertical-align      : '+$('.overlay_vertical_align').val()+' !important; \n';
            css         += '}\n';

            css         += '.style_editor_preview .media-box-content {\n'
            css             += ( !$('.content_show').is(':checked') ? " display : none !important; " : "") +" \n";
            css             += 'background-color    : '+$('.content_background_color').val()+' !important; \n';
            css             += 'padding-top         : '+$('.content_padding_top').val()+'px !important; \n';
            css             += 'padding-right       : '+$('.content_padding_right').val()+'px !important; \n';
            css             += 'padding-bottom      : '+$('.content_padding_bottom').val()+'px !important; \n';
            css             += 'padding-left        : '+$('.content_padding_left').val()+'px !important; \n';
            css             += 'text-align          : '+$('.content_text_align').val()+' !important; \n';
            css         += '}\n';

            css     += '</style>\n';

            $('body').append(css);

            $('#style_editor_preview').mediaBoxes('resize');
        }        

        edit_overlay_and_content_css();

        style_editor.find('input[type="text"].overlay_and_content_css').keyup(function(){
            edit_overlay_and_content_css();            
        });

        style_editor.find('input[type="checkbox"].overlay_and_content_css').click(function(){
            edit_overlay_and_content_css();            
        });

        style_editor.find('select.overlay_and_content_css').change(function(){
            edit_overlay_and_content_css();            
        });


    });
})(jQuery);