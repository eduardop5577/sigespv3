<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();
	ini_set('max_execution_time ','0');
$li_diasem = date('w');
switch ($li_diasem){
  case '0': $ls_diasem='Domingo';
  break; 
  case '1': $ls_diasem='Lunes';
  break;
  case '2': $ls_diasem='Martes';
  break;
  case '3': $ls_diasem='Mi&eacute;rcoles';
  break;
  case '4': $ls_diasem='Jueves';
  break;
  case '5': $ls_diasem='Viernes';
  break;
  case '6': $ls_diasem='S&aacute;bado';
  break;
  $ls_ruta="txt/errores/detalles";
  @mkdir($ls_ruta,0755);

}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Traspaso de Movimientos Bancarios</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}

-->
</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
  $ls_ruta="txt";
  @mkdir($ls_ruta,0755);
	//--------------------------------------------------------------
   function uf_cargarperiodos_bancos()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cargarnomina
		//		   Access: private
		//	  Description: Función que obtiene todas las nóminas y las carga en un 
		//				   combo para seleccionarlas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_class_mensajes.php");
		$io_mensajes=new class_mensajes();
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		

		$ls_sql="SELECT DISTINCT(codperi) FROM scb_movbco";
		
		$rs_data=$io_sql->select($ls_sql);
       	print "<select name='cmbperi' id='cmbperi' style='width:150px'>";
        print " <option value='' selected>--Seleccione un Periodo--</option>";
		if($rs_data===false)
		{
        	$io_mensajes->message("Clase->Seleccionar Periodo Método->uf_cargarperiodos_bancos Error->".$io_funciones->uf_convertirmsg($io_sql->message)); 
			print "<script language=JavaScript>";
			print "	close();";
			print "</script>";		
		}
		else
		{
			while($row=$io_sql->fetch_row($rs_data))
			{
				$ls_codperi=$row["codperi"];
            	print "<option value='".$ls_codperi."'>".$ls_codperi."</option>";				
			}
			$io_sql->free_result($rs_data);
		}
       	print "</select>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);	
		unset($io_mensajes);		
		unset($io_funciones);		
        unset($ls_codemp);
   }
   //--------------------------------------------------------------
	$ls_nombrearchivo=$ls_ruta."/errores.txt";
	if (file_exists("$ls_nombrearchivo"))
	{
		if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
		{
			$lb_valido=false;
		}
		else
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
		}
	}
	else
	{
		$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
	}
	
	require_once("class_folder/sigesp_ins_c_traspaso_mov_banco.php");
	$io_class_reprocesar=new sigesp_ins_c_traspaso_mov_banco();
	$ls_operacion= "NUEVO";
	$lb_chk_saldo=false;
	if( array_key_exists("operacion",$_POST))
	{
		$ls_operacion= $_POST["operacion"];
		if(array_key_exists("chkreprocesar_saldo",$_POST))
		{
			$lb_chk_saldo=true;
		}
	}
	if($ls_operacion=="EJECUTAR")
	{
		if($lb_chk_saldo)
		{
				$ls_codperi=$_POST["cmbperi"];
				$lb_movbanco=$io_class_reprocesar->uf_select_movbanco_peri($ls_codperi);
				if ($lb_movbanco)
				{
					$li_totmov=$io_class_reprocesar->DS->getRowCount("codperi");
					for($li_i=1;$li_i<=$li_totmov;$li_i++)
					{
						$ls_codigoemp=$io_class_reprocesar->DS->data["codemp"][$li_i];
						$ls_codban=$io_class_reprocesar->DS->data["codban"][$li_i];
						$ls_ctaban=$io_class_reprocesar->DS->data["ctaban"][$li_i];
						$ls_numdoc=$io_class_reprocesar->DS->data["numdoc"][$li_i];
						$ls_codope=$io_class_reprocesar->DS->data["codope"][$li_i];
						$ls_estmov=$io_class_reprocesar->DS->data["estmov"][$li_i];
						$ls_codpro=$io_class_reprocesar->DS->data["cod_pro"][$li_i];
						$ls_cedbene=$io_class_reprocesar->DS->data["ced_bene"][$li_i];
						$ls_destino=$io_class_reprocesar->DS->data["tipo_destino"][$li_i];
						$ls_codconmov=$io_class_reprocesar->DS->data["codconmov"][$li_i];
						$ls_fecmov=$io_class_reprocesar->DS->data["fecmov"][$li_i];
						$ls_conmov=$io_class_reprocesar->DS->data["conmov"][$li_i];
						$ls_nomproben=$io_class_reprocesar->DS->data["nomproben"][$li_i];
						$ls_monto=$io_class_reprocesar->DS->data["monto"][$li_i];
						$ls_estbpd=$io_class_reprocesar->DS->data["estbpd"][$li_i];
						$ls_estcon=$io_class_reprocesar->DS->data["estcon"][$li_i];
						$ls_estcobing=$io_class_reprocesar->DS->data["estcobing"][$li_i];
						$ls_esttra=$io_class_reprocesar->DS->data["esttra"][$li_i];
						$ls_chevau=$io_class_reprocesar->DS->data["chevau"][$li_i];
						$ls_estimpche=$io_class_reprocesar->DS->data["estimpche"][$li_i];
						$ls_monobjret=$io_class_reprocesar->DS->data["monobjret"][$li_i];
						$ls_monret=$io_class_reprocesar->DS->data["monret"][$li_i];
						$ls_procede=$io_class_reprocesar->DS->data["procede"][$li_i];
						$ls_comprobante=$io_class_reprocesar->DS->data["comprobante"][$li_i];
						$ls_fecha=$io_class_reprocesar->DS->data["fecha"][$li_i];
						$ls_idmco=$io_class_reprocesar->DS->data["id_mco"][$li_i];
						$ls_emicheproc=$io_class_reprocesar->DS->data["emicheproc"][$li_i];
						$ls_emicheced=$io_class_reprocesar->DS->data["emicheced"][$li_i];
						$ls_emichenom=$io_class_reprocesar->DS->data["emichenom"][$li_i];
						$ls_emichefec=$io_class_reprocesar->DS->data["emichefec"][$li_i];
						$ls_estmovint=$io_class_reprocesar->DS->data["estmovint"][$li_i];
						$ls_codusu=$io_class_reprocesar->DS->data["codusu"][$li_i];
						$ls_codopeidb=$io_class_reprocesar->DS->data["codopeidb"][$li_i];
						$ls_aliidb=$io_class_reprocesar->DS->data["aliidb"][$li_i];
						$ls_feccon=$io_class_reprocesar->DS->data["feccon"][$li_i];
						$ls_estreglib=$io_class_reprocesar->DS->data["estreglib"][$li_i];
						$ls_numcarord=$io_class_reprocesar->DS->data["numcarord"][$li_i];
						$ls_numpolcon=$io_class_reprocesar->DS->data["numpolcon"][$li_i];
						$ls_coduniadmsig=$io_class_reprocesar->DS->data["coduniadmsig"][$li_i];
						$ls_codbansig=$io_class_reprocesar->DS->data["codbansig"][$li_i];
						$ls_fecordpagsig=$io_class_reprocesar->DS->data["fecordpagsig"][$li_i];
						$ls_tipdocressig=$io_class_reprocesar->DS->data["tipdocressig"][$li_i];
						$ls_numdocressig=$io_class_reprocesar->DS->data["numdocressig"][$li_i];
						$ls_estmodordpag=$io_class_reprocesar->DS->data["estmodordpag"][$li_i];
						$ls_codfuefin=$io_class_reprocesar->DS->data["codfuefin"][$li_i];
						$ls_forpagsig=$io_class_reprocesar->DS->data["forpagsig"][$li_i];
						$ls_medpagsig=$io_class_reprocesar->DS->data["medpagsig"][$li_i];
						$ls_codestprosig=$io_class_reprocesar->DS->data["codestprosig"][$li_i];
						$ls_nrocontrolop=$io_class_reprocesar->DS->data["nrocontrolop"][$li_i];
						$ls_fechaconta=$io_class_reprocesar->DS->data["fechaconta"][$li_i];
						$ls_fechaanula=$io_class_reprocesar->DS->data["fechaanula"][$li_i];
						$ls_conanu=$io_class_reprocesar->DS->data["conanu"][$li_i];
						$ls_estant=$io_class_reprocesar->DS->data["estant"][$li_i];
						$ls_docant=$io_class_reprocesar->DS->data["docant"][$li_i];
						$ls_monamo=$io_class_reprocesar->DS->data["monamo"][$li_i];
						$ls_numordpagmin=$io_class_reprocesar->DS->data["numordpagmin"][$li_i];
						$ls_codtipfon=$io_class_reprocesar->DS->data["codtipfon"][$li_i];
						$ls_estserext=$io_class_reprocesar->DS->data["estserext"][$li_i];
						$ls_estmovcob=$io_class_reprocesar->DS->data["estmovcob"][$li_i];
						$ls_codper=$io_class_reprocesar->DS->data["codper"][$li_i];
						$ls_codperi=$io_class_reprocesar->DS->data["codperi"][$li_i];
						$ls_numconint='P-000'.$ls_codperi.$li_i;
						$ls_estapribs=$io_class_reprocesar->DS->data["estapribs"][$li_i];
						$ls_estxmlibs=$io_class_reprocesar->DS->data["estxmlibs"][$li_i];
						$ls_tranoreglib=$io_class_reprocesar->DS->data["tranoreglib"][$li_i];
						$ls_estcondoc=$io_class_reprocesar->DS->data["estcondoc"][$li_i];
						$ls_fecenvfir=$io_class_reprocesar->DS->data["fecenvfir"][$li_i];
						$ls_fecenvcaj=$io_class_reprocesar->DS->data["fecenvcaj"][$li_i];
											
						$lb_exist_banco=$io_class_reprocesar->uf_buscar_bancos($ls_codban);
						if ($lb_exist_banco)
						{
							$lb_exist_cuenta=$io_class_reprocesar->uf_buscar_cuenta($ls_codban,$ls_ctaban);
							if ($lb_exist_cuenta)
							{
								
								$lb_valido1=$io_class_reprocesar->uf_insert_cabecera_movbanco($ls_codigoemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,
																									  $ls_estmov,$ls_codpro,$ls_cedbene,$ls_destino,$ls_codconmov,
																									  $ls_fecmov,$ls_conmov,$ls_nomproben,$ls_monto,$ls_estbpd,$ls_estcon,
																									  $ls_estcobing,$ls_esttra,$ls_chevau,$ls_estimpche,$ls_monobjret,$ls_monret,
																									  $ls_procede,$ls_comprobante,$ls_fecha,$ls_idmco,$ls_emicheproc,$ls_emicheced,
																									  $ls_emichenom,$ls_emichefec,$ls_estmovint,$ls_codusu,$ls_codopeidb,$ls_aliidb,
																									  $ls_feccon,$ls_estreglib,$ls_numcarord,$ls_numpolcon,$ls_coduniadmsig,$ls_codbansig,
																									  $ls_fecordpagsig,$ls_tipdocressig,$ls_numdocressig,$ls_estmodordpag,$ls_codfuefin,
																									  $ls_forpagsig,$ls_medpagsig,$ls_codestprosig,$ls_nrocontrolop,$ls_fechaconta,
																									  $ls_fechaanula,$ls_conanu,$ls_estant,$ls_docant,$ls_monamo,$ls_numordpagmin,
																									  $ls_codtipfon,$ls_estserext,$ls_estmovcob,$ls_numconint,$ls_codper,$ls_codperi,
																									  $ls_estapribs,$ls_estxmlibs,$ls_tranoreglib,$ls_estcondoc,$ls_fecenvfir,$ls_fecenvcaj);
								
								$lb_movbanco_dt=$io_class_reprocesar->uf_select_movbanco_dt_peri($ls_codperi,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ls_estmov);
								if($lb_movbanco_dt)
								{
									$li_totmovdt=$io_class_reprocesar->DS_R->getRowCount("codperi");
									
									for($li_s=1;$li_s<=$li_totmovdt;$li_s++)
									{
										$ls_codigoempdet=$io_class_reprocesar->DS_R->data["codemp"][$li_s];
										$ls_codbandet=$io_class_reprocesar->DS_R->data["codban"][$li_s];
										$ls_ctabandet=$io_class_reprocesar->DS_R->data["ctaban"][$li_s];
										$ls_numdocdet=$io_class_reprocesar->DS_R->data["numdoc"][$li_s];
										$ls_codopedet=$io_class_reprocesar->DS_R->data["codope"][$li_s];
										$ls_estmovdet=$io_class_reprocesar->DS_R->data["estmov"][$li_s];
										$ls_cuentadet=$io_class_reprocesar->DS_R->data["scg_cuenta"][$li_s];
										$ls_debhabdet=$io_class_reprocesar->DS_R->data["debhab"][$li_s];
										$ls_coddeddet=$io_class_reprocesar->DS_R->data["codded"][$li_s];
										$ls_documentodet=$io_class_reprocesar->DS_R->data["documento"][$li_s];
										$ls_desmovdet=$io_class_reprocesar->DS_R->data["desmov"][$li_s];
										$ls_procededet=$io_class_reprocesar->DS_R->data["procede_doc"][$li_s];
										$ls_montodet=$io_class_reprocesar->DS_R->data["monto"][$li_s];
										$ls_monobjretdet=$io_class_reprocesar->DS_R->data["monobjret"][$li_s];
										$ls_codperdet=$io_class_reprocesar->DS_R->data["codper"][$li_s];
										$ls_codperidet=$io_class_reprocesar->DS_R->data["codperi"][$li_s];
										
										if ($lb_valido1)
										{
											$lb_valido2=$io_class_reprocesar->uf_insert_dt_movbanco($ls_codigoempdet,$ls_codbandet,$ls_ctabandet,$ls_numdocdet,$ls_codopedet,
																									$ls_estmovdet,$ls_cuentadet,$ls_debhabdet,$ls_coddeddet,$ls_documentodet,
																									$ls_desmovdet,$ls_procededet,$ls_montodet,$ls_monobjretdet,$ls_codperdet,$ls_codperidet);
											
											if($lb_valido2)
											{
												$lb_valido=true;
											}
											else
											{
												$lb_valido=false;
												$ls_cadena="El detalle del movimiento ".$ls_numdoc." no pudo ser transferido.."."\r\n";
												if ($ls_creararchivo)
												{
													if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
													{
														$io_class_reprocesar->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
														$lb_valido=false;
													}
												}
												else
												{
													$io_class_reprocesar->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
													$lb_valido=false;
												}
												break;
											}
											
										}
										else
										{
											$lb_valido=false;
											$ls_cadena="El movimiento ".$ls_numdoc." no pudo ser transferido.."."\r\n";
											if ($ls_creararchivo)
											{
												if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
												{
													$io_class_reprocesar->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
													$lb_valido=false;
												}
											}
											else
											{
												$io_class_reprocesar->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
												$lb_valido=false;
											}
											break;
										
										}
									}
								}
							}
							
							else
							{
								$lb_valido=false;
								$ls_cadena="La cuenta bancaria ".$ls_ctaban." no existe para el banco ".$ls_codban." por favor chequee.."."\r\n";
								if ($ls_creararchivo)
								{
									if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
									{
										$io_class_reprocesar->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
										$lb_valido=false;
									}
								}
								else
								{
									$io_class_reprocesar->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
									$lb_valido=false;
								}
								break;
							}
						}
						else
						{
							$lb_valido=false;
							$ls_cadena="El Banco ".$ls_codban." no existe por favor chequee.. "."\r\n";
							if ($ls_creararchivo)
							{
								if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
								{
									$io_class_reprocesar->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
									$lb_valido=false;
								}
							}
							else
							{
								$io_class_reprocesar->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
								$lb_valido=false;
							}
							break;
						}
					}
				}
				else
				{
					$lb_valido=false;
				}
			if($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexión y liberamos la memoria
				$io_class_reprocesar->io_sql_destino->commit(); 
				$io_class_reprocesar->io_mensajes->message("Proceso de Traspaso Ejecutado Satisfactoriamente!");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexión y liberamos la memoria
				$io_class_reprocesar->io_sql_destino->rollback();
				$io_class_reprocesar->io_mensajes->message("No se logró transferir la data!");
				$io_class_reprocesar->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado para chequear los errores..");
			}
		}
	}
	unset($io_class_reprocesar);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
  <td width="778" height="20" colspan="11" bgcolor="#E7E7E7">
    <table width="778" border="0" align="center" cellpadding="0" cellspacing="0">			
      <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Instala</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table>
  </td>
  </tr>
  <tr>
    <td height="20" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
