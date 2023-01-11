/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de Empresa. 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true;
var formempresa         = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var comcampocatctacon   = null;  												// instancia del componente campo catalogo cuentas contables
var comcampocatbene     = null;  												// instancia del componente campo catalogo cuentas contables
var dataStoreEmpresa 	= '';                           						// Variable que almacena el Registro de la Empresa
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_empresa.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= true;												    // Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaImprimir		= true;
var Actualizar          = null;
var ComboTipo           = null;
var dirvirtual          = '';
var banderaCatalogo		= 'estandar';
var fecha = new Date(); 

var arregloValidacionNivel = [
                          ['1','Partida'],
                          ['2','Generica'],
                          ['3','Especifica'],
                          ['4','Sub-Especifica'],
                          ['5','Auxiliar 1']
                          ]; // Arreglo que contiene los Niveles de las Cuentas de Gasto a los que se puede Validar


var arregloNivelPresupuestario = [['1'],['2'],['3'],['4'],['5']]; // Arreglo que contiene los Niveles a los que se puede Validar

var dataStoreNivelValidacion = new Ext.data.SimpleStore({
	  fields: ['nivel', 'denominacion'],
	  data : arregloValidacionNivel // Se asocian las validaciones disponibles
	});

var dataStoreEmpresa = new Ext.data.SimpleStore({
	  fields: ['codemp', 'nombre']
	});

var dataStoreNivelPresupuestario = new Ext.data.SimpleStore({
	  fields: ['nivel'],
	  data : arregloNivelPresupuestario // Se asocian las validaciones disponibles
	});

