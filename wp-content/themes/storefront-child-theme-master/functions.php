<?php

/**
 * Dequeue the Storefront Parent theme core CSS
 */

function sf_child_theme_dequeue_style()
{
    wp_dequeue_style("storefront-style");
    wp_dequeue_style("storefront-woocommerce-style");
}

add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Configurações iniciais 
 */

if (!function_exists("theme_setup")):
    function theme_setup()
    {
        // LOGO
        add_theme_support("custom-logo", [
            "flex-width" => false,
            "flex-height" => false,
        ]);

        // BACKGROUND IMAGE
        $defaults = [
            "default-color" => "ffffff",
            "default-repeat" => "no-repeat",
            "default-position-x" => "center",
            "default-attachment" => "fixed",
        ];

        add_theme_support("custom-background", $defaults);

        // ADD SUPPORT FOR RESPONSIVE EMBEDDED CONTENT
        add_theme_support("responsive-embeds");

        // ADD THUMBNAIL TO POST THEME
        add_theme_support("post-thumbnails");
        add_image_size("post-thumb", 320, 190, true);
        add_image_size("blog-thumb", 370, 250, true);
    }
endif;

add_action("after_setup_theme", "theme_setup");

/**
 * Scripts e Estilos
 */

require get_stylesheet_directory() . '/inc/capsutec-scripts.php';

/**
 * Options theme fields
 */

 require get_stylesheet_directory() . '/inc/capsutec-option-theme-fields.php';


 function wsb_add_to_cart_button( ) {
    global $product;

    $classes = implode( ' ',  array(
        'button',
        'product_type_' . $product->get_type(),
        $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
        $product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
    )  );

    return apply_filters( 'woocommerce_loop_add_to_cart_link',
        sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="button %s product_type_%s">%s</a>',
            esc_url( $product->add_to_cart_url() ),
            esc_attr( $product->get_id() ),
            esc_attr( $product->get_sku() ),
            esc_attr( isset( $quantity ) ? $quantity : 1 ),
            esc_attr( isset( $classes ) ? $classes : 'button' ),
            esc_attr( $product->get_type() ),
            esc_html( $product->add_to_cart_text() )
        ),
    $product );
} 