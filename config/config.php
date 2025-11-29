<?php
/**
 * Application Configuration
 * Field Training Management System
 * University of Petra, Faculty of Information Technology
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Amman');

// Base URL (adjust if needed)
define('BASE_URL', 'http://localhost/ase-project');

// Site Configuration
define('SITE_NAME', 'Field Training Management System');
define('UNIVERSITY_NAME', 'University of Petra');
define('FACULTY_NAME', 'Faculty of Information Technology');

// Training Configuration
define('TRAINING_HOURS', 160);
define('TRAINING_DAYS', 20);
define('WEEKS_IN_TRAINING', 4);

// Grade Scale
define('GRADE_SCALE', [
    'A+' => ['min' => 95, 'max' => 100],
    'A'  => ['min' => 90, 'max' => 94],
    'B+' => ['min' => 85, 'max' => 89],
    'B'  => ['min' => 80, 'max' => 84],
    'C+' => ['min' => 75, 'max' => 79],
    'C'  => ['min' => 70, 'max' => 74],
    'D'  => ['min' => 60, 'max' => 69],
    'F'  => ['min' => 0,  'max' => 59]
]);

// File paths
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Create uploads directory if it doesn't exist
if (!file_exists(UPLOADS_PATH)) {
    mkdir(UPLOADS_PATH, 0777, true);
}

// Include database configuration
require_once ROOT_PATH . '/config/database.php';

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

/**
 * Require user to be logged in
 * Redirects to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit();
    }
}

/**
 * Check if user has specific role
 * @param array|string $allowed_types User types allowed
 * @return bool
 */
function hasRole($allowed_types) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_type = $_SESSION['user_type'];
    
    if (is_array($allowed_types)) {
        return in_array($user_type, $allowed_types);
    }
    
    return $user_type === $allowed_types;
}

/**
 * Require user to have specific role
 * Redirects to dashboard if user doesn't have required role
 * @param array|string $allowed_types User types allowed
 */
function requireRole($allowed_types) {
    requireLogin();
    
    if (!hasRole($allowed_types)) {
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    }
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user type
 * @return string|null
 */
function getCurrentUserType() {
    return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
}

/**
 * Get current user full name
 * @return string|null
 */
function getCurrentUserName() {
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : null;
}

/**
 * Set flash message
 * @param string $message Message text
 * @param string $type Message type (success, error, warning, info)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Get and clear flash message
 * @return array|null Array with 'message' and 'type' or null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type'] ?? 'info'
        ];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return $message;
    }
    return null;
}

/**
 * Sanitize input data
 * @param mixed $data Input data
 * @return mixed Sanitized data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 * @param string $email Email address
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Format date for display
 * @param string $date Date string
 * @param string $format Date format
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d') {
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    return date($format, strtotime($date));
}

/**
 * Format date for input type="date"
 * @param string $date Date string
 * @return string Formatted date (Y-m-d)
 */
function formatDateForInput($date) {
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    return date('Y-m-d', strtotime($date));
}

/**
 * Calculate grade from total mark
 * @param float $total_mark Total mark
 * @return string Letter grade
 */
function calculateGrade($total_mark) {
    foreach (GRADE_SCALE as $grade => $range) {
        if ($total_mark >= $range['min'] && $total_mark <= $range['max']) {
            return $grade;
        }
    }
    return 'F';
}

?>

