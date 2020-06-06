<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 10/19/18
 * Time: 17:57
 */
class OP_Woocommerce_Product_Bundles{
    public function init(){
        if(file_exists(ABSPATH.'wp-content/plugins/woocommerce-product-bundles/woocommerce-product-bundles.php'))
        {
            $this->wc_bundle();
        }

    }

    //start bundle
    public function wc_bundle(){
        if(is_plugin_active( 'woocommerce-product-bundles/woocommerce-product-bundles.php' ))
        {
            if(!class_exists('WC_Bundles') )
            {
                require_once ABSPATH.'wp-content/plugins/woocommerce-product-bundles/woocommerce-product-bundles.php';
            }
            add_filter('op_product_data',[$this,'wc_product_bundle'],10,2);
            add_action('op_add_order_item_after',[$this,'op_add_order_item_after'],10,3);
        }
    }
    public function wc_product_bundle($response_data,$_product){
        $product = wc_get_product($_product->ID);
        $bundles = array();
        try{
            if($product->is_type( 'bundle' ))
            {

                $bundled_items = $product->get_bundled_items();
                $bundle_price_data = $product->get_bundle_price_data();
                

                foreach($bundled_items as $bkey => $bundled_item)
                {
                    $is_require =  true;
                    if ( $bundled_item->is_optional() ) {
                        $is_require = false;
                    }
                    $title        = $bundled_item->product->get_title();
                    $variations = array();
                    $variation_prices = array();
                    $bundle_product = $bundled_item->product;
                    if(!isset($bundle_price_data['regular_prices'][$bkey]))
                    {
                        $price = 0;
                    }else{
                        $price = $bundle_price_data['regular_prices'][$bkey];
                    }

                    if($bundled_item->is_priced_individually())
                    {
                        $price = $bundled_item->get_raw_price($bundled_item->product);
                    }
                    $quantity_min = $bundled_item->get_quantity();
                    $quantity_max = $bundled_item->get_quantity( 'max', array( 'bound_by_stock' => true ) );
                   
                    if(!$bundled_item->is_in_stock())
                    {
                        $quantity_min = 0;
                        $quantity_max = 0;
                    }
                    $variation_attributes          = $bundled_item->get_product_variation_attributes();
                    
                    $filtered = $bundled_item->get_filtered_variations();

                    if($variation_attributes && $bundle_product->get_type() == 'variable')
                    {

                        $bundled_item_variations = $bundle_product->get_available_variations();
                        $variant_products_with_attribute = array();

                        foreach($bundled_item_variations as $a_p)
                        {
                            $variant_product = wc_get_product($a_p['variation_id']);
                            $a_p_price = 0;
                            if($bundled_item->is_priced_individually()) {
                                $a_p_price = wc_get_price_excluding_tax($variant_product);
                                //update price
                                $discount           = $bundled_item->get_discount();
                                if($discount)
                                {
                                    $a_p_price = round( ( double ) $a_p_price * ( 100 - $discount ) / 100, WC_PB_Product_Prices::get_discounted_price_precision() ) ;
                                }
                            }

                            //end update price
                            if(!empty($filtered))
                            {
                                if(in_array($a_p['variation_id'],$filtered))
                                {
                                    $v_tmp = array(
                                        'value_id' => $a_p['variation_id'],
                                        'price' => $a_p_price
                                    );
                                    $variation_prices[] = $v_tmp;

                                    $variant_products_with_attribute[] = array(
                                        'value_id' => $a_p['variation_id'],
                                        'price' => $a_p_price,
                                        'attributes' => $a_p['attributes']
                                    );
                                }

                            }else{
                                $v_tmp = array(
                                    'value_id' => $a_p['variation_id'],
                                    'price' => $a_p_price
                                );
                                $variation_prices[] = $v_tmp;

                                $variant_products_with_attribute[] = array(
                                    'value_id' => $a_p['variation_id'],
                                    'price' => $a_p_price,
                                    'attributes' => $a_p['attributes']
                                );
                            }

                        }


                        foreach($variation_attributes as $key => $variants)
                        {


                            $options = array();
                            foreach($variants as $v)
                            {
                                $values = array();
                                foreach($variant_products_with_attribute as $vp)
                                {
                                    $attribute_key_1 = strtolower('attribute_'.$key);

                                    if(isset($vp['attributes'][$attribute_key_1]) && ($vp['attributes'][$attribute_key_1] == $v || $vp['attributes'][$attribute_key_1] == ''))
                                    {
                                        $values[] = $vp['value_id'];
                                    }
                                }
                                $option_tmp = array(
                                    'title' => $v,
                                    'slug' => $v,
                                    'values' => array_unique($values)
                                );
                                $options[] = $option_tmp;
                            }
                            $variant = array(
                                'title' => wc_attribute_label( $key ),
                                'slug' => $key,
                                'options' => $options
                            );
                            $variations[] = $variant;
                        }


                    }

                    $bundle = array(
                        'label' => $title,
                        'option_id' => $bkey,
                        'product_id' => $bundled_item->product->get_id(),
                        'price' =>  $price,
                        'type' => 'bundle',
                        'require' => $is_require,
                        'min_qty' => $quantity_min,
                        'max_qty' => $quantity_max,
                        'variation' => $variations,
                        'variation_price' => $variation_prices
//                            'variation_price' => array(
//                                ['value_id' => 100,'price' => 100],
//                                ['value_id' => 101,'price' => 101],
//                                ['value_id' => 102,'price' => 102],
//                                ['value_id' => 103,'price' => 103],
//                            )
                    );

                    $bundles[]= $bundle;
                }
            }

        }catch (Exception $e)
        {
            print_r($e->getMessage());
        }

        /*
         $variation = array(
             0 => array(
                 'title' => 'Color',
                 'slug' => 'color',
                 'options' => array(
                     0 => array(
                         'title' => 'Red',
                         'slug' => 'red',
                         'values' => array(100,101)
                     ),
                     1 => array(
                         'title' => 'Blue',
                         'slug' => 'blue',
                         'values' => array(102,103)
                     )
                 )
             ),
             1 => array(
                 'title' => 'Size',
                 'slug' => 'size',
                 'options' => array(
                     0 => array(
                         'title' => 'Small',
                         'slug' => 'small',
                         'values' => array(100,102)
                     ),
                     1 => array(
                         'title' => 'Medium',
                         'slug' => 'medium',
                         'values' => array(101,103)
                     )
                 )
             )
         );
         $bundle = array(
             'label' => "Bundle Option Label Item 1",
             'option_id' => 1,
             'product_id' => 1,
             'price' => 10,
             'type' => 'bundle',
             'require' => true,
             'min_qty' => 1,
             'max_qty' => 4,
             'variation' => $variation,
             'variation_price' => array(
                 ['value_id' => 100,'price' => 100],
                 ['value_id' => 101,'price' => 101],
                 ['value_id' => 102,'price' => 102],
                 ['value_id' => 103,'price' => 103],
             )
         );
         $bundles[]= $bundle;
         */

        $response_data['bundles'] = $bundles;
        return $response_data;
    }
    public function op_add_order_item_after($order,$item,$_item_data){
        $_item = $_item_data;
        $item_bundles = isset($_item_data['bundles']) ? $_item_data['bundles'] : array();
        foreach($item_bundles as $bundle)
        {
            if(!$bundle['value'] || $bundle['qty'] == 0)
            {
                continue;
            }
            $bundle_item = new WC_Order_Item_Product();
            if(isset($bundle['variation']) && !empty($bundle['variation']))
            {
                $bundle_item->set_variation_id($bundle['value']);
                $bundle['value'] = wp_get_post_parent_id($bundle['value']);
            }


            $bundle_item->set_name($bundle['label']);

            $bundle_item_qty = $_item['qty'] * $bundle['qty'];
            $bundle_item->set_quantity($bundle_item_qty);
            $bundle_item->set_product_id($bundle['value']);

            if(isset($bundle['variation']))
            {
                foreach($bundle['value_label'] as $v)
                {
                    $meta_key = $v['title'];
                    $meta_value = $v['value'];
                    $bundle_item->add_meta_data($meta_key , $meta_value);
                }
            }
            $bundle_item->set_props(
                array(
                    'custom_price' => $bundle['price']
                )
            );
            $bundle_item_sub_total = 0;//$bundle['qty'] * $bundle['price'];
            $bundle_item->set_subtotal($bundle_item_sub_total);
            $bundle_item->set_total($bundle_item_sub_total);
            $order->add_item($bundle_item);
        }
    }

}