/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var fromReporteModPresupuestarias = null; //varibale para almacenar la instacia de objeto de formulario
barraherramienta = true;
var comprobante = "";
var comprobanteProc ="";
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//--------------------------------------------------------------------------------------------
	//creacion del boton que carga el catalodo de comprobantes
	var botbuscarComprobante = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:460px;top:10px',
		listeners:{
	        'click' : function(boton){
	        	ventanaCatalogoComprobante()
	       }
	    }
	});	

	//--------------------------------------------------------------------------------------------

	//formulario del estado de modificaciones presupuestarias
	fieldset = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Estado de Modificaciones Presupuestarias',
		style: 'position:absolute;left:160px;top:5px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:5px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,230],
								id:'rdEstado',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [
								        {boxLabel: 'Aprobadas', name: 'tipo',inputValue: '1',checked:true},
								        {boxLabel: 'No Aprobadas', name: 'tipo', inputValue: '0'}
								        ]
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	//formulario de las modificaciones presupuestarias
	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Modificaciones Presupuestarias',
		style: 'position:absolute;left:160px;top:65px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:5px;top:5px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								boxLabel: 'Rectificaciones',
								id: 'chkRectificaciones',
								inputValue:1,
								binding:true,
								checked : true,
								hiddenvalue:'',
								defaultvalue:'0',
								allowBlank:true,
								width: 500
								},
								{
								layout: "form",
								border: false,
								labelWidth: 100,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										boxLabel: 'Traspaso',
										id: 'chkTraspaso',
										inputValue:1,
										binding:true,
										checked : true,
										hiddenvalue:'',
										defaultvalue:'0',
										allowBlank:true,
										width: 500
									}]
								}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:230px;top:5px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								boxLabel: 'Insubsistencias',
								id: 'chkInsubsistencias',
								inputValue:1,
								binding:true,
								checked : true,
								hiddenvalue:'',
								defaultvalue:'0',
								allowBlank:true,
								width: 500
								},
								{
								layout: "form",
								border: false,
								labelWidth: 100,
								items: [{
										xtype: 'checkbox',
										labelSeparator :'',
										boxLabel: 'Cr&#233;ditos Adicionales',
										id: 'chkCredito',
										inputValue:1,
										binding:true,
										checked : true,
										hiddenvalue:'',
										defaultvalue:'0',
										allowBlank:true,
										width: 500
									}]
								}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	//formulario de fechas
	fieldsetcuatro = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Intervalo de Fechas',
		style: 'position:absolute;left:160px;top:135px',
		cls :'fondo',
		items: [{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:80px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Desde",
								name:'Desde',
								id:'dtFechaDesde',
								allowBlank:true,
								width:100,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value: '01/01/'+fecha.getFullYear(),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				},
				{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:300px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Hasta",
								name:'Hasta',
								id:'dtFechaHasta',
								allowBlank:true,
								width:100,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value:  new Date().format('d-m-Y'),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	//formulario de comprobante
	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Comprobante',
		style: 'position:absolute;left:160px;top:195px',
		cls :'fondo',
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 35,
						items: [{
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: '',
								id: 'txtComprobante',
								disabled:true,
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:165px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 30,
						items: [{
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: '',
								id: 'txtComprobanteProcedencia',
								disabled:true,
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:320px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 30,
						items: [{
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: '',
								id: 'txtComprobanteFecha',
								disabled:true,
								width:100,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},botbuscarComprobante]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(450)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteModPresupuestarias = new Ext.FormPanel({
		applyTo: 'formReporteModificacionesPresupuestarias',
		width:850, //700
		height: 320,
		title: "<H1 align='center'>Modificaciones Presupuestarias</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;', 
		items: [fieldset,fieldsetdos,fieldsettres,fieldsetcuatro]
	});	
	fromReporteModPresupuestarias.doLayout();
});	

//------------------------------------------------------------------------------------------------------------

function ventanaCatalogoComprobante()
{	
	//Datos del tipo de procedencia
	var opcproc = [ [ 'Seleccione', '' ],
				   [ 'Traspaso', 'SPGTRA' ], 
	               [ 'Rectificaciones', 'SPGREC' ], 
	               [ 'Insubsistencia', 'SPGINS' ], 
	               [ 'Cr�dito Adicional', 'SPGCRA' ]];

	var stOpcProc = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcproc
	});
	
	//Creando datastore y columnmodel para el catalogo de comprobante
	var reVentana = Ext.data.Record.create([
	          {name: 'comprobante'}, 
	          {name: 'descripcion'},
	          {name: 'procede'},
	          {name: 'fecha'}
	]);
		                                        	
	var dsVentana =  new Ext.data.Store({
	         reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reVentana)
	});
		                                						
	var cmVentana = new Ext.grid.ColumnModel([ 
	         new Ext.grid.CheckboxSelectionModel,
		     {header: "<H1 align='center'>Comprobante</H1>", width: 30, sortable: true, dataIndex: 'comprobante'},
		     {header: "<H1 align='center'>Descripci&#243;n</H1>", width: 85, sortable: true, dataIndex: 'descripcion'},
		     {header: "<H1 align='center'>Procede</H1>", width: 20, sortable: true, dataIndex: 'procede'},
		     {header: "<H1 align='center'>Fecha</H1>", width: 20, sortable: true, dataIndex: 'fecha'},
    ]);
		 
	//Creando grid para el catalogo de comprobante
	var gridVentanaCatalogoComprobante = new Ext.grid.GridPanel({
		width:630,
		height:230,
		frame:true,
		style: 'position:absolute;left:10px;top:120px',
		autoScroll:true,
		border:true,
		ds: dsVentana,
		cm: cmVentana,
		sm: new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
		
	var	formVentanaCatalogo = new Ext.FormPanel({
		width: 690,
		height: 410,
		style: 'position:absolute;left:5px;top:10px',
		frame: true,
		autoScroll:false,
		items: [{
				xtype:"fieldset", 
				title:'Cat&#225;logo de Comprobantes',
				style: 'position:absolute;left:15px;top:10px',
				border:true,
				cls: 'fondo',
				width: 650,
				height: 380,
				items:[{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:40px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 90,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Comprobante',
										id: 'txtcmp',									
										width: 150,
										autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"},
										changeCheck: function(){
											var textvalor = this.getValue();
											dsVentana.filter('txtcodigo',textvalor,true);
											if(String(textvalor) !== String(this.startValue)){
												this.fireEvent('change', this, textvalor, this.startValue);
											} 
										}, 
										initEvents: function(){
											AgregarKeyPress(this);
										}
									}]
								}]
						},
						{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:350px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 90,
								items: [{
										xtype: 'combo',
										fieldLabel: 'Procedencia',
										labelSeparator :'',
										id: 'cmbprocede',
										editable : false,
										displayField : 'col',
										valueField : 'tipo',
										typeAhead : true,
										triggerAction : 'all',
										width: 150,
										mode : 'local',
										emptyText:'Seleccione',
										store : stOpcProc,
										changeCheck: function(){
											var textvalor = this.getValue();
											dsVentana.filter('tipbieser',textvalor,true);
											if(String(textvalor) !== String(this.startValue)){
												this.fireEvent('change', this, textvalor, this.startValue);
											} 
										}, 
										initEvents: function(){
											AgregarKeyPress(this);
										}
									}]
								}]
						},
						{
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:40px;top:60px',
						border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:90,
								items:[{
										xtype:"datefield",
										labelSeparator :'',
										fieldLabel:"Desde",
										name:'Desde',
										id:'dtFechaInicial',
										allowBlank:true,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										allowBlank:false,
										value: '01/01/'+fecha.getFullYear(),
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
								}]
						},
						{
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:350px;top:60px',
						border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:90,
								items:[{
										xtype:"datefield",
										labelSeparator :'',
										fieldLabel:"Hasta",
										name:'Hsta',
										id:'dtFechaFinal',
										allowBlank:true,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										allowBlank:false,
										value: new Date().format('d-m-Y'),
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
								}]
						},
						{
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:525px;top:95px', 
						border:false,
						items:[{
								layout:"form",
								border:false,
								items:[{
										xtype: 'button',
										fieldLabel: '',
										id: 'btagregar',
										text: 'Buscar',
										iconCls: 'menubuscar',
										handler: function()
										{
											if(Ext.getCmp('dtFechaInicial').getValue()>Ext.getCmp('dtFechaFinal').getValue())
											{
							    				Ext.MessageBox.show({
								 					title:'Advertencia',
								 					msg:'El rango de fechas no es valido!!!',
								 					buttons: Ext.Msg.OK,
								 					icon: Ext.MessageBox.WARNING
								 				});
							    			}
											else
											{
												obtenerMensaje('procesar','','Buscando Datos');
						   						//Buscar comprobante
												gridVentanaCatalogoComprobante.store.removeAll();
												
												if (Ext.getCmp('rdEstado').items.items[0].checked)
												{
													estapro=1;
												}
												else if(Ext.getCmp('rdEstado').items.items[1].checked)
												{
													estapro=0;
												}
								   				var JSONObject = {
													'operacion'     : 'buscarModificacionesPresupuestarias',
													'comprobante'   : Ext.getCmp('txtcmp').getValue(),
													'procede'		: Ext.getCmp('cmbprocede').getValue(),
													'fecdesde'		: Ext.getCmp('dtFechaInicial').getValue().format('d/m/Y'),
													'fechasta'		: Ext.getCmp('dtFechaFinal').getValue().format('d/m/Y'),
													'fechasta'		: Ext.getCmp('dtFechaFinal').getValue().format('d/m/Y'),
													'estapro'		: estapro,
									   				'filtro'        : 'MODIFICACIONES'
								   				}
								   				var ObjSon = JSON.stringify(JSONObject);
								   				var parametros = 'ObjSon='+ObjSon; 
								   				Ext.Ajax.request({
								   					url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
								   					params : parametros,
								   					method: 'POST',
								   					success: function ( resultado, request)
													{
								   						Ext.Msg.hide();
								   						var datos = resultado.responseText;
								   						var objOrdCom = eval('(' + datos + ')');
								   						if(objOrdCom!='')
														{
															if(objOrdCom!='0')
															{
								   								if(objOrdCom.raiz == null || objOrdCom.raiz =='')
																{
								   									Ext.MessageBox.show({
													 					title:'Advertencia',
													 					msg:'No existen datos para mostrar',
													 					buttons: Ext.Msg.OK,
													 					icon: Ext.MessageBox.WARNING
													 				});
																}
																else
																{
								   									gridVentanaCatalogoComprobante.store.loadData(objOrdCom);
																}
								   							}
								   							else{
								   								Ext.MessageBox.show({
													 				title:'Advertencia',
													 				msg:'',
													 				buttons: Ext.Msg.OK,
													 				icon: Ext.MessageBox.WARNING
													 			});
								   							}
								   						}
								   					}//fin del success	
								   				});//fin del ajax request
											}
										}
									}]
								}]
							},gridVentanaCatalogoComprobante]
						}]
					});
					
    var ventanaEstructura = new Ext.Window({
    	width:700, 
        height:480,
    	border:false,
    	modal: true,
    	closable:false,
    	frame:true,
    	title:"<H1 align='center'>Cat&#225;logo de Comprobantes</H1>",
    	items:[formVentanaCatalogo],
    	buttons:[{
       			text:'Aceptar',  
       	        handler: function(){
    				var registro = gridVentanaCatalogoComprobante.getSelectionModel().getSelected();	        	
    				if(registro!= undefined){
						Ext.getCmp('txtComprobante').setValue(registro.get('comprobante'));
						Ext.getCmp('txtComprobanteProcedencia').setValue(registro.get('procede'));
						Ext.getCmp('txtComprobanteFecha').setValue(registro.get('fecha'));
						gridVentanaCatalogoComprobante.destroy();
						ventanaEstructura.destroy();
    				}
    				else{
    					Ext.MessageBox.show({
    						title:'Mensaje',
    						msg:'Debe seleccionar al menos un registro a procesar',
    						buttons: Ext.Msg.OK,
    						icon: Ext.MessageBox.INFO
    					});
    				}
    			}
    			},
    			{
    			text: 'Salir',
    			handler: function(){
    				ventanaEstructura.destroy();
    			}
    			}] 	
    });
    ventanaEstructura.show();			
}

