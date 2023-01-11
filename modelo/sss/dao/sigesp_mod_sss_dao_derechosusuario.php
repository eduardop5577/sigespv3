<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirmodsssdaousu = "";
$dirmodsssdaousu = dirname(__FILE__);
$dirmodsssdaousu = str_replace("\\","/",$dirmodsssdaousu);
$dirmodsssdaousu = str_replace("/modelo/sss/dao","",$dirmodsssdaousu);
require_once ($dirmodsssdaousu."/base/librerias/php/general/sigesp_lib_daogenerico.php");


class DerechoUsuario extends DaoGenerico{
	private $conexionbd = null;
	
	public function __construct() {
		parent::__construct ( 'sss_derechos_usuarios' );
	}
    
    
	function obtenerDerechosUsuario($as_empresa,$as_usuario,$as_sistema,$as_ventana,$ls_enabled) {
		$consulta= "SELECT * FROM sss_derechos_usuarios".
					" WHERE codemp='".$as_empresa."'".
					"   AND codusu='".$as_usuario."'".
					"   AND codsis='".$as_sistema."'".
					"   AND enabled=".$ls_enabled." ";
		return $this->buscarSql($consulta);
	}
	
	function getMenuUsuario($as_codemp,$as_codsis,$as_codusu) {
		$consulta = "SELECT sv.nomfisico,sv.codmenu,du.visible
  						FROM sss_sistemas_ventanas sv
  						INNER JOIN sss_derechos_usuarios du ON sv.codmenu=du.codmenu AND sv.codsis=du.codsis
  						WHERE sv.nomfisico <> ''  AND du.codemp='".$as_codemp."'
  							  AND sv.codsis='".$as_codsis."' AND du.codusu='".$as_codusu."' 
  						ORDER BY sv.orden";
		return $this->buscarSql($consulta);
	}
}
?>