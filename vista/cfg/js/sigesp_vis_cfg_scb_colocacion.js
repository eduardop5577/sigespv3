/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de colocacion   
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true; 
var formcolocacion      = null;  //instancia del formulario principal 
var comcampocatbanco    = null;  //instancia del componente campo catalogo bancos
var comcampocattipcol   = null;  //instancia del componente campo catalogo tipo colocacio
var comcampocatctacon   = null;  //instancia del componente campo catalogo cuentas contables
var comcampocatctacob   = null;  //instancia del componente campo catalogo cuentas contables
var comcampocatctaban   = null;  //instancia del componente campo catalogo cuentas bancarias
var gridreintegro       = null;  //instancia de grid de reintegros

Ext.onReady(function()
{
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//creando datastore y columnmodel para el catalogo de bancos
	var registro_banco = Ext.data.Record.create([
						{name: 'codban'},
						{name: 'nomban'}
				]);
	
	var dsbanco =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_banco)
	  			});
						
	var colmodelcatbanco = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codban'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'nomban'}
				]);
	//fin creando datastore y columnmodel para el catalogo de bancos
	
	//componente campocatalogo para el campo banco
	comcampocatbanco = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo de Bancos',
							anchoformbus: 450,
							altoformbus:100,
							anchogrid: 450,
							altogrid: 350,
							anchoven: 500,
							altoven: 450,
							anchofieldset:850,
							datosgridcat: dsbanco,
							colmodelocat: colmodelcatbanco,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scb_banco.php',
							parametros: "ObjSon={'oper': 'catalogo'}",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'codiban',valor:'codban'},
									   {etiqueta:'Descripci&#243;n',id:'desban',valor:'nomban'}],
							posicion:'position:absolute;left:5px;top:60px',
							tittxt:'Banco',
							idtxt:'codban',
							campovalue:'codban',
							anchoetiquetatext:130,
							anchotext:70,
							anchocoltext:0.25,
							idlabel:'nomban',
							labelvalue:'nomban',
							anchocoletiqueta:0.55,
							anchoetiqueta:150,
							tipbus:'L',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
				});
	//fin componente campocatalogo para el campo banco
	
	//creando datastore y columnmodel para el catalogo tipo colocaciones
	var registro_tipcolocacion = Ext.data.Record.create([
						{name: 'codtipcol'},
						{name: 'nomtipcol'}
				]);
	
	var dstipcolocacion =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_tipcolocacion)
	  			});
						
	var colmodelcattipcolocacion = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codtipcol'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'nomtipcol'}
				]);
	//fin creando datastore y columnmodel para el catalogo tipo colocaciones
	
	//componente campocatalogo para el campo tipo colocacion
	comcampocattipcol = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo Tipo Colocaciones',
							anchoformbus: 450,
							altoformbus:100,
							anchogrid: 450,
							altogrid: 350,
							anchoven: 500,
							altoven: 450,
							anchofieldset:850,
							datosgridcat: dstipcolocacion,
							colmodelocat: colmodelcattipcolocacion,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scb_tipocolocacion.php',
							parametros: "ObjSon={'oper': 'catalogo'}",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'codtipcolo',valor:'codtipcol'},
									   {etiqueta:'Descripci&#243;n',id:'destipcolo',valor:'nomtipcol'}],
							posicion:'position:absolute;left:5px;top:92px',
							tittxt:'Tipo Colocaci&#243n',
							idtxt:'codtipcol',
							campovalue:'codtipcol',
							anchoetiquetatext:130,
							anchotext:70,
							anchocoltext:0.25,
							idlabel:'nomtipcol',
							labelvalue:'nomtipcol',
							anchocoletiqueta:0.53,
							anchoetiqueta:150,
							tipbus:'L',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
							
				});
	//fin componente campocatalogo para el campo tipo colocacion
	
	//creando datastore y columnmodel para el catalogo cuentas contables
	var registro_ctacon = Ext.data.Record.create([
						{name: 'sc_cuenta'},
						{name: 'denominacion'}
				]);
	
	var dsctacon =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_ctacon)
	  			});
						
	var colmodelcatctacon = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
				]);
	//fin creando datastore y columnmodel para el catalogo cuentas contables
	
	//componente campocatalogo para el campo cuenta contable
	comcampocatctacon = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo Cuentas Contables',
							anchoformbus: 450,
							altoformbus:130,
							anchogrid: 450,
							altogrid: 350,
							anchoven: 500,
							altoven: 450,
							anchofieldset:850,
							datosgridcat: dsctacon,
							colmodelocat: colmodelcatctacon,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_catcuentas.php',
							parametros: "ObjSon={'operacion': 'catalogo'",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'codcue',valor:'sc_cuenta'},
									   {etiqueta:'Descripci&#243;n',id:'dencue',valor:'denominacion'}],
							posicion:'position:absolute;left:5px;top:250px',
							tittxt:'Cuenta Contable',
							idtxt:'sc_cuenta',
							campovalue:'sc_cuenta',
							anchoetiquetatext:130,
							anchotext:130,
							anchocoltext:0.33,
							idlabel:'scgctadeno',
							labelvalue:'denominacion',
							anchocoletiqueta:0.53,
							anchoetiqueta:150,
							tipbus:'P',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
				});
	//fin componente campocatalogo para el campo cuenta contable
	
	//creando datastore y columnmodel para el catalogo cuentas por cobrar
	var registro_ctacob = Ext.data.Record.create([
						{name: 'sc_cuenta'},
						{name: 'denominacion'}
				]);
	
	var dsctacob =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_ctacob)
	  			});
						
	var colmodelcatctacob = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
				]);
	//fin creando datastore y columnmodel para el catalogo cuentas por cobrar
	
	//componente campocatalogo para el campo cuenta por cobrar
	comcampocatctacob = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo Cuentas Contables',
							anchoformbus: 450,
							altoformbus:130,
							anchogrid: 450,
							altogrid: 350,
							anchoven: 500,
							altoven: 450,
							anchofieldset:850,
							datosgridcat: dsctacob,
							colmodelocat: colmodelcatctacob,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_catcuentas.php',
							parametros: "ObjSon={'operacion': 'catalogoctacob'",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'codcuecob',valor:'sc_cuenta'},
									   {etiqueta:'Descripci&#243;n',id:'dencuecob',valor:'denominacion'}],
							posicion:'position:absolute;left:5px;top:280px',
							tittxt:'Cuenta por Cobrar',
							idtxt:'sc_cuentacob',
							campovalue:'sc_cuenta',
							anchoetiquetatext:130,
							anchotext:130,
							anchocoltext:0.33,
							idlabel:'denocob',
							labelvalue:'denominacion',
							anchocoletiqueta:0.53,
							anchoetiqueta:150,
							tipbus:'P',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:true
				});
	//fin componente campocatalogo para el campo cuenta por cobrar
	
	//creando datastore y columnmodel para el catalogo cuentas bancarias
	var registro_ctaban = Ext.data.Record.create([
						{name: 'ctaban'},
						{name: 'dencta'},
						{name: 'nomban'},
						{name: 'nomtipcta'},
						{name: 'sc_cuenta'}
				]);
	
	var dsctaban =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_ctaban)
	  			});
						
	var colmodelcatctaban = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'ctaban'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'dencta'},
						{header: "Banco", width: 40, sortable: true, dataIndex: 'nomban'},
						{header: "Tipo Cuenta", width: 40, sortable: true, dataIndex: 'nomtipcta'},
						{header: "Cuenta Contable", width: 40, sortable: true, dataIndex: 'sc_cuenta'}
				]);
	//fin creando datastore y columnmodel para el catalogo cuentas bancarias
	
	//componente campocatalogo para el campo cuenta cedente
	comcampocatctaban = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo de Cuentas',
							anchoformbus: 450,
							altoformbus:130,
							anchogrid: 450,
							altogrid: 350,
							anchoven: 500,
							altoven: 450,
							anchofieldset:850,
							datosgridcat: dsctaban,
							colmodelocat: colmodelcatctaban,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scb_cuentabanco.php',
							parametros: "ObjSon={'oper': 'catalogocol'",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'coctaban',valor:'ctaban'},
									   {etiqueta:'Descripci&#243;n',id:'dectaban',valor:'dencta'}],
							posicion:'position:absolute;left:5px;top:345px',
							tittxt:'Cuenta Cedente',
							idtxt:'ctaban',
							campovalue:'ctaban',
							anchoetiquetatext:130,
							anchotext:130,
							anchocoltext:0.33,
							idlabel:'dencta',
							labelvalue:'dencta',
							anchocoletiqueta:0.53,
							anchoetiqueta:150,
							tipbus:'LF',
							arrtxtfiltro:['codban'],
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
				});
	//fin componente campocatalogo para el campo cuenta cedente
	
	//funcion para el formato de fecha en grid
	function formatoFechaGrid(fecha){
		if (fecha != '') {
			var fechanoguion = fecha.replace('-', '/', 'g');
			var objfecha = new Date(fechanoguion);
			return objfecha.format(Date.patterns.fechacorta);
		}
		
	}
	//fin funcion para el formato de fecha en grid
	
	//funcion para el formato numericos en grid
	function formatoNumericoGrid(numero){
		if (numero != '') {
			return formatoNumericoMostrar(numero,2,'.',',','','','-','');
		}
		else{
			return 0;
		}
		
	}
	//fin funcion para el formato numericos en grid
	
	//creando datastore y columnmodel para la grid de reintegros
	var registro_reintegro = Ext.data.Record.create([
						{name: 'fecreint'},
						{name: 'montoreint'}
				]);
	
	var dsreintegro =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_reintegro)
	  			});
						
	var colmodelreintegro = new Ext.grid.ColumnModel([
          				{header: "Fecha", width: 20, sortable: true, dataIndex: 'fecreint'},
          				{header: "Monto", width: 40, sortable: true, dataIndex: 'montoreint'}
				]);
	//creando datastore y columnmodel para la grid de reintegros
	
	//creando grid para los reintegros
	gridreintegro = new Ext.grid.GridPanel({
	 		width:400,
	 		height:150,
			frame:true,
			title:'Reintegro de Intereses',
			style: 'position:absolute;left:200px;top:420px',
	 		autoScroll:true,
     		border:true,
     		ds: dsreintegro,
       		cm: colmodelreintegro,
       		stripeRows: true,
      		viewConfig: {forceFit:true}
		});
	//fin creando grid para los reintegros
	
	
	//creando funcion que construye formulario principal
	function getFromColocacion(){
		Ext.QuickTips.init();
		var Xpos = ((screen.width/2)-(920/2));
		formcolocacion = new Ext.FormPanel({
			applyTo: 'formulario_colocacion',
			width: 920,
			height: 630,
			title: 'Definici&#243;n Colocaciones',
			frame: true,
			autoScroll:true,
			style:'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;',
			items: [{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:5px',
						items: [{
									layout: "form",
									border: false,
									labelWidth: 130,
									columnWidth: 0.5,
									items: [{
												xtype: 'textfield',
												fieldLabel: 'N&#250;mero',
												labelSeparator :'',
												id: 'numcol',
												autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
												width: 130,
												listeners: {
                                							'blur': function(){
																		var valorcampo = this.getValue();
																		valorcampo = ue_rellenarcampo(valorcampo, 15);
																		this.setValue(valorcampo);
																		var cadjson = "{'operacion':'validarcodigo','codban':'" + Ext.getCmp('codban').getValue() + "','ctaban':'" + Ext.getCmp('ctaban').getValue() + "','numcol':'" + valorcampo + "'}"
																		var parametros = 'ObjSon=' + cadjson;
																		Ext.Ajax.request({
																			url: '../../controlador/cfg/sigesp_ctr_cfg_scb_colocacion.php',
																			params: parametros,
																			method: 'POST',
																			success: function(resultad, request){
																				datos = resultad.responseText;
																				resultado = datos.split("|");
																				if (resultado[1] == '0') {
																					Ext.MessageBox.alert('Advertencia', 'El c&#243;digo seleccionado ya existe si continua la operaci&#243;n modificara la informaci&#243;n del existente');
																				}
																			},
																			failure: function(result, request){
																				Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
																			}
																		});
																		
																	}
                       							},
												binding:true,
												hiddenvalue:'',
												defaultvalue:'',
												allowBlank:false
									}]
							}]
					},{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:35px',
						items: [{
									layout: "form",
									border: false,
									labelWidth: 130,
									columnWidth: 0.5,
									items: [{
												xtype: 'textfield',
												fieldLabel: 'Denominaci&#243;n',
												labelSeparator :'',
												name: 'denominacion',
												id: 'dencol',
												autoCreate: {tag: 'input', type: 'text', maxlength: 200, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
												width: 400,
												binding:true,
												hiddenvalue:'',
												defaultvalue:'',
												allowBlank:false
											}]
								}]
					},
					comcampocatbanco.fieldsetCatalogo,
					comcampocattipcol.fieldsetCatalogo
					,{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:135px',
						items: [{
									layout: "form",
									border: false,
									labelWidth: 130,
									columnWidth: 0.5,
									items: [{
				               					xtype:"datefield",
				                				fieldLabel:"Fecha Desde",
												labelSeparator :'',
				                				width:100,
												id:"feccol",
												endDateField: 'fecvencol',
												readOnly: true,
												vtype: 'daterange',
												listeners: {
                                					'blur': function(){
                                    							var feccol = Ext.getCmp('feccol').getValue();
																if(Ext.getCmp('fecvencol').getValue()!=""){
																	var fecvencol = Ext.getCmp('fecvencol').getValue();
																	var dias   = numeroDias (feccol,fecvencol);
																	Ext.getCmp('diacol').setValue(dias);
																}
																
                                					}
                            					},
												binding:true,
												hiddenvalue:'',
												defaultvalue:'',
												allowBlank:false
											}]
								},{
									layout: "form",
									border: false,
									labelWidth: 70,
									columnWidth: 0.5,
									items: [{
				               					xtype:"datefield",
				                				fieldLabel:"Fecha Hasta",
				                				labelSeparator :'',
				                				width:100,
												id:"fecvencol",
												startDateField: 'feccol',
												readOnly: true,
												vtype: 'daterange',
												listeners: {
                                							'blur': function(){
                                    									var fecvencol = Ext.getCmp('fecvencol').getValue();
																		if( Ext.getCmp('feccol').getValue()!=""){
																			var feccol = Ext.getCmp('feccol').getValue();
																			var dias   = numeroDias (feccol,fecvencol);
																			Ext.getCmp('diacol').setValue(dias);
																			}
															}
                            					},
												binding:true,
												hiddenvalue:'',
												defaultvalue:'',
												allowBlank:false
					           		}]
						}]
								
			},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:170px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 130,
					columnWidth: 0.5,
					items: [{
						xtype: 'numberfield',
						fieldLabel: 'Plazo',
						labelSeparator :'',
						id: 'diacol',
						readOnly: true,
						width: 50,
						binding:true,
						hiddenvalue:'',
						defaultvalue:'',
						allowBlank:false
					}]
				}]
			},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:200px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 130,
					columnWidth: 0.5,
					items: [{
						xtype: 'textfield',
						fieldLabel: 'Tasa',
						labelSeparator :'',
						id: 'tascol',
						width: 50,
						listeners: {
                                	'blur': function(){
                                    			var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
												this.setValue(formatonumero);
									}
                        },
						binding:true,
						hiddenvalue:'',
						defaultvalue:'',
						allowBlank:false
					}]
				}]
			},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:230px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							items: [{
										xtype: 'textfield',
										fieldLabel: 'Monto',
										labelSeparator :'',
										id: 'monto',
										style:'margin-right:15px',
										width: 100,
										listeners: {
                                					'blur': function(){
                                    					var formatonumero = formatoNumericoMostrar(this.getValue(),2,'.',',','','','-','');
														this.setValue(formatonumero);
													},
													'change':function(){
														Ext.Msg.show({
															title:'Advertencia',
															msg: 'Debe recalcular los reintegros',
															buttons: Ext.Msg.OK,
															icon: Ext.MessageBox.WARNING
														});
														gridreintegro.store.removeAll();
													}
                            			},
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										allowBlank:false
									}]
						},{
							layout: "form",
							border: false,
							labelWidth: 70,
							columnWidth: 0.5,
							items: [{
										xtype: 'textfield',
										fieldLabel: 'Intereses',
										labelSeparator :'',
										id: 'monint',
										readOnly: true,
										width: 100,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										allowBlank:false
									}]
						}]
								
			},
			comcampocatctacon.fieldsetCatalogo,
			comcampocatctacob.fieldsetCatalogo
			,{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:320px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							items: [{
										xtype: 'textfield',
										fieldLabel: 'Cuenta de Ingreso',
										labelSeparator :'',
										id: 'spi_cuenta',
										style:'margin-right:17px',
										width: 130,
										readOnly:true,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										allowBlank:false
									}]
						},{
							layout: "form",
							border: false,
							labelWidth: 70,
							columnWidth: 0.2,
							items: [{
										xtype:'button',
										id:'botcatspicuenta',
										iconCls: 'menubuscar',
										handler: function(){
											catalogocuentaspi(false,'spi_cuenta','spictadeno');
										}
									}]
						},{
							layout: "form",
							border: false,
							labelWidth: 70,
							columnWidth: 0.3,
							items: [{
										xtype: 'textfield',
  										labelSeparator :'',
  										style:'border:none;background:#f1f1f1;',
  										id: 'spictadeno',
  										disabled:true,  
  										width: 250,
										binding:false
									}]
						}]
								
			},{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:420px',
				items: [{
					layout: "form",
					border: false,
					labelWidth: 130,
					columnWidth: 0.5,
					items: [{
					xtype:'button',
					id:'botgenreintegro',
					text: 'Generar Reintegros',
					handler:function()
					{
                            	var feccol   = Ext.util.Format.date(Ext.getCmp('feccol').getValue(),'d/m/Y');
								var fecvencol   = Ext.util.Format.date(Ext.getCmp('fecvencol').getValue(),'d/m/Y');
								var nummeses = numeroMeses (feccol,fecvencol);
								var numdias  = parseInt(Ext.getCmp('diacol').getValue());
								var monto    = parseFloat(ue_formato_operaciones(Ext.getCmp('monto').getValue()));
								var tasa     = parseFloat(ue_formato_operaciones(Ext.getCmp('tascol').getValue()));;
								var monint   = (monto*tasa*numdias)/(360*100);
								Ext.getCmp('monint').setValue(formatoNumericoMostrar(monint,2,'.',',','','','-',''));
								var ndias      = 0;
								var fecharei   = '';
								var fechaaux   = '';
								var auxfecdes  = '';
								var arrfecdes  = '';
								var fdmes      = 0;
								var montoint   = redondearNumero(parseFloat(monint),2);
								var monrei     = 0;
								auxfecdes = feccol;
								arrfecdes = auxfecdes.split("/");
								fdmes     = parseFloat(arrfecdes[1]);
								fechaaux  = new Date(fdmes+'/'+arrfecdes[0]+'/'+arrfecdes[2]);
								var cadenajson = "{'raiz':[";
								for (var i = 0; i <= nummeses -1; i++)
								{
									if(i == 0)
									{
										auxfecdes = feccol;
										arrfecdes = auxfecdes.split("/");
										fdmes     = parseFloat(arrfecdes[1]);
										fechaaux  = new Date(fdmes+'/'+arrfecdes[0]+'/'+arrfecdes[2]);
										fecharei  = fechaaux.getLastDateOfMonth();
										ndias     = numeroDias (fechaaux,fecharei,'I');
									}
									else
									{
										fecharei  = fechaaux.getLastDateOfMonth();
										ndias     = numeroDias (fechaaux,fecharei,'I');
									}
									monrei     = (montoint * ndias)/numdias;
									fechaaux   = fechaSiguiente(fecharei.format(Date.patterns.fechacorta));
									cadenajson = cadenajson + "{'fecreint':'"+fecharei.format(Date.patterns.fechacorta)+"','montoreint':'"+formatoNumericoMostrar(monrei,2,'.',',','','','-','')+"'},";
								}
								ndias = numeroDias (fechaaux,Ext.getCmp('fecvencol').getValue(),'I');
								monrei = (montoint * ndias)/numdias;
								cadenajson = cadenajson + "{'fecreint':'"+Ext.getCmp('fecvencol').getValue().format(Date.patterns.fechacorta)+"','montoreint':'"+formatoNumericoMostrar(monrei,2,'.',',','','','-','')+"'}]}";
								var objetodata = Ext.util.JSON.decode(cadenajson);
								dsreintegro.loadData(objetodata);
							}
					}]
				}]
			},
			comcampocatctaban.fieldsetCatalogo,
			gridreintegro
			,{
				xtype: 'hidden',
				id: 'estreicol',
				value:'1',
				binding:true,
				defaultvalue:''
			},{
				xtype: 'hidden',
				id: 'codestpro1',
				binding:true,
				defaultvalue:'-------------------------'
			},{
				xtype: 'hidden',
				id: 'codestpro2',
				binding:true,
				defaultvalue:'-------------------------'
			},{
				xtype: 'hidden',
				id: 'codestpro3',
				binding:true,
				defaultvalue:'-------------------------'
			},{
				xtype: 'hidden',
				id: 'codestpro4',
				binding:true,
				defaultvalue:'-------------------------'
			},{
				xtype: 'hidden',
				id: 'codestpro5',
				binding:true,
				defaultvalue:'-------------------------'
			},{
				xtype: 'hidden',
				id: 'estcla',
				binding:true,
				defaultvalue:'-'
			}]
		});
	}
	//fin creando funcion que construye formulario principal
	
	//Llamado de funciones
	getFromColocacion();
});

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarFormulario(formcolocacion);
	gridreintegro.store.removeAll();
}

