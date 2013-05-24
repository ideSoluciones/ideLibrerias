<?php

class ControlVistaBreadcrumb{
	function ControlVistaBreadcrumb(){
	}
	// breadcrumb: apuntador a un SimpleXMLELement que contiene un breadcrumb
	// titulo: nombre que aparece en el nuevo nivel del breadcrumb
	// enlace: url a la cual será direccionado el usuario al hacer clic en este nivel del breacrumb
	// Agrega un nuevo nivel a un breadcrumb
	public static function agregarNivelBreadcrumb(&$breadcrumb,$titulo,$enlace,$claseEnlace=""){
		// ToDo: validar que $breacrumb es de clase SimpleXML
		// ToDo: validar que $titulo es de tipo string
		// ToDo: validar que $enlace es de tipo string
		$categoria = $breadcrumb->addChild("Categoria");
		$categoria->addAttribute("titulo",$titulo);
		$categoria->addAttribute("enlace",$enlace);
		$categoria->addAttribute("claseEnlace",$claseEnlace);
		return $categoria;
	}

}
?>
