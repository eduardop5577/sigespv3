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

var cmbtipcompra = null;
var comcampocatproveedor = null;
var date = new Date();
var dia = date.getDate();
var mes = date.getMonth()+1;
var anio = date.getFullYear();
if (mes < 10)
{
	mes="0"+mes;
}
if (dia < 10)
{
	dia="0"+dia;
}

var fecha_hoy=dia+"/"+mes+"/"+anio;
//-----------------------------------------------------------------------------------------------------------------------	
function tipoCompra(valor){
	if (valor=="B")
	{
		return 'Bienes';
	}
	else
	{
		return 'Servicios';	
	}
}
//-----------------------------------------------------------------------------------------------------------------------	
	//creando datastore y columnmodel para la grid de solicitudes
	var reOrdenCompra = Ext.data.Record.create([
	    {name: 'numordcom'}, 
	    {name: 'cod_pro'},
	    {name: 'estcondat'},
	    {name: 'fecaprord'},
		{name: 'fechaconta'},
		{name: 'fecordcom'},
		{name: 'obscom'},
		{name: 'nompro'}
	]);
	
	var dsOrdenCompra =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reOrdenCompra)
	});
						
	var cmOrdenCompra = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>Tipo</CENTER>", width: 30, sortable: true, dataIndex: 'estcondat',renderer:tipoCompra},
        {header: "<CENTER>N° Orden</CENTER>", width: 30, sortable: true, dataIndex: 'numordcom'},
		{header: "<CENTER>Fecha Orden <br> de Compra</CENTER>", width: 30, sortable: true, dataIndex: 'fecordcom'},
        {header: "<CENTER>Concepto</CENTER>", width: 100, sortable: true, dataIndex: 'obscom'}
	]);
	//creando datastore y columnmodel para la grid de reintegros
				
	//creando grid para los reintegros
	var gridOrdenCompra = new Ext.grid.GridPanel({
	 		width:880,
	 		height:220,
			frame:true,
			title:'',
			style: 'position:absolute;left:15px;top:240px',
			autoScroll:true,
     		border:true,
     		ds: dsOrdenCompra,
       		cm: cmOrdenCompra,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	//fin creando grid para los reintegros
//-------------------------------------------------------------------------------------------------------------------------	
	// Creando la ventana emergente de los detalles
	
	gridOrdenCompra.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovOrdencompra = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'operacion'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovOrdencompra =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovOrdencompra)
				});
									
				var cmMovOrdencompra = new Ext.grid.ColumnModel([
			        {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				
				if(registro.get('estcondat')=='B')
				{
					var tp_com="Bienes";
				}
				else
				{
					var tp_com="Servicios";
				}
				//creando componente detalle comprobante
				var comDetalleOrdencompra = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: 'Reverso de Anulación de Orden de Compra/Servicio',
					anchoVentana: 600,
					altoVentana: 500,
					anchoFormulario: 580,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'N&#250;mero',
								id:'cmpsoc',
								valor: registro.get('numordcom'),
								ancho: 200 
								},
						        {
								tipo:'textfield',
								etiqueta:'Tipo',
								id:'tip_compra',
								valor:tp_com,
								ancho: 100
								},
							    {	
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fecsoc2',
								valor:registro.get('fecordcom'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Fecha Aprobaci&#243;n',
								id:'fecaprsoc2',
								valor:registro.get('fecaprord'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Proveedor',
								id:'prosoc',
								valor:registro.get('cod_pro')+"   "+registro.get('nompro'),//y falta el nombre del proveedor
								ancho: 300
								}],
					tienePresupuesto:true,
					tituloGridPresupuestario:'Detalle Presupuestario de Gasto',
					anchoGridPG :580,
					altoGridPG :150,
					dsPresupuestoGasto: dsMovOrdencompra,
					cmPresupuestoGasto: cmMovOrdencompra,
					rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsoc.php',
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles',
																'numordcom':registro.get('numordcom'),
																'estcondat':registro.get('estcondat')}),
					tieneContable: false
				});
				//fin creando componente detalle comprobante
				
				comDetalleOrdencompra.mostrarVentana();
			}
		}
	});

//-----------------------------------------------------------------------------------------------------------------------	

	//creando store para la tipo compra (reverso anulación)
	var tipcompra = 	[
                    	['-- Bienes/Servicios --','-'],
                    	['Bienes','B'],
                    	['Servicios','S']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttipcompra = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tipcompra
	});
	//fin creando store para el combo tipo compra

	//creando objeto combo tipo compra
	var cmbtipcompra = new Ext.form.ComboBox({
		store : sttipcompra,
		fieldLabel : 'Tipo Compra ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'estcondat',
		width:200,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local',
		emptyText : '-- Bienes/Servicios --'
	});
	
	//fin creando objeto nacionalidad
//-------------------------------------------------------------------------------------------------------------------------	

