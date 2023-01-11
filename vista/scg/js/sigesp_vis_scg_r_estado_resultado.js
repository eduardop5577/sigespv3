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

var frmPagos = null;  //instancia del formulario de pagos
var Actualizar = null;
var ruta ='../../controlador/scg/sigesp_ctr_scg_estado_resultado.php'; //ruta del controlador
var ruta2 ='../../controlador/scg/sigesp_ctr_scg_balance_comprobacion.php'; //ruta del controlador
var fechaPrimera = obtenerPrimerDiaMes();
var formato1 = '';
var formatoExcel = '';
var cencos = '';

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                     		INICIO DEL FORMULARIO PROVEEDOR / BENEFICIARIO	
//**********************************************************************************************************************************
// Combo del A�o
var anio = Ext.data.Record.create([
	    {name:'anuales'}
	]);

	var stanio =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "anuales"},anio)
	});
	
	var staniodes =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "anuales"},anio)
	});
	
	var staniohas =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "anuales"},anio)
	});
	
	//creando objeto combo filtrar
	var cmbanio = new Ext.form.ComboBox({
		store : stanio,
		fieldLabel : 'A&#241;o',
		labelSeparator : '',
		editable : false,
		displayField : 'anuales',
		valueField : 'anuales',
		id : 'anio',
		width : 90,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		emptyText:'Seleccione',
		mode : 'local'
	});
	
	//creando objeto combo filtrar
	var cmbaniodes = new Ext.form.ComboBox({
		store : staniodes,
		fieldLabel : '',
		labelSeparator : '',
		editable : false,
		displayField : 'anuales',
		valueField : 'anuales',
		id : 'aniod',
		width : 90,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		emptyText:'Seleccione',
		mode : 'local'
	});
	
	//creando objeto combo filtrar
	var cmbaniohas = new Ext.form.ComboBox({
		store : staniohas,
		fieldLabel : '',
		labelSeparator : '',
		editable : false,
		displayField : 'anuales',
		valueField : 'anuales',
		id : 'anioh',
		width : 90,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		emptyText:'Seleccione',
		mode : 'local'
	});