var Campos =new Array(
								['codemp','novacio|'], 
								['nombre','novacio|'], 
								['titulo',''], 
								['sigemp',''], 
								['direccion',''], 
								['telemp',''], 
								['faxemp',''], 
								['email',''], 
								['website',''], 
								['m01',''], 
								['m02',''], 
								['m03',''], 
								['m04',''], 
								['m05',''], 
								['m06',''], 
								['m07',''], 
								['m08',''], 
								['m09',''], 
								['m10',''], 
								['m11',''], 
								['m12',''], 
								['periodo',''], 
								['vali_nivel',''], 
								['esttipcont',''], 
								['formpre',''], 
								['formplan',''], 
								['formspi',''], 
								['activo',''], 
								['pasivo',''], 
								['ingreso',''], 
								['gasto',''], 
								['resultado',''], 
								['capital',''], 
								['c_resultad',''], 
								['c_resultan',''], 
								['orden_d',''], 
								['orden_h',''], 
								['soc_gastos',''], 
								['soc_servic',''], 
								['activo_h',''], 
								['pasivo_h',''], 
								['resultado_h',''], 
								['ingreso_f',''], 
								['gasto_f',''], 
								['ingreso_p',''], 
								['gasto_p',''], 
								['numniv',''], 
								['nomestpro1',''], 
								['nomestpro2',''], 
								['nomestpro3',''], 
								['nomestpro4',''], 
								['nomestpro5',''], 
								['rifemp',''],
								['nitemp',''],
								['estemp',''], 
								['ciuemp',''], 
								['zonpos',''], 
								['estmodape',''],  
								['codorgsig',''], 
								['socbieser',''], 
								['estmodest',''], 
								['salinipro',''], 
								['salinieje',''], 
								['numordcom',''], 
								['numordser',''], 
								['numsolpag',''], 
								['nomorgads',''], 
								['numlicemp',''], 
								['concomiva',''], 
								['estmodiva',''], 
								['activo_t',''], 
								['pasivo_t',''], 
								['resultado_t',''], 
								['c_financiera',''], 
								['c_fiscal',''], 
								['diacadche',''], 
								['codasiona',''], 
								['loncodestpro1',''], 
								['loncodestpro2',''],
								['loncodestpro3',''],
								['loncodestpro4',''],
								['loncodestpro5',''],  
								['conrecdoc',''], 
								['nroivss',''], 
								['nomrep',''], 
								['cedrep',''], 
								['telfrep',''], 
								['cargorep',''],  
								['clactacon',''],
								['estparsindis',''],
								['basdatcmp',''],
								['confinstr',''], 
								['estintcred',''],
								['estmodpartsep',''], 
								['estmodpartsoc',''], 
								['estmanant',''], 
								['estpreing',''],
								['estretiva',''], 
								['modageret',''],
								['concommun',''], 
								['confiva',''], 
								['casconmov',''], 
								['estmodprog',''], 
								['confi_ch',''], 
								['ctaresact',''],
								['ctaresant',''], 
								['dedconproben',''], 
								['estaprsep',''], 
								['sujpasesp',''], 
								['bloanu',''],
								['contintmovban',''],
								['valinimovban',''],
								['estretmil',''],
								['concommil',''],
								['estceniva',''],
								['envcorsup',''],
								['capiva',''],
								['estciesem',''],
								['estspgdecimal',''],
								['nivapro',''],
								['estcencos',''],
								['inicencos',''],
								['fincencos',''],
								['cueproacu',''],
								['cuedepamo',''],
								['parcapiva',''],
								['estaprsoc',''],
								['valclacon',''],
								['valcomrd',''],
								['repcajchi',''],
								['cedben',''],
								['nomben',''],
								['scctaben',''],
								['estcomobr',''],
								['tiesesact',''],
								['blocon',''],
								['intblocon',''],
								['valestpre',''],
								['nivvalest',''],
								['estretislr',''],
								['estaprcxp',''],
								['estspidecimal',''],
								['estcommas',''],
								['valiniislr',''],
								['estcanret',''],
								['costo',''],
								['estconcom',''],
								['nroinicom',''],
                                ['scforden_d',''],
                                ['ctaejeprecie',''],
                                ['scforden_h',''],
								['numrefcarord']
		);


Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	var nivel = [['Ninguno', '--'],
				['Nivel 1', 'N1'], 
				['Nivel 2', 'N2'],
				['Nivel 3', 'N3'],
				['Nivel 4', 'N4'],
				['Nivel 5', 'N5']]
    var storeNivel = new Ext.data.SimpleStore({
        fields: ['nomnivel', 'codnivel'],
        data: nivel
    });
    	
	var tipo = [['Con Afectacion al Presupuesto de Gasto', '1'], ['Con Afectacion al Presupuesto de Ingreso', '0']]
    var storeTipo = new Ext.data.SimpleStore({
        fields: ['col', 'tipo'],
        data: tipo
    });
    
    ComboTipo = new Ext.form.ComboBox({
        store: storeTipo,
        editable: false,
        labelSeparator : '',
        fieldLabel : 'Manejo de Notas de Credito',
        displayField: 'col',
        valueField: 'tipo',
        id: 'estafenc',
        width: 250,
        listWidth: 250,
        typeAhead: true,
        triggerAction: 'all',
        mode: 'local'
    })
    
  //creando datastore y columnmodel para el catalogo de beneficiarios
	var reBeneficiario = Ext.data.Record.create([
						{name: 'ced_bene'},
						{name: 'nombene'},
						{name: 'apebene'},
						{name: 'nomapebene'},
						{name: 'scctaben'}
				]);
	
	var dsBeneficiario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},reBeneficiario)
	  			});
						
	var cmBeneficiario = new Ext.grid.ColumnModel([
          				{header: "Cedula", width: 20, sortable: true,   dataIndex: 'ced_bene'},
          				{header: "Nombre", width: 40, sortable: true, dataIndex: 'nombene'},
          				{header: "Apellido", width: 40, sortable: true, dataIndex: 'apebene'}
				]);
	//fin creando datastore y columnmodel para el catalogo de beneficiarios
	
	//componente campocatalogo para el campo de beneficiarios
	comcampocatbene = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Catalogo Beneficiarios',
							anchoformbus: 450,
							altoformbus:165,
							anchogrid: 450,
							altogrid: 400,
							anchoven: 500,
							altoven: 500,
							anchofieldset: 600,
							datosgridcat: dsBeneficiario,
							colmodelocat: cmBeneficiario,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
							parametros: "ObjSon={'oper': 'catbeneficiario'",
							arrfiltro:[{etiqueta:'Cedula',id:'cedben_c',valor:'ced_bene',ancho:150,longitud:'10'},
									   {etiqueta:'Nombre',id:'nomben_c',valor:'nombene'},
									   {etiqueta:'Apellido',id:'apeben_c',valor:'apebene'}],
							posicion:'position:absolute;left:80px;top:235px',
							tittxt:'Beneficiario',
							idtxt:'cedben',
							campovalue:'ced_bene',
							anchoetiquetatext:120,
							anchotext:100,
							anchocoltext:0.45,
							idlabel:'nomben',
							labelvalue:'nomapebene',
							anchocoletiqueta:0.45,
							anchoetiqueta:450,
							tipbus:'P',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false,
							datosadicionales: 1,
							camposoadicionales : [{tipo:'cadena',id:'scctaben'}],
							numFiltroNoVacio:2
				});
	//fin componente campocatalogo para el campo de beneficiarios
	
	//creando datastore y columnmodel para el catalogo cuentas contables
	var registro_ctacon = Ext.data.Record.create([
						{name: 'sc_cuenta'},
						{name: 'denominacion'}
				]);
	
	var dsctacon =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_ctacon)
	  			});
						
	var colmodelcatctacon = new Ext.grid.ColumnModel([
          				{header: "Código", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
          				{header: "Denominación", width: 40, sortable: true, dataIndex: 'denominacion'}
				]);
	//fin creando datastore y columnmodel para el catalogo cuentas contables
	
	//componente campocatalogo para el campo cuenta contable
	comcampocatctacon = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Catalogo Cuentas Contables',
							anchoformbus: 450,
							altoformbus:130,
							anchogrid: 450,
							altogrid: 360,
							anchoven: 500,
							altoven: 430,
							anchofieldset: 700,
							datosgridcat: dsctacon,
							colmodelocat: colmodelcatctacon,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_catcuentas.php',
							parametros: "ObjSon={'operacion': 'catalogo'",
							arrfiltro:[{etiqueta:'Código',id:'codcue',valor:'sc_cuenta',requerido:true,ancho:200,longitud:'25',anyMatch:false},
									   {etiqueta:'Descripción',id:'dencue',valor:'denominacion',ancho:300,longitud:'254'}],
							posicion:'position:absolute;left:5px;top:220px',
							tittxt:'Cuenta Contable Reposición de Caja Chica',
							idtxt:'repcajchi',
							campovalue:'sc_cuenta',
							anchoetiquetatext:230,
							anchotext:130,
							anchocoltext:0.55,
							idlabel:'scgctadeno',
							labelvalue:'denominacion_',
							anchocoletiqueta:0.40,
							anchoetiqueta:150,
							tipbus:'P',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
				});
	//fin componente campocatalogo para el campo cuenta contable
	
	 var Xpos = ((screen.width/2)-(475));
	 formempresa = new Ext.FormPanel({
	 title:"Definición de Empresa",
	 frame:true,
	 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:10px',
	 width: 1000,
	 height: 570,
	 items:[{
	    xtype:"tabpanel",
		activeTab:0,
		deferredRender:false,
		enableTabScroll:true,
		width:980,
	    border:false,
	    frame:true,
	    height: 560,
	    id:"tabempresa",
	    items:[{ 
			    title:"Datos Básicos",
			    labelWidth:150,
				layout:"form",
				frame:true,
				height:550,
				width:950,
				id:'tabdefempresa',
			    items:[{
					    xtype:"fieldset",
					    autoScroll:true,
					    border:false,
					    height:500,
						width:880,
					    items:[{
					        xtype:"textfield",
					        fieldLabel:"Código",
					        name:"codigo",
					        id:"codemp",
							autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
							disabled:true,
					        width:75
					      },{
					        xtype:"textfield",
					        fieldLabel:"Nombre",
					        name:"nombre",
					        id:"nombre",
					        width:400,
							id:"nombre",
							autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ1234567890  ');"},
					        allowBlank:false
					      },{
					        xtype:"textfield",
					        fieldLabel:"Nombre Resumido",
					        name:"txtnomres",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ1234567890  ');"},
							id:"titulo",
					        width:300
					      },{
					        xtype:"textfield",
					        fieldLabel:"Siglas",
					        name:"sigemp",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ1234567890  ');"},
							id:"sigemp",
					        width:150
					      },{
					        xtype:"textfield",
					        fieldLabel:"Dirección",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ1234567890  ');"},
					        name:"textvalue",
							id:"direccion",
					        width:500
					      },{
					        xtype:"textfield",
					        fieldLabel:"Ciudad",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,');"},
					        name:"textvalue",
							id:"ciuemp",
					        width:255
					      },{
					        xtype:"textfield",
					        fieldLabel:"Estado",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,');"},
					        name:"estado",
							id:"estemp",
					        width:255
					      },
					      {
					        xtype:"numberfield",
					        fieldLabel:"Zona Postal",
					        name:"txtzonpos",
							id:"zonpos",
							allowDecimal:false,
							allowNegative:false,
							autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '5', onkeypress: "return keyRestrict(event,'0123456789');"},
					        width:60
					      },
						      {
					        layout:"column",
					        border:false,
					        items:[{
					            columnWidth:0.5,
					            layout:"form",
					            border:false,
					            items:[{
					                xtype:"textfield",
					                fieldLabel:"Telefono",
					                name:"telefono",
									id:"telemp",
									autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"},
					                width:125
					              }]
					          },{
					            columnWidth:0.5,
					            layout:"form",
					            border:false,
					            labelWidth:50,
					            items:[{
					                xtype:"textfield",
					                fieldLabel:"Fax",
					                name:"txtfax",
					                autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '18', onkeypress: "return keyRestrict(event,'0123456789');"},
					                id:"faxemp",
					                width:125
					              }]
					          }]
					      },{
					        layout:"column",
					        border:false,
					        items:[{
					            layout:"form",
					            columnWidth:0.5,
					            border:false,
					            items:[{
					                xtype:"textfield",
					                fieldLabel:"RIF",
					                name:"txtrif",
					                autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'JGVE-0123456789');"},
									id:"rifemp",
					                width:150,
					                listeners:{
			   							'blur' : function(campo){
			   								var regExPattern = /^[JGVE]-\d{8}-\d$/
			   									if (!campo.getValue().match(regExPattern)){
			   										Ext.Msg.show({
			   											title:'Advertencia',
			   											msg: 'El formato del RIF es incorrecto, use [JGVE]-[99999999]-[9]',
			   											buttons: Ext.Msg.OK,
			   											icon: Ext.MessageBox.WARNING
			   										});
			   									}
			   								uf_verificar_rif(campo.getValue());
			   							}
			   						}
					              }]
					          },{
					            layout:"form",
					            border:false,
					            columnWidth:0.5,
					            labelWidth:50,
					            items:[{
					                xtype:"textfield",
					                fieldLabel:"NIT",
					                name:"txtnit",
					                autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"},
									id:"nitemp",
					                width:150
					              }]
					          }]
					      },{
					        xtype:"textfield",
					        fieldLabel:"Nro. IVSS",
					        width:250,
					        name:"txtnroseguro",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"},
							id:"nroivss"
					      },{
					        xtype:"textfield",
					        fieldLabel:"E-mail",
					        name:"txtcorreo",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ._-@');"},
							vtype: 'email',
							id:"email",
					        width:300
					      },{
					        xtype:"textfield",
					        fieldLabel:"Sitio Web",
					        name:"txtsitioweb",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ._-@1234567890');"},
							id:"website",
					        width:300
					      },{
					        xtype:"textfield",
					        fieldLabel:"Nro. de Licencia",
					        name:"txtnrolicencia",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '25', onkeypress: "return keyRestrict(event,'0123456789');"},
							id:"numlicemp",
					        width:300
					      },{
					        xtype:"textfield",
					        fieldLabel:"Organismo de Adscripción",
					        name:"txtorgadscripcion",
					        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
							id:"nomorgads",
					        width:500
					      },{
				              xtype: "checkboxgroup",
				              fieldLabel: "",
				              labelSeparator:"",
							  labelWidth:15,
				              columns:[345,355],
							  vertical:true,
				              items: [
				                  //{boxLabel: "Permitir Cambio de Empresa", name: 'chkpercamempresa', id:"estcamemp", inputValue:1},
				                  {boxLabel: "Manejo de Anticipo (Caso Pago Directo)", name: 'chkmananticipo', id:"estmanant", inputValue:1},
								  {boxLabel: "Presupuesto de Ingreso por Estructura", name: 'chkperspiestructura', id:"estpreing", inputValue:1},
				                  {boxLabel: "Integrar con Presupuesto de Creditos", name: 'chkintprecreditos', id:"estintcred", inputValue:1},
								  {boxLabel: "Sujeto Pasivo Especial", name: 'chksujpasespecial', id:"sujpasesp", inputValue:1},
								  //{boxLabel: "Trabajar con Interfases Bancarias", name: 'chkintban', id:"estintban", inputValue:1},
								  {boxLabel: "Envio Notificación a Supervisor", name: 'chknotsup', id:"envcorsup", inputValue:1},
								  {boxLabel: "Capitalización del IVA", name: 'chkcapgasiva', id:"capiva", inputValue:1},
								  {boxLabel: "Cierre Semestral(SUDEBAN)", name: 'chkestciesem', id:"estciesem", inputValue:1},
								  {boxLabel: "Niveles de Aprobación", name: 'chknivapro', id:"nivapro", inputValue:1},
								  {boxLabel: "Centro de Costos Contables", name: 'chkestcencos', id:"estcencos", inputValue:1},
								  {boxLabel: "Validar Estructura Presupuestaria", name: 'chkvalestpre', id:"valestpre", inputValue:1},
								  {boxLabel: "Decimales en Apertura SPG", name: 'chkestspgdecimal', id:"estspgdecimal", inputValue:1},
								  {boxLabel: "Decimales en Apertura SPI", name: 'chkestspidecimal', id:"estspidecimal", inputValue:1},
								  {boxLabel: "Comprobante Contable Masivo", name: 'chkestcommas', id:"estcommas", inputValue:1}
				              ] // Items del CheckGroup
				          }] // Items contenidos en Datos Basicos
			    }] // Items del Tab Datos Basicos
	  },{
		  title:"Representante Legal",
		  height:390,
		  labelWidth:150,
		  width:950,
		  layout:"form",
		  frame:true,
		  listeners:{
  	 				'beforeshow': function(componente){
  	 													if(Ext.getCmp('codemp').getValue() == "")
  	 													{
  	 														Ext.Msg.alert('Mensaje','Debe indicar el código de empresa, verifique por favor');
  	 														Ext.getCmp('tabempresa').activate('tabdefempresa');
  	 														return false;
  	 													}
  	 													else
  	 													{
  	 														return true;
  	 													}
  											          }
                    },
		  items:[{
			      xtype:"fieldset",
				  autoScroll:true,
				  border:false,
				  height:458,
				  style: 'margin-top:20px;margin-left: 25px',
			      items:[{
				        xtype:"textfield",
				        fieldLabel:"Apellidos y Nombres",
				        name:"txtnomaperep",
						id:"nomrep",
						autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '60', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
				        width:400
				      },{
				        xtype:"textfield",
				        fieldLabel:"Cedula de Identidad",
				        name:"txtcedrep",
				        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'EV0123456789');"},
						id:"cedrep",
				        width:300
				      },{
				        xtype:"textfield",
				        fieldLabel:"Telefono",
				        name:"txttelrep",
				        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'.-0123456789');"},
						id:"telfrep",
				        width:150
				      },{
				        xtype:"textfield",
				        fieldLabel:"Cargo",
				        name:"txtcarpre",
				        autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '80', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
						id:"cargorep",
				        width:500
				      }]
	    }]// Items del Tab Informacion de Consolidacion
	  },{
		  title:"Ejercicio Fiscal",
		  layout:"form",
		  width:950,
		  frame:true,
		  listeners:{
 				'beforeshow': function(componente){
 													if(Ext.getCmp('codemp').getValue() == "")
 													{
 														Ext.Msg.alert('Mensaje','Debe indicar el código de empresa, verifique por favor');
 														Ext.getCmp('tabempresa').activate('tabdefempresa');
 														return false;
 													}
 													else
 													{
 														return true;
 													}
										          }
              },
		  items:[{
			      xtype:"fieldset",
				  autoScroll:true,
				  border:false,
				  height:390,
				  width:800,
				  //style: 'margin-top:20px;margin-left: 50px',
			      items:[{
				            layout:"form",
				            columnWidth:1,
				            border:false,
							labelWidth:75,
							style: 'margin-top:10px;margin-left: 200px',
				            items:[{
				                xtype:"datefield",
				                fieldLabel:"Periodo",
				                name:"txtperiodo",
				                allowBlank:false,
								width:100,
								id:"periodo",
								editable:false
								}]
				      },
				      {
					      xtype:"fieldset",
						  title:"Validaciones",
						  border:true,
						  height:170,
						  width:780,
					      items:[{
					                xtype:"combo",
					                labelSeparator:'',
					                store: dataStoreNivelValidacion,
					                hiddenName:'valnivel',
					                displayField:'denominacion',
					                valueField:'nivel',
									id:"vali_nivel",
					                typeAhead: true,
					                mode: 'local',
					                triggerAction: 'all',
					                selectOnFocus:true,
					                fieldLabel:'Nivel',
					           	    listWidth:150,
					           	    editable:false,
					                width:150
				         		},
				         		{
				         			xtype:"combo",
				         			store: storeNivel,
								    editable: false,
								    labelSeparator : '',
								    fieldLabel : 'Nivel de validación por estructura',
								    displayField: 'nomnivel',
								    valueField: 'codnivel',
								    id: 'nivvalest',
								    width: 150,
								    listWidth: 150,
								    typeAhead: true,
								    triggerAction: 'all',
								    mode: 'local'
				         		},
			         			{
					                xtype: "checkboxgroup",
					                fieldLabel: "",
					                labelSeparator:"",
					                columns: 1,
					                items: [
					                  {boxLabel: "Colocar estatus REGISTRADA al generar SEP con bienes, servicios o conceptos sin disponibilidad presupuestaria", name: 'chkgensepsindispres',id:"estparsindis", inputValue:1},
					                  {boxLabel: "Validación por el Programado", name: 'chkvalprogramado', id:"estmodprog", inputValue:1},
									  {boxLabel: "Bloquear anulación de documentos en meses cerrados", name: 'chkbloanudocmescerrado', id:"bloanu", inputValue:1}
					              ] // Items del CheckGroup
					            }] // Items del fieldset Validacion
			          }
			          ,
			          {
			              xtype: "checkboxgroup",
			              fieldLabel: "Meses Abiertos",
			              style: 'margin-top:30px;margin-left: 15px',
			              columns: [175, 175, 175],
			              vertical:true,
			              autoWidth:false,
			              items: [
			                  {boxLabel: "Enero", name: 'chkenero',id:"m01", inputValue:1},
			                  {boxLabel: "Febrero", name: 'chkfebrero',id:"m02", inputValue:1},
			                  {boxLabel: "Marzo", name: 'chkmarzo',id:"m03", inputValue:1},
			                  {boxLabel: "Abril", name: 'chkabril',id:"m04", inputValue:1},
			                  {boxLabel: "Mayo", name: 'chkmayo',id:"m05", inputValue:1},
			                  {boxLabel: "Junio", name: 'chkjunio',id:"m06", inputValue:1},
			                  {boxLabel: "Julio", name: 'chkjulio',id:"m07", inputValue:1},
			                  {boxLabel: "Agosto", name: 'chkagosto',id:"m08", inputValue:1},
			                  {boxLabel: "Septiembre", name: 'chkseptiembre',id:"m09", inputValue:1},
			                  {boxLabel: "Octubre", name: 'chkoctubre',id:"m10", inputValue:1},
			                  {boxLabel: "Noviembre", name: 'chknoviembre',id:"m11", inputValue:1},
			                  {boxLabel: "Diciembre", name: 'chkdiciembre',id:"m12", inputValue:1}
			              ] // Items del CheckGroup Meses
			          }]
		        }] // Items del Tab Informacion de Consolidacion
	  },{
		  title:"Formatos y Digitos de Cuentas",
		  height:380,
		  layout:"accordion",
		  width:950,
		  frame:true,
		  listeners:{
 				'beforeshow': function(componente){
 													if(Ext.getCmp('codemp').getValue() == "")
 													{
 														Ext.Msg.alert('Mensaje','Debe indicar el código de empresa, verifique por favor');
 														Ext.getCmp('tabempresa').activate('tabdefempresa');
 														return false;
 													}
 													else
 													{
 														return true;
 													}
										          }
              },
          layoutConfig:{
					      activeOnTop:false,
						  animate:true,
						  collapseFirst:true,
						  fill:true,
						  titleCollapse:true},
		  items:[{
				 	title:"Formatos",
				 	iconCls :'bmenuagregar',
				    collapsed : true,
				 	items:[{
						 xtype:"fieldset",
				         autoScroll:true,
				         border:false,
						 height:250,
						 items:[{
								xtype: "radiogroup",
								style: 'margin-top:20px;margin-left: 100px',
								fieldLabel: "",
								labelSeparator:"",
								columns: [250, 250],
								id:"esttipcont",
								items: [
										{boxLabel: 'Contabilidad General', name:'rbcontfisgen', inputValue: 1},
										{boxLabel: 'Contabilidad Fiscal', name: 'rbcontfisgen', inputValue: 2}
                					   ]
            					},
								{
								layout:"column",
								border:false,
								style: 'margin-top:30px;margin-left: 25px',
								items:[{
									layout:"form",
									columnWidth:0.5,
									labelWidth:150,
									border:false,
									items:[{
										xtype:"textfield",
										labelWidth:150,
										fieldLabel:"Plan Unico de Cuentas",
										name:"txtmskplanunico",
										maxLength:30,
										allowBlank:false,
										id:"formplan",
										width:150,
										autoCreate: {tag: 'input', type: 'text', size: '30', autocomplete: 'off', maxlength: '30', onkeypress: "return keyRestrict(event,'9-');"}
									  }]
								  },
								  {
									layout:"form",
									columnWidth:0.3,
									labelWidth:150,
									border:false,
									bodyStyle:'padding-left:10px',
									items:[{
										xtype:"textfield",
										labelWidth:150,
										fieldLabel:"Contabilidad",
										name:"txtmskplanunico",
										maxLength:15,
										allowBlank:false,
										id:"formcont_1",
										width:95,
										autoCreate: {tag: 'input', type: 'text', size: '20', autocomplete: 'off', maxlength: '30', onkeypress: "return keyRestrict(event,'9-');"}
									  }]
								  },
								  {
									layout:"form",
									columnWidth:0.2,
									bodyStyle:'padding-left:5px',
									border:false,
									items:[{
											xtype:"textfield",
											hideLabel: true,
											fieldLabel:"",
											labelSeparator:"",
											maxLength:10,
											allowBlank:false,
											id:"formcont_2",
											width:100,
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '30', onkeypress: "return keyRestrict(event,'9-');"}
										  }]
								  }]	
						},{
							layout:"column",
							border:false,
							style: 'margin-top:5px;margin-left: 25px',
							items:[{
								layout:"form",
								columnWidth:0.5,
								border:false,
								labelWidth:150,
								items:[{
									xtype:"textfield",
									labelWidth:150,
									fieldLabel:"Presupuesto de Gasto",
									name:"txtmskpregasto",
									maxLength:30,
									minLength:1,
									allowBlank:false,
									id:"formpre",
									width:150,
									autoCreate: {tag: 'input', type: 'text', size: '30', autocomplete: 'off', maxlength: '30', onkeypress: "return keyRestrict(event,'9-');"}
								  }]
							  },
							  {
									layout:"form",
									columnWidth:0.5,
									labelWidth:150,
									border:false,
									bodyStyle:'padding-left:10px',
									items:[{
										xtype:"textfield",
										fieldLabel:"Presupuesto de Ingreso",
										name:"txtmskpreingreso",
										maxLength:30,
										minLength:1,
										allowBlank:false,
										id:"formspi",
										width:150,
										autoCreate: {tag: 'input', type: 'text', size: '30', autocomplete: 'off', maxlength: '30', onkeypress: "return keyRestrict(event,'9-');"}
									  }]
							}]
						},
						{
							layout:"column",
							border:false,
							style: 'margin-top:5px;margin-left: 100px',
							items:[{
									layout:"form",
									columnWidth:0.5,
									labelWidth:150,
									border:false,
									bodyStyle:'padding-left:10px',
									items:[{
										xtype:"textfield",
										fieldLabel:"Inicio Centro de Costos",
										name:"txtmskplanunico",
										maxLength:30,
										allowBlank:false,
										id:"inicencos",
										width:50,
										autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '2', onkeypress: "return keyRestrict(event,'0123456789');"},
										listeners:{
											'blur': function(){
												var valorcampo = this.getValue();
												var fincencos = parseInt(this.getValue()) + 2;
												Ext.getCmp('fincencos').setValue(fincencos);
											}
										}
									  }]
									},
									{
									layout:"form",
									columnWidth:0.5,
									border:false,
									labelWidth:150,
									bodyStyle:'padding-left:20px',
									items:[{
										xtype:"textfield",
										fieldLabel:"Fin Centro de Costos",
										name:"txtmskpregasto",
										maxLength:5,
										allowBlank:false,
										id:"fincencos",
										width:50,
										readOnly:true,
										autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '2', onkeypress: "return keyRestrict(event,'0123456789');"}
									}]
								}]
							},{
								layout:"column",
								border:false,
								style: 'margin-top:30px;margin-left: 320px',
								items:[{
									layout:"form",
									labelWidth:150,
									border:false,
									items:[{
										xtype:"button",
										text:"<b>Digitos de Centros de Costos</b>",
										handler: function (){
											if(empresa['estcencos']=='1'){
												ventanaCentroCostos(empresa);
											}
											else{
												Ext.Msg.show({
													title:'Mensaje',
							        				msg: 'Debe configurar las empresa con el manejo de centros de costos',
							        				buttons: Ext.Msg.OK,
							        				icon: Ext.MessageBox.WARNING
							    				});
											}
										}
									}]
								}]
							}] // Items del FieldSet Formatos
				 		}] // Items del Accordion Formatos
				 },
				 {
				    title:"Digitos de Cuentas - Contabilidad Patrimonial",
				    iconCls :'bmenuagregar',
				    collapsed : true,
				    items:[{
						 xtype:"fieldset",
				         border:false,
						 height:150,
						 width:700,
						 style: 'margin-top:10px',
			             items:[{
					        layout:"column",
					        border:false,
							style:'padding-left:100px',
					        items:[{
					            layout:"form",
					            columnWidth:0.33,
					            border:false,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Activo",
					                name:"txtdigactivo",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									id:"activo",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          },
							  {
					            layout:"form",
								columnWidth:0.33,
					            border:false,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Pasivo",
					                name:"txtdigpasivo",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									id:"pasivo",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          },
							  {
					            layout:"form",
					            border:false,
					            columnWidth:0.34,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Ingreso",
					                name:"txtdigingreso",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									id:"ingreso",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          }] // Items de la Columna del FieldSet
					      },
						  {
						  	layout:"column",
					        border:false,
							style:'padding-left:100px;margin-top:20px',
					        items:[{
					            layout:"form",
					            border:false,
					            columnWidth:0.33,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Gasto",
					                name:"txtdiggasto",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									id:"gasto",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          },
							  {
					            layout:"form",
					            border:false,
					            columnWidth:0.33,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Resultado",
					                name:"txtdigresultado",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									id:"resultado",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          },
							  {
					            layout:"form",
					            border:false,
					            columnWidth:0.34,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Capital",
					                name:"txtdigcapital",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									style: 'text-align:left',
									id:"capital",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          }]
						  },
						  {
						  	layout:"column",
					        border:false,
							style:'padding-left:100px;margin-top:30px',
					        items:[{
					            layout:"form",
					            border:false,
					            columnWidth:0.33,
					            items:[{
					                xtype:"numberfield",
					                fieldLabel:"Costo",
					                name:"txtdigcosto",
									maxLength:1,
									minLength:1,
									allowBlank:false,
									allowDecimals:false,
									allowNegative:false,
									id:"costo",
									autoCreate: {tag: 'input', type: 'text', size: '1', autocomplete: 'off', maxlength: '1'},
					                width:30
					              }]
					          }]						 						  
						  }] // Items del FieldSet de Formatos
						},
						{ 
						    xtype:"fieldset",
							style: 'padding-left:10px; margin-top:5px',
							border:false,
							height:150,
							items:[{layout:"column",
							        border:false,
									items:[{
							            	layout:"form",
							            	border:false,
							            	labelWidth:400,
							            	items:[{
							            		xtype:"numberfield",
							            		fieldLabel:"Grupo de Cuentas de Provisiones Acumuladas y Reservas Tecnicas",
							            		allowBlank:false,
							            		allowDecimals:false,
							            		allowNegative:false,
							            		style: 'text-align:left',
							            		id:"cueproacu",
							            		autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '3'},
							            		width:30
							            	}]
							        	},{
							        		layout:"form",
							        		border:false,
							        		labelWidth:400,
							        		items:[{
							        			xtype:"numberfield",
							        			fieldLabel:"Grupo de Cuentas Depreciacion y Amortizacion Acumulada",
							        			allowBlank:false,
							        			allowDecimals:false,
							        			allowNegative:false,
							        			style: 'text-align:left',
							        			id:"cuedepamo",
							        			autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '3'},
							        			width:30
						            	}]
							        }]
								},{
									layout: "column",
									border: false,
									defaults: {columnWidth: ".5",border: false},
									items:[{
										    bodyStyle: "padding-right:5px;",
											items:[{
													xtype:"fieldset",
													title: "Orden",
													height:50,
													items: [{
															 layout:"column",
															 border:false,
															 items:[{
																layout:"form",
																border:false,
															    defaultType: "numberfield",  
																bodyStyle:'padding-left:10px',
																columnWidth:0.5,
																items:[{
																		fieldLabel:"Deudor",
																		name:"txtdigdeudor",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"orden_d",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
																		width:30
																	   }]
															},
															{
																layout:"form",
																border:false,
																bodyStyle:'padding-left:10px',
																columnWidth:0.5,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Acreedor",
																		name:"txtdigacreedor",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"orden_h",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
																		width:30
																		}]
															}] // Items de la Columna del FieldSet Orden
														}] // Items del FieldSet Orden
												   }] // Items de la Columna que contiene el FieldSet Orden
										   },
										   {
											bodyStyle: "padding-right:5px;",
											items:[{
													xtype:"fieldset",
													title: "Presupuesto",
													height:50,
													items: [{
															 layout:"column",
															 border:false,
															 items:[{
																layout:"form",
																border:false,
																defaultType: "numberfield",  
																bodyStyle:'padding-left:10px',
																columnWidth:0.5,
																items:[{
																		fieldLabel:"Gasto",
																		name:"txtdigcontgasto",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"gasto_p",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '1'},
																		width:30
																	   }]
															},
															{
																layout:"form",
																border:false,
																bodyStyle:'padding-left:10px',
																columnWidth:0.5,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Ingreso",
																		name:"txtdigcontingreso",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"ingreso_p",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '1'},
																		width:30
																		}]
															}] // Items de la Columna del FieldSet Orden
														}]// Items del FieldSet Presupuesto
												   }] // Items del de la Columna que contiene el FieldSet Presupuesto
										   }] // Items de la Columna del FieldSet de Digitos de Cuentas - Contabilidad General
								   }]// Items del FieldSet de Digitos de Cuentas - Contabilidad General
						  }] // Items de los Digitos de Cuentas
				 },
				 {
				 	title:"Digitos de Cuentas - Contabilidad Fiscal",
				 	iconCls :'bmenuagregar',
				    collapsed : true,
				 	items:[{ 
						    xtype:"fieldset",
							style: 'padding-left:10px; margin-top:10px',
							border:false,
							height:170,
							items:[{
									layout: "column",
									defaults: {columnWidth: ".5",border: false},
									items:[{
										    bodyStyle: "padding-right:5px;",
											items:[{
													xtype:"fieldset",
													title: "Hacienda",
													height:50,
													labelWidth:60,
													items: [{
															 layout:"column",
															 border:false,
															 items:[{
																layout:"form",
																border:false,
															    defaultType: "numberfield",  
																columnWidth:0.33,
																items:[{
																		fieldLabel:"Activo",
																		name:"txtdigconfishacactivo",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"activo_h",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																	   }]
															},
															{
																layout:"form",
																border:false,
																columnWidth:0.33,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Pasivo",
																		name:"txtdigconfishacpasivo",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"pasivo_h",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																		}]
															},
															{
																layout:"form",
																border:false,
																columnWidth:0.34,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Resultado",
																		name:"txtdigconfishacresultado",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"resultado_h",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																		}]
															}] // Items de la Columna del FieldSet Orden
														}] // Items del FieldSet Orden
												   },
												   {
													xtype:"fieldset",
													title: "Tesoro",
													height:50,
													labelWidth:60,
													items: [{
															 layout:"column",
															 border:false,
															 items:[{
																layout:"form",
																border:false,
															    defaultType: "numberfield",
																columnWidth:0.33,
																items:[{
																		fieldLabel:"Activo",
																		name:"txtdigconfistesactivo",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"activo_t",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																	   }]
															},
															{
																layout:"form",
																border:false,
																columnWidth:0.33,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Pasivo",
																		name:"txtdigconfistespasivo",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"pasivo_t",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																		}]
															},
															{
																layout:"form",
																border:false,
																columnWidth:0.34,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Resultado",
																		name:"txtdigconfistesresultado",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"resultado_t",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																		}]
															}] // Items de la Columna del FieldSet Orden
														}] // Items del FieldSet Orden
												   }] // Items de la Columna que contiene el FieldSet Orden
										   },
										   {
											bodyStyle: "padding-right:5px;",
											items:[{
													xtype:"fieldset",
													title: "Fiscal",
													height:50,
													items: [{
															 layout:"column",
															 border:false,
															 items:[{
																layout:"form",
																border:false,
																defaultType: "numberfield",  
																bodyStyle:'padding-left:10px',
																columnWidth:0.5,
																items:[{
																		fieldLabel:"Gasto",
																		name:"txtdigconfispregasto",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"gasto_f",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																	   }]
															},
															{
																layout:"form",
																border:false,
																bodyStyle:'padding-left:10px',
																columnWidth:0.5,
																defaultType: "numberfield", 
																items:[{
																		fieldLabel:"Ingreso",
																		name:"txtdigconfispreingreso",
																		maxLength:3,
																		minLength:1,
																		allowBlank:false,
																		allowDecimals:false,
																		allowNegative:false,
																		id:"ingreso_f",
																		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																		width:30
																		}]
															}] // Items de la Columna del FieldSet Orden
														}]// Items del FieldSet Presupuesto
												   }] // Items del de la Columna que contiene el FieldSet Presupuesto
										   },
										   {
												bodyStyle: "padding-right:5px;",
												items:[{
														xtype:"fieldset",
														title: "Orden",
														height:50,
														items: [{
																 layout:"column",
																 border:false,
																 items:[{
																	layout:"form",
																	border:false,
																	defaultType: "numberfield",  
																	bodyStyle:'padding-left:10px',
																	columnWidth:0.5,
																	items:[{
																			fieldLabel:"Deudor",
																			name:"txtdigconfispregasto",
																			maxLength:3,
																			minLength:1,
																			allowBlank:false,
																			allowDecimals:false,
																			allowNegative:false,
																			id:"scforden_h",
																			autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																			width:30
																		   }]
																},
																{
																	layout:"form",
																	border:false,
																	bodyStyle:'padding-left:10px',
																	columnWidth:0.5,
																	defaultType: "numberfield", 
																	items:[{
																			fieldLabel:"Acreedor",
																			name:"txtdigconfispreingreso",
																			maxLength:3,
																			minLength:1,
																			allowBlank:false,
																			allowDecimals:false,
																			allowNegative:false,
																			id:"scforden_d",
																			autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
																			width:30
																			}]
																}] // Items de la Columna del FieldSet Orden
															}]// Items del FieldSet Presupuesto
													   }] // Items del de la Columna que contiene el FieldSet Presupuesto
											   }] // Items de la Columna de FieldSet de Digitos de Cuentas - Contabilidad Fiscal
								   }] // Items del FieldSet de Digitos de Cuentas - Contabilidad Fiscal
						  }] // Items del Accordion Digitos de Cuentas - Contabilidad Fiscal
				 }]// Items del Tab Formatos de Cuentas
	  },{
		  title:"Configuración de Cuentas de Resultado, Estructura y Niveles Presupuestarios",
		  height:380,
		  layout:"accordion",
		  width:950,
		  frame:true,
		  listeners:{
 				'beforeshow': function(componente){
 													if(Ext.getCmp('codemp').getValue() == "")
 													{
 														Ext.Msg.alert('Mensaje','Debe indicar el código de empresa, verifique por favor');
 														Ext.getCmp('tabempresa').activate('tabdefempresa');
 														return false;
 													}
 													else
 													{
 														return true;
 													}
										          }
              },
          layoutConfig:{  activeOnTop:false,
						  animate:true,
						  collapseFirst:true,
						  fill:true,
						  titleCollapse:true},
		  items:[{
				  title:"Cuentas",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  items:[{
						 	xtype:"fieldset",
							style: 'padding-left:10px; margin-top:10px',
							border:false,
							height:700,
							width:950,
							items:[{
								   	layout: "column",
									defaults: {columnWidth: "1",border: false},
									items:[{
										    bodyStyle: "padding-right:5px;",
											items:[{
													xtype:"fieldset",
													title: "Cuentas Resultados",
													height:80,
													border:true,
													width:850,
													items: [{
															 layout:"column",
															 //defaults: {columnWidth: ".5",border: false},
															 items:[{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:10px;",
																		labelWidth:100,
																		items:[{
																				fieldLabel:"Resultado Actual",
																				name:"txtctaresactual",
																				maxLength:25,
																				minLength:1,
																				id:"c_resultad",
																				readOnly:true,
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler: function (){
																					if(Ext.getCmp('codemp').getValue() == empresa['codemp']){
																						mostrarCatalogoCuentaContable('catalogocuentaresultado',Ext.getCmp('c_resultad'),null);
																					}
																					else {
																						Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																					}
																				}
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:20px;",
																		labelWidth:125,
																		items:[{
																				fieldLabel:"Resultado Anterior",
																				name:"txtctaresanterior",
																				maxLength:25,
																				minLength:1,
																				id:"c_resultan",
																				readOnly:true,
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler: function (){
																										if(Ext.getCmp('codemp').getValue() == empresa['codemp'])
																										{
																										 mostrarCatalogoCuentaContable('catalogocuentaresultado',Ext.getCmp('c_resultan'),null);
																										}
																										else
																										{
																										 Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																										}
																									}
																			   }]
															/*aqui*/},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:10px;",
																		labelWidth:160,
																		items:[{
																				fieldLabel:"Resultado del Presupuesto ",
																				name:"txtctaejeprecie",
																				maxLength:25,
																				minLength:1,
																				id:"ctaejeprecie",
																				readOnly:true,
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler: function (){
																										if(Ext.getCmp('codemp').getValue() == empresa['codemp'])
																										{
																										 mostrarCatalogoCuentaContable('catalogo',Ext.getCmp('ctaejeprecie'),null);
																										}
																										else
																										{
																										 Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																										}
																									}
																			   }]
																	}] // Items de la Columna del FildSet de Cuentas de Resultados
															}] // Items del FieldSet de la Columna del FieldSet de Cuentas
										  		   },
												   {
													xtype:"fieldset",
													title: "Cuentas Consolidación Resultados",
													height:50,
													labelWidth:60,
													border:true,
													width:850,
													items: [{
															 layout:"column",
															 //defaults: {columnWidth: ".25",border: false},
															 items:[{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:10px;",
																		labelWidth:100,
																		items:[{
																				fieldLabel:"Resultado Actual",
																				name:"txtctaconresactual",
																				maxLength:25,
																				minLength:1,
																				id:"ctaresact",
																				readOnly:true,
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler: function (){
																										if(Ext.getCmp('codemp').getValue() == empresa['codemp'])
																										{	
																											mostrarCatalogoCuentaContable('catalogocuentaresultado',Ext.getCmp('ctaresact'),null);
																										}
																										else
																										{
																											Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																										}
																									}
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:20px;",
																		labelWidth:125,
																		items:[{
																				fieldLabel:"Resultado Anterior",
																				name:"txtctaconresanterior",
																				maxLength:25,
																				minLength:1,
																				id:"ctaresant",
																				readOnly:true,
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler: function (){
																										if(Ext.getCmp('codemp').getValue() == empresa['codemp'])
																										{	
																											mostrarCatalogoCuentaContable('catalogocuentaresultado',Ext.getCmp('ctaresant'),null);
																										}
																										else
																										{
																											Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																										}
																									}
																			   }]
																	}] // Items de la Columna del FildSet de Cuentas de Resultados
															}] // Items del FieldSet de la Columna del FieldSet de Cuentas
										  		   },
												   {
													xtype:"fieldset",
													title: "Cuentas Situación del Tesoro",
													height:50,
													labelWidth:60,
													border:true,
													width:850,
													items: [{
															 layout:"column",
															 //defaults: {columnWidth: ".5",border: false},
															 items:[{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:10px;",
																		labelWidth:100,
																		items:[{
																				fieldLabel:"Financiera (199)",
																				name:"txtctasittesfinanciera",
																				maxLength:25,
																				minLength:1,
																				readOnly:true,
																				id:"c_financiera",
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler : function(){
																										
																										if(Ext.getCmp('codemp').getValue() == empresa['codemp'])
																										{
																				                            if(Ext.getCmp('esttipcont').items.items[1].checked)
																											{
																												mostrarCatalogoCuentaContable('catalogocuentafinanciera',Ext.getCmp('c_financiera'),null);
																											}
																											else if(Ext.getCmp('esttipcont').items.items[0].checked)
																											{
																												obtenerMensaje('informacion','','Mensaje','Configuracion no permitida, el tipo de contabilidad no es fiscal');
																											}
																											else
																											{
																												obtenerMensaje('informacion','','Mensaje','Debe indicar el tipo de contabilidad, verifique por favor');
																											}
																										}
																										else
																										{
																											Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																										}
																									}
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "textfield",  
																		columnWidth:0.4,
																		style: "padding-left:20px;",
																		labelWidth:125,
																		items:[{
																				fieldLabel:"Fiscal (200)",
																				name:"txtctasittesfiscal",
																				maxLength:25,
																				minLength:1,
																				readOnly:true,
																				id:"c_fiscal",
																				autoCreate: {tag: 'input', type: 'text', size: '25', autocomplete: 'off', maxlength: '25'},
																				width:150
																			   }]
																	},
																	{
																		layout:"form",
																		border:false,
																		defaultType: "button",  
																		columnWidth:0.1,
																		items:[{
																				iconCls: 'menubuscar',
																				style:'padding-left:5px;',
																				handler : function(){
																										if(Ext.getCmp('codemp').getValue() == empresa['codemp'])
																										{
																											if(Ext.getCmp('esttipcont').items.items[1].checked)
																											{
																												mostrarCatalogoCuentaContable('catalogocuentafiscal',Ext.getCmp('c_fiscal'),null);
																											}
																											else if(Ext.getCmp('esttipcont').items.items[0].checked)
																											{
																												obtenerMensaje('informacion','','Mensaje','Configuracion no permitida, el tipo de contabilidad no es fiscal');
																											}
																											else
																											{
																												obtenerMensaje('informacion','','Mensaje','Debe indicar el tipo de contabilidad, verifique por favor');
																											}
																										}
																										else
																										{
																											Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
																										}
																									}
																			   }]
																	}] // Items de la Columna del FildSet de Cuentas de Resultados
															}] // Items del FieldSet de la Columna del FieldSet de Cuentas
										  		   }] // Items de los Items de la Columna que contiene el FieldSet Cuentas de Resultados
										   }] // Items de la Columna del FieldSet de Cuentas
								   }] // Items del FieldSet de Cuentas
						 }] // Items del Accordion de Cuentas
				 },
				 {
					 title:"Estructuras y Niveles Presupuestarios",
					 collapsed : true,
					 iconCls :'bmenuagregar',
					 tooltip : 'Click para desplegar opciones',
					 items:[{
							 xtype:"fieldset",
							 autoScroll:true,
							 border:false,
							 height:400,
							 labelWidth:200,
							 items:[{
									xtype:"fieldset",
									title: "Configuración de Estructura Presupuestaria o Programatica",
									height:50,
									labelWidth:60,
									border:true,
									width:690,
									items:[{
											xtype: "radiogroup",
											style: 'margin-left: 100px',
											fieldLabel: "",
											labelSeparator:"",
											id:"estmodest",
											columns: [250, 250],
											items: [
													{boxLabel: "Por Proyectos", name: "rbtipestpre", inputValue: 1},
													{boxLabel: "Por Programas", name: "rbtipestpre", inputValue: 2}
												   ] // Items del RadioGroup de Configuracion de Estructura Presupuestaria o Programatica
											}] // Items del FieldSet de Configuracion de Estrucutura Presupuestaria o Programatica
									},
									{
										xtype:"combo",
						                store: dataStoreNivelPresupuestario,
						                hiddenName:'nivel',
						                valueField:'nivel',
						                displayField:'nivel',
						                typeAhead: true,
						                mode: 'local',
						                triggerAction: 'all',
						                selectOnFocus:true,
						                fieldLabel:"Numero de Niveles de la Estructura",
						           	    listWidth:20,
						           	    editable:false,
										id:"numniv",
						                width:20,
						                listeners : {
						                	'select': function(){
						                		validaNivelesEst(this.getValue());
						                	}
						                }
				         			},
									{
									 layout:"column",
									 defaults: {columnWidth: ".5",border: false},
									 title:"Estructura Presupuestaria",
									 items:[{
												layout:"form",
												border:false,
												defaultType: "textfield",  
												columnWidth:0.5,
												style: "margin-top:10px;",
												labelWidth:100,
												items:[{
														fieldLabel:"Nombre Nivel 1",
														name:"txtnomnivel1",
														maxLength:200,
														allowBlank:false,
														id:"nomestpro1",
														width:200
													   },
													   {
														fieldLabel:"Nombre Nivel 2",
														name:"txtnomnivel1",
														maxLength:200,
														allowBlank:false,
														id:"nomestpro2",
														width:200
													   },
													   {
														fieldLabel:"Nombre Nivel 3",
														name:"txtnomnivel1",
														maxLength:200,
														allowBlank:false,
														id:"nomestpro3",
														width:200
													   }]
											},
											{
												layout:"form",
												border:false,
												defaultType: "textfield",  
												columnWidth:0.5,
												style: "padding-left:15px;margin-top:10px",
												labelWidth:125,
												items:[{
														fieldLabel:"Nombre Nivel 4",
														name:"txtnomnivel4",
														maxLength:200,
														minLength:1,
														allowBlank:false,
														id:"nomestpro4",
														width:200
													   },
													   {
														fieldLabel:"Nombre Nivel 5",
														name:"txtnomnivel5",
														maxLength:200,
														minLength:1,
														allowBlank:false,
														id:"nomestpro5",
														width:200
													   }]
											}] // Items de la Columna del FildSet de Cuentas de Resultados
															},
									{
									 layout:"column",
									 defaults: {columnWidth: ".5",border: false},
									 title:"Longitud del Codigo de la Estructura Presupuestaria",
									 items:[{
												layout:"form",
												border:false,
												defaultType: "numberfield",  
												columnWidth:0.33,
												style: "margin-top:10px;",
												labelWidth:100,
												items:[{
														fieldLabel:"Longitud Nivel 1",
														name:"txtlonnivel1",
														maxLength:2,
														minLength:1,
														allowBlank:false,
														allowNegative:false,
														id:"loncodestpro1",
														autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
														width:30
													   },
													   {
														fieldLabel:"Longitud Nivel 2",
														name:"txtlonnivel1",
														maxLength:2,
														minLength:1,
														allowBlank:false,
														allowNegative:false,
														id:"loncodestpro2",
														autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
														width:30
													   }]
											},
											{
												layout:"form",
												border:false,
												defaultType: "numberfield",  
												columnWidth:0.33,
												style: "margin-top:10px",
												labelWidth:125,
												items:[{
														fieldLabel:"Longitud Nivel 3",
														name:"txtlonnivel1",
														maxLength:2,
														minLength:1,
														allowBlank:false,
														allowNegative:false,
														id:"loncodestpro3",
														autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
														width:30
													   },
													   {
														fieldLabel:"Longitud Nivel 4",
														name:"txtlonnivel4",
														maxLength:2,
														minLength:1,
														allowBlank:false,
														allowNegative:false,
														id:"loncodestpro4",
														autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
														value:0,
														width:30
													   }]
											},
											{
												layout:"form",
												border:false,
												defaultType: "numberfield",  
												columnWidth:0.34,
												style: "margin-top:10px",
												labelWidth:125,
												items:[{
														fieldLabel:"Longitud Nivel 5",
														name:"txtlonnivel5",
														maxLength:2,
														minLength:1,
														allowBlank:false,
														allowNegative:false,
														id:"loncodestpro5",
														autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
														value:0,
														width:30
													   }]
										}] // Items de la Columna que Contiene la Configuracion de la Longitud de los Digitos de los Niveles Presupuestarios
								 }] // Items del FieldSet que contiene el FieldSet de Estructuras y Niveles Presupuestarios
							}] // Items del Accordion de Estructuras y Niveles Presupuestarios
				 }] // Items del Tab Cuentas de Resultado y Niveles Presupuestarios
	  },{
		  title:"Configuración General de Modulos",
		  layout:"accordion",
		  width:950,
		  height:600,
		  frame:true,
		  listeners:{
 				'beforeshow': function(componente){
 													if(Ext.getCmp('codemp').getValue() == "")
 													{
 														Ext.Msg.alert('Mensaje','Debe indicar el código de empresa, verifique por favor');
 														Ext.getCmp('tabempresa').activate('tabdefempresa');
 														return false;
 													}
 													else
 													{
 														return true;
 													}
										          }
              },
          layoutConfig:{  activeOnTop:false,
						  animate:true,
						  collapseFirst:true,
						  fill:true,
						  titleCollapse:true},
		  items:[{
				  title:"Contabilidad Presupuestaria de Gasto",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  layout:"form",
				  items:[{
							 layout:"column",
							 defaults: {columnWidth: "1",border: false},
							 items:[{
										layout:"form",
										border:false,
										defaultType: "textfield",  
										style: "margin-top:10px;margin-left:150px;",
										labelWidth:225,
										items:[{
												fieldLabel:"Codigo ONAPRE asignado a la Empresa",
												name:"txtcodasiona",
												maxLength:15,
												minLength:1,
												allowNegative:false,
												id:"codasiona",
												autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');"},
												width:150
											   }] // Items del Form que contiene al configuracion para el codigo de la Onapre
									}] // // Items de la Columna que contiene al configuracion para el codigo de la Onapre
			      		},
						{
							xtype:"fieldset",
							title: "Modalidad de la Apertura de Cuentas",
							height:50,
							labelWidth:60,
							border:true,
							style: "margin-top:10px;margin-left:165px;",
							width:450,
							items: [{
									 layout:"column",
									 defaults: {columnWidth: "1",border: false},
									 items:[{
							
												xtype: "radiogroup",
												style: 'margin-left: 50px',
												fieldLabel: "",
												labelSeparator:"",
												columns: [170, 200],
												id:"estmodape",
												items: [
														{boxLabel: 'Mensual', name: 'rbspgapertura', inputValue: 0},
														{boxLabel: 'Trimestral', name: 'rbspgapertura', inputValue: 1}
													   ]
											}] // Items del RadioGroup para el Tipo de Apertura
									}] // Items de la Columna para la Configuracion del Tipo de Apertura
            		   },
					   {	
					   		xtype:"fieldset",
							title: "Saldos Iniciales",
							height:50,
							labelWidth:60,
							border:true,
							style: "margin-top:10px;margin-left:30px;",
							width:650,
							items: [{
									 layout:"column",
									 defaults: {columnWidth: ".5",border: false},
									 items:[{
												layout:"form",
												border:false,
												defaultType: "textfield",  
												columnWidth:0.5,
												style: "margin-left:65px",
												labelWidth:75,
												items:[{
													fieldLabel:"Programado",
													name:"txtspgsaliniprogramado",
													maxLength:15,
													minLength:1,
													width:150,
													id:"salinipro",
													style: 'text-align:right',
													autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
													listeners:{
														'blur':function(objeto){
															var numero = objeto.getValue();
															var valorFormato = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
															objeto.setValue(valorFormato);
														},
														'focus':function(objeto){
															var numero = formatoNumericoEdicion(objeto.getValue());
															objeto.setValue(numero);
																	
														}
													}
												}]
											},
											{
												layout:"form",
												border:false,
												defaultType: "textfield",  
												columnWidth:0.5,
												style: "margin-left:25px",
												labelWidth:75,
												items:[{
														fieldLabel:"Ejecutado",
														name:"txtspgsaliniejecutado",
														maxLength:15,
														minLength:1,
														allowNegative:false,
														id:"salinieje",
														width:150,
														style: 'text-align:right',
														autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
														listeners:{
																	'blur':function(objeto)
																							{
																								var numero = objeto.getValue();
																									valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
																									objeto.setValue(valor);
																								
																							},
																	'focus':function(objeto)
																	{
																		var valor = formatoNumericoEdicion(objeto.getValue());
																			objeto.setValue(valor);
																		
																	}
												}
													   }]
											}] // Items de la Columna que Contiene la Configuracion de la Inicializacion de los Contadores para Compras
									}]
							},
							{
						  xtype: "checkboxgroup",
						  fieldLabel: "",
						  labelSeparator:"",
						  style: "margin-top:15px;margin-left:25px;",
						  labelWidth:200,
						  columns:[1],
						  vertical:true,
						  items: [
								  {boxLabel: "Contador de Compromiso", name: 'chkconcom', id:"estconcom", inputValue:'1'}
								 ] // Items del CheckGroup
				       },
					   {
							 layout:"column",
							 defaults: {columnWidth: "1",border: false},
							 items:[{
										layout:"form",
										border:false,
										defaultType: "textfield",  
										style: "margin-top:10px;margin-left:150px;",
										labelWidth:225,
										items:[{
												fieldLabel:"Numero Inicial",
												name:"txtnroinicom",
												maxLength:15,
												minLength:1,
												allowNegative:false,
												id:"nroinicom",
												autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"},
												width:150
											   }] // Items del Form que contiene al configuracion para el codigo de la Onapre
									}] // // Items de la Columna que contiene al configuracion para el codigo de la Onapre
			      		}] // Items del Accordion de Contabilidad Presupuestaria de Gasto
				 },
				 {
				  title:"Solicitud de Ejecución Presupuestaria",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  items:[{
						  xtype: "checkboxgroup",
						  fieldLabel: "",
						  labelSeparator:"",
						  style: "margin-top:15px;margin-left:25px;",
						  labelWidth:200,
						  columns:[1],
						  vertical:true,
						  items: [
								  {boxLabel: "Permitir Modificación de las Cuentas de Gasto en la Imputación Presupuestaria", name: 'chkseppermodctagasto', id:"estmodpartsep", inputValue:'1'}
								 ] // Items del CheckGroup
				       },
					   {
						  xtype: "checkboxgroup",
						  fieldLabel: "",
						  labelSeparator:"",
						  style: "margin-top:15px;margin-left:25px;",
						  labelWidth:200,
						  columns:[1],
						  vertical:true,
						  items: [
								  {boxLabel: "Permitir Aprobación de Documentos sin Disponibilidad Presupuestaria", name: 'chksepperdocsindisponibilidad', id:"estaprsep", inputValue:'1'}
								 ] // Items del CheckGroup
				       }] 
				 },
				 {
				  title:"Compras",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  items:[{
					  		xtype: "checkboxgroup",
					  		fieldLabel: "",
					  		labelSeparator:"",
					  		style: "margin-top:15px;margin-left:25px;",
					  		labelWidth:200,
					  		columns:[1],
					  		vertical:true,
					  		items: [{
					  					boxLabel: "Permitir Aprobación de Documentos sin Disponibilidad Presupuestaria", 
					  					id:"estaprsoc", 
					  					inputValue:'1'
					  				}
							 ] // Items del CheckGroup
			       		},{
							xtype:"fieldset",
							title: "Cuentas de Gasto",
							height:70,
							labelWidth:60,
							border:true,
							style: "margin-top:10px;",
							width:850,
							items: [{
									 layout:"column",
									 defaults: {columnWidth: ".5",border: false},
									 items:[{
												layout:"form",
												border:false,
												defaultType: "textfield",  
												columnWidth:0.4,
												style: "margin-left:75px;",
												labelWidth:50,
												items:[{
														fieldLabel:"Bienes",
														name:"txtctacombienes",
														maxLength:254,
														width:150,
														id:"soc_gastos",
														autoCreate: {tag: 'input', type: 'text', maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789,');"} 
												}]
											},
											{
												layout:"form",
												border:false,
												defaultType: "button",  
												columnWidth:0.1,
												items:[{
														iconCls: 'menubuscar',
														style:'padding-left:5px;',
														handler: function (){
															if(Ext.getCmp('codemp').getValue() == empresa['codemp']){
																catalogoCuentasspg('soc_gastos');
															}
															else {
																Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
															}
														}
												}]
											},
											{
												layout:"form",
												border:false,
												defaultType: "textfield",
												style: "margin-left:75px;",
												columnWidth:0.4,
												labelWidth:50,
												items:[{
														fieldLabel:"Servicios",
														name:"txtctacomservicios",
														maxLength:254,
														width:150,
														id:"soc_servic",
														autoCreate: {tag: 'input', type: 'text', maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789,');"}
													   }]
											},
											{
												layout:"form",
												border:false,
												defaultType: "button",  
												columnWidth:0.1,
												items:[{
														iconCls: 'menubuscar',
														style:'padding-left:5px;',
														handler: function (){
															if(Ext.getCmp('codemp').getValue() == empresa['codemp']){
																catalogoCuentasspg('soc_servic');
															}
															else {
																Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
															}
														}
												}]
											}] // Items de la Columna del FildSet de Cuentas de Resultados
									},{
										 layout:"column",
										 defaults: {border: false},
										 items:[{
													layout:"form",
													border:false,
													columnWidth:0.6,
													style: "margin-left:75px;",
													labelWidth:150,
													items:[{
														    xtype:'textfield',
														    fieldLabel:"Capitalización del IVA",
															maxLength:100,
															width:150,
															id:"parcapiva",
															readOnly: true
														   }]
												},
												{
													layout:"form",
													border:false,
													defaultType: "button",  
													columnWidth:0.1,
													items:[{
														iconCls: 'menubuscar',
														style:'padding-left:5px;',
														handler: function (){
															if(Ext.getCmp('codemp').getValue() == empresa['codemp']){
																if(empresa['capiva']=='1'){
																	//creando datastore y columnmodel para el catalogo de cuentas
																	var reCuenta = Ext.data.Record.create([
																		{name: 'spg_cuenta'},
																		{name: 'denominacion'}
																	]);
																	
																	var dsCuenta =  new Ext.data.Store({
																		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)
																	});
																						
																	var cmCuenta = new Ext.grid.ColumnModel([
																    	{header: "Código", width: 20, sortable: true,   dataIndex: 'spg_cuenta'},
																        {header: "Descripción", width: 40, sortable: true, dataIndex: 'denominacion'}
																    ]);
																	//fin creando datastore y columnmodel para el catalogo de cuentas
																	
																	var comCatalotoCuentaSPG = new com.sigesp.vista.comCatalogo({
																		titvencat: 'Catálogo de Cuentas Presupuestarias',
																		anchoformbus: 450,
																		altoformbus:130,
																		anchogrid: 450,
																		altogrid: 400,
																		anchoven: 500,
																		altoven: 400,
																		datosgridcat: dsCuenta,
																		colmodelocat: cmCuenta,
																		arrfiltro:[{etiqueta:'Código',id:'coage',valor:'spg_cuenta'},
																				   {etiqueta:'Descripción',id:'noage',valor:'denominacion'}],
																		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
																		parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catcuentaspg'}),
																		tipbus:'L',
																		setdatastyle:'T',
																		idTextfield:'parcapiva',
																		setCampo:'spg_cuenta'
																	});
																	
																	comCatalotoCuentaSPG.mostrarVentana();
																}
															}
															else {
																Ext.Msg.alert('Mensaje','Esta acción es válida dentro de la sesión activa de la empresa '+Ext.getCmp('codemp').getValue()+', verifique por favor');
															}
														}
													}]
												}]
										}] // Items del FieldSet de la Columna del FieldSet de Cuentas
						   },
						   {
							xtype:"fieldset",
							title: "Inicio de Contadores",
							height:70,
							labelWidth:60,
							border:true,
							style: "margin-top:10px;",
							width:850,
							items: [{
									 layout:"column",
									 defaults: {columnWidth: ".5",border: false},
									 items:[{
												layout:"form",
												border:false,
												defaultType: "numberfield",  
												columnWidth:0.5,
												style: "margin-top:10px;margin-left:25px;",
												labelWidth:125,
												items:[{
														fieldLabel:"Orden de Compra",
														name:"txtnomnivel1",
														maxLength:15,
														minLength:1,
														allowNegative:false,
														id:"numordcom",
														autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15'},
														width:150
													   }]
											},
											{
												layout:"form",
												border:false,
												defaultType: "numberfield",  
												columnWidth:0.5,
												style: "margin-top:10px",
												labelWidth:125,
												items:[{
														fieldLabel:"Orden de Servicio",
														name:"txtlonnivel1",
														maxLength:15,
														minLength:1,
														allowNegative:false,
														id:"numordser",
														autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15'},
														width:150
													   }]
											}] // Items de la Columna que Contiene la Configuracion de la Inicializacion de los Contadores para Compras
									}]
								 },
								 {
								  xtype: "checkboxgroup",
								  fieldLabel: "",
								  labelSeparator:"",
								  style: "margin-top:5px;margin-left:25px;",
								  labelWidth:200,
								  columns:[1],
								  vertical:true,
								  items: [
										  {boxLabel: "Permitir Modificación de las Cuentas de Gasto en la Imputacion Presupuestaria", name: 'chkscopermodctagasto',id:"estmodpartsoc", inputValue:1}
										 ] // Items del CheckGroup
				          		},
				          		{
								  xtype: "hidden",
								  labelSeparator:"",
								  style: "margin-top:5px;margin-left:25px;",
								  value:1,
								  id:"socbieser"
					          	}] // Items del Accordion Compras
				 },
				 {
				  title:"Cuentas por Pagar",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  items:[{
							xtype:"fieldset",
							title: "Inicio de Contador",
							height:45,
							labelWidth:60,
							border:true,
							style: "margin-top:5px;margin-left:125px",
							width:400,
							items: [{
									 layout:"column",
									 defaults: {border: false},
									 items:[{
												layout:"form",
												border:false,
												defaultType: "numberfield",  
												columnWidth:1,
												style: "margin-left:25px;",
												labelWidth:125,
												items:[{
														fieldLabel:"Solicitud de Pago",
														name:"txtlonnivel1",
														maxLength:15,
														minLength:1,
														allowNegative:false,
														id:"numsolpag",
														autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15'},
														width:150
													   }]
										}] // Items de la Columna que Contiene la Configuracion de la Inicializacion de los Contadores para Compras
								}]
							 },
							 {
			              xtype: "checkboxgroup",
			              fieldLabel: "",
			              style: 'margin-top:5px;margin-left: 20px',
			              columns: 2,
			              vertical:true,
			              autoWidth:false,
			              items: [
			                  {boxLabel: "Permitir Aprobación de Documentos sin Disponibilidad Presupuestaria", id:"estaprcxp", inputValue:1},    
			                  {boxLabel: "Permitir modificar IVA en Recepción de Documentos", name: 'chkcxpmodivarecdoc', id:"estmodiva", inputValue:1},
			                  {boxLabel: "Contabilizar Recepciones de Documento", name: 'chkcxpconrecdoc', disabled:true, id:"conrecdoc", inputValue:1},
			                  {boxLabel: "Permitir Cuenta Contable en Clasificador de Cuentas por Pagar", name: 'chkcxpctaconclacxp', id:"clactacon", inputValue:1},
			                  {boxLabel: "Consolidar Comprobantes de Retención", name: 'chkcxpconscompret', id:"", inputValue:1},
			                  {boxLabel: "Mostrar solo deducciones configuradas al Proveedor o Beneficiario", name: 'chkmosdedconfprvben', id:"dedconproben", inputValue:1},
			                  {boxLabel: "Validar Clasificador de Conceptos en Recepcion de Documentos", id:"valclacon", inputValue:1},
			                  {boxLabel: "Validar Compromisos de Compras y Servicios en Recepcion de Documentos", id:"valcomrd", inputValue:1},
			                  {boxLabel: "Permirtir Generar Comprobantes de Retencion de ISLR ", id:"estretislr", inputValue:1}
			              ] // Items del CheckGroup de Validaciones de las Cuentas por Pagar
			          },
					  {
						layout:"form",
						border:false,
						defaultType: "textfield",  
						columnWidth:0.5,
						style: "margin-left:15px;margin-top:5px",
						labelWidth:150,
						items:[{
								labelSeparator:'',
								fieldLabel:"Base de Datos Integradora",
								name:"txtscobdintegradora",
								maxLength:100,
								autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_ ');"},
								id:"basdatcmp",
								width:150
							   }]  
					  },
					  comcampocatctacon.fieldsetCatalogo,
					  {
							layout:"form",
							border:false,
							style: "margin-left:15px;margin-top:30px",
							labelWidth:200,
							items:[ComboTipo]  
						  }
					  ]// Items del Accordion de Cuentas por Pagar 
				 },
				 {
				  title:"Caja y Banco",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  autoScroll : true,
				  items:[{
							layout:"form",
							border:false,
							defaultType: "textfield",  
							columnWidth:0.5,
							style: "margin-left:75px;margin-top:20px",
							labelWidth:200,
							items:[{
									fieldLabel:"Caducidad del Cheque (en dias)",
									name:"txtscbcadcheq",
									maxLength:50,
									id:"diacadche",
									autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3', onkeypress: "return keyRestrict(event,'0123456789');"},
									width:50
								   }]  
					  },
					  {
						 layout:"column",
						 defaults: {columnWidth: "1",border: false},
						 items:[{
									layout:"form",
									border:false,
									labelWidth:150,
									style: 'margin-top:5px;margin-left: 75px',
				                    items:[{
											xtype: "radiogroup",
											fieldLabel: "Generación de Cheques",
											columns: [125, 125],
											id:"confi_ch",
											items: [
													{boxLabel: "Manual", name: "rbscbgenchq", inputValue: 0},
													{boxLabel: "Automatica", name: "rbscbgenchq", inputValue: 1}
												   ]
										   }]
								}] // Items del RadioGroup para el Tipo de Apertura
					 },
					 {
					  xtype: "checkboxgroup",
					  fieldLabel: "",
					  labelSeparator:"",
					  style: "margin-top:5px;margin-left:75px;",
					  labelWidth:200,
					  columns:[1],
					  vertical:true,
					  items: [
							  {boxLabel: "Uso del Casamiento Conceptos Movimiento con Cuentas Bancarias", name: 'chkscbcasmovctaban', id:"casconmov", inputValue:1}
							 ] // Items del CheckGroup
				     },
				     {
						  xtype: "checkboxgroup",
						  fieldLabel: "",
						  labelSeparator:"",
						  style: "margin-top:5px;margin-left:75px;",
						  labelWidth:200,
						  columns:[1],
						  vertical:true,
						  items: [
								  {
									  boxLabel: "Uso del Numero de Referencia para Carta Orden",
									  name:"numrefcarord",
									  id:"numrefcarord", 
									  inputValue:1
								  }
								 ] // Items del CheckGroup
					 },
					 {
						  xtype: "checkboxgroup",
						  fieldLabel: "",
						  labelSeparator:"",
						  style: "margin-top:5px;margin-left:75px;",
						  labelWidth:200,
						  columns:[1],
						  vertical:true,
						  items: [
								  {
								   boxLabel: "Uso de Número de Control Interno Para los Movimientos de Banco", 
								   name: 'chkscbvalinimovban', 
								   id:"valinimovban", 
								   inputValue:1,
								   listeners:{
									  			'check' : function(componente,check)
											  			  {
											  				if(check)
											  				{
											  					Ext.getCmp('contintmovban').enable();
											  				}
											  				else
											  				{
											  					Ext.getCmp('contintmovban').disable();
											  				}
											  			  }
								             }
								  }
								 ] // Items del CheckGroup
					},
					{
						layout:"form",
						border:false,
						defaultType: "numberfield",  
						columnWidth:0.5,
						style: "margin-left:75px;margin-top:5px",
						labelWidth:350,
						items:[{
								fieldLabel:"Valor Inicial del Contador Para el Número de Control Interno",
								name:"numinicontban",
								disabled:true,
								maxLength:40,
								id:"contintmovban",
								autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
								width:40,
								allowNegative:false
							   }]  
				  },
				  {
						layout:"form",
						border:false,
						columnWidth:0.5,
						title:'Carta Orden',
						style: "margin-left:75px;margin-top:5px",
						labelWidth:350,
						items:[{
							xtype: 'hidden',
							id: 'parche'}] 
				  },
				  comcampocatbene.fieldsetCatalogo,
				  {
						layout:"form",
						border:false,
						columnWidth:1,
						style: "margin-left:90px;margin-top:50px;",
						labelWidth:125,
						items:[{
								xtype : 'textfield',
								labelSeparator : '',
							    fieldLabel:"Cuenta Beneficiario",
								id:"scctaben",
								width:150,
								readOnly:true
							   }]
				  }] 
				 },
				 {
				  title:"Activos Fijos",
				  iconCls :'bmenuagregar',
				  collapsed : true,
				  items:[{
						    xtype:"fieldset",
							title: "Datos SIGECOF",
							height:50,
							labelWidth:100,
							border:true,
							style: "margin-top:10px;margin-left:150px",
							width:400,
							items: [{						 
										layout:"form",
										border:false,
										defaultType: "textfield",  
										columnWidth:1,
										style: "margin-top:5px;margin-left:35px",
										labelWidth:125,
										items:[{
												fieldLabel:"Codigo del Organismo",
												name:"txtlonnivel1",
												maxLength:5,
												id:"codorgsig",
												autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '5',onkeypress: "return keyRestrict(event,'0123456789');"},
												width:80
											   }]
									}]
						 }] // Items del Accordion de Activos Fijos
				 },{
					  title:"Obras",
					  iconCls :'bmenuagregar',
				  	  collapsed : true,
					  items:[{
			              xtype: "checkboxgroup",
			              fieldLabel: "",
			              style: 'margin-top:5px;margin-left: 20px',
			              columns: 1,
			              vertical:true,
			              autoWidth:false,
			              items: [
			                  {boxLabel: "Permitir Manejo de Compromisos", id:"estcomobr", inputValue:1}    
			              ] // Items del CheckGroup de Validaciones de las Cuentas por Pagar
			          }]
				 },{
					  title:"Seguridad",
					  iconCls :'bmenuagregar',
				      collapsed : true,
					  items:[{						 
								layout:"form",
								border:false,
								style: "margin-top:5px;margin-left:15px",
								labelWidth:250,
								items:[{
										xtype : 'textfield',
										labelSeparator : '',
										fieldLabel:"Tiempo Maximo de Inactividad (Minutos)",
										id:"tiesesact",
										width:80,
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789,');"}
										
								}]},{						 
								layout:"form",
								border:false,
								style: "margin-top:5px;margin-left:15px",
								labelWidth:250,
								items:[{
										xtype: "checkbox",
										labelWidth:200,
										labelSeparator:'',
										fieldLabel: "Bloqueo Usuario Clave Invalida Reiterada",
										id: 'blocon',
										inputValue: 1,
										listeners:{
											'check': function (checkbox, checked) {
												if(checked)
												{
													Ext.getCmp('intblocon').enable();
												}
												else{
													Ext.getCmp('intblocon').setValue('0');
													Ext.getCmp('intblocon').disable();
												}
											 }
										}
								}]},{						 
								layout:"form",
								border:false,
								style: "margin-top:5px;margin-left:15px",
								labelWidth:250,
								items:[{
										xtype : 'textfield',
										labelWidth:200,
										labelSeparator : '',
										fieldLabel:"Numero Maximo de Clave Invalida",
										id:"intblocon",
										width:50,
										disabled:true,
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]}]
				 }] // Items del TabPanel de Configuracion General de Modulos
	  },
	  {
		title:"Configuración General de Procesos",
		layout:"accordion",
		width:950,
		height:380,
		frame:true,
		listeners:{
				'beforeshow': function(componente){
													if(Ext.getCmp('codemp').getValue() == "")
													{
														Ext.Msg.alert('Mensaje','Debe indicar el código de empresa, verifique por favor');
														Ext.getCmp('tabempresa').activate('tabdefempresa');
														return false;
													}
													else
													{
														return true;
													}
										          }
            },
		layoutConfig:{activeOnTop:false,
					  animate:true,
					  collapseFirst:true,
					  fill:true,
					  titleCollapse:true},
		items:[{
			    title:"Generación del Comprobante de Impuesto Municipal",
			    iconCls :'bmenuagregar',
				collapsed : true,
				items:[{
						xtype: "radiogroup",
						style: 'margin-top:20px;margin-left: 150px',
						fieldLabel: "",
						labelSeparator:"",
						id:"modageret",
						columns: [250, 250],
						items: [
								{boxLabel: "Modulo de Cuentas por Pagar", name: "rbcomimpmun", inputValue: 'C'},
								{boxLabel: "Modulo de Caja y Banco", name: "rbcomimpmun", inputValue: 'B'}
							   ]
            		  },
					  {
						layout:"form",
						border:false,
						defaultType: "numberfield",  
						columnWidth:0.5,
						style: "margin-left:75px;margin-top:20px",
						labelWidth:200,
						items:[{
								fieldLabel:"Valor Inicial del Contador",
								name:"txtcomimpmuncont",
								maxLength:6,
								id:"concommun",
								autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '6'},
								allowNegative:false,
								width:75
							   }]  
					  }]
			   },
			   {
			    title:"Generación del Comprobante de IVA e ISLR",
			    iconCls :'bmenuagregar',
				collapsed : true,
				items:[{
						xtype: "radiogroup",
						style: 'margin-top:20px;margin-left: 150px',
						fieldLabel: "",
						labelSeparator:"",
						columns: [250, 250],
						id:"estretiva",
						items: [
								{boxLabel: "Modulo de Cuentas por Pagar", name: "rbcomimpiva", inputValue: 'C'},
								{boxLabel: "Modulo de Caja y Banco", name: "rbcomimpiva", inputValue: 'B'}
							   ]
            		  },
					  {
						layout:"form",
						border:false,
						defaultType: "numberfield",  
						columnWidth:0.5,
						style: "margin-left:75px;margin-top:20px",
						labelWidth:200,
						items:[{
								fieldLabel:"Valor Inicial del Contador (IVA)",
								name:"txtcomimpivacont",
								maxLength:6,
								id:"concomiva",
								autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '6'},
								allowNegative:false,
								width:75
							   },
							   {
								fieldLabel:"Valor Inicial del Contador (ISLR)",
								name:"txtvaliniislr",
								maxLength:6,
								id:"valiniislr",
								autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '6'},
								allowNegative:false,
								width:75
							   },
							   {
				                xtype: "checkboxgroup",
				                fieldLabel: "",
				                labelSeparator:"",
							    labelWidth:15,
				                columns:[345],
							    vertical:true,
				                items: [{boxLabel: "Permitir procesar mas de 10 Retenciones", name: 'chkestcanret', id:"estcanret", inputValue:1}]	 // Items del CheckGroup
				          	   }]  
					  }]
			   },
			   {
				    title:"Generación del Comprobante de 1x1000",
				    iconCls :'bmenuagregar',
				    collapsed : true,
					items:[{
							xtype: "radiogroup",
							style: 'margin-top:20px;margin-left: 150px',
							fieldLabel: "",
							labelSeparator:"",
							columns: [250, 250],
							id:"estretmil",
							items: [
									{boxLabel: "Modulo de Cuentas por Pagar", name: "rbcomimpiva", inputValue: 'C'},
									{boxLabel: "Modulo de Caja y Banco", name: "rbcomimpiva", inputValue: 'B'}
								   ]
	            		  },
						  {
							layout:"form",
							border:false,
							defaultType: "numberfield",  
							columnWidth:0.5,
							style: "margin-left:75px;margin-top:20px",
							labelWidth:200,
							items:[{
									fieldLabel:"Valor Inicial del Contador 1x1000",
									name:"txtcommil",
									maxLength:6,
									id:"concommil",
									autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '6'},
									allowNegative:false,
									width:75
								   }]  
						  }]
			   },
			   {
			    title:"Manejo del IVA",
			    iconCls :'bmenuagregar',
				collapsed : true,
				items:[{
						layout:"form",
						border:false,
						columnWidth:0.5,
						style: "margin-left:85px;margin-top:20px",
						items:[{
							xtype: "checkbox",
							labelSeparator:'',
							fieldLabel: "Centralizar IVA",
							id: 'estceniva',
							inputValue: 1
						}]
						},{
						xtype: "radiogroup",
						style: 'margin-top:20px;margin-left: 150px',
						fieldLabel: "",
						labelSeparator:"",
						atributo:"",
						id:"confiva",
						columns: [250, 250],
						items: [
						        {boxLabel: "Iva Presupuestario", name: "rbtipmaniva", inputValue: "P"},
								{boxLabel: "Iva Contable", name: "rbtipmaniva", inputValue: "C"}
							   ]
            		  }]
			   },
			   {
			    title:"Configuración de los Instructivos de la ONAPRE",
			    iconCls :'bmenuagregar',
				collapsed : true,
				items:[{
						layout:"form",
						border:false, 
						columnWidth:1,
						style: 'margin-top:20px;margin-left: 175px',
						labelWidth:75,
						items:[{
								xtype: "radiogroup",
								fieldLabel: "Instructivo",
								atributo:"",
								columns: [100,100,100],
								vertical:false,
								id:"confinstr",
								items: [
										{boxLabel: "2007",  name: "rbconinsonapre", inputValue: "V"},
										{boxLabel: "2008",  name: "rbconinsonapre", inputValue: "N"},
										{boxLabel: "Ambos", name: "rbconinsonapre", inputValue: "A"}
									   ]
							   }]
            		  }] // Items del Accordion de los Instructivos de la ONAPRE
			   }] // Items del TabPanel de Configuracion General de Procesos			  				  
	  }] // Items del TabPanel
	 }] // Items del Panel
	});
		formempresa.render("formulario_Empresa");
		
});	//fin del archivo

