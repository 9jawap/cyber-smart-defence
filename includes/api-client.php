<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function csd_check_ip_reputation( $ip ) {
    $response = wp_remote_get(
        'https://cybersmartempire.com/api/reputation?ip=' . urlencode( $ip ),
        [ 'timeout' => 3 ]
    );

    if ( is_wp_error( $response ) ) return false;

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    return ! empty( $data['malicious'] );
}
