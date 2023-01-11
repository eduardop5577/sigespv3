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
var formsepconcepto = null;
var Actualizar      = null;
var ruta			= '../../controlador/cfg/sigesp_ctr_cfg_sep_concepto.php'; 	// Ruta del Controlador de la Pantalla
var gridDetalles    = null;
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
	        ['codconsep','novacio|'],
	        ['denconsep','novacio|'],
	        ['monconsepe','novacio|'],
	        ['spg_cuenta','novacio|'],
	        ['obsconesp','|']
		)

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

Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';	
	Ext.QuickTips.init();
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 25;
	formsepconcepto = new Ext.FormPanel({
	applyTo: 'formulario_sep_concepto',
	width: 730,
	height: 500,
	title: 'Concepto',
	frame:true,
	defaultType: 'textfield',
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
	items: [{
		xtype: 'textfield',
		fieldLabel: 'C&#243;digo',
		name: 'c&#243;digo',
		id: 'codconsep',
		disabled:true,
		maxLength: 5,
		width: 50		
		},{
		xtype: 'textfield',
		fieldLabel: 'Denominaci&#243;n',
		name: 'denominaci&#243;n',
		autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
		id: 'denconsep',		
		width: 500
		},{
		xtype: 'textfield',
		fieldLabel: 'Monto',
		name: 'Monto',
		id: 'monconsepe',
		autoCreate: {tag: 'input', type: 'text', onkeypress: "return keyRestrict(event,'0123456789');"},
		width: 150,
		listeners:{
					'blur':function(objeto)
					{
						var numero = objeto.getValue();
						valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
						objeto.setValue(valor);
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
		style:'position:absolute;left:265px;top:78px;',
		handler: function (){
				catalogoCuentasspg('buscar_cuenta');
				}
		},{
		xtype: 'textfield',
		labelSeparator :'',
		style:'border:none;background:#f1f1f1;',
		id: 'denominacionspg',
		disabled:true,		
		width: 650
		},{
		xtype: 'textarea',
		fieldLabel: 'Observaciones',
		name: 'observaciones',
		autoCreate: {tag: "textarea", style: "width:100px;height:60px;", onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
		id: 'obsconesp',		
		width: 520
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


function irAgregar()
{
	if (Ext.getCmp('codconsep').getValue() !='')
	{
		mostrarCatalogoCargo('concepto');
	}
	else
	{
		Ext.MessageBox.alert('Mensaje','Debe seleccionar un concepto, verifique por favor');
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
				{header: 'Codigo', width: 200, sortable: true, dataIndex: 'codcar'},
				{header: 'Denominacion', width: 500, sortable: true, dataIndex: 'dencar'},
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


function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarCampos();
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_sep_concepto.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codconsep').setValue(codigo);
			if(gridDetalles.store.getCount()>0)
			{
				gridDetalles.store.removeAll();
				dataStoreDetalleEliminacion.removeAll();					
			}
		}
	}	
	})
}

function irGuardar()
{
	
	if(validarObjetos2()==false)
	{
		return false;
	}
	else
	{
		var cadjson = "{'oper':'incluir',"+
					  "'datoscabecera':[{'codconsep':'"+Ext.getCmp('codconsep').getValue()+"',"+
							"'denconsep':'"+Ext.getCmp('denconsep').getValue()+"',"+
							"'monconsepe':'"+ue_formato_operaciones(Ext.getCmp('monconsepe').getValue())+"',"+
							"'obsconesp':'"+Ext.getCmp('obsconesp').getValue()+"',"+
							"'spg_cuenta':"+Ext.getCmp('spg_cuenta').getValue()+"}],";
					  
		cadjson = cadjson +",'esp_sep_conceptocargos':[";
       	var numDetalle = 0;
       	if(gridDetalles.getStore().getCount()!=0)
		{
       		gridDetalles.store.each(function (DetalleCargo)
			{
				if(Ext.getCmp('codconsep').getValue()!='')
				{
					if (numDetalle==0)
					{
						cadjson = cadjson +"{'codcar':'"+DetalleCargo.get('codcar')+"',"+	
										   "'codconsep':'"+Ext.getCmp('codconsep').getValue()+"'}";
	
					}
					else
					{
						cadjson = cadjson +",{'codcar':'"+DetalleCargo.get('codcar')+"',"+	
										   "'codconsep':'"+Ext.getCmp('codconsep').getValue()+"'}";
					}
					numDetalle++;
				}
			});
		}
		cadjson = cadjson + "]";
		if (dataStoreDetalleEliminacion.getCount() > 0)
		{
			var detalleEliminar = dataStoreDetalleEliminacion.getRange(0,dataStoreDetalleEliminacion.getCount() - 1);
			total = detalleEliminar.length;
			cadjson = cadjson + ",'cargoseliminar':[";
			for ( var i = 0; i <= total - 1; i++)
			{
				if (i == 0)
				{
					cadjson = cadjson +"{'codcar':'"+detalleEliminar[i].get('codcar')+"','codconsep':'"+Ext.getCmp('codconsep').getValue()+"'}";
				}
				else
				{
					cadjson = cadjson +",{'codcar':'"+detalleEliminar[i].get('codcar')+"','codconsep':'"+Ext.getCmp('codconsep').getValue()+"'}";
				}
			}
			cadjson = cadjson + "]";
		}
		cadjson = cadjson +  ",'codmenu':'"+codmenu+"'}";
		obtenerMensaje('procesar','','Guardando Datos');
		var parametros = 'ObjSon=' + cadjson;
		Ext.Ajax.request({
				url: '../../controlador/cfg/sigesp_ctr_cfg_sep_concepto.php',
				params: parametros,
				method: 'POST',
				success: function(resultad, request)
				{
					Ext.Msg.hide();
					var datos = resultad.responseText;
					var resultado = datos.split("|");
					switch (resultado[1])
					{
							case "0":
								Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
								break;
							case "1":
								Ext.MessageBox.alert('Mensaje', 'El registro fue actualizado');
								break;
							case "2":
								Ext.MessageBox.alert('Mensaje', 'El registro fue incluido');
								break;
					}
					limpiarFormulario(formsepconcepto);
					gridDetalles.store.removeAll();
					dataStoreDetalleEliminacion.removeAll();					
				},
				failure: function(result, request)
				{
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
				}
			});
	}
}

function irEliminar()
{
	function eliminando(btn)
	{
		if(btn=='yes')
		{
			if(!validarCamposConcepto())
			{
				return false;
			}
			var cadjson = "{'oper':'eliminar',"+
			  "'datoscabecera':[{'codconsep':'"+Ext.getCmp('codconsep').getValue()+"',"+
					"'denconsep':'"+Ext.getCmp('denconsep').getValue()+"',"+
					"'monconsepe':'"+ue_formato_operaciones(Ext.getCmp('monconsepe').getValue())+"',"+
					"'obsconesp':'"+Ext.getCmp('obsconesp').getValue()+"',"+
					"'spg_cuenta':"+Ext.getCmp('spg_cuenta').getValue()+"}]";
			cadjson = cadjson +",'esp_sep_conceptocargos':[";
			arrCargos = gridDetalles.store.getRange(0,gridDetalles.store.getCount()-1);
			total = arrCargos.length;
			if (total>0)
			{	
				if(Ext.getCmp('codconsep').getValue()!='')
				{
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							cadjson = cadjson +"{'codcar':'"+arrCargos[i].get('codcar')+"',"+	
													 "'codconsep':'"+Ext.getCmp('codconsep').getValue()+"'}";
		
						}
						else
						{
							cadjson = cadjson +",{'codcar':'"+arrCargos[i].get('codcar')+"',"+	
													 "'codconsep':'"+Ext.getCmp('codconsep').getValue()+"'}";
						}
					}
				}
			}
			cadjson = cadjson +  "],'codmenu':'"+codmenu+"'}";
			obtenerMensaje('procesar','','Eliminando Datos');
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({
					url: '../../controlador/cfg/sigesp_ctr_cfg_sep_concepto.php',
					params: parametros,
					method: 'POST',
					success: function(resultad, request)
					{
						Ext.Msg.hide();
						var datos = resultad.responseText;
						var resultado = datos.split("|");
						switch (resultado[1])
						{
							case "-1":
								Ext.MessageBox.alert('Mensaje', 'El registro no puede ser eliminado, ya que ha sido referenciado en otros procesos');
								break;
							case "1":
								Ext.MessageBox.alert('Mensaje', 'El registro fue eliminado');
								break;
							case "0":
								Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
								break;
							case "-8":
								Ext.MessageBox.alert('Error','El registro no puede ser eliminado, no puede eliminar registros intermedios');
								break;
						}
						limpiarFormulario(formsepconcepto);
						gridDetalles.store.removeAll();
						dataStoreDetalleEliminacion.removeAll();					
					},
					failure: function(result, request)
					{
						Ext.Msg.hide();
					 	Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
				    }
				});
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminando);
}

function irBuscar()
{
	catalogoConcepto();
}

function validarCamposConcepto()
{
	if(Ext.getCmp('codconsep').getValue()=="")
	{
		alert('Debe indicar el codigo');
		return false;
	}
	else if(Ext.getCmp('denconsep').getValue()=="")
	{
		alert('Debe indicar la denominacion');
		return false;
	}
	else if(Ext.getCmp('spg_cuenta').getValue()=="")
	{
		alert('Debe indicar la cuenta presupuestaria');
		return false;
	}
	else
	{
		return true;
	}
	
}