// Combo del A�o
//--------------------------------------------------------------------------------------------------------------------------------	
// Combo de nivel
var nivel = [ 
				 [ '1', '01' ], 
	             [ '2', '02' ],
				 [ '3', '03' ],
				 [ '4', '04' ],
				 [ '5', '05' ],
				 [ '6', '06' ],
				 [ '7', '07' ],
				 [ '8', '08' ],
				 [ '9', '09' ],
				 [ '10', '10' ],
			];
	
	var stnivel = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : nivel
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbnivel = new Ext.form.ComboBox({
		store : stnivel,
		fieldLabel : 'Nivel',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'cmbnivel',
		width : 50,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbnivel.setValue('01');

//--------------------------------------------------------------------------------------------------------------------------------	
// Combo mes desde (MES)
var mesdesde = [ 
				 [ 'Enero', '01' ], 
	             [ 'Febrero', '02' ],
				 [ 'Marzo', '03' ],
				 [ 'Abril', '04' ],
				 [ 'Mayo', '05' ],
				 [ 'Junio', '06' ],
				 [ 'Julio', '07' ],
				 [ 'Agosto', '08' ],
				 [ 'Septiembre', '09' ],
				 [ 'Octubre', '10' ],
				 [ 'Noviembre', '11' ],
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmesdesde = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mesdesde
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmesdesde = new Ext.form.ComboBox({
		store : stmesdesde,
		fieldLabel : 'Desde',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'mesdes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
	
	//cmbtiporeporte.setValue('P');
//--------------------------------------------------------------------------------------------------------------------------------		
// Combo mes hasta (MES)
var meshasta = [ 
				 [ 'Enero', '01' ], 
	             [ 'Febrero', '02' ],
				 [ 'Marzo', '03' ],
				 [ 'Abril', '04' ],
				 [ 'Mayo', '05' ],
				 [ 'Junio', '06' ],
				 [ 'Julio', '07' ],
				 [ 'Agosto', '08' ],
				 [ 'Septiembre', '09' ],
				 [ 'Octubre', '10' ],
				 [ 'Noviembre', '11' ],
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmeshasta = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : meshasta
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmeshasta = new Ext.form.ComboBox({
		store : stmeshasta,
		fieldLabel : 'Hasta',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'meshas',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
	
	//cmbtiporeporte.setValue('P');
//--------------------------------------------------------------------------------------------------------------------------------	
	var re_cencos = Ext.data.Record.create([
  		{name: 'codcencos'},
  		{name: 'denominacion'}
  	]);
  	
  	var dscencos=  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},re_cencos)
  	});
  						
  	var cmcatcencos = new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcencos'},
          {header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
  	]);
  	//componente campocatalogo para el campo cuentas contables
  	
  	comcampocatcencosdes = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Centro de Costos Contable</H1>",
  			anchoformbus: 450,
  			altoformbus:100,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dscencos,
  			colmodelocat: cmcatcencos,
  			rutacontrolador:ruta2,
  			parametros: "ObjSon={'operacion': 'buscarCentroCostos'}",
  			arrfiltro:[{etiqueta:'Codigo',id:'codicencos',valor:'codcencos',longitud:'10'},
  					   {etiqueta:'Denominaci&#243;n',id:'denominacioni',valor:'denominacion'}],
  			posicion:'position:absolute;left:15px;top:10px',
  			tittxt:'Desde',
  			idtxt:'codcencosdes',
  			campovalue:'codcencos',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'denominaciond',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarCentroCosContable,
			msjValidarMostrar: 'Debe estar configurado para trabajar con Centro de Costos'
  	});

  	comcampocatcencoshas = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Centro de Costos Contable</H1>",
  			anchoformbus: 450,
  			altoformbus:100,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dscencos,
  			colmodelocat: cmcatcencos,
  			rutacontrolador:ruta2,
  			parametros: "ObjSon={'operacion': 'buscarCentroCostos'}",
  			arrfiltro:[{etiqueta:'Codigo',id:'codicencos',valor:'codcencos',longitud:'10'},
  					   {etiqueta:'Denominaci&#243;n',id:'denominacioni',valor:'denominacion'}],
  			posicion:'position:absolute;left:390px;top:10px',
  			tittxt:'Hasta',
  			idtxt:'codcencoshas',
  			campovalue:'codcencos',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'denominacionh',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarCentroCosContable,
			msjValidarMostrar: 'Debe estar configurado para trabajar con Centro de Costos'
  	});
//--------------------------------------------------------------------------------------------------------------------------------		
//creacion del formulario de datos de estado resultado
	
		fieldsetPeriodos = new Ext.form.FieldSet({
		   	title:"Periodos",
			style: 'position:absolute;left:10px;top:15px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 90,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:50px;top:45px',
					items: [{
							layout: "form",
							border: false,
							items:[{
									xtype: "radiogroup",
									fieldLabel: "",
									labelSeparator:"",
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',	
									columns: [190,160,160],
									id:'hidbot',
									items: [
											{
											boxLabel: 'Trimestres', name: 'intervalp',inputValue: '3',checked:true,
											listeners:{		
														'check': function (checkbox, checked) 
														{
															if(checked)
															{
																Ext.getCmp('mesdes').reset();
																Ext.getCmp('meshas').reset();
																Ext.getCmp('aniod').reset();
																Ext.getCmp('anioh').reset();
																Ext.getCmp('mesdes').disable();
																Ext.getCmp('meshas').disable();
																Ext.getCmp('aniod').disable();
																Ext.getCmp('anioh').disable();
																Ext.getCmp('fecdesde').reset();
																Ext.getCmp('fechasta').reset();
																Ext.getCmp('fecdesde').disable();
																Ext.getCmp('fechasta').disable();
																Ext.getCmp('intervaltri').enable();
																Ext.getCmp('intervaltri').reset();
																Ext.getCmp('anio').enable();
																Ext.getCmp('anio').reset();
															}
														}
												 }
											},
											{
											boxLabel: 'Meses', name: 'intervalp', inputValue: '1',
											listeners:{		
														'check': function (checkbox, checked) 
														{
															if(checked)
															{
																Ext.getCmp('mesdes').reset();
																Ext.getCmp('mesdes').enable();
																Ext.getCmp('meshas').reset();
																Ext.getCmp('meshas').enable();
																Ext.getCmp('aniod').reset();
																Ext.getCmp('aniod').enable();
																Ext.getCmp('anioh').reset();
																Ext.getCmp('anioh').enable();
																Ext.getCmp('intervaltri').disable();
																Ext.getCmp('anio').disable();
																Ext.getCmp('fecdesde').reset();
																Ext.getCmp('fechasta').reset();
																Ext.getCmp('fecdesde').disable();
																Ext.getCmp('fechasta').disable();
															}
														}
													}
											},
											{
											boxLabel: 'Dias', name: 'intervalp', inputValue: '2',
											listeners:{		
														'check': function (checkbox, checked) 
														{
															if(checked)
															{
																Ext.getCmp('mesdes').reset();
																Ext.getCmp('meshas').reset();
																Ext.getCmp('aniod').reset();
																Ext.getCmp('anioh').reset();
																Ext.getCmp('mesdes').disable();
																Ext.getCmp('meshas').disable();
																Ext.getCmp('aniod').disable();
																Ext.getCmp('anioh').disable();
																Ext.getCmp('fecdesde').enable();
																Ext.getCmp('fechasta').enable();
																Ext.getCmp('fecdesde').reset();
																Ext.getCmp('fechasta').reset();
																Ext.getCmp('intervaltri').disable();
																Ext.getCmp('anio').disable();
																Ext.getCmp('intervaltri').reset();
																Ext.getCmp('anio').reset();
															}
														}
													}
											}]
									}]
							}]
					}]

	});
