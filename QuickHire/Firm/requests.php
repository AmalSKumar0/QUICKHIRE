<style>
.cards {
    
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Space between cards */
}

.cards__item {
    
    flex: 1 1 calc(30% - 20px); /* Adjust to fit your design */
    /* box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); */
    border-radius: 8px;
    /* background-color: #ffffff; */
    padding: 20px;
    transition: box-shadow 0.3s;
}

.card__title {
    font-size: 1.5em;
    margin-bottom: 10px;
}

.card__text {
    margin-bottom: 8px;
}

.button-group {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.btn {
    background-color: blue; /* Button color */
    color: white; /* Text color */
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    flex: 1; /* Makes buttons take equal width */
    margin: 0 5px; /* Space between buttons */
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: darkblue; /* Darker shade on hover */
}
.card{
    background-color:  #E0E5E9;
    position: relative;
    left: 20px;
    padding: 20px;
}
h1{
    text-align: center;
}
</style>
<h1>ALL REQUESTS</h1>
</div>
<br><br>
<?php
// Check if Accept or Decline button is pressed
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];
    $job_id = $_POST['job_id'];
    $employee_id = $_POST['employee_id'];

    if ($action == 'accept') {
        // Accept the request: Update the request status and the job status to accepted
        $updateRequest = "UPDATE job_requests SET request_status = 'accepted' WHERE request_id = ?";
        $updateJob = "UPDATE job_postings SET job_status = 'in-progress' WHERE job_id = ?"; // Assuming 'in-progress' for accepted jobs

        $stmt1 = $conn->prepare($updateRequest);
        $stmt1->bind_param("i", $request_id);
        $stmt1->execute();
        $stmt1->close();

        $stmt2 = $conn->prepare($updateJob);
        $stmt2->bind_param("i", $job_id);
        $stmt2->execute();
        $stmt2->close();

        echo "<script> alert('Job request accepted successfully'); </script>";
    } elseif ($action == 'decline') {
        // Decline the request: Delete the job request from the database
        $deleteRequest = "DELETE FROM job_requests WHERE request_id = ?";
        $stmt = $conn->prepare($deleteRequest);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();
        
        echo "<script> alert('Job request declined'); </script>";
    }
    echo '<script>window.location.href="firmMain.php";</script>';
}

// Query to fetch all job requests for the logged-in firm user
$query = "SELECT jr.request_id , jr.job_id, jr.employee_id, jr.request_status, jr.request_date,
                 jp.job_title, jp.job_description, jp.job_location, jp.job_date, jp.job_timing,
                 emp.Emp_name AS employee_name, emp.email
          FROM job_requests jr
          JOIN job_postings jp ON jr.job_id = jp.job_id
          JOIN temporary_employee emp ON jr.employee_id = emp.Employee_id
          WHERE jp.firm_id = ? AND jr.request_status = 'pending'
          ORDER BY jr.request_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['firm_id']); // Bind the firm_id from session
$stmt->execute();
$result = $stmt->get_result();

// Display job requests

// Display job requests
if ($result->num_rows > 0) {
    echo '<ul class="cards">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="card">';

        // Job details
        echo "<div class='card__title'>" . htmlspecialchars($row['job_title']) . "</div>";
        echo "<p class='card__text'>" . htmlspecialchars($row['job_description']) . "</p>";
        echo "<p class='card__text'><strong>Date:</strong> " . htmlspecialchars($row['job_date']) . "</p>";
        echo "<p class='card__text'><strong>Time:</strong> " . htmlspecialchars($row['job_timing']) . "</p>";
        echo "<p class='card__text'><strong>Location:</strong> " . htmlspecialchars($row['job_location']) . "</p>";

        // Employee details
        echo "<p class='card__text'><strong>Requested by:</strong> " . htmlspecialchars($row['employee_name']) . "</p>";
        echo "<p class='card__text'><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";

        // Accept or Decline buttons
        echo "<form method='POST' class='button-group'>";
        echo "<input type='hidden' name='request_id' value='" . htmlspecialchars($row['request_id']) . "'>";
        echo "<input type='hidden' name='job_id' value='" . htmlspecialchars($row['job_id']) . "'>";
        echo "<input type='hidden' name='employee_id' value='" . htmlspecialchars($row['employee_id']) . "'>";
        echo '<button type="submit" name="action" value="accept" class="btn btn--block card__btn">Accept</button>';
        echo '<button type="submit" name="action" value="decline" class="btn btn--block card__btn">Decline</button>';
        echo "</form>";
        echo '</div>'; // Close card
    }
    echo '</ul>';
} else {
    echo "<p style='text-align: center;'>No job requests found.</p>";
}

$stmt->close();
?>
<br>

