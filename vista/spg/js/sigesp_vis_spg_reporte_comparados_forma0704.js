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

var fromReportePreGasxPar = null; //variable para almacenar la instacia de objeto de formulario
barraherramienta = true;
var	fromIntervaloComprobante = null;
var	fromIntervaloProcede = null;
var	fromIntervaloCuentas = null;
var	fromIntervaloFechas = null;
var	fromOrden = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var fecha = new Date()
	
	//Datos del nivel de cuentas
	var nivelcuentas = [ [ '1', '1' ], 
	                     [ '2', '2' ],
			             [ '3', '3' ],
			             [ '4', '4' ],
			             [ '5', '5' ],
			             [ '6', '6' ],
			             [ '7', '7' ]];
	
	var stNivelcuentas = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : nivelcuentas
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del combo mensual uno
	var mensualuno = [ [ 'Enero', '01' ], 
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
	
	var stCmbMenUno = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mensualuno
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del combo mensual dos
	var mensualdos = [ [ 'Enero', '01' ], 
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
	
	var stCmbMenDos = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mensualdos
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del combo bi-mensual
	var bimensual = [ [ 'Enero-Febrero', '0102' ], 
	                  [ 'Febrero-Marzo', '0203' ],
			          [ 'Marzo-Abril', '0304' ],
			          [ 'Abril-Mayo', '0405' ],
			          [ 'Mayo-Junio', '0506' ],
			          [ 'Junio-Julio', '0607' ],
			          [ 'Julio-Agosto', '0708' ],
			          [ 'Agosto-Septiembre', '0809' ],
			          [ 'Septiembre-Octubre', '0910' ],
			          [ 'Octubre-Noviembre', '1011' ],
			          [ 'Noviembre-Diciembre', '1112' ]];
	
	var stCmbBiMensual = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : bimensual
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del combo trimestral
	var trimestral = [ [ 'Enero-Marzo', '0103' ], 
	                   [ 'Abril-Junio', '0406' ],
			           [ 'Julio-Septiembre', '0709' ],
			           [ 'Octubre-Diciembre', '1012' ]];
	
	var stCmbTrimestral = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : trimestral
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del combo semestral
	var semestral = [ [ 'Enero-Junio', '0106' ], 
	                  [ 'Febrero-Julio', '0207' ],
			          [ 'Marzo-Agosto', '0308' ],
			          [ 'Abril-Septiembre', '0409' ],
			          [ 'Mayo-Octubre', '0510' ],
			          [ 'Junio-Noviembre', '0611' ],
			          [ 'Julio-Diciembre', '0712' ],];
	
	var stCmbSemestral = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : semestral
	});
	
	//-------------------------------------------------------------------------------------


	fromOrden = new Ext.form.FieldSet({
			title:'Organización de las Fechas',
			style: 'position:absolute;left:10px;top:80px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:70px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 20,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [100,100,100,100],
									id:'rdOrden',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [
									        {
									        	boxLabel: 'Mensual',
									        	name: 'orden',
									        	inputValue: '0',
									        	checked:true,
									        	listeners:{	
													'check': function (checkbox, checked){
														if(checked){
															fromSemestral.hide();
															fromBiMensual.hide();
															fromTrimestral.hide();
															limpiarFormulario(fromMensual);
															fromMensual.show();
														}
													}
									        	}
									        },
									        {
									        	boxLabel: 'Bi-Mensual',
									        	name: 'orden',
									        	inputValue: '1',
									        	listeners:{	
													'check': function (checkbox, checked){
														if(checked){
															fromSemestral.hide();
															fromMensual.hide();
															fromTrimestral.hide();
															limpiarFormulario(fromBiMensual);
															fromBiMensual.show();
														}
									        		}
									        	}
									        },
									        {
												boxLabel: 'Trimestral', name: 'orden', inputValue: '2',
												listeners:{	
													'check': function (checkbox, checked){
														if(checked){
															fromSemestral.hide();
															fromMensual.hide();
															fromBiMensual.hide();
															limpiarFormulario(fromTrimestral);
															fromTrimestral.show();
														}
													}
									        	}
									        },
									        {
									        boxLabel: 'Semestral',
									        	name: 'orden',
									        	inputValue: '3',
									        	listeners:{	
													'check': function (checkbox, checked){
														if(checked){
															fromTrimestral.hide();
															fromMensual.hide();
															fromBiMensual.hide();
															limpiarFormulario(fromSemestral);
															fromSemestral.show();
														}
													}
										        }
									        }]
								}]
						}]
			}]
	})
	
	//--------------------------------------------------------------------------------------------

	fromFormato = new Ext.form.FieldSet({ 
			title:'Nivel de Cuentas',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false}, 
					style: 'position:absolute;left:170px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Nivel',
									labelSeparator :'',
									id: 'nivelCtas',
									store : stNivelcuentas,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
			//						typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'----Seleccione----',
									listWidth:150,
									width:150,					
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	fromMensual = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:150px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:50px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Mensual',
									labelSeparator :'',
									id: 'mensualuno',
									store : stCmbMenUno,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
					//				typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Enero',
									listWidth:120,
									width:120,
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:225px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 0,
							items: [{
									xtype: 'combo',
									fieldLabel: '',
									labelSeparator :'',
									id: 'mensualdos',
									store : stCmbMenDos,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
					//				typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Enero',
									listWidth:120,
									width:120,
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	fromBiMensual = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:150px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:140px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 80,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Bi-Mensual',
									labelSeparator :'',
									id: 'bimensual',
									store : stCmbBiMensual,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
					//				typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Enero-Febrero',
									listWidth:150,
									width:150,
								}]
							}]
					}]
	
	}) //stCmbTrimestral
	
	//--------------------------------------------------------------------------------------------

	fromTrimestral = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:150px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:140px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 80,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Trimestral',
									labelSeparator :'',
									id: 'trimestral',
									store : stCmbTrimestral,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
					//				typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Enero-Marzo',
									listWidth:150,
									width:150,
								}]
							}]
					}]
	}) //stCmbSemestral
	
	//--------------------------------------------------------------------------------------------

	fromSemestral = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:150px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:140px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 80,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Semestral',
									labelSeparator :'',
									id: 'semestral',
									store : stCmbSemestral,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
					//				typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'Enero-Junio',
									listWidth:150,
									width:150,
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(600/2));
	fromReportePreGasxPar = new Ext.FormPanel({
		applyTo: 'formReportePreGasxPar',
		width:600, //700
		height: 280,
		title: "<H1 align='center'>Resumen Del Presupuesto de Gastos por Partidas</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;', 
		items: [fromFormato,
		        fromOrden,
		        fromMensual,
		        fromBiMensual,
		        fromTrimestral,
		        fromSemestral
		        ]
	});
	fromReportePreGasxPar.doLayout();
	fromBiMensual.hide();
	fromTrimestral.hide();
	fromSemestral.hide();
});	

