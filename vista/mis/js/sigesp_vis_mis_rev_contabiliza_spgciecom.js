/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var gridCompromisoCierre  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromContabilzarSPG = null //varibale para almacenar la instacia de objeto de formulario 
var fecha = new Date(); 

barraherramienta    = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.Ajax.timeout=36000000000;
					 
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	
	var Xpos = ((screen.width/2)-(920/2));
	var	plContabilzarSPG = new Ext.FormPanel({
		applyTo: 'formularioSPG',
		width: 950,
		height: 500,
		style: 'position:absolute;left:200px;top:80px',
		title: "<H1 align='center'>Reverso de Cierre/Disminucion de Compromisos</H1>",
		frame: true,
		autoScroll:true,
		items: [fromContabilzarSPG,gridCompromisoCierre]
	});
	plContabilzarSPG.doLayout();
});
	
	var tipocompromiso = 	[
                     	['Solicitud de ejecucion Presupuestaria','SEPCIE'],
 			['Orden de Compra','SOCCIE'],
                        ['Orden de Servicio','SOCCIE']]; 
 	
 	var sttipocompromiso = new Ext.data.SimpleStore({
 		fields : [ 'etiqueta', 'valor' ],
 		data : tipocompromiso
 	});

 	var cmbtipocompromiso = new Ext.form.ComboBox({
 		store : sttipocompromiso,
 		fieldLabel : 'Tipo Compromiso ',
 		labelSeparator : '',
 		editable : false,
 		displayField : 'etiqueta',
 		valueField : 'valor',
 		id : 'sistema',
 		width:200,
 		typeAhead: true,
 		triggerAction:'all',
 		forceselection:true,
 		binding:true,
 		mode:'local',
 		emptyText : '-- Seleccione --',
                value:'SEPCIE',
                listeners: {'select':irLimpiar}
 	});

var reg_comprobante = Ext.data.Record.create([
    {name: 'comprobante'},
    {name: 'cod_pro'},
    {name: 'codigo'},
    {name: 'fecha'},
    {name: 'monto'}
]);

var dscomprobante =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({
		root: 'raiz',             
		id: "id"},reg_comprobante)
});

var colmodelcomprobante = new Ext.grid.ColumnModel([
    {header: "<CENTER>Comprobante<CENTER>", width: 50, sortable: true,   dataIndex: 'comprobante'},
    {header: "<CENTER>Proveedor/Beneficiario</CENTER>", width: 150, sortable: true,   dataIndex: 'codigo'},
    {header: "<CENTER>fecha</CENTER>", width: 50, sortable: true,   dataIndex: 'fecha'},
    {header: "<CENTER>Monto</CENTER>", width: 50, sortable: true, dataIndex: 'monto'}
]);
//fin del campo de proveedores

//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
comcampocatcomprobante = new com.sigesp.vista.comCampoCatalogo({
	titvencat: "<H1 align='center'>Catalogo de Cierres/Disminuciones</H1>",
	anchoformbus: 770,
	altoformbus:180,
	anchogrid: 770,
	altogrid: 520,
	anchoven: 800,
	altoven: 600,
	datosgridcat: dscomprobante,
	colmodelocat: colmodelcomprobante,
	rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
	parametros: "ObjSon={'operacion': 'catalogo_cierredisminuciones', 'sistema': '"+Ext.getCmp('sistema').getValue()+"'}",
	arrfiltro:[{etiqueta:'Comprobante',id:'mcomprobante',valor:'comprobante',longitud:'15'},
                   {etiqueta:'Fecha Desde',id:'mfecdes',valor:'fecha',tipo:'datefield',defecto:fecha.getFullYear()+'-01-01'},
                   {etiqueta:'Fecha Hasta',id:'mfechas',valor:'fecha',tipo:'datefield',defecto:obtenerFechaActual()},
                   {etiqueta:'Proveedor',id:'mcodigo',valor:'codigo'}],
	posicion:'position:absolute;left:5px;top:40px',
	tittxt:'Compromiso',
	idtxt:'comprobante',
	campovalue:'comprobante',
	anchoetiquetatext:150,
	anchotext:135,
	anchocoltext:0.50,
	anchocoletiqueta:0.45,
	anchoetiqueta:400,
	anchofieldset: 700,
	tipbus:'P',
	hiddenvalue:'',
	defaultvalue:'',
        arrtxtfiltro:['sistema'],
	allowblank:false,
        onAceptar:true,
        fnOnAceptar:irBuscar
});

	var reMovPresupuestario = Ext.data.Record.create([
        {name: 'comprobante'}, 
        {name: 'fecha'},
        {name: 'descripcion'},
        {name: 'procede'},
        {name: 'tipproben'},
        {name: 'codigo'},
        {name: 'nombre'},
        {name: 'monto'}
    ]);

	var dsMovPresupuestario =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovPresupuestario)
	});

	var cmMovPresupuestario = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width: 100, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Procede</CENTER>", width: 50, sortable: true, dataIndex: 'procede'},
	    {header: "<CENTER>Fecha</CENTER>", width: 50, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Descripci&#243;n</CENTER>", width: 150, sortable: true, dataIndex: 'descripcion'},
	    {header: "<CENTER>Proveedor/Beneficiario</CENTER>", width: 150, sortable: true, dataIndex: 'nombre'},
	    {header: "<CENTER>Monto</CENTER>", width: 75, sortable: true, dataIndex: 'monto'}
	]);

	gridCompromisoCierre = new Ext.grid.GridPanel({
		width:900,
		height:330,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:120px',
		autoScroll:true,
		border:true,
		ds: dsMovPresupuestario,
		cm: cmMovPresupuestario,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});


	var Xpos = ((screen.width/2)-(300));
	fromContabilzarSPG = new Ext.form.FieldSet({
		    title:'',
		    style: 'position:absolute;left:15px;top:15px',
			border:true,
			width: 900,
			cls: 'fondo',
			height: 100,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
						layout: "column",
						defaults: {border: false},
						items: [{
								layout: "form",
								border: false,
								labelWidth: 150,
								items: [cmbtipocompromiso]
								}]
                                            }]
					},comcampocatcomprobante.fieldsetCatalogo
					]
	});

