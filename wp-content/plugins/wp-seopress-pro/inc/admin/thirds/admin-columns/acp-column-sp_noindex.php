<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

use AC\Type\ToggleOptions;
use AC\Helper\Select\Option;

class ACP_Column_sp_noindex extends AC\Column\Meta
	implements \ACP\Editing\Editable, \ACP\Sorting\Sortable, \ACP\Export\Exportable, \ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-sp_noindex' );
		$this->set_group( 'seopress' );
		$this->set_label( __( 'noindex?', 'wp-seopress-pro' ) );
	}

	public function get_meta_key() {
		return '_seopress_robots_index';
	}

	/**
	 * @param int $id ID
	 *
	 * @return string Value
	 */
	public function get_value( $post_id ) {
		$value = $this->get_raw_value( $post_id );
		if ( $value === 'yes' ) {
			return '<span class="dashicons dashicons-hidden"></span>';
		}
		// Show a dashicon for the 'no' state for clarity
		return '<span class="dashicons dashicons-visibility"></span>';
	}

	public function editing() {
		return new ACP\Editing\Service\Basic(
			new ACP\Editing\View\Toggle(
				ToggleOptions::create_from_array([
					'yes' => 'Yes',
					''    => 'no',
				])
			),
			new ACP\Editing\Storage\Post\Meta( $this->get_meta_key() )
		);
	}

	public function sorting() {
		return ( new ACP\Sorting\Model\MetaFactory() )
			->create( 'post', $this->get_meta_key() );
	}

	public function export() {
		return new ACP\Export\Model\Meta( new AC\MetaType( $this->get_meta_type() ), $this->get_meta_key() );
	}

	public function search() {
		return new ACP\Search\Comparison\Meta\Checkmark( $this->get_meta_key(), $this->get_meta_type() );
	}

}
