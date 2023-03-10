<?php
/***********************************************************************************
* @fecha de modificacion: 15/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.formulario.submit();";
		print "</script>";		
	}
	require_once("class_folder/class_funciones_sep.php");
	$io_fun_cxp=new class_funciones_sep();
	$ls_tipo=$io_fun_cxp->uf_obtenertipo();
	$ls_numrecdoc=$io_fun_cxp->uf_obtenervalor_get("numrecdoc","");
	$li_subtotal=$io_fun_cxp->uf_obtenervalor_get("subtotal","0,00");
	$ls_procede=$io_fun_cxp->uf_obtenervalor_get("procede","0,00");
	unset($io_fun_cxp);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Otros Cr&eacute;ditos</title>
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
</head>
<script type="text/javascript"  src="js/funcion_sep.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<body onLoad="javascript: ue_search();">
<form name="formulario" method="post" action="">
<input name="campoorden" type="hidden" id="campoorden" value="codcar">
<input name="orden" type="hidden" id="orden" value="ASC">
<input name="tipo" type="hidden" id="tipo" value="<?php print $ls_tipo; ?>">
<input name="numrecdoc" type="hidden" id="numrecdoc" value="<?php print $ls_numrecdoc; ?>">
<input name="subtotal" type="hidden" id="subtotal" value="<?php print $li_subtotal; ?>">
<input name="procede" type="hidden" id="procede" value="<?php print $ls_procede; ?>">
<input name="totrow" type="hidden" id="totrow">
<input name="ajustar" type="hidden" id="ajustar">
  <table width="640" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td height="20" colspan="2" class="titulo-ventana">Otros Cr&eacute;ditos </td>
    </tr>
  </table>
	<p>
  <div id="resultados" align="center"></div>	
	</p>
</form>      
</body>
<script >
function ue_cerrar()
{
	close();
}

function ue_calcular(fila)
{
	f=document.formulario;
	marcado=eval("f.chkcargos"+fila+".checked");
	if(marcado==true)
	{
		baseimponible=eval("f.txtbaseimp"+fila+".value");
		baseimponible=ue_formato_calculo(baseimponible);
		if(parseFloat(baseimponible)>0)
		{
			formula=eval("f.formula"+fila+".value");
			while(formula.indexOf("$LD_MONTO")!=-1)
			{ 
				formula=formula.replace("$LD_MONTO",baseimponible);
			} 	
			while(formula.indexOf("ROUND")!=-1)
			{ 
				formula=formula.replace("ROUND","redondear");
			} 
			formula=formula.replace("IIF","ue_iif");

			cargo=eval(formula);
			cargo=redondear(cargo,2);
			cargo=uf_convertir(cargo);
			eval("f.txtmonimp"+fila+".value='"+cargo+"'"); 
		}
		else
		{
			alert("La Base Imponible no puede ser Igual a 0");
			eval("f.txtbaseimp"+fila+".value='0,00'"); 
			eval("f.chkcargos"+fila+".checked=false;");
		}
	}
	else
	{
		eval("f.txtmonimp"+fila+".value='0,00'"); 
	}
}

function ue_search()
{
	f=document.formulario;
	// Cargamos las variables para pasarlas al AJAX
	orden      = f.orden.value;
	campoorden = f.campoorden.value;
	tipo       = f.tipo.value;
	procede=f.procede.value;
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('resultados');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde est?n los m?todos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_sep_c_catalogo_ajax.php",true);
	ajax.onreadystatechange=function(){
		if(ajax.readyState==1)
		{
			divgrid.innerHTML = "<img src='imagenes/loading.gif' width='350' height='200'>";//<-- aqui iria la precarga en AJAX 
		}
		else
		{
			if(ajax.readyState==4)
			{
				if(ajax.status==200)
				{//mostramos los datos dentro del contenedor
					divgrid.innerHTML = ajax.responseText
				}
				else
				{
					if(ajax.status==404)
					{
						divgrid.innerHTML = "La p?gina no existe";
					}
					else
					{//mostramos el posible error     
						divgrid.innerHTML = "Error:".ajax.status;
					}
				}
				
			}
		}
	}	
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	// Enviar todos los campos a la pagina para que haga el procesamiento
	ajax.send("catalogo=OTROSCREDITOS&tipo="+tipo+"&orden="+orden+"&campoorden="+campoorden+"&procededoc="+procede+"&parcial=0");
}

function ue_aceptar()
{
	f=document.formulario;
	valido=true;
	totrow=ue_calcular_total_fila_local("txtcodcar");
	f.totrow.value=totrow;
	ls_ajuste=f.ajustar.value;
	//---------------------------------------------------------------------------------
	// Cargar las Cuentas del opener y el seleccionado
	//---------------------------------------------------------------------------------
	parametros="";
	totrowconceptos=ue_calcular_total_fila_opener("txtcodcon");

	tipo=opener.document.formulario.cmbcodtipsol.value;
	codestpro1=opener.document.formulario.txtcodestpro1.value;
	codestpro2=opener.document.formulario.txtcodestpro2.value;
	codestpro3=opener.document.formulario.txtcodestpro3.value;
	codestpro4=opener.document.formulario.txtcodestpro4.value;
	codestpro5=opener.document.formulario.txtcodestpro5.value;	   
	ls_coduniadm    = opener.document.formulario.txtcoduniadm.value;
	ls_estcla =	opener.document.formulario.txtestcla.value;
	//opener.document.formulario.totrowspg.value=totrowspg;
	ls_codfuefin=opener.document.formulario.txtcodfuefin.value;
	programatica=codestpro1+codestpro2+codestpro3+codestpro4+codestpro5;
	//---------------------------------------------------------------------------------
	// recorremos grid de las cuentas presupuestarias
	//---------------------------------------------------------------------------------
	codcon="00000";
	dencon="CONCEPTO POR DEFECTO";
	cancon="0,00";
	precon="0,00";
	subtotcon="0,00";
	carcon="0,00";
	totcon="0,00";
	spgcuenta="403180100";
	ls_codgas	 = "";
	ls_codspg	 = "";
	ls_estatus   = "";
	li_i=0;
/*	totrowconceptos=1;
	for(j=1;(j<2)&&(valido);j++)
	{
		codcon="00000";
		dencon="CONCEPTO POR DEFECTO";
		cancon="0,00";
		precon="0,00";
		subtotcon="0,00";
		carcon="0,00";
		totcon="0,00";
		spgcuenta="403180100";
		ls_codgas	 = "";
		ls_codspg	 = "";
		ls_estatus   = "";

		parametros=parametros+"&txtcodcon"+j+"="+codcon+"&txtdencon"+j+"="+dencon+""+
				   "&txtcancon"+j+"="+cancon+"&txtprecon"+j+"="+precon+""+
				   "&txtsubtotcon"+j+"="+subtotcon+"&txtcarcon"+j+"="+carcon+""+
				   "&txttotcon"+j+"="+totcon+"&txtcodgas"+j+"="+ls_codgas+"&txtcodspg"+j+"="+ls_codspg+""+
				   "&txtspgcuenta"+j+"="+spgcuenta+"&txtstatus"+j+"="+ls_estatus;
	}
	totalconceptos=eval(totrowconceptos+"+1");
	parametros=parametros+"&txtcodcon"+totrowconceptos+"="+as_codcon+"&txtdencon"+totrowconceptos+"="+as_dencon+""+
			   "&txtcancon"+totrowconceptos+"=0,00"+"&txtprecon"+totrowconceptos+"="+ai_precio+"&txtsubtotcon"+totrowconceptos+"=0,00"+
			   "&txtcarcon"+totrowconceptos+"=0,00&txttotcon"+totrowconceptos+"=0,00"+"&txtspgcuenta"+totrowconceptos+"="+as_spg_cuenta+
			   "&txtcodgas"+totrowconceptos+"="+programatica+"&txtcodspg"+totrowconceptos+"="+as_spg_cuenta+
			   "&totalconceptos="+totalconceptos+"&txtstatus"+totrowconceptos+"="+ls_estcla+"";

*/
	for(j=1;(j<totrowconceptos)&&(valido);j++)
	{
		codcon=eval("opener.document.formulario.txtcodcon"+j+".value");
		dencon=eval("opener.document.formulario.txtdencon"+j+".value");
		cancon=eval("opener.document.formulario.txtcancon"+j+".value");
		precon=eval("opener.document.formulario.txtprecon"+j+".value");
		subtotcon=eval("opener.document.formulario.txtsubtotcon"+j+".value");
		carcon=eval("opener.document.formulario.txtcarcon"+j+".value");
		totcon=eval("opener.document.formulario.txttotcon"+j+".value");
		spgcuenta=eval("opener.document.formulario.txtspgcuenta"+j+".value");
		ls_codgas	 = eval("opener.document.formulario.txtcodgas"+j+".value");
		ls_codspg	 = eval("opener.document.formulario.txtcodspg"+j+".value");
		ls_estatus   = eval("opener.document.formulario.txtstatus"+j+".value");

		parametros=parametros+"&txtcodcon"+j+"="+codcon+"&txtdencon"+j+"="+dencon+""+
				   "&txtcancon"+j+"="+cancon+"&txtprecon"+j+"="+precon+""+
				   "&txtsubtotcon"+j+"="+subtotcon+"&txtcarcon"+j+"="+carcon+""+
				   "&txttotcon"+j+"="+totcon+"&txtcodgas"+j+"="+ls_codgas+"&txtcodspg"+j+"="+ls_codspg+""+
				   "&txtspgcuenta"+j+"="+spgcuenta+"&txtstatus"+j+"="+ls_estatus;
	}
	totalconceptos=eval(totrowconceptos+"+1");
	parametros=parametros+"&txtcodcon"+totrowconceptos+"="+codcon+"&txtdencon"+totrowconceptos+"="+dencon+""+
			   "&txtcancon"+totrowconceptos+"=0,00"+"&txtprecon"+totrowconceptos+"="+precon+"&txtsubtotcon"+totrowconceptos+"=0,00"+
			   "&txtcarcon"+totrowconceptos+"=0,00&txttotcon"+totrowconceptos+"=0,00"+"&txtspgcuenta"+totrowconceptos+"="+spgcuenta+
			   "&txtcodgas"+totrowconceptos+"="+programatica+"&txtcodspg"+totrowconceptos+"="+spgcuenta+
			   "&totalconceptos="+totalconceptos+"&txtstatus"+totrowconceptos+"="+ls_estcla+"";
	li_i=0;
	li_cargos=0;
	for(j=1;(j<=totrow);j++)
	{
		marcado=eval("f.chkcargos"+j+".checked");
		monto=ue_formato_calculo(eval("f.txtmonimp"+j+".value"));
		if((marcado==true)&&(parseFloat(monto)>0))
		{
			li_i=li_i+1;
			codservic="00000";
			codcar=eval("f.txtcodcar"+j+".value");
			dencar=eval("f.txtdencar"+j+".value");
			bascar=eval("f.txtbaseimp"+j+".value");
			moncar=eval("f.txtmonimp"+j+".value");
			codestpro=eval("f.codestpro"+j+".value");
			estcla=eval("f.estcla"+j+".value");
			cuentacargo=eval("f.spgcuenta"+j+".value");
			sccuenta=eval("f.sccuenta"+j+".value");
			formulacargo=eval("f.formula"+j+".value");
			porcar=eval("f.porcar"+j+".value");
			procededoc=eval("f.procededoc"+j+".value");
			subcargo=eval(ue_formato_calculo(bascar)+"+"+ue_formato_calculo(moncar));
			//li_cargos=eval(li_cargos+"+"+ue_formato_calculo(monimp));
			parametros=parametros+"&txtcodservic"+li_i+"="+codservic+"&txtcodcar"+li_i+"="+codcar+
					   "&txtdencar"+li_i+"="+dencar+"&txtbascar"+li_i+"="+bascar+
					   "&txtmoncar"+li_i+"="+moncar+"&txtsubcargo"+li_i+"="+subcargo+
					   "&cuentacargo"+li_i+"="+cuentacargo+"&formulacargo"+li_i+"="+formulacargo+
					   "&txtcodgascre"+li_i+"="+programatica+"&txtcodspgcre"+li_i+"="+programatica+"&txtstatuscre"+li_i+"="+ls_estcla;
		}
	}
	parametros=parametros+"&totrowcargos="+li_i+"";
	parametros=parametros+"&tipo="+tipo+"&cargarcargos=0";

	if((parametros!="")&&(valido))
	{
		// Div donde se van a cargar los resultados
		divgrid = opener.document.getElementById("bienesservicios");
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde est?n los m?todos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_sep_c_solicitud_ajax.php",true);
		ajax.onreadystatechange=function(){
			if(ajax.readyState==1)
			{
				//divgrid.innerHTML = "";//<-- aqui iria la precarga en AJAX 
			}
			else
			{
				if(ajax.readyState==4)
				{
					if(ajax.status==200)
					{//mostramos los datos dentro del contenedor
						divgrid.innerHTML = ajax.responseText
					}
					else
					{
						if(ajax.status==404)
						{
							divgrid.innerHTML = "La p?gina no existe";
						}
						else
						{//mostramos el posible error     
							divgrid.innerHTML = "Error:".ajax.status;
						}
					}
					
				}
			}
		}	
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("proceso=AGREGARCONCEPTOS"+parametros);
		opener.document.formulario.totrowconceptos.value=totalconceptos;
	}
}

function uf_aceptar_creditos(li_totrows)
{
  f         = document.formulario;
  fop       = opener.document.formulario;
  li_filsel = 0;
  li_filope = fop.hidfilsel.value;
  lb_valido = false;
  for (i=1;i<=li_totrows;i++)
      {
	    if (eval("f.radiocargos"+i+".checked==true"))
		   {
		     lb_valido=true;
			 li_filsel=i;
			 break;
		   }
	  }
  if (lb_valido)
     {
       ld_porcar = eval("f.porcar"+li_filsel+".value");
	   ls_forcar = eval("f.formula"+li_filsel+".value");
	   ld_basimp = eval("fop.txtbasimp"+li_filope+".value");
	   ld_basimp = ue_formato_calculo(ld_basimp);
	   ld_totimp = eval(ls_forcar.replace('$LD_MONTO',ld_basimp));
	   ld_totimp = redondear(ld_totimp,3);
	   ld_totimp = uf_convertir(ld_totimp);
	   eval("fop.txtporimp"+li_filope+".value='"+ld_porcar+"'");
	   eval("fop.txttotimp"+li_filope+".value='"+ld_totimp+"'");
	   close();
	 }
}
</script>
</html>