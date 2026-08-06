<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione Soggetti Procedimento.
 * @link       http://www.eduva.org
 * @since     4.8
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
$albopc_messages[7] = __('Impossibile cancellare Soggetti che sono collegati ad Atti','albo-pretorio-on-line');
$albopc_messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-on-line");
?>
<div id="errori" title="<?php esc_html_e("Validazione Dati","albo-pretorio-on-line");?>" style="display:none">
  <h3><?php esc_html_e("Lista Campi con Errori","albo-pretorio-on-line");?>:</h3>
  	<p id="ElencoCampiConErrori"></p>
  	<p style='color:red;font-weight: bold;'><?php  esc_html_e("Correggere gli errori per continuare","albo-pretorio-on-line");?></p>
</div>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-businessman" style="font-size: 1.1em;"></span> <?php esc_html_e("Soggetti Procedimento","albo-pretorio-on-line");?>
		<a href="?page=soggetti" class="add-new-h2"><?php esc_html_e("Aggiungi nuovo","albo-pretorio-on-line");?></a></h2>
	</div>
<?php
$albopc_SoggettiAtti=albopc_get_NumAttiSoggetti();
$albopc_NC="";
if (isset($_REQUEST['action']) And $_REQUEST['action']=="delete-responsabile"){
	if (!isset($_REQUEST['cancresp'])) {
		$albopc_NC=$albopc_messages[80];
	}else{
		if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cancresp'])),'deleteresponsabile')){
			$albopc_NC=$albopc_messages[80];
		}else{
			if(isset($albopc_SoggettiAtti[(isset($_REQUEST['id'])?intval($_REQUEST['id']):0)]) And $albopc_SoggettiAtti[(isset($_REQUEST['id'])?intval($_REQUEST['id']):0)]>0){
				$albopc_NC=__('Impossibile cancellare Soggetti che sono collegati ad Atti','albo-pretorio-on-line');
			}else{
				$albopc_NC=albopc_del_responsabile((isset($_REQUEST['id'])?intval($_REQUEST['id']):0));
			}
		}
	}	
} 
if ( (isset($_REQUEST['message']) && ( $albopc_msg = (isset($_REQUEST['message'])?intval($_REQUEST['message']):0))) or $albopc_NC!="") {
	echo '<div id="message" class="updated"><p>'.esc_html(isset($albopc_msg)?$albopc_messages[$albopc_msg]:""). esc_html($albopc_NC);
	if (isset($_REQUEST['errore']))
		echo '<br />'.esc_html(sanitize_text_field(wp_unslash($_REQUEST['errore'])));
	echo '</p></div>';
	$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
}
if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit"){
	$albopc_risultato=albopc_get_responsabile((isset($_REQUEST['id'])?intval($_REQUEST['id']):0));
	$albopc_edit=True;
}else{
	$albopc_edit=False;
}

?>
<br class="clear" />
<div id="col-container">
<div id="col-right">
<div class="col-wrap">
<h3><?php  esc_html_e("Elenco Soggetti","albo-pretorio-on-line");?></h3>
<table class="widefat" id="elenco-responsabili"> 
    <thead>
    	<tr>
        	<th scope="col" style="text-align:center;"><?php  esc_html_e("Soggetti","albo-pretorio-on-line");?></th>
		</tr>
    </thead>
    <tbody id="the-list">
<?php 
$albopc_lista=albopc_get_responsabili(); 
echo '<tr>
        	<td>
			<ul>';
