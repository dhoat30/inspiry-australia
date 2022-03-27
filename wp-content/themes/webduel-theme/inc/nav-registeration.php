<?php 
 //add nav menu
 function inspiry_config(){ 
    register_nav_menus( 
       array(
         "my-account-nav-top" => "My Account Top Navbar",
           "top-navbar" => "Top Navbar (under logo)",
          "inspiry_main_menu" => "Inspiry Main Menu",
          "footer-services" => "Footer Services", 
          "footer-help-info" => "Footer Help & info", 
          "footer-store" => "Footer Store", 
          "customer-service-sidebar" => "Customer Service Sidebar"
       )
       );  

       add_theme_support( "title-tag");

         add_post_type_support( "gd_list", "thumbnail" );   
         
         add_theme_support( 'woocommerce' );
  }
 
  add_action("after_setup_theme", "inspiry_config", 0);
?>