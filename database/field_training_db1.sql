-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 30 نوفمبر 2025 الساعة 17:59
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `field_training_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `academic_supervisors`
--

CREATE TABLE `academic_supervisors` (
  `supervisor_id` int(11) NOT NULL,
  `employee_number` varchar(20) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `academic_supervisors`
--

INSERT INTO `academic_supervisors` (`supervisor_id`, `employee_number`, `department`) VALUES
(3, 'EMP001', 'Computer Science'),
(4, 'EMP002', 'Information Technology'),
(5, 'EMP003', 'Computer Science');

-- --------------------------------------------------------

--
-- بنية الجدول `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `full_address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `full_address`, `phone`, `fax`, `email`, `website`, `approval_status`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 'Orange Jordan', 'Amman, Shmeisani, King Hussein Street, Building 15', '+962 6 5678910', '+962 6 5678911', 'info@orange.jo', 'https://www.orange.jo', 'approved', 2, '2024-10-01 07:00:00', '2025-11-29 13:20:03', '2025-11-29 13:20:03'),
(2, 'Zain Jordan', 'Amman, Abdali, King Hussein Street, Zain Tower', '+962 6 5678920', '+962 6 5678921', 'info@zain.com.jo', 'https://www.zain.com.jo', 'approved', 2, '2024-10-02 08:00:00', '2025-11-29 13:20:03', '2025-11-29 13:20:03'),
(3, 'Umniah', 'Amman, Shmeisani, King Abdullah II Street, Umniah Tower', '+962 6 5678930', '+962 6 5678931', 'info@umniah.com.jo', 'https://www.umniah.com.jo', 'approved', 2, '2024-10-03 09:00:00', '2025-11-29 13:20:03', '2025-11-29 13:20:03'),
(4, 'ASEZA (Aqaba Special Economic Zone Authority)', 'Aqaba, ASEZA Headquarters', '+962 3 2092000', '+962 3 2092001', 'info@aseza.jo', 'https://www.aseza.jo', 'pending', NULL, NULL, '2025-11-29 13:20:03', '2025-11-29 13:20:03'),
(5, 'Microsoft Jordan', 'Amman, Amman Financial District', '+962 6 5678940', '+962 6 5678941', 'jordan@microsoft.com', 'https://www.microsoft.com/en-jo', 'approved', 2, '2024-10-04 10:00:00', '2025-11-29 13:20:03', '2025-11-29 13:20:03');

-- --------------------------------------------------------

--
-- بنية الجدول `company_evaluations`
--

CREATE TABLE `company_evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `intended_goals` text DEFAULT NULL COMMENT 'Section A',
  `assigned_tasks_summary` text DEFAULT NULL COMMENT 'Section B',
  `feedback_curriculum_a` text DEFAULT NULL COMMENT 'Section 2a',
  `feedback_curriculum_b` text DEFAULT NULL COMMENT 'Section 2b',
  `feedback_curriculum_c` text DEFAULT NULL COMMENT 'Section 2c',
  `additional_notes` text DEFAULT NULL COMMENT 'Section 3',
  `recommend_students` tinyint(1) DEFAULT NULL COMMENT 'Question 8',
  `recommend_explanation` text DEFAULT NULL COMMENT 'Question 8 explanation',
  `company_cooperation` enum('very_cooperative','acceptable','weak','totally_uncooperative') DEFAULT NULL COMMENT 'Question 9',
  `company_supervisor_signature` varchar(100) DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `company_evaluations`
--

INSERT INTO `company_evaluations` (`evaluation_id`, `assignment_id`, `intended_goals`, `assigned_tasks_summary`, `feedback_curriculum_a`, `feedback_curriculum_b`, `feedback_curriculum_c`, `additional_notes`, `recommend_students`, `recommend_explanation`, `company_cooperation`, `company_supervisor_signature`, `evaluation_date`, `submitted_at`) VALUES
(1, 4, 'The student aimed to learn about network security and cybersecurity practices. The training program was designed to provide hands-on experience in security assessment and threat detection.', 'The student worked on security assessments, firewall configuration reviews, implementation of security patches, system log monitoring, and security documentation. Tasks were progressively more complex and aligned with learning objectives.', 'The student demonstrated strong theoretical knowledge in computer networks and security concepts. The university curriculum provided a solid foundation that allowed the student to quickly adapt to practical security implementations.', 'The 160-hour training period over 4 weeks was appropriate and allowed sufficient time for the student to gain meaningful experience. The timing during fall semester was convenient.', 'The combination of theoretical learning at university and practical training at the company worked well. The student could apply concepts learned in coursework to real-world scenarios.', 'The student showed great potential and would be an asset to any security team. We recommend continuing the relationship with the university.', 1, 'The student demonstrated excellent work ethic, technical skills, and professionalism. The training program was successful, and we would welcome more students from this program.', 'very_cooperative', 'Ahmad Al-Karim', '2024-10-22', '2025-11-29 13:20:04');

