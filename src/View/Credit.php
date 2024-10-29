<?php
namespace Apiki\Care\View;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Apiki\Care\Core;
use Apiki\Care\Helper\Utils;
use Apiki\Care\View\Dashboard;

class Credit
{
	public static function render_page()
	{
		?>
		<div>
			<?php Dashboard::render_header(); ?>

			<div class="awpc-wrap awpc-content">
				<h1 class="awpc-page-title">
					<?php esc_html_e( 'Credits', Core::SLUG ); ?>
				</h1>

				<div class="awpc-section">
					<h2><?php esc_html_e( 'WP Safe', Core::SLUG ); ?></h2>
					<p><?php esc_html_e( 'WP Safe is an Apiki service used to analyze and process security analysis routines on the WordPress platform.', Core::SLUG ); ?></p>
				</div>

				<div class="awpc-section">
					<h2><?php esc_html_e( 'WPScan Vulnerability Database', Core::SLUG ); ?></h2>
					<p><?php esc_html_e( 'The WPScan Vulnerability Database is an online browsable version of WPScan\'s data files which are used to detect known WordPress core, plugin and theme vulnerabilities. This database has been compiled by the WPScan Team and various other contributors since WPScan\'s release. The development of the WPScan Vulnerability Database was funded by BruCON\'s 5by5 project.', Core::SLUG ); ?></p>
				</div>
			</div>

			<?php Dashboard::render_footer(); ?>
		</div>
		<?php
	}
}
