<?php
// Include the file with the database connection
include "./db_conn.php";

// Ensure the API key matches
$api_key = "Jayesh";

if ($api_key == strval($_GET["apikey"])) {
    // Check if the request method is GET
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Sanitize input
        $card_number = sanitizeInput($_GET["card_number"]);

        // Fetch student data
        $fetch_sql = "SELECT * FROM students_data WHERE card_number='$card_number'";
        $fetch_res = mysqli_query($conn, $fetch_sql);

        if (mysqli_num_rows($fetch_res) > 0) {
            while ($card = mysqli_fetch_assoc($fetch_res)) {
                $card_balance = $card['balance'];
                $card_new_balance = $card_balance;

                // Fetch latest payment transaction
                $check_transaction = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM payment WHERE reads_card=0 ORDER BY payment_id DESC LIMIT 1"));

                if (!is_null($check_transaction)) {
                    $payment_type = $check_transaction['transaction_type'];
                    $amount_to_pay = $check_transaction['amount'];
                    $transaction_id = $check_transaction['transaction_id'];

                    if ($payment_type == "debit") {
                        if ($card_balance < $amount_to_pay) {
                            echo "Insufficient balance, current balance = #" . $card_new_balance;
                        } else {
                            $card_new_balance = $card_balance - $amount_to_pay;
                            updateBalance($conn, $card_number, $amount_to_pay, $card_balance, $card_new_balance, "Payment successful", "debit", $transaction_id, $card['name']);
                        }
                    } else {
                        $card_new_balance = $card_balance + $amount_to_pay;
                        updateBalance($conn, $card_number, $amount_to_pay, $card_balance, $card_new_balance, "Account credited", "credit", $transaction_id, $card['name']);
                    }
                } else {
                    echo "No pending transaction found";
                }
            }
        } else {
            echo "Card not registered";
        }

        $conn->close();
    } else {
        echo "Invalid method of sending data";
    }
} else {
    echo "Incorrect API key";
}

// Function to sanitize input
function sanitizeInput($input) {
    global $conn; // Assuming $conn is your database connection object
    return mysqli_real_escape_string($conn, htmlspecialchars(stripslashes(trim($input))));
}

// Function to update balance and log transaction
function updateBalance($dbConn, $cardNumber, $amount, $previousBalance, $currentBalance, $returnMessage, $transactionType, $transactionId, $name) {
    // Update database with new balance
    $update_sql = "UPDATE students_data SET balance='$currentBalance' WHERE card_number='$cardNumber'";
    $log_update = "INSERT INTO logs (card_number, transaction_type, amount, previous_balance, balance) VALUES ('$cardNumber', '$transactionType','$amount', '$previousBalance', '$currentBalance')";
    $transaction_update = "UPDATE payment SET reads_card=1 WHERE transaction_id='$transactionId'";

    // Check if updating of balance worked
    if ($dbConn->query($update_sql) === TRUE && $dbConn->query($log_update) === TRUE && $dbConn->query($transaction_update)) {
        echo "Thank you, ".$name."! ";
        echo $returnMessage . ", new balance = #" . $currentBalance;
    } else {
        echo "Error: " . $update_sql . "<br>" . $dbConn->error;
    }
}
?>
