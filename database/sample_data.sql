-- =====================================================
-- Field Training Management System - Sample Data
-- University of Petra, Faculty of Information Technology
-- =====================================================

USE field_training_db;

-- =====================================================
-- 1. USERS - Sample Users
-- =====================================================

-- Password for all test users: "password123" (hashed with password_hash)
-- In production, use: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, email, full_name, phone, user_type, is_active) VALUES
-- Dean
('dean.petra', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'dean@uop.edu.jo', 'Dr. Ahmad Al-Masri', '+962 6 5715546', 'dean', TRUE),

-- Coordinators
('coordinator1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coord1@uop.edu.jo', 'Sara Al-Khalili', '+962 6 5715547', 'coordinator', TRUE),

-- Academic Supervisors
('supervisor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor1@uop.edu.jo', 'Dr. Mohammed Al-Hashimi', '+962 6 5715548', 'supervisor', TRUE),
('supervisor2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor2@uop.edu.jo', 'Dr. Fatima Al-Zahra', '+962 6 5715549', 'supervisor', TRUE),
('supervisor3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor3@uop.edu.jo', 'Dr. Khalid Al-Ahmad', '+962 6 5715550', 'supervisor', TRUE),

-- Students
('student001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student001@uop.edu.jo', 'Omar Al-Mahmoud', '+962 79 1234567', 'student', TRUE),
('student002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student002@uop.edu.jo', 'Layla Al-Rashid', '+962 79 2345678', 'student', TRUE),
('student003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student003@uop.edu.jo', 'Yusuf Al-Hassan', '+962 79 3456789', 'student', TRUE),
('student004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student004@uop.edu.jo', 'Mariam Al-Ibrahim', '+962 79 4567890', 'student', TRUE),
('student005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student005@uop.edu.jo', 'Hassan Al-Nasser', '+962 79 5678901', 'student', TRUE);

-- =====================================================
-- 2. STUDENTS - Student Details
-- =====================================================

INSERT INTO students (student_id, student_number, major, academic_year, semester) VALUES
(6, '202010001', 'Computer Science', '2024/2025', 'Fall 2024'),
(7, '202010002', 'Information Technology', '2024/2025', 'Fall 2024'),
(8, '202010003', 'Computer Science', '2024/2025', 'Fall 2024'),
(9, '202010004', 'Information Technology', '2024/2025', 'Fall 2024'),
(10, '202010005', 'Computer Science', '2024/2025', 'Fall 2024');

-- =====================================================
-- 3. ACADEMIC_SUPERVISORS - Academic Supervisor Details
-- =====================================================

INSERT INTO academic_supervisors (supervisor_id, employee_number, department) VALUES
(3, 'EMP001', 'Computer Science'),
(4, 'EMP002', 'Information Technology'),
(5, 'EMP003', 'Computer Science');

-- =====================================================
-- 4. COMPANIES - Sample Companies
-- =====================================================

INSERT INTO companies (company_name, full_address, phone, fax, email, website, approval_status, approved_by, approved_at) VALUES
('Orange Jordan', 'Amman, Shmeisani, King Hussein Street, Building 15', '+962 6 5678910', '+962 6 5678911', 'info@orange.jo', 'https://www.orange.jo', 'approved', 2, '2024-10-01 10:00:00'),
('Zain Jordan', 'Amman, Abdali, King Hussein Street, Zain Tower', '+962 6 5678920', '+962 6 5678921', 'info@zain.com.jo', 'https://www.zain.com.jo', 'approved', 2, '2024-10-02 11:00:00'),
('Umniah', 'Amman, Shmeisani, King Abdullah II Street, Umniah Tower', '+962 6 5678930', '+962 6 5678931', 'info@umniah.com.jo', 'https://www.umniah.com.jo', 'approved', 2, '2024-10-03 12:00:00'),
('ASEZA (Aqaba Special Economic Zone Authority)', 'Aqaba, ASEZA Headquarters', '+962 3 2092000', '+962 3 2092001', 'info@aseza.jo', 'https://www.aseza.jo', 'pending', NULL, NULL),
('Microsoft Jordan', 'Amman, Amman Financial District', '+962 6 5678940', '+962 6 5678941', 'jordan@microsoft.com', 'https://www.microsoft.com/en-jo', 'approved', 2, '2024-10-04 13:00:00');

