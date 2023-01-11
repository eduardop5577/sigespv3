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
var ruta ='../../controlador/scg/sigesp_ctr_scg_cuentas.php'; //ruta del controlador
var fechaPrimera = obtenerPrimerDiaMes();
var formato_resumen = '';
var formato = '';
var formato_excel = '';
var cencos = '';

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//	--------------------------------------------------------------------------------------------------------------------------------	
//	**********************************************************************************************************************************	
//	INICIO DEL FORMULARIO PROVEEDOR / BENEFICIARIO	
//	**********************************************************************************************************************************
//	componente catalogo de cuentas contables
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
//	--------------------------------------------------------------------------------------------------------------------------------	
//	componente catalogo de cuentas contables
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
	                                            {header: "Código", width: 20, sortable: true,   dataIndex: 'codcencos'},
	                                            {header: "Denominación", width: 40, sortable: true, dataIndex: 'denominacion'}
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
		rutacontrolador:ruta,
		parametros: "ObjSon={'operacion': 'buscarCentroCostos'}",
		arrfiltro:[{etiqueta:'Codigo',id:'codicencos',valor:'codcencos',longitud:'10'},
		           {etiqueta:'Denominación',id:'denominacioni',valor:'denominacion'}],
		           posicion:'position:absolute;left:20px;top:15px',
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
		rutacontrolador:ruta,
		parametros: "ObjSon={'operacion': 'buscarCentroCostos'}",
		arrfiltro:[{etiqueta:'Codigo',id:'codicencos',valor:'codcencos',longitud:'10'},
		           {etiqueta:'Denominación',id:'denominacioni',valor:'denominacion'}],
		           posicion:'position:absolute;left:440px;top:15px',
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

//	--------------------------------------------------------------------------------------------------------------------------------	
//	--------------------------------------------------------------------------------------------
//	creacion del formulario de datos de proveedor / beneficiario
	fieldsetctascontable = new Ext.form.FieldSet({
		title:"Cuentas Contables",
		style: 'position:absolute;left:10px;top:5px',
		border:true,
		width: 715,
		cls :'fondo',
		height: 100,
		items: [{
				style:'position:absolute;left:25px;top:45px',
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
				style:'position:absolute;left:235px;top:45px',
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
									comcampocatcuentacontable1.mostrarVentana();	
								}
							}]
						}]
				},
				{
				style:'position:absolute;left:450px;top:45px',
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
				style:'position:absolute;left:660px;top:45px',
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
									comcampocatcuentacontable2.mostrarVentana();	
								}
							}]
						}]
				}]

	});
