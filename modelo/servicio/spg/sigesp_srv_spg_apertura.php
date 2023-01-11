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
require_once ($dirsrv."/modelo/servicio/spg/sigesp_srv_spg_iapertura.php");
require_once ($dirsrv."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobantespg.php");

class ServicioComprobanteApertura implements IComprobanteApertura {

	public  $mensaje; 
	public  $valido; 
	private $conexionbd; 
	private $daoComprobante;
	private $daoDetalleSpg;
	
	public function __construct() 
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->daoComprobante = null;
		$this->daoDetalleSpg = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function buscarCuentasApertura($codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla)
	{
		$cadena='';
		$cadena1='';
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$cadena="CONVERT('0,00' USING utf8) AS pordistribuir ";
				$cadena1="THEN CONVERT('1' USING utf8) ELSE CONVERT('0' USING utf8)  ";
				break;
			case "MYSQLI":
				$cadena="CONVERT('0,00' USING utf8) AS pordistribuir ";
				$cadena1="THEN CONVERT('1' USING utf8) ELSE CONVERT('0' USING utf8)  ";
				break;
			case "POSTGRES":
				$cadena="CAST('0,00' AS varchar) AS pordistribuir, CAST('0' AS varchar) AS apertura";
				$cadena1="THEN CAST('1' AS varchar) ELSE CAST('0' AS varchar) ";
				break;					
			case "INFORMIX":
				$cadena="CAST('0,00' AS varchar) AS pordistribuir, CAST('0' AS varchar) AS apertura";
				$cadena1="THEN CAST('1' AS varchar) ELSE CAST('0' AS varchar) ";
				break;					
		}
		
		$cadenasql="SELECT spg_cuenta,status,denominacion,asignado,distribuir,enero, ".
	           "           febrero,marzo,abril,mayo,junio,julio,agosto,septiembre,octubre, ".
			   "           noviembre,diciembre, ".$cadena.", ".
			   "		   CASE WHEN asignado <> 0 ".$cadena1." END AS apertura ".
			   "    FROM spg_cuentas  ".
			   "    WHERE codemp='".$codemp."'  AND ".
			   "          codestpro1='".$codestpro1."' AND ".
			   "          codestpro2='".$codestpro2."' AND ". 
			   "          codestpro3='".$codestpro3."' AND ".
			   "          codestpro4='".$codestpro4."' AND ".
			   "          codestpro5='".$codestpro5."' AND ".
			   "          estcla='".$estcla."' AND ".
			   "          status='C' ".
			   "    ORDER BY spg_cuenta ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarCuentasApertura ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}	
	
	public function buscarFuentesFinanciamiento($codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla,$spg_cuenta)
	{
		$cadenasql = '';
		$tabla = 'spg_cuenta_fuentefinanciamiento';
		if(strtoupper($_SESSION["ls_gestor"])=="OCI8PO"){
	   		$tabla = 'spg_cuenta_fuentefinan';
	  	}
		$cadenasql="SELECT spg_dt_fuentefinanciamiento.codfuefin, sigesp_fuentefinanciamiento.denfuefin, ".
			       "       (SELECT COALESCE({$tabla}.monto,0) as monto  ".
			   	   "        FROM {$tabla} ".
			   	   "        WHERE {$tabla}.codemp = '".$codemp."' ".
			       "		  AND {$tabla}.codestpro1 = '".$codestpro1."' ".	 
			       "		  AND {$tabla}.codestpro2 = '".$codestpro2."' ".
			       "		  AND {$tabla}.codestpro3 = '".$codestpro3."' ".	
			   	   "		  AND {$tabla}.codestpro4 = '".$codestpro4."' ".	
			   	   "		  AND {$tabla}.codestpro5 = '".$codestpro5."' ".
			   	   "		  AND {$tabla}.estcla = '".$estcla."' ".
			       "		  AND {$tabla}.spg_cuenta = '".$spg_cuenta."' ".
			       "          AND {$tabla}.codfuefin = spg_dt_fuentefinanciamiento.codfuefin) as asignado ".
			       "FROM spg_dt_fuentefinanciamiento ".
				   "INNER JOIN sigesp_fuentefinanciamiento USING(codemp,codfuefin) ".
				   "WHERE spg_dt_fuentefinanciamiento.codemp = '".$codemp."' ".
			       "  AND spg_dt_fuentefinanciamiento.codestpro1 = '".$codestpro1."' ".
			       "  AND spg_dt_fuentefinanciamiento.codestpro2 = '".$codestpro2."' ".
			       "  AND spg_dt_fuentefinanciamiento.codestpro3 = '".$codestpro3."' ".
			       "  AND spg_dt_fuentefinanciamiento.codestpro4 = '".$codestpro4."' ".
			   	   "  AND spg_dt_fuentefinanciamiento.codestpro5 = '".$codestpro5."' ".
			       "  AND spg_dt_fuentefinanciamiento.estcla = '".$estcla."'".
			   	   "  AND sigesp_fuentefinanciamiento.codfuefin <> '--'  ";	
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarFuentesFinanciamiento ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}

