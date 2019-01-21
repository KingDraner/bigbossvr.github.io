<?php
/**
 * The template for displaying the header.
 *
 * Displays everything from the doctype declaration down to the navigation.
 */

$point_button_section  = get_theme_mod( 'point_button_section', '1' );
$point_button_bg_color = get_theme_mod( 'point_button_bg_color' );
$point_button_text     = get_theme_mod( 'point_button_text', 'Download!' );
$point_button_url      = get_theme_mod( 'point_button_url' );
$point_bottom_text     = get_theme_mod( 'point_bottom_text', 'Download Point responsive WP Theme for FREE!' );
$point_header_ad_code  = get_theme_mod( 'point_header_ad_code' );
$trending_section      = get_theme_mod( 'point_trending_section', '1' );
$trending_cat_names    = get_theme_mod( 'point_trending_cat', '1' );
$featured_section      = get_theme_mod( 'point_feature_setting', '1' );
$feature_cat_names     = get_theme_mod( 'point_feature_cat', '1' );
$custom_sidebar        = point_custom_sidebar();
$container             = point_conatiner();

$show_header  = '';
if ( is_singular() ) {
	$show_header = get_post_meta( get_the_ID(), '_disable_header', true );
}

$class = $container . ' ' . $custom_sidebar;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body id="blog" <?php body_class( $class ); ?>>

<div class="main-container clear">

	<?php
	if ( empty( $show_header ) ) {
		if ( '1' === $trending_section && isset( $trending_cat_names ) ) {
			?>
			<div class="trending-articles">
				<ul>
					<li class="firstlink"><?php esc_html_e( 'Now Trending', 'point' ); ?>:</li>
					<?php
					if ( isset( $trending_cat_names ) ) {
						// prevent implode error.
						if ( empty( $trending_cat_names ) || ! is_array( $trending_cat_names ) ) {
							$trending_cat_names = array( '0' );
						}
						$trending_cat_name = implode( ',', $trending_cat_names );
						$my_query          = new wp_query( 'category_name=' . $trending_cat_name . '&posts_per_page=4&ignore_sticky_posts=1' );
						if ( $my_query->have_posts() ) :
							while (
								$my_query->have_posts() ) :
								$my_query->the_post();
								?>
								<li class="trendingPost">
									<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php mts_short_title( '...', 24 ); ?></a>
								</li>
								<?php
							endwhile;
						endif;
						wp_reset_postdata();
					}
					?>
				</ul>
			</div>
			<?php
		}

		$header_image = get_header_image();
		?>
		<header id="masthead" class="site-header" role="banner">
			<div class="site-branding">
				<?php
				if ( ! empty( $header_image ) ) {
					?>
					<?php if ( is_front_page() || is_home() || is_404() ) { ?>
						<h1 id="logo" class="image-logo" itemprop="headline">
							<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php header_image(); ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
						</h1><!-- END #logo -->
					<?php } else { ?>
						<h2 id="logo" class="image-logo" itemprop="headline">
							<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php header_image(); ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
						</h2><!-- END #logo -->
					<?php } ?>
				<?php } else { ?>
					<?php if ( is_front_page() || is_home() || is_404() ) { ?>
						<h1 id="logo" class="text-logo" itemprop="headline">
							<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
						</h1><!-- END #logo -->
					<?php } else { ?>
						<h2 id="logo" class="text-logo" itemprop="headline">
							<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
						</h2><!-- END #logo -->
					<?php } ?>
				<?php } ?>

				<a href="#" id="pull" class="toggle-mobile-menu"><?php esc_html_e( 'Menu', 'point' ); ?></a>
				<div class="primary-navigation">
					<nav id="navigation" class="mobile-menu-wrapper" role="navigation">
						<?php
						if ( has_nav_menu( 'primary' ) ) {
							wp_nav_menu(
								array(
									'theme_location' => 'primary',
									'menu_class'     => 'menu clearfix',
									'container'      => '',
									'walker'         => new mts_Walker(),
								)
							);
						} else {
							?>
							<ul class="menu clearfix">
								<?php wp_list_categories( 'title_li=' ); ?>
							</ul>
						<?php } ?>
					</nav><!-- #navigation -->
				</div><!-- .primary-navigation -->
			</div><!-- .site-branding -->
		</header><!-- #masthead -->

		<?php if ( ! empty( $point_header_ad_code ) ) { ?>
			<div class="header-bottom-second">
				<div id="header-widget-container">
					<div class="widget-header">
						<?php echo $point_header_ad_code; ?>
					</div>
					<div class="widget-header-bottom-right">
						<div class="textwidget">
							<div class="topad">
								<a href="<?php echo esc_url( $point_button_url ); ?>" class="header-button"><?php echo esc_attr( $point_button_text ); ?></a><?php echo esc_attr( $point_bottom_text ); ?>
							</div>
						</div>
					</div><!-- .widget-header-bottom-right -->
				</div><!-- #header-widget-container -->
			</div><!-- .header-bottom-second -->
			<?php
		}
	}

	if ( '1' === $featured_section && isset( $feature_cat_names ) ) {
		if ( is_home() && ! is_paged() ) {
			?>
			<div class="featuredBox">
				<?php
				$i = 1;
				// prevent implode error.
				if ( empty( $feature_cat_names ) || ! is_array( $feature_cat_names ) ) {
					$feature_cat_names = array( '0' );
				}
				$feature_cat_name = implode( ',', $feature_cat_names );
				$featured_query   = new WP_Query( 'category_name=' . $feature_cat_name . '&posts_per_page=4' );
				while ( $featured_query->have_posts() ) :
					$featured_query->the_post();
					?>
					<?php if ( 1 === $i ) { ?>
						<div class="firstpost excerpt">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" id="first-thumbnail">
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'bigthumb', array( 'title' => '' ) ); ?>
								<?php } else { ?>
									<div class="featured-thumbnail">
										<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/bigthumb.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
									</div>
								<?php } ?>
								<p class="featured-excerpt">
									<span class="featured-title"><?php the_title(); ?></span>
									<span class="f-excerpt"><?php echo esc_html( mts_excerpt( 10 ) ); ?></span>
								</p>
							</a>
						</div><!--.post excerpt-->
					<?php } elseif ( 2 === $i ) { ?>
						<div class="secondpost excerpt">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" id="second-thumbnail">
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'mediumthumb', array( 'title' => '' ) ); ?>
								<?php } else { ?>
									<div class="featured-thumbnail">
										<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/mediumthumb.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
									</div>
								<?php } ?>
								<p class="featured-excerpt">
									<span class="featured-title"><?php the_title(); ?></span>
								</p>
							</a>
						</div><!--.post excerpt-->
					<?php } elseif ( 3 === $i || 4 === $i ) { ?>
						<div class="thirdpost excerpt">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" id="third-thumbnail">
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'smallthumb', array( 'title' => '' ) ); ?>
								<?php } else { ?>
									<div class="featured-thumbnail">
										<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/images/smallfthumb.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
									</div>
								<?php } ?>
								<p class="featured-excerpt">
									<span class="featured-title"><?php the_title(); ?></span>
								</p>
							</a>
						</div><!--.post excerpt-->
					<?php
					}
					$i++;
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php
		}
	}
	?>