function irNuevo()
{
	limpiarFormulario(formempresa);
	formempresa.findById('tabempresa').activate('tabdefempresa');
	var myJSONObject ={
		"oper":"nuevo"
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codemp').setValue(codigo);
		}
	}	
	});
	Ext.getCmp('tabempresa').activate('tabdefempresa');
	Ext.getCmp('nombre').setValue('SIGESP CA');
	Ext.getCmp('titulo').setValue('Sigesp CA');
	Ext.getCmp('sigemp').setValue('SIGESP');
	Ext.getCmp('direccion').setValue('Urbanizacion Del Este');
	Ext.getCmp('telemp').setValue('02512547643');
	Ext.getCmp('faxemp').setValue('02512547643');
	Ext.getCmp('email').setValue('sigesp@gmail.com');
	Ext.getCmp('website').setValue('sigespweb@sigesp.com');
	Ext.getCmp('m01').setValue(true);
	Ext.getCmp('m02').setValue(true);
	Ext.getCmp('m03').setValue(true);
	Ext.getCmp('m04').setValue(true);
	Ext.getCmp('m05').setValue(true);
	Ext.getCmp('m06').setValue(true);
	Ext.getCmp('m07').setValue(true);
	Ext.getCmp('m08').setValue(true);
	Ext.getCmp('m09').setValue(true);
	Ext.getCmp('m10').setValue(true);
	Ext.getCmp('m11').setValue(true);
	Ext.getCmp('m12').setValue(true);
	Ext.getCmp('periodo').setValue(fecha.getFullYear()+'-01-01');    // Periodo Fiscal
	Ext.getCmp('vali_nivel').setValue(1);                            // Nivel de Validacion Presupuesatario
	Ext.getCmp('esttipcont').items.items[0].setValue(true);          // Tipo de Contabilidad - Contabilidad Patrimonial
	Ext.getCmp('formpre').setValue('999-99-99-99');                  // Formato del Presupuesto de Gasto
	Ext.getCmp('formcont_1').setValue('9-9-9-99-99-99-');         // Formato del Presupuesto Contable
	Ext.getCmp('formcont_2').setValue('999');                        // Formato del Presupuesto Contable
	Ext.getCmp('formplan').setValue('9-9-9-99-99-99');               // Formato del Plan de Cuentas
	Ext.getCmp('formspi').setValue('999-99-99-99');                  // Formato del Presupuesto de Ingreso
	Ext.getCmp('activo').setValue('1');                              // Digito Contable para representar los Activos
	Ext.getCmp('pasivo').setValue('2');                              // Digito Contable para representar los Pasivos
	Ext.getCmp('ingreso').setValue('3');                             // Digito Presupuestario para representar los Ingresos
	Ext.getCmp('gasto').setValue('4');                               // Digito Presupuestario para representar los Gastos
	Ext.getCmp('resultado').setValue('5');                           // Digito Contable para representar los Resultados
	Ext.getCmp('capital').setValue('7');                             // Digito Contable para representar el Capital
	Ext.getCmp('c_resultad').setValue('5010201000000');
	Ext.getCmp('c_resultan').setValue('5010201000000');
	Ext.getCmp('orden_d').setValue('1');
	Ext.getCmp('orden_h').setValue('2');
	Ext.getCmp('soc_gastos').setValue('');
	Ext.getCmp('soc_servic').setValue('');
	Ext.getCmp('activo_h').setValue('11');
	Ext.getCmp('pasivo_h').setValue('22');
	Ext.getCmp('resultado_h').setValue('12');
	Ext.getCmp('ingreso_f').setValue('1');
	Ext.getCmp('gasto_f').setValue('2');
	Ext.getCmp('ingreso_p').setValue('3');
	Ext.getCmp('gasto_p').setValue('4');
	Ext.getCmp('numniv').setValue(3);
	Ext.getCmp('nomestpro1').setValue('Proyecto y/o Acciones Centralizadas');
	Ext.getCmp('nomestpro2').setValue('Acciones Especificas');
	Ext.getCmp('nomestpro3').setValue('Otros.');
	Ext.getCmp('nomestpro4').setValue('No tiene');
	Ext.getCmp('nomestpro5').setValue('No tiene');
	Ext.getCmp('nomestpro1').enable();
	Ext.getCmp('nomestpro2').enable();
	Ext.getCmp('nomestpro3').enable();
	Ext.getCmp('nomestpro4').disable();
	Ext.getCmp('nomestpro5').disable();
	Ext.getCmp('loncodestpro1').enable();
	Ext.getCmp('loncodestpro2').enable();
	Ext.getCmp('loncodestpro3').enable();
	Ext.getCmp('loncodestpro4').disable();
	Ext.getCmp('loncodestpro5').disable();
	Ext.getCmp('estmodape').items.items[0].setValue(true);
	Ext.getCmp('codorgsig').setValue('');
	Ext.getCmp('salinipro').setValue(0);
	Ext.getCmp('salinieje').setValue(0);
	Ext.getCmp('numordcom').setValue(0);
	Ext.getCmp('numordser').setValue(0);
	Ext.getCmp('numsolpag').setValue(0);
	Ext.getCmp('estmodest').items.items[0].setValue(true);
	Ext.getCmp('numlicemp').setValue('0000000000000000000000000');
	Ext.getCmp('modageret').setValue('B');
	Ext.getCmp('socbieser').setValue(1);
	Ext.getCmp('concomiva').setValue('');
	Ext.getCmp('valiniislr').setValue('');
	Ext.getCmp('estcanret').setValue(false);		
	Ext.getCmp('estmodiva').setValue(false);
	Ext.getCmp('diacadche').setValue('');
	Ext.getCmp('nroivss').setValue('');
	Ext.getCmp('nomrep').setValue('');
	Ext.getCmp('cedrep').setValue('');
	Ext.getCmp('telfrep').setValue('');
	Ext.getCmp('cargorep').setValue('');
	Ext.getCmp('estretiva').items.items[1].setValue(true);
	Ext.getCmp('confinstr').items.items[1].setValue(true);
	Ext.getCmp('esttipcont').enable();          
	Ext.getCmp('formpre').enable();                  
	Ext.getCmp('formcont_1').enable();
	Ext.getCmp('formcont_2').enable();
	Ext.getCmp('formplan').enable();              
	Ext.getCmp('formspi').enable();
	Ext.getCmp('numniv').enable();
	Ext.getCmp('esttipcont').enable();
	Ext.getCmp('estmodape').enable();
	Ext.getCmp('estmodest').enable();
	Ext.getCmp('inicencos').setValue('0');
	Ext.getCmp('fincencos').setValue('0');
	Ext.getCmp('tiesesact').setValue('5000');
	Ext.getCmp('intblocon').setValue('3');
	Ext.getCmp('estciesem').setValue('0');
	Ext.getCmp('estceniva').setValue('0');
	
	
}

