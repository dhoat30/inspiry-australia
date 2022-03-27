<?php
//routes

add_action("rest_api_init", "search_route");

function search_route() {
    // search products
    register_rest_route("inspiry/v1/", "search", array(
        "methods" => "GET",
        "callback" => "searchProducts"
    ));

     // search all products
     register_rest_route("inspiry/v1/", "all-products-search", array(
        "methods" => "GET",
        "callback" => "allProductsSearch"
    ));
	
}

// all products search
function allProductsSearch($data){
    $term = sanitize_text_field($data["term"]);
    $argsSearch = array(
        'post_type' => 'product',
        'posts_per_page'=> 100, 
        "s"=> $term
    );
    $incVar = 1; 
    $search = new WP_Query( $argsSearch );
    $secondResult =  array(); 
    while($search->have_posts()){ 
        $search->the_post(); 
        $incVar++; 
        if($incVar > 10){ 
            array_push($secondResult, array(
                "title"=> get_the_title(),
                  "image"=> get_the_post_thumbnail_url(get_the_id()), 
                "link"=> get_the_permalink()
            ));
        }
      
    }
  
   
        $argsSearch = array(
            'post_type' => 'product',
            'posts_per_page'=> 100,
            "s"=> substr_replace($term, "", -1)
        );
        $search = new WP_Query( $argsSearch );
      
        $incrementVar = 1; 
        while($search->have_posts()){ 
            $search->the_post(); 

            $incrementVar++; 
            if($incrementVar > 10){ 
            array_push($secondResult, array(
                "title"=> get_the_title(),
                "link"=> get_the_permalink(),
				  "image"=> get_the_post_thumbnail_url(get_the_id()), 

            ));
        }
        }
        return $secondResult;
    
    
}

function searchProducts($data){
    $term = sanitize_text_field($data["term"]);
    $argsSearch = array(
        'post_type' => 'product',
        'posts_per_page'=> 10,
        "s"=> $term
    );
    $search = new WP_Query( $argsSearch );
    $productResult =  array(); 
    while($search->have_posts()){ 
        $search->the_post(); 

        array_push($productResult, array(
            "title"=> get_the_title(),
            "image"=> get_the_post_thumbnail_url(get_the_id()), 
            "link"=> get_the_permalink()
        ));
    }
    
        $argsSearch = array(
            'post_type' => 'product',
            'posts_per_page'=> 10,
            "s"=> substr_replace($term, "", -1)
        );
        $search = new WP_Query( $argsSearch );
        $productResult =  array(); 
        while($search->have_posts()){ 
            $search->the_post(); 
    
            array_push($productResult, array(
                "title"=> get_the_title(),
                "image"=> get_the_post_thumbnail_url(get_the_id()), 
                "link"=> get_the_permalink()
            ));
        }
        return $productResult;
}



?>