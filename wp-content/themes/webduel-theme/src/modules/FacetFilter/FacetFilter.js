const $ = jQuery

class FacetFilter {
    constructor() {


        // mobile and desktop filter show/hide
        this.closeButton = $('.mobile-filter-container .close-button')
        this.closeIcon = $('.mobile-filter-container .close-icon')
        this.showResultsButton = $('.mobile-filter-container .primary-button')
        // desktop filter show 
        this.filterButton = $('.filter-sort-container .filter-button')
        // facet label button
        this.labelButton = $('.facet-label-button')
        this.events()

    }
    events() {
        $(window).scroll(function (event) {
            var scroll = $(window).scrollTop();
            // Do something
            if (scroll > 300) {
                $('.fixed-filter-button').slideDown()
            }
            else {
                $('.fixed-filter-button').slideUp()
            }
        });
        // show filter container
        this.filterButton.on('click', this.showDesktopContainer)
        // hide filter container
        this.closeIcon.on('click', this.hideDesktopContainer)
        this.showResultsButton.on('click', this.hideDesktopContainer)
        // show filter when clicked on label desktop 

        this.labelButton.on('click', this.showFilter)
    }



    // show desktop filter container on button click
    showDesktopContainer() {

        if (window.matchMedia("(max-width: 1100px)").matches) {
            $('.facet-wp-container').slideDown('slow')
        }
        else {
            $('.facet-wp-container').slideToggle('slow')
        }
        if ($('.filter-sort-container .filter-button span').text() === 'Show Filters') {
            $('.filter-sort-container .filter-button span').text('Hide Filters')
        }
        else {
            $('.filter-sort-container .filter-button span').text('Show Filters')
        }
    }

    hideDesktopContainer() {
        console.log('clicked close button')
        $('.filter-sort-container .filter-button span').text('Show Filters')
        $('.facet-wp-container').hide('slow')
    }

    showFilter(e) {
        console.log(e)
        $(this).siblings('.facetwp-facet').slideToggle('fast')
        $(this).find('i').toggleClass('fa-plus')
        $(this).find('i').toggleClass('fa-minus')
    }
}

export default FacetFilter