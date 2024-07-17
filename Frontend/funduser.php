<?php 
session_start();
if(isset($_SESSION['username'])){
    $user = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NearPay Service | Fund User</title>
    <link rel="stylesheet" href="./styles/funduser.css" />
    <script src="https://kit.fontawesome.com/bef2386e82.js" crossorigin="anonymous"></script>
</head>
<style>
    input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
<body>
    <div class="header">
        <div class="side-nav">
            <a href="#" class="logo">NearPay Payment Service</a>
            <ul class="nav-links">
                <li>
                    <a href="dashboard.php"><i class="fa-solid fa-gauge"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li>
                    <a href="funduser.php"><i class="fa-solid fa-money-bill-transfer"></i>
                        <p>Make Payment</p>
                    </a>
                </li>
                <li>
                    <a href="users.php"><i class="fa-solid fa-users"></i>
                        <p>User Management</p>
                    </a>
                </li>
                <li>
                    <a href="logs.php"><i class="fa-solid fa-book"></i>
                        <p>Logs</p>
                    </a>
                </li>
                <div class="logout">
                    <a href="../backend/process_logout.php">
                        <p>Log-Out</p>
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            </ul>
        </div>
        <div class="main">
            <div class="head">
                <h1>Payment</h1>
            </div>
            <form id="paymentForm" action="../backend/process_frontend_transaction.php" method="POST" onsubmit="return validateForm()">
    <div class="form-box">
        <h1>Make payment here!</h1>
        
        <div class="input-box">
            <div class="inputs">
                <input id="amountInput" name="amount" placeholder="Enter amount" required type="number">
                <p id="amountError" style="color: red; display: none;">Amount must be 1 or more.</p>
            </div>
        </div>
        <div class="oinput" style="display:flex; flex-direction:column; align-items:center;">
                <p style="margin-left:0;">Debit / Credit </p>
                <div class="dc"> 
                    <div class="dcinput">
                        <label for="tdebit">Debit&nbsp; &nbsp; </label>
                        <input type="radio" name="type" id="tdebit" value="debit">
                    </div>  
                    <div class="dcinput">
                        <label for="tcredit">Credit &nbsp;</label>
                        <input type="radio" name="type" id="tcredit" value="credit">
                    </div>        
                </div>
                <p id="typeError" style="color: red; display: none;">Please select Debit or Credit.</p>
            </div>
        </div>
        <button type="submit" class="login-btn">Fund User</button>
    </div>
</form>


</div>
<script src="users.js"></script>
</body>

<script>
    function validateForm() {
        var amount = document.getElementById("amountInput").value;
        var amountError = document.getElementById("amountError");
        
        var debitChecked = document.getElementById("tdebit").checked;
        var creditChecked = document.getElementById("tcredit").checked;
        var typeError = document.getElementById("typeError");

        // Validate amount
        if (amount < 1) {
            amountError.style.display = "flex";
            return false; // Prevent form submission
        } else {
            amountError.style.display = "none";
        }

        // Validate type (debit or credit)
        if (!debitChecked && !creditChecked) {
            typeError.style.display = "flex";
            return false; // Prevent form submission
        } else {
            typeError.style.display = "none";
        }

        return true; // Allow form submission
    }
</script>
</html>

<?php 
}else{
    header("location: ./index.php");
} 
?>