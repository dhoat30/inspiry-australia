const $ = jQuery;

class PopUpCart {
    constructor() {

        this.events();
    }

    events() {
        $('.variable-item').on('click', () => {

            let formData = $('form.cart').data('product_variations')
        })

        $('.header .shopping-cart').on('click', this.openCart)
        $(document).on('click', '.cart-box .cont-shopping a', this.closeCart)
        $(document).on('click', '.dark-overlay', this.closeCart)
        $(document).on('click', '.cart-popup-container .title-section i', this.closeCart)
        // $('.cart-popup-container .fa-times').on('click', this.closeCart)
        $(document).on('click', '.single_add_to_cart_button', this.ajaxAddToCart)
        // remove item from cart ajax 
        $(document).on('click', '.cart-popup-container .fa-times', this.removeItem)

        // plus minus quantity button 
        $('form.cart').on('click', ' .plus, .minus', this.plusMinusButtons)
    }

    //remove item from cart function 
    removeItem(e) {
        e.preventDefault();
        var productId = $(this).attr("data-productid"),
            cart_item_key = $(this).attr("data-cart_item_key"),
            product_container = $(this).parents('.product-card');
        console.log(productId)
        console.log(cart_item_key)
        // Add loader
        product_container.block({
            message: null,
            overlayCSS: {
                cursor: 'none'
            }
        });

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: "product_remove",
                product_id: productId,
                cart_item_key: cart_item_key
            },
            success: function (response) {
                console.log(response)
                if (!response || response.error)
                    return;

                var fragments = response.fragments;

                // Replace fragments
                if (fragments) {
                    $.each(fragments, function (key, value) {
                        $(key).replaceWith(value);
                    });
                }
            }
        });
    }

    //close cart

    // open cart
    openCart(event) {
        event.preventDefault();
        console.log('slide down cart')
        $('.cart-popup-container').slideToggle('slow');
        $('.header .shopping-cart a i').toggleClass('fa-chevron-up');
        $('.dark-overlay').show()

    }
    closeCart() {
        $('.cart-popup-container').slideUp('slow')
        $('.header .shopping-cart a i').removeClass('fa-chevron-up');
        $('.dark-overlay').hide()
    }

    ajaxAddToCart(e) {

        console.log(wc_add_to_cart_params.ajax_url)
        e.preventDefault();
        let thisbutton = $(this),
            $form = thisbutton.closest('form.cart'),
            id = thisbutton.val(),
            product_qty = $form.find('input[name=quantity]').val() || 1,
            product_id = $form.find('input[name=product_id]').val() || id,
            variation_id = $form.find('input[name=variation_id]').val() || 0;

        var data = {
            action: 'woocommerce_ajax_add_to_cart',
            product_id: product_id,
            product_sku: '',
            quantity: product_qty,
            variation_id: variation_id,
        };

        $(document.body).trigger('adding_to_cart', [thisbutton, data]);
        $.ajax({
            type: 'post',
            url: '/wp-admin/admin-ajax.php',
            data: data,
            beforeSend: function (response) {
                thisbutton.removeClass('added').addClass('loading');
            },
            complete: function (response) {
                thisbutton.addClass('added').removeClass('loading');

            },
            success: function (response) {
                $('.cart-popup-container').slideDown();
                $('.dark-overlay').show()
                // setTimeout(function () { $('.cart-popup-container').slideUp('slow'); }, 3000);

                if (response.error & response.product_url) {
                    window.location = response.product_url;
                    return;
                } else {
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, thisbutton]);
                }
            },
        });
    }

    plusMinusButtons() {
        // Get current quantity values
        var qty = $(this).closest('form.cart').find('.qty');
        var val = parseFloat(qty.val());
        var max = parseFloat(qty.attr('max'));
        var min = parseFloat(qty.attr('min'));
        var step = parseFloat(qty.attr('step'));

        // Change the value if plus or minus
        if ($(this).is('.plus')) {
            if (max && (max <= val)) {
                qty.val(max);
            }
            else {
                qty.val(val + step);
            }
        }
        else {
            if (min && (min >= val)) {
                qty.val(min);
            }
            else if (val > 1) {
                qty.val(val - step);
            }
        }
    }
}

export default PopUpCart;