/***********************************************************************************
* @Js que contiene las funciones que van a ser usadas en todas las pantallas.
* @fecha de modificacion: 05/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

function messageSuccesfull(exito)
{
    document.getElementById("display_exito").style.display = "";
    document.getElementById("message_exito").innerHTML = exito;
    setTimeout(function(){document.getElementById("display_exito").style.display = "none"; }, 2000);
}

function messageError(error)
{
    document.getElementById("display_error").style.display = "";
    document.getElementById("message_error").innerHTML = error;
    setTimeout(function(){document.getElementById("display_error").style.display = "none"; }, 2000);
}

function validarObjetos(id,long,tipoVal)
{
	obj   = document.getElementById(id);
	arVal = tipoVal.split('|');
	for (i=0;i<arVal.length;i++)
	{
		switch(arVal[i])
		{
			case 'novacio':
				if ((obj.value=='') ||  (obj.value=='Seleccione'))
				{
					messageError('Debe llenar el campo '+obj.name);
					return '0';
				}
			break;
			
			case 'novaciodos':
				arrid=id.split('&');
			    obj1 = document.getElementById(arrid[0]);
			    obj2 =document.getElementById(arrid[1]);
			    if((obj1.value=='' || obj1.value=='Seleccione') && (obj2.value=='' || obj2.value=='Seleccione'))
			    {
					messageError('Debe llenar alg�n campo: '+obj1.name+' o '+obj2.name+' por favor');
			     	return false;
			    }
			break;
			
			case 'nombre': //solo letras
				val = obj.value;
				longitud = val.length;
				validos='ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����'+' ';
			 	for(r=0;r<longitud;r++)
			 	{
		      		ch=val.charAt(r);					  
			  		if(validos.search(ch) == -1) //busca en la cadena validos el caracter ch
			  		{
						messageError('El campo '+obj.name+ ' debe contener solo letras');
			   			return '0';
			  		}			
		     	}
			break;
			
			case 'longexacta':
				val = obj.value;
				longitud = val.length;
				validos='ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����'+' ';
				if ((longitud<long) || (longitud>long))
				{
					messageError('El campo '+obj.name+ ' no tiene la longitud correcta');
				 	return '0';
				}
				else
				{
				 	for(r=0;r<longitud;r++)
				 	{
			      		ch=val.charAt(r);					  
						if((!(ch=='('))&&(!(ch==')')))
						{
							if (validos.search(ch) == -1) //busca en la cadena validos el caracter ch
							{
								messageError('El campo '+obj.name+ ' debe contener solo letras');
								return '0';
							}			
						}
			     	}
				}				
			break;
			
			case 'telefonoFormato':
				val = obj.value;	
			 	var er_tlf = /^\d{4}-\d{7}$/; //expresi�n regular para telefono con formato ejm: 0251-5555555
				if(!er_tlf.test(val))
				{
					messageError('El campo '+obj.name+ ' es incorrecto');
				  	return '0';
            	}
			break;
			
			case 'vaciotelefono':
				val = obj.value;
				longitud = val.length;
				if ((longitud <= long) && longitud>0)
				{			
					var er_tlf = /^\d{4}-\d{7}$/; //expresi�n regular para telefono con formato ejm: 0251-5555555
					if (!er_tlf.test(val))
					{
						messageError('El campo '+obj.name+ ' es incorrecto')
						return '0';
					}
				}
			break;
						
			
			case 'vacioemail':
			   	val = obj.value;
				longitud = val.length;
				if ((longitud <= long) && longitud>0)
				{			
					var filtro=/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/; //expresi�n regular para emails
					if (!filtro.test(val)) //test compara la cadena val con la de la expresi�n regular
					{
						messageError('El campo '+obj.name+' es incorrecto');
						return '0';	
					}
				}
			break;
			
			case 'numero': //para solo numeros
				val = obj.value;
				longitud = val.length;
				if ((longitud <= long) && (longitud!=0))
				{			
					validos='0123456789';
					for(r=0;r<longitud;r++)
					{
						ch=val.charAt(r);					
						if(validos.search(ch) == -1)
						{
							messageError('El campo '+obj.name+ ' es incorrecto');
							return '0';
						}					
					}				
				}
				else if (longitud!=0)
				{					
					messageError('El campo '+obj.name+' no tiene la longitud correcta');
					return '0';	
				}
			break;			
			
			
			case 'login':
				val = obj.value;
				var er_login = /^[a-zd_]{4,20}$/i; 
				if(!er_login.test(val))
				{
       			 	messageError('El campo '+obj.name+ ' es incorrecto');
				  	return '0';
            	}			
			break;
			
			case 'telefono':
				val = obj.value;
				longitud = val.length;
				validos='0123456789'+'-';
				for(r=0;r<longitud;r++)
				{
					ch=val.charAt(r);					
					if(validos.search(ch) == -1)
					{
						messageError('El campo '+obj.name+ ' es incorrecto');
						return '0';
					}					
				}				
			break;
			
			case 'alfanumerico':  //solo numeros o letras, guiones y espacios
				val = obj.value;
				longitud = val.length;
				if (longitud <= long)
				{
					validos='ABCDEFGHIJKLMN�OPQRSTUVWXYZ�����abcdefghijklmn�opqrstuvwxyz�����0123456789'+'-'+')'+'('+'@'+'_'+' ';
					for(r=0;r<longitud;r++)
					{
						ch=val.charAt(r);
						if((!(ch=='('))&&(!(ch==')')))
						{
							if(validos.search(ch) == -1)
							{
								messageError('El campo '+obj.name+ ' no debe contener caracteres especiales');
								return '0';
							}
						}
					}
				}
				else
				{
					messageError('El campo '+obj.name+' no tiene la longitud correcta');
					return '0';	
				}
			break;

			case 'fecha':
				var valido = true;
			    var fecha= new String(obj.value);   
			    var anio= new String(fecha.substring(fecha.lastIndexOf("/")+1,fecha.length))   
			    var mes= new String(fecha.substring(fecha.indexOf("/")+1,fecha.lastIndexOf("/")))   
			    var dia= new String(fecha.substring(0,fecha.indexOf("/")))   
			    if (isNaN(anio) || anio.length<4 || parseFloat(anio)<1900)
			    {   
					valido = false;	
			    }   
			    if (isNaN(mes) || parseFloat(mes)<1 || parseFloat(mes)>12)   
			    {   
					valido = false;	
			    }   
			    if (isNaN(dia) || parseInt(dia, 10)<1 || parseInt(dia, 10)>31)   
			    {   
					valido = false;	
			    }   
			    if (mes==4 || mes==6 || mes==9 || mes==11 || mes==2) 
			    {   
			        if (dia>30) 
			        {   
						valido = false;	
			        }   
			    }   
			    if (valido == false)
			    {
					messageError('El campo '+obj.name+' el valor es inv�lido.');
					return '0';	
			    }
			    else
			    {
			    	return '1';
			    }  						
			break;
			
			case 'rellenar': // rellenar con ceros seg�n la longitud
				total=0;
			    auxiliar=obj.value;
				longitud=obj.value.length;
				total=long-longitud;
				if (total <= long)
				{
					for (index=0;index<total;index++)
					{
					   auxiliar="0"+auxiliar;      
					}
					obj.value = auxiliar;
				}
				return 	obj.value;
			break;
		}
	}
}

function validarClave(clave)
{
	var patron = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;
   	if(!patron.test(clave))
	{		
		messageError('La Contrase�a debe contener al menos una letra may�scula, al menos una letra min�scula, al menos un n�mero o car�cter especial, como m�nimo 8 caracteres y maximo 20 caracteres.');
		return false;
	}
   return true;
}
