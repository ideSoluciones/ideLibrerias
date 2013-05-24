<?php
class xml{
	/**
	*	@name add
	*	@abstract	Función que crea un nuevo nodo en $xml de tipo $tipoNodo y agrega las
	*				propiedades $atributos. Si $xml es nulo, crea un objeto SimpleXMLElement
	*				y le agrega las propiedades. Si el parámetro $attrOTexto es de tipo String
					lo agrega en el cuerpo del nodo, si es de tipo Array lo agrega como atributos.
	*	@license Pendiente
	*	@author Felipe Cano <fcano@idesoluciones.com >
	*	@param SimpleXMLElement XML padre.
	*	@param string $tipoNodo Tipo de nodo hijo.
	*	@param mixed $attrOTexto Texto o atributos.
	*	@param array $atributos array("nombre" => "valor", ...).
	*	@version 1.0
	*/
	public static function add($xml=null,$tipoNodo,$attrOTexto=array(),$atributos=array()){
		$texto="";
		if(!is_array($atributos)){
			$atributos=array();
		}
		if(is_string($attrOTexto)){
			$texto=$attrOTexto;
		}
		if(is_array($attrOTexto)){
			$atributos=array_merge($atributos,$attrOTexto);
		}
		if(!is_object($xml)){
			$campo=new SimpleXMLElement("<".$tipoNodo."/>");
		}else{
			if(get_class($xml)=="SimpleXMLElement"){
				$campo=$xml->addChild($tipoNodo);
			}else{
				throw new Exception("El parametro suministrado no es un SimpleXMLElement.");
			}
		}
		foreach($atributos as $nombre=>$valor){
			$campo->addAttribute($nombre,$valor);
		}
		if(strlen($texto)>0){
			$campo[]=$texto;
		}
		return $campo;
	}
}

class ControlXML{

	/**
	*	@name agregarNodo
	*	@abstract	Función que crea un nuevo nodo en $xml de tipo $tipoNodo y agrega las
	*				propiedades $atributos.
	*	@license Pendiente
	*	@author Felipe Cano <fcano@idesoluciones.com >
	*	@param SimpleXMLElement XML padre.
	*	@param string $tipoNodo Tipo de nodo hijo.
	*	@param array $atributos array("nombre" => "valor", ...).
	*	@version 1.0
	*/
	public static function agregarNodo($xml,$tipoNodo,$atributos=array()){
		$campo=$xml->addChild($tipoNodo);
		if(is_array($atributos)){
			foreach($atributos as $nombre=>$valor){
				$campo->addAttribute($nombre,$valor);
			}
		}
		return $campo;
	}

	/**
	*	@name nuevo
	*	@abstract	Funcion que crea un xml.
	*	@license Pendiente
	*	@version 1.0
	*/
	public static function nuevo($nombre){
		return new SimpleXMLElement("<".$nombre."/>");
	}
	
	/**
	*	@name agregarTexto
	*	@abstract Función que agrega nodos de texto
	*	@license Pendiente
	*	@author Felipe Cano <fcano@idesoluciones.com >
	*	@param int $idEvento Id de un evento.
	*	@return Array array("estado" => boolean, "valor" => mixed)
	*	@version 1.0
	*/
	public static function agregarNodoTexto($xml,$tipo,$texto,$atributos=array()){
		$campo=ControlXML::agregarNodo($xml,$tipo,$atributos);
		$campo[]=$texto;
		return $campo;
	}
}
?>
