<?php
/**
 * Single post layout
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<article id="post-<?php the_ID(); ?>">
<?php echo do_shortcode( '[nsaa_get_meeting id="' . get_the_ID() . '"]' ); ?>
</article>
