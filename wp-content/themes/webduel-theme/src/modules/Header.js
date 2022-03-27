const $ = jQuery

class Header {
    constructor() {
        this.events()
    }
    events() {
        // show sign in modal 
        $('.useful-links-container .sign-in-container').hover(this.showSignInModal, this.hideSignInModal)

        // show design boards header modal 
        $('.useful-links-container .design-board-icon-container').hover(this.showDesignBoardModal, this.hideDesignBoardModal)

    }
    showSignInModal() {
        $('.useful-links-container .sign-in-modal').show()
    }
    hideSignInModal() {
        $('.useful-links-container .sign-in-modal').hide()
    }
    // design board modal 
    showDesignBoardModal() {
        $('.useful-links-container .design-board-header-modal').show()
    }
    hideDesignBoardModal() {
        $('.useful-links-container .design-board-header-modal').hide()
    }
}
export default Header