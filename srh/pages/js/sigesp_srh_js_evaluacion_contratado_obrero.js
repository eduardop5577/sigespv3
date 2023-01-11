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
var params = 'operacion';
var img="<img src=\"../../../public/imagenes/progress.gif\"> ";

function ue_chequear_codigo()
{
	if ((ue_valida_null($('txtcodper'))) && ($('hidguardar').value!='modificar'))
	{
		function onChequearcodpersonal(respuesta)
		{	  
			if (trim(respuesta.responseText) != "")
			{
				alert(respuesta.responseText);
				Field.clear('txtcodper');//INICIALIZAR
				Field.clear('txtnomper');//INICIALIZAR
				Field.clear('txtfecha');//INICIALIZAR
				Field.activate('txtcodper');//FOCUS
			}
		}
		params = "operacion=ue_chequear_codigo&codper="+$F('txtcodper')+"&feceval="+$F('txtfecha');
		new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onChequearcodpersonal});	
	}
}


function ue_guardar()
{
	lb_valido=true;
	var la_objetos=new Array ("txtcodper", "txtfecha", "txtcarpos");
	var la_mensajes=new Array ("el código del personal", "la fecha de la evaluación", "el cargo para el cual se evalua");
	lb_valido = valida_datos_llenos(la_objetos,la_mensajes);
	
	if (lb_valido)
	{
		if ($('totalfilas').value==1)
		{
			lb_valido=false;
			alert ('No hay aspectos a evaluar');
		}
	}
	if (lb_valido)
	{  
		divResultado = document.getElementById('mostrar');
		divResultado.innerHTML= img;
		function onGuardar(respuesta)
		{
			alert(respuesta.responseText);   
			ue_cancelar();
			divResultado = document.getElementById('mostrar');
			divResultado.innerHTML="";
		}
		//Arreglo con el detalle
		var evaluacion = new Array();
		var filas = $('grid').getElementsByTagName("tr");
		g=2;
		total=0;
		for (f=1; f<(filas.length - 1); f++)
		{
			//var IdFila   = filas[g].getAttribute("id");
			var columnas = filas[g].getElementsByTagName("input");
			//alert(IdFila);
			var puntaje = filas[g].getElementsByTagName("select");
			puntos = puntaje[0].value;
			var eva = 
			{
				"codper"  : $F('txtcodper'),
				"feceval"  : $F('txtfecha'),
				"codasp"  : columnas[0].value,
				"puntaje" : puntos
			}
			g++;
			evaluacion[f-1] = eva;
		}
		var evaluacion_p = 
		{
			"codper"     : $F('txtcodper'),
			"feceval"    : $F('txtfecha'),
			"carpos"     : $F('txtcarpos'),
			"obseval"	 : $F('txtobseval'),
			"receval"	 : $F('cmbreceval'),
			"evaluacion" : evaluacion
		};
	var objeto = JSON.stringify(evaluacion_p);
	params = "operacion=ue_guardar&objeto="+objeto+"&insmod="+$F('hidguardar');
	new Ajax.Request(url,{method:'post',parameters:params,onComplete:onGuardar});
	};
}

function ue_eliminar()
{
	lb_valido=true;
	var la_objetos=new Array ("txtcodper", "txtfecha");
	var la_mensajes=new Array ("el código de personal. Seleccione una Evaluación Psicologica del Catalago","la fecha. Seleccione una Evaluación Psicologica del Catalago");
	lb_valido = valida_datos_llenos(la_objetos,la_mensajes);
	if(lb_valido)
	{
		if (confirm("¿ Esta seguro de Eliminar este Registro ?"))
		{
			divResultado = document.getElementById('mostrar');
			divResultado.innerHTML= img;
			function onGuardar(respuesta)
			{
				alert(respuesta.responseText);   
				ue_cancelar();
				divResultado = document.getElementById('mostrar');
				divResultado.innerHTML="";
			}
			params = "operacion=ue_eliminar&codper="+$F('txtcodper')+"&feceval="+$F('txtfecha');
			new Ajax.Request(url,{method:metodo,parameters:params,onComplete:onGuardar});
		}
		else
		{
			ue_cancelar();
			alert("Eliminación Cancelada !!!");	  
		}
	}
}

function ue_cancelar()
{
	ue_nuevo();
	scrollTo(0,0);
	$('txtcodper').focus();
}

function ue_nuevo()
{
	f=document.form1;
	f.operacion.value="NUEVO";
	f.existe.value="FALSE";		
	f.action="sigesp_srh_p_evaluacion_contratado_obrero.php";
	f.submit(); 
}

function catalogo_persona()
{
	f= document.form1;
	if (form1.hidguardar.value == "modificar")
	{
		alert("El aspirante no se puede cambiar");	
	}
	else
	{
		pagina="../catalogos/sigesp_srh_cat_solicitud_empleo.php?valor_cat=0";
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no,dependent=yes");
	}
}
 
function catalogo_concurso()
{
	if (form1.hidguardar.value == "modificar")
	{
		alert("El Concurso no se puede cambiar");	
	}
	else
	{
		pagina="../catalogos/sigesp_srh_cat_concurso.php?valor_cat=0";
		window.open(pagina,"catalogo2","menubar=no,toolbar=no,scrollbars=yes,width=520,height=400,resizable=yes,location=no,dependent=yes");
	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if(li_leer==1)
	{
		window.open("../catalogos/sigesp_srh_cat_evaluacion_contratado_obrero.php?valor_cat=1","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=518,height=400,left=50,top=50,location=no,resizable=yes");
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_cerrar()
{
	window.location.href="sigespwindow_blank.php";
}
 //--------------------------------------------------------
//	Función que verifica que la fecha 2 sea mayor que la fecha 1
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


//FUNCIONES PARA EL CALENDARIO

// Esta es la funcion que detecta cuando el usuario hace click en el calendario, necesaria
function selected(cal, date) {
  cal.sel.value = date; // just update the date in the input field.
                           
  if (cal.dateClicked )
      cal.callCloseHandler();
}


function closeHandler(cal) {
  cal.hide();                        // hide the calendar

  _dynarch_popupCalendar = null;
}


function showCalendar(id, format, showsTime, showsOtherMonths) {
  var el = document.getElementById(id);
  if (_dynarch_popupCalendar != null) {
    // we already have some calendar created
    _dynarch_popupCalendar.hide();                 // so we hide it first.
  } else {
    // first-time call, create the calendar.

    var cal = new Calendar(1, null, selected, closeHandler);
    if (typeof showsTime == "string") {
      cal.showsTime = true;
      cal.time24 = (showsTime == "24");
    }
    if (showsOtherMonths) {
      cal.showsOtherMonths = true;
    }
    _dynarch_popupCalendar = cal;                  // remember it in the global var
    cal.setRange(1900, 2070);        // min/max year allowed.
    cal.create();
  }
  _dynarch_popupCalendar.setDateFormat(format);    // set the specified date format
  _dynarch_popupCalendar.parseDate(el.value);      // try to parse the text in field
  _dynarch_popupCalendar.sel = el;                 // inform it what input field we use
 _dynarch_popupCalendar.showAtElement(el, "T");        // show the calendar

  return false;
}   
