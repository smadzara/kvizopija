<?php
defined('ABSPATH') || exit;

/**
 * The layout for authenticated or guest pages
 *
 * @package memberpress-pro-template
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">

  <?php
    MeprHooks::do_action('mepr_rl_enqueue_scripts');
    wp_head(); ?>
</head>

<?php
$mepr_options = class_exists('MeprOptions') ? MeprOptions::fetch() : null;
$mepr_login_page_id = ($mepr_options && !empty($mepr_options->login_page_id)) ? (int) $mepr_options->login_page_id : 0;
$request_uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
$is_memberpress_login_request = (false !== stripos($request_uri, '/login')) || (false !== stripos($request_uri, '/prijava'));
$queried_post = get_queried_object();
$post_content = ($queried_post instanceof WP_Post) ? (string) $queried_post->post_content : '';
$has_login_shortcode = has_shortcode($post_content, 'mepr-login-form') || has_shortcode($post_content, 'mepr-login');
$is_memberpress_login_page = $is_memberpress_login_request || $has_login_shortcode || is_page('login') || ($mepr_login_page_id > 0 && is_page($mepr_login_page_id));
$resolved_body_classes = isset($body_classes) ? (string) $body_classes : 'mepr-pro-template mepr-app-layout';
if ($is_memberpress_login_page && false === strpos($resolved_body_classes, 'page-login')) {
    $resolved_body_classes .= ' page-login';
}
?>
<body <?php body_class(trim($resolved_body_classes)); ?>>
  <?php wp_body_open(); ?>
  <div id="page" class="site app-layout">
    <?php
    $use_theme_navigation = true;
    ?>

    <?php if ($use_theme_navigation) : ?>
      <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'kvizopija'); ?></a>
      <header id="masthead" class="site-header">
        <div class="site-branding">
          <?php if (is_front_page() && is_home()) : ?>
            <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
          <?php else : ?>
            <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
          <?php endif; ?>
          <?php
          $kvizopija_description = get_bloginfo('description', 'display');
          if ($kvizopija_description || is_customize_preview()) :
              ?>
            <p class="site-description"><?php echo esc_html($kvizopija_description); ?></p>
          <?php endif; ?>
        </div>

        <nav id="site-navigation" class="main-navigation">
          <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'kvizopija'); ?></button>
          <?php
          wp_nav_menu(
              [
                  'theme_location' => 'menu-1',
                  'menu_id'        => 'primary-menu',
              ]
          );
          ?>
        </nav>
      </header>
    <?php else : ?>
      <header id="masthead" class="site-header <?php echo isset($is_account_page) ? 'account-header' : ''; ?>">
        <div class="site-branding">
          <a href="<?php echo esc_url(home_url()); ?>"><img class="site-branding__logo"
              src="<?php echo esc_url_raw($logo); ?>" /></a>
        </div><!-- .site-branding -->

        <?php if ($user) : ?>
          <div class="ml-3 profile-menu">
            <div class="profile-menu__button-group">
              <button type="button" class="profile-menu__button --is-tablet" id="user-menu-button"
                aria-expanded="false" aria-haspopup="true">
                <img class="profile-menu__avatar h-8 w-8 rounded-full"
                  src="<?php echo esc_url_raw(get_avatar_url($user->ID, ['size' => '51'])); ?>"
                  alt="">

                <div class="profile-menu__text">
                  <span>
                    <?php echo esc_html($user->full_name()); ?>
                  </span>
                  <span class="profile-menu__text--small"><?php echo esc_html($user->user_email); ?></span>
                </div>

                <svg class="profile-menu__arrow_down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                  fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"></path>
                </svg>
              </button>

              <button class="profile-menu__button --is-mobile">
                <svg xmlns="http://www.w3.org/2000/svg" class="profile-menu__hamburger" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              </button>

              <div class="profile-menu__dropdown dropdown">
                <a class="profile-menu__dropdown-item dropdown__item"
                  href="<?php echo esc_url($account_url); ?>"><?php echo esc_html_x('Account', 'ui', 'memberpress'); ?></a>
                <a class="profile-menu__dropdown-item dropdown__item"
                  href="<?php echo esc_url($change_password_url); ?>"><?php echo esc_html_x('Change Password', 'ui', 'memberpress'); ?></a>
                <a class="profile-menu__dropdown-item dropdown__item"
                  href="<?php echo esc_url($logout_url); ?>"><?php echo esc_html_x('Logout', 'ui', 'memberpress'); ?></a>
              </div>

            </div>
          </div>
        <?php endif; ?>

      </header><!-- #masthead -->
    <?php endif; ?>
