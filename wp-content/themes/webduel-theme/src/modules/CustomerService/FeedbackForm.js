const $ = jQuery
import GeneralFormProcessor from '../Form/GeneralFormProcessor'

class FeedbackForm {
    constructor() {
        this.events()
    }

    events() {
        $('#feedback-form').on('submit', this.feedbackFormSubmission)
    }
    feedbackFormSubmission(e) {
        e.preventDefault()
        let formID = '#feedback-form'
        let url = window.location.hostname;
        let apiRoute
        if (url === 'localhost') {
            apiRoute = `/inspirynew/wp-json/inspiry/v1/feedback-email`
        }
        else {
            apiRoute = `https://inspiry.co.nz/wp-json/inspiry/v1/feedback-email`
        }

        let dataObj = {}
        dataObj.firstName = $('#feedback-form #first-name').val()
        dataObj.lastName = $('#feedback-form #last-name').val()
        dataObj.email = $('#feedback-form #email').val()
        dataObj.phone = $('#feedback-form #phone-number').val()
        dataObj.feedback = $('#feedback-form #feedback').val()
        dataObj.emailTo = 'support@inspiry.co.nz'
        console.log(dataObj)
        // send data to form processor 
        const generalFormProcessor = new GeneralFormProcessor(apiRoute, dataObj, formID)
    }
}

export default FeedbackForm