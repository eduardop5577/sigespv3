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
var ruta ='../../controlador/scg/sigesp_ctr_scg_listado_comprobante.php'; //ruta del controlador
var fechaPrimera = obtenerPrimerDiaMes();
var formato1 = '';
var formato1_1 = '';
var formato2 = '';
var formato2_1 = '';
var formato_excel = '';
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
//componente catalogo de cuentas contables
	var reCuentaContable = Ext.data.Record.create([
        {name: 'sc_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);

	var comcampocatcuentacontable1 = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'scg1',
		reCatalogo: reCuentaContable,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		soloCatalogo: true,
		valorStatus: '',
		arrSetCampo:[{campo:'sc_cuenta1',valor:'sc_cuenta'}]
	});
	//fin componente catalogo de cuentas contables
//--------------------------------------------------------------------------------------------------------------------------------	
//componente catalogo de cuentas contables
	var reCuentaContable2 = Ext.data.Record.create([
        {name: 'sc_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);

	var comcampocatcuentacontable2 = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'scg2',
		reCatalogo: reCuentaContable2,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		soloCatalogo: true,
		valorStatus: '',
		arrSetCampo:[{campo:'sc_cuenta2',valor:'sc_cuenta'}]
	});
	//fin componente catalogo de cuentas contables
	//--------------------------------------------------------------------------------------------
//Intervalos de Comprobante
	var re_comprobante = Ext.data.Record.create([
  		{name: 'comprobante'},
		{name: 'descripcion'},
		{name: 'procede'},
		{name: 'fecha'},
		{name: 'cod_pro'},
		{name: 'ced_bene'},
  		{name: 'monto'}
  	]);
  	
  	var dscomprobante=  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},re_comprobante)
  	});
  						
  	var cmcatcomprobante = new Ext.grid.ColumnModel([
        {header: "<H1 align='center'>Comprobante</H1>", width: 60, sortable: true, dataIndex: 'comprobante'},
		{header: "<H1 align='center'>Descrpci&#243;n Comprobante</H1>", width: 45, sortable: true, dataIndex: 'descripcion'},
		{header: "<H1 align='center'>Procede</H1>", width: 25, sortable: true, dataIndex: 'procede' },
		{header: "<H1 align='center'>Fecha</H1>", width: 35, sortable: true, dataIndex: 'fecha'},
		{header: "<H1 align='center'>Proveedor</H1>", width: 40, sortable: true, dataIndex: 'cod_pro'}, 
		{header: "<H1 align='center'>Beneficiario</H1>", width: 35, sortable: true, dataIndex: 'ced_bene'},
		{header: "<H1 align='center'>Monto</H1>", width: 35, sortable: true, dataIndex: 'monto'}
  	]);
  	//componente campocatalogo para el campo cuentas contables
  	
  	comcampocatcomprobantedes = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Comprobantes Contables</H1>",
  			anchoformbus: 710,
  			altoformbus:100,
  			anchogrid: 710,
  			altogrid: 400,
  			anchoven: 750,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dscomprobante,
  			colmodelocat: cmcatcomprobante,
  			rutacontrolador:ruta,
  			parametros: "ObjSon={'operacion': 'catalogo_comprobante'",
  			arrfiltro:[{etiqueta:'Comprobante',id:'id_comprobante',valor:'comprobante',longitud:'10'},
  					   {etiqueta:'Procedencia',id:'id_procede',valor:'procede'}],
  			posicion:'position:absolute;left:25px;top:10px',
  			tittxt:'Desde',
  			idtxt:'comprodes',
  			campovalue:'comprobante',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'descripciond',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'P',
			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarTipoFormato1,
			msjValidarMostrar: 'Para usar este catalogo debe seleccionar formato 1'
  	});

  	comcampocatcomprobantehas = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Comprobantes Contables</H1>",
  			anchoformbus: 710,
  			altoformbus:100,
  			anchogrid: 710,
  			altogrid: 400,
  			anchoven: 750,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dscomprobante,
  			colmodelocat: cmcatcomprobante,
  			rutacontrolador:ruta,
  			parametros: "ObjSon={'operacion': 'catalogo_comprobante_hasta'",
  			arrfiltro:[{etiqueta:'Comprobante',id:'icomprobante',valor:'comprobante',longitud:'10'},
  					   {etiqueta:'Procedencia',id:'iprocede',valor:'procede'}],
  			posicion:'position:absolute;left:440px;top:10px',
  			tittxt:'Hasta',
  			idtxt:'comprohas',
  			campovalue:'comprobante',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'descripcionh',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'P',
			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarTipoFormato1,
			msjValidarMostrar: 'Para usar este catalogo debe seleccionar formato 1'
  	});