function validarDatos(arrdatos){
	valido = true;
	for(var i=0;i<arrdatos.length;i++){
		var componente = Ext.getCmp(arrdatos[i]);
		if(componente.getValue()==''){
			Ext.Msg.alert('Advertencia', 'Debe llenar el campo '+componente.fieldLabel);
			return false;
		}				
	}
	
	return valido;
}

function generarJsonEmpresa(operacion)
{
	strJson="{'oper':'"+operacion+"'";
	for(i=0;i<Campos.length;i++)
	{
		valor='';
		if (Ext.getCmp(Campos[i][0]) != null)
		{
			
			switch(Ext.getCmp(Campos[i][0]).getXType())
			{
				case 'radiogroup':
								 for( var j=0; j < Ext.getCmp(Campos[i][0]).items.length; j++ ) 
								 {
									if (Ext.getCmp(Campos[i][0]).items.items[j].checked)
									{
										valor = Ext.getCmp(Campos[i][0]).items.items[j].inputValue;
										break;
									}
								 }
								  if(typeof(Ext.getCmp(Campos[i][0]).items.items[0].inputValue) == 'number')
								  {
									  if(valor == '')
									 {
									  valor = 0;
									  strJson=strJson+",'"+Campos[i][0]+"':"+valor+"";
									 }
									 else
									 {
									  strJson=strJson+",'"+Campos[i][0]+"':"+valor+""; 
									 }
								  }
								  else
								  {
									  strJson=strJson+",'"+Campos[i][0]+"':'"+valor+"'"; 
								  }
				                 break;
							 
				case 'checkbox':
								if (Ext.getCmp(Campos[i][0]).checked)
								{
									valor = Ext.getCmp(Campos[i][0]).inputValue;
								}
								else
								{
									if(typeof(Ext.getCmp(Campos[i][0]).inputValue)== 'number')
									{
										valor = 0;
									}
									else{
										valor = '0';
									}
								}
								
								if(typeof(valor)== 'number')
								{
									strJson=strJson+",'"+Campos[i][0]+"':"+valor+"";	
								}
								else
								{
									strJson=strJson+",'"+Campos[i][0]+"':'"+valor+"'";
								}
							    
							    break;
								
			   case 'textfield':
								var cadena  = Ext.getCmp(Campos[i][0]).getValue();
								var cadfinal = '';
								for(j=0;j<cadena.length;j++)
								{
									letra = cadena.substr(j,1);
									cod = escape(letra);
									if(cod=='%0A')
									{
										letra='|';	
									}
								cadfinal=cadfinal+letra;
								}
							    strJson=strJson+",'"+Campos[i][0]+"':'"+cadfinal+"'";
							    break;
								
			  case 'combo':
								valor = Ext.getCmp(Campos[i][0]).getValue();
								if(valor != '')
								{
									if(typeof(valor)== 'number')
									{
										strJson=strJson+",'"+Campos[i][0]+"':"+valor+"";
									}
									else
									{
									 strJson=strJson+",'"+Campos[i][0]+"':'"+valor+"'";	
									}
								}
								else
								{
									valor=3; // Solo para el Nivel de Validacion y Niveles Presupuestarios
									strJson=strJson+",'"+Campos[i][0]+"':"+valor+"";
								}
							    break;
								
			 case 'datefield':
								valor = Ext.util.Format.date(Ext.getCmp(Campos[i][0]).getValue(),'d/m/Y');
								strJson=strJson+",'"+Campos[i][0]+"':'"+valor+"'";	
							    break;
								
			case 'numberfield':
								valor = Ext.getCmp(Campos[i][0]).getValue();
								if(valor == '')
								{
								 valor = 0;
								}
								strJson=strJson+",'"+Campos[i][0]+"':"+valor+"";	
							    break;
			}
		}
	}
	var formatocont = Ext.getCmp('formcont_1').getValue()+Ext.getCmp('formcont_2').getValue();	
	strJson=strJson+",'codmenu':"+codmenu+",'dirvirtual':'"+dirvirtual+"',formcont:'"+formatocont+"'}";
	strJson = strJson.replace("&","");
	return strJson;
}

