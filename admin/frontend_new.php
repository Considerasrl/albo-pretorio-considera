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

ob_start();

if(isset($_REQUEST['id']) And !is_numeric($_REQUEST['id'])){
	$_REQUEST['id']=0;
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">ID</span>'));
}
if(isset($_REQUEST['action']) And $_REQUEST['action']!=wp_strip_all_tags($_REQUEST['action'])){
	unset($_REQUEST['action']);
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Action</span>'));
	return;
}
if(isset($_REQUEST['categoria']) And !is_numeric($_REQUEST['categoria'])){
	$_REQUEST['categoria']=0;
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Categoria</span>'));
}
if(isset($_REQUEST['numero']) And $_REQUEST['numero']!="" AND !is_numeric($_REQUEST['numero'])){
	$_REQUEST['numero']="";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Numero</span>'));
}
if(isset($_REQUEST['anno']) And !is_numeric($_REQUEST['anno'])){
	$_REQUEST['anno']=0;
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Anno</span>'));
}
if(isset($_REQUEST['ente']) And !is_numeric($_REQUEST['ente'])){
	$_REQUEST['ente']="-1";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Ente</span>'));
}
if(isset($_REQUEST['Pag']) And !is_numeric($_REQUEST['Pag'])){
	$_REQUEST['Pag']=1;
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Pag</span>'));
}
if(isset($_REQUEST['oggetto']) And $_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
	$_REQUEST['oggetto']="";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Oggetto</span>'));
}
if(isset($_REQUEST['riferimento']) And $_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
	$_REQUEST['riferimento']="";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Riferimento</span>'));
}
if(isset($_REQUEST['DataInizio']) And $_REQUEST['DataInizio']!=wp_strip_all_tags($_REQUEST['DataInizio'])){
	$_REQUEST['DataInizio']="";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">Da Data</span>'));
}
if(isset($_REQUEST['DataFine']) And $_REQUEST['DataFine']!=wp_strip_all_tags($_REQUEST['DataFine'])){
	$_REQUEST['DataFine']="";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">A Data</span>'));
}
if(isset($_REQUEST['filtra']) And ($_REQUEST['filtra']!=__("Filtra","albo-pretorio-considera") And $_REQUEST['filtra']!=__("Annulla Filtro","albo-pretorio-considera"))){
	$_REQUEST['filtra']="Filtra";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">filtra</span>'));
}
if(isset($_REQUEST['vf']) And ($_REQUEST['vf']!="s" And $_REQUEST['vf']!="h" And $_REQUEST['vf']!="undefined")){
	$_REQUEST['vf']="undefined";
	echo "<br />".wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sATTENZIONE.%2\$s E' stato indicato un VALORE non valido per il parametro %3\$s","albo-pretorio-considera"),'<span style="color:red;">',"</span>",'<span style="color:red;">vf</span>'));
}
foreach($_REQUEST as $Key => $Val){
	$_REQUEST[$Key]=htmlspecialchars(wp_strip_all_tags($_REQUEST[$Key]));
}

include_once(dirname (__FILE__) .'/frontend_filtro_new.php');

