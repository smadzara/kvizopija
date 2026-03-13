<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package kvizopija
 */

?>

<!doctype html>

<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="https://gmpg.org/xfn/11">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

            <!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-WHSMNMQ');</script>
            <!-- End Google Tag Manager -->

        	<!-- Google tag (gtag.js) -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=G-8ZG00G03MQ"></script>
				<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());

				gtag('config', 'G-8ZG00G03MQ');
				</script>
			<!-- END Google tag (gtag.js) -->

			<!-- Global site tag (gtag.js) - Google Analytics -OLD- -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=UA-239932210-1"></script>
				<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());

				gtag('config', 'UA-239932210-1');
				</script>
			<!--END Global site tag (gtag.js) - Google Analytics -OLD- -->

            <!-- AdSense -->
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4983269975347159" crossorigin="anonymous"></script>
            <!-- AdSense - END -->

		<?php wp_head(); ?>

	</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'kvizopija' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="site-branding">
			<?php
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$kvizopija_description = get_bloginfo( 'description', 'display' );
			if ( $kvizopija_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $kvizopija_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif; ?>
		</div>

		<nav id="site-navigation" class="main-navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'kvizopija' ); ?></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</nav>
	</header>
<?php /*
<div align="center" style="padding-top: 80px; position: relative; z-index: -9999 !important;">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4983269975347159"
        crossorigin="anonymous"></script>
    <!-- pkp.com - responsive -->
    <ins class="adsbygoogle"
        style="display:block; z-index: -9999 !important;"
        data-ad-client="ca-pub-4983269975347159"
        data-ad-slot="2181067593"
        data-ad-format="auto"
        data-full-width-responsive="true"></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
    <p style="text-align:center">Klikom na reklamu podržavate rad ove stranice, na čemu najljubaznije zahvaljujemo. :)</p>

	<script>

		// Function to send data to the server
		function sendVisitorData(userData) {
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "https://localhost/kvizopija/record_visitor.php", true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("data=" + encodeURIComponent(JSON.stringify(userData)));
		}

		// Collecting various information from the browser
		var userData = {
			userAgent: navigator.userAgent,
			language: navigator.language,
			screenWidth: screen.width,
			screenHeight: screen.height,
			screenColorDepth: screen.colorDepth || 'unknown',
			screenPixelDepth: screen.pixelDepth || 'unknown',
			deviceMemory: navigator.deviceMemory || 'unknown',
			hardwareConcurrency: navigator.hardwareConcurrency || 'unknown',
			platform: navigator.platform,
			maxTouchPoints: navigator.maxTouchPoints || 0,
			isJavaEnabled: navigator.javaEnabled() ? 'Yes' : 'No',
			cookieEnabled: navigator.cookieEnabled ? 'Yes' : 'No',
			onlineStatus: navigator.onLine ? 'Online' : 'Offline',
			doNotTrack: navigator.doNotTrack || 'unknown',
			connectionType: navigator.connection ? navigator.connection.effectiveType : 'unknown',
			connectionDownlink: navigator.connection ? navigator.connection.downlink : 'unknown',
			connectionRtt: navigator.connection ? navigator.connection.rtt : 'unknown',
			connectionSaveData: navigator.connection ? (navigator.connection.saveData ? 'Yes' : 'No') : 'unknown',
			// ... other properties
		};

		// Check if userAgentData is available and use it to get more detailed information
		if (navigator.userAgentData) {
			navigator.userAgentData.getHighEntropyValues(["model", "platform", "platformVersion"])
				.then(uaData => {
					userData.uaModel = uaData.model || 'unknown';
					userData.uaPlatform = uaData.platform || 'unknown';
					userData.uaPlatformVersion = uaData.platformVersion || 'unknown';

					// Send the data after getting additional information
					sendVisitorData(userData);
				});
		} else {
			// Send the data immediately if userAgentData is not available
			sendVisitorData(userData);
		}

    </script>

</div>
*/?>

	

