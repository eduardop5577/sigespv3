<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrvscb = "";
$dirsrvscb = dirname(__FILE__);
$dirsrvscb = str_replace("\\","/",$dirsrvscb);
$dirsrvscb = str_replace("/modelo/servicio/scb","",$dirsrvscb); 
require_once ($dirsrvscb."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvscb.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrvscb."/modelo/servicio/scb/sigesp_srv_scb_iemision_chq.php");
require_once ($dirsrvscb."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrvscb.'/modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php');

class servicioBanco implements iemision_chq
{
	private $daoBanco;
	private $daoEmisionChq;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
	{
		$this->daoBanco = null;
		$this->daoEmisionChq = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}
	
	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarConceptosScb($codope) {
		
		$cadenasql= " SELECT codconmov,denconmov,codope ".
			     	" FROM scb_concepto ".
				 	" WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
					" AND codope = '".$codope."' OR codope='--' ORDER BY codconmov";		   

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarContableCta($codban,$ctaban)
	{
		$cadenasql = "	SELECT 	scb_ctabanco.codban as codban, TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta ".
				   	 " 	FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, sss_permisos_internos ".
				     " 	WHERE scb_ctabanco.codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				     " 	AND scb_ctabanco.codban= '".$codban."' ".  
				     " 	AND scb_ctabanco.ctaban= '".$ctaban."' ".
				     " 	AND scb_ctabanco.estact='1' ".
				     " 	AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
				     " 	AND scb_ctabanco.codban=scb_banco.codban ".
				     " 	AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
				     " 	AND scb_ctabanco.codemp=scg_cuentas.codemp ".
				     " 	ORDER BY codban ASC ";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ls_ctacontable  = $resultado->fields["sc_cuenta"];
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $ls_ctacontable;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarReporteCfg($sistema,$seccion,$variable,$valor,$tipo,$arrevento)
	{
		$lb_valido=false;
		$ls_valor="";
		$cadenasql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				"   AND codsis='".$sistema."' ".
				"   AND seccion='".$seccion."' ".
				"   AND entry='".$variable."' ";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			$li_i=0;
			while((!$resultado->EOF))
			{
				$ls_valor=$resultado->fields["value"];
				$li_i=$li_i+1;
				$resultado->MoveNext();
			}
			if($li_i==0)
			{
				$resultado = 0;
				$codemp   = $_SESSION["la_empresa"]["codemp"];
				//Creación del Dao con la tabla y campos a insertar.
				$this->daoConfig=FabricaDao::CrearDAO("N","sigesp_config");
				$this->daoConfig->codemp       = $codemp;
				$this->daoConfig->codsis       = $sistema; 
				$this->daoConfig->seccion      = $seccion; 
				$this->daoConfig->entry        = $variable; 
				//Iniciar transacción
				DaoGenerico::iniciarTrans();
				//Eliminamos el config
				$resultado = $this->daoConfig->eliminar();
				
				$servicioEvento = new ServicioEvento();
				$servicioEvento->evento=$arrevento['evento'];
				$servicioEvento->codemp=$arrevento['codemp'];
				$servicioEvento->codsis=$arrevento['codsis'];
				$servicioEvento->nomfisico=$arrevento['nomfisico'];
				$servicioEvento->desevetra=$arrevento['desevetra'];
				
				if ($resultado)
				{
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
					switch ($tipo)
					{
						case "C"://Caracter
							$value = $valor;
							break;
		
						case "D"://Double
							$value=str_replace(".","",$valor);
							$value=str_replace(",",".",$valor);
							$value = $valor;
							break;
		
						case "B"://Boolean
							$value = $valor;
							break;
		
						case "I"://Integer
							$value = intval($valor);
							break;
					}
					$this->daoConfig=FabricaDao::CrearDAO("N","sigesp_config");
					$this->daoConfig->codemp     = $codemp;
					$this->daoConfig->codsis     = $sistema; 
					$this->daoConfig->seccion    = $seccion; 
					$this->daoConfig->entry      = $variable; 
					$this->daoConfig->value      = $value; 
					$this->daoConfig->type       = $tipo; 
					$resultado = $this->daoConfig->incluir();
					if($resultado)
					{
						$lb_valido=true;	
					}
					else
					{
						$lb_valido=false;
					}
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoConfig->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
				}
				
				if (DaoGenerico::completarTrans($lb_valido)) 
				{
					$resultado = 1;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento(); 		
				}
				else
				{
					$arrevento ['desevetra'] = $this->daoConfig->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
				}  
				if ($lb_valido)
				{
					$ls_valor=$this->buscarReporteCfg($sistema, $seccion, $variable, $valor, $tipo);
				}
			}
		}
		unset($conexionbd);
		unset($resultado);
		return rtrim($ls_valor);
	}

	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarVoucherNuevo($codemp) {
		$this->daoEmisionChq = FabricaDao::CrearDAO("N", "scb_movbco");
		$this->daoEmisionChq->codemp = $codemp;
		$codigo = $this->daoEmisionChq->buscarCodigo("chevau",true,25);
		unset($this->daoEmisionChq);
		return $codigo;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarVoucheExistente($chevau)
	{
		$chevau=str_pad($chevau,25,"0",LEFT); 
		$cadenasql=" SELECT chevau ".
				" FROM scb_movbco ".
				" WHERE chevau='".$chevau."' ";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		$voucher=$resultado->fields['chevau'];
		unset($conexionbd);
		unset($resultado);
		return trim($voucher);
	}

	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarDocumentoExistente($numdoc,$codban,$ctaban,$operacion)
	{
		$cadenasql=" SELECT numdoc ".
				 " FROM   scb_movbco ".
				 " WHERE  codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				 " AND codban ='".$codban."' ".
				 " AND ctaban='".$ctaban."' ".
				 " AND numdoc='".$numdoc."' ".
				 " AND codope ='".$operacion."' ";
				 
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		$numdoc=$resultado->fields['numdoc'];
		unset($conexionbd);
		unset($resultado);
		return trim($numdoc);
	}
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarSolicitudesProgProv($numsol,$fecdes,$fechas)
	{
	  $arrSolicitudesProg=array();
		 
		 $cadenasql = " SELECT DISTINCT cxp_solicitudes.cod_pro, rpc_proveedor.nompro, scb_prog_pago.codban, ".
					  " scb_prog_pago.ctaban, scb_banco.nomban, scb_ctabanco.dencta, TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta ".
					  " FROM cxp_solicitudes, rpc_proveedor, scb_prog_pago, scb_banco, scb_ctabanco ".
					  " WHERE scb_prog_pago.codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
					  " AND scb_prog_pago.numsol like '%".$numsol."%' ".
					  " AND cxp_solicitudes.estprosol='S' ".
					  " AND scb_prog_pago.estmov='P' ".
					  " AND cxp_solicitudes.tipproben='P' ".
					  " AND scb_prog_pago.fecpropag BETWEEN '".$fecdes."' AND '".$fechas."' ".
					  " AND scb_prog_pago.codemp=scb_banco.codemp ".
					  " AND scb_prog_pago.codban=scb_banco.codban ". 
					  " AND scb_prog_pago.ctaban=scb_ctabanco.ctaban ".
					  " AND scb_prog_pago.codban=scb_ctabanco.codban ".
					  " AND cxp_solicitudes.codemp=rpc_proveedor.codemp ".
					  " AND cxp_solicitudes.cod_pro=rpc_proveedor.cod_pro ".
					  " AND cxp_solicitudes.numsol=scb_prog_pago.numsol ".
					  " ORDER BY cxp_solicitudes.cod_pro, rpc_proveedor.nompro, scb_prog_pago.codban, ".
					  " scb_prog_pago.ctaban, scb_banco.nomban, scb_ctabanco.dencta, sc_cuenta ASC";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			$j=0;
			while((!$resultado->EOF))
			{
  			   $ls_codpro = trim($resultado->fields["cod_pro"]);
			   $ls_nompro = $resultado->fields["nompro"];
			   $ls_codban = trim($resultado->fields["codban"]);
			   $ls_nomban = $resultado->fields["nomban"];
			   $ls_banco  = $ls_codban." - ".$ls_nomban;
			   $ls_ctaban = trim($resultado->fields["ctaban"]);
			   $ls_nomcta = $resultado->fields["dencta"];
			   $ls_cuenta_banco= $ls_ctaban." - ".$ls_nomcta;
			   $ls_scgcta = $resultado->fields["sc_cuenta"];

			   $arrSolicitudesProg[$j]['codigo']  = $ls_codpro;
			   $arrSolicitudesProg[$j]['nombrep']  = $ls_nompro;
			   $arrSolicitudesProg[$j]['banco']   = $ls_banco;
			   $arrSolicitudesProg[$j]['cuenta']  = $ls_cuenta_banco;
			   $arrSolicitudesProg[$j]['codban']  = $ls_codban;
			   $arrSolicitudesProg[$j]['ctaban']  = $ls_ctaban;
			   $arrSolicitudesProg[$j]['nombreban']  = $ls_nomban;
			   $arrSolicitudesProg[$j]['nombrecta']  = $ls_nomcta;
			   $arrSolicitudesProg[$j]['scg_cta']  = $ls_scgcta;
			   
			   
			   $j++;
			   $resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $arrSolicitudesProg;
	}

	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarSolicitudesProgBen($numsol,$fecdes,$fechas)
	{
	  $arrSolicitudesProg=array();
		 
		 $cadenasql = " SELECT DISTINCT trim(cxp_solicitudes.ced_bene) as ced_bene,rpc_beneficiario.nombene, rpc_beneficiario.apebene, ".
	                  " scb_prog_pago.codban,scb_prog_pago.ctaban, scb_banco.nomban, scb_ctabanco.dencta, ".
					  " TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta ".
	 	          	  " FROM cxp_solicitudes, rpc_beneficiario, scb_prog_pago, scb_banco, scb_ctabanco ".
				 	  " WHERE scb_prog_pago.codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
			          " AND scb_prog_pago.numsol like '%".$numsol."%' ".
				   	  " AND cxp_solicitudes.estprosol='S' ".
			          " AND scb_prog_pago.estmov='P' ".
			          " AND cxp_solicitudes.tipproben='B' ".
				      " AND scb_prog_pago.fecpropag BETWEEN '".$fecdes."' AND '".$fechas."' ".
				      " AND scb_prog_pago.codemp=scb_banco.codemp ".
				      " AND scb_prog_pago.codban=scb_banco.codban ".
				      " AND scb_prog_pago.ctaban=scb_ctabanco.ctaban ".
				      " AND scb_prog_pago.codban=scb_ctabanco.codban ".
				      " AND cxp_solicitudes.codemp=rpc_beneficiario.codemp ".
				      " AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene ".
				      " AND cxp_solicitudes.numsol=scb_prog_pago.numsol ".
				      " ORDER BY rpc_beneficiario.nombene, rpc_beneficiario.apebene, scb_prog_pago.codban, ".
                      " scb_prog_pago.ctaban, scb_banco.nomban, scb_ctabanco.dencta, sc_cuenta ASC";
		
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			$j=0;
			while((!$resultado->EOF))
			{
  			   $ls_cedbene = trim($resultado->fields["ced_bene"]);
			   $ls_nombene = $resultado->fields["nombene"];
			   $ls_apebene = $resultado->fields["apebene"];
			   $ls_beneficiario= $ls_nombene." , ".$ls_apebene; 
			   $ls_codban = trim($resultado->fields["codban"]);
			   $ls_nomban = $resultado->fields["nomban"];
			   $ls_banco  = $ls_codban." - ".$ls_nomban;
			   $ls_ctaban = trim($resultado->fields["ctaban"]);
			   $ls_nomcta = $resultado->fields["dencta"];
			   $ls_cuenta_banco= $ls_ctaban." - ".$ls_nomcta;
			   $ls_scgcta = $resultado->fields["sc_cuenta"];

			   $arrSolicitudesProg[$j]['codigo']  = $ls_cedbene;
			   $arrSolicitudesProg[$j]['nombreb']  = $ls_beneficiario;
			   $arrSolicitudesProg[$j]['banco']   = $ls_banco;
			   $arrSolicitudesProg[$j]['cuenta']  = $ls_cuenta_banco;
			   $arrSolicitudesProg[$j]['codban']  = $ls_codban;
			   $arrSolicitudesProg[$j]['ctaban']  = $ls_ctaban;
			   $arrSolicitudesProg[$j]['nombreban']  = $ls_nomban;
			   $arrSolicitudesProg[$j]['nombrecta']  = $ls_nomcta;
			   $arrSolicitudesProg[$j]['scg_cta']  = $ls_scgcta;
			   $j++;
			   $resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $arrSolicitudesProg;
	}

	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */									
	public function buscarCtasBancariasPagmin($codban,$ctaban,$denctaban) 
	{
	$ls_gestor = $_SESSION["ls_gestor"];
	 if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
	 {
		  $ls_sql_seguridad = " AND trim(sss_permisos_internos.codintper)=trim(CONCAT(scb_ctabanco.codban,'-',scb_ctabanco.ctaban)) ";
	 }
	 else
	 {
		  $ls_sql_seguridad = " AND  trim(sss_permisos_internos.codintper)=trim(scb_ctabanco.codban||'-'||scb_ctabanco.ctaban) ";
	 }
	$cadenasql = "	SELECT 	scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta, ".
				 " 			scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban, ".
				 " 			scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr, ".
				 " 			scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact ".
				 " 	FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, sss_permisos_internos ".
				 " 	WHERE scb_ctabanco.codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
				 " 	AND scb_ctabanco.codban like '%".$codban."%' ".  
				 " 	AND scb_ctabanco.ctaban like '".$ctaban."%' ".
				 " 	AND scb_ctabanco.estact='1' ".
				 " 	AND UPPER(scb_ctabanco.dencta) like '%".strtoupper($denctaban)."%' ".
				 " 	AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
				 " 	AND scb_ctabanco.codban=scb_banco.codban ".
				 " 	AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta ".
				 " 	AND scb_ctabanco.codemp=scg_cuentas.codemp ".
				 " 	".$ls_sql_seguridad." ".
				 " 	ORDER BY codban ASC ";
//	echo $cadenasql;
//	break;		   

	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$resultado = $conexionbd->Execute ( $cadenasql );
	unset($conexionbd);
	return $resultado;
	}
	
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */									
	public function buscarMovNumpagmin($numpagmin,$opepagmin,$fecpagmin,$banpagmin,$ctapagmin)
	{
	  $arrMovNumpagmin=array();
		if (!empty($fecpagmin))
		{
			$ls_sqlaux = " AND scb_movbco.fecmov = '".$fecpagmin."'";
		}
		if ($opepagmin!='-')
		{
		  	$ls_sqlaux = $ls_sqlaux." AND scb_movbco.codope='".$opepagmin."'";
		}
		else
	    {
		  	$ls_sqlaux = $ls_sqlaux." AND (scb_movbco.codope = 'DP' OR scb_movbco.codope = 'NC')";
	    }
		$cadenasql=" SELECT scb_movbco.numordpagmin, scb_movbco.codban, scb_movbco.ctaban, scb_banco.nomban, scb_ctabanco.dencta, ".
		           " scb_tipofondo.porrepfon, scb_movbco.fecmov, scb_tipocuenta.codtipcta, scb_tipocuenta.nomtipcta, ".
				   " trim(scb_ctabanco.sc_cuenta) as sc_cuenta, scb_movbco.monto, scb_movbco.codtipfon, scb_tipofondo.dentipfon ".
				   " FROM scb_movbco, scb_banco, scb_ctabanco, scb_tipocuenta, scb_tipofondo ".
				   " WHERE scb_movbco.codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
				   " AND trim(scb_movbco.numordpagmin) <>'' ".
				   " AND trim(scb_movbco.numordpagmin) <>'-'	$ls_sqlaux	".			
				   " AND scb_movbco.codtipfon<>'----' ".
				   " AND scb_movbco.codban like '%".$banpagmin."%' ".
				   " AND scb_movbco.ctaban like '%".$ctapagmin."%' ".
				   " AND scb_movbco.numordpagmin like '%".$numpagmin."%' ".
				   " AND scb_movbco.codemp = scb_banco.codemp ".
				   " AND scb_movbco.codban = scb_banco.codban ".					
				   " AND scb_movbco.codemp = scb_ctabanco.codemp ".
				   " AND scb_movbco.codban = scb_ctabanco.codban ".
				   " AND scb_movbco.ctaban = scb_ctabanco.ctaban ".
				   " AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
				   " AND scb_movbco.codemp=scb_tipofondo.codemp ".
				   " AND scb_movbco.codtipfon=scb_tipofondo.codtipfon ".
				   " ORDER BY scb_movbco.numordpagmin, scb_movbco.fecmov ASC ";//echo $cadenasql.'<br><br>';
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			$j=0;
			while((!$resultado->EOF))
			{
				$ls_codban 		  = trim($resultado->fields["codban"]);
				$ls_ctaban 		  = trim($resultado->fields["ctaban"]);
				$ld_mondiscta     = $this->buscarSaldoCtabanSinFormat($ls_codban,$ls_ctaban);
				$ld_mondiscta     = number_format($ld_mondiscta,2,',','.');
				$ls_scgcta 		  = $resultado->fields["sc_cuenta"];
				$ls_nomban   	  = $resultado->fields["nomban"];
				$ls_fecmov   	  = $resultado->fields["fecmov"];
				$ls_denctaban 	  = $resultado->fields["dencta"];
				$ls_codtipcta 	  = $resultado->fields["codtipcta"];
				$ls_dentipcta	  = $resultado->fields["nomtipcta"];					 
				$ls_numordpagmin  = $resultado->fields["numordpagmin"];
				$ld_monordpagmin  = $resultado->fields["monto"];//Monto Total de la Orden de Pago Ministerio.
				$ls_codtipfon     = $resultado->fields["codtipfon"];
				$ls_dentipfon     = $resultado->fields["dentipfon"];
				$ld_porrepfon     = $resultado->fields["porrepfon"];//Porcentaje de Reposición.
				$ld_totmoncon     = $this->uf_load_monto_consumido($ls_numordpagmin,$ls_codtipfon);//Monto Consumido del Monto Original.
				$ld_monmaxmov     = (($ld_monordpagmin*($ld_porrepfon/100))-$ld_totmoncon);
				$ld_monmaxmov     = number_format($ld_monmaxmov,2,'.','');
				$ld_totporcon     = (($ld_totmoncon*100)/$ld_monordpagmin);//Porcentaje Consumido.
				
				/*if ($as_origen=='EC' || $as_origen=='CO' || $ld_totporcon<$ld_porrepfon)//Emisión de Cheques ó Carta Orden.
				{
					if ($ld_monmaxmov>0)
					{
						echo "<tr class=celdas-azules>";						   
					}
					else
					{
						echo "<tr class=celdas-blancas>"; 
					}
				}*/
			   $arrMovNumpagmin[$j]['numordpagmin']  = $ls_numordpagmin;
			   $arrMovNumpagmin[$j]['codnomban']  	 = $ls_codban.' - '.$ls_ctaban;
			   $arrMovNumpagmin[$j]['ctanomban']     = $ls_ctaban.' - '.$ls_denctaban;
			   $arrMovNumpagmin[$j]['monto']  		 = number_format($ld_monordpagmin,2,',','.');
			   $arrMovNumpagmin[$j]['porrep']  		 = number_format($ld_porrepfon,2,',','.');
			   $arrMovNumpagmin[$j]['porcon']  		 = number_format($ld_totporcon,2,',','.');
			   $arrMovNumpagmin[$j]['disp']  		 = number_format($ld_monmaxmov,2,',','.');
			   $j++;
			   $resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $arrMovNumpagmin;
	}
	
	
	/* (non-PHPdoc)
     * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
     */
	public function buscarSaldoCtabanSinFormat($codban,$ctaban)
	{
		$ldec_monto_debe=$ldec_monto_haber=$ldec_saldo=$ld_totmonhab=$ld_totmondeb=0;
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];

		$cadenasql = "SELECT SUM(monto - monret) As monhab, 0 As mondeb 
				     FROM scb_movbco 
					WHERE codemp='".$ls_codemp."' 
					  AND codban='".$codban."' 
					  AND trim(ctaban)='".trim($ctaban)."' 
					  AND (codope='RE' OR codope='ND' OR codope='CH') 
					  AND estmov<>'A' 
					  AND estmov<>'O'
			        GROUP BY codemp, codban, ctaban
					UNION
				   SELECT 0 As monhab, SUM(monto - monret) As mondeb 
					 FROM scb_movbco 
					WHERE codemp='".$ls_codemp."' 
					  AND codban='".$codban."' 
					  AND trim(ctaban)='".trim($ctaban)."' 
					  AND (codope='NC' OR codope='DP') 
					  AND estmov<>'A' 
					  AND estmov<>'O'
					GROUP BY codemp, codban, ctaban";
		
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ldec_monto_debe  = $resultado->fields["mondeb"];
				$ldec_monto_haber = $resultado->fields["monhab"];
				$ld_totmondeb += $ldec_monto_debe;
                $ld_totmonhab += $ldec_monto_haber;
				$resultado->MoveNext();
			}
			 $ldec_saldo = $ld_totmondeb-$ld_totmonhab;
		}
		unset($conexionbd);
		unset($resultado);
		return $ldec_saldo;
	}
	
	/* (non-PHPdoc)
     * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
     */
	public function uf_load_monto_consumido($as_numordpagmin,$as_codtipfon)
   	{
		$ld_totmoncon = 0;//Sumatoria de los Consumos de Movimientos asociados a la Orden de Pago Ministerio.
		$ld_moncon = 0;

		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_aux_where=" AND CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene) ".
							   "	 NOT IN (SELECT CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene)".
							   "			   FROM cxp_rd,cxp_dt_solicitudes,cxp_sol_banco".
							   "			  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp".
							   "				AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
							   "				AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
							   "				AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
							   "				AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
							   "				AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
							   "				AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol) ";
				break;
			case "MYSQLI":
				$ls_aux_where=" AND CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene) ".
							   "	 NOT IN (SELECT CONCAT(cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene)".
							   "			   FROM cxp_rd,cxp_dt_solicitudes,cxp_sol_banco".
							   "			  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp".
							   "				AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
							   "				AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
							   "				AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
							   "				AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
							   "				AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
							   "				AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol) ";
				break;
			case "POSTGRES":
				$ls_aux_where =" AND cxp_rd.codemp||cxp_rd.numrecdoc||cxp_rd.codtipdoc||cxp_rd.cod_pro||cxp_rd.ced_bene".
							   "	 NOT IN (SELECT (cxp_rd.codemp||cxp_rd.numrecdoc||cxp_rd.codtipdoc||cxp_rd.cod_pro||cxp_rd.ced_bene)".
							   "			   FROM cxp_rd,cxp_dt_solicitudes,cxp_sol_banco".
							   "			  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp".
							   "				AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
							   "				AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
							   "				AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
							   "				AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
							   "				AND cxp_dt_solicitudes.codemp=cxp_sol_banco.codemp".
							   "				AND cxp_dt_solicitudes.numsol=cxp_sol_banco.numsol) ";
				break;
		}

		$cadenasql = "SELECT SUM(monto) as moncon
					 FROM scb_movbco 
					WHERE numordpagmin<>'-' 
					  AND numordpagmin<>''
					  AND numordpagmin = '".$as_numordpagmin."'
					  AND codtipfon = '".$as_codtipfon."'
					  AND (codope='CH' OR codope='ND')
					GROUP BY numordpagmin,codtipfon
					UNION
				   SELECT SUM(montotdoc) as moncon
				     FROM cxp_rd
					WHERE numordpagmin = '".$as_numordpagmin."'
					  AND codtipfon = '".$as_codtipfon."' $ls_aux_where
					GROUP BY numordpagmin";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ld_moncon = $rs_data->fields["moncon"];
				$ld_totmoncon += $ld_moncon;
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $ld_totmoncon;
	}// end function uf_load_monto_consumido

/* (non-PHPdoc)
     * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
     */
	public function uf_select_solcxp_montocancelado($ls_codemp,$ls_numsol,$ls_codban,$ls_ctaban)
	{
		$ls_codemp = $_SESSION["la_empresa"]["codemp"];
		$ldec_montocancelado=0;
		$cadenasql = "SELECT sum(monto) as monto
					 FROM cxp_sol_banco 
					WHERE codemp='".$ls_codemp."'
					  AND numsol='".$ls_numsol."'
					  AND estmov<>'A' 
					  AND estmov<>'O'";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ldec_montocancelado = $resultado->fields["monto"];
				$resultado->MoveNext();
			}
		}
		
		unset($conexionbd);
		unset($resultado);
		return $ldec_montocancelado;
			
	}//Fin de uf_select_solcxp_montocancelado


/* (non-PHPdoc)
     * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
     */
	public function  buscarSolicitudesProgCheques($codban,$ctaban,$codigopb,$numpagmin,$codtipfon,$tipproben,$fechadhoy)
	{
	    $arrSolProgCheques=array();
		$li_estciespg = $_SESSION["la_empresa"]["estciespg"];
	    $li_estciespi = $_SESSION["la_empresa"]["estciespi"];
	    $li_estciescg = $_SESSION["la_empresa"]["estciescg"];

	    $ls_codemp = $_SESSION["la_empresa"]["codemp"];
	    $fechadhoy  = date("Y-m-d");
	    if ($tipproben=='P')
	       {
		     $ls_tabla  = ', rpc_proveedor';
		     $ls_campo  = 'cod_pro';
		     $ls_campos = ',cxp_solicitudes.cod_pro as cod_pro, rpc_proveedor.nompro';
		     $ls_sqlaux = " AND cxp_solicitudes.tipproben='P' AND cxp_solicitudes.cod_pro=rpc_proveedor.cod_pro";
		   }
	    elseif($tipproben=='B')
	       {
		     $ls_tabla  = ', rpc_beneficiario';
		     $ls_campo  = 'ced_bene';
		     $ls_campos = ',cxp_solicitudes.ced_bene,rpc_beneficiario.nombene,rpc_beneficiario.apebene';
		     $ls_sqlaux = " AND cxp_solicitudes.tipproben='B' AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene";
		   }
	    if (!empty($numpagmin) && !empty($codtipfon) && $numpagmin!='-' && $codtipfon!='----')
		   {
		     $ls_sqlaux = $ls_sqlaux." AND trim(cxp_solicitudes.numordpagmin) = '".$numpagmin."' 
			                		   AND cxp_solicitudes.codtipfon = '".$codtipfon."'";
		   }
		else
		   {
		   /*  $ls_sqlaux = $ls_sqlaux." AND trim(cxp_solicitudes.numordpagmin) = '-' 
			                		   AND cxp_solicitudes.codtipfon = '----'";*/
		   }
		$cadenasql = "SELECT cxp_solicitudes.numsol as numsol,
		   		 	      cxp_solicitudes.consol as consol,
						  cxp_solicitudes.monsol as monsol,
						  scb_prog_pago.codban as codban,
						  scb_prog_pago.ctaban as ctaban,
						  cxp_solicitudes.nombenaltcre,scb_prog_pago.fecpropag,
						  cxp_solicitudes.codfuefin $ls_campos,
					      (SELECT count(cxp_rd_spg.spg_cuenta) 
						     FROM cxp_rd_spg, cxp_rd, cxp_dt_solicitudes
						    WHERE cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
						      AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
						      AND cxp_dt_solicitudes.codemp=cxp_rd_spg.codemp
						      AND cxp_dt_solicitudes.numrecdoc=cxp_rd_spg.numrecdoc
						      AND cxp_dt_solicitudes.codtipdoc=cxp_rd_spg.codtipdoc
						      AND cxp_dt_solicitudes.cod_pro=cxp_rd_spg.cod_pro
						      AND cxp_dt_solicitudes.ced_bene=cxp_rd_spg.ced_bene
						      AND cxp_rd.codemp=cxp_rd_spg.codemp
						      AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc
						      AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc
						      AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro
						      AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) as detspg
	 			     FROM cxp_solicitudes, scb_prog_pago $ls_tabla
				    WHERE cxp_solicitudes.codemp='".$ls_codemp."' 
					  AND trim(cxp_solicitudes.$ls_campo)='".trim($codigopb)."' 
					  AND cxp_solicitudes.estprosol='S' 
					  AND scb_prog_pago.estmov='P' 
					  AND scb_prog_pago.codban='".$codban."' 
					  AND scb_prog_pago.ctaban='".$ctaban."'
					  AND scb_prog_pago.fecpropag<='".$fechadhoy."' $ls_sqlaux   
				      AND cxp_solicitudes.numsol=scb_prog_pago.numsol 
					  AND cxp_solicitudes.codemp=scb_prog_pago.codemp 
					ORDER BY cxp_solicitudes.numsol ASC";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
		    $j=0;
			while((!$resultado->EOF))
			{
			 $li_detspg = $resultado->fields["detspg"];
			 if (($li_estciespg==1 || $li_estciespi==1) && ($li_detspg==0 && $li_estciescg==0) || 
				($li_estciespg==0 && $li_estciespi==0 && $li_estciescg==0))
				{
					if ($tipproben=='P')
					{
						$ls_codprovben = trim($resultado->fields["cod_pro"]);
						$ls_nomproben  = $resultado->fields["nompro"];
					}
					else
					{ 
						$ls_codprovben = trim($resultado->fields["ced_bene"]);
						$ls_nomproben  = $resultado->fields["nombene"].', '.$resultado->fields["apebene"];
					}
					$ls_numsol    = trim($resultado->fields["numsol"]);
					$ls_consol	= $resultado->fields["consol"];
					$ldec_monsol  = $resultado->fields["monsol"];
					$ls_codban	= $resultado->fields["codban"];
					$ls_ctaban    = $resultado->fields["ctaban"];
					$ls_codfuefin = $resultado->fields["codfuefin"];
					$ls_nombenealt= $resultado->fields["nombenaltcre"];
					$ld_fecpropag = $resultado->fields["fecpropag"];
					$ldec_montocancelado = $this->uf_select_solcxp_montocancelado($ls_codemp,$ls_numsol,$ls_codban,$ls_ctaban);
					$ai_montonotas=0;
					$lb_valido=$this->uf_load_notas_asociadas($ls_codemp,$ls_numsol);// Ojo quitar este parametro de referencia!!!!
					$ai_montonotas=$this->uf_load_monto_notas_asociadas($ls_codemp,$ls_numsol);
					$ldec_montopendiente  = ($ldec_monsol-$ldec_montocancelado)+$ai_montonotas;
				}
			   $arrMovNumpagmin[$j]['numsolicitud']  = $ls_numsol;
			   $arrMovNumpagmin[$j]['consolicitud']  = $ls_consol;
			   $arrMovNumpagmin[$j]['monsolicitud']  = number_format($ldec_monsol,2,',','.');
			   $arrMovNumpagmin[$j]['montopendiente']= number_format($ldec_montopendiente,2,',','.');
			   $arrMovNumpagmin[$j]['montop']  		 = number_format($ldec_montopendiente,2,',','.');
			   $arrMovNumpagmin[$j]['codfuefin']  	 = $ls_codfuefin;
			   $arrMovNumpagmin[$j]['fechapropag']   = $ld_fecpropag;
			   $arrMovNumpagmin[$j]['nombenalt']  	 = $ls_nombenealt;
			   $j++;
			   $resultado->MoveNext();
			}
		 }
		unset($conexionbd);
		unset($resultado);
		return $arrMovNumpagmin;
	}//Fin de uf_cargar_programaciones


/* (non-PHPdoc)
 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
 */									
public function buscarNumdocCheques($codban,$ctaban)
{
  $arrNumdocChq=array();

 $cadenasql = " SELECT scb_cheques.codban as codban,scb_cheques.ctaban as ctaban ,scb_cheques.numche as numche, ".
	       " scb_cheques.estche as estche,scb_cheques.numchequera as numchequera,scb_banco.nomban as nomban, ".
		   " scb_ctabanco.dencta as dencta,scb_tipocuenta.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta ".
		   " FROM scb_cheques ,scb_banco ,scb_ctabanco ,scb_tipocuenta ".
		   " WHERE scb_cheques.codemp='".$_SESSION["la_empresa"]["codemp"]."' ".
		   " AND scb_cheques.codban  like '%".$codban."%' ".
		   " AND scb_cheques.ctaban like '%".$ctaban."%' ".
		   " AND scb_cheques.codusu ='".$_SESSION['la_logusr']."' ".
		   " AND scb_cheques.estche<>1 ".
		   " AND scb_cheques.codemp=scb_banco.codemp ".
		   " AND scb_cheques.codban=scb_banco.codban ".
		   " AND scb_cheques.codemp=scb_ctabanco.codemp ". 
		   " AND scb_banco.codban=scb_ctabanco.codban ".
		   " AND scb_cheques.ctaban=scb_ctabanco.ctaban ".
		   " AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta ".
		   " ORDER BY scb_cheques.numchequera, scb_cheques.numche";

	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$resultado = $conexionbd->Execute ( $cadenasql );
	if ($resultado===false)
	{
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
	}
	else
	{
		$j=0;
		while((!$resultado->EOF))
		{
			$ls_codban 		  = trim($resultado->fields["codban"]);
			$ls_ctaban 		  = trim($resultado->fields["ctaban"]);
			$ls_numche 		  = $resultado->fields["numche"];
			$ls_estche   	  = $resultado->fields["estche"];
			$ls_numchequera	  = $resultado->fields["numchequera"];
			$ls_nomban   	  = $resultado->fields["nomban"];
			$ls_nomcta  	  = $resultado->fields["dencta"];
			$ls_codtipcta	  = $resultado->fields["codtipcta"];					 
			$ls_nomtipcta     = $resultado->fields["nomtipcta"];
			
		  
		   $arrNumdocChq[$j]['cheque']   = $ls_numche;
		   $arrNumdocChq[$j]['chequera'] = $ls_numchequera;
		   $arrNumdocChq[$j]['banco']    = $ls_nomban;
		   $arrNumdocChq[$j]['cuenta']   = $ls_nomcta;
		   $arrNumdocChq[$j]['estatus']  = $ls_estche;
		   $j++;
		   $resultado->MoveNext();
		}
	}
	unset($conexionbd);
	unset($resultado);
	return $arrNumdocChq;
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function uf_load_notas_asociadas($as_codemp,$as_numsol)
	{
		$lb_valido=true;
		$as_codemp = $_SESSION["la_empresa"]["codemp"];
		$ai_montonotas=0;
		$cadenasql = "SELECT SUM(CASE cxp_sol_dc.codope WHEN 'NC' THEN (-1*cxp_sol_dc.monto) ".
			   "  ELSE (cxp_sol_dc.monto) END) as total ".
			   "  FROM cxp_dt_solicitudes, cxp_sol_dc ".
			   " WHERE cxp_dt_solicitudes.codemp='".$as_codemp."' ".
			   "   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
			   "   AND cxp_sol_dc.estnotadc= 'C' ".
			   "   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
			   "   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
			   "   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
			   "   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
			   "   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
			   "   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ai_montonotas=$resultado->fields["total"];
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $lb_valido;
	}

	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function uf_load_monto_notas_asociadas($as_codemp,$as_numsol)
	{
		$lb_valido=true;
		$as_codemp = $_SESSION["la_empresa"]["codemp"];
		$ai_montonotas=0;
		$cadenasql = "SELECT SUM(CASE cxp_sol_dc.codope WHEN 'NC' THEN (-1*cxp_sol_dc.monto) ".
			   "  ELSE (cxp_sol_dc.monto) END) as total ".
			   "  FROM cxp_dt_solicitudes, cxp_sol_dc ".
			   " WHERE cxp_dt_solicitudes.codemp='".$as_codemp."' ".
			   "   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
			   "   AND cxp_sol_dc.estnotadc= 'C' ".
			   "   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
			   "   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
			   "   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
			   "   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
			   "   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
			   "   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ai_montonotas=$resultado->fields["total"];
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $ai_montonotas;
	}

	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function uf_select_ctacxpclasificador($as_numsol,$as_provbene,$as_codprobene)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:	  uf_select_ctacxpclasificador
	// Access:		  public
	//	Returns:	  String--- Retorno la cuenta contable del catalogo de clasificación de CXP
	//	Description:  Funcion que busca la cuenta contable de la recepción o recepciones
	//////////////////////////////////////////////////////////////////////////////
	$as_codemp = $_SESSION["la_empresa"]["codemp"];
	if($as_provbene=='P')
	{
		
		$cadenasql=	"SELECT sc_cuenta ".
					"	FROM   cxp_rd_scg, cxp_dt_solicitudes ". 
					"	WHERE  cxp_rd_scg.codemp='".$as_codemp."' ".
					"	AND cxp_rd_scg.cod_pro='".$as_codprobene."' ".
					"	AND cxp_rd_scg.debhab='H' ".
					"	AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
					"	AND cxp_rd_scg.codemp=cxp_dt_solicitudes.codemp ".
					"	AND cxp_rd_scg.cod_pro=cxp_dt_solicitudes.cod_pro ".
					"	AND cxp_rd_scg.numrecdoc=cxp_dt_solicitudes.numrecdoc ";
	}
	else
	{
		$cadenasql=	"SELECT sc_cuenta ".
					"	FROM   cxp_rd_scg, cxp_dt_solicitudes ". 
					"	WHERE  cxp_rd_scg.codemp='".$as_codemp."' ".
					"	AND cxp_rd_scg.ced_bene='".$as_codprobene."' ".
					"	AND cxp_rd_scg.debhab='H' ".
					"	AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
					"	AND cxp_rd_scg.codemp=cxp_dt_solicitudes.codemp ".
					"	AND cxp_rd_scg.cod_pro=cxp_dt_solicitudes.cod_pro ".
					"	AND cxp_rd_scg.numrecdoc=cxp_dt_solicitudes.numrecdoc ";
	}	
	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$resultado = $conexionbd->Execute ( $cadenasql );
	if ($resultado===false)
	{
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
	}
	else
	{
		while((!$resultado->EOF))
		{
			$ls_cuenta_scg=$resultado->fields["sc_cuenta"];
			$resultado->MoveNext();
		}
	}
	return $ls_cuenta_scg;

	}//Fin de uf_select_ctacxpclasificador
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function uf_verificar_sol_repcajachica($as_codemp,$as_numsol)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_verificar_sol_repcajachica
		//	          Access:  public
		//	        Arguments  
		//	         Returns:  lb_valido.
		//	     Description:  Función que verifica si una solicitud corresponde a una reposición de caja chica
		//     Elaborado Por:  OFIMATICA DE VENEZUELA,C.A. - Ing. Nelson Barraez
		// Fecha de Creación:  01-06-2011       			Fecha Última Actualización:
		//////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$cadenasql="SELECT repcajchi
				   FROM cxp_solicitudes
				  WHERE codemp='".$as_codemp."'
					AND numsol='".$as_numsol."'"; 
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
			$lb_valido = false;
		}
		else
		{
			if (!$resultado->EOF)
			{
				$lb_valido = true;
			}
		}
		return $lb_valido;
	}/// fin uf_verificar_sol_repcajachica
	/////////////////////////////////////////FIN BLOQUE AGREGADO POR OFIMATICA DE VENEZUELA/////////////////////////////////////////////////
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	function uf_select_ctaprovbene($as_provbene,$as_codprobene,$as_codban,$as_ctaban)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:	  uf_select_catprovben
	// Access:		  public
	//	Returns:	  String--- Retorno la cuenta contable del proveedor o beneficiario y como parametro de referenica el banco y la cuenta de banco del mismo
	//	Description:  Funcion que busca el banco, la cuenta de banbco y la cuenta contable del proveedor o beneficiario.
	//////////////////////////////////////////////////////////////////////////////
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
	if($as_provbene=='P')
	{
		
		$cadenasql="SELECT codban,ctaban,sc_cuenta
				 FROM   rpc_proveedor 
				 WHERE  codemp='".$ls_codemp."' AND cod_pro='".$as_codprobene."'";
	}
	else
	{
		$cadenasql="SELECT codban,ctaban,sc_cuenta
				 FROM   rpc_beneficiario 
				 WHERE  codemp='".$ls_codemp."' AND ced_bene='".$as_codprobene."'";
	}	
	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$resultado = $conexionbd->Execute ( $cadenasql );
	if ($resultado===false)
	{
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		$lb_valido = false;
	}
	else
	{
		while((!$resultado->EOF))
		{
			$as_codban=$resultado->fields["codban"];
			$as_ctaban=$resultado->fields["ctaban"];
			$ls_cuenta_scg=$resultado->fields["sc_cuenta"];
			$resultado->MoveNext();
		}
	}
	return $ls_cuenta_scg;
	
	}//Fin de uf_select_ctaprovbene
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_load_retenciones_iva_cxp($as_codemp,$as_numsol)
	{
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_load_retenciones_iva_cxp
	//		   Access: public
	//		 Argument: $as_codemp = Código de la Empresa.
	//                 $as_numsol = Número de la Solicitud de Pago.
	//	  Description: Función que extrae la sumatoria de las retenciones de IVA Cuentas Por Pagar asociadas
	//                 a una Solicitud de Pago.
	//	   Creado Por: Ing. Néstor Falcón.
	//     Modificado por: Ing. Jennifer Rivero
	// Fecha Creación: 23/06/2008
	// Fecha de Modificación:17/10/2008
	////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $li_i = 0;
	  $la_deducciones = array();
	  $cadenasql = "SELECT max(cxp_rd_deducciones.codded) as codded, max(sigesp_deducciones.dended) as dended, 
						max(cxp_rd_deducciones.sc_cuenta) as sc_cuenta, max(cxp_rd_deducciones.monobjret) as monobjret, 
						COALESCE(sum(cxp_rd_deducciones.monret),0) as montotret
				   FROM cxp_dt_solicitudes, cxp_solicitudes, cxp_rd_deducciones, cxp_rd, sigesp_deducciones
				  WHERE cxp_solicitudes.codemp = '".$as_codemp."'
					AND cxp_solicitudes.numsol = '".$as_numsol."'
					AND sigesp_deducciones.iva=1
					AND sigesp_deducciones.islr=0
					AND sigesp_deducciones.estretmun=0
					AND sigesp_deducciones.otras=0
					AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
					AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.codemp=cxp_rd_deducciones.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd_deducciones.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd_deducciones.codtipdoc
					AND cxp_dt_solicitudes.ced_bene=cxp_rd_deducciones.ced_bene
					AND cxp_dt_solicitudes.cod_pro=cxp_rd_deducciones.cod_pro
					AND cxp_rd.codemp=cxp_rd_deducciones.codemp
					AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc
					AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc 
					AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
					AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
					AND sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
					AND sigesp_deducciones.codded=cxp_rd_deducciones.codded
				  GROUP BY cxp_solicitudes.numsol";
		$cadenasql = $cadenasql." UNION ".
				  "SELECT max(cxp_rd_deducciones.codded) as codded, max(sigesp_deducciones.dended) as dended, 
						max(cxp_rd_deducciones.sc_cuenta) as sc_cuenta, max(cxp_rd_deducciones.monobjret) as monobjret, 
						COALESCE(sum(cxp_rd_deducciones.monret),0) as montotret
				   FROM cxp_dt_solicitudes, cxp_solicitudes, cxp_rd_deducciones, cxp_rd, sigesp_deducciones
				  WHERE cxp_solicitudes.codemp = '".$as_codemp."'
					AND cxp_solicitudes.numsol = '".$as_numsol."'
					AND sigesp_deducciones.iva=0
					AND sigesp_deducciones.islr=1
					AND sigesp_deducciones.estretmun=0
					AND sigesp_deducciones.otras=0
					AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
					AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.codemp=cxp_rd_deducciones.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd_deducciones.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd_deducciones.codtipdoc
					AND cxp_dt_solicitudes.ced_bene=cxp_rd_deducciones.ced_bene
					AND cxp_dt_solicitudes.cod_pro=cxp_rd_deducciones.cod_pro
					AND cxp_rd.codemp=cxp_rd_deducciones.codemp
					AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc
					AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc 
					AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
					AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
					AND sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
					AND sigesp_deducciones.codded=cxp_rd_deducciones.codded
				  GROUP BY cxp_solicitudes.numsol";
	  $conexionbd = ConexionBaseDatos::getInstanciaConexion();
	  $resultado = $conexionbd->Execute ( $cadenasql );
	  if ($resultado===false)
	  {
		 $mensaje .= '  ->'.$conexionbd->ErrorMsg();
	  }
	  else
	  {
		$j=0;
		while((!$resultado->EOF))
		{
			$ls_codded 		  = trim($resultado->fields["codded"]);
			$ls_dended 		  = trim($resultado->fields["dended"]);
			$ls_scuenta		  = trim($resultado->fields["sc_cuenta"]);
			$li_monobjret  	  = $resultado->fields["monobjret"];
			$li_montotret	  = $resultado->fields["montotret"];
			
		    $la_deducciones[$j]['codded']    = $ls_codded;
		    $la_deducciones[$j]['dended']    = $ls_dended;
		    $la_deducciones[$j]['sc_cuenta'] = $ls_scuenta;
		    $la_deducciones[$j]['monobjret'] = $li_monobjret;
		    $la_deducciones[$j]['montotret'] = $li_montotret;
			
		    $j++;
		    $resultado->MoveNext();
		}
	  }
	  unset($conexionbd);
	  unset($resultado);
	  return $la_deducciones;			  
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_recepciones_asociadas($as_numsol)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_load_recepciones
	//		   Access: public
	//		 Argument: as_numsol // Número de solicitud
	//	  Description: Función que busca las recepciones de documentos asociadas a una solicitud
	//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
	// Fecha Creación: 29/04/2007								Fecha Última Modificación : 
	//////////////////////////////////////////////////////////////////////////////
	$lb_valido=true;
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
	$cadenasql="SELECT cxp_dt_solicitudes.numrecdoc, cxp_rd.codrecdoc".
			"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd ".	
			" WHERE cxp_dt_solicitudes.codemp='".$ls_codemp."' ".
			"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
			"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
			"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
			"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
			"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
			"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
			"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
			"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene";
    
	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$resultado = $conexionbd->Execute ( $cadenasql );
	if ($resultado===false)
	{
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		return false;	
	}
	return $resultado;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------
    
	//--------------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_dt_cxpspg($as_numsol)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:	    uf_buscar_dt_cxpspg
	// 	Access:			public
	//	Returns:		Boolean Retorna si proceso correctamente
	//	Description:	Funcion que se buscar el detalle presupuestario de una solicitud de pago 
	//////////////////////////////////////////////////////////////////////////////
	$aa_dt_cxpspg = array();
	$aa_dt_scbspg = array();
	$aa_dt_spg    = array();
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
	
	//BUSCANDO LOS DETALLES PRESUPUESTARIOS DE LOS PAGOS ANTERIORES
	$cadenasql="SELECT codestpro, spg_cuenta, sum(monto) as monto, estcla
			 FROM scb_movbco_spg
			 WHERE codemp='".$ls_codemp."'
			 AND procede_doc='CXPSOP' 
			 AND documento ='".$as_numsol."'
			 AND estmov <> 'A'
			 AND estmov <> 'O'
			 GROUP BY codestpro, spg_cuenta, estcla";
	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$rs_dt_spgchq = $conexionbd->Execute ( $cadenasql );
	if ($rs_dt_spgchq===false)
	{
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		return false;	
	}
	
	//AHORA BUSCANDO LOS DETALLES PRESUPUESTARIOS DE LA SOLICITUD
	$ls_conrecdoc=$_SESSION["la_empresa"]["conrecdoc"];
	if($ls_conrecdoc!=1)
	{
		$cadenasql="SELECT spg_dt_cmp.codestpro1 as codestpro1,
			                spg_dt_cmp.codestpro2 as codestpro2,
							spg_dt_cmp.codestpro3 as codestpro3,
							spg_dt_cmp.codestpro4 as codestpro4,
							spg_dt_cmp.codestpro5 as codestpro5,
							spg_dt_cmp.spg_cuenta as spg_cuenta,
							sum(spg_dt_cmp.monto) as monto,
							spg_dt_cmp.descripcion as descripcion,
							spg_dt_cmp.estcla as estcla,0 as nota
					   FROM sigesp_cmp, spg_dt_cmp
					  WHERE spg_dt_cmp.codemp='".$ls_codemp."'
					    AND spg_dt_cmp.procede='CXPSOP'
					    AND spg_dt_cmp.comprobante='".$as_numsol."'
					    AND sigesp_cmp.codemp=spg_dt_cmp.codemp
						AND sigesp_cmp.procede=spg_dt_cmp.procede
						AND sigesp_cmp.comprobante=spg_dt_cmp.comprobante
						AND sigesp_cmp.fecha=spg_dt_cmp.fecha
						AND sigesp_cmp.codban=spg_dt_cmp.codban
						AND sigesp_cmp.ctaban=spg_dt_cmp.ctaban						
					  GROUP BY spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,
					           spg_dt_cmp.codestpro5,spg_dt_cmp.spg_cuenta,spg_dt_cmp.estcla,spg_dt_cmp.descripcion
					 UNION ".
					"SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,sum(spg_dt_cmp.monto) as monto,descripcion,estcla,1 as nota ".
					"	FROM spg_dt_cmp, cxp_dt_solicitudes, cxp_sol_dc ".
					" WHERE spg_dt_cmp.codemp='".$ls_codemp."' ".
					"   AND spg_dt_cmp.procede='CXPNOC' ".
					"   AND spg_dt_cmp.comprobante=LPAD(cxp_sol_dc.numdc,15,'0') ".
					"   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
					"   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
					"   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
					"   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
					"   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
					"   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
					"   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro ".
					" GROUP BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,estcla,descripcion ".
					" UNION  ".
					"SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,sum(spg_dt_cmp.monto) as monto,descripcion,estcla,2 as nota ".
					"	FROM spg_dt_cmp, cxp_dt_solicitudes, cxp_sol_dc ".
					" WHERE spg_dt_cmp.codemp='".$ls_codemp."' ".
					"   AND spg_dt_cmp.procede='CXPNOD' ".
					"   AND spg_dt_cmp.comprobante=LPAD(cxp_sol_dc.numdc,15,'0') ".
					"   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
					"   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
					"   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
					"   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
					"   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
					"   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
					"   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro ".
					" GROUP BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,estcla,descripcion ";
	}
	else
	{
		$rs_dararec=$this->uf_obtener_recepciones_asociadas($as_numsol);
		$li_i=0;
		$ls_cadena="";
		$lb_check=false;
		while ((!$rs_dararec->EOF))
		{
			$li_i++;
			$lb_check=true;
			$ls_numrecdoc = trim($rs_dararec->fields["numrecdoc"]);
			$ls_codrecdoc = trim($rs_dararec->fields["codrecdoc"]);
            if (empty($ls_cadena))
			{
            	$ls_cadena = "AND (spg_dt_cmp.comprobante='".$ls_codrecdoc."'";
			}
			else
			{
				$ls_cadena=$ls_cadena." OR spg_dt_cmp.comprobante='".$ls_codrecdoc."'";
			}
		    $rs_dararec->MoveNext();
		}
		if ($lb_check==false)
		{
			if (!empty($ls_cadena))
			{
				$ls_cadena = $ls_cadena." OR spg_dt_cmp.comprobante='".$as_numsol."')";
			}
			else
			{
				$ls_cadena = " AND comprobante='".$as_numsol."'";
			}
		}
		else
		{
			$ls_cadena=$ls_cadena." )";
		}
		$cadenasql = "SELECT spg_dt_cmp.codestpro1 as codestpro1,
			                  spg_dt_cmp.codestpro2 as codestpro2,
							  spg_dt_cmp.codestpro3 as codestpro3,
							  spg_dt_cmp.codestpro4 as codestpro4,
							  spg_dt_cmp.codestpro5 as codestpro5,
					          spg_dt_cmp.spg_cuenta as spg_cuenta,
							  sum(spg_dt_cmp.monto) as monto,
							  spg_dt_cmp.descripcion as descripcion,
							  spg_dt_cmp.estcla as estcla,0 as nota
						 FROM sigesp_cmp, spg_dt_cmp
					    WHERE spg_dt_cmp.codemp='".$ls_codemp."' 
						  AND sigesp_cmp.procede='CXPRCD' $ls_cadena 
						  AND sigesp_cmp.codemp=spg_dt_cmp.codemp
						  AND sigesp_cmp.procede=spg_dt_cmp.procede
						  AND sigesp_cmp.comprobante=spg_dt_cmp.comprobante
						  AND sigesp_cmp.fecha=spg_dt_cmp.fecha
						  AND sigesp_cmp.codban=spg_dt_cmp.codban
						  AND sigesp_cmp.ctaban=spg_dt_cmp.ctaban
					    GROUP BY spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,
								 spg_dt_cmp.spg_cuenta,spg_dt_cmp.estcla,spg_dt_cmp.descripcion
					 UNION ".
					"SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,sum(spg_dt_cmp.monto) as monto,descripcion,estcla,1 as nota ".
					"	FROM spg_dt_cmp, cxp_dt_solicitudes, cxp_sol_dc ".
					" WHERE spg_dt_cmp.codemp='".$ls_codemp."' ".
					"   AND spg_dt_cmp.procede='CXPNOC' ".
					"   AND spg_dt_cmp.comprobante=LPAD(cxp_sol_dc.numdc,15,'0') ".
					"   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
					"   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
					"   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
					"   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
					"   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
					"   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
					"   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro ".
					" GROUP BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,estcla,descripcion ".
					" UNION  ".
					"SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,sum(spg_dt_cmp.monto) as monto,descripcion,estcla,2 as nota ".
					"	FROM spg_dt_cmp, cxp_dt_solicitudes, cxp_sol_dc ".
					" WHERE spg_dt_cmp.codemp='".$ls_codemp."' ".
					"   AND spg_dt_cmp.procede='CXPNOD' ".
					"   AND spg_dt_cmp.comprobante=LPAD(cxp_sol_dc.numdc,15,'0') ".
					"   AND cxp_dt_solicitudes.numsol='".$as_numsol."' ".
					"   AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
					"   AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
					"   AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc ".
					"   AND cxp_dt_solicitudes.codtipdoc = cxp_sol_dc.codtipdoc ".
					"   AND cxp_dt_solicitudes.ced_bene = cxp_sol_dc.ced_bene ".
					"   AND cxp_dt_solicitudes.cod_pro = cxp_sol_dc.cod_pro ".
					" GROUP BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,estcla,descripcion ";
	}
	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$rs_dt_cxpspg = $conexionbd->Execute ( $cadenasql );
	if ($rs_dt_cxpspg===false)
	{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();		
			return false;
	}
	if (!$rs_dt_cxpspg->EOF&&!$rs_dt_spgchq->EOF) 
	{
		$i = 0;
		$ld_totpre=0;
		$aa_dt_scbspg= $rs_dt_spgchq->GetArray();
		$aa_dt_cxpspg = $rs_dt_cxpspg->GetArray();
		foreach($aa_dt_cxpspg as $dt_cxpspg)
		{
			$ls_codestpro1 = $dt_cxpspg["codestpro1"];
			$ls_codestpro2 = $dt_cxpspg["codestpro2"];
			$ls_codestpro3 = $dt_cxpspg["codestpro3"];
			$ls_codestpro4 = $dt_cxpspg["codestpro4"];
			$ls_codestpro5 = $dt_cxpspg["codestpro5"];
			$ls_estcla     = $dt_cxpspg["estcla"];
			$ls_spg_cuenta = trim($dt_cxpspg["spg_cuenta"]);
			$ldec_monto    = $dt_cxpspg["monto"];
			$li_nota       = $dt_cxpspg["nota"];
			$ls_descripcion = $dt_cxpspg["descripcion"];
			switch ($li_nota) 
			{
				case 0:
					$ld_totpre     = $ld_totpre + doubleval($ldec_monto);
					break;
				case 1:
					$ld_totpre     = $ld_totpre - doubleval($ldec_monto);
					break;
				case 2:
					$ld_totpre     = $ld_totpre + doubleval($ldec_monto);
					break;
			}
			
			foreach($aa_dt_scbspg as $dt_scbspg)
			{
				$ls_estpro1    = substr($dt_scbspg["codestpro"],0,25);
				$ls_estpro2    = substr($dt_scbspg["codestpro"],25,25);
				$ls_estpro3    = substr($dt_scbspg["codestpro"],50,25);
				$ls_estpro4    = substr($dt_scbspg["codestpro"],75,25);
				$ls_estpro5    = substr($dt_scbspg["codestpro"],100,25);
				$ls_tipcla     = $dt_scbspg["estcla"];
				$ls_cuentaspg  = trim($dt_scbspg["spg_cuenta"]);
				if ($dt_scbspg["descripcion"]!="")
				{
					$ls_descripcion = $dt_scbspg["descripcion"];
				}
				$ldec_montotmp = $dt_scbspg["monto"];
				if(($ls_codestpro1==$ls_estpro1)&&($ls_codestpro2==$ls_estpro2)&&($ls_codestpro3==$ls_estpro3)&&($ls_codestpro4==$ls_estpro4)&&($ls_codestpro5==$ls_estpro5)&&($ls_spg_cuenta==$ls_cuentaspg)&&($ls_estcla==$ls_tipcla))
				{
					$ldec_new_monto = doubleval($ldec_monto)-doubleval($ldec_montotmp);
					$aa_dt_spg[$i]["codestpro1"]=$ls_codestpro1;
					$aa_dt_spg[$i]["codestpro2"]=$ls_codestpro2;
					$aa_dt_spg[$i]["codestpro3"]=$ls_codestpro3;
					$aa_dt_spg[$i]["codestpro4"]=$ls_codestpro4;
					$aa_dt_spg[$i]["codestpro5"]=$ls_codestpro5;
					$aa_dt_spg[$i]["estcla"]=$ls_estcla;
					$aa_dt_spg[$i]["spg_cuenta"]=$ls_spg_cuenta;
					$aa_dt_spg[$i]["descripcion"]=$ls_descripcion;
					$aa_dt_spg[$i]["monto"]=$ldec_new_monto;
					$i++;
				}
			}
		}
	}
	else
	{
		return $rs_dt_cxpspg->GetArray();;
	}
	unset($aa_dt_cxpspg);
	unset($aa_dt_scbspg);
	return $aa_dt_spg;
}//Fin uf_buscar_dt_cxpspg.	
//--------------------------------------------------------------------------------------------------------------------------------------
function uf_select_original($as_numsol)
{
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
   	$li_monsol=0;
	$cadenasql="SELECT (CASE WHEN cxp_sol_dc.codope = 'NC' ".
	      	" THEN	".
            " cxp_solicitudes.monsol - cxp_sol_dc.monto  ".
            " ELSE ".
	        " cxp_solicitudes.monsol + cxp_sol_dc.monto END) AS monsol".
			" FROM cxp_solicitudes ".
			" INNER JOIN cxp_sol_dc ON  cxp_solicitudes.codemp=cxp_sol_dc.codemp AND cxp_solicitudes.numsol=cxp_sol_dc.numsol".	
			" WHERE cxp_solicitudes.codemp='".$ls_codemp."'".
			" AND cxp_solicitudes.numsol = '".$as_numsol."'";
	$conexionbd = ConexionBaseDatos::getInstanciaConexion();
	$resultado = $conexionbd->Execute ( $cadenasql );
	if ($resultado===false)
	{
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
	}
	else
	{
		while((!$resultado->EOF))
		{
			$li_monsol=$resultado->fields["monsol"];
			$resultado->MoveNext();
		}
	}
	return $li_monsol;
}
//--------------------------------------------------------------------------------------------------------------------------------------
	public function uf_select_pagados($as_numsol,$as_numdoc,$as_codban,$as_ctaban)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$li_pagado=0;
		$cadenasql="SELECT monto,numdoc,codban,ctaban".
				"   FROM cxp_sol_banco".
				"  WHERE codemp='".$ls_codemp."'".
				"	 AND numsol = '".$as_numsol."'".
				"    AND estmov<>'A'".
				"    AND estmov<>'O'";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$li_monto=$resultado->fields["monto"];
				$ls_numdoc=trim($resultado->fields["numdoc"]);
				$ls_codban=trim($resultado->fields["codban"]);
				$ls_ctaban=trim($resultado->fields["ctaban"]);
				if(($ls_numdoc!=trim($as_numdoc)))
				{
					$li_pagado=$li_pagado+$li_monto;
				}
				else
				{
					if(($ls_codban!=trim($as_codban))||($ls_ctaban!=trim($as_ctaban)))
					{
						$li_pagado=$li_pagado+$li_monto;
					}
				}
				$resultado->MoveNext();
			}
		}
		return $li_pagado;
	}
