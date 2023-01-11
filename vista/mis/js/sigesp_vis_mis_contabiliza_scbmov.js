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
	//creando datastore y columnmodel para la grid de movimientos bancarios
	var reMovBanco = Ext.data.Record.create([
	    {name: 'codban'}, 
	    {name: 'ctaban'}, 
	    {name: 'estmov'}, 
	    {name: 'codope'}, 
	    {name: 'numdoc'}, 
	    {name: 'numcarord'},
	    {name: 'fecmov'},
	    {name: 'conmov'},
		{name: 'nomban'},
		{name: 'cod_pro'},
		{name: 'nompro'},
		{name: 'nombene'},
		{name: 'apebene'},
		{name: 'ced_bene'},
		{name: 'tipo_destino'}
	]);
	
	var dsMovBanco =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBanco)
	});
						
	var cmMovBanco = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>N° Documento</CENTER>", width: 30, sortable: true, dataIndex: 'numdoc'},
        {header: "<CENTER>N° Carta Orden</CENTER>", width: 30, sortable: true, dataIndex: 'numcarord',renderer:tipoCarta},
		{header: "<CENTER>Fecha Movimiento</CENTER>", width: 30, sortable: true, dataIndex: 'fecmov'},
        {header: "<CENTER>Concepto</CENTER>", width: 60, sortable: true, dataIndex: 'conmov'}
	]);
	//creando datastore y columnmodel para la movimientos bancarios
				
	//creando grid para los movimientos bancarios
	var gridMovBanco = new Ext.grid.GridPanel({
	 		width:870,
	 		height:250,
			frame:true,
			title:'',
			style: 'position:absolute;left:15px;top:220px',
			autoScroll:true,
     		border:true,
     		ds: dsMovBanco,
       		cm: cmMovBanco,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	//fin creando grid para los reintegros
//-----------------------------------------------------------------------------------------------------------------------	
		// Creando la ventana emergente de los detalles
	
	gridMovBanco.on({
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
				if(registro.get('codope')=='ND')
				{
					var tp_mov="Nota Débito";
				}
				else if(registro.get('codope')=='NC')
				{
					var tp_mov="Nota Crédito";
				}
				else if(registro.get('codope')=='CH')
				{
					var tp_mov="Cheque";
				}
				else if(registro.get('codope')=='DP')
				{
					var tp_mov="Deposito";
				}
				else if(registro.get('codope')=='RE')
				{
					var tp_mov="Retiro";
				}
				if ((registro.get('codope')=='ND')||(registro.get('codope')=='CH')||(registro.get('codope')=='RE'))
				{
					var tit='Detalle Presupuestario de Gasto';
				}
				else
				{
					var tit='Detalle Presupuestario de Ingreso';
				}
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
			
				//creando componente detalle comprobante
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
									ancho: 100
								},
								{
									tipo:'textfield',
									etiqueta:'Operaci&#243;n',
									id:'tip_mov',
									valor:tp_mov,
									ancho: 100
								},
							    {
									tipo:'textfield',
									etiqueta:'Banco',
									id:'b_mov',
									valor:registro.get('codban')+"   "+registro.get('nomban'),
									ancho: 250
								},
								{
									tipo:'textfield',
									etiqueta:tit_dest,
									id:'prosoc',
									valor:nombre_dest,
									ancho: 300
					}],
						tienePresupuesto:true,
						tituloGridPresupuestario:tit,
						anchoGridPG :680,
						altoGridPG :150,
						dsPresupuestoGasto: dsMovBancario,
						cmPresupuestoGasto: cmMovBancario,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
						paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto_ing',
																	'numdoc':registro.get('numdoc'),
																	'codban':registro.get('codban'),
																	'ctaban':registro.get('ctaban'),
																	'codope':registro.get('codope')}),
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

	//creando store para el tipo de documentos
	var tipoperacion = 	[
                    	['-- Seleccione --','-'],
                    	['Nota Débito','ND'],
						['Nota Crédito','NC'],
						['Cheque','CH'],
						['Deposito','DP'],
                    	['Retiro','RE']
                  		]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttipoperacion = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tipoperacion
	});
	//fin creando store para el tipo de documentos

	//creando objeto combo tipo documentos
	var cmbtipoperacion = new Ext.form.ComboBox({
		store : sttipoperacion,
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
		emptyText : '-- Seleccione --',
		listeners: {
			'select': function(){
				if(this.getValue()=='DP'){
					var myJSONObject = {
							"operacion" : "verificar_config",
							"sistema"   : "MIS",
							"seccion"   : "BANCO",
							"variable"  : "FECHA_CONT_DEPOSITOS",
					};
							
					var ObjSon=Ext.util.JSON.encode(myJSONObject);
					var parametros ='ObjSon='+ObjSon;
					Ext.Ajax.request({
						url: '../../controlador/mis/sigesp_ctr_mis_integracionscb.php',
						params: parametros,
						method: 'POST',
						success: function ( result, request ) { 
								var config = result.responseText;
								if (config != 0)
								{
									Ext.getCmp('fecconta').enable();
									Ext.getCmp('fecconta').setValue(new Date().format('d/m/Y'))
								}
						},
						failure: function ( result, request){ 
								Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
						}
					});
				}
			}
	   	}
	});
	
	//fin creando objeto tipo documentos
