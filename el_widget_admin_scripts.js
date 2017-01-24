jQuery(document).ready(function($){
	
	
	
	function set_up_widget_colour_picker(){
		
		//Set up the default colourpicker to use our theme colours
		colour_picker_array = true;
		if (typeof el_theme_colours != "undefined") {
			var colour_picker_array = [];
			
			for(var colour in el_theme_colours){
				var colour_value = el_theme_colours[colour]; 
				colour_picker_array.push(colour_value);
			}
		}

		
		/*Admin Color Picker*/
		$('.colorpicker-field').wpColorPicker({
			palettes: colour_picker_array,
			hide: true
		});
	}
	set_up_widget_colour_picker();
	
	
	//image upload admin functionality (used for widgets only)
	function set_up_widget_upload_button(){
		
		$('body').on('click','.widget-upload.image-upload-button', function(){
		
			var upload_button = $(this);
			var images_container = $(this).siblings('.image-container');
			event.preventDefault();
			
			//Determine the type of element to be selected (defaults to all)
			//TODO: actually restrict this input at some point
			var fileType = upload_button.attr('data-file-type'); 
			
			//Determine if multiple-selections allowed
			var multiImage = (upload_button.attr('data-multiple-upload') == 'true') ? true : false;
			
			//create the media frame for the uploader
			file_frame = wp.media.frames.customHeader = wp.media({
				title: (multiImage) ? 'Upload / Select your resources' : 'Upload / Select your resource',
				library: {
					type: 'image'
				},
				button: {
					text: (multiImage) ? 'Select Resources' : 'Select Resource'
				},
				multiple: multiImage
			});
			
			//on select choose from uploader
			file_frame.on('select', function(){
				
				var attachments = file_frame.state().get('selection').toJSON();
				$(attachments).each(function(){
					
					//get attachment object
					var attachment = (this);
					var attachment_id = attachment.id; 
					
					//get properties based on type
					var attachment_type = attachment.type; 
					var attachment_subtype = attachment.subtype; 
					
					//Images
					if(attachment_type == 'image'){
						
						//if sub-type is SVG
						if(attachment_subtype == 'svg+xml'){
							attachment_sample_image = attachment.url;	
						}
						//else normal image, get preview image
						else{
							attachment_sample_image = '';
							if(attachment.sizes.hasOwnProperty('thumbnail')){
								attachment_sample_image = attachment.sizes['thumbnail'].url;
							}else{
								attachment_sample_image = attachment.sizes['full'].url;
							}
							
						}	
					}
					
				
					//image output
					var image = '';
					
					//determine name of hidden field based on supplied data attribute for re-useability
					//needed as we might have several image upload sections on admin back-end
					var field_name = upload_button.attr('data-field-name');
					var name = (multiImage) ? field_name + '[]' : field_name; 
					
					image += '<div class="image">';
					image +=	'<input type="hidden" name="' + name + '" value="' +  attachment_id + '"/>';
					image +=	'<div class="image-preview" style="background-image:url(' + attachment_sample_image + ');"></div>';
					image +=	'<div class="image-controls cf">';
					image +=		'<div class="control remove_image">Remove<i class="fa fa-minus"></i></div>';
					//only need up/down controls on multi
					if(multiImage){
					image +=		'<div class="control image_up">Move Up<i class="fa fa-caret-up"></i></div>';
					image +=		'<div class="control image_down">Move Down<i class="fa fa-caret-down"></i></div>';
					}
					image +=	'</div>';
					image += '</div>';
					
					//remove existing image if not multi image
					if(!multiImage){
						images_container.find('.image').remove();
					}
					//add our new image
					images_container.prepend(image);
	
				});
				
				
			});
			
			file_frame.open();
			
		});
		
		//removes an image when clicking on the remove button
		$('.image-container').on('click', '.remove_image', function(event){
			$(this).parents('.image').remove();
		});	
		
	}
	set_up_widget_upload_button();

	
});
