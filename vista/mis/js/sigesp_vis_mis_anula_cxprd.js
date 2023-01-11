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

var reRecepcion = Ext.data.Record.create([
    {name: 'numrecdoc'},
    {name: 'codtipdoc'},
    {name: 'ced_bene'},
    {name: 'cod_pro'},
    {name: 'dencondoc'},
    {name: 'fecregdoc'},
    {name: 'tipproben'},
    {name: 'nombre'},
    {name: 'conanurd'}
]);

var dsRecepcion =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reRecepcion)
});
					
var cmRecepcion = new Ext.grid.ColumnModel([
	new Ext.grid.CheckboxSelectionModel(),
    {header: "<CENTER>Nº Recepci&#243;n</CENTER>", width: 30, sortable: true, dataIndex: 'numrecdoc'},
    {header: "<CENTER>Proveedor/Beneficiario</CENTER>", width: 30, sortable: true, dataIndex: 'nombre'},
    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecregdoc'},
    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'dencondoc'},
    {header: "<CENTER>Concepto de Anulacion</CENTER>", width: 60, sortable: true, dataIndex: 'conanurd',editor : new Ext.form.TextArea({allowBlank : false,autoCreate: {tag: 'textarea', type: 'text', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()¿?¡-+*[]{}');"}})}
]);	
//creando datastore y columnmodel para la grid de Anulacion de recepciones

var gridRecepcion = new Ext.grid.EditorGridPanel({
	width:870,
	height:250,
	frame:true,
	title:'',
	autoScroll:true,
	border:true,
	style: 'position:absolute;left:15px;top:220px',
	ds: dsRecepcion,
	cm: cmRecepcion,
	sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	stripeRows: true,
	viewConfig: {forceFit:true}
});
//fin creando grid para los reintegros

gridRecepcion.on({
	'rowcontextmenu': {
		fn: function(grid, numFila, evento){
			var registro = grid.getStore().getAt(numFila);
			
			//creando datastore y columnmodel para la grid de detalles presupuestarios
			var reMovPresupuestario = Ext.data.Record.create([
			    {name: 'estructura'}, 
			    {name: 'estcla'},
			    {name: 'spg_cuenta'},
			    {name: 'monto'},
			    {name: 'disponibilidad'}
			]);
			
			var dsMovPresupuestario =  new Ext.data.Store({
				reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovPresupuestario)
			});
								
			var cmMovPresupuestario = new Ext.grid.ColumnModel([
		        {header: "Estructura", width: 35, sortable: true, dataIndex: 'estructura'},
		        {header: "Estatus", width: 15, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
		        {header: "Cuenta", width: 25, sortable: true, dataIndex: 'spg_cuenta'},
		        {header: "Monto", width: 20, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
		        {header: "Disponibilidad", width: 15, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
			]);
			//fin creando datastore y columnmodel para la grid de detalles presupuestarios
			
			//creando componente detalle comprobante
			var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
				tituloVentana: 'Anular Recepci&#243;n de Documentos',
				anchoVentana: 600,
				altoVentana: 500,
				anchoFormulario: 580,
				altoFormulario:150,
				arrCampos:[{
					tipo:'textfield',
					etiqueta:'Comprobante',
					id:'cmpmod',
					valor: registro.get('numrecdoc'),
					ancho: 200 
				},{
					tipo:'textfield',
					etiqueta:'Fecha',
					id:'fecmod',
					valor:registro.get('fecregdoc'),
					ancho: 100
				},{
					tipo:'textarea',
					etiqueta:'Descripci&#243;n',
					id:'cmpdes',
					valor:registro.get('dencondoc'),
					ancho: 350
				}],
				tienePresupuesto:true,
				tituloGridPresupuestario:'Detalle Presupuestario de Gasto',
				anchoGridPG :580,
				altoGridPG :150,
				dsPresupuestoGasto: dsMovPresupuestario,
				cmPresupuestoGasto: cmMovPresupuestario,
				rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracioncxp.php',
				paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_spgrec',
																  'numrecdoc':registro.get('numrecdoc'),
																  'codtipdoc':registro.get('codtipdoc'),
																  'ced_bene':registro.get('ced_bene'),
																  'cod_pro':registro.get('cod_pro'),
																  'fecreg':registro.get('fecregdoc')}),
				tieneContable: true,
				anchoGridCO :550,
				altoGridCO :120,
				paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_scgrec',
															   'numrecdoc':registro.get('numrecdoc'),
															   'codtipdoc':registro.get('codtipdoc'),
															   'ced_bene':registro.get('ced_bene'),
															   'cod_pro':registro.get('cod_pro')})
															   
			});
			//fin creando componente detalle comprobante
			
			comDetalleModificacion.mostrarVentana();
		}
	}
});

			
//creando store para el destino
var destino = [
	['Proveedor','P'],
	['Beneficiario','B']
];

var stdestino = new Ext.data.SimpleStore({
	fields : [ 'etiqueta', 'valor' ],
	data : destino
});
//fin creando store para el combo destino

