$.fn.generarEntornoXML = function(opciones)
{
	var defaults = {  
		length: 300,  
		minTrail: 20,  
		moreText: "more",  
		lessText: "less",  
		ellipsisText: "..."  
	};
	var options = $.extend(defaults, opciones);  
	return this.each(function (){
		obj = $(this);  
		alert("Aun no implementado "+" , "+obj.attr("id")+" con "+opciones);
		//alert("Aun no implementado "+" , "+obj.attr("id"));
		//alert("Fue construido "+opciones);
		$(obj).css("background", "#F99" );
		$(obj).css("height", "100%" );
		$(obj).css("width", "100%" );
		


		xml = (new DOMParser()).parseFromString(opciones, "text/xml");

		alert("opciones.length="+xml.childNodes.length);
		
		 for (var iNode = 0; iNode < xml.childNodes.length; iNode++) {
			 var node = xml.childNodes.item(iNode);
			 alert(node.nodeName+" - "+node.attributes.getNamedItem("nombre").value);


		 	
		 }

		
		
		/*for (i=0; i < opciones.length; i++)
		{
			alert(opciones[i]);
			/*
			var li = document.createElement('LI');
			for (j=0; j < labels[i].childNodes.length; j++)
			{
				if (labels[i].childNodes[j].nodeType != ELEMENT_NODE) continue;
				var cdata = document.createTextNode(
				labels[i].childNodes[j].firstChild.nodeValue);
				li.appendChild(cdata);
			}
			var labelId = document.createTextNode('(' +
			labels[i].getAttribute('id') + ')');
			li.appendChild(labelId);
			ol.appendChild(li);
			*/
	//	}

		
		
		
		//@TODO no se encuentra como saber sobre quien fue llamado esta función
	});

}
/*
<Especificacion nombre="texto" titulo="TextoXML" inicial="Artefacto">
	<Nodo nombre="Artefacto" etiqueta="Artefacto" formatoHijos="1">
		<Propiedad nombre="Fecha" valor="2009-12-15" formato="fecha"/>
		<Hijo nombre="Propuesta Inicial" cantidad="1"/>
		<Hijo nombre="Descripci&#xF3;n Proyecto" cantidad="1"/>
	</Nodo>
	<Nodo nombre="Propuesta Inicial" etiqueta="PropuestaInicial" formatoHijos="1">
		<Hijo nombre="Seccion" cantidad="1"/>
	</Nodo>
	<Nodo nombre="Descripci&#xF3;n Proyecto" etiqueta="DescripcionProyecto" formatoHijos="1">
		<Hijo nombre="Seccion" cantidad="1"/>
	</Nodo>
	<Nodo nombre="Seccion" etiqueta="Seccion" formatoHijos="*">
		<Hijo nombre="Seccion" cantidad="*"/>
		<Propiedad nombre="nivel" valor=""/>
		<Propiedad nombre="titulo" valor=""/>
		<Propiedad nombre="texto" valor=""/>
	</Nodo>
</Especificacion>

<?xml version="1.0" encoding="UTF-8"?> 
<xsd:schema xmlns:xsd="http://www.w3c.org/2001/XMLSchema">
	<xsd:element name="Artefacto">
		<xsd:complexType>
			<xsd:sequence>
				<xsd:element name="Propuesta Inicial" type="xsd:string" maxOccurs="1"/>
				<xsd:element name="Descripción Proyecto" type="xsd:string" maxOccurs="1"/>
				<xsd:element name="Editorial" type="xsd:string"/>
			</xsd:sequence>
			<xsd:attribute name="Fecha" type="xsd:dateTime"/>
		</xsd:complexType>
	</xsd:element>
	<xsd:element name="Propuesta Inicial">
		<xsd:complexType>
			<xsd:sequence>
				<xsd:element name="Propuesta Inicial" type="xsd:string" maxOccurs="1"/>
				<xsd:element name="Descripción Proyecto" type="xsd:string" maxOccurs="1"/>
				<xsd:element name="Editorial" type="xsd:string"/>
			</xsd:sequence>
			<xsd:attribute name="Fecha" type="xsd:dateTime"/>
		</xsd:complexType>
	</xsd:element>
</xsd:schema>




*/


// Initializes plugin
$.fn.generarEntornoXML.initialize = function()
{
     alert("Fue inicializado");

}

// Runs the plugin
$.fn.generarEntornoXML.run = function()
{
     alert("Fue corrido");

};

