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
var gridCuentaCobrar = null;
var	fromSFCXC = null;

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevFactCobrCXC = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 935,
		height: 400,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Contabilización de Cuentas por Cobrar</H1>",
		frame: true,
		autoScroll:true,
		items: [fromSFCXC,
		        gridCuentaCobrar
		        ]
	});
	
	fromRevFactCobrCXC.doLayout();
});
		
	var reCuentaCobrar = Ext.data.Record.create([
		{name: 'comprobante'},                      
        {name: 'fecha'},
        {name: 'procede'},
        {name: 'descripcion'}                  	    
    ]);
  	
  	var dsCuentaCobrar =  new Ext.data.Store({
  		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuentaCobrar)
  	});
  						
  	var cmCuentaCobrar = new Ext.grid.ColumnModel([
  		new Ext.grid.CheckboxSelectionModel(),
  		  {header: "<CENTER>Comprobante</CENTER>", width:20, sortable: true, dataIndex: 'comprobante'},
          {header: "<CENTER>Fecha</CENTER>", width: 15, sortable: true, dataIndex: 'fecha'},
          {header: "<CENTER>Descripción</CENTER>", width: 65, sortable: true, dataIndex: 'descripcion'}
    ]);
                  	
	gridCuentaCobrar = new Ext.grid.EditorGridPanel({
    	width:875,
 		height:150,
		frame:true,
		title:"<H1 align='center'>Comprobantes de Cuentas por Cobrar</H1>",
		style: 'position:absolute;left:15px;top:180px',
		autoScroll:true,
   		border:true,
   		ds: dsCuentaCobrar,
     	cm: cmCuentaCobrar,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
     	stripeRows: true,
    	viewConfig: {forceFit:true}
	});
	
	gridCuentaCobrar.on({
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
					tituloVentana: "<H1 align='center'>Reversar Contabilización de Cuentas por Cobrar</H1>",
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
								valor:registro.get('fecha'),
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
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_spicxc',
																	  'comprobante':registro.get('comprobante'),
																	  'procede':registro.get('procede'),
																	  'fecha':registro.get('fecha')}),
					tieneContable: true,
					anchoGridCO :550,
					altoGridCO :100,
					paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_scgcxc',
																   'comprobante':registro.get('comprobante'),
																   'procede':registro.get('procede'),
																   'fecha':registro.get('fecha')})
				});
				//fin creando componente detalle comprobante
				comDetalleModificacion.mostrarVentana();
			}
		}
	});

	var Xpos = ((screen.width/2)-(425));
  	fromSFCXC = new  Ext.form.FieldSet({ 
		    title:'Datos de la Cuenta por Cobrar',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 875,
			cls: 'fondo',
			height: 150,
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
									autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{} ');"}
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
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: 'Procede',
								id: 'procede',
								width: 90,
								autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '6', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;: ');"}
							}]
	 					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:90px',
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
	limpiarFormulario(fromSFCXC);
	gridCuentaCobrar.store.removeAll();
}

function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar modificaciones a aprobar
	var fecha = '';
	var comprobante = Ext.getCmp('comprobante').getValue();
	var procede     = Ext.getCmp('procede').getValue();
	if(Ext.getCmp('fecha').getValue()!=''){
		fecha = Ext.getCmp('fecha').getValue().format(Date.patterns.bdfecha);
	}

	var JSONObject = {
		'operacion'  : 'buscar_reversar_cxc',
		'comprobante': comprobante,
		'procede'    : procede,
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
					gridCuentaCobrar.store.removeAll();
				}
				else{
					gridCuentaCobrar.store.loadData(objetoMovsfc);
				}
			}
		},
		failure: function (result,request){
			Ext.MessageBox.alert('Error', 'Error de conexion con el Servidor'); 
		}
	});
}

function irProcesar(){
	var cadenaJson = "{'operacion': 'rev_contabilizacion_cxc', 'cuentaCobrar':[";				
	var arrCuentaCobrar = gridCuentaCobrar.getSelectionModel().getSelections();
	var	total = arrCuentaCobrar.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'comprobante':'"+arrCuentaCobrar[i].get('comprobante')+ "'," +
										 "'fecha':'"+arrCuentaCobrar[i].get('fecha')+"'," +
										 "'procede':'"+arrCuentaCobrar[i].get('procede')+"'," +
										 "'descripcion':'"+arrCuentaCobrar[i].get('descripcion')+"'}";
			}
			else {
				cadenaJson = cadenaJson +",{'comprobante':'"+arrCuentaCobrar[i].get('comprobante')+ "'," +
										 "'fecha':'"+arrCuentaCobrar[i].get('fecha')+"'," +
										 "'procede':'"+arrCuentaCobrar[i].get('procede')+"'," +
										 "'descripcion':'"+arrCuentaCobrar[i].get('descripcion')+"'}";
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
					tituloVentana: 'Resultado Contabilizaci&#243;n de Solicitudes de Orden de Pago',
					anchoLabel: 200,
					labelTotal:'Total documentos procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total documentos contabilizados',
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