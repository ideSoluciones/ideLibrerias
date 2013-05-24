<?php
/* Pendientes por validar
vector
ip 6
*/

	class ValorRequeridoNulo extends Exception{
	    // Redefine the exception so message isn't optional
    	public function __construct($message, $code = 0) {
		    // some code
		
		    // make sure everything is assigned properly
		    parent::__construct($message, $code);
		}

		// custom string representation of object */
		public function __toString() {
		    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
		}
	}
	
	class TipoValorInvalido extends Exception{
	    // Redefine the exception so message isn't optional
    	public function __construct($message, $code = 0) {
		    // some code
		
		    // make sure everything is assigned properly
		    parent::__construct($message, $code);
		}

		// custom string representation of object */
		public function __toString() {
		    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
		}
	}

	class ValidarTipo{
		
		function tipo($tipo,$dato,$requerido=false){
			if($GLOBALS["debug"]>3){ registrarlog("->ValidarTipo::tipo() = Se desactivo toda la validación de tipos.<br>"); return true; }
			//new mensajes("Solicito revizar ".$tipo."(".$dato.")");
			if($requerido && (is_null($dato) || strcmp($dato,"")==0)){
				throw new ValorRequeridoNulo("no puede estar vacio.");
			}
			switch($tipo){
			case "entero": case "llavePrimariaAutonumerica": case "autonumerico":
				if(!$this->entero($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos.");
				}else{
					return true;
				}
				break;
			case "cadena": case "clave":
				if(!$this->cadena($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos.");
				}else{
					return true;
				}
				break;
				
			case "decimal":
				//new mensajes("Revisando decimal ".$dato.($this->decimal($dato)?"OK":"ERR"));
				//if(!$this->decimal($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos.");
				//}else{
					return true;
				//}
				break;
			case "correo":
				if(!$this->email($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos o el formato es incorrecto.");
				}else{
					return true;
				}
				break;
			case "boleano":
				if(!$this->booleano($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos o el formato es incorrecto.");
				}else{
					return true;
				}
				break;
			case "xml":
				if(!$this->xml($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos o el formato es incorrecto.");
				}else{
					return true;
				}
				break;
			case "fecha":
				if(!$this->fechaEstandar($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos o el formato es incorrecto.");
				}else{
					return true;
				}
				break;
			case "ip":
				if(!$this->ip($dato)){
					throw new TipoValorInvalido("contiene caracteres invalidos o el formato es incorrecto.");
				}else{
					return true;
				}
				break;
			default:
				/*
				llavePrimaria
				llavePrimariaForanea
				llaveForanea
				vector
				*/
				return true;
			}
		}
		
		function entero($int,$min=null,$max=null){
			$int=intval($int);
			if(is_numeric($int)){
				if(!is_null($min)){
					if($int<$min){
						return false;
					}
				}
				if(!is_null($max)){
					if($int>$max){
						return false;
					}
				}
				return true;
			}else{
				return false;
			}
		}
	
		function email($email){
			if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			   return true;
			}else{
			   return false;
			}
		}
		
		function booleano($bool){
			if(is_bool($bool)){
				return true;
			}
			if(strcmp(strtoupper($bool),"TRUE")==0){
			   return true;
			}
			if(strcmp(strtoupper($bool),"FALSE")==0){
				return true;
			}
			return false;
		}
		
		function decimal($float){
			$float=floatval($float);
			if(is_numeric($float)){
			   return true;
			}else{
			   return false;
			}
		}
		
		
		function cadena($match){
				//echo ("analizando [".$match."]");
			preg_match_all('/[\\\'\"]+/',$match, $matches);
				//echo ("analizando [".revisarArreglo($matches, "matches")."]");
			if(count($matches[0])>0){
				return false;
			}else{
				return true;
	       		}
	    	}
	    	
	    	function xml($xml){
	    		try{
	    			$obj=@new SimpleXMLElement(stripslashes($xml));
	    		}catch(Exception $e){
	    			return false;
	    		}
	    		return true;
	    	}
	    	
	    	//AAAA-MM-DD
	    	function fechaEstandar($fecha){
	    		$fecha=str_replace(array(":", " "), "-", $fecha);
	    		$std=explode("-","$fecha");
	    		//@todo: Falta analizar la hora si esta
	    		//if(count($std)!=3) return false;
	    		if(strlen($std[0])>4 || strlen($std[0])==0) return false;
	    		if(strlen($std[1])>2 || strlen($std[1])==0) return false;
	    		if(strlen($std[2])>2 || strlen($std[2])==0) return false;
	    		
	    		if (isset($std[3]))
		    		if(strlen($std[3])>2 || strlen($std[3])==0) return false;
	    		if (isset($std[4]))
	    			if(strlen($std[4])>2 || strlen($std[4])==0) return false;
	    		if (isset($std[5]))
	    			if(strlen($std[5])>2 || strlen($std[5])==0) return false;

	    		if (isset($std[3]))
	    			if($std[3]<0 || $std[3]>=24) return false;
	    		if (isset($std[4]))
	    			if($std[4]<0 || $std[4]>=60) return false;
	    		if (isset($std[5]))
	    			if($std[5]<0 || $std[5]>=60) return false;
	    		return checkdate($std[1], $std[2], $std[0]);
		}
		
		function ip($ip) {
			$partes=explode(".","$ip");
			if (count($partes)!=4) {
				return false;
			}
			for($i=0;$i<4;$i++){ 
				$tmp=(int)$partes[$i];
				if($tmp>255){
					return false;
				}
			}
			return true;
		}

	}
?>
