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

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
	var mesdesde = [ 
				 [ 'Enero', '01' ], 
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
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmesdesde = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mesdesde
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmesdesde = new Ext.form.ComboBox({
		store : stmesdesde,
		fieldLabel : 'Desde',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'mesdes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	
	
	var meshasta = [ 
				 [ 'Enero', '01' ], 
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
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmeshasta = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : meshasta
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmeshasta = new Ext.form.ComboBox({
		store : stmeshasta,
		fieldLabel : 'Hasta',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'meshas',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	

	var bimensual = [ 
				 [ 'Enero-Febrero', '0102' ], 
	             [ 'Marzo-Abril', '0304' ],
				 [ 'Mayo-Junio', '0506' ],
				 [ 'Julio-Agosto', '0708' ],
				 [ 'Septiembre-Octubre', '0910' ],
				 [ 'Noviembre-Diciembre', '1112' ]
				 ];
	
	var stbimensual = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : bimensual
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbbimensual = new Ext.form.ComboBox({
		store : stbimensual,
		fieldLabel : 'Rango',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'bimensual',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	

	var trimestral = [ 
				 [ 'Enero-Marzo', '0103' ], 
	             [ 'Abril-Junio', '0406' ],
				 [ 'Julio-Septiembre', '0709' ],
				 [ 'Octubre-Diciembre', '1012' ]
				 ];
	
	var sttrimestral = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : trimestral
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtrimestral = new Ext.form.ComboBox({
		store : sttrimestral,
		fieldLabel : 'Rango',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'trimestral',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	

	var semestral = [ 
				 [ 'Enero-Junio', '0106' ],
				 [ 'Julio-Diciembre', '0712' ]
				 ];
	
	var stsemestral = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : semestral
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbsemestral = new Ext.form.ComboBox({
		store : stsemestral,
		fieldLabel : 'Rango',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'semestral',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
//--------------------------------------------------------------------------------------------------------------------------------	

	fieldsetPeriodos = new Ext.form.FieldSet({
		title:"Periodos",
		style: 'position:absolute;left:10px;top:15px',
		border:true,
		width: 715,
		cls :'fondo',
		height: 60,
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:-30px;top:10px',
				items: [{
						layout: "form",
						border: false,
						items:[{
								xtype: "radiogroup",
								fieldLabel: "",
								labelSeparator:"",
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',	
								width: 710,
								columns: [170,180,180,180],
								id:'hidbot',
								items: [
										{
										boxLabel: 'Mensual', name: 'intervalp', inputValue: '1',
										listeners:{		
													'check': function (checkbox, checked) 
													{
														if(checked)
														{
															Ext.getCmp('mesdes').reset();
															Ext.getCmp('mesdes').enable();
															Ext.getCmp('meshas').reset();
															Ext.getCmp('meshas').enable();
															Ext.getCmp('bimensual').reset();
															Ext.getCmp('bimensual').disable();
															Ext.getCmp('trimestral').reset();
															Ext.getCmp('trimestral').disable();
															Ext.getCmp('semestral').reset();
															Ext.getCmp('semestral').disable();
														}
													}
												}
										},
										{
										boxLabel: 'BiMensual', name: 'intervalp', inputValue: '2',
										listeners:{		
													'check': function (checkbox, checked) 
													{
														if(checked)
														{
															Ext.getCmp('mesdes').reset();
															Ext.getCmp('meshas').reset();
															Ext.getCmp('mesdes').disable();
															Ext.getCmp('meshas').disable();
															Ext.getCmp('bimensual').reset();
															Ext.getCmp('bimensual').enable();
															Ext.getCmp('trimestral').reset();
															Ext.getCmp('trimestral').disable();
															Ext.getCmp('semestral').reset();
															Ext.getCmp('semestral').disable();
														}
													}
												}
										},
										{
										boxLabel: 'Trimestral', name: 'intervalp',inputValue: '3',checked:true,
										listeners:{		
													'check': function (checkbox, checked) 
													{
														if(checked)
														{
															Ext.getCmp('mesdes').reset();
															Ext.getCmp('meshas').reset();
															Ext.getCmp('mesdes').disable();
															Ext.getCmp('meshas').disable();
															Ext.getCmp('bimensual').reset();
															Ext.getCmp('bimensual').disable();
															Ext.getCmp('trimestral').reset();
															Ext.getCmp('trimestral').enable();
															Ext.getCmp('semestral').reset();
															Ext.getCmp('semestral').disable();
														}
													}
											 }
										},
										{
										boxLabel: 'Semestral', name: 'intervalp', inputValue: '4',
										listeners:{		
													'check': function (checkbox, checked) 
													{
														if(checked)
														{
															Ext.getCmp('mesdes').reset();
															Ext.getCmp('meshas').reset();
															Ext.getCmp('mesdes').disable();
															Ext.getCmp('meshas').disable();
															Ext.getCmp('bimensual').reset();
															Ext.getCmp('bimensual').disable();
															Ext.getCmp('trimestral').reset();
															Ext.getCmp('trimestral').disable();
															Ext.getCmp('semestral').reset();
															Ext.getCmp('semestral').enable();
														}
													}
												}
										}]
								}]
						}]
				}]

});
//----------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO INTERVALO FECHAS
//**********************************************************************************************************************************
	fieldsetIntervaloFechasMeses = new Ext.form.FieldSet({
		title:"Intervalo Mensual",
		style: 'position:absolute;left:10px;top:90px',
		border:true,
		width: 715,
		cls :'fondo',
		height: 75,
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:20px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [cmbmesdesde]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:400px;top:20px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:50,
						items: [cmbmeshasta]
						}]
				}]

	});	

	fieldsetIntervaloFechasBimensual = new Ext.form.FieldSet({
			title:"Intervalo BiMensual",
			style: 'position:absolute;left:10px;top:180px',
			border:true,
			width: 715,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:150,
							items: [cmbbimensual]
							}]
					}]

	});	

	fieldsetIntervaloFechasTrimestres = new Ext.form.FieldSet({
			title:"Intervalo Trimestral",
			style: 'position:absolute;left:10px;top:270px',
			border:true,
			width: 715,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:150,
							items: [cmbtrimestral]
							}]
					}]

	});			

	fieldsetIntervaloFechasSemestral = new Ext.form.FieldSet({
			title:"Intervalo Semestral",
			style: 'position:absolute;left:10px;top:360px',
			border:true,
			width: 715,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:150,
							items: [cmbsemestral]
							}]
					}]

	});	
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                 				INICIO DEL FORMULARIO ORDENADO POR
//**********************************************************************************************************************************
//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 760,
	height: 500,
	title: "<H1 align='center'>Estado de Resultaod 0811</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [	
        	fieldsetPeriodos,
        	fieldsetIntervaloFechasMeses,
			fieldsetIntervaloFechasBimensual,
           	fieldsetIntervaloFechasTrimestres,
        	fieldsetIntervaloFechasSemestral]	
	});
	irCancelar();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irCancelar()
{
	Ext.getCmp('mesdes').disable();
	Ext.getCmp('meshas').disable();
	Ext.getCmp('bimensual').disable();
	Ext.getCmp('trimestral').enable();
	Ext.getCmp('semestral').disable();
}