//--------------------------------------------------------------------------------------------------------------------------------------
	public function uf_validar_monto_cancelado($as_numsol,$as_numdoc,$as_codban,$as_ctaban,$ai_monpag)
	{
		$ls_origen=0;
		$li_montosolicitud=$this->uf_select_original($as_numsol);
		$li_montosolicitud=round($li_montosolicitud,2);
		if($li_montosolicitud>0)
		{
			$li_montopagado=$this->uf_select_pagados($as_numsol,$as_numdoc,$as_codban,$as_ctaban);
			if($li_montopagado>=$li_montosolicitud)
			{
				$lb_valido=false;
				$ls_origen=1;
			}
			else
			{
				$li_totalmovimiento=$li_montopagado+$ai_monpag;
				$li_totalmovimiento=round($li_totalmovimiento,2);
				if($li_totalmovimiento>$li_montosolicitud)
				{
					$lb_valido=false;	
					$ls_origen=2;
				}
			}
		}
		return $ls_origen;
	}
//--------------------------------------------------------------------------------------------------------------------------------------
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarChequeraDoc($codban,$ctaban,$codusu)
	{
		$cadenasql = " SELECT numche AS numche, numchequera, ".
		             " max(orden) as orden ".
		             " FROM scb_cheques ".
		             " WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
					 " AND codban = '".$codban."' ".
					 " AND ctaban = '".$ctaban."' ".
					 " AND estche = 0 ".
					 " AND codusu='".rtrim($codusu)."' ".
					 " GROUP BY numchequera, numche ".
					 " ORDER BY orden ASC LIMIT 1"; 
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ls_numche  = $resultado->fields["numche"];
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $ls_numche;
	}
