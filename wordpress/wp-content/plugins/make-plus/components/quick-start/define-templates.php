<?php
/**
 * @package Make Plus
 */

$images = array(
	'800x600' => array(
		'src'    => '800x600.png',
		'alt'    => __( '800x600 pixel placeholder' ),
		'class'  => '800x600-placeholder placeholder attachment-large',
		'width'  => '800',
		'height' => '600',
	),
	'800x800' => array(
		'src'    => '800x800.png',
		'alt'    => __( '800x800 pixel placeholder' ),
		'class'  => '800x800-placeholder placeholder attachment-large',
		'width'  => '800',
		'height' => '800',
	),
	'800x1067' => array(
		'src'    => '800x1067.png',
		'alt'    => __( '800x1067 pixel placeholder' ),
		'class'  => '800x1067-placeholder placeholder attachment-large',
		'width'  => '800',
		'height' => '1067',
	),
	'960x540' => array(
		'src'    => '960x540.png',
		'alt'    => __( '960x540 pixel placeholder' ),
		'class'  => '960x540-placeholder placeholder attachment-large',
		'width'  => '960',
		'height' => '540',
	),
	'1440x750' => array(
		'src'    => '1440x750.png',
		'alt'    => __( '1440x750 pixel placeholder' ),
		'class'  => '1440x750-placeholder placeholder attachment-large',
		'width'  => '1440',
		'height' => '750',
	),
);

foreach ( $images as $id => $data ) {
	$data['src'] = ttfmp_get_quick_start()->url_base . '/images/' . $data['src'];
	ttfmake_register_placeholder_image( $id, $data );
}

/**
 * About page Quick Start template.
 */

$about_1 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'Kenn Cooper, CEO, Founder',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. It's curious. Ted did figure it out – time travel. And when we get back, we gonna tell everyone. How it's possible, how it's done, what the dangers are. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an ‘unknown entry event'? Why don't they know? If they don't know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400218672729',
	)
);

$about_2 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => 'Sally Woods, Co-founder',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. Fly into your mouth. Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar. The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn't listen.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400218805454',
	)
);

$about_3 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => 'About our studio',
		'title'          => 'About our studio',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. Stroke at the present moment; and yet I feel that I never was a greater artist than now. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees, and but a few stray gleams steal into the inner sanctuary, I throw myself down among the tall grass by the trickling stream; and, as I lie close to the earth, a thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects and flies, then I feel the presence of the Almighty, who formed us in his own image, and the breath.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400219165733',
	)
);

$about_4 = new TTFMP_Section(
	'gallery',
	array(
		'columns'            => 4,
		'caption-color'      => 'light',
		'captions'           => 'reveal',
		'aspect'             => 'portrait',
		'background-image'   => 0,
		'label'              => 'Meet our staff',
		'title'              => 'Meet our staff',
		'darken'             => 0,
		'background-color'   => '',
		'background-style'   => 'tile',
		'gallery-item-order' => array(
			0 => '1400218262861',
			1 => '1400218262868',
			2 => '1400218262874',
			3 => '1400218301649',
			4 => '1400218481620',
			5 => '1400218486284',
			6 => '1400218483660',
		),
		'gallery-items'      => array(
			'1400218262861' => array(
				'title'       => 'Jake Thomas',
				'link'        => '',
				'description' => 'CEO, Founder',
				'image-id'    => '800x1067',
			),
			'1400218262868' => array(
				'title'       => 'Luke McGee',
				'link'        => '',
				'description' => 'Director',
				'image-id'    => '800x1067',
			),
			'1400218262874' => array(
				'title'       => 'Nancy Smith',
				'link'        => '',
				'description' => 'Marketing expert',
				'image-id'    => '800x1067',
			),
			'1400218301649' => array(
				'title'       => 'Sue Williams',
				'link'        => '',
				'description' => 'Customer support',
				'image-id'    => '800x1067',
			),
			'1400218481620' => array(
				'title'       => 'Mike Hall',
				'link'        => '',
				'description' => 'Art director',
				'image-id'    => '800x1067',
			),
			'1400218486284' => array(
				'title'       => 'René Roberts',
				'link'        => '',
				'description' => 'Communications',
				'image-id'    => '800x1067',
			),
			'1400218483660' => array(
				'title'       => 'Flinn Lewis',
				'link'        => '',
				'description' => 'Intern',
				'image-id'    => '800x1067',
			),
		),
		'state'              => 'open',
		'section-type'       => 'gallery',
		'id'                 => '1400218262858',
	)
);

