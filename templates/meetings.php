<?php
/**
 * The template for displaying all pages, single posts and attachments
 *
 * This is a new template file that WordPress introduced in
 * version 4.3.
 *
 * @package OceanWP WordPress theme
 */

get_header(); ?>

	<?php //do_action( 'ocean_before_content_wrap' ); ?>

	<div id="content-wrap" class="container clr">

		<?php //do_action( 'ocean_before_primary' ); ?>

		<div id="primary" class="content-area clr">

			<?php //do_action( 'ocean_before_content' ); ?>

			<div id="content" class="site-content clr">

				<?php //do_action( 'ocean_before_content_inner' ); ?>

				<?php
				// Elementor `single` location
//				if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
					
					// Start loop
					if ( have_posts() ) : the_post();

                            the_content();
                            
                    endif;

//				} ?>

				<?php //do_action( 'ocean_after_content_inner' ); ?>

			</div><!-- #content -->

			<?php //do_action( 'ocean_after_content' ); ?>

		</div><!-- #primary -->

		<?php //do_action( 'ocean_after_primary' ); ?>

	</div><!-- #content-wrap -->

	<?php //do_action( 'ocean_after_content_wrap' ); ?>

<?php get_footer(); ?>
