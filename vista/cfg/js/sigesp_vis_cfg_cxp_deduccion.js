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
var formdeduccion       = null;                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_cxp_deduccion.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= true;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar=null;
var banderaCatalogo = 'generica';
var banderaImprimir = false;


var Campos =new Array(
						['codded','novacio|'],
						['dended','novacio|'],
						['sc_cuenta','novacio|'],
						['porded','novacio|'],
						['monded','novacio|'],
						['islr',''],
						['iva',''],
						['estretmun',''],
						['formula',''],
						['otras',''],
						['mondedaux',''],
						['tipopers',''],
						['estretmil',''],
						['retaposol',''],
						['codconret','']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady
(
  function()
	{
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 var Xpos = ((screen.width/2)-(800/2));
	 var Ypos = 90;	
     formdeduccion = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Deducci&#243;n",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 800,
    		 height: 400,
    		 labelPad: 10,
    		 id:'formdeduccion',
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
							        name:"codigo",
							        id:"codded",
									autoCreate: {tag: 'input', type: 'text', size: '5', autocomplete: 'off', maxlength: '5'},
							        width:75,
									disabled:true,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false
				        	   },
				        	   {
							        xtype:"textfield",
							        fieldLabel:"Denominaci&#243;n",
							        labelWidth:40,
							        name:"denominacion",
							        id:"dended",
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
							        width:250,
							        binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false
					           },
				        	   {
							        xtype:"textfield",
							        fieldLabel:"Porcentaje(%)",
							        labelWidth:40,
							        name:"porcentaje",
							        id:"porded",
									autoCreate: {tag: 'input', type: 'text', size: '5', autocomplete: 'off', maxlength: '5', onkeypress: "return keyRestrict(event,'0123456789.');"},
									listeners: {
	                                	'blur': function(){
	                                    			var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
													this.setValue(formatonumero);
										}
									},
							        width:50,
							        binding:true,
									hiddenvalue:'',
									defaultvalue:''
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
					        					columnWidth : 0.40,
					        					labelWidth : 100,
					        					items : [ {
					        						xtype : "textfield",
					        						fieldLabel : "Cuenta Contable",
					        						name : "cuenta_contable",
					        						id : "sc_cuenta",
					        						allowBlank:false,
					        						readOnly:true,
					        						width : 150,
					        						autoCreate : {
					        							tag : 'input',
					        							type : 'text',
					        							size : '15',
					        							autocomplete : 'off',
					        							maxlength : '15'
					        						},
					        						binding:true,
													hiddenvalue:'',
													defaultvalue:'',
													allowBlank:false
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "button",
					        					columnWidth : 0.15,
					        					items : [ {
					        						iconCls : 'menubuscar',
					        						handler : function() {
					        							mostrarCatalogoCuentaContable('catalogocuentamovimiento',Ext.getCmp('sc_cuenta'),Ext.getCmp('dencuenta'));
					        						}
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "label",
					        					columnWidth : 0.45,
					        					items : [ {
							        						text:'',
															id:"dencuenta",
															cls:"x-form-item"
					        			
					        							} ]
					        				}  ]

					        	},
					            {
									xtype:"textfield",
					        	   	fieldLabel:"Monto deducible",
									name:"deducible",
									maxLength:15,
									minLength:1,
									id:"monded",
									width:150,
									style: 'text-align:right',
									autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
									listeners: {
	                                	'blur': function(){
	                                    			var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
													this.setValue(formatonumero);
										}
									},
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0'
								},
								{
									xtype:"fieldset",
									title: "Tipo de deducci&#243;n",
									height:100,
									labelWidth:60,
									border:true,
									width:700,
									items:[{
											xtype: "radiogroup",
											fieldLabel: "",
											labelSeparator:"",
											id:"tipodeduccion",
											vertical:true,
											autoWidth:false,
			             					style: 'margin-top:10px;margin-left: -20px',
											columns: [180,230,200],
											items: [
													{boxLabel: "I.S.L.R", name: "rbtipdeduccion", inputValue: 'S',checked: true,id:'islr',
												     listeners:{'check': function (checkbox, checked) {
														            if(checked){
														            	Ext.getCmp('tipopers').enable();
														            	Ext.getCmp('botbusqueda').enable();
												                    }
												                   }
													           }
												    },
													{boxLabel: "Ret. I.V.A", name: "rbtipdeduccion", inputValue: 'I',id:'iva',
												     listeners:{ 
															       'check': function (checkbox, checked) {
														            if(checked){
														            	Ext.getCmp('tipopers').disable();
														            	Ext.getCmp('botbusqueda').disable();
												                    }
												                   }
											                    }
											        },
													{boxLabel: "Ret. Municipal", name: "rbtipdeduccion", inputValue: 'M',id:'estretmun',
												     listeners:{ 
															       'check': function (checkbox, checked) {
														            if(checked){
														            	Ext.getCmp('tipopers').disable();
														            	Ext.getCmp('botbusqueda').disable();
												                    }
												                   }
											                    }
										    		},
													{boxLabel: "Ret. Aporte Social", name: "rbtipdeduccion", inputValue: 'A',id:'retaposol',
												     listeners:{ 
															       'check': function (checkbox, checked) {
														            if(checked){
														            	Ext.getCmp('tipopers').disable();
														            	Ext.getCmp('botbusqueda').disable();
												                    }
												                   }
											       }
										    		},
													{boxLabel: "Ret. 1x1000", name: "rbtipdeduccion", inputValue: '1',id:'estretmil',
													 listeners:{ 
																       'check': function (checkbox, checked) {
															            if(checked){
															            	Ext.getCmp('tipopers').disable();
															            	Ext.getCmp('botbusqueda').disable();
													                    }
													                   }
												                   }
											    	},
													{boxLabel: "Otras", name: "rbtipdeduccion", inputValue: 'O',id:'otras',
												     listeners:{ 
															       'check': function (checkbox, checked) {
														            if(checked){
														            	Ext.getCmp('tipopers').disable();
														            	Ext.getCmp('botbusqueda').disable();
												                    }
												                   }
											                    }
										    		}],
											    	binding:true,
													hiddenvalue:'',
													defaultvalue:''
													
											}]
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
					        					columnWidth : 0.75,
					        					items : [ {
					        						xtype : "textfield",
													labelWidth:50,
					        						fieldLabel : "F&#243;rmula",
					        						name : "formula",
					        						id : "formula",
													readOnly:true,
					        						width : 350,
					        						autoCreate : {
					        							tag : 'input',
					        							type : 'text',
					        							size : '254',
					        							autocomplete : 'off',
					        							maxlength : '254'
					        						},
					        						binding:true,
													hiddenvalue:'',
													defaultvalue:'',
													allowBlank:false
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "button",
					        					columnWidth : 0.25,
					        					items : [ {
					 								text:'F&#243;rmula',
					        						handler : function() {
					        						mostrarEditorFormula(Ext.getCmp('formula'));
					        						}
					        					} ]
					        				}]

					        	},
								{
									xtype: "radiogroup",
									style: 'margin-left: 25px',
									fieldLabel: "Tipo de Persona",
									labelSeparator:":",
									id:"tipopers",
									vertical:false,
									columns: [200,200],
									items: [
											{boxLabel: "Natural", name: "rbtipestpre", inputValue: 'N'},
											{boxLabel: "Juridica", name: "rbtipestpre", inputValue: 'J', checked: true}
										   ],
										   binding:true,
											hiddenvalue:'',
											defaultvalue:''
												   
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
					        					columnWidth : 0.17,
					        					labelWidth : 60,
					        					items : [ {
					        						xtype : "textfield",
					        						fieldLabel : "Concepto",
					        						name : "concepto de retencion",
					        						id : "codconret",
					        						readOnly:true,
					        						width : 30,
					        						autoCreate : {
					        							tag : 'input',
					        							type : 'text',
					        							size : '3',
					        							autocomplete : 'off',
					        							maxlength : '3'
					        						},
					        						binding:true,
													hiddenvalue:'',
													defaultvalue:''
												} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "button",
					        					id:'botbusqueda',
					        					columnWidth : 0.05,
					        					items : [ {
					        						iconCls : 'menubuscar',
					        						handler : function() {
					        						mostrarCatalogoConceptoRetencion(Ext.getCmp('codconret'),Ext.getCmp('desact'));
					        						}
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "label",
					        					columnWidth : 0.78,
					        					items : [ {
							        						text:'',
															id:"desact",
															cls:"x-form-item"
					        			
					        							} ]
					        				}  ]

					        	}]
			        }]
    		});
     formdeduccion.render("formulario_deduccion");
	}
);

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarCampos();
	Ext.getCmp('dencuenta').setText('');
	Ext.getCmp('desact').setText('');
	var myJSONObject ={
		"operacion":"nuevo"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_deduccion.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codded').setValue(codigo);
		}
	}	
	})
}

