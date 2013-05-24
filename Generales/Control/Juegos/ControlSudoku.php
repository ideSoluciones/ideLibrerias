<?php
	define("SUDOKU_EASY",1,true);
	define("SUDOKU_MEDIUM",2,true);
	define("SUDOKU_HARD",3,true);
	define("SUDOKU_VERY_HARD",4,true);
	class CSudoku{
		private $path;
		private $sesion;
		public $no;
		public $nivel;
		public $puntaje=0;
		public $tiempo=0;
		public $propiedades="";
		function CSudoku($nivel=SUDOKU_EASY){
			$this->sesion=Sesion::getInstancia();
			$this->nivel=$nivel;
			$this->path=$this->sesion->leerParametro("pathServidor");
		}
		function setNivel($nivel){
			if(intval($nivel)>0 && intval($nivel)<=4){
				$this->nivel=$nivel;
			}			
		}
		function reset(){
			$this->sesion->borrarParametroInterno("sudoku","horaInicio");
		}
		function getNombre(){
			return "Sudoku";
		}
		function getNiveles(){
			return array(SUDOKU_EASY,SUDOKU_MEDIUM,SUDOKU_HARD,SUDOKU_VERY_HARD);
		}
		function getPresentacion($xml){
			$tabla=ControlXML::agregarNodo($xml,"Tabla");
			$fila=ControlXML::agregarNodo($tabla,"Fila");
			$wiki=ControlXML::agregarNodoTexto($fila,"Wiki","=Sudoku=\n");
			$wiki[].="http://upload.wikimedia.org/wikipedia/commons/1/13/Sudoku-by-L2G-20050714.gif\n <div style='text-align:justify;'>'''Sudoku''' (en japonés: 数独, sūdoku) es un pasatiempo  que se popularizó en Japón en 1986, y se dio a conocer en el ámbito internacional en 2005. El objetivo es rellenar una cuadrícula de 9 × 9 celdas (81 casillas) dividida en subcuadrículas de 3 × 3 (también llamadas \"cajas\" o \"regiones\") con las cifras del 1 al 9 partiendo de algunos números ya dispuestos en algunas de las celdas. Aunque se podrían usar colores, letras, figuras, se conviene en usar números para mayor claridad. Lo que importa, en todo caso, es que sean nueve elementos diferenciados. No se debe repetir ninguna cifra en una misma fila, columna o subcuadrícula. Un sudoku está bien planteado si la solución es única. La resolución del problema requiere paciencia y ciertas dotes lógicas.</div>\n";
			$wiki[].="[http://es.wikipedia.org/wiki/Sudoku Ver más...]";
			try{
				$daoPuntaje=new DAO1Puntaje($this->sesion->getDB());
				$consulta=new SimpleXMLElement("<Consulta />");
				$condicionesConsulta=ControlXML::agregarNodo($consulta,"Condiciones");
				ControlXML::agregarNodo($condicionesConsulta,"Igual",array("campo"=>"nombreJuego","tabla"=>"1Puntaje","valor"=>"Sudoku"));
				$ordenar=ControlXML::agregarNodo($consulta,"Ordenar");
				ControlXML::agregarNodo($ordenar,"OrdenarCampo",array("campo"=>"tiempo","modo"=>"ASC"));
				$puntajes=$daoPuntaje->getRegistros($consulta);
				$wiki=ControlXML::agregarNodoTexto($fila,"Wiki","=Mejores puntajes=\n");
				$wiki[].="<div style='width:170px;'>\n{|\n";
				$tabPuntaje=array();
				$daoUsuario= new DAO0Usuario($this->sesion->getDB());
				foreach($puntajes as $puntaje){
					try{
						$usuario=$daoUsuario->getRegistro($puntaje->getIdUsuario());
						$tabPuntaje[]="| ".$usuario->getUser()."\n| ".ceil($puntaje->getTiempo()/60)." min\n";
					}catch(Exception $e){}
				}
				$wiki[].=implode("|-\n",$tabPuntaje);
				$wiki[].="|}\n</div>";
			}catch(Exception $e){}
		}
		
		
		function formularioSeleccionDeSudoku($xml){
			$file=$this->obtenerPathCompleto($this->nivel);
			if(file_exists($file)){
				$read = explode("\n",file_get_contents($file));
			}else{
				mensaje::add("La base de datos no existe ó el nivel no esta contemplado.",ERROR);
				$read =array();
			}
			$formulario=ControlFormulario::generarFormulario($xml,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso")));
			ControlFormulario::generarCampo($formulario,array("titulo"=>"Especifique el número de sudoku a jugar(0 - ".(count($read)-2).")","nombre"=>"no","tipo"=>"entero","minimo"=>0,"maximo"=>count($read)-2,"valorPorDefecto"=>0,"style"=>"width:30px;"));
			ControlFormulario::generarEnviar($formulario,array("titulo"=>"Jugar con el seleccionado","nombre"=>"forma"));
			ControlFormulario::generarEnviar($formulario,array("titulo"=>"Jugar con uno aleatorio","nombre"=>"forma"));
		}
		
		function generarContenido($xml,$sudokuActual=null){
			$tiempo=$this->sesion->leerParametroInterno("sudoku","horaInicio");
			if(strcmp($tiempo,"")==0){
				$this->sesion->escribirParametroInterno("sudoku","horaInicio",time());
			}
			$imprimirTablero=true;
			/*
			if(strcmp($this->sesion->leerParametroDestinoActual("forma"),"")!=0){
				switch($this->sesion->leerParametroDestinoActual("forma")){
					case "Jugar con el seleccionado":
						if(!$sudoku=$this->obtenerSudoku($this->sesion->leerParametroDestinoActual("no"))){
							$this->formularioSeleccionDeSudoku($xml);
							$imprimirTablero=false;
						}
						break;
					case "Jugar con uno aleatorio":
						$sudoku=$this->obtenerSudokuAleatorio();
						break;
				}
			}else{*/
				if(!is_null($sudokuActual)){
					$sudoku=$this->obtenerSudoku($this->no);
				}else{
					/*$this->formularioSeleccionDeSudoku($xml);
					$imprimirTablero=false;*/
					$sudoku=$this->obtenerSudokuAleatorio();
				}
			//}
			if($imprimirTablero){
				
				ControlXML::agregarNodoTexto($xml,"Wiki","=SUDOKU {$this->no}=");
				$formulario=ControlFormulario::generarFormulario($xml,array("idCasoUso"=>$this->sesion->leerParametro("idCasoUso")));
				ControlFormulario::generarCampo($formulario,array("nombre"=>"no","tipo"=>"oculto","valorPorDefecto"=>$this->no));
				ControlFormulario::generarCampo($formulario,array("nombre"=>"operacion","tipo"=>"oculto","valorPorDefecto"=>"calificarSudoku"));
				ControlFormulario::generarCampo($formulario,array("nombre"=>"nivel","tipo"=>"oculto","valorPorDefecto"=>$this->nivel));
			
				$tabSudoku=ControlXML::agregarNodo($formulario,"Sudoku",array());
				foreach($sudoku as $i=>$celda){
					if(intval($celda)==0){
						$valorPorDefecto="";
						if(isset($sudokuActual[$i])){
							$valorPorDefecto=intval($sudokuActual[$i]);
							if($valorPorDefecto==0) $valorPorDefecto="";
						}
						ControlXML::agregarNodo($tabSudoku,"Casilla",array("valor"=>$valorPorDefecto,"tipo"=>"v","nombre"=>"campo".$i));
					}else{
						ControlXML::agregarNodo($tabSudoku,"Casilla",array("valor"=>$celda,"tipo"=>"f"));
					}
				}

				ControlFormulario::generarEnviar($formulario,array("titulo"=>"Enviar"));
			}
		}
		
		function procesarFormulario(){
			$operacion=$this->sesion->leerParametroFormularioActual("operacion");
			if(strcmp($this->sesion->leerParametroFormularioActual("operacion"),"calificarSudoku")==0){
				$this->no=$this->sesion->leerParametroFormularioActual("no");
				$sudoku=$this->obtenerSudoku($this->no);
				$parametros=$this->sesion->leerParametrosDestinoActual("/^campo/");
				$error=false;
				foreach($parametros as $nombre=>$parametro){
					$id=intval(substr($nombre,5));
					if($parametro==0){$error=true;}
					$sudoku[$id]=$parametro;
				}
				$gano=true;
				if($error){
					mensaje::add("Faltan campos por llenar",ERROR);
					$gano=false;
				}
				for($i=0;$i<9;$i++){
					$mi=(($i%3)*3)+(3*intval($i/3)*9);
					for($j=0;$j<9-1;$j++){
						$mj=$mi+(($j%3))+(intval($j/3)*9);
						$columna=(3*($i%3))+($j%3)+1;
						$fila=intval($j/3)+(3*($i%3))+1;
						if(intval($sudoku[$mj])<0 || intval($sudoku[$mj])>9){
							mensaje::add("#Error, fila $fila columna $columna tiene valor fuera del rango({$sudoku[$mj]}).",ERROR);
							$gano=false;
						}
						for($k=$j+1;$k<9;$k++){
							$columna2=(3*($i%3))+($k%3)+1;
							$fila2=intval($k/3)+(3*($i%3))+1;
							$mk=$mi+(($k%3))+(intval($k/3)*9);
							if($sudoku[$mj]==$sudoku[$mk] && intval($sudoku[$mj])!=0 && intval($sudoku[$mk])!=0){
								mensaje::add("*Error, fila $fila columna $columna y fila $fila2 columna $columna2 tienen valor {$sudoku[$mj]}.",ERROR);
								$gano=false;
							}
							if($sudoku[$i*9+$j]==$sudoku[$i*9+$k] && intval($sudoku[$i*9+$j])!=0 && intval($sudoku[$i*9+$k])!=0){
								mensaje::add("-Error, fila ".($i+1)." columna ".($j+1)." y fila ".($i+1)." columna ".($k+1)." tienen valor {$sudoku[$i*9+$j]}.",ERROR);
								$gano=false;
							}
							if($sudoku[$j*9+$i]==$sudoku[$k*9+$i] && intval($sudoku[$j*9+$i])!=0 && intval($sudoku[$k*9+$i])!=0){
								mensaje::add("|Error, fila ".($j+1)." columna ".($i+1)." y fila ".($k+1)." columna ".($i+1)." tienen valor {$sudoku[$j*9+$i]}.",ERROR);
								$gano=false;
							}
						}
					}
				}
				if($gano){
					$tiempoFinal=time();
					$tiempoInicial=doubleval($this->sesion->leerParametroInterno("sudoku","horaInicio"));
					$this->sesion->borrarParametroInterno("sudoku","horaInicio");
					$this->tiempo=$tiempoFinal-$tiempoInicial;
					return array(true,$sudoku);
				}
				return array(false,$sudoku);
			}
			return array(false,null);
		}
		
		function obtenerSudoku($no,$sudokus=null){
			$sudoku=array();
			if(is_null($sudokus)){
				$file=$this->obtenerPathCompleto($this->nivel);
				if(file_exists($file)){
					$read = explode("\n",file_get_contents($file));
				}else{
					mensaje::add("La base de datos no existe ó el nivel no esta contemplado.",ERROR);
					$read =array();
				}
			}else{
				if(is_array($sudokus)){
					$read = $sudokus;
				}else{
					mensaje::add("El sudoku enviado no tiene formato valido [obtenerSudoku parámetro 2].",ERROR);
					$read =array();
				}
			}
			if((count($read)-2)>0){
				if(isset($read[intval($no)])){
					$tmp=explode("\t",$read[intval($no)]);
					if(is_array($tmp)){
						if(count($tmp)==2){
							$sudoku=explode(" ",$tmp[0]);
							$this->no=intval($no);
							$propiedades=new SimpleXMLElement("<Propiedades><Propiedad nombre='noSudoku' valor='{$this->no}' /><Propiedad nombre='nivel' valor='{$tmp[1]}' /></Propiedades>");
							$this->propiedades=$propiedades->asXML();
						}
					}
				}else{
					mensaje::add("El sudoku solicitado no existe.");
					return false;
				}
			}
			//mensaje::add("Total:".count($sudoku).print_r($sudoku,true));
			return $sudoku;
		}
		
		function obtenerSudokuAleatorio(){
			$sudoku=array();
			$file = $file=$this->obtenerPathCompleto($this->nivel);
			if(file_exists($file)){
				$read = explode("\n",file_get_contents($file));
				if((count($read)-1)>0){
					$no=mt_rand(0,count($read)-1);
					$this->no=$no;
					$sudoku=$this->obtenerSudoku($no,$read);
				}else{
					mensaje::add("La base de datos esta vacía.",ERROR);
				}
			}else{
				mensaje::add("La base de datos no existe ó el nivel no esta contemplado.",ERROR);
			}
			return $sudoku;
		}
		
		function obtenerPathCompleto($nivel){
			$archivo="none";
			switch($nivel){
				case SUDOKU_EASY:
					$archivo="easy";
					break;
				case SUDOKU_MEDIUM:
					$archivo="medium";
					break;
				case SUDOKU_HARD:
					$archivo="hard";
					break;
				case SUDOKU_VERY_HARD:
					$archivo="very_hard";
					break;
			}
			return $this->path."/../Librerias/Generales/Control/Juegos/DatosSudoku/".$archivo;
		}
	}
?>
