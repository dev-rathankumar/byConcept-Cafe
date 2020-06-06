<?php
/**
 * Template tags used in about page
 *
 * @package pizzaro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function pizzaro_get_default_about_options() {
	$about = array(
		'fl'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'title'				=> esc_html__( 'About Us', 'pizzaro' ),
			'subtitle'			=> esc_html__( 'We are a second-generation family business established in 1972', 'pizzaro' ),
			'shortcode'			=> '[woothemes_features limit="3" per_row="3" size="370"]',
		),
		'bas'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'image'				=> '',
			'blocks'			=> array(
				'block_1'			=> wp_kses_post( '<h2>' . esc_html__( 'Pizza Basics', 'pizzaro' ) . '</h2>' ),
				'block_2'			=> wp_kses_post( '<p>' . esc_html__( 'Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum.  Aliquam non tincidunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat.', 'pizzaro' ) . '</p>' ),
				'block_3'			=> wp_kses_post( '<p>' . esc_html__( 'Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum.  Aliquam non tincidunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat.', 'pizzaro' ) . '</p>' )
			)
		),
		'brs'	=> array(
			'is_enabled'		=> 'yes',
			'priority'			=> 10,
			'animation'			=> '',
			'title'				=> esc_html__( 'We delivering Pizzas for', 'pizzaro' ),
			'image_ids'			=> '',
		)
	);

	return apply_filters( 'pizzaro_about_default_options', $about );
}

function pizzaro_get_about_meta( $merge_default = true ) {
	global $post;

	if ( isset( $post->ID ) ){
	
		$about_options = json_decode( get_post_meta( $post->ID, '_about_options', true ), true );
	
		if ( $merge_default ) {
			$default_options = pizzaro_get_default_about_options();
			$about = wp_parse_args( $about_options, $default_options );
		} else {
			$about = $about_options;
		}
	
		return apply_filters( 'pizzaro_about_meta', $about, $post );
	}
}

if ( ! function_exists( 'pizzaro_aboutpage_header' ) ) {
	/**
	 * Display about header
	 */
	function pizzaro_aboutpage_header() {

		if ( has_post_thumbnail() ) {
			?>
			<header class="about-header">
				<?php the_post_thumbnail( 'full' ); ?>
			</header>
		<?php
		}
	}
}

