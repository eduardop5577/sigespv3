<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
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
require_once('../../base/librerias/php/general/Json.php');
require_once('sigesp_ctr_siv_servicio.php');

if ($_POST['ObjSon']) 	
{
	$submit = str_replace("\\","",$_POST['ObjSon']);
	$json = new Services_JSON;	
	$ArJson = $json->decode($submit);
	$evento = $ArJson->operacion;
	
	switch ($evento)
	{
		case 'catarticulo':
			$sqlvaldis = "";
			$oservicio = new ServicioSiv ('siv_articulo');
			$digspgctas=$datosempresa["soc_gastos"];
			if (!empty($digspgctas)){
				$arrspgctas = explode(",",$digspgctas);
				if (!empty($arrspgctas)){
					$li_totrows = count($arrspgctas);
					for ($li_i=0;$li_i<$li_totrows;$li_i++){
						if ($li_i==0){
							$valsqlaux = $valsqlaux." AND (siv_articulo.spg_cuenta like '".$arrspgctas[$li_i]."%'";
						}
						else{
							$valsqlaux = $valsqlaux." OR siv_articulo.spg_cuenta like '".$arrspgctas[$li_i]."%'";
						}
							 
						if ($li_i==$li_totrows-1){
							      $valsqlaux = $valsqlaux.")";
						}						   
					}
					
					if($ArJson->tipsepbie=='M'){
						$cadsqlaux = " AND siv_articulo.estact=0";
					}
					elseif($ArJson->tipsepbie=='A'){
						$cadsqlaux = " AND siv_articulo.estact=1";
					}
			
					if($ArJson->codtipart!=''){
						$cadsqlaux =$cadsqlaux ." AND siv_articulo.codtipart like '%".$ArJson->codtipart."%'";
					}
			
					if ($datosempresa["estparsindis"]==1){
			                $sqlvaldis = ",(SELECT (spg_cuentas.asignado-(spg_cuentas.comprometido+spg_cuentas.precomprometido)+spg_cuentas.aumento-spg_cuentas.disminucion)
											  FROM spg_cuentas
											 WHERE spg_cuentas.codestpro1 = '".$ArJson->codestpro1."'
											   AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."'
											   AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."'
											   AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."'
											   AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."'
											   AND spg_cuentas.estcla='".$ArJson->estcla."'
						    				   AND spg_cuentas.codemp=siv_articulo.codemp
											   AND spg_cuentas.spg_cuenta = siv_articulo.spg_cuenta) AS disponibilidad";
					}
									
					$cadenasql = "SELECT siv_articulo.codart AS coditem,siv_articulo.denart AS denitem,siv_articulo.ultcosart AS preitem,
										 TRIM(siv_articulo.spg_cuenta) AS spg_cuenta,
										 siv_unidadmedida.denunimed, siv_unidadmedida.unidad,
										 (SELECT COUNT(spg_cuentas.spg_cuenta) 
										    FROM
											 spg_cuentas
							  			    WHERE 
										     spg_cuentas.codestpro1 = '".$ArJson->codestpro1."'
											 AND spg_cuentas.codestpro2 = '".$ArJson->codestpro2."'
											 AND spg_cuentas.codestpro3 = '".$ArJson->codestpro3."'
											 AND spg_cuentas.codestpro4 = '".$ArJson->codestpro4."'
											 AND spg_cuentas.codestpro5 = '".$ArJson->codestpro5."'
					           				 AND spg_cuentas.estcla = '".$ArJson->estcla."'
											 AND siv_articulo.codemp = spg_cuentas.codemp
											 AND siv_articulo.spg_cuenta = spg_cuentas.spg_cuenta) AS existecuenta$sqlvaldis 
					  				FROM siv_articulo, siv_unidadmedida
								   WHERE siv_articulo.codemp='".$datosempresa["codemp"]."'
									 AND siv_articulo.codart like '%".$ArJson->coditem."%'
									 AND siv_articulo.denart like '%".$ArJson->denitem."%'$cadsqlaux 									 
									 AND siv_articulo.codunimed = siv_unidadmedida.codunimed$valsqlaux
								   ORDER BY siv_articulo.codart";
					$datos = $oservicio->buscarSql($cadenasql);					
					$ObjSon = generarJson($datos);
					echo $datosempresa["estparsindis"]."|".$ObjSon;
		        }
			}
			break;
		
		case 'combotipoart':
			$oservicio = new ServicioSiv ('siv_tipoarticulo');
			$datos = $oservicio->buscarTodos('codtipart',1);					
			$ObjSon = generarJson($datos);
			echo $ObjSon;	
			break;
		
	}
}
?>