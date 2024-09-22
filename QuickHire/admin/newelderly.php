<div class="into">
    <h1 class="TagLine">New <span class="ride">Elderly!</span></h1>
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
 
                $sql = "SELECT profile_image FROM elderly_registration WHERE elderly_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $id_to_delete); // $accepted_id should be the elderly_id to transfer
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        unlink($row["profile_image"]);
                    }
                }


                $sql = "DELETE FROM elderly_registration WHERE elderly_id = ?";
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
            //deleteing the records form temporary and placing them at driver database
            if (isset($_GET['accept'])) {
                $accepted_id = $_GET['accept'];

                $query = "SELECT COALESCE(MAX(CAST(SUBSTRING(elderly_id, 2) AS UNSIGNED)), 0) + 1 AS next_id FROM elderly";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $elderly_id = 'E' . str_pad($row['next_id'], 3, '0', STR_PAD_LEFT);

                // Query to select data from elderly_registration table for the given elderly_id
                $sql = "SELECT * FROM elderly_registration WHERE elderly_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $accepted_id); // $accepted_id should be the elderly_id to transfer
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Prepare an insert statement to insert data into the elderly table
                        $stmt = $conn->prepare("INSERT INTO elderly (elderly_id, full_name, email, gender, dob, phone, address, landmark, emergency_contact, medical_conditions, allergies, medications, physical_limitations, help_type, preferred_days, language_preference, hobbies, preferred_assistant_gender, interaction_preference, registered_at, password, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                        // Bind the values from elderly_registration
                        $stmt->bind_param(
                            "ssssssssssssssssssssss",
                            $elderly_id,
                            $row['full_name'],
                            $row['email'],
                            $row['gender'],
                            $row['dob'],
                            $row['phone'],
                            $row['address'],
                            $row['landmark'],
                            $row['emergency_contact'],
                            $row['medical_conditions'],
                            $row['allergies'],
                            $row['medications'],
                            $row['physical_limitations'],
                            $row['help_type'],
                            $row['preferred_days'],
                            $row['language_preference'],
                            $row['hobbies'],
                            $row['preferred_assistant_gender'],
                            $row['interaction_preference'],
                            $row['registered_at'],
                            $row['password'],
                            $row['profile_image'] // For BLOB (binary data) handling
                        );

                        // Execute the statement to insert into elderly table
                        if ($stmt->execute()) {
                            echo "<script>alert('Elderly record accepted successfully');</script>";

                            // After successful insertion, delete from elderly_registration
                            $stmt = $conn->prepare("DELETE FROM elderly_registration WHERE elderly_id = ?");
                            $stmt->bind_param("s", $accepted_id);

                            if ($stmt->execute()) {
                                echo "<script>console.log('Elderly record with ID " . $accepted_id . " deleted');</script>";
                            } else {
                                echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
                            }
                        } else {
                            echo "<script>alert('Error inserting record: " . $stmt->error . "');</script>";
                        }

                        $stmt->close();
                    } else {
                        echo "<script>alert('No such elderly record found.');</script>";
                    }
                    // $conn->close();
                } else {
                    handleError("Error preparing statement: " . $conn->error);
                }
            }
        }

        $sql = "SELECT * FROM elderly_registration";
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
                                <button name="accept" value='<?php echo $row["elderly_id"]; ?>'>Accept</button>
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