</table>
<form name="form1" method="post" action="">
<p>&nbsp;</p>
<table width="442" height="223" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="571" height="221" valign="top">
        <p>&nbsp;</p>
        <table width="360" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr class="titulo-ventana">
            <td colspan="3">Traspaso de Movimientos Bancarios</td>
          </tr>
          <tr class="formato-blanco">
            <td height="18">&nbsp;</td>
			<td class="toolbar" width="25">&nbsp;</td>
          	<td class="" width="25"><div align="center"><a href="javascript: ue_descargar('<?php print $ls_ruta;?>');"><img src="../shared/imagebank/tools20/download.gif" title='Descargar' alt="Salir" width="20" height="20" border="0"></a></div></td>
		  </tr>
          <tr class="formato-blanco">
            <td height="59">Periodo a Trasferir 
            <td width="241"><div align="left"><?php uf_cargarperiodos_bancos(); ?></div></td>
          </tr>
		  <tr class="formato-blanco">
            <td width="117" height="22"><div align="right">
              <input name="chkreprocesar_saldo" type="checkbox" class="sin-borde" id="chkreprocesar_saldo" value="1">
            </div></td>
            <td colspan="2"><div align="left">Transferir Movimientos de Banco </div></td>
          </tr>
          <tr class="formato-blanco">
            <td height="22">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr class="formato-blanco">
            <td height="22" colspan="3"><div align="center">
              <input name="botejecutar" type="button" class="boton" id="botejecutar" onClick="javascript:uf_ejecutar();" value="Ejecutar">
            </div></td>
          </tr>
          <tr class="formato-blanco">
            <td height="20">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table>
        <p>
          <input name="operacion" type="hidden" id="operacion">
        </p>
      </td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
function  uf_ejecutar()
{
	f=document.form1;
	if(f.chkreprocesar_saldo.checked)
	{
		f.operacion.value="EJECUTAR";
		f.action="sigesp_ins_p_movbancarios_trans.php";
		f.submit();
	}
	else
	{
      alert("Debe Tildar la Opción!");	
	}	
}
function ue_descargar(ruta)
{
	window.open("sigesp_ins_cat_directorio.php?ruta="+ruta+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

</script>
</html>