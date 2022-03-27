import { data } from "jquery";

const $ = jQuery;
class Form {
    constructor() {
        this.enquiryForm = $('#enquiry-form');
        this.events();
    }

    events() {
        this.enquiryForm.on('submit', this.enquiryFormProcessor.bind(this));

    }

    enquiryFormProcessor(e) {
        let dataObj = this.getFormData(e, '#enquiry-form');
        // this.sendMailchimpReq(dataObj, 'wp-json/inspiry/v1/enquiry-mailchimp')
        this.sendRequest(dataObj, 'wp-json/inspiry/v1/enquiry-email', '#enquiry-form')
    }
    // send data to mailchimp 
    sendMailchimpReq(dataObj, fileName, formID) {
        const jsonData = JSON.stringify(dataObj);
        let xhr = new XMLHttpRequest();
        let url = window.location.hostname;
        let filePath;

        if (url === 'localhost') {
            filePath = `http://localhost/${fileName}`
        }
        else {
            filePath = `https://inspiry.co.nz/${fileName}`
        }

        xhr.open('POST', filePath);

        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function () {
            $(`${formID} p`).html('');
            console.log("mailchimp response")
            console.log(xhr.response)
        }
        xhr.send(jsonData);
    }

    // send request function
    sendRequest(dataObj, fileName, formID) {
        // change button to loading icon
        $('#enquiry-form button').html('<div class="loader-icon loader--visible"></div>')
        let filePath;
        let url = window.location.hostname;

        if (url === 'localhost') {
            filePath = `http://localhost/${fileName}`
        }
        else {
            filePath = `https://inspiry.co.nz/${fileName}`
        }

        const jsonData = JSON.stringify(dataObj);
        let xhr = new XMLHttpRequest();


        xhr.open('POST', filePath);

        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function () {
            // remove loader icon
            // $('.loader-icon').remove()
            // show button
            $('#enquiry-form button').html("Sent")

            $(`${formID} p`).html('');
            if (xhr.status == 200) {
                console.log(xhr)
                $($(formID).prop('elements')).each(function (i) {
                    if (this.value !== 'Submit') {
                        this.value = "";
                        // uncheck the checked box 
                        $('#newsletter').prop('checked', false);
                    }
                });

                $(formID).append('<p class="success-msg paragraph regular success">Thanks for contacting us!</p>');
                setTimeout(() => {
                    $('.enquiry-form-section').hide();
                    $('.overlay').hide();
                }, 4000);
            }
            else {
                console.log('this is an error')
                $(formID).append('<p class="error-msg paragraph regular error">Something went wrong. Please try again!</p>');
            }
        }

        xhr.send(jsonData);
    }

    getFormData(e, formID) {

        e.preventDefault();

        var dataObj = {};
        $($(formID).prop('elements')).each(function (i) {
            dataObj[$(this).attr('name')] = this.value;
            dataObj[$(this).attr('last-name')] = this.value;
        });

        // check if the checkbox is checked 
        if ($('#enquiry-form #newsletter:checked').length > 0) {
            dataObj.newsletter = 'Yes';
        }
        else {
            dataObj.newsletter = 'No';
        }

        // send custom data
        let productID = $(this.enquiryForm).data('id');
        let productName = $(this.enquiryForm).data('name');
        if (productID && productName) {
            dataObj.productID = productID;
            dataObj.productName = productName;
        }
        dataObj.emailTo = "hello@inspiry.co.nz"

        return dataObj;
    }

}

export default Form;