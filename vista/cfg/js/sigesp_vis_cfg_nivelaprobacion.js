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
var formulario          = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var pantalla    		= 'sigesp_vis_cfg_nivelaprobacion.php'; 							// Variable que contiene el nombre fï¿½sico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_nivelaprobacion.php'; 	// Ruta del Controlador de la Pantalla
var Actualizar=null;
   
var dataStoreDetalleEliminacion = new Ext.data.SimpleStore({
    fields: ['codniv','monnivdes','monnivhas']
});

var registroDetalle = Ext.data.Record.create
	([
		{name: 'codniv'}, 
		{name: 'monnivdes'},
		{name: 'monnivhas'}
	]);
	

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 var agregar = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregar,
			iconCls: 'agregar',
	    	tooltip: 'Agregar detalle'
		});
		
	var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'remover',
	    	tooltip: 'Quitar detalle'
		});
	 var Xpos = ((screen.width/2)-(700/2));
	 var formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n Niveles de Aprobaci&#243;n",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:75px',
    		 width: 700,
    		 height: 400,
    		 labelPad: 10,
    		 items:[
					{
						xtype:'panel',
						width:650,
						height:400,
						style: "padding-left:50px;",
						autoScroll:true,
						title:'Niveles',
						tbar: [agregar,quitar],
						contentEl:'grid_detalle'
			        }]
    		});
     formulario.render("formulario_nivel");
     obtenerGridDetalle();
	 irNuevo();
	}
);

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	var myJSONObject ={"oper":"catalogo"};
	var	ObjSon=Ext.util.JSON.encode(myJSONObject);
	var	parametros ='ObjSon='+ObjSon;
    Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function ( resultad, request )
		{ 
			var datos = resultad.responseText;
			var objetodata = eval('(' + datos + ')');
			if(objetodata != '')
			{
				if((objetodata.raiz == null) || (objetodata.raiz == ''))
				{
					Ext.MessageBox.alert('Informaci&#243;n','No se encontraron datos')
					Actualizar=false;
				}
				else
				{
					gridDetalles.getStore().loadData(objetodata);
					Actualizar=true;
				}
			}
	    },
	    failure: function ( result, request)
		{ 
	    	Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
	    } 
	});
}


function irAgregar()
{
	var myJSONObject ={"oper":"nuevo"};
	var	ObjSon=Ext.util.JSON.encode(myJSONObject);
	var	parametros ='ObjSon='+ObjSon;
    Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function ( resultad, request )
		{ 
			var datos = resultad.responseText;
			var codigo = eval('(' + datos + ')');
			if(codigo != "")
			{
				detallenivel = new registroDetalle
				({
				'codniv':'',
				'monnivdes':'',
				'monnivhas':''
				});
				detallenivel.set('codniv',codigo);
				detallenivel.set('monnivdes','0,00');
				detallenivel.set('monnivhas','0,00');
				
				if(gridDetalles.store.getCount()==0)
				{
					gridDetalles.store.insert(0,detallenivel);
				}
				else
				{
					gridDetalles.store.insert(gridDetalles.store.getCount(),detallenivel);	
				}
			}
	    },
	    failure: function ( result, request)
		{ 
	    	Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
	    } 
	});
}

function irQuitar()
{
	var detallesEliminar = gridDetalles.getSelectionModel().getSelections();
	
	for (i=0; i<detallesEliminar.length; i++)
    {
		if(detallesEliminar[i].isModified('codniv'))
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
	var datosNuevo = {'raiz':[{'codniv':'','monnivdes':'','monnivdes':''}]};
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
			width:600,
			autoScroll:true,
			height:300,
			border:true,
			ds: dataStoreDetalle,
			cm: new Ext.grid.ColumnModel([
			     modoSeleccionDetalle,
				{header: 'C&#243;digo nivel', width: 100, sortable: true,   dataIndex: 'codniv', align: 'center',editor: new Ext.form.TextField({
                         allowBlank: false,
						 style: 'text-align:center',
						 autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '4', onkeypress: "return keyRestrict(event,'0123456789');"}
                     })},
				{header: 'Monto Aprobaci&#243;n Desde', width: 200, sortable: true, dataIndex: 'monnivdes', align: 'right', editor: new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
				})},
				{header: 'Monto Aprobaci&#243;n Hasta', width: 200, sortable: true, dataIndex: 'monnivhas', align: 'right', editor: new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
				})}
			]),
			viewConfig: {
							forceFit:true
						},
			stripeRows: true,
			sm: new Ext.grid.CheckboxSelectionModel({}),
			listeners:{
						'beforeedit':function(objeto)
									{
										if(!objeto.record.isModified('codniv'))
											{
											 Ext.MessageBox.alert('Mensaje','No puede modificar un detalle ya guardado, verifique por favor');	
											 objeto.cancel=true;
											}
									},
						'afteredit': function(Obj){
								         if (Obj.value == '' && Obj.field == 'codniv')
										 {
											 Ext.MessageBox.alert('Mensaje','Debe colocar un código de Nivel, verifique por favor');	
											 objeto.cancel=true;
								         }
								     }
								       
					  }
	});
   gridDetalles.render('grid_detalle');
}