//**********************************************************************************************************************************	
//                                  						BOTONES 
//**********************************************************************************************************************************
function irImprimir()
{
	var radio= Ext.getCmp('hidbot');
	for (var j = 0; j < radio.items.length; j++)
	{
		if (radio.items.items[j].checked)
		{
			hidbot = radio.items.items[j].inputValue;
			break;
		}
	} 
	valido       = true;
	if(valido)
	{
		if(hidbot==1)
		{
			cmbmesdes  = Ext.getCmp('mesdes').getValue();
			cmbmeshas  = Ext.getCmp('meshas').getValue();
			if((cmbmesdes=="")||(cmbmeshas==""))
			{
				Ext.Msg.hide();
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_estadoresultado0811.php?mesdes="+cmbmesdes
					   +"&meshas="+cmbmeshas+"&etiqueta=Mensual";
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		if(hidbot==2)
		{
			cmbbimensual  = Ext.getCmp('bimensual').getValue();
			if(cmbbimensual=="")
			{
				Ext.Msg.hide();
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_estadoresultado0811.php?mesdes="+cmbbimensual+"&etiqueta=Bimensual";
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		if(hidbot==3)
		{
			cmbtrimestral  = Ext.getCmp('trimestral').getValue();
			if(cmbtrimestral=="")
			{
				Ext.Msg.hide();
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_estadoresultado0811.php?mesdes="+cmbtrimestral+"&etiqueta=Trimestral";
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
		if(hidbot==4)
		{
			cmbsemestral  = Ext.getCmp('semestral').getValue();
			if(cmbsemestral=="")
			{
				Ext.Msg.hide();
				alert ("Debe seleccionar los Parametros de Busqueda");
			}
			else
			{
				pagina="reportes/sigesp_scg_rpp_estadoresultado0811.php?mesdes="+cmbsemestral+"&etiqueta=Semestral";
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			}
		}
	}
}