function irGuardar()
{
	
	var arrtablas = [{nomtabla:'esp_scb_dt_colocacion',
					comstore:gridreintegro.getStore(),
					numcampo:2,
					arrclave:['codban','ctaban','numcol']}];
	
	var arrcampostablas = [{nomcampo:'fecreint',
							tipocampo:'fecha',
							formato:true},
							{nomcampo:'montoreint',
							tipocampo:'numerico',
							formato:true}];
	
	var cadjson = getItems(formcolocacion,'incluir','A',arrtablas,arrcampostablas);
	try {
		var objjson = Ext.util.JSON.decode(cadjson);
		if (typeof(objjson) == 'object') {
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({
				url: '../../controlador/cfg/sigesp_ctr_cfg_scb_colocacion.php',
				params: parametros,
				method: 'POST',
				success: function(resultad, request){
					var datos = resultad.responseText;
					var resultado = datos.split("|");
					if (resultado[2] == "1") {
						switch (resultado[1]) {
							case "0":
								Ext.Msg.show({
									title:'Error',
									msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
								break;
							case "1":
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'El registro fue actualizado',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								break;
							case "2":
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'El registro fue incluido',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								break;
						}
					}
					else {
						Ext.Msg.show({
							title:'Error',
							msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
					}
					limpiarFormulario(formcolocacion);
					gridreintegro.store.removeAll();
				},
				failure: function(result, request){
					Ext.Msg.show({
						title:'Error',
						msg: 'Ha ocurrido un error de conexion, por favor comuniquese con el administrador del sistema',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
				}
			});
		}
	}
	catch(e){
		//no imprimo excepcion
	}
}

function irEliminar()
{
	Ext.Msg.show({
		   title:'Confirmar',
		   msg: 'øDesea eliminar este registro?',
		   buttons: Ext.Msg.YESNO,
		   fn: function(btn){
			   if (btn == 'yes') {
				   var arrtablas = [{nomtabla:'esp_scb_dt_colocacion',
						comstore:gridreintegro.getStore(),
						numcampo:2,
						arrclave:['codban','ctaban','numcol']}];
		
				   var arrcampostablas = [{nomcampo:'fecreint',
								tipocampo:'fecha',
								formato:true},
								{nomcampo:'montoreint',
								tipocampo:'numerico',
								formato:true}];
		
					var cadjson = getItems(formcolocacion,'eliminar','A',arrtablas,arrcampostablas);
					try {
						var objjson = Ext.util.JSON.decode(cadjson);
						if (typeof(objjson) == 'object') {
							var parametros = 'ObjSon=' + cadjson;
							Ext.Ajax.request({
								url: '../../controlador/cfg/sigesp_ctr_cfg_scb_colocacion.php',
								params: parametros,
								method: 'POST',
								success: function(resultad, request){
									var datos = resultad.responseText;
									if (datos == "1") {
										Ext.Msg.show({
											title:'Mensaje',
											msg: 'El registro fue eliminado',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
										});
										
									}
									else if(datos == "-1"){
										Ext.Msg.show({
											title:'Error',
											msg: 'El registro no puede ser eliminado, esta vinculado con otros registros',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.ERROR
										});
									}
									else{
										Ext.Msg.show({
											title:'Error',
											msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.ERROR
										});
									}
									limpiarFormulario(formcolocacion);
									gridreintegro.store.removeAll();
								},
								failure: function(result, request){
									Ext.Msg.show({
										title:'Error',
										msg: 'Ha ocurrido un error de conexion, por favor comuniquese con el administrador del sistema',
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.ERROR
									});
								}
							});
						}
					}
					catch(e){
						Ext.Msg.show({
							title:'Error',
							msg: 'Los datos proporcionados contienen caracteres no validos',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
					}
			    }
		   },
		   icon: Ext.MessageBox.QUESTION
	});
}

function irBuscar()
{
	catalogoColocacion();
}