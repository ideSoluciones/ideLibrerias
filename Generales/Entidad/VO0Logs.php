<?php
	class VO0Logs{
		protected $idLog=null;
		protected $idUsuario=null;
		protected $direcionIP=null;
		protected $fechalog=null;
		protected $ident=null;
		protected $prioridad=null;
		protected $mensaje=null;
		function VO0Logs($idLog=null,$idUsuario=null,$direcionIP=null,$fechalog=null,$ident=null,$prioridad=null,$mensaje=null){
			if(!is_null($idLog)) $this->setIdLog($idLog);
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($direcionIP)) $this->setDirecionIP($direcionIP);
			if(!is_null($fechalog)) $this->setFechalog($fechalog);
			if(!is_null($ident)) $this->setIdent($ident);
			if(!is_null($prioridad)) $this->setPrioridad($prioridad);
			if(!is_null($mensaje)) $this->setMensaje($mensaje);
		}
		function toString(){
			return 
			'idLog='.$this->idLog.', '.
			'idUsuario='.$this->idUsuario.', '.
			'direcionIP='.$this->direcionIP.', '.
			'fechalog='.$this->fechalog.', '.
			'ident='.$this->ident.', '.
			'prioridad='.$this->prioridad.', '.
			'mensaje='.$this->mensaje.', ';
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
			return json_encode(array('idLog'=>$this->idLog,'idUsuario'=>$this->idUsuario,'direcionIP'=>$this->direcionIP,'fechalog'=>$this->fechalog,'ident'=>$this->ident,'prioridad'=>$this->prioridad,'mensaje'=>$this->mensaje));
		}
		function setIdLog($idLog){ 
 			if(is_null($idLog)||strlen($idLog)==0){ 
				 throw new valorNuloInvalido('El campo idLog es requerido.');
			 }
 			$this->idLog=$idLog; 
		}
		function setIdUsuario($idUsuario=null){ 
  			$this->idUsuario=$idUsuario; 
		}
		function setDirecionIP($direcionIP){ 
 			if(is_null($direcionIP)||strlen($direcionIP)==0){ 
				 throw new valorNuloInvalido('El campo direcionIP es requerido.');
			 }
 			$this->direcionIP=$direcionIP; 
		}
		function setFechalog($fechalog){ 
 			if(is_null($fechalog)||strlen($fechalog)==0){ 
				 throw new valorNuloInvalido('El campo fechalog es requerido.');
			 }
 			$this->fechalog=$fechalog; 
		}
		function setIdent($ident=null){ 
  			$this->ident=$ident; 
		}
		function setPrioridad($prioridad=null){ 
  			$this->prioridad=$prioridad; 
		}
		function setMensaje($mensaje){ 
 			if(is_null($mensaje)||strlen($mensaje)==0){ 
				 throw new valorNuloInvalido('El campo mensaje es requerido.');
			 }
 			$this->mensaje=$mensaje; 
		}
		function getIdLog(){ return $this->idLog; }
		function getIdUsuario(){ return $this->idUsuario; }
		function getDirecionIP(){ return $this->direcionIP; }
		function getFechalog(){ return $this->fechalog; }
		function getIdent(){ return $this->ident; }
		function getPrioridad(){ return $this->prioridad; }
		function getMensaje(){ return $this->mensaje; }
	}
?>