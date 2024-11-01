<?php
/*
Plugin Name:    Simple Schema
Plugin URI: simpleschema.com
Description:    The most complete Semantic HTML Plugins for WordPress. Includes: Person, Product, Event, Organization, Movie, Book, and Review markup, and subtypes (Organization Type, Event Type).
Author: Simple Schema
Version:    1.0.2
Author URI: https://simpleschema.com
Promo Paragraf: Simple Schema is the most complete Semantic HTML Plugin available for WordPress. Search Engines use semantic markup to rank and display your content appropriately. Our plugin includes: Person, Product, Event, Organization, Movie, Book, and Review. Assign schemas per page or post; select where they display (Before Content, After Content, Hidden); even Preview them before saving. Simple Schema makes Semantic SEO as easy as selecting a few simple options and filling-in-the-blanks.
*/

// our prefix is SDC / SDC

define( 'SDCVersion', '1.0.0' );
define( 'SDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SDC_PROTECTION_H', plugin_basename(__FILE__) );
define( 'SDC_NAME', 'simple-schema' );
define( 'SDC_DATA_NAME', 'sdc_data' );
define( 'SDC_DATA_CONTENT', SDC_DATA_NAME.'_content' );
define( 'SDC_PLUGIN_SITE_URL', 'https://simpleschema.com/' );

define( 'SDC_PAGE_LINK', 'mockups' );

global $sdc_already_print;
$sdc_already_print = array();

function isdc_print( $array ) {
    echo '<pre>'; print_r( $array ); echo '</pre>';
}

register_activation_hook( __FILE__, array( 'SDC', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'SDC', 'plugin_deactivation' ) );


require_once( SDC_PLUGIN_DIR . 'lib/class.sdc.php' );

add_action( 'init', array( 'SDC', 'init' ) );
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( 'SDC', 'sdc_plugin_action_links' ) );

if ( is_admin() ) {
    require_once( SDC_PLUGIN_DIR . 'lib/class.sdc-admin.php' );
    add_action( 'init', array( 'SDC_Admin', 'init' ) );
}

/**
 * Searches haystack for needle and
 * returns an array of the key path if
 * it is found in the (multidimensional)
 * array, FALSE otherwise.
 *
 * @mixed array_searchRecursive ( mixed needle,
 * array haystack [, bool strict[, array path]] )
 */
function array_searchRecursive( $needle, $haystack, $strict=false, $path=array() ) {
    if( !is_array($haystack) ) {
        return false;
    }

    foreach( $haystack as $key => $val ) {
        if( is_array($val) && $subPath = array_searchRecursive($needle, $val, $strict, $path) ) {
            $path = array_merge($path, array($key), $subPath);
            return $path;
        } elseif( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
            $path[] = $key;
            return $path;
        }
    }
    return false;
}


require_once SDC_PLUGIN_DIR . 'lib/plugin-update-checker/plugin-update-checker.php';
//$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$MyUpdateChecker = PucFactory::buildUpdateChecker(
    'https://bitbucket.org/simpleschemateam/simple-schema-updates/raw/master/info.json',
    __FILE__,
    'simple-schema-updates'
);
