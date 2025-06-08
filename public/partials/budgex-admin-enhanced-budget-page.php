<?php
/**
 * Enhanced Budget Management Page
 * 
 * This file provides a comprehensive, modern interface for budget management
 * with improved UX, better organization, and advanced features.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Variables should be passed from the calling function
if (!isset($budget) || !isset($calculation) || !isset($user_role)) {
    echo '<div class="budgex-error"><p>' . __('שגיאה בטעינת נתוני התקציב', 'budgex') . '</p></div>';
    return;
}

// Get additional data for enhanced features
$calculator = new Budgex_Budget_Calculator();
$database = new Budgex_Database();

// Get budget permissions and shared users
$shared_users = $database->get_budget_shared_users($budget->id);
$pending_invitations = $database->get_pending_invitations($budget->id);

// Get future and recurring expenses
$future_expenses = $database->get_future_expenses($budget->id);
$recurring_expenses = $database->get_recurring_expenses($budget->id);

// Get monthly breakdown data
$monthly_breakdown = $calculator->get_monthly_breakdown($budget->id);
$budget_adjustments = $database->get_budget_adjustments($budget->id);

// Calculate projected balance
$projected_balance = $calculator->calculate_projected_balance($budget->id);
?>

<div class="budgex-enhanced-budget-page" data-budget-id="<?php echo esc_attr($budget->id); ?>" dir="rtl">
    <!-- Enhanced Header with Navigation -->
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
                
                <div class="dropdown">
                    <button type="button" class="action-button dropdown-toggle" id="budget-menu">
                        <span class="dashicons dashicons-menu"></span>
                    </button>
                    <div class="dropdown-menu">
                        <a href="#" class="dropdown-item" id="export-excel">
                            <span class="dashicons dashicons-download"></span>
                            <?php _e('ייצא לאקסל', 'budgex'); ?>
                        </a>
                        <a href="#" class="dropdown-item" id="print-budget">
                            <span class="dashicons dashicons-printer"></span>
                            <?php _e('הדפס', 'budgex'); ?>
                        </a>
                        <?php if ($user_role === 'owner'): ?>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item danger" id="delete-budget">
                                <span class="dashicons dashicons-trash"></span>
                                <?php _e('מחק תקציב', 'budgex'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
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
                    <span class="budget-date">
                        <?php printf(__('נוצר ב%s', 'budgex'), date('d/m/Y', strtotime($budget->created_at))); ?>
                    </span>
                    <?php if (count($shared_users) > 0): ?>
                        <span class="shared-indicator">
                            <span class="dashicons dashicons-groups"></span>
                            <?php printf(__('משותף עם %d משתמשים', 'budgex'), count($shared_users)); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="budget-period-selector">
                <label><?php _e('תקופת תצוגה:', 'budgex'); ?></label>
                <select id="period-selector" class="period-selector">
                    <option value="current"><?php _e('החודש הנוכחי', 'budgex'); ?></option>
                    <option value="last-3"><?php _e('3 חודשים אחרונים', 'budgex'); ?></option>
                    <option value="last-6"><?php _e('6 חודשים אחרונים', 'budgex'); ?></option>
                    <option value="year"><?php _e('השנה הנוכחית', 'budgex'); ?></option>
                    <option value="all"><?php _e('כל התקופה', 'budgex'); ?></option>
                    <option value="custom"><?php _e('תקופה מותאמת', 'budgex'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <!-- Enhanced Dashboard Cards -->
    <div class="enhanced-dashboard-cards">
        <div class="dashboard-row">
            <!-- Main Budget Overview Card -->
            <div class="dashboard-card budget-overview">
                <div class="card-header">
                    <h3><?php _e('סקירת תקציב', 'budgex'); ?></h3>
                    <span class="card-icon">
                        <span class="dashicons dashicons-chart-pie"></span>
                    </span>
                </div>
                <div class="card-content">
                    <div class="budget-progress">
                        <div class="progress-circle" data-percentage="<?php echo round(($calculation['total_spent'] / $calculation['total_available']) * 100); ?>">
                            <svg viewBox="0 0 42 42" class="progress-ring">
                                <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="var(--border-primary)" stroke-width="3"></circle>
                                <circle cx="21" cy="21" r="15.91549430918954" fill="transparent" stroke="var(--accent-primary)" stroke-width="3" stroke-dasharray="<?php echo round(($calculation['total_spent'] / $calculation['total_available']) * 100); ?> 100" stroke-dashoffset="25"></circle>
                            </svg>
                            <div class="progress-text">
                                <span class="percentage"><?php echo round(($calculation['total_spent'] / $calculation['total_available']) * 100); ?>%</span>
                                <span class="label"><?php _e('נוצל', 'budgex'); ?></span>
                            </div>
                        </div>
                        
                        <div class="budget-amounts">
                            <div class="amount-item">
                                <span class="label"><?php _e('תקציב זמין', 'budgex'); ?></span>
                                <span class="amount positive"><?php echo $calculator->format_currency($calculation['total_available'], $budget->currency); ?></span>
                            </div>
                            <div class="amount-item">
                                <span class="label"><?php _e('סך הוצאות', 'budgex'); ?></span>
                                <span class="amount spent"><?php echo $calculator->format_currency($calculation['total_spent'], $budget->currency); ?></span>
                            </div>
                            <div class="amount-item">
                                <span class="label"><?php _e('יתרה', 'budgex'); ?></span>
                                <span class="amount <?php echo $calculation['remaining'] >= 0 ? 'positive' : 'negative'; ?>">
                                    <?php echo $calculator->format_currency($calculation['remaining'], $budget->currency); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="dashboard-card quick-stats">
                <div class="card-header">
                    <h3><?php _e('סטטיסטיקות מהירות', 'budgex'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo count($outcomes); ?></span>
                            <span class="stat-label"><?php _e('הוצאות החודש', 'budgex'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?></span>
                            <span class="stat-label"><?php _e('תקציב חודשי', 'budgex'); ?></span>
                        </div>
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

            <!-- Alerts and Notifications -->
            <div class="dashboard-card alerts">
                <div class="card-header">
                    <h3><?php _e('התראות', 'budgex'); ?></h3>
                </div>
                <div class="card-content">
                    <div class="alerts-list" id="budget-alerts">
                        <?php if ($calculation['remaining'] < 0): ?>
                            <div class="alert alert-danger">
                                <span class="dashicons dashicons-warning"></span>
                                <div class="alert-content">
                                    <strong><?php _e('חריגה מהתקציב!', 'budgex'); ?></strong>
                                    <p><?php printf(__('חרגת מהתקציב ב%s', 'budgex'), $calculator->format_currency(abs($calculation['remaining']), $budget->currency)); ?></p>
                                </div>
                            </div>
                        <?php elseif ($calculation['remaining'] / $calculation['total_available'] < 0.1): ?>
                            <div class="alert alert-warning">
                                <span class="dashicons dashicons-warning"></span>
                                <div class="alert-content">
                                    <strong><?php _e('יתרה נמוכה', 'budgex'); ?></strong>
                                    <p><?php _e('נשארו לך פחות מ-10% מהתקציב', 'budgex'); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (count($future_expenses) > 0): ?>
                            <div class="alert alert-info">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                <div class="alert-content">
                                    <strong><?php _e('הוצאות עתידיות', 'budgex'); ?></strong>
                                    <p><?php printf(__('יש לך %d הוצאות עתידיות מתוכננות', 'budgex'), count($future_expenses)); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-success">
                            <span class="dashicons dashicons-yes"></span>
                            <div class="alert-content">
                                <strong><?php _e('הכל תקין', 'budgex'); ?></strong>
                                <p><?php _e('התקציב מנוהל בצורה תקינה', 'budgex'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Tab Navigation -->
    <div class="enhanced-tab-navigation">
        <nav class="tab-nav budget-tabs">
            <button class="tab-button active" data-tab="overview"><?php _e('סקירה כללית', 'budgex'); ?></button>
            <button class="tab-button" data-tab="outcomes"><?php _e('הוצאות', 'budgex'); ?></button>
            <button class="tab-button" data-tab="analysis"><?php _e('ניתוח ודוחות', 'budgex'); ?></button>
            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                <button class="tab-button" data-tab="planning"><?php _e('תכנון', 'budgex'); ?></button>
                <button class="tab-button" data-tab="management"><?php _e('ניהול', 'budgex'); ?></button>
            <?php endif; ?>
            <?php if ($user_role === 'owner'): ?>
                <button class="tab-button" data-tab="settings"><?php _e('הגדרות', 'budgex'); ?></button>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="tab-content-container">
        
        <!-- Overview Tab -->
        <div class="tab-content active" id="tab-overview">
            <div class="overview-layout">
                <div class="overview-main">
                    <!-- Monthly Breakdown Chart -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><?php _e('פירוט חודשי', 'budgex'); ?></h3>
                            <div class="section-actions">
                                <button class="action-button secondary" id="toggle-chart-type">
                                    <span class="dashicons dashicons-chart-bar"></span>
                                    <?php _e('שנה תצוגה', 'budgex'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="chart-container">
                                <canvas id="monthly-breakdown-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Outcomes -->
                    <div class="section-card">
                        <div class="section-header">
                            <h3><?php _e('הוצאות אחרונות', 'budgex'); ?></h3>
                            <a href="#" class="view-all-link" data-tab="outcomes"><?php _e('צפה בכל ההוצאות', 'budgex'); ?></a>
                        </div>
                        <div class="section-content">
                            <div class="recent-outcomes-list" id="recent-outcomes">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overview-sidebar">
                    <!-- Budget Details -->
                    <div class="sidebar-card">
                        <div class="card-header">
                            <h4><?php _e('פרטי התקציב', 'budgex'); ?></h4>
                        </div>
                        <div class="card-content">
                            <div class="detail-list">
                                <div class="detail-item">
                                    <span class="label"><?php _e('תאריך התחלה', 'budgex'); ?></span>
                                    <span class="value"><?php echo date('d/m/Y', strtotime($budget->start_date)); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="label"><?php _e('תקציב חודשי', 'budgex'); ?></span>
                                    <span class="value"><?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="label"><?php _e('מטבע', 'budgex'); ?></span>
                                    <span class="value"><?php echo $budget->currency; ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="label"><?php _e('סטטוס', 'budgex'); ?></span>
                                    <span class="value status active"><?php _e('פעיל', 'budgex'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Projected Balance -->
                    <div class="sidebar-card">
                        <div class="card-header">
                            <h4><?php _e('מאזן חזוי', 'budgex'); ?></h4>
                        </div>
                        <div class="card-content">
                            <div class="projected-balance">
                                <div class="projection-item">
                                    <span class="label"><?php _e('סוף החודש', 'budgex'); ?></span>
                                    <span class="value <?php echo $projected_balance['end_of_month'] >= 0 ? 'positive' : 'negative'; ?>">
                                        <?php echo $calculator->format_currency($projected_balance['end_of_month'], $budget->currency); ?>
                                    </span>
                                </div>
                                <div class="projection-item">
                                    <span class="label"><?php _e('בעוד 3 חודשים', 'budgex'); ?></span>
                                    <span class="value <?php echo $projected_balance['three_months'] >= 0 ? 'positive' : 'negative'; ?>">
                                        <?php echo $calculator->format_currency($projected_balance['three_months'], $budget->currency); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shared Users -->
                    <?php if (count($shared_users) > 0): ?>
                    <div class="sidebar-card">
                        <div class="card-header">
                            <h4><?php _e('משתמשים משותפים', 'budgex'); ?></h4>
                        </div>
                        <div class="card-content">
                            <div class="shared-users-list">
                                <?php foreach ($shared_users as $user): ?>
                                    <div class="shared-user-item">
                                        <div class="user-avatar"><?php echo strtoupper(substr($user->display_name, 0, 1)); ?></div>
                                        <div class="user-info">
                                            <span class="user-name"><?php echo esc_html($user->display_name); ?></span>
                                            <span class="user-role"><?php echo $user->role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex'); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Outcomes Tab -->
        <div class="tab-content" id="tab-outcomes">
            <div class="outcomes-layout">
                <div class="outcomes-header">
                    <div class="search-filter-bar">
                        <div class="search-group">
                            <input type="text" id="outcomes-search" placeholder="<?php _e('חפש הוצאה...', 'budgex'); ?>" class="search-input">
                            <button class="search-button">
                                <span class="dashicons dashicons-search"></span>
                            </button>
                        </div>
                        
                        <div class="filter-group">
                            <select id="category-filter" class="filter-select">
                                <option value=""><?php _e('כל הקטגוריות', 'budgex'); ?></option>
                                <!-- Categories will be populated by JavaScript -->
                            </select>
                            
                            <select id="date-filter" class="filter-select">
                                <option value=""><?php _e('כל התאריכים', 'budgex'); ?></option>
                                <option value="today"><?php _e('היום', 'budgex'); ?></option>
                                <option value="week"><?php _e('השבוע', 'budgex'); ?></option>
                                <option value="month"><?php _e('החודש', 'budgex'); ?></option>
                                <option value="custom"><?php _e('תקופה מותאמת', 'budgex'); ?></option>
                            </select>
                            
                            <button class="filter-button" id="clear-filters">
                                <span class="dashicons dashicons-dismiss"></span>
                                <?php _e('נקה', 'budgex'); ?>
                            </button>
                        </div>
                        
                        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <div class="action-group">
                                <button class="action-button primary" id="add-outcome-btn">
                                    <span class="dashicons dashicons-plus-alt"></span>
                                    <?php _e('הוסף הוצאה', 'budgex'); ?>
                                </button>
                                
                                <button class="action-button secondary" id="bulk-actions-btn">
                                    <span class="dashicons dashicons-admin-settings"></span>
                                    <?php _e('פעולות מרובות', 'budgex'); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="outcomes-content">
                    <div class="outcomes-table-container">
                        <table class="outcomes-table" id="outcomes-table">
                            <thead>
                                <tr>
                                    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                        <th class="checkbox-column">
                                            <input type="checkbox" id="select-all-outcomes">
                                        </th>
                                    <?php endif; ?>
                                    <th class="sortable" data-sort="date"><?php _e('תאריך', 'budgex'); ?></th>
                                    <th class="sortable" data-sort="description"><?php _e('תיאור', 'budgex'); ?></th>
                                    <th class="sortable" data-sort="category"><?php _e('קטגוריה', 'budgex'); ?></th>
                                    <th class="sortable" data-sort="amount"><?php _e('סכום', 'budgex'); ?></th>
                                    <th><?php _e('הוסף על ידי', 'budgex'); ?></th>
                                    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                        <th class="actions-column"><?php _e('פעולות', 'budgex'); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="outcomes-table-body">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                        
                        <div class="table-pagination" id="outcomes-pagination">
                            <!-- Pagination will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Tab -->
        <div class="tab-content" id="tab-analysis">
            <div class="analysis-layout">
                <div class="analysis-header">
                    <h2><?php _e('ניתוח ודוחות', 'budgex'); ?></h2>
                    <div class="analysis-controls">
                        <select id="analysis-period" class="analysis-select">
                            <option value="month"><?php _e('החודש הנוכחי', 'budgex'); ?></option>
                            <option value="quarter"><?php _e('הרבעון הנוכחי', 'budgex'); ?></option>
                            <option value="year"><?php _e('השנה הנוכחית', 'budgex'); ?></option>
                            <option value="custom"><?php _e('תקופה מותאמת', 'budgex'); ?></option>
                        </select>
                        
                        <button class="action-button secondary" id="export-analysis">
                            <span class="dashicons dashicons-download"></span>
                            <?php _e('ייצא דוח', 'budgex'); ?>
                        </button>
                    </div>
                </div>

                <div class="analysis-content">
                    <div class="analysis-row">
                        <!-- Spending by Category -->
                        <div class="analysis-card">
                            <div class="card-header">
                                <h3><?php _e('הוצאות לפי קטגוריה', 'budgex'); ?></h3>
                            </div>
                            <div class="card-content">
                                <div class="chart-container">
                                    <canvas id="category-pie-chart"></canvas>
                                </div>
                                <div class="category-breakdown" id="category-breakdown">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Spending Trends -->
                        <div class="analysis-card">
                            <div class="card-header">
                                <h3><?php _e('מגמות הוצאות', 'budgex'); ?></h3>
                            </div>
                            <div class="card-content">
                                <div class="chart-container">
                                    <canvas id="spending-trends-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="analysis-row">
                        <!-- Budget Performance -->
                        <div class="analysis-card">
                            <div class="card-header">
                                <h3><?php _e('ביצועי תקציב', 'budgex'); ?></h3>
                            </div>
                            <div class="card-content">
                                <div class="performance-metrics">
                                    <div class="metric-item">
                                        <span class="metric-label"><?php _e('ממוצע הוצאות יומי', 'budgex'); ?></span>
                                        <span class="metric-value" id="daily-average">-</span>
                                    </div>
                                    <div class="metric-item">
                                        <span class="metric-label"><?php _e('חיסכון חודשי', 'budgex'); ?></span>
                                        <span class="metric-value" id="monthly-savings">-</span>
                                    </div>
                                    <div class="metric-item">
                                        <span class="metric-label"><?php _e('תחזית סוף חודש', 'budgex'); ?></span>
                                        <span class="metric-value" id="month-end-forecast">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Categories -->
                        <div class="analysis-card">
                            <div class="card-header">
                                <h3><?php _e('קטגוריות מובילות', 'budgex'); ?></h3>
                            </div>
                            <div class="card-content">
                                <div class="top-categories-list" id="top-categories">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Planning Tab (Admin/Owner only) -->
        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
        <div class="tab-content" id="tab-planning">
            <div class="planning-layout">
                <div class="planning-header">
                    <h2><?php _e('תכנון תקציבי', 'budgex'); ?></h2>
                    <p><?php _e('נהל הוצאות עתידיות, הוצאות חוזרות ותכנן את התקציב שלך מראש', 'budgex'); ?></p>
                </div>

                <div class="planning-sections">
                    <!-- Future Expenses -->
                    <div class="planning-section">
                        <div class="section-header">
                            <h3><?php _e('הוצאות עתידיות', 'budgex'); ?></h3>
                            <button class="action-button primary" id="add-future-expense">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף הוצאה עתידית', 'budgex'); ?>
                            </button>
                        </div>
                        <div class="section-content">
                            <div class="future-expenses-grid" id="future-expenses-grid">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Recurring Expenses -->
                    <div class="planning-section">
                        <div class="section-header">
                            <h3><?php _e('הוצאות חוזרות', 'budgex'); ?></h3>
                            <button class="action-button primary" id="add-recurring-expense">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף הוצאה חוזרת', 'budgex'); ?>
                            </button>
                        </div>
                        <div class="section-content">
                            <div class="recurring-expenses-list" id="recurring-expenses-list">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Budget Adjustments -->
                    <div class="planning-section">
                        <div class="section-header">
                            <h3><?php _e('התאמות תקציב', 'budgex'); ?></h3>
                            <button class="action-button primary" id="add-budget-adjustment">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף התאמה', 'budgex'); ?>
                            </button>
                        </div>
                        <div class="section-content">
                            <div class="budget-adjustments-timeline" id="budget-adjustments-timeline">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Management Tab (Admin/Owner only) -->
        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
        <div class="tab-content" id="tab-management">
            <div class="management-layout">
                <div class="management-header">
                    <h2><?php _e('ניהול תקציב', 'budgex'); ?></h2>
                    <p><?php _e('כלים מתקדמים לניהול ובקרה של התקציב', 'budgex'); ?></p>
                </div>

                <div class="management-sections">
                    <!-- Bulk Operations -->
                    <div class="management-section">
                        <div class="section-header">
                            <h3><?php _e('פעולות מרובות', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <div class="bulk-operations-grid">
                                <div class="bulk-operation-card">
                                    <div class="card-icon">
                                        <span class="dashicons dashicons-upload"></span>
                                    </div>
                                    <h4><?php _e('ייבוא הוצאות', 'budgex'); ?></h4>
                                    <p><?php _e('ייבא הוצאות מקובץ CSV או Excel', 'budgex'); ?></p>
                                    <button class="action-button secondary" id="import-outcomes">
                                        <?php _e('ייבא הוצאות', 'budgex'); ?>
                                    </button>
                                </div>

                                <div class="bulk-operation-card">
                                    <div class="card-icon">
                                        <span class="dashicons dashicons-admin-tools"></span>
                                    </div>
                                    <h4><?php _e('עדכון מרובה', 'budgex'); ?></h4>
                                    <p><?php _e('עדכן קטגוריות או תגיות למספר הוצאות', 'budgex'); ?></p>
                                    <button class="action-button secondary" id="bulk-update">
                                        <?php _e('עדכון מרובה', 'budgex'); ?>
                                    </button>
                                </div>

                                <div class="bulk-operation-card">
                                    <div class="card-icon">
                                        <span class="dashicons dashicons-backup"></span>
                                    </div>
                                    <h4><?php _e('גיבוי נתונים', 'budgex'); ?></h4>
                                    <p><?php _e('צור גיבוי מלא של נתוני התקציב', 'budgex'); ?></p>
                                    <button class="action-button secondary" id="backup-data">
                                        <?php _e('צור גיבוי', 'budgex'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Maintenance -->
                    <div class="management-section">
                        <div class="section-header">
                            <h3><?php _e('תחזוקת נתונים', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <div class="maintenance-tools">
                                <div class="tool-item">
                                    <div class="tool-info">
                                        <h4><?php _e('זיהוי כפילויות', 'budgex'); ?></h4>
                                        <p><?php _e('מצא והסר הוצאות כפולות', 'budgex'); ?></p>
                                    </div>
                                    <button class="action-button secondary" id="find-duplicates">
                                        <?php _e('בדק כפילויות', 'budgex'); ?>
                                    </button>
                                </div>

                                <div class="tool-item">
                                    <div class="tool-info">
                                        <h4><?php _e('ניקוי קטגוריות', 'budgex'); ?></h4>
                                        <p><?php _e('אחד קטגוריות דומות ונקה נתונים', 'budgex'); ?></p>
                                    </div>
                                    <button class="action-button secondary" id="clean-categories">
                                        <?php _e('נקה קטגוריות', 'budgex'); ?>
                                    </button>
                                </div>

                                <div class="tool-item">
                                    <div class="tool-info">
                                        <h4><?php _e('בדיקת תקינות', 'budgex'); ?></h4>
                                        <p><?php _e('בדק תקינות נתונים וחישובים', 'budgex'); ?></p>
                                    </div>
                                    <button class="action-button secondary" id="data-integrity-check">
                                        <?php _e('בדק תקינות', 'budgex'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Automation Rules -->
                    <div class="management-section">
                        <div class="section-header">
                            <h3><?php _e('כללי אוטומציה', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <div class="automation-rules" id="automation-rules">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <button class="action-button primary" id="add-automation-rule">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף כלל אוטומציה', 'budgex'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Settings Tab (Owner only) -->
        <?php if ($user_role === 'owner'): ?>
        <div class="tab-content" id="tab-settings">
            <div class="settings-layout">
                <div class="settings-header">
                    <h2><?php _e('הגדרות תקציב', 'budgex'); ?></h2>
                </div>

                <div class="settings-sections">
                    <!-- General Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><?php _e('הגדרות כלליות', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <form id="general-settings-form" class="settings-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="budget-name"><?php _e('שם התקציב', 'budgex'); ?></label>
                                        <input type="text" id="budget-name" name="budget_name" value="<?php echo esc_attr($budget->budget_name); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="monthly-budget"><?php _e('תקציב חודשי', 'budgex'); ?></label>
                                        <input type="number" id="monthly-budget" name="monthly_budget" value="<?php echo $budget->monthly_budget; ?>" step="0.01" min="0" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="currency"><?php _e('מטבע', 'budgex'); ?></label>
                                        <select id="currency" name="currency" required>
                                            <option value="ILS" <?php selected($budget->currency, 'ILS'); ?>>₪ - שקל ישראלי</option>
                                            <option value="USD" <?php selected($budget->currency, 'USD'); ?>>$ - דולר אמריקני</option>
                                            <option value="EUR" <?php selected($budget->currency, 'EUR'); ?>>€ - יורו</option>
                                            <option value="GBP" <?php selected($budget->currency, 'GBP'); ?>>£ - פאונד בריטי</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="start-date"><?php _e('תאריך התחלה', 'budgex'); ?></label>
                                        <input type="date" id="start-date" name="start_date" value="<?php echo $budget->start_date; ?>" required>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="action-button primary">
                                        <?php _e('שמור שינויים', 'budgex'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- User Management -->
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><?php _e('ניהול משתמשים', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <div class="user-management-tools">
                                <div class="invite-user-section">
                                    <h4><?php _e('הזמן משתמש חדש', 'budgex'); ?></h4>
                                    <form id="invite-user-form" class="invite-form">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="invite-email"><?php _e('כתובת אימייל', 'budgex'); ?></label>
                                                <input type="email" id="invite-email" name="email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="invite-role"><?php _e('הרשאה', 'budgex'); ?></label>
                                                <select id="invite-role" name="role" required>
                                                    <option value="viewer"><?php _e('צופה', 'budgex'); ?></option>
                                                    <option value="admin"><?php _e('מנהל', 'budgex'); ?></option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="action-button primary">
                                                    <?php _e('שלח הזמנה', 'budgex'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="current-users-section">
                                    <h4><?php _e('משתמשים נוכחיים', 'budgex'); ?></h4>
                                    <div class="users-list" id="current-users-list">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>

                                <?php if (count($pending_invitations) > 0): ?>
                                <div class="pending-invitations-section">
                                    <h4><?php _e('הזמנות ממתינות', 'budgex'); ?></h4>
                                    <div class="pending-invitations-list" id="pending-invitations-list">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="settings-section">
                        <div class="section-header">
                            <h3><?php _e('התראות', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <form id="notifications-settings-form" class="settings-form">
                                <div class="notification-setting">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="budget_exceeded" checked>
                                        <span class="checkmark"></span>
                                        <?php _e('התראה על חריגה מהתקציב', 'budgex'); ?>
                                    </label>
                                </div>

                                <div class="notification-setting">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="low_balance" checked>
                                        <span class="checkmark"></span>
                                        <?php _e('התראה על יתרה נמוכה (פחות מ-10%)', 'budgex'); ?>
                                    </label>
                                </div>

                                <div class="notification-setting">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="future_expenses" checked>
                                        <span class="checkmark"></span>
                                        <?php _e('התראה על הוצאות עתידיות', 'budgex'); ?>
                                    </label>
                                </div>

                                <div class="notification-setting">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="monthly_summary" checked>
                                        <span class="checkmark"></span>
                                        <?php _e('סיכום חודשי באימייל', 'budgex'); ?>
                                    </label>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="action-button primary">
                                        <?php _e('שמור הגדרות', 'budgex'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    <div class="settings-section danger-zone">
                        <div class="section-header">
                            <h3><?php _e('אזור מסוכן', 'budgex'); ?></h3>
                        </div>
                        <div class="section-content">
                            <div class="danger-actions">
                                <div class="danger-action">
                                    <div class="action-info">
                                        <h4><?php _e('איפוס התקציב', 'budgex'); ?></h4>
                                        <p><?php _e('מחיקת כל ההוצאות והתחלה מחדש (לא ניתן לבטל)', 'budgex'); ?></p>
                                    </div>
                                    <button class="action-button danger" id="reset-budget">
                                        <?php _e('אפס תקציב', 'budgex'); ?>
                                    </button>
                                </div>

                                <div class="danger-action">
                                    <div class="action-info">
                                        <h4><?php _e('מחיקת התקציב', 'budgex'); ?></h4>
                                        <p><?php _e('מחיקה מלאה של התקציב וכל הנתונים (לא ניתן לבטל)', 'budgex'); ?></p>
                                    </div>
                                    <button class="action-button danger" id="delete-budget">
                                        <?php _e('מחק תקציב', 'budgex'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Quick Add Outcome Modal -->
    <div id="quick-add-outcome-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><?php _e('הוסף הוצאה מהירה', 'budgex'); ?></h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="quick-add-outcome-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quick-outcome-description"><?php _e('תיאור', 'budgex'); ?></label>
                            <input type="text" id="quick-outcome-description" name="description" required>
                        </div>
                        <div class="form-group">
                            <label for="quick-outcome-amount"><?php _e('סכום', 'budgex'); ?></label>
                            <input type="number" id="quick-outcome-amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="quick-outcome-category"><?php _e('קטגוריה', 'budgex'); ?></label>
                            <input type="text" id="quick-outcome-category" name="category" list="categories-datalist">
                            <datalist id="categories-datalist">
                                <!-- Will be populated by JavaScript -->
                            </datalist>
                        </div>
                        <div class="form-group">
                            <label for="quick-outcome-date"><?php _e('תאריך', 'budgex'); ?></label>
                            <input type="date" id="quick-outcome-date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                        <button type="submit" class="action-button primary">
                            <?php _e('הוסף הוצאה', 'budgex'); ?>
                        </button>
                        <button type="button" class="action-button secondary modal-close">
                            <?php _e('ביטול', 'budgex'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p><?php _e('טוען...', 'budgex'); ?></p>
        </div>
    </div>
</div>

<!-- JavaScript Data -->
<script type="text/javascript">
    window.budgexData = {
        budgetId: <?php echo $budget->id; ?>,
        userRole: '<?php echo $user_role; ?>',
        currency: '<?php echo $budget->currency; ?>',
        ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
        nonce: '<?php echo wp_create_nonce('budgex_nonce'); ?>',
        strings: {
            confirm_delete: '<?php _e('האם אתה בטוח שברצונך למחוק?', 'budgex'); ?>',
            loading: '<?php _e('טוען...', 'budgex'); ?>',
            error: '<?php _e('שגיאה', 'budgex'); ?>',
            success: '<?php _e('בוצע בהצלחה', 'budgex'); ?>',
            no_data: '<?php _e('אין נתונים להצגה', 'budgex'); ?>'
        }
    };
</script>
