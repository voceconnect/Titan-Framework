<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionMulticheckPosts extends TitanFrameworkOptionMulticheck {

	public $defaultSecondarySettings = array(
		'options' => array(),
		'post_type' => 'post',
		'num' => -1,
		'post_status' => 'any',
		'orderby' => 'post_date',
		'order' => 'DESC',
		'extra_query_args' => array()
	);

	/*
	 * Display for options and meta
	 */
	public function display() {
		$query_args = array(
			'post_type' => $this->settings['post_type'],
			'posts_per_page' => $this->settings['num'],
			'post_status' => $this->settings['post_status'],
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order'],
			'fields' => 'ids'
		);

		if( !empty( $this->settings['extra_query_args'] ) ){
			$query_args = array_merge( $query_args, $this->settings['extra_query_args'] );
		}

		$posts_query = new WP_Query( $query_args );

		if( !empty( $posts_query->posts ) ){
			$this->settings['options'] = array();
			foreach( $posts_query->posts as $post_id ){
				$this->settings['options'][$post_id] = the_title_attribute( array( 'post' => $post_id, 'echo' => false ) );
			}

			parent::display();
		}
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$args = array(
			'post_type' => $this->settings['post_type'],
			'posts_per_page' => $this->settings['num'],
			'post_status' => $this->settings['post_status'],
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order'],
		);

		$posts = get_posts( $args );

		$this->settings['options'] = array();
		foreach ( $posts as $post ) {
			$this->settings['options'][$post->ID] = $post->post_title;
		}

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