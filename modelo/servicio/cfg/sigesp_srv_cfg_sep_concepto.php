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

require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/base/librerias/php/general/sigesp_lib_daogenericoplus.php');

class servicioSepConcepto extends DaoGenericoPlus
{
	private $arraydetalles = null;
	
	public function __construct()
	{
		$this->arraydetalles[0] = 'esp_sep_conceptocargos';
        parent::__construct('sep_conceptos',$this->arraydetalles);
    }
    
    function grabarConcepto($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$resultado = $this->incluirDto();
		return $resultado;
	}
	
	function eliminarConcepto($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$datosCabecera    = $arrjson->datoscabecera[0];
		$arrtabignorar[0] = 'sep_conceptos';
		$arrtabignorar[1] = 'sep_conceptocargos';
		return $this->eliminarDto(true, 'codconsep', $datosCabecera->codconsep, $arrtabignorar);
	}
}
?>