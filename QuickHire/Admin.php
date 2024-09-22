<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles/displayCards.css">

<!-- <link rel="stylesheet" href="styles/profile.css"> -->
<?php           //session starting for setting flags for selecting the page to be loade
   session_start();
   if(isset($_SESSION['adminFlag'])) {$flag=$_SESSION['adminFlag'];}
   else {$flag=1;}
   if(isset($_GET['newelderly']) && $_GET['newelderly']=='true') {
    $_SESSION['adminFlag']=1;
    header("Location: " . $_SERVER['PHP_SELF']);
  } elseif(isset($_GET['newfirms']) && $_GET['newfirms']=='true') {
    $_SESSION['adminFlag']=2;
    header("Location: " . $_SERVER['PHP_SELF']);
  } elseif(isset($_GET['allemployee']) && $_GET['allemployee']=='true') {
    $_SESSION['adminFlag']=3;
    header("Location: " . $_SERVER['PHP_SELF']);
  } elseif(isset($_GET['allelderly']) && $_GET['allelderly']=='true') {
    $_SESSION['adminFlag']=4;
    header("Location: " . $_SERVER['PHP_SELF']);
  }
  elseif(isset($_GET['allfirms']) && $_GET['allfirms']=='true') {
    $_SESSION['adminFlag']=5;
    header("Location: " . $_SERVER['PHP_SELF']);
  }
  
?>
<!-- header -->
<nav>
      <div class="nav__header">
        <div class="nav__logo">
          <a href="index.php">QUICK<span>HIRE</span>.</a>
        </div>
        <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="nav__menu__btn" id="menu-btn">
          <span><i class="ri-menu-line"></i></span>
        </div>
      </div>
      <ul class="nav__links" id="nav-links">
        <!-- <li><a href="#">Destination</a></li> -->
  <br>
  <li><a><button name="newelderly" value="true" <?php if($flag==1) echo "class='clor'"; ?> onclick="window.location.href='?newelderly=true'">New Elderly </button></a></li>
  <li><a><button name="newfirms" value="true" <?php if($flag==2) echo "class='clor'"; ?> onclick="window.location.href='?newfirms=true'">New Firms</button></a></li>
  <li><a><button name="allemployee" value="true" <?php if($flag==3) echo "class='clor'"; ?> onclick="window.location.href='?allemployee=true'">All Employees</button></a></li>
  <li><a><button name="allelderly" value="true" <?php if($flag==4) echo "class='clor'"; ?> onclick="window.location.href='?allelderly=true'">All Elderly</button></a></li>
  <li><a><button name="allfirms" value="true" <?php if($flag==5) echo "class='clor'"; ?> onclick="window.location.href='?allfirms=true'">All Firms</button></a></li>
</ul>
    </form>
      <div class="admin">
     Welcome<span><?php  echo " ".$_SESSION['admin'];?></span> 
      </div>
    </nav>
    <!-- background animation -->
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
    <?php
    switch($flag){
          case 1:include 'admin/newelderly.php';break;
          case 2:include 'admin/newfirms.php';break;
          case 3:include 'admin/allemployee.php';break;
          case 4:include 'admin/allelderly.php';break;
          case 5:include 'admin/allfirms.php';break;
    }?>
   <!-- footer -->
<?php include 'footer.php'; ?>
