<?php
// This file displays the individual budget pages for users.

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Variables should be passed from the calling function
if (!isset($budget) || !isset($calculation) || !isset($user_role)) {
    echo '<div class="budgex-error"><p>' . __('שגיאה בטעינת נתוני התקציב', 'budgex') . '</p></div>';
    return;
}
?>

<div class="budgex-budget-page" dir="rtl">
    <div class="budget-page-header">
        <div class="budget-title-section">
            <h1><?php echo esc_html($budget->budget_name); ?></h1>
            <span class="budget-role-badge <?php echo $user_role; ?>">
                <?php 
                echo $user_role === 'owner' ? __('בעלים', 'budgex') : 
                    ($user_role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                ?>
            </span>
        </div>
        
        <div class="budget-actions">
            <a href="<?php echo home_url('/budgex/'); ?>" class="button secondary">
                <span class="dashicons dashicons-arrow-right-alt"></span>
                <?php _e('חזור לרשימה', 'budgex'); ?>
            </a>
            
            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                <button type="button" class="button primary toggle-all-management" id="toggle-management-panel">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e('ניהול מתקדם', 'budgex'); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>    <!-- Enhanced Budget Summary Cards -->
    <div class="enhanced-budget-summary">
        <div class="summary-cards-grid">
            <div class="summary-card total-budget">
                <div class="card-icon">
                    <span class="dashicons dashicons-chart-pie"></span>
                </div>
                <div class="card-content">
                    <h3><?php _e('סך התקציב הזמין', 'budgex'); ?></h3>
                    <div class="amount"><?php 
                        $total_available = isset($calculation['total_available']) ? $calculation['total_available'] : 0;
                        echo $calculator->format_currency($total_available, $budget->currency); 
                    ?></div>
                    <div class="breakdown">
                        <small><?php 
                            $monthly_budget = isset($calculation['budget_details']['monthly_budget']) ? $calculation['budget_details']['monthly_budget'] : $budget->monthly_budget;
                            $additional_budget = isset($calculation['budget_details']['additional_budget']) ? $calculation['budget_details']['additional_budget'] : 0;
                            printf(__('חודשי: %s | נוסף: %s', 'budgex'), 
                                $calculator->format_currency($monthly_budget, $budget->currency),
                                $calculator->format_currency($additional_budget, $budget->currency)
                            ); 
                        ?></small>
                    </div>
                </div>
            </div>

        <div class="summary-card spent-budget">
            <div class="card-icon">
                <span class="dashicons dashicons-cart"></span>
            </div>
            <div class="card-content">
                <h3><?php _e('סך ההוצאות', 'budgex'); ?></h3>
                <div class="amount spent"><?php echo $calculator->format_currency($calculation['total_spent'], $budget->currency); ?></div>
                <div class="breakdown">
                    <small><?php printf(__('מתוך %d הוצאות', 'budgex'), count($outcomes)); ?></small>
                </div>
            </div>
        </div>

        <div class="summary-card remaining-budget">
            <div class="card-icon">
                <span class="dashicons dashicons-analytics"></span>
            </div>
            <div class="card-content">
                <h3><?php _e('יתרה', 'budgex'); ?></h3>
                <div class="amount <?php echo $calculation['remaining'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $calculator->format_currency($calculation['remaining'], $budget->currency); ?>
                </div>
                <?php if ($calculation['remaining'] < 0): ?>
                    <div class="warning">
                        <small><?php _e('חריגה מהתקציב!', 'budgex'); ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Budget Details -->
    <div class="budget-details-section">
        <h2><?php _e('פרטי התקציב', 'budgex'); ?></h2>
        <div class="details-grid">
            <div class="detail-item">
                <label><?php _e('תאריך התחלה:', 'budgex'); ?></label>
                <span><?php echo date('d/m/Y', strtotime($budget->start_date)); ?></span>
            </div>
            <div class="detail-item">
                <label><?php _e('תקציב חודשי:', 'budgex'); ?></label>
                <span><?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?></span>
            </div>
            <div class="detail-item">
                <label><?php _e('מטבע:', 'budgex'); ?></label>
                <span><?php echo $budget->currency; ?></span>
            </div>
            <div class="detail-item">
                <label><?php _e('נוצר בתאריך:', 'budgex'); ?></label>
                <span><?php echo date('d/m/Y H:i', strtotime($budget->created_at)); ?></span>
            </div>
        </div>
    </div>

    <!-- Advanced Management Panel -->
    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
    <div id="advanced-management-panel" class="advanced-management-panel" style="display: none;">
        <div class="management-panel-header">
            <h2><?php _e('ניהול מתקדם', 'budgex'); ?></h2>
            <p><?php _e('כלים מתקדמים לניהול התקציב שלך', 'budgex'); ?></p>
        </div>

        <div class="management-sections">
            <!-- Budget Settings Section -->
            <div class="management-section">
                <h3><?php _e('הגדרות תקציב', 'budgex'); ?></h3>
                <div class="management-cards">
                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-edit"></span>
                            <h4><?php _e('ערוך פרטי תקציב', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('שנה שם, תקציב חודשי ומטבע', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#edit-budget-details-section">
                            <?php _e('ערוך תקציב', 'budgex'); ?>
                        </button>
                    </div>

                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <h4><?php _e('תאריך התחלה', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('התקציב החל מתאריך: ', 'budgex'); echo date('d/m/Y', strtotime($budget->start_date)); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#change-start-date-section">
                            <?php _e('שנה תאריך', 'budgex'); ?>
                        </button>
                    </div>

                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <h4><?php _e('הגדל תקציב חודשי', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('הגדל את התקציב החודשי החל מתאריך מסוים', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#increase-monthly-budget-section">
                            <?php _e('הגדל תקציב', 'budgex'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Future and Recurring Expenses Section -->
            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
            <div class="management-section">
                <h3><?php _e('הוצאות עתידיות וחוזרות', 'budgex'); ?></h3>
                <div class="management-cards">
                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-calendar-alt"></span>
                            <h4><?php _e('הוצאות עתידיות', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('נהל הוצאות חד-פעמיות המתוכננות לעתיד', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#future-expenses-section">
                            <?php _e('נהל הוצאות עתידיות', 'budgex'); ?>
                        </button>
                    </div>

                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-backup"></span>
                            <h4><?php _e('הוצאות חוזרות', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('הגדר הוצאות שחוזרות על עצמן במחזוריות קבועה', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#recurring-expenses-section">
                            <?php _e('נהל הוצאות חוזרות', 'budgex'); ?>
                        </button>
                    </div>

                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-visibility"></span>
                            <h4><?php _e('תחזית מאזן', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('צפה במאזן החזוי כולל הוצאות עתידיות', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#projected-balance-section">
                            <?php _e('צפה בתחזית', 'budgex'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- User Management Section -->
            <?php if ($user_role === 'owner'): ?>
            <div class="management-section">
                <h3><?php _e('ניהול משתמשים', 'budgex'); ?></h3>
                <div class="management-cards">
                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-groups"></span>
                            <h4><?php _e('משתמשים מורשים', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('נהל הרשאות משתמשים קיימים', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#manage-users-section">
                            <?php _e('נהל משתמשים', 'budgex'); ?>
                        </button>
                    </div>

                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-email-alt"></span>
                            <h4><?php _e('הזמנות ממתינות', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('צפה והזמן שוב הזמנות ממתינות', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#pending-invitations-section">
                            <?php _e('הזמנות ממתינות', 'budgex'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Data Management Section -->
            <div class="management-section">
                <h3><?php _e('ניהול נתונים', 'budgex'); ?></h3>
                <div class="management-cards">
                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-download"></span>
                            <h4><?php _e('ייצא נתונים', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('הורד את כל נתוני התקציב כקובץ Excel', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary" id="export-budget-data">
                            <?php _e('ייצא לאקסל', 'budgex'); ?>
                        </button>
                    </div>

                    <div class="management-card">
                        <div class="card-header">
                            <span class="dashicons dashicons-chart-bar"></span>
                            <h4><?php _e('דוחות מתקדמים', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('צפה בדוחות מפורטים וגרפים', 'budgex'); ?></p>
                        <button type="button" class="button button-secondary toggle-form" data-target="#advanced-reports-section">
                            <?php _e('צפה בדוחות', 'budgex'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <?php if ($user_role === 'owner'): ?>
            <div class="management-section danger-zone">
                <h3><?php _e('אזור מסוכן', 'budgex'); ?></h3>
                <div class="management-cards">
                    <div class="management-card danger">
                        <div class="card-header">
                            <span class="dashicons dashicons-trash"></span>
                            <h4><?php _e('מחק תקציב', 'budgex'); ?></h4>
                        </div>
                        <p><?php _e('מחיקת התקציב תמחק את כל הנתונים לצמיתות', 'budgex'); ?></p>
                        <button type="button" class="button button-danger" id="delete-budget-btn">
                            <?php _e('מחק תקציב', 'budgex'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Future and Recurring Expenses Forms -->
        
        <!-- Future Expenses Section -->
        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
        <div id="future-expenses-section" class="form-section" style="display: none;">
            <h3><?php _e('ניהול הוצאות עתידיות', 'budgex'); ?></h3>
            
            <!-- Add Future Expense Form -->
            <div class="form-subsection">
                <h4><?php _e('הוסף הוצאה עתידית', 'budgex'); ?></h4>
                <form id="add-future-expense-form" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="future_expense_name"><?php _e('שם ההוצאה', 'budgex'); ?></label>
                            <input type="text" id="future_expense_name" name="expense_name" required>
                        </div>
                        <div class="form-group">
                            <label for="future_expense_amount"><?php _e('סכום', 'budgex'); ?></label>
                            <input type="number" id="future_expense_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="future_expense_date"><?php _e('תאריך צפוי', 'budgex'); ?></label>
                            <input type="date" id="future_expense_date" name="expected_date" required>
                        </div>
                        <div class="form-group">
                            <label for="future_expense_category"><?php _e('קטגוריה', 'budgex'); ?></label>
                            <input type="text" id="future_expense_category" name="category" placeholder="<?php _e('קטגוריה (אופציונלי)', 'budgex'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="future_expense_description"><?php _e('תיאור', 'budgex'); ?></label>
                        <textarea id="future_expense_description" name="description" rows="3" placeholder="<?php _e('תיאור ההוצאה (אופציונלי)', 'budgex'); ?>"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                        <input type="submit" class="button primary" value="<?php _e('הוסף הוצאה עתידית', 'budgex'); ?>">
                        <button type="button" class="button secondary cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
                    </div>
                </form>
            </div>
            
            <!-- Future Expenses List -->
            <div class="form-subsection">
                <h4><?php _e('הוצאות עתידיות קיימות', 'budgex'); ?></h4>
                <div id="future-expenses-list" class="expenses-list">
                    <div class="loading-message"><?php _e('טוען הוצאות עתידיות...', 'budgex'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Recurring Expenses Section -->
        <div id="recurring-expenses-section" class="form-section" style="display: none;">
            <h3><?php _e('ניהול הוצאות חוזרות', 'budgex'); ?></h3>
            
            <!-- Add Recurring Expense Form -->
            <div class="form-subsection">
                <h4><?php _e('הוסף הוצאה חוזרת', 'budgex'); ?></h4>
                <form id="add-recurring-expense-form" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recurring_expense_name"><?php _e('שם ההוצאה', 'budgex'); ?></label>
                            <input type="text" id="recurring_expense_name" name="expense_name" required>
                        </div>
                        <div class="form-group">
                            <label for="recurring_expense_amount"><?php _e('סכום', 'budgex'); ?></label>
                            <input type="number" id="recurring_expense_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recurring_expense_start_date"><?php _e('תאריך התחלה', 'budgex'); ?></label>
                            <input type="date" id="recurring_expense_start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="recurring_expense_end_date"><?php _e('תאריך סיום', 'budgex'); ?></label>
                            <input type="date" id="recurring_expense_end_date" name="end_date" placeholder="<?php _e('אופציונלי', 'budgex'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recurring_expense_frequency"><?php _e('תדירות', 'budgex'); ?></label>
                            <select id="recurring_expense_frequency" name="frequency" required>
                                <option value=""><?php _e('בחר תדירות', 'budgex'); ?></option>
                                <option value="daily"><?php _e('יומי', 'budgex'); ?></option>
                                <option value="weekly"><?php _e('שבועי', 'budgex'); ?></option>
                                <option value="monthly"><?php _e('חודשי', 'budgex'); ?></option>
                                <option value="quarterly"><?php _e('רבעוני', 'budgex'); ?></option>
                                <option value="yearly"><?php _e('שנתי', 'budgex'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recurring_expense_interval"><?php _e('מרווח', 'budgex'); ?></label>
                            <input type="number" id="recurring_expense_interval" name="frequency_interval" min="1" value="1" required>
                            <small><?php _e('לדוגמה: כל 2 חודשים = מרווח 2', 'budgex'); ?></small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="recurring_expense_category"><?php _e('קטגוריה', 'budgex'); ?></label>
                            <input type="text" id="recurring_expense_category" name="category" placeholder="<?php _e('קטגוריה (אופציונלי)', 'budgex'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="recurring_expense_description"><?php _e('תיאור', 'budgex'); ?></label>
                        <textarea id="recurring_expense_description" name="description" rows="3" placeholder="<?php _e('תיאור ההוצאה (אופציונלי)', 'budgex'); ?>"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                        <input type="submit" class="button primary" value="<?php _e('הוסף הוצאה חוזרת', 'budgex'); ?>">
                        <button type="button" class="button secondary cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
                    </div>
                </form>
            </div>
            
            <!-- Recurring Expenses List -->
            <div class="form-subsection">
                <h4><?php _e('הוצאות חוזרות קיימות', 'budgex'); ?></h4>
                <div id="recurring-expenses-list" class="expenses-list">
                    <div class="loading-message"><?php _e('טוען הוצאות חוזרות...', 'budgex'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Projected Balance Section -->
        <div id="projected-balance-section" class="form-section" style="display: none;">
            <h3><?php _e('תחזית מאזן עתידי', 'budgex'); ?></h3>
            
            <div class="form-subsection">
                <form id="projected-balance-form" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="target_date"><?php _e('תאריך יעד לתחזית', 'budgex'); ?></label>
                            <input type="date" id="target_date" name="target_date" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="button primary"><?php _e('חשב תחזית', 'budgex'); ?></button>
                        </div>
                    </div>
                    
                    <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                </form>
            </div>
            
            <div id="projected-balance-results" class="projection-results" style="display: none;">
                <h4><?php _e('תוצאות התחזית', 'budgex'); ?></h4>
                <div class="projection-summary">
                    <!-- Results will be populated via AJAX -->
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Advanced Management Forms -->
    
        <!-- Edit Budget Details Form -->
        <?php if ($user_role === 'owner'): ?>
        <div id="edit-budget-details-section" class="form-section" style="display: none;">
            <h3><?php _e('עריכת פרטי התקציב', 'budgex'); ?></h3>
            <form id="edit-budget-form" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="budget_name"><?php _e('שם התקציב', 'budgex'); ?></label>
                        <input type="text" id="budget_name" name="budget_name" value="<?php echo esc_attr($budget->name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="budget_description"><?php _e('תיאור התקציב', 'budgex'); ?></label>
                        <textarea id="budget_description" name="budget_description" rows="3"><?php echo esc_textarea($budget->description); ?></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                    <input type="submit" name="edit_budget_details" class="button primary" value="<?php _e('עדכן פרטים', 'budgex'); ?>">
                    <button type="button" class="button cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Change Start Date Form -->
        <?php if ($user_role === 'owner'): ?>
        <div id="change-start-date-section" class="form-section" style="display: none;">
            <h3><?php _e('שינוי תאריך התחלה', 'budgex'); ?></h3>
            <div class="warning-notice">
                <span class="dashicons dashicons-warning"></span>
                <p><?php _e('שימו לב: שינוי תאריך ההתחלה עלול להשפיע על הדוחות והחישובים הקיימים', 'budgex'); ?></p>
            </div>
            <form id="change-start-date-form" method="post">
                <div class="form-group">
                    <label for="new_start_date"><?php _e('תאריך התחלה חדש', 'budgex'); ?></label>
                    <input type="date" id="new_start_date" name="new_start_date" value="<?php echo esc_attr($budget->start_date); ?>" required>
                </div>
                
                <div class="form-actions">
                    <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                    <input type="submit" name="change_start_date" class="button primary" value="<?php _e('עדכן תאריך', 'budgex'); ?>">
                    <button type="button" class="button cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Increase Monthly Budget Form -->
        <?php if ($user_role === 'owner'): ?>
        <div id="increase-monthly-budget-section" class="form-section" style="display: none;">
            <h3><?php _e('הגדלת תקציב חודשי', 'budgex'); ?></h3>
            <div class="info-notice">
                <span class="dashicons dashicons-info"></span>
                <p><?php _e('הגדלת התקציב החודשי תיכנס לתוקף מהתאריך שתבחר ואילך. התקציב הקודם יישאר כפי שהיה עד התאריך החדש.', 'budgex'); ?></p>
            </div>
            
            <form id="increase-monthly-budget-form" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_monthly_amount"><?php _e('תקציב חודשי חדש', 'budgex'); ?></label>
                        <div class="input-group">
                            <input type="number" id="new_monthly_amount" name="new_monthly_amount" step="0.01" min="<?php echo floatval($budget->monthly_budget) + 0.01; ?>" required 
                                   placeholder="<?php echo number_format($budget->monthly_budget, 2); ?>">
                            <span class="currency-symbol"><?php echo esc_html($budget->currency); ?></span>
                        </div>
                        <small class="form-hint"><?php printf(__('התקציב הנוכחי: %s', 'budgex'), $calculator->format_currency($budget->monthly_budget, $budget->currency)); ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="effective_date"><?php _e('תאריך יעד', 'budgex'); ?></label>
                        <input type="date" id="effective_date" name="effective_date" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        <small class="form-hint"><?php _e('התאריך ממנו יתחיל התקציב החדש', 'budgex'); ?></small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="increase_reason"><?php _e('סיבה להגדלה (אופציונלי)', 'budgex'); ?></label>
                    <textarea id="increase_reason" name="increase_reason" rows="3" 
                              placeholder="<?php _e('לדוגמה: העלאת שכר, הוצאות נוספות צפויות, שינוי בנסיבות', 'budgex'); ?>"></textarea>
                </div>
                
                <div class="form-actions">
                    <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                    <input type="submit" name="increase_monthly_budget" class="button primary" value="<?php _e('הגדל תקציב', 'budgex'); ?>">
                    <button type="button" class="button cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Manage Users Section -->
        <?php if ($user_role === 'owner'): ?>
        <div id="manage-users-section" class="form-section" style="display: none;">
            <h3><?php _e('ניהול משתמשים', 'budgex'); ?></h3>
            
            <!-- Current Users -->
            <div class="current-users">
                <h4><?php _e('משתמשים קיימים', 'budgex'); ?></h4>
                <?php
                global $wpdb;
                $users = $wpdb->get_results($wpdb->prepare("
                    SELECT u.*, bu.role 
                    FROM {$wpdb->prefix}budgex_users bu 
                    JOIN {$wpdb->users} u ON bu.user_id = u.ID 
                    WHERE bu.budget_id = %d
                ", $budget->id));
                ?>
                
                <?php if (!empty($users)): ?>
                <div class="users-list">
                    <?php foreach ($users as $user): ?>
                    <div class="user-item">
                        <div class="user-info">
                            <strong><?php echo esc_html($user->display_name); ?></strong>
                            <span class="user-role"><?php echo esc_html($user->role); ?></span>
                            <span class="user-email"><?php echo esc_html($user->user_email); ?></span>
                        </div>
                        <?php if ($user->ID != get_current_user_id()): ?>
                        <div class="user-actions">
                            <select class="user-role-select" data-user-id="<?php echo $user->ID; ?>">
                                <option value="viewer" <?php selected($user->role, 'viewer'); ?>><?php _e('צופה', 'budgex'); ?></option>
                                <option value="admin" <?php selected($user->role, 'admin'); ?>><?php _e('מנהל', 'budgex'); ?></option>
                            </select>
                            <button type="button" class="button button-small remove-user" data-user-id="<?php echo $user->ID; ?>">
                                <?php _e('הסר', 'budgex'); ?>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p><?php _e('אין משתמשים נוספים בתקציב זה', 'budgex'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Invitations Section -->
        <?php if ($user_role === 'owner'): ?>
        <div id="pending-invitations-section" class="form-section" style="display: none;">
            <h3><?php _e('הזמנות ממתינות', 'budgex'); ?></h3>
            
            <?php
            $pending_invitations = $wpdb->get_results($wpdb->prepare("
                SELECT * FROM {$wpdb->prefix}budgex_invitations 
                WHERE budget_id = %d AND status = 'pending'
            ", $budget->id));
            ?>
            
            <?php if (!empty($pending_invitations)): ?>
            <div class="invitations-list">
                <?php foreach ($pending_invitations as $invitation): ?>
                <div class="invitation-item">
                    <div class="invitation-info">
                        <strong><?php echo esc_html($invitation->email); ?></strong>
                        <span class="invitation-role"><?php echo esc_html($invitation->role); ?></span>
                        <span class="invitation-date"><?php echo date_i18n(get_option('date_format'), strtotime($invitation->created_at)); ?></span>
                    </div>
                    <div class="invitation-actions">
                        <button type="button" class="button button-small resend-invitation" data-invitation-id="<?php echo $invitation->id; ?>">
                            <?php _e('שלח שוב', 'budgex'); ?>
                        </button>
                        <button type="button" class="button button-small cancel-invitation" data-invitation-id="<?php echo $invitation->id; ?>">
                            <?php _e('ביטול', 'budgex'); ?>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p><?php _e('אין הזמנות ממתינות', 'budgex'); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Advanced Reports Section -->
        <div id="advanced-reports-section" class="form-section" style="display: none;">
            <h3><?php _e('דוחות מתקדמים', 'budgex'); ?></h3>
            
            <div class="reports-grid">
                <div class="report-card">
                    <h4><?php _e('דוח תקציב לפי חודש', 'budgex'); ?></h4>
                    <p><?php _e('צפה בהתפתחות התקציב לאורך זמן', 'budgex'); ?></p>
                    <button type="button" class="button generate-report" data-report="monthly">
                        <?php _e('צור דוח', 'budgex'); ?>
                    </button>
                </div>
                
                <div class="report-card">
                    <h4><?php _e('דוח הוצאות לפי קטגוריה', 'budgex'); ?></h4>
                    <p><?php _e('ניתוח הוצאות לפי סוגים', 'budgex'); ?></p>
                    <button type="button" class="button generate-report" data-report="category">
                        <?php _e('צור דוח', 'budgex'); ?>
                    </button>
                </div>
                
                <div class="report-card">
                    <h4><?php _e('יצוא לאקסל', 'budgex'); ?></h4>
                    <p><?php _e('יצוא כל נתוני התקציב לקובץ אקסל', 'budgex'); ?></p>
                    <button type="button" class="button export-excel" data-budget-id="<?php echo $budget->id; ?>">
                        <?php _e('יצא לאקסל', 'budgex'); ?>
                    </button>
                </div>
                
                <div class="report-card">
                    <h4><?php _e('דוח מפורט PDF', 'budgex'); ?></h4>
                    <p><?php _e('צור דוח מלא בפורמט PDF', 'budgex'); ?></p>
                    <button type="button" class="button generate-pdf" data-budget-id="<?php echo $budget->id; ?>">
                        <?php _e('צור PDF', 'budgex'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Report Results Container -->
            <div id="report-results" style="display: none;">
                <h4><?php _e('תוצאות הדוח', 'budgex'); ?></h4>
                <div id="report-content"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Quick Actions Section -->
    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
    <div class="quick-actions-section">
        <h2><?php _e('פעולות מהירות', 'budgex'); ?></h2>
        <div class="quick-actions-grid">
            <button type="button" class="action-card toggle-form" data-target="#add-outcome-section">
                <span class="dashicons dashicons-plus-alt"></span>
                <h3><?php _e('הוסף הוצאה', 'budgex'); ?></h3>
                <p><?php _e('רשום הוצאה חדשה', 'budgex'); ?></p>
            </button>
            
            <button type="button" class="action-card toggle-form" data-target="#add-budget-section">
                <span class="dashicons dashicons-money-alt"></span>
                <h3><?php _e('הוסף תקציב', 'budgex'); ?></h3>
                <p><?php _e('הוסף תקציב נוסף', 'budgex'); ?></p>
            </button>
            
            <?php if ($user_role === 'owner'): ?>
            <button type="button" class="action-card toggle-form" data-target="#invite-section">
                <span class="dashicons dashicons-groups"></span>
                <h3><?php _e('הזמן משתמשים', 'budgex'); ?></h3>
                <p><?php _e('שתף את התקציב', 'budgex'); ?></p>
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Add Outcome Section -->
    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
    <div id="add-outcome-section" class="form-section" style="display: none;">
        <h2><?php _e('הוסף הוצאה חדשה', 'budgex'); ?></h2>
        <form id="add-outcome-form" method="post" action="">
            <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="outcome_name"><?php _e('שם ההוצאה', 'budgex'); ?></label>
                    <input type="text" id="outcome_name" name="outcome_name" required 
                           placeholder="<?php _e('לדוגמה: קניות, דלק, ציוד', 'budgex'); ?>">
                </div>
                
                <div class="form-field">
                    <label for="outcome_amount"><?php _e('סכום', 'budgex'); ?></label>
                    <input type="number" id="outcome_amount" name="outcome_amount" step="0.01" min="0" required 
                           placeholder="0.00">
                </div>
                
                <div class="form-field">
                    <label for="outcome_date"><?php _e('תאריך ההוצאה', 'budgex'); ?></label>
                    <input type="date" id="outcome_date" name="outcome_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-field full-width">
                    <label for="outcome_description"><?php _e('תיאור ההוצאה', 'budgex'); ?></label>
                    <textarea id="outcome_description" name="outcome_description" rows="3" 
                              placeholder="<?php _e('תיאור מפורט של ההוצאה (אופציונלי)', 'budgex'); ?>"></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <input type="submit" name="budgex_add_outcome" class="button button-primary" value="<?php _e('הוסף הוצאה', 'budgex'); ?>">
                <button type="button" class="button cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
            </div>
        </form>
    </div>

    <!-- Add Additional Budget Section -->
    <div id="add-budget-section" class="form-section" style="display: none;">
        <h2><?php _e('הוסף תקציב נוסף', 'budgex'); ?></h2>
        <form id="add-additional-budget-form" method="post" action="">
            <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="additional_amount"><?php _e('סכום נוסף', 'budgex'); ?></label>
                    <input type="number" id="additional_amount" name="additional_amount" step="0.01" min="0" required 
                           placeholder="0.00">
                </div>
                
                <div class="form-field">
                    <label for="additional_description"><?php _e('תיאור', 'budgex'); ?></label>
                    <input type="text" id="additional_description" name="additional_description" 
                           placeholder="<?php _e('לדוגמה: בונוס, החזר, הכנסה נוספת', 'budgex'); ?>">
                </div>
            </div>
            
            <div class="form-actions">
                <input type="submit" name="budgex_add_additional_budget" class="button button-primary" value="<?php _e('הוסף תקציב נוסף', 'budgex'); ?>">
                <button type="button" class="button cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Invite Users Section -->
    <?php if ($user_role === 'owner'): ?>
    <div id="invite-section" class="form-section" style="display: none;">
        <h2><?php _e('הזמן משתמשים לתקציב', 'budgex'); ?></h2>
        <form id="invite-user-form" method="post" action="">
            <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="invite_email"><?php _e('כתובת אימייל', 'budgex'); ?></label>
                    <input type="email" id="invite_email" name="invite_email" required
                           placeholder="user@example.com">
                </div>
                
                <div class="form-field">
                    <label for="invite_role"><?php _e('הרשאה', 'budgex'); ?></label>
                    <select id="invite_role" name="invite_role">
                        <option value="viewer"><?php _e('צופה - יכול לראות את התקציב', 'budgex'); ?></option>
                        <option value="admin"><?php _e('מנהל - יכול לערוך ולהוסיף הוצאות', 'budgex'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <input type="submit" name="budgex_invite_user" class="button button-primary" value="<?php _e('שלח הזמנה', 'budgex'); ?>">
                <button type="button" class="button cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Outcomes Section -->
    <div class="outcomes-section">
        <div class="section-header">
            <h2><?php _e('הוצאות', 'budgex'); ?></h2>
            <span class="outcomes-count"><?php printf(__('%d הוצאות', 'budgex'), count($outcomes)); ?></span>
        </div>

        <!-- Add Outcome Form -->
        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
        <div id="add-outcome-form" class="form-section" style="display: none;">
            <h3><?php _e('הוסף הוצאה חדשה', 'budgex'); ?></h3>
            <form class="outcome-form" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="outcome_name"><?php _e('שם ההוצאה', 'budgex'); ?></label>
                        <input type="text" id="outcome_name" name="outcome_name" required>
                    </div>
                    <div class="form-group">
                        <label for="outcome_amount"><?php _e('סכום', 'budgex'); ?></label>
                        <input type="number" id="outcome_amount" name="outcome_amount" step="0.01" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="outcome_description"><?php _e('תיאור ההוצאה', 'budgex'); ?></label>
                    <textarea id="outcome_description" name="outcome_description" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="outcome_date"><?php _e('תאריך ההוצאה', 'budgex'); ?></label>
                    <input type="date" id="outcome_date" name="outcome_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-actions">
                    <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
                    <input type="submit" name="add_outcome" class="button primary" value="<?php _e('הוסף הוצאה', 'budgex'); ?>">
                    <button type="button" class="button secondary cancel-form"><?php _e('ביטול', 'budgex'); ?></button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <!-- Outcomes List -->
        <?php if (!empty($outcomes)): ?>
            <div class="outcomes-list">
                <?php foreach ($outcomes as $outcome): ?>
                    <div class="outcome-item">
                        <div class="outcome-main">
                            <div class="outcome-header">
                                <h4><?php echo esc_html($outcome->outcome_name); ?></h4>
                                <span class="outcome-amount"><?php echo $calculator->format_currency($outcome->amount, $budget->currency); ?></span>
                            </div>
                            <p class="outcome-description"><?php echo esc_html($outcome->description); ?></p>
                            <div class="outcome-meta">
                                <span class="outcome-date"><?php echo date('d/m/Y', strtotime($outcome->outcome_date)); ?></span>
                            </div>
                        </div>
                        <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <div class="outcome-actions">
                                <button type="button" class="button-link delete-outcome" data-outcome-id="<?php echo $outcome->id; ?>">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-outcomes-message">
                <div class="no-outcomes-icon">
                    <span class="dashicons dashicons-cart"></span>
                </div>
                <h3><?php _e('עדיין אין הוצאות', 'budgex'); ?></h3>
                <p><?php _e('התחל לעקוב אחר ההוצאות שלך על ידי הוספת ההוצאה הראשונה', 'budgex'); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Monthly Breakdown -->
    <?php if (isset($monthly_breakdown) && !empty($monthly_breakdown)): ?>
    <div class="monthly-breakdown-section">
        <h2><?php _e('פירוט חודשי', 'budgex'); ?></h2>
        <div class="breakdown-grid">
            <?php foreach ($monthly_breakdown as $month_data): ?>
                <div class="month-card">
                    <h4><?php echo $month_data['month_name']; ?></h4>
                    <div class="month-budget"><?php echo $calculator->format_currency($month_data['monthly_budget'], $budget->currency); ?></div>
                    <div class="month-spent"><?php echo $calculator->format_currency($month_data['spent'], $budget->currency); ?></div>
                    <div class="month-remaining <?php echo $month_data['remaining'] >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo $calculator->format_currency($month_data['remaining'], $budget->currency); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle forms
    $('.toggle-form').on('click', function() {
        var target = $(this).data('target');
        $(target).slideToggle();
    });
    
    // Cancel form
    $('.cancel-form').on('click', function() {
        $(this).closest('.form-section').slideUp();
    });
    
    // Handle outcome form submission
    $('.outcome-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_add_outcome',
            budget_id: $('input[name="budget_id"]').val(),
            outcome_name: $('#outcome_name').val(),
            amount: $('#outcome_amount').val(),
            description: $('#outcome_description').val(),
            outcome_date: $('#outcome_date').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Handle add outcome form submission
    $('#add-outcome-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_add_outcome',
            budget_id: $(this).find('input[name="budget_id"]').val(),
            outcome_name: $(this).find('#outcome_name').val(),
            amount: $(this).find('#outcome_amount').val(),
            description: $(this).find('#outcome_description').val(),
            outcome_date: $(this).find('#outcome_date').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Handle additional budget form submission
    $('#add-additional-budget-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_add_additional_budget',
            budget_id: $(this).find('input[name="budget_id"]').val(),
            additional_amount: $(this).find('#additional_amount').val(),
            additional_description: $(this).find('#additional_description').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Enhanced monthly budget increase form validation and submission
    $('#increase-monthly-budget-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var $newAmountField = $form.find('#new_monthly_amount');
        var $dateField = $form.find('#effective_date');
        var currentBudget = <?php echo floatval($budget->monthly_budget); ?>;
        
        // Clear previous errors
        $form.find('.form-error-message').remove();
        $form.find('.error').removeClass('error');
        
        var formData = {
            action: 'budgex_increase_monthly_budget',
            budget_id: $form.find('input[name="budget_id"]').val(),
            new_amount: $newAmountField.val(),
            effective_date: $dateField.val(),
            reason: $form.find('#increase_reason').val(),
            nonce: budgex_ajax.nonce
        };
        
        var hasErrors = false;
        
        // Validate new amount
        if (!formData.new_amount || formData.new_amount.trim() === '') {
            showFieldError($newAmountField, 'נא להזין סכום חדש');
            hasErrors = true;
        } else {
            var newAmount = parseFloat(formData.new_amount);
            if (isNaN(newAmount) || newAmount <= 0) {
                showFieldError($newAmountField, 'נא להזין סכום חיובי תקין');
                hasErrors = true;
            } else if (newAmount <= currentBudget) {
                showFieldError($newAmountField, 'הסכום החדש חייב להיות גבוה מהתקציב הנוכחי (' + currentBudget.toFixed(2) + ')');
                hasErrors = true;
            }
        }
        
        // Validate effective date
        if (!formData.effective_date) {
            showFieldError($dateField, 'נא לבחור תאריך יעד');
            hasErrors = true;
        } else {
            var selectedDate = new Date(formData.effective_date);
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showFieldError($dateField, 'התאריך לא יכול להיות בעבר');
                hasErrors = true;
            }
        }
        
        if (hasErrors) {
            return false;
        }
        
        // Show confirmation dialog
        var confirmMessage = 'האם אתה בטוח שברצונך להגדיל את התקציב החודשי?\n\n';
        confirmMessage += 'סכום נוכחי: <?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?>\n';
        confirmMessage += 'סכום חדש: ' + parseFloat(formData.new_amount).toFixed(2) + ' <?php echo esc_html($budget->currency); ?>\n';
        confirmMessage += 'יעיל מתאריך: ' + formatDate(formData.effective_date);
        
        if (!confirm(confirmMessage)) {
            return false;
        }
        
        // Disable form and show loading
        $submitBtn.prop('disabled', true).addClass('loading');
        $form.find('input, textarea, button').prop('disabled', true);
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showSuccessMessage('התקציב החודשי הוגדל בהצלחה!');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showErrorMessage(response.data.message || 'אירעה שגיאה בעדכון התקציב');
                    enableForm();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showErrorMessage('אירעה שגיאה בחיבור לשרת. נא לנסות שוב.');
                enableForm();
            }
        });
        
        function enableForm() {
            $submitBtn.prop('disabled', false).removeClass('loading');
            $form.find('input, textarea, button').prop('disabled', false);
        }
    });
    
    // Helper functions for form validation and feedback
    function showFieldError($field, message) {
        $field.addClass('error');
        $field.after('<span class="form-error-message">' + message + '</span>');
    }
    
    function showSuccessMessage(message) {
        var $notice = $('<div class="budgex-notification budgex-notification-success show">' +
            '<div class="notification-content">' +
            '<span class="notification-message">' + message + '</span>' +
            '</div>' +
            '</div>');
        
        $('body').append($notice);
        
        setTimeout(function() {
            $notice.removeClass('show');
            setTimeout(function() {
                $notice.remove();
            }, 300);
        }, 3000);
    }
    
    function showErrorMessage(message) {
        var $notice = $('<div class="budgex-notification budgex-notification-error show">' +
            '<div class="notification-content">' +
            '<span class="notification-message">' + message + '</span>' +
            '</div>' +
            '</div>');
        
        $('body').append($notice);
        
        setTimeout(function() {
            $notice.removeClass('show');
            setTimeout(function() {
                $notice.remove();
            }, 300);
        }, 5000);
    }
    
    function formatDate(dateStr) {
        var date = new Date(dateStr);
        return date.toLocaleDateString('he-IL');
    }
    
    // Real-time validation for new amount field
    $('#new_monthly_amount').on('input blur', function() {
        var $field = $(this);
        var value = $field.val();
        var currentBudget = <?php echo floatval($budget->monthly_budget); ?>;
        
        // Remove previous error
        $field.removeClass('error');
        $field.next('.form-error-message').remove();
        
        if (value && value.trim() !== '') {
            var amount = parseFloat(value);
            if (isNaN(amount) || amount <= 0) {
                showFieldError($field, 'נא להזין סכום חיובי תקין');
            } else if (amount <= currentBudget) {
                showFieldError($field, 'הסכום חייב להיות גבוה מ-' + currentBudget.toFixed(2));
            }
        }
    });
    
    // Real-time validation for date field
    $('#effective_date').on('change', function() {
        var $field = $(this);
        var value = $field.val();
        
        // Remove previous error
        $field.removeClass('error');
        $field.next('.form-error-message').remove();
        
        if (value) {
            var selectedDate = new Date(value);
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showFieldError($field, 'התאריך לא יכול להיות בעבר');
            }
        }
    });
    
    // Handle invite user form submission
    $('#invite-user-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_invite_user',
            budget_id: $(this).find('input[name="budget_id"]').val(),
            invite_email: $(this).find('#invite_email').val(),
            invite_role: $(this).find('#invite_role').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $(this)[0].reset();
                    $(this).closest('.form-section').slideUp();
                } else {
                    alert(response.data.message);
                }
            }.bind(this),
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Delete outcome
    $('.delete-outcome').on('click', function() {
        if (confirm(budgex_ajax.strings.confirm_delete)) {
            var outcomeId = $(this).data('outcome-id');
            
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
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });

    // Edit Budget Details
    $('#edit-budget-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: $(this).serialize() + '&action=budgex_edit_budget_details&nonce=' + budgex_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });

    // Change Start Date
    $('#change-start-date-form').on('submit', function(e) {
        e.preventDefault();
        
        if (confirm('האם אתה בטוח שברצונך לשנות את תאריך ההתחלה? פעולה זו עלולה להשפיע על הדוחות הקיימים.')) {
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: $(this).serialize() + '&action=budgex_change_start_date&nonce=' + budgex_ajax.nonce,
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });

    // Change User Role
    $('.user-role-select').on('change', function() {
        var userId = $(this).data('user-id');
        var newRole = $(this).val();
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_change_user_role',
                user_id: userId,
                budget_id: '<?php echo $budget->id; ?>',
                new_role: newRole,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('תפקיד המשתמש עודכן בהצלחה');
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });

    // Remove User
    $('.remove-user').on('click', function() {
        if (confirm('האם אתה בטוח שברצונך להסיר את המשתמש מהתקציב?')) {
            var userId = $(this).data('user-id');
            
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'budgex_remove_user',
                    user_id: userId,
                    budget_id: '<?php echo $budget->id; ?>',
                    nonce: budgex_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });

    // Resend Invitation
    $('.resend-invitation').on('click', function() {
        var invitationId = $(this).data('invitation-id');
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_resend_invitation',
                invitation_id: invitationId,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('ההזמנה נשלחה שוב בהצלחה');
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });

    // Cancel Invitation
    $('.cancel-invitation').on('click', function() {
        if (confirm('האם אתה בטוח שברצונך לבטל את ההזמנה?')) {
            var invitationId = $(this).data('invitation-id');
            
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'budgex_cancel_invitation',
                    invitation_id: invitationId,
                    nonce: budgex_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });

    // Generate Reports
    $('.generate-report').on('click', function() {
        var reportType = $(this).data('report');
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_generate_report',
                budget_id: '<?php echo $budget->id; ?>',
                report_type: reportType,
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#report-content').html(response.data.content);
                    $('#report-results').show();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });

    // Export to Excel
    $('.export-excel').on('click', function() {
        var budgetId = $(this).data('budget-id');
        window.location.href = budgex_ajax.ajax_url + '?action=budgex_export_excel&budget_id=' + budgetId + '&nonce=' + budgex_ajax.nonce;
    });

    // Generate PDF
    $('.generate-pdf').on('click', function() {
        var budgetId = $(this).data('budget-id');
        window.location.href = budgex_ajax.ajax_url + '?action=budgex_generate_pdf&budget_id=' + budgetId + '&nonce=' + budgex_ajax.nonce;
    });

    // Delete Budget
    $('#delete-budget-btn').on('click', function() {
        if (confirm('האם אתה בטוח שברצונך למחוק את התקציב? פעולה זו בלתי הפיכה!')) {
            if (confirm('האישור השני: כל הנתונים יימחקו לצמיתות. האם להמשיך?')) {
                $.ajax({
                    url: budgex_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'budgex_delete_budget',
                        budget_id: '<?php echo $budget->id; ?>',
                        nonce: budgex_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '<?php echo home_url('/budgex'); ?>';
                        } else {
                            alert(response.data.message);
                        }
                    },
                    error: function() {
                        alert(budgex_ajax.strings.error);
                    }
                });
            }
        }
    });
    
    // Future and Recurring Expenses Functionality
    
    // Toggle form sections
    $('.toggle-form').on('click', function() {
        var target = $(this).data('target');
        $(target).toggle();
        
        // Load data if showing expenses lists
        if (target === '#future-expenses-section') {
            loadFutureExpenses();
        } else if (target === '#recurring-expenses-section') {
            loadRecurringExpenses();
        }
    });
    
    // Cancel form buttons
    $('.cancel-form').on('click', function() {
        $(this).closest('.form-section').hide();
    });
    
    // Add Future Expense Form
    $('#add-future-expense-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_add_future_expense',
            budget_id: $('input[name="budget_id"]').val(),
            expense_name: $('#future_expense_name').val(),
            amount: $('#future_expense_amount').val(),
            expected_date: $('#future_expense_date').val(),
            category: $('#future_expense_category').val(),
            description: $('#future_expense_description').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#add-future-expense-form')[0].reset();
                    loadFutureExpenses();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Add Recurring Expense Form
    $('#add-recurring-expense-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_add_recurring_expense',
            budget_id: $('input[name="budget_id"]').val(),
            expense_name: $('#recurring_expense_name').val(),
            amount: $('#recurring_expense_amount').val(),
            start_date: $('#recurring_expense_start_date').val(),
            end_date: $('#recurring_expense_end_date').val(),
            frequency: $('#recurring_expense_frequency').val(),
            frequency_interval: $('#recurring_expense_interval').val(),
            category: $('#recurring_expense_category').val(),
            description: $('#recurring_expense_description').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#add-recurring-expense-form')[0].reset();
                    loadRecurringExpenses();
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Projected Balance Form
    $('#projected-balance-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_get_projected_balance',
            budget_id: $('input[name="budget_id"]').val(),
            target_date: $('#target_date').val(),
            nonce: budgex_ajax.nonce
        };
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    displayProjectedBalance(response.data.projection);
                } else {
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(budgex_ajax.strings.error);
            }
        });
    });
    
    // Load Future Expenses
    function loadFutureExpenses() {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_future_expenses',
                budget_id: '<?php echo $budget->id; ?>',
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayFutureExpenses(response.data.expenses);
                } else {
                    $('#future-expenses-list').html('<div class="error-message">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $('#future-expenses-list').html('<div class="error-message">שגיאה בטעינת ההוצאות</div>');
            }
        });
    }
    
    // Load Recurring Expenses
    function loadRecurringExpenses() {
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_get_recurring_expenses',
                budget_id: '<?php echo $budget->id; ?>',
                nonce: budgex_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayRecurringExpenses(response.data.expenses);
                } else {
                    $('#recurring-expenses-list').html('<div class="error-message">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $('#recurring-expenses-list').html('<div class="error-message">שגיאה בטעינת ההוצאות</div>');
            }
        });
    }
    
    // Display Future Expenses
    function displayFutureExpenses(expenses) {
        var html = '';
        
        if (expenses.length === 0) {
            html = '<div class="no-expenses-message">אין הוצאות עתידיות מתוכננות</div>';
        } else {
            html = '<div class="expenses-table">';
            html += '<div class="table-header">';
            html += '<div class="col-name">שם ההוצאה</div>';
            html += '<div class="col-amount">סכום</div>';
            html += '<div class="col-date">תאריך צפוי</div>';
            html += '<div class="col-category">קטגוריה</div>';
            html += '<div class="col-actions">פעולות</div>';
            html += '</div>';
            
            expenses.forEach(function(expense) {
                html += '<div class="table-row" data-expense-id="' + expense.id + '">';
                html += '<div class="col-name">' + expense.expense_name + '</div>';
                html += '<div class="col-amount">₪' + parseFloat(expense.amount).toFixed(2) + '</div>';
                html += '<div class="col-date">' + expense.expected_date + '</div>';
                html += '<div class="col-category">' + (expense.category || '-') + '</div>';
                html += '<div class="col-actions">';
                html += '<button class="button small confirm-future-expense" data-expense-id="' + expense.id + '">אשר</button>';
                html += '<button class="button small delete-future-expense" data-expense-id="' + expense.id + '">מחק</button>';
                html += '</div>';
                html += '</div>';
            });
            
            html += '</div>';
        }
        
        $('#future-expenses-list').html(html);
    }
    
    // Display Recurring Expenses
    function displayRecurringExpenses(expenses) {
        var html = '';
        
        if (expenses.length === 0) {
            html = '<div class="no-expenses-message">אין הוצאות חוזרות מוגדרות</div>';
        } else {
            html = '<div class="expenses-table">';
            html += '<div class="table-header">';
            html += '<div class="col-name">שם ההוצאה</div>';
            html += '<div class="col-amount">סכום</div>';
            html += '<div class="col-frequency">תדירות</div>';
            html += '<div class="col-next">הופעה הבאה</div>';
            html += '<div class="col-status">סטטוס</div>';
            html += '<div class="col-actions">פעולות</div>';
            html += '</div>';
            
            expenses.forEach(function(expense) {
                var frequencyText = getFrequencyText(expense.frequency, expense.frequency_interval);
                var statusText = expense.is_active === '1' ? 'פעיל' : 'לא פעיל';
                var statusClass = expense.is_active === '1' ? 'active' : 'inactive';
                
                html += '<div class="table-row" data-expense-id="' + expense.id + '">';
                html += '<div class="col-name">' + expense.expense_name + '</div>';
                html += '<div class="col-amount">₪' + parseFloat(expense.amount).toFixed(2) + '</div>';
                html += '<div class="col-frequency">' + frequencyText + '</div>';
                html += '<div class="col-next">' + expense.next_occurrence + '</div>';
                html += '<div class="col-status"><span class="status-badge ' + statusClass + '">' + statusText + '</span></div>';
                html += '<div class="col-actions">';
                html += '<button class="button small toggle-recurring-expense" data-expense-id="' + expense.id + '" data-active="' + expense.is_active + '">';
                html += (expense.is_active === '1' ? 'השבת' : 'הפעל');
                html += '</button>';
                html += '<button class="button small delete-recurring-expense" data-expense-id="' + expense.id + '">מחק</button>';
                html += '</div>';
                html += '</div>';
            });
            
            html += '</div>';
        }
        
        $('#recurring-expenses-list').html(html);
    }
    
    // Display Projected Balance
    function displayProjectedBalance(projection) {
        var html = '<div class="projection-card">';
        html += '<div class="current-balance">';
        html += '<h5>מאזן נוכחי</h5>';
        html += '<div class="amount">₪' + parseFloat(projection.current_balance).toFixed(2) + '</div>';
        html += '</div>';
        
        html += '<div class="projection-breakdown">';
        html += '<div class="breakdown-item">';
        html += '<span class="label">תקציב נוסף עד התאריך:</span>';
        html += '<span class="amount positive">+₪' + parseFloat(projection.additional_budget).toFixed(2) + '</span>';
        html += '</div>';
        
        html += '<div class="breakdown-item">';
        html += '<span class="label">הוצאות עתידיות:</span>';
        html += '<span class="amount negative">-₪' + parseFloat(projection.future_expenses).toFixed(2) + '</span>';
        html += '</div>';
        
        html += '<div class="breakdown-item">';
        html += '<span class="label">הוצאות חוזרות:</span>';
        html += '<span class="amount negative">-₪' + parseFloat(projection.recurring_expenses).toFixed(2) + '</span>';
        html += '</div>';
        html += '</div>';
        
        html += '<div class="projected-balance">';
        html += '<h5>מאזן צפוי ל-' + projection.target_date + '</h5>';
        var projectedAmount = parseFloat(projection.projected_balance);
        var balanceClass = projectedAmount >= 0 ? 'positive' : 'negative';
        html += '<div class="amount ' + balanceClass + '">₪' + projectedAmount.toFixed(2) + '</div>';
        html += '</div>';
        
        html += '</div>';
        
        $('#projected-balance-results .projection-summary').html(html);
        $('#projected-balance-results').show();
    }
    
    // Helper function for frequency text
    function getFrequencyText(frequency, interval) {
        var texts = {
            'daily': 'יומי',
            'weekly': 'שבועי', 
            'monthly': 'חודשי',
            'quarterly': 'רבעוני',
            'yearly': 'שנתי'
        };
        
        var baseText = texts[frequency] || frequency;
        return interval > 1 ? 'כל ' + interval + ' ' + baseText : baseText;
    }
    
    // Event handlers for expense actions
    $(document).on('click', '.confirm-future-expense', function() {
        if (confirm('האם לאשר את ההוצאה ולהוסיף אותה לתקציב?')) {
            var expenseId = $(this).data('expense-id');
            
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'budgex_confirm_future_expense',
                    expense_id: expenseId,
                    nonce: budgex_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadFutureExpenses();
                        // Refresh budget display if needed
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });
    
    $(document).on('click', '.delete-future-expense', function() {
        if (confirm('האם למחוק את ההוצאה העתידית?')) {
            var expenseId = $(this).data('expense-id');
            
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'budgex_delete_future_expense',
                    expense_id: expenseId,
                    nonce: budgex_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadFutureExpenses();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });
    
    $(document).on('click', '.toggle-recurring-expense', function() {
        var expenseId = $(this).data('expense-id');
        var currentActive = $(this).data('active');
        var newActive = currentActive === '1' ? 0 : 1;
        var actionText = newActive === 1 ? 'להפעיל' : 'להשבית';
        
        if (confirm('האם ' + actionText + ' את ההוצאה החוזרת?')) {
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'budgex_toggle_recurring_expense',
                    expense_id: expenseId,
                    is_active: newActive,
                    nonce: budgex_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadRecurringExpenses();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });
    
    $(document).on('click', '.delete-recurring-expense', function() {
        if (confirm('האם למחוק את ההוצאה החוזרת? פעולה זו תמחק את כל המופעים העתידיים.')) {
            var expenseId = $(this).data('expense-id');
            
            $.ajax({
                url: budgex_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'budgex_delete_recurring_expense',
                    expense_id: expenseId,
                    nonce: budgex_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadRecurringExpenses();
                    } else {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert(budgex_ajax.strings.error);
                }
            });
        }
    });

});
</script>