<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

function Visualizza_Atto($Parametri){
	ob_start();
	if(isset($_GET["titolo"])){
		$Titolo=sanitize_text_field(wp_unslash($_GET["titolo"]));
	}else{
		if (isset($Parametri['titolo'])){
			$Titolo=$Parametri['titolo'];	
		}
	}
	if (isset($Parametri['numero']) And is_numeric($Parametri['numero'])){
		$Numero=$Parametri['numero'];	
	}else{
		if(isset($_GET["numero"]) And is_numeric($_GET["numero"])){
			$Numero=$_GET["numero"];
		}else{
			echo esc_html__("Parametro Numero Atto non impostato","albo-pretorio-considera");
			return ob_get_clean();		
		}
	}
	if (isset($Parametri['anno']) And is_numeric($Parametri['anno'])){
		$Anno=$Parametri['anno'];	
	}else{
		if(isset($_GET["anno"]) And is_numeric($_GET["anno"])){
			$Anno=$_GET["anno"];
		}else{
			echo esc_html__("Parametro Anno Atto non impostato","albo-pretorio-considera");
			return ob_get_clean();
		}
	}
	$risultato=ap_get_all_atti(0,$Numero,$Anno);
	if(count($risultato)==0){
		echo esc_html__("Nessun atto trovato con questi parametri","albo-pretorio-considera");
		return ob_get_clean();
	}
	$risultato=$risultato[0];
	$id=$risultato->IdAtto;
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$allegati=ap_get_all_allegati_atto($id);
	$responsabile=ap_get_responsabile($risultato->RespProc);
	$responsabile=$responsabile[0];
	ap_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$Unitao=ap_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
	$NomeResp=ap_get_responsabile($risultato->RespProc);
	$NomeResp=$NomeResp[0];	
	if($risultato->DataAnnullamento!='0000-00-00')
		$Annullato=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __('%1$sAtto Annullato dal Responsabile del Procedimento %2$s Motivo: %3$s','albo-pretorio-considera'),'<p style="background-color: '.esc_attr($coloreAnnullati).';text-align:center;font-size:1.5em;">','<br /><br />','<span style="font-size:1;font-style: italic;">'.esc_html(stripslashes($risultato->MotivoAnnullamento)).'</span></p>');
	else
		$Annullato='';
	$Stato="Scaduto";
	if ($risultato->DataFine>gmdate("Y-m-d"))
		$Stato=__("In corso di Validità","albo-pretorio-considera");
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- template view atto: markup fisso + label i18n del plugin, con valori DB/utente escapati singolarmente (esc_html/esc_url/esc_attr).
echo '
<div>
	<p style="margin-bottom:1.5em;">
	'.$Annullato.'
	</p>
	<h3>'.esc_html($Titolo).'</h3>
	<div class="Grid Grid--withGutter u-padding-all-l">
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Stato Atto","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($Stato).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Ente titolare dell'Atto","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes(ap_get_ente($risultato->Ente)->Nome)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Numero Albo","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($risultato->Numero)."/".esc_html($risultato->Anno).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Codice di Riferimento","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($risultato->Riferimento)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Oggetto","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-margin-top-xs u-padding-left-m u-padding-right-m u-padding-top-xxs u-padding-bottom-s u-border-bottom-xxs">'.esc_html(stripslashes($risultato->Oggetto)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Data di registrazione","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(ap_VisualizzaData($risultato->Data)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Data inizio Pubblicazione","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(ap_VisualizzaData($risultato->DataInizio)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Data fine Pubblicazione","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(ap_VisualizzaData($risultato->DataFine)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Data oblio","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(ap_VisualizzaData($risultato->DataOblio)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Richiedente","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($risultato->Richiedente)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($Unitao->Nome)).'</div>
		</div>		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($NomeResp->Nome." ".$NomeResp->Cognome)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Categoria","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($risultatocategoria->Nome)).'</div>
		</div>';
$MetaDati=ap_get_meta_atto($id);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);
		echo'	
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Meta Dati","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($Meta).'</div>
		</div>';
}
echo'		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xxs u-borderRadius-m u-padding-all-m">'.__("Note","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-margin-top-xs u-padding-left-m u-padding-right-m u-padding-top-xxs u-padding-bottom-s u-border-bottom-xxs">'.(strlen(stripslashes($risultato->Informazioni))>0?wp_kses_post(stripslashes($risultato->Informazioni)):"&nbsp;&nbsp;").'</div>
		</div>
	</div>
</div>';
$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
$Soggetti=ap_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
$Ruolo="";
if($Soggetti){
	echo "		<h3 style=\"text-align:center;\">".__("Soggetti","albo-pretorio-considera")."</h3>";
}
foreach($Soggetti as $Soggetto){
	if(ap_get_Funzione_Responsabile($Soggetto->Funzione,"Display")=="No"){
		continue;
	}
	if($Soggetto->Funzione!=$Ruolo And $Ruolo!=""){
		echo '</div>';
	}
	if($Soggetto->Funzione!=$Ruolo){
		echo '<div>
	<h4>'.esc_html(ap_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")).'</h4>
	<div class="Grid Grid--withGutter u-padding-all-l">';
	}
	$Ruolo=$Soggetto->Funzione;
	echo'		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Persona","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($Soggetto->Cognome." ".$Soggetto->Nome).'</div>
		</div>';		
	if ($Soggetto->Email)
	echo'				<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Email","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs"><a href="'.esc_url('mailto:'.$Soggetto->Email).'">'.esc_html($Soggetto->Email).'</a></div>
		</div>';
	if ($Soggetto->Telefono)
	echo'					<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Telefono","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($Soggetto->Telefono).'</div>
		</div>		
		<tr>';
	if ($Soggetto->Orario)
	echo'				<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Orario ricevimento","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($Soggetto->Orario).'</div>
		</div>
		<tr>';
	if ($Soggetto->Note)
	echo'		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.__("Note","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html($Soggetto->Note).'</div>
		</div>';
echo'</div>';
}
if($Ruolo!=""){
	echo '</div>';
}
$TipidiFiles=ap_get_tipidifiles();
if (strpos(get_permalink(),"?")>0)
	$sep="&";
else
	$sep="?";
$documenti=ap_get_documenti_atto($id);
$StatoAllegati= get_option('opt_AP_Allegati');
if(count($documenti)>0){
	echo '
	<div class="Grid Grid--withGutter u-padding-all-l">
		<h3>'. __("Documenti firmati","albo-pretorio-considera").'</h3>
			<div class="Grid Grid--withGutter u-padding-all-l">';
	//print_r($_SERVER);
	foreach ($documenti as $allegato) {
		$Estensione=ap_ExtensionType($allegato->Allegato);
		echo'
			<div class="Grid-cell HeadAllegati">
				<div class="u-margin-bottom-xs u-borderRadius-m u-padding-all-m u-border-all-xxs">
					<img src="'.esc_url($TipidiFiles[strtolower($Estensione)]['Icona']).'" alt="'.esc_attr($TipidiFiles[strtolower($Estensione)]['Descrizione']).'" height="30" width="30"allegato/> '.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.__("Descrizione","albo-pretorio-considera").'</strong>: '.wp_strip_all_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato))).'</strong><br /><strong>'.__("Impronta","albo-pretorio-considera").'</strong>: '.esc_html($allegato->Impronta).'<br /><strong>'.__("Dimensione file","albo-pretorio-considera").'</strong>: '.esc_html(ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0))."<br />";
				if (is_file($allegato->Allegato)){
					if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
						echo '<a href="'.esc_url(ap_DaPath_a_URL($allegato->Allegato)).'" class="addstatdw noUnderLine" rel="'.esc_url(get_permalink().$sep.'action=addstatall&id='.$allegato->IdAllegato.'&idAtto='.$id).'" title="'.esc_attr__("Visualizza Allegato","albo-pretorio-considera").'" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg> </a> ';
					}
					if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
						echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.esc_url(get_permalink().$sep.'action=dwnalle&id='.$allegato->IdAllegato.'&idAtto='.$id).'" class="noUnderLine" title="'.esc_attr__("Scarica allegato","albo-pretorio-considera").'">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>';
						if($StatoAllegati=="dwn"){
							echo " <strong>".esc_html(basename( $allegato->Allegato)).'</strong></a>';
						}else{
							echo "</a>";
						}
					}
				}else
					echo esc_html(basename( $allegato->Allegato)).' '.esc_html__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
		echo '</div>
			</div>
		';
		}
	echo '</div>
</div>';
}
$allegati=ap_get_allegati_atto($id);
if(count($allegati)>0){
	echo '
	<div class="Grid Grid--withGutter u-padding-all-l">
		<h3>'. __("Allegati","albo-pretorio-considera").'</h3>
			<div class="Grid Grid--withGutter u-padding-all-l">';
	//print_r($_SERVER);
	foreach ($allegati as $allegato) {
		$Estensione=ap_ExtensionType($allegato->Allegato);
		echo'
			<div class="Grid-cell HeadAllegati">
				<div class="u-margin-bottom-xs u-borderRadius-m u-padding-all-m u-border-all-xxs">
					<img src="'.esc_url($TipidiFiles[strtolower($Estensione)]['Icona']).'" alt="'.esc_attr($TipidiFiles[strtolower($Estensione)]['Descrizione']).'" height="30" width="30"allegato/> '.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.__("Descrizione","albo-pretorio-considera").'</strong>: '.wp_strip_all_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato))).'</strong><br /><strong>'.__("Impronta","albo-pretorio-considera").'</strong>: '.esc_html($allegato->Impronta).'<br /><strong>'.__("Dimensione file","albo-pretorio-considera").'</strong>: '.esc_html(ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0))."<br />";
				if (is_file($allegato->Allegato)){
					if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
						echo '<a href="'.esc_url(ap_DaPath_a_URL($allegato->Allegato)).'" class="addstatdw noUnderLine" rel="'.esc_url(get_permalink().$sep.'action=addstatall&id='.$allegato->IdAllegato.'&idAtto='.$id).'" title="'.esc_attr__("Visualizza Allegato","albo-pretorio-considera").'" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg></a> ';
					}
					if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
						echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.esc_url(get_permalink().$sep.'action=dwnalle&id='.$allegato->IdAllegato.'&idAtto='.$id).'" class="noUnderLine" title="'.esc_attr__("Scarica allegato","albo-pretorio-considera").'">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>';
						if($StatoAllegati=="dwn"){
							echo " <strong>".esc_html(basename( $allegato->Allegato)).'</strong></a>';
						}else{
							echo "</a>";
						}	
					}
				}else
					echo esc_html(basename( $allegato->Allegato)).' '.esc_html__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
		echo '</div>
			</div>
		';
		}
	echo '</div>
</div>
<div class="Prose Alert Alert--info Alert--withIcon" role="alert">
    <h2 class="u-text-h3">'.__("Informazioni","albo-pretorio-considera").'</h2>
    <p class="u-text-p">'.__("L'impronta dei files è calcolata con algoritmo SHA256 al momento dell'upload","albo-pretorio-considera").'</p>
</div>';
}
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
return ob_get_clean();
}
?>