-- =====================================================
-- 5. COMPANY_SUPERVISORS - Company Supervisor Details
-- =====================================================

INSERT INTO company_supervisors (company_id, full_name, email, phone, is_primary) VALUES
(1, 'Ahmad Al-Karim', 'ahmad.karim@orange.jo', '+962 79 1111111', TRUE),
(2, 'Samira Al-Rahman', 'samira.rahman@zain.com.jo', '+962 79 2222222', TRUE),
(3, 'Tariq Al-Salem', 'tariq.salem@umniah.com.jo', '+962 79 3333333', TRUE),
(4, 'Noura Al-Ali', 'noura.ali@aseza.jo', '+962 79 4444444', TRUE),
(5, 'Rami Al-Mansour', 'rami.mansour@microsoft.com', '+962 79 5555555', TRUE);

-- =====================================================
-- 6. TRAINING_ASSIGNMENTS - Sample Training Assignments
-- =====================================================

INSERT INTO training_assignments (student_id, company_id, academic_supervisor_id, training_start_date, training_end_date, status, total_hours, training_type) VALUES
(6, 1, 3, '2024-11-01', '2024-11-22', 'in_progress', 160, 'company'),
(7, 2, 4, '2024-11-05', '2024-11-26', 'in_progress', 160, 'company'),
(8, 3, 3, '2024-11-10', NULL, 'assigned', 160, 'company'),
(9, 1, 5, '2024-10-01', '2024-10-22', 'completed', 160, 'company'),
(10, 5, 4, '2024-11-15', NULL, 'assigned', 160, 'company');

-- =====================================================
-- 7. STAGE1_REPORTS - Sample Stage 1 Reports
-- =====================================================

INSERT INTO stage1_reports (assignment_id, introduction_text, intended_goals, company_details, company_supervisor_signature, company_supervisor_signed_date, status) VALUES
(1, 'Orange Jordan was established in 1995 and is one of the leading telecommunications companies in Jordan. The company specializes in mobile communications, internet services, and enterprise solutions. Orange Jordan has multiple branches across Jordan, with its headquarters located in Amman, Shmeisani area.', 
 'The intended goal of this training is to gain hands-on experience in software development, specifically in mobile application development and web technologies. I aim to understand the software development lifecycle and work with industry-standard tools and methodologies.',
 'The company has several departments including Software Development, IT Operations, Customer Service, and Network Engineering. I will be working in the Software Development department which uses various technologies including Java, JavaScript, React Native, and cloud services. The department follows Agile methodology for project management.',
 'Ahmad Al-Karim', '2024-11-01', 'submitted'),

(2, 'Zain Jordan, established in 1996, is a leading telecommunications provider in Jordan. The company specializes in mobile and internet services, digital solutions, and enterprise services. Zain operates from its headquarters in Amman, Abdali area.',
 'The main goal is to learn about database management systems, data analytics, and business intelligence tools used in telecommunications. I want to understand how large-scale data is processed and analyzed.',
 'The IT department consists of Database Administrators, Data Analysts, and System Administrators. They use Oracle databases, SQL Server, Python for data analysis, and various BI tools. The work environment is collaborative with daily standup meetings.',
 'Samira Al-Rahman', '2024-11-05', 'submitted'),

(4, 'Orange Jordan was established in 1995 and is a leading telecommunications provider in Jordan.',
 'To gain experience in network security and cybersecurity practices.',
 'The Security department handles network security, threat detection, and compliance. They use various security tools and frameworks.',
 'Ahmad Al-Karim', '2024-10-01', 'graded');

