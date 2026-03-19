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

if ( ! defined( 'KVIZOPIJA_QOTD_OPTION' ) ) {
	define( 'KVIZOPIJA_QOTD_OPTION', 'kvizopija_question_of_the_day_state' );
}

if ( ! function_exists( 'kvizopija_is_question_available_for_daily_pick' ) ) {
	/**
	 * Check whether a Question CPT item is valid for the Question of the Day slot.
	 *
	 * @param int $question_id Question post ID.
	 * @return bool
	 */
	function kvizopija_is_question_available_for_daily_pick( $question_id ) {
		$question_id = absint( $question_id );
		if ( $question_id <= 0 ) {
			return false;
		}

		$question_post = get_post( $question_id );
		if ( ! ( $question_post instanceof WP_Post ) ) {
			return false;
		}

		if ( 'questions' !== $question_post->post_type || 'publish' !== $question_post->post_status ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'kvizopija_get_daily_question_category_ids' ) ) {
	/**
	 * Fetch all question category IDs that currently have at least one question.
	 *
	 * @return int[]
	 */
	function kvizopija_get_daily_question_category_ids() {
		$category_ids = get_terms(
			array(
				'taxonomy'   => 'questions_categories',
				'hide_empty' => true,
				'fields'     => 'ids',
			)
		);

		if ( is_wp_error( $category_ids ) || ! is_array( $category_ids ) ) {
			return array();
		}

		return array_values(
			array_unique(
				array_filter(
					array_map( 'absint', $category_ids )
				)
			)
		);
	}
}

if ( ! function_exists( 'kvizopija_pick_random_daily_question_for_category' ) ) {
	/**
	 * Pick one random question from a category, excluding previously used IDs.
	 *
	 * @param int   $category_id       Question category term ID.
	 * @param int[] $used_question_ids Question IDs already used in previous days.
	 * @return int Question post ID, or 0 when no candidate is available.
	 */
	function kvizopija_pick_random_daily_question_for_category( $category_id, $used_question_ids ) {
		$category_id       = absint( $category_id );
		$used_question_ids = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', is_array( $used_question_ids ) ? $used_question_ids : array() )
				)
			)
		);

		if ( $category_id <= 0 ) {
			return 0;
		}

		$query_args = array(
			'post_type'              => 'questions',
			'post_status'            => 'publish',
			'posts_per_page'         => 40,
			'orderby'                => 'rand',
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy' => 'questions_categories',
					'field'    => 'term_id',
					'terms'    => array( $category_id ),
				),
			),
		);

		if ( ! empty( $used_question_ids ) ) {
			$query_args['post__not_in'] = $used_question_ids;
		}

		$question_ids = get_posts( $query_args );
		if ( ! is_array( $question_ids ) || empty( $question_ids ) ) {
			return 0;
		}

		foreach ( $question_ids as $question_id ) {
			$question_id = absint( $question_id );
			if ( kvizopija_is_question_available_for_daily_pick( $question_id ) ) {
				return $question_id;
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'kvizopija_normalize_daily_question_state' ) ) {
	/**
	 * Normalize stored Question of the Day option payload.
	 *
	 * @param mixed $state Raw state from the database.
	 * @return array<string,mixed>
	 */
	function kvizopija_normalize_daily_question_state( $state ) {
		if ( ! is_array( $state ) ) {
			$state = array();
		}

		$used_question_ids = isset( $state['used_question_ids'] ) && is_array( $state['used_question_ids'] ) ? $state['used_question_ids'] : array();
		$used_category_ids = isset( $state['used_category_ids'] ) && is_array( $state['used_category_ids'] ) ? $state['used_category_ids'] : array();

		return array(
			'question_id'       => isset( $state['question_id'] ) ? absint( $state['question_id'] ) : 0,
			'category_id'       => isset( $state['category_id'] ) ? absint( $state['category_id'] ) : 0,
			'picked_at'         => isset( $state['picked_at'] ) ? absint( $state['picked_at'] ) : 0,
			'expires_at'        => isset( $state['expires_at'] ) ? absint( $state['expires_at'] ) : 0,
			'used_question_ids' => array_values(
				array_unique(
					array_filter(
						array_map( 'absint', $used_question_ids )
					)
				)
			),
			'used_category_ids' => array_values(
				array_unique(
					array_filter(
						array_map( 'absint', $used_category_ids )
					)
				)
			),
		);
	}
}

