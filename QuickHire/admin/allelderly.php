<div class="into">
    <h1 class="TagLine">All <span class="ride">Elderly!</span></h1>
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
        //if the buttons are clicked
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET['delete'])) { //deleting the content of temporarydriver database
                //on clicking the delete button
                $id_to_delete = $_GET['delete'];
                $sql = "DELETE * FROM elderly WHERE elderly_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $id_to_delete);
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

        $sql = "SELECT * FROM elderly";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <div class="card">
                    <div class="card-image">
                        <!-- Display the profile image, assuming profile_image is stored as a BLOB in the database -->
                        <img src="<?php echo $row['profile_image']; ?>" alt="<?php echo $row['full_name']; ?>">
                    </div>
                    <div class="card-content">
                        <h2><?php echo $row['full_name']; ?></h2>
                        <div class="section">
                            <p>Address: <?php echo $row['address']; ?></p>
                            <p>Gender: <?php echo $row['gender']; ?></p>
                            <p>Date of Birth: <?php echo $row['dob']; ?></p>
                        </div>
                        <div class="section">
                            <p>Phone No: <?php echo $row['phone']; ?></p>
                        </div>
                        <p>Email: <?php echo $row['email']; ?></p>
                        <p>Emergency Contact: <?php echo $row['emergency_contact']; ?></p>
                        <p>Medical Conditions: <?php echo $row['medical_conditions']; ?></p>
                        <p>Preferred Assistant Gender: <?php echo $row['preferred_assistant_gender']; ?></p>
                        <p>Help Type: <?php echo $row['help_type']; ?></p>
                        <div class="button-group">
                            <form method='get' style='display:inline;' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <button name="delete" value='<?php echo $row["elderly_id"]; ?>'>Delete</button>
                                <!-- <button name="accept" value='<?php //echo $row["elderly_id"]; ?>'>Accept</button> -->
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