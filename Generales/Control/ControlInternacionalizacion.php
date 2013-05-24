<?php
	function t($mensaje){
		return $mensaje;
		//return Internacionalizacion::traducir($mensaje);
	}
	class Internacionalizacion{
		static function traducir($mensaje){
			$sesion=Sesion::getInstancia();
			$idioma=$sesion->leerParametro("idioma");
		}
	}
?>