	//Este método mediante la cadena mensaje retorna el codigo operacion asociado
	public function operacionMensajeCodigo($mensaje)
	{
		$asignar=0;
		$aumento=0;
		$disminucion=0;
		$precomprometer=0;
		$comprometer=0;
		$causar=0;
		$pagar=0; 
		$operacion=""; 
		$mensaje=strtoupper(trim($mensaje)); // devuelve cadena en MAYUSCULAS
		$pos_i=strpos($mensaje,"I"); 
		if(!($pos_i===false))
		{
			$asignar=1;
		}
		$pos_a=strpos($mensaje,"A");
		if(!($pos_a===false))
		{
			$aumento=1;
		}
		$pos_d=strpos($mensaje,"D");
		if(!($pos_d===false))
		{
			$disminucion=1;
		}
		$pos_r=strpos($mensaje,"R");
		if(!($pos_r===false))
		{
			$precomprometer=1;
		}
		$pos_o=strpos($mensaje,"O");
		if(!($pos_o===false))
		{
			$comprometer=1;
		}
		$pos_c=strpos($mensaje,"C");
		if(!($pos_c===false))
		{
			$causar=1;
		}
		$pos_p=strpos($mensaje,"P"); 
		if(!($pos_p===false))
		{
			$pagar=1;
		}
		$cadenasql="SELECT operacion ".
				"   FROM spg_operaciones ".
				"   WHERE asignar=".$asignar ." ".
				"     AND aumento=".$aumento." ".
				"     AND disminucion=".$disminucion." ".
				"     AND precomprometer=".$precomprometer." ".
				"     AND comprometer=".$comprometer." ".
				"     AND causar=".$causar." ".
				"     AND pagar=".$pagar;
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->operacionMensajeCodigo ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
				$operacion=$resultado->fields["operacion"];
			}
			else  
			{  
				$this->mensaje =  "No hay operacion asociada al mensaje ".$mensaje;		   		  		  			 
			}			
		}
		return $operacion;	
	} 
	
	//Método que actualiza la información de las Fuentes de Financiamiento 
	//Asociadas a la Estrutura Presupuestaria, Este proceso es utilizado en 
	//apertura de cuentas presupuestaria.
	public function existeDetFueFinEstructura($codemp,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$codfuefin)
  	{ 
  		$existe=false;
	 	$cadenasql= " SELECT * FROM spg_dt_fuentefinanciamiento ".
               		" WHERE spg_dt_fuentefinanciamiento.codemp = '".$codemp."' ".
			   		"	AND spg_dt_fuentefinanciamiento.codestpro1 = '".$ep1."' ".
			   		"	AND spg_dt_fuentefinanciamiento.codestpro2 = '".$ep2."' ".
			   		"	AND spg_dt_fuentefinanciamiento.codestpro3 = '".$ep3."' ".
			   		"	AND spg_dt_fuentefinanciamiento.codestpro4 = '".$ep4."' ".
			   		"	AND spg_dt_fuentefinanciamiento.codestpro5 = '".$ep5."' ".
			   		"	AND spg_dt_fuentefinanciamiento.estcla = '".$estcla."'".
			    	"	AND spg_dt_fuentefinanciamiento.codfuefin = '".$codfuefin."'";    
  		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->existeDetFueFinEstructura ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
	  	else
	  	{
	   		if(!$resultado->EOF)
	   		{
	    		$existe = true;
	   		}
		} 
	    return $existe;
	}
	
	//Método que inserta la información de las Fuentes de Financiamiento 
	//Asociadas a la Estrutura Presupuestaria, Este proceso es utilizado en 
	//apertura de cuentas presupuestaria.
	public function insertarDetFueFinEstructura($codemp,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$codfuefin)
	{
		$this->daoDetFueFinEst = FabricaDao::CrearDAO("N","spg_dt_fuentefinanciamiento");
		$this->daoDetFueFinEst->codemp=$codemp;
		$this->daoDetFueFinEst->codfuefin = $codfuefin;
		$this->daoDetFueFinEst->codestpro1 = $ep1;
		$this->daoDetFueFinEst->codestpro2 = $ep2;
		$this->daoDetFueFinEst->codestpro3 = $ep3;
		$this->daoDetFueFinEst->codestpro4 = $ep4;
		$this->daoDetFueFinEst->codestpro5 = $ep5;
		$this->daoDetFueFinEst->estcla = $estcla;
		if(!$this->daoDetFueFinEst->incluir()){
 	    	$this->mensaje .= ' Error en incluir fuente de financiamiento ';
		  	return false;
		}
		else{
			return true;
		}
	}
	
	//Método que actualiza la información de las Fuentes de Financiamiento 
	//Asociadas a la Estrutura Presupuestaria, Este proceso es utilizado en 
	//apertura de cuentas presupuestaria.
	public function existeFueFinEstructura($codemp,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$cuenta,$codfuefin)
  	{ 	
  		$valido=false;
	  	if($_SESSION['ls_gestor']=='oci8po') {
	  		$tabla = 'spg_cuenta_fuentefinan';
	  	}
	  	else{
	  		$tabla = 'spg_cuenta_fuentefinanciamiento';
	  	}
	  	$cadenasql= " SELECT * FROM {$tabla} ".
               		" WHERE {$tabla}.codemp = '".$codemp."' ".
			   		"	AND {$tabla}.codestpro1 = '".$ep1."' ".
			   		"	AND {$tabla}.codestpro2 = '".$ep2."' ".
			   		"	AND {$tabla}.codestpro3 = '".$ep3."' ".
			   		"	AND {$tabla}.codestpro4 = '".$ep4."' ".
			   		"	AND {$tabla}.codestpro5 = '".$ep5."' ".
			   		"	AND {$tabla}.estcla = '".$estcla."'".
			    	"	AND {$tabla}.codfuefin = '".$codfuefin."'".
			   		"	AND {$tabla}.spg_cuenta = '".$cuenta."'";	       
  		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->existeFueFinEstructura ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
	  	else
	  	{
	   		if(!$resultado->EOF)
	   		{
	    		$valido = true;
	   		}
	  	} 
	    return $valido;
	} 
	
	public function insertarFueFinEstructura($codemp,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$cuenta,$codfuefin,$monto)
	{
		if($_SESSION['ls_gestor']=='oci8po') {
	  		$tabla = "spg_cuenta_fuentefinan";
	  	}
	  	else{
	  		$tabla = "spg_cuenta_fuentefinanciamiento";
	  	}
	  	$this->daoFueFinEst = FabricaDao::CrearDAO("N",$tabla);
		$this->daoFueFinEst->codemp=$codemp;
		$this->daoFueFinEst->codfuefin = $codfuefin;
		$this->daoFueFinEst->codestpro1 = $ep1;
		$this->daoFueFinEst->codestpro2 = $ep2;
		$this->daoFueFinEst->codestpro3 = $ep3;
		$this->daoFueFinEst->codestpro4 = $ep4;
		$this->daoFueFinEst->codestpro5 = $ep5;
		$this->daoFueFinEst->estcla = $estcla;
		$this->daoFueFinEst->spg_cuenta = $cuenta;
	  	$this->daoFueFinEst->monto = $monto;
		if(!$this->daoFueFinEst->incluir()){
 	    	$this->mensaje .= ' Error en incluir fuente de financiamiento y estructura';
		  	return false;
		}
		else{
			return true;
		}
	}
	
	public function actualizarFueFinEstructura($codemp,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$cuenta,$codfuefin,$monto)
	{
		if($_SESSION['ls_gestor']=='oci8po') {
	  		$tabla = "spg_cuenta_fuentefinan";
	  	}
	  	else{
	  		$tabla = "spg_cuenta_fuentefinanciamiento";
	  	}
		$cadenasql= " UPDATE {$tabla} SET monto=$monto ".
               		" WHERE {$tabla}.codemp = '".$codemp."' ".
			   		"	AND {$tabla}.codestpro1 = '".$ep1."' ".
			   		"	AND {$tabla}.codestpro2 = '".$ep2."' ".
			   		"	AND {$tabla}.codestpro3 = '".$ep3."' ".
			   		"	AND {$tabla}.codestpro4 = '".$ep4."' ".
			   		"	AND {$tabla}.codestpro5 = '".$ep5."' ".
			   		"	AND {$tabla}.estcla = '".$estcla."'".
			    	"	AND {$tabla}.codfuefin = '".$codfuefin."'".
			   		"	AND {$tabla}.spg_cuenta = '".$cuenta."'";	       
  		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->actualizarFueFinEstructura ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else{
			$this->valido = true;
		}
		return $this->valido;
	}
	
	//Funcion que se usa  para actualizar los saldos de la distribucion de la apertura
	public function actualizarSaldosApertura($codemp,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10,$m11,$m12,
	                                         $ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$spg_cuenta,$distribuir,$asignado)
	{ 
		$m1=formatoNumericoBd($m1,1);
		$m2=formatoNumericoBd($m2,1);
		$m3=formatoNumericoBd($m3,1);
		$m4=formatoNumericoBd($m4,1);
		$m5=formatoNumericoBd($m5,1);
		$m6=formatoNumericoBd($m6,1);
		$m7=formatoNumericoBd($m7,1);
		$m8=formatoNumericoBd($m8,1);
		$m9=formatoNumericoBd($m9,1);
		$m10=formatoNumericoBd($m10,1);
		$m11=formatoNumericoBd($m11,1);
		$m12=formatoNumericoBd($m12,1);
		$cadenasql= " UPDATE spg_cuentas SET enero=$m1,febrero=$m2,marzo=$m3,abril=$m4,mayo=$m5, ".
		            "        junio=$m6,julio=$m7,agosto=$m8,septiembre=$m9,octubre=$m10, ".
		            "        noviembre=$m11,diciembre=$m12,distribuir=$distribuir ".
               		" WHERE codemp = '".$codemp."' ".
			   		"	AND codestpro1 = '".$ep1."' ".
			   		"	AND codestpro2 = '".$ep2."' ".
			   		"	AND codestpro3 = '".$ep3."' ".
			   		"	AND codestpro4 = '".$ep4."' ".
			   		"	AND codestpro5 = '".$ep5."' ".
			   		"	AND estcla = '".$estcla."'".
			   		"	AND spg_cuenta = '".$spg_cuenta."'";	  
  		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->actualizarFueFinEstructura ERROR->'.$this->conexionbd->ErrorMsg();
			return false;
		}
		else{
			return true;
		}
	}
	
	//Este método retorna la cuenta sin ceros a la derecha
	public function buscarCuentaSinCero($cuenta,$servicio)
	{
		$nivel=0;
		$anterior=0;
		$cadena="";
		$nivel=$servicio->obtenerCuentaSiguiente($cuenta);
		$anterior=$nivel;
		$len=strlen($anterior);
		$cadena=substr($cuenta,0,$anterior+1);
		return $cadena;
	} // end function uf_spg_cuenta_sin_cero
	
	//Metodo que retorna las cuentas hijas de la cuenta enviada.
	public function obtenerHijosMontos($codemp,$spg_cuenta,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla)
	{
		$monto=0;
		$arreglo=array();
		$cadenasql= " SELECT SUM(enero) as enero,SUM(febrero) as febrero,SUM(marzo) as marzo,SUM(abril) as abril, ".
		            "        SUM(mayo) as mayo,SUM(junio) as junio,SUM(julio) as julio,SUM(agosto) as agosto,   ".
	           		"        SUM(septiembre) as septiembre,SUM(octubre) as octubre,SUM(noviembre) as noviembre,  ".
		            "        SUM(diciembre) as diciembre, SUM(asignado) as asignado ".
			  		" FROM spg_cuentas ".
			  		" WHERE referencia like '%".$spg_cuenta."%' AND ".
			  		"       codestpro1='".$ep1."' AND codestpro2='".$ep2."' AND ".
			  		"		codestpro3='".$ep3."' AND codestpro4='".$ep4."' AND ".
			  		"       codestpro5='".$ep5."' AND estcla='".$estcla."'  ".
			  		" GROUP BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,referencia";    
  		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->obtenerHijosMontos ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
				$arreglo['enero']=$resultado->fields['enero'];
				$arreglo['febrero']=$resultado->fields['febrero'];
				$arreglo['marzo']=$resultado->fields['marzo'];
				$arreglo['abril']=$resultado->fields['abril'];
				$arreglo['mayo']=$resultado->fields['mayo'];
				$arreglo['junio']=$resultado->fields['junio'];
				$arreglo['julio']=$resultado->fields['julio'];
				$arreglo['agosto']=$resultado->fields['agosto'];
				$arreglo['septiembre']=$resultado->fields['septiembre'];
				$arreglo['octubre']=$resultado->fields['octubre'];
				$arreglo['noviembre']=$resultado->fields['noviembre'];
				$arreglo['diciembre']=$resultado->fields['diciembre'];
				$arreglo['asignado']=$resultado->fields['asignado'];
			}
		}
		return $arreglo;
	}
	
	//Funcion que se usa  para actualizar los saldos de la distribuciob y de las cuentas madres
	public function actualizarDistribucion($codemp,$estprog,$cuenta,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10,$m11,$m12,$distribuir,$asignado)
	{ 
		$this->valido=true;
		$arreglo=array();
		$serviciocomprobante = new ServicioComprobanteSPG();
		$nivel = $serviciocomprobante->obtenerNivel($cuenta);
		$nextcuenta=$cuenta;
		$ep1=$estprog[0];
		$ep2=$estprog[1];
		$ep3=$estprog[2];
		$ep4=$estprog[3];
		$ep5=$estprog[4];
		$estcla=$estprog[5];
		//Distribuyo los montos para la cuenta actual.
		$this->valido=$this->actualizarSaldosApertura($codemp,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10,$m11,$m12,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$nextcuenta,$distribuir,$asignado);
		//Obtengo la cuenta anterior.
		$nextcuenta = $serviciocomprobante->obtenerCuentaSiguiente($nextcuenta);
		while(($this->valido)&&($nivel>=1))
		{
		  	//Obtengo los hijos y los montos de la cuenta.
		  	$arreglo=$this->obtenerHijosMontos($codemp,$nextcuenta,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla);
	  	  	//Actualizo los saldos para la cuenta.
	  	  	$m1=$arreglo['enero'];
	  	  	$m2=$arreglo['febrero'];
	  	  	$m3=$arreglo['marzo'];
	  	  	$m4=$arreglo['abril'];
	  	  	$m5=$arreglo['mayo'];
	  	  	$m6=$arreglo['junio'];
	  	  	$m7=$arreglo['julio'];
	  	  	$m8=$arreglo['agosto'];
	  	  	$m9=$arreglo['septiembre'];
	  	  	$m10=$arreglo['octubre'];
	  	  	$m11=$arreglo['noviembre'];
	  	  	$m12=$arreglo['diciembre'];
	  	  	$asignado=$arreglo['asignado'];
			$this->valido=$this->actualizarSaldosApertura($codemp,$m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10,$m11,$m12,$ep1,$ep2,$ep3,$ep4,$ep5,$estcla,$nextcuenta,$distribuir,$asignado);
			if($serviciocomprobante->obtenerNivel($nextcuenta)==1)
			{ 
		  		break;
			}
			$nextcuenta = $serviciocomprobante->obtenerCuentaSiguiente($nextcuenta);
			$nivel = $serviciocomprobante->obtenerNivel($nextcuenta);
   		}//while	
   		return $this->valido;
	}//fin
	
	public function obtenerCodigoMenu($codsis,$nomfisico)
	{
		$arreglo=array();
		if(array_key_exists('session_activa',$_SESSION))
		{				
			$consulta = "SELECT codmenu ".
						"FROM sss_sistemas_ventanas ".
						"WHERE codsis = '$codsis' ".
						"  AND nomfisico ='$nomfisico' ";
			$resultado = $this->conexionbd->Execute($consulta);
			
			if($resultado===false)
			{
				$this->mensaje .= ' CLASE->SPG MÉTODO->obtenerCodigoMenu ERROR->'.$this->conexionbd->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				if(!$resultado->EOF)
				{   
					$codmenu=$resultado->fields["codmenu"];
				}
				$campo= "codmenu";
			}
		}
		else
		{
			$codmenu = $nomfisico;
			$campo= "nomven";
		}
		$arreglo['campo']=$campo;
		$arreglo['codmenu']=$codmenu;
		return $arreglo;
	}
	
	//Funcion que verificar si el usuario es administrador o no
	public function verificarAministrador($codemp,$codusu,$arrevento)
	{
    	$valido=false;
    	$i=1;
		$arreglo = $this->obtenerCodigoMenu('SPG',$arrevento['nomfisico']);
		$campo = $arreglo['campo'];
		$ventana = $arreglo['codmenu'];
		$consulta = " SELECT codusu ".
              		" FROM sss_derechos_usuarios  ".
              		" WHERE codemp ='".$codemp."' AND  codsis='SPG' AND  ".
			  		"       $campo ='".$ventana."' AND ".
              		"       incluir='1' AND cambiar='1' AND codusu='".$codusu."'";
		$resultado = $this->conexionbd->Execute($consulta);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->verificarAministrador ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
		   		$valido=true;
			}		
		}
    	return $valido;
	}
	
	//Funcion que se usa  para saber si existe  movimiento de la cuenta
	public function buscarCompromisoCuenta($codemp,$spg_cuenta,$estprog)
	{
		$arreglo=array();
		$arreglo['valido']=false;
    	$sql = "SELECT comprometido,aumento,disminucion  ".
               "FROM spg_cuentas  ".
               "WHERE codemp='".$codemp."' ".
			   "  AND codestpro1='".$estprog[0]."'  ".
			   "  AND codestpro2='".$estprog[1]."' ".
			   "  AND codestpro3='".$estprog[2]."' ".
               "  AND codestpro4='".$estprog[3]."' ".
			   "  AND codestpro5='".$estprog[4]."' ".
			   "  AND estcla='".$estprog[5]."' ".
			   "  AND spg_cuenta='".$spg_cuenta."' " ;
		$resultado = $this->conexionbd->Execute($sql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarCompromisoCuenta ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
		   		$arreglo['comprometido']=$resultado->fields["comprometido"];
		   		$arreglo['aumento']=$resultado->fields["aumento"];
		   		$arreglo['disminucion']=$resultado->fields["disminucion"];
		   		$arreglo['valido']=true;
			}
		}
		return $arreglo;
	}
	
	public function guardar($codemp,$objson,$arrevento)
	{   
		$arrDet = $objson->arrDetalle;
		$i=1;
		$funcion = '';
		$arrcabecera = array();
		$arregloSPG = array();
		$fecha = '01/01/'.$objson->periodo;
		$fecha = convertirFechaBd($fecha);
		$estprog[0]=$objson->codestpro1;
		$estprog[1]=$objson->codestpro2;
		$estprog[2]=$objson->codestpro3;
		$estprog[3]=$objson->codestpro4;
		$estprog[4]=$objson->codestpro5;
		$estprog[5]=$objson->estcla;
		DaoGenerico::iniciarTrans();
		if(!validarFechaPeriodo($fecha))
		{
			$this->mensaje .=  'Verifique que el periodo, y el mes de la empresa est&#233;n abiertos';
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $codemp;
			$arrcabecera['procede'] = 'SPGAPR';
			$arrcabecera['comprobante'] = '0000000APERTURA';
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = 'APERTURA DE CUENTAS';
			$arrcabecera['tipo_comp'] = 2;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['total'] = 0;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			foreach ($arrDet as $detalle)
			{
				$arrDetFueFin = $detalle->arrFueFin;
				if($this->valido)
				{
					if(count($arrDetFueFin)>0)
					{
						foreach ($arrDetFueFin as $detfuefin)
						{
							$existe = $this->existeFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,$detalle->spg_cuenta,$detfuefin->codfuefin);
							$monto = formatoNumericoBd($detfuefin->asignado,1);
							if(!$existe)
							{
								$this->valido = $this->insertarFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,$detalle->spg_cuenta,$detfuefin->codfuefin,$monto);
							}
							else
							{
								$this->valido = $this->actualizarFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,$detalle->spg_cuenta,$detfuefin->codfuefin,$monto);
							}
						}
					}
				}
				if($this->valido)
				{
					$existe = $this->existeDetFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,'--');
					if(!$existe)
					{
						$this->valido = $this->insertarDetFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,'--');
					}
					if($this->valido)
					{
						$existe = $this->existeFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,$detalle->spg_cuenta,'--');
						if(!$existe)
						{
							$this->valido = $this->insertarFueFinEstructura($codemp,$objson->codestpro1,$objson->codestpro2,$objson->codestpro3,$objson->codestpro4,$objson->codestpro5,$objson->estcla,$detalle->spg_cuenta,'--',0);
						}
					}
				}
				if($this->valido)
				{
					$monto = formatoNumericoBd($detalle->monto,1);
					if($monto!=0)
					{
						$logusr=$_SESSION['la_logusr'];
						if($this->verificarAministrador($codemp,$logusr,$arrevento))
						{
							$comprometido=0;
							$aumento=0;
							$disminucion=0;
							$arreglo=$this->buscarCompromisoCuenta($codemp,$detalle->spg_cuenta,$estprog);	
							$asignado=$monto;
							$comprometido=number_format($arreglo['comprometido'],2,".","");
							$montoactualizado=(number_format($monto,2,".","")+number_format($arreglo['aumento'],2,".",""))-number_format($arreglo['disminucion'],2,".","");
							$montoactualizado=number_format($montoactualizado,2,".","");
							if($this->valido)
							{
								if($montoactualizado>=$comprometido)
								{
									$arregloSPG[$i]['codemp']=$codemp;
									$arregloSPG[$i]['procede']= $arrcabecera['procede'];
									$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
									$arregloSPG[$i]['codban']= $arrcabecera['codban'];
									$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
									$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
									$arregloSPG[$i]['orden']= $i;	
									$arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];		
									$arregloSPG[$i]['spg_cuenta'] = $detalle->spg_cuenta;
									$arregloSPG[$i]['procede_doc'] = $arrcabecera['procede'];
									$arregloSPG[$i]['documento'] = $arrcabecera['comprobante'];
									$arregloSPG[$i]['operacion'] = $this->operacionMensajeCodigo("I");
									$arregloSPG[$i]['estcla'] = $objson->estcla;
									$arregloSPG[$i]['codestpro1'] = $objson->codestpro1;
									$arregloSPG[$i]['codestpro2'] = $objson->codestpro2;
									$arregloSPG[$i]['codestpro3'] = $objson->codestpro3;
									$arregloSPG[$i]['codestpro4'] = $objson->codestpro4;
									$arregloSPG[$i]['codestpro5'] = $objson->codestpro5;
									$arregloSPG[$i]['codfuefin'] = '--';
									$arregloSPG[$i]['monto'] = $monto;
									$i++;
									$funcion='ACTUALIZAR';
								}
								else
							   	{
								 	$this->mensaje= " La Cuenta ".$detalle->spg_cuenta."  tiene comprometido ".$arreglo['comprometido']." y el monto actualizado es ".$montoactualizado." esta asignando menos de lo comprometido por favor revise su monto asignado... ";
								  	$this->valido=false;						  
							   	}
							}	
						}
						else
						{
							$this->mensaje=" El usuario ".$logusr." no tiene permiso para modificar el asignado de la apertura  comuniquese con el  Administrador del Sistema....";
						    $this->valido=false;						  
						}
					}
				}
				if($this->valido)
				{
					$this->valido=$this->actualizarDistribucion($codemp,$estprog,$detalle->spg_cuenta,$detalle->enero,$detalle->febrero,
						                                        $detalle->marzo,$detalle->abril,$detalle->mayo,$detalle->junio,$detalle->julio,
						                                        $detalle->agosto,$detalle->septiembre,$detalle->octubre,$detalle->noviembre,
						                                        $detalle->diciembre,$detalle->distribuir,$monto);
				}
			}
			$serviciocomprobante = new ServicioComprobante();
			$_SESSION['fechacomprobante']=$fecha;
			if($serviciocomprobante->existeComprobante($codemp,$arrcabecera['procede'],$arrcabecera['comprobante'],$arrcabecera['codban'],$arrcabecera['ctaban'])){
				if(!empty($funcion))
				{
					if($funcion=='ACTUALIZAR')
					{
						$totDet = count($arregloSPG);
						for($j=1;$j<=$totDet;$j++)
						{
							if ($this->existeDetalleComprobante($arregloSPG[$j]))
							{
								$this->updateDetalleComprobante($arregloSPG[$j]);
							}
							else
							{
								$this->incluirDetalleComprobante($arregloSPG[$j]);
							}
						}						
					}
				}
			}
			else
			{
				$arrevento['desevetra'] = 'Guardo el Comprobante numero '.$arrcabecera['comprobante'].' asociado a la empresa '.$codemp;
				$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arregloSPG,null,null,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
			}
			unset($serviciocomprobante);
			
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
	
			//completando la transaccion retorna 1 si no hay errores
			if(DaoGenerico::completarTrans($this->valido))
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$this->mensaje.='Registro guardado con &#233;xito'; 		
			}
			else
			{
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
				$this->valido=false;
			}
			 
			//liberando variables y retornando el resultado de la operacion
			unset($this->daoRegistroEvento);
		}
		return $this->valido;
	}
	
	public function existeDetalleComprobante($arregloSPG) {
		$existe = false;
		$cadenaSQL = "SELECT spg_cuenta
  						FROM spg_dt_cmp
  						WHERE codemp='{$arregloSPG['codemp']}' AND comprobante='{$arregloSPG['comprobante']}' 
  						  AND procede_doc='{$arregloSPG['procede_doc']}' AND documento='{$arregloSPG['documento']}' 
  						  AND codestpro1='{$arregloSPG['codestpro1']}' AND estcla='{$arregloSPG['estcla']}' 
  						  AND codestpro2='{$arregloSPG['codestpro2']}' AND codestpro3='{$arregloSPG['codestpro3']}' 
  						  AND codestpro4='{$arregloSPG['codestpro4']}' AND codestpro5='{$arregloSPG['codestpro5']}' 
  						  AND spg_cuenta='{$arregloSPG['spg_cuenta']}' AND codban='{$arregloSPG['codban']}' 
  						  AND codfuefin='{$arregloSPG['codfuefin']}' AND codcencos='---' 
						  AND ctaban='{$arregloSPG['ctaban']}' AND procede='{$arregloSPG['procede']}' 
						  AND operacion='{$arregloSPG['operacion']}'";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->existeDetalleComprobante ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
				$existe=true;
			}
		}
		
		return $existe;
	}
	
	public function updateDetalleComprobante($arregloSPG)
	{
		$servicioCompSPG = new ServicioComprobanteSPG();
		$cadenaPk = "codemp='{$arregloSPG['codemp']}' AND comprobante='{$arregloSPG['comprobante']}'
						AND procede_doc='{$arregloSPG['procede_doc']}' AND documento='{$arregloSPG['documento']}'
						AND codestpro1='{$arregloSPG['codestpro1']}' AND estcla='{$arregloSPG['estcla']}'
						AND codestpro2='{$arregloSPG['codestpro2']}' AND codestpro3='{$arregloSPG['codestpro3']}'
						AND codestpro4='{$arregloSPG['codestpro4']}' AND codestpro5='{$arregloSPG['codestpro5']}'
						AND spg_cuenta='{$arregloSPG['spg_cuenta']}' AND codban='{$arregloSPG['codban']}'
						AND codfuefin='{$arregloSPG['codfuefin']}' AND codcencos='---'
						AND ctaban='{$arregloSPG['ctaban']}' AND procede='{$arregloSPG['procede']}'
						AND operacion='{$arregloSPG['operacion']}'";
		$servicioCompSPG->daoDetalleSpg = FabricaDao::CrearDAO('C', 'spg_dt_cmp', array(), $cadenaPk);
		$servicioCompSPG->mensajespg = trim(strtoupper($servicioCompSPG->buscarMensaje($servicioCompSPG->daoDetalleSpg->operacion)));
		
		if((is_null($servicioCompSPG->daoDetalleSpg->documento)) or (empty($servicioCompSPG->daoDetalleSpg->documento)))
		{
			$servicioCompSPG->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';
			$servicioCompSPG->valido = false;
		}
		if((is_null($servicioCompSPG->daoDetalleSpg->procede_doc)) or (empty($servicioCompSPG->daoDetalleSpg->procede_doc)))
		{
			$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';
			$this->valido = false;
		}
		if((is_null($servicioCompSPG->daoDetalleSpg->descripcion)) or (empty($servicioCompSPG->daoDetalleSpg->descripcion)))
		{
			$this->mensaje .= 'La Descripci&#243;n no puede tener valor nulo o vacio.';
			$this->valido = false;
		}
		if(($servicioCompSPG->existeCuenta())&&($this->valido))
		{
			if ($servicioCompSPG->existeCuentaFuenteFinanciamiento())
			{
				if($servicioCompSPG->saldoActual($servicioCompSPG->daoDetalleSpg->monto,$arregloSPG['monto']))
				{
					$cadenaSQL = "UPDATE spg_dt_cmp
									SET monto = {$arregloSPG['monto']}
									WHERE codemp='{$arregloSPG['codemp']}' AND comprobante='{$arregloSPG['comprobante']}'
  						  			AND procede_doc='{$arregloSPG['procede_doc']}' AND documento='{$arregloSPG['documento']}'
					  				AND codestpro1='{$arregloSPG['codestpro1']}' AND estcla='{$arregloSPG['estcla']}'
					  				AND codestpro2='{$arregloSPG['codestpro2']}' AND codestpro3='{$arregloSPG['codestpro3']}'
					  				AND codestpro4='{$arregloSPG['codestpro4']}' AND codestpro5='{$arregloSPG['codestpro5']}'
					  				AND spg_cuenta='{$arregloSPG['spg_cuenta']}' AND codban='{$arregloSPG['codban']}'
					  				AND codfuefin='{$arregloSPG['codfuefin']}' AND codcencos='---'
					  				AND ctaban='{$arregloSPG['ctaban']}' AND procede='{$arregloSPG['procede']}'
					  				AND operacion='{$arregloSPG['operacion']}'";
					$respuesta = $this->conexionbd->Execute($cadenaSQL);
					if($respuesta === false)
					{
						$this->mensaje .= $servicioCompSPG->daoDetalleSpg->ErrorMsg;
						$this->valido = false;
					}
				}
				else
				{
					$this->valido=false;
		
				}
				
			}
			else
			{
				$this->valido=false;
		
			}
		}
		else
		{
			$this->valido=false;
		}
	}
	
	public function incluirDetalleComprobante($arregloSPG) {
		$servicioCompSPG = new ServicioComprobanteSPG();
		$servicioCompSPG->daoDetalleSpg = FabricaDao::CrearDAO('N', 'spg_dt_cmp');				
		$servicioCompSPG->daoDetalleSpg->codemp=$arregloSPG['codemp'];
		$servicioCompSPG->daoDetalleSpg->procede=$arregloSPG['procede'];
		$servicioCompSPG->daoDetalleSpg->comprobante=$arregloSPG['comprobante'];
		$servicioCompSPG->daoDetalleSpg->codban=$arregloSPG['codban'];
		$servicioCompSPG->daoDetalleSpg->ctaban=$arregloSPG['ctaban'];
		$servicioCompSPG->daoDetalleSpg->estcla=$arregloSPG['estcla'];
		$servicioCompSPG->daoDetalleSpg->codestpro1=$arregloSPG['codestpro1'];
		$servicioCompSPG->daoDetalleSpg->codestpro2=$arregloSPG['codestpro2'];
		$servicioCompSPG->daoDetalleSpg->codestpro3=$arregloSPG['codestpro3'];
		$servicioCompSPG->daoDetalleSpg->codestpro4=$arregloSPG['codestpro4'];
		$servicioCompSPG->daoDetalleSpg->codestpro5=$arregloSPG['codestpro5'];
		$servicioCompSPG->daoDetalleSpg->spg_cuenta=$arregloSPG['spg_cuenta'];
		$servicioCompSPG->daoDetalleSpg->procede_doc=$arregloSPG['procede_doc'];
		$servicioCompSPG->daoDetalleSpg->documento=$arregloSPG['documento'];
		$servicioCompSPG->daoDetalleSpg->operacion=$arregloSPG['operacion'];
		$servicioCompSPG->daoDetalleSpg->codfuefin=$arregloSPG['codfuefin'];
		$servicioCompSPG->daoDetalleSpg->fecha=$arregloSPG['fecha'];
		$_SESSION['fechacomprobante']=$servicioCompSPG->daoDetalleSpg->fecha;
		$servicioCompSPG->daoDetalleSpg->descripcion=$arregloSPG['descripcion'];
		$servicioCompSPG->daoDetalleSpg->monto=$arregloSPG['monto'];
		$servicioCompSPG->daoDetalleSpg->orden=$arregloSPG['orden'];
		$servicioCompSPG->daoDetalleSpg->codcencos='---';
		$servicioCompSPG->mensajespg = trim(strtoupper($servicioCompSPG->buscarMensaje($arregloSPG['operacion'])));
		
		if((is_null($servicioCompSPG->daoDetalleSpg->documento)) or (empty($servicioCompSPG->daoDetalleSpg->documento)))
		{
			$servicioCompSPG->mensaje .= 'El N° de Documento no puede tener valor nulo o vacio.';			
			$servicioCompSPG->valido = false;	
		}
		if((is_null($servicioCompSPG->daoDetalleSpg->procede_doc)) or (empty($servicioCompSPG->daoDetalleSpg->procede_doc)))
		{
			$this->mensaje .= 'El Procede no puede tener valor nulo o vacio.';			
			$this->valido = false;	
		}
		if((is_null($servicioCompSPG->daoDetalleSpg->descripcion)) or (empty($servicioCompSPG->daoDetalleSpg->descripcion)))
		{
			$this->mensaje .= 'La Descripci&#243;n no puede tener valor nulo o vacio.';			
			$this->valido = false;	
		}
		if(($servicioCompSPG->existeCuenta())&&($this->valido))
		{
			if ($servicioCompSPG->existeCuentaFuenteFinanciamiento())
			{
				if(!$servicioCompSPG->existeMovimiento('2'))
				{
					if($servicioCompSPG->saldoActual(0,$servicioCompSPG->daoDetalleSpg->monto))
					{
						$this->valido=$servicioCompSPG->daoDetalleSpg->incluir();
						if(!$this->valido)
						{
							$this->mensaje .= $servicioCompSPG->daoDetalleSpg->ErrorMsg;
						}
					}
					else
					{
						$this->valido=false;
						
					}
				}
				else
				{
					$this->valido=false;
					
				}
			}
			else
			{
				$this->valido=false;						
				
			}
		}
		else
		{
			$this->valido=false;
		}
	}
	
	public function existeMovimiento($codemp, $objJson) {
		$existe = "N";
		$cadenaSQL = "SELECT spg_cuenta
  						FROM spg_dt_cmp
  						WHERE codemp='{$codemp}' AND codestpro1='{$objJson->codestpro1}' 
  						  AND estcla='{$objJson->estcla}' AND codestpro2='{$objJson->codestpro2}' 
  						  AND codestpro3='{$objJson->codestpro3}' AND codestpro4='{$objJson->codestpro4}' 
  						  AND codestpro5='{$objJson->codestpro5}' 
  						  AND spg_cuenta='{$objJson->cuenta}' AND procede <>'SPGAPR'";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false) {
			$this->mensaje .= ' CLASE->SPG MÉTODO->existeMovimiento ERROR->'.$this->conexionbd->ErrorMsg();
			return false;
		}
		else {
			if(!$resultado->EOF) {
				$existe = "Y";
			}
		}
		
		return $existe;
	}
	
	public function eliminarDetalleComprobante($codemp, $objJson)
	{
		$ok = true;
		$cadenaSQL = "DELETE FROM spg_dt_cmp
						WHERE codemp='{$codemp}' AND codestpro1='{$objJson->codestpro1}' AND estcla='{$objJson->estcla}'
						AND codestpro2='{$objJson->codestpro2}' AND codestpro3='{$objJson->codestpro3}'
						AND codestpro4='{$objJson->codestpro4}' AND codestpro5='{$objJson->codestpro5}'
						AND spg_cuenta='{$objJson->cuenta}'";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->eliminarDetalleComprobante ERROR->'.$this->conexionbd->ErrorMsg();
			return false;
		}
		
	
		return $ok;
	}
	
	public function eliminarCuentaFuenteEst($codemp, $objJson)
	{
		$ok = true;
		$cadenaSQL = "DELETE FROM spg_cuenta_fuentefinanciamiento
						WHERE codemp='{$codemp}' AND codestpro1='{$objJson->codestpro1}' AND estcla='{$objJson->estcla}'
						AND codestpro2='{$objJson->codestpro2}' AND codestpro3='{$objJson->codestpro3}'
						AND codestpro4='{$objJson->codestpro4}' AND codestpro5='{$objJson->codestpro5}'
						AND spg_cuenta='{$objJson->cuenta}'";
		$resultado = $this->conexionbd->Execute($cadenaSQL);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->eliminarDetalleComprobante ERROR->'.$this->conexionbd->ErrorMsg();
			return false;
		}
	
	
		return $ok;
	}
	
	public function reiniciarSaldoCuenta($codemp, $objJson)
	{
		$servicioCompSPG = new ServicioComprobanteSPG();
		$cadenaPk = "codemp='{$codemp}' AND codestpro1='{$objJson->codestpro1}' AND estcla='{$objJson->estcla}'
					 AND codestpro2='{$objJson->codestpro2}' AND codestpro3='{$objJson->codestpro3}'
					 AND codestpro4='{$objJson->codestpro4}' AND codestpro5='{$objJson->codestpro5}'
					 AND spg_cuenta='{$objJson->cuenta}' AND comprobante='0000000APERTURA'
					 AND procede_doc='SPGAPR' AND documento='0000000APERTURA' AND operacion='AAP'
					 AND codban='---' AND ctaban='-------------------------' AND codfuefin= '--'
					 AND codcencos='---' AND procede='SPGAPR'";
		$servicioCompSPG->daoDetalleSpg = FabricaDao::CrearDAO('C', 'spg_dt_cmp', array(), $cadenaPk);
		$servicioCompSPG->mensajespg = trim(strtoupper($servicioCompSPG->buscarMensaje($servicioCompSPG->daoDetalleSpg->operacion)));
		$monto=formatoNumericoBd($objJson->monto,1);
		return $servicioCompSPG->saldoActual($monto,0);
	}
	
	public function saldoCeroCuenta($codemp, $objJson)
	{
		$procesado = "N";
		$estprog[0]=$objJson->codestpro1;
		$estprog[1]=$objJson->codestpro2;
		$estprog[2]=$objJson->codestpro3;
		$estprog[3]=$objJson->codestpro4;
		$estprog[4]=$objJson->codestpro5;
		$estprog[5]=$objJson->estcla;
		
		$respuesta = $this->existeMovimiento($codemp, $objJson);
		if ($respuesta === false)
		{
			$this->mensaje .= $this->conexionbd->ErrorMsg();
			return false;
		}
		else
		{
			if ($respuesta == "N")
			{
				if ($this->reiniciarSaldoCuenta($codemp, $objJson) !== false)
				{
					if ($this->eliminarDetalleComprobante($codemp, $objJson))
					{
						$ok = $this->actualizarDistribucion($codemp,$estprog,$objJson->cuenta,'0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0','0','0', '$monto');
						if ($ok)
						{
							$procesado="Y";
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}	
				}				
			}
			else
			{
				$this->mensaje .= "La cuenta tiene movimientos no puede ser reiniciada.";
				return false;
			}
		}
		
		return $procesado;
	}
	
}
?>