<?php
// One-time demo content and Media Library import

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Import the five portfolio pages and the Ethereum logo
function devenci_import_demo_content() {
	$media  = devenci_import_media();
	$errors = array();

	if ( empty( $media ) ) {
		$errors[] = __( 'The Ethereum logo could not be imported. Check upload directory permissions.', 'devenci' );
	}

	$pages_created = 0;
	$member_ids    = array();

	$member_definitions = array(
		1 => array(
			'title' => 'Tyrone Parreño | Dev Venci',
			'slug'  => 'member-1',
		),
		2 => array(
			'title' => 'Member 2',
			'slug'  => 'member-2',
		),
		3 => array(
			'title' => 'Member 3',
			'slug'  => 'member-3',
		),
		4 => array(
			'title' => 'Member 4',
			'slug'  => 'member-4',
		),
	);

	$site_title = get_option( 'blogname' );
	if ( in_array( $site_title, array( '', 'WordPress', 'My WordPress', 'My Site' ), true ) ) {
		update_option( 'blogname', 'DEV VENCI' );
		update_option( 'blogdescription', 'TeamKU-4A Web3 Portfolios' );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules( false );

	foreach ( $member_definitions as $index => $definition ) {
		$existing = get_page_by_path( $definition['slug'] );
		$template = 1 === $index ? 'member-portfolio' : 'member-portfolio-generic';

		if ( $existing ) {
			$page_id          = (int) $existing->ID;
			$current_template = get_post_meta( $page_id, '_wp_page_template', true );

			if ( ! $current_template || ( $index > 1 && 'member-portfolio' === $current_template ) ) {
				update_post_meta( $page_id, '_wp_page_template', $template );
			}
		} else {
			$data    = 1 === $index ? devenci_member_one_data() : devenci_member_starter_data( $index );
			$page_id = wp_insert_post(
				array(
					'post_title'   => $definition['title'],
					'post_name'    => $definition['slug'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_content' => devenci_member_page_content( $data, $media ),
				),
				true
			);

			if ( is_wp_error( $page_id ) ) {
				continue;
			}

			update_post_meta( $page_id, '_wp_page_template', $template );
			$pages_created++;
		}

		$member_ids[ $index ] = (int) $page_id;
	}

	$home_existing = get_page_by_path( 'home' );

	if ( $home_existing ) {
		$home_id = (int) $home_existing->ID;
	} else {
		flush_rewrite_rules( false );

		$cards = array(
			array(
				'url'   => get_permalink( $member_ids[1] ),
				'image' => isset( $media['member1'] ) ? $media['member1'] : 0,
				'alt'   => 'Tyrone Dev Venci',
				'name'  => 'Tyrone',
				'role'  => 'Dev Venci',
				'delay' => '0s',
			),
			array(
				'url'   => get_permalink( $member_ids[2] ),
				'image' => isset( $media['member2'] ) ? $media['member2'] : 0,
				'alt'   => 'Member 2',
				'name'  => 'Member 2',
				'role'  => 'Role',
				'delay' => '0.6s',
			),
			array(
				'url'   => get_permalink( $member_ids[3] ),
				'image' => isset( $media['member3'] ) ? $media['member3'] : 0,
				'alt'   => 'Member 3',
				'name'  => 'Member 3',
				'role'  => 'Role',
				'delay' => '1.2s',
			),
			array(
				'url'   => get_permalink( $member_ids[4] ),
				'image' => isset( $media['member4'] ) ? $media['member4'] : 0,
				'alt'   => 'Member 4',
				'name'  => 'Member 4',
				'role'  => 'Role',
				'delay' => '1.8s',
			),
		);

		$home_id = wp_insert_post(
			array(
				'post_title'   => 'TeamKU-4A',
				'post_name'    => 'home',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => devenci_home_page_content( $cards ),
			),
			true
		);

		if ( is_wp_error( $home_id ) ) {
			$home_id = 0;
		} else {
			$pages_created++;
		}
	}

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $home_id );
	}

	if ( ! empty( $media['eth_icon'] ) ) {
		set_theme_mod( 'custom_logo', (int) $media['eth_icon'] );
		update_option( 'site_icon', (int) $media['eth_icon'] );
	}

	update_option( 'devenci_demo_imported', time() );
	update_option( 'devenci_demo_home_id', (int) $home_id );
	update_option( 'devenci_demo_member_ids', array_map( 'intval', $member_ids ) );

	return array(
		'pages'  => $pages_created,
		'media'  => count( $media ),
		'errors' => $errors,
	);
}

