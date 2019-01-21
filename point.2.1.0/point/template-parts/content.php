<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package point
 */

$point_full_posts = get_theme_mod('point_full_posts');

?>

<article class="<?php echo 'pexcerpt' . $i++ ?> post excerpt <?php echo ( ++$j % 2 == 0 ) ? 'last' : ''; ?>">
	<?php
	if ( empty( $point_full_posts ) ) :
		if ( has_post_thumbnail() ) {
			?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" id="featured-thumbnail">
				<?php
				echo '<div class="featured-thumbnail">';
				the_post_thumbnail( 'featured', array( 'title' => '' ) );
				echo '</div>';
				?>
				<div class="featured-cat">
					<?php
					$category = get_the_category();
					echo esc_attr( $category[0]->cat_name );
					?>
				</div>
				<?php
				if ( function_exists( 'wp_review_show_total' ) ) wp_review_show_total( true, 'latestPost-review-wrapper' );
					?>
			</a>
			<?php
		} else {
			?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" id="featured-thumbnail">
				<div class="featured-thumbnail">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/nothumb.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
				</div>
				<div class="featured-cat"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></div>
				<?php
				if ( function_exists( 'wp_review_show_total' ) ) wp_review_show_total( true, 'latestPost-review-wrapper' );
				?>
			</a>
			<?php
		}
	endif;
	?>
	<header>
		<h2 class="title">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h2>
		<div class="post-info"><span class="theauthor"><?php the_author_posts_link(); ?></span> | <span class="thetime"><?php the_time( get_option( 'date_format' ) ); ?></span></div>
	</header><!--.header-->
	<?php if ( empty( $point_full_posts ) ) : ?>
		<div class="post-content image-caption-format-1">
			<?php echo esc_html( mts_excerpt( 29 ) ); ?>
		</div>
		<span class="readMore"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="nofollow"><?php esc_html_e( 'Read More', 'point' ); ?></a></span>
	<?php else : ?>
		<div class="post-content image-caption-format-1 full-post">
			<?php the_content(); ?>
		</div>
		<?php if ( mts_post_has_moretag() ) : ?>
			<span class="readMore"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="nofollow"><?php esc_html_e( 'Read More', 'point' ); ?></a></span>
		<?php endif; ?>
	<?php endif; ?>
</article>