//--------------------------------------------------------------------------------------------------------------------------------------
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarChequera($codban,$ctaban,$codusu)
	{
		$cadenasql = " SELECT numche AS numche, numchequera, ".
		             " max(orden) as orden ".
		             " FROM scb_cheques ".
		             " WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
					 " AND codban = '".$codban."' ".
					 " AND ctaban = '".$ctaban."' ".
					 " AND estche = 0 ".
					 " AND codusu='".rtrim($codusu)."' ".
					 " GROUP BY numchequera, numche ".
					 " ORDER BY orden ASC LIMIT 1"; 

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ls_numchequera  = $resultado->fields["numchequera"];
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $ls_numchequera;
	}
//--------------------------------------------------------------------------------------------------------------------------------------
	public function procesar_emision_chq($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ls_numsol,$ls_estmov,$ldec_monto,$ls_estdoc,$arrevento)
	{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:	    uf_procesar_emision_scq
	// Access:			public
	//	Returns:		Boolean Retorna si proceso correctamente
	//	Description:	Funcion que se encarga de guardar los detalles d ela emision de cheque
	//////////////////////////////////////////////////////////////////////////////
	
	///////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$resultado = 0;
		$ls_codemp   = $_SESSION["la_empresa"]["codemp"];
	    $ls_codusu   = $_SESSION["la_logusr"];			 
		//Creación del Dao con la tabla y campos a insertar.
		
		$this->daoInsertEmich=FabricaDao::CrearDAO("N","cxp_sol_banco");
		$this->daoInsertEmich->codemp       = $ls_codemp;
		$this->daoInsertEmich->codban    	= $ls_codban; 
		$this->daoInsertEmich->ctaban       = $ls_ctaban; 
		$this->daoInsertEmich->numdoc       = $ls_numdoc; 
		$this->daoInsertEmich->codope       = $ls_codope; 
		$this->daoInsertEmich->numsol       = $ls_numsol; 
		$this->daoInsertEmich->estmov       = $ls_estmov; 
		$this->daoInsertEmich->monto        = $ldec_monto; 
		//Iniciar transacción
		//--DaoGenerico::iniciarTrans();
		//Insertamos la programación de pago
		$resultado = $this->daoInsertEmich->incluir();
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];
		
		if ($resultado)
		{
			if ($ls_estdoc=='C')
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$strPk = "codemp='{$ls_codemp}' AND numsol='{$ls_numsol}'";
				$this->daoInsertEmich = FabricaDao::CrearDAO("C", "scb_prog_pago", '', $strPk);
				$this->daoInsertEmich->estmov = $ls_estdoc ;
				$resultado = $this->daoInsertEmich->modificar();
				
				if ($resultado)
				{
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
					$strPk = "codemp='{$ls_codemp}' AND numsol='{$ls_numsol}'";
					$this->daoInsertEmich = FabricaDao::CrearDAO("C", "cxp_solicitudes", '', $strPk);
					$this->daoInsertEmich->estprosol = 'P' ;
					$resultado = $this->daoInsertEmich->modificar();
					if ($resultado)
					{
						$lb_valido=true;
					}
					else
					{
						$lb_valido=false;
					}
				}
				else
				{
					$arrevento ['desevetra'] = $this->daoInsertEmich->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$lb_valido=false;
				}
			}
			if ($resultado)
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$lb_valido=true;
			}
			else
			{
				$arrevento ['desevetra'] = $this->daoInsertEmich->ErrorMsg();
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
				$lb_valido=false;
			}
		}
		else
		{
			$arrevento ['desevetra'] = $this->daoInsertEmich->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
			$lb_valido=false;
		}
		
	//liberando variables y retornando el resultado de la operacion
	unset($this->daoInsertEmich);	
    return $lb_valido;	

	}//Fin de  procesar_emision_chq	
