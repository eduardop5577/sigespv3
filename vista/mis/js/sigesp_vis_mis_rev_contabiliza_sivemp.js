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

var gridMovRevEmpaquetado  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromRevEmpaquetado = null //varibale para almacenar la instacia de objeto de formulario 


barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevContabilzarEmpaquetado = new Ext.FormPanel({
		applyTo: 'formularioSIV',
		width: 920,
		height: 460,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Contabilización de Empaquetado</H1>",
		frame: true,
		autoScroll:true,
		items: [fromRevEmpaquetado,
		        gridMovRevEmpaquetado
		        ]
	});
	
	fromRevContabilzarEmpaquetado.doLayout();
});

	/*	CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */	

	//creando datastore y columnmodel para la grid de modificaciones presupuestarias
	var reMovEmpaquetado = Ext.data.Record.create([
	    {name: 'comprobante'}, 
	    {name: 'fecha'},
	    {name: 'descripcion'},
	    {name: 'fechaconta'}
	]);

	var dsMovEmpaquetado =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovEmpaquetado)
	});

	var cmMovEmpaquetado = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width: 30, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'}
	]);
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias

	//creando grid para las modificaciones presupuestarias
	gridMovRevEmpaquetado = new Ext.grid.GridPanel({
		width:870,
		height:250,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:135px',
		autoScroll:true,
		border:true,
		ds: dsMovEmpaquetado,
		cm: cmMovEmpaquetado,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	//fin creando grid para las modificaciones presupuestarias

	gridMovRevEmpaquetado.on({
		'rowcontextmenu': {
		fn: function(grid, numFila, evento){
		var registro = grid.getStore().getAt(numFila);

		//creando componente detalle comprobante
		var comDetalleEmpaquetado = new com.sigesp.vista.comDetalleComprobante({
			tituloVentana: 'Empaquetados',
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
			paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'empaquetado_detalle_contable',
				'codemppro':registro.get('codemppro'),
				'fecemppro':registro.get('fecemppro')})

		});
		//fin creando componente detalle comprobante

		comDetalleEmpaquetado.mostrarVentana();
	}
	}
	});

	//creando formulario principal con parametros de busqueda y grid de modificaciones
	var Xpos = ((screen.width/2)-(300));
	fromRevEmpaquetado = new Ext.form.FieldSet({ 
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
									fieldLabel: 'Empaquetado',
									labelSeparator :'',
									id: 'codemppro',
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
									fieldLabel:"Fecha Empaquetado",
									allowBlank:true,
									width:100,
									id:"fecemppro",
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
							items: [gridMovRevEmpaquetado]
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
	//buscar Empaquetado a reversar
	var codemppro   = Ext.getCmp('codemppro').getValue();
	var fecemppro	 = Ext.getCmp('fecemppro').getValue();

	var JSONObject = {
			'operacion' : 'empaquetado_por_reversar',
			'codemppro'    : codemppro,
			'fecemppro'    : fecemppro
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
			var objetoMovEmpaquetado = eval('(' + datos + ')');
			if(objetoMovEmpaquetado!=''){
				if(objetoMovEmpaquetado!='0'){
					if(objetoMovEmpaquetado.raiz == null || objetoMovEmpaquetado.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridMovRevEmpaquetado.store.removeAll();
					}
					else{
						gridMovRevEmpaquetado.store.loadData(objetoMovEmpaquetado);
					}
				}
				else{
					Ext.MessageBox.show({
						title:'Advertencia',
		 				msg:'no hay datos para mostar',
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
	limpiarFormulario(fromRevEmpaquetado);
	gridMovRevEmpaquetado.store.removeAll();
}

function irProcesar(){
	valido=true;			
	var cadenaJson = "{'operacion': 'reversar_empaquetado', 'codsis':'"+sistema+"', 'nomven':'"+vista+"', 'arrDetalle':[";				
	var arrEmpaquetado = gridMovRevEmpaquetado.getSelectionModel().getSelections();
	var total = arrEmpaquetado.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'comprobante':'"+ arrEmpaquetado[i].get('comprobante')+ "','fecemppro':'"+ arrEmpaquetado[i].get('fecha')+ "'}";
			}
			else {
				cadenaJson = cadenaJson +",{'comprobante':'"+ arrEmpaquetado[i].get('comprobante')+ "','fecemppro':'"+ arrEmpaquetado[i].get('fecha')+ "'}";
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
					tituloVentana: 'Resultado Empaquetados Reversados',
					anchoLabel: 200,
					labelTotal:'Total Empaquetados procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total Empaquetados reversados',
					valorProcesada:arrResultado[1],
					labelError:'Total Empaquetados con error',
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