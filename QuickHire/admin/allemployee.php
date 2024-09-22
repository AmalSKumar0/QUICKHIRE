<div class="into">
    <h1 class="TagLine">All <span class="ride">Employees!</span></h1>
    <div class="card-container">
        <?php
        function handleError($error)
        {
            error_log($error);
            die("Something went wrong! Please try again later.");
        }

        $conn = mysqli_connect("localhost", "root", "", "quickhire");

        if ($conn->connect_error) {
            handleError("Connection failed: " . $conn->connect_error);
        }

        // If the delete button is clicked
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET['delete'])) { // Deleting a record from temporary_employee table
                $id_to_delete = $_GET['delete'];
                $sql = "DELETE FROM temporary_employee WHERE Employee_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $id_to_delete); // 's' for string (Employee_id is VARCHAR)
                    if ($stmt->execute()) {
                        echo "<script>alert('Deleted successfully');</script>";
                    } else {
                        handleError("Error deleting record: " . $stmt->error);
                    }
                    $stmt->close();
                } else {
                    handleError("Error preparing statement: " . $conn->error);
                }
            }
        }

        // Fetching all employees
        $sql = "SELECT * FROM temporary_employee";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <div class="card">
                    <div class="card-image">
                        <!-- Display the profile image, assuming profile_image is stored as a BLOB in the database -->
                        <img src="<?php echo $row['profile_image']; ?>" alt="<?php echo $row['Emp_name']; ?>">
                    </div>
                    <div class="card-content">
                        <h2><?php echo $row['Emp_name']; ?></h2>
                        <div class="section">
                            <p>Address: <?php echo $row['address']; ?></p>
                            <p>Email: <?php echo $row['email']; ?></p>
                            <p>Phone No: <?php echo $row['phone_number']; ?></p>
                            <p>Work Experience: <?php echo $row['work_experience']; ?></p>
                            <p>Skills: <?php echo $row['skills']; ?></p>
                            <p>Availability: <?php echo $row['availability']; ?></p>
                            <p>Hourly Wage: <?php echo $row['hourly_wage']; ?></p>
                            <p>Qualified: <?php echo $row['is_qualified'] ? 'Yes' : 'No'; ?></p>
                        </div>
                        <div class="button-group">
                            <form method='get' style='display:inline;' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <button name="delete" value='<?php echo $row["Employee_id"]; ?>'>Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php }
        } else {
            echo "No results found.";
            echo "<br><br><br><br><br><br><br><br><br>";
        }

        $conn->close();
        ?>
    </div>
</div>
