<?php include 'header.php'; ?>
<link rel="stylesheet" href="styles/passenger.css">
<link rel="stylesheet" href="styles/displayCards.css">
<link rel="stylesheet" href="styles/DisplayBox.css">
<!-- landing page of our website -->

<?php session_start(); ?>
<?php
$conn = mysqli_connect("localhost", "root", "", "quickhire");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle quiz submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correct_answers = 3; // Set the number of correct answers needed to pass
    $score = 0;

    // Check quiz answers
    if (isset($_POST['q1']) && $_POST['q1'] == 'option1') $score++;
    if (isset($_POST['q2']) && $_POST['q2'] == 'option3') $score++;
    if (isset($_POST['q3']) && $_POST['q3'] == 'option2') $score++;

    // Check if employee passed
    $is_qualified = $score >= $correct_answers ? 1 : 0;

    // Update the employee's qualification status
    $employee_id = $_SESSION['Employee_id']; // Assuming the employee ID is stored in session
    $stmt = $conn->prepare("UPDATE temporary_employee SET is_qualified = ? WHERE Employee_id = ?");
    $stmt->bind_param("is", $is_qualified, $employee_id);
    $stmt->execute();
    $stmt->close();

    // Redirect based on the result
    if ($is_qualified) {
        echo "<script>alert('Congratulations! You are qualified.');</script>";
        echo '<script> window.location.href = "employeeMain.php"; </script>';
    } else {
        echo "<script>alert('Sorry, you did not pass. Please try again.');</script>";
    }
}
?>

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
        <li><a href="index.php" >Home</a></li>
        <li><a href="#">About Us</a></li>
        <li><a href="#" id="testimoniesButton">Reviews</a></li>
        <li><a href="#" id="aboutusButton">Contact us</a></li>
    </ul>
    <div class="admin"><a href="profile.php">
     <span><?php  echo " ".$_SESSION['Employee_name'];?></span> </a>
      </div>
</nav>
<style>
    /* General styling */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        /* background-color: #296bfa; */
        color: #333;
        text-align: center;
    }

    .section {
        display: none;
        /* Initially hide all sections */
    }

    .active {
        display: block;
        /* Show active section */
    }

    /* Button container for section navigation */
    .stage-buttons-container {
        position: absolute;

        display: flex;
        justify-content: center;
        margin: 20px 0;
        top: 20%;
        left: 30px;
        gap: 40px;
        flex-direction: column;
    }

    .stage-button {
        color: white;
        width: 130px;
        padding: 10px 20px;
        margin: 0 10px;
        border: 2px solid #ccc;
        background-color: #296bfa;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .stage-button:hover {
        background-color: #ddd;
    }

    .stage-button.active {
        background-color: #2344d9;
        /* Highlight active stage */
        color: white;
        border-color: #2344d9;
        ;
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        .stage-buttons-container {
            flex-direction: column;
        }

        .stage-button {
            width: 100%;
            margin: 5px 0;
        }
    }

    .searchcard {
        width: 1000px;
        height: 500px;
    }

    .searchcard-content {
        left: -60px;
    }

    .searchcard-image {
        max-width: 50px;
        background-color: white;
    }

    .scroll-container {
        max-height: 300px;
        width: 120%;
        /* Adjust height as needed */
        overflow-x: scroll;
        padding: 20px;
    }

    .searchcard-title {
        margin-bottom: 20px;
    }
</style>
<!-- <h1></h1> -->

<!-- Buttons to navigate sections -->

<div class="booking-section into">
<h1 class="TagLine">Welcome to Employee Training </h1>
    <div class="cardCont into ">
        <div class="searchcard">
            <div class="searchcard-image">
                <div class="stage-buttons-container">
                    <button class="stage-button" onclick="goToSection(0)">Video 1</button>
                    <button class="stage-button" onclick="goToSection(1)">Video 2</button>
                    <button class="stage-button" onclick="goToSection(2)">Quiz</button>
                </div>
            </div>
            <div class="searchcard-content special-card">
                <div class="section active" id="section1">
                    <h2>Training Video 1</h2>
                    <video width="600" controls>
                        <source src="training_videos/video1.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="section" id="section2">
                    <h2>Training Video 2</h2>
                    <video width="600" controls>
                        <source src="training_videos/video2.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="section" id="section3">
                    <h2>Quiz</h2>
                    <div class="scroll-container">
                        <form method="post" action="">
                            <p>1. What is the first rule of safety?</p>
                            <label><input type="radio" name="q1" value="option1"> Follow all safety protocols</label><br>
                            <label><input type="radio" name="q1" value="option2"> Ignore warnings</label><br>
                            <label><input type="radio" name="q1" value="option3"> Only worry about major issues</label><br>

                            <p>2. How often should equipment be inspected?</p>
                            <label><input type="radio" name="q2" value="option1"> Once a year</label><br>
                            <label><input type="radio" name="q2" value="option2"> Never</label><br>
                            <label><input type="radio" name="q2" value="option3"> Regularly</label><br>

                            <p>3. What is the most important part of teamwork?</p>
                            <label><input type="radio" name="q3" value="option1"> Working alone</label><br>
                            <label><input type="radio" name="q3" value="option2"> Communication</label><br>
                            <label><input type="radio" name="q3" value="option3"> Ignoring others</label><br>

                            <input type="submit" value="Submit Quiz">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let currentSection = 0; // Initialize current section
    const totalSections = 3; // Total number of sections (adjust this if you add more sections)

    function changeSection(n) {
        const sections = document.querySelectorAll('.section');
        const buttons = document.querySelectorAll('.stage-button');

        // Hide current section and remove active class from the button
        sections[currentSection].classList.remove('active');
        buttons[currentSection].classList.remove('active');

        // Update the current section index
        currentSection = (currentSection + n + sections.length) % sections.length;

        // Show the new section and highlight the current stage button
        sections[currentSection].classList.add('active');
        buttons[currentSection].classList.add('active');
    }

    // Go to a specific section
    function goToSection(sectionIndex) {
        const sections = document.querySelectorAll('.section');
        const buttons = document.querySelectorAll('.stage-button');

        // Hide current section and remove active class from the button
        sections[currentSection].classList.remove('active');
        buttons[currentSection].classList.remove('active');

        // Update the current section index to the clicked section
        currentSection = sectionIndex;

        // Show the new section and highlight the corresponding button
        sections[currentSection].classList.add('active');
        buttons[currentSection].classList.add('active');
    }

    // Initially show the first section and highlight the first button
    document.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('.section');
        const buttons = document.querySelectorAll('.stage-button');

        sections[0].classList.add('active');
        buttons[0].classList.add('active');
    });
</script>

<?php include 'footer.php'; ?>