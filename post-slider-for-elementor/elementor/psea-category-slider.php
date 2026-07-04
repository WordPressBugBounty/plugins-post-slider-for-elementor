<?php

/**
 * Post Category Slider widget — horizontally sliding cards of post
 * categories (name + post count over the category's latest featured image),
 * each linking to the category archive.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Category_Slider_Widget' ) ) {

	class Psea_Category_Slider_Widget extends Psea_Widget_Base {

		public function get_name() {
			return 'psea-category-slider';
		}

		public function get_title() {
			return esc_html__( 'Post Category Slider', 'psea' );
		}

		public function get_icon() {
			return 'eicon-carousel';
		}

		public function get_script_depends() {
			return array( 'psea_widgets_js' );
		}

		protected function register_controls() {

			$this->start_controls_section( 'psea_cats_section', array(
				'label' => esc_html__( 'Categories', 'psea' ),
			) );

			$this->add_control( 'cat_count', array(
				'label'   => esc_html__( 'Number of Categories', 'psea' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 24,
				'default' => 8,
			) );

			$this->add_control( 'cat_orderby', array(
				'label'   => esc_html__( 'Order By', 'psea' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'count' => esc_html__( 'Post Count', 'psea' ),
					'name'  => esc_html__( 'Name', 'psea' ),
				),
				'default' => 'count',
			) );

			$this->add_control( 'show_count', array(
				'label'        => esc_html__( 'Show Post Count', 'psea' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			) );

			$this->end_controls_section();

			$this->register_promo_section();
			$this->register_common_style_controls( '.psea-cat-card-name' );
		}

		protected function render() {

			$settings = $this->get_settings_for_display();
			$count    = ! empty( $settings['cat_count'] ) ? absint( $settings['cat_count'] ) : 8;

			$terms = get_terms( array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
				'number'     => $count,
				'orderby'    => ( 'name' === $settings['cat_orderby'] ) ? 'name' : 'count',
				'order'      => ( 'name' === $settings['cat_orderby'] ) ? 'ASC' : 'DESC',
			) );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				$this->render_no_posts();
				return;
			}
			?>
			<div class="psea-carousel">
				<button type="button" class="psea-carousel-prev" aria-label="<?php esc_attr_e( 'Previous', 'psea' ); ?>">&lsaquo;</button>
				<div class="psea-carousel-track">
					<?php foreach ( $terms as $term ) :
						// Latest post's featured image becomes the card background.
						$latest = get_posts( array(
							'cat'            => $term->term_id,
							'posts_per_page' => 1,
							'fields'         => 'ids',
							'no_found_rows'  => true,
						) );
						$img = ! empty( $latest ) ? get_the_post_thumbnail_url( $latest[0], 'medium_large' ) : '';
					?>
						<a class="psea-carousel-item psea-cat-card" href="<?php echo esc_url( get_category_link( $term->term_id ) ); ?>"
							<?php if ( $img ) : ?>style="background-image:url('<?php echo esc_url( $img ); ?>');"<?php endif; ?>>
							<span class="psea-cat-card-inner">
								<span class="psea-cat-card-name"><?php echo esc_html( $term->name ); ?></span>
								<?php if ( 'yes' === $settings['show_count'] ) : ?>
									<span class="psea-cat-card-count">
										<?php
										/* translators: %s: number of posts in the category */
										echo esc_html( sprintf( _n( '%s Post', '%s Posts', $term->count, 'psea' ), number_format_i18n( $term->count ) ) );
										?>
									</span>
								<?php endif; ?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
				<button type="button" class="psea-carousel-next" aria-label="<?php esc_attr_e( 'Next', 'psea' ); ?>">&rsaquo;</button>
			</div>
			<?php
		}
	}
}
