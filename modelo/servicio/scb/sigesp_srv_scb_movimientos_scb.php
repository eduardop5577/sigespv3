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
require_once ($dirsrvscb.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrvscb.'/modelo/servicio/scb/sigesp_srv_scb_imovimientos_scb.php');
require_once ($dirsrvscb.'/modelo/servicio/scb/sigesp_srv_scb_emision_chq.php');
require_once ($dirsrvscb.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrvscb.'/base/librerias/php/general/sigesp_lib_funciones.php');

class ServicioMovimientoScb implements IMovimientoScb
{
	private $daoMovbancarios;
	public  $mensaje; 
	public  $valido;
	public  $conexionBaseDatos; 
		
	public function __construct()  
	{
		$this->daoMovbancarios = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->bloanu = $_SESSION['la_empresa']['bloanu'];
	}
	
	public function buscarNroControlInterno($codemp,$procede)
	{
		$this->daoMovimiento = FabricaDao::CrearDAO("N", "scb_movbco");
		$this->daoMovimiento->codemp = $codemp;
		$this->pregenerico=$this->daoMovimiento->utilizaPrefijoGenerico('SCB',$procede);
		if($this->pregenerico)
		{
			$this->utilizaprefijo = $this->daoMovimiento->utilizaPrefijo('SCB',$procede,$_SESSION['la_logusr']);
			if($this->utilizaprefijo)
			{
				$this->valido=true;
				$codigo=$this->daoMovimiento->buscarCodigo('numconint',true,15,$procede,'SCB',$procede,$_SESSION['la_logusr'],'','');
			}
			else
			{
				$this->valido=false;
				$this->mensaje='El documento esta configurado con prefijos y el usuario no tiene asignado';
			}
		}
		else
		{
			$this->valido=true;
			$codigo = $this->daoMovimiento->buscarCodigo("numconint",true,15,'','SCB',$procede,$_SESSION["la_logusr"]);
		}
		$this->utilizaprefijo = $this->daoMovimiento->utilizaPrefijo('SCB',$procede,$_SESSION['la_logusr']);
		unset($this->daoMovimiento);
		return $codigo;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function validaFechaPeriodo($as_fecha,$as_codemp)
	{
	    $li_ano    = 0 ; $li_mes=0;$li_ano_periodo=0;$li_mes_periodo=0;
	    $ls_fecha  = ""; $ls_periodo_final=""; 
	    $lb_valido = true;
   	    //$as_fecha=$this->uf_convert_date_to_db($as_fecha);
	    $li_ano = intval(substr($as_fecha,0,4));
	    $li_mes = intval(substr($as_fecha,5,2));
	    $li_ano_periodo = intval(substr($_SESSION["la_empresa"]["periodo"],0,4));
	    $li_mes_periodo = intval(substr($_SESSION["la_empresa"]["periodo"],5,2));
	    $ld_periodo_final = "31/12/".$li_ano_periodo;
		if ($li_ano == $li_ano_periodo)
		{
			if($li_mes >= $li_mes_periodo)
			{
			   if($this->uf_valida_fecha_mes( $as_codemp, $as_fecha ))
			   {
			 	  $lb_valido = true;
			   }
			   else	 
			   {
				  $lb_valido = false;
			 	  //$this->is_msg_error = "Mes no esta Abierto";
				  return false;
			   }
			} 			
			else {  $lb_valido = false;	}
		}
		else 
		{ 
			$lb_valido = false;	
		}
		return $lb_valido;	
	} // end function()

	public function uf_valida_fecha_mes($as_codemp,$as_fecha)
 	{ 
		 $li_mes=0;$li_M01=0;$li_M02=0;$li_M03=0;$li_M04=0;$li_M05=0;
		 $li_M06=0;$li_M07=0;$li_M08=0;$li_M09=0;$li_M10=0;$li_M11=0;$li_M12=0;
		 $lb_abierto_mes=false;
		 $lb_valido=false;
		 $ls_cadena="";
		 $li_mes = intval(substr($as_fecha,5,2));
		 $cadenasql=" SELECT m01,m02,m03,m04,m05,m06,m07,m08,m09,m10,m11,m12 ". 
		 			" FROM sigesp_empresa WHERE codemp = '".$as_codemp."' ";
		 $conexionbd = ConexionBaseDatos::getInstanciaConexion();
		 $resultado = $conexionbd->Execute ( $cadenasql );
		 if ($resultado===false)
		 {
	 		 $mensaje .= '  ->'.$conexionbd->ErrorMsg();
		 }
		 else
		 {
			$lb_valido=true;
			while((!$resultado->EOF))
			{
				$li_M01=$resultado->fields["m01"];
				$li_M02=$resultado->fields["m02"];
				$li_M03=$resultado->fields["m03"];
				$li_M04=$resultado->fields["m04"];
				$li_M05=$resultado->fields["m05"];
				$li_M06=$resultado->fields["m06"];
				$li_M07=$resultado->fields["m07"];
				$li_M08=$resultado->fields["m08"];
				$li_M09=$resultado->fields["m09"];
				$li_M10=$resultado->fields["m10"];
				$li_M11=$resultado->fields["m11"];
				$li_M12=$resultado->fields["m12"];
				$resultado->MoveNext();
			}
		 } 
		 if ($lb_valido)
		 {
			switch ($li_mes)
			{
				case 1:
					if($li_M01==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 2:
					if($li_M02==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 3:
					if($li_M03==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 4:
					if($li_M04==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 5:
					if($li_M05==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 6:
					if($li_M06==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 7:
					if($li_M07==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 8:
					if($li_M08==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 9:
					if($li_M09==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 10:
					if($li_M10==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 11:
					if($li_M11==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				case 12:
					if($li_M12==1) 	{ $lb_abierto_mes = true; }
					else { $lb_abierto_mes = false;	}
					break;
				default:
			}
		 }	
		 if (!$lb_abierto_mes)
		 {
		   	 //$this->is_msg_error = "El Mes ".$li_mes." no esta abierto.";
			 $lb_valido = false;
		 }
		 unset($conexionbd);
		 unset($resultado);
		 return $lb_valido;
    } // end fuction
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_insert_fuentefinancimiento($arrCabeceraScb,$arrevento)
	{
		$resultado=false;
		//Creación del Dao con la tabla y campos a insertar.
		$this->daoInsertFteFinan=FabricaDao::CrearDAO("N","scb_movbco_fuefinanciamiento");
		$this->daoInsertFteFinan->codemp       = $arrCabeceraScb['codemp'];
		$this->daoInsertFteFinan->codban       = $arrCabeceraScb['codban']; 
		$this->daoInsertFteFinan->ctaban       = $arrCabeceraScb['ctaban']; 
		$this->daoInsertFteFinan->numdoc       = $arrCabeceraScb['numdoc']; 
		$this->daoInsertFteFinan->codope       = $arrCabeceraScb['codope']; 
		$this->daoInsertFteFinan->estmov       = $arrCabeceraScb['estmov']; 
		$this->daoInsertFteFinan->codfuefin    = $arrCabeceraScb['codfuefin']; 
		
		//Insertamos la fuente de financiamiento del banco
		$resultado = $this->daoInsertFteFinan->incluir();
		//Inicializamos el arreglo de eventos
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Inserto la fuente de financiamiento '.$arrCabeceraScb['codfuefin'].', en el movimiento de banco '.$arrCabeceraScb['codban'].'-'.$arrCabeceraScb['ctaban'].'-'.$arrCabeceraScb['numdoc'].'-'.$arrCabeceraScb['codope']; 
		if ($resultado)
		{
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento();
		}
		else
		{
			$arrevento ['desevetra'] = $this->daoInsertFteFinan->ErrorMsg();
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
			$this->mensaje .= $this->daoInsertFteFinan->ErrorMsg();
		}
		
		return $resultado;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_select_dt_contable($arrCabeceraScb,$sc_cuenta,$debhab,$codded,$documento)
	{
		$ls_codemp 		 = $arrCabeceraScb["codemp"];
		$ls_codban 		 = $arrCabeceraScb["codban"];
		$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
		$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
		$ls_codope 		 = $arrCabeceraScb["codope"];
		$ls_estmov 		 = $arrCabeceraScb["estmov"]; 

		$lb_valido=false;

		$cadenasql=" SELECT monto ".
				   " FROM scb_movbco_scg ".
				   " WHERE codemp='".$ls_codemp."' ".
				   " AND codban='".$ls_codban."' ".
				   " AND ctaban='".$ls_ctaban."' ".
				   " AND numdoc='".$ls_numdoc."' ".
				   " AND codope='".$ls_codope."' ".
				   " AND estmov='".$ls_estmov."' ".
				   " AND scg_cuenta='".$sc_cuenta."' ".
				   " AND debhab='".$debhab."' ".
				   " AND codded='".$codded."' ".
				   " AND documento='$documento' "; 

		$resultado = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($resultado===false)
		{
			$mensaje .= '  ->'.$conexionbd->ErrorMsg();
			$lb_valido = false;
		}
		else
		{
			while((!$resultado->EOF))
			{
				$ldec_actual=$resultado->fields["monto"];
				$lb_valido = true;
				$resultado->MoveNext();
			}
		}
		return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_select_dt_contable_montoac($arrCabeceraScb,$sc_cuenta,$debhab,$codded,$documento)
	{
		$ldec_actual=0;
		$lb_valido=true;
		$ls_codemp 		 = $arrCabeceraScb["codemp"];
		$ls_codban 		 = $arrCabeceraScb["codban"];
		$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
		$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
		$ls_codope 		 = $arrCabeceraScb["codope"];
		$ls_estmov 		 = $arrCabeceraScb["estmov"]; 
		
		$cadenasql=" SELECT monto ".
				   " FROM scb_movbco_scg ".
				   " WHERE codemp='".$ls_codemp."' ".
				   " AND codban='".$ls_codban."' ".
				   " AND ctaban='".$ls_ctaban."' ".
				   " AND numdoc='".$ls_numdoc."' ".
				   " AND codope='".$ls_codope."' ".
				   " AND estmov='".$ls_estmov."' ".
				   " AND scg_cuenta='".$sc_cuenta."' ".
				   " AND debhab='".$debhab."' ".
				   " AND codded='".$codded."' ".
				   " AND documento='$documento' "; 
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
				$ldec_actual=$resultado->fields["monto"];
				$lb_valido = true;
				$resultado->MoveNext();
			}
		}
		return $ldec_actual;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function  guardarDetalleSCG($arrCabeceraScb,$arrdetallescg,$arrevento)
	{
		$totalscg = count($arrdetallescg["scg_cuenta"]);
		$lb_valido=true;
		for ($i=0;$i<$totalscg;$i++)
		{
			$ldec_monto= $arrdetallescg["monto"][$i];
			$lb_valido = $this->uf_select_dt_contable($arrCabeceraScb,$arrdetallescg["scg_cuenta"][$i],$arrdetallescg["debhab"][$i],
													  $arrdetallescg["codded"][$i],$arrdetallescg["documento"][$i]);
			if(!$lb_valido)
			{
				$resultado=false;
				//Creación del Dao con la tabla y campos a insertar.
				$this->daoInsertDtScg=FabricaDao::CrearDAO("N","scb_movbco_scg");
				$this->daoInsertDtScg->codemp       = $arrCabeceraScb['codemp'];
				$this->daoInsertDtScg->codban       = $arrCabeceraScb['codban']; 
				$this->daoInsertDtScg->ctaban       = $arrCabeceraScb['ctaban']; 
				$this->daoInsertDtScg->numdoc       = $arrCabeceraScb['numdoc']; 
				$this->daoInsertDtScg->codope       = $arrCabeceraScb['codope'];
				$this->daoInsertDtScg->estmov       = $arrCabeceraScb['estmov'];  
				$this->daoInsertDtScg->scg_cuenta   = $arrdetallescg['scg_cuenta'][$i]; 
				$this->daoInsertDtScg->debhab       = $arrdetallescg['debhab'][$i];
				$this->daoInsertDtScg->documento    = $arrdetallescg['documento'][$i];
				$this->daoInsertDtScg->codded       = $arrdetallescg['codded'][$i];
				$this->daoInsertDtScg->desmov       = $arrdetallescg['desmov'][$i];
				$this->daoInsertDtScg->procede_doc  = $arrdetallescg['procede_doc'][$i];
				$this->daoInsertDtScg->monto        = $arrdetallescg['monto'][$i];
				$this->daoInsertDtScg->monobjret    = $arrdetallescg['monobjret'][$i]; 
				$this->daoInsertDtScg->codcencos = '---';
				
				
				//Insertamos la fuente de financiamiento del banco
				$resultado = $this->daoInsertDtScg->incluir();
				
				//Inicializamos el arreglo de eventos
				$servicioEvento = new ServicioEvento();
				$servicioEvento->evento=$arrevento['evento'];
				$servicioEvento->codemp=$arrevento['codemp'];
				$servicioEvento->codsis=$arrevento['codsis'];
				$servicioEvento->nomfisico=$arrevento['nomfisico'];
				$servicioEvento->desevetra='Incluyo la cuenta '.$this->daoInsertDtScg->scg_cuenta.'::'.$this->daoInsertDtScg->debhab.' en el movimiento de banco '.$this->daoInsertDtScg->codban.'::'.$this->daoInsertDtScg->ctaban.'::'.$this->daoInsertDtScg->numdoc.'::'.$this->daoInsertDtScg->codope; 
				if ($resultado)
				{
					$lb_valido=true;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoInsertDtScg->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$this->mensaje .= $this->daoInsertDtScg->ErrorMsg();
				}
			}
			else
			{ 
				$ldec_actual=$this->uf_select_dt_contable_montoac($arrCabeceraScb,$arrdetallescg["scg_cuenta"][$i],$arrdetallescg["debhab"][$i],
													  $arrdetallescg["codded"][$i],$arrdetallescg["documento"][$i]);
				$ldec_monto= number_format($ldec_monto+$ldec_actual,2,".","");
				$ls_codemp 		 = $arrCabeceraScb["codemp"];
				$ls_codban 		 = $arrCabeceraScb["codban"];
				$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
				$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
				$ls_codope 		 = $arrCabeceraScb["codope"];
				$ls_estmov 		 = $arrCabeceraScb["estmov"]; 
				$ls_cuenta 		 = $arrdetallescg["scg_cuenta"][$i];
				$ls_operacioncon = $arrdetallescg["debhab"][$i];
				$ls_codded 		 = $arrdetallescg["codded"][$i];
				$ls_documento 	 = $arrdetallescg["documento"][$i];
				
				$strPk = "codemp='{$ls_codemp}' AND codban='{$ls_codban}'".
				         "AND ctaban='{$ls_ctaban}' AND numdoc='{$ls_numdoc}'". 
						 "AND codope='{$ls_codope}' AND estmov='{$ls_estmov}'".
						 "AND scg_cuenta='{$ls_cuenta}' AND debhab='{$ls_operacioncon}'".
						 "AND codded='{$ls_codded}' AND documento='{$ls_documento}' ";

				$this->daoUpdateDtScg = FabricaDao::CrearDAO("C", "scb_movbco_scg", '', $strPk);
				$this->daoUpdateDtScg->monto = $ldec_monto ;
				$resultado = $this->daoUpdateDtScg->modificar();
				
				if ($resultado)
				{
					$lb_valido=true;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoUpdateDtScg->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$this->mensaje .= $this->daoUpdateDtScg->ErrorMsg();
				}
			}
		}
		return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_select_dt_gasto($arrCabeceraScb,$spg_cuenta,$codestpro,$documento,$estcla)
	{
		$ls_codemp 		 = $arrCabeceraScb["codemp"];
		$ls_codban 		 = $arrCabeceraScb["codban"];
		$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
		$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
		$ls_codope 		 = $arrCabeceraScb["codope"];
		$ls_estmov 		 = $arrCabeceraScb["estmov"]; 
		
		$lb_valido=false;
		
		$cadenasql="SELECT codemp 
			   FROM scb_movbco_spg
			  WHERE codemp='".$ls_codemp."' 
			    AND codban='".$ls_codban."' 
				AND ctaban='".$ls_ctaban."' 
			    AND numdoc='".$ls_numdoc."' 
				AND codope='".$ls_codope."' 
				AND estmov='".$ls_estmov."' 
			    AND spg_cuenta='".$spg_cuenta."' 
				AND codestpro='".$codestpro."' 
				AND documento='".$documento."'
				AND estcla='".$estcla."' ";
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
				$codigo=$resultado->fields["codemp"];
				$lb_valido = true;
				$resultado->MoveNext();
			}
		}
		return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_select_dt_spg_montoac($arrCabeceraScb,$spg_cuenta,$codestpro,$documento,$estcla)
	{
		$monto=0;
		$ls_codemp 		 = $arrCabeceraScb["codemp"];
		$ls_codban 		 = $arrCabeceraScb["codban"];
		$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
		$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
		$ls_codope 		 = $arrCabeceraScb["codope"];
		$ls_estmov 		 = $arrCabeceraScb["estmov"]; 

		$lb_valido=true;
		
		$cadenasql="SELECT monto 
			   FROM scb_movbco_spg
			  WHERE codemp='".$ls_codemp."' 
			    AND codban='".$ls_codban."' 
				AND ctaban='".$ls_ctaban."' 
			    AND numdoc='".$ls_numdoc."' 
				AND codope='".$as_codope."' 
				AND estmov='".$ls_estmov."' 
			    AND spg_cuenta='".$spg_cuenta."' 
				AND codestpro='".$codestpro."' 
				AND documento='".$documento."'
				AND estcla='".$estcla."'";
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
				$monto=$resultado->fields["monto"];
				$lb_valido = true;
				$resultado->MoveNext();
			}
		}
		return $monto;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function  guardarDetalleSPG($arrCabeceraScb,$arrdetallespg,$arrevento)
	{
		$ls_codfuefin = $arrCabeceraScb['codfuefin'];
		if ($ls_codfuefin=='')
		{
			$ls_codfuefin='--';
		}
		$lb_valido=true;
		$totalspg = count($arrdetallespg["spgcuenta"]);
		for ($i=0;$i<$totalspg;$i++)
		{
			$ldec_monto= $arrdetallespg["monto"][$i];
			$lb_valido = $this->uf_select_dt_gasto($arrCabeceraScb,$arrdetallespg["spgcuenta"][$i],$arrdetallespg["codestpro"][$i],
												   $arrdetallespg["documento"][$i],$arrdetallespg["estcla"][$i]);
			if(!$lb_valido)
			{
				$resultado=false;
				//Creación del Dao con la tabla y campos a insertar.
				$this->daoInsertDtSpg=FabricaDao::CrearDAO("N","scb_movbco_spg");
				$this->daoInsertDtSpg->codemp       = $arrCabeceraScb['codemp'];
				$this->daoInsertDtSpg->codban       = $arrCabeceraScb['codban']; 
				$this->daoInsertDtSpg->ctaban       = $arrCabeceraScb['ctaban']; 
				$this->daoInsertDtSpg->numdoc       = $arrCabeceraScb['numdoc']; 
				$this->daoInsertDtSpg->codope       = $arrCabeceraScb['codope']; 
				$this->daoInsertDtSpg->estmov       = $arrCabeceraScb['estmov']; 
				$this->daoInsertDtSpg->codestpro    = $arrdetallespg['codestpro'][$i];
				$this->daoInsertDtSpg->spg_cuenta   = $arrdetallespg['spgcuenta'][$i];
				$this->daoInsertDtSpg->documento    = $arrdetallespg['documento'][$i];
				$this->daoInsertDtSpg->desmov       = $arrdetallespg['desmov'][$i];
				$this->daoInsertDtSpg->procede_doc  = $arrdetallespg['procede_doc'][$i];
				$this->daoInsertDtSpg->monto        = $arrdetallespg['monto'][$i];
				$this->daoInsertDtSpg->operacion    = $arrdetallespg['operacion'][$i];
				$this->daoInsertDtSpg->estcla       = $arrdetallespg['estcla'][$i];
				$this->daoInsertDtSpg->codfuefin    = $ls_codfuefin; 
				$this->daoInsertDtSpg->codcencos = '---';
				
				//Insertamos la fuente de financiamiento del banco
				$resultado = $this->daoInsertDtSpg->incluir();
				
				//Inicializamos el arreglo de eventos
				$servicioEvento = new ServicioEvento();
				$servicioEvento->evento=$arrevento['evento'];
				$servicioEvento->codemp=$arrevento['codemp'];
				$servicioEvento->codsis=$arrevento['codsis'];
				$servicioEvento->nomfisico=$arrevento['nomfisico'];
				$servicioEvento->desevetra='Incluyo la cuenta '.$this->daoInsertDtSpg->codestpro.'::'.$this->daoInsertDtSpg->estcla.'::'.$this->daoInsertDtSpg->spg_cuenta.' en el movimiento de banco '.$this->daoInsertDtSpg->codban.'::'.$this->daoInsertDtSpg->ctaban.'::'.$this->daoInsertDtSpg->numdoc.'::'.$this->daoInsertDtSpg->codope; 
				if ($resultado)
				{
					$lb_valido=true;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoInsertDtSpg->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$this->mensaje .= $this->daoInsertDtSpg->ErrorMsg();
				}
			}
			else
			{ 
				$ldec_actual=$this->uf_select_dt_spg_montoac($arrCabeceraScb,$arrdetallespg["spgcuenta"][$i],$arrdetallespg["codestpro"][$i],
												   $arrdetallespg["documento"][$i],$arrdetallespg["estcla"][$i]);
				$ldec_monto=$ldec_monto+$ldec_actual;
				
				$ls_codemp 		 = $arrCabeceraScb["codemp"];
				$ls_codban 		 = $arrCabeceraScb["codban"];
				$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
				$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
				$ls_codope 		 = $arrCabeceraScb["codope"];
				$ls_estmov 		 = $arrCabeceraScb["estmov"]; 
				$ls_programa    = $arrdetallespg['codestpro'][$i];
				$ls_spgcuenta   = $arrdetallespg['spgcuenta'][$i];
				$ls_documento   = $arrdetallespg['documento'][$i];
				$ls_estcla       = $arrdetallespg['estcla'][$i];
				
				$strPk = "codemp='{$ls_codemp}' AND codban='{$ls_codban}'".
				         "AND ctaban='{$ls_ctaban}' AND numdoc='{$ls_numdoc}'". 
						 "AND codope='{$ls_codope}' AND estmov='{$ls_estmov}'".
						 "AND codestpro='{$ls_programa}' AND spg_cuenta='{$ls_spgcuenta}'".
						 "AND documento='{$ls_documento}' AND estcla='{$ls_estcla}' ".
						 "AND codfuefin='{$ls_codfuefin}'";

				$this->daoUpdateDtSpg = FabricaDao::CrearDAO("C", "scb_movbco_spg", '', $strPk);
				$this->daoUpdateDtSpg->monto = $ldec_monto ;
				$resultado = $this->daoUpdateDtSpg->modificar();
				
				if ($resultado)
				{
					$lb_valido=true;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoUpdateDtSpg->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$this->mensaje .= $this->daoUpdateDtSpg->ErrorMsg();
				}
			}
		}
		return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_select_dt_ingreso($arrCabeceraScb,$spicuenta,$codestpro1,$codestpro2,$codestpro3,
										$codestpro4,$codestpro5,$documento,$estcla)
	{
		$ls_codemp 		 = $arrCabeceraScb["codemp"];
		$ls_codban 		 = $arrCabeceraScb["codban"];
		$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
		$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
		$ls_codope 		 = $arrCabeceraScb["codope"];
		$ls_estmov 		 = $arrCabeceraScb["estmov"]; 

		$lb_valido=false;
		
		$cadenasql="SELECT codemp 
			   FROM scb_movbco_spi
			  WHERE codemp='".$ls_codemp."' 
			    AND codban='".$ls_codban."' 
				AND ctaban='".$ls_ctaban."' 
			    AND numdoc='".$ls_numdoc."' 
				AND codope='".$ls_codope."' 
				AND estmov='".$ls_estmov."' 
			    AND spi_cuenta='".$spicuenta."' 
				AND codestpro1='".$codestpro1."'
				AND codestpro2='".$codestpro2."'
				AND codestpro3='".$codestpro3."'
				AND codestpro4='".$codestpro4."'
				AND codestpro5='".$codestpro5."' 
				AND documento='".$documento."'
				AND estcla='".$estcla."'";
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
				$codigo=$resultado->fields["codemp"];
				$lb_valido = true;
				$resultado->MoveNext();
			}
		}
		return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function uf_select_dt_spi_montoac($arrCabeceraScb,$spicuenta,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,
										$documento,$estcla)
	{
		$monto=0;
		$ls_codemp 		 = $arrCabeceraScb["codemp"];
		$ls_codban 		 = $arrCabeceraScb["codban"];
		$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
		$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
		$ls_codope 		 = $arrCabeceraScb["codope"];
		$ls_estmov 		 = $arrCabeceraScb["estmov"]; 

		$lb_valido=true;
		
		$cadenasql="SELECT monto 
			   FROM scb_movbco_spi
			 WHERE codemp='".$ls_codemp."' 
			    AND codban='".$ls_codban."' 
				AND ctaban='".$ls_ctaban."' 
			    AND numdoc='".$ls_numdoc."' 
				AND codope='".$ls_codope."' 
				AND estmov='".$ls_estmov."' 
			    AND spi_cuenta='".$spicuenta."' 
				AND codestpro1='".$codestpro1."'
				AND codestpro2='".$codestpro2."'
				AND codestpro3='".$codestpro3."'
				AND codestpro4='".$codestpro4."'
				AND codestpro5='".$codestpro5."' 
				AND documento='".$documento."'
				AND estcla='".$estcla."'";
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
				$monto=$resultado->fields["monto"];
				$lb_valido = true;
				$resultado->MoveNext();
			}
		}
		return $monto;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function  guardarDetalleSPI($arrCabeceraScb,$arrdetallespi,$arrevento)
	{
		$ls_codfuefin = $arrCabeceraScb['codfuefin'];
		if ($ls_codfuefin=='')
		{
			$ls_codfuefin='--';
		}
		$lb_valido=true;
		$totalspi = count($arrdetallespi["spicuenta"]);
		for ($i=0;$i<$totalspi;$i++)
		{
			
			$ldec_monto= $arrdetallespi["monto"][$i];
			$lb_valido = $this->uf_select_dt_ingreso($arrCabeceraScb,$arrdetallespi["spicuenta"][$i],$arrdetallespi["codestpro1"][$i],
								 $arrdetallespi["codestpro2"][$i],$arrdetallespi["codestpro3"][$i],$arrdetallespi["codestpro4"][$i],
								 $arrdetallespi["codestpro5"][$i],$arrdetallespi["documento"][$i],$arrdetallespi["estcla"][$i]);
			if(!$lb_valido)
			{
				$resultado=false;
				//Creación del Dao con la tabla y campos a insertar.
				$this->daoInsertDtSpi=FabricaDao::CrearDAO("N","scb_movbco_spi");
				$this->daoInsertDtSpi->codemp       = $arrCabeceraScb['codemp'];
				$this->daoInsertDtSpi->codban       = $arrCabeceraScb['codban']; 
				$this->daoInsertDtSpi->ctaban       = $arrCabeceraScb['ctaban']; 
				$this->daoInsertDtSpi->numdoc       = $arrCabeceraScb['numdoc']; 
				$this->daoInsertDtSpi->codope       = $arrCabeceraScb['codope']; 
				$this->daoInsertDtSpi->estmov       = $arrCabeceraScb['estmov']; 
				$this->daoInsertDtSpi->codestpro1   = $arrdetallespi['codestpro1'][$i];
				$this->daoInsertDtSpi->codestpro2   = $arrdetallespi['codestpro2'][$i];
				$this->daoInsertDtSpi->codestpro3   = $arrdetallespi['codestpro3'][$i];
				$this->daoInsertDtSpi->codestpro4   = $arrdetallespi['codestpro4'][$i];
				$this->daoInsertDtSpi->codestpro5   = $arrdetallespi['codestpro5'][$i];
				$this->daoInsertDtSpi->spi_cuenta   = $arrdetallespi['spicuenta'][$i];
				$this->daoInsertDtSpi->documento    = $arrdetallespi['documento'][$i];
				$this->daoInsertDtSpi->desmov       = $arrdetallespi['desmov'][$i];
				$this->daoInsertDtSpi->procede_doc  = $arrdetallespi['procede_doc'][$i];
				$this->daoInsertDtSpi->monto        = $arrdetallespi['monto'][$i];
				$this->daoInsertDtSpi->operacion    = $arrdetallespi['operacion'][$i];
				$this->daoInsertDtSpi->estcla       = $arrdetallespi['estcla'][$i];
				$this->daoInsertDtSpi->codfuefin    = $ls_codfuefin; 
				$this->daoInsertDtSpi->codcencos    = '---'; 
				//Insertamos la fuente de financiamiento del banco
				$resultado = $this->daoInsertDtSpi->incluir();
				
				//Inicializamos el arreglo de eventos
				$servicioEvento = new ServicioEvento();
				$servicioEvento->evento=$arrevento['evento'];
				$servicioEvento->codemp=$arrevento['codemp'];
				$servicioEvento->codsis=$arrevento['codsis'];
				$servicioEvento->nomfisico=$arrevento['nomfisico'];
				$servicioEvento->desevetra='Incluyo la cuenta '.$this->daoInsertDtSpi->spi_cuenta.' en el movimiento de banco '.$this->daoInsertDtSpi->codban.'::'.$this->daoInsertDtSpi->ctaban.'::'.$this->daoInsertDtSpi->numdoc.'::'.$this->daoInsertDtSpi->codope; 
				if ($resultado)
				{
					$lb_valido=true;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoInsertDtSpi->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$this->mensaje .= $this->daoInsertDtSpi->ErrorMsg();
				}
			}
			else
			{ 
				$ldec_actual=$this->uf_select_dt_spi_montoac($arrCabeceraScb,$arrdetallespi["spicuenta"][$i],$arrdetallespi["codestpro1"][$i],
									     $arrdetallespi["codestpro2"][$i],$arrdetallespi["codestpro3"][$i],$arrdetallespi["codestpro4"][$i],
									     $arrdetallespi["codestpro5"][$i],$arrdetallespi["documento"][$i],$arrdetallespi["estcla"][$i]);
                                
                                $ldec_monto= number_format($ldec_monto+$ldec_actual,2,".","");
                                
				$ls_codemp 		 = $arrCabeceraScb["codemp"];
				$ls_codban 		 = $arrCabeceraScb["codban"];
				$ls_ctaban 		 = $arrCabeceraScb["ctaban"];
				$ls_numdoc 		 = $arrCabeceraScb["numdoc"];
				$ls_codope 		 = $arrCabeceraScb["codope"];
				$ls_estmov 		 = $arrCabeceraScb["estmov"]; 
				$ls_programa1    = $arrdetallespi['codestpro1'][$i];
				$ls_programa2    = $arrdetallespi['codestpro2'][$i];
				$ls_programa3    = $arrdetallespi['codestpro3'][$i];
				$ls_programa4    = $arrdetallespi['codestpro4'][$i];
				$ls_programa5    = $arrdetallespi['codestpro5'][$i];
				$ls_spgcuenta    = $arrdetallespi['spicuenta'][$i];
				$ls_documento    = $arrdetallespi['documento'][$i];
				$ls_estcla       = $arrdetallespi['estcla'][$i];
				
				$strPk = "codemp='{$ls_codemp}' AND codban='{$ls_codban}'".
				         " AND ctaban='{$ls_ctaban}' AND numdoc='{$ls_numdoc}'". 
						 " AND codope='{$ls_codope}' AND estmov='{$ls_estmov}'".
						 " AND codestpro1='{$ls_programa1}' AND codestpro2='{$ls_programa2}'".
						 " AND codestpro3='{$ls_programa3}' AND codestpro4='{$ls_programa4}'".
						 " AND codestpro5='{$ls_programa5}' AND spi_cuenta='{$ls_spgcuenta}'".
						 " AND documento='{$ls_documento}' AND estcla='{$ls_estcla}' ";

				$this->daoUpdateDtSpi = FabricaDao::CrearDAO("C", "scb_movbco_spi", '', $strPk);
				$this->daoUpdateDtSpi->monto = $ldec_monto ;
				$resultado = $this->daoUpdateDtSpi->modificar();
				
				if ($resultado)
				{
					$lb_valido=true;
					$servicioEvento->tipoevento=true;
					$servicioEvento->incluirEvento();
				}
				else
				{
					$lb_valido=false;
					$arrevento ['desevetra'] = $this->daoUpdateDtSpi->ErrorMsg();
					$servicioEvento->tipoevento=false;
					$servicioEvento->desevetra=$arrevento['desevetra'];
					$servicioEvento->incluirEvento();
					$this->mensaje .= $this->daoUpdateDtSpi->ErrorMsg();
				}
			}
		}
		return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function actualizar_estatus_ch($ls_codban,$ls_ctaban,$ls_numdoc,$ls_numchequera)
	{
		$lb_valido = true;
		if (!empty($ls_numdoc)&(!empty($ls_numchequera)))
	    { 
			$cadenasql = " SELECT numche ".
						 " FROM scb_cheques ".
						 " WHERE codban='".$ls_codban."' ".
						 " AND ctaban='".$ls_ctaban."' ".
						 " AND numche='".$ls_numdoc."' ".
						 " AND numchequera='".$ls_numchequera."'";
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
				 	$cadenasql = " UPDATE scb_cheques ".
						  	 " SET estche=1 ".
						   	" WHERE codban='".$ls_codban."' ".
						   	" AND ctaban='".$ls_ctaban."' ".
						   	" AND numche='".$ls_numdoc."' ".
						  	 " AND numchequera='".$ls_numchequera."'";
					$conexionbd = ConexionBaseDatos::getInstanciaConexion();
					$resultado = $conexionbd->Execute ( $cadenasql );
					if ($resultado===false)
					{
						$mensaje .= '  ->'.$conexionbd->ErrorMsg();
						$lb_valido = false;
					}
					else
					{
						$lb_valido = true;
					}
				}
			}
	  	}
	  	else
	  	{
	    	$lb_valido = true;
	  	}
	return $lb_valido;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	public function GuardarAutomatico($arrCabeceraScb,$arrDetalleScg,$arrDetalleSpg,$arrDetalleSpi,$arrevento) 
	{
		$this->valido=false;
		if ($arrCabeceraScb['monto']=="")
		{
			$arrCabeceraScb['monto']=0;
		}
		if ($arrCabeceraScb['monobjret']=="")
		{
			$arrCabeceraScb['monobjret']=0;
		}
		if ($arrCabeceraScb['monret']=="")
		{
			$arrCabeceraScb['monret']=0;
		}
		if ($arrCabeceraScb['codope']=="DP")
		{
			$arrCabeceraScb['procede']='SCBBDP';
		}
		if (($arrCabeceraScb['codope']=="CH")&&($arrCabeceraScb['chevau']!=""))
	    {
		 	$arrCabeceraScb['chevau']= str_pad($arrCabeceraScb['chevau'],25,"0",STR_PAD_LEFT);
	    }
		if (empty($arrCabeceraScb['numordpagmin']))
	    {
	    	$arrCabeceraScb['numordpagmin'] = '-';
	   	}
	   	if (empty($arrCabeceraScb['codtipfon']))
		{
		    $arrCabeceraScb['codtipfon'] = '----';
		}
		if (strlen($arrCabeceraScb['nomproben'])>100)
		{
		    $arrCabeceraScb['nomproben']=substr($arrCabeceraScb['nomproben'],0,100);
		}
		if ($arrCabeceraScb['tranoreglib']=='1')
		{
			$arrCabeceraScb['estreglib']='A';
		}
		if ($arrCabeceraScb['numconint']=='')
		{
			if(($arrCabeceraScb['codope']=="NC")||($arrCabeceraScb['codope']=="ND")||($arrCabeceraScb['codope']=="DP"))
			{
				$procede="SCBBRE";
			}
			else
			{
				$procede="SCBBCH";
			}
			$arrCabeceraScb['numconint']=$this->buscarNroControlInterno($arrCabeceraScb['codemp'],$procede);
		}
		if ($arrCabeceraScb['codmon']=="")
		{
			$arrCabeceraScb['codmon']="---";
		}
		if ($arrCabeceraScb['tascam']=="")
		{
			$arrCabeceraScb['tascam']=1;
		}
		if ($arrCabeceraScb['montot']=="")
		{
			$arrCabeceraScb['montot']=$arrCabeceraScb['monto'];
		}
		
		$resultado = 0;
		$this->daoMovbancarios = FabricaDao::CrearDAO('N', 'scb_movbco');
		$this->daoMovbancarios->codemp      = $arrCabeceraScb['codemp'];
		$this->daoMovbancarios->codban      = $arrCabeceraScb['codban'];
		$this->daoMovbancarios->ctaban      = $arrCabeceraScb['ctaban'];
		$this->daoMovbancarios->numdoc      = $arrCabeceraScb['numdoc'];
		$this->daoMovbancarios->codope      = $arrCabeceraScb['codope'];
		$this->daoMovbancarios->fecmov      = $arrCabeceraScb['fecmov'];
		$this->daoMovbancarios->conmov      = $arrCabeceraScb['conmov'];
		$this->daoMovbancarios->codconmov   = $arrCabeceraScb['codconmov'];
		$this->daoMovbancarios->cod_pro     = $arrCabeceraScb['cod_pro'];
		$this->daoMovbancarios->ced_bene    = $arrCabeceraScb['ced_bene'];
		$this->daoMovbancarios->nomproben   = $arrCabeceraScb['nomproben'];
		$this->daoMovbancarios->monto       = $arrCabeceraScb['monto'];
		$this->daoMovbancarios->monobjret   = $arrCabeceraScb['monobjret'];
		$this->daoMovbancarios->monret      = $arrCabeceraScb['monret'];
		$this->daoMovbancarios->chevau      = $arrCabeceraScb['chevau'];
		$this->daoMovbancarios->estmov      = $arrCabeceraScb['estmov'];
		$this->daoMovbancarios->estmovint   = $arrCabeceraScb['estmovint'];
		$this->daoMovbancarios->estcobing   = $arrCabeceraScb['estcobing'];
		$this->daoMovbancarios->estbpd      = $arrCabeceraScb['estbpd'];
		$this->daoMovbancarios->esttra      = 0;
		$this->daoMovbancarios->feccon      = '1900-01-01';
		$this->daoMovbancarios->codbansig   = '---';
		$this->daoMovbancarios->estcondoc   = 'S';
		$this->daoMovbancarios->procede     = $arrCabeceraScb['procede'];
		$this->daoMovbancarios->estreglib   = $arrCabeceraScb['estreglib'];
		$this->daoMovbancarios->tipo_destino= $arrCabeceraScb['tipo_destino'];
		$this->daoMovbancarios->numordpagmin= $arrCabeceraScb['numordpagmin'];
		$this->daoMovbancarios->codfuefin   = $arrCabeceraScb['codfuefin'];
		$this->daoMovbancarios->codtipfon   = $arrCabeceraScb['codtipfon'];
		$this->daoMovbancarios->estmovcob   = $arrCabeceraScb['estmovcob'];
		$this->daoMovbancarios->estmodordpag = '';
		$this->daoMovbancarios->numconint   = $arrCabeceraScb['numconint'];
		$this->daoMovbancarios->tranoreglib = $arrCabeceraScb['tranoreglib'];
		$this->daoMovbancarios->numcarord 	= $arrCabeceraScb['numcarord'];
		$this->daoMovbancarios->codusu      = $arrCabeceraScb['codusu'];
//		$this->daoMovbancarios->estmodordpag = $arrCabeceraScb['estmodordpag'];
		$this->daoMovbancarios->coduniadmsig = $arrCabeceraScb['coduniadmsig'];
		if ($arrCabeceraScb['codbansig']!="")
		{
			$this->daoMovbancarios->codbansig = $arrCabeceraScb['codbansig'];
		}
		$this->daoMovbancarios->estserext = $arrCabeceraScb['estserext'];
		$this->daoMovbancarios->tipdocressig = $arrCabeceraScb['tipdocressig'];
		$this->daoMovbancarios->numdocressig = $arrCabeceraScb['numdocressig'];
		$this->daoMovbancarios->fecordpagsig = $arrCabeceraScb['fecordpagsig'];
		$this->daoMovbancarios->forpagsig = $arrCabeceraScb['forpagsig'];
		$this->daoMovbancarios->medpagsig = $arrCabeceraScb['medpagsig'];
		$this->daoMovbancarios->codestprosig = $arrCabeceraScb['codestprosig'];
		$this->daoMovbancarios->nrocontrolop = $arrCabeceraScb['nrocontrolop'];
		$this->daoMovbancarios->codmon = $arrCabeceraScb['codmon'];
		$this->daoMovbancarios->tascam = $arrCabeceraScb['tascam'];
		$this->daoMovbancarios->montot = $arrCabeceraScb['montot'];
                
		$this->daoMovbancarios->estcon = 0;
		$this->daoMovbancarios->estimpche = 0;
		$this->daoMovbancarios->fecha = '1900-01-01';
		$this->daoMovbancarios->emicheproc = 0;
		$this->daoMovbancarios->emichefec = '1900-01-01';
		$this->daoMovbancarios->aliidb = 0;
		$this->daoMovbancarios->numpolcon = 0;
		$this->daoMovbancarios->fechaconta = '1900-01-01';
		$this->daoMovbancarios->fechaanula = '1900-01-01';
		$this->daoMovbancarios->estant = 0;
		$this->daoMovbancarios->docant = '---------------';
		$this->daoMovbancarios->monano = 0;
		$this->daoMovbancarios->estapribs = 0;
		$this->daoMovbancarios->estxmlibs = 0;
		$this->daoMovbancarios->fecenvfir = '1900-01-01';
		$this->daoMovbancarios->fecenvcaj = '1900-01-01';
		$this->daoMovbancarios->docdestrans = '---------------';
		$this->daoMovbancarios->tiptrans = 0;
		$this->daoMovbancarios->codcencos = '---';
		if($this->valido)
		{
			if($this->validaFechaPeriodo($this->daoMovbancarios->fecmov,$this->daoMovbancarios->codemp))
			{
				//$this->valido = $this->daoMovbancarios->incluir(true,"numconint",true,15,true); Se comento por que el campo numconint no es PK y causa error
				$this->valido = $this->daoMovbancarios->incluir();
				if($this->valido)
				{
					if($this->valido)
					{
						$this->valido = $this->uf_insert_fuentefinancimiento($arrCabeceraScb,$arrevento);	
					}
					if((count($arrDetalleScg)>0)&&($this->valido))
					{
						// incluir detalles de Contabilidad 
						$this->valido= $this->guardarDetalleSCG($arrCabeceraScb,$arrDetalleScg,$arrevento);
					}
					if((count($arrDetalleSpg)>0)&&($this->valido))
					{
						// incluir detalles de Presupuesto de Gasto
						$this->valido= $this->guardarDetalleSPG($arrCabeceraScb,$arrDetalleSpg,$arrevento);
					}
					if((count($arrDetalleSpi)>0)&&($this->valido))
					{
						// incluir detalles de Presupuesto de Ingreso
						$this->valido= $this->guardarDetalleSPI($arrCabeceraScb,$arrDetalleSpi,$arrevento); 
					}
					if(($this->valido)&&($this->daoMovbancarios->codope=='CH'))
					{
						$this->valido= $this->actualizar_estatus_ch($this->daoMovbancarios->codban,$this->daoMovbancarios->ctaban,$this->daoMovbancarios->numdoc,$arrCabeceraScb["numchequera"]);
					}
				}
				else
				{
					$this->mensaje .= 'El documento '.$this->daoMovbancarios->numdoc.' No puede ser incluido.';
					$this->mensaje .= $this->daoMovbancarios->ErrorMsg();
					$this->valido = false;	
				}
			}
			else
			{
				$this->mensaje .=  'Verifique que el periodo y el mes de la empresa estén abiertos.'.$this->daoMovbancarios->fecmov;
				$this->valido = false;
			}
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Incluyo el movimiento de banco '.$this->daoMovbancarios->numdoc.'::'.$this->daoMovbancarios->codban.'::'.$this->daoMovbancarios->ctaban;			
		if ($this->valido) 
		{
			$servicioEvento->incluirEvento();
		}
		else
		{
			$servicioEvento->desevetra=$this->mensaje;
			$servicioEvento->incluirEvento();
		}
		unset($servicioEvento);
		return $this->valido;
	}
}
?>