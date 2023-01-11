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

var fromReporteAcumxCuentas = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

    //-------------------------------------------------------------------------------------
	fieldSetEstOrigenDesde = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Desde',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		CuentaMovimiento:0,
		idtxt:'comfsestdesde',
		nofiltroest:empresa['filindspg'],
		filtrosindistintos:empresa['filindspg']
	});
	
	fieldSetEstOrigenHasta = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Hasta',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		CuentaMovimiento:0,
		idtxt:'comfsesthasta',
		nofiltroest:empresa['filindspg'],
		filtrosindistintos:empresa['filindspg']
	});
	
    //-------------------------------------------------------------------------------------
	
	//Datos para la opcion de Impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ],
	               [ 'GR�FICOS', 'G' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	
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
	
	//Datos de los meses
	var meses = [[ 'Enero', '01' ], 
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
	
	var stMeses = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : meses
	});

	//--------------------------------------------------------------------------------------------
	
	//formulario de la estructura
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
				},
				{	
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
	
	//formulario del nivel de cuentas
	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Nivel de Cuentas',
		style: 'position:absolute;left:190px;top:'+(280+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:55px;top:10px',
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
								triggerAction : 'all',
								mode : 'local',
								emptyText:'----Seleccione----',
								listWidth:150,
								width:150,
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:340px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								fieldLabel: 'Sub-Niveles',
								id: 'subniveles',
								inputValue:1,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'0',
								allowBlank:true
							}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------	
	
	//formulario del nivel del reporte
	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Nivel de Reporte',
		style: 'position:absolute;left:190px;top:'+(340+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:30px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 70,
						items: [{
								xtype: "radiogroup",
								labelSeparator:"",	
								columns: [200,200],
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

	//formulario de los meses
	fieldsetcuatro = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Acumulado Hasta',
		style: 'position:absolute;left:190px;top:'+(400+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [{
								xtype: 'combo',
								fieldLabel: 'Hasta',
								labelSeparator :'',
								id: 'meses',
								store : stMeses,
								editable : false,
								displayField : 'col',
								valueField : 'tipo',
								triggerAction : 'all',
								mode : 'local',
								emptyText:'Enero',
								listWidth:150,
								width:150,
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------	
	
	fieldsetseis = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Gr&#225;ficos',
		style: 'position:absolute;left:190px;top:'+(520+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:15px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200],
								id:'graficoReporte',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [
								        {boxLabel: 'Torta', name: 'tipo_grafico',inputValue: '1',checked:true},
								        {boxLabel: 'Barras', name: 'tipo_grafico', inputValue: '0'}
								        ]
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------
	
	fieldsetcinco = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Tipo de Impresion',
		style: 'position:absolute;left:190px;top:'+(480+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
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
								width:150,
								listeners: {
									'select': function(){	
										if(this.getValue()=='G'){
											fieldsetseis.show();
											//DESBLOQUEAR
										}
										else{
											fieldsetseis.hide();//BLOQUEAR
										}
									}
								}
							}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteAcumxCuentas = new Ext.FormPanel({
		applyTo: 'formReporteAcumxCuentas',
		width:965, //700
		height: 500,
		title: "<H1 align='center'>Acumulado por Cuentas</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fieldset,fieldsetdos,fieldsettres,fieldsetcuatro,fieldsetcinco,fieldsetseis]
		});	
	fieldsetseis.hide();     
		fromReporteAcumxCuentas.doLayout();
	});	

	//-------------------------------------------------------------------------------------------------------------------------	
	function irImprimir()
	{
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
		var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
		var opcionimp = 'P';
		var valido = true;
		if(Ext.getCmp('tipoimp').getValue()!='')
		{
			opcionimp=Ext.getCmp('tipoimp').getValue();
		}
		if(arrCodigosDesde[6] > arrCodigosHasta[6])
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por fuente de financiamiento no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if(arrCodigosDesde[7] > arrCodigosHasta[7])
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por cuenta no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if((arrCodigosDesde[0]!='0000000000000000000000000')&&(empresa['filindspg']==0))
		{
			if(!fieldSetEstOrigenDesde.validarEstructuraCompleta())
			{
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe seleccionar toda la estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			
			if(!fieldSetEstOrigenHasta.validarEstructuraCompleta())
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
		if((arrCodigosHasta[0]!='0000000000000000000000000')&&(empresa['filindspg']==0))
		{
			if(!fieldSetEstOrigenHasta.validarEstructuraCompleta())
			{
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe seleccionar toda la estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			
			if(!fieldSetEstOrigenDesde.validarEstructuraCompleta())
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
		
		if((arrCodigosDesde[6]=="--" && arrCodigosHasta[6]!="--") || (arrCodigosDesde[6]!="--" && arrCodigosHasta[6]=="--"))
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Fuente de Financiamiento !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[7]=="" && arrCodigosHasta[7]!="") || (arrCodigosDesde[7]!="" && arrCodigosHasta[7]==""))
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if(valido)
		{
			if(arrCodigosDesde[6]=="--" && arrCodigosHasta[6]=="--")
			{
				arrCodigosDesde[6]="";
				arrCodigosHasta[6]="";
			}
			var nivel = Ext.getCmp('nivelCtas').getValue();
			if(nivel=="")
			{
				nivel='s1';
			}
			var subniveles = '0';
			if(Ext.getCmp('subniveles').checked)
			{ //Estatus Emitida
				subniveles = '1';
			}
			var mesdes = '01';  
			var meshas = '01';
			if(Ext.getCmp('meses').getValue()!='')
			{
				meshas = Ext.getCmp('meses').getValue();
			}
			if(opcionimp=='P')
			{
				if (Ext.getCmp('nivelReport').items.items[0].checked)
				{
					imprimir('ACUMULADO_POR_CUENTA','sigesp_spg_rpp_acum_x_cuenta_pdf.php',subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,'');
				}
				else if(Ext.getCmp('nivelReport').items.items[1].checked)
				{
					imprimir('ACUMULADO_POR_CUENTA_DET','sigesp_spg_rpp_acum_x_cuenta_pdf_detallado.php',subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,'');
				}
			}
			if(opcionimp=='E')
			{
				if (Ext.getCmp('nivelReport').items.items[0].checked)
				{
					imprimir('ACUMULADO_POR_CUENTA_EXCEL','sigesp_spg_rpp_acum_x_cuenta_excel.php',subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,'');
				}
				else if(Ext.getCmp('nivelReport').items.items[1].checked)
				{
					imprimir('ACUMULADO_POR_CUENTA_DET_EXCEL','sigesp_spg_rpp_acum_x_cuenta_excel_detallado.php',subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,'');
				}
			}
			if(opcionimp=='G')
			{
				if (Ext.getCmp('graficoReporte').items.items[0].checked)
				{
					imprimir('','',subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,'sigesp_spg_rpp_acum_x_cuenta_torta.php');
				}
				else if(Ext.getCmp('graficoReporte').items.items[1].checked)
				{
					imprimir('','',subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,'sigesp_spg_rpp_acum_x_cuenta_barra.php');
				}
			}
		}
	}
	
	function MostrarTipo(valor)
	{
		if(valor=='B')
		{
			return 'Bienes';
		}
		else if(valor=='S')
		{
			return 'Servicios';
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

	function imprimir(variable,valor,subniveles,mesdes,meshas,nivel,arrCodigosDesde,arrCodigosHasta,ruta)
	{
		if(ruta=='')
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
			Ext.Ajax.request(
			{
				url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
				params: parametros,
				method: 'POST',
				success: function (result, request)
				{ 
					formato = result.responseText;	
					if(empresa['estmodest']==1)
					{
						pagina="reportes/"+formato+"?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
						+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
						+"&codestpro3h="+arrCodigosHasta[2]+"&cmbnivel="+nivel+"&cmbmesdes="+mesdes+"&cmbmeshas="+meshas
						+"&checksubniv="+subniveles+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
						+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
						+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
					}
					else
					{
						pagina="reportes/"+formato+"?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
						+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
						+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
						+"&codestpro3h="+arrCodigosHasta[2]+"&codestpro4h="+arrCodigosHasta[3]
						+"&codestpro5h="+arrCodigosHasta[4]+"&cmbnivel="+nivel+"&cmbmesdes="+mesdes+"&cmbmeshas="+meshas
						+"&checksubniv="+subniveles+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
						+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
						+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
					}
					window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				},
				failure: function (result, request){ 
					Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
				}
			})
		}
		else
		{
			if(empresa['estmodest']==1)
			{
				pagina="reportes/"+ruta+"?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
				+"&codestpro3h="+arrCodigosHasta[2]+"&cmbnivel="+nivel+"&cmbmesdes="+mesdes+"&cmbmeshas="+meshas
				+"&checksubniv="+subniveles+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
				+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
				+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
			}
			else
			{
				pagina="reportes/"+ruta+"?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
				+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
				+"&codestpro3h="+arrCodigosHasta[2]+"&codestpro4h="+arrCodigosHasta[3]
				+"&codestpro5h="+arrCodigosHasta[4]+"&cmbnivel="+nivel+"&cmbmesdes="+mesdes+"&cmbmeshas="+meshas
				+"&checksubniv="+subniveles+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
				+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
				+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
	}
	