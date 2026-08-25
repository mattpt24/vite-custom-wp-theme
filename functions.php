<?php 


// Tells WordPress to add support for features like the document title tag
function my_custom_theme_setup() {
    add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'my_custom_theme_setup' );



// Figures out if you are coding locally and enqueues your stylesheets and JavaScript files
function my_custom_theme_assets() {
    $is_dev = (defined('WP_DEBUG') && WP_DEBUG) 
        && !str_contains($_SERVER['HTTP_HOST'] ?? '', '.local.lt') 
        && !str_contains($_SERVER['HTTP_HOST'] ?? '', '.localsite.io');


    // Always load the root CSS
    wp_enqueue_style( 'my-theme-style', get_stylesheet_uri(), [], '1.0.0' );


    if ($is_dev) {
        // This injects Vite's hot-reload client so your browser watches for changes
        wp_enqueue_script('vite-client', 'http://localhost:5173/@vite/client', [], null, true);
        wp_enqueue_script('my-theme-script', 'http://localhost:5173/src/js/script.js', [], null, true);
    } else {
        // Production: load script from root
        wp_enqueue_script('my-theme-script', get_theme_file_uri('script.js'), [], '1.0.0', true);
    }
}


add_action('wp_enqueue_scripts', 'my_custom_theme_assets');




// Adds the required type="module" attribute specifically for Vite development scripts
function my_theme_script_type_attribute($tag, $handle, $src) {
    if (in_array($handle, ['vite-client', 'my-theme-script']) && str_contains($src, 'localhost:5173')) {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}
add_filter('script_loader_tag', 'my_theme_script_type_attribute', 10, 3);