<?php
include "db/db_connect.php"; // include database connection

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete query
    $sql = "DELETE FROM customer_details WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to index after delete
        header("Location: index.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
