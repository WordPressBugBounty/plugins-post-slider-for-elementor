<?php

/**
 * Timeline widget — vertical timeline of posts with date badges, cards
 * alternating left/right on desktop and stacking on mobile. Pure CSS.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Timeline_Widget' ) ) {

	class Psea_Timeline_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-timeline';
		}

		public function get_title() {
			return esc_html__( 'Timeline', 'psea' );
		}

		public function get_icon() {
			return 'eicon-time-line';
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_timeline_section', array(
				'label' => esc_html__( 'Timeline', 'psea' ),
			) );

			$this->add_control( 'show_excerpt', array(
				'label'        => esc_html__( 'Show Excerpt', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			) );

			$this->add_control( 'show_thumb', array(
				'label'        => esc_html__( 'Show Thumbnail', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			) );

			$this->end_controls_section();

			$this->register_query_controls( 5, 20 );
			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-timeline-title a', '.psea-timeline-date', '.psea-card-excerpt' );
		}

		protected function render() {

			$settings = $this->get_settings_for_display();

			$query = new \WP_Query( $this->build_query_args( $settings, 5 ) );

			if ( ! $query->have_posts() ) {
				$this->render_no_posts();
				return;
			}

			$i = 0;
			?>
			<div class="psea-timeline">
				<?php while ( $query->have_posts() ) : $query->the_post(); $i++; ?>
					<div class="psea-timeline-item <?php echo ( $i % 2 ) ? 'psea-tl-left' : 'psea-tl-right'; ?>">
						<span class="psea-timeline-dot"></span>
						<div class="psea-timeline-card">
							<span class="psea-timeline-date"><?php echo esc_html( get_the_date() ); ?></span>
							<?php if ( 'yes' === $settings['show_thumb'] && has_post_thumbnail() ) : ?>
								<a class="psea-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
							<?php endif; ?>
							<h3 class="psea-timeline-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
								<p class="psea-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<?php
		}
	}
}
