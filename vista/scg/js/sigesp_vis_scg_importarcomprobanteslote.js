/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var formComprobanteContableLote = null; //varibale para almacenar la instacia de objeto de formulario 
var gridDetComprobantes = null;
var procesando = false;
barraherramienta    = true;
var dsDetComprobantes = null;
Ext.onReady(function()
{
	
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	Ext.QuickTips.init();
	Ext.Ajax.timeout=36000000000;
	//-------------------------------------------------------------------------------------------------------------------------	
	//creando datastore y columnmodel para la grid de los detalles contables
	var reDetComprobantes = Ext.data.Record.create([
		{name: 'comprobante'},
	    {name: 'descripcion'},
	    {name: 'fecha'},
	    {name: 'monto_debe'},
	    {name: 'monto_haber'},
	    {name: 'valido'}
	]);
	
	dsDetComprobantes =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetComprobantes)
	});
						
	var cmDetComprobantes = new Ext.grid.ColumnModel([
        {header: "<CENTER>Comprobante</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'comprobante',editor: new Ext.form.TextField({allowBlank: false})},
        {header: "<CENTER>Descripci&#243;n</CENTER>", width: 80, sortable: true, dataIndex: 'descripcion',editor: new Ext.form.TextField({allowBlank: false})},
        {header: "<CENTER>fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha', align: 'center'},
		{header: "<CENTER>Debe</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto_debe'},
		{header: "<CENTER>Haber</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto_haber'},
		{header: "<CENTER>Valido</CENTER>",  width: 30, sortable: true, dataIndex: 'valido', align: 'center',renderer:mostrarDisponibleComCmp},
		
	]);
	//fin del datastore y columnmodel para la grid de bienes
	
	//creando grid para los detalles de bienes
	gridDetComprobantes = new Ext.grid.EditorGridPanel({
 		width:900,
 		height:280,
		frame:true,
		title:"<H1 align='center'>Comprobantes Contables en Lote </H1>",
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		style: 'position:absolute;left:10px;top:200px',
		autoScroll:true,
 		border:true,
 		ds: dsDetComprobantes,
   		cm: cmDetComprobantes,
   		stripeRows: true,
  		viewConfig: {forceFit:true}
	});
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando formulario principal 
	var Xpos = ((screen.width/2)-(475));
	var Ypos = ((screen.height/2)-(330));
	formComprobanteContableLote = new Ext.form.FormPanel({
		title: "<H1 align='center'>Importar Comprobantes Contables en Lote</H1>",
		applyTo: 'formComprobanteContableLote',
		fileUpload: true,
		width: 950,
		height: 600,
		id: 'formdos',
		style: 'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		frame: true,
		autoScroll:true,
		items: [{
				xtype:"fieldset", 
			    title:'Datos del Comprobante',
			    style: 'position:absolute;left:10px;top:5px',
			    border:true,
			    width: 900,
			    cls :'fondo',
			    height: 190,
			    items: [{ 
							xtype: 'fileuploadfield',
							fieldLabel: 'Archivo Txt',
							id: 'archivo',
							labelSeparator:'',
							allowBlank:false,
							width: 330,
							emptyText: 'Seleccione el Archivo txt...',
							fileUpload: true,
							buttonCfg:
							{
								text: '...'
							}
                        },
				        {
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:10px;top:50px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 100,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Procedencia',
											id: 'procede',
											value: 'SCGCMP',
											readOnly: true,
											allowBlank:false,
											width:100,
											binding:true,
											defaultvalue:'',
											hiddenvalue:'',
										}]
									}]
					    },
					    {
				    	layout:"column",
				    	defaults: {border: false},
				    	style: 'position:absolute;left:10px;top:90px',
				    	border:false,
				    	items:[{
				    		  	layout:"form",
				    		  	border:false,
				    		  	labelWidth:100,
				    		  	items:[{
					    			  xtype: 'textarea',
					    			  labelSeparator :'',
					    			  fieldLabel: 'Descripci&#243;n',
					    			  id: 'descripcion',
					    			  width: 700,
					    			  row: 2,
					    			  binding:true,
					    			  hiddenvalue:'',
					    			  defaultvalue:'',
					    			  allowBlank:false,
					    			  autoCreate: {tag: 'textarea', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@()?-+*');"},
					    		  }]
				    	  	}]
					   }]
		},gridDetComprobantes]
		});	

	gridDetComprobantes.on({
		'rowcontextmenu':
		{
			fn: function(grid, numFila, evento)
			{
				var registro = grid.getStore().getAt(numFila);
				//creando datastore y columnmodel para la grid de los detalles contables
				var reDetalles = Ext.data.Record.create([
					{name: 'sc_cuenta'},
					{name: 'monto'},
					{name: 'debhab'}
					
				]);
				
				var dsDetalles =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetalles)
				});
									
				var cmDetalles = new Ext.grid.ColumnModel([
					{header: "<CENTER>Cuenta</CENTER>", width: 100, align: 'center', sortable: true, dataIndex: 'sc_cuenta'},
					{header: "<CENTER>Operaci&#243;n</CENTER>", type: 'float', width: 60, align: 'center', sortable: true, dataIndex: 'debhab'},
					{header: "<CENTER>Monto</CENTER>", type: 'float', width: 60, align: 'right', sortable: true, dataIndex: 'monto'}
				]);
				//fin del datastore y columnmodel para la grid de bienes
								
				//creando grid para los detalles de bienes
				var gridDetalles = new Ext.grid.EditorGridPanel({
					width:686,
					height:420,
					frame:true,
					title:"<H1 align='center'>Detalles</H1>",
					sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
					style: 'position:absolute;left: 10px;top:10px',
					autoScroll:true,
					border:true,
					ds: dsDetalles,
					cm: cmDetalles,
					stripeRows: true,
					viewConfig: {forceFit:true}
				});

				var myJSONObject = 
				{
						"operacion" :'detalles_archivo', 
						"fecha" : registro.get('fecha')
				};
				var ObjSon= JSON.stringify(myJSONObject);
				var parametros ='ObjSon='+ObjSon;
				Ext.Ajax.request({
					url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
					params: parametros,
					method: 'POST',
					success: function ( result, request ) 
					{ 
						var datos = result.responseText;
						var objetoSCG = eval('(' + datos + ')');
						if(objetoSCG!='')
						{
							if(objetoSCG!='0')
							{
								if(objetoSCG.raiz == null || objetoSCG.raiz =='')
								{
									Ext.MessageBox.show({
										title:'Advertencia',
										msg:'No existen datos para mostrar',
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.WARNING
									});
									gridDetalles.store.removeAll();
								}
								else
								{
									dsDetalles.loadData(objetoSCG);
									ventanaAgregarLote.show();									
								}
							}
						}
					}
				});

				var ventanaAgregarLote = new Ext.Window({
						title: "<H1 align='center'>Movimientos Contables</H1>",
						y:10,
						width:710,
						height:500, 
						modal: true,
						closable:false,
						plain: false,
						frame:true,
						items:[gridDetalles],
						buttons: [
								{
									text: 'Salir',
									handler:function(){
										ventanaAgregarLote.close();
									}
								}]
						});
			}
		}
	});

}); //fin creando formulario principal con parametros de busqueda y grid de modificaciones

	
	//-------------------------------------------------------------------------------------------------------------------------	

	function irProcesar()
	{
		if (!procesando)
		{
			procesando=true;
			var cadjson = '';
			var valido = true;
			cadjson = "{'operacion':'guardar_en_lote','codsis':'"+sistema+"','nomven':'"+vista+"','evento':'INSERT',";
			cadjson += "'detallesComprobantes':[";	
			var numDetalle = 0;
			gridDetComprobantes.store.each(function (reDetCon)
			{
				if(reDetCon.get('valido')=='false')
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'El Comprobante debe estar cuadrado debe y habaer !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					valido = false;
				}
				if (valido)
				{
					if(numDetalle==0)
					{
						cadjson +="{'comprobante':'"+reDetCon.get('comprobante')+"','descripcion':'"+reDetCon.get('descripcion')+"'," +
								   "'fecha':'"+reDetCon.get('fecha')+"'}";
					}
					else
					{
						cadjson +=",{'comprobante':'"+reDetCon.get('comprobante')+"','descripcion':'"+reDetCon.get('descripcion')+"'," +
								   "'fecha':'"+reDetCon.get('fecha')+"'}";
					}
				}
				numDetalle++;
			});
			cadjson += "]}";
			if(valido)
			{
				try
				{
					var objjson = Ext.util.JSON.decode(cadjson);
					if(typeof(objjson) == 'object')
					{
						obtenerMensaje('procesar','','Espere un Momento...');
						var parametros = 'ObjSon=' + cadjson;
						Ext.Ajax.request({
							url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
							params : parametros,
							method: 'POST',
							success: function ( result, request)
							{
								datos = result.responseText;
								Ext.Msg.hide();
								var datajson = eval('(' + datos + ')');
								if(datajson.raiz.valido==true)
								{	
									Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
									irCancelar();
								}
								else
								{
									Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
								}
								procesando=false;									
							}
						});
					}
				}	
				catch(e)
				{
					alert('Verifique los datos, esta insertando caracteres invalidos '+e);
					procesando=false;
				}
			}
		}
		else
		{
			Ext.MessageBox.alert('Error', 'Espere un momento el sistema esta procesando.');
		}		
	}

	function irBuscar()
	{
		gridDetComprobantes.store.removeAll();
		if(formComprobanteContableLote.getForm().isValid())
		{
			formComprobanteContableLote.getForm().submit({
				url: '../../controlador/scg/sigesp_ctr_scg_cargararchivo.php',
				waitMsg: 'Cargando el archivo...',
				success: function(formCargarArchivo, o)
				{
					CargarGrid();
				}
			});
		}
	}

	function irCancelar()
	{
		formComprobanteContableLote.getForm().reset();
		gridDetComprobantes.store.removeAll();
		procesando=false;
	}
   //-------------------------------------------------------------------------------------------------------------------------		 

	function CargarGrid()
	{
		var myJSONObject = 
		{
			"operacion" :'cargar_archivo_lote',
			"descripcion" :  Ext.getCmp('descripcion').getValue()
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		obtenerMensaje('procesar','','Espere un Momento...');
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
				Ext.Msg.hide();
	    		var datos = result.responseText;
				var objetoSCG = eval('(' + datos + ')');
				if(objetoSCG!='')
				{
					if(objetoSCG!='0')
					{
						if(objetoSCG.raiz == null || objetoSCG.raiz =='')
						{
							Ext.MessageBox.show({
								title:'Advertencia',
								msg:'No existen datos para mostrar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
							});
							gridDetComprobantes.store.removeAll();
						}
						else
						{
							dsDetComprobantes.loadData(objetoSCG);
						}
					}
				}
			}
		});
	}
