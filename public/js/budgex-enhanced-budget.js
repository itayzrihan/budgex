/**
 * Enhanced Budget Management - JavaScript
 * 
 * This file handles all interactive functionality for the enhanced budget management page
 */

(function($) {
    'use strict';
    
    // Global variables
    let currentTab = 'overview';
    let chartsInitialized = false;
    let charts = {};
    
    // Initialize when document is ready
    $(document).ready(function() {
        initializeEnhancedBudgetPage();
    });
    
    /**
     * Main initialization function
     */
    function initializeEnhancedBudgetPage() {
        setupTabNavigation();
        setupDropdowns();
        setupModals();
        setupFormHandlers();
        setupSearchAndFilters();
        setupQuickActions();
        loadInitialData();
        setupCharts();
        setupRealTimeUpdates();
    }
    
    /**
     * Tab Navigation Setup
     */
    function setupTabNavigation() {
        $('.tab-button').on('click', function() {
            const tabId = $(this).data('tab');
            switchTab(tabId);
        });
        
        // Handle direct links to tabs
        $('.view-all-link').on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            if (tabId) {
                switchTab(tabId);
            }
        });
    }
    
    function switchTab(tabId) {
        // Update tab buttons
        $('.tab-button').removeClass('active');
        $(`.tab-button[data-tab="${tabId}"]`).addClass('active');
        
        // Update tab content
        $('.tab-content').removeClass('active');
        $(`#tab-${tabId}`).addClass('active');
        
        currentTab = tabId;
        
        // Load tab-specific data
        loadTabData(tabId);
        
        // Update URL without reload
        if (history.pushState) {
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('tab', tabId);
            history.pushState(null, '', newUrl);
        }
    }
    
    /**
     * Dropdown Setup
     */
    function setupDropdowns() {
        $('.dropdown-toggle').on('click', function(e) {
            e.stopPropagation();
            const dropdown = $(this).closest('.dropdown');
            dropdown.toggleClass('active');
            
            // Close other dropdowns
            $('.dropdown').not(dropdown).removeClass('active');
        });
        
        // Close dropdowns when clicking outside
        $(document).on('click', function() {
            $('.dropdown').removeClass('active');
        });
        
        // Prevent dropdown close when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
    }
    
    /**
     * Modal Setup
     */
    function setupModals() {
        // Quick add outcome modal
        $('#quick-add-outcome').on('click', function() {
            showModal('#quick-add-outcome-modal');
        });
        
        // Modal close handlers
        $('.modal-close').on('click', function() {
            hideModal($(this).closest('.modal'));
        });
        
        // Close modal when clicking overlay
        $('.modal').on('click', function(e) {
            if (e.target === this) {
                hideModal($(this));
            }
        });
        
        // Escape key to close modal
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal($('.modal:visible'));
            }
        });
    }
    
    function showModal(modalSelector) {
        $(modalSelector).fadeIn(300);
        $('body').addClass('modal-open');
    }
    
    function hideModal(modal) {
        modal.fadeOut(300);
        $('body').removeClass('modal-open');
    }
    
    /**
     * Form Handlers Setup
     */
    function setupFormHandlers() {
        // Quick add outcome form
        $('#quick-add-outcome-form').on('submit', function(e) {
            e.preventDefault();
            handleQuickAddOutcome($(this));
        });
        
        // General settings form
        $('#general-settings-form').on('submit', function(e) {
            e.preventDefault();
            handleGeneralSettings($(this));
        });
        
        // Invite user form
        $('#invite-user-form').on('submit', function(e) {
            e.preventDefault();
            handleInviteUser($(this));
        });
        
        // Notifications settings form
        $('#notifications-settings-form').on('submit', function(e) {
            e.preventDefault();
            handleNotificationSettings($(this));
        });
    }
    
    /**
     * Search and Filters Setup
     */
    function setupSearchAndFilters() {
        // Outcomes search
        $('#outcomes-search').on('input', debounce(function() {
            filterOutcomes();
        }, 300));
        
        // Filter dropdowns
        $('#category-filter, #date-filter').on('change', function() {
            filterOutcomes();
        });
        
        // Clear filters
        $('#clear-filters').on('click', function() {
            clearFilters();
        });
        
        // Period selector
        $('#period-selector').on('change', function() {
            const period = $(this).val();
            updateDataForPeriod(period);
        });
        
        // Analysis period
        $('#analysis-period').on('change', function() {
            const period = $(this).val();
            updateAnalysisData(period);
        });
    }
    
    /**
     * Quick Actions Setup
     */
    function setupQuickActions() {
        // Refresh data
        $('#refresh-data').on('click', function() {
            refreshAllData();
        });
        
        // Export to Excel
        $('#export-excel, #export-budget-data').on('click', function() {
            exportToExcel();
        });
        
        // Print budget
        $('#print-budget').on('click', function() {
            printBudget();
        });
        
        // Delete budget
        $('#delete-budget').on('click', function() {
            confirmDeleteBudget();
        });
        
        // Bulk actions
        $('#bulk-actions-btn').on('click', function() {
            toggleBulkActions();
        });
        
        // Select all outcomes
        $('#select-all-outcomes').on('change', function() {
            const checked = $(this).is(':checked');
            $('.outcome-checkbox').prop('checked', checked);
            updateBulkActionsState();
        });
        
        // Individual outcome checkboxes
        $(document).on('change', '.outcome-checkbox', function() {
            updateBulkActionsState();
        });
    }
    
    /**
     * Load Initial Data
     */
    function loadInitialData() {
        showLoadingOverlay();
        
        // Load recent outcomes for overview
        loadRecentOutcomes();
        
        // Load chart data
        loadChartData();
        
        // Load shared users
        loadSharedUsers();
        
        // Load categories for filters
        loadCategories();
        
        hideLoadingOverlay();
    }
    
    /**
     * Load Tab-Specific Data
     */
    function loadTabData(tabId) {
        switch (tabId) {
            case 'outcomes':
                loadOutcomesData();
                break;
            case 'analysis':
                loadAnalysisData();
                break;
            case 'planning':
                loadPlanningData();
                break;
            case 'management':
                loadManagementData();
                break;
            case 'settings':
                loadSettingsData();
                break;
        }
    }
    
    /**
     * Chart Setup and Management
     */
    function setupCharts() {
        // Initialize Chart.js defaults
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text-primary');
            Chart.defaults.borderColor = getComputedStyle(document.documentElement).getPropertyValue('--border-primary');
            Chart.defaults.backgroundColor = getComputedStyle(document.documentElement).getPropertyValue('--bg-card');
        }
    }
    
    function initializeMonthlyBreakdownChart(data) {
        const ctx = document.getElementById('monthly-breakdown-chart');
        if (!ctx || charts.monthlyBreakdown) return;
        
        charts.monthlyBreakdown = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: window.budgexData.strings.budget || 'תקציב',
                    data: data.budget,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent-primary'),
                    backgroundColor: 'transparent',
                    tension: 0.4
                }, {
                    label: window.budgexData.strings.spent || 'הוצאות',
                    data: data.spent,
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--accent-warning'),
                    backgroundColor: 'transparent',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--border-primary')
                        }
                    },
                    x: {
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--border-primary')
                        }
                    }
                }
            }
        });
    }
    
    function initializeCategoryPieChart(data) {
        const ctx = document.getElementById('category-pie-chart');
        if (!ctx || charts.categoryPie) return;
        
        charts.categoryPie = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                        '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
    
    function initializeSpendingTrendsChart(data) {
        const ctx = document.getElementById('spending-trends-chart');
        if (!ctx || charts.spendingTrends) return;
        
        charts.spendingTrends = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: window.budgexData.strings.daily_spending || 'הוצאות יומיות',
                    data: data.daily,
                    backgroundColor: getComputedStyle(document.documentElement).getPropertyValue('--accent-primary'),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: getComputedStyle(document.documentElement).getPropertyValue('--border-primary')
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    /**
     * AJAX Functions
     */
    function makeAjaxRequest(action, data, successCallback, errorCallback) {
        $.ajax({
            url: window.budgexData.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'budgex_' + action,
                nonce: window.budgexData.nonce,
                budget_id: window.budgexData.budgetId,
                ...data
            },
            success: function(response) {
                if (response.success) {
                    if (successCallback) successCallback(response.data);
                } else {
                    console.error('AJAX Error:', response.data);
                    if (errorCallback) errorCallback(response.data);
                    else showNotification('error', response.data || window.budgexData.strings.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Request Failed:', error);
                if (errorCallback) errorCallback(error);
                else showNotification('error', window.budgexData.strings.error);
            }
        });
    }
    
    /**
     * Data Loading Functions
     */
    function loadRecentOutcomes() {
        makeAjaxRequest('get_recent_outcomes', {}, function(data) {
            renderRecentOutcomes(data);
        });
    }
    
    function loadChartData() {
        makeAjaxRequest('get_chart_data', {}, function(data) {
            if (data.monthly_breakdown) {
                initializeMonthlyBreakdownChart(data.monthly_breakdown);
            }
            if (data.category_breakdown) {
                initializeCategoryPieChart(data.category_breakdown);
            }
            if (data.spending_trends) {
                initializeSpendingTrendsChart(data.spending_trends);
            }
        });
    }
    
    function loadOutcomesData() {
        const filters = getOutcomesFilters();
        makeAjaxRequest('get_outcomes_data', filters, function(data) {
            renderOutcomesTable(data.outcomes);
            renderOutcomesPagination(data.pagination);
        });
    }
    
    function loadAnalysisData() {
        const period = $('#analysis-period').val() || 'month';
        makeAjaxRequest('get_analysis_data', { period: period }, function(data) {
            renderAnalysisData(data);
        });
    }
    
    function loadPlanningData() {
        makeAjaxRequest('get_planning_data', {}, function(data) {
            renderFutureExpenses(data.future_expenses);
            renderRecurringExpenses(data.recurring_expenses);
            renderBudgetAdjustments(data.budget_adjustments);
        });
    }
    
    function loadManagementData() {
        makeAjaxRequest('get_management_data', {}, function(data) {
            renderManagementData(data);
        });
    }
    
    function loadSettingsData() {
        makeAjaxRequest('get_settings_data', {}, function(data) {
            renderCurrentUsers(data.users);
            renderPendingInvitations(data.pending_invitations);
            populateSettingsForm(data.settings);
        });
    }
    
    function loadSharedUsers() {
        makeAjaxRequest('get_shared_users', {}, function(data) {
            renderSharedUsers(data);
        });
    }
    
    function loadCategories() {
        makeAjaxRequest('get_categories', {}, function(data) {
            populateCategories(data);
        });
    }
    
    /**
     * Rendering Functions
     */
    function renderRecentOutcomes(outcomes) {
        const container = $('#recent-outcomes');
        container.empty();
        
        if (!outcomes || outcomes.length === 0) {
            container.html('<p class="no-data">' + window.budgexData.strings.no_data + '</p>');
            return;
        }
        
        outcomes.forEach(function(outcome) {
            const outcomeElement = $(`
                <div class="outcome-item">
                    <div class="outcome-main">
                        <div class="outcome-description">${escapeHtml(outcome.description)}</div>
                        <div class="outcome-amount">${formatCurrency(outcome.amount)}</div>
                    </div>
                    <div class="outcome-meta">
                        <span class="outcome-date">${formatDate(outcome.date)}</span>
                        ${outcome.category ? '<span class="outcome-category">' + escapeHtml(outcome.category) + '</span>' : ''}
                    </div>
                </div>
            `);
            container.append(outcomeElement);
        });
    }
    
    function renderOutcomesTable(outcomes) {
        const tbody = $('#outcomes-table-body');
        tbody.empty();
        
        if (!outcomes || outcomes.length === 0) {
            tbody.html('<tr><td colspan="7" class="no-data">' + window.budgexData.strings.no_data + '</td></tr>');
            return;
        }
        
        outcomes.forEach(function(outcome) {
            const row = $(`
                <tr data-outcome-id="${outcome.id}">
                    ${window.budgexData.userRole !== 'viewer' ? '<td class="checkbox-column"><input type="checkbox" class="outcome-checkbox" value="' + outcome.id + '"></td>' : ''}
                    <td>${formatDate(outcome.date)}</td>
                    <td>${escapeHtml(outcome.description)}</td>
                    <td>${outcome.category ? escapeHtml(outcome.category) : '-'}</td>
                    <td class="amount-cell">${formatCurrency(outcome.amount)}</td>
                    <td>${escapeHtml(outcome.user_name)}</td>
                    ${window.budgexData.userRole !== 'viewer' ? '<td class="actions-column"><div class="action-buttons"><button class="edit-outcome" title="עריכה"><span class="dashicons dashicons-edit"></span></button><button class="delete-outcome" title="מחיקה"><span class="dashicons dashicons-trash"></span></button></div></td>' : ''}
                </tr>
            `);
            tbody.append(row);
        });
        
        setupOutcomeActions();
    }
    
    function renderSharedUsers(users) {
        const container = $('.shared-users-list');
        container.empty();
        
        if (!users || users.length === 0) {
            container.html('<p class="no-data">' + window.budgexData.strings.no_data + '</p>');
            return;
        }
        
        users.forEach(function(user) {
            const userElement = $(`
                <div class="shared-user-item">
                    <div class="user-avatar">${user.display_name.charAt(0).toUpperCase()}</div>
                    <div class="user-info">
                        <span class="user-name">${escapeHtml(user.display_name)}</span>
                        <span class="user-role">${user.role === 'admin' ? 'מנהל' : 'צופה'}</span>
                    </div>
                </div>
            `);
            container.append(userElement);
        });
    }
    
    function renderAnalysisData(data) {
        // Update performance metrics
        $('#daily-average').text(formatCurrency(data.daily_average));
        $('#monthly-savings').text(formatCurrency(data.monthly_savings));
        $('#month-end-forecast').text(formatCurrency(data.month_end_forecast));
        
        // Render category breakdown
        renderCategoryBreakdown(data.category_breakdown);
        
        // Render top categories
        renderTopCategories(data.top_categories);
    }
    
    function renderCategoryBreakdown(categories) {
        const container = $('#category-breakdown');
        container.empty();
        
        categories.forEach(function(category) {
            const percentage = Math.round((category.amount / category.total) * 100);
            const item = $(`
                <div class="category-item">
                    <div class="category-info">
                        <span class="category-name">${escapeHtml(category.name)}</span>
                        <span class="category-amount">${formatCurrency(category.amount)}</span>
                    </div>
                    <div class="category-bar">
                        <div class="category-progress" style="width: ${percentage}%"></div>
                    </div>
                    <span class="category-percentage">${percentage}%</span>
                </div>
            `);
            container.append(item);
        });
    }
    
    function renderTopCategories(categories) {
        const container = $('#top-categories');
        container.empty();
        
        categories.forEach(function(category, index) {
            const item = $(`
                <div class="top-category-item">
                    <div class="category-rank">${index + 1}</div>
                    <div class="category-info">
                        <span class="category-name">${escapeHtml(category.name)}</span>
                        <span class="category-amount">${formatCurrency(category.amount)}</span>
                    </div>
                    <div class="category-count">${category.count} הוצאות</div>
                </div>
            `);
            container.append(item);
        });
    }
    
    /**
     * Form Handlers
     */
    function handleQuickAddOutcome(form) {
        const formData = getFormData(form);
        
        makeAjaxRequest('add_outcome', formData, function(data) {
            showNotification('success', window.budgexData.strings.success);
            hideModal('#quick-add-outcome-modal');
            form[0].reset();
            refreshAllData();
        });
    }
    
    function handleGeneralSettings(form) {
        const formData = getFormData(form);
        
        makeAjaxRequest('update_budget_settings', formData, function(data) {
            showNotification('success', 'הגדרות התקציב נשמרו בהצלחה');
            // Update page title if name changed
            if (formData.budget_name) {
                $('.budget-title').text(formData.budget_name);
                document.title = formData.budget_name + ' - Budgex';
            }
        });
    }
    
    function handleInviteUser(form) {
        const formData = getFormData(form);
        
        makeAjaxRequest('invite_user', formData, function(data) {
            showNotification('success', 'הזמנה נשלחה בהצלחה');
            form[0].reset();
            loadSettingsData();
        });
    }
    
    function handleNotificationSettings(form) {
        const formData = getFormData(form);
        
        makeAjaxRequest('update_notification_settings', formData, function(data) {
            showNotification('success', 'הגדרות התראות נשמרו בהצלחה');
        });
    }
    
    /**
     * Filter Functions
     */
    function getOutcomesFilters() {
        return {
            search: $('#outcomes-search').val(),
            category: $('#category-filter').val(),
            date_filter: $('#date-filter').val(),
            page: getCurrentPage()
        };
    }
    
    function filterOutcomes() {
        loadOutcomesData();
    }
    
    function clearFilters() {
        $('#outcomes-search').val('');
        $('#category-filter').val('');
        $('#date-filter').val('');
        filterOutcomes();
    }
    
    /**
     * Utility Functions
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    function formatCurrency(amount) {
        const currency = window.budgexData.currency;
        const symbols = {
            'ILS': '₪',
            'USD': '$',
            'EUR': '€',
            'GBP': '£'
        };
        
        const symbol = symbols[currency] || currency;
        const formattedAmount = parseFloat(amount).toLocaleString('he-IL', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        return symbol + ' ' + formattedAmount;
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('he-IL');
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function getFormData(form) {
        const formData = {};
        form.find('input, select, textarea').each(function() {
            const $input = $(this);
            const name = $input.attr('name');
            if (name) {
                if ($input.attr('type') === 'checkbox') {
                    formData[name] = $input.is(':checked');
                } else {
                    formData[name] = $input.val();
                }
            }
        });
        return formData;
    }
    
    function showLoadingOverlay() {
        $('#loading-overlay').fadeIn(200);
    }
    
    function hideLoadingOverlay() {
        $('#loading-overlay').fadeOut(200);
    }
    
    function showNotification(type, message) {
        // Create notification element
        const notification = $(`
            <div class="notification notification-${type}">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `);
        
        // Add to page
        $('body').append(notification);
        
        // Show notification
        notification.slideDown(300);
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            notification.slideUp(300, function() {
                notification.remove();
            });
        }, 5000);
        
        // Manual close
        notification.find('.notification-close').on('click', function() {
            notification.slideUp(300, function() {
                notification.remove();
            });
        });
    }
    
    function getCurrentPage() {
        return parseInt($('.pagination .current').text()) || 1;
    }
    
    function refreshAllData() {
        showLoadingOverlay();
        
        // Refresh current tab data
        loadTabData(currentTab);
        
        // Refresh overview data
        loadRecentOutcomes();
        loadChartData();
        
        // Update alerts
        updateAlerts();
        
        hideLoadingOverlay();
    }
    
    function updateDataForPeriod(period) {
        showLoadingOverlay();
        
        makeAjaxRequest('get_period_data', { period: period }, function(data) {
            // Update charts with new data
            updateCharts(data);
            
            // Update summary cards
            updateSummaryCards(data.summary);
            
            hideLoadingOverlay();
        });
    }
    
    function updateCharts(data) {
        if (charts.monthlyBreakdown && data.monthly_breakdown) {
            charts.monthlyBreakdown.data = data.monthly_breakdown;
            charts.monthlyBreakdown.update();
        }
        
        if (charts.categoryPie && data.category_breakdown) {
            charts.categoryPie.data = data.category_breakdown;
            charts.categoryPie.update();
        }
        
        if (charts.spendingTrends && data.spending_trends) {
            charts.spendingTrends.data = data.spending_trends;
            charts.spendingTrends.update();
        }
    }
    
    function updateSummaryCards(summary) {
        // Update budget overview amounts
        $('.budget-amounts .amount').each(function() {
            const $amount = $(this);
            const amountType = $amount.closest('.amount-item').find('.label').text();
            
            // Update based on amount type
            // This would need to be implemented based on the specific data structure
        });
    }
    
    function updateAlerts() {
        makeAjaxRequest('get_budget_alerts', {}, function(data) {
            const alertsContainer = $('#budget-alerts');
            alertsContainer.empty();
            
            data.alerts.forEach(function(alert) {
                const alertElement = $(`
                    <div class="alert alert-${alert.type}">
                        <span class="dashicons dashicons-${alert.icon}"></span>
                        <div class="alert-content">
                            <strong>${alert.title}</strong>
                            <p>${alert.message}</p>
                        </div>
                    </div>
                `);
                alertsContainer.append(alertElement);
            });
        });
    }
    
    function setupOutcomeActions() {
        // Edit outcome
        $('.edit-outcome').on('click', function() {
            const outcomeId = $(this).closest('tr').data('outcome-id');
            editOutcome(outcomeId);
        });
        
        // Delete outcome
        $('.delete-outcome').on('click', function() {
            const outcomeId = $(this).closest('tr').data('outcome-id');
            deleteOutcome(outcomeId);
        });
    }
    
    function editOutcome(outcomeId) {
        makeAjaxRequest('get_outcome', { outcome_id: outcomeId }, function(data) {
            // Populate edit form with outcome data
            showEditOutcomeModal(data);
        });
    }
    
    function deleteOutcome(outcomeId) {
        if (confirm(window.budgexData.strings.confirm_delete)) {
            makeAjaxRequest('delete_outcome', { outcome_id: outcomeId }, function(data) {
                showNotification('success', 'ההוצאה נמחקה בהצלחה');
                loadOutcomesData();
                refreshAllData();
            });
        }
    }
    
    function updateBulkActionsState() {
        const selectedCount = $('.outcome-checkbox:checked').length;
        const $bulkBtn = $('#bulk-actions-btn');
        
        if (selectedCount > 0) {
            $bulkBtn.text(`פעולות מרובות (${selectedCount})`).addClass('active');
        } else {
            $bulkBtn.text('פעולות מרובות').removeClass('active');
        }
    }
    
    function toggleBulkActions() {
        const selectedIds = $('.outcome-checkbox:checked').map(function() {
            return $(this).val();
        }).get();
        
        if (selectedIds.length === 0) {
            showNotification('warning', 'יש לבחור הוצאות לפני ביצוע פעולה');
            return;
        }
        
        showBulkActionsModal(selectedIds);
    }
    
    function exportToExcel() {
        showLoadingOverlay();
        
        makeAjaxRequest('export_budget_data', {}, function(data) {
            // Create download link
            const link = document.createElement('a');
            link.href = data.download_url;
            link.download = data.filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            hideLoadingOverlay();
            showNotification('success', 'הקובץ יוצא בהצלחה');
        });
    }
    
    function printBudget() {
        window.print();
    }
    
    function confirmDeleteBudget() {
        if (confirm('האם אתה בטוח שברצונך למחוק את התקציב? פעולה זו לא ניתנת לביטול!')) {
            makeAjaxRequest('delete_budget', {}, function(data) {
                showNotification('success', 'התקציב נמחק בהצלחה');
                setTimeout(function() {
                    window.location.href = '/budgex/';
                }, 2000);
            });
        }
    }
    
    function setupRealTimeUpdates() {
        // Poll for updates every 30 seconds if there are other users
        if (window.budgexData.hasSharedUsers) {
            setInterval(function() {
                checkForUpdates();
            }, 30000);
        }
    }
    
    function checkForUpdates() {
        makeAjaxRequest('check_budget_updates', {}, function(data) {
            if (data.has_updates) {
                showUpdateNotification();
            }
        });
    }
    
    function showUpdateNotification() {
        const notification = $(`
            <div class="update-notification">
                <span>יש עדכונים חדשים בתקציב</span>
                <button id="refresh-updates">רענן</button>
                <button id="dismiss-update">&times;</button>
            </div>
        `);
        
        $('body').append(notification);
        
        $('#refresh-updates').on('click', function() {
            refreshAllData();
            notification.remove();
        });
        
        $('#dismiss-update').on('click', function() {
            notification.remove();
        });
    }
    
    // Handle browser back/forward
    window.addEventListener('popstate', function(e) {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'overview';
        switchTab(tab);
    });
    
    // Auto-save form data
    function setupAutoSave() {
        $('form input, form select, form textarea').on('input change', debounce(function() {
            const $form = $(this).closest('form');
            if ($form.hasClass('auto-save')) {
                saveFormDraft($form);
            }
        }, 1000));
    }
    
    function saveFormDraft(form) {
        const formId = form.attr('id');
        const formData = getFormData(form);
        localStorage.setItem('budgex_draft_' + formId, JSON.stringify(formData));
    }
    
    function loadFormDraft(formId) {
        const draftData = localStorage.getItem('budgex_draft_' + formId);
        if (draftData) {
            const data = JSON.parse(draftData);
            const $form = $('#' + formId);
            
            Object.keys(data).forEach(function(key) {
                const $input = $form.find(`[name="${key}"]`);
                if ($input.length) {
                    if ($input.attr('type') === 'checkbox') {
                        $input.prop('checked', data[key]);
                    } else {
                        $input.val(data[key]);
                    }
                }
            });
        }
    }
    
    // Initialize auto-save
    setupAutoSave();
    
    // Load form drafts
    $('form[id]').each(function() {
        loadFormDraft($(this).attr('id'));
    });
    
    // Missing functions implementation
    
    /**
     * Initialize Enhanced Budget (global function)
     */
    window.initEnhancedBudget = function() {
        initializeEnhancedBudgetPage();
    };
    
    /**
     * Save Settings Function
     */
    window.saveSettings = function(form) {
        const formData = getFormData($(form));
        
        makeAjaxRequest('save_budget_settings', formData, function(data) {
            showNotification('success', 'הגדרות נשמרו בהצלחה');
            
            // Update page title if name changed
            if (formData.budget_name) {
                $('.budget-title').text(formData.budget_name);
                document.title = formData.budget_name + ' - Budgex';
            }
        });
    };
    
    /**
     * Bulk Delete Outcomes Function
     */
    window.bulkDeleteOutcomes = function(outcomeIds) {
        if (!outcomeIds || outcomeIds.length === 0) {
            showNotification('warning', 'יש לבחור הוצאות למחיקה');
            return;
        }
        
        if (!confirm('האם אתה בטוח שברצונך למחוק ' + outcomeIds.length + ' הוצאות?')) {
            return;
        }
        
        makeAjaxRequest('bulk_delete_outcomes', {
            outcome_ids: outcomeIds
        }, function(data) {
            showNotification('success', data.message || 'הוצאות נמחקו בהצלחה');
            loadOutcomesData();
            refreshAllData();
        });
    };
    
    /**
     * Export Data Function
     */
    window.exportData = function(format, dateRange) {
        format = format || 'csv';
        dateRange = dateRange || 'all';
        
        showLoadingOverlay();
        
        makeAjaxRequest('export_data', {
            export_format: format,
            date_range: dateRange
        }, function(data) {
            if (data.file_url) {
                // Create download link
                const link = document.createElement('a');
                link.href = data.file_url;
                link.download = data.filename || 'budget_export.' + format;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showNotification('success', 'הקובץ יוצא בהצלחה');
            }
            hideLoadingOverlay();
        });
    };
    
    /**
     * Search Outcomes Function
     */
    window.searchOutcomes = function(searchTerm) {
        makeAjaxRequest('search_outcomes', {
            search_term: searchTerm
        }, function(data) {
            renderOutcomesTable(data.outcomes);
        });
    };
    
    /**
     * Load Quick Stats Function
     */
    window.loadQuickStats = function() {
        makeAjaxRequest('get_quick_stats', {}, function(data) {
            updateQuickStats(data);
        });
    };
    
    /**
     * Update Quick Stats Display
     */
    function updateQuickStats(stats) {
        if (stats.today_spent !== undefined) {
            $('.stat-today .stat-value').text(formatCurrency(stats.today_spent));
        }
        if (stats.week_spent !== undefined) {
            $('.stat-week .stat-value').text(formatCurrency(stats.week_spent));
        }
        if (stats.month_spent !== undefined) {
            $('.stat-month .stat-value').text(formatCurrency(stats.month_spent));
        }
        if (stats.avg_daily !== undefined) {
            $('.stat-avg-daily .stat-value').text(formatCurrency(stats.avg_daily));
        }
        if (stats.health_score !== undefined) {
            $('.stat-health .stat-value').text(stats.health_score + '%');
            
            // Update health indicator color
            const healthClass = stats.health_score >= 80 ? 'healthy' : 
                               stats.health_score >= 60 ? 'warning' : 'danger';
            $('.stat-health').removeClass('healthy warning danger').addClass(healthClass);
        }
    }
    
    /**
     * Enhanced Bulk Actions Modal
     */
    function showBulkActionsModal(selectedIds) {
        const modalHtml = `
            <div id="bulk-actions-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>פעולות מרובות (${selectedIds.length} פריטים)</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="bulk-actions-grid">
                            <button class="action-button danger" onclick="bulkDeleteOutcomes([${selectedIds.join(',')}])">
                                <span class="dashicons dashicons-trash"></span>
                                מחק הוצאות נבחרות
                            </button>
                            <button class="action-button secondary" onclick="exportSelectedOutcomes([${selectedIds.join(',')}])">
                                <span class="dashicons dashicons-download"></span>
                                ייצא הוצאות נבחרות
                            </button>
                            <button class="action-button secondary" onclick="categorizeSelectedOutcomes([${selectedIds.join(',')}])">
                                <span class="dashicons dashicons-category"></span>
                                עדכן קטגוריה
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        showModal('#bulk-actions-modal');
    }
    
    /**
     * Export Selected Outcomes
     */
    window.exportSelectedOutcomes = function(outcomeIds) {
        makeAjaxRequest('export_selected_outcomes', {
            outcome_ids: outcomeIds
        }, function(data) {
            if (data.file_url) {
                const link = document.createElement('a');
                link.href = data.file_url;
                link.download = data.filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            hideModal($('#bulk-actions-modal'));
        });
    };
    
    /**
     * Categorize Selected Outcomes
     */
    window.categorizeSelectedOutcomes = function(outcomeIds) {
        const category = prompt('הכנס קטגוריה חדשה:');
        if (category) {
            makeAjaxRequest('update_outcomes_category', {
                outcome_ids: outcomeIds,
                category: category
            }, function(data) {
                showNotification('success', 'קטגוריה עודכנה בהצלחה');
                loadOutcomesData();
                hideModal($('#bulk-actions-modal'));
            });
        }
    };
    
    /**
     * Advanced Search with Filters
     */
    function setupAdvancedSearch() {
        let searchTimeout;
        
        $('#outcomes-search').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();
            
            searchTimeout = setTimeout(function() {
                if (searchTerm.length >= 2 || searchTerm.length === 0) {
                    searchOutcomes(searchTerm);
                }
            }, 300);
        });
        
        // Advanced filter modal
        $('#advanced-filters-btn').on('click', function() {
            showAdvancedFiltersModal();
        });
    }
    
    /**
     * Show Advanced Filters Modal
     */
    function showAdvancedFiltersModal() {
        const modalHtml = `
            <div id="advanced-filters-modal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>סינון מתקדם</h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="advanced-filters-form">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>טווח תאריכים</label>
                                    <input type="date" name="date_from" placeholder="מתאריך">
                                    <input type="date" name="date_to" placeholder="עד תאריך">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>טווח סכומים</label>
                                    <input type="number" name="amount_min" placeholder="סכום מינימלי" step="0.01">
                                    <input type="number" name="amount_max" placeholder="סכום מקסימלי" step="0.01">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>קטגוריה</label>
                                    <select name="category">
                                        <option value="">כל הקטגוריות</option>
                                        <!-- Categories will be populated -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="action-button primary">החל סינון</button>
                                <button type="button" class="action-button secondary" onclick="clearAdvancedFilters()">נקה סינון</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        showModal('#advanced-filters-modal');
        
        // Handle form submission
        $('#advanced-filters-form').on('submit', function(e) {
            e.preventDefault();
            applyAdvancedFilters();
        });
    }
    
    /**
     * Apply Advanced Filters
     */
    function applyAdvancedFilters() {
        const formData = getFormData($('#advanced-filters-form'));
        
        makeAjaxRequest('filter_outcomes', {
            filters: formData
        }, function(data) {
            renderOutcomesTable(data.outcomes);
            hideModal($('#advanced-filters-modal'));
            showNotification('success', 'סינון הוחל בהצלחה');
        });
    }
    
    /**
     * Clear Advanced Filters
     */
    window.clearAdvancedFilters = function() {
        $('#advanced-filters-form')[0].reset();
        loadOutcomesData(); // Reload all data
        hideModal($('#advanced-filters-modal'));
    };
    
    // Initialize advanced search when document is ready
    $(document).ready(function() {
        setupAdvancedSearch();
        
        // Load quick stats on page load
        if (window.budgexData && window.budgexData.budgetId) {
            loadQuickStats();
        }
    });
    
})(jQuery);
