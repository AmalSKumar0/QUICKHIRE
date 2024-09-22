<!DOCTYPE html>
<html>

<head>
    <title>Driver Registration</title>
    <link rel="shortcut icon" href="images/favicon.ico" title="Favicon" />
    <link rel="stylesheet" type="text/css" href="styles/LoginReg.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<?php
session_start();
$err = ""; //error variable to show error in our input field
//connecting to our database
$conn = mysqli_connect("localhost", "root", "", "quickhire");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

//if the driver registered his details
//it will be stored in temporary database table for the admin to view and accept or reject

if (isset($_POST["Register"]) && $_POST["Register"] == "submit") {

    // Handle password and confirm password
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm']) ? $_POST['confirm'] : '';

    if ($password !== $confirm_password) {
        $err = "Passwords do not match.";
    } else {
        // Get form data and sanitize input
        $firm_name = isset($_POST['bname']) ? htmlspecialchars(trim($_POST['bname'])) : '';
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
        $contact_name = isset($_POST['cname']) ? htmlspecialchars(trim($_POST['cname'])) : '';
        $contact_phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
        $industry_type = isset($_POST['Btype']) ? htmlspecialchars(trim($_POST['Btype'])) : '';
        $number_of_employees = isset($_POST['empno']) ? intval($_POST['empno']) : 0;


        $sql = $conn->prepare("select email from firms where email = ? union select email from firms_registration  where email = ? union select email from elderly where email = ? union select email from elderly_registration where email = ? union select email from temporary_employee where email = ?");
        $sql->bind_param("sssss", $email, $email, $email, $email, $email);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->fetch_assoc() > 0) {
            $err = "Email already exist";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Handle image upload
            if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $target_dir = "uploads/"; // Directory where the images will be stored
                $image_name = basename($_FILES['image']['name']);
                $target_file = $target_dir . uniqid() . "_" . $image_name; // Unique file name

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Prepare SQL statement to insert data into firms_registration table
                    $stmt = $conn->prepare("INSERT INTO firms_registration 
                    (firm_id, firm_name, email, password, contact_name, contact_phone, address, industry_type, number_of_employees, profile_image, registration_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

                    // Generate a new firm ID starting with 'F'
                    $stmt_id = $conn->prepare("SELECT CONCAT('F', LPAD(IFNULL(MAX(SUBSTR(firm_id, 2)) + 1, 1), 3, '0')) AS next_id FROM firms_registration");
                    $stmt_id->execute();
                    $stmt_id->bind_result($next_firm_id);
                    $stmt_id->fetch();
                    $stmt_id->close();

                    // Bind parameters for the prepared statement
                    $stmt->bind_param("ssssssssis", $next_firm_id, $firm_name, $email, $hashed_password, $contact_name, $contact_phone, $address, $industry_type, $number_of_employees, $target_file);

                    // Execute the prepared statement
                    if ($stmt->execute()) {
                        echo "<script>alert('Firm registration successful! Pleas wait till admin lets you in');</script>";
                    } else {
                        $err = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $err = "Failed to upload the image.";
                }
            } else {
                $err = "Profile image is required.";
            }
        }
    }
}


// if the user trying to login
if (isset($_POST["login"]) && $_POST["login"] == "submit") {
    $email = isset($_POST['logmail']) ? htmlspecialchars(trim($_POST['logmail'])) : '';
    $password = isset($_POST['logpass']) ? $_POST['logpass'] : '';

    $stmt = $conn->prepare("SELECT password FROM firms WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $err = "User not found";
    } else {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // Verify the hashed password
        if (password_verify($password, $db_password)) {
            $stmt = $conn->prepare("SELECT * FROM firms WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $_SESSION['firm_name'] = $user['firm_name'];
            $_SESSION['firm_id'] = $user['firm_id'];
            // $_SESSION['did'] = $user['driver_id'];
            echo '<script>
    window.location.href = "firmMain.php";
</script>';
        } else {
            $err = "Incorrect password";
        }
        $stmt->close();
    }
}

mysqli_close($conn);
?>

<body>
    <!-- animation for the view -->
    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <div class="wrapper">
        <div class="inner">
            <div class="image-holder">
                <img src="images/registration-form.jpg" alt="">
            </div>
            <a href="index.php" class="cross"></a>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                enctype="multipart/form-data" class="disable">
                <h3>Firm <span>Registration</span></h3>
                <div class="form-wrapper">
                    <input type="text" placeholder="Business Name" class="form-control" name="bname" required>
                    <i class="zmdi zmdi-account"></i>
                </div>
                <div class="form-wrapper">
                    <input type="email" placeholder="Email Address" class="form-control" name="email" required>
                    <i class="zmdi zmdi-email"></i>
                </div>
                <div class="form-wrapper">
                    <input type="text" placeholder="Contact Name" class="form-control" name="cname" required>
                    <i class="zmdi zmdi-account"></i>
                </div>
                <div class="form-wrapper">
                    <input type="tel" required placeholder="Phone number" class="form-control" name="phone">
                    <i class="zmdi zmdi-phone"></i>
                </div>
                <div class="form-wrapper">
                    <input type="text" required placeholder="Address" class="form-control" name="address">
                    <i class="zmdi zmdi-home"></i>
                </div>
                <div class="form-wrapper">
                    <input type="text" required placeholder="Industry Type" class="form-control" name="Btype">
                    <i class="zmdi zmdi-home"></i>
                </div>

                <div class="form-wrapper">
                    <input type="number" required placeholder="Number of employee" class="form-control" name="empno">
                    <i class="zmdi zmdi-card"></i>
                </div>

                <div class="form-wrapper">
                    <label for="imageUpload">Image of the firm:</label>
                    <input type="file" id="imageUpload" name="image" accept="image/*" required>
                    <i class="zmdi zmdi-camera"></i>
                </div>
                <div class="form-wrapper">
                    <input type="password" placeholder="Password" class="form-control" name="password" required>
                    <input type="password" placeholder="Confirm Password" class="form-control" name="confirm" required>
                    <i class="zmdi zmdi-lock"></i>
                </div>
                <h5 class="error"><?php echo $err; ?></h5>
                <div class="buttons">
                    <button value="submit" name="Register">Register</button>
                    <div class="logbtn">Login</div>
                </div>
            </form>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login">
                <h3>Firm <span>Login</span></h3>
                <div class="form-wrapper">
                    <input type="email" placeholder="Email Address" class="form-control" name="logmail" required>
                    <i class="zmdi zmdi-email"></i>
                </div>
                <div class="form-wrapper">
                    <input type="password" placeholder="Password" class="form-control" name="logpass" required>
                    <i class="zmdi zmdi-lock"></i>
                </div>
                <h5 class="error"><?php echo $err; ?></h5>
                <div class="buttons">
                    <button value="submit" name="login">Login</button>
                    <div class="regbtn">Register</div>
                </div>
            </form>
        </div>
    </div>
    <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
    <script src='scripts/reg.js'></script>
</body>

</html>