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
function tipoCarta(valor){
	if (valor=="")
	{
		return '--No Aplica--';
	}
}
//-----------------------------------------------------------------------------------------------------------------------	
	//creando datastore y columnmodel para la grid de orden de pago directa
	var reOpdBanco = Ext.data.Record.create([
	    {name: 'numdoc'}, 
	    {name: 'fecmov'},
	    {name: 'conmov'},
		{name: 'codban'},
		{name: 'ctaban'},
		{name: 'codope'},
		{name: 'nomban'},
		{name: 'cod_pro'},
		{name: 'nompro'},
		{name: 'nombene'},
		{name: 'apebene'},
		{name: 'ced_bene'},
		{name: 'tipo_destino'},
		{name: 'estmov'}
		
	]);
	
	var dsOpdBanco =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reOpdBanco)
	});
						
	var cmOpdBanco = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>N° Documento</CENTER>", width: 30, sortable: true, dataIndex: 'numdoc'},
		{header: "<CENTER>Fecha Movimiento</CENTER>", width: 30, sortable: true, dataIndex: 'fecmov'},
        {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'conmov'}
	]);
	//creando datastore y columnmodel para la grid de colocaciones bancarias
				
	//creando grid de colocaciones bancarias
	var gridOpdBanco = new Ext.grid.GridPanel({
	 		width:855,
	 		height:250,
			frame:true,
			title:'',
			style: 'position:absolute;left:15px;top:140px',
			autoScroll:true,
     		border:true,
     		ds: dsOpdBanco,
       		cm: cmOpdBanco,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	//fin creando grid de colocaciones bancarias
//-----------------------------------------------------------------------------------------------------------------------	
		// Creando la ventana emergente de los detalles
	
	gridOpdBanco.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovBancario = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'denominacion'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovBancario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBancario)
				});
									
				var cmMovBancario = new Ext.grid.ColumnModel([
			        {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "Cuenta", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "Denominacion", width: 100, sortable: true, dataIndex: 'denominacion'},
			        {header: "Monto", width: 40, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				if(registro.get('codope')=='OP')
				{
					var tp_mov="ORDEN DE PAGO DIRECTA";
				}
				//creando componente detalle comprobante
				if(registro.get('tipo_destino')=='B')
				{
					var tit_dest="Beneficiario";
					var nombre_dest=registro.get('ced_bene')+" - "+registro.get('apebene')+", "+registro.get('nombene');
				}
				else
				{
					var tit_dest="Proveedor";
					var nombre_dest=registro.get('cod_pro')+" - "+registro.get('nompro');
				}
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: 'Contabilizaci&#243;n de Banco',
					anchoVentana: 720,
					altoVentana: 500,
					anchoFormulario: 680,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Comprobante',
								id:'ndoc',
								valor: registro.get('numdoc'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fmov',
								valor:registro.get('fecmov'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'cmov',
								valor:registro.get('conmov'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Operaci&#243;n',
								id:'tip_mov',
								valor:tp_mov,
								ancho: 300
								},
							    {
								tipo:'textfield',
								etiqueta:'Banco',
								id:'b_mov',
								valor:registro.get('codban')+"   "+registro.get('nomban'),
								ancho: 300
								},
								{
									tipo:'textfield',
									etiqueta:tit_dest,
									id:'prosoc',
									valor:nombre_dest,
									ancho: 300
								}],
						tienePresupuesto:false,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
						tieneContable: true,
						anchoGridCO :680,
						altoGridCO :100,
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable',
																	    'numdoc':registro.get('numdoc'),
																		'codban':registro.get('codban'),
																		'ctaban':registro.get('ctaban'),
																		'codope':registro.get('codope')})
																   
				});
				//fin creando componente detalle comprobante
				
				comMovBancario.mostrarVentana();
			}
		}
	});

//-----------------------------------------------------------------------------------------------------------------------	

	//creando funcion que construye formulario principal para contabilización de Pagos directos
	var	fromBusquedaMovOpdSCB = new Ext.form.FieldSet({ 
		    title:'Datos de la Orden de Pago',
		    style: 'position:absolute;left:15px;top:15px',
			border:true,
			width: 855,
			cls: 'fondo',
			height: 100,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Número de Documento',
									labelSeparator :'',
									id: 'numdoc',
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
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:50px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Del Documento",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									id:"fecmov",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	});
//-----------------------------------------------------------------------------------------------------------------------	
//-----------------------------------------------------------------------------------------------------------------------	
barraherramienta    = true;
//var fechacontabil = new Date(Ext.getCmp('fecaconta').getValue());

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarMovopdSCB = new Ext.FormPanel({
		applyTo: 'formulario_MovopdSCB',
		width: 895,
		height: 445,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Contabilización de Orden de Pago Directa</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaMovOpdSCB,
		        gridOpdBanco]
	});
	
	fromContabilzarMovopdSCB.doLayout();
});

function irBuscar(){
	var numdoc     = Ext.getCmp('numdoc').getValue();
	var fecmov	   = Ext.getCmp('fecmov').getValue();

	obtenerMensaje('procesar','','Buscando Datos');
	var JSONObject = {
			'operacion'   : 'buscar_contabilizacion_opd',
			'numdoc' : numdoc,
			'fecmov' : fecmov
		}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
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
						gridOpdBanco.store.removeAll();
					}
					else{
						dsOpdBanco.loadData(objetoMovbco);
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
	limpiarFormulario(fromBusquedaMovOpdSCB);
	gridOpdBanco.store.removeAll();
}

function irProcesar(){
		valido=true;
		grid = gridOpdBanco.getSelectionModel().getSelections();
		cadenajson = "{'operacion':'contabilizar_opd','codsis':'"+sistema+"','nomven':'"+vista+"','arrDetalle':[";
		total = grid.length;
		if (total>0)
		{			
			for (i=0; i < total; i++)
			{
				if (i==0) 
				{
					cadenajson += "{'codban':'"+grid[i].get('codban')+"','ctaban':'"+grid[i].get('ctaban')+"'," +
					"'numdoc':'"+grid[i].get('numdoc')+"','codope':'"+grid[i].get('codope')+"'," +
					"'estmov':'"+grid[i].get('estmov')+"'}";                
				}
				else {
					cadenajson += ",{'codban':'"+grid[i].get('codban')+"','ctaban':'"+grid[i].get('ctaban')+"'," +
					"'numdoc':'"+grid[i].get('numdoc')+"','codope':'"+grid[i].get('codope')+"'," +
					"'estmov':'"+grid[i].get('estmov')+"'}";                
				}
			}	
			cadenajson += "]}";	
			var parametros = 'ObjSon='+cadenajson;	
			Ext.Ajax.request({
				url : '../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
				params : parametros,
				method: 'POST',
				success: function (resultado, request)
				{ 
					var resultado = resultado.responseText;
					var arrResultado = resultado.split("|");
					Ext.Msg.hide();
					//creando componente detalle comprobante
					var comResultado = new com.sigesp.vista.comResultadoIntegrador({
						tituloVentana: 'Resultado Contabilizaci&#243;n de Movimientos Bancarios',
						anchoLabel: 200,
						labelTotal:'Total movimientos procesados',
						valorTotal: arrResultado[0],
						labelProcesada:'Total movimientos contabilizados',
						valorProcesada:arrResultado[1],
						labelError:'Total movimientos con error',
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
			irCancelar();
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