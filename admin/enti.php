<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * WGestione Enti.
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
$albopc_messages[7] = __('Impossibile cancellare Enti che sono collegati ad Atti','albo-pretorio-on-line');
$albopc_messages[9] = __('Bisogna assegnare il nome al nuovo ente','albo-pretorio-on-line');
$albopc_messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-on-line");

?>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-awards" style="font-size: 1.1em;"></span> <?php esc_html_e("Enti","albo-pretorio-on-line");?>
		<a href="?page=enti" class="add-new-h2"><?php esc_html_e("Aggiungi nuovo","albo-pretorio-on-line");?></a></h2>
	</div>
<?php 
if ( (isset($_REQUEST['message']) && ( $albopc_msg = (isset($_REQUEST['message'])?intval($_REQUEST['message']):0)))) {
	echo '<div id="message" class="updated"><p>'.esc_html($albopc_messages[$albopc_msg]);
	if (isset($_REQUEST['errore']))
		echo '<br />'.esc_html(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['errore']))));
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$albopc_risultato=albopc_get_ente((isset($_REQUEST['id'])?intval($_REQUEST['id']):0));
	$albopc_edit=True;
}else{
	$albopc_edit=False;
}
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-on-line");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-on-line");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php  esc_html_e("Correggere gli errori per continuare","albo-pretorio-on-line");?></p>
</div>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3><?php esc_html_e("Elenco Enti","albo-pretorio-on-line");?></h3>
<table class="widefat" id="elenco-enti"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php esc_html_e("Enti","albo-pretorio-on-line");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$albopc_lista=albopc_get_enti(); 
echo '<tr>
        	<td>
			<ul>';
$albopc_shift=1;
if ($albopc_lista){
	foreach($albopc_lista as $albopc_riga){
		echo'<li style="text-align:left;padding-left:1px;">';
	 	$albopc_Tab=0;
		$albopc_Testo_da=__("Confermi la cancellazione dell'Ente","albo-pretorio-on-line")." ".albopc_sanifica_testo($albopc_riga->Nome). "?\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-pretorio-on-line");
		if($albopc_riga->IdEnte>0 and albopc_num_enti_atto($albopc_riga->IdEnte)==0)
			echo '<span class="cancella"><a href="?page=enti&amp;action=delete-ente&amp;id='.(int)$albopc_riga->IdEnte.'&amp;cancellaente='.esc_attr(wp_create_nonce('deleteente')).'" rel="'.esc_attr($albopc_Testo_da).'" class="confdel">
					<span class="dashicons dashicons-trash" title="'.esc_attr__("Cancella ente","albo-pretorio-on-line").'"></span>
					</a></span>';
		else
			$albopc_Tab=23;
		echo '					<a href="?page=enti&amp;action=edit-ente&amp;id='.(int)$albopc_riga->IdEnte.'&amp;modificaente='.esc_attr(wp_create_nonce('editente')).'" rel="'.esc_attr(albopc_sanifica_testo($albopc_riga->Nome)).'">
					<span class="dashicons dashicons-edit" title="'.esc_attr__("Modifica ente","albo-pretorio-on-line").'" style="margin-left:'.(int)$albopc_Tab.'px;"></span>
					</a>';
		echo '<strong>'.esc_html(albopc_sanifica_testo($albopc_riga->Nome)).'</strong> (n&ordm; atti '.(int)albopc_num_enti_atto($albopc_riga->IdEnte).')';
		echo '</li>';
	}
} else {
		echo '<li>'.esc_html__("Nessun Ente Codificato","albo-pretorio-on-line").'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$albopc_righe=albopc_get_all_Oggetto_log(7);
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
			<td >'.esc_html(stripslashes($albopc_riga->Operazione)).'</td>
		</tr>';
}
echo '    </tbody>
	</table>
</div>';
?>
</div><!-- /col-right -->

