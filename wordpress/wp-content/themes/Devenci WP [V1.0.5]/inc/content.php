<?php
// Block markup builders and the original portfolio content

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Encode block attributes
function devenci_block_attrs( $attributes ) {
	return wp_json_encode( $attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
}

// Build a paragraph block
function devenci_paragraph( $text, $class = '' ) {
	$attrs      = $class ? array( 'className' => $class ) : array();
	$attributes = $attrs ? ' ' . devenci_block_attrs( $attrs ) : '';
	$class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';

	return '<!-- wp:paragraph' . $attributes . ' --><p' . $class_attr . '>' . $text . '</p><!-- /wp:paragraph -->';
}

// Build a heading block
function devenci_heading( $text, $level = 2, $class = '' ) {
	$attrs = array( 'level' => (int) $level );
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$class_attr = $class ? ' class="wp-block-heading ' . esc_attr( $class ) . '"' : ' class="wp-block-heading"';

	return '<!-- wp:heading ' . devenci_block_attrs( $attrs ) . ' --><h' . (int) $level . $class_attr . '>' . $text . '</h' . (int) $level . '><!-- /wp:heading -->';
}

// Build a group block
function devenci_group( $inner, $class = '', $tag = 'div', $anchor = '', $extra = array() ) {
	$attrs          = $extra;
	$attrs['tagName'] = $tag;
	if ( $class ) {
		$attrs['className'] = $class;
	}
	if ( $anchor ) {
		$attrs['anchor'] = $anchor;
	}
	$attrs['layout'] = array( 'type' => 'default' );

	$id_attr    = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';
	$class_attr = 'wp-block-group' . ( $class ? ' ' . $class : '' );

	return '<!-- wp:group ' . devenci_block_attrs( $attrs ) . " -->\n<" . $tag . $id_attr . ' class="' . esc_attr( $class_attr ) . "\">\n" . $inner . "\n</" . $tag . ">\n<!-- /wp:group -->";
}

// Build a numbered section group
function devenci_section( $id, $class, $inner ) {
	return devenci_group( $inner, trim( 'section devenci-section ' . $class ), 'section', $id );
}

// Wrap content in the 1200px container used by the original CSS
function devenci_container( $inner ) {
	return devenci_group( $inner, 'container' );
}

// Build a centred section header with the CSS separator
function devenci_section_header( $title, $level = 2 ) {
	return devenci_group( devenci_heading( $title, $level, 'section-title glitch-title' ), 'section-header' );
}

// Build an image block from a Media Library attachment
function devenci_image_block( $attachment_id, $class = '', $alt = '' ) {
	$attachment_id = (int) $attachment_id;
	$url           = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'full' ) : '';

	if ( ! $url ) {
		$placeholder = 'Add an image';

		if ( 'profile-hex' === $class ) {
			$placeholder = 'Add your profile image';
		} elseif ( 'image-wrapper' === $class ) {
			$placeholder = 'Add your about image';
		} elseif ( 'blog-img' === $class ) {
			$placeholder = 'Add your article image';
		}

		$attrs        = $class ? ' ' . devenci_block_attrs( array( 'className' => $class ) ) : '';
		$figure_class = 'wp-block-image' . ( $class ? ' ' . $class : '' );

		return devenci_paragraph( esc_html( $placeholder ), 'image-placeholder-text' )
			. "\n<!-- wp:image" . $attrs . " -->\n<figure class=\"" . esc_attr( $figure_class ) . "\"><img alt=\"\"/></figure>\n<!-- /wp:image -->";
	}

	$attrs = array(
		'id'              => $attachment_id,
		'sizeSlug'        => 'full',
		'linkDestination' => 'none',
	);
	if ( $class ) {
		$attrs['className'] = $class;
	}

	$figure_class = 'wp-block-image size-full' . ( $class ? ' ' . $class : '' );
	$img_class    = 'wp-image-' . $attachment_id;

	return '<!-- wp:image ' . devenci_block_attrs( $attrs ) . " -->\n<figure class=\"" . esc_attr( $figure_class ) . '"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="' . esc_attr( $img_class ) . "\"/></figure>\n<!-- /wp:image -->";
}

