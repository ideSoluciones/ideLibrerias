<?php
class ControlPaginacion{

	private $sesion;
	private $paginaActual=1;
	private $totalPaginas=1;
	private $xml;
	private $casoUso;
	private $id;
	private $idReferenciaSalto="";
	private $basePotenciaSaltosPaginas;
	private $totalRegistros;
	private $tipoSelectorPaginador;
	var $registroDesde=0;
	var $registrosPorPagina=2;
	
	function ControlPaginacion($sesion,$id="",$idReferenciaSalto="",$propiedades=""){
		$this->sesion=Sesion::getInstancia();
		$this->id=$id;
		if(isset($propiedades["base"])){
			if(intval($propiedades["base"])>1){
				$this->basePotenciaSaltosPaginas=intval($propiedades["base"]);
			}else{
				$this->basePotenciaSaltosPaginas=2;
			}
		}else{
			$this->basePotenciaSaltosPaginas=intval($this->sesion->leerParametro("{$this->id}{$this->casoUso}_basePotenciaSaltosPaginas"));
			if($this->basePotenciaSaltosPaginas<=1){
				$this->basePotenciaSaltosPaginas=2;
			}
		}
		$this->casoUso=$this->sesion->leerParametro("nombreCasoUso");
		$this->idReferenciaSalto=$idReferenciaSalto;
		$this->paginaActual=intval($this->sesion->leerParametro("{$this->id}{$this->casoUso}_paginaActual"));
		if($this->paginaActual<1){
			$this->paginaActual=1;
		}
		if(isset($propiedades["registrosPorPagina"])){
			if(intval($propiedades["registrosPorPagina"])>0){
				$this->registrosPorPagina=intval($propiedades["registrosPorPagina"]);
			}else{
				$this->registrosPorPagina=10;
			}
		}else{
			$this->registrosPorPagina=intval($this->sesion->leerParametro("{$this->id}{$this->casoUso}_registrosPorPagina"));
			if($this->registrosPorPagina<=0){
				$this->registrosPorPagina=10;
			}
		}
		$this->setTotalRegistros(-1);
		if(isset($propiedades["tipoSelector"])){
			$this->tipoSelectorPaginador=$propiedades["tipoSelector"];
		}else{
			$this->tipoSelectorPaginador="selector";
		}
	}
	
	function setTotalRegistros($totalRegistros){
		if(intval($totalRegistros)<0){
			$this->totalRegistros=intval($this->sesion->leerParametro("{$this->id}{$this->casoUso}_totalRegistros"));
		}else{
			$this->totalRegistros=$totalRegistros;
			$this->sesion->escribirParametro("{$this->id}{$this->casoUso}_totalRegistros",$this->totalRegistros);
		}
		$this->totalPaginas=$this->totalPaginas=ceil(intval($this->totalRegistros)/$this->registrosPorPagina);
		if($this->paginaActual>$this->totalPaginas){
			$this->paginaActual=1;
		}
		$this->registroDesde=($this->paginaActual-1)*$this->registrosPorPagina;
	}
	
	function obtenerRegistros($dao,$condiciones=null){
		if(is_null($condiciones)){
			$consulta=new SimpleXMLElement("<Consulta />");
			$condiciones=ControlXML::agregarNodo($consulta,"Condiciones");
		}else{
			$consulta=$condiciones;
		}
		try{
			$numeroRegistros=$dao->getTotalRegistros($consulta);
			$this->setTotalRegistros($numeroRegistros);
			$registros=$dao->getNMRegistros($consulta,$this->registroDesde,$this->registrosPorPagina);
			return $registros;
		}catch(Exception $e){
			//new mensajes("Lista ".$this->id.":".$e->getMessage());
			$this->setTotalRegistros(0);
			return array();
		}
	}
	
