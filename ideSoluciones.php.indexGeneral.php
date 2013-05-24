<?php
$GLOBALS["debug"]=0;
define("CON_XSLT",false);


/*
Nota, si se tiene problema con el id de la sesion
para borrarla ejecute:
unset($_SESSION['idSesion'.$configtemp->titulo]);


*/



/*
 //@todo reorganizar el flujo de trabajo
 1. El usuario hace la solicitud de la pagina a una interfaz
 2. Esta envía a la parte de control la solicitud separando la
 información de la solicitud, como el documento, formulario o pagina
 que solicita.
 3. El sistema verifica la información del usuario e información de sesión
 4. Con la información del usuario se determinan los roles
 5. Se determinan los casos de uso a los que puede acceder de pendiendo
 su id de usuario
 6. Se determinan los casos de uso a los que puede acceder de pendiendo
 sus roles
 7. Se hace una mezcla de información para generar la información de los
 casos de uso y la información de los menús y bloques, a los que puede
 acceder.
 8. Se determina si el usuario tiene acceso al modulo y al caso de uso al
 que esta haciendo el pedido
 1. En el caso de que no exista un modulo o no tenga acceso a el,
 el sistema debe generar como contenido un mensaje de acceso
 no autorizado o que no existe el modulo al que se quiere
 acceder
 2. En el caso de que exista y este autorizado determina si es
 una petición de contenido
 1. Si es una petición de contenido llama al caso de uso
 Pedido Contenido, con los parámetros de la petición
 2. Si es una petición de formulario llama al caso de uso
 Pedido Formulario, con los parámetros del formulario
 9. Recibe el resultado
 10. Consulta de las cosas que toca incluir como estilos y librerias (ej:
 js)
 11. Se consulta los elementos del menú de cada uno de los módulos
 12. Se consulta los bloques de cada uno de los módulos
 13. Con la información de contenido, menús, y bloques se genera la
 pagina de acuerdo al tema
 14. Se retorna la pagina generada al usuario



 Notas:
 Crear la clase controlPaquete,


 */
 
//La quito por que no se esta utilizando
//require_once('../Externos/xajax/xajax_core/xajax.inc.php');
//Solo incluir esta libreria si es necesario
include_once("../Externos/geshi/geshi.php");

require_once("ideSoluciones.configuracionGeneral.php");

require_once("ideSoluciones.php.general.php");
require_once("ideSoluciones.XML.general.php");
require_once("ideSoluciones.XML.SQL.php");
require_once("ideSoluciones.php.excepciones.php");

require_once("ideSoluciones.XML.XMLClase2XMLFormulario.php");

require_once("ideSoluciones.XML.procesadorAjax.php");
require_once("ideSoluciones.php.validarTipo.php");
require_once("ideSoluciones.php.sesion.php");


//IMEC
//require_once("ideSoluciones.XML.IMEC.php");


cargarLibreriasDirectorio("../Librerias/ideComponentes");
cargarLibreriasDirectorio("../Librerias/Generales");


require_once("ideSoluciones.php.tema.php");


// Prevenir XSS
if (isset($_REQUEST['_SESSION'])){
    echo "Buen intento <meta http-equiv='Refresh' CONTENT=\"0; URL='".resolverPath()."'\" />";
    exit();
}

if($GLOBALS["debug"]>0){ registrarlog("<h1>(".date("D M j G:i:s T Y").") Debug nivel ".$GLOBALS["debug"]."</h1>"); }

class IndexGeneral extends generalXML{
	var $db=null;
	var $sesion=null;

	var $menu=null;
	var $contenido=null;
	var $bloque=null;

	var $controlCasoUsoUsuario=null;
	var $controlCasoUso=null;
	var $controlCasoUsoRol=null;

	var $paquete=null;
	var $tema=null;
	var $config=null;

