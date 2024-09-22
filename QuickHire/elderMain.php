<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles/passenger.css">
<link rel="stylesheet" href="styles/displayCards.css">
<link rel="stylesheet" href="styles/DisplayBox.css">
<!-- landing page of our website -->

<?php          //session starting for setting flags for selecting the page to be loade
session_start();
if (isset($_SESSION['elderFlag'])) {
    $flag = $_SESSION['elderFlag'];
} else {
    $flag = 1;
}
if (isset($_GET['post']) && $_GET['post'] == 'true') {
    $_SESSION['elderFlag'] = 1;
    header("Location: " . $_SERVER['PHP_SELF']);
} elseif (isset($_GET['allpost']) && $_GET['allpost'] == 'true') {
    $_SESSION['elderFlag'] = 2;
    header("Location: " . $_SERVER['PHP_SELF']);
} elseif (isset($_GET['requests']) && $_GET['requests'] == 'true') {
    $_SESSION['elderFlag'] = 3;
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
  <li><a><button name="post" value="true" <?php if($flag==1) echo "style='color: #296bfa;'"; ?> onclick="window.location.href='?newelderly=true'">Post Job </button></a></li>
  <li><a><button name="allpost" value="true" <?php if($flag==2) echo "style='color: #296bfa;'"; ?> onclick="window.location.href='?newfirms=true'">All Posts</button></a></li>
  <li><a><button name="requests" value="true" <?php if($flag==3) echo "style='color: #296bfa;'"; ?> onclick="window.location.href='?allemployee=true'">Requests</button></a></li>
  </ul>
    </form>
    <div class="admin"><a href="profile.php">
            <span><?php echo " " . strtoupper($_SESSION['elderlyname']); ?></span> </a>
    </div>
</nav>


<div class="booking-section into">
    <?php
switch($flag){
          case 1:include 'Elder/formToview.php';break;
          case 2:include 'Elder/posts.php';break;
          case 3:include 'Elder/Requests.php';break;
    }?>
</div>

<?php include 'footer.php'; ?>