//--------------------------------------------------------------------------------------------------------------------------------------
	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()*/
	public function emitirCheque($tipproben,$codproben,$codban,$ctaban,$numdoc,$fecmov,$codope,$estmov,$montomov,
							     $monobjret,$monret,$concepto,$codconmov,$chevau,$nomproben,$numordpagmin,$ls_modageret,
								 $ls_estretmil,$ls_sccuenta,$ls_numchequera,$arremisionch,$arrededucciones,$arrevento)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:	uf_procesar_programacion
		// Access:		public
		//	Returns:	Boolean Retorna si proceso correctamente
		//	Description:	Funcion que se encarga de guardar el movimiento bien sea 
		//						insertando o actualizando
		//////////////////////////////////////////////////////////////////////////////
		$arrCabeceraScb=array();
		$arrDetalleScg=array();
		$arrDetalleSpg=array();
		$arrDetalleSpi=array();
		$resultado = 0;
		$li_cont = 0;
		$li_count_emich = count($arremisionch);
		$li_count_deducc = count($arrededucciones);
		$li_cont_dtscg = 0;
		$li_cont_dtspg = 0;
		if($tipproben=='P')
		{
			$ls_codpro=$codproben;
			$ls_cedbene="----------";
		}
		else
		{
			$ls_codpro="----------";
			$ls_cedbene=$codproben;
		}
		$ls_codemp 	  = $_SESSION["la_empresa"]["codemp"];
		$ls_estretiva = $_SESSION["la_empresa"]["estretiva"];//Indica si las Retenciones IVA se aplican por Cuentas por Pagar o Banco.		
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		$ls_estbenalt = $_SESSION["la_empresa"]["estbenalt"];
		$montomov	  = str_replace(".","",$montomov);
		$montomov     = str_replace(",",".",$montomov);
		$monobjret	  = str_replace(".","",$monobjret);
		$monobjret     = str_replace(",",".",$monobjret);
		$monret       = str_replace(".","",$monret);
		$monret       = str_replace(",",".",$monret);
		$li_totalretenido = $monret;
		$fecmov		  = convertirFechaBd($fecmov);
		//$numdoc=str_pad($numdoc,15,"0",LEFT); 
		
		//Iniciar transacción
		DaoGenerico::iniciarTrans();
		$servicioEvento = new ServicioEvento();
		//Creación del Arreglo de Cabecera
		$arrCabeceraScb["codemp"]	 = $ls_codemp;
		$arrCabeceraScb["codban"]	 = $codban;
		$arrCabeceraScb["ctaban"]	 = $ctaban;
		$arrCabeceraScb["numdoc"]	 = $numdoc;
		$arrCabeceraScb["codope"]	 = $codope;
		$arrCabeceraScb["fecmov"]	 = $fecmov;
		$arrCabeceraScb["conmov"]	 = $concepto;
		$arrCabeceraScb["codconmov"] = $codconmov;
		$arrCabeceraScb["cod_pro"]	 = $ls_codpro;
		$arrCabeceraScb["ced_bene"]	 = $ls_cedbene;
		$arrCabeceraScb["nomproben"] = $nomproben;
		$arrCabeceraScb["monto"]	 = $montomov;
		$arrCabeceraScb["monobjret"] = $monobjret;
		$arrCabeceraScb["monret"]	 = $monret;
		$arrCabeceraScb["chevau"]	 = $chevau;
		$arrCabeceraScb["estmov"]	 = $estmov;
		$arrCabeceraScb["estmovint"] = 0;
		$arrCabeceraScb["estcobing"] = 1;
		$arrCabeceraScb["estbpd"]	 = $tipproben;
		$arrCabeceraScb["procede"]	 = 'SCBBCH';
		$arrCabeceraScb["estreglib"] = "";
		$arrCabeceraScb["tipo_destino"]	 = $tipproben;
		$arrCabeceraScb["numordpagmin"]	 = $numordpagmin;
		$arrCabeceraScb["codfuefin"] = '--';
		$arrCabeceraScb["codtipfon"] = '----';
		$arrCabeceraScb["estmovcob"] = 0;
		$arrCabeceraScb["numconint"] = "";
		$arrCabeceraScb["tranoreglib"] 	 = 0;
		$arrCabeceraScb["numchequera"] 	 = $ls_numchequera;
		$arrCabeceraScb['codbansig'] = '---';
		//Creación del Arreglo de Cabecera
		
		if ($ls_estbenalt=='1')
		{
			$ls_desproben_1=$nomproben;
			$lb_valido=true;
			$ls_nombenalt="";
			$ls_nombenaltant="";
			$lb_pasoben=false;
			for($a=0;$a<$li_count_emich;$a++)
			{
				$ls_nombenalt= $arremisionch[$a]->nombenalt;
				if(!$lb_pasoben)
				{
					$arrCabeceraScb["nomproben"]=$ls_nombenalt;
					$ls_nombenaltant=$ls_nombenalt;
					$lb_paso=true;
				}
				else
				{
					if ($ls_nombenaltant==$ls_nombenalt)
					{
						$arrCabeceraScb["nomproben"]=$ls_nombenalt;
					}
					else
					{
						$lb_valido=false;
						$this->mensaje .=  'No se puede emitir el cheque debido a que existen beneficiarios alternos distintos!';
						break;
					}
				}
			}
		}
		$ldec_montoretbanco=$monret;
		if (($ls_estbenalt =='1')&&($lb_valido))
		{
			if ($nomproben=="")
			{
				$arrCabeceraScb["nomproben"]=$ls_desproben_1;
			}
		}
		elseif ($ls_estbenalt =='0')
		{
			$lb_valido=true;
		}	
		$lb_pago=false;
		if ($lb_valido==true)
		{
			$ls_checklist='';
			for($i=0;$i<$li_count_emich;$i++)
			{
				$li_cont++;
				$lb_pago			 = true;					
				$ld_montotret 		 = 0;
				$ls_numsol   		 = $arremisionch[$i]->numsolicitud;
				$ldec_monsol 		 = $arremisionch[$i]->monsolicitud;
				$ldec_monsol 		 = str_replace(".","",$ldec_monsol);
				$ldec_monsol		 = str_replace(",",".",$ldec_monsol);
				$ldec_montopendiente = $arremisionch[$i]->montopendiente;
				$ldec_montopendiente = str_replace(".","",$ldec_montopendiente);
				$ldec_montopendiente = str_replace(",",".",$ldec_montopendiente);
				$ldec_monto			 = $arremisionch[$i]->montop;
				$ldec_monto			 = str_replace(".","",$ldec_monto);
				$ldec_monto			 = str_replace(",",".",$ldec_monto);
				$ls_codfuefin		 = $arremisionch[$i]->codfuefin;
				$ls_numsolp			 = $ls_numsol;
				$ldec_montop		 = $ldec_monto;
				if ($ldec_montopendiente==$ldec_monto)
				{
					$ls_estsol='C';	//Cancelado							
				}
				else
				{
					$ls_estsol='P';//Programado
				}
				$ls_estsolp			= $ls_estsol;
				
				if ($lb_valido)
				{
					if ($ls_clactacon==1)
					{
						$ls_ctaprovbene = $this->uf_select_ctacxpclasificador($ls_numsol,$tipproben,$codproben);
					}
					else
					{
						if($this->uf_verificar_sol_repcajachica($ls_codemp,$ls_numsol))
						{
							$ls_ctaprovbene=$_SESSION["la_empresa"]["repcajchi"];
						}
						else
						{
							$ls_ctaprovbene = $this->uf_select_ctaprovbene($tipproben,$codproben,$codban,$ctaban);//Ojo aqui 
						}
						////////////////////////////////////////FIN MODIFICACION OFIMATICA DE VENEZUELA////////////////////////////////////////////////////////////////////////////
					}
				}
				//print "Ret-Iva-->  ".$ls_estretiva."<br>";
				//print "ModAgeRet-->  ".$ls_modageret."<br>";
				if ($ls_estretiva=='B')//Retenciones aplicadas desde el Módulo de Cuentas Por Pagar y reflejadas en el Módulo Banco.
				{
				  $ls_procede_doc  = "CXPSOP";
				  $la_deducciones1 = $this->uf_load_retenciones_iva_cxp($ls_codemp,$ls_numsol);
				}
				elseif($ls_estretiva=='C')//Retenciones aplicadas desde el Módulo de Cuentas Por Pagar.
				{
				  $ls_procede_doc = "SCBBCH";
				  if ($li_count_deducc > 0)
				  {
				  	$la_deducciones1 = $arrededucciones; 
				  }										
				} 
				$li_total = 0;
				if (!empty($la_deducciones1))
				{
					if ($ls_estretiva=='C')
					{
						$li_total = count($la_deducciones1["codded"]);
					}
					else
					{
						$li_total = count($la_deducciones1["codded"]);
					}
				}
				if ($ls_modageret=="B")/// se realiza el calculo de la ret. municipal
				{
					$la_deducciones2=$arrededucciones;
					$li_total2 = count($la_deducciones2["codded"]); //VIENEN DE LAS DEDUCCIONES POR BANCO
					if ($li_total2 > 0)
					{
						for ($j=0;$j<$li_total2;$j++)
						{ 
							$ls_ctascg1	 = trim($la_deducciones2["sc_cuenta"][$j]);
							$ls_dended1	 = $la_deducciones2["dended"][$j];
							$ls_codded1	 = $la_deducciones2["codded"][$j];
							$ldec_objret1   = $la_deducciones2["objret"][$j];
							$ldec_montoret1 = round($la_deducciones2["montotret"][$j],2);										
							$ld_montotret 	 = $ld_montotret+$ldec_montoret1;
							if (!empty($ls_codded1))
							{
								if (strpos($ls_checklist,$ls_codded1)==0)
								{
									// Se envia el arreglo de detalles contables
									$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]	= $ls_ctascg1;
									$arrDetalleScg["procede_doc"][$li_cont_dtscg] 	= $ls_procede_doc;
									$arrDetalleScg["desmov"][$li_cont_dtscg]	 	= $ls_dended1;
									$arrDetalleScg["documento"][$li_cont_dtscg]	 	= $ls_numsol;
									$arrDetalleScg["debhab"][$li_cont_dtscg]	 	= 'H';
									$arrDetalleScg["monto"][$li_cont_dtscg]	 	    = $ldec_montoret1;
									$arrDetalleScg["monobjret"][$li_cont_dtscg]	 	= $ldec_objret1;
									$arrDetalleScg["codded"][$li_cont_dtscg]	 	= $ls_codded1;
									$li_cont_dtscg++;
									$ls_checklist=$ls_checklist.",".$ls_codded1;
								}
							}
				  		}
					}
				}
				if ($li_total > 0)
				{
					for ($i=0;$i<$li_total;$i++)
					{
						if ($ls_estretiva=='C')
						{
							$ls_ctascg	   = trim($la_deducciones1["sc_cuenta"][$i]);
							$ls_dended	   = $la_deducciones1["dended"][$i];
							$ls_codded	   = $la_deducciones1["codded"][$i];
							$ldec_objret   = $la_deducciones1["objret"][$i];
							$ldec_montoret = round($la_deducciones1["montotret"][$i],2);										
							$ld_montotret  = $ld_montotret+$ldec_montoret; 
							if ($ls_codded!="")
							{ 
								if (strpos($ls_checklist,$ls_codded)==0)
								{
									// Se envia el arreglo de detalles contables
									$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]	= $ls_ctascg;
									$arrDetalleScg["procede_doc"][$li_cont_dtscg] 	= $ls_procede_doc;
									$arrDetalleScg["desmov"][$li_cont_dtscg]	 	= $ls_dended;
									$arrDetalleScg["documento"][$li_cont_dtscg]	 	= $ls_numsol;
									$arrDetalleScg["debhab"][$li_cont_dtscg]	 	= 'H';
									$arrDetalleScg["monto"][$li_cont_dtscg]	 	    = $ldec_montoret;
									$arrDetalleScg["monobjret"][$li_cont_dtscg]	 	= $ldec_montoret;
									$arrDetalleScg["codded"][$li_cont_dtscg]	 	= $ls_codded;
									$li_cont_dtscg++;
									$ls_checklist=$ls_checklist.",".$ls_codded;
								}	
							 }
						}
						else
						{
							$ls_ctascg	   = trim($la_deducciones1["sc_cuenta"][$i]);
							$ls_dended	   = $la_deducciones1["dended"][$i];
							$ls_codded	   = $la_deducciones1["codded"][$i];
							$ldec_objret   = $la_deducciones1["objret"][$i];
							$ldec_montoret = round($la_deducciones1["montotret"][$i],2);											
							$ld_montotret  = $ld_montotret+$ldec_montoret; 
							if ($ls_codded!="")
							   { 
								  if (strpos($ls_checklist,$ls_codded)==0)
								 {
									// Se envia el arreglo de detalles contables
									$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]	= $ls_ctascg;
									$arrDetalleScg["procede_doc"][$li_cont_dtscg] 	= $ls_procede_doc;
									$arrDetalleScg["desmov"][$li_cont_dtscg]	 	= $ls_dended;
									$arrDetalleScg["documento"][$li_cont_dtscg]	 	= $ls_numsol;
									$arrDetalleScg["debhab"][$li_cont_dtscg]	 	= 'H';
									$arrDetalleScg["monto"][$li_cont_dtscg]	 	    = $ldec_montoret;
									$arrDetalleScg["monobjret"][$li_cont_dtscg]	 	= $ldec_objret;
									$arrDetalleScg["codded"][$li_cont_dtscg]	 	= $ls_codded;
									$li_cont_dtscg++;
									$ls_checklist=$ls_checklist.",".$ls_codded;
								 }	
							   }
						}
					}
				}
				if ($ls_estretiva=='B')
				{
					$ldec_montotot=($montomov-$ldec_montoretbanco);
				   //$ldec_montotot=$ldec_montomov;
				}
			 	elseif($ls_estretiva=='C')
				{
					if (($ls_modageret=="B")||( $ls_estretmil=='B'))/// se realiza el calculo de la ret. municipal ó 1x1000
				    {
						$ldec_montotot=(round($montomov,2))-(round($li_totalretenido,2));
				  	}
				  	else
				  	{
						$ldec_montotot=$montomov;
				  	}		
			 	}
			 	unset($la_deducciones1);
				if ($lb_valido)
				{ 
					 $ldec_monto_spg=0;
					 $ldec_montospg2=0;
					 $aa_dt_spgcxp=$this->uf_buscar_dt_cxpspg($ls_numsol);
					 
					 //CALCULO TOTAL PRESUPUESTARIO
					 $ld_totpre=0;
					 foreach($aa_dt_spgcxp as $dt_cxpspg)
					 {
						$ld_mon_aux    = $dt_cxpspg["monto"];
						$li_nota       = $dt_cxpspg["nota"];
						switch ($li_nota)
						 {
							case 0:
								$ld_totpre     = $ld_totpre + doubleval($ld_mon_aux);
								break;
							case 1:
								$ld_totpre     = $ld_totpre - doubleval($ld_mon_aux);
								break;
							case 2:
								$ld_totpre     = $ld_totpre + doubleval($ld_mon_aux);
								break;
						}
					 }										
						
					 if ($ls_estsol=="C")
					 {
						foreach($aa_dt_spgcxp as $dt_cxpspg)
						{
							$ls_codestpro1 = $dt_cxpspg["codestpro1"];
							$ls_codestpro2 = $dt_cxpspg["codestpro2"];
							$ls_codestpro3 = $dt_cxpspg["codestpro3"];
							$ls_codestpro4 = $dt_cxpspg["codestpro4"];
							$ls_codestpro5 = $dt_cxpspg["codestpro5"];
							$ls_estcla     = $dt_cxpspg["estcla"];
							$ls_cuentaspg  = trim($dt_cxpspg["spg_cuenta"]);
							$ls_descripcion = $dt_cxpspg["descripcion"];
							$ld_monto_par   = $dt_cxpspg["monto"];
							$ls_programa    = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
							
							// Se envia el arreglo de detalles presupuestarios
							$arrDetalleSpg["codestpro"][$li_cont_dtspg] = $ls_programa;
							$arrDetalleSpg["spgcuenta"][$li_cont_dtspg] = $ls_cuentaspg;
							$arrDetalleSpg["documento"][$li_cont_dtspg] = $ls_numsol;
							$arrDetalleSpg["desmov"][$li_cont_dtspg]    = $ls_descripcion;
							$arrDetalleSpg["procede_doc"][$li_cont_dtspg] = 'CXPSOP';
							$arrDetalleSpg["monto"][$li_cont_dtspg] = $ld_monto_par;
							$arrDetalleSpg["operacion"][$li_cont_dtspg] = 'PG';
							$arrDetalleSpg["estcla"][$li_cont_dtspg] = $ls_estcla;
							$arrDetalleSpg["codfuefin"][$li_cont_dtspg] = "";
							$li_cont_dtspg++;
						}
					 }
					 else
					 {
						if($ldec_monto<$ld_totpre)
						{
							foreach($aa_dt_spgcxp as $dt_cxpspg)
							{
								$ls_codestpro1 = $dt_cxpspg["codestpro1"];
								$ls_codestpro2 = $dt_cxpspg["codestpro2"];
								$ls_codestpro3 = $dt_cxpspg["codestpro3"];
								$ls_codestpro4 = $dt_cxpspg["codestpro4"];
								$ls_codestpro5 = $dt_cxpspg["codestpro5"];
								$ls_estcla     = $dt_cxpspg["estcla"];
								$ls_cuentaspg  = trim($dt_cxpspg["spg_cuenta"]);
								$ls_descripcion = $dt_cxpspg["descripcion"];
								$ld_monto_par   = $dt_cxpspg["monto"];
								$ld_monto_spg   = round(round($ld_monto_par , 2 ) *($ldec_monto  / $ld_totpre),2);
								$ls_programa    = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
								
								// Se envia el arreglo de detalles presupuestarios
								$arrDetalleSpg["codestpro"][$li_cont_dtspg] = $ls_programa;
								$arrDetalleSpg["spgcuenta"][$li_cont_dtspg] = $ls_cuentaspg;
								$arrDetalleSpg["documento"][$li_cont_dtspg] = $ls_numsol;
								$arrDetalleSpg["desmov"][$li_cont_dtspg]    = $ls_descripcion;
								$arrDetalleSpg["procede_doc"][$li_cont_dtspg] = 'CXPSOP';
								$arrDetalleSpg["monto"][$li_cont_dtspg] = $ld_monto_spg;
								$arrDetalleSpg["operacion"][$li_cont_dtspg] = 'PG';
								$arrDetalleSpg["estcla"][$li_cont_dtspg] = $ls_estcla;
								$arrDetalleSpg["codfuefin"][$li_cont_dtspg] = "";
								$li_cont_dtspg++;
							}
						 }
						 elseif ($ldec_monto>=$ld_totpre)
						 {
							foreach($aa_dt_spgcxp as $dt_cxpspg)
							{
								$ls_codestpro1 = $dt_cxpspg["codestpro1"];
								$ls_codestpro2 = $dt_cxpspg["codestpro2"];
								$ls_codestpro3 = $dt_cxpspg["codestpro3"];
								$ls_codestpro4 = $dt_cxpspg["codestpro4"];
								$ls_codestpro5 = $dt_cxpspg["codestpro5"];
								$ls_estcla     = $dt_cxpspg["estcla"];
								$ls_cuentaspg  = trim($dt_cxpspg["spg_cuenta"]);
								$ls_descripcion = $dt_cxpspg["descripcion"];
								$ld_monto_spg   = $dt_cxpspg["monto"];
								$ls_programa    = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
								if($ld_monto_spg>0)
								{
									// Se envia el arreglo de detalles presupuestarios
									$arrDetalleSpg["codestpro"][$li_cont_dtspg] = $ls_programa;
									$arrDetalleSpg["spgcuenta"][$li_cont_dtspg] = $ls_cuentaspg;
									$arrDetalleSpg["documento"][$li_cont_dtspg] = $ls_numsol;
									$arrDetalleSpg["desmov"][$li_cont_dtspg]    = $ls_descripcion;
									$arrDetalleSpg["procede_doc"][$li_cont_dtspg] = 'CXPSOP';
									$arrDetalleSpg["monto"][$li_cont_dtspg] = $ld_monto_spg;
									$arrDetalleSpg["operacion"][$li_cont_dtspg] = 'PG';
									$arrDetalleSpg["estcla"][$li_cont_dtspg] = $ls_estcla;
									$arrDetalleSpg["codfuefin"][$li_cont_dtspg] = "";
									$li_cont_dtspg++;
								}
								else
								{
									$lb_valido = true;
								}
							}
						 }
					 }
					 unset($aa_dt_spgcxp);
				}
				
				$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]	= $ls_ctaprovbene;
				$arrDetalleScg["procede_doc"][$li_cont_dtscg] 	= 'CXPSOP';
				$arrDetalleScg["desmov"][$li_cont_dtscg]	 	= $concepto;
				$arrDetalleScg["documento"][$li_cont_dtscg]	 	= $ls_numsol;
				$arrDetalleScg["debhab"][$li_cont_dtscg]	 	= 'D';
				$arrDetalleScg["monto"][$li_cont_dtscg]	 	    = $ldec_monto;
				$arrDetalleScg["monobjret"][$li_cont_dtscg]	 	= $monobjret;
				$arrDetalleScg["codded"][$li_cont_dtscg]	 	= '00000';

				if ($li_cont==$li_count_emich)
				{
					$li_cont_dtscg++;
					$arrDetalleScg["scg_cuenta"][$li_cont_dtscg]  = $ls_sccuenta;
					$arrDetalleScg["procede_doc"][$li_cont_dtscg] = 'SCBBCH';
					$arrDetalleScg["desmov"][$li_cont_dtscg]	  = $concepto;
					$arrDetalleScg["documento"][$li_cont_dtscg]	  = $numdoc;
					$arrDetalleScg["debhab"][$li_cont_dtscg]	  = 'H';
					$arrDetalleScg["monto"][$li_cont_dtscg]	 	  = $ldec_montotot;
					$arrDetalleScg["monobjret"][$li_cont_dtscg]	  = $monobjret;
					$arrDetalleScg["codded"][$li_cont_dtscg]	  = '00000';
				}
				else
				{
					$li_cont_dtscg++;
				}			
				if($lb_valido)
				{
					$li_origen=$this->uf_validar_monto_cancelado($ls_numsol,$arrCabeceraScb["numdoc"],$arrCabeceraScb["codban"],$arrCabeceraScb["ctaban"],$ldec_monto);
					if($li_origen==1)
					{
						$lb_valido=false;
						$this->mensaje .=  'La solicitud de pago '.$ls_numsol.' ya ha sido cancelada en su totalidad.';
						break;
					}
					elseif($li_origen==2)
					{
						$lb_valido=false;
						$this->mensaje .=  'El pago excede el monto estipulado en la solicitud de pago '.$ls_numsol;
						break;
					}
				}
			}
		}
		
		if ($lb_valido)
		{
			// Llamado al Servicio de Movimientos Bancarios
			$servicioBancario = new ServicioMovimientoScb();
			$lb_valido = $servicioBancario->GuardarAutomatico($arrCabeceraScb,$arrDetalleScg,$arrDetalleSpg,$arrDetalleSpi,$arrevento);
			$this->mensaje.= $servicioBancario->mensaje;
			// Llamado al Servicio de Movimientos Bancarios
		}
		if ($lb_valido)
		{												
			for($i=0;$i<$li_count_emich;$i++)
			{
				$ls_numsol   		 = $arremisionch[$i]->numsolicitud;
				$ldec_montopendiente = $arremisionch[$i]->montopendiente;
				$ldec_montopendiente = str_replace(".","",$ldec_montopendiente);
				$ldec_montopendiente = str_replace(",",".",$ldec_montopendiente);
				$ldec_monto			 = $arremisionch[$i]->montop;
				$ldec_monto			 = str_replace(".","",$ldec_monto);
				$ldec_monto			 = str_replace(",",".",$ldec_monto);
				$ls_numsolp			 = $ls_numsol;
				$ldec_montop		 = $ldec_monto;
				if ($ldec_montopendiente==$ldec_monto)
				{
					$ls_estsol='C';	//Cancelado							
				}
				else
				{
					$ls_estsol='P';//Programado
				}
				$ls_estsolp			= $ls_estsol;
				$lb_valido=$this->procesar_emision_chq($codban,$ctaban,$numdoc,$codope,$ls_numsolp,$estmov,$ldec_montop,$ls_estsolp,$arrevento);
			}
		}
		if (DaoGenerico::completarTrans($lb_valido)) 
		{
			$resultado = 1;
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento();
			$lb_valido=true; 		
		}
		else
		{
			$arrevento ['desevetra'] = $this->mensaje;
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
			$lb_valido=false;
		}  
		//liberando variables y retornando el resultado de la operacion
		//unset($this->daoEmisionChq);	
		return $lb_valido;
	}
}
?>