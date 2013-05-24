<?php
	
	class ControlListas extends ControlXML{
	
		public $CClase;
		private $sesion;
		private $id;
		private $DAO;
		private $camposConFiltro;
		private $camposEnListadoPrincipal;
		private $componentesAnexosALista;
		private $parametrosDIMEC;
		private $itemSeleccionado;
		private $titulos;
		private $prefunciones;
		private $posfunciones;
		public $propiedades;
		private $peticionPorAjax=false;
		
		
		function ControlListas($sesion,$nombreClase,$id,$camposEnListadoPrincipal=array(),$camposConFiltro=array(),$parametrosDIMEC=array(),$componentesAnexosALista=array(),$propiedadesNavegacion="",$titulos=array(),$prefunciones=array(),$posfunciones=array()){
			$this->sesion=Sesion::getInstancia();
			
			$this->propiedades=new CPropiedades();
			
			$this->prefunciones=$prefunciones;
			$this->posfunciones=$posfunciones;
			
			$this->titulos=$titulos;
			
			if(is_string($nombreClase)){
				$this->CClase=ControlClases::getCClase($nombreClase);
			}else{
				if(is_object($nombreClase)){
					$this->CClase=new CClase($nombreClase);
				}
			}
			if(is_null($parametrosDIMEC)){
				$this->parametrosDIMEC=array("nuevo","editar","borrar","filtro","desactivar");
			}else{
				$this->parametrosDIMEC=$parametrosDIMEC;
			}
			$campos=$this->CClase->getCCampos();
			
			$this->camposEnListadoPrincipal=$camposEnListadoPrincipal;

			if(count($this->camposEnListadoPrincipal)<=0){
				$this->camposEnListadoPrincipal=array();
				foreach($campos as $campo){
					$this->camposEnListadoPrincipal[]=(string)$campo->get("nombre");
				}
			}
			
			$campos=&$this->CClase->getCCampos();
			foreach($campos as $campo){
				$prop=$campo->get("listadoPrincipal");
				$respuesta=$this->buscarCampo($campo->get("nombre"));
				if(strcmp($prop,"")==0){
					if($respuesta["encontrado"]){
						$campo->set("listadoPrincipal","true");
					}else{
						$campo->set("listadoPrincipal","false");
					}
				}else{
					if($respuesta["encontrado"]){
						$campo->set("listadoPrincipal","true");
					}else{
						$campo->set("listadoPrincipal","false");
					}
				}
			}
			$this->id=$id;
			$this->camposConFiltro=$camposConFiltro;
			$this->componentesAnexosALista=$componentesAnexosALista;
			$this->itemSeleccionado=json_decode($this->sesion->leerParametro("{$this->id}ListaDIMEC_parametrosSeleccion"),true);
			$DAO="DAO".$this->CClase->getPropiedad("nombre");
			$this->DAO=new $DAO($this->sesion->getDB());
			
			######## Paginación ########
			$this->ControlPaginacion=new ControlPaginacion($sesion,$this->id,$this->id,$propiedadesNavegacion);
			
		}
		
		function buscarCampo($valor){
			foreach($this->camposEnListadoPrincipal as $campo){
				if(is_string($campo)){
					if(strcmp($campo,$valor)==0){
						return array("tipo"=>"normal","encontrado"=>true);
					}
				}
				if(is_array($campo)){
					if(isset($campo["campo"])&&isset($campo["clase"])&&isset($campo["funcion"])){
						if(strcmp($campo["campo"],$valor)==0){
							return array("tipo"=>"componente","encontrado"=>true,"clase"=>$campo["clase"],"funcion"=>$campo["funcion"]);
						}
					}
				}
			}
			return array("encontrado"=>false);
		}
		
		function generarContenidoEn($xml,$titulo){
			######### Validación de parametros #########
			if(is_null($xml)){ return $this->retornarError("XML destino es nulo."); }
			if(!is_object($xml)){ return $this->retornarError("XML no es un objeto SimpleXMLElement."); }
			if(strcmp(get_class($xml),"SimpleXMLElement")!=0){ return $this->retornarError("XML no es un objeto SimpleXMLElement."); }
			if(strcmp($titulo,"")==0) $titulo=$this->CClase->getPropiedad("nombre");
			######### Procesar URL ##########
			$renderizarLista=true;
			######### Procesar paginación ########
			$this->ControlPaginacion->procesarPeticionNavegador();
			if (isset($this->sesion->args[1])){
				if(strcmp($this->sesion->args[1],$this->id)==0){
					for($i=2;$i<count($this->sesion->args);$i++){
						if(isset($this->sesion->args["{$i}"])){
							switch($this->sesion->args["{$i}"]){
								case "seleccionar":
									if(isset($this->sesion->args["".($i+1).""])){
										$parametrosOcultos=$this->sesion->leerParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										$this->sesion->borrarParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										if(strcmp($parametrosOcultos,"")!=0){
											$parametrosOcultos=json_decode($parametrosOcultos,true);
											$llave=base64_decode($this->sesion->args["".($i+1).""]);
											if(isset($parametrosOcultos[$llave])){
												$this->itemSeleccionado=json_decode($parametrosOcultos[$llave],true);
												$this->sesion->escribirParametro("{$this->id}ListaDIMEC_parametrosSeleccion",$parametrosOcultos[$llave]);
											}
										}
									}
									break;
								case "editar":
									if(isset($this->sesion->args["".($i+1).""])){
										$parametrosOcultos=$this->sesion->leerParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										$this->sesion->borrarParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										if(strcmp($parametrosOcultos,"")!=0){
											$parametrosOcultos=json_decode($parametrosOcultos,true);
											$llave=base64_decode($this->sesion->args["".($i+1).""]);
											if(isset($parametrosOcultos[$llave])){
												$ids=json_decode($parametrosOcultos[$llave],true);
												$ControlDimec=new ControlDimec($this->sesion,$this->CClase,$this->id,array(),$this->titulos);
												$ControlDimec->generarContenido($xml,"modificar","Editar registro",$ids);
												$renderizarLista=false;
											}
										}
									}
									break;
								case "borrar":
									if(isset($this->sesion->args["".($i+1).""])){
										$parametrosOcultos=$this->sesion->leerParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										$this->sesion->borrarParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										if(strcmp($parametrosOcultos,"")!=0){
											$parametrosOcultos=json_decode($parametrosOcultos,true);
											$llave=base64_decode($this->sesion->args["".($i+1).""]);
											if(isset($parametrosOcultos[$llave])){
												$ids=json_decode($parametrosOcultos[$llave],true);
												$ControlDimec=new ControlDimec($this->sesion,$this->CClase,$this->id);
												$ControlDimec->generarContenido($xml,"borrar","¿Borrar registro?",$ids,array(),$this->titulos);
												$renderizarLista=false;
											}
										}
									}
									break;
								case "activar":
									if(isset($this->sesion->args["".($i+1).""])){
										$parametrosOcultos=$this->sesion->leerParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										$this->sesion->borrarParametro("{$this->id}ListaDIMEC_parametrosOcultos");
										if(strcmp($parametrosOcultos,"")!=0){
											$parametrosOcultos=json_decode($parametrosOcultos,true);
											$llave=base64_decode($this->sesion->args["".($i+1).""]);
											if(isset($parametrosOcultos[$llave])){
												$ids=json_decode($parametrosOcultos[$llave],true);
												$ControlDimec=new ControlDimec($this->sesion,$this->CClase,$this->id);
												$ControlDimec->generarContenido($xml,"desactivar","Activar/Desactivar registro",$ids,array(),$this->titulos);
												$renderizarLista=false;
											}
										}
									}
									break;
							}
						}
					}
				}
			}
		
			if($renderizarLista){
				$lista=xml::add($xml,"Lista");
				$propiedades=xml::add($lista,"Propiedades");
				xml::add($propiedades,"Propiedad",array("nombre"=>"titulo","valor"=>"$titulo"));
				xml::add($propiedades,"Propiedad",array("nombre"=>"id","valor"=>"{$this->id}ListaDIMEC_Contenedor"));
				$componentes=xml::add($lista,"Componentes");
			
				######### Formulario Nuevo Registro #########
				if(is_array($this->parametrosDIMEC)){
					if(in_array("nuevo",$this->parametrosDIMEC)){
						$componente=xml::add($componentes,"Componente",array("panel"=>"inferior"));
						$script="
							<script type='text/javascript'>
								$(function() {
									var {$this->id}efectoContenedorNuevo=0;
									function {$this->id}runEffectContenedorNuevo(){
										var obj=$('#{$this->id}ListaDIMEC_contenedorFormularioNuevo');
										var obj2=$('#{$this->id}ListaDIMEC_contenedorFiltros');
										var cont=$('#{$this->id}ListaDIMEC_Contenedor');
										if(obj.hasClass('ui-helper-hidden')){
											obj.removeClass('ui-helper-hidden');
											obj2.addClass('ui-helper-hidden');
											cont.hide('blind','{}',500,{$this->id}callback_mostrar);
										}else{
											obj.addClass('ui-helper-hidden');
											if({$this->id}efectoContenedorNuevo>0){
												cont.show('blind');
												{$this->id}callback_ocultar();
											}else{
												{$this->id}efectoContenedorNuevo=1;
											}
											
										}
									};
									function {$this->id}callback_ocultar(){
										setTimeout(function(){
											$('#{$this->id}ListaDIMEC_Contenedor:hidden').removeAttr('style').hide().fadeIn();
										}, 1000);
									};
									function {$this->id}callback_mostrar(){
										setTimeout(function(){
											$('#{$this->id}ListaDIMEC_Contenedor:visible').removeAttr('style').hide().fadeOut();
										}, 1000);
									};
									{$this->id}runEffectContenedorNuevo();
									$('#{$this->id}ListaDIMEC_botonContenedorFormularioNuevo').click(function() {
										{$this->id}runEffectContenedorNuevo();
										return false;
									});
								});
							</script>
						";
						$contenedor=xml::add($componente,"htmlencodeado",base64_encode($script));
						$contenedor=xml::add($componente,"Boton",array("id"=>$this->id."ListaDIMEC_botonContenedorFormularioNuevo","imagen"=>"{\"src\":\"".resolverPath("/../Librerias/img/agregar.png")."\"}"));
						$contenedor=xml::add($componente,"Contenedor",array("id"=>$this->id."ListaDIMEC_contenedorFormularioNuevo","estilo"=>"background:white;color:black;"));
						$ControlDimec=new ControlDimec($this->sesion,$this->CClase,$this->id);
						$ControlDimec->generarContenido($contenedor,"nuevo","Nuevo",array(),array(),$this->titulos);
					}
				}

				######### Formulario Filtros de Registros #########
				$consultaFiltros=new SimpleXMLElement("<Consulta />");
				$Condiciones=xml::add($consultaFiltros,"Condiciones");
				$Y=xml::add($Condiciones,"Y");
				$Relacion=xml::add($consultaFiltros,"Relacion");
				$banderaAgregarTablaARelacion=true;
				if(is_array($this->parametrosDIMEC)){
					if(in_array("filtro",$this->parametrosDIMEC)){
						$ponerBotonFiltrar=false;
						$camposFiltros=array();
						$filtrar=$this->sesion->leerParametroDestinoActual("{$this->id}ListaDIMEC_filtrosOperacion");
						$llenarConsultaFiltro=false;
						if(strcmp($filtrar,"Filtrar")==0){
							$llenarConsultaFiltro=true;
						}
						foreach($this->camposConFiltro as $filtro){
							if(isset($filtro["tipo"])){
								switch($filtro["tipo"]){
									//ToDo: Hacer filtros por fechas y rangos de fechas
									//Filtros por lista de seleccion
									//Filtros por RexExp
									case "campoBuscar":
										if(isset($filtro["nombre"])&&isset($filtro["camposAfectados"])){
											$tituloFiltro="Buscar por";
											if(isset($filtro["titulo"])){
												$tituloFiltro=$filtro["titulo"];
											}
											$camposFiltros[]=array("tipo"=>"cadena","nombre"=>"{$this->id}ListaDIMEC_{$filtro["nombre"]}","titulo"=>"{$tituloFiltro}");
											if($llenarConsultaFiltro){
												$valor=$this->sesion->leerParametroDestinoActual("{$this->id}ListaDIMEC_{$filtro["nombre"]}");
												if(is_array($filtro["camposAfectados"])){
													$O=xml::add($Y,"O");
													foreach($filtro["camposAfectados"] as $campoAfectado){
														if(isset($campoAfectado["campoForaneo"]) && isset($campoAfectado["tablaForanea"]) && isset($campoAfectado["campoLocal"])){
															if($banderaAgregarTablaARelacion){
																xml::add($Relacion,"Tabla",array("nombre"=>$this->CClase->getPropiedad("nombre"), "campo"=>$campoAfectado["nombre"]));
																$banderaAgregarTablaARelacion=false;
															}
															xml::add($Relacion,"Tabla",array("nombre"=>$campoAfectado["tablaForanea"], "campo"=>$campoAfectado["campoForaneo"], "tablaDestino"=>$this->CClase->getPropiedad("nombre"), "campoDestino"=>$campoAfectado["campoLocal"]));
														}
														if(isset($campoAfectado["nombre"])&&isset($campoAfectado["condicion"])){
															switch($campoAfectado["condicion"]){
																case "igual":
																	xml::add($O,"Igual",array("campo"=>$campoAfectado["nombre"],"tabla"=>isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre"),"valor"=>$valor));
																	break;
																case "diferente":
																	xml::add($O,"Diferente",array("campo"=>$campoAfectado["nombre"],"tabla"=>isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre"),"valor"=>$valor));
																	break;
																case "como":
																	xml::add($O,"Como",array("campo"=>$campoAfectado["nombre"],"tabla"=>isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre"),"valor"=>"%".$valor."%"));
																	break;
																case "comoEstricto":
																	xml::add($O,"Como",array("campo"=>$campoAfectado["nombre"],"tabla"=>isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre"),"valor"=>$valor));
																	break;
															}
														}
													}
												}
											}
											$ponerBotonFiltrar=true;
										}
										break;
									case "constante":
										if(isset($filtro["camposAfectados"])){
											if(is_array($filtro["camposAfectados"])){
												$O=xml::add($Y,"O");
												foreach($filtro["camposAfectados"] as $campoAfectado){
													if(isset($campoAfectado["nombre"])&&isset($campoAfectado["valor"])&&isset($campoAfectado["condicion"])){
														if(isset($campoAfectado["campoForaneo"]) && isset($campoAfectado["tablaForanea"]) && isset($campoAfectado["campoLocal"])){
															if($banderaAgregarTablaARelacion){
																xml::add($Relacion,"Tabla",array("nombre"=>$this->CClase->getPropiedad("nombre"), "campo"=>$campoAfectado["nombre"]));
																$banderaAgregarTablaARelacion=false;
															}
															xml::add($Relacion,"Tabla",array("nombre"=>$campoAfectado["tablaForanea"], "campo"=>$campoAfectado["campoForaneo"], "tablaDestino"=>$this->CClase->getPropiedad("nombre"), "campoDestino"=>$campoAfectado["campoLocal"]));
														}
														switch($campoAfectado["condicion"]){
															case "igual":
																xml::add($O,"Igual",array("campo"=>$campoAfectado["nombre"],"tabla"=>(isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre")),"valor"=>$campoAfectado["valor"]));
																break;
															case "diferente":
																xml::add($O,"Diferente",array("campo"=>$campoAfectado["nombre"],"tabla"=>isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre"),"valor"=>$campoAfectado["valor"]));
																break;
															case "como":
																xml::add($O,"Como",array("campo"=>$campoAfectado["nombre"],"tabla"=>(isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre")),"valor"=>"%".$campoAfectado["valor"]."%"));
																break;
															case "comoEstricto":
																xml::add($O,"Como",array("campo"=>$campoAfectado["nombre"],"tabla"=>(isset($campoAfectado["tablaForanea"])?$campoAfectado["tablaForanea"]:$this->CClase->getPropiedad("nombre")),"valor"=>$campoAfectado["valor"]));
																break;
														}
													}
												}
											}
										}
										break;
									case "constanteDoble":
										//msg::add("Constante doble");
									/*
											"nombre"=>"nombre",
                                            "condicion"=>"igual",
                                            "tablaBase"=>"0Usuario",
                                            "tablaIntermedia"=>"0UsuarioRol",
                                            "tablaRelacion"=>"0Rol",
                                            
                                            "campoBase"=>"idUsuario",
                                            "campoIntermedioBase"=>"idUsuario",
                                            //"campoIntermedio"=>"",
                                            "campoIntermedioRelacion"=>"idRol",
                                            "campoRelacion"=>"idRol",
                                            
                                            "valorRelacion"=>$rol->getIdRol()									
									*/
									
										if(isset($filtro["camposAfectados"])){
											if(is_array($filtro["camposAfectados"])){
												$O=xml::add($Y,"O");
												foreach($filtro["camposAfectados"] as $campoAfectado){
													//msg::add("Campo afectado");
													if(isset($campoAfectado["nombre"])&&isset($campoAfectado["valorRelacion"])&&isset($campoAfectado["condicion"])){
														//if(isset($campoAfectado["campoForaneo"]) && isset($campoAfectado["tablaForanea"]) && isset($campoAfectado["campoLocal"])){
//															if($banderaAgregarTablaARelacion){
//																xml::add($Relacion,"Tabla",array("nombre"=>$this->CClase->getPropiedad("nombre"), "campo"=>$campoAfectado["nombre"]));
//																$banderaAgregarTablaARelacion=false;
//															}
															xml::add($Relacion,"Tabla",array("nombre"=>$campoAfectado["tablaBase"], "campo"=>$campoAfectado["campoBase"], "tablaDestino"=>$campoAfectado["tablaIntermedia"], "campoDestino"=>$campoAfectado["campoIntermedioBase"]));
															xml::add($Relacion,"Tabla",array("nombre"=>$campoAfectado["tablaRelacion"], "campo"=>$campoAfectado["campoRelacion"], "tablaDestino"=>$campoAfectado["tablaIntermedia"], "campoDestino"=>$campoAfectado["campoIntermedioRelacion"]));
														//}
														switch($campoAfectado["condicion"]){
															case "igual":
																xml::add($O,"Igual",array("campo"=>$campoAfectado["campoRelacion"],"tabla"=>$campoAfectado["tablaRelacion"],"valor"=>$campoAfectado["valorRelacion"]));
																break;
															case "como":
																xml::add($O,"Como", array("campo"=>$campoAfectado["campoRelacion"],"tabla"=>$campoAfectado["tablaRelacion"],"valor"=>"%".$campoAfectado["valorRelacion"]."%"));
																break;
															case "comoEstricto":
																xml::add($O,"Como", array("campo"=>$campoAfectado["campoRelacion"],"tabla"=>$campoAfectado["tablaRelacion"],"valor"=>$campoAfectado["valorRelacion"]));
																break;
														}
													}
												}
											}
										}
										break;
								}
							}
						}
						if($ponerBotonFiltrar){
							$componente=xml::add($componentes,"Componente",array("panel"=>"superior"));
							$script="
								<script type='text/javascript'>
									$(function() {
										var {$this->id}efectoContenedorFiltros=0;
										function {$this->id}runEffectContenedorFiltros(){
											var obj=$('#{$this->id}ListaDIMEC_contenedorFiltros');
											var obj2=$('#{$this->id}ListaDIMEC_contenedorFormularioNuevo');
											var cont=$('#{$this->id}ListaDIMEC_Contenedor');
											if(obj.hasClass('ui-helper-hidden')){
												obj.removeClass('ui-helper-hidden');
												obj2.addClass('ui-helper-hidden');
												cont.hide('blind','{}',500,{$this->id}callback_mostrar);
											}else{
												obj.addClass('ui-helper-hidden');
												if({$this->id}efectoContenedorFiltros!=0){
													cont.show('blind','{}',500,{$this->id}callback_ocultar);
												}else{
													{$this->id}efectoContenedorFiltros=1;
												}
											}
										};
										function {$this->id}callback_ocultar(){
											setTimeout(function(){
												$('#{$this->id}ListaDIMEC_Contenedor:hidden').removeAttr('style').hide().fadeIn();
											}, 1000);
										};
										function {$this->id}callback_mostrar(){
											setTimeout(function(){
												$('#{$this->id}ListaDIMEC_Contenedor:visible').removeAttr('style').hide().fadeOut();
											}, 1000);
										};
										
										{$this->id}runEffectContenedorFiltros();
										
										$('#{$this->id}ListaDIMEC_botonContenedorFiltros').click(function() {
											{$this->id}runEffectContenedorFiltros();
											return false;
										});
									});
								</script>
							";
							$contenedor=xml::add($componente,"htmlencodeado",base64_encode($script));
							$contenedor=xml::add($componente,"Boton",array("id"=>$this->id."ListaDIMEC_botonContenedorFiltros","imagen"=>"{\"src\":\"".resolverPath("/../Librerias/img/filtro.png")."\"}"));
							$contenedor=xml::add($componente,"Contenedor",array("id"=>$this->id."ListaDIMEC_contenedorFiltros","estilo"=>"background:white;color:black;"));
							$formularioFiltros=ControlFormulario::generarFormulario($contenedor,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso")));
							foreach($camposFiltros as $campoFiltro){
								ControlFormulario::generarCampo($formularioFiltros,$campoFiltro);
							}
							ControlFormulario::generarCampo($formularioFiltros,array("tipo"=>"enviar","titulo"=>"Filtrar","nombre"=>"{$this->id}ListaDIMEC_filtrosOperacion"));
						}
					}
				}
				######### Llenado de información #########
			
				$informacion=xml::add($lista,"Informacion");
				//echo generalXML::geshiXML($consultaFiltros);


				######## Calculo de paginación #########

				$registros=$this->ControlPaginacion->obtenerRegistros($this->DAO,$consultaFiltros);

				$campos=$this->CClase->getCCampos();

				$titulos=xml::add($informacion,"Titulos");
				foreach($campos as $campo){
					if(strcmp(strtolower($campo->get("listadoPrincipal")),"true")==0){
						if(isset($this->titulos["{$campo->get("nombre")}"])){
							$textoTitulo=$this->titulos["{$campo->get("nombre")}"];
						}else{
							$textoTitulo=$campo->get("nombre");
						}
						xml::add($titulos,"Titulo", array("nombre"=>$textoTitulo));
					}
				}
				$datos=xml::add($informacion,"Datos");
				$parametrosOcultos=array();
				if(count($registros)>0){
					foreach($registros as $no=>$registro){
						$seleccionado=false;
						if(is_array($this->itemSeleccionado)){
							$seleccionado=true;
							foreach($this->itemSeleccionado as $nombrCampo=>$valorCampo){
								$funcionGet="get".ucfirst($nombrCampo);
								if($registro->$funcionGet()!=$valorCampo){
									$seleccionado=false;
									break;
								}
							}
						}
				
						$fila=xml::add($datos,"Fila",array("class"=>($seleccionado?"ui-state-focus":"")));
					
						$llaves=$this->CClase->getLlaves();
						$parametros=array();
						foreach($llaves as $llave){
							$funcionGet="get".ucfirst($llave->getPropiedad("nombre"));
							$parametros["{$llave->getPropiedad("nombre")}"]=$registro->$funcionGet();
						}
						$parametros=json_encode($parametros);
						do{
							$idRdm=mt_rand();
						}while(array_key_exists($idRdm, $parametrosOcultos));
						$parametrosOcultos[$idRdm]=$parametros;
					
						foreach($campos as $campo){
							if(strcmp(strtolower($campo->get("listadoPrincipal")),"true")==0){
								$respuesta=$this->buscarCampo($campo->get("nombre"));
								$columna=xml::add($fila,"Columna");
								$funcionGet=$campo->get("funcionGet");
								$valorPorDefecto=$registro->$funcionGet();
								if($campo->esForanea()){
									$tipoCampo="ListaSeleccion";
									try{
										$nombreDAO="DAO".$campo->get("tablaClaveForanea");
										$DAOTmp=new $nombreDAO($this->sesion->db);
										$funcionGetTexto="get".ucfirst($campo->get("campoTextoClaveForanea"));
										$registroTmp=$DAOTmp->getRegistroCondiciones(array("{$campo->get("campoClaveForanea")}"=>$valorPorDefecto));
										$valorPorDefecto=$registroTmp->$funcionGetTexto();
									}catch(sinResultados $e){
										$valorPorDefecto="--";
									}catch(Exeption $e){
										msg::add("Lista ".$this->id.":".$e->getMessage());
										return null;
									}
								}
								if(isset($respuesta["tipo"])){
									switch($respuesta["tipo"]){
										case "normal":
											if(strcmp($campo->get("tipo"),"xml")==0){
												$valorPorDefecto=generalPhp::geshi($valorPorDefecto,"xml");
											}
											$textoCelda="<a href='".resolverPath()."/".$this->sesion->leerParametro("nombreCasoUso")."/".$this->id."/seleccionar/".base64_encode($idRdm)."'>{$valorPorDefecto}</a>";
											xml::add($columna,"Html",$textoCelda);
											break;
										case "componente":
											if(isset($respuesta["clase"])&&isset($respuesta["funcion"])){
												$claseTmp=@new $respuesta["clase"]();
												$claseTmp->$respuesta["funcion"]($columna, json_decode($parametros,true),$valorPorDefecto);
											}
											break;
									}
								}
							
							}
						}
					
						$columna=xml::add($fila,"Columna");
						if(is_array($this->parametrosDIMEC)){
							if(in_array("editar",$this->parametrosDIMEC)){
								$boton=xml::add($columna,"Boton",array("id"=>$this->id."ListaDIMEC_botonEditar".$no,"imagen"=>"{\"src\":\"".resolverPath("/../Librerias/img/editar.png")."\",\"width\":\"20\"}","path"=>resolverPath("/".$this->sesion->leerParametro("nombreCasoUso")."/".$this->id."/editar/".base64_encode($idRdm))));
							}
							if(in_array("borrar",$this->parametrosDIMEC)){
								$boton=xml::add($columna,"Boton",array("id"=>$this->id."ListaDIMEC_botonBorrar".$no,"imagen"=>"{\"src\":\"".resolverPath("/../Librerias/img/borrar.png")."\",\"width\":\"20\"}","path"=>resolverPath("/".$this->sesion->leerParametro("nombreCasoUso")."/".$this->id."/borrar/".base64_encode($idRdm))));
							}
							if($this->CClase->esDesactivable()){
								if(in_array("desactivar",$this->parametrosDIMEC)){
									if(intval($registro->getActivo())==1){
										$imagenTmp="activo.png";
									}else{
										$imagenTmp="noactivo.png";
									}
									$boton=xml::add($columna,"Boton",array("id"=>$this->id."ListaDIMEC_botonDesactivar".$no,"imagen"=>"{\"src\":\"".resolverPath("/../Librerias/img/$imagenTmp")."\",\"width\":\"20\"}","path"=>resolverPath("/".$this->sesion->leerParametro("nombreCasoUso")."/".$this->id."/activar/".base64_encode($idRdm))));
								}
							}
						}
						if(is_array($this->componentesAnexosALista)){
							foreach($this->componentesAnexosALista as $componenteAnexo){
								if(is_object($componenteAnexo)){
									if(strcmp(get_class($componenteAnexo),"SimpleXMLElement")==0){
										$componenteAnexo=new SimpleXMLElement(str_replace("{idContolLista}",base64_encode($parametros),$componenteAnexo->asXML()));
										append_simplexml($columna,$componenteAnexo);
									}
								}
								if(is_array($componenteAnexo)){
									if(isset($componenteAnexo["clase"])&&isset($componenteAnexo["funcion"])&&isset($componenteAnexo["campos"])){
										$nombreClase=(string)$componenteAnexo["clase"];
										if(method_exists($nombreClase,$componenteAnexo["funcion"])){
											$claseTmp=new $nombreClase();
											$camposParam=array();
											if(is_array($componenteAnexo["campos"])){
												foreach($componenteAnexo["campos"] as $campo){
													$funcionGet="get".ucfirst($campo);
													$camposParam["$campo"]=$registro->$funcionGet();
												}
											}
											$claseTmp->$componenteAnexo["funcion"]($columna, json_decode($parametros,true), $camposParam);
										}
									}
								}
							}
						}
					}
				}else{
					xml::add($lista,"Wiki","No hay registros.");
				}
				$this->sesion->escribirParametro("{$this->id}ListaDIMEC_parametrosOcultos",json_encode($parametrosOcultos));
				$navegador=xml::add($lista,"Navegacion");
				$this->ControlPaginacion->generarNavegador($navegador);
			}

			return true;
		}
		
		function generarContenido(){
			$contenido=new SimpleXMLElement("<Contenido />");
			$this->generarContenidoEn($contenido,"");
			return $contenido;
		}
		
		function procesarFormularioSinContenido(){
			$id=$this->sesion->leerParametroFormularioActual("id");
			if(strlen($id)<=0){
				$id=$this->sesion->leerParametroDestinoActual("id");
			}
			if(strlen($id)>0){
				$tmp=explode(";",base64_decode($id));
				if(count($tmp)>1){
					$id=$tmp[0];
				}
			}
			if(strcmp($id,$this->id)==0){
				$ControlDimec=new ControlDimec($this->sesion,$this->CClase,$this->id);
				if(isset($this->prefunciones["nuevo"])){
					if(isset($this->prefunciones["nuevo"]["clase"])&&isset($this->prefunciones["nuevo"]["funcion"])){
						$ControlDimec->setPrefuncion((string)$this->prefunciones["nuevo"]["clase"],(string)$this->prefunciones["nuevo"]["funcion"]);
					}
				}
				if(isset($this->posfunciones["nuevo"])){
					if(isset($this->posfunciones["nuevo"]["clase"])&&isset($this->posfunciones["nuevo"]["funcion"])){
						$ControlDimec->setPosfuncion((string)$this->posfunciones["nuevo"]["clase"],(string)$this->posfunciones["nuevo"]["funcion"]);
					}
				}
				if(isset($this->prefunciones["modificar"])){
					if(isset($this->prefunciones["modificar"]["clase"])&&isset($this->prefunciones["modificar"]["funcion"])){
						$ControlDimec->setPrefuncion((string)$this->prefunciones["modificar"]["clase"],(string)$this->prefunciones["modificar"]["funcion"]);
					}
				}
				if(isset($this->posfunciones["modificar"])){
					if(isset($this->posfunciones["modificar"]["clase"])&&isset($this->posfunciones["modificar"]["funcion"])){
						$ControlDimec->setPosfuncion((string)$this->posfunciones["modificar"]["clase"],(string)$this->posfunciones["modificar"]["funcion"]);
					}
				}
				if(isset($this->prefunciones["borrar"])){
					if(isset($this->prefunciones["borrar"]["clase"])&&isset($this->prefunciones["borrar"]["funcion"])){
						$ControlDimec->setPrefuncion((string)$this->prefunciones["borrar"]["clase"],(string)$this->prefunciones["borrar"]["funcion"]);
					}
				}
				if(isset($this->posfunciones["borrar"])){
					if(isset($this->posfunciones["borrar"]["clase"])&&isset($this->posfunciones["borrar"]["funcion"])){
						$ControlDimec->setPosfuncion((string)$this->posfunciones["borrar"]["clase"],(string)$this->posfunciones["borrar"]["funcion"]);
					}
				}
				if(isset($this->prefunciones["desactivar"])){
					if(isset($this->prefunciones["desactivar"]["clase"])&&isset($this->prefunciones["desactivar"]["funcion"])){
						$ControlDimec->setPrefuncion((string)$this->prefunciones["desactivar"]["clase"],(string)$this->prefunciones["desactivar"]["funcion"]);
					}
				}
				if(isset($this->posfunciones["desactivar"])){
					if(isset($this->posfunciones["desactivar"]["clase"])&&isset($this->posfunciones["desactivar"]["funcion"])){
						$ControlDimec->setPosfuncion((string)$this->posfunciones["desactivar"]["clase"],(string)$this->posfunciones["desactivar"]["funcion"]);
					}
				}
				$id=$ControlDimec->procesarFormulario();
				if(is_numeric($id)){
					$this->propiedades->addPropiedad("ultimoId",$id);
				}
			}
			return true;
		}
		
		function procesarFormulario(){
			$this->procesarFormularioSinContenido();
			return $this->generarContenido();
		}
		
		function retornarError($mensaje){
			new mensajes("Lista ".$this->id.":".$mensaje);
			return null;
		}
	}
	class AsistenteControlListas{
		function procesarXMLDatosFormularioGenericoTodos($xmlContenido, $llave, $campo){
			//throw new Exception('procesarXMLDatosFormulario');
			try{
				$xml=new SimpleXMLElement($campo);
				$textoWiki="";
				foreach($xml->children() as  $nodo){
					//foreach($xml->attributes() as $nombre => $nodo){
					$nombre=(string)$nodo["nombre"];
					$nombre{0}=strtoupper($nombre{0});
					$textoWiki.="'''".$nombre." : '''".$nodo["valor"]."\n";
				}
				xml::add($xmlContenido, "Wiki", $textoWiki);
			}catch(Exception $e){
				xml::add($xmlContenido, "Wiki", "Sin información");
			}
		}
		function procesarXML_AtributoNombre_ContenidoValor($xmlContenido, $llave, $campo){
			//throw new Exception('procesarXMLDatosFormulario');
			try{
				$xml=new SimpleXMLElement($campo);
				$textoWiki="";
				foreach($xml->children() as  $nodo){
					//foreach($xml->attributes() as $nombre => $nodo){
					$nombre=(string)$nodo["nombre"];
					$nombre{0}=strtoupper($nombre{0});
					$textoWiki.="'''".$nombre." : '''".$nodo."\n";
				}
				xml::add($xmlContenido, "Wiki", $textoWiki);
			}catch(Exception $e){
				xml::add($xmlContenido, "Wiki", "Sin información");
			}
		}
		function procesarXMLDatosFormularioGenericoTodosXML($xmlContenido, $llave, $campo){
			//throw new Exception('procesarXMLDatosFormulario');
			try{
				$xml=new SimpleXMLElement($campo);
				$textoWiki="";
				foreach($xml->children() as  $nodo){
					//foreach($xml->attributes() as $nombre => $nodo){
					$nombre=(string)$nodo->getName();
					$nombre{0}=strtoupper($nombre{0});
					$textoWiki.="'''".$nombre." : '''".$nodo."\n";
				}
				xml::add($xmlContenido, "Wiki", $textoWiki);
			}catch(Exception $e){
				xml::add($xmlContenido, "Wiki", "Sin información");
			}
		}
	}
?>
