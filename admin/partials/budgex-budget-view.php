<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if (!isset($budget_summary) || !$budget_summary) {
    echo '<div class="error"><p>' . __('לא ניתן לטעון את נתוני התקציב', 'budgex') . '</p></div>';
    return;
}

$budget = $budget_summary['budget'];
$calculation = $budget_summary['calculation'];
$outcomes = $budget_summary['outcomes'];
$monthly_breakdown = $budget_summary['monthly_breakdown'];
$user_role = $budget_summary['user_role'];
$calculator = new Budgex_Budget_Calculator();
?>

<div class="wrap budgex-view-wrap" dir="rtl">
    <div class="budget-header">
        <h1><?php echo esc_html($budget->budget_name); ?>
            <span class="budget-role-badge <?php echo $user_role; ?>">
                <?php 
                echo $user_role === 'owner' ? __('בעלים', 'budgex') : 
                    ($user_role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                ?>
            </span>
        </h1>
        
        <div class="budget-actions">
            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                <button type="button" class="button toggle-form" data-target="#add-outcome-section">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e('הוסף הוצאה', 'budgex'); ?>
                </button>
                
                <button type="button" class="button toggle-form" data-target="#add-budget-section">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php _e('הוסף תקציב', 'budgex'); ?>
                </button>
            <?php endif; ?>
            
            <?php if ($user_role === 'owner'): ?>
                <button type="button" class="button toggle-form" data-target="#invite-section">
                    <span class="dashicons dashicons-groups"></span>
                    <?php _e('הזמן משתמשים', 'budgex'); ?>
                </button>
            <?php endif; ?>
            
            <a href="<?php echo admin_url('admin.php?page=budgex'); ?>" class="button">
                <span class="dashicons dashicons-arrow-right-alt"></span>
                <?php _e('חזור לרשימה', 'budgex'); ?>
            </a>
        </div>
    </div>

    <div class="budget-summary-grid">
        <div class="summary-card total-budget">
            <div class="card-icon">
                <span class="dashicons dashicons-chart-pie"></span>
            </div>
            <div class="card-content">
                <h3><?php _e('סך התקציב הזמין', 'budgex'); ?></h3>
                <div class="amount"><?php echo $calculator->format_currency($calculation['total_available'], $budget->currency); ?></div>
                <div class="breakdown">
                    <small><?php printf(__('מחודשי: %s | נוסף: %s', 'budgex'), 
                        $calculator->format_currency($calculation['budget_details']['monthly_budget'], $budget->currency),
                        $calculator->format_currency($calculation['budget_details']['additional_budget'], $budget->currency)
                    ); ?></small>
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
                    <small><?php printf(__('%d הוצאות', 'budgex'), count($outcomes)); ?></small>
                </div>
            </div>
        </div>

        <div class="summary-card remaining-budget">
            <div class="card-icon">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="card-content">
                <h3><?php _e('יתרה', 'budgex'); ?></h3>
                <div class="amount <?php echo $calculation['remaining'] < 0 ? 'negative' : 'positive'; ?>">
                    <?php echo $calculator->format_currency($calculation['remaining'], $budget->currency); ?>
                </div>
                <div class="breakdown">
                    <?php if ($calculation['remaining'] < 0): ?>
                        <small class="warning"><?php _e('חריגה מהתקציב!', 'budgex'); ?></small>
                    <?php else: ?>
                        <small><?php printf(__('%.1f%% נותר', 'budgex'), ($calculation['remaining'] / $calculation['total_available']) * 100); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="summary-card budget-info">
            <div class="card-icon">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="card-content">
                <h3><?php _e('מידע נוסף', 'budgex'); ?></h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="label"><?php _e('תאריך התחלה:', 'budgex'); ?></span>
                        <span class="value"><?php echo date('d/m/Y', strtotime($budget->start_date)); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label"><?php _e('חודשים פעילים:', 'budgex'); ?></span>
                        <span class="value"><?php echo $calculation['budget_details']['months_passed']; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label"><?php _e('תקציב חודשי:', 'budgex'); ?></span>
                        <span class="value"><?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Outcome Section -->
    <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
    <div id="add-outcome-section" class="form-section" style="display: none;">
        <h2><?php _e('הוסף הוצאה חדשה', 'budgex'); ?></h2>
        <form id="add-outcome-form" method="post" action="">
            <?php wp_nonce_field('budgex_add_outcome', 'budgex_nonce'); ?>
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
                    <label for="outcome_date"><?php _e('תאריך', 'budgex'); ?></label>
                    <input type="date" id="outcome_date" name="outcome_date" value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-field full-width">
                    <label for="outcome_description"><?php _e('תיאור', 'budgex'); ?></label>
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
            <?php wp_nonce_field('budgex_add_additional_budget', 'budgex_nonce'); ?>
            <input type="hidden" name="budget_id" value="<?php echo $budget->id; ?>">
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="additional_amount"><?php _e('סכום נוסף', 'budgex'); ?></label>
                    <input type="number" id="additional_amount" name="additional_amount" step="0.01" min="0" required
                           placeholder="0.00">
                    <p class="description"><?php _e('סכום חד-פעמי להוספה לתקציב', 'budgex'); ?></p>
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
            <?php wp_nonce_field('budgex_invite_user', 'budgex_nonce'); ?>
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

    <!-- Outcomes List -->
    <div class="outcomes-section">
        <h2><?php _e('רשימת הוצאות', 'budgex'); ?></h2>
        
        <?php if (!empty($outcomes)): ?>
            <div class="outcomes-table-wrapper">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('תאריך', 'budgex'); ?></th>
                            <th><?php _e('שם ההוצאה', 'budgex'); ?></th>
                            <th><?php _e('תיאור', 'budgex'); ?></th>
                            <th><?php _e('סכום', 'budgex'); ?></th>
                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <th><?php _e('פעולות', 'budgex'); ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($outcomes as $outcome): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($outcome->outcome_date)); ?></td>
                            <td><strong><?php echo esc_html($outcome->outcome_name); ?></strong></td>
                            <td><?php echo esc_html($outcome->description); ?></td>
                            <td class="outcome-amount">
                                <span class="amount"><?php echo $calculator->format_currency($outcome->amount, $budget->currency); ?></span>
                            </td>
                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <td>
                                <button type="button" class="button-link delete-outcome" data-outcome-id="<?php echo $outcome->id; ?>">
                                    <span class="dashicons dashicons-trash"></span>
                                    <?php _e('מחק', 'budgex'); ?>
                                </button>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="outcomes-total">
                            <td colspan="3"><strong><?php _e('סך הכל הוצאות:', 'budgex'); ?></strong></td>
                            <td><strong><?php echo $calculator->format_currency($calculation['total_spent'], $budget->currency); ?></strong></td>
                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                            <td></td>
                            <?php endif; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <div class="no-outcomes">
                <div class="dashicons dashicons-cart"></div>
                <h3><?php _e('עדיין אין הוצאות', 'budgex'); ?></h3>
                <p><?php _e('התחל לעקוב אחר ההוצאות שלך על ידי הוספת הוצאה ראשונה', 'budgex'); ?></p>
                <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                <button type="button" class="button button-primary toggle-form" data-target="#add-outcome-section">
                    <?php _e('הוסף הוצאה ראשונה', 'budgex'); ?>
                </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Monthly Breakdown -->
    <?php if (!empty($monthly_breakdown)): ?>
    <div class="monthly-breakdown-section">
        <h2><?php _e('פירוט חודשי', 'budgex'); ?></h2>
        <div class="breakdown-grid">
            <?php foreach (array_reverse($monthly_breakdown) as $month): ?>
            <div class="month-card">
                <h4><?php echo $month['hebrew_month'] . ' ' . $month['year']; ?></h4>
                <div class="month-amount">
                    <?php echo $calculator->format_currency($month['budget_added'], $budget->currency); ?>
                </div>
                <small><?php printf(__('חודש %d', 'budgex'), $month['month']); ?></small>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.budgex-view-wrap {
    direction: rtl;
    font-family: 'Arial', sans-serif;
}

