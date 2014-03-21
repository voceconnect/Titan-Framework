<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionSelectPages extends TitanFrameworkOption {

	public $defaultSecondarySettings = array(
		'default' => '0', // show this when blank
		'extra_query_args' => array()
	);

	private static $allPages;

	/*
	 * Display for options and meta
	 */
	public function display() {
		$this->echoOptionHeader();

		// Remember the pages so as not to perform any more lookups
		// But only keep the query if there are no extra query args passed in
		if( !empty( $this->extra_query_args ) ){
			if ( ! isset( self::$allPages ) ) {
				$pages = self::$allPages = get_pages();
			} else {
				$pages = self::$allPages;
			}
		} else {
			$pages = get_pages( $this->extra_query_args );
		}

		echo "<select name='" . esc_attr( $this->getID() ) . "'>";

		// The default value (nothing is selected)
		printf( "<option value='%s' %s>%s</option>",
			'0',
			selected( $this->getValue(), '0', false ),
			"— " . __( "Select", TF_I18NDOMAIN ) . " —"
		);

		// Print all the other pages
		foreach ( $pages as $page ) {
			printf( "<option value='%s' %s>%s</option>",
				esc_attr( $page->ID ),
				selected( $this->getValue(), $page->ID, false ),
				get_the_title( $page->ID )
			);
		}
		echo "</select>";

		$this->echoOptionFooter();
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$wp_customize->add_control( new TitanFrameworkCustomizeControl( $wp_customize, $this->getID(), array(
			'label' => $this->settings['name'],
			'section' => $section->settings['id'],
			'settings' => $this->getID(),
			'type' => 'dropdown-pages',
			'description' => $this->settings['desc'],
			'priority' => $priority,
		) ) );
	}
}

?>