//----------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO INTERVALO FECHAS
//**********************************************************************************************************************************
	//creacion del formulario de datos de intervalo de fechas
	fieldsetIntervaloFechasTrimestres = new Ext.form.FieldSet({
		title:"Intervalo de Fechas Trimestrales",
		style: 'position:absolute;left:10px;top:115px',
		border:true,
		width: 705,
		cls :'fondo',
		height: 100,						
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:0px;top:15px',
				items: [{
						layout: "form",
						border: false,
						items:[{
								xtype: "radiogroup",
								fieldLabel: "",
								labelSeparator:"",
								binding:true,
								width: 700,
								hiddenvalue:'',
								defaultvalue:'',	
								columns: [170,170,170,170],
								id:'intervaltri',
								items: [
										{boxLabel: 'Trim. 1', name: 'intervalt', inputValue: '1',checked:true},
										{boxLabel: 'Trim. 2', name: 'intervalt', inputValue: '2'},
										{boxLabel: 'Trim. 3', name: 'intervalt', inputValue: '3'},
										{boxLabel: 'Trim. 4', name: 'intervalt', inputValue: '4'}
										]
							}]
						}]
				},
				{
				style:'position:absolute;left:250px;top:55px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [cmbanio]
						}]
				}]

	});			
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO COMPROBANTES
//**********************************************************************************************************************************
//creacion del formulario de datos de intervalo de fechas
	
		fieldsetIntervaloFechasMeses = new Ext.form.FieldSet({
			title:"Intervalo de Fechas Mensuales",
			style: 'position:absolute;left:10px;top:230px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbmesdesde]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:145px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbaniodes]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:400px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbmeshasta]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:525px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbaniohas]
							}]
					}]

	});	
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO PROCEDE
//**********************************************************************************************************************************
//creacion del formulario de datos de intervalo de fechas
		fieldsetIntervaloFechasDias = new Ext.form.FieldSet({
			title:"Intervalo de Fechas en Dias",
			style: 'position:absolute;left:10px;top:315px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 75,
			items: [{
					style:'position:absolute;left:25px;top:20px',
					layout:"column",
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [{
									 xtype:"datefield",
									 labelSeparator:'',
									 fieldLabel:'Desde',
									 name:'fechadesde',
									 id:'fecdesde',
									 value: fechaPrimera,
									 width:150,
									 binding:true,
									 hiddenvalue:'',
									 defaultvalue:'1900-01-01',
									 allowBlank:true,
									 autoCreate:{tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
						}]
					},
					{
					style:'position:absolute;left:400px;top:25px',
					layout:"column",
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [{
									 xtype:"datefield",
									 labelSeparator:'',
									 fieldLabel:'Hasta',
									 name:'fechahasta',
									 id:'fechasta',
									 value: new Date().format('Y-m-d'),
									 width:150,
									 binding:true,
									 hiddenvalue:'',
									 defaultvalue:'1900-01-01',
									 allowBlank:true,
									 autoCreate:{tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
							}]
					}]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO ORDENADO POR
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos
		fieldsetNivelCuenta  = new Ext.form.FieldSet({
			title:"Nivel de las Cuentas",
			style: 'position:absolute;left:10px;top:490px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 65,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:260px;top:12px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbnivel]
							}]
					}]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//creacion del formulario de datos de estado resultado
		fieldsetGraficos  = new Ext.form.FieldSet({
			title:"Graficos",
			style: 'position:absolute;left:10px;top:565px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 70,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:120px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,
							items:[{
									xtype: "radiogroup",
									fieldLabel: "",
									labelSeparator:"",
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',	
									columns: [170,170],
									id:'graf',
									items: [
											{boxLabel: 'Torta', name: 'graf1',inputValue: '1',checked:true},
											{boxLabel: 'Barras', name: 'graf1', inputValue: '2'}
											]
								}]
							}]
					}]
	});