//--------------------------------------------------------------------------------------------------------------------------------	
//Intervalos de Procede
	var re_procede = Ext.data.Record.create([
  		{name: 'comprobante'},
		{name: 'descripcion'},
		{name: 'procede'},
		{name: 'fecha'},
		{name: 'cod_pro'},
		{name: 'ced_bene'},
  		{name: 'monto'}
  	]);
  	
  	var dsprocede=  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},re_procede)
  	});
  						
  	var cmcatprocede = new Ext.grid.ColumnModel([
        {header: "<H1 align='center'>Procede</H1>", width: 25, sortable: true, dataIndex: 'procede' },
		{header: "<H1 align='center'>Comprobante</H1>", width: 60, sortable: true, dataIndex: 'comprobante'},
		{header: "<H1 align='center'>Descrpci&#243;n Comprobante</H1>", width: 45, sortable: true, dataIndex: 'descripcion'},
		{header: "<H1 align='center'>Fecha</H1>", width: 35, sortable: true, dataIndex: 'fecha'},
		{header: "<H1 align='center'>Proveedor</H1>", width: 40, sortable: true, dataIndex: 'cod_pro'}, 
		{header: "<H1 align='center'>Beneficiario</H1>", width: 35, sortable: true, dataIndex: 'ced_bene'},
		{header: "<H1 align='center'>Monto</H1>", width: 35, sortable: true, dataIndex: 'monto'}
  	]);
  	//componente campocatalogo para el campo cuentas contables
  	
  	comcampocatprocededes = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Comprobantes Contables</H1>",
  			anchoformbus: 710,
  			altoformbus:100,
  			anchogrid: 710,
  			altogrid: 400,
  			anchoven: 750,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dsprocede,
  			colmodelocat: cmcatprocede,
  			rutacontrolador:ruta,
  			parametros: "ObjSon={'operacion': 'catalogo_procede'",
  			arrfiltro:[{etiqueta:'Comprobante',id:'id_comprobante1',valor:'comprobante',longitud:'10'},
  					   {etiqueta:'Procedencia',id:'id_procede1',valor:'procede'}],
  			posicion:'position:absolute;left:25px;top:10px',
  			tittxt:'Desde',
  			idtxt:'procededes',
  			campovalue:'procede',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'descripciond',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'P',
			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarTipoFormato1,
			msjValidarMostrar: 'Para usar este catalogo debe seleccionar formato 1'
  	});

  	comcampocatprocedehas = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Comprobantes Contables</H1>",
  			anchoformbus: 710,
  			altoformbus:100,
  			anchogrid: 710,
  			altogrid: 400,
  			anchoven: 750,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dsprocede,
  			colmodelocat: cmcatprocede,
  			rutacontrolador:ruta,
  			parametros: "ObjSon={'operacion': 'catalogo_procede_hasta'",
  			arrfiltro:[{etiqueta:'Comprobante',id:'icomprobante1',valor:'comprobante',longitud:'10'},
  					   {etiqueta:'Procedencia',id:'iprocede1',valor:'procede'}],
  			posicion:'position:absolute;left:440px;top:10px',
  			tittxt:'Hasta',
  			idtxt:'procedehas',
  			campovalue:'procede',
  			anchoetiquetatext:35,
  			anchotext:150,
  			anchocoltext:0.23,
  			idlabel:'descripcionh',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'P',
			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false,
			validarMostrar:1,
			fnValidarMostrar: validarTipoFormato1,
			msjValidarMostrar: 'Para usar este catalogo debe seleccionar formato 1'
  	});

//--------------------------------------------------------------------------------------------------------------------------------	