if(isset($_REQUEST['action'])){
	switch ($_REQUEST['action']){
        case 'printatto':
            if (is_numeric($_REQUEST['id'])) {
                include_once(dirname (__FILE__) .'/stampe.php');
                $AttoStampa = ap_get_atto((int)$_REQUEST['id']);
                if (!empty($AttoStampa)) {
                    $AttoStampa = $AttoStampa[0];
                    $Oggi = ap_oggi();
                    if (($AttoStampa->DataInizio!="0000-00-00" And $AttoStampa->DataInizio>$Oggi) Or ($AttoStampa->DataOblio!="0000-00-00" And $AttoStampa->DataOblio<=$Oggi))
                        wp_die(__("Documento non disponibile","albo-pretorio-considera"),"",array('response'=>404));
                }
                if ($_REQUEST['pdf'] == 'c') {
                    StampaAtto($_REQUEST['id'], 'c');
                } elseif ($_REQUEST['pdf'] == 'a') {
                    StampaAtto($_REQUEST['id'], 'a');
                }
            }else{
				echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
			}
            break;
		case 'visatto':
			if(is_numeric($_REQUEST['id']))
				$ret=VisualizzaAtto($_REQUEST['id']);
			else{
				echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
			}
			break;
		case 'addstatall':
			if(is_numeric($_GET['id']) and is_numeric($_GET['idAtto']))
				ap_insert_log(5,5,(int)$_GET['id'],"Visualizzazione",(int)$_GET['idAtto']);
			break;
		default: 
			if (isset($_REQUEST['filtra'])){
				if(!is_numeric($_REQUEST['categoria']) OR
				   !is_numeric($_REQUEST['numero']) OR
				   !is_numeric($_REQUEST['anno']) OR
				   !is_numeric($_REQUEST['ente'])){
						echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
						break;
				}
			if(isset($_REQUEST['oggetto']) And $_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
				echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
				break;
			}
			if(isset($_REQUEST['riferimento']) And $_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
				echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
				break;
			}
	 		$ret=Lista_Atti($Parametri,
				 			isset($_REQUEST['categoria'])?(int)$_REQUEST['categoria']:0,
							isset($_REQUEST['numero'])?(int)$_REQUEST['numero']:0,
							isset($_REQUEST['anno'])?(int)$_REQUEST['anno']:0, 
							isset($_REQUEST['oggetto'])?esc_html($_REQUEST['oggetto']):"",
							isset($_REQUEST['DataInizio'])?esc_html($_REQUEST['DataInizio']):0,
							isset($_REQUEST['DataFine'])?esc_html($_REQUEST['DataFine']):0, 
							isset($_REQUEST['riferimento'])?esc_html($_REQUEST['riferimento']):"",
							isset($_REQUEST['ente'])?(int)$_REQUEST['ente']:-1);			
			}else if(isset($_REQUEST['annullafiltro'])){
					 unset($_REQUEST['categoria']);
					 unset($_REQUEST['numero']);
					 unset($_REQUEST['anno']);
					 unset($_REQUEST['oggetto']);
					 unset($_REQUEST['riferimento']);
					 unset($_REQUEST['DataInizio']);
					 unset($_REQUEST['DataFine']);
					 unset($_REQUEST['ente']);
					 $ret=Lista_Atti($Parametri);
				}else{
					$ret=Lista_Atti($Parametri);
				}
		}	
	}else{
		if (isset($_REQUEST['filtra'])){
			if((isset($_REQUEST['categoria']) And !is_numeric($_REQUEST['categoria'])) OR
			   (isset($_REQUEST['numero']) And $_REQUEST['numero']!="" AND !is_numeric($_REQUEST['numero'])) OR
			   (isset($_REQUEST['anno']) And !is_numeric($_REQUEST['anno'])) OR
			   (isset($_REQUEST['ente']) And !is_numeric($_REQUEST['ente']))){
					echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
					return;
			}
			if(isset($_REQUEST['oggetto']) And $_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
				echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
				return;
			}
			if(isset($_REQUEST['riferimento']) And $_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
				echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
				return;
			}
			$ret=Lista_Atti($Parametri,
							isset($_REQUEST['categoria'])?(int)$_REQUEST['categoria']:0,
							isset($_REQUEST['numero'])?(int)$_REQUEST['numero']:0,
							isset($_REQUEST['anno'])?(int)$_REQUEST['anno']:0, 
							isset($_REQUEST['oggetto'])?esc_html($_REQUEST['oggetto']):"",
							isset($_REQUEST['DataInizio'])?esc_html($_REQUEST['DataInizio']):0,
							isset($_REQUEST['DataFine'])?esc_html($_REQUEST['DataFine']):0, 
							isset($_REQUEST['riferimento'])?esc_html($_REQUEST['riferimento']):"",
							isset($_REQUEST['ente'])?(int)$_REQUEST['ente']:-1);
		}else 
			if(isset($_REQUEST['annullafiltro'])){
				 unset($_REQUEST['categoria']);
				 unset($_REQUEST['numero']);
				 unset($_REQUEST['anno']);
				 unset($_REQUEST['oggetto']);
				 unset($_REQUEST['riferimento']);
				 unset($_REQUEST['DataInizio']);
				 unset($_REQUEST['ente']);
				 $ret=Lista_Atti($Parametri);
			}else{
				$ret=Lista_Atti($Parametri);
			}
	}
	
