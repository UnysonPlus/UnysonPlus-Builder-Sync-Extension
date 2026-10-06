<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Builder Sync — keeps Unyson+ Theme Settings and another page builder's global design in step.
 *
 *   Theme Settings saved  → every available provider's push( design )
 *   builder settings saved → that provider's pull() → FW_Builder_Sync_Design::write()
 *
 * One static flag stops the echo: a push writes the builder directly (no builder save event), a pull
 * writes Theme Settings directly (no settings-form event), and either one in flight blocks the other.
 */
class FW_Extension_Builder_Sync extends FW_Extension {

	/** @var FW_Builder_Sync_Provider[]|null */
	private $providers = null;

	/** @var bool a sync is writing — ignore the events it causes */
	private static $busy = false;

	/**
	 * @internal
	 */
	public function _init() {
		require_once $this->get_declared_path( '/includes/class-fw-builder-sync-design.php' );
		require_once $this->get_declared_path( '/includes/class-fw-builder-sync-provider.php' );
		require_once $this->get_declared_path( '/includes/providers/class-fw-builder-sync-elementor.php' );

		add_action( 'fw_settings_form_saved', array( $this, '_on_theme_settings_saved' ), 20 );
		// Providers hook their builder once every plugin has loaded (the builder may load after us).
		add_action( 'init', array( $this, '_listen' ), 20 );
	}

	/**
	 * Every registered provider, available or not.
	 *
	 * @return FW_Builder_Sync_Provider[]
	 */
	public function get_providers() {
		if ( null === $this->providers ) {
			$p = apply_filters( 'fw_builder_sync_providers', array( 'elementor' => new FW_Builder_Sync_Elementor() ) );
			$this->providers = array();
			foreach ( (array) $p as $prov ) {
				if ( $prov instanceof FW_Builder_Sync_Provider ) { $this->providers[ $prov->slug() ] = $prov; }
			}
		}
		return $this->providers;
	}

	/** @return FW_Builder_Sync_Provider|null */
	public function get_provider( $slug ) {
		$all = $this->get_providers();
		return isset( $all[ $slug ] ) ? $all[ $slug ] : null;
	}

	/** 'both' | 'to_builder' | 'off' */
	public function mode() {
		$m = fw_get_db_ext_settings_option( $this->get_name(), 'mode', 'both' );
		return in_array( $m, array( 'both', 'to_builder', 'off' ), true ) ? $m : 'both';
	}

	/**
	 * Push the current Theme Settings design to one provider (or all available ones).
	 * Public so other code — the Site Converter after an import — can sync on demand.
	 *
	 * @param string $only a provider slug, or '' for every available provider
	 * @return array provider slug => changed builder keys
	 */
	public function push( $only = '' ) {
		if ( self::$busy || 'off' === $this->mode() ) { return array(); }
		self::$busy = true;
		$out = array();
		try {
			$design = FW_Builder_Sync_Design::read();
			foreach ( $this->get_providers() as $slug => $prov ) {
				if ( ( '' !== $only && $only !== $slug ) || ! $prov->is_available() ) { continue; }
				$out[ $slug ] = $prov->push( $design );
			}
		} finally {
			self::$busy = false;
		}
		return $out;
	}

	/**
	 * Pull one provider's builder design into Theme Settings.
	 *
	 * @return string[] the Theme Settings keys that changed
	 */
	public function pull( $slug ) {
		if ( self::$busy || 'both' !== $this->mode() ) { return array(); }
		$prov = $this->get_provider( $slug );
		if ( ! $prov || ! $prov->is_available() ) { return array(); }
		self::$busy = true;
		try {
			$res = $prov->pull();
			return FW_Builder_Sync_Design::write( (array) ( $res['design'] ?? array() ), (array) ( $res['renames'] ?? array() ) );
		} finally {
			self::$busy = false;
		}
	}

	/**
	 * @internal
	 */
	public function _on_theme_settings_saved() {
		$this->push();
	}

	/**
	 * @internal
	 */
	public function _listen() {
		foreach ( $this->get_providers() as $slug => $prov ) {
			if ( ! $prov->is_available() ) { continue; }
			$prov->listen( function () use ( $slug ) { $this->pull( $slug ); } );
		}
	}
}
