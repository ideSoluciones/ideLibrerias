<?php

class ControlOperacionesNavegador{

	function ControlOperacionesNavegador(){
	}
	/*
	Breadcrumb inicial:
	Inicio
	 * Clase_no_referenciada_1
	 * Clase_no_referenciada_2
	 ...
	 * Clase_no_referenciada_n

	Al dar clic en alguna de las clases del breadcrumb:
	 * Se buscan las entidades que referencian esa entidad
	 * Se carga esa entidad como siguiente nivel del breadcrumb
	 * Las entidades que referencian a la entidad se cargan en una lista desplegable que sale del breadcrumb
	 * Un listado de los objetos asociados a esa entidad se carga en el "panel_1" y se limpian los paneles 2 y 3

	Para navegar las entidades hijas de alguna entidad seleccionada desde el breadcrumb se utilizan los links en los paneles
	Para determinar lo que se carga en los paneles se utiliza el listado principal definido en el XML-CLASE si no hay listado principal definido entonces se muestran todos los campos

	Asistente para crear listado principal
	*/

	// Guarda el parámetros diagramación
	// Limpia y persiste los args1-5 en sesión
	// Utilizando estas dos reglas:
	// * Si me mandan args por cookie debo garantizar que solo esos args están
	// * Si no me mandan debo dejar quietos los que están en sesión. Ej: recarga de página
	// * Si me mandan como primer argumento algo particular, e.g. "inicio", debo borrar todos los argumentos
	public static function persistirYLimpiarParametros($id){
		$sesion = Sesion::getInstancia();
		// Se guarda la variable de diagramación en sesion
		$diagramacion = siNoVacio($sesion->leerParametroDestinoActual('diagramacion'), $sesion->leerParametro("diagramacion"));
		if($diagramacion){
			$sesion->escribirParametro("diagramacion",$diagramacion);
		}

		// Este bloque de código es para limpiar todos los parámetros utilizando un valor que viene por get
		if( strcasecmp($sesion->args[1],$id."_inicio")==0 ){
			foreach(array(1,2,3,4,5) as $i){
				$sesion->borrarParametro($id."_args".$i);
			}
		}

		$argsLimpiados = FALSE;
		// Recorre todos los args guardando y borrando los necesarios
		foreach(array(1,2,3,4,5) as $i){
			$arg = $id."_args".$i;
			$argSesion = (string)$sesion->leerParametro($arg);
			$argActual = (string)$sesion->leerParametroDestinoActual($arg);
			//echo $arg.")) argSesion: [".$argSesion."] argActual: [".$argActual."]<br/>";
			if( strcmp($argActual,"")!=0 ){
			// Me enviaron un arg por cookie
				// Persitir ese arg en sesión
				$sesion->escribirParametro($arg,$argActual);
				//echo "persistiendo ".$arg."=[".$argActual."] en sesión<br/>";
				if(!$argsLimpiados){
				// No he limpiado argumentos
					// Setteo el contador que indica desde cual arg voy a comenzar a borrar
					$j = $i+1;
					// Se borraran los argumentos necesarios
					while ($j <= 6) {
						//echo "borrando args".$j." de sesión<br/>";
						$sesion->borrarParametro($id."_args".$j);
						$j++;
					}
					$argsLimpiados = TRUE;
				}
			}
		}

		return $diagramacion;
	}
	// Carga una entidad con sus filtros, referencias, registros y permisos
	// 
	// Recibe:
	// * Una entidad
	// * Un arreglo de parejas nombreCampoLocal=>valorCampoForaneo que permiten restringir los registros de $entidad según los registros con los que está relacionado contra entidad
	// * Un arreglo cuyas llaves son los nombres de entidades permitidas
	// Notas:
	// * referenciasTotales: lista de todas las entidades hacia las cuales se puede navegar desde $entidad y que son permitidas y distintas
	// * Por defecto NO se permite analizar NI navegar a entidades que no estén especificadas en entidadesPermitidas
	private static function obtenerFiltrosReferenciasRegistrosPermisos(&$entidad,&$filtrosForaneos=array(),$entidadesPermitidas=array()){

		if( !is_array($entidadesPermitidas) ){
			$entidadesPermitidas = array();
		}

		if(array_key_exists($entidad["nombre"],$entidadesPermitidas)){
		// La entidad es permitida
			$sesion = Sesion::getInstancia();
	 		$filtros = ControlUtilidades::descifrarDecodificarJson($entidad["valor"]);
			$referencias = ControlClases::obtenerReferencias($entidad["nombre"]);
			$camposForaneos = ControlClases::obtenerLlavesForaneas($entidad["nombre"]);
			$camposPrimarios = ControlClases::obtenerLlavesPrimarias($entidad["nombre"]);
			$referenciasTotales = array();
			// Se agregan a $referenciasTotales las entidades PERMITIDAS que referencian a $entidad
			foreach($referencias as $referencia){
				if(array_key_exists((string)$referencia["tabla"],$entidadesPermitidas) || strcmp((string)$referencia["tabla"],$entidad)==0 ){
				// La entidad mencionada en $referencia["tabla"] está PERMITIDA o ES la misma entidad que estoy analizando
				// entonces es agregada al arreglo de referencias legales hacia las cuales se puede navegar
					$ref = new SimpleXMLElement("<Referencia/>");
					$ref["tabla"] = (string)$referencia["tabla"];
					$referenciasTotales[(string)$referencia["tabla"]] = $ref;
				}else{
					// La entidad mencionada en $referencia["tabla"] NO está PERMITIDA
				}
			}
			// Se agregan a $referenciasTotales las entidades DISTINTAS Y PERMITIDAS que $entidad está referenciando
			foreach($camposForaneos as $campo){
				if(!isset($referenciasTotales[(string)$campo["tablaClaveForanea"]])){
				// La entidad mencionada en $campo["tablaClaveForanea"] se agrega si no ha sido ya agregada,
				// esto se puede saber consultando las llaves del arreglo $referenciasTotales
					if(array_key_exists((string)$campo["tablaClaveForanea"],$entidadesPermitidas) || strcmp((string)$referencia["tabla"],$entidad)==0 ){
					// La entidad mencionada en $referencia["tabla"] está PERMITIDA o ES la misma entidad que estoy analizando
					// entonces es agregada al arreglo de referencias legales hacia las cuales se puede navegar
						$ref = new SimpleXMLElement("<Referencia/>");
						$ref["tabla"] = (string)$campo["tablaClaveForanea"];
						$referenciasTotales[(string)$campo["tablaClaveForanea"]] = $ref;
					}else{
						// La entidad mencionada en $campo["tablaClaveForanea"] NO está PERMITIDA
					}
				}
			}
			// Instancia de ControlPaginacion
			$ControlPaginacion = new ControlPaginacion($sesion,$entidad["nombre"]."Id","",'{"tipoSelector":"selector","base":"3","registrosPorPagina":"10"}');
			// Procesar peticiones de paginación
			$ControlPaginacion->procesarPeticionNavegador();
			// Instancia de el DAO de mi interes
			$daoClase = new ReflectionClass("DAO".$entidad["nombre"]);
			if ($daoClase->isInstantiable()){
				$instancia = $daoClase->newInstance($sesion->getDB());
			}else{
				// A llorar porque no es instanciable
			}
			// Estructura de condiciones
			$consultaFiltros = new SimpleXMLElement("<Consulta />");
			$condiciones = ControlXML::agregarNodo($consultaFiltros,"Condiciones");
			$y = ControlXML::agregarNodo($condiciones,"Y");
			foreach($filtros as $campo=>$valor){
		    	ControlXML::agregarNodo($y,"Igual",array("tabla"=>$entidad["nombre"],"campo"=>$campo,"valor"=>$valor));
			}
			// Buscar cada condicion de filtrosForaneos dentro de referencias
			foreach($filtrosForaneos as $nombreFiltro=>$valorFiltro){
			// Se recorren los filtros de campos foráneos que restringen la consulta
				foreach($camposForaneos as $foranea){
				// Se recorren los campos foráneos de la entidad que estoy analizando
					// Si el campo del filtro foráneo corresponde con algun campo
					// foráneo de la entidad que estoy analizando entonces lo agrego a los filtros de la consulta
					if(strcasecmp($foranea["campoClaveForanea"],$nombreFiltro)==0){
						ControlXML::agregarNodo($y,"Igual",array("tabla"=>$entidad["nombre"],"campo"=>(string)$foranea["nombre"],"valor"=>$valorFiltro));
					}
				}
				foreach($camposPrimarios as $primaria){
				// Se recorren los campos primarios de la entidad que estoy analizando
					// Si el campo del filtro foráneo corresponde con algun campo
					// primario de la entidad que estoy analizando entonces lo agrego a los filtros de la consulta
					if(strcasecmp($primaria["nombre"],$nombreFiltro)==0){
						ControlXML::agregarNodo($y,"Igual",array("tabla"=>$entidad["nombre"],"campo"=>(string)$primaria["nombre"],"valor"=>$valorFiltro));
					}
				}
			}
			// Se obtienen los registros a mostrar
			$vos = $ControlPaginacion->obtenerRegistros($instancia,$consultaFiltros);
			$registros = ControlClases::listaVos2ListaCadenas($entidad["nombre"],$vos);
			// Se guardan los filtros, referencias y registros, todo en entidad
			$entidad["filtros"] = $filtros;
			$entidad["referencias"] = $referenciasTotales;
			$entidad["registros"] = $registros;
			$entidad["permisos"] = $entidadesPermitidas[$entidad["nombre"]];
		}else{
			// Se guardan los filtros, referencias y registros, todo en entidad
			$entidad["filtros"] = array();
			$entidad["referencias"] = array();
			$entidad["registros"] = array();
			$entidad["permisos"] = ControlOperacionesNavegador::construirPermisos();
		}
	}
	public static function procesarEntidades(&$entidad1, &$entidad2, &$entidad3, &$permitidas){

		if( !is_array($permitidas) ){
			$permitidas = array();
		}

		$sesion = Sesion::getInstancia();
		try{
			if($entidad1["esValida"]){
			// Entidad1
				ControlOperacionesNavegador::obtenerFiltrosReferenciasRegistrosPermisos($entidad1, $filtros=array(), $permitidas);
				if(count($entidad1["filtros"])>0){
				// Entidad1/ValorEntidad1

					if($entidad2["esValida"]){
					// Entidad1/ValorEntidad1/Entidad2
					ControlOperacionesNavegador::traducirCamposFiltros($entidad1, $entidad2);
					ControlOperacionesNavegador::obtenerFiltrosReferenciasRegistrosPermisos($entidad2, $entidad1["filtros"], $permitidas);

						if(count($entidad2["filtros"])>0){
						// /Entidad1/ValorEntidad1/Entidad2/ValorEntidad2
							if($entidad3["esValida"]){
							// /Entidad1/ValorEntidad1/Entidad2/ValorEntidad2/Entidad3
								ControlOperacionesNavegador::traducirCamposFiltros($entidad2, $entidad3);
								ControlOperacionesNavegador::obtenerFiltrosReferenciasRegistrosPermisos($entidad3, $entidad2["filtros"], $permitidas);
							}else{
								$entidad3["registros"] = array();
								foreach($entidad2["referencias"] as $referencia){
									$entidad3["registros"][] = (string)$referencia["tabla"];
								}
							}
						}
					}else{
						$entidad2["registros"] = array();
						foreach($entidad1["referencias"] as $referencia){
							$entidad2["registros"][] = (string)$referencia["tabla"];
						}
					}
				}

			}else{
				$entidad1["registros"] = array();
			}
		}catch(ReflectionException $e){
			new mensajes("Ha ocurrido un error al intentar instanciar una clase.<br/>".(string)$e);
		}		
	}
	// Compara los nombres de las entidades que se están revisando contra la lista de clases para saber si existe
	// Agrega un elemento al arreglo de cada entidad identificado por la llave 'esValida' que indica si el nombre de entidad existe
	public static function verificarEntidades(&$lista, &$entidad1, &$entidad2, &$entidad3){
		// Se recorre el arreglo de XML-CLASE
		foreach($lista as $clase){
			// Se verifica contra cada clase de la lista si la entidad 1 está en la lista original
			if(!$entidad1["esValida"]){
				if(strcmp($entidad1["nombre"],(string)$clase->Propiedades["nombre"])==0){
					$entidad1["esValida"] = true;
				}else{
					$entidad1["esValida"] = false;
				}
			}
			// Se verifica contra cada clase de la lista si la entidad 2 está en la lista original
			if(!$entidad2["esValida"]){
				if(strcmp($entidad2["nombre"],(string)$clase->Propiedades["nombre"])==0){
					$entidad2["esValida"] = true;
				}else{
					$entidad2["esValida"] = false;
				}
			}
			// Se verifica contra cada clase de la lista si la entidad 3 está en la lista original
			if(!$entidad3["esValida"]){
				if(strcmp($entidad3["nombre"],(string)$clase->Propiedades["nombre"])==0){
					$entidad3["esValida"] = true;
				}else{
					$entidad3["esValida"] = false;
				}
			}
		}
	}
	// Llama a los procesarFormulario de ControlDIMEC
	public static function procesarFormulariosDIMEC(&$entidad1, &$entidad2, &$entidad3){
		$sesion = Sesion::getInstancia();
		if($entidad1["esValida"]){
		// Entidad1
		$ControlDimec = new ControlDimec($sesion, $entidad1["nombre"], $entidad1["nombre"]."Id");
		$ControlDimec->procesarFormulario();

			if(count($entidad1["filtros"])>0){
			// Entidad1/ValorEntidad1

				if($entidad2["esValida"]){
				// Entidad1/ValorEntidad1/Entidad2
				$ControlDimec = new ControlDimec($sesion, $entidad2["nombre"], $entidad2["nombre"]."Id");
				$ControlDimec->procesarFormulario();

					if(count($entidad2["filtros"])>0){
					// /Entidad1/ValorEntidad1/Entidad2/ValorEntidad2

						if($entidad3["esValida"]){
						// /Entidad1/ValorEntidad1/Entidad2/ValorEntidad2/Entidad3
							$ControlDimec = new ControlDimec($sesion, $entidad3["nombre"], $entidad3["nombre"]."Id");
							$ControlDimec->procesarFormulario();
						}
					}
				}
			}
		}
	}
	// Retorna un arreglo con los XML-CLASE indicados por $entidadesPermitidas
	public static function filtrarClases(&$entidadesPermitidas){
		$listaInicial = ControlClases::getListaClases();
		$lista = array();
		if( is_array($entidadesPermitidas) ){
			foreach($entidadesPermitidas as $nombre => $permisos ){
				if(isset($listaInicial[$nombre])){
					$lista[$nombre] = $listaInicial[$nombre];
				}else{
						mensajes::add("La entidad [".(string)$nombre."] es permitida pero no existe");
				}
			}
		}
		return $lista;
	}
	// Retorna un numero que identifica los permisos para realizar operaciones sobre una entidad
	// 'I' es equivalente a 'i' y a 'insertar'
	// 'M' es equivalente a 'm' y a 'modificar'
	// 'E' es equivalente a 'e' y a 'eliminar'
	//
	// Los valores que puede tomar son:
	// 000 
	// 001 
	// 010 
	// 011 
	// 100 
	// 101 
	// 110 
	// 111 
	//
	// cero (0) indica que la operación NO se puede realizar
	// uno (1) indica que la operación se puede realizar
	//
	// Por defecto todas las operaciones están restringidas
	public static function construirPermisos($permisos=NULL){
		$valorPermisos = "000";
		if(is_array($permisos)){
			if( isset($permisos["I"]) || isset($permisos["i"]) || isset($permisos["insertar"]) ){
				$valorPermisos[0] = "1";
			}
			if( isset($permisos["M"]) || isset($permisos["m"]) || isset($permisos["modificar"])){
				$valorPermisos[1] = "1";
			}
			if( isset($permisos["E"]) || isset($permisos["e"]) || isset($permisos["eliminar"])){
				$valorPermisos[2] = "1";
			}
		}
		return $valorPermisos;
	}
	// Cambia las llaves de los filtros según los campos foráneos del sentido de la relación
	//
	// Identifica el sentido de la relación y con base en eso se cambian las campos de los filtros para que
	// representen filtros en otra entidad (en este caso la primera entidad que produzca un arreglo $nuevasLlaves con más de un elemento
	//
	// Retorna FALSE si los campos foraneos de ninguna entidad concordaron con las llaves $filtrosEntidad1
	// Retorna TRUE
	public static function traducirCamposFiltros(&$entidad1, &$entidad2){
		$entidades = array($entidad1, $entidad2);
		$viejasLlaves = array_keys($entidades[0]["filtros"]);
		foreach($entidades as $entidad){
			$nuevasLlaves = array();
			foreach($viejasLlaves as $llave){
				$campoClaseXMLForaneo = ControlClases::obtenerLlavesForaneas($entidad["nombre"], $condiciones=array("nombre"=>$llave));
				if(count($campoClaseXMLForaneo)>0){
					$nuevasLlaves[] = (string)$campoClaseXMLForaneo[0]["campoClaveForanea"];
				}
			}

			if(count($nuevasLlaves)>0){
				$filtrosEntidad1 = array_combine($nuevasLlaves, $entidades[0]["filtros"]);
				return TRUE;
			}
		}
		mensaje::add("Las entidades ".$entidad1["nombre"]." y ".$entidad2["nombre"]." no están relacionadas ",ERROR);
		return FALSE;
	}
}

?>
