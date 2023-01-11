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
$datosempresa=$_SESSION["la_empresa"];
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
		$submit = str_replace("\\", "", $_POST['ObjSon']);
		$json = new Services_JSON;
		$arrjson = $json->decode($submit);
		$arrtabdetalles[0]='imo_spg_dt_unidadadministrativa';
		$arrtabdetalles[1]='pel_spg_dt_unidadadministrativa';
		$daounijecutora = new DaoGenericoPlus('spg_unidadadministrativa',$arrtabdetalles);

		$servicioEvento = new ServicioEvento();
		
		switch ($arrjson->operacion)
		{
			case 'buscarcodigo':
				$oservicio = new ServicioCfg('spg_unidadadministrativa');
				$oservicio->setCodemp($datosempresa["codemp"]);
				$cad = $oservicio->buscarCodUnidadEjecutora();
				echo "|{$cad}";
				unset($oservicio);
				break;
				
			case 'incluir':
				$daounijecutora->setData($arrjson,$datosempresa["codemp"]);
				$resultado=$daounijecutora->incluirDto(true);
				foreach ($resultado as $valresul => $valor)
				{
					echo"|".$valor;
				}
				break;

			case 'eliminar':
				$oservicio = new ServicioCfg('spg_unidadadministrativa');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$ultimo=$oservicio->verificarUltimo('coduniadm','spg_unidadadministrativa'," WHERE codemp='".$_SESSION["la_empresa"]["codemp"]."' ",$arrjson->datoscabecera[0]->coduniadm);
				if ($ultimo)
				{
					$daounijecutora->setData($arrjson,$datosempresa["codemp"]);
					$datosCabecera    = $arrjson->datoscabecera[0];
					$arrtabignorar[0] = 'spg_unidadadministrativa';
					$arrtabignorar[1] = 'spg_dt_unidadadministrativa';
					$respuesta = $daounijecutora->eliminarDto(true, 'coduniadm', $datosCabecera->coduniadm, $arrtabignorar);
				}
				else
				{
					$respuesta ='-8';
				}
				unset($oservicio);
				echo "|".$respuesta;	
				break;
				
			case 'catalogo':
				$objcabecera = $daounijecutora->getCabecera();
				$cadenasql   = "SELECT spg_unidadadministrativa.coduniadm,spg_unidadadministrativa.denuniadm, spg_unidadadministrativa.estemireq,spg_unidadadministrativa.coduniadmsig, ".
							   "	   spg_ministerio_ua.denuac, spg_unidadadministrativa.resuniadm ".
							   "  FROM spg_unidadadministrativa, spg_ministerio_ua ".
							   " WHERE spg_unidadadministrativa.codemp = '".$datosempresa["codemp"]."' ".
							   "   AND spg_unidadadministrativa.codemp = spg_ministerio_ua.codemp ".
							   "   AND spg_unidadadministrativa.coduniadmsig = spg_ministerio_ua.coduac ".
							   " ORDER BY spg_unidadadministrativa.coduniadm";
				$dataUnidadEje = $objcabecera->buscarSql($cadenasql);
				echo generarJson($dataUnidadEje);
				unset($dataUnidadEje);
				unset($objcabecera);
				break;
				
			case 'detalles':
				$objdetalle = $daounijecutora->getInstaciaDetalle('spg_dt_unidadadministrativa');
				switch ($arrjson->cantnivel)
				{
					case 1:
						$cadenasql  = "SELECT SUBSTR(spg_dt_unidadadministrativa.codestpro1,length(spg_dt_unidadadministrativa.codestpro1)-{$datosempresa['loncodestpro1']}) AS codestpro1,".
									  "		  spg_ep1.denestpro1, spg_dt_unidadadministrativa.estcla, spg_dt_unidadadministrativa.central ".
									  "  FROM spg_dt_unidadadministrativa,spg_ep1 ".
									  " WHERE spg_dt_unidadadministrativa.coduniadm ='".$arrjson->coduniadm."' ".
									  "   AND spg_dt_unidadadministrativa.codemp ='".$datosempresa["codemp"]."' ".
									  "   AND spg_dt_unidadadministrativa.codemp = spg_ep1.codemp ".
									  "   AND spg_dt_unidadadministrativa.codestpro1 = spg_ep1.codestpro1 ".
									  "   AND spg_dt_unidadadministrativa.estcla = spg_ep1.estcla";
						break;
						
					case 2:
						$cadenasql  = "SELECT SUBSTR(spg_dt_unidadadministrativa.codestpro1,length(spg_dt_unidadadministrativa.codestpro1)-{$datosempresa['loncodestpro1']}) AS codestpro1,".
									  "	  	  SUBSTR(spg_dt_unidadadministrativa.codestpro2,length(spg_dt_unidadadministrativa.codestpro2)-{$datosempresa['loncodestpro2']}) AS codestpro2,". 
									  "		  spg_ep2.denestpro2, spg_dt_unidadadministrativa.estcla, spg_dt_unidadadministrativa.central ".
									  "  FROM spg_dt_unidadadministrativa,spg_ep2 ".
									  " WHERE spg_dt_unidadadministrativa.coduniadm ='".$arrjson->coduniadm."'".
									  "   AND spg_dt_unidadadministrativa.codemp = '".$datosempresa["codemp"]."'".
									  "   AND spg_dt_unidadadministrativa.codemp = spg_ep2.codemp".
									  "   AND spg_dt_unidadadministrativa.codestpro1 = spg_ep2.codestpro1".
									  "   AND spg_dt_unidadadministrativa.codestpro2 = spg_ep2.codestpro2".
									  "   AND spg_dt_unidadadministrativa.estcla = spg_ep2.estcla";
						break;
						
					case 3:
						$cadenasql  = "SELECT SUBSTR(spg_dt_unidadadministrativa.codestpro1,length(spg_dt_unidadadministrativa.codestpro1)-{$datosempresa['loncodestpro1']}) AS codestpro1,".
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro2,length(spg_dt_unidadadministrativa.codestpro2)-{$datosempresa['loncodestpro2']}) AS codestpro2,".
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro3,length(spg_dt_unidadadministrativa.codestpro3)-{$datosempresa['loncodestpro3']}) AS codestpro3,". 
									  "		  spg_ep3.denestpro3, spg_dt_unidadadministrativa.estcla,spg_dt_unidadadministrativa.central".
									  "  FROM spg_dt_unidadadministrativa,spg_ep3 ".
									  " WHERE spg_dt_unidadadministrativa.coduniadm ='".$arrjson->coduniadm."'".
									  "   AND spg_dt_unidadadministrativa.codemp = '".$datosempresa["codemp"]."'".
									  "   AND spg_dt_unidadadministrativa.codemp = spg_ep3.codemp".
									  "   AND spg_dt_unidadadministrativa.codestpro1 = spg_ep3.codestpro1".
									  "   AND spg_dt_unidadadministrativa.codestpro2 = spg_ep3.codestpro2".
									  "   AND spg_dt_unidadadministrativa.codestpro3 = spg_ep3.codestpro3".
									  "   AND spg_dt_unidadadministrativa.estcla = spg_ep3.estcla";
						break;
					case 4:
						$cadenasql  = "SELECT SUBSTR(spg_dt_unidadadministrativa.codestpro1,length(spg_dt_unidadadministrativa.codestpro1)-{$datosempresa['loncodestpro1']}) AS codestpro1,".
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro2,length(spg_dt_unidadadministrativa.codestpro2)-{$datosempresa['loncodestpro2']}) AS codestpro2,".
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro3,length(spg_dt_unidadadministrativa.codestpro3)-{$datosempresa['loncodestpro3']}) AS codestpro3,". 
									  "       SUBSTR(spg_dt_unidadadministrativa.codestpro4,length(spg_dt_unidadadministrativa.codestpro4)-{$datosempresa['loncodestpro4']}) AS codestpro4,".  
									  "       spg_ep4.denestpro4, spg_dt_unidadadministrativa.estcla, spg_dt_unidadadministrativa.central". 
									  "  FROM spg_dt_unidadadministrativa,spg_ep4 ".
									  " WHERE spg_dt_unidadadministrativa.coduniadm ='".$arrjson->coduniadm."' ".
									  "   AND spg_dt_unidadadministrativa.codemp = '".$datosempresa["codemp"]."' ".
									  "   AND spg_dt_unidadadministrativa.codemp = spg_ep4.codemp ".
									  "   AND spg_dt_unidadadministrativa.codestpro1 = spg_ep4.codestpro1 ".
									  "   AND spg_dt_unidadadministrativa.codestpro2 = spg_ep4.codestpro2 ".
									  "   AND spg_dt_unidadadministrativa.codestpro3 = spg_ep4.codestpro3 ".
									  "   AND spg_dt_unidadadministrativa.codestpro4 = spg_ep4.codestpro4 ".
									  "   AND spg_dt_unidadadministrativa.estcla = spg_ep4.estcla";
						break;
					case 5:
						$cadenasql  = "SELECT SUBSTR(spg_dt_unidadadministrativa.codestpro1,length(spg_dt_unidadadministrativa.codestpro1)-{$datosempresa['loncodestpro1']}) AS codestpro1,".
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro2,length(spg_dt_unidadadministrativa.codestpro2)-{$datosempresa['loncodestpro2']}) AS codestpro2,".
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro3,length(spg_dt_unidadadministrativa.codestpro3)-{$datosempresa['loncodestpro3']}) AS codestpro3,". 
									  "       SUBSTR(spg_dt_unidadadministrativa.codestpro4,length(spg_dt_unidadadministrativa.codestpro4)-{$datosempresa['loncodestpro4']}) AS codestpro4,".  
									  "		  SUBSTR(spg_dt_unidadadministrativa.codestpro5,length(spg_dt_unidadadministrativa.codestpro5)-{$datosempresa['loncodestpro5']}) AS codestpro5,".
									  "		  spg_ep5.denestpro5, spg_dt_unidadadministrativa.estcla, spg_dt_unidadadministrativa.central".
									  "  FROM spg_dt_unidadadministrativa,spg_ep5 ".
									  " WHERE spg_dt_unidadadministrativa.coduniadm ='".$arrjson->coduniadm."' ".
									  "   AND spg_dt_unidadadministrativa.codemp = '".$datosempresa["codemp"]."' ".
									  "   AND spg_dt_unidadadministrativa.codemp = spg_ep5.codemp ".
									  "   AND spg_dt_unidadadministrativa.codestpro1 = spg_ep5.codestpro1 ".
									  "   AND spg_dt_unidadadministrativa.codestpro2 = spg_ep5.codestpro2 ".
									  "   AND spg_dt_unidadadministrativa.codestpro3 = spg_ep5.codestpro3 ".
									  "   AND spg_dt_unidadadministrativa.codestpro4 = spg_ep5.codestpro4 ".
									  "   AND spg_dt_unidadadministrativa.codestpro5 = spg_ep5.codestpro5 ".
									  "   AND spg_dt_unidadadministrativa.estcla = spg_ep5.estcla";
						break;
				}
				$datos = $objdetalle->buscarSql($cadenasql);
				echo  generarJson($datos);
				unset($datos);
				unset($objdetalle);
				break;
				
			case 'catalogounidadestructura':
				$oservicio = new ServicioCfg('sigesp_consolidacion');
				$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
				$criterio="''";
				$seguridad="";
				
				$arrcadunidad[0]="UNIDADES.coduniadm";
				$arrcadunidad[1]="' - '";
				$arrcadunidad[2]="UNIDADES.denuniadm";
				
				$nomunidad= $oservicio->concatenarSQL($arrcadunidad); 
				
				$arrsegunidad[0] = "'".$_SESSION["la_empresa"]["codemp"]."'";
				$arrsegunidad[1] = "'SOC'";
				$arrsegunidad[2] = "'".$_SESSION["la_logusr"]."'";
				$arrsegunidad[3] = "UNIDADES.coduniadm";
				
				$cadsegunidad= $oservicio->concatenarSQL($arrsegunidad);
				
				$arrsegperint[0] = "sss_permisos_internos.codemp";
				$arrsegperint[1] = "sss_permisos_internos.codsis";
				$arrsegperint[2] = "sss_permisos_internos.codusu";
				$arrsegperint[3] = "sss_permisos_internos.codintper";
				
				$cadsegperint= $oservicio->concatenarSQL($arrsegperint);
				
				$seguridad = "AND ".$cadsegunidad." IN (SELECT ".$cadsegperint." FROM sss_permisos_internos WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'AND codusu = '".$_SESSION["la_logusr"]."' AND codsis = 'SOC')";
				
				
				switch($_SESSION["la_empresa"]["numniv"])
				{
				 case 1:
						$criterio = $arreglo[0]="SUBSTR(UNIDADES.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						break;
						
				 case 2:
						$arreglo[0]="SUBSTR(UNIDADES.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[1]="'  -  '";
						$arreglo[2]="SUBSTR(UNIDADES.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$criterio = $oservicio->concatenarSQL($arreglo);
						break;
						
				 case 3:
						$arreglo[0]="SUBSTR(UNIDADES.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[1]="'  -  '";
						$arreglo[2]="SUBSTR(UNIDADES.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[3]="'  -  '";
						$arreglo[4]="SUBSTR(UNIDADES.codestpro3,26-(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$criterio = $oservicio->concatenarSQL($arreglo);
						break;
						
				 case 4:
						$arreglo[0]="SUBSTR(UNIDADES.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[1]="'  -  '";
						$arreglo[2]="SUBSTR(UNIDADES.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[3]="'  -  '";
						$arreglo[4]="SUBSTR(UNIDADES.codestpro3,26-(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[5]="'  -  '";
						$arreglo[6]="SUBSTR(UNIDADES.codestpro4,26-(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$criterio = $oservicio->concatenarSQL($arreglo);
						break;
				 
				 case 5:
						$arreglo[0]="SUBSTR(UNIDADES.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[1]="'  -  '";
						$arreglo[2]="SUBSTR(UNIDADES.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[3]="'  -  '";
						$arreglo[4]="SUBSTR(UNIDADES.codestpro3,26-(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[5]="'  -  '";
						$arreglo[6]="SUBSTR(UNIDADES.codestpro4,26-(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$arreglo[7]="'  -  '";
						$arreglo[8]="SUBSTR(UNIDADES.codestpro5,26-(SELECT loncodestpro5 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp),(SELECT loncodestpro5 from sigesp_empresa where sigesp_empresa.codemp = UNIDADES.codemp))";
						$criterio = $oservicio->concatenarSQL($arreglo);
						break;
				}
				
				$cadenasql= " SELECT UNIDADES.*,(".$criterio.") AS estructura,(".$nomunidad.") AS unidad  FROM (SELECT                    ".
							"	  spg_dt_unidadadministrativa.codemp,            ".
							"	  spg_dt_unidadadministrativa.coduniadm,         ".
							"	  spg_unidadadministrativa.denuniadm,            ".
							"	  spg_dt_unidadadministrativa.codestpro1,        ". 
							"	  spg_dt_unidadadministrativa.codestpro2,        ". 
							"	  spg_dt_unidadadministrativa.codestpro3,        ". 
							"	  spg_dt_unidadadministrativa.codestpro4,        ". 
							"	  spg_dt_unidadadministrativa.codestpro5,        ".  
							"	  spg_dt_unidadadministrativa.estcla,            ".
							"	  (CASE spg_dt_unidadadministrativa.estcla WHEN  ". 
							"		'A' THEN 'ACCION'                            ".
							"			WHEN 'P' THEN 'PROYECTO'                 ".
							"			ELSE                                     ".
							"			''                                       ".
							"	   END) AS denestcla                             ".
							"	FROM                                             ".
							"	  spg_ep5,                                       ". 
							"	  spg_dt_unidadadministrativa,                   ".
							"	  spg_unidadadministrativa                       ". 
							"	WHERE                                            ". 
							"	  spg_dt_unidadadministrativa.codemp = spg_unidadadministrativa.codemp AND         ".    
							"	  spg_dt_unidadadministrativa.coduniadm = spg_unidadadministrativa.coduniadm AND   ".
							"	  spg_dt_unidadadministrativa.codestpro1 = spg_ep5.codestpro1 AND                  ".
							"	  spg_dt_unidadadministrativa.codestpro2 = spg_ep5.codestpro2 AND                  ".
							"	  spg_dt_unidadadministrativa.codestpro3 = spg_ep5.codestpro3 AND                  ".
							"	  spg_dt_unidadadministrativa.codestpro4 = spg_ep5.codestpro4 AND                  ".
							"	  spg_dt_unidadadministrativa.codestpro5 = spg_ep5.codestpro5 AND                  ".
							"	  spg_dt_unidadadministrativa.estcla = spg_ep5.estcla AND                          ".
							"	  spg_dt_unidadadministrativa.codemp = spg_ep5.codemp) AS UNIDADES                 ".
							" WHERE UNIDADES.codemp = '".$_SESSION["la_empresa"]["codemp"]."' AND UNIDADES.coduniadm <> '----------' ".$seguridad.
							" ORDER BY  UNIDADES.coduniadm";
				$resultado = $oservicio->buscarSql($cadenasql);
				echo generarJson($resultado);
				unset($resultado);
				unset($oservicio); 
				break;
		}
		unset($daounijecutora);
	}
}
?>