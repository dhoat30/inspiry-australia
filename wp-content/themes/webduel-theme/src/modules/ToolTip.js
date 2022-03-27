let $ = jQuery;

class ToolTip {
    constructor() {
        $('.be-inspired-section').append(`
                <div class="tooltips poppins-font paragraph-font-size box-shadow">
                    Save to design board
                </div>`);

        $('.design-board-save-btn-container').append(`
                <div class="tooltips poppins-font paragraph-font-size box-shadow">
                    Save to design board
                </div>`);
        this.events();
    }
    events() {
        //show tooltip for be inspired section 
        $('.be-inspired-section').hover(this.showTooltip, this.hideTooltip);
        // show tool tip for design boards
        $('.design-board-save-btn-container i').hover(this.showTooltip, this.hideTooltip);
    }

    showTooltip(e) {
        console.log('tooltop ')
        $(e.target).siblings('.tooltips').slideDown('200');

    }
    hideTooltip(e) {
        $('.tooltips').hide();
    }
}

export default ToolTip;