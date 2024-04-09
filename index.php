<?php
ob_start();

session_start();

// If the user is already logged in end the session
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: logoutPage.php");
    exit;
}

require_once "config.php";

$email = $password = "";
$email_err = $password_err = $login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

   // Validate email
   if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter email.";
} else {
    $email = trim($_POST["email"]);
}

// Validate password
if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
} else {
    $password = trim($_POST["password"]);
}

// Check for errors before querying the database
if (empty($email_err) && empty($password_err)) {
    
    // Check if user is a student
    $sql = "SELECT studentID, emailAddress, password FROM students WHERE emailAddress = ?";
    $userType = "student"; // Default user type

    $stmt = mysqli_prepare($link, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Start session for logged in user
                        session_start();
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $id;
                        $_SESSION["email"] = $email;
                        $userType = "student"; 
                        header("location: studentHomepage.php"); // Redirect to student homepage
                        exit;
                    } else {
                        $password_err = "Incorrect password.";
                    }
                }
            } else {
                $email_err = "Email not found.";
            }
        }
    }

    // If user is not a student, check if they are a therapist
    if ($userType === "student") {
        $sql = "SELECT therapistID, emailAddress, password FROM therapists WHERE emailAddress = ?";
        $stmt = mysqli_prepare($link, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Start session for logged in user
                            session_start();
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $id;
                            $_SESSION["email"] = $email;
                            $userType = "therapist"; 
                            header("location: therapistHomepage.php"); // Redirect to therapist homepage
                            exit;
                        } else {
                            $password_err = "Incorrect password.";
                        }
                    }
                } else {
                    $email_err = "Email not found.";
                }
            }
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Set error message if login fails
    $login_err = "Invalid email or password.";
}

// Close connection
mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "icon" href = "Images/logo.png" type = "image/x-icon">
    <title>Mental Health Platform</title>   
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-7 col-sm-9">
                <div class="text-center my-4">
                    <img src="Images/logo.png" alt="logo" width="150">
                </div>
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h1 class="fs-4 card-title fw-bold mb-4 signin" style="text-align: center;">Sign In</h1>
                        <form method="POST" class="needs-validation" novalidate="" autocomplete="off">
                            <div class="mb-3">
                                <label class="mb-2 text-muted loginfont" for="email">E-Mail Address</label>
                                <input id="email" type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" name="email" required value="<?php echo $email; ?>" required>
                                <div class="invalid-feedback">
                                  <?php echo $email_err; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted loginfont" for="password">Password</label>
                                <input id="password" type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" name="password" required value="<?php echo $password; ?>" required>
                                <div class="invalid-feedback">
                                  <?php echo $password_err; ?>
                                </div>
                            </div>

                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>             
                            </div>
                        </form>
                    </div>
                    <div class="card-footer py-3 border-0">
                    <div class="text-center loginfont">
                        Don't have an account? Select user type </br>
                        <a href="studentSignUp.php" class="text-dark loginfont">Student</a>
                        <a href="therapistSignUp.php" class="text-dark loginfont">Therapist</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>