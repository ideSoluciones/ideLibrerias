<?php
	class ControlInforme extends generalXML{
		
		private $sesion;
		private $contenido;
		private $parametro="";
		private $filtros=array();
		private $campos;
		private $paginaActual=1;
		private $totalPaginas=3;
		private $registrosPorPagina;
		private $basePotenciaSaltosPaginas;
		private $accion;
		private $path;
		private $nuevoEstado;
		private $tabla;
		private $filtrosConstantes;
		private $tiposDeFiltros;
		
		function ControlInforme(){
			$this->sesion=Sesion::getInstancia();
		}
		
		function generarContenido($xml,$propiedades){
			//mensaje::add(generalXML::geshiXML($this->sesion->xml));
			$titulo=$propiedades->getPropiedad("titulo");
			ControlXML::agregarNodoTexto($xml,"Wiki","=$titulo=\n");
			if(strlen((string)$propiedades->getPropiedad("tablaBase"))>0){
				$nombreClaseDao="DAO{$propiedades->getPropiedad("tablaBase")}";
				if(!class_exists($nombreClaseDao)){
					mensaje::add("La clase $nombreClaseDao no existe.",ERROR);
					return false;
				}
				$clase=ControlClases::getCClase("{$propiedades->getPropiedad("tablaBase")}");
				// Se crea el ControlPaginacion
				$propPaginacion=$propiedades->getPropiedad("paginacion");
				if(!is_array($propPaginacion)){
					$propPaginacion=array();
				}
				$ControlPaginacion=new ControlPaginacion($this->sesion,"idUnico","",$propPaginacion);
				// Se procesan pedidos de paginación
				$ControlPaginacion->procesarPeticionNavegador();
				try{
					$DAO=new $nombreClaseDao($this->sesion->getDB());
					$consultaFiltros=new SimpleXMLElement("<Consulta />");
					$filtrosActivos=json_decode($this->sesion->leerParametroInterno("Informe","filtros"),true);
					if(!is_array($filtrosActivos)){
						$filtrosActivos=array();
					}
					$filtros=$propiedades->getPropiedad("filtros");
					if(!is_array($filtros)){
						$filtros=array();
					}
					foreach($filtros as $campoFiltrado=>$propiedadesFiltrado){
						if(isset($propiedadesFiltrado["tipo"])){
							if(strcmp($propiedadesFiltrado["tipo"],"constante")==0){
								$filtrosActivos["{$campoFiltrado}"]=array(
									"tipo"=>"Igual",
									"valor"=>$propiedadesFiltrado["valor"]
								);
							}
						}
					}
					if(isset($_COOKIE["filtro"])){
						$peticionCookie=explode(",",$_COOKIE["filtro"]);
						//mensaje::add("COOKIE:".$_COOKIE["filtro"]);
						setcookie("filtro","",-1,"/");
						$peticion=array();
						foreach($peticionCookie as $variable){
							$tmp=explode(":",$variable);
							if(isset($tmp[0])&&isset($tmp[1])){
								$peticion["{$tmp[0]}"]=$tmp[1];
							}
						}
						//mensaje::add(print_r($peticion,true));
						$filtrosActivos["{$peticion["campo"]}"]=array("tipo"=>$peticion["tipo"],"valor"=>$peticion["valor"]);
					}
					//mensaje::add(print_r($filtrosActivos,true));
					if(count($filtrosActivos)>0){
						$Condiciones=ControlXML::agregarNodo($consultaFiltros,"Condiciones");
						$Y=ControlXML::agregarNodo($Condiciones,"Y");
						foreach($filtrosActivos as $nombreCampo=>$propiedadesCampo){
							if(strcmp((string)$propiedadesCampo["valor"],"null")!=0){
								//mensaje::add("tipo:".$propiedadesCampo["tipo"]);
								ControlXML::agregarNodo($Y,$propiedadesCampo["tipo"],array("campo"=>$nombreCampo,"tabla"=>$propiedades->getPropiedad("tablaBase"),"valor"=>$propiedadesCampo["valor"]));
							}
						}
					}
					
					$this->sesion->escribirParametroInterno("Informe","filtros",json_encode($filtrosActivos));
					//echo generalXML::geshiXML($consultaFiltros);
					$registros=$ControlPaginacion->obtenerRegistros($DAO,$consultaFiltros);
					
					$contenedor=ControlXML::agregarNodo($xml,"Contenedor",array("estilo"=>"border:1px solid;overflow:auto;"));
					$campos=$clase->getCampos();
					// Se verifica si hay solicitud de campos a mostrar o si se mostraran todos
					$todosLosCampos=true;
					$camposSolicitados=$propiedades->getPropiedad("campos");
					if(!is_array($camposSolicitados)){
						$camposSolicitados=array();
					}
					
					if(is_array($camposSolicitados)){
						if(count($camposSolicitados)>0){
							$todosLosCampos=false;
						}
					}
					// Se genera el titulo de la tabla
					//$wiki=ControlXML::agregarNodoTexto($contenedor,"Wiki","=DATOS=\n");
					// Se crea la tabla
					$tabla=ControlXML::agregarNodo($contenedor,"Tabla",array("plano"=>"true"));
					// Se genera el encabezado de la tabla de datos
					$cabeza=ControlXML::agregarNodo($tabla,"Cabecera");
					if(count($registros)>0){
						foreach($campos as $campo){
							if(isset($camposSolicitados["{$campo->getPropiedad("nombre")}"]["titulo"])){
								$valor=$camposSolicitados["{$campo->getPropiedad("nombre")}"]["titulo"];
							}else{
								$valor=$campo->getPropiedad("nombre");
							}
							$contenedor=null;
							if($todosLosCampos){
								$contenedor=ControlXML::agregarNodo($cabeza,"Contenedor");
								$wiki=ControlXML::agregarNodoTexto($contenedor,"Wiki",$valor);
							}else{
								if(isset($camposSolicitados["{$campo->getPropiedad("nombre")}"])){
									$contenedor=ControlXML::agregarNodo($cabeza,"Contenedor");
									$wiki=ControlXML::agregarNodoTexto($contenedor,"Wiki",$valor);
								}
							}
							if(isset($filtros["{$campo->getPropiedad("nombre")}"]["tipo"])&&!is_null($contenedor)){
								switch($filtros["{$campo->getPropiedad("nombre")}"]["tipo"]){
									case "campo":
										foreach($filtrosActivos as $nombreCampo=>$propiedadesCampo){
											if(strcmp($nombreCampo,$campo->getPropiedad("nombre"))==0){
												$valorPorDefecto=$propiedadesCampo["valor"];
												break;
											}
										}
										$html=ControlXML::agregarNodoTexto($contenedor,"Html","<div style='width:200px;'><input style='width:60%;height:14px;float:left;' value='{$valorPorDefecto}' id='filtro".$campo->getPropiedad("nombre")."'><div style='cursor:pointer;width:30%;float:left;border:1px solid silver;padding:2px;font-size:10px;' onclick='enviarPeticionCookie(\"filtro\",\"campo:{$campo->getPropiedad("nombre")},tipo:Igual,valor:\"+$(\"#filtro".$campo->getPropiedad("nombre")."\").val());'>Aplicar</div>");
										break;
									case "selector":
										if(
											isset($filtros["{$campo->getPropiedad("nombre")}"]["campoClaveForanea"])&&
											isset($filtros["{$campo->getPropiedad("nombre")}"]["tablaClaveForanea"])&&
											isset($filtros["{$campo->getPropiedad("nombre")}"]["campoTextoClaveForanea"])
										){
											$nombreDao="DAO".$filtros["{$campo->getPropiedad("nombre")}"]["tablaClaveForanea"];
											$nombreVo="VO".$filtros["{$campo->getPropiedad("nombre")}"]["tablaClaveForanea"];
											$funcionGetCampo="get".ucfirst($filtros["{$campo->getPropiedad("nombre")}"]["campoClaveForanea"]);
											$funcionGetTexto="get".ucfirst($filtros["{$campo->getPropiedad("nombre")}"]["campoTextoClaveForanea"]);
											if(class_exists($nombreDao)&&method_exists($nombreVo,$funcionGetCampo)&&method_exists($nombreVo,$funcionGetTexto)){
												try{
													$dao=new $nombreDao($this->sesion->getDB());
													$vos=$dao->getRegistros();
													$html=ControlXML::agregarNodoTexto($contenedor,"Html","<select onchange='enviarPeticionCookie(\"filtro\",\"campo:{$campo->getPropiedad("nombre")},tipo:Igual,valor:\"+this.value);'>");
													$valorPorDefecto="";
													foreach($filtrosActivos as $nombreCampo=>$propiedadesCampo){
														if(strcmp($nombreCampo,$campo->getPropiedad("nombre"))==0){
															$valorPorDefecto=$propiedadesCampo["valor"];
															break;
														}
													}
												
													$consultaFiltrosTmp=new SimpleXMLElement($consultaFiltros->asXML());
													$filtroValPermitidos=new SimpleXMLElement("<Consulta />");
													$agrupar=ControlXML::agregarNodo($filtroValPermitidos,"Agrupar");
													ControlXML::agregarNodo($agrupar,"AgruparCampo",array("campo"=>$campo->getPropiedad("nombre")));
													simplexml_merge($consultaFiltrosTmp,$filtroValPermitidos);
													try{
														$valoresPermitidos=$DAO->getRegistros($consultaFiltrosTmp);
													}catch(Exception $e){
														$valoresPermitidos=array();
													}
													$funcionGetLocal="get".ucfirst($campo->getPropiedad("nombre"));
													$html[].="<option value='null'>Todos</option>";
													foreach($vos as $vo){
														$valorAceptado=false;
														foreach($valoresPermitidos as $valorPermitido){
															if(strcmp($vo->$funcionGetCampo(),$valorPermitido->$funcionGetLocal())==0){
																$valorAceptado=true;
																break;
															}
														}
														if($valorAceptado){
															$seleccionado="";
															if(strcmp($valorPorDefecto,$vo->$funcionGetCampo())==0){
																$seleccionado="selected";
															}
															$html[].="<option value='{$vo->$funcionGetCampo()}' $seleccionado >{$vo->$funcionGetTexto()}</option>";
														}
													}
													$html[].="</select>";
												}catch(Exception $e){}
											}else{
												mensaje::add("No existe el dao especificado.",ERROR);
											}
										}
								
								}
							}
						}
						// Se llena la tabla con los datos
						
						foreach($registros as $registro){
							$fila=ControlXML::agregarNodo($tabla,"Fila");
							foreach($campos as $campo){
								$funcionGet="get".ucfirst($campo->getPropiedad("nombre"));
								if(!method_exists($registro,$funcionGet)){
									mensaje::add("El método ".get_class($registro)."::".$funcionGet."() no existe.",ERROR);
									return false;
								}
								if($todosLosCampos||isset($camposSolicitados["{$campo->getPropiedad("nombre")}"])){
									if(
										isset($camposSolicitados["{$campo->getPropiedad("nombre")}"]["tablaClaveForanea"]) &&
										isset($camposSolicitados["{$campo->getPropiedad("nombre")}"]["campoClaveForanea"]) &&
										isset($camposSolicitados["{$campo->getPropiedad("nombre")}"]["campoTextoClaveForanea"])
									){
										$nombreDaoTmp="DAO".$camposSolicitados["{$campo->getPropiedad("nombre")}"]["tablaClaveForanea"];
										if(!class_exists($nombreDaoTmp)){
											mensaje::add("La clase ".$nombreDaoTmp." no existe.",ERROR);
											return false;
										}
										$nombreVoTmp="VO".$camposSolicitados["{$campo->getPropiedad("nombre")}"]["tablaClaveForanea"];
										if(!class_exists($nombreVoTmp)){
											mensaje::add("La clase ".$nombreVoTmp." no existe.",ERROR);
											return false;
										}
										$daoTmp=new $nombreDaoTmp($this->sesion->getDB());
										$voTmp=new $nombreVoTmp();
										$funcionGetCClaveForanea="get".ucfirst($camposSolicitados["{$campo->getPropiedad("nombre")}"]["campoClaveForanea"]);
										if(!method_exists($voTmp,$funcionGetCClaveForanea)){
											mensaje::add("El método ".$nombreDaoTmp."::".$funcionGetCClaveForanea."() no existe.",ERROR);
											return false;
										}
										$funcionSetCClaveForanea="set".ucfirst($camposSolicitados["{$campo->getPropiedad("nombre")}"]["campoClaveForanea"]);
										if(!method_exists($voTmp,$funcionGetCClaveForanea)){
											mensaje::add("El método ".$nombreDaoTmp."::".$funcionSetCClaveForanea."() no existe.",ERROR);
											return false;
										}
										$funcionGetCTexClaveForanea="get".ucfirst($camposSolicitados["{$campo->getPropiedad("nombre")}"]["campoTextoClaveForanea"]);
										if(!method_exists($voTmp,$funcionGetCTexClaveForanea)){
											mensaje::add("El método ".$nombreDaoTmp."::".$funcionGetCTexClaveForanea."() no existe.",ERROR);
											return false;
										}
										$voTmp->$funcionSetCClaveForanea($registro->$funcionGet());
										$llaves=array($camposSolicitados["{$campo->getPropiedad("nombre")}"]["campoClaveForanea"]=>$registro->$funcionGet());
										try{
											$vosTmp=$daoTmp->getRegistroCondiciones($llaves);
											if(count($vosTmp)>0){
												ControlXML::agregarNodoTexto($fila,"Wiki",$vosTmp->$funcionGetCTexClaveForanea());
											}else{
												ControlXML::agregarNodoTexto($fila,"Wiki",$registro->$funcionGet());
											}
										}catch(sinResultados $e){
											ControlXML::agregarNodoTexto($fila,"Wiki","Borrado ".$registro->$funcionGet());
										}
									}else{
										ControlXML::agregarNodoTexto($fila,"Wiki",$registro->$funcionGet());
									}
								}
							}
						}
						//msg::add($tabla);
					}else{
						mensaje::add("(1)No hay registros.");
					}
					// Se genera el pie de pagina de la tabla de datos
//					$pie=ControlXML::agregarNodo($tabla,"Pie");
					// Se genera la paginación
					$ControlPaginacion->generarNavegador($xml);
				}catch(Exception $e){
					mensaje::add("(1) Error, {$e->getMessage()}",ERROR);
					mensaje::add($e,ERROR);
				}
			}
		}
	}
?>
