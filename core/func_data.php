<?php
function theme_read_json_file(string $json_file) {

    if (!file_exists($json_file)) {
        log_error("Could not find JSON file <{$json_file}>", LOG_DOMAIN_FUNCTIONS);

        return null;
    }

    $json_data = file_get_contents($json_file);

    if ($json_data === false) {
        log_error("Could not read JSON file <{$json_file}>", LOG_DOMAIN_FUNCTIONS);

        return null;
    }

    $decoded = json_decode($json_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        log_error(
            "Invalid JSON in file <{$json_file}>: " . json_last_error_msg(),
            LOG_DOMAIN_FUNCTIONS
        );

        return null;
    }

    return $decoded;
}

function theme_write_json_file(string $json_file, $data): bool {
 
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);  

    if ($json === false) {
        log_error(
            "JSON encode failed for file <{$json_file}>: " . json_last_error_msg(),
            LOG_DOMAIN_FUNCTIONS
        );

        return false;
    }

    $dir = dirname($json_file);

    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
            log_error("Failed to create directory <{$dir}>", LOG_DOMAIN_FUNCTIONS);
            return false;
        }
    }

    $tmp_file = $json_file . '.tmp';

    $written = file_put_contents($tmp_file, $json, LOCK_EX);

    if ($written === false) {
        log_error("Failed to write JSON temp file <{$tmp_file}>", LOG_DOMAIN_FUNCTIONS);
        return false;
    }

    if (!rename($tmp_file, $json_file)) {
        log_error("Failed to move JSON file <{$tmp_file}> to <{$json_file}>", LOG_DOMAIN_FUNCTIONS);
        return false;
    }

    return true;
}
?>