<?php
include 'connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

// Fetch account ID from session
$acc_id = $_SESSION['acc_id'];

// Initialize variables for account number and billing info
$accNum = '';
$accountDetails = [];
$billingInfo = [];

// Fetch account number
$stmt = $conn->prepare("SELECT accNum FROM tblConsumer WHERE acc_id = ?");
$stmt->bind_param("i", $acc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $accNum = htmlspecialchars($row['accNum']);
} else {
    die("Error: Account number not found.");
}

$stmt->close();

$stmt = $conn->prepare("SELECT accName, zoneID, townID, accStatus, consumerTypeCode, dateConnected FROM tblConsumer WHERE acc_id = ?");
$stmt->bind_param("i",$acc_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $accountDetails = $result->fetch_assoc();
}else{
    die("Error: Account Details not found");
}

$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['billCode'])) {
    $billCode = htmlspecialchars($_POST['billCode']); // Sanitize user input

    // Validate bill code format
    $billCodePattern = '/^(0[1-9]|1[0-2])\d{4}$/'; // Regex to match MMYYYY
    if (!preg_match($billCodePattern, $billCode)) {
        $billingInfo['error'] = 'Invalid format. Please enter MMYYYY (e.g., 082024).';
    } else {
        // Prepare the SQL statement to fetch billing information
        $stmt = $conn->prepare("SELECT accNum, billCode, totalEB, totalServiceCharge, totalSurCharge, totalAmountDue, billingDate, billDueDate, amountAfterDueDate FROM tblBillingInfo WHERE acc_id = ? AND billCode = ?");
        $stmt->bind_param("is", $acc_id, $billCode);

        // Execute the statement
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die("Error in SQL statement: " . $conn->error); // Handle SQL errors
        }

        // Fetch the results
        if ($result->num_rows > 0) {
            $billingInfo = $result->fetch_assoc();
            $billingInfo['error'] = ''; // Clear any previous error messages
        } else {
            $billingInfo['error'] = 'No billing information found for the bill code.'; // Set error message if no results
        }

        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>


<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="user_dashboard.css">
    <script src="user.js"></script>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <title>Dashboard - Ilocos Norte Electric Cooperative, Inc.</title>
</head>
<body>
    <section class="header">
        <nav>
            <a href="user_dashboard.php"><img src="images/logo.png" alt="Logo"></a>
            <div class="logo-text">
                <h1>INEC</h1>
                <p>ILOCOS NORTE ELECTRIC COOPERATIVE</p>
            </div>
            <div class="nav-links">
                <ul>
                    <li><a href="#" onclick="showContactUs()">CONTACT US</a></li>
                    <li><a href="#" onclick="showAboutUs()">ABOUT US</a></li>
                </ul>
            </div>
        </nav>
    </section>

    <!-- About Us Section -->
    <div id="about-us" class="about-us">
        <button id="exit-button" onclick="hideAboutUs()"></button>
        <div class="about-us-content">
            <h1>About Us</h1>
            <h2><br><br>Brief History</h2>
            <p><br>Recognizing the importance of electricity in the development of the province of Ilocos Norte, on July 7, 1971, Governor Elizabeth Marcos Keon placed the province under the “Rural Electrification Program” being undertaken by the National Electrification Administration (NEA) by virtue of Presidential Decree No. 269 by then President Ferdinand E. Marcos.</p>
            <p><br>On the same date, the Articles of Incorporation of the Ilocos Norte Electric Cooperative, Inc. was signed. With the full support of the Government Officials spearheaded by Governor Keon, backed by the Provincial Electric Cooperative Team (PECT) as headed by Chairman Eligio Magpantay, the Ilocos Norte Electric Cooperative, Inc. (INEC) was registered with NEA under NEC Resolution No. 8 on August 10, 1977.</p>
            <p><br>Eight members of the Board of Directors were appointed representing the originally conceived eight eastern towns of the province as envisioned by the incorporators namely: Mr. Oswaldo Parado of Dingras; Mr. Elficer T. Garcia of Banna; Mrs. Magdalena Mabuti of Marcos; Mrs. Juanita Acnam of Nueva Era; Mr. Julian Bayag of Piddig; Atty. Alipio Flores of Sarrat; Mrs. Trinidad Mata of Solsona, and Mr. Jaime Hernando of Vintar. The first General Manager was Col. Teodoro Apostol and the first President was Elficer T. Garcia.</p>
            <p><br>The cooperative began its operation with an initial outlay of Php 15.3 million in materials, a loan from the National Electrification Administration.</p>
            <p><br>The first energization was launched in September 11, 1973 and the first General Annual Meeting was held in 1974 at Dingras.</p>
            <p><br>In 1975, INEC bought the franchises of four private electric plants in Paoay, Bacarra, Currimao and Batac.</p>
            <p><br>In 1977, the cooperative took over the privately-owned Ilocos Norte Electric Company, thus, the electrification of the entire province was left at the hands of INEC.</p>
            <p><br>1978 was a year of struggle for INEC. There was an acute shortage of construction materials and electrical hardwares needed for expansion. Collection was made through bicycles and some consumers paid in kind such as chicken, vegetables, and rice among others. However, house connection never stopped and even cynics were surprised to know that in a short period of time, at least 31,311 houses were lighted in September 1978. For this feat, INEC ranked third among the then 121 electric cooperatives in the country as far as house connection is concerned.</p>
            <p><br>By November 14, 1999, INEC was able to achieve 100% barangay level energization with the energization of Brgy. Barangobong in Nueva Era. 40 years after its incorporation, the cooperative finally achieved total rural electrification down to the sitio level when Sitio Bucarot in the municipality of Adams was energized on May 2, 2012.</p>
            <p><br>On January 24, 2006, NEA issued Certificate of Franchise Number 219 to INEC, to wit, “for the renewal of the franchise granted under NEC Resolution No. 8, dated August 10, 1977 for a period of Twenty Five (25) years from the expiry of its original franchise on August 10, 2027 or until August 10, 2052.”</p>
            <p><br>INEC’s service area covers the whole of Ilocos Norte which is composed of 2 cities, 21 municipalities and 559 barangays with 143,376 member-consumer-owners with 164,550 house connections as of December 31, 2018.</p>
            <img src="images/milestone.png" alt="Milestone">
        </div>
    </div>

    <!-- Contact Us Section -->
    <div id="contact-us" class="contact-us">
        <div class="contact-us-content">
            <h2>Area Offices</h2>
            <div class="contact-us-row">

                <div class="contact-us-box">
                    <h3>Main and Area Office</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. Suyo, Dingras, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>(077) 600-4632</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0998) 951-4632<br><b>Globe:</b> (0917) 579-4632</p>
                    </div>
                </div>

                <div class="contact-us-box">
                    <h3>Area II Office</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. 23, San Matias, Vintar Road, Laoag City, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>(077) 771-3343 | (077) 773-19-01</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart: </b> (0920) 959-4632 | (0998) 952-4632<br><b>Globe:</b> (0917) 512-4632 | (0917) 728-4632 | (0917) 800-4632</p>
                    </div>
                </div>

                <div class="contact-us-box">
                    <h3>Area III</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. Lacub, Batac City, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>Not Available</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0920) 957-4632<br><b>Globe:</b>  (0917) 300-4632</p>
                    </div>
                </div>
                <div class="contact-us-box">
                    <h3>Area III</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. Lang-ayan, Currimao, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>Not Available</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0920) 967-4632<br><b>Globe:</b>  (0917) 300-4632</p>
                    </div>
                </div>

                <div class="contact-us-box">
                    <h3>Area IV Office</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. Masikil, Bangui, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>(077) 676-1344 | (0917) 724-432</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0920) 958-4632<br><b>Globe:</b>  (0917) 729-4632</p>
                    </div>
                </div>
                <div class="contact-us-box">
                    <h3>Area V Office</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. San Joaquin, Sarrat, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>(077) 781-28-96
                        </p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0928) 862-3624 | (0998) 950-4632<br><b>Globe:</b> (0917) 555-4632 | (0917) 500-4632</p>
                    </div>
                </div>
                <div class="contact-us-box">
                    <h3>Area VI Office</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. 16, San Marcos, San Nicolas, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>(077) 781-28-96
                        </p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0998) 868-9896<br><b>Globe:</b>  (0917) 630-4632</p>
                    </div>
                </div>
                <div class="contact-us-box">
                    <h3>Mini-Hydro Power Plant</h3>
                    <div class="contact-info">
                        <img src="images/loc.png" alt="Barangay Icon" class="icon">
                        <p>Brgy. Pancian, Pagudpud, Ilocos Norte</p>
                    </div>
                    <div class="contact-info">
                        <img src="images/phone.png" alt="Phone Icon" class="icon">
                        <p>(077) 676-13-09
                        </p>
                    </div>
                    <div class="contact-info">
                        <img src="images/cp.png" alt="Mobile Icon" class="icon">
                        <p><b>Smart:</b> (0920) 919-8677<br><b>Globe:</b> (0908) 810-3855</p>
                    </div>
                </div>
            </div>
            <button id="contact-exit-button" onclick="hideContactUs()"></button>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="overlay"></div>





    <!-- Dashboard Content -->
    <div id="dashboard" class="dashboard">
        <div class="dashboard-content">
            <div class="options">
                <h2><button id="billing-btn" onclick="showBillingInfo()">Billing Info</button></h2>
                <h2><button id="profile-btn" onclick="showMyProfile()">My Profile</button></h2>
            </div>
            <div class="dashboard-info">
                <div id="billing-info" class="billing-info">
                    <!-- Account Number and Bill Code -->
                    <div class="account-info">
                        <div class="info-box">
                            <span class="info-label">Account Number</span>
                            <span class="info-value" id="account-number"><?php echo htmlspecialchars($accNum); ?></span>
                        </div>
                        <form method="post" action="">
                            <label for="billCode">Enter Bill Code:</label>
                            <input type="text" id="billCode" name="billCode" required placeholder="MMYYYY">
                            <input type="submit" value="Search">
                            <?php if (isset($billingInfo['error'])): ?>
                                <p class="error-message"><?php echo htmlspecialchars($billingInfo['error']); ?></p>
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- Always visible info boxes with empty values -->
                    <div class="info-box-container">
                        <div class="info-box">
                            <span class="info-label">Total Electric Bill</span>
                            <span class="info-value" id="total-eb">₱<?php echo htmlspecialchars($billingInfo['totalEB'] ?? ''); ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Total Service Charge</span>
                            <span class="info-value" id="total-service-charge">₱<?php echo htmlspecialchars($billingInfo['totalServiceCharge'] ?? ''); ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Total Surcharge</span>
                            <span class="info-value" id="total-surcharge">₱<?php echo htmlspecialchars($billingInfo['totalSurCharge'] ?? ''); ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Total Amount Due</span>
                            <span class="info-value" id="total-amount-due">₱<?php echo htmlspecialchars($billingInfo['totalAmountDue'] ?? ''); ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Billing Date</span>
                            <span class="info-value" id="billing-date"><?php echo htmlspecialchars($billingInfo['billingDate'] ?? ''); ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Due Date</span>
                            <span class="info-value" id="due-date"><?php echo htmlspecialchars($billingInfo['billDueDate'] ?? ''); ?></span>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Amount After Due Date</span>
                            <span class="info-value" id="amount-after-due-date">₱<?php echo htmlspecialchars($billingInfo['amountAfterDueDate'] ?? ''); ?></span>
                        </div>
                    </div>
                </div>


                <!-- My Profile Content -->
                <div id="myprofile" class="myprofile" style="display: none;">
                    <div class="myprofile-groupone">
                        <label for="accNum">Account Number</label>
                        <div class="info-box">
                            <span class="profile-value" id="accNum"><?php echo htmlspecialchars($accNum); ?></span>
                        </div>
                        
                        <label for="accName">Name</label>
                        <div class="info-box">
                            <span class="profile-value" id="accName"><?php echo htmlspecialchars($accountDetails['accName']);?></span>
                        </div>
                    </div>

                    <div class="myprofile-grouptwo">
                        <div>
                            <label for="zoneid">Zone ID</label>
                            <div class="info-box">
                                <span class="profile-value" id="profile-zoneid"><?php echo htmlspecialchars($accountDetails['zoneID']);?></span>
                            </div>
                        </div>
                        <div>
                            <label for="townid">Town ID</label>
                            <div class="info-box">
                                <span class="profile-value" id="profile-townid"><?php echo htmlspecialchars($accountDetails['townID']);?></span>
                            </div>
                        </div>
                        <div>
                            <label for="accstatus">Account Status</label>
                            <div class="info-box">
                                <span class="profile-value" id="profile-accstatus"><?php echo htmlspecialchars($accountDetails['accStatus']);?></span>
                            </div>
                        </div>
                    </div>

                    <div class="myprofile-groupthree">
                        <label for="consumertype">Consumer Type</label>
                        <div class="info-box">
                            <span class="profile-value" id="consumertype"><?php echo htmlspecialchars($accountDetails['consumerTypeCode']);?></span>
                        </div>
                        
                        <label for="date-connected">Date Connected</label>
                        <div class="info-box">
                            <span class="profile-value" id="profile-date-connected"><?php echo htmlspecialchars($accountDetails['dateConnected']);?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Logout Button -->
            <div class="logout-container">
                <form id="logout-form" action="logout.php" method="post">
                    <button type="submit" class="logout-button">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
