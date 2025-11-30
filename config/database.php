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
// Database name used by the current application (updated to the new DB)
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
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Set charset
            $conn->set_charset(DB_CHARSET);
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
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


