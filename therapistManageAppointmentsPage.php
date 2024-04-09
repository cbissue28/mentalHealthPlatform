<?php

session_start();

require_once "config.php";

// Get therapist ID from session
$therapistID = $_SESSION['user_id'];

// Get current date
$currentDate = date("Y-m-d");

// Query to select appointments for the therapist with dates that have not passed
$query = "SELECT a.*, s.first_name, s.last_name, s.diagnoses, ar.description
          FROM appointments a
          JOIN students s ON a.studentID = s.studentID
          LEFT JOIN appointment_request ar ON a.requestID = ar.requestID
          WHERE a.therapistID = ? AND a.date >= ?";

// Prepare and execute the query
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "is", $therapistID, $currentDate);
mysqli_stmt_execute($stmt);

// Get appointments result
$appointments = mysqli_stmt_get_result($stmt);

// Close the statement
mysqli_stmt_close($stmt);

// Initialize timeslot error message
$timeslotError = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  // Reschedule existing appointment
  if (isset($_POST['appointmentID'])) {
      $appointmentID = $_POST['appointmentID'];
      $date = $_POST['dateInput'];
      $time = $_POST['timeInput'];

      // Check if there's no timeslot error
      if ($timeslotError === null) {
          // Update appointment date and time
          $updateQuery = "UPDATE appointments SET date = ?, time = ? WHERE appointmentID = ?";
          $updateStmt = mysqli_prepare($link, $updateQuery);
          mysqli_stmt_bind_param($updateStmt, "ssi", $date, $time, $appointmentID);
          mysqli_stmt_execute($updateStmt);

          // Refresh the page
          header("Location: therapistManageAppointmentsPage.php");
          exit();
      }
  }

  // Book a follow up appointment based on request ID
  if (isset($_POST['requestID'])) {
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

      // If the student has an appointment booked on the selected date, set timeslot error
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

          // Loop through existing appointments to check timeslots
          while ($row = mysqli_fetch_assoc($existingAppointments)) {
              $existingTime = strtotime($row['time']);
              $selectedTime = strtotime($time);
              $difference = abs($selectedTime - $existingTime) / 60; 

              // If there's less than or equal to 30 minutes difference between appointments, set timeslot error
              if ($difference <= 30) {
                  $timeslotError = "* Appointment Booking FAILED, You already have a student booked in that half-hour timeslot";
                  break; 
              }
          }

          // If there's no timeslot error, book the appointment
          if ($timeslotError === null) {
              $insertQuery = "INSERT INTO appointments (studentID, therapistID, requestID, date, time) 
                              VALUES (?, ?, ?, ?, ?)";
              $insertStmt = mysqli_prepare($link, $insertQuery);
              mysqli_stmt_bind_param($insertStmt, "iiiss", $studentID, $therapistID, $requestID, $date, $time);
              mysqli_stmt_execute($insertStmt);

              // Redirect to manage appointments page
              header("Location: therapistManageAppointmentsPage.php");
              exit(); 
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
                        <a href="therapistHomepage.php" class="nav-link text-white">
                          <svg class="bi d-block mx-auto mb-1 bi-house" width="30" height="30" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"></svg>
                          Home
                        </a>
                      </li>
                      <li>
                        <a href="#" class="nav-link text-white">
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
        <h4 class="fst-italic" style="text-align: center;">Manage Appointments</h4>
         <?php } else { ?>
        <h4 class="fst-italic" style="text-align: center; color: red;"><?php echo $timeslotError; ?></h4>
         <?php } ?>

        <?php while ($row = mysqli_fetch_assoc($appointments)) { ?>
          <div class="rounded mt-3 appointmentBox">
            <h5>Appointment with <?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?> 
            <strong style="color: red; text-transform: uppercase;"><?php echo $row['status']; ?></strong></h5>
            <div class="row">
              <div class="col-sm-6">
                <p>Diagnoses: <?php echo $row['diagnoses']; ?></p>
              </div>
              <div class="col-sm-6 text-end">
                <p style="display: inline; margin-right: 10px;">Time: <?php echo date('h:i A', strtotime($row['time'])); ?></p>
                <p style="display: inline;">Date: <?php echo $row['date']; ?></p>
                <button type="button" class="btn infoButton rescheduleButton" data-bs-toggle="modal" data-bs-target="#rescheduleDateTimeModal">Reschedule</button>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                 <button type="button" class="btn infoButton" data-bs-toggle="modal" data-bs-target="#additionalInfoModal_<?php echo $row['appointmentID']; ?>">View additional info</button>
              </div>
              <div class="col-sm-6 text-end">
                 <button type="button" class="btn infoButton" data-bs-toggle="modal" data-bs-target="#followUpDateTimeModal">Book Follow-up</button>
              </div>
            </div>
          </div>


    <div class="modal" id="additionalInfoModal_<?php echo $row['appointmentID']; ?>">
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
    

    <div class="modal" id="rescheduleDateTimeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reschedule Date and Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="rescheduleForm">
                    <input type="hidden" name="appointmentID" value="<?php echo $row['appointmentID']; ?>">
                    <div class="mb-3">
                        <label for="rescheduleDateInput" class="form-label">Date:</label>
                        <input type="date" class="form-control" name="dateInput" id="rescheduleDateInput" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="rescheduleTimeInput" class="form-label">Time:</label>
                        <input type="time" class="form-control" name="timeInput" id="rescheduleTimeInput">
                    </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn-primary infoButton">Confirm</button>
              </div>
              </form>
            </div>
        </div>
    </div>
</div>

  <div class="modal" id="followUpDateTimeModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book a Date and Time</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="followUpForm">
                  <input type="hidden" name="requestID" value="<?php echo $row['requestID']; ?>">
                  <input type="hidden" name="studentID" value="<?php echo $row['studentID']; ?>">
                    <div class="mb-3">
                    <label for="followUpDateInput" class="form-label">Date:</label>
                    <input type="date" class="form-control" name="dateInput" id="followUpDateInput" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                      <label for="followUpTimeInput" class="form-label">Time:</label>
                      <input type="time" class="form-control" name="timeInput" id="followUpTimeInput">
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
</div>
      
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script>
   // Event listener for reschedule date input
document.getElementById("rescheduleDateInput").addEventListener("input", function() {
    // Get current date
    var currentDate = new Date();
    // Get selected date from input
    var selectedDate = new Date(this.value);

    // Check if selected date is the same as current date
    if (selectedDate.toDateString() === currentDate.toDateString()) {
        // If it is, set minimum time to current time
        var hours = currentDate.getHours();
        var minutes = currentDate.getMinutes();
        var time = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes;
        document.getElementById("rescheduleTimeInput").setAttribute("min", time);
    } else {
        // If not, remove minimum time attribute
        document.getElementById("rescheduleTimeInput").removeAttribute("min");
    }
});

// Event listener for reschedule time input
document.getElementById("rescheduleTimeInput").addEventListener("input", function() {
    var time = this.value;
    if (time) {
        var selectedTime = new Date("2000-01-01 " + time); 
        var startTime = new Date("2000-01-01 09:00");
        var endTime = new Date("2000-01-01 17:00");

        // Validate selected time
        if (selectedTime < startTime || selectedTime > endTime) {
            this.setCustomValidity("Please select a time between 9:00 AM and 5:00 PM");
        } else {
            this.setCustomValidity("");
        }
    }
});

// Event listener for follow-up date input
document.getElementById("followUpDateInput").addEventListener("input", function() {
    var currentDate = new Date();
    var selectedDate = new Date(this.value);

    // Similar to reschedule date input, set minimum time if selected date is today
    if (selectedDate.toDateString() === currentDate.toDateString()) {
        var hours = currentDate.getHours();
        var minutes = currentDate.getMinutes();
        var time = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes;
        document.getElementById("followUpTimeInput").setAttribute("min", time);
    } else {
        document.getElementById("followUpTimeInput").removeAttribute("min");
    }
});

// Event listener for follow-up time input
document.getElementById("followUpTimeInput").addEventListener("input", function() {
    var time = this.value;
    if (time) {
        var selectedTime = new Date("2000-01-01 " + time);
        var startTime = new Date("2000-01-01 09:00");
        var endTime = new Date("2000-01-01 17:00");

        // Validate selected time for follow-up
        if (selectedTime < startTime || selectedTime > endTime) {
            this.setCustomValidity("Please select a time between 9:00 AM and 5:00 PM");
        } else {
            this.setCustomValidity("");
        }
    }
});
</script>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        const rescheduleForm = document.getElementById('rescheduleForm');
        rescheduleForm.addEventListener('submit', function(event) {
            const dateInput = document.getElementById('rescheduleDateInput');
            const timeInput = document.getElementById('rescheduleTimeInput');

            if (dateInput.value.trim() === '' || timeInput.value.trim() === '') {
                event.preventDefault(); 
                alert('Date and time fields are required for reschedule.');
            }
        });

        const followUpForm = document.getElementById('followUpForm');
        followUpForm.addEventListener('submit', function(event) {
            const dateInput = document.getElementById('followUpDateInput');
            const timeInput = document.getElementById('followUpTimeInput');

            if (dateInput.value.trim() === '' || timeInput.value.trim() === '') {
                event.preventDefault(); 
                alert('Date and time fields are required for follow-up.');
            }
        });
    });
</script>
</body>
</html>