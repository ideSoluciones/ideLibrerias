<?php

class XMLConjunto extends ComponentePadre{
	protected $xml;
	protected $contenido;
	protected $XMLFormulario=null;
	
	public function obtenerResultado($dato){
		$this->setXMLConjunto($dato);
		return $this->toHTML();
	}

	function XMLConjunto($xml=null){/*Colocados por los logs de php function XMLConjunto($xlm=null){*/
		$this->contenido=array();
		$this->XMLFormulario=new Formulario();
		if(!is_null($xml)){
			$this->setXMLConjunto($xml);
		}
	}

	function setXMLConjunto($xml){
		$this->campos=array();
		$this->xml=$xml;
		$this->XMLConjunto_analizarXML();
	}

	function XMLConjunto_Campo($xml){
		$this->contenido[]=htmlspecialchars($xml);
		foreach($xml as $titulo=>$contenido){
			if($titulo=="Formulario"){
				$this->contenido[]=$this->XMLFormulario->obtenerResultado($contenido,"conjuntoImec");
			}
		}
	}

	function XMLConjunto_Elemento($xml){
		foreach($xml as $nodo){
			switch($nodo->getName()){
				case "Titulo":
					$this->contenido[]="<td class='conjuntoTablaTdTitulo'><h3>";
					$this->XMLConjunto_Campo($nodo);
					$this->contenido[]="</h3></td>\n";
					break;
				case "CampoImec":
					$this->contenido[]="<td class='conjuntoTablaTd'>";
					$this->XMLConjunto_Campo($nodo);
					$this->contenido[]="</td>\n";
					break;
			}
		}
	}

	function XMLConjunto_analizarXML(){
		$this->contenido[]="<table class='conjuntoTabla'>\n";
		foreach($this->xml as $nodo){
			switch($nodo->getName()){
				case "Elemento":
					$this->contenido[]="<tr class='conjuntoTablaTr'>\n";
					$this->XMLConjunto_Elemento($nodo);
					$this->contenido[]="</tr>\n";
					break;
			}
		}
		$this->contenido[]="</table>\n";
	}

	function toHTML(){
		$total=@implode("",$this->contenido);
		return $total;
	}

}
?>
