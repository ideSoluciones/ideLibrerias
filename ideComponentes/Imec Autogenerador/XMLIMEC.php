<?php

	class XMLIMEC extends generalXML{
		var $conexion;
		private $xml;
		private $nombretabla;
		private $titulo;
		private $editar;
		private $borrar;
		private $consultar;
		private $nuevo;
		private $lista;
		private $titulosLista;
		private $listaCampos;
		private $ini; //Numero de registro desde el cual se empieza a mostrar la lista
		private $sup; //Numero de registros a mostrar en la lista
		private $idCasoUso;
		private $sesion;
		private $accion;
		private $urlcss;
		private $totalRegistros;
		
		
		function XMLIMEC($sesion,$xml,$urlcss=null){
			$this->sesion=$sesion;
			$this->xml=$xml;
			$urlcss=null;
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::XMLIMEC()<br>"); }
			$this->campos=array();
			$this->titulosLista=array();
			$this->listaCampos=array();
			$this->nombretabla="";
			$this->editar='';
			$this->borrar='';
			$this->consultar='';
			$this->totalRegistros=0;
			if ($this->sesion->leerParametroFormularioActual('inicial')==null){
				$this->inicial=0;
			}else{
				$this->inicial=$this->sesion->leerParametroFormularioActual('inicial');
			}
			if ($this->sesion->leerParametroFormularioActual('cantidad')==null){
				//var_dump($configuracionGeneral);
				$this->cantidad=$this->sesion->configuracion->cantidadImec;
			}else{
				$this->cantidad=$this->sesion->leerParametroFormularioActual('cantidad');
			}
			$this->conexion=$sesion->getDB();
			$this->idCasoUso=-1;
			$this->urlcss=$urlcss;
			$this->lista=array();
			$this->XMLIMEC_analizarPeticion($sesion);
			$this->XMLIMEC_analizarXML();
		}
		
		function XMLIMEC_analizarPeticion($sesion){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::XMLIMEC_analizarPeticion()<br>"); }
			$this->idCasoUso=$sesion->leerParametro("idCasoUso");
		}
		
		/**
		* @abstrac Funcion que almacena el nombre de la tabla principal y el titulo principal
		*/
		function XMLIMEC_Propiedades($datos){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::XMLIMEC_Propiedades()<br>"); }
			$this->nombretabla=$datos["tabla"];
			$this->titulo=$datos["titulo"];
		}
		
		/**
		* @abstrac 	Funcion que almacena la lista que se muestra en el formulario
		* 			principal.
		*/
		function XMLIMEC_Lista($datos){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::XMLIMEC_Lista()<br>"); }
			//Se establece que propiedades se deben activar
			$this->editar=$datos["editar"];
			$this->borrar=$datos["borrar"];
			$this->consultar=$datos["consultar"];
			$this->nuevo=$datos["nuevo"];
			
			$this->titulosLista=array();
			$this->lista=array();
			
			// Se crea el objeto xml que contendra la consulta de la lista
			$consulta=new SimpleXMLElement("<Consulta />");
			$arrtmp=array();
			
			//Se sacan los campos llave primaria, y si no los hay, se toman todos los datos
			$xmltmp=$this->xml->xpath('Datos/Campo[@tipo="llavePrimariaForanea" or @tipo="llavePrimaria" or @tipo="llavePrimariaAutonumerica" or @llavePrimaria="true"]');
			$llaves=array();
			if(is_array($xmltmp)){
				if(count($xmltmp)>0){
					foreach($xmltmp as $nodo){
						$llaves[]=$nodo[0]['nombre'];
						$parametro = $consulta->addChild('Campo');
						$parametro->addAttribute('nombre', $nodo[0]['nombre']);
						$parametro->addAttribute('tablaOrigen', $this->nombretabla);
					}
				}else{
					$xmltmp=$this->xml->xpath('Datos/Campo');
					foreach($xmltmp as $nodo){
						$llaves[]=$nodo[0]['nombre'];
						$parametro = $consulta->addChild('Campo');
						$parametro->addAttribute('nombre', $nodo[0]['nombre']);
						$parametro->addAttribute('tablaOrigen', $this->nombretabla);
					}
				}
			}
			//Se extraen los campos que se mostraran en la lista
			$arr=$datos->xpath('Titulo');
			
			foreach($arr as $col){
				$parametro = $consulta->addChild('Campo');
				
				//@ToDo revisar elementos necesarios para que los joins funcionen bien
				
				$xmlLlaves=$this->xml->xpath('Datos/Campo[@nombre="'.$col["campo"].'" ]');
				if (strcmp($xmlLlaves[0]['tipo'], "llavePrimariaForanea")==0 || 
					strcmp($xmlLlaves[0]['tipo'], "llaveForanea")==0|| 
					strcmp($xmlLlaves[0]['llaveForanea'], "true")==0){
					$parametro->addAttribute('nombre', $xmlLlaves[0]['campoTextoClaveForanea']);
					$parametro->addAttribute('tablaOrigen', $xmlLlaves[0]['tablaClaveForanea']);
					$parametroRelacion = $consulta->addChild('Relacion');
					$parametroRelacionTabla = $parametroRelacion->addChild("Tabla");
					$parametroRelacionTabla->addAttribute('nombre', $this->nombretabla);
					$parametroRelacionTabla->addAttribute('campo', $col['campo']);
					$parametroRelacionTabla->addAttribute('tablaDestino', $xmlLlaves[0]['tablaClaveForanea']);
					$parametroRelacionTabla->addAttribute('campoDestino', $xmlLlaves[0]['campoClaveForanea']);
					$parametroRelacionTabla->addAttribute('campoTextoClaveForanea', $xmlLlaves[0]['campoTextoClaveForanea']);
					$parametroRelacionTabla->addAttribute('campoAliasTextoClaveForanea', $xmlLlaves[0]['campoAliasTextoClaveForanea']);
					//@ToDo cambiar el nombre de elefante2 por algo mas adecuado
					$elefante2=isset($xmlLlaves[0]['campoAliasTextoClaveForanea'])
											?
											$xmlLlaves[0]['campoAliasTextoClaveForanea']
											:
											$xmlLlaves[0]['campoTextoClaveForanea'];
					$this->listaCampos["$elefante2"]="1";
				}else{
					$parametro->addAttribute('nombre', $col["campo"]);
					$parametro->addAttribute('tablaOrigen', $this->nombretabla);
					$elefante2=$col["campo"];
					$this->listaCampos["$elefante2"]="1";
				}
				$elefante2=isset($xmlLlaves[0]['titulo'])?$xmlLlaves[0]['titulo']:$col["campo"];
				$this->titulosLista["$elefante2"]="";
			}
			$registros=$this->conexion->consultar($consulta->asXML());
			$this->totalRegistros=count($registros);
			
			$parametro = $consulta->addChild('Limitar');
			$parametro->addAttribute('regInicial', $this->inicial);
			$parametro->addAttribute('noRegistros', $this->cantidad);
			//Se consulta el listado a mostrar y se almacena en una matriz	
			$registros=$this->conexion->consultar($consulta->asXML());
			foreach($registros as $fil){
				$arrtmp=array();
				$camposLlave="<llaves>";
				foreach($llaves as $llave){
					$camposLlave.="<llave nombre=\"".$llave."\" valor=\"".$fil["$llave"]."\" />";
				}
				$camposLlave.="</llaves>";
				$arrtmp[]=base64_encode($camposLlave);
				foreach($this->listaCampos as $campo => $valor){
					$arrtmp[]=$fil["$campo"];
				}
				$this->lista[]=$arrtmp;
			}
		}
		
		function XMLIMEC_analizarXML(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::XMLIMEC_analizarXML()<br>"); }
			$xmltmp=$this->xml->xpath('Propiedades');
			$this->extraerNodo($xmltmp, "XMLIMEC_Propiedades");
			$xmltmp=$this->xml->xpath('Lista');
			$this->extraerNodo($xmltmp, "XMLIMEC_Lista");
		}
		
		function generarContenido($mensaje=null){
			//echo revisarArreglo($mensaje, "Contenido a generar Elefantes verdes");
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::generarContenido()<br>"); }
			$this->XMLIMEC_analizarXML();
			$total="";
			$banderaTitulo=true;
			$banderaLlave=true;
			$tmpLlave="";
			$contenidoTotal=new SimpleXMLElement("<Contenido/>");
			if(!is_null($this->urlcss)){
				$form=$contenidoTotal->addChild("HojaEstilo",$this->urlcss);
			}
			$contenido=$contenidoTotal->addChild("Imec");
			$parametro=$contenido->addChild("Etiqueta");
			$parametro->addAttribute('nombre', 'TituloImec');
			$parametro->addAttribute('valor', $this->titulo);
			
			$parametro=$contenido->addChild("Conjunto");
			$elemento=$parametro->addChild("Elemento");
			foreach($this->titulosLista as $tit => $val){
				$campo = $elemento->addChild('Titulo',$tit);
			}
			$campo = $elemento->addChild('Titulo','Acciones IMEC');
			//echo "se van a analizar ",count($this->lista),"elementos";
			foreach($this->lista as $fil){
				$elemento=$parametro->addChild("Elemento");
				$banderaLlave=true;
				foreach($fil as $col){
					if($banderaLlave){
						$tmpLlave=$col;
						$banderaLlave=false;
					}else{
						$campo = $elemento->addChild('CampoImec',$col);
					}
				}
				$campo = $elemento->addChild('CampoImec');
				$form=$campo->addChild("Formulario");
				$propiedad = $form->addChild('Propiedad');
				$propiedad->addAttribute('nombre', 'Metodo');
				$propiedad->addAttribute('valor', 'POST');
				$propiedad = $form->addChild('Propiedad');
				$propiedad->addAttribute('nombre', 'idCasoUso');
				$propiedad->addAttribute('valor', $this->idCasoUso);
				$propiedad = $form->addChild('Propiedad');
				$propiedad->addAttribute('nombre', 'accion');
				$propiedad->addAttribute('valor', '#procesoImec');
				if($this->editar=="true"){
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', 'editar');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');
				}
				if($this->borrar=="true"){
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', 'borrar');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');
				}
				if($this->consultar=="true"){
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', 'consultar');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');
				}
				
				$propiedad = $form->addChild('Campo');
				$propiedad->addAttribute('nombre', 'imec_llave');
				$propiedad->addAttribute('valorPorDefecto',$tmpLlave );
				$propiedad->addAttribute('tipo', 'oculto');
			}
			if($this->nuevo=="true"){
				$form=$contenido->addChild("Formulario");
				$propiedad = $form->addChild('Propiedad');
				$propiedad->addAttribute('nombre', 'Metodo');
				$propiedad->addAttribute('valor', 'POST');
				$propiedad = $form->addChild('Propiedad');
				$propiedad->addAttribute('nombre', 'idCasoUso');
				$propiedad->addAttribute('valor', $this->idCasoUso);
				$propiedad = $form->addChild('Propiedad');
				$propiedad->addAttribute('nombre', 'accion');
				$propiedad->addAttribute('valor', '#procesoImec');

				
				$propiedad = $form->addChild('Campo');
				$propiedad->addAttribute('titulo', 'Nuevo');
				$propiedad->addAttribute('nombre', 'imec_accion');
				$propiedad->addAttribute('tipo', 'enviar');
			}
			

			
			if ($this->inicial!=0 || $this->totalRegistros>($this->inicial+$this->cantidad)){
				$mens=$contenido->addChild("Etiqueta"); 
				$mens->addAttribute('nombre', 'comentario');
				$mens->addAttribute('nivel', '4');
				$mens->addAttribute('valor', 'pagina '.floor($this->inicial/$this->cantidad+1).' de '.ceil($this->totalRegistros/$this->cantidad).'');
			}
			
			$contenedor=$contenido->addChild("Contenedor");
			$contenedor->addAttribute("clase", "caja");
			$contenedor->addAttribute("claseHijo", "floatIzquierda");
			/*echo "Inicial: ".($this->inicial);
			echo "<br>Inicial-10: ".($this->inicial-($this->cantidad*10));
			*/
						
			if (($this->inicial-($this->cantidad*100))>0){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
			
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '« « « 100');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');
					$nuevoInicio=$this->inicial-($this->cantidad*100);
					$nuevoInicio=($nuevoInicio<0)?0:$nuevoInicio;
					$propiedad->addAttribute('valorPorDefecto', $nuevoInicio);
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}
			
			if (($this->inicial-($this->cantidad*10))>0){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
			
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '« « 10');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');
					$nuevoInicio=$this->inicial-($this->cantidad*10);
					$nuevoInicio=($nuevoInicio<0)?0:$nuevoInicio;
					$propiedad->addAttribute('valorPorDefecto', $nuevoInicio);
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}
			if (($this->inicial-($this->cantidad*2))>0){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
			
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '« « 2');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');
					$nuevoInicio=$this->inicial-($this->cantidad*2);
					$nuevoInicio=($nuevoInicio<0)?0:$nuevoInicio;
					$propiedad->addAttribute('valorPorDefecto', $nuevoInicio);
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}
			if ($this->inicial!=0){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
			
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '« 1');
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');
					$nuevoInicio=$this->inicial-$this->cantidad;
					$nuevoInicio=($nuevoInicio<0)?0:$nuevoInicio;
					$propiedad->addAttribute('valorPorDefecto', $nuevoInicio);
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}
	
			if ($this->totalRegistros>($this->inicial+$this->cantidad)){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
		
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '1 »');//.$this->inicial.' - '.($this->inicial+$this->cantidad)." de ".$this->totalRegistros);
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');				
					$propiedad->addAttribute('valorPorDefecto', $this->inicial+$this->cantidad);
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}	
			
			
			if ($this->totalRegistros>($this->inicial+($this->cantidad*10))){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
		
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '10 » »');//.$this->inicial.' - '.($this->inicial+$this->cantidad)." de ".$this->totalRegistros);
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');				
					$propiedad->addAttribute('valorPorDefecto', $this->inicial+($this->cantidad*10));
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}	
			if ($this->totalRegistros>($this->inicial+($this->cantidad*2))){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
		
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '2 » »');//.$this->inicial.' - '.($this->inicial+$this->cantidad)." de ".$this->totalRegistros);
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');				
					$propiedad->addAttribute('valorPorDefecto', $this->inicial+($this->cantidad*2));
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}
			if ($this->totalRegistros>($this->inicial+($this->cantidad*100))){
				$form=$contenedor->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);
		
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', '100 » » »');//.$this->inicial.' - '.($this->inicial+$this->cantidad)." de ".$this->totalRegistros);
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');

					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'inicial');				
					$propiedad->addAttribute('valorPorDefecto', $this->inicial+($this->cantidad*100));
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'oculto');
					$propiedad->addAttribute('nombre', 'cantidad');
					$propiedad->addAttribute('valorPorDefecto', $this->cantidad);
			}	
			
			/*
	<Formulario>
		<Propiedad nombre='idCasoUso' valor='".$sesion->leerParametro("idCasoUso")."' /> 
		<Campo tipo='cadena' titulo='Usuario' nombre='Usuario' requerido='true' />
		<Campo tipo='clave'  titulo='Contraseña' nombre='Pass' requerido='true' />
		<Campo tipo='enviar' titulo='Enviar' nombre='Enviar' />
		<Campo tipo='oculto' nombre='campoSuperEscondido' valorPorDefecto='iduno' />
	</Formulario>			
	<Campo titulo='' nombre='SeleccioneC' tipo='ComboSeleccion' valorPorDefecto='' >
		<Opcion nombre='Seleccion uno' valor='1'/>
		<Opcion nombre='Seleccion dos' valor='2'/>
		<Opcion nombre='Seleccion tres' valor='3'/>
	</Campo>
	
	<Texto>
		<Campo nombre='titulo'    nivel='1' valor='Ingresando al sistema'/>
		<Campo nombre='contenido' valor='Exito ingresando al sistema ud es ".$usuario["idUsuario"]."' />
	</Texto>
	
			*/
			/*
			echo "Total: ".$this->totalRegistros."<br>";
			echo "cantidadx3: ".($this->cantidad*2)."<br>";
			if ($this->totalRegistros>($this->cantidad*2)){
				echo "Ojo si entra<br>";
				$form=$contenido->addChild("Formulario");
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'Metodo');
					$propiedad->addAttribute('valor', 'POST');
					$propiedad = $form->addChild('Propiedad');
					$propiedad->addAttribute('nombre', 'idCasoUso');
					$propiedad->addAttribute('valor', $this->idCasoUso);

					
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('tipo', 'listaseleccion');
					$propiedad->addAttribute('titulo', 'Seleccione');
					$propiedad->addAttribute('nombre', 'imec_combo');

					for ($i=0;$i<10;$i++){
						$opcion=$propiedad->addChild('Opcion');
						$opcion->addAttribute('nombre', 'Pagina '.$i);
						$opcion->addAttribute('valor', $i);
					}
		
					$propiedad = $form->addChild('Campo');
					$propiedad->addAttribute('titulo', 'Saltar');//.$this->inicial.' - '.($this->inicial+$this->cantidad)." de ".$this->totalRegistros);
					$propiedad->addAttribute('nombre', 'imec_accion');
					$propiedad->addAttribute('tipo', 'enviar');
					
					
					//$propiedad->addAttribute('valor', $links);

			}
			*/
			
			if(!is_null($mensaje)){
				$this->simplexml_merge($contenidoTotal,$mensaje);
			}
			//echo $this->geshiXML($contenidoTotal);
			return $contenidoTotal;
		}
		
		function procesarFormulario(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::procesarFormulario()<br>"); }
			$contenido=new SimpleXMLElement("<Imec />");
			$this->accion=$this->sesion->leerParametroFormularioActual("imec_accion");
			switch($this->accion){
				//Generación de formulario de edición
				case "editar":
					$contenido=$this->ProcesarFormulario_Editar();
					break;
				//Generación de formulario de eliminación
				case "borrar":
					$contenido=$this->ProcesarFormulario_Borrar();
					break;
				//Generación de formulario de eliminación
				case "Eliminar":
					$contenido=$this->ProcesarFormulario_Eliminar();
					break;	
				//Generación de formulario de consulta
				case "consultar":
					$contenido=$this->ProcesarFormulario_Consultar();
					break;
				//Generación de formulario de Nuevo
				case "Nuevo":
					$contenido=$this->ProcesarFormulario_Nuevo();
					break;
				case "Crear":
					$contenido=$this->ProcesarFormulario_Crear();
					break;
				case "Salvar":
					$contenido=$this->ProcesarFormulario_Salvar();
					break;
				case "Anterior":
					$contenido=$this->ProcesarFormulario_Anterior();
					break;
				case "Siguiente":
					$contenido=$this->ProcesarFormulario_Siguiente();
					break;
				/*case "Saltar":
					$contenido=$this->ProcesarFormulario_Saltar();
					break;*/
				default:
				/*
					echo "accion: ".$this->accion."<br>";
					echo "Largo: ".strlen($this->accion)."<br>";
					echo "primero".$this->accion[0]."<br>";
					echo "ultimo".substr($this->accion,-1);*/
					if (substr($this->accion,0,1)=='«' || substr($this->accion,-1, 1)=='»'){
						$contenido=new SimpleXMLElement("<Imec />");
						$mens=$contenido->addChild("Texto"); 
						$texto=$mens->addChild("Campo"); 
						$texto->addAttribute('nombre', 'titulo');
						$texto->addAttribute('valor', 'La acción ['.$this->accion.'] solicitada no esta implementada.');
					}
					

			}
			return $this->generarContenido($contenido);
		}
		
		function getLlaves(){
			$llaves=new SimpleXMLElement((base64_decode($this->sesion->leerParametroFormularioActual("imec_llave"))));
			//new mensajes("<b>Felipe Recibe</b>".revisarArreglo($llaves,"Llave").htmlspecialchars(base64_decode($this->sesion->leerParametroFormularioActual("imec_llave")))."<hr><hr>");
			$listaLlaves=$llaves->xpath('//llave');
			foreach($listaLlaves as $llave){
				$a=$llave["nombre"];
				$temp["$a"]=$llave["valor"];
			}
			return $temp;
		}
				
		function consultarRegistros(){
			/*Se genera la consulta de todos los campos filtrando con las llaves enviadas*/
			$temp=$this->getLlaves();
			$consulta=new SimpleXMLElement("<Consulta />");
			$xmltmp=$this->xml->xpath('Datos/Campo');
			$campos=array();
			$foraneas=array();
			foreach($xmltmp as $nodo){
				if($nodo['tipo']=="llaveForanea" || $nodo['tipo']=="llavePrimariaForanea" || $nodo['llaveForanea']=="true"){
					$foraneas[]=array(
						"campoDestino"=>$nodo["nombre"],
						"tablaDestino"=>$this->nombretabla,
						"campo"=>$nodo['campoClaveForanea'],
						"nombre"=>$nodo['tablaClaveForanea']
					);
					$nombreCampo=$nodo['campoTextoClaveForanea'];
					$nombreTabla=$nodo['tablaClaveForanea'];
				}else{
					$nombreCampo=$nodo["nombre"];
					$nombreTabla=$this->nombretabla;
				}
				$parametro = $consulta->addChild('Campo');
				$parametro->addAttribute('nombre', $nombreCampo);
				$parametro->addAttribute('tablaOrigen', $nombreTabla);
				if(strlen($nodo["titulo"])>0){
					$a=$nodo["titulo"];
					$campos["$a"]=array("nombreCampo" => $nombreCampo, "tipoCampo"=>$nodo['tipo']);
				}else{
					$campos["$nombreCampo"]=array("nombreCampo" => $nombreCampo, "tipoCampo"=>$nodo['tipo']);
				}
			}
			
			if(count($foraneas)>0){
				$parametro = $consulta->addChild('Relacion');
				$parametro1 = $parametro->addChild('Tabla');
					$parametro1->addAttribute('nombre', $this->nombretabla);
					$parametro1->addAttribute('campo', "");
				foreach($foraneas as $foranea){
					$parametro1 = $parametro->addChild('Tabla');
					$parametro1->addAttribute('nombre', $foranea["nombre"]);
					$parametro1->addAttribute('campo', $foranea["campo"]);
					$parametro1->addAttribute('campoDestino', $foranea["campoDestino"]);
					$parametro1->addAttribute('tablaDestino', $foranea["tablaDestino"]);
				}
			}
			
			$parametro = $consulta->addChild('Condiciones');
			$parametro = $parametro->addChild('Y');
			foreach($temp as $nom=>$val){
				$parametro = $parametro->addChild('Igual');
				$parametro->addAttribute('campo', $nom);
				$parametro->addAttribute('tabla', $this->nombretabla);
				$parametro->addAttribute('valor', $val);
			}

			$registros=$this->conexion->Consultar($consulta->asXML());
			
			/**/
			return array("Registros"=>$registros,"Campos"=>$campos);
		}
		
		function agregarCampoAContenidoTexto($XMLtexto,$titulo,$contenido){
			$parametro = $XMLtexto->addChild('Campo');
			$parametro->addAttribute('nombre', 'titulo');
			$parametro->addAttribute('valor', $titulo);
			$parametro->addAttribute('nivel', '4');
			$parametro = $XMLtexto->addChild('Campo');
			$parametro->addAttribute('nombre', 'contenido');
			$parametro->addAttribute('valor', $contenido);
		}
		
		function ProcesarFormulario_Consultar(){
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->consultar!="disabled"){
				$consulta=$this->consultarRegistros();
				
				$contenedorFormulario=$contenido->addChild("Contenedor");
				$contenedorFormulario->addAttribute("id", "procesoImec");
				$mens=$contenedorFormulario->addChild("Texto"); 
				$this->agregarCampoAContenidoTexto($mens,"","_caja id='procesoImec'__finCaja_");
				foreach($consulta["Registros"] as $registro){
					foreach($consulta["Campos"] as $titulo=>$campo){
						if ($campo["tipoCampo"]!="xml"){
							$this->agregarCampoAContenidoTexto($mens,$titulo,$registro["{$campo["nombreCampo"]}"]);
						}else{
							$this->agregarCampoAContenidoTexto($mens,$titulo,$this->geshiTexto($registro["{$campo["nombreCampo"]}"]));
						}
					}
				}
			}
			return $contenido;
		}

		function ProcesarFormulario_Anterior(){
			//echo "Llamando al anterior";
		}
		function ProcesarFormulario_Siguiente(){
			//echo "Llamando al siguiente";
		}
		function ProcesarFormulario_Saltar(){
			echo "Llamando al saltar";
		}
		
		function ProcesarFormulario_Nuevo($camposInvalidos=null){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::ProcesarFormulario_Nuevo()<br>"); }
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->nuevo!="disabled"){
				$xmltmp=$this->xml->xpath('Datos/Campo');
				$contenedorFormulario=$contenido->addChild("Contenedor");
				$contenedorFormulario->addAttribute("id", "procesoImec");
				$form=$contenedorFormulario->addChild("Formulario");
				$form->addAttribute('prefijo', "procesoImec");

				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'Metodo');
				$parametro->addAttribute('valor', 'POST');
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'idCasoUso');
				$parametro->addAttribute('valor', $this->idCasoUso);
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'Titulo');
				$parametro->addAttribute('valor', $this->titulo);
				if(is_array($xmltmp)){
					foreach($xmltmp as $nodo){
						if($nodo[0]['tipo']!="llavePrimariaAutonumerica"  && $nodo[0]['llavePrimaria']!="true"){
							if($nodo[0]['tipo']=="llaveForanea" || $nodo[0]['tipo']=="llavePrimariaForanea" || $nodo[0]['llaveForanea']=="true"){
								$parametro = $form->addChild('Campo');
								$parametro->addAttribute('titulo', (isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre']));
								$parametro->addAttribute('nombre', "imec_nuevo_".$nodo[0]['nombre']);
								$parametro->addAttribute('tipo', 'listaSeleccion');
								$xmlsql="<Consulta>
									<Campo nombre='".$nodo[0]['campoClaveForanea']."' tablaOrigen='".$nodo[0]['tablaClaveForanea']."' />
									<Campo nombre='".$nodo[0]['campoTextoClaveForanea']."' tablaOrigen='".$nodo[0]['tablaClaveForanea']."' />
									</Consulta>";
								$registros=$this->conexion->Consultar($xmlsql);
								if (strcmp($nodo[0]['requerido'], "false")==0){
									$subParametro=$parametro->addChild('Opcion');
									$subParametro->addAttribute('nombre',"");
									$subParametro->addAttribute('valor',"NULL");
								}
								foreach($registros as $registro){
									$a=$nodo[0]['campoClaveForanea'];
									$b=$nodo[0]['campoTextoClaveForanea'];
									$subParametro=$parametro->addChild('Opcion');
									//echo "Vamo a agregar ".$b." de ".$registro["$b"]." a algún lugar<br>";
									$subParametro->addAttribute('nombre',$registro["$b"]);
									$subParametro->addAttribute('valor',$registro["$a"]);
								}
								$a=$nodo[0]['nombre'];
								if(!is_null($camposInvalidos)){
									if($camposInvalidos["$a"][1]==true){
										$parametro->addAttribute('error', 'true');
									}
									$parametro->addAttribute('valorPorDefecto', $camposInvalidos["$a"][0]);
								}
							}else{
								$parametro = $form->addChild('Campo');
								$parametro->addAttribute('titulo', (isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre']));
								$parametro->addAttribute('nombre', "imec_nuevo_".$nodo[0]['nombre']);
								$parametro->addAttribute('tipo', $nodo[0]['tipo']);
								$parametro->addAttribute('filas','12');
								$parametro->addAttribute('columnas','80');
								$a=$nodo[0]['nombre'];
								if(!is_null($camposInvalidos)){
									if($camposInvalidos["$a"][1]==true){
										$parametro->addAttribute('error', 'true');
									}
									$parametro->addAttribute('valorPorDefecto', $camposInvalidos["$a"][0]);
								}
							}
						}
					}
					$parametro = $form->addChild('Campo');
					$parametro->addAttribute('titulo', 'Crear');
					$parametro->addAttribute('nombre', 'imec_accion');
					$parametro->addAttribute('tipo', 'enviar');
				}
			}else{
				$mens=$contenido->addChild("Texto"); 
				$parametro = $mens->addChild('Campo');
				$parametro->addAttribute('nombre', 'No tiene permisos para agregar registros');
				$parametro->addAttribute('nivel', '2');
			}
			return $contenido;
		}
		
		function ProcesarFormulario_Crear(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::ProcesarFormulario_Crear()<br>"); }
			$camposInvalidos=array();
			$error=false;
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->nuevo!="disabled"){
				$validar=new ValidarTipo();
				$mens=$contenido->addChild("Texto"); 
				if($this->nuevo!="disabled"){
					$xmltmp=$this->xml->xpath('Datos/Campo');
					if(is_array($xmltmp)){
						$consulta=new SimpleXMLElement("<Consulta />");
						foreach($xmltmp as $nodo){
							//echo revisarArreglo($nodo, "Nodo:");
							if($nodo[0]['tipo']!="llavePrimariaAutonumerica" && $nodo[0]['llavePrimaria']!="true"){
								try{
									if($validar->tipo($nodo[0]['tipo'],$this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']))){
										$campo=$consulta->addChild("Campo");
										$campo->addAttribute("nombre",$nodo[0]['nombre']);
										$campo->addAttribute("tablaOrigen",$this->nombretabla);
										$valor="";
										if($nodo[0]['tipo']=="clave"){
											$valor=md5($this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']));
										}else{
											$valor=$this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']);
										}
										$campo->addAttribute("valor",$valor);
									}
									$c=$nodo[0]['nombre'];
									$camposInvalidos["$c"]=array($this->sesion->leerParametroFormularioActual("imec_nuevo_".$c),false);
								}catch(ValorRequeridoNulo $ex){
									$error=true;
									$c=$nodo[0]['nombre'];
									$camposInvalidos["$c"]=array($this->sesion->leerParametroFormularioActual("imec_nuevo_".$c),true);
									new mensajes("El campo <b>[".(isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre'])."]</b> ".$ex->getMessage());
								}catch(TipoValorInvalido $ex){
									$error=true;
									$c=$nodo[0]['nombre'];
									$camposInvalidos["$c"]=array($this->sesion->leerParametroFormularioActual("imec_nuevo_".$c),true);
									new mensajes("El campo <b>[".(isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre'])."]</b> ".$ex->getMessage());
								} 
							}
						}
					}
				}
				if($error){
					return $this->ProcesarFormulario_Nuevo($camposInvalidos);
				}
				if($this->conexion->insertar($consulta->asXML())){
					$parametro = $mens->addChild('Campo');
					$parametro->addAttribute('nombre', 'titulo');
					$parametro->addAttribute('valor', 'El registro fue creado satisfactoriamente.');
					$parametro->addAttribute('nivel', '4');
				}else{								
					$parametro = $mens->addChild('Campo');
					$parametro->addAttribute('nombre', 'titulo');
					$parametro->addAttribute('valor', 'No se pudo crear');
					$parametro->addAttribute('nivel', '4');
				}
			}
			
			return $contenido;
		}
		
		function ProcesarFormulario_Editar($camposInvalidos=null){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::ProcesarFormulario_Editar()<br>"); }
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->editar!="disabled"){
				//Se sacan las llaves de la sesion y se guardan en (Array)$temp
				$llaves=new SimpleXMLElement((base64_decode($this->sesion->leerParametroFormularioActual("imec_llave"))));
				$listaLlaves=$llaves->xpath('//llave');
				foreach($listaLlaves as $llave){
					$a=$llave["nombre"];
					$temp["$a"]=$llave["valor"];
				}
				//Se crea el objeto xml para consultar en la BD
				$consulta=new SimpleXMLElement("<Consulta />");
				//Se revisa el formIMEC y se obtienen los Campos que se deben mostrar y se almacenan en $campos,
				//al mismo tiempo se agregan los campos al objeto XML consulta.
				$xmltmp=$this->xml->xpath('Datos/Campo');
				$campos=array();
				foreach($xmltmp as $nodo){
					$parametro = $consulta->addChild('Campo');
					$parametro->addAttribute('nombre', $nodo["nombre"]);
					$parametro->addAttribute('tablaOrigen', $this->nombretabla);
					if(strlen($nodo["titulo"])>0){
						$a=$nodo["titulo"];
						$campos["$a"]=$nodo["nombre"];							
					}else{
						$a=$nodo["nombre"];
						$campos["$a"]=$a;
					}
				}
				
				//Se agregan las llaves como condiciones de la consulta
				$parametro = $consulta->addChild('Condiciones');
				$parametroy = $parametro->addChild('Y');
				foreach($temp as $nom=>$val){
					$parametro = $parametroy->addChild('Igual');
					$parametro->addAttribute('campo', $nom);
					$parametro->addAttribute('tabla', $this->nombretabla);
					$parametro->addAttribute('valor', $val);
				}
				//Se realiza la consulta del registro indicado
				$registros=$this->conexion->Consultar($consulta->asXML());
				
				//Se crea el XMLFormulario respuesta y se le agregan las propidades de
				//metodo, caso de uso y titulo
				$contenedorFormulario=$contenido->addChild("Contenedor");
				$contenedorFormulario->addAttribute("id", "procesoImec");
				$form=$contenedorFormulario->addChild("Formulario");
				$form->addAttribute('prefijo', "procesoImec");
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'Metodo');
				$parametro->addAttribute('valor', 'POST');
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'idCasoUso');
				$parametro->addAttribute('valor', $this->idCasoUso);
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'Titulo');
				$parametro->addAttribute('valor', $this->titulo);
				$parametro = $form->addChild('Campo');
				$parametro->addAttribute('valorPorDefecto', $this->sesion->leerParametroFormularioActual("imec_llave"));
				$parametro->addAttribute('nombre', "imec_llave");
				$parametro->addAttribute('tipo', 'oculto');
				if(is_array($xmltmp)){
					foreach($xmltmp as $nodo){
						if($nodo[0]['tipo']!="llavePrimariaAutonumerica" && $nodo[0]['llavePrimaria']!="true"){
							if($nodo[0]['tipo']=="llaveForanea"  || $nodo[0]['tipo']=="llavePrimariaForanea" || $nodo[0]['llaveForanea']=="true"){
								$parametro = $form->addChild('Campo');
								$parametro->addAttribute('titulo', (isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre']));
								$parametro->addAttribute('nombre', "imec_nuevo_".$nodo[0]['nombre']);
								$parametro->addAttribute('tipo', 'listaSeleccion');
								$a=$nodo[0]['nombre'];
								if(!is_null($camposInvalidos)){
									if($camposInvalidos["$a"][1]==true){
										$parametro->addAttribute('error', 'true');
										$parametro->addAttribute('valorPorDefecto', $camposInvalidos["$a"][0]);
									}else{
										$parametro->addAttribute('valorPorDefecto', $registros[0]["$a"]);
									}
								}else{
									$parametro->addAttribute('valorPorDefecto', $registros[0]["$a"]);
								}
								$conForKey=new SimpleXMLElement("<Consulta />");
								$parametroFK = $conForKey->addChild('Campo');
								$parametroFK->addAttribute('nombre', $nodo[0]['campoClaveForanea']);
								$parametroFK->addAttribute('tablaOrigen', $nodo[0]['tablaClaveForanea']);
								$parametroFK = $conForKey->addChild('Campo');
								$parametroFK->addAttribute('nombre', $nodo[0]['campoTextoClaveForanea']);
								$parametroFK->addAttribute('tablaOrigen', $nodo[0]['tablaClaveForanea']);
								$registrosFK=array();
								$registrosFK=$this->conexion->Consultar($conForKey->asXML());
								foreach($registrosFK as $registro){
									$a=$nodo[0]['campoClaveForanea'];
									$b=$nodo[0]['campoTextoClaveForanea'];
									$subParametro=$parametro->addChild('Opcion');
									$subParametro->addAttribute('nombre',$registro["$b"]);
									$subParametro->addAttribute('valor',$registro["$a"]);
								}
							}else{
								$parametro = $form->addChild('Campo');
								$parametro->addAttribute('titulo', (isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre']));
								$parametro->addAttribute('nombre', "imec_nuevo_".$nodo[0]['nombre']);
								$a=$nodo[0]['nombre'];
								if(!is_null($camposInvalidos)){
									if($camposInvalidos["$a"][1]==true){
										$parametro->addAttribute('error', 'true');
										$parametro->addAttribute('valorPorDefecto', $camposInvalidos["$a"][0]);
									}else{
										if ($nodo[0]['tipo']!="clave"){
											$parametro->addAttribute('valorPorDefecto', $registros[0]["$a"]);
										}else{
											$parametro->addAttribute('valorPorDefecto', "");
										}
									}
								}else{
									if ($nodo[0]['tipo']!="clave"){
										$parametro->addAttribute('valorPorDefecto', $registros[0]["$a"]);
									}else{
										$parametro->addAttribute('valorPorDefecto', "");
									}
								}
								$parametro->addAttribute('tipo', $nodo[0]['tipo']);
								$parametro->addAttribute('filas','12');
								$parametro->addAttribute('columnas','80');
							}
						}
					}
					$parametro = $form->addChild('Campo');
					$parametro->addAttribute('titulo', 'Salvar');
					$parametro->addAttribute('nombre', 'imec_accion');
					$parametro->addAttribute('tipo', 'enviar');
				}
			}else{
				$mens=$contenido->addChild("Texto"); 
				$parametro = $mens->addChild('Campo');
				$parametro->addAttribute('nombre', 'No tiene permisos para agregar registros');
				$parametro->addAttribute('nivel', '2');
			}
			return $contenido;
		}
		
		function ProcesarFormulario_Salvar(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::ProcesarFormulario_Salvar()<br>"); }
			$camposInvalidos=array();
			$error=false;
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->editar!="disabled"){
				$validar=new ValidarTipo();
				$temp=$this->getLlaves();
				$campos=$this->getCampos();
				$mens=$contenido->addChild("Texto"); 
				if($this->nuevo!="disabled"){
					$xmltmp=$this->xml->xpath('Datos/Campo');
					if(is_array($xmltmp)){
						$consulta=new SimpleXMLElement("<Consulta />");
						foreach($xmltmp as $nodo){
							//echo "Tratando de editar : ".revisarArreglo($nodo[0], "arreglo")." con ".$nodo[0]['llavePrimaria']."<br>";
							if($nodo[0]['tipo']!="llavePrimariaAutonumerica" && $nodo[0]['llavePrimaria']!="true"){
								try{
									//echo $this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']);
									if($validar->tipo($nodo[0]['tipo'],$this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']))){
										//$total.=$nodo[0]['nombre']."=".$this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre'])."<hr>";
										$valor=$this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']);
										if(strcmp($nodo[0]['tipo'],"clave")!=0 || strlen($valor)!=0){
											$campo=$consulta->addChild("Campo");
											$campo->addAttribute("nombre",$nodo[0]['nombre']);
											$campo->addAttribute("tablaOrigen",$this->nombretabla);
											if($nodo[0]['tipo']=="clave"){
												$valor=md5($valor);
											}
											$campo->addAttribute("valor",$valor);
										}
									}
									$c=$nodo[0]['nombre'];
									$camposInvalidos["$c"]=array($this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']),false);
								}catch(ValorRequeridoNulo $ex){
									$error=true;
									$c=$nodo[0]['nombre'];
									$camposInvalidos["$c"]=array($this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']),true);
									new mensajes("El campo <b>[".(isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre'])."]</b> ".$ex->getMessage());
								}catch(TipoValorInvalido $ex){
									$error=true;
									$c=$nodo[0]['nombre'];
									$camposInvalidos["$c"]=array($this->sesion->leerParametroFormularioActual("imec_nuevo_".$nodo[0]['nombre']),true);
									new mensajes("El campo <b>[".(isset($nodo[0]['titulo'])?$nodo[0]['titulo']:$nodo[0]['nombre'])."]</b> ".$ex->getMessage());
								} 
							}
						}
					}
				}
				
				if($error){
					return $this->ProcesarFormulario_Editar($camposInvalidos);
				}
				
				//Se agregan las llaves como condiciones de la consulta
				$parametro = $consulta->addChild('Condiciones');
				$parametroy = $parametro->addChild('Y');
				foreach($temp as $nom=>$val){
					$parametro = $parametroy->addChild('Igual');
					$parametro->addAttribute('campo', $nom);
					$parametro->addAttribute('tabla', $this->nombretabla);
					$parametro->addAttribute('valor', $val);
				}
				
				//echo "Se va a actualizar esto: ".$consulta->asXML();
				if($this->conexion->actualizar($consulta->asXML())){
					//echo "se actualizo ejecutando esto: ".$this->conexion->sql."<br>";
					$parametro = $mens->addChild('Campo');
					$parametro->addAttribute('nombre', 'titulo');
					$parametro->addAttribute('valor', 'El registro fue actualizado satisfactoriamente.');
					$parametro->addAttribute('nivel', '4');
				}else{								
					$parametro = $mens->addChild('Campo');
					$parametro->addAttribute('nombre', 'titulo');
					$parametro->addAttribute('valor', 'No se pudo actualizar');
					$parametro->addAttribute('nivel', '4');
				}
			}
			return $contenido;
		}
		
		function ProcesarFormulario_Borrar(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::ProcesarFormulario_Borrar()<br>"); }
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->consultar!="disabled"){
				$consulta=$this->consultarRegistros();
				$contenedorFormulario=$contenido->addChild("Contenedor");
				$contenedorFormulario->addAttribute("id", "procesoImec");
				$mens=$contenedorFormulario->addChild("Texto"); 
				foreach($consulta["Registros"] as $registro){
					foreach($consulta["Campos"] as $titulo=>$campo){
						if ($campo["tipoCampo"]!="xml"){
							$this->agregarCampoAContenidoTexto($mens,$titulo,$registro["{$campo["nombreCampo"]}"]);
						}else{
							$this->agregarCampoAContenidoTexto($mens,$titulo,$this->geshiTexto($registro["{$campo["nombreCampo"]}"]));
						}
					}
				}
				$form=$contenido->addChild("Formulario");
				$form->addAttribute('prefijo', "procesoImec");
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'Metodo');
				$parametro->addAttribute('valor', 'POST');
				$parametro = $form->addChild('Propiedad');
				$parametro->addAttribute('nombre', 'idCasoUso');
				$parametro->addAttribute('valor', $this->idCasoUso);
				$parametro = $form->addChild('Campo');
				$parametro->addAttribute('valorPorDefecto', $this->sesion->leerParametroFormularioActual("imec_llave"));
				$parametro->addAttribute('nombre', "imec_llave");
				$parametro->addAttribute('tipo', 'oculto');
				$parametro = $form->addChild('Campo');
				$parametro->addAttribute('titulo', 'Eliminar');
				$parametro->addAttribute('nombre', 'imec_accion');
				$parametro->addAttribute('tipo', 'enviar');
			}
			return $contenido;
		}
		
		function ProcesarFormulario_Eliminar(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::ProcesarFormulario_Eliminar()<br>"); }
			$contenido=new SimpleXMLElement("<Imec />");
			if($this->editar!="disabled"){
				//Se sacan las llaves de la sesion y se guardan en (Array)$temp
				$temp=$this->getLlaves();
				$consulta=new SimpleXMLElement("<Consulta />");
				$parametro = $consulta->addChild('Campo');
				$parametro->addAttribute('nombre', '');
				$parametro->addAttribute('tablaOrigen', $this->nombretabla);
				//Se agregan las llaves como condiciones de la consulta
				$parametro = $consulta->addChild('Condiciones');
				$parametroy = $parametro->addChild('Y');
				foreach($temp as $nom=>$val){
					$parametro = $parametroy->addChild('Igual');
					$parametro->addAttribute('campo', $nom);
					$parametro->addAttribute('tabla', $this->nombretabla);
					$parametro->addAttribute('valor', $val);
				}
				$mens=$contenido->addChild("Texto"); 
				if($this->conexion->eliminar($consulta->asXML())){
					$parametro = $mens->addChild('Campo');
					$parametro->addAttribute('nombre', 'titulo');
					$parametro->addAttribute('valor', 'El registro fue eliminado satisfactoriamente.');
					$parametro->addAttribute('nivel', '4');
				}else{								
					$parametro = $mens->addChild('Campo');
					$parametro->addAttribute('nombre', 'titulo');
					$parametro->addAttribute('valor', 'No se pudo eliminar');
					$parametro->addAttribute('nivel', '4');
				}
			}
			return $contenido;
		}
		function getCampos(){
			if($GLOBALS["debug"]>0){ registrarlog("->XMLIMEC::getCampos()<br>"); }
			$campos=array();
			$valores=array("nombre","valor","titulo");
			$xmltmp=$this->xml->xpath('Datos/Campo');
			if(is_array($xmltmp)){
				foreach($xmltmp as $nodo){
					$arrtmp=array();
					foreach($valores as $nombre){
						$tmp=$nodo[0]["$nombre"];
						settype($tmp, "string");
						$arrtmp["$nombre"]=$tmp;
					}
					$campos[]=$arrtmp;
				}
			}
			return $campos;
		}
	}
	
	
	

?>
