<?php
class ControlFormulario{

	/**
	 * Agrega al final de $nodo una lista desplegable con los datos que contiene $datos 
	 *
	 * $nodo: XML donde se agrega la lista desplegable (ComboBox)
	 * $nombre: Variable con la que se recupera la información en leerParametroDestinoActual
	 * $datos: Arreglo con los datos para generar la lista desplegable
	 *			$datos=array(
					array("nombre"=> "Uno", "valor"=>1"),
					array("nombre"=> "Dos", "valor"=>"2"),
					array("nombre"=> "Tres", "valor"=>"3"),
					array("nombre"=> "Cuatro", "valor"=>"4"),
					array("nombre"=> "Cinco", "valor"=>"5"),
				);
			nombre, en este caso los numeros, es el texto que representa la opción
			valor, es el identificador de la opción y es lo que llega a leerParametroDestinoActual
	 * $titulo: titulo de la lista desplegable en el formulario. Por defecto es vacío.
	 * $valorPorDefecto: valor de la opción que debe quedar seleccionada al momento de pintar la lista desplegable. Por defecto es vacío.
	 * Att: jag2kn
	 */
	public static function generarListaSeleccion($nodo, $nombre, $datos, $titulo="", $valorPorDefecto="", $parametros=array()){
		return ControlFormulario::agregarListaSeleccion($nodo, $nombre, $datos, $titulo, $valorPorDefecto, $parametros);
	}
	public static function agregarListaSeleccion($nodo, $nombre, $datos, $titulo="", $valorPorDefecto="", $parametros=array()){
		$campo=$nodo->addChild("Campo");
		$campo->addAttribute("nombre", $nombre);
		$campo->addAttribute("titulo", $titulo);
		$campo->addAttribute("valorPorDefecto", $valorPorDefecto);
		$campo->addAttribute("tipo", 'listaSeleccion');
    	foreach($parametros as $nombre=>$parametro){
    		$campo->addAttribute($nombre, $parametro);
		}
		
		foreach($datos as $dato){
			$opcion=$campo->addChild("Opcion");
			$opcion->addAttribute("nombre", siEsta($dato["nombre"]));
			$opcion->addAttribute("valor", siEsta($dato["valor"]));
			if (strcmp($valorPorDefecto, siEsta($dato["valor"]))==0){
				$opcion->addAttribute("selected", "");
			}
		}
		
	}

	// Agrega al final de $nodo un formulario
	// * Recibe el nodo donde va a ser creado el caso de uso
	//    Recibe un arreglo de parametros con el idCaso de uso
	// * O
	//    Directamente el caso de uso, al ser el unico parametro a recibir
	public static function generarFormulario($nodo, $parametros=array()){
		return ControlFormulario::agregarFormulario($nodo, $parametros);
	}
	public static function agregarFormulario($nodo, $parametros=array()){
		if (is_null($nodo)){
			$formulario=ControlXML::nuevo("Formulario");
		}else{
			$formulario = $nodo->addChild("Formulario");
		}
        if (is_array($parametros)){
			if(!isset($parametros["idCasoUso"])){
				$sesion=Sesion::getInstancia();
				$parametros["idCasoUso"]=$sesion->leerParametro("idCasoUso");
			}
        	foreach($parametros as $nombre=>$parametro){
	        	$propiedad = $formulario->addChild("Propiedad");
        		$propiedad->addAttribute("nombre", $nombre);
        		$propiedad->addAttribute("valor", $parametro);
			}
		}else{
			$propiedad = $formulario->addChild("Propiedad");
			$propiedad->addAttribute("nombre", "idCasoUso");
        	$propiedad->addAttribute("valor", $parametros);
		}
		return $formulario;
	}
	// Agrega al final de $nodo un botón enviar 
	public static function generarEnviar($nodo,$parametros=array()){
		return ControlFormulario::agregarEnviar($nodo,$parametros);
	}
	public static function agregarEnviar($nodo,$parametros=array()){
		$campo=$nodo->addChild("Campo");
		$campo->addAttribute("tipo", 'enviar');
		$campo->addAttribute("nombre", isset($parametros["nombre"])?siEsta($parametros["nombre"],"Enviar"):"Enviar");
		$campo->addAttribute("titulo", isset($parametros["titulo"])?siEsta($parametros["titulo"],"Enviar"):"Enviar");
		if (isset($parametros["id"])){
			$campo->addAttribute("id", $parametros["id"]);
		}
		if (isset($parametros["estilo"])){
			$campo->addAttribute("estilo", $parametros["estilo"]);
		}
		return $campo;
	}



	public static function generarCampo($nodo, $parametros=array()){
		return ControlFormulario::agregarCampo($nodo, $parametros);
	}
	// Agrega al final de $nodo un Campo
	public static function agregarCampo($nodo, $parametros=array()){
		//@ToDo: unificar esto con ControlXML
		$campo = $nodo->addChild("Campo");
		foreach($parametros as $nombre=>$parametro){
			//msg::add("Agregando ".$nombre);
			if (isset($campo[$nombre])){
				$campo[$nombre]=$parametro;
			}else{
				$campo->addAttribute($nombre, $parametro);
			}
		}
		return $campo;
	}
}

class cf extends ControlFormulario{
}

?>
