<?php

if (isset($_POST)){
	//$nombre
}

?>
<form method="POST">
NombreDB :<input name="nombreBD" value="<?php echo $_POST['nombreBD']; ?>"/><br>
UserDB: <input name="userBD" value="<?php echo $_POST['userBD']; ?>" /><br>
PassDB: <input name="passBD" value="<?php echo $_POST['passBD']; ?>" /><br>
HostDB: <input name="hostBD" value="<?php echo $_POST['hostBD']; ?>" /><br>
Motor:
	<select name="motor" value="<?php echo $_POST['motor']; ?>" >
		<option value="sqlite">SQLite</option>
		<option value="mysql">MySql</option>
	</select>
<br>
Prefijo: <input name="prefijoTabla" value="<?php echo $_POST['prefijoTabla']; ?>" /><br>
Instalar: <input type="checkbox" name="instalar"  /><br>
<input type="submit" />
</form>

<?php

	if (!is_null($_POST["nombreBD"])){
/*		echo "Nombre :".$_POST["nombreBD"]." <br>
User: ".$_POST["userBD"]." <br>
Pass: ".$_POST["passBD"]." <br>
Host: ".$_POST["hostBD"]." <br>
Prefijo: ".$_POST["prefijoTabla"]." <br>";*/
		instalar($_POST["nombreBD"], $_POST["userBD"], $_POST["passBD"], $_POST["hostBD"], $_POST["prefijoTabla"], $_POST["motor"], $_POST["instalar"]);
	}
	

	function revisarArreglo($value,$nom='Sin Nombre'){
		//return "";
		return IDESolGFAA($value,$nom);
	}
	function rgbhex($red, $green, $blue)
	{
		return sprintf('#%02X%02X%02X', $red, $green, $blue);
	}
	

	function IDESolGFAA($value,$nom, $i=0){
		if(is_array($value)){
			$ret="<fieldset style='padding:0 10px; border:3px groove gold; background:".rgbhex(200,200,100+$i*30)."'><legend><strong>".$nom."</strong></legend>\n";
			foreach($value as $treg=>$dreg){
				$ret.=IDESolGFAA($dreg,$treg, $i+1);
			}
			$ret.='</fieldset>'."\n";
			return $ret;
		}else if(is_object ($value)){
			settype($value, "array");
			return IDESolGFAA($value,'object('.$nom.')', $i+1);
		}
		$color=array(
			"boolean" => 'rgb(255, 0, 0)',
			"integer"  => '#00aa00',
			"double"  => '#00a000',
			"string" => 'f95800',
			"array" => '#aa00aa',
			"object" => '#ffaa00',
			"resource" => '#00aaff',
			"NULL" => '#7ec2c4',
			"user function" => '#aa0000',
			"unknown type" => '#000000',);
		$type= gettype($value);
		$value = str_replace('<', '<', $value);
		$value = str_replace('>', '>', $value);
		if ($type=="boolean"){
			if ($value)
				return "<div style='padding:0 5px; color: ".$color[$type].";'>($type) $nom = TRUE<br>"/*.var_dump($value)*/."</div>";
			else
				return "<div style='padding:0 5px; color: ".$color[$type].";'>($type) $nom = FALSE<br>"/*.var_dump($value)*/."</div>";
		}else
			return  "<div style='padding:0 5px; color: ".$color[$type].";'>($type) $nom = $value<br>"/*.var_dump($value)*/."</div>";
	}
		
	
	function instalar($nombreBD, $userBD, $passBD, $hostBD, $prefijoTabla, $motor, $instalar){


		$sql=crearBaseDatos($prefijoTabla, $motor);
		echo "Instalar=[".$instalar."]";
		if ($instalar=="on"){
			if (strcmp($motor,"mysql")==0){
				$conn = new mysqli($hostBD, $userBD, $passBD, $nombreBD);
				if (mysqli_connect_errno()) {
					if (mysqli_connect_errno()==1049){
						echo "No existe la base de datos ".$nombreBD.".";
					}else{
						//Donde esta esta función??
						echo "<br>[".mysqli_connect_errno().", ".mysqli_connect_error()."]";
					}
					exit(0);
				}
				if($conn->multi_query($sql)){
					/*@ Todo
					 * Ojo Hice una instalación y en la creación de los usuarios fallo
					 * pero me retorno OK, tonces toca ver bien lo del multi_query
					 
					 * Este es el codigo que se esta ejecutando manualmente para que 
					 * termine la instalación
					 
					 
				INSERT INTO IDECC_0Usuario (idUsuario, user, pass, xmlPropiedades) VALUES 
					..............
				ADD CONSTRAINT IDECC_0UsuarioRol_ibfk_4 FOREIGN KEY (idRol) REFERENCES IDECC_0Rol (idRol) ON DELETE CASCADE ON UPDATE CASCADE;
					 */
					echo "OK.";
				}else{
					echo "KO.";
				}
			}
			if (strcmp($motor,"sqlite")==0){
				echo "Intentando crear: ".$hostBD."/".$nombreBD."<br>";
				$conn = new  SQLiteDatabase($hostBD."/".$nombreBD);
				if(!$conn){
					echo "Error en la conexion ".$nombreBD.".<br>[".$sqliteerror."]";
					exit(0);
				}
				$error=false;
				foreach (split(";",$sql) as $i => $a){
					if (strlen(trim($a))>0){
						echo "Ejecutando [".trim($a).";]<br>";
						if(!@$conn->query(trim($a).";")){
							echo "ERROR: ".$a."<br>[".sqlite_error_string($conn)."].";
							//echo revisarArreglo($conn, "conexion");
							$error=true;
						}
					}
					//break;
				}
			
				if ($error){
					echo "Termino con errores.<br>";
				}else{
					echo "Termino sin errores.<br>";
				}
		
			}
		}
		echo "<pre>".htmlentities($sql)."</pre>";
	}
	

	function crearBaseDatos($prefijo, $motor){
	
		if (strcmp($motor, "mysql")==0){
			$final="ENGINE=InnoDB  DEFAULT CHARSET=utf8 ";
			$procedimientos="
DELIMITER //
CREATE PROCEDURE XDB_insertar(in cmd text) BEGIN SET @x = cmd; prepare x from @x; execute x; drop prepare x; select LAST_INSERT_ID() as id; END //
DELIMITER ;
";
		
		
			$autoIncremento="auto_increment";
			
			$foraneas=
"ALTER TABLE ".$prefijo."0RolCasoUso
ADD CONSTRAINT ".$prefijo."0RolCasoUso_ibfk_3 FOREIGN KEY (idRol) REFERENCES ".$prefijo."0Rol (idRol) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT ".$prefijo."0RolCasoUso_ibfk_4 FOREIGN KEY (idCasoUso) REFERENCES ".$prefijo."0CasoUso (idCasoUso) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE ".$prefijo."0UsuarioCasoUso
ADD CONSTRAINT ".$prefijo."0UsuarioCasoUso_ibfk_2 FOREIGN KEY (idUsuario) REFERENCES ".$prefijo."0Usuario (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT ".$prefijo."0UsuarioCasoUso_ibfk_3 FOREIGN KEY (idCasoUso) REFERENCES ".$prefijo."0CasoUso (idCasoUso) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE ".$prefijo."0UsuarioRol
ADD CONSTRAINT ".$prefijo."0UsuarioRol_ibfk_3 FOREIGN KEY (idUsuario) REFERENCES ".$prefijo."0Usuario (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT ".$prefijo."0UsuarioRol_ibfk_4 FOREIGN KEY (idRol) REFERENCES ".$prefijo."0Rol (idRol) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ".$prefijo."0Logs
ADD CONSTRAINT ".$prefijo."0Logs_ibfk_1 FOREIGN KEY (idUsuario) REFERENCES ".$prefijo."0Usuario (idUsuario) ON DELETE CASCADE ON UPDATE CASCADE;

";
			  $siNoExiste="IF NOT EXISTS";
		}
		
		if (strcmp($motor, "sqlite")==0){
			$final="";
			$procedimientos="";
			$autoIncremento="";
			$foraneas="";
			$siNoExiste="";
		
		}
	
		
		$sql="
CREATE TABLE ".$siNoExiste." ".$prefijo."0CasoUso (
  idCasoUso int(11) NOT NULL ".$autoIncremento.",
  idPaquete int(11) NOT NULL,
  nombreCasoUso varchar(50) NOT NULL,
  PRIMARY KEY  (idCasoUso)
) ".$final.";

INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(1, 1, 'login');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(2, 1, 'logout');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(3, 2, 'adminPaquetes');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(4, 2, 'adminCasoUso');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(5, 2, 'relacionUsuarioCasoUso');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(6, 2, 'relacionRolCasoUso');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(7, 3, 'adminUsuarios');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(8, 3, 'adminRoles');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES 
(9, 3, 'relacionUsuariosRoles');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES
(10, 4, 'nodo');
INSERT INTO ".$prefijo."0CasoUso (idCasoUso, idPaquete, nombreCasoUso) VALUES
(11, 4, 'adminNodo');

CREATE TABLE ".$siNoExiste." ".$prefijo."0Paquete (
  idPaquete int(11) NOT NULL ".$autoIncremento.",
  nombrePaquete varchar(30) NOT NULL,
  PRIMARY KEY  (idPaquete)
)  ".$final.";
INSERT INTO ".$prefijo."0Paquete (idPaquete, nombrePaquete) VALUES 
(1, 'Usuario');
INSERT INTO ".$prefijo."0Paquete (idPaquete, nombrePaquete) VALUES 
(2, 'AdminModulos');
INSERT INTO ".$prefijo."0Paquete (idPaquete, nombrePaquete) VALUES 
(3, 'AdminUsuariosYRoles');
INSERT INTO ".$prefijo."0Paquete (idPaquete, nombrePaquete) VALUES 
(4, 'Noticias');


CREATE TABLE ".$siNoExiste." ".$prefijo."0Rol (
  idRol int(11) NOT NULL ".$autoIncremento.",
  nombreRol varchar(30) NOT NULL,
  PRIMARY KEY  (idRol)
) ".$final.";
INSERT INTO ".$prefijo."0Rol (idRol, nombreRol) VALUES 
(1, 'administrador');

CREATE TABLE ".$siNoExiste." ".$prefijo."0RolCasoUso (
  idRol int(11) NOT NULL,
  idCasoUso int(11) NOT NULL,
  condiciones text NULL,
  PRIMARY KEY  (idRol,idCasoUso)
) ".$final.";
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 2, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 3, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 4, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 5, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 6, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 7, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 8, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 9, '');
INSERT INTO ".$prefijo."0RolCasoUso (idRol, idCasoUso, condiciones) VALUES 
(1, 11, '');

CREATE TABLE ".$siNoExiste." ".$prefijo."0Sesion (
  idSesion int(11) NOT NULL ".$autoIncremento.",
  datosSesion text NOT NULL,
  PRIMARY KEY  (idSesion)
) ".$final.";


CREATE TABLE ".$siNoExiste." ".$prefijo."0Usuario (
  idUsuario int(11) NOT NULL ".$autoIncremento.",
  user varchar(20) NOT NULL,
  pass varchar(32) NOT NULL,
  correo varchar(100) NOT NULL,
  xmlPropiedades text NOT NULL,
  PRIMARY KEY  (idUsuario)
  ".((strcmp($motor, "mysql")==0)?", UNIQUE KEY user (user)":"")."
) ".$final.";
INSERT INTO ".$prefijo."0Usuario (idUsuario, user, pass, correo, xmlPropiedades) VALUES 
(1, 'anonimo', '81dc9bdb52d04dc20036dbd8313ed055', 'a@a.com', '');
INSERT INTO ".$prefijo."0Usuario (idUsuario, user, pass, correo, xmlPropiedades) VALUES 
(2, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'admin@a.com', '');


CREATE TABLE ".$siNoExiste." ".$prefijo."0UsuarioCasoUso (
  idCasoUso int(11) NOT NULL,
  idUsuario int(11) NOT NULL,
  condiciones text NULL,
  PRIMARY KEY  (idCasoUso,idUsuario)
) ".$final.";
INSERT INTO ".$prefijo."0UsuarioCasoUso (idCasoUso, idUsuario, condiciones) VALUES 
(1, 1, '');
INSERT INTO ".$prefijo."0UsuarioCasoUso (idCasoUso, idUsuario, condiciones) VALUES 
(10, 1, '');
INSERT INTO ".$prefijo."0UsuarioCasoUso (idCasoUso, idUsuario, condiciones) VALUES 
(10, 2, '');


CREATE TABLE ".$siNoExiste." ".$prefijo."0UsuarioRol (
  idUsuario int(11) NOT NULL,
  idRol int(11) NOT NULL,
  PRIMARY KEY  (idUsuario,idRol)
) ".$final.";
INSERT INTO ".$prefijo."0UsuarioRol (idUsuario, idRol) VALUES 
(2, 1);


CREATE TABLE ".$siNoExiste." ".$prefijo."1Nodo (
  idNodo int(11) NOT NULL ".$autoIncremento.",
  idAutor int(11) NOT NULL,
  path varchar(30) NOT NULL,
  fecha timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  titulo text NOT NULL,
  xmlContenidoCorto text NOT NULL,
  xmlContenidoCompleto text NOT NULL,
  PRIMARY KEY (idNodo)
) ".$final.";

INSERT INTO ".$prefijo."1Nodo (idNodo, idAutor, path, fecha, titulo, xmlContenidoCorto, xmlContenidoCompleto) VALUES
(1, 2, 'Principal', CURRENT_TIMESTAMP, 'Pagina Inicial', 'Contenido ejemplo', '<Wiki>
== Ejemplo de contenido ==
Este es un contenido de ejemplo.
</Wiki>');



CREATE TABLE IF NOT EXISTS ".$prefijo."0Logs (
  idLog int(11) NOT NULL AUTO_INCREMENT,
  idUsuario int(11) DEFAULT NULL,
  direcionIP varchar(20) NOT NULL,
  fechalog datetime NOT NULL,
  ident char(30) DEFAULT NULL,
  prioridad int(11) DEFAULT NULL,
  mensaje longtext NOT NULL,
  PRIMARY KEY (idLog),
  KEY idUsuario (idUsuario)
) ".$final.";





".$foraneas."
";
//".$procedimientos."
		return $sql;
	}
?>
