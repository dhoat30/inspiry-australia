const $ = jQuery

class SingleProduct {
    constructor() {
        this.variationProduct = $('.single-product .variations_form .variation_id')
        this.events()
    }
    events() {
        this.variationProduct.on('change', this.getVariationValue)
    }
    getVariationValue(e) {
        const variationID = $(this).val()
        const variationData = JSON.parse($('.single-product .variations_form .variation-availability-data').attr('data-variation_availability'))

        if (variationID > 0) {
            console.log(variationData)
            const selectedVariation = variationData.filter(item => item.variation_id === Number(variationID))
            console.log(selectedVariation[0].availability)
            if (selectedVariation[0].availability === "in-stock") {
                $('.single-product .availability .title span').text('In Stock')
                $('.single-product .availability .title span').css({ 'color': '#1fac75' })
                $('.single-product .availability .title .fa-circle-check').css({ 'color': '#1fac75' })
            }
            else {
                $('.single-product .availability .title span').text('Pre Order')
                $('.single-product .availability .title span').css({ 'color': '#d69400' })
                $('.single-product .availability .title .fa-circle-check').css({ 'color': '#d69400' })
            }
        }
        else {
            console.log('id is zero ')
        }
    }
}
export default SingleProduct