function irBuscar( )
{
	gridCompromisoCierre.store.removeAll();
	obtenerMensaje('procesar','','Buscando Datos');	
	var numcom   = Ext.getCmp('comprobante').getValue();
	var sistema  = Ext.getCmp('sistema').getValue();

	var JSONObject = {
			'operacion' : 'buscar_rev_cierre_compromiso',
			'numcom'    : numcom,
			'sistema'   : sistema
	}

	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request)
		{
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoMov = eval('(' + datos + ')');
			if(objetoMov!='')
			{
				if(objetoMov!='0')
				{
					if(objetoMov.raiz == null || objetoMov.raiz =='')
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
						gridCompromisoCierre.store.loadData(objetoMov);
					}
				}
				else
				{
					Ext.MessageBox.show({
						title:'Advertencia',
		 				msg:'Error al buscar datos',
		 				buttons: Ext.Msg.OK,
		 				icon: Ext.MessageBox.WARNING
		 			});
				}
			}
		},
		failure: function (result,request) 
		{ 
			Ext.MessageBox.alert('Error', 'Error de comunicación con el Servidor'); 
		}	
	});
}

function irCancelar()
{
	limpiarFormulario(fromContabilzarSPG);
	gridCompromisoCierre.store.removeAll();
}

function irProcesar()
{
	valido=true;
	var cadenaJson = "{'operacion': 'rev_contabilizar_cierre_compromisos', 'codsis':'"+sistema+"','nomven':'"+vista+"', 'arrDetalle':[";				
	var arrComprobantes = gridCompromisoCierre.getSelectionModel().getSelections();
	var total = arrComprobantes.length;
	if (total>0)
	{
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++)
		{
			if (i==0)
			{
				cadenaJson = cadenaJson +"{'codcom':'"+ arrComprobantes[i].get('comprobante')+ "','fecha':'"+ arrComprobantes[i].get('fecha')+ "','procede':'"+ arrComprobantes[i].get('procede')+ "'}";
			}
			else
			{
				cadenaJson = cadenaJson +",{'codcom':'"+ arrComprobantes[i].get('comprobante')+ "','fecha':'"+ arrComprobantes[i].get('fecha')+ "','procede':'"+ arrComprobantes[i].get('procede')+ "'}";
			}
		}

		cadenaJson = cadenaJson + ']}';
		var objdata= eval('(' + cadenaJson + ')');	
		objdata=JSON.stringify(objdata);
		var parametros = 'ObjSon='+objdata; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Contabilizaci&#243;n de Cierre de Compromisos',
					anchoLabel: 200,
					labelTotal:'Total Cierres procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total Cierres contabilizados',
					valorProcesada:arrResultado[1],
					labelError:'Total Cierres con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				
				comResultado.mostrarVentana();
				irCancelar();
			},
			failure: function (resultado,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
			}					
		});
	}
	else
	{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar al menos un documento a procesar',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}

function irLimpiar()
{
	Ext.getCmp('comprobante').setValue('');
	gridComprCauParcialmente.store.removeAll();
}
