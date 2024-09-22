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

.but {
    align-items: center;
    background-color: blue; /* Button color */
    color: white; /* Text color */
    border: none;
    cursor: pointer;
    position: relative;
    bottom: 45px;
    height: 30px;
    width: 140px;
    border-radius: 20px;
    transition: background-color 0.3s;
    left: 30%;
}

.but:hover {
    background-color: darkblue; /* Darker shade on hover */
}
</style>
<h1>YOUR ALL REQUESTS</h1>
</div>
<br><br>

<?php

if (isset($_POST['delete_job'])) {
    $request_id = $_POST['delete_job'];
    $employee_id = $_SESSION['Employee_id'];
    $deleteRequest = "DELETE FROM job_requests WHERE request_id = ?";
    $stmt = $conn->prepare($deleteRequest);
    $stmt->bind_param("i", $request_id);
    if($stmt->execute()){
        echo "<script> alert('Job request deleted'); </script>";
        echo '<script>window.location.href="employeeMain.php";</script>';
    }
    else{
        echo "<script> alert('ERROR :/'); </script>";
    }  
}
// Query to fetch all job requests sent by the logged-in employee
$query = "SELECT jr.request_id, jr.job_id, jr.employee_id, jr.request_status, jr.request_date,
                 jp.job_title, jp.job_description, jp.job_location, jp.job_date, jp.job_timing,
                 emp.Emp_name AS employee_name, emp.email
          FROM job_requests jr
          JOIN job_postings jp ON jr.job_id = jp.job_id
          JOIN temporary_employee emp ON jr.employee_id = emp.Employee_id
          WHERE jr.employee_id = ? 
          ORDER BY jr.request_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['Employee_id']); // Bind the employee_id from session
$stmt->execute();
$result = $stmt->get_result();

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

        // Request status
        echo "<p class='card__text'><strong>Status:</strong> " . htmlspecialchars($row['request_status']) . "</p>";

         // Delete button with a form
         echo "<form method='POST' onsubmit='return confirmDelete()'>";
         echo '<button type="submit" name="delete_job" value="'.$row['request_id'].'" class="but">Delete</button>';
         echo "</form>";

        echo '</div>'; // Close card
    }
    echo '</ul>';
} else {
    echo "<p style='text-align: center;'>No job requests found.</p>";
}

$stmt->close();
?>
<script>
// Confirm before deleting
function confirmDelete() {
    return confirm("Are you sure you want to delete this job request?");
}
</script>
<br>