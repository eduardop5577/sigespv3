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



class SistemaDao extends DaoGenerico{

	public function __construct() {
		parent::__construct ( 'sss_sistemas' );
	}
    
    
	public function getSistemasUsuario($codemp,$codusu){
		$cadena =	" SELECT  sss_sistemas.codsis, sss_sistemas.nomsis , 
							  sss_sistemas.tipsis, sss_sistemas.imgsis, 
	                          sss_sistemas.accsis, sss_sistemas.ordsis 
	                  FROM sss_derechos_usuarios
					  INNER JOIN sss_sistemas 
	                  ON sss_derechos_usuarios.codsis = sss_sistemas.codsis
	                  AND sss_sistemas.tipsis <> '0'
	                  INNER JOIN sss_usuarios
	                  ON sss_derechos_usuarios.codemp = sss_usuarios.codemp
	                  AND sss_derechos_usuarios.codusu = sss_usuarios.codusu
	                  WHERE sss_derechos_usuarios.codemp = '".$codemp."' 
	                  AND sss_derechos_usuarios.codusu = '".$codusu."' 
					  AND sss_derechos_usuarios.enabled = '1'
					  AND sss_derechos_usuarios.enabled <> '0'
					  GROUP BY sss_sistemas.codsis,sss_sistemas.nomsis,sss_sistemas.tipsis,
						       sss_sistemas.imgsis,sss_sistemas.ordsis, sss_sistemas.accsis
					  ORDER BY sss_sistemas.tipsis, sss_sistemas.ordsis";
		return $this->buscarSql($cadena);
	}
}
?>