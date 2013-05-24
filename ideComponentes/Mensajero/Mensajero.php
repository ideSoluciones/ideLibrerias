<?php

	class Mensajero extends ComponentePadre implements componente{

		function Mensajero(){
			//$this->js[]="ruta/archivo.js";
			//$this->css[]="ruta/archivo.css";
		}

		function obtenerResultado($xml){

			$form=new SimpleXMLElement("<Formulario/>");
			$nodo=$form->addChild("Propiedad");
			$nodo->addAttribute("nombre","idCasoUso");
			$nodo->addAttribute("valor",$xml["idCasoUso"]);
			
			$nodo=$form->addChild("Propiedad");
			$nodo->addAttribute("nombre","enctype");
			$nodo->addAttribute("valor","multipart/form-data");

			// Los campos correo, cc_correo y cco_correo deben ser tipo correo,
			// pero por el momento para soportar las comas (,) es necesario
			// que sean cadena para solucionar esto implementar ticket #155
			$nodo=$form->addChild("Campo");
			$nodo->addAttribute("nombre", "correo");
			//$nodo->addAttribute("tipo", 'correo');
			$nodo->addAttribute("tipo", 'cadena');
			$nodo->addAttribute("requerido", "true");
			$nodo->addAttribute("titulo", "Para: (*)");

			if(strcasecmp($xml["cc_correo"],"true")==0){
				$nodo=$form->addChild("Campo");
				$nodo->addAttribute("nombre", "cc_correo");
				//$nodo->addAttribute("tipo", 'correo');
				$nodo->addAttribute("tipo", 'cadena');
				$nodo->addAttribute("titulo", "CC:");
			}

			if(strcasecmp($xml["cco_correo"],"true")==0){
				$nodo=$form->addChild("Campo");
				$nodo->addAttribute("nombre", "cco_correo");
				//$nodo->addAttribute("tipo", 'correo');
				$nodo->addAttribute("tipo", 'cadena');
				$nodo->addAttribute("titulo", "CCO:");
			}

			if(strcasecmp($xml["responder"],"true")==0){
				$nodo=$form->addChild("Campo");
				$nodo->addAttribute("nombre", "responder");
				//$nodo->addAttribute("tipo", 'correo');
				$nodo->addAttribute("tipo", 'cadena');
				$nodo->addAttribute("titulo", "Responder a:");
			}

			$nodo=$form->addChild("Campo");
			$nodo->addAttribute("nombre", "asunto");
			$nodo->addAttribute("tipo", 'cadena');
			$nodo->addAttribute("requerido", "true");
			$nodo->addAttribute("titulo", "Asunto: (*)");

			if(strcasecmp($xml["archivo"],"true")==0){
				$nodo=$form->addChild("Campo");
				$nodo->addAttribute("nombre", "archivo");
				$nodo->addAttribute("tipo", 'archivo');
				$nodo->addAttribute("titulo", "Adjuntar archivo");
			}

			$nodo=$form->addChild("Campo");
			$nodo->addAttribute("nombre", "mensaje");
			$nodo->addAttribute("tipo", 'texto');
			$nodo->addAttribute("requerido", "true");
			$nodo->addAttribute("titulo", "Mensaje: (*)");

			$nodo=$form->addChild("Propiedad");
			$nodo->addAttribute("nombre", "recaptcha");
			$nodo->addAttribute("valor", $xml["recaptcha"]);

			$nodo=$form->addChild("Campo");
			$nodo->addAttribute("nombre", "enviar");
			$nodo->addAttribute("tipo", 'enviar');
			$nodo->addAttribute("titulo", "Enviar Correo");

			return $this->llamarClaseGenerica($form);
		}

	}

?>
