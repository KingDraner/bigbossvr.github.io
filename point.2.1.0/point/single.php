<?php
/**
 * The template for displaying all single posts.
 */
get_header();
$point_single_adcode            = get_theme_mod( 'point_single_adcode' );
$point_single_adcode_days       = get_theme_mod( 'point_single_adcode_days', '0' );
$point_single_adcode_below      = get_theme_mod( 'point_single_adcode_below' );
$point_single_adcode_days_below = get_theme_mod( 'point_single_adcode_days_below', '0' );
$point_single_tags_section      = get_theme_mod( 'point_single_tags_section', '1' );
$point_authorbox_section        = get_theme_mod( 'point_authorbox_section', '1' );
$point_relatedposts_section     = get_theme_mod( 'point_relatedposts_section', '1' );
$disable_title                  = get_post_meta( get_the_ID(), '_disable_title', true );
$disable_post_meta              = get_post_meta( get_the_ID(), '_disable_post_meta', true );
$disable_author_box             = get_post_meta( get_the_ID(), '_disable_author_box', true );
$disable_related_posts          = get_post_meta( get_the_ID(), '_disable_related_posts', true );

?>
<div id="page" class="single clear">
	<div class="content">
		<!-- Start Article -->
		<article class="article">
			<?php
			if ( have_posts() ) while ( have_posts() ) :
				the_post();
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
					<div class="single_post">
						<?php if ( empty( $disable_title ) || empty( $disable_post_meta ) ) { ?>
							<header>
								<?php if ( empty( $disable_title ) ) { ?>
									<h1 class="title single-title"><?php the_title(); ?></h1>
								<?php } ?>
								<?php if ( empty( $disable_post_meta ) ) { ?>
									<div class="post-info"><span class="theauthor"><?php the_author_posts_link(); ?></span> | <span class="thetime"><?php the_time( get_option( 'date_format' ) ); ?></span> | <span class="thecategory"><?php the_category( ', ' ); ?></span> | <span class="thecomment"><a href="<?php comments_link(); ?>"><?php comments_number(); ?></a></span></div>
								<?php } ?>
							</header>
						<?php } ?>
						<!-- Start Content -->
						<div class="post-single-content box mark-links">
							<?php
							if ( ! empty( $point_single_adcode ) && empty( $disable_post_meta ) ) {
								$toptime = $point_single_adcode_days;
								if (strcmp( date( 'Y-m-d', strtotime( "-$toptime day" ) ), get_the_time( 'Y-m-d' ) ) >= 0 ) {
									?>
									<div class="topad">
										<?php echo $point_single_adcode; ?>
									</div>
									<?php
								}
							}

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

							if ( $point_single_adcode_below && empty( $disable_related_posts ) ) {
								$endtime = $point_single_adcode_days_below;

								if (strcmp( date( 'Y-m-d', strtotime( "-$endtime day" ) ), get_the_time( 'Y-m-d' ) ) >= 0 ) {
									?>
									<div class="bottomad">
										<?php echo $point_single_adcode_below;?>
									</div>
									<?php
								}
							}

							if ( '1' === $point_single_tags_section && empty( $disable_related_posts ) ) {
								?>
								<!-- Start Tags -->
								<div class="tags"><?php the_tags( '<span class="tagtext">' . __( 'Tags', 'point' ) . ':</span>', ', ' ); ?></div>
								<!-- End Tags -->
								<?php
							}
							?>
						</div>
						<!-- End Content -->
						<?php
						if ( empty( $disable_related_posts ) ) {
							if ( '1' === $point_relatedposts_section ) {
								// Start Related Posts.
								$categories = get_the_category( $post->ID );
								if ( $categories ) {
									$category_ids = array();
									foreach ( $categories as $individual_category ) {
										$category_ids[] = $individual_category->term_id;
									}
									$args = array(
										'category__in' => $category_ids,
										'post__not_in' => array( $post->ID ),
										'ignore_sticky_posts' => 1,
										'showposts'    => 4,
										'orderby'      => 'rand',
									);

									$my_query = new wp_query( $args );

									if ( $my_query->have_posts() ) {
										echo '<div class="related-posts"><h3>' . __( 'Related Posts', 'point' ) . '</h3><div class="postauthor-top"><ul>';
										$pexcerpt = 1;
										$j        = 0;
										$counter  = 0;

										while ( $my_query->have_posts() ) {
											++$counter;
											if ( 4 === $counter ) {
												$postclass = 'last';
												$counter   = 0;
											} else {
												$postclass = '';
											}
											$my_query->the_post();
											?>
											<li class="<?php echo esc_attr( $postclass ); ?> rpexcerpt<?php echo esc_attr( $pexcerpt ); ?> <?php echo ( ++$j % 2 == 0 ) ? 'last' : ''; ?>">
												<a class="relatedthumb" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>">
													<span class="rthumb">
														<?php
														if ( has_post_thumbnail() ) :
															the_post_thumbnail( 'widgetthumb', 'title=' );
														else :
															?>
															<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/smallthumb.png" alt="<?php the_title(); ?>" class="wp-post-image" />
														<?php endif; ?>
													</span>
													<span>
														<?php the_title(); ?>
													</span>
												</a>
												<div class="meta">
													<a href="<?php comments_link(); ?>" rel="nofollow"><?php comments_number(); ?></a> | <span class="thetime"><?php the_time( 'M j, Y' ); ?></span>
												</div> <!--end .entry-meta-->
											</li>
											<?php
											$pexcerpt++;
										}
										echo '</ul></div></div>';
									}
								}
								wp_reset_postdata();
								// End Related Posts.
							}
						}

						if ( empty( $disable_author_box ) ) {
							if ( '1' === $point_authorbox_section ) {
								?>
								<!-- Start Author Box -->
								<div class="postauthor-container">
									<h4><?php esc_html_e( 'About The Author', 'point' ); ?></h4>
									<div class="postauthor">
										<?php
										if ( function_exists( 'get_avatar' ) ) :
											echo get_avatar( get_the_author_meta( 'email' ), '100' );
										endif;
										?>
										<h5><?php the_author_meta( 'nickname' ); ?></h5>
										<p><?php the_author_meta( 'description' ); ?></p>
									</div>
								</div>
								<!-- End Author Box -->
								<?php
							}
						}
						?>
					</div>
				</div>
				<?php
				comments_template( '', true );
			endwhile;
			?>
		</article>
		<?php
		get_sidebar();
		get_footer();
		?>