function VisualizzaAtto($id){
	$risultato=ap_get_atto($id);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	ap_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$Unitao=ap_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
	if ($Unitao!==FALSE)
		$UnitaoNome=$Unitao->Nome;
	else
		$UnitaoNome="";
	$NomeResp=ap_get_responsabile($risultato->RespProc);
	if (count($NomeResp)>0) {
		$NomeResp=$NomeResp[0];
		$NomeResp=stripslashes($NomeResp->Nome." ".$NomeResp->Cognome);
	} else{
		$NomeResp="";
	}
echo '
<div>
	<button class="alboh" onclick="window.location.href=\''.esc_url(wp_get_referer()).'\'"><span class="dashicons dashicons-controls-back"></span>'.esc_html__("Torna alla Lista","albo-pretorio-considera").'</button>
	<h3 class="u-padding-top-s">'.esc_html__("Dati atto","albo-pretorio-considera").'</h3>
	<div class="Grid Grid--withGutter u-padding-all-l">';
	if($risultato->DataAnnullamento!='0000-00-00'){
		echo '<div class="Grid-cell u-padding-bottom-m" >
	<p style="text-align:center;font-size:1.5em;background-color: '.$coloreAnnullati.'">';
		echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __('Atto Annullato dal Responsabile del Procedimento %1$s Motivo: %2$s','albo-pretorio-considera'),'<br /><br />','<span style="font-size:1;font-style: italic;">'.stripslashes($risultato->MotivoAnnullamento).'</span>');
		echo '    </p>
	</div>';
	}
echo '
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Ente titolare dell'Atto","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.stripslashes(ap_get_ente($risultato->Ente)->Nome).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Numero Albo","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$risultato->Numero."/".$risultato->Anno.'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Codice di Riferimento","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($risultato->Riferimento)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Oggetto","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-margin-top-xs u-padding-left-m u-padding-right-m u-padding-top-xxs u-padding-bottom-s u-border-bottom-xxs">'.esc_html(stripslashes($risultato->Oggetto)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Data di registrazione","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->Data).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Data inizio Pubblicazione","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->DataInizio).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Data fine Pubblicazione","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->DataFine).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Data oblio","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.ap_VisualizzaData($risultato->DataOblio).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Richiedente","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.esc_html(stripslashes($risultato->Richiedente)).'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
		<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$UnitaoNome.'</div>
		</div>		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
		<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$NomeResp.'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Categoria","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.stripslashes($risultatocategoria->Nome).'</div>
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
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Meta Dati","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$Meta.'</div>
		</div>';
}
echo'		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
				<div class="u-background-50 u-color-white u-margin-bottom-xxs u-borderRadius-m u-padding-all-m">'.esc_html__("Note","albo-pretorio-considera").'</div>
			</div>
			<div class="Grid-cell u-size1of2 u-lg-size1of2">
				<div class="u-margin-bottom-xs u-margin-top-xs u-padding-left-m u-padding-right-m u-padding-top-xxs u-padding-bottom-s u-border-bottom-xxs">'.(strlen(stripslashes($risultato->Informazioni))>0?wp_kses_post(stripslashes($risultato->Informazioni)):"&nbsp;&nbsp;").'</div>
			</div>
		</div>
</div>';
$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
$Ruolo="";
if($Soggetti){
	$Soggetti=ap_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
	echo "		<h3 style=\"text-align:center;\">".__("Soggetti","albo-pretorio-considera")."</h3>";
}else{
	$Soggetti=array();
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
	<h\>'.ap_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione").'</h4>';
	}
	$Ruolo=$Soggetto->Funzione;
	echo'<div class="Grid Grid--withGutter u-padding-all-l">		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Persona","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$Soggetto->Cognome." ".$Soggetto->Nome.'</div>
		</div>';		
	if ($Soggetto->Email)
	echo'
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Email","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs"><a href="mailto:'.$Soggetto->Email.'">'.$Soggetto->Email.'</a></div>
		</div>';
	if ($Soggetto->Telefono)
	echo'
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Telefono","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$Soggetto->Telefono.'</div>
		</div>		
		<tr>';
	if ($Soggetto->Orario)
	echo'
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Orario ricevimento","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$Soggetto->Orario.'</div>
		</div>
		<tr>';
	if ($Soggetto->Note)
	echo'		
		<div class="Grid-cell u-size1of2 u-lg-size1of2 HeadAtto">
			<div class="u-background-50 u-color-white u-margin-bottom-xs u-borderRadius-m u-padding-all-m">'.esc_html__("Note","albo-pretorio-considera").'</div>
		</div>
		<div class="Grid-cell u-size1of2 u-lg-size1of2">
			<div class="u-margin-bottom-xs u-padding-all-m u-border-bottom-xxs">'.$Soggetto->Note.'</div>
		</div>';
	echo'</div>';
}
if($Ruolo!=""){
	echo '</div>';
}
$TipidiFiles=ap_get_tipidifiles();
if (strpos(get_permalink(),"?")>0)
	$sep="&amp;";