//----------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO CENTRO DE COSTOS
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos
	
		fieldsetIntervaloCencos = new Ext.form.FieldSet({
	    	title:"Centros de Costos",
			style: 'position:absolute;left:10px;top:400px',
			border:true,
			width: 705,
			cls :'fondo',
			height: 80,
			items: [comcampocatcencosdes.fieldsetCatalogo,
					comcampocatcencoshas.fieldsetCatalogo]

	});	
//----------------------------------------------------------------------------------------------------------------------------------

	var opcimp = [ 
				 [ 'PDF', 'P' ], 
	             [ 'EXCEL', 'E' ] 
				 ];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtiporeporte = new Ext.form.ComboBox({
		store : stOpcimp,
		fieldLabel : 'Tipo Impresi&#243;n',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoimp',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtiporeporte.setValue('P');
//----------------------------------------------------------------------------------------------------------------------------------	
	//creando funcion para llenar el combo
	function llenarComboAnio()
	{
		var myJSONObject ={
				"operacion": 'comboanio'
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datosc = resultado.responseText;  
				if(datosc!='')
				{
					var DatosAnio = eval('(' + datosc + ')');
					stanio.loadData(DatosAnio);
				}
			}
		});
	}
	//cmbformato.setValue('1');
//----------------------------------------------------------------------------------------------------------------------------------
	//creando funcion para llenar el combo
	function llenarComboMesDesAnio()
	{
		var myJSONObject ={
				"operacion": 'comboanio'
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datosc = resultado.responseText;  
				if(datosc!='')
				{
					var DatosAnio = eval('(' + datosc + ')');
					staniodes.loadData(DatosAnio);
				}
			}
		});
	}
	//cmbformato.setValue('1');
//----------------------------------------------------------------------------------------------------------------------------------
	//creando funcion para llenar el combo
	function llenarComboMesHasAnio()
	{
		var myJSONObject ={
				"operacion": 'comboanio'
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datosc = resultado.responseText;  
				if(datosc!='')
				{
					var DatosAnio = eval('(' + datosc + ')');
					staniohas.loadData(DatosAnio);
				}
			}
		});
	}
	//cmbformato.setValue('1');
//----------------------------------------------------------------------------------------------------------------------------------

