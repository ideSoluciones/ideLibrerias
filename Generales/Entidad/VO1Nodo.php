<?php
	class VO1Nodo{
		protected $idNodo=null;
		protected $idAutor=null;
		protected $path=null;
		protected $fecha=null;
		protected $titulo=null;
		protected $contenidoCorto=null;
		protected $contenidoCompleto=null;
		function VO1Nodo($idNodo=null,$idAutor=null,$path=null,$fecha=null,$titulo=null,$contenidoCorto=null,$contenidoCompleto=null){
			if(!is_null($idNodo)) $this->setIdNodo($idNodo);
			if(!is_null($idAutor)) $this->setIdAutor($idAutor);
			if(!is_null($path)) $this->setPath($path);
			if(!is_null($fecha)) $this->setFecha($fecha);
			if(!is_null($titulo)) $this->setTitulo($titulo);
			if(!is_null($contenidoCorto)) $this->setContenidoCorto($contenidoCorto);
			if(!is_null($contenidoCompleto)) $this->setContenidoCompleto($contenidoCompleto);
		}
		function toString(){
			return 
			'idNodo='.$this->idNodo.', '.
			'idAutor='.$this->idAutor.', '.
			'path='.$this->path.', '.
			'fecha='.$this->fecha.', '.
			'titulo='.$this->titulo.', '.
			'contenidoCorto='.$this->contenidoCorto.', '.
			'contenidoCompleto='.$this->contenidoCompleto.', ';
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
			return json_encode(array('idNodo'=>$this->idNodo,'idAutor'=>$this->idAutor,'path'=>$this->path,'fecha'=>$this->fecha,'titulo'=>$this->titulo,'contenidoCorto'=>$this->contenidoCorto,'contenidoCompleto'=>$this->contenidoCompleto));
		}
		function setIdNodo($idNodo){ 
 			if(is_null($idNodo)||strlen($idNodo)==0){ 
				 throw new valorNuloInvalido('El campo idNodo es requerido.');
			 }
 			$this->idNodo=$idNodo; 
		}
		function setIdAutor($idAutor){ 
 			if(is_null($idAutor)||strlen($idAutor)==0){ 
				 throw new valorNuloInvalido('El campo idAutor es requerido.');
			 }
 			$this->idAutor=$idAutor; 
		}
		function setPath($path){ 
 			if(is_null($path)||strlen($path)==0){ 
				 throw new valorNuloInvalido('El campo path es requerido.');
			 }
 			$this->path=$path; 
		}
		function setFecha($fecha=null){ 
  			$this->fecha=$fecha; 
		}
		function setTitulo($titulo=null){ 
  			$this->titulo=$titulo; 
		}
		function setContenidoCorto($contenidoCorto=null){ 
  			$this->contenidoCorto=$contenidoCorto; 
		}
		function setContenidoCompleto($contenidoCompleto){ 
 			if(is_null($contenidoCompleto)||strlen($contenidoCompleto)==0){ 
				 throw new valorNuloInvalido('El campo contenidoCompleto es requerido.');
			 }
 			$this->contenidoCompleto=$contenidoCompleto; 
		}
		function getIdNodo(){ return $this->idNodo; }
		function getIdAutor(){ return $this->idAutor; }
		function getPath(){ return $this->path; }
		function getFecha(){ return $this->fecha; }
		function getTitulo(){ return $this->titulo; }
		function getContenidoCorto(){ return $this->contenidoCorto; }
		function getContenidoCompleto(){ return $this->contenidoCompleto; }
	}
?>