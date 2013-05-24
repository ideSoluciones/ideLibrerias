<?php
	class CCampo{
		private $llavePrimaria=false;
		private $llaveForanea=false;
		private $nombre="";
		private $funcionGet="";
		private $tipo="";
		private $tipoEnFormulario="";
		private $autoIncremento=false;
		private $descripcion="";
		private $tablaClaveForanea="";
		private $campoClaveForanea="";
		private $campoTextoClaveForanea="";
		private $titulo="";
		private $unico=false;
		private $activo=false;
		private $clave=false;
		private $requerido=false;
		private $valorDefecto="";
		private $valorMinimo=null;
		private $valorMaximo=null;
		private $numeroCaracteres=-1;
		private $valor="";
		private $valorMostrar="";
		private $otros;
		
		function __construct($campo=null){
			$this->otros=new CPropiedades();
		}
		
		function set($nombre,$valor){
			if(is_string($nombre)){
				switch($nombre){
					case "tipo":
						if(is_string($valor)){
							if(strlen($valor)>0){
								$this->$nombre=$valor;
								$this->tipoEnFormulario=$valor;
								if(strcmp($valor,"clave")==0){
									$this->clave=true;
								}
							}else{
								throw new valorInvalido(t("CCampo, la propiedad [$nombre] no puede ser vacía."));
							}
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo String."));
						}
						break;
					case "nombre":
						if(is_string($valor)){
							if(strlen($valor)>0){
								$this->$nombre=$valor;
								$this->funcionGet="get".ucfirst($valor);
								if(strcmp($valor,"activo")==0){
									$this->activo=true;
								}
							}else{
								throw new valorInvalido(t("CCampo, la propiedad [$nombre] no puede ser vacía."));
							}
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo String."));
						}
						break;
					case "tablaClaveForanea":
					case "campoClaveForanea":
					case "campoTextoClaveForanea":
						if(is_string($valor)){
							if(strlen($valor)>0){
								$this->$nombre=$valor;
							}else{
								throw new valorInvalido(t("CCampo, la propiedad [$nombre] no puede ser vacía."));
							}
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo String."));
						}
						break;
					case "titulo":
					case "descripcion":
						if(is_string($valor)){
							$this->$nombre=$valor;
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo String."));
						}
						break;
					case "valorDefecto":
						$this->$nombre=$valor;
						break;
					case "valor":
						$this->setValor($valor);
						break;
					case "autoIncremento":
					case "unico":
					case "requerido":
					case "llavePrimaria":
					case "llaveForanea":
						if(is_string($valor)){
							if(strcmp(strtolower($valor),"true")==0){
								$this->$nombre=true;
							}elseif(strcmp(strtolower($valor),"false")==0){
								$this->$nombre=false;
							}else{
								throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo booleano."));
							}
						}elseif(is_int($valor)){
							if($valor==0){
								$this->$nombre=false;
							}else{
								$this->$nombre=true;
							}
						}elseif(is_bool($valor)){
							$this->$nombre=$valor;
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo booleano."));
						}
						break;
					case "valorMinimo":
					case "valorMaximo":
						if(is_int($valor)){
							$this->$nombre=intval($valor);
						}elseif(is_float($valor)){
							$this->$nombre=floatval($valor);
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo numérico."));
						}
					case "numeroCaracteres":
						if(is_int($valor)){
							$this->$nombre=intval($valor);
						}else{
							throw new valorInvalido(t("CCampo, la propiedad [$nombre] tiene que ser de tipo entero."));
						}
					default:
						$this->otros->addPropiedad($nombre,$valor);
				}
			}
		}

		function get($nombre){
			if(property_exists($this, $nombre)){
				return $this->$nombre;
			}else{
				return $this->otros->getPropiedad($nombre);
			}
		}
		
		function setValor($valor,$revisarReferencias=false){
			switch($this->tipo){
				case "entero":
					if(!is_numeric($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						$valor=intval($valor);
					}
					break;
				case "decimal":
					if(!is_numeric($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						$valor=floatval($valor);
					}
					break;
				case "cadena":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}
					break;
				case "autonumerico":
					if(!is_numeric($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						$valor=intval($valor);
					}
					break;
				case "correo":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						if(!filter_var($valor, FILTER_VALIDATE_EMAIL)){
							throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es un correo electrónico valido."));
						}
					}
					break;
				case "url":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						if(!filter_var($valor, FILTER_VALIDATE_URL)){
							throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es una URL valida."));
						}
					}
					break;
				case "fecha":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						try{
							$fecha = new DateTime($valor);
							$valor=$fecha->format('Y-m-d');
						}catch(Exception $e){
							throw new valorInvalido(t("CCampo, el campo {$this->nombre} se esta recibiendo una fecha invalida."));
						}
					}
					break;
				case "fechaHora":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						try{
							$fecha = new DateTime($valor);
							$valor=$fecha->format('Y-m-d H:i:s');
						}catch(Exception $e){
							throw new valorInvalido(t("CCampo, el campo {$this->nombre} se esta recibiendo una fecha invalida."));
						}
					}
					break;
				case "hora":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}else{
						try{
							$fecha = new DateTime($valor);
							$valor=$fecha->format('H:i:s');
						}catch(Exception $e){
							throw new valorInvalido(t("CCampo, el campo {$this->nombre} se esta recibiendo una hora invalida."));
						}
					}
					break;
				case "clave":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}
					break;
				case "booleano":
					if(!is_bool($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}
					break;
				case "texto":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}
					break;
				case "color":
					if(!is_string($valor)){
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} tiene que ser de tipo {$this->tipo}."));
					}
					break;
				case "xml":
					if(is_string($valor)){
						try{
							@new SimpleXMLElement($valor);
						}catch(Exception $e){
							throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es un XML valido."));
						}
					}elseif(is_object($valor)){
						if(strcmp(get_class($valor),"SimpleXMLElement")!=0){
							throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es un XML valido."));
						}
					}else{
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es un XML valido."));
					}
					break;
				case "ip":
					if(is_string($valor)){
						if(!filter_var($valor, FILTER_VALIDATE_IP)){
							throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es una IP valida."));
						}
					}else{
						throw new valorInvalido(t("CCampo, el valor del campo {$this->nombre} no es una IP valida."));
					}
					break;
			}
			if($this->requerido){
				if(is_string($valor)){
					if(strlen($valor)<=0){
						if(is_string($this->valorDefecto)&&strlen($this->valorDefecto)>0){
							$valor=$this->valorDefecto;
						}else{
							throw new valorInvalido(t("CCampo, el campo {$this->nombre} es requerido."));
						}
					}else{
						if(strcmp(strtolower($valor),"null")==0){
							throw new valorInvalido(t("CCampo, el campo {$this->nombre} no puede ser NULO."));
						}
					}
				}elseif(is_null($valor)){
					throw new valorInvalido(t("CCampo, el campo {$this->nombre} no puede ser NULO."));
				}
			}
			if(!is_null($this->valorMinimo)){
				if(is_numeric($valor)&&is_numeric($this->valorMinimo)){
					if($valor<$this->valorMinimo){
						throw new valorInvalido(t("CCampo, el campo {$this->nombre} no puede ser menor que {$this->valorMinimo}."));
					}
				}
			}
			if(!is_null($this->valorMaximo)){
				if(is_numeric($valor)&&is_numeric($this->valorMaximo)){
					if($valor>$this->valorMaximo){
						throw new valorInvalido(t("CCampo, el campo {$this->nombre} no puede ser mayor que {$this->valorMaximo}."));
					}
				}
			}
			
			if(is_string($valor)){
				if($this->numeroCaracteres>=0){
					if($this->numeroCaracteres<strlen($valor)){
						throw new valorInvalido(t("CCampo, el campo {$this->nombre} no puede tener una longitud mayor a {$this->numeroCaracteres}."));
					}
				}
			}
			if($revisarReferencias){
				if(strlen($this->tablaClaveForanea)>0&&strlen($this->campoClaveForanea)>0&&strlen($this->campoTextoClaveForanea)>0){
					$nombreDAO="DAO".$this->tablaClaveForanea;
					$funcionGet="get".ucfirst($this->campoTextoClaveForanea);
					if(method_exists($nombreDAO,$funcionGet)){
						if(method_exists($nombreDAO,"getRegistroCondiciones")){
							$sesion=Sesion::getInstancia();
							$dao=new $nombreDAO($sesion->getDB());
							$reg=$dao->getRegistroCondiciones(array($this->campoClaveForanea=>$valor));
							$this->valorMostrar=$reg->$funcionGet();
						}else{
							throw new metodoNoExiste(t("CCampo, el método {$nombreDAO}::getRegistroCondiciones() no existe."));
						}
					}else{
						throw new metodoNoExiste(t("CCampo, el método {$nombreDAO}::$funcionGet() no existe."));
					}
				}
			}
			if(strlen("{$this->valorMostrar}")<=0){
				$this->valorMostrar=$valor;
			}
			$this->valor=$valor;
		}
		function esClave(){
			return $this->clave;
		}
		function esPrimaria(){
			return $this->llavePrimaria;
		}
		function esForanea(){
			return $this->llaveForanea;
		}
	}
?>
