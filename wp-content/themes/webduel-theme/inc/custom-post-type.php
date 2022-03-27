<?php 
//custom post register
add_post_type_support( "sliders", "thumbnail" ); 
add_post_type_support( "loving", "thumbnail" ); 
add_post_type_support( "blogs", "thumbnail" );
add_post_type_support( "shop-my-fav", "thumbnail" );
add_post_type_support( "shop_by_brand", "thumbnail" );
add_post_type_support( "modal", "thumbnail" );
add_post_type_support( "shop_by_brand", "trending-now" );  

function register_custom_type2(){ 
   // boards

      register_post_type("boards", array(
        'show_in_rest' => true, 
         'has_archive' => true,
         "supports" => array("title", "page-attributes", 'editor'), 
         "public" => true, 
         "show_ui" => true, 
         "hierarchical" => true,
         "labels" => array(
            "name" => "Boards", 
            "add_new_item" => "Add New Board", 
            "edit_item" => "Edit Board", 
            "all_items" => "All Boards", 
            "singular_name" => "Board"
         ), 
         "menu_icon" => "dashicons-heart"
      )
      ); 
   
   //Covid 19 updates 
   register_post_type("modal", array(
      "supports" => array("title", "editor"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Modal", 
         "add_new_item" => "Add New Modal", 
         "edit_item" => "Edit Modal", 
         "all_items" => "All Modals", 
         "singular_name" => "Modal"
      ), 
      "menu_icon" => "dashicons-warning"
   )
   ); 
   //Banner
   register_post_type("banners", array(
      "supports" => array("title"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Banner", 
         "add_new_item" => "Add New Banner", 
         "edit_item" => "Edit Banner", 
         "all_items" => "All Banners", 
         "singular_name" => "Banner"
      ), 
      "menu_icon" => "dashicons-align-wide"
   )
   ); 
   //Home Page Cards
   register_post_type("videos", array(
      "supports" => array("title", 'editor'), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Video", 
         "add_new_item" => "Add New Video", 
         "edit_item" => "Edit Video", 
         "all_items" => "All Videos", 
         "singular_name" => "Video"
      ), 
      "menu_icon" => "dashicons-video-alt3"
   )
   ); 
    //Home Page Cards
    register_post_type("homepage-cards", array(
      "supports" => array("title", 'thumbnail'), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Home Page Cards", 
         "add_new_item" => "Add New Home Page Card", 
         "edit_item" => "Edit Home Page Card", 
         "all_items" => "All Home Page Cards", 
         "singular_name" => "Home Page Card"
      ), 
      "menu_icon" => "dashicons-visibility"
   )
   ); 
    //Brand Logo
    register_post_type("brand-logo", array(
      "supports" => array("title", 'thumbnail'), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Brand Logos", 
         "add_new_item" => "Add New Brand Logo", 
         "edit_item" => "Edit Brand Logo", 
         "all_items" => "All Brand Logos", 
         "singular_name" => "Brand Logo"
      ), 
      "menu_icon" => "dashicons-images-alt2"
   )
   ); 

   //sliders psot type
   register_post_type("sliders", array(
      "supports" => array("title"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Sliders", 
         "add_new_item" => "Add New Slider", 
         "edit_item" => "Edit Slider", 
         "all_items" => "All Sliders", 
         "singular_name" => "Slider"
      ), 
      "menu_icon" => "dashicons-slides"
   )
   ); 

   //loving post type
   register_post_type("loving", array(
      "supports" => array("title", "page-attributes", 'editor'), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Lovings", 
         "add_new_item" => "Add New Loving", 
         "edit_item" => "Edit Loving", 
         "all_items" => "All Lovings", 
         "singular_name" => "Loving"
      ), 
      "menu_icon" => "dashicons-welcome-widgets-menus",
      'taxonomies'          => array('category')
   )
   );

   //blogs post type
   register_post_type("blogs", array(
      'show_in_rest' => true,
      "supports" => array("title", "page-attributes", 'editor'), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Blogs", 
         "add_new_item" => "Add New Blog", 
         "edit_item" => "Edit Blog", 
         "all_items" => "All Blogs", 
         "singular_name" => "Blog"
      ), 
      "menu_icon" => "dashicons-welcome-write-blog"
   )
   );

   //loving post type
   register_post_type("shop-my-fav", array(
      "supports" => array("title", "page-attributes"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Shop My Favs", 
         "add_new_item" => "Add New Shop My Fav", 
         "edit_item" => "Edit Shop My Fav", 
         "all_items" => "All Shop My Favs", 
         "singular_name" => "Shop My Fav"
      ), 
      "menu_icon" => "dashicons-welcome-write-blog"
   )
   );
   
   //shop by brand page post type
   register_post_type("shop_by_brand", array(
      "supports" => array("title", "page-attributes"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Brands", 
         "add_new_item" => "Add New Brand", 
         "edit_item" => "Edit Brand", 
         "all_items" => "All Brands", 
         "singular_name" => "Brand"
      ), 
      "menu_icon" => "dashicons-shield"
   )
   );

      // typrewriter effect 

   register_post_type("typewriter_effect", array(
      "supports" => array("title"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
	   "show_in_rest"=> true, 
      "labels" => array(
         "name" => "Typewriter Effect", 
         "add_new_item" => "Add New Typewriter Effect", 
         "edit_item" => "Edit Typewriter Effect", 
         "all_items" => "All Typewriter Effect", 
         "singular_name" => "Typewriter Effect"
      ), 
      "menu_icon" => "dashicons-welcome-write-blog"
   )
   );
   // tri photos 

register_post_type("tri-images", array(
   "supports" => array("title", "editor"), 
   "public" => true, 
   "show_ui" => true, 
   "hierarchical" => true,
   "labels" => array(
      "name" => "Tri Images ", 
      "add_new_item" => "Add New Tri Images", 
      "edit_item" => "Edit Tri Images", 
      "all_items" => "All Tri Images", 
      "singular_name" => "Tri Images"
   ), 
   "menu_icon" => "dashicons-images-alt"
)
);

//projects
register_post_type("projects", array(
   "supports" => array("title"), 
   "public" => true, 
   "show_ui" => true, 
   "hierarchical" => true,
   "show_in_rest"=> true, 
   "labels" => array(
      "name" => "Project", 
      "add_new_item" => "Add New Project", 
      "edit_item" => "Edit Project", 
      "all_items" => "All Projects", 
      "singular_name" => "Project"
   ), 
   "menu_icon" => "dashicons-text"
)
); 

//projects
register_post_type("trade-professionals", array(
   "supports" => array("title", "editor", "author"), 
   "public" => true, 
   "show_ui" => true, 
   "hierarchical" => true,
   "show_in_rest"=> true, 
   "labels" => array(
      "name" => "Trade Professional", 
      "add_new_item" => "Add New Trade Professional", 
      "edit_item" => "Edit Trade Professional", 
      "all_items" => "All Trade Professionals", 
      "singular_name" => "Trade Professional"
   ), 
   "menu_icon" => "dashicons-store"
)
); 

   //select options
   register_post_type("select-options", array(
      "supports" => array("title"), 
      "public" => true, 
      "show_ui" => true, 
      "show_in_rest"=>true, 
      "hierarchical" => true,
      "labels" => array(
         "name" => "Select Option", 
         "add_new_item" => "Add New Select Option", 
         "edit_item" => "Edit Select Option", 
         "all_items" => "All Select Options", 
         "singular_name" => "Select Option"
      ), 
      "menu_icon" => "dashicons-table-row-after"
   )
   ); 


   //Customer Services
   register_post_type("customer-service", array(
      "supports" => array("title", "editor"), 
      "public" => true, 
      "show_ui" => true, 
      "hierarchical" => true,
      "show_in_rest"=> true, 
      'has_archive'=> true, 
      "labels" => array(
         "name" => "Customer Service", 
         "add_new_item" => "Add New Customer Service Post", 
         "edit_item" => "Edit Customer Service Post", 
         "all_items" => "All Customer Service Posts", 
         "singular_name" => "Customer Service Post"
   ), 
      "menu_icon" => "dashicons-editor-alignleft"
   )
   ); 
  
}

