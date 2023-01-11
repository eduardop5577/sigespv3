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
var gridPagos = null;
var	fromSFCPAG = null;

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevIntegraPag = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 935,
		height: 400,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'> Reverso de Integración de Pagos</H1>",
		frame: true,
		autoScroll:true,
		items: [fromSFCPAG,
		        gridPagos
		        ]
	});
	
	fromRevIntegraPag.doLayout();
});		
	var rePago = Ext.data.Record.create([
		{name: 'numdoc'},
		{name: 'comprobante'},                      
        {name: 'fecdep'},
        {name: 'procede'},
        {name: 'codban'},
        {name: 'ctaban'},
        {name: 'operacion'},
        {name: 'descripcion'},
        {name: 'bancue'}
    ]);
  	
  	var dsPago =  new Ext.data.Store({
  		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rePago)
  	});
  						
  	var cmPago = new Ext.grid.ColumnModel([
  		new Ext.grid.CheckboxSelectionModel(),
  		{header: "<CENTER>Documento</CENTER>", width:20, sortable: true, dataIndex: 'numdoc'},
  		{header: "<CENTER>Comprobante</CENTER>", width:20, sortable: true, dataIndex: 'comprobante'},
        {header: "<CENTER>Fecha</CENTER>", width: 15, sortable: true, dataIndex: 'fecdep'},
        {header: "<CENTER>Banco/Cuenta</CENTER>", width: 45, sortable: true, dataIndex: 'bancue'},
        {header: "<CENTER>Concepto</CENTER>", width: 50, sortable: true, dataIndex: 'descripcion'}
    ]);
                  	
	gridPagos = new Ext.grid.EditorGridPanel({
    	width:870,
 		height:180,
		frame:true,
		title:"<H1 align='center'>Movimientos de Pagos por Integrar</H1>",
		style: 'position:absolute;left:15px;top:150px',
		autoScroll:true,
   		border:true,
   		ds: dsPago,
     	cm: cmPago,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
     	stripeRows: true,
    	viewConfig: {forceFit:true}
	});
	
	gridPagos.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
		
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovDetPresupuestario = Ext.data.Record.create([
				    {name: 'spi_cuenta'},
				    {name: 'monto'}
				]);
		
				var dsMovDetPresupuestario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovDetPresupuestario)
				});
		
				var cmMovDetPresupuestario = new Ext.grid.ColumnModel([
				    {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spi_cuenta'},
				    {header: "Monto", width: 50, sortable: true, dataIndex: 'monto'} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
		
				//creando componente detalle comprobante
				var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: "<H1 align='center'>Reverso de Integración de Pagos</H1>",
					anchoVentana: 600,
					altoVentana: 500,
					anchoFormulario: 580,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Comprobante',
								id:'cmpmod',
								valor: registro.get('comprobante'),
								ancho: 200 
								},
								{
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fecmod',
								valor:registro.get('fecdep'),
								ancho: 100
								},
								{	
								tipo:'textarea',
								etiqueta:'Descripci&#243;n',
								id:'cmpdes',
								valor:registro.get('descripcion'),
								ancho: 350
								}],
					tienePresupuesto:true,
					tituloGridPresupuestario:'Detalle Presupuestario de Ingreso',
					anchoGridPG :580,
					altoGridPG :150,
					dsPresupuestoGasto: dsMovDetPresupuestario,
					cmPresupuestoGasto: cmMovDetPresupuestario,
					rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsfc.php',
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_spipag',
																	  'comprobante':registro.get('numdoc'),
																	  'procede':registro.get('procede'),
																	  'fecha':registro.get('fecdep'),
																	  'codban':registro.get('codban'),
																	  'ctaban':registro.get('ctaban')}),
					tieneContable: true,
					anchoGridCO :550,
					altoGridCO :100,
					paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_scgpag',
																   'comprobante':registro.get('numdoc'),
																   'procede':registro.get('procede'),
																   'fecha':registro.get('fecdep'),
																   'codban':registro.get('codban'),
																   'ctaban':registro.get('ctaban')})
				});
				//fin creando componente detalle comprobante
				comDetalleModificacion.mostrarVentana();
			}
		}
	});

	var Xpos = ((screen.width/2)-(425));
  	fromSFCPAG = new Ext.form.FieldSet({  
		    title:'Datos de los Pagos',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 120,
			items:[{
					layout: "column",
		 			defaults: {border: false},
		 			style: 'position:absolute;left:15px;top:30px',
		 			items: [{
			 				layout: "form",
							border: false,
							labelWidth: 100,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Comprobante',
									id: 'comprobante',
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false
								}]
		 					}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:60px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 100,
							items: [{
							        xtype:"datefield",
							        fieldLabel:"Fecha",
							        labelSeparator :'',
							        allowBlank:false,
									width:100,
									id:"fecha",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
				        		}]
							}]
					}]
});

