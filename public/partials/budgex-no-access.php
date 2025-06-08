<?php
// This file displays when users don't have access to a budget

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="budgex-no-access" dir="rtl">
    <div class="no-access-container">
        <div class="no-access-icon">
            <span class="dashicons dashicons-lock"></span>
        </div>
        
        <h2><?php _e('אין הרשאה לצפות בתקציב זה', 'budgex'); ?></h2>
        
        <p><?php _e('התקציב שאתה מנסה לגשת אליו הוא פרטי או שאין לך הרשאות מתאימות לצפות בו.', 'budgex'); ?></p>
        
        <div class="no-access-actions">            <a href="<?php echo home_url('/budgex/'); ?>" class="button primary">
                <span class="dashicons dashicons-arrow-right-alt"></span>
                <?php _e('חזור לתקציבים שלך', 'budgex'); ?>
            </a>
            
            <button type="button" class="button secondary" id="create-new-budget-btn">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e('צור תקציב חדש', 'budgex'); ?>
            </button>
        </div>
        
        <div class="contact-info">
            <p><small><?php _e('אם אתה חושב שזו שגיאה, פנה לבעל התקציב או למנהל המערכת.', 'budgex'); ?></small></p>
        </div>
    </div>
</div>

<style>
.budgex-no-access {
    padding: 60px 20px;
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.no-access-container {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 12px;
    padding: 40px;
    color: #fff;
}

.no-access-icon {
    margin-bottom: 20px;
}

.no-access-icon .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    color: #ff5722;
}

.budgex-no-access h2 {
    color: #fff;
    margin-bottom: 15px;
    font-size: 24px;
}

.budgex-no-access p {
    color: #ccc;
    line-height: 1.6;
    margin-bottom: 30px;
}

.no-access-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.button.primary {
    background: #bb86fc;
    color: #fff;
}

.button.primary:hover {
    background: #9b59b6;
    color: #fff;
}

.button.secondary {
    background: transparent;
    color: #bb86fc;
    border: 1px solid #bb86fc;
}

.button.secondary:hover {
    background: #bb86fc;
    color: #fff;
}

.contact-info {
    color: #888;
    font-size: 13px;
}

@media (max-width: 768px) {
    .budgex-no-access {
        padding: 40px 15px;
    }
    
    .no-access-container {
        padding: 30px 20px;
    }
    
    .no-access-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .button {
        width: 100%;
        max-width: 250px;
        justify-content: center;
    }
}
</style>
