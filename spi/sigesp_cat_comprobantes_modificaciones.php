<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
require_once("../base/librerias/php/general/sigesp_lib_include.php");
$in=new sigesp_include();
$con=$in->uf_conectar();
require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../shared/class_folder/class_sigesp_int.php");
require_once("../shared/class_folder/class_sigesp_int_scg.php");
$int_scg=new class_sigesp_int_scg();
$msg=new class_mensajes();
$ds=new class_datastore();
$ds_procedencias=new class_datastore();
$SQL=new class_sql($con);
$SQL_cmp=new class_sql($con);
$arr=$_SESSION["la_empresa"];
$as_codemp=$arr["codemp"];
$ls_funciones=new class_funciones(); 
if(array_key_exists("operacion",$_POST))
{
	$ls_codemp=$as_codemp;
	$ls_operacion=$_POST["operacion"];
	$ls_comprobante=$_POST["txtdocumento"];
	$ls_fecdesde=$_POST["txtfechadesde"];
	$ls_fechasta=$_POST["txtfechahasta"];	
	$ls_procedencia=$_POST["procede"];
	$ls_provben	= $_POST["txtprovbene"];
	$ls_tipo=$_POST["tipo"];
	if(array_key_exists("tipocat",$_GET))
	{
		$ls_tipocat=$_GET["tipocat"];
	}
	else
	{
		$ls_tipocat="";
	} 
	if(array_key_exists("tiporep",$_GET))
	{
		$ls_tiporep=$_GET["tiporep"];
	}
	else
	{
		$ls_tiporep="";
	}
}
else
{
	$ls_operacion="";
	if(array_key_exists("tipocat",$_GET))
	{ 
		$ls_tipocat=$_GET["tipocat"];
	}
	else
	{
		$ls_tipocat="";
	}
	if(array_key_exists("tiporep",$_GET))
	{
		$ls_tiporep=$_GET["tiporep"];
	}
	else
	{
		$ls_tiporep="";
	}
	$ls_procedencia='SPIAUM';
	$ls_fecdesde=date("d/m/Y");
	$ls_fechasta=date("d/m/Y");
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Catalogo de Comprobantes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
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
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion" >
</p>
  <table width="700" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td colspan="2" class="titulo-celda"><div align="center">Cat&aacute;logo de Comprobantes</div></td>
    </tr>
  </table>
  <br>
  <div align="center">
    <table width="700" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="84" align="right">&nbsp;</td>
        <td colspan="3">&nbsp;</td>
        <td colspan="3"><div align="left"></div></td>
      </tr>
      <tr>
        <td align="right">Comprobante</td>
        <td colspan="3"><div align="left">
          <input name="txtdocumento" type="text" id="txtdocumento" onBlur="javascript: rellenar_cad(document.form1.txtdocumento.value,15,'doc');">        
        </div></td>
			<td width="59" align="right"><div align="left">Fecha </div></td>
            <td align="left"><div align="right">Desde
        </div></td>
            <td align="left"><input name="txtfechadesde" type="text" id="txtfechadesde" style="text-align:center" onBlur="valFecha(document.form1.txtfecha)" onKeyPress="javascript:currencyDate(this)" value="<?php print $ls_fecdesde;?>" size="18" maxlength="10" datepicker="true" ></td>
      </tr>
      <tr>
        <td height="27"><div align="right">Tipo</div></td>
        <td width="125" align="left">
          <select name="tipo" id="tipo" >
            <option value="P">Proveedor</option>
            <option value="B">Beneficiario</option>
            <option value="-" selected>Ninguno</option>
          </select>
        <a href="javascript:catprovbene(document.form1.tipo.value)"><img src="../shared/imagebank/tools15/buscar.gif" alt="Catalogo Proveedores/Beneficiarios" width="15" height="15" border="0"></a>		</td>
        <td width="67" align="left">
          <input name="txtprovbene" type="text" class="letras-negrita" id="txtprovbene" style="text-align:center" value="" size="10" maxlength="10">
        <label></label></td>
        <td width="205" align="left"><input name="txtdesproben" type="text" class="letras-negrita" id="txtdesproben" size="30" maxlength="250"></td>
        <td><div align="right"></div></td>
        <td width="38"><div align="right">Hasta </div></td>
        <td width="120" align="left"><input name="txtfechahasta" type="text" id="txtfechahasta" onBlur="valFecha(document.form1.txtfecha)" style="text-align:center" onKeyPress="javascript:currencyDate(this)" value="<?php print $ls_fechasta;?>" size="18" maxlength="10" datepicker="true" > </td>
      </tr>
      <tr>
	  <?php 
	       if($ls_procedencia=='SPIAUM')
		   {
			 $ls_aumento="selected";
		     $ls_disminucion="";
		   }
	       if($ls_procedencia=='SPIDIS')
		   {
			 $ls_aumento="";
		     $ls_disminucion="selected";
		   }
	  ?>
        <td height="10"><div align="right">Procedencia</div></td>
        <td colspan="4" align="left" ><select name="procede" id="select">
          <option value="SPIAUM" <?php  print $ls_aumento ?>>Aumento</option>
          <option value="SPIDIS" <?php  print $ls_disminucion ?>>Disminucion</option>
        </select></td>
        <td><div align="right"></div></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="15"><div align="left"></div></td>
        <td colspan="6"><div align="left">
          <table width="72" border="0" align="right" cellpadding="0" cellspacing="0" class="letras-peque&ntilde;as">
            <tr>
              <td width="28"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" width="20" height="20" border="0"></a></td>
              <td width="44"><a href="javascript: ue_search();">Buscar</a></td>
              </tr>
          </table>
        </div></td>
      </tr>
    </table>
    <?php

function uf_select_provbene($sql,$ls_cadena,$ls_campo)
{
	$data=$sql->select($ls_cadena);
     
	if($row=$sql->fetch_row($data))
	{
		$ls_provbene=$row[$ls_campo];
		
	}	
	else
	{
		$ls_provbene="";
	}
	$sql->free_result($data);
	return $ls_provbene;
}

if(($ls_tipocat=="")||($ls_tipocat=="repcompdes")||($ls_tipocat=="repcomphas")||($ls_tipocat=="rep_ejecucion"))
{
	print "<table width=700 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
		print "<td>Comprobante</td>";
		print "<td>Descripcion Comprobante</td>";
		print "<td>Procede</td>";
		print "<td>Fecha</td>";
		print "<td>Proveedor</td>";
		print "<td>Beneficiario</td>";
		print "<td>Nombre Prov./Benef.</td>";
		print "<td>Monto</td>";
	print "</tr>";
}
elseif(($ls_tipocat=="rep_proc_des")||($ls_tipocat=="rep_proc_has"))
{
	print "<table width=700 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
	print "<tr class=titulo-celda>";
		print "<td>Procede</td>";
		print "<td>Comprobante</td>";
		print "<td>Descripcion Comprobante</td>";
		print "<td>Fecha</td>";
		print "<td>Proveedor</td>";
		print "<td>Beneficiario</td>";
		print "<td>Nombre Prov./Benef.</td>";
		print "<td>Monto</td>";
	print "</tr>";
}
if($ls_operacion=="BUSCAR")
{ 
		if($ls_tipocat=="rep_ejecucion")
		{
		  $ls_estapro=0; // No Aprobadas 		  
		}
		elseif($ls_tipocat=="")
		{
		  $ls_estapro=1;  // Aprobadas
		 	 
		}
		if($ls_tiporep=="")
		{
		  $ls_cadena2 =" AND b.comprobante=c.comprobante AND b.codemp=c.codemp ";
		   $ls_sql="SELECT distinct a.procede,a.codemp,a.comprobante,c.fecha,a.ced_bene,a.cod_pro,a.descripcion,a.tipo_destino,b.monto".
		 		" FROM sigesp_cmp_md a,spi_dtmp_cmp b,spi_dt_cmp c ".
		  		" WHERE a.procede=b.procede AND a.tipo_comp=2 AND a.comprobante=b.comprobante AND a.fecha=b.fecha AND ".
				"        a.codemp=b.codemp AND a.codemp='".$as_codemp."' AND a.comprobante like '%".$ls_comprobante."%' AND".
				"	   a.procede like '%".$ls_procedencia."%' AND a.estapro='".$ls_estapro."'".
				"	   $ls_cadena2 " ;
		}
		else
		{
		  $ls_cadena2 =" ";
		  $ls_sql="SELECT distinct a.procede,a.codemp,a.comprobante,a.fecha,a.ced_bene,a.cod_pro,a.descripcion,a.tipo_destino,b.monto".
		 		" FROM sigesp_cmp_md a,spi_dtmp_cmp b ".
		  		" WHERE a.procede=b.procede AND a.tipo_comp=2 AND a.comprobante=b.comprobante AND a.fecha=b.fecha AND ".
				"        a.codemp=b.codemp AND a.codemp='".$as_codemp."' AND a.comprobante like '%".$ls_comprobante."%' AND".
				"	   a.procede like '%".$ls_procedencia."%' AND a.estapro='".$ls_estapro."'".
				"	   $ls_cadena2 " ;
		}
		$ls_order=" ORDER BY fecha ASC ,comprobante ASC ,procede ASC ";
		/*$ls_sql="SELECT distinct a.procede,a.codemp,a.comprobante,a.fecha,a.ced_bene,a.cod_pro,a.descripcion,a.tipo_destino,b.monto
		 		 FROM sigesp_cmp_md a,spi_dtmp_cmp b
		  		 WHERE a.procede=b.procede AND a.tipo_comp=2 AND a.comprobante=b.comprobante AND a.fecha=b.fecha AND
				       a.codemp=b.codemp AND a.codemp='".$as_codemp."' AND a.comprobante like '%".$ls_comprobante."%' AND
					   a.procede like '%".$ls_procedencia."%' AND a.estapro='".$ls_estapro."' " ;*/ 
	   
		if((($ls_fecdesde!="")&&($ls_fecdesde!="01/01/1900"))&&(($ls_fechasta!="")&&($ls_fechasta!="01/01/1900")))
		{
			$ls_fecdesde=$ls_funciones->uf_convertirdatetobd($ls_fecdesde);
			$ls_fechasta=$ls_funciones->uf_convertirdatetobd($ls_fechasta);
			$ls_sql=$ls_sql." AND a.fecha>='".$ls_fecdesde."' AND a.fecha<='".$ls_fechasta."'";
		}
		if(($ls_tipo=="P")&&($ls_provben!=""))
		{
			$ls_sql=$ls_sql." AND cod_pro like '".$ls_provben."'";
		}
		elseif(($ls_tipo=="B")&&($ls_provben!=""))
		{
			$ls_sql=$ls_sql." AND ced_bene like'".$ls_provben."'";
		}
		$ls_sql=$ls_sql.$ls_order;		
		$rs_cmp=$SQL_cmp->select($ls_sql);
		//print $ls_sql;
		$data=$rs_cmp;
	
		if($row=$SQL_cmp->fetch_row($rs_cmp))
		{   
		    $data=$SQL_cmp->obtener_datos($rs_cmp);
			$arrcols=array_keys($data);
			$totcol=count((array)$arrcols);
			$ds->data=$data;
			$totrow=$ds->getRowCount("comprobante"); 
			for($z=1;$z<=$totrow;$z++)
			{ 
				$ls_comprobante=$data["comprobante"][$z];
				$ls_descripcion=$data["descripcion"][$z];
				$ls_procedencia=$data["procede"][$z];
				$ls_fecha=$data["fecha"][$z];
				$ls_fecha=substr($ls_fecha,8,2)."/".substr($ls_fecha,5,2)."/".substr($ls_fecha,0,4);
				$ls_prov=$data["cod_pro"][$z];
				$ls_bene=$data["ced_bene"][$z];
				//$ls_provbene=$data["nombre"][$z];
				$ls_tip=$data["tipo_destino"][$z];
				$ldec_monto=$data["monto"][$z];
				if($ls_tip=="-")
				{
					$ls_tip="-";
					$ls_prov="----------";
					$ls_bene="----------";
					$ls_provbene="Ninguno";
				}
				elseif($ls_tip=="B")
				{
					$ls_tip="B";
					$ls_provbene= uf_select_provbene($SQL,"SELECT nombene FROM rpc_beneficiario WHERE ced_bene='".$ls_bene."'","nombene");
					$ls_provbene= $ls_provbene.uf_select_provbene($SQL,"SELECT apebene FROM rpc_beneficiario WHERE ced_bene='".$ls_bene."'","apebene");
				}
				elseif($ls_tip="P")
				{
					$ls_tip="P";
					$ls_provbene= uf_select_provbene($SQL,"SELECT nompro FROM rpc_proveedor WHERE cod_pro='".$ls_prov."'","nompro");
				}
				else
				{
					$ls_tip="-";
					$ls_prov="----------";
					$ls_bene="----------";
					$ls_provbene="Ninguno";
				}
					if($ls_tipocat=="")
					{
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:uf_aceptar('$ls_comprobante','$ls_descripcion','$ls_procedencia',
										   '$ls_fecha','$ls_tip','$ls_prov','$ls_bene','$ls_provbene');\">".$ls_comprobante."</a></td>";
						print "<td>".$ls_descripcion."</td>";				
						print "<td>".$ls_procedencia."</td>";						
						print "<td>".$ls_fecha."</td>";
						print "<td>".$ls_prov."</td>";
						print "<td>".$ls_bene."</td>";				
						print "<td>".$ls_provbene."</td>";
						print "<td>".$ldec_monto."</td>";										
						print "</tr>";		
				  }		
				  if($ls_tipocat=="repcompdes")
				  {
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: uf_aceptar_repcompdes('$ls_comprobante','$ls_descripcion','$ls_procedencia','$ls_fecha','$ls_tip','$ls_prov','$ls_bene','$ls_provbene');\">".$ls_comprobante."</a></td>";
						print "<td>".$ls_descripcion."</td>";
						print "<td>".$ls_procedencia."</td>";				
						print "<td>".$ls_fecha."</td>";
						print "<td>".$ls_prov."</td>";
						print "<td>".$ls_bene."</td>";	
						print "<td>".$ls_provbene."</td>";					
						print "<td>".$ldec_monto."</td>";													
						print "</tr>";		
				  }
				  if($ls_tipocat=="repcomphas")
				  {
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: uf_aceptar_repcomphas('$ls_comprobante','$ls_descripcion','$ls_procedencia','$ls_fecha','$ls_tip','$ls_prov','$ls_bene','$ls_provbene');\">".$ls_comprobante."</a></td>";
						print "<td>".$ls_descripcion."</td>";
						print "<td>".$ls_procedencia."</td>";				
						print "<td>".$ls_fecha."</td>";
						print "<td>".$ls_prov."</td>";
						print "<td>".$ls_bene."</td>";				
						print "<td>".$ls_provbene."</td>";
						print "<td>".$ldec_monto."</td>";										
						print "</tr>";		
				  }
				  if($ls_tipocat=="rep_proc_des")
				  {
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: uf_aceptar_rep_proc_des('$ls_comprobante','$ls_descripcion','$ls_procedencia','$ls_fecha','$ls_tip','$ls_prov','$ls_bene','$ls_provbene');\">".$ls_procedencia."</a></td>";
						print "<td>".$ls_comprobante."</td>";				
						print "<td>".$ls_descripcion."</td>";
						print "<td>".$ls_fecha."</td>";
						print "<td>".$ls_prov."</td>";
						print "<td>".$ls_bene."</td>";				
						print "<td>".$ls_provbene."</td>";					
						print "<td>".$ldec_monto."</td>";										
						print "</tr>";		
				  }
				  if($ls_tipocat=="rep_proc_has")
				  {
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript: uf_aceptar_rep_proc_has('$ls_comprobante','$ls_descripcion','$ls_procedencia','$ls_fecha','$ls_tip','$ls_prov','$ls_bene','$ls_provbene');\">".$ls_procedencia."</a></td>";
						print "<td>".$ls_comprobante."</td>";				
						print "<td>".$ls_descripcion."</td>";
						print "<td>".$ls_fecha."</td>";
						print "<td>".$ls_prov."</td>";
						print "<td>".$ls_bene."</td>";				
						print "<td>".$ls_provbene."</td>";
						print "<td>".$ldec_monto."</td>";										
						print "</tr>";		
				  }
				  if($ls_tipocat=="rep_ejecucion")
				  {
						print "<tr class=celdas-blancas>";
						print "<td><a href=\"javascript:uf_aceptar_rep_ejecucion('$ls_comprobante','$ls_descripcion','$ls_procedencia',
										   '$ls_fecha','$ls_tip','$ls_prov','$ls_bene','$ls_provbene');\">".$ls_comprobante."</a></td>";
						print "<td>".$ls_descripcion."</td>";				
						print "<td>".$ls_procedencia."</td>";						
						print "<td>".$ls_fecha."</td>";
						print "<td>".$ls_prov."</td>";
						print "<td>".$ls_bene."</td>";				
						print "<td>".$ls_provbene."</td>";
						print "<td>".$ldec_monto."</td>";										
						print "</tr>";		
				  }
			}
		$SQL_cmp->free_result($rs_cmp);	
		}
		else
		{
			if(!empty($ls_procedencia))
			{
				?>
				<script >
				alert("No se han creado Comprobantes asociados a la procedencia seleccionada \n ? no poseen detalles presupuestarios                   relacionados.....");
				//close();
				</script>
				<?php
			}
			else
			{
				?>
				<script >
				alert("No se han creado Comprobantes Presupuestarios.....");
				close();
				</script>
				<?php
			}
		}
	}
