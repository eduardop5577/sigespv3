<?php
/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
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
require_once ($dirsrvscb."/modelo/servicio/scb/sigesp_srv_scb_iprogpago.php");
require_once ($dirsrvscb."/modelo/servicio/sss/sigesp_srv_sss_evento.php");


class servicioBanco implements iprogpago 
{
	private $daoBanco;
	private $daoProgramacion;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
	{
		$this->daoBanco = null;
		$this->daoProgramacion = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarBancos() {
		$this->daoBanco = FabricaDao::CrearDAO("N", "scb_banco");
		$databan = $this->daoBanco->leerTodos('codban',1,'');
		unset($this->daoBanco);
		return $databan;
	}
	
	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarCtasBancarias($codban,$ctaban,$denctaban) {
		$ls_gestor = $_SESSION["ls_gestor"];
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		
		 $ls_sql_seguridad = $conexionbd->Concat('scb_ctabanco.codban',"'-'",'scb_ctabanco.ctaban');
		 
		$cadenasql = "	SELECT 	scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta, ".
				   	 " 			scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban, ".
				   	 " 			scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr, ".
				     " 			scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact, scg_cuentas.status as status ".
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
				     " 	AND trim(sss_permisos_internos.codintper)=trim(".$ls_sql_seguridad.") ".
					 "	GROUP BY scb_ctabanco.ctaban, scb_ctabanco.dencta, scb_ctabanco.sc_cuenta, scg_cuentas.denominacion, ". 
					 "	scb_ctabanco.codban, scb_banco.nomban, scb_ctabanco.codtipcta, scb_tipocuenta.nomtipcta, ".
					 "	scb_ctabanco.fecapr, scb_ctabanco.feccie, scb_ctabanco.estact, scg_cuentas.status ".
				     " 	ORDER BY codban ASC ";
	//	echo $cadenasql;
	//	break;		   
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
	
	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarCtasBancariasTransf($codbandes,$ctaban,$denctaban) {
		$ls_gestor = $_SESSION["ls_gestor"];
		 if ((strtoupper($ls_gestor) == "MYSQLT") ||  (strtoupper($ls_gestor) == "MYSQLI"))
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
				     " 	AND scb_ctabanco.codban like '%".$codbandes."%' ".  
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
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarSaldoCtaban($codban,$ctaban)
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
		return number_format($ldec_saldo,2,",",".");
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarSolicitudes($tipproben,$tipvia)
	{
	  $arrSolicitudes=array();
	  $ls_cadaux = "";
	  if ($tipproben=='P')
		 {
		   $ls_tabla   = 'rpc_proveedor ';
		   $ls_columna = 'nompro as nomproben';
		   $ls_campo   = 'cod_pro ';
		   $ls_aux     = 'AND rpc_proveedor.codemp=cxp_solicitudes.codemp AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro';
		 }
	  elseif($tipproben=='B')
		 {
		   $ls_tabla   = 'rpc_beneficiario';
		   $ls_columna = "nombene, rpc_beneficiario.apebene";
		   $ls_campo   = 'ced_bene ';
		   $ls_aux     = 'AND rpc_beneficiario.codemp=cxp_solicitudes.codemp AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene';
		   if ($tipvia=='1')
			  {
			    $ls_cadaux = " AND cxp_rd.procede='SCVSOV' ";
			  } 
		   else
			  {
			    $ls_cadaux = " AND cxp_rd.procede<>'SCVSOV' "; 
			  } 
		 }																		   
	    //Condición despues de $ls_tabla ---->   cxp_rd.procede as procede, !!!No Eliminar!!!
		$cadenasql = "SELECT DISTINCT cxp_solicitudes.numsol as numsol,
								 cxp_solicitudes.$ls_campo as codproben,
								 cxp_solicitudes.fecemisol as fecemisol,
								 cxp_solicitudes.tipproben as tipproben,
								 cxp_solicitudes.fecpagsol as fecpagsol,
								 cxp_solicitudes.consol as consol,
								 cxp_solicitudes.estprosol as estprosol,
								 cxp_solicitudes.monsol as monsol,
								 cxp_solicitudes.obssol as obssol,
								 $ls_tabla.$ls_columna,
								 cxp_solicitudes.numordpagmin,
								 cxp_solicitudes.codtipfon
					        FROM cxp_solicitudes, $ls_tabla, cxp_rd, cxp_dt_solicitudes
						   WHERE cxp_solicitudes.codemp='".$_SESSION["la_empresa"]["codemp"]."'
						     AND cxp_solicitudes.tipproben='".$tipproben."'
							 AND cxp_solicitudes.estprosol='C' $ls_cadaux
							 AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
							 AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
							 AND cxp_rd.codemp=cxp_dt_solicitudes.codemp
							 AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc
							 AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc
							 AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro
							 AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene $ls_aux
							 AND cxp_solicitudes.numsol NOT IN (SELECT numsol
																  FROM scb_prog_pago
																 WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'
																   AND numsol=cxp_solicitudes.numsol)";				
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
				$ld_montotsal = $ld_montotnot = 0;
			    $ls_numsol    = $resultado->fields["numsol"];
				$ls_codproben = $resultado->fields["codproben"];
				$ld_monsolpag = $resultado->fields["monsol"];
				$ls_fecpagsol = $resultado->fields["fecpagsol"];
				$this->uf_load_notas_asociadas($ls_codemp,$ls_numsol,$ld_montotnot);
				$ld_montotsal = $ld_monsolpag+$ld_montotnot;
				$ls_fecemisol = $resultado->fields["fecemisol"];
				$ls_codproben = trim($resultado->fields["codproben"]);
				if ($tipproben=='P')
				{
					$ls_nomproben = $resultado->fields["nomproben"];
				}
			    elseif($tipproben=='B')
				{
				 	$ls_nomproben = $resultado->fields["nombene"];
				 	$ls_apeben    = $resultado->fields["apebene"];
				 	if (!empty($ls_apeben))
				    {
				        $ls_nomproben = $ls_nomproben.', '.$ls_apeben;
				    } 								   
				}
				$arrSolicitudes[$j]['numsol']     = $ls_numsol;
				$arrSolicitudes[$j]['monsol']     = number_format($ld_monsolpag,2,",",".");
				$arrSolicitudes[$j]['saldo']      = number_format($ld_montotsal,2,",",".");
				$arrSolicitudes[$j]['fecemisol']  = $ls_fecemisol;
				$arrSolicitudes[$j]['codproben']  = $ls_nomproben;
				$j++;
				$resultado->MoveNext();
			}
		}
		unset($conexionbd);
		unset($resultado);
		return $arrSolicitudes;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function uf_load_notas_asociadas($as_codemp,$as_numsol,$ai_montonotas)
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

	
	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()*/
	public function insertarProgramacion($numsol,$fechaprog,$estmov,$codban,$ctaban,$provee_benef,$tipproben,$tipvia,$arrevento)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:	uf_procesar_programacion
		// Access:		public
		//	Returns:	Boolean Retorna si proceso correctamente
		//	Description:	Funcion que se encarga de guardar el movimiento bien sea 
		//						insertando o actualizando
		//////////////////////////////////////////////////////////////////////////////
		$resultado = 0;
		$ls_codemp   = $_SESSION["la_empresa"]["codemp"];
	    $ls_codusu   = $_SESSION["la_logusr"];			 
		//Creación del Dao con la tabla y campos a insertar.
		$this->daoProgramacion=FabricaDao::CrearDAO("N","scb_prog_pago");
		$this->daoProgramacion->codemp       = $ls_codemp;
		$this->daoProgramacion->codban    	 = $codban; 
		$this->daoProgramacion->ctaban       = $ctaban; 
		$this->daoProgramacion->codusu       = $ls_codusu; 
		$this->daoProgramacion->numsol       = $numsol; 
		$this->daoProgramacion->fecpropag    = convertirFechaBd($fechaprog); 
		$this->daoProgramacion->estmov       = $estmov; 
		$this->daoProgramacion->esttipvia    = $tipvia; 
		//Iniciar transacción
		DaoGenerico::iniciarTrans();
		//Insertamos la programación de pago
		$resultado = $this->daoProgramacion->incluir();
		
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
			$strPk = "codemp='{$ls_codemp}' AND numsol='{$numsol}'";
			$this->daoProgramacion = FabricaDao::CrearDAO("C", "cxp_solicitudes", '', $strPk);
			$this->daoProgramacion->estprosol = 'S' ;
			$resultado = $this->daoProgramacion->modificar();
			
			if ($resultado)
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
			}
			else
			{
				$arrevento ['desevetra'] = $this->daoProgramacion->ErrorMsg();
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
			}
			
			$this->daoProgramacion=FabricaDao::CrearDAO("N","cxp_historico_solicitud");
			$this->daoProgramacion->codemp       = $ls_codemp;
			$this->daoProgramacion->numsol       = $numsol; 
			$this->daoProgramacion->fecha    = convertirFechaBd($fechaprog); 
			$this->daoProgramacion->estprodoc    = 'S';
			$resultado = $this->daoProgramacion->incluir();
			if ($resultado)
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
			}
			else
			{
				$arrevento ['desevetra'] = $this->daoProgramacion->ErrorMsg();
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
			}
			
			 
		}
		else
		{
			$arrevento ['desevetra'] = $this->daoProgramacion->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		}
		
		if (DaoGenerico::completarTrans()) 
		{
			$resultado = 1;
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento(); 		
		}
		else
		{
			$arrevento ['desevetra'] = $this->daoProgramacion->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
		}  
	//liberando variables y retornando el resultado de la operacion
	unset($this->daoProgramacion);	
    return $resultado;	
		
	}

}
?>