<?php
/**
 * The main template file.
 *
 * Used to display the homepage when home.php doesn't exist.
 */

get_header(); ?>

<div id="page" class="home-page">
	<div class="content">
		<div class="article">

			<h3 class="frontTitle"><div class="latest"><?php esc_html_e( 'Latest', 'point' ); ?></div></h3>

			<?php
			if ( have_posts() ) :
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/content' );
				endwhile;

				point_post_navigation();
			else :
				get_template_part( 'template-parts/content', 'none' );
			endif;
			?>

		</div><!-- .article -->
		<?php
		get_sidebar();
		get_footer();
		?>
