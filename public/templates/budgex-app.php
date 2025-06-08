<?php
/**
 * Template for Budgex Frontend App
 * This template loads when users access /budgex
 */

get_header(); ?>

<div id="budgex-app-container" class="budgex-frontend-app">
    <style>
        /* Ensure Budgex styles take precedence */
        .budgex-frontend-app {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        .budgex-frontend-app * {
            box-sizing: border-box;
        }
        
        /* Reset any theme styles that might interfere */
        .budgex-frontend-app .card,
        .budgex-frontend-app .button,
        .budgex-frontend-app .btn {
            font-family: inherit;
            direction: rtl;
        }
        
        .budgex-main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 500px;
        }
        
        .budgex-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .budgex-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .budgex-header p {
            margin: 10px 0 0 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .budgex-nav {
            background: #f8f9fa;
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .budgex-nav a {
            background: #007cba;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .budgex-nav a:hover {
            background: #005a87;
        }
        
        .budgex-nav a.current {
            background: #46b450;
        }
        
        @media (max-width: 768px) {
            .budgex-header {
                padding: 20px 15px;
            }
            
            .budgex-header h1 {
                font-size: 2rem;
            }
            
            .budgex-nav {
                padding: 10px 15px;
            }
            
            .budgex-nav a {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>

    <?php if (is_user_logged_in()): ?>
        <div class="budgex-header">
            <h1>💰 Budgex - ניהול תקציב</h1>
            <p>ברוכים הבאים למערכת ניהול התקציב המתקדמת שלכם</p>
        </div>
        
        <div class="budgex-nav">
            <?php 
            $current_page = get_query_var('budgex_page', 'dashboard');
            $budget_id = get_query_var('budget_id');
            ?>
            <a href="<?php echo home_url('/budgex/'); ?>" 
               class="<?php echo $current_page === 'dashboard' ? 'current' : ''; ?>">
                🏠 דשבורד ראשי
            </a>
            
            <?php if ($budget_id): ?>
                <a href="<?php echo home_url('/budgex/budget/' . $budget_id . '/'); ?>" class="current">
                    📊 תקציב #<?php echo $budget_id; ?>
                </a>
            <?php endif; ?>
              <button type="button" id="advanced-settings-btn">
                ⚙️ הגדרות מתקדמות
            </button>
            
            <a href="<?php echo wp_logout_url(home_url('/budgex/')); ?>" style="margin-right: auto;">
                🚪 התנתק
            </a>
        </div>
    <?php endif; ?>

    <div class="budgex-main-content">
        <?php
        // Display the Budgex app content
        $budgex = new Budgex();
        echo $budgex->display_budgex_app();
        ?>
    </div>
</div>

<script>
// Ensure Budgex JavaScript is loaded
jQuery(document).ready(function($) {
    // Initialize any additional frontend functionality
    console.log('Budgex Frontend App Loaded');
    
    // Handle budget navigation
    $('.budget-card').on('click', function(e) {
        e.preventDefault();
        const budgetId = $(this).data('budget-id');
        if (budgetId) {
            window.location.href = '/budgex/budget/' + budgetId + '/';
        }
    });
    
    // Add smooth scrolling for better UX
    $('html').css('scroll-behavior', 'smooth');
});
</script>

<?php get_footer(); ?>
