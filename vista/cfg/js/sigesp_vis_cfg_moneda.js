/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de Moneda. 
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
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var pantalla    		= 'sigesp_vis_cfg_moneda.php'; 							// Variable que contiene el nombre fï¿½sico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_moneda.php'; 	// Ruta del Controlador de la Pantalla
var Actualizar=null;


var dataStoreDetalleEliminacion = new Ext.data.SimpleStore({
    fields: ['codmon','fecha','tascam1','tascam2']
});

var registroDetalle = Ext.data.Record.create
	([
		{name: 'codmon'}, 
		{name: 'fecha'},
		{name: 'tascam1'},
		{name: 'tascam2'}
	]);
	
var Campos =new Array(
						['codmon','novacio|'],
						['denmon','novacio|'],
						['desmon','novacio|'],
						['codpai','novacio|'],
						['estatuspri',''],
						['abrmon','novacio|']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

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
    	   	 title:"Definici&#243;n de Moneda",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:75px',
    		 width: 700,
    		 height: 400,
    		 labelPad: 10,
    		 items:[{
				        layout:"form",
						border:false,
						style: "margin-top:30px;padding-left:50px;",
						labelWidth:100,
						items:[
						       {
							        xtype:"textfield",
							        fieldLabel:"C&#243;digo",
							        labelWidth:40,
							        labelSeparator:'',
							        name:"codigo",
							        id:"codmon",
									autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
							        width:40,
									disabled:true
				        	   },
				        	   {
							        xtype:"textfield",
							        fieldLabel:"Denominaci&#243;n",
							        labelWidth:40,
							        labelSeparator:'',
							        name:"denominacion",
							        id:"denmon",
									autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
							        width:250
					           },
				        	   {
							        xtype:"textfield",
							        fieldLabel:"Descripci&#243;n",
							        labelWidth:40,
							        labelSeparator:'',
							        name:"descripcion",
							        id:"desmon",
									autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
							        width:250
					           },
					           {
									xtype:"checkbox",
					        	   	fieldLabel:"Moneda Principal",
									name:"chkestatuspri",
									id:"estatuspri",
									inputValue:1
							   },
					           {
					        		layout : "column",
					        		defaults : {
					        			border : false
					        		},
					        		items : [
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "textfield",
					        					columnWidth : 0.25,
					        					labelWidth : 100,
					        					items : [ {
					        						xtype : "textfield",
					        						fieldLabel : "Pa&#237;s",
					        						name : "pais",
					        						labelSeparator:'',
					        						id : "codpai",
					        						width : 40,
					        						readOnly:true,
					        						autoCreate : {
					        							tag : 'input',
					        							type : 'text',
					        							size : '3',
					        							autocomplete : 'off',
					        							maxlength : '3'
					        						}
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "button",
					        					columnWidth : 0.1,
					        					items : [ {
					        						iconCls : 'menubuscar',
					        						handler : function() {
					        							mostrarCatalogoPais('catalogo', Ext.getCmp('codpai'), Ext.getCmp('despai')); 
					        						}
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "label",
					        					columnWidth : 0.65,
					        					items : [ {
							        						text:'',
															id:"despai",
															cls:"x-form-item"
					        							} ]
					        				}  ]

					        	},
					           {
				        		   	xtype:"textfield",
							        fieldLabel:"Abreviatura",
							        labelWidth:100,
							        labelSeparator:'',
							        name:"abreviatura",
							        id:"abrmon",
							        width:40,
									autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-,_$');"}
							   }]
			        },
					{
						xtype:'panel',
						width:650,
						height:400,
						style: "padding-left:50px;",
						autoScroll:true,
						title:'Detalle de la Moneda',
						tbar: [agregar,quitar],
						contentEl:'grid_detalle_moneda'
			        }]
    		});
     formulario.render("formulario_moneda");
     obtenerGridDetalle();
	}
);

function irCancelar()
{
	irNuevo();
}

function irBuscar()
{
	CatalogoMoneda();
}

