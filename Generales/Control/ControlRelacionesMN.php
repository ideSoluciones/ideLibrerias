<?php
	class ControlRelacionesMN{
		public $entidad1;
		public $entidad2;
		public $entidadUnion;
		public $condiciones1;
		public $condiciones2;
		public $campoUnion1;
		public $campoUnion2;
		public $campo1;
		public $campo2;
		
		function ControlRelacionesMN($datos=array()){
			if (isset($datos["entidad1"])){
				$this->entidad1=$datos["entidad1"];
			}else{
				throw new ParametroIncompletoException("Falta la entidad 1");
			}
			if (isset($datos["entidad2"])){
				$this->entidad2=$datos["entidad2"];
			}else{
				throw new ParametroIncompletoException("Falta la entidad 2");
			}
			if (isset($datos["entidadUnion"])){
				$this->entidadUnion=$datos["entidadUnion"];
			}else{
				throw new ParametroIncompletoException("Falta la entidad Union");
			}
			if (isset($datos["condiciones1"])){
				$this->condiciones1=$datos["condiciones1"];
			}
			if (isset($datos["condiciones2"])){
				$this->condiciones2=$datos["condiciones2"];
			}
			if (isset($datos["campoUnion1"])){
				$this->campoUnion1=$datos["campoUnion1"];
			}else{
				throw new ParametroIncompletoException("Falta el campo union 1");
			}
			if (isset($datos["campoUnion2"])){
				$this->campoUnion2=$datos["campoUnion2"];
			}else{
				throw new ParametroIncompletoException("Falta el campo union 2");
			}
			if (isset($datos["campo1"])){
				$this->campo1=$datos["campo1"];
			}else{
				throw new ParametroIncompletoException("Falta el campo 1");
			}
			if (isset($datos["campo2"])){
				$this->campo2=$datos["campo2"];
			}else{
				throw new ParametroIncompletoException("Falta el campo 2");
			}
			if (isset($datos["campoTexto1"])){
				$this->campoTexto1=$datos["campoTexto1"];
			}else{
				throw new ParametroIncompletoException("Falta el campoTexto 1");
			}
			if (isset($datos["campoTexto2"])){
				$this->campoTexto2=$datos["campoTexto2"];
			}else{
				throw new ParametroIncompletoException("Falta el campoTexto 2");
			}
			if (isset($datos["titulo1"])){
				$this->titulo1=$datos["titulo1"];
			}
			if (isset($datos["titulo2"])){
				$this->titulo2=$datos["titulo2"];
			}
			
		}

		function generarInterfaz($xml){
			$sesion=Sesion::getInstancia();
			$nombreDaoEntidad1="DAO".$this->entidad1;
			$nombreDaoEntidad2="DAO".$this->entidad2;
			$nombreDaoEntidadUnion="DAO".$this->entidadUnion;
			
			$getNombreLlave1="get".ucfirst($this->campo1);
			$getNombreLlave2="get".ucfirst($this->campo2);
			$getNombreTexto1="get".ucfirst($this->campoTexto1);
			$getNombreTexto2="get".ucfirst($this->campoTexto2);

			$setNombreLlave1="set".ucfirst($this->campo1);
			$setNombreLlave2="set".ucfirst($this->campo2);
			$setNombreTexto1="set".ucfirst($this->campoTexto1);
			$setNombreTexto2="set".ucfirst($this->campoTexto2);

			
			
			if (!class_exists($nombreDaoEntidad1)){
				xml::add($xml, "Wiki", "Error la clase ".$this->entidad1." no existe", ERROR);
				return;
			}
			if (!class_exists($nombreDaoEntidad2)){
				xml::add($xml, "Wiki", "Error la clase ".$this->entidad2." no existe", ERROR);
				return;
			}
			if (!class_exists($nombreDaoEntidadUnion)){
				xml::add($xml, "Wiki", "Error la clase ".$this->entidadUnion." no existe", ERROR);
				return;
			}
			$daoEnt1 = new $nombreDaoEntidad1($sesion->getDB());
			$daoEnt2 = new $nombreDaoEntidad2($sesion->getDB());
			$daoEntUnion = new $nombreDaoEntidadUnion($sesion->getDB());
			
			$listaEnt1=array();
			$listaEnt2=array();
			
			
			try{
				$vosCompletosEnt1=$daoEnt1->getRegistros($this->condiciones1);
			}catch(sinResultados $e){
				xml::add($xml, "Wiki", "No existen registros en ".$this->entidad1);
				return;
			}
			try{
				$vosCompletosEnt2=$daoEnt2->getRegistros($this->condiciones2);
				//msg::add($this->condiciones2);
				//msg::add($sesion->db->sql);
			}catch(sinResultados $e){
				xml::add($xml, "Wiki", "No existen registros en ".$this->entidad2);
				return;
			}
			$controlPaginacion= new ControlPaginacion($sesion, "adasd", "", array("registrosPorPagina"=>10));
			$controlPaginacion->procesarPeticionNavegador();
			
			/*
			if (is_null($this->condicionesUnion)){
				$this->condicionesUnion= xml::add(null, "Consulta");
			}
			*/
			
			
			try{
				$vosEntidad1=$controlPaginacion->obtenerRegistros($daoEnt1, $this->condiciones1);
				$vosEntUnion=array();
				foreach($vosEntidad1 as $voEntidad1){
					//msg::add("Valor ".$getNombreLlave1." ".$voEntidad1->$getNombreLlave1());
					$vosEntidadUnion=$daoEntUnion->getRegistrosCondiciones(array($this->campo1=>$voEntidad1->$getNombreLlave1()));
					if (count($vosEntidadUnion)==0){
						$voEntidadUnion = $daoEntUnion->crearVO();
						$voEntidadUnion->$setNombreLlave1($voEntidad1->$getNombreLlave1());
						$vosEntidadUnion=array($voEntidadUnion);
					}
					$vosEntUnion=array_merge($vosEntUnion, $vosEntidadUnion);
				}
				//$vosEntUnion=$daoEntUnion->getRegistros($this->condicionesUnion);
			}catch(sinResultados $e){
				xml::add($xml, "Wiki", "No existen registros en ".$this->entidadUnion);
				return;
			}

			
			$elementos=array();
			
			$lista=xml::add($xml, "Wiki", "===Agregar===\n");
			
			//msg::add($vosEntUnion);
			foreach($vosEntUnion as $voEntUnion){
				if (!isset($elementos[$voEntUnion->$getNombreLlave1()])){
					$elementos[$voEntUnion->$getNombreLlave1()]=array();
				}
				if (!isset($listaEnt1[$voEntUnion->$getNombreLlave1()])){
					//echo $voEntUnion->$getNombreLlave1()."<br>";
					$vosEnt1=$daoEnt1->getRegistro($voEntUnion->$getNombreLlave1());
					$listaEnt1[$vosEnt1->$getNombreTexto1()]=$vosEnt1;
				}else{
					$vosEnt1=$listaEnt1[$voEntUnion->$getNombreLlave1()];
				}
				if (!is_null($voEntUnion->$getNombreLlave2())){
					if (!isset($listaEnt2[$voEntUnion->$getNombreLlave2()])){
						$vosEnt2=$daoEnt2->getRegistro($voEntUnion->$getNombreLlave2());
						$listaEnt2[$vosEnt2->$getNombreTexto2()]=$vosEnt2;
					}else{
						$vosEnt2=$listaEnt2[$voEntUnion->$getNombreLlave2()];
					}
				}else{
					$vosEnt2=null;
				}
				$elementos[$voEntUnion->$getNombreLlave1()][$voEntUnion->$getNombreLlave2()]=array(0=>$vosEnt1, 1=>$vosEnt2);
			}
			
			//msg::add($listaEnt1);
			//msg::add($listaEnt2);
			
			
			$form=ControlFormulario::agregarFormulario($xml);
			$datos1=array();
			foreach($vosCompletosEnt1 as $voEnt1){
				$datos1[$voEnt1->$getNombreTexto1()]=array("nombre"=>$voEnt1->$getNombreTexto1(), "valor"=>$voEnt1->$getNombreLlave1());
			}
			ksort($datos1);
			ControlFormulario::agregarListaSeleccion($form, "elemento1", $datos1, ucfirst($this->titulo1),$sesion->leerParametroDestinoActual("elemento1"));

			$datos2=array();
			foreach($vosCompletosEnt2 as $voEnt2){
				$datos2[$voEnt2->$getNombreTexto2()]=array("nombre"=>$voEnt2->$getNombreTexto2(), "valor"=>$voEnt2->$getNombreLlave2());
			}
			ksort($datos2);
			ControlFormulario::agregarListaSeleccion($form, "elemento2", $datos2, ucfirst($this->titulo2),$sesion->leerParametroDestinoActual("elemento2"));
			
			ControlFormulario::agregarEnviar($form, array("titulo"=>"Agregar"));
			
			xml::add($xml, "Wiki", "===Actuales===");
			$form=ControlFormulario::agregarFormulario($xml);
			//msg::add($elementos);
			foreach($elementos as $idsEnt1){
				$primero=true;
				foreach($idsEnt1 as $datos){
					if ($primero){
						$contenedor=xml::add($form, "Contenedor", array("titulo"=>$datos[0]->$getNombreTexto1()));
						$primero=false;
					}
					if(!is_null($datos[1])){
						xml::add($contenedor, "Campo", array(
							"tipo"=>"booleano",
							"nombre"=>"selectorBorrar_".$datos[0]->$getNombreLlave1()."_".$datos[1]->$getNombreLlave2(),
							"titulo"=>$datos[1]->$getNombreTexto2()
							)
						);
					}else{
						xml::add($contenedor, "Wiki", "Sin relación");
					}
					//$lista[].="* ".$datos[0]->$getNombreTexto1()." - ".$datos[1]->$getNombreTexto2()."\n";
					//msg::add($xml);
				}
			}
			$contenedor=xml::add($form, "Contenedor", array("estilo"=>"overflow:hidden"));
			$controlPaginacion->generarNavegador($contenedor);
			
			xml::add($form, "Wiki", "===Eliminar===");
			ControlFormulario::agregarEnviar($form, array("titulo"=>"Borrar seleccionados"));
			//msg::add($xml);
		}
		function procesarDatos(){
			$sesion=Sesion::getInstancia();
			$nombreDaoEntidad1="DAO".$this->entidad1;
			$nombreDaoEntidad2="DAO".$this->entidad2;
			$nombreDaoEntidadUnion="DAO".$this->entidadUnion;
			$daoEntidadUnion= new $nombreDaoEntidadUnion($sesion->getDB());

			$elementosABorrar=$sesion->leerParametrosDestinoActual("/selectorBorrar_/");
			foreach($elementosABorrar as $nombre => $elementoBorrar){
				$partesNombre=explode("_", $nombre);
				if (count($partesNombre)==3){
					try{
						//msg::add($partesNombre[1]." y ".$partesNombre[2]);
						$voEntidadUnion=$daoEntidadUnion->getRegistro($partesNombre[1], $partesNombre[2]);
						//msg::add($voEntidadUnion);
						if ($daoEntidadUnion->eliminarRegistro($voEntidadUnion)){
							msg::add("Registro eliminado");
						}else{
							msg::add("Error eliminado registro", ERROR);
						}
					}catch(sinResultados $e){
						msg::add("No se encontro el registro", ERROR);
					}
				}
			}
			
			$elemento1=$sesion->leerParametroDestinoActual("elemento1");
			$elemento2=$sesion->leerParametroDestinoActual("elemento2");
			
			if (strcmp($elemento1, "")!=0 && strcmp($elemento2, "")!=0){
				$setNombreLlave1="set".ucfirst($this->campo1);
				$setNombreLlave2="set".ucfirst($this->campo2);
			
				$voEntidadUnion=$daoEntidadUnion->crearVO();
				$voEntidadUnion->$setNombreLlave1($elemento1);
				$voEntidadUnion->$setNombreLlave2($elemento2);
				try{
					if ($daoEntidadUnion->agregarRegistro($voEntidadUnion)){
						msg::add("Registro creado correctamente");
					}
				}catch(XMLSQLExcepcionRegistroDuplicado $e){
					msg::add("El registro ya existia");
				}
			}
			
		}
		
	}
	
	
	
	
	
	
	
	
?>
