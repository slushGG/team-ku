<?php
// Filterable portfolio render callback

$inner_blocks = isset( $block->parsed_block['innerBlocks'] ) ? $block->parsed_block['innerBlocks'] : array();
$categories   = array();

foreach ( $inner_blocks as $inner_block ) {
	if ( 'devenci/portfolio-item' !== $inner_block['blockName'] ) {
		continue;
	}

	$category = isset( $inner_block['attrs']['category'] ) ? trim( (string) $inner_block['attrs']['category'] ) : '';

	if ( '' === $category ) {
		continue;
	}

	$slug = sanitize_title( $category );

	if ( ! isset( $categories[ $slug ] ) ) {
		$categories[ $slug ] = $category;
	}
}
?>
<div class="devenci-portfolio">
	<div class="portfolio-filter" role="group" aria-label="<?php esc_attr_e( 'Filter portfolio items', 'devenci' ); ?>">
		<button type="button" class="filter-btn active" data-filter="all"><?php esc_html_e( 'All', 'devenci' ); ?></button>
		<?php foreach ( $categories as $slug => $label ) : ?>
			<button type="button" class="filter-btn" data-filter="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="portfolio-grid">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered inner blocks. ?>
	</div>
</div>
