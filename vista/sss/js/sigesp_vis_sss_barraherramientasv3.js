/***********************************************************************************
* @Barra de Herramientas Genéricas para todas las funcionalidades del sistema 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/
var usuario = "";
var objusuario ={
                    'operacion': 'cabecera',
                    'codsis': sistema
		};
				
objusuario=JSON.stringify(objusuario);

$.ajax({
    url:'../../controlador/sss/sigesp_ctr_sss_seguridad.php',
    type:"POST",
    cache:false,
    data:'objdata='+objusuario,
    success: function(data)
    {
            obj   = eval('('+data+')');
            if(obj.raiz[0].valido==true)
            {
                    usuario = obj.raiz[0].apeusu+', '+obj.raiz[0].nomusu+'';
            }
            else
            {
                    alert('Error->'+obj.raiz[0].mensaje);
                    setTimeout('volverEscritorio()',500);
            }			
    },
    failure: function (data) 
    { 
            alert('Error->No se pudo Cargar el sistema. Favor Contacte al administrador del Sistema.');
            setTimeout('volverEscritorio()',500);
    }
});


var barraherramienta = false; 
var tbguardar = false;
var strbarra = '';
var strpermisos = '';
var objbarra ={
                'operacion': 'barraherramienta', 
                'codsis': sistema,
                'nomfisico': vista
};
			
objbarra=JSON.stringify(objbarra);

$.ajax({
    url:'../../controlador/sss/sigesp_ctr_sss_menu.php',
    type:"POST",
    cache:false,
    data:'objdata='+objbarra,
    success:function(data)
    {
        strbarra = strbarra + "<header class='hs-menubar'>";
        strbarra = strbarra + "		<div class='brand-logo'> ";
        strbarra = strbarra + "			<a href='#home_link'><img src='../../base/imagenes/sigesp_img_logov3.jpg' alt='Sigesp' width='600' height='141' title='sigesp'> </a>";
        strbarra = strbarra + "		</div>";
        strbarra = strbarra + "		<div class='menu-trigger'> <i class='zmdi zmdi-menu'></i></div>";
        strbarra = strbarra + "		<div class='hs-user toggle' data-reveal='.user-info'>";
        strbarra = strbarra + "			<img src='../../vista/sss/fotos/usuario.jpg' alt='' width='105' height='40'>";
        strbarra = strbarra + "		</div>";
        strbarra = strbarra + "		<div class='grid-trigger toggle' data-reveal='.grid-items'> <i class='zmdi zmdi-view-module'></i> </div>";
        strbarra = strbarra + "		<div class='more-trigger toggle' data-reveal='.user-penal'> <i class='zmdi zmdi-more-vert'></i></div>";
        strbarra = strbarra + "</header>";
        strbarra = strbarra + "<section class='box-model'>";
        strbarra = strbarra + "		<ul class='user-penal'>";
        strbarra = strbarra + "			<li> <a href=javascript:volverEscritorio();> <i class='zmdi zmdi-star'></i> Modulos  </a> </li>";
        strbarra = strbarra + " 		<li> <a href=javascript:cerrarSession();> <i class='zmdi zmdi-run'></i> Salir  </a> </li>";
        strbarra = strbarra + "		</ul>";
        strbarra = strbarra + "		<ul class='user-info'>";
        strbarra = strbarra + "			<li class='profile-pic'> </li>";
        strbarra = strbarra + " 		<li class='user-name'>"+usuario+"</li>";
        strbarra = strbarra + "		</ul>";
        herramienta=0;
        obj   = eval('('+data+')');
        if (obj.raiz == null)
        {

        }
        else
        {
            total = obj.raiz.length;
            strbarra = strbarra + "		<ul class='grid-items'>";
            // Generar la barra de manera dinamica
            for (menu=0; menu<total; menu++) 
            {
                if (obj.raiz[menu].cancelar==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irCancelar();><img src='../../base/imagenes/sigesp_img_cancelarv3.png' title='Cancelar' width='20' height='20'></a></li>";
                }
                if (obj.raiz[menu].incluir==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irNuevo();><img src='../../base/imagenes/sigesp_img_nuevov3.png' title='Nuevo' width='20' height='20'></a></li>";
                    tbnuevo = true;
                }
                if (obj.raiz[menu].cambiar==1)
                {
                    tbactualizar = true;
                }	
                if (((tbnuevo==true) || (tbactualizar==true)) && (tbguardar==false))
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irGuardar();><img src='../../base/imagenes/sigesp_img_guardarv3.png' title='Guardar' width='20' height='20'></a></li>";	
                    tbguardar = true;
                }
                if (obj.raiz[menu].leer==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irBuscar();><img src='../../base/imagenes/sigesp_img_buscarv3.png' title='Buscar' width='20' height='20'></a></li>";	
                }
                if (obj.raiz[menu].eliminar==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irEliminar();><img src='../../base/imagenes/sigesp_img_eliminarv3.png' title='Eliminar' width='20' height='20'></a></li>";	
                }
                if (obj.raiz[menu].anular==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irAnular();><img src='../../base/imagenes/sigesp_img_eliminarv3.png' title='Anular' width='20' height='20'></a></li>";	
                }
                if (obj.raiz[menu].ejecutar==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irProcesar();><img src='../../base/imagenes/sigesp_img_procesarv3.png' title='Procesar' width='20' height='20'></a></li>";	
                }
                if (obj.raiz[menu].administrativo==1)
                {
                    tbadministrativo = true;
                }	
                if (obj.raiz[menu].imprimir==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irImprimir();><img src='../../base/imagenes/sigesp_img_imprimirv3.png' title='Imprimir' width='20' height='20'></a></li>";	
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irImprimirExcel();><img src='../../base/imagenes/sigesp_img_imprimirexcelv3.png' title='Imprimir Excel' width='20' height='20'></a></li>";	
                }
                if (obj.raiz[menu].descargar==1)
                {
                    strbarra = strbarra + "			<li class='grid'><a href=javascript:irDescargar();><img src='../../base/imagenes/sigesp_img_descargarv3.png' title='Descargar' width='20' height='20'></a></li>";	
                }
            }
            strbarra = strbarra + "		</ul>";
            
            strpermisos = strpermisos + "<input type=hidden name=incluir id=incluir value='"+obj.raiz[0].incluir+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=cambiar id=cambiar value='"+obj.raiz[0].cambiar+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=leer id=leer value='"+obj.raiz[0].leer+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=eliminar id=eliminar value='"+obj.raiz[0].eliminar+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=anular id=anular value='"+obj.raiz[0].anular+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=ejecutar id=ejecutar value='"+obj.raiz[0].ejecutar+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=imprimir id=imprimir value='"+obj.raiz[0].imprimir+"'>"; 
            strpermisos = strpermisos + "<input type=hidden name=descargar id=descargar value='"+obj.raiz[0].descargar+"'>"; 
            
        }
        strbarra = strbarra + "</section>";
        divbarra = document.getElementById('barraherramientas');
        divbarra.innerHTML = strbarra;		
        divpermisos = document.getElementById('permisos');
        divpermisos.innerHTML = strpermisos;		
        
    },
    failure:function(data)
    {
        alert('Ocurrio un error al Crear la Barra de Herramientas. Contacte con el Administrador del Sistema ');	
    }
});



/***********************************************************************************
* @Función para regresar al escritorio
* @parametros: 
* @retorno: 
* @fecha de creación: 14/10/2008
* @autor: Ing. Yesenia Moreno.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
function volverEscritorio()
{
    parent.location.target='_parent';
    parent.location.href='../../escritorio.html';
}

/***********************************************************************************
* @Función para regresar al escritorio
* @parametros: 
* @retorno: 
* @fecha de creación: 14/10/2008
* @autor: Ing. Yesenia Moreno.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
function cerrarSession()
{
    if(confirm("Desea salir del Sistema?"))
    {
        top.close();
    }
}