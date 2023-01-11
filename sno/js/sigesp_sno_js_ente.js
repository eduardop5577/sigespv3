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

// ActionScript Remote Document
ruta = "../";
function mensajes_sigesp(titulo,texto){
	
		/*	Ext.MessageBox.show({
							   title: titulo,
							   msg: texto,
							   buttons: Ext.MessageBox.OK,
							   width: 300,
							   icon: 'sigesp_icono'
						   });
	*/
	alert(texto);
	
	}

function buscar(){
	f=document.form1;
	txtcod=f.txt_codigo.value;
	txtente=f.txt_ente.value;
	criterio = 'por_listado';
	
	datos = "catalogo=entes&txtcod="+txtcod+"&txtente="+txtente+"&criterio="+criterio;
	enviar_ajax(datos,'sigesp_sno_rajax_ente.php','resultados','POST','',ruta);

}

function aceptar(codente,ente,porcentaje)
{
	if(document.form1.tipo.value=="codentdes")
	{
		opener.document.form1.txtcodentdes.value=codente;
	
	}
	else if(document.form1.tipo.value=="codenthas")
	{
		opener.document.form1.txtcodenthas.value=codente;
		
	}
	else if(document.form1.tipo.value=="replisconc")
	{
		opener.document.form1.txtcodente.value=codente;
		opener.document.form1.operacion.value="VERIFICAR_RANGO";
		opener.document.form1.action="sigesp_sno_r_listadoconcepto.php";
		opener.document.form1.submit();
		close();
	}
	else if(document.form1.tipo.value=="repnetded")
	{
		opener.document.form1.txtcodente.value=codente;
		close();
	}
	else
	{
		opener.document.form1.txt_codente.value=codente;
		opener.document.form1.txt_ente.value=ente;
		if(opener.document.getElementById('hid_cod_ente')!=null)
		{opener.document.form1.hid_cod_ente.value=codente;}
		if(opener.document.getElementById('txt_porcentaje_ente')!=null)
		{opener.document.form1.txt_porcentaje_ente.value=porcentaje;}
	}	
	
	close();
}