add_action("init", "register_custom_type2"); 

//Banner taxonomy
function wpdocs_register_private_taxonomy() {

   // banner taxonomy 
   $argsBanner = array(
      'label'        => __( 'Banner Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'hierarchical' => true,
      'show_in_rest' => true
  );
   
  register_taxonomy( 'banners_categories', 'banners', $argsBanner );

   // project taxonomy 
   $argsProject = array(
      'label'        => __( 'Project Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'hierarchical' => true,
      'show_in_rest' => true
  );
   
  register_taxonomy( 'project_categories', 'projects', $argsProject );

 // trade professional taxonomy 
 $ardsTradeProfessional = array(
   'label'        => __( 'Trade Categories', 'textdomain' ),
   'public'       => true,
   'rewrite'      => true,
   'hierarchical' => true,
   'show_in_rest' => true
   );

   register_taxonomy( 'trade_professional_categories', 'trade-professionals', $ardsTradeProfessional );

 // trade professional taxonomy  Tag
 $ardsTradeProfessional = array(
   'label'        => __( 'Trade Regions', 'textdomain' ),
   'public'       => true,
   'rewrite'      => true,
   'show_in_rest' => true
   );

   register_taxonomy( 'trade_professional_tags', 'trade-professionals', $ardsTradeProfessional );

      // covid taxonomy 
      $argsModal = array(
         'label'        => __( 'Modal Categories', 'textdomain' ),
         'public'       => true,
         'rewrite'      => true,
         'hierarchical' => true
   );
   
  register_taxonomy( 'modal-categories', 'modal', $argsModal );

   $args = array(
       'label'        => __( 'favorite', 'textdomain' ),
       'public'       => true,
       'rewrite'      => true,
       'hierarchical' => true
   );
    
   register_taxonomy( 'favorite', 'shop-my-fav', $args );

   $argsBlog = array(
      'label'        => __( 'Blog Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'hierarchical' => true,
      'show_in_rest' => true
  );
   
  register_taxonomy( 'blog-category', 'blogs', $argsBlog );

   //   taxonomy for Typewriter effect
   $argsTypewriter = array(
      'label'        => __( 'Typewriter Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'hierarchical' => true,
      'show_in_rest' => true
   );
   register_taxonomy( 'typewriter-category', 'typewriter_effect', $argsTypewriter );


   //   taxonomy for sliders
   $argsSliders = array(
      'label'        => __( 'Slider Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'show_in_rest' => true,
      'hierarchical' => true,
   );
   register_taxonomy( 'slider-category', 'sliders', $argsSliders );

   //   taxonomy for tri images
   $argsTriImages = array(
      'label'        => __( 'Tri Images Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'show_in_rest' => true,
      'hierarchical' => true,
   );
   register_taxonomy( 'tri-Images-category', 'tri-images', $argsTriImages );

   //   taxonomy for home page cards
   $argsHomePageCards = array(
      'label'        => __( 'Home Page Card Categories', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'show_in_rest' => true,
      'hierarchical' => true,
   );
   register_taxonomy( 'home-page-card-category', 'homepage-cards', $argsHomePageCards );

   //   taxonomy for Videos
   $argsVideos = array(
      'label'        => __( 'Video Category', 'textdomain' ),
      'public'       => true,
      'rewrite'      => true,
      'show_in_rest' => true,
      'hierarchical' => true,
   );
   register_taxonomy( 'video-category', 'videos', $argsVideos );



}
add_action( 'init', 'wpdocs_register_private_taxonomy', 0 );