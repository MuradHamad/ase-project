<?php
/**
 * Database Configuration and Connection
 * Field Training Management System
 * University of Petra, Faculty of Information Technology
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'field_training');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * @return mysqli Database connection object
 */
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            // First try to connect without selecting database
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
            
            if ($conn->connect_error) {
                throw new Exception("MySQL Connection failed: " . $conn->connect_error . ". Please make sure XAMPP MySQL is running.");
            }
            
            // Check if database exists
            $db_check = $conn->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
            if ($db_check->num_rows == 0) {
                throw new Exception("Database '" . DB_NAME . "' does not exist. Please run setup_check.php to create it.");
            }
            
            // Select the database
            if (!$conn->select_db(DB_NAME)) {
                throw new Exception("Failed to select database '" . DB_NAME . "': " . $conn->error);
            }
            
            // Set charset
            $conn->set_charset(DB_CHARSET);
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("<div style='padding:20px;background:#fee;border:1px solid #c00;color:#c00;margin:20px;border-radius:5px'>" .
                "<h3>Database Connection Error</h3>" .
                "<p>" . htmlspecialchars($e->getMessage()) . "</p>" .
                "<p><a href='" . (defined('BASE_URL') ? BASE_URL : '') . "/setup_check.php'>Run Database Setup Check</a></p>" .
                "</div>");
        }
    }
    
    return $conn;
}

/**
 * Close database connection
 */
function closeDBConnection($conn) {
    if ($conn && !$conn->connect_error) {
        $conn->close();
    }
}

/**
 * Execute prepared statement
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return mysqli_stmt|false Prepared statement or false on error
 */
function executeQuery($sql, $params = []) {
    $conn = getDBConnection();
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    if (!empty($params)) {
        $types = '';
        $values = [];
        
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $param;
        }
        
        $stmt->bind_param($types, ...$values);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    return $stmt;
}

/**
 * Fetch all rows from query
 * @param string $sql SQL query
 * @param array $params Parameters to bind
 * @return array|false Array of rows or false on error
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    
    if (!$stmt) {
        return false;
    }
    
    $result = $stmt->get_result();
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    return $rows;
}

/**
 * Fetch single row from query
 * @param string $sql SQL query
 * @param array $params Parameters to bind
 * @return array|false Single row or false on error
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    
    if (!$stmt) {
        return false;
    }
    
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $stmt->close();
    return $row ? $row : false;
}

/**
 * Execute INSERT, UPDATE, DELETE query
 * @param string $sql SQL query
 * @param array $params Parameters to bind
 * @return int|false Number of affected rows or false on error
 */
function executeUpdate($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    
    if (!$stmt) {
        return false;
    }
    
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    
    return $affected_rows;
}

/**
 * Get last insert ID
 * @return int Last insert ID
 */
function getLastInsertId() {
    $conn = getDBConnection();
    return $conn->insert_id;
}

?>


