<?php
	class PaqueteAdministracionGeneral extends Paquete{
		function PaqueteAdministracionGeneral($db){
			$this->Paquete($db);
		}
		
		##############################
		# Administrar XMLPropiedades #
		##############################
		function nombreMenu_administrarXMLPropiedades($sesion){
			return "Administrar/Campos XML";
		}
		function generarContenido_administrarXMLPropiedades($sesion){
			$contenido=new SimpleXMLElement("<Contenido />");
            $ControlListas=new ControlListas($sesion,"0XMLPropiedades","admXmlProp",array("nombre0XMLPropiedades","tabla","campo","xmlPropiedades"),array(),array("nuevo","editar","borrar"));
            $ControlListas->procesarFormularioSinContenido();
            $ControlListas->generarContenidoEn($contenido,"Administrar campos XML");
			return $contenido;
		}
		
		function procesarFormulario_administrarXMLPropiedades($sesion){
			return $this->generarContenido_administrarXMLPropiedades($sesion);
		}
		#######################################
		### Administración de Configuración ###
		#######################################
		function nombreMenu_adminConfiguracion($sesion){
			return "Administrar/Configuración del sistema";
		}
		function generarContenido_adminConfiguracion($sesion){
			$contenido=new SimpleXMLElement("<Contenido />");
			$ControlListas=new ControlListas($sesion,"0Configuracion","admConf",array("idUsuario","nombreConfiguracion","xmlValor"),array(),array("nuevo","editar","borrar","filtro","desactivar"),array(),"",array("idUsuario"=>"Propietario","nombreConfiguracion"=>"Variable","xmlValor"=>"Configuración"));
            $ControlListas->procesarFormularioSinContenido();
            $ControlListas->generarContenidoEn($contenido,"Configuración de variables del sistema");
			return $contenido;
		}
		function procesarFormulario_adminConfiguracion($sesion){
			return $this->generarContenido_adminConfiguracion($sesion);
		}
		
	}
?>
