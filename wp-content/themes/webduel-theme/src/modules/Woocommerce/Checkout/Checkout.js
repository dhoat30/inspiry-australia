import Windcave from "./Windcave";

const $ = jQuery
class Checkout {
    constructor() {
        $(":submit").removeAttr("disabled");
        this.onPaymentSelectionChange
        this.windcavePaymentSelected = $("input[type='radio'][name='payment_method']:checked").val();
        this.events()
    }
    events() {
        $('#pay-button').on('click', this.showPaymentOptions)
    }
    showPaymentOptions(e) {
        e.preventDefault()
        const validateInputField = (selector, errorText, selectorID, validationFormat) => {
            if (selector.val().length < 1 && !validationFormat) {
                selector.closest('.woocommerce-input-wrapper').append(`<div class="error">${errorText}</div>`)
                $('html, body').animate({
                    scrollTop: $(selectorID).offset().top
                }, 100);
                return false
            }
            else if (validationFormat === 'email'
                && !selector.val().match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) {
                selector.closest('.woocommerce-input-wrapper').append(`<div class="error">${errorText}</div>`)
                $('html, body').animate({
                    scrollTop: $(selectorID).offset().top
                }, 100);
                return false
            }
            else if (validationFormat === 'phone'
                && !selector.val().match(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{1,6}$/im)) {
                selector.closest('.woocommerce-input-wrapper').append(`<div class="error">${errorText}</div>`)
                $('html, body').animate({
                    scrollTop: $(selectorID).offset().top
                }, 100);
                return false
            }
            else {
                return true
            }
        }
        // validate shipping form 
        const validateShippingForm = () => {
            $('.error').remove()
            let firstName = $('.woocommerce-checkout #shipping_first_name')
            let lastName = $('.woocommerce-checkout #shipping_last_name')
            let address1 = $('.woocommerce-checkout #shipping_address_1')
            let city = $('.woocommerce-checkout #shipping_city')
            let postCode = $('.woocommerce-checkout #shipping_postcode')
            // validate first name
            const isFirstNameValid = validateInputField(firstName, 'Please enter your first name', '#billing_first_name')
            // validate last name
            const isLastNameValid = validateInputField(lastName, 'Please enter your last name', '#billing_last_name')
            // validate address1
            const isAddress1Valid = validateInputField(address1, 'Please enter your street address', '#billing_address_1')
            // validate city
            const isCityValid = validateInputField(city, 'Please enter your city', '#billing_city')
            // validate post code
            const isPostCodeValid = validateInputField(postCode, 'Please enter your post Code', '#billing_postcode')
            // validate phone

            if (isFirstNameValid && isLastNameValid && isAddress1Valid && isCityValid && isPostCodeValid) {
                return true
            }
        }
        const validateBillingForm = () => {
            $('.error').remove()
            // check the required values 
            let firstName = $('.woocommerce-checkout #billing_first_name')
            let lastName = $('.woocommerce-checkout #billing_last_name')
            let address1 = $('.woocommerce-checkout #billing_address_1')
            let city = $('.woocommerce-checkout #billing_city')
            let postCode = $('.woocommerce-checkout #billing_postcode')
            let phone = $('.woocommerce-checkout #billing_phone')
            let emailAddress = $('.woocommerce-checkout #billing_email')



            // validate first name
            const isFirstNameValid = validateInputField(firstName, 'Please enter your first name', '#billing_first_name')
            // validate last name
            const isLastNameValid = validateInputField(lastName, 'Please enter your last name', '#billing_last_name')
            // validate address1
            const isAddress1Valid = validateInputField(address1, 'Please enter your street address', '#billing_address_1')
            // validate city
            const isCityValid = validateInputField(city, 'Please enter your city', '#billing_city')
            // validate post code
            const isPostCodeValid = validateInputField(postCode, 'Please enter your post Code', '#billing_postcode')
            // validate phone
            const isPhoneValid = validateInputField(phone, 'Please enter your phone number', '#billing_phone', 'phone')
            // validate email address
            const isEmailAddressValid = validateInputField(emailAddress, 'Please enter your email address', '#billing_email', 'email')

            // ship to different address validation
            let shipToDifferentAddress = $("input[type='checkbox'][name='ship_to_different_address']:checked").val()

            if (shipToDifferentAddress) {

                if (isFirstNameValid && isLastNameValid && isAddress1Valid && isCityValid && isPostCodeValid && isPhoneValid && isEmailAddressValid && validateShippingForm()) {
                    return true
                }
            }
            else {
                if (isFirstNameValid && isLastNameValid && isAddress1Valid && isCityValid && isPostCodeValid && isPhoneValid && isEmailAddressValid) {
                    return true
                }
            }

        }
        if (validateBillingForm()) {
            $('#payment').show()
            // hide the pay now button 
            $(this).hide()
        }


    }

}
export default Checkout