if ( ! function_exists( 'kvizopija_build_next_daily_question_state' ) ) {
	/**
	 * Generate the next Question of the Day state.
	 *
	 * @param array<string,mixed> $state Current normalized state.
	 * @param int                 $now   Current timestamp in site timezone.
	 * @return array<string,mixed>
	 */
	function kvizopija_build_next_daily_question_state( $state, $now ) {
		$all_category_ids = kvizopija_get_daily_question_category_ids();
		if ( empty( $all_category_ids ) ) {
			$state['question_id'] = 0;
			$state['category_id'] = 0;
			$state['picked_at']   = 0;
			$state['expires_at']  = 0;

			return $state;
		}

		$used_question_ids = isset( $state['used_question_ids'] ) ? $state['used_question_ids'] : array();
		$used_category_ids = isset( $state['used_category_ids'] ) ? $state['used_category_ids'] : array();

		$attempt_counter    = 0;
		$selected_question  = 0;
		$selected_category  = 0;

		while ( $attempt_counter < 3 && 0 === $selected_question ) {
			$candidate_categories = array_values( array_diff( $all_category_ids, $used_category_ids ) );

			if ( empty( $candidate_categories ) ) {
				$used_category_ids    = array();
				$candidate_categories = $all_category_ids;
			}

			shuffle( $candidate_categories );

			foreach ( $candidate_categories as $candidate_category_id ) {
				$question_id = kvizopija_pick_random_daily_question_for_category( $candidate_category_id, $used_question_ids );
				if ( $question_id > 0 ) {
					$selected_question = $question_id;
					$selected_category = absint( $candidate_category_id );
					break;
				}
			}

			if ( 0 !== $selected_question ) {
				break;
			}

			// All currently available questions were already used; start a fresh cycle.
			$used_question_ids = array();
			$used_category_ids = array();
			++$attempt_counter;
		}

		if ( 0 === $selected_question ) {
			$state['question_id'] = 0;
			$state['category_id'] = 0;
			$state['picked_at']   = 0;
			$state['expires_at']  = 0;

			return $state;
		}

		$used_question_ids[] = $selected_question;
		$used_category_ids[] = $selected_category;

		$state['question_id']       = $selected_question;
		$state['category_id']       = $selected_category;
		$state['picked_at']         = $now;
		$state['expires_at']        = $now + DAY_IN_SECONDS;
		$state['used_question_ids'] = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', $used_question_ids )
				)
			)
		);
		$state['used_category_ids'] = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', $used_category_ids )
				)
			)
		);

		return $state;
	}
}

if ( ! function_exists( 'kvizopija_prepare_daily_question_payload' ) ) {
	/**
	 * Build a template-friendly data payload from state.
	 *
	 * @param array<string,mixed> $state Normalized state.
	 * @return array<string,mixed>
	 */
	function kvizopija_prepare_daily_question_payload( $state ) {
		$question_id = isset( $state['question_id'] ) ? absint( $state['question_id'] ) : 0;
		if ( $question_id <= 0 || ! kvizopija_is_question_available_for_daily_pick( $question_id ) ) {
			return array();
		}

		$category_id   = isset( $state['category_id'] ) ? absint( $state['category_id'] ) : 0;
		$category_term = $category_id > 0 ? get_term( $category_id, 'questions_categories' ) : null;

		if ( ! ( $category_term instanceof WP_Term ) ) {
			$question_terms = get_the_terms( $question_id, 'questions_categories' );
			if ( is_array( $question_terms ) && ! empty( $question_terms[0] ) && $question_terms[0] instanceof WP_Term ) {
				$category_term = $question_terms[0];
				$category_id   = (int) $category_term->term_id;
			}
		}

		$category_link = '';
		$category_name = '';
		if ( $category_term instanceof WP_Term ) {
			$term_link = get_term_link( $category_term );
			if ( ! is_wp_error( $term_link ) ) {
				$category_link = $term_link;
			}
			$category_name = $category_term->name;
		}

		return array(
			'question_id'     => $question_id,
			'question_title'  => get_the_title( $question_id ),
			'question_link'   => get_permalink( $question_id ),
			'category_id'     => $category_id,
			'category_name'   => $category_name,
			'category_link'   => $category_link,
			'picked_at'       => isset( $state['picked_at'] ) ? absint( $state['picked_at'] ) : 0,
			'expires_at'      => isset( $state['expires_at'] ) ? absint( $state['expires_at'] ) : 0,
		);
	}
}

