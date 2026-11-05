<?php
// Skill bar render callback

$label   = isset( $attributes['label'] ) ? $attributes['label'] : '';
$percent = isset( $attributes['percent'] ) ? (int) $attributes['percent'] : 0;
$percent = max( 0, min( 100, $percent ) );
?>
<div class="skill-item">
	<div class="skill-info">
		<span class="skill-label"><?php echo esc_html( $label ); ?></span>
		<span class="skill-value"><?php echo esc_html( $percent . '%' ); ?></span>
	</div>
	<div class="skill-bar">
		<div class="skill-progress" style="width:<?php echo esc_attr( $percent ); ?>%" data-width="<?php echo esc_attr( $percent ); ?>"></div>
	</div>
</div>
