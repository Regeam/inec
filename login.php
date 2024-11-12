<?php
include 'connect.php';
session_start();

$error = ''; // Variable to store error message
$notificationMessage = ''; // Variable to store notification messages


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT * FROM tbluseraccess WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    // Check the result
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $isApproved = $row['isApproved'];

        if ($isApproved == "approved") { // Account is approved
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['accessType'] = $row['accessType'];
            $_SESSION['acc_id'] = $row['acc_id']; // Correctly set the acc_id

            if ($row['accessType'] == 1) {
                header("Location: admin_approval.php");
                exit; // Ensure the script stops executing after redirection
            } else {
                header("Location: user_dashboard.php");
                exit; // Ensure the script stops executing after redirection
            }
        } elseif ($isApproved == "pending") { // Account is pending
            $notificationMessage = "Your account is pending approval. Please wait for an administrator to approve your account.";
        } elseif ($isApproved == "denied") { // Account is denied
            $notificationMessage = "Your account has been denied. Please contact support for more information.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!-- HTML  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <title>Ilocos Norte Electric Cooperative, Inc.</title>
</head>
<body>
    <section class="header">
        <nav>
            <a href="login.php"><img src="images/logo.png" alt="Logo"></a>
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

    <!-- Log in form -->
    <form action="login.php" method = "POST">
        <div id="main-content" class="main-content">
            <div class="middle-text">
                <h1>SILAW TI ILOCOS NORTE</h1>
            </div>
            <div class="login-form">

                <!-- Error message container -->
                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($notificationMessage): ?>
                    <div class="<?php 
                        if (strpos($notificationMessage, 'pending') !== false) {
                            echo 'pending-message';
                        } elseif (strpos($notificationMessage, 'denied') !== false) {
                            echo 'denied-message';
                        }
                    ?>"><?php echo $notificationMessage; ?></div>
                <?php endif; ?>
                
                <!-- username/password input -->
                <div class="input-box username">
                    <img src="images/profile.png" alt="Username Icon" class="icon">
                    <input type="text" id="username" placeholder="Username" name="username">
                </div>
                <div class="input-box password">
                    <img src="images/lock.png" alt="Password Icon" class="icon">
                    <input type="password" id="password" placeholder="Password" name="password">
                    <span class="toggle-password" onclick="togglePassword()"></span>
                </div>
                <div class="button-box">
                    <input type="submit" name="submit" value="Log In"></input>
                </div>

                <!-- create account button -->
                <div class="create-account">
                    <a href="createacc.php">CREATE NEW ACCOUNT</a>
                </div>
            </div>
        </div>
    </form>    
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

    <!-- script -->
    <script src="script.js"></script>
</body>
</html>