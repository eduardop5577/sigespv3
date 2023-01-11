/***********************************************************************************
* @Archivo javascript que incluye tanto los componentes como los eventos asociados 
* al proceso de asignar usuarios a un ODI. 
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
var pantalla   = 'usuariosodi';
var ruta = '../../controlador/sss/sigesp_ctr_sss_usuariospermisos.php';
var RecordDefUsu = '';
var gridUsu   = '';
var dsusuario = '';
var arrAdmin	= new Array();
var arrEliminar = new Array();
var usuarioElim = '';
var toteliminar = 0;
var codsis = 'SRH';
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
        	tooltip: 'Agregar usuario a un ODI'
		});
		
		var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'bmenuquitar',
        	tooltip: 'Quitar usuario de un ODI'
		});

		var datODI = {'raiz':[{'nroreg':'No posee ODI....'}]};
		
		recordODI = Ext.data.Record.create([
			{name: 'nroreg'}
		]);		
					
		dsODI =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(datODI),
			reader: new Ext.data.JsonReader(
			{
				root: 'raiz',               
				id: 'id'   
			},
			recordODI
			),
			data: datODI			
		 });	 
					 
		
		Xpos = ((screen.width/2)-(550/2)); 
		Ypos = ((screen.height/2)-(550/2));
		//Panel con los componentes del formulario
		panel = new Ext.FormPanel({
        labelWidth: 75,
     	title: 'Proceso de Asignar Usuarios a ODI',
        bodyStyle:'padding:5px 5px 5px',
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
	  	tbar: [],
        defaults: {width: 230},		   
		items:[{
			   	xtype:'fieldset',
				title:'Datos del ODI',
				id:'fsODI',
    			autoHeight:true,
				autoWidth:true,
				cls :'fondo',	
			    items:[{	
					xtype:'combo',
					fieldLabel:'ODI',
					readOnly:true,
					name:'ODI',
					id:'cmbODI',
					emptyText:'Seleccione',
					displayField:'nroreg',
					valueField:'nroreg',
					typeAhead: true,
					mode: 'local',
					triggerAction: 'all',
					store: dsODI,							
					width:300
				}]
				},{
					xtype:'panel',
					width:500,
					title:'Usuarios para el ODI',
					tbar: [agregar,quitar],
					contentEl:'grid-usuariosodi'
			}]
		});
		panel.render(document.body);
		obtenerGridUsuario();
		irODI();
	}
);	//FIN

		
/***********************************************************************************
* @Función para agregar un registro en la grid y llamar al catálogo de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
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
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
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
		gridUsu.render('grid-usuariosodi');
	}
	
		
/***********************************************************************************
* @Función para confirmar eliminar un registro de la grid de usuarios.
* @parametros: 
* @retorno: 
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
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
* @autor: Ing. Yesenia Moreno. 
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
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function limpiarCampos() 
	{		 
		Ext.getCmp('cmbODI').setValue('Seleccione');	
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
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
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
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
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
		if (Ext.getCmp('cmbODI').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar un ODI');
		}
		else
		{
			obtenerMensaje('procesar','','Guardando Datos');
			nroreg = Ext.getCmp('cmbODI').getValue();
			codtippersss=nroreg;
			var cadenaJson = "{'oper': 'actualizar','codsis':'"+codsis+"','seleccionado':'odi','sistema': sistema,'vista': vista,'codtippersss': '"+codtippersss+"'";				
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
* @fecha de creación: 20/09/2017. 
* @autor: Ing. Yesenia Moreno. 
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
				if ((validarObjetos('cmbODI','60','novacio')=='0'))
				{					
					Ext.MessageBox.alert('Mensaje','No existen datos para eliminar');
				}
				else
				{
					obtenerMensaje('procesar','','Eliminando Datos');
					nroreg = Ext.getCmp('cmbODI').getValue();
					codtippersss=nroreg;
					
					var objdata ={
						'oper': 'eliminar', 
						'codsis':codsis,
						'seleccionado':'odi',
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
* @fecha de creación: 20/09/2017.
* @autor: Ing. Yesenia Moreno.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/				
	function irODI()
	{
		gridUsu.store.removeAll();
		gridUsu.store.loadData(datosNuevo);
		gridUsu.store.commitChanges();
		arrEliminar = new Array();
		toteliminar = 0;
		cambiar = false;

		Ext.getCmp('cmbODI').setValue('');
		var objdata ={
			'oper': 'obtenerODI'
		};	
		objdata=Ext.util.JSON.encode(objdata);			
		parametros = 'ObjSon='+objdata;
		Ext.Ajax.request({
			url : '../../controlador/srh/sigesp_ctr_srh_odi.php',
			params : parametros,
			method: 'POST',
			success: function (resultado,request)
			{
				datos = resultado.responseText;
				var datajson = Ext.util.JSON.decode(datos);
				if (datajson.raiz[0]!=null)
				{
					dsODI.loadData(datajson);
				}
				Ext.getCmp('cmbODI').addListener('select',irUsuarios);				
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
* @fecha de creación: 20/09/2017
* @autor: Ing. Yesenia Moreno. 
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

		nroreg = Ext.getCmp('cmbODI').getValue();
		codtippersss=nroreg;
		var objdata ={
				'oper': 'catalogodetalle',
				'codtippersss': nroreg,
				'codsis': codsis,
				'campo': 'nroreg',
				'tabla': 'srh_odi',
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
	