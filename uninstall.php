<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

// Uninstall code goes here

delete_option( 'moup_uploads' );