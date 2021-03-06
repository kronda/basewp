<?php

/**
 * @class FLGalleryModule
 */
class FLGalleryModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __('Gallery', 'fl-builder'),
			'description'   => __('Display multiple photos in a gallery view.', 'fl-builder'),
			'category'      => __('Advanced Modules', 'fl-builder'),
			'editor_export'  => false
		));

		$this->add_js('jquery-wookmark');
		$this->add_js('jquery-mosaicflow');
		$this->add_js('jquery-magnificpopup');
		$this->add_css('jquery-magnificpopup');
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update($settings)
	{
		// Cache the photo data if using the WordPress media library.
		if($settings->source == 'wordpress') {
			$settings->photo_data = $this->get_wordpress_photos();
		}

		return $settings;
	}

	/**
	 * @method get_photos
	 */
	public function get_photos()
	{
		// WordPress
		if($this->settings->source == 'wordpress') {
			return $this->get_wordpress_photos();
		}

		// SmugMug
		if($this->settings->source == 'smugmug') {
			return $this->get_smugmug_photos();
		}
	}

	/**
	 * @method get_wordpress_photos
	 */
	public function get_wordpress_photos()
	{
		$photos     = array();
		$ids        = $this->settings->photos;
		$medium_w   = get_option('medium_size_w');
		$large_w    = get_option('large_size_w');

		if(empty($this->settings->photos)) {
			return $photos;
		}

		foreach($ids as $id) {

			$photo = FLBuilderPhoto::get_attachment_data($id);

			// Use the cache if we didn't get a photo from the id.
			if ( ! $photo ) {
				
				if ( ! isset( $this->settings->photo_data ) ) {
					continue;
				}
				else if ( is_array( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data[ $id ];
				}
				else if ( is_object( $this->settings->photo_data ) ) {
					$photos[ $id ] = $this->settings->photo_data->{$id};
				}
				else {
					continue;
				}
			}

			// Only use photos who have the sizes object.
			if(isset($photo->sizes)) {

				// Photo data object
				$data = new stdClass();
				$data->id = $id;
				$data->alt = $photo->alt;
				$data->caption = $photo->caption;
				$data->description = $photo->description;
				$data->title = $photo->title;

				// Collage photo src
				if($this->settings->layout == 'collage') {

					if($this->settings->photo_size < $medium_w && isset($photo->sizes->medium)) {
						$data->src = $photo->sizes->medium->url;
					}
					else if($this->settings->photo_size <= $large_w && isset($photo->sizes->large)) {
						$data->src = $photo->sizes->large->url;
					}
					else {
						$data->src = $photo->sizes->full->url;
					}
				}

				// Grid photo src
				else {

					if(isset($photo->sizes->thumbnail)) {
						$data->src = $photo->sizes->thumbnail->url;
					}
					else {
						$data->src = $photo->sizes->full->url;
					}
				}

				// Photo Link
				if(isset($photo->sizes->large)) {
					$data->link = $photo->sizes->large->url;
				}
				else {
					$data->link = $photo->sizes->full->url;
				}

				// Push the photo data
				$photos[$id] = $data;
			}
		}

		return $photos;
	}

	/**
	 * @method get_smugmug_photos
	 */
	public function get_smugmug_photos()
	{
		$photos = array();

		// Load the feed into a DOM object.
		$feed = @simplexml_load_file($this->settings->feed_url);

		if($feed !== false) {

			// Get the feed data into an array.
			foreach($feed->channel->item as $item) {

				// SmugMug photo sizes.
				$media = array();

				foreach($item->xpath('media:group/media:content') as $media_content) {
					if($media_content['medium'] == 'image') {
						$media[] = array(
							'height'    => $media_content['height'],
							'width'     => $media_content['width'],
							'url'       => $media_content['url']
						);
					}
				}

				// Only continue if we have media.
				if(count($media) > 0) {

					// Photo link
					if(count($media) <= 3) {
						$link = $media[0]['url'];
					}
					else {
						$link = $media[count($media) - 2]['url'];
					}

					// Photo Src
					if($this->settings->layout == 'collage') {
						for($i = count($media) - 1; $i >= 0; $i--) {
							if($this->settings->photo_size <= $media[$i]['width']) {
								$src = $media[$i]['url'];
							}
						}
					}
					else {
						$src = $media[1]['url'];
					}

					// Photo data object.
					$data = new stdClass();
					$data->alt = $item->title;
					$data->caption = $item->title;
					$data->description = $item->title;
					$data->title = $item->title;
					$data->height = $media[count($media) - 1]['height'];
					$data->width = $media[count($media) - 1]['width'];
					$data->link = $link;
					$data->src = $src;

					// Push the photo data.
					array_push($photos, $data);
				}
			}
		}

		return $photos;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLGalleryModule', array(
	'general'       => array(
		'title'         => __('General', 'fl-builder'),
		'sections'      => array(
			'general'       => array(
				'title'         => '',
				'fields'        => array(
					'layout'        => array(
						'type'          => 'select',
						'label'         => __('Layout', 'fl-builder'),
						'default'       => 'collage',
						'options'       => array(
							'collage'       => __('Collage', 'fl-builder'),
							'grid'          => _x( 'Thumbs', 'Gallery layout: thumbnails.', 'fl-builder' )
						),
						'toggle'        => array(
							'collage'       => array(
								'fields'       => array('photo_size')
							)
						)
					),
					'source'        => array(
						'type'          => 'select',
						'label'         => __('Source', 'fl-builder'),
						'default'       => 'wordpress',
						'options'       => array(
							'wordpress'     => __('Media Library', 'fl-builder'),
							'smugmug'       => 'SmugMug'
						),
						'help'          => __('Pull images from the WordPress media library or a gallery on your SmugMug site by inserting the RSS feed URL from SmugMug. The RSS feed URL can be accessed by using the get a link function in your SmugMug gallery.', 'fl-builder'),
						'toggle'        => array(
							'wordpress'      => array(
								'fields'        => array('photos')
							),
							'smugmug'        => array(
								'fields'        => array('feed_url')
							)
						)
					),
					'photos'        => array(
						'type'          => 'multiple-photos',
						'label'         => __('Photos', 'fl-builder')
					),
					'feed_url'   => array(
						'type'          => 'text',
						'label'         => __('Feed URL', 'fl-builder')
					),
					'photo_size'    => array(
						'type'          => 'select',
						'label'         => __('Photo Size', 'fl-builder'),
						'default'       => '300',
						'options'       => array(
							'200'           => _x( 'Small', 'Photo size.', 'fl-builder' ),
							'300'           => _x( 'Medium', 'Photo size.', 'fl-builder' ),
							'400'           => _x( 'Large', 'Photo size.', 'fl-builder')
						)
					),
					'photo_spacing' => array(
						'type'          => 'text',
						'label'         => __('Photo Spacing', 'fl-builder'),
						'default'       => '20',
						'maxlength'     => '3',
						'size'          => '4',
						'description'   => 'px'
					),
					'show_captions' => array(
						'type'          => 'select',
						'label'         => __('Show Captions', 'fl-builder'),
						'default'       => '0',
						'options'       => array(
							'0'             => __('Never', 'fl-builder'),
							'hover'         => __('On Hover', 'fl-builder'),
							'below'         => __('Below Photo', 'fl-builder')
						),
						'help'          => __('The caption pulls from whatever text you put in the caption area in the media manager for each image. The caption is also pulled directly from SmugMug if you have captions set in your gallery.', 'fl-builder')
					),
					'click_action'  => array(
						'type'          => 'select',
						'label'         => __('Click Action', 'fl-builder'),
						'default'       => 'lightbox',
						'options'       => array(
							'none'          => _x( 'None', 'Click action.', 'fl-builder' ),
							'lightbox'      => __('Lightbox', 'fl-builder'),
							'link'          => __('Photo Link', 'fl-builder')
						),
						'preview'       => array(
							'type'          => 'none'
						)
					)
				)
			)
		)
	)
));