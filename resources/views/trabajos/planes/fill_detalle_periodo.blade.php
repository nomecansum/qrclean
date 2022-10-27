<script src="{{ asset('/js/cron.js')}}" defer></script>
<div class="card mb-2">
    <div id="crontabs" class="card-body">
        <h2 id="fakeNumbers" class="card-title mb-3">Generador de expresion CRON - Quartz</h2>
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
                    <span class="d-block d-sm-none">Minutes</span>
                    <span class="d-none d-sm-block">Minutes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-3" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Hours</span>
                    <span class="d-none d-sm-block">Hours</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-4" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Day</span>
                    <span class="d-none d-sm-block">Day</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tabs-5" role="tab" aria-selected="true">
                    <span class="d-block d-sm-none">Month</span>
                    <span class="d-none d-sm-block">Month</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tabs-6" role="tab" aria-selected="false">
                    <span class="d-block d-sm-none">Year</span>
                    <span class="d-none d-sm-block">Year</span>
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
                        <label class="form-check-label" for="cronEveryMinute">Every minute</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronMinuteIncrement" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteIncrement">
                            Every
                            <select id="cronMinuteIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option>
                            </select> minute(s) starting at minute
                            <select id="cronMinuteIncrementStart" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input type="radio" id="cronMinuteSpecific" checked="checked" name="cronMinute">
                        <label for="cronMinuteSpecific">Specific minute (choose one or many)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute0" value="0" checked="">
                                    <label class="form-check-label" for="cronMinute0">00</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute1" value="1">
                                    <label class="form-check-label" for="cronMinute1">01</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute2" value="2">
                                    <label class="form-check-label" for="cronMinute2">02</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute3" value="3">
                                    <label class="form-check-label" for="cronMinute3">03</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute4" value="4">
                                    <label class="form-check-label" for="cronMinute4">04</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute5" value="5">
                                    <label class="form-check-label" for="cronMinute5">05</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute6" value="6">
                                    <label class="form-check-label" for="cronMinute6">06</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute7" value="7">
                                    <label class="form-check-label" for="cronMinute7">07</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute8" value="8">
                                    <label class="form-check-label" for="cronMinute8">08</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute9" value="9">
                                    <label class="form-check-label" for="cronMinute9">09</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute10" value="10">
                                    <label class="form-check-label" for="cronMinute10">10</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute11" value="11">
                                    <label class="form-check-label" for="cronMinute11">11</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute12" value="12">
                                    <label class="form-check-label" for="cronMinute12">12</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute13" value="13">
                                    <label class="form-check-label" for="cronMinute13">13</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute14" value="14">
                                    <label class="form-check-label" for="cronMinute14">14</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute15" value="15">
                                    <label class="form-check-label" for="cronMinute15">15</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute16" value="16">
                                    <label class="form-check-label" for="cronMinute16">16</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute17" value="17">
                                    <label class="form-check-label" for="cronMinute17">17</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute18" value="18">
                                    <label class="form-check-label" for="cronMinute18">18</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute19" value="19">
                                    <label class="form-check-label" for="cronMinute19">19</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute20" value="20">
                                    <label class="form-check-label" for="cronMinute20">20</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute21" value="21">
                                    <label class="form-check-label" for="cronMinute21">21</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute22" value="22">
                                    <label class="form-check-label" for="cronMinute22">22</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute23" value="23">
                                    <label class="form-check-label" for="cronMinute23">23</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute24" value="24">
                                    <label class="form-check-label" for="cronMinute24">24</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute25" value="25">
                                    <label class="form-check-label" for="cronMinute25">25</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute26" value="26">
                                    <label class="form-check-label" for="cronMinute26">26</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute27" value="27">
                                    <label class="form-check-label" for="cronMinute27">27</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute28" value="28">
                                    <label class="form-check-label" for="cronMinute28">28</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute29" value="29">
                                    <label class="form-check-label" for="cronMinute29">29</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute30" value="30">
                                    <label class="form-check-label" for="cronMinute30">30</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute31" value="31">
                                    <label class="form-check-label" for="cronMinute31">31</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute32" value="32">
                                    <label class="form-check-label" for="cronMinute32">32</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute33" value="33">
                                    <label class="form-check-label" for="cronMinute33">33</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute34" value="34">
                                    <label class="form-check-label" for="cronMinute34">34</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute35" value="35">
                                    <label class="form-check-label" for="cronMinute35">35</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute36" value="36">
                                    <label class="form-check-label" for="cronMinute36">36</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute37" value="37">
                                    <label class="form-check-label" for="cronMinute37">37</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute38" value="38">
                                    <label class="form-check-label" for="cronMinute38">38</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute39" value="39">
                                    <label class="form-check-label" for="cronMinute39">39</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute40" value="40">
                                    <label class="form-check-label" for="cronMinute40">40</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute41" value="41">
                                    <label class="form-check-label" for="cronMinute41">41</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute42" value="42">
                                    <label class="form-check-label" for="cronMinute42">42</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute43" value="43">
                                    <label class="form-check-label" for="cronMinute43">43</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute44" value="44">
                                    <label class="form-check-label" for="cronMinute44">44</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute45" value="45">
                                    <label class="form-check-label" for="cronMinute45">45</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute46" value="46">
                                    <label class="form-check-label" for="cronMinute46">46</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute47" value="47">
                                    <label class="form-check-label" for="cronMinute47">47</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute48" value="48">
                                    <label class="form-check-label" for="cronMinute48">48</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute49" value="49">
                                    <label class="form-check-label" for="cronMinute49">49</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute50" value="50">
                                    <label class="form-check-label" for="cronMinute50">50</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute51" value="51">
                                    <label class="form-check-label" for="cronMinute51">51</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute52" value="52">
                                    <label class="form-check-label" for="cronMinute52">52</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute53" value="53">
                                    <label class="form-check-label" for="cronMinute53">53</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute54" value="54">
                                    <label class="form-check-label" for="cronMinute54">54</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute55" value="55">
                                    <label class="form-check-label" for="cronMinute55">55</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute56" value="56">
                                    <label class="form-check-label" for="cronMinute56">56</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute57" value="57">
                                    <label class="form-check-label" for="cronMinute57">57</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute58" value="58">
                                    <label class="form-check-label" for="cronMinute58">58</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronMinuteSpecificSpecific" type="checkbox" id="cronMinute59" value="59">
                                    <label class="form-check-label" for="cronMinute59">59</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronMinuteRange" name="cronMinute">
                        <label class="form-check-label" for="cronMinuteRange">
                            Every minute between minute
                            <select id="cronMinuteRangeStart" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                            </select>
                            and minute
                            <select id="cronMinuteRangeEnd" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-3" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryHour" name="cronHour">
                        <label class="form-check-label" for="cronEveryHour">Every hour</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronHourIncrement" name="cronHour">
                        <label class="form-check-label" for="cronHourIncrement">
                            Every
                            <select id="cronHourIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option>
                            </select> hour(s) starting at hour
                            <select id="cronHourIncrementStart" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input type="radio" id="cronHourSpecific" checked="checked" name="cronHour">
                        <label for="cronHourSpecific">Specific hour (choose one or many)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour0" value="0" checked="">
                                    <label class="form-check-label" for="cronHour0">00</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour1" value="1">
                                    <label class="form-check-label" for="cronHour1">01</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour2" value="2">
                                    <label class="form-check-label" for="cronHour2">02</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour3" value="3">
                                    <label class="form-check-label" for="cronHour3">03</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour4" value="4">
                                    <label class="form-check-label" for="cronHour4">04</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour5" value="5">
                                    <label class="form-check-label" for="cronHour5">05</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour6" value="6">
                                    <label class="form-check-label" for="cronHour6">06</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour7" value="7">
                                    <label class="form-check-label" for="cronHour7">07</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour8" value="8">
                                    <label class="form-check-label" for="cronHour8">08</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour9" value="9">
                                    <label class="form-check-label" for="cronHour9">09</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour10" value="10">
                                    <label class="form-check-label" for="cronHour10">10</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour11" value="11">
                                    <label class="form-check-label" for="cronHour11">11</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour12" value="12">
                                    <label class="form-check-label" for="cronHour12">12</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour13" value="13">
                                    <label class="form-check-label" for="cronHour13">13</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour14" value="14">
                                    <label class="form-check-label" for="cronHour14">14</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour15" value="15">
                                    <label class="form-check-label" for="cronHour15">15</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour16" value="16">
                                    <label class="form-check-label" for="cronHour16">16</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour17" value="17">
                                    <label class="form-check-label" for="cronHour17">17</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour18" value="18">
                                    <label class="form-check-label" for="cronHour18">18</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour19" value="19">
                                    <label class="form-check-label" for="cronHour19">19</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour20" value="20">
                                    <label class="form-check-label" for="cronHour20">20</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour21" value="21">
                                    <label class="form-check-label" for="cronHour21">21</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour22" value="22">
                                    <label class="form-check-label" for="cronHour22">22</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronHourSpecificSpecific" type="checkbox" id="cronHour23" value="23">
                                    <label class="form-check-label" for="cronHour23">23</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronHourRange" name="cronHour">
                        <label class="form-check-label" for="cronHourRange">
                            Every hour between hour
                            <select id="cronHourRangeStart" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
                            </select>
                            and hour
                            <select id="cronHourRangeEnd" style="width:50px;">
                                <option value="0">00</option><option value="1">01</option><option value="2">02</option><option value="3">03</option><option value="4">04</option><option value="5">05</option><option value="6">06</option><option value="7">07</option><option value="8">08</option><option value="9">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-4" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryDay" name="cronDay" checked="">
                        <label class="form-check-label" for="cronEveryDay">Every day</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronDowIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDowIncrement">
                            Every
                            <select id="cronDowIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
                            </select> day(s) starting on
                            <select id="cronDowIncrementStart" style="width:125px;">
                                <option value="1">Sunday</option>
                                <option value="2">Monday</option>
                                <option value="3">Tuesday</option>
                                <option value="4">Wednesday</option>
                                <option value="5">Thursday</option>
                                <option value="6">Friday</option>
                                <option value="7">Saturday</option>
                            </select>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronDomIncrement" name="cronDay">
                        <label class="form-check-label" for="cronDomIncrement">
                            Every
                            <select id="cronDomIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
                            </select> day(s) starting on the
                            <select id="cronDomIncrementStart" style="width:50px;">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">5th</option>
                                <option value="6">6th</option>
                                <option value="7">7th</option>
                                <option value="8">8th</option>
                                <option value="9">9th</option>
                                <option value="10">10th</option>
                                <option value="11">11th</option>
                                <option value="12">12th</option>
                                <option value="13">13th</option>
                                <option value="14">14th</option>
                                <option value="15">15th</option>
                                <option value="16">16th</option>
                                <option value="17">17th</option>
                                <option value="18">18th</option>
                                <option value="19">19th</option>
                                <option value="20">20th</option>
                                <option value="21">21st</option>
                                <option value="22">22nd</option>
                                <option value="23">23rd</option>
                                <option value="24">24th</option>
                                <option value="25">25th</option>
                                <option value="26">26th</option>
                                <option value="27">27th</option>
                                <option value="28">28th</option>
                                <option value="29">29th</option>
                                <option value="30">30th</option>
                                <option value="31">31st</option>
                            </select>
                            of the month
                        </label>
                    </div>
                    <div class="mb-3">
                        <input type="radio" id="cronDowSpecific" name="cronDay">
                        <label for="cronDowSpecific">Specific day of the week (choose one or many)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSun" value="SUN" checked="">
                                    <label class="form-check-label" for="cronDowSun">Sunday</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowMon" value="MON">
                                    <label class="form-check-label" for="cronDowMon">Monday</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowTue" value="TUE">
                                    <label class="form-check-label" for="cronDowTue">Tuesday</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowWed" value="WED">
                                    <label class="form-check-label" for="cronDowWed">Wednesday</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowThu" value="THU">
                                    <label class="form-check-label" for="cronDowThu">Thursday</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowFri" value="FRI">
                                    <label class="form-check-label" for="cronDowFri">Friday</label>
                                </span>
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronDowSpecificSpecific" type="checkbox" id="cronDowSat" value="SAT">
                                    <label class="form-check-label" for="cronDowSat">Saturday</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="radio" id="cronDomSpecific" name="cronDay">
                        <label for="cronDomSpecific">Specific day of month (choose one or many)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom1" value="1" checked="">
                                    <label class="form-check-label" for="cronDom1">01</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom2" value="2">
                                    <label class="form-check-label" for="cronDom2">02</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom3" value="3">
                                    <label class="form-check-label" for="cronDom3">03</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom4" value="4">
                                    <label class="form-check-label" for="cronDom4">04</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom5" value="5">
                                    <label class="form-check-label" for="cronDom5">05</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom6" value="6">
                                    <label class="form-check-label" for="cronDom6">06</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom7" value="7">
                                    <label class="form-check-label" for="cronDom7">07</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom8" value="8">
                                    <label class="form-check-label" for="cronDom8">08</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom9" value="9">
                                    <label class="form-check-label" for="cronDom9">09</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom10" value="10">
                                    <label class="form-check-label" for="cronDom10">10</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom11" value="11">
                                    <label class="form-check-label" for="cronDom11">11</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom12" value="12">
                                    <label class="form-check-label" for="cronDom12">12</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom13" value="13">
                                    <label class="form-check-label" for="cronDom13">13</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom14" value="14">
                                    <label class="form-check-label" for="cronDom14">14</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom15" value="15">
                                    <label class="form-check-label" for="cronDom15">15</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom16" value="16">
                                    <label class="form-check-label" for="cronDom16">16</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom17" value="17">
                                    <label class="form-check-label" for="cronDom17">17</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom18" value="18">
                                    <label class="form-check-label" for="cronDom18">18</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom19" value="19">
                                    <label class="form-check-label" for="cronDom19">19</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom20" value="20">
                                    <label class="form-check-label" for="cronDom20">20</label>
                                </span>
                            </div>
                            <div class="row row-cols-lg-auto g-3 align-items-center">
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom21" value="21">
                                    <label class="form-check-label" for="cronDom21">21</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom22" value="22">
                                    <label class="form-check-label" for="cronDom22">22</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom23" value="23">
                                    <label class="form-check-label" for="cronDom23">23</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom24" value="24">
                                    <label class="form-check-label" for="cronDom24">24</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom25" value="25">
                                    <label class="form-check-label" for="cronDom25">25</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom26" value="26">
                                    <label class="form-check-label" for="cronDom26">26</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom27" value="27">
                                    <label class="form-check-label" for="cronDom27">27</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom28" value="28">
                                    <label class="form-check-label" for="cronDom28">28</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom29" value="29">
                                    <label class="form-check-label" for="cronDom29">29</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom30" value="30">
                                    <label class="form-check-label" for="cronDom30">30</label>
                                </span>
                                <span class="col-6p">
                                    <input class="form-check-input" name="cronDomSpecificSpecific" type="checkbox" id="cronDom31" value="31">
                                    <label class="form-check-label" for="cronDom31">31</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastDayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastDayOfMonth">On the last day of the month</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastWeekdayOfMonth" name="cronDay">
                        <label class="form-check-label" for="cronLastWeekdayOfMonth">On the last weekday of the month</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronLastSpecificDom" name="cronDay">
                        <label class="form-check-label" for="cronLastSpecificDom">
                            On the last
                            <select id="cronLastSpecificDomDay" style="width:125px;">
                                <option value="1">Sunday</option>
                                <option value="2">Monday</option>
                                <option value="3">Tuesday</option>
                                <option value="4">Wednesday</option>
                                <option value="5">Thursday</option>
                                <option value="6">Friday</option>
                                <option value="7">Saturday</option>
                            </select>
                            of the month
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronDaysBeforeEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysBeforeEom">
                            On the last
                            <select id="cronDaysBeforeEomMinus" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option>
                            </select>
                            day(s) before the end of the month
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronDaysNearestWeekdayEom" name="cronDay">
                        <label class="form-check-label" for="cronDaysNearestWeekdayEom">
                            Nearest weekday (Monday to Friday) to the
                            <select id="cronDaysNearestWeekday" style="width:50px;">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">5th</option>
                                <option value="6">6th</option>
                                <option value="7">7th</option>
                                <option value="8">8th</option>
                                <option value="9">9th</option>
                                <option value="10">10th</option>
                                <option value="11">11th</option>
                                <option value="12">12th</option>
                                <option value="13">13th</option>
                                <option value="14">14th</option>
                                <option value="15">15th</option>
                                <option value="16">16th</option>
                                <option value="17">17th</option>
                                <option value="18">18th</option>
                                <option value="19">19th</option>
                                <option value="20">20th</option>
                                <option value="21">21st</option>
                                <option value="22">22nd</option>
                                <option value="23">23rd</option>
                                <option value="24">24th</option>
                                <option value="25">25th</option>
                                <option value="26">26th</option>
                                <option value="27">27th</option>
                                <option value="28">28th</option>
                                <option value="29">29th</option>
                                <option value="30">30th</option>
                                <option value="31">31st</option>
                            </select>
                            of the month
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronNthDay" name="cronDay">
                        <label class="form-check-label" for="cronNthDay">
                            On the
                            <select id="cronNthDayNth" style="width:50px;">
                                <option value="1">1st</option>
                                <option value="2">2nd</option>
                                <option value="3">3rd</option>
                                <option value="4">4th</option>
                                <option value="5">5th</option>
                            </select>
                            <select id="cronNthDayDay" style="width:125px;">
                                <option value="1">Sunday</option>
                                <option value="2">Monday</option>
                                <option value="3">Tuesday</option>
                                <option value="4">Wednesday</option>
                                <option value="5">Thursday</option>
                                <option value="6">Friday</option>
                                <option value="7">Saturday</option>
                            </select>
                            of the month
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane active" id="tabs-5" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryMonth" name="cronMonth" checked="">
                        <label class="form-check-label" for="cronEveryMonth">Every month</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronMonthIncrement" name="cronMonth">
                        <label class="form-check-label" for="cronMonthIncrement">
                            Every
                            <select id="cronMonthIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option>
                            </select> month(s) starting in
                            <select id="cronMonthIncrementStart" style="width:125px;">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input type="radio" id="cronMonthSpecific" name="cronMonth">
                        <label for="cronMonthSpecific">Specific month (choose one or many)</label>
                        <div style="margin-left:20px;">
                            <div class="row row-cols-lg-auto g-3 align-items-center mb-3">
                                <span class="col-sm-1">
                                    <input class="form-check-input" name="cronMonthSpecificSpecific" type="checkbox" id="cronMonth1" value="JAN" selected="">
                                    <label class="form-check-label" for="cronMonth1">JAN</label>
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
                                    <label class="form-check-label" for="cronMonth4">APR</label>
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
                                    <label class="form-check-label" for="cronMonth8">AUG</label>
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
                                    <label class="form-check-label" for="cronMonth12">DEC</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronMonthRange" name="cronMonth">
                        <label class="form-check-label" for="cronMonthRange">
                            Every month between
                            <select id="cronMonthRangeStart" style="width:125px;">
                                <option value="1" selected="">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                            and
                            <select id="cronMonthRangeEnd" style="width:125px;">
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12" selected="">December</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="tabs-6" role="tabpanel">
                <div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronEveryYear" name="cronYear" checked="">
                        <label class="form-check-label" for="cronEveryYear">Any year</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronYearIncrement" name="cronYear">
                        <label class="form-check-label" for="cronYearIncrement">
                            Every
                            <select id="cronYearIncrementIncrement" style="width:50px;">
                                <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option>
                            </select> years(s) starting in
                            <select id="cronYearIncrementStart" style="width:80px;">
                                <option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029">2029</option><option value="2030">2030</option><option value="2031">2031</option><option value="2032">2032</option><option value="2033">2033</option><option value="2034">2034</option><option value="2035">2035</option><option value="2036">2036</option><option value="2037">2037</option><option value="2038">2038</option><option value="2039">2039</option><option value="2040">2040</option><option value="2041">2041</option><option value="2042">2042</option><option value="2043">2043</option><option value="2044">2044</option><option value="2045">2045</option><option value="2046">2046</option><option value="2047">2047</option><option value="2048">2048</option><option value="2049">2049</option><option value="2050">2050</option>
                            </select>
                        </label>
                    </div>
                    <div>
                        <input type="radio" id="cronYearSpecific" name="cronYear">
                        <label for="cronYearSpecific">Specific year (choose one or many)</label>
                        <div style="margin-left:20px;">
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2020" value="2020">
                                    <label class="form-check-label" for="cronYear2020">2020</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2021" value="2021">
                                    <label class="form-check-label" for="cronYear2021">2021</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2022" value="2022">
                                    <label class="form-check-label" for="cronYear2022">2022</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2023" value="2023">
                                    <label class="form-check-label" for="cronYear2023">2023</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2024" value="2024">
                                    <label class="form-check-label" for="cronYear2024">2024</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2025" value="2025">
                                    <label class="form-check-label" for="cronYear2025">2025</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2026" value="2026">
                                    <label class="form-check-label" for="cronYear2026">2026</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2027" value="2027">
                                    <label class="form-check-label" for="cronYear2027">2027</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2028" value="2028">
                                    <label class="form-check-label" for="cronYear2028">2028</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2029" value="2029">
                                    <label class="form-check-label" for="cronYear2029">2029</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2030" value="2030">
                                    <label class="form-check-label" for="cronYear2030">2030</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2031" value="2031">
                                    <label class="form-check-label" for="cronYear2031">2031</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2032" value="2032">
                                    <label class="form-check-label" for="cronYear2032">2032</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2033" value="2033">
                                    <label class="form-check-label" for="cronYear2033">2033</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2034" value="2034">
                                    <label class="form-check-label" for="cronYear2034">2034</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2035" value="2035">
                                    <label class="form-check-label" for="cronYear2035">2035</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2036" value="2036">
                                    <label class="form-check-label" for="cronYear2036">2036</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2037" value="2037">
                                    <label class="form-check-label" for="cronYear2037">2037</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2038" value="2038">
                                    <label class="form-check-label" for="cronYear2038">2038</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2039" value="2039">
                                    <label class="form-check-label" for="cronYear2039">2039</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2040" value="2040">
                                    <label class="form-check-label" for="cronYear2040">2040</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2041" value="2041">
                                    <label class="form-check-label" for="cronYear2041">2041</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2042" value="2042">
                                    <label class="form-check-label" for="cronYear2042">2042</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2043" value="2043">
                                    <label class="form-check-label" for="cronYear2043">2043</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2044" value="2044">
                                    <label class="form-check-label" for="cronYear2044">2044</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2045" value="2045">
                                    <label class="form-check-label" for="cronYear2045">2045</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2046" value="2046">
                                    <label class="form-check-label" for="cronYear2046">2046</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2047" value="2047">
                                    <label class="form-check-label" for="cronYear2047">2047</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2048" value="2048">
                                    <label class="form-check-label" for="cronYear2048">2048</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2049" value="2049">
                                    <label class="form-check-label" for="cronYear2049">2049</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2050" value="2050">
                                    <label class="form-check-label" for="cronYear2050">2050</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2051" value="2051">
                                    <label class="form-check-label" for="cronYear2051">2051</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2052" value="2052">
                                    <label class="form-check-label" for="cronYear2052">2052</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2053" value="2053">
                                    <label class="form-check-label" for="cronYear2053">2053</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2054" value="2054">
                                    <label class="form-check-label" for="cronYear2054">2054</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2055" value="2055">
                                    <label class="form-check-label" for="cronYear2055">2055</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2056" value="2056">
                                    <label class="form-check-label" for="cronYear2056">2056</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2057" value="2057">
                                    <label class="form-check-label" for="cronYear2057">2057</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2058" value="2058">
                                    <label class="form-check-label" for="cronYear2058">2058</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2059" value="2059">
                                    <label class="form-check-label" for="cronYear2059">2059</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2060" value="2060">
                                    <label class="form-check-label" for="cronYear2060">2060</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2061" value="2061">
                                    <label class="form-check-label" for="cronYear2061">2061</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2062" value="2062">
                                    <label class="form-check-label" for="cronYear2062">2062</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2063" value="2063">
                                    <label class="form-check-label" for="cronYear2063">2063</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2064" value="2064">
                                    <label class="form-check-label" for="cronYear2064">2064</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2065" value="2065">
                                    <label class="form-check-label" for="cronYear2065">2065</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2066" value="2066">
                                    <label class="form-check-label" for="cronYear2066">2066</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2067" value="2067">
                                    <label class="form-check-label" for="cronYear2067">2067</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2068" value="2068">
                                    <label class="form-check-label" for="cronYear2068">2068</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2069" value="2069">
                                    <label class="form-check-label" for="cronYear2069">2069</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2070" value="2070">
                                    <label class="form-check-label" for="cronYear2070">2070</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2071" value="2071">
                                    <label class="form-check-label" for="cronYear2071">2071</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2072" value="2072">
                                    <label class="form-check-label" for="cronYear2072">2072</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2073" value="2073">
                                    <label class="form-check-label" for="cronYear2073">2073</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2074" value="2074">
                                    <label class="form-check-label" for="cronYear2074">2074</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2075" value="2075">
                                    <label class="form-check-label" for="cronYear2075">2075</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2076" value="2076">
                                    <label class="form-check-label" for="cronYear2076">2076</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2077" value="2077">
                                    <label class="form-check-label" for="cronYear2077">2077</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2078" value="2078">
                                    <label class="form-check-label" for="cronYear2078">2078</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2079" value="2079">
                                    <label class="form-check-label" for="cronYear2079">2079</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2080" value="2080">
                                    <label class="form-check-label" for="cronYear2080">2080</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2081" value="2081">
                                    <label class="form-check-label" for="cronYear2081">2081</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2082" value="2082">
                                    <label class="form-check-label" for="cronYear2082">2082</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2083" value="2083">
                                    <label class="form-check-label" for="cronYear2083">2083</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2084" value="2084">
                                    <label class="form-check-label" for="cronYear2084">2084</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2085" value="2085">
                                    <label class="form-check-label" for="cronYear2085">2085</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2086" value="2086">
                                    <label class="form-check-label" for="cronYear2086">2086</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2087" value="2087">
                                    <label class="form-check-label" for="cronYear2087">2087</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2088" value="2088">
                                    <label class="form-check-label" for="cronYear2088">2088</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2089" value="2089">
                                    <label class="form-check-label" for="cronYear2089">2089</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2090" value="2090">
                                    <label class="form-check-label" for="cronYear2090">2090</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2091" value="2091">
                                    <label class="form-check-label" for="cronYear2091">2091</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2092" value="2092">
                                    <label class="form-check-label" for="cronYear2092">2092</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2093" value="2093">
                                    <label class="form-check-label" for="cronYear2093">2093</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2094" value="2094">
                                    <label class="form-check-label" for="cronYear2094">2094</label>
                                </span>
                                
                                    </div>
                                
                            
                                
                                    <div class="row">
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2095" value="2095">
                                    <label class="form-check-label" for="cronYear2095">2095</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2096" value="2096">
                                    <label class="form-check-label" for="cronYear2096">2096</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2097" value="2097">
                                    <label class="form-check-label" for="cronYear2097">2097</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2098" value="2098">
                                    <label class="form-check-label" for="cronYear2098">2098</label>
                                </span>
                                
                            
                                
                                <span class="col-2">
                                    <input class="form-check-input" name="cronYearSpecificSpecific" type="checkbox" id="cronYear2099" value="2099">
                                    <label class="form-check-label" for="cronYear2099">2099</label>
                                </span>
                                
                                    </div>
                                
                            
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" id="cronYearRange" name="cronYear">
                        <label class="form-check-label" for="cronYearRange">
                            Every year between
                            <select id="cronYearRangeStart" style="width:80px;">
                                <option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029">2029</option><option value="2030">2030</option><option value="2031">2031</option><option value="2032">2032</option><option value="2033">2033</option><option value="2034">2034</option><option value="2035">2035</option><option value="2036">2036</option><option value="2037">2037</option><option value="2038">2038</option><option value="2039">2039</option><option value="2040">2040</option><option value="2041">2041</option><option value="2042">2042</option><option value="2043">2043</option><option value="2044">2044</option><option value="2045">2045</option><option value="2046">2046</option><option value="2047">2047</option><option value="2048">2048</option><option value="2049">2049</option><option value="2050">2050</option>
                            </select>
                            and
                            <select id="cronYearRangeEnd" style="width:80px;">
                                <option value="2022">2022</option><option value="2023">2023</option><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029">2029</option><option value="2030">2030</option><option value="2031">2031</option><option value="2032">2032</option><option value="2033">2033</option><option value="2034">2034</option><option value="2035">2035</option><option value="2036">2036</option><option value="2037">2037</option><option value="2038">2038</option><option value="2039">2039</option><option value="2040">2040</option><option value="2041">2041</option><option value="2042">2042</option><option value="2043">2043</option><option value="2044">2044</option><option value="2045">2045</option><option value="2046">2046</option><option value="2047">2047</option><option value="2048">2048</option><option value="2049">2049</option><option value="2050">2050</option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div style="">
            <h3 class="mb-2" style="color:#1b6ca8;text-align: center;">- Cron Expression -</h3>
            <h2 class="cronResult mb-2" style="text-align: center;background: aliceblue;padding: 10px;">0 0 3-10 ? * * *</h2>
            <table class="table" style="text-align:center;">
                <thead>
                    <tr>
                        <th>Seconds</th>
                        <th>Minutes</th>
                        <th>Hours</th>
                        <th>Day Of Month</th>
                        <th>Month</th>
                        <th>Day Of Week</th>
                        <th>Year</th>
                    </tr>
                </thead>
                <tbody style="font-weight: 400;font-size: large; overflow-wrap: anywhere;">
                    <tr>
                        <td><span id="cronResultSecond">0</span></td>
                        <td><span id="cronResultMinute">0</span></td>
                        <td><span id="cronResultHour">3-10</span></td>
                        <td><span id="cronResultDom">?</span></td>
                        <td><span id="cronResultMonth">*</span></td>
                        <td><span id="cronResultDow">*</span></td>
                        <td><span id="cronResultYear">*</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- end card body -->
</div>

<script>
    $(function () {
        $('#crontabs input, #crontabs select').change(_FF.cron);
        _FF.cron();
    });
</script>