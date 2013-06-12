<?php

	//use Doctrine\Common\ClassLoader;

	//require '/usr/share/php/Doctrine/Common/ClassLoader.php';

	define("SQL_CONSULTA","SQL_CONSULTA");
	define("SQL_ACTUALIZACION","SQL_ACTUALIZACION");
	define("SQL_ELIMINACION","SQL_ELIMINACION");
	define("SQL_EJECUTAR","SQL_EJECUTAR");
	define("SQL_INSERCION","SQL_INSERCION");
	
	/*$classLoader = new ClassLoader('Doctrine', '/usr/share/php');
	$classLoader->register();
	$config = new \Doctrine\DBAL\Configuration();*/
	
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
	
		var $conn;
		var $pool=array();
		var $tipo;
		var $registros;
		var $Error;
		public $sql;
		var $ultimoId;
		
		function table_exists ($table) { 
			$prefijo=$this->conn["pconn"]["prefijo"];
			$schema=$this->conn["conn"]->getSchemaManager();
			$tables = $schema->listTables();
			foreach($tables as $tabla){
				if (strcmp($tabla->getName(), $prefijo.$table)==0){
					return TRUE;
				}
			}
			return FALSE;
		}

		
		function XMLSQL($listp=null){
			if(is_array($listp)){
				$primera=true;
				foreach($listp as $id=>$p){
					$this->addConn($id,$p);
				}
			}
		}
		
		function addConn($id,$p){
			if(is_array($p)){
				$paramConexion = array();
				if(isset($p["driver"])){
					# dbname (string): Name of the database/schema to connect to.
					$paramConexion["driver"]=$p["driver"];
					# prefijo (string): Prefijo usado en las tablas.
					if(isset($p["prefijo"])){
						$paramConexion["prefijo"]=$p["prefijo"];
					}else{
						$paramConexion["prefijo"]="";
					}
					switch($p["driver"]){
						case "pdo_mysql":
							# user (string): Username to use when connecting to the database.
							if(isset($p["user"])){
								$paramConexion["user"]=$p["user"];
							}
							# password (string): Password to use when connecting to the database.
							if(isset($p["password"])){
								$paramConexion["password"]=$p["password"];
							}
							# host (string): Hostname of the database to connect to.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# port (integer): Port of the database to connect to.
							if(isset($p["port"])){
								$paramConexion["port"]=$p["port"];
							}
							# dbname (string): Name of the database/schema to connect to.
							if(isset($p["dbname"])){
								$paramConexion["dbname"]=$p["dbname"];
							}
							# unix_socket (string)
							if(isset($p["unix_socket"])){
								$paramConexion["unix_socket"]=$p["unix_socket"];
							}
							break;
						case "pdo_sqlite":
							# user (string): Username to use when connecting to the database.
							if(isset($p["user"])){
								$paramConexion["user"]=$p["user"];
							}
							# password (string): Password to use when connecting to the database.
							if(isset($p["password"])){
								$paramConexion["password"]=$p["password"];
							}
							# path (string): The filesystem path to the database file. Mutually exclusive with memory. path takes precedence.
							if(isset($p["path"])){
								$paramConexion["path"]=$p["path"];
							}
							# memory (boolean): True if the SQLite database should be in-memory (non-persistent). Mutually exclusive with path. path takes precedence.
							if(isset($p["memory"])){
								$paramConexion["memory"]=$p["memory"];
							}
							break;
						case "pdo_pgsql":
							# user (string): Username to use when connecting to the database.
							if(isset($p["user"])){
								$paramConexion["user"]=$p["user"];
							}
							# password (string): Password to use when connecting to the database.
							if(isset($p["password"])){
								$paramConexion["password"]=$p["password"];
							}
							# host (string): Hostname of the database to connect to.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# port (integer): Port of the database to connect to.
							if(isset($p["port"])){
								$paramConexion["port"]=$p["port"];
							}
							# dbname (string): Name of the database/schema to connect to.
							if(isset($p["dbname"])){
								$paramConexion["dbname"]=$p["dbname"];
							}
							break;
						case "pdo_oci":case "oci8":
							# user (string): Username to use when connecting to the database.
							if(isset($p["user"])){
								$paramConexion["user"]=$p["user"];
							}
							# password (string): Password to use when connecting to the database.
							if(isset($p["password"])){
								$paramConexion["password"]=$p["password"];
							}
							# user (string): Username to use when connecting to the database.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# password (string): Password to use when connecting to the database.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# host (string): Hostname of the database to connect to.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# port (integer): Port of the database to connect to.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# dbname (string): Name of the database/schema to connect to.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							# charset (string): The charset used when connecting to the database.
							if(isset($p["host"])){
								$paramConexion["host"]=$p["host"];
							}
							break;
					}
					# Se agrega al POOL los parametros de conexión
					$this->pool[$id]=array("pconn"=>$paramConexion,"conn"=>"");
					
					# Si es la primera conexión, se señala con un apuntador como default
					if(count($this->pool)==1){
						$this->conn=&$this->pool[$id];
					}
				}
			}
		}
		
		function openConn(&$pool){
			$conn=null;
			if(is_array($pool)){
				if(isset($pool["pconn"])&&isset($pool["conn"])){
					if(is_null($pool["conn"])||$pool["conn"]==""){
						$conn = \Doctrine\DBAL\DriverManager::getConnection($pool["pconn"]);
						$pool["conn"]=$conn;
					}else{
						$conn=$pool["conn"];
					}
				}
			}
			return $conn;
		}
		
		function consultar($XMLSQL,$id=null){
			try{
				$conn=null;
				$this->registros=array();
				$prefijo="";
				$driver="";
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
						$driver=$this->pool["$id"]["pconn"]["driver"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$prefijo=$this->conn["pconn"]["prefijo"];
					$driver=$this->conn["pconn"]["driver"];
				}
				if(!is_null($conn)){
					$this->sql=$this->XMLSQL_To_DQL($XMLSQL,SQL_CONSULTA,$prefijo,$driver);
					$statement = $conn->prepare($this->sql);
					$statement->execute();
					$this->registros = $statement->fetchAll();
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."][".$this->conn["pconn"]["driver"]."][".$this->sql."].");
			}
			return $this->registros;
		}
		
		function insertar($XMLSQL, $id=null){
			try{
				$conn=null;
				$prefijo="";
				$driver="";
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
						$driver=$this->pool["$id"]["pconn"]["driver"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$prefijo=$this->conn["pconn"]["prefijo"];
					$driver=$this->conn["pconn"]["driver"];
				}
				if(!is_null($conn)){
					$this->sql=$this->XMLSQL_To_DQL($XMLSQL,SQL_INSERCION,$prefijo,$driver);
					$count = $conn->executeUpdate($this->sql);

					switch($driver){
						case "pdo_mysql":case "pdo_sqlite": case "pdo_oci":case "oci8":
							$this->ultimoId=$conn->lastInsertId();
							break;
						case "pdo_pgsql":
							try{
								$ids=$conn->fetchAssoc("SELECT lastval();");
								if(isset($ids["lastval"])){
									$this->ultimoId=$ids["lastval"];
								}else{
									$this->ultimoId=0;
								}
							}catch(Exception $e){
								$this->ultimoId=0;
							}
							break;
					}
					return true;
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				# Validación de registros duplicados SQLite
				if($e->errorInfo[0]==23000 && $e->errorInfo[1]==19 ){
					$val=explode(" ",$e->errorInfo[2],3);
					$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>$val[1],"valor"=>"");
					throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
				}
				
				# Validación de registros duplicados MySQL
				if($e->errorInfo[0]==23000 && $e->errorInfo[1]==1062 ){
					$tmp=explode("Duplicate entry '",$e->errorInfo[2]);
					$tmp=explode("' for key '",$tmp[1]);
					$tmp[1]=substr($tmp[1], 0, -1);
					$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>$tmp[1],"valor"=>$tmp[0]);
					throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
				}
				
				# Validación de registros duplicados Postgres
				if($e->errorInfo[0]==23505 && $e->errorInfo[1]==7 ){
					$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>"","valor"=>"");
					throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
				}

				throw new XMLSQLException("No se pudo insertar el nuevo registro, ocurrio un error [".$e->getMessage()."].");
			}
			return false;
		}
		
		function ejecutar($XMLSQL,$id=null){
			try{
				$conn=null;
				$driver="";
				$prefijo="";
				$this->registros=array();
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$driver=$this->pool["$id"]["pconn"]["driver"];
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$driver=$this->conn["pconn"]["driver"];
					$prefijo=$this->conn["pconn"]["prefijo"];
				}
				if(!is_null($conn)){
					$this->sql=$this->XMLSQL_To_DQL($XMLSQL,SQL_EJECUTAR,$prefijo,$driver);
					$statement = $conn->prepare($this->sql);
					$statement->execute();
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				throw new XMLSQLException("No se pudo ejecutar el procedimiento, [".$e->getMessage()."].");
			}
			return $this->registros;
		}
		
		function dql($sql, $id=null){
			try{
				$conn=null;
				$prefijo="";
				$driver="";
				$this->registros=array();
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$prefijo=$this->conn["pconn"]["prefijo"];
				}
				if(!is_null($conn)){
					$this->sql=str_replace("PREFIJO",$prefijo,$sql);
					$statement = $conn->prepare($this->sql);
					$statement->execute();
					$this->registros = $statement->fetchAll();
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				throw new XMLSQLException("No se pudo ejecutar el procedimiento, [".$e->getMessage()."].");
			}
			return $this->registros;
		}
		
		function eliminar($XMLSQL, $id=null){
			try{
				$conn=null;
				$prefijo="";
				$driver="";
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
						$driver=$this->pool["$id"]["pconn"]["driver"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$prefijo=$this->conn["pconn"]["prefijo"];
					$driver=$this->conn["pconn"]["driver"];
				}
				if(!is_null($conn)){
					$this->sql=$this->XMLSQL_To_DQL($XMLSQL,SQL_ELIMINACION,$prefijo,$driver);
					$count = $conn->executeUpdate($this->sql);
					return true;
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				throw new XMLSQLException("No se pudo eliminar el registro, ocurrio un error [".$e->getMessage()."].");
			}
			return false;
		}
		
		function actualizar($XMLSQL, $id=null){
			try{
				$conn=null;
				$prefijo="";
				$driver="";
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
						$driver=$this->pool["$id"]["pconn"]["driver"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$prefijo=$this->conn["pconn"]["prefijo"];
					$driver=$this->conn["pconn"]["driver"];
				}
				if(!is_null($conn)){
					$this->sql=$this->XMLSQL_To_DQL($XMLSQL,SQL_ACTUALIZACION,$prefijo,$driver);
					$count = $conn->executeUpdate($this->sql);
					return true;
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				# Validación de registros duplicados SQLite
				if($e->errorInfo[0]==23000 && $e->errorInfo[1]==19 ){
					$val=explode(" ",$e->errorInfo[2],3);
					$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>$val[1],"valor"=>"");
					throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
				}
				
				# Validación de registros duplicados MySQL
				if($e->errorInfo[0]==23000 && $e->errorInfo[1]==1062 ){
					$tmp=explode("Duplicate entry '",$e->errorInfo[2]);
					$tmp=explode("' for key '",$tmp[1]);
					$tmp[1]=substr($tmp[1], 0, -1);
					$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>$tmp[1],"valor"=>$tmp[0]);
					throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
				}
				
				# Validación de registros duplicados Postgres
				if($e->errorInfo[0]==23505 && $e->errorInfo[1]==7 ){
					$mensaje=array("mensaje"=>"Registro duplicado.","campo"=>"","valor"=>"");
					throw new XMLSQLExcepcionRegistroDuplicado(json_encode($mensaje));
				}

				throw new XMLSQLException("No se pudo actulizar el registro, ocurrio un error [".$e->getMessage()."].");
			}
			return false;
		}
		
		function numeroRegistros($XMLSQL=null, $id=null){
			try{
				$conn=null;
				$no=-1;
				$prefijo="";
				$driver="";
				if(!is_null($id)){
					if(isset($this->pool["$id"])){
						$conn=$this->openConn($this->pool["$id"]);
						$prefijo=$this->pool["$id"]["pconn"]["prefijo"];
						$driver=$this->pool["$id"]["pconn"]["driver"];
					}
				}else{
					$conn=$this->openConn($this->conn);
					$prefijo=$this->conn["pconn"]["prefijo"];
					$driver=$this->conn["pconn"]["driver"];
				}
				if(!is_null($conn)){
					$this->sql=$this->XMLSQL_To_DQL($XMLSQL,SQL_CONSULTA,$prefijo,$driver);
					$statement = $conn->prepare($this->sql);
					$statement->execute();
					$no = $statement->rowCount();
				}else{
					throw new XMLSQLException("No se pudo establecer la conexión [$id].");
				}
			}catch(PDOException $e){
				throw new XMLSQLException("Ocurrio un error, [".$e->getMessage()."].");
			}
			return $no;
		}
		
		function ajusteNombre($texto,$driver,$t='"'){
			switch($driver){
				case "pdo_mysql":case "pdo_sqlite":
					return $texto;
					break;
				case "pdo_oci":case "oci8":
					return strtolower($texto);
					break;
				case "pdo_pgsql":
					if(strcmp($texto,"*")!=0){
						return $t.$texto.$t;
					}
					return $texto;
					break;
			}
		}
		
		function XMLSQL_To_DQL($XMLSQL,$tipo,$prefijo="",$driver=""){
			$MySQL="";
			$tablas=array();
			$campos=array();
			$valores=array();
			$camposC="";
			$tablasC="";
			$valoresC="";
			$nombreProc="";
			//$prefijo=$this->ajusteNombre($prefijo,$driver);
			if(is_object($XMLSQL)){
				$xml = $XMLSQL;
			}else{
				$xml = simplexml_load_string($XMLSQL);
			}
			switch($tipo){
				case SQL_EJECUTAR:
					$xmltmp=$xml->xpath('Ejecutar');
					foreach($xmltmp as $nodo){
						$nombreProc=$nodo[0]["nombre"];
						$parametrosProc=$nodo[0]["parametros"];
					}
					switch($driver){
						case "pdo_mysql":
							$MySQL="CALL $nombreProc($parametrosProc);";
							break;
						case "pdo_sqlite":
							break;
						case "pdo_pgsql":
							$MySQL="SELECT $nombreProc($parametrosProc);";
							break;
						case "pdo_oci":case "oci8":
							break;
					}
					break;
				default:
					//CAMPOS Y TABLAS
					$xmltmp=$xml->xpath('Campo');
					if(is_array($xmltmp)){
						foreach($xmltmp as $nodo){
							$a=$this->ajusteNombre($prefijo.$nodo[0]["tablaOrigen"],$driver,isset($nodo[0]["comilla"])?$nodo[0]["comilla"]:'"');
							if($tipo==SQL_INSERCION||$tipo==SQL_ACTUALIZACION){
								if (is_null($nodo[0]["valor"]))
									$valores[]="NULL";
								else
									$valores[]="'".str_replace("'", "\'", $nodo[0]["valor"])."'";
							}
							if(isset($nodo[0]["tablaOrigen"])){
								$tablas["$a"]=1;
							}
							$campos[]=(isset($nodo[0]["tablaOrigen"])?($tipo==SQL_CONSULTA?$this->ajusteNombre($prefijo.$nodo[0]["tablaOrigen"],$driver).".":""):"").$this->ajusteNombre($nodo[0]["nombre"],$driver,isset($nodo[0]["comilla"])?$nodo[0]["comilla"]:'"').(isset($nodo[0]["titulo"])?($tipo==SQL_CONSULTA?" AS ".$this->ajusteNombre($nodo[0]["titulo"],$driver):""):"");
						}
					}

					if($tipo==SQL_INSERCION){
						$valoresC.=implode(",",$valores);
					}

					if($tipo==SQL_ACTUALIZACION){
						$campval=array();
						for($i=0;$i<count($campos);$i++){
							$campval[]=$campos[$i]."=".$valores[$i];
						}
						$valoresA=implode(",",$campval);
					}
			
					if($tipo==SQL_CONSULTA){
						//RELACIONES ENTRE TABLAS
						$xmltmp2=$xml->xpath('Relacion');
						if(is_array($xmltmp2)){
							$tablasC.=" FROM ";
							if(count($xmltmp2)>0){
								$condicionesJoins= array();
								$arrTablas=array();
								$arrTablasJoin=array();
								$arrCondicion="";
								$almenosUnaRelacion=false;
								foreach($xmltmp2 as $xmltmp){
									if(count($xmltmp->children())>0){
										$almenosUnaRelacion=true;
										$xmltmpTablas=$xmltmp[0]->xpath('Tabla');
										$tablaAnterior="";
										$campoAnterior="";
										foreach($xmltmpTablas as $nodo){
											if (strcmp($nodo[0]["tablaDestino"], $nodo[0]["nombre"])==0){
												$b=$this->ajusteNombre($prefijo.$nodo[0]["tablaDestino"],$driver)." as ".$this->ajusteNombre("virtual_".$nodo[0]["tablaDestino"],$driver);
												$campos[]=$this->ajusteNombre("virtual_".$nodo[0]["tablaDestino"],$driver).".".$this->ajusteNombre($nodo[0]["campoTextoClaveForanea"],$driver)." as ".$this->ajusteNombre($nodo[0]["campoAliasTextoClaveForanea"],$driver);
												$arrCondicion=$this->ajusteNombre("virtual_".$nodo[0]["tablaDestino"],$driver).".".$this->ajusteNombre($nodo[0]["campoDestino"],$driver).
															"=".
																$this->ajusteNombre($prefijo.$nodo[0]["nombre"],$driver).".".$this->ajusteNombre($nodo[0]["campo"],$driver);
												$a=$this->ajusteNombre($prefijo.$nodo[0]["nombre"],$driver);
												$arrTablas["$a"]="";
												$arrTablas["$b"]=$arrCondicion;
												$arrTablasJoin["$b"]="".(string)$nodo[0]["tipo"];
											}else if(strlen($nodo[0]["tablaDestino"])>0 && strlen($nodo[0]["campoDestino"])>0){
												$b=$this->ajusteNombre($prefijo.$nodo[0]["nombre"],$driver);
												$arrCondicion=$this->ajusteNombre($prefijo.$nodo[0]["tablaDestino"],$driver).".".$this->ajusteNombre($nodo[0]["campoDestino"],$driver).
															"=".
																$this->ajusteNombre($prefijo.$nodo[0]["nombre"],$driver).".".$this->ajusteNombre($nodo[0]["campo"],$driver);
												$arrTablas["$b"]=$arrCondicion;
												$arrTablasJoin["$b"]=$nodo[0]["tipo"];
											}else{
												$arrCondicion="";
												if(strlen($tablaAnterior)>0){						
													$arrCondicion=$this->ajusteNombre($prefijo.$tablaAnterior,$driver).".".$this->ajusteNombre($campoAnterior,$driver).
																"=".
																	$this->ajusteNombre($prefijo.$nodo[0]["nombre"],$driver).".".$this->ajusteNombre($nodo[0]["campo"],$driver);
												}
												$a=$this->ajusteNombre($prefijo.$nodo[0]["nombre"],$driver);
												$arrTablas["$a"]=$arrCondicion;
												$arrTablasJoin["$a"]=$nodo[0]["tipo"];
											}
											$tablaAnterior=$nodo[0]["nombre"];
											$campoAnterior=$nodo[0]["campo"];
										}
									}
								}
								if($almenosUnaRelacion){
									$primero=true;
									foreach($arrTablas as $t=>$c){
										if($primero){
											$tablasC.=$t;
											$primero=false;
										}else{
											$tablasC.=" ".$arrTablasJoin[$t]." JOIN ".$t." ON ".$c;
										}
									}
								}else{
									$tablasC.=implode(",",array_keys($tablas));
								}
							}else{
								$tablasC.=implode(",",array_keys($tablas));
							}
						}
					}

					if($tipo==SQL_INSERCION || $tipo==SQL_CONSULTA){
						$camposC.=implode(",",$campos);
					}		

					//LIMITAR
					$limitante="";
					$xmltmp=$xml->xpath('Limitar');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							switch($driver){
								case "pdo_mysql":
									if(strlen($xmltmp[0][0]["regInicial"])>0){
										$limitante.=" LIMIT ".$xmltmp[0][0]["regInicial"].",".$xmltmp[0][0]["noRegistros"];
									}else{
										$limitante.=" LIMIT ".$xmltmp[0][0]["noRegistros"];
									}
									break;
								case "pdo_sqlite":case "pdo_pgsql":
									if(strlen($xmltmp[0][0]["regInicial"])>0){
										$limitante.=" LIMIT ".$xmltmp[0][0]["noRegistros"]." OFFSET ".$xmltmp[0][0]["regInicial"];
									}else{
										$limitante.=" LIMIT ".$xmltmp[0][0]["noRegistros"];
									}
									break;
								case "pdo_oci":case "oci8":
									/*TODO*/
									break;
							}
						}
					}
			
					//ORDENAR
					$tmpOrden=array();
					$Orden="";
					$xmltmp=$xml->xpath('Ordenar/OrdenarCampo');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$tmpAgrupar=$this->extraerNodo1pArray($xmltmp,"analizarOrden",array("driver"=>$driver,"prefijo"=>$prefijo));
							if(is_array($tmpAgrupar)){
								$Orden=" ORDER BY ".implode(",",$tmpAgrupar);
							}
							if(is_string($tmpAgrupar)){
								$Orden=" OREDER BY ".$tmpAgrupar;
							}
						}
					}
					
					//AGRUPAR
					$tmpAgrupar=array();
					$Agrupar="";
					$xmltmp=$xml->xpath('Agrupar/AgruparCampo');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$tmpAgrupar=$this->extraerNodo1pArray($xmltmp,"analizarOrden",array("driver"=>$driver,"prefijo"=>$prefijo));
							if(is_array($tmpAgrupar)){
								$Agrupar=" GROUP BY ".implode(",",$tmpAgrupar);
							}
							if(is_string($tmpAgrupar)){
								$Agrupar=" GROUP BY ".$tmpAgrupar;
							}
						}
					}
			
					$condicionesC="";
					$xmltmp=$xml->xpath('Condiciones');
					if(is_array($xmltmp)){
						if(count($xmltmp)>0){
							$cond=$this->analizarCondiciones($xmltmp,$prefijo,$driver);
							if(strcmp($cond,"")!=0){
								$condicionesC=" WHERE ".$cond." ";
							}
						}
					}
			
					$tmp=array_keys($tablas);
					switch($tipo){
						case SQL_CONSULTA:
							$MySQL="SELECT ".$camposC.$tablasC.$condicionesC.$Agrupar.$Orden.$limitante.";";
							break;
						case SQL_ACTUALIZACION:
							$MySQL="UPDATE ".$tmp[0]." SET ".$valoresA.$condicionesC.";";
							break;
						case SQL_ELIMINACION:
							$MySQL="DELETE FROM ".$tmp[0].$condicionesC.$limitante.";";
							break;
						case SQL_INSERCION:
							$MySQL="INSERT INTO ".$tmp[0]."(".$camposC.") VALUES (".$valoresC.");";
							break;
						default:
					}
			}

			return $MySQL;
		}
		
		function analizarOrden($nodo,$params){
			$tabla="";
			if(isset($nodo["tabla"])){
				$tabla=$this->ajusteNombre($params["prefijo"].$nodo["tabla"],$params["driver"]).".";
			}
			if(isset($nodo["modo"])){
				return $tabla.$this->ajusteNombre($nodo["campo"],$params["driver"])." ".$nodo["modo"];
			}else{
				return $tabla.$this->ajusteNombre($nodo["campo"],$params["driver"]);
			}
		}

		function analizarCondiciones($xmltmp,$prefijo,$driver,$padre=null, $prof=0){
			$totalHijos=count($xmltmp)-1;
			$temp="";
			if($totalHijos>0)
				$temp="(";
			$j=0;
			foreach($xmltmp as $i => $hijo){
				$pre=(isset($hijo["pre"])?$hijo["pre"]:"");
				$pos=(isset($hijo["pos"])?$hijo["pos"]:"");
				$nombreCompletoTabla=(isset($hijo["noTabla"])?"":$this->ajusteNombre($prefijo.$hijo["tabla"],$driver).".");
				if($hijo->getName()=="Y"){
				}
				if($hijo->getName()=="O"){
				}
				if($hijo->getName()=="Igual"){
					if (strlen($hijo["tabla"])>0){
						$comilla=isset($hijo["noComilla"])?"":"'";
						$temp.= $pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." = $comilla".$hijo["valor"]."$comilla";
					}
					if (strlen($hijo["tabla1"])>0){
						$temp.= $pre.$this->ajusteNombre($prefijo.$hijo["tabla1"],$driver).".".$this->ajusteNombre($hijo["campo1"],$driver).$pos." = ".$this->ajusteNombre($prefijo.$hijo["tabla2"],$driver).".".$this->ajusteNombre($hijo["campo2"],$driver);
					}
				}
				if($hijo->getName()=="EsNulo"){
					if (strlen($hijo["tabla"])>0){
						$temp.= $pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." IS NULL ";
					}
				}
				if($hijo->getName()=="NoEsNulo"){
					if (strlen($hijo["tabla"])>0){
						$temp.= $pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." IS NOT NULL ";
					}
				}
				if($hijo->getName()=="Diferente"){
					if (strlen($hijo["tabla"])>0){
						$comilla=isset($hijo["noComilla"])?"":"'";
						$temp.= $pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." <> $comilla".$hijo["valor"]."$comilla";
					}
					if (strlen($hijo["tabla1"])>0){
						$temp.= $pre.$this->ajusteNombre($prefijo.$hijo["tabla1"],$driver).".".$this->ajusteNombre($hijo["campo1"],$driver).$pos." <> ".$this->ajusteNombre($prefijo.$hijo["tabla2"],$driver).".".$this->ajusteNombre($hijo["campo2"],$driver);
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
						$temp.= $pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." ".$signo." $comilla".$hijo["valor"]."$comilla";
					}
					if (strlen($hijo["tabla1"])>0){
						$temp.= $pre.$this->ajusteNombre($prefijo.$hijo["tabla1"],$driver).".".$this->ajusteNombre($hijo["campo1"],$driver).$pos." ".$signo." ".$this->ajusteNombre($prefijo.$hijo["tabla2"],$driver).".".$this->ajusteNombre($hijo["campo2"],$driver);
					}
				}
				if(($hijo->getName()=="Como")>0){
					if (strlen($hijo["tabla"])>0){
						$temp.=$pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." LIKE '".$hijo["valor"]."'";
					}else{
						if (strlen($hijo["campo"])>0){
							$temp.=$pre.$this->ajusteNombre($hijo["campo"],$driver).$pos." LIKE '".$hijo["valor"]."'";
						}
					}
				}
				if(($hijo->getName()=="ExpresionRegular")>0){
					if (strlen($hijo["tabla"])>0){
						$temp.=$pre.$nombreCompletoTabla.$this->ajusteNombre($hijo["campo"],$driver).$pos." REGEXP '".$hijo["valor"]."'";
					}
				}
				$temp.=$this->analizarCondiciones($hijo,$prefijo,$driver,$hijo->getName(), $prof+1);
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
