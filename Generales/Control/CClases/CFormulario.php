<?php

	class CFormulario{
	
		private $CClase=null;
		private $campos=array();
		private $valoresPorDefecto=null;
		private $tipo=array();
		private $accesoLectura=true;
		private $accesoEscritura=true;
		private $serEstrictoConValoresPorDefecto=true;
		
		function CFormulario($CClase=null){
			$this->setCClase($CClase);
			$this->tipo["d"]=false;//Desactivar
			$this->tipo["n"]=false;//Nuevo
			$this->tipo["m"]=false;//Modificar
			$this->tipo["e"]=false;//Eliminar
			$this->tipo["c"]=false;//Consultar
		}

		function serEstrictoConValoresPorDefecto($valor){
			if(is_bool($valor)){
				$this->serEstrictoConValoresPorDefecto=$valor;
			}
		}
		
		function setValoresPorDefecto($valoresPorDefecto){
			if(is_object($valoresPorDefecto)){
				$this->valoresPorDefecto=$valoresPorDefecto;
				if(!is_null($this->CClase)){
					foreach($this->campos as $campo){
						if(!$campo->esClave()){
							$funcionGet=$campo->get("funcionGet");
							if(!is_null($this->valoresPorDefecto)&&method_exists($this->valoresPorDefecto,$funcionGet)){
								if($this->serEstrictoConValoresPorDefecto){
									$campo->setValor($this->valoresPorDefecto->$funcionGet());
								}else{
									try{
										$campo->setValor($this->valoresPorDefecto->$funcionGet());
									}catch(Exception $e){}
								}
							}
						}
					}
				}
			}
		}
		
		function setCClase($CClase){
			if(!is_null($CClase)){
				if(is_object($CClase)){
					if(strcmp(get_class($CClase),"CClase")==0){
						$this->CClase=$CClase;
						$this->campos=$this->CClase->getCCampos();
					}else{
						throw new objetoInvalido(t("CFormulario, se esperaba un objeto de tipo CCLase."));
					}
				}else{
					throw new objetoInvalido(t("CFormulario, se esperaba un objeto de tipo CCLase."));
				}
			}else{
				$this->CClase=null;
			}
		}

		function setTipo($tipo){
			switch($tipo){
				case "nuevo":
					$this->tipo["d"]=false;
					$this->tipo["n"]=true;
					$this->tipo["m"]=false;
					$this->tipo["b"]=false;
					$this->tipo["c"]=false;
					break;
				case "modificar":
					$this->tipo["d"]=false;
					$this->tipo["n"]=false;
					$this->tipo["m"]=true;
					$this->tipo["b"]=false;
					$this->tipo["c"]=false;
					break;
				case "borrar":
					$this->tipo["d"]=false;
					$this->tipo["n"]=false;
					$this->tipo["m"]=false;
					$this->tipo["b"]=true;
					$this->tipo["c"]=false;
					break;
				case "desactivar":
					$this->tipo["d"]=true;
					$this->tipo["n"]=false;
					$this->tipo["m"]=false;
					$this->tipo["b"]=false;
					$this->tipo["c"]=false;
					break;
				case "consultar":
					$this->tipo["d"]=false;
					$this->tipo["n"]=false;
					$this->tipo["m"]=false;
					$this->tipo["b"]=false;
					$this->tipo["c"]=true;
					break;
				default:
					throw new objetoInvalido(t("CFormulario, el formulario de tipo [$tipo] no está soportado."));
			}
		}
		function es($tipo){
			if(is_string($tipo)){
				if(isset($this->tipo["{$tipo[0]}"])){
					return $this->tipo["{$tipo[0]}"];
				}
			}
			return false;
		}
		function setPermisoLectura($valor){
			if(is_bool($valor)){
				$this->accesoLectura=$valor;
			}
		}
		function setPermisoEscritura($valor){
			if(is_bool($valor)){
				$this->accesoEscritura=$valor;
			}
		}
		function lectura(){
			return $this->accesoLectura;
		}
		function escritura(){
			return $this->accesoEscritura;
		}
		function cargar($llaves){
			if(!is_null($this->CClase)){
				$sesion=Sesion::getInstancia();
				$nombreDAO="DAO".$this->CClase->getPropiedad("nombre");
				$nombreVO="VO".$this->CClase->getPropiedad("nombre");
				$DAO=new $nombreDAO($sesion->getDb());
				if(method_exists($nombreDAO,"getRegistroCondiciones")){
					$dao=new $nombreDAO($sesion->getDB());
					$reg=$dao->getRegistroCondiciones($llaves);
					foreach($this->campos as $campo){
						if(!$campo->esClave()){
							$funcionGet=$campo->get("funcionGet");
							if(!is_null($this->valoresPorDefecto)&&method_exists($this->valoresPorDefecto,$funcionGet)){
								$campo->setValor($this->valoresPorDefecto->$funcionGet());
							}else{
								if(method_exists($nombreVO,$funcionGet)){
									$campo->setValor($reg->$funcionGet());
								}else{
									throw new metodoNoExiste(t("CFormulario, el método {$nombreVO}::{$funcionGet}() no existe."));
								}
							}
						}
					}
				}else{
					throw new metodoNoExiste(t("CFormulario, el método {$nombreDAO}::getRegistroCondiciones() no existe."));
				}
			}
		}
		function getCampos(){
			return $this->campos;
		}
	}


?>
