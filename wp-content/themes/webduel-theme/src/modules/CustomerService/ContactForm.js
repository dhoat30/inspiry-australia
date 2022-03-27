const $ = jQuery
import GeneralFormProcessor from '../Form/GeneralFormProcessor'

class ContactForm {
    constructor() {
        this.events()
    }

    events() {
        $('#contact-form').on('submit', this.contactFormSubmission)
    }
    contactFormSubmission(e) {
        e.preventDefault()
        let formID = '#contact-form'
        let url = window.location.hostname;
        let apiRoute
        if (url === 'localhost') {
            apiRoute = `/inspirynew/wp-json/inspiry/v1/contact`
        }
        else {
            apiRoute = `https://inspiry.co.nz/wp-json/inspiry/v1/contact`
        }

        let dataObj = {}
        dataObj.firstName = $('#contact-form #first-name').val()
        dataObj.lastName = $('#contact-form #last-name').val()
        dataObj.email = $('#contact-form #email').val()
        dataObj.phone = $('#contact-form #phone-number').val()
        dataObj.enquiry = $('#contact-form #enquiry-term').val()
        dataObj.message = $('#contact-form #message').val()
        dataObj.emailTo = 'support@inspiry.co.nz'

        // send data to form processor 
        const generalFormProcessor = new GeneralFormProcessor(apiRoute, dataObj, formID)

    }
}

export default ContactForm