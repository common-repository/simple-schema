<?php
global $post;

$sdc_data_content_prename = SDC_DATA_CONTENT;
$sdc_data_name_prename = SDC_DATA_NAME;

$def_type_label_txt = '';

if( $_GET['page'] == 'simple_schema_settings' ){
    $get_cpt_args = array(
        'public'   => true
    );
    $post_types = get_post_types( $get_cpt_args, 'object' );

    $sdc_data_name_prename = 'sdc_default_schema';
    $sdc_data_content_prename = 'sdc_default_schema_content';

    $sdc_data = $sdc_default_schema;
    //$sdc_default_schema = get_option('sdc_default_schema',true);
    $sdc_data_content = $sdc_default_schema_content;
} else {
    $post_id = $post->ID;
    $sdc_data = get_post_meta($post_id, $sdc_data_name_prename, true);
    //echo SDC::sdc_generator( $post_id );
    $sdc_data_content = get_post_meta($post_id, $sdc_data_content_prename, true);

    if( $schema_types[ $sdc_default_schema['type'] ] ){
        $def_type_label_txt = 'Default: '.$schema_types[ $sdc_default_schema['type'] ]['title'].' - ';
    }
}



$schema_type_structure = array();
foreach ($schema_types as $schema_type) {
    $schema_type_structure[$schema_type['id']] = $schema_type['fields'];
}
?>
<script type="text/javascript">
    var schema_types = jQuery.parseJSON('<?php echo json_encode($schema_type_structure); ?>');
    var schema_types_add = jQuery.parseJSON('<?php echo json_encode($schema_types_add); ?>');
    var schema_options = jQuery.parseJSON('<?php echo json_encode($schema_options); ?>');
</script>
<?php
echo '<link type="text/css" href="' . SDC_PLUGIN_URL . 'resources/style/admin_style.css" rel="stylesheet"/>';
?>
<div class="wp-list-table i_list_types i_metabox_sdc_list">
    <?php
    if ( defined('SDC_PRO_PLUGIN_DIR') ) {
        if( !SDC_PRO::is_active() ){
            require_once( SDC_PRO_PLUGIN_DIR . 'view/admin/schema-types-activate-notice.php' );
        }
    }
    ?>
    <input type="hidden" id="i_post_link" value="<?php echo get_permalink(); ?>" >

    <div id="position_type_score">
        <?php
        if( $_GET['page'] == 'simple_schema_settings' && false ){
            ?>
        <div class="i_row clearfix i_sdc_post_type">
        <?php
        //i_print($post_types);
        if( $post_types ){
            ?>
            <label for="sdc_post_type_changer"><b><?php _e('Post Types', SDC_NAME); ?>:</b></label>
            <select name="<?php echo $sdc_data_name_prename; ?>[post_type]" id="sdc_post_type_changer" class="sdc_input sdc_post_type_changer i_sdc_meta_field">
             <?php
             foreach( $post_types as $post_type_key => $post_type_val ) {
                echo '<option value="'.$post_type_key.'"> '.$post_type_val->label.' </option>';
            }
            ?>
            </select>
        <?php
        }
        ?>
        </div>
        <?php
        }
        ?>

        <div class="i_row clearfix i_sdc_type">
            <label for="sdc_type_changer"><b><?php _e('Type', SDC_NAME); ?>:</b></label>
            <select name="<?php echo $sdc_data_name_prename; ?>[type]" id="sdc_type_changer" class="sdc_input sdc_type_changer i_sdc_meta_field">
                <option value="null" id="type_null"> <?php echo $def_type_label_txt.'('.__('Select A Type', SDC_NAME).')'; ?> </option>
