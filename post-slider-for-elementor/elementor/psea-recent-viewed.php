<?php

/**
 * Recent Viewed Posts widget — shows the posts THIS visitor opened recently,
 * read from the cookie written by psea_track_recently_viewed() (main file).
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Recent_Viewed_Widget' ) ) {

	class Psea_Recent_Viewed_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-recent-viewed';
		}

		public function get_title() {
			return esc_html__( 'Recent Viewed Posts', 'psea' );
		}

		public function get_icon() {
			return 'eicon-history';
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_rv_section', array(
				'label' => esc_html__( 'Settings', 'psea' ),
			) );

			$this->add_control( 'ppr', array(
				'label'   => esc_html__( 'Max Posts', 'psea' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 12,
				'default' => 4,
			) );

			$this->add_control( 'empty_text', array(
				'label'   => esc_html__( 'Nothing Viewed Yet Text', 'psea' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'You have not viewed any posts yet.', 'psea' ),
			) );

			$this->end_controls_section();

			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-list-title a', '.psea-meta-date' );
		}

		protected function render() {

			$settings = $this->get_settings_for_display();
			$max      = ! empty( $settings['ppr'] ) ? absint( $settings['ppr'] ) : 4;

			$viewed = array();
			if ( isset( $_COOKIE['psea_recently_viewed'] ) ) {
				$viewed = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_COOKIE['psea_recently_viewed'] ) ) ) ) );
			}

			$is_editor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode();

			// The editor has no meaningful browsing history — preview with the
			// latest posts (clearly labelled) so the layout is still visible.
			if ( empty( $viewed ) && $is_editor ) {
				echo '<p class="psea-editor-note">' . esc_html__( 'Preview: showing latest posts. On the live site this lists the posts each visitor actually opened.', 'psea' ) . '</p>';
				$query = new \WP_Query( array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => $max,
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => true,
				) );
			} elseif ( empty( $viewed ) ) {
				echo '<p class="psea-no-posts">' . esc_html( $settings['empty_text'] ) . '</p>';
				return;
			} else {
				$query = new \WP_Query( array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'post__in'            => array_slice( $viewed, 0, $max ),
					'orderby'             => 'post__in', // keep most-recently-viewed first.
					'posts_per_page'      => $max,
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => true,
				) );
			}

			if ( ! $query->have_posts() ) {
				echo '<p class="psea-no-posts">' . esc_html( $settings['empty_text'] ) . '</p>';
				return;
			}
			?>
			<div class="psea-list">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<article class="psea-list-item">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="psea-list-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
						<?php endif; ?>
						<div class="psea-list-body">
							<h4 class="psea-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							<span class="psea-meta-date"><?php echo esc_html( get_the_date() ); ?></span>
						</div>
					</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<?php
		}
	}
}
