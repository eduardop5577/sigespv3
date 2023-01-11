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
require_once('sigesp_ctr_cfg_servicio.php');

class servicioSocServicio extends DaoGenericoPlus
{
	private $objcabecera;
	private $arraydetalles = null;
		
	public function __construct($tabcabecera,$arrtabdetalles)
	{
        parent::__construct($tabcabecera,$arrtabdetalles);
    }
    
	public function grabarServicio($arrjson,$codemp)
	{
		$oservicio = new ServicioCfg('soc_servicios');
		$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
		ServicioCfg::iniTransaccion ();
		$nronuevo=$oservicio->daogenerico->buscarCodigo('codser',true,10,'','SOC','SOCSVR',$_SESSION['la_logusr'],'','');
		if($nronuevo==$arrjson->datoscabecera[0]->codser)
		{
			$nronuevo=$oservicio->daogenerico->buscarCodigo('codser',true,10,'','SOC','SOCSVR',$_SESSION['la_logusr'],'','');
		}
		if($nronuevo!=$arrjson->datoscabecera[0]->codser)
		{
			$arrjson->datoscabecera[0]->codser=$nronuevo;
		}
		$resultado =$oservicio->incluirDto($arrjson->datoscabecera[0],true,'codser',true,10);
		if (ServicioCfg::comTransaccion ())
		{
			$resultados='2|1';
		}
		else
		{
			$resultados='0|0';
		}
		return $resultados;
	}
	
	public function getServicios($codemp)
	{
		$cadenasql ="SELECT soc_servicios.codser, MAX(soc_servicios.codtipser) AS codtipser, MAX(soc_tiposervicio.dentipser) AS dentipser, MAX(soc_servicios.denser) AS denser, MAX(soc_servicios.preser) AS preser, ".
					"		MAX(soc_servicios.spg_cuenta) AS spg_cuenta, MAX(spg_cuentas.denominacion) AS denominacion, MAX(soc_servicios.codunimed) AS codunimed, MAX(siv_unidadmedida.denunimed) AS denunimed ".
  					"  FROM soc_servicios ".
  					" INNER JOIN soc_tiposervicio ".
					"    ON soc_servicios.codemp=soc_tiposervicio.codemp ".
					"   AND soc_servicios.codtipser=soc_tiposervicio.codtipser ".
  					"  LEFT OUTER JOIN spg_cuentas ".
					"    ON soc_servicios.codemp=spg_cuentas.codemp ".
					"   AND soc_servicios.spg_cuenta=spg_cuentas.spg_cuenta ".
  					"  LEFT OUTER JOIN siv_unidadmedida ".
					"    ON soc_servicios.codunimed=siv_unidadmedida.codunimed ".
  					"  LEFT OUTER JOIN soc_serviciocargo ".
					"    ON soc_servicios.codemp=soc_serviciocargo.codemp ".
					"   AND soc_servicios.codser=soc_serviciocargo.codser ".
  					" WHERE soc_servicios.codemp = '{$codemp}' ".
  					" GROUP BY soc_servicios.codser".
  					" ORDER BY soc_servicios.codser";
		$this->objcabecera = $this->getCabecera();
		return $this->objcabecera->buscarSql($cadenasql);
	}
	
	public function eliminarDtoServicio($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$datosCabecera    = $arrjson->datoscabecera[0];
		$arrtabignorar[0] = 'soc_servicios';
		$arrtabignorar[1] = 'soc_serviciocargo';
		return $this->eliminarDto(true, 'codser', $datosCabecera->codser, $arrtabignorar);
	}
}
?>