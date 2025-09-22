<?php
function custom_log($message, $log_file_name) {
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    $log_file = $log_dir . '/' . $log_file_name;
    $formatted_message = date('c') . ' - ' . $message . "\n";
    file_put_contents($log_file, $formatted_message, FILE_APPEND);
}
?>
