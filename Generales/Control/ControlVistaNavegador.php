<?php

class ControlVistaNavegador{
	function ControlVistaNavegador(){
	}

	// Recibe:
	// * Un apuntador a un xml del ideComponente tabla
	// * Un arreglo que representa una entidad y es de la forma: entidad["nombre", "esValida", "referencias", "registros"]
	// * Un booleano que dice si la entidad a la cual hacen referencia registrosEntidad tiene referencias, comunmente es count(entidad["referencias"])
	// * Un arreglo con parejas nombre=>valor que será utilizado para crear los parametros que serán enviados al hacer clic en el boton
	//   ver relaciones y que por tanto establecen los campos por los cuales registros de otras entidades pueden relacionarse con cada registro
	//
	// A los parametros enviados con el boton "ver relaciones" se le suman los campos primarios y foraneos que identifican al registro
	//
	// Cualquier función que quiera reemplazar a esta debe:
	// * Presentar los datos en entidad["registros"]
	// * Permitir enviar cookies de la misma manera que lo hace el botón "Ver relaciones"
	// * Manejar los permisos que vienen en entidad["permisos"] para restringir el envio de cookies que agendan las operaciones de edición y borrado para cada registro
	//	
	// Ej: array( array("cabecera1","cabecera2","cabecera3"), array("campo11","campo12"), array("campo"1","campo22") )
	// agrega el contenido del array a la tabla
	public static function agregarCampos(&$tabla, $entidad, $parametros=array(), $id){

		$tieneReferencias = count($entidad["referencias"]);
		$registrosEntidad = $entidad["registros"];
		$cabeza = $tabla->addChild("Cabecera");
		$cabecerasCreadas = FALSE; # Indica si ya se terminaron de agregar las cabeceras
		$hayRegistrosNulos = FALSE; # Indica si 
		$referenciasAgregadas = FALSE; # Indica si se he agregado el campo Referencias

		$contadorRegistros = 0;

		foreach($registrosEntidad as $campos){
			$fila = $tabla->addChild("Fila");
			$referencias = array();

			foreach($campos as $i => $campo){

			    $campoJson = json_decode($campo,true);

				if(!$cabecerasCreadas){
					$wikiCabeza = $cabeza->addChild("Wiki");
					if(!is_null($campoJson) && is_array($campoJson)){
					// Es un JSON y es ARRAY, entonces es campo PRIMARIO o FORANEO
						$contenidoWikiCabeza = "";

						if(strcmp($campoJson["tipo"],"primario")==0){
							$contenidoWikiCabeza = "<img src='".resolverPath()."/../Externos/iconos/tango/22x22/emotes/face-monkey.png' />".(string)$i;
						}else if(strcmp($campoJson["tipo"],"foraneo")==0){
							$contenidoWikiCabeza = "<img src='".resolverPath()."/../Externos/iconos/tango/22x22/emotes/face-devilish.png' />".(string)$i;
						}else{
							mensaje::add("El campo [".(string)$i."] no es foraneo, primario u ordinario.\n".print_r( $campoJson, true), ERROR);
						}

						$wikiCabeza[] = $contenidoWikiCabeza;
						$wikiCabeza->addAttribute("estilo","min-width: 100");
					}else{
					// NO es un JSON, NO es campo primario NI foraneo
    					$wikiCabeza[] = $i;
					}
				}

				// Si el valor del campo es nulo significa que no hay registros
				// en ese caso se settea una variable booleana que luego se consultará para saber
				// si ese campo se debe pintar y si se debe mostrar una advertencia sobre campos null
				if(!is_null($campo)){
					$wiki = $fila->addChild("Wiki");
					if(!is_null($campoJson) && is_array($campoJson)){
                    // Es un JSON, entonces es campo PRIMARIO o FORANEO
                        if( strcasecmp(substr($i,0,3),"xml")==0 ){
							$wiki[] = generalXML::geshiTexto( $campoJson["valor"] );
                        }else{
							$wiki[] = (string)$campoJson["valor"];
                        }
					    $referencias[(string)$i] = (string)$campoJson["referencia"];
					}else{
                    // NO es un JSON, se procesa como un campo ordinario
                        if( strcasecmp(substr($i,0,3),"xml")==0 ){
							$wiki[] = generalXML::geshiTexto( $campo );
                        }else{
							$wiki[] = $campo;
                        }
					}
				}else{
					$wiki = $fila->addChild("Wiki");
					$wiki[] = "- - -";
					$hayRegistrosNulos = TRUE;
				}
			}

			$cabecerasCreadas = TRUE;

			// Si:
			// No hay registros nulos
			// Y ya se crearon las cabeceras (no se van a agregar mas campos a la cabecera)
			// Y esta entidad tiene otras que la referencian
			// Y los parametros del boton de "ver relaciones" no es vacio
			// Y no se ha agregado la columna para linkear las referencias
			// Y hay otros campos columna
			// Entonces se agrega la columna para linkear las referencias
			if(!$hayRegistrosNulos && $cabecerasCreadas && $tieneReferencias && count($parametros) && !$referenciasAgregadas && count($cabeza)>0){
			    $referenciasAgregadas = TRUE;
		    }

			// Se agrega el link de referencias si:
			// Existe la columna referencias
			// Y si ya se han agregado campos en las demas columnas de la tabla
			if($referenciasAgregadas && count($fila)>0){

				// Inicialización de la variable de eventos
				$eventoClick = "";
				foreach($parametros as $i => $param){
					$eventoClick .= "enviarPeticionCookie('".$id."_args".($i+1)."','".$param."');";
				}

				// Se agrega el arreglo el json de llaves primarias, cifrado en base 64
				$param = base64_encode(json_encode($referencias));
				$eventoClick .= "enviarPeticionCookie('".$id."_args".(count($parametros)+1)."','".$param."');";

				// Agrego el botón para ver las referencias
				$boton = $fila->addChild("Boton");
				$boton->addAttribute("titulo","Ver relaciones");
				$boton->addAttribute("style","width: 110px");
				$boton->addAttribute("onclick", $eventoClick);
			}

			if($entidad["permisos"][1]==="1"){
				// Se agrega el botón de editar
				$boton = $fila->addChild("Boton");
				$boton->addAttribute("titulo","Editar");
				$boton->addAttribute("onclick", "enviarPeticionCookie('editar".$entidad["nombre"]."','".base64_encode(json_encode($referencias))."');");
			}

			if($entidad["permisos"][2]==="1"){
				// Se agrega el botón de eliminar
				$boton = $fila->addChild("Boton");
				$boton->addAttribute("titulo","Eliminar");
				$boton->addAttribute("onclick", "enviarPeticionCookie('eliminar".$entidad["nombre"]."','".base64_encode(json_encode($referencias))."');");
			}
		}

	}
	// Agrega a $contenido una lista con anclas dadas definidas por $args
	// Retorna un $t que se renderiza en algo como:
	// <ol>
	// <li><a href="#" onclick="enviarPeticionCookie(args1);...enviarPeticionCookie(argsN);">Referencia 1</li>
	// <li><a href="#" onclick="enviarPeticionCookie(args1);...enviarPeticionCookie(argsN);">Referencia 2</li>
	// <li><a href="#" onclick="enviarPeticionCookie(args1);...enviarPeticionCookie(argsN);">Referencia 3</li>
	// <li><a href="#" onclick="enviarPeticionCookie(args1);...enviarPeticionCookie(argsN);">Referencia 4</li>
	// </ol>
	public static function agregarListaReferencias(&$contenedor,$registros,$args,$id){

		// Se agregan un contenedor de tabs
		$tabs = $contenedor->addChild("Tabs");
		// Se agrega un tab
		$tab = $tabs->addChild("Nodo");
		$tab->addAttribute("titulo","Listado de referencias");
		$tabs->addAttribute("estilo","height: 100%;");

		$html = $tab->addChild("Html");

		$html[] = "";
		foreach($registros as $registro){
			$enlace = "";
			foreach($args as $i=>$arg){
				$enlace .= 'enviarPeticionCookie("'.$id.'_args'.($i+1).'","'.$arg.'");';
			}
			$enlace .= 'enviarPeticionCookie("'.$id.'_args'.($i+2).'","'.$registro.'");';
			$html[] .= "<div><a href='#' onclick='".$enlace."'>".(string)$registro."</a></div>";
		}

	}
	// categoria: apuntador a un SimpleXMLElement de una categoría de un Breadcrumb
	// Agrega una lista de items con un formato particular del caso de uso navegadorMejorado a una categoria breadcrumb
	public static function agregarItemBreadcrumb($items, &$categoria, $id){
		// Agrega a una categoría determinada del breadcrumb la lista de items
		if(!empty($items)){
			$categoria->addAttribute("estiloListaDesplegable","display: none;");
			foreach($items as $item){
				$nodoItem = $categoria->addChild("Item");
				$nodoItem->addAttribute("titulo", (string)$item["tabla"]);
				$enlace = "javascript:void( enviarPeticionCookie(\"".$id."_args1\",\"".(string)$item["tabla"]."\") )";
				$nodoItem->addAttribute("enlace", $enlace);
			}
		}
	}
	public static function agregarBreadcrumb(&$contenido, $pathCasoUso, $entidades, $id){

		$breadcrumb = $contenido->addChild("Breadcrumb");
		$breadcrumb->addAttribute("id","breadcrumbs-1");
		$breadcrumb->addAttribute("estilo","margin-bottom: 10px;");

		$categoria = $breadcrumb->addChild("Categoria");
		$enlace = $pathCasoUso."/".$id."_inicio";
		$categoria->addAttribute("enlace",$enlace);
		$categoria->addAttribute("claseEnlace","home");
		$categoria->addAttribute("titulo","Inicio");
		$categoria->addAttribute("estiloListaDesplegable","display: none;");

		foreach($entidades as $i=>$entidad){
			if($entidad["esValida"]){
				// Se agrega un nuevo nivel al breadcrumb
				$enlace = "javascript:void( enviarPeticionCookie(\"".$id."_args1\",\"".$entidad["nombre"]."\") )";
				$categoria = ControlVistaBreadcrumb::agregarNivelBreadcrumb($breadcrumb,$entidad["nombre"],$enlace);
				// Agrega al breadcrumb la lista desplegable de entidades que referencian a entidad
				ControlVistaNavegador::agregarItemBreadcrumb($entidad["referencias"],$categoria,$id);
			}else{
				break;
			}
		}

	}
	// Lee un parámetro de la sesión y basado en eso modifica los valores de los parámetros estiloContenedor y estiloBotones
	public static function seleccionarDiagramacion(&$diagramacion, &$estiloContenedor,&$estiloBotones){
		if($diagramacion==1){
			$estiloContenedor = array(
				"background: none repeat scroll 0% 0% DarkGoldenRod; float: left; height: 100%; width: 30%; max-width: 100%; overflow: hidden; min-height: 100px;",
				"background: none repeat scroll 0% 0% DarkCyan; float: left; height: 70%; width: 70%; max-width: 100%; overflow: hidden; min-height: 100px; display: none;",
				"background: none repeat scroll 0% 0% BurlyWood; float: left; height: 30%; width: 70%; max-width: 100%; overflow: hidden; min-height: 100px; display: none;"
			);
			$estiloBotones = array(
				"background-position: -36px;",
				"background-position: -18px;"
			);
		}else{
			$estiloContenedor = array(
				"background: none repeat scroll 0% 0% DarkGoldenRod; overflow: hidden; height: 60%; max-width: 100%; min-height: 100px;",
				"background: none repeat scroll 0% 0% DarkCyan; float: left; overflow: hidden; width: 50%; height: 50%; max-width: 100%; min-height: 100px; display: none;",
				"background: none repeat scroll 0% 0% BurlyWood; float: left; overflow: hidden; width: 50%; height: 50%; max-width: 100%; min-height: 100px; display: none;"
			);
			$estiloBotones = array(
				"background-position: -54px;",
				"background-position: 0px;"
			);
		}
	}
    // Se agrega el formulario de inserción dentro de un contenedor dentro de un tab
	public static function agregarFormularioInsercion(&$ControlDimec, &$tabs){
		$tab = $tabs->addChild("Nodo");
		$tab->addAttribute("titulo","Inserción");
		$nodo = $tab->addChild("Contenedor");
		$nodo->addAttribute("estilo","height:80%; overflow:auto;");
		$ControlDimec->generarContenido($nodo,"nuevo","Nuevo registro");
	}
	public static function agregarFormularioEditar(&$ControlDimec, &$tabs, $editar){
		// Se agrega el formulario de edición dentro de un contenedor dentro de un tab
		if($editar){
			$tab = $tabs->addChild("Nodo");
			$tab->addAttribute("titulo","Editar");
			$nodo = $tab->addChild("Contenedor");
			$nodo->addAttribute("estilo","height:80%; overflow:auto;");
			$primariasRegistroAEditar = ControlUtilidades::descifrarDecodificarJson($editar);
			$ControlDimec->generarContenido($nodo,"modificar","Editar registro",$primariasRegistroAEditar);
		}
	}
	public static function agregarFormularioEliminar(&$ControlDimec, &$tabs, $eliminar){
		// Se agrega el formulario de edición dentro de un contenedor dentro de un tab
		if($eliminar){
			$tab = $tabs->addChild("Nodo");
			$tab->addAttribute("titulo","Eliminar");
			$nodo = $tab->addChild("Contenedor");
			$nodo->addAttribute("estilo","height:80%; overflow:auto;");
			$primariasRegistro = ControlUtilidades::descifrarDecodificarJson($eliminar);
			$ControlDimec->generarContenido($nodo,"borrar","Eliminar registro",$primariasRegistro);
		}
	}
	public static function agregarEntidadesInicio(&$contenedor, &$listaClases, $id){

		// Se agregan un contenedor de tabs
		$tabs = $contenedor->addChild("Tabs");
		// Se agrega un tab
		$tab = $tabs->addChild("Nodo");
		$tab->addAttribute("titulo","Entidades inicio");
		$tabs->addAttribute("estilo","height: 100%;");

		if( !is_array($listaClases) || empty($listaClases) ){

			$wiki = $tab->addChild("Wiki");
			$wiki[] = "No hay entidades hacia las cuales navegar. La lista de entidades de este proyecto está vacía.";

		}else{

			$html = $tab->addChild("Html");
			$html[] = "<table style='width: 100%'><tr>";
			$cols=array();
			$cols[0] = "<td style='width: 30%;'>";
			$cols[1] = "<td style='width: 30%;'>";
			$cols[2] = "<td style='width: 30%;'>";

			$contador = 0;

			foreach($listaClases as $a=>$clase){ 
				$enlace = "javascript:void(enviarPeticionCookie(\"".$id."_args1\",\"".(string)$clase->Propiedades["nombre"]."\"))";
				$cols[$contador] .= "<div><a href='".$enlace."'>".(string)$clase->Propiedades["nombre"]."</a></div>";
				$contador=($contador+1)%3;
			}

			$cols[0] .= "</td>";
			$cols[1] .= "</td>";
			$cols[2] .= "</td>";

			$html[] .= $cols[0].$cols[1].$cols[2]."</tr></table>";

		}
	}
	public static function agregarBotonesDiagramacion(&$contenido, &$estiloBotones){
		$boton = $contenido->addChild("Boton");
		$boton->addAttribute("onclick","enviarPeticionCookie('diagramacion','1')");
		$boton->addAttribute("style",'background:url('.resolverPath('/../Librerias/img/diagramas.gif').'); width:18px; height:18px; border:0; '.$estiloBotones[0]);
		$boton->addAttribute("sombra","orange");

		$boton = $contenido->addChild("Boton");
		$boton->addAttribute("onclick","enviarPeticionCookie('diagramacion','2')");
		$boton->addAttribute("style",'background:url('.resolverPath('/../Librerias/img/diagramas.gif').'); width:18px; height:18px; border:0; '.$estiloBotones[1]);
		$boton->addAttribute("sombra","orange");
	}
	public static function renderizarEntidad(&$contenedor, &$entidad, $parametros=array(), $id){

		$sesion = Sesion::getInstancia();

		// Se agregan un contenedor de tabs
		$tabs = $contenedor->addChild("Tabs");
		// Se agrega un tab
		$tab = $tabs->addChild("Nodo");
		$tab->addAttribute("titulo","Consulta");
		// Se agrega un contenedor para los elementos dentro del tab
		$nodo = $tab->addChild("Contenedor");
		$nodo->addAttribute("estilo","height:80%; overflow:auto;");
		// Se agrega un texto descriptivo
		$wiki = $nodo->addChild("Wiki");
		$wiki[] = count($entidad["registros"])>0?"Esta es una lista de registros de la tabla ''".$entidad["nombre"]."''":"No hay registros en la tabla ''".$entidad["nombre"]."''";

		// ToDo: parametrizar este llamado y permitir utilizar otras funciones
		// Se agrega un ideComponente tabla
		$tabla = $nodo->addChild("Tabla");
		$tabla->addAttribute("plano", "true");
		// Se agrega la tabla con los registros
		ControlVistaNavegador::agregarCampos($tabla, $entidad, $parametros, $id);

		// Se agrega la paginación
		$ControlPaginacion = new ControlPaginacion($sesion,$entidad["nombre"]."Id","",'{"tipoSelector":"selector","base":"3","registrosPorPagina":"10"}');
		$ControlPaginacion->generarNavegador($nodo);
		// Se instancia el control DIMEC para agregar los formularios de inserción y edición
		$ControlDimec = new ControlDimec($sesion,$entidad["nombre"],$entidad["nombre"]."Id");
		if(isset($entidad["permisos"])){
			if($entidad["permisos"][0]==="1")
				ControlVistaNavegador::agregarFormularioInsercion($ControlDimec, $tabs);
			if($entidad["permisos"][1]==="1")
				ControlVistaNavegador::agregarFormularioEditar($ControlDimec, $tabs, (string)$sesion->leerParametroDestinoActual('editar'.$entidad["nombre"]));
			if($entidad["permisos"][2]==="1")
				ControlVistaNavegador::agregarFormularioEliminar($ControlDimec, $tabs, (string)$sesion->leerParametroDestinoActual('eliminar'.$entidad["nombre"]));
		}
	}

}
?>
