<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kvizopija
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="footer-main">
			<?php if ( has_nav_menu( 'footer-menu' ) ) : ?>
				<div class="footer-row footer-row-menu">
					<nav id="footer-navigation" class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer Menu', 'kvizopija' ); ?>">
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
	                <p class="footer">pubkvizpitanja.com Premium - Powered by: <a class="footer-links" href="https://www.kvizopija.com/" target="_blank">Kvizopija</a></p>
	                <p class="footer-small">v0.3 - beta sa gomilom mana i nedostataka, ali i nekih fixanih bugova iz verzije v0.11</p>
	            </div>
			</div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> -->

<?php wp_footer(); ?>

</body>
</html>
