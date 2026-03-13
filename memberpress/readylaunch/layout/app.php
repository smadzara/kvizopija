<?php
defined('ABSPATH') || exit;

$mepr_options = class_exists('MeprOptions') ? MeprOptions::fetch() : null;
$mepr_login_page_id = ($mepr_options && !empty($mepr_options->login_page_id)) ? (int) $mepr_options->login_page_id : 0;
$request_uri = isset($_SERVER['REQUEST_URI']) ? (string) wp_unslash($_SERVER['REQUEST_URI']) : '';
$is_memberpress_login_request = (false !== stripos($request_uri, '/login')) || (false !== stripos($request_uri, '/prijava'));
$queried_post = get_queried_object();
$post_content = ($queried_post instanceof WP_Post) ? (string) $queried_post->post_content : '';
$has_login_shortcode = has_shortcode($post_content, 'mepr-login-form') || has_shortcode($post_content, 'mepr-login');
$is_memberpress_login_page = $is_memberpress_login_request || $has_login_shortcode || is_page('login') || ($mepr_login_page_id > 0 && is_page($mepr_login_page_id));

do_action('mepr_rl_before_main', get_defined_vars()); ?>
<main id="primary" class="site-main <?php echo esc_attr($wrapper_classes) ?>">
  <?php the_content(); ?>

  <div class="mepr-rl-footer-widgets">
    <?php if (
      is_active_sidebar('mepr_rl_registration_footer') &&
      (MeprReadyLaunchCtrl::template_enabled('checkout') || MeprAppHelper::has_block('memberpress/checkout'))
) : ?>
      <div id="mepr-rl-login-registration-widget" class="mepr-rl-login-registration-widget widget-area" role="complementary">
        <?php dynamic_sidebar('mepr_rl_registration_footer'); ?>
      </div>
    <?php endif; ?>

    <?php if (
      is_active_sidebar('mepr_rl_account_footer') &&
      (MeprReadyLaunchCtrl::template_enabled('account') || MeprAppHelper::has_block('memberpress/pro-account-tabs'))
) : ?>
      <div id="mepr-rl-registration-footer-widget" class="mepr-rl-registration-footer-widget widget-area" role="complementary">
        <?php dynamic_sidebar('mepr_rl_account_footer'); ?>
      </div>
    <?php endif; ?>

    <?php if (is_active_sidebar('mepr_rl_global_footer')) : ?>
      <div id="mepr-rl-global-footer-widget" class="mepr-rl-global-footer-widget widget-area" role="complementary">
        <?php dynamic_sidebar('mepr_rl_global_footer'); ?>
      </div>
    <?php endif; ?>
  </div>

</main>

<?php get_sidebar('questions'); ?>

<?php
do_action('mepr_rl_after_main', get_defined_vars()); ?>
