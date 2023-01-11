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
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var pantalla    		= 'sigesp_vis_cfg_cxp_otroscreditos.php'; 							// Variable que contiene el nombre fÔøΩsico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_cxp_otroscreditos.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= true;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar=null;
var banderaCatalogo = 'generica';
var banderaImprimir = false;
var esIvaPresupuestario = empresa['confiva'] == 'P';
var esIvaContable = empresa['confiva'] == 'C';

var Campos =new Array(
						['codcar','novacio|'],
						['dencar','novacio|'],
						['codestpro',''],
						['porcar','novacio|'],
						['spg_cuenta',''],
						['sc_cuenta',''],
						['estlibcom',''],
						['formula','novacio|'],
						['tipo_iva','novacio|'],
						['estpagele','novacio|'],
						['estcla','']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
    //creando store para el combo tipo iva
	var tipoiva = [ [ 'No Aplica', '0' ],
	                [ 'General', '1' ],
	                [ 'Reducido', '2' ],
	                [ 'Adicional', '3' ]];
	
	var sttipoiva = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tipoiva
	});
	//fin creando store para el combo tipo iva

	//creando objeto combo tipo iva
	var cmbtipoiva = new Ext.form.ComboBox({
		store : sttipoiva,
		fieldLabel : 'Tipo IVA',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'tipo_iva',
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local'
	});
	//fin creando objeto combo tipo iva

    //creando store para el combo tipo alicuota
	var tipoalicuota = [ [ 'No Aplica', '0' ],
	                     [ 'Pagos No Electronicos', '1' ],
	                     [ 'Pagos Electronicos Menores a 2 Mill.', '2' ],
	                     [ 'Pagos Electronicos Mayores a 2 Mill.', '3' ]];
	
	var sttipoalicuota = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tipoalicuota
	});
	//fin creando store para el combo tipo alicuota

	//creando objeto combo tipo alicuota
	var cmbtipoalicuota = new Ext.form.ComboBox({
		store : sttipoalicuota,
		fieldLabel : 'Tipo Alicuota',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'estpagele',
		name: 'Tipo Alicuota',
		width : 250,
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local'
	});
	//fin creando objeto combo tipo alicuota

	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = ((screen.height/2)-(600/2));	
    var formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Otros Cr&#233;ditos",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 700,
    		 height: 300,
    		 labelPad: 10,
    		 items:[{
				        layout:"form",
						border:false,
						style: "margin-top:30px;padding-left:50px;",
						items:[
						       {
							        xtype:"textfield",
							        fieldLabel:"C&#243;digo",
							        labelWidth:40,
							        name:"codigo",
							        id:"codcar",
									autoCreate: {tag: 'input', type: 'text', size: '5', autocomplete: 'off', maxlength: '5'},
							        width:75,
									disabled:true
				        	   },
				        	   {
							        xtype:"textfield",
							        fieldLabel:"Denominaci&#243;n",
							        labelWidth:40,
							        name:"denominacion",
							        id:"dencar",
									autoCreate: {tag: 'input', type: 'text', size: '254', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
							        width:400
					           },
				        	   {
							        xtype:"textfield",
							        fieldLabel:"Porcentaje(%)",
							        labelWidth:40,
							        name:"porcentaje",
							        id:"porcar",
									autoCreate: {tag: 'input', type: 'text', size: '5', autocomplete: 'off', maxlength: '5', onkeypress: "return keyRestrict(event,'0123456789.');"},
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
											},
							        width:50
					           },
					           {
					        		layout : "column",
					        		layoutConfig: {
		        						renderHidden:esIvaContable
					           		},
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
					        						fieldLabel : "Presupuesto",
					        						name : "presupuesto",
					        						id : "spg_cuenta",
					        						width : 150,
					        						readOnly:true,
					        						autoCreate : {
					        							tag : 'input',
					        							type : 'text',
					        							size : '15',
					        							autocomplete : 'off',
					        							maxlength : '15'
					        						}
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					defaultType : "button",
					        					columnWidth : 0.08,
					        					items : [ {
					        						iconCls : 'menubuscar',
					        						handler : function() {
					        						mostrarCatalogoCuentaGasto()
					        						}
					        					} ]
					        				},
					        				{
					        					layout : "form",
					        					border : false,
					        					labelWidth : 50,
					        					defaultType : "textfield",
					        					columnWidth : 0.52,
					        					items : [ {
															id:"codestpro",
															hideLabel:true, 
															style:'border:none;background:#f1f1f1;color:#000000;cursor:text;font-weight: bold;text-aling:left;',
															disabledClass :'',
															disabled:true,
															width: 250,
															autoCreate: {tag: 'input', type: 'text', size: '125', autocomplete: 'off', maxlength: '75'}
					        			
					        							} ]
					        				}  ]

					        	},
					           {
					        		layout : "column",
					        		layoutConfig: {
					        						renderHidden:esIvaPresupuestario
					        	    },
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
					        						name : "cuenta contable",
					        						id : "sc_cuenta",
					        						readOnly:true,
					        						width : 150,
					        						autoCreate : {
					        							tag : 'input',
					        							type : 'text',
					        							size : '15',
					        							autocomplete : 'off',
					        							maxlength : '15'
					        						}
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
									xtype:"checkbox",
					        	   	fieldLabel:"Libro de Compras",
									name:"libro_de_compras",
									id:"estlibcom",
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
					        						}
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
					        	cmbtipoiva,
					        	cmbtipoalicuota,
					        	{
							        xtype:"hidden",
							        name:'codestpro1',
							        id:'codestpro1'
				        	    },
				        	    {
							        xtype:"hidden",
							        name:'codestpro2',
							        id:'codestpro2'
				        	    },
				        	    {
							        xtype:"hidden",
							        name:'codestpro3',
							        id:'codestpro3'
				        	    },
				        	    {
							        xtype:"hidden",
							        name:'codestpro4',
							        id:'codestpro4'
				        	    },
				        	    {
							        xtype:"hidden",
							        name:'codestpro5',
							        id:'codestpro5'
				        	    },
					        	{
							        xtype:"hidden",
							        name:'estcla',
							        id:'estcla'
				        	    }]
			        }]
    		});
     formulario.render("formulario_otrocredito");
});


