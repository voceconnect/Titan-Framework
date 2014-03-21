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

	/*
	 * Display for options and meta
	 */
	public function display() {
		$this->echoOptionHeader();

		$query_args = array(
			'post_type' => $this->settings['post_type'],
			'posts_per_page' => $this->settings['num'],
			'post_status' => $this->settings['post_status'],
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order']
		);

		if( !empty( $this->settings['extra_query_args'] ) ){
			$query_args = array_merge( $query_args, $this->settings['extra_query_args'] );
		}

		$posts_query = new WP_Query( $query_args );

		echo "<select name='" . esc_attr( $this->getID() ) . "'>";

		// The default value (nothing is selected)
		printf( "<option value='%s' %s>%s</option>",
			'0',
			selected( $this->getValue(), '0', false ),
			"— " . __( "Select", TF_I18NDOMAIN ) . " —"
		);

		// Print all the other pages
		if( $posts_query->have_posts() ):
			while ( $posts_query->have_posts() ): $posts_query->the_post();
				printf( "<option value='%s' %s>%s</option>",
					esc_attr( get_the_ID() ),
					selected( $this->getValue(), get_the_ID(), false ),
					get_the_title()
				);
			endwhile; wp_reset_postdata();
		endif;
		echo "</select>";

		$this->echoOptionFooter();
	}

	/*
	 * Display for theme customizer
	 */
	public function registerCustomizerControl( $wp_customize, $section, $priority = 1 ) {
		$wp_customize->add_control( new TitanFrameworkOptionSelectPostsControl( $wp_customize, $this->getID(), array(
			'label' => $this->settings['name'],
			'section' => $section->settings['id'],
			'settings' => $this->getID(),
			'description' => $this->settings['desc'],
			'post_type' => $this->settings['post_type'],
			'posts_per_page' => $this->settings['num'],
			'post_status' => $this->settings['post_status'],
			'orderby' => $this->settings['orderby'],
			'order' => $this->settings['order'],
			'extra_query_args' => $this->settings['extra_query_args'],
			'priority' => $priority
		) ) );
	}
}

/*
 * WP_Customize_Control with description
 */
add_action( 'customize_register', 'registerTitanFrameworkOptionSelectPostsControl', 1 );
function registerTitanFrameworkOptionSelectPostsControl() {
	class TitanFrameworkOptionSelectPostsControl extends WP_Customize_Control {
		public $description;
		public $post_type;
		public $num;
		public $post_status;
		public $orderby;
		public $order;
		public $extra_query_args;

		public function render_content() {
			$query_args = array(
				'post_type' => $this->post_type,
				'posts_per_page' => $this->num,
				'post_status' => $this->post_status,
				'orderby' => $this->orderby,
				'order' => $this->order,
				'extra_query_args' => $this->extra_query_args
			);

			$posts_query = new WP_Query( $query_args );

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
					if( $posts_query->have_posts() ):
						while ( $posts_query->have_posts() ): $posts_query->the_post();
							printf( "<option value='%s' %s>%s</option>",
								esc_attr( get_the_ID() ),
								selected( $this->value, get_the_ID(), false ),
								get_the_title()
							);
						endwhile; wp_reset_postdata();
					endif;
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