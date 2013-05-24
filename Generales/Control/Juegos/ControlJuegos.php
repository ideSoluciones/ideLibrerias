<?php
	class ControlJuegos{
		private $sesion;
		private $clase;
		private $nivel;
		public $rolDeRegistro=0;
		private $juegos=array(
			"Sudoku"=>array("clase"=>"CSudoku"),
		);		
		function ControlJuegos($propiedades=array()){
			$this->sesion=Sesion::getInstancia();
			if(isset($propiedades["nivel"])){
				$this->nivel=$propiedades["nivel"];
			}else{
				$nivel=$this->sesion->leerParametroInterno("ControlJuegos","nivel");
				if(strcmp($nivel,"")!=0){
					$this->nivel=intval($nivel);
				}else{
					$this->nivel=-1;
				}
			}
			if(isset($propiedades["rolDeRegistro"])){
				$this->rolDeRegistro=$propiedades["rolDeRegistro"];
			}
			if(isset($propiedades["juego"])){
				$clase=$this->getClaseJuego($propiedades["juego"]);
				if(class_exists($clase)){
					$this->clase=new $clase();
					$this->clase->setNivel($nivel);
				}else{
					mensaje::add("La clase [$clase] no existe.",ERROR);
				}
			}else{
				$juego=$this->sesion->leerParametroInterno("ControlJuegos","juego");
				//mensaje::add($juego);
				if(strcmp($juego,"")!=0){
					$clase=$this->getClaseJuego((string)$juego);
					if(class_exists($clase)){
						$this->clase=new $clase();
						$this->clase->setNivel($nivel);
					}else{
						$this->clase=null;
					}
				}else{
					$this->clase=null;
				}
			}
			//mensaje::add("clase:".print_r($this->clase,true));
		}
		function getClaseJuego($juego){
			if(isset($this->juegos[$juego])){
				return $this->juegos[$juego]["clase"];
			}
			return false;
		}
		function generarContenido($xml,$parametros=null){
			return $this->clase->generarContenido($xml,$parametros);
		}
		function jugar($xml, $parametros){
			//mensaje::add(generalXML::geshiXML($this->sesion->xml));
			if(intval($this->sesion->leerParametro("idUsuario"))==1){
				$contenedor=ControlXML::agregarNodo($xml,"Contenedor",array("textoModal"=>"Para jugar registrate aquí!","titulo"=>"Registro de nuevo usuario","icono"=>resolverPath()."/../Librerias/img/ok.png","propiedadesIcono"=>"class='' style='padding-top:3px;' width='20'","ancho"=>"570","alto"=>"400"));
				$tabs=ControlXML::agregarNodo($contenedor, "Tabs");
				$nodo=ControlXML::agregarNodo($tabs, "Nodo", array("titulo"=>"Nuevo Usuario"));
				$mostrarMensaje=Control0Usuario::procesarFormularioNuevoUsuario($nodo,$this->rolDeRegistro,$parametros,"Activación completa, ahora puede ingresar el sistema.",true);
				$nodo=ControlXML::agregarNodo($tabs, "Nodo", array("titulo"=>"Ingreso Usuario"));
				$paqueteUsuario= new PaqueteUsuario();
				$paqueteUsuario->generarContenido_login(null, $nodo, $parametros["nombreCasoUso"]);
				
				
				if(!$mostrarMensaje)
					$contenedor->addAttribute("modal","true");
			}else{
				$operacion=$this->sesion->leerParametroFormularioActual("operacion");
				if(strcmp($this->sesion->leerParametroFormularioActual("operacion"),"seleccionarNivel")==0){
					$this->sesion->escribirParametroInterno("ControlJuegos","nivel",$this->sesion->leerParametroDestinoActual("nivel"));
					$this->nivel=intval($this->sesion->leerParametroDestinoActual("nivel"));
				}
				if(strcmp($this->sesion->leerParametroFormularioActual("operacion"),"seleccionarJuego")==0){
					$this->sesion->escribirParametroInterno("ControlJuegos","juego",$this->sesion->leerParametroDestinoActual("juego"));
					$clase=$this->getClaseJuego((string)$this->sesion->leerParametroDestinoActual("juego"));
					if(class_exists($clase)){
						$this->clase=new $clase();
						$this->clase->setNivel($this->nivel);
					}else{
						$this->clase=null;
					}
				}
				if(strcmp($this->sesion->leerParametroFormularioActual("operacion"),"seleccionarNuevoJuego")==0){
					$this->sesion->borrarParametroInterno("ControlJuegos","juego");
					$this->sesion->borrarParametroInterno("ControlJuegos","nivel");
					$this->clase->reset();
					$this->clase=null;
					$this->nivel=-1;
				}
			}
			if(is_null($this->clase)){
				$formulario=ControlFormulario::generarFormulario($xml,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso")));
				ControlFormulario::generarCampo($formulario,array("nombre"=>"operacion","tipo"=>"oculto","valorPorDefecto"=>"seleccionarJuego"));
				foreach($this->juegos as $juego){
					if(class_exists($juego["clase"])){
						$obj=new $juego["clase"]();
						$contenedor=ControlXML::agregarNodo($formulario,"Contenedor",array("estilo"=>"border:1px solid;padding:15px;text-align:justify;"));
						ControlFormulario::generarEnviar($contenedor,array("titulo"=>$obj->getNombre(),"nombre"=>"juego"));
						$obj->getPresentacion($contenedor);
					}
				}
			}else{
				if($this->nivel<0){
					$niveles=$this->clase->getNiveles();
					$formulario=ControlFormulario::generarFormulario($xml,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso")));
					ControlFormulario::generarCampo($formulario,array("nombre"=>"operacion","tipo"=>"oculto","valorPorDefecto"=>"seleccionarNivel"));
					ControlXML::agregarNodoTexto($formulario,"Wiki","=Seleccione un nivel=\n");
					foreach($niveles as $nivel){
						ControlFormulario::generarEnviar($formulario,array("titulo"=>$nivel,"nombre"=>"nivel"));
					}
				}else{
					$formulario=ControlFormulario::generarFormulario($xml,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso")));
					ControlFormulario::generarCampo($formulario,array("nombre"=>"operacion","tipo"=>"oculto","valorPorDefecto"=>"seleccionarNuevoJuego"));
					ControlFormulario::generarEnviar($formulario,array("titulo"=>"Seleccionar otro juego"));
					$wiki=ControlXML::agregarNodoTexto($xml,"Wiki","------\n");
					$respuesta=$this->clase->procesarFormulario();
					if(is_array($respuesta)){
						if(isset($respuesta[0])){
							if($respuesta[0]){
								mensaje::add("Felicitaciones has ganado el juego!");
								$dao=new DAO1Puntaje($this->sesion->getDB());
								$vo=new VO1Puntaje();
								$vo->setIdUsuario($this->sesion->leerParametro("idUsuario"));
								$vo->setTiempo($this->clase->tiempo);
								$vo->setPuntaje($this->clase->puntaje);
								$vo->setNivel($this->clase->nivel);
								$vo->setNombreJuego($this->clase->getNombre());
								$vo->setXmlPropiedades($this->clase->propiedades);
								$dao->agregarRegistro($vo);
								$this->generarContenido($xml,siEsta($respuesta[1],null));
							}else{
								$this->generarContenido($xml,siEsta($respuesta[1],null));
							}
						}else{
							mensaje::add("La respuesta de ".get_class($this->clase)."::procesarFormulario()  no contiene los datos necesarios.",ERROR);
						}
					}else{
						mensaje::add("La respuesta de ".get_class($this->clase)."::procesarFormulario()  no es valida.",ERROR);
					}
				}
			}
		}
	}
?>