function irGuardar()
{
	var mensajeexito = 'Registro <operacion> con &#233;xito';
    var mensajeerror = 'Error al <operacion> registro';
	var cadjson = '';
	if(Actualizar == null)
	{
		cadjson = getItems(formdeduccion,'incluir','N',null,null);
	    mensajeexito = mensajeexito.replace('<operacion>','incluido');
	    mensajeerror = mensajeerror.replace('<operacion>','incluir');
    } 
    else
	{
    	cadjson = getItems(formdeduccion,'actualizar','N',null,null);
    	mensajeexito = mensajeexito.replace('<operacion>','actualizado');
    	mensajeerror = mensajeerror.replace('<operacion>','actualizar');
    }
	try
	{
		var objjson = Ext.util.JSON.decode(cadjson);
		if (typeof(objjson) == 'object')
		{
			obtenerMensaje('procesar','','Guardando Datos');
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_deduccion.php',
			params : parametros,
			method: 'POST',
			success: function ( result, request)
			{ 
				Ext.Msg.hide();
				var datos = result.responseText;
				var codigo = datos.split("|");
				if(codigo[1] == '1'){
						Ext.Msg.show({
							title:'Mensaje',
							msg: mensajeexito,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						    });
						limpiarCampos();
						Ext.getCmp('dencuenta').setText('');
						Ext.getCmp('desact').setText('');
						Actualizar=null;
				}
				else
				{
						Ext.Msg.show({
							title:'Mensaje',
							msg: mensajeerror,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						    });
				}
				
			},
			failure: function (result, request)
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', result.responseText);
			}});
		}
	}	
	catch(e)
	{
	}
}

