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

var barraherramienta = false; 
var tbguardar = false;
Ext.onReady(function(){
	
		var rutaarchivo ='../../controlador/sss/sigesp_ctr_sss_menu.php';
    	Ext.QuickTips.init();
    	
    	var tb_barraherramienta = new Ext.Toolbar();
    	
    	if(barraherramienta){
			tb_barraherramienta.render('barra_herramientas');
		}
		
    	
    	// Tool Bar que va a obtener las Opciones de Menu
		var objbarraherramienta ={
			'operacion': 'barraherramienta', 
			'codsis': sistema,
			'nomfisico': vista
		};
		var objbarraherramienta = JSON.stringify(objbarraherramienta);
		var parametros = 'objdata='+objbarraherramienta; 
		Ext.Ajax.request({
		url : rutaarchivo,
		params : parametros,
		method: 'POST',
		success: function (resultado, request)
		{ 
			herramienta=0;
			obj   = eval('('+resultado.responseText+')');
			if (obj.raiz == null)
			{
				
			}
			else
			{
				total = obj.raiz.length;
				// Generar el menu de manera dinamica
				for (menu=0; menu<total; menu++) 
				{
					herramienta=1;
					if (obj.raiz[menu].cancelar==1)
					{
						// Acción de cancelar
						var cancelar = new Ext.Action(
						{
							text: 'Cancelar',
							handler: irCancelar,
							iconCls: 'bmenucancelar',
							tooltip: 'Limpiar campos'
						});
						tb_barraherramienta.add(cancelar);
					}
					if (obj.raiz[menu].incluir==1)
					{
						// Acción de Nuevo
						var nuevo = new Ext.Action(
						{
							text: 'Nuevo',
							handler: irNuevo,
							iconCls: 'menunuevo',
							tooltip: 'Crear un nuevo registro'
						});
						tbnuevo = true;
						tb_barraherramienta.add(nuevo);
					}
					if (obj.raiz[menu].cambiar==1)
					{
						// Acción de Actualizar
						tbactualizar = true;
					}
					if (((tbnuevo==true)|| (tbactualizar==true)) && (tbguardar==false))
					{
						// Acción de Guardar
						var guardar = new Ext.Action(
						{
							text: 'Guardar',
							handler: irGuardar,
							iconCls: 'menuguardar',
							tooltip: 'Guardar ó Actualizar un Registro'
						});
						tbguardar = true;
						tb_barraherramienta.add(guardar);
					}
					if (obj.raiz[menu].leer==1)
					{
						// Acción de Leer
						var buscar = new Ext.Action(
						{
							text: 'Buscar',
							handler: irBuscar,
							iconCls: 'bmenubuscar',
							tooltip: 'Buscar un registro'
						});
						tb_barraherramienta.add(buscar);
					}
					if (obj.raiz[menu].eliminar==1)
					{
						// Acción de Eliminar
						var eliminar = new Ext.Action(
						{
							text: 'Eliminar',
							handler: irEliminar,
							iconCls: 'menueliminar',
							tooltip: 'Eliminar un Registro'
						});
						tb_barraherramienta.add(eliminar);
					}
					if (obj.raiz[menu].anular==1)
					{
						// Acción de Anular
						var anular = new Ext.Action(
						{
							text: 'Anular',
							handler: irAnular,
							iconCls: 'bmenuanular',
							tooltip: 'Anular un Registro'
						});
						tb_barraherramienta.add(anular);
					}
					if (obj.raiz[menu].ejecutar==1)
					{
						// Acción de Procesar
						var procesar = new Ext.Action(
						{
							text: 'Procesar',
							handler: irProcesar,
							iconCls: 'bmenuprocesar',
							tooltip: 'Procesar'
						});
						tb_barraherramienta.add(procesar);
					}
					if (obj.raiz[menu].administrativo==1)
					{
						// Acción de Administrador
						tbadministrativo = true;
					}
					if (obj.raiz[menu].imprimir==1)
					{
						// Acción de Imprimir
						var imprimir = new Ext.Action(
						{
							text: 'Imprimir',
							handler: irImprimir,
							iconCls: 'menuimprimir',
							tooltip: 'Imprimir un Registro'
						});
						tb_barraherramienta.add(imprimir);
					}
					if (obj.raiz[menu].descargar==1)
					{
						// Acción de Descargar
						var descargar = new Ext.Action(
						{
							text: 'Descargar',
							handler: irDescargar,
							iconCls: 'bmenudescargar',
							tooltip: 'Descargar Archivos Generados'
						});
						tb_barraherramienta.add(descargar);
					}
					if (obj.raiz[menu].ayuda==1)
					{
						// Acción de Ayuda
						var ayuda = new Ext.Action(
						{
							text: 'Ayuda',
							handler: irAyuda,
							iconCls: 'bmenuayuda',
							tooltip: 'Ayuda sobre la funcionalidad'
						});
						tb_barraherramienta.add(ayuda);
					}
				}
			}
			if (herramienta == 1)
			{
				// Acción de Volver
				var volver = new Ext.Action(
				{
					text: 'Salir',
					handler: irVolver,
					iconCls: 'menusalir',
					tooltip: 'Volver Menu Principal'
				});
				tb_barraherramienta.add(volver);
			}						
		},
		failure: function (resultado,request) 
		{ 
			Ext.MessageBox.alert('Error', request); 
		}
		});
		

/***********************************************************************************
* @Función para mostrar el archivo de ayuda.
* @parametros: pagina
* @retorno: 
* @fecha de creación: 02/09/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
		function irAyuda(pagina)
		{
			pagina = vista.replace('sigesp_vis_','sigesp_ayu_');
			pagina = pagina.replace('html','pdf');
			pagina = pagina.replace('php','pdf');
			pagina = 'ayuda/'+pagina;
			abrirVentana(pagina);
		}


/***********************************************************************************
* @Función para Volver a la página de inicio
* @parametros: 
* @retorno: 
* @fecha de creación: 12/11/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
		function irVolver()
		{
			cadena = sistema.toLowerCase();
			location.href = 'sigesp_vis_'+cadena+'_inicio.html';
			barraherramienta=false;
		}
	}
);

