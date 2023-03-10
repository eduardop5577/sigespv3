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

//creando datastore y columnmodel para la grid de Anulacion de solicitudes
var reSolicitud = Ext.data.Record.create([
    {name: 'numsol'}, 
    {name: 'fecemisol'},
    {name: 'consol'},
    {name: 'conanusop'},
	{name: 'cod_pro'},
	{name: 'nompro'},
	{name: 'nombene'},
	{name: 'apebene'},
	{name: 'ced_bene'},
	{name: 'tipproben'}
]);

var dsSolicitud =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
});
					
var cmSolicitud = new Ext.grid.ColumnModel([
	new Ext.grid.CheckboxSelectionModel(),
    {header: "<CENTER>N? Solicitud</CENTER>", width: 30, sortable: true, dataIndex: 'numsol'},
    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecemisol'},
    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'consol'},
    {header: "<CENTER>Concepto de Anulaci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'conanusop',editor : new Ext.form.TextArea({allowBlank : false,autoCreate: {tag: 'textarea', type: 'text', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()???-+*[]{}');"}})}
]);

//creando datastore y columnmodel para la grid de reintegros
var gridSolicitud = new Ext.grid.EditorGridPanel({
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
//fin creando grid para los reintegros
gridSolicitud.on({
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
			
				if(registro.get('tipproben')=='B')
				{
					var tit_dest="Beneficiario";
					var nombre_dest=registro.get('ced_bene')+" - "+registro.get('apebene')+", "+registro.get('nombene');
				}
				else
				{
					var tit_dest="Proveedor";
					var nombre_dest=registro.get('cod_pro')+" - "+registro.get('nompro');
				}
			//creando componente detalle comprobante
			var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
				tituloVentana: 'Anular Solicitud de Orden de Pago',
				anchoVentana: 600,
				altoVentana: 500,
				anchoFormulario: 580,
				altoFormulario:150,
				arrCampos:[{
					tipo:'textfield',
					etiqueta:'Comprobante',
					id:'cmpmod',
					valor: registro.get('numsol'),
					ancho: 200 
				},{
					tipo:'textfield',
					etiqueta:'Fecha',
					id:'fecmod',
					valor:registro.get('fecemisol'),
					ancho: 100
				},{
					tipo:'textarea',
					etiqueta:'Descripci&#243;n',
					id:'cmpdes',
					valor:registro.get('consol'),
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
				anchoGridPG :580,
				altoGridPG :150,
				dsPresupuestoGasto: dsMovPresupuestario,
				cmPresupuestoGasto: cmMovPresupuestario,
				rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracioncxp.php',
				paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_spgsop',
																  'numsol':registro.get('numsol'),
																  'fecreg':registro.get('fecemisol')}),
				tieneContable: true,
				anchoGridCO :550,
				altoGridCO :120,
				paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_scgsop',
															   'numsol':registro.get('numsol')})
															   
			});
			//fin creando componente detalle comprobante
			
			comDetalleModificacion.mostrarVentana();
		}
	}
});
//-------------------------------------------------------------------------------------------------------------------------					
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

//creando objeto combo destino
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
	binding:true,
	mode:'local',
	listeners: {'select':CatalogoDestino}
});
//-------------------------------------------------------------------------------------------------------------------------					
	//creando funcion que construye formulario principal Anulacion de CXP
	var	fromAnulaCXP = new Ext.form.FieldSet({
			title:'Datos de la Orden de Pago',
			style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 215,
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
						fieldLabel: 'N?mero Solicitud',
						labelSeparator :'',
						id: 'numsol',
						autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
						width: 130,
						listeners: {
							'onClick': function(){
						}			
						},
						allowBlank:false
					}]
				}]
			},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:50px',
				items: [{
					layout: "form",
					border: true,
					labelWidth: 150,
					items: [cmbdestino]

				},{
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
						width: 130,
						listeners: {
							'onClick': function(){
						}			
						},
						allowBlank:false
					}]
				},{
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
						width: 300,
						listeners: {
							'onClick': function(){
						}			
						},
						allowBlank:false
					}]
				}]
			},{
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
						name:"Fecemisol",
						labelSeparator :'',
						allowBlank:true,
						width:130,
						binding:true,
						defaultvalue:'1900-01-01',
						hiddenvalue:'',
						id:"fecemisol",
						autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
					}]
				}]
			},{
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
						name:"Fecaprdoc",
						labelSeparator :'',
						allowBlank:true,
						width:130,
						binding:true,
						defaultvalue:'1900-01-01',
						hiddenvalue:'',
						id:"fecaprdoc",
						autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									

					}]
				}]
			},{
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
						allowBlank:true,
						labelSeparator :'',
						width:130,
						value:obtenerFechaActual(),
						id:"fecanusol",
						autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									

					}]
				}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:160px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 150,
					items: [{
							xtype:'checkbox',
							fieldLabel:'Documentos Contables',
							labelStyle: 'width:250px',
							checked:false,
							name:'estrepcon',
							id:'chbestrepcon'	
							}]
					}]
			}]
			
	});
