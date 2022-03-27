const $ = jQuery

class Coupon {
    constructor() {
        this.events()
    }
    events() {
        $('.total-summary .coupon-code-input-container button').on('click', this.applyCoupon)
        $(document).on('click', '.total-summary .coupon-row button', this.removeCoupon)
    }
    applyCoupon(e) {
        const couponCode = $('.total-summary .coupon-code-input-container #coupon').val()

        $.ajax({
            beforeSend: (xhr) => {
                $('.overlay').show()

                xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
            },
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                couponCode: couponCode,
                action: 'woocommerce_ajax_add_coupon'
            },
            complete: () => {
                console.log('completed ajax request ')
                $('.overlay').hide()
            },
            success: (response) => {
                if (response.code === 200) {
                    console.log(response);
                    $('.overlay').hide()
                    // refresh cart data 
                    $('.total-summary .subtotal-row .amount span').text(response.subtotal)
                    $('.total-summary .shipping-row .amount span').text(response.shipping)
                    $('.total-summary .tax-row .amount span').text(response.tax)
                    $('.total-summary .total-row .amount').html(response.total)
                    $(` <ul class="flex-box coupon-row">
                    <li class="title">Coupon: give10</li>
                    <li class="amount">-$<span>10 <button>[Remove]</button></span></li>
                    </ul>`).insertAfter('.subtotal-row')
                    // hide coupon input field 
                    $('.coupon-code-input-container').hide()
                }
                else {
                    console.log(response)
                    $('.overlay').hide()
                    $('.error-modal .content').text('Coupon does not exist.')
                    $('.error-modal').show()
                    e.stopPropagation();
                }
            },
            error: (response) => {
                $('.overlay').hide()
                console.log('this is an error');
                console.log(response)
                $('.error-modal').show()
                $('.error-modal .content').text('An error has occurred while applying coupon. Please try again.')

            }
        });
    }

    removeCoupon() {
        const couponCode = $('.total-summary .coupon-code-input-container #coupon').val()
        $.ajax({
            beforeSend: (xhr) => {
                $('.overlay').show()
                xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce)
            },
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: {
                action: 'woocommerce_ajax_add_coupon',
                couponCode: 'remove'
            },
            complete: () => {
                console.log('completed ajax request ')
            },
            success: (response) => {

                if (response.code === 202) {
                    console.log(response)
                    $('.overlay').hide()
                    location.reload();
                }
                else {
                    $('.error-modal .content').text('An error has occurred while removing coupon. Please try again.')
                    $('.error-modal').show()
                }
            },
            error: (response) => {
                $('.overlay').hide()
                console.log('this is an error');
                console.log(response)
                $('.error-modal').show()
                $('.error-modal .content').text('An error has occurred while removing coupon. Please try again.')

            }
        });
    }
}
export default Coupon