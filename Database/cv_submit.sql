-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 07:51 PM
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
-- Database: `cv_submit`
--

-- --------------------------------------------------------

--
-- Table structure for table `resume`
--

CREATE TABLE `resume` (
  `ID` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `professional_summary` text NOT NULL,
  `name_of_company_1` varchar(100) DEFAULT NULL,
  `experience_1` text DEFAULT NULL,
  `experience_1_years` int(11) DEFAULT NULL,
  `name_of_company_2` varchar(100) DEFAULT NULL,
  `experience_2` text DEFAULT NULL,
  `experience_2_years` int(11) DEFAULT NULL,
  `name_of_company_3` varchar(100) DEFAULT NULL,
  `experience_3` text DEFAULT NULL,
  `experience_3_years` int(11) DEFAULT NULL,
  `name_of_institution_1` varchar(100) DEFAULT NULL,
  `education_1` text DEFAULT NULL,
  `education_level_1` varchar(50) DEFAULT NULL,
  `educational_qualification_1` varchar(100) DEFAULT NULL,
  `gcse_passes_1` varchar(255) DEFAULT NULL,
  `professional_qualification_1` varchar(100) DEFAULT NULL,
  `name_of_institution_2` varchar(100) DEFAULT NULL,
  `education_2` text DEFAULT NULL,
  `education_level_2` varchar(50) DEFAULT NULL,
  `educational_qualification_2` varchar(100) DEFAULT NULL,
  `gcse_passes_2` varchar(255) DEFAULT NULL,
  `professional_qualification_2` varchar(100) DEFAULT NULL,
  `name_of_institution_3` varchar(100) DEFAULT NULL,
  `education_3` text DEFAULT NULL,
  `education_level_3` varchar(50) DEFAULT NULL,
  `educational_qualification_3` varchar(100) DEFAULT NULL,
  `gcse_passes_3` varchar(255) DEFAULT NULL,
  `professional_qualification_3` varchar(100) DEFAULT NULL,
  `job_sector_preference` varchar(100) DEFAULT NULL,
  `skill` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `resume`
--

INSERT INTO `resume` (`ID`, `full_name`, `mobile`, `email`, `address`, `professional_summary`, `name_of_company_1`, `experience_1`, `experience_1_years`, `name_of_company_2`, `experience_2`, `experience_2_years`, `name_of_company_3`, `experience_3`, `experience_3_years`, `name_of_institution_1`, `education_1`, `education_level_1`, `educational_qualification_1`, `gcse_passes_1`, `professional_qualification_1`, `name_of_institution_2`, `education_2`, `education_level_2`, `educational_qualification_2`, `gcse_passes_2`, `professional_qualification_2`, `name_of_institution_3`, `education_3`, `education_level_3`, `educational_qualification_3`, `gcse_passes_3`, `professional_qualification_3`, `job_sector_preference`, `skill`) VALUES
(1, 'Kingsley Ugonna Aguagwa', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', 'User and delivery\r\nThe view in Figure 1 allows for different audiences to access the frost monitor through\r\ndifferent channels. These two channels are:\r\n● Researches and the public users will access application over the internet using a web\r\nbrowser. These are the main users of the FRoST Monitor System.\r\n● Other users such as the web service requester will access the application through a web\r\nservice implemented in a ReSTful service. Essentially these “other users” are\r\napplications/systems that access our application via the REST service to get/post data\r\nsuch as Nuthatch.', 'mr biggs ', 'User and delivery\r\nThe view in Figure 1 allows for different audiences to access the frost monitor through\r\ndifferent channels. These two channels are:\r\n● Researches and the public users will access application over the internet using a web\r\nbrowser. These are the main users of the FRoST Monitor System.\r\n● Other users such as the web service requester will access the application through a web\r\nservice implemented in a ReSTful service. Essentially these “other users” are\r\napplications/systems that access our application via the REST service to get/post data\r\nsuch as Nuthatch.', 1, 'mr. stones ', 'User and delivery\r\nThe view in Figure 1 allows for different audiences to access the frost monitor through\r\ndifferent channels. These two channels are:\r\n● Researches and the public users will access application over the internet using a web\r\nbrowser. These are the main users of the FRoST Monitor System.\r\n● Other users such as the web service requester will access the application through a web\r\nservice implemented in a ReSTful service. Essentially these “other users” are\r\napplications/systems that access our application via the REST service to get/post data\r\nsuch as Nuthatch.', 1, 'your name ', 'User and delivery\r\nThe view in Figure 1 allows for different audiences to access the frost monitor through\r\ndifferent channels. These two channels are:\r\n● Researches and the public users will access application over the internet using a web\r\nbrowser. These are the main users of the FRoST Monitor System.\r\n● Other users such as the web service requester will access the application through a web\r\nservice implemented in a ReSTful service. Essentially these “other users” are\r\napplications/systems that access our application via the REST service to get/post data\r\nsuch as Nuthatch.', 1, 'YOUR NAME ', 'User and delivery\r\nThe view in Figure 1 allows for different audiences to access the frost monitor through\r\ndifferent channels. These two channels are:\r\n● Researches and the public users will access application over the internet using a web\r\nbrowser. These are the main users of the FRoST Monitor System.\r\n● Other users such as the web service requester will access the application through a web\r\nservice implemented in a ReSTful service. Essentially these “other users” are\r\napplications/systems that access our application via the REST service to get/post data\r\nsuch as Nuthatch.', '0', 'Bachelor\'s Degree', '5', 'License', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Finance', 'Programming'),
(2, 'JOHN SMITH', '074823489838', 'kingston@gmail.com', 'KINGSTON university Campus', 'We are testing the professional summary. check back soon', 'Microsoft', 'AS aN IT specialist in Microsoft', 1, 'mr. stones ', 'As a front Desk operator ', 3, 'your name ', 'AS a good a goood guy', 1, 'KIngston uni campus', 'kINGSTON UNIVERSITY', '0', 'Diploma', '3', 'Certification', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Finance', 'Programming'),
(3, 'Hallaand Jones', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', ' am a hard working, honest individual. I am a good timekeeper, always willing to learn new skills. I am friendly, helpful and polite, ', 'mr biggs ', 'The art of writing a persuasive personal statement is adding in lots of detail (without waffling), and making it relevant to the job you\'re applying for.', 1, 'mr. stones ', 'I am an enthusiastic, self-motivated, reliable, responsible and hard working person. I am a mature team worker and adaptable to all challenging situations. I am able to work well both in a team environment as well as using own initiative. I am able to work well under pressure and adhere to strict deadlines.', 3, 'kingston university', 'our summary statement should be three to five lines describing your strengths, the position/industry you are seeking, and what you will bring to the job. Strengths and traits should be focused on the direction you are moving, not where you are coming from.', 1, 'kings college', 'our summary statement should be three to five lines describing your strengths, the position/industry you are seeking, and what you will bring to the job. Strengths and traits should be focused on the direction you are moving, not where you are coming from.', '0', 'Master\'s Degree', '3', 'Certification', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Technology', 'Communication'),
(4, 'Kingsley Ugonna Aguagwa', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', 'job%20search/', 'mr biggs ', 'Aguagwa', 1, 'mr. stones ', 'Aguagwa', 6, 'your name ', 'Aguagwa', 3, 'Kingsley Ugonna', 'Aguagwa', '0', 'Bachelor\'s Degree', '5', 'License', 'Kingsley Ugonna Aguagwa', '0', '', '', '', '0', '', '', '', '', '', '', 'Technology', 'Communication'),
(5, 'Kingsley Ugonna Aguagwa', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', 'job', 'Microsoft', 'Aguagwa', 1, 'mr. stones ', 'Aguagwa', 6, 'kingston university', 'Aguagwa', 3, 'Kingsley Ugonna', 'Aguagwa', '0', 'PhD', '3', 'License', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Technology', 'Communication'),
(6, 'Kingsley Ugonna Aguagwa', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', 'job', 'Microsoft', 'Aguagwa', 1, 'mr. stones ', 'Aguagwa', 6, 'kingston university', 'Aguagwa', 3, 'Kingsley Ugonna', 'Aguagwa', '0', 'PhD', '3', 'License', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Technology', 'Communication'),
(7, 'jojo same ', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', 'the same thing ', 'mr biggs ', 'Aguagwa', 6, 'mr. stones ', 'Aguagwa', 1, 'your name ', 'Aguagwa', 3, 'Kingsley Ugonna', 'Aguagwa', '0', 'Master\'s Degree', '6', 'License', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Technology', 'Problem Solving'),
(8, 'jojo same ', '7459943902', 'ugosonic@gmail.com', '66 All Saints Road', 'the same thing ', 'mr biggs ', 'Aguagwa', 6, 'mr. stones ', 'Aguagwa', 1, 'your name ', 'Aguagwa', 3, 'Kingsley Ugonna', 'Aguagwa', '0', 'Master\'s Degree', '6', 'License', '', '0', '', '', '', '0', '', '', '', '', '', '', 'Technology', 'Problem Solving');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resume`
--
ALTER TABLE `resume`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `resume`
--
ALTER TABLE `resume`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
