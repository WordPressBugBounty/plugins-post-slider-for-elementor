<?php

/**
 * Ticker List widget — breaking-news style headline ticker. The scroll is a
 * pure-CSS marquee (the headline row is printed twice and translated -50% in
 * a loop), so it needs no JS and never stutters.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Ticker_Widget' ) ) {

	class Psea_Ticker_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-ticker';
		}

		public function get_title() {
			return esc_html__( 'Ticker List', 'psea' );
		}

		public function get_icon() {
			return 'eicon-animation-text';
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_ticker_section', array(
				'label' => esc_html__( 'Ticker', 'psea' ),
			) );

			$this->add_control( 'label', array(
				'label'   => esc_html__( 'Label', 'psea' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Breaking', 'psea' ),
			) );

			$this->add_control( 'speed', array(
				'label'       => esc_html__( 'Scroll Duration (seconds)', 'psea' ),
				'description' => esc_html__( 'Time for one full loop — higher is slower.', 'psea' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 5,
				'max'         => 120,
				'default'     => 30,
			) );

			$this->end_controls_section();

			$this->register_query_controls( 6, 15 );
			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-ticker-item a' );
		}

		protected function render() {

			$settings = $this->get_settings_for_display();
			$speed    = ! empty( $settings['speed'] ) ? absint( $settings['speed'] ) : 30;

			$query = new \WP_Query( $this->build_query_args( $settings, 6 ) );

			if ( ! $query->have_posts() ) {
				$this->render_no_posts();
				return;
			}

			$items = array();
			while ( $query->have_posts() ) {
				$query->the_post();
				$items[] = '<span class="psea-ticker-item"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></span>';
			}
			wp_reset_postdata();

			$row = implode( '<span class="psea-ticker-sep">&bull;</span>', $items );
			?>
			<div class="psea-ticker">
				<?php if ( ! empty( $settings['label'] ) ) : ?>
					<span class="psea-ticker-label"><?php echo esc_html( $settings['label'] ); ?></span>
				<?php endif; ?>
				<div class="psea-ticker-viewport">
					<div class="psea-ticker-scroll" style="animation-duration: <?php echo esc_attr( $speed ); ?>s;">
						<?php
						// Printed twice on purpose: the -50% translate loop
						// hands off seamlessly from copy one to copy two.
						echo wp_kses_post( $row . '<span class="psea-ticker-sep">&bull;</span>' . $row . '<span class="psea-ticker-sep">&bull;</span>' );
						?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
