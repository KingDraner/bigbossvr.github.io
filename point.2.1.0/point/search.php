<?php
/**
 * The template for displaying search results pages.
 */

get_header(); ?>

<div id="page" class="home-page">
	<div class="content">
		<div class="article">

			<h1 class="postsby">
				<span><?php esc_html_e( 'Search Results for:', 'point' ); ?></span> <?php the_search_query(); ?>
			</h1>
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

		</div>
		<?php
		get_sidebar();
		get_footer();
		?>
