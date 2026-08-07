<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- pagina admin di visualizzazione/redisplay: le letture di superglobali servono al rendering del form; le mutazioni avvengono negli handler di admin.php, protetti da wp_verify_nonce.
/**
 * Gestione Atti.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */
if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- schermata admin di gestione atti (WP_List_Table + form CRUD/view): l'output e' markup fisso + label i18n del plugin (__() su stringhe letterali) + output di helper che generano markup (dropdown, azioni, celle). I value degli input sono escapati inline (esc_attr) e le stringhe JS via esc_js; i dati mostrati provengono da tabelle custom con input gia' sanitizzato/validato in fase di scrittura (audit sicurezza 4.9.x/4.10.1).

if (!class_exists('WP_List_Table')) {
 require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
}

class albopc_AdminTableAtti extends WP_List_Table
{
/*		 1 - in corso di validità 	"Correnti"
		 2 - scaduti				"Scaduti"	
		 3 - da pubblicare			"DaPubblicare"
		 4 - da cancellare			"Eliminare"
		 5 - cerca "Cerca" mr
*/
  public $stato_atti="Tutti";
  public $Atti_DaPubblicare; 
  public $Atti_Correnti; 
  public $Atti_Scaduti; 
  public $Atti_Eliminare; 
  public $Atti_Tutti; 
  public $Atti_Cerca;
  public $AzioneDefault;
  public $Cerca; /* mr */
 
  function Codstato_atti(){
  	switch ($this->stato_atti){
		case "Correnti":$Ret=1;break;
		case "Scaduti":$Ret=2;break;
		case "DaPubblicare":$Ret=3;break;
		case "Eliminare":$Ret=4;break;
		case "Cerca":$Ret=5;break; /* mr */
 		default: $Ret=0;break;
	}
	return $Ret;
  }

  function __construct() {
  	$this->Atti_DaPubblicare=albopc_get_all_atti(3,0,0,0,'', 0,0,"",0,0,true);
  	$this->Atti_Correnti=albopc_get_all_atti(1,0,0,0,'', 0,0,"",0,0,true); 
  	$this->Atti_Scaduti=albopc_get_all_atti(2,0,0,0,'', 0,0,"",0,0,true); 
  	$this->Atti_Eliminare=albopc_get_all_atti(4,0,0,0,'', 0,0,"",0,0,true);
  	$this->Atti_Tutti=albopc_get_all_atti(0,0,0,0,'', 0,0,"",0,0,true);
    $this->Atti_Cerca=albopc_get_all_atti(5,0,0,0,(isset($_REQUEST['s'])?sanitize_text_field(wp_unslash($_REQUEST['s'] ?? '')):""), 0,0,"",0,0,true);
    parent::__construct(array('singular'=>'Atto','plural'=>'Atti'));
  }

	function get_views() {
	    $status_links = array(
	        "Tutti"		  => "<a href='?page=atti&amp;stato_atti=Tutti'><strong>".__("Tutti","albo-pretorio-considera")." (".$this->Atti_Tutti.")</strong></a>",
	        "nuovi"       => "<a href='?page=atti&amp;stato_atti=Nuovi'>".__("da Pubblicare","albo-pretorio-considera")."(".$this->Atti_DaPubblicare.")</a>",
	        "correnti"    => "<a href='?page=atti&amp;stato_atti=Correnti'>".__("Correnti","albo-pretorio-considera")."(".$this->Atti_Correnti.")</a>",
	        "storico"     => "<a href='?page=atti&amp;stato_atti=Scaduti'>".__("Scaduti","albo-pretorio-considera")."(".$this->Atti_Scaduti.")</a>",
	        "oblio"       => "<a href='?page=atti&amp;stato_atti=Eliminare'>".__("da Eliminare","albo-pretorio-considera")."(".$this->Atti_Eliminare.")</a>",
	    );
	    return $status_links;
	}
  // Funzione per la preparazione dei campi da visualizzare
  // e la query SQL principale che deve essere eseguita 

  function prepare_items()
  {
    global $wpdb;
 
    // Calcolo elenco de dei campi per le differenti
    // sezioni e memorizzo tutto in array separati

    $columns  = $this->get_columns();
    $hidden   = $this->get_columns_hidden();
    $sortable = $this->get_columns_sortable();

    // Bisogna memorizzare tre array che devono contenere i campi da 
    // visualizzare, quelli nascosti e quelli per eseguire l'ordinamento


    $this->_column_headers = array($columns,$hidden,$sortable);

    // Preparazione delle variabili che devono essere utilizzate
    // nella preparazione della query con gli ordinamenti e la posizione
	$user = get_current_user_id();
	$screen = get_current_screen();
	$screen_option = $screen->get_option('per_page', 'option');
	$per_page = get_user_meta($user, $screen_option, true);
	if ( empty ( $per_page) || $per_page < 1 ) {
	    $per_page = $screen->get_option( 'per_page', 'default' );
	}
	if (!is_numeric($per_page))
		$per_page = 10;

    if (!isset($_REQUEST['paged'])) 
    	$paged = 0;
      else $paged = max(0,((isset($_REQUEST['paged'])?intval($_REQUEST['paged']):0)-1)*$per_page);

    if (isset($_REQUEST['orderby'])and in_array(sanitize_text_field(wp_unslash($_REQUEST['orderby'] ?? '')),array_keys($sortable)))
    	$orderby = sanitize_text_field(wp_unslash($_REQUEST['orderby'] ?? '')); 
    else
    	$orderby ="Anno DESC, Numero DESC , Data DESC";

    if (isset($_REQUEST['order']) and in_array(sanitize_text_field(wp_unslash($_REQUEST['order'] ?? '')),array('asc','desc')))
    	$order = sanitize_text_field(wp_unslash($_REQUEST['order'] ?? '')); 
    else $order = '';

    // Calcolo le variabili che contengono il numero dei record totali
    // e l'elenco dei record da visualizzare per una singola pagina
    // In stato "Cerca" i filtri (oggetto, riferimento, numero parziale,
    // categoria) vanno passati come parametri: lo stato 5 e' solo la base
    // (WHERE 1) e i singoli filtri sono preparati a valle in albopc_get_all_atti.
    $Termine = ''; $Rif = ''; $NumParz = ''; $Cat = 0;
    if ($this->stato_atti=="Cerca"){
    	$Termine = isset($_REQUEST['s'])            ? sanitize_text_field(wp_unslash($_REQUEST['s'] ?? ''))                 : '';
    	$Rif     = isset($_REQUEST['f_riferimento'])? sanitize_text_field(wp_unslash($_REQUEST['f_riferimento'] ?? ''))     : '';
    	$NumParz = isset($_REQUEST['f_numero'])     ? sanitize_text_field(wp_unslash($_REQUEST['f_numero'] ?? ''))          : '';
    	$Cat     = isset($_REQUEST['f_categoria'])  ? (isset($_REQUEST['f_categoria'])?(int)$_REQUEST['f_categoria']:0)  : 0;
    }
    $total_items = albopc_get_all_atti($this->Codstato_atti(),0,0,$Cat,$Termine, 0,0,"",0,0,true,false,$Rif,-1,false,$NumParz);
    $this->items = albopc_get_all_atti($this->Codstato_atti(),0,0,$Cat,$Termine, 0,0,$orderby." ".$order ,$paged,$per_page,false,false,$Rif,-1,false,$NumParz);
    $this->set_pagination_args(array(
    'total_items' => $total_items,
    'per_page'    => $per_page,
    'total_pages' => ceil($total_items/$per_page)
  ));
  }

  // Funzione per la definizione dei campi che devono
  // essere visualizzati nella lista da visualizzare

	function get_columns()
	{
	  switch ($this->stato_atti){
	  	case "Tutti":
	  	case "Correnti": 
	  	case "Scaduti":
	  		$columns = array(
		    'Stato'			 	 => __('Stato','albo-pretorio-considera'),
		    'Numero'             => __('Numero','albo-pretorio-considera'),
		    'Riferimento'        => __('Riferimento','albo-pretorio-considera'),
		    'Oggetto'          	 => __('Oggetto','albo-pretorio-considera'),
		    'Ente'               => __('Ente','albo-pretorio-considera'),
			'MetaDati'           => __('Meta Dati','albo-pretorio-considera'),
		    'Data'          	 => __('Del','albo-pretorio-considera'),
		    'validita'           => __('Validità Dal/Al','albo-pretorio-considera'),
		    'dataoblio'        	 => __('Oblio','albo-pretorio-considera'),
		    'Idcategoria'     	 => __('Categoria','albo-pretorio-considera'));
		    break;
	  	case "DaPubblicare": 
	  		$columns = array(
		    'Stato'			 	 => __('Stato','albo-pretorio-considera'),
		    'Riferimento'        => __('Riferimento','albo-pretorio-considera'),
		    'Oggetto'          	 => __('Oggetto','albo-pretorio-considera'),
		    'Ente'               => __('Ente','albo-pretorio-considera'),
			'MetaDati'           => __('Meta Dati','albo-pretorio-considera'),
		    'Data'          	 => __('Del','albo-pretorio-considera'),
		    'Idcategoria'     	 => __('Categoria','albo-pretorio-considera'));
		    break;
	  	case "Eliminare": 
	  		$columns = array(
	    	'cb'                 => '<input type="checkbox"/>',
		    'Stato'			 	 => __('Stato','albo-pretorio-considera'),
		    'Numero'             => __('Numero','albo-pretorio-considera'),
		    'Riferimento'        => __('Riferimento','albo-pretorio-considera'),
		    'Oggetto'          	 => __('Oggetto','albo-pretorio-considera'),
		    'Ente'               => __('Ente','albo-pretorio-considera'),
			'MetaDati'           => __('Meta Dati','albo-pretorio-considera'),	
		    'Data'          	 => __('Del','albo-pretorio-considera'),
		    'validita'           => __('Validità Dal/Al','albo-pretorio-considera'),
		    'dataoblio'        	 => __('Oblio','albo-pretorio-considera'),
		    'Idcategoria'     	 => __('Categoria','albo-pretorio-considera'));
		    break;
        case "Cerca": 
        	$columns = array(
		    'Stato'			 	 => __('Stato','albo-pretorio-considera'),
		    'Numero'             => __('Numero','albo-pretorio-considera'),
		    'Riferimento'        => __('Riferimento','albo-pretorio-considera'),
		    'Oggetto'          	 => __('Oggetto','albo-pretorio-considera'),
		    'Ente'               => __('Ente','albo-pretorio-considera'),
		    'Data'          	 => __('Del','albo-pretorio-considera'),
		    'validita'           => __('Validità Dal/Al','albo-pretorio-considera'),
		    'dataoblio'        	 => __('Oblio','albo-pretorio-considera'),
		    'Idcategoria'     	 => __('Categoria','albo-pretorio-considera'));
		    break;
	  }
	  return $columns;
	}

  // Funzione per la definizione dei campi che possono
  // essere utilizzati per eseguire la funzione di ordinamento

  function get_columns_sortable()
  {
	if (isset($_REQUEST['s'])){ /* mr */
		$sortable_columns = array(
      		'Data'       => array('Data',true),
            'Numero'      => array('Numero',true),             
      		'DataInizio'  => array('DataInizio',true),
      		'DataFine'    => array('DataFine',false));
    }else{	
   		$sortable_columns = array(
   			'Data'       => array('Data',true),
      		'DataInizio' => array('DataInizio',true),
      		'DataFine' 	=> array('DataFine',false));
	}
    return $sortable_columns;
  }

  // Funzione per la definizione dei campi che devono 
  // essere calcolati dalla query ma non visualizzati

  function get_columns_hidden() {
	  return array();  
  }

  // Funzione per reperire il valore di un campo in
  // maniera standard senza una personalizzazione di output

  function column_default($item,$column_name) { 
    return $item->$column_name; 
  }

