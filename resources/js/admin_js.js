jQuery(document).ready( function( $ ) {
    var sdc_type = '';
    var type_id = '';
    var i_post_link = $('#i_post_link').val();
    var exclude_options = ['header', 'end_section'];
    if( typeof sdc_defaults !== 'undefined' && sdc_defaults.sdc_default_schema_content != '1' )
        sdc_defaults.sdc_default_schema_content.content = sdc_defaults.sdc_default_schema_content.content.replace(/\\/g, '');

    $('.sdc_type_changer').change( sdc_type_changer );
    function sdc_type_changer(){
        type_id = $(this).val(); sdc_type = $('option:selected', this).data('itemtype');
        $(this).parents('.i_list_types').find('.i_field_wrapper').hide(); $('.sdc_active_field').removeClass('sdc_active_field');
        $(this).parents('.i_list_types').find('.for_type_'+type_id).show().find('.i_sdc_meta_field').addClass('sdc_active_field');
        i_sdc_preview();
    }
    $('.sdc_type_changer').change();

    // i_sdc_meta_preview

    $('.i_sdc_meta_field').change( i_sdc_preview);
    function i_sdc_preview(){
        var sdc_preview = $('.i_sdc_meta_preview');
        var sdc_html_div = '<div itemscope itemtype="'+sdc_type+'" class="i_sdc_div"> \n';
        var sdc_html = '';
        var schema_fields = schema_types[type_id];
        var sdc_field = '', schema_field = ''; var i_itemprop = '';

        if ( sdc_type == 'undefined' || !sdc_type ) {
            var sdc_default_content = '';
            if( typeof sdc_defaults !== 'undefined' && sdc_defaults.sdc_default_schema_content != '1' ){
                sdc_default_content = sdc_defaults.sdc_default_schema_content.content;
            }
            sdc_preview.html( sdc_default_content );
            $('.i_sdc_html_preview').val( sdc_default_content ); return;
        }

        for(var index in schema_fields) {
            schema_field = schema_fields[index];
            if( Array.isArray( schema_field ) ){
                i_itemprop = index.replace(' ','_'); //console.log(index);
                sdc_html+= '<div itemprop="'+i_itemprop+'" itemscope itemtype="'+schema_types_add[i_itemprop]['itemtype']+'"> \n';
                for(var f in schema_field) {
                    if( exclude_options.indexOf( schema_options[ schema_field[f] ]['type'] ) >= 0 ) continue;
                    sdc_field = $( '#sdc_meta_'+schema_field[f] );
                    if( sdc_field.val() != '' ){
                        if ( sdc_field.is(':checkbox') && !sdc_field.is(':checked')) continue;
                        i_itemprop = sdc_field.data('itemprop');

                        console.log( schema_options[ schema_field[f] ] );
                        if( schema_options[ schema_field[f] ]['type'] == 'image_url' ){
                            console.log( 'f = '+f + 'schema_field[f] =' + schema_field[f] );
                            sdc_html+= '<img itemprop="'+i_itemprop+'" src="' + sdc_field.val() + '" class="schema_'+i_itemprop+'" > ';
                        } else if( schema_options[ schema_field[f] ]['links'] == '1' ){    //Available in
                            sdc_html+= '<link itemprop="'+i_itemprop+'" href="http://schema.org/' + sdc_field.val() + '" class="schema_'+i_itemprop+'" > ';
                            if( sdc_field.is('select') ){
                                sdc_html+= sdc_field.children('option:selected').text()+' \n';
                            } else {
                                sdc_html+= sdc_field.val()+' \n';
                            }
                        } else {
                            sdc_html += '<div class="schema_' + i_itemprop + '"> ' + sdc_field.data('label') + ' ';
                            if( sdc_field.data('wrap_tag') == 'meta' ){
                                sdc_html += '<meta itemprop="' + i_itemprop + '" content="' + sdc_field.val() + '" >';
                            } else {
                                sdc_html += '<span class="schema_' + i_itemprop + '_span" itemprop="' + i_itemprop + '" >' + sdc_field.val() + '</span>';
                            }

                            sdc_html += ' </div> \n';
                        }
                    }
                }
                sdc_html+= '</div> \n';
            } else {
                if( exclude_options.indexOf( schema_options[schema_field]['type'] ) >= 0 ) continue;
                sdc_field = $( '#sdc_meta_'+schema_field );

                if( sdc_field.val() != '' ){
                    if ( sdc_field.is(':checkbox') && !sdc_field.is(':checked')) continue;
                    i_itemprop = sdc_field.data('itemprop');

                    if( i_itemprop == 'sdc_type_changer' ){
                        sdc_html_div = '<div itemscope itemtype="http://schema.org/'+sdc_field.val()+'" class="i_sdc_div"> \n';
                        continue;
                    }
                    if( schema_options[schema_field]['type'] == 'image_url' ){
                        console.log( 'f = '+f + 'schema_field[f] =' + schema_field[f] );
                        sdc_html+= '<img itemprop="'+i_itemprop+'" src="' + sdc_field.val() + '" class="schema_'+i_itemprop+'" > ';
                    } else if( schema_field == 'name' && sdc_field.attr('id') == 'sdc_meta_name' ){
                        sdc_html+= '<a href="'+i_post_link+'" itemprop="url" class="schema_url">';
                        sdc_html+= '<div class="schema_'+i_itemprop+'" itemprop="'+i_itemprop+'"> '+sdc_field.data('label')+' ' + sdc_field.val() + '</div></a> \n';
                    } else {
                        if( schema_options[ schema_field ]['links'] == '1' ){
                            sdc_html+= '<link itemprop="'+i_itemprop+'" href="http://schema.org/' + sdc_field.val() + '" class="schema_'+i_itemprop+'" > '+sdc_field.children('option:selected').text()+' \n';
                        } else {
                            if( sdc_field.data('wrap_tag') == 'meta' ){
                                sdc_html += '<meta itemprop="' + i_itemprop + '" content="' + sdc_field.val() + '" >';
                            } else {
                                sdc_html += '<div class="schema_' + i_itemprop + '" itemprop="' + i_itemprop + '" >' + sdc_field.data('label') + ' ' + sdc_field.val() + '</div> \n';
                            }
                        }
                    }

                }
            }
        }

        /*$('.sdc_active_field').each( function( index, el){
         sdc_html+= '<p>'+ $(this).val() +'</p>';
         });*/
        sdc_html = sdc_html_div + sdc_html + '</div>';

        sdc_preview.html( sdc_html );
        $('.i_sdc_html_preview').val( sdc_html );
    }

    //

    if( $( ".i_datepicker" ).length )
        $( ".i_datepicker" ).datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'mm/dd/yy',
            //minDate: 0,
        });

    // Uploading files
    var file_frame; var thiss, thiss_id;
    $('.i_input_url').live('click', function( event ){ //i_image_uploader
        event.preventDefault();
        thiss_id = $(this).attr('id'); console.log( thiss_id );
        if ( file_frame ) { file_frame.open(); return; }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery( this ).data( 'uploader_title' ),
            library: {
                type: 'image'
            },
            button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
            },
            multiple: false // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            attachments = file_frame.state().get('selection').first().toJSON();
            i_add_images( attachments, thiss_id );
        });

        file_frame.open();
    });

    //adding images
    function i_add_images( image, i_image_input ){
        var cur_input = $( '#' + i_image_input );
        console.log( cur_input.attr('id') );console.log( image );
        var i_image_preview = $( '#i_preview_'+i_image_input );
        if( cur_input.attr( 'data-set-width-el' ) != '' ){
            $( '#'+cur_input.data( 'set-width-el' ) ).val( image.width );
        }
        if( cur_input.attr( 'data-set-height-el' ) != '' ){
            $( '#'+cur_input.data( 'set-height-el' ) ).val( image.height );
        }
        cur_input.val( image.url).change();

        //$('#mockup_item_link').val( images.name );
        //$('#mockup_attachment_id').val( images.id );

        i_image_preview.hide().attr( 'src', image.url ).show();
        return false;
    }

    //moup submit checking
    $('#moup_submit').click(
        function (){
            if( $('#mockup_attachment_id').val() == '' )
                return false;
        }
    );

    ////Delete
    $('body').on('click','.i_remove',get_open_del_window);
    function get_open_del_window() {
        thiss = $(this);
        if (confirm(" Delete this Mockup ? ")) {
            $(this).parents('form').submit();
        }
        return false;
    }


    $( ".i_json_preview" ).click(function() {
        $(".i_sdc_json_preview").css("display","block");
        $(".i_sdc_html_preview").css("display","none");
    });
    $( ".i_html_preview" ).click(function() {
        $(".i_sdc_json_preview").css("display","none");
        $(".i_sdc_html_preview").css("display","block");
    });

});
