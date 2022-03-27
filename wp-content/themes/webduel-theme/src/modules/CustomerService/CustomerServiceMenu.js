const $ = jQuery

class CustomerServiceMenu {
    constructor() {
        this.events()
    }
    events() {
        // add toggle icon in menu link 
        $('#menu-customer-service-sidebar-menu .menu-item-has-children>a').append('<span>+</span>')
        // toggle submenu on click 
        $('#menu-customer-service-sidebar-menu .menu-item-has-children>a').on('click', this.toggleSubmenu)

        // select aria current attribute
        $('#menu-customer-service-sidebar-menu a[aria-current="page"]').closest('.sub-menu').show()

        // find if the submenu is open and add "-" in span
        $('#menu-customer-service-sidebar-menu a[aria-current="page"]').closest('.current-menu-parent').find('a span').html("–")

        // show mobile menu
        $('.customer-service-page .sidebar-mobile-menu .secondary-button').on('click', this.showMobileNavbar)
    }


    // toggle submenu 
    toggleSubmenu(e) {
        e.preventDefault()
        $(this).siblings('.sub-menu').slideToggle("fast", function () {
            // toggle the icon by check the current icon of span
            if ($(this).siblings('a').find('span').html() === "+") {
                $(this).siblings('a').find('span').html("–")
            }
            else {
                $(this).siblings('a').find('span').html("+")
            }
        })

    }

    // show mobile navbar
    showMobileNavbar() {
        $('.customer-service-page .sidebar-mobile-menu i').toggleClass('arrow-up')
        $('.customer-service-page .sidebar').slideToggle()
    }
}

export default CustomerServiceMenu