  // Dato che alcuni campi hanno bisogno di output 
  // personalizzato bisogna creare una funzione per campo
  function column_Stato($item) { 

	$Msg="";
	if ( $item->DataAnnullamento != '0000-00-00' ) {
			$Annullato = true;
		} else {
			$Annullato = false;
		}

		if ((albopc_cvdate($item->DataInizio) <= albopc_cvdate(gmdate("Y-m-d"))) and (albopc_cvdate($item->DataFine) >= albopc_cvdate(gmdate("Y-m-d"))))
			$Scaduto=False;
		else	
			$Scaduto=True;

   	  $actions = array(
	    'visualizza'   => '<a href="?page=atti&amp;action=view-atto&amp;id='.$item->IdAtto.'&amp;stato_atti='.$this->stato_atti.'"  >
						<span class="dashicons dashicons-search" title="'.__('Visualizza dati atto','albo-pretorio-considera').'"></span>
					</a>');
	$this->AzioneDefault='<a href="?page=atti&amp;action=view-atto&amp;id='.$item->IdAtto.'&amp;stato_atti='.$this->stato_atti.'" >';
	switch($this->stato_atti){
		case "Tutti":
			$Msg="";
			$Msg.=($Scaduto?'<span style="color: rgb(23, 5, 161);font-weight: bold;">'.__('Scaduto','albo-pretorio-considera').'</span>':'<span style="color: green;font-weight: bold;">'.__('Corrente','albo-pretorio-considera').'</span>');
			$Msg.=($Annullato?' <span style="color: red;font-weight: bold;">'.__('Annullato','albo-pretorio-considera').'</span>':"");
			break;		
		case "DaPubblicare":
			$actions['cancella'] ='<span class="trash"><a href="?page=atti&amp;action=delete-atto&amp;id='.$item->IdAtto.'&amp;cancellaatto='.
				wp_create_nonce('deleteatto').'" rel="'.wp_strip_all_tags($item->Oggetto).'" tag="" class="ac">
						<span class="dashicons dashicons-trash" title="'.__('Cancella Atto','albo-pretorio-considera').'"></span>
					</a></span>';
			$this->AzioneDefault='<a href="?page=atti&amp;action=edit-atto&amp;id='.$item->IdAtto.'&amp;modificaatto='.
			wp_create_nonce('editatto').'">';
			$actions['modifica'] ='<a href="?page=atti&amp;action=edit-atto&amp;id='.$item->IdAtto.'&amp;modificaatto='.
			wp_create_nonce('editatto').'">
						<span class="dashicons dashicons-edit" title="'.__('Modifica atto','albo-pretorio-considera').'"></span>
					</a>';
			$actions['allegati'] ='<a href="?page=atti&amp;action=allegati-atto&amp;id='.$item->IdAtto.'&amp;allegatoatto='.
			wp_create_nonce('gestallegatiatto').'">
						<span class="dashicons dashicons-upload" title="'.__('Allegati','albo-pretorio-considera').'"></span>
					</a>';
			if (current_user_can('editore_atti_albo')){
				$actions['pubblica'] ='<a href="?page=atti&amp;action=approva-atto&amp;id='.$item->IdAtto.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$item->IdAtto).'"  >
	<span class="dashicons dashicons-share-alt" title="'.__('Pubblica atto','albo-pretorio-considera').'"></span>
					</a>';
			}
			$Msg='<span style="color: green;font-weight: bold;">'.__('Da Pubblicare','albo-pretorio-considera').'</span>';
			break;
		case "Correnti":
			$Msg=($Annullato?'<span style="color: red;font-weight: bold;">Annullato</span>':'<span style="color: green;font-weight: bold;">Pubblicato</span>');
			$actions['meta'] ='<a href="?page=atti&amp;action=metadati-atto&amp;id='.$item->IdAtto.'&amp;metaatto='.wp_create_nonce('operazionemetaatto').'&stato_atti=Correnti">
				<span class="dashicons dashicons-screenoptions" title="'.__('Gestisci metadati Atto','albo-pretorio-considera').'"></span>
			</a>';
			if (current_user_can('editore_atti_albo')){
				$actions['avviso'] ='<a href="?page=atti&amp;action=avviso_affissione-atto&amp;id='.$item->IdAtto.'&amp;avvisoatto='.wp_create_nonce('operazioneavviso_affissione').'&stato_atti=Correnti">
				<span class="dashicons dashicons-media-text" title="'.__('Stampa Avviso di Affissione','albo-pretorio-considera').'"></span>
			</a>';
			}
		break;
		case "Scaduti":
			$Msg=($Annullato?'<span style="color: red;font-weight: bold;">'.__('Annullato','albo-pretorio-considera').'</span>':'<span style="color: rgb(23, 5, 161);font-weight: bold;">Scaduto</span>');			
			$actions['meta'] ='<a href="?page=atti&amp;action=metadati-atto&amp;id='.$item->IdAtto.'&amp;metaatto='.wp_create_nonce('operazionemetaatto').'&stato_atti=Scaduti">
				<span class="dashicons dashicons-screenoptions" title="'.__('Gestisci metadati Atto','albo-pretorio-considera').'"></span>
			</a>';
			$actions['certificato'] ='<a href="?page=atti&amp;action=certificato_pubblicazione-atto&amp;id='.$item->IdAtto.'&amp;certificatoatto='.wp_create_nonce('operazionecertificato_pubblicazione').'&stato_atti=Scaduti">
				<span class="dashicons dashicons-media-spreadsheet" title="'.__('Stampa Certificato Pubblicazione','albo-pretorio-considera').'"></span>
			</a>';
			$actions['oblioallegati'] ='<a href="?page=atti&amp;action=oblio-allegati-atto&amp;id='.$item->IdAtto.'&amp;oaatto='.wp_create_nonce('operazioneoblioallegati').'&stato_atti=Scaduti">
				<span class="dashicons dashicons-editor-unlink" title="'.__('Cancella Allegati Atto','albo-pretorio-considera').'"></span>
			</a>';
			$actions['oblioatto'] ='<a href="?page=atti&amp;action=oblia-atto&amp;id='.$item->IdAtto.'&amp;oatto='.wp_create_nonce('operazionebliaatto').'&stato_atti=Scaduti">
				<span class="dashicons dashicons-hammer" title="'.__('Imposta la data di Oblio dell\'atto ad oggi','albo-pretorio-considera').'"></span>
			</a>';
			break;
		case "Eliminare":
			if (current_user_can('editore_atti_albo')){
				$actions['delete'] ='<span class="trash"><a href="?page=atti&amp;action=elimina-atto&amp;id='.$item->IdAtto.'&amp;cancellatto='.
				wp_create_nonce('operazionecancelaatto').'">
				<span class="dashicons dashicons-trash" title="'.__('Oblio Atto','albo-pretorio-considera').'"></span>
			</a></span>';
			}
			$Msg='<span style="color: red;font-weight: bold;">'.__('Oblio','albo-pretorio-considera').'</span>';			
			break;			
        case "Cerca": /* mr */
            if( $item->Numero == 0 ){    
				$Msg=('<span style="color: green;font-weight: bold;">'.__('Da Pubblicare','albo-pretorio-considera').'</span>');
                $actions['cancella'] ='<span class="trash"><a href="?page=atti&amp;action=delete-atto&amp;id='.$item->IdAtto.'&amp;cancellaatto='.
			wp_create_nonce('deleteatto').'" rel="'.wp_strip_all_tags($item->Oggetto).'" tag="" class="ac">
					<span class="delete dashicons dashicons-trash" title="'.__('Cancella Atto','albo-pretorio-considera').'"></span>
				</a></span>';
				$this->AzioneDefault='<a href="?page=atti&amp;action=edit-atto&amp;id='.$item->IdAtto.'&amp;modificaatto='.
				wp_create_nonce('editatto').'">';
				$actions['modifica'] ='<a href="?page=atti&amp;action=edit-atto&amp;id='.$item->IdAtto.'&amp;modificaatto='.
				wp_create_nonce('editatto').'">
					<span class="dashicons dashicons-edit" title="'.__('Modifica atto','albo-pretorio-considera').'"></span>
				</a>';
				$actions['allegati'] ='<a href="?page=atti&amp;action=allegati-atto&amp;id='.$item->IdAtto.'&amp;allegatoatto='.
				wp_create_nonce('gestallegatiatto').'">
					<span class="dashicons dashicons-upload" title="'.__('Allegati','albo-pretorio-considera').'"></span>
				</a>';
				if (current_user_can('editore_atti_albo')){
				$actions['pubblica'] ='<a href="?page=atti&amp;action=approva-atto&amp;id='.$item->IdAtto.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$item->IdAtto).'"  >
	<span class="dashicons dashicons-share-alt" title="'.__('Pubblica atto','albo-pretorio-considera').'"></span>
					</a>';
				}         
            }else{
                $Msg=($Annullato?'<span style="color: red;font-weight: bold;">'.__('Annullato','albo-pretorio-considera').'</span>':'<span style="color: green;font-weight: bold;">'.__('Pubblicato','albo-pretorio-considera').'</span>');    
                }
                break;
	}
	if( !$Scaduto and $Annullato=='' and ($this->stato_atti=="Correnti" || $this->stato_atti=="Cerca") and current_user_can('editore_atti_albo')){
		$actions['annulla'] ='<span class="trash"><a class="annullaatto" href="?page=atti&amp;action=annullamento-atto&amp;id='.$item->IdAtto.'">
				<span class="dashicons dashicons-dismiss" title="'.__('Annulla atto','albo-pretorio-considera').'"></span>
			</a></span>';
	}
	return sprintf('%1$s %2$s',$Msg,$this->row_actions($actions));
  }  
  function column_Ente($item) { 
  	$Ente=albopc_get_ente($item->Ente);
  	if($Ente===FALSE){
		return "<spam style=\"color:red;\">".__('Ente non definito','albo-pretorio-considera')."</spam>";
	}else{
    return stripslashes($Ente->Nome); 	
	}
  }  
   function column_MetaDati($item) { 
	$MetaDati=albopc_get_meta_atto($item->IdAtto);
	$Meta="";
	if($MetaDati!==FALSE){
		foreach($MetaDati as $Metadato){
			$Meta.=$Metadato->Meta."=".$Metadato->Value."<br />";
		}
		$Meta=substr($Meta,0,-6);
	}
    return stripslashes($Meta); 
  }  
 function column_Numero($item) { 
    return $this->AzioneDefault.$item->Numero."/".$item->Anno."</a>"; 
  }  
  function column_Data($item) { 
    return albopc_VisualizzaData($item->Data); 
  }  
  function column_Riferimento($item) { 
    return $this->AzioneDefault.stripslashes($item->Riferimento)."</a>"; 
  }  
  function column_Oggetto($item) { 
  	$Oggetto=stripslashes($item->Oggetto);
  	if ( strlen( $Oggetto ) > 120 ) {
			$Oggetto = substr( $Oggetto, 0, 120 ) . " ...";
		}
	return $this->AzioneDefault.$Oggetto."</a>"; 
  }   
  function column_validita($item) { 
    return albopc_VisualizzaData($item->DataInizio)."<br />".albopc_VisualizzaData($item->DataFine); 
  }  
  function column_Idcategoria($item) {
	if ($item->IdCategoria>0){
		$Cate=albopc_get_categoria($item->IdCategoria);
		return $Cate[0]->Nome;
	}else{
		return __('Non Definita','albo-pretorio-considera');
	}
  }   
  function column_dataoblio($item) { 
    return albopc_VisualizzaData($item->DataOblio); 
  }  

// Definire la nuova funzione per indicare le
// azioni che devo essere presenti sul menu a tendina

	function get_bulk_actions() {
	  if (isset($_GET['stato_atti']) And sanitize_text_field(wp_unslash($_GET['stato_atti'] ?? '')) == "Eliminare" And current_user_can('editore_atti_albo'))	
	  	return array('delete_bulk_atti' => __('Elimina','albo-pretorio-considera'));
	}

	// Funzione per la prima colonna che non sarà più il 
	// numero di tessera ma un campo di checkbox per la selezione

	function column_cb($item) {
	  if (current_user_can('editore_atti_albo')){
		  return sprintf('<input type="checkbox" name="IdAtto[]" value="%s"/>',$item->IdAtto);
	  }
	}
}

if(isset($_REQUEST['action'])){
	switch (sanitize_text_field(wp_unslash($_REQUEST['action'] ?? ''))){
		case "metadati-atto":
			albopc_Gestione_Metadati((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0));
			break;
		case "logatto" :
			global $albopc_AP_OnLine;
			$IdAtto = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
			echo wp_json_encode($albopc_AP_OnLine->CreaLog(1,$IdAtto,0));
			die();
			break;
		case "view-atto" :
			albopc_View_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0));
			break;
			
			case "oblio-allegati-atto":
				if ( isset( $_GET['oaatto'] ) && ! empty( $_GET['oaatto'] ) ) {
		            $albopc_nonce  = filter_input( INPUT_GET, 'oaatto' );
		            $action = 'operazioneoblioallegati';
		            if ( ! wp_verify_nonce( $albopc_nonce, $action ) )
		                wp_die( __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti&stato_atti=Correnti") );
			 		if (is_numeric(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')))) {
 	                    $albopc_MessaggiRitorno=albopc_CancellaAllegatiAtto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0));
					}
				}else
					wp_die( __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti") );					
			break;				
		case "annullamento-atto" :
			albopc_annulla_atto_page((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0));
			break;
		case "new-atto" :
			albopc_Nuovo_atto();
			break;
		case "edit-atto" :
			if (!isset($_REQUEST['modificaatto'])) {
				albopc_Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modificaatto'] ?? '')),'editatto')){
				albopc_Go_Atti();
				break;
			} 		
			albopc_Edit_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0));
			break;
		case "pubblica-atto":
			if ( ! isset( $_REQUEST['pubblicaatto'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['pubblicaatto'] ?? '')), 'pubblicaatto-'.(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0) ) ) {
				albopc_Lista_Atti(__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			}
			albopc_Lista_Atti(albopc_approva_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0)));
			break;
		case "setta-anno":
			if ( ! isset( $_REQUEST['settaanno'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['settaanno'] ?? '')), 'settaanno-'.(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0) ) ) {
				albopc_PreApprovazione((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			}
			update_option('opt_AP_AnnoProgressivo',gmdate("Y") );
		  	update_option('opt_AP_NumeroProgressivo',1 );
			albopc_PreApprovazione((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __('Anno Albo settato a %s Numero progressivo settato a 0','albo-pretorio-considera'),gmdate("Y")));
			break;
		case "approva-atto" :
			$albopc_ret="";
			if ( ! isset( $_REQUEST['approvaatto'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['approvaatto'] ?? '')), 'approvaatto-'.(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0) ) ) {
				albopc_PreApprovazione((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			}
			if (isset($_REQUEST['apa'])){
				$albopc_ret=albopc_update_selettivo_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),array('Anno' => sanitize_text_field(wp_unslash($_REQUEST['apa'] ?? ''))),array('%s'),__('Modifica in Approvazione','albo-pretorio-considera')."\n");
			}
			if (isset($_REQUEST['pnp'])){
				update_option( 'opt_AP_NumeroProgressivo', (isset($_REQUEST['pnp'])?(int)$_REQUEST['pnp']:0));
			}
			if (isset($_REQUEST['udi'])){
				$albopc_ret=albopc_update_selettivo_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),array('DataInizio' => sanitize_text_field(wp_unslash($_REQUEST['udi'] ?? ''))),array('%s'),__('Modifica in Approvazione','albo-pretorio-considera')."\n");	
			}
			if (isset($_REQUEST['udf'])){
				$albopc_ret=albopc_update_selettivo_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),array('DataFine' => sanitize_text_field(wp_unslash($_REQUEST['udf'] ?? ''))),array('%s'),__('Modifica in Approvazione','albo-pretorio-considera')."\n");	
			}
			if (isset($_REQUEST['udo'])){
				$albopc_ret=albopc_update_selettivo_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),array('DataOblio' => sanitize_text_field(wp_unslash($_REQUEST['udo'] ?? ''))),array('%s'),"Modifica in Approvazione\n");	
			}
			if(isset($_REQUEST['id']))
				$id=(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0);
			else
				$id=0;
			albopc_PreApprovazione($id,$albopc_ret);
			break;
		case "allegati-atto" :
			if (!isset($_REQUEST['allegatoatto'])) {
				albopc_Lista_Atti(__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['allegatoatto'] ?? '')),'gestallegatiatto')){
				albopc_Lista_Atti(__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			} 		
			albopc_Allegati_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),(isset($_REQUEST['messaggio'])?sanitize_text_field(wp_unslash($_REQUEST['messaggio'] ?? '')):""));
			break;
		case "edit-allegato-atto" :
			if (!isset($_REQUEST['modificaallegatoatto'])) {
				albopc_Lista_Atti(__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modificaallegatoatto'] ?? '')),'editallegatoatto')){
				albopc_Lista_Atti(__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			} 				
			albopc_Allegati_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),(isset($_REQUEST['messaggio'])?sanitize_text_field(wp_unslash($_REQUEST['messaggio'] ?? '')):""),(isset($_REQUEST['idAlle'])?(int)$_REQUEST['idAlle']:0));
			break;
		case "UpAllegati":
			include_once ( dirname (__FILE__) . '/allegati_multi.php' );
			break;
		case "AssAllegati":
			include_once ( dirname (__FILE__) . '/allegati.php' );
			break;
		default:
			if(isset($_REQUEST['message'])){
				if (is_numeric(sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''))))
					$albopc_message=sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''));
				elseif(strlen(sanitize_text_field(wp_unslash($_REQUEST['message'] ?? '')))>0)
						$albopc_message=sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''));
				else $albopc_message="";
			}else
				$albopc_message="";

			albopc_Lista_Atti($albopc_message);
			break;
	}	
}else{
	if(isset($_REQUEST['message'])){
		if (is_numeric(sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''))))
			$albopc_message=sanitize_text_field(wp_unslash($_REQUEST['message'] ?? ''));
		elseif(strlen(sanitize_text_field(wp_unslash($_REQUEST['message'] ?? '')))>0)
				$albopc_message=urldecode(sanitize_text_field(wp_unslash($_REQUEST['message'] ?? '')));
	}else{
		$albopc_message="";
	}
	albopc_Lista_Atti($albopc_message);
}

unset($_REQUEST['action']);

