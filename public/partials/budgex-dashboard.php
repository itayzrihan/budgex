<?php
// This file contains the HTML structure for the user dashboard.

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Initialize classes
$database = new Budgex_Database();
$calculator = new Budgex_Budget_Calculator();
$permissions = new Budgex_Permissions();

// Check if we're in frontend mode
$is_frontend = !is_admin() && !wp_doing_ajax();

// Fetch user budgets from the database
$budgets = $database->get_user_budgets($user_id);
$shared_budgets = $database->get_shared_budgets($user_id);
$pending_invitations = $database->get_pending_invitations($user_id);

// Calculate statistics
$total_budgets = count($budgets) + count($shared_budgets);
$total_available = 0;
$total_spent = 0;

foreach (array_merge($budgets, $shared_budgets) as $budget) {
    $calculation = $calculator->calculate_remaining_budget($budget->id);
    $total_available += $calculation['total_available'];
    $total_spent += $calculation['total_spent'];
}
?>

<div class="budgex-public-dashboard" dir="rtl">
    <div class="dashboard-header">
        <h1><?php _e('לוח בקרה - Budgex', 'budgex'); ?></h1>
        <p class="dashboard-welcome"><?php printf(__('שלום %s, כאן תוכל לנהל את כל התקציבים שלך', 'budgex'), $current_user->display_name); ?></p>
    </div>

    <!-- Dashboard Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-chart-pie"></span>
            </div>
            <div class="stat-content">
                <h3><?php _e('מספר תקציבים', 'budgex'); ?></h3>
                <div class="stat-number"><?php echo $total_budgets; ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-money-alt"></span>
            </div>
            <div class="stat-content">
                <h3><?php _e('סך התקציב הזמין', 'budgex'); ?></h3>
                <div class="stat-number currency positive"><?php echo $calculator->format_currency($total_available); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-cart"></span>
            </div>
            <div class="stat-content">
                <h3><?php _e('סך ההוצאות', 'budgex'); ?></h3>
                <div class="stat-number currency spent"><?php echo $calculator->format_currency($total_spent); ?></div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-analytics"></span>
            </div>
            <div class="stat-content">
                <h3><?php _e('יתרה', 'budgex'); ?></h3>
                <div class="stat-number currency <?php echo ($total_available - $total_spent) >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $calculator->format_currency($total_available - $total_spent); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2><?php _e('פעולות מהירות', 'budgex'); ?></h2>
        <div class="actions-grid">
            <button type="button" class="action-card" id="create-new-budget-btn">
                <span class="dashicons dashicons-plus-alt"></span>
                <h4><?php _e('תקציב חדש', 'budgex'); ?></h4>
                <p><?php _e('צור תקציב חדש ותתחיל לעקוב אחר ההוצאות', 'budgex'); ?></p>
            </button>
            
            <button type="button" class="action-card" id="view-all-budgets-btn">
                <span class="dashicons dashicons-chart-line"></span>
                <h4><?php _e('כל התקציבים', 'budgex'); ?></h4>
                <p><?php _e('צפה בכל התקציבים שלך במקום אחד', 'budgex'); ?></p>
            </button>
        </div>
    </div>

    <!-- Budget List -->
    <div class="budgets-section">
        <h2><?php _e('התקציבים שלך', 'budgex'); ?></h2>
        
        <?php if (!empty($budgets) || !empty($shared_budgets)): ?>
            <div class="budgets-grid">
                <?php foreach (array_merge($budgets, $shared_budgets) as $budget): 
                    $calculation = $calculator->calculate_remaining_budget($budget->id);
                    $user_role = $permissions->get_user_role_on_budget($budget->id, $user_id);
                    if (!$user_role) $user_role = 'owner'; // Fallback for budget owner
                ?>
                    <div class="budget-card">
                        <div class="budget-card-header">
                            <h3><?php echo esc_html($budget->budget_name); ?></h3>
                            <span class="budget-role <?php echo $user_role; ?>">
                                <?php 
                                echo $user_role === 'owner' ? __('בעלים', 'budgex') : 
                                    ($user_role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                                ?>
                            </span>
                        </div>
                        
                        <div class="budget-card-content">
                            <div class="budget-info">
                                <div class="info-row">
                                    <span class="label"><?php _e('תקציב חודשי:', 'budgex'); ?></span>
                                    <span class="value"><?php echo $calculator->format_currency($budget->monthly_budget, $budget->currency); ?></span>
                                </div>
                                
                                <div class="info-row">
                                    <span class="label"><?php _e('סך התקציב:', 'budgex'); ?></span>
                                    <span class="value total"><?php echo $calculator->format_currency($calculation['total_available'], $budget->currency); ?></span>
                                </div>
                                
                                <div class="info-row">
                                    <span class="label"><?php _e('הוצאות:', 'budgex'); ?></span>
                                    <span class="value spent"><?php echo $calculator->format_currency($calculation['total_spent'], $budget->currency); ?></span>
                                </div>
                                
                                <div class="info-row remaining">
                                    <span class="label"><?php _e('יתרה:', 'budgex'); ?></span>
                                    <span class="value <?php echo ($calculation['remaining']) >= 0 ? 'positive' : 'negative'; ?>">
                                        <?php echo $calculator->format_currency($calculation['remaining'], $budget->currency); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($calculation['remaining'] < 0): ?>
                                <div class="budget-warning">
                                    <span class="dashicons dashicons-warning"></span>
                                    <?php _e('חריגה מהתקציב!', 'budgex'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="budget-card-actions">
                            <?php if ($is_frontend): ?>
                                <a href="<?php echo home_url('/budgex/budget/' . $budget->id . '/'); ?>" class="button primary view-budget" data-budget-id="<?php echo $budget->id; ?>">
                                    <?php _e('צפה בתקציב', 'budgex'); ?>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo home_url('/budgex/budget/' . $budget->id . '/'); ?>" class="button primary">
                                    <?php _e('צפה בתקציב', 'budgex'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($user_role === 'owner' || $user_role === 'admin'): ?>
                                <button type="button" class="button secondary manage-budget-btn" data-budget-id="<?php echo esc_attr($budget->id); ?>" data-budget-name="<?php echo esc_attr($budget->budget_name); ?>">
                                    <?php _e('ניהול מתקדם', 'budgex'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-budgets-message">
                <div class="no-budgets-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <h3><?php _e('עדיין אין לך תקציבים', 'budgex'); ?></h3>
                <p><?php _e('צור את התקציב הראשון שלך ותתחיל לנהל את הכספים שלך בצורה מקצועית', 'budgex'); ?></p>
                <button type="button" class="button primary large" id="create-first-budget-btn">
                    <?php _e('צור תקציב ראשון', 'budgex'); ?>
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .budgex-dashboard {
        background-color: #1e1e1e;
        color: #ffffff;
        padding: 20px;
        border-radius: 8px;
    }
    .budgex-budget-list h2,
    .budgex-invite-section h2,
    .budgex-add-budget-section h2 {
        color: #f39c12;
    }
    .budgex-budget-list ul {
        list-style-type: none;
        padding: 0;
    }
    .budgex-budget-list li {
        margin-bottom: 15px;
        padding: 10px;
        background-color: #2c2c2c;
        border-radius: 5px;
    }
    .budgex-budget-list a {
        color: #3498db;
    }
</style>

<!-- Create New Budget Modal -->
<div id="create-budget-modal" class="budgex-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php _e('צור תקציב חדש', 'budgex'); ?></h2>
            <button type="button" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="create-budget-form">
                <div class="form-field">
                    <label for="budget_name"><?php _e('שם התקציב', 'budgex'); ?></label>
                    <input type="text" id="budget_name" name="budget_name" required>
                </div>
                
                <div class="form-field">
                    <label for="monthly_budget"><?php _e('תקציב חודשי', 'budgex'); ?></label>
                    <input type="number" id="monthly_budget" name="monthly_budget" step="0.01" min="0" required>
                </div>
                
                <div class="form-field">
                    <label for="currency"><?php _e('מטבע', 'budgex'); ?></label>
                    <select id="currency" name="currency" required>
                        <option value="ILS">₪ שקל</option>
                        <option value="USD">$ דולר</option>
                        <option value="EUR">€ יורו</option>
                    </select>
                </div>
                
                <div class="form-field">
                    <label for="start_date"><?php _e('תאריך התחלה', 'budgex'); ?></label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="button button-primary"><?php _e('צור תקציב', 'budgex'); ?></button>
                    <button type="button" class="button button-secondary modal-close"><?php _e('ביטול', 'budgex'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Open create budget modal
    $('#create-new-budget-btn, #create-first-budget-btn').on('click', function() {
        $('#create-budget-modal').show();
    });
    
    // Close modal
    $('.modal-close').on('click', function() {
        $('.budgex-modal').hide();
    });
    
    // Close modal when clicking outside
    $('.budgex-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });
    
    // Handle create budget form submission
    $('#create-budget-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'budgex_create_budget_frontend',
            budget_name: $('#budget_name').val(),
            monthly_budget: $('#monthly_budget').val(),
            currency: $('#currency').val(),
            start_date: $('#start_date').val(),
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
                alert('שגיאה ביצירת התקציב');
            }
        });
    });
    
    // Handle manage budget button
    $('.manage-budget-btn').on('click', function() {
        var $button = $(this);
        var budgetId = $button.data('budget-id');
        var budgetName = $button.data('budget-name');
        
        // Enhanced debug logging
        console.log('=== Manage Budget Button Clicked ===');
        console.log('Button element:', $button[0]);
        console.log('Button HTML:', $button[0].outerHTML);
        console.log('All data attributes:', $button.data());
        console.log('Budget ID from data():', budgetId);
        console.log('Budget ID from attr():', $button.attr('data-budget-id'));
        console.log('Budget Name:', budgetName);
        console.log('budgex_ajax object:', budgex_ajax);
        
        // Validate we have the required data
        if (!budgetId) {
            console.error('❌ Budget ID is missing!');
            console.log('Available data attributes:', Object.keys($button.data()));
            console.log('Button raw attributes:');
            Array.from($button[0].attributes).forEach(attr => {
                console.log(`  ${attr.name}: ${attr.value}`);
            });
            alert('שגיאה: מזהה התקציב חסר');
            return;
        }
        
        if (!budgex_ajax || !budgex_ajax.budget_url) {
            console.error('❌ Budgex AJAX configuration is missing');
            console.log('budgex_ajax:', budgex_ajax);
            alert('שגיאה: הגדרות JavaScript חסרות');
            return;
        }
        
        var targetUrl = budgex_ajax.budget_url + budgetId + '/#advanced-management-panel';
        console.log('✅ Target URL constructed:', targetUrl);
        console.log('Navigating now...');
        
        window.location.href = targetUrl;
    });
    
    // Handle view all budgets button
    $('#view-all-budgets-btn').on('click', function() {
        $('html, body').animate({
            scrollTop: $('.budgets-section').offset().top - 20
        }, 500);
    });
});
</script>