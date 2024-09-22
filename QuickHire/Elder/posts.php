<style>
 
.cards {
    /*  */
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
<h1>ALL POSTS</h1>
</div>
<br><br><?php
// Check if a delete request has been made
if (isset($_POST['delete_job'])) {
    $job_id = $_POST['job_id'];
    $query = "DELETE FROM elderly_jobs WHERE job_id = ? AND elderly_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $job_id, $_SESSION['elderly_id']);
    if ($stmt->execute()) {
        echo '<script>window.location.href="elderMain.php";</script>';
    } else {
        echo "<p>Error deleting job post.</p>";
    }
    $stmt->close();
}

// Query to fetch job posts created by the logged-in elderly user
$query = "SELECT job_id, job_title, job_description, job_date, job_time, job_status, wage, created_at, job_location 
          FROM elderly_jobs 
          WHERE elderly_id = ? 
          ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['elderly_id']); // Bind the elderly_id from session
$stmt->execute();
$result = $stmt->get_result();

// Display jobs in a card format
if ($result->num_rows > 0) {
    echo '<ul class="cards">';
    while ($row = $result->fetch_assoc()) {
        echo '<div class="card">';
        
        // Job title
        echo "<div class='card__title'>" . htmlspecialchars($row['job_title']) . "</div>";
        
        // Job description
        echo "<p class='card__text'>" . htmlspecialchars($row['job_description']) . "</p>";
        
        // Wage
        echo "<p class='card__text'><strong>Wage:</strong> $" . htmlspecialchars($row['wage']) . "</p>";
        
        // Job date and time
        echo "<p class='card__text'><strong>Date:</strong> " . htmlspecialchars($row['job_date']) . "</p>";
        echo "<p class='card__text'><strong>Time:</strong> " . htmlspecialchars($row['job_time']) . "</p>";
        
        // Job location
        echo "<p class='card__text'><strong>Location:</strong> " . htmlspecialchars($row['job_location']) . "</p>";
        
        // Job status
        echo "<p class='card__text'><strong>Status:</strong> " . htmlspecialchars($row['job_status']) . "</p>";

        // Delete button with a form
        echo "<form method='POST' onsubmit='return confirmDelete()'>";
        echo "<input type='hidden' name='job_id' value='" . htmlspecialchars($row['job_id']) . "'>";
        echo '<button type="submit" name="delete_job" class="btn but btn--block card__btn">Delete</button>';
        echo "</form>";

        echo '</div>'; // Close card content
        echo '</div>'; // Close card
    }
    echo '</ul>';
} else {
    echo "<p style='text-align: center;'>No job posts found.</p>";
}

$stmt->close();
?>

<script>
// Confirm before deleting
function confirmDelete() {
    return confirm("Are you sure you want to delete this job post?");
}
</script>
