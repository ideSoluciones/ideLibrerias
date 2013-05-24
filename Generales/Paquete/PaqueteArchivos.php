<?php
	class PaqueteArchivos extends Paquete{
		function PaqueteArchivos($db){		
			$this->Paquete($db);
		}



		/*
		 * generarContenido_inc
		 * Con este caso de uso se puede realizar inclusión de librerias
		 * cambiando constantes como pathCliente
		 * este caso de uso surge como necesidad de utilizar constantes en los
		 * css y js de los proyectos con direcciones relativas pero afectadas
		 * por las urls limpias
		 * 
		 * Basado en: http://icant.co.uk/articles/cssconstants/
		 */

		function nombreMenu_inc($sesion){
			return "";
			//return "IncluirArchivo/Css";
		}
		function generarContenido_inc($sesion){

            // Determina el nombre del archivo
            // @ToDo: Verificar ataques XSS
            // es permitido que escriba ../../../ en el parametro?, hasta el momento
            // no he visto que tenga problema de seguridad pero verificar          
            $parametrosCompletos=implode("/",$sesion->args);
            $partes=explode("/",$parametrosCompletos,2);
            $nombreArchivo=$partes[1];

            //Acepta inclusiones de Librerias y Externos
			if (strcmp($sesion->args[1], "Librerias")==0)
				$nombreArchivo="../".$nombreArchivo;
			if (strcmp($sesion->args[1], "Externos")==0)
				$nombreArchivo="../".$nombreArchivo;
            

            $partes=explode(".",$parametrosCompletos);
            $extension=$partes[count($partes)-1];
            
            
             //verifica que sea js o css
            
            if(
            	preg_match('/http:/',$nombreArchivo) ||
            	preg_match('/https:/',$nombreArchivo) ||
            	(
            		strcasecmp(strtolower($extension) , "js")!=0
            		&& 
            		strcasecmp(strtolower($extension) , "css")!=0
            	)
            )
            {
            	if (strcmp(strtolower($extension),"png")==0 || strcmp(strtolower($extension),"jpg")==0 || strcmp(strtolower($extension),"jpeg")==0 || strcmp(strtolower($extension),"gif")==0 ){
	            	if (file_exists($nombreArchivo))
		            {
		            	header('Content-Type: image/'.strtolower($extension));
						readfile($nombreArchivo);
					}else{
						header("HTTP/1.0 404 Not Found"); 
					}
            	}else{
					die('/* Son permitidos unicamente css y js!*/');
				}
				exit;
            }
			if (strcmp($extension,"css")==0)
            	header('content-type:text/'.$extension);
			if (strcmp($extension,"js")==0)
            	header('Content-type: application/javascript');
            header("Expires: ".gmdate("D, d M Y H:i:s", (time()+600)) . " GMT");


            // Carga el contenido del archivo o avisa si no lo puede encontrar
            $contenidoArchivo=$this->load($nombreArchivo);
            
            
            if($contenidoArchivo=='')
            {
	            die('/* Archivo no encontrado */');
	            exit(0);
            }

            //Variable con constantes a cambiar
            $constantes=array(
							//'$pathServidor'=>$sesion->leerParametro("pathServidor"),
							'\\$pathCliente'=>$sesion->leerParametro("pathCliente"),
							'\\$direccionCompleta'=>$sesion->leerParametro("direccionCompleta"),
							'\\$pathServidor'  => $sesion->leerParametro("pathServidor"),
							'\\$pathProyectos' => $sesion->leerParametro("pathProyectos"),
						);

            foreach($constantes as $nombre => $valor)
            {
           		//Remplaza las constantes en el contenido
	            $contenidoArchivo=preg_replace('/'.$nombre.'/',$valor,$contenidoArchivo);
            }

            echo $contenidoArchivo;
            exit(0);
		}
		function procesarFormulario_inc($sesion){
			// Para complementar este caso de uso se podria hacer paso de 
			// variables post que permitan parametrización de los remplazos
			return $this->generarContenido_incluirJS($sesion);
		}
        function load($filelocation)
        {
        	//echo "Vamos a abrir el archivo ", $filelocation, "<br>";
            if (file_exists($filelocation))
            {
	            $newfile = fopen($filelocation,"r");
	            if (filesize($filelocation)>0)
		            $file_content = fread($newfile, filesize($filelocation));
	            fclose($newfile);
	            return $file_content;
            }
        }		
        function nombreMenu_conectorFileTree($sesion){
			return "";
		}
        function generarContenido_conectorFileTree($sesion){
        	//echo "Auch";
        	$html="";
			$_POST['dir'] = urldecode($sesion->leerParametroDestinoActual('dir'));
			$root=$sesion->leerParametro("pathServidor")."/files/".$sesion->leerParametroDestinoActual('dir');
			$dir=$sesion->leerParametroDestinoActual('dir');			
			if( file_exists($root)) {
				$files = scandir($root);
				natcasesort($files);
				
				if( count($files) > 2 ) { /* The 2 accounts for . and .. */
					$html.= "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					// All dirs
					foreach( $files as $file ) {
						if( file_exists($root. $file) && $file != '.' && $file != '..' && is_dir($root. $file) ) {
							$html.= "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($dir . $file) . "/\">" . htmlentities($file) . "</a></li>";
						}
					}
					// All files
					foreach( $files as $file ) {
						if( file_exists($root  . $file) && $file != '.' && $file != '..' && !is_dir($root  . $file) ) {
							$ext = preg_replace('/^.*\./', '', $file);
							$html.= "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($dir. $file) . "\">" . htmlentities($file) . "</a></li>";
						}
					}
					$html.= "</ul>";	
				}
			}    
			$contenido=xml::add(null, "Contenido", array("Unico"=>"true", "Cabeza"=>"false"));
			xml::add($contenido, "Html", $html);
			return $contenido;
        }
		function procesarFormulario_conectorFileTree($sesion){
			return $this->generarContenido_conectorFileTree();
		}


		/**
		* administrarArchivos
		*/

		function iniciarCasoUso_administrarArchivos($sesion){
			//$html=ControlArchivo::administradorArchivos(/*$PathATrabajar*/);
			//echo "-> Iniciando caso de uso administrarArchivos<br>";
			//var_dump($sesion->configuracion->titulo);
			ControlArchivo::iniciar_administradorArchivos(str_replace(' ', "_", $sesion->configuracion->titulo.$sesion->idSesion));
		}
		function finalizarCasoUso_administrarArchivos($sesion, $permitido){
			//echo "-< Finalizando caso de uso administrarArchivos<br>";
			if ($permitido){
				//echo "Se sigue permitiendo el acceso del usuario<br>";
			}else{
				//echo "----NO Se sigue permitiendo el acceso del usuario<br>";
				ControlArchivo::finalizar_administradorArchivos(str_replace(' ', "_", $sesion->configuracion->titulo.$sesion->idSesion));
			}
		}
		function nombreMenu_administrarArchivos($sesion){
			return "Administrar/Archivos";
		}
		function generarContenido_administrarArchivos($sesion){
			//$controlUsuario = new Control0Usuario($this->db);
			
			$html=ControlArchivo::administradorArchivos(/*$PathATrabajar*/);
			
			$xmlFormulario=new SimpleXMLElement("
				<Contenido>
					<htmlencodeado>".base64_encode($html)."</htmlencodeado>
				</Contenido>");
  			return $xmlFormulario;
		}
		function procesarFormulario_administrarArchivos($sesion){
			return $this->generarContenido_administrarArchivos();
		}
    }
?>