.budget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #333;
}

.budget-header h1 {
    margin: 0;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 15px;
}

.budget-role-badge {
    background: #bb86fc;
    color: #000;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.budget-role-badge.owner {
    background: #4caf50;
}

.budget-role-badge.admin {
    background: #ff9800;
}

.budget-role-badge.viewer {
    background: #2196f3;
}

.budget-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.budget-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    color: #fff;
}

.card-icon {
    width: 50px;
    height: 50px;
    background: #bb86fc;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.card-icon .dashicons {
    font-size: 24px;
    color: #000;
}

.card-content {
    flex: 1;
}

.card-content h3 {
    margin: 0 0 8px 0;
    color: #bb86fc;
    font-size: 14px;
    font-weight: normal;
}

.amount {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    margin-bottom: 5px;
}

.amount.spent {
    color: #ff5722;
}

.amount.positive {
    color: #4caf50;
}

.amount.negative {
    color: #f44336;
}

.breakdown {
    color: #ccc;
    font-size: 12px;
}

.breakdown .warning {
    color: #ff5722;
    font-weight: bold;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
}

.info-item .label {
    color: #ccc;
}

.info-item .value {
    color: #fff;
    font-weight: bold;
}

.form-section {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    color: #fff;
}

.form-section h2 {
    margin-top: 0;
    color: #bb86fc;
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-field.full-width {
    grid-column: 1 / -1;
}

.form-field label {
    display: block;
    margin-bottom: 5px;
    color: #bb86fc;
    font-weight: bold;
}

.form-field input,
.form-field select,
.form-field textarea {
    width: 100%;
    background: #2d2d2d;
    border: 1px solid #555;
    border-radius: 4px;
    color: #fff;
    padding: 8px 12px;
}

.form-field input:focus,
.form-field select:focus,
.form-field textarea:focus {
    border-color: #bb86fc;
    outline: none;
}

.form-actions {
    display: flex;
    gap: 10px;
    border-top: 1px solid #333;
    padding-top: 20px;
}

.outcomes-section {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    color: #fff;
}

.outcomes-section h2 {
    margin-top: 0;
    color: #bb86fc;
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
}

.outcomes-table-wrapper {
    overflow-x: auto;
}

.wp-list-table {
    background: #2d2d2d;
    color: #fff;
}

.wp-list-table th {
    background: #333;
    color: #bb86fc;
    border-bottom: 1px solid #555;
}

.wp-list-table td {
    border-bottom: 1px solid #444;
}

.wp-list-table .striped > tbody > :nth-child(odd) {
    background: #2a2a2a;
}

.outcomes-total {
    background: #333 !important;
    font-weight: bold;
}

.no-outcomes {
    text-align: center;
    padding: 40px 20px;
    color: #ccc;
}

.no-outcomes .dashicons {
    font-size: 48px;
    width: 48px;
    height: 48px;
    color: #bb86fc;
    margin-bottom: 15px;
}

.monthly-breakdown-section {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 25px;
    color: #fff;
}

.monthly-breakdown-section h2 {
    margin-top: 0;
    color: #bb86fc;
    border-bottom: 1px solid #333;
    padding-bottom: 10px;
}

.breakdown-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
}

.month-card {
    background: #2d2d2d;
    border: 1px solid #555;
    border-radius: 6px;
    padding: 15px;
    text-align: center;
}

.month-card h4 {
    margin: 0 0 8px 0;
    color: #bb86fc;
    font-size: 14px;
}

.month-amount {
    font-size: 18px;
    font-weight: bold;
    color: #4caf50;
    margin-bottom: 5px;
}

.delete-outcome {
    color: #ff5722 !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 5px;
    border-radius: 3px;
    transition: background-color 0.2s;
}

.delete-outcome:hover {
    background: #ff5722;
    color: #fff !important;
}

.cancel-form {
    background: transparent !important;
    border-color: #666 !important;
    color: #ccc !important;
}

@media (max-width: 768px) {
    .budget-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .budget-actions {
        justify-content: center;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle form sections
    $('.toggle-form').on('click', function() {
        var target = $(this).data('target');
        $(target).slideToggle();
        
        // Hide other forms
        $('.form-section').not(target).slideUp();
    });
    
    // Cancel form
    $('.cancel-form').on('click', function() {
        $(this).closest('.form-section').slideUp();
    });
});
</script>
