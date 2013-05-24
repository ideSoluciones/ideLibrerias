/**
 * jQuery-Plugin "relCopy"
 * 
 * @version: 1.0.0, 15.12.2009
 * 
 * @author: Andrés Pérez
 *			andresf89@yahoo.com.mx
 *			http://www.idesoluciones.com
 *
 * @author: 
 * Basado en la librería de:
 *			Andres Vidal
 *          code@andresvidal.com
 *          http://www.andresvidal.com
 *
 * Instructions: Call $(selector).relCopy(options) on an element with a jQuery type selector 
 * defined in the attribute "rel" tag. This defines the DOM element to copy.
 * @example: $('a.copy').relCopy({limit: 5}); // <a href="example.com" class="copy" rel=".phone">Copy Phone</a>
 *
 * Instrucciones: Llamar a $(selector).relCopy(options) en un elemento con un selector tipo jQuery
 * definido en el atributo "rel" de un tag. Esto define el elemento DOM a copiar.
 * @example: $('a.copy').relCopy({limit: 5}); // <a href="#" class="copy" rel=".phone">Copy Phone</a>
 *
 * @param: string	excludeSelector - A jQuery selector used to exclude an element and its children
 * @param: integer	limit - The number of allowed copies. Default: 0 is unlimited
 * @param: string	append - HTML to attach at the end of each copy. Default: remove link
 * @param: string	copyClass - A class to attach to each copy
 * @param: boolean	clearInputs - Option to clear each copies text input fields or textarea
 * 
 */

(function($) {
	var contadoresCopias=new Array();
	$.fn.relCopy = function(options) {
		var settings = jQuery.extend({
			excludeSelector: ".exclude",
			emptySelector: ".empty",
			copyClass: "copy",
			append: '<a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false">remove</a>',
			clearInputs: true,
			limit: 0, // 0 = unlimited
			funcionPre: "",
			funcionPost: ""
		}, options);
		
		settings.limit = parseInt(settings.limit);
		
		// loop each element
		this.each(function() {
			
			// set click action
			$(this).click(function(){
				var rel = $(this).attr('rel'); // rel in jquery selector format	
				if (contadoresCopias[rel]==undefined)
					contadoresCopias[rel]=1;
				else
					contadoresCopias[rel]++;			
				var counter = contadoresCopias[rel];
				
				// stop limit
				if (settings.limit != 0 && counter >= settings.limit){
					return false;
				};

				var master = $(rel+":first");
				var parent = $(master).parent();							


				if (settings.funcionPre){
					//alert(settings.funcion+"('"+clone.attr('id')+"')");
					eval (settings.funcionPre+"('"+master.attr('id')+"')");
				}else{
					//alert("settings.funcion NO SETEADO");
				}

				var clone = $(master).clone(true).append(settings.append);	

				

				//Remove Elements with excludeSelector
				if (settings.excludeSelector){
					$(clone).find(settings.excludeSelector).remove();
				};

				//Empty Elements with emptySelector
				if (settings.emptySelector){
					$(clone).find(settings.emptySelector).empty();
				};								

				// Revisar a profundidad
				incrementarIdsNames(clone,counter);

				//Clear Inputs/Textarea
				if (settings.clearInputs){
					$(clone).find('input:text, textarea').each(function(){
						$(this).val("");						  
					});					
				};

				$(parent).find(rel+':last').after(clone);

				if (settings.funcionPost){
					//alert(settings.funcion+"('"+clone.attr('id')+"')");
					eval (settings.funcionPost+"('"+clone.attr('id')+"')");
				}else{
					//alert("settings.funcion NO SETEADO");
				}

				return false;

			}); // end click action

		}); //end each loop
		
		return this; // return to jQuery
	};
	
})(jQuery);
