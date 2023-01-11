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

var fromReportePreCom = null;
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var fecha = new Date();

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
	
	//------------------------------------------------------------------------------------------------------------
	var	fromIntervaloFechas = new Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:280px',
			border:true,
			width: 570,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:10px',
					border:false,
					items:[{
							layout:"form",
							border:false,
							labelWidth:50,
							items:[{
									xtype:"datefield",
									labelSeparator :'',
									fieldLabel:"Desde",
									name:'Desde',
									id:'dtFechaDesde',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									allowBlank:false,
									value: '01/01/'+fecha.getFullYear(),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
						}]
				},
				{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:370px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Hasta",
								name:'Hasta',
								id:'dtFechaHasta',
								allowBlank:true,
								width:100,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value:  new Date().format('d-m-Y'),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				}]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(475));
	fromReportePreCom = new Ext.FormPanel({
		applyTo: 'formReportePreCom',
		width:950, //700
		height: 450,
		title: "<H1 align='center'>PRE-COMPROMISOS</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:70px;left:'+Xpos+'px', 
		items: [fromEstructura,fromIntervaloFechas]
	});	
	fromReportePreCom.doLayout();
});

function obtenerPosicion(){
	if(empresa['numniv']=='3'){
		return 0;
	}
	else{
		return 80;
	}
}

//------------------------------------------------------------------------------------------------------------

function irImprimir()
{
	var valido = true;
	var validoEst = true;
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
    var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
		
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
	
	if(fecdes>fechas){
		
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	
	if(valido){
		var pagina = "reportes/sigesp_spg_rpp_precompromisos_nocomprometidos.php?txtfecdes="+fecdes+"&txtfechas="+fechas
					+"&codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
        			+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
        			+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
        			+"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]+"&estclades="+arrCodigosDesde[5]
					+"&estclahas="+arrCodigosHasta[5];
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}