jQuery(document).ready(function($) {
    // Handle outcome deletion
    $('.delete-outcome').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm(budgex_ajax.strings.confirm_delete)) {
            return;
        }
        
        var outcomeId = $(this).data('outcome-id');
        var $row = $(this).closest('tr');
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_delete_outcome',
                outcome_id: outcomeId,
                nonce: budgex_ajax.nonce
            },
            beforeSend: function() {
                $row.css('opacity', '0.5');
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        updateBudgetCalculation();
                    });
                    showNotice(response.data.message, 'success');
                } else {
                    $row.css('opacity', '1');
                    showNotice(response.data.message, 'error');
                }
            },
            error: function() {
                $row.css('opacity', '1');
                showNotice(budgex_ajax.strings.error, 'error');
            }
        });
    });

    // Handle additional budget form
    $('#add-additional-budget-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var amount = $form.find('input[name="additional_amount"]').val();
        var budgetId = $form.find('input[name="budget_id"]').val();
        
        if (!amount || amount <= 0) {
            showNotice('נא להזין סכום תקף', 'error');
            return;
        }
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_add_additional_budget',
                budget_id: budgetId,
                amount: amount,
                nonce: budgex_ajax.nonce
            },
            beforeSend: function() {
                $submitBtn.prop('disabled', true).val(budgex_ajax.strings.loading);
            },
            success: function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    $form.find('input[name="additional_amount"]').val('');
                    updateBudgetCalculation();
                } else {
                    showNotice(response.data.message, 'error');
                }
            },
            error: function() {
                showNotice(budgex_ajax.strings.error, 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).val('הוסף תקציב נוסף');
            }
        });
    });

    // Handle outcome form
    $('#add-outcome-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var formData = $form.serialize();
        
        // Validate form
        var amount = $form.find('input[name="outcome_amount"]').val();
        var description = $form.find('textarea[name="outcome_description"]').val();
        var name = $form.find('input[name="outcome_name"]').val();
        
        if (!amount || amount <= 0 || !description.trim() || !name.trim()) {
            showNotice('נא למלא את כל השדות הנדרשים', 'error');
            return;
        }
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=budgex_add_outcome&nonce=' + budgex_ajax.nonce,
            beforeSend: function() {
                $submitBtn.prop('disabled', true).val(budgex_ajax.strings.loading);
            },
            success: function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    $form[0].reset();
                    // Refresh outcomes list
                    location.reload();
                } else {
                    showNotice(response.data.message, 'error');
                }
            },
            error: function() {
                showNotice(budgex_ajax.strings.error, 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).val('הוסף הוצאה');
            }
        });
    });

    // Handle invitation form
    $('#invite-user-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('input[type="submit"]');
        var email = $form.find('input[name="invite_email"]').val();
        var role = $form.find('select[name="invite_role"]').val();
        var budgetId = $form.find('input[name="budget_id"]').val();
        
        if (!email || !validateEmail(email)) {
            showNotice('נא להזין כתובת אימייל תקינה', 'error');
            return;
        }
        
        $.ajax({
            url: budgex_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'budgex_invite_user',
                budget_id: budgetId,
                user_email: email,
                role: role,
                nonce: budgex_ajax.nonce
            },
            beforeSend: function() {
                $submitBtn.prop('disabled', true).val(budgex_ajax.strings.loading);
            },
            success: function(response) {
                if (response.success) {
                    showNotice(response.data.message, 'success');
                    $form[0].reset();
                } else {
                    showNotice(response.data.message, 'error');
                }
            },
            error: function() {
                showNotice(budgex_ajax.strings.error, 'error');
            },
            complete: function() {
                $submitBtn.prop('disabled', false).val('שלח הזמנה');
            }
        });
    });

    // Currency formatting
    $('.currency-input').on('input', function() {
        var value = $(this).val();
        if (value) {
            var currency = $(this).data('currency') || 'ILS';
            var symbol = currency === 'ILS' ? 'ש"ח' : '$';
            $(this).next('.currency-symbol').text(symbol);
        }
    });

    // Date picker Hebrew localization
    if ($.datepicker) {
        $.datepicker.regional['he'] = {
            closeText: 'סגור',
            prevText: '&#x3C;הקודם',
            nextText: 'הבא&#x3E;',
            currentText: 'היום',
            monthNames: ['ינואר','פברואר','מרץ','אפריל','מאי','יוני',
                'יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר'],
            monthNamesShort: ['ינו','פבר','מרץ','אפר','מאי','יונ',
                'יול','אוג','ספט','אוק','נוב','דצמ'],
            dayNames: ['ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת'],
            dayNamesShort: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','ש\''],
            dayNamesMin: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','ש\''],
            weekHeader: 'Wk',
            dateFormat: 'dd/mm/yy',
            firstDay: 0,
            isRTL: true,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['he']);
    }

    // Initialize date pickers
    $('.datepicker').datepicker();

    // Helper functions
    function showNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
        
        setTimeout(function() {
            $notice.fadeOut();
        }, 5000);
    }

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function updateBudgetCalculation() {
        // This would typically refresh the budget calculation display
        // For now, we'll just reload the page
        setTimeout(function() {
            location.reload();
        }, 1000);
    }

    // Toggle forms
    $('.toggle-form').on('click', function() {
        var target = $(this).data('target');
        $(target).slideToggle();
        $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
    });

    // Auto-calculate remaining budget
    function calculateRemainingBudget() {
        var totalBudget = parseFloat($('#total-budget').text()) || 0;
        var totalOutcomes = 0;
        
        $('.outcome-amount').each(function() {
            totalOutcomes += parseFloat($(this).text()) || 0;
        });
        
        var remaining = totalBudget - totalOutcomes;
        $('#remaining-budget').text(remaining.toFixed(2));
        
        // Update color based on remaining budget
        if (remaining < 0) {
            $('#remaining-budget').addClass('negative');
        } else {
            $('#remaining-budget').removeClass('negative');
        }
    }

    // Initial calculation
    calculateRemainingBudget();
});
