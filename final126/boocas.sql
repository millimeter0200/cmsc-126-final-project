-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 05:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boocas`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `first_name`, `middle_initial`, `last_name`, `email`, `password`) VALUES
(1, 'Jane', 'B', 'Bigay', 'jane.b@up.edu.ph', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `manages`
--

CREATE TABLE `manages` (
  `reservationID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `remark` text DEFAULT NULL,
  `action_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservationID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `roomID` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `adviser` varchar(100) NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `purpose` text NOT NULL,
  `submission_date` datetime NOT NULL,
  `reservation_date` date NOT NULL,
  `duration` int(11) NOT NULL
) ;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservationID`, `studentID`, `roomID`, `start_time`, `end_time`, `adviser`, `status`, `purpose`, `submission_date`, `reservation_date`, `duration`) VALUES
(1, 1, 101, '13:00:00', '16:00:00', 'Sir Poy', 'Rejected', 'Group Study', '2025-05-20 19:07:18', '2025-05-12', 180),
(2, 1, 3, '09:00:00', '14:30:00', 'Sir Poy', 'Pending', 'Lab Work', '2025-05-20 19:07:18', '2025-05-10', 330),
(3, 1, 101, '10:00:00', '12:00:00', 'Sir Poy', 'Pending', 'Meeting', '2025-05-01 10:00:00', '2025-05-01', 120),
(4, 1, 3, '14:00:00', '17:00:00', 'Sir Poy', 'Approved', 'Experiment', '2025-04-28 10:00:00', '2025-04-28', 180);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `roomID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`roomID`, `name`, `type`) VALUES
(2, 'PL2', 'Laboratory'),
(3, 'CL3', 'Classroom'),
(101, 'Room 101', 'Classroom'),
(106, '106', 'Classroom');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentID` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentID`, `first_name`, `middle_initial`, `last_name`, `email`, `password`) VALUES
(1, 'John', 'A', 'dela Cruz', 'john.dc@up.edu.ph', 'password1');

-- --------------------------------------------------------

--
-- Table structure for table `timeslot`
--

CREATE TABLE `timeslot` (
  `timeslotID` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ;

--
-- Dumping data for table `timeslot`
--

INSERT INTO `timeslot` (`timeslotID`, `start_time`, `end_time`) VALUES
(1, '07:00:00', '07:30:00'),
(2, '07:30:00', '08:00:00'),
(3, '08:00:00', '08:30:00'),
(4, '08:30:00', '09:00:00'),
(5, '09:00:00', '09:30:00'),
(6, '09:30:00', '10:00:00'),
(7, '10:00:00', '10:30:00'),
(8, '10:30:00', '11:00:00'),
(9, '11:00:00', '11:30:00'),
(10, '11:30:00', '12:00:00'),
(11, '12:00:00', '12:30:00'),
(12, '12:30:00', '13:00:00'),
(13, '13:00:00', '13:30:00'),
(14, '13:30:00', '14:00:00'),
(15, '14:00:00', '14:30:00'),
(16, '14:30:00', '15:00:00'),
(17, '15:00:00', '15:30:00'),
(18, '15:30:00', '16:00:00'),
(19, '16:00:00', '16:30:00'),
(20, '16:30:00', '17:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `manages`
--
ALTER TABLE `manages`
  ADD PRIMARY KEY (`reservationID`,`adminID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservationID`),
  ADD KEY `studentID` (`studentID`),
  ADD KEY `roomID` (`roomID`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`roomID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `timeslot`
--
ALTER TABLE `timeslot`
  ADD PRIMARY KEY (`timeslotID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `timeslotID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `manages`
--
ALTER TABLE `manages`
  ADD CONSTRAINT `manages_ibfk_1` FOREIGN KEY (`reservationID`) REFERENCES `reservation` (`reservationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `manages_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`) ON DELETE CASCADE;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`studentID`) REFERENCES `student` (`studentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
