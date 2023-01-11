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

var gridMovTransferencia  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromContTransferencia = null //varibale para almacenar la instacia de objeto de formulario 

barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarTran = new Ext.FormPanel({
		applyTo: 'formularioMIS',
		width: 920,
		height: 480,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Contabilización de Transferencias</H1>",
		frame: true,
		autoScroll:true,
		items: [fromContTransferencia,
		    	gridMovTransferencia
		        ]
	});
	
	fromContabilzarTran.doLayout();
});

	//validando si la configuracion permite contabilizar transferencias
	var JSONObject = {
			'operacion' : 'validarTransferencia'
	}

	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsiv.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
		var respuesta = resultado.responseText;
		if(respuesta == '0'){
			Ext.MessageBox.show({
				title:'Mensaje',
				msg:'Debe configurar la opci&#243;n centro de costos, Producción o Mercado para realizar este proceso',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO,
				fn:function(){
				location.href = 'sigesp_vis_mis_inicio.html';
			}
			});

		}
	}	
	});
	//fin validando si la configuracion permite contabilizar transferencias

	/*	CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */	

	//creando datastore y columnmodel para la grid de modificaciones presupuestarias
	var reMovTransferencia = Ext.data.Record.create([
	    {name: 'comprobante'}, 
	    {name: 'fecha'},
	    {name: 'descripcion'}
	]);

	var dsMovTransferencia =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovTransferencia)
	});

	var cmMovTransferencia = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width: 30, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'}
	]);
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias

	//creando grid para las modificaciones presupuestarias
	gridMovTransferencia = new Ext.grid.GridPanel({
		width:870,
		height:250,
		frame:true,
		style: 'position:absolute;left:15px;top:155px',
		title:'',
		autoScroll:true,
		border:true,
		ds: dsMovTransferencia,
		cm: cmMovTransferencia,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	//fin creando grid para las modificaciones presupuestarias

	gridMovTransferencia.on({
		'rowcontextmenu': {
		fn: function(grid, numFila, evento){
		var registro = grid.getStore().getAt(numFila);

		//creando componente detalle comprobante
		var comDetalleTransferencia = new com.sigesp.vista.comDetalleComprobante({
			tituloVentana: 'Modificaci&#243;n Presupuestaria',
			anchoVentana: 720,
			altoVentana: 500,
			anchoFormulario: 680,
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
			rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsiv.php',
			tienePresupuesto:false,
			tieneContable: true,
			anchoGridCO :680,
			altoGridCO :100,
			paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'transferencia_detalle_contable',
				'numtra':registro.get('comprobante'),
				'fecemi':registro.get('fecha')})

		});
		//fin creando componente detalle comprobante

		comDetalleTransferencia.mostrarVentana();
	}
	}
	});

	//creando formulario principal con parametros de busqueda y grid de modificaciones
	var Xpos = ((screen.width/2)-(300));
	fromContTransferencia = new Ext.form.FieldSet({ 
		    title:'',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 120,
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
									fieldLabel: 'Transferencia',
									labelSeparator :'',
									id: 'numtra',
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
									fieldLabel:"Fecha Transferencia",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									id:"fecemi",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:75px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Contabilizaci&#243;n",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									id:"feccon",
									format: 'd/m/Y',
									value : obtenerFechaActual(),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:125px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 0,
							items: [gridMovTransferencia]
						}]
					}]
	});
	//fin creando formulario principal con parametros de busqueda y grid de modificaciones

	/*	FIN CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */




function irBuscar( ){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar transferencias a contabilizar
	var numtra  = Ext.getCmp('numtra').getValue();
	var fecemi  = Ext.getCmp('fecemi').getValue();

	var JSONObject = {
			'operacion' : 'transferencias_por_contabilizar',
			'numtra'    : numtra,
			'fecemi'    : fecemi
	}

	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsiv.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoMovTransferencia = eval('(' + datos + ')');
			if(objetoMovTransferencia!=''){
				if(objetoMovTransferencia!='0'){
					if(objetoMovTransferencia.raiz == null || objetoMovTransferencia.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridMovTransferencia.store.removeAll();
					}
					else{
						gridMovTransferencia.store.loadData(objetoMovTransferencia);
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
			Ext.MessageBox.alert('Error', 'Error de comunicacion con el Servidor'); 
		}
	});
}
//fin creando funcion que se ejecuta al clikcear el boton buscar

function irCancelar(){
	limpiarFormulario(fromContTransferencia);
	gridMovTransferencia.store.removeAll();
}

function irProcesar(){
	valido=true;
	var feccon = Ext.util.Format.date(Ext.getCmp('feccon').getValue(),'d/m/Y');			
	var cadenaJson = "{'operacion': 'contabilizar_transferencia', 'codsis':'"+sistema+"', 'nomven':'"+vista+"', 'feccon': '"+feccon+"', 'arrDetalle':[";				
	var arrTransferencias = gridMovTransferencia.getSelectionModel().getSelections();
	var total = arrTransferencias.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'comprobante':'"+ arrTransferencias[i].get('comprobante')+ "','fecemi':'"+ arrTransferencias[i].get('fecha')+ "'}";
			}
			else {
				cadenaJson = cadenaJson +",{'comprobante':'"+ arrTransferencias[i].get('comprobante')+ "','fecemi':'"+ arrTransferencias[i].get('fecha')+ "'}";
			}
		}

		cadenaJson = cadenaJson + ']}';
		var objdata= eval('(' + cadenaJson + ')');	
		objdata=JSON.stringify(objdata);
		var parametros = 'ObjSon='+objdata; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsiv.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Contabilizaci&#243;n de Transferencias',
					anchoLabel: 200,
					labelTotal:'Total transferencias procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total transferencias contabilizados',
					valorProcesada:arrResultado[1],
					labelError:'Total transferencias con error',
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