let $ = jQuery

class MobileSearch {
    // describe and create/initiate our object
    constructor() {
        this.url = window.location.hostname === "localhost" ? "http://localhost/inspirynew/wp-json/inspiry/v1/search?term=" : "https://inspiry.co.nz/wp-json/inspiry/v1/search?term="
        this.allProductsURL = window.location.hostname === "localhost" ? "http://localhost/inspirynew/wp-json/inspiry/v1/all-products-search?term=" : "https://inspiry.co.nz/wp-json/inspiry/v1/all-products-search?term="
        this.loading = $('.fa-spinner')
        this.searchIcon = $('.search-code .fa-search')
        this.resultDiv = $('.search-code .result-div')
        this.searchField = $('#mobile-search-term')
        this.typingTimer
        this.searchBar = $('.search-bar')
        this.events()
        this.isSpinnerVisible = false
        this.previousValue
    }
    // events 
    events() {
        this.searchField.on("keyup", this.typingLogic.bind(this))
        this.searchField.on("click", this.searchFieldClickHandler.bind(this))
        $(document).on("click", this.documentClickHandler.bind(this))
    }
    // document click handler
    documentClickHandler(e) {
        if (!this.searchBar.is(e.target) && this.searchBar.has(e.target).length === 0) {
            this.resultDiv.hide()
        }
    }
    // search field click
    searchFieldClickHandler() {
        this.resultDiv.show()
    }
    // methods
    typingLogic() {
        if (this.searchField.val() != this.previousValue) {
            clearTimeout(this.typingTimer)
            // check if the value is not empty
            if (this.searchField.val()) {
                if (!this.isSpinnerVisible) {
                    // show loading spinner
                    this.loading.show()
                    this.isSpinnerVisible = true
                }
                this.typingTimer = setTimeout(this.getResults.bind(this), 2000)
            }
            else {
                // hide loading
                this.loading.hide()
                this.isSpinnerVisible = false
            }
        }
        this.previousValue = this.searchField.val()
    }


    // get result method
    async getResults() {
        // send request 
        $.getJSON(`${this.url}${this.searchField.val()}`, (data) => {
            this.resultDiv.show()
            if (data.length) {
                this.resultDiv.html(`<ul class="search-list">
                ${data.map(item => {
                    return `<li>
                    <a href="${item.link}"> 
                    <img src="${item.image}" alt=${item.title}/>
                    <span>${item.title}</span>
                    </a>
                    </li>`
                }).join('')}
                </ul>`)

                // get rest of the query projects
                $.getJSON(`${this.allProductsURL}${this.searchField.val()}`, (allProducts) => {

                    if (allProducts.length) {
                        $('.search-list').append(` ${allProducts.map(item => {
                            return `<li>
                            <a href="${item.link}"> 
                            <img src="${item.image}" alt=${item.title}/>
                            <span>${item.title}</span>
                            </a>
                            </li>`
                        }).join('')}`)
                    }

                })

            }
            else {
                this.resultDiv.html(`<p class="center-align medium">Nothing found</p>`)
            }


            // hide loading spinner 
            if (this.isSpinnerVisible) {
                this.loading.hide()
                this.isSpinnerVisible = false
            }
        })
    }

}
export default MobileSearch