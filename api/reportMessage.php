><?php
// Include database connection
$mysqli = require __DIR__ . "/database.php";

// Function to sanitize input
function sanitizeInput($input) {
    return mysqli_real_escape_string($GLOBALS['mysqli'], $input);
}

// Check if JSON data is received
if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    // Get JSON data from the request body
    $json_data = file_get_contents('php://input');

    // Check if JSON data is not empty
    if (!empty($json_data)) {
        // Decode JSON data
        $data = json_decode($json_data, true);

        // Check if required fields are present in JSON data
        if (isset($data["message_id"])) {
            // Sanitize input
            $messageID = sanitizeInput($data["message_id"]);

            // Update query
            $sql = "UPDATE message SET Reported = 1 WHERE ID = $messageID";

            // Perform the update query
            if ($mysqli->query($sql) === TRUE) {
                echo json_encode(array("status" => "success", "message" => "Message reported successfully."));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error reporting message. Please try again."));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Invalid JSON data. 'message_id' is required."));
        }
        exit(); // Stop execution after processing JSON data
    }
}

// If not JSON data, proceed with form data handling
$messageID = isset($_POST["message_id"]) ? sanitizeInput($_POST["message_id"]) : null;

if (!is_null($messageID)) {
    // Update query
    $sql = "UPDATE message SET Reported = 1 WHERE ID = $messageID";

    // Perform the update query
    if ($mysqli->query($sql) === TRUE) {
        $subjectPIN = $_POST["subject_pin"];
        header("Location: /api/emnePage.php/?subject_pin=$subjectPIN");
    } else {
        echo "Error reporting message. Please try again.";
    }
} else {
    echo "No message ID received.";
}
?>
