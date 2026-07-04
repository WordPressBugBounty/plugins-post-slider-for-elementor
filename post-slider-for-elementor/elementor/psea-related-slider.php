<?php

/**
 * Related Post Slider widget — a sliding row of posts sharing the current
 * post's categories. Falls back to the latest posts anywhere a "current
 * post" doesn't exist (the Elementor editor, archives, pages), so the
 * widget is never blank while designing.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Related_Slider_Widget' ) ) {

	class Psea_Related_Slider_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-related-slider';
		}

		public function get_title() {
			return esc_html__( 'Related Post Slider', 'psea' );
		}

		public function get_icon() {
			return 'eicon-post-slider';
		}

		public function get_script_depends() {
			return array( 'psea_widgets_js' );
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_related_section', array(
				'label' => esc_html__( 'Settings', 'psea' ),
			) );

			$this->add_control( 'ppr', array(
				'label'   => esc_html__( 'Number of Posts', 'psea' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 2,
				'max'     => 12,
				'default' => 6,
			) );

			$this->add_control( 'related_note', array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Posts are matched by the current post\'s categories — place this widget in a single-post template. Elsewhere it falls back to the latest posts.', 'psea' ),
				'content_classes' => 'elementor-descriptor',
			) );

			$this->end_controls_section();

			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-card-title a', '.psea-meta' );
		}

		protected function render() {

			$settings = $this->get_settings_for_display();
			$count    = ! empty( $settings['ppr'] ) ? absint( $settings['ppr'] ) : 6;

			$args = array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $count,
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => true,
			);

			$is_editor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();

			if ( is_singular( 'post' ) && ! $is_editor ) {
				$current = get_queried_object_id();
				$cats    = wp_get_post_categories( $current );
				if ( ! empty( $cats ) ) {
					$args['category__in'] = $cats;
				}
				$args['post__not_in'] = array( $current );
			} elseif ( $is_editor ) {
				echo '<p class="psea-editor-note">' . esc_html__( 'Preview: showing latest posts. On a single post this shows posts from the same categories.', 'psea' ) . '</p>';
			}

			$query = new \WP_Query( $args );

			if ( ! $query->have_posts() ) {
				$this->render_no_posts();
				return;
			}
			?>
			<div class="psea-carousel">
				<button type="button" class="psea-carousel-prev" aria-label="<?php esc_attr_e( 'Previous', 'psea' ); ?>">&lsaquo;</button>
				<div class="psea-carousel-track">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<article class="psea-carousel-item psea-card">
							<?php $this->render_thumb( 'medium_large' ); ?>
							<div class="psea-card-body">
								<?php $this->render_post_meta(); ?>
								<h3 class="psea-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
				<button type="button" class="psea-carousel-next" aria-label="<?php esc_attr_e( 'Next', 'psea' ); ?>">&rsaquo;</button>
			</div>
			<?php
		}
	}
}
