<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles/passenger.css">
<link rel="stylesheet" href="styles/displayCards.css">
<link rel="stylesheet" href="styles/DisplayBox.css">
<!-- landing page of our website -->

<?php          //session starting for setting flags for selecting the page to be loade
session_start();
if (isset($_SESSION['empFlag'])) {
    $flag = $_SESSION['empFlag'];
} else {
    $flag = 1;
}
if (isset($_GET['searches']) && $_GET['searches'] == 'true') {
    $_SESSION['empFlag'] = 1;
    header("Location: " . $_SERVER['PHP_SELF']);
} elseif (isset($_GET['request']) && $_GET['request'] == 'true') {
    $_SESSION['empFlag'] = 2;
    header("Location: " . $_SERVER['PHP_SELF']);
} elseif (isset($_GET['current']) && $_GET['current'] == 'true') {
    $_SESSION['empFlag'] = 3;
    header("Location: " . $_SERVER['PHP_SELF']);
} 

$conn = mysqli_connect("localhost", "root", "", "quickhire");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<nav>
      <div class="nav__header">
        <div class="nav__logo">
          <a href="#">QUICK<span>HIRE</span>.</a>
        </div>
<form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="nav__menu__btn" id="menu-btn">
          <span><i class="ri-menu-line"></i></span>
        </div>
      </div>
      <ul class="nav__links" id="nav-links">
        <!-- <li><a href="#">Destination</a></li> -->
  <br>
  <li><a href="index.php" >Home</a></li>
  <li><a><button name="searches" value="true" <?php if($flag==1) echo "style='color: #296bfa;'"; ?> onclick="window.location.href='?searches=true'">Search </button></a></li>
  <li><a><button name="request" value="true" <?php if($flag==2) echo "style='color: #296bfa;'"; ?> onclick="window.location.href='?request=true'">Requests</button></a></li>
  <li><a><button name="current" value="true" <?php if($flag==3) echo "style='color: #296bfa;'"; ?> onclick="window.location.href='?current=true'">Current job</button></a></li>
  </ul>
    </form>
    <div class="admin"><a href="profile.php">
            <span><?php echo " " . strtoupper($_SESSION['Employee_name']); ?></span> </a>
    </div>
</nav>


<div class="booking-section into">
    <?php
switch($flag){
          case 1:include 'Employee/formToview.php';break;
          case 3:include 'Employee/posts.php';break;
          case 2:include 'Employee/Requests.php';break;
    }?>
</div>

<?php include 'footer.php'; ?>