function irNuevo()
{
	limpiarCampos();
	Actualizar= null;
	Ext.getCmp('codestpro').setValue('');
	var myJSONObject = {"oper":"nuevo"};
	var ObjSon       = Ext.util.JSON.encode(myJSONObject);
	var parametros   = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_otroscreditos.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request){ 
					var datos = result.responseText;
					var codigo = eval('(' + datos + ')');
					if(codigo != "")
					{
						Ext.getCmp('codcar').setValue(codigo);
					}
		}	
	});
}

function irCancelar()
{
	irNuevo();	
}

function validarCamposVacios()
{
	var ok = true;
	for(var j=0;((j<Campos.length)&&(ok));j++)
	{
		var objeto   = document.getElementById(Campos[j][0]);
		var valores = Campos[j][1].split('|');
		for (var i=0;i<valores.length;i++)
		{
			switch(valores[i])
			{
				case 'novacio':
					if ((objeto.value=='') || (objeto.value=='Seleccione'))
					{
						Ext.MessageBox.alert('Campo Vacio', 'Debe llenar el campo '+objeto.name);
						return false;
					}
					break;
				
				case '':
					if ((objeto.value=='') || (objeto.value=='Seleccione'))
					{
						if((objeto.id =='sc_cuenta')&&(esIvaContable))
						{
							Ext.MessageBox.alert('Campo Vacio', 'Debe llenar el campo cuenta contable');
							return false;
						}
						else
						{
							if((objeto.id =='spg_cuenta')&&(esIvaPresupuestario))
							{
									Ext.MessageBox.alert('Campo Vacio', 'Debe llenar el campo cuenta presupuestaria');
									return false;
							}
						}
					}
				break;
				
			}
		}
	}
	return ok;
}


