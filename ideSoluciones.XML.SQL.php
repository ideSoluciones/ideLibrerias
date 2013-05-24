<?php

	require_once("ideSoluciones.XML.general.php");
	include_once("ideSoluciones.php.general.php");
	
	define("MOTOR_MySQL", "MOTOR_MySQL");
	define("MySQL_CONSULTA","MySQL_CONSULTA");
	define("MySQL_ACTUALIZACION","MySQL_ACTUALIZACION");
	define("MySQL_ELIMINACION","MySQL_ELIMINACION");
	define("MySQL_EJECUTAR","MySQL_EJECUTAR");
	define("MySQL_INSERCION","MySQL_INSERCION");
	
	define("MOTOR_SQLite", "MOTOR_SQLite");
	define("SQLite_CONSULTA","SQLite_CONSULTA");
	define("SQLite_ACTUALIZACION","SQLite_ACTUALIZACION");
	define("SQLite_ELIMINACION","SQLite_ELIMINACION");
	define("SQLite_EJECUTAR","SQLite_EJECUTAR");
	define("SQLite_INSERCION","SQLite_INSERCION");
	
	/**
	*    @name XMLSQL
	*    @abstract	Funcion que examina un array y lo desglosa en un mensaje de drupal.
	*				$value: Es el arreglo a examinar.
	*				$nom: Es el titulo con el que se muestra el resultado.
	*    @author Felipe Cano <felipe.cano@idesoluciones.com >
	*    @param array,string $value,$nom
	*    @version 0.4
	*/
		
	class XMLSQL extends generalXML{
	
		var $nombreBD;
		var $userBD;
		var $passBD;
		var $hostBD;
		var $conn;
		var $tipo;
		var $registros;
		var $Error;
		public $sql;
		var $ultimoId;
		
		private $prefijoTabla;

		function setPrefijoTabla($prefijoTabla){
			$this->prefijoTabla=$prefijoTabla;
		}
		function getPrefijoTabla(){
			return $this->prefijoTabla;
		}
		
		function XMLSQL($nombreBD, $userBD, $passBD, $hostBD, $motor, $prefijoTabla=""){
			$this->nombreBD = $nombreBD;
			$this->userBD = $userBD;
			$this->passBD= $passBD;
			$this->hostBD= $hostBD;
			$this->registros=array();
			$this->Error=0;
			$this->ultimoId=0;
			$this->setPrefijoTabla($prefijoTabla);
			switch($motor){
				case MOTOR_MySQL:
					//mysqli_report(MYSQLI_REPORT_ALL);
					$this->conexionMySQL();
					$this->tipo=MOTOR_MySQL;
					break;
				case MOTOR_SQLite:
					$this->conexionSQLite();
					$this->tipo=MOTOR_SQLite;
					break;
				default:
					$this->Error=1;
			}
		}
		
		function conexionMySQL(){
			try{
				$this->conn = new mysqli($this->hostBD, $this->userBD, $this->passBD, $this->nombreBD);
				if (mysqli_connect_errno()) {
					if (mysqli_connect_errno()==1049){
						asercion("No existe la base de datos ".$this->nombreBD.".");
					}else{
						asercion($this->errorToString(4)."<br>[".mysqli_connect_errno().", ".mysqli_connect_error()."]");
					}
				}
			}catch(Exception $e){
				throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."].");
				asercion($this->errorToString(4)."<br>[".$e->getMessage()."]");
			}
		}

		function conexionSQLite(){
			try{
				$this->conn = new SQLiteDatabase($this->hostBD."/".$this->nombreBD);
				if(!$this->conn){
				//if (!$this->conn = sqlite_open($this->hostBD."/".$this->nombreBD, 0666, $sqliteerror)) {
					//throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."].");
					asercion($this->errorToString(4)."<br>[".$sqliteerror."]");
				}
			}catch(Exception $e){
				//throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."].");
				asercion($this->errorToString(4)."<br>[".$e->getMessage()."]");
			}
		}

		function consultar($XMLSQL,$encode=false, $debug=false){
			$this->registros=array();
			if($this->Error!=4){
				switch($this->tipo){
					case MOTOR_MySQL:
						try{
							$this->sql=$this->XMLSQL_To_MySQL($XMLSQL,MySQL_CONSULTA);
							registrarlog("<br><b>SQL:</b>".$this->sql);
							if ($debug)
								msg::add($this->sql);
							if($resultado=$this->conn->query($this->sql)){
								if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)."]");
/*								if (strncmp($this->sql,"SELECT sistemaVET_VistaBoletas.* FROM sistemaVET_VistaBoletas WHERE (sistemaVET_VistaBoletas.idEvento = '6' AND (sistemaVET_VistaBoletas.idEstado = '7' OR sistemaVET_VistaBoletas.idEstado = '12')) ;", 150)==0){
									echo $this->sql,"<br>";
									//throw new Exception('Esta consulta');
								}
*/								while ($valor = $resultado->fetch_array()) {
									/*if($encode){
										foreach($valor as $val){
											//$val=base64_encode($val);
										}
									}*/
									$this->registros[]=$valor;
								}
							}else{
								$this->Error=3;
							}
						}catch (Exception $e) {
							throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."].");
						}
						break;
					case MOTOR_SQLite:
						try{
							$this->sql=$this->XMLSQL_To_SQLite($XMLSQL,SQLite_CONSULTA);
							if($resultado=$this->conn->query($this->sql)){
								if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)."]");
								while ($valor = $resultado->fetch_array()) {
									$this->registros[]=$valor;
								}
							}else{
								$this->Error=3;
							}
						}catch (Exception $e) {
							throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."].");
						}
						break;
					default:
						$this->$Error=1;
						return array();
				}
			}
			return $this->registros;
		}
		
		function insertar($XMLSQL, $debug=false){
			if($this->Error!=4){
				switch($this->tipo){
					case MOTOR_MySQL:
						$this->sql=$this->XMLSQL_To_MySQL($XMLSQL,MySQL_INSERCION);
						if($resultado=$this->conn->query($this->sql)){
							$this->ultimoId=$this->conn->insert_id;
							return true;
						}else{
							//var_dump(mysqli_error($this->conn));
							if (intval(mysqli_errno($this->conn))==1062){
								$error=mysqli_error($this->conn);
								$tmp=explode("Duplicate entry '",$error);
								$tmp=explode("' for key '",$tmp[1]);
								$tmp[1]=substr($tmp[1], 0, -1);
								$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>$tmp[1],"valor"=>$tmp[0]);
								throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
							}else{
								throw new XMLSQLException("No se pudo insertar el nuevo registro, [".mysqli_error($this->conn)."].");
							}
						}
						break;
					case MOTOR_SQLite:
						$this->sql=$this->XMLSQL_To_SQLite($XMLSQL,SQLite_INSERCION);
						if($debug) registrarlog("<b>Insertando </b>".$this->sql."<br>");
						if($resultado=$this->conn->query('call XDB_insertar("'.$this->sql.'");')){
							if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)."]");
							$valor = $resultado->fetch_array();
							$this->ultimoId=$valor["id"];
							$resultado=$this->conn->query('select 1 as a;');
							mysqli_next_result($this->conn);
							return true;
						}else{
							throw new XMLSQLException("No se pudo insertar el nuevo registro, [".mysqli_error($this->conn)."].");
						}
						break;
					default:
						$this->Error=1;
						$this->ultimoId=0;
						return false;
				}
			}
			return false;
		}
		
		function ejecutar($XMLSQL){
			//registrarlog("<hr><b>".date("Y-m-d G:i:s")."</b><br>XMLSQL::ejecutar");
			//registrarlog("<br><b>XMLSQL:</b>".htmlspecialchars($XMLSQL));
			$this->registros=array();
			if($this->Error!=4){
				switch($this->tipo){
					case MOTOR_MySQL:
						$this->sql=$this->XMLSQL_To_MySQL($XMLSQL,MySQL_EJECUTAR);
						//registrarlog("<br><b>SQL:<b>".$this->sql);
						if($resultado=$this->conn->query($this->sql)){
							if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)."]");
							if (!is_bool($resultado)){
								while ($valor = $resultado->fetch_array()) {
									$this->registros[]=$valor;
								}
								$resultado=$this->conn->query('select 1 as a;');
								mysqli_next_result($this->conn);
							}
						}else{
							throw new XMLSQLException("No se pudo ejecutar el procedimiento, [".mysqli_error($this->conn)."].");
						}
						break;
					default:
						$this->Error=1;
				}
			}
			//registrarlog("<b>");
			return $this->registros;
		}
		
		function mysql($sql){
			$sql=str_replace("PREFIJO",$this->getPrefijoTabla(),$sql);
			if($resultado=$this->conn->query($sql)){
				if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)."]");
				return true;
			}else{
				mensaje::add("No se pudo ejecutar el script, [".mysqli_error($this->conn)."].");
				return false;
			}
		}
		
		function eliminar($XMLSQL){
			if($this->Error!=4){
				switch($this->tipo){
					case MOTOR_MySQL:
						$this->sql=$this->XMLSQL_To_MySQL($XMLSQL,MySQL_ELIMINACION);
						//new mensajes("Eliminando: ".$this->sql);
						if($this->conn->query($this->sql)){
							if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)."]");
							return true;
						}else{
							//
							throw new XMLSQLException("No se pudo eliminar el registro, [".mysqli_error($this->conn)."].");
						}
						break;
					default:
						$this->Error=1;
						return false;
				}
			}
			return false;
		}
		
		function actualizar($XMLSQL){
			if($this->Error!=4){
				switch($this->tipo){
					case MOTOR_MySQL:
						$this->sql=$this->XMLSQL_To_MySQL($XMLSQL,MySQL_ACTUALIZACION);
						//registrarlog("<b>Actualizando:</b> ".$XMLSQL->asXML()." -> ".$this->sql."<br>");
						///echo "<b>Actualizando:</b> ".$XMLSQL." -> ".$this->sql."<br>";
						if($this->conn->query($this->sql)){
							///echo "Lugar1: [".mysqli_error($this->conn)."]";
							if (mysqli_errno($this->conn)==1062){
								throw new XMLSQLExcepcionRegistroDuplicado(mysqli_error($this->conn));
							}elseif (mysqli_errno($this->conn)!=0){
								throw new XMLSQLException("No se pudo actualizar el nuevo registro, [".mysqli_error($this->conn)."].");
							}
							return true;
						}
						///echo "Lugar2: ".mysqli_error($this->conn);
						break;
					default:
						$this->Error=1;
						return false;
				}
			}
			return false;
		}
		
		function numeroRegistros($XMLSQL=null){
			//echo "Num registros.<br>";
			$no=-1;
			if($this->Error!=4){
				switch($this->tipo){
					case MOTOR_MySQL:
						if($XMLSQL!=null){
							//registrarlog("<hr>XMLSQL numeroRegistros:".htmlspecialchars($XMLSQL->asXML())."<hr>");
							$sql=$this->XMLSQL_To_MySQL($XMLSQL,MySQL_CONSULTA);
							//registrarlog("<hr>SQL".$sql."<hr>");
							$resultado=mysqli_query($this->conn,$sql);
							if(mysqli_errno($this->conn)) asercion("Ocurrio un error: [".mysqli_error($this->conn)." | <br>$sql]");
							$no=mysqli_num_rows($resultado);
						}else{
							$no=count($this->registros);
						}
						break;
					default:
						$this->Error=1;
						return 0;
				}
			}
			//registrarlog("<hr>No".$no."<hr>");
			return $no;
		}
		
		function XMLSQL_To_MySQL($XMLSQL,$tipo){
			$MySQL="";
			$tablas=array();
			$campos=array();
			$valores=array();
			$camposC="";
			$tablasC="";
			$valoresC="";
			$nombreProc="";
			if(is_object($XMLSQL)){
				$xml = $XMLSQL;
			}else{
				$xml = simplexml_load_string($XMLSQL);
			}
			switch($tipo){
				case MySQL_EJECUTAR:
					$xmltmp=$xml->xpath('Ejecutar');
					foreach($xmltmp as $nodo){
						$nombreProc=$nodo[0]["nombre"];
						$parametrosProc=$nodo[0]["parametros"];
					}
					$MySQL="CALL $nombreProc($parametrosProc);";
					break;
				default:
					//CAMPOS Y TABLAS
					$xmltmp=$xml->xpath('Campo');
					if(is_array($xmltmp)){
						foreach($xmltmp as $nodo){
							$a=$this->getPrefijoTabla().$nodo[0]["tablaOrigen"];
							//asercion(revisarArreglo($a));
							if($tipo==MySQL_INSERCION||$tipo==MySQL_ACTUALIZACION){
								if (is_null($nodo[0]["valor"]))
									$valores[]="NULL";
								else
									$valores[]="'".str_replace("'", "\'", $nodo[0]["valor"])."'";
							}
							if(isset($nodo[0]["tablaOrigen"])){
								$tablas["$a"]=1;
							}
							$campos[]=(isset($nodo[0]["tablaOrigen"])?($tipo==MySQL_CONSULTA?$this->getPrefijoTabla().$nodo[0]["tablaOrigen"].".":""):"").$nodo[0]["nombre"].(isset($nodo[0]["titulo"])?($tipo==MySQL_CONSULTA?" AS ".$nodo[0]["titulo"]:""):"");
						}
					}
					if($tipo==MySQL_INSERCION || $tipo==MySQL_CONSULTA){
						if($tipo==MySQL_INSERCION){
							$valoresC.=implode(",",$valores);
						}
					}

					if($tipo==MySQL_ACTUALIZACION){
						$campval=array();
						for($i=0;$i<count($campos);$i++){
							$campval[]=$campos[$i]."=".$valores[$i];
						}
						$valoresA=implode(",",$campval);
					}
			
					if($tipo==MySQL_CONSULTA){
						//RELACIONES ENTRE TABLAS
						$xmltmp2=$xml->xpath('Relacion');
						if(is_array($xmltmp2)){
							$tablasC.=" FROM ";
							if(count($xmltmp2)>0){
								$condicionesJoins= array();
									$arrTablas=array();
									$arrCondicion=array();
								$almenosUnaRelacion=false;
								foreach($xmltmp2 as $xmltmp){
									if(count($xmltmp->children())>0){
										$almenosUnaRelacion=true;
										$xmltmpTablas=$xmltmp[0]->xpath('Tabla');
										$tablaAnterior="";
										$campoAnterior="";
										foreach($xmltmpTablas as $nodo){
											$a=$this->getPrefijoTabla().$nodo[0]["nombre"];
											$arrTablas["$a"]="";
											if (strcmp($nodo[0]["tablaDestino"], $nodo[0]["nombre"])==0){
												$elefante=$this->getPrefijoTabla().$nodo[0]["tablaDestino"]." as virtual_".$nodo[0]["tablaDestino"];
												$arrTablas["$elefante"]="1";
												$campos[]="virtual_".$nodo[0]["tablaDestino"].".".$nodo[0]["campoTextoClaveForanea"]." as ".$nodo[0]["campoAliasTextoClaveForanea"];
												$arrCondicion[]="virtual_".$nodo[0]["tablaDestino"].".".$nodo[0]["campoDestino"].
															"=".
																$this->getPrefijoTabla().$nodo[0]["nombre"].".".$nodo[0]["campo"];
											}else if(strlen($nodo[0]["tablaDestino"])>0 && strlen($nodo[0]["campoDestino"])>0){
												//@todo cambiar el nombre de la variable elefante por una mas adecuada
												$elefante=$this->getPrefijoTabla().$nodo[0]["tablaDestino"];
												$arrTablas["$elefante"]="1";
												$arrCondicion[]=$this->getPrefijoTabla().$nodo[0]["tablaDestino"].".".$nodo[0]["campoDestino"].
															"=".
																$this->getPrefijoTabla().$nodo[0]["nombre"].".".$nodo[0]["campo"];
											}else{
												if(strlen($tablaAnterior)>0){						
													$arrCondicion[]=$this->getPrefijoTabla().$tablaAnterior.".".$campoAnterior.
																"=".
																	$this->getPrefijoTabla().$nodo[0]["nombre"].".".$nodo[0]["campo"];
												}
											}
											$tablaAnterior=$nodo[0]["nombre"];
											$campoAnterior=$nodo[0]["campo"];
										}
										$condicionesJoins[]=implode(" AND ",$arrCondicion);
									}
								}
								if($almenosUnaRelacion){
									$tablasC.=implode(" JOIN ",array_keys($arrTablas))." ON ";
									$tablasC.=implode(" AND ", $condicionesJoins);
								}else{
									$tablasC.=implode(",",array_keys($tablas));
								}
							}else{
								$tablasC.=implode(",",array_keys($tablas));
							}
						}
					}

					if($tipo==MySQL_INSERCION || $tipo==MySQL_CONSULTA){
						$camposC.=implode(",",$campos);
					}		

					//LIMITAR
					/*Colocados por los logs de php*/$limitante="";
					$xmltmp=$xml->xpath('Limitar');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							if(strlen($xmltmp[0][0]["regInicial"])>0){
								$limitante.=" LIMIT ".$xmltmp[0][0]["regInicial"].",".$xmltmp[0][0]["noRegistros"];
							}else{
								$limitante.=" LIMIT ".$xmltmp[0][0]["noRegistros"];
							}
						}
					}
			
					//ORDENAR
					$tmpOrden=array();
					$Orden="";
					$xmltmp=$xml->xpath('Ordenar/OrdenarCampo');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$tmpOrden=$this->extraerNodoArreglo($xmltmp,"analizarOrden");
							$Orden=" ORDER BY ".implode(",",$tmpOrden);
						}
					}
					
					//AGRUPAR
					$tmpAgrupar=array();
					$Agrupar="";
					$xmltmp=$xml->xpath('Agrupar/AgruparCampo');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$tmpAgrupar=$this->extraerNodoArreglo($xmltmp,"analizarOrden");
							$Agrupar=" GROUP BY ".implode(",",$tmpAgrupar);
						}
					}
			
					$condicionesC="";
					$xmltmp=$xml->xpath('Condiciones');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$cond=$this->analizarCondiciones($xmltmp);
							if(strcmp($this->analizarCondiciones($xmltmp),"")!=0){
								$condicionesC=" WHERE ".$cond." ";
							}
						}
					}
			
					$tmp=array_keys($tablas);
					switch($tipo){
						case MySQL_CONSULTA:
							$MySQL="SELECT ".$camposC.$tablasC.$condicionesC.$Agrupar.$Orden.$limitante.";";
							break;
						case MySQL_ACTUALIZACION:
							$MySQL="UPDATE ".$tmp[0]." SET ".$valoresA.$condicionesC.";";
							break;
						case MySQL_ELIMINACION:
							$MySQL="DELETE FROM ".$tmp[0].$condicionesC.$limitante.";";
							break;
						case MySQL_INSERCION:
							$MySQL="INSERT INTO ".$tmp[0]."(".$camposC.") VALUES (".$valoresC.");";
							break;
						default:
					}
			}
			return $MySQL;
		}
		
		function analizarOrden($nodo){
			return $nodo["campo"]." ".$nodo["modo"];
		}

		function analizarCondiciones($xmltmp,$padre=null, $prof=0){
			$totalHijos=count($xmltmp)-1;/*Colocados por los logs de php*/$temp="";
			if($totalHijos>0)
				$temp="(";
			$j=0;
			foreach($xmltmp as $i => $hijo){
				if($hijo->getName()=="Y"){
				}
				if($hijo->getName()=="O"){
				}
				if($hijo->getName()=="Igual"){
					if (strlen($hijo["tabla"])>0){
						$comilla=isset($hijo["noComilla"])?"":"'";
						$temp.= $this->getPrefijoTabla().$hijo["tabla"].".".$hijo["campo"]." = $comilla".$hijo["valor"]."$comilla";
					}
					if (strlen($hijo["tabla1"])>0){
						$temp.= $this->getPrefijoTabla().$hijo["tabla1"].".".$hijo["campo1"]." = ".$this->getPrefijoTabla().$hijo["tabla2"].".".$hijo["campo2"];
					}
				}
				if($hijo->getName()=="EsNulo"){
					if (strlen($hijo["tabla"])>0){
						$temp.= $this->getPrefijoTabla().$hijo["tabla"].".".$hijo["campo"]." IS NULL ";
					}
				}
				if($hijo->getName()=="Diferente"){
					if (strlen($hijo["tabla"])>0){
						$comilla=isset($hijo["noComilla"])?"":"'";
						$temp.= $this->getPrefijoTabla().$hijo["tabla"].".".$hijo["campo"]." <> $comilla".$hijo["valor"]."$comilla";
					}
					if (strlen($hijo["tabla1"])>0){
						$temp.= $this->getPrefijoTabla().$hijo["tabla1"].".".$hijo["campo1"]." <> ".$this->getPrefijoTabla().$hijo["tabla2"].".".$hijo["campo2"];
					}
				}
				if($hijo->getName()=="Otro"){
					switch($hijo["signo"]){
						case "menor":
							$signo="<";
							break;
						case "mayor":
							$signo=">";
							break;
						case "menorIgual":
							$signo="<=";
							break;
						case "mayorIgual":
							$signo=">=";
							break;
					}
					if (strlen($hijo["tabla"])>0){
						$comilla=isset($hijo["noComilla"])?"":"'";
						$temp.= $this->getPrefijoTabla().$hijo["tabla"].".".$hijo["campo"]." ".$signo." $comilla".$hijo["valor"]."$comilla";
					}
					if (strlen($hijo["tabla1"])>0){
						$temp.= $this->getPrefijoTabla().$hijo["tabla1"].".".$hijo["campo1"]." ".$signo." ".$this->getPrefijoTabla().$hijo["tabla2"].".".$hijo["campo2"];
					}
				}
				if(($hijo->getName()=="Como")>0){
					if (strlen($hijo["tabla"])>0){
						$temp.=$this->getPrefijoTabla().$hijo["tabla"].".".$hijo["campo"]." LIKE '".$hijo["valor"]."'";
					}else{
						if (strlen($hijo["campo"])>0){
							$temp.=$hijo["campo"]." LIKE '".$hijo["valor"]."'";
						}
					}
				}
				if(($hijo->getName()=="ExpresionRegular")>0){
					if (strlen($hijo["tabla"])>0){
						$temp.=$this->getPrefijoTabla().$hijo["tabla"].".".$hijo["campo"]." REGEXP '".$hijo["valor"]."'";
					}
				}
				$temp.=$this->analizarCondiciones($hijo,$hijo->getName(), $prof+1);
				if($j!=$totalHijos){
					if($padre=="Y")
						$temp.=" AND ";
					if($padre=="O")
						$temp.=" OR ";
				}
				$j++;
			}
			if($totalHijos>0)
				$temp.=")";
			return $temp;
		}
		
		function XMLSQL_To_SQLite($XMLSQL,$tipo){
			$MySQL="";
			$tablas=array();
			$campos=array();
			$valores=array();
			$camposC="";
			$tablasC="";
			$valoresC="";
			$nombreProc="";
			if(is_object($XMLSQL)){
				$xml = $XMLSQL;
			}else{
				$xml = simplexml_load_string($XMLSQL);
			}
			switch($tipo){
				/*case MySQL_EJECUTAR:
					$xmltmp=$xml->xpath('Ejecutar');
					foreach($xmltmp as $nodo){
						$nombreProc=$nodo[0]["nombre"];
						$parametrosProc=$nodo[0]["parametros"];
					}
					$MySQL="CALL $nombreProc($parametrosProc);";
					break;*/
				default:
					//CAMPOS Y TABLAS
					$xmltmp=$xml->xpath('Campo');
					if(is_array($xmltmp)){
						foreach($xmltmp as $nodo){
							$a=$this->getPrefijoTabla().$nodo[0]["tablaOrigen"];
							//asercion(revisarArreglo($a));
							if($tipo==MySQL_INSERCION||$tipo==MySQL_ACTUALIZACION){
								if (is_null($nodo[0]["valor"]))
									$valores[]="NULL";
								else
									$valores[]="'".$nodo[0]["valor"]."'";
							}
							if(isset($nodo[0]["tablaOrigen"])){
								$tablas["$a"]=1;
							}
							$campos[]=(isset($nodo[0]["tablaOrigen"])?($tipo==MySQL_CONSULTA?$this->getPrefijoTabla().$nodo[0]["tablaOrigen"].".":""):"").$nodo[0]["nombre"].(isset($nodo[0]["titulo"])?($tipo==MySQL_CONSULTA?" AS ".$nodo[0]["titulo"]:""):"");
						}
					}
					if($tipo==SQLite_INSERCION || $tipo==SQLite_CONSULTA){
						if($tipo==SQLite_INSERCION){
							$valoresC.=implode(",",$valores);
						}
					}

					if($tipo==SQLite_ACTUALIZACION){
						$campval=array();
						for($i=0;$i<count($campos);$i++){
							$campval[]=$campos[$i]."=".$valores[$i];
						}
						$valoresA=implode(",",$campval);
					}
			
					if($tipo==SQLite_CONSULTA){
						//RELACIONES ENTRE TABLAS
						$xmltmp2=$xml->xpath('Relacion');
						if(is_array($xmltmp2)){
							$tablasC.=" FROM ";
							if(count($xmltmp2)>0){
								$condicionesJoins= array();
									$arrTablas=array();
									$arrCondicion=array();
								foreach($xmltmp2 as $xmltmp){
									$xmltmpTablas=$xmltmp[0]->xpath('Tabla');
									$tablaAnterior="";
									$campoAnterior="";
									foreach($xmltmpTablas as $nodo){
										$a=$this->getPrefijoTabla().$nodo[0]["nombre"];
										$arrTablas["$a"]="";
										if (strcmp($nodo[0]["tablaDestino"], $nodo[0]["nombre"])==0){
											$elefante=$this->getPrefijoTabla().$nodo[0]["tablaDestino"]." as virtual_".$nodo[0]["tablaDestino"];
											$arrTablas["$elefante"]="1";
											$campos[]="virtual_".$nodo[0]["tablaDestino"].".".$nodo[0]["campoTextoClaveForanea"]." as ".$nodo[0]["campoAliasTextoClaveForanea"];
											$arrCondicion[]="virtual_".$nodo[0]["tablaDestino"].".".$nodo[0]["campoDestino"].
														"=".
															$this->getPrefijoTabla().$nodo[0]["nombre"].".".$nodo[0]["campo"];
										}else if(strlen($nodo[0]["tablaDestino"])>0 && strlen($nodo[0]["campoDestino"])>0){
											//@todo cambiar el nombre de la variable elefante por una mas adecuada
											$elefante=$this->getPrefijoTabla().$nodo[0]["tablaDestino"];
											$arrTablas["$elefante"]="1";
											$arrCondicion[]=$this->getPrefijoTabla().$nodo[0]["tablaDestino"].".".$nodo[0]["campoDestino"].
														"=".
															$this->getPrefijoTabla().$nodo[0]["nombre"].".".$nodo[0]["campo"];
										}else{
											if(strlen($tablaAnterior)>0){						
												$arrCondicion[]=$this->getPrefijoTabla().$tablaAnterior.".".$campoAnterior.
															"=".
																$this->getPrefijoTabla().$nodo[0]["nombre"].".".$nodo[0]["campo"];
											}
										}
										$tablaAnterior=$nodo[0]["nombre"];
										$campoAnterior=$nodo[0]["campo"];
									}
									$condicionesJoins[]=implode(" AND ",$arrCondicion);
								}
								$tablasC.=implode(" JOIN ",array_keys($arrTablas))." ON ";
								$tablasC.=implode(" AND ", $condicionesJoins);
							}else{
								$tablasC.=implode(",",array_keys($tablas));
							}
						}
					}

					if($tipo==SQLite_INSERCION || $tipo==SQLite_CONSULTA){
						$camposC.=implode(",",$campos);
					}		

					//LIMITAR
					/*Colocados por los logs de php*/$limitante="";
					$xmltmp=$xml->xpath('Limitar');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							if(strlen($xmltmp[0][0]["regInicial"])>0){
								$limitante.=" LIMIT ".$xmltmp[0][0]["regInicial"].",".$xmltmp[0][0]["noRegistros"];
							}else{
								$limitante.=" LIMIT ".$xmltmp[0][0]["noRegistros"];
							}
						}
					}
			
					//ORDENAR
					$tmpOrden=array();
					$Orden="";
					$xmltmp=$xml->xpath('Ordenar/OrdenarCampo');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$tmpOrden=$this->extraerNodoArreglo($xmltmp,"analizarOrden");
							$Orden=" ORDER BY ".implode(",",$tmpOrden);
						}
					}
					
					//AGRUPAR
					$tmpAgrupar=array();
					$Agrupar="";
					$xmltmp=$xml->xpath('Agrupar/AgruparCampo');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$tmpAgrupar=$this->extraerNodoArreglo($xmltmp,"analizarOrden");
							$Agrupar=" GROUP BY ".implode(",",$tmpAgrupar);
						}
					}
			
					$condicionesC="";
					$xmltmp=$xml->xpath('Condiciones');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$condicionesC=" WHERE ". $this->analizarCondiciones($xmltmp)." ";
						}
					}
			
					$tmp=array_keys($tablas);
					switch($tipo){
						case SQLite_CONSULTA:
							$MySQL="SELECT ".$camposC.$tablasC.$condicionesC.$Agrupar.$Orden.$limitante.";";
							break;
						case SQLite_ACTUALIZACION:
							$MySQL="UPDATE ".$tmp[0]." SET ".$valoresA.$condicionesC.";";
							break;
						case SQLite_ELIMINACION:
							$MySQL="DELETE FROM ".$tmp[0].$condicionesC.$limitante.";";
							break;
						case SQLite_INSERCION:
							$MySQL="INSERT INTO ".$tmp[0]."(".$camposC.") VALUES (".$valoresC.");";
							break;
						default:
					}
			}
			return $MySQL;
		}
		
		function errorToString($error=null){
			$msj;
			if(is_null($error)){
				$errChk=$this->Error;
			}else{
				$errChk=$error;
			}
			switch($errChk){
				case 0:
					$msj="No hubo errores.";
					break;
				case 1:
					$msj="El motor de base de datos no esta contemplado en la clase.";
					break;
				case 2:
					$msj="Falta campo o tabla en relación.";
					break;
				case 3:
					$msj="Error: ".mysqli_error($this->conn)."<hr>";
					break;
				case 4:
					$msj="Fallo la conexi&oacute;n con la base de datos.";
					break;
			}
			return $msj;
		}
		
		
	}

?>
