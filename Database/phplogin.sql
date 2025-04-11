-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 07:40 PM
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
-- Database: `phplogin`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `usergroup` enum('allusers','employers') NOT NULL DEFAULT 'allusers'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `email`, `usergroup`) VALUES
(1, 'test', '$2y$10$SfhYIDtn.iOuCW7zfoFLuuZHX6lja4lF4XA4JqNmpiH/.P3zB8JCa', 'test@test.com', 'allusers'),
(2, 'admin', '$2y$10$IcDKYzZ5t8vQMrVYicbiXOmj3gw7phzpqNIH7NfG6v.vv5kNMGlk2', '', 'employers'),
(3, 'zid', '$2y$10$y9p1/D8GQrRm79VntNC99.SQNhU0mGlqOMBZr129s/PR1bOHrOjba', '', 'allusers');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `user_id` int(11) NOT NULL,
  `accept_email_template` text DEFAULT NULL,
  `reject_email_template` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `application_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_name` varchar(255) DEFAULT NULL,
  `applicant_email` varchar(255) DEFAULT NULL,
  `applicant_phone` varchar(20) DEFAULT NULL,
  `cv_selection` int(11) DEFAULT NULL,
  `uploaded_cv_id` int(11) DEFAULT NULL,
  `cv_type` varchar(100) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `application_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`application_id`, `user_id`, `job_id`, `applicant_name`, `applicant_email`, `applicant_phone`, `cv_selection`, `uploaded_cv_id`, `cv_type`, `cover_letter`, `application_date`, `status`) VALUES
(2, 1, 1, 'Kingsley Ugonna Aguagwa', 'ugosonic@gmail.com', '07459943902', NULL, 1, 'file', '', '2025-03-27 22:49:44', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `job_posts`
--

CREATE TABLE `job_posts` (
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `company_description` text DEFAULT NULL,
  `job_type` varchar(255) NOT NULL,
  `experience_level` varchar(100) DEFAULT NULL,
  `job_category` varchar(100) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `remote_option` tinyint(1) DEFAULT 0,
  `currency` varchar(10) DEFAULT NULL,
  `salary_min` decimal(10,2) DEFAULT NULL,
  `salary_max` decimal(10,2) DEFAULT NULL,
  `application_email` varchar(255) DEFAULT NULL,
  `application_url` varchar(255) DEFAULT NULL,
  `about_job` text DEFAULT NULL,
  `description` text NOT NULL,
  `responsibilities` text DEFAULT NULL,
  `requirements` text NOT NULL,
  `benefits` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `education_level` varchar(100) DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL,
  `interview_questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interview_questions`)),
  `application_deadline` date DEFAULT NULL,
  `number_of_positions` int(11) DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `expiring_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_posts`
--

INSERT INTO `job_posts` (`job_id`, `user_id`, `job_title`, `company_name`, `company_website`, `company_description`, `job_type`, `experience_level`, `job_category`, `location`, `remote_option`, `currency`, `salary_min`, `salary_max`, `application_email`, `application_url`, `about_job`, `description`, `responsibilities`, `requirements`, `benefits`, `skills`, `education_level`, `languages`, `interview_questions`, `application_deadline`, `number_of_positions`, `submission_date`, `expiring_date`, `created_at`) VALUES
(1, 2, 'software engineer', 'ulez', 'http://ulez.com', 'unknown CREATE TABLE job_posts (\r\n    id INT PRIMARY KEY AUTO_INCREMENT,\r\n    user_id INT NOT NULL,\r\n    job_title VARCHAR(255) NOT NULL,\r\n    company_name VARCHAR(255) NOT NULL,\r\n    company_website VARCHAR(255),\r\n    company_description TEXT,\r\n    job_type VARCHAR(255) NOT NULL,\r\n    experience_level VARCHAR(100),\r\n    job_category VARCHAR(100),\r\n    location VARCHAR(255) NOT NULL,\r\n    remote_option TINYINT(1) DEFAULT 0,\r\n    currency VARCHAR(10),\r\n    salary_min DECIMAL(10, 2),\r\n    salary_max DECIMAL(10, 2),\r\n    application_email VARCHAR(255),\r\n    application_url VARCHAR(255),\r\n    about_job TEXT,\r\n    description TEXT NOT NULL,\r\n    responsibilities TEXT,\r\n    requirements TEXT NOT NULL,\r\n    benefits TEXT,\r\n    skills TEXT,\r\n    education_level VARCHAR(100),\r\n    languages VARCHAR(255),\r\n    interview_questions JSON,\r\n    application_deadline DATE,\r\n    number_of_positions INT,\r\n    submission_date DATE,\r\n    expiring_date DATE,\r\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\r\n    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE\r\n);\r\n', 'Part-time', 'Mid-level', 'Finance', 'london', 1, '0', 0.00, 0.00, 'ugosonic@gmail.com', '', ' Explanation:\r\nid: Auto-incrementing unique identifier.\r\n\r\nuser_id: Foreign key to link job posts to the user who created them.\r\n\r\njob_title, company_name, etc.: Corresponding fields for the form data.\r\n\r\nremote_option: Tiny integer (0 or 1) to handle the checkbox value.\r\n\r\nsalary_min and salary_max: Decimal fields to handle salary ranges.\r\n\r\ninterview_questions: Stored as JSON to support multiple questions/options flexibly.\r\n\r\ncreated_at: Captures when the job post was created.\r\n\r\nFOREIGN KEY: Ensures job posts are deleted if the corresponding user is removed.', ' Explanation:\r\nid: Auto-incrementing unique identifier.\r\n\r\nuser_id: Foreign key to link job posts to the user who created them.\r\n\r\njob_title, company_name, etc.: Corresponding fields for the form data.\r\n\r\nremote_option: Tiny integer (0 or 1) to handle the checkbox value.\r\n\r\nsalary_min and salary_max: Decimal fields to handle salary ranges.\r\n\r\ninterview_questions: Stored as JSON to support multiple questions/options flexibly.\r\n\r\ncreated_at: Captures when the job post was created.\r\n\r\nFOREIGN KEY: Ensures job posts are deleted if the corresponding user is removed.', 'you have to show it ', 'unknown ', '', '', '', '', '[{\"question\":\"do you work \",\"options\":[\"yes\",\"no\"]}]', '2025-03-29', 0, '2025-03-26', '2025-03-29', '2025-03-26 17:07:35');

-- --------------------------------------------------------

--
-- Table structure for table `resume`
--

CREATE TABLE `resume` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `professional_summary` text NOT NULL,
  `job_sector_preference` varchar(255) NOT NULL,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`skills`)),
  `experiences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`experiences`)),
  `educations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`educations`)),
  `creation_date` datetime NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resume`
