<?php

/**
 * Plugin Name:       Woo Related Products Widget
 * Plugin URI:        https://facebook.com
 * Description:       Show Related products widget on products page only
 * Version:           1.1.0
 * Author:            Rajdeep Tayde
 * Author URI:        https://www.facebook.com/rajdeeptayde
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-related-product-widget
 * Domain Path:       /languages
 */


/**
 * Related Products Widget.
 * Gets and displays Related products in an unordered list.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget Ralated products class.
*/

function WC_Widget_Related_Products_Function() {
    register_widget( 'WC_Widget_Related_Products' );
}
add_action( 'widgets_init', 'WC_Widget_Related_Products_Function' );


function WC_Widget_Related_Products_Function_Front() {
    return new WC_Widget_Related_Products();
}
add_action( 'init', 'WC_Widget_Related_Products_Function_Front' );


if(!class_exists('WC_Widget_Related_Products'))
{



class WC_Widget_Related_Products extends WP_Widget{

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->widget_cssclass    = 'woocommerce woocommerce_related_products_widget';
		$this->widget_description = __( "A list of your store's top-rated products.", 'woocommerce' );
		$this->widget_id          = 'woocommerce_top_rated_products';
		$this->widget_name        = __( 'Woo Related Products', 'woocommerce' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Woo Related Products', 'woocommerce' ),
				'label' => __( 'Title', 'woocommerce' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of products to show', 'woocommerce' ),
			),
		);

		parent::__construct($this->widget_cssclass,$this->widget_name,$this->settings);
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 * @param array $args     Arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {

		

		ob_start();

		$number = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['std'];


		if(!is_product()){
			return;
		}


		
		

		$t = wc_get_related_products( get_the_ID());

		if(empty($t)){
			return;
		}

		$query_args = array(
			'posts_per_page' => $number,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_query'     => WC()->query->get_meta_query(),
			'tax_query'      => WC()->query->get_tax_query(),
			'post__in'		 => $t
		); 

		$r = new WP_Query( $query_args );

		

		if ( $r->have_posts() ) {

			

			echo wp_kses_post( apply_filters( 'woocommerce_before_widget_product_list', '<ul class="product_list_widget">' ) );

			$template_args = array(
				'widget_id'   => $args['widget_id'],
				'show_rating' => true,
			);

			while ( $r->have_posts() ) {
				$r->the_post();
				wc_get_template( 'content-widget-product.php', $template_args );
			}

			echo wp_kses_post( apply_filters( 'woocommerce_after_widget_product_list', '</ul>' ) );

			
		}

		wp_reset_postdata();

		$content = ob_get_clean();

		echo $content; // WPCS: XSS ok.

		
	}
}

}
