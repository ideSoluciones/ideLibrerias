<?php

class ControlNavegador{

	private $id;
	private $entidades;
	private $lista;
	private $permitidas;

	function __construct($id=null, $permitidas=null){
		$sesion = Sesion::getInstancia();
		$navegador = $sesion->leerParametro($id."_id");
		if(empty($navegador)){
			$this->setId($id);
			$this->setEntidades(array());
		}else{
			$this->setId( unserialize(base64_decode($sesion->leerParametro($id."_id"))) );
			$this->setEntidades( unserialize(base64_decode($sesion->leerParametro($id."_entidades"))) );
			foreach($this->entidades as $entidad){
				if(isset( $entidad['referencias'] )){
					foreach($entidad['referencias'] as $i=>$ref){
						$this->entidades[0]['referencias'][$i] = unserializemmp($ref);
					}
				}
			}
		}
		$this->setLista( ControlOperacionesNavegador::filtrarClases($permitidas) );
		$this->setPermitidas( $permitidas );
	}

	function __destruct() {
		foreach($this->entidades as $entidad){
			if(isset( $entidad['referencias'] )){
				foreach($entidad['referencias'] as $i=>$ref){
					$this->entidades[0]['referencias'][$i] = serializemmp($ref);
				}
			}
		}
		// Persistir el objeto en sesión
		$sesion = Sesion::getInstancia();
		$sesion->escribirParametro($this->id."_id", base64_encode(serialize($this->id)) );
		$sesion->escribirParametro($this->id."_entidades", base64_encode(serialize($this->entidades)) );
	}

	function setId($id){
		$this->id = $id;
	}
	function getId(){
		return $this->id;
	}
	function setEntidades($entidades){
		$this->entidades = $entidades;
	}
	function getEntidades(){
		return $this->entidades;
	}
	function setLista($lista){
		$this->lista = $lista;
	}
	function getLista(){
		return $this->lista;
	}
	function setPermitidas($permitidas){
		$this->permitidas = $permitidas;
	}
	function getPermitidas(){
		return $this->permitidas;
	}

	function analizarSesion(){
		$sesion = Sesion::getInstancia();
		ControlOperacionesNavegador::persistirYLimpiarParametros($this->getId());
		$entidad1 = array("nombre"=>$sesion->leerParametro($this->getId()."_args1"), "valor"=>$sesion->leerParametro($this->getId()."_args2"), "esValida"=>false);
		$entidad2 = array("nombre"=>$sesion->leerParametro($this->getId()."_args3"), "valor"=>$sesion->leerParametro($this->getId()."_args4"), "esValida"=>false);
		$entidad3 = array("nombre"=>$sesion->leerParametro($this->getId()."_args5"), "esValida"=>false);

		$this->entidades = array($entidad1, $entidad2, $entidad3);
	}

