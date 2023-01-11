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

var ventanaEditorFormula=null;
var formularioEditorFormula= null;
var arregloOperadores = [
                          ['+','+'],
                          ['-','-'],
                          ['*','*'],
                          ['/','/'],
                          ['(','('],
                          [')',')'],
                          ['IIF','IIF']
                          ]; // Arreglo que contiene los Operadores validos para la formula

var dataStoreOperadores = new Ext.data.SimpleStore({
	  fields: ['codigo', 'simbolo'],
	  data : arregloOperadores // Se asocian los operadores disponibles
	});

function agregarCaracterFormula(caracter)
{
	var cadena = '';
	    if(caracter != '')
	    {
	     cadena = Ext.getCmp('ediformula').getValue();
	 	 cadena = cadena + caracter;
	 	 Ext.getCmp('ediformula').setValue(cadena);
	 	 Ext.getCmp('btnasignar').disable();
	    }
	    else
	    {
	    	Ext.Msg.alert('Mensaje','Indique alg&#250;n valor a agregar, verifique por favor');
	    }	
}



function crearFormularioEdicionFormula()
{
		formularioEditorFormula = new Ext.form.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'Editor de F&#243;rmulas',
        bodyStyle:'padding:5px 5px 0',
        width: 601,
		height:150,
		header:false,
		items: [{
		    		layout : "column",
		    		defaults : {
		    			border : false
		    		},
		    		items : [{
		    					layout : "form",
		    					border : false,
		    					columnWidth : 0.50,
		    					labelWidth : 100,
		    					items : [ {
		    						xtype : "textfield",
		    						fieldLabel : "Variable Estatica",
		    						name : "varestatica",
		    						id : "varestatica",
		    						width : 100,
		    						autoCreate : {
		    							tag : 'input',
		    							type : 'text',
		    							size : '10',
		    							autocomplete : 'off',
		    							maxlength : '10'
		    						},
		    						readOnly:true,
		    						value:'$LD_MONTO'
		    					} ]
		    				},
		    				{
		    					layout : "form",
		    					border : false,
		    					defaultType : "button",
		    					columnWidth : 0.50,
		    					items : [ {
		    						iconCls : 'agregar',
		    						handler : function() {
		    							agregarCaracterFormula(Ext.getCmp('varestatica').getValue());
		    						}
		    					} ]
		    				}]
		
		    	},
		    	{
		    		layout : "column",
		    		defaults : {
		    			border : false
		    		},
		    		items : [{
		    					layout : "form",
		    					border : false,
		    					columnWidth : 0.50,
		    					labelWidth : 100,
		    					items : [{
		    						        xtype:"combo",
							                store: dataStoreOperadores,
							                displayField:'simbolo',
							                valueField:'codigo',
											id:"operador",
							                typeAhead: true,
							                mode: 'local',
							                triggerAction: 'all',
							                selectOnFocus:true,
							                fieldLabel:'Operadores',
							           	    listWidth:100,
							           	    editable:false,
							                width:100
				         		    }]
		    				},
		    				{
		    					layout : "form",
		    					border : false,
		    					defaultType : "button",
		    					columnWidth : 0.50,
		    					items : [ {
		    						iconCls : 'agregar',
		    						handler : function() {
		    						
		    						agregarCaracterFormula(Ext.getCmp('operador').getValue());
		    						}
		    					} ]
		    				}]
		
		    	},
		    	{
		    		layout : "column",
		    		defaults : {
		    			border : false
		    		},
		    		items : [{
		    					layout : "form",
		    					border : false,
		    					columnWidth : 0.50,
		    					labelWidth : 100,
		    					items : [ {
		    						xtype : "textfield",
		    						fieldLabel : "Valor",
		    						name : "valor",
		    						id : "constante",
		    						style: 'text-align:right',
		    						width : 100,
		    						autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
		    						readOnly:false,
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
									value:'0,00'
		    						
		    					} ]
		    				},
		    				{
		    					layout : "form",
		    					border : false,
		    					defaultType : "button",
		    					columnWidth : 0.50,
		    					items : [ {
		    						iconCls : 'agregar',
		    						handler : function() {
		    						var numero = formatoNumericoEdicion(Ext.getCmp('constante').getValue());
		    						agregarCaracterFormula(numero);
		    						}
		    					} ]
		    				}]
		
		    	},{
		    		layout : "column",
		    		defaults : {
		    			border : false
		    		},
		    		items : [{
		    					layout : "form",
		    					border : false,
		    					columnWidth : 0.90,
		    					labelWidth : 100,
		    					items : [ {
		    						xtype : "textfield",
		    						fieldLabel : "F&#243;rmula",
		    						name : "ediformula",
		    						id : "ediformula",
		    						width : 390,
		    						autoCreate : {
		    							tag : 'input',
		    							type : 'text',
		    							size : '254',
		    							autocomplete : 'off',
		    							maxlength : '254'
		    						}
		    					} ]
		    				}]
		
		    	},{
		    		layout : "column",
		    		defaults : {
		    			border : false
		    		},
		    		items : [{
		    					layout : "form",
		    					border : false,
		    					columnWidth : 1,
		    					labelWidth : 100,
		    					items : [ {
		    						xtype : "textfield",
		    						fieldLabel : "Valor de prueba",
		    						style: 'text-align:right',
		    						name : "valprueba",
		    						id : "valprueba",
		    						width : 100,
		    						autoCreate : {
		    							tag : 'input',
		    							type : 'text',
		    							size : '10',
		    							autocomplete : 'off',
		    							maxlength : '10'
		    						},
		    						readOnly:false,
		    						value:'0,00',
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
		    					} ]
		    				}]
		
		    	}]
					});				  

}



