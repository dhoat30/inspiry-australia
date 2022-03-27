let $ = jQuery;

class CartModal {
    constructor() {
        this.events();

    }

    events() {
        this.showModal();
        // hide modal 
        $('.modal-section .fa-times').on('click', this.hideModal);
    }

    showModal(e) {
        setTimeout(() => {
            $('.modal-section').show(200)
            if ($('.modal-section').data('overlay') === true) {

            }

        }, 3000)

    }
    hideModal() {
        $('.modal-section').hide(200)
        $('.overlay').hide();
    }
}

export default CartModal;