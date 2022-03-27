const $ = jQuery

class RemoveCartItem {
    constructor(qty, cartItemKey) {
        this.qty = qty
        this.cartItemKey = cartItemKey
        this.removeItem()
    }
    removeItem() {
        $.ajax({
            beforeSend: (xhr) => {
                $('.overlay').show()
                xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
            },
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                qty: this.qty,
                cartItemKey: this.cartItemKey,
                action: 'woocommerce_ajax_update_cart'
            },
            complete: () => {
                console.log('completed ajax request ')
            },
            success: (response) => {
                if (response.code === 200) {
                    console.log(response)
                    $('.overlay').hide()
                    $(`.${this.cartItemKey}`).hide()
                    $('.total-summary .subtotal-row .amount span').text(response.subtotal)
                    $('.total-summary .shipping-row .amount span').text(response.shipping)
                    $('.total-summary .tax-row .amount span').text(response.tax)
                    $('.total-summary .total-row .amount').html(response.total)
                    location.reload()
                }
                else {
                    $('.overlay').hide()
                    $('.error-modal .content').text('An error has occurred while removing item. Please try again.')
                    $('.error-modal').show()

                }
            },
            error: (response) => {

                $('.error-modal .content').text('An error has occurred while removing item. Please try again.')
                $('.error-modal').show()
                console.log('this is an error');
                $('.overlay').hide()
                console.log(response)

            }
        });
    }
}
export default RemoveCartItem