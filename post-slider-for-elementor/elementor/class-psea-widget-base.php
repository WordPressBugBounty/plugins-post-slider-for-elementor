<?php

/**
 * Shared base for every widget in the pack.
 *
 * Centralizes the pieces all nine widgets repeat: the post-filter controls
 * (count / category / tag / order), the WP_Query builder those controls feed,
 * a common Style section (title / meta / excerpt typography+color), the
 * SolverWP promo section, and small render helpers. Each widget file stays
 * focused on its own layout only.
 *
 * @package Psea
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Psea_Widget_Base' ) ) {

	abstract class Psea_Widget_Base extends \Elementor\Widget_Base {

		public function get_categories() {
			return array( 'psea' );
		}

		public function get_style_depends() {
			return array( 'psea_widgets_css' );
		}

		/* ── Controls helpers ─────────────────────────────────────────── */

		/**
		 * Standard post filter section: count, category include/exclude,
		 * tag, orderby, order.
		 * */
		protected function register_query_controls( $default_count = 6, $max = 50 ) {

			$this->start_controls_section( 'psea_query_section', array(
				'label' => esc_html__( 'Post Filter', 'psea' ),
			) );

			$this->add_control( 'ppr', array(
				'label'   => esc_html__( 'Number of Posts', 'psea' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => $max,
				'step'    => 1,
				'default' => $default_count,
			) );

			$this->add_control( 'select_cat', array(
				'label'    => esc_html__( 'Select Category', 'psea' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => psea_blog_post_category(),
			) );

			$this->add_control( 'exclude_cat', array(
				'label'    => esc_html__( 'Exclude Category', 'psea' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => psea_blog_post_category(),
			) );

			$this->add_control( 'select_tag', array(
				'label'    => esc_html__( 'Select Tag', 'psea' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options'  => psea_blog_post_tag(),
			) );

			$this->add_control( 'orderby', array(
				'label'   => esc_html__( 'Order By', 'psea' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'date'   => esc_html__( 'Date', 'psea' ),
					'title'  => esc_html__( 'Title', 'psea' ),
					'author' => esc_html__( 'Author', 'psea' ),
					'rand'   => esc_html__( 'Random', 'psea' ),
				),
				'default' => 'date',
			) );

			$this->add_control( 'order', array(
				'label'   => esc_html__( 'Order', 'psea' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					'desc' => esc_html__( 'DESC', 'psea' ),
					'asc'  => esc_html__( 'ASC', 'psea' ),
				),
				'default' => 'desc',
			) );

			$this->end_controls_section();
		}

		/**
		 * Build WP_Query args from the controls above.
		 * */
		protected function build_query_args( $settings, $default_count = 6 ) {

			$args = array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => true,
				'posts_per_page'      => ! empty( $settings['ppr'] ) ? absint( $settings['ppr'] ) : $default_count,
				'orderby'             => ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date',
				'order'               => ! empty( $settings['order'] ) ? $settings['order'] : 'desc',
			);

			if ( ! empty( $settings['exclude_cat'] ) ) {
				$args['category__not_in'] = array_map( 'absint', (array) $settings['exclude_cat'] );
			}
			if ( ! empty( $settings['select_cat'] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', array_values( (array) $settings['select_cat'] ) ),
				);
			}
			if ( ! empty( $settings['select_tag'] ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'post_tag',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', array_values( (array) $settings['select_tag'] ) ),
				);
			}

			return $args;
		}

		/**
		 * Common Style tab: title, meta and excerpt typography/colors.
		 * Pass an empty string to skip a part.
		 * */
		protected function register_common_style_controls( $title_selector, $meta_selector = '', $excerpt_selector = '' ) {

			$this->start_controls_section( 'psea_style_section', array(
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Style', 'psea' ),
			) );

			if ( $title_selector ) {
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					array(
						'name'     => 'psea_title_typography',
						'label'    => esc_html__( 'Title Typography', 'psea' ),
						'selector' => '{{WRAPPER}} ' . $title_selector,
					)
				);
				$this->add_control( 'psea_title_color', array(
					'label'     => esc_html__( 'Title Color', 'psea' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $title_selector => 'color: {{VALUE}}',
					),
				) );
			}

			if ( $meta_selector ) {
				$this->add_control( 'psea_meta_color', array(
					'label'     => esc_html__( 'Meta Color', 'psea' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $meta_selector => 'color: {{VALUE}}',
					),
				) );
			}

			if ( $excerpt_selector ) {
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					array(
						'name'     => 'psea_excerpt_typography',
						'label'    => esc_html__( 'Excerpt Typography', 'psea' ),
						'selector' => '{{WRAPPER}} ' . $excerpt_selector,
					)
				);
				$this->add_control( 'psea_excerpt_color', array(
					'label'     => esc_html__( 'Excerpt Color', 'psea' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $excerpt_selector => 'color: {{VALUE}}',
					),
				) );
			}

			$this->add_control( 'psea_accent_color', array(
				'label'     => esc_html__( 'Accent Color', 'psea' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .psea-meta-cat'        => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .psea-ticker-label'    => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .psea-timeline-dot'    => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .psea-carousel-prev, {{WRAPPER}} .psea-carousel-next' => 'background-color: {{VALUE}}',
				),
			) );

			$this->end_controls_section();
		}

		/**
		 * The SolverWP promo section every widget in the pack carries.
		 * */
		protected function register_promo_section() {

			$this->start_controls_section( 'psea_service_section', array(
				'label' => esc_html__( 'Service', 'psea' ),
			) );

			$this->add_control( 'psea_service_note', array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<p style="line-height:20px;">Do you need any WordPress service like speeding up your website, or custom theme/plugin development? Contact us at</p><strong style="color:green;font-size:16px;">support@solverwp.com</strong><br/><br/><a target="_blank" href="https://solverwp.com/">Check our premium products</a>', 'psea' ),
				'content_classes' => 'warining',
			) );

			$this->end_controls_section();
		}

		/* ── Render helpers ───────────────────────────────────────────── */

		/**
		 * Category badge + date row.
		 * */
		protected function render_post_meta( $show_cat = true, $show_date = true ) {
			echo '<div class="psea-meta">';
			if ( $show_cat ) {
				$cats = get_the_category();
				if ( ! empty( $cats ) ) {
					echo '<a class="psea-meta-cat" href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
				}
			}
			if ( $show_date ) {
				echo '<span class="psea-meta-date">' . esc_html( get_the_date() ) . '</span>';
			}
			echo '</div>';
		}

		/**
		 * Linked featured image (skipped when the post has none).
		 * */
		protected function render_thumb( $size = 'medium_large' ) {
			if ( has_post_thumbnail() ) {
				echo '<a class="psea-thumb" href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( null, $size ) . '</a>';
			}
		}

		/**
		 * Friendly empty state instead of a blank box (matters most in the
		 * Elementor editor).
		 * */
		protected function render_no_posts() {
			echo '<p class="psea-no-posts">' . esc_html__( 'No posts found for the current filters. Try a different category or tag.', 'psea' ) . '</p>';
		}
	}
}