else
	$sep="?";
$documenti=ap_get_documenti_atto($id);
$StatoAllegati= get_option('opt_AP_Allegati');
if(count($documenti)>0){
	echo '
		<h3>'. __("Documenti firmati","albo-pretorio-considera").'</h3>
			<div class="Grid Grid--withGutter u-padding-all-l break-word">';
	//print_r($_SERVER);
	foreach ($documenti as $allegato) {
		$Estensione=ap_ExtensionType($allegato->Allegato);
		echo'
			<div class="Grid-cell HeadAllegati">
				<div class="u-margin-bottom-xs u-borderRadius-m u-padding-all-m u-border-all-xxs">
					<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"allegato/> '.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.esc_html(esc_html__("Descrizione","albo-pretorio-considera")).'</strong>: '.wp_strip_all_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato))).'</strong><br />';
			if(!is_file($allegato->Allegato) And $allegato->Note!=""){
				echo '<strong>'.esc_html(esc_html__("Documento rimosso","albo-pretorio-considera")).'</strong>: '.$allegato->Note.'<strong>';
			}else{
				echo '<strong>'.esc_html(esc_html__("Impronta","albo-pretorio-considera")).'</strong>: '.$allegato->Impronta.'<br /><strong>'.esc_html(esc_html__("Dimensione file","albo-pretorio-considera")).'</strong>: '.ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0)."<br />";
				if (is_file($allegato->Allegato)){
					if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
						echo '<a href="'.ap_DaPath_a_URL($allegato->Allegato).'" class="addstatdw noUnderLine" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" title="'.esc_html__("Visualizza Allegato","albo-pretorio-considera").'" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg></a> ';
					}
					if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
						echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.get_permalink().$sep.'action=dwnalle&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" class="noUnderLine" title="'.esc_html__("Scarica allegato","albo-pretorio-considera").'">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>';
						if($StatoAllegati=="dwn"){
							echo " <strong>".basename( $allegato->Allegato).'</strong></a>';
						}else{
							echo "</a>";
						}
					}
				}else{
					echo basename( $allegato->Allegato).' '.__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
				}
			}
		echo '</div>
			</div>
		';
		}
	echo '</div>';
}
$allegati=ap_get_allegati_atto($id);
if(count($allegati)>0){
	echo '
		<h3>'. __("Allegati","albo-pretorio-considera").'</h3>
			<div class="Grid Grid--withGutter u-padding-all-l break-word">';
	//print_r($_SERVER);
	foreach ($allegati as $allegato) {
		$Estensione=ap_ExtensionType($allegato->Allegato);
		echo'
			<div class="Grid-cell HeadAllegati">
				<div class="u-margin-bottom-xs u-borderRadius-m u-padding-all-m u-border-all-xxs">
					<img src="'.$TipidiFiles[strtolower($Estensione)]['Icona'].'" alt="'.$TipidiFiles[strtolower($Estensione)]['Descrizione'].'" height="30" width="30"allegato/> '.($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.esc_html(esc_html__("Descrizione","albo-pretorio-considera")).'</strong>: '.wp_strip_all_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato))).'</strong><br />';
			if(!is_file($allegato->Allegato) And $allegato->Note!=""){
				echo '<strong>'.esc_html(esc_html__("Allegato rimosso","albo-pretorio-considera")).'</strong>: '.$allegato->Note.'<br />';
			}else{
				echo '<strong>'.esc_html(esc_html__("Impronta","albo-pretorio-considera")).'</strong>: '.$allegato->Impronta.'<br /><strong>'.esc_html(esc_html__("Dimensione file","albo-pretorio-considera")).'</strong>: '.ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0)."<br />";
				if (is_file($allegato->Allegato)){
					if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
						echo '<a href="'.ap_DaPath_a_URL($allegato->Allegato).'" class="addstatdw noUnderLine" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" title="'.esc_html__("Visualizza Allegato","albo-pretorio-considera").'" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg> </a> ';
					}
					if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
						echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.get_permalink().$sep.'action=dwnalle&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" class="noUnderLine" title="'.esc_html__("Scarica allegato","albo-pretorio-considera").'">
						<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>';	
						if($StatoAllegati=="dwn"){
							echo " <strong>".basename( $allegato->Allegato).'</strong></a>';
						}else{
							echo "</a>";
						}
					}
				}else{
					echo basename( $allegato->Allegato).' '.__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
				}
			}
		echo '</div>
			</div>
		';
		}
	echo '</div>