function irGuardar()
{
	if(validarCamposVacios())
	{
		var estLibroCompra = 0;
		var arregloObjeto = "";
		var jsonOtroCredito = "";
		var ObjSon="";
		var cuenta="";
		var estructura = Ext.getCmp('codestpro1').getValue()+Ext.getCmp('codestpro2').getValue()+Ext.getCmp('codestpro3').getValue()+Ext.getCmp('codestpro4').getValue()+Ext.getCmp('codestpro5').getValue();
	
		if(Ext.getCmp('estlibcom').checked){
			estLibroCompra = 1;
		}
	
		if(esIvaPresupuestario){
			cuenta = Ext.getCmp('spg_cuenta').getValue();
		}
		else if (esIvaContable){
			cuenta = Ext.getCmp('sc_cuenta').getValue();
		}
		obtenerMensaje('procesar','','Guardando Datos');
	    if(Actualizar == null)
		{
	    	arregloObjeto = "{'oper':'incluir','codcar':'"+Ext.getCmp('codcar').getValue()+"','dencar':'"+Ext.getCmp('dencar').getValue()+"'"+ 
			                    ",'codestpro':'"+estructura+"','spg_cuenta':'"+cuenta+"', 'porcar':"+formatoNumericoEdicion(Ext.getCmp('porcar').getValue())+
			                    ",'estlibcom':"+estLibroCompra+", 'formula':'"+Ext.getCmp('formula').getValue()+"',estcla:'"+Ext.getCmp('estcla').getValue()+
								"',tipo_iva:'"+Ext.getCmp('tipo_iva').getValue()+"',estpagele:'"+Ext.getCmp('estpagele').getValue()+"',codmenu:'"+codmenu+"'}";
		    jsonOtroCredito = eval('(' + arregloObjeto + ')');
			ObjSon=Ext.util.JSON.encode(jsonOtroCredito);
			parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( resultado, request )
				{ 
					Ext.Msg.hide();
					var datos = resultado.responseText;
					var registro = datos.split("|");
					if(trim(registro[1])=='1')
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Registro incluido con &#233;xito',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						limpiarCampos();									
					}
					else
					{
						Ext.MessageBox.alert('Mensaje','Ha ocurrido un error en el proceso de registro');				
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
			arregloObjeto = "{'oper':'actualizar','codcar':'"+Ext.getCmp('codcar').getValue()+"','dencar':'"+Ext.getCmp('dencar').getValue()+"'"+ 
			                    ",'codestpro':'"+estructura+"','spg_cuenta':'"+cuenta+"', 'porcar':"+formatoNumericoEdicion(Ext.getCmp('porcar').getValue())+
			                    ",'estlibcom':"+estLibroCompra+", 'formula':'"+Ext.getCmp('formula').getValue()+"',estcla:'"+Ext.getCmp('estcla').getValue()+
								"',tipo_iva:'"+Ext.getCmp('tipo_iva').getValue()+"',estpagele:'"+Ext.getCmp('estpagele').getValue()+"',codmenu:'"+codmenu+"'}";

		    jsonOtroCredito = eval('(' + arregloObjeto + ')');
			ObjSon=Ext.util.JSON.encode(jsonOtroCredito);
			parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
				url : ruta,
				params : parametros,
				method: 'POST',
				success: function ( resultado, request )
				{ 
					Ext.Msg.hide();
					datos = resultado.responseText;
					var registro = datos.split("|");
					if(registro[1]=='1'){
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Registro actualizado con &#233;xito',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						limpiarCampos();									
					}
					else
					{
						Ext.MessageBox.alert('Mensaje','Ha ocurrido un error en el proceso de registro');				
					}
				},
				failure: function ( result, request)
				{ 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
				} 
			});
		}
	}
}

function ejecutarEliminacion(btn)
{
    var estLibroCompra = 0;
	var arregloObjeto = "";
	var jsonOtroCredito = "";
	var ObjSon="";
	var cuenta = "";
	var estructura = Ext.getCmp('codestpro1').getValue()+Ext.getCmp('codestpro2').getValue()+Ext.getCmp('codestpro3').getValue()+Ext.getCmp('codestpro4').getValue()+Ext.getCmp('codestpro5').getValue();
	if(esIvaPresupuestario)
	{
		cuenta = Ext.getCmp('spg_cuenta').getValue();
	}
	else if (esIvaContable)
	{
		cuenta = Ext.getCmp('sc_cuenta').getValue();
	}
	if(Ext.getCmp('estlibcom').checked)
	{
		estLibroCompra = 1;
	}
	if(btn=='yes')
	{
		 arregloObjeto = "{'oper':'eliminar','codcar':'"+Ext.getCmp('codcar').getValue()+"','dencar':'"+Ext.getCmp('dencar').getValue()+"'"+ 
		 ",'codestpro':'"+estructura+"','spg_cuenta':'"+cuenta+"', 'porcar':"+formatoNumericoEdicion(Ext.getCmp('porcar').getValue())+
		 ",'estlibcom':"+estLibroCompra+", 'formula':'"+Ext.getCmp('formula').getValue()+"',estcla:'"+Ext.getCmp('estcla').getValue()+"',codmenu:'"+codmenu+"'}";
	
		jsonOtroCredito = eval('(' + arregloObjeto + ')');
		ObjSon=Ext.util.JSON.encode(jsonOtroCredito);
		obtenerMensaje('procesar','','Eliminando Datos');		
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request )
		{ 
			Ext.Msg.hide();
			datos = resultado.responseText;
			var registro = datos.split("|");
			if(registro[1]=='1')
			{
				Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro eliminado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
				limpiarCampos();
				Actualizar=null;
			}
			else
			{
				if(registro[1] == '-8')
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
					var respuesta = eval('('+resultado.responseText+')');
					if(respuesta.mensaje != null)
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: respuesta.mensaje[0],
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
					}
					else
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Ha ocurrido un error en el proceso de eliminaci&#243;n',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});	
					}
				}
			}
		},
		failure: function ( result, request)
		{ 
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
		} 
		})
	}
}

function irEliminar()
{
	if((Actualizar != null)&&(Ext.getCmp('codcar') != ""))
	{
		Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', ejecutarEliminacion);	
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
	mostrarCatalogoOtroCredito('definicion');
	
};
