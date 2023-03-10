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

var fromReporteInfMenEjecFin = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
    //Datos de la opcion de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos de los meses
	var mes = [ [ 'Enero', '01' ], 
	            [ 'Febrero', '02' ],
	            [ 'Marzo', '03' ],
	            [ 'Abril', '04' ],
	            [ 'Mayo', '05' ],
	            [ 'Junio', '06' ],
	            [ 'Julio', '07' ],
	            [ 'Agosto', '08' ],
	            [ 'Septiembre', '09' ],
	            [ 'Octubre', '10' ],
	            [ 'Noviembre', '11' ],
	            [ 'Diciembre', '12' ]];
	
	var stMes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mes
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromMeses = new Ext.form.FieldSet({
			title:'Mes',
			style: 'position:absolute;left:20px;top:10px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 60,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:115px;top:10px',
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
									emptyText:'---Seleccione una Opci?n---',
									listWidth:200,
									width:200
								}]
						}]
					}]
	})

	//--------------------------------------------------------------------------------------------
	
	var	fromTipoImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:20px;top:80px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 60,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:130px;top:10px',
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
	
	var	fromNivelReporte = new Ext.form.FieldSet({
			title:'Nivel de Reporte',
			style: 'position:absolute;left:20px;top:150px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 78,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:100px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [220,220],
									id:'nivelReport',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [
									        {boxLabel: 'Consolidado', name: 'nivel_reporte',inputValue: '1',checked:true},
									        {boxLabel: 'Detallado', name: 'nivel_reporte', inputValue: '0'}
									        ]
								}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(600/2));
	fromReporteInfMenEjecFin = new Ext.FormPanel({
		applyTo: 'formReporteInfMenEjecFin',
		width:600, 
		height: 300,
		title: "<H1 align='center'>Informaci?n Mensual de la Ejecuci?n Financiera</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromMeses,
		        fromTipoImpresion,
		        fromNivelReporte
		        ]
		});	
		fromReporteInfMenEjecFin.doLayout();
	});	

	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir()
	{
		var mes = Ext.getCmp('meses').getValue();
		var opcionimp = 'P';
		var tipo = 'C';
		var valido = true;
		
		if(mes==''){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Favor Seleccionar un mes... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}
		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp = Ext.getCmp('tipoimp').getValue();
		}
		if(Ext.getCmp('nivelReport').items.items[1].checked){
			tipo = 'D';
		}
		if(valido){
			var pagina = '';
			if(opcionimp=='P'){
				pagina="reportes/sigesp_spg_rpp_instructivo_06_inf_men_eje_fin.php?mes="+mes+"&tipo="+tipo;
			}
			else{
				pagina="reportes/sigesp_spg_rpp_instructivo_06_inf_men_eje_fin_excel.php?mes="+mes+"&tipo="+tipo;
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
		
	
	