&lt;?php
/**
 * Automatic Database Installation Script
 * This will create the database and all tables automatically
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'field_training_db');

echo "&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
    &lt;title&gt;Database Installation&lt;/title&gt;
    &lt;style&gt;
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        h1 { color: #9d1e26; }
        .step { padding: 10px; margin: 10px 0; border-left: 4px solid #0d255e; background: #f8fafc; }
    &lt;/style&gt;
&lt;/head&gt;
&lt;body&gt;
    &lt;h1&gt;Database Installation&lt;/h1&gt;";

// Step 1: Connect to MySQL
echo "&lt;div class='step'&gt;&lt;strong&gt;Step 1:&lt;/strong&gt; Connecting to MySQL...&lt;/div&gt;";
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn-&gt;connect_error) {
    die("&lt;p class='error'&gt;❌ Failed: " . $conn-&gt;connect_error . "&lt;/p&gt;&lt;p&gt;Make sure XAMPP MySQL is running!&lt;/p&gt;&lt;/body&gt;&lt;/html&gt;");
}
echo "&lt;p class='success'&gt;✓ Connected to MySQL&lt;/p&gt;";

// Step 2: Create database
echo "&lt;div class='step'&gt;&lt;strong&gt;Step 2:&lt;/strong&gt; Creating database...&lt;/div&gt;";
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn-&gt;query($sql)) {
    echo "&lt;p class='success'&gt;✓ Database created/verified&lt;/p&gt;";
} else {
    die("&lt;p class='error'&gt;❌ Failed: " . $conn-&gt;error . "&lt;/p&gt;&lt;/body&gt;&lt;/html&gt;");
}

// Step 3: Select database
$conn-&gt;select_db(DB_NAME);

// Step 4: Create tables
echo "&lt;div class='step'&gt;&lt;strong&gt;Step 3:&lt;/strong&gt; Creating tables...&lt;/div&gt;";

$tables = [
    "users" =&gt; "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        user_type ENUM('student', 'supervisor', 'coordinator', 'dean', 'company_supervisor') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_user_type (user_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "students" =&gt; "CREATE TABLE IF NOT EXISTS students (
        student_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNIQUE NOT NULL,
        student_number VARCHAR(50) UNIQUE NOT NULL,
        major VARCHAR(100),
        gpa DECIMAL(3,2),
        phone VARCHAR(20),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        INDEX idx_student_number (student_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "academic_supervisors" =&gt; "CREATE TABLE IF NOT EXISTS academic_supervisors (
        supervisor_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNIQUE NOT NULL,
        department VARCHAR(100),
        office_location VARCHAR(100),
        phone VARCHAR(20),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "companies" =&gt; "CREATE TABLE IF NOT EXISTS companies (
        company_id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(255) NOT NULL,
        industry VARCHAR(100),
        address TEXT,
        phone VARCHAR(20),
        email VARCHAR(255),
        website VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_company_name (company_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "company_supervisors" =&gt; "CREATE TABLE IF NOT EXISTS company_supervisors (
        company_supervisor_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNIQUE NOT NULL,
        company_id INT NOT NULL,
        position VARCHAR(100),
        phone VARCHAR(20),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "training_assignments" =&gt; "CREATE TABLE IF NOT EXISTS training_assignments (
        assignment_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        company_id INT NOT NULL,
        academic_supervisor_id INT,
        company_supervisor_id INT,
        training_start_date DATE,
        training_end_date DATE,
        status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
        FOREIGN KEY (company_id) REFERENCES companies(company_id) ON DELETE CASCADE,
        FOREIGN KEY (academic_supervisor_id) REFERENCES academic_supervisors(supervisor_id),
        FOREIGN KEY (company_supervisor_id) REFERENCES company_supervisors(company_supervisor_id),
        INDEX idx_student (student_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "stage1_reports" =&gt; "CREATE TABLE IF NOT EXISTS stage1_reports (
        report_id INT AUTO_INCREMENT PRIMARY KEY,
        assignment_id INT NOT NULL,
        company_profile TEXT,
        training_plan TEXT,
        status ENUM('submitted', 'graded') DEFAULT 'submitted',
        grade DECIMAL(5,2),
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "weekly_followups" =&gt; "CREATE TABLE IF NOT EXISTS weekly_followups (
        followup_id INT AUTO_INCREMENT PRIMARY KEY,
        assignment_id INT NOT NULL,
        week_number INT NOT NULL,
        activities TEXT,
        achievements TEXT,
        challenges TEXT,
        status ENUM('submitted', 'graded') DEFAULT 'submitted',
        grade DECIMAL(5,2),
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE,
        UNIQUE KEY unique_week (assignment_id, week_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "final_reports" =&gt; "CREATE TABLE IF NOT EXISTS final_reports (
        report_id INT AUTO_INCREMENT PRIMARY KEY,
        assignment_id INT NOT NULL,
        executive_summary TEXT,
        technical_work TEXT,
        learning_outcomes TEXT,
        recommendations TEXT,
        status ENUM('submitted', 'graded') DEFAULT 'submitted',
        grade DECIMAL(5,2),
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "company_evaluations" =&gt; "CREATE TABLE IF NOT EXISTS company_evaluations (
        evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
        assignment_id INT NOT NULL,
        technical_skills INT,
        communication_skills INT,
        teamwork INT,
        initiative INT,
        professionalism INT,
        overall_performance INT,
        comments TEXT,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "student_total_marks" =&gt; "CREATE TABLE IF NOT EXISTS student_total_marks (
        mark_id INT AUTO_INCREMENT PRIMARY KEY,
        assignment_id INT UNIQUE NOT NULL,
        stage1_mark DECIMAL(5,2) DEFAULT 0,
        weekly_followups_mark DECIMAL(5,2) DEFAULT 0,
        final_report_mark DECIMAL(5,2) DEFAULT 0,
        company_evaluation_mark DECIMAL(5,2) DEFAULT 0,
        total_mark DECIMAL(5,2) DEFAULT 0,
        final_grade VARCHAR(5),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (assignment_id) REFERENCES training_assignments(assignment_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

foreach ($tables as $table_name =&gt; $sql) {
    if ($conn-&gt;query($sql)) {
        echo "&lt;p class='success'&gt;✓ Table '$table_name' created&lt;/p&gt;";
    } else {
        echo "&lt;p class='error'&gt;❌ Failed to create '$table_name': " . $conn-&gt;error . "&lt;/p&gt;";
    }
}

// Step 5: Create sample admin user
echo "&lt;div class='step'&gt;&lt;strong&gt;Step 4:&lt;/strong&gt; Creating sample users...&lt;/div&gt;";

// Check if users already exist
$check = $conn-&gt;query("SELECT COUNT(*) as count FROM users");
$row = $check-&gt;fetch_assoc();

if ($row['count'] == 0) {
    // Create sample users
    $password = password_hash('password123', PASSWORD_DEFAULT);
    
    $sample_users = [
        "INSERT INTO users (email, password, full_name, user_type) VALUES 
         ('omar.mahmoud@uop.edu.jo', '$password', 'Omar Al-Mahmoud', 'student')",
        
        "INSERT INTO users (email, password, full_name, user_type) VALUES 
         ('mohammed.hashimi@uop.edu.jo', '$password', 'Dr. Mohammed Al-Hashimi', 'supervisor')",
        
        "INSERT INTO users (email, password, full_name, user_type) VALUES 
         ('layla.ahmad@uop.edu.jo', '$password', 'Dr. Layla Ahmad', 'coordinator')"
    ];
    
    foreach ($sample_users as $sql) {
        $conn-&gt;query($sql);
    }
    
    // Create student record
    $conn-&gt;query("INSERT INTO students (user_id, student_number, major, gpa, phone) 
                   VALUES (1, '202011400', 'Computer Science', 3.50, '0791234567')");
    
    // Create supervisor record
    $conn-&gt;query("INSERT INTO academic_supervisors (user_id, department, office_location, phone) 
                   VALUES (2, 'Computer Science', 'Building A, Room 301', '065715546')");
    
    // Create sample company
    $conn-&gt;query("INSERT INTO companies (company_name, industry, address, phone, email) 
                   VALUES ('Orange Jordan', 'Telecommunications', 'Amman, Jordan', '0799999999', 'info@orange.jo')");
    
    echo "&lt;p class='success'&gt;✓ Sample users created&lt;/p&gt;";
    echo "&lt;div class='info'&gt;
        &lt;h3&gt;Sample Login Credentials:&lt;/h3&gt;
        &lt;p&gt;&lt;strong&gt;Student:&lt;/strong&gt;&lt;br&gt;
        Email: omar.mahmoud@uop.edu.jo&lt;br&gt;
        Password: password123&lt;/p&gt;
        
        &lt;p&gt;&lt;strong&gt;Supervisor:&lt;/strong&gt;&lt;br&gt;
        Email: mohammed.hashimi@uop.edu.jo&lt;br&gt;
        Password: password123&lt;/p&gt;
        
        &lt;p&gt;&lt;strong&gt;Coordinator:&lt;/strong&gt;&lt;br&gt;
        Email: layla.ahmad@uop.edu.jo&lt;br&gt;
        Password: password123&lt;/p&gt;
    &lt;/div&gt;";
} else {
    echo "&lt;p class='info'&gt;ℹ Users already exist, skipping sample data&lt;/p&gt;";
}

$conn-&gt;close();

echo "&lt;hr&gt;
    &lt;h2 class='success'&gt;✓ Installation Complete!&lt;/h2&gt;
    &lt;p&gt;Your database is now ready to use.&lt;/p&gt;
    &lt;p&gt;&lt;a href='index.php' style='display:inline-block; padding:10px 20px; background:#9d1e26; color:white; text-decoration:none; border-radius:5px;'&gt;Go to Login Page&lt;/a&gt;&lt;/p&gt;
    &lt;p style='color:#999; font-size:12px; margin-top:30px;'&gt;You can delete this file (install_database.php) after installation.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;";
?&gt;
