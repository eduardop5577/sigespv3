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

barraherramienta = true;
var gridSolicitud = null;
var fromRevAnulaContabilizaSobcon = null;

Ext.onReady(function(){

	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	

	var reSolicitud = Ext.data.Record.create([
	    {name: 'codcon'}, 
	    {name: 'codasi'}, 
	    {name: 'feccon'},
	    {name: 'obscon'},
	    {name: 'fechaconta'},
	    {name: 'fechaanulada'},
	    {name: 'fechacontaasig'},
	    {name: 'montotasi'},
	    {name: 'desobr'},
	    {name: 'cod_pro'},
	    {name: 'obsasi'},
	    {name: 'nompro'}
	]);

	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});

	var cmSolicitud = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Nro Contrato</CENTER>", width: 40, sortable: true, dataIndex: 'codcon'},
	    {header: "<CENTER>Nro Asignaci&#243;n</CENTER>", width: 40, sortable: true, dataIndex: 'codasi'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'feccon'},
	    {header: "<CENTER>Observaci&#243;n</CENTER>", width: 50, sortable: true, dataIndex: 'obscon'},
	]);

	//creando datastore y columnmodel para la grid de contratos
	gridSolicitud = new Ext.grid.GridPanel({
		width:570,
		height:250,
		frame:true,
		title:"<H1 align='center'>Contratos Anulados</H1>",
		style: 'position:absolute;left:15px;top:190px',
		autoScroll:true,
		border:true,
		ds: dsSolicitud,
		cm: cmSolicitud,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	
	// Creando la ventana emergente de los detalles
	gridSolicitud.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovBancario = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovBancario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBancario)
				});
									
				var cmMovBancario = new Ext.grid.ColumnModel([
			        {header: "<CENTER>Estructura</CENTER>", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "<CENTER>Estatus</CENTER>", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "<CENTER>Cuenta</CENTER>", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "<CENTER>Monto</CENTER>", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "<CENTER>Disponibilidad</CENTER>", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				
				//creando componente detalle comprobante
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: "<H1 align='center'>Informaci&#243;n del Comprobante</H1>",
					anchoVentana: 600,
					altoVentana: 500,
					anchoFormulario: 580,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Contrato',
								id:'ndoc',
								valor: registro.get('codcon'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fmov',
								valor:registro.get('feccon'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'cmov',
								valor:registro.get('obscon'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Proveedor',
								id:'tip_mov',
								valor:registro.get('cod_pro')+" - "+registro.get('nompro'),
								ancho: 300
								},
							    {
								tipo:'textfield',
								etiqueta:'Asignación Asociada',
								id:'b_mov',
								valor:registro.get('codasi')+" - "+registro.get('obsasi'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Contabilizaci&#243;n',
								id:'promov',
								valor:'COMPROMISO',
								ancho: 300
								}],
					tienePresupuesto:true,
					tituloGridPresupuestario:"<H1 align='center'>Detalle Presupuestario de Gasto</H1>",
					anchoGridPG :580,
					altoGridPG :150,
					dsPresupuestoGasto: dsMovBancario,
					cmPresupuestoGasto: cmMovBancario,
					rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto',
																	  'codasi':registro.get('codasi'),
																	  'codcon':''}), 
																   
				});
				//fin creando componente detalle comprobante
				comMovBancario.mostrarVentana();
			}
		}
	});

	fromRevAnulaContabilizaSobcon = new Ext.form.FieldSet({
		    title:'Datos del Contrato',
		    style: 'position:absolute;left:15px;top:5px',
			border:true,
			width: 570,
			cls: 'fondo',
			height: 165,
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
									labelSeparator :'',
									fieldLabel: 'Número de Contrato',
									id: 'codcon',
									width: 150,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
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
									labelSeparator :'',
									fieldLabel: 'Número de Asignaci&#243;n',
									id: 'codasi',
									width: 150,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
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
									xtype:"datefield",
									fieldLabel:"Fecha del Contrato",
									labelSeparator :'',
									name:"feccon",
									allowBlank:false,
									width:100,
									id:"feccon",
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
									xtype:"datefield",
									fieldLabel:"Fecha de Asignaci&#243;n",
									labelSeparator :'',
									name:"fecasi",
									allowBlank:false,
									width:100,
									id:"fecinicon",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	})
	//Creacion del formulario
	var Xpos = ((screen.width/2)-(300));
	plRevAnulaContabilizaSobcon = new Ext.FormPanel({
		applyTo: 'formulario',
		width:613,
		height: 500,
		title: "<H1 align='center'>Reverso de Anulaci&#243;n de Contabilizaci&#243;n de Contratos</H1>",
		frame:true,
		autoScroll:false,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',

		items: [fromRevAnulaContabilizaSobcon,gridSolicitud]
	});	
	plRevAnulaContabilizaSobcon.doLayout();
});
function irCancelar(){
	limpiarFormulario(fromRevAnulaContabilizaSobcon);
	gridSolicitud.store.removeAll();
}
function irAnular(){

}