print "</table>";
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >

   function uf_aceptar(comprobante,descripcion,procede,fecha,tipo,prov,bene,ls_provbene)
   {
		f=opener.document.form1;
		f.txtcomprobante.value=comprobante;
		f.txtcomprobante.readOnly=true;
		f.txtprocede.value=procede;
		f.txtprocede.readOnly=true;
		f.txtfecha.value=fecha;
		f.txtfecha.readOnly=true;
		close(); 
   }

  function uf_aceptar_repcompdes(comprobante,descripcion,procede,fecha,tipo,prov,bene,ls_provbene)
  {
		f=opener.document.form1;
		f.txtcompdes.value=comprobante;
		f.txtcompdes.readOnly=true;
		close();
  }
  
   function uf_aceptar_repcomphas(comprobante,descripcion,procede,fecha,tipo,prov,bene,ls_provbene)
   {
		f=opener.document.form1;
		f.txtcomphas.value=comprobante;
		f.txtcomphas.readOnly=true;
		close(); 
   }
   
   function uf_aceptar_rep_proc_des(comprobante,descripcion,procede,fecha,tipo,prov,bene,ls_provbene)
   {
		f=opener.document.form1;
		f.txtprocdes.value=procede;
		f.txtprocdes.readOnly=true;
		close(); 
   }
   
   function uf_aceptar_rep_proc_has(comprobante,descripcion,procede,fecha,tipo,prov,bene,ls_provbene)
   {
		f=opener.document.form1;
		f.txtprochas.value=procede;
		f.txtprochas.readOnly=true;
		close(); 
   }
   function uf_aceptar_rep_ejecucion(comprobante,descripcion,procede,fecha,tipo,prov,bene,ls_provbene)
   {
		f=opener.document.form1;
		f.txtcomprobante.value=comprobante;
		f.txtcomprobante.readOnly=true;
		f.txtprocede.value=procede;
		f.txtprocede.readOnly=true;
		f.txtfecha.value=fecha;
		f.txtfecha.readOnly=true;
		close(); 
   }
  function ue_search()
  {
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_cat_comprobantes_modificaciones.php?tiporep=<?php print $ls_tiporep;?>&tipocat=<?php print $ls_tipocat;?>";
	  f.submit();
  }
	function catprovbene(provbene)
	{
		f=document.form1;
		if(provbene=="P")
		{
			f.txtprovbene.disabled=false;	
			window.open("sigesp_catdinamic_prov.php","_blank","width=502,height=350");
		}
		else if(provbene=="B")
		{
			f.txtprovbene.disabled=false;	
			window.open("sigesp_catdinamic_bene.php","_blank","width=502,height=350");
		}
	}
	//Funciones de validacion de fecha.
	function rellenar_cad(cadena,longitud,campo)
	{
		var mystring=new String(cadena);
		cadena_ceros="";
		lencad=mystring.length;
	
		total=longitud-lencad;
		for(i=1;i<=total;i++)
		{
			cadena_ceros=cadena_ceros+"0";
		}
		cadena=cadena_ceros+cadena;
		if(campo=="doc")
		{
			document.form1.txtdocumento.value=cadena;
		}
		else
		{
			document.form1.txtcomprobante.value=cadena;
		}
	
	}
	
	function currencyDate(date)
  { 
	ls_date=date.value;
	li_long=ls_date.length;
	f=document.form1;
			 
		if(li_long==2)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(0,2);
			li_string=parseInt(ls_string,10);

			if((li_string>=1)&&(li_string<=31))
			{
				date.value=ls_date;
			}
			else
			{
				date.value="";
			}
			
		}
		if(li_long==5)
		{
			ls_date=ls_date+"/";
			ls_string=ls_date.substr(3,2);
			li_string=parseInt(ls_string,10);
			if((li_string>=1)&&(li_string<=12))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,3);
			}
		}
		if(li_long==10)
		{
			ls_string=ls_date.substr(6,4);
			li_string=parseInt(ls_string,10);
			if((li_string>=1900)&&(li_string<=2090))
			{
				date.value=ls_date;
			}
			else
			{
				date.value=ls_date.substr(0,6);
			}
		}
			//alert(ls_long);


  //  return false; 
   }
	

	  function valSep(oTxt){ 
    var bOk = false; 
    var sep1 = oTxt.value.charAt(2); 
    var sep2 = oTxt.value.charAt(5); 
    bOk = bOk || ((sep1 == "-") && (sep2 == "-")); 
    bOk = bOk || ((sep1 == "/") && (sep2 == "/")); 
    return bOk; 
   } 

   function finMes(oTxt){ 
    var nMes = parseInt(oTxt.value.substr(3, 2), 10); 
    var nAno = parseInt(oTxt.value.substr(6), 10); 
    var nRes = 0; 
    switch (nMes){ 
     case 1: nRes = 31; break; 
     case 2: nRes = 28; break; 
     case 3: nRes = 31; break; 
     case 4: nRes = 30; break; 
     case 5: nRes = 31; break; 
     case 6: nRes = 30; break; 
     case 7: nRes = 31; break; 
     case 8: nRes = 31; break; 
     case 9: nRes = 30; break; 
     case 10: nRes = 31; break; 
     case 11: nRes = 30; break; 
     case 12: nRes = 31; break; 
    } 
    return nRes + (((nMes == 2) && (nAno % 4) == 0)? 1: 0); 
   } 

   function valDia(oTxt){ 
    var bOk = false; 
    var nDia = parseInt(oTxt.value.substr(0, 2), 10); 
    bOk = bOk || ((nDia >= 1) && (nDia <= finMes(oTxt))); 
    return bOk; 
   } 

   function valMes(oTxt){ 
    var bOk = false; 
    var nMes = parseInt(oTxt.value.substr(3, 2), 10); 
    bOk = bOk || ((nMes >= 1) && (nMes <= 12)); 
    return bOk; 
   } 

   function valAno(oTxt){ 
    var bOk = true; 
    var nAno = oTxt.value.substr(6); 
    bOk = bOk && ((nAno.length == 2) || (nAno.length == 4)); 
    if (bOk){ 
     for (var i = 0; i < nAno.length; i++){ 
      bOk = bOk && esDigito(nAno.charAt(i)); 
     } 
    } 
    return bOk; 
   } 

   function valFecha(oTxt){ 
    var bOk = true; 
	
		if (oTxt.value != ""){ 
		 bOk = bOk && (valAno(oTxt)); 
		 bOk = bOk && (valMes(oTxt)); 
		 bOk = bOk && (valDia(oTxt)); 
		 bOk = bOk && (valSep(oTxt)); 
		 if (!bOk){ 
		  alert("Fecha inv?lida ,verifique el formato(Ejemplo: 10/10/2005) \n o introduzca una fecha correcta."); 
		  oTxt.value = "01/01/1900"; 
		  oTxt.focus(); 
		 } 
		}
	 
   }

  function esDigito(sChr){ 
    var sCod = sChr.charCodeAt(0); 
    return ((sCod > 47) && (sCod < 58)); 
   }
	
</script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
</html>
