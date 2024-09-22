<div class="into">
    <h1 class="TagLine">New <span class="ride">Firm!</span></h1>
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

        // If the buttons are clicked
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // Deleting the content of firms_registration database
            if (isset($_GET['delete'])) {
                $id_to_delete = $_GET['delete'];


                $sql = "SELECT profile_image FROM firms_registration WHERE firm_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $id_to_delete);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Assuming profile_image only stores the file name
                        $file_path = 'uploads/' . $row['profile_image']; // Adjust 'uploads/' to your actual folder path

                        // Check if file exists before deleting
                        if (file_exists($file_path)) {
                            unlink($file_path); // Delete the image
                            echo "<script>alert('Image deleted successfully');</script>";
                        } else {
                            echo "<script>alert('File not found');</script>";
                        }
                    }
                }

                
                $sql = "DELETE FROM firms_registration WHERE firm_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $id_to_delete);
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

            // Accepting the records from firms_registration and placing them in the firms database
            if (isset($_GET['accept'])) {
                $accepted_id = $_GET['accept'];

                // Query to select data from firms_registration table
                $sql = "SELECT * FROM firms_registration WHERE firm_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("s", $accepted_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Insert data into firms table
                        $stmt = $conn->prepare("INSERT INTO firms (firm_id, profile_image, firm_name, email, password, contact_name, contact_phone, address, industry_type, number_of_employees, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                        // Bind values from firms_registration
                        $stmt->bind_param(
                            "sssssssssis",
                            $row['firm_id'],
                            $row['profile_image'],
                            $row['firm_name'],
                            $row['email'],
                            $row['password'],
                            $row['contact_name'],
                            $row['contact_phone'],
                            $row['address'],
                            $row['industry_type'],
                            $row['number_of_employees'],
                            $row['registration_date']
                        );

                        // Execute insertion
                        if ($stmt->execute()) {
                            echo "<script>alert('Firm record accepted successfully');</script>";

                            // Delete from firms_registration after successful insertion
                            $stmt = $conn->prepare("DELETE FROM firms_registration WHERE firm_id = ?");
                            $stmt->bind_param("s", $accepted_id);

                            if ($stmt->execute()) {
                                echo "<script>console.log('Firm record with ID " . $accepted_id . " deleted');</script>";
                            } else {
                                echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
                            }
                        } else {
                            echo "<script>alert('Error inserting record: " . $stmt->error . "');</script>";
                        }

                        $stmt->close();
                    } else {
                        echo "<script>alert('No such firm record found.');</script>";
                    }
                } else {
                    handleError("Error preparing statement: " . $conn->error);
                }
            }
        }

        // Displaying all firms in the firms_registration table
        $sql = "SELECT * FROM firms_registration";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { ?>
                <div class="card">
                    <div class="card-image">
                        <!-- Display profile image from BLOB -->
                        <img src="<?php echo $row['profile_image']; ?>" alt="<?php echo $row['firm_name']; ?>">
                    </div>
                    <div class="card-content">
                        <h2><?php echo $row['firm_name']; ?></h2>
                        <div class="section">
                            <p>Contact Name: <?php echo $row['contact_name']; ?></p>
                            <p>Contact Phone: <?php echo $row['contact_phone']; ?></p>
                            <p>Industry: <?php echo $row['industry_type']; ?></p>
                        </div>
                        <div class="section">
                            <p>Email: <?php echo $row['email']; ?></p>
                            <p>Number of Employees: <?php echo $row['number_of_employees']; ?></p>
                        </div>
                        <div class="button-group">
                            <form method='get' style='display:inline;'
                                action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <button name="delete" value='<?php echo $row["firm_id"]; ?>'>Delete</button>
                                <button name="accept" value='<?php echo $row["firm_id"]; ?>'>Accept</button>
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