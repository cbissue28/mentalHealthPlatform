<?php

session_start();

require_once "config.php";

// Get the student ID from the session
$studentID = $_SESSION['user_id'];

// Prepare and execute a query to retrieve student information based on the student ID
$stmt = mysqli_prepare($link, "SELECT * FROM students WHERE studentID = ?");
mysqli_stmt_bind_param($stmt, "i", $studentID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the submitted values from the form
    $newFirstName = $_POST['new_first_name'];
    $newLastName = $_POST['new_last_name'];
    $newDiagnoses = $_POST['new_diagnoses'];

    // Prepare and execute a query to update student information in the database
    $stmt = mysqli_prepare($link, "UPDATE students SET first_name = ?, last_name = ?, diagnoses = ? WHERE studentID = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $newFirstName, $newLastName, $newDiagnoses, $studentID);
    $updateResult = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Refresh the page after updating the information
    if ($updateResult) {
        header("Location: studentProfilePage.php");
        exit();
    } else {
        // Display an error message if the update operation fails
        echo "Error updating data: " . mysqli_error($link);
        exit();
    }
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
    <header>
        <nav> 
            <div class="px-3 py-2 border-bottom navBar">
                <div class="container">
                  <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <a href="studentHomepage.php" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
                      <img src="Images/solidLogo.png" style="height: 60pt;">
                    </a>
          
                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small navIcons">
                      <li>
                        <a href="studentHomepage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-house" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"></svg>
                          Home
                        </a>
                      </li>
                      <li>
                        <a href="studentCreateAppointmentsPage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-people" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"></svg>
                          Create Appointment
                        </a>
                      </li>
                      <li>
                        <a href="studentManageAppointmentsPage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-book" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"></svg>
                          Manage Appointments
                        </a>
                      </li>
                      <li>
                        <a href="studentAppointmentHistoryPage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-calendar-event" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"></svg>
                          Appointment History
                        </a>
                      </li>
                      <li>
                        <a href="#" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-person-circle" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"></svg>
                          Profile
                        </a>
                      </li>
                      <li>
                        <a href="logoutPage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-box-arrow-right" width="28" height="29" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"></svg>
                          Sign Out
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
        </nav>
    </header>

    <div class="p-4 mb-3 bg-body-white rounded">
        <h4 class="fst-italic" style="text-align: center;">Your Profile</h4>
        <div class="container">
            <div class="main-body">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="profileForm">
                        <div class="card">
                            <div class="card-body">
                              <div class="row mb-3">
                                <div class="col-sm-3">
                                  <h6 class="mb-0">First Name</h6>
                               </div>
                               <div class="col-sm-9 text-secondary">
                                  <input type="text" class="form-control" name="new_first_name" value="<?php echo $row['first_name']; ?>" id="new_first_name" maxlength="15">
                               </div>
                               </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Last Name</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" class="form-control" name="new_last_name" value="<?php echo $row['last_name']; ?>" id="new_last_name" maxlength="15">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Diagnoses</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" class="form-control" name="new_diagnoses" value="<?php echo $row['diagnoses']; ?>" id="new_diagnoses">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="submit" class="btn px-4 infoButton" value="Save Changes">
                                    </div>
                                </div>
                              </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      <a href="studentPasswordPage.php" class="serviceLinks"> <p style="margin-top: 20pt; font-weight: bold;">Click here to change your password</p></a>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script>
    // Function to validate the first name input field
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("profileForm");
        // Clear any previous custom validation message
        document.getElementById("new_first_name").setCustomValidity("");
        
        // Add event listener for input on the first name field
        document.getElementById("new_first_name").addEventListener("input", function (event) {
            // Call the validateFirstName function
            if (!validateFirstName()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Function to validate the first name
        function validateFirstName() {
            var newFirstName = document.getElementById("new_first_name").value;

            // Check if the field is empty
            if (newFirstName.trim() === "") {
                document.getElementById("new_first_name").setCustomValidity("No values can be empty");
                return false;
            } else {
                document.getElementById("new_first_name").setCustomValidity("");
            }

            // Check if the input contains only alphabetic characters, hyphens, and apostrophes
            if (!/^[a-zA-Z'-]+$/.test(newFirstName)) {
                document.getElementById("new_first_name").setCustomValidity("Invalid First Name");
                return false;
            } else {
                document.getElementById("new_first_name").setCustomValidity("");
            }

            return true; // Return true if validation passes
        }
    });
</script>

<script>
    // Function for validating the last name input field
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("profileForm");
        // Clear any previous custom validation message
        document.getElementById("new_last_name").setCustomValidity("");
        
        // Add event listener for input on the last name field
        document.getElementById("new_last_name").addEventListener("input", function (event) {
            // Call the validateLastName function
            if (!validateLastName()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Function to validate the last name
        function validateLastName() {
            var newLastName = document.getElementById("new_last_name").value;

            // Check if the field is empty
            if (newLastName.trim() === "") {
                document.getElementById("new_last_name").setCustomValidity("No values can be empty");
                return false;
            } else {
                document.getElementById("new_last_name").setCustomValidity("");
            }

            // Check if the input contains only alphabetic characters, hyphens, and apostrophes
            if (!/^[a-zA-Z'-]+$/.test(newLastName)) {
                document.getElementById("new_last_name").setCustomValidity("Invalid Last Name");
                return false;
            } else {
                document.getElementById("new_last_name").setCustomValidity("");
            }

            return true; // Return true if validation passes
        }
    });
</script>

<script>
    // Function for validating the diagnoses input field
    document.addEventListener("DOMContentLoaded", function () {
        var form = document.getElementById("profileForm");
        // Clear any previous custom validation message
        document.getElementById("new_diagnoses").setCustomValidity("");
        
        // Add event listener for input on the diagnoses field
        document.getElementById("new_diagnoses").addEventListener("input", function (event) {
            // Call the validateDiagnoses function
            if (!validateDiagnoses()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });

        // Function to validate the diagnoses
        function validateDiagnoses() {
            var newDiagnoses = document.getElementById("new_diagnoses").value;

            // Check if the field is empty
            if (newDiagnoses.trim() === "") {
                document.getElementById("new_diagnoses").setCustomValidity("No values can be empty");
                return false;
            } else {
                document.getElementById("new_diagnoses").setCustomValidity("");
            }

            // Check if the input contains only alphanumeric characters, apostrophes, commas, slashes, spaces, and hyphens
            if (!/^[a-zA-Z0-9',/\s-]+$/.test(newDiagnoses)) {
                document.getElementById("new_diagnoses").setCustomValidity("Invalid Diagnoses");
                return false;
            } else {
                document.getElementById("new_diagnoses").setCustomValidity("");
            }

            return true; // Return true if validation passes
        }
    });
</script>
</body>
</html>