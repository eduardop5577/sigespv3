<?php
/***********************************************************************************
* @Clase para el inicio de Sessión del sistema
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
require_once('../../base/librerias/php/general/sigesp_lib_validaciones.php');
if ($_POST['objdata'])	
{	
	$objdata = str_replace('\\','',$_POST['objdata']);
	$objdata = json_decode($objdata,false);	
	$ruta = '../../base/xml/';
	$archivoconfig = 'sigesp_xml_configuracion.xml';
	switch ($objdata->operacion)
	{
		case 'obtenerbd':    		
			session_unset();
			session_destroy();
			$documentoxml = abrirArchivoXml($ruta,$archivoconfig);
			if ($documentoxml != null)
			{
				$datos = array();
				$datos  = obtenerConexionbd($documentoxml,$datos);
				$datos  = array('raiz'=>$datos);
				$textJson = json_encode($datos);
				echo $textJson;
			}
		break;
			
		case 'obtenerempresa': 
			$documentoxml = abrirArchivoXml($ruta,$archivoconfig);	
			if (!is_null($documentoxml))
			{
				$basededatos = obtenerEmpresa($documentoxml,$objdata->basedatos);
				require_once('../../modelo/servicio/cfg/sigesp_srv_cfg_empresa.php');
				$objEmpresa = new Empresa();
				if ($basededatos !='')
				{
					$datos = $objEmpresa->filtrarEmpresas();
					if ($datos->EOF)
					{
						$_SESSION['la_empresa']['codemp'] = '0001';
						$_SESSION['sigesp_sitioweb'] = 'sigesp2011';
						$_SESSION['tiempo_session'] = 600;
					}
					else
					{
						$_SESSION['la_empresa']['codemp'] = $datos->fields['codemp'];
						$_SESSION['la_empresa']['nombre'] = $datos->fields['nombre'];
						$_SESSION['la_empresa']['titulo'] = $datos->fields['titulo'];
						$_SESSION['la_empresa']['sigemp'] = $datos->fields['sigemp'];
						$_SESSION['la_empresa']['faxemp'] = $datos->fields['faxemp'];
						$_SESSION['la_empresa']['email'] = $datos->fields['email'];
						$_SESSION['la_empresa']['ingreso'] = $datos->fields['ingreso'];
						$_SESSION['la_empresa']['gasto'] = $datos->fields['gasto'];
						$_SESSION['la_empresa']['activo'] = $datos->fields['activo'];
						$_SESSION['la_empresa']['pasivo'] = $datos->fields['pasivo'];
						$_SESSION['la_empresa']['resultado'] = $datos->fields['resultado'];
						$_SESSION['la_empresa']['capital'] = $datos->fields['capital'];
						$_SESSION['la_empresa']['c_resultad'] = $datos->fields['c_resultad'];
						$_SESSION['la_empresa']['c_resultan'] = $datos->fields['c_resultan'];
						$_SESSION['la_empresa']['orden_d'] = $datos->fields['orden_d'];
						$_SESSION['la_empresa']['orden_h'] = $datos->fields['orden_h'];
						$_SESSION['la_empresa']['soc_gastos'] = $datos->fields['soc_gastos'];
						$_SESSION['la_empresa']['soc_servic'] = $datos->fields['soc_servic'];
						$_SESSION['la_empresa']['orden_h'] = $datos->fields['orden_h'];
						$_SESSION['la_empresa']['activo_h'] = $datos->fields['activo_h'];
						$_SESSION['la_empresa']['pasivo_h'] = $datos->fields['pasivo_h'];
						$_SESSION['la_empresa']['resultado_h'] = $datos->fields['resultado_h'];
						$_SESSION['la_empresa']['ingreso_f'] = $datos->fields['ingreso_f'];
						$_SESSION['la_empresa']['gasto_f'] = $datos->fields['gasto_f'];
						$_SESSION['la_empresa']['ingreso_p'] = $datos->fields['ingreso_p'];
						$_SESSION['la_empresa']['direccion'] = $datos->fields['direccion'];
						$_SESSION['la_empresa']['telemp'] = $datos->fields['telemp'];
						$_SESSION['la_empresa']['periodo'] = date('Y-m-d',strtotime($datos->fields['periodo']));
						$_SESSION['la_empresa']['vali_nivel'] = $datos->fields['vali_nivel'];
						$_SESSION['la_empresa']['esttipcont'] = $datos->fields['esttipcont'];
						$_SESSION['la_empresa']['formpre'] = $datos->fields['formpre'];
						$_SESSION['la_empresa']['formcont'] = $datos->fields['formcont'];
						$_SESSION['la_empresa']['formplan'] = $datos->fields['formplan'];
						$_SESSION['la_empresa']['formspi'] = $datos->fields['formspi'];
						$_SESSION['la_empresa']['numniv'] = $datos->fields['numniv'];						
						$_SESSION['la_empresa']['estmodest'] = $datos->fields['estmodest'];						
						$_SESSION['la_empresa']['nomestpro1'] = $datos->fields['nomestpro1'];
						$_SESSION['la_empresa']['nomestpro2'] = $datos->fields['nomestpro2'];
						$_SESSION['la_empresa']['nomestpro3'] = $datos->fields['nomestpro3'];
						$_SESSION['la_empresa']['nomestpro4'] = $datos->fields['nomestpro4'];
						$_SESSION['la_empresa']['nomestpro5'] = $datos->fields['nomestpro5'];
						$_SESSION['la_empresa']['rifemp'] = $datos->fields['rifemp'];
						$_SESSION['la_empresa']['loncodestpro1'] = $datos->fields['loncodestpro1'];
						$_SESSION['la_empresa']['loncodestpro2'] = $datos->fields['loncodestpro2'];
						$_SESSION['la_empresa']['loncodestpro3'] = $datos->fields['loncodestpro3'];
						$_SESSION['la_empresa']['loncodestpro4'] = $datos->fields['loncodestpro4'];
						$_SESSION['la_empresa']['loncodestpro5'] = $datos->fields['loncodestpro5'];
						$_SESSION['la_empresa']['estvaldis'] = $datos->fields['estvaldis'];
						$_SESSION['la_empresa']['estintcred'] = $datos->fields['estintcred'];
						$_SESSION['la_empresa']['estciespg'] = $datos->fields['estciespg'];
						$_SESSION['la_empresa']['estciespi'] = $datos->fields['estciespi'];
						$_SESSION['la_empresa']['estciescg'] = $datos->fields['estciescg'];
						$_SESSION['la_empresa']['confinstr'] = $datos->fields['confinstr'];
						$_SESSION['la_empresa']['gasto_p'] = $datos->fields['gasto_p'];
						$_SESSION['la_empresa']['estvaltra'] = $datos->fields['estvaltra'];
						$_SESSION['la_empresa']['nitemp'] = $datos->fields['nitemp'];
						$_SESSION['la_empresa']['estemp'] = $datos->fields['estemp'];
						$_SESSION['la_empresa']['ciuemp'] = $datos->fields['ciuemp'];
						$_SESSION['la_empresa']['zonpos'] = $datos->fields['zonpos'];
						$_SESSION['la_empresa']['estmodape'] = $datos->fields['estmodape'];
						$_SESSION['la_empresa']['estdesiva'] = $datos->fields['estdesiva'];
						$_SESSION['la_empresa']['estprecom'] = $datos->fields['estprecom'];
						$_SESSION['la_empresa']['estmodsepsoc'] = $datos->fields['estmodsepsoc'];
						$_SESSION['la_empresa']['codorgsig'] = $datos->fields['codorgsig'];
						$_SESSION['la_empresa']['socbieser'] = $datos->fields['socbieser'];
						$_SESSION['la_empresa']['estmodest'] = $datos->fields['estmodest'];
						$_SESSION['la_empresa']['salinipro'] = $datos->fields['salinipro'];
						$_SESSION['la_empresa']['salinieje'] = $datos->fields['salinieje'];
						$_SESSION['la_empresa']['numordcom'] = $datos->fields['numordcom'];
						$_SESSION['la_empresa']['numordser'] = $datos->fields['numordser'];
						$_SESSION['la_empresa']['numsolpag'] = $datos->fields['numsolpag'];
						$_SESSION['la_empresa']['nomorgads'] = $datos->fields['nomorgads'];
						$_SESSION['la_empresa']['numlicemp'] = $datos->fields['numlicemp'];
						$_SESSION['la_empresa']['modageret'] = $datos->fields['modageret'];
						$_SESSION['la_empresa']['nomres'] = $datos->fields['nomres'];
						$_SESSION['la_empresa']['concomiva'] = $datos->fields['concomiva'];
						$_SESSION['la_empresa']['cedben'] = $datos->fields['cedben'];
						$_SESSION['la_empresa']['nomben'] = $datos->fields['nomben'];
						$_SESSION['la_empresa']['scctaben'] = $datos->fields['scctaben'];
						$_SESSION['la_empresa']['estmodiva'] = $datos->fields['estmodiva'];
						$_SESSION['la_empresa']['activo_t'] = $datos->fields['activo_t'];
						$_SESSION['la_empresa']['pasivo_t'] = $datos->fields['pasivo_t'];
						$_SESSION['la_empresa']['resultado_t'] = $datos->fields['resultado_t'];
						$_SESSION['la_empresa']['c_financiera'] = $datos->fields['c_financiera'];
						$_SESSION['la_empresa']['c_fiscal'] = $datos->fields['c_fiscal'];
						$_SESSION['la_empresa']['diacadche'] = $datos->fields['diacadche'];
						$_SESSION['la_empresa']['codasiona'] = $datos->fields['codasiona'];
						$_SESSION['la_empresa']['conrecdoc'] = $datos->fields['conrecdoc'];
						$_SESSION['la_empresa']['estvaldis'] = $datos->fields['estvaldis'];
						$_SESSION['la_empresa']['nroivss'] = $datos->fields['nroivss'];
						$_SESSION['la_empresa']['nomrep'] = $datos->fields['nomrep'];
						$_SESSION['la_empresa']['cedrep'] = $datos->fields['cedrep'];
						$_SESSION['la_empresa']['telfrep'] = $datos->fields['telfrep'];
						$_SESSION['la_empresa']['cargorep'] = $datos->fields['cargorep'];
						$_SESSION['la_empresa']['estretiva'] = $datos->fields['estretiva'];
						$_SESSION['la_empresa']['clactacon'] = $datos->fields['clactacon'];
						$_SESSION['la_empresa']['estempcon'] = $datos->fields['estempcon'];
						$_SESSION['la_empresa']['codaltemp'] = $datos->fields['codaltemp'];
						$_SESSION['la_empresa']['basdatcon'] = $datos->fields['basdatcon'];
						$_SESSION['la_empresa']['estcamemp'] = $datos->fields['estcamemp'];
						$_SESSION['la_empresa']['estparsindis'] = $datos->fields['estparsindis'];
						$_SESSION['la_empresa']['basdatcmp'] = $datos->fields['basdatcmp'];
						$_SESSION['la_empresa']['estciespg'] = $datos->fields['estciespg'];
						$_SESSION['la_empresa']['estciespi'] = $datos->fields['estciespi'];
						$_SESSION['la_empresa']['confinstr'] = $datos->fields['confinstr'];
						$_SESSION['la_empresa']['estintcred'] = $datos->fields['estintcred'];
						$_SESSION['la_empresa']['estciescg'] = $datos->fields['estciescg'];
						$_SESSION['la_empresa']['estvalspg'] = $datos->fields['estvalspg'];
						$_SESSION['la_empresa']['ctaspgrec'] = $datos->fields['ctaspgrec'];
						$_SESSION['la_empresa']['ctaspgced'] = $datos->fields['ctaspgced'];
						$_SESSION['la_empresa']['estmodpartsep'] = $datos->fields['estmodpartsep'];
						$_SESSION['la_empresa']['estmodpartsoc'] = $datos->fields['estmodpartsoc'];
						$_SESSION['la_empresa']['estmanant'] = $datos->fields['estmanant'];
						$_SESSION['la_empresa']['estpreing'] = $datos->fields['estpreing'];
						$_SESSION['la_empresa']['concommun'] = $datos->fields['concommun'];
						$_SESSION['la_empresa']['confiva'] = $datos->fields['confiva'];
						$_SESSION['la_empresa']['confi_ch'] = $datos->fields['confi_ch'];
						$_SESSION['la_empresa']['casconmov'] = $datos->fields['casconmov'];
						$_SESSION['la_empresa']['ctaresact'] = $datos->fields['ctaresact'];
						$_SESSION['la_empresa']['confi_ch'] = $datos->fields['confi_ch'];
						$_SESSION['la_empresa']['ctaresant'] = $datos->fields['ctaresant'];
						$_SESSION['la_empresa']['estvaldisfin'] = $datos->fields['estvaldisfin'];
						$_SESSION['la_empresa']['dedconproben'] = $datos->fields['dedconproben'];
						$_SESSION['la_empresa']['estaprsep'] = $datos->fields['estaprsep'];
						$_SESSION['la_empresa']['sujpasesp'] = $datos->fields['sujpasesp'];
						$_SESSION['la_empresa']['bloanu'] = $datos->fields['bloanu'];
						$_SESSION['la_empresa']['estretmil'] = $datos->fields['estretmil'];
						$_SESSION['la_empresa']['concommil'] = $datos->fields['concommil'];
						$_SESSION['la_empresa']['contintmovban'] = $datos->fields['contintmovban'];
						$_SESSION['la_empresa']['valinimovban'] = $datos->fields['valinimovban'];
						$_SESSION['la_empresa']['estintban'] = $datos->fields['estintban'];
						$_SESSION['la_empresa']['cueproacu'] = $datos->fields['cueproacu'];
						$_SESSION['la_empresa']['cuedepamo'] = $datos->fields['cuedepamo'];
						$_SESSION['la_empresa']['valclacon'] = $datos->fields['valclacon'];
						$_SESSION['la_empresa']['valcomrd'] = $datos->fields['valcomrd'];
						$_SESSION['la_empresa']['ctaejeprecie'] = $datos->fields['ctaejeprecie'];
						$_SESSION['la_empresa']['estaprsoc'] = $datos->fields['estaprsoc'];
						$_SESSION['la_empresa']['estaprcxp'] = $datos->fields['estaprcxp'];
						$_SESSION['la_empresa']['scforden_h'] = $datos->fields['scforden_h'];
						$_SESSION['la_empresa']['scforden_d'] = $datos->fields['scforden_d'];
						$_SESSION['la_empresa']['tiesesact'] = $datos->fields['tiesesact'];
						$_SESSION['la_empresa']['repcajchi'] = $datos->fields['repcajchi'];
						$_SESSION['la_empresa']['estafenc'] = $datos->fields['estafenc'];
						$_SESSION['la_empresa']['blocon'] = $datos->fields['blocon'];
						$_SESSION['la_empresa']['intblocon'] = $datos->fields['intblocon'];
						$_SESSION['la_empresa']['capiva'] = $datos->fields['capiva'];
						$_SESSION['la_empresa']['parcapiva'] = $datos->fields['parcapiva'];
						$_SESSION['la_empresa']['estciesem'] = $datos->fields['estciesem'];
						$_SESSION['la_empresa']['ciesem1'] = $datos->fields['ciesem1'];
						$_SESSION['la_empresa']['ciesem2'] = $datos->fields['ciesem2'];
						$_SESSION['la_empresa']['estceniva'] = $datos->fields['estceniva'];
						$_SESSION['la_empresa']['codestprocen1'] = $datos->fields['codestprocen1'];
						$_SESSION['la_empresa']['codestprocen2'] = $datos->fields['codestprocen2'];
						$_SESSION['la_empresa']['codestprocen3'] = $datos->fields['codestprocen3'];
						$_SESSION['la_empresa']['codestprocen4'] = $datos->fields['codestprocen4'];
						$_SESSION['la_empresa']['codestprocen5'] = $datos->fields['codestprocen5'];
						$_SESSION['la_empresa']['esclacen'] = $datos->fields['esclacen'];
						$_SESSION['la_empresa']['estspgdecimal'] = $datos->fields['estspgdecimal'];
						$_SESSION['la_empresa']['nivapro'] = $datos->fields['nivapro'];
						$_SESSION['la_empresa']['envcorsup'] = $datos->fields['envcorsup'];
						$_SESSION['la_empresa']['estcomobr'] = $datos->fields['estcomobr'];
						$_SESSION['la_empresa']['estbenalt'] = $datos->fields['estbenalt'];
						$_SESSION['la_empresa']['numrefcarord'] = $datos->fields['numrefcarord'];
						$_SESSION['la_empresa']['estcossig'] = $datos->fields['estcossig'];	   
						$_SESSION['la_empresa']['m01'] = $datos->fields['m01'];	   
						$_SESSION['la_empresa']['m02'] = $datos->fields['m02'];	   
						$_SESSION['la_empresa']['m03'] = $datos->fields['m03'];	   
						$_SESSION['la_empresa']['m04'] = $datos->fields['m04'];	   
						$_SESSION['la_empresa']['m05'] = $datos->fields['m05'];	   
						$_SESSION['la_empresa']['m06'] = $datos->fields['m06'];	   
						$_SESSION['la_empresa']['m07'] = $datos->fields['m07'];	   
						$_SESSION['la_empresa']['m08'] = $datos->fields['m08'];	   
						$_SESSION['la_empresa']['m09'] = $datos->fields['m09'];	   
						$_SESSION['la_empresa']['m10'] = $datos->fields['m10'];	   
						$_SESSION['la_empresa']['m11'] = $datos->fields['m11'];	   
						$_SESSION['la_empresa']['m12'] = $datos->fields['m12'];
						$_SESSION['la_empresa']['estcencos'] = $datos->fields['estcencos'];
						$_SESSION['la_empresa']['cencosact'] = $datos->fields['cencosact'];
						$_SESSION['la_empresa']['cencospas'] = $datos->fields['cencospas'];
						$_SESSION['la_empresa']['cencosing'] = $datos->fields['cencosing'];
						$_SESSION['la_empresa']['cencosgas'] = $datos->fields['cencosgas'];
						$_SESSION['la_empresa']['cencosres'] = $datos->fields['cencosres'];
						$_SESSION['la_empresa']['cencoscap'] = $datos->fields['cencoscap'];
						$_SESSION['la_empresa']['valestpre'] = $datos->fields['valestpre'];
						$_SESSION['la_empresa']['nivvalest'] = $datos->fields['nivvalest']; 	   
						$_SESSION['la_empresa']['estmodprog'] = $datos->fields['estmodprog']; 	   
						$_SESSION['tiempo_session'] = $datos->fields['tiesesact'];
						$_SESSION['bloqueo_clave'] = $datos->fields['blocon'];
						$_SESSION['intentos_bloqueo'] = $datos->fields['intblocon'];
						$_SESSION['la_empresa']['estspidecimal'] = $datos->fields['estspidecimal'];
						$_SESSION['la_empresa']['estantspg'] = $datos->fields['estantspg'];
						$_SESSION['la_empresa']['estvarpar'] = $datos->fields['estvarpar'];
						$_SESSION['la_empresa']['inicencos'] = $datos->fields['inicencos'];
						$_SESSION['la_empresa']['estfilpremod'] = $datos->fields['estfilpremod'];
						$_SESSION['la_empresa']['blopresep'] = $datos->fields['blopresep'];
						$_SESSION['la_empresa']['codperalf'] = $datos->fields['codperalf'];
						$_SESSION['la_empresa']['blonumche'] = $datos->fields['blonumche'];
						$_SESSION['la_empresa']['estpereli'] = $datos->fields['estpereli'];
						$_SESSION['la_empresa']['numdecper'] = $datos->fields['numdecper'];
						$_SESSION['la_empresa']['estrescxp'] = $datos->fields['estrescxp'];
						$_SESSION['la_empresa']['estretislr'] = $datos->fields['estretislr'];
						$_SESSION['la_empresa']['reucon'] = $datos->fields['reucon'];
						$_SESSION['la_empresa']['nroconreu'] = $datos->fields['nroconreu'];
						$_SESSION["la_empresa"]["candeccon"] = $datos->fields['candeccon'];
						$_SESSION["la_empresa"]["tipconmon"] = $datos->fields['tipconmon'];
						$_SESSION["la_empresa"]["redconmon"] = $datos->fields['redconmon'];
						$_SESSION["la_empresa"]["estcanret"] = $datos->fields['estcanret'];
						$_SESSION["la_empresa"]["estvercta"] = $datos->fields['estvercta'];
						$_SESSION["la_empresa"]["estconlot"] = $datos->fields['estconlot'];
						$_SESSION["la_empresa"]["estconcom"] = $datos->fields['estconcom'];
						$_SESSION["la_empresa"]["nroinicom"] = $datos->fields['nroinicom'];
						$_SESSION["la_empresa"]["estcommas"] = $datos->fields['estcommas'];
						$_SESSION["la_empresa"]["costo"] = '';
						if(isset($datos->fields['costo']))
						{
							$_SESSION["la_empresa"]["costo"] = $datos->fields['costo'];
						}
						$_SESSION["la_empresa"]["filindspg"] = 0;
						if(isset($datos->fields['filindspg']))
						{
							$_SESSION["la_empresa"]["filindspg"] = $datos->fields['filindspg'];
						}
						$varJson = generarJson($datos);
						echo $varJson;
					}
					$datos->close();
				}
				else
				{
					$arreglo['valido']  = $objEmpresa->valido;
					$arreglo['mensaje'] = $objEmpresa->mensaje;
					$textJso  = array('raiz'=>$arreglo);
					$textJson = json_encode($textJso);
					echo $textJson;
				}
				unset($objEmpresa);
			}
			else
			{
				$arreglo['valido']  = true;
				$arreglo['mensaje'] = 'Error al abrir el archivo de configuración';
				$textJso  = array('raiz'=>$arreglo);
				$textJson = json_encode($textJso);
				echo $textJson;
			}                        
		break;		

		case 'cargarsession':
			$basededatos = $objdata->basedatos;                    
			require_once('../../modelo/servicio/cfg/sigesp_srv_cfg_empresa.php');
			$objEmpresa = new Empresa();
			if ($basededatos !='')
			{
				$objEmpresa->codemp = $objdata->empresa;
				$datos = $objEmpresa->filtrarEmpresa();
				if ($datos->EOF)
				{
						$_SESSION['la_empresa']['codemp'] = '0001';
						$_SESSION['sigesp_sitioweb'] = 'sigesp2011';
						$_SESSION['tiempo_session'] = 600;
				}
				else
				{
					$_SESSION['la_empresa']['codemp'] = $datos->fields['codemp'];
					$_SESSION['la_empresa']['nombre'] = $datos->fields['nombre'];
					$_SESSION['la_empresa']['titulo'] = $datos->fields['titulo'];
					$_SESSION['la_empresa']['sigemp'] = $datos->fields['sigemp'];
					$_SESSION['la_empresa']['faxemp'] = $datos->fields['faxemp'];
					$_SESSION['la_empresa']['email'] = $datos->fields['email'];
					$_SESSION['la_empresa']['ingreso'] = $datos->fields['ingreso'];
					$_SESSION['la_empresa']['gasto'] = $datos->fields['gasto'];
					$_SESSION['la_empresa']['activo'] = $datos->fields['activo'];
					$_SESSION['la_empresa']['pasivo'] = $datos->fields['pasivo'];
					$_SESSION['la_empresa']['resultado'] = $datos->fields['resultado'];
					$_SESSION['la_empresa']['capital'] = $datos->fields['capital'];
					$_SESSION['la_empresa']['c_resultad'] = $datos->fields['c_resultad'];
					$_SESSION['la_empresa']['c_resultan'] = $datos->fields['c_resultan'];
					$_SESSION['la_empresa']['orden_d'] = $datos->fields['orden_d'];
					$_SESSION['la_empresa']['orden_h'] = $datos->fields['orden_h'];
					$_SESSION['la_empresa']['soc_gastos'] = $datos->fields['soc_gastos'];
					$_SESSION['la_empresa']['soc_servic'] = $datos->fields['soc_servic'];
					$_SESSION['la_empresa']['orden_h'] = $datos->fields['orden_h'];
					$_SESSION['la_empresa']['activo_h'] = $datos->fields['activo_h'];
					$_SESSION['la_empresa']['pasivo_h'] = $datos->fields['pasivo_h'];
					$_SESSION['la_empresa']['resultado_h'] = $datos->fields['resultado_h'];
					$_SESSION['la_empresa']['ingreso_f'] = $datos->fields['ingreso_f'];
					$_SESSION['la_empresa']['gasto_f'] = $datos->fields['gasto_f'];
					$_SESSION['la_empresa']['ingreso_p'] = $datos->fields['ingreso_p'];
					$_SESSION['la_empresa']['direccion'] = $datos->fields['direccion'];
					$_SESSION['la_empresa']['telemp'] = $datos->fields['telemp'];
					$_SESSION['la_empresa']['periodo'] = date('Y-m-d',strtotime($datos->fields['periodo']));
					$_SESSION['la_empresa']['vali_nivel'] = $datos->fields['vali_nivel'];
					$_SESSION['la_empresa']['esttipcont'] = $datos->fields['esttipcont'];
					$_SESSION['la_empresa']['formpre'] = $datos->fields['formpre'];
					$_SESSION['la_empresa']['formcont'] = $datos->fields['formcont'];
					$_SESSION['la_empresa']['formplan'] = $datos->fields['formplan'];
					$_SESSION['la_empresa']['formspi'] = $datos->fields['formspi'];
					$_SESSION['la_empresa']['numniv'] = $datos->fields['numniv'];						
					$_SESSION['la_empresa']['estmodest'] = $datos->fields['estmodest'];						
					$_SESSION['la_empresa']['nomestpro1'] = $datos->fields['nomestpro1'];
					$_SESSION['la_empresa']['nomestpro2'] = $datos->fields['nomestpro2'];
					$_SESSION['la_empresa']['nomestpro3'] = $datos->fields['nomestpro3'];
					$_SESSION['la_empresa']['nomestpro4'] = $datos->fields['nomestpro4'];
					$_SESSION['la_empresa']['nomestpro5'] = $datos->fields['nomestpro5'];
					$_SESSION['la_empresa']['rifemp'] = $datos->fields['rifemp'];
					$_SESSION['la_empresa']['loncodestpro1'] = $datos->fields['loncodestpro1'];
					$_SESSION['la_empresa']['loncodestpro2'] = $datos->fields['loncodestpro2'];
					$_SESSION['la_empresa']['loncodestpro3'] = $datos->fields['loncodestpro3'];
					$_SESSION['la_empresa']['loncodestpro4'] = $datos->fields['loncodestpro4'];
					$_SESSION['la_empresa']['loncodestpro5'] = $datos->fields['loncodestpro5'];
					$_SESSION['la_empresa']['estvaldis'] = $datos->fields['estvaldis'];
					$_SESSION['la_empresa']['estintcred'] = $datos->fields['estintcred'];
					$_SESSION['la_empresa']['estciespg'] = $datos->fields['estciespg'];
					$_SESSION['la_empresa']['estciespi'] = $datos->fields['estciespi'];
					$_SESSION['la_empresa']['estciescg'] = $datos->fields['estciescg'];
					$_SESSION['la_empresa']['confinstr'] = $datos->fields['confinstr'];
					$_SESSION['la_empresa']['gasto_p'] = $datos->fields['gasto_p'];
					$_SESSION['la_empresa']['estvaltra'] = $datos->fields['estvaltra'];
					$_SESSION['la_empresa']['nitemp'] = $datos->fields['nitemp'];
					$_SESSION['la_empresa']['estemp'] = $datos->fields['estemp'];
					$_SESSION['la_empresa']['ciuemp'] = $datos->fields['ciuemp'];
					$_SESSION['la_empresa']['zonpos'] = $datos->fields['zonpos'];
					$_SESSION['la_empresa']['estmodape'] = $datos->fields['estmodape'];
					$_SESSION['la_empresa']['estdesiva'] = $datos->fields['estdesiva'];
					$_SESSION['la_empresa']['estprecom'] = $datos->fields['estprecom'];
					$_SESSION['la_empresa']['estmodsepsoc'] = $datos->fields['estmodsepsoc'];
					$_SESSION['la_empresa']['codorgsig'] = $datos->fields['codorgsig'];
					$_SESSION['la_empresa']['socbieser'] = $datos->fields['socbieser'];
					$_SESSION['la_empresa']['estmodest'] = $datos->fields['estmodest'];
					$_SESSION['la_empresa']['salinipro'] = $datos->fields['salinipro'];
					$_SESSION['la_empresa']['salinieje'] = $datos->fields['salinieje'];
					$_SESSION['la_empresa']['numordcom'] = $datos->fields['numordcom'];
					$_SESSION['la_empresa']['numordser'] = $datos->fields['numordser'];
					$_SESSION['la_empresa']['numsolpag'] = $datos->fields['numsolpag'];
					$_SESSION['la_empresa']['nomorgads'] = $datos->fields['nomorgads'];
					$_SESSION['la_empresa']['numlicemp'] = $datos->fields['numlicemp'];
					$_SESSION['la_empresa']['modageret'] = $datos->fields['modageret'];
					$_SESSION['la_empresa']['nomres'] = $datos->fields['nomres'];
					$_SESSION['la_empresa']['concomiva'] = $datos->fields['concomiva'];
					$_SESSION['la_empresa']['cedben'] = $datos->fields['cedben'];
					$_SESSION['la_empresa']['nomben'] = $datos->fields['nomben'];
					$_SESSION['la_empresa']['scctaben'] = $datos->fields['scctaben'];
					$_SESSION['la_empresa']['estmodiva'] = $datos->fields['estmodiva'];
					$_SESSION['la_empresa']['activo_t'] = $datos->fields['activo_t'];
					$_SESSION['la_empresa']['pasivo_t'] = $datos->fields['pasivo_t'];
					$_SESSION['la_empresa']['resultado_t'] = $datos->fields['resultado_t'];
					$_SESSION['la_empresa']['c_financiera'] = $datos->fields['c_financiera'];
					$_SESSION['la_empresa']['c_fiscal'] = $datos->fields['c_fiscal'];
					$_SESSION['la_empresa']['diacadche'] = $datos->fields['diacadche'];
					$_SESSION['la_empresa']['codasiona'] = $datos->fields['codasiona'];
					$_SESSION['la_empresa']['conrecdoc'] = $datos->fields['conrecdoc'];
					$_SESSION['la_empresa']['estvaldis'] = $datos->fields['estvaldis'];
					$_SESSION['la_empresa']['nroivss'] = $datos->fields['nroivss'];
					$_SESSION['la_empresa']['nomrep'] = $datos->fields['nomrep'];
					$_SESSION['la_empresa']['cedrep'] = $datos->fields['cedrep'];
					$_SESSION['la_empresa']['telfrep'] = $datos->fields['telfrep'];
					$_SESSION['la_empresa']['cargorep'] = $datos->fields['cargorep'];
					$_SESSION['la_empresa']['estretiva'] = $datos->fields['estretiva'];
					$_SESSION['la_empresa']['clactacon'] = $datos->fields['clactacon'];
					$_SESSION['la_empresa']['estempcon'] = $datos->fields['estempcon'];
					$_SESSION['la_empresa']['codaltemp'] = $datos->fields['codaltemp'];
					$_SESSION['la_empresa']['basdatcon'] = $datos->fields['basdatcon'];
					$_SESSION['la_empresa']['estcamemp'] = $datos->fields['estcamemp'];
					$_SESSION['la_empresa']['estparsindis'] = $datos->fields['estparsindis'];
					$_SESSION['la_empresa']['basdatcmp'] = $datos->fields['basdatcmp'];
					$_SESSION['la_empresa']['estciespg'] = $datos->fields['estciespg'];
					$_SESSION['la_empresa']['estciespi'] = $datos->fields['estciespi'];
					$_SESSION['la_empresa']['confinstr'] = $datos->fields['confinstr'];
					$_SESSION['la_empresa']['estintcred'] = $datos->fields['estintcred'];
					$_SESSION['la_empresa']['estciescg'] = $datos->fields['estciescg'];
					$_SESSION['la_empresa']['estvalspg'] = $datos->fields['estvalspg'];
					$_SESSION['la_empresa']['ctaspgrec'] = $datos->fields['ctaspgrec'];
					$_SESSION['la_empresa']['ctaspgced'] = $datos->fields['ctaspgced'];
					$_SESSION['la_empresa']['estmodpartsep'] = $datos->fields['estmodpartsep'];
					$_SESSION['la_empresa']['estmodpartsoc'] = $datos->fields['estmodpartsoc'];
					$_SESSION['la_empresa']['estmanant'] = $datos->fields['estmanant'];
					$_SESSION['la_empresa']['estpreing'] = $datos->fields['estpreing'];
					$_SESSION['la_empresa']['concommun'] = $datos->fields['concommun'];
					$_SESSION['la_empresa']['confiva'] = $datos->fields['confiva'];
					$_SESSION['la_empresa']['confi_ch'] = $datos->fields['confi_ch'];
					$_SESSION['la_empresa']['casconmov'] = $datos->fields['casconmov'];
					$_SESSION['la_empresa']['ctaresact'] = $datos->fields['ctaresact'];
					$_SESSION['la_empresa']['confi_ch'] = $datos->fields['confi_ch'];
					$_SESSION['la_empresa']['ctaresant'] = $datos->fields['ctaresant'];
					$_SESSION['la_empresa']['estvaldisfin'] = $datos->fields['estvaldisfin'];
					$_SESSION['la_empresa']['dedconproben'] = $datos->fields['dedconproben'];
					$_SESSION['la_empresa']['estaprsep'] = $datos->fields['estaprsep'];
					$_SESSION['la_empresa']['sujpasesp'] = $datos->fields['sujpasesp'];
					$_SESSION['la_empresa']['bloanu'] = $datos->fields['bloanu'];
					$_SESSION['la_empresa']['estretmil'] = $datos->fields['estretmil'];
					$_SESSION['la_empresa']['concommil'] = $datos->fields['concommil'];
					$_SESSION['la_empresa']['contintmovban'] = $datos->fields['contintmovban'];
					$_SESSION['la_empresa']['valinimovban'] = $datos->fields['valinimovban'];
					$_SESSION['la_empresa']['estintban'] = $datos->fields['estintban'];
					$_SESSION['la_empresa']['cueproacu'] = $datos->fields['cueproacu'];
					$_SESSION['la_empresa']['cuedepamo'] = $datos->fields['cuedepamo'];
					$_SESSION['la_empresa']['valclacon'] = $datos->fields['valclacon'];
					$_SESSION['la_empresa']['valcomrd'] = $datos->fields['valcomrd'];
					$_SESSION['la_empresa']['ctaejeprecie'] = $datos->fields['ctaejeprecie'];
					$_SESSION['la_empresa']['estaprsoc'] = $datos->fields['estaprsoc'];
					$_SESSION['la_empresa']['estaprcxp'] = $datos->fields['estaprcxp'];
					$_SESSION['la_empresa']['scforden_h'] = $datos->fields['scforden_h'];
					$_SESSION['la_empresa']['scforden_d'] = $datos->fields['scforden_d'];
					$_SESSION['la_empresa']['tiesesact'] = $datos->fields['tiesesact'];
					$_SESSION['la_empresa']['repcajchi'] = $datos->fields['repcajchi'];
					$_SESSION['la_empresa']['estafenc'] = $datos->fields['estafenc'];
					$_SESSION['la_empresa']['blocon'] = $datos->fields['blocon'];
					$_SESSION['la_empresa']['intblocon'] = $datos->fields['intblocon'];
					$_SESSION['la_empresa']['capiva'] = $datos->fields['capiva'];
					$_SESSION['la_empresa']['parcapiva'] = $datos->fields['parcapiva'];
					$_SESSION['la_empresa']['estciesem'] = $datos->fields['estciesem'];
					$_SESSION['la_empresa']['ciesem1'] = $datos->fields['ciesem1'];
					$_SESSION['la_empresa']['ciesem2'] = $datos->fields['ciesem2'];
					$_SESSION['la_empresa']['estceniva'] = $datos->fields['estceniva'];
					$_SESSION['la_empresa']['codestprocen1'] = $datos->fields['codestprocen1'];
					$_SESSION['la_empresa']['codestprocen2'] = $datos->fields['codestprocen2'];
					$_SESSION['la_empresa']['codestprocen3'] = $datos->fields['codestprocen3'];
					$_SESSION['la_empresa']['codestprocen4'] = $datos->fields['codestprocen4'];
					$_SESSION['la_empresa']['codestprocen5'] = $datos->fields['codestprocen5'];
					$_SESSION['la_empresa']['esclacen'] = $datos->fields['esclacen'];
					$_SESSION['la_empresa']['estspgdecimal'] = $datos->fields['estspgdecimal'];
					$_SESSION['la_empresa']['nivapro'] = $datos->fields['nivapro'];
					$_SESSION['la_empresa']['envcorsup'] = $datos->fields['envcorsup'];
					$_SESSION['la_empresa']['estcomobr'] = $datos->fields['estcomobr'];
					$_SESSION['la_empresa']['estbenalt'] = $datos->fields['estbenalt'];
					$_SESSION['la_empresa']['numrefcarord'] = $datos->fields['numrefcarord'];
					$_SESSION['la_empresa']['estcossig'] = $datos->fields['estcossig'];	   
					$_SESSION['la_empresa']['m01'] = $datos->fields['m01'];	   
					$_SESSION['la_empresa']['m02'] = $datos->fields['m02'];	   
					$_SESSION['la_empresa']['m03'] = $datos->fields['m03'];	   
					$_SESSION['la_empresa']['m04'] = $datos->fields['m04'];	   
					$_SESSION['la_empresa']['m05'] = $datos->fields['m05'];	   
					$_SESSION['la_empresa']['m06'] = $datos->fields['m06'];	   
					$_SESSION['la_empresa']['m07'] = $datos->fields['m07'];	   
					$_SESSION['la_empresa']['m08'] = $datos->fields['m08'];	   
					$_SESSION['la_empresa']['m09'] = $datos->fields['m09'];	   
					$_SESSION['la_empresa']['m10'] = $datos->fields['m10'];	   
					$_SESSION['la_empresa']['m11'] = $datos->fields['m11'];	   
					$_SESSION['la_empresa']['m12'] = $datos->fields['m12'];
					$_SESSION['la_empresa']['estcencos'] = $datos->fields['estcencos'];
					$_SESSION['la_empresa']['cencosact'] = $datos->fields['cencosact'];
					$_SESSION['la_empresa']['cencospas'] = $datos->fields['cencospas'];
					$_SESSION['la_empresa']['cencosing'] = $datos->fields['cencosing'];
					$_SESSION['la_empresa']['cencosgas'] = $datos->fields['cencosgas'];
					$_SESSION['la_empresa']['cencosres'] = $datos->fields['cencosres'];
					$_SESSION['la_empresa']['cencoscap'] = $datos->fields['cencoscap'];
					$_SESSION['la_empresa']['valestpre'] = $datos->fields['valestpre'];
					$_SESSION['la_empresa']['nivvalest'] = $datos->fields['nivvalest']; 	   
					$_SESSION['la_empresa']['estmodprog'] = $datos->fields['estmodprog']; 	   
					$_SESSION['tiempo_session'] = $datos->fields['tiesesact'];
					$_SESSION['bloqueo_clave'] = $datos->fields['blocon'];
					$_SESSION['intentos_bloqueo'] = $datos->fields['intblocon'];
					$_SESSION['la_empresa']['estspidecimal'] = $datos->fields['estspidecimal'];
					$_SESSION['la_empresa']['estantspg'] = $datos->fields['estantspg'];
					$_SESSION['la_empresa']['estvarpar'] = $datos->fields['estvarpar'];
					$_SESSION['la_empresa']['inicencos'] = $datos->fields['inicencos'];
					$_SESSION['la_empresa']['estfilpremod'] = $datos->fields['estfilpremod'];
					$_SESSION['la_empresa']['blopresep'] = $datos->fields['blopresep'];
					$_SESSION['la_empresa']['codperalf'] = $datos->fields['codperalf'];
					$_SESSION['la_empresa']['blonumche'] = $datos->fields['blonumche'];
					$_SESSION['la_empresa']['estpereli'] = $datos->fields['estpereli'];
					$_SESSION['la_empresa']['numdecper'] = $datos->fields['numdecper'];
					$_SESSION['la_empresa']['estrescxp'] = $datos->fields['estrescxp'];
					$_SESSION['la_empresa']['estretislr'] = $datos->fields['estretislr'];
					$_SESSION['la_empresa']['reucon'] = $datos->fields['reucon'];
					$_SESSION['la_empresa']['nroconreu'] = $datos->fields['nroconreu'];
					$_SESSION["la_empresa"]["candeccon"] = $datos->fields['candeccon'];
					$_SESSION["la_empresa"]["tipconmon"] = $datos->fields['tipconmon'];
					$_SESSION["la_empresa"]["redconmon"] = $datos->fields['redconmon'];
					$_SESSION["la_empresa"]["estcanret"] = $datos->fields['estcanret'];
					$_SESSION["la_empresa"]["estvercta"] = $datos->fields['estvercta'];
					$_SESSION["la_empresa"]["estconlot"] = $datos->fields['estconlot'];
					$_SESSION["la_empresa"]["estconcom"] = $datos->fields['estconcom'];
					$_SESSION["la_empresa"]["nroinicom"] = $datos->fields['nroinicom'];
					$_SESSION["la_empresa"]["estcommas"] = $datos->fields['estcommas'];
					$_SESSION["la_empresa"]["costo"] = '';
					if(isset($datos->fields['costo']))
					{
						$_SESSION["la_empresa"]["costo"] = $datos->fields['costo'];
					}
					$_SESSION["la_empresa"]["filindspg"] = 0;
					if(isset($datos->fields['filindspg']))
					{
						$_SESSION["la_empresa"]["filindspg"] = $datos->fields['filindspg'];
					}
				}
				$datos->close();
				unset($objEmpresa);
			}
		break;		
		
		case 'iniciarsesion':							
			require_once('../../modelo/sss/sigesp_dao_sss_usuario.php');
			if (isUTF8($objdata->codusuario))
			{
				$objdata->codusuario = utf8_to_latin9($objdata->codusuario);
			}
			if (isUTF8($objdata->pasusuario))
			{
				$objdata->pasusuario = utf8_to_latin9($objdata->pasusuario);
			}
			$objUsuario = new Usuario();
			$objUsuario->codemp = $_SESSION['la_empresa']['codemp'];
			$objUsuario->codusu = $objdata->codusuario;
			$objUsuario->pwdusu   = $objdata->pasusuario;
			$objUsuario->nomfisico = 'sigesp_vis_sss_usuario.html';
			$_SESSION['la_logusr'] = $objUsuario->codusu;	
			$objUsuario->verificarUsuario();
			if(!$objUsuario->valido)
			{
				unset($_SESSION['la_logusr']);
			}
			$arreglo['valido']  = $objUsuario->valido;
			$arreglo['mensaje'] = $objUsuario->mensaje;
			$arreglo['iniciosession'] = $objUsuario->iniciosession;
			$_SESSION['session_activa']=time();
			$textJso  = array('raiz'=>$arreglo);
			$textJson = json_encode($textJso);
			echo $textJson;
			unset($objUsuario);
		break;

		case 'cambiarbd':
			require_once('../../modelo/sss/sigesp_dao_sss_usuario.php');
			$objUsuario = new Usuario();
			$objUsuario->codemp = $_SESSION['la_empresa']['codemp'];
			$objUsuario->codusu = $_SESSION['la_logusr'];
			$objUsuario->pwdusu = $_SESSION['la_pasusu'];			
			$objUsuario->verificarUsuario();
			$arreglo['valido']  = $objUsuario->valido;
			$arreglo['mensaje'] = $objUsuario->mensaje;
			$_SESSION['session_activa']=time();
			$textJso  = array('raiz'=>$arreglo);
			$textJson = json_encode($textJso);
			echo $textJson;
			unset($objUsuario);	
		break;		
	}
}	
?>