function validarIvaConfigurado(empresa)
{
	var objetoJson ={
			"oper":"validariva",
			"codemp":empresa
		};
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var resultado = eval('(' + datos + ')');
			if(resultado != "")
			{
			 if(parseInt(resultado.totalcargos)>0)
			 {
				 Ext.getCmp('confiva').disable();
			 } 
			 else
			 {
				 Ext.getCmp('confiva').enable(); 
			 }
			}
		}	
		})
}

function validarFormatoCuentasIngreso(empresa)
{
	var objetoJson ={
			"oper":"validarformatospi",
			"codemp":empresa
		};
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var resultado = eval('(' + datos + ')');
			if(resultado != "")
			{
			 if(parseInt(resultado.totalcuentasingreso)>0)
			 {
				 Ext.getCmp('formspi').disable();
			 } 
			 else
			 {
				 Ext.getCmp('formspi').enable(); 
			 }
			}
		}	
		})
}

function validarFormatoCuentasGasto(empresa)
{
	var objetoJson ={
			"oper":"validarformatospg",
			"codemp":empresa
		};
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var resultado = eval('(' + datos + ')');
			if(resultado != "")
			{
			 if(parseInt(resultado.totalcuentasgasto)>0)
			 {
				 Ext.getCmp('formpre').disable();
			 } 
			 else
			 {
				 Ext.getCmp('formpre').enable(); 
			 }
			}
		}	
		})
}

