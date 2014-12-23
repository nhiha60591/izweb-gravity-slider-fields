<?php
/*
Plugin Name: Cigar Sense GF Sliders
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

            // Add a script to the display of the particular form only if tos field is being used
            add_action( 'init' , array( $this, 'gform_enqueue_scripts' ) );

            // Add Choices Default Value
            add_action( 'gform_editor_js_set_default_values', array( $this, 'set_choice_default_value') );
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

        /**
         * Change title field
         *
         * @param $type
         * @return string|void
         */
        function slider_title_field($type){
            if ( $type == 'slider' ) {
                return __('Slider', 'gravityforms');
            }
        }

        /**
         * Add Input Container
         *
         * @param $input
         * @param $field
         * @param $value
         * @param $lead_id
         * @param $form_id
         * @return string
         */
        function slider_input( $input, $field, $value, $lead_id, $form_id ){
            if ( $field["type"] == "slider" ) {
                ob_start();
                $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
                $current_label = '';
                ?>
                <div id="<?php echo 'slider-'.$field['id']; ?>" class="ginput_container">
                    <?php if( sizeof( $field['choices'] ) > 0 ): ?>
                        <input type="hidden" name="input_<?php echo $field['id']; ?>" id="<?php echo 'slider-input-'.$field['id']; ?>" value="<?php echo $value ?>">
                        <select style="display: none;" class="gform_slider <?php echo $field["type"]." {$css}"; ?>">
                        <?php foreach( $field['choices'] as $row): ?>
                            <?php if( $value == $row['value'] ) $current_label = $row['text']; ?>
                            <option value="<?php echo $row['value']; ?>" <?php selected( $value,$row['value']); ?>><?php echo $row['text']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                    <script type="text/javascript">
                        jQuery(document).ready( function( $ ) {
                            var select = $( "#<?php echo 'slider-'.$field['id']; ?> select" );
                            var max = select.length;
                            var amount = $( '#<?php echo 'slider-'.$field['id']; ?> input[name="input_<?php echo $field['id']; ?>"]');
                            var value_sl = $( "#<?php echo 'slider-'.$field['id']; ?> .field-value label" );
                            var slider = $( "<div id='slider_<?php echo $field['id']; ?>'></div>" ).insertAfter( select ).slider({
                                min: 1,
                                max: <?php echo sizeof( $field['choices']); ?>,
                                range: "min",
                                value: select[ 0 ].selectedIndex + 1,
                                slide: function( event, ui ) {
                                    select[ 0 ].selectedIndex = ui.value - 1;
                                    value_sl.html( select.find('option:selected').text() );
                                    amount.val( select.find('option:selected').val() );
                                }
                            });
                        });
                    </script>
                    <div class="field-value">
                        <label><?php echo $current_label; ?></label>
                    </div>
                </div>
                <?php
                return ob_get_clean();
            }
            return $input;
        }

        /**
         * Add Slider Form Editor Js
         */
        function slider_gform_editor_js(){
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function ($) {
                    //Add all textarea settings to the "TOS" field plus custom "tos_setting"
                    // fieldSettings["tos"] = fieldSettings["textarea"] + ", .tos_setting"; // this will show all fields that Paragraph Text field shows plus my custom setting
                    // from forms.js; can add custom "tos_setting" as well
                    fieldSettings["slider"] = ".label_setting,.rules_setting,.form_field_required, .description_setting, .choices_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting, .slider_setting"; //this will show all the fields of the Paragraph Text field minus a couple that I didn’t want to appear.
                    //binding to the load field settings event to initialize the checkbox
                    $(document).bind("gform_load_field_settings", function (event, field, form) {
                        jQuery("#field_slider").attr("checked", field["field_slider"] == true);
                        $("#field_slider_value").val(field["slider"]);
                    });
                });
            </script>
            <?php
        }

        /**
         * Enqueue Script
         */
        function gform_enqueue_scripts( ){
            wp_enqueue_style( 'gform_izw_slider_style', '//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css' );
            /*wp_register_script( 'gform_izw_slider_script', plugin_dir_url( __FILE__ )."assets/js/izweb-slider.js", array( 'jquery', 'jquery-ui-slider' ) );*/
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-slider' );
        }

        /**
         * Set choice default value
         */
        function set_choice_default_value(){
            ?>
            case "slider" :
            field.enableChoiceValue = true;
            if(!field.choices){
                field.choices = new Array(
                                    new Choice("<?php _e("Hate It", "gravityforms"); ?>", "-3", "0.00"),
                                    new Choice("<?php _e("Dislike Somewhat", "gravityforms"); ?>", "-1", "0.00"),
                                    new Choice("<?php _e("Ambivalent", "gravityforms"); ?>", "0", "0.00"),
                                    new Choice("<?php _e("Like Somewhat", "gravityforms"); ?>", "1", "0.00"),
                                    new Choice("<?php _e("Love It ", "gravityforms"); ?>", "3", "0.00")
                                );
            }
            break;
            <?php
        }

    }
    new Izweb_Gravity_Slider();
endif;