if ( ! function_exists( 'kvizopija_get_question_of_the_day' ) ) {
	/**
	 * Return the current Question of the Day. Rotates every 24 hours.
	 *
	 * @return array<string,mixed>
	 */
	function kvizopija_get_question_of_the_day() {
		$now   = (int) current_time( 'timestamp' );
		$state = kvizopija_normalize_daily_question_state( get_option( KVIZOPIJA_QOTD_OPTION, array() ) );

		$is_current_question_valid = (
			$state['question_id'] > 0 &&
			$state['expires_at'] > $now &&
			kvizopija_is_question_available_for_daily_pick( $state['question_id'] )
		);

		if ( ! $is_current_question_valid ) {
			$state = kvizopija_build_next_daily_question_state( $state, $now );
			update_option( KVIZOPIJA_QOTD_OPTION, $state, false );
		}

		return kvizopija_prepare_daily_question_payload( $state );
	}
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

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'kvizopija' ),
			'footer-menu' => esc_html__( 'Footer Menu', 'kvizopija' ),
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
	$style_handle   = 'kvizopija-style';
	$min_style_path = get_template_directory() . '/style.min.css';
	$style_src      = get_stylesheet_uri();
	$style_version  = KVIZOPIJA_VERSION;

	// Prefer the minified stylesheet when present to reduce payload size.
	if ( file_exists( $min_style_path ) ) {
		$style_src     = get_template_directory_uri() . '/style.min.css';
		$style_version = (string) filemtime( $min_style_path );
	}

	wp_enqueue_style( $style_handle, $style_src, array(), $style_version );
	wp_style_add_data( $style_handle, 'rtl', 'replace' );

	wp_enqueue_script( 'kvizopija-navigation', get_template_directory_uri() . '/js/navigation.js', array(), KVIZOPIJA_VERSION, true );
    /* wp_enqueue_script( 'kvizopija-navbar', get_template_directory_uri() . '/js/navbar.js', array(), KVIZOPIJA_VERSION, true ); */

	$is_questions_list_view = is_post_type_archive( 'questions' ) || is_tax( array( 'questions_categories', 'questions_terms' ) ) || is_search();
	$list_actions_path      = get_template_directory() . '/js/questions-list-actions.js';

	if ( $is_questions_list_view && file_exists( $list_actions_path ) ) {
		wp_enqueue_script(
			'kvizopija-questions-list-actions',
			get_template_directory_uri() . '/js/questions-list-actions.js',
			array(),
			(string) filemtime( $list_actions_path ),
			true
		);
	}


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
    // Bootstrap import
    //wp_enqueue_style("bootstrap", "//cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css");
}
add_action( 'wp_enqueue_scripts', 'kvizopija_scripts' );

/**
 * Keep theme stylesheet loaded on MemberPress ReadyLaunch pages.
 *
 * ReadyLaunch removes most non-MemberPress styles; this preserves the
 * main theme styles so pricing pages can reuse site header/menu/sidebar design.
 */
function kvizopija_keep_theme_style_on_memberpress_readylaunch( $allowed_handles ) {
	if ( ! is_array( $allowed_handles ) ) {
		$allowed_handles = array();
	}

	$allowed_handles[] = 'kvizopija-style';

	return array_values( array_unique( $allowed_handles ) );
}
add_filter( 'mepr_design_style_handles', 'kvizopija_keep_theme_style_on_memberpress_readylaunch' );

