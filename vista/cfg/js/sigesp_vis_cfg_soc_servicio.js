/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true;
var gridDetalles = null;
var formsocservicio = null;
var ActualizarRegistro      = null;
var Actualizar      = null;

var ruta			= '../../controlador/cfg/sigesp_ctr_cfg_soc_servicio.php'; 	// Ruta del Controlador de la Pantalla

var dataStoreDetalleEliminacion = new Ext.data.SimpleStore({
    fields: ['codcar','dencar','porcar']
});

var registroDetalle = Ext.data.Record.create
	([
		{name: 'codcar'}, 
		{name: 'dencar'},
		{name: 'porcar'}
	]);

var Campos =new Array(
		
	        ['codser','novacio|'],
	        ['codtipser','novacio|'],
	        ['dentipser','novacio|'],
	        ['denser','novacio|'],
	        ['preser','|'],
			['spg_cuenta','novacio|'],
			['denominacionspg','novacio|'],
			['codunimed','novacio|'],
			['denunimed','novacio|']
		)
	    
Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';	
	Ext.QuickTips.init();
	Ext.Ajax.timeout=36000000000;	
	
	 var agregar = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregar,
			iconCls: 'agregar',
	    	tooltip: 'Agregar Cargo'
		});
		
	var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'remover',
	    	tooltip: 'Quitar Cargo'
		});
	
	
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 100;
	formsocservicio = new Ext.FormPanel({
	applyTo: 'formulario_soc_servicio',
	width: 700,
	height: 690,
	labelWidth: 120,
	title: 'Servicios',
	frame:true,
	defaultType: 'textfield',
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
	items: [{
		xtype: 'textfield',
		fieldLabel: 'C&#243;digo',
		name: 'c&#243;digo',
		id: 'codser',
                autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
		disabled:true,
		width: 100,		
		listeners:{
					'blur':function(objeto)
					{
						var numero = objeto.getValue();
						valor = ue_rellenarcampo(numero,10);
						objeto.setValue(valor);
					}
				}
		},{
		xtype: 'textfield',
		fieldLabel: 'Tipo',
		name: 'tipo',
		id: 'codtipser',
		disabled:true,		
		width: 100
		},{
		xtype: 'textfield',
		labelSeparator :'',
		style:'border:none;background:#f1f1f1;',
		id: 'dentipser',
		disabled:true,		
		width: 550
		},{
		xtype:'button',
		iconCls: 'menubuscar',
		style:'position:absolute;left:235px;top:26px;',
		tooltip:'Buscar tipo de servicio',
		handler: function (){
				catalogoTipoServicio();
				}
		},{		
		xtype: 'textfield',
		fieldLabel: 'Denominaci&#243;n',
		name: 'denominaci&#243;n',
		autoCreate: {tag: 'input', type: 'text', onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
		id: 'denser',		
		width: 500
		},{
		xtype: 'textfield',
		fieldLabel: 'Monto',
		name: 'Monto',
		autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789');"},
		id: 'preser',		
		width: 150,
		listeners:{
					'blur':function(objeto)
					{
						var numero = objeto.getValue();
						valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
						objeto.setValue(valor);
					},
					'focus':function(objeto)
					{
						var numero = formatoNumericoEdicion(objeto.getValue());
						objeto.setValue(numero);
					}
				}
		},{
		xtype: 'textfield',
		fieldLabel: 'Cuenta',
		name: 'Cuenta',
		id: 'spg_cuenta',
		disabled:true,		
		width: 150	
		},{
		xtype:'button',
		iconCls: 'menubuscar',
		style:'position:absolute;left:285px;top:128px;',
		tooltip:'Buscar cuenta',
		handler: function (){
				catalogoCuentasspg('buscar_cuenta_servicio');
				}
		},{
		xtype: 'textfield',
		labelSeparator :'',
		style:'border:none;background:#f1f1f1;',
		id: 'denominacionspg',
		disabled:true,		
		width: 550
		},{
		xtype: 'textfield',
		fieldLabel: 'Unidad de medida',
		name: 'unidad de medida',
		id: 'codunimed',
		disabled:true,		
		width: 150	
		},{
		xtype:'button',
		iconCls: 'menubuscar',
		style:'position:absolute;left:285px;top:178px;',
		tooltip:'Buscar unidad de medida',
		handler: function (){
				catalogoUnidadMedida();
				}
		},{
		xtype: 'textfield',
		labelSeparator :'',
		style:'border:none;background:#f1f1f1;',
		id: 'denunimed',
		disabled:true,		
		width: 550
		},
		{
			xtype:'panel',
			width:630,
			height:200,
			autoScroll:true,
			title:'Cargos Asociados',
			tbar: [agregar,quitar],
			contentEl:'grid_detalle_cargos'
		}]//fin de items del panel
	});
    obtenerGridDetalle();
});

function irCancelar()
{
	irNuevo();	
}

