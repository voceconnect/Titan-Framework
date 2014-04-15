<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class TitanFrameworkOptionSelectPosts extends TitanFrameworkOption {

	public $defaultSecondarySettings = array(
		'default' => '0', // show this when blank
		'post_type' => 'post',
		'num' => -1,
		'post_status' => 'any',
		'orderby' => 'post_date',
		'order' => 'DESC',
		'extra_query_args' => array()
	);

	public function __construct( $settings, $owner ){
		parent::__construct( $settings, $owner );

		$this->setOptions();
	}

	private function setOptions(){
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
			foreach( $posts_query->posts as $post ){
				$this->settings['options'][$post->ID] = the_title_attribute( array( 'post' => $post->ID, 'echo' => false ) );
			}
		}
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		if( !empty( $this->settings['options'] ) ){
			$wp_customize->add_control( new TitanFrameworkOptionSelectPostsControl( $wp_customize, $this->getID(), array(
				'label' => $this->settings['name'],
				'section' => $section->settings['id'],
				'settings' => $this->getID(),
				'description' => $this->settings['desc'],
				'options' => $this->settings['options'],
				'priority' => $priority
			) ) );
		}
	}
}

/*
 * WP_Customize_Control with description
 */
add_action( 'customize_register', 'registerTitanFrameworkOptionSelectPostsControl', 1 );
function registerTitanFrameworkOptionSelectPostsControl() {
	class TitanFrameworkOptionSelectPostsControl extends WP_Customize_Control {
		public $description;
		public $options;

		public function render_content() {
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<select <?php $this->link(); ?>>
					<?php
					// The default value (nothing is selected)
					printf( "<option value='%s' %s>%s</option>",
						'0',
						selected( $this->value(), '0', false ),
						"— " . __( "Select", TF_I18NDOMAIN ) . " —"
					);

					// Print all the other pages
					foreach( $this->options as $value => $label ){
						printf( "<option value='%s' %s>%s</option>",
							esc_attr( $value ),
							selected( $this->value, $value, false ),
							$label
						);
					}
					?>
				</select>
			</label>
			<?php
			if( !empty( $this->description ) ){
				echo '<p class="description">' . $this->description . '</p>';
			}
		}
	}
}