/**
 * Add a dedicated submenu toggle control for parent items in the primary menu.
 * This keeps parent links clickable while submenu open/close is handled by a button.
 */
function kvizopija_add_primary_submenu_toggles( $item_output, $item, $depth, $args ) {
	if ( empty( $args->theme_location ) || 'menu-1' !== $args->theme_location ) {
		return $item_output;
	}

	if ( empty( $item->classes ) || ! is_array( $item->classes ) ) {
		return $item_output;
	}

	$has_children = in_array( 'menu-item-has-children', $item->classes, true ) || in_array( 'page_item_has_children', $item->classes, true );
	if ( ! $has_children ) {
		return $item_output;
	}

	$label = sprintf(
		/* translators: %s: menu item title. */
		esc_attr__( 'Open submenu for %s', 'kvizopija' ),
		wp_strip_all_tags( $item->title )
	);

	$item_output .= '<button class="submenu-toggle" aria-expanded="false" aria-label="' . $label . '" type="button"></button>';
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'kvizopija_add_primary_submenu_toggles', 10, 4 );

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
        'show_in_menu' => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'show_in_rest' => true,
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


// Limit search to the Questions CPT and keep the page size predictable.
function searchfilter( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        $query->set( 'post_type', array( 'questions' ) );
        $query->set( 'posts_per_page', 30 );
    }

    return $query;
}

add_filter( 'pre_get_posts', 'searchfilter' );

// Keep Questions term archives ordered from newest to oldest.
function kvizopija_order_questions_terms_archive( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_tax( 'questions_terms' ) ) {
        $query->set( 'post_type', array( 'questions' ) );
        $query->set( 'orderby', 'date' );
        $query->set( 'order', 'DESC' );
    }
}

add_action( 'pre_get_posts', 'kvizopija_order_questions_terms_archive', 20 );


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





// MemberPress non-singular protection for Questions views.
/**
 * Ensure MemberPress protection applies to questions non-singular pages:
 * - CPT archive: /questions/
 * - taxonomy archives: /questions_categories/* and /questions_terms/*
 *
 * MemberPress can skip non-singular redirects when redirect_non_singular is disabled,
 * so this theme-level guard enforces lock checks on those views.
 */
function kvizopija_protect_questions_non_singular_with_memberpress() {
    if ( is_admin() ) {
        return;
    }

    $is_questions_non_singular = is_post_type_archive( 'questions' ) || is_tax( array( 'questions_categories', 'questions_terms' ) );
    if ( ! $is_questions_non_singular ) {
        return;
    }

    // Prevent redirect loops on MemberPress unauthorized flow.
    if ( isset( $_GET['action'] ) && 'mepr_unauthorized' === $_GET['action'] ) {
        return;
    }

    if ( ! class_exists( 'MeprRule' ) || ! class_exists( 'MeprOptions' ) ) {
        return;
    }

    $request_uri_raw = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    if ( '' === $request_uri_raw ) {
        return;
    }

    $request_path = wp_parse_url( $request_uri_raw, PHP_URL_PATH );
    if ( ! is_string( $request_path ) || '' === $request_path ) {
        return;
    }

    $request_path_trail   = trailingslashit( $request_path );
    $request_path_untrail = untrailingslashit( $request_path );

    // Support both rule styles: with install subdirectory path and without it.
    $home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
    $home_path = is_string( $home_path ) ? trailingslashit( $home_path ) : '/';
    $path_without_home = $request_path_trail;
    if ( '/' !== $home_path && 0 === strpos( $request_path_trail, $home_path ) ) {
        $path_without_home = '/' . ltrim( substr( $request_path_trail, strlen( $home_path ) ), '/' );
        $path_without_home = trailingslashit( $path_without_home );
    }

    $uri_candidates = array_unique(
        array_filter(
            array(
                $request_uri_raw,
                $request_path,
                $request_path_trail,
                $request_path_untrail,
                $path_without_home,
                untrailingslashit( $path_without_home ),
                home_url( $request_path_trail ),
            )
        )
    );

    $should_block_view = false;
    foreach ( $uri_candidates as $uri_candidate ) {
        if ( MeprRule::is_uri_locked( $uri_candidate ) ) {
            $should_block_view = true;
            break;
        }
    }

    // Fallback: if URI rule did not match, still block if posts on the view are locked.
    if ( ! $should_block_view ) {
        global $wp_query;

        if ( isset( $wp_query->posts ) && is_array( $wp_query->posts ) ) {
            foreach ( $wp_query->posts as $archive_post ) {
                if ( $archive_post instanceof WP_Post && MeprRule::is_locked( $archive_post ) ) {
                    $should_block_view = true;
                    break;
                }
            }
        }
    }

    if ( ! $should_block_view ) {
        return;
    }

    $redirect_target = $request_path_trail;
    $mepr_options    = MeprOptions::fetch();
    $redirect_arg    = 'action=mepr_unauthorized&redirect_to=' . urlencode( $redirect_target );

    if ( ! empty( $mepr_options->redirect_on_unauthorized ) ) {
        $delim        = false !== strpos( $mepr_options->unauthorized_redirect_url, '?' ) ? '&' : '?';
        $redirect_url = $mepr_options->unauthorized_redirect_url . $delim . $redirect_arg;
    } else {
        $redirect_url = $mepr_options->login_page_url( $redirect_arg );
    }

    if ( is_ssl() ) {
        $redirect_url = str_replace( 'http:', 'https:', $redirect_url );
    }

    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'template_redirect', 'kvizopija_protect_questions_non_singular_with_memberpress', 20 );


