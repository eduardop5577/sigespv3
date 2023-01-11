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

barraherramienta    = true;
//var fechacontabil = new Date(Ext.getCmp('fecaconta').getValue());
var cmbtipcomprobante = null;
var comcampocatproveedor = null;
var	fromBusquedaRevSNO = null;
var gridSno = null;
var date = new Date();
var dia = date.getDate();
var mes = date.getMonth()+1;
var anio = date.getFullYear();
if (mes < 10)
{
	mes="0"+mes;
}
if (dia < 10)
{
	dia="0"+dia;
}

var fecha_hoy=dia+"/"+mes+"/"+anio;
//-----------------------------------------------------------------------------------------------------------------------	
//creando datastore y columnmodel para la grid de solicitudes
var reSno = Ext.data.Record.create([
    {name: 'codcom'}, 
    {name: 'descripcion'},
    {name: 'fechaconta'},
    {name: 'fechaanula'},
    {name: 'cod_pro'},
    {name: 'ced_bene'},
    {name: 'tipo_destino'},
    {name: 'operacion'},
    {name: 'codcomapo'},
    {name: 'nompro'},
    {name: 'nombene'},
    {name: 'apebene'},
    {name: 'fechasper'},
    {name: 'codestpro1'},
    {name: 'codestpro3'},
    {name: 'codestpro3'},
    {name: 'codestpro4'},
    {name: 'codestpro5'},
    {name: 'estcla'}
]);

var dsSno =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSno)
});

var cmSno = new Ext.grid.ColumnModel([
	  new Ext.grid.CheckboxSelectionModel(),
	  {header: "<CENTER>N� Comprobante</CENTER>", width: 50, sortable: true, dataIndex: 'codcom'},
	  {header: "<CENTER>Concepto</CENTER>", width: 150, sortable: true, dataIndex: 'descripcion'}
]);
//creando datastore y columnmodel para la grid de reintegros

//creando grid para los reintegros
gridSno = new Ext.grid.GridPanel({
	width:850,
	height:230,
	frame:true,
	title:'',
	style: 'position:absolute;left:15px;top:190px',
	autoScroll:true,
	border:true,
	ds: dsSno,
	cm: cmSno,
	sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	stripeRows: true,
	viewConfig: {forceFit:true}
});
//fin creando grid para los reintegros
//-----------------------------------------------------------------------------------------------------------------------	
//Creando la ventana emergente de los detalles
gridSno.on({
	'rowcontextmenu': {
	fn: function(grid, numFila, evento){
	var registro = grid.getStore().getAt(numFila);

	//creando datastore y columnmodel para la grid de detalles presupuestarios
	var reSno = Ext.data.Record.create([
	                                    {name: 'estructura'}, 
	                                    {name: 'estcla'},
	                                    {name: 'spg_cuenta'},
	                                    {name: 'monto'},
	                                    {name: 'disponibilidad'}
	                                    ]);

	var dsSno =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSno)
	});

	var cmSno = new Ext.grid.ColumnModel([
	                                      {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
	                                      {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
	                                      {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
	                                      {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
	                                      {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
	                                      ]);
	//fin creando datastore y columnmodel para la grid de detalles presupuestarios

	if(registro.get('tipo_destino')=='B')
	{
		var tit_dest="Beneficiario";
		var nombre_dest=registro.get('ced_bene')+" - "+registro.get('apebene')+", "+registro.get('nombene');
	}
	else
	{
		var tit_dest="Proveedor";
		var nombre_dest=registro.get('cod_pro')+" - "+registro.get('nompro');
	}
	//dia1 = fecha1.substr(0,2);
	if(registro.get('operacion')=='O')
	{
		var tip_conta="COMPROMETE";
	}
	if(registro.get('operacion')=='OC')
	{
		var tip_conta="COMPROMETE Y CAUSA";
	}
	if(registro.get('operacion')=='OCP')
	{
		var tip_conta="COMPROMETE, CAUSA Y PAGA";
	}
	if(registro.get('operacion')=='CP')
	{
		var tip_conta="CAUSAR Y PAGAR";
	}
	if(registro.get('operacion')=='DC')
	{
		var tip_conta="DEVENGADO Y COBRADO";
	}
	if(registro.get('operacion')=='')
	{
		var tip_conta="CONTABLE";
	}
	//creando componente detalle comprobante
	var comSno = new com.sigesp.vista.comDetalleComprobante({
		tituloVentana: 'Detalle Comprobante',
		anchoVentana: 600,
		altoVentana: 500,
		anchoFormulario: 580,
		altoFormulario:150,
		arrCampos:[{
					tipo:'textfield',
					etiqueta:'Comprobante',
					id:'ncomp',
					valor: registro.get('codcom'),
					ancho: 200 
					},
					{	
					tipo:'textfield',
					etiqueta:'Fecha',
					id:'fhasper',
					valor:registro.get('fechasper'),
					ancho: 100
					},
					{
					tipo:'textfield',
					etiqueta:'Descripci&#243;n',
					id:'descomp',
					valor:registro.get('descripcion'),
					ancho: 350
					},
					{
					tipo:'textfield',
					etiqueta:tit_dest,
					id:'tip_des',
					valor:nombre_dest,
					ancho: 350
					},
					{
					tipo:'textfield',
					etiqueta:'Contabilizaci&#243;n',
					id:'tip',
					valor:tip_conta,
					ancho: 350
			
					}],
		tienePresupuesto:true,
		tituloGridPresupuestario:'Detalle Presupuestario',
		anchoGridPG :580,
		altoGridPG :150,
		dsPresupuestoGasto: dsSno,
		cmPresupuestoGasto: cmSno,
		rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsno.php',
		paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto_ing',
			'codcom':registro.get('codcom'),
			'codcomapo':registro.get('codcomapo')}),
			tieneContable: true,
			anchoGridCO :550,
			altoGridCO :100,
			paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable',
				'codcom':registro.get('codcom'),
				'codcomapo':registro.get('codcomapo')})

	});
	//fin creando componente detalle comprobante

	comSno.mostrarVentana();
}
}
});

