const $ = jQuery




// windcave success callback
var successCallback = function (data) {
    var checkout_form = $('form.woocommerce-checkout');
    checkout_form.find('#windcave_session_id').val(data);
    // deactivate the tokenRequest function event
    checkout_form.off('checkout_place_order');


    // send request to api to check the transaction status before submiting a form 
    $.ajax({
        beforeSend: (xhr) => {
            $('.payment-gateway-container .foreground-loader').show()

            xhr.setRequestHeader('X-WP-NONCE', webduel_params.nonce)
        },
        url: webduel_params.root_url + '/wp-json/inspiry/v1/windcave-session-status',
        type: 'POST',
        data: {
            sessionID: data
        },
        complete: () => {

        },
        success: (response) => {
            console.log(response)
            if (response.transactions[0].authorised) {
                WindcavePayments.Seamless.cleanup()
                checkout_form.trigger("submit");
            }
            else {
                $('.error-modal').show()
                $('.error-modal .content').text(response.transactions[0].responseText)
                $('.error-modal button').text("Try Again")
                $('.payment-gateway-container').hide();
                $('.overlay').hide();
                $('.error-modal').on('click', () => location.reload())
            }
        },
        error: (response) => {
            console.log('this is a board error');
            console.log(response)
        }
    })
    // submit the form now


};

var errorCallback = function (data) {
    console.log(data);
};
var tokenRequest = function (e) {
    // here will be a payment gateway function that process all the card data from your form,
    // maybe it will need your Publishable API key which is webduel_params.publishableKey
    // and fires successCallback() on success and errorCallback on failure
    // show submit button
    $('.windcave-submit-button').show()
    WindcavePayments.Seamless.prepareIframe({
        url: webduel_params.windcaveObj.links[2].href,
        containerId: "windcave-custom-container",
        loadTimeout: 30,
        width: 400,
        height: 500,
        onProcessed: function (response) {
            console.log(response)
        },
        onError: function (error) {
            console.log(error)
        }
    });

    $('.windcave-submit-button').on('click', () => {
        // validate windcave credit card form 
        WindcavePayments.Seamless.validate({
            onProcessed: function (isValid) {
                if (isValid) {
                    console.log(isValid)
                    // if the credit card is valid, submit the form 
                    $('.windcave-submit-button').hide()
                    WindcavePayments.Seamless.submit({
                        onProcessed: function (response) {
                            successCallback(webduel_params.windcaveObj.id)

                        },
                        onError: function (error) {
                            console.log(error)
                            errorCallback()
                        }
                    });
                }
            },
            onError: function (error) {
                console.log(error)
                console.log('this is an error')
            }
        });
    })
    return false

};
jQuery(function () {
    jQuery('body')
        .on('updated_checkout', function () {
            usingGateway();

            jQuery('input[name="payment_method"]').change(function () {
                usingGateway();
            });
        });
});


function usingGateway() {
    // check if the windcave gateway is selected 
    if (jQuery('form[name="checkout"] input[name="payment_method"]:checked').val() === 'webduel_windcave_gateway') {
        jQuery(function ($) {
            console.log(webduel_params)
            var checkout_form = $('form.woocommerce-checkout');
            checkout_form.on('checkout_place_order', tokenRequest);
        });
        //Etc etc
    } else {
        var checkout_form = $('form.woocommerce-checkout');
        console.log("Not using my gateway. Proceed as usual");
        checkout_form.off('checkout_place_order');
        // submit the form now
    }
}


