<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * WGestione Enti.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$messages[1] = __('Elemento aggiunto.','albo-pretorio-considera');
$messages[2] = __('Elemento cancellato.','albo-pretorio-considera');
$messages[3] = __('Elemento aggiornato.','albo-pretorio-considera');
$messages[4] = __('Elemento non aggiunto.','albo-pretorio-considera');
$messages[5] = __('Elemento non aggiornato.','albo-pretorio-considera');
$messages[6] = __('Elemento non cancellato.','albo-pretorio-considera');
$messages[7] = __('Impossibile cancellare Enti che sono collegati ad Atti','albo-pretorio-considera');
$messages[9] = __('Bisogna assegnare il nome al nuovo ente','albo-pretorio-considera');
$messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");

?>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-awards" style="font-size: 1.1em;"></span> <?php esc_html_e("Enti","albo-pretorio-considera");?>
		<a href="?page=enti" class="add-new-h2"><?php esc_html_e("Aggiungi nuovo","albo-pretorio-considera");?></a></h2>
	</div>
<?php 
if ( (isset($_REQUEST['message']) && ( $msg = intval($_REQUEST['message'])))) {
	echo '<div id="message" class="updated"><p>'.esc_html($messages[$msg]);
	if (isset($_REQUEST['errore']))
		echo '<br />'.esc_html(ap_sanifica_testo($_REQUEST['errore']));
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$risultato=ap_get_ente(intval($_REQUEST['id']));
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
<h3><?php esc_html_e("Elenco Enti","albo-pretorio-considera");?></h3>
<table class="widefat" id="elenco-enti"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php esc_html_e("Enti","albo-pretorio-considera");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$lista=ap_get_enti(); 
echo '<tr>
        	<td>
			<ul>';
$shift=1;
if ($lista){
	foreach($lista as $riga){
		echo'<li style="text-align:left;padding-left:1px;">';
	 	$Tab=0;
		$Testo_da=__("Confermi la cancellazione dell'Ente","albo-pretorio-considera")." ".ap_sanifica_testo($riga->Nome). "?\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-pretorio-considera");
		if($riga->IdEnte>0 and ap_num_enti_atto($riga->IdEnte)==0)
			echo '<span class="cancella"><a href="?page=enti&amp;action=delete-ente&amp;id='.(int)$riga->IdEnte.'&amp;cancellaente='.esc_attr(wp_create_nonce('deleteente')).'" rel="'.esc_attr($Testo_da).'" class="confdel">
					<span class="dashicons dashicons-trash" title="'.esc_attr__("Cancella ente","albo-pretorio-considera").'"></span>
					</a></span>';
		else
			$Tab=23;
		echo '					<a href="?page=enti&amp;action=edit-ente&amp;id='.(int)$riga->IdEnte.'&amp;modificaente='.esc_attr(wp_create_nonce('editente')).'" rel="'.esc_attr(ap_sanifica_testo($riga->Nome)).'">
					<span class="dashicons dashicons-edit" title="'.esc_attr__("Modifica ente","albo-pretorio-considera").'" style="margin-left:'.(int)$Tab.'px;"></span>
					</a>';
		echo '<strong>'.esc_html(ap_sanifica_testo($riga->Nome)).'</strong> (n&ordm; atti '.(int)ap_num_enti_atto($riga->IdEnte).')';
		echo '</li>';
	}
} else {
		echo '<li>'.esc_html__("Nessun Ente Codificato","albo-pretorio-considera").'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$righe=ap_get_all_Oggetto_log(7);
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
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag di apertura e chiusura del grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-considera"),"<strong>","</strong>"));?>
	</div>
	<br />
	<form id="addtag" method="post" action="?page=enti" class="<?php if($edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($edit || (isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-ente"; else echo "add-ente"; ?>"/>
		<input type="hidden" name="action2" value="<?php echo isset($_REQUEST['action'])?esc_attr(ap_sanifica_testo($_REQUEST['action'])):"";?>"/>
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?intval($_REQUEST['id']):0; ?>" />
		<input type="hidden" name="enti" value="<?php echo esc_attr(wp_create_nonce('enti'))?>" />

		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-nome"><?php esc_html_e("Nome Ente","albo-pretorio-considera");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="ente-nome" id="<?php esc_html_e("Nome Ente","albo-pretorio-considera");?>" type="text" value="<?php if($edit) echo isset($risultato->Nome)?esc_attr(ap_sanifica_testo($risultato->Nome)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-nome'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-nome'])):""; ?>" size="30" alt="Nome Ente" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-indirizzo"><?php esc_html_e("Indirizzo","albo-pretorio-considera");?></label>
			<input name="ente-indirizzo" id="ente-indirizzo" type="text" value="<?php if($edit) echo isset($risultato->Indirizzo)?esc_attr(ap_sanifica_testo($risultato->Indirizzo)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-indirizzo'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-indirizzo'])):"";?>" size="150"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-url"><?php esc_html_e("Url","albo-pretorio-considera");?></label>
			<input name="ente-url" id="ente-url" type="url" value="<?php if($edit) echo isset($risultato->Url)?esc_attr(ap_sanifica_testo($risultato->Url)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-url'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-url'])):"";?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-email"><?php esc_html_e("Email","albo-pretorio-considera");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="ente-email" id="<?php esc_html_e("Email","albo-pretorio-considera");?>" type="email" value="<?php if($edit) echo isset($risultato->Email)?esc_attr(ap_sanifica_testo($risultato->Email)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-email'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-email'])):"";?>" size="100" alt="Email" required/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-pec"><?php esc_html_e("Pec","albo-pretorio-considera");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="ente-pec" id="<?php esc_html_e("Pec","albo-pretorio-considera");?>" type="email" value="<?php if($edit) echo isset($risultato->Pec)?esc_attr(ap_sanifica_testo($risultato->Pec)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-pec'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-pec'])):"";?>" size="100" alt="Pec" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-telefono"><?php esc_html_e("Telefono","albo-pretorio-considera");?></label>
			<input name="ente-telefono" id="ente-telefono" type="text" value="<?php if($edit) echo isset($risultato->Telefono)?esc_attr(ap_sanifica_testo($risultato->Telefono)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-telefono'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-telefono'])):"";?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="ente-fax"><?php esc_html_e("Fax","albo-pretorio-considera");?></label>
			<input name="ente-fax" id="ente-fax" type="text" value="<?php if($edit) echo isset($risultato->Fax)?esc_attr(ap_sanifica_testo($risultato->Fax)):esc_attr__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-fax'])?esc_attr(ap_sanifica_testo($_REQUEST['ente-fax'])):"";?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="tag-description"><?php esc_html_e("Note","albo-pretorio-considera");?></label>
			<textarea name="ente-note" id="ente-note" rows="5" cols="40"><?php if($edit) echo isset($risultato->Note)?esc_textarea(ap_sanifica_areatesto($risultato->Note)):esc_html__("Non Definito","albo-pretorio-considera"); else echo isset($_REQUEST['ente-note'])?esc_textarea(ap_sanifica_areatesto($_REQUEST['ente-note'])):"";?></textarea>
			<p><?php esc_html_e("inserire eventuali informazioni aggiuntive","albo-pretorio-considera");?></p>
		</div>

<?php
if($edit) {
	echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Ente","albo-pretorio-considera").' '.stripslashes($risultato->Nome)).'" rel="'.esc_attr(stripslashes($risultato->Nome)).'" />';
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Ente","albo-pretorio-considera").' '.stripslashes($risultato->Nome)).'" rel="'.esc_attr($_REQUEST['ente-nome']).'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuovo Ente","albo-pretorio-considera").'"  />';
}
?>
	</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->