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

var fromReporteDisponibilidad = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//-------------------------------------------------------------------------------------

	//Creando los componentes para el catalogo de la estructura
	fieldSetEstOrigenDesde = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Desde',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsestdesde',
		nofiltroest:'1'
	});

	fieldSetEstOrigenHasta = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Hasta',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsesthasta',
		nofiltroest:'1'
	});
	
	//-------------------------------------------------------------------------------------

	var dataFecha = new Ext.form.DateField({
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
	});
	
	//-------------------------------------------------------------------------------------

	var checkFecha = new Ext.form.Checkbox({
		labelSeparator :'',
		fieldLabel: 'Reportar hasta la Fecha',
		id: 'hastafecha',
		inputValue:1,
		allowBlank:true,
		checked:true,
	});
		
	checkFecha.on('check', function(obj){
		if(obj.checked){
			Ext.getCmp('dtFechaHasta').enable();
		}
		else{
			Ext.getCmp('dtFechaHasta').disable();
		}
	})

	//-------------------------------------------------------------------------------------

	//Datos del tipo de formato a imprimir
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];

	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});

	//--------------------------------------------------------------------------------------------

	fieldset = new Ext.form.FieldSet({
		width: 930,
		height: 275+obtenerPosicion(),
		title: '',
		style: 'position:absolute;left:5px;top:5px',
		cls :'fondo',
		items: [{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:5px;top:10px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 50,
				items: [fieldSetEstOrigenDesde.fsEstructura]
			}]
		},{	
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:470px;top:10px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 50,
				items: [fieldSetEstOrigenHasta.fsEstructura]
			}]
		}]
	})

	//--------------------------------------------------------------------------------------------

	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 90,
		title: 'Intervalo de Fechas',
		style: 'position:absolute;left:190px;top:'+(350+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:60px;top:40px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[dataFecha]
					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:60px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 140,
						items: [checkFecha]
					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:270px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 180,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								fieldLabel: 'Quitar Cuentas sin Movimientos',
								id: 'cuesinmov',
								inputValue:1,
								allowBlank:true
							}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------

	fieldsetcuatro = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Fecha de Corte',
		style: 'position:absolute;left:190px;top:'+(350+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:60px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype: "datefield",
								labelSeparator :'',
								fieldLabel:"Hasta",
								name:'Hasta',
								id:'fechacorte',
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
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:270px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								fieldLabel: 'Forma Continua',
								id: 'forcon',
								inputValue:1,
								allowBlank:true
							}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Tipo Formato',
		style: 'position:absolute;left:190px;top:'+(280+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:30px;top:10px',
				items: [{
						layout: "form",
						border: false,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200],
								id:'rdFormato',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [{
							        	boxLabel: 'Formato #1',
							        	name: 'formato',
							        	inputValue: '0',
							        	checked:true, 
							        	listeners:{	
								        	'check': function (checkbox, checked){
									        	if(checked){
									        		fieldsetdos.show();
									        		fieldsetcuatro.hide();
									        	}
							        		}
								        }
								        },
								        {
								        	boxLabel: 'Formato #2',
								        	name: 'formato',
								        	inputValue: '1',
								        	listeners:{	
								        		'check': function (checkbox, checked){
								        			if(checked){
										        		fieldsetdos.hide();
										        		fieldsetcuatro.show();
										        	}
								        		}
								        	}
								        }]					
								}]
						}]
			}]
	})

	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteDisponibilidad = new Ext.FormPanel({
		applyTo: 'formReporteDisponibilidad',
		width:965, 
		height: 500,
		title: "<H1 align='center'>Diponibilidad Presupuestaria</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fieldset,fieldsetdos,fieldsettres,fieldsetcuatro]
	});	
	fieldsetcuatro.hide();
	fromReporteDisponibilidad.doLayout();
});	

//--------------------------------------------------------------------------------------------