function irEliminar()
{
	var respuesta;
	
	function respuesta(btn)
	{
	 if(btn=='yes')
	 {
		var cadjson = getItems(formdeduccion,'eliminar','N',null,null);
		try
		{
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object')
			{
					obtenerMensaje('procesar','','Eliminando Datos');
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( result, request) 
					{ 
						var datos = result.responseText;
						var codigo = datos.split("|");
						if(codigo[1] == '1')
						{
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro eliminado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
						else
						{
							if(codigo[1] == '-8')
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'El registro no puede ser eliminado, no puede eliminar registros intermedios',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}
							else
							{
								var respuesta = eval('('+result.responseText+')');
								if(respuesta.mensaje != "")
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'Error al tratar de eliminar el registro <br>'+respuesta.mensaje,
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.ERROR
									}); 
								}
								else
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: 'Error al tratar de eliminar el registro ',
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.ERROR
									});
								}
							}
						}
						limpiarCampos();
						Actualizar=null;
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', result.responseText);
					}})
				}
			}
			catch(e)
			{
			}
	 }
	}
	if(Actualizar)
	{
			var mensajeconfirma = '&#191;Desea eliminar este registro&#63?';
			Ext.MessageBox.confirm('Confirmar', mensajeconfirma, respuesta);
	}
	else
	{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El registro debe estar guardado para poder eliminarlo, verifique por favor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			}); 
	}	

}

function irBuscar()
{
	mostrarCatalogoDeduccion('definicion');
	
};