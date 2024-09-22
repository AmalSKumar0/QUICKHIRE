<!DOCTYPE html>
<html>

<head>
    <title>Employee Registration</title>
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

$err = "";

// Handle registration
if (isset($_POST["Register"]) && $_POST["Register"] == "submit") {
    // Handle password and confirm password
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm']) ? $_POST['confirm'] : '';

    if ($password !== $confirm_password) {
        $err = "Passwords do not match.";
    } else {
        // Get form data and sanitize input
        $emp_name = htmlspecialchars(trim($_POST['ename']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone_number = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));
        $work_experience = htmlspecialchars(trim($_POST['exp']));
        $availability = htmlspecialchars(trim($_POST['availability']));
        $hourly_wage = floatval($_POST['wage']);

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = $conn->prepare("select email from firms where email = ? union select email from firms_registration  where email = ? union select email from elderly where email = ? union select email from elderly_registration where email = ? union select email from temporary_employee where email = ?");
        $sql->bind_param("sssss", $email, $email, $email, $email, $email);
        $sql->execute();
        $result = $sql->get_result();
        if ($result->fetch_assoc() > 0) {
            $err = "Email already exist";
        } else {

            // Handle image upload
            if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $target_dir = "uploads/"; // Directory where the images will be stored
                $image_name = basename($_FILES['image']['name']);
                $target_file = $target_dir . uniqid() . "_" . $image_name; // Unique file name

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Prepare SQL statement to insert data into temporary_employee table
                    $stmt = $conn->prepare("INSERT INTO temporary_employee (Employee_id, profile_image, Emp_name, email, password, phone_number, address, work_experience, availability, hourly_wage, is_qualified, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, FALSE, NOW())");

                    // Generate a new employee ID starting with 'E'
                    $stmt_id = $conn->prepare("SELECT CONCAT('E', LPAD(IFNULL(MAX(SUBSTR(Employee_id, 2)) + 1, 1), 3, '0')) AS next_id FROM temporary_employee");
                    $stmt_id->execute();
                    $stmt_id->bind_result($next_emp_id);
                    $stmt_id->fetch();
                    $stmt_id->close();

                    // Bind parameters for the prepared statement
                    $stmt->bind_param("ssssssssss", $next_emp_id, $target_file, $emp_name, $email, $hashed_password, $phone_number, $address, $work_experience, $availability, $hourly_wage);
                    $_SESSION['Employee_id'] = $next_emp_id;

                    // Execute the prepared statement
                    if ($stmt->execute()) {
                        $_SESSION['Employee_name'] = $emp_name;
                        $_SESSION['Employee_id'] = $next_emp_id;
                        echo '<script>window.location.href = "quiz.php";</script>';
                    } else {
                        $err = "Error: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $err = "Profile image is required.";
            }
        }
    }
}

// Handle login
if (isset($_POST["login"]) && $_POST["login"] == "submit") {
    $email = htmlspecialchars(trim($_POST['logmail']));
    $password = $_POST['logpass'];

    $stmt = $conn->prepare("SELECT password FROM temporary_employee WHERE email = ?");
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
            $stmt = $conn->prepare("SELECT * FROM temporary_employee WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $_SESSION['Employee_name'] = $user['Emp_name'];
            $_SESSION['Employee_id'] = $user['Employee_id'];

            echo '<script>window.location.href = "' . (!$user['is_qualified'] ? 'quiz.php' : 'employeeMain.php') . '";</script>';
        } else {
            $err = "Incorrect password";
        }
        $stmt->close();
    }
}

mysqli_close($conn);
?>

<body>
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
                <h3>Employee <span>Registration</span></h3>
                <div class="form-wrapper">
                    <input type="text" placeholder="Name" class="form-control" name="ename" required>
                    <i class="zmdi zmdi-account"></i>
                </div>
                <div class="form-wrapper">
                    <label for="imageUpload">Profile Picture:</label>
                    <input type="file" id="imageUpload" name="image" accept="image/*" required>
                    <i class="zmdi zmdi-camera"></i>
                </div>
                <div class="form-wrapper">
                    <input type="email" placeholder="Email Address" class="form-control" name="email" required>
                    <i class="zmdi zmdi-email"></i>
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
                    <input type="number" required placeholder="Years of experience" class="form-control" name="exp">
                    <i class="zmdi zmdi-card"></i>
                </div>
                <div class="form-wrapper">
                    <input type="text" required placeholder="Availability" class="form-control" name="availability">
                    <i class="zmdi zmdi-card"></i>
                </div>
                <div class="form-wrapper">
                    <input type="number" required placeholder="Hourly wage" class="form-control" name="wage">
                    <i class="zmdi zmdi-card"></i>
                </div>
                <div class="form-wrapper">
                    <input type="password" placeholder="Password" class="form-control" name="password" required>
                    <input type="password" placeholder="Confirm Password" class="form-control" name="confirm" required>
                    <i class="zmdi zmdi-lock"></i>
                </div>
                <h5 class="error"><?php echo $err; ?></h5>
                <div class="buttons">
                    <button type="submit" name="Register" value="submit">Register</button>
                    <div class="logbtn">Login</div>
                </div>
            </form>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login">
                <h3>Employee <span>Login</span></h3>
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
                    <button type="submit" name="login" value="submit">Login</button>
                    <div class="regbtn">Register</div>
                </div>
            </form>
        </div>
    </div>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
    <script src='scripts/reg.js'></script>
</body>