/**
 * @license 
 * Copyright (c) 2018, Immo Schulz-Gerlach, www.isg-software.de 
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are 
 * permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT 
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED 
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


( function($) {

	var drawText = function(sValue, sUnit, args) {
		var opts = $.extend({}, $.fn.progressPie.contentPlugin.valueDisplayDefaults, args);
		var text = args.newSvgElement("text");
		text.setAttribute("x", 0);
		text.setAttribute("y", 0);
		text.setAttribute("dy", "0.33em");
		text.textContent = sValue;
		var fsFactor = typeof args.fontSizeFactor === 'number' ? args.fontSizeFactor 
					: opts.singleLine ? opts.fontSizeFactorSingleLine : opts.fontSizeFactorTwoLines;
		//Set style attribute manually (instead of using the style properties like text.style.textAnchor etc.) for better browser support.
		//(At least Firefox 38 did not support the latter.)
		text.style.textAnchor = "middle";
		text.style.fontSize = opts.radius * fsFactor + "px";
		if (typeof args.color === "string") { //not in CSS mode.
			text.setAttribute("fill", opts.color);
		}
		text.setAttribute("class", opts.cssClass);
		if (typeof sUnit === 'string') {
			var text2 = args.newSvgSubelement(text, "tspan");
			text2.textContent = sUnit;
			if (!opts.singleLine) {
				text2.setAttribute("dy", "1.1em");
				text2.setAttribute("x", 0);
			}
			var fsFactor2 = typeof opts.unitFontSizeFactor === 'number' ? opts.unitFontSizeFactor : 0.35;
			text2.style.fontSize = opts.radius * fsFactor2 + "px";
			text2.setAttribute("class", opts.cssClassUnit);
		}
	};
	
	/**
	 * SVG Content Plug-in for jquery-progresspiesvg: Display the percent value (rounded to integer) with a "%" label inside
	 * a ring graph rendered by the progressPie component.
	 * <p>Use by adding the option <code>contentPlugin: "percent"</code> (or <code>contentPlugin: $.fn.progressPie.contentPlugin.percent</code>)
	 * to your call of the progresspie plug-in.
	 * <p>Additional arguments may be supplied by adding the option <code>contentPluginOptions</code> to the progressPie plugin options.
	 * This is to be an object which may hold the following properties:</p>
	 * <ul>
	 * <li><code>singleLine</code>: Default is "undefinied". If truthy, the unit label ("%" in this case) 
	 * is displayed in the same line <em>behind</em> the percent value, otherwise in a new line below the value.</li>
	 * <li><code>fontSizeFactor</code>: Number, Default is 1.0 (0.9 in singleLine mode). The font-size for the percent number inside the ring is the inner radius
	 * of the ring multiplied by this factor. E.g. to shrink the text by 10%, set fontSizeFactor to 0.9. A factor larger than 1.0 is not recommended, because
	 * the text (especially a "100") might not fit into the graphic!</li>
	 * <li><code>unitFontSizeFactor</code>: Number, Default is 0.35. Defines the font size for the unit label ("%"),
	 * see <code>fontSizeFactor</code>.</li>
	 * <li><code>color</code>: color code (string). Defaults to the color of the ring graph (except in CSS mode, where color defaults
	 * to undefined, in which case no font color will be set in the SVG itself as long as this property is not set, leaving
	 * the font color subject to CSS stylesheets). 
	 * Override this option in order to draw content in different color than the surrounding ring.</li>
	 * <li><code>cssClass</code>: String. Value for the class attribute. Especially in CSS mode the class enables you to
	 * style the font color via CSS. This option can be used to override the default class name ("progresspie-valuedisplay").</li>
	 * <li><code>cssClassUnit</code>: String. If the value display contains a unit label (as sub-element of the text element), 
	 * this unit is fitted with a class name, too. So you can style the unit string differently from the value display itself. 
	 * Use this option to override the default class name ("progresspie-unit").</li>
	 * </ul>
	 * <p>Please note: This function is called <em>internally</em> by the progressPie jQuery plug-in! Don't call this function directly,
	 * but use it as desrcibed above!</p>
	 * @function precent
	 * @param {object} args object holding several arguments provided by the progressPie plug-in, including any option you specified in
	 * the object <code>contentPluginOptions</code>.
	 * @memberof jQuery.fn.progressPie.contentPlugin
	 * @requires jquery-progresspiesvg-min.js
	 */
	$.fn.progressPie.contentPlugin.percent = function(args) {
		drawText(Math.round(args.percentValue), "%", args);
	};
	
	/**
	 * SVG Content Plug-in for jquery-progresspiesvg: Display the the raw value of a ring graph (before translation to a percent
	 * value via a valueAdapter function) inside the ring graph, optionally combined with a second string, intended as a unit label for the value.
	 * <p>Use by adding the option <code>contentPlugin: "rawValue"</code> (or <code>contentPlugin: $.fn.progressPie.contentPlugin.rawValue</code>)
	 * to your call of the progresspie plug-in.
	 * <p>Additional arguments may be supplied by adding the option <code>contentPluginOptions</code> to the progressPie plugin options.
	 * This is to be an object which may hold the following properties:</p>
	 * <ul>
	 * <li><code>unit</code>: String, default is "undefined". If defined <code>(typeof unit === "string")</code>, this string will
	 * be displayed behind or below the value. It should be a very short string holding a unit label like "s" or "sec.".
	 * If not of type string (especially if left undefined), the value will be the sole content.</li>
	 * <li><code>singleLine</code>: Default is "undefinied". If truthy, the unit label (see above) 
	 * is displayed in the same line <em>behind</em> the percent value, otherwise in a new line below the value.</li>
	 * <li><code>fontSizeFactor</code>: Number, Default is 1.0 (0.9 in singleLine mode). The font-size for the percent number inside the ring is the inner radius
	 * of the ring multiplied by this factor. E.g. to shrink the text by 10%, set fontSizeFactor to 0.9. A factor larger than 1.0 is not recommended, because
	 * the text (especially a "100") might not fit into the graphic!</li>
	 * <li><code>unitFontSizeFactor</code>: Number, Default is 0.35. Defines the font size for the unit label,
	 * see <code>fontSizeFactor</code>.</li>
	 * <li><code>color</code>: color code (string). Defaults to the color of the ring graph. Overwrite to draw content in different color
	 * than the surrounding ring.</li>
	 * </ul>
	 * <p>Please note: This function is called <em>internally</em> by the progressPie jQuery plug-in! Don't call this function directly,
	 * but use it as desrcibed above!</p>
	 * @function rawValue
	 * @param {object} args object holding several arguments provided by the progressPie plug-in, including any option you specified in
	 * the object <code>contentPluginOptions</code>.
	 * @memberof jQuery.fn.progressPie.contentPlugin
	 * @requires jquery-progresspiesvg-min.js
	 */
	$.fn.progressPie.contentPlugin.rawValue = function(args) {
		drawText(args.rawValue, args.unit, args);
	};
	
	/**
	 * Default Options.
	 * This is a public (static) object in order to allow users to globally modify the defaults
	 * before using the <code>valueDisplay</code> content plug-ins.
	 * @member valueDisplayDefaults
	 * @memberof jQuery.fn.progressPie.contentPlugin
	 * @property {boolean} singleLine - defaults to false, meaning an (optional) unit label is displayed below the value.
	 * If true, value and unit are displayed in the same line.
	 * @property {number} fontSizeFactorTwoLines - Default value for the fontSizeFactor option in case singleLine === false.
	 * Defaults to 1.0. The fontSizeFactor value is multiplied by the inner radius to get the font size for the value display.
	 * @property {number} fontSizeFactorSingleLine - Default value for the fontSizeFactor option in case singleLine === true.
	 * Defaults to 0.9
	 * @property {number} unitFontSizeFactor - default fontSizeFactor for the size of the unit label. 
	 * Defaults to 0.35
	 * @property {string} cssClass - value of the class attribute to define a CSS class name for the text element holding
	 * the whole text output (value and unit). Defaults to "progresspie-valuedisplay"
	 * @property {string} cssClassUnit - name of a CSS class to be added to the (optional) tspan element showing the
	 * unit of the displayed value. Defaults to "progresspie-unit".
	 */
	$.fn.progressPie.contentPlugin.valueDisplayDefaults = {
		singleLine: false,
		fontSizeFactorTwoLines: 1.0,
		fontSizeFactorSingleLine: 0.9,
		unitFontSizeFactor: 0.35,
		cssClass: "progresspie-valuedisplay",
		cssClassUnit: "progresspie-unit"
	};


} (jQuery));

