-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 05, 2025 at 01:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','teacher','staff') NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `status` enum('present','absent','late','excused') NOT NULL,
  `fingerprint_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `user_type`, `class_id`, `date`, `time_in`, `time_out`, `status`, `fingerprint_id`) VALUES
(44, 71, 'student', 9, '2025-07-01', '10:00:00', '00:00:00', 'present', NULL),
(45, 86, 'student', 9, '2025-07-01', '10:00:00', '00:00:00', 'present', NULL),
(46, 64, 'student', 3, '2025-07-01', '20:55:00', '00:00:00', 'present', NULL),
(47, 65, 'student', 3, '2025-07-01', '20:50:00', '00:00:00', 'present', NULL),
(57, 86, 'student', 9, '2025-07-02', '22:29:00', '00:00:00', 'present', NULL),
(58, 7, 'teacher', NULL, '2025-07-01', '00:00:00', '00:00:00', 'present', NULL),
(59, 7, 'teacher', NULL, '2025-06-30', '22:55:00', '00:00:00', 'present', NULL),
(60, 64, 'student', 3, '2025-06-30', '00:00:00', '00:00:00', 'present', NULL),
(61, 65, 'student', 3, '2025-06-30', '22:57:00', '00:00:00', 'present', NULL),
(62, 8, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(63, 60, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(64, 62, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(65, 81, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(66, 82, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(67, 83, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(68, 84, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(69, 85, 'teacher', NULL, '2025-06-30', '22:58:00', '00:00:00', 'present', NULL),
(70, 8, 'teacher', NULL, '2025-07-01', '11:06:00', '00:00:00', 'present', NULL),
(71, 7, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(72, 8, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(73, 60, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(74, 62, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(75, 81, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(76, 82, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(77, 83, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(78, 84, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(79, 85, 'teacher', NULL, '2025-07-02', '12:01:00', '00:00:00', 'present', NULL),
(80, 7, 'teacher', NULL, '2025-07-03', '10:00:00', '00:00:00', 'present', NULL),
(81, 7, 'teacher', NULL, '2025-07-04', '10:10:00', '00:00:00', 'present', NULL),
(82, 8, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(83, 60, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(84, 62, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(85, 81, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(86, 82, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(87, 83, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(88, 84, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(89, 85, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(90, 94, 'teacher', NULL, '2025-07-04', '14:31:00', '00:00:00', 'present', NULL),
(91, 64, 'student', 3, '2025-07-04', '10:00:00', '00:00:00', 'present', NULL),
(92, 65, 'student', 3, '2025-07-04', '10:00:00', '00:00:00', 'present', NULL),
(93, 66, 'student', 3, '2025-07-04', '10:00:00', '00:00:00', 'present', NULL),
(94, 67, 'student', 3, '2025-07-04', '10:01:00', '00:00:00', 'present', NULL),
(95, 70, 'student', 4, '2025-07-04', '10:00:00', '00:00:00', 'present', NULL),
(96, 64, 'student', 3, '2025-07-03', '00:00:00', '00:00:00', 'absent', NULL),
(97, 65, 'student', 3, '2025-07-03', '00:00:00', '00:00:00', 'absent', NULL),
(98, 66, 'student', 3, '2025-07-03', '22:00:45', '00:00:00', 'present', NULL),
(99, 67, 'student', 3, '2025-07-03', '22:00:45', '00:00:00', 'present', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `is_deleted`) VALUES
(3, 'Class 3', 0),
(4, 'Class 4', 0),
(5, 'Class 5', 0),
(6, 'Class 6', 0),
(7, 'Class 7', 0),
(8, 'Class 8', 0),
(9, 'Class 9', 0),
(10, 'Class 10-New', 0),
(11, 'Class 10-Old', 0),
(13, 'newClass', 0);

-- --------------------------------------------------------

--
-- Table structure for table `class_dept_sec`
--

CREATE TABLE `class_dept_sec` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_dept_sec`
--

