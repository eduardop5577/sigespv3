/*@Javascript  el manejo de pantalla del Escritorio
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

ruta ='controlador/sss/sigesp_ctr_sss_seguridad.php';

function cargarEscritorio()
{
	var objmenu ={
		'operacion': 'escritorio'
	};
	objmenu=JSON.stringify(objmenu);
	parametros = 'objdata='+objmenu; 
	
    $.ajax({
        data:  parametros,
        url:   ruta,
        dataType: 'html',
        type:  'post',
        success:  function (response)
        {
			obj   = eval('('+response+')');
			total = obj.raiz.length;
			if(obj.raiz[0].valido==true)
			{
				cadenaprincipales = '';
				cadenaauxiliares = '';
				cadenapersonal = '';
				cadenaherramientas = '';
				cadenaadministrativos = '';
				totalprincipales = 0;
				totalauxiliares = 0;
				totalpersonal = 0;
				totalherramientas = 0;
				totaladministrativos = 0;
				// Se recorren los módulos principales
				for (sistemas=0; sistemas<total; sistemas++) 
				{
					switch (obj.raiz[sistemas].tipsis)
					{
						case '1': // Modulo Principales
							if(obj.raiz[sistemas].total>0)
							{
								cadenaprincipales = formatoCadena(cadenaprincipales,obj,sistemas,totalprincipales);
								totalprincipales++;
							}
						break;
	
						case '2': // Modulo Auxiliares
							if(obj.raiz[sistemas].total>0)
							{
								cadenaauxiliares = formatoCadena(cadenaauxiliares,obj,sistemas,totalauxiliares);
								totalauxiliares++;
							}
						break;
	
						case '3': // Modulo Personal
							if(obj.raiz[sistemas].total>0)
							{
								cadenapersonal = formatoCadena(cadenapersonal,obj,sistemas,totalpersonal);
								totalpersonal++;
							}
						break;
	
						case '4': // Modulo Herramientas
							if(obj.raiz[sistemas].total>0)
							{
								cadenaherramientas = formatoCadena(cadenaherramientas,obj,sistemas,totalherramientas);
								totalherramientas++;
							}
						break;
	
						case '5': // Modulo Administrativos
							if(obj.raiz[sistemas].total>0)
							{
								cadenaadministrativos = formatoCadena(cadenaadministrativos,obj,sistemas,totaladministrativos);
								totaladministrativos++;
							}
						break;
					}
				}
				DetallesPrincipales = "";
				DetallesAdministrativos = "";
				DetallesAuxiliares = "";
				DetallesPersonal = "";
				DetallesHerramientas = "";
				Contenido = "<ul class='nav nav-tabs nav-justified' id='myModulos' role='tablist'>";
				if (totalprincipales>0)
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Principales-tab' data-bs-toggle='tab' data-bs-target='#Principales' type='button' role='tab' aria-controls='Principales' aria-selected='true'>M&oacutedulos Principales</button></li>";
					Activa = "";
					DetallesPrincipales = cadenaprincipales + "</div>";
				}
				else
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Principales-tab' data-bs-toggle='tab' data-bs-target='#Principales' type='button' role='tab' aria-controls='Principales' aria-selected='true'>M&oacutedulos Principales</button></li>";
				}
				if (totaladministrativos>0)
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Administrativos-tab' data-bs-toggle='tab' data-bs-target='#Administrativos' type='button' role='tab' aria-controls='Administrativos' aria-selected='true'>M&oacutedulos Administrativos</button></li>";
					Activa = "";
					DetallesAdministrativos = cadenaadministrativos + "</div>";
				}
				else
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Administrativos-tab' data-bs-toggle='tab' data-bs-target='#Administrativos' type='button' role='tab' aria-controls='Administrativos' aria-selected='true'>M&oacutedulos Administrativos</button></li>";
				}
				if (totalauxiliares>0)
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Auxiliares-tab' data-bs-toggle='tab' data-bs-target='#Auxiliares' type='button' role='tab' aria-controls='Auxiliares' aria-selected='true'>M&oacutedulos Auxiliares</button></li>";
					Activa = "";
					DetallesAuxiliares = cadenaauxiliares +"</div>";
				}
				else
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Auxiliares-tab' data-bs-toggle='tab' data-bs-target='#Auxiliares' type='button' role='tab' aria-controls='Auxiliares' aria-selected='true'>M&oacutedulos Auxiliares</button></li>";
				}
				if (totalpersonal>0)
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Personal-tab' data-bs-toggle='tab' data-bs-target='#Personal' type='button' role='tab' aria-controls='Personal' aria-selected='true'>M&oacutedulos de Personal</button></li>";
					Activa = "";
					DetallesPersonal = cadenapersonal + "</div>";
				}
				else
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Personal-tab' data-bs-toggle='tab' data-bs-target='#Personal' type='button' role='tab' aria-controls='Personal' aria-selected='true'>M&oacutedulos de Personal</button></li>";
				}
				if (totalherramientas>0)
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Herramientas-tab' data-bs-toggle='tab' data-bs-target='#Herramientas' type='button' role='tab' aria-controls='Herramientas' aria-selected='true'>Herramientas del Sistema</button></li>";
					Activa = "";
					DetallesHerramientas = completarCadena(cadenaherramientas,totalherramientas);
				}
				else
				{
					Contenido = Contenido + "<li class='nav-item' role='presentation'><button class='nav-link' id='Herramientas-tab' data-bs-toggle='tab' data-bs-target='#Herramientas' type='button' role='tab' aria-controls='Herramientas' aria-selected='true'>Herramientas del Sistema</button></li>";
				}
				Contenido =  Contenido + "</ul>";
				document.getElementById('Contenido').innerHTML=Contenido;
				document.getElementById('Principales').innerHTML=DetallesPrincipales;
				document.getElementById('Administrativos').innerHTML=DetallesAdministrativos;
				document.getElementById('Auxiliares').innerHTML=DetallesAuxiliares;
				document.getElementById('Personal').innerHTML=DetallesPersonal;
				document.getElementById('Herramientas').innerHTML=DetallesHerramientas;
			}
			else
			{
				messageError('No se pudo Cargar el Escritorio.'+obj.raiz[0].mensaje);
			}
		},
                error: function (response)
                { 
                    messageError('No se pudo Cargar el Escritorio. Favor Contacte al administrador del Sistema.');
                }
		
	});
}


function formatoCadena(cadena,obj,id,total)
{
    if (total == 0)
    {
            cadena = cadena + " <div class='row' style='margin-top:30px'>";
    }
    if (total == 4)
    {
            cadena = cadena + "</div> " +
                     " <div class='row' style='margin-top:30px'>";
    }
    if (total == 7)
    {
            cadena = cadena + "</div> " +
                     " <div class='row' style='margin-top:30px'>";
    }
    
    cadena = cadena +"<div class='col-sm-3' align='center'> " +
                     "	<a href='"+obj.raiz[id].accsis+"'><img src='base/imagenes/"+obj.raiz[id].imgsis+"' class='rounded img-fluid' alt='"+obj.raiz[id].nomsis+"'></a>" +
                     "	<div class='control-label'>"+obj.raiz[id].nomsis+"</div>" +
                     "</div> ";
    return cadena;
}

function completarCadena(cadena,total)
{
    if ((total == 3) || (total == 6) || (total == 9))
    {
            cadena = cadena + "</div> ";
    }
    else 
    {
        if (total < 3)
        {
            for (cont=total; cont<3; cont++) 
            {
                cadena = cadena + "<div class='col-sm-3' align='center'></div> ";
            }
            cadena = cadena + "</div> ";
        }
        else
        {
            if (total < 6)
            {
                for (cont=total; cont<6; cont++) 
                {
                    cadena = cadena + "<div class='col-sm-3' align='center'> </div> ";
                }
                cadena = cadena + "</div> ";
            }            
            else
            {
                if (total < 9)
                {
                    for (cont=total; cont<9; cont++) 
                    {
                        cadena = cadena + "<div class='col-sm-3' align='center'> </div> ";
                    }
                    cadena = cadena + "</div> ";
                }            
            }
        }
    }
    return cadena;
}

cargarEscritorio();


var triggerTabList = [].slice.call(document.querySelectorAll('#myModulos a'))
triggerTabList.forEach(function (triggerEl) {
  var tabTrigger = new bootstrap.Tab(triggerEl)

  triggerEl.addEventListener('click', function (event) {
    event.preventDefault()
    tabTrigger.show()
  })
})