$about_5 = new TTFMP_Section(
	'blank',
	array(
		'label'        => 'Contact us',
		'title'        => 'Contact us',
		'content'      => 'Add your content here. Below is an <a href="https://support.google.com/maps/answer/72644">embedded Google map</a>. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an "unknown entry event"? Why do they not know? If they do not know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive logic.

<iframe style="border: 0;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193572.00379171062!2d-73.97800349999999!3d40.70563080000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY!5e0!3m2!1sen!2s!4v1400218923667" width="600" height="450" frameborder="0"></iframe>',
		'state'        => 'open',
		'section-type' => 'blank',
		'id'           => '1400218889915',
	)
);

$about_template = new TTTFMP_Template(
	'about-page',
	__( 'About', 'make-plus' ),
	array( $about_1, $about_2, $about_3, $about_4, $about_5 )
);

ttfmp_register_template( $about_template );

/**
 * Business page Quick Start template.
 */

$business_1 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => 'Our business',
		'title'          => 'Our business',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '960x540',
				'content'       => '<span style="line-height: 1.55;">Add your content here. One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections. The bedding was hardly able to cover.</span>
<blockquote>Example quote. Why do they not know? If they do not know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive.</blockquote>
One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar. The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text did not listen. She packed her seven versalia, put her initial into the belt and made herself on the way. When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline of her own road, the Line Lane. Pityful a rethoric question ran over her cheek.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400204449885',
	)
);

$business_2 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'Our process',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. It's curious. Ted did figure it out - time travel. And when we get back, we gonna tell everyone. How it's possible, how it's done, what the dangers are. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an 'unknown entry event'? Why don't they know? If they don't know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400204558852',
	)
);

$business_3 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => 'Our achievements',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. She packed her seven versalia, put her initial into the belt and made herself on the way. When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline of her own road, the Line Lane. Pityful a rethoric question ran over her cheek.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400204619686',
	)
);

$business_4 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'Our goals',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. Fly into your mouth. Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life. One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar. The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn’t listen.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400205151287',
	)
);

$business_5 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 3,
		'label'          => 'Meet our team',
		'title'          => 'Meet our team',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'James Smith',
				'image-link'    => 'http://tc1.com',
				'image-id'      => '800x1067',
				'content'       => 'Add your content here. Wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => 'Sarah Bridges',
				'image-link'    => 'http://tc2.com',
				'image-id'      => '800x1067',
				'content'       => "Add your content here. I should be incapable of drawing a single stroke at the present moment; and yet I feel that I never was a greater artist than now. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees, and but a few stray gleams steal into the inner sanctuary, I throw myself down among the tall grass.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => "Tim O'Brien",
				'image-link'    => '#',
				'image-id'      => '800x1067',
				'content'       => 'Add your content here. Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1399320730932',
	)
);

$business_template = new TTTFMP_Template(
	'business-page',
	__( 'Business', 'make-plus' ),
	array( $business_1, $business_2, $business_3, $business_4, $business_5 )
);

ttfmp_register_template( $business_template );

/**
 * Landing page Quick Start template.
 */