INSERT INTO `class_dept_sec` (`id`, `class_id`, `department_id`, `section_id`, `is_deleted`) VALUES
(158, 4, NULL, 2, 0),
(159, 4, NULL, 1, 0),
(160, 4, NULL, 3, 0),
(161, 5, 3, NULL, 0),
(162, 5, 2, NULL, 0),
(163, 5, 1, NULL, 0),
(164, 6, 1, 1, 0),
(165, 6, 1, 3, 0),
(166, 6, 3, NULL, 0),
(167, 6, 1, NULL, 0),
(168, 6, 2, NULL, 0),
(169, 6, 1, 2, 0),
(170, 6, 3, 9, 0),
(171, 6, 2, 1, 0),
(172, 6, 3, 3, 0),
(173, 6, 2, 2, 0),
(174, 7, NULL, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_dept_sub`
--

CREATE TABLE `class_dept_sub` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_dept_sub`
--

INSERT INTO `class_dept_sub` (`id`, `class_id`, `department_id`, `subject_id`, `is_deleted`) VALUES
(511, 3, NULL, 1, 0),
(512, 3, NULL, 3, 0),
(513, 3, NULL, 2, 0),
(514, 4, NULL, 3, 0),
(515, 4, NULL, 2, 0),
(516, 4, NULL, 1, 0),
(517, 4, NULL, 4, 0),
(518, 5, 1, 7, 0),
(519, 5, 1, 6, 0),
(520, 5, 1, 9, 0),
(521, 5, 2, 13, 0),
(522, 5, 3, 16, 0),
(523, 5, 3, 15, 0),
(524, 5, 3, 17, 0),
(525, 5, 2, 14, 0),
(526, 5, 3, 18, 0),
(527, 6, 1, 6, 0),
(528, 6, 1, 7, 0),
(529, 6, 2, 14, 0),
(530, 6, 1, 9, 0),
(531, 6, 1, 8, 0),
(532, 6, 3, 16, 0),
(533, 6, 3, 15, 0),
(534, 6, 2, 13, 1),
(535, 6, 3, 17, 0),
(536, 6, 2, 6, 1),
(537, 6, 2, 18, 0);

-- --------------------------------------------------------

--
-- Table structure for table `class_dept_sub_teacher`
--

CREATE TABLE `class_dept_sub_teacher` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_dept_sub_teacher`
--

INSERT INTO `class_dept_sub_teacher` (`id`, `class_id`, `department_id`, `subject_id`, `teacher_id`, `is_deleted`) VALUES
(286, 3, NULL, NULL, 21, 0),
(287, 3, NULL, NULL, 17, 0),
(288, 3, NULL, 1, 17, 0),
(289, 3, NULL, 3, 17, 1),
(290, 3, NULL, 2, 17, 0),
(291, 3, NULL, 3, 21, 0),
(292, 3, NULL, 2, 21, 1),
(293, 3, NULL, 1, 21, 0),
(294, 6, NULL, NULL, 14, 0),
(295, 6, NULL, NULL, 13, 0),
(296, 6, NULL, NULL, 15, 0),
(297, 6, 3, NULL, 13, 0),
(298, 6, 3, 15, 13, 0),
(299, 6, 3, 16, 13, 0),
(300, 6, 3, 17, 13, 1),
(301, 4, NULL, NULL, 12, 0),
(302, 4, NULL, NULL, 2, 0),
(303, 4, NULL, NULL, 15, 0),
(304, 4, NULL, NULL, 13, 0),
(305, 4, NULL, NULL, 17, 0),
(306, 4, NULL, NULL, 16, 0),
(307, 4, NULL, NULL, 1, 0),
(308, 4, NULL, NULL, 14, 0),
(309, 4, NULL, 2, 1, 0),
(310, 4, NULL, 1, 1, 0),
(311, 4, NULL, 3, 2, 0),
(312, 4, NULL, 4, 2, 0),
(313, 4, NULL, 4, 12, 0),
(314, 4, NULL, 4, 13, 0),
(315, 4, NULL, 3, 14, 0),
(316, 4, NULL, 2, 15, 0),
(317, 5, NULL, NULL, 1, 0),
(318, 5, NULL, NULL, 10, 0),
(319, 5, NULL, NULL, 15, 0),
(320, 5, NULL, NULL, 12, 0),
(321, 5, NULL, NULL, 16, 0),
(322, 5, NULL, NULL, 14, 0),
(323, 5, NULL, NULL, 17, 0),
(324, 5, NULL, NULL, 21, 0),
(325, 5, NULL, NULL, 13, 0),
(326, 5, NULL, NULL, 2, 0),
(327, 5, 1, 9, 15, 0),
(328, 5, 3, NULL, 15, 0),
(329, 5, 1, NULL, 15, 0),
(330, 5, 3, 16, 15, 0),
(331, 5, 1, NULL, 16, 0),
(332, 5, 1, 6, 16, 0),
(333, 5, 3, NULL, 21, 0),
(334, 5, 3, 15, 21, 0),
(335, 5, 2, NULL, 12, 0),
(336, 5, 2, 14, 12, 0),
(337, 5, 2, 13, 12, 0),
(338, 5, 2, NULL, 2, 0),
(339, 5, 2, 13, 2, 0),
(340, 5, 1, 9, 10, 0),
(341, 5, 1, 7, 10, 0),
(342, 5, 1, NULL, 10, 0),
(343, 5, 3, 16, 10, 0),
(344, 5, 3, 17, 10, 0),
(345, 5, 3, NULL, 10, 0),
(346, 5, 3, 18, 10, 0),
(347, 6, 1, 6, 14, 0),
(348, 6, 1, 7, 14, 0),
(349, 6, 1, NULL, 14, 0),
(350, 6, 2, 14, 15, 0),
(351, 6, 2, 18, 15, 0),
(352, 6, 2, NULL, 15, 0),
(353, 6, 1, 9, 13, 0),
(354, 6, 1, NULL, 13, 0),
(355, 6, 1, 9, 14, 0),
(356, 6, 1, 9, 15, 0),
(357, 6, 1, NULL, 15, 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `is_deleted`) VALUES
(1, 'Science', 0),
(2, 'Arts', 0),
(3, 'Commerce', 0),
(6, 'newDept', 0),
(7, 'aaaaaaass', 1);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `exam_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `class_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `exam_type_id` int(11) DEFAULT NULL,
  `academic_year` int(11) DEFAULT NULL,
  `exam_status` enum('draft','published','completed','cancelled') DEFAULT 'draft',
  `instructions` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `exam_name`, `description`, `class_id`, `department_id`, `section_id`, `created_by`, `creation_date`, `exam_type_id`, `academic_year`, `exam_status`, `instructions`, `is_deleted`) VALUES
(28, 'Class 6_All Department_All Section_First Term_2025', NULL, 6, NULL, NULL, 'dev_user', '2025-07-04 19:25:51', 4, 2025, 'draft', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_attendance`
--

CREATE TABLE `exam_attendance` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `exam_subject_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `status` enum('present','absent','late','excused') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_subjects`
--

CREATE TABLE `exam_subjects` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `total_marks` int(11) NOT NULL,
  `pass_mark` int(11) NOT NULL,
  `exam_status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `room_number` varchar(50) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_subjects`
--

INSERT INTO `exam_subjects` (`id`, `exam_id`, `class_id`, `department_id`, `subject_id`, `exam_date`, `start_time`, `end_time`, `teacher_id`, `total_marks`, `pass_mark`, `exam_status`, `room_number`, `instructions`, `created_at`, `updated_at`, `is_deleted`) VALUES
(7, 28, 6, 2, 14, '2025-07-10', '09:00:00', '11:00:00', 15, 100, 35, 'scheduled', '700', NULL, '2025-07-04 19:26:57', '2025-07-04 19:26:57', 0),
(8, 28, 6, 3, 16, '2025-07-10', '09:00:00', '11:00:00', 13, 100, 35, 'scheduled', '708', NULL, '2025-07-04 19:32:56', '2025-07-04 19:32:56', 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_types`
--

CREATE TABLE `exam_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_types`
--

INSERT INTO `exam_types` (`id`, `type_name`, `description`, `is_deleted`) VALUES
(4, 'First Term', 'First Term Description', 0),
(9, 'Mid Term', 'Mid Term Exam on School', 0),
(10, 'Final Term', 'Final Term Exam on School', 0),
(11, 'Admisson Test', 'Admisson Test for Classes', 0),
(12, 'Quiz Tests', 'Quiz Tests for School', 0);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `exam_subject_id` int(11) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `marks_obtained` decimal(6,2) DEFAULT NULL,
  `total_marks` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `grade_letter` varchar(5) DEFAULT NULL,
  `grade_point` decimal(3,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL,
  `marked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `user_id`, `first_name`, `last_name`, `gender`, `email`, `phone`, `address`, `profile_picture`, `created_at`, `is_deleted`) VALUES
(1, 78, 'Local', 'Guardian', 'male', 'localguardian@email.com', '00112233', 'Chilahati', 'parent_68629a59815ed3.24029862.jfif', '2025-06-30 16:43:27', 0),
(2, 79, 'Father', 'abcdefghij', 'male', 'adbcd@gmail.com', '0011223344', 'address line 1', 'parent_68626a7f3d09b9.73114024.jpeg', '2025-06-30 16:44:15', 0),
(3, 80, 'Mother', '1', 'female', 'abdh@gmail.com', '098765', 'address line 22', 'parent_68626a9e5f4ca7.99396264.jpg', '2025-06-30 16:44:46', 0),
(4, 95, 'new', 'Parent', 'male', 'newParent@email.com', '0098877', 'newPaerntAddress', 'parent_6866c806445701.89363345.jfif', '2025-07-04 00:00:10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `is_deleted`) VALUES
(1, 'Red', 0),
(2, 'Green', 0),
(3, 'Blue', 0),
(9, 'NewSection', 0),
(10, 'aaaaaassss', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `father_id` int(11) DEFAULT NULL,
  `mother_id` int(11) DEFAULT NULL,
  `local_guardian_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `roll_no` int(11) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `current_class_id` int(11) DEFAULT NULL,
  `current_section_id` int(11) DEFAULT NULL,
  `current_department_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `father_id`, `mother_id`, `local_guardian_id`, `user_id`, `first_name`, `last_name`, `roll_no`, `dob`, `gender`, `address`, `admission_date`, `current_class_id`, `current_section_id`, `current_department_id`, `profile_picture`, `note`, `is_deleted`) VALUES
(46, 1, 3, 2, 64, 'Md. Robiul', 'Islam', 1, '2000-01-01', 'male', 'Address', '2025-01-01', 3, 1, NULL, 'student_68626de041c9d3.45757257.jpeg', '', 0),
(47, NULL, NULL, NULL, 65, 'student3-2', 'last Name', 2, '2000-01-01', 'female', 'Address', '2025-01-01', 3, NULL, NULL, 'student_68626e04ca5f36.08268898.jpg', '', 0),
(48, NULL, NULL, NULL, 66, 'student3-4', '', 3, '2000-01-01', 'female', '', '2025-01-01', 3, NULL, NULL, 'student_68626fb1ab2bb4.26926742.jpeg', '', 0),
(49, NULL, NULL, NULL, 67, 'student3-5', '', 4, '2000-01-01', 'male', '', '2024-01-01', 3, NULL, NULL, 'student_68626e7c4f1089.41379022.jpg', '', 0),
(50, NULL, NULL, NULL, 70, 'student4-1', '', 1, '2000-01-01', 'male', 'n/a', '2025-01-10', 4, 1, NULL, NULL, 'student on class 4 roll no 1', 0),
(51, NULL, NULL, NULL, 71, 'student9-1', 'LastName', 1, '2000-01-01', 'male', 'Chilahati', '2025-01-01', 9, 3, 1, NULL, 'student 10 roll 1 science blue, no parent', 0),
(52, 2, 3, 1, 74, 'student 6-1', '', 1, '2000-01-01', 'male', 'Address 6-1', '2025-01-01', 5, 3, 1, NULL, 'new note added', 0),
(53, 2, 3, 1, 86, 'student9-2', 'lastName', 2, '2001-01-01', 'male', 'Chilahati', '2025-01-01', 9, NULL, NULL, NULL, '', 0),
(54, NULL, NULL, NULL, 87, 'aaaaa', 'bbbbb', 10, '2006-01-01', 'male', '', '2025-01-10', 9, NULL, 1, NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_history`
--

CREATE TABLE `student_history` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `result` varchar(50) DEFAULT NULL,
  `attendance_summary` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `is_deleted`) VALUES
(1, 'Bangla', 0),
(2, 'English', 0),
(3, 'Math', 0),
(4, 'Islamic Studies', 0),
(5, 'Sociology', 0),
(6, 'Biology', 0),
(7, 'Physics', 0),
(8, 'Chemistry', 0),
(9, 'Higher Math', 0),
(10, 'English (1)', 0),
(11, 'English (2)', 0),
(12, 'General Science', 0),
(13, 'Fine Arts', 0),
(14, 'History', 0),
(15, 'Statistics', 0),
(16, 'Accounting', 0),
(17, 'Management', 0),
(18, 'newSubject', 0);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `leave_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `email`, `dob`, `gender`, `address`, `position`, `profile_picture`, `join_date`, `leave_date`, `salary`, `is_deleted`) VALUES
(1, 7, 'Abdul', 'Jabbar', '01234-112233', '', '0000-00-00', 'male', 'Address Line-1', 'Senior Teacher', 'teacher_6861813c200c51.84927770.jpeg', '2023-01-01', '0000-00-00', 20000.00, 0),
(2, 8, 'Jannatul', 'Ferdaus', '01234-112233', '', '1990-01-01', 'female', 'Gosaigonj, Chilahati', 'Junior Teacher', 'teacher_686182c23e2b30.32612503.jpg', '2024-01-06', '0000-00-00', 12000.00, 0),
(10, 60, 'Sirajul', 'Islam', '01234-223344', '', '1980-01-01', 'male', 'Chilahati', 'Assistant Teacher', NULL, '2015-01-01', '0000-00-00', 11000.00, 0),
(11, 61, 'Teacher61', 'Lastname', NULL, NULL, '1980-01-01', 'male', 'Teacher Address', 'Teacher', NULL, '2015-01-01', NULL, 25000.00, 1),
(12, 62, 'Fatima', 'Khatun', '01234-332211', '', '1980-01-01', 'female', 'Chilahati', 'Senior Teacher', NULL, '2015-01-01', '0000-00-00', 18000.00, 0),
(13, 81, 'Golam', 'Rabbani', '01234-445566', '', '1990-01-01', 'male', 'Ambari, Domar, Nilphamari', 'Assistant Teacher', 'teacher_6862776d1467f6.22628459.jpeg', '2018-01-01', '0000-00-00', 15000.00, 0),
(14, 82, 'Ashraful', 'Islam', '0123456789', 'noemail@email.com', '1950-01-01', 'male', 'Rajshahi, Bangladesh', 'Principal', 'teacher_68627cbd9c3978.81834288.jfif', '1990-01-01', '0000-00-00', 20000.00, 0),
(15, 83, 'Manik', 'Sir', '012345678', 'maniksir@email.com', '1951-01-01', 'male', 'Chilahati, Domar', 'Asst. Principal', 'teacher_68627df0b37cd7.36783859.jfif', '1991-01-01', '0000-00-00', 19000.00, 0),
(16, 84, 'Nobiul', 'Sir', '01234567', 'nobiulsir@email.com', '1952-01-01', 'male', 'Vogdaburi, Chilahati', 'Senior Teacher', 'teacher_686280a8de2ca0.42226251.jfif', '1993-01-01', '0000-00-00', 18000.00, 0),
(17, 85, 'Moulvi', 'Sir', '00998877', 'moulvisir@email.com', '1970-01-01', 'male', 'Ketibari, Chilahati', 'General Teacher', NULL, '1980-01-01', '0000-00-00', 10000.00, 0),
(21, 94, 'new', 'Teacher', '0987', 'newEmail@email.com', '1999-01-01', 'male', 'newAddress', 'newPosition', NULL, '2025-01-01', '0000-00-00', 5000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student','parent','staff') NOT NULL,
  `fingerprint_id` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `fingerprint_id`, `profile_picture`, `created_at`, `is_deleted`) VALUES
(1, 'erfan.alam433', '$2y$10$GW2ek9pGcufYDAW4jsvZb.da3LPX9nwKczvfnFMfYE1ihWpKrtEoG', 'student', NULL, NULL, '2025-06-29 13:39:04', 0),
(2, 'erfan.alam490', '$2y$10$TVK.T1CZ9imW5g9Uy8kRRuxgSew5do1D6jOjT2.gnxpKyr9ahKzNO', 'student', NULL, NULL, '2025-06-29 13:42:57', 0),
(3, 'erfan.alam747', '$2y$10$ibwIXgiJ/FJjoqSJOfc/qur6pBCjPaNY0MOhCv0KPmolqrg7mWhUm', 'student', NULL, NULL, '2025-06-29 14:16:57', 0),
(4, 'erfan.alam237', '$2y$10$NU8GgN9EjIUSb9CbbUySrObx0uhJlq0zi9CM96bzMY8KpOhwHHb5m', 'student', NULL, NULL, '2025-06-29 14:17:39', 0),
(5, 'erfan.alam152', '$2y$10$B.3OloXHD4zWKdhgNm.C4ebqW7UK3AlLV8Z8JdjCoM/ph9WrRourW', 'student', NULL, NULL, '2025-06-29 14:17:40', 0),
(6, 'erfan.alam680', '$2y$10$X5SN0V8RTgnaMB6.4Tko2evTgGBbVsxyYnAzEsgzxxMKPI0Hr1SLa', 'student', NULL, NULL, '2025-06-29 14:40:25', 0),
(7, 'abdul.karim276', '$2y$10$vmF7zr3VGw6KUkHgJ6JOO.bIzsVZm7VU0eF04hU8RYu32JhbjuqIy', 'teacher', NULL, NULL, '2025-06-29 18:08:39', 0),
(8, 'jannatul.ferdaus221', '$2y$10$gCbBJ44NwmrrwpXPTGslheVxoAw6HC25YAF6asw4yQ32f9.NvsfL6', 'teacher', NULL, NULL, '2025-06-29 18:14:23', 0),
(9, 'student2.1', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(10, 'student2.2', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(11, 'student2.3', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(12, 'student2.4', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(13, 'student2.5', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(14, 'student2.6', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(15, 'student2.7', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(16, 'student2.8', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(17, 'student2.9', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(18, 'student2.10', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(19, 'student2.11', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(20, 'student2.12', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(21, 'student2.13', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(22, 'student2.14', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(23, 'student2.15', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:33:32', 0),
(24, 'student3.1', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(25, 'student3.2', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(26, 'student3.3', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(27, 'student3.4', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(28, 'student3.5', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(29, 'student3.6', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(30, 'student3.7', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(31, 'student3.8', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(32, 'student3.9', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(33, 'student3.10', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(34, 'student3.11', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(35, 'student3.12', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(36, 'student3.13', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(37, 'student3.14', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(38, 'student3.15', '*AF8B0FA3E4FD603D634F62731916EC4031BFA25A', 'student', NULL, NULL, '2025-06-29 18:34:38', 0),
(54, 'teacher1', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(55, 'teacher2', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(56, 'teacher3', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(57, 'teacher4', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(58, 'teacher5', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(59, 'teacher6', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(60, 'teacher7', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(61, 'teacher8', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(62, 'teacher9', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(63, 'teacher10', '*854243B9D1073E77B674326156626EAC57B22263', 'teacher', NULL, NULL, '2025-06-29 18:35:48', 0),
(64, 'student3-1.last name282', '$2y$10$oZYx51MGdkn.fubFSj2pLeQP36vbi0Kd5402SJ7GptscNXJguqXSG', 'student', NULL, NULL, '2025-06-29 18:50:07', 0),
(65, 'student3-2.last name820', '$2y$10$AJa0MTuy5DFStD/UzGp3J.F2cckSEAariVk1aMgJnbzQu42BzE4.y', 'student', NULL, NULL, '2025-06-29 18:53:48', 0),
(66, 'student3-3542', '$2y$10$YRRSNBAnJ/5mIg4CamaFR.jwsmJqPJCC6kIGJqLXU7Q5JlH3PS.ea', 'student', NULL, NULL, '2025-06-29 19:05:59', 0),
(67, 'student3-5723', '$2y$10$ulZstF1e2hfv74cOpw69yuXemr.Bw/C5ZBDm9HG2.umlxrnsec4ye', 'student', NULL, NULL, '2025-06-29 19:26:51', 0),
(68, 'student5-1509', '$2y$10$4YUZvlD6wy9OpMgmkeqJMu2zrJx18tcuQDkdk2oR6zdb9/u9/DiN.', 'student', NULL, NULL, '2025-06-30 09:33:52', 0),
(69, 'student5-1120', '$2y$10$EuDAOvmKL7utSw94WKnrF..YTqT0pHdnq5rVLRWeODV7BMpoTGjmG', 'student', NULL, NULL, '2025-06-30 09:36:57', 0),
(70, 'student5-1953', '$2y$10$281kRSKL3hQaXZ/S8LhmJ.fbOAJRUh4Bu2X6TjbCwWHohn5tZLAM.', 'student', NULL, NULL, '2025-06-30 09:38:55', 0),
(71, 'student10-1599', '$2y$10$rWckl3hqeY2pkKHa69FS9uD4zjvrCPSscbqQCojHRqemUPt2Nqf7m', 'student', NULL, NULL, '2025-06-30 09:53:34', 0),
(72, 'student 6-1903', '$2y$10$yG9sV/czb5EJc0CIuZ3LJ.z9r/STHfa2m1dJXhcxL5iOIBU2YXeh.', 'student', NULL, NULL, '2025-06-30 10:19:50', 0),
(73, 'student 6-1130', '$2y$10$ADQLqJrJDCvwSDo8/3gDuOJsX/zPM3pghdCFo0JgqT5lhp6BDu4Vm', 'student', NULL, NULL, '2025-06-30 10:19:58', 0),
(74, 'student 6-1880', '$2y$10$Y.1kdgE//DqepQdke.O9tOW0Fz868uAbSbTps/NRrpMEISFm3ZNbG', 'student', NULL, NULL, '2025-06-30 10:22:26', 0),
(75, 'parent.1231', '$2y$10$/VmoIWEr9EUGL7zLKH44ee3Q4ArHDe3b03YiGBg8jGSGtVZkpKFbm', 'parent', NULL, NULL, '2025-06-30 10:41:39', 0),
(76, 'parent.1892', '$2y$10$rGNqNQv2P4nyvyU805TprecglNSceSrZQaqd./KNE6sSIIGCWqiz6', 'parent', NULL, NULL, '2025-06-30 10:41:45', 0),
(77, 'parent.1945', '$2y$10$WMKL299COAMEFNxMWjf9.eVvNDme0MUcHfQFIY9yBf.Ll6NTecxwy', 'parent', NULL, NULL, '2025-06-30 10:42:30', 0),
(78, 'parent.1360', '$2y$10$sFBOyQE1sPW/v0xCCuvBmOT3wK7pc7Jk7f9XRNuL7mqc03mlmCDiy', 'parent', NULL, NULL, '2025-06-30 10:43:27', 0),
(79, 'father.1169', '$2y$10$Wt10Lr45q0LS57ftdoGAk.fth0tLK.X4vRys0857fnuoVvdY6YNuW', 'parent', NULL, NULL, '2025-06-30 10:44:15', 0),
(80, 'mother.1796', '$2y$10$f2dhHBL0NM.8vO7oETTiyOqQE6bLg0fOyEdcuY1T4B10evvrdyzFS', 'parent', NULL, NULL, '2025-06-30 10:44:46', 0),
(81, 'teacher.teacher387', '$2y$10$9dm1PtXsCWvbPiR61vJvuejSpTw65Dukvae0yZup7TO2QjOQVxdXS', 'teacher', NULL, NULL, '2025-06-30 11:39:25', 0),
(82, 'ashraful.islam958', '$2y$10$Nj.zzd25BfGMYfDzPi5eR.kXt6.hjSQXfYtEwMVFnq6qwkHcRE1u2', 'teacher', NULL, NULL, '2025-06-30 12:02:05', 0),
(83, 'manik.sir777', '$2y$10$r68Ol83KIfsnWDo4m0scpum76TfbHXF58smC/4ZzGuv4z.BHeUjLm', 'teacher', NULL, NULL, '2025-06-30 12:07:12', 0),
(84, 'nobiu.sir938', '$2y$10$aZdf97zSWEbJzn34yvdbou/RO6y//hGrZTGfapAD0RsHbCsh.QVLy', 'teacher', NULL, NULL, '2025-06-30 12:18:49', 0),
(85, 'moulvi.sir635', '$2y$10$O6M9qVsoEk4io4U.m0XdWe4EDVrLX5xLNuHRsogyY7tk2vOVGL5Ky', 'teacher', NULL, NULL, '2025-07-01 10:27:42', 0),
(86, 'student9-2.lastname733', '$2y$10$ovK1sJ83zqwGCDTJUEVYBeFpnOWFkULXLkRakHZDLl1YWXTfQ.oBS', 'student', NULL, NULL, '2025-07-01 14:48:14', 0),
(87, 'aaaaa.bbbbb942', '$2y$10$qyPZhVzWH/sznru8ilbM4.qTPt9Q9qrDxnu2Ofm6EDIAuRY3VUGky', 'student', NULL, NULL, '2025-07-02 09:38:40', 0),
(88, 'aaaa.aaaa784', '$2y$10$RQWuSZ86DkXMa8QFN195fu2fwCSHlVFgVFu6x9gAnLr/4hUaknY6q', 'teacher', NULL, NULL, '2025-07-02 21:51:19', 0),
(89, 'bbbb.bbbb695', '$2y$10$77yY9dhjO2GKDOYrsGC3eeN3qNdVBzk1/t8L8utkmQviX7jBnyRfu', 'teacher', NULL, NULL, '2025-07-02 21:51:31', 0),
(90, 'cccc.cccc953', '$2y$10$w4tQ7VqgFjpyrig57zj6xuvyuQ/Sr1N4f05h2DhX1roamBfw1YStG', 'teacher', NULL, NULL, '2025-07-02 21:51:37', 0),
(91, 'new.teacher101', '$2y$10$gQKVzpcT0EgyJZwYDKmcPeMbff2TiEF5F.QfrLfFp/dc73JaF8xhe', 'teacher', NULL, NULL, '2025-07-03 17:18:48', 0),
(92, 'new.teacher319', '$2y$10$hyiqFTa4NkfEK59ERSy0Oe7zhhVpueyehn2q6UjuqdKoBXgyXsRIe', 'teacher', NULL, NULL, '2025-07-03 17:18:49', 0),
(93, 'new.teacher648', '$2y$10$1Q7YgUxwFU.jIB3XeuLTIuddX0UwUZd0J9PQbQmuhRAxPi5wXDtAW', 'teacher', NULL, NULL, '2025-07-03 17:18:56', 0),
(94, 'new.teacher291', '$2y$10$c4TwYH0lUPD0fF4mvYSFa.tYYI1f0Rn1GQo4LCzO25aoJKFDWXYBa', 'teacher', NULL, NULL, '2025-07-03 17:20:35', 0),
(95, 'new.parent613', '$2y$10$k9aoJSLX4BwjZNPQ1UX3V.H/Y6cegk8Dh14Ms7Wn5fbsG4p7ALJXq', 'parent', NULL, NULL, '2025-07-03 18:00:10', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_dept_sec`
--
ALTER TABLE `class_dept_sec`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class_dept_section` (`class_id`,`department_id`,`section_id`) USING BTREE,
  ADD KEY `department_id` (`department_id`),
  ADD KEY `fk_class_departments_section_id` (`section_id`);

--
-- Indexes for table `class_dept_sub`
--
ALTER TABLE `class_dept_sub`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_id` (`class_id`,`subject_id`,`department_id`),
  ADD UNIQUE KEY `uniq_class_subject_dept` (`class_id`,`subject_id`,`department_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `class_dept_sub_teacher`
--
ALTER TABLE `class_dept_sub_teacher`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_id` (`class_id`,`department_id`,`subject_id`,`teacher_id`) USING BTREE,
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `fk_class_teachers_department` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `ibfk_exams_type_id` (`exam_type_id`);

--
-- Indexes for table `exam_attendance`
--
ALTER TABLE `exam_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_subject_id` (`exam_subject_id`);

--
-- Indexes for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `idx_exam_subjects_exam_date` (`exam_date`),
  ADD KEY `idx_exam_subjects_status` (`exam_status`),
  ADD KEY `idx_exam_subjects_teacher` (`teacher_id`);

--
-- Indexes for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `exam_subject_id` (`exam_subject_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `current_class_id` (`current_class_id`),
  ADD KEY `current_section_id` (`current_section_id`),
  ADD KEY `current_department_id` (`current_department_id`),
  ADD KEY `fk_father` (`father_id`),
  ADD KEY `fk_mother` (`mother_id`),
  ADD KEY `fk_local_guardian` (`local_guardian_id`);

--
-- Indexes for table `student_history`
--
ALTER TABLE `student_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `fingerprint_id` (`fingerprint_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `class_dept_sec`
--
ALTER TABLE `class_dept_sec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `class_dept_sub`
--
ALTER TABLE `class_dept_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=538;

--
-- AUTO_INCREMENT for table `class_dept_sub_teacher`
--
ALTER TABLE `class_dept_sub_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `exam_attendance`
--
ALTER TABLE `exam_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exam_types`
--
ALTER TABLE `exam_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `student_history`
--
ALTER TABLE `student_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `class_dept_sec`
--
ALTER TABLE `class_dept_sec`
  ADD CONSTRAINT `class_dept_sec_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `class_dept_sec_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `class_dept_sec_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `class_dept_sub`
--
ALTER TABLE `class_dept_sub`
  ADD CONSTRAINT `class_dept_sub_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `class_dept_sub_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `class_dept_sub_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `class_dept_sub_teacher`
--
ALTER TABLE `class_dept_sub_teacher`
  ADD CONSTRAINT `ibfk_class_teachers_classes_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_class_teachers_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_class_teachers_subjects_id` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_class_teachers_teachers_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `ibfk_exams_classes_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_exams_dept_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_exams_sect_id` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_exams_type_id` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `exam_attendance`
--
ALTER TABLE `exam_attendance`
  ADD CONSTRAINT `fk_exam_attendance_exam_subject_id` FOREIGN KEY (`exam_subject_id`) REFERENCES `exam_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_subjects`
--
ALTER TABLE `exam_subjects`
  ADD CONSTRAINT `fk_exam_subjects_exam_id` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exam_subjects_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exam_subjects_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `fk_grades_exam_subject_id` FOREIGN KEY (`exam_subject_id`) REFERENCES `exam_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `ibfk_students_classes` FOREIGN KEY (`current_class_id`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_students_dept` FOREIGN KEY (`current_department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_students_father` FOREIGN KEY (`father_id`) REFERENCES `parents` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_students_local` FOREIGN KEY (`local_guardian_id`) REFERENCES `parents` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_students_mother` FOREIGN KEY (`mother_id`) REFERENCES `parents` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_students_section` FOREIGN KEY (`current_section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ibfk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `student_history`
--
ALTER TABLE `student_history`
  ADD CONSTRAINT `student_history_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_history_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_history_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_history_ibfk_4` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `timetable_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `timetable_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