function albopc_Gestione_Metadati($IdAtto){
	global $albopc_AP_OnLine;
	$risultato=albopc_get_atto($IdAtto);
	$risultato=$risultato[0];
	$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$NomeEnte=albopc_get_ente($risultato->Ente);
	$NomeEnte=stripslashes($NomeEnte->Nome);
?>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> Atti</h2>
		<a href="<?php echo site_url();?>/wp-admin/admin.php?page=atti&stato_atti=<?php echo sanitize_text_field(filter_input(INPUT_GET,"stato_atti"));?>" class="add-new-h2 tornaindietro"><?php esc_html_e("Torna indietro","albo-pretorio-considera");?></a>
		<h3>Dati Atto</h3>	
	</div>
	<div class="clear"><br /></div>
	<div id="col-container">
		<div id="col-right">
			<form id="memo_metadati_atto" method="post" action="?page=atti" class="validate">
			<input type="hidden" name="action" value="memo_metadati_atto" />
			<input type="hidden" name="id" value="<?php echo $IdAtto;?>" />
			<input type="hidden" name="stato_atti" value="<?php echo sanitize_text_field(filter_input(INPUT_GET,"stato_atti"));?>" />
			<input type="hidden" name="mmda" value="<?php echo wp_create_nonce('editmetadatiattoatto')?>" />

			<div class="col-wrap postbox" style="padding:0 10px 10px 10px;margin-left:10px;" id="MetaDati">
			<h2 class="hndle"><span><?php esc_html_e("Meta Dati Personalizzati","albo-pretorio-considera");?></span> <button type="button" id="AddMeta" class="setta-def-data">Aggiungi Meta Valore</button></h2>
				<div style="display:none;" id="newMeta">
					<label for="listaAttiMeta"><?php esc_html_e("Meta già codificati","albo-pretorio-considera");?></label> <?php echo albopc_get_elenco_attimeta("Select","listaAttiMeta","ListaAttiMeta","Si");?>
					<label for="newMetaName"><?php esc_html_e("Nome Meta","albo-pretorio-considera");?></label> <input name="newMetaName" id="newMetaName"/>
					<label for="newValue"><?php esc_html_e("Valore Meta","albo-pretorio-considera");?></label> <input name="newValue" id="newValue">
					<button type="button"class="setta-def-data" id="AddNewMeta">Aggiungi</button> <button type="button"class="setta-def-data" id="UndoNewMedia">Anulla</button>
				</div>
<?php				echo albopc_get_elenco_attimeta("Div","","","",$IdAtto);			?>
			</div>
			<div class="col-wrap postbox" style="padding:10px;margin-left:10px;">
				<input type="submit" name="AggiornaMetaDati" id="AggiornaMetaDati" style="margin:auto;" class="button button-primary button-large" value="<?php esc_html_e("Memorizza Modifiche Meta Dati Atto","albo-pretorio-considera");?>" />
			</form>
			</div>
		</div><!-- /post-body-content -->
	</div>
	<div id="col-left">
		<div class="col-wrap postbox" style="padding:0 10px 10px 10px;margin-left:10px;">
			<h3>Dati atto</h3>
			<hr />
			<table class="widefat" style="border:0;">
				<tbody id="dati-atto">
				<tr>
					<th style="width:20%;"><?php esc_html_e("Ente emittente","albo-pretorio-considera");?></th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;"><?php echo $NomeEnte;?></td>
				</tr>
<?php
		if($risultato->DataAnnullamento!='0000-00-00')		
			echo '		<tr>
				<th style="width:20%;">'.__("Data Annullamento","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataAnnullamento).'</td>
			</tr>
	    	<tr>
				<th style="width:20%;">'.__("Motivo Annullamento","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:top;">'.stripslashes($risultato->MotivoAnnullamento).'</td>
			</tr>';
		echo '		<tr>
				<th style="width:20%;">'.__("Numero Albo","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
			</tr>
			<tr>
				<th>'.__("Data","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->Data).'</td>
			</tr>
			<tr>
				<th>'.__("Codice di Riferimento","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Riferimento).'</td>
			</tr>
			<tr>
				<th>'.__("Oggetto","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Oggetto).'</td>
			</tr>
			<tr>
				<th>'.__("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataInizio).'</td>
			</tr>
			<tr>
				<th>'.__("Data fine Pubblicazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataFine).'</td>
			</tr>
			<tr>
				<th>'.__("Data Oblio","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataOblio).'</td>
			</tr>
			<tr>
				<th>'.__("Note","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Informazioni).'</td>
			</tr>
			<tr>
				<th>'.__("Categoria","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultatocategoria->Nome).'</td>
			</tr>
				<tr>
					<th>'.__("Soggetti","albo-pretorio-considera").'</th>
						<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">	
					<ul>';
	$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
	$Soggetti=(is_array($Soggetti) && !empty($Soggetti)) ? albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti)) : array();
	foreach((array)$Soggetti as $Soggetto){
		echo "
			<li><strong>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</strong> ".$Soggetto->Nome." ".$Soggetto->Cognome." 
			</li>";
	}
echo'				
				</ul>
					</td>
				</tr>
		    </tbody>
		</table>
	</div>';
echo '<div class="postbox" style="padding:0 10px 10px 10px;margin-left:10px;">
	<h3>'.__("Allegati","albo-pretorio-considera").'</h3>
	<div class="Visalbo">';
$allegati=albopc_get_all_allegati_atto($IdAtto);
$TipidiFiles=albopc_get_tipidifiles();
foreach ($allegati as $allegato) {
	$Estensione=albopc_ExtensionType($allegato->Allegato);	
	echo '<div style="border: thin dashed;font-size: 1em;">
			<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
				<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"allegato/>
			</div>
			<div style="margin-top:0;">
				<p style="margin-top:0;">'.wp_strip_all_tags($allegato->TitoloAllegato).' <br />';
			if (is_file($allegato->Allegato))
				echo '        <a href="'.albopc_DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.albopc_Formato_Dimensione_File(filesize($allegato->Allegato)).')<br />'.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']);
			else
				echo basename( $allegato->Allegato).__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
echo'				</p>
			</div>
			<div style="clear:both;"></div>
		</div>';
	}
echo '</div>
	</div>

</div>';	
}

function albopc_PreApprovazione($id,$albopc_ret=''){
global $wpdb;
if (!current_user_can('editore_atti_albo')){
	echo '<div id="message" class="updated"><p>'.__("Questa Operazione non ti è consentita, operazione di pertinenza dell'amministratore dell'Albo o del redattore","albo-pretorio-considera").'</p></div>';
	return;
}
if ($albopc_ret!=""){
	$albopc_ret=str_replace("%%br%%","<br />",$albopc_ret);
}
	$NumeroDaDb=albopc_get_last_num_anno(gmdate("Y"));
	$atto=albopc_get_atto($id);
	$atto=$atto[0];
	//$dif=albopc_datediff("d",albopc_cvdate($atto->DataInizio),albopc_cvdate($atto->DataFine));
	$NumeroOpzione=get_option('opt_AP_NumeroProgressivo');
	$NumAttiPubblicati=albopc_get_all_atti(9,0,0,0,"",0,0,"",0,0,TRUE);
	if($NumAttiPubblicati==0) 
		$AppPostMigrazione=" <span style='color:red;'>".__("Validato perchè primo atto dopo l'INSTALLAZIONE","albo-pretorio-considera")." </span>";
	else
		$AppPostMigrazione="";
echo'
<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> Atti</h2>
		<a href="'.site_url().'/wp-admin/admin.php?page=atti" class="add-new-h2 tornaindietro">'.__("Torna indietro","albo-pretorio-considera").'</a>';
	if ( $albopc_ret!="" ) {
		echo '<div id="message" class="updated"><p>'.$albopc_ret.'</p></div>';
	}
	echo '
		<h3>'.__("Approvazione Atto","albo-pretorio-considera").'</h3>	
	</div>
	<br class="clear" />';
if(get_option('opt_AP_AnnoProgressivo')!=gmdate("Y")){
	echo '<div style="border: medium groove Blue;margin-top:10px;">
			<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
				<form id="agg_anno_progressivo" method="post" action="?page=atti">
				<input type="hidden" name="action" value="setta-anno" />
				<input type="hidden" name="id" value="'.esc_attr($id).'" />
				<input type="hidden" name="settaanno" value="'.esc_attr(wp_create_nonce('settaanno-'.$id)).'" />
				<input type="submit" name="submit" id="submit" class="button" value="'.__("Aggiorna Anno Albo ed Azzera numero Progressivo","albo-pretorio-considera").'"  />
				</form>
			</div>
		</div>';
}else
{
echo'<br />
<table class="widefat">
	<thead>	
	<tr>
		<th style="text-align:center;font-size:1.5em;width:20%;">'.__("Campi Atto","albo-pretorio-considera").'</th>
		<th style="text-align:center;font-size:1.5em;width:30%;">'.__("Dati atto","albo-pretorio-considera").'</th>
		<th style="text-align:center;font-size:1.5em;width:30%;">'.__("Stato","albo-pretorio-considera").'</th>
		<th style="text-align:center;font-size:1.5em;">'.__("Operazioni","albo-pretorio-considera").'</th>
	</tr>
	</thead>
    <tbody id="dati-atto">
	<tr>
		<td>'.__("Anno Atto","albo-pretorio-considera").'</td>
		<td>'.$atto->Anno.'</td>';
		if ($atto->Anno==gmdate("Y")){
		 	$Passato=true;
			echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
		}else{
		 	$Passato=false;
			echo '<td>'.__("Verificata incongruenza, bisogna rimediare prima di proseguire","albo-pretorio-considera").'</td>
			      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$id).'&amp;apa='.gmdate("Y").'" class="add-new-h2">Imposta Anno Pubblicazione a '.gmdate("Y").'</td>';
		}
		echo '</tr>';
		if($Passato){
			echo '<tr>
			<td>'.__("Numero Atto","albo-pretorio-considera").'</td>
			<td>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("da Parametri %s Progressivo da ultima pubblicazione","albo-pretorio-considera"),get_option('opt_AP_NumeroProgressivo')).' '.$NumeroDaDb.$AppPostMigrazione.'</td>';
			if (($NumeroDaDb==$NumeroOpzione) Or $NumAttiPubblicati==0){
			 	$Passato=true;
				echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
			}else{
			 	$Passato=false;
				echo '<td>'.__("Verificata incongruenza, bisogna rimediare prima di proseguire","albo-pretorio-considera").'</td>
				      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$id).'&amp;pnp='.$NumeroDaDb.'" class="add-new-h2">'.__("Imposta Parametro a","albo-pretorio-considera").' '.$NumeroDaDb.'</td>';
			}
			echo '</tr>';
		}
		if($Passato){
			echo '<tr>
					<td>'.__("Data Inizio Pubblicazione","albo-pretorio-considera").'</td>
					<td>'.albopc_VisualizzaData($atto->DataInizio).'</td>';
			if($atto->DataInizio==albopc_oggi()){
				$Passato=true;
				echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
			}else{
	 			$Passato=false;
	   			echo '<td>'.__("Aggiornare la data di Inizio Pubblicazione","albo-pretorio-considera").'</td>
			      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$id).'&amp;udi='.albopc_oggi().'" class="add-new-h2">'.__("Aggiorna a","albo-pretorio-considera").' '.albopc_VisualizzaData(albopc_oggi()).'</td>';
			}
			echo "</tr>";
		}
		if($Passato){
 			$categoria=albopc_get_categoria($atto->IdCategoria);
 			$incrementoStandard=$categoria[0]->Giorni;
 			$newDataFine=albopc_DateAdd($atto->DataInizio,$incrementoStandard);
 			$differenza=albopc_datediff("d", $atto->DataInizio, $atto->DataFine);
			$differenza=($differenza==-1) ? 0 : $differenza;
 			$NggInc=0;
 			while(albopc_IsDataFestiva($atto->DataFine)){
					$NggInc++;					
					$atto->DataFine=albopc_DateAdd($atto->DataFine,1);
				}
			if($NggInc>0){
				albopc_update_selettivo_atto($id,array('DataFine' => $atto->DataFine),array('%s'),__('Modifica della data di fine pubblicazione perchè giorno festivo','albo-pretorio-considera')."\n");		
			}
			echo '<tr>
					<td>'.__("Data Fine Pubblicazione","albo-pretorio-considera").'</td>';
			if(albopc_SeDate(">=",$atto->DataFine,$atto->DataInizio)){
				$Passato=true;
				echo '<td>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$s GG Pubblicazione Atto %2\$s GG Pubblicazione standard Categoria %3\$s GG Incremento per scadenza in giorno Festivo %4\$s","albo-pretorio-considera"),albopc_VisualizzaData($atto->DataFine),$differenza,$categoria[0]->Giorni,$NggInc).'</td>';
				if (albopc_datediff("d", $atto->DataInizio, $atto->DataFine)== $categoria[0]->Giorni){
					echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
				}else{
					echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
					echo '<td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$id).'&amp;udf='.$newDataFine.'" class="add-new-h2">Aggiorna a '.albopc_VisualizzaData($newDataFine).'</a></td>';
				}
			}else{
	 			$Passato=false;
	   			echo '<td><span style="color:red;">'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("La data di fine Pubblicazione %1\$s è antecedente della data di inizio pubblicazione %2\$s","albo-pretorio-considera"),albopc_VisualizzaData($atto->DataFine),albopc_VisualizzaData($atto->DataInizio)).'</span></td>
				   <td><span style="font-weight:bold;">'.__("Aggiornare la data di Fine Pubblicazione con i giorni della categoria o tornare indietro e modificare l'atto","albo-pretorio-considera").'</span></td>
			      <td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$id).'&amp;udf='.$newDataFine.'" class="add-new-h2">'.__("Aggiorna a","albo-pretorio-considera").' '.albopc_VisualizzaData($newDataFine).'</a></td>';
			}
			echo '</tr>';
		}
		if($Passato){
  			$incrementoStandard=get_option('opt_AP_GiorniOblio');
 			$DataOblioStandard=(gmdate("Y")+6)."-01-01";
 			//echo $atto->DataInizio."   -  ".$incrementoStandard;
			echo '<tr>
					<td>'.__("Data Oblio","albo-pretorio-considera").'</td>
					<td>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$s - Data Oblio da Decreto n. 33/2013 art. 8 %2\$s","albo-pretorio-considera"),albopc_VisualizzaData($atto->DataOblio),albopc_VisualizzaData($DataOblioStandard)).'</td>';
				//	echo $atto->DataFine.' '.$atto->DataInizio. ' '.SeDate("<=",$atto->DataFine,$atto->DataInizio);
			if(albopc_SeDate("=",$atto->DataOblio,$DataOblioStandard)){
				$Passato=true;
				echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
			}else{
				echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
				echo '<td><a href="?page=atti&amp;action=approva-atto&amp;id='.$id.'&amp;approvaatto='.wp_create_nonce('approvaatto-'.$id).'&amp;udo='.$DataOblioStandard.'" class="add-new-h2">'.__("Aggiorna a","albo-pretorio-considera").' '.albopc_VisualizzaData($DataOblioStandard).'</a></td>';
			}
		echo '</tr>';
		}
		if($Passato){
 			$numAllegati=albopc_get_num_allegati($id);
			echo '<tr>
					<td>'.__("Allegati","albo-pretorio-considera").'</td>
					<td>'.__("N.","albo-pretorio-considera").' '.$numAllegati.'</td>';
			if($numAllegati>0){
				$Passato=true;
					echo '<td>'.__("Ok","albo-pretorio-considera").'</td>';
				}else{
					$Passato=false;
					echo '<td>'.__("Da revisionare","albo-pretorio-considera").'</td>
					      <td><a href="?page=atti&amp;id='.$id.'&amp;action=UpAllegati&amp;ref=approva-atto" class="add-new-h2">'.__("Inserisci Allegato","albo-pretorio-considera").'</a></td>';
				}
			echo '</tr>';
		}
		if($Passato){
			if(strlen($atto->Richiedente)<1){
				$Passato=false;
				echo '<tr>
					<td>'.__("Richiedente","albo-pretorio-considera").'</td>
					<td>'.__("Richiesto","albo-pretorio-considera").'</td>
					<td>'.__("Da revisionare","albo-pretorio-considera").'</td>
				      <td><a href="?page=atti&action=edit-atto&id='.$id.'&amp;modificaatto='.wp_create_nonce('editatto').'" class="add-new-h2">'.__("Modifica atto","albo-pretorio-considera").'</a></td>';
			}
			echo '</tr>';
		}
		if($Passato){
			if($atto->IdUnitaOrganizzativa==0){
				$Passato=false;
				echo '<tr>
					<td>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</td>
					<td>'.__("Richiesto","albo-pretorio-considera").'</td>
					<td>'.__("Da revisionare","albo-pretorio-considera").'</td>
				      <td><a href="?page=atti&action=edit-atto&id='.$id.'&amp;modificaatto='.wp_create_nonce('editatto').'" class="add-new-h2">'.__("Modifica atto","albo-pretorio-considera").'</a></td>';
			}
			echo '</tr>';
		}
		if($Passato){
			if($atto->RespProc==0){
				$Passato=false;
				echo '<tr>
					<td>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</td>
					<td>'.__("Richiesto","albo-pretorio-considera").'</td>
					<td>'.__("Da revisionare","albo-pretorio-considera").'</td>
				      <td><a href="?page=atti&action=edit-atto&id='.$id.'&amp;modificaatto='.wp_create_nonce('editatto').'" class="add-new-h2">'.__("Modifica atto","albo-pretorio-considera").'</a></td>';
			}
			echo '</tr>';
		}
