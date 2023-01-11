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
var	fromRevAproSPI = null;

Ext.onReady(function(){

	//Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	var Xpos = ((screen.width/2)-(920/2));
	var	plRevAproSPI = new Ext.FormPanel({
		applyTo: 'formulario',
		width:623,
		height: 465,
		style: 'position:absolute;left:200px;top:80px',
		title: "<H1 align='center'>Reverso de Modificaciones Presupuestarias de Ingreso</H1>",
		frame: true,
		autoScroll:true,
		items: [fromRevAproSPI,
		        gridSolicitud]
	});
	plRevAproSPI.doLayout();
});

	var reSolicitud = Ext.data.Record.create([
	    {name: 'comprobante'},                      
	    {name: 'fecha'}, 
	    {name: 'descripcion'},   
	    {name: 'procede'},
	    {name: 'detalle'}
	]);

	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});

	var cmSolicitud = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Comprobante</CENTER>", width:25, sortable: true, dataIndex: 'comprobante'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Descripci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'},
//	    {header: "Detalle", width: 20, sortable: true, dataIndex: 'detalle',editor : new Ext.form.TextField({allowBlank : true})}
	]);

	//creando datastore y columnmodel para la grid de spi
	gridSolicitud = new Ext.grid.EditorGridPanel({
		width:570,
		height:250,
		frame:true,
		title:"<H1 align='center'>Comprobantes de Presupuesto de Ingreso</H1>",
		style: 'position:absolute;left:15px;top:160px',
		autoScroll:true,
		border:true,
		ds: dsSolicitud,
		cm: cmSolicitud,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	
	gridSolicitud.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reDetalleIng = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spi_cuenta'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsDetalleIng =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetalleIng)
				});
									
				var cmDetalleIng = new Ext.grid.ColumnModel([
			        {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spi_cuenta'},
			        {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid}, 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				//creando componente detalle comprobante
				var comDetalleIng = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: "<H1 align='center'>Detalle de la Modificaci&#243;n Presupuestaria de Ingreso</H1>",
					anchoVentana: 600,
					altoVentana: 500,
					anchoFormulario: 580,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Comprobante',
								id:'cod_cmp',
								valor: registro.get('comprobante'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fec_cmp',
								valor:registro.get('fecha'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Procede',
								id:'pro_cmp',
								valor:registro.get('procede'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'des_cmp',
								valor:registro.get('descripcion'),
								ancho: 100
						}],
						tienePresupuesto:true,
						tituloGridPresupuestario:'Detalle Presupuestario de Ingreso',
						anchoGridPG :580,
						altoGridPG :150,
						dsPresupuestoGasto: dsDetalleIng,
						cmPresupuestoGasto: cmDetalleIng,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionspi.php',
						paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detspi',
																	'codcom':registro.get('comprobante'),
																	'procede':registro.get('procede')}),
						tieneContable: true,
						anchoGridCO :550,
						altoGridCO :100,
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detscg',
																	    'codcom':registro.get('comprobante'),
																		'procede':registro.get('procede')})
																   
				});
				//fin creando componente detalle comprobante
				
				comDetalleIng.mostrarVentana();
			}
		}
	});

	fromRevAproSPI = new Ext.form.FieldSet({
		    title:'',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 570,
			cls: 'fondo',
			height: 125,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:15px',
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
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
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
							labelWidth: 100,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Procede',
									id: 'procede',
									width: 100,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '6', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');"}
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
	})


function irCancelar(){
	limpiarFormulario(fromRevAproSPI);
	gridSolicitud.store.removeAll();
}
function irAnular(){

}

function irProcesar(){
	valido=true;
	grid = gridSolicitud.getSelectionModel().getSelections();
	cadenajson = "{'operacion':'rev_contabilizar_spi','codsis':'"+sistema+"','nomven':'"+vista+"','arrDetalle':[";
	total = grid.length;
	if (total>0)
	{			
		for (i=0; i<total; i++)
		{
			if (i==0) 
			{
				cadenajson += "{'codcom':'"+grid[i].get('comprobante')+"','fecha':'"+grid[i].get('fecha')+"'," +
							   "'descripcion':'"+grid[i].get('descripcion')+"','procede':'"+grid[i].get('procede')+"'}";                
			}
			else {
				cadenajson += ",{'codcom':'"+grid[i].get('comprobante')+"','fecha':'"+grid[i].get('fecha')+"'," +
								"'descripcion':'"+grid[i].get('descripcion')+"','procede':'"+grid[i].get('procede')+"'}";                
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionspi.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado de Reversar las Modificaciones Presupuestarias de Ingreso',
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
	var JSONObject = {
			'operacion'   : 'buscar_cmpspi',
			'codcmp'      : Ext.getCmp('comprobante').getValue(),
			'procede'     : Ext.getCmp('procede').getValue(),
			'fecha'       : Ext.getCmp('fecha').getValue(),
			'estatus'     : '1'
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionspi.php',
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
