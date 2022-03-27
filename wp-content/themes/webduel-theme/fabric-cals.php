<div class="sl-modal-popup" id="FabricCalculatorModal">
    <header>
        <h1 class="sl-heading">Fabric Calculator</h1>
        <span class="sl-heading sl-close like-h1">
            ×
        </span>
    </header>
    <div class="sl-modal-content fabric-rolls">
        <section class="contentContainer container-flex">
            <form novalidate="novalidate">
                <fieldset>
                    <div class="form-group">
                        <h3><span>1</span> <em>Please</em> check the dimensions of your fabric</h3>
                            <p class="small">we've started you off with our standard sizes below:</p>
                        <div class="row">
                            <div class="small-6 medium-6 large-6 columns">
                                <label id="fabric-width-label">Fabric width in cm</label>
                                <input id="fabric-width" type="number" value="138">
                            </div>
                        </div>
                        <div class="row">
                            <div class="small-6 medium-6 large-6 columns">
                                <label id="fabric-match-label">Pattern Match</label>
                                <select id="fabric-pattern-match" class="">
                                    <option value="" selected="">No Pattern Match</option>
                                    <option value="Straight Match">Straight Match</option>
                                    <option value="Quarter Drop Match">Quarter Drop Match</option>
                                    <option value="Third Drop Match">Third Drop Match</option>
                                    <option value="Half Drop Match">Half Drop Match</option>
                                    <option value="Random Match">Random Match</option>
                                    <option value="Reverse Hang Alternative Lengths">Reverse Hang Alternative Lengths</option>
                                </select>
                            </div>
                            <div class="small-6 medium-6 large-6 columns patternRpt">
                                <label id="fabric-repeat-label">Pattern Repeat in cm</label>
                                <input id="fabric-repeat" type="number" value="0" style="padding:11px;">
                            </div>
                            <input type="hidden" id="loadedPatternMatch" value="">
                            <input type="hidden" id="Ssp" value="0">
                        </div>
                    </div>

                    

                    <div class="form-group unit-selector">
                        <h3><span>3</span>add your curtain/blind size</h3>
                        <div id="fabric-group"><div class="wall metres" data-wall="metres_wall_">
    <div class="row">
        <div class="small-6 medium-6 large-6 columns">
            <label>Width in centimetres</label>
            <input class=" width roll" type="number" placeholder="width in centimetres" name="measure">
        </div>
        <div class="small-6 medium-6 large-6 columns">
            <label>Height in centimetres</label>
            <input class="height roll" type="number" placeholder="height in centimetres" name="measure">
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // retrieve previously entered calc measurements
        var width=$.cookie("calcWidth")*100; 
        var height=$.cookie("calcHeight")*100;

        // populate calculator
        if(width!=null||height!=null) {
            $('input.width.roll').val(width.toFixed(0));
            $('input.height.roll').val(height.toFixed(0));
        }

    });
</script></div>
                        <p id="error-message" class="error-message"></p>
                    </div>

                    <div class="form-group style-selection">
                        <h3><span>4</span>Choose your style</h3>
                        <div class="row style-selector">
                            <div>
                                <input type="radio" name="style" value="curtains" checked="">
                                <label class="radio" for="curtains"> Curtains </label>
                            </div>
                            <div>
                                <input type="radio" name="style" value="blinds">
                                <label class="radio" for="blinds"> Blinds </label>
                            </div>
                        </div>
                        <div id="curtain-styles">
                            <div class="row">
                                <div class="small-6 medium-6 large-6 columns">
                                    <label>Pair or Single</label>
                                    <select id="curtainType">
                                        <option value="Pair">Pair</option>
                                        <option value="Single">Single</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="small-6 medium-6 large-6 columns">
                                    <label>Heading Type</label>
                                    <select id="heading-type">
                                        <option value="">Please select</option>
                                        <option value="Pencil Pleat">Pencil Pleat</option>
                                        <option value="Gathered Heading">Gathered Heading</option>
                                        <option value="Triple Pleat">Triple Pleat</option>
                                        <option value="Goblet Heading">Goblet Heading</option>
                                        <option value="Eyelets">Eyelets</option>
                                        <option value="Tab Tops">Tab Tops</option>
                                    </select>
                                </div>

                                <div class="small-6 medium-6 large-6 columns fullness hide">
                                    <label>Fullness</label>
                                    <select id="fullness">
                                        <option value="" class="" select="selected">select</option>
                                        <option value="1.9" class="pencilpleat">1.9 (less full)</option>
                                        <option value="2.2" class="pencilpleat">2.2x (normal)</option>
                                        <option value="2.5" class="pencilpleat">2.5x (more full)</option>
                                        <option value="1.7" class="gatheredheading">1.7x (less full)</option>
                                        <option value="2.2" class="gatheredheading">2.2x (normal)</option>
                                        <option value="2.5" class="gatheredheading">2.5x (more full)</option>
                                        <option value="2.1" class="triplepleat">2.1x (less full)</option>
                                        <option value="2.3" class="triplepleat">2.3x (normal)</option>
                                        <option value="2.5" class="triplepleat">2.5x (more full)</option>
                                        <option value="1.9" class="gobletheading">1.9x (less full)</option>
                                        <option value="2.2" class="gobletheading">2.2x (normal)</option>
                                        <option value="2.5" class="gobletheading">2.5x (more full)</option>
                                        <option value="1.3" class="eyelets">1.3x (less full)</option>
                                        <option value="1.7" class="eyelets">1.7x (normal)</option>
                                        <option value="2.2" class="eyelets">2.2x (more full)</option>
                                        <option value="1.3" class="tabtops">1.3x (less full)</option>
                                        <option value="1.7" class="tabtops">1.7x (normal)</option>
                                        <option value="2.2" class="tabtops">2.2x (more full)</option>
                                    </select>
                                </div>

                                <p id="error-message-2" class="error-message"></p>
                            </div>
                        </div>
                        <div id="blind-styles" class="hide">
                            <div class="row">
                                <div class="small-6 medium-6 large-6 columns">
                                    <label id="">Blind Style</label>
                                    <select id="" class="">
                                        <option value="Roman Blind">Roman Blind</option>
                                        <option value="Roller Blind">Roller Blind</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="button-holder">
                        <button id="calculate-fabric-btn" type="submit" class="actionButton"><span class="fa fa-calculator" aria-hidden="true"></span>Calculate Fabric Needed</button>
                        <button id="reset-btn" type="button" class="secondaryButton"><span class="fa fa-refresh" aria-hidden="true"></span>Reset</button>
                    </div>
                </fieldset>
            </form>
        </section>

        <section id="result">
            <div id="roll-result">
                <p class="default-text">*Enter your fabric and window dimensions to calculate how much fabric your project will require.</p>

                <p class="result-text">
                    *You will need approximately <actual>0</actual>&nbsp;metres of fabric
                </p>
            </div>
            <hr>
            <div id="disclaimer">
                <p>*This calculator provides an approximate recommendation only. Style Library advise that you always consult your retailer before ordering fabric as we cannot be held responsible for any incorrect quantities of fabric ordered.</p>
            </div>
        </section>
    </div>
</div>
