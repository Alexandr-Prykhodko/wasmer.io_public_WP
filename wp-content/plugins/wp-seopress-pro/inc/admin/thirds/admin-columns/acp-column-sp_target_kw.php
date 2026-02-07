<?php
defined( 'ABSPATH' ) or die( 'Please don&rsquo;t call the plugin directly. Thanks :)' );

class ACP_Column_sp_target_kw extends AC\Column\Meta
	implements \ACP\Editing\Editable, \ACP\Sorting\Sortable, \ACP\Export\Exportable, \ACP\Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-sp_target_kw' );
		$this->set_group( 'seopress' );
		$this->set_label( __( 'Target keywords', 'wp-seopress-pro' ) );
	}

	public function get_meta_key() {
		return '_seopress_analysis_target_kw';
	}

	public function get_value( $post_id ) {
		return esc_html( $this->get_raw_value( $post_id ) );
	}

	public function editing() {
        return new ACP\Editing\Service\Basic(
            new ACP\Editing\View\Text(),
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
		return new ACP\Search\Comparison\Meta\Text( $this->get_meta_key(), $this->get_meta_type() );
	}

}