-- =====================================================
-- 8. STAGE1_GRADES - Sample Stage 1 Grades
-- =====================================================

INSERT INTO stage1_grades (report_id, supervisor_id, introduction_mark, intended_goals_mark, company_details_mark, total_mark, comments) VALUES
(3, 3, 1.0, 0.9, 2.8, 4.7, 'Well-written report with good understanding of the company structure. Minor improvements needed in goal clarification.');

-- =====================================================
-- 9. WEEKLY_FOLLOWUPS - Sample Weekly Follow-ups
-- =====================================================

INSERT INTO weekly_followups (assignment_id, week_start_date, week_end_date, week_number, company_supervisor_signed, company_supervisor_signed_date, academic_supervisor_signed, academic_supervisor_signed_date, marks_deducted) VALUES
(1, '2024-11-01', '2024-11-07', 1, TRUE, '2024-11-07', TRUE, '2024-11-08', 0),
(1, '2024-11-08', '2024-11-14', 2, TRUE, '2024-11-14', TRUE, '2024-11-15', 0),
(2, '2024-11-05', '2024-11-11', 1, TRUE, '2024-11-11', TRUE, '2024-11-12', 0),
(4, '2024-10-01', '2024-10-07', 1, TRUE, '2024-10-07', TRUE, '2024-10-08', 0),
(4, '2024-10-08', '2024-10-14', 2, TRUE, '2024-10-14', TRUE, '2024-10-15', 0),
(4, '2024-10-15', '2024-10-21', 3, TRUE, '2024-10-21', TRUE, '2024-10-22', 0);

-- =====================================================
-- 10. WEEKLY_TASKS - Sample Weekly Tasks
-- =====================================================

INSERT INTO weekly_tasks (followup_id, tasks_duties, notes, gained_skills, task_order) VALUES
(1, 'Set up development environment, reviewed existing codebase, attended team meeting', 'Getting familiar with the project structure', 'Learned to use React Native, Git version control, and project management tools', 1),
(1, 'Fixed minor bugs in mobile app, updated documentation', 'Working on small tasks first', 'Improved debugging skills and code review process', 2),
(2, 'Developed new feature for user authentication, code review', 'Feature is in testing phase', 'Gained experience in secure authentication implementation', 1),
(2, 'Participated in sprint planning meeting, updated project documentation', 'Active involvement in team activities', 'Learned Agile methodology practices', 2),
(3, 'Reviewed database schema, learned SQL queries for data extraction', 'Focusing on database operations', 'Improved SQL skills, learned data extraction techniques', 1),
(3, 'Created reports using BI tools, presented findings to team', 'First experience with business intelligence tools', 'Gained skills in data visualization and report generation', 2),
(4, 'Network security assessment, reviewed firewall configurations', 'Learning security protocols', 'Gained knowledge in network security best practices', 1),
(5, 'Implemented security patches, monitored system logs', 'Hands-on security work', 'Learned about threat detection and incident response', 1),
(6, 'Final security audit, prepared security documentation', 'Completing training tasks', 'Comprehensive understanding of security frameworks', 1);

-- =====================================================
-- 11. WEEKLY_GRADES - Sample Weekly Grades
-- =====================================================

INSERT INTO weekly_grades (followup_id, supervisor_id, mark, comments) VALUES
(1, 3, 9.5, 'Good progress in first week. Continue with active participation.'),
(2, 3, 10.0, 'Excellent work on the new feature. Well documented.'),
(3, 4, 9.0, 'Good understanding of database concepts. Keep it up.'),
(4, 3, 9.5, 'Great progress. Security implementation looks solid.'),
(5, 3, 10.0, 'Excellent work on security monitoring.'),
(6, 3, 10.0, 'Outstanding final week. Comprehensive documentation.');

-- =====================================================
-- 12. COMPANY_EVALUATIONS - Sample Company Evaluations
-- =====================================================

