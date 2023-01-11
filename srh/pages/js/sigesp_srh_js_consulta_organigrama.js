/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

function Consultar()
{
     if  ( ($('cmbnivorg').value=="") ) 		
	 {
		 alert("Debe seleccionar un nivel de organigrama.");	
		 
	 }
	 else 
	 {
		  f=document.form1;
		  f.operacion.value="CONSULTAR";
		  f.action="../pantallas/sigesp_srh_p_consulta_organigrama.php";		 		
		  f.submit();	
	 }
}
	



function Limpiar_Datos()
{
  $('cmbnivorg').value="";
  $('txtcodorg').value="";
  $('txtdesorg').value="";
}



function ue_buscarunidad()
{

	nivorg=$('cmbnivorg').value;
	if (nivorg!="")
	{
		
		pagina="../catalogos/sigesp_srh_cat_organigrama.php?valor_cat=0&tipo=5&nivel="+nivorg;
		window.open(pagina,"catalogo","menubar=no, toolbar=no, scrollbars=yes,width=530, height=400,resizable=yes, location=no,				dependent=yes");
	}
	else
	{
		alert ('Debe Seleccionar un Nivel');	
	}
}




function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}





