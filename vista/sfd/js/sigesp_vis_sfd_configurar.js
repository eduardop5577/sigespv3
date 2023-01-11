/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var panel      = '';
var actualizar = false;
var rutaProceso  =  '../../controlador/sfd/sigesp_ctr_sfd_configurar.php'; 

//Registro para el combo de tamaño de página
    var reTamanno = [ ['CARTA','LETTER'],
                    ['OFICIO','LEGAL'],
                    ['A4','A4'],
                    ['A5','A5'],
                    ['A6','A6'],
                ]; 
    // Arreglo que contiene los tamaños que se pueden usar

    var seTam = new Ext.data.SimpleStore({
        fields: ['den', 'cod'],
        data : reTamanno // Se asocian la información de la pag
    });          

    barraherramienta    = true;
    Ext.onReady(
    function()
    {
        Ext.QuickTips.init();
        Ext.Ajax.timeout=36000000000;
        Ext.form.Field.prototype.msgTarget = 'side';        
        
        //componentes del formulario
        Xpos = ((screen.width/2)-(450/2)); 
        Ypos = ((screen.height/2)-(450/2));
        
        panel = new Ext.FormPanel({
            title: '',
            bodyStyle:'padding:5px 5px 0px',
            width:500,
            height:260,    
            tbar: [],
            fileUpload: true,
            style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
            items: [{
                xtype:'fieldset',
                id:'fsConfigurar',
                title: 'Propiedades del Documento',
                collapsible: false,
                cls :'fondo',
                autoHeight:true,
                items :[{
                    store: seTam,
                    xtype: 'combo',
                    labelSeparator: '',
                    fieldLabel: 'Tamaño Página',
                    id: 'tamanno',
                    editable: false,
                    displayField:'den',
                    valueField:'cod',
                    typeAhead: true,
                    triggerAction: 'all',
                    mode: 'local',
                    emptyText: 'CARTA',
                    listWidth: 100,
                    width: 100,
                }]
            },{
                xtype:'fieldset',
                checkboxToggle:true,
                title: 'Firma Visible',
                autoHeight:true,
                cls :'fondo',
                collapsed: true,
                items :[{
                    xtype: "numberfield",
                    fieldLabel: "Distancia eje X",
                    name: "ubicFirmaX",
                    id:"ubicFirmaX",
                    autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '3'},
                    allowBlank:false,
                    alloweDecimal:false,
                    allowNegative:false
                },{
                    xtype:"numberfield",
                    fieldLabel:"Distancia eje Y",
                    name: "ubicFirmaY",
                    id:"ubicFirmaY",
                    autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '3'},
                    allowBlank:false,
                    alloweDecimal:false,
                    allowNegative:false
                },{
                    xtype:"checkbox",
                    fieldLabel:"Imagen visible ",
                    labelWidth:40,
                    name:"chImgVisible",
                    labelSeparator:'',
                    id:"chImgVisible",
                    width:100,
                    disabled:false,
                    inputValue:'1'
                },{ 
                    xtype: 'fileuploadfield',
                    fieldLabel: 'Imagen',
                    id: 'imagen',
                    labelSeparator:'',
                    allowBlank:false,
                    width: 330,
                    emptyText: 'Seleccione la imagen .png',
                    fileUpload: true,
                    buttonCfg:
                    {
                        text: '...'
                    }
                }
                ]
            }],
        buttons: [{
            text: 'Cargar'           
        },{
            text: 'Cancel'
        }]              
        })
        panel.render(document.body);
    })
    
/***********************************************************************************
* @Función para limpiar los campos.
* @parametros: 
* @retorno:
* @fecha de creación: 10/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/    
    function irCancelar()
    {
        
    }   
    
/***********************************************************************************
* @Función para procesar el movimiento inicial de existencias de inventario.
* @parametros: 
* @retorno:
* @fecha de creación: 15/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/                
    function irGuardar()
    {

    }