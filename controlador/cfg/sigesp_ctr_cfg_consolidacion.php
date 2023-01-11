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
require_once ('../../base/librerias/php/general/Json.php');
require_once ('../../modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ('sigesp_ctr_cfg_servicio.php');

if ($_POST['ObjSon']) {
	$submit = str_replace ( "\\", "", $_POST ['ObjSon'] );
	$json = new Services_JSON();
	$ArJson = $json->decode($submit);
	$servicioEvento = new ServicioEvento();
	$valido = false;
		
	switch ($ArJson->oper) {
		case 'incluirvarios' :
			ServicioCfg::iniTransaccion();
			if($ArJson->registrosincluir) {
				$total = count((array)$ArJson->registrosincluir);
				for($j=0; $j<$total; $j++) {
					$oservicio = new ServicioCfg('sigesp_consolidacion');
					$oservicio->setCodemp($_SESSION["la_empresa"]["codemp"]);
					if ($oservicio->incluirDto($ArJson->registrosincluir[$j])){
						$valido=true;
					}
					else {
						break;
					}
					unset($oservicio);
				}
			}	
			if (ServicioCfg::comTransaccion ($valido)) {
				$servicioEvento->evento="INSERTAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_consolidacion.php";
				$servicioEvento->desevetra="Insertó en CFG un nuevo detalle de consolidación";	
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				echo "1";
			}
			else {
				echo "0";
			}
			break;
		
		case 'eliminarvarios' :
			ServicioCfg::iniTransaccion();
			if($ArJson->registroseliminar) {
				$total = count((array)$ArJson->registroseliminar);
				for($j=0; $j<$total; $j++) {
					$oservicio = new ServicioCfg('sigesp_consolidacion');
					$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
					if ($oservicio->eliminarDto($ArJson->registroseliminar[$j]) == ''){
						$valido=true;
					}
					else {
						break;
					}
					unset($oservicio);
				}
			}
			
			if (ServicioCfg::comTransaccion ($valido)) {
				$servicioEvento->evento="ELIMINAR";
				$servicioEvento->codmenu=$ArJson->codmenu;
				$servicioEvento->codusu=$_SESSION["la_logusr"];
				$servicioEvento->codemp=$_SESSION["la_empresa"]["codemp"];
				$servicioEvento->codsis="CFG";
				$servicioEvento->nomfisico="sigesp_vis_cfg_consolidacion.php";
				$servicioEvento->desevetra="Elimino en CFG un nuevo detalle de consolidación";	
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				echo "1";
			}
			else {
				echo "0";
			}
			break;
		
		case 'detalles' :
			$oservicio = new ServicioCfg('sigesp_consolidacion');
			$oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
			$criterio = "''";
			switch($_SESSION["la_empresa"]["numniv"])
			{
			 case 1:
			 		$criterio = $arreglo[0]="SUBSTR(sigesp_consolidacion.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					break;
					
			 case 2:
			 		$arreglo[0]="SUBSTR(sigesp_consolidacion.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[1]="'-'";
					$arreglo[2]="SUBSTR(sigesp_consolidacion.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$criterio = $oservicio->concatenarSQL($arreglo);
					break;
					
			 case 3:
			 		$arreglo[0]="SUBSTR(sigesp_consolidacion.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[1]="'-'";
					$arreglo[2]="SUBSTR(sigesp_consolidacion.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[3]="'-'";
					$arreglo[4]="SUBSTR(sigesp_consolidacion.codestpro3,26-(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$criterio = $oservicio->concatenarSQL($arreglo);
					break;
					
			 case 4:
			 		$arreglo[0]="SUBSTR(sigesp_consolidacion.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[1]="'-'";
					$arreglo[2]="SUBSTR(sigesp_consolidacion.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[3]="'-'";
					$arreglo[4]="SUBSTR(sigesp_consolidacion.codestpro3,26-(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[5]="'-'";
					$arreglo[6]="SUBSTR(sigesp_consolidacion.codestpro4,26-(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$criterio = $oservicio->concatenarSQL($arreglo);
					break;
			 
			 case 5:
			 		$arreglo[0]="SUBSTR(sigesp_consolidacion.codestpro1,26-(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro1 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[1]="'-'";
					$arreglo[2]="SUBSTR(sigesp_consolidacion.codestpro2,26-(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro2 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[3]="'-'";
					$arreglo[4]="SUBSTR(sigesp_consolidacion.codestpro3,26-(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro3 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[5]="'-'";
					$arreglo[6]="SUBSTR(sigesp_consolidacion.codestpro4,26-(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro4 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$arreglo[7]="'-'";
					$arreglo[8]="SUBSTR(sigesp_consolidacion.codestpro5,26-(SELECT loncodestpro5 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp),(SELECT loncodestpro5 from sigesp_empresa where sigesp_empresa.codemp = sigesp_consolidacion.codemp))";
					$criterio = $oservicio->concatenarSQL($arreglo);
					break;
			}
			
			$cadenaSql= " SELECT ". 
						"   sigesp_consolidacion.codemp,  ".
						"   sigesp_consolidacion.nombasdat,  ".
						"   sigesp_consolidacion.codestpro1,  ".
						"   sigesp_consolidacion.codestpro2,  ".
						"   sigesp_consolidacion.codestpro3,  ".
						"   sigesp_consolidacion.codestpro4,  ".
						"   sigesp_consolidacion.codestpro5,  ".
						"   sigesp_consolidacion.estcla, ".
						"   (CASE estcla WHEN 'P' ".
						"   THEN 'PROYECTO' ".
						"   ELSE 'ACCION' ".
						"   END ) AS desestcla, ".
						"(".$criterio.") AS codestpro ".
						" FROM  ".
						"  sigesp_consolidacion ".
			            "  WHERE sigesp_consolidacion.codemp='".$_SESSION["la_empresa"]["codemp"]."'";
			$dataDetalle = $oservicio->buscarSql($cadenaSql);
			echo generarJson($dataDetalle);
			unset($dataDetalle); 
			unset($oservicio);
			break;
			
		case 'obtenerbasesdatos':
			  $oservicio->setCodemp ($_SESSION["la_empresa"]["codemp"]);
			  $resultado = $oservicio->obtenerBasesDatos();
			  $TextJso = array ("raiz" => $arRegistros );
			  echo $json->encode ( $TextJso );
			  unset($oservicio);
			  break;
		
	
	}
}

?>