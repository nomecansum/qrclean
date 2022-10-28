<script src="{{ asset('/js/cron/cronstrue.min.js')}}" defer></script>
<script src="{{ asset('/js/cron/cron.js')}}" defer></script>


<style type="text/css">
    div.warning {
    color: saddlebrown;
    font-size: 75%;
    height: 0;
    }
    .text-editor input {
    font-family: "Courier New", Courier, monospace;
    text-align: center;
    font-size: 250%;
    width: 100%;
    background-color: #333333;
    border: 1px solid #cccccc;
    border-radius: 0.6em;
    color: #ffffff;
    padding-top: 0.075rem;
    }
    .text-editor input.invalid {
    border: 1px solid darkred;
    }
    .text-editor input.warning {
    border: 1px solid saddlebrown;
    }
    .text-editor input:focus {
    outline: none;
    }
    .text-editor input::-ms-clear {
    width: 0;
    height: 0;
    }
    .text-editor input::-moz-selection {
    color: #ffff80;
    background-color: rgba(255, 255, 128, 0.2);
    }
    .text-editor input::selection {
    color: #ffff80;
    background-color: rgba(255, 255, 128, 0.2);
    }
    .clickable {
    text-decoration: underline;
    cursor: pointer;
    -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
            user-select: none;
    }
    .part-explanation {
    font-size: 75%;
    color: #a8a8a8;
    height: 24em;
    }
    .part-explanation div {
    display: inline-block;
    vertical-align: top;
    margin: 0 1em 0 0;
    }
    .part-explanation .active {
    color: #ffff80;
    }
    .part-explanation .invalid {
    background-color: darkred;
    }
    .part-explanation .warning {
    background-color: saddlebrown;
    }
    .part-explanation .clickable {
    border-radius: 1em;
    padding: 0.1em 0.36em;
    }
    .part-explanation .clickable:last-child {
    margin: 0;
    }
    .human-readable {
    font-size: 200%;
    font-family: Georgia, serif;
    min-height: 2.2em;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: end;
        justify-content: flex-end;
    -ms-flex-line-pack: end;
        align-content: flex-end;
    -ms-flex-direction: column;
        flex-direction: column;
    margin-bottom: 0.2em;
    margin-top: 0.9em;
    }
    .human-readable .active {
    color: #ffff80;
    }
    .next-date {
    font-size: 75%;
    margin-left: 0.5em;
    }
    .tips {
    font-size: 75%;
    text-align: left;
    display: inline-block;
    vertical-align: top;
    margin-bottom: 3em;
    }
    .tips .title {
    font-weight: bold;
    }
    .example {
    text-align: right;
    font-size: 75%;
    margin-top: -1em;
    margin-bottom: 7px;
    }
</style>