//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 750,
	height: 500,
	title: "<H1 align='center'>Estado de Resultado</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [	
        	fieldsetPeriodos,
           	fieldsetIntervaloFechasTrimestres,
        	fieldsetIntervaloFechasMeses,
        	fieldsetIntervaloFechasDias,
        	fieldsetIntervaloCencos,
        	fieldsetNivelCuenta,
        	fieldsetGraficos,
			{
			xtype: 'hidden',
			id: 'estcencos',
			binding:true,
			defaultvalue:'',
			allowBlank:true
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:225px;top:45px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:100,
					items: [cmbtiporeporte]
					}]
			}]	
	});
	irNuevo();
	irBuscarFormato();
	llenarComboAnio();
	llenarComboMesDesAnio();
	llenarComboMesHasAnio();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	Ext.getCmp('mesdes').disable();
	Ext.getCmp('meshas').disable();
	Ext.getCmp('aniod').disable();
	Ext.getCmp('anioh').disable();
	Ext.getCmp('fecdesde').disable();
	Ext.getCmp('fechasta').disable();
	var myJSONObject = {
			"operacion":"verificar_estatus_estcencos" 
		};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function ( result, request ) 
		{ 
			cencos = result.responseText;
			if (cencos != "")
			{
				Ext.getCmp('estcencos').setValue(cencos);
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});
}

function validarCentroCosContable()
{
	var unidadOk = true;
	if(cencos==0){
		unidadOk = false;
	}
	
	return unidadOk;
}

function irBuscarFormato()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'ESTADO_RESULTADO',
		'valor'		  : 'sigesp_scg_rpp_estado_resultado.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato1 = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})

	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'ESTADO_RESULTADO_EXCEL',
		'valor'		  : 'sigesp_scg_rpp_estado_resultado_excel.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formatoExcel = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}

