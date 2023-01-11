/****************************************************************************************
* @Proceso de permisos en lote.
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var panel = '';
var pantalla = 'permisosenlote';
var administrador = null;
ruta =  '../../controlador/sss/sigesp_ctr_sss_permisosenlote.php'; 
barraherramienta    = true;

var proceso = 	[
               	['-- Seleccione --','---'],
               	['Copiar permisos y asignaciones, blanqueando la permisologia anterior','1'],
				['Copiar permisos y asignaciones manteniendo permisologia anterior','2'], 
				['Blanquear todos los permisos y asignaciones','3'],
				['Agregar todos los permisos y asignaciones','4']]; 
	  	
	var stproceso = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : proceso
	});

	var cmbproceso = new Ext.form.ComboBox({
		store : stproceso,
		fieldLabel : 'Proceso a Aplicar',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'proceso',
		width:400,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local',
		emptyText : '-- Seleccione --'
	});


Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.form.Field.prototype.msgTarget = 'side';		
		Ext.Ajax.timeout=36000000000;
						
		Xpos = ((screen.width/2)-(600/2)); 
		Ypos = ((screen.height/2)-(500/2));
		panel = new Ext.FormPanel({
			title: 'Permisos en Lote',
			bodyStyle:'padding:5px 5px 5px',
			labelWidth: 160,
   			height:250,
   			width:600,
			style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
			tbar: [],
			items:[{
				xtype:'fieldset',
				title:'Usuarios',
				id:'fsformpermisos',
				autoHeight:true,
				Width:600,
				cls :'fondo',		
				items:[{
					xtype:'textfield',
					fieldLabel:'Usuario a otorgar permisos',
					name:'usuario',
					id:'txtcodusuario',
					disabled:true,
					width:200
				  },{
					xtype:'button',
					id:'btnBuscardestino',
					handler: irBuscarDestino,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar un usuario',
					style:'position:absolute;left:385px;top:30px',
					width:50					  
				  },{
					xtype:'textfield',
					fieldLabel:'Usuario a copiar permisos',
					name:'usuario',
					id:'txtcodusuarioorigen',
					disabled:true,
					width:200
				  },{
					xtype:'button',
					id:'btnBuscarorigen',
					handler: irBuscarOrigen,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar un usuario',
					style:'position:absolute;left:385px;top:55px',
					width:50					  
				 }]},
				{
				xtype:'fieldset',
				title:'Procesos',
				id:'fsformprocesos',
				autoHeight:true,
				Width:600,
				cls :'fondo',		
	   			items:[cmbproceso]
   				}]
		});
		panel.render(document.body);
	
}); //fin de archivo	

	function irCancelar() 
	{
		 Ext.getCmp('txtcodusuario').setValue('');
		 Ext.getCmp('txtcodusuarioorigen').setValue('');
		 Ext.getCmp('proceso').setValue('');
	}
			
	
	function irBuscarDestino()
	{
		var arreglotxt = new Array('txtcodusuario');		
		var arreglovalores = new Array('codusu');			
		objCatusuario = new catalogoUsuario();
		objCatusuario.mostrarCatalogo(panel,'fsformpermisos',arreglotxt, arreglovalores);
	}
	
	function irBuscarOrigen()
	{
		var arreglotxt = new Array('txtcodusuarioorigen');		
		var arreglovalores = new Array('codusu');			
		objCatusuario = new catalogoUsuario();
		objCatusuario.mostrarCatalogo(panel,'fsformpermisos',arreglotxt, arreglovalores);
	}
	
	function irProcesar()
	{
		var continuar=false;
		var proceso = Ext.getCmp('proceso').getValue();
		if (proceso=='1')
		{
			if (validarObjetos('txtcodusuarioorigen','20','novacio')!='0')
			{
				if (validarObjetos('txtcodusuario','20','novacio')!='0')
				{
					continuar = true;			
					var objdata ={
						'operacion': 'blanquear_copiar_permisos',
						'codusu': Ext.getCmp('txtcodusuario').getValue(), 
						'codusuorigen': Ext.getCmp('txtcodusuarioorigen').getValue(), 
						'sistema': sistema,
						'vista': vista
						};
				}
				else
				{
					Ext.MessageBox.alert('Error', 'Debe seleccionar el usuario a Copiar los permisos.');
				}
			}
			else
			{
				Ext.MessageBox.alert('Error', 'Debe seleccionar el usuario de donde se van a copiar los permisos.');
			}			
		}
		if (proceso=='2')
		{
			if (validarObjetos('txtcodusuarioorigen','20','novacio')!='0')
			{
				if (validarObjetos('txtcodusuario','20','novacio')!='0')
				{
					continuar = true;			
					var objdata ={
						'operacion': 'copiar_permisos',
						'codusu': Ext.getCmp('txtcodusuario').getValue(), 
						'codusuorigen': Ext.getCmp('txtcodusuarioorigen').getValue(), 
						'sistema': sistema,
						'vista': vista
						};
				}
				else
				{
					Ext.MessageBox.alert('Error', 'Debe seleccionar el usuario a Copiar los permisos.');
				}
			}
			else
			{
				Ext.MessageBox.alert('Error', 'Debe seleccionar el usuario de donde se van a copiar los permisos.');
			}						
		}
		if (proceso=='3')
		{
			if (validarObjetos('txtcodusuarioorigen','20','novacio')=='0')
			{
				if (validarObjetos('txtcodusuario','20','novacio')!='0')
				{
					continuar = true;			
					var objdata ={
						'operacion': 'blanquear_permisos',
						'codusu': Ext.getCmp('txtcodusuario').getValue(), 
						'sistema': sistema,
						'vista': vista
						};
				}
				else
				{
					Ext.MessageBox.alert('Error', 'Debe seleccionar el usuario a blanquear los permisos.');
				}
			}
			else
			{
				Ext.MessageBox.alert('Error', 'Para blanquear los permisos no debe seleccionar el usuario para copiar permisos.');
			}
		}
		if (proceso=='4')
		{
			if (validarObjetos('txtcodusuarioorigen','20','novacio')=='0')
			{
				if (validarObjetos('txtcodusuario','20','novacio')!='0')
				{
					continuar = true;			
					var objdata ={
						'operacion': 'agregar_permisos',
						'codusu': Ext.getCmp('txtcodusuario').getValue(), 
						'sistema': sistema,
						'vista': vista
						};
				}
				else
				{
					Ext.MessageBox.alert('Error', 'Debe seleccionar el usuario a Agregar los permisos.');
				}
			}
			else
			{
				Ext.MessageBox.alert('Error', 'Para Agregar los permisos no debe seleccionar el usuario para copiar permisos.');
			}			
		}	
		if (continuar)
		{
			obtenerMensaje('procesar','','Guardando Datos');
			
			objdata=JSON.stringify(objdata);
			parametros = 'objdata='+objdata; 
			Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultad, request)
			{ 
				datos = resultad.responseText;
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				if (datajson.raiz.valido==true)
				{
					Ext.MessageBox.alert('Mensaje',datajson.raiz.mensaje);
					irCancelar();  
				}
				else
				{
					Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					
				}
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				alert(result);
				Ext.MessageBox.alert('Error', 'El registro no se pudo procesar.'); 
			}					
			});
		}
	}
	
	