<div id="col-left">
<div class="form-wrap">
	<div class="Obbligatori">
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag di apertura e chiusura del grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-on-line"),"<strong>","</strong>"));?>
	</div>
	<br />
	<form id="addtag" method="post" action="?page=enti" class="<?php if($albopc_edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($albopc_edit || (isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-ente"; else echo "add-ente"; ?>"/>
		<input type="hidden" name="action2" value="<?php echo isset($_REQUEST['action'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['action'])))):"";?>"/>
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?intval($_REQUEST['id']):0; ?>" />
		<input type="hidden" name="enti" value="<?php echo esc_attr(wp_create_nonce('enti'))?>" />

		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-nome"><?php esc_html_e("Nome Ente","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="ente-nome" id="<?php esc_html_e("Nome Ente","albo-pretorio-on-line");?>" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato->Nome)?esc_attr(albopc_sanifica_testo($albopc_risultato->Nome)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-nome'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-nome'])))):""; ?>" size="30" alt="Nome Ente" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-indirizzo"><?php esc_html_e("Indirizzo","albo-pretorio-on-line");?></label>
			<input name="ente-indirizzo" id="ente-indirizzo" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato->Indirizzo)?esc_attr(albopc_sanifica_testo($albopc_risultato->Indirizzo)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-indirizzo'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-indirizzo'])))):"";?>" size="150"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-url"><?php esc_html_e("Url","albo-pretorio-on-line");?></label>
			<input name="ente-url" id="ente-url" type="url" value="<?php if($albopc_edit) echo isset($albopc_risultato->Url)?esc_attr(albopc_sanifica_testo($albopc_risultato->Url)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-url'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-url'])))):"";?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-email"><?php esc_html_e("Email","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="ente-email" id="<?php esc_html_e("Email","albo-pretorio-on-line");?>" type="email" value="<?php if($albopc_edit) echo isset($albopc_risultato->Email)?esc_attr(albopc_sanifica_testo($albopc_risultato->Email)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-email'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-email'])))):"";?>" size="100" alt="Email" required/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-pec"><?php esc_html_e("Pec","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="ente-pec" id="<?php esc_html_e("Pec","albo-pretorio-on-line");?>" type="email" value="<?php if($albopc_edit) echo isset($albopc_risultato->Pec)?esc_attr(albopc_sanifica_testo($albopc_risultato->Pec)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-pec'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-pec'])))):"";?>" size="100" alt="Pec" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-telefono"><?php esc_html_e("Telefono","albo-pretorio-on-line");?></label>
			<input name="ente-telefono" id="ente-telefono" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato->Telefono)?esc_attr(albopc_sanifica_testo($albopc_risultato->Telefono)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-telefono'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-telefono'])))):"";?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-fax"><?php esc_html_e("Fax","albo-pretorio-on-line");?></label>
			<input name="ente-fax" id="ente-fax" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato->Fax)?esc_attr(albopc_sanifica_testo($albopc_risultato->Fax)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-fax'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-fax'])))):"";?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="tag-description"><?php esc_html_e("Note","albo-pretorio-on-line");?></label>
			<textarea name="ente-note" id="ente-note" rows="5" cols="40"><?php if($albopc_edit) echo isset($albopc_risultato->Note)?esc_textarea(albopc_sanifica_areatesto($albopc_risultato->Note)):esc_html__("Non Definito","albo-pretorio-on-line"); else echo isset($_REQUEST['ente-note'])?esc_textarea(albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['ente-note'])))):"";?></textarea>
			<p><?php esc_html_e("inserire eventuali informazioni aggiuntive","albo-pretorio-on-line");?></p>
		</div>

<?php
if($albopc_edit) {
	echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Ente","albo-pretorio-on-line").' '.stripslashes($albopc_risultato->Nome)).'" rel="'.esc_attr(stripslashes($albopc_risultato->Nome)).'" />';
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Ente","albo-pretorio-on-line").' '.stripslashes($albopc_risultato->Nome)).'" rel="'.esc_attr(sanitize_text_field(wp_unslash($_REQUEST['ente-nome']))).'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuovo Ente","albo-pretorio-on-line").'"  />';
}
?>
	</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->