if ($albopc_lista){
	foreach($albopc_lista as $albopc_riga){
		$albopc_Funzione=albopc_get_Funzione_Responsabile($albopc_riga->Funzione,"Descrizione");
		echo'<li style="text-align:left;padding-left:1px;">';
		$albopc_Tab=0;
		if(albopc_get_NumAttiSoggetto($albopc_riga->IdResponsabile)==0)
			echo '<span class="cancella"><a href="?page=soggetti&amp;action=delete-responsabile&amp;id='.(int)$albopc_riga->IdResponsabile.'&amp;cancresp='.esc_attr(wp_create_nonce('deleteresponsabile')).'" rel="'.esc_attr($albopc_riga->Cognome).'" class="dr">
			<span class="dashicons dashicons-trash" title="'.esc_attr__("Cancella soggetto","albo-pretorio-on-line").'"></span>
			</a></span>';
		else
			$albopc_Tab=23;
		echo '
			<a href="?page=soggetti&amp;action=edit-responsabile&amp;id='.(int)$albopc_riga->IdResponsabile.'&amp;modresp='.esc_attr(wp_create_nonce('editresponsabile')).'" rel="'.esc_attr($albopc_riga->Cognome).'">
			<span class="dashicons dashicons-edit" title="'.esc_attr__("Modifica soggetto","albo-pretorio-on-line").'" style="margin-left:'.(int)$albopc_Tab.'px;"></span>
			</a>
			('.(int)$albopc_riga->IdResponsabile.') '.esc_html($albopc_riga->Cognome) .' (n&ordm; atti '.(int)(isset($albopc_SoggettiAtti[$albopc_riga->IdResponsabile])?$albopc_SoggettiAtti[$albopc_riga->IdResponsabile]:0).') <strong>'.esc_html($albopc_Funzione).'</strong>
			</li>';
	}
} else {
		echo '<li>'.esc_html__("Nessun Soggetto Codificato","albo-pretorio-on-line").'</li>';
}
echo '</td>
	</tr>
</ul>
	</tbody>
</table>
</div>
<div class="col-wrap">
<h3>Log</h3>';
$albopc_righe=albopc_get_all_Oggetto_log(4);
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
	<div class="Obbligatori">
		<span style="color:red;font-weight: bold;">*</span> <?php /* translators: %1$s e %2$s: tag di apertura e chiusura del grassetto */ echo wp_kses_post(sprintf(__("i campi contrassegnati dall'asterisco sono %1\$s obbligatori %2\$s","albo-pretorio-on-line"),"<strong>","</strong>"));?>
	</div>
<div class="form-wrap">
<form id="addtag" method="post" action="?page=soggetti" class="<?php if($albopc_edit) echo "edit"; else echo "validate"; ?>"  >
	<input type="hidden" name="action" value="<?php if($albopc_edit ||(isset($_REQUEST['action']) And  $_REQUEST['action']=="edit_err")) echo "memo-responsabile"; else echo "add-responsabile"; ?>"/>
	<input type="hidden" name="id" value="<?php echo isset($_REQUEST['id'])?intval($_REQUEST['id']):0; ?>" />
	<input type="hidden" name="responsabili" value="<?php echo esc_attr(wp_create_nonce('elabresponsabili'))?>" />

<div class="form-field form-required">
	<label for="resp-cognome"><?php esc_html_e("Cognome","albo-pretorio-on-line");?><span style="color:red;font-weight: bold;">*</span></label>
	<input name="resp-cognome" id="<?php esc_html_e("Cognome","albo-pretorio-on-line");?>" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato[0]->Cognome)?esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Cognome)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo esc_attr(albopc_sanifica_testo(isset($_GET['resp-cognome'])?sanitize_text_field(wp_unslash($_GET['resp-cognome'])):"")); ?>" size="20" required />
</div>
<div class="form-field form-required">
	<label for="resp-nome"><?php esc_html_e("Nome","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
	<input name="resp-nome" id="<?php esc_html_e("Nome","albo-pretorio-on-line");?>" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato[0]->Nome)?esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Nome)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo esc_attr(albopc_sanifica_testo(isset($_GET['resp-nome'])?sanitize_text_field(wp_unslash($_GET['resp-nome'])):"")); ?>" size="20" required />
