<?php

class SDC_Admin {

    private static $initiated = false;
    public static $schema_types;
    public static $schema_types_add;
    public static $schema_options;
	
	public function init( ) {
        self::$schema_types = SDC::$schema_types;
        self::$schema_types_add = SDC::$schema_types_add;
        self::$schema_options = SDC::$schema_options;
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks( ) {
		self::$initiated = true;

        add_action( 'load-post.php', array( 'SDC_Admin' , 'sdc_meta_boxes' ) );
        add_action( 'load-post-new.php', array( 'SDC_Admin' , 'sdc_meta_boxes' ) );

		add_action( 'admin_menu', array( 'SDC_Admin', 'init_menus' ), 10, 2 );
        //add_filter( 'media_buttons', array( 'SDC_Admin', 'media_button' ), 31 );
	}

    public function init_menus( ) {
        add_options_page( 'Simple Schema', 'Simple Schema', 'manage_options', 'simple_schema_settings', array( 'SDC_Admin' ,'i_settings'), 'dashicons-align-center', 1 );
            //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
            //add_menu_page( 'Simple Schema', 'Simple Schema', 'manage_options', 'simple_schema_settings', array( 'SDC_Admin' ,'i_settings'), 'dashicons-align-center', '80.08'  );
    }


    public function sdc_meta_boxes(){
        $current_screen = get_current_screen();
        if($current_screen->base == 'post'){
            $post_type = get_post_type_object($current_screen->post_type);
            if($post_type->public == true){
                add_action( 'add_meta_boxes', array( 'SDC_Admin' , 'add_sdc_meta_boxes' ) );
                add_action( 'save_post', array( 'SDC_Admin' , 'save_sdc_metas' ), 10, 2 );
            }
        }
    }

    public function add_sdc_meta_boxes(){
        add_meta_box(
            'sdc_area_box',			// Unique ID
            esc_html__( 'Simple Schema', '' ),		// Title
            array( 'SDC_Admin' , 'sdc_metas'),		// Callback function
            '',					// Admin page (or post type)
            'advanced',					// Context
            'high'					// Priority
        );
    }

    public function save_sdc_metas($post_id) {
        if (!wp_verify_nonce($_POST['sdc_post_class_nonce'], plugin_basename(__FILE__))) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        global $wpdb;
        $sdc_data_content = SDC_DATA_CONTENT;

        $sdc_data = $_POST[SDC_DATA_NAME];
        $sdc_post_content = $_POST[ $sdc_data_content ];

        update_post_meta($post_id, SDC_DATA_NAME, $sdc_data);
        update_post_meta($post_id, $sdc_data_content, $sdc_post_content);
    }

    public function sdc_metas(){
        wp_nonce_field( plugin_basename(__FILE__), 'sdc_post_class_nonce' );
        $schema_types = self::$schema_types;
        $schema_types_add = self::$schema_types_add;
        $schema_options = self::$schema_options;

        $sdc_default_schema = get_option('sdc_default_schema',true);
        $sdc_default_schema_content = get_option('sdc_default_schema_content',true);
        //i_print( $sdc_default_schema_content );
        wp_enqueue_style('jquery-ui-smoothness', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', false, null);

        wp_enqueue_script( 'jquery-ui-core');
        wp_enqueue_script( 'jquery-ui-datepicker');
        wp_enqueue_script( 'jquery-ui-slider');
        wp_enqueue_script( 'sdc-admin-js', SDC_PLUGIN_URL.'resources/js/admin_js.js' , array('jquery'), SDCVersion, true );
        //wp_enqueue_script( 'jquery-timepicker', plugins_url('/lib/js/jquery.timepicker.js', __FILE__) , array('jquery'), SC_VER, true );
        //wp_enqueue_script( 'format-currency', plugins_url('/lib/js/jquery.currency.min.js', __FILE__) , array('jquery'), SC_VER, true );

        wp_localize_script('sdc-admin-js', 'sdc_defaults', array(
                'sdc_default_schema_content' => $sdc_default_schema_content,
            )
        );

        include SDC_PLUGIN_DIR.'view/metabox/sdc_metas.php';
    }

    public function media_button() {

        // don't show on dashboard (QuickPress)
        $current_screen = get_current_screen();
        if ( is_object($current_screen) && 'dashboard' == $current_screen->base )
            return;

        // don't display button for users who don't have access
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
            return;

        // do a version check for the new 3.5 UI
        $version = get_bloginfo('version');

        if ($version < 3.5) {
            // show button for v 3.4 and below
            echo '<a href="#TB_inline?width=650&inlineId=schema_build_form" class="thickbox schema_clear schema_one" id="add_schema" title="' . __('Schema Creator Form', 'schema') . '">' .
                __('Structured Data Form', SDC_NAME ) .
                '</a>';
        } else {
            // display button matching new UI
            $img = '<span class="schema-media-icon"></span> ';
            echo '<a href="#TB_inline?width=650&inlineId=schema_build_form" class="thickbox schema_clear schema_two button" id="add_schema" title="' . esc_attr__( 'Add Schema', 'schema' ) . '">' .
                $img . __( 'Add Structured Data', SDC_NAME ) .
                '</a>';
        }

    }

    public function sdc_actions( ) {

        if ( isset( $_POST['sdc_action'] )) {

            if ( !wp_verify_nonce( $_POST['sdc_class_nonce'], SDC_PROTECTION_H ) ) {
                return false;
            }

            if( $_POST['sdc_action'] == 'update_simple_schema_settings' ) {
                if( $_POST['sdc_defaults'] ){
                    $sdc_defaults = $_POST['sdc_defaults'];
                    update_option( 'sdc_defaults', $sdc_defaults );
                }
                if( $_POST['sdc_settings'] ){
                    $sdc_settings = $_POST['sdc_settings'];
                    update_option( 'sdc_settings', $sdc_settings );
                }
                if( $_POST['sdc_default_schema'] ){
                    $sdc_default_schema = $_POST['sdc_default_schema'];
                    update_option( 'sdc_default_schema', $sdc_default_schema );
                }
                if( $_POST['sdc_default_schema_content'] ){
                    $sdc_default_schema_content = $_POST['sdc_default_schema_content'];
                    update_option( 'sdc_default_schema_content', $sdc_default_schema_content );
                }
            }

        }
    }

    public function i_settings( ){
        $schema_types = self::$schema_types;
        $schema_types_add = self::$schema_types_add;
        $schema_options = self::$schema_options;

        self::sdc_actions();
        wp_enqueue_media();

        wp_enqueue_style('jquery-ui-smoothness', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', false, null);

        wp_enqueue_script( 'jquery-ui-core');
        wp_enqueue_script( 'jquery-ui-datepicker');
        wp_enqueue_script( 'jquery-ui-slider');
        //wp_enqueue_script( 'sdc-admin-js', SDC_PLUGIN_URL.'resources/js/admin_js.js' , array('jquery'), SDCVersion, true );

        require_once ( SDC_PLUGIN_DIR . 'view/admin/sdc_settings.php' );
    }


    function sdc_option_name( $field, $key = false ){
        $op_name = SDC_DATA_NAME;
        if( $_GET['page'] == 'simple_schema_settings' ){
            $op_name = 'sdc_default_schema';
        }
        $name = $field['id'];

        if( isset($field['global_option']) && $field['global_option'] ){
            return $name;
        }

        if( $key ){
            return $op_name.'['.$name.']['.$key.']';
        }
        return $op_name.'['.$name.']';
    }
    /*
     *	Field generator
     */
    function sdc_field_generator( $field, $f_value, $attrs, $sdc_data ){
        switch ( $field['type'] ) {
            case "text";
                return self::create_section_for_text( $field, $f_value, $attrs );
                break;

            case "textarea":
                return self::create_section_for_textarea( $field, $f_value, $attrs );
                break;

            case "textarea_editor":
                self::create_section_for_textarea_editor( $field, $f_value, $attrs );
                break;

            case "checkbox":
                return self::create_section_for_checkbox( $field, $f_value, $attrs );
                break;

            case "radio":
                return self::create_section_for_radio( $field, $f_value, $attrs );
                break;

            case "selectbox":
                return self::create_section_for_selectbox( $field, $f_value, $attrs );
                break;

            case "number":
                return self::create_section_for_number( $field, $f_value, $attrs );
                break;

            case "url";
                return self::create_section_for_url( $field, $f_value, $attrs );
                break;

            case "date":
                return self::create_section_for_date( $field, $f_value, $attrs );
                break;

            case "image_url":
                return self::create_section_for_image_url( $field, $f_value, $attrs );
                break;

            case "intro_view":
                return self::create_section_for_intro_view( $field, $f_value, $attrs );
                break;

            case "header":
                return self::create_section_for_header( $field, $f_value, $attrs );
                break;

            case "fields_group":
                return self::create_section_for_fields_group( $field, $f_value, $attrs, $sdc_data );
                break;

            case "end_section":
                return self::create_section_for_end_section( $field, $f_value, $attrs );
                break;
        }
    }


    /*
     *	Field generators
     */

    function create_section_for_header( $field, $value = '', $attrs = '' ) {
        $html = '<h3 id="sdc_meta_'.$field['id'].'" data-sdc-generator="false">' . $field['title'] . '</h3>';
        if( $field['subtitle'] ){
            $html.= '<span class="subtitle">' . $field['subtitle'] . '</span>';
        }

        return $html;
    }

    function create_section_for_end_section( $field, $value = '', $attrs = '' ) {
        $html = '<hr>';
        return $html;
    }

    function create_section_for_text( $field, $value = '', $attrs = '' ) {
        if( !$value && $field['default'] )$value = $field['default'];
        //<div class="i_row">
        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        //$html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="text" name="'.self::sdc_option_name( $field ).'" value="'. $value . '" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_sdc_meta_field" >';
        if( $field['subtitle'] ){
            $html.= '<span class="subtitle">' . $field['subtitle'] . '</span>';
        }
        //$html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_url( $field, $value = '', $attrs = '' ) {
        if( !$value && $field['default'] )$value = $field['default'];
        //<div class="i_row">
        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        //$html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="text" name="'.self::sdc_option_name( $field ).'" value="'. $value . '" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_sdc_meta_field" >';
        if( $field['subtitle'] ){
            $html.= '<span class="subtitle">' . $field['subtitle'] . '</span>';
        }
        //$html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_number( $field, $value = '', $attrs = '' ) {
        if( !$value && $field['default'] )$value = $field['default'];

        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        //$html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="number" name="'.self::sdc_option_name( $field ).'" value="'. $value . '" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_sdc_meta_field" >';
        if( $field['subtitle'] ){
            $html.= '<span class="subtitle">' . $field['subtitle'] . '</span>';
        }
        //$html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_date( $field, $value = '', $attrs = '' ) {
        if( !$value && $field['default'] )$value = $field['default'];

        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        //$html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="text" name="'.self::sdc_option_name( $field ).'" value="'. $value . '" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_datepicker i_sdc_meta_field" >';
        //$html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_textarea( $field, $value = '', $attrs = '' ) {
        if( !$value && $field['default'] )$value = $field['default'];

        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        $html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<textarea type="text" name="'.self::sdc_option_name( $field ).'" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_sdc_meta_field" >'. $value . '</textarea>';
        $html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_textarea_editor( $field, $value = '', $attrs = '' ) {
        if( !$value && $field['default'] )$value = $field['default'];
        $html = '';
        echo '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        echo '<p class="subtitle">' . $field['subtitle'] . '</p>';
        wp_editor( $value, 'field_'.$field['id'],
            array(
                'textarea_rows' =>  12,
                'textarea_name' =>  self::sdc_option_name( $field ),
                //'media_buttons' => 1,
            )
        );
        /*echo '<textarea type="text" name="'.self::sdc_option_name( $field ).'" ' .
            ' id="field_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_texteditor" >'. $value . '</textarea>';*/
        echo '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_checkbox( $field, $value = '', $attrs = '' ) {
        //if( !$value && $field['default'] )$value = $field['default'];
        $checked = '';
        if( $value ) $checked='checked';
        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        $html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="checkbox" name="'.self::sdc_option_name( $field ).'" value="'.$field['value'].'" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" class="i_checkbox i_sdc_meta_field" '.$checked.' >';
        $html.= '<span class="description">' . $field['description'] . '</span>';

        return $html;
    }

    function create_section_for_radio( $field, $options ) {
        $html = '';
        return $html;
    }

    function create_section_for_selectbox( $field, $value = '', $attrs = '' ){
        if( !$value && $field['default'] )$value = $field['default'];
        $options = $field['options'];
        $html = '';
        $html.= '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        //$html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';


        $html.= '<select name="'.self::sdc_option_name( $field ).'" '. $attrs . ' id="sdc_meta_'.$field['id'].'" class="i_sdc_meta_field" >';
        //$html.= '<option value="null" > --- </option>';
        if( is_array( $options ) && count( $options ) )
        foreach ($options as $option => $option_name) {
            $i_selected ='';
            if( $option == $value ) $i_selected='selected';
            $html.= '<option value="'.$option.'" '.$i_selected.'  >' . $option_name . '</option>';
        }
        $html.= '</select>';

        //$html.= '<span class="description">' . $field['description'] . '</span>';

        return $html;
    }

    function create_section_for_image_url( $field, $value = '', $attrs = '' ) {
        $class = ''; if( trim($value) == '' )$class = 'i_hidden';
        $html = '<label for="sdc_meta_'.$field['id'].'">' . $field['title'] . '</label>';
        $html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="text" name="'.self::sdc_option_name( $field).'" value="'. $value . '" ' . $attrs .
            ' id="sdc_meta_'.$field['id'].'" placeholder="' . $field['placeholder'].'" class="i_input i_sdc_meta_field i_input_url upload_image_button" >';
        $html.= '<img src="'.$value.'" class="i_preview_img '.$class.'" id="i_preview_sdc_meta_'.$field['id'].'" >'; //.'_'.$field['id_key']
        $html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_intro_view( $field, $value = '', $attrs = '' ){
        $value = $field['value'];
        $html = '<label for="field_'.$field['id'].'">' . $field['title'] . '</label>';
        $html.= '<p class="subtitle">' . $field['subtitle'] . '</p>';
        $html.= '<input type="text" value="'.$value.'" ' . $attrs . ' id="field_'.$field['id'].'" class="i_inputi_sdc_meta_field i_click_checkall" readonly  >';
        $html.= '<p class="description">' . $field['description'] . '</p>';

        return $html;
    }

    function create_section_for_fields_group( $field, $value = '', $attrs = '', $sdc_data = array() ){
        $options = $field['options'];
        $value = $field['value'];
        $schema_options = self::$schema_options;

        $html = '';
        $html.= '<label for="sdc_meta_'.$field['id'].'" class="sdc_group_parent_label">' . $field['title'] . '</label>';
        $html.= '<div class="fields_group_div">';
        if( is_array( $options ) && count( $options ) )
            foreach ($options as $field_id) {
                $i_selected ='';
                $f_value = ( isset( $sdc_data[ $field_id ] ) ) ? $sdc_data[ $field_id ] : '';
                $field = $schema_options[ $field_id ];
                $html.= self::sdc_field_generator( $field, $f_value, $attrs, $sdc_data );
                //if( $option == $value ) $i_selected='selected';
                //$html.= '<option value="'.$option.'" '.$i_selected.'  >' . $option_name . '</option>';
            }
        $html.= '</div>';
        return $html;
    }

}