<?php
	class PaqueteAdminArtefactos extends Paquete{
		function PaqueteAdminArtefactos($db){
			$this->Paquete($db);
		}

//		construirJavaScriptXML
		function nombreMenu_construirJavaScriptXML($sesion){
			return "Construir js XML";
		}
		function generarContenido_construirJavaScriptXML($sesion){
			$a= new EditorXML("Datos/EspecificacionNodosXML.xml");
			$contenido=new SimpleXMLElement("
				<Contenido>
						<htmlencodeado>".base64_encode(
							$a->generarEspeficicacion()
						)."</htmlencodeado>
				</Contenido>");
			return $contenido;
		}
		function procesarFormulario_construirJavaScriptXML($sesion){
			$contenido=new SimpleXMLElement("
				<Contenido>
						<Texto>
							<Campo nombre='titulo' nivel='1' valor='Administración Relaciones Usuario Rol'/>
							<Campo nombre='contenido' valor='Seleccione los roles del usuario' />
						</Texto>
				</Contenido>");
			return $contenido;
		}
		
	}

?>
