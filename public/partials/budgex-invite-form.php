<?php
/**
 * Budgex Invite Form Template
 * 
 * This file provides the form for inviting other users to view or manage budgets.
 * Integrates with the enhanced permission system and modern UI.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Get current user and budget information
$current_user = wp_get_current_user();
$budget_id = isset($_GET['budget_id']) ? intval($_GET['budget_id']) : 0;
$budget_name = '';

if ($budget_id) {
    global $wpdb;
    $budget = $wpdb->get_row(
        $wpdb->prepare("SELECT budget_name FROM {$wpdb->prefix}budgex_budgets WHERE id = %d", $budget_id)
    );
    $budget_name = $budget ? $budget->budget_name : '';
}

?>

<div class="budgex-container">
    <div class="budgex-header">
        <h1 class="budgex-title"><?php _e( 'הזמן משתמשים', 'budgex' ); ?></h1>
        <?php if ($budget_name): ?>
        <p class="budgex-subtitle">
            <?php printf( __( 'הזמן משתמשים לתקציב: %s', 'budgex' ), esc_html($budget_name) ); ?>
        </p>
        <?php endif; ?>
    </div>

    <?php if ($budget_id): ?>
    <form id="invite-form" class="budgex-form" method="post" action="">
        <h3><?php _e( 'הזמן משתמש חדש', 'budgex' ); ?></h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="invite_email" class="form-label">
                    <?php _e( 'כתובת אימייל:', 'budgex' ); ?> *
                </label>
                <input 
                    type="email" 
                    id="invite_email" 
                    name="invite_email" 
                    class="form-input" 
                    placeholder="<?php _e( 'הכנס כתובת אימייל', 'budgex' ); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="invite_role" class="form-label">
                    <?php _e( 'תפקיד:', 'budgex' ); ?> *
                </label>
                <select id="invite_role" name="invite_role" class="form-select" required>
                    <option value=""><?php _e( 'בחר תפקיד', 'budgex' ); ?></option>
                    <option value="viewer"><?php _e( 'צופה', 'budgex' ); ?></option>
                    <option value="admin"><?php _e( 'מנהל', 'budgex' ); ?></option>
                </select>
                <small class="form-help">
                    <?php _e( 'צופה: יכול לראות את התקציב והתוצאות. מנהל: יכול לערוך ולנהל את התקציב.', 'budgex' ); ?>
                </small>
            </div>
        </div>

        <div class="form-group">
            <label for="invite_message" class="form-label">
                <?php _e( 'הודעה אישית (אופציונלי):', 'budgex' ); ?>
            </label>
            <textarea 
                id="invite_message" 
                name="invite_message" 
                class="form-textarea" 
                rows="3"
                placeholder="<?php _e( 'הוסף הודעה אישית להזמנה...', 'budgex' ); ?>"
            ></textarea>
        </div>

        <input type="hidden" name="budget_id" value="<?php echo esc_attr($budget_id); ?>">
        <input type="hidden" name="action" value="budgex_invite_user">
        <?php wp_nonce_field( 'budgex_invite_user_nonce', 'budgex_invite_user_nonce_field' ); ?>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                📧 <?php _e( 'שלח הזמנה', 'budgex' ); ?>
            </button>
            <a href="<?php echo esc_url(add_query_arg('id', $budget_id, home_url('/budget-page/'))); ?>" class="btn btn-secondary">
                ↩️ <?php _e( 'חזור לתקציב', 'budgex' ); ?>
            </a>
        </div>
    </form>

    <?php
    // Display existing invitations and permissions
    global $wpdb;
    
    // Get existing permissions
    $permissions = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT s.*, u.display_name, u.user_email 
             FROM {$wpdb->prefix}budgex_budget_shares s
             LEFT JOIN {$wpdb->users} u ON s.user_id = u.ID
             WHERE s.budget_id = %d
             ORDER BY s.role DESC, u.display_name ASC",
            $budget_id
        )
    );

    // Get pending invitations
    $invitations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}budgex_invitations 
             WHERE budget_id = %d AND status = 'pending'
             ORDER BY created_at DESC",
            $budget_id
        )
    );

    if ($permissions || $invitations): ?>
    <div class="budgex-table">
        <h3><?php _e( 'משתמשים והרשאות', 'budgex' ); ?></h3>
        
        <?php if ($permissions): ?>
        <h4><?php _e( 'משתמשים פעילים', 'budgex' ); ?></h4>
        <table>
            <thead>
                <tr>
                    <th><?php _e( 'משתמש', 'budgex' ); ?></th>
                    <th><?php _e( 'אימייל', 'budgex' ); ?></th>
                    <th><?php _e( 'תפקיד', 'budgex' ); ?></th>
                    <th><?php _e( 'תאריך הצטרפות', 'budgex' ); ?></th>
                    <th><?php _e( 'פעולות', 'budgex' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($permissions as $permission): ?>
                <tr>
                    <td><?php echo esc_html($permission->display_name ?: __('משתמש לא זמין', 'budgex')); ?></td>
                    <td><?php echo esc_html($permission->user_email ?: __('לא זמין', 'budgex')); ?></td>
                    <td>
                        <span class="budget-role">
                            <?php echo $permission->role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex'); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($permission->created_at))); ?></td>
                    <td>
                        <?php if ($permission->user_id != $current_user->ID): ?>
                        <button type="button" class="btn btn-sm btn-danger remove-permission" 
                                data-permission-id="<?php echo esc_attr($permission->id); ?>">
                            🗑️ <?php _e( 'הסר', 'budgex' ); ?>
                        </button>
                        <?php else: ?>
                        <span class="text-muted"><?php _e( 'בעלים', 'budgex' ); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if ($invitations): ?>
        <h4><?php _e( 'הזמנות ממתינות', 'budgex' ); ?></h4>
        <table>
            <thead>
                <tr>
                    <th><?php _e( 'אימייל', 'budgex' ); ?></th>
                    <th><?php _e( 'תפקיד', 'budgex' ); ?></th>
                    <th><?php _e( 'תאריך שליחה', 'budgex' ); ?></th>
                    <th><?php _e( 'פעולות', 'budgex' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invitations as $invitation): ?>
                <tr>
                    <td><?php echo esc_html($invitation->email); ?></td>
                    <td>
                        <span class="budget-role">
                            <?php echo $invitation->role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex'); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($invitation->created_at))); ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-secondary resend-invitation" 
                                data-invitation-id="<?php echo esc_attr($invitation->id); ?>">
                            📧 <?php _e( 'שלח שוב', 'budgex' ); ?>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger cancel-invitation" 
                                data-invitation-id="<?php echo esc_attr($invitation->id); ?>">
                            ❌ <?php _e( 'בטל', 'budgex' ); ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="no-access-container">
        <div class="no-access-icon">⚠️</div>
        <h2 class="no-access-title"><?php _e( 'תקציב לא נמצא', 'budgex' ); ?></h2>
        <p class="no-access-message">
            <?php _e( 'לא נמצא תקציב להזמנת משתמשים. אנא בחר תקציב קיים או צור תקציב חדש.', 'budgex' ); ?>
        </p>
        <a href="<?php echo esc_url(home_url('/dashboard/')); ?>" class="btn btn-primary">
            🏠 <?php _e( 'חזור לדשבורד', 'budgex' ); ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.form-help {
    color: var(--text-muted);
    font-size: var(--font-size-sm);
    margin-top: var(--spacing-xs);
    display: block;
}

.form-actions {
    display: flex;
    gap: var(--spacing-md);
    justify-content: flex-start;
    margin-top: var(--spacing-xl);
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Handle invite form submission
    $('#invite-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const formData = new FormData(form[0]);
        formData.append('action', 'budgex_send_invitation');
        formData.append('nonce', '<?php echo wp_create_nonce('budgex_invite_nonce'); ?>');

        // Validate form
        let isValid = true;
        form.find('.form-input[required], .form-select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('error');
                isValid = false;
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            BudgexPublic.showNotification('אנא מלא את כל השדות הנדרשים', 'error');
            return;
        }

        // Show loading
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('🔄 שולח הזמנה...');

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    BudgexPublic.showNotification('ההזמנה נשלחה בהצלחה!', 'success');
                    form[0].reset();
                    // Reload page to show updated invitations
                    setTimeout(() => location.reload(), 1500);
                } else {
                    BudgexPublic.showNotification(response.data || 'שגיאה בשליחת ההזמנה', 'error');
                }
            },
            error: function() {
                BudgexPublic.showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('📧 שלח הזמנה');
            }
        });
    });

    // Handle permission removal
    $('.remove-permission').on('click', function() {
        const permissionId = $(this).data('permission-id');
        
        if (!confirm('האם אתה בטוח שברצונך להסיר את המשתמש מהתקציב?')) {
            return;
        }

        $(this).prop('disabled', true);

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_remove_permission',
                permission_id: permissionId,
                nonce: '<?php echo wp_create_nonce('budgex_permission_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    BudgexPublic.showNotification('המשתמש הוסר בהצלחה', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    BudgexPublic.showNotification(response.data || 'שגיאה בהסרת המשתמש', 'error');
                }
            },
            error: function() {
                BudgexPublic.showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                $(this).prop('disabled', false);
            }
        });
    });

    // Handle invitation cancellation
    $('.cancel-invitation').on('click', function() {
        const invitationId = $(this).data('invitation-id');
        
        if (!confirm('האם אתה בטוח שברצונך לבטל את ההזמנה?')) {
            return;
        }

        $(this).prop('disabled', true);

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_cancel_invitation',
                invitation_id: invitationId,
                nonce: '<?php echo wp_create_nonce('budgex_invitation_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    BudgexPublic.showNotification('ההזמנה בוטלה בהצלחה', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    BudgexPublic.showNotification(response.data || 'שגיאה בביטול ההזמנה', 'error');
                }
            },
            error: function() {
                BudgexPublic.showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                $(this).prop('disabled', false);
            }
        });
    });

    // Handle invitation resend
    $('.resend-invitation').on('click', function() {
        const invitationId = $(this).data('invitation-id');
        
        $(this).prop('disabled', true).html('🔄 שולח...');

        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_resend_invitation',
                invitation_id: invitationId,
                nonce: '<?php echo wp_create_nonce('budgex_invitation_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    BudgexPublic.showNotification('ההזמנה נשלחה שוב בהצלחה', 'success');
                } else {
                    BudgexPublic.showNotification(response.data || 'שגיאה בשליחת ההזמנה', 'error');
                }
            },
            error: function() {
                BudgexPublic.showNotification('שגיאה בחיבור לשרת', 'error');
            },
            complete: function() {
                $(this).prop('disabled', false).html('📧 שלח שוב');
            }
        });
    });
});
</script>