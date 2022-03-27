let $ = jQuery; 
class ShopFav{ 
    constructor(){ 
        this.button = $('.inspiry-blogs .fourth-section .nav-buttons button');
        this.events(); 
    }
    events(){ 
       this.button.on('click', this.showProducts); 
    }

    showProducts(e){ 
        let targetVal = $(e.target).html();
        console.log(targetVal); 

        if(targetVal == 'Furniture'){ 
            $(e.target).siblings().removeClass('button-border');
            $(e.target).addClass('button-border');
            $(e.target).closest('.flex-container').find('.flex').removeClass('--visible-flex');
            $(e.target).closest('.flex-container').find('.furniture').addClass('--visible-flex');
        }
        else if(targetVal == 'Wallpaper'){ 
            $(e.target).siblings().removeClass('button-border');
            $(e.target).addClass('button-border');
            $(e.target).closest('.flex-container').find('.flex').removeClass('--visible-flex');
            $(e.target).closest('.flex-container').find('.wallpaper').addClass('--visible-flex');
        }
        else if(targetVal == 'Homeware'){ 
            $(e.target).siblings().removeClass('button-border');
            $(e.target).addClass('button-border');
            $(e.target).closest('.flex-container').find('.flex').removeClass('--visible-flex');
            $(e.target).closest('.flex-container').find('.homeware').addClass('--visible-flex');
        }
    }
}

export default ShopFav;