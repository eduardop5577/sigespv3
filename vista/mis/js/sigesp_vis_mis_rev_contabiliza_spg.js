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

var gridMovModPreRev  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromContabilzarRevSPG = null //varibale para almacenar la instacia de objeto de formulario 


barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	/*	CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */	
	var Xpos = ((screen.width/2)-(920/2));
	var	plContabilzarRevSPG = new Ext.FormPanel({
		applyTo: 'formularioSPG',
		width: 640,
		height: 450,
		style: 'position:absolute;left:200px;top:80px',
		title: "<H1 align='center'>Reverso de Modificaciones Presupuestarias de Gasto</H1>",
		frame: true,
		autoScroll:true,
		items: [
		       fromContabilzarRevSPG,
		       gridMovModPreRev
		        ]
	});
	plContabilzarRevSPG.doLayout();
});
	
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias
	var reMovPresupuestario = Ext.data.Record.create([
	    {name: 'comprobante'}, 
	    {name: 'fecha'},
	    {name: 'descripcion'},
	    {name: 'procede'},
	    {name: 'fechaconta'}
	]);

	var dsMovPresupuestario =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovPresupuestario)
	});

	var cmMovPresupuestario = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width: 30, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Descripci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'}
	]);
	//creando datastore y columnmodel para la grid de modificaciones presupuestarias

	//creando grid para las modificaciones presupuestarias
	gridMovModPreRev = new Ext.grid.GridPanel({
		width:600,
		height:240,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:160px',
		autoScroll:true,
		border:true,
		ds: dsMovPresupuestario,
		cm: cmMovPresupuestario,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	//fin creando grid para las modificaciones presupuestarias

	gridMovModPreRev.on({
		'rowcontextmenu': {
		fn: function(grid, numFila, evento){
		var registro = grid.getStore().getAt(numFila);


		//creando datastore y columnmodel para la grid de detalles presupuestarios
		var reMovDetPresupuestario = Ext.data.Record.create([
		    {name: 'estructura'}, 
		    {name: 'estcla'},
		    {name: 'spg_cuenta'},
		    {name: 'operacion'},
		    {name: 'monto'},
		    {name: 'disponibilidad'}
		]);

		var dsMovDetPresupuestario =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovDetPresupuestario)
		});

		var cmMovDetPresupuestario = new Ext.grid.ColumnModel([
		    {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
		    {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
		    {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
		    {header: "Operaci&#243;n", width: 35, sortable: true, dataIndex: 'operacion'},
		    {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
		    {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp}
		]);
		//fin creando datastore y columnmodel para la grid de detalles presupuestarios

		//creando componente detalle comprobante
		var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
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
			tienePresupuesto:true,
			tituloGridPresupuestario:'Detalle Presupuestario de Gasto',
			anchoGridPG :580,
			altoGridPG :150,
			dsPresupuestoGasto: dsMovDetPresupuestario,
			cmPresupuestoGasto: cmMovDetPresupuestario,
			rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
			paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'detalle_presupuesto',
				'numcom':registro.get('comprobante'),
				'procede':registro.get('procede')}),
				tieneContable: false
		});
		//fin creando componente detalle comprobante

		comDetalleModificacion.mostrarVentana();
	}
	}
	});

	//creando formulario principal con parametros de busqueda y grid de modificaciones
	var Xpos = ((screen.width/2)-(300));
	fromContabilzarRevSPG = new Ext.form.FieldSet({
		    title:'',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 600,
			cls: 'fondo',
			height: 125,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 100,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Comprobante',
									labelSeparator :'',
									id: 'numcom',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 150,
									allowBlank:false
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
							labelWidth: 100,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Procede',
									labelSeparator :'',
									id: 'procede',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '6', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');"},
									width: 100,
									allowBlank:false
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
							labelWidth: 100,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									id:"fecmov",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
						}]
					}]
		
	});
	//fin creando formulario principal con parametros de busqueda y grid de modificaciones

	/*	FIN CREACION DE VENTANA PRINCIPAL FORMULARIO QUE CONTIENE PARAMENTROS
	 *  DE BUSQUEDA Y GRID DE MODIFICACIONES A APROBAR.	  
	 */



function irBuscar( ){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar modificaciones a reversar
	var numcom   = Ext.getCmp('numcom').getValue();
	var procede  = Ext.getCmp('procede').getValue();
	var fecmov	 = Ext.getCmp('fecmov').getValue();

	var JSONObject = {
			'operacion' : 'buscar_por_reversar',
			'numcom'    : numcom,
			'procede'   : procede,
			'fecmov'    : fecmov
	}

	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
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
					}
					else{
						gridMovModPreRev.store.loadData(objetoMovbco);
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
			Ext.MessageBox.alert('Error', 'Error de comunicaci?n con el Servidor'); 
		}	
	});
}


function irCancelar(){
	limpiarFormulario(fromContabilzarRevSPG);
	gridMovModPreRev.store.removeAll();
}

function irProcesar(){
	valido=true;
	var cadenaJson = "{'operacion': 'rev_contabilizar_spg', 'codsis':'"+sistema+"','nomven':'"+vista+"', 'arrDetalle':[";				
	var arrModificaciones = gridMovModPreRev.getSelectionModel().getSelections();
	var total = arrModificaciones.length;
	if (total>0){
		obtenerMensaje('procesar','','Procesando Datos');
		for (i=0; i < total; i++){
			if (i==0) {
				cadenaJson = cadenaJson +"{'codcom':'"+ arrModificaciones[i].get('comprobante')+ "','fecha':'"+ arrModificaciones[i].get('fecha')+ "','procede':'"+ arrModificaciones[i].get('procede')+ "','descripcion':'"+ arrModificaciones[i].get('descripcion')+ "','fechacont':'"+ arrModificaciones[i].get('fechaconta')+ "'}";
			}
			else {
				cadenaJson = cadenaJson +",{'codcom':'"+ arrModificaciones[i].get('comprobante')+ "','fecha':'"+ arrModificaciones[i].get('fecha')+ "','procede':'"+ arrModificaciones[i].get('procede')+ "','descripcion':'"+ arrModificaciones[i].get('descripcion')+ "','fechacont':'"+ arrModificaciones[i].get('fechaconta')+ "'}";
			}
		}

		cadenaJson = cadenaJson + ']}';
		var objdata= eval('(' + cadenaJson + ')');	
		objdata=JSON.stringify(objdata);
		var parametros = 'ObjSon='+objdata; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Reverso de Modificaciones Presupuestarias de Gastos',
					anchoLabel: 200,
					labelTotal:'Total modificaciones procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total modificaciones contabilizados',
					valorProcesada:arrResultado[1],
					labelError:'Total modificaciones con error',
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
				Ext.MessageBox.alert('Error', 'Error al procesar la Informaci?n'); 
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