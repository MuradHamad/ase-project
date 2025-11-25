# Field Training Management System - Database Files

This directory contains all database-related files for the Field Training Management System.

## Files Overview

### 1. `schema.sql`
Complete database schema with all table definitions, indexes, foreign keys, and constraints.
- Creates database: `field_training_db`
- Character set: `utf8mb4`
- Collation: `utf8mb4_unicode_ci`
- Contains 18 tables

### 2. `sample_data.sql`
Sample/test data for development and testing purposes.
- Includes sample users (students, supervisors, coordinators, dean)
- Sample companies with approval status
- Sample training assignments
- Sample reports, evaluations, and grades
- **Default password for all test users:** `password123`

### 3. `init_database.php`
PHP script to initialize the database automatically.
- Creates database if it doesn't exist
- Executes schema creation
- Optionally loads sample data
- Can be run from CLI or web browser

### 4. `FIELD_DOCUMENTATION.md`
Detailed documentation of all database fields, including:
- Data types and lengths
- Validation rules
- Constraints
- Form field mappings
- Business rules

## Quick Start

### Option 1: Using XAMPP phpMyAdmin (GUI)
1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Create a new database named `field_training_db`
3. Select the database
4. Go to "Import" tab
5. Import `schema.sql` file
6. (Optional) Import `sample_data.sql` for test data

### Option 2: Using MySQL Command Line
```bash
# Navigate to database directory
cd database

# Login to MySQL
mysql -u root -p

# In MySQL prompt:
source schema.sql;
source sample_data.sql;  # Optional
```

### Option 3: Using PHP Initialization Script
```bash
# Navigate to database directory
cd database

# Run initialization script
php init_database.php

# Follow prompts to load sample data
```

Or via web browser:
```
http://localhost/your-project/database/init_database.php
```

To load sample data via browser:
```
http://localhost/your-project/database/init_database.php?load_sample=1
```

## Database Configuration

Default XAMPP MySQL configuration:
- **Host:** localhost
- **Username:** root
- **Password:** (empty by default)
- **Database:** field_training_db

To change these settings, edit the `init_database.php` file or update your PHP application's database configuration.

## Important Notes

### Security
1. **Remove or secure `init_database.php` in production!**
2. Change default MySQL root password in production
3. Create dedicated database user with limited privileges
4. Use prepared statements in PHP code to prevent SQL injection

### Password Hashing
Sample data uses a test password hash. In production:
```php
// Use PHP password_hash() function
$hashed_password = password_hash('user_password', PASSWORD_DEFAULT);

// Verify passwords with password_verify()
if (password_verify($input_password, $stored_hash)) {
    // Password is correct
}
```

### Sample Data Password
All test users in `sample_data.sql` have the password: **password123**

Test users include:
- Dean: dean.petra
- Coordinator: coordinator1
- Supervisors: supervisor1, supervisor2, supervisor3
- Students: student001, student002, student003, student004, student005

## Database Structure

### Core Tables
1. `users` - All system users
2. `students` - Student information
3. `companies` - Company information
4. `company_supervisors` - Company supervisor details
5. `academic_supervisors` - Academic supervisor information
6. `training_assignments` - Links students to companies

### Report & Evaluation Tables
7. `stage1_reports` - Stage 1 Company Profile reports
8. `stage1_grades` - Grading for Stage 1 reports
9. `weekly_followups` - Weekly follow-up submissions
10. `weekly_tasks` - Tasks within weekly follow-ups
11. `weekly_grades` - Grading for weekly follow-ups
12. `company_evaluations` - Company evaluation of trainees
13. `evaluation_criteria_scores` - Individual criterion scores (1-5 scale)
14. `final_reports` - Final report submissions
15. `final_report_grades` - Grading for final reports

### Supporting Tables
16. `student_total_marks` - Calculated total marks per assignment
17. `training_request_letters` - Letters issued by Dean
18. `kpi_reports` - Generated KPI reports

## Verifying Installation

After running the schema, verify installation:

```sql
USE field_training_db;

-- Check if all tables exist
SHOW TABLES;

-- Count tables (should be 18)
SELECT COUNT(*) as table_count 
FROM information_schema.tables 
WHERE table_schema = 'field_training_db';

-- Verify character set
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME
FROM information_schema.SCHEMATA
WHERE SCHEMA_NAME = 'field_training_db';
```

## Troubleshooting

### Error: Access denied
- Check MySQL username and password in `init_database.php`
- Verify MySQL service is running in XAMPP

### Error: Table already exists
- This is normal if you're re-running the schema
- To start fresh, drop the database first:
  ```sql
  DROP DATABASE IF EXISTS field_training_db;
  ```

### Error: Foreign key constraint fails
- Make sure to run `schema.sql` completely before `sample_data.sql`
- Sample data depends on existing tables and relationships

### Character encoding issues
- Ensure database uses `utf8mb4` character set
- Verify connection charset: `$conn->set_charset("utf8mb4");`

## Next Steps

After database setup:
1. Create PHP connection file (e.g., `config/database.php`)
2. Implement user authentication
3. Build forms for each report type
4. Implement grading system
5. Create KPI reporting functionality

## Support

For issues or questions, refer to:
- `FIELD_DOCUMENTATION.md` for detailed field documentation
- Project Charter for business requirements
- Iteration 1 plan for Sprint 1 requirements

