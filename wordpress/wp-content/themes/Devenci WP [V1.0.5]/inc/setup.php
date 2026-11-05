<?php
// Theme supports, assets and global markup

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Register theme supports
function devenci_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 80, 'flex-height' => true, 'flex-width' => true ) );

	add_editor_style(
		array(
			'assets/vendor/fontawesome/css/all.min.css',
			'assets/css/base.css',
			'assets/css/components.css',
			'assets/css/group.css',
			'assets/css/editor.css',
		)
	);
}
add_action( 'after_setup_theme', 'devenci_setup' );

// Register the pattern categories used by the bundled patterns
function devenci_register_pattern_categories() {
	register_block_pattern_category(
		'devenci-group',
		array( 'label' => __( 'Dev Venci: Group Homepage', 'devenci' ) )
	);
	register_block_pattern_category(
		'devenci-portfolio',
		array( 'label' => __( 'Dev Venci: Portfolio Sections', 'devenci' ) )
	);
}
add_action( 'init', 'devenci_register_pattern_categories' );

// Make sure bundled patterns still register in early loaders
function devenci_register_theme_patterns() {
	$theme    = wp_get_theme();
	$registry = WP_Block_Patterns_Registry::get_instance();
	$dirpath  = $theme->get_stylesheet_directory() . '/patterns/';
	$patterns = $theme->get_block_patterns();

	foreach ( $patterns as $file => $pattern_data ) {
		if ( empty( $pattern_data['slug'] ) || $registry->is_registered( $pattern_data['slug'] ) ) {
			continue;
		}

		$file_path = $dirpath . $file;

		if ( ! file_exists( $file_path ) ) {
			continue;
		}

		$pattern_data['filePath'] = $file_path;
		register_block_pattern( $pattern_data['slug'], $pattern_data );
	}
}
add_action( 'init', 'devenci_register_theme_patterns', 20 );

// Enqueue front-end and editor assets
function devenci_enqueue_assets() {
	$theme_uri = get_theme_file_uri();

	wp_enqueue_style( 'devenci-fontawesome', $theme_uri . '/assets/vendor/fontawesome/css/all.min.css', array(), DEVENCI_VERSION );
	wp_enqueue_style( 'devenci-base', $theme_uri . '/assets/css/base.css', array( 'devenci-fontawesome' ), devenci_asset_version( 'assets/css/base.css' ) );
	wp_enqueue_style( 'devenci-components', $theme_uri . '/assets/css/components.css', array( 'devenci-base' ), devenci_asset_version( 'assets/css/components.css' ) );
	wp_enqueue_style( 'devenci-group', $theme_uri . '/assets/css/group.css', array( 'devenci-components' ), devenci_asset_version( 'assets/css/group.css' ) );

	wp_enqueue_script( 'devenci-particles', $theme_uri . '/assets/vendor/particles.min.js', array(), DEVENCI_VERSION, true );
	wp_enqueue_script( 'devenci-gsap', $theme_uri . '/assets/vendor/gsap.min.js', array(), DEVENCI_VERSION, true );
	wp_enqueue_script( 'devenci-typeit', $theme_uri . '/assets/vendor/typeit.min.js', array(), DEVENCI_VERSION, true );
	wp_enqueue_script(
		'devenci-theme',
		$theme_uri . '/assets/js/theme.js',
		array( 'devenci-particles', 'devenci-gsap', 'devenci-typeit' ),
		devenci_asset_version( 'assets/js/theme.js' ),
		true
	);

	wp_localize_script(
		'devenci-theme',
		'devenciData',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'homeUrl'  => home_url( '/' ),
			'musicUrl' => $theme_uri . '/assets/audio/bgmus2.mp3',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'devenci_enqueue_assets' );

// Cache-busting version from file modification time
function devenci_asset_version( $relative_path ) {
	$file = get_theme_file_path( $relative_path );

	return file_exists( $file ) ? (string) filemtime( $file ) : DEVENCI_VERSION;
}

// Fixed background layers from the original design
function devenci_background_layers() {
	echo '<div id="particles-js" aria-hidden="true"></div>';
	echo '<div class="glitch-overlay" aria-hidden="true"></div>';
}
add_action( 'wp_body_open', 'devenci_background_layers', 5 );

// Music player and toast container, matching the original site chrome
function devenci_site_chrome() {
	$music_url = get_theme_file_uri( 'assets/audio/bgmus2.mp3' );
	?>
	<audio id="background-music" loop preload="none">
		<source src="<?php echo esc_url( $music_url ); ?>" type="audio/mpeg">
	</audio>

	<div class="music-control">
		<button id="music-toggle" class="music-btn" type="button" aria-label="<?php esc_attr_e( 'Toggle background music', 'devenci' ); ?>" aria-pressed="false">
			<i class="fa-solid fa-volume-high" id="volume-icon" aria-hidden="true"></i>
		</button>
	</div>

	<div class="toast-container" aria-live="polite" aria-atomic="true">
		<div class="toast" id="wallet-toast">
			<div class="toast-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
			<div class="toast-content">
				<div class="toast-title"><?php esc_html_e( 'Wallet Connected', 'devenci' ); ?></div>
				<div class="toast-message"><?php esc_html_e( 'Your wallet has been successfully connected!', 'devenci' ); ?></div>
			</div>
			<button class="toast-close" type="button" aria-label="<?php esc_attr_e( 'Close notification', 'devenci' ); ?>">
				<i class="fas fa-times" aria-hidden="true"></i>
			</button>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'devenci_site_chrome', 20 );

// Body classes used by the original design
function devenci_body_classes( $classes ) {
	$classes[] = 'devenci-theme';

	if ( is_front_page() ) {
		$classes[] = 'devenci-group-home';
	}

	return $classes;
}
add_filter( 'body_class', 'devenci_body_classes' );
