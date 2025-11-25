<?php
/**
 * Field Training Management System - Database Initialization Script
 * University of Petra, Faculty of Information Technology
 * 
 * This script initializes the database by:
 * 1. Creating the database if it doesn't exist
 * 2. Running the schema creation SQL
 * 3. Optionally loading sample data
 * 
 * Usage:
 * - Run from command line: php init_database.php
 * - Run from web browser: Access via web server
 * 
 * IMPORTANT: Remove or secure this file in production!
 */

// Database configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = ''; // Default XAMPP MySQL password (empty)
$db_name = 'field_training_db';

// Get the directory of this script
$script_dir = __DIR__;

// SQL files
$schema_file = $script_dir . '/schema.sql';
$sample_data_file = $script_dir . '/sample_data.sql';

// Colors for CLI output
$GREEN = "\033[0;32m";
$RED = "\033[0;31m";
$YELLOW = "\033[1;33m";
$NC = "\033[0m"; // No Color

// Check if running from CLI or web
$is_cli = php_sapi_name() === 'cli';

if (!$is_cli) {
    echo "<html><head><title>Database Initialization</title></head><body><pre>";
}

function print_message($message, $type = 'info') {
    global $is_cli, $GREEN, $RED, $YELLOW, $NC;
    
    if ($is_cli) {
        $color = '';
        switch ($type) {
            case 'success':
                $color = $GREEN;
                break;
            case 'error':
                $color = $RED;
                break;
            case 'warning':
                $color = $YELLOW;
                break;
        }
        echo $color . $message . $NC . "\n";
    } else {
        $style = '';
        switch ($type) {
            case 'success':
                $style = 'color: green;';
                break;
            case 'error':
                $style = 'color: red;';
                break;
            case 'warning':
                $style = 'color: orange;';
                break;
        }
        echo "<span style='$style'>$message</span>\n";
    }
}

try {
    print_message("=====================================", 'info');
    print_message("Field Training Database Initialization", 'info');
    print_message("=====================================", 'info');
    echo "\n";
    
    // Connect to MySQL server (without selecting database)
    print_message("Connecting to MySQL server...", 'info');
    $conn = new mysqli($db_host, $db_username, $db_password);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    print_message("✓ Connected to MySQL server", 'success');
    
    // Create database if it doesn't exist
    print_message("\nCreating database '$db_name'...", 'info');
    $create_db_sql = "CREATE DATABASE IF NOT EXISTS `$db_name` 
                      CHARACTER SET utf8mb4 
                      COLLATE utf8mb4_unicode_ci";
    
    if ($conn->query($create_db_sql) === TRUE) {
        print_message("✓ Database created/verified successfully", 'success');
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($db_name);
    
    // Set charset for connection
    $conn->set_charset("utf8mb4");
    
    // Read and execute schema file
    print_message("\nReading schema file...", 'info');
    if (!file_exists($schema_file)) {
        throw new Exception("Schema file not found: $schema_file");
    }
    
    $schema_sql = file_get_contents($schema_file);
    if ($schema_sql === false) {
        throw new Exception("Error reading schema file");
    }
    print_message("✓ Schema file read successfully", 'success');
    
    // Remove USE statement if present (we already selected the database)
    $schema_sql = preg_replace('/USE\s+[^;]+;/i', '', $schema_sql);
    
    print_message("\nExecuting schema SQL...", 'info');
    
    // Split SQL by semicolon and execute each statement
    // Remove comments and empty lines
    $schema_sql = preg_replace('/--.*$/m', '', $schema_sql);
    $statements = array_filter(
        array_map('trim', explode(';', $schema_sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*\/\*.*\*\/\s*$/s', $stmt);
        }
    );
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }
        
        // Skip comment blocks
        if (preg_match('/^\s*\/\*.*\*\/\s*$/s', $statement)) {
            continue;
        }
        
        if ($conn->multi_query($statement)) {
            do {
                // Store result to clear buffer
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            } while ($conn->next_result());
            $success_count++;
        } else {
            // Some errors are expected (e.g., table already exists)
            $error_msg = $conn->error;
            // Ignore "already exists" errors during re-runs
            if (strpos($error_msg, 'already exists') === false && 
                strpos($error_msg, 'Duplicate key') === false) {
                print_message("Warning: $error_msg", 'warning');
                $error_count++;
            }
        }
    }
    
    print_message("✓ Schema execution completed", 'success');
    print_message("  - Successful statements: $success_count", 'info');
    if ($error_count > 0) {
        print_message("  - Errors encountered: $error_count", 'warning');
    }
    
    // Ask about sample data (CLI only)
    $load_sample_data = false;
    if ($is_cli) {
        echo "\n";
        print_message("Do you want to load sample data? (y/n) [n]: ", 'info');
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);
        $load_sample_data = (strtolower($line) === 'y' || strtolower($line) === 'yes');
    } else {
        // For web interface, check if sample data parameter is set
        $load_sample_data = isset($_GET['load_sample']) && $_GET['load_sample'] === '1';
        if ($load_sample_data) {
            print_message("\nSample data will be loaded...", 'info');
        } else {
            print_message("\nTo load sample data, add ?load_sample=1 to the URL", 'info');
        }
    }
    
    // Load sample data if requested
    if ($load_sample_data) {
        print_message("\nReading sample data file...", 'info');
        if (!file_exists($sample_data_file)) {
            throw new Exception("Sample data file not found: $sample_data_file");
        }
        
        $sample_data_sql = file_get_contents($sample_data_file);
        if ($sample_data_sql === false) {
            throw new Exception("Error reading sample data file");
        }
        print_message("✓ Sample data file read successfully", 'success');
        
        // Remove USE statement if present
        $sample_data_sql = preg_replace('/USE\s+[^;]+;/i', '', $sample_data_sql);
        
        print_message("\nExecuting sample data SQL...", 'info');
        
        // Remove comments
        $sample_data_sql = preg_replace('/--.*$/m', '', $sample_data_sql);
        $sample_statements = array_filter(
            array_map('trim', explode(';', $sample_data_sql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^\s*\/\*.*\*\/\s*$/s', $stmt);
            }
        );
        
        $sample_success = 0;
        $sample_error = 0;
        
        foreach ($sample_statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) {
                continue;
            }
            
            if ($conn->query($statement) === TRUE) {
                $sample_success++;
            } else {
                $error_msg = $conn->error;
                // Ignore duplicate key errors (data might already exist)
                if (strpos($error_msg, 'Duplicate entry') === false) {
                    print_message("Warning: $error_msg", 'warning');
                    $sample_error++;
                }
            }
        }
        
        print_message("✓ Sample data loaded successfully", 'success');
        print_message("  - Successful inserts: $sample_success", 'info');
        if ($sample_error > 0) {
            print_message("  - Errors encountered: $sample_error", 'warning');
        }
    }
    
    // Display summary
    echo "\n";
    print_message("=====================================", 'info');
    print_message("Initialization Complete!", 'success');
    print_message("=====================================", 'info');
    echo "\n";
    print_message("Database: $db_name", 'info');
    print_message("Host: $db_host", 'info');
    print_message("Character Set: utf8mb4", 'info');
    print_message("Collation: utf8mb4_unicode_ci", 'info');
    echo "\n";
    print_message("You can now start using the Field Training Management System!", 'success');
    echo "\n";
    
    // Close connection
    $conn->close();
    
} catch (Exception $e) {
    print_message("\nERROR: " . $e->getMessage(), 'error');
    exit(1);
}

if (!$is_cli) {
    echo "</pre></body></html>";
}
?>