function irNuevo()
{
	limpiarCampos();
	Ext.getCmp('despai').setText('');
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_moneda.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codmon').setValue(codigo);
			if(gridDetalles.store.getCount()>0)
			{
				gridDetalles.store.removeAll();
			}
		}
	}	
	})
}


function irAgregar()
{
	if (Ext.getCmp('codmon').getValue() !='')
	{
		detalleMoneda = new registroDetalle
		({
		'codmon':'',
		'fecha':'',
		'tascam1':'',
		'tascam2':''
		});
		var fecha = new Date();
		
		detalleMoneda.set('codmon',Ext.getCmp('codmon').getValue());
		detalleMoneda.set('fecha',fecha.format('d/m/Y'));
		detalleMoneda.set('tascam1','0,00');
		detalleMoneda.set('tascam2','0,00');
		
		if(gridDetalles.store.getCount()==0)
		{
			gridDetalles.store.insert(0,detalleMoneda);
		}
		else
		{
			gridDetalles.store.insert(gridDetalles.store.getCount(),detalleMoneda);	
		}
	}
	else
	{
		Ext.MessageBox.alert('Mensaje','Debe seleccionar una moneda, verifique por favor');
	}
}

function irQuitar()
{
	var detallesEliminar = gridDetalles.getSelectionModel().getSelections();
	
	for (i=0; i<detallesEliminar.length; i++)
    {
		if(detallesEliminar[i].isModified('codmon'))
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
	var datosNuevo = {'raiz':[{'codmon':'','fecha':'','tascam1':'','tascam2':''}]};
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
				{header: 'Fecha de vigencia', width: 100, sortable: true,   dataIndex: 'fecha', 
                     editor: new Ext.form.DateField({
                         allowBlank: false
                     })},
				{header: 'Tasa de cambio (moneda principal)', width: 200, sortable: true, dataIndex: 'tascam1', align: 'right', editor: new Ext.form.TextField({
					maxLength:20,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:8,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto)
								{
                                                                    var numero = objeto.getValue();
                                                                    valor = formatoNumericoMostrar(objeto.getValue(),8,'.',',','','','-','');
                                                                    objeto.setValue(valor);
								},
								'focus':function(objeto)
								{
                                                                    var numero = formatoNumericoEdicion(objeto.getValue());
                                                                    objeto.setValue(numero);
									
								}
							}
				})},
				{header: 'Tasa de cambio (moneda secundaria)', width: 200, sortable: true, dataIndex: 'tascam2', align: 'right', editor: new Ext.form.TextField({
					maxLength:20,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
                                        precision:8,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto)
                                                                {
                                                                        var numero = objeto.getValue();
                                                                                valor = formatoNumericoMostrar(objeto.getValue(),8,'.',',','','','-','');
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
										if(!objeto.record.isModified('fecha'))
											{
											 Ext.MessageBox.alert('Mensaje','No puede modificar un detalle ya guardado, verifique por favor');	
											 objeto.cancel=true;
											}
									},
						'afteredit': function(Obj){
								         if (Obj.value != '' && Obj.field == 'fecha') {
								            	   	var fecha = Obj.value;
								            	   	var objfecha = new Date(fecha);
								            	   	var fechaformato = objfecha.format(Date.patterns.fechacorta);
								           			gridDetalles.getSelectionModel().getSelected().set('fecha', fechaformato);
								         }
								     }
								       
					  }
	});
	gridDetalles.render('grid_detalle_moneda');
}

