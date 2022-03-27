<?php

/*
  Plugin Name: Webduel Accordions
  Description: This plugin adds a new accordion block type
  Version: 1.0
  Author: Gurpreet Dhoat
  Author URI: https://webduel.co.nz
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WebAccordion {
  function __construct() {
    add_action('init', array($this, 'adminAssets'));
  }

  function adminAssets() {
    wp_register_style('accordionCSS', plugin_dir_url(__FILE__) . 'build/index.css');
    wp_register_script('ournewblocktype', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-element', 'wp-editor'));

    register_block_type('ourplugin/webduel-accordion', array(
      'editor_script'=> 'ournewblocktype', 
      'editor_style' => 'accordionCSS',
      'render_callback'=> array($this, 'theHTML')
    )); 
  }

  // output front end html 
  function theHTML($attributes){ 
    // adding frontend script and css here to make sure it only loads if needed on a page
    if(!is_admin()){ 
      wp_enqueue_script('attentionFrontend', plugin_dir_url(__FILE__).'build/frontend.js', array('wp-element')); 
      wp_enqueue_style('attentionFrontendStyles', plugin_dir_url(__FILE__).'build/frontend.css'  ); 
    }
    ob_start(); ?> 
        <!-- we will use the below div to add content using React render function -->
        <div class="webduel-accordion-update-me"><pre style="display: none" ><?php echo wp_json_encode($attributes);?></pre></div>
    <?php return ob_get_clean(); 
  }
}

$webduelAccordion = new WebAccordion();