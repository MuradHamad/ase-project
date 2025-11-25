# Field Training System - Database Field Documentation

## Data Types and Validation Rules

This document provides detailed information about field lengths, data types, and validation rules for each form field in the Field Training Management System.

---

## 1. USERS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `user_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `username` | VARCHAR | 50 | UNIQUE, NOT NULL | Alphanumeric, no spaces, case-sensitive |
| `password` | VARCHAR | 255 | NOT NULL | Hashed using PHP password_hash() |
| `email` | VARCHAR | 100 | UNIQUE | Valid email format |
| `full_name` | VARCHAR | 100 | NOT NULL | Letters, spaces, hyphens, max 100 chars |
| `phone` | VARCHAR | 20 | - | Numbers, dashes, parentheses, spaces |
| `user_type` | ENUM | - | NOT NULL | Values: 'student', 'supervisor', 'coordinator', 'dean', 'company_supervisor' |
| `is_active` | BOOLEAN | - | DEFAULT TRUE | TRUE or FALSE |
| `created_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-generated |

---

## 2. STUDENTS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `student_id` | INT | - | PRIMARY KEY, FK to users.user_id | Must exist in users table |
| `student_number` | VARCHAR | 20 | UNIQUE, NOT NULL | Alphanumeric, unique identifier |
| `major` | VARCHAR | 100 | NOT NULL | Faculty major (e.g., "Computer Science", "Information Technology") |
| `academic_year` | VARCHAR | 20 | - | Format: "2024/2025" |
| `semester` | VARCHAR | 20 | - | Format: "Fall 2024", "Spring 2025" |

---

## 3. COMPANIES TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `company_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `company_name` | VARCHAR | 200 | NOT NULL | Company legal name, max 200 chars |
| `full_address` | TEXT | - | - | Complete address including street, city, country |
| `phone` | VARCHAR | 20 | - | International format supported |
| `fax` | VARCHAR | 20 | - | International format supported |
| `email` | VARCHAR | 100 | - | Valid email format, business email |
| `website` | VARCHAR | 200 | - | Valid URL format (http:// or https://) |
| `approval_status` | ENUM | - | DEFAULT 'pending' | Values: 'pending', 'approved', 'rejected' |
| `approved_by` | INT | - | FK to users.user_id | Coordinator user_id who approved |
| `approved_at` | TIMESTAMP | - | NULL | Set when status changes to 'approved' |
| `created_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-generated |
| `updated_at` | TIMESTAMP | - | ON UPDATE CURRENT_TIMESTAMP | Auto-updated |

---

## 4. COMPANY_SUPERVISORS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `supervisor_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `company_id` | INT | - | NOT NULL, FK to companies.company_id | Must exist in companies table |
| `user_id` | INT | - | FK to users.user_id | Optional, if supervisor has system account |
| `full_name` | VARCHAR | 100 | NOT NULL | Full name of company supervisor |
| `email` | VARCHAR | 100 | - | Valid email format |
| `phone` | VARCHAR | 20 | - | Contact phone number |
| `is_primary` | BOOLEAN | - | DEFAULT TRUE | One primary supervisor per company |

---

## 5. ACADEMIC_SUPERVISORS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `supervisor_id` | INT | - | PRIMARY KEY, FK to users.user_id | Must exist in users table |
| `employee_number` | VARCHAR | 20 | UNIQUE | University employee ID |
| `department` | VARCHAR | 100 | - | Department name within faculty |

---

## 6. TRAINING_ASSIGNMENTS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `assignment_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `student_id` | INT | - | NOT NULL, FK to students.student_id | Must exist in students table |
| `company_id` | INT | - | NOT NULL, FK to companies.company_id | Company must be approved |
| `academic_supervisor_id` | INT | - | NOT NULL, FK to academic_supervisors.supervisor_id | Must exist in academic_supervisors |
| `training_start_date` | DATE | - | NOT NULL | Valid date, format: YYYY-MM-DD |
| `training_end_date` | DATE | - | - | Must be >= training_start_date, null until training ends |
| `status` | ENUM | - | DEFAULT 'assigned' | Values: 'assigned', 'in_progress', 'completed', 'cancelled' |
| `total_hours` | INT | - | DEFAULT 160 | Standard: 160 hours (20 days × 8 hours) |
| `training_type` | VARCHAR | 50 | DEFAULT 'company' | Current: 'company', future: other tracks |
| `created_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-generated |

**Validation Rules:**
- `training_end_date` must be NULL or >= `training_start_date`
- Company must have `approval_status = 'approved'` before assignment

---

## 7. STAGE1_REPORTS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `report_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `assignment_id` | INT | - | NOT NULL, FK to training_assignments.assignment_id | One report per assignment |
| `introduction_text` | TEXT | - | - | Max ~65,535 chars. Introduction about place of training (1 point) |
| `intended_goals` | TEXT | - | - | Max ~65,535 chars. Intended goal of training (1 point) |
| `company_details` | TEXT | - | - | Max ~65,535 chars. Company department, roles, software (3 points) |
| `company_supervisor_signature` | VARCHAR | 100 | - | Supervisor name or signature text |
| `company_supervisor_signed_date` | DATE | - | - | Date when supervisor signed |
| `submitted_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on submission |
| `status` | ENUM | - | DEFAULT 'draft' | Values: 'draft', 'submitted', 'graded' |

**Form Fields Mapping:**
- Introduction about the place of training → `introduction_text`
- Intended goal of training → `intended_goals`
- Describe company department, roles and software → `company_details`

---

## 8. STAGE1_GRADES TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `grade_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `report_id` | INT | - | NOT NULL, FK to stage1_reports.report_id | One grade per report (latest kept) |
| `supervisor_id` | INT | - | NOT NULL, FK to academic_supervisors.supervisor_id | Academic supervisor who graded |
| `introduction_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 1.00 |
| `intended_goals_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 1.00 |
| `company_details_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 3.00 |
| `total_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 5.00 (sum of above) |
| `comments` | TEXT | - | - | Supervisor comments |
| `graded_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on grading |

**Point Distribution:**
- Introduction: 1 point
- Intended goals: 1 point
- Company details: 3 points
- **Total: 5 points**

---

## 9. WEEKLY_FOLLOWUPS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `followup_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `assignment_id` | INT | - | NOT NULL, FK to training_assignments.assignment_id | Multiple followups per assignment |
| `week_start_date` | DATE | - | NOT NULL | Start of week period |
| `week_end_date` | DATE | - | NOT NULL | End of week period, must be >= week_start_date |
| `week_number` | INT | - | - | Calculated: Week 1, 2, 3, etc. |
| `company_supervisor_signed` | BOOLEAN | - | DEFAULT FALSE | Signature status |
| `company_supervisor_signed_date` | DATE | - | - | Date when company supervisor signed |
| `academic_supervisor_signed` | BOOLEAN | - | DEFAULT FALSE | Signature status |
| `academic_supervisor_signed_date` | DATE | - | - | Date when academic supervisor signed |
| `marks_deducted` | INT | - | DEFAULT 0 | 2 marks deducted if student doesn't follow up |
| `submitted_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on submission |

**Business Rules:**
- Week period should be ~7 days
- Multiple weekly followups per training assignment (typically 4 weeks = 160 hours / 40 hours per week)
- 2 marks deducted if academic supervisor doesn't receive follow-up

---

## 10. WEEKLY_TASKS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `task_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `followup_id` | INT | - | NOT NULL, FK to weekly_followups.followup_id | Multiple tasks per followup |
| `tasks_duties` | TEXT | - | - | Tasks and duties assigned to trainee |
| `notes` | TEXT | - | - | Additional notes |
| `gained_skills` | TEXT | - | - | Skills or knowledge gained |
| `task_order` | INT | - | DEFAULT 1 | Order within the week (1, 2, 3, etc.) |

**Form Fields Mapping:**
- Tasks and Duties assigned to trainee → `tasks_duties`
- Notes → `notes`
- Gained skills or knowledge → `gained_skills`

---

## 11. WEEKLY_GRADES TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `grade_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `followup_id` | INT | - | NOT NULL, FK to weekly_followups.followup_id | One grade per followup |
| `supervisor_id` | INT | - | NOT NULL, FK to academic_supervisors.supervisor_id | Academic supervisor who graded |
| `mark` | DECIMAL(5,2) | - | DEFAULT 0 | Non-negative, typically 0-100 or 0-10 scale |
| `comments` | TEXT | - | - | Supervisor comments |
| `graded_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on grading |

---

## 12. COMPANY_EVALUATIONS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `evaluation_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `assignment_id` | INT | - | NOT NULL, FK to training_assignments.assignment_id | One evaluation per assignment |
| `intended_goals` | TEXT | - | - | Section A: Intended goals of training |
| `assigned_tasks_summary` | TEXT | - | - | Section B: Summary of assigned tasks |
| `feedback_curriculum_a` | TEXT | - | - | Section 2a: Theoretical knowledge appropriateness |
| `feedback_curriculum_b` | TEXT | - | - | Section 2b: Convenience of training time |
| `feedback_curriculum_c` | TEXT | - | - | Section 2c: Convenience of training mechanism |
| `additional_notes` | TEXT | - | - | Section 3: Additional notes and suggestions |
| `recommend_students` | BOOLEAN | - | - | Question 8: Recommend other students? |
| `recommend_explanation` | TEXT | - | - | Question 8: Explanation |
| `company_cooperation` | ENUM | - | - | Question 9: Values: 'very_cooperative', 'acceptable', 'weak', 'totally_uncooperative' |
| `company_supervisor_signature` | VARCHAR | 100 | - | Supervisor name or signature |
| `evaluation_date` | DATE | - | - | Date of evaluation |
| `submitted_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on submission |

**Question 9 Options:**
- very_cooperative
- acceptable
- weak
- totally_uncooperative

---

## 13. EVALUATION_CRITERIA_SCORES TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `score_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `evaluation_id` | INT | - | NOT NULL, FK to company_evaluations.evaluation_id | 10 scores per evaluation |
| `criterion_number` | INT | - | NOT NULL | Values: 1 to 10 |
| `criterion_name` | VARCHAR | 200 | - | Stored for reference |
| `score` | INT | - | NOT NULL | Values: 1 to 5 (1=lowest, 5=highest) |

**Evaluation Criteria (1-10):**
1. Student's ability to fulfill assigned duties
2. Student's response to training directions
3. Student's interaction and ability to learn
4. Student's general skills progression
5. Student's ability to communicate and cooperate with others
6. Student's commitment to training time/attendance
7. Student's Compliance with procedures and regulations
8. Student's organizational and time management skills
9. Student's ability to propose new ideas
10. Student's analytical skills

**Scoring Scale:**
- 1 = Poor
- 2 = Below Average
- 3 = Average
- 4 = Good
- 5 = Excellent

**Total Possible Score:** 50 points (10 criteria × 5 points each)

---

## 14. FINAL_REPORTS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `report_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `assignment_id` | INT | - | NOT NULL, FK to training_assignments.assignment_id | One report per assignment |
| `training_type` | ENUM | - | DEFAULT 'inside_company' | Values: 'inside_company', 'training_courses', 'project' |
| `duration` | VARCHAR | 50 | - | Training duration description |
| `acknowledgment` | TEXT | - | - | Section 1: Acknowledgment (2 points) |
| `objectives` | TEXT | - | - | Section 2.1: Objectives of training (4 points) |
| `importance` | TEXT | - | - | Section 2.2: Importance of training (part of Section 2) |
| `nature_of_training` | TEXT | - | - | Section 2.3: Nature of training (part of Section 2) |
| `nature_of_supervision` | TEXT | - | - | Section 2.4: Nature of supervision (part of Section 2) |
| `training_experience_technical` | TEXT | - | - | Section 3, Skill 1: Technical skills (3 points) |
| `training_experience_personal` | TEXT | - | - | Section 3, Skill 2: Personal skills (part of Section 3) |
| `training_experience_communication` | TEXT | - | - | Section 3, Skill 3: Communication skills (part of Section 3) |
| `company_societal_impact` | TEXT | - | - | Section 4: Company impact on local society (5 points) |
| `relevance_to_major` | TEXT | - | - | Section 5: Relevance to major (2 points) |
| `theoretical_appropriateness` | TEXT | - | - | Section 6: Theoretical knowledge appropriateness (2 points) |
| `suggestions` | TEXT | - | - | Section 7: Suggestions to improve field training (2 points) |
| `submitted_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on submission |
| `status` | ENUM | - | DEFAULT 'draft' | Values: 'draft', 'submitted', 'graded' |

---

## 15. FINAL_REPORT_GRADES TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `grade_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `report_id` | INT | - | NOT NULL, FK to final_reports.report_id | One grade per report |
| `supervisor_id` | INT | - | NOT NULL, FK to academic_supervisors.supervisor_id | Academic supervisor who graded |
| `section1_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 2.00 (ILO T.3) |
| `section2_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 4.00 (ILO T.3) |
| `section3_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 3.00 (ILO T.3) |
| `section4_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 5.00 (ILO T.3) |
| `section5_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 2.00 (ILO T.3) |
| `section6_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 2.00 (ILO T.3) |
| `section7_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 2.00 (ILO T.3) |
| `total_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Range: 0.00 to 20.00 (sum of all sections) |
| `comments` | TEXT | - | - | Supervisor comments |
| `graded_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on grading |

**Point Distribution (Total: 20 points):**
- Section 1 (Acknowledgment): 2 points (ILO T.3)
- Section 2 (Introduction - combined): 4 points (ILO T.3)
- Section 3 (Training Experience - combined): 3 points (ILO T.3)
- Section 4 (Company Societal Impact): 5 points (ILO T.3)
- Section 5 (Relevance to Major): 2 points (ILO T.3)
- Section 6 (Theoretical Appropriateness): 2 points (ILO T.3)
- Section 7 (Suggestions): 2 points (ILO T.3)
- **Total: 20 points**

---

## 16. STUDENT_TOTAL_MARKS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `mark_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `assignment_id` | INT | - | NOT NULL UNIQUE, FK to training_assignments.assignment_id | One record per assignment |
| `stage1_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Max: 5.00 |
| `weekly_followups_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Calculated from all weekly grades |
| `company_evaluation_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Max: 50 (scaled down), or percentage |
| `final_report_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Max: 20.00 |
| `total_mark` | DECIMAL(5,2) | - | DEFAULT 0 | Grand total (sum of all components) |
| `final_grade` | VARCHAR | 5 | - | Letter grade: A+, A, B+, B, C+, C, D, F |
| `calculated_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Auto-updated when marks change |

**Calculation Logic:**
- Stage 1: Direct mark (0-5)
- Weekly Followups: Sum or average of all weekly grades
- Company Evaluation: Total score from 10 criteria (0-50), may need scaling
- Final Report: Direct mark (0-20)
- Total: Sum of all components
- Final Grade: Calculated based on total mark and grading scale

---

## 17. TRAINING_REQUEST_LETTERS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `letter_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `assignment_id` | INT | - | NOT NULL, FK to training_assignments.assignment_id | One letter per assignment |
| `issued_by` | INT | - | NOT NULL, FK to users.user_id | Dean user_id |
| `letter_content` | TEXT | - | - | Letter body text |
| `issued_date` | DATE | - | NOT NULL | Date letter was issued |
| `file_path` | VARCHAR | 500 | - | Path to stored PDF/document file |
| `created_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-generated |

---

## 18. KPI_REPORTS TABLE

| Field | Type | Length | Constraints | Validation Rules |
|-------|------|--------|-------------|------------------|
| `report_id` | INT | - | PRIMARY KEY, AUTO_INCREMENT | Auto-generated |
| `generated_by` | INT | - | FK to users.user_id | User who generated report |
| `report_type` | VARCHAR | 50 | - | Values: 'students_performance', 'company_statistics', etc. |
| `report_data` | JSON | - | - | Flexible JSON structure for different KPIs |
| `generated_at` | TIMESTAMP | - | DEFAULT CURRENT_TIMESTAMP | Auto-set on generation |

**Report Types:**
- `students_performance`: Overall student performance metrics
- `company_statistics`: Company evaluation and statistics
- `completion_rates`: Training completion statistics
- `supervisor_workload`: Academic supervisor workload distribution

---

## General Validation Rules

### Email Format
- Must contain @ symbol
- Must have valid domain
- Recommended: Use PHP `filter_var($email, FILTER_VALIDATE_EMAIL)`

### Phone Numbers
- Accept international format
- May contain: digits, spaces, dashes, parentheses
- Example formats: +962 6 1234567, (06) 123-4567, 061234567

### Dates
- Format: YYYY-MM-DD (MySQL DATE format)
- No future dates for historical records
- Training dates must be logical (end >= start)

### Text Fields
- TEXT type: Max ~65,535 characters
- VARCHAR: Max length as specified per field
- Trim whitespace on input
- Sanitize HTML/scripts for security

### Passwords
- Store hashed using PHP `password_hash($password, PASSWORD_DEFAULT)`
- Verify using PHP `password_verify($password, $hash)`
- Never store plain text passwords

### ENUM Values
- Must exactly match one of the allowed values
- Case-sensitive in some MySQL configurations
- Use lowercase values for consistency

---

## Database Constraints Summary

1. **Foreign Keys:** All relationships enforced with CASCADE or SET NULL where appropriate
2. **Check Constraints:** Numeric ranges enforced (marks, scores, dates)
3. **Unique Constraints:** Usernames, emails, student numbers, employee numbers
4. **NOT NULL:** Required fields enforced at database level
5. **Default Values:** Appropriate defaults set for common fields

---

## Notes

- All timestamps are stored in UTC or server timezone
- Consider timezone handling for international users
- Text fields use UTF8MB4 for full Unicode support (emojis, Arabic text)
- Dates use DATE type for date-only values (no time component)
- DECIMAL(5,2) allows values from -999.99 to 999.99 (sufficient for marks)

