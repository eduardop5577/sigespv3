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

function objetoAjax()
{
	var xmlhttp=false;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
  		}
	}

	if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}




var url= "../../php/sigesp_srh_a_tiporequerimiento.php";
var metodo="POST";
var img="<img src=\"../../../public/imagenes/progress.gif\"> ";
var ajax=objetoAjax();





function ue_validaexiste()
{
  
	var divResultado= $('existe');
	var paran="txtcodtipreq="+$F('txtcodtipreq');
	if (($F('txtcodtipreq')!="") && ($F('hidstatus')!='C'))
	{
	   ajax.open(metodo,url+"?valor=existe",true);
	   ajax.onreadystatechange=function() 
	   {
		  if (ajax.readyState==4)
		  {
				if(ajax.status==200)
				{
					 divResultado.innerHTML = ajax.responseText
					 if(divResultado.innerHTML=='')
					 {
					 }
					 else
					 {
						  Field.clear('txtcodtipreq');
						  Field.activate('txtcodtipreq');
						  
						  alert(divResultado.innerHTML);
						 
					 }
				}
				else
				{
					 alert('ERROR '+ajax.status);
				}
		  }
	   }
	
      ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	  ajax.send(paran);
	}
}//fin ue_validaexiste()



function ue_cancelar()
{
  document.form1.reset();
  document.form1.hidstatus.value="";
   scrollTo(0,0);
}

function ue_nuevo()
{
  function onNuevo(respuesta)
  {
	ue_cancelar();
	$('txtcodtipreq').value  = trim(respuesta.responseText);
	$('txtdentipreq').focus();
  }	

  params = "operacion=ue_nuevo";
  new Ajax.Request(url,{method:'get',parameters:params,onComplete:onNuevo});
}




function ue_validavacio()
{
  lb_valido=true;
  f=document.form1;
  
if(f.txtcodtipreq.value=="")
  {
		alert('Falta Codigo del Tipo de Requerimiento');
		lb_valido=false;
   }
   else if(f.txtdentipreq.value=="")
   {
	   alert('Falta la Denominacion del Tipo de Requerimiento');
	   lb_valido=false;
   }
 
   
   return lb_valido;
 

}











function ue_guardar_registro()
{
	     //donde se mostrar?? lo resultados
			  divResultado = document.getElementById('mostrar');
			
			  divResultado.innerHTML= img;
			
			  
			  dentipreq=document.form1.txtdentipreq.value;
			  codtipreq=document.form1.txtcodtipreq.value;

			 
			  
			  ajax=objetoAjax();
			  
			  
			  ajax.open(metodo,url+"?valor=guardar",true);
			  ajax.onreadystatechange=function() 
			  {
				  if (ajax.readyState==4)
				  {
				  //mostrar resultados en esta capa
				  divResultado.innerHTML = ajax.responseText;
				  
				  
				   if(divResultado.innerHTML)
					{
					   
					   
					   if(ajax.status==200)
					   {
					   	alert(divResultado.innerHTML);
					   }
					   else
					   {
						alert(ajax.statusText);   
						}
					   //divResultado.innerHTML ='';
					}
					
				
				  }
			
				 ue_nuevo();
			  }
   ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  //enviando los valores
   ajax.send("txtdentipreq="+dentipreq+"&txtcodtipreq="+codtipreq);
   

}



function ue_guardar()
{
	lb_valido=ue_validavacio();
	
	if(lb_valido)
	{
		
		ue_guardar_registro();
				
    }//lb_valido
}//ue_guardar







function ue_eliminar()
{
		
		
		if(document.form1.hidstatus.value=="C")
		{
			
			
			if (confirm("??Esta seguro de eliminar este registro?"))
			{
		
		
			  //donde se mostrar?? lo resultados
  
			  divResultado = document.getElementById('mostrar');
			    divResultado.innerHTML= img;
			  
			  codtipreq=document.form1.txtcodtipreq.value;
			//  valini=document.form1.txtvalini.value;
			  
			 
			  //instanciamos el objetoAjax
			  ajax=objetoAjax();
			  //uso del medoto POST
					  
			  ajax.open(metodo,url+"?valor=eliminar",true);
			  ajax.onreadystatechange=function() 
			  {
				  if (ajax.readyState==4)
				  {
				  //mostrar resultados en esta capa
				  divResultado.innerHTML = ajax.responseText;
				  
				  
				   if(divResultado.innerHTML)
					{
					   
					   
					   if(ajax.status==200)
					   {
					   	alert(divResultado.innerHTML);
					   }
					   else
					   {
						alert(ajax.statusText);   
						}
					} 
									  //llamar a funcion para limpiar los inputs
					
				  }
				
				 ue_nuevo();
			  }
   ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  //enviando los valores
   ajax.send("txtcodtipreq="+codtipreq);

  			 }
		}
		else
	   {
		
		alert('Debe elegir un Tipo de Requerimiento del Catalogo');
		
		}
}






  
  
  