<?php
foreach ($schema_types as $schema_type) {
    $attrs = '';
    if ($itemtype = $schema_type['itemtype'])
        $attrs.= ' data-itemtype="' . $itemtype . '"';
    $i_selected = '';
    if (isset($sdc_data['type']) && $schema_type['id'] == $sdc_data['type'])
        $i_selected = 'selected';
    echo '<option value="' . $schema_type['id'] . '" id="type_' . $schema_type['id'] . '" ' . $attrs . ' ' . $i_selected . ' > ' . $schema_type['title'] . ' </option>';
}
?>
            </select>
        </div>
        <!--<hr>-->
        <div class="i_row clearfix i_sdc_position">
            <label for="sdc_position"><?php _e('Front-end Position', SDC_NAME); ?>:</label>
            <?php $sdc_position = ( isset($sdc_data_content['style']) && $sdc_data_content['style']['position'] ) ? $sdc_data_content['style']['position'] : 'hidden'; ?>
            <select name="<?php echo $sdc_data_content_prename; ?>[style][position]" id="sdc_position" class="sdc_input">
                <option value="before" id="sdc_position_before" <?php echo ($sdc_position == 'before') ? 'selected' : ''; ?> > <?php _e('Before Content', SDC_NAME); ?> </option>
                <option value="after" id="sdc_position_after" <?php echo ($sdc_position == 'after') ? 'selected' : ''; ?> > <?php _e('After Content', SDC_NAME); ?> </option>
                <option value="hidden" id="sdc_position_hidden" <?php echo ($sdc_position == 'hidden') ? 'selected' : ''; ?> > <?php _e('Hidden', SDC_NAME); ?> </option>
            </select>
        </div>
        <!--<hr>-->
        <?php
        if( $_GET['page'] != 'simple_schema_settings' ){
        ?>
        <div class="i_row clearfix i_sdc_og_data">
            <label for="sdc_og_data" id="label_sdc_og_data"><?php _e('Open Graph metadata', SDC_NAME); ?>:</label>
            <div class="i_sdc_og_field_div clearfix">
                <?php
                $sdc_og_title = (get_the_title($post->ID) !== NULL ? get_the_title($post->ID) : $sdc_data['og_title'] );
                $field = array(
                    'title'       => __( 'OG Title', SDC_NAME ),
                    'subtitle'    => '',
                    'type'        => 'text',
                    'id'          => 'og_title',
                    'itemtype'    => '',
                    'itemprop'    => '',
                    'description' => '',
                );
                echo self::sdc_field_generator($field, $sdc_og_title, '', $sdc_data);
                ?>
            </div>
            <div class="i_sdc_og_field_div clearfix">
                <?php
                $sdc_og_description = (get_the_excerpt($post->ID) !== NULL ? get_the_excerpt($post->ID) : $sdc_data['og_description'] );
                $field = array(
                    'title'       => __( 'OG Description', SDC_NAME ),
                    'subtitle'    => '',
                    'type'        => 'textarea',
                    'id'          => 'og_description',
                    'itemtype'    => '',
                    'itemprop'    => '',
                    'description' => '',
                );
                echo self::sdc_field_generator($field, $sdc_og_description, '', $sdc_data);
                ?>
            </div>
            <div class="i_sdc_og_field_div clearfix">
                <?php
                //$sdc_og_metadata_image = ( isset($sdc_data_content['og_metadata']) && $sdc_data_content['og_metadata']['image'] ) ? $sdc_data_content['og_metadata']['image'] : '';
                // $sdc_og_image_url = ( isset($sdc_data['og_image_url']) ) ? $sdc_data['og_image_url'] : '';
                $sdc_og_image_url =  (has_post_thumbnail($post->ID) ? get_the_post_thumbnail_url($post->ID) : '' );
                $field = array(
                    'title'       => __( 'OG Image URL', SDC_NAME ),
                    'subtitle'    => '',
                    'type'        => 'image_url',
                    'id'          => 'og_image_url',
                    'itemtype'    => '',
                    'itemprop'    => 'url',
                    'description' => '',
                );
                echo self::sdc_field_generator($field, $sdc_og_image_url, '', $sdc_data);
                ?>
            </div>
        </div><?php
        }
        ?>
        <!--<hr>-->
        <div class="i_sdc_score" style="display: none;">
            <label> <?php _e('Score', SDC_NAME); ?></label>
            <ul class="sdc_score_4">
                <li> <span></span> </li>
                <li> <span></span> </li>
                <li> <span></span> </li>
                <li> <span></span> </li>
                <li> <span></span> </li>
            </ul>
        </div>
    </div><hr>
    <div id="simple_schema_preview_and_imput">
        <div class="simple_schema_imputs_div">
    <?php
    foreach ($schema_options as $schema_option) {
        $schema_id = $schema_option['id'];
        $class_types = '';
        foreach ($schema_types as $schema_type) {
            if (( $t_key = array_searchRecursive($schema_id, $schema_type['fields']) ) !== FALSE) {
                $class_types.= ' for_type_' . $schema_type['id'];
            }
        }
        $field = $schema_option;
        ?>
        <div class="i_row i_field_wrapper i_div_<?php echo $field['id']; ?> <?php echo $class_types; ?>">
            <?php
            $f_value = ( isset($sdc_data[$field['id']]) ) ? $sdc_data[$field['id']] : '';

            $attrs = '';
            if ($itemprop = $field['itemprop'])
                $attrs.= ' data-itemprop="' . $itemprop . '"';
            //if( $itemtype_url = $field['itemtype-url'] )$attrs.= ' data-itemtype-url="'.$itemtype_url.'"';
            $item_label = ( $field['label'] ) ? $field['label'] . ':' : '';
            $item_wrap_tag = ( $field['wrap_tag'] ) ? $field['wrap_tag'] : '';
            $attrs.= ' data-label="' . $item_label . '" data-wrap_tag="' . $item_wrap_tag . '"';
            if ($field['data-attrs'] && count($field['data-attrs']))
                foreach ($field['data-attrs'] as $data_attr => $data_attr_val) {
                    $attrs.= ' data-' . $data_attr . '="' . $data_attr_val . '" ';
                }

            echo self::sdc_field_generator($field, $f_value, $attrs, $sdc_data);
            ?>
        </div>
    <?php
}
?>
        </div>

    <div class="simple_schema_preview_div">
    <div class="i_sdc_meta_preview_div">
        <div class="front_end_preview">
        <h2> <?php _e( 'Front-end Preview', SDC_NAME); ?></h2>
        <div class="i_sdc_meta_preview">

        </div>
        </div>
        <div class="html_preview">
            <h2 class="i_html_preview"> <?php _e( 'HTML Preview',SDC_NAME ); ?></h2>
            <h2 class="i_json_preview" style="display: none;"> <?php _e( 'Json Preview', SDC_NAME ); ?></h2>
            <textarea class="i_sdc_html_preview" name="<?php echo $sdc_data_content_prename; ?>[content]" readonly></textarea>
            <textarea class="i_sdc_json_preview" name="<?php echo $sdc_data_content_prename; ?>[json]" readonly></textarea>
        </div>
    </div>
        </div>
    </div>
</div>

<?php ?>
