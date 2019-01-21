<?php
/**
 * Template Name: Full Width
 */

get_header();

$point_container = get_theme_mod( 'point_container', 'boxed' );
$content_layout  = get_post_meta( get_the_ID(), '_content_layout', true );
$disable_title   = get_post_meta( get_the_ID(), '_disable_title', true );

if ( 'default' !== $content_layout ) {
	$class = $content_layout;
} elseif ( 'default' !== $point_container ) {
	$class = $point_container;
}
?>
<div id="page" class="single clear">
	<div class="content">
		<article class="article article-full-width">
			<div id="content_box" >
				<?php
				if ( have_posts() ) while ( have_posts() ) :
					the_post();
					?>
					<div id="post-<?php the_ID(); ?>" <?php post_class( 'g post' ); ?>>
						<div class="single_page single_post">
							<?php if ( empty( $disable_title ) ) { ?>
								<header>
									<h1 class="title"><?php the_title(); ?></h1>
								</header>
							<?php } ?>
							<div class="post-content box mark-links">
								<?php
								the_content();

								wp_link_pages(
									array(
										'before'           => '<div class="pagination">',
										'after'            => '</div>',
										'link_before'      => '<span class="current"><span class="currenttext">',
										'link_after'       => '</span></span>',
										'next_or_number'   => 'next_and_number',
										'nextpagelink'     => __( 'Next', 'point' ),
										'previouspagelink' => __( 'Previous', 'point' ),
										'pagelink'         => '%',
										'echo'             => 1,
									)
								);
								?>
							</div><!--.post-content box mark-links-->
						</div>
					</div>
					<?php
					comments_template( '', true );
				endwhile;
				?>
			</div>
		</article>
		<?php
		get_sidebar();
		get_footer();
		?>
