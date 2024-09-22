<!DOCTYPE html>
<html>
<head>
	<title>Admin</title>
    <link rel="shortcut icon" href="images/favicon.ico" title="Favicon"/>
	<link rel="stylesheet" type="text/css" href="styles/LoginReg.css">
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>
<?php
 $err="";
$conn = mysqli_connect("localhost", "root", "", "rikshawhub");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
session_start();

// login for the admin 
if (isset($_POST["login"]) && $_POST["login"] == "submit"){
    $email = isset($_POST['logmail']) ? htmlspecialchars(trim($_POST['logmail'])) : '';
    $password = isset($_POST['logpass']) ? $_POST['logpass'] : '';
    // fetching password from admintable for varification
    $stmt = $conn->prepare("SELECT password FROM admintable WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 0) {
        $err= "User not found";
    } else {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        
        if ($password==$db_password) {
            $stmt = $conn->prepare("SELECT name FROM admintable WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($adminName);
            $stmt->fetch();
            $_SESSION['admin']=$adminName;
            // if password match login the admin
            echo '<script>window.location.href="Admin.php";</script>';
        } else {
            $err= "Incorrect password";
        }
        $stmt->close();
    }
}

mysqli_close($conn);
?>

	<body>
<!-- animation -->
<div class="area" >
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
</div >
        <div class="wrapper" >
        <div class="inner">
        <div class="image-holder">
        <img src="images/registration-form.jpg" alt="">
        </div>
        <!-- login form -->
        <a href="index.php" class="cross"></a>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login">
            <h3 >Admin <span>Login</span></h3>
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
            <button fdprocessedid="lfyluc" value="submit" name="login">Login
            </button>
            </div>
            </form>
        </div>
        </div>
        <script>
            let cross = document.querySelector('.cross');
            cross.innerHTML = "\u00d7";
        </script>
        <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>   
</body>
</html>