	function generarNavegador(&$xml){
		if($this->totalRegistros>0){
			$this->xml=$xml;
			$total="_caja class='paginacion'_";
		
			######### Botón ir a primera página #########
			if($this->paginaActual>1 && ($this->paginaActual-1)>1){
				$total.="_caja style='padding:3px;margin:2px;border:1px solid;cursor:pointer;float:left;' onclick='enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:1\");'_Primero_finCaja_";
			}
		
			######### Botón anterior #########
			if($this->paginaActual>1){
				$total.="_caja style='padding:3px;margin:2px;border:1px solid;cursor:pointer;float:left;' onclick='enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:".($this->paginaActual-1)."\");'_Anterior_finCaja_";
			}
		
			######### Selector de páginas hacia atras #########
			if(($this->paginaActual-1)>0){
				$limitePaginacion=intval(floor(log(($this->paginaActual-1), $this->basePotenciaSaltosPaginas)));
				switch($this->tipoSelectorPaginador){
					case "botones":
						$listado=false;
						$totalTmp="";
						for ($contadorPaginacion=$limitePaginacion;$contadorPaginacion>=0;$contadorPaginacion--){
							$deltaPaginacion=pow($this->basePotenciaSaltosPaginas, $contadorPaginacion);
							if(($this->paginaActual-$deltaPaginacion)<=$this->totalPaginas){
								$totalTmp.="_caja style='padding:3px;margin:2px;border:1px solid;cursor:pointer;float:left;' onclick='enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:".($this->paginaActual-$deltaPaginacion)."\");'_".($this->paginaActual-$deltaPaginacion)."_finCaja_";
							}
							$listado=true;
						}
						if($listado){
							$total.=$totalTmp;
						}
						break;
					case "selector":default:
						$totalTmp="_seleccion style='float:left;' onchange='if(this.value>0){enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:\"+this.value);}'_";
						$totalTmp.="_opcion value='0'_Seleccione..._finOpcion_";
						$listado=false;
						for ($contadorPaginacion=$limitePaginacion;$contadorPaginacion>=0;$contadorPaginacion--){
							$deltaPaginacion=pow($this->basePotenciaSaltosPaginas, $contadorPaginacion);
							if(($this->paginaActual-$deltaPaginacion)<=$this->totalPaginas){
								$totalTmp.="_opcion value='".($this->paginaActual-$deltaPaginacion)."'_Página ".($this->paginaActual-$deltaPaginacion)."_finOpcion_";
							}
							$listado=true;
						}
						$totalTmp.="_finSeleccion_";
						if($listado){
							$total.=$totalTmp;
						}
				}
			}
			if($this->totalPaginas>1){
				$total.="_caja style='padding:3px;margin:2px;border:1px solid;float:left;'_Página {$this->paginaActual} de {$this->totalPaginas} ({$this->totalRegistros} registros)_finCaja_";
			}
		
			if(($this->totalPaginas-$this->paginaActual)>0){
				$limitePaginacion=floor(log(($this->totalPaginas-$this->paginaActual), $this->basePotenciaSaltosPaginas));
				switch($this->tipoSelectorPaginador){
					case "botones":
						$listado=false;
						$totalTmp="";
						for ($contadorPaginacion=$limitePaginacion;$contadorPaginacion>=0;$contadorPaginacion--){
							$deltaPaginacion=pow($this->basePotenciaSaltosPaginas, $contadorPaginacion);
							if(($this->paginaActual+$deltaPaginacion)<=$this->totalPaginas){
								$totalTmp="_caja style='padding:3px;margin:2px;border:1px solid;cursor:pointer;float:left;' onclick='enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:".($this->paginaActual+$deltaPaginacion)."\");'_".($this->paginaActual+$deltaPaginacion)."_finCaja_".$totalTmp;
							}
							$listado=true;
						}
						if($listado){
							$total.=$totalTmp;
						}
						break;
					case "selector":default:
						$totalTmp="_seleccion style='float:left;' onchange='if(this.value>0){enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:\"+this.value);}'_";
						$totalTmp.="_opcion value='0'_Seleccione..._finOpcion_";
						$listado=false;
						for ($contadorPaginacion=0;$contadorPaginacion<=$limitePaginacion;$contadorPaginacion++){
							$deltaPaginacion=pow($this->basePotenciaSaltosPaginas, $contadorPaginacion);
							if(($this->paginaActual+$deltaPaginacion)<=$this->totalPaginas){
								$totalTmp.="_opcion value='".($this->paginaActual+$deltaPaginacion)."'_Página ".($this->paginaActual+$deltaPaginacion)."_finOpcion_";
							}
							$listado=true;
						}
						$totalTmp.="_finSeleccion_";
						if($listado){
							$total.=$totalTmp;
						}
				}
			}
		
			######### Botón siguiente #########
			if($this->paginaActual<$this->totalPaginas){
				$total.="_caja style='padding:3px;margin:2px;border:1px solid;cursor:pointer;float:left;' onclick='enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:".($this->paginaActual+1)."\");'_Siguiente_finCaja_";
			}
		
			######### Botón ir al Ultimo #########
			if($this->paginaActual<$this->totalPaginas && ($this->paginaActual+1)<$this->totalPaginas){
				$total.="_caja style='padding:3px;margin:2px;border:1px solid;cursor:pointer;float:left;' onclick='enviarPeticionCookie(\"paginacion{$this->id}\",\"operacion:pagina,parametro:".$this->totalPaginas."\");'_Ultimo_finCaja_";
			}
				
			$total.="_finCaja_";
			return ControlXML::agregarNodoTexto($this->xml,"T",$total);
		}else{
			return $this->xml;
		}
	}
	
	function procesarPeticionNavegador(){
		if(isset($_COOKIE["paginacion{$this->id}"])){
			$peticionCookie=explode(",",$_COOKIE["paginacion{$this->id}"]);
			setcookie("paginacion{$this->id}","",-1,"/");
			$peticion=array();
			foreach($peticionCookie as $variable){
				$tmp=explode(":",$variable);
				if(isset($tmp[0])&&isset($tmp[1])){
					$peticion["{$tmp[0]}"]=$tmp[1];
				}
			}
			if(isset($peticion["operacion"])&&isset($peticion["parametro"])){
				switch($peticion["operacion"]){
					case "pagina":
						if(intval($peticion["parametro"])>0){
							if($this->totalPaginas>=intval($peticion["parametro"])){
								$this->paginaActual=intval($peticion["parametro"]);
								$this->sesion->escribirParametro("{$this->id}{$this->casoUso}_paginaActual",$this->paginaActual);
								$this->setTotalRegistros(-1);
							}
						}
						break;
				}
			}
		}
	}
}
?>
