<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$user_id = get_current_user_id();
$database = new Budgex_Database();
$calculator = new Budgex_Budget_Calculator();
$budgets = $database->get_user_budgets($user_id);
?>

<div class="wrap budgex-admin-wrap" dir="rtl">
    <h1><?php _e('Budgex - מערכת ניהול תקציב', 'budgex'); ?>
        <a href="<?php echo admin_url('admin.php?page=budgex-new'); ?>" class="page-title-action"><?php _e('תקציב חדש', 'budgex'); ?></a>
    </h1>

    <div class="budgex-dashboard-stats">
        <div class="stat-box">
            <h3><?php _e('סך התקציבים', 'budgex'); ?></h3>
            <div class="stat-number"><?php echo count($budgets); ?></div>
        </div>
        
        <?php 
        $total_available = 0;
        $total_spent = 0;
        foreach ($budgets as $budget) {
            $budget_calc = $calculator->calculate_remaining_budget($budget->id);
            $total_available += $budget_calc['total_available'];
            $total_spent += $budget_calc['total_spent'];
        }
        ?>
        
        <div class="stat-box">
            <h3><?php _e('סך התקציב הזמין', 'budgex'); ?></h3>
            <div class="stat-number currency"><?php echo $calculator->format_currency($total_available); ?></div>
        </div>
        
        <div class="stat-box">
            <h3><?php _e('סך ההוצאות', 'budgex'); ?></h3>
            <div class="stat-number currency spent"><?php echo $calculator->format_currency($total_spent); ?></div>
        </div>
        
        <div class="stat-box">
            <h3><?php _e('יתרה', 'budgex'); ?></h3>
            <div class="stat-number currency <?php echo ($total_available - $total_spent) < 0 ? 'negative' : 'positive'; ?>">
                <?php echo $calculator->format_currency($total_available - $total_spent); ?>
            </div>
        </div>
    </div>

    <div class="budgex-budgets-section">
        <h2><?php _e('התקציבים שלך', 'budgex'); ?></h2>
        
        <?php if (!empty($budgets)): ?>
            <div class="budgets-grid">
                <?php foreach ($budgets as $budget): 
                    $budget_calc = $calculator->calculate_remaining_budget($budget->id);
                    $role = isset($budget->user_role) ? $budget->user_role : 'owner';
                ?>
                    <div class="budget-card" data-budget-id="<?php echo $budget->id; ?>">
                        <div class="budget-card-header">
                            <h3><?php echo esc_html($budget->budget_name); ?></h3>
                            <span class="budget-role"><?php 
                                echo $role === 'owner' ? __('בעלים', 'budgex') : 
                                    ($role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                            ?></span>
                        </div>
                        
                        <div class="budget-card-content">
                            <div class="budget-info-row">
                                <span class="label"><?php _e('תקציב חודשי:', 'budgex'); ?></span>
                                <span class="value"><?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?></span>
                            </div>
                            
                            <div class="budget-info-row">
                                <span class="label"><?php _e('תאריך התחלה:', 'budgex'); ?></span>
                                <span class="value"><?php echo date('d/m/Y', strtotime($budget->start_date)); ?></span>
                            </div>
                            
                            <div class="budget-info-row">
                                <span class="label"><?php _e('סך התקציב:', 'budgex'); ?></span>
                                <span class="value total-budget"><?php echo $calculator->format_currency($budget_calc['total_available'], $budget->currency); ?></span>
                            </div>
                            
                            <div class="budget-info-row">
                                <span class="label"><?php _e('סך ההוצאות:', 'budgex'); ?></span>
                                <span class="value spent-budget"><?php echo $calculator->format_currency($budget_calc['total_spent'], $budget->currency); ?></span>
                            </div>
                            
                            <div class="budget-info-row remaining-row">
                                <span class="label"><?php _e('יתרה:', 'budgex'); ?></span>
                                <span class="value remaining-budget <?php echo $budget_calc['remaining'] < 0 ? 'negative' : 'positive'; ?>">
                                    <?php echo $calculator->format_currency($budget_calc['remaining'], $budget->currency); ?>
                                </span>
                            </div>
                            
                            <?php if ($budget_calc['remaining'] < 0): ?>
                                <div class="budget-warning">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('חריגה מהתקציב!', 'budgex'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="budget-card-actions">
                            <a href="<?php echo admin_url('admin.php?page=budgex-view&budget_id=' . $budget->id); ?>" class="button button-primary">
                                <?php _e('צפה בתקציב', 'budgex'); ?>
                            </a>
                            
                            <?php if ($role === 'owner' || $role === 'admin'): ?>
                                <a href="<?php echo admin_url('admin.php?page=budgex-edit&budget_id=' . $budget->id); ?>" class="button">
                                    <?php _e('עריכה', 'budgex'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($role === 'owner'): ?>
                                <button type="button" class="button button-link-delete delete-budget" data-budget-id="<?php echo $budget->id; ?>">
                                    <?php _e('מחק', 'budgex'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-budgets-message">
                <div class="dashicons dashicons-chart-line"></div>
                <h3><?php _e('עדיין אין לך תקציבים', 'budgex'); ?></h3>
                <p><?php _e('צור את התקציב הראשון שלך ותתחיל לנהל את הכספים שלך בצורה מקצועית', 'budgex'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=budgex-new'); ?>" class="button button-primary button-large">
                    <?php _e('צור תקציב ראשון', 'budgex'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="budgex-quick-actions">
        <h2><?php _e('פעולות מהירות', 'budgex'); ?></h2>
        <div class="quick-actions-grid">
            <a href="<?php echo admin_url('admin.php?page=budgex-new'); ?>" class="quick-action-card">
                <span class="dashicons dashicons-plus-alt"></span>
                <h4><?php _e('תקציב חדש', 'budgex'); ?></h4>
                <p><?php _e('צור תקציב חדש ותתחיל לעקוב אחר ההוצאות', 'budgex'); ?></p>
            </a>
            
            <a href="<?php echo get_permalink(get_page_by_title('Budgex')); ?>" class="quick-action-card">
                <span class="dashicons dashicons-visibility"></span>
                <h4><?php _e('תצוגה ציבורית', 'budgex'); ?></h4>
                <p><?php _e('עבור לתצוגה הציבורית של המערכת', 'budgex'); ?></p>
            </a>
            
            <div class="quick-action-card info-card">
                <span class="dashicons dashicons-info"></span>
                <h4><?php _e('עזרה', 'budgex'); ?></h4>
                <p><?php _e('מערכת Budgex מאפשרת לך לנהל תקציבים, להזמין משתמשים ולעקוב אחר הוצאות', 'budgex'); ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.budgex-admin-wrap {
    direction: rtl;
    font-family: 'Arial', sans-serif;
}

.budgex-dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-box {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    color: #fff;
}

.stat-box h3 {
    margin: 0 0 10px 0;
    color: #bb86fc;
    font-size: 14px;
    font-weight: normal;
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
    color: #fff;
}

.stat-number.currency {
    color: #4caf50;
}

.stat-number.spent {
    color: #ff5722;
}

.stat-number.negative {
    color: #f44336;
}

.stat-number.positive {
    color: #4caf50;
}

.budgets-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.budget-card {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.budget-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.budget-card-header {
    background: #2d2d2d;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.budget-card-header h3 {
    margin: 0;
    color: #bb86fc;
    font-size: 18px;
}

.budget-role {
    background: #bb86fc;
    color: #000;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.budget-card-content {
    padding: 20px;
    color: #fff;
}

.budget-info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #333;
}

.budget-info-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.budget-info-row .label {
    color: #ccc;
    font-size: 14px;
}

.budget-info-row .value {
    font-weight: bold;
    color: #fff;
}

.remaining-row .value.negative {
    color: #f44336;
}

.remaining-row .value.positive {
    color: #4caf50;
}

.budget-warning {
    background: #ff5722;
    color: #fff;
    padding: 8px 12px;
    border-radius: 4px;
    margin-top: 10px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.budget-card-actions {
    padding: 15px 20px;
    background: #2d2d2d;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.no-budgets-message {
    text-align: center;
    padding: 60px 20px;
    color: #ccc;
}

.no-budgets-message .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    color: #bb86fc;
    margin-bottom: 20px;
}

.no-budgets-message h3 {
    color: #fff;
    margin-bottom: 10px;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.quick-action-card {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 20px;
    text-decoration: none;
    color: #fff;
    transition: background-color 0.2s;
    display: block;
}

.quick-action-card:hover {
    background: #2d2d2d;
    color: #fff;
}

.quick-action-card .dashicons {
    font-size: 32px;
    width: 32px;
    height: 32px;
    color: #bb86fc;
    margin-bottom: 10px;
}

.quick-action-card h4 {
    margin: 0 0 10px 0;
    color: #bb86fc;
}

.quick-action-card p {
    margin: 0;
    color: #ccc;
    font-size: 14px;
}

.info-card:hover {
    background: #1e1e1e !important;
    cursor: default;
}
</style>