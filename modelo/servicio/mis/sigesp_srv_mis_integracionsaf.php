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

$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionsaf.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");

class servicioIntegracionSAF implements IIntegracionSAF 
{

	public  $mensaje; 
	public  $valido; 
	public  $conexionBaseDatos; 
	
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->contintmovban=$_SESSION['la_empresa']['contintmovban'];
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}

	/* (non-PHPdoc)
    * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
    */									
	public function buscarContabilizarDepSaf($mes,$anio,$estatus)
	{
        $arrContSafDep=array();
	    $lb_valido=true;
		$ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_criterio="";
		$ls_mes_letra="";

		if ($mes=="01"){
		$ls_mes_letra="ENERO";
		}
		if ($mes=="02"){
		$ls_mes_letra="FEBRERO";
		}
		if ($mes=="03"){
		$ls_mes_letra="MARZO";
		}
		if ($mes=="04"){
		$ls_mes_letra="ABRIL";
		}
		if ($mes=="05"){
		$ls_mes_letra="MAYO";
		}
		if ($mes=="06"){
		$ls_mes_letra="JUNIO";
		}
		if ($mes=="07"){
		$ls_mes_letra="JULIO";
		}
		if ($mes=="08"){
		$ls_mes_letra="AGOSTO";
		}
		if ($mes=="09"){
		$ls_mes_letra="SEPTIEMBRE";
		}
		if ($mes=="10"){
		$ls_mes_letra="OCTUBRE";
		}
		if ($mes=="11"){
		$ls_mes_letra="NOVIEMBRE";
		}
		if ($mes=="12"){
		$ls_mes_letra="DICIEMBRE";
		}
		
		if(!empty($mes))
		{
			if($mes!="00")
			{
				$ls_criterio=$ls_criterio." AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) = '".$mes."' ";
			}
		}
		if(!empty($anio))
		{
			$ls_criterio=$ls_criterio." AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) = '".$anio."'";
		}
		$cadenasql="SELECT SUM(saf_depreciacion.mondepmen) AS monto, SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) AS anio,".
				"		SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) AS mes, 'I' AS estact,".
				"		MAX(saf_depreciacion.fechaconta) AS fechaconta, MAX(saf_depreciacion.fechaanula) AS fechaanula  ".
                " FROM saf_depreciacion, saf_activo, saf_dta".
                " WHERE saf_depreciacion.codemp = '".$ls_codemp."'".
				"   AND saf_depreciacion.estcon = ".$estatus.
				"   AND saf_activo.estdepact = 1".
				"	AND saf_dta.estact <> 'D'".				
				"  ".$ls_criterio.
				"   AND saf_depreciacion.codemp = saf_activo.codemp".
				"   AND saf_depreciacion.codact = saf_activo.codact".
				"   AND saf_depreciacion.codemp = saf_dta.codemp".
				"   AND saf_depreciacion.codact = saf_dta.codact".
				"   AND saf_depreciacion.ideact = saf_dta.ideact".
				" GROUP BY SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4),SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) ".
				" UNION ".
				"SELECT SUM((saf_depreciacion.mondepmen/30)*CAST(SUBSTR(CAST(saf_dta.fecdesact as char(10)),8,2) AS INT)) AS monto, SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) AS anio,".
				"		SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) AS mes, 'D' AS estact,".
				"		MAX(saf_depreciacion.fechaconta) AS fechaconta, MAX(saf_depreciacion.fechaanula) AS fechaanula".
                " FROM saf_depreciacion, saf_activo, saf_dta".
                " WHERE saf_depreciacion.codemp = '".$ls_codemp."'".
				"   AND saf_depreciacion.estcon = ".$estatus.
				"   AND saf_activo.estdepact = 1".
				"	AND saf_dta.estact = 'D'".				
				"  ".$ls_criterio.
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) = SUBSTR(CAST(saf_dta.fecdesact as char(10)),6,2)".
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) = SUBSTR(CAST(saf_dta.fecdesact as char(10)),1,4)".
				"   AND saf_depreciacion.codemp = saf_activo.codemp".
				"   AND saf_depreciacion.codact = saf_activo.codact".
				"   AND saf_depreciacion.codemp = saf_dta.codemp".
				"   AND saf_depreciacion.codact = saf_dta.codact".
				"   AND saf_depreciacion.ideact = saf_dta.ideact".
				" GROUP BY SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4),SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) ".
				" UNION ".
				"SELECT SUM(saf_depreciacion.mondepmen) AS monto, SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) AS anio,".
				"		SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) AS mes, 'D' AS estact,".
				"		MAX(saf_depreciacion.fechaconta) AS fechaconta, MAX(saf_depreciacion.fechaanula) AS fechaanula".
                " FROM saf_depreciacion, saf_activo, saf_dta".
                " WHERE saf_depreciacion.codemp = '".$ls_codemp."'".
				"   AND saf_depreciacion.estcon = ".$estatus.
				"   AND saf_activo.estdepact = 1".
				"	AND saf_dta.estact = 'D'".				
				"  ".$ls_criterio.
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) < SUBSTR(CAST(saf_dta.fecdesact as char(10)),6,2) ".
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) <= SUBSTR(CAST(saf_dta.fecdesact as char(10)),1,4) ".
				"   AND saf_depreciacion.codemp = saf_activo.codemp".
				"   AND saf_depreciacion.codact = saf_activo.codact".
				"   AND saf_depreciacion.codemp = saf_dta.codemp".
				"   AND saf_depreciacion.codact = saf_dta.codact".
				"   AND saf_depreciacion.ideact = saf_dta.ideact".
				" GROUP BY SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4),SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) ".
				" ORDER BY mes";
			//echo $cadenasql."\n";
			$conexionbd = ConexionBaseDatos::getInstanciaConexion();
			$resultado = $conexionbd->Execute ( $cadenasql );
			if ($resultado===false)
			{
				$mensaje .= '  ->'.$conexionbd->ErrorMsg();
			}
			else
			{
				$j=0;
					$li_total=0;
				while((!$resultado->EOF))
				{
					$ls_mes 		  = trim($resultado->fields["mes"]);
					$ls_anio 		  = trim($resultado->fields["anio"]);
					$li_monto		  = $resultado->fields["monto"];
					$ls_estact   	  = $resultado->fields["estact"];
					$ld_fechaconta	  = $resultado->fields["fechaconta"];
					$ld_fechaanula 	  = $resultado->fields["fechaanula"];
					$ls_comprobante	  = str_pad($mes.$anio,15,"0",0);
					$ls_descripcion   ="DEPRECIACION DE LOS ACTIVOS FIJOS CORRESPONDIENTES AL ANO ".$anio." MES DE ".$ls_mes_letra;					 
					$li_total=$li_total+$li_monto;
				    if ($li_total>0)
					{
						$li_total=number_format($li_total,2,",",".");
						$arrContSafDep[$j]['monto']        = $li_total;
						$arrContSafDep[$j]['anio']         = $ls_anio;
						$arrContSafDep[$j]['mes']          = $ls_mes;
						$arrContSafDep[$j]['fechaconta']   = $ld_fechaconta;
						$arrContSafDep[$j]['fechanula']    = $ld_fechaanula;
						$arrContSafDep[$j]['descripcion']  = $ls_descripcion;
						$arrContSafDep[$j]['comprobante']  = $ls_comprobante;
						$j++;
					}
				    $resultado->MoveNext();
				}
			}
	unset($conexionbd);
	unset($resultado);
	return $arrContSafDep;
	}
   //----------------------------------------------------------------------------------------------------------------------------
	/* (non-PHPdoc)
    * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
    */									
	public function buscarRevContabilizacionDepSaf($mes,$anio,$estatus)
	{
        $arrContSafDep=array();
	    $lb_valido=true;
		$ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_criterio="";
		$ls_mes_letra="";

		if ($mes=="01"){
		$ls_mes_letra="ENERO";
		}
		if ($mes=="02"){
		$ls_mes_letra="FEBRERO";
		}
		if ($mes=="03"){
		$ls_mes_letra="MARZO";
		}
		if ($mes=="04"){
		$ls_mes_letra="ABRIL";
		}
		if ($mes=="05"){
		$ls_mes_letra="MAYO";
		}
		if ($mes=="06"){
		$ls_mes_letra="JUNIO";
		}
		if ($mes=="07"){
		$ls_mes_letra="JULIO";
		}
		if ($mes=="08"){
		$ls_mes_letra="AGOSTO";
		}
		if ($mes=="09"){
		$ls_mes_letra="SEPTIEMBRE";
		}
		if ($mes=="10"){
		$ls_mes_letra="OCTUBRE";
		}
		if ($mes=="11"){
		$ls_mes_letra="NOVIEMBRE";
		}
		if ($mes=="12"){
		$ls_mes_letra="DICIEMBRE";
		}
		
		if(!empty($mes))
		{
			if($mes!="00")
			{
				$ls_criterio=$ls_criterio." AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) = '".$mes."'";
			}
		}
		if(!empty($anio))
		{
			$ls_criterio=$ls_criterio." AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) = '".$anio."'";
		}
		$cadenasql="SELECT SUM(saf_depreciacion.mondepmen) AS monto, SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) AS anio, ".
				"		SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) AS mes, 'I' AS estact,".
				"		MAX(saf_depreciacion.fechaconta) AS fechaconta, MAX(saf_depreciacion.fechaanula) AS fechaanula".
                " FROM saf_depreciacion, saf_activo, saf_dta".
                " WHERE saf_depreciacion.codemp = '".$ls_codemp."'".
				"   AND saf_depreciacion.estcon = ".$estatus.
				"   AND saf_activo.estdepact = 1 ".
				"	AND saf_dta.estact <> 'D'".				
				"  ".$ls_criterio.
				"   AND saf_depreciacion.codemp = saf_activo.codemp".
				"   AND saf_depreciacion.codact = saf_activo.codact".
				"   AND saf_depreciacion.codemp = saf_dta.codemp".
				"   AND saf_depreciacion.codact = saf_dta.codact".
				"   AND saf_depreciacion.ideact = saf_dta.ideact".
				" GROUP BY SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4),SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) ".
				" UNION ".
				"SELECT SUM((saf_depreciacion.mondepmen/30)*CAST(SUBSTR(CAST(saf_dta.fecdesact as char(10)),8,2) AS INT)) AS monto, SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) AS anio,".
				"		SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) AS mes, 'D' AS estact,".
				"		MAX(saf_depreciacion.fechaconta) AS fechaconta, MAX(saf_depreciacion.fechaanula) AS fechaanula".
                " FROM saf_depreciacion, saf_activo, saf_dta".
                " WHERE saf_depreciacion.codemp = '".$ls_codemp."'".
				"   AND saf_depreciacion.estcon = ".$estatus.
				"   AND saf_activo.estdepact = 1".
				"	AND saf_dta.estact = 'D'".				
				"  ".$ls_criterio.
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) = SUBSTR(CAST(saf_dta.fecdesact as char(10)),6,2) ".
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) = SUBSTR(CAST(saf_dta.fecdesact as char(10)),1,4) ".
				"   AND saf_depreciacion.codemp = saf_activo.codemp".
				"   AND saf_depreciacion.codact = saf_activo.codact".
				"   AND saf_depreciacion.codemp = saf_dta.codemp".
				"   AND saf_depreciacion.codact = saf_dta.codact".
				"   AND saf_depreciacion.ideact = saf_dta.ideact".
				" GROUP BY SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4),SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) ".
				" UNION ".
				"SELECT SUM(saf_depreciacion.mondepmen) AS monto, SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) AS anio, ".
				"		SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) AS mes, 'D' AS estact,".
				"		MAX(saf_depreciacion.fechaconta) AS fechaconta, MAX(saf_depreciacion.fechaanula) AS fechaanula".
                " FROM saf_depreciacion, saf_activo, saf_dta".
                " WHERE saf_depreciacion.codemp = '".$ls_codemp."'".
				"   AND saf_depreciacion.estcon = ".$estatus.
				"   AND saf_activo.estdepact = 1".
				"	AND saf_dta.estact = 'D'".				
				"  ".$ls_criterio.
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) < SUBSTR(CAST(saf_dta.fecdesact as char(10)),6,2) ".
				"   AND SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4) <= SUBSTR(CAST(saf_dta.fecdesact as char(10)),1,4) ".
				"   AND saf_depreciacion.codemp = saf_activo.codemp".
				"   AND saf_depreciacion.codact = saf_activo.codact".
				"   AND saf_depreciacion.codemp = saf_dta.codemp".
				"   AND saf_depreciacion.codact = saf_dta.codact".
				"   AND saf_depreciacion.ideact = saf_dta.ideact".
				" GROUP BY SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),1,4),SUBSTR(CAST(saf_depreciacion.fecdep as char(10)),6,2) ".
				" ORDER BY mes";
			//echo $cadenasql."\n";
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
					$ls_mes 		  = trim($resultado->fields["mes"]);
					$ls_anio 		  = trim($resultado->fields["anio"]);
					$li_monto		  = $resultado->fields["monto"];
					$ls_estact   	  = $resultado->fields["estact"];
					$ld_fechaconta	  = $resultado->fields["fechaconta"];
					$ld_fechaanula 	  = $resultado->fields["fechaanula"];
					$ls_comprobante	  = str_pad($mes.$anio,15,"0",0);
					$ls_descripcion   ="DEPRECIACION DE LOS ACTIVOS FIJOS CORRESPONDIENTES AL ANO ".$anio." MES DE ".$ls_mes_letra;					 
					$li_total=0;
					$li_total=$li_total+$li_monto;
					$li_total=number_format($li_total,2,",",".");
				   
				    $arrContSafDep[$j]['monto']        = $li_total;
				    $arrContSafDep[$j]['anio']         = $ls_anio;
				    $arrContSafDep[$j]['mes']          = $ls_mes;
				    $arrContSafDep[$j]['fechaconta']   = $ld_fechaconta;
				    $arrContSafDep[$j]['fechanula']    = $ld_fechaanula;
				    $arrContSafDep[$j]['descripcion']  = $ls_descripcion;
				    $arrContSafDep[$j]['comprobante']  = $ls_comprobante;
				    $j++;
				    $resultado->MoveNext();
				}
			}
	unset($conexionbd);
	unset($resultado);
	return $arrContSafDep;
	}
   //----------------------------------------------------------------------------------------------------------------------------
 	/* (non-PHPdoc)
    * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
    */									
	public function buscarContabilizarDesSaf($numcmp,$feccmp,$estatus)
	{
        $arrContSafDes=array();
	    $lb_valido=true;
		$ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_criterio="";
		if(!empty($numcmp))
		{
			$ls_criterio=$ls_criterio." AND numcmp like '%".$numcmp."%'";
		}
		if(!empty($as_fecdoc))
		{
			$feccmp=convertirFechaBd($feccmp);
			$ls_criterio=$ls_criterio." AND feccmp = '".$feccmp."'";
		}
		
		$cadenasql="SELECT cmpmov, numcmp, feccmp, descmp ".
                "  FROM saf_movimiento ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND estint=".$estatus." ".
				"   AND tipcmp = 'DE' ".$ls_criterio;
			//echo $cadenasql."\n";
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
				$ls_numdoc = trim($resultado->fields["numcmp"]);
				$ls_numcmp = trim($resultado->fields["cmpmov"]);
				$ls_fecmov = convertirFecha($resultado->fields["feccmp"]);
				$ls_conmov = $resultado->fields["descmp"];
				
				$arrContSafDes[$j]['numcmp']  = $ls_numdoc;
				$arrContSafDes[$j]['cmpmov']  = $ls_numcmp;
				$arrContSafDes[$j]['feccmp']  = $ls_fecmov;
				$arrContSafDes[$j]['conmov']  = $ls_conmov;
				$j++;
				$resultado->MoveNext();
			}
		}
	unset($conexionbd);
	unset($resultado);
	return $arrContSafDes;
	}
   //----------------------------------------------------------------------------------------------------------------------------
