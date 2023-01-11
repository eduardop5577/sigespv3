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
	var reSolicitud = Ext.data.Record.create([
	                    {name: 'numcmp'},                      
                  	    {name: 'cmpmov'}, 
                  	    {name: 'feccmp'},                  	    
                  	    {name: 'conmov'}
                  	]);
                  	
	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});
                  						
	var cmSolicitud = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
		  {header: "<CENTER>N° Documento</CENTER>", width: 30, sortable: true, align: 'center',dataIndex: 'numcmp'},
		  {header: "<CENTER>Fecha Movimiento</CENTER>", width: 20, sortable: true, align: 'center',dataIndex: 'feccmp'},
		  {header: "<CENTER>Concepto</CENTER>", width: 50, sortable: true, dataIndex: 'conmov', align: 'center'}
	]);
                  	
	//creando datastore y columnmodel para la grid de depreciaciones
	gridSolicitud = new Ext.grid.EditorGridPanel({
			width:870,
	 		height:250,
			frame:true,
			title:"<H1 align='center'>Desincorporaciones por Contabilizar</H1>",
			style: 'position:absolute;left:15px;top:165px',
			autoScroll:true,
			border:true,
			ds: dsSolicitud,
			cm: cmSolicitud,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
			stripeRows: true,
			viewConfig: {forceFit:true}
	});
//-----------------------------------------------------------------------------------------------------------------------	
	// Creando la ventana emergente de los detalles
	
	gridSolicitud.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = gridSolicitud.getStore().getAt(numFila);
				
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
				var tit='Detalle Presupuestario de Gasto';
				
				//creando componente detalle comprobante
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: 'Contabilizaci&#243;n de Activos Fijos',
					anchoVentana: 720,
					altoVentana: 500,
					anchoFormulario: 680,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Comprobante',
								id:'ndoc',
								valor: registro.get('cmpmov'),
								ancho: 120 
								},
								{
								tipo:'textfield',
								etiqueta:'Documento',
								id:'cmov',
								valor:registro.get('numcmp'),
								ancho: 120
								},
								{
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fec_mov',
								valor:registro.get('feccmp'),
								ancho: 100
								},
							    {
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'b_mov',
								valor:registro.get('conmov'),
								ancho: 300
								}],
						tienePresupuesto:false,
						tituloGridPresupuestario:tit,
						anchoGridPG :680,
						altoGridPG :150,
						dsPresupuestoGasto: dsMovBancario,
						cmPresupuestoGasto: cmMovBancario,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsaf.php',
						paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto_ing',
																	'anio':registro.get('anio'),
																	'mes':registro.get('mes')}),
						tieneContable: true,
						anchoGridCO :680,
						altoGridCO :150,
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable_des',
																	   'comp':registro.get('cmpmov'),
																	   'fecha':registro.get('feccmp')}),
																   
				});
				//fin creando componente detalle comprobante
				
				comMovBancario.mostrarVentana();
			}
		}
	});

//-------------------------------------------------------------------------------------------------------------------------	
	//creando funcion que construye formulario principal para la anulación de banco
	var	fromDesincorp_Saf = new Ext.form.FieldSet({ 
			title:'Datos del Movimiento',
			style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 870,
			cls: 'fondo',
			height: 135,
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
									id: 'numcmp',
									autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
									width: 130,
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
									id:"feccmp",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
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
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha Contabilización",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									binding:true,
									value: new Date().format('Y-m-d'),
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									value:fecha_hoy,
									id:"fechaconta",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
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
	var	p1ContDesincorp = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 920,
		height: 460,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		title: "<H1 align='center'>Contabilización de Desincorporaciones de Activos</H1>",
		frame: true,
		autoScroll:true,
		items: [fromDesincorp_Saf,gridSolicitud]
	});
	
	p1ContDesincorp.doLayout();
});



function irBuscar(numdoc,fecmov,codope,numcarord)
{
	var numcmp     = Ext.getCmp('numcmp').getValue();
	var feccmp	   = Ext.getCmp('feccmp').getValue();
	var estatus    = '0';

		obtenerMensaje('procesar','','Buscando Datos');
		var JSONObject = {
				'operacion'   : 'buscar_por_contabilizar_dessaf',
				'numcmp' : numcmp,
				'feccmp' : feccmp,
				'estatus' : estatus
			}
		var ObjSon = JSON.stringify(JSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsaf.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request){
				Ext.Msg.hide();
				var datos = resultado.responseText;
				var objetoSaf = eval('(' + datos + ')');
				if(objetoSaf!=''){
					if(objetoSaf!='0'){
						if(objetoSaf.raiz == null || objetoSaf.raiz ==''){
							Ext.MessageBox.show({
								title:'Advertencia',
								msg:'No existen datos para mostrar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
			 				});
						}
						else{
							gridSolicitud.store.loadData(objetoSaf);
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

function irCancelar(){
	limpiarFormulario(fromDesincorp_Saf);
	gridSolicitud.store.removeAll();
}

function irAnular()
{
}


function irProcesar(){
	valido=true;
	var feccont     = new Date(Ext.getCmp('fechaconta').getValue());      
	grid = gridSolicitud.getSelectionModel().getSelections();fechaconta
	cadenajson = "{'operacion':'contabilizar_safdes','codsis':'"+sistema+"','nomven':'"+vista+"','fechaconta':'"+feccont.format(Date.patterns.bdfecha)+"','arrDetalle':[";
	total = grid.length;
	if (total>0)
	{			
		for (i=0; i<total; i++)
		{
			if (i==0) 
			{
				cadenajson += "{'cmpmov':'"+grid[i].get('cmpmov')+"','cmpmov':'"+grid[i].get('cmpmov')+"'," +
				"'feccmp':'"+grid[i].get('feccmp')+"','conmov':'"+grid[i].get('conmov')+"'}";                
			}
			else {
				cadenajson += ",{'cmpmov':'"+grid[i].get('cmpmov')+"','cmpmov':'"+grid[i].get('cmpmov')+"'," +
				"'feccmp':'"+grid[i].get('feccmp')+"','conmov':'"+grid[i].get('conmov')+"'}";               
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsaf.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) {
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Contabilizacion de Desincorporaci&#243;n de Activos',
					anchoLabel: 200,
					labelTotal:'Total desincorporaciones procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total desincorporaciones contabilizadas',
					valorProcesada:arrResultado[1],
					labelError:'Total desincorporaciones con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				
				comResultado.mostrarVentana();
				
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
irCancelar();
}