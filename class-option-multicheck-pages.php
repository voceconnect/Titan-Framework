<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionMulticheckPages extends TitanFrameworkOptionMulticheck {

	public $defaultSecondarySettings = array(
		'options' => array(),
		'extra_query_args' => array()
	);

	private static $allPages;

	public function __construct( $settings, $owner ){
		parent::__construct( $settings, $owner );

		$this->setOptions();
	}

	/*
	 * Set setting options
	 */
	private function setOptions(){
		// Remember the pages so as not to perform any more lookups
		// But only keep the query if there are no extra query args passed in
		if( !empty( $this->settings['extra_query_args'] ) ){
			$pages = get_pages( $this->settings['extra_query_args'] );
		} else {
			if ( ! isset( self::$allPages ) ) {
				$pages = self::$allPages = get_pages();
			} else {
				$pages = self::$allPages;
			}
		}

		if( !empty( $pages ) ){
			$this->settings['options'] = array();
			foreach ( $pages as $page ) {
				$this->settings['options'][$page->ID] = the_title_attribute( array( 'post' => $page->ID, 'echo' => false ) );
			}
		}
	}

	/*
	 * Display for options and meta
	 */
	public function display() {
		if( !empty( $this->settings['options'] ) ){
			parent::display();
		}
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		if( !empty( $this->settings['options'] ) ){
			$wp_customize->add_control( new TitanFrameworkOptionMulticheckControl( $wp_customize, $this->getID(), array(
				'label' => $this->settings['name'],
				'section' => $section->settings['id'],
				'settings' => $this->getID(),
				'description' => $this->settings['desc'],
				'options' => $this->settings['options'],
				'priority' => $priority,
			) ) );
		}
	}
}