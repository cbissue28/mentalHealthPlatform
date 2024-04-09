<?php
  
    session_start();

    require_once "config.php";

    // Initialize variables for form data and error messages
    $first_name = $last_name = $expertise = $room = $email = $password = $confirm_password = "";
    $first_name_err = $last_name_err = $expertise_err = $room_err = $email_err = $password_err = $confirm_password_err = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate first name
        if (empty(trim($_POST["first_name"]))) {
            $first_name_err = "Please enter your first name.";
        } elseif (!preg_match('/^[a-zA-Z\'-]+$/', trim($_POST["first_name"]))) {
            $first_name_err = "First name must not contain numbers or special characters.";
        } else {
            $first_name = trim($_POST["first_name"]);
        }
  
        // Validate last name
        if (empty(trim($_POST["last_name"]))) {
            $last_name_err = "Please enter your last name.";
        } elseif (!preg_match('/^[a-zA-Z\'-]+$/', trim($_POST["last_name"]))) {
            $last_name_err = "Last name must not contain numbers or special characters.";
        } else {
            $last_name = trim($_POST["last_name"]);
        }

        // Validate expertise
        if (empty(trim($_POST["expertise"]))) {
            $expertise_err = "Please enter your expertise.";
        } elseif (!preg_match('/^[a-zA-Z\'\-, ]*$/', trim($_POST["expertise"]))) {
            $expertise_err = "Expertise must only contain letters.";
        } else {
            $expertise = trim($_POST["expertise"]);
        }

        // Validate room
        if (empty(trim($_POST["room"]))) {
            $room_err = "Please enter your room number.";
        } elseif (!preg_match('/^[a-zA-Z0-9\'\-, ]*$/', trim($_POST["room"]))) {
            $room_err = "Room must only contain letters and numbers.";
        } else {
            $room = trim($_POST["room"]);
        }
    
        // Validate email
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter an email.";
        } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
            $email_err = "Invalid email format.";
        } else {
            $email = trim($_POST["email"]);
    
            // Check if the email already exists in the database
            $sql = "SELECT studentID FROM students WHERE emailAddress = '$email' UNION SELECT therapistID FROM therapists WHERE emailAddress = '$email'";
            $result = mysqli_query($link, $sql);
    
            if ($result && mysqli_num_rows($result) > 0) {
                $email_err = "This email is already registered.";
            }
        }
    
        // Validate password
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])/', trim($_POST["password"]))) {
            $password_err = "Password must contain at least one uppercase letter, number and special character.";
        } else {
            $password = trim($_POST["password"]);
        }
    
        // Validate confirm password
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
            }
        }

        // If no errors, insert data into database
        if (empty($first_name_err) && empty($last_name_err) && empty($expertise_err) && empty($room_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
            $sql = "INSERT INTO therapists (first_name, last_name, expertise, room, emailAddress, password, therapist_image) VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind parameters and execute the statement
                mysqli_stmt_bind_param($stmt, "sssssss", $param_first_name, $param_last_name, $param_expertise, $param_room, $param_email, $param_password, $param_therapist_image);
  
                $param_first_name = $first_name;
                $param_last_name = $last_name;
                $param_expertise = $expertise;
                $param_room = $room;
                $param_email = $email;
                $param_password = password_hash($password, PASSWORD_DEFAULT); 
                $param_therapist_image = null;

                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to confirmation page
                    header("location: signUpConfirmation.php");
                    exit();
                } else {
                    // Display error message if insertion fails
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

        // Close database connection
        mysqli_close($link);
    }
?>

<!DOCTYPE php>
<php lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "icon" href = "Images/logo.png" type = "image/x-icon">
    <title>Mental Health Platform</title>   
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <div class="returnButton">
        <a href="index.php" class="btn btn-outline-primary">  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-circle" viewBox="0 0 16 16">
         <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.5-.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5z"/>
         </svg> Back</a>
     </div>

    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-lg-12 col-xl-11">
          <div class="card-body p-md-5">
            <div class="row justify-content-center">
              <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1 signUpBox">
                <img src="Images/logo.png" width="150pt">
                   <p class="text-center h3 fw-bold mb-5 mx-1 mx-md-4 mt-4 loginfont">Therapist Sign Up</p>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mx-1 mx-md-4">
                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example1c">First Name</label>
                      <input type="text" id="form3Example1c" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" name="first_name" value="<?php echo $first_name; ?>" required>  
                    <div class="invalid-feedback">
                       <?php echo $first_name_err; ?>
                    </div>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example1c">Last Name</label>
                      <input type="text" id="form3Example1c" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" name="last_name" value="<?php echo $last_name; ?>" required>  
                    <div class="invalid-feedback">
                       <?php echo $last_name_err; ?>
                    </div>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example1c">Expertise</label>
                      <input type="text" id="form3Example1c" class="form-control <?php echo (!empty($expertise_err)) ? 'is-invalid' : ''; ?>" name="expertise" value="<?php echo $expertise; ?>" required>  
                    <div class="invalid-feedback">
                       <?php echo $expertise_err; ?>
                    </div>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example1c">Room</label>
                      <input type="text" id="form3Example1c" class="form-control <?php echo (!empty($room_err)) ? 'is-invalid' : ''; ?>" name="room" value="<?php echo $room; ?>" required>  
                    <div class="invalid-feedback">
                       <?php echo $room_err; ?>
                    </div>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example3c">Email Address</label>
                      <input type="email" id="form3Example3c" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" name="email" value="<?php echo $email; ?>" required>                           
                     <div class="invalid-feedback">
                       <?php echo $email_err; ?>
                     </div>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example4c">Password</label>
                      <input type="password" id="form3Example4c" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" name="password" maxlength="20" required>                            
                     <div class="invalid-feedback">
                       <?php echo $password_err; ?>
                     </div>
                    </div>
                  </div>

                  <div class="d-flex flex-row align-items-center mb-4">
                    <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                    <div class="form-outline flex-fill mb-0">
                      <label class="form-label loginfont" for="form3Example4cd">Confirm password</label>
                      <input type="password" id="form3Example4cd" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" name="confirm_password" maxlength="20" required>
                      <div class="invalid-feedback">
                       <?php echo $confirm_password_err; ?>
                      </div>
                    </div>
                  </div>
                    <button class="btn btn-primary w-100 py-2" type="submit" style="margin-left: 5pt;">Sign in</button>             
                </form>
              </div>
            </div>
          </div>     
      </div>
    </div>
  </div>
</body>
</html>