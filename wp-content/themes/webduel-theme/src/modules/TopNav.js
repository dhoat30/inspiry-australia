let $ = jQuery; 
class TopNav{ 
    constructor(){ 
        this.events(); 
    }
    events(){ 
        $('#top-navbar a').mouseover(this.showSubNav); 
       
    }
    showSubNav(e){ 
      
        let linkHTML = $(e.target).html();
        if(linkHTML == 'Design Services'){
            $('.design-services').show(300);
            $("body > *").not(e.target).closest('.top-navbar').mouseout(()=>{
                $('.design-services').hide(1000);
            }); 
        }
        
    }
    hideSubnav(e){ 
            $('.design-services').hide(1000);
        
    }
}

export default TopNav; 