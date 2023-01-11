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

$dirmodsepdaosigcon = "";
$dirmodsepdaosigcon = dirname(__FILE__);
$dirmodsepdaosigcon = str_replace("\\","/",$dirmodsepdaosigcon);
$dirmodsepdaosigcon = str_replace("/modelo/cfg/dao","",$dirmodsepdaosigcon);
require_once($dirmodsepdaosigcon."/base/librerias/php/general/sigesp_lib_daogenerico.php");

class SigespConfigDao extends DaoGenerico
{

	public function __construct()
	{
		parent::__construct ( 'sigesp_config' );
	}

	public function getFormato($codemp,$codsis,$seccion,$entry,$type)
	{
		$cadenasql = "SELECT sigesp_config.value ".
  					 "  FROM sigesp_config ".
  					 " WHERE sigesp_config.codemp='".$codemp."' ".
  					 "   AND trim(sigesp_config.codsis)='".$codsis."'  ".
  					 "   AND trim(sigesp_config.seccion)='".$seccion."'". 
  					 "   AND trim(sigesp_config.entry)='".$entry."'  ".
  					 "   AND trim(sigesp_config.type)='".$type."'";
		$dataconfig = $this->buscarSql($cadenasql);
		if($dataconfig->_numOfRows>0)
		{
			if ($dataconfig->fields ['value'] == "")
			{
				return "";
			}
			else
			{
				return $dataconfig->fields ['value'];
			}
		}
		else
		{
			return 0;
		}
	}

	public function insertarConfigReporte($codemp,$codsis,$seccion,$entry,$type,$value,$formato)
	{
		$this->codemp  = $codemp;
		$this->codsis  = $codsis;
		$this->seccion = $seccion;
		$this->entry   = $entry;
		$this->type    = $type;
		$this->value   = $value;
		$resultado = $this->incluir();
		
		if($formato==0)
		{
			return $resultado;
		}
		else
		{
			if($resultado==1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}
?>