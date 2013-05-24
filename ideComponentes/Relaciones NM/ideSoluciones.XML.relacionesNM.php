<?php

	class RelacionesMN extends generalXML{
	
		var $formularioRelacionesMN;
		var $sesion;
		var $clases;
		
		function getControl($nombre){
			//throw new Exception("Elefante");
			$nombreNuevo="Control".$this->getNombre($nombre);
			//asercion("El nombre del paquete es: ".$nombre);
			return new $nombreNuevo($this->sesion->db);
		}
		function getNombre($nombre){
			/*$prefijo=substr($nombre, 0, 11);
			if (strcmp($prefijo, "sistemaVET_")==0 || strcmp($prefijo, "1_")==0){
				$nombreNuevo=substr($nombre, 11);
				return $nombreNuevo;
			}
			*/
			return $nombre;
		}
		function getLista($tabla, $campo, $filtros=null){
			//echo "Generando lista con la tabla ".$tabla."[".$campo."]<br>";
			$nombreDao="DAO".$tabla;
			$daoTabla=new $nombreDao($this->sesion->getDB());
			if (is_null($filtros)){
				$filtros= new SimpleXMLElement("<Consulta/>");
			}
			$campoFiltro=$filtros->addChild("Campo");
			$campoFiltro->addAttribute("nombre", $campo);
			$campoFiltro->addAttribute("tablaOrigen", $tabla);
			
			//echo revisarArreglo($campo, "campo");
			//echo $this->geshiXML($filtros);
			try{
				$lista=$daoTabla->getRegistros($filtros);
			}catch(sinResultados $e){
				//echo "Excepcion ".$this->sesion->db->sql."<br>";
				$lista=array();
			}
			//echo "Consulta: ".$this->geshi($this->sesion->db->sql, "sql")."<br>";
			return $lista;
		}
		
		function RelacionesMN($sesion, $clases, $xmlEspecificacion){
			$this->sesion=$sesion;
			$this->clases=$clases;
			$this->xmlEspecificacion=$xmlEspecificacion;
			
			
			if (strcmp($xmlEspecificacion->getName(), "XMLRelacionesMN")!=0){
				assert("Se tiene que enviar un xml <strong>XMLRelacionesMN</strong>");
			}
			$this->formularioRelacionesMN= new SimpleXmlElement("<Contenido/>");
			$contenido= $this->formularioRelacionesMN->addChild("FormularioRelacionesMN");
			
			$propiedad= $contenido->addChild("Propiedad");
			$propiedad->addAttribute("nombre", "idCasoUso");
			$propiedad->addAttribute("valor", $sesion->leerParametro("idCasoUso"));


			$clasesEspecificacion=$xmlEspecificacion->xpath("/XMLRelacionesMN/Relacion");

			foreach ($clasesEspecificacion as $i => $a){
				$clase=$contenido->addChild("Clase");
				$clase->addAttribute("titulo", $a["titulo"]);
				$clase->addAttribute("id", $a["id"]);
				$otraClase=$clases[0+$a["id"]];
				
				$filtrosCompletos=$a->children();
				$filtros=$filtrosCompletos[0];
				//echo revisarArreglo($filtros, "Los filtros son: ");
				
				$propiedades=$otraClase->xpath("/Clase/Propiedades");
				
				$nombreTabla=$propiedades[0]["nombre"];
				/*$control=$this->getControl($nombreTabla);
				$realNombre=$this->getNombre($nombreTabla);
				*/
				$lista=$this->getLista($nombreTabla, $a["campo"], $filtros);
				//echo revisarArreglo($lista, "Lista ".$nombreTabla);

				$llaves=$otraClase->xpath('/Clase/Propiedades/Propiedad[@tipo="llavePrimaria" or @tipo="llavePrimariaAutonumerica" or @llavePrimaria="true"]');
				if (count($llaves)==false){
					asercion("Relaciones MN - Se requiere una llave primaria");
				}
				//new mensajes (revisarArreglo($llaves, "llaves"));
				$campo="".$a["campo"];
				//echo revisarArreglo($campo);
				$campo[0] = strtoupper($campo[0]);

				foreach($lista as $j => $b){
					$nombreFuncionGetCampo="get".$campo;
					$titulo=$b->$nombreFuncionGetCampo();
					$elemento=$clase->addChild("Elemento");
					$elemento->addAttribute("titulo", $titulo);
					$idElementos=array();
					foreach($llaves as $ill => $all){
						$nombreElemento="".$all["nombre"];
						$nombreElemento[0] = strtoupper($nombreElemento[0]);
						$nombreFuncionGetCampo="get".$nombreElemento;
						$idElementos[]=$b->$nombreFuncionGetCampo();
					}
					//new mensajes(revisarArreglo($idElementos, "idElementos"));
					$idElemento=implode(",", $idElementos);
					$elemento->addAttribute("idElemento", $idElemento);
				}
			}
			//new mensajes($this->geshiXML($this->formularioRelacionesMN));
		}
		
		function generarRelaciones(){	
			$contenidos=$this->formularioRelacionesMN->xpath("/Contenido/FormularioRelacionesMN");
			$contenido=$contenidos[0];

			$clasesEspecificacion=$this->xmlEspecificacion->xpath("/XMLRelacionesMN/Relacion");
			$ida=$clasesEspecificacion[0]["id"];
			$idb=$clasesEspecificacion[1]["id"];
			
			//echo "ida=$ida - 	";
			//echo "idb=$idb  - ";
			
			$propiedad=$this->clases["$ida"]->xpath("/Clase/Propiedades/Propiedad");
			$lista1=$propiedad[0]['nombre'];
			$propiedad=$this->clases["$idb"]->xpath("/Clase/Propiedades/Propiedad");
			$lista2=$propiedad[0]['nombre'];
			
			//echo "Los datos a relacionar son: ".$lista1." - ".$lista2;
			
				
			$propiedades=$this->clases['R']->xpath("/Clase/Propiedades");
			//$propiedad=$this->clases['R']->xpath("/Clase/Propiedades/Propiedad");
			
			//echo "Se quiere tener las relaciones de: [".$propiedades[0]["nombre"]."]<br>";
			$tabla="".$propiedades[0]["nombre"];
			//$controlRelacion=$this->getControl($propiedades[0]["nombre"]);
			$nombreDao="DAO".$tabla;
			$daoTabla=new $nombreDao($this->sesion->getDB());
			
			$filtros= new SimpleXMLElement("<Consulta/>");
			
			$campoFiltro=$filtros->addChild("Campo");
			$campoFiltro->addAttribute("nombre", "*");
			$campoFiltro->addAttribute("tablaOrigen", $tabla);
			
			//msg::add("Consulta");
			//msg::add($clasesEspecificacion[0]->Consulta);
			
			
			//echo revisarArreglo($campo, "campo");
			//echo $this->geshiXML($filtros);
			$relaciones=$contenido->addChild("Relaciones");
			try{
				$stringXML=$clasesEspecificacion[0]->Consulta->asXML();
				//(string)$clasesEspecificacion[0]->Consulta
				if (strlen($stringXML)>0){
					$listaRelacion=$daoTabla->getRegistros($clasesEspecificacion[0]->Consulta);
				}else{
					$listaRelacion=$daoTabla->getRegistros($filtros);
				}
				//msg::add($listaRelacion);
				foreach($listaRelacion as $i => $a){
					$relacion=$relaciones->addChild("Relacion");
				/*
						$nombreElemento="".$all["nombre"];
						$nombreElemento[0] = strtoupper($nombreElemento[0]);
						$nombreFuncionGetCampo="get".$nombreElemento;
						$idElementos[]=$b->$nombreFuncionGetCampo();
						*/
					$x="".$lista1;
					$y="".$lista2;
					$x[0]=strtoupper($x[0]);
					$y[0]=strtoupper($y[0]);
					$nombreFuncionGetX="get".$x;
					$nombreFuncionGetY="get".$y;
					$relacion->addAttribute("idElemento1", $a->$nombreFuncionGetX());
					$relacion->addAttribute("idElemento2", $a->$nombreFuncionGetY());
				}
			}catch(sinResultados $e){
				//echo "Excepcion ".$this->sesion->db->sql."<br>";
				$lista=array();
			}
			//msg::add("Consulta: ".$this->geshi($this->sesion->db->sql, "sql"));
			//return $lista;
			
			
/*
			$listaRelacion=$controlRelacion->getRelaciones($this->sesion);
			//echo revisarArreglo($listaRelacion, "listaRelacion ".$propiedades[0]["nombre"]);
			//new mensajes(revisarArreglo($this->clases['R'], "Clase R="));
			//$lista1="".$propiedad[0]['nombre']."";
			//$lista2="".$propiedad[1]['nombre']."";
			foreach($listaRelacion as $i => $a){
				//new mensajes(revisarArreglo($a, "a[$i]"));
				$relacion=$relaciones->addChild("Relacion");
				$x=$a["$lista1"];
				$y=$a["$lista2"];
				//new mensajes (var_dump($a[0+$lista1]).", ".var_dump($a[$lista2]));
				$relacion->addAttribute("idElemento1", $x);
				$relacion->addAttribute("idElemento2", $y);
			}*/

			$propiedad= $contenido->addChild("Campo");
			$propiedad->addAttribute("tipo", "oculto");
			$propiedad->addAttribute("nombre", "nombreClaseControlRelacion");
			$propiedad->addAttribute("valorPorDefecto", $this->getNombre($tabla));
			$propiedad= $contenido->addChild("Campo");
			$propiedad->addAttribute("tipo", "oculto");
			$propiedad->addAttribute("nombre", "nombreTabla1ControlRelacion");
			$propiedad->addAttribute("valorPorDefecto", $lista1);
			$propiedad= $contenido->addChild("Campo");
			$propiedad->addAttribute("tipo", "oculto");
			$propiedad->addAttribute("nombre", "nombreTabla2ControlRelacion");
			$propiedad->addAttribute("valorPorDefecto", $lista2);
			//new mensajes($this->geshiXML($this->formularioRelacionesMN));

		}
		function generarContenido(){
			$this->generarRelaciones();
			return $this->formularioRelacionesMN;
		}
		function procesarFormulario(){
			$relaciones=$this->sesion->leerParametrosFormularioActual();
			//echo "Relaciones ";
			//var_dump($relaciones);
			//return "";
			$reemplazar=false;
			foreach($relaciones as $i => $a){
				$datos=explode("_", $a["nombre"]);
				if (strcmp($datos[0], "relacion")==0){
					$reemplazar=true;
					break;
				}
			}
			
			
		
			if ($reemplazar){
				$tabla="".$this->sesion->leerParametroFormularioActual("nombreClaseControlRelacion");
				$lista1="".$this->sesion->leerParametroFormularioActual("nombreTabla1ControlRelacion");
				$lista2="".$this->sesion->leerParametroFormularioActual("nombreTabla2ControlRelacion");
				$tabla[0]=strtoupper($tabla[0]);
				$lista1[0]=strtoupper($lista1[0]);
				$lista2[0]=strtoupper($lista2[0]);
				$set1="set".$lista1;
				$set2="set".$lista2;
				
				$nombreDao="DAO".$tabla;
				$daoTabla=new $nombreDao($this->sesion->db);

				$clasesEspecificacion=$this->xmlEspecificacion->xpath("/XMLRelacionesMN/Relacion");
				//msg::add($clasesEspecificacion[0]);

				try{
					//$vos=$daoTabla->getRegistros();
					$stringXML=$clasesEspecificacion[0]->Consulta->asXML();
					//(string)$clasesEspecificacion[0]->Consulta
					if (strlen($stringXML)>0){
						$listaRelacion=$daoTabla->getRegistros($clasesEspecificacion[0]->Consulta);
					}else{
						$vos=$daoTabla->getRegistros();
					}
					//$vos=$daoTabla->getRegistros($clasesEspecificacion[0]->Consulta);
					if ($vos){
						foreach($vos as $vo){				
							if (!$daoTabla->eliminarRegistro($vo))
								echo "ERROR Eliminando<br>";
							//echo revisarArreglo($vo, "VO Eliminado con ".$nombreDao." ".$this->geshi($this->sesion->db->sql, "sql"));
						}
					}
				}catch(sinResultados $e){
					//echo "Sin resultados";
				}
				//$controlRelacion->limpiarRelaciones();
				//echo revisarArreglo($relaciones, "relaciones");
				foreach($relaciones as $i => $a){
					//echo "Agregando con ".$nombreDao." ".$a["nombre"]."<br>";
					$datos=explode("_", $a["nombre"]);
					if (strcmp($datos[0], "relacion")==0){
						$vo= $daoTabla->crearVO();
						$vo->$set1($datos[1]);
						$vo->$set2($datos[2]);
						$daoTabla->agregarRegistro($vo);
						//new mensajes("agregando ".$datos[1].", ".$datos[2]);
					}
				}
			}
			$this->generarRelaciones();
			return $this->formularioRelacionesMN;
		}
	}

?>
