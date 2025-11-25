<?php
/**
 * Header Component
 * Field Training Management System
 */

require_once __DIR__ . '/../config/config.php';
requireLogin();

$current_user_name = getCurrentUserName();
$current_user_type = getCurrentUserType();

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <h1><?php echo UNIVERSITY_NAME; ?></h1>
                <span><?php echo FACULTY_NAME; ?></span>
            </div>
            <nav class="main-nav">
                <ul>
                    <li>
                        <a href="<?php echo getDashboardURL(); ?>">
                            <span class="icon">🏠</span> Dashboard
                        </a>
                    </li>
                    <?php if ($current_user_type === 'student'): ?>
                        <li><a href="<?php echo BASE_URL; ?>/student/companies.php">Companies</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/student/reports.php">My Reports</a></li>
                    <?php elseif ($current_user_type === 'supervisor'): ?>
                        <li><a href="<?php echo BASE_URL; ?>/supervisor/students.php">My Students</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/supervisor/grading.php">Grading</a></li>
                    <?php elseif ($current_user_type === 'coordinator'): ?>
                        <li><a href="<?php echo BASE_URL; ?>/coordinator/companies.php">Companies</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/coordinator/reports.php">Reports</a></li>
                    <?php elseif ($current_user_type === 'dean'): ?>
                        <li><a href="<?php echo BASE_URL; ?>/dean/letters.php">Request Letters</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="user-menu">
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($current_user_name); ?></span>
                    <span class="user-role"><?php echo ucfirst($current_user_type); ?></span>
                </div>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-sm btn-secondary">
                    Logout
                </a>
            </div>
        </div>
    </header>
    
    <?php 
    // Display flash messages
    $flash = getFlashMessage();
    if ($flash): 
    ?>
        <div class="flash-messages">
            <div class="alert alert-<?php echo $flash['type']; ?>">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
            </div>
        </div>
    <?php endif; ?>
    
    <main class="main-content">