/***********************************************************************************
/***********************************************************************************
* @Funci?n para buscar los Proveedores o Beneficiarios segun sea el caso
* @parametros: 
* @retorno:
* @fecha de creaci?n: 04/07/2012.
* @autor: Ing. Luis Anibal Lang.
************************************************************************************
* @fecha modificaci?n:
* @descripci?n:
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
								{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'cod_pro'},
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
				arrfiltro:[{etiqueta:'C&#243;digo',id:'copro',valor:'codpro'},
						   {etiqueta:'Descripci&#243;n',id:'nopro',valor:'nompro'}],
				rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
				parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'catalogo'}),
				tipbus:'L',
				setdatastyle:'F',
				formulario:fromAnulaCXP
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
				formulario:fromAnulaCXP
			});

			
			comcatproveedor.mostrarVentana();
		}
	}	


barraherramienta    = true;
//var fechaconta = new Date(Ext.getCmp('fechaconta').getValue());
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarCXP = new Ext.FormPanel({
		applyTo: 'formularioCXP',
		width: 920,
		height: 500,
		autoScroll:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:35px;',
		title: "<H1 align='center'>Anulacion de Solicitud de Orden de Pago</H1>",
		frame: true,
		autoScroll:true,
		items: [fromAnulaCXP,
		        gridSolicitud
		        ]
	});
	
	fromAnulaCXP.doLayout();
});


function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	var numsol      = Ext.getCmp('numsol').getValue();
	var destino     = Ext.getCmp('cmbdestino').getValue();
	var cod_pro     = Ext.getCmp('cod_pro').getValue();
	var nompro      = Ext.getCmp('nompro').getValue();
	var fecemisol   = Ext.getCmp('fecemisol').getValue();
	if (fecemisol!='')
	{
		fecemisol   =Ext.getCmp('fecemisol').getValue().format('Y/m/d');	
	}
	var fecaprdoc   = Ext.getCmp('fecaprdoc').getValue();
	if (fecaprdoc!='')
	{
		fecaprdoc   =Ext.getCmp('fecaprdoc').getValue().format('Y/m/d');	
	}
	var estrepcon   = Ext.getCmp('chbestrepcon').getValue();

	var JSONObject = {
			'operacion' : 'buscar_por_anular_sop',
			'numsol'    : numsol,
			'tipo'      : destino,
			'codigo'    : cod_pro,
			'fecreg'    : fecemisol,
			'fecapr'    : fecaprdoc,
			'estrepcon'    : estrepcon
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
				dsSolicitud.loadData(objetoCxp);
			}
		}	
	});
}

function irCancelar(){
	limpiarFormulario(fromAnulaCXP);
	gridSolicitud.store.removeAll();	
}

function irProcesar(){
	var cadenaJson = "{'operacion': 'anular_sop','fecanusol':'"+Ext.getCmp('fecanusol').getValue().format(Date.patterns.bdfecha)+"','solicitudes':[";				
	var arrSolicitud = gridSolicitud.getSelectionModel().getSelections();
	var	total = arrSolicitud.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if(arrSolicitud[i].get('conanusop')!=''){
				if (i==0) {
					cadenaJson = cadenaJson +"{'numsol':'"+ arrSolicitud[i].get('numsol')+ "','conanusop':'"+ arrSolicitud[i].get('conanusop')+ "'}";
				}
				else {
					cadenaJson = cadenaJson +",{'numsol':'"+ arrSolicitud[i].get('numsol')+ "','conanusop':'"+ arrSolicitud[i].get('conanusop')+ "'}";
				}
			}
			else {
				Ext.MessageBox.show({
					title:'Advertencia',
					msg:'Debe indicar el concepto de anulaci&#243;n de la solicitud de pago '+arrSolicitud[i].get('numsol'),
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
					tituloVentana: 'Resultado Anulaci&#243;n de Contabilizaci&#243;n de Solicitud de Orden de Pago',
					anchoLabel: 200,
					labelTotal:'Total solicitudes procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total solicitudes anuladas',
					valorProcesada:arrResultado[1],
					labelError:'Total solicitudes con error',
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
			title:'Advertencia',
			msg:'Debe seleccionar al menos un documento a procesar',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
		});
	}	
}