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

var gridMovRevProduccion  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromRevProduccion = null //varibale para almacenar la instacia de objeto de formulario 


barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevContabilzarTran = new Ext.FormPanel({
		applyTo: 'formularioSIV',
		width: 920,
		height: 460,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Contabilización de Produccion</H1>",
		frame: true,
		autoScroll:true,
		items: [fromRevProduccion,
		        gridMovRevProduccion
		        ]
	});
	
	fromRevContabilzarTran.doLayout();
});

	/*	CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */	

	//creando datastore y columnmodel para la grid de modificaciones presupuestarias
	var reMovProduccion = Ext.data.Record.create([
	    {name: 'comprobante'}, 
	    {name: 'fecha'},
	    {name: 'descripcion'},
	    {name: 'fechaconta'}
	]);

	var dsMovProduccion =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovProduccion)
	});

	var cmMovProduccion = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width: 30, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'}
	]);
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias

	//creando grid para las modificaciones presupuestarias
	gridMovRevProduccion = new Ext.grid.GridPanel({
		width:870,
		height:250,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:135px',
		autoScroll:true,
		border:true,
		ds: dsMovProduccion,
		cm: cmMovProduccion,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	//fin creando grid para las modificaciones presupuestarias

	gridMovRevProduccion.on({
		'rowcontextmenu': {
		fn: function(grid, numFila, evento){
		var registro = grid.getStore().getAt(numFila);

		//creando componente detalle comprobante
		var comDetalleProduccion = new com.sigesp.vista.comDetalleComprobante({
			tituloVentana: 'Modificaci&#243;n Presupuestaria',
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
			rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsiv.php',
			tienePresupuesto:false,
			tieneContable: true,
			anchoGridCO :550,
			altoGridCO :100,
			paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'produccion_detalle_contable',
				'numpro':registro.get('comprobante'),
				'fecemi':registro.get('fecha')})

		});
		//fin creando componente detalle comprobante

		comDetalleProduccion.mostrarVentana();
	}
	}
	});

	//creando formulario principal con parametros de busqueda y grid de modificaciones
	var Xpos = ((screen.width/2)-(300));
	fromRevProduccion = new Ext.form.FieldSet({ 
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
									fieldLabel: 'Produccion',
									labelSeparator :'',
									id: 'numpro',
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
									labelSeparator :'',
									fieldLabel:"Fecha Produccion",
									allowBlank:true,
									width:100,
									id:"fecemi",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
						}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:110px',
					items: [{
							layout: "form",
							labelSeparator :'',
							border: false,
							labelWidth: 0,
							items: [gridMovRevProduccion]
						}]
					}]
	});
	//fin creando formulario principal con parametros de busqueda y grid de modificaciones

	/*	FIN CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */




function irBuscar( )
{
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar Produccion a reversar
	var numpro   = Ext.getCmp('numpro').getValue();
	var fecemi	 = Ext.getCmp('fecemi').getValue();

	var JSONObject = {
			'operacion' : 'produccion_por_reversar',
			'numpro'    : numpro,
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
			var objetoMovProduccion = eval('(' + datos + ')');
			if(objetoMovProduccion!=''){
				if(objetoMovProduccion!='0'){
					if(objetoMovProduccion.raiz == null || objetoMovProduccion.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridMovRevProduccion.store.removeAll();
					}
					else{
						gridMovRevProduccion.store.loadData(objetoMovProduccion);
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
	limpiarFormulario(fromRevProduccion);
	gridMovRevProduccion.store.removeAll();
}

function irProcesar(){
	valido=true;			
	var cadenaJson = "{'operacion': 'reversar_produccion', 'codsis':'"+sistema+"', 'nomven':'"+vista+"', 'arrDetalle':[";				
	var arrProduccion = gridMovRevProduccion.getSelectionModel().getSelections();
	var total = arrProduccion.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'comprobante':'"+ arrProduccion[i].get('comprobante')+ "','fecemi':'"+ arrProduccion[i].get('fecha')+ "'}";
			}
			else {
				cadenaJson = cadenaJson +",{'comprobante':'"+ arrProduccion[i].get('comprobante')+ "','fecemi':'"+ arrProduccion[i].get('fecha')+ "'}";
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
					tituloVentana: 'Resultado Produccion Reversadas',
					anchoLabel: 200,
					labelTotal:'Total Produccion procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total Produccion reversadas',
					valorProcesada:arrResultado[1],
					labelError:'Total Produccion con error',
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