function irNuevo()
{
	ActualizarRegistro = null;
	limpiarFormulario(formsocservicio);
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function ( result, request)
		{ 
			var codigo = result.responseText;
			if (codigo<0)
			{
				if(tbadministrativo)
				{                            
					Ext.getCmp('codser').setValue('0000000000');
					Ext.getCmp('codser').setDisabled(false);
				}
				else
				{
					Ext.MessageBox.alert('Error','Ha ocurrido un error, contacte al  administrador '); 
				}
			}
			else
			{
				if(codigo != "")
				{
					Ext.getCmp('codser').setValue(codigo);
									Ext.getCmp('codser').setDisabled(true);
				}
				else
				{
					Ext.MessageBox.alert('Error','Ha ocurrido un error, contacte al  administrador '); 
				}
			}
			if(gridDetalles.store.getCount()>0)
			{
					gridDetalles.store.removeAll();
					dataStoreDetalleEliminacion.removeAll();
			}
		},
		failure: function ( result, request)
		{ 
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo '+result.responseText); 
		} 
		
	});
}

function irAgregar()
{
	if (Ext.getCmp('codser').getValue() !='')
	{
		mostrarCatalogoCargo('servicio');
	}
	else
	{
		Ext.MessageBox.alert('Mensaje','Debe seleccionar un servicio, verifique por favor');
	}
}

function irQuitar()
{
	var detallesEliminar = gridDetalles.getSelectionModel().getSelections();
	
	for (i=0; i<detallesEliminar.length; i++)
    {
		if(detallesEliminar[i].isModified('codcar'))
		{
			gridDetalles.store.remove(detallesEliminar[i]);
		}
		else
		{
			dataStoreDetalleEliminacion.add(detallesEliminar[i]);
			gridDetalles.store.remove(detallesEliminar[i]);
		}
    }
}

function obtenerGridDetalle()
{	
	var datosNuevo = {'raiz':[{'codcar':'','dencar':'','porcar':''}]};
	var modoSeleccionDetalle = new Ext.grid.CheckboxSelectionModel({});
	var dataStoreDetalle =  new Ext.data.Store({
	proxy: new Ext.data.MemoryProxy(datosNuevo),
	reader: new Ext.data.JsonReader({
		root: 'raiz',               
		id: 'id'   
		},
			registroDetalle
		)
		});
	
   gridDetalles = new Ext.grid.EditorGridPanel({
			width:630,
			autoScroll:true,
			height:100,
			border:true,
			ds: dataStoreDetalle,
			cm: new Ext.grid.ColumnModel([
			     modoSeleccionDetalle,
				{header: 'C&#243;digo', width: 200, sortable: true, dataIndex: 'codcar'},
				{header: 'Denominaci&#243;n', width: 500, sortable: true, dataIndex: 'dencar'},
				{header: 'Porcentaje', width: 500, sortable: true, dataIndex: 'porcar'}
			]),
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		                      viewConfig:{
		                      forceFit:true
		                      },
			autoHeight:true,
			stripeRows: true
	});
	gridDetalles.render('grid_detalle_cargos');
}

function irBuscar()
{
	mostrarCatalogoServicio();
	limpiarFormulario(formsocservicio);
	ActualizarRegistro=true;
}

