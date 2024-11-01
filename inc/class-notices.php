<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Setup notices.
 */
class SFR_Notices {
	/**
	 * The single instance of the class.
	 *
	 * @var SFR_Notices
	 */
	protected static $_instance = null;

	/**
	 * WP_Error instance.
	 *
	 * @var null|WP_Error
	 */
	public $notices = null;

	/**
	 * @var string
	 */
	public $code = 'sfr';

	/**
	 * Main notices instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add notice.
	 *
	 * @param string $message
	 * @param string $type
	 */
	public function add( $message, $type = 'success' ) {
		$saved_notices            = $this->get_notices();
		$saved_notices[ $type ][] = $message;

		set_transient( 'sfr_notices', $saved_notices );
	}

	/**
	 * Print notices.
	 */
	public static function print_notices() {
		$instance = self::instance();

		if ( ! $instance->has_notices() ) {
			return;
		}
		?>

		<?php foreach ( $instance->get_notices() as $type => $notices ) { ?>
			<?php if ( empty( $notices ) ) {
				continue;
			} ?>
			
			<ul class="sfr-notices sfr-notices--<?php echo esc_attr( $type ); ?>">
				<?php foreach ( $notices as $notice ) { ?>
					<li class="sfr-notices__notice"><?php echo $notice; ?></li>
				<?php } ?>
			</ul>
		<?php } ?>

		<?php

		delete_transient( 'sfr_notices' );
	}

	/**
	 * Get notices.
	 *
	 * @return array
	 */
	public function get_notices() {
		$notices = array(
			'success' => array(),
			'error'   => array(),
		);

		$saved_notices = get_transient( 'sfr_notices' );

		return $saved_notices === false ? $notices : $saved_notices;
	}

	/**
	 * Has notices?
	 *
	 * @return bool
	 */
	public function has_notices() {
		$notices = $this->get_notices();

		$success = empty( $notices['success'] );
		$error   = empty( $notices['error'] );

		return ! $success || ! $error;
	}
}