//creando objeto combo tipo iva
var cmbdestino = new Ext.form.ComboBox({
	store : stdestino,
	fieldLabel : 'Destino ',
	labelSeparator : '',
	editable : false,
	displayField : 'etiqueta',
	valueField : 'valor',
	id : 'cmbdestino',
	width:130,
	typeAhead: true,
	emptyText:'Seleccione',
	triggerAction:'all',
	forceselection:true,
	mode:'local',
	listeners: {'select':CatalogoDestino}
});
//-------------------------------------------------------------------------------------------------------------------------					
//-------------------------------------------------------------------------------------------------------------------------	
		
//creando funcion que construye formulario principal Contabilizar
var	fromBusquedaCXP = new Ext.form.FieldSet({  
			title:'Datos de la Orden de Pago',
			style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 190,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Número Recepci&#243;n',
									labelSeparator :'',
									id: 'numrecdoc',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 130
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:50px',
					items: [{
							layout: "form",
							border: true,
							labelWidth: 150,
							items: [cmbdestino]
							},
							{
							layout: "form",
							border: false,
							labelWidth: 10,
							items: [{
									xtype: 'textfield',
									fieldLabel: '',
									labelSeparator :'',
									id: 'cod_pro',
									disabled:true,
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 150
								}]
							},
							{
							layout: "form",
							border: false,
							labelWidth: 10,
							items: [{
									xtype: 'textfield',
									fieldLabel: '',
									labelSeparator :'',
									id: 'nompro',
									disabled:true,
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 300
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:80px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'datefield',
									fieldLabel:"Fecha de Registro",
									labelSeparator :'',
									width:130,
									id:"fecregdoc",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:110px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'datefield',
									fieldLabel:"Fecha de Aprobaci&#243;n",
									labelSeparator :'',
									width:130,
									id:"fecaprdoc",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:140px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'datefield',
									fieldLabel:"Fecha de Anulaci&#243;n",
									labelSeparator :'',
									allowBlank:true,
									width:130,
									value:obtenerFechaActual(),
									id:"fecanurd",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
								}]
							}]
					}]
});


