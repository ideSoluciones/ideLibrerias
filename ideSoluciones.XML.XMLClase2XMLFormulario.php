<?php


	require_once("ideSoluciones.XML.general.php");


	class XMLClase2XMLFormulario extends generalXML{
//		$result = $xml->xpath('/a/b/c');

		var $propiedades;
		var $filtros;
		var $lista;
		var $datos;
		
		var $formulario;
		
/*
<FormIMEC>
		<Propiedades tabla="tabla1" titulo="Mi primer formulario con IMEC" />
		<Filtros>
			<Lista nombreCampo="idTabla2" otraTabla="tabla2" campoOtraTabla="texto" />
			<Campo nombreCampo="correo" titulo="El email es o contiene:" />
		</Filtros>
		<Lista editar="true" borrar="true" consultar="true">
			<Titulo campo="texto" />
			<Titulo campo="numero" />
		</Lista>
		<Datos>
			<Campo nombre="idTabla1" tipo="llavePrimaria" descripcion="Campo llave primaria" requerido="true"/>
			<Campo nombre="texto" tipo="texto" descripcion="Campo de texto" requerido="true" valorDefecto="a" />
			<Campo nombre="numero" tipo="entero" descripcion="Campo numerico" unico="true" />
			<Campo nombre="correo" tipo="email" descripcion="Campo email" requerido="true" />
			<Campo nombre="idTabla2" tipo="llaveForanea" descripcion="Campo llave foranea"  tablaClaveForanea="tabla2" campoClaveForanea="idTabla2" requerido="true" />
		</Datos>
</FormIMEC>
*/
		var $xmlPrincipal;
		var $listaXmlAux;
		function XMLClase2XMLFormulario($xmlOrg, $listaXml=array()){
/*			echo "xmlOrg= ".var_dump($xmlOrg);
			echo revisarArreglo($xmlOrg, "xmlOrg");*/
			
			//$txml=$xmlOrg->asXML();
			
    			$this->xmlPrincipal= $xmlOrg;
    			
			    /*$error = 'Hola';
			    throw new Exception("hola");
			    */
/*			echo revisarArreglo($this->geshiXML($this->xmlPrincipal), "XMLClase2XMLFormulario");
			echo "XMLClase2XMLFormulario".$this->geshiXML($this->xmlPrincipal);*/
			//echo revisarArreglo($xmlOrg,"xmlOrg");
			//echo revisarArreglo($this->xmlPrincipal,"this->xmlPrincipal");
			if (strcmp($this->xmlPrincipal->getName(), "Clase")!=0){
				assert("Se tiene que enviar un xml <strong>Clase</strong>");
			}
			$this->listaXmlAux=array();
			foreach ($listaXml as $i => $a){
				//echo "SubAgregando $i => $a<br>";
				$this->listaXmlAux[$i]= new SimpleXMLElement($a);
			}
		}
		function imprimir(){
			$t= "<h1>XML Principal</h1><br>";
			$t.= htmlspecialchars($this->xmlPrincipal->asXML())."<br>";
			foreach ($this->listaXmlAux as $i => $a){
				$t.= "<h2>XMLs Auxs $i</h2><br>";
				$t.= htmlspecialchars($a->asXML());
			}
			$t.= "<hr>";
		}
		function agregarCampoFiltros($xml){
			//$campo=new SimpleXMLElement("<Campo />");	
			$campo=$this->datos->addChild("Campo");
			
			//echo revisarArreglo($xml, "XML de agregarCampoFiltros ".count($xml));
			foreach($xml->attributes() as $i => $a ){
				//echo "Analizando ", $i, " como ", $a, "<br>";
				$campo->addAttribute($i, $a);	
			
			}
		/*
			if(strlen($xml["nombre"])!=0)		//	$campo->addAttribute("nombre", $xml["nombre"]);		
			echo "agregando nombre [".$xml["nombre"]."]<br>";
			if(strlen($xml["tipo"])!=0)		//	$campo->addAttribute("tipo", $xml["tipo"]);		
			echo "agregando tipo<br>";
			if(strlen($xml["descripcion"])!=0)	//	$campo->addAttribute("descripcion", $xml["descripcion"]);		
			echo "agregando descripcion<br>";
			if(strlen($xml["titulo"])!=0)		//	$campo->addAttribute("titulo", $xml["titulo"]);					echo "agregando nombre<br>";
			echo "agregando titulo<br>";
			if(strlen($xml["valorDefecto"])!=0)	//	$campo->addAttribute("valorDefecto", $xml["valorDefecto"]);			echo "agregando nombre<br>";
			echo "agregando valorDefecto<br>";
			if(strlen($xml["llavePrimaria"])!=0)	//	$campo->addAttribute("llavePrimaria", $xml["llavePrimaria"]);
			echo "agregando llavePrimaria<br>";*/

			####################
			#### DEPRECATED ####
			####################
				if(strcmp($xml["tipo"], "llaveForanea")==0){
					/*if(strlen($xml["tablaClaveForanea"])!=0)	//$campo->addAttribute("tablaClaveForanea", $xml["tablaClaveForanea"]);		
					echo "agregando tablaClaveForanea<br>";
					if(strlen($xml["campoClaveForanea"])!=0)	//$campo->addAttribute("campoClaveForanea", $xml["campoClaveForanea"]);
					echo "agregando campoClaveForanea<br>";
					if(strlen($xml["campoTextoClaveForanea"])!=0)	//$campo->addAttribute("campoTextoClaveForanea", $xml["campoTextoClaveForanea"]);
					echo "agregando campoTextoClaveForanea<br>";
					if(strlen($xml["campoAliasTextoClaveForanea"])!=0)//	$campo->addAttribute("campoAliasTextoClaveForanea", $xml["campoAliasTextoClaveForanea"]);
					//echo "agregando campoAliasTextoClaveForanea<br>";
					*/
				}
			####################
			if(strcmp(strtoupper(strlen($xml["llaveForanea"])),"TRUE")==0){
				$campo->addAttribute("llaveForanea", "TRUE");
				if(strlen($xml["tablaClaveForanea"])!=0)	$campo->addAttribute("tablaClaveForanea", $xml["tablaClaveForanea"]);		
				if(strlen($xml["campoClaveForanea"])!=0)	$campo->addAttribute("campoClaveForanea", $xml["campoClaveForanea"]);
				if(strlen($xml["campoTextoClaveForanea"])!=0)	$campo->addAttribute("campoTextoClaveForanea", $xml["campoTextoClaveForanea"]);
				if(strlen($xml["campoAliasTextoClaveForanea"])!=0)	$campo->addAttribute("campoAliasTextoClaveForanea", $xml["campoAliasTextoClaveForanea"]);
			}
			
			
			if(strlen((string)$campo["unico"])>0){
				$campo["unico"]=$xml["unico"];
			}else{
				if(strcmp($xml["unico"], "true")==0)		$campo->addAttribute("unico", $xml["unico"]);		
			}
			
			//echo generalXML::geshiXML($campo);
			
			if(strlen((string)$campo["requerido"])>0){
				$campo["requerido"]=(string)$xml["requerido"];
			}else{
				if(strcmp($xml["requerido"], "")==0){
					$campo->addAttribute("requerido", "true");
				}else{
					$campo->addAttribute("requerido", $xml["requerido"]);
				}
			}
			if(strcmp($xml["listadoPrincipal"], "true")==0){
				$titulo=$this->lista->addChild("Titulo");
				$titulo->addAttribute("campo", $xml["nombre"]);
			}

			if(strcmp($xml["filtro"], "true")==0){
				####################
				#### DEPRECATED ####
				####################
				if(strcmp($xml["tipo"], "llaveForanea")==0){
					$lista=$this->filtros->addChild("Lista");
					$lista->addAttribute("nombreCampo", $xml["nombre"]);
					$lista->addAttribute("otraTabla", $xml["tablaClaveForanea"]);
					$lista->addAttribute("campoOtraTabla", $xml["campoClaveForanea"]);
					$lista->addAttribute("titulo", $xml["titulo"]);
				}else{
				####################
					if(strcmp(strtoupper(strlen($xml["llaveForanea"])),"TRUE")==0){
						$lista=$this->filtros->addChild("Lista");
						$lista->addAttribute("nombreCampo", $xml["nombre"]);
						$lista->addAttribute("otraTabla", $xml["tablaClaveForanea"]);
						$lista->addAttribute("campoOtraTabla", $xml["campoClaveForanea"]);
						$lista->addAttribute("titulo", $xml["titulo"]);
					}else{
						$lista=$this->filtros->addChild("Campo");
						$lista->addAttribute("nombreCampo", $xml["nombre"]);
						$lista->addAttribute("titulo", $xml["titulo"]);
					}
				}
			}
			//echo "<hr>XML Campo= ".$this->textoXML($campo)."<br>";
			//echo "XML Datos= ".$this->textoXML($this->datos)."<br>";
		}
		function agregarPropiedades($xml){
			$this->propiedades->addAttribute("tabla", $xml["nombre"]);
			$this->propiedades->addAttribute("tipo", $xml["tipo"]);
			$this->propiedades->addAttribute("titulo", $xml["titulo"]);
			//echo "XML Propiedad= [".$this->textoXML($this->propiedades)."]<br>";
		}
		function generarFormulario($opciones){
			$this->formulario=new SimpleXMLElement("<FormIMEC />");
			$this->propiedades=$this->formulario->addChild("Propiedades");
			$this->filtros=$this->formulario->addChild("Filtros");
			$this->lista=$this->formulario->addChild("Lista");
			foreach ($opciones as $i => $a){
				//if(strcmp($a, "true")==0){
					$this->lista->addAttribute($a,"true");
				//}
			}

			$this->datos=$this->formulario->addChild("Datos");

			$xmltmp=$this->xmlPrincipal->xpath('/Clase/Propiedades');
			$this->extraerNodo($xmltmp, "agregarPropiedades");


			$xmltmp=$this->xmlPrincipal->xpath('/Clase/Propiedades/Propiedad');
			$this->extraerNodo($xmltmp, "agregarCampoFiltros");
			
			
			return $this->formulario;

		}
	}


?>
