<?php
	class ControlClases{
		//Retorna la lista de funciones definidas por ideProyecto que contienen
		//los xmlClase
		//Retorna un array de SimpleXMLElements
		//$regex sirve para filtrar nombres de entidades, solo se tiene en cuenta cuando es diferente del valor por defecto
		public static function getListaClases($regex="//"){
			$funciones=get_defined_functions();
			$baseFunciones="getxml";
			$respuesta=array();
			foreach($funciones["user"] as $f){
				if (strncmp($f, $baseFunciones, strlen($baseFunciones))==0){
					$xmlClase = $f();
					if(strcmp($regex,"//")!=0){
					// En este caso el regex NO tiene el valor por defecto
						if(preg_match($regex,(string)$xmlClase->Propiedades["nombre"])){
							// Solo se agrega a la respuesta si el nombre de la entidad concuerda con el regex
							$respuesta[(string)$xmlClase->Propiedades["nombre"]] = $xmlClase;
						}
					}else{
						// En este caso el regex TIENE el valor por defecto
						$respuesta[(string)$xmlClase->Propiedades["nombre"]] = $xmlClase;
					}
				}
			}
			return $respuesta;
		}
		// Dada un arreglo de VOs genera un arreglo de arreglos con la misma información identificando los campos
		// primarios y foraneos en una estrucutura de json y los demás solo incluyen el valor del campo
		// Ej:
		// 
		// [0]=> array(
		// 	"campo1"=>{"nombre"=>campo1, "valor"=>valorCampo, "tipo"=>"primario"},
		// 	"campo2"=>{"nombre"=>campo2, "valor"=>valorCampo, "tipo"=>"foraneo"},
		// 	"campo3"=>valorCampo // este campo no es foraneo ni primario y por tanto no es un json
		// )
		// [1]=> array(
		// 	"campo1"=>{"nombre"=>campo1, "valor"=>valorCampo, "tipo"=>tipoCampo},
		// 	"campo2"=>{"nombre"=>campo2, "valor"=>valorCampo, "tipo"=>tipoCampo},
		// 	"campo3"=>{"nombre"=>campo3, "valor"=>valorCampo, "tipo"=>tipoCampo}
		// )

		public static function listaVos2ListaCadenas(&$nombreEntidad,&$registros){
			$campos = array();

			$sesion = Sesion::getInstancia();

			$llavesForaneas = ControlClases::obtenerLlavesForaneas($nombreEntidad);

/*
Zonas=>
	1=>
		idZona=>nombreZona
	2=>
		idZona=>nombreZona

Negocio=>
	1=>
		idNegocio=>nombreNegocio
*/
			$tablasForaneas = array();
			foreach($llavesForaneas as $llave){
				foreach($registros as $i=>$vo){

					$claseEntidadReflection = new ReflectionClass(get_class($vo));
					$metodo = $claseEntidadReflection->getMethod("get".strtoupper(substr((string)$llave["campoClaveForanea"],0,1)).substr($llave["campoClaveForanea"],1));

					$daoClase = new ReflectionClass("DAO".(string)$llave["tablaClaveForanea"]);
					if ($daoClase->isInstantiable()){
						$instanciaDao = $daoClase->newInstance($sesion->getDB());
						$metodoGetRegistro = $daoClase->getMethod("getRegistro");
						$voRef = $metodoGetRegistro->invoke($instanciaDao, (string)$metodo->invoke($vo));

						$referenciaReflection = new ReflectionClass(get_class($voRef));
						$metodoGet = $referenciaReflection->getMethod( "get".strtoupper(substr((string)$llave["campoTextoClaveForanea"],0,1)).substr($llave["campoTextoClaveForanea"],1) );
						// Se guarda la pareja valorCampoId=>valorCampoTexto
						$tablasForaneas[(string)$llave["tablaClaveForanea"]][(string)$metodo->invoke($vo)] = (string)$metodoGet->invoke($voRef);
					}else{
						// A llorar porque no es instanciable
					}
				}
			}

			$voClase = new ReflectionClass("VO".$nombreEntidad);
			// Obtengo los métodos públicos del VO para ejecutarlos y obtener el contenido de todos los campos de cada registro
			$metodos = $voClase->getMethods(ReflectionMethod::IS_PUBLIC);
			if(count($registros)>0){
			// SI hay registros a los cuales sacarles sus campos

				$llavesPrimarias = ControlClases::obtenerLlavesPrimarias($nombreEntidad);

				// Recorrer todos los registros de esa clase
				foreach($registros as $registro){
					// Se crea el array donde se pondrán los campos del registro
					$camposRegistro = array();
					// Ejecutar todos los métodos get de los campos de la clase que significativos en el modelo, y extraer los valores
					foreach($metodos as $metodo){
						// ToDo: tener en cuenta la lista principal como criterio para ver cuales campos se muestran.
						if(strcasecmp(substr($metodo->getName(),0,3),"get")==0){

							// Se obtiene el nombre del campo y se pasa a formato camel case
							$nombreCampo = substr($metodo->getName(),3);
							$nombreCampo = strtolower($nombreCampo[0]).substr($nombreCampo,1);
							// Se obtiene el valor del campo
							$valorCampo = (string)$metodo->invoke($registro);

							if( ControlClases::esCampoPrimario($nombreCampo,$llavesPrimarias) ){
							// El campo es primario
	                            $camposRegistro[$nombreCampo] = json_encode( array("nombre"=>$nombreCampo,"valor"=>$valorCampo,"tipo"=>"primario", "referencia"=>$valorCampo) );
							}else if( ControlClases::esCampoForaneo($nombreCampo,$llavesForaneas) ){
								// Se obtiene el XML de descripción del campo para obtener de allí el nombre de la tabla
								$campoForaneo = ControlClases::obtenerLlavesForaneas($nombreEntidad, $condiciones=array("nombre"=>$nombreCampo) );
								// Se obtiene el valor del campo texto clave foranea del arreglo tablasForaneas que fue llenado arriba
								$referencia = $tablasForaneas[(string)$campoForaneo[0]['tablaClaveForanea']][$valorCampo];
								// Se construye la estructura json del campo foraneo
	                            $camposRegistro[$nombreCampo] = json_encode( array("nombre"=>$nombreCampo,"valor"=>$referencia,"tipo"=>"foraneo", "referencia"=>$valorCampo) );
							}else{
							// El campo NO es primario
								$camposRegistro[$nombreCampo] = $valorCampo;
							}

						}
					}
					// Se agregan los campos del registro al arreglo de campos
					$campos[] = $camposRegistro;
				}
			}

			return $campos;
		}
		// Retorna un arreglo donde cada registro es un arreglo con parejas (nombreCampo, valorCampo)
		// e.g.: array( 0=>array(nombreCampo1=>'nombre',valorCampo1='valor'), 1=>array(nombreCampo1=>'nombre',valorCampo1='valor') )
		// Recibe:
		// $nombreEntidad = nombre de una entidad
		// $db = conexion a la base de datos
		// $filtros = arreglo con parejas nombreCampo,valorCampo que permiten filtrar los registros que se retornan
		//			  Estos filtros NO deben ser mutuamente excluyentes, es decir que las reglas no se contradigan
		//			  En otras palabras los registros que se retornen deben cumplir TODOS los filtros
		// Driver acoplado a nuestro modelo de DAO's y VO's
		// Suposiciones:
		// * Existe un objeto DAOnombreEntidad y VOnombreEntidad
		// * DAOnombreEntidad tiene un método llamado consultarRegistros que retorna una lista de objetos tipo VOnombreEntidad
		// ToDo: analizar si se debe escribir toda esta clase para cada ORM o si sobre el ORM se coloca nuestra capa de DAOs y VOs
		// y se garaniza la escalabilidad de los métodos que aquí se utilizan
		public static function obtenerDatosRegistros(&$nombreEntidad, &$db, &$filtros=array()){
			$campos = array();
			// El bloque try-catch para ReflectionException se hace en el caso de uso
			$daoClase = new ReflectionClass("DAO".$nombreEntidad);
			$voClase = new ReflectionClass("VO".$nombreEntidad);
			$metodo = new ReflectionMethod("DAO".$nombreEntidad, 'consultarRegistros');
			$registros = array();
			// Obtener todos los registros de esa clase
			if ($daoClase->isInstantiable()){
				$instancia = $daoClase->newInstance($db);
				if ($voClase->isInstantiable()){
					$instanciaVoClase = $voClase->newInstance();
					try{
						$registros = $metodo->invoke($instancia, $instanciaVoClase);
					}catch(sinResultados $e){
		    			$registros = array();
					}
				}else{
					// A llorar porque no es instanciable
				}
			}else{
					// A llorar porque no es instanciable
			}
			// Obtengo los métodos públicos del VO para ejecutarlos y obtener el contenido de todos los campos de cada registro
			$metodos = $voClase->getMethods(ReflectionMethod::IS_PUBLIC);
			if(count($registros)>0){
			// SI hay registros a los cuales sacarles sus campos

				$llavesPrimarias = ControlClases::obtenerLlavesPrimarias($nombreEntidad);

				// Recorrer todos los registros de esa clase
				foreach($registros as $registro){
					// Se crea el array donde se pondrán los campos del registro
					$procesarRegistro = TRUE;
					$camposRegistro = array();
					// Ejecutar todos los métodos get de los campos de la clase que significativos en el modelo, y extraer los valores
					foreach($metodos as $metodo){
						// ToDo: tener en cuenta la lista principal como criterio para ver cuales campos se muestran.
						if(strcasecmp(substr($metodo->getName(),0,3),"get")==0){

							$nombreCampo = substr($metodo->getName(),3);
							$valorCampo = (string)$metodo->invoke($registro);

							// Si hay filtros
							// Y hay alguna regla para el campo que esta siendo revisado
							// Y esa regla no se cumple
							// No procesar ningun campo del registro
							if( count($filtros)>0 && !is_null($filtros[$nombreCampo]) && strcmp($filtros[$nombreCampo],$valorCampo)!=0 ){
								$procesarRegistro = FALSE;
							}
							if($procesarRegistro){
								if( ControlClases::esCampoPrimario(substr($metodo->getName(),3),$llavesPrimarias) ){
								// El campo es primario
									$nombreCampoPrimario = json_encode( array ('icono'=>"/ide/Externos/iconos/tango/22x22/emotes/face-monkey.png",'nombre'=>$nombreCampo) );
		                            $camposRegistro[$nombreCampoPrimario] = $valorCampo;
								}else{
								// El campo NO es primario
									$camposRegistro[$nombreCampo] = $valorCampo;
								}
							}
						}
					}
					// Se agregan los campos del registro al arreglo de campos
					if($procesarRegistro){
						$campos[] = $camposRegistro;
					}
				}
			}

			// El arreglo de campos que se retorna es vacio si:
			//  * La consulta no arrojo ningun registro
			//  * Ningun registro paso la validacion de registros contra los filtros
			// En este caso debo recorrer los métodos y por lo menos agregar las cabeceras
			if(!count($campos)>0){
				// Ejecutar todos los métodos get de los campos de la clase significativos en el modelo y extraer los valores
				foreach($metodos as $metodo){
					// ToDo: tener en cuenta la lista principal como criterio para ver cuales campos se muestran.
					if(strcasecmp(substr($metodo->getName(),0,3),"get")==0){
						$camposRegistro[substr($metodo->getName(),3)] = null;
					}
				}
				// Se agregan los campos del registro al arreglo de campos
				$campos[] = $camposRegistro;
			}

			return $campos;
		}
		// Alias de esCampoDe para buscar si nombreCampo está en llavesPrimarias
		private static function esCampoPrimario(&$nombreCampo, &$llavesPrimarias){
			return ControlClases::esCampoDe($nombreCampo, $llavesPrimarias);
		}
		// Alias de esCampoDe para buscar si nombreCampo está en llavesForaneas
		private static function esCampoForaneo(&$nombreCampo, &$llavesForaneas){
			return ControlClases::esCampoDe($nombreCampo, $llavesForaneas);
		}
		// Hace comparaciones de nombreCampo con el arreglo de campo y retorna un booleano dependiendo si está adentro o no
		private static function esCampoDe(&$nombreCampo, &$arregloCampos){
			foreach($arregloCampos as $campo){
			// Se itera sobre todos los campos de arregloCampos
				if( strcasecmp($nombreCampo,$campo["nombre"])==0 ){
				// El campo recibido está en el arreglo de campos
					return TRUE;
				}
			}
			return FALSE;
		}		
		// Retorna un arreglo de SimpleXML 'Referencia' donde cada elemento es una clase que referencia a la clase recibida
		// Recibe:
		// $clase es el nombre de una entidad del proyecto
		public static function obtenerReferencias(&$clase){
			// Se obtiene la especificación de todas las clases del proyecto
			$listaClases = ControlClases::getListaClases("/^".(string)$clase."$/");
			if(count($listaClases)>1){
				new mensajes("Existe mas de una entidad con el mismo nombre: ".$clase);
				return array();
			}else if(count($listaClases)==0){
				new mensajes("No existe ninguna clase con el nombre: ".$clase);
				return array();
			}else{
				// Se recupera la primera clase de la lista.
				$claseXML = array_shift($listaClases);
				// Se retorna la lista de hijos en referencias			
				return $claseXML->Referencias->children();
			}
		}
		// Devuelve un arreglo con elementos SimpleXML 'Propiedad' donde cada elemento es un campo llave primaria de la clase recibida
		// * $clase es el nombre de una entidad del proyecto
		// * un arreglo $condiciones que representa condiciones de búsqueda adicionales sobre campos llavePrimaria
		public static function obtenerLlavesPrimarias(&$nombreClase, &$condiciones=array()){
			$condiciones["llavePrimaria"] = "true";
			return ControlClases::obtenerPropiedades($nombreClase, $condiciones);
		}
		// Devuelve un arreglo con elementos SimpleXML 'Propiedad' donde cada elemento es un campo llave foránea de la clase recibida
		// Recibe:
		// * $clase es el nombre de una entidad del proyecto
		// * un arreglo $condiciones que representa condiciones de búsqueda adicionales sobre campos llaveForanea
		public static function obtenerLlavesForaneas(&$nombreClase, &$condiciones=array()){
			$condiciones["llaveForanea"] = "true";
			return ControlClases::obtenerPropiedades($nombreClase, $condiciones);
		}
		// Devuelve un arreglo con elementos SimpleXML 'Propiedad' que cumplen las condiciones especificadas por $condiciones
		// Recibe:
		// * $clase es el nombre de una entidad del proyecto
		// * un arreglo $condiciones que representa condiciones de búsqueda adicionales		
		private static function obtenerPropiedades(&$nombreClase, &$condiciones=array()){
			// Se obtiene la especificación de todas las clases del proyecto
			$listaClasesXML = ControlClases::getListaClases("/^".(string)$nombreClase."$/");
			if(count($listaClasesXML)>1){
				new mensajes("Existe mas de una entidad con el mismo nombre: ".$nombreClase);
				return array();
			}else if(count($listaClasesXML)==0){
				new mensajes("No existe ninguna clase con el nombre: ".$nombreClase);
				return array();
			}else{
				// Se recupera la primera clase de la lista.
				$claseXML = array_shift($listaClasesXML);
				$condicionesXpath = array();
				foreach($condiciones as $campoCondicion=>$valorCondicion){
					$condicionesXpath[] = "@".$campoCondicion."='".$valorCondicion."'";
				}
				return $claseXML->xpath("/Clase/Propiedades/Propiedad[".implode(" and ",$condicionesXpath)."]");
			}
		}

		/**
		*	@name getCClase
		*	@abstract	Función que retorna una CClase de un XMLClase especificado.
		*	@license Pendiente
		*	@author Felipe Cano <fcano@idesoluciones.com >
		*	@param SimpleXMLElement XML padre.
		*	@param string $tipoNodo Tipo de nodo hijo.
		*	@param array $atributos array("nombre" => "valor", ...).
		*	@version 1.0
		*/
		public static function getCClase($nombreClase){
			if(function_exists("getXml".$nombreClase)){
				$funcion="getXml".$nombreClase;
				$cclase=new CClase($funcion());
				return $cclase;
			}else{
				return null;
			}
		}

	}
?>
