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
$use_theme_footer = true;
?>

<?php if ($use_theme_footer) : ?>
  <footer id="colophon" class="site-footer memberpress-pricing-footer">
    <div class="footer-main">
      <?php if (has_nav_menu('footer-menu')) : ?>
        <div class="footer-row footer-row-menu">
          <nav id="footer-navigation" class="footer-navigation" aria-label="<?php esc_attr_e('Footer Menu', 'kvizopija'); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'footer-menu',
                    'menu_id'        => 'footer-menu',
                    'container'      => false,
                    'depth'          => 1,
                )
            );
            ?>
          </nav>
        </div>
      <?php endif; ?>

      <div class="footer-row footer-row-info">
        <div class="footer-centered">
          <p class="footer">pubkvizpitanja.com Premium - Powered by: <a class="footer-links" href="https://www.kvizopija.com/" target="_blank" rel="noopener">Kvizopija</a></p>
          <p class="footer-small">v0.3 - beta sa gomilom mana i nedostataka, ali i nekih fixanih bugova iz verzije v0.11</p>
        </div>
      </div>
    </div>
  </footer>
<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