/***********************************************************************************
* @Función que muestra el editor de Formulas para las deduccion y los Otros Creditos
* @parámetros:  objetoDestino: objeto que va a recibir la formula una vez validada
* @retorno: 
* @fecha de creación: 14/08/2009
* @autor: Ing. Arnaldo Suarez 
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	

function mostrarEditorFormula(objetoDestino)
{
				   crearFormularioEdicionFormula();
				   if(objetoDestino.getValue() != '')
				   {
					  Ext.getCmp('ediformula').setValue(objetoDestino.getValue()); 
				   }
                   ventanaEditorFormula = new Ext.Window(
                   {
                    title: 'Editor de F&#243;rmulas',
		    		autoScroll:true,
                    width:615,
                    height:219,
                    modal: true,
                    plain: false,
                    items:[formularioEditorFormula],
                    buttons: [{
                    text:'Evaluar f&#243;rmula',  
                    handler: function()
                    {         
						if((Ext.getCmp('ediformula').getValue() != "") && (Ext.getCmp('valprueba').getValue() != ""))
						{
						Ext.Ajax.request({
						url : '../../controlador/cfg/sigesp_ctr_cfg_editor_formula.php',
						params : {
							       formula:Ext.getCmp('ediformula').getValue(),
								   monto:  Ext.getCmp('valprueba').getValue()
							     },
						method: 'GET',
						success: function ( resultado, request) 
						{ 
							datos = resultado.responseText;
							var objeto = eval('(' + datos + ')');
							if(objeto.mensaje!='')
							{
								if(objeto.valido)
								{	
									Ext.Msg.show({
										title:'Mensaje',
										msg: objeto.mensaje,
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.INFO,
										fn: function(buttonId,text)
										    {
												if(buttonId=='ok')
												{
													Ext.getCmp('valprueba').setValue('');
												}
										    }
									    });
									Ext.getCmp('btnasignar').enable();
								}
								else
								{
									Ext.Msg.show({
										title:'Mensaje',
										msg: objeto.mensaje,
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.ERROR,
										fn: function(buttonId,text)
										    {
												if(buttonId=='ok')
												{
													Ext.getCmp('valprueba').setValue('');
												}
										    }
									    });
									Ext.getCmp('btnasignar').disable();
								}	
							}
							
						}	
					   })
					   }
					   else
					   {
						  Ext.Msg.show({
									title:'Error',
									msg: 'Debe indicar la f&#243;rmula y el valor de prueba, verifique por favor',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								    }); 
					   }
                    }
                    }
                    ,
                    {
                     text: 'Asignar f&#243;rmula',
                     disabled:true,
                     id:'btnasignar',
                     handler: function()
                     {
                     	if(Ext.getCmp('ediformula').getValue()!='')
                     	{
	                    	objetoDestino.setValue('');
							objetoDestino.setValue(Ext.getCmp('ediformula').getValue());
	                    	formularioEditorFormula.destroy();
			      			ventanaEditorFormula.destroy();
                     	}
                     	else
                     	{
                     		Ext.Msg.show({
								title:'Error',
								msg: 'Debe indicar la f&#243;rmula, verifique por favor',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
							    }); 
                     	}
                     }
                    },
                    {
                        text: 'Cancelar',
                        handler: function()
                        {
                       	formularioEditorFormula.destroy();
   		      			ventanaEditorFormula.destroy();
                        }
                       }]
                    
                   });
                  ventanaEditorFormula.show();       
 }