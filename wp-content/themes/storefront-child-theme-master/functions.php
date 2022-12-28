<?php
/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */

function sf_child_theme_dequeue_style()
{
    wp_dequeue_style("storefront-style");
    wp_dequeue_style("storefront-woocommerce-style");
}

/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */

// SETUP INITIAL
//--------------
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

function capsutec_theme_scripts()
{
    // Google Font
    wp_register_style(
        "google-font",
        "https://fonts.googleapis.com/css2?family=Raleway:wght@100;200;300;400;500;600;700;800;900&display=swap"
    );

    //Bootstrap css
    wp_register_style(
        "bootstrap",
        "https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css",
        [],
        "5.2.2",
        "all"
    );

    // Swiper css
    wp_register_style(
        "swiper",
        "https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css",
        [],
        "8.4.4",
        "all"
    );

    // Tema principal css
    wp_enqueue_style(
        "main",
        get_stylesheet_directory_uri() . "/assets/css/style.min.css",
        ["bootstrap", "google-font", "swiper"],
        "1.0.0",
        "all"
    );

    // Swiper JS
    wp_enqueue_script(
        "swiper",
        "https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js",
        ["jquery"],
        "8.4.4"
    );

    // Bootstrap js
    wp_enqueue_script(
        "bootstrap",
        "https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js",
        ["jquery"],
        "5.0.0"
    );

    wp_enqueue_script(
        "js-mask",
        get_stylesheet_directory_uri() .
            "/assets/vendor/jquery/jquery.mask.min.js",
        ["jquery"],
        "1.0",
        true
    );

    wp_enqueue_script(
        "js-forms",
        get_stylesheet_directory_uri() . "/assets/js/forms.min.js",
        ["jquery"],
        wp_get_theme()->get("Version"),
        true
    );

    // Custom js
    wp_enqueue_script(
        "custom",
        get_stylesheet_directory_uri() . "/assets/js/custom.min.js",
        ["jquery"],
        "1.0.0"
    );
}

add_action("wp_enqueue_scripts", "capsutec_theme_scripts");
