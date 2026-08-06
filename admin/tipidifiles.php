<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione Responsabili.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- pagina di sola VISUALIZZAZIONE/redisplay del form: le letture di $_REQUEST/$_GET servono a ripopolare il form (link edit via GET, valori dopo errore di validazione); le MUTAZIONI effettive (add/memo/delete) avvengono negli handler di admin.php, ciascuno protetto da wp_verify_nonce.

$albopc_messages[1] = __('Elemento aggiunto.','albo-pretorio-considera');
$albopc_messages[2] = __('Elemento cancellato.','albo-pretorio-considera');
$albopc_messages[3] = __('Elemento aggiornato.','albo-pretorio-considera');
$albopc_messages[4] = __('Elemento non aggiunto.','albo-pretorio-considera');
$albopc_messages[5] = __('Elemento non aggiornato.','albo-pretorio-considera');
$albopc_messages[6] = __('Elemento non cancellato.','albo-pretorio-considera');
$albopc_messages[7] = __('Impossibile cancellare i Tipi di files che sono collegati ad Atti','albo-pretorio-considera');
$albopc_messages[8] = __('Impossibile creare il Tipo di file perchè mancano dati obbligatori','albo-pretorio-considera');
$albopc_messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-considera");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-considera");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php  esc_html_e("Correggere gli errori per continuare","albo-pretorio-considera");?></p>
</div>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-paperclip"></span> <?php esc_html_e("Tipi di Files","albo-pretorio-considera");?>
		<a href="?page=tipifiles" class="add-new-h2"><?php esc_html_e("Aggiungi nuovo","albo-pretorio-considera");?></a></h2>
	</div>
<?php
$albopc_lista=albopc_get_tipidifiles(); 
$albopc_NC="";
if (isset($_REQUEST['action']) And $_REQUEST['action']=="delete-tipidifiles"){
	if (!isset($_REQUEST['canctipfil'])) {
		$albopc_NC=$albopc_messages[80];
	}else{
		if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['canctipfil'])),'deletetipidifiles')){
			$albopc_NC=$albopc_messages[80];
		}else{
			$albopc_risultato=albopc_del_tipidifiles((isset($_REQUEST['id'])?intval($_REQUEST['id']):0));
			if(is_array($albopc_risultato)){
				/* translators: %s: numero di atti che utilizzano il tipo di file */
				$albopc_NC=sprintf(__("Il Tipo di File non può essere cancellato perchè ci sono %s atti che lo utilizzano","albo-pretorio-considera"),$albopc_risultato["atti"]);
			}
		}
	}	
} 
if ( (isset($_REQUEST['message']) && ( $albopc_msg = (isset($_REQUEST['message'])?intval($_REQUEST['message']):0))) or $albopc_NC!="") {
	echo '<div id="message" class="updated"><p>'.esc_html($albopc_messages[$albopc_msg]). esc_html($albopc_NC);
	if (isset($_REQUEST['errore'])) 
		echo '<br />'.esc_html(sanitize_text_field(wp_unslash($_REQUEST['errore'])));
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$albopc_edit=True;
}else{
	$albopc_edit=False;
}
?>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3><?php  esc_html_e("Elenco Tipi di Files","albo-pretorio-considera");?> <a href="?page=tipifiles&action=set-default&tipifiles=<?php echo esc_attr(wp_create_nonce('elabtipifiles'))?>" class="add-new-h2"><?php  esc_html_e("Reimposta Estensioni di Default","albo-pretorio-considera");?></a></h3>
<table class="widefat" id="elenco-tipidifiles"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php  esc_html_e("Tipi di Files","albo-pretorio-considera");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
echo '<tr>
        	<td>
			<ul>';
