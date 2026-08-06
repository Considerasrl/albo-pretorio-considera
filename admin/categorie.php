<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione Categorie.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */
if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- pagina di sola VISUALIZZAZIONE/redisplay del form: le letture di $_REQUEST/$_GET servono a ripopolare il form (link edit via GET, valori dopo errore di validazione); le MUTAZIONI effettive (add/memo/delete) avvengono negli handler di admin.php, ciascuno protetto da wp_verify_nonce.

$albopc_messages[1] = __('Elemento aggiunto.','albo-pretorio-on-line');
$albopc_messages[2] = __('Elemento cancellato.','albo-pretorio-on-line');
$albopc_messages[3] = __('Elemento aggiornato.','albo-pretorio-on-line');
$albopc_messages[4] = __('Elemento non aggiunto.','albo-pretorio-on-line');
$albopc_messages[5] = __('Elemento non aggiornato.','albo-pretorio-on-line');
$albopc_messages[6] = __('Elemento non cancellato.','albo-pretorio-on-line');
$albopc_messages[7] = __('Impossibile cancellare Categorie che contengono Categorie Figlio. Cancellare prima i Figli','albo-pretorio-on-line');
$albopc_messages[8] = __('Impossibile cancellare Categorie che sono collegate ad Atti','albo-pretorio-on-line');
$albopc_messages[9] = __('Bisogna assegnare il nome alla nuova categoria','albo-pretorio-on-line');
$albopc_messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-on-line");
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-on-line");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-on-line");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php  esc_html_e("Correggere gli errori per continuare","albo-pretorio-on-line");?></p>
</div>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-category"></span> <?php esc_html_e("Categorie Atti","albo-pretorio-on-line");?>
		<a href="?page=categorie" class="add-new-h2"><?php esc_html_e("Aggiungi nuovo","albo-pretorio-on-line");?></a></h2>
	</div>
<?php 
if ( isset($_REQUEST['message']) && ( $albopc_msg = intval($_REQUEST['message'] )) ) {
	echo '<div id="message" class="updated"><p>'.esc_html($albopc_messages[$albopc_msg]).'</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$albopc_risultato=albopc_get_categoria((isset($_REQUEST['id'])?intval($_REQUEST['id']):0));
//	print_r($albopc_risultato);
	$albopc_edit=True;
}else{
	$albopc_edit=False;
}
?>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3><?php esc_html_e("Elenco Categorie codificate","albo-pretorio-on-line");?></h3>
<table class="widefat" id="elenco-categorie"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php esc_html_e("Categorie","albo-pretorio-on-line");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$albopc_lista=albopc_get_categorie_gerarchica(); 
echo '<tr>
        	<td>
			<ul>';
if ($albopc_lista){
	foreach($albopc_lista as $albopc_riga){
	 $albopc_shift=((intval($albopc_riga[2]))*30)+5;
	 echo'<li style="text-align:left;padding-left:'.(int)$albopc_shift.'px;">';
	 $albopc_Tab=0;
	 $albopc_Testo_da=__("Confermi la cancellazione della Categoria","albo-pretorio-on-line")." ".albopc_sanifica_testo($albopc_riga[1]). "?\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-pretorio-on-line");
 	 if (albopc_num_atti_categoria($albopc_riga[0])==0)
		echo'<span class="cancella">
			<a href="?page=categorie&amp;action=delete-categorie&amp;id='.esc_attr($albopc_riga[0]).'&amp;canccategoria='.esc_attr(wp_create_nonce('delcategoria')).'" rel="'.esc_attr($albopc_Testo_da).'" class="confdel">			
			<span class="dashicons dashicons-trash" title="'.esc_html__("Cancella categoria","albo-pretorio-on-line").'"></span>
		</a></span>
';
	 else
		$albopc_Tab=23;
	 echo'					
			<a href="?page=categorie&amp;action=edit-categorie&amp;id='.esc_attr($albopc_riga[0]).'&amp;modcategoria='.esc_attr(wp_create_nonce('editcategoria')).'" rel="'.esc_attr($albopc_riga[1]).'">
			<span class="dashicons dashicons-edit" title="'.esc_html__("Modifica categoria","albo-pretorio-on-line").'" style="margin-left:'.(int)$albopc_Tab.'px;"></span>
			</a>
			('.esc_html($albopc_riga[0]) .') '.esc_html($albopc_riga[1]) .' (n&ordm; atti '.(int)(albopc_num_atti_categoria($albopc_riga[0])).')
			</li>'; 
	}
} else {
		echo '<li>'.esc_html__("Nessuna Categoria Codificata","albo-pretorio-on-line").'</li>';
}
echo '</ul>
		</td>
	 </tr>
      </tbody>
	</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$albopc_righe=albopc_get_all_Oggetto_log(2);
