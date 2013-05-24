<?php

	class Codigo extends ComponentePadre implements componente{
		
		function Codigo(){
			//$this->js["incluir"][]="../Librerias/ideComponentes/Navegador/navegador.js";
			//$this->css["incluir"][]="../Librerias/ideComponentes/Navegador/navegador.css";
		}
	
		function obtenerResultado($xml, $principal=true){
			return "<script  language='javascript' type='text/javascript'>".$xml."</script>";
		}
		
	}
	
	
	
?>
