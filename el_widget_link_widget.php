<?php
 
 /**
 * File Download or link widget
 * 
 * used to let users click on a widget and download a selected resource. Or visit a selected page on click
 */
 
 class el_download_widget extends WP_Widget{
 	
	public function __construct(){
			
		$args = array(
			'description'	=> 'Creates a simple download widget, useful for linking users to a single PDF or file'
		);
		
		parent::__construct(
			'el_download_widget', esc_html__('Simple Download / URL Widget', 'ycc'), $args
		);
		
	}

	
	/**
	 * Visual output frontend
	 */
	public function widget($args, $instance){
		
		$title = isset($instance['title']) ? $instance['title'] : '';
		$resource_id = isset($instance['resource_id']) ? $instance['resource_id'] : '';
		$background_id = isset($instance['background_id']) ? $instance['background_id'] : '';
		$background_colour = isset($instance['background_colour']) ? $instance['background_colour'] : '';
		$text_colour = isset($instance['text_colour']) ? $instance['text_colour'] : '';
		$link_url = isset($instance['link_url']) ? $instance['link_url'] : ''; 
		
		$html = '';
		
		$html .= $args['before_widget'];
		
		
			$style = '';
			$style .= !empty($background_colour) ? 'background-color: ' . $background_colour . '; ' : '';
			$style .= !empty($text_colour) ? 'color: ' . $text_colour . '; ' : '';
			
			$html .= '<div class="widget-wrap" style="' . $style . '">';
			
				//widget background
				if(!empty($background_id)){
					$background_url = wp_get_attachment_image_src($background_id, 'medium', false)[0];
					$html .= '<div class="background-image" style="background-image:url(' . $background_url . ');"></div>';
;				}
			
				//title if supplied
				if(isset($instance['title'])){
					$html .= $args['before_title'];
						$html .= $instance['title'];
					$html .= $args['after_title'];	
				}
				
				//main content
				$html .= '<div class="widget-content">';
					//Link to a resource
					if(!empty($resource_id)){
						$resource_url = get_permalink($resource_id);
						
						$style = '';
						$style .= !empty($text_colour) ? 'border-color: ' . $text_colour . '; ' : '';
						$html .= '<a download class="download" style="' . $style . '" href="' . $resource_url . '"><i class="fa fa-angle-down" aria-hidden="true"></i></a>';
					}
					//Link to a URL
					if(!empty($link_url)){
						$style = '';
						$style .= !empty($text_colour) ? 'border-color: ' . $text_colour . '; ' : '';
						$html .= '<a class="download" style="' . $style . '" href="' . $link_url . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
					}
				$html .= '</div>';
			
			$html .= '</div>';
		
		$html .= $args['after_widget'];
		
		
		echo $html;
		
		
	}
	
	/**
	 * Form output on admin
	 */
	public function form($instance){
		
		//enqueue media scripts
		wp_enqueue_media();	

		$title = isset($instance['title']) ? $instance['title'] : '';
		$background_id = isset($instance['background_id']) ? $instance['background_id'] : '';
		$resource_id = isset($instance['resource_id']) ? $instance['resource_id'] : '';
		$background_colour = isset($instance['background_colour']) ? $instance['background_colour'] : '';
		$text_colour = isset($instance['text_colour']) ? $instance['text_colour'] : '';
		$link_url = isset($instance['link_url']) ? $instance['link_url'] : '';
		
		
		$html = '';
		
		//TODO: Come back and adjust this to collect right pallets
		$html .= '<script id="test" type="text/javascript">';
		$html .= 'jQuery(document).ready(function($){
							
				//on update, set colours back up again
				$(document).on("widget-updated", function (event, $widget) {
					console.log("Updasted colour");
					$(".colorpicker-field").wpColorPicker({
						hide: true
					});
				});

			  });';
		$html .= '</script>';
		
		
			
		
		
		$html .= '<p>';
			$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title', 'ycc') .'</label>';
			$html .= '<input class="widefat" type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" value="' . $title .'"/>';
		$html .= '</p>';
		
		//Resources Selection
		$field_name = $this->get_field_name('resource_id');
		$field_id = $this->get_field_id('resource_id');
		$html .= '<p>';
			$html .= '<div class="image-upload-container">';
				$html .= '<label for="' . $field_id . '">' . __('If you want this to link to a resource, select it below <br/>','ycc') . '</label>';
				$html .= '<input type="button" value="Select Resource" class="widget-upload image-upload-button" data-multiple-upload="false" data-file-type="image" data-field-name="' . $field_name .'"/>';
				$html .= '<div class="image-container cf">';
				
				if(!empty($resource_id)){
					$image_url = wp_get_attachment_image_src($resource_id, 'thumbnail', false)[0];
					
					$html .= '<div class="image">';
					$html .=	'<input type="hidden" id="' . $field_id . '" name="' . $field_name . '" value="' .  $resource_id . '"/>';
					$html .=	'<div class="image-preview" style="background-image:url(' . $image_url . ');"></div>';
					$html .=	'<div class="image-controls cf">';
					$html .=		'<div class="control remove_image">Remove Resource<i class="fa fa-minus"></i></div>';	
					$html .=	'</div>';
					$html .= '</div>';
				}
				$html .= '</div>';
					
			$html .= '</div>';
		$html .= '</p>';
		
		//Link URL
		$html .= '<p>';
			$html .= '<label for="' . $this->get_field_id('link_url') . '">' . __('Link URL', 'ycc') .'</label></br>';
			$html .= '<span>If you want this widget instead to link to a URL, enter the full URL here </span>';
			$html .= '<input class="widefat" type="url" name="' . $this->get_field_name('link_url') . '" id="' . $this->get_field_id('link_url') . '" value="' . $link_url .'"/>';
		$html .= '</p>';
		
		//Background Image Selection
		$field_name = $this->get_field_name('background_id');
		$field_id = $this->get_field_id('background_id');
		
		$html .= '<p>';
			$html .= '<div class="image-upload-container">';
				$html .= '<label for="' . $field_id . '">' . __('Select the image that will be used as the background image <br/>','ycc') . '</label>';
				$html .= '<input type="button" value="Select Resource" class="widget-upload image-upload-button" data-multiple-upload="false" data-file-type="image" data-field-name="' . $field_name .'"/>';
				$html .= '<div class="image-container cf">';
				
				if(!empty($background_id)){
					$image_url = wp_get_attachment_image_src($background_id, 'thumbnail', false)[0];
					
					$html .= '<div class="image">';
					$html .=	'<input type="hidden" id="' . $field_id . '" name="' . $field_name . '" value="' .  $background_id . '"/>';
					$html .=	'<div class="image-preview" style="background-image:url(' . $image_url . ');"></div>';
					$html .=	'<div class="image-controls cf">';
					$html .=		'<div class="control remove_image">Remove Resource<i class="fa fa-minus"></i></div>';	
					$html .=	'</div>';
					$html .= '</div>';
				}
				$html .= '</div>';
					
			$html .= '</div>';
		$html .= '</p>';
		
		
		
		
		//Background colour
		$html .= '<p>';
			$html .= '<label for="' . $this->get_field_id('background_colour') . '">' . __('Background Colour', 'ycc') .'</label></br>';
			$html .= '<input class="widefat colorpicker-field" type="text" name="' . $this->get_field_name('background_colour') . '" id="' . $this->get_field_id('background_colour') . '" value="' . $background_colour .'"/>';
		$html .= '</p>';

		//Text Colour
		$html .= '<p>';
			$html .= '<label for="' . $this->get_field_id('text_colour') . '">' . __('Text Colour', 'ycc') .'</label></br>';
			$html .= '<input class="widefat colorpicker-field" type="text" name="' . $this->get_field_name('text_colour') . '" id="' . $this->get_field_id('text_colour') . '" value="' . $text_colour .'"/>';
		$html .= '</p>';
	
	
		
		echo $html;
	}
	
	
	/**
	 * Save callback
	 */
	public function update($new_instance, $old_instance){
		
		$instance = array();
		
		$instance['title'] = isset($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
		$instance['resource_id'] = isset($new_instance['resource_id']) ? sanitize_text_field($new_instance['resource_id']) : '';
		$instance['background_id'] = isset($new_instance['background_id']) ? sanitize_text_field($new_instance['background_id']) : '';
		$instance['background_colour'] = isset($new_instance['background_colour']) ? sanitize_text_field($new_instance['background_colour']) : '';
		$instance['text_colour'] = isset($new_instance['text_colour']) ? sanitize_text_field($new_instance['text_colour']) : '';
		$instance['link_url'] = isset($new_instance['link_url']) ? sanitize_text_field($new_instance['link_url']) : '';
	
	
		return $instance;
		
	}
	
 }



?>