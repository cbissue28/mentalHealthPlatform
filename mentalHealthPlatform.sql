-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 08, 2024 at 02:04 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mentalHealthPlatform`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointmentID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `therapistID` int(11) NOT NULL,
  `requestID` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time(6) NOT NULL,
  `status` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointmentID`, `studentID`, `therapistID`, `requestID`, `date`, `time`, `status`) VALUES
(10, 4, 5, 7, '2023-11-20', '14:30:00.000000', NULL),
(11, 3, 5, 8, '2023-11-20', '10:30:00.000000', NULL),
(12, 1, 5, 9, '2023-11-20', '12:00:00.000000', 'cancelled'),
(13, 5, 5, 10, '2023-12-11', '15:00:00.000000', 'cancelled'),
(15, 5, 5, 11, '2024-01-12', '14:00:00.000000', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_request`
--

CREATE TABLE `appointment_request` (
  `requestID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `therapistID` int(11) NOT NULL,
  `description` varchar(3000) NOT NULL,
  `status` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_request`
--

INSERT INTO `appointment_request` (`requestID`, `studentID`, `therapistID`, `description`, `status`) VALUES
(7, 4, 5, 'N/A', 'seen'),
(8, 3, 5, 'I have been experiencing a depression for the past two weeks', 'seen'),
(9, 1, 5, 'Recent academic pressures and being far from home has caused me to feel depressed and struggle to leave my room. (I am free on Tuesdays and Fridays)\r\n', 'seen'),
(10, 5, 5, 'I prefer Monday mornings and Friday afternoons', 'seen'),
(11, 5, 5, 'I am free on Mondays and Fridays', 'seen');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `studentID` int(11) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `diagnoses` varchar(150) NOT NULL,
  `emailAddress` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`studentID`, `first_name`, `last_name`, `diagnoses`, `emailAddress`, `password`) VALUES
(1, 'Daniel', 'Stevens', 'N/A', 'danstevens123@gmail.com', '$2y$10$5pUW7J.vMVwkD8uTJI2sauOw263VrOqk1tc3PwwqBmGrdPfIJpF/K'),
(2, 'Lisa', 'James', 'Generalised Anxiety Disorder', 'lisajames123@gmail.com', '$2y$10$toLs/rMYTTdzjnM3O.t5C.pOck3V1Dfnl.r3l2mMQ8pLDhiFtyEGa'),
(3, 'Melissa', 'Jones', 'N/A', 'meljones123@gmail.com', '$2y$10$2eWLEJM0pyQNFSStW1fv.u84/EVO4vyG6wPgMho6NRJ2naW3Q5pk.'),
(4, 'Matt', 'Towns', 'Mood disorder', 'matttowns123@gmail.com', '$2y$10$Y4IsNgodZkUaBv.0x2E6FOUVixZKMOAfrBB0TCB1jbA1JxNziEExi'),
(5, 'Maximilian', 'Smith', 'Anxiety, Depression', 'maxsmith123@gmail.com', '$2y$10$BETLFwDq2eU0Tz8ZOGtaX.Rcwp7Ijw0n8MrmoUrzErsnwHcpMtQL6');

-- --------------------------------------------------------

--
-- Table structure for table `therapists`
--

CREATE TABLE `therapists` (
  `therapistID` int(11) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `expertise` varchar(150) NOT NULL,
  `room` varchar(150) NOT NULL,
  `emailAddress` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `therapist_image` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `therapists`
--

INSERT INTO `therapists` (`therapistID`, `first_name`, `last_name`, `expertise`, `room`, `emailAddress`, `password`, `therapist_image`) VALUES
(1, 'Jill', 'Wilson', 'Depression, PTSD', 'CC101', 'jillwilson321@gmail.com', '$2y$10$er3JmKff5vSZt213CZwUyuCMFXK3NAcXXJtIlR0t05R5PVcYvxqEm', 'Images/Therapists/therapist1.png'),
(2, 'Mandy', 'Stewart', 'Anxiety', 'CC105', 'mandystewart321@gmail.com', '$2y$10$v8b7S2wGUuBvwVuEHBfkB.3YUMnxoG/ZwHR0PRfC13u9grTAoP0tq', 'Images/Therapists/therapist2.png'),
(3, 'Carl', 'Davis', 'Depression, Personality Disorders', 'CC103', 'carldavis321@gmail.com', '$2y$10$h8ew2qYEa4YtEw.3r5nug.kKaBOGRoEJHyGLTd9tfjhKoCCRq7ANW', 'Images/Therapists/therapist5.png'),
(4, 'Abdul', 'Salah', 'Body Dysmorphia, Mood Disorders', 'CC102', 'abdulsalah321@gmail.com', '$2y$10$1OW/kaz7rMokECYyxcT3.O73zKir2jyh.Dwb1pTb5YsBLZdSrj4HC', 'Images/Therapists/therapist4.png'),
(5, 'Janice', 'Leroy', 'ADHD, Anxiety', 'CC109 ', 'janiceleroy321@gmail.com', '$2y$10$BETLFwDq2eU0Tz8ZOGtaX.Rcwp7Ijw0n8MrmoUrzErsnwHcpMtQL6', 'Images/Therapists/therapist3.png'),
(6, 'Susan', 'Taylor', 'Depression, Anxiety, ADHD', 'CC107', 'susantaylor321@gmail.com', '$2y$10$Rts7rcDYAOv7aGvqikYrneuLislcwW5FW9yqvQbiMIYItH4H68CUG', 'Images/Therapists/therapist6.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointmentID`),
  ADD KEY `requestID` (`requestID`),
  ADD KEY `studentID` (`studentID`),
  ADD KEY `therapistID` (`therapistID`);

--
-- Indexes for table `appointment_request`
--
ALTER TABLE `appointment_request`
  ADD PRIMARY KEY (`requestID`),
  ADD KEY `studentID` (`studentID`),
  ADD KEY `therapistID` (`therapistID`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`studentID`);

--
-- Indexes for table `therapists`
--
ALTER TABLE `therapists`
  ADD PRIMARY KEY (`therapistID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `appointment_request`
--
ALTER TABLE `appointment_request`
  MODIFY `requestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `studentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `therapists`
--
ALTER TABLE `therapists`
  MODIFY `therapistID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`requestID`) REFERENCES `appointment_request` (`requestID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `students` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`therapistID`) REFERENCES `therapists` (`therapistID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `appointment_request`
--
ALTER TABLE `appointment_request`
  ADD CONSTRAINT `appointment_request_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `students` (`studentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointment_request_ibfk_2` FOREIGN KEY (`therapistID`) REFERENCES `therapists` (`therapistID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
