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

//creando datastore y columnmodel para la grid de solicitudes
var reSolicitud = Ext.data.Record.create([
    {name: 'numsol'}, 
    {name: 'numrecdoc'},
    {name: 'codope'},
    {name: 'numdc'},
    {name: 'fecope'},
	{name: 'desope'},
	{name: 'codtipdoc'},
	{name: 'ced_bene'},
	{name: 'cod_pro'},
	{name: 'nomprov'},
	{name: 'nombene'},
	{name: 'apebene'}
]);

var dsSolicitud =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
});
					
var cmSolicitud = new Ext.grid.ColumnModel([
	new Ext.grid.CheckboxSelectionModel(),
	{header: "<CENTER>Nº Solicitud</CENTER>", width: 30, sortable: true, dataIndex: 'numsol'},
    {header: "<CENTER>Nº Recepcion</CENTER>", width: 30, sortable: true, dataIndex: 'numrecdoc'},
    {header: "<CENTER>Nº Nota</CENTER>", width: 30, sortable: true, dataIndex: 'numdc'},
    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecope'},
    {header: "<CENTER>Descripci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'desope'}
]);	

//creando datastore y columnmodel para la grid de notas
var gridNotas = new Ext.grid.GridPanel({
 		width:870,
 		height:250,
		frame:true,
		title:'',
		autoScroll:true,
 		border:true,
 		style: 'position:absolute;left:15px;top:220px',
 		ds: dsSolicitud,
   		cm: cmSolicitud,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
   		stripeRows: true,
  		viewConfig: {forceFit:true}
});
//fin creando grid para las notas

gridNotas.on({
	'rowcontextmenu': {
		fn: function(grid, numFila, evento){
			var registro = grid.getStore().getAt(numFila);
			//creando datastore y columnmodel para la grid de detalles presupuestarios
			var reMovPresupuestario = Ext.data.Record.create([
			    {name: 'estructura'}, 
			    {name: 'estcla'},
			    {name: 'spg_cuenta'},
			    {name: 'denominacion'},
			    {name: 'monto'},
			    {name: 'disponibilidad'}
			]);
			
			var dsMovPresupuestario =  new Ext.data.Store({
				reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovPresupuestario)
			});
								
			var cmMovPresupuestario = new Ext.grid.ColumnModel([
		        {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
		        {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
		        {header: "Cuenta", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
		        {header: "Denominacion", width: 100, sortable: true, dataIndex: 'denominacion'},
		        {header: "Monto", width: 40, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
		        {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
			]);
			//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				if(registro.get('cod_pro')=='----------')
				{
					var tit_dest="Beneficiario";
					var nombre_dest=registro.get('ced_bene')+" - "+registro.get('apebene')+", "+registro.get('nombene');
				}
				else
				{
					var tit_dest="Proveedor";
					var nombre_dest=registro.get('cod_pro')+" - "+registro.get('nomprov');
				}
			//creando componente detalle comprobante
			var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
				tituloVentana: 'Contabilizar Nota de Cr&#233;dito/D&#233;bito',
				anchoVentana: 720,
				altoVentana: 500,
				anchoFormulario: 680,
				altoFormulario:150,
				arrCampos:[{
							tipo:'textfield',
							etiqueta:'Comprobante',
							id:'cmpmod',
							valor: registro.get('numsol'),
							ancho: 200 
							},
							{
							tipo:'textfield',
							etiqueta:'Fecha',
							id:'fecmod',
							valor:registro.get('fecope'),
							ancho: 100
							},
							{
							tipo:'textarea',
							etiqueta:'Descripci&#243;n',
							id:'cmpdes',
							valor:registro.get('desope'),
							ancho: 350
							},
							{
								tipo:'textfield',
								etiqueta:tit_dest,
								id:'prosoc',
								valor:nombre_dest,
								ancho: 300
							}],
				tienePresupuesto:true,
				tituloGridPresupuestario:'Detalle Presupuestario de Gasto',
				anchoGridPG :680,
				altoGridPG :150,
				dsPresupuestoGasto: dsMovPresupuestario,
				cmPresupuestoGasto: cmMovPresupuestario,
				rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracioncxp.php',
				paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_spgncd',
																  'numsol':registro.get('numsol'),
																  'numrecdoc':registro.get('numrecdoc'),
																  'codtipdoc':registro.get('codtipdoc'),
																  'ced_bene':registro.get('ced_bene'),
																  'cod_pro':registro.get('cod_pro'),
																  'codope':registro.get('codope'),
																  'numdc':registro.get('numdc'),
																  'fecope':registro.get('fecope')}),
				tieneContable: true,
				anchoGridCO :680,
				altoGridCO :120,
				paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_scgncd',
															   'numsol':registro.get('numsol'),
															   'numrecdoc':registro.get('numrecdoc'),
															   'codtipdoc':registro.get('codtipdoc'),
															   'ced_bene':registro.get('ced_bene'),
															   'cod_pro':registro.get('cod_pro'),
															   'codope':registro.get('codope'),
															   'numdc':registro.get('numdc')})
															   
			});
			//fin creando componente detalle comprobante
			
			comDetalleModificacion.mostrarVentana();
		}
	}
});

			
//creando store para la operacion
var operacion = [
	['Nota Crédito','NC'],
	['Nota Débito','ND']
]; 