function validarFormatoCuentasContables(empresa)
{
	var objetoJson ={
			"oper":"validarformatoscg",
			"codemp":empresa
		};
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var resultado = eval('(' + datos + ')');
			if(resultado != "")
			{
			 if(parseInt(resultado.totalcuentascontables)>0)
			 {
				 Ext.getCmp('esttipcont').disable();
				 Ext.getCmp('formcont_1').disable();
				 Ext.getCmp('formcont_2').disable();
				 Ext.getCmp('formplan').disable();
			 } 
			 else
			 {
				 Ext.getCmp('esttipcont').enable();
				 Ext.getCmp('formcont_1').enable();
				 Ext.getCmp('formcont_2').enable();
				 Ext.getCmp('formplan').enable();
			 }
			}
		}	
		})
}

function validarEstructuras(empresa) {
	var objetoJson = {
			"oper":"validarestructuras",
			"codemp":empresa
	};
	var	ObjSon=Ext.util.JSON.encode(objetoJson);
	var parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) {
			var datos = result.responseText;
			var respuesta = datos.split("|");
			var resultado = eval('(' + respuesta[1] + ')');
			if(resultado != "") {
				if(parseInt(resultado.totalestructura)>0) {
					Ext.getCmp('estmodest').disable();
					Ext.getCmp('numniv').disable();
					Ext.getCmp('nomestpro1').disable();
					Ext.getCmp('nomestpro2').disable();
					Ext.getCmp('nomestpro3').disable();
					Ext.getCmp('nomestpro4').disable();
					Ext.getCmp('nomestpro5').disable();
					Ext.getCmp('loncodestpro1').disable();
					Ext.getCmp('loncodestpro2').disable();
					Ext.getCmp('loncodestpro3').disable();
					Ext.getCmp('loncodestpro4').disable();
					Ext.getCmp('loncodestpro5').disable();
				} 
			 	else {
			 		Ext.getCmp('estmodest').enable(); 
					Ext.getCmp('numniv').enable();
					validaNivelesEst(respuesta[0])
			 	}
			}
		}	
	});
	
}

