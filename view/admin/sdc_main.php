<?php
    echo '<link type="text/css" href="'.SDC_PLUGIN_URL.'resources/style/admin_style.css" rel="stylesheet"/>';
    echo '<script type="text/javascript" src="'.SDC_PLUGIN_URL.'resources/js/admin_js.js" > </script>';

    $sdc_uploads=get_option('sdc_uploads',true);
    $home_url = home_url(); //i_print($sdc_uploads);
?>

<h1> <?php _e( 'Simple Schema', 'sdc_plugin' ); ?> </h1>
<form method="post" action="">
    <div class="wrap i_sdc_wrap">
        <div class="postbox i_sdc_div">

        </div>
    </div>
</form>