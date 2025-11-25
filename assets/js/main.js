/**
 * Main JavaScript File
 * Field Training Management System
 */

(function() {
    'use strict';

    // ===================================
    // Utility Functions
    // ===================================

    /**
     * Show alert message
     */
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            ${message}
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        `;
        
        const container = document.querySelector('.main-content') || document.body;
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    /**
     * Form validation helper
     */
    function validateForm(form) {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('error');
                
                // Show error message
                let errorDiv = input.parentElement.querySelector('.error-text');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'error-text';
                    input.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = 'This field is required';
            } else {
                input.classList.remove('error');
                const errorDiv = input.parentElement.querySelector('.error-text');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
        
        return isValid;
    }

    /**
     * Clear form errors
     */
    function clearFormErrors(form) {
        const errorInputs = form.querySelectorAll('.error');
        errorInputs.forEach(input => input.classList.remove('error'));
        
        const errorTexts = form.querySelectorAll('.error-text');
        errorTexts.forEach(text => text.remove());
    }

    /**
     * Format date for input type="date"
     */
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toISOString().split('T')[0];
    }

    /**
     * Get URL parameters
     */
    function getUrlParams() {
        const params = {};
        const urlParams = new URLSearchParams(window.location.search);
        for (const [key, value] of urlParams) {
            params[key] = value;
        }
        return params;
    }

    /**
     * Confirm action
     */
    function confirmAction(message) {
        return confirm(message || 'Are you sure you want to proceed?');
    }

    // ===================================
    // Auto-dismiss flash messages
    // ===================================
    document.addEventListener('DOMContentLoaded', function() {
        const flashMessages = document.querySelectorAll('.flash-messages .alert');
        flashMessages.forEach(alert => {
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, 5000);
        });
    });

    // ===================================
    // Form submission handlers
    // ===================================
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(form)) {
                    e.preventDefault();
                    showAlert('Please fill in all required fields', 'error');
                    return false;
                }
            });
        });
    });

    // ===================================
    // Dynamic form fields (add/remove rows)
    // ===================================
    window.addTableRow = function(button, tableId, rowTemplate) {
        const table = document.querySelector(`#${tableId} tbody`);
        if (!table) return;
        
        const newRow = document.createElement('tr');
        newRow.innerHTML = rowTemplate;
        table.appendChild(newRow);
    };

    window.removeTableRow = function(button) {
        if (confirmAction('Are you sure you want to remove this row?')) {
            button.closest('tr').remove();
        }
    };

    // ===================================
    // Date validation
    // ===================================
    document.addEventListener('DOMContentLoaded', function() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach(input => {
            input.addEventListener('change', function() {
                const date = new Date(this.value);
                if (isNaN(date.getTime())) {
                    this.setCustomValidity('Please enter a valid date');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    });

    // ===================================
    // Auto-save draft functionality
    // ===================================
    let autoSaveTimer;
    
    window.enableAutoSave = function(formId, saveUrl) {
        const form = document.getElementById(formId);
        if (!form) return;
        
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Auto-save logic can be implemented here
                    console.log('Auto-saving draft...');
                }, 2000);
            });
        });
    };

    // ===================================
    // Export functions to global scope
    // ===================================
    window.FTMS = {
        showAlert: showAlert,
        validateForm: validateForm,
        clearFormErrors: clearFormErrors,
        formatDateForInput: formatDateForInput,
        getUrlParams: getUrlParams,
        confirmAction: confirmAction
    };

})();