INSERT INTO company_evaluations (assignment_id, intended_goals, assigned_tasks_summary, feedback_curriculum_a, feedback_curriculum_b, feedback_curriculum_c, additional_notes, recommend_students, recommend_explanation, company_cooperation, company_supervisor_signature, evaluation_date) VALUES
(4, 'The student aimed to learn about network security and cybersecurity practices. The training program was designed to provide hands-on experience in security assessment and threat detection.',
 'The student worked on security assessments, firewall configuration reviews, implementation of security patches, system log monitoring, and security documentation. Tasks were progressively more complex and aligned with learning objectives.',
 'The student demonstrated strong theoretical knowledge in computer networks and security concepts. The university curriculum provided a solid foundation that allowed the student to quickly adapt to practical security implementations.',
 'The 160-hour training period over 4 weeks was appropriate and allowed sufficient time for the student to gain meaningful experience. The timing during fall semester was convenient.',
 'The combination of theoretical learning at university and practical training at the company worked well. The student could apply concepts learned in coursework to real-world scenarios.',
 'The student showed great potential and would be an asset to any security team. We recommend continuing the relationship with the university.',
 TRUE, 'The student demonstrated excellent work ethic, technical skills, and professionalism. The training program was successful, and we would welcome more students from this program.',
 'very_cooperative', 'Ahmad Al-Karim', '2024-10-22');

-- =====================================================
-- 13. EVALUATION_CRITERIA_SCORES - Sample Evaluation Scores
-- =====================================================

INSERT INTO evaluation_criteria_scores (evaluation_id, criterion_number, criterion_name, score) VALUES
(1, 1, 'Student''s ability to fulfill assigned duties', 5),
(1, 2, 'Student''s response to training directions', 5),
(1, 3, 'Student''s interaction and ability to learn', 5),
(1, 4, 'Student''s general skills progression', 5),
(1, 5, 'Student''s ability to communicate and cooperate with others', 5),
(1, 6, 'Student''s commitment to training time/attendance', 5),
(1, 7, 'Student''s Compliance with procedures and regulations', 5),
(1, 8, 'Student''s organizational and time management skills', 4),
(1, 9, 'Student''s ability to propose new ideas', 4),
(1, 10, 'Student''s analytical skills', 5);

-- =====================================================
-- 14. FINAL_REPORTS - Sample Final Reports
-- =====================================================