echo '</tbody>
	</table>';
if ($Passato){
echo'
<div style="border: medium groove Blue;margin-top:10px;">
	<div style="float:none;width:200px;margin-left:auto;margin-right:auto;">
		<form id="approva-atto" method="post" action="?page=atti">
		<input type="hidden" name="action" value="pubblica-atto" />
		<input type="hidden" name="id" value="'.esc_attr($id).'" />
		<input type="hidden" name="pubblicaatto" value="'.esc_attr(wp_create_nonce('pubblicaatto-'.$id)).'" />
		<input type="hidden" name="stato_atti" value="Correnti" />
		<input type="submit" name="submit" id="submit" class="button" value="Pubblica Atto"  />
		</form>
	</div>
</div>
<div id="col-right">
<div class="col-wrap">
<h3>'.__("Documenti/Allegati","albo-pretorio-considera").'</h3>';
$righe=albopc_get_all_allegati_atto($id,array("Natura","IdAllegato"),array("DESC","ASC"));
$Ente=albopc_get_ente($atto->Ente);
$Unitao=albopc_get_unitaorganizzativa($atto->IdUnitaOrganizzativa);
$NomeResp=albopc_get_responsabile($atto->RespProc);
$NomeResp=$NomeResp[0];
echo'
	<table class="widefat">
	    <thead>
		<tr>
			<th style="font-size:1.5em;">'.__("Operazioni","albo-pretorio-considera").'</th>
			<th style="font-size:1.5em;">'. __("Natura doc.","albo-pretorio-considera").'</th>
			<th style="font-size:1.5em;">'.__("Allegato","albo-pretorio-considera").'</th>
			<th style="font-size:1.5em;">'.__("File","albo-pretorio-considera").'</th>
			<th style="font-size:1.5em;">'. __("Doc. Integrale","albo-pretorio-considera").'</th>
		</tr>
	    </thead>
	    <tbody id="righe-log">';
foreach ($righe as $riga) {
	echo '<tr>
			<td>	
					<a href="'.albopc_DaPath_a_URL($riga->Allegato).'" target="_parent">
						<span class="dashicons dashicons-search" title="'.__("Visualizza dati atto","albo-pretorio-considera").'"></span>
					</a>
			</td>
			<td >'. basename( $riga->Natura=="A"?__("Allegato","albo-pretorio-considera"):__("Doc. Firmato","albo-pretorio-considera")).'</td>
			<td >'.$riga->TitoloAllegato.'</td>
			<td >'. basename( $riga->Allegato).'</td>
			<td >'. basename( $riga->DocIntegrale==1?__("Si","albo-pretorio-considera"):__("No","albo-pretorio-considera")).'</td>
		</tr>';
}
echo '    </tbody>
	</table>
</div>
</div>
<div id="col-left">
<div class="col-wrap">
<h3>'.__("Atto","albo-pretorio-considera").'</h3>
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="2" style="text-align:center;font-size:1.5em;">'.__("Dati atto","albo-pretorio-considera").'</th>
		</tr>
	    </thead>
	    <tbody id="dati-atto">
		<tr>
			<th>'.__("Ente emittente","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($Ente->Nome).'</td>
		</tr>
		<tr>
			<th style="width:50%;">'.__("Numero Albo","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->Numero."/".$atto->Anno.'</td>
		</tr>
		<tr>
			<th>'.__("Codice di Riferimento","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Riferimento).'</td>
		</tr>
		<tr>
			<th>'.__("Oggetto","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Oggetto).'</td>
		</tr>
		<tr>
			<th>'.__("Data di registrazione","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->Data.'</td>
		</tr>
		<tr>
			<th>'.__("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->DataInizio.'</td>
		</tr>
		<tr>
			<th>'.__("Data fine Pubblicazione","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->DataFine.'</td>
		</tr>
		<tr>
			<th>'.__("Data oblio","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$atto->DataOblio.'</td>
		</tr>
		<tr>
			<th>'.__("Richiedente","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Richiedente).'</td>
		</tr>
		<tr>
			<th>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($Unitao->Nome).'</td>
		</tr>
		<tr>
			<th>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($NomeResp->Nome." ".$NomeResp->Cognome).'</td>
		</tr>
		<tr>
			<th>'.__("Categoria","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($categoria[0]->Nome).'</td>
		</tr>
		<tr>
			<th>'.__("Note","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($atto->Informazioni).'</td>
		</tr>';
	$MetaDati=albopc_get_meta_atto($id);
	if($MetaDati!==FALSE){
		$Meta="";
		foreach($MetaDati as $Metadato){
			$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
		}
		$Meta=substr($Meta,0,-3);
		echo'
				<tr>
					<th>'. __("Meta Dati","albo-pretorio-considera").'</th>
					<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$Meta.'</td>
				</tr>';
	}
	echo'
			<tr>
				<th>'. __("Soggetti","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">
				<ul>';
	$Soggetti=unserialize($atto->Soggetti, array('allowed_classes'=>false));
	if ($Soggetti){
		$Soggetti=albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
		foreach($Soggetti as $Soggetto){
			echo "
				<li><strong>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</strong> <br />".$Soggetto->Nome." ".$Soggetto->Cognome." 
				</li>";
		}
	}
	echo'				
					</ul>
					</td>
				</tr>	    



	    </tbody>
	</table></div>
</div>';
}
}
echo '</div>';
}


function albopc_Nuovo_atto(){
/*	$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];*/
	if (isset($_REQUEST['Data']) And sanitize_text_field(wp_unslash($_REQUEST['Data'] ?? '')) != "")
		$dataCorrente=sanitize_text_field(wp_unslash($_REQUEST['Data'] ?? ''));
	else
		$dataCorrente=gmdate("d/m/Y");
	if (isset($_REQUEST['Ente']))
		$defEnte=(isset($_REQUEST['Ente'])?intval($_REQUEST['Ente']):0);
	else
		$defEnte=get_option('opt_AP_DefaultEnte');
	if (isset($_REQUEST['Riferimento']) )
		$Riferimento=sanitize_text_field(wp_unslash($_REQUEST['Riferimento'] ?? ''));
	else
		$Riferimento="";
	if (isset($_REQUEST['Oggetto']))
		$Oggetto=sanitize_text_field(wp_unslash($_REQUEST['Oggetto'] ?? ''));
	else
		$Oggetto="";
/*	if (sanitize_text_field(wp_unslash($_REQUEST['DataInizio'] ?? '')))
		$DataI=sanitize_text_field(wp_unslash($_REQUEST['DataInizio'] ?? ''));
	else*/
	$DataI=gmdate("d/m/Y");
	if (isset($_REQUEST['DataFine']))
		$DataF=sanitize_text_field(wp_unslash($_REQUEST['DataFine'] ?? ''));
	else
		$DataF=gmdate("d/m/Y");
	if (isset($_REQUEST['DataOblio']))
		$DataO=sanitize_text_field(wp_unslash($_REQUEST['DataOblio'] ?? ''));
	else
		$DataO=albopc_VisualizzaData((gmdate("Y")+6)."-01-01");
	if (isset($_REQUEST['Note']))
		$Note=sanitize_textarea_field(wp_unslash($_REQUEST['Note'] ?? ''));
	else	
		$Note="";
	if (isset($_REQUEST['Categoria']))
		$Categoria=(isset($_REQUEST['Categoria'])?intval($_REQUEST['Categoria']):0);
	else
		$Categoria=0;
	if (isset($_REQUEST['Unitao']))
		$Unitao=(isset($_REQUEST['Unitao'])?intval($_REQUEST['Unitao']):0);
	else
		$Unitao=0;
	if (isset($_REQUEST['Responsabile']))
		$Responsabile=(isset($_REQUEST['Responsabile'])?intval($_REQUEST['Responsabile']):0);
	else{
		$Resp=albopc_get_responsabili();
		if (count($Resp)>0)
			$Responsabile=$Resp[0]->IdResponsabile;
		else
			$Responsabile=0;	
	}
	if (isset($_REQUEST['Richiedente']))
		$Richiedente=albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['Richiedente'] ?? '')));
	else	
		$Richiedente="";
	$DefaultSoggetti=get_option('opt_AP_DefaultSoggetti',
								array("RP"=>0,
	  								  "RB"=>0,
	  								  "AM"=>0));
	if(!is_array($DefaultSoggetti)){
		$DefaultSoggetti=json_decode($DefaultSoggetti,TRUE);
	}
$DataOblioStandard=(gmdate("Y")+6)."-01-01";		
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-considera");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-considera");?>:</h3><p id="ElencoCampiConErrori"></p><p style='color:red;font-weight: bold;'><?php esc_html_e("Correggere gli errori per continuare","albo-pretorio-considera");?></p>
</div>

