<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_include
{
	var $msg;
	public function __construct() // contructor
	{
		require_once("sigesp_lib_mensajes.php");
		require_once("sigesp_lib_sql.php");
		if(!defined('ADODB_ASSOC_CASE'))
		{
			define('ADODB_ASSOC_CASE', 0);
		}
		$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
		require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_conexion.php');
		$this->msg=new class_mensajes();	
	}

	/*
	 * Metodo conexion modificado para usar la clase sigesp_lib_conexion de version2
	 * @autor: Ing. Gerardo Cordero
	 * fecha: 19-03-2013
	 */
	function uf_conectar () {
		return ConexionBaseDatos::getInstanciaConexion();
	}
	
 	function uf_conectar_otra_bd ($as_hostname, $as_login, $as_password,$as_database,$as_gestor,$as_puerto='') 
	{ 
		$conec=&ADONewConnection($as_gestor);
		if($as_puerto=='')
		{
			if(($as_gestor=='MYSQLT') || ($as_gestor=='MYSQLI'))
			{
				$as_puerto='3306';
			}
			if($as_gestor=='POSTGRES')
			{
				$as_puerto='5432';
			}
		}
		$servidor=$as_hostname.':'.$as_puerto;
		$conec->Connect($as_hostname, $as_login, $as_password,$as_database); 
		
		$conec->SetFetchMode(ADODB_FETCH_ASSOC);
		if($conec===false)
		{
			$this->msg->message("No pudo conectar al servidor de base de datos, contacte al administrador del sistema");				
			exit();
		}
		return $conec;
	}
	
	function uf_obtener_parametros_conexion($as_path,$as_database,$as_hostname,$as_login,$as_password,$as_gestor)
	{
		/*require_once($as_path."sigesp_config.php");
		$as_hostname="";
		$as_login="";
		$as_password="";
		$as_gestor="";
		for($li_i=1;$li_i<=$i;$li_i++)
		{
			if($empresa["database"][$li_i]==$as_database)
			{
				$as_hostname=$empresa["hostname"][$li_i];
				$as_login=$empresa["login"][$li_i];
				$as_password=$empresa["password"][$li_i];
				$as_gestor=$empresa["gestor"][$li_i];
			}	
		}
		$arrResultado['as_hostname']=$as_hostname;
		$arrResultado['as_login']=$as_login;
		$arrResultado['as_password']=$as_password;
		$arrResultado['as_gestor']=$as_gestor;
		return $arrResultado;	*/	
	}
	
	function uf_conectar_odbc_db2()
	{
		require_once("sigesp_lib_conexion_odbc_db2.php");
	    
		if((defined("DSN_DB2"))&&(defined("LOGIN_DB2"))&&(defined("PASSWORD_DB2"))&&(defined("DATABASE_DB2")))
		{
			
			if((trim(DSN_DB2) != "")&&(trim(LOGIN_DB2) != "")&&(trim(PASSWORD_DB2) != "")&&(trim(DATABASE_DB2) != ""))
			{
				$conec=&ADONewConnection('odbc_db2');
					 
				$conec->Connect(DSN_DB2,LOGIN_DB2,PASSWORD_DB2,DATABASE_DB2); 
				
				$conec->SetFetchMode(ADODB_FETCH_ASSOC);
				if($conec===false)
				{
					$this->msg->message("No pudo conectar al servidor de base de datos, contacte al administrador del sistema");				
					exit();
				}
				return $conec;
			}
			else
			{
			 $this->msg->message("Se deben completar todos los parametros de conexion, contacte a su administrador del sistema");				
			 exit();
			}
	    }
		else
		{
		 $this->msg->message("No se han definido los parametros de conexion, contacte a su administrador del sistema");				
		 exit();
		}

	}
}
?>
