const $ = jQuery

class SingleProductAccordion {
    constructor() {
        this.firstItem = $('.single-product .accordion-container .item')[0]
        this.events()
    }
    events() {
        $('.single-product .accordion-container .item .title').on('click', this.toggleAccordion)
        $('.single-product .accordion-container .item .title span').on('click', this.toggleIcon)
        this.showFirstItem()

    }
    toggleAccordion(e) {
        // console.log($(e.target).closest('.title').siblings('.content'))
        $(e.target).closest('.title').siblings('.content').slideToggle()
        let currentIcon = $(e.target).find('span').html()
        $(e.target).find('span').html(currentIcon === "+" ? "–" : "+")
    }
    toggleIcon(e) {
        console.log('icon function')
        let currentIcon = $(e.target).html()
        $(e.target).html(currentIcon === "+" ? "–" : "+")
    }
    showFirstItem() {
        $(this.firstItem).find('.content').show()
        $(this.firstItem).find('span').html('–')
    }
}

export default SingleProductAccordion