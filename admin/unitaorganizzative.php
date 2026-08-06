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

$messages[1] = __('Elemento aggiunto.','albo-pretorio-considera');
$messages[2] = __('Elemento cancellato.','albo-pretorio-considera');
$messages[3] = __('Elemento aggiornato.','albo-pretorio-considera');
$messages[4] = __('Elemento non aggiunto.','albo-pretorio-considera');
$messages[5] = __('Elemento non aggiornato.','albo-pretorio-considera');
$messages[6] = __('Elemento non cancellato.','albo-pretorio-considera');
$messages[7] = __('Impossibile cancellare Unità organizzative che sono collegati ad Atti','albo-pretorio-considera');
$messages[9] = __('Bisogna assegnare il nome alla nuova Unità organizzative','albo-pretorio-considera');
$messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");

?>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-awards" style="font-size: 1.1em;"></span> <?php esc_html_e("Unità Organizzative","albo-pretorio-considera");?>
		<a href="?page=unitao" class="add-new-h2"><?php esc_html_e("Aggiungi nuova","albo-pretorio-considera");?></a></h2>
	</div>
<?php 
if ( (isset($_REQUEST['message']) && ( $msg = (int) $_REQUEST['message']))) {
	echo '<div id="message" class="updated"><p>'.esc_html($messages[$msg]);
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.esc_html(sanitize_text_field(wp_unslash($_REQUEST['errore'])));
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$risultato=albopc_get_unitaorganizzativa(isset($_REQUEST['id'])?(int)$_REQUEST['id']:0);
	$edit=True;
}else{
	$edit=False;
}
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-considera");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-considera");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php  esc_html_e("Correggere gli errori per continuare","albo-pretorio-considera");?></p>
</div>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3><?php esc_html_e("Elenco Unità Organizzative","albo-pretorio-considera");?></h3>
<table class="widefat" id="elenco-unitao"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php esc_html_e("Unità Organizzative","albo-pretorio-considera");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$lista=albopc_get_unitao(); 
echo '<tr>
        	<td>
			<ul>';
