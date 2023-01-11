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

$dirsrvcfg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrvcfg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvcfg."/modelo/servicio/cfg/sigesp_srv_cfg_spg_iestructurafuente.php");
require_once($dirsrvcfg.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');

class ServicioEstructuraFuente implements IServicioEstructuraFuente
{
	private $daoCasamientoEstructuraFuente;
	private $daoRegistroEvento;
	private $daoGenerico;
	private $conexionBaseDatos;
	
	public function __construct()
	{
		$this->daoCasamientoEstructuraFuente = null;
		$this->daoRegistroEvento = new ServicioEvento();
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function buscarFuentes($codemp)
	{
		$cadenaSQL = "SELECT * ".
					 "  FROM sigesp_fuentefinanciamiento ".
					 " WHERE codemp='{$codemp}' ".
					 "	 AND codfuefin<>'--' ".
					 " ORDER BY codfuefin";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function grabarCasamientoEstructuraFuente($codemp, $arrjson, $arrEvento)
	{
		$resultado = '0|';
		DaoGenerico::iniciarTrans();
		
		$fuentesInsertar = $arrjson->arrFuentesIncluir;
		$numFueInc = count((array)$fuentesInsertar);
		for ($i = 0; $i < $numFueInc; $i++)
		{
			$this->daoCasamientoEstructuraFuente = FabricaDao::CrearDAO('N','spg_dt_fuentefinanciamiento');
			$this->daoCasamientoEstructuraFuente->setData($fuentesInsertar[$i]);
			$this->daoCasamientoEstructuraFuente->codemp = $codemp;
			if(!$this->daoCasamientoEstructuraFuente->incluir(false,'',false,0,true))
			{
				break;
			}
			else
			{
				$this->daoRegistroEvento->evento="INSERTAR";
				$this->daoRegistroEvento->codusu=$_SESSION["la_logusr"];
				$this->daoRegistroEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$this->daoRegistroEvento->codsis="CFG";
				$this->daoRegistroEvento->nomfisico="sigesp_vis_cfg_spg_estructurafuente.php";
				$this->daoRegistroEvento->desevetra='Inserto el casamiento estructura-fuente {$fuentesInsertar[$i]->codestpro1} - '.
										   '{$fuentesInsertar[$i]->codestpro2} - {$fuentesInsertar[$i]->codestpro3} - '.
										   '{$fuentesInsertar[$i]->codestpro4} - {$fuentesInsertar[$i]->codestpro5} - '.
										   'codigo fuente {$fuentesInsertar[$i]->codfuefin} asociada a la empresa {$codemp}';;	
				$this->daoRegistroEvento->tipoevento=true;
				$this->daoRegistroEvento->incluirEvento();
			}
			unset($this->daoCasamientoEstructuraFuente);
		}
		
		//Copiar datos en la tabla spg_cuenta_fuentefinanciamiento *
		switch ($_SESSION["ls_gestor"])
		{
			case "oci8po":
				$cadenafuentefinanciamiento = $this->conexionBaseDatos->Concat('spg_dt_fuentefinanciamiento.codestpro1','spg_dt_fuentefinanciamiento.codestpro2','spg_dt_fuentefinanciamiento.codestpro3','spg_dt_fuentefinanciamiento.codestpro4','spg_dt_fuentefinanciamiento.codestpro5','spg_dt_fuentefinanciamiento.estcla','spg_cuentas.spg_cuenta','spg_dt_fuentefinanciamiento.codfuefin');
				$cadenacuentafuentefinanciamiento = $this->conexionBaseDatos->Concat('spg_cuenta_fuentefinan.codestpro1','spg_cuenta_fuentefinan.codestpro2','spg_cuenta_fuentefinan.codestpro3','spg_cuenta_fuentefinan.codestpro4','spg_cuenta_fuentefinan.codestpro5','spg_cuenta_fuentefinan.estcla','spg_cuenta_fuentefinan.spg_cuenta','spg_cuenta_fuentefinan.codfuefin');
				$strSql = "INSERT INTO spg_cuenta_fuentefinan (codemp,codfuefin,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta, monto) ".
						  "SELECT spg_dt_fuentefinanciamiento.codemp,spg_dt_fuentefinanciamiento.codfuefin,spg_dt_fuentefinanciamiento.codestpro1,spg_dt_fuentefinanciamiento.codestpro2, ".
						  " 	  spg_dt_fuentefinanciamiento.codestpro3,spg_dt_fuentefinanciamiento.codestpro4,spg_dt_fuentefinanciamiento.codestpro5,spg_dt_fuentefinanciamiento.estcla,spg_cuentas.spg_cuenta, 1 ".
						  "  FROM spg_cuentas ".
						  "  INNER JOIN spg_dt_fuentefinanciamiento ".
						  "     ON spg_dt_fuentefinanciamiento.codemp=spg_cuentas.codemp ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro1=spg_cuentas.codestpro1 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro2=spg_cuentas.codestpro2 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro3=spg_cuentas.codestpro3 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro4=spg_cuentas.codestpro4 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro5=spg_cuentas.codestpro5 ".
						  "    AND spg_dt_fuentefinanciamiento.estcla=spg_cuentas.estcla ".
						  "  WHERE  ".$cadenafuentefinanciamiento. 
						  "  	   NOT IN (SELECT  ".$cadenacuentafuentefinanciamiento.
						  "  							FROM spg_cuenta_fuentefinan, spg_cuentas ".
						  "  						   WHERE spg_cuenta_fuentefinan.codestpro1=spg_cuentas.codestpro1 ".
						  "  							 AND spg_cuenta_fuentefinan.codestpro2=spg_cuentas.codestpro2 ".
						  "  							 AND spg_cuenta_fuentefinan.codestpro3=spg_cuentas.codestpro3 ".
						  "  							 AND spg_cuenta_fuentefinan.codestpro4=spg_cuentas.codestpro4 ".
						  "  							 AND spg_cuenta_fuentefinan.codestpro5=spg_cuentas.codestpro5 ".
						  "  							 AND spg_cuenta_fuentefinan.estcla=spg_cuentas.estcla ".
						  "  							 AND spg_cuenta_fuentefinan.spg_cuenta=spg_cuentas.spg_cuenta)";
				break;
				
			default:
				$cadenafuentefinanciamiento = $this->conexionBaseDatos->Concat('spg_dt_fuentefinanciamiento.codestpro1','spg_dt_fuentefinanciamiento.codestpro2','spg_dt_fuentefinanciamiento.codestpro3','spg_dt_fuentefinanciamiento.codestpro4','spg_dt_fuentefinanciamiento.codestpro5','spg_dt_fuentefinanciamiento.estcla','spg_cuentas.spg_cuenta','spg_dt_fuentefinanciamiento.codfuefin');
				$cadenacuentafuentefinanciamiento = $this->conexionBaseDatos->Concat('spg_cuenta_fuentefinanciamiento.codestpro1','spg_cuenta_fuentefinanciamiento.codestpro2','spg_cuenta_fuentefinanciamiento.codestpro3','spg_cuenta_fuentefinanciamiento.codestpro4','spg_cuenta_fuentefinanciamiento.codestpro5','spg_cuenta_fuentefinanciamiento.estcla','spg_cuenta_fuentefinanciamiento.spg_cuenta','spg_cuenta_fuentefinanciamiento.codfuefin');
				$strSql = "INSERT INTO spg_cuenta_fuentefinanciamiento (codemp,codfuefin,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta, monto) ".
						  "SELECT spg_dt_fuentefinanciamiento.codemp,spg_dt_fuentefinanciamiento.codfuefin,spg_dt_fuentefinanciamiento.codestpro1,spg_dt_fuentefinanciamiento.codestpro2, ".
						  " 	  spg_dt_fuentefinanciamiento.codestpro3,spg_dt_fuentefinanciamiento.codestpro4,spg_dt_fuentefinanciamiento.codestpro5,spg_dt_fuentefinanciamiento.estcla,spg_cuentas.spg_cuenta, 0 ".
						  "  FROM spg_cuentas ".
						  "  INNER JOIN spg_dt_fuentefinanciamiento ".
						  "     ON spg_dt_fuentefinanciamiento.codemp=spg_cuentas.codemp ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro1=spg_cuentas.codestpro1 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro2=spg_cuentas.codestpro2 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro3=spg_cuentas.codestpro3 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro4=spg_cuentas.codestpro4 ".
						  "    AND spg_dt_fuentefinanciamiento.codestpro5=spg_cuentas.codestpro5 ".
						  "    AND spg_dt_fuentefinanciamiento.estcla=spg_cuentas.estcla ".
						  "  WHERE  ".$cadenafuentefinanciamiento. 
						  "  	   NOT IN (SELECT  ".$cadenacuentafuentefinanciamiento.
						  "  							FROM spg_cuenta_fuentefinanciamiento, spg_cuentas ".
						  "  						   WHERE spg_cuenta_fuentefinanciamiento.codestpro1=spg_cuentas.codestpro1 ".
						  "  							 AND spg_cuenta_fuentefinanciamiento.codestpro2=spg_cuentas.codestpro2 ".
						  "  							 AND spg_cuenta_fuentefinanciamiento.codestpro3=spg_cuentas.codestpro3 ".
						  "  							 AND spg_cuenta_fuentefinanciamiento.codestpro4=spg_cuentas.codestpro4 ".
						  "  							 AND spg_cuenta_fuentefinanciamiento.codestpro5=spg_cuentas.codestpro5 ".
						  "  							 AND spg_cuenta_fuentefinanciamiento.estcla=spg_cuentas.estcla ".
						  "  							 AND spg_cuenta_fuentefinanciamiento.spg_cuenta=spg_cuentas.spg_cuenta)";
				break;
		}
		$this->conexionBaseDatos->Execute($strSql);
		
		$fuenteEliminar = $arrjson->arrFuentesEliminar;
		$numFueEli   = count((array)$fuenteEliminar);
		$fuenteError = '';
		for ($i = 0; $i < $numFueEli; $i++)
		{
			if(!$this->validarEliminar($codemp, $fuenteEliminar[$i]))
			{
				$strSql = "DELETE ".
						  "  FROM spg_cuenta_fuentefinanciamiento ".
						  "	WHERE codemp='{$codemp}' ".
						  "   AND codfuefin='{$fuenteEliminar[$i]->codfuefin}' ".
						  "	  AND codestpro1='{$fuenteEliminar[$i]->codestpro1}' ".
						  "   AND codestpro2='{$fuenteEliminar[$i]->codestpro2}' ".
						  "	  AND codestpro3='{$fuenteEliminar[$i]->codestpro3}' ".
						  "   AND codestpro4='{$fuenteEliminar[$i]->codestpro4}' ".
						  "	  AND codestpro5='{$fuenteEliminar[$i]->codestpro5}' ".
						  "   AND estcla='{$fuenteEliminar[$i]->estcla}'";
				if($this->conexionBaseDatos->Execute($strSql))
				{
					$this->daoCasamientoEstructuraFuente = FabricaDao::CrearDAO('N','spg_dt_fuentefinanciamiento');
					$this->daoCasamientoEstructuraFuente->setData($fuenteEliminar[$i]);
					$this->daoCasamientoEstructuraFuente->codemp = $codemp;
					if(!$this->daoCasamientoEstructuraFuente->eliminar())
					{
						break;
					}
					else
					{
						$this->daoRegistroEvento->evento="ELIMINAR";
						$this->daoRegistroEvento->codusu=$_SESSION["la_logusr"];
						$this->daoRegistroEvento->codemp=$_SESSION["la_empresa"]["codemp"];
						$this->daoRegistroEvento->codsis="CFG";
						$this->daoRegistroEvento->nomfisico="sigesp_vis_cfg_spg_estructurafuente.php";
						$this->daoRegistroEvento->desevetra= 'Elimino la estructura {$fuenteEliminar[$i]->codestpro1} - '.
															 '{$fuenteEliminar[$i]->codestpro2} - {$fuenteEliminar[$i]->codestpro3} - '.
															 '{$fuenteEliminar[$i]->codestpro4} - {$fuenteEliminar[$i]->codestpro5} - '.
															 'codigo fuente {$fuenteEliminar[$i]->codfuefin} de estructuras asociada a la empresa {$codemp}';
						$this->daoRegistroEvento->tipoevento=true;
						$this->daoRegistroEvento->incluirEvento();
					}
					unset($this->daoValidacionEstructura);
				}
			}
			else
			{
				if ($fuenteError=='')
				{
					$fuenteError .= $fuenteEliminar[$i]->codfuefin;
				}
				else
				{
					$fuenteError .= ','.$fuenteEliminar[$i]->codfuefin;
				}
			}
		}
		
		
		if (DaoGenerico::completarTrans())
		{
			$resultado = '1|'.$fuenteError;
		}
		else
		{
			$resultado .= $fuenteError;		
		}

		return $resultado;
	}
	
	public function buscarCasamiento($codemp, $codest1, $codest2, $codest3, $codest4, $codest5, $estcla)
	{
		$cadenaSQL = "SELECT DT.codfuefin, FF.denfuefin, '0' as registrocat ".
   					 "	FROM spg_dt_fuentefinanciamiento DT ".
					 " INNER JOIN sigesp_fuentefinanciamiento FF  ".
					 "	  ON DT.codfuefin=FF.codfuefin ".
					 " WHERE DT.codemp='{$codemp}' ".
					 "	 AND DT.codestpro1='{$codest1}' ".
					 "	 AND DT.codestpro2='{$codest2}' ". 
					 "	 AND DT.codestpro3='{$codest3}' ".
					 "	 AND DT.codestpro4='{$codest4}' ". 
					 "	 AND DT.codestpro5='{$codest5}' ". 
					 "	 AND DT.estcla='{$estcla}' ".
					 "	 AND DT.codfuefin<>'--'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function validarEliminar($codemp, $fuenteEliminar)
	{
		$this->daogenerico = new DaoGenerico ('spg_dt_fuentefinanciamiento');
		$arrtabla[0] = 'sigesp_fuentefinanciamiento';
		$arrtabla[1] = 'scb_movbco_spg';
		$arrtabla[2] = 'soc_sol_cotizacion';
		$arrtabla[3] = 'sno_nomina';
		$arrtabla[4] = 'sno_thnomina';
		$arrtabla[5] = 'siv_despacho';
		$arrtabla[6] = 'cxp_dc_spg';
		$arrtabla[7] = 'sob_cargovaluacion';
		$arrtabla[8] = 'spg_ep3';
		$arrtabla[9] = 'sob_variacioncontrato';
		$arrtabla[10] = 'cxp_dc_spi';
		$arrtabla[11] = 'cxp_rd_spg';
		$arrtabla[12] = 'sigesp_cmp_md';
		$arrtabla[13] = 'sigesp_cmp_int';
		$arrtabla[14] = 'sob_fuentefinanciamientoobra';
		$arrtabla[15] = 'sob_cargoanticipo';
		$arrtabla[16] = 'sigesp_cmp';
		$arrtabla[17] = 'cxp_solicitudes';
		$arrtabla[18] = 'scb_movbco_spgop';
		$arrtabla[19] = 'sob_contrato';
		$arrtabla[20] = 'scb_movbco_fuefinanciamiento';
		$arrtabla[21] = 'scb_movcol_spg';
		$arrtabla[22] = 'sob_puntodecuenta';
		$arrtabla[23] = 'sob_cargoasignacion';
		$arrtabla[24] = 'sno_hnomina';
		$arrtabla[25] = 'sob_valuacion';
		$arrtabla[26] = 'scb_movbco';
		$arrtabla[27] = 'spg_cuenta_fuentefinanciamiento';
		$arrtabla[28] = 'detalle_acumulado';
		$filtroadicional = " AND codestpro1 = '$fuenteEliminar->codestpro1' ".
						  " AND codestpro2 = '$fuenteEliminar->codestpro2' ".
						  " AND codestpro3 = '$fuenteEliminar->codestpro3' ".
						  " AND codestpro4 = '$fuenteEliminar->codestpro4' ".
						  " AND codestpro5 = '$fuenteEliminar->codestpro5' ".
						  " AND estcla = '$fuenteEliminar->estcla' ";
		$existeRelacion = $this->daogenerico->validarRelacionesPlus('codfuefin',  $fuenteEliminar->codfuefin, $arrtabla, true, '',$filtroadicional);
		if($existeRelacion===false)
		{
			$existeRelacion = false;
		}
		else
		{
			$arrModulos = explode(',', $existeRelacion);
			$totMod = count((array)$arrModulos);
			$Relacion = '';
			for ($i = 0; $i < $totMod; $i++)
			{
				$Relacion = str_replace($arrModulos[$i], '', $Relacion);
				$Relacion .= ', '.$arrModulos[$i];
			}
			$existeRelacion = str_replace(', ,', ',', $Relacion);
		}
		return $existeRelacion;
	}
}