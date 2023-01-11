<?php
/***********************************************************************************
* @fecha de modificacion: 26/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/servicio/cfg/sigesp_srv_cfg_sep_concepto.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/servicio/cfg/sigesp_srv_cfg_soc_servicio.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/servicio/cfg/sigesp_srv_cfg_soc_modalidadclausula.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/servicio/cfg/sigesp_srv_cfg_scb_chequera.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/servicio/cfg/sigesp_srv_cfg_scb_colocacion.php');

class ServicioCfg
{
	public $daogenerico;
	private $scgcuentadao;
	private $spicuentadao;
	private $colocaciondao;
	private $derechosdao;
	private $modclausuladao;
	private $sepconceptodao;
	private $socservicio;
	private $codemp;


	public function __construct($tabla='')
	{
		if ($tabla != '')
		{
			$this->daogenerico = new DaoGenerico ($tabla);
		}		
	}
	
	/*public function ServicioCfg($tabla='')
	{print "Primero";
		if ($tabla != '')
		{
			$this->daogenerico = new DaoGenerico ($tabla);
		}
	}*/

	public function getDto($cadenapk)
	{
		$resultado=$this->daogenerico->load($cadenapk);
		if($resultado)
		{
			return $this->daogenerico;
		}
		else
		{
			return $resultado;
		}
	}
	
	public function getDaogenerico()
	{
		return $this->daogenerico;
	}

	public function getCodemp()
	{
		return $this->daogenerico->codemp;
	}

	public function setCodemp($codemp)
	{
		$this->daogenerico->codemp = $codemp;
	}
	
	public function setValoresDefectoEmpresa($arrValores)
	{
		$this->daogenerico->codemp = $arrValores['codemp'];
		$this->daogenerico->ciesem1 = $arrValores['ciesem1'];
		$this->daogenerico->ciesem2 = $arrValores['ciesem2'];
	}

	/***********************************/
	/* Metodos Estandar DAO Generico   */
	/***********************************/

	public static function iniTransaccion()
	{
		DaoGenerico::iniciarTrans ();
	}

	public static function comTransaccion()
	{
		return DaoGenerico::completarTrans ();
	}

	public function incluirDto($dto, $multiusuario=false, $consecutivo="", $validarempresa=true, $longitud = 0)
	{
		$this->pasarDatos ( $dto );
		$resultado=$this->daogenerico->incluir ($multiusuario,$consecutivo,$validarempresa,$longitud);
		return $resultado;
	}

	public function modificarDto($dto,$validarexistencia=false)
	{
		$this->pasarDatos ( $dto );
		try
		{
			return $this->daogenerico->modificar($validarexistencia);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function eliminarDto($dto,$campovalidar='',$valor='')
	{
		$errorNo = '';
		$this->pasarDatos ( $dto );
		try
		{
			if(!$this->daogenerico->eliminar($campovalidar,$valor))
			{
				if($this->daogenerico->errorValidacion)
				{
					$errorNo = '-1';
				}
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		return $errorNo;		
	}

	public function pasarDatos($ObJson)
	{
		$arratributos = $this->daogenerico->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO )
		{
			foreach ( $ObJson as $IndiceJson => $valorJson )
			{
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp")
				{
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
				} 
			}
		}
	}
	
	public function validarEliminar($campo, $valor, $arrtablaignorar=null) 
	{
		return $this->daogenerico->validarRelacionesPlus($campo, $valor, $arrtablaignorar);
	}

	public function buscarTodos($campoorden="",$tipoorden=0)
	{
		return $this->daogenerico->leerTodos ($campoorden,$tipoorden);
	}

	public function buscarCampo($campo, $valor)
	{
		return $this->daogenerico->buscarCampo ( $campo, $valor );
	}

	public function buscarCampoRestriccion($restricciones,$banderatabla=false,$tabla='')
	{
		return $this->daogenerico->buscarCampoRestriccion($restricciones,$banderatabla,$tabla) ;
	}

	public function buscarSql($cadenasql)
	{
		return $this->daogenerico->buscarSql($cadenasql) ;
	}

	public function concatenarSQL($arreglocadena)
	{
		return $this->daogenerico->concatenarCadena($arreglocadena);
	}

	public function obtenerConexionBd()
	{
		return $this->daogenerico->obtenerConexionBd();
	}
	/***************************************/
	/* Fin Metodos Estandar DAO Generico   */
	/***************************************/

	/***************************************/
	/* Metodos Asociados al Servicio       */
	/***************************************/

	public function buscarCodigoEmpresa()
	{
		return $this->daogenerico->buscarCodigo('codemp',true,4);
	}
	
	public function buscarCodigoPais()
	{
		return $this->daogenerico->buscarCodigo ('codpai',false,3);
	}
	
	public function buscarCodigoEstado($codpai)
	{
		$filtro['codpai']=$codpai;
		return $this->daogenerico->buscarCodigo ('codest',false,3,$filtro);
	}

	public function buscarCodigoMunicipio($codpai,$codest)
	{
		$filtro['codpai']=$codpai;
		$filtro['codest']=$codest;
		return $this->daogenerico->buscarCodigo ('codmun',false,3,$filtro);
	}

	public function buscarCodigoParroquia($codpai,$codest,$codmun)
	{
		$filtro['codpai']=$codpai;
		$filtro['codest']=$codest;
		$filtro['codmun']=$codmun;
		return $this->daogenerico->buscarCodigo ('codpar',false,3,$filtro);
	}

	public function buscarCodigoComunidad($codpai,$codest,$codmun,$codpar)
	{
		$filtro['codpai']=$codpai;
		$filtro['codest']=$codest;
		$filtro['codmun']=$codmun;
		$filtro['codpar']=$codpar;
		return $this->daogenerico->buscarCodigo ('codcom',false,3,$filtro);
	}

	public function buscarCodigoCiudad($codpai,$codest)
	{
		$filtro['codpai']=$codpai;
		$filtro['codest']=$codest;
		return $this->daogenerico->buscarCodigo ('codciu',false,3,$filtro);
	}

	public function buscarCodigoControlNumero()
	{
		return $this->daogenerico->buscarCodigo ('id',true,4);
	}

	public function verificarControlNumero($procede,$codusu,$prefijo)
	{
		$total=0;
		$tabla = '';
		$criterio = '';
		switch($procede)
		{
			case 'SEPSPC':
				$tabla = 'sep_solicitud';
				$criterio = " numsol LIKE '$prefijo%'  AND codusu='$codusu'";
			break;
			
			case 'SOCCOC':
				$tabla = 'soc_ordencompra';
				$criterio = " numordcom LIKE '$prefijo%' AND estcondat='B' AND codusureg='$codusu'";
			break;
			
			case 'SOCCOS':
				$tabla = 'soc_ordencompra';
				$criterio = " numordcom LIKE '$prefijo%' AND estcondat='S' AND codusureg='$codusu'";
			break;

			case 'CXPSOP':
				$tabla = 'cxp_solicitudes';
				$criterio = " numsol LIKE '$prefijo%' AND codusureg='$codusu'";
			break;

			case 'SCBBRE':
				$tabla = 'scb_movbco';
				$criterio = " numdoc LIKE '$prefijo%' AND codusu='$codusu'";
			break;
			
			case 'SCGCMP':
				$tabla = 'sigesp_cmp';
				$criterio = " comprobante LIKE '$prefijo%' AND procede='SCGCMP' AND codusu='$codusu'";
			break;
			
			case 'SPGCMP':
				$tabla = 'sigesp_cmp';
				$criterio = " comprobante LIKE '$prefijo%' AND procede='SPGCMP' AND codusu='$codusu'";
			break;
			
			case 'SPGCRA':
				$tabla = 'sigesp_cmp_md';
				$criterio = " comprobante LIKE '$prefijo%' AND procede='SPGCRA' AND codusu='$codusu'";
			break;
			
			case 'SPGTRA':
				$tabla = 'sigesp_cmp_md';
				$criterio = " comprobante LIKE '$prefijo%' AND procede='SPGTRA' AND codusu='$codusu'";
			break;
			
			case 'SPGINS':
				$tabla = 'sigesp_cmp_md';
				$criterio = " comprobante LIKE '$prefijo%' AND procede='SPGINS' AND codusu='$codusu'";
			break;
			
			case 'SPGREC':
				$tabla = 'sigesp_cmp_md';
				$criterio = " comprobante LIKE '$prefijo%' AND procede='SPGREC' AND codusu='$codusu'";
			break;
			
		}
		
		$cadenasql="SELECT COUNT(codemp) as total ".
				   "  FROM $tabla ".
				   " WHERE codemp='".$this->daogenerico->codemp."' ".
				   "   AND ".$criterio."";
		$resultado = $this->buscarSql($cadenasql);
		if(!$resultado->EOF)
		{
			$total = $resultado->fields['total'];
		}
		return $total;
	}

	
	public function buscarCodigoUnidadTributaria()
	{
		return $this->daogenerico->buscarCodigo ('codunitri',true,4);
	}

	public function verificarunidadtributaria($codemp)
	{
		$total=0;
		$cadenasql="SELECT COUNT(codconc) as total ".
				   "  FROM sno_concepto ".
				   " WHERE codemp='".$codemp."' ".
				   "   AND (forcon LIKE '%UNIDADTRIBUTARIA%' ".
				   "    OR  forpatcon LIKE '%UNIDADTRIBUTARIA%') ";
		$resultado = $this->buscarSql($cadenasql);
		if(!$resultado->EOF)
		{
			$total = $resultado->fields['total'];
		}
		return $total;
	}

	public function buscarCodigoMoneda()
	{
		return $this->daogenerico->buscarCodigo ('codmon',true,3);
	}

	public function verificarMonedaPrincipal($codemp,$codmon,$estatuspri)
	{
		$valido=true;
		$cadenasql="SELECT codmon ".
				   "  FROM sigesp_moneda ".
				   " WHERE codemp='".$codemp."' ".
				   "   AND estatuspri = '1' ";
		$resultado = $this->buscarSql($cadenasql);
		if (!$resultado->EOF)
		{
			$codmonaux=$resultado->fields ['codmon'];
			if(($codmonaux!=$codmon)&&($estatuspri=="1"))
			{
				$valido=false;
			}
		}
		return $valido;
	}

 	public function eliminarDetallesMoneda($codemp,$codmon)
	{
		$valido = true;
		$cadenasql= "DELETE FROM sigesp_dt_moneda ".
			    " WHERE codemp ='".$codemp."' ".
                            "   AND codmon ='".$codmon."' ";
		$result = $this->daogenerico->ejecutarSql($cadenasql);
		if ($result===false)
		{
			$valido = false;
		}
		return $valido;
	}
	
	public function buscarCodigoTipoSolicitud()
	{
		return $this->daogenerico->buscarCodigo('codtipsol',true,2);
	}

	public function verificarTipoSolicitud($codemp,$modsep,$estope)
	{
		$existe=false;
		$cadenasql="SELECT COUNT(modsep) as total ".
				   "  FROM sep_tiposolicitud ".
				   " WHERE codemp='".$codemp."' ".
				   "   AND modsep = '".$modsep."' ".
				   "   AND estope = '".$estope."' ";
		$resultado = $this->buscarSql($cadenasql);
		if (!$resultado->EOF)
		{
			$total=$resultado->fields ['total'];
			if($total>0)
			{
				$existe=true;
			}
		}
		return $existe;
	}

	public function buscarCodigoSepConcepto()
	{
		return $this->daogenerico->buscarCodigo('codconsep',true,5);
	}

	public function guardarConcepto($arrjson,$codemp)
	{
		$this->sepconceptodao = new servicioSepConcepto();
		$resultado = $this->sepconceptodao->grabarConcepto($arrjson,$codemp);
		unset($this->sepconceptodao);
		return $resultado;
	}
	
	public function eliminarConcepto($arrjson,$codemp)
	{
		$this->sepconceptodao = new servicioSepConcepto();
		$resultado = $this->sepconceptodao->eliminarConcepto($arrjson, $codemp);
		unset($this->sepconceptodao);
		return $resultado;
	}	
	
	public function buscarCargos($codemp,$tipocargo)
	{
		$cadenasql="";
		if ($tipocargo=='G')
		{
			$cadenasql="SELECT * ".
					   "  FROM sigesp_cargos ".
					   " WHERE codemp='".$codemp."' ".
					   "   AND (tipo_iva=1 OR tipo_iva=2) ".
					   " ORDER BY codcar";
		}
		else
		{
			$cadenasql="SELECT * ".
					   "  FROM sigesp_cargos ".
					   " WHERE codemp='".$codemp."' ".
					   "   AND tipo_iva=3 ".
					   " ORDER BY codcar";
		}
		$resultado = $this->buscarSql($cadenasql);
		return $resultado;
	}
	
	public function buscarCodigoTipoServicio()
	{
		return $this->daogenerico->buscarCodigo('codtipser',true,4);
	}
	
	public function buscarCodigoSocServicio()
	{
		return $this->daogenerico->buscarCodigoSinPrefijo('codser',true,10);
	}
	
	function guardarServicio($arrjson,$codemp)
	{
		$arrtabdetalles[0] = 'esp_soc_serviciocargo';
		$this->socservicio = new ServicioSocServicio('soc_servicios', $arrtabdetalles);
		$resultado = $this->socservicio->grabarServicio($arrjson, $codemp);
		return $resultado;
		unset($this->socservicio);
	}
	
	function buscarServicios($codemp)
	{
		$arrtabdetalles[0] = 'esp_soc_serviciocargo';
		$this->socservicio = new ServicioSocServicio('soc_servicios', $arrtabdetalles);
		return $this->socservicio->getServicios($codemp);
		unset($this->socservicio);
	}
	
	function eliminarServicio($arrjson,$codemp)
	{
		$arrtabdetalles[0] = 'esp_soc_serviciocargo';
		$this->socservicio = new ServicioSocServicio('soc_servicios', $arrtabdetalles);
		return $this->socservicio->eliminarDtoServicio($arrjson, $codemp);
		unset($this->socservicio);
	}
	
	public function buscarCodigoClausula()
	{
		return $this->daogenerico->buscarCodigo('codcla',true,6);
	}
	
	public function buscarCodigoModclausula($codemp)
	{
		$this->modclausuladao = new servicioModalidadClausula();
		$resultado = $this->modclausuladao->getCodigo($codemp);
		unset($this->modclausuladao);
		return $resultado;
	}
	public function guardarModclausula($arrjson,$codemp)
	{
		$this->modclausuladao = new servicioModalidadClausula();
		$resultado = $this->modclausuladao->grabarModclausula($arrjson, $codemp);
		unset($this->modclausuladao);
		return $resultado;
	}
	
	public function buscarModclausula($codemp)
	{
		$this->modclausuladao = new servicioModalidadClausula();
		$resultado = $this->modclausuladao->getModclausulas($codemp);
		unset($this->modclausuladao);
		return $resultado;
	}
	
	public function buscarDetModclausula($codemp, $codtipmod)
	{
		$this->modclausuladao = new servicioModalidadClausula();
		$resultado = $this->modclausuladao->getDetalle($codemp, $codtipmod);
		unset($this->modclausuladao);
		return $resultado;
	}
	
	public function eliminarModclausula($arrjson,$codemp)
	{
		$this->modclausuladao = new servicioModalidadClausula();
		$resultado = $this->modclausuladao->eliminarModclausula($arrjson, $codemp);
		unset($this->modclausuladao);
		return $resultado;
	}
		
	public function buscarCodigoDeduccion($codemp)
	{
		return $this->daogenerico->buscarCodigo('codded',true,5);
	}

	public function guardarDeduccion($objjson)
	{
		$this->pasarDatos ( $objjson );
		switch ($objjson->tipodeduccion)
		{
			case 'S':
				$this->daogenerico->islr=1;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'I':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=1;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'M':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=1;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'O':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=1;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'A':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=1;
				$this->daogenerico->estretmil=0;
				break;
			case '1':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=1;
				break;
		}
		$resultado=$this->daogenerico->incluir ();
		return $resultado;
	}
	
	public function modificarDeduccion($objjson)
	{
		$this->pasarDatos ( $objjson );
		switch ($objjson->tipodeduccion)
		{
			case 'S':
				$this->daogenerico->islr=1;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'I':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=1;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'M':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=1;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'O':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=1;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=0;
				break;
			case 'A':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=1;
				$this->daogenerico->estretmil=0;
				break;
			case '1':
				$this->daogenerico->islr=0;
				$this->daogenerico->iva=0;
				$this->daogenerico->estretmun=0;
				$this->daogenerico->otras=0;
				$this->daogenerico->retaposol=0;
				$this->daogenerico->estretmil=1;
				break;
		}
		return $this->daogenerico->modificar();
	}
	
	public function buscarCodigoCargos()
	{
		return $this->daogenerico->buscarCodigo('codcar',true,5);
	}	

	public function buscarCodigoDocumento($codemp)
	{
		return $this->daogenerico->buscarCodigo('codtipdoc',true,5);
	}	
	
	public function buscarCodigoConcepto($codemp)
	{
		return $this->daogenerico->buscarCodigo('codcla',true,2);
	}
	
	public function buscarCodigoBanco()
	{
		return $this->daogenerico->buscarCodigo('codban',true,3);
	}	
	
	public function buscarCodigoAgencia()
	{
		return $this->daogenerico->buscarCodigo('codage',true,10);
	}	

	public function buscarAgencias()
	{
		$cadenasql	=	"SELECT scb_agencias.*,scb_banco.nomban ".
  						"  FROM scb_agencias ".
  						" INNER JOIN scb_banco ". 
  					    "	 ON scb_agencias.codemp=scb_banco.codemp ".
						"   AND scb_agencias.codban=scb_banco.codban ".
  						"	ORDER BY scb_agencias.codage";
		 
		return $this->daogenerico->buscarSql($cadenasql);
	}

	public function buscarCodigoTipoCuenta($codemp)
	{
		return $this->daogenerico->buscarCodigo('codtipcta',true,3);
	}
	
	function obtenerChequeraBanco($codemp,$codban)
	{
		$chequera = new servicioChequera();
		$chequera->codemp=$codemp;
		$chequera->codban=$codban;
		$resultado=$chequera->obtenerCatalogoCuentaBancoChequera();
		return $resultado;
	}
	
	public function buscarCodigoConmovimiento()
	{
		return $this->daogenerico->buscarCodigo('codconmov',true,3);
	}

	public function buscarCodigoTipoColocacion($codemp)
	{
		return $this->daogenerico->buscarCodigo('codtipcol',true,3);
	}
	
	public function guardarColocacion($arrjson,$codemp)
	{
		$arrtabdetalles[0]='esp_scb_dt_colocacion';
		$this->servicioColocacion = new servicioColocacion('scb_colocacion',$arrtabdetalles);
		$resultado = $this->servicioColocacion->grabarColocacion($arrjson,$codemp);
		unset($this->servicioColocacion);
		return $resultado;
	}

	public function buscarColocaciones($codemp,$numctaban,$dencol,$nomban)
	{
		$arrtabdetalles[0]='esp_scb_dt_colocacion';
		$this->servicioColocacion = new servicioColocacion('scb_colocacion',$arrtabdetalles);
		$resultado = $this->servicioColocacion->getColocaciones($codemp,$numctaban,$dencol,$nomban);
		unset($this->servicioColocacion);
		return $resultado;
	}

	public function buscarDetalleColocaciones($codemp,$codban,$ctaban,$numcol)
	{
		$arrtabdetalles[0]='esp_scb_dt_colocacion';
		$this->servicioColocacion = new servicioColocacion('scb_colocacion',$arrtabdetalles);
		$resultado = $this->servicioColocacion->getDetalleColocacion($codemp,$codban,$ctaban,$numcol);
		unset($this->servicioColocacion);
		return $resultado;
	}

	public function verificarColocacion($codemp,$codban,$ctaban,$numcol)
	{
		$arrtabdetalles[0]='esp_scb_dt_colocacion';
		$this->servicioColocacion = new servicioColocacion('scb_colocacion',$arrtabdetalles);
		$resultado = $this->servicioColocacion->getDetalleColocacion($codemp,$codban,$ctaban,$numcol);
		unset($this->servicioColocacion);
		if($resultado->_numOfRows > 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public function elimarColocacion($arrjson,$codemp)
	{
		$arrtabdetalles[0]='esp_scb_dt_colocacion';
		$this->servicioColocacion = new servicioColocacion('scb_colocacion',$arrtabdetalles);
		$resultado = $this->servicioColocacion->borrarColocacion($arrjson, $codemp);
		unset($this->servicioColocacion);
		return $resultado;
	}

	public function buscarCodigoTipoFondo()
	{
		return $this->daogenerico->buscarCodigo('codtipfon',true,4);
	}

	function obtenerChequera()
	{
		$chequera = new servicioChequera();
		$chequera->codemp= $this->daogenerico->codemp;
		return $chequera->buscarChequeras();
	}

	function obtenerChequesChequera($codban,$ctaban,$numchequera)
	{
		$chequera = new servicioChequera();
		$chequera->codemp= $this->daogenerico->codemp;
		$chequera->codban = $codban;
		$chequera->ctaban = $ctaban;
		$chequera->numchequera = $numchequera;
		return $chequera->cargarCheques();
	}

	function obtenerUsuariosChequera($codban,$ctaban,$numchequera)
	{
		$chequera = new servicioChequera();
		$chequera->codemp= $this->daogenerico->codemp;
		$chequera->codban = $codban;
		$chequera->ctaban = $ctaban;
		$chequera->numchequera = $numchequera;
		return $chequera->cargarUsuariosCheques();
	}

	function verificarExistencia($codban,$ctaban,$numchequera)
	{
		$chequera = new servicioChequera();
		$chequera->codemp= $this->daogenerico->codemp;
		$chequera->codban = $codban;
		$chequera->ctaban = $ctaban;
		$chequera->numchequera = $numchequera;
		return $chequera->verificarExistenciaChequera();
	}

	function verificarExistenciaChequera($codban,$ctaban,$numchequera)
	{
		$chequera = new servicioChequera();
		$chequera->codemp= $this->daogenerico->codemp;
		$chequera->codban = $codban;
		$chequera->ctaban = $ctaban;
		$chequera->numchequera = $numchequera;
		return $chequera->verificarExistenciaChequera();
	}
	
	public function buscarCentroCosto($codemp,$codigo,$denominacion)
	{
		$filtro = '';
		$codigo = trim($codigo);
		if($codigo != "")
		{
			$filtro = " AND codcencos LIKE '%{$codigo}%'";
		}
		$denominacion = trim($denominacion);
		if($denominacion != "")
		{
			$filtro .= " AND denominacion LIKE '%{$denominacion}%'";
		}
		
		$cadenasql = "SELECT codcencos, denominacion ".
					 "  FROM sigesp_cencosto  ".
					 " WHERE codemp= '{$codemp}' {$filtro}  ".
					 " ORDER BY codcencos";
		return $this->daogenerico->buscarSql ( $cadenasql );
	}
	
	public function buscarCodigoCentroCosto()
	{
		return $this->daogenerico->buscarCodigo('codcencos',true,3);
	}

	public function validarCuentaMovimiento($codemp, $cuenta)
	{
		$noMovimiento = true;
		$cadenaSql =  "SELECT spg_cuenta ".
					  "  FROM spg_dt_cmp ".
					  " WHERE codemp = '{$codemp}' ".
					  "   AND spg_cuenta = '{$cuenta}'";
		$dataSet  = $this->daogenerico->buscarSql ( $cadenaSql );
		if ($dataSet->_numOfRows > 0)
		{
			$noMovimiento = false;
		}
		unset($dataSet);
		return $noMovimiento;
	}
	
	public function buscarPlanCuentaSpi($codemp)
	{
		$cadenasql ="SELECT spi_cuentas.spi_cuenta, spi_cuentas.denominacion, spi_cuentas.sc_cuenta, spi_cuentas.status, spi_cuentas.cueclaeco ".
            		"  FROM  spi_cuentas  ".
	    			" WHERE codemp = '{$codemp}' ".
					"   AND status = 'C' ".
	    			" ORDER BY spi_cuenta";
		return $this->buscarSql($cadenasql);	
	}
	
	public function buscarPlanCuentaSpiEstructura($codemp,$codest1,$codest2,$codest3,$codest4,$codest5,$estcla)
	{
		$cadenasql ="SELECT spi_cuentas.spi_cuenta, spi_cuentas.denominacion, spi_cuentas.sc_cuenta, spi_cuentas.status, spi_cuentas.cueclaeco ".
            		"  FROM spi_cuentas  ".
					" INNER JOIN spi_cuentas_estructuras ".
	    			"    ON spi_cuentas_estructuras.codemp = '{$codemp}' ".
					"   AND spi_cuentas_estructuras.codestpro1 ='".$codest1."'  ".
					"   AND spi_cuentas_estructuras.codestpro2 ='".$codest2."' ".
					"	AND spi_cuentas_estructuras.codestpro3 ='".$codest3."' ".
					"	AND spi_cuentas_estructuras.codestpro4 ='".$codest4."' ".
					"	AND spi_cuentas_estructuras.codestpro5 ='".$codest5."'  ".
					"	AND spi_cuentas_estructuras.estcla ='".$estcla."' ".
					"   AND spi_cuentas.status = 'C' ".
					"   AND spi_cuentas.codemp = spi_cuentas_estructuras.codemp ".
					"   AND spi_cuentas.spi_cuenta = spi_cuentas_estructuras.spi_cuenta ".
	    			" ORDER BY spi_cuenta";
		return $this->buscarSql($cadenasql);	
	}

	public function buscarCodigoFuentefinanciamiento()
	{
		return $this->daogenerico->buscarCodigo ('codfuefin',true, 2);

	}

	public function bucarNivelesPresupuesto($codemp)
	{
		$i = 0;
		$cadenasql = "SELECT nomestpro1,nomestpro2,nomestpro3, nomestpro4, nomestpro5 ".
					 "  FROM sigesp_empresa ".
					 " WHERE codemp= '".$codemp."'";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		foreach ( $resultado as $etiqueta => $valor )
		{
			$datosetiqueta [$i] = $valor;
			$i ++;
		}
		return $datosetiqueta;
	}
	
	public function bucarCantNivelPresu($codemp)
	{
		$cadenasql = "SELECT numniv ".
					 "  FROM sigesp_empresa ".
					 " WHERE codemp= '".$codemp."'";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		if ($resultado->fields ['numniv'] == '')
		{
			return '0';
		}
		else
		{
			return $resultado->fields ['numniv'];
		}
	}

	public function buscarEstructuraNiv1($codemp)
	{
		$cadenasql = "SELECT * ".
					 "  FROM spg_ep1  ".
					 " WHERE codemp= '".$codemp."' ".
					 "   AND codestpro1 <> '-------------------------' ".
					 "   AND estcla <> '-'";
		return $this->daogenerico->buscarSql ( $cadenasql );
	}

	public function setFuenteDefecto($dto, $estNivTres)
	{
		$this->pasarDatos ( $dto );
		$this->daogenerico->codfuefin = '--';
		if ($estNivTres)
		{
			$this->daogenerico->codestpro4= '0000000000000000000000000';
			$this->daogenerico->codestpro5= '0000000000000000000000000';
		}
		$this->daogenerico->modificar (true);
	}

	public function buscarCodUnidadAdm()
	{
		return $this->daogenerico->buscarCodigo ('coduac',true, 5);
	}

	public function buscarCodUnidadEjecutora()
	{
		return $this->daogenerico->buscarCodigo ('coduniadm',true, 10);
	}

	public function buscarCuentasSpg($codemp,$codigo,$denominacion,$filtro_cuenta="")
	{
		$filtroBase = '';
		$codigo = trim($codigo);
		if($codigo != "")
		{
			$filtroBase = " AND spg_cuenta LIKE '{$codigo}%'";
		}
		
		$denominacion = trim($denominacion);
		if($denominacion != "")
		{
			$filtroBase .= " AND denominacion LIKE '%{$denominacion}%'";
		}
		
		$filtro = '';
		$filtro_cuenta = trim($filtro_cuenta);
		if($filtro_cuenta != "")
		{
			$cuentas=explode(",",$filtro_cuenta);
			$total=count((array)$cuentas);
			for($i=0;$i<$total;$i++)
			{
				if($i==0)
				{
					$filtro .="   AND (spg_cuenta like '".$cuentas[$i]."%'";
				}
				else
				{
					$filtro .= "    OR spg_cuenta like '".$cuentas[$i]."%'";
				}
			
			}
			if ($total>0)
			{
				$filtro .= ")";
			}
		}
		$cadenasql = "SELECT DISTINCT spg_cuenta, denominacion ".
					 "  FROM spg_cuentas ".
					 " WHERE codemp= '{$codemp}' {$filtroBase} {$filtro} AND status='C' ORDER BY spg_cuenta";
		return $this->daogenerico->buscarSql ( $cadenasql );
	}

	public function buscarCuentas($codest1,$codest2,$codest3,$codest4,$codest5,$estcla)
	{
		$cadenasql= "SELECT spg_cuentas.spg_cuenta as sig_cuenta, spg_cuentas.denominacion, spg_cuentas.sc_cuenta, spg_cuentas.cueclaeco ".
					"  FROM spg_cuentas".
					" WHERE spg_cuentas.codemp ='".$this->daogenerico->codemp."'  ".
					"   AND spg_cuentas.codestpro1 ='".$codest1."'  ".
					"   AND spg_cuentas.codestpro2 ='".$codest2."' ".
					"	AND spg_cuentas.codestpro3 ='".$codest3."' ".
					"	AND spg_cuentas.codestpro4 ='".$codest4."' ".
					"	AND spg_cuentas.codestpro5 ='".$codest5."'  ".
					"	AND spg_cuentas.estcla ='".$estcla."' ".
					"	AND spg_cuentas.status ='C' ".   
					" ORDER BY  spg_cuentas.spg_cuenta ASC";
		return $this->daogenerico->buscarSql($cadenasql);
	}


	public function updateValicacionPresupuestaria($estvalspg,$ctaspgced,$ctaspgrec)
	{
		$valido = true;
		$cadenasql= "UPDATE sigesp_empresa ".
					"  SET  estvalspg = '".$estvalspg."', ".
					"       ctaspgced = '".$ctaspgced."', ".
					"  		ctaspgrec = '".$ctaspgrec."' ".
					" WHERE codemp ='".$this->daogenerico->codemp."' ";
		$result = $this->daogenerico->ejecutarSql($cadenasql);
		if ($result===false)
		{
			$valido = false;
		}
		return $valido;
	}
	
	public function bucarCodTipoModi()
	{
		return $this->daogenerico->buscarCodigo ('codtipmodpre',true,4);
	}	

	public function buscarCodigoContinente()
	{
		return $this->daogenerico->buscarCodigo ('codcont',false,3);
	}
	public function buscarSucursales($arrEmpresa)
	{
		
		switch($arrEmpresa["estmodest"])
		{
			case "1": // Modalidad por Proyecto
				$codest = "SUBSTR(SU.codestpro1,length(SU.codestpro1)-{$arrEmpresa['loncodestpro1']}) AS codestcomfs0, ".
						  "SUBSTR(SU.codestpro2,length(SU.codestpro2)-{$arrEmpresa['loncodestpro2']}) AS codestcomfs1, ".
						  "SUBSTR(SU.codestpro3,length(SU.codestpro3)-{$arrEmpresa['loncodestpro3']}) AS codestcomfs2, ".
						  "E1.denestpro1, E2.denestpro2, E3.denestpro3 ";
				
				$join   = 'INNER JOIN spg_ep1 E1 ON SU.codemp=E1.codemp AND SU.codestpro1=E1.codestpro1 AND SU.estcla=E1.estcla '.
						  'INNER JOIN spg_ep2 E2 ON SU.codemp=E2.codemp AND SU.codestpro1=E2.codestpro1 AND SU.codestpro2=E2.codestpro2 '.
						  'INNER JOIN spg_ep3 E3 ON SU.codemp=E3.codemp AND SU.codestpro1=E3.codestpro1 AND SU.codestpro2=E3.codestpro2 AND SU.codestpro3=E3.codestpro3 ';
				break;
				
			case "2": // Modalidad por Programatica
				$codest = "SUBSTR(codestpro1,length(codestpro1)-{$arrEmpresa["loncodestpro1"]}) AS codestcomfs0, ".
						  "SUBSTR(codestpro2,length(codestpro2)-{$arrEmpresa["loncodestpro2"]}) AS codestcomfs1, ".
						  "SUBSTR(codestpro3,length(codestpro3)-{$arrEmpresa["loncodestpro3"]}) AS codestcomfs2, ".
						  "SUBSTR(codestpro4,length(codestpro4)-{$arrEmpresa["loncodestpro4"]}) AS codestcomfs3, ".
						  "SUBSTR(codestpro5,length(codestpro5)-{$arrEmpresa["loncodestpro5"]}) AS codestcomfs4";
				break;
		}
		$cadenasql = "SELECT codsuc, nomsuc, {$codest}, SU.estcla ".
  					 "  FROM sigesp_sucursales SU ".
  					 "	{$join} ".
  					 " WHERE SU.codemp = '{$arrEmpresa['codemp']}' ";
		return $this->daogenerico->buscarSql($cadenasql);
	}

	public function buscarNivelesAprobacion($codemp)
	{
		$cadenasql ="SELECT codemp, codniv, monnivdes, monnivhas ".
            		"  FROM  sigesp_nivel  ".
	    			" WHERE codemp = '{$codemp}' ".
	    			" ORDER BY codniv";
		return $this->buscarSql($cadenasql);	
	}

	public function buscarCodigoNivel()
	{
		return $this->daogenerico->buscarCodigo ('codniv',true,4);
	}	

	public function VerificarNivelAprobacion($codemp,$codniv)
	{
		$cadenasql ="SELECT codniv ".
            		"  FROM  sigesp_nivel  ".
	    			" WHERE codemp = '{$codemp}' ".
	    			"   AND codniv = '{$codniv}' ".
	    			" ORDER BY codniv";
		$resultado = $this->daogenerico->ejecutarSql($cadenasql);
		if ($resultado->fields ['codniv'] == '')
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function buscarCodigoAsignacionNivel()
	{
		return $this->daogenerico->buscarCodigo ('codasiniv',true,4);
	}	

	public function buscarAsignacionNivelesAprobacion($codemp)
	{
		$cadenasql ="SELECT codemp,codasiniv,codniv,tipproc,despridoc ".
            		"  FROM sigesp_asig_nivel  ".
	    			" WHERE codemp = '{$codemp}' ".
	    			" ORDER BY codasiniv";
		return $this->buscarSql($cadenasql);	
	}

	public function setEstructura($indice,$codigo,$denominacion)
	{
		switch ($indice)
		{
			case 1:
				$this->daogenerico->codestpro1=$codigo;
				$this->daogenerico->denestpro1=$denominacion;
				break;
					
			case 2:
				$this->daogenerico->codestpro2=$codigo;
				$this->daogenerico->denestpro2=$denominacion;
				break;
					
			case 3:
				$this->daogenerico->codestpro3=$codigo;
				$this->daogenerico->denestpro3=$denominacion;
				break;
					
			case 4:
				$this->daogenerico->codestpro4=$codigo;
				$this->daogenerico->denestpro4=$denominacion;
				break;
					
			case 5:
				$this->daogenerico->codestpro5=$codigo;
				$this->daogenerico->denestpro5=$denominacion;
				break;
		}
	}

	public function obtenerPrimaryKey()
	{
		return $this->daogenerico->obtenerArregloPk();
	}

	public function obtenerSistemaProcecencia($procedencia)
	{
		$codsis= "";
		$cadenasql="SELECT codsis ".
				   " FROM sigesp_procedencias ".
				   " WHERE sigesp_procedencias.procede='".$procedencia."'";
			
		$resultado = $this->daogenerico->buscarSql($cadenasql);
			
		if ($resultado->fields ['codsis'] != "")
		{
			$codsis = $resultado->fields ['codsis'];
		}
		return $codsis;
	}

	public function obtenerEmpresa($codemp)
	{
		 $cadenasql = "SELECT * ".
					  "  FROM sigesp_empresa ".
					  " WHERE codemp =  '".$codemp."'";
		 $resultado = $this->daogenerico->buscarSql ( $cadenasql );	
		 return $resultado;
	}

	public function buscarCuentasContables($codemp,$codigo,$denominacion,$status)
	{
		 $cadenasql = "SELECT * ".
					  "  FROM scg_cuentas ".
					  " WHERE codemp =  '".$codemp."'".
					  "   AND sc_cuenta LIKE '".$codigo."%'".
					  "   AND denominacion LIKE  '%".$denominacion."%'".
					  "   AND status =  '".$status."'".
					  " ORDER BY sc_cuenta";
		 $resultado = $this->daogenerico->buscarSql ( $cadenasql );	
		 return $resultado;
	}
	
	public function verificarExistenciaRegistro($dto)
	{
		$existe = false;
		$this->pasarDatos ( $dto );
		$datos = $this->daogenerico->buscarPk();
		if($datos->_numOfRows>0)
		{
			$existe = true;
		}

		return $existe;
	}
	
	public function buscarDeducciones($codemp)
	{
		$cadenasql = "SELECT ".
					 "	  sigesp_deducciones.codemp,". 
					 "	  sigesp_deducciones.codded,". 
					 "	  sigesp_deducciones.dended,". 
					 "	  sigesp_deducciones.sc_cuenta,". 
					 "	  sigesp_deducciones.porded,". 
					 "	  sigesp_deducciones.monded,". 
					 "	  sigesp_deducciones.islr,". 
					 "	  sigesp_deducciones.iva,". 
					 "	  sigesp_deducciones.estretmun,". 
					 "	  sigesp_deducciones.formula,". 
					 "	  sigesp_deducciones.otras,". 
					 "	  sigesp_deducciones.tipopers,". 
					 "	  sigesp_deducciones.retaposol,". 
					 "	  sigesp_deducciones.codconret,". 
					 "	  sigesp_deducciones.estretmil,". 
					 "	  sigesp_conceptoretencion.desact,".
					 "	  scg_cuentas.denominacion".
					 "	FROM sigesp_deducciones".
					 "	  JOIN sigesp_empresa ON  sigesp_empresa.codemp = sigesp_empresa.codemp".
					 "	  JOIN scg_cuentas    ON  scg_cuentas.codemp  = sigesp_deducciones.codemp".
					 "	                      AND scg_cuentas.sc_cuenta = sigesp_deducciones.sc_cuenta".
					 "	  LEFT OUTER JOIN sigesp_conceptoretencion ON  sigesp_deducciones.codemp    = sigesp_conceptoretencion.codemp".
					 "	                                          AND sigesp_deducciones.codconret = sigesp_conceptoretencion.codconret".
					 "	WHERE ".
					 "	  sigesp_deducciones.codemp = '".$codemp."'".
					 "  ORDER BY sigesp_deducciones.codded ASC";

		return $this->daogenerico->buscarSql($cadenasql);
	}

	public function obtenerMenuUsuario($codemp, $codsis, $codusu)
	{
		$this->derechosdao = new DerechoUsuario();
		return $this->derechosdao->getMenuUsuario($codemp, $codsis, $codusu);
	}

	public function buscarClausulas($codemp)
	{
		$this->modclausuladao = new servicioModalidadClausula();
		$resultado = $this->modclausuladao->getClausulas($codemp);
		unset($this->modclausuladao);
		return $resultado;
	}

	public function buscarSpiCuentas($codemp,$codigo,$denominacion,$status)
	{
		$cadenasql =  "SELECT *  ".
			          "  FROM spi_cuentas            ".
			          " WHERE codemp='".$codemp."'  ";
					  "   AND spi_cuenta LIKE '".$codigo."%'".
					  "   AND denominacion LIKE  '%".$denominacion."%'".
					  "   AND status =  '".$status."'".
					  " ORDER BY spi_cuenta";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function buscarCuentasBanco($codemp,$codban)
	{
		if ((strtoupper($_SESSION["ls_gestor"]) == "MYSQLT")|| (strtoupper($_SESSION["ls_gestor"]) == "MYSQLI"))
		{
			$cadenaSeguridad = " AND e.codintper=CONCAT(scb_ctabanco.codban,'-',scb_ctabanco.ctaban) ";
		}
		else
		{
			$cadenaSeguridad = " AND e.codintper=a.codban||'-'||a.ctaban ";
		}
		$cadenasql	=	"SELECT a.ctaban as ctaban,a.dencta as dencta,c.nomban as nomban,b.nomtipcta as nomtipcta,a.sc_cuenta as sc_cuenta ".
						"FROM scb_ctabanco a,scb_tipocuenta b,scb_banco c,scg_cuentas d,sss_permisos_internos e ".
						"WHERE a.codemp='".$codemp."' AND a.codtipcta=b.codtipcta AND a.codban=c.codban ". 
						"AND a.codban like '%".$codban."%' ".
						"AND (a.sc_cuenta=d.sc_cuenta AND a.codemp=d.codemp) {$cadenaSeguridad}";
		return $this->daogenerico->buscarSql($cadenasql);
	}

	public function verificarExistenciaAperturaSPG($codemp)
	{
		$cadenasql =  " SELECT count(*) as aperturaspg  ".
			          " FROM   spg_dt_cmp            ".
			          " WHERE  spg_dt_cmp.codemp='".$codemp."'  ".
					  " AND spg_dt_cmp.operacion='AAP'";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function verificarExistenciaAperturaSPI($codemp)
	{
		$cadenasql =  " SELECT count(*) as aperturaspi  ".
			          " FROM   spi_dt_cmp            ".
			          " WHERE  spi_dt_cmp.codemp='".$codemp."'  ".
					  " AND spi_dt_cmp.operacion='AAP'";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function validarExistenciaIvaConfigurado($codemp)
	{
		$cadenasql =  " SELECT count(*) as totalcargos  ".
			          " FROM   sigesp_cargos            ".
			          " WHERE  codemp='".$codemp."'  ";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function validarExistenciaCuentasIngreso($codemp)
	{
		$cadenasql =  " SELECT count(*) as totalcuentasingreso  ".
			          " FROM   spi_cuentas            ".
			          " WHERE  codemp='".$codemp."'  ";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function validarExistenciaCuentasGasto($codemp)
	{
		$cadenasql =  " SELECT count(*) as totalcuentasgasto  ".
			          " FROM   spg_cuentas            ".
			          " WHERE  codemp='".$codemp."'  ";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function validarExistenciaCuentasContables($codemp)
	{
		$cadenasql =  " SELECT count(*) as totalcuentascontables  ".
			          " FROM   scg_cuentas            ".
			          " WHERE  codemp='".$codemp."'  ";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function validarExistenciaEstructuras($codemp)
	{
		$cadenasql =  " SELECT count(*) as totalestructura  ".
			          " FROM   spg_ep1            ".
			          " WHERE  codemp='".$codemp."' AND
			                   codestpro1 <> '-------------------------' AND 
			                   estcla <>'-'";
		$resultado = $this->daogenerico->buscarSql ($cadenasql);

		return $resultado;
	}

	public function verificarUltimo($campo,$table,$where,$valor)
	{
		$existe=false;
		$cadenasql = "SELECT ".$campo." as campo ".
					 "  FROM ".$table." ".
					 $where.
					 " ORDER BY ".$campo." DESC LIMIT 1";
		$resultado = $this->daogenerico->buscarSql ( $cadenasql );
		if (!$resultado->EOF)
		{
			$ultimo=$resultado->fields ['campo'];
			if($valor==$ultimo)
			{
				$existe=true;
			}
		}
		return $existe;
	}
        
        
	public function eliminarIntegacionTodos()
	{
		$valido = true;
		$cadenasql= "DELETE FROM scg_casa_presu ".
			    " WHERE codemp ='".$this->daogenerico->codemp."' ";
		$result = $this->daogenerico->ejecutarSql($cadenasql);
		if ($result===false)
		{
			$valido = false;
		}
		return $valido;
	}
        
}
?>