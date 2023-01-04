<?php

/**
 * Todos os scripts e estilos do Tema
 */

function capsutec_theme_scripts()
{
    // Google Font
    wp_register_style(
        "google-font",
        "https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap"
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

    // wp_enqueue_script(
    //     "js-forms",
    //     get_stylesheet_directory_uri() . "/assets/js/forms.min.js",
    //     ["jquery"],
    //     wp_get_theme()->get("Version"),
    //     true
    // );

    // Custom js
    wp_enqueue_script(
        "custom",
        get_stylesheet_directory_uri() . "/assets/js/custom.min.js",
        ["jquery"],
        "1.0.0"
    );

    // Header js
    wp_enqueue_script(
        "header",
        get_stylesheet_directory_uri() . "/assets/js/header.min.js",
        ["jquery"],
        "1.0.0"
    );
}

add_action("wp_enqueue_scripts", "capsutec_theme_scripts");