var stOperacion = new Ext.data.SimpleStore({
	fields : [ 'etiqueta', 'valor' ],
	data : operacion
});
//fin creando store para el combo tipo iva

//creando objeto combo operacion
var cmbOperacion = new Ext.form.ComboBox({
	store : stOperacion,
	fieldLabel : 'Operaci&#243;n',
	labelSeparator : '',
	editable : false,
	displayField : 'etiqueta',
	valueField : 'valor',
	id : 'operacion',
	width:130,
	typeAhead: true,
	emptyText:'Seleccione',
	triggerAction:'all',
	forceselection:true,
	mode:'local'
});
	
//creando funcion que construye formulario de busqueda
var	fromBusquedaCXP = new Ext.form.FieldSet({
		title:'Datos de la Nota de Crédito/Débito',
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
								fieldLabel: 'N&#250;mero Solicitud',
								labelSeparator :'',
								id: 'numsol',
								autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
								width: 130,
								allowBlank:false
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:50px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 150,
						items: [{
								xtype: 'textfield',
								fieldLabel: 'N&#250;mero Recepci&#243;n',
								labelSeparator :'',
								id: 'numrecdoc',
								autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
								width: 130,
								allowBlank:true
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:80px',
				items: [{
						layout: "form",
						border: true,
						labelWidth: 150,
						items: [cmbOperacion]
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
								fieldLabel:"Fecha de Registro",
								allowBlank:true,
								labelSeparator :'',
								width:130,
								id:"fecope",
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
								fieldLabel:"Fecha de Aprobaci&#243;n",
								allowBlank:true,
								labelSeparator :'',
								width:130,
								id:"fecaprnc",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
							}]
						}]
				}]
});

barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarCXP = new Ext.FormPanel({
		applyTo: 'formularioCXP',
		width: 920,
		height: 600,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:35px;',
		title: "<H1 align='center'>Contabilizar Notas de Cr&#233;dito/D&#233;bito</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaCXP,
		        gridNotas
		        ]
	});
	
	fromContabilzarCXP.doLayout();
});

function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	var JSONObject = {
		'operacion' : 'buscar_por_contabilizar_ncd',
		'numsol'    : Ext.getCmp('numsol').getValue(),
		'numrecdoc' : Ext.getCmp('numrecdoc').getValue(),
		'codope'    : Ext.getCmp('operacion').getValue(),
		'fecope'    : Ext.getCmp('fecope').getValue(),
		'fecaprnc'  : Ext.getCmp('fecaprnc').getValue()
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
				
				if(objetoCxp!='0'){
					if(objetoCxp.raiz == null || objetoCxp.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridNotas.store.removeAll();
					}
					else{
						dsSolicitud.loadData(objetoCxp);
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
				
				//dsSolicitud.loadData(objetoCxp);
			}
		}	
	});
}

function irCancelar(){
	limpiarFormulario(fromBusquedaCXP);
	gridNotas.store.removeAll();	
}

function irProcesar(){
	var cadenaJson = "{'operacion': 'contabilizar_ncd', 'notas':[";				
	var arrSolicitud = gridNotas.getSelectionModel().getSelections();
	var	total = arrSolicitud.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'numsol':'"+ arrSolicitud[i].get('numsol')+ "','numrecdoc':'"+ arrSolicitud[i].get('numrecdoc')+ "'," +
						                 "'codtipdoc':'"+ arrSolicitud[i].get('codtipdoc')+ "','ced_bene':'"+ arrSolicitud[i].get('ced_bene')+ "'," +
								         "'cod_pro':'"+ arrSolicitud[i].get('cod_pro')+ "','numdc':'"+ arrSolicitud[i].get('numdc')+ "','codope':'"+ arrSolicitud[i].get('codope')+ "'}";
			}
			else {
				cadenaJson = cadenaJson +",{'numsol':'"+ arrSolicitud[i].get('numsol')+ "','numrecdoc':'"+ arrSolicitud[i].get('numrecdoc')+ "'," +
						                 "'codtipdoc':'"+ arrSolicitud[i].get('codtipdoc')+ "','ced_bene':'"+ arrSolicitud[i].get('ced_bene')+ "'," +
								         "'cod_pro':'"+ arrSolicitud[i].get('cod_pro')+ "','numdc':'"+ arrSolicitud[i].get('numdc')+ "','codope':'"+ arrSolicitud[i].get('codope')+ "'}";
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
					tituloVentana: 'Resultado Contabilizaci&#243;n de Notas de Cr&#233;dito/D&#233;bito',
					anchoLabel: 200,
					labelTotal:'Total Notas de Cr&#233;dito/D&#233;bito procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total Notas de Cr&#233;dito/D&#233;bito contabilizadas',
					valorProcesada:arrResultado[1],
					labelError:'Total Notas de Cr&#233;dito/D&#233;bito con error',
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