$shift=1;
if ($lista){
	foreach($lista as $riga){
		echo'<li style="text-align:left;padding-left:1px;">';
	 	$Tab=0;
		$Testo_da=__("Confermi la cancellazione dell'Unità Organizzativa","albo-pretorio-considera")." ".stripslashes($riga->Nome). "?\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-pretorio-considera");
		if($riga->IdUO>0 and albopc_num_unitao_atto($riga->IdUO)==0)
			echo '<span class="cancella"><a href="?page=unitao&amp;action=delete-unitao&amp;id='.esc_attr($riga->IdUO).'&amp;cancellaunitao='.esc_attr(wp_create_nonce('deleteunitao')).'" rel="'.esc_attr($Testo_da).'" class="confdel">
					<span class="dashicons dashicons-trash" title="'.esc_html__("Cancella unità organizzativa","albo-pretorio-considera").'"></span>
					</a></span>';
		else
			$Tab=23;		
		echo '					<a href="?page=unitao&amp;action=edit-unitao&amp;id='.esc_attr($riga->IdUO).'&amp;modificaunitao='.esc_attr(wp_create_nonce('ediunitao')).'" rel="'.esc_attr(stripslashes($riga->Nome)).'">
					<span class="dashicons dashicons-edit" title="'.esc_html__("Modifica Unità Organizzativa","albo-pretorio-considera").'" style="margin-left:'.(int)$Tab.'px;"></span>
					</a>';
		echo '<strong>'.esc_html(stripslashes($riga->Nome)).'</strong> (n&ordm; atti '.(int)(albopc_num_enti_atto($riga->IdUO)).')';
		echo '</li>';
	}
} else {
		echo '<li>'.esc_html__("Nessuna Unità Organizzativa Codificata","albo-pretorio-considera").'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$righe=albopc_get_all_Oggetto_log(9);
echo'
	<table class="widefat">
	    <thead>
		<tr>
			<th style="font-size:1.2em;">'.esc_html__("Data","albo-pretorio-considera").'</th>
			<th style="font-size:1.2em;">'.esc_html__("Operazione","albo-pretorio-considera").'</th>
			<th style="font-size:1.2em;">'.esc_html__("Informazioni","albo-pretorio-considera").'</th>
		</tr>
	    </thead>
	    <tbody id="righe-log">';
foreach ($righe as $riga) {
	switch ($riga->TipoOperazione){
	 	case 1:
	 		$Operazione=__("Inserimento","albo-pretorio-considera");
	 		break;
	 	case 2:
	 		$Operazione=__("Modifica","albo-pretorio-considera");
			break;
	 	case 3:
	 		$Operazione=__("Cancellazione","albo-pretorio-considera");
			break;
	}
	echo '<tr  title="'.esc_attr($riga->Utente.' da '.$riga->IPAddress).'">
			<td >'.esc_html($riga->Data).'</th>
			<td >'.esc_html($Operazione).'</th>
			<td >'.esc_html(stripslashes($riga->Operazione)).'</td>
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
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-considera"),"<strong>","</strong>"));?>
	</div>
	<br />
	<form id="addtag" method="post" action="?page=unitao" class="<?php if($edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($edit || (isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-unitao"; else echo "add-unitao"; ?>"/>
		<input type="hidden" name="action2" value="<?php echo esc_attr(isset($_REQUEST['action'])?sanitize_text_field(wp_unslash($_REQUEST['action'])):""); ?>"/>
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?intval($_REQUEST['id']):0; ?>" />
		<input type="hidden" name="unitao" value="<?php echo esc_attr(wp_create_nonce('unitao'))?>" />

		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-nome"><?php esc_html_e("Nome Unità Organizzativa","albo-pretorio-considera");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="unitao-nome" id="<?php esc_html_e("Nome Unità Organizzativa","albo-pretorio-considera");?>" type="text" value="<?php if($edit) echo (isset($risultato->Nome)?esc_attr(albopc_sanifica_testo($risultato->Nome)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo (isset($_REQUEST['unitao-nome'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'])))):""); ?>" size="30" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-indirizzo"><?php esc_html_e("Indirizzo","albo-pretorio-considera");?></label>
			<input name="unitao-indirizzo" id="unitao-indirizzo" type="text" value="<?php if($edit) echo (isset($risultato->Indirizzo)?esc_attr(albopc_sanifica_testo($risultato->Indirizzo)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo (isset($_REQUEST['unitao-indirizzo'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-indirizzo'])))):""); ?>" size="150"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-url"><?php esc_html_e("Url","albo-pretorio-considera");?></label>
			<input name="unitao-url" id="unitao-url" type="url" value="<?php if($edit) echo (isset($risultato->Url)?esc_attr(albopc_sanifica_testo($risultato->Url)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo (isset($_REQUEST['unitao-url'])?esc_attr(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-url'])))):"");?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-email"><?php esc_html_e("Email","albo-pretorio-considera");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="unitao-email" id="<?php esc_html_e("Email","albo-pretorio-considera");?>" type="email" required value="<?php if($edit) echo (isset($risultato->Email)?esc_attr(albopc_sanifica_testo($risultato->Email)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo esc_attr((isset($_REQUEST['unitao-email'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-email']))):""));?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-pec"><?php esc_html_e("Pec","albo-pretorio-considera");?></label>
			<input name="unitao-pec" id="unitao-pec" type="email" value="<?php if($edit) echo (isset($risultato->Pec)?esc_attr(albopc_sanifica_testo($risultato->Pec)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo esc_attr((isset($_REQUEST['unitao-pec'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-pec']))):""));?>" size="100"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-telefono"><?php esc_html_e("Telefono","albo-pretorio-considera");?></label>
			<input name="unitao-telefono" id="unitao-telefono" type="text" value="<?php if($edit) echo (isset($risultato->Telefono)?esc_attr(albopc_sanifica_testo($risultato->Telefono)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo esc_attr((isset($_REQUEST['unitao-telefono'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-telefono']))):"")); ?>"' size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-fax"><?php esc_html_e("Fax","albo-pretorio-considera");?></label>
			<input name="unitao-fax" id="unitao-fax" type="text" value="<?php if($edit) echo (isset($risultato->Fax)?esc_attr(albopc_sanifica_testo($risultato->Fax)):esc_attr__("Non Definito","albo-pretorio-considera")); else echo esc_attr((isset($_REQUEST['unitao-fax'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-fax']))):"")); ?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="tag-description"><?php esc_html_e("Note","albo-pretorio-considera");?></label>
			<textarea name="unitao-note" id="unitao-note" rows="5" cols="40"><?php if($edit) echo (isset($risultato->Note)?esc_textarea(albopc_sanifica_areatesto($risultato->Note)):esc_html__("Non Definito","albo-pretorio-considera")); else echo esc_textarea((isset($_REQUEST['unitao-note'])?albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['unitao-note']))):"")); ?></textarea>
			<p><?php esc_html_e("inserire eventuali informazioni aggiuntive","albo-pretorio-considera");?></p>
		</div>

<?php
if($edit) {
	if(isset($risultato->Nome)){
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Unità Organizzativa","albo-pretorio-considera").' '.(isset($risultato->Nome)?albopc_sanifica_testo($risultato->Nome):"")).'" rel="'.esc_attr((isset($risultato->Nome)?albopc_sanifica_testo($risultato->Nome):"")).'" />';
	}
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Unità Organizzativa","albo-pretorio-considera").' '.(isset($_GET['unitao-nome'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['unitao-nome']))):"")).'" rel="'.esc_attr(isset($_GET['unitao-nome'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['unitao-nome']))):"").'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuovo Unità Organizzativa","albo-pretorio-considera").'"  />';
}
?>
	</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->