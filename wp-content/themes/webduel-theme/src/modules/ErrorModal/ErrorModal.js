const $ = jQuery

class ErrorModal {
    constructor() {
        this.dismissBtn = $('.error-modal button')
        this.events()
    }
    events() {
        this.dismissBtn.on('click', this.hideModal)
        $('.error-modal').on('click', this.hideModal)
    }
    hideModal() {
        $('.error-modal').hide()

    }
}

export default ErrorModal