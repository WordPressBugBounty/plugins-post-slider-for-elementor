<?php

/**
 * Post List widget — compact vertical list with small thumbnails,
 * ideal for sidebars and "latest news" columns.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Post_List_Widget' ) ) {

	class Psea_Post_List_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-post-list';
		}

		public function get_title() {
			return esc_html__( 'Post List', 'psea' );
		}

		public function get_icon() {
			return 'eicon-post-list';
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_layout_section', array(
				'label' => esc_html__( 'Layout', 'psea' ),
			) );

			$this->add_control( 'show_thumb', array(
				'label'        => esc_html__( 'Show Thumbnail', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			) );

			$this->add_control( 'show_date', array(
				'label'        => esc_html__( 'Show Date', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			) );

			$this->add_control( 'numbered', array(
				'label'        => esc_html__( 'Show Numbers', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			) );

			$this->end_controls_section();

			$this->register_query_controls( 5 );
			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-list-title a', '.psea-meta-date' );
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
			<div class="psea-list<?php echo 'yes' === $settings['numbered'] ? ' psea-list-numbered' : ''; ?>">
				<?php while ( $query->have_posts() ) : $query->the_post(); $i++; ?>
					<article class="psea-list-item">
						<?php if ( 'yes' === $settings['numbered'] ) : ?>
							<span class="psea-list-num"><?php echo esc_html( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php endif; ?>
						<?php if ( 'yes' === $settings['show_thumb'] && has_post_thumbnail() ) : ?>
							<a class="psea-list-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
						<?php endif; ?>
						<div class="psea-list-body">
							<h4 class="psea-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							<?php if ( 'yes' === $settings['show_date'] ) : ?>
								<span class="psea-meta-date"><?php echo esc_html( get_the_date() ); ?></span>
							<?php endif; ?>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<?php
		}
	}
}
