<?php
if (isset($_POST["postJob"])) {

    // Sanitize and validate form input
    $firm_id = $_SESSION['firm_id'];
    $job_title = $_POST['jobTitle'];
    $job_location = $_POST['jobLocation'];
    $landmark = $_POST['landmark'];
    $job_description = $_POST['jobDescription'];
    $wage = $_POST['wage'];
    $job_timing = $_POST['jobTiming'];
    $job_post_type = $_POST['jobPostType'];
    $job_date = $_POST['jobDate'];

    // Set job status to 'open' by default
    $job_status = 'open';


    // Prepare SQL statement
    $sql = "INSERT INTO job_postings (firm_id, job_title, job_location, landmark, job_description, wage, job_timing, job_post_type, job_date, job_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the statement
        $stmt->bind_param("issssdssss", $firm_id, $job_title, $job_location, $landmark, $job_description, $wage, $job_timing, $job_post_type, $job_date, $job_status);

        // Execute the statement
        if ($stmt->execute()) {
            echo '<script>window.location.href="firmMain.php";</script>';
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
        height: 450px;
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
    POST A <span class='ride'>JOB</span>
</h1>
<div class="cardCont">
    <div class="searchcard" >

        <!-- Form for posting a job with added fields for wage, timing, post type, and date -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form">
                <div class="formSearch">
                    <div class="jobDetails">
                        <div class="form-group">
                            <label for="jobTitle">Job Title</label>
                            <input type="text" id="jobTitle" name="jobTitle" placeholder="Enter job title" required>
                        </div>
                        <div class="form-group">
                            <label for="jobLocation">Location</label>
                            <input type="text" id="autocompleteJobLocation" name="jobLocation" placeholder="Select job location" required>
                            <div id="jobLocationSuggestions"></div>
                        </div>

                        <div class="form-group">
                            <label for="landmark">Landmark</label>
                            <input type="text" name="landmark" id="landmark" placeholder="Enter landmark near location" required>
                        </div>

                        <div class="form-group">
                            <label for="jobDescription">Job Description</label>
                            <textarea width="200px" name="jobDescription" id="jobDescription" placeholder="Provide job details" required></textarea>
                        </div>
                    </div>

                    <!-- Right-side form elements -->
                    <div class="jobDetails">
                        <div class="form-group">
                            <label for="wage">Wage</label>
                            <input type="number" name="wage" id="wage" placeholder="Enter wage (per day)" required>
                        </div>

                        <div class="form-group">
                            <label for="jobTiming">Job Timing</label>
                            <input type="text" name="jobTiming" id="jobTiming" placeholder="Enter job timings (e.g., 9am - 5pm)" required>
                        </div>

                        <div class="form-group">
                            <label for="jobPostType">Post Type</label>
                            <select name="jobPostType" id="jobPostType" required>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                                <option value="temporary">Temporary</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="jobDate">Job Date</label>
                            <input type="date" name="jobDate" id="jobDate" required>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>

            <div class="find-button">
                <!-- Post job button will submit the job posting -->
                <button value="postJob" name="postJob" class="postJob">POST JOB</button>
            </div>
        </form>

    </div>
</div>
