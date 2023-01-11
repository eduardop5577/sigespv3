/***********************************************************************************
* @Archivo javascript que incluye tanto los componentes como los eventos asociados 
* al proceso de asignar usuarios a un prefijo. 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var cambiar = false;
var panel      = '';
var pantalla   = 'usuariosprefijo';
var ruta = '../../controlador/sss/sigesp_ctr_sss_usuariospermisos.php';
var RecordDefUsu = '';
var gridUsu   = '';
var dsusuario = '';
var arrAdmin	= new Array();
var arrEliminar = new Array();
var usuarioElim = '';
var toteliminar = 0;
var datosNuevo={'raiz':[{'codusu':'','nomusu':'','apeusu':''}]};
barraherramienta    = true;
Ext.onReady
(
	function()
	{
	    Ext.QuickTips.init();
		Ext.form.Field.prototype.msgTarget = 'side';
		var agregar = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregar,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar usuario a un Prefijo'
		});
		
		var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'bmenuquitar',
        	tooltip: 'Quitar usuario de un Prefijo'
		});

		var datSistema = {'raiz':[{'codsis':'SEP','nomsis':'Solicitud de Ejecución Presupuestaria'},{'codsis':'SOC','nomsis':'Ordenes de Compra'}]};
		
		var recordPrefijo = Ext.data.Record.create([
			{name: 'codsis'},     
			{name: 'nomsis'}
			]);	
						
		dssistema =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(datSistema),
			reader: new Ext.data.JsonReader(
			{
				root: 'raiz',               
				id: 'id'   
			},
			recordPrefijo
			),
			data: datSistema			
		 });

		var datPrefijo = {'raiz':[{'prefijo':'No posee prefijo....'}]};
		
		recordPrefijo = Ext.data.Record.create([
			{name: 'prefijo'}
		]);		
					
		dsprefijo =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(datPrefijo),
			reader: new Ext.data.JsonReader(
			{
				root: 'raiz',               
				id: 'id'   
			},
			recordPrefijo
			),
			data: datPrefijo			
		 });	 
					 
		
		Xpos = ((screen.width/2)-(550/2)); 
		Ypos = ((screen.height/2)-(550/2));
		//Panel con los componentes del formulario
		panel = new Ext.FormPanel({
        labelWidth: 75,
     	title: 'Proceso de Asignar Usuarios a Prefijo',
        bodyStyle:'padding:5px 5px 5px',
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
	  	tbar: [],
        defaults: {width: 230},		   
		items:[{
			   	xtype:'fieldset',
				title:'Datos del Sistema',
				id:'fsSistema',
    			autoHeight:true,
				autoWidth:true,
				cls :'fondo',	
			    items:[{	
					xtype:'combo',
					fieldLabel:'Sistema',
					readOnly:true,
					name:'sistema',
					id:'cmbsistema',
					emptyText:'Seleccione',
					displayField:'nomsis',
					valueField:'codsis',
					typeAhead: true,
					mode: 'local',
					triggerAction: 'all',
					store: dssistema,							
					width:300
				}
			   ,{
					xtype:'combo',
					fieldLabel:'Prefijo',
					readOnly:true,
					name:'prefijo',
					id:'cmbprefijo',
					emptyText:'Seleccione',
					displayField:'prefijo',
					valueField:'prefijo',
					typeAhead: true,
					mode: 'local',
					triggerAction: 'all',
					store: dsprefijo,							
					width:300
			
					}]
				},{
					xtype:'panel',
					width:500,
					title:'Usuarios para los Prefijos',
					tbar: [agregar,quitar],
					contentEl:'grid-usuariosprefijo'
			}]
		});
		panel.render(document.body);
		Ext.getCmp('cmbsistema').addListener('select',irPrefijos);
		obtenerGridUsuario();
	}
);	//FIN

		
/***********************************************************************************
* @Función para agregar un registro en la grid y llamar al catálogo de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function irAgregar()
	{
		ParamGridTarget = gridUsu;
		var arreglotxt     = new Array('','');		
		var arreglovalores = new Array('codusu','cedusu','nomusu','apeusu','telusu','email','ultingusu','actusu','admusu','nota');
		ObjUsuario      = new catalogoUsuario();
		ObjUsuario.mostrarCatalogo('','',arreglotxt, arreglovalores);
	}			


/***********************************************************************************
* @Función para crear la grid y pasarle los datos del usuario.
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function obtenerGridUsuario()
	{	
		RecordDefUsu = Ext.data.Record.create
		([
			{name: 'codusu'}, 
			{name: 'nomusu'},
			{name: 'apeusu'}
		]);
		
		var DatosNuevo = {'raiz':[{'codusu':'','nomusu':'','apeusu':''}]};	
		dsusuario =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
			},
				  RecordDefUsu
			),
			data: DatosNuevo
			});
		
		gridUsu = new Ext.grid.GridPanel({
				width:500,
				autoScroll:true,
				border:true,
				ds: dsusuario,
				cm: new Ext.grid.ColumnModel([
					{header: 'Código', width: 100, sortable: true,   dataIndex: 'codusu'},
					{header: 'Nombre', width: 200, sortable: true, dataIndex: 'nomusu'},
					{header: 'Apellido', width: 200, sortable: true, dataIndex: 'apeusu'}
				]),
				viewConfig: {
								forceFit:true
							},
				autoHeight:true,
				stripeRows: true
		});
		gridUsu.render('grid-usuariosprefijo');
	}
	
		
/***********************************************************************************
* @Función para confirmar eliminar un registro de la grid de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irQuitar()
	{
		var claveseleccionada = gridUsu.selModel.selections.keys;
		if(claveseleccionada.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el registro?', borrarRegistro);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}
	}
	
	
/***********************************************************************************
* @Función para eliminar un registro de la grid de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creación: 15/08/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function borrarRegistro(btn) 
	{
		if (btn=='yes') 
		{
			var filaseleccionada = gridUsu.getSelectionModel().getSelected();
			if (filaseleccionada)
			{
				usuarioElim    = gridUsu.getSelectionModel().getSelected().get('codusu');
				arrEliminar[toteliminar] = usuarioElim;
				toteliminar++;
				gridUsu.store.remove(filaseleccionada);
				Ext.Msg.alert('Exito','Registro eliminado');				
			}
		} 
	}
	
	
/***********************************************************************************
* @Limpiar campos del formulario
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function limpiarCampos() 
	{		 
		Ext.getCmp('cmbprefijo').setValue('');	
		Ext.getCmp('cmbsistema').setValue('Seleccione');	
		for (i=0; i<=arrAdmin.length; i++)
		{
			arrAdmin.pop();			
		}
		for (i=0; i<=arrEliminar.length; i++)
		{
			arrEliminar.pop();			
		}
	}


/***********************************************************************************
* @Función que limpia los campos y asigna un nuevo código
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irCancelar()
	{
		limpiarCampos();
		gridUsu.store.removeAll();
		gridUsu.store.loadData(datosNuevo);
		gridUsu.store.commitChanges();
		arrEliminar = new Array();
		toteliminar = 0;
		cambiar = false;
	}


/***********************************************************************************
* @Función que guarda o actualiza los datos del proceso de asignación.
* @parametros: 
* @retorno: 
* @fecha de creación: 24/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irGuardar()
	{
		valido=true;
		if((!tbactualizar)&&(cambiar))
		{
			valido=false;
			Ext.MessageBox.alert('Error','No tiene permiso para Modificar.');
		}
		if (Ext.getCmp('cmbsistema').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar un Sistema');
		}
		else if (Ext.getCmp('cmbprefijo').getValue()=='')
		
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar un Prefijo');
		}
		else
		{
			obtenerMensaje('procesar','','Guardando Datos');
			codsis = Ext.getCmp('cmbsistema').getValue();
			prefijo = Ext.getCmp('cmbprefijo').getValue();
			codtippersss=prefijo;
			var cadenaJson = "{'oper': 'actualizar','codsis':'"+codsis+"','seleccionado':'prefijo','sistema': sistema,'vista': vista,'codtippersss': '"+codtippersss+"'";				
			arrAdmin = gridUsu.store.getModifiedRecords();
			cadenaJson=cadenaJson+ ",datosAdmin:[";
			total = arrAdmin.length;
			if (total>0)
			{	
				for (i=0; i < total; i++)
				{
					if (i==0)
					{
						cadenaJson = cadenaJson +"{'codusu':'"+ arrAdmin[i].get('codusu')+ "'}";
					}
					else
					{
						cadenaJson = cadenaJson +",{'codusu':'"+ arrAdmin[i].get('codusu')+ "'}";
					}
				}			
			}
			cadenaJson = cadenaJson + ']';
			cadenaJson=cadenaJson+ ',datosEliminar:[';
			total = arrEliminar.length;
			if (total>0)
			{
				for (i=0; i < total; i++)
				{
					if (i==0)
					{
						cadenaJson = cadenaJson +"{'codusu':'"+ arrEliminar[i]+ "'}";
					}
					else
					{
						cadenaJson = cadenaJson +",{'codusu':'"+ arrEliminar[i]+ "'}";
					}
				}			
			}
			cadenaJson = cadenaJson + ']}';
			objdata= eval('(' + cadenaJson + ')');	
			objdata=JSON.stringify(objdata);
			parametros = 'objdata='+objdata; 
			Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				datos = resultado.responseText;
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				if (datajson.raiz.valido==true)
				{	
					Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					irCancelar();  
				}
				else
				{
					Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
				}
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
			}					
			});
		}	
	}
	
/***********************************************************************************
* @Función que elimina un usuario de una unidad ejecutora seleccionada.
* @parametros: 
* @retorno: 
* @fecha de creación: 29/10/2008. 
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function irEliminar()
	{
		var Result;
		Ext.MessageBox.confirm('Confirmar', '¿Desea eliminar todos los Usuarios?', Result);
		function Result(btn)
		{
			if(btn=='yes')
			{ 
				if ((validarObjetos('cmbsistema','60','novacio')=='0')&&(validarObjetos('cmbprefijo','60','novacio')=='0'))
				{					
					Ext.MessageBox.alert('Mensaje','No existen datos para eliminar');
				}
				else
				{
					obtenerMensaje('procesar','','Eliminando Datos');
					codsis = Ext.getCmp('cmbsistema').getValue();
					prefijo = Ext.getCmp('cmbprefijo').getValue();
					codtippersss=prefijo;
					
					var objdata ={
						'oper': 'eliminar', 
						'codsis':codsis,
						'seleccionado':'prefijo',
						'sistema': sistema,
						'vista': vista,
						'codtippersss': codtippersss						
					};	
					objdata=JSON.stringify(objdata);
					parametros = 'objdata='+objdata;
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( resultado, request )
					{ 
						datos = resultado.responseText;
						Ext.Msg.hide();
						var datajson = eval('(' + datos + ')');
						if (datajson.raiz.valido==true)
						{
							Ext.MessageBox.alert('Mensaje',datajson.raiz.mensaje);
							irCancelar();	  
						}
						else
						{
							Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
						}
					},
					failure: function ( result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', 'Error al procesar la información'); 
					} 
					});
				}
			}
		};		
	}	


/***********************************************************************************
* @Función para definir la búsqueda de las cuentas asociadas al banco seleccionado.   
* @parametros: 
* @retorno:
* @fecha de creación: 04/12/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/				
	function irPrefijos()
	{
		gridUsu.store.removeAll();
		gridUsu.store.loadData(datosNuevo);
		gridUsu.store.commitChanges();
		arrEliminar = new Array();
		toteliminar = 0;
		cambiar = false;

		codsis = Ext.getCmp('cmbsistema').getValue();
		Ext.getCmp('cmbprefijo').setValue('');
		var objdata ={
			'oper': 'obtenerPrefijo', 
			'codsis': codsis,
			'sistema': sistema,
			'vista': vista
		};	
		objdata=Ext.util.JSON.encode(objdata);			
		parametros = 'ObjSon='+objdata;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_controlnumero.php',
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				var datajson = Ext.util.JSON.decode(datos);
				if (datajson.raiz[0]!=null)
				{
					dsprefijo.loadData(datajson);
				}
				Ext.getCmp('cmbprefijo').addListener('select',irUsuarios);				
			},
			failure: function ( result, request)
			{ 
				Ext.MessageBox.alert('Error', result.responseText); 
			},
			
		});	
	}
/***********************************************************************************
* @Función que carga los usuarios de la Banco
* @parámetros: 
* @retorno: 
* @fecha de creación: 29/10/2008
* @autor: Ing. Gusmary Balza. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function irUsuarios()
	{
		gridUsu.store.removeAll();
		gridUsu.store.loadData(datosNuevo);
		gridUsu.store.commitChanges();
		arrEliminar = new Array();
		toteliminar = 0;
		cambiar = false;

		codsis = Ext.getCmp('cmbsistema').getValue();
		prefijo = Ext.getCmp('cmbprefijo').getValue();
		codtippersss=prefijo;
		var objdata ={
				'oper': 'catalogodetalle',
				'codtippersss': codtippersss,
				'codsis': codsis,
				'campo': 'prefijo',
				'tabla': 'sigesp_ctrl_numero',
				'sistema': sistema,
				'vista': vista					
		};
		objdata=JSON.stringify(objdata);
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				if (datos!='')
				{
					var myObject = eval('(' + datos + ')');
					if(myObject.raiz[0].valido==true)
					{
						gridUsu.store.loadData(myObject);
					}
					else
					{
						Ext.MessageBox.alert('Error', myObject.raiz[0].mensaje+' Al cargar los usuarios.');
					}
				}
			}
		});
	}	
	