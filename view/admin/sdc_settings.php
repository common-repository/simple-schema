<div class="i_sdc_settings_div">

<?php
    echo '<link type="text/css" href="'.SDC_PLUGIN_URL.'resources/style/admin_style.css" rel="stylesheet"/>';
    echo '<script type="text/javascript" src="'.SDC_PLUGIN_URL.'resources/js/admin_js.js" > </script>';

    $sdc_settings = get_option('sdc_settings',true);
    $sdc_defaults = get_option('sdc_defaults',true);
    $sdc_default_schema = get_option('sdc_default_schema',true);
    $sdc_default_schema_content = get_option('sdc_default_schema_content',true);

    $home_url = home_url(); //i_print($sdc_uploads);
?>

<h1> <?php _e( 'Simple Schema', 'sdc_plugin' ); ?> </h1>
    <div class="sdc_italic_h i_row">
        <div class="col-md-1" >
            <img src="<?php echo SDC_PLUGIN_URL;?>/images/simple-schema-icon-64x64.jpg" class="">
        </div>
        <div class="col-md-11 sdc_settings_description" >
            Simple Schema is the most complete Semantic HTML Plugin available for WordPress.
            Search Engines use semantic markup to rank and display your content appropriately.
            Our plugin includes: Person, Product, Event, Organization, Movie, Book, and Review.
            Assign schemas per page or post; select where they display (Before Content, After Content, Hidden); even Preview them before saving.
            Simple Schema makes Semantic SEO as easy as selecting a few simple options and filling-in-the-blanks.
        </div>
    </div>
    <?php
    if ( defined('SDC_PRO_PLUGIN_DIR') ) {
        include_once SDC_PRO_PLUGIN_DIR.'view/admin/sdc_license.php';
    } else {
        ?>
        <div id="get_pro_btn" class="wrap i_sdc_wrap">
            <a class="get_pro_btn" href="<?php echo SDC_PLUGIN_SITE_URL; ?>" target="_blank">Get PRO Version</a>
        </div>
    <?php
    }
    ?>
    <form method="post" action="">
        <div class="wrap i_sdc_wrap">
            <div class="postbox i_sdc_div" id="sdc_area_box">

                <h3 class="mt_0"> <?php esc_attr_e( 'Simple Schema - Default Schema:', 'sdc_plugin' ); ?> </h3>
                <?php
                include_once SDC_PLUGIN_DIR.'view/metabox/sdc_metas.php';
                /*

                <div class="sdc_inp_div">
                    <label for="sdc_def_default_schema_type"> <?php _e( 'Default Schema type:', 'sdc_plugin' ); ?> </label>
                    <select name="sdc_default_schema[default_schema_type]" id="sdc_def_default_schema_type" class="i_sdc_inp sdc_type_changer i_sdc_meta_field">
                        <?php
                        foreach ($schema_types as $schema_type) {
                            $attrs = '';
                            if ($itemtype = $schema_type['itemtype'])
                                $attrs.= ' data-itemtype="' . $itemtype . '"';
                            $i_selected = '';
                            if ( isset($sdc_default_schema['default_schema_type']) && $schema_type['id'] == $sdc_default_schema['default_schema_type'] )
                                $i_selected = 'selected';
                            echo '<option value="' . $schema_type['id'] . '" id="type_' . $schema_type['id'] . '" ' . $attrs . ' ' . $i_selected . ' > ' . $schema_type['title'] . ' </option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="sdc_inp_div">
                    <label for="sdc_def_copyright_holder_name"> <?php _e( 'Copyright holder name:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_default_schema[copyright_holder_name]" value="<?php echo ( $sdc_default_schema['copyright_holder_name'] ) ? $sdc_default_schema['copyright_holder_name'] : ''; ?>" id="sdc_def_copyright_holder_name" class="regular-text i_sdc_inp" />
                </div>

                <div class="sdc_inp_div">
                    <label for="sdc_def_copyright_holder_url"> <?php _e( 'Copyright holder url:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_default_schema[copyright_holder_url]" value="<?php echo ( $sdc_default_schema['copyright_holder_url'] ) ? $sdc_default_schema['copyright_holder_url'] : ''; ?>" id="sdc_def_copyright_holder_url" class="regular-text i_sdc_inp" />
                </div>

                <div class="sdc_inp_div">
                    <label for="sdc_def_author"> <?php _e( 'Author:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_default_schema[author]" value="<?php echo ( $sdc_default_schema['author'] ) ? $sdc_default_schema['author'] : ''; ?>" id="sdc_def_author" class="regular-text i_sdc_inp" />
                </div>
                <div class="sdc_inp_div">
                    <label for="sdc_def_publisher"> <?php _e( 'Publisher:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_default_schema[publisher]" value="<?php echo ( $sdc_default_schema['publisher'] ) ? $sdc_default_schema['publisher'] : ''; ?>" id="sdc_def_publisher" class="regular-text i_sdc_inp" />
                </div>

                <h3 class="i_hidden"> <?php esc_attr_e( 'Simple Schema - Settings:', 'sdc_plugin' ); ?> </h3>
                */ ?>


                <input type="hidden" name="sdc_action" value="update_simple_schema_settings" />
                <?php submit_button('Save', 'primary', 'sdc_submit', false); ?>
                <div class="i_ajax_msg">
                </div>
                <?php
                wp_nonce_field( SDC_PROTECTION_H, 'sdc_class_nonce' );
                ?>
            </div>
        </div>
    </form>


    <form method="post" action="">
        <div class="wrap i_sdc_wrap">
            <div class="postbox i_sdc_div">

                <h3 class="mt_0"> <?php esc_attr_e( 'Simple Schema - Default values:', 'sdc_plugin' ); ?> </h3>

                <div class="sdc_inp_div">
                    <label for="sdc_copyright_holder_name"> <?php _e( 'Copyright holder name:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_defaults[copyright_holder_name]" value="<?php echo ( $sdc_defaults['copyright_holder_name'] ) ? $sdc_defaults['copyright_holder_name'] : ''; ?>" id="sdc_copyright_holder_name" class="regular-text i_sdc_inp" />
                </div>

                <div class="sdc_inp_div">
                    <label for="sdc_copyright_holder_url"> <?php _e( 'Copyright holder url:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_defaults[copyright_holder_url]" value="<?php echo ( $sdc_defaults['copyright_holder_url'] ) ? $sdc_defaults['copyright_holder_url'] : ''; ?>" id="sdc_copyright_holder_url" class="regular-text i_sdc_inp" />
                </div>
                <!--<label for="schema_some_setting"> <?php /*_e( 'Copyright holder name:', 'sdc_plugin' ); */?> </label>
            <input type="text" name="sdc_settings[copyright_holder_name]" value="<?php /*echo ( $sdc_uploads['copyright_holder_name'] ) ? $sdc_uploads['copyright_holder_name'] : ''; */?>" id="schema_some_setting" class="regular-text i_sdc_inp" />-->

                <div class="sdc_inp_div">
                    <label for="sdc_author"> <?php _e( 'Author:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_defaults[author]" value="<?php echo ( $sdc_defaults['author'] ) ? $sdc_defaults['author'] : ''; ?>" id="sdc_author" class="regular-text i_sdc_inp" />
                </div>
                <div class="sdc_inp_div">
                    <label for="sdc_publisher"> <?php _e( 'Publisher:', 'sdc_plugin' ); ?> </label>
                    <input type="text" name="sdc_defaults[publisher]" value="<?php echo ( $sdc_defaults['publisher'] ) ? $sdc_defaults['publisher'] : ''; ?>" id="sdc_publisher" class="regular-text i_sdc_inp" />
                </div>



                <h3 class="i_hidden"> <?php esc_attr_e( 'Simple Schema - Settings:', 'sdc_plugin' ); ?> </h3>


                <input type="hidden" name="sdc_action" value="update_simple_schema_settings" />
                <?php submit_button('Save', 'primary', 'sdc_submit', false); ?>
                <div class="i_ajax_msg">
                </div>
                <?php
                wp_nonce_field( SDC_PROTECTION_H, 'sdc_class_nonce' );
                ?>

                <div class="sdc_copyright_div">
                    COPYRIGHT © 2017 · <a href="https://simpleschema.com/" target="_blank">SIMPLE SCHEMA</a>
                </div>
            </div>
        </div>
    </form>

</div>