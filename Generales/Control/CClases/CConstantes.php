<?php

	define("BD",101);
	
	define("IGUAL",201);
	define("CONTIENE",202);
	define("MAYOR_IGUAL",203);
	define("MENOR_IGUAL",204);
	define("MAYOR",205);
	define("MENOR",206);
	define("DIFERENTE",207);
	
	define("Y",301);
	define("O",302);
	
	class CConstantes{
		public static function codToString($codigo){
			switch($codigo){
				case 101: return "Base de datos";
				case 201: return "=";
				case 202: return "contiene";
				case 203: return ">=";
				case 204: return "<=";
				case 205: return ">";
				case 206: return "<";
				case 207: return "!=";
				case 301: return "Y";
				case 302: return "O";
			}
			return "";
		}
	}
?>