function validarApertura(empresa)
{
	var objetoJson ={
			"oper":"verificarapertura",
			"codemp":empresa
		};
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var resultado = eval('(' + datos + ')');
			if(resultado != "")
			{
			 if(parseInt(resultado.totalapertura)>0)
			 {
				 Ext.getCmp('estmodape').disable();
			 } 
			 else
			 {
				 Ext.getCmp('estmodape').enable(); 
			 }
			}
		}	
		})
}

function irGuardar()
{
	arrdatos = new Array('c_resultad','c_resultan');
	
	if(validarDatos(arrdatos)){
		obtenerMensaje('procesar','','Guardando Datos');
		if(Actualizar)
		{
			var arregloObjeto = generarJsonEmpresa('actualizar');
			var jsonEmpresa= eval('(' + arregloObjeto + ')');
			var ObjSon=Ext.util.JSON.encode(jsonEmpresa);
			var parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function ( resultado, request )
			{ 
				Ext.Msg.hide();
				datos = resultado.responseText;
				var respuesta = datos.split("|");
				if(respuesta[1] == '1') {
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Registro actualizado exitosamente',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
					formempresa.findById('formcont_1').reset();
        			formempresa.findById('formcont_2').reset();
					limpiarCampos();
					formempresa.findById('tabempresa').activate('tabdefempresa');
				}
				else
				{
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Ha ocurrido un error en el proceso de actualización',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});	
				}
			  },
			failure: function ( result, request)
			 { 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operación, por favor intente de nuevo');
				} 
			  });
		}
		else
		{
			var arregloObjeto = generarJsonEmpresa('incluir');
			var jsonEmpresa= eval('(' + arregloObjeto + ')');
			var ObjSon=Ext.util.JSON.encode(jsonEmpresa);
			var parametros = 'ObjSon='+ObjSon;
			Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function ( resultado, request )
			{ 
				Ext.Msg.hide();
				datos = resultado.responseText;
				var respuesta = datos.split("|");
				if(respuesta[1] == '1')
				{
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro incluido exitosamente',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
					formempresa.findById('formcont_1').reset();
        			formempresa.findById('formcont_2').reset();
					limpiarCampos();
					formempresa.findById('tabempresa').activate('tabdefempresa');
				}
				else
				{
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Ha ocurrido un error en el proceso de registro',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
					Ext.Msg.hide();
				}
			  },
			failure: function ( result, request)
			 { 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operación, por favor intente de nuevo');
				Ext.Msg.hide();
			 } 
		  });
		}
	}
}