<div class="Prose Alert Alert--info Alert--withIcon" role="alert">
    <h3 class="u-text-h3">'.esc_html__("Informazioni","albo-pretorio-considera").'</h3>
    <p class="u-text-p">'.esc_html__("L'impronta dei files è calcolata con algoritmo SHA256 al momento dell'upload","albo-pretorio-considera").'</p>
</div>';
}
}

function Lista_Atti($Parametri,$Categoria=0,$Numero=0,$Anno=0,$Oggetto='',$Dadata=0,$Adata=0,$Riferimento='',$Ente=-1){
		switch ($Parametri['stato']){
			case 0:
				$TitoloAtti=__("Tutti gli atti","albo-pretorio-considera");
				break;
			case 1:
				$TitoloAtti=__("Atti in corso di Validità","albo-pretorio-considera");
				break;
			case 2:
				$TitoloAtti=__("Atti Scaduti","albo-pretorio-considera");
				break;
			case 3:
				$TitoloAtti=__("Atti da Pubblicare","albo-pretorio-considera");
				break;
	}
	if (isset($Parametri['per_page'])){
		$N_A_pp=$Parametri['per_page'];	
	}else{
		$N_A_pp=10;
	}
	if (isset($Parametri['cat']) and $Parametri['cat']!=0){
		$DesCategorie="";
		$Categoria="";
		$Categorie=explode(",",$Parametri['cat']);
		foreach($Categorie as $Cate){
			$DesCat=ap_get_categoria($Cate);
			$DesCategorie.=$DesCat[0]->Nome.",";
			$Categoria.=$Cate.",";
		}
		$DesCategorie= substr($DesCategorie,0, strlen($DesCategorie)-1);
		$TitoloAtti.=" Categorie ".$DesCategorie;
		$Categoria=substr($Categoria,0, strlen($Categoria)-1);
		$cat=1;
	}else{
		$Categorie=$Categoria;
		$cat=0;
	}
	if (!isset($_REQUEST['Pag'])){
		$Da=0;
		$A=$N_A_pp;
	}else{
		if(is_numeric($_REQUEST['Pag'])){
			$Da=($_REQUEST['Pag']-1)*$N_A_pp;
			$A=$N_A_pp;
		}else{
			echo sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />");
		}
	}
	if (!isset($_REQUEST['ente'])){
	         $Ente = '-1';
	}else{
	        $Ente = $_REQUEST['ente'];
	}
	$TotAtti=ap_get_all_atti($Parametri['stato'],intval($Numero),intval($Anno),$Categorie,$Oggetto,intval($Dadata),intval($Adata),'',0,0,true,false,$Riferimento,$Ente);
	$lista=ap_get_all_atti($Parametri['stato'],intval($Numero),intval($Anno),$Categorie,$Oggetto,intval($Dadata),intval($Adata),'Anno DESC,Numero DESC',$Da,$A,false,false,$Riferimento,$Ente); 
	$titEnte=get_option('opt_AP_LivelloTitoloEnte');
	if ($titEnte=='')
		$titEnte="h2";
	$titPagina=get_option('opt_AP_LivelloTitoloPagina');
	if ($titPagina=='')
		$titPagina="h3";
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$VisFiltro="";
	if(isset($Parametri['minfiltri']) And $Parametri['minfiltri']=="si"){
		if(isset($_REQUEST['vf']) and  $_REQUEST['vf']=="s"){
//			$VisFiltro='<img src="'.Albo_URL.'img/minimize.png" id="maxminfiltro" class="s" alt="icona minimizza finestra filtri"/>';
			$VisFiltro='<button id="maxminfiltro" class="albos"><span class="dashicons dashicons-filter"></span> '.esc_html__("Chiudi Ricerca atti mediante filtri","albo-pretorio-considera").'</button>';
		}else{
//			$VisFiltro='<img src="'.Albo_URL.'img/maximize.png" id="maxminfiltro" class="h" alt="icona massimizza finestra filtri"/>';
			$VisFiltro='<button id="maxminfiltro" class="alboh"><span class="dashicons dashicons-filter"></span> '.esc_html__("Apri Ricerca atti mediante filtri","albo-pretorio-considera").'</button>';
		}
	}
echo ' <div class="Visalbo">
<a name="dati"></a> ';
if (get_option('opt_AP_VisualizzaEnte')=='Si')
		echo '<'.$titEnte.' ><span  class="titoloEnte">'.stripslashes(get_option('opt_AP_Ente')).'</span></'.$titEnte.'>';
echo'<'.$titPagina.'><span  class="titoloPagina">'.$TitoloAtti.'</span></'.$titPagina.'>';
if (!isset($Parametri['filtri']) Or $Parametri['filtri']=="si")
	echo'<h4 class="filtri">'.$VisFiltro.'</h4>'.VisualizzaRicerca($Parametri['stato'],$Categoria,$Parametri['minfiltri']);
//$Contenuto.=  $nascondi;
if ($TotAtti>$N_A_pp){
		$appo=$_REQUEST;
		unset($appo["Pag"]);
		unset($appo["vf"]);	
	    $Para=http_build_query($appo);
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&amp;Pag=";
		$Npag=(int)($TotAtti/$N_A_pp);
		if ($TotAtti%$N_A_pp>0){
			$Npag++;
		}
		echo '<div style="float:right;" id="risultati">
	<nav role="navigation" aria-label="Navigazione paginata" class="u-layout-prose">
		<ul class="Grid Grid--fit u-text-r-xxs">';
		if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1;
			$PagSuc=$Pagcur+1;
			echo'	
				<li class="Grid-cell u-textCenter">
					<a href="'.$Para.'1" class="u-color-50 u-textClean u-block" title="'.esc_html__("Vai alla Prima pagina","albo-pretorio-considera").'">
						<span class="u-text-r-m">&laquo;</span>
					</a>
				</li>
				<li class="Grid-cell u-textCenter u-block">
					<a href="'.$Para.$PagPre.'" class="u-color-50 u-textClean u-block" title="'.esc_html__("Vai alla Pagina precedente","albo-pretorio-considera").'">
						<span class="u-text-r-m">&lsaquo;</span>
					</a>
				</li>';			
		}else{
			$Pagcur=$PagPre=1;
			$PagSuc=$Pagcur+1;
			echo'	
				<li class="Grid-cell u-textCenter u-block">
					<span class="u-text-r-m u-color-5">&laquo;</span>
				</li>
				<li class="Grid-cell u-textCenter u-block">
					<span class="u-color-5 u-textClean u-block">&lsaquo;</span>
				</li>';	

		}
		$DisCP=$Pagcur.'/'.$Npag;
		$MR=strlen($DisCP)*0.5;
		echo'	<li class="Grid-cell u-textCenter u-block" aria-hidden="true" style="margin-right:'.$MR.'em;">
				<span class="u-block u-color-black">
					<span class="u-text-r-s">'.$DisCP.'</span>
				</span>
			</li>
			<li class="Grid-cell u-textCenter u-block">';
		if($Pagcur==$Npag){
			echo'					
				<span class="u-text-r-m u-color-5">&rsaquo;</span>	
			</li>
			<li class="Grid-cell u-textCenter u-block">
					<span class="u-text-r-m u-color-5">&raquo;</span>
				</a>
			</li>';
		}else{
			$PagSuc=$Pagcur+1;
			echo'				 
					<a href="'.$Para.$PagSuc.'" class="u-color-50 u-textClean u-block" title="'.esc_html__("Vai alla Prima successiva","albo-pretorio-considera").'">
					<span class="u-text-r-m">&rsaquo;</span>
				</a>
			</li>
			<li class="Grid-cell u-textCenter u-block">
				<a href="'.$Para.$Npag.'" class="u-color-50 u-textClean u-block" title="'.esc_html__("Vai all'Ultima pagina","albo-pretorio-considera").'">
					<span class="u-text-r-m">&raquo;</span>
				</a>
			</li>';
		}
		echo'			</ul>
	</nav>
</div>';
	}	