<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> <?php esc_html_e("Atti","albo-pretorio-considera");?></h2>
		<a href="<?php echo site_url().'/wp-admin/admin.php?page=atti';?>" class="add-new-h2 tornaindietro"><?php esc_html_e("Torna indietro","albo-pretorio-considera");?></a>
		<div class="Obbligatori">
		<span style="color:red;font-weight: bold;">*</span> <?php esc_html_e("i campi contrassegnati dall'asterisco sono <strong>obbligatori</strong>","albo-pretorio-considera");?>
		</div>
		<h3><?php esc_html_e("Nuovo Atto","albo-pretorio-considera");?></h3>	
	</div>
	<input type="hidden" id="NonVal" value="<?php esc_html_e("Non Valorizzato","albo-pretorio-considera");?>" />
	<input type="hidden" id="NonSOg" value="<?php esc_html_e("Nessun Soggetto selezionato, ne devi selezionare almeno UNO","albo-pretorio-considera");?>" />

		<form id="addatto" method="post" action="?page=atti" class="validate">
		<input type="hidden" name="action" value="add-atto" />
		<input type="hidden" name="id" value="<?php echo(int)(isset($_REQUEST['id'])?sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')):0);?>" />
		<input type="hidden" name="nuovoatto" value="<?php echo wp_create_nonce('nuovoatto')?>" />

	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div id="riferimentodiv">
					<h2><?php esc_html_e("Riferimento","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></h2>
					<textarea name="Riferimento" id="<?php echo esc_html_e("Riferimento","albo-pretorio-considera");?>"" rows="2" cols="255"  class="richiesto" style="width: 100%"><?php echo stripslashes($Riferimento);?></textarea>
				<label for="Riferimento" style="font-style: italic;"><?php esc_html_e("Codice di riferimento dell'atto, es. N. Protocollo","albo-pretorio-considera");?> </label>
				</div><!-- /riferimentodiv -->
				<div id="riferimentowrap">
					<h2><?php esc_html_e("Oggetto","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></h2>
					<textarea name="Oggetto" id="<?php echo esc_html_e("Oggetto","albo-pretorio-considera");?>"" rows="10" cols="255"  class="richiesto" style="width: 100%"><?php echo stripslashes($Oggetto);?></textarea>
				<label for="Riferimento" style="font-style: italic;"><?php esc_html_e("Descrizione sintetica dell'atto","albo-pretorio-considera");?> </label>
				</div><!-- /riferimentowrap -->
				<div id="richiedente">
					<h2><?php esc_html_e("Richiedente","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></h2>
					<input type="text" name="Richiedente" id="<?php echo esc_html_e("Richiedente","albo-pretorio-considera");?>" class="richiesto" style="width: 100%" value="<?php echo stripslashes($Richiedente);?>" />
				<label for="Richiedente" style="font-style: italic;"><?php esc_html_e("Dati identificativi (Nome Cognome) della persona che richiede la pubblicazione","albo-pretorio-considera");?> </label>
				</div><!-- /riferimentowrap -->
				<div id="notewrap">
					<h2><?php esc_html_e("Note","albo-pretorio-considera");?></h2>
					<div id="note-wrap">
						<?php wp_editor( stripslashes($Note), 'note_txt',
									array('wpautop'=>true,
										  'textarea_name' => 'Note',
										  'textarea_rows' => 20,
										  'teeny' => TRUE,
										  'media_buttons' => false)
										)?>
						<span style="font-style: italic;font-size: 0.8em;"><?php esc_html_e("Eventuali note a corredo dell'atto","albo-pretorio-considera");?></span>
					</div>
					</div><!-- /notewrap -->
				<div class="notewrap postbox" id="MetaDati">
				<h2 class='hndle'><span><?php esc_html_e("Meta Dati Personalizzati","albo-pretorio-considera");?></span> <button type="button" id="AddMeta" class="setta-def-data"><?php esc_html_e("Aggiungi Meta Valore","albo-pretorio-considera");?></button></h2>
					<div style="display:none;" id="newMeta">
						<label for="listaAttiMeta"><?php esc_html_e("Meta già codificati","albo-pretorio-considera");?></label> <?php echo albopc_get_elenco_attimeta("Select","listaAttiMeta","ListaAttiMeta","Si");?>
						<label for="newMetaName"><?php esc_html_e("Nome Meta","albo-pretorio-considera");?></label> <input name="newMetaName" id="newMetaName"/>
						<label for="newValue"><?php esc_html_e("Valore Meta","albo-pretorio-considera");?></label> <input name="newValue" id="newValue">
						<button type="button"class="setta-def-data" id="AddNewMeta"><?php esc_html_e("Aggiungi","albo-pretorio-considera");?></button> <button type="button"class="setta-def-data" id="UndoNewMedia"><?php esc_html_e("Anulla","albo-pretorio-considera");?></button>
					</div>
<?php				//echo albopc_get_elenco_attimeta("Div");			?>
				</div>
			</div><!-- /post-body-content -->

		<div id="postbox-container-1" class="postbox-container">
			<div id="postimagediv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Memorizza","albo-pretorio-considera");?>Memorizza</span></h2>
				<div class="inside">
					<p><?php esc_html_e("Numero Albo","albo-pretorio-considera");?>: 
						<span style="font-weight: bold;">0000000/<?php echo gmdate("Y");?></span>
					</p>
					<p class="hide-if-no-js">
					<input type="submit" name="MemorizzaDati" id="MemorizzaDati" class="button button-primary button-large" value="<?php esc_html_e("Memorizza Atto","albo-pretorio-considera");?>">
					</p>
				</div>
			</div>
			<div id="datediv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Date","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><?php esc_html_e("Data di registrazione","albo-pretorio-considera");?>:
						<input name="Data" type="text" id="CalendarioMO" value="<?php echo albopc_VisualizzaData($dataCorrente);?>" maxlength="10" size="10" />					
					</p>
					<p><abbr title="<?php esc_html_e("Data in cui inizia a validità legale dell'atto. Viene impostata automaticamente in fase di pubblicazione","albo-pretorio-considera");?>"><?php esc_html_e("Data inizio Pubblicazione","albo-pretorio-considera");?></abbr>:
						<input name="DataInizio" type="hidden" value="<?php echo $DataI;?>" />
					</p>
					<p><abbr title="<?php esc_html_e("Data fine validità legale dell'atto","albo-pretorio-considera");?>"><?php esc_html_e("Data fine Pubblicazione","albo-pretorio-considera");?></abbr>:
						<input name="DataFine" id="Calendario3" type="text" value="<?php echo $DataF;?>" maxlength="10" size="10" />	
					</p>		
					<p><abbr title="<?php esc_html_e("Data in cui l'atto viene eliminato dall'archivio, in base al Decreto n. 33/2013 art.8:<br />5 anni, decorrenti dal 1° gennaio dell'anno successivo a quello
da cui decorre l'obbligo di pubblicazione, e comunque fino a che gli atti pubblicati producono i loro effetti,
fatti salvi i diversi termini previsti dalla normativa in materia di trattamento dei dati personali e quanto
previsto dagli articoli 14, comma 2, e 15, comma 4","albo-pretorio-considera");?>"><?php esc_html_e("Data Oblio","albo-pretorio-considera");?></abbr>:
						<input name="DataOblio" id="Calendario4" type="text" value="<?php echo $DataO;?>" maxlength="10" size="10" />
						<button type="button" id="setta-def-data-o" class="setta-def-data" name="<?php echo albopc_VisualizzaData($DataOblioStandard);?>" style="margin-top: 5px;margin-left:10px;"> <?php esc_html_e("Aggiorna a","albo-pretorio-considera");?> <?php echo albopc_VisualizzaData($DataOblioStandard);?></button>	
					</p>				
				</div>
			</div>
			<div id="metadiv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Meta dati","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><abbr title="<?php esc_html_e("Ente che pubblica l'atto; potrebbe essere diverso dall'ente titolare del sito web se la pubblicazione avviene per conto di altro ente","albo-pretorio-considera");?>"><?php esc_html_e("Ente","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></abbr>: 
						<?php echo albopc_get_dropdown_enti('Ente',__('Ente','albo-pretorio-considera'),'postform maxdime richiesto ValValue(>-1)','',$defEnte);?>
					</p>
					<p><abbr title="<?php esc_html_e("Categoria in cui viene collocato l'atto, questo sistema permette di raggruppare gli oggetti in base alla lor natura","albo-pretorio-considera");?>"><?php esc_html_e("Categoria","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></abbr>:
						<?php echo albopc_get_dropdown_categorie('Categoria',__('Categoria','albo-pretorio-considera'),'postform maxdime richiesto ValValue(>0)','',$Categoria);?>					
					</p>
					<p><abbr title="<?php esc_html_e("Unità Organizzativa responsabile del procedimento amministrativo","albo-pretorio-considera");?>"><?php esc_html_e("Unità Organizzativa Responsabile","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></abbr>:
						<?php echo albopc_get_dropdown_unitao('Unitao',__("Unità Organizzativa Responsabile","albo-pretorio-considera"),'postform maxdime richiesto ValValue(>0)','',$Unitao);?>					
					</p>		
					<p><?php esc_html_e("Responsabile del procedimento amministrativo","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span>:
						<?php echo albopc_get_dropdown_responsabili("Responsabile",__("Responsabile del procedimento amministrativo","albo-pretorio-considera"),"postform maxdime richiesto ValValue(>0)","",(isset($DefaultSoggetti["RP"])?$DefaultSoggetti["RP"]:0),"RP");?>					
					</p>							
				</div>
			</div>
			<div id="metadiv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Soggetti","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><?php esc_html_e("In questo spazio bisogna codificare i soggetti che sono coinvolti in questo atto, possono essere specificati più soggetti","albo-pretorio-considera");?>
					</p>
					<ul>
<?php
		$Ana_Soggetti=albopc_get_responsabili();
		foreach($Ana_Soggetti as $Soggetto){
			if($Soggetto->Funzione!="RP"){
				$Sel="";
				if(is_array($DefaultSoggetti)And in_array($Soggetto->IdResponsabile,$DefaultSoggetti)){
					$Sel=" checked ";
				}
				echo "
				<li>
					<input type=\"checkbox\" name=\"Soggetto[]\" value=\"$Soggetto->IdResponsabile\"  $Sel/>".$Soggetto->Cognome." ".$Soggetto->Nome." <strong><em>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</em></strong>
				</li>";				
			}

		}
?>						
					</ul>
				</div>
			</div>
	</div>
	</div><!-- /post-body-content -->	
	</div>
	</form>
</div>
<?php
}


function albopc_Edit_atto($id){
$atto=albopc_get_atto($id);
$atto=$atto[0];
$DataOblioStandard=(gmdate("Y")+6)."-01-01";
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-considera");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-considera");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php esc_html_e("Correggere gli errori per continuare","albo-pretorio-considera");?></p>
</div>
<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> <?php esc_html_e("Atti","albo-pretorio-considera");?></h2>
		<a href="<?php echo site_url().'/wp-admin/admin.php?page=atti';?>" class="add-new-h2 tornaindietro"><?php esc_html_e("Torna indietro","albo-pretorio-considera");?></a>
		<div class="Obbligatori">
		<span style="color:red;font-weight: bold;">*</span> <?php esc_html_e("i campi contrassegnati dall'asterisco sono <strong>obbligatori</strong>","albo-pretorio-considera");?>
		</div>
		<h3><?php esc_html_e("Modifica Atto","albo-pretorio-considera");?></h3>	
	</div>
	<input type="hidden" id="NonVal" value="<?php esc_html_e("Non Valorizzato","albo-pretorio-considera");?>" />
	<input type="hidden" id="NonSOg" value="<?php esc_html_e("Nessun Soggetto selezionato, ne devi selezionare almeno UNO","albo-pretorio-considera");?>" />

	<form id="addatto" method="post" action="?page=atti" class="validate">
		<input type="hidden" name="action" value="memo-atto" />
		<input type="hidden" name="id" value="<?php echo (isset($_REQUEST['id'])?(int)$_REQUEST['id']:0);?>" />
		<input type="hidden" name="modificaatto" value="<?php echo wp_create_nonce('editatto')?>" />
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div id="riferimentodiv">
					<h2><?php esc_html_e("Riferimento","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></h2>
					<textarea name="Riferimento" id="<?php echo esc_html_e("Riferimento","albo-pretorio-considera");?>" rows="2" cols="255"  class="richiesto" style="width: 100%" alt="<?php echo esc_html_e("Riferimento","albo-pretorio-considera");?>"><?php echo stripslashes($atto->Riferimento);?></textarea>
				<label for="Riferimento" style="font-style: italic;"><?php esc_html_e("Codice di riferimento dell'atto, es. N. Protocollo","albo-pretorio-considera");?> </label>
				</div><!-- /riferimentodiv -->
				<div id="riferimentowrap">
					<h2><?php esc_html_e("Oggetto","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></h2>
					<textarea name="Oggetto" id="<?php echo esc_html_e("Oggetto","albo-pretorio-considera");?>" rows="10" cols="255"  class="richiesto" style="width: 100%"><?php echo stripslashes($atto->Oggetto);?></textarea>
				<label for="Riferimento" style="font-style: italic;"><?php esc_html_e("Descrizione sintetica dell'atto","albo-pretorio-considera");?> </label>
				</div><!-- /riferimentowrap -->
				<div id="richiedente">
					<h2><?php esc_html_e("Richiedente","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></h2>
					<input type="text" name="Richiedente" id="<?php echo esc_html_e("Richiedente","albo-pretorio-considera");?>" class="richiesto" style="width: 100%" value="<?php echo stripslashes($atto->Richiedente);?>" />
				<label for="Richiedente" style="font-style: italic;"><?php esc_html_e("Dati identificativi (Nome Cognome) della persona che richiede la pubblicazione","albo-pretorio-considera");?> </label>
				</div><!-- /riferimentowrap -->
				<div id="notewrap">
					<h2><?php esc_html_e("Note","albo-pretorio-considera");?></h2>
					<div id="note-wrap">
						<?php wp_editor( stripslashes($atto->Informazioni), 'note_txt',
									array('wpautop'=>true,
										  'textarea_name' => 'Note',
										  'textarea_rows' => 10,
										  'teeny' => TRUE,
										  'media_buttons' => false)
										)?>
						<span style="font-style: italic;font-size: 0.8em;"><?php esc_html_e("Note","albo-pretorio-considera");?>Eventuali note a corredo dell'atto</span>
					</div>
					</div><!-- /notewrap -->
				<div class="notewrap postbox" id="MetaDati">
				<h2 class='hndle'><span><?php esc_html_e("Meta Dati Personalizzati","albo-pretorio-considera");?></span> <button type="button" id="AddMeta" class="setta-def-data"><?php esc_html_e("Aggiungi Meta Valore","albo-pretorio-considera");?></button></h2>
					<div style="display:none;" id="newMeta">
						<label for="listaAttiMeta"><?php esc_html_e("Meta già codificati","albo-pretorio-considera");?></label> <?php echo albopc_get_elenco_attimeta("Select","listaAttiMeta","ListaAttiMeta","Si");?>
						<label for="newMetaName"><?php esc_html_e("Nome Meta","albo-pretorio-considera");?></label> <input name="newMetaName" id="newMetaName"/>
						<label for="newValue"><?php esc_html_e("Valore Meta","albo-pretorio-considera");?></label> <input name="newValue" id="newValue">
						<button type="button"class="setta-def-data" id="AddNewMeta"><?php esc_html_e("Aggiungi","albo-pretorio-considera");?></button> <button type="button"class="setta-def-data" id="UndoNewMedia"><?php esc_html_e("Anulla","albo-pretorio-considera");?></button>
					</div>
<?php				echo albopc_get_elenco_attimeta("Div","","","",$id);			?>
				</div>
			</div><!-- /post-body-content -->

		<div id="postbox-container-1" class="postbox-container">
			<div id="postimagediv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Memorizza","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><?php esc_html_e("Numero Albo","albo-pretorio-considera");?>: 
						<span style="font-weight: bold;">0000000/<?php echo $atto->Anno;?></span>
					</p>
					<p class="hide-if-no-js">
						<input type="submit" name="MemorizzaDati" id="MemorizzaDati" class="button button-primary button-large" value="<?php esc_html_e("Memorizza Modifiche Atto","albo-pretorio-considera");?>" />
					</p>
				</div>
			</div>
			<div id="datediv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Date","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><abbr title="<?php esc_html_e("viene inserita automaticamente nel momento in cui viene creato","albo-pretorio-considera");?>."><?php esc_html_e("Data di registrazione","albo-pretorio-considera");?></abbr>: 
						<input name="Data" type="text" id="CalendarioMO" value="<?php echo albopc_VisualizzaData($atto->Data);?>" maxlength="10" size="10" />
					</p>
					<p><abbr title="<?php esc_html_e("Data in cui inizia a validità legale dell'atto. Viene impostata automaticamente in fase di pubblicazione","albo-pretorio-considera");?>"><?php esc_html_e("Data inizio Pubblicazione","albo-pretorio-considera");?></abbr>:
						<input name="DataInizio" type="hidden" value="<?php echo albopc_VisualizzaData($atto->DataInizio);?>" />
						<em><strong><?php echo albopc_VisualizzaData($atto->DataInizio);?></strong></em>					
					</p>
					<p><abbr title="<?php esc_html_e("Data fine validità legale dell'atto","albo-pretorio-considera");?>"><?php esc_html_e("Data fine Pubblicazione","albo-pretorio-considera");?></abbr>:
						<input name="DataFine" id="Calendario3" type="text" value="<?php echo albopc_VisualizzaData($atto->DataFine);?>" maxlength="10" size="10" />		
					</p>		
					<p><abbr title="<?php esc_html_e("Data in cui l'atto viene eliminato dall'archivio, in base al Decreto n. 33/2013 art.8:<br />5 anni, decorrenti dal 1° gennaio dell'anno successivo a quello
da cui decorre l'obbligo di pubblicazione, e comunque fino a che gli atti pubblicati producono i loro effetti,
fatti salvi i diversi termini previsti dalla normativa in materia di trattamento dei dati personali e quanto
previsto dagli articoli 14, comma 2, e 15, comma 4","albo-pretorio-considera");?>"><?php esc_html_e("Data Oblio","albo-pretorio-considera");?></abbr>:
						<input name="DataOblio" id="Calendario4" type="text" value="<?php echo albopc_VisualizzaData($atto->DataOblio);?>" maxlength="10" size="10" />
						<button type="button" id="setta-def-data-o" class="setta-def-data" name="<?php echo albopc_VisualizzaData($DataOblioStandard);?>" style="margin-top: 5px;margin-left:10px;"> <?php esc_html_e("Aggiorna a","albo-pretorio-considera");?> <?php echo albopc_VisualizzaData($DataOblioStandard);?></button>	
					</p>				
				</div>
			</div>
			<div id="metadiv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Meta dati","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><abbr title="<?php esc_html_e("Ente che pubblica l'atto; potrebbe essere diverso dall'ente titolare del sito web se la pubblicazione avviene per conto di altro ente","albo-pretorio-considera");?>"><?php esc_html_e("Ente","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></abbr>: 
						<?php echo albopc_get_dropdown_enti('Ente',__("Ente","albo-pretorio-considera"),'postform maxdime richiesto ValValue(>-1)','',$atto->Ente);?>
					</p>
					<p><abbr title="<?php esc_html_e("Categoria in cui viene collocato l'atto, questo sistema permette di raggruppare gli oggetti in base alla lor natura","albo-pretorio-considera");?>"><?php esc_html_e("Categoria","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></abbr>:
						<?php echo albopc_get_dropdown_categorie('Categoria',__("Categoria","albo-pretorio-considera"),'postform maxdime richiesto ValValue(>0)','',$atto->IdCategoria);?>					
					</p>
					<p><abbr title="<?php esc_html_e("Unità Organizzativa responsabile del procedimento amministrativo","albo-pretorio-considera");?>"><?php esc_html_e("Unità Organizzativa Responsabile","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></abbr>:
						<?php echo albopc_get_dropdown_unitao('Unitao',__("Unità Organizzativa Responsabile","albo-pretorio-considera"),'postform maxdime richiesto ValValue(>0)','',$atto->IdUnitaOrganizzativa);?>					
					</p>
					<p><?php esc_html_e("Responsabile del procedimento amministrativo","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span>:
						<?php echo albopc_get_dropdown_responsabili("Responsabile",__("Responsabile del procedimento amministrativo","albo-pretorio-considera"),"postform maxdime richiesto ValValue(>0)","",$atto->RespProc,"RP");?>					
					</p>	
				</div>
			</div>
			<div id="metadiv" class="postbox " >
				<h2 class='hndle'><span><?php esc_html_e("Soggetti","albo-pretorio-considera");?></span></h2>
				<div class="inside">
					<p><?php esc_html_e("In questo spazio bisogna codificare i soggetti che sono coinvolti in questo atto possono essere specificati più soggetti.","albo-pretorio-considera");?>
					</p>
					<ul>
<?php
		$Soggetti=unserialize($atto->Soggetti, array('allowed_classes'=>false));
		$Ana_Soggetti=albopc_get_responsabili();
		foreach($Ana_Soggetti as $Soggetto){
			if($Soggetto->Funzione!="RP"){
				$Selected="";
				if (is_array($Soggetti)And in_array($Soggetto->IdResponsabile,$Soggetti)) {
					$Selected=" checked ";
				}
				echo "
				<li>
					<input type=\"checkbox\" name=\"Soggetto[]\" value=\"$Soggetto->IdResponsabile\"  $Selected/>".$Soggetto->Cognome." ".$Soggetto->Nome." <strong><em>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</em></strong>
				</li>";				
			}
		}
?>						
					</ul>
				</div>
			</div>
	</div>
	</div><!-- /post-body-content -->	
	</div>
	</form>
</div>
<?php	
}

function albopc_Allegati_atto($IdAtto,$messaggio="",$IdAllegato=0){
	$risultato=albopc_get_atto($IdAtto);
	$risultato=$risultato[0];
	$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$dirUpload =  get_option('opt_AP_FolderUpload').'/';
	echo '
<div class="wrap">

	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> '. __("Atti","albo-pretorio-considera").'</h2>
		<a href="'. site_url().'/wp-admin/admin.php?page=atti&stato_atti=Nuovi" class="add-new-h2 tornaindietro">'. __("Torna indietro","albo-pretorio-considera").'</a>
		<h3>'. __("Allegati Atto","albo-pretorio-considera").'</h3>	
	</div>';
if ( $messaggio!="" ) {
	 	$messaggio=str_replace("%%br%%", "<br />", $messaggio);
		print('<div id="message" class="updated"><p>'.$messaggio.'</p></div>');
		$_SERVER['REQUEST_URI'] = remove_query_arg(array('messaggio'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
	}
echo'
<div id="col-container">
<div id="col-right">
<div class="col-wrap">';
if ($IdAllegato!=0){
 	$allegato=albopc_get_allegato_atto($IdAllegato);
 	$allegato=$allegato[0];
	echo '<h3>'. __("Modifica Allegato","albo-pretorio-considera").'</h3>
	<form id="allegato"  method="post" action="?page=atti" class="validate">
	<input type="hidden" name="action" value="update-allegato-atto" />
	<input type="hidden" name="id" value="'.esc_attr($IdAtto).'" />
	<input type="hidden" name="idAlle" value="'.esc_attr($IdAllegato).'" />
	<input type="hidden" name="modificaallegatoatto" value="'.esc_attr(wp_create_nonce("editallegatoatto")).'" />
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="3" style="text-align:center;font-size:1.2em;">'. __("Dati Allegato","albo-pretorio-considera").'</th>
		</tr>
	    </thead>
	    <tbody id="dati-allegato">
		<tr>
			<th>'. __("Descrizione Allegato","albo-pretorio-considera").'</th>
			<td><textarea  name="titolo" rows="4" cols="50" wrap="ON" maxlength="255">'.$allegato->TitoloAllegato.'</textarea></td>
		</tr>
		<tr>
			<th>'. __("Natura File","albo-pretorio-considera").'</th>
			<td><select name="Natura" id="Natura" wrap="ON" >
				<option value="D" '.($allegato->Natura=="D"?"selected":"").'>Documento firmato</option>
				<option value="A" '.($allegato->Natura=="A"?"selected":"").'>Allegato</option>
			</select></td>
		</tr>
		<tr>
			<th>'. __("Documento Integrale?","albo-pretorio-considera").'</th>
			<td><input type="checkbox" name="Integrale" value="1" id="Integrale" '.($allegato->DocIntegrale=="1"?"checked":"").'></td>
		</tr>
		<tr>
			<th>'. __("File","albo-pretorio-considera").':</th>
			<td>'.$allegato->Allegato.'</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="submit" id="submit" class="button" value="'. __("Aggiorna Allegato","albo-pretorio-considera").'"  />&nbsp;&nbsp;
			    <input type="submit" name="annulla" id="annulla" class="button" value="'. __("Annulla Operazione","albo-pretorio-considera").'"  />
		    </td>
		</tr>
	    </tbody>
	</table>
	</form>';	
}else{
	echo'
	<h3 style="margin-top:50px;">Allegati <a href="'.site_url().'/wp-admin/admin.php?page=atti&amp;id='.$IdAtto.'&amp;action=UpAllegati" class="add-new-h2">'. __("Aggiungi nuovo","albo-pretorio-considera").'</a> <a href="'.site_url().'/wp-admin/admin.php?page=atti&amp;id='.$IdAtto.'&amp;action=AssAllegati" class="add-new-h2">'. __("Associa file","albo-pretorio-considera").'</a></h3>';
	$righe=albopc_get_all_allegati_atto($IdAtto);
	echo'
	<div  style="overflow: scroll;">
		<table class="widefat">
		    <thead>
			<tr>
				<th style="font-size:1.2em;">'. __("Operazioni","albo-pretorio-considera").'</th>
				<th style="font-size:1.2em;">'. __("Allegato","albo-pretorio-considera").'</th>
				<th style="font-size:1.2em;">'. __("File","albo-pretorio-considera").'</th>
				<th style="font-size:1.2em;">'. __("Natura doc.","albo-pretorio-considera").'</th>
				<th style="font-size:1.2em;">'. __("Doc. Integrale","albo-pretorio-considera").'</th>
				<th style="font-size:1.2em;">'. __("Impronta","albo-pretorio-considera").'</th>
			</tr>
		    </thead>
		    <tbody id="righe-log">';
	foreach ($righe as $riga) {
		$Testo_da=__("Confermi la cancellazione del'Allegato","albo-pretorio-considera")." ".wp_strip_all_tags($riga->TitoloAllegato). "?\n\n".__("ATTENZIONE questa operazione cancellera' anche il file sul server!","albo-pretorio-considera")."\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-pretorio-considera");
		echo '<tr>
				<td>	
					<a href="?page=atti&amp;action=delete-allegato-atto&amp;idAllegato='.$riga->IdAllegato.'&amp;idAtto='.$IdAtto.'&amp;Allegato='.$riga->TitoloAllegato.'&amp;cancellaallegatoatto='.wp_create_nonce('deleteallegatoatto').'" rel="'.$Testo_da.'" class="confdel">
						<span class="dashicons dashicons-trash" title="'. __("Cancella allegato","albo-pretorio-considera").'"></span>
					</a>
					<a href="?page=atti&amp;action=edit-allegato-atto&amp;id='.$IdAtto.'&amp;idAlle='.$riga->IdAllegato.'&amp;modificaallegatoatto='.wp_create_nonce('editallegatoatto').'" >
						 <span class="dashicons dashicons-edit" title="'. __("Modifica allegato","albo-pretorio-considera").'"></span>
					</a>
					<a href="'.albopc_DaPath_a_URL($riga->Allegato).'" target="_blank">
							<span class="dashicons dashicons-search" title="'. __("Visualizza dati allegato","albo-pretorio-considera").'"></span>
					</a>
				</td>
				<td >'.$riga->TitoloAllegato.'</td>
				<td >'. basename( $riga->Allegato).'</td>
				<td >'. basename( $riga->Natura=="A"?__("Allegato","albo-pretorio-considera"):__("Doc. Firmato","albo-pretorio-considera")).'</td>
				<td >'. basename( $riga->DocIntegrale==1?__("Si","albo-pretorio-considera"):__("No","albo-pretorio-considera")).'</td>
				<td style="font-family: courier;">'. basename( $riga->Impronta).'</td>
			</tr>';
	}
	echo '    </tbody>
		</table>
	</div>';
}
$Ente=albopc_get_ente($risultato->Ente);
$Unitao=albopc_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
$NomeResp=albopc_get_responsabile($risultato->RespProc);
$NomeResp=$NomeResp[0];
echo'</div>
</div>
<div id="col-left">
<div class="col-wrap">
<h3>'. __("Dati Atto","albo-pretorio-considera").'</h3>
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="2" style="text-align:center;font-size:1.2em;">'. __("Dati atto","albo-pretorio-considera").'</th>
		</tr>
	    </thead>
	    <tbody id="dati-atto">
		<tr>
			<th>'.__("Ente emittente","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($Ente->Nome).'</td>
		</tr>
		<tr>
			<th style="width:50%;">'. __("Numero Albo","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
		</tr>
		<tr>
			<th>'. __("Codice di Riferimento","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Riferimento).'</td>
		</tr>
		<tr>
			<th>'. __("Oggetto","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Oggetto).'</td>
		</tr>
		<tr>
			<th>'. __("Data di registrazione","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->Data).'</td>
		</tr>
		<tr>
			<th>'. __("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataInizio).'</td>
		</tr>
		<tr>
			<th>'. __("Data fine Pubblicazione","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataFine).'</td>
		</tr>
		<tr>
			<th>'. __("Data oblio","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataOblio).'</td>
		</tr>
		<tr>
			<th>'.__("Richiedente","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Richiedente).'</td>
		</tr>
		<tr>
			<th>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($Unitao->Nome).'</td>
		</tr>
		<tr>
			<th>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($NomeResp->Nome." ".$NomeResp->Cognome).'</td>
		</tr>
		<tr>
			<th>'. __("Categoria","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultatocategoria->Nome).'</td>
		</tr>
		<tr>
			<th>'. __("Note","albo-pretorio-considera").'</th>
			<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Informazioni).'</td>
		</tr>';
$MetaDati=albopc_get_meta_atto($IdAtto);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);
	echo'
			<tr>
				<th>'. __("Meta Dati","albo-pretorio-considera").'</th>
				<td style="vertical-align: middle;color: Red;">'.$Meta.'</td>
			</tr>';
}	
	echo'	<tr>
				<th>'. __("Soggetti","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">
				<ul>';
	$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
	$Soggetti=(is_array($Soggetti) && !empty($Soggetti)) ? albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti)) : array();
	foreach((array)$Soggetti as $Soggetto){
		echo "
			<li><strong>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</strong> <br />".$Soggetto->Nome." ".$Soggetto->Cognome." 
			</li>";
	}
echo'				
				</ul>
				</td>
			</tr>	    
	    </tbody>
	</table></div>
</div>
</div>
</div>';	
}
function albopc_View_atto($IdAtto){
	global $albopc_AP_OnLine;
if (isset($_REQUEST['stato_atti']))
	$Prov=sanitize_text_field(wp_unslash($_REQUEST['stato_atti'] ?? ''));
else
	$Prov="DaPubblicare";
$risultato=albopc_get_atto($IdAtto);
$risultato=$risultato[0];
$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
$risultatocategoria=$risultatocategoria[0];
$NomeEnte=albopc_get_ente($risultato->Ente);
$NomeEnte=stripslashes($NomeEnte->Nome);
$Ente=albopc_get_ente($risultato->Ente);
$Unitao=albopc_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
$NomeResp=albopc_get_responsabile($risultato->RespProc);
if(isset($NomeResp[0]))
	$NomeResp=$NomeResp[0];
else
	$NomeResp="";
echo '
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> Atti</h2>
		<a href="'.site_url().'/wp-admin/admin.php?page=atti&stato_atti='.$Prov.'" class="add-new-h2 tornaindietro">'. __("Torna indietro","albo-pretorio-considera").'</a>
		<h3>'. __("Visualizza dati Atto","albo-pretorio-considera").'</h3>	
	</div>
		<div class="clear"><br /></div>
		<div id="col-container">
			<div id="col-right">
				<div class="col-wrap postbox" style="padding:0 10px 10px 10px;margin-left:10px;">
					<h3>Log</h3>
					<hr />
					<div id="utility-tabs-container">
						<ul>
							<li><a href="#log-tab-1">'. __("Atto","albo-pretorio-considera").'</a></li>
							<li><a href="#log-tab-2">'. __("Allegati","albo-pretorio-considera").'</a></li>
							<li><a href="#log-tab-3">'. __("Statistiche Visite","albo-pretorio-considera").'</a></li>
							<li><a href="#log-tab-4">'. __("Statistiche Download","albo-pretorio-considera").'</a></li>
						</ul>
						<div id="log-tab-1">
							<div id="DatiLog">'.$albopc_AP_OnLine->CreaLog(1,$IdAtto,0).'</div>
						</div>
						<div id="log-tab-2">
							<div id="DatiLog">'.$albopc_AP_OnLine->CreaLog(3,$IdAtto,0).'</div>
						</div>
						<div id="log-tab-3">
							<div id="DatiLog">'.$albopc_AP_OnLine->CreaLog(5,$IdAtto,0).'</div>
						</div>
						<div id="log-tab-4">
							<div id="DatiLog">'.$albopc_AP_OnLine->CreaLog(6,$IdAtto,0).'</div>
						</div>
					</div>
				</div>
			</div>
<div id="col-left">
	<div class="col-wrap postbox" style="padding:0 10px 10px 10px;">
		<h3>'. __("Dati atto","albo-pretorio-considera").'</h3>
		<hr />
		<table class="widefat fixed striped" style="border:0;">
		    <tbody id="dati-atto">
			<tr>
				<th style="width:50%;">'. __("Ente emittente","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_removeCaratteriSpeciali($NomeEnte).'</td>
			</tr>
			<tr>
				<th style="width:20%;">'. __("Numero Albo","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
			</tr>
			<tr>
				<th>'. __("Codice di Riferimento","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes(albopc_removeCaratteriSpeciali($risultato->Riferimento)).'</td>
			</tr>
			<tr>
				<th>'. __("Oggetto","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes(albopc_removeCaratteriSpeciali($risultato->Oggetto)).'</td>
			</tr>';
		if($risultato->DataAnnullamento!='0000-00-00')		
			echo '		<tr>
				<th style="width:20%;">'. __("Data Annullamento","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataAnnullamento).'</td>
			</tr>
	    	<tr>
				<th style="width:20%;">'. __("Motivo Annullamento","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:top;">'.stripslashes(albopc_removeCaratteriSpeciali($risultato->MotivoAnnullamento)).'</td>
			</tr>';
		echo '		
			<tr>
				<th>'. __("Data di registrazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->Data).'</td>
			</tr>

			<tr>
				<th>'. __("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataInizio).'</td>
			</tr>
			<tr>
				<th>'. __("Data fine Pubblicazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataFine).'</td>
			</tr>
			<tr>
				<th>'. __("Data Oblio","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataOblio).'</td>
			</tr>
			<tr>
				<th>'.__("Richiedente","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes(albopc_removeCaratteriSpeciali($risultato->Richiedente)).'</td>
			</tr>
			<tr>
				<th>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.(isset($Unitao->Nome)?stripslashes(albopc_removeCaratteriSpeciali($Unitao->Nome)):"").'</td>
			</tr>
			<tr>
				<th>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.(is_object($NomeResp)?stripslashes(albopc_removeCaratteriSpeciali($NomeResp->Nome))." ".stripslashes(albopc_removeCaratteriSpeciali($NomeResp->Cognome)):stripslashes(albopc_removeCaratteriSpeciali($NomeResp))).'</td>
			</tr>
			<tr>
				<th>'. __("Categoria","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes(albopc_removeCaratteriSpeciali($risultatocategoria->Nome)).'</td>
			</tr>
			<tr>
				<th>'. __("Note","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes(albopc_removeCaratteriSpeciali($risultato->Informazioni)).'</td>
			</tr>';
$MetaDati=albopc_get_meta_atto($IdAtto);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);
		echo'
				<tr>
					<th>'. __("Meta Dati","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;color: Red;">'.$Meta.'</td>
				</tr>';
}
echo'
			<tr>
				<th>'. __("Soggetti","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">
				<ul>';
	$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
	if ($Soggetti){
		$Soggetti=albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
		foreach($Soggetti as $Soggetto){
			echo "
				<li><strong>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</strong><br />".$Soggetto->Nome." ".$Soggetto->Cognome." 
				</li>";
		}
	}
	echo'				
					</ul>
					</td>
				</tr>	    
				</tbody>
			</table>
		</div>';	
		$documenti=albopc_get_documenti_atto($IdAtto);
		if(count($documenti)>0){
			echo '<div class="postbox" style="padding:0 10px 10px 10px;">
				<h3>'. __("Documenti firmati","albo-pretorio-considera").'</h3>
				<div class="Visalbo">';
			$TipidiFiles=albopc_get_tipidifiles();
			foreach ($documenti as $allegato) {
				$Estensione=albopc_ExtensionType($allegato->Allegato);	
				echo '<div style="border: thin dashed;font-size: 1em;">
						<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
							<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30" />
						</div>
						<div style="margin-top:0;">
							<p style="margin-top:0;">
							'.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'
							'.wp_strip_all_tags($allegato->TitoloAllegato).' <br />';
						if (is_file($allegato->Allegato))
							echo '        <a href="'.albopc_DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.albopc_Formato_Dimensione_File(filesize($allegato->Allegato)).')<br />'.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']);
						else{
							echo basename( $allegato->Allegato)."<br />";
							if( $allegato->Note=="")
								echo __("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
							else
								echo __("Note:","albo-pretorio-considera")." ".$allegato->Note;
						}
			echo'				</p>
						</div>
					</div>';
			}
			echo '</div>
	</div>';
		}
		$allegati=albopc_get_allegati_atto($IdAtto);
		if(count($allegati)>0){
			echo '<div class="postbox" style="padding:0 10px 10px 10px;">
				<h3>'. __("Allegati","albo-pretorio-considera").'</h3>
				<div class="Visalbo">';
			$TipidiFiles=albopc_get_tipidifiles();
			foreach ($allegati as $allegato) {
				$Estensione=albopc_ExtensionType($allegato->Allegato);	
				echo '<div style="border: thin dashed;font-size: 1em;">
						<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
							<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30" />		
						</div>
						<div style="margin-top:0;">
							<p style="margin-top:0;">
								'.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'
								'.wp_strip_all_tags($allegato->TitoloAllegato).' <br />';
						if (is_file($allegato->Allegato))
							echo '        <a href="'.albopc_DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.albopc_Formato_Dimensione_File(filesize($allegato->Allegato)).')<br />'.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']);
						else{
							echo basename( $allegato->Allegato)."<br />";
							if( $allegato->Note=="")
								echo __("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
							else
								echo __("Note:","albo-pretorio-considera")." ".$allegato->Note;
						}
			echo'				</p>
						</div>
					</div>';
			}
			echo '</div>
	</div>';
		}
echo '</div>
	</div>
</div>';	
}


function albopc_CancellaAllegatiAtto($IdAtto){
	global $albopc_AP_OnLine;
	$risultato=albopc_get_atto($IdAtto);
	$risultato=$risultato[0];
	$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$NomeEnte=albopc_get_ente($risultato->Ente);
	$NomeEnte=stripslashes($NomeEnte->Nome);
	$Ente=albopc_get_ente($risultato->Ente);
	$Unitao=albopc_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
	$NomeResp=albopc_get_responsabile($risultato->RespProc);
	if(isset($NomeResp[0]))
		$NomeResp=$NomeResp[0];
	else
		$NomeResp="";
	echo '
<div class="wrap nosubsub">
	<input type="hidden" id="IdAtto" value="'.esc_attr($IdAtto).'" />
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> Atti</h2>
		<a href="'.site_url().'/wp-admin/admin.php?page=atti&stato_atti='.sanitize_text_field(filter_input(INPUT_GET,"stato_atti")).'" class="add-new-h2 tornaindietro">'. __("Torna indietro","albo-pretorio-considera").'</a>
		<h3>'. __("Dati Atto","albo-pretorio-considera").'</h3>	
	</div>
		<div class="clear"><br /></div>
		<div id="col-container">
		<div id="col-right">
				<div class="col-wrap postbox" style="padding:0 10px 10px 10px;margin-left:10px;">
				<h3>Documenti</h3>
				<hr />';
		$documenti=albopc_get_documenti_atto($IdAtto);
		if(count($documenti)>0){
			echo '<div class="postbox" style="padding:0 10px 10px 10px;">
				<h3>'. __("Documenti firmati","albo-pretorio-considera").'</h3>
				<div class="Visalbo">';
			$TipidiFiles=albopc_get_tipidifiles();
			foreach ($documenti as $allegato) {
				$Estensione=albopc_ExtensionType($allegato->Allegato);	
				echo '<div style="border: thin dashed;font-size: 1em;">
						<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
							<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30" />
						</div>
						<div style="margin-top:0;">
							<p style="margin-top:0;">
							'.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'
							'.wp_strip_all_tags($allegato->TitoloAllegato).' <br />';
						if (is_file($allegato->Allegato))
							echo '        <a href="'.albopc_DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.albopc_Formato_Dimensione_File(filesize($allegato->Allegato)).')<br />'.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).'</p>
						<p>
							<label for="motivo'.$allegato->IdAllegato.'" style="vertical-align: text-top;" id="LblIDA'.$allegato->IdAllegato.'">Indicare il motivo della rimozione del Documento Firmato</label>
							<input type="text" id="motivo'.$allegato->IdAllegato.'" name="motivo'.$allegato->IdAllegato.'" size="50" style="border: 1px solid #d63638;"/>
							<input type="hidden" id="IDA'.$allegato->IdAllegato.'" name="IDA'.$allegato->IdAllegato.'" value="'.esc_attr($allegato->IdAllegato).'"/>
							<span class="dashicons dashicons-trash CancellaAllegato" title="Elimina Allegato" style="color:red;cursor: -webkit-grab; cursor: grab;" id="'.$allegato->IdAllegato.'" rel="'.wp_strip_all_tags($allegato->TitoloAllegato).'"></span><br id="SR'.$allegato->IdAllegato.'" />';
						else
							echo __("Documento Cancellato","albo-pretorio-considera")."<br />";
			echo ' Note: <span id="Note'.$allegato->IdAllegato.'">'.$allegato->Note.'</span>
						</p>		
						</div>
					</div>';
			}
			echo '</div>
	</div>';
		}
		$allegati=albopc_get_allegati_atto($IdAtto);
		if(count($allegati)>0){
			echo '<div class="postbox" style="padding:0 10px 10px 10px;">
				<h3>'. __("Allegati","albo-pretorio-considera").'</h3>
				<div class="Visalbo">';
			$TipidiFiles=albopc_get_tipidifiles();
			foreach ($allegati as $allegato) {
				$Estensione=albopc_ExtensionType($allegato->Allegato);	
				echo '<div style="border: thin dashed;font-size: 1em;">
						<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
							<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30" />		
						</div>
						<div style="margin-top:0;">
							<p style="margin-top:0;">
								'.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'
								'.wp_strip_all_tags($allegato->TitoloAllegato).' <br />';
						if (is_file($allegato->Allegato))
							echo '        <a id="file'.$allegato->IdAllegato.'" href="'.albopc_DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.albopc_Formato_Dimensione_File(filesize($allegato->Allegato)).')</p>
						<p>
							<label for="motivo'.$allegato->IdAllegato.'" style="vertical-align: text-top;" id="LblIDA'.$allegato->IdAllegato.'">Indicare il motivo della rimozione dell\'allegato</label>
							<input type="text" id="motivo'.$allegato->IdAllegato.'" name="motivo'.$allegato->IdAllegato.'" size="50" style="border: 1px solid #d63638;"/>
							<input type="hidden" id="IDA'.$allegato->IdAllegato.'" name="IDA'.$allegato->IdAllegato.'" value="'.esc_attr($allegato->IdAllegato).'"/>
							<span class="dashicons dashicons-trash CancellaAllegato" title="Elimina Allegato" style="color:red;cursor: -webkit-grab; cursor: grab;" id="'.$allegato->IdAllegato.'" rel="'.wp_strip_all_tags($allegato->TitoloAllegato).'"></span><br id="SR'.$allegato->IdAllegato.'" />';
						else
							echo __("Allegato Cancellato","albo-pretorio-considera")."<br />";
						echo ' Note: <span id="Note'.$allegato->IdAllegato.'">'.$allegato->Note.'</span>
						</p>		
						</div>
					</div>';
			}
			echo '</div>
	</div>';
		}
	echo'			</div>
	</div>
<div id="col-left">
	<div class="col-wrap postbox" style="padding:0 10px 10px 10px;">
		<h3>'. __("Dati atto","albo-pretorio-considera").'</h3>
		<hr />
		<table class="widefat fixed striped" style="border:0;">
		    <tbody id="dati-atto">
			<tr>
				<th style="width:50%;">'. __("Ente emittente","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$NomeEnte.'</td>
			</tr>
			<tr>
				<th style="width:20%;">'. __("Numero Albo","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
			</tr>
			<tr>
				<th>'. __("Codice di Riferimento","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Riferimento).'</td>
			</tr>
			<tr>
				<th>'. __("Oggetto","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Oggetto).'</td>
			</tr>';
		if($risultato->DataAnnullamento!='0000-00-00')		
			echo '		<tr>
				<th style="width:20%;">'. __("Data Annullamento","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataAnnullamento).'</td>
			</tr>
	    	<tr>
				<th style="width:20%;">'. __("Motivo Annullamento","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-weight: bold;color: Red;vertical-align:top;">'.stripslashes($risultato->MotivoAnnullamento).'</td>
			</tr>';
		echo '		
			<tr>
				<th>'. __("Data di registrazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->Data).'</td>
			</tr>

			<tr>
				<th>'. __("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataInizio).'</td>
			</tr>
			<tr>
				<th>'. __("Data fine Pubblicazione","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataFine).'</td>
			</tr>
			<tr>
				<th>'. __("Data Oblio","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataOblio).'</td>
			</tr>
			<tr>
				<th>'.__("Richiedente","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Richiedente).'</td>
			</tr>
			<tr>
				<th>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.(isset($Unitao->Nome)?stripslashes($Unitao->Nome):"").'</td>
			</tr>
			<tr>
				<th>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</th>
				<td style="font-size:14px;font-style: italic;color: Blue;vertical-align:middle;">'.(is_object($NomeResp)?$NomeResp->Nome." ".$NomeResp->Cognome:$NomeResp).'</td>
			</tr>
			<tr>
				<th>'. __("Categoria","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultatocategoria->Nome).'</td>
			</tr>
			<tr>
				<th>'. __("Note","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Informazioni).'</td>
			</tr>';
$MetaDati=albopc_get_meta_atto($IdAtto);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);
		echo'
				<tr>
					<th>'. __("Meta Dati","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;color: Red;">'.$Meta.'</td>
				</tr>';
}
echo'
			<tr>
				<th>'. __("Soggetti","albo-pretorio-considera").'</th>
				<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">
				<ul>';
	$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
	if ($Soggetti){
		$Soggetti=albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
		foreach($Soggetti as $Soggetto){
			echo "
				<li><strong>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</strong><br />".$Soggetto->Nome." ".$Soggetto->Cognome." 
				</li>";
		}
	}
	echo'				
					</ul>
					</td>
				</tr>	    
				</tbody>
			</table>
		</div>';	

echo '</div>
	</div>
</div>';	
}
function albopc_annulla_atto_page($IdAtto){
	global $albopc_AP_OnLine;
	$risultato=albopc_get_atto($IdAtto);
	$risultato=$risultato[0];
	$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$NomeEnte=albopc_get_ente($risultato->Ente);
	$NomeEnte=stripslashes($NomeEnte->Nome);
	echo '
<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> '. __("Atti","albo-pretorio-considera").'</h2>
		<a href="'.site_url().'/wp-admin/admin.php?page=atti&amp;stato_atti=Correnti" class="add-new-h2 tornaindietro">Torna indietro</a>
		<h3>'. __("Annulla Atto","albo-pretorio-considera").'</h3>	
	</div>
	<div id="col-container">
		<div class="clear"><br /></div>
		<form id="annullaatto" method="post" action="?page=atti" class="validate">
		<input type="hidden" name="action" value="annulla-atto" />
		<input type="hidden" name="id" value="'.(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0).'" />
		<input type="hidden" name="annatto" value="'.esc_attr(wp_create_nonce('annatto')).'" />
		<table class="widefat">
		    <thead>
		    <tr>
				<th style="text-align:center;font-size:1.2em;width:50%;">'. __("Dati atto","albo-pretorio-considera").'</th>
				<th style="font-size:1.2em;">'. __("Allegati atto","albo-pretorio-considera").'</th>
			</tr>
		    </thead>
		    <tbody>
		    <tr>
		    <td style="border-right-style: groove;border-right-width: thin;">
		    	<table>
				<tr>
					<th style="width:20%;">'. __("Ente emittente","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$NomeEnte.'</td>
				</tr>
				<tr>
					<th style="width:20%;">'. __("Numero Albo","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.$risultato->Numero."/".$risultato->Anno.'</td>
				</tr>
				<tr>
					<th>'. __("Data","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->Data).'</td>
				</tr>
				<tr>
					<th>'. __("Codice di Riferimento","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Riferimento).'</td>
				</tr>
				<tr>
					<th>'. __("Oggetto","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Oggetto).'</td>
				</tr>
				<tr>
					<th>'. __("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataInizio).'</td>
				</tr>
				<tr>
					<th>'. __("Data fine Pubblicazione","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataFine).'</td>
				</tr>
				<tr>
					<th>'. __("Data Oblio","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.albopc_VisualizzaData($risultato->DataOblio).'</td>
				</tr>
				<tr>
					<th>'. __("Note","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultato->Informazioni).'</td>
				</tr>
				<tr>
					<th>'. __("Categoria","albo-pretorio-considera").'</th>
					<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">'.stripslashes($risultatocategoria->Nome).'</td>
				</tr>
				<tr>
					<th>'. __("Soggetti","albo-pretorio-considera").'</th>
						<td style="font-size:12px;font-style: italic;color: Blue;vertical-align:middle;">	
					<ul>';
	$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
	$Soggetti=(is_array($Soggetti) && !empty($Soggetti)) ? albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti)) : array();
	foreach((array)$Soggetti as $Soggetto){
		echo "
			<li><strong>".albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")."</strong> ".$Soggetto->Nome." ".$Soggetto->Cognome." 
			</li>";
	}
