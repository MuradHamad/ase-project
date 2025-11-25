-- =====================================================
-- Field Training Management System - Database Schema
-- University of Petra, Faculty of Information Technology
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS field_training_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE field_training_db;

-- =====================================================
-- 1. USERS TABLE
-- Stores all system users with authentication
-- =====================================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('student', 'supervisor', 'coordinator', 'dean', 'company_supervisor') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_user_type (user_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. STUDENTS TABLE
-- Extended student information
-- =====================================================
CREATE TABLE students (
    student_id INT PRIMARY KEY,
    student_number VARCHAR(20) UNIQUE NOT NULL,
    major VARCHAR(100) NOT NULL,
    academic_year VARCHAR(20),
    semester VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_student_number (student_number),
    INDEX idx_major (major)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. COMPANIES TABLE
-- Company information and approval status
-- =====================================================
CREATE TABLE companies (
    company_id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(200) NOT NULL,
    full_address TEXT,
    phone VARCHAR(20),
    fax VARCHAR(20),
    email VARCHAR(100),
    website VARCHAR(200),
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_company_name (company_name),
    INDEX idx_approval_status (approval_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. COMPANY_SUPERVISORS TABLE
-- Company supervisor details linked to companies
-- =====================================================
CREATE TABLE company_supervisors (
    supervisor_id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    is_primary BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_company_id (company_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. ACADEMIC_SUPERVISORS TABLE
-- Academic supervisor (staff member) information
-- =====================================================
CREATE TABLE academic_supervisors (
    supervisor_id INT PRIMARY KEY,
    employee_number VARCHAR(20) UNIQUE,
    department VARCHAR(100),
    FOREIGN KEY (supervisor_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_employee_number (employee_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. TRAINING_ASSIGNMENTS TABLE
-- Links students to companies and academic supervisors
-- =====================================================
CREATE TABLE training_assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    company_id INT NOT NULL,
    academic_supervisor_id INT NOT NULL,
    training_start_date DATE NOT NULL,
    training_end_date DATE,
    status ENUM('assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'assigned',
    total_hours INT DEFAULT 160,
    training_type VARCHAR(50) DEFAULT 'company',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
    FOREIGN KEY (academic_supervisor_id) REFERENCES academic_supervisors(supervisor_id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_company_id (company_id),
    INDEX idx_academic_supervisor_id (academic_supervisor_id),
    INDEX idx_status (status),
    CONSTRAINT chk_dates CHECK (training_end_date IS NULL OR training_end_date >= training_start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. STAGE1_REPORTS TABLE
-- Stage 1 Report (Company Profile) submissions
-- =====================================================
CREATE TABLE stage1_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    introduction_text TEXT COMMENT 'Introduction about place of training (1 point)',
    intended_goals TEXT COMMENT 'Intended goal of training (1 point)',
    company_details TEXT COMMENT 'Describe company department, roles, software (3 points)',
    company_supervisor_signature VARCHAR(100),
    company_supervisor_signed_date DATE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'submitted', 'graded') DEFAULT 'draft',
    FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
    INDEX idx_assignment_id (assignment_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. STAGE1_GRADES TABLE
-- Grading for Stage 1 reports
-- =====================================================
CREATE TABLE stage1_grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    introduction_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 1',
    intended_goals_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 1',
    company_details_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 3',
    total_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 5',
    comments TEXT,
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES stage1_reports(report_id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES academic_supervisors(supervisor_id) ON DELETE CASCADE,
    INDEX idx_report_id (report_id),
    INDEX idx_supervisor_id (supervisor_id),
    CONSTRAINT chk_stage1_intro CHECK (introduction_mark >= 0 AND introduction_mark <= 1),
    CONSTRAINT chk_stage1_goals CHECK (intended_goals_mark >= 0 AND intended_goals_mark <= 1),
    CONSTRAINT chk_stage1_details CHECK (company_details_mark >= 0 AND company_details_mark <= 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. WEEKLY_FOLLOWUPS TABLE
-- Weekly follow-up submissions
-- =====================================================
CREATE TABLE weekly_followups (
    followup_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    week_start_date DATE NOT NULL,
    week_end_date DATE NOT NULL,
    week_number INT COMMENT 'calculated week number in training',
    company_supervisor_signed BOOLEAN DEFAULT FALSE,
    company_supervisor_signed_date DATE,
    academic_supervisor_signed BOOLEAN DEFAULT FALSE,
    academic_supervisor_signed_date DATE,
    marks_deducted INT DEFAULT 0 COMMENT '2 marks if not followed up',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
    INDEX idx_assignment_id (assignment_id),
    INDEX idx_week_number (week_number),
    INDEX idx_assignment_week (assignment_id, week_number),
    CONSTRAINT chk_week_dates CHECK (week_end_date >= week_start_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. WEEKLY_TASKS TABLE
-- Tasks/duties within each weekly follow-up
-- =====================================================
CREATE TABLE weekly_tasks (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    followup_id INT NOT NULL,
    tasks_duties TEXT COMMENT 'Tasks and duties assigned',
    notes TEXT,
    gained_skills TEXT COMMENT 'Gained skills or knowledge',
    task_order INT DEFAULT 1,
    FOREIGN KEY (followup_id) REFERENCES weekly_followups(followup_id) ON DELETE CASCADE,
    INDEX idx_followup_id (followup_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. WEEKLY_GRADES TABLE
-- Grading for weekly follow-ups
-- =====================================================
CREATE TABLE weekly_grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    followup_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    mark DECIMAL(5,2) DEFAULT 0,
    comments TEXT,
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (followup_id) REFERENCES weekly_followups(followup_id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES academic_supervisors(supervisor_id) ON DELETE CASCADE,
    INDEX idx_followup_id (followup_id),
    INDEX idx_supervisor_id (supervisor_id),
    CONSTRAINT chk_weekly_mark CHECK (mark >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. COMPANY_EVALUATIONS TABLE
-- Company supervisor's evaluation of the trainee
-- =====================================================
CREATE TABLE company_evaluations (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    intended_goals TEXT COMMENT 'Section A',
    assigned_tasks_summary TEXT COMMENT 'Section B',
    feedback_curriculum_a TEXT COMMENT 'Section 2a',
    feedback_curriculum_b TEXT COMMENT 'Section 2b',
    feedback_curriculum_c TEXT COMMENT 'Section 2c',
    additional_notes TEXT COMMENT 'Section 3',
    recommend_students BOOLEAN COMMENT 'Question 8',
    recommend_explanation TEXT COMMENT 'Question 8 explanation',
    company_cooperation ENUM('very_cooperative', 'acceptable', 'weak', 'totally_uncooperative') COMMENT 'Question 9',
    company_supervisor_signature VARCHAR(100),
    evaluation_date DATE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
    INDEX idx_assignment_id (assignment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. EVALUATION_CRITERIA_SCORES TABLE
-- Individual scores for each of the 10 evaluation criteria (1-5 scale)
-- =====================================================
CREATE TABLE evaluation_criteria_scores (
    score_id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT NOT NULL,
    criterion_number INT NOT NULL COMMENT '1-10',
    criterion_name VARCHAR(200) COMMENT 'stored for reference',
    score INT NOT NULL COMMENT '1-5',
    FOREIGN KEY (evaluation_id) REFERENCES company_evaluations(evaluation_id) ON DELETE CASCADE,
    INDEX idx_evaluation_id (evaluation_id),
    INDEX idx_criterion (evaluation_id, criterion_number),
    CONSTRAINT chk_score_range CHECK (score >= 1 AND score <= 5),
    CONSTRAINT chk_criterion_number CHECK (criterion_number >= 1 AND criterion_number <= 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. FINAL_REPORTS TABLE
-- Final report submissions from students
-- =====================================================
CREATE TABLE final_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    training_type ENUM('inside_company', 'training_courses', 'project') DEFAULT 'inside_company',
    duration VARCHAR(50),
    acknowledgment TEXT COMMENT 'Section 1',
    objectives TEXT COMMENT 'Section 2.1',
    importance TEXT COMMENT 'Section 2.2',
    nature_of_training TEXT COMMENT 'Section 2.3',
    nature_of_supervision TEXT COMMENT 'Section 2.4',
    training_experience_technical TEXT COMMENT 'Section 3, Skill 1',
    training_experience_personal TEXT COMMENT 'Section 3, Skill 2',
    training_experience_communication TEXT COMMENT 'Section 3, Skill 3',
    company_societal_impact TEXT COMMENT 'Section 4',
    relevance_to_major TEXT COMMENT 'Section 5',
    theoretical_appropriateness TEXT COMMENT 'Section 6',
    suggestions TEXT COMMENT 'Section 7',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'submitted', 'graded') DEFAULT 'draft',
    FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
    INDEX idx_assignment_id (assignment_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. FINAL_REPORT_GRADES TABLE
-- Grading for final reports (7 sections, total 20 points)
-- =====================================================
CREATE TABLE final_report_grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    report_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    section1_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 2 (ILO T.3)',
    section2_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 4 (ILO T.3)',
    section3_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 3 (ILO T.3)',
    section4_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 5 (ILO T.3)',
    section5_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 2 (ILO T.3)',
    section6_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 2 (ILO T.3)',
    section7_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 2 (ILO T.3)',
    total_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 20',
    comments TEXT,
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES final_reports(report_id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES academic_supervisors(supervisor_id) ON DELETE CASCADE,
    INDEX idx_report_id (report_id),
    INDEX idx_supervisor_id (supervisor_id),
    CONSTRAINT chk_final_section1 CHECK (section1_mark >= 0 AND section1_mark <= 2),
    CONSTRAINT chk_final_section2 CHECK (section2_mark >= 0 AND section2_mark <= 4),
    CONSTRAINT chk_final_section3 CHECK (section3_mark >= 0 AND section3_mark <= 3),
    CONSTRAINT chk_final_section4 CHECK (section4_mark >= 0 AND section4_mark <= 5),
    CONSTRAINT chk_final_section5 CHECK (section5_mark >= 0 AND section5_mark <= 2),
    CONSTRAINT chk_final_section6 CHECK (section6_mark >= 0 AND section6_mark <= 2),
    CONSTRAINT chk_final_section7 CHECK (section7_mark >= 0 AND section7_mark <= 2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. STUDENT_TOTAL_MARKS TABLE
-- Calculated total marks for each student's training assignment
-- =====================================================
CREATE TABLE student_total_marks (
    mark_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL UNIQUE,
    stage1_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 5',
    weekly_followups_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'calculated from all weeks',
    company_evaluation_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'calculated from 10 criteria (max 50, scaled)',
    final_report_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'max 20',
    total_mark DECIMAL(5,2) DEFAULT 0 COMMENT 'grand total',
    final_grade VARCHAR(5) COMMENT 'letter grade (A, B, C, etc.)',
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
    INDEX idx_assignment_id (assignment_id),
    CONSTRAINT chk_total_mark CHECK (total_mark >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 17. TRAINING_REQUEST_LETTERS TABLE
-- Letters issued by Faculty Dean for training requests
-- =====================================================
CREATE TABLE training_request_letters (
    letter_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    issued_by INT NOT NULL COMMENT 'dean user_id',
    letter_content TEXT,
    issued_date DATE NOT NULL,
    file_path VARCHAR(500) COMMENT 'if stored as file',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
    FOREIGN KEY (issued_by) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_assignment_id (assignment_id),
    INDEX idx_issued_by (issued_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 18. KPI_REPORTS TABLE
-- KPI reports generated for supervisors/coordinators
-- =====================================================
CREATE TABLE kpi_reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,
    generated_by INT,
    report_type VARCHAR(50) COMMENT 'e.g., students_performance, company_statistics',
    report_data JSON COMMENT 'flexible structure for different KPIs',
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_generated_by (generated_by),
    INDEX idx_report_type (report_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- END OF SCHEMA
-- =====================================================