function irGuardar()
{
	if (validarObjetos2())
	{
		obtenerMensaje('procesar','','Guardando Datos');
		
		var estatuspri = 0;
		if(Ext.getCmp('estatuspri').checked)
		{
			estatuspri = 1;
		}		
		if (Actualizar == null)
		{
			if (gridDetalles.store.getCount() > 0)
			{
				var arregloObjeto = "{'oper':'incluir','codmenu':" + codmenu
						+ ",'cabecera':[{'codmon':'"
						+ Ext.getCmp('codmon').getValue() + "','denmon':'"
						+ Ext.getCmp('denmon').getValue() + "','desmon':'"
						+ Ext.getCmp('desmon').getValue() + "','codpai':'"
						+ Ext.getCmp('codpai').getValue() + "','estatuspri':"
						+ estatuspri + ",'estmonpri':"
						+ parseInt(Ext.getCmp('codmon').getValue())
						+ ",'abrmon':'" + Ext.getCmp('abrmon').getValue()
						+ "'}]";
				var arregloJsonIncluir = "";
                                arregloJsonIncluir = ",'detallesincluir':[";
                                var numDetalle = 0;
                                gridDetalles.store.each(function (reDetalle)
                                {
                                    if(numDetalle==0)
                                    {                                    
                                        arregloJsonIncluir = arregloJsonIncluir
                                                        + "{'codmon':'"
                                                        + reDetalle.get('codmon')
                                                        + "','fecha':'"
                                                        + reDetalle.get('fecha')+ "','tascam1':'"
                                                        + reDetalle.get('tascam1') + "','tascam2':'"
                                                        + reDetalle.get('tascam2') + "'}";   
                                    }
                                    else
                                    {
                                        arregloJsonIncluir = arregloJsonIncluir
                                                        + ",{'codmon':'"
                                                        + reDetalle.get('codmon')
                                                        + "','fecha':'"
                                                        + reDetalle.get('fecha')+ "','tascam1':'"
                                                        + reDetalle.get('tascam1') + "','tascam2':'"
                                                        + reDetalle.get('tascam2') + "'}";   
                                    }
                                    numDetalle++;
                                            
                                }); 
                                arregloJsonIncluir = arregloJsonIncluir + "]";
				arregloObjeto = arregloObjeto + arregloJsonIncluir + "}";
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
									limpiarCampos();
									Ext.getCmp('despai').setText('');
								}
								if (datos == '2')
								{
									Ext.Msg.show({
										title : 'Mensaje',
										msg : 'No se Incluyo el registro solo puede existir una moneda principal',
										buttons : Ext.Msg.OK,
										icon : Ext.MessageBox.INFO
									});
									gridDetalles.store.commitChanges();
									gridDetalles.store.removeAll();
									limpiarCampos();
									Ext.getCmp('despai').setText('');
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
				var arregloObjeto = "{'oper':'actualizar','codmenu':" + codmenu
						+ ",'cabecera':[{'codmon':'"
						+ Ext.getCmp('codmon').getValue() + "','denmon':'"
						+ Ext.getCmp('codmon').getValue() + "','denmon':'"
						+ Ext.getCmp('denmon').getValue() + "','desmon':'"
						+ Ext.getCmp('desmon').getValue() + "','codpai':'"
						+ Ext.getCmp('codpai').getValue() + "','estatuspri':"
						+ estatuspri + ",'estmonpri':"
						+ parseInt(Ext.getCmp('codmon').getValue())
						+ ",'abrmon':'" + Ext.getCmp('abrmon').getValue()
						+ "'}]";
				var arregloJsonIncluir = "";
                                arregloJsonIncluir = ",'detallesincluir':[";
                                var numDetalle = 0;
                                gridDetalles.store.each(function (reDetalle)
                                {
                                    if(numDetalle==0)
                                    {                                    
                                        arregloJsonIncluir = arregloJsonIncluir
                                                        + "{'codmon':'"
                                                        + reDetalle.get('codmon')
                                                        + "','fecha':'"
                                                        + reDetalle.get('fecha')+ "','tascam1':'"
                                                        + reDetalle.get('tascam1') + "','tascam2':'"
                                                        + reDetalle.get('tascam2') + "'}";   
                                    }
                                    else
                                    {
                                        arregloJsonIncluir = arregloJsonIncluir
                                                        + ",{'codmon':'"
                                                        + reDetalle.get('codmon')
                                                        + "','fecha':'"
                                                        + reDetalle.get('fecha')+ "','tascam1':'"
                                                        + reDetalle.get('tascam1') + "','tascam2':'"
                                                        + reDetalle.get('tascam2') + "'}";   
                                    }
                                    numDetalle++;
                                            
                                }); 
                                arregloJsonIncluir = arregloJsonIncluir + "]";
				arregloObjeto = arregloObjeto + arregloJsonIncluir + "}";
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
							if (respuesta == "1") 
							{
								Ext.Msg.show({
									title : 'Mensaje',
									msg : 'Registro actualizado exitosamente',
									buttons : Ext.Msg.OK,
									icon : Ext.MessageBox.INFO
								});
								gridDetalles.store.commitChanges();
								gridDetalles.store.removeAll();
								limpiarCampos();
								Ext.getCmp('despai').setText("");
							}
							else
							{
								Ext.Msg.show({
									title : 'Mensaje',
									msg : 'Ocurrio un error actualizando el registro',
									buttons : Ext.Msg.OK,
									icon : Ext.MessageBox.INFO
								});
								gridDetalles.store.commitChanges();
								gridDetalles.store.removeAll();
								limpiarCampos();
								Ext.getCmp('despai').setText("");
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
	}
}

function irEliminar()
{
	var respuesta;
	var estatuspri = 0;
	if(Ext.getCmp('estatuspri').checked)
	{
		estatuspri = 1;
	}			
	if(Actualizar)
	{
		function respuesta(btn)
		{
			if(btn=='yes')
			{
			var arregloObjeto = "{'oper':'eliminar','codmenu':"+codmenu+",'cabecera':[{'codmon':'"+Ext.getCmp('codmon').getValue()+"','denmon':'"+Ext.getCmp('denmon').getValue()+"','codpai':'"+Ext.getCmp('codpai').getValue()+"','estatuspri':"+estatuspri+",'abrmon':'"+Ext.getCmp('abrmon').getValue()+"'}]";
			if(gridDetalles.store.getCount() > 0)
			{
				detalleEliminar = gridDetalles.store.getRange(0,gridDetalles.store.getCount()-1);
				var arregloJsonEliminar = ",'detalleseliminar':[";
				for(var i=0;i<=detalleEliminar.length-1;i++)
				{	
					if(!(detalleEliminar[i].isModified('codmon')))
					{
						if(i==0)
						{
							arregloJsonEliminar = arregloJsonEliminar + "{'codmon':'"+detalleEliminar[i].get('codmon')+"','fecha':'"+detalleEliminar[i].get('fecha')+"','tascam1':'"+detalleEliminar[i].get('tascam1')+"','tascam2':'"+detalleEliminar[i].get('tascam2')+"'}";
						}	
						else
						{
							arregloJsonEliminar = arregloJsonEliminar + ",{'codmon':'"+detalleEliminar[i].get('codmon')+"','fecha':'"+detalleEliminar[i].get('fecha')+"','tascam1':'"+detalleEliminar[i].get('tascam1')+"','tascam2':'"+detalleEliminar[i].get('tascam2')+"'}";
						}
					}
				}
				arregloJsonEliminar = arregloJsonEliminar + "]";
				arregloObjeto  = arregloObjeto + arregloJsonEliminar + "}";
				var jsonMoneda = eval('(' + arregloObjeto + ')');
				ObjSon=Ext.util.JSON.encode(jsonMoneda);
				parametros = 'ObjSon='+ObjSon;
				Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( resultado, request )
				{ 
					Ext.Msg.hide();
					var respuesta = eval('('+resultado.responseText+')');
					if(respuesta.resultado != null)
					{
						Ext.Msg.show({
										title:'Mensaje',
										msg: respuesta.resultado[0]+"<br>"+respuesta.resultado[1],
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.INFO
									});
						gridDetalles.store.commitChanges();
						gridDetalles.store.removeAll();
						limpiarCampos();
						Ext.getCmp('despai').setText("");
					}
				  },
				failure: function ( result, request)
				 { 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
				} 
				  });
			}
			else
			{
					Ext.Msg.hide();
			  Ext.MessageBox.alert('Error','El registro seleccionado no tiene detalles asignados, verifique por favor'); 
			}
		}
	}
		Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta);
	}
	else
	{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Opci&#243;n inv&#225;lida, el registro debe estar previamente guardado, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});  
	}	
}