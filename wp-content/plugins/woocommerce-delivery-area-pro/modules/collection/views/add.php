<?php
/**
 * Add Collection page for wp-delivery-area-pro.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 2.0.0
 * @package woo-delivery-area-pro
 */
$data = array();
if ( isset( $_REQUEST['_wpnonce'] ) ) {

	$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
	if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
		die( 'Cheating...' );
	} else {
		$data = $_POST;
	}
}
global $wpdb;

$modelFactory = new WDAP_Model();
$ques_obj = $modelFactory->create_object( 'collection' );
if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_text_field( $_GET['doaction'] ) && isset( $_GET['id'] ) ) {
	$ques_obj = $ques_obj->fetch( array( array( 'id', '=', intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) ) ) ) );
	$data = isset($ques_obj[0]) ? (array)$ques_obj[0] : array();
} elseif ( ! isset( $_GET['doaction'] ) && isset( $response['success'] ) ) {
	unset( $data );
}

$settingdata = maybe_unserialize( get_option( 'wp-delivery-area-pro' ) );

$form  = new WDAP_FORM();
$title = isset( $_POST['wdap_collection_title'] ) ? $_POST['wdap_collection_title'] : '';

$form->form_name = 'addcollection';
$form->form_id = 'addcollection';
$form->set_header( esc_html__( 'Add / Update Collection', 'woo-delivery-area-pro' ), $response );
$is_posted_title = ! empty( $_POST['wdap_collection_title'] ) ? $_POST['wdap_collection_title'] : '';

