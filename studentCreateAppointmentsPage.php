<?php

session_start();
require_once "config.php";

    // Retrieve all therapists from the database
$therapists = mysqli_query($link,"SELECT * FROM therapists");

// Initialize error variables
$therapistError = '';
$appointmentError = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the student ID from session
    $studentID = $_SESSION['user_id']; 

    // Check if the student has already requested an appointment that is unseen
    $checkQuery = "SELECT * FROM appointment_request WHERE studentID = ? AND status = 'unseen'";
    $checkStatement = mysqli_prepare($link, $checkQuery);
    mysqli_stmt_bind_param($checkStatement, "i", $studentID);
    mysqli_stmt_execute($checkStatement);
    $checkResult = mysqli_stmt_get_result($checkStatement);

    // If the student has already requested an appointment, display an error message
    if ($row = mysqli_fetch_assoc($checkResult)) {
        $appointmentError = '<p style="color: red;">*You have already requested an appointment.</p>';
    } else {
        // If the student has not requested an appointment, proceed with the request

        // Get the selected therapist ID and appointment description from the form
        $therapistID = $_POST['therapistID'];
        // If description is empty store it in the database as N/A
        $description = !empty($_POST['description']) ? $_POST['description'] : 'N/A';

        // Validate if a therapist is selected
        if ($therapistID === "--") {
            $therapistError = '<p style="color: red;">*Please select a Therapist</p>';
        } else {
            // Insert the appointment request into the database
            $insertQuery = "INSERT INTO appointment_request (studentID, therapistID, description, status) VALUES (?, ?, ?, 'unseen')";
            $statement = mysqli_prepare($link, $insertQuery);
            mysqli_stmt_bind_param($statement, "iis", $studentID, $therapistID, $description);
            $success = mysqli_stmt_execute($statement);

            // If the request is successful, redirect to booking confirmation page
            if ($success) {
                header("Location: bookingConfirmation.php");
                exit();
            } else {
                // If the request fails, display an error message
                echo "Failed to send the request.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "icon" href = "Images/Logo.png" type = "image/x-icon">
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
          
                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                      <li>
                        <a href="studentHomepage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-house" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"></svg>
                          Home
                        </a>
                      </li>
                      <li>
                        <a href="#" class="nav-link text-white">
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
                        <a href="studentProfilePage.php" class="nav-link text-white">
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
        <h4 class="fst-italic" style="text-align: center;">Create an Appointment</h4>
       <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="therapistInput">
        <h6>Select a Therapist</h6>
        <select class="form-select form-select-sm" name="therapistID">
            <option selected>--</option>
            <?php while ($row = mysqli_fetch_assoc($therapists)) { ?>
            <option value="<?php echo $row['therapistID']; ?>"><?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?></option>
            <?php } ?>
        </select>
        <?php echo $therapistError; ?>
        <div class="form-outline studentTextBox">
            <h6>Enter Additional Information/Preferred Times (Optional)</h6>
            <textarea class="form-control" id="textAreaExample1" name="description" rows="5"></textarea>           
          </div>
            <div class="text-center">
                <button type="submit" class="btn infoButton" name="submit">Send Request</button>
            </div>
            <?php echo $appointmentError; ?>
        </form>
    </div>

    <div class="row">
    <?php mysqli_data_seek($therapists, 0); ?>
    <?php while ($row = mysqli_fetch_assoc($therapists)) { ?>
        <div class="col-lg-4 therapistProfiles">
          <img src="<?php echo $row['therapist_image']; ?>" width="120" height="120" class="rounded-circle">
          <h4 class="fw-normal"><?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?></h4>
          <p>Specialises in: <?php echo $row['expertise']; ?></p>
          <p style="font-weight:Bold;">Room: <?php echo $row['room']; ?></p>
        </div>
        <?php } ?>
    </div>
</body>
</html>