<?php

/**
 * Widget Manager settings.
 *
 * One toggle per widget in the pack (see psea_widget_registry() in the main
 * plugin file). Disabled widgets are simply never registered with Elementor,
 * so their code isn't even loaded — keeps the editor lean on sites that only
 * need one or two of them.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Settings' ) ) {

	class Psea_Settings {

		const OPTION_KEY = 'psea_enabled_widgets';

		private static $instance = null;

		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		private function __construct() {
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'admin_init', array( $this, 'register' ) );
		}

		/**
		 * Is a widget enabled? Until the option is first saved, every widget
		 * defaults to ON (so updating from an older version changes nothing).
		 * */
		public static function is_enabled( $slug ) {
			$saved = get_option( self::OPTION_KEY, false );
			if ( false === $saved || ! is_array( $saved ) ) {
				return true; // never saved: all widgets on.
			}
			return in_array( $slug, $saved, true );
		}

		public function menu() {
			add_menu_page(
				esc_html__( 'Smart Post Showcase', 'psea' ),
				esc_html__( 'Smart Post Showcase', 'psea' ),
				'manage_options',
				'psea-settings',
				array( $this, 'render' ),
				'dashicons-slides',
				58
			);
		}

		public function register() {
			register_setting( 'psea_settings_group', self::OPTION_KEY, array(
				'sanitize_callback' => array( $this, 'sanitize' ),
			) );
		}

		/**
		 * Keep only known widget slugs.
		 * */
		public function sanitize( $input ) {
			$valid = array_keys( psea_widget_registry() );
			$input = is_array( $input ) ? $input : array();
			return array_values( array_intersect( array_map( 'sanitize_key', $input ), $valid ) );
		}

		public function render() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$registry = psea_widget_registry();
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Smart Post Showcase — Widget Manager', 'psea' ); ?></h1>
				<p class="description"><?php esc_html_e( 'Turn individual Elementor widgets on or off. Disabled widgets are not loaded at all, keeping the editor fast.', 'psea' ); ?></p>

				<form method="post" action="options.php">
					<?php settings_fields( 'psea_settings_group' ); ?>
					<table class="widefat striped" style="max-width:720px;margin-top:16px;">
						<thead>
							<tr>
								<th style="width:60px;"><?php esc_html_e( 'Enabled', 'psea' ); ?></th>
								<th><?php esc_html_e( 'Widget', 'psea' ); ?></th>
								<th><?php esc_html_e( 'Description', 'psea' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $registry as $slug => $w ) : ?>
								<tr>
									<td>
										<input type="checkbox"
											id="psea-w-<?php echo esc_attr( $slug ); ?>"
											name="<?php echo esc_attr( self::OPTION_KEY ); ?>[]"
											value="<?php echo esc_attr( $slug ); ?>"
											<?php checked( self::is_enabled( $slug ) ); ?> />
									</td>
									<td><label for="psea-w-<?php echo esc_attr( $slug ); ?>"><strong><?php echo esc_html( $w['title'] ); ?></strong></label></td>
									<td><?php echo esc_html( $w['desc'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
			<?php
		}
	}

	Psea_Settings::instance();
}