$landing_1 = new TTFMP_Section(
	'banner',
	array(
		'label'              => '',
		'title'              => '',
		'hide-arrows'        => 0,
		'hide-dots'          => 0,
		'autoplay'           => 1,
		'transition'         => 'scrollHorz',
		'delay'              => 6000,
		'height'             => 750,
		'banner-slide-order' => array(
			0 => '1400219533713',
			1 => '1400220283266',
		),
		'banner-slides'      => array(
			'1400219533713' => array(
				'content'          => '<h2 style="text-align: center;">Why fifty years in the future when the spacecraft encounters a black hole does the computer call it an "unknown entry event"? If they do not know, that means we never told anyone. And if we never told anyone it means we never made it back.</h2>',
				'background-color' => '',
				'darken'           => 1,
				'image-id'         => '1440x750',
				'alignment'        => 'none',
				'state'            => 'open',
			),
			'1400220283266' => array(
				'content'          => '<h2 style="text-align: center;">The Little Blind Text did not listen. She packed her seven versalia, put her initial into the belt and made herself on the way. When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline.</h2>',
				'background-color' => '',
				'darken'           => 1,
				'image-id'         => '1440x750',
				'alignment'        => 'none',
				'state'            => 'open',
			),
		),
		'state'              => 'open',
		'section-type'       => 'banner',
		'id'                 => '1400219533708',
	)
);

$landing_2 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 3,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 2,
			1 => 1,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => 'Add your content here. Present moment; and yet I feel that I never was a greater artist than now. When, while the lovely valley teems.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => 'Add your content here. With vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees, and but a few stray gleams.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => 'Add your content here. Thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400219839247',
	)
);

$landing_3 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'The window at the dull weather',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => 'Add your content here. From troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections. The bedding was hardly able to cover it and seemed ready to slide off any moment. His many legs, pitifully thin compared with the size of the rest of him, waved about helplessly as he looked. "What has happened to me".

<a class="ttfmake-normal color-primary-background ttfmake-button" href="#"><big>Call to action</big></a>',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400219957572',
	)
);

$landing_4 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => 'Add your content here. At the present moment; and yet I feel that I never was a greater artist than now. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees, and but a few stray gleams steal into the inner sanctuary, I throw myself down among the tall grass by.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400220112219',
	)
);

$landing_5 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'She reached the first hills',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => 'Add your content here. And, as I lie close to the earth, a thousand unknown plants are noticed by me: when I hear the buzz of the little world among the stalks, and grow familiar with the countless indescribable forms of the insects and flies, and the breath. Roasted parts of sentences fly into your mouth. Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name.

<a class="ttfmake-normal color-primary-background ttfmake-button" href="#"><big>Call to action</big></a>',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400220170458',
	)
);

$landing_6 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '<small>Add your content here. This text is wrapped in <code>small</code> tags. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an "unknown entry event"? Why do they not know? If they do not know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here.</small>',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400220387316',
	)
);

$landing_template = new TTTFMP_Template(
	'landing-page',
	__( 'Landing', 'make-plus' ),
	array( $landing_1, $landing_2, $landing_3, $landing_4, $landing_5, $landing_6 )
);

ttfmp_register_template( $landing_template );

/**
 * Creative page Quick Start template.
 */

$creative_1 = new TTFMP_Section(
	'banner',
	array(
		'label'              => '',
		'title'              => '',
		'hide-arrows'        => 0,
		'hide-dots'          => 0,
		'autoplay'           => 1,
		'transition'         => 'scrollHorz',
		'delay'              => 6000,
		'height'             => 600,
		'banner-slide-order' => array(
			0 => '1400280193203',
		),
		'banner-slides'      => array(
			'1400280193203' => array(
				'content'          => '',
				'background-color' => '',
				'darken'           => 0,
				'image-id'         => '1440x750',
				'alignment'        => 'none',
				'state'            => 'open',
			),
		),
		'state'              => 'open',
		'section-type'       => 'banner',
		'id'                 => '1400280193200',
	)
);

$creative_2 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => 'Our mission',
		'title'          => 'Our mission',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Ted did figure it out - time travel. And when we get back, we gonna tell everyone. How it's possible, how it's done, what the dangers are. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an 'unknown entry event'? Why don't they know? If they don't know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive logic.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400280592836',
	)
);

