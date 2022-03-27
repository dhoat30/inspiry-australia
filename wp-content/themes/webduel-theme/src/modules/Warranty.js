let $ = jQuery; 
class Warranty{ 
    constructor(){
        this.events(); 
    }

    events(){ 
        $('.bc-single-product__warranty h1').append('<i class="fal fa-plus"></i>');
        $(document).on('click', '.bc-single-product__warranty i', this.showContentIcon); 
        $(document).on('click', '.bc-single-product__warranty h1', this.showContent); 
    }

    showContent(e){ 
        $(e.target).closest('h1').next().slideToggle(300); 
        $(e.target).closest('h1').siblings('ul').slideToggle(300); 
        $(e.target).find('i').toggleClass('fa-plus');
        $(e.target).find('i').toggleClass('fa-minus');
       
    }
    showContentIcon(e){ 
       console.log('worked !')
        $(e.target).toggleClass('fa-plus');
        $(e.target).toggleClass('fa-minus');
    }

}

export default Warranty; 