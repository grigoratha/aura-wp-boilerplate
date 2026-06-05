<?php
if (!defined('THEME_LOG_LEVEL')) {
    define('THEME_LOG_LEVEL', 'WARNING');
}

function log_get_trace(): array {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    $result = [
        'caller'  => 'unknown',
        'invoker' => 'unknown',
        'stack'   => '',
    ];

    $skip = [
        'log_get_trace',
        'theme_logger',
        'log_info',
        'log_warn',
        'log_error',
    ];

    $found  = 0;
    $stack  = [];

    foreach ($trace as $frame) {
        if (empty($frame['function']) || in_array($frame['function'], $skip, true)) {
            continue;
        }

        $label = $frame['function'];

        if (!empty($frame['class'])) {
            $label = $frame['class'] . $frame['type'] . $label;
        }

        if (!empty($frame['line'])) {
            $label .= ':' . $frame['line'];
        }

        // Full trace
        $stack[] = $label;

        if ($found === 0) {
            $result['caller'] = $label;
        }
        elseif ($found === 1) {
            $result['invoker'] = $label;
        }

        $found++;
    }

    // Format call stack
    $result['stack'] = implode("\n", array_reverse($stack));

    $result['stack'] = implode(
        "\n",
        array_map(
            fn($line) => '↪ ' . $line,
            array_reverse($stack)
        )
    );

    return $result;
}

function theme_logger($message, $level = 'INFO', $domain = '') {

    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }

    // Handle Array / Object
    if (is_array($message) || is_object($message)) {
        $message = print_r($message, true);
    }

    $trace = log_get_trace();

    $entry = [
        'time'    => date('Y-m-d H:i:s'),
        'level'   => strtoupper($level),
        'domain'  => $domain,
        'caller'  => $trace['caller'],
        'invoker' => $trace['invoker'],
        'msg'     => $message,
    ];

    $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;

    file_put_contents(THEME_LOG_FILE, $line, FILE_APPEND);
}

function log_level_filter($level): bool {

    $levels = [
        'NONE'    => 0,
        'ERROR'   => 1,
        'WARNING' => 2,
        'INFO'    => 3,
    ];

    $current = $levels[THEME_LOG_LEVEL] ?? 3;
    $incoming = $levels[strtoupper($level)] ?? 3;

    return $incoming <= $current;
}

function log_info($message, $domain = '') {
    if (!log_level_filter('INFO')) {
        return;
    }
    
    theme_logger($message, 'INFO', $domain);
}

function log_warn($message, $domain = '') {
    if (!log_level_filter('WARNING')) {
        return;
    }

    theme_logger($message, 'WARNING', $domain);
}

function log_error($message, $domain = '') {
    if (!log_level_filter('ERROR')) {
        return;
    }

    theme_logger($message, 'ERROR', $domain);
}

function log_clear($domain = '') {
    if (file_exists(THEME_LOG_FILE)) {
        $result = file_put_contents(THEME_LOG_FILE, '');

        if ($result === false) {
            log_error(THEME_LOG_FILE . " failed to clear", $domain);

            return [
                'success' => false,
                'error'   => 'Failed to clear log file'
            ];
        }

        return [
            'success' => true,
            'message' => 'Log file cleared successfully'
        ];
    } 
    else {
        log_warn(THEME_LOG_FILE . " was not found", $domain);

        return [
            'success' => false,
            'error'   => 'Log file was not found'
        ];
    }
}
?>