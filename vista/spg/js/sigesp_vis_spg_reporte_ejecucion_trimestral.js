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

var fromReporteEjeTrimestral = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var gridOrdCom = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	fieldSetEstOrigenDesde = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsestdesde'
	});
	
	fieldSetEstOrigenHasta = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsesthasta'
	});
	
    //-------------------------------------------------------------------------------------
	
	//Datos de la opcion de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ],
	               [ 'GRAFICOS', 'G' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------	
	
	//Datos de los meses
	var mes = [ [ 'Enero-Marzo', '0103' ], 
	            [ 'Abril-Junio', '0406' ],
	            [ 'Julio-Septiembre', '0709' ],
	            [ 'Octubre-Diciembre', '1012' ]];
	
	var stMes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mes
	});

	//--------------------------------------------------------------------------------------------
	
	var	fromEstructura = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 200+obtenerPosicion(),
			items: [{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenDesde.fieldSetEstPre]
						}]
					},
					{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:470px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenHasta.fieldSetEstPre]
						}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	var	fromMeses = new Ext.form.FieldSet({
			title:'Trimestre',
			style: 'position:absolute;left:10px;top:330px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 60,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 40,
							items: [{
									xtype: 'combo',
									fieldLabel: '',
									labelSeparator :'',
									id: 'meses',
									store : stMes,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Enero-Marzo',
									listWidth:200,
									width:200
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromTipoImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:10px;top:400px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 60,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'combo',
									fieldLabel: '',
									labelSeparator :'',
									id: 'tipoimp',
									store : stOpcimp,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'PDF',
									listWidth:150,
									width:150
								}]
							}]
				}]
	})

	//-------------------------------------------------------------------------------------
	
	//Datos de instructivos
	var instructivo = [ [ 'INSTRUCTIVO 07', '07' ],
	                    [ 'INSTRUCTIVO 08', '08' ]];
	
	var stInstructivo = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : instructivo
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromInstuctivo = new Ext.form.FieldSet({
			title:'Instructivo',
			style: 'position:absolute;left:10px;top:260px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 60,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:125px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 40,
							items: [{
									xtype: 'combo',
									fieldLabel: '',
									labelSeparator :'',
									id: 'instructivo',
									store : stInstructivo,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'---Seleccionar Instructivo---',
									listWidth:200,
									width:200
								}]
							}]
					}]
	})

	//--------------------------------------------------------------------------------------------	
	
	
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteEjeTrimestral = new Ext.FormPanel({
		applyTo: 'formReporteEjeTrimestral',
		width:950, //700
		height: 600,
		title: "<H1 align='center'>Ejecuci&#243;n Trimestral de Gastos y Aplicaciones Financieras (Resumen Institucional)</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  
		items: [fromEstructura,
		        fromInstuctivo,
		        fromMeses,
		        fromTipoImpresion
		        ]
		});	
		fromReporteEjeTrimestral.doLayout();
	});	

	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir()
	{
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
		var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
		var opcionimp = 'P';
		var mes = '0103';
		var valido = true;
		var instructivo = Ext.getCmp('instructivo').getValue();
		
		if(Ext.getCmp('tipoimp').getValue()!='')
                {
			opcionimp=Ext.getCmp('tipoimp').getValue();
		}
		if(Ext.getCmp('meses').getValue()!='')
                {
			mes=Ext.getCmp('meses').getValue();
		}
		
		if(arrCodigosDesde[0] != '0000000000000000000000000')
                {
			if (!fieldSetEstOrigenDesde.validarEstructura())
                        {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar la estructura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			if (!fieldSetEstOrigenHasta.validarEstructura())
                        {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if(arrCodigosHasta[0] != '0000000000000000000000000')
                {
			if (!fieldSetEstOrigenHasta.validarEstructura())
                        {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar la estructura  !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			if (!fieldSetEstOrigenDesde.validarEstructura())
                        {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if(instructivo=='')
                {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Favor Seleccionar el Instructivo... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}
		if(valido)
                {
			var pagina = '';
			var datosReporte = '';
			for ( var i = 0; i < arrCodigosDesde.length; i++)
                        {
				if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--")
                                {
					arrCodigosDesde[i]="";
				}
				if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--")
                                {
					arrCodigosHasta[i]="";
				}		
			}
			var datosReporte = "?cmbmes="+mes+"&codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			                  +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
			                  +"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			                  +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]+"&txtcodfuefindes="+""
			                  +"&txtcodfuefinhas="+""+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
			
			if (instructivo == '07')
                        {
				if(opcionimp=='P')
                                {
					imprimir('EJECUCION TRIMESTRAL INST 07','sigesp_spg_rpp_ejecucion_trimestral_inst_07.php',datosReporte);
				}
				else if(opcionimp=='G')
                                {
					pagina = "reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_barra.php"+datosReporte;
				}
				else if(opcionimp=='E')
                                {
					imprimir('EJECUCION TRIMESTRAL INST 07 EXCEL','sigesp_spg_rpp_ejecucion_trimestral_inst_07_excel.php',datosReporte);
				}
			}
			else {//TODO LLAMAR ARCHIVO DEL INSTRUCTIVO 08
				if(opcionimp=='P')
                                {
					pagina = "reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07.php"+datosReporte;
				}
				else if(opcionimp=='G')
                                {
					pagina = "reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_barra.php"+datosReporte;
				}
				else if(opcionimp=='E')
                                {
					pagina = "reportes/sigesp_spg_rpp_ejecucion_trimestral_inst_07_excel.php"+datosReporte;
				}
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=600,height=400,resizable=yes,location=no");
		}
	}
	
	function obtenerPosicion()
        {
		if(empresa['numniv']=='3')
                {
			return 0;
		}
		else
                {
			return 80;
		}
	}
	
	function imprimir(variable,valor,datosReporte)
	{
		var myJSONObject =
		{
			'operacion'   : 'buscarFormato',
			'sistema'	  : 'SPG',
			'seccion'     : 'REPORTE',
			'variable'    : variable,
			'valor'		  : valor,
			'tipo'		  : 'C'
		};	
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function (result, request)
			{ 
				formato = result.responseText;	
				pagina="reportes/"+formato+datosReporte;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			},
			failure: function (result, request){ 
				Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
			}
		})
	}