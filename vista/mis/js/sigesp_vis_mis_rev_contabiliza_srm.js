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

var gridMovRevCobranza  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromRevContabilzarSRM = null //varibale para almacenar la instacia de objeto de formulario 


barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	p1fromRevContabilzarSRM = new Ext.FormPanel({
		applyTo: 'formularioSRM',
		width: 935,
		height: 400,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Contabilización de resumen de Cobranzas</H1>",
		frame: true,
		autoScroll:true,
		items: [fromRevContabilzarSRM,
		        gridMovRevCobranza
		        ]
	});
	
	p1fromRevContabilzarSRM.doLayout();
});
	/*	CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */	

	//creando datastore y columnmodel para la grid de modificaciones presupuestarias
	var reMovCobranza = Ext.data.Record.create([
	    {name: 'comprobante'}, 
	    {name: 'fecdep'},
	    {name: 'nomban'},
	    {name: 'descripcion'},
	    {name: 'procede'},
	    {name: 'codban'},
	    {name: 'ctaban'}
	]);

	var dsMovCobranza =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovCobranza)
	});

	var cmMovCobranza = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Nº Documento</CENTER>", width: 50, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha Movimiento</CENTER>", width: 30, sortable: true, dataIndex: 'fecdep'},
	    {header: "<CENTER>Banco/Cuenta</CENTER>", width: 60, sortable: true, dataIndex: 'nomban'},
	    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'}
	]);
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias

	//creando grid para las modificaciones presupuestarias
	gridMovRevCobranza = new Ext.grid.GridPanel({
		width:870,
		height:250,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:130px',
		autoScroll:true,
		border:true,
		ds: dsMovCobranza,
		cm: cmMovCobranza,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	//fin creando grid para las modificaciones presupuestarias

	gridMovRevCobranza.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
		
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovDetPresupuestario = Ext.data.Record.create([
				    {name: 'spi_cuenta'},
				    {name: 'monto'},
				]);
		
				var dsMovDetPresupuestario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovDetPresupuestario)
				});
		
				var cmMovDetPresupuestario = new Ext.grid.ColumnModel([
				    {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spi_cuenta'},
				    {header: "Monto", width: 50, sortable: true, dataIndex: 'monto'/*,renderer:formatoMontoGrid*/}, 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
		
				//creando componente detalle comprobante
				var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: "<H1 align='center'>Modificaci&#243;n Presupuestaria</H1>",
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
					rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsrm.php',
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'detalle_ingreso',
																	  'numcom':registro.get('comprobante'),
																	  'procede':registro.get('procede'),
																	  'fecha':registro.get('fecdep'),
																	  'codban':registro.get('codban'),
																	  'ctaban':registro.get('ctaban')}),
					tieneContable: true,
					anchoGridCO :550,
					altoGridCO :100,
					paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'detalle_contable',
																   'numcom':registro.get('comprobante'),
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

	//creando formulario principal con parametros de busqueda y grid de modificaciones
	var Xpos = ((screen.width/2)-(300));
	fromRevContabilzarSRM = new Ext.form.FieldSet({ 
		    title:'',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 90,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Nº Documento',
									labelSeparator :'',
									id: 'numcom',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 130,
									allowBlank:false
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:45px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Documento",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									id:"fecmov",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:100px',
					items: [{
							layout: "form",
							border: false,
							items: [gridMovRevCobranza]
							}]
					}]
	});
	//fin creando formulario principal con parametros de busqueda y grid de modificaciones

	/*	FIN CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */




function irBuscar( ){
	obtenerMensaje('procesar','','Buscando Datos');
	var numcom   = Ext.getCmp('numcom').getValue();
	var fecmov	 = Ext.getCmp('fecmov').getValue();

	var JSONObject = {
			'operacion' : 'buscar_por_reversar',
			'numcom'    : numcom,
			'fecmov'    : fecmov
	}

	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsrm.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoMovbco = eval('(' + datos + ')');
			if(objetoMovbco!=''){
				if(objetoMovbco!='0'){
					if(objetoMovbco.raiz == null || objetoMovbco.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridMovRevCobranza.store.removeAll();
					}
					else{
						gridMovRevCobranza.store.loadData(objetoMovbco);
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
//fin creando funcion que se ejecuta al clikcear el boton buscar

function irCancelar(){
	limpiarFormulario(fromRevContabilzarSRM);
	gridMovRevCobranza.store.removeAll();
}

function irProcesar(){
	valido=true;
	var cadenaJson = "{'operacion': 'rev_contabilizar', 'codsis':'"+sistema+"', 'nomven':'"+vista+"', 'arrDetalle':[";				
	var arrModificaciones = gridMovRevCobranza.getSelectionModel().getSelections();
	var total = arrModificaciones.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'comprobante':'"+ arrModificaciones[i].get('comprobante')+ "'," +
										  "'fecha':'"+ arrModificaciones[i].get('fecdep')+ "'," +
										  "'procede':'"+ arrModificaciones[i].get('procede')+ "'," +
										  "'descripcion':'"+ arrModificaciones[i].get('descripcion')+ "'," +
										  "'codban':'"+ arrModificaciones[i].get('codban')+"'," +
										  "'ctaban':'"+ arrModificaciones[i].get('ctaban')+"'}";
			}
			else {
				cadenaJson = cadenaJson +",{'comprobante':'"+ arrModificaciones[i].get('comprobante')+ "'," +
										  "'fecha':'"+ arrModificaciones[i].get('fecdep')+ "'," +
										  "'procede':'"+ arrModificaciones[i].get('procede')+ "'," +
										  "'descripcion':'"+ arrModificaciones[i].get('descripcion')+ "'," +
										  "'codban':'"+ arrModificaciones[i].get('codban')+"'," +
										  "'ctaban':'"+ arrModificaciones[i].get('ctaban')+"'}";
			}
		}
		cadenaJson = cadenaJson + ']}';
		var objdata= eval('(' + cadenaJson + ')');	
		objdata=JSON.stringify(objdata);
		var parametros = 'ObjSon='+objdata; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsrm.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Cobranzas Reversadas',
					anchoLabel: 200,
					labelTotal:'Total de cobros procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total de cobros reversadas',
					valorProcesada:arrResultado[1],
					labelError:'Total de cobros con error',
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
			msg:'Debe seleccionar al menos un documento a procesar',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}