$creative_3 = new TTFMP_Section(
	'gallery',
	array(
		'columns'            => 2,
		'caption-color'      => 'dark',
		'captions'           => 'reveal',
		'aspect'             => 'square',
		'background-image'   => 0,
		'label'              => 'Our work',
		'title'              => 'Our work',
		'darken'             => 0,
		'background-color'   => '',
		'background-style'   => 'tile',
		'gallery-item-order' => array(
			0 => '1400280236552',
			1 => '1400280236560',
			2 => '1400280236566',
			3 => '1400280465778',
			4 => '1400280471521',
			5 => '1400280476578',
			6 => '1400280481444',
			7 => '1400280486357',
		),
		'gallery-items'      => array(
			'1400280236552' => array(
				'title'       => 'Logo design',
				'link'        => '',
				'description' => 'Add your content here. Collection of textile samples lay spread out on the table - Samsa was a travelling salesman - and above it there hung a picture that he had recently cut out of.',
				'image-id'    => '800x800',
			),
			'1400280236560' => array(
				'title'       => 'SEO report',
				'link'        => '',
				'description' => 'Add your content here. One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back.',
				'image-id'    => '800x800',
			),
			'1400280236566' => array(
				'title'       => 'Magazine cover',
				'link'        => '',
				'description' => 'Add your content here. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees.',
				'image-id'    => '800x800',
			),
			'1400280465778' => array(
				'title'       => 'Print project',
				'link'        => '',
				'description' => '',
				'image-id'    => '800x800',
			),
			'1400280471521' => array(
				'title'       => 'Color theory',
				'link'        => '',
				'description' => '',
				'image-id'    => '800x800',
			),
			'1400280476578' => array(
				'title'       => 'Brand design',
				'link'        => '',
				'description' => 'Add your content here. When, while the lovely valley teems with vapour around me, and the meridian sun strikes the upper surface of the impenetrable foliage of my trees.',
				'image-id'    => '800x800',
			),
			'1400280481444' => array(
				'title'       => 'Website design',
				'link'        => '',
				'description' => 'Add your content here. One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back.',
				'image-id'    => '800x800',
			),
			'1400280486357' => array(
				'title'       => 'Style guide',
				'link'        => '',
				'description' => '',
				'image-id'    => '800x800',
			),
		),
		'state'              => 'open',
		'section-type'       => 'gallery',
		'id'                 => '1400280236549',
	)
);

$creative_4 = new TTFMP_Section(
	'text',
	array
	(
		'columns-number' => 3,
		'label'          => 'Our services',
		'title'          => 'Our services',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'Design and development',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => "Advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn't listen. She packed her seven versalia, put her initial into the belt and made herself on the way. When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline of her own road.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => 'Writing and editing',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => "Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth. Even the all-powerful Pointing has no control.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => 'Sales and marketing',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => "Wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents. I should be incapable of drawing a single stroke at the present moment; and yet I feel.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1400280504212',
	)
);

$creative_template = new TTTFMP_Template(
	'creative-page',
	__( 'Creative', 'make-plus' ),
	array( $creative_1, $creative_2, $creative_3, $creative_4 )
);

ttfmp_register_template( $creative_template );

/**
 * Photography page Quick Start template.
 */

$photography_1 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '960x540',
				'content'       => 'Add your content here. It is a paradisematic country, in which roasted parts of sentences fly into your mouth. Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1401409569073',
	)
);

$photography_2 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => 'Services',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. How it's done, what the dangers are. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an 'unknown entry event'? Why don't they know? If they don't know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive logic.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1401409724421',
	)
);

$photography_3 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 2,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => '800x600',
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => 'Experience',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => 'Add your content here. One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1401409787889',
	)
);

$photography_4 = new TTFMP_Section(
	'text',
	array(
		'columns'            => 1,
		'caption-color'      => 'light',
		'captions'           => 'none',
		'aspect'             => 'none',
		'background-image'   => 0,
		'label'              => 'My latest work',
		'title'              => 'My latest work',
		'darken'             => 0,
		'background-color'   => '',
		'background-style'   => 'tile',
		'gallery-item-order' => array(
			0 => '1401409662162',
			1 => '1401409662168',
			2 => '1401409662175',
		),
		'gallery-items'      => array(
			'1401409662162' => array(
				'title'       => '',
				'link'        => '',
				'description' => '',
				'image-id'    => '960x540',
			),
			'1401409662168' => array(
				'title'       => '',
				'link'        => '',
				'description' => '',
				'image-id'    => '960x540',
			),
			'1401409662175' => array(
				'title'       => '',
				'link'        => '',
				'description' => '',
				'image-id'    => '960x540',
			),
		),
		'state'              => 'open',
		'section-type'       => 'gallery',
		'id'                 => '1401409662159',
	)
);

