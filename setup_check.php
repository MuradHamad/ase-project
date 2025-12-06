&lt;?php
/**
 * Database Setup Checker
 * Run this file to verify database connection and setup
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'field_training_db');

echo "&lt;h2&gt;Database Setup Checker&lt;/h2&gt;";

// Step 1: Check if we can connect to MySQL
echo "&lt;p&gt;1. Checking MySQL connection...&lt;/p&gt;";
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn-&gt;connect_error) {
    die("&lt;p style='color:red'&gt;❌ Failed to connect to MySQL: " . $conn-&gt;connect_error . "&lt;/p&gt;&lt;p&gt;Please make sure XAMPP MySQL is running.&lt;/p&gt;");
}

echo "&lt;p style='color:green'&gt;✓ MySQL connection successful&lt;/p&gt;";

// Step 2: Check if database exists
echo "&lt;p&gt;2. Checking if database exists...&lt;/p&gt;";
$result = $conn-&gt;query("SHOW DATABASES LIKE '" . DB_NAME . "'");

if ($result-&gt;num_rows == 0) {
    echo "&lt;p style='color:orange'&gt;⚠ Database '" . DB_NAME . "' does not exist.&lt;/p&gt;";
    echo "&lt;p&gt;Creating database...&lt;/p&gt;";
    
    if ($conn-&gt;query("CREATE DATABASE " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        echo "&lt;p style='color:green'&gt;✓ Database created successfully&lt;/p&gt;";
    } else {
        die("&lt;p style='color:red'&gt;❌ Failed to create database: " . $conn-&gt;error . "&lt;/p&gt;");
    }
} else {
    echo "&lt;p style='color:green'&gt;✓ Database exists&lt;/p&gt;";
}

// Step 3: Connect to the database
echo "&lt;p&gt;3. Connecting to database...&lt;/p&gt;";
$conn-&gt;select_db(DB_NAME);
echo "&lt;p style='color:green'&gt;✓ Connected to database&lt;/p&gt;";

// Step 4: Check if tables exist
echo "&lt;p&gt;4. Checking tables...&lt;/p&gt;";
$tables = ['users', 'students', 'companies', 'training_assignments'];
$missing_tables = [];

foreach ($tables as $table) {
    $result = $conn-&gt;query("SHOW TABLES LIKE '$table'");
    if ($result-&gt;num_rows == 0) {
        $missing_tables[] = $table;
    }
}

if (empty($missing_tables)) {
    echo "&lt;p style='color:green'&gt;✓ All required tables exist&lt;/p&gt;";
} else {
    echo "&lt;p style='color:orange'&gt;⚠ Missing tables: " . implode(', ', $missing_tables) . "&lt;/p&gt;";
    echo "&lt;p&gt;You need to run the database setup script located in /database/schema.sql&lt;/p&gt;";
}

echo "&lt;hr&gt;";
echo "&lt;h3&gt;Summary&lt;/h3&gt;";
echo "&lt;p&gt;Database Host: " . DB_HOST . "&lt;/p&gt;";
echo "&lt;p&gt;Database Name: " . DB_NAME . "&lt;/p&gt;";
echo "&lt;p&gt;Database User: " . DB_USER . "&lt;/p&gt;";

if (empty($missing_tables)) {
    echo "&lt;p style='color:green; font-weight:bold'&gt;✓ Database is ready to use!&lt;/p&gt;";
    echo "&lt;p&gt;&lt;a href='index.php'&gt;Go to Login Page&lt;/a&gt;&lt;/p&gt;";
} else {
    echo "&lt;p style='color:orange; font-weight:bold'&gt;⚠ Database setup is incomplete&lt;/p&gt;";
    echo "&lt;p&gt;Please import the database schema from /database/schema.sql&lt;/p&gt;";
}

$conn-&gt;close();
?&gt;
