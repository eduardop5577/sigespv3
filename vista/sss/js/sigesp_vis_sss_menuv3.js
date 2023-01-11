/***********************************************************************************
* @Javascript para el manejo del Menu en todos los sistemas actualizados a V3
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var strmenu = '';
var objmenu ={
				'operacion': 'menu', 
				'codsis': sistema
			};
			
objmenu=JSON.stringify(objmenu);

$.ajax({
		url:'../../controlador/sss/sigesp_ctr_sss_menu.php',
		type:"POST",
		cache:false,
		data:'objdata='+objmenu,
		success:function(data)
		{
			obj   = eval('('+data+')');
			total = obj.raiz.length;
			// Generar el menu de manera dinamica
			strmenu = strmenu + "<nav class='hs-navigation'>";
			strmenu = strmenu + "	<ul class='nav-links'>";
			for (menu1=0; menu1<total; menu1++) 
			{
				if (obj.raiz[menu1].nivel==1)  // Busco los nodos Principales
				{
					// Menu Principal Nivel 1
					strmenu = strmenu + "	<li class='has-child'>";
					strmenu = strmenu + "		<span class='its-parent'>";
					strmenu = strmenu + "			<span class='icon'>";
					strmenu = strmenu + "				<i class='zmdi zmdi-collection-item'></i>";
					strmenu = strmenu + "			</span>";
					strmenu = strmenu + "			"+obj.raiz[menu1].nomlogico;
					strmenu = strmenu + "		</span>";
					codpadre1 = obj.raiz[menu1].codmenu; 
					nivel1    = obj.raiz[menu1].nivel;
					
					strmenu = strmenu + "		<ul class='its-children'>";							
					for (menu2=0; menu2<total; menu2++) // Recorro todos los nodos para buscar los hijos del nivel 1
					{
						if (obj.raiz[menu2].codpadre==codpadre1) // Verifico que el padre del nodo sea igual al del nivel superior
						{
							strmenu = strmenu + "	<li>";
							strmenu = strmenu + "       <a href='"+obj.raiz[menu2].nomfisico+"'> "+obj.raiz[menu2].nomlogico+" </a>";
							strmenu = strmenu + "	</li>";
						}
					}
					strmenu = strmenu + "		</ul>";
					strmenu = strmenu + "	</li>";
				}
			}
			strmenu = strmenu + "	</ul>";
			strmenu = strmenu + "</nav>";
			divhsmenu = document.getElementById('hsmenu');
			divhsmenu.innerHTML = strmenu;
					
			$(document).ready(function ()
			{
				$(".hs-menubar").hsMenu(); 
			}); 					
		},
		failure:function(data)
		{
			alert('Ocurrio un error al Crear el Menú. Contacte con el Administrador del Sistema ');	
		}
});
