/***
@title:
CenterOn

@version:
1.0

@author:
Felipe Cano

@date:
2010-06-27

@url:

@license:
http://creativecommons.org/licenses/by/3.0/

@copyright:

@requires:
jquery

@does:
This little pluggy centers an element on the screen using either fixed or absolute positioning. Can be used to display messages, pop up images etc.

@howto:
jQuery('#my-element').centerOn(jQuery('#my-element'),true); would center the element with ID 'my-element' using absolute position (leave empty for fixed).

@exampleHTML:
<p>I should be fixed centered</p>

<p>The paragraph above and the paragraph beneath this one are centered. They should be in the middle of the viewport.</p>

<p>I should be absolutely centered</p>

@exampleJS:
jQuery('#jquery-center-example p:first-child').center();
jQuery('#jquery-center-example p:last-child').center(true);
***/
jQuery.fn.centerOn = function (obj) {
	return this.each(function () {
		var t = jQuery(this);
		t.css({
			position:'absolute', 
		});
		var pos=obj.position();
		var offset=obj.offset()
		t.offset({ top: offset.top+(obj.outerHeight()/2)-(t.outerHeight() / 2), left: offset.left+(obj.outerWidth()/2)-(t.outerWidth() / 2)});
	});
};
