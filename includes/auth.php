<?php
/**
 * Authentication Functions
 * Field Training Management System
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Login user
 * @param string $username Username
 * @param string $password Password
 * @return array Result array with 'success' and 'message'
 */
function loginUser($username, $password) {
    $conn = getDBConnection();
    
    // Get user by username
    $sql = "SELECT u.*, 
            s.student_id, s.student_number, s.major,
            ac.supervisor_id as academic_supervisor_id, ac.employee_number, ac.department
            FROM users u
            LEFT JOIN students s ON u.user_id = s.student_id
            LEFT JOIN academic_supervisors ac ON u.user_id = ac.supervisor_id
            WHERE u.username = ? AND u.is_active = 1";
    
    $user = fetchOne($sql, [$username]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['user_type'] = $user['user_type'];
    
    // Set role-specific session data
    if ($user['user_type'] === 'student') {
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['student_number'] = $user['student_number'];
        $_SESSION['major'] = $user['major'];
    } elseif ($user['user_type'] === 'supervisor') {
        $_SESSION['supervisor_id'] = $user['academic_supervisor_id'];
        $_SESSION['employee_number'] = $user['employee_number'];
        $_SESSION['department'] = $user['department'];
    }
    
    return ['success' => true, 'message' => 'Login successful'];
}

/**
 * Logout user
 */
function logoutUser() {
    // Destroy session
    session_destroy();
    
    // Start new session to clear all data
    session_start();
    
    // Redirect to login page
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

/**
 * Check if user can access assignment
 * Students can only access their own assignments
 * Supervisors can access assignments they supervise
 * Coordinators and Dean can access all assignments
 * @param int $assignment_id Assignment ID
 * @return bool
 */
function canAccessAssignment($assignment_id) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user_type = getCurrentUserType();
    $user_id = getCurrentUserId();
    
    // Coordinators and Dean have full access
    if (in_array($user_type, ['coordinator', 'dean'])) {
        return true;
    }
    
    // Get assignment
    $sql = "SELECT student_id, academic_supervisor_id 
            FROM training_assignments 
            WHERE assignment_id = ?";
    $assignment = fetchOne($sql, [$assignment_id]);
    
    if (!$assignment) {
        return false;
    }
    
    // Students can only access their own assignments
    if ($user_type === 'student') {
        $sql = "SELECT student_id FROM students WHERE student_id = ?";
        $student = fetchOne($sql, [$user_id]);
        return $student && $assignment['student_id'] == $student['student_id'];
    }
    
    // Supervisors can access assignments they supervise
    if ($user_type === 'supervisor') {
        $sql = "SELECT supervisor_id FROM academic_supervisors WHERE supervisor_id = ?";
        $supervisor = fetchOne($sql, [$user_id]);
        return $supervisor && $assignment['academic_supervisor_id'] == $supervisor['supervisor_id'];
    }
    
    return false;
}

/**
 * Get user dashboard URL based on user type
 * @return string Dashboard URL
 */
function getDashboardURL() {
    $user_type = getCurrentUserType();
    
    switch ($user_type) {
        case 'student':
            return BASE_URL . '/student/dashboard.php';
        case 'supervisor':
            return BASE_URL . '/supervisor/dashboard.php';
        case 'coordinator':
            return BASE_URL . '/coordinator/dashboard.php';
        case 'dean':
            return BASE_URL . '/dean/dashboard.php';
        default:
            return BASE_URL . '/index.php';
    }
}

?>