function irBuscarFormato(variable,nombreArchivo,ruta,datos)
{
	var myJSONObject =
	{
			'operacion'   : 'buscarFormato',
			'sistema'	  : 'SPG',
			'seccion'     : 'REPORTE',
			'variable'    : variable,
			'valor'		  : nombreArchivo,
			'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			nombreArchivo = result.responseText;	
			pagina=ruta+nombreArchivo+datos;
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		},
		failure: function (result, request)
		{ 
			pagina=ruta+nombreArchivo+datos;
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	})
}

function irImprimir()
{
	var rectificaciones= 0;
	var traspaso= 0;
	var insubsistencias= 0;
	var credito= 0;
	var valido = true;
	var cmp = Ext.getCmp('txtComprobante').getValue();
	var cmpFecha = Ext.getCmp('txtComprobanteFecha').getValue();
	var cmpProcedencia = Ext.getCmp('txtComprobanteProcedencia').getValue();
	var fechaReporteDesde = Ext.getCmp('dtFechaDesde').getValue().format('d/m/Y');
	var fechaReporteHasta = Ext.getCmp('dtFechaHasta').getValue().format('d/m/Y');
	var aprobada = true;

	if(Ext.getCmp('chkRectificaciones').checked){
		rectificaciones=1;
	}
	if(Ext.getCmp('chkTraspaso').checked){
		traspaso=1;
	}
	if(Ext.getCmp('chkInsubsistencias').checked){
		insubsistencias=1;
	}
	if(Ext.getCmp('chkCredito').checked){
		credito=1;
	}
	if(fechaReporteDesde > fechaReporteHasta)
	{
		alert('Debe verificar el intervalo de fechas seleccionado');
		valido = false;
	}
	if(valido)
	{
		var pagina = '';
		var datos = "?ckbrect="+rectificaciones+"&txtcomprobante="+cmp+"&txtfecha="+cmpFecha
		           +"&txtprocede="+cmpProcedencia+"&ckbtras="+traspaso+"&ckbinsu="+insubsistencias
		           +"&ckbcre="+credito+"&txtfecdes="+fechaReporteDesde+"&txtfechas="+fechaReporteHasta;
		if (Ext.getCmp('rdEstado').items.items[0].checked)
		{
			irBuscarFormato('MODIFICACION_PRESUPUESTARIA_APROBADA','sigesp_spg_rpp_modificaciones_presupuestarias_aprobadas.php',"reportes/",datos);
		}          
		else if(Ext.getCmp('rdEstado').items.items[1].checked)
		{
			irBuscarFormato('MODIFICACION_PRESUPUESTARIA_NO_APROBADA','sigesp_spg_rpp_modificaciones_presupuestarias_no_aprobadas.php',"reportes/",datos);
		}
	}
}