<?php
	class ControlAjax{
		
		private static function obtenerPeticiones(){
			$sesion=Sesion::getInstancia();
			$peticiones=json_decode($sesion->leerParametroInterno("ControlAjax","peticiones"),true);
			if(!is_array($peticiones)){
				return array();
			}else{
				return $peticiones;
			}
		}
		
		public static function generarContenido($contenido,$clave,$propiedades=null){
			$peticiones=ControlAjax::obtenerPeticiones();
			if(!is_null($propiedades)){
				if(isset($peticiones["$clave"])){
					if(isset($peticiones["$clave"]["clase"])&&isset($peticiones["$clave"]["funcion"])){
						$clase=$peticiones["$clave"]["clase"];
						$funcion=$peticiones["$clave"]["funcion"];
						if(method_exists($peticiones["$clave"]["clase"],$peticiones["$clave"]["funcion"])){
							$obj=new $clase();
							$propiedades=json_decode(str_replace("'",'"',$propiedades),true);
							$prop=array();
							if(is_array($propiedades)){
								$parametrosOcultos=null;
								foreach($propiedades as $propiedad){
									if(strcmp($propiedad["nombre"],"oculto")==0){
										if(is_null($parametrosOcultos)){
											$parametrosOcultos=ControlAjax::obtenerParametrosOcultos();
										}
										if(isset($parametrosOcultos["{$propiedad["valor"]}"]["nombre"])&&isset($parametrosOcultos["{$propiedad["valor"]}"]["valor"])){
											$prop[(string)$parametrosOcultos["{$propiedad["valor"]}"]["nombre"]]=$parametrosOcultos["{$propiedad["valor"]}"]["valor"];
										}
									}else{
										$prop[(string)$propiedad["nombre"]]=str_replace("[nl]","\n",(string)$propiedad["valor"]);
									}
								}
							}
							$obj->$funcion($contenido,$prop);
						}
					}elseif(isset($peticiones["$clave"]["operacion"])){
						$clase=$peticiones["$clave"]["operacion"];
						if(method_exists($peticiones["$clave"]["operacion"],"generarContenido")){
							$obj=new $clase();
							$propiedades=json_decode(str_replace("'",'"',$propiedades),true);
							foreach($propiedades as &$propiedad){
								$propiedad=str_replace("[nl]","\n",$propiedad);
							}
							$obj->generarContenido($contenido,$propiedades);
						}
					}
				}
			}else{
				if(isset($peticiones["$clave"])){
					if(isset($peticiones["$clave"]["clase"])&&isset($peticiones["$clave"]["funcion"])){
						$clase=$peticiones["$clave"]["clase"];
						$funcion=$peticiones["$clave"]["funcion"];
						if(method_exists($peticiones["$clave"]["clase"],$peticiones["$clave"]["funcion"])){
							$obj=new $clase();
							$obj->$funcion($contenido);
						}
					}
				}
			}
		}

		public static function solicitarClave($parametros){
			if(is_array($parametros)){
				$sesion=Sesion::getInstancia();
				$peticiones=ControlAjax::obtenerPeticiones();
				$generada=false;
				$clave=0;
				if(isset($parametros["clase"])&&isset($parametros["funcion"])){
					$indice=ControlAjax::obtenerIndicePeticiones();
					if(isset($indice["{$parametros["clase"]}::{$parametros["funcion"]}"])){
						$clave=$indice["{$parametros["clase"]}::{$parametros["funcion"]}"];
					}else{
						while(!$generada){
							$clave=mt_rand();
							if(!array_key_exists($clave,$peticiones)){
								$generada=true;
							}
						}
						$indice["{$parametros["clase"]}::{$parametros["funcion"]}"]=$clave;
						$peticiones["$clave"]=$parametros;
						$sesion->escribirParametroInterno("ControlAjax","peticiones",json_encode($peticiones));
						$sesion->escribirParametroInterno("ControlAjax","indicePeticiones",json_encode($indice));
					}
				}else{
					while(!$generada){
						$clave=mt_rand();
						if(!array_key_exists($clave,$peticiones)){
							$generada=true;
						}
					}
					$peticiones["$clave"]=$parametros;
					$sesion->escribirParametroInterno("ControlAjax","peticiones",json_encode($peticiones));
				}
				
				return $clave;
			}
			return false;
		}

		private static function obtenerIndicePeticiones(){
			$sesion=Sesion::getInstancia();
			$peticiones=json_decode($sesion->leerParametroInterno("ControlAjax","indicePeticiones"),true);
			if(!is_array($peticiones)){
				return array();
			}else{
				return $peticiones;
			}
		}
		
		public static function generarFuncionJS($clave,$parametros){
			//return "peticionAjax(\"".$clave."\",\"".str_replace('"','\\"',str_replace("\n","",$parametros->obtenerParametros()))."\");";
			return "peticionAjax(\"".$clave."\",\"".base64_encode($parametros->obtenerParametros())."\");";
		}
		
		private static function obtenerParametrosOcultos(){
			$sesion=Sesion::getInstancia();
			$peticiones=json_decode($sesion->leerParametroInterno("ControlAjax","parametrosOcultos"),true);
			if(!is_array($peticiones)){
				return array();
			}else{
				return $peticiones;
			}
		}
		
		private static function obtenerIndiceParametrosOcultos(){
			$sesion=Sesion::getInstancia();
			$peticiones=json_decode($sesion->leerParametroInterno("ControlAjax","indiceParametrosOcultos"),true);
			if(!is_array($peticiones)){
				return array();
			}else{
				return $peticiones;
			}
		}
		
		public static function solicitarClaveParametrosOcultos($nombre,$valor){
			if(strlen($valor)>0&&strlen($nombre)>0){
				$sesion=Sesion::getInstancia();
				$parametros=ControlAjax::obtenerParametrosOcultos();
				$indice=ControlAjax::obtenerIndiceParametrosOcultos();
				$clave=0;
				if(isset($indice["$nombre"])){
					if(isset($parametros["{$indice["$nombre"]}"])){
						$parametros["{$indice["$nombre"]}"]["nombre"]=$nombre;
						$parametros["{$indice["$nombre"]}"]["valor"]=$valor;
						$sesion->escribirParametroInterno("ControlAjax","parametrosOcultos",json_encode($parametros));
						$clave=$indice["$nombre"];
					}
				}else{
					$generada=false;
					while(!$generada){
						$clave=mt_rand();
						if(!array_key_exists($clave,$parametros)){
							$generada=true;
						}
					}
					$parametros["$clave"]=array("nombre"=>$nombre,"valor"=>$valor);
					$indice["$nombre"]=$clave;
					$sesion->escribirParametroInterno("ControlAjax","parametrosOcultos",json_encode($parametros));
					$sesion->escribirParametroInterno("ControlAjax","indiceParametrosOcultos",json_encode($indice));
				}
				return $clave;
			}
			return false;
		}
		
	}
	class parametros{
		private $param=array();
		function numero($nombre, $valor,$oculto=false){
			if(is_numeric($valor)&&strlen($nombre)>0){
				if($oculto){
					if($clave=ControlAjax::solicitarClaveParametrosOcultos($nombre,$valor)){
						$this->param[]="{nombre:\"oculto\",valor:\"".$clave."\"}";
					}else{
						$this->param[]="{nombre:\"error\",valor:\"No se pudo almacenar el parametro oculto.\"}";
					}
				}else{
					$this->param[]="{nombre:\"{$nombre}\",valor:".$valor."}";
				}
			}
		}
		function texto($nombre, $valor,$oculto=false){
			if(is_string($valor)&&strlen($nombre)>0){
				if($oculto){
					if($clave=ControlAjax::solicitarClaveParametrosOcultos($nombre,$valor)){
						$this->param[]="{nombre:\"oculto\",valor:\"".$clave."\"}";
					}else{
						$this->param[]="{nombre:\"error\",valor:\"No se pudo almacenar el parametro oculto.\"}";
					}
				}else{
					$this->param[]="{nombre:\"{$nombre}\",valor:\"".$valor."\"}";
				}
			}
		}
		function script($nombre, $valor){
			if(is_string($valor)&&strlen($nombre)>0){
				$valor=str_replace("'",'"',$valor);
				$this->param[]="{nombre:\"{$nombre}\",valor:".$valor."}";
			}
		}
		function obtenerParametros(){
			return "[".implode(",",$this->param)."]";
		}
	}
	
?>