//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		var radio= Ext.getCmp('hidbot');
		for (var j = 0; j < radio.items.length; j++)
		{
		  if (radio.items.items[j].checked)
		  {
			hidbot = radio.items.items[j].inputValue;
			break;
		  }
		} 
		cmbnivel = Ext.getCmp('cmbnivel').getValue();
		li_precierre = 0;
		valido       = true;
		
		if(cencos=='1')
		{
			ls_costodesde  = Ext.getCmp('codcencosdes').getValue();
			ls_costohasta  = Ext.getCmp('codcencoshas').getValue();
		}
		else
		{
			ls_costodesde  = '';
			ls_costohasta  = '';
		}
			
		if(valido)
		{
			if(hidbot==1)
			{
				cmbmesdes  = Ext.getCmp('mesdes').getValue();
				cmbmeshas  = Ext.getCmp('meshas').getValue();
				cmbagnodes = Ext.getCmp('aniod').getValue();
				cmbagnohas = Ext.getCmp('anioh').getValue();
				if((cmbagnodes=="")||(cmbagnohas=="")||(cmbmesdes=="")||(cmbmeshas==""))
				{
					Ext.Msg.hide();
					alert ("Debe seleccionar los Parametros de Busqueda");
				}
				else
				{
					pagina="reportes/"+formato1+"?cmbmesdes="+cmbmesdes
							+"&cmbmeshas="+cmbmeshas+"&cmbagnodes="+cmbagnodes+"&cmbagnohas="+cmbagnohas+"&cmbnivel="+cmbnivel
							+"&hidbot="+hidbot+"&precierre="+li_precierre+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
					window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
			}
			if(hidbot==2)
			{
				fecdes2 = Ext.getCmp('fecdesde').getValue();
				fechas2 = Ext.getCmp('fechasta').getValue();
				txtfecdes = fecdes2.format(Date.patterns.fechacorta);
				txtfechas = fechas2.format(Date.patterns.fechacorta);
				if((txtfecdes=="")&&(txtfechas==""))
				{
					alert ("Debe seleccionar los Parametros de Busqueda");
				}
				else
				{
					pagina="reportes/"+formato1+"?txtfecdes="+txtfecdes
							+"&txtfechas="+txtfechas+"&cmbnivel="+cmbnivel+"&hidbot="+hidbot
							+"&precierre="+li_precierre+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;;
					window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
				}
			}
			if(hidbot==3)
			{
				var radio2= Ext.getCmp('intervaltri');
				for (var j = 0; j < radio2.items.length; j++)
				{
					if (radio2.items.items[j].checked)
					{
						rbtrimestre = radio2.items.items[j].inputValue;
						break;
					}
				}
				cmbagno = Ext.getCmp('anio').getValue();
				if (cmbagno!="")
				{
					if(rbtrimestre==1)
					{
						pagina="reportes/"+formato1+"?cmbmesdes=01&cmbmeshas=03&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
					if(rbtrimestre==2)
					{
						pagina="reportes/"+formato1+"?cmbmesdes=04&cmbmeshas=06&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
					if(rbtrimestre==3)
					{
						pagina="reportes/"+formato1+"?cmbmesdes=07&cmbmeshas=09&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
					if(rbtrimestre==4)
					{
						pagina="reportes/"+formato1+"?cmbmesdes=10&cmbmeshas=12&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
				}
				else
				{
					Ext.Msg.hide();
					alert ("Debe seleccionar los Parametros de Busqueda");
				}
			}
		}
	}
	else
	{
		valido = true;
		var radio= Ext.getCmp('hidbot');
		for (var j = 0; j < radio.items.length; j++)
		{
			if (radio.items.items[j].checked)
			{
				hidbot = radio.items.items[j].inputValue;
				break;
			}
		} 
		cmbnivel = Ext.getCmp('cmbnivel').getValue();
		li_precierre = 0;
		ls_costodesde  = '';
		ls_costohasta  = '';
		if(cencos=='1')
		{
			ls_costodesde  = Ext.getCmp('codcencosdes').getValue();
			ls_costohasta  = Ext.getCmp('codcencoshas').getValue();
		}
		if(valido)
		{
			if(hidbot==1)
			{
				cmbmesdes  = Ext.getCmp('mesdes').getValue();
				cmbmeshas  = Ext.getCmp('meshas').getValue();
				cmbagnodes = Ext.getCmp('aniod').getValue();
				cmbagnohas = Ext.getCmp('anioh').getValue();
				if((cmbagnodes=="")||(cmbagnohas=="")||(cmbmesdes=="")||(cmbmeshas==""))
				{
					Ext.Msg.hide();
					alert ("Debe seleccionar los Parametros de Busqueda");
				}
				else
				{
					pagina="reportes/"+formatoExcel+"?cmbmesdes="+cmbmesdes
						+"&cmbmeshas="+cmbmeshas+"&cmbagnodes="+cmbagnodes+"&cmbagnohas="+cmbagnohas+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				}
			}
			if(hidbot==2)
			{
				fecdes2 = Ext.getCmp('fecdesde').getValue();
				fechas2 = Ext.getCmp('fechasta').getValue();
				txtfecdes = fecdes2.format(Date.patterns.fechacorta);
				txtfechas = fechas2.format(Date.patterns.fechacorta);
				if((txtfecdes=="")&&(txtfechas==""))
				{
					alert ("Debe seleccionar los Parametros de Busqueda");
				}
				else
				{
					pagina="reportes/"+formatoExcel+"?txtfecdes="+txtfecdes
						+"&txtfechas="+txtfechas+"&cmbnivel="+cmbnivel+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
				}
			}
			if(hidbot==3)
			{
				var radio2= Ext.getCmp('intervaltri');
				for (var j = 0; j < radio2.items.length; j++)
				{
					if (radio2.items.items[j].checked)
					{
						rbtrimestre = radio2.items.items[j].inputValue;
						break;
					}
				}
				cmbagno = Ext.getCmp('anio').getValue();
				if (cmbagno!="")
				{
					if(rbtrimestre==1)
					{
						pagina="reportes/"+formatoExcel+"?cmbmesdes=01&cmbmeshas=03&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
					if(rbtrimestre==2)
					{
						pagina="reportes/"+formatoExcel+"?cmbmesdes=04&cmbmeshas=06&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
					if(rbtrimestre==3)
					{
						pagina="reportes/"+formatoExcel+"?cmbmesdes=07&cmbmeshas=09&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
					if(rbtrimestre==4)
					{
						pagina="reportes/"+formatoExcel+"?cmbmesdes=10&cmbmeshas=12&cmbagnodes="+cmbagno
						+"&cmbagnohas="+cmbagno+"&cmbnivel="+cmbnivel
						+"&hidbot="+hidbot+"&precierre="+li_precierre
						+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
						window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
					}
				}
				else
				{
					Ext.Msg.hide();
					alert ("Debe seleccionar los Parametros de Busqueda");
				}
			}
		}
	}
}
