<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionMulticheckCategories extends TitanFrameworkOptionMulticheck {

	public $defaultSecondarySettings = array(
		'options' => array(),
		'orderby' => 'name',
		'order' => 'ASC',
		'taxonomy' => 'category',
		'hide_empty' => false,
		'show_count' => false,
	);

	public __construct( $settings, $owner ){
		parent::__construct( $settings, $owner );

		$this->setOptions();
	}

	/*
	 * Set setting options
	 */
	private function setOptions(){
		$args = array(
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order'],
			'taxonomy' => $this->settings['taxonomy'],
			'hide_empty' => $this->settings['hide_empty'] ? '1' : '0',
			'child_of' => !empty( $this->settings['child_of'] ) ? $this->settings['child_of'] : 0,
			'parent' => !empty( $this->settings['parent'] ) ? $this->settings['parent'] : false,
			'exclude' => !empty( $this->settings['exclude'] ) ? $this->settings['exclude'] : false,
			'include' => !empty( $this->settings['include'] ) ? $this->settings['include'] : false
		);

		$categories = get_categories( $args );

		$this->settings['options'] = array();
		foreach ( $categories as $category ) {
			$this->settings['options'][$category->term_id] = $category->name . ( $this->settings['show_count'] ? " (" . $category->count . ")" : '' );
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