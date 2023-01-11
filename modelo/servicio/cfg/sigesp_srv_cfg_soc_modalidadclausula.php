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

require_once ("../../base/librerias/php/general/sigesp_lib_daogenericoplus.php");

class servicioModalidadClausula extends DaoGenericoPlus
{
	private $arraydetalles = null;
	
	public function __construct()
	{
		$this->arraydetalles[0] = 'esp_soc_dtm_clausulas';
        parent::__construct('soc_modalidadclausulas',$this->arraydetalles);
    }
    
    public function getCodigo($codemp)
	{
    	$this->getCabecera()->codemp=$codemp;
    	return $this->getCabecera()->buscarCodigo('codtipmod',true,2);
    }
    
	public function getClausulas($codemp)
	{
		$objdetalle = $this->getInstaciaDetalle('soc_clausulas');
		return $objdetalle->leerTodos('codcla',1,$codemp);
	} 
	
	public function getModclausulas($codemp)
	{
		return $this->getCabecera()->leerTodos('codtipmod',0,$codemp);
	}
	
	public function getDetalle($codemp,$codtipmod)
	{
		$cadenasql  = "SELECT soc_dtm_clausulas.codcla, soc_clausulas.dencla ".
  					  "  FROM soc_dtm_clausulas ".
  					  "	INNER JOIN soc_clausulas  ".
					  "    ON soc_dtm_clausulas.codemp=soc_clausulas.codemp ".
					  "   AND soc_dtm_clausulas.codcla=soc_clausulas.codcla ".
  					  " WHERE soc_dtm_clausulas.codemp='".$codemp."' ".
					  "   AND codtipmod='".$codtipmod."'";
		$objdetalle = $this->getInstaciaDetalle('soc_clausulas');
		return $objdetalle->buscarSql($cadenasql);
	}
	
	function grabarModclausula($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$resultado = $this->incluirDto();
		return $resultado;
	}
	
	function eliminarModclausula($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$datosCabecera    = $arrjson->datoscabecera[0];
		$arrtabignorar[0] = 'soc_modalidadclausulas';
		$arrtabignorar[1] = 'soc_dtm_clausulas';
		return $this->eliminarDto(true, 'codtipmod', $datosCabecera->codtipmod, $arrtabignorar);
	}
}
?>