<?php include 'header.php'; ?>
<!-- landing page of our website -->
    <nav>
      <div class="nav__header">
        <div class="nav__logo">
          <a href="#">QUICK<span>HIRE</span>.</a>
        </div>
        <div class="nav__menu__btn" id="menu-btn">
          <span><i class="ri-menu-line"></i></span>
        </div>
      </div>
      <ul class="nav__links" id="nav-links">
        <!-- <li><a href="#">Destination</a></li> -->
        <li><a href="#" style="color: #296bfa;">Home</a></li>
        <li><a href="#" >About Us</a></li>
        <li><a href="#" id="testimoniesButton">Reviews</a></li>
        <li><a href="#" id="aboutusButton">Contact us</a></li>
      </ul>
      <div class="nav__btns">
      <a href="parttimeLog.php" class="btn sign__in">I'M PART TIMER</a>
      </div>
    </nav>
    <header class="header__container">
      <div class="header__image">
        <div class="header__image__card header__image__card-1">
          <span><i class="ri-key-line"></i></span>
          User friendly
        </div>
        <div class="header__image__card header__image__card-2">
          <style>
            .hiddenadm:link, .hiddenadm:visited {
    color: inherit; /* or set your desired color here */
    text-decoration: none; /* optional, if you want to remove the underline */
}
          </style>
          <span><a href="adminLog.php" class="hiddenadm"><i class="ri-passport-line"></i></a></span>
          Affordable
        </div>
        <div class="header__image__card header__image__card-3">
          <span><i class="ri-map-2-line"></i></span>
          Anywhere
        </div>
        <div class="header__image__card header__image__card-4">
          <span><i class="ri-guide-line"></i></span>
          On time
        </div>
        <img class="auto-pic" src="assets/header.png" alt="header" />
      </div>
      <div class="header__content">
      <h1>LET’S GO!<br /><span>QUICK</span> <span>HIRES</span> FOR<br> A BETTER TOMORROW</h1>
        <p>
        Find Help Fast and Make a Difference!<br>
Connect with reliable workers for your business needs or provide companionship for seniors.
Efficient, compassionate services that improve lives every day!
        </p>
        <div class="container">
          <div class="input__row">
            <a class="passenger" id="passengerBtn" href="elderlyReg.php">ELDERLY SERVICE</a><a class="driver" id="driverBtn" href="firmReg.php">FIRM SERVICE</a>
          </div>
        </div>
      </div>
    </header>
    <br><br><br><br>
     <!--Testimonials-->
     <aside id="testimonials" class="scrollto text-center" data-enllax-ratio=".2">

      <div class="row clearfix" id="testimoniesSection">

          <div class="section-heading">
              <h3>FEEDBACK</h3>
              <h2 class="section-title">What our customers are saying</h2>
          </div>
        <div class="testcon">
          <!--User Testimonial-->
          <blockquote class="col-3 testimonial classic john" >
              <img class="imgu" src="assets/user-images/user-1.jpg" alt="User"/>
              <q>This service is fantastic! It's easy to use, and the workers are reliable, whether you need quick help for your business or a friendly companion for a loved one. Highly recommend it for anyone looking for fast, dependable support!</q>
              <footer>John Doe - Happy Customer</footer>
          </blockquote>
          <!-- End of Testimonial-->

          <!--User Testimonial-->
          <blockquote class="col-3 testimonial classic user2">
              <img class="imgu" src="assets/user-images/user-2.jpg" alt="User"/>
              <q>I've tried several hiring services, but this one stands out. The interface is clean, and finding the right help is seamless. Plus, the workers are friendly and dependable. Highly recommend it!</q>
              <footer>Emily Johnson - Happy Customer</footer>
          </blockquote>
          <!-- End of Testimonial-->

          <!--User Testimonial-->
          <blockquote class="col-3 testimonial classic user3">
              <img class="imgu" src="assets/user-images/user-3.jpg" alt="User"/>
              <q>Great platform with a user-friendly design! I love how quickly I can find help or a companion and see updates in real-time. It’s made managing daily needs so much easier!</q>
              <footer>John Smith - Happy Customer</footer>
          </blockquote>
        </div>
          <!-- End of Testimonial-->
 
      </div>
      <br><br><br><br>
  </aside>

  <!--End of Testimonials-->
  <?php include 'footer.php'; ?>