//Creando el campo catalogo para llamar al proveedor (reverso anulación)
	var reg_proveedor = Ext.data.Record.create([
		{name: 'cod_pro'},
		{name: 'nompro'},
		{name: 'dirpro'}
	]);
	
	var dsproveedor =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_proveedor)
	});
						
	var colmodelproveedor = new Ext.grid.ColumnModel([
        {header: "Codigo", width: 20, sortable: true,   dataIndex: 'cod_pro'},
        {header: "Nombre", width: 40, sortable: true, dataIndex: 'nompro'}
    ]);
	//fin del campo de proveedores
	
	//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
	comcampocatproveedor = new com.sigesp.vista.comCampoCatalogo({
		titvencat: 'Catalogo de Proveedores',
		anchoformbus: 450,
		altoformbus:150,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dsproveedor,
		colmodelocat: colmodelproveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_proveedor'",
		arrfiltro:[{etiqueta:'Código',id:'mcod_pro',valor:'cod_pro'},
		           {etiqueta:'Nombre',id:'mnompro',valor:'nompro'},
		           {etiqueta:'Dirección',id:'mdirpro',valor:'dirpro'}],
		posicion:'position:absolute;left:10px;top:30px',
		tittxt:'Proveedor',
		idtxt:'cod_pro',
		campovalue:'cod_pro',
		anchoetiquetatext:200,
		anchotext:135,
		anchocoltext:0.50,
		idlabel:'nompro',
		labelvalue:'nompro',
		anchocoletiqueta:0.45,
		anchoetiqueta:400,
		anchofieldset: 700,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false
	});
	//fin componente para el campo de cuentas contables para las solicitudes a pagar

//-------------------------------------------------------------------------------------------------------------------------	
	
	//creando funcion que construye formulario principal para reverso de la contabilización
	var	fromBusquedaReversoAnulaSOC = new Ext.form.FieldSet({
			title:'Datos de la Orden de Compra',
			style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 880,
			cls: 'fondo',
			height: 210,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Nro. de Orden de Compra',
									labelSeparator :'',
									id: 'numordcom',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 130,
									listeners: {
										'onClick': function(){
									}			
									},
									allowBlank:false
								}]
							}]
					},
					comcampocatproveedor.fieldsetCatalogo,
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:70px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [cmbtipcompra]
						}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:100px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Registro",
									labelSeparator :'',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									id:"fecordcom",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:130px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Aprobación",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									id:"fecaprord",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:160px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Anulación",
									labelSeparator :'',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									id:"fechaanula",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	});

//-------------------------------------------------------------------------------------------------------------------------	
//-------------------------------------------------------------------------------------------------------------------------	


barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromReversoAnularSOC = new Ext.FormPanel({
		applyTo: 'formulario_reverso_anulaSOC',
		width: 920,
		height: 550,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Anulación de Orden de Compra/Servicio</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaReversoAnulaSOC,
		        gridOrdenCompra
		        ]
	});
	fromReversoAnularSOC.doLayout();
});

function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	var numordcom      = Ext.getCmp('numordcom').getValue();
	var cod_pro		   = Ext.getCmp('cod_pro').getValue();
	var estcondat	   = Ext.getCmp('estcondat').getValue();
	var fecaprord	   = Ext.getCmp('fecaprord').getValue();
	if(fecaprord!='')
	{
		fecaprord = Ext.getCmp('fecaprord').getValue().format('Y-m-d');
	}
	var fecordcom	   = Ext.getCmp('fecordcom').getValue();
	if(fecordcom!='')
	{
		fecordcom = Ext.getCmp('fecordcom').getValue().format('Y-m-d');
	}
	var fechaanula     = Ext.getCmp('fechaanula').getValue();
	if(fechaanula!='')
	{
		fechaanula = Ext.getCmp('fechaanula').getValue().format('Y-m-d');
	}
	var JSONObject = {
			'operacion'   : 'buscar_por_rev_anular',
			'numordcom' : numordcom,
			'cod_pro' : cod_pro,
			'estcondat' : estcondat,
			'fecaprord' : fecaprord,
			'fecordcom' : fecordcom,
			'fechaanula' : fechaanula
		}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsoc.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();		
			var datos = resultado.responseText;
			var objetoOrdenes = eval('(' + datos + ')');
			if(objetoOrdenes!=''){
				if(objetoOrdenes!='0'){
					if(objetoOrdenes.raiz == null || objetoOrdenes.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridOrdenCompra.store.removeAll();
					}
					else{
						dsOrdenCompra.loadData(objetoOrdenes);
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

function irCancelar(){
	limpiarFormulario(fromBusquedaReversoAnulaSOC);
	gridOrdenCompra.store.removeAll();
}

function irProcesar(){
	valido=true;
	arrOrdenCompra = gridOrdenCompra.getSelectionModel().getSelections();
	total = arrOrdenCompra.length;
	cadenajson = "{'operacion':'rev_anulacion','codsis':'"+sistema+"','nomven':'"+vista+"','arrDetalle':[";
	if (total>0)
	{			
		for (i=0; i<total && valido==true; i++)
		{
			if (i==0) 
			{
				cadenajson += "{'numordcom':'"+arrOrdenCompra[i].get('numordcom')+"'," +
						       "'estcondat':'"+arrOrdenCompra[i].get('estcondat')+"'}";
			}
			else {
				cadenajson += ",{'numordcom':'"+arrOrdenCompra[i].get('numordcom')+"'," +
						        "'estcondat':'"+arrOrdenCompra[i].get('estcondat')+"'}";
			}
		}
		if(valido){
			cadenajson += "]}";	
			var parametros = 'ObjSon='+cadenajson;
			Ext.Ajax.request({
				url : '../../controlador/mis/sigesp_ctr_mis_integracionsoc.php',
				params : parametros,
				method: 'POST',
				success: function (resultado, request)
				{ 
					var resultado = resultado.responseText;
					var arrResultado = resultado.split("|");
					Ext.Msg.hide();
					//creando componente detalle comprobante
					var comResultado = new com.sigesp.vista.comResultadoIntegrador({
						tituloVentana: "<H1 align='center'>Resultado Reversar la Anulaci&#243;n de las Ordenes de Compra</H1>",
						anchoLabel: 200,
						labelTotal:'Total ordenes procesadas',
						valorTotal: arrResultado[0],
						labelProcesada:'Total ordenes reversadas',
						valorProcesada:arrResultado[1],
						labelError:'Total ordenes con error',
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