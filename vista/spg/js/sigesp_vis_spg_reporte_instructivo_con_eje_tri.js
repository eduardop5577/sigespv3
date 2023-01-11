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

var fromReporteConEjeTrimestral = null; //varibale para almacenar la instacia de objeto de formulario
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
barraherramienta = true;

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
	
    //Datos de la opcion de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos de los meses
	var mes = [ [ 'Enero-Marzo', '0103' ],
	            [ 'Abril-Junio', '0406' ],
	            [ 'Julio-Septiembre', '0709' ],
	            [ 'Octubre-Diciembre', '10-12' ]];
	
	var stMes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mes
	});
	
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
			style: 'position:absolute;left:20px;top:260px',
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
	
	var	fromMeses = new Ext.form.FieldSet({
			title:'Trimestre',
			style: 'position:absolute;left:20px;top:330px',
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
									id: 'meses',
									store : stMes,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'---Seleccionar Trimestre---',
									listWidth:200,
									width:200
								}]
							}]
					}]
	})

	//--------------------------------------------------------------------------------------------
	
	var	fromTipoImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:20px;top:400px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 60,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:140px;top:10px',
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
	
	//--------------------------------------------------------------------------------------------
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(475)); //375
	
	fromReporteConEjeTrimestral = new Ext.FormPanel({
		applyTo: 'formReporteConEjeTrimestral',
		width:950, 
		height: 500,
		title: "<H1 align='center'>Consolidado de Ejecuci&#243;n Trimestral</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:70px;left:'+Xpos+'px',   
		items: [fromEstructura,
		        fromInstuctivo,
		        fromMeses,
		        fromTipoImpresion
		        ]
		});	
		fromReporteConEjeTrimestral.doLayout();
	});

	function obtenerPosicion(){
		if(empresa['numniv']=='3'){
			return 0;
		}
		else{
			return 80;
		}
	}
	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir()
	{
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
		var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
		var mes = Ext.getCmp('meses').getValue();
		var instructivo = Ext.getCmp('instructivo').getValue();
		var opcionimp = 'P';
		var valido = true;
		
		if(arrCodigosDesde[0] != '0000000000000000000000000') {
			if (!fieldSetEstOrigenDesde.validarEstructura()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar la estructura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			
			if (!fieldSetEstOrigenHasta.validarEstructura()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		
		if(arrCodigosHasta[0] != '0000000000000000000000000') {
			if (!fieldSetEstOrigenHasta.validarEstructura()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar la estructura  !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			
			if (!fieldSetEstOrigenDesde.validarEstructura()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		
		if(mes==''){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Favor Seleccionar el Trimestre... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}
		
		if(instructivo==''){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Favor Seleccionar el Instructivo... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}
		
		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp = Ext.getCmp('tipoimp').getValue();
		}
		if(valido){
			var pagina = '';
			if (instructivo == '07') {
				if(opcionimp=='P'){
					pagina="reportes/sigesp_spg_rpp_instructivo_consolidado_ejecucion_trimestral.php?trimestre="+mes+"&codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
	                +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
	                +"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
	                +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]+"&estclades="+arrCodigosDesde[5]
					+"&estclahas="+arrCodigosHasta[5];
				}
				else {
					pagina="reportes/sigesp_spg_rpp_instructivo_consolidado_ejecucion_trimestral_excel.php?trimestre="+mes+"&codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
	                +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
	                +"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
	                +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]+"&estclades="+arrCodigosDesde[5]
					+"&estclahas="+arrCodigosHasta[5];
				}
			} 
			else {//TODO COLOCAR LOS ENLACES PARA LOS REPORTES INST 08
				if(opcionimp=='P'){
					pagina="reportes/sigesp_spg_rpp_instructivo_consolidado_ejecucion_trimestral08.php?trimestre="+mes+"&codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
	                +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
	                +"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
	                +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]+"&estclades="+arrCodigosDesde[5]
					+"&estclahas="+arrCodigosHasta[5];
				}
				else {
					pagina="reportes/sigesp_spg_rpp_instructivo_consolidado_ejecucion_trimestral_excel08.php?trimestre="+mes+"&codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
	                +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
	                +"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
	                +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]+"&estclades="+arrCodigosDesde[5]
					+"&estclahas="+arrCodigosHasta[5];
				}
			}
			
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}