	// Para que se retorne algún contenido visible se debe cumplir alguna de estas precondiciones:
	// * Se haya cargado el arreglo $entidades, es decir que se ha ejecutado la función de analizarSesion
	// * Se haya cargado el arreglo $lista que tiene una lista de espeficaciones de clase permitidas, es decir que se ha ejecutado la función de analizarSesion
	function generarContenido($contenido){

		$sesion = Sesion::getInstancia();

		$estiloContenedor = array();
		$estiloBotones = array();
		ControlVistaNavegador::seleccionarDiagramacion($sesion->leerParametro("diagramacion"), $estiloContenedor, $estiloBotones);

		ControlVistaNavegador::agregarBotonesDiagramacion($contenido, $estiloBotones);

		$pathCasoUso = $sesion->configuracion->pathCliente."/".$sesion->leerParametro("nombreCasoUso");
		ControlVistaNavegador::agregarBreadcrumb($contenido, $pathCasoUso, $this->entidades, $this->id);

    $wiki = $contenido->addChild("Wiki");
    $wiki[] = "Los campos marcados con <img src='".resolverPath()."/../Externos/iconos/tango/22x22/emotes/face-monkey.png' /> son campos primarios y los campos marcados con <img src='".resolverPath()."/../Externos/iconos/tango/22x22/emotes/face-devilish.png' /> son campos foraneos";
		$contenedor = $contenido->addChild("Contenedor");
		$contenedor->addAttribute("estilo","height: 100%;");
		$contenedorUno=$contenedor->addChild("Contenedor");
		$contenedorUno->addAttribute("estilo",$estiloContenedor[0]);
		$contenedorDos=$contenedor->addChild("Contenedor");
		$contenedorDos->addAttribute("estilo",$estiloContenedor[1]);
		$contenedorTres=$contenedor->addChild("Contenedor");
		$contenedorTres->addAttribute("estilo",$estiloContenedor[2]);

		$agregarEntidadesInicio = FALSE;
		// Se verifica si se ha cargado el arreglo entidades
		if(is_array($this->entidades)){
			// Se guardan apuntadores a cada entidad partiendo de la estructura principal
			$entidad1 = &$this->entidades[0];
			$entidad2 = &$this->entidades[1];
			$entidad3 = &$this->entidades[2];

			// Se analizan y renderizan las entidades
			if($entidad1["esValida"]){
				$args = array($entidad1["nombre"]);
				ControlVistaNavegador::renderizarEntidad($contenedorUno, $entidad1, $args, $this->getId());
				if(count($entidad1["filtros"])>0 || $entidad2["esValida"]){
					$contenedorDos["estilo"] = $estiloContenedor[1]." display: block; ";
					if(!is_null($entidad2["registros"][0]) && !is_array($entidad2["registros"][0])){
					// $entidad2["registros"] es una lista de referencias de entidad2 a entidad1
						$args = array($entidad1["nombre"], $entidad1["valor"]);
						ControlVistaNavegador::agregarListaReferencias($contenedorDos,$entidad2["registros"],$args,$this->getId());
					}else{
					// $entidad2["registros"] es una lista de registros de entidad2
						$args = array($entidad1["nombre"], $entidad1["valor"], $entidad2["nombre"]);
						ControlVistaNavegador::renderizarEntidad($contenedorDos, $entidad2, $args, $this->getId());
					}

					if(count($entidad2["filtros"])>0 || $entidad3["esValida"]){
						$contenedorTres["estilo"] = $estiloContenedor[2]." display: block;";
						if(!is_null($entidad3["registros"][0]) && !is_array($entidad3["registros"][0])){
						// $entidad3["registros"] es una lista de referencias de entidad3 a entidad2
							$args = array($entidad1["nombre"], $entidad1["valor"], $entidad2["nombre"], $entidad2["valor"]);
							ControlVistaNavegador::agregarListaReferencias($contenedorTres,$entidad3["registros"],$args,$this->getId());
						}else{
						// $entidad3["registros"] es una lista de registros de entidad3
							ControlVistaNavegador::renderizarEntidad($contenedorTres, $entidad3, array(), $this->getId());
						}
					}
				}
			}else{
				$agregarEntidadesInicio = TRUE;
			}
		}
		// Se verifca si es necesario agregar el listado de entidades iniciales
		if($agregarEntidadesInicio){
			ControlVistaNavegador::agregarEntidadesInicio($contenedorUno, $this->lista, $this->getId());
		}
	}

	// Precondiciones:
	// * Se ha cargado el arreglo $entidades, es decir que se ha ejecutado la función de analizarSesion
	// Poscondiciones:
	// * Cada entidad tiene filtros referencias registros y permisos, sino está vacío o se reventó
	function procesar(){

		if(is_array($this->entidades)){
				$entidad1 = &$this->entidades[0];
				$entidad2 = &$this->entidades[1];
				$entidad3 = &$this->entidades[2];
				ControlOperacionesNavegador::verificarEntidades($this->lista, $entidad1, $entidad2, $entidad3);
				ControlOperacionesNavegador::procesarEntidades($entidad1, $entidad2, $entidad3, $this->permitidas, $this->lista);
				ControlOperacionesNavegador::procesarFormulariosDIMEC($entidad1, $entidad2, $entidad3);
				ControlOperacionesNavegador::procesarEntidades($entidad1, $entidad2, $entidad3, $this->permitidas, $this->lista);
		}

	}

}

?>
