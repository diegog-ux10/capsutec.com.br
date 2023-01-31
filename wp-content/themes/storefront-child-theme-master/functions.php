<?php

/**
 * Dequeue the Storefront Parent theme core CSS
 */

// function sf_child_theme_dequeue_style()
// {
//     wp_dequeue_style("storefront-style");
//     wp_dequeue_style("storefront-woocommerce-style");
// }

// add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

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