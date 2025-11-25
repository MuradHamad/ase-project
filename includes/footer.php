<?php
/**
 * Footer Component
 * Field Training Management System
 */

?>
    </main>
    
    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; <?php echo date('Y'); ?> <?php echo UNIVERSITY_NAME; ?> - <?php echo FACULTY_NAME; ?></p>
            <p>Field Training Management System</p>
        </div>
    </footer>
    
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

