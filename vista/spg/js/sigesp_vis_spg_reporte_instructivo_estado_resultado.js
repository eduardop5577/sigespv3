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

var fromReporteEstRes = null; //varibale para almacenar la instacia de objeto de formulario 
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
	var mes = [ [ 'Enero-Marzo', '0103' ],
	            [ 'Abril-Junio', '0406' ],
	            [ 'Julio-Septiembre', '0709' ],
	            [ 'Octubre-Diciembre', '10-12' ]];
	
	var stMes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mes
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromMeses = new Ext.form.FieldSet({
			title:'Trimestre',
			style: 'position:absolute;left:20px;top:80px',
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
			style: 'position:absolute;left:20px;top:150px',
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
			style: 'position:absolute;left:20px;top:10px',
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
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(600/2));
	fromReporteEstRes = new Ext.FormPanel({
		applyTo: 'formReporteEstRes',
		width:600, 
		height: 270,
		title: "<H1 align='center'>ESTADO DE RESULTADO</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromInstuctivo,
		        fromMeses,
		        fromTipoImpresion
		        ]
		});	
		fromReporteEstRes.doLayout();
	});	

	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir()
	{
		var mes = Ext.getCmp('meses').getValue();
		var opcionimp = 'P';
		var instructivo = Ext.getCmp('instructivo').getValue();
		var valido = true;
		
		if(mes==''){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Favor Seleccionar el Trimestre... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFOR
			});
		}
		
		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp = Ext.getCmp('tipoimp').getValue();
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
		
		if(valido){
			var pagina = '';
			if (instructivo == '07') {
				if(opcionimp=='P'){
					pagina="reportes/sigesp_spg_rpp_instructivo_estado_resultado.php?trimestre="+mes;
				}
				else{
					pagina="reportes/sigesp_spg_rpp_instructivo_estado_resultado_excel.php?trimestre="+mes;
				}
			}
			else {
				//TODO COLOCAR LOS ENLACES PARA LOS REPORTES INST 08
				if(opcionimp=='P'){
					pagina="reportes/sigesp_spg_rpp_instructivo_estado_resultado.php?trimestre="+mes;
				}
				else{
					pagina="reportes/sigesp_spg_rpp_instructivo_estado_resultado_excel.php?trimestre="+mes;
				}
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
		
	
	