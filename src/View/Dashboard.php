<?php
namespace Apiki\Care\View;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Apiki\Care\Core;
use Apiki\Care\Helper\Utils;
use Apiki\Care\Model\Report;
use Apiki\Care\View\Security;

class Dashboard
{
	public static function render_page()
	{
		?>
		<div>
			<?php self::render_header(); ?>

			<div class="awpc-wrap awpc-content">
				<h1 class="awpc-page-title">
					<?php esc_html_e( 'Dashboard', Core::SLUG ); ?>
				</h1>

				<?php self::render_section_security_alerts(); ?>

				<div class="awpc-section">
					<h2>
						<?php esc_html_e( 'About the WP Care Plugin', Core::SLUG ); ?>
					</h2>
					<p>
						<?php esc_html_e( 'A solution to monitor your WordPress installation, plugins and themes in use to alert you to existing security vulnerabilities.', Core::SLUG ); ?>
					</p>
					<p>
						<?php esc_html_e( 'The WP Care plugin security alerts is an Apiki initiative to help you keep only WordPress versions, plugins, and themes safe.', Core::SLUG ); ?>
					</p>

					<p>
						<ul>
							<li>
								<span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e( 'All analyzes are automated;', Core::SLUG ); ?>
							</li>
							<li>
								<span class="dashicons dashicons-wordpress"></span> <?php esc_html_e( 'The WordPress version is checked for vulnerabilities;', Core::SLUG ); ?>
							</li>
							<li>
								<span class="dashicons dashicons-shield-alt"></span> <?php esc_html_e( 'Each Plugin, and its respective version, is checked for vulnerabilities. As well as each Theme.', Core::SLUG ); ?>
							</li>
						</ul>
					</p>
				</div>

				<div class="awpc-section">
					<h2>
						<?php esc_html_e( 'About Apiki Company', Core::SLUG ); ?>
					</h2>
					<p>
						<?php esc_html_e( 'Company of the iMasters Group, is the first company specialized in WordPress in Brazil, focused solely and exclusively on this platform. Responsible for large cases in the market and proudly certified by its customers.', Core::SLUG ); ?>
					</p>
				</div>

				<?php self::render_section_contact(); ?>
			</div>

			<?php self::render_footer(); ?>
		</div>
		<?php
	}

