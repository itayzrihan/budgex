<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="wrap budgex-form-wrap" dir="rtl">
    <h1><?php _e( 'יצירת תקציב חדש', 'budgex' ); ?></h1>
    
    <div class="budgex-form-container">
        <div class="form-section">
            <h2><?php _e( 'פרטי התקציב', 'budgex' ); ?></h2>
            <p class="description"><?php _e( 'הזן את פרטי התקציב החדש. המערכת תחשב אוטומטית את סך התקציב הזמין בהתבסס על התאריך שתבחר.', 'budgex' ); ?></p>
            
            <form method="post" action="" class="budgex-budget-form">
                <?php wp_nonce_field( 'budgex_create_budget', 'budgex_nonce' ); ?>
                
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="budget_name"><?php _e( 'שם התקציב', 'budgex' ); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="text" id="budget_name" name="budget_name" class="regular-text" required 
                                       placeholder="<?php _e( 'לדוגמה: תקציב בית, פרויקט חדש, וכו\'', 'budgex' ); ?>">
                                <p class="description"><?php _e( 'בחר שם מתאר לתקציב שלך', 'budgex' ); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="monthly_budget"><?php _e( 'תקציב חודשי', 'budgex' ); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <div class="input-group">
                                    <input type="number" id="monthly_budget" name="monthly_budget" 
                                           class="regular-text currency-input" step="0.01" min="0" required
                                           placeholder="0.00">
                                    <select name="currency" id="currency" class="currency-select">
                                        <option value="ILS"><?php _e( 'שקל ישראלי (₪)', 'budgex' ); ?></option>
                                        <option value="USD"><?php _e( 'דולר אמריקאי ($)', 'budgex' ); ?></option>
                                    </select>
                                </div>
                                <p class="description"><?php _e( 'הסכום שמתווסף לתקציב מדי חודש', 'budgex' ); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="start_date"><?php _e( 'תאריך התחלת הפרויקט', 'budgex' ); ?> <span class="required">*</span></label>
                            </th>
                            <td>
                                <input type="date" id="start_date" name="start_date" class="regular-text datepicker" required
                                       max="<?php echo date('Y-m-d'); ?>">
                                <p class="description"><?php _e( 'התאריך בו התחלת לצבור תקציב לפרויקט זה', 'budgex' ); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="budget-preview">
                    <h3><?php _e( 'תצוגה מקדימה של התקציב', 'budgex' ); ?></h3>
                    <div class="preview-content">
                        <div class="preview-item">
                            <span class="label"><?php _e( 'תקציב חודשי:', 'budgex' ); ?></span>
                            <span class="value" id="preview-monthly">0 ש"ח</span>
                        </div>
                        <div class="preview-item">
                            <span class="label"><?php _e( 'חודשים שעברו:', 'budgex' ); ?></span>
                            <span class="value" id="preview-months">0</span>
                        </div>
                        <div class="preview-item total">
                            <span class="label"><?php _e( 'סך התקציב הזמין:', 'budgex' ); ?></span>
                            <span class="value" id="preview-total">0 ש"ח</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <input type="submit" name="budgex_create_budget" class="button button-primary button-large" 
                           value="<?php _e( 'צור תקציב', 'budgex' ); ?>">
                    <a href="<?php echo admin_url('admin.php?page=budgex'); ?>" class="button button-secondary">
                        <?php _e( 'ביטול', 'budgex' ); ?>
                    </a>
                </div>
            </form>
        </div>
        
        <div class="help-section">
            <h3><?php _e( 'עזרה ומידע', 'budgex' ); ?></h3>
            <div class="help-box">
                <h4><?php _e( 'איך זה עובד?', 'budgex' ); ?></h4>
                <p><?php _e( 'המערכת מחשבת את התקציב הזמין בהתבסס על:', 'budgex' ); ?></p>
                <ul>
                    <li><?php _e( 'התקציב החודשי שהגדרת', 'budgex' ); ?></li>
                    <li><?php _e( 'מספר החודשים שעברו מתאריך ההתחלה', 'budgex' ); ?></li>
                    <li><?php _e( 'תקציב נוסף שתוסיף ידנית', 'budgex' ); ?></li>
                </ul>
            </div>
            
            <div class="help-box">
                <h4><?php _e( 'דוגמה', 'budgex' ); ?></h4>
                <p><?php _e( 'אם התחלת פרויקט לפני 3 חודשים עם תקציב חודשי של 1000 ש"ח, התקציב הזמין יהיה 3000 ש"ח.', 'budgex' ); ?></p>
            </div>
            
            <div class="help-box">
                <h4><?php _e( 'טיפים', 'budgex' ); ?></h4>
                <ul>
                    <li><?php _e( 'בחר שם תקציב מתאר כדי לזהות אותו בקלות', 'budgex' ); ?></li>
                    <li><?php _e( 'תוכל להזמין משתמשים אחרים לצפות או לנהל את התקציב', 'budgex' ); ?></li>
                    <li><?php _e( 'ניתן לעדכן את הפרטים בכל עת אחרי יצירת התקציב', 'budgex' ); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    function updatePreview() {
        var monthlyBudget = parseFloat($('#monthly_budget').val()) || 0;
        var startDate = $('#start_date').val();
        var currency = $('#currency').val();
        var currencySymbol = currency === 'ILS' ? 'ש"ח' : '$';
        
        $('#preview-monthly').text(monthlyBudget.toFixed(2) + ' ' + currencySymbol);
        
        if (startDate) {
            var start = new Date(startDate);
            var current = new Date();
            
            var months = (current.getFullYear() - start.getFullYear()) * 12;
            months += current.getMonth() - start.getMonth();
            
            // Add partial month if current day is past start day
            if (current.getDate() >= start.getDate()) {
                months++;
            }
            
            months = Math.max(0, months);
            var totalBudget = monthlyBudget * months;
            
            $('#preview-months').text(months);
            $('#preview-total').text(totalBudget.toFixed(2) + ' ' + currencySymbol);
        } else {
            $('#preview-months').text('0');
            $('#preview-total').text('0 ' + currencySymbol);
        }
    }
    
    $('#monthly_budget, #start_date, #currency').on('input change', updatePreview);
    
    // Initial preview update
    updatePreview();
    
    // Form validation
    $('.budgex-budget-form').on('submit', function(e) {
        var monthlyBudget = parseFloat($('#monthly_budget').val());
        var budgetName = $('#budget_name').val().trim();
        var startDate = $('#start_date').val();
        
        if (!budgetName) {
            alert('נא להזין שם תקציב');
            e.preventDefault();
            return false;
        }
        
        if (monthlyBudget <= 0) {
            alert('נא להזין תקציב חודשי תקף');
            e.preventDefault();
            return false;
        }
        
        if (!startDate) {
            alert('נא לבחור תאריך התחלה');
            e.preventDefault();
            return false;
        }
        
        var startDateObj = new Date(startDate);
        var today = new Date();
        if (startDateObj > today) {
            alert('תאריך ההתחלה לא יכול להיות בעתיד');
            e.preventDefault();
            return false;
        }
    });
});
</script>