$form->add_element(
	'text', 'wdap_collection_title', array(
		'lable' => esc_html__( 'Collection Title', 'woo-delivery-area-pro' ),
		'value' => ! empty( $data['title'] ) ? $data['title'] : $is_posted_title,
		'class' => 'form-control',
		'required' => true,
		'placeholder' => esc_html__( 'Enter Collection Title Here', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-6" >',
		'after' => '</div>',
		'desc' => esc_html__( 'A collection is created to associate your shop products with delivery areas that you are going to define shortly.', 'woo-delivery-area-pro' ),

	)
);

$applyOn = array(

	'All Products' => esc_html__( 'All Shop Products', 'woo-delivery-area-pro' ),
	'Selected Products' => esc_html__( 'Selected Products', 'woo-delivery-area-pro' ),
	'selected_categories' => esc_html__( 'Products With Specific Categories', 'woo-delivery-area-pro' ),
	'all_products_excluding_some' => esc_html__( 'All Shop Products Excluding Some', 'woo-delivery-area-pro' ),

);

$applyon = isset( $_POST['wdap_applyonRadio'] ) ? $_POST['wdap_applyonRadio'] : '';
$is_posted_apply = isset( $_POST['wdap_applyonRadio'] ) ? $_POST['wdap_applyonRadio'] : '';

$form->add_element(
	'radio', 'wdap_applyonRadio', array(
		'lable'   => esc_html__( 'Select Collection Products', 'woo-delivery-area-pro' ),
		'current' => ( isset( $data ['applyon'] ) && ! empty( $data ['applyon'] ) ) ? $data ['applyon'] : $is_posted_apply,
		'radio-val-label' => $applyOn,
		'default_value'   => 'All Products',
		'class' => ' switch_onoffs',
		'data'  => array( 'target' => '.wdappage_listing' ),
		'desc'  => esc_html__( 'Select products that will be associated with this collection. You can create a single collection for your shop or multiple depending on your requirements.', 'woo-delivery-area-pro' ),
	)
);

$chooseproducts = isset( $_POST['wdap_select_product'] ) ? $_POST['wdap_select_product'] : '';

$form->add_element(
	'category_selector', 'wdap_select_product', array(
		'lable' => esc_html__( 'Choose Products', 'woo-delivery-area-pro' ),
		'value' => '',
		'current' => isset( $data ['chooseproducts'] ) ? unserialize( $data ['chooseproducts'] ) : $chooseproducts,
		'class'   => 'chkbox_class wdappage_listing_wdap_select_product',
		'data_type' => 'post_type=product',
		'show' => 'false',
	)
);

$choose_categories = isset( $_POST['selectedcategories'] ) ? $_POST['selectedcategories'] : '';
$form->add_element(
	'category_selector', 'selectedcategories', array(
		'lable' => esc_html__( 'Choose Categories', 'woo-delivery-area-pro' ),
		'value' => '',
		'current' => isset( $data ['selectedcategories'] ) ? maybe_unserialize( $data ['selectedcategories'] ) : $choose_categories,
		'class' => 'chkbox_class wdappage_listing_selected_categories',
		'data_type' => 'taxonomy=product_cat',
		'show' => 'false',
		'desc' => esc_html__( 'All products falling in above selected categories will be associated with this collection', 'woo-delivery-area-pro' ),
	)
);

$exclude_products = isset( $_POST['exclude_products'] ) ? $_POST['exclude_products'] : '';

$form->add_element(
	'category_selector', 'exclude_products', array(
		'lable' => esc_html__( 'Exclude Products', 'woo-delivery-area-pro' ),
		'value' => '',
		'current' => isset( $data ['exclude_products'] ) ? maybe_unserialize( $data ['exclude_products'] ) : $exclude_products,
		'class' => 'chkbox_class wdappage_listing_all_products_excluding_some',
		'data_type' => 'post_type=product',
		'show' => 'false',
	)
);

$form->add_element(
	'group', 'wdap_zip_code', array(
		'value' => esc_html__( 'Define Your Store Delivery Area', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);

$map_region = array(
	'continents' => esc_html__( 'By Continent', 'woo-delivery-area-pro' ),
	'sub-continents' => esc_html__( 'By Sub-Continent', 'woo-delivery-area-pro' ),
	'country' => esc_html__( 'By Country', 'woo-delivery-area-pro' ),
	'zipcode' => esc_html__( 'By ZipCode', 'woo-delivery-area-pro' ),
	'by_distance' => esc_html__( 'By Distance From Store', 'woo-delivery-area-pro' ),
);

$is_posted_map_region = ! empty( $data['wdap_map_region'] ) ? $data['wdap_map_region'] : '';

$form->add_element(
	'radio', 'wdap_map_region', array(
		'lable' => esc_html__( 'Define Delivery Area By', 'woo-delivery-area-pro' ),
		'current' => ( isset( $_POST['wdap_map_region'] ) && ! empty( $_POST ['wdap_map_region'] ) ) ? $_POST ['wdap_map_region'] : $is_posted_map_region,
		'class' => 'select_region radio-inline switch_onoff',
		'radio-val-label' => $map_region,
		'data' => array( 'target' => '.wpgeop_map_region' ),
		'default_value' => 'zipcode',
		'desc' => esc_html__( 'These are the different methods that you can choose to specify delivery area for your store. You can specify continent,sub-continent,country or zipcodes. Providing comma seperated zipcode is the preferred and recommended method for specifying delivery area.', 'woo-delivery-area-pro' ),
	)
);

$countries_obj   = new WC_Countries();
$countries   = $countries_obj->__get( 'countries' );
$newchoose_continent = array();
foreach ( $countries as  $key => $values ) {
	 $newchoose_continent[] = array(
		 'id' => $key,
		 'text' => $values,
	 );
}

$map_region_values = ! empty( $data['wdap_map_region_value'] ) ? maybe_unserialize( $data['wdap_map_region_value'] ) : '';
$is_posted_country = ! empty( $_POST['wdap_map_region_setting']['country'] ) ? $_POST['wdap_map_region_setting']['country'] : '';

$form->add_element(
	'category_selector', 'wdap_map_region_setting[country]', array(
		'lable' => esc_html__( 'Choose Country', 'woo-delivery-area-pro' ),
		'data' => $newchoose_continent,
		'current' => ( isset( $map_region_values['country'] ) && ! empty( $map_region_values['country'] ) ) ? $map_region_values['country'] : $is_posted_country,
		'desc' => esc_html__( 'Choose country to display as main map.', 'woo-delivery-area-pro' ),
		'class' => 'wpgeop_map_region wpgeop_map_region_country',
		'before' => '<div class="fc-4">',
		'after' => '</div>',
		'multiple' => 'true',
		'show' => 'false',
	)
);

$select_continent = WDAP_Delivery_Area::$continent_list;
$selected_continent = array();
foreach ( $select_continent as $key => $values ) {
	$selected_continent[] = array(
		'id' => $values,
		'text' => $values,
	);
}

$is_posted_continent = ! empty( $_POST['wdap_map_region_setting']['continent'] ) ? $_POST['wdap_map_region_setting']['continent'] : '';

$form->add_element(
	'category_selector', 'wdap_map_region_setting[continent]', array(
		'lable' => esc_html__( 'Choose Continent', 'woo-delivery-area-pro' ),
		'data' => $selected_continent,
		'current' => ( isset( $map_region_values['continent'] ) && ! empty( $map_region_values['continent'] ) ) ? $map_region_values['continent'] : $is_posted_continent,
		'class' => 'wpgeop_map_region wpgeop_map_region_continents',
		'before' => '<div class="fc-4">',
		'after' => '</div>',
		'multiple' => 'true',
		'show' => 'false',
	)
);

$sub_continents = WDAP_Delivery_Area::$sub_continent_list;

$select_sub_continent = array();
foreach ( $sub_continents as $key => $values ) {
	$select_sub_continent[] = array(
		'id' => $values,
		'text' => $values,
	);
}

$is_posted_sub_continent = ! empty( $_POST['wdap_map_region_setting']['sub_continent'] ) ? $_POST['wdap_map_region_setting']['sub_continent'] : '';

$form->add_element(
	'category_selector', 'wdap_map_region_setting[sub_continent]', array(
		'lable' => esc_html__( 'Sub-Continent', 'woo-delivery-area-pro' ),
		'data' => $select_sub_continent,
		'current' => ( isset( $map_region_values['sub_continent'] ) && ! empty( $map_region_values['sub_continent'] ) ) ? $map_region_values['sub_continent'] : $is_posted_sub_continent,
		'class' => 'wpgeop_map_region wpgeop_map_region_sub-continents',
		'before' => '<div class="fc-4">',
		'after' => '</div>',
		'multiple' => 'true',
		'show' => 'false',
	)
);

if ( isset( $data['wdap_map_region_value'] ) && $data['wdap_map_region'] == 'zipcode' ) {
	$unserializezipcodes = unserialize( $data['wdap_map_region_value'] );
	$separatedzipcodes = implode( ',', $unserializezipcodes );
}

$wdap_zip_codearea = isset( $_POST['wdap_zip_codearea'] ) ? $_POST['wdap_zip_codearea'] : '';

$form->add_element(
	'textarea', 'wdap_zip_codearea', array(
		'lable' => esc_html__( 'Enter Zip Codes ', 'woo-delivery-area-pro' ),
		'value' => isset( $separatedzipcodes ) ? $separatedzipcodes : $wdap_zip_codearea,
		'class' => 'wpgeop_map_region wdap_zip_codearea wpgeop_map_region_zipcode',
		'placeholder' => esc_html__( 'Enter comma separated zip codes', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Enter comma separated multiple zip codes here where shipping for your products is available. You can also use wildcard character (*) to specify zipcodes in short form that has similar starting pattern. For eg. providing zipcode like FL* with cover all UK post codes starting with FL.', 'woo-delivery-area-pro' ),
		'id'    => 'wdap_zip_codearea',
		'before' => '<div class="fc-6" >',
		'after' => '</div>',
		'show' => 'false',
	)
);


$storedaddress = ! empty( $data['wdap_map_region_value'] ) ? maybe_unserialize( $data['wdap_map_region_value'] ) : '';
$address = '';

if ( ! empty( $storedaddress['range'] ) ) {

	$range = $storedaddress['range'];
	$address = json_decode( html_entity_decode( stripslashes( $storedaddress['address'] ) ) );
	$address = (array) $address;
	$address = $address['placename'];
}

$form->add_element(
	'text', 'wdap_store_address', array(
		'lable' => esc_html__( 'Specify Your Store Address', 'woo-delivery-area-pro' ),
		'value' => isset( $address ) ? $address : '',
		'class' => 'wpgeop_map_region wdap_zip_codearea wpgeop_map_region_by_distance',
		'placeholder' => esc_html__( 'Specify your store address', 'woo-delivery-area-pro' ),
		'desc' => esc_html__( 'Specify most nearest location to your store address. Distance difference will be calculated in kilometers  between this location and user\'s specified location from frontend and delivery message will be displayed accordingly. Note: This method works only for delivery area form that is rendered using shortcode, not on woocommerce pages delivery area form.', 'woo-delivery-area-pro' ),
		'id'    => 'wdap_store_address',
		'before' => '<div class="fc-6" >',
		'after' => '</div>',
		'show' => 'false',
	)
);

$is_post_address_range = ! empty( $_POST['wdap_store_address_range'] ) ? $_POST['wdap_store_address_range'] : '';

$form->add_element(
	'text', 'wdap_store_address_range', array(
		'lable' => esc_html__( 'Specify Store Delivery Distance', 'woo-delivery-area-pro' ),
		'value' => isset( $range ) ? $range : $is_post_address_range,
		'class' => 'wpgeop_map_region wdap_zip_codearea wpgeop_map_region_by_distance',
		'placeholder' => esc_html__( 'Specify your store address delivery area kilometer range', 'woo-delivery-area-pro' ),
		'desc' => __( 'Specify maximum distance in kilometers where delivery is available by your store. For eg. 10, this represents that your store does delivery in 10 km radius from location specified in above control. Note: This method works only for delivery area form that is rendered using shortcode, not on woocommerce pages delivery area forms.', 'woo-delivery-area-pro' ),
		'id'    => 'wdap_store_address_range',
		'before' => '<div class="fc-6" >',
		'after' => '</div>',
		'show' => 'false',
	)
);



$hasGoogleAPI = false;
if ( ! empty( $settingdata['wdap_googleapikey'] ) ) {

	$hasGoogleAPI = true;
	$form->add_element(
		'group', 'wdap_assign_polygonsd', array(
			'value' => esc_html__( 'Draw Product Delivery Area (Optional)', 'woo-delivery-area-pro' ),
			'before' => '<div class="fc-12">',
			'after' => '</div>',
			'id' => 'wdap_assign_polygonsd',

		)
	);

	$settingdata['wdap_map_height'] = isset( $settingdata['wdap_map_height'] ) ? $settingdata['wdap_map_height'] : 750;
	$settingdata['wdap_map_width'] = isset( $settingdata['wdap_map_width'] ) ? $settingdata['wdap_map_width'] : 750;

	?>
	<style type="text/css">
		#wdappolygons{
			height:<?php echo esc_html( $settingdata['wdap_map_height'] ); ?>px;
			width:<?php echo esc_html( $settingdata['wdap_map_width'] ); ?>px; margin: 0px;padding: 0px;}
	</style>
	<?php
	ob_start();
	echo "<div class='row polygon_property'><div class='fc-9'>";
	echo '<input id="pac-input" class="controls" type="text" placeholder="Enter a location">';
	echo "<div name='wdap_assignpolygonsarea' id='wdappolygons' class='form-control'></div><p class='help-block'>" . esc_html__( 'Drawing on polygon is an optional method for specifying delivery area. You can skip drawing on map if you have already specify delivery area using any of the method described above. Still you can pick drawing tool (polygon) and start creating your product delivery area. Connect the first and last point of drawing of polygon to complete it.', 'woo-delivery-area-pro' ) . '</p></div>';
	echo "<div class='fc-3 hiderow '>";
	echo '<h4 class="alert fc-title-blue alert-info">' . esc_html__( 'Shape Properties', 'woo-delivery-area-pro' ) . '<i class=" hiderow dashicons-before dashicons-trash" id="wdap-shape-delete"></i></h4>';
	echo "<div class='row hiderow'><div class='fc-6'>";
	echo FlipperCode_HTML_Markup::field_text(
		'shape_stroke_color', array(
			'value' => '#ff0000',
			'class' => 'wpdap_stroke_color color {pickerClosable:true} form-control',
			'id' => 'shape_stroke_color',
			'desc' => esc_html__( 'Stroke Color', 'woo-delivery-area-pro' ),
			'data-default-color' => '#effeff',
			'placeholder' => esc_html__( 'Stroke Color', 'woo-delivery-area-pro' ),
		)
	);
	echo "</div><div class='fc-6 shape_fill_color'>";
	$stroke_opacity = array(
		'1' => '1',
		'0.9' => '0.9',
		'0.8' => '0.8',
		'0.7' => '0.7',
		'0.6' => '0.6',
		'0.5' => '0.5',
		'0.4' => '0.4',
		'0.3' => '0.3',
		'0.2' => '0.2',
		'0.1' => '0.1',
	);
	echo FlipperCode_HTML_Markup::field_text(
		'shape_fill_color', array(
			'value' => '#00ff00',
			'class' => 'wpdap_fill_color  color {pickerClosable:true} form-control',
			'id' => 'shape_fill_color',
			'desc' => esc_html__( 'Fill Color', 'woo-delivery-area-pro' ),
			'placeholder' => esc_html__( 'Fill Color', 'woo-delivery-area-pro' ),

		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-6'>";
	$stroke_weight = array(
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => '5',
		'6' => '6',
		'7' => '7',
		'8' => '8',
		'9' => '9',
		'10' => '10',
		'11' => '11',
		'12' => '12',
		'13' => '13',
		'14' => '14',
		'15' => '15',
		'16' => '16',
		'17' => '17',
		'18' => '18',
		'19' => '19',
		'20' => '20',
	);
	echo FlipperCode_HTML_Markup::field_select(
		'wdap_shape_stroke_weight', array(
			'current' => ( isset( $data['wdap_shape_stroke_weight'] ) && ! empty( $data['wdap_shape_stroke_weight'] ) ) ? sanitize_text_field( wp_unslash( $data['wdap_shape_stroke_weight'] ) ) : 1,
			'desc' => esc_html__( 'Stroke Weight', 'woo-delivery-area-pro' ),
			'options' => $stroke_weight,
			'id' => 'wdap_shape_stroke_weight',
			'class' => 'form-control-select',
		)
	);
	echo "</div><div class='fc-6'>";
	echo FlipperCode_HTML_Markup::field_select(
		'wdap_shape_stroke_opacity', array(
			'current' => ( isset( $data['wdap_shape_stroke_opacity'] ) ) ? sanitize_text_field( wp_unslash( $data['wdap_shape_stroke_opacity'] ) ) : 1,
			'desc' => esc_html__( 'Stroke Opacity', 'woo-delivery-area-pro' ),
			'id' => 'wdap_shape_stroke_opacity',
			'options' => $stroke_opacity,
			'class' => 'form-control-select',
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-6'>";
	echo FlipperCode_HTML_Markup::field_select(
		'wdap_shape_fill_opacity', array(
			'current' => ( isset( $data['wdap_shape_fill_opacity'] ) ) ? sanitize_text_field( wp_unslash( $data['wdap_shape_fill_opacity'] ) ) : 1,
			'desc' => esc_html__( 'Fill Opacity', 'woo-delivery-area-pro' ),
			'id' => 'wdap_shape_fill_opacity',
			'options' => $stroke_opacity,
			'class' => 'form-control-select',
		)
	);
	echo '</div>';
	echo '</div>';
	echo "<div class='row hiderow'><div class='fc-12'>";
	echo FlipperCode_HTML_Markup::field_textarea(
		'wdap_shape_path', array(
			'value' => '',
			'class' => 'form-control',
			'id' => 'wdap_shape_path',
			'desc' => esc_html__( 'Cordinates', 'woo-delivery-area-pro' ),
			'data' => array( 'lng' => '' ),
			'data' => array( 'lat' => '' ),
			'placeholder' => esc_html__( 'Cordinates', 'woo-delivery-area-pro' ),
		)
	);
	echo '</div></div>';
	echo FlipperCode_HTML_Markup::field_message(
		'shape_message', array(
			'value' => esc_html__( 'Draw or click on a shape to apply properties.', 'woo-delivery-area-pro' ),
			'class' => 'help-block ',
		)
	);
	echo '';
	echo '<h4 class="alert fc-title-blue alert-info">' . esc_html__( 'Shape onclick Event', 'woo-delivery-area-pro' ) . '</h4>';
	$shape_events = array(
		'click' => 'click',
		'dblclick' => 'dblclick',
		'mouseover' => 'mouseover',
		'mouseout' => 'mouseout',
	);
	echo "<div class='row hiderow'><div class='fc-12'>";
	echo FlipperCode_HTML_Markup::field_text(
		'wdap_shape_click_url', array(
			'value' => '',
			'class' => 'form-control',
			'id' => 'wdap_shape_click_url',
			'desc' => esc_html__( 'Redirect URL', 'woo-delivery-area-pro' ),
			'placeholder' => esc_html__( 'Redirect url on click.', 'woo-delivery-area-pro' ),
		)
	);
	echo "</div></div><div class='row hiderow'><div class='fc-12'>";
	echo FlipperCode_HTML_Markup::field_textarea(
		'wdap_shape_click_message', array(
			'value' => '',
			'class' => 'form-control',
			'id' => 'wdap_shape_click_message',
			'desc' => esc_html__( 'Message on click.', 'woo-delivery-area-pro' ),
			'placeholder' => esc_html__( 'Message to display on click.', 'woo-delivery-area-pro' ),
		)
	);
	echo '</div></div>';
	echo '</div></div>';
	$map_markup = ob_get_contents();
	ob_clean();
	$form->set_col( 12 );
	$form->add_element(
		'html', 'wdap_assign_polygons', array(
			'html' => $map_markup,
			'before' => '<div class="fc-12">',
			'after' => '</div>',
			'class' => 'drawing_map_markup_control',
		)
	);
}
// End of Delivery Notifications
$form->add_element(
	'submit', 'WCRP_save_settings', array(
		'value' => esc_html__( 'Save Collection ', 'woo-delivery-area-pro' ),
		'before' => '<div class="fc-2">',
		'after' => '</div>',
	)
);
$jsonpolygons = array();
if ( ! empty( $data['assignploygons'] ) ) {
	$jsonpolygons = $data['assignploygons'];
	$jsonpolygons = str_replace( '"', "'", $jsonpolygons );
}

$addpolygons = isset( $_POST['polygons_json'] ) ? $_POST['polygons_json'] : '';
$form->add_element(
	'hidden', 'polygons_json', array(
		'value' => isset( $jsonpolygons ) ? $jsonpolygons : $addpolygons,
		'id' => 'polygons_json',
	)
);

$jsonpolygons = array();
$stored_loc = '';

if ( ! empty( $storedaddress['address'] ) ) {

	$stored_loc = sanitize_text_field( $storedaddress['address'] );
	$stored_loc = str_replace( '"', "'", $stored_loc );
	$stored_loc = stripcslashes( $stored_loc );
}

$addpolygons = isset( $_POST['store_address_json'] ) ? $_POST['store_address_json'] : $stored_loc;


$form->add_element(
	'hidden', 'store_address_json', array(
		'value' => isset( $stored_loc ) ? $stored_loc : $addpolygons,
		'id' => 'store_address_json',
	)
);

$form->add_element(
	'hidden', 'deliverypro_submission', array(
		'value' => 'yes',
	)
);
$form->add_element(
	'hidden', 'hasGoogleAPI', array(
		'value' => $hasGoogleAPI,
	)
);


$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);

if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_text_field( $_GET['doaction'] ) ) {

	$form->add_element(
		'hidden', 'entityID', array(
			'value' => intval( wp_unslash( sanitize_text_field( $_GET['id'] ) ) ),
			'id' => 'wdapentityID',
		)
	);
}
$form->render();