$FEColsOption=get_option('opt_AP_ColonneFE',array(
									"Ente"=>0,
									"Riferimento"=>0,
									"Oggetto"=>0,
									"Validita"=>0,
									"Categoria"=>0,
									"Note"=>0,
									"RespProc"=>0,
									"DataOblio"=>0));
if(!is_array($FEColsOption)){
$FEColsOption=json_decode($FEColsOption,TRUE);
}	
echo '	<div class="tabalbo">                               
		<table id="elenco-atti" class="Table Table--withBorder u-text-r-xs js-TableResponsive tablesaw tablesaw-stack" data-tablesaw-mode="stack">	    <caption class="u-hiddenVisually">'.esc_html__("Atti","albo-pretorio-considera").'</caption>
		<thead>
	    	<tr class="u-border-bottom-xs">
	        	<th scope="col">'.esc_html__("Numero Atto","albo-pretorio-considera").'</th>';
foreach($FEColsOption as $Opzione => $Valore){
		if($Valore==1){
			echo '			<th scope="col">'.($Opzione=="Validita"?esc_html__("Validità","albo-pretorio-considera"):esc_html($Opzione)).'</th>';
		}
}
echo '	</tr>
	    </thead>
	    <tbody>';
	    $CeAnnullato=false;
	if ($lista){
	 	$pari=true;
		if (strpos(get_permalink(),"?")>0)
			$sep="&amp;";
		else
			$sep="?";
		foreach($lista as $riga){
			$Link='<a href="'.get_permalink().$sep.'action=visatto&amp;id='.$riga->IdAtto.'"  style="text-decoration: underline;">';
			$categoria=ap_get_categoria($riga->IdCategoria);
			$cat=$categoria[0]->Nome;
			$NumeroAtto=$riga->Numero;
			$classe='';
			if ($pari) 
				$classe='class="u-background-white"';
			if (!$pari)
				$classe='class="u-background-grey-10"';
			$pari=!$pari;
			if($riga->DataAnnullamento!='0000-00-00'){
				$classe='style="background-color: '.$coloreAnnullati.';"';
				$CeAnnullato=true;
			}
			echo '<tr >
			        <td '.$classe.'>'.$Link.$NumeroAtto.'/'.$riga->Anno .'</a> 
					</td>';
			if (isset($FEColsOption['Data']) And $FEColsOption['Data']==1)
				echo'
					<td '.$classe.'>
						'.$Link.ap_VisualizzaData($riga->Data) .'</a>
					</td>';
			if (isset($FEColsOption['Ente']) And $FEColsOption['Ente']==1)
				echo'
					<td '.$classe.'>
						'.$Link.$Link.stripslashes(ap_get_ente($riga->Ente)->Nome) .'</a>
					</td>';
			if (isset($FEColsOption['Riferimento']) And $FEColsOption['Riferimento']==1)
				echo'
					<td '.$classe.'>
						'.$Link.stripslashes($riga->Riferimento) .'</a>
					</td>';
			if (isset($FEColsOption['Oggetto']) And $FEColsOption['Oggetto']==1)
				echo '			
					<td '.$classe.'>
						'.$Link.stripslashes($riga->Oggetto) .'</a>
					</td>';
			if (isset($FEColsOption['Validita']) And $FEColsOption['Validita']==1)
				echo '								
					<td '.$classe.'>
						'.$Link.ap_VisualizzaData($riga->DataInizio) .'<br />'.ap_VisualizzaData($riga->DataFine) .'</a>  
					</td>';
			if (isset($FEColsOption['Categoria']) And $FEColsOption['Categoria']==1)
				echo'								
					<td '.$classe.'>
						'.$Link.$cat .'</a>  
					</td>';
			if (isset($FEColsOption['Note']) And $FEColsOption['Note']==1)
				echo'
					<td '.$classe.'>
						'.$Link.stripslashes($riga->Informazioni) .'</a>
					</td>';
			if (isset($FEColsOption['DataOblio']) And $FEColsOption['DataOblio']==1)
				echo'
					<td '.$classe.'>
						'.$Link.ap_VisualizzaData($riga->DataOblio) .'</a>
					</td>';
		echo'	
				</tr>'; 
			}
	} else {
			echo '<tr>
					<td colspan="6">'.esc_html__("Nessun Atto Codificato","albo-pretorio-considera").'</td>
				  </tr>';
	}
	echo '
     </tbody>
    </table>';
echo '</div>';
	if ($CeAnnullato) 
		echo '<p>'. sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __('Le righe evidenziate con questo sfondo %s indicano Atti Annullati','albo-pretorio-considera'),' <span style="background-color: '.$coloreAnnullati.';">&nbsp;&nbsp;&nbsp;</span>').'</p>';
	echo '</div><!-- /wrap -->	';
	return ob_get_clean();
}
?>
