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

var gridMovRevDespacho  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromRevDespacho = null //varibale para almacenar la instacia de objeto de formulario 


barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarDes = new Ext.FormPanel({
		applyTo: 'formularioSIV',
		width: 920,
		height: 430,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Contabilización de Despacho</H1>",
		frame: true,
		autoScroll:true,
		items: [fromRevDespacho,
		        gridMovRevDespacho
		        ]
	});
	
	fromContabilzarDes.doLayout();
});
	
	//validando si la configuracion permite contabilizar despachos
	var JSONObject = {
			'operacion' : 'validarDespacho'
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
					msg:'Debe configurar en el modulo la opci&#243;n contabilizar despachos para realizar este proceso',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO,
					fn:function(){
						location.href = 'sigesp_vis_mis_inicio.html';
					}
				});
	
			}
		}	
	});
	//fin validando si la configuracion permite contabilizar despachos

	/*	CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */	

	//creando datastore y columnmodel para la grid de modificaciones presupuestarias
	var reMovDespacho = Ext.data.Record.create([
	    {name: 'comprobante'}, 
	    {name: 'fecha'},
	    {name: 'descripcion'},
	    {name: 'fechaconta'}
	]);
	
	var dsMovDespacho =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovDespacho)
	});

	var cmMovDespacho = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width: 30, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'}
	]);
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias

	//creando grid para las modificaciones presupuestarias
	gridMovRevDespacho = new Ext.grid.GridPanel({
		width:870,
		height:250,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:130px',
		autoScroll:true,
		border:true,
		ds: dsMovDespacho,
		cm: cmMovDespacho,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	//fin creando grid para las modificaciones presupuestarias

	gridMovRevDespacho.on({
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
		    {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
		    {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
		    {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
		    {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
		    {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
		]);
		//fin creando datastore y columnmodel para la grid de detalles presupuestarios

		//creando componente detalle comprobante
		var comDetalleDespacho = new com.sigesp.vista.comDetalleComprobante({
			tituloVentana: 'Detalle Comprobante',
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
			tituloGridPresupuestario:'Detalle Presupuestario de Gasto',
			anchoGridPG :580,
			altoGridPG :150,
			dsPresupuestoGasto: dsMovPresupuestario,
			cmPresupuestoGasto: cmMovPresupuestario,
			rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsiv.php',
			paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'despacho_detalle_presupuesto',
				'numorddes':registro.get('comprobante')}),
				tieneContable: true,
				anchoGridCO :550,
				altoGridCO :100,
				paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'despacho_detalle_contable',
					'numorddes':registro.get('comprobante')})

		});
		//fin creando componente detalle comprobante

		comDetalleDespacho.mostrarVentana();
	}
	}
	});

	//creando formulario principal con parametros de busqueda y grid de modificaciones
	var Xpos = ((screen.width/2)-(300));
	fromRevDespacho = new Ext.form.FieldSet({ 
		    title:'',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 85,
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
									fieldLabel: 'Despacho',
									labelSeparator :'',
									id: 'numorddes',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"},
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
									fieldLabel:"Fecha Despacho",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									id:"fecdes",
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
							labelWidth: 0,
							items: [gridMovRevDespacho]
						}]
					}]
	});
	//fin creando formulario principal con parametros de busqueda y grid de modificaciones

	/*	FIN CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */



function irBuscar( ){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar modificaciones a aprobar
	var numorddes = Ext.getCmp('numorddes').getValue();
	var fecdes	  = Ext.getCmp('fecdes').getValue();

	var JSONObject = {
			'operacion' : 'despachos_por_reversar',
			'numorddes' : numorddes,
			'fecdes'    : fecdes
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
			var objetoMovDespacho = eval('(' + datos + ')');
			if(objetoMovDespacho!=''){
				if(objetoMovDespacho!='0'){
					if(objetoMovDespacho.raiz == null || objetoMovDespacho.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridMovRevDespacho.store.removeAll();
					}
					else{
						gridMovRevDespacho.store.loadData(objetoMovDespacho);
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
	limpiarFormulario(fromRevDespacho);
	gridMovRevDespacho.store.removeAll();
}

function irProcesar(){
	valido=true;
	var cadenaJson = "{'operacion': 'reversar_despacho', 'codsis':'"+sistema+"','nomven':'"+vista+"','arrDetalle':[";				
	var arrDespachos = gridMovRevDespacho.getSelectionModel().getSelections();
	var total = arrDespachos.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'comprobante':'"+ arrDespachos[i].get('comprobante')+ "'}";
			}
			else {
				cadenaJson = cadenaJson +",{'comprobante':'"+ arrDespachos[i].get('comprobante')+ "'}";
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
					tituloVentana: 'Resultado Despachos Reversados',
					anchoLabel: 200,
					labelTotal:'Total despachos procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total despachos reversados',
					valorProcesada:arrResultado[1],
					labelError:'Total despachos con error',
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