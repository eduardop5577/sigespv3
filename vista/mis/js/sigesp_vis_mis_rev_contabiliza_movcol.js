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
	//creando datastore y columnmodel para la grid de colocaciones bancarias
	var reColMovBanco = Ext.data.Record.create([
	    {name: 'numdoc'}, 
	    {name: 'fecmovcol'},
	    {name: 'conmov'},
		{name: 'codban'},
		{name: 'ctaban'},
		{name: 'codope'}
	]);
	
	var dsColMovBanco =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reColMovBanco)
	});
						
	var cmColMovBanco = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "N° Documento", width: 30, sortable: true, dataIndex: 'numdoc'},
		{header: "Fecha Movimiento", width: 30, sortable: true, dataIndex: 'fecmovcol'},
        {header: "Concepto", width: 60, sortable: true, dataIndex: 'conmov'}
	]);
	//creando datastore y columnmodel para la grid de colocaciones bancarias
				
	//creando grid de colocaciones bancarias
	var gridColMovBanco = new Ext.grid.GridPanel({
	 		width:880,
	 		height:250,
			frame:true,
			title:'',
			style: 'position:absolute;left:15px;top:160px',
			autoScroll:true,
     		border:true,
     		ds: dsColMovBanco,
       		cm: cmColMovBanco,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	//fin creando grid de colocaciones bancarias
//-----------------------------------------------------------------------------------------------------------------------	
		// Creando la ventana emergente de los detalles
	gridColMovBanco.on({
		'rowdblclick': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovBancario = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovBancario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBancario)
				});
									
				var cmMovBancario = new Ext.grid.ColumnModel([
			        {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				if(registro.get('codope')=='ND')
				{
					var tp_mov="Nota Débito";
				}
				else if(registro.get('codope')=='NC')
				{
					var tp_mov="Nota Crédito";
				}
				else if(registro.get('codope')=='DP')
				{
					var tp_mov="Deposito";
				}
				//creando componente detalle comprobante
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: 'Contabilizaci&#243;n de Colocaciones',
					anchoVentana: 600,
					altoVentana: 500,
					anchoFormulario: 580,
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
								valor:registro.get('fecmovcol'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'cmov',
								valor:registro.get('conmov'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Operaci&#243;n',
								id:'tip_mov',
								valor:tp_mov,
								ancho: 100
								}],
					
						tienePresupuesto:false,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
						tieneContable: true,
						anchoGridCO :550,
						altoGridCO :100,
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable_movcol',
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
//creando store para el tipo de documentos para la colocación
	var tipoperacion_col = 	[
                    	['-- Seleccione --','-'],
                    	['Nota Débito','ND'],
						['Nota Crédito','NC'],
						['Deposito','DP']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttipoperacion_col = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tipoperacion_col
	});
	//fin creando store para el tipo de documentos

	//creando objeto combo tipo documentos
	var cmbtipoperacion_col = new Ext.form.ComboBox({
		store : sttipoperacion_col,
		fieldLabel : 'Operación ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'codope',
		width:200,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local',
		emptyText : '-- Seleccione --'
	});
	
	//fin creando objeto tipo documentos
//-------------------------------------------------------------------------------------------------------------------------	
//creando funcion que construye formulario principal para reverso de contabilización de colocaciones
	var	fromBusquedaRevMovColSCB = new Ext.form.FieldSet({ 
			width: 880,
			height: 115,
			title: '',
			frame: true,
			cls: 'fondo',
			border:true,
			autoScroll:true,
			style: 'position:absolute;left:15px;top:20px',
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:15px',
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
					style: 'position:absolute;left:15px;top:45px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,
							items: [{
									xtype:"datefield",
									 fieldLabel:"Fecha Del Documento",
									 allowBlank:true,
									 width:100,
									 binding:true,
									 defaultvalue:'1900-01-01',
									 hiddenvalue:'',
									 id:"fecmovcol",
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
							labelWidth: 200,
							items: [cmbtipoperacion_col]
						}]
					}]
	});

//-------------------------------------------------------------------------------------------------------------------------	
//-------------------------------------------------------------------------------------------------------------------------	
barraherramienta    = true;
//var fechacontabil = new Date(Ext.getCmp('fecaconta').getValue());

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromRevContabilzarMovcolSCB = new Ext.FormPanel({
		applyTo: 'formulario_RevMovcolSCB',
		width: 930,
		height: 470,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Reverso de Contabilización de Colocaciones</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaRevMovColSCB,gridColMovBanco]
	});
	
	fromRevContabilzarMovcolSCB.doLayout();
});

function irBuscar(){
	var numdoc     = Ext.getCmp('numdoc').getValue();
	var fecmovcol  = Ext.getCmp('fecmovcol').getValue();
    var operacion  = Ext.getCmp('codope').getValue();
	if(Ext.getCmp('codope').getValue()=="")
	{
		Ext.Msg.alert('Mensaje','Debe seleccionar un tipo de operación!');
	}
	else
	{
		obtenerMensaje('procesar','','Buscando Datos');
		var JSONObject = {
				'operacion'   : 'buscar_por_rev_contabilizacion_movcol',
				'numdoc' : numdoc,
				'fecmovcol' : fecmovcol,
				'codope' : operacion,
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
							gridColMovBanco.store.removeAll();
						}
						else{
							dsColMovBanco.loadData(objetoMovbco);
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
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error de comunicacion con el Servidor'); 
			}
		});	
	}
}

function irCancelar(){
	limpiarFormulario(fromBusquedaRevMovColSCB);
	gridColMovBanco.store.removeAll();
}

function irProcesar(){
		valido=true;
		gridColMovBanco = gridColMovBanco.getSelectionModel().getSelections();
		total = gridColMovBanco.length;
		if (total>0)
		{			
			for (i=0; i < total; i++)
			{
				Delay(500);
				obtenerMensaje('procesar','','Procesando Datos');
				var JSONObject = {
						'operacion' : 'rev_contabilizar_movcol',
						'codsis'    : sistema,
						'nomven'    : vista,
						'codban'    : gridColMovBanco[i].get('codban'),
						'ctaban'    : gridColMovBanco[i].get('ctaban'),
						'numdoc'    : gridColMovBanco[i].get('numdoc'),
						'codope'    : gridColMovBanco[i].get('codope'),
						'fecmovcol' : gridColMovBanco[i].get('fecmovcol')
						
					}
				objdata=JSON.stringify(JSONObject);
				var parametros = 'ObjSon='+objdata; 
				Ext.Ajax.request({
				url : '../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
				params : parametros,
				method: 'POST',
				success: function (resultado, request)
				{ 
					Ext.Msg.hide();
					var datajson = eval('(' + datos + ')');
					if (datajson.raiz.valido==true)
					{	
						Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					}
					else
					{
						Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
					}
				},
				failure: function (result,request) 
				{ 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
				}					
				});
			}
			irCancelar();
		}
}