//-------------------------------------------------------------------------------------------------------------------------	
	//creando funcion que construye formulario principal para contabilización de banco
	var	fromBusquedaSCB = new Ext.form.FieldSet({ 
		    title:'Datos del Movimiento',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 190,
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
							'onClick': function(){}			
						},
						allowBlank:false,
						changeCheck: function(){
							var textvalor = this.getValue();
							dsMovBanco.filter('numdoc',textvalor,true);
							if(String(textvalor) !== String(this.startValue)){
								this.fireEvent('change', this, textvalor, this.startValue);
							} 
						}, 
						initEvents: function(){
							AgregarKeyPress(this);
						}
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
						xtype: 'textfield',
						fieldLabel: 'Número de Carta Orden',
						labelSeparator :'',
						id: 'numcarord',
						autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
						width: 130,
						listeners: {
							'onClick': function(){}			
						},
						allowBlank:false,
						changeCheck: function(){
							var textvalor = this.getValue();
							dsMovBanco.filter('numcarord',textvalor,true);
							if(String(textvalor) !== String(this.startValue)){
								this.fireEvent('change', this, textvalor, this.startValue);
							} 
						}, 
						initEvents: function(){
							AgregarKeyPress(this);
						}
					}]
				}]
			},
			{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:80px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items: [{xtype:"datefield",
						fieldLabel:"Fecha Del Documento",
						allowBlank:true,
						width:100,
						binding:true,
						labelSeparator :'',
						defaultvalue:'1900-01-01',
						hiddenvalue:'',
						id:"fecmov",
						autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
						changeCheck: function(){
							var textvalor = this.getValue();
							dsMovBanco.filter('fecmov',textvalor,true);
							if(String(textvalor) !== String(this.startValue)){
								this.fireEvent('change', this, textvalor, this.startValue);
							} 
						}, 
						initEvents: function(){
							AgregarKeyPress(this);
						}
					}]
				}]
			},
			{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:110px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 200,
					items: [cmbtipoperacion]
				}]
			},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:140px',
				items: [{
					layout: "form",
					border: false,					
					labelWidth: 200,
					items: [{
						xtype:"datefield",
						fieldLabel:"Fecha Contabilizaci&#243;n",
						labelSeparator :'',
						name:"fecconta",
						allowBlank:false,
						width:100,
						disabled:true,
						id:"fecconta",
						autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
					}]
				}]
			}]
	});
//-----------------------------------------------------------------------------------------------------------------------	
//-----------------------------------------------------------------------------------------------------------------------	

barraherramienta    = true;

Ext.onReady(function() {
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
					 
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarSCB = new Ext.FormPanel({
		applyTo: 'formularioSCB',
		width: 920,
		height: 500,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:35px;',
		title: "<H1 align='center'>Contabilización de Banco</H1>",
		frame: true,
		autoScroll:true,
		items: [fromBusquedaSCB,
		        gridMovBanco
		        ]
	});
	
	fromContabilzarSCB.doLayout();
});
function irBuscar(){
	var numdoc     = Ext.getCmp('numdoc').getValue();
	var numcarord  = Ext.getCmp('numcarord').getValue();
	var fecmov = '';
	if(Ext.getCmp('fecmov').getValue()){
		fecmov = Ext.getCmp('fecmov').getValue().format('Y/m/d');
	}
	var codope  = Ext.getCmp('codope').getValue();
	if(Ext.getCmp('codope').getValue()=="")
				{
		Ext.Msg.alert('Mensaje','Debe seleccionar un tipo de operación!');
	}
	else
	{
		obtenerMensaje('procesar','','Buscando Datos');
		var JSONObject = {
				'operacion'   : 'buscar_por_contabilizar_movbco',
				'numdoc' : numdoc,
				'fecmov' : fecmov,
				'codope' : codope,
				'numcarord' : numcarord
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
							gridMovBanco.store.removeAll();
						}
						else{
							dsMovBanco.loadData(objetoMovbco);
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

}

function irCancelar(){
	limpiarFormulario(fromBusquedaSCB);
	gridMovBanco.store.removeAll();
}

function irProcesar(){
	valido=true;
	fecha='';
	if(Ext.getCmp('fecconta').getValue()!=''){
		fecha=Ext.getCmp('fecconta').getValue().format('Y/m/d');
	}
	grid = gridMovBanco.getSelectionModel().getSelections();
	cadenajson = "{'operacion':'contabilizar_movbco','codsis':'"+sistema+"','nomven':'"+vista+"','fecha':'"+fecha+"','arrDetalle':[";
	total = grid.length;
	if (total>0)
	{			
		if(total>50)
		{
			total=50;
			alert('Por su seguridad,el sistema Procesará 50 Registros continuos.');
		}
		obtenerMensaje('procesar','','Procesando Informacion');
		for (i=0; i<total; i++)
		{
			if (i==0) 
			{
				cadenajson += "{'codban':'"+grid[i].get('codban')+"','ctaban':'"+grid[i].get('ctaban')+"',";
				cadenajson += "'numdoc':'"+grid[i].get('numdoc')+"','codope':'"+grid[i].get('codope')+"',";
				cadenajson += "'estmov':'"+grid[i].get('estmov')+"'}";                
			}
			else
			{
				cadenajson += ",{'codban':'"+grid[i].get('codban')+"','ctaban':'"+grid[i].get('ctaban')+"',";
				cadenajson +="'numdoc':'"+grid[i].get('numdoc')+"','codope':'"+grid[i].get('codope')+"',";
				cadenajson +="'estmov':'"+grid[i].get('estmov')+"'}";                
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