-- --------------------------------------------------------

--
-- بنية الجدول `company_supervisors`
--

CREATE TABLE `company_supervisors` (
  `supervisor_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `company_supervisors`
--

INSERT INTO `company_supervisors` (`supervisor_id`, `company_id`, `user_id`, `full_name`, `email`, `phone`, `is_primary`) VALUES
(1, 1, NULL, 'Ahmad Al-Karim', 'ahmad.karim@orange.jo', '+962 79 1111111', 1),
(2, 2, NULL, 'Samira Al-Rahman', 'samira.rahman@zain.com.jo', '+962 79 2222222', 1),
(3, 3, NULL, 'Tariq Al-Salem', 'tariq.salem@umniah.com.jo', '+962 79 3333333', 1),
(4, 4, NULL, 'Noura Al-Ali', 'noura.ali@aseza.jo', '+962 79 4444444', 1),
(5, 5, NULL, 'Rami Al-Mansour', 'rami.mansour@microsoft.com', '+962 79 5555555', 1);

-- --------------------------------------------------------

--
-- بنية الجدول `evaluation_criteria_scores`
--

CREATE TABLE `evaluation_criteria_scores` (
  `score_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `criterion_number` int(11) NOT NULL COMMENT '1-10',
  `criterion_name` varchar(200) DEFAULT NULL COMMENT 'stored for reference',
  `score` int(11) NOT NULL COMMENT '1-5'
) ;

--
-- إرجاع أو استيراد بيانات الجدول `evaluation_criteria_scores`
--

INSERT INTO `evaluation_criteria_scores` (`score_id`, `evaluation_id`, `criterion_number`, `criterion_name`, `score`) VALUES
(1, 1, 1, 'Student\'s ability to fulfill assigned duties', 5),
(2, 1, 2, 'Student\'s response to training directions', 5),
(3, 1, 3, 'Student\'s interaction and ability to learn', 5),
(4, 1, 4, 'Student\'s general skills progression', 5),
(5, 1, 5, 'Student\'s ability to communicate and cooperate with others', 5),
(6, 1, 6, 'Student\'s commitment to training time/attendance', 5),
(7, 1, 7, 'Student\'s Compliance with procedures and regulations', 5),
(8, 1, 8, 'Student\'s organizational and time management skills', 4),
(9, 1, 9, 'Student\'s ability to propose new ideas', 4),
(10, 1, 10, 'Student\'s analytical skills', 5);

-- --------------------------------------------------------

--
-- بنية الجدول `final_reports`
--

