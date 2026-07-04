<?php

/**
 * Post Masonry widget — Pinterest-style layout using CSS multi-columns
 * (uncropped featured images give the varying heights, no JS library needed).
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Post_Masonry_Widget' ) ) {

	class Psea_Post_Masonry_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-post-masonry';
		}

		public function get_title() {
			return esc_html__( 'Post Masonry', 'psea' );
		}

		public function get_icon() {
			return 'eicon-posts-masonry';
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_layout_section', array(
				'label' => esc_html__( 'Layout', 'psea' ),
			) );

			$this->add_control( 'columns', array(
				'label'   => esc_html__( 'Columns', 'psea' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default' => '3',
			) );

			$this->add_control( 'show_excerpt', array(
				'label'        => esc_html__( 'Show Excerpt', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			) );

			$this->end_controls_section();

			$this->register_query_controls( 9 );
			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-card-title a', '.psea-meta', '.psea-card-excerpt' );
		}

		protected function render() {

			$settings = $this->get_settings_for_display();
			$columns  = in_array( $settings['columns'], array( '2', '3', '4' ), true ) ? $settings['columns'] : '3';

			$query = new \WP_Query( $this->build_query_args( $settings, 9 ) );

			if ( ! $query->have_posts() ) {
				$this->render_no_posts();
				return;
			}
			?>
			<div class="psea-masonry psea-mcols-<?php echo esc_attr( $columns ); ?>">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<article class="psea-card psea-masonry-item">
						<?php
						// 'large' (soft-crop) keeps each image's own aspect
						// ratio, which is what creates the masonry effect.
						$this->render_thumb( 'large' );
						?>
						<div class="psea-card-body">
							<?php $this->render_post_meta(); ?>
							<h3 class="psea-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
								<p class="psea-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 16 ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<?php
		}
	}
}
