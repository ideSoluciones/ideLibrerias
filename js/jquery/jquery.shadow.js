/*
Jquery plugin name : Drop shadow v 0.1
description : dropshadow on mouse over then delete is after mouseout
author : Emad Elsaid
Date : Feb 15 2010 
Application : Dreamweaver CS4
paramters : 
x : shadow x ofsset default:0
y : shadow y offset default:0
blur : shadow blur amount default:10
color : shadow color default:#999
*/
(function($) {
 
   $.fn.shadow = function(settings) {
     var config = $(settings).extend({x:0,y:0,blur:10,color:'#999'},settings);
 
     if (settings) $.extend(config, settings);
 
     this.each(function() {
		$(this).css('-webkit-box-shadow',config.x+'px '+config.y+'px '+config.blur+'px '+config.color);
		$(this).css('-moz-box-shadow',config.x+'px '+config.y+'px '+config.blur+'px '+config.color);
     });
 
     return this;
 
   };
 
 })(jQuery);
