jQuery(function($) {

    $('#woocommerce_laybuy_environment').on('change', function () {
        $('#woocommerce_laybuy_currency').trigger('change');
    }).trigger('change');

    function showCredentials(currency) {

        var env = $('#woocommerce_laybuy_environment').val();
        var envHide = env == 'production' ? 'sandbox' : 'production';

        $('#woocommerce_laybuy_' + env + '_' + currency + '_merchant_id').closest('tr').show();
        $('#woocommerce_laybuy_' + env + '_' + currency + '_api_key').closest('tr').show();

        $('#woocommerce_laybuy_' + envHide + '_' + currency + '_merchant_id').closest('tr').hide();
        $('#woocommerce_laybuy_' + envHide + '_' + currency + '_api_key').closest('tr').hide();
    }

    function hideAllCredentials() {

        var currencies = [];

        currenciesList = $('#woocommerce_laybuy_currency option');

        currenciesList.each(function(){
            currencies.push($(this).val());
        });

        currencies.push('global');

        for (var i in currencies) {

            var currency = currencies[i];

            $('#woocommerce_laybuy_sandbox_' + currency + '_merchant_id').closest('tr').hide();
            $('#woocommerce_laybuy_sandbox_' + currency + '_api_key').closest('tr').hide();

            $('#woocommerce_laybuy_production_' + currency + '_merchant_id').closest('tr').hide();
            $('#woocommerce_laybuy_production_' + currency + '_api_key').closest('tr').hide();

        };
    }

    if ($('#woocommerce_laybuy_laybuy_advance_setting').is(':checked')) {
        $("#wp-category_pages_info_text-wrap").show();
        $("#wp-category_pages_info_text-wrap .description").show();
        $("#wp-product_pages_info_text-wrap").show();
        $("#wp-product_pages_info_text-wrap + .description").show();
        $("#woocommerce_laybuy_cart_page_info_text").show();
        $("#woocommerce_laybuy_cart_page_info_text .description").show();
        $("#wp-checkout_page_info_text-wrap").show();
        $("#wp-checkout_page_info_text-wrap .description").show();
        $("#wp-checkout_page_info_text-wrap").closest('tr').show();
        $("#woocommerce_laybuy_laybuy_page_enabled").closest('tr').show();
        $("#woocommerce_laybuy_laybuy_compatibility_mode").closest('tr').show();
        $("#woocommerce_laybuy_laybuy_geolocate_ip").closest('tr').show();
        $("#woocommerce_laybuy_laybuy_price_breakdown_out_of_stock").closest('tr').show();
        $("#woocommerce_laybuy_laybuy_billing_phone_field").closest('tr').show();
    }

    $('#woocommerce_laybuy_laybuy_advance_setting').on('change', function () {
        var $elEnabled = $('#woocommerce_laybuy_laybuy_advance_setting');
        if ($elEnabled.is(':checked')) {
            $("#wp-category_pages_info_text-wrap").show();
            $("#wp-category_pages_info_text-wrap .description").show();
            $("#wp-product_pages_info_text-wrap").show();
            $("#wp-product_pages_info_text-wrap + .description").show();
            $("#woocommerce_laybuy_cart_page_info_text").show();
            $("#woocommerce_laybuy_cart_page_info_text .description").show();
            $("#wp-checkout_page_info_text-wrap").show();
            $("#wp-checkout_page_info_text-wrap .description").show();
            $("#wp-checkout_page_info_text-wrap").closest('tr').show();
            $("#woocommerce_laybuy_laybuy_page_enabled").closest('tr').show();
            $("#woocommerce_laybuy_laybuy_compatibility_mode").closest('tr').show();
            $('[for="woocommerce_laybuy_currency"]').text('Currencies');
            $("#woocommerce_laybuy_laybuy_geolocate_ip").closest('tr').show();
            $("#woocommerce_laybuy_laybuy_price_breakdown_out_of_stock").closest('tr').show();
            $("#woocommerce_laybuy_laybuy_billing_phone_field").closest('tr').show();
        } else {

            $("#wp-category_pages_info_text-wrap").hide();
            $("#wp-category_pages_info_text-wrap .description").hide();
            $("#wp-product_pages_info_text-wrap").hide();
            $("#wp-product_pages_info_text-wrap + .description").hide();
            $("#woocommerce_laybuy_cart_page_info_text").hide();
            $("#woocommerce_laybuy_cart_page_info_text .description").hide();
            $("#wp-checkout_page_info_text-wrap").hide();
            $("#wp-checkout_page_info_text-wrap .description").hide();
            $("#wp-checkout_page_info_text-wrap").closest('tr').hide();
            $('[for="woocommerce_laybuy_currency"]').text('Default Currency');
            $("#woocommerce_laybuy_laybuy_compatibility_mode").closest('tr').hide();
            $("#woocommerce_laybuy_laybuy_page_enabled").closest('tr').hide();
            $("#woocommerce_laybuy_laybuy_geolocate_ip").closest('tr').hide();
            $("#woocommerce_laybuy_laybuy_price_breakdown_out_of_stock").closest('tr').hide();
            $("#woocommerce_laybuy_laybuy_billing_phone_field").closest('tr').hide();
        }
    }).trigger('change');

    hideAllCredentials();

    $('#woocommerce_laybuy_currency').on('change', function () {

        hideAllCredentials();
        var $self = $(this);

        if ($('#woocommerce_laybuy_global').is(':checked')) {
            showCredentials('global');
        } else {
            var currencies = $('#woocommerce_laybuy_currency').val();
            for (var i in currencies) {
                showCredentials(currencies[i]);
            }
        }
    }).trigger('change');

    $('#woocommerce_laybuy_global').on('change', function () {
        var currencies = $('#woocommerce_laybuy_currency').val();

        hideAllCredentials();

        if ($(this).is(':checked')) {
            showCredentials('global');
        } else {
            for (var i in currencies) {
                showCredentials(currencies[i]);
            }
        }
    });

    $('#laybuy_send_support_request').on('click', function (e) {

        e.preventDefault();
        var $self = $(this);

        $self.text('Sending...');

        var data = {
            action: 'send_laybuy_support_request'
        };

        jQuery.post( ajaxurl, data, function(response) {
            if (response.success === true){
                alert('Your support request has been sent!');
            } else {
                alert('Wait, something went wrong or too many requests, try later');
            }
        }).done(function(){

        }).always(function () {
            $self.text('Send Support Request');
        });
    });
});
