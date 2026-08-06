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
$albopc_messages[7] = __('Impossibile cancellare Unità organizzative che sono collegati ad Atti','albo-pretorio-on-line');
$albopc_messages[9] = __('Bisogna assegnare il nome alla nuova Unità organizzative','albo-pretorio-on-line');
$albopc_messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-on-line");

?>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-awards" style="font-size: 1.1em;"></span> <?php esc_html_e("Unità Organizzative","albo-pretorio-on-line");?>
		<a href="?page=unitao" class="add-new-h2"><?php esc_html_e("Aggiungi nuova","albo-pretorio-on-line");?></a></h2>
	</div>
<?php 
if ( (isset($_REQUEST['message']) && ( $albopc_msg = (int) $_REQUEST['message']))) {
	echo '<div id="message" class="updated"><p>'.esc_html($albopc_messages[$albopc_msg]);
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.esc_html(sanitize_text_field(wp_unslash($_REQUEST['errore'])));
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$albopc_risultato=albopc_get_unitaorganizzativa(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0);
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
<h3><?php esc_html_e("Elenco Unità Organizzative","albo-pretorio-on-line");?></h3>
<table class="widefat" id="elenco-unitao"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php esc_html_e("Unità Organizzative","albo-pretorio-on-line");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$albopc_lista=albopc_get_unitao(); 
echo '<tr>
        	<td>
			<ul>';
$albopc_shift=1;
if ($albopc_lista){
	foreach($albopc_lista as $albopc_riga){
		echo'<li style="text-align:left;padding-left:1px;">';
	 	$albopc_Tab=0;
		$albopc_Testo_da=__("Confermi la cancellazione dell'Unità Organizzativa","albo-pretorio-on-line")." ".stripslashes($albopc_riga->Nome). "?\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-pretorio-on-line");
		if($albopc_riga->IdUO>0 and albopc_num_unitao_atto($albopc_riga->IdUO)==0)
			echo '<span class="cancella"><a href="?page=unitao&amp;action=delete-unitao&amp;id='.esc_attr($albopc_riga->IdUO).'&amp;cancellaunitao='.esc_attr(wp_create_nonce('deleteunitao')).'" rel="'.esc_attr($albopc_Testo_da).'" class="confdel">
					<span class="dashicons dashicons-trash" title="'.esc_html__("Cancella unità organizzativa","albo-pretorio-on-line").'"></span>
					</a></span>';
		else
			$albopc_Tab=23;		
		echo '					<a href="?page=unitao&amp;action=edit-unitao&amp;id='.esc_attr($albopc_riga->IdUO).'&amp;modificaunitao='.esc_attr(wp_create_nonce('ediunitao')).'" rel="'.esc_attr(stripslashes($albopc_riga->Nome)).'">
					<span class="dashicons dashicons-edit" title="'.esc_html__("Modifica Unità Organizzativa","albo-pretorio-on-line").'" style="margin-left:'.(int)$albopc_Tab.'px;"></span>
					</a>';
		echo '<strong>'.esc_html(stripslashes($albopc_riga->Nome)).'</strong> (n&ordm; atti '.(int)(albopc_num_enti_atto($albopc_riga->IdUO)).')';
		echo '</li>';
	}
} else {
		echo '<li>'.esc_html__("Nessuna Unità Organizzativa Codificata","albo-pretorio-on-line").'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$albopc_righe=albopc_get_all_Oggetto_log(9);
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
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-on-line"),"<strong>","</strong>"));?>
	</div>
	<br />
	<form id="addtag" method="post" action="?page=unitao" class="<?php if($albopc_edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($albopc_edit || (isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-unitao"; else echo "add-unitao"; ?>"/>
		<input type="hidden" name="action2" value="<?php echo esc_attr(isset($_REQUEST['action'])?sanitize_text_field(wp_unslash($_REQUEST['action'])):""); ?>"/>
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?intval($_REQUEST['id']):0; ?>" />
		<input type="hidden" name="unitao" value="<?php echo esc_attr(wp_create_nonce('unitao'))?>" />

		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-nome"><?php esc_html_e("Nome Unità Organizzativa","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="unitao-nome" id="<?php esc_html_e("Nome Unità Organizzativa","albo-pretorio-on-line");?>" type="text" value="<?php if($albopc_edit) echo (isset($albopc_risultato->Nome)?esc_attr(albopc_sanifica_testo($albopc_risultato->Nome)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo (isset($_REQUEST['unitao-nome'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'])))):""); ?>" size="30" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-indirizzo"><?php esc_html_e("Indirizzo","albo-pretorio-on-line");?></label>
			<input name="unitao-indirizzo" id="unitao-indirizzo" type="text" value="<?php if($albopc_edit) echo (isset($albopc_risultato->Indirizzo)?esc_attr(albopc_sanifica_testo($albopc_risultato->Indirizzo)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo (isset($_REQUEST['unitao-indirizzo'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-indirizzo'])))):""); ?>" size="150"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-url"><?php esc_html_e("Url","albo-pretorio-on-line");?></label>
			<input name="unitao-url" id="unitao-url" type="url" value="<?php if($albopc_edit) echo (isset($albopc_risultato->Url)?esc_attr(albopc_sanifica_testo($albopc_risultato->Url)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo (isset($_REQUEST['unitao-url'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-url'])))):"");?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-email"><?php esc_html_e("Email","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="unitao-email" id="<?php esc_html_e("Email","albo-pretorio-on-line");?>" type="email" required value="<?php if($albopc_edit) echo (isset($albopc_risultato->Email)?esc_attr(albopc_sanifica_testo($albopc_risultato->Email)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo esc_attr((isset($_REQUEST['unitao-email'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-email']))):""));?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-pec"><?php esc_html_e("Pec","albo-pretorio-on-line");?></label>
			<input name="unitao-pec" id="unitao-pec" type="email" value="<?php if($albopc_edit) echo (isset($albopc_risultato->Pec)?esc_attr(albopc_sanifica_testo($albopc_risultato->Pec)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo esc_attr((isset($_REQUEST['unitao-pec'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-pec']))):""));?>" size="100"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-telefono"><?php esc_html_e("Telefono","albo-pretorio-on-line");?></label>
			<input name="unitao-telefono" id="unitao-telefono" type="text" value="<?php if($albopc_edit) echo (isset($albopc_risultato->Telefono)?esc_attr(albopc_sanifica_testo($albopc_risultato->Telefono)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo esc_attr((isset($_REQUEST['unitao-telefono'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-telefono']))):"")); ?>"' size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-fax"><?php esc_html_e("Fax","albo-pretorio-on-line");?></label>
			<input name="unitao-fax" id="unitao-fax" type="text" value="<?php if($albopc_edit) echo (isset($albopc_risultato->Fax)?esc_attr(albopc_sanifica_testo($albopc_risultato->Fax)):esc_attr__("Non Definito","albo-pretorio-on-line")); else echo esc_attr((isset($_REQUEST['unitao-fax'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-fax']))):"")); ?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="tag-description"><?php esc_html_e("Note","albo-pretorio-on-line");?></label>
			<textarea name="unitao-note" id="unitao-note" rows="5" cols="40"><?php if($albopc_edit) echo (isset($albopc_risultato->Note)?esc_textarea(albopc_sanifica_areatesto($albopc_risultato->Note)):esc_html__("Non Definito","albo-pretorio-on-line")); else echo esc_textarea((isset($_REQUEST['unitao-note'])?albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['unitao-note']))):"")); ?></textarea>
			<p><?php esc_html_e("inserire eventuali informazioni aggiuntive","albo-pretorio-on-line");?></p>
		</div>

<?php
if($albopc_edit) {
	if(isset($albopc_risultato->Nome)){
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Unità Organizzativa","albo-pretorio-on-line").' '.(isset($albopc_risultato->Nome)?albopc_sanifica_testo($albopc_risultato->Nome):"")).'" rel="'.esc_attr((isset($albopc_risultato->Nome)?albopc_sanifica_testo($albopc_risultato->Nome):"")).'" />';
	}
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Unità Organizzativa","albo-pretorio-on-line").' '.(isset($_GET['unitao-nome'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['unitao-nome']))):"")).'" rel="'.esc_attr(isset($_GET['unitao-nome'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['unitao-nome']))):"").'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuovo Unità Organizzativa","albo-pretorio-on-line").'"  />';
}
?>
	</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->