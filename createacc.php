<?php
session_start(); // Start the session

include 'connect.php';

$notification = ''; // Variable to hold notification messages
$errorUsername = ''; // Variable to hold error message for username

// Initialize variables to preserve form values
$accName = '';
$municipality = '';
$zone = '';
$consumertype = '';
$phonenumber = '';
$username = '';

// Check if there's a notification message in the session
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    unset($_SESSION['notification']); // Clear the notification message after displaying
}

if (isset($_POST['submit'])) {
    $accName = mysqli_real_escape_string($conn, $_POST['accName']);
    $municipality = mysqli_real_escape_string($conn, $_POST['municipality']);
    $zone = mysqli_real_escape_string($conn, $_POST['zone']);
    $consumertype = mysqli_real_escape_string($conn, $_POST['consumertype']);
    $phonenumber = mysqli_real_escape_string($conn, $_POST['phonenumber']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_users = mysqli_query($conn, "SELECT * FROM `tbluseraccess` WHERE Username = '$username'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $errorUsername = 'Username already exists.';
    } else {
        mysqli_query($conn, "INSERT INTO `tbluseraccess` (username, password) VALUES ('$username', '$password')") or die('query failed');

        $acc_id = mysqli_insert_id($conn);
        $accNum = sprintf("%02d%02d%06d", $municipality, $zone, $acc_id);

        mysqli_query($conn, "INSERT INTO `tblconsumer` (acc_id, accNum, accName, TownID, ZoneID, ConsumerTypeCode, phonenumber) VALUES ('$acc_id', '$accNum', '$accName', '$municipality', '$zone', '$consumertype', '$phonenumber')") or die('query failed');

        // Store the notification message in a session variable
        $_SESSION['notification'] = 'Account created! Please wait for approval. Thank you.';
        
        // Clear form values
        $accName = $municipality = $zone = $consumertype = $phonenumber = $username = '';

        // Redirect to the same page to clear form values
        header('Location: createacc.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="createacc.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <title>INEC - Create New Account</title>

</head>
<body>
    <form action="createacc.php" method="POST">
        <div class="container">
            <div class="left-side">
                <img src="images/logo.png" alt="Logo" class="logo">
                <p class="welcome-text"><b>Welcome <br>to<br> INEC</b></p>
            </div>
            <div class="right-side">
                <div class="create-account-form">
                    <div class="back-button">
                        <a href="login.php">
                            <img src="images/arrow.png" alt="Back">
                            <span>Back</span>
                        </a>
                    </div>
                    <h2>Create New Account</h2>

                    <!-- Notification Box -->
                    <?php if ($notification): ?>
                    <div class="notification-box">
                        <p><?php echo $notification; ?></p>
                        <button class="ok-button" onclick="hideNotification()">OK</button>
                    </div>
                    <?php endif; ?>

                    <div class="form-content">
                        <!-- Full Name Row with Label -->
                        <div class="label-row">
                            <label for="full-name">Full Name</label>
                        </div>
                        <div class="full-name-row">
                            <div class="input-box">
                                <input type="text" id="accName" name="accName" value="<?php echo htmlspecialchars($accName); ?>" required>
                                <label for="accName">Account Name</label>
                            </div>
                        </div>
                        
                        <!-- Address Row with Label -->
                        <div class="label-row">
                            <label for="address">Address</label>
                        </div>
                        <div class="full-name-row">
                            <div class="input-box">
                                <select id="municipality" name="municipality" onchange="updateZones()" required>
                                    <option value="">Select Municipality</option>
                                    <option value="52" <?php echo ($municipality == '52') ? 'selected' : ''; ?>>Adams</option>
                                    <option value="40" <?php echo ($municipality == '40') ? 'selected' : ''; ?>>Bacarra</option>
                                    <option value="32" <?php echo ($municipality == '32') ? 'selected' : ''; ?>>Badoc</option>
                                    <option value="46" <?php echo ($municipality == '46') ? 'selected' : ''; ?>>Bangui</option>
                                    <option value="12" <?php echo ($municipality == '12') ? 'selected' : ''; ?>>Banna</option>
                                    <option value="28" <?php echo ($municipality == '28') ? 'selected' : ''; ?>>Batac</option>
                                    <option value="44" <?php echo ($municipality == '44') ? 'selected' : ''; ?>>Burgos</option>
                                    <option value="54" <?php echo ($municipality == '54') ? 'selected' : ''; ?>>Carasi</option>
                                    <option value="30" <?php echo ($municipality == '30') ? 'selected' : ''; ?>>Currimao</option>
                                    <option value="10" <?php echo ($municipality == '10') ? 'selected' : ''; ?>>Dingras</option>
                                    <option value="50" <?php echo ($municipality == '50') ? 'selected' : ''; ?>>Dumalneg</option>
                                    <option value="36" <?php echo ($municipality == '36') ? 'selected' : ''; ?>>Laoag City</option>
                                    <option value="14" <?php echo ($municipality == '14') ? 'selected' : ''; ?>>Marcos</option>
                                    <option value="16" <?php echo ($municipality == '16') ? 'selected' : ''; ?>>Nueva Era</option>
                                    <option value="48" <?php echo ($municipality == '48') ? 'selected' : ''; ?>>Pagudpud</option>
                                    <option value="26" <?php echo ($municipality == '26') ? 'selected' : ''; ?>>Paoay</option>
                                    <option value="42" <?php echo ($municipality == '42') ? 'selected' : ''; ?>>Pasuquin</option>
                                    <option value="18" <?php echo ($municipality == '18') ? 'selected' : ''; ?>>Piddig</option>
                                    <option value="34" <?php echo ($municipality == '34') ? 'selected' : ''; ?>>Pinili</option>
                                    <option value="38" <?php echo ($municipality == '38') ? 'selected' : ''; ?>>San Nicolas</option>
                                    <option value="20" <?php echo ($municipality == '20') ? 'selected' : ''; ?>>Sarrat</option>
                                    <option value="22" <?php echo ($municipality == '22') ? 'selected' : ''; ?>>Solsona</option>
                                    <option value="11" <?php echo ($municipality == '11') ? 'selected' : ''; ?>>Town 11</option>
                                    <option value="19" <?php echo ($municipality == '19') ? 'selected' : ''; ?>>Town 19</option>
                                    <option value="27" <?php echo ($municipality == '27') ? 'selected' : ''; ?>>Town 27</option>
                                    <option value="35" <?php echo ($municipality == '35') ? 'selected' : ''; ?>>Town 35</option>
                                    <option value="37" <?php echo ($municipality == '37') ? 'selected' : ''; ?>>Town 37</option>
                                    <option value="43" <?php echo ($municipality == '43') ? 'selected' : ''; ?>>Town 43</option>
                                    <option value="24" <?php echo ($municipality == '24') ? 'selected' : ''; ?>>Vintar</option>
                                </select>
                                <label for="municipality">Municipality</label>
                            </div>
                            
                            <div class="input-box small-box" id="zone-container">
                                <select id="zone" name="zone" required>
                                    <!-- Options will be populated based on selected municipality -->
                                </select>
                                <label for="zone">Zone</label>
                            </div>
                        </div>
                        
                        <!-- Consumer Type Code and Phone Number Row -->
                        <div class="consumer-type-row">
                            <div class="input-box consumer-type-box">
                                <label for="consumertype">Consumer Type</label>
                                <select id="consumertype" name="consumertype" required>
                                    <option value="">Select Consumer Type</option>
                                    <option value="02" <?php echo ($consumertype == '02') ? 'selected' : ''; ?>>Residential</option>
                                    <option value="04" <?php echo ($consumertype == '04') ? 'selected' : ''; ?>>Small Commercial</option>
                                    <option value="05" <?php echo ($consumertype == '05') ? 'selected' : ''; ?>>Big Commercial - LV</option>
                                    <option value="06" <?php echo ($consumertype == '06') ? 'selected' : ''; ?>>Public Building - LV</option>
                                    <option value="08" <?php echo ($consumertype == '08') ? 'selected' : ''; ?>>Imigation</option>
                                    <option value="10" <?php echo ($consumertype == '10') ? 'selected' : ''; ?>>Industrial - LV</option>
                                    <option value="12" <?php echo ($consumertype == '12') ? 'selected' : ''; ?>>SL w/o Meter</option>
                                    <option value="14" <?php echo ($consumertype == '14') ? 'selected' : ''; ?>>Big Commercial - HV</option>
                                    <option value="18" <?php echo ($consumertype == '18') ? 'selected' : ''; ?>>Industrial - HV</option>
                                    <option value="20" <?php echo ($consumertype == '20') ? 'selected' : ''; ?>>SL with Meter</option>
                                    <option value="22" <?php echo ($consumertype == '22') ? 'selected' : ''; ?>>Public Building - HV</option>
                                </select>
                            </div>
                            
                            <div class="input-box phone-number-box">
                                <label for="phonenumber">Phone Number</label>
                                <input type="tel" id="phonenumber" name="phonenumber" value="<?php echo htmlspecialchars($phonenumber); ?>" required>
                            </div>
                        </div>

                        <!-- Username Row -->
                        <div class="label-row">
                            <label for="username">Username</label>
                        </div>
                        <div class="input-box">
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                            <?php if ($errorUsername): ?>
                            <div class="error-message"><?php echo $errorUsername; ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Password Row -->
                        <div class="label-row">
                            <label for="password">Password</label>
                        </div>
                        <div class="input-box password-box">
                            <div class="password-container">
                                <input type="password" id="password" name="password" required>
                                <i class="fas fa-eye" id="toggle-password" onclick="togglePassword()"></i>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="button-box">
                            <input type="submit" name="submit" value="Create Account">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <script>
            const zonesByMunicipality = {
                '52': ['4'],
                '40': ['2', '5'],
                '32': ['1', '3'],
                '46': ['4'],
                '12': ['1'],
                '28': ['1', '3', '6'],
                '44': ['4'],
                '54': ['5'],
                '30': ['3'],
                '10': ['1', '5'],
                '50': ['4'],
                '36': ['2', '5', '6'],
                '14': ['1'],
                '16': ['1'],
                '48': ['4'],
                '26': ['3', '6'],
                '42': ['2', '4', '5'],
                '18': ['5'],
                '34': ['1', '3'],
                '38': ['6'],
                '20': ['1', '5', '6'],
                '22': ['1', '5'],
                '11': ['1'],
                '19': ['5'],
                '27': ['3'],
                '35': ['2'],
                '37': ['6'],
                '43': ['4'],
                '24': ['5']
            };

            function updateZones() {
                const municipalitySelect = document.getElementById('municipality');
                const zoneContainer = document.getElementById('zone-container');
                const selectedMunicipality = municipalitySelect.value;
        
                // Clear existing zone input or select
                while (zoneContainer.firstChild) {
                    zoneContainer.removeChild(zoneContainer.firstChild);
                }
        
                // Add the label back to the container
                const label = document.createElement('label');
                label.setAttribute('for', 'zone');
                label.textContent = 'Zone';
                zoneContainer.appendChild(label);
            
                if (selectedMunicipality) {
                    const zones = zonesByMunicipality[selectedMunicipality];
            
                    if (zones.length === 1) {
                        const zoneInput = document.createElement('input');
                        zoneInput.type = 'text';
                        zoneInput.id = 'zone';
                        zoneInput.name = 'zone';
                        zoneInput.value = zones[0];
                        zoneInput.setAttribute('readonly', true); // Optional: Make it read-only
                        zoneContainer.appendChild(zoneInput);
                    } else {
                        const zoneSelect = document.createElement('select');
                        zoneSelect.id = 'zone';
                        zoneSelect.name = 'zone';
                        zones.forEach(zone => {
                            const option = document.createElement('option');
                            option.value = zone;
                            option.textContent = zone;
                            zoneSelect.appendChild(option);
                        });
                        zoneContainer.appendChild(zoneSelect);
                    }
                } else {
                    const defaultOption = document.createElement('input');
                    defaultOption.type = 'text';
                    defaultOption.id = 'zone';
                    defaultOption.name = 'zone';
                    defaultOption.setAttribute('readonly', true);
                    defaultOption.setAttribute('placeholder', 'Select Zone');
                    zoneContainer.appendChild(defaultOption);
                }
            }

            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const isPasswordVisible = passwordInput.type === 'text';
                
                passwordInput.type = isPasswordVisible ? 'password' : 'text';
                
                const toggleButton = document.getElementById('toggle-password');
                toggleButton.classList.toggle('fa-eye', isPasswordVisible);
                toggleButton.classList.toggle('fa-eye-slash', !isPasswordVisible);
            }

            function hideNotification() {
                document.querySelector('.notification-box').style.display = 'none';
            }
            
            // Initialize zones on page load if municipality is pre-selected
            document.addEventListener('DOMContentLoaded', () => {
                updateZones();
            });
        </script>
</body>
</html>