$photography_template = new TTTFMP_Template(
	'photography-page',
	__( 'Photography', 'make-plus' ),
	array( $photography_1, $photography_2, $photography_3, $photography_4 )
);

ttfmp_register_template( $photography_template );

/**
 * Shop page Quick Start template.
 */

$shop_1 = new TTFMP_Section(
	'banner',
	array(
		'label'              => '',
		'title'              => '',
		'hide-arrows'        => 0,
		'hide-dots'          => 0,
		'autoplay'           => 1,
		'transition'         => 'scrollHorz',
		'delay'              => 6000,
		'height'             => 600,
		'banner-slide-order' => array(
			0 => '1401408503135',
		),
		'banner-slides'      => array(
			'1401408503135' => array(
				'content'          => '',
				'background-color' => '',
				'darken'           => 0,
				'image-id'         => '1440x750',
				'alignment'        => 'none',
				'state'            => 'open',
			),
		),
		'state'              => 'open',
		'section-type'       => 'banner',
		'id'                 => '1401408503131',
	)
);

$shop_2 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => 'Our latest products',
		'title'          => 'Our latest products',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => "Add your content here. How it's done, what the dangers are. But then why fifty years in the future when the spacecraft encounters a black hole does the computer call it an 'unknown entry event'? Why don't they know? If they don't know, that means we never told anyone. And if we never told anyone it means we never made it back. Hence we die down here. Just as a matter of deductive logic.",
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1401409160142',
	)
);

$shop_3 = new TTFMP_Section(
	'productgrid',
	array(
		'label'        => '',
		'title'        => '',
		'columns'      => 3,
		'type'         => 'all',
		'taxonomy'     => 'all',
		'sortby'       => 'date',
		'count'        => 12,
		'thumb'        => 1,
		'rating'       => 0,
		'price'        => 1,
		'addcart'      => 0,
		'state'        => 'open',
		'section-type' => 'productgrid',
		'id'           => '1401408539303',
	)
);

$shop_4 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => 'Products on sale',
		'title'          => 'Products on sale',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => 'Add your content here. One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections.',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),
		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1401408674055',
	)
);

$shop_5 = new TTFMP_Section(
	'productgrid',
	array(
		'label'        => '',
		'title'        => '',
		'columns'      => 4,
		'type'         => 'sale',
		'taxonomy'     => 'all',
		'sortby'       => 'price',
		'count'        => 8,
		'thumb'        => 1,
		'rating'       => 0,
		'price'        => 1,
		'addcart'      => 0,
		'state'        => 'open',
		'section-type' => 'productgrid',
		'id'           => '1401408609756',
	)
);

$shop_6 = new TTFMP_Section(
	'text',
	array(
		'columns-number' => 1,
		'label'          => '',
		'title'          => '',
		'columns-order'  => array(
			0 => 1,
			1 => 2,
			2 => 3,
			3 => 4,
		),
		'columns'        => array(
			1 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '<h5>Terms of use</h5><small>Add your content here. Wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart. I am alone, and feel the charm of existence in this spot, which was created for the bliss of souls like mine. I am so happy, my dear friend, so absorbed in the exquisite sense of mere tranquil existence, that I neglect my talents.</small>',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			2 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			3 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
			4 => array(
				'title'         => '',
				'image-link'    => '',
				'image-id'      => 0,
				'content'       => '',
				'widget-area'   => 0,
				'sidebar-label' => '',
			),
		),

		'state'          => 'open',
		'section-type'   => 'text',
		'id'             => '1401409246783',
	)
);

$shop_template = new TTTFMP_Template(
	'shop-page',
	__( 'Shop', 'make-plus' ),
	array( $shop_1, $shop_2, $shop_3, $shop_4, $shop_5, $shop_6 )
);

// Only register the template if WooCommerce is installed
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	ttfmp_register_template( $shop_template );
}