/* (non-PHPdoc)
    * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
    */									
	public function buscarRevContabilizacionDesSaf($numcmp,$feccmp,$estatus)
	{
		$arrContSafDes=array();
	    $lb_valido=true;
		$ls_codemp=$_SESSION['la_empresa']['codemp'];
		$ls_criterio="";
		if(!empty($numcmp))
		{
			$ls_criterio=$ls_criterio." AND saf_movimiento.cmpmov like '%".$numcmp."%'";
		}
		if(!empty($feccmp))
		{
			$feccmp=convertirFechaBd($feccmp);
			$ls_criterio=$ls_criterio." AND saf_movimiento.feccmp = '".$feccmp."'";
		}
		
		$cadenasql="SELECT saf_movimiento.cmpmov,  saf_movimiento.numcmp, sigesp_cmp.fecha, saf_movimiento.feccmp, saf_movimiento.descmp ".
                "  FROM saf_movimiento ".
				"  INNER JOIN sigesp_cmp ON saf_movimiento.codemp=sigesp_cmp.codemp AND saf_movimiento.cmpmov=sigesp_cmp.comprobante".
				" WHERE saf_movimiento.codemp='".$ls_codemp."'".
				"   AND saf_movimiento.estint=1 ".
				"   AND sigesp_cmp.procede='SAFCDN'".
				"   AND sigesp_cmp.codban='---'".
				"   AND sigesp_cmp.ctaban='-------------------------'".
				"   AND saf_movimiento.tipcmp = 'DE' ".$ls_criterio;
			//echo $cadenasql."\n";
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
				$ls_numdoc = trim($resultado->fields["cmpmov"]);
				$ls_numcmp = trim($resultado->fields["numcmp"]);
				$ls_fecmov = convertirFecha($resultado->fields["feccmp"]);
				$ls_feccon = convertirFecha($resultado->fields["fecha"]);
				$ls_conmov = $resultado->fields["descmp"];
				
				$arrContSafDes[$j]['cmpmov']  = $ls_numdoc;
				$arrContSafDes[$j]['numcmp']  = $ls_numcmp;
				$arrContSafDes[$j]['feccmp']  = $ls_fecmov;
				$arrContSafDes[$j]['feccont'] = $ls_feccon;
				$arrContSafDes[$j]['conmov']  = $ls_conmov;
				$j++;
				$resultado->MoveNext();
			}
		}
	unset($conexionbd);
	unset($resultado);
	return $arrContSafDes;
	}
   //----------------------------------------------------------------------------------------------------------------------------

  /* (non-PHPdoc)
  * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
  */	
	public function uf_buscarconfiguracion()
	{
		$as_depreciacion="P";
        $ls_codemp=$_SESSION['la_empresa']['codemp'];
		$cadenasql="SELECT value FROM sigesp_config".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codsis='SAF'".
				"   AND seccion='DEPRECIACION'"; 
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		}
		else
		{
			if((!$resultado->EOF))
			{
				$as_depreciacion=$resultado->fields["value"];
			}
		}
	return $as_depreciacion;
	}
   //----------------------------------------------------------------------------------------------------------------------------

 /* (non-PHPdoc)
 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
 */									
	public function buscarDetalleGasto($arrcabecera,$as_ano,$as_mes)
	{
		$arregloSPG = null;
		$ls_documento = "1";								 
		$ls_documento = fillComprobante(trim($ls_documento));
		$cadenasql=" SELECT SUM(saf_depreciacion.mondepmen) AS monto, saf_activo.codestpro1, saf_activo.codestpro2, ".
				   "        saf_activo.codestpro3, saf_activo.codestpro4, saf_activo.codestpro5, saf_activo.spg_cuenta_dep, ".
				   "        saf_dta.fecdesact, saf_activo.estcla, saf_dta.estact ".
				   "   FROM saf_depreciacion, saf_activo, saf_dta ".
				   "  WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
				   "    AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_ano."'".
				   "  AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
				   "  AND (saf_dta.estact = 'I' OR saf_dta.estact = 'M' OR saf_dta.estact = 'D')".
				   "  AND saf_depreciacion.codemp = saf_activo.codemp".
				   "  AND saf_depreciacion.codact = saf_activo.codact".
				   "  AND saf_depreciacion.codemp = saf_dta.codemp".
				   "  AND saf_depreciacion.codact = saf_dta.codact".
				   "  AND saf_depreciacion.ideact = saf_dta.ideact".
				   "  GROUP BY saf_activo.codestpro1, saf_activo.codestpro2, saf_activo.codestpro3, saf_activo.codestpro4,".
				   "           saf_activo.codestpro5, saf_activo.spg_cuenta_dep, saf_activo.estcla ,saf_dta.fecdesact, saf_dta.estact ".
				   "  ORDER BY saf_activo.codestpro1, saf_activo.codestpro2, saf_activo.codestpro3, saf_activo.codestpro4,".
				   "           saf_activo.codestpro5, saf_activo.spg_cuenta_dep, saf_activo.estcla";
		
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			$anterior = "";
			while (!$data->EOF)
			{
				$monto = $data->fields["monto"];
				$total=0;
				$estact=$data->fields["estact"];
				$fecdesact=$data->fields["fecdesact"];
				if($estact=="D")
				{
					if($as_ano==substr($fecdesact,0,4))
					{
						if($as_mes==substr($fecdesact,5,2))
						{
							$dia=substr($fecdesact,8,2);
							$monto=($monto/30)*$dia;
							$total=$monto;
						}
						if($as_mes<substr($fecdesact,5,2))
						{
							$total=$monto;
						}
					}
					if($as_ano<substr($fecdesact,0,4))
					{
						$total=$monto;
					}
				}
				else
				{
					$total=$monto;
				}
				$actual = $data->fields['codestpro1'].$data->fields['codestpro2'].$data->fields['codestpro3'].$data->fields['codestpro4'].$data->fields['codestpro5'].$data->fields['estcla'].$data->fields['spg_cuenta_dep'];
				if ($anterior <> $actual)
				{
					$anterior = $actual;
					$i++;
					$arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
					$arregloSPG[$i]['procede']= $arrcabecera['procede'];
					$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
					$arregloSPG[$i]['codban']= $arrcabecera['codban'];
					$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
					$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
					$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];
					$arregloSPG[$i]['codfuefin']=$arrcabecera['codfuefin'];
					$arregloSPG[$i]['orden']= $i;
					$arregloSPG[$i]['procede_doc']= 'SAFDPR';
					$arregloSPG[$i]['documento']= $ls_documento;
					$arregloSPG[$i]['codestpro1']=$data->fields['codestpro1'];
					$arregloSPG[$i]['codestpro2']=$data->fields['codestpro2'];
					$arregloSPG[$i]['codestpro3']=$data->fields['codestpro3'];
					$arregloSPG[$i]['codestpro4']=$data->fields['codestpro4'];
					$arregloSPG[$i]['codestpro5']=$data->fields['codestpro5'];
					$arregloSPG[$i]['estcla']=$data->fields['estcla'];
					$arregloSPG[$i]['spg_cuenta']=$data->fields['spg_cuenta_dep'];
					$arregloSPG[$i]['monto']=$total;
					$arregloSPG[$i]['mensaje']= "OCP";
				}
				else
				{
					$arregloSPG[$i]['monto']=number_format($arregloSPG[$i]['monto'] +$total,2,".","");
				}
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSPG;
	}
   //----------------------------------------------------------------------------------------------------------------------------
	public function buscarDetalleContable($arrcabecera,$anio,$mes,$as_depreciacion)
	{
		$arregloSCG = null;
		if($as_depreciacion!="C")
		{	
			$cadenasql="SELECT SUM(saf_depreciacion.mondepmen) AS monto, 'D' AS operacion, spg_cuentas.sc_cuenta ".
					"  FROM saf_depreciacion,saf_activo, spg_cuentas ".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."' ".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$anio."' ".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$mes."' ".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					"   AND saf_activo.codemp = spg_cuentas.codemp".
					"   AND saf_activo.codestpro1 = spg_cuentas.codestpro1".
					"   AND saf_activo.codestpro2 = spg_cuentas.codestpro2".
					"   AND saf_activo.codestpro3 = spg_cuentas.codestpro3".
					"   AND saf_activo.codestpro4 = spg_cuentas.codestpro4".
					"   AND saf_activo.codestpro5 = spg_cuentas.codestpro5".
					"   AND saf_activo.estcla = spg_cuentas.estcla".
					"   AND saf_activo.spg_cuenta_dep = spg_cuentas.spg_cuenta".
					" GROUP BY spg_cuentas.sc_cuenta ".
					" UNION ".
					"SELECT SUM(saf_depreciacion.mondepmen) AS monto, 'H' AS operacion, saf_activo.sc_cuenta".
					"  FROM saf_depreciacion,saf_activo, spg_ep1 ".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."' ".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$anio."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$mes."'".
					"   AND spg_ep1.estint=0".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					"   AND saf_activo.codemp=spg_ep1.codemp".
					"   AND saf_activo.codestpro1=spg_ep1.codestpro1".
					"   AND saf_activo.estcla=spg_ep1.estcla".
					" GROUP BY saf_activo.sc_cuenta".
					" UNION ".
					"SELECT SUM(saf_depreciacion.mondepmen) AS monto, 'H' AS operacion, trim(spg_ep1.sc_cuenta) as sc_cuenta".
					"  FROM saf_depreciacion,saf_activo, spg_ep1 ".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$anio."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$mes."'".
					"   AND spg_ep1.estint=1".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					"   AND saf_activo.codemp=spg_ep1.codemp".
					"   AND saf_activo.codestpro1=spg_ep1.codestpro1".
					"   AND saf_activo.estcla=spg_ep1.estcla".
					" GROUP BY spg_ep1.sc_cuenta";
		}
		else
		{
				$cadenasql="SELECT SUM(saf_depreciacion.mondepmen) AS monto,'D' AS operacion, saf_activo.spg_cuenta_dep AS sc_cuenta".
					"  FROM saf_depreciacion,saf_activo".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$anio."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$mes."'".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					" GROUP BY saf_activo.spg_cuenta_dep".
					" UNION ".
					"SELECT SUM(saf_depreciacion.mondepmen) AS monto, 'H' AS operacion, saf_activo.sc_cuenta".
					"  FROM saf_depreciacion,saf_activo".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$anio."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$mes."'".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					" GROUP BY saf_activo.sc_cuenta";
		}
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while (!$data->EOF)
			{
				$i++;
				$arregloSCG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSCG[$i]['procede']= $arrcabecera['procede'];
				$arregloSCG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSCG[$i]['codban']= $arrcabecera['codban'];
				$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSCG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSCG[$i]['orden']= $i;			
				$arregloSCG[$i]['sc_cuenta'] = $data->fields['sc_cuenta'];
				$arregloSCG[$i]['procede_doc'] = 'SAFDPR';
				if($data->fields['operacion']=="D")
				{				
					$ls_documento = "1";
					$ls_documento = fillComprobante(trim($ls_documento));								
				}
				else
				{
					$ls_documento = "2";
					$ls_documento = fillComprobante(trim($ls_documento));								
				}
				$arregloSCG[$i]['documento'] = $ls_documento;
				$arregloSCG[$i]['debhab'] = $data->fields['operacion'];
				$arregloSCG[$i]['monto'] = $data->fields['monto'];
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSCG;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function buscarDetalleContableDes($arrcabecera,$comprobante,$adt_fecha_mov,$conmov)
	{
		$arregloSCG = null;

		$cadenasql=" SELECT sc_cuenta, documento, debhab, monto ".
				   " FROM saf_contable ".
				   " WHERE codemp = '".$_SESSION['la_empresa']['codemp']."' AND".
				   " cmpmov = '".$comprobante."' AND".
				   " feccmp = '".$adt_fecha_mov."'";

		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while (!$data->EOF)
			{
				$i++;
				$arregloSCG[$i]['codemp']=$arrcabecera['codemp'];
				$arregloSCG[$i]['procede']= $arrcabecera['procede'];
				$arregloSCG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSCG[$i]['codban']= $arrcabecera['codban'];
				$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSCG[$i]['descripcion']= $arrcabecera['descripcion'];
				$arregloSCG[$i]['orden']= $i;			
				$arregloSCG[$i]['sc_cuenta'] = $data->fields['sc_cuenta'];
				$arregloSCG[$i]['procede_doc'] = 'SAFCDN';
				$ls_documento = $data->fields['documento'];
				$arregloSCG[$i]['documento'] = $ls_documento;
				$arregloSCG[$i]['debhab'] = $data->fields['debhab'];
				$arregloSCG[$i]['monto'] = $data->fields['monto'];
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSCG;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
    public function uf_update_estatus_desincorporacion($as_nummov,$as_fecmov,$ai_estatus) 
	{
    	$lb_valido=true;	
		$cadenasql="UPDATE saf_movimiento".
				"   SET estint=".$ai_estatus.
				" WHERE codemp = '".$_SESSION['la_empresa']['codemp']."'".
				"   AND cmpmov = '".$as_nummov."'".
				"   AND feccmp = '".$as_fecmov."'";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
			$lb_valido=false;
		}
		return $lb_valido;
    }
//-----------------------------------------------------------------------------------------------------------------------------------
	public function uf_update_fecha_estatus_contabilizado_saf($as_ano,$as_mes,$ad_fechaconta,$ad_fechaanula,$as_estatus)
	{
        $lb_valido=true;
		$cadenasql="UPDATE saf_depreciacion ".
				"   SET estcon=".$as_estatus.",".
				"   fechaconta='".$ad_fechaconta."',".
				"   fechaanula='".$ad_fechaanula."'".
				" WHERE codemp='".$_SESSION['la_empresa']['codemp']."'".
				"   AND SUBSTR(CAST(fecdep AS CHAR(10)),1,4)='".$as_ano."'".
				"   AND SUBSTR(CAST(fecdep AS CHAR(10)),6,2)='".$as_mes."'";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_update_fecha_contabilizado_saf
//-----------------------------------------------------------------------------------------------------------------------------------
	public function uf_procesar_detalles_contables_int($as_mes,$as_ano,$as_comprobante,$as_descripcion,$as_procede,$ad_fecha,$as_codban,$as_ctaban)
	{
	    $lb_valido=true;		
		$cadenasql = "	SELECT SUM(saf_depreciacion.mondepmen) AS monto, 'D' AS operacion, trim(spg_ep1.sc_cuenta) as sc_cuenta
				      FROM saf_depreciacion,saf_activo, spg_ep1
					 WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'
					   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_ano."'
					   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'
					   AND spg_ep1.estint=1
					   AND saf_depreciacion.codemp = saf_activo.codemp
					   AND saf_depreciacion.codact = saf_activo.codact
					   AND saf_activo.codemp=spg_ep1.codemp
					   AND saf_activo.codestpro1=spg_ep1.codestpro1
					   AND saf_activo.estcla=spg_ep1.estcla
					 GROUP BY spg_ep1.sc_cuenta
					 UNION
					SELECT SUM(saf_depreciacion.mondepmen) AS monto, 'H' AS operacion, saf_activo.sc_cuenta
					  FROM saf_depreciacion,saf_activo, spg_ep1
					 WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'
					   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_ano."'
					   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'
					   AND spg_ep1.estint=1
					   AND saf_depreciacion.codemp = saf_activo.codemp
					   AND saf_depreciacion.codact = saf_activo.codact
					   AND saf_activo.codemp=spg_ep1.codemp
					   AND saf_activo.codestpro1=spg_ep1.codestpro1
					   AND saf_activo.estcla=spg_ep1.estcla
					 GROUP BY saf_activo.sc_cuenta";
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
			$lb_valido=false;
		}
		else
		{           
		  $li_orden = 0;  
		  while((!$resultado->EOF))
		  {
			  $li_orden++;
			  $ld_monto  = $resultado->fields['monto'];
			  $ls_debhab = $resultado->fields['operacion'];
			  $ls_scgcta = trim($resultado->fields['sc_cuenta']);
			  if ($ls_debhab=="D")
			  {				
			  	$ls_numdoc = "1";								
			  }
			  else
			  {
				$ls_numdoc = "2";								
			  }
			  $ls_numdoc = fillComprobante(trim($ls_numdoc));

			  $cadenaSql =" INSERT INTO saf_depreciacion_int (codemp,procede,comprobante,fecha,codban,ctaban,sc_cuenta,procede_doc,documento,debhab,descripcion,monto,orden,estrepasi)".
					      " VALUES ('".$_SESSION['la_empresa']['codemp']."','".$as_procede."','".$as_comprobante."','".$ad_fecha."', ".
						  "			  '".$as_codban."','".$as_ctaban."','".$ls_scgcta."','".$as_procede."','".$ls_numdoc."',".
						  "			  '".$ls_debhab."','".$as_descripcion."',".$ld_monto.",".$li_orden.",0)";
			  $conexionbd  = ConexionBaseDatos::getInstanciaConexion();
			  $dataSet = $conexionbd->Execute ( $cadenaSql );
			  if ($dataSet===false)
			  {
				 $this->mensaje .= '  ->'.$conexionbd->ErrorMsg();
				 $lb_valido= false;
			  }
			  $resultado->MoveNext();
			}
		}
		return $lb_valido;
    } // end function uf_procesar_detalles_contables
//-----------------------------------------------------------------------------------------------------------------------------------
	public function procesoContabilizarDepSaf($objson) 
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrevento['codemp']    = $_SESSION['la_empresa']['codemp'];
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = $objson->codsis;
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = $objson->nomven;
		for($j=0;$j<=$nSol-1;$j++)
		{
			$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
			$arrevento['desevetra'] = "Contabilizo la Depreciaci&#243;n de Activo {$comprobante}, asociado a la empresa {$_SESSION['la_empresa']['codemp']}";
			if ($this->contabilizarDepSaf($comprobante,$objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Depreciaci&#243;n contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La depreciaci&#243;n no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function contabilizarDepSaf($comprobante,$objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$arrDetalle = $objson->arrDetalle[$j];
		$i = 0;
		$this->valido=true;
		$procede="SAFDPR";
		$ls_tipo_destino="-";
		$ls_codigo_destino="----------";
		$ld_fecha=ultimoDiaMes($arrDetalle->mes,$arrDetalle->anio);
		$adt_fecha=convertirFechaBd($ld_fecha);
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$as_depreciacion=$this->uf_buscarconfiguracion();
		if($this->valido)		
		{
			$arrcabecera['codemp'] = $_SESSION['la_empresa']['codemp'];
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = $ls_codban;
			$arrcabecera['ctaban'] = $ls_ctaban;
			$arrcabecera['fecha'] = $adt_fecha;
			$arrcabecera['descripcion'] = utf8_decode($arrDetalle->descripcion);
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $ls_tipo_destino;
			$arrcabecera['cod_pro'] = $ls_codigo_destino;
			$arrcabecera['ced_bene'] = $ls_codigo_destino;
			$arrcabecera['total'] = number_format($arrDetalle->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			if ($as_depreciacion!="C")
			{
				$arrdetallespg=$this->buscarDetalleGasto($arrcabecera,$arrDetalle->anio,$arrDetalle->mes);
			}
			if($this->valido)		
			{
				$arrdetallescg=$this->buscarDetalleContable($arrcabecera,$arrDetalle->anio,$arrDetalle->mes,$as_depreciacion);
			}
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if ($this->valido)
			{
				$this->valido = $this->uf_update_fecha_estatus_contabilizado_saf($arrDetalle->anio,$arrDetalle->mes,$adt_fecha,'1900-01-01',1);
			}
			if ($this->valido)
			{												
				$this->valido = $this->uf_procesar_detalles_contables_int($arrDetalle->mes,$arrDetalle->anio,$comprobante,
																		  $arrDetalle->descripcion,$procede,$adt_fecha,$ls_codban,$ls_ctaban);
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$arrevento['desevetra'] = 'Contabilizo la depreciaci&#243;n'.$comprobante.',asociado a la empresa'.$_SESSION['la_empresa']['codemp']; 
		$servicioEvento->desevetra=$arrevento['desevetra'];			
		if (DaoGenerico::completarTrans($this->valido)) 
		{
			$servicioEvento->incluirEvento();
			if($i==0){
				$this->mensaje .= 'La depreciaci&#243;n '.$comprobante.' del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', Fue contabilizado';
			}
			else{
				$this->mensaje .= ', '.$comprobante.'  del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', Fue contabilizado';
			}
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
			if($i==0){
				$this->mensaje .= 'La depreciaci&#243;n '.$comprobante.' del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', No fue contabilizado';
			}
			else{
				$this->mensaje .= ', '.$comprobante.'  del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', No fue contabilizado';
			}
		}
	unset($servicioEvento);
	return $this->valido;
    }
//-----------------------------------------------------------------------------------------------------------------------------------
	public function procesoRevContabilizarDepSaf($objson) 
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrevento['codemp']    = $_SESSION['la_empresa']['codemp'];
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = $objson->codsis;
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = $objson->nomven;
		for($j=0;$j<=$nSol-1;$j++)
		{
			$comprobante=fillComprobante($objson->arrDetalle[$j]->comprobante);
			$arrevento['desevetra'] = "Reverso la contabilizacion la Depreciaci&#243;n de Activo {$comprobante}, asociado a la empresa {$_SESSION['la_empresa']['codemp']}";
			if ($this->RevContabilizarDepSaf($comprobante,$objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Depreciaci&#243;n reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La depreciaci&#243;n no fue reversada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function RevContabilizarDepSaf($comprobante,$objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$arrDetalle = $objson->arrDetalle[$j];
		$i = 0;
		$this->valido=true;
		$this->valido=$this->load_depreciacion_int($comprobante);
		if ($this->valido)
		{
			$this->valido=$this->delete_depreciacion_int($comprobante);
		}
		$procede="SAFDPR";
		$ls_tipo_destino="-";
		$ls_codigo_destino="----------";
		$ld_fecha=ultimoDiaMes($arrDetalle->mes,$arrDetalle->anio);
		$adt_fecha=convertirFechaBd($ld_fecha);
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$as_depreciacion=$this->uf_buscarconfiguracion();
		if($this->valido)		
		{
			$arrcabecera['codemp'] = $_SESSION['la_empresa']['codemp'];
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = $ls_codban;
			$arrcabecera['ctaban'] = $ls_ctaban;
			$arrcabecera['fecha'] = $adt_fecha;
			$arrcabecera['descripcion'] = utf8_decode($arrDetalle->descripcion);
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $ls_tipo_destino;
			$arrcabecera['cod_pro'] = $ls_codigo_destino;
			$arrcabecera['ced_bene'] = $ls_codigo_destino;
			$arrcabecera['total'] = number_format($arrDetalle->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if ($this->valido)
			{
				$this->valido = $this->uf_update_fecha_estatus_contabilizado_saf($arrDetalle->anio,$arrDetalle->mes,'1900-01-01','1900-01-01',0);
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$arrevento['desevetra'] = 'Reverso la depreciaci&#243;n '.$comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp']; 
		$servicioEvento->desevetra=$arrevento['desevetra'];			
		if (DaoGenerico::completarTrans($this->valido)) 
		{
			$servicioEvento->incluirEvento();
			if($i==0)
			{
				$this->mensaje .= 'La depreciaci&#243;n '.$comprobante.' del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', Fue reversado';
			}
			else
			{
				$this->mensaje .= ', '.$comprobante.'  del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', Fue reversado';
			}
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
			if($i==0)
			{
				$this->mensaje .= 'La depreciaci&#243;n '.$comprobante.' del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', No fue reversado';
			}
			else
			{
				$this->mensaje .= ', '.$comprobante.'  del mes '.$arrDetalle->mes.'::'.'a&#241;o '.$arrDetalle->anio.', No fue reversado';
			}
		}
	unset($servicioEvento);
	return $this->valido;
    }
//-----------------------------------------------------------------------------------------------------------------------------------
	public function procesoRevContabilizarDesSaf($objson) 
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrevento['codemp']    = $_SESSION['la_empresa']['codemp'];
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = $objson->codsis;
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = $objson->nomven;
		for($j=0;$j<=$nSol-1;$j++)
		{
			$comprobante=fillComprobante($objson->arrDetalle[$j]->cmpmov);
			$arrevento['desevetra'] = "Reverso la contabilizacion de la desincorporaci&#243;n {$comprobante}, asociado a la empresa {$_SESSION['la_empresa']['codemp']}";
			if ($this->RevContabilizarDesSaf($comprobante,$objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Desincorporaci&#243;n reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La Desincorporaci&#243;n no fue reversada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function RevContabilizarDesSaf($comprobante,$objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$arrDetalle = $objson->arrDetalle[$j];
		$i = 0;
		$this->valido=true;
		$procede="SAFCDN";
		$ls_tipo_destino="-";
		$ls_codigo_destino="----------";
		$adt_fecha=convertirFechaBd($objson->fechaconta);
		$adt_fecha_mov=convertirFechaBd($arrDetalle->feccmp);
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		if($this->valido)		
		{
			$arrcabecera['codemp'] = $_SESSION['la_empresa']['codemp'];
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = $ls_codban;
			$arrcabecera['ctaban'] = $ls_ctaban;
			$arrcabecera['fecha'] = $adt_fecha;
			$arrcabecera['descripcion'] = utf8_decode($arrDetalle->conmov);
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $ls_tipo_destino;
			$arrcabecera['cod_pro'] = $ls_codigo_destino;
			$arrcabecera['ced_bene'] = $ls_codigo_destino;
			$arrcabecera['total'] = 0;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if ($this->valido)
			{
				$this->valido = $this->uf_update_estatus_desincorporacion($comprobante,$adt_fecha_mov,0);
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$arrevento['desevetra'] = 'Reverso la desincorporaci&#243;n '.$comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp']; 
		$servicioEvento->desevetra=$arrevento['desevetra'];			
		if (DaoGenerico::completarTrans($this->valido)) 
		{
			$servicioEvento->incluirEvento();
			if($i==0)
			{
				$this->mensaje .= 'La desincorporaci&#243;n '.$comprobante.' , Fue reversado';
			}
			else
			{
				$this->mensaje .= ', '.$comprobante.', Fue reversado';
			}
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
			if($i==0)
			{
				$this->mensaje .= 'La desincorporaci&#243;n '.$comprobante.', No fue reversado';
			}
			else
			{
				$this->mensaje .= ', '.$comprobante.', No fue reversado';
			}
		}
	unset($servicioEvento);
	return $this->valido;
    }
//-----------------------------------------------------------------------------------------------------------------------------------
	public function procesoContabilizarDesSaf($objson) 
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$h = 0;
		$nOk = 0;
		$nEr = 0;
		$arrevento['codemp']    = $_SESSION['la_empresa']['codemp'];
		$arrevento['codusu']    = $_SESSION['la_logusr'];
		$arrevento['codsis']    = $objson->codsis;
		$arrevento['evento']    = 'PROCESAR';
		$arrevento['nomfisico'] = $objson->nomven;
		for($j=0;$j<=$nSol-1;$j++)
		{
			$comprobante=fillComprobante($objson->arrDetalle[$j]->cmpmov);
			$arrevento['desevetra'] = "Contabilizo la desincorporaci&#243;n de Activo {$comprobante}, asociado a la empresa {$_SESSION['la_empresa']['codemp']}";
			if ($this->contabilizarDesSaf($comprobante,$objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = 'Desincorporaci&#243;n contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $comprobante;
				$arrRespuesta[$h]['mensaje'] = "La desincorporaci&#243;n no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function contabilizarDesSaf($comprobante,$objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$arrDetalle = $objson->arrDetalle[$j];
		$i = 0;
		$this->valido=true;
		$procede="SAFCDN";
		$ls_tipo_destino="-";
		$ls_codigo_destino="----------";
		$adt_fecha=convertirFechaBd($objson->fechaconta);
		$adt_fecha_mov=convertirFechaBd($arrDetalle->feccmp);
		$ls_codban="---";
		$ls_ctaban="-------------------------";
		
		if($this->valido)		
		{
			$arrcabecera['codemp'] = $_SESSION['la_empresa']['codemp'];
			$arrcabecera['procede'] = $procede;
			$arrcabecera['comprobante'] = $comprobante;
			$arrcabecera['codban'] = $ls_codban;
			$arrcabecera['ctaban'] = $ls_ctaban;
			$arrcabecera['fecha'] = $adt_fecha;
			$arrcabecera['descripcion'] = utf8_decode($arrDetalle->conmov);
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $ls_tipo_destino;
			$arrcabecera['cod_pro'] = $ls_codigo_destino;
			$arrcabecera['ced_bene'] = $ls_codigo_destino;
			$arrcabecera['total'] = 0;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallescg=$this->buscarDetalleContableDes($arrcabecera,$comprobante,$adt_fecha_mov,$arrDetalle->conmov);
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if ($this->valido)
			{
				$this->valido = $this->uf_update_estatus_desincorporacion($comprobante,$adt_fecha_mov,1);
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$arrevento['desevetra'] = 'Contabilizo la desincorporaci&#243;n '.$comprobante.', asociado a la empresa '.$_SESSION['la_empresa']['codemp']; 
		$servicioEvento->desevetra=$arrevento['desevetra'];			
		if (DaoGenerico::completarTrans($this->valido)) 
		{
			$servicioEvento->incluirEvento();
			if($i==0)
			{
				$this->mensaje .= 'La desincorporaci&#243;n '.$comprobante.' , Fue contabilizado';
			}
			else
			{
				$this->mensaje .= ', '.$comprobante.'  , Fue contabilizado';
			}
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
			if($i==0)
			{
				$this->mensaje .= 'La desincorporaci&#243;n '.$comprobante.', No fue contabilizado';
			}
			else
			{
				$this->mensaje .= ', '.$comprobante.'  , No fue contabilizado';
			}
		}
	unset($servicioEvento);
	return $this->valido;
    }
//-----------------------------------------------------------------------------------------------------------------------------------
	public function load_depreciacion_int($as_numcom)
	{
	  $lb_valido = true;
	  $cadenasql = " SELECT estrepasi".
				   " FROM saf_depreciacion_int".
				   " WHERE codemp='".$_SESSION['la_empresa']['codemp']."'".
				   " AND comprobante='".$as_numcom."'".
				   " AND estrepasi = '1'";
	  
	  $conexionbd = ConexionBaseDatos::getInstanciaConexion();
	  $resultado = $conexionbd->Execute ( $cadenasql );
	  if ($resultado===false)
	  {
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		$lb_valido=false;
	  }
	  else
	  {
	  	while((!$resultado->EOF))
		{
			$lb_valido = false;
			$mensaje .= '  ->'." El Comprobante Nro. ".$as_numcom.", Ya fue Replicado !!!";
			$resultado->MoveNext();
		}
	  }
	  return $lb_valido;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function delete_depreciacion_int($as_numcom)
	{
	  $lb_valido = true;
	  $cadenasql = "DELETE FROM saf_depreciacion_int ".
				   " WHERE codemp='".$_SESSION['la_empresa']['codemp']."' ".
				   " AND comprobante='".$as_numcom."' ".
				   " AND estrepasi = '0'"; 
	  
	  $conexionbd = ConexionBaseDatos::getInstanciaConexion();
	  $resultado = $conexionbd->Execute ( $cadenasql );
	  if ($resultado===false)
	  {
		$mensaje .= '  ->'.$conexionbd->ErrorMsg();
		$lb_valido=false;
	  }
	  return $lb_valido;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function buscarDetallePresupuesto($as_anio,$as_mes)
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ai_len2=$_SESSION["la_empresa"]["loncodestpro2"];
		$ai_len3=$_SESSION["la_empresa"]["loncodestpro3"];
		$ai_len4=$_SESSION["la_empresa"]["loncodestpro4"];
		$ai_len5=$_SESSION["la_empresa"]["loncodestpro5"];
			switch($ls_modalidad)
			{
				case "1": // Modalidad por Proyecto
					$codest1 = "SUBSTR(saf_activo.codestpro1,25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(saf_activo.codestpro2,25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(saf_activo.codestpro3,25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = "SUBSTR(saf_activo.codestpro1,25-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(saf_activo.codestpro2,25-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(saf_activo.codestpro3,25-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$codest4 = "SUBSTR(saf_activo.codestpro4,25-{$_SESSION["la_empresa"]["loncodestpro4"]})";
					$codest5 = "SUBSTR(saf_activo.codestpro5,25-{$_SESSION["la_empresa"]["loncodestpro5"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
					break;
			}
			 
			 
			$cadenaSQL = "SELECT {$cadenaEstructura} AS estructura, saf_activo.codestpro1,".
						 "       saf_activo.codestpro2, saf_activo.codestpro3, saf_activo.codestpro4, saf_activo.codestpro5,".
						 "       saf_activo.estcla, saf_activo.spg_cuenta_dep AS spg_cuenta, SUM(saf_depreciacion.mondepmen) AS monto,".
					     "       0 AS disponibilidad, MAX(spg_cuentas.denominacion) AS denominacion ".
						 "  FROM saf_depreciacion, saf_activo ".
						 " INNER JOIN spg_cuentas ". 
					 	 "    ON saf_activo.codemp = spg_cuentas.codemp ". 
						 "   AND saf_activo.codestpro1 = spg_cuentas.codestpro1 ". 
						 "   AND saf_activo.codestpro2 = spg_cuentas.codestpro2 ". 
						 "   AND saf_activo.codestpro3 = spg_cuentas.codestpro3 ". 
						 "   AND saf_activo.codestpro4 = spg_cuentas.codestpro4 ". 
						 "   AND saf_activo.codestpro5 = spg_cuentas.codestpro5 ". 
						 "   AND saf_activo.estcla = spg_cuentas.estcla ". 
						 "   AND saf_activo.spg_cuenta_dep = spg_cuentas.spg_cuenta ". 
						 " WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
						 "   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_anio."'".
						 "   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
						 "   AND saf_depreciacion.codemp = saf_activo.codemp".
						 "   AND saf_depreciacion.codact = saf_activo.codact".
						 " GROUP BY saf_activo.codestpro1, saf_activo.codestpro2, saf_activo.codestpro3, saf_activo.codestpro4,".
						 "		   saf_activo.codestpro5, saf_activo.estcla, saf_activo.spg_cuenta_dep";

		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function buscarInformacionDetalle($as_anio,$as_mes)
	{
		$arrDisponible = array();
		$j = 0;
		$as_depreciacion=$this->uf_buscarconfiguracion();
		if($as_depreciacion!="C")
		{
			$dataCuentas = $this->buscarDetallePresupuesto($as_anio,$as_mes);
			while (!$dataCuentas->EOF) {
				$disponible = 0;
				$this->servicioComprobante = new ServicioComprobanteSPG();
				$arrdetallespg['codemp']     = $_SESSION['la_empresa']['codemp'];
				$arrdetallespg['codestpro1'] = $dataCuentas->fields['codestpro1'];
				$arrdetallespg['codestpro2'] = $dataCuentas->fields['codestpro2'];
				$arrdetallespg['codestpro3'] = $dataCuentas->fields['codestpro3'];
				$arrdetallespg['codestpro4'] = $dataCuentas->fields['codestpro4'];
				$arrdetallespg['codestpro5'] = $dataCuentas->fields['codestpro5'];
				$arrdetallespg['estcla']     = $dataCuentas->fields['estcla'];
				$arrdetallespg['spg_cuenta'] = $dataCuentas->fields['spg_cuenta'];
				$this->servicioComprobante->setDaoDetalleSpg($arrdetallespg);
				$this->servicioComprobante->saldoSelect($status, $asignado, $aumento, $disminucion, $precomprometido, $comprometido, $causado, $pagado,'ACTUAL');
				
				$disponibilidad =  (($asignado + $aumento) - ( $disminucion + $comprometido + $precomprometido));
				if(trim($dataCuentas->fields['operacion']) == 'DI')
				{
					if(round($dataCuentas->fields['monto'],2) < round($disponibilidad,2))
					{
						$disponible = 1;
					}
				}
				else {
					$disponible = 1;
				}
				$arrDisponible[$j]['estructura']     = $dataCuentas->fields['estructura'];
				$arrDisponible[$j]['estcla']         = $dataCuentas->fields['estcla'];
				$arrDisponible[$j]['operacion']      = $dataCuentas->fields['operacion'];
				$arrDisponible[$j]['spg_cuenta']     = $dataCuentas->fields['spg_cuenta'];
				$arrDisponible[$j]['monto']          = $dataCuentas->fields['monto'];
				$arrDisponible[$j]['denominacion']   = utf8_encode($dataCuentas->fields['denominacion']);
				$arrDisponible[$j]['disponibilidad'] = $disponible;
				
				unset($this->servicioComprobante);
				$j++;
				$dataCuentas->MoveNext();
			}
			unset($dataCuentas);
		}
		
		return $arrDisponible;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function detalleContable($as_anio,$as_mes) 
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$as_depreciacion=$this->uf_buscarconfiguracion();
		if($as_depreciacion!="C")
		{
				$cadenaSQL="SELECT SUM(saf_depreciacion.mondepmen) AS debe, 0 AS haber, spg_cuentas.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion".
							"  FROM saf_depreciacion,saf_activo, spg_cuentas, scg_cuentas ".
							" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
							"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_anio."'".
							"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
							"   AND saf_depreciacion.codemp = saf_activo.codemp".
							"   AND saf_depreciacion.codact = saf_activo.codact".
							"   AND saf_activo.codemp = spg_cuentas.codemp".
							"   AND saf_activo.codestpro1 = spg_cuentas.codestpro1".
							"   AND saf_activo.codestpro2 = spg_cuentas.codestpro2".
							"   AND saf_activo.codestpro3 = spg_cuentas.codestpro3".
							"   AND saf_activo.codestpro4 = spg_cuentas.codestpro4".
							"   AND saf_activo.codestpro5 = spg_cuentas.codestpro5".
							"   AND saf_activo.spg_cuenta_dep = spg_cuentas.spg_cuenta".
							"   AND scg_cuentas.codemp = spg_cuentas.codemp".
							"   AND scg_cuentas.sc_cuenta = spg_cuentas.sc_cuenta ".
							" GROUP BY spg_cuentas.sc_cuenta".
							" UNION ".
							"SELECT 0 AS debe,SUM(saf_depreciacion.mondepmen) AS haber, saf_activo.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion".
							"  FROM saf_depreciacion,saf_activo, spg_ep1  scg_cuentas ".
							" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
							"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_anio."'".
							"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
							"   AND spg_ep1.estint=0".
							"   AND saf_depreciacion.codemp = saf_activo.codemp".
							"   AND saf_depreciacion.codact = saf_activo.codact".
							"   AND saf_activo.codemp=spg_ep1.codemp".
							"   AND saf_activo.codestpro1=spg_ep1.codestpro1".
							"   AND saf_activo.estcla=spg_ep1.estcla".
							"   AND scg_cuentas.codemp = saf_activo.codemp".
							"   AND scg_cuentas.sc_cuenta = saf_activo.sc_cuenta ".
							" GROUP BY saf_activo.sc_cuenta".
							" UNION ".
							"SELECT 0 AS debe,SUM(saf_depreciacion.mondepmen) AS haber, trim(spg_ep1.sc_cuenta) AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion".
							"  FROM saf_depreciacion,saf_activo, spg_ep1".
							" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
							"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_anio."'".
							"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
							"   AND spg_ep1.estint=1".
							"   AND saf_depreciacion.codemp = saf_activo.codemp".
							"   AND saf_depreciacion.codact = saf_activo.codact".
							"   AND saf_activo.codemp=spg_ep1.codemp".
							"   AND saf_activo.codestpro1=spg_ep1.codestpro1".
							"   AND saf_activo.estcla=spg_ep1.estcla".
							"   AND scg_cuentas.codemp = spg_ep1.codemp".
							"   AND scg_cuentas.sc_cuenta = spg_ep1.sc_cuenta ".
							" GROUP BY spg_ep1.sc_cuenta";
		}
		else
		{
				$cadenaSQL="SELECT SUM(saf_depreciacion.mondepmen) AS debe,'0' AS haber, saf_activo.spg_cuenta_dep AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion".
					"  FROM saf_depreciacion,saf_activo, scg_cuentas".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_anio."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					"   AND scg_cuentas.codemp = saf_activo.codemp".
					"   AND scg_cuentas.sc_cuenta = saf_activo.sc_cuenta ".
					" GROUP BY saf_activo.spg_cuenta_dep".
					" UNION ".
					"SELECT '0' AS debe,SUM(saf_depreciacion.mondepmen) AS haber,  saf_activo.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) AS denominacion".
					"  FROM saf_depreciacion,saf_activo, scg_cuentas".
					" WHERE saf_depreciacion.codemp='".$_SESSION['la_empresa']['codemp']."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),1,4) = '".$as_anio."'".
					"   AND SUBSTR(CAST(saf_depreciacion.fecdep AS CHAR(10)),6,2) = '".$as_mes."'".
					"   AND saf_depreciacion.codemp = saf_activo.codemp".
					"   AND saf_depreciacion.codact = saf_activo.codact".
					"   AND scg_cuentas.codemp = saf_activo.codemp".
					"   AND scg_cuentas.sc_cuenta = saf_activo.sc_cuenta ".
					" GROUP BY saf_activo.sc_cuenta";
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	public function detalleContableDes($as_comp,$as_fecha) 
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$as_fecha=convertirFechaBd($as_fecha);
		
		$cadenaSQL = "SELECT saf_contable.sc_cuenta as cuenta, (CASE WHEN debhab = 'D' THEN monto ELSE 0 end) AS debe,".
		             "     (CASE WHEN debhab = 'H' THEN monto ELSE 0 end) AS haber, scg_cuentas.denominacion ".
					 " FROM saf_contable".
				     " INNER JOIN scg_cuentas ". 
				     "    ON saf_contable.codemp = scg_cuentas.codemp ". 
				     "   AND saf_contable.sc_cuenta = scg_cuentas.sc_cuenta ". 
					 " WHERE saf_contable. codemp='".$_SESSION['la_empresa']['codemp']."'".
					 "   AND cmpmov='".$as_comp."'".
					 "   AND feccmp='".$as_fecha."'";
													  
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
//-----------------------------------------------------------------------------------------------------------------------------------

}
?>