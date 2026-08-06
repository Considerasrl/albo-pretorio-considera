<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- shortcode/vista pubblica read-only: legge id e parametri di ricerca/filtro via GET per la sola visualizzazione (pre-sanitizzati), nessuna mutazione di stato.
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }

function albopc_Visualizza_Atto($Parametri){
	ob_start();
	if(isset($_GET["titolo"])){
		$Titolo=sanitize_text_field(wp_unslash($_GET["titolo"] ?? ''));
	}else{
		if (isset($Parametri['titolo'])){
			$Titolo=$Parametri['titolo'];	
		}
	}
	if (isset($Parametri['numero']) And is_numeric($Parametri['numero'])){
		$Numero=$Parametri['numero'];	
	}else{
		if(isset($_GET["numero"]) And is_numeric(sanitize_text_field(wp_unslash($_GET["numero"] ?? '')))){
			$Numero=sanitize_text_field(wp_unslash($_GET["numero"] ?? ''));
		}else{
			echo esc_html__("Parametro Numero Atto non impostato","albo-pretorio-considera");
			return ob_get_clean();		
		}
	}
	if (isset($Parametri['anno']) And is_numeric($Parametri['anno'])){
		$Anno=$Parametri['anno'];	
	}else{
		if(isset($_GET["anno"]) And is_numeric(sanitize_text_field(wp_unslash($_GET["anno"] ?? '')))){
			$Anno=sanitize_text_field(wp_unslash($_GET["anno"] ?? ''));
		}else{
			echo esc_html__("Parametro Anno Atto non impostato","albo-pretorio-considera");
			return ob_get_clean();
		}
	}
	$risultato=albopc_get_all_atti(0,$Numero,$Anno);
	if(count($risultato)==0){
		echo esc_html__("Nessun atto trovato con questi parametri","albo-pretorio-considera");
		return ob_get_clean();
	}
	$risultato=$risultato[0];
	$id=$risultato->IdAtto;
	$risultatocategoria=albopc_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$allegati=albopc_get_all_allegati_atto($id);
	albopc_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$Unitao=albopc_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
	$NomeResp=albopc_get_responsabile($risultato->RespProc);
	$NomeResp=$NomeResp[0];
	if($risultato->DataAnnullamento!='0000-00-00')
		$Annullato=sprintf(/* translators: %1$s e %2$s: markup; %3$s: motivo annullamento */ __('%1$sAtto Annullato dal Responsabile del Procedimento %2$s Motivo: %3$s','albo-pretorio-considera'),'<p style="background-color: '.esc_attr($coloreAnnullati).';text-align:center;font-size:1.5em;">','<br /><br />','<span style="font-size:1;font-style: italic;">'.esc_html(stripslashes($risultato->MotivoAnnullamento)).'</span></p>');
	else
		$Annullato='';
	$Stato="Scaduto";
	if ($risultato->DataFine>gmdate("Y-m-d"))
		$Stato=__("In corso di Validità","albo-pretorio-considera");
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- template view atto: markup fisso intercalato a valori dinamici escapati singolarmente (esc_html sui testi DB, esc_url sugli href, esc_attr sugli attributi). $Annullato e i link contengono markup fisso + dati gia' escapati.
echo '
<div class="Visalbo">
<h3>'.esc_html($Titolo).'</h3>
<p>'.$Annullato.'</p>
<table class="tabVisalbo">
	    <tbody id="dati-atto">
	    <tr>
	    	<th>'.__("Stato Atto","albo-pretorio-considera").'</th>
	    	<td style="font-weght: bold;font-size: 1.5em;vertical-align: middle;">'.esc_html($Stato).'
	    	</td>
	    </tr>
		<tr>
			<th>'.__("Ente titolare dell'Atto","albo-pretorio-considera").'</th>
			<td style="font-weght: bold;font-size: 1.5em;vertical-align: middle;">'.esc_html(stripslashes(albopc_get_ente($risultato->Ente)->Nome)).'</td>
		</tr>
		<tr>
			<th>'.__("Numero Albo","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html($risultato->Numero)."/".esc_html($risultato->Anno).'</td>
		</tr>
		<tr>
			<th>'.__("Codice di Riferimento","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(stripslashes($risultato->Riferimento)).'</td>
		</tr>
		<tr>
			<th>'.__("Oggetto","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(stripslashes($risultato->Oggetto)).'</td>
		</tr>
		<tr>
			<th>'.__("Data di registrazione","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(albopc_VisualizzaData($risultato->Data)).'</td>
		</tr>
		<tr>
			<th>'.__("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(albopc_VisualizzaData($risultato->DataInizio)).'</td>
		</tr>
		<tr>
			<th>'.__("Data fine Pubblicazione","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(albopc_VisualizzaData($risultato->DataFine)).'</td>
		</tr>
		<tr>
			<th>'.__("Data oblio","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(albopc_VisualizzaData($risultato->DataOblio)).'</td>
		</tr>
		<tr>
			<th>'.__("Richiedente","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(stripslashes($risultato->Richiedente)).'</td>
		</tr>
		<tr>
			<th>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(stripslashes($Unitao->Nome)).'</td>
		</tr>
		<tr>
			<th>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(stripslashes($NomeResp->Nome." ".$NomeResp->Cognome)).'</td>
		</tr>
		<tr>
			<th>'.__("Categoria","albo-pretorio-considera").'</th>
			<td style="vertical-align: middle;">'.esc_html(stripslashes($risultatocategoria->Nome)).'</td>
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
					<th>'.__("Meta Dati","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;">'.esc_html($Meta).'</td>
				</tr>';
}
echo'		<tr>
				<th>'.__("Note","albo-pretorio-considera").'</th>
				<td style="vertical-align: middle;">'.wp_kses_post(stripslashes($risultato->Informazioni)).'</td>
			</tr>
 	    </tbody>
	</table>';
