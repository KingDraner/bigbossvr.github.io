<?php

define( 'RMU_PLUGIN_FILE', 'seo-by-rank-math/rank-math.php' );
define( 'RMU_PLUGIN_SLUG', 'seo-by-rank-math' );

$active_plugins = get_option( 'active_plugins' );
$rm_installed   = in_array( RMU_PLUGIN_FILE, $active_plugins, true );
define( 'RMU_INSTALLED', $rm_installed );

/**
 * Suggest Rank Math SEO in notices.
 */
class MTS_RMU {

	private static $instance;
	public $config = array();
	public $plugin;

	private function __construct( $config = array() ) {
		$config_defaults = array(

			'link_label_install'      => __( 'Try it for FREE!', 'point' ),
			'link_label_activate'     => __( 'Click here to activate it.', 'point' ),

			'show_metabox_notice'     => true,

			'add_dashboard_widget'    => true,

			/* Translators: %s is CTA, e.g. "Try it now!" */
			'metabox_notice_install'  => sprintf( __( 'The new %1$s plugin will help you rank better in the search results.', 'point' ), '<a href="https://mythemeshop.com/plugins/wordpress-seo/?utm_source=SEO+Meta+Box&utm_medium=Link+CPC&utm_content=Rank+Math+SEO+LP&utm_campaign=UserBackend" target="_blank">Rank Math SEO</a>' ) . ' @CTA',

			/* Translators: %s is CTA, e.g. "Try it now!" */
			'metabox_notice_activate' => sprintf( __( 'The %1$s plugin is installed but not activated.', 'point' ), '<a href="https://mythemeshop.com/plugins/wordpress-seo/?utm_source=SEO+Meta+Box&utm_medium=Link+CPC&utm_content=Rank+Math+SEO+LP&utm_campaign=UserBackend" target="_blank">Rank Math SEO</a>' ) . ' @CTA',

			// Add a message in Yoast & AIO metaboxes.
			'show_competitor_notice'  => true,
			'competitor_notice'       =>
					'<span class="dashicons dashicons-lightbulb"></span>
								 <span class="mts-ctad-question">' .
							__( 'Did you know?', 'point' ) . '
								</span>
								<span class="mts-ctad">' .
							sprintf( __( 'The new %1$s plugin can make your site load faster, offers more features, and can import your current SEO settings with one click.', 'point' ), '<a href="https://mythemeshop.com/plugins/wordpress-seo/?utm_source=@SOURCE&utm_medium=Link+CPC&utm_content=Rank+Math+SEO+LP&utm_campaign=UserBackend" target="_blank">Rank Math SEO</a>' ) . '
								</span>' . ' @CTA',
		);

		$this->config = $config_defaults;

		// Apply constructor config.
		$this->config( $config );

		$this->add_hooks();
	}

