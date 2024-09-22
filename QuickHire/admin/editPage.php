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
$conn = mysqli_connect("localhost", "root", "", "rikshawhub");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$err="";
session_start();

if ($_SESSION['whoEdit'] == 'pass') {
    $stmt = $conn->prepare("SELECT * FROM passenger WHERE pass_id = ?");
    $stmt->bind_param("s", $_SESSION['uid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $name = $user['name'];
    $email = $user['email'];
    $gender = $user['gender'];
    $address = $user['address'];
    $phone = $user['phone_no'];

} elseif ($_SESSION['whoEdit'] == 'driver') {
    $stmt = $conn->prepare("SELECT * FROM driver WHERE driver_id = ?");
    $stmt->bind_param("s", $_SESSION['did']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $name = $user['name'];
    $email = $user['email'];
    $gender = $user['gender'];
    $address = $user['address'];
    $phone = $user['phone_no'];
    $licence = $user['licence_no'];
    $vehicle = $user['vehicle_no'];
    $img= $user['Auto_img'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function validate_input($data) {
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if (isset($_POST["passenger"]) && $_POST["passenger"] == "submit") {
        $name = validate_input($_POST['name']);
        $email = validate_input($_POST['email']);
        $gender = validate_input($_POST['gender']);
        $address = validate_input($_POST['address']);
        $phone = validate_input($_POST['phone']);
        $stmt = $conn->prepare("UPDATE passenger SET name = ?, email = ?, phone_no = ?, address = ?, gender = ? WHERE pass_id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $address, $gender, $_SESSION['uid']);
        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            echo "<script>alert('Updated successfully')</script>";
            switch ($_SESSION['whoami']) {
                case 'pass':
                    echo "<script>window.location.href='../passenger.php';</script>";
                    break;
                case 'driver':
                    echo "<script>window.location.href='../driver.php';</script>";
                    break;
                case 'admin':
                    echo "<script>window.location.href='../admin.php';</script>";
                    break;
                default:
                    echo "<script>window.location.href='../index.php';</script>";
            }
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }

    if (isset($_POST["driver"]) && $_POST["driver"] == "submit") {
        $name = validate_input($_POST['name']);
        $email = validate_input($_POST['email']);
        $gender = validate_input($_POST['gender']);
        $address = validate_input($_POST['address']);
        $phone = validate_input($_POST['phone']);
        $licence = validate_input($_POST['licence']);
        $vehicle = validate_input($_POST['vehicle']);
        $auto_image = $_FILES['image']['name'];
        if($auto_image){
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if ($check !== false) {
            if (!file_exists($target_file)) {
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            }
        }
    }
    else{
        $auto_image=$img;
    }

        $stmt = $conn->prepare("UPDATE driver SET name = ?, email = ?, phone_no = ?, address = ?, vehicle_no = ?, licence_no = ?, gender = ?, Auto_img = ? WHERE driver_id = ?");
        $stmt->bind_param("ssssssssi", $name, $email, $phone, $address, $vehicle, $licence, $gender, $auto_image, $_SESSION['did']);
        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            echo "<script>alert('Updated successfully')</script>";
            echo "<script>window.location.href='" . ($_SESSION['whoami'] == 'admin' ? "../admin.php" : "../driver.php") . "';</script>";
        } else {
            $err = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

mysqli_close($conn);
?>

<div class="wrapper">
    <div class="inner">
        <div class="image-holder">
            <img src="../images/registration-form.jpg" alt="">
        </div>
        <a href="<?php if($_SESSION['whoami']=='admin') echo '../admin.php'; else echo '../profile.php';?>" class="cross"></a>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
            <h3><?php if ($_SESSION['whoEdit'] == 'pass') echo 'Passenger'; elseif ($_SESSION['whoEdit'] == 'driver') echo 'Driver'; ?> <span>EDIT FORM</span></h3>
            <h5 class="error"><?php echo $err; ?></h5>
            <div class="form-wrapper">
                <input type="text" placeholder="Name" required name="name" value="<?php echo $name; ?>" class="form-control">
                <i class="zmdi zmdi-account"></i>
            </div>
            <div class="form-wrapper">
                <input type="email" required placeholder="Email Address" name="email" value="<?php echo $email; ?>" class="form-control">
                <i class="zmdi zmdi-email"></i>
            </div>
            <div class="form-wrapper">
                <select name="gender" required class="form-control">
                    <option value="" disabled selected>Gender</option>
                    <option value="male" <?php if ($gender == 'male') echo 'selected'; ?>>Male</option>
                    <option value="female" <?php if ($gender == 'female') echo 'selected'; ?>>Female</option>
                    <option value="other" <?php if ($gender == 'other') echo 'selected'; ?>>Other</option>
                </select>
                <i class="zmdi zmdi-caret-down" style="font-size: 17px"></i>
            </div>
            <div class="form-wrapper">
                <input type="text" required placeholder="Address" name="address" value="<?php echo $address; ?>" class="form-control">
                <i class="zmdi zmdi-lock"></i>
            </div>
            <div class="form-wrapper">
                <input type="tel" required placeholder="Phone number" name="phone" value="<?php echo $phone; ?>" class="form-control">
                <i class="zmdi zmdi-lock"></i>
            </div>
            <?php if ($_SESSION['whoEdit'] == 'driver') { ?>
            <div class="form-wrapper">
                <input type="text" required placeholder="Licence number" value="<?php echo $licence; ?>" class="form-control" name="licence">
                <i class="zmdi zmdi-card"></i>
            </div>
            <div class="form-wrapper">
                <input type="text" required placeholder="Vehicle number" value="<?php echo $vehicle; ?>" class="form-control" name="vehicle">
                <i class="zmdi zmdi-car"></i>
            </div>
            <div class="form-wrapper">
                <label for="imageUpload">Image of the auto:</label>
                <input type="file" id="imageUpload" name="image" accept="image/*">
                <i class="zmdi zmdi-camera"></i>
            </div>
            <?php } ?>
            <div class="buttons">
                <button value="submit" name="<?php if($_SESSION['whoEdit'] == 'pass') echo 'passenger'; elseif ($_SESSION['whoEdit'] == 'driver') echo 'driver'; ?>">Confirm</button>
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