add_action( 'rest_api_init', function () {
    register_rest_route( 'custom/v1', '/questions/', array(
        'methods' => 'GET',
        'callback' => 'get_custom_questions',
        'permission_callback' => '__return_true',
    ) );

    register_rest_route( 'custom/v1', '/questions-terms/', array(
        'methods' => 'GET',
        'callback' => 'get_custom_question_terms',
        'permission_callback' => '__return_true',
    ) );
} );

// Funkcija koja obraÄ‘uje prilagoÄ‘eni endpoint
function get_custom_questions( $request ) {
    $page = max( 1, absint( $request['page'] ) );
    $per_page = absint( $request['per_page'] );
    $per_page = $per_page > 0 ? min( 100, $per_page ) : 10;

    $args = array(
        'post_type' => 'questions',
        'orderby' => 'date',
        'order' => 'DESC',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'post_status' => 'publish',
        'ignore_sticky_posts' => true,
    );

    $query = new WP_Query( $args );
    $posts = $query->posts;

    $response = array();
    foreach ( $posts as $post ) {
        // Do not expose locked content through the public custom REST endpoint.
        if ( class_exists( 'MeprRule' ) && MeprRule::is_locked( $post ) ) {
            continue;
        }

        setup_postdata( $post );

        $terms = get_the_terms( $post->ID, 'questions_categories' );
        $term_list = wp_get_post_terms( $post->ID, 'questions_terms', array( 'fields' => 'all' ) );

        if ( ! is_array( $terms ) ) {
            $terms = array();
        }

        if ( is_wp_error( $term_list ) || ! is_array( $term_list ) ) {
            $term_list = array();
        }

        // Dodajte URL-ove za svaku kategoriju i pojam.
        foreach ( $terms as &$term ) {
            $term_link = get_term_link( $term );
            $term->link = is_wp_error( $term_link ) ? '' : $term_link;
        }
        foreach ( $term_list as &$single_term ) {
            $term_link = get_term_link( $single_term );
            $single_term->link = is_wp_error( $term_link ) ? '' : $term_link;
        }

        $item_data = array(
            'id' => $post->ID,
            'title' => get_the_title( $post->ID ),
            'content' => apply_filters( 'the_content', get_the_content() ),
            'terms' => $terms,
            'question_author' => get_field( 'question_author', $post->ID ),
            'question_author_url' => get_field( 'question_author_url', $post->ID ),
            'term_list' => $term_list,
            'date' => get_the_date( 'j. n. Y.', $post->ID )
        );
        $response[] = $item_data;
    }
    wp_reset_postdata();

    return new WP_REST_Response( $response, 200 );
}

