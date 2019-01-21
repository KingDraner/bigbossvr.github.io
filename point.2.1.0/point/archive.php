<?php
/**
 * The template for displaying archive pages.
 *
 * Used for displaying archive-type pages. These views can be further customized by
 * creating a separate template for each one.
 *
 * - author.php (Author archive)
 * - category.php (Category archive)
 * - date.php (Date archive)
 * - tag.php (Tag archive)
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */

get_header(); ?>

<div id="page" class="home-page">
	<div class="content">
		<div class="article">
			<h1 class="postsby">
				<span><?php the_archive_title(); ?></span>
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
