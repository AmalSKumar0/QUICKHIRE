<?php
if (isset($_POST["postJob"])) {

    // Sanitize and validate form input
    $elderly_id = $_SESSION['elderly_id'];  // Assuming the elderly person's ID is stored in session
    $job_title = $_POST['jobTitle'];
    $job_description = $_POST['jobDescription'];
    $job_date = $_POST['jobDate'];
    $job_time = $_POST['jobTime'];
    $wage = $_POST['wage'];
    $loc = $_POST['jobLoc'];
    // Set job status to 'pending' by default
    $job_status = 'pending';

    // Prepare SQL statement
    $sql = "INSERT INTO elderly_jobs (elderly_id, job_title, job_description, job_date, job_time, wage, job_status,job_location)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the statement
        $stmt->bind_param("ssssssss", $elderly_id, $job_title, $job_description, $job_date, $job_time, $wage, $job_status,$loc);

        // Execute the statement
        if ($stmt->execute()) {
            echo '<script>window.location.href="elderMain.php";</script>';
        } else {
            echo "Error: " . $stmt->error;
        }
        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<style>
    .searchcard{
        height: 380px;
    }
    .form{
        position: relative;
        left: 13%;
    }
    .formSearch{
        gap: 20px;
        width: 80%;
    }
    .jobDetails{
        width: 60%;
    }
</style>

<!-- views based on the flags we mensioned before -->
<h1 class="TagLine">
    POST A <span class='ride'>SERVICE REQUEST</span>
</h1>
<div class="cardCont">
    <div class="searchcard" >
        <!-- Form for posting a service request -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form">
                <div class="formSearch">
                    <div class="jobDetails">
                        <div class="form-group">
                            <label for="jobTitle">Service Title</label>
                            <input type="text" id="jobTitle" name="jobTitle" placeholder="e.g., Shopping Assistance, Dinner" required>
                        </div>
                        <div class="form-group">
                            <label for="joblocation">Location</label>
                            <input type="text" id="jobTitle" name="jobLoc" placeholder="Location" required>
                        </div>
                        <div class="form-group">
                            <label for="jobDescription">Service Description</label>
                            <textarea width="200px" name="jobDescription" id="jobDescription" placeholder="Describe the service" required></textarea>
                        </div>
                    </div>

                    <!-- Right-side form elements -->
                    <div class="jobDetails">
                        <div class="form-group">
                            <label for="wage">Wage</label>
                            <input type="number" name="wage" id="wage" placeholder="Offered wage for the service" required>
                        </div>
                        <div class="form-group">
                            <label for="jobDate">Service Date</label>
                            <input type="date" name="jobDate" id="jobDate" required>
                        </div>

                        <div class="form-group">
                            <label for="jobTime">Service Time</label>
                            <input type="time" name="jobTime" id="jobTime" required>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>

            <div class="find-button">
                <!-- Post job button will submit the job posting -->
                <button value="postJob" name="postJob" class="postJob">POST SERVICE REQUEST</button>
            </div>
        </form>
    </div>
</div>
