<?php

	class ControlConfiguracion{

		
		static function obtener($nombreVariable,$idUsuario=null){
			$sesion=Sesion::getInstancia();
			$variables=array();
			if(is_string($nombreVariable)){
				$nombreVariable=array($nombreVariable);
			}
			if(is_array($nombreVariable)){
				$dao_Configuracion=new DAO0Configuracion($sesion->getDB());
				foreach($nombreVariable as $nombre){
					try{
						$filtros=array("nombreConfiguracion"=>(string)$nombre);
						if(!is_null($idUsuario)){
							$filtros["idUsuario"]=$idUsuario;
						}
						$configuracionSolicitada=$dao_Configuracion->getRegistroCondiciones($filtros);
						$tmp=new SimpleXMLElement($configuracionSolicitada->getXmlValor());
						foreach($tmp->children() as $hijo){
							switch($hijo->getName()){
								case "Propiedad":
								case "Propiedades":
									if (strlen((string)$hijo['valor'])>0){
										$valor=(string)$hijo['valor'];
									}else{
										$valor=(string)$hijo;
									}
									
									$variables[(string)$nombre][(string)$hijo['nombre']]=$valor;
							}
						}
					}catch(Exception $e){}
				}
			}
			return $variables;
		}

		static function set($idUsuario,$nombreVariable,$valor){
			$sesion=Sesion::getInstancia();
			$vo_Configuracion=new VO0Configuracion(null,$idUsuario,$nombreVariable,$valor);
			$dao_Configuracion=new DAO0Configuracion($sesion->getDB());
			try{
				$conf=$dao_Configuracion->getRegistroCondiciones(array("idUsuario"=>$idUsuario,"nombreConfiguracion"=>$nombreVariable));
				if(strcmp($valor,$conf->getXmlValor())!=0){
					$conf->setXmlValor($valor);
					try{
						$dao_Configuracion->actualizarRegistro($conf);
					}catch(Exception $e){
						msg::add($e->getMessage());
					}
				}
			}catch(sinResultados $e){
				try{
					$dao_Configuracion->agregarRegistro($vo_Configuracion);
				}catch(Exception $e){
					msg::add($e->getMessage());
				}
			}catch(Exception $e){
				msg::add($e->getMessage());
			}
		}
	}
	
	class CConf extends ControlConfiguracion{}

?>
