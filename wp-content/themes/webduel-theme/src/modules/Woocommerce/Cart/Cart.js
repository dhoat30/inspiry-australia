import RemoveCartItem from "./RemoveCartItem"
import UpdateCart from "./UpdateCart"

const $ = jQuery
class Cart {
    constructor() {
        this.plusBtn = $('.woocommerce-cart .quantity-container .plus')
        this.minusBtn = $('.woocommerce-cart .quantity-container .minus')
        this.qtyInputField = $('.woocommerce-cart .quantity-container #cart-quantity')
        this.removeIcon = $('.remove-product i')
        this.events()
    }
    events() {
        this.plusBtn.on('click', this.incrementValue)
        this.minusBtn.on('click', this.decrementValue)
        this.qtyInputField.on('change', this.onQtyChange)
        $(document).on('click', '.remove-product i', this.removeCartItemOnClick)
    }

    incrementValue(e) {
        let qty = $(this).siblings('#cart-quantity')
        let val = parseFloat(qty.val())
        var max = parseFloat(qty.attr('max'));
        var min = parseFloat(qty.attr('min'));
        var cart_item_key = qty.attr('data-cart_item_key')
        var step = 1
        if (max && (max <= val)) {
            qty.val(max);
        }
        else {
            qty.val(val + step);
            let timer = setTimeout(() => {
                const updateCart = new UpdateCart(qty.val(), cart_item_key)
            }, 1000)

        }
    }
    decrementValue() {
        let qty = $(this).siblings('#cart-quantity')
        let val = parseFloat(qty.val())
        var max = parseFloat(qty.attr('max'));
        var min = parseFloat(qty.attr('min'));
        var cart_item_key = qty.attr('data-cart_item_key')

        var step = 1
        if (min && (min >= val)) {
            qty.val(min);
        }
        else if (val > 1) {
            qty.val(val - step);
            let timer = setTimeout(() => {
                const updateCart = new UpdateCart(qty.val(), cart_item_key)
            }, 1000)

        }
    }

    onQtyChange() {
        let qty = $(this)
        var cart_item_key = qty.attr('data-cart_item_key')
        const updateCart = new UpdateCart(qty.val(), cart_item_key)
    }

    removeCartItemOnClick() {
        console.log('remove clicked')
        var cart_item_key = $(this).attr('data-cart_item_key')
        const removeCartItem = new RemoveCartItem(0, cart_item_key)
    }
}
export default Cart