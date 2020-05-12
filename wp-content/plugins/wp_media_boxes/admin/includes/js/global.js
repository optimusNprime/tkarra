    
/* ======================================================= 
 *
 *  	Media Boxes Global
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
	    	TABS
 	 * ====================================================================== */

		$( '.media_boxes_tabs' ).tabs({
		    activate: function( event, ui ) {
		    	$('#style_editor_preview').mediaBoxes('resize');
		    }
		});

	/* ====================================================================== *
      		SAVING OPTIONS WITH AJAX
 	 * ====================================================================== */

 		function showMessage(icon, message){
 			if($('body').find('media_boxes_messages')[0] === undefined){
 				$('body').append('<div class="media_boxes_messages"></div>');
 			}

 			var newMessage = $('<div class="media_boxes_new_message"><span class="fa '+icon+'"></span> '+message+'</div>');

			$('.media_boxes_messages').prepend( newMessage.fadeIn(300) );

			setTimeout(function () {
			    newMessage.fadeOut(400);
			}, 3000);
 		}

 		function blockUI(){
 			var html 	= 	$('<div class="media_boxes_blockUI">' +
 								'<div class="media_boxes_blockUI_center"><span class="fa fa-cog fa-spin fa-3x fa-fw"></span></div>' +
 							  '</div>').hide();

 			$('body').append(html.fadeIn(200));
 		}

 		function unblockUI(){
 			setTimeout(function(){
 				$('.media_boxes_blockUI').fadeOut(300);
 			}, 00);
 		}

		$('.media_boxes_options_form').submit(function(){
			var $this=$(this);

			if($this.find('input[name="name"]').val() == ''){
				alert('You must specify a name!'); // for both the skin and portfolio settings
				return false;
			}
			
			$.ajax({  
				type: 'post', 
				url: ajaxurl,
				data: $this.serialize(),
				beforeSend: function () {
					blockUI();
				}
			}).always(function() {
				unblockUI()
			}).fail(function(jqXHR, textStatus) {
				showMessage('fa-times', 'Oops, there\'s something wrong! Try again!');
			}).done(function(data) {
				if(data != 'in_use'){
					$this.find('input[name="uniqid"]').val(data);
					showMessage('fa-check', 'Portfolio successfully saved!');
				}else{
					showMessage('fa-times', 'The shortcode ID you specified is already in use!');
				}
			});

			return false;
			
		});		

	/* ====================================================================== *
      		TOOTLTIP
 	 * ====================================================================== */

 	 	var top 	= { my: "center bottom-10", at: "center top" };
 	 	var right 	= { my: "left+25 center", at: "right center" };
 		
		$( 'input, select, .media_boxes_options_form .fa-question-circle, .media_boxes_options_form a, span' ).each(function(){
			var $this = $(this);
			$this.tooltip({ 
				position 		: $this.hasClass('tooltip_at_top') ? top : right,
				tooltipClass 	: $this.hasClass('tooltip_at_top') ? 'tooltip_at_top' : '',
				content 		: function () {
	              					return $(this).prop('title');
	          					},
			});	
		});

	/* ====================================================================== *
	    	WAIT UNTIL THE JS IS LOADED
 	 * ====================================================================== */	

		$('.media_boxes_options_page_loader').hide();

 		$('.media_boxes_options_page').fadeIn(500);
	
	});
})(jQuery);