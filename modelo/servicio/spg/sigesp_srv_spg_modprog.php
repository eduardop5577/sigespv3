<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
require_once ($dirsrv."/modelo/servicio/spg/sigesp_srv_spg_imodprog.php");
require_once ($dirsrv."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobantespg.php");

class ServicioModPrePro implements IModPrePro
{
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $daoComprobante;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->daoComprobante = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function buscarCuentasPresupuestarias($codemp,$codigo,$denominacion,$codcontable,$codestpro1,$codestpro2,
	                                             $codestpro3,$codestpro4,$codestpro5,$estcla)
	{	
		$cadenaFiltro = "";
		$logusr = $_SESSION["la_logusr"];
		if(!empty($codigo)){
			$cadenaFiltro .= " AND c.spg_cuenta like '%{$codigo}%' "; 
		}
		if(!empty($denominacion)){
			$cadenaFiltro .= " AND c.denominacion like '%{$denominacion}%' ";
		}
		if (!empty($codcontable)) {
			$cadenaFiltro .= " AND c.sc_cuenta like '%{$codcontable}%' ";
		}
		if(!empty($codestpro1)){
			$cadenaFiltro .= " AND c.codestpro1 = '{$codestpro1}' ";
		}
		if(!empty($codestpro2)){
			$cadenaFiltro .= " AND c.codestpro2 = '{$codestpro2}' ";
		}
		if(!empty($codestpro3)){
			$cadenaFiltro .= " AND c.codestpro3 = '{$codestpro3}' ";
		}
		if(!empty($codestpro4)){
			$cadenaFiltro .= " AND c.codestpro4 = '{$codestpro4}' ";
		}
		if(!empty($codestpro5)){
			$cadenaFiltro .= " AND c.codestpro5 = '{$codestpro5}' ";
		}
		if(!empty($codestpro5)){
			$cadenaFiltro .= " AND c.estcla = '{$estcla}' ";
		}
		$concatA = $this->conexionbd->Concat("'{$codemp}'","'SPG'","'{$logusr}'",'c.codestpro1','c.codestpro2','c.codestpro3','c.codestpro4','c.codestpro5','c.estcla');
		$concatB = $this->conexionbd->Concat('codemp','codsis','codusu','codintper');
		$cadenaSeguridad = " AND {$concatA} IN (SELECT distinct {$concatB} ".
		                   " FROM sss_permisos_internos WHERE codusu = '{$logusr}' AND codsis = 'SPG' AND enabled=1) ";
		
		$cadenaSQL = " SELECT c.spg_cuenta, c.denominacion, c.sc_cuenta, (c.asignado-(c.comprometido+c.precomprometido)+c.aumento-c.disminucion) as disponible ".
					"  FROM  spg_cuentas c  ".
					"  WHERE c.codemp = '{$codemp}' ".
					"	 AND status='C' {$cadenaFiltro}  ".
		            "    AND (c.asignado-(c.comprometido+c.precomprometido)+c.aumento-c.disminucion)>0 {$cadenaSeguridad} ".
					"  ORDER BY c.spg_cuenta";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarCuentasPresupuestarias ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function calcularAUDIMes($fecha1,$fecha2,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,$operacion)
	{
		$monto="";
	 	$cadenaSQL="SELECT COALESCE(SUM(monto),0) As monto    ".
				"	FROM spg_dt_cmp PCT,spg_operaciones O   ".
				"	WHERE PCT.operacion=O.operacion          ".
				"	   AND PCT.fecha between '".$fecha1."' and '".$fecha2."' ".
				"	   AND PCT.codestpro1='".$coest1."' ".
				"	   AND PCT.codestpro2='".$coest2."' ".
				"	   AND PCT.codestpro3='".$coest3."' ".
				"	   AND PCT.codestpro4='".$coest4."'  ".
				"	   AND PCT.codestpro5='".$coest5."'  ".
				"	   AND PCT.spg_cuenta='".$cuenta."' ".
				"      AND PCT.estcla='".$estcla."'".
				"	   AND PCT.operacion='".$operacion."'";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->calcularAumentoMes ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
			   $monto = $resultado->fields["monto"];
			}
		}
	    return $monto;
	}// fin de calcular_aumento_mes()
	
	function buscraProgramado($mes,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta)
   {
		switch ($mes) 
		{
			case "01":
				$mes="enero";
			break;
			case "02":
				$mes="febrero";
			break;
			case "03":
				$mes="marzo";
			break;
			case "04":
				$mes="abril";
			break;
			case "05":
				$mes="mayo";
			break;
			case "06":
				$mes="junio";
			break;
			case "07":
				$mes="julio";
			break;
			case "08":
				$mes="agosto";
			break;
			case "09":
				$mes="septiembre";
			break;
			case "10":
				$mes="octubre";
			break;
			case "11":
				$mes="noviembre";
			break;
			case "12":
				$mes="diciembre";
			break;
		}		  
	    $cadenaSQL="SELECT ".$mes." as montoprog FROM spg_cuentas ".
				"   WHERE codestpro1='".$coest1."'           ".
				"	  AND codestpro2='".$coest2."'           ".
				"	  AND codestpro3='".$coest3."'           ".
				"	  AND codestpro4='".$coest4."'           ".
				"	  AND codestpro5='".$coest5."'           ".
				"	  AND spg_cuenta='".$cuenta."'        ".
				"     AND estcla='".$estcla."'";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->calcularAumentoMes ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
			   $montoprog = $resultado->fields["montoprog"];
			}
		}
	    return $montoprog;
   }// fin de la funcion uf_buescra_programado
   
   public function modificarMes($mes,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,$monto,$fecha,$operacion)
   {   		
   		$this->valido = true;
   		$cadenaFiltro='';
   		$serviciocomprobante = new ServicioComprobanteSPG();
		switch($mes) 
		{
			case "01":
				$mes="enero";
			break;
			case "02":
				$mes="febrero";
			break;
			case "03":
				$mes="marzo";
			break;
			case "04":
				$mes="abril";
			break;
			case "05":
				$mes="mayo";
			break;
			case "06":
				$mes="junio";
			break;
			case "07":
				$mes="julio";
			break;
			case "08":
				$mes="agosto";
			break;
			case "09":
				$mes="septiembre";
			break;
			case "10":
				$mes="octubre";
			break;
			case "11":
				$mes="noviembre";
			break;
			case "12":
				$mes="diciembre";
			break;
		}
		$nextcuenta=$cuenta;
		$nivel=$serviciocomprobante->obtenerNivel($nextcuenta);
		if($operacion=='1'){
			$cadenaFiltro=$cadenaFiltro." SET ".$mes."= ".$mes." - ".$monto;
		}
		else{
			$cadenaFiltro=$cadenaFiltro." SET ".$mes."= ".$mes." - ".$monto;
		}
		while(($nivel>=1)and($this->valido)and($nextcuenta!=""))
		{
			$cadenaSQL="UPDATE spg_cuentas ".$cadenaFiltro.
					"   WHERE codestpro1='".$coest1."' ".
					"     AND codestpro2='".$coest2."' ".
					"     AND codestpro3='".$coest3."' ".
					"     AND codestpro4='".$coest4."' ".
					"     AND codestpro5='".$coest5."' ".
					"     AND spg_cuenta='".$nextcuenta."'".
					"     AND estcla='".$estcla."'";		
			$resultado = $this->conexionbd->Execute($cadenaSQL);
			if($resultado===false)
			{
				$this->mensaje .= ' CLASE->SPG MÉTODO->modificarMes ERROR->'.$this->conexionbd->ErrorMsg();
				$this->valido = false;
			}
			$nextcuenta=$serviciocomprobante->obtenerCuentaSiguiente($nextcuenta);
			$nivel=$serviciocomprobante->obtenerNivel($nextcuenta);
		}
		return $this->valido;
   	}// fin de la funcion  
	   
	public function guardarRegmodificacion($codemp,$estpro,$spg_cuenta,$monto,$mesaum,$mesdis)
   	{
    	$this->valido = true;
		$ip = "IP $_SERVER[REMOTE_ADDR]";
		$host=@gethostbyaddr($ip);
		$equipo = "Ip: ".$ip." - Equipo: ".$host;
		$fecha = date("Y-m-d H:i:s");
		$mesaum = str_pad($mesaum,2,"0");
		$mesdis = str_pad($mesdis,2,"0");
		$this->daoModProgramado = FabricaDao::CrearDAO("N","spg_regmodprogramado");
		$this->daoModProgramado->codemp=$codemp;
		$this->daoModProgramado->codestpro1 = $estpro[1];
		$this->daoModProgramado->codestpro2 = $estpro[2];
		$this->daoModProgramado->codestpro3 = $estpro[3];
		$this->daoModProgramado->codestpro4 = $estpro[4];
		$this->daoModProgramado->codestpro5 = $estpro[5];
		$this->daoModProgramado->estcla = $estpro[0];
		$this->daoModProgramado->spg_cuenta = $spg_cuenta;
		$this->daoModProgramado->codusu = $_SESSION['la_logusr'];;
		$this->daoModProgramado->fecha = convertirFechaBd($fecha);
		$this->daoModProgramado->equipo = $equipo;
		$this->daoModProgramado->mesaumento = $mesaum;
		$this->daoModProgramado->mesdisminucion = $mesdis;
		$this->daoModProgramado->monto = formatoNumericoBd($monto);
		$this->daoModProgramado->montoantmesaum = 0;
		$this->daoModProgramado->montoantmesdis = 0;
		$this->daoModProgramado->codcencos= '---';
		if(!$this->daoModProgramado->incluir()){
 	    	$this->mensaje .= ' Error en incluir modificacion de programado ';
		  	$this->valido=false;
		}
    	return $this->valido;
   }
	
	public function buscarDisponibilidadMensual($codemp,$objson,$arrevento)
	{
		$mes1=$objson->mes1;
		$mes2=$objson->mes2;
		$coest1=$objson->codestpro1;
		$coest2=$objson->codestpro2;
		$coest3=$objson->codestpro3;
		$coest4=$objson->codestpro4;
		$coest5=$objson->codestpro5;
		$estcla=$objson->estcla;
		$cuenta=$objson->spg_cuenta;
		$monto=$objson->monto;
		$fecha=convertirFechaBd($objson->fecha);
		$ano=$_SESSION["la_empresa"]["periodo"];
		DaoGenerico::iniciarTrans();
		$ano=substr($ano,0,4);
		$diames='';
		if($_SESSION["la_empresa"]["estmodape"]=="0"){
			$fechaIni=$ano."-".str_pad($mes1,2,"0",0)."-01";
			$diames=ultimoDiaMes($mes1,$ano);
		}
		else{
			switch ($mes1)// corresponde al trimestre a disminuir 
			{
				case "03":// trimestre desde enero hasta  Marzo
					$fechaIni=$ano."-01-01";
					$diames=ultimoDiaMes($mes1,$ano);	
				break;
				
				case "06":// trimestre desde Abril hasta  Junio
					$fechaIni=$ano."-04-01";
					$diames=ultimoDiaMes($mes1,$ano);
				break;
				
				case "09":// trimestre desde Julio hasta  Septiembre
					$fechaIni=$ano."-07-01";
					$diames=ultimoDiaMes($mes1,$ano);	
				break;
				
				case "12":// trimestre desde Octubre hasta  Diciembre
					$fechaIni=$ano."-10-01";
					$diames=ultimoDiaMes($mes1,$ano);	
				break;
			}
		}
		$fechaFinal=$diames;		
		$fecfin=""; 
		$pos=strpos($fechaFinal,"/");
		$pos2=strpos($fechaFinal,"-");
		if(($pos==2)||($pos2==2))
		{
			 $fecfin=(substr($fechaFinal,5,4)."-".str_pad(substr($fechaFinal,3,1),2,"0",0)."-".substr($fechaFinal,0,2)); 
		}
		$aumento=$this->calcularAUDIMes($fechaIni,$fechaFinal,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,'AU');		
		$disminucion=$this->calcularAUDIMes($fechaIni,$fechaFinal,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,'DI');		
		$compometido1=$this->calcularAUDIMes($fechaIni,$fechaFinal,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,'CCP');		
		$compometido2=$this->calcularAUDIMes($fechaIni,$fechaFinal,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,'CG');		
		$compometido3=$this->calcularAUDIMes($fechaIni,$fechaFinal,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,'CS');		
		$comprometido=0;
		$comprometido=$compometido1+$compometido2+$compometido3;
		$programado=$this->buscraProgramado($mes1,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta);
		$precompromiso=$this->calcularAUDIMes($fechaIni,$fechaFinal,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,'PC');	
		$disponible=(($programado+$aumento)-($disminucion+$comprometido+$precompromiso));
		$monto = str_replace(".","",$monto);
		$monto = str_replace(",",".",$monto);		
		if($disponible>=$monto)
		{
			$this->valido=$this->modificarMes($mes1,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,$monto,$fecha,'1');
			if ($this->valido)
			{
				$this->valido=$this->modificarMes($mes2,$coest1,$coest2,$coest3,$coest4,$coest5,$estcla,$cuenta,$monto,$fecha,'2');
				if($this->valido)
				{
				  	$estpro[0]=$estcla;
				  	$estpro[1]=$coest1;
				  	$estpro[2]=$coest2;
				  	$estpro[3]=$coest3;
				  	$estpro[4]=$coest4;
				  	$estpro[5]=$coest5;
				  	$this->valido = $this->guardarRegmodificacion($codemp,$estpro,$cuenta,$monto,$mes2,$mes1);
				  	if($this->valido){
				  		$servicioEvento = new ServicioEvento();
				  		$servicioEvento->evento=$arrevento['evento'];
				  		$servicioEvento->codemp=$arrevento['codemp'];
				  		$servicioEvento->codsis=$arrevento['codsis'];
				  		$servicioEvento->nomfisico=$arrevento['nomfisico'];
				  		$servicioEvento->desevetra=$arrevento['desevetra'];
					  	if(DaoGenerico::completarTrans($this->valido)) {
							$servicioEvento->tipoevento=true;
							$servicioEvento->incluirEvento();
							$this->mensaje.='Registro modificado con &#233;xito ';
						}
						else{
							$servicioEvento->tipoevento=false;
							$servicioEvento->incluirEvento();
							$this->valido=false;
							$this->mensaje.=' Error en modificar el presupuesto programado ';
						}
				  	}
				}					  
			}
		}
		else
		{
			$this->mensaje = " El Mes No Posee Disponibilidad suficiente ";
			$this->valido=false;
		}
		return $this->valido;
	}// fin de la funcion uf _buscar_disponibilidad_mensual
	
	
	public function obtenerRegmodificacion($codemp,$spg_cuenta,$fechades,$fechahas,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla)
    {
    	$this->valido = false;
		$cadena='';
    	
		if(!empty($ep1))
		{
	  		$cadena .= " AND spg_regmodprogramado.codestpro1 = '".$ep1."'";
	  		if(!empty($ep2)){
	  			$cadena .= " AND spg_regmodprogramado.codestpro2 = '".$ep2."'";
	  		}
			if(!empty($ep3)){
	  			$cadena .= " AND spg_regmodprogramado.codestpro3 = '".$ep3."'";
	  		}
			if(!empty($ep4)){
	  			$cadena .= " AND spg_regmodprogramado.codestpro4 = '".$ep4."'";
	  		}
			if(!empty($ep5)){
	  			$cadena .= " AND spg_regmodprogramado.codestpro5 = '".$ep5."'";
	  		}
			if(!empty($estcla)){
	  			$cadena .= " AND spg_regmodprogramado.estcla = '".$estcla."'";
	  		}
	 	}
		if(!empty($spg_cuenta))
		{
			$cadena .= " AND spg_regmodprogramado.spg_cuenta ='".$spg_cuenta."'";
		}
		if((!empty($fechades)) && (!empty($fechahas)))
		{
	 		$cadena .= " AND substr(CAST(spg_regmodprogramado.fecha AS CHAR(10)),1,10) >= '".substr($fechades,0,10)."' and substr(CAST(spg_regmodprogramado.fecha AS CHAR(10)),1,10) <= '".substr($fechahas,0,10)."'";
		}
	
		$cadenaSQL = "SELECT spg_regmodprogramado.*,spg_ep1.denestpro1,spg_ep2.denestpro2,spg_ep3.denestpro3,  ".
	          		 "       spg_ep4.denestpro4,spg_ep5.denestpro5  ".
		             "FROM spg_regmodprogramado ".
					 "INNER JOIN spg_ep1 USING (codemp,codestpro1,estcla) ".
				 	 "INNER JOIN spg_ep2 USING (codemp,codestpro1,codestpro2,estcla) ".
					 "INNER JOIN spg_ep3 USING (codemp,codestpro1,codestpro2,codestpro3,estcla) ".
					 "INNER JOIN spg_ep4 USING (codemp,codestpro1,codestpro2,codestpro3,codestpro4,estcla) ".
					 "INNER JOIN spg_ep5 USING (codemp,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) ".
	          		 "WHERE spg_regmodprogramado.codemp = '0001' ".$cadena ;	  
    	$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->obtenerRegmodificacion ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
    	return $resultado;
	}// fin de la clase sigesp_spg_c_mod_programado

}
?>