<div class="card mb-2">
    <div id="crontabs" class="card-body">
        <p class="card-title-desc">Generar una expresion CRON que describirá la periodicidad del trabajo </p>
        <ul id="crongenerator" class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
            <li class="nav-item" style="display:none">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-1" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Seconds</span>
                    <span class="d-none d-sm-block">Seconds</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-2" role="tab">
                    <span class="d-block d-sm-none">Minutos</span>
                    <span class="d-none d-sm-block">Minutos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-3" role="tab" aria-selected="true">
                    <span class="d-block d-sm-none">Horas</span>
                    <span class="d-none d-sm-block">Horas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-4" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Dia</span>
                    <span class="d-none d-sm-block">Dia</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-5" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Mes</span>
                    <span class="d-none d-sm-block">Mes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-7" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Personalizado</span>
                    <span class="d-none d-sm-block">Personalizado</span>
                </a>
            </li>
        </ul>
        <div class="tab-content p-3 text-muted">
            
            <div class="tab-pane" id="tabs-1" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEverySecond" name="cronSecond">
                        <label class="form-check-label" for="cronEverySecond">Every second</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronSecondIncrement" name="cronSecond">
                        <label class="form-check-label" for="cronSecondIncrement">
                            Every
                            <select id="cronSecondIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option>
                            </select> second(s) starting at second
                            <select id="cronSecondIncrementStart" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                            </select>
                        </label>
                    </div>
                    <div style="display: none">
                        <input type="radio" id="cronSecondSpecific" checked="checked" name="cronSecond">
                        <label for="cronSecondSpecific">Specific second (choose one or many)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond0" value="0" checked="">
                                    <label class="form-check-label" for="cronSecond0">00</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond1" value="1">
                                    <label class="form-check-label" for="cronSecond1">01</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond2" value="2">
                                    <label class="form-check-label" for="cronSecond2">02</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond3" value="3">
                                    <label class="form-check-label" for="cronSecond3">03</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond4" value="4">
                                    <label class="form-check-label" for="cronSecond4">04</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond5" value="5">
                                    <label class="form-check-label" for="cronSecond5">05</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond6" value="6">
                                    <label class="form-check-label" for="cronSecond6">06</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond7" value="7">
                                    <label class="form-check-label" for="cronSecond7">07</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond8" value="8">
                                    <label class="form-check-label" for="cronSecond8">08</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond9" value="9">
                                    <label class="form-check-label" for="cronSecond9">09</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond10" value="10">
                                    <label class="form-check-label" for="cronSecond10">10</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond11" value="11">
                                    <label class="form-check-label" for="cronSecond11">11</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond12" value="12">
                                    <label class="form-check-label" for="cronSecond12">12</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond13" value="13">
                                    <label class="form-check-label" for="cronSecond13">13</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond14" value="14">
                                    <label class="form-check-label" for="cronSecond14">14</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond15" value="15">
                                    <label class="form-check-label" for="cronSecond15">15</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond16" value="16">
                                    <label class="form-check-label" for="cronSecond16">16</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond17" value="17">
                                    <label class="form-check-label" for="cronSecond17">17</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond18" value="18">
                                    <label class="form-check-label" for="cronSecond18">18</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond19" value="19">
                                    <label class="form-check-label" for="cronSecond19">19</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond20" value="20">
                                    <label class="form-check-label" for="cronSecond20">20</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond21" value="21">
                                    <label class="form-check-label" for="cronSecond21">21</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond22" value="22">
                                    <label class="form-check-label" for="cronSecond22">22</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond23" value="23">
                                    <label class="form-check-label" for="cronSecond23">23</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond24" value="24">
                                    <label class="form-check-label" for="cronSecond24">24</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond25" value="25">
                                    <label class="form-check-label" for="cronSecond25">25</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond26" value="26">
                                    <label class="form-check-label" for="cronSecond26">26</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond27" value="27">
                                    <label class="form-check-label" for="cronSecond27">27</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond28" value="28">
                                    <label class="form-check-label" for="cronSecond28">28</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond29" value="29">
                                    <label class="form-check-label" for="cronSecond29">29</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond30" value="30">
                                    <label class="form-check-label" for="cronSecond30">30</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond31" value="31">
                                    <label class="form-check-label" for="cronSecond31">31</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond32" value="32">
                                    <label class="form-check-label" for="cronSecond32">32</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond33" value="33">
                                    <label class="form-check-label" for="cronSecond33">33</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond34" value="34">
                                    <label class="form-check-label" for="cronSecond34">34</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond35" value="35">
                                    <label class="form-check-label" for="cronSecond35">35</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond36" value="36">
                                    <label class="form-check-label" for="cronSecond36">36</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond37" value="37">
                                    <label class="form-check-label" for="cronSecond37">37</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond38" value="38">
                                    <label class="form-check-label" for="cronSecond38">38</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond39" value="39">
                                    <label class="form-check-label" for="cronSecond39">39</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond40" value="40">
                                    <label class="form-check-label" for="cronSecond40">40</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond41" value="41">
                                    <label class="form-check-label" for="cronSecond41">41</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond42" value="42">
                                    <label class="form-check-label" for="cronSecond42">42</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond43" value="43">
                                    <label class="form-check-label" for="cronSecond43">43</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond44" value="44">
                                    <label class="form-check-label" for="cronSecond44">44</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond45" value="45">
                                    <label class="form-check-label" for="cronSecond45">45</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond46" value="46">
                                    <label class="form-check-label" for="cronSecond46">46</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond47" value="47">
                                    <label class="form-check-label" for="cronSecond47">47</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond48" value="48">
                                    <label class="form-check-label" for="cronSecond48">48</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond49" value="49">
                                    <label class="form-check-label" for="cronSecond49">49</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond50" value="50">
                                    <label class="form-check-label" for="cronSecond50">50</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond51" value="51">
                                    <label class="form-check-label" for="cronSecond51">51</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond52" value="52">
                                    <label class="form-check-label" for="cronSecond52">52</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond53" value="53">
                                    <label class="form-check-label" for="cronSecond53">53</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond54" value="54">
                                    <label class="form-check-label" for="cronSecond54">54</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond55" value="55">
                                    <label class="form-check-label" for="cronSecond55">55</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond56" value="56">
                                    <label class="form-check-label" for="cronSecond56">56</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond57" value="57">
                                    <label class="form-check-label" for="cronSecond57">57</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond58" value="58">
                                    <label class="form-check-label" for="cronSecond58">58</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronSecondSpecificSpecific" type="checkbox" id="cronSecond59" value="59">
                                    <label class="form-check-label" for="cronSecond59">59</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronSecondRange" name="cronSecond">
                        <label class="form-check-label" for="cronSecondRange">
                            Every second between second
                            <select id="cronSecondRangeStart" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                            </select>
                            and second
                            <select id="cronSecondRangeEnd" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-2" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMinute" name="cronMinute">
                        <label class="form-check-label" for="cronEveryMinute">Cada minuto</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMinuteIncrement" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteIncrement">
                            Cada
                            <select id="cronMinuteIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 60; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                               
                            </select> minuto(s) empezando en el minuto
                            <select id="cronMinuteIncrementStart" style="width:50px;  display: inline-block;" class="form-control">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronMinuteSpecific" checked="checked" name="cronMinute">
                        <label for="cronMinuteSpecific">Minutos específicos (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 59; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute{{ $i }}" value="{{ $i }}" >
                                        <label class="form-check-label" for="cronMinute{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                                
                        </div>
                    </div>
                    <div class="form-check mb-3 mt-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMinuteRange" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteRange">
                            Cada minuto entre el minuto
                            <select id="cronMinuteRangeStart"  style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                                
                            </select>
                            y el minuto
                            <select id="cronMinuteRangeEnd"  style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 59; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-3" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryHour" name="cronHour">
                        <label class="form-check-label" for="cronEveryHour">Cada hora</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronHourIncrement" name="cronHour">
                        <label class="form-check-label" for="cronHourIncrement">
                            Cada
                            <select id="cronHourIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                                
                            </select> hora(s) empezando en la hora
                            <select id="cronHourIncrementStart" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronHourSpecific" checked="checked" name="cronHour">
                        <label for="cronHourSpecific">Hora específica (una o varias)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 23; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour{{ $i }}" value="{{ $i }}" >
                                        <label class="form-check-label" for="cronHour{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3 mt-3">
                        <input class="form-check-input mt-2" type="radio" id="cronHourRange" name="cronHour">
                        <label class="form-check-label" for="cronHourRange">
                            Cada hora entre la hora
                            <select id="cronHourRangeStart" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            y la hora
                            <select id="cronHourRangeEnd" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 0; $i <= 23; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-4" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryDay" name="cronDay" checked="">
                        <label class="form-check-label" for="cronEveryDay">Cada día</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDowIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDowIncrement">
                            Cada
                            <select id="cronDowIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
                            </select> dia(s) empezando en el
                            <select id="cronDowIncrementStart" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miercoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sabado</option>
                                <option value="7">Domingo</option>
                            </select>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDomIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDomIncrement">
                            Cada
                            <select id="cronDomIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select> dia(s) empezando en el dia
                            <select id="cronDomIncrementStart" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            del mes
                        </label>
                    </div>
                    <div class="mb-3">
                        <input class="form-check-input" type="radio" id="cronDowSpecific" name="cronDay">
                        <label for="cronDowSpecific">Día específico de la semana (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowMon" value="MON">
                                    <label class="form-check-label" for="cronDowMon">Lunes</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowTue" value="TUE">
                                    <label class="form-check-label" for="cronDowTue">Martes</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowWed" value="WED">
                                    <label class="form-check-label" for="cronDowWed">Miercoles</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowThu" value="THU">
                                    <label class="form-check-label" for="cronDowThu">Jueves</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowFri" value="FRI">
                                    <label class="form-check-label" for="cronDowFri">Viernes</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSat" value="SAT">
                                    <label class="form-check-label" for="cronDowSat">Sabado</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSun" value="SUN" >
                                    <label class="form-check-label" for="cronDowSun">Domingo</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input  class="form-check-input"  type="radio" id="cronDomSpecific" name="cronDay">
                        <label for="cronDomSpecific">Días específico del mes (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                @for ($i = 0; $i <= 23; $i++)
                                    @if($i!=0 && $i%10==0)
                                        </div><div class="row row-cols-lg-auto g-3 align-items-center">
                                    @endif
                                    <span style="width:10%">
                                        <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom{{ $i }}" value="{{ $i }}" >
                                        <label class="form-check-label" for="cronDom{{ $i }}">{{ lz($i,2) }}</label>
                                    </span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastDayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastDayOfMonth">El ultimo día del mes</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastWeekdayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastWeekdayOfMonth">El último dia entre semana del mes</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronLastSpecificDom" name="cronDay">
                        <label class="form-check-label" for="cronLastSpecificDom">
                            El ultimo
                            <select id="cronLastSpecificDomDay" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miercoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sabado</option>
                                <option value="7">Domingo</option>
                            </select>
                            del mes
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDaysBeforeEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysBeforeEom">
                            En los ultimos
                            <select id="cronDaysBeforeEomMinus" style="width:50px; display: inline-block;"  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select>
                            dia(s) antes del fin de mes
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronDaysNearestWeekdayEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysNearestWeekdayEom">
                            El días entre semana (Lunes to Viernes) mas cercano al
                            <select id="cronDaysNearestWeekday" style="width:50px; display: inline-block;"  class="form-control">
                                @for ($i = 1; $i <= 31; $i++)
                                <option value="{{ $i }}">{{ lz($i,2) }}</option>
                            @endfor
                            </select>
                            del mes
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronNthDay" name="cronDay">
                        <label class="form-check-label" for="cronNthDay">
                            En el
                            <select id="cronNthDayNth" style="width:100px; display: inline-block; "  class="form-control">
                                <option value="1">primer</option>
                                <option value="2">segundo</option>
                                <option value="3">tercer</option>
                                <option value="4">cuarto</option>
                                <option value="5">quinto</option>
                            </select>
                            <select id="cronNthDayDay" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1">Lunes</option>
                                <option value="2">Martes</option>
                                <option value="3">Miercoles</option>
                                <option value="4">Jueves</option>
                                <option value="5">Viernes</option>
                                <option value="6">Sabado</option>
                                <option value="7">Domingo</option>
                            </select>
                            del mes
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-5" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMonth" name="cronMonth" checked="">
                        <label class="form-check-label" for="cronEveryMonth">Cada mes</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMonthIncrement" name="cronMonth">
                        <label class="form-check-label" for="cronMonthIncrement">
                            Cada
                            <select id="cronMonthIncrementIncrement" style="width:50px; display: inline-block; "  class="form-control">
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ lz($i,2) }}</option>
                                @endfor
                            </select> mes(es) empezando en
                            <select id="cronMonthIncrementStart" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input  class="form-check-input"  type="radio" id="cronMonthSpecific" name="cronMonth">
                        <label for="cronMonthSpecific">Mes específico (uno o varios)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth1" value="JAN" selected="">
                                    <label class="form-check-label" for="cronMonth1">ENE</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth2" value="FEB">
                                    <label class="form-check-label" for="cronMonth2">FEB</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth3" value="MAR">
                                    <label class="form-check-label" for="cronMonth3">MAR</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth4" value="APR">
                                    <label class="form-check-label" for="cronMonth4">ABR</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth5" value="MAY">
                                    <label class="form-check-label" for="cronMonth5">MAY</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth6" value="JUN">
                                    <label class="form-check-label" for="cronMonth6">JUN</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth7" value="JUL">
                                    <label class="form-check-label" for="cronMonth7">JUL</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth8" value="AUG">
                                    <label class="form-check-label" for="cronMonth8">AGO</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth9" value="SEP">
                                    <label class="form-check-label" for="cronMonth9">SEP</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth10" value="OCT">
                                    <label class="form-check-label" for="cronMonth10">OCT</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth11" value="NOV">
                                    <label class="form-check-label" for="cronMonth11">NOV</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth12" value="DEC">
                                    <label class="form-check-label" for="cronMonth12">DIC</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input mt-2" type="radio" id="cronMonthRange" name="cronMonth">
                        <label class="form-check-label" for="cronMonthRange">
                            Cada mes entre
                            <select id="cronMonthRangeStart" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1" selected>Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                            y
                            <select id="cronMonthRangeEnd" style="width:125px; display: inline-block; "  class="form-control">
                                <option value="1" selected>Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            

            <div class="tab-pane" id="tabs-7" role="tabpanel">
                <div>
                    <div class="example">
                      <span class="clickable">random</span>
                    </div>
                    <div class="text-editor">
                        <input id="cron_input" type="text" class="" value="0 0 ? * * ">
                    </div>
                    <div class="warning"></div>
                    <div class="part-explanation">
                      <div class="cron-parts w-100 text-center">
                        <div style="left: 200px">
                            <div class="clickable" data-div="min">min</div>
                            <div class="clickable" data-div="hor">hora</div>
                            <div class="clickable" data-div="dia">dia<br>(mes)</div>
                            <div class="clickable" data-div="mes">mes</div>
                            <div class="clickable" data-div="sem">dia<br>(sem)</div>
                           
                        </div>
                    </div>
                    <div class="row w-100">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <table>
                                <tbody>
                                  <tr><td>*</td><td>cualquier valor</td></tr>
                                  <tr><td>,</td><td>separador de lista de valores</td></tr>
                                  <tr><td>-</td><td>rango de valores</td></tr>
                                  <tr><td>/</td><td>valores de paso</td></tr>
                                </tbody>
                                <tbody style="display: none" class="min">
                                  <tr><td nowrap><b>0-59</b></td><td>valores permitidos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="hor">
                                  <tr><td nowrap><b>0-23</td><td>valores permitidos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="dia">
                                  <tr><td nowrap><b>1-31</td><td>valores permitidos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="mes">
                                  <tr><td nowrap><b>1-12</td><td>valores permitidos</td></tr>
                                  <tr><td nowrap><b>JAN-DEC</td><td>Valores alternativos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="sem">
                                  <tr><td nowrap><b>0-6</td><td>valores permitidos</td></tr>
                                  <tr><td nowrap><b>SUN-SAT</td><td>Valores alternativos</td></tr>
                                </tbody>
                                <tbody style="display: none" class="ano">
                                    <tr><td nowrap><b>2022-2040</td><td>valores permitidos</td></tr>
                                  </tbody>
                            </table>
                        </div>
                    </div>
                      
                    </div>
                    <div style="margin-bottom: 10px"></div>
                  </div>
            </div>
        </div>

        <div>
            <h3 class="mb-2" style="color:#1b6ca8;text-align: center;">- Cron Expression -</h3>
            <h2 class="cronResult mb-2" style="text-align: center;background: aliceblue;padding: 10px;">0 * * ? *</h2>
            <h4 class="cronHuman mb-2" style="text-align: center;background: #baf2e7;padding: 10px;"></h4>
            <table class="table" style="text-align:center;">
                <thead>
                    <tr>
                        {{-- <th>Segundos</th> --}}
                        <th>Minutos</th>
                        <th>Horas</th>
                        <th>Dia mes</th>
                        <th>Mes</th>
                        <th>Dia semana</th>
                    </tr>
                </thead>
                <tbody style="font-weight: 400;font-size: large; overflow-wrap: anywhere;">
                    <tr>
                        {{-- <td><span id="cronResultSecond">0</span></td> --}}
                        <td><span id="cronResultMinute">*</span></td>
                        <td><span id="cronResultHour">*</span></td>
                        <td><span id="cronResultDom">?</span></td>
                        <td><span id="cronResultMonth">*</span></td>
                        <td><span id="cronResultDow">*</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- end card body -->
</div>

<script>
    function update_human(){
        console.log('human');
        $('.cronHuman').html((cronstrue.toString($('.cronResult').html(),{ use24HourTimeFormat: true })));
    }
    $(function () {
        $('#crontabs input, #crontabs select').change(_FF.cron);
        _FF.cron();
        cronstrue = window.cronstrue;
        update_human();
    });
    $('.clickable').hover(function(){
        $('.'+$(this).data('div')).toggle();
    })

    $('#cron_input').keyup(function(){
        console.log($(this).val());
        $('.cronResult').html($(this).val());
        partes =$(this).val().split(" ");
        $('#cronResultMinute').html(partes[0]);
        $('#cronResultHour').html(partes[1]);
        $('#cronResultDom').html(partes[2]);
        $('#cronResultMonth').html(partes[3]);
        $('#cronResultDow').html(partes[4]);
        update_human();
    })
</script>