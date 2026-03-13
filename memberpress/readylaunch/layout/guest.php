<?php
defined('ABSPATH') || exit;

/**
 * ReadyLaunch guest layout override.
 * Uses theme-like header/footer and renders questions sidebar below login form.
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
  wp_head();
  ?>
</head>

<body <?php body_class('mepr-pro-template mepr-guest-layout mepr-app-layout page-login'); ?>>
<?php wp_body_open(); ?>
  <div id="page" class="site app-layout guest-layout">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'kvizopija'); ?></a>

    <header id="masthead" class="site-header">
      <div class="site-branding">
        <?php
        if (has_custom_logo()) {
            the_custom_logo();
        }
        ?>
        <?php if (is_front_page() && is_home()) : ?>
          <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
        <?php else : ?>
          <p class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a></p>
        <?php endif; ?>
      </div>

      <nav id="site-navigation" class="main-navigation">
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e('Primary Menu', 'kvizopija'); ?></button>
        <?php if (has_nav_menu('menu-1')) : ?>
          <?php
          wp_nav_menu(
              array(
                  'theme_location' => 'menu-1',
                  'menu_id'        => 'primary-menu',
              )
          );
          ?>
        <?php else : ?>
          <div>
            <ul id="primary-menu" class="menu">
              <?php
              wp_list_pages(
                  array(
                      'title_li' => '',
                  )
              );
              ?>
            </ul>
          </div>
        <?php endif; ?>
      </nav>
    </header>

    <main id="primary" class="site-main">
      <?php the_content(); ?>
    </main>

    <?php get_sidebar('questions'); ?>

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
  </div><!-- #page -->

  <?php wp_footer(); ?>
</body>
</html>

