<?php
	interface componente{
		/**
		*	obtenerResultado
		*	Procesa un XML y retorna html
		*	$dato SimpleXML
		*/
		public function obtenerResultado($dato);
	
		/**
		*	obtenerCssAIncluir
		*	Retorna arreglo de urls de archivos css o estilos css
		*	Ej: Array("incluir"=> "css/miEstilo.css", "incrustar"=>".estilo{border:1px solid gold;}") 
		* /
		public function obtenerCssAIncluir();
	
		/** 
		*	obtenerJavascriptAIncluir
		*	Retorna arreglo de urls de archivos js o codigo javascript
		*	Ej: Array("incluir"=> "js/miScript.js", "incrustar"=>"function f(x){ return x*x+2; }") 
		* /
		public function obtenerJavascriptAIncluir();
		*/
	}
?>
