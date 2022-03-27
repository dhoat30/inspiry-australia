<?php 

add_action('woocommerce_single_product_summary', function() { 

    echo '
    <div class="accordion-container">'; 
        productDescription(); 
        productDetails();
        // productShippingReturn();
    echo '
   
    </div>'; 
    // echo  $product->get_description();

}, 70); 

function productDescription(){ 
    global $product; 
    echo '
    <div class="item">
        <h2 class="title">
            Description
            <span>+</span>
        </h2>
        <div class="content">
        '. $product->get_description().'
        </div> 
    </div>'; 
}
function productDetails(){
    global $product; 
    $attributesArr = array(
        array(
            'name'=> 'Brand Name', 
            'value'=> $product->get_attribute( 'pa_brand-name' )
        ),
        array(
            'name'=> 'Brand Name', 
            'value'=> $product->get_attribute( 'pa_brands' )
        ),
        array(
            'name'=> 'Collection', 
            'value'=>  $product->get_attribute( 'pa_collection' )
        ),
        array(
            'name'=> 'Colour', 
            'value'=>  $product->get_attribute( 'pa_colour' )
        ), 
        array(
            'name'=> 'Design Name', 
            'value'=>  $product->get_attribute( 'pa_design-name' )
        ),
        array(
            'name'=> 'Design Style', 
            'value'=>  $product->get_attribute( 'pa_design-style' )
        )

    );
    
    foreach($product->get_attributes() as $key => $value){ 
        if( $key !== 'pa_brand-name' 
        && $key !== 'pa_brands' 
        && $key !== 'pa_collection' 
        && $key !== 'pa_colour'
        && $key !== 'pa_design-name'
        && $key !== 'pa_design-style'
        // && $key !== 'pa_availability'
        && $key !== 'pa_origin'
        && $value['visible']
        )
        { 
            array_push($attributesArr, array(
                'name'=> wc_attribute_label( $key ), 
                'value'=> $product->get_attribute( $value['name'])
            )); 
        }
       
    }
   

    echo '
    <div class="item">
        <h2 class="title">
            Details
            <span>+</span>
        </h2>
        <div class="content">
            <table class="woocommerce-product-attributes shop_attributes">
                <tbody>'; 
                foreach ($attributesArr as $key => $value) {
                    if($value['value']){ 
                        echo '
                        <tr>
                            <th class="woocommerce-product-attributes-item__label">'.$value['name'].'</th>
                            <td class="woocommerce-product-attributes-item__value">'.$value['value'].'<td>
                        </tr>'; 
                    }
                   
                   }
                echo '
                </tbody>
            </table>
        </div> 
    </div>'; 
}

function productShippingReturn(){ 
    global $product; 
    echo '
    <div class="item">
        <h2 class="title">
            Shipping & Returns
            <span>+</span>
        </h2>
        <div class="content">
        '. $product->get_description().'
        </div> 
    </div>'; 
}

