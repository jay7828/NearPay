<?php
// Include the file with the database connection
include "./db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve POST parameters
    $amount = $_POST['amount'];
    $type = $_POST['type']; // 'debit' or 'credit'

    // Validate and sanitize inputs (assuming amount and type are validated on the client side)

    // Insert transaction into payment table
    $transaction_id = time(); // Generate transaction ID (you might want to use a more secure method)
    $reads_card = 0; // Assuming the card was read for this transaction
    
    $query = "INSERT INTO payment (transaction_id, amount, transaction_type, reads_card) 
              VALUES ('$transaction_id', '$amount', '$type', '$reads_card')";

    if (mysqli_query($conn, $query)) {
        // Redirect to approved transaction page
        header("Location: ../Frontend/approved_transaction_page.html");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request method.";
}
?>
