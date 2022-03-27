// use this to send data for any kind of form 
const $ = jQuery
class GeneralFormProcessor {
    constructor(apiRoute, dataObj, formID) {
        this.apiRoute = apiRoute
        this.dataObj = dataObj
        this.formID = formID
        this.events()
    }

    events() {
        console.log(this.formID)
        // send data to rest email api 

        const jsonData = JSON.stringify(this.dataObj);
        let xhr = new XMLHttpRequest();
        // add a loader in a button
        $(`${this.formID} .primary-button`).html('<div class="loader-icon loader--visible"></div>')

        const formID = this.formID
        xhr.open('POST', this.apiRoute);

        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onload = function () {
            console.log(xhr)
            if (xhr.status === 200) {
                $(`${formID} .primary-button`).html('SENT')
                $(formID).append('<p class="success-msg paragraph regular success right-align">Thanks for contacting us!</p>');

            }
            else {
                console.log('this is an error')
                $(`${formID} .primary-button`).html('SEND')

                $(formID).append('<p class="error-msg paragraph regular error">Something went wrong. Please try again!</p>');
            }
        }

        xhr.send(jsonData);
    }
}
export default GeneralFormProcessor