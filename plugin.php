<?php
/*
Plugin Name: Izweb Gravity Slider Fields
Plugin URI: https://github.com/nhiha60591/izweb-gravity-slider-fields/
Description: Add Gravity Slider Fields
Version: 1.0.0
Author: Izweb Team
Author URI: https://github.com/nhiha60591
Text Domain: izweb-gravity-slider
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Check class exists
if ( ! class_exists( 'Izweb_Gravity_Slider' ) ) :

    // Create Class Izweb_Gravity_Slider
    class Izweb_Gravity_Slider{

        // Construct for class
        function __construct(){

            // Add button to GF custom field
            add_filter('gform_add_field_buttons', array( $this, 'add_slider_fields' ) );

            // Adds title to GF custom field
            add_filter( 'gform_field_type_title' , array( $this, 'slider_title_field' ) );

            // Adds the input area to the external side
            add_action( 'gform_field_input' , array( $this, 'slider_input' ), 10, 5 );

            // Execute some javascript technicalitites for the field to load correctly
            add_action( 'gform_editor_js', array( $this, 'slider_gform_editor_js' ) );
        }

        /**
         * Add Fields Slider To Gravity Form
         *
         * @param $field_groups
         * @return mixed
         */
        function add_slider_fields($field_groups){
            foreach($field_groups as &$group){
                if($group["name"] == "advanced_fields"){
                    $group["fields"][] = array(
                        "class"=>"button",
                        "value" => __("Slider", "gravityforms"),
                        "onclick" => "StartAddField('slider');"
                    );
                    break;
                }
            }
            return $field_groups;
        }
        function slider_title_field($type){
            if ( $type == 'slider' ) {
                return __('Slider', 'gravityforms');
            }
        }
        function slider_input( $input, $field, $value, $lead_id, $form_id ){
            if ( $field["type"] == "slider" ) {
                ob_start();
                ?>
                <div class="ginput_container">

                </div>
                <?php
                return ob_get_clean();
            }
            return $input;
        }
    }
    new Izweb_Gravity_Slider();
endif;