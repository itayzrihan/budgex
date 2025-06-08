/**
 * Budgex Public JavaScript
 * Handles all client-side functionality for the public-facing pages
 */

(function($) {
    'use strict';

    // Global variables
    let currentBudgetId = null;
    let isLoading = false;

    // Initialize when document is ready
    $(document).ready(function() {
        initializeBudgex();
    });

    /**
     * Initialize all Budgex functionality
     */
    function initializeBudgex() {
        bindEvents();
        initializeCurrentPage();
        loadDashboardStats();
    }

    /**
     * Bind all event handlers
     */
    function bindEvents() {
        // Outcome form submission
        $(document).on('submit', '#outcome-form', handleOutcomeSubmission);
        
        // Edit outcome form submission
        $(document).on('submit', '#edit-outcome-form', handleEditOutcomeSubmission);
        
        // Delete outcome buttons
        $(document).on('click', '.delete-outcome', handleDeleteOutcome);
        
        // Edit outcome buttons
        $(document).on('click', '.edit-outcome', handleEditOutcome);
        
        // Cancel edit buttons
        $(document).on('click', '.cancel-edit', handleCancelEdit);
        
        // Budget navigation
        $(document).on('click', '.budget-card a, .view-budget', handleBudgetNavigation);
        
        // Advanced management panel toggle
        $(document).on('click', '#toggle-management-panel', handleAdvancedManagementToggle);
        
        // Invitation acceptance
        $(document).on('click', '.accept-invitation', handleInvitationAcceptance);
        
        // Form validation
        $(document).on('input', '.form-input[required]', validateField);
        
        // Modal handling
        $(document).on('click', '[data-modal]', handleModalOpen);
        $(document).on('click', '.modal-close, .modal-backdrop', handleModalClose);
        
        // Quick actions
        $(document).on('click', '.quick-action', handleQuickAction);
    }

    /**
     * Initialize page-specific functionality
     */
    function initializeCurrentPage() {
        const page = getPageType();
        
        switch(page) {
            case 'dashboard':
                initializeDashboard();
                break;
            case 'budget':
                initializeBudgetPage();
                break;
            case 'invite':
                initializeInvitePage();
                break;
        }
    }

    /**
     * Get current page type
     */
    function getPageType() {
        if ($('.budgex-dashboard').length) return 'dashboard';
        if ($('.budget-page').length) return 'budget';
        if ($('.invite-page').length) return 'invite';
        return 'other';
    }

    /**
     * Initialize dashboard functionality
     */
    function initializeDashboard() {
        loadBudgetsList();
        updateStatistics();
    }

    /**
     * Initialize budget page functionality
     */
    function initializeBudgetPage() {
        currentBudgetId = getBudgetIdFromURL();
        if (currentBudgetId) {
            loadBudgetDetails(currentBudgetId);
            loadOutcomes(currentBudgetId);
            loadMonthlyBreakdown(currentBudgetId);
            
            // Handle anchor link navigation for advanced management panel
            handleAdvancedManagementAnchor();
        }
    }

    /**
     * Initialize invite page functionality
     */
    function initializeInvitePage() {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        if (token) {
            displayInvitationDetails(token);
        }
    }

    /**
     * Handle outcome form submission
     */
    function handleOutcomeSubmission(e) {
        e.preventDefault();
        
        if (isLoading) return;
        
        const form = $(this);
        const formData = new FormData(form[0]);
        formData.append('action', 'budgex_add_outcome');
        formData.append('budget_id', currentBudgetId);
        formData.append('nonce', budgex_ajax.nonce);

        if (!validateOutcomeForm(form)) {
            return;
        }

        setLoading(true);
        showFormLoading(form);

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('התוצאה נוספה בהצלחה', 'success');
                    form[0].reset();
                    loadOutcomes(currentBudgetId);
                    updateBudgetSummary(currentBudgetId);
                    updateStatistics();
                } else {
                    showNotification(response.data || 'שגיאה בהוספת התוצאה', 'error');
                }
            },
            error: function() {
                showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                setLoading(false);
                hideFormLoading(form);
            }
        });
    }

    /**
     * Handle edit outcome form submission
     */
    function handleEditOutcomeSubmission(e) {
        e.preventDefault();
        
        const form = $(this);
        const outcomeId = form.find('input[name="outcome_id"]').val();
        const formData = new FormData(form[0]);
        formData.append('action', 'budgex_edit_outcome');
        formData.append('nonce', budgex_ajax.nonce);

        if (!validateOutcomeForm(form)) {
            return;
        }

        setLoading(true);

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showNotification('התוצאה עודכנה בהצלחה', 'success');
                    loadOutcomes(currentBudgetId);
                    updateBudgetSummary(currentBudgetId);
                    updateStatistics();
                } else {
                    showNotification(response.data || 'שגיאה בעדכון התוצאה', 'error');
                }
            },
            error: function() {
                showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                setLoading(false);
            }
        });
    }

    /**
     * Handle delete outcome
     */
    function handleDeleteOutcome(e) {
        e.preventDefault();
        
        if (!confirm('האם אתה בטוח שברצונך למחוק תוצאה זו?')) {
            return;
        }
        
        const button = $(this);
        const outcomeId = button.data('outcome-id');
        
        setLoading(true);
        button.prop('disabled', true);

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_delete_outcome',
                outcome_id: outcomeId,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('התוצאה נמחקה בהצלחה', 'success');
                    loadOutcomes(currentBudgetId);
                    updateBudgetSummary(currentBudgetId);
                    updateStatistics();
                } else {
                    showNotification(response.data || 'שגיאה במחיקת התוצאה', 'error');
                }
            },
            error: function() {
                showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                setLoading(false);
                button.prop('disabled', false);
            }
        });
    }

    /**
     * Handle edit outcome button click
     */
    function handleEditOutcome(e) {
        e.preventDefault();
        
        const button = $(this);
        const outcomeId = button.data('outcome-id');
        const outcomeItem = button.closest('.outcome-item');
        
        // Show edit form for this outcome
        loadEditForm(outcomeId, outcomeItem);
    }

    /**
     * Handle cancel edit
     */
    function handleCancelEdit(e) {
        e.preventDefault();
        loadOutcomes(currentBudgetId);
    }

    /**
     * Handle invitation acceptance
     */
    function handleInvitationAcceptance(e) {
        e.preventDefault();
        
        const button = $(this);
        const token = button.data('token');
        
        setLoading(true);
        button.prop('disabled', true);

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_accept_invitation',
                token: token,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotification('ההזמנה התקבלה בהצלחה', 'success');
                    // Redirect to budget page
                    if (response.data && response.data.budget_id) {
                        window.location.href = budgex_ajax.budget_url + '?id=' + response.data.budget_id;
                    } else {
                        window.location.href = budgex_ajax.dashboard_url;
                    }
                } else {
                    showNotification(response.data || 'שגיאה בקבלת ההזמנה', 'error');
                }
            },
            error: function() {
                showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                setLoading(false);
                button.prop('disabled', false);
            }
        });
    }

    /**
     * Handle advanced management panel toggle
     */
    function handleAdvancedManagementToggle(e) {
        e.preventDefault();
        
        const panel = $('#advanced-management-panel');
        const button = $(this);
        
        if (panel.is(':visible')) {
            // Hide panel with animation
            panel.slideUp(300, function() {
                button.find('span').removeClass('dashicons-admin-settings').addClass('dashicons-admin-settings');
                button.removeClass('active');
            });
        } else {
            // Show panel with animation
            panel.slideDown(300, function() {
                button.find('span').removeClass('dashicons-admin-settings').addClass('dashicons-admin-settings');
                button.addClass('active');
                
                // Scroll to panel if needed
                $('html, body').animate({
                    scrollTop: panel.offset().top - 100
                }, 500);
            });
        }
    }

    /**
     * Handle anchor link navigation for advanced management panel
     */
    function handleAdvancedManagementAnchor() {
        // Check if URL contains advanced management panel anchor
        if (window.location.hash === '#advanced-management-panel') {
            // Wait for page to load completely
            setTimeout(function() {
                const panel = $('#advanced-management-panel');
                const button = $('#toggle-management-panel');
                
                if (panel.length && button.length) {
                    // Show the panel
                    panel.slideDown(300, function() {
                        button.addClass('active');
                        
                        // Scroll to panel
                        $('html, body').animate({
                            scrollTop: panel.offset().top - 100
                        }, 500);
                    });
                }
            }, 500);
        }
    }

    /**
     * Load dashboard statistics
     */
    function loadDashboardStats() {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_dashboard_stats',
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    updateDashboardStats(response.data);
                }
            }
        });
    }

    /**
     * Load budgets list for dashboard
     */
    function loadBudgetsList() {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_user_budgets',
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    updateBudgetsList(response.data);
                }
            }
        });
    }

    /**
     * Load outcomes for budget page
     */
    function loadOutcomes(budgetId) {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_outcomes',
                budget_id: budgetId,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    updateOutcomesList(response.data);
                }
            }
        });
    }

    /**
     * Load monthly breakdown
     */
    function loadMonthlyBreakdown(budgetId) {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_monthly_breakdown',
                budget_id: budgetId,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    updateMonthlyBreakdown(response.data);
                }
            }
        });
    }

    /**
     * Update dashboard statistics
     */
    function updateDashboardStats(stats) {
        $('.total-budgets .stat-value').text(stats.total_budgets || 0);
        $('.total-spent .stat-value').text(formatCurrency(stats.total_spent || 0));
        $('.total-remaining .stat-value').text(formatCurrency(stats.total_remaining || 0));
        $('.this-month-spent .stat-value').text(formatCurrency(stats.this_month_spent || 0));
    }

    /**
     * Update budgets list
     */
    function updateBudgetsList(budgets) {
        const container = $('.budgets-grid');
        if (!container.length) return;
        
        container.empty();
        
        budgets.forEach(function(budget) {
            const budgetCard = createBudgetCard(budget);
            container.append(budgetCard);
        });
    }

    /**
     * Update outcomes list
     */
    function updateOutcomesList(outcomes) {
        const container = $('.outcomes-list');
        if (!container.length) return;
        
        container.empty();
        
        if (outcomes.length === 0) {
            container.append('<p class="text-center text-muted">אין תוצאות להצגה</p>');
            return;
        }
        
        outcomes.forEach(function(outcome) {
            const outcomeItem = createOutcomeItem(outcome);
            container.append(outcomeItem);
        });
    }

    /**
     * Create budget card HTML
     */
    function createBudgetCard(budget) {
        const progressPercent = budget.total_budget > 0 ? (budget.total_spent / budget.total_budget) * 100 : 0;
        
        return `
            <div class="budget-card">
                <div class="budget-card-header">
                    <h3 class="budget-card-title">${escapeHtml(budget.name)}</h3>
                    <span class="budget-role">${budget.role === 'admin' ? 'מנהל' : 'צופה'}</span>
                </div>
                <div class="budget-stats">
                    <div class="budget-stat">
                        <div class="budget-stat-value">${formatCurrency(budget.monthly_budget)}</div>
                        <div class="budget-stat-label">תקציב חודשי</div>
                    </div>
                    <div class="budget-stat">
                        <div class="budget-stat-value">${formatCurrency(budget.total_spent)}</div>
                        <div class="budget-stat-label">הוצאות</div>
                    </div>
                </div>
                <div class="budget-progress">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${Math.min(progressPercent, 100)}%"></div>
                    </div>
                    <div class="progress-label">
                        <span>${progressPercent.toFixed(1)}%</span>
                        <span>${formatCurrency(budget.total_budget - budget.total_spent)} נותר</span>
                    </div>
                </div>
                <div class="budget-actions">
                    <a href="${budgex_ajax.budget_url}?id=${budget.id}" class="btn btn-primary btn-sm">צפה בתקציב</a>
                </div>
            </div>
        `;
    }

    /**
     * Create outcome item HTML
     */
    function createOutcomeItem(outcome) {
        const canEdit = outcome.can_edit;
        
        return `
            <div class="outcome-item" data-outcome-id="${outcome.id}">
                <div class="outcome-details">
                    <div class="outcome-description">${escapeHtml(outcome.description)}</div>
                    <div class="outcome-meta">${outcome.date} • ${outcome.category || 'ללא קטגוריה'}</div>
                </div>
                ${canEdit ? `
                <div class="outcome-actions">
                    <button type="button" class="btn btn-sm btn-secondary edit-outcome" data-outcome-id="${outcome.id}">
                        ✏️ עריכה
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-outcome" data-outcome-id="${outcome.id}">
                        🗑️ מחק
                    </button>
                </div>
                ` : ''}
                <div class="outcome-amount">${formatCurrency(outcome.amount)}</div>
            </div>
        `;
    }

    /**
     * Utility functions
     */
    function formatCurrency(amount) {
        return new Intl.NumberFormat('he-IL', {
            style: 'currency',
            currency: 'ILS',
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function getBudgetIdFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id');
    }

    function validateOutcomeForm(form) {
        let isValid = true;
        
        form.find('.form-input[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('error');
                isValid = false;
            } else {
                $(this).removeClass('error');
            }
        });
        
        return isValid;
    }

    function validateField() {
        const field = $(this);
        if (field.val().trim()) {
            field.removeClass('error');
        }
    }

    function setLoading(loading) {
        isLoading = loading;
        if (loading) {
            $('body').addClass('loading');
        } else {
            $('body').removeClass('loading');
        }
    }

    function showFormLoading(form) {
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        if (!submitBtn.find('.spinner').length) {
            submitBtn.prepend('<span class="spinner"></span> ');
        }
    }

    function hideFormLoading(form) {
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', false);
        submitBtn.find('.spinner').remove();
    }

    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = $(`
            <div class="budgex-notification budgex-notification-${type}">
                <div class="notification-content">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            </div>
        `);
        
        // Add to page
        $('body').append(notification);
        
        // Show with animation
        setTimeout(() => notification.addClass('show'), 100);
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            notification.removeClass('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        // Handle close button
        notification.find('.notification-close').on('click', function() {
            notification.removeClass('show');
            setTimeout(() => notification.remove(), 300);
        });
    }

    /**
     * Additional utility functions for specific features
     */
    function updateBudgetSummary(budgetId) {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_budget_summary',
                budget_id: budgetId,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    const summary = response.data;
                    $('.summary-card .total-budget .summary-value').text(formatCurrency(summary.total_budget));
                    $('.summary-card .total-spent .summary-value').text(formatCurrency(summary.total_spent));
                    $('.summary-card .remaining-budget .summary-value').text(formatCurrency(summary.remaining_budget));
                    $('.summary-card .this-month-spent .summary-value').text(formatCurrency(summary.this_month_spent));
                }
            }
        });
    }

    function updateStatistics() {
        if (getPageType() === 'dashboard') {
            loadDashboardStats();
        }
    }

    function loadEditForm(outcomeId, outcomeItem) {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_outcome_edit_form',
                outcome_id: outcomeId,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    outcomeItem.replaceWith(response.data);
                }
            }
        });
    }

    function handleQuickAction(e) {
        e.preventDefault();
        const action = $(this).data('action');
        
        switch(action) {
            case 'add-outcome':
                $('#outcome-form .form-input[name="description"]').focus();
                break;
            case 'view-analytics':
                // Could implement analytics modal or page
                break;
        }
    }

    function handleModalOpen(e) {
        e.preventDefault();
        const modalId = $(this).data('modal');
        const modal = $('#' + modalId);
        if (modal.length) {
            modal.addClass('show');
            $('body').addClass('modal-open');
        }
    }

    function handleModalClose(e) {
        e.preventDefault();
        $('.modal').removeClass('show');
        $('body').removeClass('modal-open');
    }

    function handleBudgetNavigation(e) {
        // Allow normal navigation, but could add loading states here
        const link = $(this);
        link.addClass('loading');
    }

    function updateMonthlyBreakdown(breakdown) {
        const container = $('.monthly-breakdown tbody');
        if (!container.length) return;
        
        container.empty();
        
        breakdown.forEach(function(month) {
            const row = `
                <tr class="month-item">
                    <td class="month-name">${month.month_name}</td>
                    <td class="month-budget">${formatCurrency(month.budget)}</td>
                    <td class="month-spent">${formatCurrency(month.spent)}</td>
                    <td class="month-remaining ${month.remaining >= 0 ? 'positive' : 'negative'}">
                        ${formatCurrency(month.remaining)}
                    </td>
                </tr>
            `;
            container.append(row);
        });
    }

    function displayInvitationDetails(token) {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_invitation_details',
                token: token,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    const details = response.data;
                    $('.invitation-details .budget-name').text(details.budget_name);
                    $('.invitation-details .inviter-name').text(details.inviter_name);
                    $('.invitation-details .role').text(details.role === 'admin' ? 'מנהל' : 'צופה');
                    $('.accept-invitation').data('token', token);
                }
            }
        });
    }

    // Expose some functions globally for debugging
    window.BudgexPublic = {
        loadOutcomes: loadOutcomes,
        updateBudgetSummary: updateBudgetSummary,
        formatCurrency: formatCurrency,
        showNotification: showNotification
    };

})(jQuery);