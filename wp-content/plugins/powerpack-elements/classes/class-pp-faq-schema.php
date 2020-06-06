<?php
namespace PowerpackElements\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class PP_Faq_Schema.
 */
class PP_Faq_Schema {
	
	/**
	 * FAQ Data
	 *
	 * @var faq_data
	 */
	private $faq_data;

	function __construct() {
		add_action('wp_head', array($this, 'render_faq_schema'));
	}
	
	public function render_faq_schema() {
		$faqs_data = $this->get_faqs_data();
		
		if ( $faqs_data ) {
			$schema_data = array(
				'@context'		=> 'https://schema.org',
				'@type'			=> 'FAQPage',
				'mainEntity'	=> $faqs_data
			);

			$encoded_data = wp_json_encode( $schema_data );
			?>
			<script type="application/ld+json">
				<?php print_r( $encoded_data ); ?>
			</script>
			<?php
		}
	}
	
	public function get_faqs_data() {
		$elementor = \Elementor\Plugin::$instance;
		$document = $elementor->documents->get( get_the_ID() );
		
		if ( ! $document ) {
			return;
		}
		
		$data = $document->get_elements_data();
		$widget_ids = $this->get_widget_ids();
		$faq_data = [];
		
		foreach ( $widget_ids as $widget_id ) {
			$widget_data = $this->find_element_recursive( $data, $widget_id );
			$widget = $elementor->elements_manager->create_element_instance( $widget_data );

			$settings = $widget->get_settings();
			$enable_schema = $settings['enable_schema'];
			$faq_items = $widget->get_faq_items();
			
			if ( !empty($faq_items) && $enable_schema == 'yes' ) {
				foreach ( $faq_items as $faqs ) {
					$new_data = array(
						'@type'          => 'Question',
						'name'           => $faqs['question'],
						'acceptedAnswer' =>
						array(
							'@type' => 'Answer',
							'text'  => $faqs['answer'],
						),
					);
					array_push( $faq_data, $new_data );
				}
			}
		}

		return $faq_data;
	}
	
	public function get_widget_ids() {
		$elementor = \Elementor\Plugin::$instance;
		$document = $elementor->documents->get( get_the_ID() );
		
		if ( ! $document ) {
			return;
		}
		
		$data = $document->get_elements_data();
		$widget_ids = [];
		
		$elementor->db->iterate_data( $data, function ( $element ) use ( &$widget_ids ) {
			if ( isset( $element['widgetType'] ) && 'pp-faq' === $element['widgetType'] ) {
				array_push( $widget_ids, $element['id'] );
			}
		} );

		return $widget_ids;
	}

	/**
	 * Get Widget Setting data.
	 *
	 * @since 1.4.13.2
	 * @access public
	 * @param array  $elements Element array.
	 * @param string $form_id Element ID.
	 * @return Boolean True/False.
	 */
	public function find_element_recursive( $elements, $form_id ) {

		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}
}
new PP_Faq_Schema();