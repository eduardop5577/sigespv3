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

session_start();
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
$sessionvalida = validarSession();
if (($_POST['ObjSon']) && ($sessionvalida))
{
	$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
	require_once($dirsrv.'/base/librerias/php/general/Json.php');
	require_once($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
	require_once('sigesp_ctr_cfg_servicio.php');
	$_SESSION['session_activa'] = time();	

	if ($_POST['ObjSon']) 		
	{
		$submit    = str_replace("\\","",$_POST['ObjSon']);
		$json      = new Services_JSON;	
		$arrjson    = $json->decode($submit);
		$servicioEvento = new ServicioEvento();
		
		switch ($arrjson->oper)
		{
			case 'incluir':
				$oservicio = new ServicioCfg ('sep_conceptos');
				$resultado = $oservicio->guardarConcepto($arrjson, $_SESSION["la_empresa"]["codemp"]);
				unset($oservicio);
				$mensaje='Inserto/Actualizo el concepto sep ' . $arrjson->codconsep . ' Asociado a la empresa '.$_SESSION["la_empresa"]["codemp"];	
				$tipoevento=true;
				$totaleliminar = count((array)$arrjson->cargoseliminar);
				for($x=0; $x<$totaleliminar; $x++)
				{
					$oservicio = new ServicioCfg ('sep_conceptocargos');
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					$oservicio->eliminarDto ($arrjson->cargoseliminar[$x]);
					unset($oservicio);
				}
				if (ServicioCfg::comTransaccion ())
				{
					foreach ($resultado as $valresul => $valor)
					{
						echo"|".$valor;
					}
				}
				else
				{
					echo "|0";
					$mensaje='Error al Insertar/Actualizar el concepto sep ' . $arrjson->codconsep . ' Asociado a la empresa '.$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;
				}
				//armando arreglo de seguridad
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$arrjson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_sep_concepto.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				unset($servicioEvento);
				break;
		
			
			case 'eliminar':
				$oservicio = new ServicioCfg ('sep_conceptos');
				$ultimo=$oservicio->verificarUltimo('codconsep','sep_conceptos'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."'",$arrjson->datoscabecera[0]->codconsep);
				if ($ultimo)
				{
					//instanciado servicio y dao registro evento
					//iniciando transaccion de base de datos
					ServicioCfg::iniTransaccion ();
					//llamado a metodo de guardar...
					$resultado = $oservicio->eliminarConcepto($arrjson, $_SESSION["la_empresa"]["codemp"]);
					//finalizando la transaccion...
					$mensaje='Elimino el concepto sep '.$arrjson->datoscabecera[0]->codconsep.' Asociado a la empresa '.$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=true;
					if (ServicioCfg::comTransaccion ())
					{
						echo"|".$resultado;
					}
					else
					{
						echo "|0";
						$mensaje='Error al eliminar el concepto sep '.$arrjson->datoscabecera[0]->codconsep.' Asociado a la empresa '.$_SESSION["la_empresa"]["codemp"];	
						$tipoevento=false;
					}
				}
				else
				{
					echo "|-8";
					$mensaje='Error al eliminar el concepto sep '.$arrjson->datoscabecera[0]->codconsep.' Asociado a la empresa '.$_SESSION["la_empresa"]["codemp"];	
					$tipoevento=false;				
				}
				//armando arreglo de seguridad
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$arrjson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_sep_concepto.php";
				$servicioEvento->desevetra=$mensaje;	
				$servicioEvento->tipoevento=$tipoevento;
				$servicioEvento->incluirEvento();
				unset($servicioEvento);
				unset($oservicio);
				break;
			
			case 'nuevo' :
				$oservicio = new ServicioCfg ('sep_conceptos');
				$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
				$resultado  = $oservicio->buscarCodigoSepConcepto();
				echo $json->encode($resultado);
				unset($oservicio);
				unset($resultado); 
				break;
			
			case 'catalogo':
				$oservicio = new ServicioCfg ('sep_conceptos');
				$cadenasql ="SELECT sep_conceptos.codconsep, MAX(sep_conceptos.denconsep) AS denconsep, MAX(sep_conceptos.monconsepe) AS monconsepe, MAX(sep_conceptos.obsconesp) AS obsconesp, ".
							"		MAX(sep_conceptos.spg_cuenta) AS spg_cuenta, MAX(spg_cuentas.denominacion) AS denominacion ".
							"  FROM sep_conceptos ".
							" INNER JOIN spg_cuentas ".
							"    ON sep_conceptos.codemp=spg_cuentas.codemp ".
							"   AND sep_conceptos.spg_cuenta=spg_cuentas.spg_cuenta ".
							" WHERE sep_conceptos.codemp='".$_SESSION["la_empresa"]["codemp"]."'". 
							" GROUP BY sep_conceptos.codconsep ".
							" ORDER BY sep_conceptos.codconsep";
				$dataSepConcepto = $oservicio->buscarSql($cadenasql);					
				echo generarJson($dataSepConcepto);
				unset($dataSepConcepto);			
				unset($oservicio);	
				break;
			
			case 'buscardetalle':
				$oservicio = new ServicioCfg ('sep_conceptos');
				$cadenasql ="SELECT sep_conceptocargos.codcar, sigesp_cargos.dencar, sigesp_cargos.porcar, sigesp_cargos.tipo_iva ".
							"  FROM sep_conceptocargos ".
							" INNER JOIN sigesp_cargos ".
							"    ON sep_conceptocargos.codemp='".$_SESSION["la_empresa"]["codemp"]."'".
							"   AND sep_conceptocargos.codconsep='".$arrjson->codconsep."'".
							"   AND sep_conceptocargos.codemp=sigesp_cargos.codemp ".
							"   AND sep_conceptocargos.codcar=sigesp_cargos.codcar ";
				$dataDetalleSepCon = $oservicio->buscarSql($cadenasql);					
				echo generarJson($dataDetalleSepCon);
				unset($dataDetalleSepCon);
				unset($oservicio);	
				break;
			
			case 'catconceptosep' :
				if($_SESSION["la_empresa"]["estparsindis"]==1)
				{
					$cadenasql ="SELECT sep_conceptos.codconsep AS coditem,sep_conceptos.denconsep AS denitem,sep_conceptos.monconsepe AS preitem,".  
								"		TRIM(spg_cuenta) as spg_cuenta, ".
								"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
								"		  WHERE spg_cuentas.codestpro1 = '".$arrjson->codestpro1."' ".
								"		    AND spg_cuentas.codestpro2 = '".$arrjson->codestpro2."' ".
								"		    AND spg_cuentas.codestpro3 = '".$arrjson->codestpro3."' ".
								"		    AND spg_cuentas.codestpro4 = '".$arrjson->codestpro4."' ".
								"		    AND spg_cuentas.codestpro5 = '".$arrjson->codestpro5."' ".
								"           AND  spg_cuentas.estcla = '".$arrjson->estcla."' ".
								"			AND spg_cuentas.spg_cuenta = sep_conceptos.spg_cuenta) AS existecuenta, ".
								"	    (SELECT (asignado-(comprometido+precomprometido)+aumento-disminucion) ".
								"		   FROM spg_cuentas ".
								"		  WHERE  spg_cuentas.codestpro1 = '".$arrjson->codestpro1."' ".
								"		    AND spg_cuentas.codestpro2 = '".$arrjson->codestpro2."'".
								"		    AND spg_cuentas.codestpro3 = '".$arrjson->codestpro3."' ".
								"		    AND spg_cuentas.codestpro4 = '".$arrjson->codestpro4."' ".
								"		    AND spg_cuentas.codestpro5 = '".$arrjson->codestpro5."' ".
								"           AND spg_cuentas.estcla='".$arrjson->estcla."'".
								"			AND spg_cuentas.spg_cuenta = sep_conceptos.spg_cuenta) AS disponibilidad ".
								"  FROM sep_conceptos  ".
								" WHERE sep_conceptos.codconsep like '%".$arrjson->coditem."%'".
								"   AND sep_conceptos.denconsep like '%".$arrjson->denitem."%'".
								" ORDER BY sep_conceptos.codconsep ASC";
					
				}
				else
				{
					$cadenasql ="SELECT sep_conceptos.codconsep AS coditem,". 
								"		sep_conceptos.denconsep AS denitem,". 
								"		sep_conceptos.monconsepe AS preitem,".
								"		TRIM(spg_cuenta) as spg_cuenta,".
								"		(SELECT COUNT(spg_cuenta) FROM spg_cuentas ".
								"		  WHERE spg_cuentas.codestpro1 = '".$arrjson->codestpro1."' ".
								"		    AND spg_cuentas.codestpro2 = '".$arrjson->codestpro2."' ".
								"		    AND spg_cuentas.codestpro3 = '".$arrjson->codestpro3."' ".
								"		    AND spg_cuentas.codestpro4 = '".$arrjson->codestpro4."' ".
								"		    AND spg_cuentas.codestpro5 = '".$arrjson->codestpro5."' ".
								"           AND  spg_cuentas.estcla = '".$arrjson->estcla."'".
								"			AND spg_cuentas.spg_cuenta = sep_conceptos.spg_cuenta) AS existecuenta ".
								"  FROM sep_conceptos ".
								" WHERE sep_conceptos.codconsep like '%".$arrjson->coditem."%'".
								"   AND sep_conceptos.denconsep like '%".$arrjson->denitem."%'".
								" ORDER BY sep_conceptos.codconsep ASC";
				}
				$dataConceptoSep  = $oservicio->buscarSql($cadenasql);
				$ObjSon = generarJson($da);
				echo $_SESSION["la_empresa"]["estparsindis"]."|".$ObjSon;
				unset($oservicio);
				unset($dataConceptoSep);
				break;
		}
	}
}
?>