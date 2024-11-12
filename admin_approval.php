<?php
include 'connect.php';

// Query to fetch pending users
$query = "
    SELECT 
        ua.acc_id,
        ua.username,
        c.accName,
        c.consumerTypeCode,
        c.townID,
        c.zoneID
    FROM 
        tbluseraccess ua
    JOIN 
        tblconsumer c ON ua.acc_id = c.acc_id
    WHERE   
        ua.isApproved = 'pending'
    ORDER BY 
        ua.acc_id ASC
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="admin_approval.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <title>INEC - Admin Dashboard</title>
</head>
<body>
    <section class="header">
        <nav>
            <a href="admin_approval.php">
                <img src="images/logo.png" alt="Logo">
                <div class="logo-text">
                    <h1>INEC</h1>
                    <p>ILOCOS NORTE ELECTRIC COOPERATIVE</p>
                </div>
            </a>
            <div class="admin">
                <span>Admin_1</span>
                <img src="images/profile.png" alt="Admin Profile">
                <div class="logout-menu">
                    <form action="logout.php" method="post">
                        <input type="submit" value="Logout">
                    </form>
                </div>
            </div>
        </nav>
    </section>

    <div class="main-content">
        <div class="title-container">
            <div class="title">Applications</div>
        </div>

        <div class="form-container">
            <table id="users">
                <thead>
                    <tr>
                        <th>Account ID</th>
                        <th>Username</th>
                        <th>Account Name</th>
                        <th>Consumer Type Code</th>
                        <th>Town ID</th>
                        <th>Zone ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_array($result)) { ?>
                        <tr>
                            <td><?php echo $row['acc_id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['accName']; ?></td>
                            <td><?php echo $row['consumerTypeCode']; ?></td>
                            <td><?php echo $row['townID']; ?></td>
                            <td><?php echo $row['zoneID']; ?></td>
                            <td>
                                <form action="admin_approval.php" method="post">
                                    <input type="hidden" name="acc_id" value="<?php echo $row['acc_id']; ?>"/>
                                    <input type="submit" name="approve" value="Approve" />
                                    <input type="submit" name="deny" value="Deny"/>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['acc_id'];
        $currentDate = date('Y-m-d');

        if (isset($_POST['approve'])) {
            // Update tblUserAccess
            $update_user_query = "UPDATE tbluseraccess SET isApproved = 'approved', dateApproved = '$currentDate' WHERE acc_id = '$id'";
            $update_user_result = mysqli_query($conn, $update_user_query);

            // Update tblConsumer
            $update_consumer_query = "UPDATE tblconsumer SET accStatus = 'connected', dateConnected = '$currentDate' WHERE acc_id = '$id'";
            $update_consumer_result = mysqli_query($conn, $update_consumer_query);

            if ($update_user_result && $update_consumer_result) {
                echo "<script>alert('User Approved!'); window.location.href = 'admin_approval.php';</script>";
            } else {
                echo "<script>alert('Approval Failed: " . mysqli_error($conn) . "');</script>";
            }
        }

        if (isset($_POST['deny'])) {
            $update_query = "UPDATE tbluseraccess SET isApproved = 'denied', dateApproved = '$currentDate' WHERE acc_id = '$id'";
            $result = mysqli_query($conn, $update_query);
            if ($result) {
                echo "<script>alert('User Denied!'); window.location.href = 'admin_approval.php';</script>";
            } else {
                echo "<script>alert('Denial Failed: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
    ?>

    <script>
        // JavaScript to toggle the logout menu
        document.querySelector('.admin').addEventListener('click', function() {
            this.classList.toggle('show-logout-menu');
        });

        // Close the logout menu if clicked outside of it
        document.addEventListener('click', function(e) {
            const admin = document.querySelector('.admin');
            if (!admin.contains(e.target)) {
                admin.classList.remove('show-logout-menu');
            }
        });
    </script>

</body>
</html>