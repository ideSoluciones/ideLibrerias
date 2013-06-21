<?php
	class VO0Usuario{
		protected $idUsuario=null;
		protected $user=null;
		protected $pass=null;
		protected $correo=null;
		protected $xmlPropiedades=null;
		protected $activo=null;
		function VO0Usuario($idUsuario=null,$user=null,$pass=null,$correo=null,$xmlPropiedades=null,$activo=null){
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($user)) $this->setUser($user);
			if(!is_null($pass)) $this->setPass($pass);
			if(!is_null($correo)) $this->setCorreo($correo);
			if(!is_null($xmlPropiedades)) $this->setXmlPropiedades($xmlPropiedades);
			if(!is_null($activo)) $this->setActivo($activo);
		}
		function toString(){
			return 
			'idUsuario='.$this->idUsuario.', '.
			'user='.$this->user.', '.
			'pass='.$this->pass.', '.
			'correo='.$this->correo.', '.
			'xmlPropiedades='.$this->xmlPropiedades.', '.
			'activo='.$this->activo.', ';
		}
		function set($parametros){
			if(is_array($parametros)){
				foreach($parametros as $nombre=>$valor){
					$funcion='set'.ucfirst($nombre);
					if(method_exists($this,$funcion)){
						$this->$funcion($valor);
					}
				}
			}
		}
		function toJson(){
			return json_encode(array('idUsuario'=>$this->idUsuario,'user'=>$this->user,'pass'=>$this->pass,'correo'=>$this->correo,'xmlPropiedades'=>$this->xmlPropiedades,'activo'=>$this->activo));
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setUser($user){ 
 			if(is_null($user)||strlen($user)==0){ 
				 throw new valorNuloInvalido('El campo user es requerido.');
			 }
 			$this->user=$user; 
		}
		function setPass($pass){ 
 			if(is_null($pass)||strlen($pass)==0){ 
				 throw new valorNuloInvalido('El campo pass es requerido.');
			 }
 			$this->pass=$pass; 
		}
		function setCorreo($correo){ 
 			if(is_null($correo)||strlen($correo)==0){ 
				 throw new valorNuloInvalido('El campo correo es requerido.');
			 }
 			$this->correo=$correo; 
		}
		function setXmlPropiedades($xmlPropiedades){ 
 			if(is_null($xmlPropiedades)||strlen($xmlPropiedades)==0){ 
				 throw new valorNuloInvalido('El campo xmlPropiedades es requerido.');
			 }
 			$this->xmlPropiedades=$xmlPropiedades; 
		}
		function setActivo($activo){ 
 			if(is_null($activo)||strlen($activo)==0){ 
				 throw new valorNuloInvalido('El campo activo es requerido.');
			 }
 			$this->activo=$activo; 
		}
		function getIdUsuario(){ return $this->idUsuario; }
		function getUser(){ return $this->user; }
		function getPass(){ return $this->pass; }
		function getCorreo(){ return $this->correo; }
		function getXmlPropiedades(){ return $this->xmlPropiedades; }
		function getActivo(){ return $this->activo; }
	}
?>