<?php
function http_request($method, $url, $args = []) {
    $defaults = [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
            'User-Agent'   => 'Mozilla/5.0'
        ],
        'timeout' => 15,
        'body'    => null,
    ];

    $args = array_merge($defaults, $args);

    if (!empty($args['body']) && is_array($args['body'])) {
        $args['body'] = json_encode($args['body']);
    }

    $response = wp_remote_request($url, [
        'method'  => strtoupper($method),
        'headers' => $args['headers'],
        'body'    => $args['body'],
        'timeout' => $args['timeout'],
    ]);

    if (is_wp_error($response)) {
        return [
            'success' => false,
            'error'   => $response->get_error_message(),
        ];
    }

    $status = wp_remote_retrieve_response_code($response);
    $body   = wp_remote_retrieve_body($response);

    $decoded = json_decode($body, true);

    return [
        'status'  => $status,
        'message' => $decoded ?? $body
    ];
}
?>