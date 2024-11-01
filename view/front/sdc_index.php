<?php
    global $mockup_item;
    $sdc_uploads = get_option('sdc_uploads',true);
    if( isset( $sdc_uploads['item_link'] ) && $sdc_uploads['item_link'] == $mockup_item ) {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta name="viewport" content="width=device-width, minimum-scale=0.1">
                <title> <?php echo $mockup_item; ?></title>
                <style type="text/css">
                    body {
                        background: #ffffff;
                    }
                    img {
                        display: table;
                        max-width: 100%;
                        margin: auto;
                    }
                </style>
            </head>
            <body style="margin: 0px;">
                <img src="<?php echo $sdc_uploads['image_url']; ?> " >
            </body>
        </html>
        <?php
        exit;
    } else {
        wp_redirect( home_url() );
    }
?>