if ( ! function_exists( 'kvizopija_terms_text_contains' ) ) {
    /**
     * Accent-friendly match for a term name and search query.
     */
    function kvizopija_terms_text_contains( $text, $query ) {
        $text  = (string) $text;
        $query = trim( (string) $query );

        if ( '' === $query ) {
            return true;
        }

        if ( function_exists( 'mb_stripos' ) ) {
            if ( false !== mb_stripos( $text, $query, 0, 'UTF-8' ) ) {
                return true;
            }
        } elseif ( false !== stripos( $text, $query ) ) {
            return true;
        }

        return false !== stripos( remove_accents( $text ), remove_accents( $query ) );
    }
}

if ( ! function_exists( 'kvizopija_term_starts_with_letter' ) ) {
    /**
     * Check whether a term starts with a specific letter/prefix (supports UTF-8 text).
     */
    function kvizopija_term_starts_with_letter( $term_name, $letter ) {
        $term_name = trim( (string) $term_name );
        $letter    = trim( (string) $letter );

        if ( '' === $letter ) {
            return true;
        }

        if ( function_exists( 'mb_strtoupper' ) ) {
            $term_upper   = mb_strtoupper( $term_name, 'UTF-8' );
            $letter_upper = mb_strtoupper( $letter, 'UTF-8' );
        } else {
            $term_upper   = strtoupper( $term_name );
            $letter_upper = strtoupper( $letter );
        }

        return 0 === strpos( $term_upper, $letter_upper );
    }
}

function get_custom_question_terms( $request ) {
    $page         = max( 1, absint( $request['page'] ) );
    $limit        = absint( $request['per_page'] );
    $limit        = $limit > 0 ? min( 200, $limit ) : 100;
    $sort         = isset( $request['sort'] ) ? sanitize_key( $request['sort'] ) : 'name_asc';
    $search_query = isset( $request['q'] ) ? sanitize_text_field( wp_unslash( $request['q'] ) ) : '';
    $letter       = isset( $request['letter'] ) ? sanitize_text_field( wp_unslash( $request['letter'] ) ) : '';
    $offset       = ( $page - 1 ) * $limit;

    $all_terms = get_terms(
        array(
            'taxonomy'   => 'questions_terms',
            'hide_empty' => false,
        )
    );

    if ( is_wp_error( $all_terms ) || ! is_array( $all_terms ) ) {
        return new WP_REST_Response( array(), 200 );
    }

    if ( '' !== $search_query || '' !== trim( $letter ) ) {
        $all_terms = array_values(
            array_filter(
                $all_terms,
                function( $term ) use ( $search_query, $letter ) {
                    if ( ! $term instanceof WP_Term ) {
                        return false;
                    }

                    if ( ! kvizopija_terms_text_contains( $term->name, $search_query ) ) {
                        return false;
                    }

                    return kvizopija_term_starts_with_letter( $term->name, $letter );
                }
            )
        );
    }

    if ( 'count_desc' === $sort ) {
        usort(
            $all_terms,
            function( $a, $b ) {
                if ( (int) $a->count === (int) $b->count ) {
                    return strcasecmp( remove_accents( (string) $a->name ), remove_accents( (string) $b->name ) );
                }
                return (int) $b->count - (int) $a->count;
            }
        );
    } else {
        usort(
            $all_terms,
            function( $a, $b ) {
                return strcasecmp( remove_accents( (string) $a->name ), remove_accents( (string) $b->name ) );
            }
        );
    }

    $terms    = array_slice( $all_terms, $offset, $limit );
    $response = array();

    foreach ( $terms as $term ) {
        $term_link  = get_term_link( $term );
        $response[] = array(
            'name'  => $term->name,
            'count' => (int) $term->count,
            'link'  => is_wp_error( $term_link ) ? '' : $term_link,
        );
    }

    return new WP_REST_Response( $response, 200 );
}

add_action('init', function () {
    remove_filter('pre_term_description', 'wp_filter_kses');
    add_filter('pre_term_description', 'wp_filter_post_kses');
});
