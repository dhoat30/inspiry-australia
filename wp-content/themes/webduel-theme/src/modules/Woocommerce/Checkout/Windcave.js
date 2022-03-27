import axios from 'axios'
const $ = jQuery
window.sessionID = 10
class Windcave {
    constructor() {
        this.events()
    }
    events() {
        this.createSession()
        $('.windcave-submit-button').on('click', this.validateWindcave)
    }
    createSession() {
        // customer data 
        let firstName = $('.woocommerce-checkout #billing_first_name')
        let lastName = $('.woocommerce-checkout #billing_last_name')
        let phone = $('.woocommerce-checkout #billing_phone')
        let emailAddress = $('.woocommerce-checkout #billing_email')

        let cartTotal = $('.payment-gateway-container').attr('data-carttotal')
        $.ajax({
            beforeSend: (xhr) => {
                $('.payment-gateway-container .foreground-loader').show()
                xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
            },
            url: inspiryData.root_url + '/wp-json/inspiry/v1/windcave-session',
            type: 'POST',
            data: {
                cartTotal: cartTotal,
                firstName: firstName.val(),
                lastName: lastName.val(),
                emailAddress: emailAddress.val(),
                phone: phone.val()
            },
            complete: () => {
                $('.payment-gateway-container .foreground-loader').hide()
            },
            success: (response) => {
                if (response.state === 'init') {
                    window.sessionID = response.id
                    console.log(`session id ${window.sessionID}`)
                    let sessionLink = response.links.filter(item => item.rel === 'seamless_hpp')
                    // load windcave iframe 
                    WindcavePayments.Seamless.prepareIframe({
                        url: sessionLink[0].href,
                        containerId: "payment-iframe-container",
                        loadTimeout: 30,
                        width: 400,
                        height: 500,
                        onProcessed: function () {
                            console.log('iframes is loaded properly ')
                        },
                        onError: function (error) {
                            console.log(error)
                            console.log('this is an error event after loading ')
                        }
                    });
                }
            },
            error: (response) => {
                console.log('something went wrong.');
                console.log(response)
                $('.payment-gateway-container .foreground-loader').hide()
            }
        });
    }

    validateWindcave(e) {

        // validate windcave credit card form 
        WindcavePayments.Seamless.validate({
            onProcessed: function (isValid) {
                if (isValid) {
                    $('.payment-gateway-container .foreground-loader').show()

                    // if the credit card is valid, submit the form 
                    WindcavePayments.Seamless.submit({
                        onProcessed: function (response) {
                            console.log(response)
                            console.log('wincave submitted')
                            $('.payment-gateway-container .foreground-loader').hide()

                            getTransactionStatus(window.sessionID)
                        },
                        onError: function (error) {
                            console.log(error)
                        }
                    });
                }
            },

            onError: function (error) {
                console.log(error)
                $('.payment-gateway-container .foreground-loader').hide()
            }
        });

        const getTransactionStatus = (sessionID) => {
            $.ajax({
                beforeSend: (xhr) => {
                    $('.payment-gateway-container .foreground-loader').show()

                    xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
                },
                url: inspiryData.root_url + '/wp-json/inspiry/v1/windcave-session-status',
                type: 'POST',
                data: {
                    sessionID: sessionID
                },
                complete: () => {
                    $('.payment-gateway-container .foreground-loader').hide()
                    console.log('request completed')

                },
                success: (response) => {
                    if (response.transactions[0].authorised) {
                        console.log('transaction successful')
                        $(".woocommerce-checkout").trigger("submit");
                        $('#payment-iframe-container .button-container').append(`<p class="success center-align">Successful</p>`)
                        WindcavePayments.Seamless.cleanup()
                    }
                    else {
                        console.log(response)
                        $('.error-modal').show()
                        $('.error-modal .content').text(response.transactions[0].responseText)
                        $('.error-modal button').text("Try Again")
                        $('.payment-gateway-container').hide();
                        $('.overlay').hide();
                    }
                },
                error: (response) => {
                    console.log('this is a board error');
                    console.log(response)
                }
            })
        }
    }
}
export default Windcave