<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="images/favicon.ico" title="Favicon" />
    <title>QUICKHIRE</title>
    <link rel="stylesheet" type="text/css" href="styles/LoginReg.css">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
</head>

<body>
    <!-- background animation -->
    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <!-- background aniamtion ends here -->
    <?php
    session_start();
    $err = "";
    $conn = mysqli_connect("localhost", "root", "", "quickhire");

    if (isset($_POST["register"])) {
        // Handle password and confirm password
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if ($password !== $confirm_password) {
            $err = "Passwords do not match.";
        } else {
            // Fetch next elderly_id
            $query = "SELECT COALESCE(MAX(CAST(SUBSTRING(elderly_id, 2) AS UNSIGNED)), 0) + 1 AS next_id FROM elderly_registration";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
            $elderly_id = 'E' . str_pad($row['next_id'], 3, '0', STR_PAD_LEFT);

            // Get form data and sanitize input
            $full_name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
            $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
            $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : '';
            $dob = isset($_POST['dob']) ? htmlspecialchars(trim($_POST['dob'])) : '';
            $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
            $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
            $landmark = isset($_POST['landmark']) ? htmlspecialchars(trim($_POST['landmark'])) : '';
            $emergency_contact = isset($_POST['emergency_contact']) ? htmlspecialchars(trim($_POST['emergency_contact'])) : '';
            $medical_conditions = isset($_POST['medical_conditions']) ? htmlspecialchars(trim($_POST['medical_conditions'])) : '';
            $allergies = isset($_POST['allergies']) ? htmlspecialchars(trim($_POST['allergies'])) : '';
            $medications = isset($_POST['medications']) ? htmlspecialchars(trim($_POST['medications'])) : '';
            $physical_limitations = isset($_POST['limitations']) ? htmlspecialchars(trim($_POST['limitations'])) : '';
            $help_type = isset($_POST['help_type']) ? htmlspecialchars(trim($_POST['help_type'])) : '';
            $preferred_days = isset($_POST['preferred_days']) ? htmlspecialchars(trim($_POST['preferred_days'])) : '';
            $language_preference = isset($_POST['language']) ? htmlspecialchars(trim($_POST['language'])) : '';
            $hobbies = isset($_POST['hobbies']) ? htmlspecialchars(trim($_POST['hobbies'])) : '';
            $preferred_assistant_gender = isset($_POST['preferred_assistant_gender']) ? htmlspecialchars(trim($_POST['preferred_assistant_gender'])) : '';
            $interaction_preference = isset($_POST['interaction_preference']) ? htmlspecialchars(trim($_POST['interaction_preference'])) : '';

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = $conn->prepare("select email from firms where email = ? union select email from firms_registration  where email = ? union select email from elderly where email = ? union select email from elderly_registration where email = ? union select email from temporary_employee where email = ?");
            $sql->bind_param("sssss", $email, $email, $email, $email, $email);
            $sql->execute();
            $result = $sql->get_result();
            if ($result->fetch_assoc() > 0) {
                $err = "Email already exist";
            } else {
                // Handle image upload
                if (isset($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                    $target_dir = "uploads/"; // Directory where the images will be stored
                    $image_name = basename($_FILES['image']['name']);
                    $target_file = $target_dir . uniqid() . "_" . $image_name; // Unique file name

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                        // Prepare SQL statement to insert data into elderly_registration table
                        $stmt = $conn->prepare("INSERT INTO elderly_registration 
                        (elderly_id, full_name, email, gender, dob, phone, address, landmark, emergency_contact, medical_conditions, allergies, medications, physical_limitations, help_type, preferred_days, language_preference, hobbies, preferred_assistant_gender, interaction_preference, password, profile_image) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                        // Bind parameters for the prepared statement
                        $stmt->bind_param(
                            "sssssssssssssssssssss",
                            $elderly_id,
                            $full_name,
                            $email,
                            $gender,
                            $dob,
                            $phone,
                            $address,
                            $landmark,
                            $emergency_contact,
                            $medical_conditions,
                            $allergies,
                            $medications,
                            $physical_limitations,
                            $help_type,
                            $preferred_days,
                            $language_preference,
                            $hobbies,
                            $preferred_assistant_gender,
                            $interaction_preference,
                            $hashed_password,
                            $target_file // Path to the image file
                        );

                        // Execute the prepared statement
                        if ($stmt->execute()) {
                            echo "<script>alert('Registration successful! Now wait till admin accepts your account');</script>";
                        } else {
                            $err = "Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $err = "Failed to upload the image.";
                    }
                } else {
                    if (!isset($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'])) {
                        $err = "Profile image is required.";
                    } elseif ($_FILES['image']['error'] != UPLOAD_ERR_OK) {
                        $err = "File upload error: " . $_FILES['image']['error'];
                    }
                }
            }
        }
    }


    //login button is pressed this will check the user exist or not 
    //cross checks the password as well
    if (isset($_POST["login"])) {
        $email = $_POST['logmail'];
        $password = $_POST['logpass'];

        // Prepare the statement to fetch the stored hashed password
        $stmt = $conn->prepare("SELECT password FROM elderly WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            $err = "User not found"; // User not found
        } else {
            $stmt->bind_result($db_password);
            $stmt->fetch();
            // Cross check passwords
            if (password_verify($password, $db_password)) {
                // Password is correct
                $stmt = $conn->prepare("SELECT * FROM elderly WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                // User is logged in and session variables are declared
                $_SESSION['elderlyname'] = $user['full_name'];
                $_SESSION['elderly_id'] = $user['elderly_id'];

                echo '<script>window.location.href="elderMain.php";</script>';
            } else {
                $err = "Incorrect password";
            }
            $stmt->close();
        }
    }


    mysqli_close($conn);
    ?>

    <body>
        <div class="wrapper">
            <div class="inner">
                <div class="image-holder">
                    <img src="images/registration-form.jpg" alt="">
                </div>
                <a href="index.php" class="cross"></a>
                <!-- user registraion form -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" class="disable">
                    <h3>Elderly <span>Registration</span></h3>
                    <h5 class="error"><?php echo $err; ?></h5>

                    <!-- Step 1: Personal Information -->
                    <div class="form-step step-1 active">
                        <div class="form-wrapper">
                            <input type="text" placeholder="Full Name" required name="name" class="form-control">
                            <label for="image">Upload Profile Image:</label>
                            <input type="file" name="image" id="profile_image" accept="image/*" required>
                            <i class="zmdi zmdi-account"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="email" required placeholder="Email Address" name="email" class="form-control">
                            <i class="zmdi zmdi-email"></i>
                        </div>
                        <div class="form-wrapper">
                            <select name="gender" required class="form-control">
                                <option value="" disabled selected>Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <i class="zmdi zmdi-caret-down" style="font-size: 17px"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="date" placeholder="Date of Birth" required name="dob" class="form-control">
                            <i class="zmdi zmdi-calendar"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="tel" required placeholder="Phone Number" name="phone" class="form-control">
                            <i class="zmdi zmdi-phone"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="text" required placeholder="Residential Address" name="address"
                                class="form-control">
                            <i class="zmdi zmdi-home"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="text" placeholder="Landmark" name="landmark" class="form-control">
                            <i class="zmdi zmdi-map"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="tel" required placeholder="Emergency Contact Number" name="emergency_contact"
                                class="form-control">
                            <i class="zmdi zmdi-alert-circle"></i>
                        </div>
                        <button type="button" class="next-btn">Next</button>
                    </div>


                    <!-- Step 3: Health Information -->
                    <div class="form-step step-2">
                        <div class="form-wrapper">
                            <textarea placeholder="Medical Conditions" required name="medical_conditions"
                                class="form-control"></textarea>
                            <i class="zmdi zmdi-hospital"></i>
                        </div>
                        <div class="form-wrapper">
                            <textarea placeholder="Allergies" required name="allergies" class="form-control"></textarea>
                            <i class="zmdi zmdi-alert-triangle"></i>
                        </div>
                        <div class="form-wrapper">
                            <textarea placeholder="Medications" name="medications" class="form-control"></textarea>
                            <i class="zmdi zmdi-pill"></i>
                        </div>
                        <div class="form-wrapper">
                            <textarea placeholder="Physical Limitations" name="limitations"
                                class="form-control"></textarea>
                            <i class="zmdi zmdi-walk"></i>
                        </div>
                        <div class="form-wrapper">
                            <label>Type of Help Needed:</label>
                            <select name="help_type" class="form-control" required>
                                <option value="companionship">Companionship</option>
                                <option value="errands">Errands</option>
                                <option value="physical_assistance">Physical Assistance</option>
                                <option value="technical_assistance">Technical Assistance</option>
                            </select>
                        </div>
                        <div class="form-wrapper">
                            <input type="text" placeholder="Preferred Days/Times for Assistance" name="preferred_days"
                                class="form-control">
                            <i class="zmdi zmdi-calendar-note"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="text" placeholder="Language Preference" name="language" class="form-control">
                            <i class="zmdi zmdi-globe"></i>
                        </div>
                        <div id="Buttons">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="button" class="next-btn">Next</button>
                        </div>
                    </div>

                    <!-- Step 4: Assistance Preferences -->


                    <!-- Step 5: Social & Behavioral Preferences -->
                    <div class="form-step step-3">
                        <div class="form-wrapper">
                            <textarea placeholder="Hobbies/Interests" name="hobbies" class="form-control"></textarea>
                            <i class="zmdi zmdi-mood"></i>
                        </div>
                        <div class="form-wrapper">
                            <select name="preferred_assistant_gender" class="form-control">
                                <option value="" disabled selected>Preferred Gender of Assistant</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="no_preference">No Preference</option>
                            </select>
                        </div>
                        <div class="form-wrapper">
                            <select name="interaction_preference" class="form-control">
                                <option value="" disabled selected>Interaction Preference</option>
                                <option value="talkative">Talkative</option>
                                <option value="quiet">Quiet</option>
                            </select>
                        </div>
                        <div class="form-wrapper">
                            <input type="password" required placeholder="Password" name="password" class="form-control">
                            <i class="zmdi zmdi-lock"></i>
                        </div>
                        <div class="form-wrapper">
                            <input type="password" required placeholder="Confirm Password" name="confirm_password"
                                class="form-control">
                            <i class="zmdi zmdi-lock"></i>
                        </div>

                        <div id="Buttons">
                            <button type="button" class="prev-btn">Previous</button>
                            <button type="submit" value="submit" name="register">Register</button>
                            <div class="logbtn">Login</div>
                        </div>
                    </div>
                </form>

                <!-- JavaScript to Handle Form Steps -->
                <script>
                    let currentStep = 1;
                    const formSteps = document.querySelectorAll('.form-step');
                    const nextBtns = document.querySelectorAll('.next-btn');
                    const prevBtns = document.querySelectorAll('.prev-btn');

                    function showStep(step) {
                        formSteps.forEach((formStep, index) => {
                            if (index === step - 1) {
                                formStep.classList.add('active');
                            } else {
                                formStep.classList.remove('active');
                            }
                        });
                    }

                    nextBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            currentStep++;
                            showStep(currentStep);
                        });
                    });

                    prevBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            currentStep--;
                            showStep(currentStep);
                        });
                    });

                    showStep(currentStep); // Initialize the first step
                </script>

                <!-- Optional CSS for Form Steps -->
                <style>
                    .form-step {
                        display: none;
                    }

                    .form-step.active {
                        display: block;
                    }

                    .buttons {
                        margin-top: 20px;
                    }

                    #Buttons {
                        display: flex;
                        gap: 10px;
                    }

                    .logbtn {
                        position: relative;
                        left: 5px;
                        top: 50px;
                    }
                </style>

                <!-- user login form -->
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="login">
                    <h3>ELDERLY <span>Login</span></h3>
                    <div class="form-wrapper">
                        <input type="email" placeholder="Email Address" class="form-control" name="logmail" required>
                        <i class="zmdi zmdi-email"></i>
                    </div>

                    <div class="form-wrapper">
                        <input type="password" placeholder="Password" class="form-control" name="logpass" required>
                        <i class="zmdi zmdi-lock"></i>
                    </div>
                    <h5 class="error"><?php echo $err; ?></h5>
                    <div class="buttons">
                        <button type="submit" value="submit" name="login">Login</button>
                        <div class="regbtn">Register</div>
                    </div>
                </form>
            </div>
        </div>
        <script async="" src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
        <script src='scripts/reg.js'></script>
    </body>
</body>

</html>