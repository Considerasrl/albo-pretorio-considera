<?php
/**
 * WGestione Enti.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

$messages[1] = __('Elemento aggiunto.','albo-online');
$messages[2] = __('Elemento cancellato.','albo-online');
$messages[3] = __('Elemento aggiornato.','albo-online');
$messages[4] = __('Elemento non aggiunto.','albo-online');
$messages[5] = __('Elemento non aggiornato.','albo-online');
$messages[6] = __('Elemento non cancellato.','albo-online');
$messages[7] = __('Impossibile cancellare Unità organizzative che sono collegati ad Atti','albo-online');
$messages[9] = __('Bisogna assegnare il nome alla nuova Unità organizzative','albo-online');
$messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-online");

?>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-awards" style="font-size: 1.1em;"></span> <?php _e("Unità Organizzative","albo-online");?>
		<a href="?page=unitao" class="add-new-h2"><?php _e("Aggiungi nuova","albo-online");?></a></h2>
	</div>
<?php 
if ( (isset($_REQUEST['message']) && ( $msg = (int) $_REQUEST['message']))) {
	echo '<div id="message" class="updated"><p>'.$messages[$msg];
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.htmlentities($_REQUEST['errore']);
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$risultato=ap_get_unitaorganizzativa((int)$_REQUEST['id']);
	$edit=True;
}else{
	$edit=False;
}
?>
<div id="errori" title="<?php _e("Validazione Dati","albo-online");?>" style="display:none">
  <h3><?php _e("Lista Campi con Errori","albo-online");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php  _e("Correggere gli errori per continuare","albo-online");?></p>
</div>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3><?php _e("Elenco Unità Organizzative","albo-online");?></h3>
<table class="widefat" id="elenco-unitao"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php _e("Unità Organizzative","albo-online");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$lista=ap_get_unitao(); 
echo '<tr>
        	<td>
			<ul>';
$shift=1;
if ($lista){
	foreach($lista as $riga){
		echo'<li style="text-align:left;padding-left:1px;">';
	 	$Tab=0;
		$Testo_da=__("Confermi la cancellazione dell'Unità Organizzativa","albo-online")." ".stripslashes($riga->Nome). "?\n\n".__("Sei sicuro di voler proseguire con la CANCELLAZIONE?","albo-online");
		if($riga->IdUO>0 and ap_num_unitao_atto($riga->IdUO)==0)
			echo '<span class="cancella"><a href="?page=unitao&amp;action=delete-unitao&amp;id='.$riga->IdUO.'&amp;cancellaunitao='.wp_create_nonce('deleteunitao').'" rel="'.$Testo_da.'" class="confdel">
					<span class="dashicons dashicons-trash" title="'.__("Cancella unità organizzativa","albo-online").'"></span>
					</a></span>';
		else
			$Tab=23;		
		echo '					<a href="?page=unitao&amp;action=edit-unitao&amp;id='.$riga->IdUO.'&amp;modificaunitao='.wp_create_nonce('ediunitao').'" rel="'.stripslashes($riga->Nome).'">
					<span class="dashicons dashicons-edit" title="'.__("Modifica Unità Organizzativa","albo-online").'" style="margin-left:'.$Tab.'px;"></span>
					</a>';
		echo '<strong>'.stripslashes($riga->Nome).'</strong> (n&ordm; atti '.ap_num_enti_atto($riga->IdUO).')';
		echo '</li>';
	}
} else {
		echo '<li>'.__("Nessuna Unità Organizzativa Codificata","albo-online").'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$righe=ap_get_all_Oggetto_log(9);
echo'
	<table class="widefat">
	    <thead>
		<tr>
			<th style="font-size:1.2em;">'.__("Data","albo-online").'</th>
			<th style="font-size:1.2em;">'.__("Operazione","albo-online").'</th>
			<th style="font-size:1.2em;">'.__("Informazioni","albo-online").'</th>
		</tr>
	    </thead>
	    <tbody id="righe-log">';
foreach ($righe as $riga) {
	switch ($riga->TipoOperazione){
	 	case 1:
	 		$Operazione=__("Inserimento","albo-online");
	 		break;
	 	case 2:
	 		$Operazione=__("Modifica","albo-online");
			break;
	 	case 3:
	 		$Operazione=__("Cancellazione","albo-online");
			break;
	}
	echo '<tr  title="'.$riga->Utente.' da '.$riga->IPAddress.'">
			<td >'.$riga->Data.'</th>
			<td >'.$Operazione.'</th>
			<td >'.stripslashes($riga->Operazione).'</td>
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
		<span style="color:red;font-weight: bold;">*</span> <?php printf(__("i campi contrassegnati dall'asterisco sono %s obbligatori %s","albo-online"),"<strong>","</strong>");?>
	</div>
	<br />
	<form id="addtag" method="post" action="?page=unitao" class="<?php if($edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($edit || (isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-unitao"; else echo "add-unitao"; ?>"/>
		<input type="hidden" name="action2" value="<?php echo htmlentities(isset($_REQUEST['action'])?$_REQUEST['action']:""); ?>"/>
		<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?intval($_REQUEST['id']):0; ?>" />
		<input type="hidden" name="unitao" value="<?php echo wp_create_nonce('unitao')?>" />

		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-nome"><?php _e("Nome Unità Organizzativa","albo-online");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="unitao-nome" id="<?php _e("Nome Unità Organizzativa","albo-online");?>" type="text" value="<?php if($edit) echo (isset($risultato->Nome)?ap_sanifica_testo($risultato->Nome):__("Non Definito","albo-online")); else echo (isset($_REQUEST['unitao-nome'])?ap_sanifica_testo($_REQUEST['unitao-nome']):""); ?>" size="30" required/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-indirizzo"><?php _e("Indirizzo","albo-online");?></label>
			<input name="unitao-indirizzo" id="unitao-indirizzo" type="text" value="<?php if($edit) echo (isset($risultato->Indirizzo)?ap_sanifica_testo($risultato->Indirizzo):__("Non Definito","albo-online")); else echo (isset($_REQUEST['unitao-indirizzo'])?ap_sanifica_testo($_REQUEST['unitao-indirizzo']):""); ?>" size="150"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-url"><?php _e("Url","albo-online");?></label>
			<input name="unitao-url" id="unitao-url" type="url" value="<?php if($edit) echo (isset($risultato->Url)?ap_sanifica_testo($risultato->Url):__("Non Definito","albo-online")); else echo (isset($_REQUEST['unitao-url'])?ap_sanifica_testo($_REQUEST['unitao-url']):"");?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-email"><?php _e("Email","albo-online");?> <span style="color:red;font-weight: bold;">*</span></label>
			<input name="unitao-email" id="<?php _e("Email","albo-online");?>" type="email" required value="<?php if($edit) echo (isset($risultato->Email)?ap_sanifica_testo($risultato->Email):__("Non Definito","albo-online")); else echo ((isset($_REQUEST['unitao-email'])?ap_sanifica_testo($_REQUEST['unitao-email']):""));?>" size="100"/>
		</div>
		<div class="form-field form-required"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-pec"><?php _e("Pec","albo-online");?></label>
			<input name="unitao-pec" id="unitao-pec" type="email" value="<?php if($edit) echo (isset($risultato->Pec)?ap_sanifica_testo($risultato->Pec):__("Non Definito","albo-online")); else echo ((isset($_REQUEST['unitao-pec'])?ap_sanifica_testo($_REQUEST['unitao-pec']):""));?>" size="100"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-telefono"><?php _e("Telefono","albo-online");?></label>
			<input name="unitao-telefono" id="unitao-telefono" type="text" value="<?php if($edit) echo (isset($risultato->Telefono)?ap_sanifica_testo($risultato->Telefono):__("Non Definito","albo-online")); else echo ((isset($_REQUEST['unitao-telefono'])?ap_sanifica_testo($_REQUEST['unitao-telefono']):"")); ?>"' size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="unitao-fax"><?php _e("Fax","albo-online");?></label>
			<input name="unitao-fax" id="unitao-fax" type="text" value="<?php if($edit) echo (isset($risultato->Fax)?ap_sanifica_testo($risultato->Fax):__("Non Definito","albo-online")); else echo ((isset($_REQUEST['unitao-fax'])?ap_sanifica_testo($_REQUEST['unitao-fax']):"")); ?>" size="30"/>
		</div>
		<div class="form-field"  style="margin-bottom:0px;margin-top:0px;">
			<label for="tag-description"><?php _e("Note","albo-online");?></label>
			<textarea name="unitao-note" id="unitao-note" rows="5" cols="40"><?php if($edit) echo (isset($risultato->Note)?ap_sanifica_areatesto($risultato->Note):__("Non Definito","albo-online")); else echo ((isset($_REQUEST['unitao-note'])?ap_sanifica_areatesto($_REQUEST['unitao-note']):"")); ?></textarea>
			<p><?php _e("inserire eventuali informazioni aggiuntive","albo-online");?></p>
		</div>

<?php
if($edit) {
	if(isset($risultato->Nome)){
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. __("Memorizza Modifiche Unità Organizzativa","albo-online").' '.(isset($risultato->Nome)?ap_sanifica_testo($risultato->Nome):"").'" rel="'.(isset($risultato->Nome)?ap_sanifica_testo($risultato->Nome):"").'" />';
	}
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. __("Memorizza Modifiche Unità Organizzativa","albo-online").' '.(isset($_GET['unitao-nome'])?ap_sanifica_testo($_GET['unitao-nome']):"").'" rel="'.(isset($_GET['unitao-nome'])?ap_sanifica_testo($_GET['unitao-nome']):"").'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. __("Aggiungi nuovo Unità Organizzativa","albo-online").'"  />';
}
?>
	</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->