echo'
	<table class="widefat">
	    <thead>
		<tr>
			<th style="font-size:1.2em;">'.esc_html__("Data","albo-pretorio-on-line").'</th>
			<th style="font-size:1.2em;">'.esc_html__("Operazione","albo-pretorio-on-line").'</th>
			<th style="font-size:1.2em;">'.esc_html__("Informazioni","albo-pretorio-on-line").'</th>
		</tr>
	    </thead>
	    <tbody id="righe-log">';
foreach ($albopc_righe as $albopc_riga) {
	switch ($albopc_riga->TipoOperazione){
	 	case 1:
	 		$albopc_Operazione=__("Inserimento","albo-pretorio-on-line");
	 		break;
	 	case 2:
	 		$albopc_Operazione=__("Modifica","albo-pretorio-on-line");
			break;
	 	case 3:
	 		$albopc_Operazione=__("Cancellazione","albo-pretorio-on-line");
			break;
	}
	echo '<tr  title="'.esc_attr($albopc_riga->Utente.' da '.$albopc_riga->IPAddress).'">
			<td >'.esc_html($albopc_riga->Data).'</th>
			<td >'.esc_html($albopc_Operazione).'</th>
			<td >'.esc_html(albopc_sanifica_testo($albopc_riga->Operazione)).'</td>
		</tr>';
}
echo '    </tbody>
	</table>
</div>';
?>
</div><!-- /col-right -->

<div id="col-left">
	<div class="Obbligatori">
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-on-line"),"<strong>","</strong>"));?>
	</div>
<div class="form-wrap">
	<form id="addtag" method="post" action="?page=categorie" class="<?php if($albopc_edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($albopc_edit) echo "memo-categoria"; else echo "add-categorie"; ?>"/>
		<input type="hidden" name="id" value="<?php echo (isset($_REQUEST['id'])?intval($_REQUEST['id']):0); ?>" />
		<input type="hidden" name="categoria" value="<?php echo esc_attr(wp_create_nonce('categoria'))?>" />

		<div class="form-field form-required">
			<label for="tag-name"><?php esc_html_e("Nome","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="cat-name" id="<?php esc_html_e("Nome","albo-pretorio-on-line");?>" type="text" value="<?php if($albopc_edit) echo esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Nome)); ?>" size="40" request/>
			<p><?php esc_html_e("Nome della categoria.","albo-pretorio-on-line");?></p>
		</div>
		<div class="form-field">
			<label for="parent"><?php esc_html_e("Parente di","albo-pretorio-on-line");?>:</label>
			<?php 
			if($albopc_edit){
				/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <select> interno */ echo albopc_get_dropdown_categorie('cat-parente','cat-parente','','',albopc_sanifica_testo($albopc_risultato[0]->Genitore));
			}else{
				/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <select> interno */ echo albopc_get_dropdown_categorie('cat-parente','cat-parente','postform','',0); 
			}
			?>
			<p><?php esc_html_e("Se si sta creando una sottocategoria, selezionare il genitore. Questo sistema permette di creare una struttura gerarchica di categorie.","albo-pretorio-on-line");?></p>
		</div>
		<div class="form-field">
			<label for="tag-description"><?php esc_html_e("Descrizione","albo-pretorio-on-line");?></label>
			<textarea name="cat-descrizione" id="cat-descrizione" rows="5" cols="40"><?php if($albopc_edit) echo esc_textarea(albopc_sanifica_areatesto($albopc_risultato[0]->Descrizione)); ?></textarea>
			<p><?php esc_html_e("Breve descrizione della categoria","albo-pretorio-on-line");?></p>
		</div>
		<div class="form-field  form-required">
			<label for="tag-durata"><?php esc_html_e("Durata","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="cat-durata" id="<?php esc_html_e("Durata","albo-pretorio-on-line");?>" type="number" minval=0 value="<?php if($albopc_edit) echo intval($albopc_risultato[0]->Giorni); else echo "0"; ?>" size="4" style="width:6em;" alt="<?php esc_html_e("Durata Atto","albo-pretorio-on-line");?>" required />
			<p><?php esc_html_e("Durata di default, espressa in giorni, di validità degli atti di questa categoria","albo-pretorio-on-line");?></p>
		</div>

<?php
if($albopc_edit) {
	echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Categoria","albo-pretorio-on-line").' '.albopc_sanifica_testo($albopc_risultato[0]->Nome)).'" rel="'.esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Nome)).'" />';
}else{
	echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuova Categoria","albo-pretorio-on-line").'"  />';	
}
?>
	</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->