$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
$Soggetti=albopc_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
$Ruolo="";
if($Soggetti){
	echo "		<h3 style=\"text-align:center;\">".__("Soggetti","albo-pretorio-considera")."</h3>";
}
foreach($Soggetti as $Soggetto){
	if(albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Display")=="No"){
		continue;
	}
	if($Soggetto->Funzione!=$Ruolo And $Ruolo!=""){
		echo '</div>';
	}
	if($Soggetto->Funzione!=$Ruolo){
		echo '<h4>'.esc_html(albopc_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione")).'</h4>
	<div class="Visallegato">';
	}
	$Ruolo=$Soggetto->Funzione;
	echo'		<table class="tabVisResp">
	    		<tbody>
				<tr>
					<th>'.__("Persona","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;">'.esc_html($Soggetto->Cognome." ".$Soggetto->Nome).'</td>
				</tr>';
	if ($Soggetto->Email)
	echo'		<tr>
					<th>'.__("Email","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;"><a href="'.esc_url('mailto:'.$Soggetto->Email).'">'.esc_html($Soggetto->Email).'</a></td>
				</tr>';
	if ($Soggetto->Telefono)
	echo'			<tr>
					<th>'.__("Telefono","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;">'.esc_html($Soggetto->Telefono).'</td>
				</tr>';
	if ($Soggetto->Orario)
	echo'		<tr>
					<th>'.__("Orario ricevimento","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;">'.esc_html($Soggetto->Orario).'</td>
				</tr>';
	if ($Soggetto->Note)
	echo'
				<tr>
					<th>'.__("Note","albo-pretorio-considera").'</th>
					<td style="vertical-align: middle;">'.esc_html($Soggetto->Note).'</td>
				</tr>';
echo'
			    </tbody>
			</table>';
}
if($Ruolo!=""){
	echo '</div>';
}
$TipidiFiles=albopc_get_tipidifiles();
if (strpos(get_permalink(),"?")>0)
	$sep="&";
else
	$sep="?";
$documenti=albopc_get_documenti_atto($id);
$StatoAllegati= get_option('opt_AP_Allegati');
if(count($documenti)>0){
	echo '<div class="postbox" style="padding:0 10px 10px 10px;">
		<h3>'. __("Documenti firmati","albo-pretorio-considera").'</h3>';
	foreach ($documenti as $allegato) {
		$Estensione=albopc_ExtensionType($allegato->Allegato);
		echo '<div class="Visallegato">
				<div class="Allegato">
					<img src="'.esc_url($TipidiFiles[strtolower($Estensione)]['Icona']).'" alt="'.esc_attr($TipidiFiles[strtolower($Estensione)]['Descrizione']).'" height="30" width="30"allegato/>
				</div>
				<div>
					<p class="secondaColonna">'.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.__("Descrizione","albo-pretorio-considera").'</strong>: '.esc_html(wp_strip_all_tags($allegato->TitoloAllegato)).'<br /><strong>'.__("Impronta","albo-pretorio-considera").'</strong>: '.esc_html($allegato->Impronta).'<br />';
				if (is_file($allegato->Allegato)){
					if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
						echo '<a href="'.esc_url(albopc_DaPath_a_URL($allegato->Allegato)).'" class="addstatdw" rel="'.esc_url(get_permalink().$sep.'action=addstatall&id='.$allegato->IdAllegato.'&idAtto='.$id).'" target="_blank" title="'.esc_attr__("Visualizza Allegato","albo-pretorio-considera").'">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg> '. esc_html(basename( $allegato->Allegato)).'</a> ('.esc_html(albopc_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0)).')<br />';
					}
					if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
						echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.esc_url(get_permalink().$sep.'action=dwnalle&id='.$allegato->IdAllegato.'&idAtto='.$id).'" >
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg> '.__("Scarica allegato","albo-pretorio-considera");
						if($StatoAllegati=="dwn"){
							echo " <strong>".esc_html(basename( $allegato->Allegato)).'</strong></a>';
						}else{
							echo "</a>";
						}
					}
				}else
					echo esc_html(basename( $allegato->Allegato)).' '.esc_html__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
		echo'				</p>
				</div>
			</div>
			';
		}
	echo '</div>';
}	
$allegati=albopc_get_allegati_atto($id);
if(count($allegati)>0){
	echo '<div class="postbox" style="padding:0 10px 10px 10px;">
		<h3>'. __("Allegati","albo-pretorio-considera").'</h3>';
	foreach ($allegati as $allegato) {
		$Estensione=albopc_ExtensionType($allegato->Allegato);
		echo '<div class="Visallegato">
				<div class="Allegato">
					<img src="'.esc_url($TipidiFiles[strtolower($Estensione)]['Icona']).'" alt="'.esc_attr($TipidiFiles[strtolower($Estensione)]['Descrizione']).'" height="30" width="30"allegato/>
				</div>
				<div>
					<p class="secondaColonna">'.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.__("Descrizione","albo-pretorio-considera").'</strong>: '.esc_html(wp_strip_all_tags($allegato->TitoloAllegato)).'<br /><strong>'.__("Impronta","albo-pretorio-considera").'</strong>: '.esc_html($allegato->Impronta).'<br />';
				if (is_file($allegato->Allegato)){
					if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
						echo '<a href="'.esc_url(albopc_DaPath_a_URL($allegato->Allegato)).'" class="addstatdw" rel="'.esc_url(get_permalink().$sep.'action=addstatall&id='.$allegato->IdAllegato.'&idAtto='.$id).'" target="_blank" title="'.esc_attr__("Visualizza Allegato","albo-pretorio-considera").'">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg> '. esc_html(basename( $allegato->Allegato)).'</a> ('.esc_html(albopc_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0)).')<br />';
					}
					if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
						echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.esc_url(get_permalink().$sep.'action=dwnalle&id='.$allegato->IdAllegato.'&idAtto='.$id).'" >
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg> '.__("Scarica allegato","albo-pretorio-considera");
						if($StatoAllegati=="dwn"){
							echo " <strong>".esc_html(basename( $allegato->Allegato)).'</strong></a>';
						}else{
							echo "</a>";
						}
					}
				}else
					echo esc_html(basename( $allegato->Allegato)).' '.esc_html__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
				echo'				</p>
				</div>
			</div>
			';
		}
	echo '</div>';
}	
echo '
	<div class="VisInfo">
	    <p class="text-1"><strong><span class="dashicons dashicons-info"></span> '.__("Informazioni","albo-pretorio-considera").'</strong>: '.__("L'impronta dei files è calcolata con algoritmo SHA256 al momento dell'upload","albo-pretorio-considera").'</p>
	</div>
</div>';
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
return ob_get_clean();
}
?>
