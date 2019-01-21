<?php
/**
 * The template for displaying the footer.
 */

$carousel_section = get_theme_mod( 'point_carousel_section', '1' );
$carousel_cats    = get_theme_mod( 'point_carousel_cat' );

$disable_footer = '';


if ( is_singular() ) {
	$disable_footer = get_post_meta( get_the_ID(), '_disable_footer', true );
}

?>
	</div><!-- .content -->
</div><!-- #page -->

<?php if ( empty( $disable_footer ) ) { ?>
	<footer>
		<?php if ( ( '1' === $carousel_section && ! empty( $carousel_cats ) ) ) { ?>
			<div class="carousel">
				<h3 class="frontTitle">
					<div class="latest">
						<?php echo esc_html( $carousel_cats[0] ); ?>
					</div>
				</h3>
				<?php
				$carousel_cat = implode( ',', $carousel_cats );
				$i            = 1;
				$my_query     = new wp_query( 'category_name=' . $carousel_cat . '&posts_per_page=6&ignore_sticky_posts=1' );
				while ( $my_query->have_posts() ) :
					$my_query->the_post();
					?>
					<div class="excerpt">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" id="footer-thumbnail">
							<div>
								<div class="hover"><i class="point-icon icon-zoom-in"></i></div>
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'carousel', array( 'title' => '' ) ); ?>
								<?php } else { ?>
									<div class="featured-thumbnail">
										<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/footerthumb.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
									</div>
								<?php } ?>
							</div>
							<p class="footer-title">
								<span class="featured-title"><?php the_title(); ?></span>
							</p>
						</a>
					</div><!--.post excerpt-->
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php } ?>
	</footer><!--footer-->
	<?php
	mts_copyrights_credit();
}

wp_footer();
?>
</div><!-- main-container -->

</body>
</html>