//-----------------------------------------------------------------------------------------------------------------------	
//creando store para la tipo compra
var tipcomprobante = 	[
                     	 ['--Seleccione--',''],
                     	 ['Aporte','A'],
                     	 ['N�mina','N'],
                     	 ['Ingresos','I'],
                     	 ['Prestaci�n Antiguedad','P'],
                     	 ['Intereses Prestaci�n Antiguedad','K'],
                     	 ['Liquidaci�n','L'],
                     	 ['Anticipo de Prestaciones','X']
                     	 ]; // Arreglo que contiene los Documentos que se pueden controlar

var sttipcomprobante = new Ext.data.SimpleStore({
	fields : [ 'etiqueta', 'valor' ],
	data : tipcomprobante
});
//fin creando store para el combo tipo compra

//creando objeto combo tipo compra
var cmbtipcomprobante = new Ext.form.ComboBox({
	store : sttipcomprobante,
	fieldLabel : 'Tipo de Comprobante ',
	labelSeparator : '',
	editable : false,
	displayField : 'etiqueta',
	valueField : 'valor',
	id : 'tipnom',
	width:200,
	typeAhead: true,
	triggerAction:'all',
	forceselection:true,
	binding:true,
	mode:'local',
	emptyText : '--Seleccione--'
});

//fin creando objeto tipo compra
//-------------------------------------------------------------------------------------------------------------------------	
//Creando el campo de nomina
var reg_nomina = Ext.data.Record.create([
                                         {name: 'codnom'},
                                         {name: 'desnom'}
                                         ]);

var dsnomina =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({
		root: 'raiz',             
		id: "id"},reg_nomina)
});

var colmodelnomina = new Ext.grid.ColumnModel([
                                               {header: "Codigo", width: 20, sortable: true,   dataIndex: 'codnom'},
                                               {header: "Denominacion", width: 40, sortable: true, dataIndex: 'desnom'}
                                               ]);
//fin creando datastore y columnmodel para el catalogo de bancos sigecof

//componente campocatalogo para el campo nomina
comcampocatnomina = new com.sigesp.vista.comCampoCatalogo({
	titvencat: 'Catalogo de Nomina',
	anchoformbus: 450,
	altoformbus:100,
	anchogrid: 450,
	altogrid: 400,
	anchoven: 500,
	altoven: 400,
	datosgridcat: dsnomina,
	colmodelocat: colmodelnomina,
	rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_integracionsno.php',
	parametros: "ObjSon={'operacion': 'catalogo_nomina_rev'}",
	arrfiltro:[{etiqueta:'C�digo',id:'mcodnom',valor:'codnom'},
	           {etiqueta:'Denominacion',id:'mdesnom',valor:'desnom'}],
	           posicion:'position:absolute;left:5px;top:10px',
	           tittxt:'N�mina',
	           idtxt:'codnom',
	           campovalue:'codnom',
	           anchoetiquetatext:200,
	           anchotext:135,
	           anchocoltext:0.50,
	           idlabel:'desnom',
	           labelvalue:'desnom',
	           anchocoletiqueta:0.45,
	           anchoetiqueta:400,
	           anchofieldset: 700,
	           tipbus:'L',
	           hiddenvalue:'',
	           defaultvalue:'',
	           allowblank:false
});
//fin componente para el campo de nomina
//-------------------------------------------------------------------------------------------------------------------------	
//Creando el campo catalogo para llamar a el periodo
var reg_periodo = Ext.data.Record.create([
                                          {name: 'codnom'},
                                          {name: 'codperi'},
                                          {name: 'fecdesper'},
                                          {name: 'fechasper'}

                                          ]);

var dsperiodo =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({
		root: 'raiz',             
		id: "id"},reg_periodo)
});

var colmodelperiodo = new Ext.grid.ColumnModel([
                                                {header: "Per�odo", width: 20, sortable: true,   dataIndex: 'codperi'},
                                                {header: "Fecha Inicio", width: 60, sortable: true,   dataIndex: 'fecdesper'},
                                                {header: "Fecha Culminaci�n", width: 60, sortable: true, dataIndex: 'fechasper'}
                                                ]);
//fin del campo de proveedores