	public function add_hooks() {

		// The rest doesn't need to run when RM is installed already
		// Or if user doesn't have the capability to install plugins.
		if ( RMU_INSTALLED || ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		add_action( 'wp_ajax_rmu_dismiss', array( $this, 'ajax_dismiss_notice' ) );

		if ( $this->get_setting( 'show_competitor_notice' ) ) {
			$active_plugins = get_option( 'active_plugins' );
			if ( in_array( 'wordpress-seo/wp-seo.php', $active_plugins, true ) ) {
				// Add message in Yoast meta box.
				add_action( 'admin_print_footer_scripts-post-new.php', array( $this, 'inject_yoast_notice' ) );
				add_action( 'admin_print_footer_scripts-post.php', array( $this, 'inject_yoast_notice' ) );
			} elseif ( in_array( 'all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins, true ) ) {
				// Add message in AIOSEO meta box.
				add_action( 'admin_print_footer_scripts-post-new.php', array( $this, 'inject_aioseo_notice' ) );
				add_action( 'admin_print_footer_scripts-post.php', array( $this, 'inject_aioseo_notice' ) );
			}
		}

		if ( $this->get_setting( 'show_metabox_notice' ) ) {
			$active_plugins = get_option( 'active_plugins' );
			if ( ! in_array( 'wordpress-seo/wp-seo.php', $active_plugins, true ) && ! in_array( 'all-in-one-seo-pack/all_in_one_seo_pack.php', $active_plugins, true ) ) {
				// Add dummy SEO meta box with link to install/activate RM.
				add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			}
		}

		if ( $this->get_setting( 'add_theme_options_tab' ) ) {
			// Add new tab in Theme Options.
			add_filter( 'mts_options_sections', array( $this, 'add_theme_options_seo_tab_content' ) );
			add_filter( 'mts_options_menus', array( $this, 'add_theme_options_seo_tab' ) );
		}

		if ( $this->get_setting( 'add_dashboard_widget' ) ) {
			// Add new tab in Theme Options.
			add_filter( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ), 99 );
		}

	}

	public function add_dashboard_widget() {
		if ( $this->is_dismissed( 'dashboard_panel' ) ) {
			return;
		}

		wp_add_dashboard_widget( 'rmu_dashboard_widget', __( 'Rank Math SEO', 'point' ), array( $this, 'dashboard_widget_output' ) );
	}

	public function dashboard_widget_output( $post, $callback_args ) {
		?>
			<div class="rmu-dashboard-panel">
				<a class="rmu-dashboard-panel-close" id="rmu-dashboard-dismiss" href="http://cyprus.local/wp-admin/?rmu-dashboard=0""><?php _e( 'Dismiss', 'point' ); ?></a>
				<div class="rmu-dashboard-panel-content">
					<p>
					<?php
					$plugins      = array_keys( get_plugins() );
					$rm_installed = in_array( RMU_PLUGIN_FILE, $plugins, true );

					if ( $rm_installed ) {
						echo strtr( $this->get_setting( 'metabox_notice_activate' ), array( '@CTA' => $this->get_activate_link() ) );
					} else {
						echo strtr( $this->get_setting( 'metabox_notice_install' ), array( '@CTA' => $this->get_install_link() ) );
					}
					?>
					</p>
				</div>
			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$('#rmu_dashboard_widget').insertAfter('.wrap > h1');
					$( '#rmu-dashboard-dismiss' ).click(function(event) {
							event.preventDefault();
							$( '#rmu_dashboard_widget' ).slideUp();
							$.ajax({
									url: ajaxurl,
									type: 'GET',
									data: { action: 'rmu_dismiss', n: 'dashboard_panel' },
							});
					});
				});
			</script>
			<style type="text/css">
				#rmu_dashboard_widget {
					margin-top: 20px;
				}
				#rmu_dashboard_widget .inside {
					margin: 0;
					padding: 0;
				}
				#rmu_dashboard_widget .hndle {
					display: none;
				}
				.rmu-dashboard-panel .rmu-dashboard-panel-close:before {
					background: 0 0;
					color: #72777c;
					content: "\f153";
					display: block;
					font: 400 16px/20px dashicons;
					speak: none;
					height: 20px;
					text-align: center;
					width: 20px;
					-webkit-font-smoothing: antialiased;
					-moz-osx-font-smoothing: grayscale
				}

				#rmu_dashboard_widget {
					position: relative;
					overflow: auto;
					border-left: 4px solid #ffba00;
					background: #fffbee;
					padding: 0;
					box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
					margin: 10px 0;
					line-height: 1.8;
				}

				.rmu-dashboard-panel h2 {
					margin: 0;
					font-size: 21px;
					font-weight: 400;
					line-height: 1.2
				}

				.rmu-dashboard-panel h3 {
					margin: 17px 0 0;
					font-size: 16px;
					line-height: 1.4
				}

				.rmu-dashboard-panel li {
					font-size: 14px
				}

				.rmu-dashboard-panel p {
					color: #72777c
				}

				.rmu-dashboard-action a {
					text-decoration: none
				}

				.rmu-dashboard-panel .about-description {
					font-size: 16px;
					margin: 0
				}

				.rmu-dashboard-panel-content hr {
					margin: 20px -23px 0;
					border-top: 1px solid #f3f4f5;
					border-bottom: none
				}

				.rmu-dashboard-panel .rmu-dashboard-panel-close {
					position: absolute;
					z-index: 10;
					top: 0;
					right: 10px;
					padding: 0 15px 10px 21px;
					font-size: 13px;
					line-height: 1.23076923;
					text-decoration: none
				}

				.rmu-dashboard-panel .rmu-dashboard-panel-close:before {
					position: absolute;
					top: 0;
					left: 0;
					transition: all .1s ease-in-out
				}

				.rmu-dashboard-panel-content {
					margin: 0 13px;
					max-width: 1500px
				}

				.mts-ctad-question {
						font-weight: bold;
				}
			</style>
			<?php
	}

	public function add_meta_boxes() {
		if ( $this->is_dismissed( 'seo_meta_box' ) ) {
			return;
		}

		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return;
		}

		$post_types = get_post_types( array( 'public' => true ) );
		add_meta_box( 'rm-upsell-metabox', 'SEO', array( $this, 'meta_box_content' ), $post_types, 'advanced', 'high' );
	}

	public function meta_box_content() {
		$plugins      = array_keys( get_plugins() );
		$rm_installed = in_array( RMU_PLUGIN_FILE, $plugins, true );
		?>
				<div id="mts-rm-upsell-metabox">
						<?php
						if ( $rm_installed ) {
							echo strtr( $this->get_setting( 'metabox_notice_activate' ), array( '@CTA' => $this->get_activate_link() ) );
						} else {
							echo strtr( $this->get_setting( 'metabox_notice_install' ), array( '@CTA' => $this->get_install_link() ) );
						}
						?>
						<a href="#" id="mts-rm-upsell-dismiss"><span class="dashicons dashicons-no-alt"></span></a>
				</div>
				<script type="text/javascript">
						jQuery(window).load(function() {
								var $ = jQuery;
								$( '#mts-rm-upsell-dismiss' ).click(function(event) {
										event.preventDefault();
										$( '#rm-upsell-metabox' ).fadeOut( '400' );
										$.ajax({
												url: ajaxurl,
												type: 'GET',
												data: { action: 'rmu_dismiss', n: 'seo_meta_box' },
										});
								});
						});
				</script>
				<style type="text/css">
						#mts-rm-upsell-metabox {
								border-left: 4px solid #ffba00;
								background: #fffbee;
								padding: 12px 24px 12px 12px;
								box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
								margin: 10px 0 0;
								line-height: 1.8;
								position: relative;
								z-index: 1;
						}
						#mts-rm-upsell-dismiss {
								display: block;
								position: absolute;
								right: 12px;
								top: 24px;
								top: calc(50% - 12px);
								text-decoration: none;
								color: #444;
						}
						.mts-ctad-question {
								font-weight: bold;
						}
				</style>
				<?php
	}

	public static function init( $config = array() ) {
		if ( self::$instance === null ) {
			self::$instance = new MTS_RMU( $config );
		} else {
			self::$instance->config( $config );
		}

		return self::$instance;
	}

	public function config( $configuration, $value = null ) {
		if ( is_string( $configuration ) && $value !== null ) {
			$this->config[ $configuration ] = $value;
			return;
		}

		$this->config = array_merge( $this->config, $configuration );
	}

	public function get_setting( $setting ) {
		if ( isset( $this->config[ $setting ] ) ) {
			return $this->config[ $setting ];
		}
		return null;
	}

	public function dismiss_notice( $notice ) {
		$current            = (array) get_user_meta( get_current_user_id(), 'rmu_dismiss', true );
		$current[ $notice ] = '1';
		update_user_meta( get_current_user_id(), 'rmu_dismiss', $current );
	}

	public function is_dismissed( $notice ) {
		$current = (array) get_user_meta( get_current_user_id(), 'rmu_dismiss', true );
		return ( ! empty( $current[ $notice ] ) );
	}

	public function ajax_dismiss_notice() {
		$notice = sanitize_title( wp_unslash( $_GET['n'] ) );
		$this->dismiss_notice( $notice );
		exit;
	}

	public function inject_metabox_notice( $plugin_name, $selector, $metabox_dependency ) {
		$plugin = sanitize_title( $plugin_name );
		if ( $this->is_dismissed( $plugin ) ) {
			return;
		}

		if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
			return;
		}
		?>
				<div style="display: none;" id="mts-rm-upsell-notice">
						<?php echo $this->get_competitor_notice( $plugin_name ); ?>
						<a href="#" id="mts-rm-upsell-dismiss"><span class="dashicons dashicons-no-alt"></span></a>
				</div>
				<script type="text/javascript">
						jQuery(window).load(function() {
								var $ = jQuery;
								if ( $( '<?php echo $metabox_dependency; ?>' ).length ) {
										$( '#mts-rm-upsell-notice' ).<?php echo $selector; ?>.show();
										$( '#mts-rm-upsell-dismiss' ).click(function(event) {
												event.preventDefault();
												$( '#mts-rm-upsell-notice' ).fadeOut( '400' );
												$.ajax({
														url: ajaxurl,
														type: 'GET',
														data: { action: 'rmu_dismiss', n: '<?php echo $plugin; ?>' },
												});
										});

								}
						});
				</script>
				<?php echo $this->get_notice_css(); ?>
				<?php
	}

	public function inject_yoast_gutenberg_notice() {
		$plugin_name        = 'Yoast+SEO';
		$metabox_dependency = '#yoast_wpseo_title';

		$plugin = sanitize_title( $plugin_name );
		if ( $this->is_dismissed( $plugin ) ) {
			return;
		}

		if ( ! function_exists( 'is_gutenberg_page' ) || ! is_gutenberg_page() ) {
			return;
		}
		?>
				<div style="display: none;" id="mts-rm-upsell-notice">
						<?php echo $this->get_competitor_notice( $plugin_name ); ?>
						<a href="#" id="mts-rm-upsell-dismiss"><span class="dashicons dashicons-no-alt"></span></a>
				</div>
				<script type="text/javascript">
						jQuery(window).load(function() {
								var $ = jQuery;
								if ( $( '<?php echo $metabox_dependency; ?>' ).length ) {
									var $notice = $( '#mts-rm-upsell-notice' );
									$( '#mts-rm-upsell-dismiss' ).click(function(event) {
											event.preventDefault();
											$( '#mts-rm-upsell-notice' ).fadeOut( '400' );
											$.ajax({
													url: ajaxurl,
													type: 'GET',
													data: { action: 'rmu_dismiss', n: '<?php echo $plugin; ?>' },
											});
									});
									$(document).on('click', 'button.components-button[aria-label="Yoast SEO"]', function(event) {
										setTimeout(function() {
											$notice.insertAfter($('.edit-post-sidebar-header strong:contains("Yoast SEO")').parent()).show();
										}, 11);
									});
								}
						});
				</script>
				<?php echo $this->get_notice_css(); ?>
				<?php
	}

	public function get_competitor_notice( $utm_source, $cta = true ) {
		return strtr(
			$this->get_setting( 'competitor_notice' ),
			array(
				'@CTA'    => $cta ? $this->get_install_or_activate_link() : '',
				'@SOURCE' => $utm_source,
			)
		);
	}

	public function get_notice_css() {
		return '
		<style type="text/css">
			#mts-rm-upsell-notice {
				border-left: 4px solid #ffba00;
				background: #fffbee;
				padding: 12px 24px 12px 12px;
				box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
				margin: 10px 0;
				line-height: 1.8;
				position: relative;
				z-index: 1;
			}
			#mts-rm-upsell-dismiss {
				display: block;
				position: absolute;
				right: 4px;
				top: 5px;
				text-decoration: none;
				color: rgba(82, 65, 0, 0.16);
			}
			.mts-ctad-question {
				font-weight: bold;
			}
			.nhp-opts-info-field {
				width: 94%;
			}
		</style>';
	}

	public function get_install_link( $class = '', $label = '' ) {
		if ( ! $label ) {
			$label = '<strong>' . $this->get_setting( 'link_label_install' ) . '</strong>';
		}
		$action       = 'install-plugin';
		$slug         = RMU_PLUGIN_SLUG;
		$install_link = add_query_arg(
			array(
				'tab'       => 'plugin-information',
				'plugin'    => $slug,
				'TB_iframe' => 'true',
				'width'     => '600',
				'height'    => '550',
			),
			admin_url( 'plugin-install.php' )
		);

		add_thickbox();
		wp_enqueue_script( 'plugin-install' );
		wp_enqueue_script( 'updates' );

		return '<a href="' . $install_link . '" class="thickbox ' . esc_attr( $class ) . '" title="' . esc_attr__( 'Rank Math SEO', 'point' ) . '">' . $label . '</a>';
	}

	public function get_activate_link( $class = '', $label = '' ) {
		if ( ! $label ) {
			$label = '<strong>' . $this->get_setting( 'link_label_activate' ) . '</strong>';
		}
		$activate_link = wp_nonce_url( 'plugins.php?action=activate&plugin=' . rawurlencode( RMU_PLUGIN_FILE ), 'activate-plugin_' . RMU_PLUGIN_FILE );
		return '<a href="' . $activate_link . '" class="' . esc_attr( $class ) . '">' . $label . '</a>';
	}

	public function get_install_or_activate_link( $class = '', $label_install = '', $label_activate = '' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins      = array_keys( get_plugins() );
		$rm_installed = in_array( RMU_PLUGIN_FILE, $plugins, true );

		if ( ! $rm_installed ) {
			return $this->get_install_link( $class, $label_install );
		} else {
			return $this->get_activate_link( $class, $label_activate );
		}
	}

	public function inject_yoast_notice() {
		$this->inject_metabox_notice( 'Yoast+SEO', 'insertBefore("#wpseo_meta")', '#wpseo_meta' );
		$this->inject_yoast_gutenberg_notice();
	}

	public function inject_aioseo_notice() {
		$this->inject_metabox_notice( 'AIO+SEO', 'insertBefore("#aiosp")', '#aiosp' );
	}

}

define( 'RMU_ACTIVE', true );
