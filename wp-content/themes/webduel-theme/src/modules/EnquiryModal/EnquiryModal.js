let $ = jQuery;

class EnquiryModal {
    constructor() {
        this.events();
    }

    events() {
        $('#enquire-button').on('click', this.showEnquiryModal);
        // hide modal 
        $('.enquiry-form-section .fa-times').on('click', this.hideEnquiryModal);



    }


    showEnquiryModal(e) {
        e.preventDefault();
        $('.enquiry-form-section').show(200)
        $('.overlay').show();
    }
    hideEnquiryModal() {
        $('.enquiry-form-section').hide(200)
        $('.overlay').hide();
    }
}

export default EnquiryModal;