<style>
.budgex-form-wrap {
    direction: rtl;
    font-family: 'Arial', sans-serif;
}

.budgex-form-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.form-section {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 30px;
    color: #fff;
}

.form-section h2 {
    margin-top: 0;
    color: #bb86fc;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

.form-section .description {
    color: #ccc;
    margin-bottom: 20px;
}

.form-table th {
    color: #bb86fc;
    font-weight: bold;
    padding: 15px 10px 15px 0;
    width: 200px;
}

.form-table td {
    padding: 15px 0;
}

.form-table input[type="text"],
.form-table input[type="number"],
.form-table input[type="date"],
.form-table select {
    background: #2d2d2d;
    border: 1px solid #555;
    border-radius: 4px;
    color: #fff;
    padding: 8px 12px;
    width: 100%;
    max-width: 300px;
}

.form-table input:focus,
.form-table select:focus {
    border-color: #bb86fc;
    outline: none;
    box-shadow: 0 0 0 1px #bb86fc;
}

.input-group {
    display: flex;
    gap: 10px;
}

.input-group input {
    flex: 1;
}

.currency-select {
    width: auto !important;
    min-width: 150px;
}

.required {
    color: #ff5722;
}

.description {
    color: #ccc !important;
    font-size: 13px;
    margin: 5px 0 0 0 !important;
}

.budget-preview {
    background: #2d2d2d;
    border: 1px solid #555;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.budget-preview h3 {
    margin-top: 0;
    color: #bb86fc;
}

.preview-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.preview-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #555;
}

.preview-item:last-child {
    border-bottom: none;
}

.preview-item.total {
    font-weight: bold;
    color: #4caf50;
    border-top: 2px solid #555;
    padding-top: 12px;
    margin-top: 8px;
}

.preview-item .label {
    color: #ccc;
}

.preview-item .value {
    color: #fff;
    font-weight: bold;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #333;
    display: flex;
    gap: 15px;
}

.help-section {
    background: #1e1e1e;
    border: 1px solid #333;
    border-radius: 8px;
    padding: 20px;
    color: #fff;
    height: fit-content;
}

.help-section h3 {
    margin-top: 0;
    color: #bb86fc;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

.help-box {
    background: #2d2d2d;
    border: 1px solid #555;
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 15px;
}

.help-box:last-child {
    margin-bottom: 0;
}

.help-box h4 {
    margin: 0 0 10px 0;
    color: #bb86fc;
    font-size: 14px;
}

.help-box p,
.help-box li {
    color: #ccc;
    font-size: 13px;
    line-height: 1.5;
}

.help-box ul {
    margin: 10px 0 0 20px;
    padding: 0;
}

.help-box li {
    margin-bottom: 5px;
}

@media (max-width: 768px) {
    .budgex-form-container {
        grid-template-columns: 1fr;
    }
    
    .input-group {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>