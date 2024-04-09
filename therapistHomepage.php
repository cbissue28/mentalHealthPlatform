<?php

session_start();

require_once "config.php";

$timeslotError = null;

// Get the therapist ID from the session
$therapistID = $_SESSION['user_id'];

// Query to fetch appointment requests for the therapist that are unseen
$query = "SELECT ar.*, s.first_name, s.last_name, s.diagnoses
          FROM appointment_request ar
          JOIN students s ON ar.studentID = s.studentID
          WHERE ar.therapistID = $therapistID AND ar.status = 'unseen'";

// Execute the query
$requests = mysqli_query($link, $query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $therapistID = $_SESSION['user_id'];

    // Extract data from the form submission
    $requestID = $_POST['requestID'];
    $studentID = $_POST['studentID'];
    $date = $_POST['dateInput'];
    $time = $_POST['timeInput'];

    // Check if the student already has an appointment booked on the selected date
    $checkStudentQuery = "SELECT * FROM appointments 
                      WHERE studentID = ? 
                      AND date = ? 
                      AND status != 'cancelled'";
    $checkStudentStmt = mysqli_prepare($link, $checkStudentQuery);
    mysqli_stmt_bind_param($checkStudentStmt, "is", $studentID, $date);
    mysqli_stmt_execute($checkStudentStmt);
    $result = mysqli_stmt_get_result($checkStudentStmt);

    // If the student already has an appointment, set error message
    if (mysqli_num_rows($result) > 0) {
        $timeslotError = "* Appointment Booking FAILED, Student already has an appointment booked on that day.";
    } else {
        // Check if the therapist already has an appointment booked in the selected timeslot
        $checkQuery = "SELECT * FROM appointments 
                       WHERE therapistID = ? 
                       AND date = ?
                       AND status != 'cancelled'";
        $checkStmt = mysqli_prepare($link, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "is", $therapistID, $date);
        mysqli_stmt_execute($checkStmt);
        $existingAppointments = mysqli_stmt_get_result($checkStmt);

        // Loop through existing appointments to check for overlapping timeslots
        while ($row = mysqli_fetch_assoc($existingAppointments)) {
            $existingTime = strtotime($row['time']);
            $selectedTime = strtotime($time);

            $difference = abs($selectedTime - $existingTime) / 60;

            // If the timeslots overlap, set error message and break the loop
            if ($difference <= 30) {
                $timeslotError = "* Appointment Booking FAILED, You already have a student booked in that half-hour timeslot";
                break;
            }
        }

         // If no timeslot conflict, insert appointment and update request status
        if ($timeslotError === null) {
            // Insert appointment into the database
            $insertQuery = "INSERT INTO appointments (studentID, therapistID, requestID, date, time) 
                VALUES (?, ?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($link, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, "iiiss", $studentID, $therapistID, $requestID, $date, $time);
            mysqli_stmt_execute($insertStmt);

            // Update the request status to 'seen'
            $updateQuery = "UPDATE appointment_request SET status = 'seen' WHERE requestID = ?";
            $updateStmt = mysqli_prepare($link, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, "i", $requestID);
            mysqli_stmt_execute($updateStmt);

            // Redirect to therapist homepage after successful booking
            header("Location: therapistHomepage.php");
            exit();
        }
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
                    <a href="therapistHomepage.php" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
                      <img src="Images/solidLogo.png" style="height: 60pt;">
                    </a>
          
                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small navIcons">
                      <li>
                        <a href="#" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-house" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"></svg>
                          Home
                        </a>
                      </li>
                      <li>
                        <a href="therapistManageAppointmentsPage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-book" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"></svg>
                          Manage Appointments
                        </a>
                      </li>
                      <li>
                        <a href="therapistAppointmentHistoryPage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-calendar-event" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"></svg>
                          Appointment History
                        </a>
                      </li>
                      <li>
                        <a href="therapistProfilePage.php" class="nav-link text-white">
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
        <?php if ($timeslotError === "") { ?>
        <h4 class="fst-italic" style="text-align: center;">Appointment requests</h4>
         <?php } else { ?>
        <h4 class="fst-italic" style="text-align: center; color: red;"><?php echo $timeslotError; ?></h4>
         <?php } ?>
            
        <?php if (mysqli_num_rows($requests) === 0) { ?>
        <div class="rounded mt-3 appointmentBox">
            <p style="text-align: center; font-weight: bold;">You currently have no new appointment requests</p>
        </div>
         <?php } else{

        mysqli_data_seek($requests, 0);
        while ($row = mysqli_fetch_assoc($requests)) { ?>
        <div class="rounded mt-3 appointmentBox">
          <h5><?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?> has requested an appointment with you</h5>
            <p>Diagnoses: <?php echo $row['diagnoses']; ?></p>
          <div class="row">
            <div class="col-sm-6">
                <button type="button" class="btn infoButton" data-bs-toggle="modal" data-bs-target="#additionalInfoModal_<?php echo $row['requestID']; ?>">View additional info</button>
            </div>
            <div class="col-sm-6 text-end">
                 <button type="button" class="btn infoButton" data-bs-toggle="modal" data-bs-target="#dateTimeModal">Confirm date and time</button>
          </div>
        </div>
    </div>

    <div class="modal" id="additionalInfoModal_<?php echo $row['requestID']; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Information provided by student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><?php echo $row['description']; ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn infoButton" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
         
    

    <div class="modal" id="dateTimeModal">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Schedule Date and Time</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="appointmentForm">
                  <input type="hidden" name="requestID" value="<?php echo $row['requestID']; ?>">
                  <input type="hidden" name="studentID" value="<?php echo $row['studentID']; ?>">

                      <div class="mb-3">
                          <label for="dateInput" class="form-label">Date:</label>
                          <input type="date" class="form-control" id="dateInput" name="dateInput" min="<?php echo date('Y-m-d'); ?>">
                      </div>
                      <div class="mb-3">
                          <label for="timeInput" class="form-label">Time:</label>
                          <input type="time" class="form-control" id="timeInput" name="timeInput">
                      </div>       
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary infoButton">Confirm</button>
              </div>
              </form>
          </div>
      </div>
  </div>
  <?php } ?>
  <?php } ?>
  </div>

      <div class="container">
        <div class="row row-cols-1 row-cols-sm-4 row-cols-md-4 g-4">
          <div class="col">
            <div class="card shadow-sm">
               <a class="serviceLinks" href="https://www.nhs.uk/nhs-services/mental-health-services/how-to-find-local-mental-health-services/" target="_blank"><img src="Images/nhsNurse.png" width="100%" height="225">
              <div class="card-body">
                <h3 style="text-align: center;">NHS</h3>
                <p class="card-text"> Discover mental health services provided by the NHS.
                </p></a>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card shadow-sm">
                <a class="serviceLinks" href="https://www.samaritans.org/how-we-can-help/if-youre-worried-about-someone-else/" target="_blank"><img src="Images/depressedStudent.png" width="100%" height="225">
              <div class="card-body">
                <h3 style="text-align: center;">Concerning student?</h3>
                <p class="card-text">If a student is showing suicidal behaviour, find out how to report it.
                </p></a>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card shadow-sm">
                <a class="serviceLinks" href="https://www.gmc-uk.org/registration-and-licensing/the-medical-register" target="_blank"><img src="Images/psychiatrist.png" width="100%" height="225">
              <div class="card-body">
                <h3 style="text-align: center;">Find a Psychiatrist</h3>
                <p class="card-text">Search for the details of a physchiatrist to refer a student to.</p></a>
              </div>
            </div>
          </div>
  
          <div class="col">
            <div class="card shadow-sm">
                <a class="serviceLinks" href="https://www.google.com/search?q=mental+health+students&sca_esv=576560510&rlz=1C5CHFA_enGB874GB876&tbm=nws&tbas=0&source=lnt&sa=X&ved=2ahUKEwitzIvt4JGCAxXXRkEAHRHEAwMQpwV6BAgBEBI&biw=1440&bih=815&dpr=2" target="_blank"><img src="Images/universityStudents.png" width="100%" height="225">
              <div class="card-body">
                <h3 style="text-align: center;">Latest Articles</h3>
                <p class="card-text">Discover the latest articles regarding student mental health.
                </p></a>
              </div>
            </div>
          </div>
      </div>
      
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
      
      <script>
    // Event listener for date input
    document.getElementById("dateInput").addEventListener("input", function() {
        // Get current date
        var currentDate = new Date();
        // Get selected date from input
        var selectedDate = new Date(this.value);

        // Check if selected date is today
        if (selectedDate.toDateString() === currentDate.toDateString()) {
            // Get current time
            var hours = currentDate.getHours();
            var minutes = currentDate.getMinutes();
            // Format time
            var time = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes;
            // Set minimum time for time input
            document.getElementById("timeInput").setAttribute("min", time);
        } else {
            // Remove minimum time for time input
            document.getElementById("timeInput").removeAttribute("min");
        }
    }); 
</script>

<script>
    // Event listener for time input
    document.getElementById("timeInput").addEventListener("input", function() {
        var time = this.value;
        if (time) {
            // Create Date objects for selected time, start time (9:00 AM), and end time (5:00 PM)
            var selectedTime = new Date("2000-01-01 " + time); 
            var startTime = new Date("2000-01-01 09:00");
            var endTime = new Date("2000-01-01 17:00");

            // Check if selected time is within working hours
            if (selectedTime < startTime || selectedTime > endTime) {
                // Set custom validity message if time is outside working hours
                this.setCustomValidity("Please select a time between 9:00 AM and 5:00 PM");
            } else {
                // Clear custom validity message if time is within working hours
                this.setCustomValidity("");
            }
        }
    });
</script>

<script>
    // Event listener for form submission
    window.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('appointmentForm');
        form.addEventListener('submit', function(event) {
            const dateInput = document.getElementById('dateInput');
            const timeInput = document.getElementById('timeInput');

            // Check if date and time fields are empty on form submission
            if (dateInput.value.trim() === '' || timeInput.value.trim() === '') {
                event.preventDefault(); // Prevent form submission
                alert('Date and time fields are required.'); // Display alert message
            }
        });
    });
</script>

</body>
</html>