echo'				
				</ul>
					</td>
				</tr>
				</table>	    
			</td>
			<td>
			<p style="color:red;font-weight: bold;">'. __("Selezionare gli allegati che devono essere cancellati per violazione di legge<br />NB: verrà cancellato solo il file, mentre sarà mantenuto il nome del file nell'elenco degli allegati","albo-pretorio-considera").'</p>';
$allegati=albopc_get_all_allegati_atto($IdAtto);
$TipidiFiles=albopc_get_tipidifiles();
foreach ($allegati as $allegato) {
	$Estensione=albopc_ExtensionType($allegato->Allegato);	
	echo '<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
			<input type="checkbox" name="Alle:'.$allegato->IdAllegato.'" value="'.esc_attr($allegato->IdAllegato).'">
		  </div>
			<div style="float: left;display: inline;width: 40px;height: 40px;padding-top:5px;padding-left:5px;">
				<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"/>
			</div>
			<div style="margin-top:0;">
				<p style="margin-top:0;">'.wp_strip_all_tags($allegato->TitoloAllegato).' <br />';
			if (is_file($allegato->Allegato))
				echo '        <a href="'.albopc_DaPath_a_URL($allegato->Allegato).'" >'. basename( $allegato->Allegato).'</a> ('.albopc_Formato_Dimensione_File(filesize($allegato->Allegato)).')<br />'.htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']);
			else
				echo basename( $allegato->Allegato)." ".__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
echo'				</p>
			</div>';
	}			
