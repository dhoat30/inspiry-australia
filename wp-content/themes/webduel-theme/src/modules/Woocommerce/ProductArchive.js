const $ = jQuery

class ProductArchive {
    constructor() {
        this.events()
    }
    events() {
        // prevent default behaviour 
        $('.wvs-archive-variation-wrapper').on('click', (e) => {
            e.preventDefault()
        })

    }
}
export default ProductArchive