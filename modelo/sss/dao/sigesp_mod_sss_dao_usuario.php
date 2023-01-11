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


class UsuarioDao extends DaoGenerico{
	private $conexionbd = null;
	
	public function __construct() {
		parent::__construct ( 'sss_usuarios' );
	}
    
    
	public function validarUsuario($codemp,$codusu,$pwdusu,$actsess){
		$cadena =	" codemp='".$codemp."' AND codusu='".$codusu."' AND pwdusu='".$pwdusu."' ";
		$resultado = $this->Load($cadena);
		if($resultado){
			if ($this->codusu!=""){
				$this->ultingusu=date('Y/m/d');
				$resultado = $this->modificar();
				if($resultado!=0){
					if($actsess){
						$_SESSION['la_cedusu']=$this->cedusu;
						$_SESSION['la_nomusu']=$this->nomusu;
						$_SESSION['la_apeusu']=$this->apeusu;
						$_SESSION['la_codusu']=$this->codusu;
						$_SESSION['la_pasusu']=$this->pwdusu;
						$_SESSION['la_logusr']=$this->codusu;
					}
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
	
	public function getUsuarioSession($codemp,$codusu){
		$this->conexionbd = $this->obtenerConexionBd();
		$consultafecha = $this->conexionbd->OffsetDate(0, $this->conexionbd->sysTimeStamp);
		$consultafecha = $this->conexionbd->SQLDate('d/m/Y h:i A', $consultafecha);
		$consulta = "SELECT nomusu,apeusu,(".$consultafecha.") AS fecha,1 as valido
					 FROM ".$this->_table." 
					 WHERE codemp='".$codemp."' 
					 AND codusu='".$codusu."'";
		return $this->buscarSql($consulta);
	}
}
?>