//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
comcampocatperiodo = new com.sigesp.vista.comCampoCatalogo({
	titvencat: 'Catalogo de Periodos',
	anchoformbus: 450,
	altoformbus:0,
	anchogrid: 450,
	altogrid: 600,
	anchoven: 500,
	altoven: 400,
	datosgridcat: dsperiodo,
	colmodelocat: colmodelperiodo,
	rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_integracionsno.php',
	parametros: "ObjSon={'operacion': 'catalogo_periodo_rev'",
	arrfiltro:[{etiqueta:'C�digo',id:'mcodnom',valor:'codnom'}],
	posicion:'position:absolute;left:5px;top:40px',
	tittxt:'Per�odo',
	idtxt:'codperi',
	campovalue:'codperi',
	anchoetiquetatext:200,
	anchotext:135,
	anchocoltext:0.50,
	anchocoletiqueta:0.45,
	anchoetiqueta:400,
	anchofieldset: 700,
	tipbus:'LF',
	arrtxtfiltro:['codnom'],
	hiddenvalue:'',
	defaultvalue:'',
	allowblank:false

});

//fin componente para el campo de cuentas contables para las solicitudes a pagar

//-------------------------------------------------------------------------------------------------------------------------	
//creando funcion que construye formulario principal para contabilizaci�n
fromBusquedaRevSNO = new Ext.form.FieldSet({ 
	    title:'Datos de la N�mina',
	    style: 'position:absolute;left:15px;top:5px',
		border:true,
		width: 850,
		cls: 'fondo',
		height: 160,
		items:[
		       comcampocatnomina.fieldsetCatalogo,
		       comcampocatperiodo.fieldsetCatalogo,
		       {
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:80px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 200,
						items: [cmbtipcomprobante]
						}]
		       },
		       {
	        	layout: "column",
	        	defaults: {border: false},
	        	style: 'position:absolute;left:15px;top:110px',
	        	items: [{
		        		layout: "form",
		        		border: false,
		        		labelWidth: 200,
		        		items: [{
			        			xtype: 'textfield',
			        			fieldLabel: 'Comprobante',
			        			labelSeparator :'',
			        			id: 'codcom',
			        			autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
			        			width: 130,
			        			listeners: {
			        				'onClick': function(){
			        				}			
			        			},
			        			allowBlank:false
			        		}]
		        	}]
		        }]
});



//--------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevContabilzarSNO = new Ext.FormPanel({
		applyTo: 'formulario_revSNO',
		width: 905,
		height: 485,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reversar Contabilizaci�n de N�mina</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaRevSNO,gridSno]
	});

	fromRevContabilzarSNO.doLayout();
});

function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	var JSONObject = {
			'operacion'  : 'buscar_nominas',
			'codcom'     : Ext.getCmp('codcom').getValue(),
			'codnom'     : Ext.getCmp('codnom').getValue(),
			'codperi'    : Ext.getCmp('codperi').getValue(),
			'tipnom'     : Ext.getCmp('tipnom').getValue(),
			'estatus'    : '1'
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsno.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoSno = eval('(' + datos + ')');
			if(objetoSno!=''){
				if(objetoSno!='0'){
					if(objetoSno.raiz == null || objetoSno.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridSno.store.removeAll();
					}
					else{
						gridSno.store.loadData(objetoSno);
					}
				}
				else{
					Ext.MessageBox.show({
						title:'Advertencia',
		 				msg:'Debe configurar en Empresa los digitos de las cuentas de gastos',
		 				buttons: Ext.Msg.OK,
		 				icon: Ext.MessageBox.WARNING
		 			});
				}
			}
		},
		failure: function (result,request) 
		{ 
			Ext.MessageBox.alert('Error', 'Error de comunicaci�n con el Servidor'); 
		}	
	});	
}

function irCancelar(){
	limpiarFormulario(fromBusquedaRevSNO);
	gridSno.store.removeAll();
}

function irProcesar(){
	grid = gridSno.getSelectionModel().getSelections();
	cadenajson = "{'operacion':'rev_contabilizar','codsis':'"+sistema+"','nomven':'"+vista+"', 'arrDetalle':[";
	total = grid.length;
	if(total>0)
	{
		for(var i=0; i<total; i++)
		{
			for (i=0; i<total; i++)
			{ 
				if (i==0) 
				{
					cadenajson += "{'codcom':'"+grid[i].get('codcom')+"','fechaconta':'"+grid[i].get('fechaconta')+"'}";                
				}
				else
				{
					cadenajson += ",{'codcom':'"+grid[i].get('codcom')+"','fechaconta':'"+grid[i].get('fechaconta')+"'}";                
				}
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsno.php',
			params : parametros,
			method: 'POST',
			timeout : 99999999999,
			success: function (resultado, request) {
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado de N�minas Reversadas',
					anchoLabel: 200,
					labelTotal:'Total n�minas procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total n�minas contabilizadas',
					valorProcesada:arrResultado[1],
					labelError:'Total n�minas con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				comResultado.mostrarVentana();
				irCancelar();
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Informaci�n'); 
			}					
		});
	}
	else{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar al menos un documento a procesar !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}