	public static function render_section_contact()
	{
		?>
		<div class="awpc-section awpc-contact">
			<h2>
				<?php esc_html_e( 'Do you want to talk to Apiki?', Core::SLUG ); ?>
			</h2>

			<p>
				<?php echo sprintf( esc_html__( 'Talk to us. %s We specialize in WordPress.', Core::SLUG ), '<br>' ); ?>
			</p>

			<p>
				<a href="https://apiki.com/fale-com-gente/" class="awpc-btn awpc-btn-primary" target="_blank" rel="noopener">
					<?php esc_html_e( 'Contact us', Core::SLUG ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	public static function render_section_security_alerts()
	{
		$report = new Report();
		$resume = $report->get_resume_vulnerabilities();

		?>
		<div class="awpc-section awpc-section-primary">
			<h2>
				<?php esc_html_e( 'Security Alerts', Core::SLUG ); ?>
			</h2>

			<p>
				<?php esc_html_e( 'Monitor your WordPress installation, plugins and themes in use against security vulnerabilities.', Core::SLUG ); ?>
			</p>

			<?php Security::render_stats_site_small( $resume['total'], $report ); ?>
		</div>
		<?php
	}

	public static function render_header()
	{
		?>
		<header class="awpc-header">
			<div class="awpc-container">
				<div class="awpc-branding">
					<a href="https://apiki.com/empresa-especializada-em-wordpress/?utm_source=plugin_wp_care&utm_medium=header&utm_campaign=apiki_empresa_especializada_em_wordpress" title="<?php esc_html_e( 'Apiki. Specialized in WordPress', Core::SLUG ); ?>" target="_blank" rel="noopener">
						<img src="<?php echo esc_url( Core::plugins_url( 'assets/images/branding.png' ) ); ?>" alt="<?php esc_html_e( 'Apiki. Specialized in WordPress', Core::SLUG ); ?>" width="200"/>
					</a>
				</div>

				<ul class="awpc-menu">
					<li>
						<a href="https://apiki.com/produtos/apiki-wp-care/?utm_source=plugin_wp_care&utm_medium=header&utm_campaign=plugin_wp_care" title="<?php esc_html_e( 'WP Care. An Apiki product for WordPress specialized support.', Core::SLUG ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'WP Care', Core::SLUG ); ?>
						</a>
					</li>
					<li>
						<a href="https://apiki.com/suporte/?utm_source=plugin_wp_care&utm_medium=header" title="<?php esc_html_e( 'Need help? We can help you.', Core::SLUG ); ?>" target="_blank" rel="noopener">
							<?php esc_html_e( 'Help', Core::SLUG ); ?>
						</a>
					</li>
				</ul>
			</div>
		</header>
		<?php
	}

	public static function render_footer()
	{
		?>
		<footer class="awpc-footer">
			<ul class="awpc-menu">
				<li>
					<a href="https://apiki.com/empresa-especializada-em-wordpress/?utm_source=plugin_wp_care&utm_medium=footer&utm_campaign=apiki_empresa_especializada_em_wordpress" title="<?php esc_html_e( 'Apiki. Specialized in WordPress', Core::SLUG ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'Apiki', Core::SLUG ); ?>
					</a>
				</li>
				<li>
					<a href="https://apiki.com/produtos/apiki-wp-care/?utm_source=plugin_wp_care&utm_medium=footer&utm_campaign=plugin_wp_care" title="<?php esc_html_e( 'WP Care. An Apiki product for WordPress specialized support.', Core::SLUG ); ?>" target="_blank" rel="noopener">
						<?php esc_html_e( 'WP Care', Core::SLUG ); ?>
					</a>
				</li>
			</ul>

			<ul class="awpc-menu">
				<li>
					<a href="https://www.facebook.com/ApikiWordPress" target="_blank" rel="noopener">
						<img class="awpc-social-icon" src="<?php echo esc_url( Core::plugins_url( 'assets/images/icons/facebook.svg' ) ); ?>"/>
					</a>
				</li>
				<li>
					<a href="https://twitter.com/apikiWordPress" target="_blank" rel="noopener">
						<img class="awpc-social-icon" src="<?php echo esc_url( Core::plugins_url( 'assets/images/icons/twitter.svg' ) ); ?>"/>
					</a>
				</li>
				<li>
					<a href="https://www.linkedin.com/company/apiki" target="_blank" rel="noopener">
						<img class="awpc-social-icon" src="<?php echo esc_url( Core::plugins_url( 'assets/images/icons/linkedin.svg' ) ); ?>"/>
					</a>
				</li>
				<li>
					<a href="https://www.instagram.com/apikiwordpress" target="_blank" rel="noopener">
						<img class="awpc-social-icon" src="<?php echo esc_url( Core::plugins_url( 'assets/images/icons/instagram.svg' ) ); ?>"/>
					</a>
				</li>
				<li>
					<a href="https://www.youtube.com/channel/UC__ToR3hqjs1ZktdLIWqYFA" target="_blank" rel="noopener">
						<img class="awpc-social-icon" src="<?php echo esc_url( Core::plugins_url( 'assets/images/icons/youtube.svg' ) ); ?>"/>
					</a>
				</li>
				<li>
					<a href="https://github.com/Apiki" target="_blank" rel="noopener">
						<img class="awpc-social-icon" src="<?php echo esc_url( Core::plugins_url( 'assets/images/icons/github.svg' ) ); ?>"/>
					</a>
				</li>
			</ul>
		</footer>
		<?php
	}
}
