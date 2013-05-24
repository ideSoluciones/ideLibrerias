<?php

	require_once("ideSoluciones.XML.general.php");
	require_once("ideSoluciones.XML.SQL.php");
	
	
	/*
	Ejemplo de uso

			$ss= new Sesion($this->db, $idSesion);
			$ss->escribirParametro("nombreParametro", "valorParametro");
			$ss->escribirParametro("nombreParametro", "valorParametro1");
			$ss->escribirParametro("nombreParametro", "valorParametro2");
			$ss->escribirParametro("nombreParametro", "valorParametro3");
			echo "Sesion2:".$ss->imprimir();
			$ss->escribirParametro("nombreParametro", "");
			echo "Sesion3:".$ss->imprimir();
			$ss->escribirParametro("nombreParametro", "valorParametro");
			echo "<div>El valor de nombreParametro es: ".$ss->leerParametro("nombreParametro")."</div>";
	*/
	
	
	
	class Sesion extends generalXML{
	
	    static private $instancia = NULL;
	
	
		public $db = null;
		private $id = null;
		private $cadena = null;
		
		var $xml=null;
		var $idSesion=0;
		var $iframe=false;
		var $prefijoAnclas="";
		var $prefijoFormularios="";
		var $respuestaAjax=null;
		var $args=array();
		var $index=null;
		var $configuracion=null;
		
		static public function getInstancia($db=null, $id=null, $cadena="") {
			if (self::$instancia == NULL) {
				self::$instancia = new Sesion ($db, $id, $cadena);
			}
			return self::$instancia;
		}
		function getDB(){
			return $this->db;
		}
		function Sesion($db, $id, $cadena=""){
			if (is_null($db)){
				asercion("ERROR la base de datos no puede ser nula en la declaración de la Sesion");
			}
			$this->instancia=$this;
			$this->respuestaAjax=null;
			$this->stackFormularios=array();
			$this->db=$db;
			$this->prefijoFormularios=resolverPath()."/";
			$this->prefijoAnclas=resolverPath()."/";
				//new mensajes ("el id es: ".$id."<br>");

			$daoSesion = new DAO0Sesion($this->db);
			try{
				$voSesion=$daoSesion->getRegistro($id);
				//msg::add("Se recupera el registro");
				//msg::add($voSesion);
			}catch(sinResultados $e){
				//msg::add("Se crea una nueva sesion");
				$voSesion=$daoSesion->crearVO();
				$voSesion->setDatosSesion(base64_encode("<Sesion />"));
				$voSesion->setIdUsuario("1");
				$voSesion->setUltimoAcceso(date("Y-m-d H:i:s"));
				
				/*
				$tags = @get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.getRealIpAddr());
				//$tags = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress=190.25.106.251');
				if (strcmp($tags["known"], "true")==0){
					foreach($tags as $id => $tag){
						
						//echo mb_detect_encoding($tag), " ",
						//	iconv('UTF-8', 'ASCII//TRANSLIT', $tag), " ",
						//	iconv('UTF-8', 'ASCII//IGNORE', $tag), " ",
						//	iconv('UTF-8', 'ASCII', $tag), "<br>";
						
						
						$tags[$id]=iconv('UTF-8', 'ASCII//IGNORE', $tag);
					}
				
					$datos=xml::add(null, "DatosUsuario", array(
						"InformacionCliente"=>$_SERVER["HTTP_USER_AGENT"],
						"IP"=>getRealIpAddr(),
						"fechaConexion"=>date("Y-m-d H:i:s"),
						"Pais"=>$tags["country"],
						"Region"=>$tags["region"],
						"Ciudad"=>$tags["city"],
						"Latitud"=>$tags["latitude"],
						"Longitud"=>$tags["longitude"],
					));
				}else{
				*/
					if(isset($_SERVER["HTTP_USER_AGENT"])){
						$datos=xml::add(null, "DatosUsuario", array(
							"InformacionCliente"=>$_SERVER["HTTP_USER_AGENT"],
							"IP"=>getRealIpAddr(),
							"fechaConexion"=>date("Y-m-d H:i:s"),
						));
					}else{
						$datos=xml::add(null, "DatosUsuario", array(
							"InformacionCliente"=>"Cliente sin user agent",
							"IP"=>getRealIpAddr(),
							"fechaConexion"=>date("Y-m-d H:i:s"),
						));
					}
				//}
				//var_dump($tags);
				
				
				
				$voSesion->setDatosAcceso($datos->asXML());
				$daoSesion->agregarRegistro($voSesion);
			}
			
			$this->idSesion=$voSesion->getIdSesion();
			$this->xml= new SimpleXMLElement(base64_decode($voSesion->getDatosSesion()));
			
			$this->cadena=$cadena;
			$_SESSION['idSesion'.$cadena] = $this->idSesion;
			$_SESSION['nombreSesion'] = $cadena;
			$this->escribirParametro("identificadorSesion", $this->cadena.$this->idSesion);
			//var_dump($voSesion);
			
			self::$instancia=$this;
			///echo "* * * * * * * * Se cargo el valor de la sesion[".$this->idSesion."] : ".$this->textoXML($this->xml)."<br>";
		}
		function sincronizarBaseDatos(){
			$daoSesion = new DAO0Sesion($this->db);
			try{
				$voSesion=$daoSesion->getRegistro($this->idSesion);
				$idAnterior=intval($voSesion->getIdUsuario());
				$idNuevo=intval($this->leerParametro("idUsuario"));
				//echo $idAnterior." vs ".$idNuevo."<br>";
				
				if ($idNuevo==1){//Anonimo
					//echo "El idNuevo es UNO<br>";
					if ($idAnterior!=1){//Usuario -> anonimo, logout
						//echo "El idAnterior es NO UNO idSesion[".$_SESSION['idSesion'.$this->cadena]."]<br>";
						$_SESSION['idSesion'.$this->cadena]="0";
					}else{
					}
					
				}else{//Usuario
					$voSesion->setIdUsuario($idNuevo);
				}
				//echo generalXML::geshiXML($this->xml);
				$voSesion->setDatosSesion(base64_encode($this->xml->asXML()));
				$voSesion->setUltimoAcceso(date("Y-m-d H:i:s"));
				
				
				
				$daoSesion->actualizarRegistro($voSesion);
			}catch(sinResultados $e){
				msg::add("Error sincronizando sesión en la base de datos");
			}
			//echo "Sesión sincronizada";
			//var_dump($voSesion);
		}
		function buscarParametro($nombre){
			$c = $this->xml->xpath("/Sesion/Parametro[@nombre='$nombre']");
			//echo revisarArreglo($c, "Buscar parametro, vs ".$valor);
			if (count($c)>0){
			//foreach ($c as $i => $a){
				//echo "<div>Comparando valores [".$a["valor"].", ".$valor."]</div>";
				//$rest=$a["valor"];
				//if (strcmp($rest,$valor)==0){
					return true;
				//}
			}
			return false;
		}
		function buscarParametroInterno($nombre, $id){
			$c = $this->xml->xpath("/Sesion/Parametro[@nombre='$nombre']/Interno[@nombre='".$id."']");
			//echo revisarArreglo($c, "Buscar parametro c=".$nombre." ".$id);
			
			//if ($nombre=="casoUsoPermitido" && $id=="logout"){
			///	echo "Buscar parametro c=<strong>".$nombre." ".$id."</strong>: ";
			//	echo "xmlOriginal:".$this->geshiXML($this->xml)."<br>xmlBusqueda";
			//	var_dump($c);
			//	echo "<br>";
			//}
			if ($c==false)
				return false;
			if (count($c)>0){
				//echo "Como c>0 y c!=false<br>";
			//foreach ($c as $i => $a){
				//echo "<div>Comparando valores [".$a["valor"].", ".$valor."]</div>";
				//$rest=$a["valor"];
				//if (strcmp($rest,$valor)==0){
					return true;
				//}
			}
			return false;
		}
		function borrarParametros($cuantos='all'){
			$this->removeNode($this->xml, "/Sesion/Parametro", $cuantos);
		}
		function borrarParametro($nombre, $cuantos='one'){
			$this->removeNode($this->xml, "/Sesion/Parametro[@nombre='$nombre']", $cuantos);
		}
		function borrarParametroInterno($nombre, $id){
			$this->removeNode($this->xml, "/Sesion/Parametro[@nombre='$nombre']/Interno[@nombre='".$id."']", 'all');
		}
		function borrarParametrosInternos($nombre){
			$this->removeNode($this->xml, "/Sesion/Parametro[@nombre='$nombre']/Interno", 'all');
		}
		function leerParametro($nombre){
			$c = $this->xml->xpath("/Sesion/Parametro[@nombre='$nombre']");
			if (count($c)>0){
				$r=(string)$c[0]["valor"];
				//$r=unserialize((string)base64_decode((string)$c[0]["valor"]));
				//echo "Retornando valor :[".$r."]<br>";
				return $r;
			}
			return "";
		}
		function leerParametroInterno($nombre, $id){
			$c = $this->xml->xpath('/Sesion/Parametro[@nombre="'.$nombre.'"]/Interno[@nombre="'.$id.'"]');
			//new mensajes("<pre>$nombre, $id ,C=".print_r($c,true)."</pre>");
			if (count($c)>0){
				$r=(string)$c[0]["valor"];
				return $r;
			}
			return "";
		}
		function leerParametrosInternos($nombre){
			$c = $this->xml->xpath("/Sesion/Parametro[@nombre='$nombre']/Interno");
			if (count($c)>0)
				return $c;
			return array();
		}
		function escribirParametroInterno($nombre, $id, $valor){
			$respuesta=$this->buscarParametroInterno($nombre, $id);
			//echo "Estoy escribiendo un parametro interno: ",$nombre, " ", $id, " ", $valor." -> la respuesta de buscar es: [".$respuesta."]<br>";
			if($respuesta){
				//echo "La respuesta es que si existe: <br>";
				$this->removeNode($this->xml, "/Sesion/Parametro[@nombre='$nombre']/Interno[@nombre='".$id."']", 'all' );
				//echo "Despues de borrado se tiene ".$this->geshiXML($this->xml)."<hr>";
			}
			$c = $this->xml->xpath("/Sesion/Parametro[@nombre='$nombre']");

			if (count ($c)==0){
				$d=$this->escribirParametro($nombre, "", true);
			}else{
				$d=$c[0];
			}
			$parametro = $d->addChild('Interno');
			$parametro->addAttribute('nombre', $id);
			$parametro->addAttribute('valor', $valor);
			//if ($nombre=="casoUsoPermitido" && $id=="logout"){
			//	echo "El xml final es: ".$this->geshiXML($this->xml)."<br>";
			//}
		}
		function escribirParametro($nombre, $valor, $retornar=false, $debug=false){
			$valor2="".$valor;
			if($this->buscarParametro($nombre)){
				//echo "Se encontro un parametro";
				/*if ($debug)
					echo "el xml antes de: ".$this->geshiXML($this->xml)."<br>";
					*/
				$this->removeNode($this->xml, "/Sesion/Parametro[@nombre='".$nombre."']", 'all' );
				/*if ($debug)
					echo "el xml despues de: ".$this->geshiXML($this->xml)."<br>";
					*/
					/*
				$c = $this->xml->xpath("/Sesion/Parametro[@nombre='$nombre']");
				if ($valor!=""){
					//revisarArreglo($this->xml);
					$c[0]["valor"]=$valor;
					//revisarArreglo($this->xml);
				}else{
				}
				*/
			}//else{
				$parametro = $this->xml->addChild('Parametro');
				$parametro->addAttribute('nombre', $nombre);
				$parametro->addAttribute('valor', $valor2);
				if ($debug)
					echo "el xml al insertarlo: ".$this->geshiXML($this->xml)."<br>";
			//}
			//$this->sincronizarBaseDatos();
			if ($retornar)
				return $parametro;
		}
		function borrarParametrosDestino(){
			$destino=$this->leerParametro("destino");
			$this->removeNode($this->xml, "/Sesion/Parametro/ParametroDestino", 'all');
		}
		
		function buscarParametroDestino($destino, $nombre, $valor){
			$c = $this->xml->xpath("/Sesion/Parametro[@valor='".$destino."']/ParametroDestino[@nombre='$nombre']");
			//echo revisarArreglo($c, "Buscar parametro, vs ".$valor);
			foreach ($c as $i => $a){
				//echo "<div>Comparando valores [".$a["valor"].", ".$valor."]</div>";
				$rest=$a["valor"];
				if (strcmp($rest,$valor)==0){
					return true;
				}
			}
			return false;

		}
		function escribirParametroDestino($destino,$nombre, $valor, $sobreescribir=false){
			if($this->buscarParametroDestino($destino,$nombre, $valor) || $sobreescribir){
				$c = $this->xml->xpath("/Sesion/Parametro[@valor='$destino']/ParametroDestino[@nombre='$nombre']");
				if ($valor!=""){
					$c[0]["valor"]=$valor;
				}else{
					$this->removeNode($this->xml, "/Sesion/Parametro[@valor='$destino']/ParametroDestino[@nombre='$nombre']" );
				}
			}else{
			//echo $this->imprimir();
				$cadena="/Sesion/Parametro[@nombre='destino' and @valor='".$destino."']";
				$c=$this->xml->xpath($cadena);
				if (count($c)>0){
					$parametro = $c[0]->addChild('ParametroDestino');
					$parametro->addAttribute('nombre', $nombre);
					//@ToDo: Falta ajustar bien este problema de los utf8 en los parametros get
					/* Actualmente quedo funcionando así					
					UTF-8
					Valor: transaccion en validaci�n
					ASCII
					Valor: transaccion en validaci?
					*/
					//echo mb_detect_encoding($valor)."<br>";
					//echo "Valor: ".$valor."<br>";
					//echo mb_detect_encoding($valor)."<br>";
					//echo "Valor: ".$valor."<br>";
					@$parametro->addAttribute('valor', $valor);
				}
			}
		}
		function leerParametroDestino($destino, $nombre){
			$c=$this->xml->xpath("/Sesion/Parametro[@valor=".$destino."]/ParametroDestino[@nombre='".$nombre."']");
			//var_dump($c);
			if (count($c)>0){
				$r=(string)$c[0]["valor"];
				return $r;
			}
			return "";
		}
		function leerParametroDestinoActual($nombre){
			$destino=$this->leerParametro("destino");
			return $this->leerParametroDestino($destino, $nombre);
		}
		//Retorna un arreglo de SimpleXMLElement
		function leerParametrosDestinoActual($patron=""){
			$destino=$this->leerParametro("destino");
			$fs=$this->xml->xpath("/Sesion/Parametro[@nombre='destino' and @valor='".$destino."']/ParametroDestino");
			if (strcmp($patron, "")==0){
				if (count($fs)>0){
					return $fs;
				}
				return "";
			}else{
				$respuesta = array();
				if(is_array($fs)){
					foreach($fs as $f){
						if (preg_match($patron, (string)$f["nombre"])){
							$respuesta[(string)$f["nombre"]]=(string)$f["valor"];
						}
					}
				}
				return $respuesta;
			}
			
			
			
			
			
			
		}
		function buscarAncla($casoUso){
			$c = $this->xml->xpath("/Sesion/Ancla[@casoUso='$casoUso']");
			if (count($c)>0)
				return true;
			return false;
		}
		function borrarAnclas(){
			$this->removeNode($this->xml, "/Sesion/Ancla", "all");
			//$this->sincronizarBaseDatos();
		}
		function leerAncla($idAncla){
			$c = $this->xml->xpath("/Sesion/Ancla[@idCasoUso='$idAncla']");
			if (count($c)>0)
				return $c[0];
			return null;
		}
		function escribirAncla($casoUso, $idCasoUso){
			return $casoUso;			
		}
		function getIdSesion(){
			return $this->idSesion;
		}
		function leerFormulario($idCasoUso){
			$fs = $this->xml->xpath("/Sesion/Formulario[@idCasoUso='".$idCasoUso."']");
			if (count($fs)>0){
				return $fs[0];
			}
			return null;
		}
		function borrarFormularios($idCasoUso){
			///echo "*borrarFormularios<br>";
			if($idCasoUso==0){//todos 
				$this->removeNode($this->xml, "/Sesion/Formulario", "all");
			}else{//borrar solo los del caso de uso actual
				//$this->removeNode($this->xml, "/Sesion/Formulario", "all");

				//$this->removeNode($this->xml,"/Sesion/Formulario[@idCasoUso='".$idCasoUso."']");
				//	new Mensajes (revisarArreglo($idCasoUso,'fabio "c"'));
				$fs = $this->xml->xpath("/Sesion/Formulario");	
				if ($fs==false){
					$fs=array();
				}
				foreach($fs as $a => $b){
					//new Mensajes (revisarArreglo($a,'formularios "a"'));
					//new Mensajes (revisarArreglo($b,'formularios "b"'));
					foreach($b->attributes() as $c => $d){					
						if (strcmp($c, "idCasoUso")==0){
							//new Mensajes (revisarArreglo($c,'atributos "c"['.$d."]"));
							if (strcmp($d, "". $idCasoUso)==0){
							//	new Mensajes (revisarArreglo($c,'----****--- "c"['.$d."]"));
								unset($b[0]);
								break;
						
							}
						}
					}
				}
			}
			//$this->sincronizarBaseDatos();
		}
		function borrarFormulario($idCasoUso, $aux){
			///echo "***borrarFormulario<br>";
			$this->removeNode($this->xml,"/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$aux."']");

		}
		function buscarFormulario($idCasoUso, $aux){
			//echo $this->geshiXML($this->xml);
			//echo "/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$aux."']<br>";
			$fs = $this->xml->xpath("/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$aux."']");
			//echo "buscarFormulario idCasoUso=>$idCasoUso, aux=>$aux".revisarArreglo($fs, "fs de la busqueda")."<hr>";
			//var_dump($fs);
			if (count($fs)>0){
				//echo "true<hr>";
				return true;
			}
			//echo "false<hr>";
			return false;
		}
		//<adf nombre="adf" valorPorDefecto="asdfasd">XXX</adf>
		function procesarCampoOcultosFormulario($campo, $formularioSesion){
			$campoOculto=$formularioSesion->addChild("Parametro");
			$campoOculto->addAttribute("nombre", $campo["nombre"]);
			$campoOculto->addAttribute("valor", $campo["valorPorDefecto"]);
			
		}
		//Determina el numero para un nuevo destino entre anclas y formularios
		function NuevoDestino(){
			$NumeroDestinos=intval($this->leerParametro("NumeroDestinos"));
			$NumeroDestinos++;
			$this->escribirParametro("NumeroDestinos", $NumeroDestinos);
			return $NumeroDestinos;
			/*
			$fs = $this->xml->xpath("/Sesion/Formulario");	
			$an = $this->xml->xpath("/Sesion/Ancla");
			if ($an==false){
				$an=array();
			}
			if ($fs==false){
				$fs=array();
			}
			$ocupados=array();
//$r=0;
			//new Mensajes (revisarArreglo($fs,'formularios "Inicio"'));
			foreach($fs as $a => $b){
				//new Mensajes (revisarArreglo($a,'formularios "a"'));
				//new Mensajes (revisarArreglo($b,'formularios "b"'));
				foreach($b->attributes() as $c => $d){
					//new Mensajes (revisarArreglo($c,'atributos "c"'));
					if (strcmp($c, "idForm")==0){
//						new Mensajes (revisarArreglo('['.$d.']'));
//						new Mensajes ('['.$d."][".($d+1)."]");
						$ocupados[$d+0]=1;
//$r++;
					}
				}
			}


			foreach($an as $a => $b){
				//new Mensajes (revisarArreglo($a,'formularios "a"'));
//				new Mensajes (revisarArreglo($b,'formularios "b"'));
				foreach($b->attributes() as $c => $d){
				//	new Mensajes (revisarArreglo($c,'atributos "c"'));
					if (strcmp($c, "idAncla")==0){
//						new Mensajes (revisarArreglo('['.$d.']'));
//						new Mensajes ('['.$d."][".($d+0)."]");

//$r++;
						$ocupados[$d+0]=1;
					}
				}
			}

			for($i=1;$i<10000;$i++){
				if(!isset($ocupados[$i])){
					return $i;
				}
			}
			return "NoEncontroDisponible";
			*/

		}
		//Guarda el contenidoxml generado por los casos de uso en base64_encode
		function agregarXMLContenido($contenido){
			$this->removeNode($this->xml, "/Sesion/xmlContenido", 'all');
			$XMLcontenido=$this->xml->addChild("xmlContenido");
			$XMLcontenido[]=base64_encode($contenido->asXML());
		}
		//Recupera el contenidoxml de la recarga anterior
		function recuperarXMLContenido(){
			$codigo=base64_decode($this->xml->xmlContenido[0]);
			return new SimpleXMLElement($codigo);
		}
		//Agrega un formulario a la sesión para poderlo consultar en el proceso de los casos de usos
		function agregarFormulario($formulario, $nombre, $contenido){
			$campoIdCasoUso = $formulario->xpath("Propiedad[@nombre='idCasoUso']");
			$campoNombreCasoUso = $formulario->xpath("Propiedad[@nombre='nombreCasoUso']");
			
			if (count($campoIdCasoUso)<=0 && count($campoNombreCasoUso)<=0)
				throw new formularioInvalidoException("No se tiene el parametro idCasoUso como propiedad en el formulario. ".$this->textoXML($formulario));

	    
		    //var_dump($campoIdCasoUso[0]["valor"]);
		    //var_dump($campoNombreCasoUso);

			$accionComplemento = $formulario->xpath("Propiedad[@nombre='accion']");
			$textoAccionComplemento="";
			if (count($accionComplemento)>0){
				$textoAccionComplemento=$accionComplemento[0]["valor"];
			}
			//echo revisarArreglo($accionComplemento, "accionComplemento");
			//echo "La accion complemento [".count($accionComplemento)."] es:".$textoAccionComplemento."<br>";
			//$campoCasoUso = $formulario->xpath("Propiedad[@nombre='idCasoUso']");
			if (count($campoIdCasoUso)>=1){
				$idCasoUso=$campoIdCasoUso[0]["valor"];
				$nombreCasoUso=Control0CasoUso::getNombreCasoUso($idCasoUso);
			}

			if (count($campoNombreCasoUso)>=1){
				$nombreCasoUso=$campoNombreCasoUso[0]["valor"];
				$idCasoUso=Control0CasoUso::getIdCasoUso($nombreCasoUso);
			}

			$total=$this->NuevoDestino();
			///echo "->AgregarFormulario::el nuevo destino es: ".$total."".$this->textoXML($formulario)."<br>";
			$formularioSesion=$this->xml->addChild('Formulario');
			$propiedad=$formulario->addChild('Propiedad');			
			$propiedad->addAttribute("nombre", "Accion");
			//$propiedad->addAttribute("valor", $this->prefijoFormularios.$this->idSesion."/".$total);
			//$propiedad->addAttribute("valor", $this->prefijoFormularios.$campoIdCasoUso[0]["valor"]."/".$total);
			/*if ($_SERVER['REMOTE_ADDR']=="190.24.69.172"){
			    //var_dump($total);
			    //var_dump($nombre);
			    if ($nombre=="1"){
			         throw new Exception("auchhh");
			    }
    		}*/
			$propiedad->addAttribute("valor", $this->prefijoFormularios.$nombre."/".$total.$textoAccionComplemento);
			$propiedadIdForm=$formulario->addChild('Propiedad');
			$propiedadIdForm->addAttribute("nombre", "idForm");
			$propiedadIdForm->addAttribute("valor", $total);

			//new mensajes(revisarArreglo($formulario));
			
			
			//new mensajes($this->geshiXML($formulario));
			//$formularioSesion->addAttribute("idFormulario", $idCasoUso);
			$formularioSesion->addAttribute("idCasoUso", $idCasoUso);
			$formularioSesion->addAttribute("idForm", $total);
			//$xmlContenido=$formularioSesion->addChild("xml");
			//$xmlContenido=$xmlContenido->addChild("Contenido");
			// Esta linea la quite para evitar los duplicados en el xml de la sesion
			//append_simplexml($formularioSesion, $formulario);
			//append_simplexml($xmlContenido, $contenido);
			//
			foreach ($formulario as $elemento){
				if (strcmp($elemento->getName(), "Propiedad")==0){
					$p=$formularioSesion->addChild("Propiedad");
					$p->addAttribute("nombre", $elemento["nombre"]);
					$p->addAttribute("valor", $elemento["valor"]);
				}
			}

			//registrarlog("Formulario de la sesión: ".$formularioSesion->asXml());
			$camposOcultos = $formulario->xpath("Campo[@tipo='oculto']");
			$this->extraerNodo1p($camposOcultos, "procesarCampoOcultosFormulario", $formularioSesion);
			//$this->sincronizarBaseDatos();			
			//new mensajes("Formulario:".$this->geshiXML($formularioSesion));
			//new mensajes("Formulario:".$this->geshiXML($formulario));
		}
		function agregarParametroFormulario($idCasoUso, $idForm, $nombre, $valor){
			$fs = $this->xml->xpath("/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$idForm."']");
			if (count($fs)>0){
				$propiedad=$fs[0]->addChild('Parametro');		
				$propiedad->addAttribute("nombre", $nombre);
				$propiedad->addAttribute("valor", $valor);
			}
		}
		function getFormularioActual(){
			$idCasoUso=$this->leerParametro("destino");
			$idForm=$this->leerParametro("destinoAux");
			$fs = $this->xml->xpath("/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$idForm."']");
			if (count($fs)>0)
				return $fs[0];
			return "";
		}
		function borrarFormularioActual(){
			$idCasoUso=$this->leerParametro("destino");
			$idForm=$this->leerParametro("destinoAux");
			//echo "borrarFormularioActual1<br>";
			generalXML::removeNode($this->xml,"/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$idForm."']", "all");
			$this->borrarParametrosDestino();
			generalXML::removeNode($this->xml,"/Sesion/Parametro[@nombre='destino' and valor='".$idCasoUso."']", "all");
			generalXML::removeNode($this->xml,"/Sesion/Parametro[@nombre='destinoAux' and valor='".$idForm."']", "all");

			//echo "borrarFormularioActual2<br>";
		}
		function leerParametrosFormularioActual($patron="", $parametro="Parametro"){
			$idCasoUso=$this->leerParametro("destino");
			$idForm=$this->leerParametro("destinoAux");
			$fs = $this->xml->xpath("/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$idForm."']/".$parametro);
			if (strcmp($patron, "")==0){
				if (count($fs)>0){
					return $fs;
				}
				return "";
			}else{
				$respuesta = array();
				if(is_array($fs)){
					foreach($fs as $f){
						if (preg_match($patron, (string)$f["nombre"])){
							$respuesta[(string)$f["nombre"]]=(string)$f["valor"];
						}
					}
				}
				return $respuesta;
			}
		}
		function leerParametroFormulario($idCasoUso, $idForm, $nombre, $parametro="Parametro"){
			$fs = $this->xml->xpath("/Sesion/Formulario[@idCasoUso='".$idCasoUso."' and @idForm='".$idForm."']/".$parametro."[@nombre='$nombre']");
			$r="";
			if (count($fs)>0)
				$r=(string)$fs[0]["valor"];
				return $r;
			return "";
		}
		function leerParametroFormularioActual($nombre, $parametro="Parametro"){
			return $this->leerParametroFormulario($this->leerParametro("destino"), $this->leerParametro("destinoAux"), $nombre, $parametro);
		}
		function imprimir(){
			return $this->textoXML($this->xml);
		}
		function asXML(){
			return $this->xml->asXML();
		}
	}
?>
