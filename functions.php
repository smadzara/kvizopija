<?php
/**
 * kvizopija functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package kvizopija
 */

if ( ! defined( 'KVIZOPIJA_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'KVIZOPIJA_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function kvizopija_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on kvizopija, use a find and replace
		* to change 'kvizopija' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'kvizopija', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'kvizopija' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'kvizopija_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'kvizopija_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function kvizopija_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'kvizopija_content_width', 640 );
}
add_action( 'after_setup_theme', 'kvizopija_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function kvizopija_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'kvizopija' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'kvizopija' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'kvizopija_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function kvizopija_scripts() {
	wp_enqueue_style( 'kvizopija-style', get_stylesheet_uri(), array(), KVIZOPIJA_VERSION );
	wp_style_add_data( 'kvizopija-style', 'rtl', 'replace' );

	/* wp_enqueue_script( 'kvizopija-navigation', get_template_directory_uri() . '/js/navigation.js', array(), KVIZOPIJA_VERSION, true ); */
    /* wp_enqueue_script( 'kvizopija-navbar', get_template_directory_uri() . '/js/navbar.js', array(), KVIZOPIJA_VERSION, true ); */



	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    // Bootstrap import
    //wp_enqueue_style("bootstrap", "//cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css");
}
add_action( 'wp_enqueue_scripts', 'kvizopija_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Custom post type declaration - 'questions'
 */

function questions_register() {
    $labels = array(
        'name' => _x('Questions', 'post type general name'),
        'singular_name' => _x('Question', 'post type singular name'),
        'add_new' => _x('Add New', 'Question'),
        'add_new_item' => __('Add New Question'),
        'edit_item' => __('Edit Question'),
        'new_item' => __('New Question'),
        'view_item' => __('View Question'),
        'search_items' => __('Search Questions'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => 8,
        'supports' => array('title','editor','thumbnail'),
        "menu_icon" => "dashicons-admin-customizer"
    ); 
    register_post_type( 'questions' , $args );
}
add_action('init', 'questions_register');

/**
 * Custom post type taxonomy declaration - 'questions_categories'
 */

function create_questions_taxonomies() {
    $labels = array(
        'name'              => _x( 'Categories', 'taxonomy general name' ),
        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
        'search_items'      => __( 'Search Categories' ),
        'all_items'         => __( 'All Categories' ),
        'parent_item'       => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item'         => __( 'Edit Category' ),
        'update_item'       => __( 'Update Category' ),
        'add_new_item'      => __( 'Add New Category' ),
        'new_item_name'     => __( 'New Category Name' ),
        'menu_name'         => __( 'Categories' ),
    );

    $args = array(
        'hierarchical'      => true, // Set this to 'false' for non-hierarchical taxonomy (like tags)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        //'rewrite'           => array( 'slug' => 'questions' )
    );

    register_taxonomy( 'questions_categories', array( 'questions' ), $args );
}
add_action( 'init', 'create_questions_taxonomies', 0 );

/**
 * Custom post type taxonomy declaration - 'questions_terms'
 */

function create_questions_terms() {
    $labels = array(
        'name'              => _x( 'Terms', 'term general name' ),
        'singular_name'     => _x( 'Term', 'term singular name' ),
        'search_items'      => __( 'Search Terms' ),
        'all_items'         => __( 'All Terms' ),
        'parent_item'       => __( 'Parent Term' ),
        'parent_item_colon' => __( 'Parent Term:' ),
        'edit_item'         => __( 'Edit Term' ),
        'update_item'       => __( 'Update Term' ),
        'add_new_item'      => __( 'Add New Term' ),
        'new_item_name'     => __( 'New Term Name' ),
        'menu_name'         => __( 'Terms' ),
    );

    $args = array(
        'hierarchical'      => false, // Set this to 'false' for non-hierarchical taxonomy (like tags)
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        //'rewrite'           => array( 'slug' => 'questions' )
    );

    register_taxonomy( 'questions_terms', array( 'questions' ), $args );
}
add_action( 'init', 'create_questions_terms', 0 );


// Limitiranje pretrage samo na pitanja 'questions' i 20 odgovora

function searchfilter($query) {
 
    if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('questions'));
        $query->set( 'posts_per_page', '50' );
    }
 
return $query;
}
 
add_filter('pre_get_posts','searchfilter');


// Custom Search form labels


function custom_search_form( $search_form ) { 
    $search_form = '<section class="search-form-custom"><form role="search" method="get" id="search-form" action="' . home_url( '/' ) . '" >
     <label class="screen-reader-text" for="s">' . __('',  'domain') . '</label>
     <input type="search" value="' . get_search_query() . '" name="s" id="s" placeholder="Pretraži pitanja..." />
     <input type="submit" id="searchsubmit" value="'. esc_attr__('Traži', 'domain') .'" />
     </form></section>';

 return $search_form;
}

add_filter( 'get_search_form', 'custom_search_form' );


// Eliminacija errora

/**
 * Proper ob_end_flush() for all levels
 *
 * This replaces the WordPress `wp_ob_end_flush_all()` function
 * with a replacement that doesn't cause PHP notices.
 */
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
add_action( 'shutdown', function() {
   while ( @ob_end_flush() );
} );

// Spremanje rezultata
function save_quiz_results() {
    global $wpdb;

    $player_name = $_POST['player_name'];
    $total_questions = $_POST['total_questions'];
    $correct_answers = $_POST['correct_answers'];
    $percentage = $_POST['percentage'];
    $time_taken = $_POST['time_taken'];

    $table_name = $wpdb->prefix . 'quiz_results';

    $wpdb->insert(
        $table_name,
        array(
            'player_name' => $player_name,
            'total_questions' => $total_questions,
            'correct_answers' => $correct_answers,
            'percentage' => $percentage,
            'time_taken' => $time_taken
        )
    );

    wp_send_json_success();
}
add_action('wp_ajax_save_quiz_results', 'save_quiz_results');
add_action('wp_ajax_nopriv_save_quiz_results', 'save_quiz_results');





// Registrirajte prilagođeni endpoint
add_action( 'rest_api_init', function () {
    register_rest_route( 'custom/v1', '/questions/', array(
        'methods' => 'GET',
        'callback' => 'get_custom_questions',
    ) );
} );

// Funkcija koja obrađuje prilagođeni endpoint
function get_custom_questions( $request ) {
    $args = array(
        'post_type' => 'questions',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => 10,
        'paged' => $request['page']
    );

    $query = new WP_Query( $args );
    $posts = $query->posts;

    $response = array();
    foreach ( $posts as $post ) {
        $terms = get_the_terms( $post->ID, 'questions_categories' );
        $term_list = wp_get_post_terms( $post->ID, 'questions_terms', array( 'fields' => 'all' ) );

        // Dodajte URL-ove za svaku kategoriju i pojam
        foreach ($terms as &$term) {
            $term->link = get_term_link($term);
        }
        foreach ($term_list as &$single_term) {
            $single_term->link = get_term_link($single_term);
        }

        $item_data = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'terms' => $terms,
            'question_author' => get_field('question_author', $post->ID),
            'question_author_url' => get_field('question_author_url', $post->ID),
            'term_list' => $term_list,
            'date' => get_the_date( 'j. n. Y.', $post->ID )
        );
        $response[] = $item_data;
    }

    return new WP_REST_Response( $response, 200 );
}