function irCancelar(){
	limpiarFormulario(fromSFCPAG);
	gridPagos.store.removeAll();
}

function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar modificaciones a aprobar
	var fecha = '';
	var comprobante = Ext.getCmp('comprobante').getValue();
	if(Ext.getCmp('fecha').getValue()!=''){
		fecha = Ext.getCmp('fecha').getValue().format(Date.patterns.bdfecha);
	}
	var JSONObject = {
		'operacion'  : 'buscar_por_reversar_pag',
		'comprobante': comprobante,
		'fecha'      : fecha
	}

	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsfc.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoMovsfc = eval('(' + datos + ')');
			if(objetoMovsfc!=''){
				if(objetoMovsfc.raiz == null || objetoMovsfc.raiz ==''){
					Ext.MessageBox.show({
						title:'Advertencia',
						msg:'No existen datos para mostrar',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.WARNING
	 				});
				}
				else{
					gridPagos.store.loadData(objetoMovsfc);
				}
			}
		},
		failure: function (result,request){
			Ext.MessageBox.alert('Error', 'Error de comunicación con el Servidor'); 
		}
	});
}

function irProcesar()
{
	var cadenaJson = "{'operacion': 'rev_contabilizar_pag', 'pagos':[";				
	var arrPagos = gridPagos.getSelectionModel().getSelections();
	var	total = arrPagos.length;
	if (total>0)
	{
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++)
		{
			if (i==0)
			{
				cadenaJson = cadenaJson +"{'comprobante':'"+arrPagos[i].get('comprobante')+ "'," +
										 "'fecha':'"+arrPagos[i].get('fecdep')+"'," +
										 "'procede':'"+arrPagos[i].get('procede')+"'," +
										 "'numdoc':'"+arrPagos[i].get('numdoc')+"'," +
										 "'codban':'"+arrPagos[i].get('codban')+"'," +
										 "'ctaban':'"+arrPagos[i].get('ctaban')+"'," +
										 "'operacion':'"+arrPagos[i].get('operacion')+"'}";
			}
			else
			{
				cadenaJson = cadenaJson +",{'comprobante':'"+arrPagos[i].get('comprobante')+ "'," +
										 "'fecha':'"+arrPagos[i].get('fecdep')+"'," +
										 "'procede':'"+arrPagos[i].get('procede')+"'," +
										 "'numdoc':'"+arrPagos[i].get('numdoc')+"'," +
										 "'codban':'"+arrPagos[i].get('codban')+"'," +
										 "'ctaban':'"+arrPagos[i].get('ctaban')+"'," +
										 "'operacion':'"+arrPagos[i].get('operacion')+"'}";
			}
		}
		
		cadenaJson = cadenaJson + ']}';
		var objdata= eval('(' + cadenaJson + ')');	
		objdata=JSON.stringify(objdata);
		var parametros = 'ObjSon='+objdata; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsfc.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) {
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Reverso de Integraci&#243;n de Pagos',
					anchoLabel: 200,
					labelTotal:'Total documentos procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total documentos reversados',
					valorProcesada:arrResultado[1],
					labelError:'Total documentos con error',
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