//creacion del formulario de datos de proveedor / beneficiario
  	fieldset = new Ext.form.FieldSet({
		width: 715,
		height: 70,
		title:"Cuentas Contables",
		style: 'position:absolute;left:10px;top:45px',
		cls :'fondo',
		items: [{
				style:'position:absolute;left:35px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								xtype:"textfield",
								labelSeparator:'',
								fieldLabel:'Desde',
								name:'sc_cuenta1',
								id:'sc_cuenta1',	
								width:150,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								allowBlank:true,
								autoCreate:{tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},
				{
				style:'position:absolute;left:245px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						items: [{
								xtype:"button",
								id:'btnBuscarProv',
								iconCls:'menubuscar',
								handler:	function(boton)
								{
									if (validarTipoFormato2())
									{
										comcampocatcuentacontable1.mostrarVentana();
									}
									else
									{
										Ext.Msg.hide();
										Ext.MessageBox.alert('Error', 'Debe seleccionar el Formato 2 para esta opci&#243;n.');
									}
								}
							}]
						}]
				},
				{
				style:'position:absolute;left:450px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [{
								xtype:"textfield",
								labelSeparator:'',
								fieldLabel:'Hasta',
								name:'sc_cuenta2',
								id:'sc_cuenta2',
								width:150,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								allowBlank:true,
								autoCreate:{tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},
				{
				style:'position:absolute;left:660px;top:10px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						items: [{
								xtype:"button",
								id:'btnBuscarben',
								iconCls:'menubuscar',
								handler:	function(boton)
								{
									if (validarTipoFormato2())
									{
										comcampocatcuentacontable2.mostrarVentana();
									}
									else
									{
										Ext.Msg.hide();
										Ext.MessageBox.alert('Error', 'Debe seleccionar el Formato 2 para esta opci&#243;n.'); 
									}
								}
							}]
						}]
				}]
	});