--

INSERT INTO `resume` (`id`, `user_id`, `full_name`, `file_name`, `mobile`, `email`, `address`, `professional_summary`, `job_sector_preference`, `skills`, `experiences`, `educations`, `creation_date`, `name`) VALUES
(1, 2, 'Kingsley Ugonna Aguagwa', 'Teaching CV ', '07459943902', 'ugosonic@gmail.com', '66 All Saints Road', '$sql = \"SELECT COUNT(*) AS total FROM job_applications WHERE job_id = ?\";\r\n$stmt = $conn->prepare($sql);\r\n$stmt->bind_param(\"i\", $job_id); // ✅ CORRECT: only 1 placeholder, so only 1 variable\r\n', 'technology', '[\"programmimng \",\"fiance\"]', '[{\"company_name\":\"CaNON \",\"job_title\":\"instrumentality \",\"description\":\"$sql = \\\"SELECT COUNT(*) AS total FROM job_applications WHERE job_id = ?\\\";\\r\\n$stmt = $conn->prepare($sql);\\r\\n$stmt->bind_param(\\\"i\\\", $job_id); \\/\\/ \\u2705 CORRECT: only 1 placeholder, so only 1 variable\\r\\n\",\"years\":\"3\"},{\"company_name\":\"Dell company \",\"job_title\":\"Trainee\",\"description\":\"$sql = \\\"SELECT COUNT(*) AS total FROM job_applications WHERE job_id = ?\\\";\\r\\n$stmt = $conn->prepare($sql);\\r\\n$stmt->bind_param(\\\"i\\\", $job_id); \\/\\/ \\u2705 CORRECT: only 1 placeholder, so only 1 variable\\r\\n\",\"years\":\"3\"}]', '[{\"institution_name\":\"Kingston University \",\"degree\":\"software engineer\",\"description\":\"$sql = \\\"SELECT COUNT(*) AS total FROM job_applications WHERE job_id = ?\\\";\\r\\n$stmt = $conn->prepare($sql);\\r\\n$stmt->bind_param(\\\"i\\\", $job_id); \\/\\/ \\u2705 CORRECT: only 1 placeholder, so only 1 variable\\r\\n\"}]', '2025-03-27 23:07:11', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_cvs`
--

CREATE TABLE `uploaded_cvs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `upload_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploaded_cvs`
--

INSERT INTO `uploaded_cvs` (`id`, `user_id`, `full_name`, `file_name`, `upload_date`) VALUES
(1, 1, 'Kingsley Ugonna Aguagwa', '1_cv_1743112184.docx', '2025-03-27 22:49:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `cv_selection` (`cv_selection`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `fk_uploaded_cv_id` (`uploaded_cv_id`);

--
-- Indexes for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `resume`
--
ALTER TABLE `resume`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploaded_cvs`
--
ALTER TABLE `uploaded_cvs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_posts`
--
ALTER TABLE `job_posts`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resume`
--
ALTER TABLE `resume`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uploaded_cvs`
--
ALTER TABLE `uploaded_cvs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD CONSTRAINT `email_templates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `fk_uploaded_cv_id` FOREIGN KEY (`uploaded_cv_id`) REFERENCES `uploaded_cvs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job_posts` (`job_id`),
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`cv_selection`) REFERENCES `resume` (`id`);

--
-- Constraints for table `uploaded_cvs`
--
ALTER TABLE `uploaded_cvs`
  ADD CONSTRAINT `uploaded_cvs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