function ejecutarEliminacion(btn)
{
	obtenerMensaje('procesar','','Eliminando Datos');
	if(btn=='yes')
	 {
			if(empresa['codemp'] == Ext.getCmp('codemp').getValue())
			 {
				 Ext.Msg.show({
						title:'Mensaje',
						msg: 'No puede eliminar la empresa que se encuentra activa, debe cambiar de sesión de empresa, verifique por favor',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR,
						closable:false
					}); 
			 }
			 else
			 {
				    var arregloObjeto = generarJsonEmpresa('eliminar');
					var jsonEmpresa= eval('(' + arregloObjeto + ')');
					var ObjSon=Ext.util.JSON.encode(jsonEmpresa);
					var parametros = 'ObjSon='+ObjSon;
					myMask.show();
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( resultado, request )
					{ 
						Ext.Msg.hide();
						datos = resultado.responseText;
						var respuesta = datos.split("|");
						if(respuesta[1] == '1')
						{
							Ext.Msg.show({
											title:'Mensaje',
											msg: 'Registro eliminado exitosamente',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
										});
							formempresa.findById('formcont_1').reset();
		        			formempresa.findById('formcont_2').reset();
							limpiarCampos();
							formempresa.findById('tabempresa').activate('tabdefempresa');
						}
						else
						{
							var respuesta = eval('('+resultado.responseText+')');
							if(respuesta.mensaje != null)
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: respuesta.mensaje[0],
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}
							else
							{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Ha ocurrido un error en el proceso de eliminación',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}				
						}
					  },
					failure: function ( result, request)
					{ 
						Ext.MessageBox.alert('Error','Ha ocurrido un error en la operación, por favor intente de nuevo');
						Ext.Msg.hide();
					} 
			     });
			 }
	 }
}

function irEliminar()
{	
 if((Ext.getCmp('codemp').getValue() != "")&&(Actualizar==true))
 {
	 Ext.MessageBox.confirm('Confirmar', '¿Desea eliminar este registro?', ejecutarEliminacion);
 }
 else
 {
	 
	 Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe seleccionar un registro válido, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR,
			closable:false
		}); 
 }
}

function validaNivelesEst(numniv){
	switch(numniv){
		case '1':
			Ext.getCmp('nomestpro1').enable();
			Ext.getCmp('nomestpro2').disable();
			Ext.getCmp('nomestpro3').disable();
			Ext.getCmp('nomestpro4').disable();
			Ext.getCmp('nomestpro5').disable();
			Ext.getCmp('loncodestpro1').enable();
			Ext.getCmp('loncodestpro2').disable();
			Ext.getCmp('loncodestpro3').disable();
			Ext.getCmp('loncodestpro4').disable();
			Ext.getCmp('loncodestpro5').disable();
			break;
		case '2':
			Ext.getCmp('nomestpro1').enable();
			Ext.getCmp('nomestpro2').enable();													
			Ext.getCmp('nomestpro3').disable();
			Ext.getCmp('nomestpro4').disable();
			Ext.getCmp('nomestpro5').disable();
			Ext.getCmp('loncodestpro1').enable();
			Ext.getCmp('loncodestpro2').enable();
			Ext.getCmp('loncodestpro3').disable();
			Ext.getCmp('loncodestpro4').disable();
			Ext.getCmp('loncodestpro5').disable();
		  	break;
		case '3':
			Ext.getCmp('nomestpro1').enable();
			Ext.getCmp('nomestpro2').enable();													
			Ext.getCmp('nomestpro3').enable();
			Ext.getCmp('nomestpro4').disable();
			Ext.getCmp('nomestpro5').disable();
			Ext.getCmp('loncodestpro1').enable();
			Ext.getCmp('loncodestpro2').enable();
			Ext.getCmp('loncodestpro3').enable();
			Ext.getCmp('loncodestpro4').disable();
			Ext.getCmp('loncodestpro5').disable();
		  	break;
		case '4':
			Ext.getCmp('nomestpro1').enable();
			Ext.getCmp('nomestpro2').enable();													
			Ext.getCmp('nomestpro3').enable();
			Ext.getCmp('nomestpro4').enable()
			Ext.getCmp('nomestpro5').disable();
			Ext.getCmp('loncodestpro1').enable();
			Ext.getCmp('loncodestpro2').enable();
			Ext.getCmp('loncodestpro3').enable();
			Ext.getCmp('loncodestpro4').enable();
			Ext.getCmp('loncodestpro5').disable();
		  	break;
		case '5':
			Ext.getCmp('nomestpro1').enable();
			Ext.getCmp('nomestpro2').enable();													
			Ext.getCmp('nomestpro3').enable();
			Ext.getCmp('nomestpro4').enable()
			Ext.getCmp('nomestpro5').enable();
			Ext.getCmp('loncodestpro1').enable();
			Ext.getCmp('loncodestpro2').enable();
			Ext.getCmp('loncodestpro3').enable();
			Ext.getCmp('loncodestpro4').enable();
			Ext.getCmp('loncodestpro5').enable();
		  	break; 
	}
}

function irCancelar()
{	
	irNuevo();
}
