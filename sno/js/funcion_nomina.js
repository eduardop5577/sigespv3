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

//--------------------------------------------------------
//	Función que valida que no se incluyan comillas simples 
//	en los textos ya que dañana la consulta SQL
//--------------------------------------------------------
function ue_validarAlphaNumeric(valor)
{
	var letras="ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghyjklmnñopqrstuvwxyz-0123456789";
	
	texto = valor.value;
	longitud = texto.length;
	textocompleto = "";
	texto = texto.toUpperCase();
    for(i=0; i<longitud; i++)
	{
      if (letras.indexOf(texto.charAt(i),0)!=-1)
	  {
         textocompleto += texto.charAt(i);
      }
	}
   	valor.value=textocompleto;
}




//--------------------------------------------------------
//	Función que valida que no se incluyan comillas simples 
//	en los textos ya que dañana la consulta SQL
//--------------------------------------------------------
function ue_validarcomillas(valor)
{
	val = valor.value;
	longitud = val.length;
	texto = "";
	textocompleto = "";
	for(r=0;r<=longitud;r++)
	{
		texto = valor.value.substring(r,r+1);
		if((texto != "'")&&(texto != '"')&&(texto != "\\"))
		{
			textocompleto += texto;
		}	
	}
	valor.value=textocompleto;
}

//--------------------------------------------------------
//	Función que valida que solo se incluyan números en los textos
//--------------------------------------------------------
function ue_validarnumero(valor)
{
	val = valor.value;
	longitud = val.length;
	texto = "";
	textocompleto = "";
	for(r=0;r<=longitud;r++)
	{
		texto = valor.value.substring(r,r+1);
		if((texto=="0")||(texto=="1")||(texto=="2")||(texto=="3")||(texto=="4")||(texto=="5")||(texto=="6")||(texto=="7")||(texto=="8")||(texto=="9"))
		{
			textocompleto += texto;
		}	
	}
	valor.value=textocompleto;
}

//--------------------------------------------------------
//	Función que valida que el texto no esté vacio
//--------------------------------------------------------
function ue_validarvacio(valor)
{
	var texto;
	while(''+valor.charAt(0)==' ')
	{
		valor=valor.substring(1,valor.length)
	}
	texto = valor;
	return texto;
}

//--------------------------------------------------------
//	Función que rellena un campo con ceros a la izquierda
//--------------------------------------------------------
function ue_rellenarcampo(valor,maxlon)
{
	var total;
	var auxiliar;
	var longitud;
	var index;
	
	total=0;
    auxiliar=valor.value;
	longitud=valor.value.length;
	total=maxlon-longitud;
	if (total < maxlon)
	{
		for (index=0;index<total;index++)
		{
		   auxiliar="0"+auxiliar;      
		}
		valor.value = auxiliar;
	}
}

//--------------------------------------------------------
//	Función que formatea un número
//--------------------------------------------------------
function ue_formatonumero(fld, milSep, decSep, e)
{ 
	var sep = 0; 
    var key = ''; 
    var i = j = 0; 
    var len = len2 = 0; 
    var strCheck = '0123456789'; 
    var aux = aux2 = ''; 
    var whichCode = (window.Event) ? e.which : e.keyCode; 
	
	if (fld.readOnly==true) return false; 
	if (whichCode == 13) return true; // Enter 
	if (whichCode == 8) return true; // Return
	if (whichCode == 127) return true; // Suprimir
    key = String.fromCharCode(whichCode); // Get key value from key code 
    if (strCheck.indexOf(key) == -1) return false; // Not a valid key 
    len = fld.value.length; 
    for(i = 0; i < len; i++) 
    	if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break; 
    aux = ''; 
    for(; i < len; i++) 
    	if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i); 
    aux += key; 
    len = aux.length; 
    if (len == 0) fld.value = ''; 
    if (len == 1) fld.value = '0'+ decSep + '0' + aux; 
    if (len == 2) fld.value = '0'+ decSep + aux; 
    if (len > 2) { 
     aux2 = ''; 
     for (j = 0, i = len - 3; i >= 0; i--) { 
      if (j == 3) { 
       aux2 += milSep; 
       j = 0; 
      } 
      aux2 += aux.charAt(i); 
      j++; 
     } 
     fld.value = ''; 
     len2 = aux2.length; 
     for (i = len2 - 1; i >= 0; i--) 
     	fld.value += aux2.charAt(i); 
     fld.value += decSep + aux.substr(len - 2, len); 
    } 
    return false; 
}

//--------------------------------------------------------
//	Función que verifica que la fecha  no tenga letras
//--------------------------------------------------------
function ue_validarfecha(valor)
{
	var texto;
	if ((valor=="dd/mm/aaaa")||(valor=="")||(valor=="01/01/1900"))
	{
		texto="1900-01-01";
	}
	else
	{
		texto = valor;
	}
	return texto;
}

//--------------------------------------------------------
//	Función que valida que solo se incluyan números(1234567890),guiones(-) y Espacios en blanco
//--------------------------------------------------------
function ue_validartelefono(valor)
{
	val = valor.value;
	longitud = val.length;
	texto = "";
	textocompleto = "";
	for(r=0;r<=longitud;r++)
	{
		texto = valor.value.substring(r,r+1);
		if((texto=="0")||(texto=="1")||(texto=="2")||(texto=="3")||(texto=="4")||(texto=="5")||(texto=="6")||(texto=="7")||
		   (texto=="8")||(texto=="9")||(texto=="-")||(texto==" ")||(texto=="(")||(texto==")"))
		{
			textocompleto += texto;
		}	
	}
	valor.value=textocompleto;
}

