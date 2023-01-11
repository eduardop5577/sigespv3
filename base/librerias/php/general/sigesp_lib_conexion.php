<?php
/***********************************************************************************
* @libreria que contiene la conexi�n a la Base de Datos
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirbaselibcon = "";
$dirbaselibcon = dirname(__FILE__);
$dirbaselibcon = str_replace("\\","/",$dirbaselibcon);
$dirbaselibcon = str_replace("/general","",$dirbaselibcon);

require_once($dirbaselibcon."/adodb/adodb.inc.php");
require_once($dirbaselibcon."/adodb/adodb-active-record.inc.php");
require_once($dirbaselibcon."/adodb/adodb-exceptions.inc.php");

class ConexionBaseDatos
{
	public static $conexionbd = null;
	public static $conexionAlternabd = null;
	
	/**
	 * @author Ing. Gerardo Cordero
 	 * @desc Metodo que crea la instacia de conexion a base de datos siguiendo 
 	 *       el patron singleton ya que solo se crea una instacia unica. Este metodo
 	 *       es estatico por tanto puede ser invocado sin necesidad de instaciar la clase
 	 *       que lo contiene
     * @return conexionbd - Instancia unica de conexion a base de datos
     */
	public static function getInstanciaConexion(){
		if (self::$conexionbd==null) {
			try {
				$host = $_SESSION["ls_hostname"].':'.$_SESSION['ls_port'];
				$gestor = $_SESSION["ls_gestor"];
				self::$conexionbd = ADONewConnection($gestor);
				if($gestor=='MYSQLI')
				{				
					self::$conexionbd->Connect($_SESSION["ls_hostname"],$_SESSION["ls_login"],$_SESSION["ls_password"],$_SESSION["ls_database"],$_SESSION['ls_port']);
				}
				else
				{
					self::$conexionbd->Connect($host,$_SESSION["ls_login"],$_SESSION["ls_password"],$_SESSION["ls_database"]);
				}
				
				if (self::$conexionbd != false){
					self::$conexionbd->SetFetchMode(ADODB_FETCH_ASSOC);
					ADOdb_Active_Record::SetDatabaseAdapter(self::$conexionbd);
					//$ADODB_ASSOC_CASE = 0;
					//$ADODB_FORCE_IGNORE = 0;
					if(strtoupper($_SESSION["ls_gestor"])=='OCI8PO')
					{
						self::$conexionbd->Execute("ALTER SESSION SET NLS_NUMERIC_CHARACTERS = '.,'");
						self::$conexionbd->Execute("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD'");
					}
				}
			} 
			catch (Exception $e) {
				return false;
			}
		}
		
		return self::$conexionbd;
	}
	
	/**
	 * @author Ing. Gerardo Cordero
 	 * @desc Metodo que crea una conexion a base de datos en este caso se requiere instanciar
 	 *       la clase para invocar al metodo
     * @return conexionbd - Instancia de conexion a base de datos
     */
	public function conectarBD($servidor, $usuario, $clave, $basedatos, $gestor, $puerto ,$flagactiverecord=false){ 
		$conexion=null;
		try{
			$host=$servidor.':'.$puerto;
			$conexion = ADONewConnection($gestor);
			if($gestor=='MYSQLI')
			{
				$conexion->Connect($servidor, $usuario, $clave, $basedatos,$puerto);
			}
			else
			{
				$conexion->Connect($host, $usuario, $clave, $basedatos);
			}
			if($conexion===false){
				return false;
			}
			else{
				$conexion->SetFetchMode(ADODB_FETCH_ASSOC);
				if($flagactiverecord){
					ADOdb_Active_Record::SetDatabaseAdapter($conexion);
				}
			}
		}
		catch (exception $e) {
			return false;
		}
		
		return $conexion;
	}
	
	/**
	 * @author Ing. Gerardo Cordero
 	 * @desc Metodo que crea una instacia de conexion alterna a base de datos siguiendo 
 	 *       el patron singleton ya que solo se crea una instacia unica. Este metodo
 	 *       es estatico por tanto puede ser invocado sin necesidad de instaciar la clase
 	 *       que lo contiene
     * @return conexionAlternabd - Instancia unica de conexion a base de datos
     */
	public static function conectarAlternaBD($servidor, $usuario, $clave, $basedatos, $gestor, $puerto, $flagactiverecord=false){ 
		if (self::$conexionAlternabd==null) {
			try{
				if (empty($puerto)) {
					$host=$servidor;
				}
				else {
					$host=$servidor.':'.$puerto;
				}
				self::$conexionAlternabd = ADONewConnection($gestor);
				if($gestor=='MYSQLI')
				{
					self::$conexionAlternabd->Connect($servidor, $usuario, $clave, $basedatos,$puerto);
				}
				else
				{
					self::$conexionAlternabd->Connect($host, $usuario, $clave, $basedatos);
				}
				if(self::$conexionAlternabd != false){
					self::$conexionAlternabd->SetFetchMode(ADODB_FETCH_ASSOC);
					if($flagactiverecord){
						ADOdb_Active_Record::SetDatabaseAdapter(self::$conexionAlternabd);
					}
					
					if(strtoupper($gestor)=='OCI8'){
						self::$conexionAlternabd->Execute("ALTER SESSION SET NLS_LANGUAGE='SPANISH'");
						self::$conexionAlternabd->Execute("ALTER SESSION SET NLS_TERRITORY='SPAIN'");
						self::$conexionAlternabd->Execute("ALTER SESSION SET NLS_DATE_FORMAT='DD/MM/RR'");
						self::$conexionAlternabd->Execute("ALTER SESSION SET NLS_TIMESTAMP_FORMAT='DD/MM/RR HH24:MI:SSXFF'");
						self::$conexionAlternabd->Execute("ALTER SESSION SET NLS_TIME_FORMAT='HH24:MI:SSXFF'");
					}
				}
			}
			catch (exception $e) {
				echo $e;
				return false;
			}
		}
		
		return self::$conexionAlternabd;
	}
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que segun sea el gestor retorna el comando limit apropiado.
	 * @return string - cadena con el comando limit del gestor en uso.
	 */
	public static function limitSIGESP() {
		$limite = '';
		switch (strtoupper($_SESSION['ls_gestor'])){
	   		case 'MYSQLT':
				$limite = 'LIMIT 0,100';
				break;
	   		case 'MYSQLI':
				$limite = 'LIMIT 0,100';
				break;
			case "POSTGRES": 
				$limite = 'LIMIT 100';
				break;
			case "OCI8PO":
				$limite = 'AND rownum<=100';
				break;
	   }
	   
	   return $limite;
	}
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @param string $campo - identificador del campo de base de datos
	 * @param string $valor - valor a aplicar para el criterio de busqueda
	 * @param string $operador - operador a usar para evaluar el criterio de busqueda
	 * @return string $criterioUpper - retorna el criterio a evaluar en formato mayuscula para evitar el efecto case sensitve
	 */
	public static function criterioUpperSIGESP($campo, $valor, $operador) {
		$criterioUpper = '';
		switch (strtoupper($_SESSION['ls_gestor'])){
	   		case 'MYSQLT':
				$criterioUpper = "{$campo} {$operador} {$valor} ";
				break;
	   		case 'MYSQLI':
				$criterioUpper = "{$campo} {$operador} {$valor} ";
				break;
			case "POSTGRES": 
				$valor = strtoupper($valor);
				$criterioUpper = "UPPER({$campo}) {$operador} {$valor}";
				break;
			case "OCI8PO":
				$valor = strtoupper($valor);
				$criterioUpper = "UPPER({$campo}) {$operador} {$valor}";
				break;
	   }
	   
	   return $criterioUpper;
	}
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo para conectarse via odbc con una base de datos db2
	 * @param string $dbName
	 * @param string $host
	 * @param string $port
	 * @param string $protocol
	 * @param string $user
	 * @param string $pwd
	 * @return null|boolean
	 */
	public function getConexionDB2($dbName, $host, $port, $protocol, $user, $pwd) {
		try {
			$dns="DRIVER={iSeries Access ODBC Driver};SYSTEM=$host;DATABASE={$dbName};PROTOCOL={$protocol}";
			$this->conexionDB2 = ADONewConnection('odbc');
			//$this->conexionDB2->debug = true;	
			$this->conexionDB2->Connect($dns, $user, $pwd);
			if($this->conexionDB2 != false){
				return $this->conexionDB2;
			}
			else {
				return false;
			}
		}
		catch (exception $e) {
			return false;
		}
	}
}

?>
