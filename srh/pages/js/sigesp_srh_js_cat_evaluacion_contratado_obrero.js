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

var url= "../../php/sigesp_srh_a_evaluacion_contratado_obrero.php";
var metodo='get';
var img="<img src=\"../../../public/imagenes/progress.gif\"> ";


var loadDataURL = "../../php/sigesp_srh_a_evaluacion_contratado_obrero.php?valor=createXML";
var actionURL = "../../php/sigesp_srh_a_evaluacion_contratado_obrero.php";
var mygrid;
var timeoutHandler;//update will be sent automatically to server if row editting was stoped;
var rowUpdater;//async. Calls doUpdateRow function when got data from server
var rowEraser;//async. Calls doDeleteRow function when got confirmation about row deletion
var authorsLoader;//sync. Loads list of available authors from server to populate dropdown (co)
var mandFields = [0,1,1,0,0];
		
		
	//initialise (from xml) and populate (from xml) grid
function doOnLoad()
{
	
	mygrid = new dhtmlXGridObject('gridbox');
	mygrid.setImagePath("../../../public/imagenes/"); 
	//set columns properties
	mygrid.setHeader("Fecha,Nombre y Apellido,Cargo");
	mygrid.setInitWidths("130,254,120");
	mygrid.setColAlign("center,center,center");
	mygrid.setColTypes("link,ro,ro");
	mygrid.setColSorting("str,str,str");//nuevo  ordenacion
	mygrid.setColumnColor("#FFFFFF,#FFFFFF,#FFFFFF");
	//mygrid.loadXML(loadDataURL);
	mygrid.setSkin("xp");
	mygrid.init();


}
		
		
function terminar_buscar ()
{ 
   divResultado = document.getElementById('mostrar');
   divResultado.innerHTML= '';   
}

function Buscar()
{	
	codper=document.form1.txtcodper.value;
	fechades=document.form1.txtfechades.value;
	fechahas=document.form1.txtfechahas.value;
	valfec= ue_comparar_fechas(fechades,fechahas);
	if (!valfec)
	{
		alert ('Rango de Fecha Invalido.');	 
	}
	else
	{
		mygrid.clearAll();
		divResultado = document.getElementById('mostrar');
		divResultado.innerHTML= img; 
		mygrid.loadXML("../../php/sigesp_srh_a_evaluacion_contratado_obrero.php?valor=buscar"+"&txtfechades="+fechades+"&txtfechahas="+fechahas+"&txtcodper="+codper);
		setTimeout (terminar_buscar,650);
	}
}

function aceptar (ls_codper, ls_codperdestino, ls_fecha, ls_fechadestino, ls_nomper, ls_nomperdestino, 
				  ls_carpos, ls_carposdestino,ls_obseval, ls_obsevaldestino, li_receval, ls_recevaldestino)
{
	obj1=eval("opener.document.form1."+ls_codperdestino+"");
	obj1.value=ls_codper;
	obj2=eval("opener.document.form1."+ls_fechadestino+"");
	obj2.value=ls_fecha;
	obj4=eval("opener.document.form1."+ls_nomperdestino+"");
	obj4.value=ls_nomper;
	obj7=eval("opener.document.form1."+ls_carposdestino+"");
	obj7.value=ls_carpos;
	obj8=eval("opener.document.form1."+ls_obsevaldestino+"");
	obj8.value=ls_obseval;
	obj9=eval("opener.document.form1."+ls_recevaldestino+"");
	obj9.value=li_receval;
	opener.document.form1.hidguardar.value = "modificar";
	opener.document.form1.txtcodper.readOnly=true;
	opener.document.form1.operacion.value="BUSCARDETALLE";
	opener.document.form1.action="../pantallas/sigesp_srh_p_evaluacion_contratado_obrero.php";
	opener.document.form1.existe.value="TRUE";			
	opener.document.form1.submit();	
	close ();
}

function Limpiar_busqueda () 
{
	document.form1.txtcodper.value="";
	document.form1.txtfechades.value=document.form1.txtfechades2.value;
	document.form1.txtfechahas.value=document.form1.txtfechahas2.value;
}

function nextPAge(val)
{
	grid.clearAll(); //clear existing data
	grid.loadXML("some_url.php?page="+val);
}
		
//--------------------------------------------------------
//	Funci?n que verifica que la fecha 2 sea mayor que la fecha 1
//----------------------------------------------------------
function ue_comparar_fechas(fecha1,fecha2)
{
	vali=false;
	dia1 = fecha1.substr(0,2);
	mes1 = fecha1.substr(3,2);
	ano1 = fecha1.substr(6,4);
	dia2 = fecha2.substr(0,2);
	mes2 = fecha2.substr(3,2);
	ano2 = fecha2.substr(6,4);
	if (ano1 < ano2)
	{
		vali = true; 
	}
    else 
	{ 
    	if (ano1 == ano2)
	 	{ 
      		if (mes1 < mes2)
	  		{
	   			vali = true; 
	  		}
      		else 
	  		{ 
       			if (mes1 == mes2)
	   			{
 					if (dia1 <= dia2)
					{
		 				vali = true; 
					}
	   			}
      		} 
     	} 	
	}
	
	return vali;
	
}