//--------------------------------------------------------
//	Función que le da formato a la fecha
//--------------------------------------------------------
function ue_formatofecha(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++)
		{
			val2 += val[r]	
		}
		if(nums)
		{
			for(z=0;z<val2.length;z++)
			{
				if(isNaN(val2.charAt(z)))
				{
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++)
		{
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++)
		{
			if(q ==0)
			{
				val = val3[q]
			}
			else
			{
				if(val3[q] != "")
				{
					val += sep + val3[q]
				}
			}
		}
		d.value = val
		d.valant = val
	}
}
//--------------------------------------------------------
//	Función que valida el correo electrónico
//--------------------------------------------------------
function ue_validarcorreo(obj)
{
	//expresion regular
    var filtro=/^[A-Za-z][A-Za-z0-9_.]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
	if (obj.length==0)
	{
		return true;
	}
	else
	{
		if(filtro.test(obj)==false)
		{
			alert("Email No Válido.");
			return false;
		}
		else
		{
			return true;
		}
	}
}

//--------------------------------------------------------
//	Función que verifica que la fecha 2 sea mayor que la fecha 1
//--------------------------------------------------------
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

function redondear(cantidad, decimales) 
{
	var cantidad = parseFloat(cantidad);
	var decimales = parseFloat(decimales);
	decimales = (!decimales ? 2 : decimales);
return Math.round(cantidad * Math.pow(10, decimales)) / Math.pow(10, decimales);
}

//--------------------------------------------------------
//	Función que convierte los montos para poder hacer calculos
//--------------------------------------------------------
function ue_formato_calculo(monto)
{
	while(monto.indexOf('.')>0)
	{//Elimino todos los puntos o separadores de miles
		monto=monto.replace(".","");
	}
	monto=monto.replace(",",".");	
	return monto;
}


//--------------------------------------------------------
//	Función que le da formato a la fecha
//--------------------------------------------------------
function ue_formatohora(d,sep,pat,nums)
{
	if(d.valant != d.value)
	{
		val = d.value
		largo = val.length
		val = val.split(sep)
		val2 = ''
		for(r=0;r<val.length;r++)
		{
			val2 += val[r]	
		}
		if(nums)
		{
			for(z=0;z<val2.length;z++)
			{
				if(isNaN(val2.charAt(z)))
				{
					letra = new RegExp(val2.charAt(z),"g")
					val2 = val2.replace(letra,"")
				}
			}
		}
		val = ''
		val3 = new Array()
		for(s=0; s<pat.length; s++)
		{
			val3[s] = val2.substring(0,pat[s])
			val2 = val2.substr(pat[s])
		}
		for(q=0;q<val3.length; q++)
		{
			if(q ==0)
			{
				val = val3[q]
			}
			else
			{
				if(val3[q] != "")
				{
					val += sep + val3[q]
				}
			}
		}
		d.value = val
		d.valant = val
	}
}

/*A esta funcion se le pasa el parametro en formato fecha dd/mm/yyyy o dd-mm-yyyy ambos son aceptados*/

function semanadelano($fecha)
{

	$const	=	[2,1,7,6,5,4,3]; // Constantes para el calculo del primer dia de la primera semana del año
		
	if ($fecha.match(/\//)){ $fecha	=	$fecha.replace(/\//g,"-",$fecha); }; //Permite que la fecha pasada a la funcion este separada por "/"
		
	$fecha	=	$fecha.split("-"); //divide la fecha en trozos
	$dia	=	eval($fecha[0]);
	$mes	=	eval($fecha[1]);
		
	if ($mes!=0) { $mes--; }; //convierte el mes a formato javascript 0=enero
		
	$ano	=	eval($fecha[2]);
	$dia_pri	=	new Date($ano,0,1); 
	$dia_pri	=	$dia_pri.getDay(); //Obtiene el dia de la semana del 1 de enero
	$dia_pri	=	eval($const[$dia_pri]); //Obtiene el valor de la constante
	$tiempo0	=	new Date($ano,0,$dia_pri); //establecemos la fecha del primer dia de la semana del año
	$dia	=	($dia+$dia_pri); //Sumamos el valor de la constante a la fecha ingresada para mantener los lapsos de tiempo
	$tiempo1	=	new Date($ano,$mes,$dia); //Obtenemos la fecha con la que operaremos
	$lapso		=	($tiempo1 - $tiempo0) //Restamos ambas fechas y obtenemos una marca de tiempo
	$semanas	=	Math.floor($lapso/1000/60/60/24/7); //Dividimos para obtener el numero de semanas
	
	if ($dia_pri == 1) { $semanas++; }; // Si el 1 de enero es lunes le sumamos 1 a la semana caso contrarios nos daria 0 el calculo y nos la presentaria como semana 52
	
	if ($semanas == 0) { $semanas=52; $ano--; }; //Establecemos que si el resultado de semanas es 0 lo cambie a 52 y reste 1 al año
	
	if ($ano < 10) { $ano = '0'+$ano; }; //pura estetica por si se usan años de 2 cifras
	
	return  $semanas;//arroja el resultado, cambien esta linea a su conveniencia
};
     