function irGuardar()
{
	if(validarObjetos2()==false)
	{
		return false;
	}
	else
	{
		obtenerMensaje('procesar','','Guardando Datos');
		if(ActualizarRegistro == null)
		{		
			var cadenajson = "{'oper':'incluir',"+
							"'datoscabecera':[{'codser':'"+Ext.getCmp('codser').getValue()+"',"+
							"'codtipser':'"+Ext.getCmp('codtipser').getValue()+"',"+
							"'denser':'"+Ext.getCmp('denser').getValue()+"',"+
							"'preser':'"+ue_formato_operaciones(Ext.getCmp('preser').getValue())+"',"+
							"'spg_cuenta':'"+Ext.getCmp('spg_cuenta').getValue()+"',"+
							"'codunimed':'"+Ext.getCmp('codunimed').getValue()+"'}]";
		}
		else
		{
			var cadenajson = "{'oper':'actualizar',"+
							"'datoscabecera':[{'codser':'"+Ext.getCmp('codser').getValue()+"',"+
							"'codtipser':'"+Ext.getCmp('codtipser').getValue()+"',"+
							"'denser':'"+Ext.getCmp('denser').getValue()+"',"+
							"'preser':'"+ue_formato_operaciones(Ext.getCmp('preser').getValue())+"',"+
							"'spg_cuenta':'"+Ext.getCmp('spg_cuenta').getValue()+"',"+
							"'codunimed':'"+Ext.getCmp('codunimed').getValue()+"'}]";
		}
		
		cadenajson = cadenajson +",'esp_soc_serviciocargo':[";
       	var numDetalle = 0;
       	if(gridDetalles.getStore().getCount()!=0)
		{
       		gridDetalles.store.each(function (DetalleCargo)
			{
				if(Ext.getCmp('codser').getValue()!='')
				{
					if (numDetalle==0)
					{
						cadenajson = cadenajson +"{'codcar':'"+DetalleCargo.get('codcar')+"',"+	
												 "'codser':'"+Ext.getCmp('codser').getValue()+"'}";
	
					}
					else
					{
						cadenajson = cadenajson +",{'codcar':'"+DetalleCargo.get('codcar')+"',"+	
												 "'codser':'"+Ext.getCmp('codser').getValue()+"'}";
					}
					numDetalle++;
				}
			});
		}
		cadenajson = cadenajson + "]";
		if (dataStoreDetalleEliminacion.getCount() > 0)
		{
			var detalleEliminar = dataStoreDetalleEliminacion.getRange(0,dataStoreDetalleEliminacion.getCount() - 1);
			total = detalleEliminar.length;
			cadenajson = cadenajson + ",'cargoseliminar':[";
			for ( var i = 0; i <= total - 1; i++)
			{
				if (i == 0)
				{
					cadenajson = cadenajson +"{'codcar':'"+detalleEliminar[i].get('codcar')+"',"+	
											 "'codser':'"+Ext.getCmp('codser').getValue()+"'}";
				}
				else
				{
					cadenajson = cadenajson +",{'codcar':'"+detalleEliminar[i].get('codcar')+"',"+	
											 "'codser':'"+Ext.getCmp('codser').getValue()+"'}";
				}
			}
			cadenajson = cadenajson + "]";
			detalleEliminar.length=0;
		}
		cadenajson = cadenajson + "}";
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_soc_servicio.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request )
			{ 
				Ext.Msg.hide();
				var datos = resultad.responseText;
				var resultado = datos.split("|");
				if(resultado[1]=="1")
				{
					switch(resultado[0])
					{
						case "0":
							Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
							break;
						case "1":
							Ext.MessageBox.alert('Mensaje','El registro fue actualizado');
							break;
						case "2":
							Ext.MessageBox.alert('Mensaje','El registro fue incluido');
							break;
					}
				}
				else
				{
					Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
				}
				limpiarFormulario(formsocservicio);
				gridDetalles.store.removeAll();
				dataStoreDetalleEliminacion.removeAll();
		},
			failure: function ( result, request)
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	}
}

function irEliminar()
{
	function eliminar(btn)
	{
		if(btn=='yes')
		{
			obtenerMensaje('procesar','','Eiminando Datos');
			var cadenajson = "{'oper':'eliminar',"+
							"'datoscabecera':[{'codser':'"+Ext.getCmp('codser').getValue()+"',"+
							"'codtipser':'"+Ext.getCmp('codtipser').getValue()+"',"+
							"'denser':'"+Ext.getCmp('denser').getValue()+"',"+
							"'preser':'"+ue_formato_operaciones(Ext.getCmp('preser').getValue())+"',"+
							"'spg_cuenta':'"+Ext.getCmp('spg_cuenta').getValue()+"',"+
							"'codunimed':'"+Ext.getCmp('codunimed').getValue()+"'}]";
			arrCargos =gridDetalles.store.getRange(0,gridDetalles.store.getCount()-1);
			cadenajson = cadenajson +",'esp_soc_serviciocargo':[";
			total = arrCargos.length;
			if (total>0)
			{	
				if(Ext.getCmp('codser').getValue()!='')
				{
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							cadenajson = cadenajson +"{'codcar':'"+arrCargos[i].get('codcar')+"',"+	
													 "'codser':'"+Ext.getCmp('codser').getValue()+"'}";
		
						}
						else
						{
							cadenajson = cadenajson +",{'codcar':'"+arrCargos[i].get('codcar')+"',"+	
													 "'codser':'"+Ext.getCmp('codser').getValue()+"'}";
						}
					}
				}
			}
			cadenajson = cadenajson + "]}";
			var parametros = 'ObjSon='+cadenajson;
			Ext.Ajax.request({
				url : '../../controlador/cfg/sigesp_ctr_cfg_soc_servicio.php',
				params : parametros,
				method: 'POST',
				success: function ( resultad, request )
				{ 
					Ext.Msg.hide();
			        var resultado = resultad.responseText;
			       	if(resultado=="1")
					{
						Ext.MessageBox.alert('Mensaje','El registro fue eliminado');
					}
					else
					{
						if(resultado=="0")
						{
							Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
						}
						else 
						{
							if (resultado=="-1")
							{
								Ext.MessageBox.alert('Error','El registro no puede ser eliminado ya que tiene relaci&#243;n con otros modulos');
							}
							else
							{
								if(resultado=="-8")
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
								}
							}							
						}
					}
			       	limpiarFormulario(formsocservicio);
					gridDetalles.store.removeAll();
					dataStoreDetalleEliminacion.removeAll();					
			},
				failure: function ( result, request)
				{ 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
				} 
			});
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminar);
}