	function IndexGeneral($db){
		global $tiempoInicial;
		$tiempoInicial=tick();
		setlocale(LC_ALL,"es_CO.utf8","es_ES@euro","es_ES","esp","es_CO","es"); 
		putenv("TZ=America/Bogota");
		//echo date("l jS \of F Y h:i:s A");

		$this->db=$db;
		$this->determinarSesion();

	
		$this->controlUsuarioRol=new Control0UsuarioRol($this->db);
		$this->controlUsuarioCasoUso=new Control0UsuarioCasoUso($this->db);
		$this->controlRolCasoUso=new Control0RolCasoUso($this->db);
		$this->controlCasoUso= new Control0CasoUso($this->db);



		$this->paqueteUsuario= new PaqueteUsuario($this->db);
		$this->tema = new SimpleXMLElement("<Tema />");
		$this->config=$this->tema->addChild("Config");
		$this->config->addAttribute("base", resolverPath()."/");
		$this->config->addAttribute("dirTransformacionesContenidosGenericos", resolverPath()."/../Librerias/xsl/transformacionesContenidosGenericos.xsl");
		$this->menu=$this->tema->addChild("Menu");
		$this->menu->addAttribute("nombre", "menuPrincipal");
		$this->contenido=$this->tema->addChild("Contenido");
		$this->bloque=$this->tema->addChild("Bloque");


	}
	/*
	function separarParametrosSolicitudSendAndLoad($pedido){
		foreach ($pedido as $i => $a){
			echo $i." - ".$a;
			echo "\n".$pedido[$i]->asXML();
		}
	}*/
	/*
	function separarParametrosSolicitudAjax($lista_args){
		$this->parametrosSolicitud=new SimpleXMLElement("<xmlPedido />");
		$pedido=split("/", $lista_args[0]);
		$this->sesion= new Sesion($this->db, $_SESSION['idSesion']);
		$this->sesion->borrarParametro("destino");
		$this->sesion->borrarParametro("destinoAux");
		$this->sesion->escribirParametro("destino", $pedido[0]);
		if (isset($pedido[1]))
			$this->sesion->escribirParametro("destinoAux", $pedido[1]);
		else
			$this->sesion->escribirParametro("destinoAux", "");
		for (
			$i=1;
			$i<count($lista_args)-1;
			$i=$i+2){

			if(strcmp($lista_args[$i],"q")!=0){
				$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $lista_args[$i], $lista_args[$i+1]);
			}
		}
	}*/
	function determinarSesion(){

		if(class_exists("ConfiguracionLocal")){
			$configtemp= new ConfiguracionLocal();
		}else{
			$configtemp= new ConfiguracionGeneral();
		}

		if (isset($_SESSION['idSesion'.$configtemp->titulo])){
			$this->sesion= Sesion::getInstancia($this->db, $_SESSION['idSesion'.$configtemp->titulo], $configtemp->titulo);
		}else{
			//echo "NO se tiene sesion\n";
			$this->sesion= Sesion::getInstancia($this->db, 0, $configtemp->titulo);
		}
		
		

		$this->sesion->configuracion=$configtemp;
		//Se agregan los parametros de configuracion del proyecto a: la sesion de php y a la sesion del proyecto
		$this->sesion->configuracion->pathCliente=resolverPath();
		$arrayPathCliente=explode("/", resolverPath());
		$this->sesion->configuracion->pathServidor=realpath(dirname(__FILE__)."/../".$arrayPathCliente[count($arrayPathCliente)-1]);
		$this->sesion->configuracion->pathProyectos=realpath(dirname(__FILE__)."/../");
		$configtemp->pathCliente=resolverPath();
		$configtemp->pathServidor=realpath(dirname(__FILE__)."/../".$arrayPathCliente[count($arrayPathCliente)-1]);

		if (strcmp($configtemp->pathServidor, "")==0){
			$partes=explode("/", $_SERVER['SCRIPT_FILENAME']);
			//var_dump($partes);
			//echo count($partes);
			unset($partes[count($partes)-1]);
			unset($partes[count($partes)-1]);
			$partes[]=$arrayPathCliente[count($arrayPathCliente)-1];
			unset($partes[0]);
			//var_dump($partes);
			$configtemp->pathServidor="/".implode("/",$partes);
			//var_dump($configtemp);
		}
		
		$this->sesion->escribirParametro("direccionBase", "http://".$_SERVER["HTTP_HOST"]);
		$this->sesion->escribirParametro("direccionCompleta", "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
		$this->sesion->escribirParametro("pathCliente", resolverPath());
		$this->sesion->escribirParametro("pathServidor", $this->sesion->configuracion->pathServidor);
		$this->sesion->escribirParametro("pathProyectos", $this->sesion->configuracion->pathProyectos);

		$arregloPaths=array(
				"direccionBase"   =>  "http://".$_SERVER["HTTP_HOST"],
				"direccionCompleta"   =>  "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],
				"pathCliente"   => resolverPath(),
				"pathServidor"  => $this->sesion->configuracion->pathServidor,
				"pathProyectos" => $this->sesion->configuracion->pathProyectos,
				"AjaXplorer_ideProyecto" => ""
			);
		$_SESSION['AjaXplorer_ideProyecto']='';

		$_SESSION['paths']=$arregloPaths;
		



		if( strcmp($this->sesion->leerParametro('fin'.$this->sesion->configuracion->titulo),"")!=0 ){
			// Está setteado el parametro de expiración de sesión
			if( $this->sesion->leerParametro('fin'.$this->sesion->configuracion->titulo) < time()){
				// El tiempo de fin de sesión es menor que la hora actual
				$_SESSION["idSesion".$this->sesion->configuracion->titulo]=0;
				session_destroy();
				header("location: ".resolverPath());
			}else{
				// Se renderizó de nuevo la página y se debe renovar el tiempo de fin de sesión
				$this->sesion->escribirParametro('fin'.$this->sesion->configuracion->titulo , time()+60*$this->sesion->leerParametro('timeout'.$configtemp->titulo));
			}
		}else{
			// NO está setteado el parametro de expiración de sesión
			$this->sesion->escribirParametro('fin'.$this->sesion->configuracion->titulo , "");
		}
		
	}
	function separarParametrosSolicitud(){
		$a="";
		if (isset($_POST["q"])){
			$a=$_POST["q"];
		}
		if (isset($_GET["q"])){
			$a=$_GET["q"];
		}
	

		$pedido=split("/", $a);
		if (count($pedido)<2){
			$pedido=split("%2F", $a);
		}

		$pedidoOriginal=$pedido[0];

		/*echo "q=".$a.", ";
		echo "q1=".$pedido[0].", ";

		echo "Pedido: ".$pedido[0]."<br>";
		echo "intval(pedido[0])=".intval($pedido[0])."<br>";*/
		//echo "pedido[0]=".$pedido[0]."<br>";

		//echo "Verificando el caso de uso a trabajar<br>";
		if (strcmp($pedido[0], "")==0){
			//echo "No se tiene caso de uso: <br>";
			$pedido[0]=$this->controlCasoUso->getIdCasoUso($this->sesion->configuracion->destinoDefecto);
			$pedido[1]=$this->sesion->configuracion->destinoAuxDefecto;
		}else if (!is_numeric($pedido[0])){
			//echo "No es un numero, se analiza el caso de uso<br>";
			//echo "Entro al if: [".$this->controlCasoUso->getIdCasoUso($pedido[0])."]";
			$pedido[0]=$this->controlCasoUso->getIdCasoUso($pedido[0]);
			///echo "No era entero pero ahora es: ".$pedido[0];
		}
		//echo "ya termino el if.<br>siendo el pedido: ".$pedido[0];
		for($i=0;$i<count($pedido);$i++){
			$this->sesion->args[$i]=$pedido[$i];
		}

		$this->sesion->borrarParametro("nombreCasoUso");
		$this->sesion->borrarParametro("destino");
		$this->sesion->borrarParametro("destinoAux");
		$this->sesion->borrarParametrosDestino();





		//echo "El destino es:[".$pedido[0].", ".$pedido[1]."]<br>";

		$this->sesion->escribirParametro("idCasoUso", $pedido[0]);
		$this->sesion->escribirParametro("nombreCasoUso", $this->controlCasoUso->getNombreCasoUso($pedido[0]));
		/*
		 * Parches para funcionamiento de modulos inc y Administrador de archivos
		 * ver como mover esto de aca
		 */
		if (strcmp($pedidoOriginal, "inc")==0){
			$this->sesion->borrarParametro("nombreCasoUso");
			$this->sesion->escribirParametro("nombreCasoUso", "inc");
		}





		$this->sesion->escribirParametro("destino", $pedido[0]);
		if (isset($pedido[1])){
			///echo "q2=".$pedido[1]."<br>";
			$this->sesion->escribirParametro("destinoAux", $pedido[1]);
		}else
			$this->sesion->escribirParametro("destinoAux", "");

		//echo "Los parametros son [".$this->sesion->leerParametro("destino").",".$this->sesion->leerParametro("destinoAux")."]<br>";

		//echo "Sesion recuperada es:".$this->textoXML($this->sesion->xml)."<br>";
		///echo "La dirección del script es: ".resolverPath();



		if (strcmp($this->sesion->leerParametro("destino"), "")!=0){
			foreach ($_COOKIE as $i=>$a){
			//echo "/*Analizando parametros cookie: ".$i.", ".$a."<br>";
				if(strcmp($i,"q")!=0){
					if (is_array($a)){
						foreach($a as $subi => $suba){
							$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"),$i."_".$subi, $suba);
							$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i."_".$subi, $suba);
						}
					}else{
						if (strcmp($i, "PHPSESSID")!=0){
							$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"),$i, $a);
							$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i, $a);
						}
					}
				}
			
				setcookie($i, "", time()-1,$this->sesion->leerParametro("pathCliente"));
			}
		//echo "/*".$this->sesion->leerParametro("destino").generalXML::geshiXML($this->sesion->xml)."";
			foreach ($_POST as $i=>$a){
				///echo "Analizando parametros post: ".$i.", ".$a."<br>";
				if(strcmp($i,"q")!=0){
					if (is_array($a)){
						foreach($a as $subi => $suba){
							$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"),$i."_".$subi, $suba);
							$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i."_".$subi, $suba);
						}
					}else{
						$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"),$i, $a);
						$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i, $a);
					}
				}
			}
			foreach ($_GET as $i=>$a){
			
			
				if(strcmp($i,"q")!=0){
					if (is_array($a)){
						foreach($a as $subi => $suba){
							$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"),$i."_".$subi, $suba);
							$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i."_".$subi, $suba);
						}
					}else{

						//echo mb_detect_encoding($a)." - ".$a."<br>";
						if (strcmp(mb_detect_encoding($a), "UTF-8")==0){
							//$a=utf8_decode($a);
							@$a=iconv('UTF-8', 'ASCII//IGNORE', $a);
						}
						//echo mb_detect_encoding($a)." - ".$a."<br>";
						
						$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"), $i, $a);
						$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i, $a);
					}
				}
			}
			if (isset($_FILES)){
				foreach ($_FILES as $i=>$a){
					if(strcmp($i, "q")!=0){
						$this->sesion->escribirParametroDestino($this->sesion->leerParametro("destino"), $i, $a["tmp_name"]);
						$this->sesion->agregarParametroFormulario($this->sesion->leerParametro("destino"), $this->sesion->leerParametro("destinoAux"), $i, $a["tmp_name"]);
					}
				}
			}
		//echo "Sesion mas las variables de los formularios: ".$this->textoXML($this->sesion->xml)."\n\n";
		}

	}

	function consultarInformacionUsuario(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::consultarInformacionUsuario()<br>"); }
		if ($this->sesion->leerParametro("idUsuario")==""){
			$this->sesion->escribirParametro("idUsuario", "1");
		}
	}
	function mezclaCasosUso(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::mezclaCasosUso()<br>"); }
		/**
		* @todo
		* Falta implemtentar la relación de prioridades entre permisos de rol y permisos de usuario
		* En este momento los casos de uso que tenga el usuario se muestran al final
		*/
		$casos = $this->sesion->xml->xpath("/Sesion/Parametro[@nombre='casoUsoRol']/Interno");
		//echo revisarArreglo($casos, "Casos de uso Rol");
		if ($casos){
			foreach ($casos as $i => $caso){
				//echo "Vamos ahora a buscar ", $caso['nombre'];
				//if (!$this->sesion->buscarParametroInterno("casoUsoPermitido", $caso["nombre"])){
					$this->sesion->escribirParametroInterno("casoUsoPermitido", $caso["nombre"], $caso["valor"]);
				//}
			}
		}
		$casos = $this->sesion->xml->xpath("/Sesion/Parametro[@nombre='casoUsoUsuario']/Interno");
		//echo revisarArreglo($casos, "Casos de uso Usuario");
		if ($casos){
			foreach ($casos as $i => $caso){
				//echo "Vamos ahora a buscar ", $caso['nombre'],"<br>";
				//if (!$this->sesion->buscarParametroInterno("casoUsoPermitido", $caso["nombre"])){
					$this->sesion->escribirParametroInterno("casoUsoPermitido", $caso["nombre"], $caso["valor"]);
				//}
			}
		}
	}
	function incluirEstilos(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::incluirEstilos()<br>"); }
	}
	function incluirLibrerias(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::incluirLibrerias()<br>"); }
	}
	function hipervinculo($dato){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::hipervinculo()<br>"); }
		return "<a href='".$dato["destino"]."' >".$dato["nombre"]."</a>";
	}

	function buscarElementoMenu($menu, $nuevoPath, $i=0){
		//echo "Buscando elemento menu para [".$nuevoPath." en ".$menu->getName()."], nivel=".$i."<br>";
		$direccion=explode("/", $nuevoPath);
		$cuentaDireccion=count($direccion);
		foreach($menu->children() as $hijo){
			//echo "analizando <b>".$hijo["nombre"]."</b><br>";

			if (strcmp($hijo["nombre"], $direccion[$i])==0){
				if ($cuentaDireccion-1==($i+1)){
					//echo "#Se encontro un padre comun [".($cuentaDireccion-1).", ".($i+1)."]<br>";
					return array("menu"=>$hijo, "nivel"=>$i+1);
				}
				if (count($hijo->children())>0){
					//echo "Se tienen hijos[".$hijo["nombre"]."], se analiza siguiente nivel<br>";
					$respuestaHijo=$this->buscarElementoMenu($hijo, $nuevoPath, $i+1);
					if (!is_null($respuestaHijo)){
						return $respuestaHijo;
					}
				}

			}

			/*

			$direccionHijo=explode("/",$hijo["nombre"]);
			echo "-> * Confirmando ".$hijo["nombre"].", <b>[[".$direccionHijo[$i].", ".$direccion[$i]."]]</b><br>";
			echo "-> * ConfirmandoP".$hijo["nombre"].", <b>[[".$direccionHijo[$i-1].", ".$direccion[$i-1]."]]</b><br>";
			echo (count($direccion)), " vs ", $i, " <- ".$nuevoPath."<br>";
			if (strcmp($direccionHijo[$i-1], $direccion[$i-1])==0 && strcmp($direccion[$i-1], "")!=0){
				return array("menu"=>$menu, "nivel"=>$i);
			}*/
		}
		if($i>0){
			if (strcmp($menu["nombre"], $direccion[$i-1])==0){
				//echo "Yo ni mis hermanos servimos pero <b>mi padre si</b><br>";
				return array("menu"=>$menu, "nivel"=>$i);
			}
		}
		return null;
	}

	function agregarMenu($link, $destino){
		//echo "Agregando menu ".$link." a: ".$destino."<br>";

		$direccion=explode("/", $link);
		if (count($direccion)>1){
			//echo revisarArreglo($direccion);
			$respuesta=$this->buscarElementoMenu($this->menu, $link);
			$padre=$respuesta["menu"];
			$nivel=$respuesta["nivel"];
			$parametros=explode("/",$padre);
			if (is_null($padre)){
				//echo revisarArreglo($direccion, "dir ".($nivel+1)." [".$direccion[0+$nivel]."]");
				$nuevoMenu=$this->menu;
				for ($i=0;$i<count($direccion);$i++){
					//echo "<b>Agregando en Nuevo padre ".$link." [".$i.", ".count($direccion)."]</b><br>";
					$nuevoMenu=$nuevoMenu->addChild("Campo");
					$nuevoMenu->addAttribute("nombre", $direccion[$i]);
					if (($i+1)==count($direccion)){
						$nuevoMenu->addAttribute("destino", $this->sesion->prefijoAnclas.$destino);
					}else{
						$nuevoMenu->addAttribute("destino", "#");
					}
				}
			}else{
				//echo "<b>Agregando en padre ".$padre["nombre"]."</b><br>";
				$campo=$padre->addChild("Campo");
				$campo->addAttribute("nombre", $direccion[$nivel]);
				$campo->addAttribute("destino", $this->sesion->prefijoAnclas.$destino);
				//$subMenu=$padre->addChild("SubMenu");
				//$subMenu->addAttribute("nombre", $direccion[0+$nivel]);
			}
		}else{
			$campo=$this->menu->addChild("Campo");
			$campo->addAttribute("nombre", $link);
			$campo->addAttribute("destino", $this->sesion->prefijoAnclas.$destino);
		}
		//echo "Agregado <b>$link</b><br>";
		//echo "<hr>";
	}
	function cargarMenus(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::cargarMenus()<br>"); }
		$casos = $this->sesion->xml->xpath("/Sesion/Parametro[@nombre='casoUsoPermitido']/Interno");
		//echo "Los casos permitidos son : ";
		//var_dump($casos);
		//echo "La sesion es: ";
		//echo $this->geshiXML($this->sesion->xml);
		if ($casos){
			foreach ($casos as $i => $caso){

				$nombrePaquete="Paquete".Control0CasoUso::getNombrePaquete($this->db, $caso["valor"]);
				if (class_exists($nombrePaquete)){
					$paqueteGenerico= new $nombrePaquete($this->db);
					$menus=$paqueteGenerico->elementosMenu($this->sesion, $caso["valor"]);

					$menu=$menus->xpath("/Menu/Campo");
					foreach ($menu as $i => $a){
						$this->agregarMenu($a["nombre"], $a["destino"]);
					}
				} else {
					mensaje::add("La clase ".$nombrePaquete." no existe".print_r($this->sesion->args,true), CORREO);
				}
			}
		}
		//echo revisarArreglo($this->menu, "Menu");
	}
	function cargarBloques(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::cargarBloques()<br>"); }
	}
	function imprimirDentroDivs($dato){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::imprimirDentroDivs()<br>"); }
		return $dato;

	}
	function combinarContenidoTema($xmlContenido){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::combinarContenidoTema()<br>"); }

		$dom_tema = dom_import_simplexml($this->tema);
		//@todo Ojo se tiene que utilizar un unico documento para crear los elementos del xml
		$dom = new DOMDocument();
		$dom_tema = $dom->importNode($dom_tema, true);
		$dom_tema = $dom->appendChild($dom_tema);

		//echo "El contenido actualmente es: ".revisarArreglo($xmlContenido);
		//echo "El contenido actualmente es: ".var_dump($xmlContenido);

		$dom_contenido = dom_import_simplexml($xmlContenido);
		$dom_contenido = $dom->importNode($dom_contenido, true);

		$cont = $dom_tema->getElementsByTagName('Contenido')->item(0);
		$dom_tema->removeChild($cont);
		$dom_tema->appendChild($dom_contenido);
		$this->tema=simplexml_import_dom($dom_tema);
	}

	function generarPaginaHtml(){
		$conXSLT=CON_XSLT;
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::generarPaginaHtml()<br>"); }
		if(class_exists("temaDelProyecto")){
			$renderizador= new temaDelProyecto($this->tema, $this->sesion->respuestaAjax);
		}else{
			$renderizador= new tema($this->tema, $this->sesion->respuestaAjax);
		}
		$renderizador->configMenu=true;
		if($this->sesion->leerParametro("cargaMenu")=="false"){
			$renderizador->configMenu=false;
		}
		//Content-type: text/html; charset=utf-8
		$renderizador->CabecerasHTML();

		//if($GLOBALS["debug"]>1){ registrarlog("->IndexGeneral::generarPaginaHtml()<hr>".$this->geshiHTML($conXSLT?$renderizador->toXML():$renderizador->toHTML())."<hr><br>"); }
		//$variableAImprimir=$conXSLT?$renderizador->toXML():$renderizador->toHTML();
		if(strcmp($this->sesion->leerParametro("cargaTema"),"false")==0){
			$variableAImprimir="";
			if(strcmp($this->sesion->leerParametro("cargaCabeza"),"false")!=0){
				$variableAImprimir.=$renderizador->getCss();
				$variableAImprimir.=$renderizador->getJs();
			}else{
			}
			$variableAImprimir.=$renderizador->getContenido();
		}else{
			$variableAImprimir=$conXSLT?$renderizador->toXML():$renderizador->toHTML();
		}
		$this->sesion->borrarParametro("cargaCabeza");
		$this->sesion->borrarParametro("cargaMenu");
		$this->sesion->borrarParametro("cargaTema");

		print("$variableAImprimir");
		flush();
	}
	function limpiarCasoUso(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::limpiarCasoUso()<br>"); }
		$this->sesion->borrarParametro("casoUsoUsuario", "all");
		$this->sesion->borrarParametro("casoUsoRol", "all");
		$this->sesion->borrarParametro("casoUsoPermitido", "all");
	}
	function limpiarAnclas(){
		if($GLOBALS["debug"]>0){ registrarlog("->IndexGeneral::limpiarAnclas()<br>"); }
		$this->sesion->borrarAnclas();
	}
	function limpiarFormularios($a=0){
		//echo " --------------- -> Limpiando los formularios: $a<br>";
		$this->sesion->borrarFormularios($a);
		$this->sesion->borrarParametrosDestino();
	}

	//Preparar formulariO para guardar en sesion	
	function procesarFormulario($formulario,$contenido){
		$nuevoForm=ControlXML::nuevo("Formulario");
		append_simplexml($nuevoForm, $formulario);
		$dato = $nuevoForm->xpath("//Propiedad[@nombre='idCasoUso' or @nombre='nombreCasoUso']");
		/*
		if ($_SERVER['REMOTE_ADDR']=="190.24.69.172"){
		    var_dump($dato);
		    var_dump($this->controlCasoUso->getNombreCasoUso($dato[0]["valor"]));
		}*/
		
		$this->sesion->agregarFormulario($formulario, $this->controlCasoUso->getNombreCasoUso($dato[0]["valor"]),$contenido);

	}

	//Preparar formulariOS para guardar en sesion
	function procesarFormularios($contenido){
		//$GLOBALS["mensajesActivos"]=true;
		//new mensajes("se activaron los mensajes en: procesarFormularios");
		$formularios = $this->tema->xpath("//Formulario");
		$this->extraerNodo1p($formularios, "procesarFormulario",$contenido);
		$formularios = $this->tema->xpath("//FormularioRelacionesMN");
		$this->extraerNodo1p($formularios, "procesarFormulario",$contenido);
		//$GLOBALS["mensajesActivos"]=false;
	}

	function determinarEstadoPermisos(){
		$this->consultarInformacionUsuario();

		//4. Con la información del usuario se determinan los roles
		//$this->determinarRolesUsuario();
		$this->sesion->borrarParametro("roles", "all");
		$this->controlUsuarioRol->cargarRolesUsuario($this->sesion);


		//3.5 limpiar los permisos que tiene el usuario
		$this->limpiarCasoUso();
		//5. Se determinan los casos de uso a los que puede acceder de pendiendo
		//   su id de usuario
		//$this->determinarCasosUsoUsuario();
		//casoUsoRol
		//echo $this->geshiXML($this->sesion->xml);
		$this->controlUsuarioCasoUso->determinarCasosUsoUsuario($this->sesion);
		//6. Se determinan los casos de uso a los que puede acceder de pendiendo
		//   sus roles
		//echo $this->geshiXML($this->sesion->xml);
		$this->controlRolCasoUso->determinarCasosUsoRol($this->sesion);

		//echo $this->geshiXML($this->sesion->xml);

		//7. Se hace una mezcla de información para generar la información de los
		//   casos de uso y la información de los menús y bloques, a los que puede
		//   acceder.
		$this->mezclaCasosUso();
		//echo $this->geshiXML($this->sesion->xml);

	}

	function verificarFormulario($formularioActual, $xml){
		//echo "Parametros formularioActual ", generalXML::geshiXML($formularioActual);
		//echo "Parametros xml", generalXML::geshiXML($xml->xml);
		
		$idCasoUso=$this->sesion->leerParametro("destino");
		$fs = $xml->xpath("//Contenido//Formulario");
		//echo revisarArreglo($fs, "fs original");
		if ($fs){
			$total=count($fs);
			foreach($fs as $f){
				//echo "Probando ",$idCasoUso, " - ", $i,"-> ", $this->geshiXML($f), "<br>";
				$propiedad=$f->Propiedad;
				foreach($f->children() as $propiedad){
					if($propiedad->getName()== 'Propiedad'){
						//echo "strcmp ", $i,"-> ", $propiedad["nombre"], ", ", 'idCasoUso', " && - strcmp ", $propiedad['valor'], ",", $idCasoUso, "<br>";
						if (strcmp($propiedad["nombre"], 'idCasoUso')==0 && strcmp($propiedad['valor'], $idCasoUso)==0){
							$respuesta=Formulario::validarFormulario($formularioActual, $f);
							return $respuesta;
						}
					}
				}
				
				
			}
		}
		return true;
	}
	
	function procesarPeticion(){
		$contenido=new SimpleXMLElement("<Contenido><Texto><Campo nombre='titulo' valor='«- Por favor de click en un elemento del menú'/></Texto></Contenido>");

		//8. Se determina si el usuario tiene acceso al modulo y al caso de uso al
		//   que esta haciendo el pedido
		$parametrodestino=$this->sesion->leerParametro("destino");


		//echo "this->sesion->args[0]=".$this->sesion->leerParametro("nombreCasoUso");
		if (strcmp($this->sesion->leerParametro("nombreCasoUso"),"inc")==0){
			$paqueteGenerico= new PaqueteArchivos($this->db);
			$contenido=  $paqueteGenerico->generarContenido_inc($this->sesion);
			exit(0);
		}
		
		//echo "Procesando peticion [".$parametrodestino."]<br>";
		if(strcmp($parametrodestino,"")!=0){
			$destino=$this->sesion->leerAncla($parametrodestino);
			$parametrodestinoAux=$this->sesion->leerParametro("destinoAux");
			//echo "Se va a procesar el caso de uso:[".$parametrodestino.",".$parametrodestinoAux."]<br>";

			//echo "la respuesta para saber si es permitido o no es: [".$this->sesion->buscarParametroInterno("casoUsoPermitido", $parametrodestino)."]<br>";
			//echo "SESION".$this->textoXML($this->sesion->xml)."<br>";
			///echo "parametrodestino=$parametrodestino<br>";
			///echo "parametrodestinoAux=$parametrodestinoAux<br>";


				//echo "Estoy procesando un formulario [".$parametrodestino.",".$parametrodestinoAux."]<br>";
				//echo "Respuesta: ".$this->sesion->buscarFormulario($parametrodestino,$parametrodestinoAux)."<br>";
			if ($this->sesion->buscarFormulario($parametrodestino,$parametrodestinoAux)){
				//echo "Estoy procesando un formulario [".$parametrodestino.",".$parametrodestinoAux."]<br>";
				$formulario=$this->sesion->leerFormulario($parametrodestino);
				//$this->sesion->escribirParametro("idCasoUso", $parametrodestino);

				$nombrePaquete="Paquete".Control0CasoUso::getNombrePaquete($this->db,$formulario["idCasoUso"]);


				//@ToDo ojo falta verificar cuando no se puede determinar el caso del uso a llamar

				$GLOBALS["mensajesActivos"]=true;

				if (class_exists($nombrePaquete)){
					$paqueteGenerico= new $nombrePaquete($this->db);
				} else {
					asercion("610 lib ind, No existe la clase ".$nombrePaquete);
				}
				$casoUso=$this->controlCasoUso->getNombreCasoUso($parametrodestino);

				// Recuperación desde la sesión del formulario enviado
				$formularioActual=$this->sesion->getFormularioActual();
				// Se hace el la verificación del formulario que el usuario envió
				$xmlContenidoAnterior=$this->sesion->recuperarXMLContenido();
				$respuestaValidacion=$this->verificarFormulario($formularioActual, $xmlContenidoAnterior);
				
				if ($respuestaValidacion){
					$nombreFuncion="procesarFormulario";

					// Se registran los parámetros del formulario enviado
					ControlActividades::registrarEnBaseDatos(
							array(
								"idUsuario"=>$this->sesion->leerParametro("idUsuario"),
								"Parametros Destino Actual"=>$this->sesion->leerParametrosDestinoActual()
								),
							$this->sesion->leerParametro("idUsuario"), $casoUso."_FormularioEnviado");

					$contenido = $paqueteGenerico->$nombreFuncion($casoUso, $this->sesion);
					//echo "Toca borrar : ",$formularioActual,generalXML::geshiXML($this->sesion->xml);
				}else{
					// @ToDo Falta agregar en el contenido los "error" marcando
					//los campos del formulario que toca corregir
					new mensajes("Error en la validación de los datos, volviendo a generar el contenido anterior");
					$contenido= $xmlContenidoAnterior;

					// Se registran los parámetros del formulario INVALIDO enviado
					ControlActividades::registrarEnArchivo(array("Formulario Actual"=>$formularioActual),$this->sesion->leerParametro("idUsuario"),"formularios.log","FormularioInvalido");
				}
				$GLOBALS["mensajesActivos"]=false;
				//echo "De: ",generalXML::geshiXML($this->sesion->xml);
				$this->sesion->borrarFormularioActual();

				//echo "Se borro el formulario actual : ",$formularioActual,generalXML::geshiXML($this->sesion->xml);

			}else if ($this->sesion->buscarParametroInterno("casoUsoPermitido", $this->controlCasoUso->getNombreCasoUso($parametrodestino))){
				//echo "Estoy procesando un pedido[".$parametrodestino."]<br>";
				$this->sesion->escribirParametro("idCasoUso", $parametrodestino);

				if ($parametrodestino!=""){
					$nombrePaquete="Paquete".Control0CasoUso::getNombrePaquete($this->db, $parametrodestino);

					$nombreFuncion=$this->controlCasoUso->getNombreCasoUso($parametrodestino);
					$GLOBALS["mensajesActivos"]=true;
					$paqueteGenerico= new $nombrePaquete($this->db);
					$contenido=  $paqueteGenerico->generarContenido($nombreFuncion,$this->sesion);
					$GLOBALS["mensajesActivos"]=false;
					// El caso de uso fue llamado

				}else{
					$this->contenido= new SimpleXMLElement("<Contenido>Contenido no encontrado</Contenido>");
				}


			}else {
				ControlActividades::registrarEnArchivo(array("Paths"=>$this->sesion->leerParametro('direccionCompleta'),"Argumentos"=>$this->sesion->args),$this->sesion->leerParametro("idUsuario"),"pedidos.log","Acceso no permitido");
				// No se ha pedido algo valido
				$contenido= new SimpleXMLElement("<Contenido><Texto><Campo nombre='titulo' valor='«- Por favor de click en un elemento del menú'/></Texto></Contenido>");
			}
			//	1. En el caso de que no exista un modulo o no tenga acceso a el,
			//	   el sistema debe generar como contenido un mensaje de acceso
			//	   no autorizado o que no existe el modulo al que se quiere
			//	   acceder
		}else{
			ControlActividades::registrarEnArchivo(array("Path"=>$this->sesion->leerParametro('direccionCompleta'),"Argumentos"=>$this->sesion->args),$this->sesion->leerParametro("idUsuario"),"pedidos.log","Pedido Invalido");
			$contenido=new SimpleXMLElement("<Contenido><Texto><Campo nombre='titulo' valor='«- Por favor de click en un elemento del menú'/></Texto></Contenido>");
			//	2. En el caso de que exista y este autorizado determina si es
			//	   una petición de contenido
			//		1. Si es una petición de contenido llama al caso de uso
			//		   Pedido Contenido, con los parámetros de la petición
			//		2. Si es una petición de formulario llama al caso de uso
			//		   Pedido Formulario, con los parámetros del formulario
		}
		$this->limpiarFormularios($parametrodestino);

		return $contenido;

	}
	/*
	function generarSalidaFlash($contenido){
		$this->combinarContenidoTema($contenido);
		$this->procesarFormularios();
		echo $this->tema;
		$this->sesion->sincronizarBaseDatos();
	}*/
	function generarSalidaHtml($contenido){
		//9. Recibe el resultado
		//echo $this->textoXML($this->sesion->xml);

		//10. Consulta de las cosas que toca incluir como estilos y librerias (ej:
		//    js)
		$this->incluirEstilos();
		$this->incluirLibrerias();
		//11. Se consulta los elementos del menú de cada uno de los módulos
		//$this->limpiarFormularios(0);

		$this->cargarMenus();
		//12. Se consulta los bloques de cada uno de los módulos
		$this->cargarBloques();
		//13. Con la información de contenido, menús, y bloques se genera la
		//    pagina de acuerdo al tema


		$this->combinarContenidoTema($contenido);

		$this->procesarFormularios($contenido);

		//14. Se retorna la pagina generada al usuario

		$this->generarPaginaHtml();
		//echo "La sesion al final es: ".$this->textoXML($this->sesion->xml)."<br>";

	}/*
	function generarSalidaHtmlContenido($response){
		//$this->limpiarFormularios(0);
		$this->procesarFormularios();
		if(class_exists("temaDelProyecto")){
			$renderizador= new temaDelProyecto($this->tema, $response);
		}else{
			$renderizador= new tema($this->tema, $response);
		}
		$this->sesion->sincronizarBaseDatos();
		return $renderizador;
	}*/
	function pedidoGeneral(){
		$GLOBALS["mensajesActivos"]=true;
		//2. Esta envía a la parte de control la solicitud separando la
		//   información de la solicitud, como el documento, formulario o pagina
		//   que solicita.

		$this->separarParametrosSolicitud();

		$this->determinarEstadoPermisos();

		//echo "despues de los permisos: ".$this->textoXML($this->sesion->xml)."\n\n";
		$this->iniciarCasosDeUso();

		$this->menusEstaticos();

		$contenido=$this->procesarPeticion();

		//se almacena el contenido en el xml de la sesion
		if(!isset($contenido)){
			$contenido=ControlXML::nuevo("Contenido");
			mensaje::add("Error en caso de uso, contenido [".$this->sesion->leerParametro("nombreCasoUso")."] vacio.",CORREO);
			mensaje::add("Error en caso de uso, contenido [".$this->sesion->leerParametro("nombreCasoUso")."] vacio.",ERROR);
		}
		
		$this->sesion->agregarXMLContenido($contenido);
		
		$this->limpiarAnclas();
		if (strcmp((string)$contenido["Unico"], "true")==0){
			$this->sesion->escribirParametro("cargaTema", "false");
			$this->sesion->escribirParametro("cargaMenu", "false");
		}
		
		if (strcmp((string)$contenido["Cabeza"], "false")==0){
			$this->sesion->escribirParametro("cargaCabeza", "false");
		}

		$this->generarSalidaHtml($contenido);
		$this->sesion->sincronizarBaseDatos();
		$this->finalizarCasosDeUso();

		//echo "xml final: ".$this->geshiXML($this->sesion->xml)."\n\n";
		if($GLOBALS["debug"]>0){ registrarlog("<hr>"); }
	}

	function iniciarCasosDeUso(){
		$casosUso=$this->sesion->leerParametrosInternos("casoUsoPermitido");
		//echo "Iniciando Casos de uso<br>";
		//var_dump($casosUso);
		foreach($casosUso as $casoUso){
			$nombrePaquete="Paquete".Control0CasoUso::getNombrePaquete($this->db, (string)$casoUso["valor"]);
			$nombreFuncion="iniciarCasoUso_".(string)$casoUso["nombre"];
			if (class_exists($nombrePaquete)){
				$paquete= new $nombrePaquete($this->db);
				if (method_exists($paquete,$nombreFuncion)){
					$paquete->$nombreFuncion($this->sesion);
					//echo "La función $nombrePaquete->$nombreFuncion caso de uso existe<br>";
				}else{
					//echo "*La función $nombrePaquete->$nombreFuncion caso de uso NO existe<br>";
				}
			}else{
				mensaje::add("La clase ".$nombrePaquete." no existe".print_r($this->sesion->args,true), CORREO);
			}
			
			//$casoUso["nombre"]
		}
	}
	function finalizarCasosDeUso(){
		//echo "Finalizando Casos de uso<br>";
		$daoCasoUso= new DAO0CasoUso($this->db);
		$listaTodosCasos=array();
		try{
			$listaTodosCasos= $daoCasoUso->getRegistros();
		}catch(sinResultados $e){
		}

		$this->determinarEstadoPermisos();
		$casosUso=$this->sesion->leerParametrosInternos("casoUsoPermitido");
		//var_dump($listaTodosCasos);
		//var_dump($casosUso);

	/*
		Aca falta hacer la mezcla de la info
		si un caso de uso
		*/
		foreach($listaTodosCasos as $TcasoUso){
			$permitido=false;
			foreach($casosUso as $casoUso){
				if ($TcasoUso->getIdCasoUso()==$casoUso["valor"]){
					//echo "verificando ",$TcasoUso->getNombreCasoUso(),"<br>";
					$permitido=true;
					break;
				}
			}

			$nombrePaquete="Paquete".Control0CasoUso::getNombrePaquete($this->db, $TcasoUso->getIdCasoUso());
			$nombreFuncion="finalizarCasoUso_".$TcasoUso->getNombreCasoUso();
			if (class_exists($nombrePaquete)){
				$paquete= new $nombrePaquete($this->db);
				if (method_exists($paquete,$nombreFuncion)){
					$paquete->$nombreFuncion($this->sesion, $permitido);
					//echo "La función $nombrePaquete->$nombreFuncion caso de uso existe<br>";
				}else{
					//echo "*La función $nombrePaquete->$nombreFuncion caso de uso NO existe<br>";
				}
			}
			
			if ($permitido){
			}
			$this->sesion->borrarParametro("idCasoUso", "all");

		}

		
		//$casosUso=$this->sesion->leerParametrosInternos("casoUsoPermitido");
		//echo "Finalizando Casos de uso";
		//var_dump($casosUso);
	}
	function menusEstaticos(){

		foreach ($this->sesion->configuracion->menus as $link){
			$this->agregarMenu($link["texto"], $link["destino"]);/*
			$campo=$this->menu->addChild("Campo");
			$campo->addAttribute("nombre", $link["texto"]);
			$campo->addAttribute("destino", $link["destino"]);*/
		}

	}

	/*
	function pedidoAjax(){
		$response = new xajaxResponse();
		$num_args = func_num_args();
		$lista_args = func_get_args();
		/ *----------------------
		$total= "Número de argumentos: $num_args<br />\n";
		for ($i = 0; $i < $num_args; $i++) {
			$total.= "El argumento $i es: " . $lista_args[$i] . "<br />\n";
		}
		//$response->alert($total);
		//return $response;
		/----------------------* /
		//2. Esta envía a la parte de control la solicitud separando la
		//   información de la solicitud, como el documento, formulario o pagina
		//   que solicita.
		//$response->alert($this->separarParametrosSolicitudAjax($lista_args));

		$this->separarParametrosSolicitudAjax($lista_args);
		//$respuestaFuncion.="[antesDeProcesarPeticion]".$this->geshiXML($this->sesion->xml)."[/inicio]";
		$this->determinarEstadoPermisos();
		$contenido=$this->procesarPeticion();
		$this->combinarContenidoTema($contenido);
		//$respuestaFuncion.="[contenido]".$this->geshiXML($contenido)."[/contenido]";
		//$respuestaFuncion.="[tema]".$this->geshiXML($contenido)."[/tema]";
		//$respuestaFuncion.="[antesdeSalidaHtmlContenido]".$this->geshiXML($this->sesion->xml)."[/inicio]";
		//$response->alert("Hola");
		$renderizador=$this->generarSalidaHtmlContenido($response);
		if ($renderizador->getRetornoAjax()){
			$response->assign('contenido', 'innerHTML', $renderizador->getContenido());
		}else{
			//$response->alert("sin contenido");
		}
		//$response->assign('contenido', 'innerHTML',  "hola mundo");
		//$response->alert("XIAO");

		return $response;
	}*/
	function limpiar(){
		flush();
	}
}
?>