INSERT INTO final_reports (assignment_id, training_type, duration, acknowledgment, objectives, importance, nature_of_training, nature_of_supervision, training_experience_technical, training_experience_personal, training_experience_communication, company_societal_impact, relevance_to_major, theoretical_appropriateness, suggestions, status) VALUES
(4, 'inside_company', '4 weeks (160 hours)', 
 'I would like to express my sincere gratitude to Orange Jordan for providing me with this valuable training opportunity. Special thanks to Mr. Ahmad Al-Karim, my company supervisor, for his continuous guidance and support throughout the training period. I also thank all team members who helped me learn and grow during this experience.',
 '1. Gain hands-on experience in network security and cybersecurity practices. 2. Understand real-world security challenges and solutions. 3. Learn industry-standard security tools and frameworks. 4. Develop professional skills in a corporate environment.',
 'Field training is crucial for bridging the gap between theoretical knowledge and practical application. It provides students with real-world experience, enhances employability, and helps in career orientation. This training was particularly important for understanding the critical role of cybersecurity in protecting organizational assets.',
 'The training focused on network security assessment, firewall configuration, security patch implementation, threat detection, and security documentation. The main areas covered were: Network Security, Cybersecurity, Threat Analysis, and Security Compliance.',
 'I received task-based supervision where I was assigned specific security tasks to complete. My supervisor provided guidance when needed and reviewed my work. The supervision style was supportive and encouraged independent problem-solving while ensuring I stayed on track.',
 'Technical Skills: I worked on network security assessments using tools like Wireshark and Nmap. I learned to configure firewalls, implement security patches, and analyze system logs. I worked both individually on assigned tasks and in a group during security team meetings. The major systems used were Linux servers, network infrastructure, and security monitoring tools. This experience significantly improved my technical capabilities in cybersecurity.',
 'Personal Skills: I developed better time management and organizational skills by handling multiple tasks and meeting deadlines. I learned to work under pressure during security incidents and improved my attention to detail, which is crucial in security work. The training helped me become more responsible and self-disciplined.',
 'Communication Skills: I participated in team meetings, presented security findings to the team, and wrote comprehensive security documentation. I learned to communicate technical information clearly to both technical and non-technical audiences. This improved my professional communication skills.',
 'Orange Jordan contributes significantly to Jordan''s digital transformation by providing reliable telecommunications services. The company supports local communities through various CSR initiatives and provides employment opportunities. Their commitment to cybersecurity helps protect national infrastructure and individual users from cyber threats.',
 'The training was highly relevant to my Computer Science major. It directly applied concepts learned in courses like Network Security, Database Systems, and Operating Systems. The practical experience complemented my theoretical knowledge perfectly.',
 'My theoretical knowledge from university courses provided a solid foundation. I could quickly understand security concepts and apply them. Strengths: Strong foundation in networking and security theory, good problem-solving skills. Weaknesses: Initially lacked hands-on experience with security tools, but this improved significantly during training.',
 'Suggestions: 1. Include more hands-on security labs in the curriculum. 2. Organize guest lectures from industry professionals. 3. Provide opportunities for students to work on security projects earlier in their academic journey. 4. Consider establishing a security lab with industry-standard tools.',
 'submitted');

-- =====================================================
-- 15. FINAL_REPORT_GRADES - Sample Final Report Grades
-- =====================================================

INSERT INTO final_report_grades (report_id, supervisor_id, section1_mark, section2_mark, section3_mark, section4_mark, section5_mark, section6_mark, section7_mark, total_mark, comments) VALUES
(1, 3, 2.0, 3.8, 2.9, 4.8, 2.0, 2.0, 2.0, 19.5, 'Excellent final report. Well-structured, comprehensive, and demonstrates deep understanding of the training experience. Minor improvements could be made in Section 2 organization.');

-- =====================================================
-- 16. STUDENT_TOTAL_MARKS - Sample Total Marks
-- =====================================================

INSERT INTO student_total_marks (assignment_id, stage1_mark, weekly_followups_mark, company_evaluation_mark, final_report_mark, total_mark, final_grade) VALUES
(4, 4.7, 29.0, 48.0, 19.5, 101.2, 'A+');

-- =====================================================
-- 17. TRAINING_REQUEST_LETTERS - Sample Letters
-- =====================================================

INSERT INTO training_request_letters (assignment_id, issued_by, letter_content, issued_date) VALUES
(1, 1, 'This is to certify that student Omar Al-Mahmoud (Student ID: 202010001) is enrolled in the Computer Science program at the University of Petra, Faculty of Information Technology. We request that you provide field training opportunity for this student for a period of 160 hours (4 weeks) as part of their academic requirements.', '2024-10-25'),
(2, 1, 'This is to certify that student Layla Al-Rashid (Student ID: 202010002) is enrolled in the Information Technology program at the University of Petra, Faculty of Information Technology. We request that you provide field training opportunity for this student for a period of 160 hours (4 weeks) as part of their academic requirements.', '2024-10-28'),
(4, 1, 'This is to certify that student Mariam Al-Ibrahim (Student ID: 202010004) is enrolled in the Information Technology program at the University of Petra, Faculty of Information Technology. We request that you provide field training opportunity for this student for a period of 160 hours (4 weeks) as part of their academic requirements.', '2024-09-25');

-- =====================================================
-- END OF SAMPLE DATA
-- =====================================================