if ( ! function_exists( 'pizzaro_aboutpage_features' ) ) {
	/**
	 * Displays Features Shortcode
	 */
	function pizzaro_aboutpage_features() {

		$about 	= pizzaro_get_about_meta();
		$fl_options = $about['fl'];

		$is_enabled = isset( $fl_options['is_enabled'] ) ? $fl_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $fl_options['animation'] ) ? $fl_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'title'			=> isset( $fl_options['title'] ) ? $fl_options['title'] : esc_html__( 'About Us', 'pizzaro' ),
			'subtitle'		=> isset( $fl_options['subtitle'] ) ? $fl_options['subtitle'] : esc_html__( 'We are a second-generation family business established in 1972', 'pizzaro' ),
			'shortcode'		=> isset( $fl_options['shortcode'] ) ? $fl_options['shortcode'] : '[woothemes_features limit="3" per_row="3" size="370"]',
		);

		extract( $args );
		$section_class = empty( $section_class ) ? 'pizzaro-about-features' : 'pizzaro-about-features ' . $section_class;
		?>
		<div class="<?php echo esc_attr( $section_class ); ?>">
			<div class="feature-head">
				<?php
					if( ! empty( $title ) ) {
						echo '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
					}
					if( ! empty( $subtitle ) ) {
						echo '<span class="section-subtitle">' . esc_html( $subtitle ) . '</span>';
					}
				?>
			</div>
			<?php echo do_shortcode( $shortcode ); ?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_aboutpage_basics' ) ) {
	/**
	 * Displays Process
	 */
	function pizzaro_aboutpage_basics() {

		$about 	= pizzaro_get_about_meta();
		$bas_options = $about['bas'];

		$is_enabled = isset( $bas_options['is_enabled'] ) ? $bas_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $bas_options['animation'] ) ? $bas_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'img_src'		=> isset( $bas_options['image'] ) && intval( $bas_options['image'] ) ? wp_get_attachment_image_src( $bas_options['image'], array( '1920', '420' ) ) : array( '//placehold.it/1920x420', '1920', '420' ),
			'blocks'		=> array(
				'block_1'		=> isset( $bas_options['blocks']['block_1'] ) ? $bas_options['blocks']['block_1'] : wp_kses_post( '<h2>' . esc_html__( 'Pizza Basics', 'pizzaro' ) . '</h2>' ),
				'block_2'		=> isset( $bas_options['blocks']['block_2'] ) ? $bas_options['blocks']['block_2'] : wp_kses_post( '<p>' . esc_html__( 'Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum.  Aliquam non tincidunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat.', 'pizzaro' ) . '</p>' ),
				'block_3'		=> isset( $bas_options['blocks']['block_3'] ) ? $bas_options['blocks']['block_3'] : wp_kses_post( '<p>' . esc_html__( 'Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum.  Aliquam non tincidunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat.', 'pizzaro' ) . '</p>' )
			)
		);

		extract( $args );
		$blocks_count = count( $blocks );
		$column_class 	= 'col-xs-12';

		switch( $blocks_count ) {
			case 1:
				$column_class = $column_class;
			break;
			case 2:
				$column_class .= ' col-sm-6';
			break;
			case 3:
				$column_class .= ' col-sm-4';
			break;
			case 4:
				$column_class .= ' col-sm-3';
			break;
			case 5:
				$column_class .= ' col-sm-20p';
			break;
			default:
				$column_class .= ' col-sm-2';
		}

		$section_class = empty( $section_class ) ? 'pizzaro-basics ' : 'pizzaro-basics ' . $section_class;
		?>
		<div class="<?php echo esc_attr( $section_class ); ?>">
			<figure>
				<?php echo pizzaro_get_image( $img_src ); ?>
			</figure>
			<div class="container">
				<div class="row">
					<div class="blocks">
						<?php foreach( $blocks as $block ) : ?>
							<div class="block <?php echo esc_attr( $column_class ); ?>"><?php echo wp_kses_post( $block ); ?></div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'pizzaro_aboutpage_brands' ) ) {
	/**
	 * Displays Brands
	 */
	function pizzaro_aboutpage_brands() {

		$about 	= pizzaro_get_about_meta();
		$brs_options = $about['brs'];

		$is_enabled = isset( $brs_options['is_enabled'] ) ? $brs_options['is_enabled'] : 'no';

		if ( $is_enabled !== 'yes' ) {
			return;
		}

		$animation = !empty( $brs_options['animation'] ) ? $brs_options['animation'] : '';

		$args = array(
			'animation'		=> $animation,
			'title'			=> isset( $brs_options['title'] ) ? $brs_options['title'] : esc_html__( 'We delivering Pizzas for', 'pizzaro' ),
			'images'		=> array(),
		);

		if( ! empty( $brs_options['image_ids'] ) && ! is_array( $brs_options['image_ids'] ) ) {
			$brs_options['image_ids'] = explode( ',', $brs_options['image_ids'] );
		}

		$x = 0; 
		while( $x <= 4 ) {
			$args['images'][$x] = isset( $brs_options['image_ids'][$x] ) && intval( $brs_options['image_ids'][$x] ) ? wp_get_attachment_image_src( $brs_options['image_ids'][$x], array( '150', '100' ) ) : array( '//placehold.it/150x100', '150', '100' );
			$x++;
		}

		extract( $args );
		$section_class = empty( $section_class ) ? 'pizzaro-brands' : 'pizzaro-brands ' . $section_class;
		?>
		<div class="<?php echo esc_attr( $section_class ); ?>">
			<?php
				if( ! empty( $title ) ) {
					echo '<h2 class="section-title">' . esc_html( $title ) . '</h2>';
				}
				echo '<ul>';
				if( ! empty( $images ) ) {
					foreach ( $images as $key => $image ) {
						?><li><?php echo pizzaro_get_image( $image ); ?></li><?php
					}
				}
				echo '</ul>';
			?>
		</div>
		<?php
	}
}

if( ! function_exists( 'pizzaro_aboutpage_hook_control' ) ) {
	function pizzaro_aboutpage_hook_control() {
		if( is_page_template( array( 'template-aboutpage.php' ) ) ) {
			remove_all_actions( 'pizzaro_aboutpage' );

			$about = pizzaro_get_about_meta();
			add_action( 'pizzaro_aboutpage', 'pizzaro_init_structured_data',     4 );
			add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_header',         4 );
			add_action( 'pizzaro_aboutpage', 'pizzaro_homepage_content',         5 );
			add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_features',       isset( $about['fl']['priority'] ) ? intval( $about['fl']['priority'] ) : 10 );
			add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_basics',         isset( $about['bas']['priority'] ) ? intval( $about['bas']['priority'] ) : 20 );
			add_action( 'pizzaro_aboutpage', 'pizzaro_aboutpage_brands',         isset( $about['brs']['priority'] ) ? intval( $about['brs']['priority'] ) : 30 );
		}
	}
}