//----------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO INTERVALO FECHAS
//**********************************************************************************************************************************
	//creacion del formulario de datos de intervalo de fechas
  	fieldsetdos = new Ext.form.FieldSet({
		width: 715,
		height: 65,
		title:"Intervalo de Fechas",
		style: 'position:absolute;left:10px;top:125px',
		cls :'fondo',
		items: [{
				style:'position:absolute;left:35px;top:10px',
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
				style:'position:absolute;left:450px;top:10px',
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
//                                 				INICIO DEL FORMULARIO COMPROBANTES
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos
  	fieldsettres = new Ext.form.FieldSet({
		width: 715,
		height: 70,
		title:"Intervalo Comprobantes",
		style: 'position:absolute;left:10px;top:200px',
		cls :'fondo',
		items: [comcampocatcomprobantedes.fieldsetCatalogo,
				comcampocatcomprobantehas.fieldsetCatalogo]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO PROCEDE
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos
  	fieldsetcuatro = new Ext.form.FieldSet({
		width: 715,
		height: 70,
		title:"Intervalo de Procede",
		style: 'position:absolute;left:10px;top:280px',
		cls :'fondo',
		items: [comcampocatprocededes.fieldsetCatalogo,
				comcampocatprocedehas.fieldsetCatalogo
		]
	});	
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO ORDENADO POR
//**********************************************************************************************************************************
//creacion del formulario de datos de centro de costos

  	fieldsetcinco = new Ext.form.FieldSet({
		width: 650,
		height: 80,
		title:"Ordenado por",
		style: 'position:absolute;left:40px;top:360px',
		cls :'fondo',
		items: [{
			xtype: "radiogroup",
			fieldLabel: "",
			labelSeparator:"",
			hideLabel:true,
			binding:true,
			hiddenvalue:'',
			defaultvalue:'',
			columns: [290,290,290],
			id:'rborden',
			items: [
			        {boxLabel: 'Procede-Comprobante-Fecha', name: 'rborden',inputValue: '1',checked:true},
			        {boxLabel: 'Comprobante-Fecha-Procede', name: 'rborden', inputValue: '2'},
			        {boxLabel: 'Fecha-Procede-Comprobante', name: 'rborden', inputValue: '3'}
			        ]
		}]
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
	//creando store para el filtrar
	var formato = 	[
					['Formato-1','1'],
					['Formato-2','2']
					]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var stformato = new Ext.data.SimpleStore({
		fields : ['etiqueta','valor'],
		data : formato
	});
	//fin creando store para el combo filtrar

	//creando objeto combo filtrar
	var cmbformato = new Ext.form.ComboBox({
		store : stformato,
		fieldLabel : 'Tipo Formato',
		labelSeparator : '',
		editable : false,
		emptyText:'--- Seleccione ---',
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'formato',
		width : 90,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		listeners: {
					'select': function(){
						if(this.getValue()=='1')
						{	
							Ext.getCmp('chkmostrar_res').reset();
							Ext.getCmp('chkmostrar_res').enable();
							Ext.getCmp('chkorderdocumento').reset();
							Ext.getCmp('chkorderdocumento').enable();
							Ext.getCmp('sc_cuenta1').reset();
							Ext.getCmp('sc_cuenta2').reset();
						}
						else
						{
							Ext.getCmp('chkmostrar_res').reset();
							Ext.getCmp('chkmostrar_res').disable();
							Ext.getCmp('chkorderdocumento').reset();
							Ext.getCmp('chkorderdocumento').disable();
							Ext.getCmp('procededes').reset();
							Ext.getCmp('procedehas').reset();
							Ext.getCmp('comprodes').reset();
							Ext.getCmp('comprohas').reset();
						}
					 }
				   }
	});
	cmbformato.setValue('1');
//----------------------------------------------------------------------------------------------------------------------------------
	//creando store para el tipo de reporte
	var tiporep = 	[
					['Mayor Anal&#225;tico','mayor'],
					['Resumen Mensual','resumen']
					]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttiporep = new Ext.data.SimpleStore({
		fields : ['etiqueta','valor'],
		data : tiporep
	});
	//fin creando store para el combo filtrar

	//creando objeto combo filtrar
	var cmbtiporep = new Ext.form.ComboBox({
		store : sttiporep,
		fieldLabel : 'Reporte',
		labelSeparator : '',
		editable : false,
		emptyText:'--- Seleccione ---',
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'cmb_reporte',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
//----------------------------------------------------------------------------------------------------------------------------------

//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 760,
	height: 550,
	title: "<H1 align='center'>Listado de Comprobante</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [
	        fieldset,fieldsetdos,fieldsettres,fieldsetcuatro,fieldsetcinco,
			{
			xtype: 'hidden',
			id: 'estcencos',
			binding:true,
			defaultvalue:'',
			allowBlank:true
			},
			{
			style:'position:absolute;left:15px;top:10px',
			layout:"column",
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:90,
					items: [cmbformato]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:440px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 150,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: 'Mostrar Descripci&#243;n de los Detalles',
							id: 'chkmostrar',
							inputValue:0,
							allowBlank:true
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:480px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 150,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: 'Generar Resumen de Comprobantes',
							id: 'chkmostrar_res',
							inputValue:0,
							allowBlank:true
						}]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:520px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 150,
					items: [{
							xtype: 'checkbox',
							labelSeparator :'',
							fieldLabel: 'Ordenar Movimientos por Documentos',
							id: 'chkorderdocumento',
							inputValue:0,
							allowBlank:true
						}]
				}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:425px;top:10px',
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
	irBuscarFormato1();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	Ext.getCmp('chkmostrar').setValue(true);
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

function validarTipoFormato2()
{
	var unidadOk = true;
	if(Ext.getCmp('formato').getValue()==1){
		unidadOk = false;
	}
	
	return unidadOk;
}

function validarTipoFormato1()
{
	var unidadOk = true;
	if(Ext.getCmp('formato').getValue()==2){
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
		'variable'    : 'COMPROBANTE_FORMATO1',
		'valor'		  : 'sigesp_scg_rpp_comprobante_formato1.php',
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
}
function irBuscarFormato1()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato1',
		'sistema'	  : 'SCG',
		'seccion'     : 'REPORTE',
		'variable'    : 'COMPROBANTE_FORMATO1_SINDT',
		'valor'		  : 'sigesp_scg_rpp_comprobante_formato1_sindt.php',
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
			formato1_1 = result.responseText;			
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
	
	if (Ext.getCmp('formato').getValue()=='1')
	{
		if (Ext.getCmp('tipoimp').getValue()=='P')
		{ 
			txtcompdes  = Ext.getCmp('comprodes').getValue();
			txtcomphas  = Ext.getCmp('comprohas').getValue();
			txtprocdes  = Ext.getCmp('procededes').getValue();
			txtprochas  = Ext.getCmp('procedehas').getValue();
			fecdes2 = Ext.getCmp('fecdesde').getValue();
			fechas2 = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdes2.format(Date.patterns.fechacorta);
			txtfechas = fechas2.format(Date.patterns.fechacorta);
			
			li_chkorderdocumento = 0;
			var radio= Ext.getCmp('rborden');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				ordenado = radio.items.items[j].inputValue;
				break;
			  }
			} 
			
			if(Ext.getCmp('chkorderdocumento').checked==true)
			{
				li_chkorderdocumento = 1;
			}
			
			if (Ext.getCmp('chkmostrar_res').checked==true)
			{
				pagina="reportes/sigesp_scg_rpp_resumen_comprobantes.php?txtcompdes="+txtcompdes
						+"&txtcomphas="+txtcomphas+"&txtprocdes="+txtprocdes+"&txtprochas="+txtprochas
						+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas+"&orderdocumento="+li_chkorderdocumento;
			}
			else if(Ext.getCmp('chkmostrar').checked==true) 
			{
				pagina="reportes/"+formato1+"?txtcompdes="+txtcompdes
						+"&txtcomphas="+txtcomphas+"&txtprocdes="+txtprocdes+"&txtprochas="+txtprochas
						+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas+"&orderdocumento="+li_chkorderdocumento;
			}
			else
			{
				pagina="reportes/"+formato1_1+"?txtcompdes="+txtcompdes
						+"&txtcomphas="+txtcomphas+"&txtprocdes="+txtprocdes+"&txtprochas="+txtprochas
						+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas+"&orderdocumento="+li_chkorderdocumento;
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
		else
		{
			txtcompdes  = Ext.getCmp('comprodes').getValue();
			txtcomphas  = Ext.getCmp('comprohas').getValue();
			txtprocdes  = Ext.getCmp('procededes').getValue();
			txtprochas  = Ext.getCmp('procedehas').getValue();
			fecdes2 = Ext.getCmp('fecdesde').getValue();
			fechas2 = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdes2.format(Date.patterns.fechacorta);
			txtfechas = fechas2.format(Date.patterns.fechacorta);
			
			var radio= Ext.getCmp('rborden');
			
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				ordenado = radio.items.items[j].inputValue;
				break;
			  }
			}
			
			if (Ext.getCmp('chkmostrar_res').checked==true)
			{
			   Ext.Msg.hide();
			   alert("Para generar este reporte debe destildar la opci&#243;n Mostrar Resumen de Comprobantes.");
			}
			else if(Ext.getCmp('chkmostrar').checked==true)
			{
				pagina="reportes/sigesp_scg_rpp_comprobante_formato1_excel.php?txtcompdes="+txtcompdes
						+"&txtcomphas="+txtcomphas+"&txtprocdes="+txtprocdes+"&txtprochas="+txtprochas
						+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_comprobante_formato1_sindt_excel.php?txtcompdes="+txtcompdes
						+"&txtcomphas="+txtcomphas+"&txtprocdes="+txtprocdes+"&txtprochas="+txtprochas
						+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
	}
	else
	{
		if (Ext.getCmp('tipoimp').getValue()=='P')
		{
			txtcuentadesde  = Ext.getCmp('sc_cuenta1').getValue();
			txtcuentahasta  = Ext.getCmp('sc_cuenta2').getValue();
			fecdes2 = Ext.getCmp('fecdesde').getValue();
			fechas2 = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdes2.format(Date.patterns.fechacorta);
			txtfechas = fechas2.format(Date.patterns.fechacorta);
			var radio= Ext.getCmp('rborden');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				ordenado = radio.items.items[j].inputValue;
				break;
			  }
			} 
			if(Ext.getCmp('chkmostrar').checked==true)
			{
				pagina="reportes/sigesp_scg_rpp_comprobante_formato2.php?txtcuentadesde="+txtcuentadesde
						+"&txtcuentahasta="+txtcuentahasta+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas;
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_comprobante_formato2_sindt.php?txtcuentadesde="+txtcuentadesde
						+"&txtcuentahasta="+txtcuentahasta+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas;
			}
			window.open(pagina,"Reportes","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
		else
		{
			txtcuentadesde  = Ext.getCmp('sc_cuenta1').getValue();
			txtcuentahasta  = Ext.getCmp('sc_cuenta2').getValue();
			fecdes2 = Ext.getCmp('fecdesde').getValue();
			fechas2 = Ext.getCmp('fechasta').getValue();
			txtfecdes = fecdes2.format(Date.patterns.fechacorta);
			txtfechas = fechas2.format(Date.patterns.fechacorta);
			var radio= Ext.getCmp('rborden');
			for (var j = 0; j < radio.items.length; j++)
			{
			  if (radio.items.items[j].checked)
			  {
				ordenado = radio.items.items[j].inputValue;
				break;
			  }
			} 
			if(Ext.getCmp('chkmostrar').checked==true)
			{
				pagina="reportes/sigesp_scg_rpp_comprobante_formato2_excel.php?txtcuentadesde="+txtcuentadesde
						+"&txtcuentahasta="+txtcuentahasta+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas;
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_comprobante_formato2_sindt_excel.php?txtcuentadesde="+txtcuentadesde
						+"&txtcuentahasta="+txtcuentahasta+"&txtfecdes="+txtfecdes+"&rborden="+ordenado+"&txtfechas="+txtfechas;
			}
			window.open(pagina,"Reportes","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
}
