<?php
// Include the Workable class definition
require_once '../classes/Workable.php';

/**
 * Helper function to send a JSON response and exit
 *
 * @param int   $code        HTTP response code
 * @param array $data_array  Data to return as JSON
 */
function response($code, $data_array){
    http_response_code($code); // Set HTTP status code
    echo json_encode($data_array, JSON_UNESCAPED_UNICODE); // Output JSON with Unicode characters
    exit; // Stop further script execution
}

try {
    // Determine which class to instantiate based on 'target' in the request
    switch ($_REQUEST["target"]) {
        case 'workable':
            $object = new Workable(); // Create a Workable object
            break;
        default:
            // If no matching target, return a 400 Bad Request response
            response(400, array('message'=>'Bad Request'));
            exit;
            break;
    }
}
catch (PDOException $e) {
    // Handle database-related exceptions (example: MySQL error 2006 - server has gone away)
    throw $e;
}

try {
    // Handle the action requested via 'action' parameter
    switch ($_REQUEST["action"]) {
        case 'sync_file':
            // Call the sync_file method from the instantiated object (e.g., Workable)
            response(200, $object->sync_file($_REQUEST));
            break;
    }
}
catch (Throwable $e) {
    // Re-throw any unexpected exceptions or errors
    throw $e;
}
