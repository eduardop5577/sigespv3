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

var fromReporteEjecFinPryAcc = null; //varibale para almacenar la instacia de objeto de formulario 
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
			style: 'position:absolute;left:10px;top:10px',
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
									emptyText:'---Seleccione una Opción---',
									listWidth:200,
									width:200,
									listeners:{ 
												'change': function () {
													Ext.getCmp('trimestre').setValue('');
												 }
												}
								}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------
	//-------------------------------------------------------------------------------------
	
	//Datos de los trimestres
	var Tmes = [ [ 'Enero-Marzo', '0103' ],
	            [ 'Abril-Junio', '0406' ],
	            [ 'Julio-Septiembre', '0709' ],
	            [ 'Octubre-Diciembre', '1012' ]];
	
	var stTmes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : Tmes
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromTmeses = new Ext.form.FieldSet({
			title:'Trimestre',
			style: 'position:absolute;left:10px;top:80px',
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
									id: 'trimestre',
									store : stTmes,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'---Seleccionar Trimestre---',
									listWidth:200,
									width:200,
									listeners:{ 
												'change': function () {
													Ext.getCmp('meses').setValue('');
												 }
												}
								}]
							}]
			}]
	})

	//--------------------------------------------------------------------------------------------
	
	
	var	fromTipoImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:10px;top:160px',
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
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(600/2));
	fromReporteEjecFinPryAcc = new Ext.FormPanel({
		applyTo: 'formReporteEjecFinPryAcc',
		width:600, 
		height: 300,
		title: "<H1 align='center'>Ejecución Financiera de los Proyectos/Acciones Centralizadas del Órgano por partidas de Egreso</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromMeses,
				fromTmeses,
		        fromTipoImpresion
		        ]
		});	
		fromReporteEjecFinPryAcc.doLayout();
	});	

	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir()
	{
		var mes = Ext.getCmp('meses').getValue();
		var Tmes = Ext.getCmp('trimestre').getValue();
		var opcionimp = 'P';
		var valido = true;
		
		if((mes=='')&&(Tmes=='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Favor Seleccionar un mes o trimestre... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}

		if((mes!='')&&(Tmes!='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Solo debe seleccionar una opcion, mes o trimestre... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}

		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp = Ext.getCmp('tipoimp').getValue();
		}
		if(valido){
			var pagina = '';
			if(opcionimp=='P'){
				pagina="reportes/sigesp_spg_rpp_instructivo_0602_inf_men_eje_fin.php?mes="+mes+"&Tmes="+Tmes;
			}
			else{
				pagina="reportes/sigesp_spg_rpp_instructivo_0602_inf_men_eje_fin_excel.php?mes="+mes+"&Tmes="+Tmes;
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}