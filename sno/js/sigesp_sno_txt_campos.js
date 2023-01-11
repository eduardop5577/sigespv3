/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

function ue_cargaritems_e(totrows)
{
	f=document.form1;
	eval('f.cmbiterelcam'+totrows+'.length=0');
	eval("f.cmbiterelcam"+totrows+".options[0]= new Option('Informativo','');");
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_constantepersonal")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Codigo Concepto','codcons');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Codigo Personal','codper');");
		eval("f.cmbiterelcam"+totrows+".options[3]= new Option('Monto Concepto','moncon');");
		eval("f.cmbiterelcam"+totrows+".options[4]= new Option('Monto Patrono','monpat');");
	}
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_personal")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Nacionalidad','nacper');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Cédula','cedper');");
	}
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_periodo")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Fecha Inicio','fecdesper');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Fecha Fin','fechasper');");
	}
}

function ue_cargaritems_i(totrows)
{
	f=document.form1;
	eval('f.cmbiterelcam'+totrows+'.length=0');
	eval("f.cmbiterelcam"+totrows+".options[0]= new Option('Informativo','');");
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_constantepersonal")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Codigo Constante','codcons');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Codigo Personal','codper');");
		eval("f.cmbiterelcam"+totrows+".options[3]= new Option('Monto Constante','moncon');");
		eval("f.cmbiterelcam"+totrows+".options[4]= new Option('Monto Patrono','monpat');");
	}
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_personal")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Nacionalidad','nacper');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Cédula','cedper');");
	}
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_periodo")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Fecha Inicio','fecdesper');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Fecha Fin','fechasper');");
	}
}

function ue_cargaritems_f(totrows)
{
	f=document.form1;
	eval('f.cmbiterelcam'+totrows+'.length=0');
	eval("f.cmbiterelcam"+totrows+".options[0]= new Option('Informativo','');");
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_constantepersonal")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Codigo Nomina','codnom');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Codigo Constante','codcons');");
		eval("f.cmbiterelcam"+totrows+".options[3]= new Option('Codigo Personal','codper');");
		eval("f.cmbiterelcam"+totrows+".options[4]= new Option('Monto Constante','moncon');");
		eval("f.cmbiterelcam"+totrows+".options[5]= new Option('Monto Patrono','monpat');");
	}
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_personal")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Nacionalidad','nacper');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Cédula','cedper');");
	}
	if(eval('f.cmbtabrelcam'+totrows+'.value')=="sno_periodo")
	{
		eval("f.cmbiterelcam"+totrows+".options[1]= new Option('Fecha Inicio','fecdesper');");
		eval("f.cmbiterelcam"+totrows+".options[2]= new Option('Fecha Fin','fechasper');");
	}
}
