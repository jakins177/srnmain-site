<?php
// Function to log custom messages to a specified log file.
function custom_log($message, $log_file_name) {
    // Define the logs directory path relative to this file's directory.
    $log_dir = __DIR__ . '/../logs';

    // Check if the logs directory exists. If not, try to create it.
    if (!is_dir($log_dir)) {
        // The third parameter 'true' allows the creation of nested directories.
        // The octal value 0777 sets the permissions to be as permissive as possible.
        if (!mkdir($log_dir, 0777, true) && !is_dir($log_dir)) {
            // If mkdir fails, and the directory still doesn't exist, log to PHP's system logger as a fallback.
            error_log("custom_log: FATAL - Failed to create log directory: {$log_dir}");
            // Terminate the script with an error message. This is a critical failure.
            die("FATAL: Logging directory could not be created. Please check permissions.");
        }
    }

    // Define the full path for the log file.
    $log_file = $log_dir . '/' . $log_file_name;
    // Format the message with a timestamp. 'c' provides a full ISO 8601 date.
    $formatted_message = date('c') . ' - ' . $message . "\n";

    // Append the formatted message to the log file.
    // The FILE_APPEND flag ensures that the message is added to the end of the file.
    // The LOCK_EX flag prevents other processes from writing to the file at the same time.
    if (file_put_contents($log_file, $formatted_message, FILE_APPEND | LOCK_EX) === false) {
        // If writing to the file fails, log the error to the system logger.
        error_log("custom_log: ERROR - Failed to write to log file: {$log_file}");
    }
}
?>
