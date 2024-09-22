<link rel="stylesheet" href="Employee/job.css">
<h1 class="TagLine">BE THE <span class='ride'>DIFFERENCE</span></h1>

<div class="cardCont ">
    <div class="searchcard">
        <div class="searchcard-content">
            <h2 class="searchcard-title">SEARCH A JOB THAT SUITS YOUR INTEREST!</h2>
            <form class="form" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="formsection">
                    <div class="form-group">
                        <label for="jobLocation">Enter Your Location:</label>
                        <input type="text" id="autocompleteJobLocation" name="jobLocation" placeholder="Enter your location" required>
                        <div id="locationSuggestions"></div>
                    </div>

                    <div class="form-group">
                        <label for="jobType">Select Job Type:</label>
                        <select name="jobType" id="jobType" required>
                            <option value="firm">Firm Jobs</option>
                            <option value="elderly">Elderly Assistance Jobs</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                </div>
                <div class="find-button sebtn">
                    <button class="searchbtn" value="true" type="submit" name="searchJobs">Search Jobs</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<?php
// Handle the job application request
if (isset($_GET['apply'])) {
    // Get employee ID from session
    $employee_id = $_SESSION['Employee_id'];

    // Get job_id and job_type from the form
    $job_id = htmlspecialchars($_GET['job_id']);
    $job_type = htmlspecialchars($_GET['job_type']);

    // Prepare SQL query to insert job request into job_requests table
    $query = "INSERT INTO job_requests (employee_id, job_id, job_type, request_status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sis", $employee_id, $job_id, $job_type);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script> alert('Your job application request has been submitted!'); </script>";
            echo '<script>window.location.href="employeeMain.php";</script>';
        } else {
            echo "Error: Could not submit your application.";
        }
        $stmt->close();
    } else {
        echo "Error in SQL query.";
    }
}

if (isset($_GET['searchJobs'])) {
    $_SESSION['jobLocation'] = htmlspecialchars(trim($_GET['jobLocation']));
    $_SESSION['jobType'] = htmlspecialchars($_GET['jobType']);
    $_SESSION['searchJobs'] = true;
}

if (isset($_SESSION['searchJobs']) && $_SESSION['searchJobs'] === true) {
    // Fetch session-stored search parameters
    $location = htmlspecialchars(trim($_SESSION['jobLocation']));
    $jobType = $_SESSION['jobType'];

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the query based on job type
    if ($jobType == 'firm') {
        $query = "SELECT jp.job_id, jp.firm_id AS employer_id, jp.job_title, jp.job_description, jp.job_date, jp.job_timing AS job_time, jp.job_status, jp.wage, jp.job_location, 'firm' AS job_type, f.firm_name AS employer_name, f.profile_image AS employer_image
                  FROM job_postings jp
                  JOIN firms f ON jp.firm_id = f.firm_id
                  WHERE jp.job_location LIKE ? AND jp.job_status = 'open'";
    } elseif ($jobType == 'elderly') {
        $query = "SELECT ej.job_id, ej.elderly_id AS employer_id, ej.job_title, ej.job_description, ej.job_date, ej.job_time, ej.job_status, ej.wage, ej.job_location, 'elderly' AS job_type, e.full_name AS employer_name, e.profile_image AS employer_image
                  FROM elderly_jobs ej
                  JOIN elderly e ON ej.elderly_id = e.elderly_id
                  WHERE ej.job_location LIKE ?";
    } else {
        $query = "(SELECT jp.job_id, jp.firm_id AS employer_id, jp.job_title, jp.job_description, jp.job_date, jp.job_timing AS job_time, jp.job_status, jp.wage, jp.job_location, 'firm' AS job_type, f.firm_name AS employer_name, f.profile_image AS employer_image
                   FROM job_postings jp
                   JOIN firms f ON jp.firm_id = f.firm_id
                   WHERE jp.job_location LIKE ? AND jp.job_status = 'open')
                   UNION
                   (SELECT ej.job_id, ej.elderly_id AS employer_id, ej.job_title, ej.job_description, ej.job_date, ej.job_time, ej.job_status, ej.wage, ej.job_location, 'elderly' AS job_type, e.full_name AS employer_name, e.profile_image AS employer_image
                   FROM elderly_jobs ej
                   JOIN elderly e ON ej.elderly_id = e.elderly_id
                   WHERE ej.job_location LIKE ?)";
    }

    $stmt = $conn->prepare($query);
    $location_param = "%$location%";
    
    if ($jobType == 'both') {
        $stmt->bind_param("ss", $location_param, $location_param);
    } else {
        $stmt->bind_param("s", $location_param);
    }

    $stmt->execute();
    $job_result = $stmt->get_result(); // Use a different variable name for the result
    echo "<h3>RESULTS NEAR ".strtoupper($location)."</h3>";
    if ($job_result->num_rows > 0) {
        ?><ul class="cards"><?php
        while ($row = $job_result->fetch_assoc()) {
            echo "<li class='cards__item'>";
            echo '<div class="cardBOX">';
            echo '<div class="card__image" style="background-image: url(\'' . htmlspecialchars($row['employer_image']) . '\');"></div>';
            echo '<div class="card__content">';
            echo "<span class='job-type-label'>" . htmlspecialchars(ucfirst($row['job_type'])) . "</span>";
            echo "<div class='card__title'>" . htmlspecialchars($row['job_title']) . "</div>";
            echo "<p class='card__text'><strong>Employer:</strong> " . htmlspecialchars($row['employer_name']) . "</p>";
            echo "<p class='card__text'>" . htmlspecialchars($row['job_description']) . "</p>";
            echo "<p class='card__text'><strong>Wage:</strong> $" . htmlspecialchars($row['wage']) . "</p>";
            echo "<p class='card__text'><strong>Date:</strong> " . htmlspecialchars($row['job_date']) . "</p>";
            echo "<p class='card__text'><strong>Time:</strong> " . htmlspecialchars($row['job_time']) . "</p>";
            echo "<p class='card__text'><strong>Location:</strong> " . htmlspecialchars($row['job_location']) . "</p>";

            // Check if the employee already applied
            $requested = $conn->prepare('SELECT * FROM job_requests WHERE employee_id = ? AND job_id = ? and job_type=?');
            $requested->bind_param("sss", $_SESSION['Employee_id'], $row['job_id'], $row['job_type']);
            $requested->execute();
            $request_result = $requested->get_result(); // Use a different variable name
            if ($request_result->num_rows > 0) {
                ?><button class="btn btn--block card__btn applied">Applied✔</button><?php
            } else {
                ?><form class="fo" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($row['job_id']); ?>">
                    <input type="hidden" name="job_type" value="<?php echo htmlspecialchars($row['job_type']); ?>">
                    <button class="btn btn--block card__btn notapplied" name="apply" value="true" type="submit">Apply Now</button>
                </form><?php
            }
            echo '</div>'; // Close card content
            echo '</div>'; // Close card
            echo "</li>";
        }
        ?></ul><?php
    } else {
        echo "<p class='cent' >No jobs found in this location.</p>";
    }
    $stmt->close();
}

?>

<?php include 'footer.php'; ?>
