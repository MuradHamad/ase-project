# Field Training Management System

A comprehensive web-based system for managing field training at University of Petra, Faculty of Information Technology.

## Project Overview

This system automates the field training process for IT students, facilitating communication between the university, students, and training companies. It includes features for:

- Company management and approval
- Training assignment management
- Student report submissions (Stage 1, Weekly Follow-ups, Final Report)
- Company trainee evaluations
- Academic supervisor grading
- KPI reporting

## Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Backend:** PHP (Basic PHP, no frameworks)
- **Database:** MySQL
- **Server:** XAMPP

## Project Structure

```
ase project/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js            # JavaScript utilities
├── config/
│   ├── config.php             # Application configuration
│   └── database.php           # Database connection
├── database/
│   ├── schema.sql             # Database schema
│   ├── sample_data.sql        # Sample/test data
│   ├── init_database.php      # Database initialization script
│   └── README.md              # Database documentation
├── includes/
│   ├── auth.php               # Authentication functions
│   ├── header.php             # Header component
│   └── footer.php             # Footer component
├── student/
│   ├── dashboard.php          # Student dashboard
│   ├── companies.php          # Company selection
│   ├── stage1_form.php        # Stage 1 Report form
│   └── weekly_followup.php    # Weekly follow-up form
├── supervisor/
│   ├── dashboard.php          # Supervisor dashboard
│   └── grade.php              # Grading interface
├── coordinator/
│   └── dashboard.php          # Coordinator dashboard (company approval)
├── dean/
│   └── (to be implemented)    # Dean pages
├── index.php                  # Login page
├── logout.php                 # Logout handler
└── README.md                  # This file
```

## Installation

### Prerequisites

- XAMPP (or similar WAMP/LAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Steps

1. **Clone/Copy the project to XAMPP htdocs:**
   ```
   C:\xampp\htdocs\ase project
   ```

2. **Initialize the database:**
   - Option 1: Use phpMyAdmin
     - Open `http://localhost/phpmyadmin`
     - Import `database/schema.sql`
     - (Optional) Import `database/sample_data.sql` for test data
   
   - Option 2: Use command line
     ```bash
     cd database
     php init_database.php
     ```
   
   - Option 3: Use MySQL command line
     ```bash
     mysql -u root -p < database/schema.sql
     mysql -u root -p < database/sample_data.sql
     ```

3. **Configure database connection (if needed):**
   Edit `config/database.php` if your MySQL credentials differ:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'field_training_db');
   ```

4. **Start XAMPP services:**
   - Apache
   - MySQL

5. **Access the application:**
   ```
   http://localhost/ase project/
   ```

## Test Accounts

The sample data includes these test accounts (password: `password123`):

- **Dean:** `dean.petra`
- **Coordinator:** `coordinator1`
- **Supervisors:** `supervisor1`, `supervisor2`, `supervisor3`
- **Students:** `student001`, `student002`, `student003`, `student004`, `student005`

## Features Implemented

### ✅ Completed

1. **Authentication System**
   - Login/Logout
   - Session management
   - Role-based access control

2. **Student Features**
   - Dashboard with assignment overview
   - Company selection
   - Stage 1 Report submission
   - Weekly Follow-up submission (with dynamic task rows)

3. **Supervisor Features**
   - Dashboard with assigned students
   - Stage 1 Report grading interface

4. **Coordinator Features**
   - Company approval/rejection interface
   - Approved companies list

5. **Shared Components**
   - Responsive header/navigation
   - Footer
   - Flash message system
   - Form validation
   - Modern, clean UI

### 🚧 Pending Implementation

1. **Student Features**
   - Final Report form
   - Company Evaluation form (for company supervisors)
   - View report pages
   - Weekly follow-up list/view

2. **Supervisor Features**
   - Weekly follow-up grading
   - Final Report grading
   - Student detail view
   - Mark calculation

3. **Coordinator Features**
   - Company management (add/edit/delete)
   - Reports and statistics
   - Assignment management

4. **Dean Features**
   - Training request letter generation

5. **Additional Features**
   - File upload functionality
   - Email notifications
   - Advanced KPI reporting
   - Export functionality (PDF, Excel)

## User Roles

1. **Student**
   - Select company for training
   - Submit Stage 1 Report
   - Submit Weekly Follow-ups
   - Submit Final Report
   - View grades and marks

2. **Academic Supervisor**
   - View assigned students
   - Grade student reports
   - Track student progress
   - Generate reports

3. **Field Training Coordinator**
   - Approve/reject companies
   - Manage company accounts
   - View statistics and reports

4. **Faculty Dean**
   - Issue training request letters
   - View overall statistics

5. **Company Supervisor** (future)
   - Evaluate trainees
   - Sign reports
   - Submit company evaluation

## Database Schema

The database includes 18 tables:

- **Core:** users, students, companies, company_supervisors, academic_supervisors, training_assignments
- **Reports:** stage1_reports, weekly_followups, weekly_tasks, final_reports
- **Evaluations:** company_evaluations, evaluation_criteria_scores
- **Grading:** stage1_grades, weekly_grades, final_report_grades, student_total_marks
- **Supporting:** training_request_letters, kpi_reports

See `database/FIELD_DOCUMENTATION.md` for detailed field documentation.

## Security Notes

- ✅ Password hashing using PHP `password_hash()`
- ✅ Prepared statements for SQL queries (prevents SQL injection)
- ✅ Input sanitization with `sanitizeInput()`
- ✅ Session-based authentication
- ✅ Role-based access control
- ⚠️ Remove `database/init_database.php` in production
- ⚠️ Change default MySQL password in production
- ⚠️ Implement CSRF protection for forms (recommended)
- ⚠️ Add file upload validation if implementing uploads

## Development Notes

- Uses basic PHP (no frameworks) as per requirements
- MySQLi extension for database operations
- Responsive design with CSS Grid and Flexbox
- Vanilla JavaScript (no jQuery or frameworks)
- UTF8MB4 character set for Arabic text support

## Future Enhancements

- Email notifications
- File upload for documents
- PDF report generation
- Advanced search and filtering
- Mobile app (optional)
- Integration with university systems
- Real-time notifications
- Calendar integration

## Support

For issues or questions:
- Check `database/README.md` for database setup
- Review `database/FIELD_DOCUMENTATION.md` for field details
- Check project charter for business requirements

## License

This project is developed for University of Petra, Faculty of Information Technology.

---

**Last Updated:** 2024
**Version:** 1.0.0 (Initial Release)