function obtenerPosicion(){
	if(empresa['numniv']=='3'){
		return 0;
	}
	else{
		return 80;
	}
}

function irImprimir()
{
	var valido = true;
	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
	if(arrCodigosDesde[6] > arrCodigosHasta[6]){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por fuente de financiamiento no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(arrCodigosDesde[7] > arrCodigosHasta[7]){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por cuenta no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	
	if(arrCodigosDesde[0]!='0000000000000000000000000') {
		if(!fieldSetEstOrigenDesde.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar toda la estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(!fieldSetEstOrigenHasta.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if(arrCodigosHasta[0]!='0000000000000000000000000') {
		if(!fieldSetEstOrigenHasta.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar toda la estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(!fieldSetEstOrigenDesde.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if((arrCodigosDesde[7]=="" && arrCodigosHasta[7]!="") || (arrCodigosDesde[7]!="" && arrCodigosHasta[7]=="")){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		for( var i=0; i<arrCodigosDesde.length; i++) {
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
				arrCodigosDesde[i]="";
			}
			
			if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
				arrCodigosHasta[i]="";
			}		
		}
		if(Ext.getCmp('rdFormato').items.items[0].checked)
		{
			var sinmov=0;
			var hasfec=0;
			var fecha='00-00-0000';
			if(Ext.getCmp('cuesinmov').checked){
				sinmov=1;
			}
			if(Ext.getCmp('hastafecha').checked){
				hasfec=1;
				fecha=Ext.getCmp('dtFechaHasta').getValue().format('d-m-Y');
			}
			if(empresa['estmodest']==1)
			{
				var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				                 +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
								 +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
				                 +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
				                 +"&txtfechas="+fecha+"&ckbhasfec="+hasfec+"&ckbctasinmov="+sinmov
								 +"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
								 +"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
			}
			if(empresa['estmodest']==2)
			{
				var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				                 +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
				                 +"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]
				                 +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
				                 +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
				                 +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
				                 +"&txtfechas="+fecha+"&ckbhasfec="+hasfec+"&ckbctasinmov="+sinmov
				                 +"&txtcodfuefindes="+arrCodigosDesde[6]
				                 +"&txtcodfuefinhas="+arrCodigosHasta[6]+"&estclades="+arrCodigosDesde[5]
				                 +"&estclahas="+arrCodigosHasta[5];
			}
			imprimir('DISPONIBILIDAD PRESUPUESTARIA PDF','sigesp_spg_rpp_disponibilidad_presup_pdf.php',datosReporte);
		}
		else
		{
			var fecha=Ext.getCmp('fechacorte').getValue().format('d-m-Y');
			if(empresa['estmodest']==1)
			{
				var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				                  +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
								  +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
				                  +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
				                  +"&txtfecdes="+"2011-01-01"+"&txtfechas="+fecha
				                  +"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
								  +"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
			}
			else
			{
				var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
                                  +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
				                  +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			                      +"&codestpro4="+arrCodigosDesde[3]+"&codestpro4h="+arrCodigosHasta[3]
                                  +"&codestpro5="+arrCodigosDesde[4]+"&codestpro5h="+arrCodigosHasta[4]
                                  +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
                                  +"&txtfecdes="+"2011-01-01"+"&txtfechas="+fecha
                                  +"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
                                  +"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
			}
			if(Ext.getCmp('forcon').checked)
			{
				imprimir('DISPONIBILIDAD FORMATO 2 CONTINUO','sigesp_spg_rpp_disponibilidad_formato2_continuo.php',datosReporte);
			}
			else
			{
				imprimir('DISPONIBILIDAD FORMATO 2','sigesp_spg_rpp_disponibilidad_formato2.php',datosReporte);
			}
		}
	}
}

function imprimir(variable,nombreArchivo,datosReporte)
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SPG',
		'seccion'     : 'REPORTE',
		'variable'    : variable,
		'valor'		  : nombreArchivo,
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