if ($albopc_lista){
	$albopc_Tipi=albopc_num_tipidifiles_atti();
	foreach($albopc_lista as $albopc_TipoFile =>$riga){
		echo'<li style="text-align:left;padding-left:1px;">';
		$albopc_Tab=0;
		if($albopc_Tipi[strtolower($albopc_TipoFile)]==0 and $albopc_TipoFile!="ndf")
			echo '<span class="cancella"><a href="?page=tipifiles&amp;action=delete-tipofile&amp;id='.esc_attr($albopc_TipoFile).'&amp;canctipfil='.esc_attr(wp_create_nonce('deletetipofile')).'" rel="'.esc_attr($albopc_riga['Descrizione']).'" class="confdel">
			<span class="dashicons dashicons-trash" title="'.esc_html__('Cancella tipo file','albo-pretorio-considera').'"></span>
			</a></span>';
		else
			$albopc_Tab=23;
		echo '
			<a href="?page=tipifiles&amp;action=edit-tipofile&amp;id='.esc_attr($albopc_TipoFile).'&amp;modtipfil='.esc_attr(wp_create_nonce('edittipofilee')).'" rel="'.esc_attr($albopc_riga['Descrizione']).'">
			<span class="dashicons dashicons-edit" title="'.esc_html__('Modifica tipo file','albo-pretorio-considera').'" style="margin-left:'.(int)$albopc_Tab.'px;"></span>
			</a>
			('.esc_html($albopc_TipoFile).') '.esc_html($albopc_riga['Descrizione']) .($albopc_TipoFile!="ndf"?'(n&ordm; atti '.(int)($albopc_Tipi[$albopc_TipoFile]).')':"").'</li>'; 
	}
} else {
		echo '<li>'.esc_html__('Nessun Tipo File Codificato','albo-pretorio-considera').'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$albopc_righe=albopc_get_all_Oggetto_log(8);
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
foreach ($albopc_righe as $albopc_riga) {
	switch ($albopc_riga->TipoOperazione){
	 	case 1:
	 		$albopc_Operazione=__("Inserimento","albo-pretorio-considera");
	 		break;
	 	case 2:
	 		$albopc_Operazione=__("Modifica","albo-pretorio-considera");
			break;
	 	case 3:
	 		$albopc_Operazione=__("Cancellazione","albo-pretorio-considera");
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
$albopc_IDTipo=isset($_REQUEST['id'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['id']))):"";
?>
</div><!-- /col-right -->

<div id="col-left">
	<div class="Obbligatori">
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-considera"),"<strong>","</strong>"));?>
	</div>
	<br />
<div class="form-wrap">
	<form id="addtag" method="post" action="?page=tipifiles" class="<?php if($albopc_edit) echo "edit"; else echo "validate"; ?>"  >
		<input type="hidden" name="action" value="<?php if($albopc_edit ||(isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-tipofile"; else echo "add-tipofile"; ?>"/>
		<input type="hidden" name="id" value="<?php echo esc_attr($albopc_IDTipo); ?>" />
		<input type="hidden" name="tipifiles" value="<?php echo esc_attr(wp_create_nonce('elabtipifiles'))?>" />
		<div class="form-required">
			<label for="estensione"><?php esc_html_e("Tipo File","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></label>
			<input name="id" id="<?php esc_html_e("Tipo File","albo-pretorio-considera");?>" type="text" value="<?php if($albopc_edit) echo esc_attr($albopc_IDTipo);?>" size="6" aria-required="true" <?php echo ($albopc_edit?'Disabled':"");?> required/>
		</div>
		<div class="form-field form-required">
			<label for="descrizione"><?php esc_html_e("Descrizione","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></label>
			<input name="descrizione" id="<?php esc_html_e("Descrizione","albo-pretorio-considera");?>" type="text" value="<?php if($albopc_edit) echo esc_attr(sanitize_text_field(isset($albopc_lista[$albopc_IDTipo]["Descrizione"])?$albopc_lista[$albopc_IDTipo]["Descrizione"]:"")); ?>" size="60" aria-required="true" required/>
		</div>
		<div class="form-field form-required">
			<label for="icona"><?php esc_html_e("Icona","albo-pretorio-considera");?><span style="color:red;font-weight: bold;">*</span></label>
			<input name="icona" id="<?php esc_html_e("Icona","albo-pretorio-considera");?>" type="text" value="<?php if($albopc_edit) echo esc_attr(sanitize_text_field(isset($albopc_lista[$albopc_IDTipo]["Icona"])?$albopc_lista[$albopc_IDTipo]["Icona"]:""));?>" size="60" aria-required="true" required/>
				<div style="float:left;"><input id="icona_upload" class="button" type="button" value="Carica" />
					<br /><?php esc_html_e("Dimensione max 30x30","albo-pretorio-considera");?>
				</div>
				<div style="float:left;margin-left:10%;margin-top:5px;">
		<?php if(isset($albopc_lista[$albopc_IDTipo]["Icona"]) And $albopc_lista[$albopc_IDTipo]["Icona"]){?>
					<img src="<?php if($albopc_edit) echo esc_url(stripslashes($albopc_lista[$albopc_IDTipo]["Icona"]));?>" width="30" height="30" id="IconaTipoFile"/>
		<?php }?>
		</div>
</div>
	<div class="clear"></div>
	<div class="form-field form-required">
		<label for="verifica"><?php esc_html_e("Verifica","albo-pretorio-considera");?></label>
		<input name="verifica" id="verifica" type="text" value="<?php if($albopc_edit) echo esc_attr(sanitize_text_field(isset($albopc_lista[$albopc_IDTipo]["Verifica"])?$albopc_lista[$albopc_IDTipo]["Verifica"]:""));?>" size="60" aria-required="true" />
	</div>

<?php
if($albopc_edit) {
	if(isset($albopc_lista[$albopc_IDTipo])){
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Formato File","albo-pretorio-considera").' '.$albopc_IDTipo).'" rel="'.esc_attr($albopc_IDTipo).'" />';
	}
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Formato File","albo-pretorio-considera").' '.(isset($_GET['resp-cognome'])?sanitize_text_field(wp_unslash($_GET['resp-cognome'])):"")).'" rel="'.esc_attr(isset($_GET['resp-cognome'])?sanitize_text_field(wp_unslash($_GET['resp-cognome'])):"").'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuovo Tipo File","albo-pretorio-considera").'"  />';	
}
?>
</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->