</div>
<div class="form-field form-required">
	<label for="resp-funzione"><?php esc_html_e("Funzione","albo-pretorio-on-line");?></label>
	<?php /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <select> generato internamente da albopc_get_Funzioni_Responsabili */ echo albopc_get_Funzioni_Responsabili($albopc_Output="Select",$albopc_ID="resp-funzione",$albopc_Name="resp-funzione",$albopc_Selezionato=($albopc_edit)?(isset($albopc_risultato[0]->Funzione)?$albopc_risultato[0]->Funzione:""):"");?>
<div class="form-field form-required">
	<label for="resp-email"><?php esc_html_e("Email","albo-pretorio-on-line");?> <span style="color:red;font-weight: bold;">*</span></label>
	<input name="resp-email" id="<?php esc_html_e("Email","albo-pretorio-on-line");?>" type="email" value="<?php if($albopc_edit) echo isset($albopc_risultato[0]->Email)?esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Email)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo esc_attr(sanitize_text_field(isset($_GET['resp-email'])?wp_unslash($_GET['resp-email']):""));?>" size="100" required />
</div>
<div class="form-field form-required">
	<label for="resp-telefono"><?php esc_html_e("Telefono","albo-pretorio-on-line");?></label>
	<input name="resp-telefono" id="resp-telefono" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato[0]->Telefono)?esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Telefono)):esc_attr__("Non Definito","albo-pretorio-on-line"); else echo esc_attr(albopc_sanifica_testo(isset($_GET['resp-telefono'])?sanitize_text_field(wp_unslash($_GET['resp-telefono'])):"")); ?>" size="30" aria-required="true" />
</div>
<div class="form-field form-required">
	<label for="resp-orario"><?php esc_html_e("Orario ricevimento","albo-pretorio-on-line");?></label>
	<input name="resp-orario" id="resp-orario" type="text" value="<?php if($albopc_edit) echo isset($albopc_risultato[0]->Orario)?esc_attr(albopc_sanifica_testo($albopc_risultato[0]->Orario)):esc_attr__("Non Definito","albo-pretorio-on-line");  else echo esc_attr(albopc_sanifica_testo(isset($_GET['resp-orario'])?sanitize_text_field(wp_unslash($_GET['resp-orario'])):""));?>" size="60" aria-required="true" />
</div>
<div class="form-field">
	<label for="resp-description"><?php esc_html_e("Note","albo-pretorio-on-line");?></label>
	<textarea name="resp-note" id="resp-note" rows="5" cols="40"><?php if($albopc_edit) echo isset($albopc_risultato[0]->Note)?esc_textarea(albopc_sanifica_areatesto($albopc_risultato[0]->Note)):esc_html__("Non Definito","albo-pretorio-on-line"); else echo esc_textarea(albopc_sanifica_areatesto(isset($_GET['resp-note'])?sanitize_textarea_field(wp_unslash($_GET['resp-note'])):"")); ?></textarea>
	<p><?php esc_html_e("inserire eventuali informazioni aggiuntive","albo-pretorio-on-line");?></p>
</div>

<?php
if($albopc_edit) {
	if(isset($albopc_risultato[0]->Cognome)){
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Modifiche Soggetto","albo-pretorio-on-line").' '.(isset($albopc_risultato[0]->Cognome)?albopc_sanifica_testo($albopc_risultato[0]->Cognome):"")).'" rel="'.esc_attr(isset($albopc_risultato[0]->Cognome)?albopc_sanifica_testo($albopc_risultato[0]->Cognome):"").'" />';
	}
}else{
 	if (isset($_REQUEST['action']) And $_REQUEST['action']=="edit_err")
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr(__("Memorizza Dati Soggetto","albo-pretorio-on-line").' '.(isset($_GET['resp-cognome'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['resp-cognome']))):"")).'" rel="'.esc_attr(isset($_GET['resp-cognome'])?albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['resp-cognome']))):"").'" />';
	else
		echo '<input type="submit" name="SaveData" id="SaveData" class="button" value="'. esc_attr__("Aggiungi nuovo Soggetto","albo-pretorio-on-line").'"  />';
}
?>
</form>
</div>
</div><!-- /col-container -->
</div><!-- /wrap -->

