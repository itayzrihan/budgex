<?php
/**
 * Enhanced Public Front-End Budget Management Page
 * 
 * This file provides the comprehensive front-end budget page for regular users
 * accessing budgex/budget/{ID} - includes all enhanced management features
 * with tabbed interface, exactly like the admin version but for public access.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Variables should be passed from the calling function
if (!isset($budget) || !isset($calculation) || !isset($user_role) || !isset($outcomes)) {
    echo '<div class="budgex-error"><p>' . __('שגיאה בטעינת נתוני התקציב', 'budgex') . '</p></div>';
    return;
}

// Initialize required classes
$calculator = new Budgex_Budget_Calculator();
$database = new Budgex_Database();

// Get enhanced data
$shared_users = $database->get_budget_shared_users($budget->id);
$pending_invitations = $database->get_pending_invitations($budget->id);
$future_expenses = $database->get_future_expenses($budget->id);
$recurring_expenses = $database->get_recurring_expenses($budget->id);
$monthly_breakdown = $calculator->get_monthly_breakdown($budget->id);
$budget_adjustments = $database->get_budget_adjustments($budget->id);

// Ensure calculations are safe
$total_available = isset($calculation['total_available']) ? $calculation['total_available'] : 0;
$total_spent = isset($calculation['total_spent']) ? $calculation['total_spent'] : 0;
$remaining = isset($calculation['remaining']) ? $calculation['remaining'] : 0;
$monthly_budget = isset($calculation['budget_details']['monthly_budget']) ? $calculation['budget_details']['monthly_budget'] : $budget->monthly_budget;
$additional_budget = isset($calculation['budget_details']['additional_budget']) ? $calculation['budget_details']['additional_budget'] : 0;
?>

<div class="budgex-app-container budgex-enhanced-container budgex-enhanced-public-budget-page" data-budget-id="<?php echo esc_attr($budget->id); ?>" dir="rtl">
    <!-- Enhanced Header -->
    <div class="enhanced-budget-header">
        <div class="header-top">
            <div class="breadcrumb">
                <a href="<?php echo home_url('/budgex/'); ?>" class="breadcrumb-link">
                    <span class="dashicons dashicons-arrow-right-alt"></span>
                    <?php _e('התקציבים שלי', 'budgex'); ?>
                </a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current"><?php echo esc_html($budget->budget_name); ?></span>
            </div>
            
            <div class="header-actions">
                <button type="button" class="action-button secondary" id="refresh-data">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('רענן', 'budgex'); ?>
                </button>
                
                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                    <button type="button" class="action-button primary" id="quick-add-outcome">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?php _e('הוסף הוצאה', 'budgex'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="header-main">
            <div class="budget-title-section">
                <h1 class="budget-title"><?php echo esc_html($budget->budget_name); ?></h1>
                <div class="budget-meta">
                    <span class="budget-role-badge <?php echo $user_role; ?>">
                        <?php 
                        echo $user_role === 'owner' ? __('בעלים', 'budgex') : 
                            ($user_role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                        ?>
                    </span>
                    <span class="budget-period">
                        <?php printf(__('החל מ-%s', 'budgex'), date('d/m/Y', strtotime($budget->start_date))); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Summary Dashboard -->
    <div class="enhanced-summary-dashboard">
        <div class="summary-cards-grid">
            <div class="summary-card total-budget">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="dashicons dashicons-chart-pie"></span>
                    </div>
                    <h3><?php _e('סך התקציב הזמין', 'budgex'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="amount primary"><?php echo $calculator->format_currency($total_available, $budget->currency); ?></div>
                    <div class="breakdown">
                        <div class="breakdown-item">
                            <span class="label"><?php _e('חודשי:', 'budgex'); ?></span>
                            <span class="value"><?php echo $calculator->format_currency($monthly_budget, $budget->currency); ?></span>
                        </div>
                        <div class="breakdown-item">
                            <span class="label"><?php _e('נוסף:', 'budgex'); ?></span>
                            <span class="value"><?php echo $calculator->format_currency($additional_budget, $budget->currency); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-card spent-budget">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="dashicons dashicons-cart"></span>
                    </div>
                    <h3><?php _e('סך ההוצאות', 'budgex'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="amount spent"><?php echo $calculator->format_currency($total_spent, $budget->currency); ?></div>
                    <div class="breakdown">
                        <div class="breakdown-item">
                            <span class="label"><?php _e('מספר הוצאות:', 'budgex'); ?></span>
                            <span class="value"><?php echo count($outcomes); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="summary-card remaining-budget">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="dashicons dashicons-analytics"></span>
                    </div>
                    <h3><?php _e('יתרה', 'budgex'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="amount <?php echo $remaining >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $calculator->format_currency($remaining, $budget->currency); ?>
                    </div>
                    <?php if ($remaining < 0): ?>
                        <div class="warning-badge">
                            <span class="dashicons dashicons-warning"></span>
                            <?php _e('חריגה מהתקציב', 'budgex'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="summary-card quick-stats">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="dashicons dashicons-chart-bar"></span>
                    </div>
                    <h3><?php _e('נתונים מהירים', 'budgex'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="quick-stats-grid">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo count($future_expenses); ?></span>
                            <span class="stat-label"><?php _e('הוצאות עתידיות', 'budgex'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo count($recurring_expenses); ?></span>
                            <span class="stat-label"><?php _e('הוצאות חוזרות', 'budgex'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Tabbed Interface -->
    <div class="budget-tabs-container">
        <div class="budget-tabs">
            <div class="tab-navigation">
                <button class="tab-button active" data-tab="overview">
                    <span class="dashicons dashicons-dashboard"></span>
                    <?php _e('סקירה', 'budgex'); ?>
                </button>
                <button class="tab-button" data-tab="outcomes">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('הוצאות', 'budgex'); ?>
                    <span class="tab-count"><?php echo count($outcomes); ?></span>
                </button>
                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                    <button class="tab-button" data-tab="future-expenses">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        <?php _e('הוצאות עתידיות', 'budgex'); ?>
                        <span class="tab-count"><?php echo count($future_expenses); ?></span>
                    </button>
                    <button class="tab-button" data-tab="recurring-expenses">
                        <span class="dashicons dashicons-backup"></span>
                        <?php _e('הוצאות חוזרות', 'budgex'); ?>
                        <span class="tab-count"><?php echo count($recurring_expenses); ?></span>
                    </button>
                    <button class="tab-button" data-tab="budget-management">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('ניהול תקציב', 'budgex'); ?>
                    </button>
                <?php endif; ?>
                <?php if ($user_role === 'owner'): ?>
                    <button class="tab-button" data-tab="users">
                        <span class="dashicons dashicons-groups"></span>
                        <?php _e('משתמשים', 'budgex'); ?>
                        <span class="tab-count"><?php echo count($shared_users); ?></span>
                    </button>
                <?php endif; ?>
                <button class="tab-button" data-tab="reports">
                    <span class="dashicons dashicons-chart-line"></span>
                    <?php _e('דוחות', 'budgex'); ?>
                </button>
            </div>

            <div class="tab-content">
                <!-- Overview Tab -->
                <div id="overview-tab" class="tab-pane active">
                    <div class="overview-content">
                        <div class="overview-grid">
                            <!-- Monthly Breakdown Chart -->
                            <div class="overview-section">
                                <h3><?php _e('פירוט חודשי', 'budgex'); ?></h3>
                                <div class="chart-container">
                                    <canvas id="monthlyBreakdownChart"></canvas>
                                </div>
                            </div>

                            <!-- Recent Expenses -->
                            <div class="overview-section">
                                <h3><?php _e('הוצאות אחרונות', 'budgex'); ?></h3>
                                <div class="recent-expenses-list">
                                    <?php 
                                    $recent_outcomes = array_slice($outcomes, 0, 5);
                                    if (!empty($recent_outcomes)): 
                                    ?>
                                        <?php foreach ($recent_outcomes as $outcome): ?>
                                            <div class="recent-expense-item">
                                                <div class="expense-info">
                                                    <div class="expense-name"><?php echo esc_html($outcome->outcome_name); ?></div>
                                                    <div class="expense-date"><?php echo date('d/m/Y', strtotime($outcome->outcome_date)); ?></div>
                                                </div>
                                                <div class="expense-amount">
                                                    <?php echo $calculator->format_currency($outcome->amount, $budget->currency); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="no-data-message">
                                            <span class="dashicons dashicons-info"></span>
                                            <?php _e('אין הוצאות רשומות עדיין', 'budgex'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Upcoming Expenses -->
                            <?php if (!empty($future_expenses)): ?>
                                <div class="overview-section">
                                    <h3><?php _e('הוצאות קרובות', 'budgex'); ?></h3>
                                    <div class="upcoming-expenses-list">
                                        <?php 
                                        $upcoming = array_slice($future_expenses, 0, 3);
                                        foreach ($upcoming as $expense): 
                                        ?>
                                            <div class="upcoming-expense-item">
                                                <div class="expense-info">
                                                    <div class="expense-name"><?php echo esc_html($expense->expense_name); ?></div>
                                                    <div class="expense-date"><?php echo date('d/m/Y', strtotime($expense->expected_date)); ?></div>
                                                </div>
                                                <div class="expense-amount">
                                                    <?php echo $calculator->format_currency($expense->amount, $budget->currency); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Outcomes Tab -->
                <div id="outcomes-tab" class="tab-pane">
                    <div class="outcomes-management">
                        <div class="outcomes-header">
                            <h3><?php _e('ניהול הוצאות', 'budgex'); ?></h3>
                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                <div class="outcomes-actions">
                                    <button type="button" class="action-button primary" id="add-outcome-btn">
                                        <span class="dashicons dashicons-plus-alt"></span>
                                        <?php _e('הוסף הוצאה', 'budgex'); ?>
                                    </button>
                                    <button type="button" class="action-button secondary" id="bulk-actions-btn">
                                        <span class="dashicons dashicons-admin-tools"></span>
                                        <?php _e('פעולות מרובות', 'budgex'); ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Outcomes Filters -->
                        <div class="outcomes-filters">
                            <div class="filter-group">
                                <label for="date-filter"><?php _e('סנן לפי תאריך:', 'budgex'); ?></label>
                                <select id="date-filter">
                                    <option value=""><?php _e('כל התאריכים', 'budgex'); ?></option>
                                    <option value="this-month"><?php _e('החודש הנוכחי', 'budgex'); ?></option>
                                    <option value="last-month"><?php _e('החודש הקודם', 'budgex'); ?></option>
                                    <option value="this-year"><?php _e('השנה הנוכחית', 'budgex'); ?></option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="category-filter"><?php _e('סנן לפי קטגוריה:', 'budgex'); ?></label>
                                <select id="category-filter">
                                    <option value=""><?php _e('כל הקטגוריות', 'budgex'); ?></option>
                                    <!-- Categories will be populated dynamically -->
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="search-outcomes"><?php _e('חיפוש:', 'budgex'); ?></label>
                                <input type="text" id="search-outcomes" placeholder="<?php _e('חפש הוצאות...', 'budgex'); ?>">
                            </div>
                        </div>

                        <!-- Enhanced Outcomes Table -->
                        <div class="enhanced-outcomes-table">
                            <div class="table-header">
                                <div class="table-cell checkbox-cell">
                                    <input type="checkbox" id="select-all-outcomes">
                                </div>
                                <div class="table-cell sortable" data-sort="name">
                                    <?php _e('שם ההוצאה', 'budgex'); ?>
                                    <span class="sort-indicator"></span>
                                </div>
                                <div class="table-cell sortable" data-sort="amount">
                                    <?php _e('סכום', 'budgex'); ?>
                                    <span class="sort-indicator"></span>
                                </div>
                                <div class="table-cell sortable" data-sort="date">
                                    <?php _e('תאריך', 'budgex'); ?>
                                    <span class="sort-indicator"></span>
                                </div>
                                <div class="table-cell">
                                    <?php _e('קטגוריה', 'budgex'); ?>
                                </div>
                                <div class="table-cell">
                                    <?php _e('תיאור', 'budgex'); ?>
                                </div>
                                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                    <div class="table-cell">
                                        <?php _e('פעולות', 'budgex'); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="table-body" id="outcomes-table-body">
                                <!-- Outcomes will be loaded dynamically -->
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="table-pagination">
                            <div class="pagination-info">
                                <span id="pagination-info-text"></span>
                            </div>
                            <div class="pagination-controls">
                                <button type="button" class="pagination-btn" id="prev-page" disabled>
                                    <span class="dashicons dashicons-arrow-right-alt2"></span>
                                </button>
                                <span class="pagination-pages" id="pagination-pages"></span>
                                <button type="button" class="pagination-btn" id="next-page">
                                    <span class="dashicons dashicons-arrow-left-alt2"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Future Expenses Tab -->
                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                    <div id="future-expenses-tab" class="tab-pane">
                        <!-- Future expenses content will be included from enhanced budget JS -->
                    </div>

                    <!-- Recurring Expenses Tab -->
                    <div id="recurring-expenses-tab" class="tab-pane">
                        <!-- Recurring expenses content will be included from enhanced budget JS -->
                    </div>

                    <!-- Budget Management Tab -->
                    <div id="budget-management-tab" class="tab-pane">
                        <!-- Budget management content will be included from enhanced budget JS -->
                    </div>
                <?php endif; ?>

                <!-- Users Tab -->
                <?php if ($user_role === 'owner'): ?>
                    <div id="users-tab" class="tab-pane">
                        <!-- Users management content will be included from enhanced budget JS -->
                    </div>
                <?php endif; ?>

                <!-- Reports Tab -->
                <div id="reports-tab" class="tab-pane">
                    <!-- Reports content will be included from enhanced budget JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Outcome Modal -->
    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
        <div id="quick-add-outcome-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><?php _e('הוסף הוצאה חדשה', 'budgex'); ?></h3>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <form id="quick-add-outcome-form" class="modal-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quick_outcome_name"><?php _e('שם ההוצאה', 'budgex'); ?></label>
                            <input type="text" id="quick_outcome_name" name="outcome_name" required>
                        </div>
                        <div class="form-group">
                            <label for="quick_outcome_amount"><?php _e('סכום', 'budgex'); ?></label>
                            <input type="number" id="quick_outcome_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quick_outcome_date"><?php _e('תאריך', 'budgex'); ?></label>
                            <input type="date" id="quick_outcome_date" name="outcome_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="quick_outcome_category"><?php _e('קטגוריה', 'budgex'); ?></label>
                            <input type="text" id="quick_outcome_category" name="category" list="categories-list">
                            <datalist id="categories-list">
                                <!-- Categories will be populated dynamically -->
                            </datalist>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quick_outcome_description"><?php _e('תיאור', 'budgex'); ?></label>
                        <textarea id="quick_outcome_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="action-button primary">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('הוסף הוצאה', 'budgex'); ?>
                        </button>
                        <button type="button" class="action-button secondary modal-close">
                            <?php _e('ביטול', 'budgex'); ?>
                        </button>
                    </div>
                    <input type="hidden" name="budget_id" value="<?php echo esc_attr($budget->id); ?>">
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize enhanced public budget page
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.tab-button').on('click', function() {
        var tabId = $(this).data('tab');
        
        // Update active tab button
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        
        // Update active tab pane
        $('.tab-pane').removeClass('active');
        $('#' + tabId + '-tab').addClass('active');
        
        // Load tab content if needed
        loadTabContent(tabId);
    });
    
    // Quick add outcome modal
    $('#quick-add-outcome').on('click', function() {
        $('#quick-add-outcome-modal').show();
    });
    
    $('.modal-close').on('click', function() {
        $('.modal-overlay').hide();
    });
    
    // Quick add outcome form submission
    $('#quick-add-outcome-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_add_outcome',
            budget_id: $('input[name="budget_id"]').val(),
            outcome_name: $('#quick_outcome_name').val(),
            amount: $('#quick_outcome_amount').val(),
            outcome_date: $('#quick_outcome_date').val(),
            category: $('#quick_outcome_category').val(),
            description: $('#quick_outcome_description').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#quick-add-outcome-modal').hide();
                    $('#quick-add-outcome-form')[0].reset();
                    location.reload(); // Refresh to show new outcome
                } else {
                    alert(response.data.message || 'שגיאה בהוספת ההוצאה');
                }
            },
            error: function() {
                alert('שגיאה בתקשורת עם השרת');
            }
        });
    });
    
    // Refresh data functionality
    $('#refresh-data').on('click', function() {
        location.reload();
    });
    
    // Load initial content
    loadTabContent('overview');
    
    function loadTabContent(tabId) {
        switch(tabId) {
            case 'overview':
                loadOverviewContent();
                break;
            case 'outcomes':
                loadOutcomesContent();
                break;
            case 'future-expenses':
                loadFutureExpensesContent();
                break;
            case 'recurring-expenses':
                loadRecurringExpensesContent();
                break;
            case 'budget-management':
                loadBudgetManagementContent();
                break;
            case 'users':
                loadUsersContent();
                break;
            case 'reports':
                loadReportsContent();
                break;
        }
    }
    
    function loadOverviewContent() {
        // Initialize monthly breakdown chart
        if (typeof Chart !== 'undefined') {
            initializeMonthlyBreakdownChart();
        }
    }
    
    function loadOutcomesContent() {
        // Load outcomes table
        loadOutcomesTable();
    }
    
    function loadOutcomesTable() {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_outcomes_table',
                budget_id: $('[data-budget-id]').data('budget-id'),
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#outcomes-table-body').html(response.data.html);
                    updatePagination(response.data.pagination);
                }
            },
            error: function() {
                $('#outcomes-table-body').html('<div class="error-message">שגיאה בטעינת ההוצאות</div>');
            }
        });
    }
    
    function updatePagination(pagination) {
        $('#pagination-info-text').text(pagination.info);
        // Update pagination controls
    }
    
    function initializeMonthlyBreakdownChart() {
        // Chart.js initialization for monthly breakdown
        var ctx = document.getElementById('monthlyBreakdownChart');
        if (ctx) {
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($monthly_breakdown, 'month_name')); ?>,
                    datasets: [{
                        label: '<?php _e("תקציב", "budgex"); ?>',
                        data: <?php echo json_encode(array_column($monthly_breakdown, 'monthly_budget')); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }, {
                        label: '<?php _e("הוצאות", "budgex"); ?>',
                        data: <?php echo json_encode(array_column($monthly_breakdown, 'spent')); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
      // Placeholder functions for other tab content - will be populated from enhanced budget JS
    function loadFutureExpensesContent() {
        $('#future-expenses-tab').html('<div class="loading">טוען הוצאות עתידיות...</div>');
        // Load from enhanced budget functionality
    }
    
    function loadRecurringExpensesContent() {
        $('#recurring-expenses-tab').html('<div class="loading">טוען הוצאות חוזרות...</div>');
        // Load from enhanced budget functionality
    }
    
    function loadBudgetManagementContent() {
        $('#budget-management-tab').html('<div class="loading">טוען כלי ניהול תקציב...</div>');
        // Load from enhanced budget functionality
    }
    
    function loadUsersContent() {
            function loadUsersContent() {
        $('#users-tab').html('<div class="loading">טוען ניהול משתמשים...</div>');
        // Load from enhanced budget functionality
    }    function loadReportsContent() {
        $('#reports-tab').html('<div class="loading">טוען דוחות...</div>');
        // Load from enhanced budget functionality
    }
});
</script>

<!-- JavaScript Data for Enhanced Budget Page -->
<script type="text/javascript">
    window.budgexEnhancedData = {
        budgetId: <?php echo $budget->id; ?>,
        userRole: '<?php echo esc_js($user_role); ?>',
        currency: '<?php echo esc_js($budget->currency); ?>',
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('budgex_public_nonce'); ?>',
        strings: {
            confirmDelete: '<?php _e('האם אתה בטוח שברצונך למחוק?', 'budgex'); ?>',
            loading: '<?php _e('טוען...', 'budgex'); ?>',
            error: '<?php _e('שגיאה', 'budgex'); ?>',
            success: '<?php _e('הצלחה', 'budgex'); ?>'
        }
    };
    
    // Initialize enhanced budget when document is ready
    jQuery(document).ready(function($) {
        if (typeof window.initializeEnhancedBudgetPage === 'function') {
            window.initializeEnhancedBudgetPage();
        }
    });
</script>