echo'			</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;border-top-style: groove;border-top-width: thin;">
				<span style="color:red;font-size:2em;font-weight: bold;">'. __("Motivo Annullamento","albo-pretorio-considera").'</span><br />
					<textarea rows="4" cols="100"  maxlength="255" placeholder="Inserisci il motivo, massimo 255 caratteri" id="Motivo" name="Motivo" ></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="submit" name="submit" id="submit" class="button" value="'. __("Annulla Pubblicazione Atto","albo-pretorio-considera").'"  />
					<input type="submit" name="submit" id="submit" class="button" value="'. __("Annulla Operazione","albo-pretorio-considera").'"  />
				<td>
			</tr>
			</tbody>
		</table>
		</form>
		</div>
		<div class="col-wrap">
			<h3>Log</h3>
					<div id="utility-tabs-container">
						<ul>
							<li><a href="#log-tab-1">'. __("Atto","albo-pretorio-considera").'</a></li>
							<li><a href="#log-tab-2">'. __("Allegati","albo-pretorio-considera").'</a></li>
							<li><a href="#log-tab-3">'. __("Statistiche Visite","albo-pretorio-considera").'</a></li>
							<li><a href="#log-tab-4">'. __("Statistiche Download","albo-pretorio-considera").'</a></li>
						</ul>
						<div id="log-tab-1">
							<div id="DatiLog1">'.$albopc_AP_OnLine->CreaLog(1,$IdAtto,0).'</div>
						</div>
						<div id="log-tab-2">
							<div id="DatiLog2">'.$albopc_AP_OnLine->CreaLog(3,$IdAtto,0).'</div>
						</div>
						<div id="log-tab-3">
							<div id="DatiLog3">'.$albopc_AP_OnLine->CreaLog(5,$IdAtto,0).'</div>
						</div>
						<div id="log-tab-4">
							<div id="DatiLog4">'.$albopc_AP_OnLine->CreaLog(6,$IdAtto,0).'</div>
						</div>
					 </div>
		</div>
