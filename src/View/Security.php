<?php
namespace Apiki\Care\View;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

if ( ! session_id() ) {
	@session_start();
}

use Apiki\Care\Core;
use Apiki\Care\Helper\Utils;
use Apiki\Care\Model\Report;
use Apiki\Care\Model\Messages;
use Apiki\Care\View\Dashboard;

class Security
{
	public static function render_stats()
	{
		$report = new Report();
		$resume = $report->get_resume_vulnerabilities();

		if ( ! Utils::get( 'care-verify' ) ) {
			$report->set_viewed();
		}

		?>
		<div>
			<?php Dashboard::render_header(); ?>

			<div class="awpc-wrap awpc-content">
				<?php self::render_wrap_header( $report ); ?>

				<?php self::render_stats_site( $resume['total'], $report ); ?>

				<?php self::render_stats_system( $resume['system'], $report ); ?>

				<?php
					self::render_stats_by_asset(
						$resume['plugins'],
						$report->get_plugins(),
						__( 'Plugins', Core::SLUG )
					);

					self::render_stats_by_asset(
						$resume['themes'],
						$report->get_themes(),
						__( 'Themes', Core::SLUG )
					);
				?>
			</div>

			<?php Dashboard::render_footer(); ?>
		</div>
		<?php
	}

	public static function render_wrap_header( $report )
	{
		$messages  = new Messages();
		$last_time = $report->get_last_time();

		?>
		<div class="awpc-wrap-header">
			<h1 class="awpc-page-title">
				<?php esc_html_e( 'Security Alerts', Core::SLUG ); ?>
			</h1>

			<?php $messages->display(); ?>

			<p>
				<a href="<?php echo esc_url( add_query_arg( 'care-verify', 1 ) ); ?>" class="awpc-btn awpc-btn-primary">
					<?php esc_html_e( 'Verify Now', Core::SLUG ); ?>
				</a>
			</p>

			<?php if ( $last_time ) : ?>
			<p>
				<?php echo sprintf( esc_html__( 'Last check at %s', Core::SLUG ), $last_time ); ?>
			</p>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function render_stats_site_small( $counter, $report )
	{
		if ( ! $report->has_really_items() ) {
			self::_render_empty_site_small();
			return;
		}

		?>
		<div class="awpc-card-status awpc-card-home">
			<?php self::_render_circle_counter( $counter ); ?>

			<div class="awpc-info">
				<h2>
					<?php echo esc_html( sprintf( __( 'Site - %s', Core::SLUG ), Utils::get_site_reference() ) ); ?>
				</h2>

				<div class="awpc-list-status">
					<?php self::render_message_stats_site( $counter ); ?>
				</div>
			</div>

			<p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Core::SLUG . '-stats-checker' ) ); ?>" class="awpc-btn awpc-btn-primary">
					<?php esc_html_e( 'View Report', Core::SLUG ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	public static function render_stats_site( $counter, $report )
	{
		if ( ! $report->has_really_items() ) {
			self::_render_empty_site();
			return;
		}

		?>
		<div class="awpc-card-status">
			<?php self::_render_circle_counter( $counter ); ?>

			<div class="awpc-info">
				<h2 class="awpc-title-status">
					<?php echo esc_html( sprintf( __( 'Site - %s', Core::SLUG ), Utils::get_site_reference() ) ); ?>
				</h2>

				<div class="awpc-list-status">
					<div class="awpc-item-list">
						<div class="awpc-asset-simple-text">
							<?php self::render_message_stats_site( $counter ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render_message_stats_site( $counter )
	{
		if ( $counter >= 1 ) {
			esc_html_e( 'Be careful! Your site is at risk. Do something right now.', Core::SLUG );
			return;
		}

		esc_html_e( 'Great! Your site seems to me with no security alert.', Core::SLUG );
	}

	public static function render_stats_by_asset( $counter, $list, $title )
	{
		if ( ! $list ) {
			self::_render_empty_asset( $title );
			return;
		}

		?>
		<div class="awpc-card-status">
			<?php self::_render_circle_counter( $counter ); ?>

			<div class="awpc-info">
				<h2 class="awpc-title-status">
					<?php esc_html_e( $title, Core::SLUG ); ?>
				</h2>

				<div class="awpc-list-status">
					<?php foreach ( $list as $item ) : ?>
					<div class="awpc-item-list">
						<div class="awpc-item-asset">
							<div class="awpc-asset-flag">
								<?php self::_render_icon_asset( $item ); ?>
							</div>

							<div class="awpc-asset-name">
								<?php echo esc_html( $item['name'] ); ?>
							</div>

							<div class="awpc-asset-simple-text">
								<?php echo esc_html( sprintf( __( 'Version %s', Core::SLUG ), $item['version'] ) ); ?>
							</div>
						</div>

						<?php self::_render_list_vulnerabilities( $item ); ?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	private static function render_stats_system( $counter, $report )
	{
		$system = $report->get_system();

		if ( ! isset( $system['wp_version'] ) ) {
			self::_render_empty_asset( __( 'WordPress', Core::SLUG ) );
			return;
		}

		?>
		<div class="awpc-card-status">
			<?php self::_render_circle_counter( $counter ); ?>

			<div class="awpc-info">
				<h2 class="awpc-title-status">
					<?php esc_html_e( 'WordPress', Core::SLUG ); ?>
				</h2>

				<div class="awpc-list-status">
					<div class="awpc-item-list">
						<div class="awpc-item-asset">
							<div class="awpc-asset-simple-text">
								<?php echo esc_html( sprintf( __( 'Version %s', Core::SLUG ), $system['wp_version'] ) ); ?>
							</div>

							<div class="awpc-asset-simple-text">
								<?php echo sprintf( esc_html__( 'SSL Activated - %s', Core::SLUG ), "<strong>{$system['ssl']}</strong>" ); ?>
							</div>
						</div>

						<?php self::_render_list_vulnerabilities( $system ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private static function _render_icon_asset( $item )
	{
		$icon = 'dashicons-yes';

		if ( ( isset( $item['vulnerabilities'] ) && count( $item['vulnerabilities'] ) >= 1 ) ) {
			$icon = 'dashicons-no-alt';
		}

		if ( isset( $item['error'] ) && ! self::is_author_apiki( $item ) ) {
			$icon = 'dashicons-warning';
		}

		echo "<span class=\"dashicons {$icon}\"></span>";
	}

	private static function _render_empty_asset( $title )
	{
		?>
		<div class="awpc-card-status">
			<div class="awpc-circle-status awpc-circle-warning">
				<span class="dashicons dashicons-search"></span>
			</div>

			<div class="awpc-info">
				<h2 class="awpc-title-status">
					<?php echo esc_html( $title ); ?>
				</h2>
			</div>
		</div>
		<?php
	}

	private static function _render_empty_site()
	{
		?>
		<div class="awpc-card-status">
			<div class="awpc-circle-status awpc-circle-warning">
				<span class="dashicons dashicons-search"></span>
			</div>

			<div class="awpc-info">
				<h2 class="awpc-title-status">
					<?php echo esc_html( sprintf( __( 'Site - %s', Core::SLUG ), Utils::get_site_reference() ) ); ?>
				</h2>

				<div class="awpc-list-status">
					<div class="awpc-item-list">
						<?php self::_render_empty_site_text(); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private static function _render_empty_site_small()
	{
		?>
		<div class="awpc-card-status awpc-card-home">
			<div class="awpc-circle-status awpc-circle-warning">
				<span class="dashicons dashicons-search"></span>
			</div>

			<div class="awpc-info">
				<h2>
					<?php echo esc_html( sprintf( __( 'Site - %s', Core::SLUG ), Utils::get_site_reference() ) ); ?>
				</h2>

				<div class="awpc-list-status">
					<?php self::_render_empty_site_text(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	private static function _render_empty_site_text()
	{
		?>
		<div class="awpc-asset-simple-text">
			<?php esc_html_e( 'Your report is empty yet. Wait while we analize your WordPress version and each plugin, and its respective version, as well as the themes.', Core::SLUG ); ?>
		</div>
		<?php
	}

	private static function _render_list_vulnerabilities( $asset )
	{
		if ( isset( $asset['error'] ) && ! self::is_author_apiki( $asset ) ) {
			self::_render_error_vulnerabilities();
			return;
		}

		if ( ! isset( $asset['vulnerabilities'] ) || count( $asset['vulnerabilities'] ) <= 0 ) {
			return;
		}

		?>
		<div class="awpc-item-sublist">
			<ul>
				<?php foreach ( $asset['vulnerabilities'] as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( "https://wpvulndb.com/vulnerabilities/{$item['id']}" ); ?>" target="_blank">
						<?php echo esc_html( self::_get_vulnerability_title( $item ) ); ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	private static function _render_error_vulnerabilities()
	{
		?>
		<div class="awpc-item-sublist">
			<?php esc_html_e( 'Sorry. We don\'t have a report available to this item.', Core::SLUG ); ?>
		</div>
		<?php
	}

	private static function _render_circle_counter( $counter )
	{
		?>
		<div class="awpc-circle-status<?php echo $counter ? ' awpc-circle-error' : ''; ?>">
			<?php echo $counter ? $counter : '✓'; ?>
		</div>
		<?php
	}

	private static function _get_vulnerability_title( $vulnerability )
	{
		$title  = $vulnerability['title'] . ' - ';
		$title .= empty( $vulnerability['fixed_in'] ) ? 'Not fixed' : "Fixed in version {$vulnerability['fixed_in']}";

		return $title;
	}

	private static function is_author_apiki( $item )
	{
		if ( ! isset( $item['author'] ) ) {
			return false;
		}

		$author = sanitize_title( $item['author'] );

		return  in_array( $author, [ 'apiki', 'apiki-wordpress'] );
	}
}
