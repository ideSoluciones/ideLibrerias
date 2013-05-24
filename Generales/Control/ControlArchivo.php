<?php

	class ControlArchivo{
	
		public static function getInfoArchivo($nombre){
			$sesion=Sesion::getInstancia();
			$respuesta=array();


			$respuesta["nombre"]="";
			$respuesta["path"]="";
			$respuesta["acceso"]="";
			$respuesta["modificación"]="";
			$respuesta["creación"]="";
			$respuesta["tamaño"]="";
			
			if (file_exists($nombre)){
				$infoGeneral=stat($nombre);

				$infoPath=pathinfo($nombre);
				
				$mime = tipo_mime($nombre);
				$partesArchivo=explode("/", $nombre);
			
				$pathServidor=$sesion->leerParametro("pathServidor");
				$partesServidor=explode("/", $pathServidor);
				$ultimoid=count($partesArchivo)-1;
				$respuesta["nombre"]=$partesArchivo[$ultimoid];
				foreach($partesServidor as $i=>$parteServidor){
					unset($partesArchivo[$i]);
				}
				unset($partesArchivo[$ultimoid]);

				$respuesta["nombre"]=$infoPath["basename"];
				$respuesta["extensión"]=$infoPath["extension"];
				$respuesta["path"]=implode("/", $partesArchivo);
				$respuesta["tipomime"]=$mime;
				$respuesta["acceso"]=date("Y-m-d H:i:s", $infoGeneral["atime"]);
				$respuesta["modificación"]=date("Y-m-d H:i:s", $infoGeneral["mtime"]);
				$respuesta["creación"]=date("Y-m-d H:i:s", $infoGeneral["ctime"]);
				$respuesta["tamaño"]=file_size($infoGeneral["size"]);

			}
			return $respuesta;
			
		}
		// Retorna un arreglo con la información de un archivo recién subido a la carpeta /tmp de apache
		// El arreglo contiene los campos:
		// nombre: nombre original del archivo subido
		// extension: extensión del archivo. Ej: Para "archivo.php" la extension es [php] Para "archivo" la extension es []
		// tamano: Es el tamaño del archivo en MegaBytes
		// pathTmp: es la ubicación temporal del archivo en el servidor
		public static function obtenerInfoArchivo($nombreArchivo){
			$nombreArchivo = "".$nombreArchivo;
			$infoArchivo = array();
			//Nombre de archivo
			if(isset($_FILES[$nombreArchivo]['name'])&&isset($_FILES[$nombreArchivo]["size"])&&isset($_FILES[$nombreArchivo]["tmp_name"])){
				$infoArchivo["nombre"] =  basename($_FILES[$nombreArchivo]['name']);
				//Extension de archivo
				$arrayArchivoNombre = explode(".",$infoArchivo["nombre"]);
				$infoArchivo["extension"] = $arrayArchivoNombre[count($arrayArchivoNombre)-1];
				//Tamaño de archivo en KB
				$infoArchivo["tamano"] = $_FILES[$nombreArchivo]["size"]/1024;
				//Path temporal donde ha sido subido el archivo
				$infoArchivo["pathTmp"] = $_FILES[$nombreArchivo]["tmp_name"];
			}
			return $infoArchivo;
		}
		// Valida un archivo identificado por los parámetros en $infoArchivo con las reglas en $parametros
		public static function validarArchivo($infoArchivo, $parametros){
			$respuesta = array(true,"");
			//Se valida el tamaño del archivo
			if($infoArchivo["tamano"] > $parametros["tamanoMax"] ){
				throw new Exception("El tamaño del archivo debe ser menor que ".$parametros["tamanoMax"]." KB");
			}
			//Se valida que la extension del archivo este permitida
			if(!in_array(strtolower($infoArchivo["extension"]),$parametros["extensiones"])){
				throw new Exception("\nEl archivo que usted quiere enviar no tiene un formato compatible. \nSolamente se soportan los siguientes formatos: ".implode(', ',$parametros["extensiones"]));
			}
			return $respuesta;
		}
		// Copia un archivo de la carpeta tmp de apache a la carpeta files en la raiz del proyecto
		// infoArchivo debe tener los campos:
		// pathTmp: ubicación temporal del archivo
		// nombre: nombre con el que quedará el archivo después de moverlo
		// Retorna un array(bool, string) donde:
		// * La primera posición indica si la recuperación se ejecuto o no
		// * La segunda posición es un mensaje que notifica la causa de un eventual error
		public static function recuperarArchivo($infoArchivo){
			$respuesta = array(true,"");
			$carpetaArchivos = "/files/";
			$infoArchivo["pathSubida"] = realpath("")."/files/".$infoArchivo["nombre"];

			// Verifica si el archivo a recuperar existe en el path indicado
			if(is_uploaded_file($infoArchivo["pathTmp"]))
			{
				if(!copy($infoArchivo["pathTmp"],$infoArchivo["pathSubida"]))
				{
					$respuesta[1].= "Ocurrio un error al intentar copiar el archivo subido";
				}
			}
			else{
				$respuesta[1].= "El archivo no se encuentra subido";
			}
			return $respuesta;
		}

		// Realiza una consulta en un directorio y consulta todos los archivos
		// con las extenciones solicitadas en filtros
		public static function getArchivosDirectorio($path, $filtros=array("png","jpg","gif")){
			$archivos = array();
			$dir=opendir($path);
			$lista=array();
			while ($archivo=readdir($dir)){
				if ($archivo{0}!="."){
					if (in_array(strtolower(substr($archivo,-3)), $filtros)){
						$archivos[]=$archivo;
					}
				}
			}
			closedir($dir);
			return $archivos;
		}





	
		//Administrar permisos de archivos
	
	
	
	
		//Control iniciar el administrador de archivos
		public static function iniciar_administradorArchivos($id,$path='files', $parametros=array("display"=>"Archivos")){
			$archivo = $_SESSION["paths"]["pathProyectos"].'/Externos/AjaXplorer/server/conf/ideConfig_'.$id.'.php';
			$fp = @fopen($archivo, "w");
			$string = 
'<?php
	unset($_SESSION["AJXP_USER"]);
	$REPOSITORIES[0] = array(
		"DISPLAY"		=>	"'.$parametros["display"].'", 
		"DRIVER"		=>	"fs", 
		"DRIVER_OPTIONS"=> array(
			"PATH"			=>	"'.$_SESSION["paths"]["pathServidor"].'/'.$path.'", 
			"CREATE"		=>	true,
			"RECYCLE_BIN" 	=> 	"recycle_bin",
			"CHMOD_VALUE"   =>  "0644",
			"DEFAULT_RIGHTS"=>  "",
			"PAGINATION_THRESHOLD" => 500,
			"PAGINATION_NUMBER" => 200,
			"SHOW_HIDDEN_FILES" => false
		),

	);
	$installPath = "'.$_SESSION["paths"]["pathProyectos"].'/Externos/AjaXplorer";
	define("INSTALL_PATH", $installPath);
	define("USERS_DIR", $installPath."/server/users");
	define("SERVER_ACCESS", "content.php");
	define("ADMIN_ACCESS", "admin.php");
	define("IMAGES_FOLDER", "client/images");
	define("CLIENT_RESOURCES_FOLDER", "client");
	define("SERVER_RESOURCES_FOLDER", "server/classes");
	define("DOCS_FOLDER", "client/doc");
	define("TESTS_RESULT_FILE", $installPath."/server/conf/diag_result.php");
	$default_language="es";
?>';
			$write = @fputs($fp, $string);
			@fclose($fp); 
		}
		//Control para finalizar la adminsitración de los archivos
		public static function finalizar_administradorArchivos($id){
			$archivo = $_SESSION["paths"]["pathProyectos"].'/Externos/AjaXplorer/server/conf/ideConfig_'.$id.'.php';
			if(file_exists($archivo)){
				if(unlink($archivo)){
					//echo "El archivo fue borrado<br>";
				}
			} 
			
		}
		//Control para generar un administrador de archivos
		public static function administradorArchivos($path='files', $parametros=array("display"=>"Archivos")){

			static $idFrame=0;

			$xml=array();
			$xml['ancho']="100%";
			$xml['alto']="100%";
		
			$html='
			<iframe id="frame'.$idFrame.'" width="'.$xml['ancho'].'" height="'.$xml['alto'].'" style="min-height:600px" 
							frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
							src="'.resolverPath().'/../Externos/AjaXplorer"></iframe>';
			return $html;

		}
	}
?>