function irProcesar(){
	grid = gridSolicitud.getSelectionModel().getSelections();
	cadenajson = "{'operacion':'rev_anular_sobcon','codsis':'"+sistema+"','nomven':'"+vista+"','arrDetalle':[";
	total = grid.length;
	if(total>0)
	{
		for(var i=0; i<total; i++)
		{
			for (i=0; i<total; i++)
			{
				if (i==0) 
				{
					cadenajson += "{'codasi':'"+grid[i].get('codasi')+"','codcon':'"+grid[i].get('codcon')+"'," +
							       "'feccon':'"+grid[i].get('feccon')+"','obscon':'"+grid[i].get('obscon')+"'," +
							       "'fechaconta':'"+grid[i].get('fechaconta')+"','fechaanulada':'"+grid[i].get('fechaanulada')+"'," +
					               "'fechacontaasig':'"+grid[i].get('fechacontaasig')+"','montotasi':'"+grid[i].get('montotasi')+"'," +
					               "'desobr':'"+grid[i].get('desobr')+"','cod_pro':'"+grid[i].get('cod_pro')+"'}";                
				}
				else {
					cadenajson += ",{'codasi':'"+grid[i].get('codasi')+"','codcon':'"+grid[i].get('codcon')+"'," +
							       "'feccon':'"+grid[i].get('feccon')+"','obscon':'"+grid[i].get('obscon')+"'," +
							       "'fechaconta':'"+grid[i].get('fechaconta')+"','fechaanulada':'"+grid[i].get('fechaanulada')+"'," +
					               "'fechacontaasig':'"+grid[i].get('fechacontaasig')+"','montotasi':'"+grid[i].get('montotasi')+"'," +
					               "'desobr':'"+grid[i].get('desobr')+"','cod_pro':'"+grid[i].get('cod_pro')+"'}";                
				}
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Reversar la Anulaci&#243;n de los Contratos',
					anchoLabel: 200,
					labelTotal:'Total contratos procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total contratos reversados',
					valorProcesada:arrResultado[1],
					labelError:'Total contratos con error',
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
				Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
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

function irDescargar(){

}

function irImprimir(){

}
function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar contratos
	var codcon = Ext.getCmp('codcon').getValue();
	var codasi = Ext.getCmp('codasi').getValue();
	var feccon = Ext.getCmp('feccon').getValue();
	if(feccon!='')
	{
		feccon = Ext.getCmp('feccon').getValue().format('Y-m-d');
	}
	var fecinicon = Ext.getCmp('fecinicon').getValue();
	if(fecinicon!='')
	{
		fecinicon = Ext.getCmp('fecinicon').getValue().format('Y-m-d');
	}
	var JSONObject = {
			'operacion' : 'buscar_sobcon',
			'codcon'    : codcon,
			'codasi'    : codasi,
			'feccon'    : feccon,
			'fecinicon' : fecinicon,
			'estatus'   : '2',
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoSobasi = eval('(' + datos + ')');
			if(objetoSobasi!=''){
				if(objetoSobasi!='0'){
					if(objetoSobasi.raiz == null || objetoSobasi.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						gridSolicitud.store.loadData(objetoSobasi);
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
			Ext.MessageBox.alert('Error', 'Error de comunicación con el Servidor'); 
		}	
	});
}