// Run the demo import once when the theme is activated
function devenci_maybe_import_demo() {
	if ( get_option( 'devenci_demo_imported' ) ) {
		return;
	}

	devenci_import_demo_content();
}
add_action( 'after_switch_theme', 'devenci_maybe_import_demo' );
add_action( 'admin_init', 'devenci_maybe_import_demo' );

// Add a setup screen for re-running the import
function devenci_register_setup_page() {
	add_theme_page(
		__( 'Dev Venci Setup', 'devenci' ),
		__( 'Dev Venci Setup', 'devenci' ),
		'manage_options',
		'devenci-setup',
		'devenci_render_setup_page'
	);
}
add_action( 'admin_menu', 'devenci_register_setup_page' );

// Render the setup screen
function devenci_render_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$report = null;

	if ( isset( $_POST['devenci_import_demo'] ) ) {
		check_admin_referer( 'devenci_import_demo' );
		$report = devenci_import_demo_content();
	}

	$home_id   = (int) get_option( 'devenci_demo_home_id' );
	$member_ids = get_option( 'devenci_demo_member_ids', array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Dev Venci Theme Setup', 'devenci' ); ?></h1>

		<?php if ( is_array( $report ) ) : ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: pages created, 2: images available. */
						esc_html__( 'Import finished. %1$d new pages created and %2$d images available in the Media Library.', 'devenci' ),
						(int) $report['pages'],
						(int) $report['media']
					);
					?>
				</p>
				<?php foreach ( $report['errors'] as $error ) : ?>
					<p><?php echo esc_html( $error ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<p><?php esc_html_e( 'The theme creates a group homepage and four member portfolio pages the first time it is activated. Images are imported into the Media Library so they can be replaced in the Page Editor or Site Editor.', 'devenci' ); ?></p>

		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>"><?php esc_html_e( 'Open Site Editor', 'devenci' ); ?></a>
			<?php if ( $home_id ) : ?>
				<a class="button" href="<?php echo esc_url( get_edit_post_link( $home_id ) ); ?>"><?php esc_html_e( 'Edit Group Homepage', 'devenci' ); ?></a>
				<a class="button" href="<?php echo esc_url( get_permalink( $home_id ) ); ?>"><?php esc_html_e( 'View Homepage', 'devenci' ); ?></a>
			<?php endif; ?>
		</p>

		<?php if ( ! empty( $member_ids ) ) : ?>
			<h2><?php esc_html_e( 'Member Portfolios', 'devenci' ); ?></h2>
			<ul>
				<?php foreach ( $member_ids as $member_id ) : ?>
					<?php if ( get_post( $member_id ) ) : ?>
						<li>
							<a href="<?php echo esc_url( get_edit_post_link( $member_id ) ); ?>"><?php echo esc_html( get_the_title( $member_id ) ); ?></a>
							&nbsp;|&nbsp;
							<a href="<?php echo esc_url( get_permalink( $member_id ) ); ?>"><?php esc_html_e( 'View', 'devenci' ); ?></a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'devenci_import_demo' ); ?>
			<p>
				<button type="submit" class="button" name="devenci_import_demo" value="1"><?php esc_html_e( 'Import Missing Pages and Images', 'devenci' ); ?></button>
			</p>
			<p class="description"><?php esc_html_e( 'Existing pages are left untouched. Only missing pages and attachments are created.', 'devenci' ); ?></p>
		</form>
	</div>
	<?php
}

// Prompt the administrator to run the import if it did not complete
function devenci_import_admin_notice() {
	if ( get_option( 'devenci_demo_imported' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html__( 'Dev Venci has not imported its demo pages yet.', 'devenci' ),
		esc_url( admin_url( 'themes.php?page=devenci-setup' ) ),
		esc_html__( 'Open the setup screen', 'devenci' )
	);
}
add_action( 'admin_notices', 'devenci_import_admin_notice' );