</div>';	
}

function albopc_Lista_Atti($Msg_op=""){
if (isset($_REQUEST['p']))
	$Pag=(isset($_REQUEST['p'])?intval($_REQUEST['p']):0);
else
	$Pag=0;
$Message[0] = __("Messaggio non definito","albo-pretorio-considera");
$messages[1] = __("Atto Aggiunto","albo-pretorio-considera");
$messages[2] = __("Atto Cancellato","albo-pretorio-considera");
$messages[3] = __("Atto Aggiornato","albo-pretorio-considera");
$messages[4] = __("Atto non Aggiunto","albo-pretorio-considera");
$messages[5] = __("Atto non Aggiornato","albo-pretorio-considera");
$messages[6] = __("Atto non Cancellato","albo-pretorio-considera");
$messages[7] = __("Impossibile cancellare un Atto che contiene Allegati<br />Cancellare prima gli Allegati e poi riprovare","albo-pretorio-considera");
$messages[8] = __("Impossibile ANULLARE l'Atto","albo-pretorio-considera");
$messages[9] = __("Atto ANNULLATO","albo-pretorio-considera");
$messages[10] = __("Allegati all'Atto Cancellati","albo-pretorio-considera");
$messages[11] = __("Allegati all'Atto NON Cancellati","albo-pretorio-considera");
$messages[12] = __("Metadati dell'Atto Memorizzati","albo-pretorio-considera");
$messages[13] = __("Metadati dell'Atto NON Memorizzati","albo-pretorio-considera");
$messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
$messages[99] = __("OPERAZIONE NON AMMESSA!<br />l'atto non è ancora da eliminare","albo-pretorio-considera");
//Gestione Messaggi di stato
if (isset($_REQUEST['message'])) 
	$msg = (isset($_REQUEST['message'])?intval($_REQUEST['message']):0);
if (isset($_REQUEST['message2'])) 
	$msg2 = (isset($_REQUEST['message2'])?intval($_REQUEST['message2']):0);
if (isset($_REQUEST['errore']))
	$Errore=sanitize_text_field(wp_unslash($_REQUEST['errore'] ?? ''));
if ($Msg_op!=""){
	if (is_numeric($Msg_op))
		$msg=$Msg_op;
	else{
		$msg =9;
		$messages[9]=(str_replace("%%br%%","<br />",sanitize_text_field($Msg_op)));	
	}
}
?>
<div id="ConfermaCancellazione" title="Conferma Cancellazione" style="display:none;">
	<input type="hidden" value="" id="UrlDest" />
  <h3><?php esc_html_e("Atto","albo-pretorio-considera");?> <span id="oggetto"></span></span></h3><p style='color:red;font-weight: bold;'><?php esc_html_e("Confermi la cancellazione dell'atto?","albo-pretorio-considera");?></p>
</div>
<?php
echo' <div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-portfolio"></span> '. __("Atti","albo-pretorio-considera");
$HtmlNP="";
if (albopc_get_num_categorie()==0){
	$HtmlNP.='<p> </p>
			<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				'. __("Non risultano categorie codificate, se vuoi posso impostare le categorie di default","albo-pretorio-considera").' &ensp;&ensp;<a href="?page=utilityAlboP&amp;action=creacategorie">'. __("Crea Categorie di Default","albo-pretorio-considera").'</a>
				</p>
			</div>';
}
if (albopc_num_responsabili()==0){
	$HtmlNP.='<p> </p>
			<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				'. sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Non risultano %1\$sResponsabili%2\$s codificati, devi crearne almeno uno prima di iniziare a codificare gli Atti","albo-pretorio-considera"),"<strong>","</strong>").' &ensp;&ensp;<a href="?page=soggetti">'. __("Crea Soggetti","albo-pretorio-considera").'</a>
				</p>
			</div>';
}
if (albopc_num_unitao()==0){
	$HtmlNP.='<p> </p>
			<div class="widefat" >
				<p style="text-align:center;font-size:1.2em;font-weight: bold;color: green;">
				'. sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Non risulta nessuna %1\$sUnità Organizzativa%2\$s codificata, devi crearne almeno una prima di iniziare a codificare gli Atti","albo-pretorio-considera"),"<strong>","</strong>").' &ensp;&ensp;<a href="?page=unitao">'. __("Crea Unità Organizzativa","albo-pretorio-considera").'</a>
				</p>
			</div>';
}
if ($HtmlNP!=""){
	echo '</h2>
	<div class="clear"></div>
	<div class="postbox-container" style="width:80%;margin-top:20px;">'.
	$HtmlNP.'
	</div>
</div><!-- /wrap -->	';
	return;	
}

echo'
	<a href="?page=atti&amp;action=new-atto" class="add-new-h2">'. __("Aggiungi nuovo","albo-pretorio-considera").'</a></h2>';
	if ( isset($msg) or isset($msg2) or isset($Errore) ){
		$stato="";
		if (isset($_GET['stato_atti'])){
			switch(sanitize_text_field(wp_unslash($_REQUEST['stato_atti'] ?? ''))){
				case "Tutti": $stato="&stato_atti=Tutti";break;
				case "Nuovi": $stato="&stato_atti=DaPubblicare";break;
				case "Correnti": $stato="&stato_atti=Correnti";break;
				case "Scaduti": $stato="&stato_atti=Scaduti";break;
				case "Eliminare":  $stato="&stato_atti=Eliminare";break;
				case "Cerca":  $stato="&stato_atti=Cerca";break; 
				default: $stato="&stato_atti=Tutti";break;
			}			
		}
		if($Msg_op=="Atto PUBBLICATO"){
			$stato="&stato_atti=Correnti";
		}
		if(substr($Msg_op,0,19)== __("Atto non PUBBLICATO","albo-pretorio-considera")){
			$stato="&stato_atti=Nuovi";
		}
		echo '<div id="message" class="updated"><p>'.(isset($msg)?$messages[$msg]:"").(isset($msg2)?"<br />".$messages[$msg2]:"").'<br /><br />'.(isset($Errore)?$Errore:"").'</p></div>
		      <meta http-equiv="refresh" content="2;url=admin.php?page=atti'.$stato.'"/>';
		      return;
	} 
	if (isset($_REQUEST['stato_atti']))
		switch(sanitize_text_field(wp_unslash($_REQUEST['stato_atti'] ?? ''))){
			case "Tutti": $Titolo=__("Tutti gli atti","albo-pretorio-considera");$Azione="Tutti";break;
			case "Nuovi": $Titolo=__("Atti da pubblicare","albo-pretorio-considera");$Azione="DaPubblicare";break;
			case "Correnti": $Titolo=__("Atti in corso di Validità","albo-pretorio-considera");$Azione="Correnti";break;
			case "Scaduti":  $Titolo=__("Atti Scaduti","albo-pretorio-considera");$Azione="Scaduti";break;
			case "Eliminare":  $Titolo=__("Atti da Eliminare (Oblio)","albo-pretorio-considera");$Azione="Eliminare";break;
            case "Cerca":  $Titolo=__("Cerca Atti","albo-pretorio-considera");$Azione="Cerca";break; /* mr */
			default: $Titolo=__("Atti da Pubblicare","albo-pretorio-considera");$Azione="DaPubblicare";break;
		}
	else{
			$Titolo=__("Tutti gli atti","albo-pretorio-considera");
			$Azione="Tutti";
	}
	$tablenew = new albopc_AdminTableAtti(); // Il codice della classe a seguire
   	$tablenew->stato_atti=$Azione;
  	$tablenew->prepare_items(); // Metodo per elenco campi
  	$page = filter_input(INPUT_GET,'page' ,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  	$paged = filter_input(INPUT_GET,'paged',FILTER_SANITIZE_NUMBER_INT);
	// Valori correnti dei filtri di ricerca (per renderli persistenti nel form)
	$f_rif = isset($_REQUEST['f_riferimento']) ? sanitize_text_field(wp_unslash($_REQUEST['f_riferimento'] ?? ''))    : '';
	$f_num = isset($_REQUEST['f_numero'])      ? sanitize_text_field(wp_unslash($_REQUEST['f_numero'] ?? ''))         : '';
	$f_cat = isset($_REQUEST['f_categoria'])   ? (isset($_REQUEST['f_categoria'])?(int)$_REQUEST['f_categoria']:0) : 0;
	echo '<h3>'.$Titolo.'</h3>
		</div>
		<div class="wrap">
	  	<form method="get">
	  		<input type="hidden" name="page" value="'.$page. '"/>
	  		<input type="hidden" name="stato_atti" value="Cerca"/>
	  		<div class="ap-filtri-cerca" style="margin:8px 0;display:flex;gap:16px;flex-wrap:wrap;align-items:center;">
	  			<label>'.__("Riferimento","albo-pretorio-considera").' <input type="search" name="f_riferimento" value="'.esc_attr($f_rif).'"/></label>
	  			<label>'.__("Numero","albo-pretorio-considera").' <input type="search" name="f_numero" size="10" value="'.esc_attr($f_num).'" placeholder="'.esc_attr__("anche parziale","albo-pretorio-considera").'"/></label>
	  			<label>'.__("Categoria","albo-pretorio-considera").' '.albopc_get_dropdown_ricerca_categorie("f_categoria","f_categoria","","",$f_cat).'</label>
	  		</div>'; /* mr */
	    	$tablenew->search_box(__("Cerca in Oggetto","albo-pretorio-considera"),'search_id'); /* mr  */
	    	$tablenew->views();
	echo '</form>
        <form id="persons-table" method="GET">
            <input type="hidden" name="page" value="'.esc_attr(sanitize_text_field(wp_unslash($_REQUEST['page'] ?? ''))).'" />
		  	<input type="hidden" name="paged" value="'.esc_attr($paged).'"/>';
	$tablenew->display(); // Metodo per visualizzare elenco records
	echo '</form>
	</div>
</div>
';
}
?>