function irGuardar()
{
	obtenerMensaje('procesar','','Guardando Datos');
	var detalle = gridDetalles.store.getModifiedRecords();
	if (Actualizar == false)
	{
		if (gridDetalles.store.getCount() > 0)
		{
			var arregloObjeto = "{'oper':'incluir','codmenu':" + codmenu;
			var arregloJson = ",'detallesincluir':[";
			for ( var i = 0; i <= detalle.length - 1; i++)
			{
				if (i == 0)
				{
					arregloJson = arregloJson
							+ "{'codniv':'"+ detalle[i].get('codniv')
							+ "','monnivdes':'"+ detalle[i].get('monnivdes') 
							+ "','monnivhas':'"+ detalle[i].get('monnivhas') + "'}";
				}
				else
				{
					arregloJson = arregloJson
							+ ",{'codniv':'"+ detalle[i].get('codniv')
							+ "','monnivdes':'"+ detalle[i].get('monnivdes') 
							+ "','monnivhas':'"+ detalle[i].get('monnivhas') + "'}";
				}
			}
			arregloJson = arregloJson + "]";
			arregloObjeto = arregloObjeto + arregloJson + "}";
			var jsonMoneda = eval('(' + arregloObjeto + ')');
			var ObjSon = Ext.util.JSON.encode(jsonMoneda);
			var parametros = 'ObjSon=' + ObjSon;
			Ext.Ajax.request({
						url : ruta,
						params : parametros,
						method : 'POST',
						success : function(resultado, request)
						{
							Ext.Msg.hide();
							var datos = resultado.responseText;
							if (datos == '1')
							{
								Ext.Msg.show({
									title : 'Mensaje',
									msg : 'Registro incluido exitosamente',
									buttons : Ext.Msg.OK,
									icon : Ext.MessageBox.INFO
								});
								gridDetalles.store.commitChanges();
								gridDetalles.store.removeAll();
								irNuevo();
							}
						},
						failure : function(result, request)
						{
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
						}
			});
		} 
		else
		{
			Ext.Msg.hide();
			Ext.Msg.show({
						title : 'Mensaje',
						msg : 'Debe indicar al menos un detalle asociado a la Moneda, verifique por favor',
						buttons : Ext.Msg.OK,
						icon : Ext.MessageBox.INFO
					});
		}

	} 
	else
	{
		if (gridDetalles.store.getCount() > 0)
		{
			var arregloObjeto = "{'oper':'actualizar','codmenu':" + codmenu;
			var arregloJsonIncluir = "";
			var arregloJsonEliminar = "";
			if (detalle.length > 0)
			{
				arregloJsonIncluir = ",'detallesincluir':[";
				for ( var i = 0; i <= detalle.length - 1; i++)
				{
					if (i == 0)
					{
						arregloJsonIncluir = arregloJsonIncluir
								+ "{'codniv':'"+ detalle[i].get('codniv')
								+ "','monnivdes':'"+ detalle[i].get('monnivdes') 
								+ "','monnivhas':'"+ detalle[i].get('monnivhas') + "'}";
					}
					else
					{
						arregloJsonIncluir = arregloJsonIncluir
								+ ",{'codniv':'"+ detalle[i].get('codniv')
								+ "','monnivdes':'"+ detalle[i].get('monnivdes') 
								+ "','monnivhas':'"+ detalle[i].get('monnivhas') + "'}";
					}
				}
				arregloJsonIncluir = arregloJsonIncluir + "]";
			}
			if (dataStoreDetalleEliminacion.getCount() > 0)
			{
				var detalleEliminar = dataStoreDetalleEliminacion.getRange(0,dataStoreDetalleEliminacion.getCount() - 1);
				arregloJsonEliminar = ",'detalleseliminar':[";
				for ( var i = 0; i <= detalleEliminar.length - 1; i++)
				{
					if (i == 0)
					{
						arregloJsonEliminar = arregloJsonEliminar
								+ "{'codniv':'"+ detalleEliminar[i].get('codniv')
								+ "','monnivdes':'"+ detalleEliminar[i].get('monnivdes') 
								+ "','monnivhas':'"+ detalleEliminar[i].get('monnivhas') + "'}";
					}
					else
					{
						arregloJsonEliminar = arregloJsonEliminar
								+ ",{'codniv':'"+ detalleEliminar[i].get('codniv')
								+ "','monnivdes':'"+ detalleEliminar[i].get('monnivdes') 
								+ "','monnivhas':'"+ detalleEliminar[i].get('monnivhas') + "'}";
					}
				}
				arregloJsonEliminar = arregloJsonEliminar + "]";
			}

			arregloObjeto = arregloObjeto + arregloJsonIncluir + arregloJsonEliminar + "}";
			var jsonMoneda = eval('(' + arregloObjeto + ')');
			ObjSon = Ext.util.JSON.encode(jsonMoneda);
			parametros = 'ObjSon=' + ObjSon;
			Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method : 'POST',
					success : function(resultado, request)
					{
						Ext.Msg.hide();
						var respuesta = resultado.responseText;
						var resultado = respuesta.split("|");
						if (resultado[0] == "0")
						{
							Ext.Msg.show({
								title : 'Mensaje',
								msg : 'Registro actualizado exitosamente',
								buttons : Ext.Msg.OK,
								icon : Ext.MessageBox.INFO
							});
							gridDetalles.store.commitChanges();
							gridDetalles.store.removeAll();
						}
						else
						{
							Ext.Msg.show({
								title : 'Mensaje',
								msg : resultado[1],
								buttons : Ext.Msg.OK,
								icon : Ext.MessageBox.INFO
							});
							gridDetalles.store.commitChanges();
							gridDetalles.store.removeAll();
						}
						irNuevo();
					},
					failure : function(result, request)
					{
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
					}
				});
		} 
		else
		{
			Ext.Msg.hide();
			Ext.Msg.show({
						title : 'Mensaje',
						msg : 'Debe indicar al menos un detalle asociado a los niveles, verifique por favor',
						buttons : Ext.Msg.OK,
						icon : Ext.MessageBox.INFO
					});
		}
	}
}
