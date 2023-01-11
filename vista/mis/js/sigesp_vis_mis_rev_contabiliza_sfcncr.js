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
var gridNotasCD = null;
var	fromSFCNCD = null;

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevFactCobrNCD = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 935,
		height: 400,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'> Reverso de Contabilización de Notas de Crédito/Débito</H1>",
		frame: true,
		autoScroll:true,
		items: [fromSFCNCD,
		        gridNotasCD
		        ]
	});
	
	fromRevFactCobrNCD.doLayout();
});
	
	
	var reNota = Ext.data.Record.create([
		{name: 'comprobante'},                      
        {name: 'fecha'},
        {name: 'procede'},
        {name: 'descripcion'}                  	    
    ]);
  	
  	var dsNota =  new Ext.data.Store({
  		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reNota)
  	});
  						
  	var cmNota = new Ext.grid.ColumnModel([
  		new Ext.grid.CheckboxSelectionModel(),
  		  {header: "<CENTER>Comprobante</CENTER>", width:20, sortable: true, dataIndex: 'comprobante'},
          {header: "<CENTER>Fecha</CENTER>", width: 15, sortable: true, dataIndex: 'fecha'},
          {header: "<CENTER>Descripción</CENTER>", width: 65, sortable: true, dataIndex: 'descripcion'}
    ]);
                  	
	gridNotasCD = new Ext.grid.EditorGridPanel({
    	width:870,
 		height:150,
		frame:true,
		title:"<H1 align='center'>Comprobantes de Notas de Crédito/Débito</H1>",
		style: 'position:absolute;left:15px;top:180px',
		autoScroll:true,
   		border:true,
   		ds: dsNota,
     	cm: cmNota,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
     	stripeRows: true,
    	viewConfig: {forceFit:true}
	});
	
	gridNotasCD.on({
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
					tituloVentana: "<H1 align='center'>Reverso Contabilización de  Notas de Crédito/Débito</H1>",
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
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_spincd',
																	  'comprobante':registro.get('comprobante'),
																	  'procede':registro.get('procede'),
																	  'fecha':registro.get('fecha')}),
					tieneContable: true,
					anchoGridCO :550,
					altoGridCO :100,
					paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'comprobante_detalle_scgncd',
																   'comprobante':registro.get('comprobante'),
																   'procede':registro.get('procede'),
																   'fecha':registro.get('fecha')})
				});
				//fin creando componente detalle comprobante
				comDetalleModificacion.mostrarVentana();
			}
		}
	});
	
	//creando store para el combo destino
	var documento = [
	    ['Nota de Crédito','C'],
		['Nota de Débito','D']
	]; 
	
	var stDocumento = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : documento
	});
	//fin creando store para el combo destino 
	
	//creando objeto combo destino
	var cmbDocumento = new Ext.form.ComboBox({
		id:'documento',
		store : stDocumento,
		fieldLabel : 'Tipo de Documento',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		width:130,
		typeAhead: true,
		emptyText:'Seleccione',
		triggerAction:'all',
		forceselection:true,
		mode:'local'
	});

	var Xpos = ((screen.width/2)-(425));
  	fromSFCNCD = new Ext.form.FieldSet({  
		    title:'Datos de la Nota Crédito/Débito',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 150,
			items:[{
					layout: "column",
		 			defaults: {border: false},
		 			style: 'position:absolute;left:15px;top:30px',
		 			items: [{
			 				layout: "form",
							border: false,
							labelWidth: 150,
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
							labelWidth: 150,
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
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:90px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 150,
							items: [cmbDocumento]
						}]
					}]
});

function irCancelar(){
	limpiarFormulario(fromSFCNCD);
	gridNotasCD.store.removeAll();
}

function irBuscar(){
	var documento = Ext.getCmp('documento').getValue(); 
	if(documento!=''){
		//buscar modificaciones a aprobar
		var fecha = '';
		var comprobante = Ext.getCmp('comprobante').getValue();
		if(Ext.getCmp('fecha').getValue()!=''){
			fecha = Ext.getCmp('fecha').getValue().format(Date.patterns.bdfecha);
		}
		
		var JSONObject = {
			'operacion'  : 'buscar_reversar_ncd',
			'comprobante': comprobante,
			'fecha'      : fecha,
			'documento'  : documento
		}
		obtenerMensaje('procesar','','Buscando Datos');
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
						gridNotasCD.store.loadData(objetoMovsfc);
					}
				}
			},
			failure: function (result,request){
				Ext.MessageBox.alert('Error', 'Error de comunicación con el Servidor'); 
			}	
		});
	}
	else {
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar el tipo de documento',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}

function irProcesar(){
	var cadenaJson = "{'operacion': 'rev_contabilizacion_ded', 'cuentaCobrar':[";				
	var arrCuentaCobrar = gridNotasCD.getSelectionModel().getSelections();
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
					tituloVentana: 'Resultado Reverso Contabilizaci&#243;n de Notas de Crédito/Débito',
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