//--------------------------------------------------------------------------------------------

function irImprimir()
{
	var valido=true;
	combo='';
	cmbnivel='s1';
	combomes='';
	etiqueta='';
	if(Ext.getCmp('rdOrden').items.items[0].checked){
		etiqueta="Mensual";
		combo='01';
		combomes='01';
		if(Ext.getCmp('mensualuno').getValue()!='' && Ext.getCmp('mensualdos').getValue()!=''){
			combo=Ext.getCmp('mensualuno').getValue();
			combomes=Ext.getCmp('mensualdos').getValue();
			if(combo>combomes){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Intervalo de meses incorrecto...!!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if(Ext.getCmp('mensualuno').getValue()!='' && Ext.getCmp('mensualdos').getValue()==''){
			combo=Ext.getCmp('mensualuno').getValue();
			if(combo>combomes){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Intervalo de meses incorrecto...!!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if(Ext.getCmp('mensualuno').getValue()=='' && Ext.getCmp('mensualdos').getValue()!=''){
			combomes=Ext.getCmp('mensualdos').getValue();
			if(combo>combomes){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Intervalo de meses incorrecto...!!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if(Ext.getCmp('nivelCtas').getValue()!=''){
			cmbnivel=Ext.getCmp('nivelCtas').getValue();
		}
		pagina="reportes/sigesp_spg_rpp_comparados_forma0704.php?combo="+combo+"&combomes="+combomes
		      +"&txtetiqueta="+etiqueta+"&cmbnivel="+cmbnivel+"&tipoformato="+"0";
	}
	else{
		if(Ext.getCmp('nivelCtas').getValue()!=''){
			cmbnivel=Ext.getCmp('nivelCtas').getValue();
		}
		if(Ext.getCmp('rdOrden').items.items[1].checked){
			etiqueta="Bi-Mensual";
			combo='0102';
			if(Ext.getCmp('bimensual').getValue()!=''){
				combo=Ext.getCmp('bimensual').getValue();
			}
		}
		if(Ext.getCmp('rdOrden').items.items[2].checked){
			etiqueta="Trimestral";
			combo='0103';
			if(Ext.getCmp('trimestral').getValue()!=''){
				combo=Ext.getCmp('trimestral').getValue();
			}
		}
		if(Ext.getCmp('rdOrden').items.items[3].checked){
			etiqueta="Semestral";
			combo='0106';
			if(Ext.getCmp('semestral').getValue()!=''){
				combo=Ext.getCmp('semestral').getValue();
			}
		}
		pagina="reportes/sigesp_spg_rpp_comparados_forma0704.php?cmbnivel="+cmbnivel
        +"&combo="+combo+"&txtetiqueta="+etiqueta+"&tipoformato="+"0";
	}
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
}