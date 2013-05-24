<?php
	
	class ControlDimec{

		private $CClase;
		private $sesion;
		private $DAO;
		private $id;
		private $publicarMensajes;
		private $prefuncion=array();
		private $posfuncion=array();
		private $permisos;
		public $mensajes=array();
		public $titulos=array();
		public $botonesNuevo=true;
		public $peticionPorAjax=false;
		public $voUltimoProceso;
		public $objetoPadre="";
		public $funcionGetTexto="";
		public $funcionGetValor="";
		public $idMiContenedor="";
		public $posAccion=null;
		
		function ControlDimec($sesion,$Clase,$id="",$publicarMensajes=true){
			if(is_object($Clase)){
				$this->CClase=$Clase;
			}else{
				$this->CClase=ControlClases::getCClase($Clase);
			}
			$this->publicarMensajes=$publicarMensajes;
			$this->sesion=$sesion;
			$this->id=$id;
			$DAO="DAO".$this->CClase->getPropiedad("nombre");
			$this->DAO=new $DAO($this->sesion->getDb());
			$this->permisos=new CPermisos(array("lectura"=>true,"escritura"=>true));
		}

		function setPermiso($nombre,$valor){
			if(is_array($nombre)){
				foreach($nombre as $n=>$v){
					if(is_string($n)){
						if(is_bool($valor)){
							$tmp=$valor;
						}else{
							$tmp=is_bool($v)?$v:false;
						}
						$this->permisos->setPermiso($n,$tmp);
					}
				}
			}elseif(is_string($nombre)){
				if(is_bool($valor)){
					$this->permisos->setPermiso($nombre,$valor);
				}
			}
		}
		
		function setPrefuncion($clase,$metodo){
			if(class_exists((string)$clase)){
				if(method_exists((string)$clase,(string)$metodo)){
					$this->prefuncion=array(
						"clase"=>(string)$clase,
						"funcion"=>(string)$metodo
					);
					return true;
				}else{
					mensaje::add("El método $metodo de la clase $clase no existe.",ERROR);
				}
			}else{
				mensaje::add("La clase $clase no existe.",ERROR);
			}
			return false;
		}
		
		function setPosfuncion($clase,$metodo){
			if(class_exists((string)$clase)){
				if(method_exists((string)$clase,(string)$metodo)){
					$this->posfuncion=array(
						"clase"=>(string)$clase,
						"funcion"=>(string)$metodo
					);
					return true;
				}else{
					mensaje::add("El método $metodo de la clase $clase no existe.",ERROR);
				}
			}else{
				mensaje::add("La clase $clase no existe.",ERROR);
			}
			return false;
		}
		
		private function llamarFuncion($especificacion,$vo,$estado){
			try{
				if(isset($especificacion["clase"]) && isset($especificacion["funcion"])){
					$clase=(string)$especificacion["clase"];
					$metodo=(string)$especificacion["funcion"];
					if(method_exists($clase,$metodo)){
						$obj=@new $clase();
						$obj->$metodo($vo,$estado,$this->permisos);
					}
				}
			}catch(Exception $e){
				mensaje::add($e->getMessage(),ERROR);
			}
		}
		
		function retornarError($mensaje){
			$this->nuevoMensajeError($mensaje);
			return null;
		}
		
		function nuevoMensajeError($mensaje){
			if($this->publicarMensajes)
				mensaje::add($mensaje,ERROR);
			if(strlen($mensaje)>0)
				$this->mensajes["ERROR"][]=$mensaje;
		}
		
		function nuevoMensaje($mensaje){
			if($this->publicarMensajes)
				mensaje::add($mensaje);
			if(strlen($mensaje)>0)
				$this->mensajes["ALERTA"][]=$mensaje;
		}
		
		function generarContenido($xmlContenido,$tipo,$titulo="",$llaves=array(),$valoresPorDefecto=array(),$titulos=array()){
			// Se validan los parametros
			if(is_null($xmlContenido)){ return $this->retornarError("XML destino es nulo."); }
			if(!is_object($xmlContenido)){ return $this->retornarError("XML no es un objeto SimpleXMLElement."); }
			if(strcmp(get_class($xmlContenido),"SimpleXMLElement")!=0){ return $this->retornarError("XML no es un objeto SimpleXMLElement."); }

			$form=new CFormulario($this->CClase);
			$form->setTipo($tipo);

			$xml=xml::add($xmlContenido,"Contenedor",array("estilo"=>"padding:5px;"));

			// Se pinta el titulo 
			if(!is_null($titulo)){
				if(strcmp($titulo,"")==0) $titulo=$tipo;
				ControlXML::agregarNodoTexto($xml,"Wiki","=$titulo=\n");
			}
			// Se crea el formulario
			if($this->permisos->getPermiso("escritura")&&!$this->peticionPorAjax){
				$formulario=ControlFormulario::generarFormulario($xml,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso"),"id"=>$this->id."f".$tipo.$this->CClase->getPropiedad("nombre")));
			}else{
				$formulario=xml::add($xml,"Contenedor");
			}
			xml::add($formulario,"Contenedor",array("id"=>"mensajes".$this->id));
			// Se realiza la verificación de si el formulario contiene datos de error
			$hayDatosFormulario=false;
			$datosFormulario=null;
			$camposDuplicados=array();
			if(strcmp($this->sesion->leerParametroInterno("{$this->id}ControlDimec","datosFormulario"),"")!=0){
				$datosFormulario=unserialize((string)base64_decode($this->sesion->leerParametroInterno("{$this->id}ControlDimec","datosFormulario")));
				$this->sesion->borrarParametroInterno("{$this->id}ControlDimec","datosFormulario");
				if(strcmp($this->sesion->leerParametroInterno("{$this->id}ControlDimec","registroDuplicado"),"")!=0){
					$tmp=json_decode($this->sesion->leerParametroInterno("{$this->id}ControlDimec","registroDuplicado"),true);
					$camposDuplicados[(string)$tmp["campo"]]=$tmp["valor"];
				}
				$hayDatosFormulario=true;
			}
			
			// Se carga un VO con los datos de el registro asociado con las llaves
			$banderaLlenarCampos=false;
			$VO=null;
			if($form->es("modificar")||$form->es("borrar")||$form->es("consultar")||$form->es("desactivar")){
				foreach($llaves as $nombre=>$valor){
					$porpCampo=array("tipo"=>"oculto","idForm"=>$this->id,"nombre"=>"$nombre","valorPorDefecto"=>$valor);
					if($this->peticionPorAjax){
						$porpCampo["mostrarOculto"]=true;
					}
					ControlFormulario::generarCampo($formulario,$porpCampo);
				}
				$form->cargar($llaves);
				$banderaLlenarCampos=true;
			}

			if($hayDatosFormulario){
				$form->serEstrictoConValoresPorDefecto(false);
				$form->setValoresPorDefecto($datosFormulario);
			}
			
			// Se activa la bandera que especifica si el formulario lleva campos de modificación 
			$banderaConsulta=$form->es("borrar")||$form->es("consultar")||$form->es("desactivar");

			// Se generan los Campos del formulario
			$valorActivo=-1;
			//$campos=$this->CClase->getCampos();
			
			$campos=$form->getCampos();
			foreach($campos as $campo){
				// Se valida si no es solo llave primaria
				if(!$campo->esPrimaria() || $campo->esForanea()){
					// Se valida si el campo es Activo para omitirlo
					if($campo->get("activo")){
						$valorActivo=$campo->get("valor");
					/*}elseif($campo->get("clave") && !$form->es("nuevo")){
						if($clave=ControlAjax::solicitarClave(array("clase"=>"XControlDimec","funcion"=>"cambiarClave"))){
							$parametros=new parametros();
							$voTexto=serialize($VO);
	                    	$parametros->texto("clase",(string)$this->CClase->getPropiedad("nombre"),true);
	                    	$parametros->texto("vo",(string)$voTexto,true);
	                    	$parametros->texto("id",(string)$this->id);
	                    	$parametros->texto("campo",(string)$campo->get("nombre"),true);
	                    	$parametros->script("claveAnterior","$('#{$this->id}ClaveAnterior{$campo->get("nombre")}').val()");
	                    	$parametros->script("nuevaClave","$('#{$this->id}ClaveNueva{$campo->get("nombre")}').val()");
	                    	$parametros->script("nuevaClave2","$('#{$this->id}ClaveNueva2{$campo->get("nombre")}').val()");
							$funcion=ControlAjax::generarFuncionJS($clave,$parametros);
							$contenedor=ControlXML::agregarNodo($formulario,"Contenedor",array("modal"=>"true","textoModal"=>"Modificar contraseña","titulo"=>"Agregar nuevo","icono"=>resolverPath()."/../Librerias/img/candado.png","propiedadesIcono"=>"class='' style='padding-top:3px;margin-right:2px;' width='10'","ancho"=>"360","alto"=>"177","estiloBoton"=>"margin-top:7px;","id"=>"{$this->id}dialogo{$campo->get("nombre")}"));
							$html ="<div id='{$this->id}Mensaje{$campo->get("nombre")}'></div>";
							$html.="<div class='fuente1'>";
							$html.="<div>";
							$html.="<label class='f-etiqueta margen1'>Clave actual</label>";
							$html.="<input class='margen1' type='password' id='{$this->id}ClaveAnterior{$campo->get("nombre")}'/>";
							$html.="</div>";
							$html.="<div>";
							$html.="<label class='f-etiqueta margen1'>Nueva Clave</label>";
							$html.="<input class='margen1' type='password' id='{$this->id}ClaveNueva{$campo->get("nombre")}'/>";
							$html.="</div>";
							$html.="<div>";
							$html.="<label class='f-etiqueta margen1'>Vuelva a escribir la nueva clave</label>";
							$html.="<input class='margen1' type='password' id='{$this->id}ClaveNueva2{$campo->get("nombre")}'/>";
							$html.="</div>";
							$html.="<input class='margen1' type='button' value='Cambiar contraseña' onclick='$funcion'/>";
							$html.="</div>";
							ControlXML::agregarNodoTexto($contenedor,"Html",$html);
						}
						*/
					}else{
						// Se establece el tipo de campo
						$tipoCampo=$campo->get("tipo");
						// Se verifica si el campo es llave foránea y de ser así se 
						// obtienen el DAO y las funciones funcionGetTexto y funcionGetValor
						// y se establece en verdadero la bandera banderaLlenadoCampo
						$banderaLlenadoCampo=false;
						$funcionGetTexto="";
						$funcionGetValor="";
						$nombreDaoTablaForanea="";
						if($campo->esForanea()){
							$tipoCampo="ListaSeleccion";
							try{
								$nombreDaoTablaForanea="DAO".$campo->get("tablaClaveForanea");
								$DAOTmp=new $nombreDaoTablaForanea($this->sesion->getDb());
								$funcionGetTexto="get".ucfirst($campo->get("campoTextoClaveForanea"));
								$funcionGetValor="get".ucfirst($campo->get("campoClaveForanea"));
							}catch(Exeption $e){
								$this->retornarError($e->getMessage());
							}
							$banderaLlenadoCampo=true;
						}
						// Se establece el titulo del campo
						$titulo=$campo->get("titulo");
						if(isset($titulos[(string)$campo->get("nombre")])){
							if(strlen($titulos[(string)$campo->get("nombre")])>0){
								$titulo=$titulos[(string)$campo->get("nombre")];
							}
						}
						
						// Se revisa si el formulario NO ES de tipo consulta
						if(!$banderaConsulta){
							
							// Se establece si el campo tiene error asignado
							$error="false";
							if(isset($camposDuplicados["{$campo->get("nombre")}"])){
								$error="true";
							}
							
							// Se revisa si el campo es de tipo XML
							if(strcmp($campo->get("tipo"),"xml")==0){
								// Se revisa si el campo tiene asociado alguna configuración en la tabla 0XMLPropiedades
								// y de ser así genera el formulario asociado de lo contrario genera un campo tipo XML
								try{
									$controlXMLPropiedades=new ControlXMLPropiedades();
									$controlXMLPropiedades->generarFormulario($formulario, $this->CClase->getPropiedad("nombre"), $campo->get("nombre"),$campo->get("nombre"),$campo->get("valorMostrar"));
								}catch(Exception $e){
									ControlFormulario::generarCampo($formulario,array("tipo"=>"$tipoCampo","nombre"=>"{$campo->get("nombre")}","titulo"=>"{$titulo}","requerido"=>($campo->get("requerido")?"true":"false"),"title"=>"{$campo->get("descripcion")}","valorPorDefecto"=>$campo->get("valorMostrar"),"error"=>$error));
								}
							}else{// El campo no es XML
								// Genera el campo en el formulario
								$contenedorCampo=xml::add($formulario,"Contenedor",array("estilo"=>"overflow:hidden;"));
								$porpCampo=array(
									"idForm"=>$this->id,
									"tipo"=>"$tipoCampo",
									"nombre"=>"{$campo->get("nombre")}",
									"titulo"=>"{$titulo}",
									"requerido"=>($campo->get("requerido")?"true":"false"),
									"alt"=>"{$campo->get("descripcion")}",
									"valorPorDefecto"=>$campo->get("valorMostrar"),
									"error"=>$error,
									"id"=>$this->id."_".$campo->get("nombre")
								);
								if(strcmp($tipoCampo,"oculto")==0&&$this->peticionPorAjax){
									$porpCampo["mostrarOculto"]=true;
								}
								$campoTmp=ControlFormulario::generarCampo($contenedorCampo,$porpCampo);
								// Verifica si el campo es una lista de selección
								if($banderaLlenadoCampo){
									// Obtiene la información de la tabla foránea y llena el campo de opciones
									try{
										$registros=$DAOTmp->getRegistros();
										if(count($registros)<=0){
											$this->retornarError("No se encontraron {$this->CClase->get("nombre")}s");
										}
										foreach($registros as $registro){
											ControlXML::agregarNodo($campoTmp,"Opcion",array("nombre"=>$registro->$funcionGetTexto(),"valor"=>$registro->$funcionGetValor()));
										}
									}catch(sinResultados $e){
										$this->retornarError($e->getMessage());
									}catch(Exeption $e){
										$this->retornarError($e->getMessage());
									}
									// Se genera el contenedor con el formulario de la tabla foránea
									if($this->botonesNuevo){
										$campoTmp->addAttribute("estiloContenedor","float:left;");
										$contenedor=ControlXML::agregarNodo($contenedorCampo,"Contenedor",array("id"=>"contenedor".(string)$campo->get("tablaClaveForanea")."En".$this->id,"modal"=>"true","textoModal"=>"Nuevo","titulo"=>"Agregar nuevo","icono"=>resolverPath()."/../Librerias/img/agregar.png","propiedadesIcono"=>"class='' style='padding-top:3px;margin-right:2px;' width='10'","ancho"=>"570","alto"=>"400","estiloBoton"=>"margin-top:7px;"));
										$nuevo=new ControlDimec($this->sesion,(string)$campo->get("tablaClaveForanea"),(string)$campo->get("tablaClaveForanea")."En".$this->id);
										$nuevo->peticionPorAjax=true;
										$nuevo->objetoPadre=$this->id."_".$campo->get("nombre");
										$nuevo->idMiContenedor="contenedor".(string)$campo->get("tablaClaveForanea")."En".$this->id;
										$nuevo->nombreDaoTablaForanea=$nombreDaoTablaForanea;
										$nuevo->funcionGetTexto=$funcionGetTexto;
										$nuevo->funcionGetValor=$funcionGetValor;
										$nuevo->generarContenido($contenedor,"nuevo","",array(),array(),$this->titulos);
									}
								}
							}
						}else{// El formulario es de tipo consulta
							// Verifica si el campo es de tipo XML y de ser así pasa el contenido por la función generalXML::geshiTexto()
							if(strcmp($campo->get("tipo"),"xml")==0){
								$valorPorDefecto=generalXML::geshiTexto($campo->get("valorMostrar"));
							}else{
								$valorPorDefecto=$campo->get("valorMostrar");
							}

							// Agrega un Wiki con la información del campo
							ControlXML::agregarNodoTexto($formulario,"Wiki","'''".$titulo.":''' ".$valorPorDefecto."\n");
						}
					}
				}
			}

			
			// Se evalúa que tipo de botón enviar hay que construir
			if(!$form->es("consultar")){
				$tituloBotonEnviar="Enviar";
				$valorPorDefecto="";
				// Se obtiene el titulo del botón y el valor por defecto que
				// va a llevar el campo oculto "{$ID}ControlDimec_operacion"
				if($form->es("nuevo")){
					$valorPorDefecto="crear";
					$tituloBotonEnviar="Crear";
				}elseif($form->es("modificar")){
					$valorPorDefecto="modificar";
					$tituloBotonEnviar="Guardar";
				}elseif($form->es("borrar")){
					$valorPorDefecto="borrar";
					$tituloBotonEnviar="Borrar";
				}elseif($form->es("desactivar")){
					if($valorActivo>-1){
						$valorPorDefecto="desactivar";
						if($valorActivo==1){
							$tituloBotonEnviar="Desactivar";
							$valorActivo=0;
						}else{
							$tituloBotonEnviar="Activar";
							$valorActivo=1;
						}
					}
				}
				
				// Evalua el valor del campo activo y crea un campo oculto con este valor
				// si es requerido
				if($valorActivo>-1){
					if(isset($valoresPorDefecto["activo"])){
						$valorActivo=intval($valoresPorDefecto["activo"]);
					}else{
						if(strcmp($tipo,"nuevo")==0){
							$valorActivo=1;
						}
					}
					$porpCampo=array("tipo"=>"oculto","idForm"=>$this->id,"nombre"=>"activo","valorPorDefecto"=>$valorActivo);
					if($this->peticionPorAjax){
						$porpCampo["mostrarOculto"]=true;
					}
					ControlFormulario::generarCampo($formulario,$porpCampo);
				}
								
				// Cenera los campos operación y id además de los botones enviar y cancelar
				$porpCampo=array("tipo"=>"oculto","idForm"=>$this->id,"nombre"=>"{$this->id}ControlDimec_operacion","valorPorDefecto"=>$valorPorDefecto);
				if($this->peticionPorAjax){
					$porpCampo["mostrarOculto"]=true;
				}
				ControlFormulario::generarCampo($formulario,$porpCampo);
				
				$porpCampo=array("tipo"=>"oculto","idForm"=>$this->id,"nombre"=>"id","valorPorDefecto"=>base64_encode($this->id.";".$this->CClase->getPropiedad("nombre")));
				if($this->peticionPorAjax){
					$porpCampo["mostrarOculto"]=true;
				}
				ControlFormulario::generarCampo($formulario,$porpCampo);

				if($this->peticionPorAjax){
					$propCampo=array(
						"tipo"=>"oculto",
						"idForm"=>$this->id,
						"nombre"=>"posOperacion",
					);
					if(is_array($this->posAccion)){
						$propCampo["valorPorDefecto"]=base64_encode(json_encode($this->posAccion));
					}else{
						$propCampo["valorPorDefecto"]=base64_encode(
							json_encode(
								array(
									"clase"=>"XControlDimec",
									"funcion"=>"actualizarListaSeleccion",
									"propiedades"=>array(
										"objetoPadre"=>$this->objetoPadre,
										"funcionGetTexto"=>$this->funcionGetTexto,
										"funcionGetValor"=>$this->funcionGetValor,
										"nombreDaoTablaForanea"=>$this->nombreDaoTablaForanea,
										"idMiContenedor"=>$this->idMiContenedor
									)
								)
							)
						);
					}
					$propCampo["mostrarOculto"]=true;
					ControlFormulario::generarCampo($formulario,$propCampo);
				}

				if($this->peticionPorAjax){
					if($codigo=ControlAjax::solicitarClave(array("clase"=>"XControlDimec","funcion"=>"procesarFormulario"))){
	                	ControlXML::agregarNodo($formulario,"Boton",array("titulo"=>"$tituloBotonEnviar","onclick"=>"enviarCampos('".$this->id."','".$codigo."');","ui"=>"false"));
					}
				}else{
					ControlFormulario::generarCampo($formulario,array("tipo"=>"enviar","titulo"=>$tituloBotonEnviar,"nombre"=>"Enviar","estiloContenedor"=>"float:left;","estilo"=>"float:left;","class"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"));
					ControlXML::agregarNodo($formulario,"Boton",array("id"=>$this->id."ListaDIMEC_botonCancelarFormularioEditar","titulo"=>"Cancelar","onclick"=>"location.reload(true)","ui"=>"false","style"=>"float:right;margin:5px 0px 0px 0px;"));
				}
			}
		}
				
		function procesarFormulario(){
			try{
				$this->voUltimoProceso=null;
				$tipo=$this->sesion->leerParametroFormularioActual("{$this->id}ControlDimec_operacion");
				if(strlen($tipo)<=0){
					$tipo=$this->sesion->leerParametroDestinoActual("{$this->id}ControlDimec_operacion");
				}
				
				if(strcmp($tipo,"")!=0){

					if(strcmp($tipo,"crear")==0){
						$nombreClaseVO="VO".$this->CClase->getPropiedad("nombre");
						$VO=new $nombreClaseVO();
					}else{
						$llaves=$this->CClase->getLlaves();
						$parametros=array();
						foreach($llaves as $llave){
							if(strlen($this->sesion->leerParametroFormularioActual($llave->getPropiedad("nombre")))>0){
								$parametros["{$llave->getPropiedad("nombre")}"]=$this->sesion->leerParametroFormularioActual($llave->getPropiedad("nombre"));
							}elseif(strlen($this->sesion->leerParametroDestinoActual($llave->getPropiedad("nombre")))>0){
								$parametros["{$llave->getPropiedad("nombre")}"]=$this->sesion->leerParametroDestinoActual($llave->getPropiedad("nombre"));
							}
						}
						$VO=$this->DAO->getRegistroCondiciones($parametros);
					}
					/*
					msg::add("El VO consultado es");
					msg::add($VO);
					*/
					$campos=$this->CClase->getCampos();
					foreach($campos as $campo){
						if(strcmp(strtoupper($campo->getPropiedad("llavePrimaria")),"TRUE")==0 || strcmp($campo->getPropiedad("nombre"),"activo")==0){
							if(strcmp($this->sesion->leerParametroFormularioActual($campo->getPropiedad("nombre")),"")!=0){
								$funcionSet="set".ucfirst($campo->getPropiedad("nombre"));
								$VO->$funcionSet($this->sesion->leerParametroFormularioActual($campo->getPropiedad("nombre")));
							}else{
								if(strcmp($this->sesion->leerParametroDestinoActual($campo->getPropiedad("nombre")),"")!=0){
									$funcionSet="set".ucfirst($campo->getPropiedad("nombre"));
									$VO->$funcionSet($this->sesion->leerParametroDestinoActual($campo->getPropiedad("nombre")));
								}
							}
						}else{
							if(strcmp($campo->getPropiedad("tipo"),"xml")==0){
								try{
									if (strcmp($tipo, "desactivar")!=0){
										$funcionSet="set".ucfirst($campo->getPropiedad("nombre"));
										$controlXMLPropiedades=new ControlXMLPropiedades();
										$VO->$funcionSet($controlXMLPropiedades->procesarFormulario($campo->getPropiedad("nombre"),$this->CClase->getPropiedad("nombre"), $campo->getPropiedad("nombre")));
									}
								}catch(Exception $e){
									if(strcmp($this->sesion->leerParametroDestinoActual($campo->getPropiedad("nombre")),"")!=0){
										$funcionSet="set".ucfirst($campo->getPropiedad("nombre"));
										$valor=$this->sesion->leerParametroDestinoActual($campo->getPropiedad("nombre"));
										if(strcmp($campo->getPropiedad("tipo"),"clave")==0){
											$valor=md5($valor);
										}
										$VO->$funcionSet($valor);
									}	
								}
							}else{
								if(strcmp($this->sesion->leerParametroDestinoActual($campo->getPropiedad("nombre")),"")!=0){
									$funcionSet="set".ucfirst($campo->getPropiedad("nombre"));
									$valor=$this->sesion->leerParametroDestinoActual($campo->getPropiedad("nombre"));
									if(strcmp($campo->getPropiedad("tipo"),"clave")==0){
										$valor=md5($valor);
									}
									$VO->$funcionSet($valor);
								}
							}
						}
					}
					/*msg::add("El VO procesado es");
					msg::add($VO);
					*/
					
					$this->voUltimoProceso=$VO;
					switch($tipo){
						case "crear":
							$estado=true;
							$this->llamarFuncion($this->prefuncion,$VO,$estado);
							$this->sesion->escribirParametroInterno("{$this->id}ControlDimec","datosFormulario",base64_encode(serialize($VO)));
							if($this->DAO->agregarRegistro($VO)){
								$this->nuevoMensaje("Registro creado correctamente.");
								$this->sesion->borrarParametroInterno("{$this->id}ControlDimec","datosFormulario");
								$db=$this->sesion->getDB();
								$this->llamarFuncion($this->posfuncion,$VO,$estado);
								return $db->ultimoId;
							}else{
								$this->nuevoMensajeError("No se pudo crear el registro.");
								$estado=false;
							}
							$this->llamarFuncion($this->posfuncion,$VO,$estado);
							break;
						case "modificar":
							$estado=true;
							$this->llamarFuncion($this->prefuncion,$VO,$estado);
							if($this->DAO->actualizarRegistro($VO)){
								
								$this->nuevoMensaje("Registro actualizado correctamente.");
								$this->llamarFuncion($this->posfuncion,$VO,$estado);
								return true;
							}else{
								$this->nuevoMensajeError("No se pudo actualizar el registro.");
								$estado=false;
							}
							$this->llamarFuncion($this->posfuncion,$VO,$estado);
							break;
						case "borrar":
							$estado=true;
							$this->llamarFuncion($this->prefuncion,$VO,$estado);
							if($this->DAO->eliminarRegistro($VO)){
								$this->nuevoMensaje("Registro eliminado correctamente.");
								$this->llamarFuncion($this->posfuncion,$VO,$estado);
								return true;
							}else{
								$this->nuevoMensajeError("No se pudo eliminar el registro.");
								$estado=false;
							}
							$this->llamarFuncion($this->posfuncion,$VO,$estado);
							break;
						case "desactivar":
							$estado=true;
							$this->llamarFuncion($this->prefuncion,$VO,$estado);
							//msg::add($VO);
							if($this->DAO->actualizarRegistro($VO)){
								$this->nuevoMensaje("Registro activado/desactivado correctamente.");
								$this->llamarFuncion($this->posfuncion,$VO,$estado);
								return true;
							}else{
								$this->nuevoMensajeError("No se pudo activado/desactivar el registro.");
								$estado=false;
							}
							$this->llamarFuncion($this->posfuncion,$VO,$estado);
							break;
					}
				}
			}catch(valorNuloInvalido $e){
				$this->nuevoMensajeError($e->getMessage());
			}catch(XMLSQLExcepcionRegistroDuplicado $e){
				$this->sesion->escribirParametroInterno("{$this->id}ControlDimec","registroDuplicado",$e->getMessage());
				$tmp=json_decode($e->getMessage(),true);
				$this->nuevoMensajeError($tmp["mensaje"]);
			}catch(Exception $e){
				$this->nuevoMensajeError($e->getMessage());
			}
			return false;
		}
	}

###################################################
#	Clase encargada de procesar peticiones Ajax   #
###################################################

	class XControlDimec{
		function cambiarClave($xml,$propiedades){
			$sesion=Sesion::getInstancia();
            $r=new RespuestaAjax($xml);
            
            if(
            	isset($propiedades["clase"])&&
            	isset($propiedades["campo"])&&
            	isset($propiedades["claveAnterior"])&&
            	isset($propiedades["nuevaClave"])&&
            	isset($propiedades["nuevaClave2"])&&
            	isset($propiedades["id"])&&
            	isset($propiedades["vo"])
            ){
				if(strlen($propiedades["vo"])<=0){return;}
	           	if(strlen($propiedades["campo"])<=0){return;}
    	       	if(strlen($propiedades["claveAnterior"])<=0){return;}
    	       	

	        	$VO=unserialize($propiedades["vo"]);
	        	$funcionGet="get".ucfirst($propiedades["campo"]);
	        	$funcionSet="set".ucfirst($propiedades["campo"]);

				if(!method_exists($VO,$funcionGet)){
					$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","La función ".get_class($VO)."::$funcionGet() no existe.");
					return;
				}
				if(!method_exists($VO,$funcionSet)){
					$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","La función ".get_class($VO)."::$funcionSet() no existe.");
					return;
				}
				
				$dao="DAO".$propiedades["clase"];
				if(!class_exists($dao)){
					$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","La clase $dao no existe.");
					return;
				}
				$dao=new $dao($sesion->getDB());
				try{
					$VO->$funcionSet(md5($propiedades["claveAnterior"]));
					if(strlen($propiedades["nuevaClave"])<0||strcmp($propiedades["nuevaClave"],$propiedades["nuevaClave2"])!=0){
						$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","La clave y su verificación no coinciden.".$propiedades["nuevaClave"].",".$propiedades["nuevaClave2"]);
			       		return;
			       	}
					$registro=$dao->getRegistros($VO);
					
					if(count($registro)==1){
						$patron=$registro[0]->$funcionGet();
						if(strcmp(md5($propiedades["claveAnterior"]),$patron)==0){
							$VO->$funcionSet(md5($propiedades["nuevaClave"]));
							try{
								if($dao->actualizarRegistro($VO)){
									$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","Se cambio la contraseña.");
									$r->script("cerrarDialogo{$propiedades["id"]}dialogo{$propiedades["campo"]}();");
								}else{
									$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","No se pudo cambiar la contraseña.");
								}
							}catch(Exception $e){
								$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"No se pudo cambiar la contraseña.");
							}
						}else{
							$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","Contraseña anterior errónea.");	
						}
					}
				}catch(Exception $e){
					$r->asignar("#".$propiedades["id"]."Mensaje".$propiedades["campo"],"innerHTML","Error, no se pudo cambiar la contraseña.");	
				}
			}
		}
		function procesarFormulario($xml){
			$r=new RespuestaAjax($xml);

			$sesion=Sesion::getInstancia();
			$tmp=explode(";",base64_decode($sesion->leerParametroDestinoActual("id")));

			if(count($tmp)==2){
				$ControlDimec=new ControlDimec($sesion,$tmp[1],$tmp[0]);
				if($ControlDimec->procesarFormulario()){
					$especificacion=json_decode(base64_decode($sesion->leerParametroDestinoActual("posOperacion")),true);
					$clase=isset($especificacion["clase"])?(string)$especificacion["clase"]:"";
					$metodo=isset($especificacion["funcion"])?(string)$especificacion["funcion"]:"";
					$propiedades=isset($especificacion["propiedades"])?$especificacion["propiedades"]:null;
					if(method_exists($clase,$metodo)){
						$obj=@new $clase();
						$obj->$metodo($r,$ControlDimec->voUltimoProceso,$propiedades);
					}
					//$r->alerta(print_r($ControlDimec->mensajes,true));
					$r->alerta($this->obtenerMensaje($ControlDimec->mensajes));
				}else{
					$r->asignar("#mensajes".$tmp[0],"innerHTML",$this->obtenerMensajeError($ControlDimec->mensajes));
				}
			}
		}
		function actualizarListaSeleccion($r,$vo,$propiedades){
			if(!is_null($propiedades)>0){
				if(isset($propiedades["objetoPadre"])&&isset($propiedades["funcionGetTexto"])&&isset($propiedades["funcionGetValor"])&&isset($propiedades["nombreDaoTablaForanea"])){
					try{
						$sesion=Sesion::getInstancia();
						$nombreDaoTablaForanea=(string)$propiedades["nombreDaoTablaForanea"];
						$DAOTmp=new $nombreDaoTablaForanea($sesion->getDb());
						$funcionGetTexto=(string)$propiedades["funcionGetTexto"];
						$funcionGetValor=(string)$propiedades["funcionGetValor"];
						$registros=$DAOTmp->getRegistros();
						if(count($registros)<=0){
							//$this->retornarError("No se encontraron {$this->CClase->get("nombre")}s");
						}
						$html="";
						foreach($registros as $registro){
							$seleccionado="";
							if($vo->$funcionGetValor()==$registro->$funcionGetValor()){
								$seleccionado="selected";
							}
							$html.="<option value='".$registro->$funcionGetValor()."' $seleccionado >".$registro->$funcionGetTexto()."</option>";
						}
						$r->asignar("#".$propiedades["objetoPadre"],"innerHTML",$html);
						$r->script("$('#".$propiedades["idMiContenedor"]."').dialog('close');");
					}catch(sinResultados $e){
						//$this->retornarError($e->getMessage());
					}catch(Exeption $e){
						//$this->retornarError($e->getMessage());
					}
				}
			}
		}

		function obtenerMensaje($arreglo){
			if(isset($arreglo["ERROR"])){
				if(is_array($arreglo["ERROR"])){
					$html="";
					foreach($arreglo["ERROR"] as $mensaje){
						$html.=$mensaje."<br>";
					}
					$html=mensaje::crearMensajeError($html);
					return $html;
				}
			}
			if(isset($arreglo["ALERTA"])){
				if(is_array($arreglo["ALERTA"])){
					$html="";
					foreach($arreglo["ALERTA"] as $mensaje){
						$html.=$mensaje."\n";
					}
					return $html;
				}
			}
			return false;
		}
	}
?>
