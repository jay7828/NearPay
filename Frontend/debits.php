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
    <title>NearPay Payment Service | Logs</title>
    <link rel="stylesheet" href="./styles/logs.css" />
    <script src="https://kit.fontawesome.com/bef2386e82.js" crossorigin="anonymous"></script>
</head>

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
                <h1>Debits</h1>
            </div>
            <div class="logtable">
                <table>
                    <tr id="header">
                        <th>S/N</th>
                        <th>transaction_id</th>
                        <th>amount</th>
                        <th>transaction type</th>
                        <th>timestamp</th>
                    </tr>

                    <?php
					include "../backend/db_conn.php";
					$fetch_logs = mysqli_query($conn, "SELECT  transaction_id, amount, transaction_type, reads_card, time_stamp FROM `payment`as t1 where transaction_type='debit'");
	
					$sn = 1;
					while($res = mysqli_fetch_assoc($fetch_logs)){
					?>
                    <tr>
                        
                        <td><?php echo $sn; ?></td>
                        <td><?php echo $res['transaction_id']; ?></td>
                        <td><?php echo $res['amount']; ?></td>
                        <td><?php echo $res['transaction_type']; ?></td>
                        <td><?php echo $res['time_stamp']; ?></td>
                    
                    </tr>
                    <?php $sn ++; } ?>
                </table>
            </div>
        </div>
    </div>
</body>

</html>

<?php 
}else{
	header("location: ./index.php");
} 
?>