/***********************************************************************************
/***********************************************************************************
* @Función para buscar los Proveedores o Beneficiarios segun sea el caso
* @parametros: 
* @retorno:
* @fecha de creación: 04/07/2012.
* @autor: Ing. Luis Anibal Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function CatalogoDestino()
	{
		valor=Ext.getCmp('cmbdestino').getValue();
		if(valor=="P")
		{
			//creando datastore y columnmodel para el catalogo de agencias
			var registro_parametro = Ext.data.Record.create([
								{name: 'cod_pro'},
								{name: 'nompro'}
				]);
			
			var dsparametro =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_parametro)
				});
								
			var colmodelcatparametro = new Ext.grid.ColumnModel([
								{header: "Codigo", width: 20, sortable: true,   dataIndex: 'cod_pro'},
								{header: "Nombre", width: 40, sortable: true, dataIndex: 'nompro'}
				]);
			//fin creando datastore y columnmodel para el catalogo de agencias
			
			comcatproveedor = new com.sigesp.vista.comCatalogo({
				titvencat: 'Catalogo de Proveedores',
				anchoformbus: 450,
				altoformbus:130,
				anchogrid: 450,
				altogrid: 400,
				anchoven: 500,
				altoven: 400,
				datosgridcat: dsparametro,
				colmodelocat: colmodelcatparametro,
				arrfiltro:[{etiqueta:'Codigo',id:'copro',valor:'codpro'},
						   {etiqueta:'Descripcion',id:'nopro',valor:'nompro'}],
				rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
				parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'catalogo'}),
				tipbus:'L',
				setdatastyle:'F',
				formulario:fromBusquedaCXP
			});

			
			comcatproveedor.mostrarVentana();
		}
		else
		{
			//creando datastore y columnmodel para el catalogo de agencias
			var registro_parametro = Ext.data.Record.create([
								{name: 'cod_pro'},
								{name: 'nompro'}
				]);
			
			var dsparametro =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_parametro)
				});
								
			var colmodelcatparametro = new Ext.grid.ColumnModel([
								{header: "Codigo", width: 20, sortable: true,   dataIndex: 'cod_pro'},
								{header: "Nombre", width: 40, sortable: true, dataIndex: 'nompro'}
				]);
			//fin creando datastore y columnmodel para el catalogo de agencias
			
			comcatproveedor = new com.sigesp.vista.comCatalogo({
				titvencat: 'Catalogo de Beneficiario',
				anchoformbus: 450,
				altoformbus:130,
				anchogrid: 450,
				altogrid: 400,
				anchoven: 500,
				altoven: 400,
				datosgridcat: dsparametro,
				colmodelocat: colmodelcatparametro,
				arrfiltro:[{etiqueta:'Codigo',id:'copro',valor:'cod_pro'},
						   {etiqueta:'Nombre',id:'conom',valor:'nompro'}],
				rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
				parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscarBeneficiarios'}),
				tipbus:'L',
				setdatastyle:'F',
				formulario:fromBusquedaCXP
			});

			
			comcatproveedor.mostrarVentana();
		}
	}

barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//validando si la configuracion permite integrar recepciones
	var JSONObject = {
		'operacion' : 'validar_recepciones'
	}
	
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracioncxp.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			var respuesta = resultado.responseText;
			if(respuesta == '0'){
				Ext.MessageBox.show({
					title:'Mensaje',
					msg:'La empresa no esta configurada para integrar recepci&#243;n de documentos',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO,
					fn:function(){
						location.href = 'sigesp_vis_mis_inicio.html';
					}
				});
				
			}
		}	
	});
	//fin validando si la configuracion permite integrar recepciones
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarCXP = new Ext.FormPanel({
		applyTo: 'formularioCXP',
		width: 920,
		height: 500,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:35px;',
		title: "<H1 align='center'>Anular Recepci&#243;n de Documentos</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaCXP,gridRecepcion]
	});
	
	fromContabilzarCXP.doLayout();
});

function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	var fecregdoc = '';
	if(Ext.getCmp('fecregdoc').getValue()!=''){
		fecregdoc = Ext.getCmp('fecregdoc').getValue().format(Date.patterns.bdfecha);
	}
	var fecaprdoc = '';
	if(Ext.getCmp('fecaprdoc').getValue()!=''){
		fecaprdoc = Ext.getCmp('fecaprdoc').getValue().format(Date.patterns.bdfecha);
	}
	
	//Ext.getCmp('fecaprdoc').getValue().format(Date.patterns.bdfecha)
	var numrecdoc   = Ext.getCmp('numrecdoc').getValue();
	var destino     = Ext.getCmp('cmbdestino').getValue();
	var cod_pro     = Ext.getCmp('cod_pro').getValue();
	
	var JSONObject = {
		'operacion' : 'buscar_por_anular_rec',
		'numrecdoc' : numrecdoc,
		'tipo'      : destino,
		'codigo'    : cod_pro,
		'fecreg'    : fecregdoc,
		'fecapr'    : fecaprdoc
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracioncxp.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoCxp = eval('(' + datos + ')');
			if(objetoCxp!=''){
				dsRecepcion.loadData(objetoCxp);
			}
		}	
	});
}

function irCancelar(){
	limpiarFormulario(fromBusquedaCXP);
	gridRecepcion.store.removeAll();	
}

function irProcesar(){
	var cadenaJson = "{'operacion': 'anular_rec', 'fechaAnula':'"+Ext.getCmp('fecanurd').getValue().format(Date.patterns.bdfecha)+"', 'recepciones':[";				
	var arrRecepcion = gridRecepcion.getSelectionModel().getSelections();
	var	total = arrRecepcion.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if(arrRecepcion[i].get('conanurd')!=''){
				if (i==0) {
					cadenaJson = cadenaJson +"{'numrecdoc':'"+ arrRecepcion[i].get('numrecdoc')+ "','codtipdoc':'"+ arrRecepcion[i].get('codtipdoc')+ "'," +
											 "'ced_bene':'"+ arrRecepcion[i].get('ced_bene')+ "','cod_pro':'"+ arrRecepcion[i].get('cod_pro')+ "'," +
											 "'conanurd':'"+ arrRecepcion[i].get('conanurd')+ "'}";
				}
				else {
					cadenaJson = cadenaJson +",{'numrecdoc':'"+ arrRecepcion[i].get('numrecdoc')+ "','codtipdoc':'"+ arrRecepcion[i].get('codtipdoc')+ "'," +
											 "'ced_bene':'"+ arrRecepcion[i].get('ced_bene')+ "','cod_pro':'"+ arrRecepcion[i].get('cod_pro')+ "'," +
											 "'conanurd':'"+ arrRecepcion[i].get('conanurd')+ "'}";
				}
			}
			else {
				Ext.MessageBox.show({
					title:'Advertencia',
					msg:'Debe indicar el concepto de anulaci&#243;n de la recepci&#243;n de documentos '+arrRecepcion[i].get('numrecdoc'),
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});
				
			 	return false;
			}
		}
		
		cadenaJson = cadenaJson + ']}';
		var objdata= eval('(' + cadenaJson + ')');	
		objdata=JSON.stringify(objdata);
		var parametros = 'ObjSon='+objdata; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracioncxp.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) {
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Anulaci&#243;n de Recepci&#243;n de Documentos',
					anchoLabel: 230,
					labelTotal:'Total Recepci&#243;n de Documentos procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total Recepci&#243;n de Documentos anuladas',
					valorProcesada:arrResultado[1],
					labelError:'Total Recepci&#243;n de Documentos con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				comResultado.mostrarVentana();
			},
			failure: function (result,request){
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Informaci&#243;n'); 
			}					
		});
		irCancelar();
	}
	else{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar al menos un documento a procesar',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}