//	----------------------------------------------------------------------------------------------------------------------------------	
//	**********************************************************************************************************************************	
//	INICIO DEL FORMULARIO INTERVALO FECHAS
//	**********************************************************************************************************************************
	//creacion del formulario de datos de intervalo de fechas
	fieldsetIntervaloFechas= new Ext.form.FieldSet({
		title:"Periodo",
		style: 'position:absolute;left:10px;top:115px',
		border:true,
		width: 715,
		cls :'fondo',
		height: 75,
		items: [{
				style:'position:absolute;left:25px;top:25px',
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
				style:'position:absolute;left:450px;top:25px',
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
//	----------------------------------------------------------------------------------------------------------------------------------
//	**********************************************************************************************************************************	
//	INICIO DEL FORMULARIO CENTRO DE COSTOS
//	**********************************************************************************************************************************
//	creacion del formulario de datos de centro de costos
	//var frmIntervaloCencos = new Ext.FormPanel({
	fieldsetIntervaloCencos= new Ext.form.FieldSet({
		title:"Centros de Costos",
		style: 'position:absolute;left:10px;top:115px',
		border:true,
		width: 715,
		cls :'fondo',
		height: 85,
		items: [comcampocatcencosdes.fieldsetCatalogo,
		        comcampocatcencoshas.fieldsetCatalogo
		        ]

	});	
//	----------------------------------------------------------------------------------------------------------------------------------
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
		fieldLabel : 'Tipo Impresión',
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
//	----------------------------------------------------------------------------------------------------------------------------------	
	//creando store para el filtrar
	var filtrar = 	[
	              	 ['1','1'],
	              	 ['2','2'],
	              	 ['3','3'],
	              	 ['4','4'],
	              	 ['5','5'],
	              	 ['6','6'],
	              	 ['7','7'],
	              	 ['8','8'],
	              	 ['9','9'],
	              	 ['10','10']
	              	 ]; // Arreglo que contiene los Documentos que se pueden controlar

	var stfiltrar = new Ext.data.SimpleStore({
		fields : ['etiqueta','valor'],
		data : filtrar
	});
	//fin creando store para el combo filtrar

	//creando objeto combo filtrar
	var cmbfiltrar = new Ext.form.ComboBox({
		store : stfiltrar,
		fieldLabel : 'Nivel',
		labelSeparator : '',
		editable : false,
		emptyText:'--- Seleccione ---',
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'nivel',
		width : 40,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	cmbfiltrar.setValue('1');
//	----------------------------------------------------------------------------------------------------------------------------------
	//creando store para el tipo de reporte
	var tiporep = 	[
	              	 ['Mayor Analítico','mayor'],
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
//	----------------------------------------------------------------------------------------------------------------------------------

//	Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 750,
		height: 270,
		title: "<H1 align='center'>Listado de Cuentas</H1>",
		frame: true,
		autoScroll: true,
		style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [	
	        	fieldsetctascontable,
	        	//fieldsetIntervaloFechas,
	        	fieldsetIntervaloCencos,
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
        		style: 'position:absolute;left:255px;top:30px',
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
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
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
		'variable'    : 'BALANCE_COMPROBACION',
		'valor'		  : 'sigesp_scg_rpp_balance_comprobacion_pdf.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: '../../controlador/scg/sigesp_ctr_scg_balance_comprobacion.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}
//**********************************************************************************************************************************	
//BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		ls_cuentadesde  = Ext.getCmp('sc_cuenta1').getValue(); 
		ls_cuentahasta  = Ext.getCmp('sc_cuenta2').getValue(); 

		if(cencos=='1')
		{
			ls_costodesde  = Ext.getCmp('codcencosdes').getValue(); 
			ls_costohasta  = Ext.getCmp('codcencosdes').getValue(); 
		}
		else
		{
			ls_costodesde  = '';
			ls_costohasta  = '';
		}
		if(ls_cuentadesde>ls_cuentahasta)
		{
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error', 'Intervalo de cuentas incorrecto.'); 
			Ext.getCmp('sc_cuenta1').setValue('');
			Ext.getCmp('sc_cuenta2').setValue('');
		}
		else
		{
			pagina="reportes/sigesp_scg_rpp_cuentas.php?cuentadesde="+ls_cuentadesde+"&cuentahasta="+ls_cuentahasta
			+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
	else
	{
		ls_cuentadesde  = Ext.getCmp('sc_cuenta1').getValue(); 
		ls_cuentahasta  = Ext.getCmp('sc_cuenta2').getValue(); 

		if(cencos=='1')
		{
			ls_costodesde  = Ext.getCmp('codcencosdes').getValue(); 
			ls_costohasta  = Ext.getCmp('codcencosdes').getValue(); 
		}
		else
		{
			ls_costodesde  = '';
			ls_costohasta  = '';
		}
		if(ls_cuentadesde>ls_cuentahasta)
		{
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error', 'Intervalo de cuentas incorrecto.'); 
			Ext.getCmp('sc_cuenta1').setValue('');
			Ext.getCmp('sc_cuenta2').setValue('');
		}
		else
		{
			pagina="reportes/sigesp_scg_rpp_cuentas_excel.php?cuentadesde="+ls_cuentadesde+"&cuentahasta="+ls_cuentahasta
			+"&costodesde="+ls_costodesde+"&costohasta="+ls_costohasta;
			window.open(pagina,"Reporte","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
}