CREATE TABLE `final_reports` (
  `report_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `training_type` enum('inside_company','training_courses','project') DEFAULT 'inside_company',
  `duration` varchar(50) DEFAULT NULL,
  `acknowledgment` text DEFAULT NULL COMMENT 'Section 1',
  `objectives` text DEFAULT NULL COMMENT 'Section 2.1',
  `importance` text DEFAULT NULL COMMENT 'Section 2.2',
  `nature_of_training` text DEFAULT NULL COMMENT 'Section 2.3',
  `nature_of_supervision` text DEFAULT NULL COMMENT 'Section 2.4',
  `training_experience_technical` text DEFAULT NULL COMMENT 'Section 3, Skill 1',
  `training_experience_personal` text DEFAULT NULL COMMENT 'Section 3, Skill 2',
  `training_experience_communication` text DEFAULT NULL COMMENT 'Section 3, Skill 3',
  `company_societal_impact` text DEFAULT NULL COMMENT 'Section 4',
  `relevance_to_major` text DEFAULT NULL COMMENT 'Section 5',
  `theoretical_appropriateness` text DEFAULT NULL COMMENT 'Section 6',
  `suggestions` text DEFAULT NULL COMMENT 'Section 7',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('draft','submitted','graded') DEFAULT 'draft',
  `supervisor_grade` int(11) DEFAULT NULL,
  `supervisor_comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `final_reports`
--

INSERT INTO `final_reports` (`report_id`, `assignment_id`, `training_type`, `duration`, `acknowledgment`, `objectives`, `importance`, `nature_of_training`, `nature_of_supervision`, `training_experience_technical`, `training_experience_personal`, `training_experience_communication`, `company_societal_impact`, `relevance_to_major`, `theoretical_appropriateness`, `suggestions`, `submitted_at`, `status`, `supervisor_grade`, `supervisor_comments`) VALUES
(1, 4, 'inside_company', '4 weeks (160 hours)', 'I would like to express my sincere gratitude to Orange Jordan for providing me with this valuable training opportunity. Special thanks to Mr. Ahmad Al-Karim, my company supervisor, for his continuous guidance and support throughout the training period. I also thank all team members who helped me learn and grow during this experience.', '1. Gain hands-on experience in network security and cybersecurity practices. 2. Understand real-world security challenges and solutions. 3. Learn industry-standard security tools and frameworks. 4. Develop professional skills in a corporate environment.', 'Field training is crucial for bridging the gap between theoretical knowledge and practical application. It provides students with real-world experience, enhances employability, and helps in career orientation. This training was particularly important for understanding the critical role of cybersecurity in protecting organizational assets.', 'The training focused on network security assessment, firewall configuration, security patch implementation, threat detection, and security documentation. The main areas covered were: Network Security, Cybersecurity, Threat Analysis, and Security Compliance.', 'I received task-based supervision where I was assigned specific security tasks to complete. My supervisor provided guidance when needed and reviewed my work. The supervision style was supportive and encouraged independent problem-solving while ensuring I stayed on track.', 'Technical Skills: I worked on network security assessments using tools like Wireshark and Nmap. I learned to configure firewalls, implement security patches, and analyze system logs. I worked both individually on assigned tasks and in a group during security team meetings. The major systems used were Linux servers, network infrastructure, and security monitoring tools. This experience significantly improved my technical capabilities in cybersecurity.', 'Personal Skills: I developed better time management and organizational skills by handling multiple tasks and meeting deadlines. I learned to work under pressure during security incidents and improved my attention to detail, which is crucial in security work. The training helped me become more responsible and self-disciplined.', 'Communication Skills: I participated in team meetings, presented security findings to the team, and wrote comprehensive security documentation. I learned to communicate technical information clearly to both technical and non-technical audiences. This improved my professional communication skills.', 'Orange Jordan contributes significantly to Jordan\'s digital transformation by providing reliable telecommunications services. The company supports local communities through various CSR initiatives and provides employment opportunities. Their commitment to cybersecurity helps protect national infrastructure and individual users from cyber threats.', 'The training was highly relevant to my Computer Science major. It directly applied concepts learned in courses like Network Security, Database Systems, and Operating Systems. The practical experience complemented my theoretical knowledge perfectly.', 'My theoretical knowledge from university courses provided a solid foundation. I could quickly understand security concepts and apply them. Strengths: Strong foundation in networking and security theory, good problem-solving skills. Weaknesses: Initially lacked hands-on experience with security tools, but this improved significantly during training.', 'Suggestions: 1. Include more hands-on security labs in the curriculum. 2. Organize guest lectures from industry professionals. 3. Provide opportunities for students to work on security projects earlier in their academic journey. 4. Consider establishing a security lab with industry-standard tools.', '2025-11-29 13:20:04', 'submitted', NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `final_report_grades`
--

CREATE TABLE `final_report_grades` (
  `grade_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `section1_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 2 (ILO T.3)',
  `section2_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 4 (ILO T.3)',
  `section3_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 3 (ILO T.3)',
  `section4_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 5 (ILO T.3)',
  `section5_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 2 (ILO T.3)',
  `section6_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 2 (ILO T.3)',
  `section7_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 2 (ILO T.3)',
  `total_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 20',
  `comments` text DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- إرجاع أو استيراد بيانات الجدول `final_report_grades`
--

INSERT INTO `final_report_grades` (`grade_id`, `report_id`, `supervisor_id`, `section1_mark`, `section2_mark`, `section3_mark`, `section4_mark`, `section5_mark`, `section6_mark`, `section7_mark`, `total_mark`, `comments`, `graded_at`) VALUES
(1, 1, 3, 2.00, 3.80, 2.90, 4.80, 2.00, 2.00, 2.00, 19.50, 'Excellent final report. Well-structured, comprehensive, and demonstrates deep understanding of the training experience. Minor improvements could be made in Section 2 organization.', '2025-11-29 13:20:04');

-- --------------------------------------------------------

--
-- بنية الجدول `kpi_reports`
--

CREATE TABLE `kpi_reports` (
  `report_id` int(11) NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `report_type` varchar(50) DEFAULT NULL COMMENT 'e.g., students_performance, company_statistics',
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'flexible structure for different KPIs' CHECK (json_valid(`report_data`)),
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `stage1_grades`
--

CREATE TABLE `stage1_grades` (
  `grade_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `introduction_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 1',
  `intended_goals_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 1',
  `company_details_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 3',
  `total_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 5',
  `comments` text DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- إرجاع أو استيراد بيانات الجدول `stage1_grades`
--

INSERT INTO `stage1_grades` (`grade_id`, `report_id`, `supervisor_id`, `introduction_mark`, `intended_goals_mark`, `company_details_mark`, `total_mark`, `comments`, `graded_at`) VALUES
(1, 3, 3, 1.00, 0.90, 2.80, 4.70, 'Well-written report with good understanding of the company structure. Minor improvements needed in goal clarification.', '2025-11-29 13:20:04');

-- --------------------------------------------------------

--
-- بنية الجدول `stage1_reports`
--

CREATE TABLE `stage1_reports` (
  `report_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `introduction_text` text DEFAULT NULL COMMENT 'Introduction about place of training (1 point)',
  `intended_goals` text DEFAULT NULL COMMENT 'Intended goal of training (1 point)',
  `company_details` text DEFAULT NULL COMMENT 'Describe company department, roles, software (3 points)',
  `company_supervisor_signature` tinyint(1) DEFAULT NULL,
  `company_supervisor_signed_date` date DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('draft','submitted','graded') DEFAULT 'draft',
  `supervisor_grade` int(11) DEFAULT NULL,
  `supervisor_comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `stage1_reports`
--

INSERT INTO `stage1_reports` (`report_id`, `assignment_id`, `introduction_text`, `intended_goals`, `company_details`, `company_supervisor_signature`, `company_supervisor_signed_date`, `submitted_at`, `status`, `supervisor_grade`, `supervisor_comments`) VALUES
(1, 1, 'Orange Jordan was established in 1995 and is one of the leading telecommunications companies in Jordan. The company specializes in mobile communications, internet services, and enterprise solutions. Orange Jordan has multiple branches across Jordan, with its headquarters located in Amman, Shmeisani area.', 'The intended goal of this training is to gain hands-on experience in software development, specifically in mobile application development and web technologies. I aim to understand the software development lifecycle and work with industry-standard tools and methodologies.', 'The company has several departments including Software Development, IT Operations, Customer Service, and Network Engineering. I will be working in the Software Development department which uses various technologies including Java, JavaScript, React Native, and cloud services. The department follows Agile methodology for project management.', 1, '2024-11-01', '2025-11-29 13:20:04', 'graded', 5, 'excellent'),
(2, 2, 'Zain Jordan, established in 1996, is a leading telecommunications provider in Jordan. The company specializes in mobile and internet services, digital solutions, and enterprise services. Zain operates from its headquarters in Amman, Abdali area.', 'The main goal is to learn about database management systems, data analytics, and business intelligence tools used in telecommunications. I want to understand how large-scale data is processed and analyzed.', 'The IT department consists of Database Administrators, Data Analysts, and System Administrators. They use Oracle databases, SQL Server, Python for data analysis, and various BI tools. The work environment is collaborative with daily standup meetings.', 1, '2024-11-05', '2025-11-29 13:20:04', 'submitted', NULL, NULL),
(3, 4, 'Orange Jordan was established in 1995 and is a leading telecommunications provider in Jordan.', 'To gain experience in network security and cybersecurity practices.', 'The Security department handles network security, threat detection, and compliance. They use various security tools and frameworks.', 1, '2024-10-01', '2025-11-29 19:34:35', 'graded', 5, ''),
(4, 6, '111111111', '33333333333', 'ffffffffff', 0, NULL, '2025-11-29 23:07:59', 'graded', 2, 'improve');

-- --------------------------------------------------------

--
-- بنية الجدول `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `major` varchar(100) NOT NULL,
  `academic_year` varchar(20) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `students`
--

INSERT INTO `students` (`student_id`, `student_number`, `major`, `academic_year`, `semester`) VALUES
(6, '202010001', 'Computer Science', '2024/2025', 'Fall 2024'),
(7, '202010002', 'Information Technology', '2024/2025', 'Fall 2024'),
(8, '202010003', 'Computer Science', '2024/2025', 'Fall 2024'),
(9, '202010004', 'Information Technology', '2024/2025', 'Fall 2024'),
(10, '202010005', 'Computer Science', '2024/2025', 'Fall 2024'),
(11, '2025-001', 'Computer Science', '2025/2026', 'Fall 2025');

-- --------------------------------------------------------

--
-- بنية الجدول `student_total_marks`
--

CREATE TABLE `student_total_marks` (
  `mark_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `stage1_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 5',
  `weekly_followups_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'calculated from all weeks',
  `company_evaluation_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'calculated from 10 criteria (max 50, scaled)',
  `final_report_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'max 20',
  `total_mark` decimal(5,2) DEFAULT 0.00 COMMENT 'grand total',
  `final_grade` varchar(5) DEFAULT NULL COMMENT 'letter grade (A, B, C, etc.)',
  `calculated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- إرجاع أو استيراد بيانات الجدول `student_total_marks`
--

INSERT INTO `student_total_marks` (`mark_id`, `assignment_id`, `stage1_mark`, `weekly_followups_mark`, `company_evaluation_mark`, `final_report_mark`, `total_mark`, `final_grade`, `calculated_at`) VALUES
(1, 4, 4.70, 29.00, 48.00, 19.50, 101.20, 'A+', '2025-11-29 13:20:04');

-- --------------------------------------------------------

--
-- بنية الجدول `training_assignments`
--

CREATE TABLE `training_assignments` (
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `academic_supervisor_id` int(11) NOT NULL,
  `training_start_date` date NOT NULL,
  `training_end_date` date DEFAULT NULL,
  `status` enum('assigned','in_progress','completed','cancelled') DEFAULT 'assigned',
  `total_hours` int(11) DEFAULT 160,
  `training_type` varchar(50) DEFAULT 'company',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- إرجاع أو استيراد بيانات الجدول `training_assignments`
--

INSERT INTO `training_assignments` (`assignment_id`, `student_id`, `company_id`, `academic_supervisor_id`, `training_start_date`, `training_end_date`, `status`, `total_hours`, `training_type`, `created_at`) VALUES
(1, 6, 1, 3, '2024-11-01', '2024-11-22', 'in_progress', 160, 'company', '2025-11-29 13:20:03'),
(2, 7, 2, 4, '2024-11-05', '2024-11-26', 'in_progress', 160, 'company', '2025-11-29 13:20:03'),
(3, 8, 3, 3, '2024-11-10', NULL, 'assigned', 160, 'company', '2025-11-29 13:20:03'),
(4, 9, 1, 5, '2024-10-01', '2024-10-22', 'completed', 160, 'company', '2025-11-29 13:20:03'),
(5, 10, 5, 4, '2024-11-15', NULL, 'assigned', 160, 'company', '2025-11-29 13:20:03'),
(6, 11, 5, 3, '2025-11-29', '2025-12-19', 'assigned', 160, 'company', '2025-11-29 15:25:01');

-- --------------------------------------------------------

--
-- بنية الجدول `training_request_letters`
--

CREATE TABLE `training_request_letters` (
  `letter_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `issued_by` int(11) NOT NULL COMMENT 'dean user_id',
  `letter_content` text DEFAULT NULL,
  `issued_date` date NOT NULL,
  `file_path` varchar(500) DEFAULT NULL COMMENT 'if stored as file',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `training_request_letters`
--

INSERT INTO `training_request_letters` (`letter_id`, `assignment_id`, `issued_by`, `letter_content`, `issued_date`, `file_path`, `created_at`) VALUES
(1, 1, 1, 'This is to certify that student Omar Al-Mahmoud (Student ID: 202010001) is enrolled in the Computer Science program at the University of Petra, Faculty of Information Technology. We request that you provide field training opportunity for this student for a period of 160 hours (4 weeks) as part of their academic requirements.', '2024-10-25', NULL, '2025-11-29 13:20:04'),
(2, 2, 1, 'This is to certify that student Layla Al-Rashid (Student ID: 202010002) is enrolled in the Information Technology program at the University of Petra, Faculty of Information Technology. We request that you provide field training opportunity for this student for a period of 160 hours (4 weeks) as part of their academic requirements.', '2024-10-28', NULL, '2025-11-29 13:20:04'),
(3, 4, 1, 'This is to certify that student Mariam Al-Ibrahim (Student ID: 202010004) is enrolled in the Information Technology program at the University of Petra, Faculty of Information Technology. We request that you provide field training opportunity for this student for a period of 160 hours (4 weeks) as part of their academic requirements.', '2024-09-25', NULL, '2025-11-29 13:20:04');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_type` enum('student','supervisor','coordinator','dean','company_supervisor') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `phone`, `user_type`, `is_active`, `created_at`) VALUES
(1, 'dean.petra', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dean@uop.edu.jo', 'Dr. Ahmad Al-Masri', '+962 6 5715546', 'dean', 1, '2025-11-29 13:20:03'),
(2, 'coordinator1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coord1@uop.edu.jo', 'Sara Al-Khalili', '+962 6 5715547', 'coordinator', 1, '2025-11-29 13:20:03'),
(3, 'supervisor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor1@uop.edu.jo', 'Dr. Mohammed Al-Hashimi', '+962 6 5715548', 'supervisor', 1, '2025-11-29 13:20:03'),
(4, 'supervisor2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor2@uop.edu.jo', 'Dr. Fatima Al-Zahra', '+962 6 5715549', 'supervisor', 1, '2025-11-29 13:20:03'),
(5, 'supervisor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor3@uop.edu.jo', 'Dr. Khalid Al-Ahmad', '+962 6 5715550', 'supervisor', 1, '2025-11-29 13:20:03'),
(6, 'student001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student001@uop.edu.jo', 'Omar Al-Mahmoud', '+962 79 1234567', 'student', 1, '2025-11-29 13:20:03'),
(7, 'student002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student002@uop.edu.jo', 'Layla Al-Rashid', '+962 79 2345678', 'student', 1, '2025-11-29 13:20:03'),
(8, 'student003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student003@uop.edu.jo', 'Yusuf Al-Hassan', '+962 79 3456789', 'student', 1, '2025-11-29 13:20:03'),
(9, 'student004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student004@uop.edu.jo', 'Mariam Al-Ibrahim', '+962 79 4567890', 'student', 1, '2025-11-29 13:20:03'),
(10, 'student005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student005@uop.edu.jo', 'Hassan Al-Nasser', '+962 79 5678901', 'student', 1, '2025-11-29 13:20:03'),
(11, 'std', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'newstudent01@example.com', 'John Doe', '+962 7 1234 5678', 'student', 1, '2025-11-29 15:15:28');

-- --------------------------------------------------------

--
-- بنية الجدول `weekly_followups`
--

CREATE TABLE `weekly_followups` (
  `followup_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `week_start_date` date NOT NULL,
  `week_end_date` date NOT NULL,
  `week_number` int(11) DEFAULT NULL COMMENT 'calculated week number in training',
  `company_supervisor_signed` tinyint(1) DEFAULT 0,
  `company_supervisor_signed_date` date DEFAULT NULL,
  `academic_supervisor_signed` tinyint(1) DEFAULT 0,
  `academic_supervisor_signed_date` date DEFAULT NULL,
  `marks_deducted` int(11) DEFAULT 0 COMMENT '2 marks if not followed up',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `supervisor_grade` int(11) DEFAULT NULL,
  `supervisor_comments` text DEFAULT NULL
) ;

--
-- إرجاع أو استيراد بيانات الجدول `weekly_followups`
--

INSERT INTO `weekly_followups` (`followup_id`, `assignment_id`, `week_start_date`, `week_end_date`, `week_number`, `company_supervisor_signed`, `company_supervisor_signed_date`, `academic_supervisor_signed`, `academic_supervisor_signed_date`, `marks_deducted`, `submitted_at`, `supervisor_grade`, `supervisor_comments`) VALUES
(1, 1, '2024-11-01', '2024-11-07', 1, 1, '2024-11-07', 1, '2025-11-30', 0, '2025-11-29 13:20:04', 10, 'good'),
(2, 1, '2024-11-08', '2024-11-14', 2, 1, '2024-11-14', 1, '2024-11-15', 0, '2025-11-29 13:20:04', NULL, NULL),
(3, 2, '2024-11-05', '2024-11-11', 1, 1, '2024-11-11', 1, '2024-11-12', 0, '2025-11-29 13:20:04', NULL, NULL),
(4, 4, '2024-10-01', '2024-10-07', 1, 1, '2024-10-07', 1, '2024-10-08', 0, '2025-11-29 13:20:04', NULL, NULL),
(5, 4, '2024-10-08', '2024-10-14', 2, 1, '2024-10-14', 1, '2024-10-15', 0, '2025-11-29 13:20:04', NULL, NULL),
(6, 4, '2024-10-15', '2024-10-21', 3, 1, '2024-10-21', 1, '2024-10-22', 0, '2025-11-29 13:20:04', NULL, NULL),
(7, 3, '2024-11-10', '2024-11-16', 1, 0, NULL, 0, NULL, 0, '2025-11-29 14:37:36', NULL, NULL),
(8, 4, '2024-10-22', '2024-10-28', 4, 0, NULL, 0, NULL, 0, '2025-11-29 14:53:28', NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `weekly_grades`
--

CREATE TABLE `weekly_grades` (
  `grade_id` int(11) NOT NULL,
  `followup_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `mark` decimal(5,2) DEFAULT 0.00,
  `comments` text DEFAULT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

--
-- إرجاع أو استيراد بيانات الجدول `weekly_grades`
--

INSERT INTO `weekly_grades` (`grade_id`, `followup_id`, `supervisor_id`, `mark`, `comments`, `graded_at`) VALUES
(1, 1, 3, 9.50, 'Good progress in first week. Continue with active participation.', '2025-11-29 13:20:04'),
(2, 2, 3, 10.00, 'Excellent work on the new feature. Well documented.', '2025-11-29 13:20:04'),
(3, 3, 4, 9.00, 'Good understanding of database concepts. Keep it up.', '2025-11-29 13:20:04'),
(4, 4, 3, 9.50, 'Great progress. Security implementation looks solid.', '2025-11-29 13:20:04'),
(5, 5, 3, 10.00, 'Excellent work on security monitoring.', '2025-11-29 13:20:04'),
(6, 6, 3, 10.00, 'Outstanding final week. Comprehensive documentation.', '2025-11-29 13:20:04');

-- --------------------------------------------------------

--
-- بنية الجدول `weekly_tasks`
--

CREATE TABLE `weekly_tasks` (
  `task_id` int(11) NOT NULL,
  `followup_id` int(11) NOT NULL,
  `tasks_duties` text DEFAULT NULL COMMENT 'Tasks and duties assigned',
  `notes` text DEFAULT NULL,
  `gained_skills` text DEFAULT NULL COMMENT 'Gained skills or knowledge',
  `task_order` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `weekly_tasks`
--

INSERT INTO `weekly_tasks` (`task_id`, `followup_id`, `tasks_duties`, `notes`, `gained_skills`, `task_order`) VALUES
(1, 1, 'Set up development environment, reviewed existing codebase, attended team meeting', 'Getting familiar with the project structure', 'Learned to use React Native, Git version control, and project management tools', 1),
(2, 1, 'Fixed minor bugs in mobile app, updated documentation', 'Working on small tasks first', 'Improved debugging skills and code review process', 2),
(3, 2, 'Developed new feature for user authentication, code review', 'Feature is in testing phase', 'Gained experience in secure authentication implementation', 1),
(4, 2, 'Participated in sprint planning meeting, updated project documentation', 'Active involvement in team activities', 'Learned Agile methodology practices', 2),
(5, 3, 'Reviewed database schema, learned SQL queries for data extraction', 'Focusing on database operations', 'Improved SQL skills, learned data extraction techniques', 1),
(6, 3, 'Created reports using BI tools, presented findings to team', 'First experience with business intelligence tools', 'Gained skills in data visualization and report generation', 2),
(7, 4, 'Network security assessment, reviewed firewall configurations', 'Learning security protocols', 'Gained knowledge in network security best practices', 1),
(8, 5, 'Implemented security patches, monitored system logs', 'Hands-on security work', 'Learned about threat detection and incident response', 1),
(9, 6, 'Final security audit, prepared security documentation', 'Completing training tasks', 'Comprehensive understanding of security frameworks', 1),
(10, 7, '1', '2', '3', 1),
(11, 8, '1', '2', '3', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_supervisors`
--
ALTER TABLE `academic_supervisors`
  ADD PRIMARY KEY (`supervisor_id`),
  ADD UNIQUE KEY `employee_number` (`employee_number`),
  ADD KEY `idx_employee_number` (`employee_number`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `idx_company_name` (`company_name`),
  ADD KEY `idx_approval_status` (`approval_status`);

--
-- Indexes for table `company_evaluations`
--
ALTER TABLE `company_evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `idx_assignment_id` (`assignment_id`);

--
-- Indexes for table `company_supervisors`
--
ALTER TABLE `company_supervisors`
  ADD PRIMARY KEY (`supervisor_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_company_id` (`company_id`);

--
-- Indexes for table `evaluation_criteria_scores`
--
ALTER TABLE `evaluation_criteria_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `idx_evaluation_id` (`evaluation_id`),
  ADD KEY `idx_criterion` (`evaluation_id`,`criterion_number`);

--
-- Indexes for table `final_reports`
--
ALTER TABLE `final_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_assignment_id` (`assignment_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `final_report_grades`
--
ALTER TABLE `final_report_grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `idx_report_id` (`report_id`),
  ADD KEY `idx_supervisor_id` (`supervisor_id`);

--
-- Indexes for table `kpi_reports`
--
ALTER TABLE `kpi_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_generated_by` (`generated_by`),
  ADD KEY `idx_report_type` (`report_type`);

--
-- Indexes for table `stage1_grades`
--
ALTER TABLE `stage1_grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `idx_report_id` (`report_id`),
  ADD KEY `idx_supervisor_id` (`supervisor_id`);

--
-- Indexes for table `stage1_reports`
--
ALTER TABLE `stage1_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_assignment_id` (`assignment_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD KEY `idx_student_number` (`student_number`),
  ADD KEY `idx_major` (`major`);

--
-- Indexes for table `student_total_marks`
--
ALTER TABLE `student_total_marks`
  ADD PRIMARY KEY (`mark_id`),
  ADD UNIQUE KEY `assignment_id` (`assignment_id`),
  ADD KEY `idx_assignment_id` (`assignment_id`);

--
-- Indexes for table `training_assignments`
--
ALTER TABLE `training_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_company_id` (`company_id`),
  ADD KEY `idx_academic_supervisor_id` (`academic_supervisor_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `training_request_letters`
--
ALTER TABLE `training_request_letters`
  ADD PRIMARY KEY (`letter_id`),
  ADD KEY `idx_assignment_id` (`assignment_id`),
  ADD KEY `idx_issued_by` (`issued_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- Indexes for table `weekly_followups`
--
ALTER TABLE `weekly_followups`
  ADD PRIMARY KEY (`followup_id`),
  ADD KEY `idx_assignment_id` (`assignment_id`),
  ADD KEY `idx_week_number` (`week_number`),
  ADD KEY `idx_assignment_week` (`assignment_id`,`week_number`);

--
-- Indexes for table `weekly_grades`
--
ALTER TABLE `weekly_grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `idx_followup_id` (`followup_id`),
  ADD KEY `idx_supervisor_id` (`supervisor_id`);

--
-- Indexes for table `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `idx_followup_id` (`followup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `company_evaluations`
--
ALTER TABLE `company_evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_supervisors`
--
ALTER TABLE `company_supervisors`
  MODIFY `supervisor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `evaluation_criteria_scores`
--
ALTER TABLE `evaluation_criteria_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `final_reports`
--
ALTER TABLE `final_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `final_report_grades`
--
ALTER TABLE `final_report_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_reports`
--
ALTER TABLE `kpi_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stage1_grades`
--
ALTER TABLE `stage1_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stage1_reports`
--
ALTER TABLE `stage1_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_total_marks`
--
ALTER TABLE `student_total_marks`
  MODIFY `mark_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_assignments`
--
ALTER TABLE `training_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `training_request_letters`
--
ALTER TABLE `training_request_letters`
  MODIFY `letter_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `weekly_followups`
--
ALTER TABLE `weekly_followups`
  MODIFY `followup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_grades`
--
ALTER TABLE `weekly_grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `academic_supervisors`
--
ALTER TABLE `academic_supervisors`
  ADD CONSTRAINT `academic_supervisors_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- قيود الجداول `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- قيود الجداول `company_evaluations`
--
ALTER TABLE `company_evaluations`
  ADD CONSTRAINT `company_evaluations_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `training_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- قيود الجداول `company_supervisors`
--
ALTER TABLE `company_supervisors`
  ADD CONSTRAINT `company_supervisors_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_supervisors_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- قيود الجداول `evaluation_criteria_scores`
--
ALTER TABLE `evaluation_criteria_scores`
  ADD CONSTRAINT `evaluation_criteria_scores_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `company_evaluations` (`evaluation_id`) ON DELETE CASCADE;

--
-- قيود الجداول `final_reports`
--
ALTER TABLE `final_reports`
  ADD CONSTRAINT `final_reports_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `training_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- قيود الجداول `final_report_grades`
--
ALTER TABLE `final_report_grades`
  ADD CONSTRAINT `final_report_grades_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `final_reports` (`report_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `final_report_grades_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `academic_supervisors` (`supervisor_id`) ON DELETE CASCADE;

--
-- قيود الجداول `kpi_reports`
--
ALTER TABLE `kpi_reports`
  ADD CONSTRAINT `kpi_reports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- قيود الجداول `stage1_grades`
--
ALTER TABLE `stage1_grades`
  ADD CONSTRAINT `stage1_grades_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `stage1_reports` (`report_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stage1_grades_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `academic_supervisors` (`supervisor_id`) ON DELETE CASCADE;

--
-- قيود الجداول `stage1_reports`
--
ALTER TABLE `stage1_reports`
  ADD CONSTRAINT `stage1_reports_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `training_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- قيود الجداول `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- قيود الجداول `student_total_marks`
--
ALTER TABLE `student_total_marks`
  ADD CONSTRAINT `student_total_marks_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `training_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- قيود الجداول `training_assignments`
--
ALTER TABLE `training_assignments`
  ADD CONSTRAINT `training_assignments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_assignments_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_assignments_ibfk_3` FOREIGN KEY (`academic_supervisor_id`) REFERENCES `academic_supervisors` (`supervisor_id`) ON DELETE CASCADE;

--
-- قيود الجداول `training_request_letters`
--
ALTER TABLE `training_request_letters`
  ADD CONSTRAINT `training_request_letters_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `training_assignments` (`assignment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `training_request_letters_ibfk_2` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- قيود الجداول `weekly_followups`
--
ALTER TABLE `weekly_followups`
  ADD CONSTRAINT `weekly_followups_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `training_assignments` (`assignment_id`) ON DELETE CASCADE;

--
-- قيود الجداول `weekly_grades`
--
ALTER TABLE `weekly_grades`
  ADD CONSTRAINT `weekly_grades_ibfk_1` FOREIGN KEY (`followup_id`) REFERENCES `weekly_followups` (`followup_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `weekly_grades_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `academic_supervisors` (`supervisor_id`) ON DELETE CASCADE;

--
-- قيود الجداول `weekly_tasks`
--
ALTER TABLE `weekly_tasks`
  ADD CONSTRAINT `weekly_tasks_ibfk_1` FOREIGN KEY (`followup_id`) REFERENCES `weekly_followups` (`followup_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