// Build a button block
function devenci_button( $text, $url, $class = '' ) {
	$attrs      = $class ? array( 'className' => $class ) : array();
	$attributes = $attrs ? ' ' . devenci_block_attrs( $attrs ) : '';
	$div_class  = 'wp-block-button' . ( $class ? ' ' . $class : '' );

	return '<!-- wp:button' . $attributes . ' --><div class="' . esc_attr( $div_class ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . $text . '</a></div><!-- /wp:button -->';
}

// Build a buttons group
function devenci_buttons( $inner, $class = '' ) {
	$attrs      = $class ? array( 'className' => $class ) : array();
	$attributes = $attrs ? ' ' . devenci_block_attrs( $attrs ) : '';

	return '<!-- wp:buttons' . $attributes . ' --><div class="wp-block-buttons' . ( $class ? ' ' . $class : '' ) . '">' . $inner . '</div><!-- /wp:buttons -->';
}

// Build columns
function devenci_columns( $columns, $class = '' ) {
	$attrs      = $class ? array( 'className' => $class ) : array();
	$attributes = $attrs ? ' ' . devenci_block_attrs( $attrs ) : '';
	$inner      = '';

	foreach ( $columns as $column ) {
		$inner .= '<!-- wp:column --><div class="wp-block-column">' . $column . '</div><!-- /wp:column -->';
	}

	return '<!-- wp:columns' . $attributes . ' --><div class="wp-block-columns' . ( $class ? ' ' . $class : '' ) . '">' . $inner . '</div><!-- /wp:columns -->';
}

// Build a raw HTML block
function devenci_html( $html ) {
	return '<!-- wp:html -->' . $html . '<!-- /wp:html -->';
}

// Build a social links block
function devenci_social_links( $links ) {
	$inner = '';

	foreach ( $links as $link ) {
		$service = isset( $link['service'] ) ? $link['service'] : ( isset( $link[0] ) ? $link[0] : '' );
		$url     = isset( $link['url'] ) ? $link['url'] : ( isset( $link[1] ) ? $link[1] : '' );

		$inner .= '<!-- wp:social-link ' . devenci_block_attrs(
			array(
				'url'     => $url,
				'service' => $service,
			)
		) . ' /-->';
	}

	return '<!-- wp:social-links {"className":"social-links"} --><ul class="wp-block-social-links social-links">' . $inner . '</ul><!-- /wp:social-links -->';
}

// Original member 1 content
function devenci_member_one_data() {
	return array(
		'name'              => 'Tyrone Parreño',
		'role'              => 'Dev Venci',
		'profile'           => 'profile',
		'about_image'       => 'about',
		'hero_subtitle'     => "Hi There! I'm a",
		'hero_title'        => 'WEB3 ENTHUSIAST',
		'intro'             => 'Passionate about building and growing Web3 communities while contributing to the future of decentralized ecosystems. Specializing in NFT trading analysis, community management, and Web3 engagement strategies.',
		'experience_number' => '2',
		'experience_text'   => 'Years of Experience',
		'about_paragraphs'  => array(
			"I'm a passionate Web3 enthusiast with 2 years of hands-on experience in the blockchain ecosystem. My journey began with active participation in crypto communities, where I developed expertise in managing and growing Web3 communities.",
			"Beyond development, I'm an experienced trader specializing in meme coins and NFTs, with a keen eye for market trends and opportunities. I combine technical knowledge with trading experience to navigate the dynamic Web3 landscape effectively.",
		),
		'info'              => array(
			array( 'Name:', 'Tyrone Parreño | Dev Venci' ),
			array( 'Email:', 'tyrone.web3@proton.me' ),
			array( 'Experience:', '2 Years' ),
			array( 'Specialty:', 'Blockchain' ),
			array( 'Location:', 'Philippines, Opol' ),
		),
		'services'          => array(
			array( 'users', 'Community Management', 'Experienced community building and engagement strategies for Web3 projects. Grow and manage active communities around your blockchain project.' ),
			array( 'chart-line', 'Crypto Trading', 'Specialized trading strategies for meme coins and NFTs. Market analysis and trading signals to maximize your crypto investments.' ),
			array( 'palette', 'NFT Development', 'NFT solutions from smart contracts to marketplaces. Create, deploy, and trade non-fungible tokens on various blockchains.' ),
			array( 'comments', 'Crypto Community Building', 'Expert strategies for growing and engaging Web3 communities. From Discord setup to community engagement campaigns and influencer partnerships.' ),
			array( 'chart-pie', 'NFT Trading Analysis', 'Comprehensive NFT market analysis including floor price tracking, rarity scoring, and trading strategy development for collections.' ),
			array( 'graduation-cap', 'Web3 Consultation', 'Strategic consultation on blockchain implementation, tokenomics design, and Web3 transition for businesses and startups.' ),
		),
		'portfolio'         => array(
			array( 'plume', 'Plume NFT mint project', 'Community', "Plume's Goon NFT mint for the Testers", 'Contributed to the growth of the Plume community by inviting 1k–5k new users.', 'https://x.com/plumenetwork' ),
			array( 'bonk', 'Meme coin trading chart', 'Analysis', 'Meme Coin Trading', 'Achieved 10x–1000x ROI by strategically sniping meme coins.', 'https://dexscreener.com/' ),
			array( 'jaks', 'Jackson Sharkz NFT project', 'NFT', 'Jackson Sharkz', 'Guided and onboarded a significant number of users during the Jackson NFT airdrop.', 'https://x.com/jackson_app' ),
			array( 'signad', 'Sign Protocol community project', 'Community', 'Sign Protocol Influencer Partnerships', 'Collaborated with co-sign influencers to strategically grow the Sign Protocol community.', 'https://x.com/ethsign' ),
		),
		'education'         => array(
			array( '2024 - 2027', 'BS in Information Technology', 'Opol Community College', 'Gaining comprehensive knowledge in technology and development coding to enhance my Web3 expertise.' ),
		),
		'experience'        => array(
			array( '2024 - Present', 'Web3 Community Manager', 'Web3 Creator', 'Managed and grew Web3 communities, developed engagement strategies, and coordinated influencer partnerships for NFT projects.' ),
			array( '2024 - Present', 'Trading Analyst', 'Market Insights', 'Provided TREND market analysis, price tracking, and trading strategy development.' ),
		),
		'skill_columns'     => array(
			array(
				array( 'Community Management', 95 ),
				array( 'NFT Trading Analysis', 90 ),
				array( 'Market Trend Analysis', 80 ),
			),
			array(
				array( 'NFT Market Dynamics', 95 ),
				array( 'Community Growth Strategies', 90 ),
				array( 'Influencer Partnerships', 85 ),
				array( 'Tokenomics Analysis', 80 ),
			),
		),
		'blog'              => array(
			array( 'mtr', 'Memecoin trading analysis article', 'April 15, 2025', 'Dev Venci', 'Memecoin Trading Analysis', 'Memecoins like Dogecoin, Shiba Inu, and the newer generation such as PEPE and WIF have shown that viral momentum can outperform fundamentals…', 'https://open.substack.com/pub/devvenci/p/memecoin-trading-analysis' ),
			array( 'trendm', 'Crypto market trend article', 'February 21, 2025', 'Dev Venci', 'Market Trend', 'The broader crypto market is currently in a consolidation phase following a sharp bull run in Q1 2025. Bitcoin and Ethereum are holding key support levels, while Layer 2 tokens…', 'https://open.substack.com/pub/devvenci/p/market-trend' ),
			array( 'web12', 'How Web3 is expanding article', 'January 26, 2025', 'Dev Venci', 'How Web3 Is Expanding', "Web3 continues to evolve beyond just decentralized finance. We're now seeing significant adoption in gaming (GameFi), social media (decentralized social platforms)…", 'https://open.substack.com/pub/devvenci/p/how-web3-is-expanding' ),
		),
		'contact'           => array(
			'location' => 'Opol, Mis OR, Philippines',
			'email'    => 'tyrone.web3@proton.me',
			'phone'    => '+639 750 443 410',
			'socials'  => array(
				array( 'x', 'https://x.com/venciiOnweb3_' ),
				array( 'github', 'https://github.com/580019' ),
				array( 'linkedin', 'http://www.linkedin.com/in/tyroneweb3' ),
				array( 'discord', 'https://discord.com/users/1326148612836818993' ),
				array( 'telegram', 'https://t.me/DEVVenci' ),
			),
		),
	);
}

// Starter content for the other member portfolios
function devenci_member_starter_data( $index ) {
	return array(
		'name'              => 'Member ' . $index,
		'role'              => 'Role',
		'profile'           => '',
		'about_image'       => '',
		'hero_subtitle'     => "Hi There! I'm",
		'hero_title'        => 'MEMBER ' . $index,
		'intro'             => 'Replace this introduction with a short summary of who you are, what you do, and the kind of work you enjoy.',
		'experience_number' => '0',
		'experience_text'   => 'Years of Experience',
		'about_paragraphs'  => array(
			'Use this space to tell visitors about your background, the projects you have worked on, and what you are currently learning.',
			'Add a second paragraph with the work you enjoy most, the tools you use, and the kind of collaborations you are looking for.',
		),
		'info'              => array(
			array( 'Name:', 'Member ' . $index ),
			array( 'Email:', 'you@example.com' ),
			array( 'Experience:', 'Add years' ),
			array( 'Specialty:', 'Add specialty' ),
			array( 'Location:', 'Add location' ),
		),
		'services'          => array(
			array( 'users', 'Add a Skill', 'Describe this skill and the value you bring to a team or project.' ),
			array( 'chart-line', 'Add a Skill', 'Describe this skill and the value you bring to a team or project.' ),
			array( 'palette', 'Add a Skill', 'Describe this skill and the value you bring to a team or project.' ),
			array( 'comments', 'Add a Skill', 'Describe this skill and the value you bring to a team or project.' ),
			array( 'chart-pie', 'Add a Skill', 'Describe this skill and the value you bring to a team or project.' ),
			array( 'graduation-cap', 'Add a Skill', 'Describe this skill and the value you bring to a team or project.' ),
		),
		'portfolio'         => array(
			array( '', 'Project image', 'Project', 'Project One', 'Describe the project, your role, and the result. Add an image from the Media Library.', '' ),
			array( '', 'Project image', 'Project', 'Project Two', 'Describe the project, your role, and the result. Add an image from the Media Library.', '' ),
			array( '', 'Project image', 'Project', 'Project Three', 'Describe the project, your role, and the result. Add an image from the Media Library.', '' ),
			array( '', 'Project image', 'Project', 'Project Four', 'Describe the project, your role, and the result. Add an image from the Media Library.', '' ),
		),
		'education'         => array(
			array( 'Add dates', 'Your Degree or Course', 'Your School', 'Add a short description of what you studied and how it supports your work.' ),
		),
		'experience'        => array(
			array( 'Add dates', 'Your Role', 'Your Project', 'Describe your responsibilities, the team you worked with, and the outcome.' ),
			array( 'Add dates', 'Your Role', 'Your Project', 'Describe your responsibilities, the team you worked with, and the outcome.' ),
		),
		'skill_columns'     => array(
			array(
				array( 'Skill One', 80 ),
				array( 'Skill Two', 75 ),
			),
			array(
				array( 'Skill Three', 70 ),
				array( 'Skill Four', 65 ),
			),
		),
		'blog'              => array(
			array( '', 'Article image', 'Add date', 'Member ' . $index, 'Article One', 'Add a short excerpt for this article and link it to the full post.', '' ),
			array( '', 'Article image', 'Add date', 'Member ' . $index, 'Article Two', 'Add a short excerpt for this article and link it to the full post.', '' ),
			array( '', 'Article image', 'Add date', 'Member ' . $index, 'Article Three', 'Add a short excerpt for this article and link it to the full post.', '' ),
		),
		'contact'           => array(
			'location' => 'Add your location',
			'email'    => 'you@example.com',
			'phone'    => 'Add your phone number',
			'socials'  => array(
				array( 'x', 'https://x.com' ),
				array( 'github', 'https://github.com' ),
				array( 'linkedin', 'https://www.linkedin.com' ),
			),
		),
	);
}

// Build a full member portfolio page
function devenci_member_page_content( $data, $media ) {
	$media_id = static function ( $key ) use ( $media ) {
		return isset( $media[ $key ] ) ? (int) $media[ $key ] : 0;
	};

	/* Home / hero. */
	$hero = devenci_group(
		devenci_heading( $data['hero_subtitle'], 3, 'subtitle typing-subtitle' )
		. devenci_heading( $data['hero_title'], 1, 'glitch-title' )
		. devenci_image_block( $media_id( $data['profile'] ), 'profile-hex', $data['name'] )
		. devenci_paragraph( $data['intro'], 'typing-text' )
		. devenci_buttons(
			devenci_button( 'Learn More', '#about', 'btn primary-btn icon-arrow' )
			. devenci_button( 'View Portfolio', '#portfolio', 'btn secondary-btn icon-briefcase' ),
			'cta-buttons'
		),
		'home-content'
	);

	$home_section = devenci_section(
		'home',
		'section-home devenci-home',
		devenci_container( $hero )
		. devenci_html(
			'<div class="scroll-indicator" aria-hidden="true"><span class="mouse"><span class="wheel"></span></span><span class="scroll-text">Scroll Down</span></div>'
		)
	);

	/* About. */
	$about_info = '';
	foreach ( $data['info'] as $info ) {
		$about_info .= devenci_paragraph( '<strong>' . esc_html( $info[0] ) . '</strong> ' . esc_html( $info[1] ), 'info-item' );
	}

	$about_image = devenci_group(
		devenci_image_block( $media_id( $data['about_image'] ), 'image-wrapper', $data['name'] )
		. devenci_group(
			devenci_heading( $data['experience_number'], 3, 'experience-number' )
			. devenci_paragraph( $data['experience_text'], 'experience-text' ),
			'experience-box'
		),
		'about-image'
	);

	$about_text = devenci_group(
		devenci_paragraph( $data['about_paragraphs'][0] )
		. devenci_paragraph( $data['about_paragraphs'][1] )
		. devenci_group( $about_info, 'about-info' )
		. devenci_buttons( devenci_button( 'Contact Me', '#contact', 'btn primary-btn icon-paper-plane' ), 'about-cta' ),
		'about-text'
	);

	$about_section = devenci_section(
		'about',
		'section-about',
		devenci_container(
			devenci_section_header( 'ABOUT ME' )
			. devenci_columns( array( $about_image, $about_text ), 'about-content' )
		)
	);

	/* Services / skills cards. */
	$service_cards = '';
	foreach ( $data['services'] as $service ) {
		$service_cards .= devenci_group(
			devenci_html( '<div class="service-icon"><i class="fas fa-' . esc_attr( $service[0] ) . '" aria-hidden="true"></i></div>' )
			. devenci_heading( $service[1], 3, 'service-title' )
			. devenci_paragraph( $service[2], 'service-desc' ),
			'service-card'
		);
	}

	$services_section = devenci_section(
		'services',
		'section-services',
		devenci_container(
			devenci_section_header( 'MY SKILLS' )
			. devenci_group( $service_cards, 'services-grid' )
		)
	);

	/* Portfolio. */
	$portfolio_items = '';
	foreach ( $data['portfolio'] as $item ) {
		$portfolio_items .= '<!-- wp:devenci/portfolio-item ' . devenci_block_attrs(
			array(
				'category' => $item[2],
				'imageId'  => $media_id( $item[0] ),
				'imageAlt' => $item[1],
				'link'     => $item[5],
			)
		) . " -->\n"
			. '<!-- wp:heading {"level":3,"className":"portfolio-title"} --><h3 class="wp-block-heading portfolio-title">' . esc_html( $item[3] ) . '</h3><!-- /wp:heading -->'
			. '<!-- wp:paragraph {"className":"portfolio-category"} --><p class="portfolio-category">' . esc_html( $item[4] ) . '</p><!-- /wp:paragraph -->'
			. "\n<!-- /wp:devenci/portfolio-item -->\n";
	}

	$portfolio_section = devenci_section(
		'portfolio',
		'section-portfolio',
		devenci_container(
			devenci_section_header( 'MY PORTFOLIO' )
			. '<!-- wp:devenci/portfolio-grid -->' . "\n" . $portfolio_items . '<!-- /wp:devenci/portfolio-grid -->'
		)
	);

	/* Resume + technical skills. */
	$timeline = static function ( $items ) {
		$html = '';
		foreach ( $items as $item ) {
			$html .= devenci_group(
				devenci_paragraph( esc_html( $item[0] ), 'resume-date' )
				. devenci_heading( esc_html( $item[1] ), 4, 'resume-title' )
				. devenci_paragraph( esc_html( $item[2] ), 'resume-company' )
				. devenci_paragraph( esc_html( $item[3] ), 'resume-desc' ),
				'resume-item'
			);
		}

		return devenci_group( $html, 'resume-timeline' );
	};

	$skill_columns = array();
	foreach ( $data['skill_columns'] as $column ) {
		$bars = '';
		foreach ( $column as $skill ) {
			$bars .= '<!-- wp:devenci/skill-bar ' . devenci_block_attrs(
				array(
					'label'   => $skill[0],
					'percent' => (int) $skill[1],
				)
			) . ' /-->';
		}
		$skill_columns[] = devenci_group( $bars, 'skill-category' );
	}

	$resume_section = devenci_section(
		'resume',
		'section-resume',
		devenci_container(
			devenci_section_header( 'MY RESUME' )
			. devenci_columns(
				array(
					devenci_heading( 'Education', 3, 'resume-section-title' ) . $timeline( $data['education'] ),
					devenci_heading( 'Experience', 3, 'resume-section-title' ) . $timeline( $data['experience'] ),
				),
				'resume-container'
			)
			. devenci_group(
				devenci_heading( 'Technical Skills', 3, 'skills-title' )
				. devenci_columns( $skill_columns, 'skills-grid' ),
				'skills-content'
			)
		)
	);

	/* Blog. */
	$blog_cards = '';
	foreach ( $data['blog'] as $post ) {
		$read_more = $post[6]
			? devenci_paragraph( '<a href="' . esc_url( $post[6] ) . '">Read More</a>', 'blog-link' )
			: '';

		$blog_cards .= devenci_group(
			devenci_image_block( $media_id( $post[0] ), 'blog-img', $post[1] )
			. devenci_group(
				devenci_html( '<div class="blog-meta"><span><i class="far fa-calendar" aria-hidden="true"></i> ' . esc_html( $post[2] ) . '</span><span><i class="far fa-user" aria-hidden="true"></i> ' . esc_html( $post[3] ) . '</span></div>' )
				. devenci_heading( $post[4], 3, 'blog-title' )
				. devenci_paragraph( $post[5], 'blog-desc' )
				. $read_more,
				'blog-content'
			),
			'blog-card'
		);
	}

	$blog_section = devenci_section(
		'blog',
		'section-blog',
		devenci_container(
			devenci_section_header( 'LATEST ARTICLES' )
			. devenci_group( $blog_cards, 'blog-grid' )
		)
	);

	/* Contact. */
	$contact_items = '';
	$contact_icons = array(
		'location' => 'map-marker-alt',
		'email'    => 'envelope',
		'phone'    => 'phone-alt',
	);
	$contact_labels = array(
		'location' => 'Location',
		'email'    => 'Email',
		'phone'    => 'Phone',
	);

	foreach ( array( 'location', 'email', 'phone' ) as $key ) {
		$contact_items .= devenci_group(
			devenci_html( '<div class="contact-icon"><i class="fas fa-' . esc_attr( $contact_icons[ $key ] ) . '" aria-hidden="true"></i></div>' )
			. devenci_group(
				devenci_heading( $contact_labels[ $key ], 4 )
				. devenci_paragraph( esc_html( $data['contact'][ $key ] ) ),
				'contact-text'
			),
			'contact-item'
		);
	}

	$contact_info = devenci_group(
		devenci_heading( "Let's Connect", 3 )
		. devenci_group( $contact_items, 'contact-details' )
		. devenci_heading( 'Follow Me', 3 )
		. devenci_social_links( $data['contact']['socials'] ),
		'contact-info'
	);

	$contact_section = devenci_section(
		'contact',
		'section-contact',
		devenci_container(
			devenci_section_header( 'CONTACT ME' )
			. devenci_columns(
				array(
					$contact_info,
					'<!-- wp:devenci/contact-form /-->',
				),
				'contact-content'
			)
		)
	);

	return $home_section . $about_section . $services_section . $portfolio_section . $resume_section . $blog_section . $contact_section;
}

// Build the group homepage
function devenci_home_page_content( $cards ) {
	$members = '';

	foreach ( $cards as $card ) {
		$members .= '<!-- wp:devenci/member-card ' . devenci_block_attrs(
			array(
				'url'      => $card['url'],
				'imageId'  => (int) $card['image'],
				'imageAlt' => $card['alt'],
				'delay'    => $card['delay'],
			)
		) . " -->\n"
			. devenci_paragraph( esc_html( $card['name'] ) . '<em>' . esc_html( $card['role'] ) . '</em>' )
			. "\n<!-- /wp:devenci/member-card -->\n";
	}

	$hero = devenci_group(
		devenci_heading( 'Meet the', 3, 'subtitle typing-subtitle' )
		. devenci_heading( 'TEAM-KU 4A', 1, 'glitch-title' )
		. devenci_paragraph( 'Pick a member to explore their portfolio.', 'group-tagline' ),
		'group-intro'
	);

	return devenci_group(
		$hero . devenci_group( $members, 'members' ),
		'group-main'
	);
}
