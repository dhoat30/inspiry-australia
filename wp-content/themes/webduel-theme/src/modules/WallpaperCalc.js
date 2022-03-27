class WallpaperCalc {
    constructor() {
        this.show();
        this.calc();
    }

    show() {
        //Wallpaper Calculator click event
        const calculatorButton = document.querySelector('.sizing-calculator-button');
        const calculatorOverlay = document.querySelector('.calculator-overlay');
        const overlayBackground = document.querySelector('.overlay-background');
        const closeIcon = document.querySelector('.close');

        if (calculatorButton) {
            calculatorButton.addEventListener('click', () => {
                console.log('button clicked')
                overlayBackground.classList.add('overlay-background--visible');
                calculatorOverlay.classList.add("calculator-overlay--visible");

            })
        }

        if (closeIcon) {
            closeIcon.addEventListener('click', () => {
                overlayBackground.classList.remove('overlay-background--visible');
                calculatorOverlay.classList.remove("calculator-overlay--visible");
            })
        }



    }

    calc() {
        var $j = jQuery.noConflict();

        var WAL = WAL || {};


        var WV = WV || {};

        //TODO: Move to unit.
        WV.CALCULATORMODULE = function (current) {

            // public api
            return {
                calculateNumberOfRolls: function (widthMeter, heightMeter, rollWidthCentiMeter, rollHeightMeter, rollPatternRepeatCentiMeter) {

                    var rollHeightCm = rollHeightMeter * 100;
                    var heightCm = heightMeter * 100;
                    var widthCm = widthMeter * 100;

                    console.log("calculateNumberOfRolls widthMeter", widthMeter);
                    console.log("calculateNumberOfRolls heightMeter", heightMeter);
                    console.log("calculateNumberOfRolls rollWidthCentiMeter", rollWidthCentiMeter);
                    console.log("calculateNumberOfRolls rollHeightMeter", rollHeightMeter);
                    console.log("calculateNumberOfRolls rollPatternRepeatCentiMeter", rollPatternRepeatCentiMeter);

                    var stripsRaw = rollHeightCm / (heightCm + rollPatternRepeatCentiMeter);
                    var strips = stripsRaw < 0 ? Math.ceil(stripsRaw) : Math.floor(stripsRaw);

                    var stripWidth = strips * rollWidthCentiMeter;
                    var numRolls = Math.round((widthCm / stripWidth) * 10000) / 10000;

                    console.log("strips", strips);
                    console.log("stripWidth", stripWidth);
                    console.log("numRolls", numRolls);

                    var numRollsRoundedUp = Math.ceil(numRolls);

                    console.log("numRolls", numRolls);

                    var result = {
                        numberOfRolls: numRolls,
                        numberOfRollsRoundedUp: Math.ceil(numRolls),
                    };

                    console.log("WV.MODULES.calculateNumberOfRolls result", result);

                    return result;
                }


            };

        }();

        $j(document).ready(function ($) {
            $j("#estimate-roll").click(function (event) {
                event.preventDefault();

                var parseAndValidate = function (selector) {
                    var $element = $j(selector);
                    console.log($element);
                    if ($element.val() == '') {
                        return 0;
                    } else {
                        var int_val = $element.val();
                        var maybeFloat = parseFloat(int_val.replace(",", "."));
                        if ($.isNumeric(maybeFloat)) {
                            $element.parent().addClass("has-success");
                            $element.parent().removeClass("has-error");

                        }
                        else {
                            $element.parent().removeClass("has-success");
                            $element.parent().addClass("has-error");
                        }

                        return maybeFloat;
                    }
                };

                let rollWidth = parseAndValidate("#calc-roll-width");
                let rollHeight = parseAndValidate("#calc-roll-height");
                let patternRepeat = parseAndValidate("#calc-pattern-repeat");
                let wallCount = 4;
                let rollTotal = 0;

                for (let i = 1; i <= wallCount; i++) {
                    let wallWidth = parseAndValidate("#calc-wall-width" + i);
                    let wallHeight = parseAndValidate("#calc-wall-height" + i);

                    let calculatedResult = WV.CALCULATORMODULE.calculateNumberOfRolls(wallWidth, wallHeight, rollWidth, rollHeight, patternRepeat);
                    console.log("wall" + i + " " + calculatedResult.numberOfRolls);
                    rollTotal += calculatedResult.numberOfRolls;
                    console.log("roll total " + i + " - " + rollTotal);
                }
                console.log("roll total " + rollTotal);


                if (rollTotal.numberOfRollsRoundedUp <= 1) {
                    $j(".suffix-singular").show();
                    $j(".suffix-plural").hide();
                }
                else {
                    $j(".suffix-singular").hide();
                    $j(".suffix-plural").show();
                }

                //$j(".calc-result").html(rollTotal.numberOfRolls);
                $j(".calc-round").html(Math.ceil(rollTotal));


                // var calculatorParams = {
                //     wallWidth: wallWidth,
                //     wallHeight: wallHeight,
                //     rollWidth: rollWidth,
                //     rollHeight: rollHeight,
                //     patternRepeat: patternRepeat
                // };

                // console.log(calculatedResult);

                // console.log("calculator parameters", calculatorParams);

                // console.log(' calculator-button ');
            });
        });


        //pop up overlay control





        //fabric calculator
        /*
     let fabricType = document.getElementById('fabric-type'); 
     let fabricWidth = document.getElementById('fabric-width'); 
     let trackLength = document.getElementById('track-length');
     let pattern = document.getElementById('pattern'); 
     let patternInputHorizontal = document.getElementById('pattern-value-hr'); 
     let patternInputVertical = document.getElementById('pattern-value-vr'); 
     let formHiddenFields = document.querySelector('.form-hidden-field'); 
     
     let calcDataField = document.getElementById('calculated-data'); 
     let fButton = document.getElementById('f-button'); 
     
     let calForm = document.getElementById('cal-form')
     calForm.addEventListener('submit', (e)=>{
          e.preventDefault(); 
     
        console.log(fabricWidth.value)
         fabricWidth = parseFloat(fabricWidth.value); 
         trackLength = parseFloat(trackLength.value); 
        console.log("after parse " + fabricWidth); 
         
     
         let calcData; 
         
         if(fabricType.value == 'inverted' || fabricType.value == 'pencil'){ 
             let a = trackLength * 2; 
             calcData = a/fabricWidth; 
         }
         else { 
             calcData = 20; 
         }
     
         if(pattern.value == 'yes'){ 
             console.log(pattern.value); 
             
         }
     
         
     
         calcDataField.innerHTML = calcData;
         calcData = 0 ; 
         console.log('worked')
     
     
     })*/


    }
}

export default WallpaperCalc;