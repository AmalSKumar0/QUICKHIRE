<!DOCTYPE html>
<html>
<head>
    <link rel="shortcut icon" href="../images/favicon.ico" title="Favicon"/>
    <title>RICKSHAWHUB</title>
    <link rel="stylesheet" type="text/css" href="../styles/LoginReg.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
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
      <li></li>
  </ul>
</div>

<?php
// connecting to database
$conn = mysqli_connect("localhost", "root", "", "rikshawhub");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$err="";
session_start();

// saving the edited data based on the person who is in $_SESSION['whoami']
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function validate_input($data) {
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if (isset($_POST["passenger"]) && $_POST["passenger"] == "submit") {
        $password = validate_input($_POST['password']);
        $conpass = validate_input($_POST['conpassword']);
       if($password==$conpass){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE passenger SET password = ? WHERE pass_id = ?");
        $stmt->bind_param("si", $hashed_password, $_SESSION['uid']);
        if ($stmt->execute()) {
            echo "<script>alert('Password reseted')</script>";
            echo "<script>window.location.href='../profile.php';</script>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
    else{
        $err="Password not matching";
    }
    }

    if (isset($_POST["driver"]) && $_POST["driver"] == "submit") {
        $password = validate_input($_POST['password']);
        $conpass = validate_input($_POST['conpassword']);
       if($password==$conpass){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE driver SET password = ? WHERE driver_id = ?");
        $stmt->bind_param("si", $hashed_password, $_SESSION['did']);
        if ($stmt->execute()) {
            echo "<script>alert('Password reseted')</script>";
            echo "<script>window.location.href='../profile.php';</script>";
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
    else{
        $err="Password not matching";
    }
    }
}

mysqli_close($conn);
?>

<div class="wrapper">
    <div class="inner">
        <div class="image-holder">
            <img src="../images/registration-form.jpg" alt="">
        </div>
        <a href="../profile.php" class="cross"></a>
        <!-- form for eding profile -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
            <h3><?php if ($_SESSION['whoEdit'] == 'pass') echo 'Passenger'; elseif ($_SESSION['whoEdit'] == 'driver') echo 'Driver'; ?> <span>PASSWORD RESET</span></h3>
            <h5 class="error"><?php echo $err; ?></h5>
            <div class="form-wrapper">
                <input type="password" required placeholder="new password"  class="form-control" name="password">
                <i class="zmdi zmdi-card"></i>
            </div>
            <div class="form-wrapper">
                <input type="password" required placeholder="confirm password"  class="form-control" name="conpassword">
                <i class="zmdi zmdi-car"></i>
            </div>
            <div class="buttons">
                <button value="submit" name="<?php if($_SESSION['whoEdit'] == 'pass') echo 'passenger'; elseif ($_SESSION['whoEdit'] == 'driver') echo 'driver'; ?>">Reset Password</button>
            </div>
        </form>
    </div>
</div>
<script>
    let cross = document.querySelector('.cross');
    cross.innerHTML = "\u00d7";
</script>  
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
</body>
</html>
