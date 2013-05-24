<?php
	class ControlGeneralListas extends generalXML{
	
		var $listaClases;
		var $listaRaiz;
		var $pathPrincipal;
		var $tituloPagina;
		var $sesion;
		var $contenido;
		var $listaActual;
		var $listas;

		function ControlGeneralListas($sesion){
			$this->sesion=$sesion;
			$this->contenido= new SimpleXMLElement("<Contenido />");
			$this->listaActual=$this->sesion->leerParametro("{$this->pathPrincipal}_navegador_listaActual");
			$this->recargarIds($this->listaClases, true);
		}
		
		function recargarIds($lista,$crear=false){
			foreach($lista as $clase=>$propiedades){
				$id=$propiedades["id"];
				$nombreClase="CLista".ucfirst($clase);
				if ($crear){
					$this->listas[$clase]=new $nombreClase($this->sesion, $id, $this->pathPrincipal);
				}else{
					$this->listas[$clase]->setIdListaAnterior($id);
					$this->listas[$clase]->setPathPrincipal($this->pathPrincipal);
				}
				if(isset($propiedades["hijos"])){
					$this->recargarIds($propiedades["hijos"],$crear);
				}
			}
		}
		
		function obtenerInterfazPrincipal(){
			$this->procesarAnclasYFormularios();
			$this->generarInterfazPrincipal();
			return $this->contenido;
		}
		
		
		
		function procesarAnclasYFormularios(){
		
			/* Conteo de elementos de las listas */
			foreach($this->listas as $lista){
				$lista->contarListadoCompleto();
			}

			$operacion=$this->sesion->leerParametroFormularioActual("operacion");
			if($operacion!=""){//Si es petición de formulario
				if(isset($this->listas[$this->listaActual])){
					$this->listas[$this->listaActual]->procesarOperacionDeFormulario($operacion);
					$this->listas[$this->listaActual]->cargarListado();
				}
			}else{//Si es petición por url
				if(strcmp($this->listaActual,"")==0){
					$this->listaActual=$this->listaRaiz;
				}
				/* Buscando en la url parametros de lista actual */
				for($i=0;$i<count($this->sesion->args);$i++){
					if(array_key_exists($this->sesion->args["$i"],$this->listas)){
						$this->listaActual=$this->sesion->args["$i"];
					}
				}
				
				/* Se envia a procesar argumentos de lista actual */
				if(isset($this->listas[$this->listaActual])){
					$this->listas[$this->listaActual]->procesarArgumentos();
					$this->listas[$this->listaActual]->cargarListado();
					//$this->listas[$this->listaActual]->cargarListadoInterno();
				}
				
				/* Limpiar Ids */
				$this->borrarIdsListas($this->listaClases);
			}
			
			foreach($this->listas as $lista){
				if(strcmp($lista->obtenerNombreClase(),$this->listaActual)!=0){
					$lista->cargarListado();
				}
				$lista->almacenarCambiosEnSesion();
				$this->recargarIds($this->listaClases);
			}

			$this->sesion->escribirParametro("{$this->pathPrincipal}_navegador_listaActual",$this->listaActual);
		}
		
		function borrarIdsListas(&$lista,$borrar=false){
			/* Limpiando "ListaAnterior" y "idActual" de listas que esten despues de lista actual */
			foreach($lista as $clase=>$propiedades){
				if($borrar){
					$lista[$clase]["id"]=0;
					$this->listas[$clase]->setIdListaAnterior(0);
					$this->listas[$clase]->borrarIdActual();
				}
				if (strcmp($this->listaActual,$clase)==0){
					if(isset($propiedades["hijos"])){
						$this->borrarIdsListas($propiedades["hijos"],true);
						foreach($propiedades["hijos"] as $claseHijo => $propiedadesHijo){
							$lista[$clase]["hijos"]["$claseHijo"]["id"]=$this->listas[$this->listaActual]->obtenerIdActual();
							$this->listas[$claseHijo]->setIdListaAnterior($this->listas[$this->listaActual]->obtenerIdActual());
						}
					}
				}else{
					if(isset($propiedades["hijos"])){
						$this->borrarIdsListas($lista[$clase]["hijos"],$borrar);
					}
				}
			}
		}
		
		function generarInterfazPrincipal(){
			$texto=$this->contenido->addChild("Texto");
			
			$campo=$texto->addChild("Campo");
			$campo->addAttribute("nombre","titulo");
			$campo->addAttribute("nivel","1");
			$campo->addAttribute("valor",$this->tituloPagina);
			
			if (isset($this->listas[$this->listaActual])){
				$this->simplexml_merge($this->contenido,$this->listas[$this->listaActual]->mensajes);

				$navegador=$this->contenido->addChild("Navegador");
				
				$listas=$navegador->addChild("Listas");
				
				$this->renderizarLista($this->listaClases,$listas);
				
				if (!is_null($this->listas[$this->listaActual]->formulario)){
						$this->simplexml_merge($this->contenido,$this->listas[$this->listaActual]->formulario);
				}
			}
			//echo $this->geshiXML($navegador),"$navegador";
		}
		
		function renderizarLista($listas,$navegador, $acordeon=null){
			foreach($listas as $clase=>$propiedades){
				if (is_null($acordeon))
					$acordeon=$navegador->addChild("Tabs");
				$nodo=$acordeon->addChild("Nodo");
				$nodo->addAttribute("titulo", $clase);
				$navega=$nodo->addChild("Navegador");
				$this->listas["$clase"]->renderizarLista($navega);
				if(isset($propiedades["hijos"])){
					$this->renderizarLista($propiedades["hijos"],$navega, $acordeon);
				}
			}
		}
	}
?>
