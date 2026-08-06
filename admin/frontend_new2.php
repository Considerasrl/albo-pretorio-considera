<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione FrontEnd.
 * @link       http://www.eduva.org
 * @since      4.7
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

include_once(dirname (__FILE__) .'/frontend_filtro_new2.php');

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
                        wp_die(esc_html__("Documento non disponibile","albo-pretorio-considera"),"",array('response'=>404));
                }
                if ($_REQUEST['pdf'] == 'c') {
                    StampaAtto($_REQUEST['id'], 'c');
                } elseif ($_REQUEST['pdf'] == 'a') {
                    StampaAtto($_REQUEST['id'], 'a');
                }
            }else{
				echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
			}
            break;
		case 'visatto':
			if(is_numeric($_REQUEST['id']))
				$ret=VisualizzaAtto($_REQUEST['id']);
			else{
				echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
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
						echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
						break;
				}
			if(isset($_REQUEST['oggetto']) And $_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
				echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
				break;
			}
			if(isset($_REQUEST['riferimento']) And $_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
				echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
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
					echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
					return;
			}
			if(isset($_REQUEST['oggetto']) And $_REQUEST['oggetto']!=wp_strip_all_tags($_REQUEST['oggetto'])){
				echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
				return;
			}
			if(isset($_REQUEST['riferimento']) And $_REQUEST['riferimento']!=wp_strip_all_tags($_REQUEST['riferimento'])){
				echo wp_kses_post(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("ATTENZIONE:%sE' stato indicato un parametro non valido che può rappresentare un ATTACCO INFORMATICO AL SITO","albo-pretorio-considera"),"<br />"));
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
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- template di rendering front-end [Albo]: markup fisso + label i18n del plugin, con i valori DB/utente escapati singolarmente (esc_html/esc_url/esc_attr) e input gia sanitizzato a monte; le variabili $Link/$classe contengono solo markup fisso + dati gia escapati.
function VisualizzaAtto($id){
	$risultato=ap_get_atto($id);
	$risultato=$risultato[0];
	$risultatocategoria=ap_get_categoria($risultato->IdCategoria);
	$risultatocategoria=$risultatocategoria[0];
	$Unitao=ap_get_unitaorganizzativa($risultato->IdUnitaOrganizzativa);
	$NomeResp=ap_get_responsabile($risultato->RespProc);
	if (count($NomeResp)>0)
		$NomeResp=$NomeResp[0];
	else
		$NomeResp="";
	$allegati=ap_get_all_allegati_atto($id);
	ap_insert_log(5,5,$id,"Visualizzazione");
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	if($risultato->DataAnnullamento!='0000-00-00')
		$Annullato='<p style="background-color: '.esc_attr($coloreAnnullati).';text-align:center;font-size:1.5em;">'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __('Atto Annullato dal Responsabile del Procedimento %1$s Motivo: %2$s','albo-pretorio-considera'),'<br /><br />','<span style="font-size:1;font-style: italic;">'.esc_html(stripslashes($risultato->MotivoAnnullamento)).'</span>');
	else{
		$Annullato='';
	}
?>
<section  id="DatiAtto">
	<div class="container clearfix mb-3 pb-3">
		<button class="btn btn-primary" onclick="window.location.href='<?php echo esc_url(wp_get_referer());?>'"><span class="fas fa-arrow-circle-left"></span> <?php esc_html_e("Torna alla Lista","albo-pretorio-considera");?></button>
		<h2 class="u-text-h2 pt-3 pl-2">Dati atto</h2>
		<?php echo ($Annullato?"<h3>".$Annullato."</h3>":"");?>
	   	<div class="row">
	   		<div class="col-12">
				<table class="table table-striped table-hover table-responsive-md">
				    <tbody id="dati-atto">
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Ente titolare dell'Atto","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo stripslashes(ap_get_ente($risultato->Ente)->Nome);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Numero Albo","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo $risultato->Numero."/".$risultato->Anno;?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Codice di Riferimento","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo esc_html(stripslashes($risultato->Riferimento));?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Oggetto","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo esc_html(stripslashes($risultato->Oggetto));?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Data di registrazione","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->Data);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Data inizio Pubblicazione","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->DataInizio);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Data fine Pubblicazione","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->DataFine)?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Data oblio","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo ap_VisualizzaData($risultato->DataOblio);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Richiedente","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo esc_html(stripslashes($risultato->Richiedente));?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Unità Organizzativa Responsabile","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo (isset($Unitao->Nome)?stripslashes($Unitao->Nome):"");?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Responsabile del procedimento amministrativo","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo (is_object($NomeResp)?$NomeResp->Nome." ".$NomeResp->Cognome:$NomeResp);?></td>
					</tr>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Categoria","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo stripslashes($risultatocategoria->Nome)?></td>
					</tr>
<?php
$MetaDati=ap_get_meta_atto($id);
if($MetaDati!==FALSE){
	$Meta="";
	foreach($MetaDati as $Metadato){
		$Meta.="{".$Metadato->Meta."=".$Metadato->Value."} - ";
	}
	$Meta=substr($Meta,0,-3);?>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Meta Dati","albo-pretorio-considera");?></th>
						<td style="vertical-align: middle;"><?php echo $Meta;?></td>
					</tr>
<?php }?>
					<tr>
						<th class="w-25 text-right"><?php esc_html_e("Note","albo-pretorio-considera");?></th>
						<td class="align-middle"><?php echo wp_kses_post(stripslashes($risultato->Informazioni));?></td>
					</tr>
		 	    </tbody>
			</table>
		</div>
<?php 		
$Soggetti=unserialize($risultato->Soggetti, array('allowed_classes'=>false));
if(count($Soggetti)>0){
	$Soggetti=ap_get_alcuni_soggetti_ruolo(implode(",",$Soggetti));
	$Ruolo="";
	if($Soggetti){
				echo "<div class=\"col-8 ml-5 pl-5\">
	<h3 class=\"u-text-h2 pt-3 pl-2\">". __("Soggetti","albo-pretorio-considera")."</h3>";
	}
	foreach($Soggetti as $Soggetto){
		if(ap_get_Funzione_Responsabile($Soggetto->Funzione,"Display")=="No"){
			continue;
		}
		if($Soggetto->Funzione!=$Ruolo){
				$Ruolo=$Soggetto->Funzione;?>
				<div class="callout mycallout">
	  				<div class="callout-title"><?php echo ap_get_Funzione_Responsabile($Soggetto->Funzione,"Descrizione"); ?></div>
	 				<div>
						<?php echo $Soggetto->Cognome." ".$Soggetto->Nome;?><br />
	<?php	} 
		if ($Soggetto->Email)
			echo __("Email","albo-pretorio-considera").' <a href="'.esc_url('mailto:'.$Soggetto->Email).'">'.esc_html($Soggetto->Email).'</a><br />';
		if ($Soggetto->Telefono)
			echo __("Telefono","albo-pretorio-considera")." ".$Soggetto->Telefono."<br />";
		if ($Soggetto->Orario)
			echo 	__("Orario ricevimento","albo-pretorio-considera")." ".$Soggetto->Orario.'<br />';
		if ($Soggetto->Note)
			echo __("Note","albo-pretorio-considera")." ".$Soggetto->Note;
	?>
					</div>	
			</div>
<?php	}?>
		</div>
<?php }?>
	</div>
<?php
$UrlSprite=get_option('opt_AP_UrlSprite');	   	
$TipidiFiles=ap_get_tipidifiles();
if (strpos(get_permalink(),"?")>0)
	$sep="&";
else
	$sep="?";
$documenti=ap_get_documenti_atto($id);
$StatoAllegati= get_option('opt_AP_Allegati');
if(count($documenti)>0){?>
	<div class="row">
	   	<div class="col">
			<h3 class="u-text-h2 pt-3 pb-2"><?php esc_html_e("Documenti firmati","albo-pretorio-considera");?></h3>
<?php
foreach ($documenti as $allegato) {
	$Estensione=ap_ExtensionType($allegato->Allegato);?>
			<div class="row border-dashed border-primary mb-1">
				<div class="col-1 icona-comunicazione">
<?php
	if(isset($allegato->TipoFile) and $allegato->TipoFile!="" and ap_isExtensioType($allegato->TipoFile)){
		$Estensione=ap_ExtensionType($allegato->TipoFile);
		echo '<img src="'.$TipidiFiles[$Estensione]['Icona'].'" alt="'.$TipidiFiles[$Estensione]['Descrizione'].'" height="30" width="30"/>';
	}else{
		echo '<img src="'.esc_url($TipidiFiles[strtolower($Estensione)]['Icona']).'" alt="'.esc_attr($TipidiFiles[strtolower($Estensione)]['Descrizione']).'" height="30" width="30"allegato/>';
	}?>
				</div>
				<div class="col-11 break-word">  				
				<?php 
				echo ($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.esc_html(esc_html__("Descrizione","albo-pretorio-considera")).'</strong>: '.esc_html(wp_strip_all_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato)))).'</strong><br />';
				if(!is_file($allegato->Allegato) And $allegato->Note!=""){
					echo '<strong>'.esc_html(esc_html__("Documento rimosso","albo-pretorio-considera")).'</strong>: '.esc_html($allegato->Note).'<br /><strong><br />';
				}else{
					echo '<strong>'.esc_html(esc_html__("Impronta","albo-pretorio-considera")).'</strong>: '.esc_html($allegato->Impronta).'<br /><strong>'.esc_html(esc_html__("Dimensione file","albo-pretorio-considera")).'</strong>: '.esc_html(ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0))."<br />";
					if (is_file($allegato->Allegato)){
						if($StatoAllegati=="all" Or $StatoAllegati=="vis"){
							echo '<a href="'.esc_url(ap_DaPath_a_URL($allegato->Allegato)).'" class="addstatdw noUnderLine" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" title="'.esc_html__("Visualizza Allegato","albo-pretorio-considera").'" target="_blank">
							<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg></a> ';
						}
						if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
							echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.get_permalink().$sep.'action=dwnalle&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" class="noUnderLine" title="'.esc_html__("Scarica allegato","albo-pretorio-considera").'">
							<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>';
								if($StatoAllegati=="dwn"){
									echo " <strong>".esc_html(basename( $allegato->Allegato)).'</strong></a>';
								}else{
									echo "</a>";
								}		
						}
					}else{
						echo esc_html(basename( $allegato->Allegato)).' '.esc_html__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
					}
				}?>
				</div>
			</div>
<?php	}?>
		</div>
	</div>
<?php	}
$allegati=ap_get_allegati_atto($id);
if(count($allegati)>0) { ?>
	<div class="row">
	   	<div class="col">
			<h3 class="u-text-h2 pt-3 pb-2"><?php esc_html_e("Allegati","albo-pretorio-considera");?></h3>
<?php
foreach ($allegati as $allegato) {
	$Estensione=ap_ExtensionType($allegato->Allegato);?>
			<div class="row border-dashed border-primary mb-1">
				<div class="col-1 icona-comunicazione">
<?php
	if(isset($allegato->TipoFile) and $allegato->TipoFile!="" and ap_isExtensioType($allegato->TipoFile)){
		$Estensione=ap_ExtensionType($allegato->TipoFile);
		echo '<img src="'.$TipidiFiles[$Estensione]['Icona'].'" alt="'.$TipidiFiles[$Estensione]['Descrizione'].'" height="30" width="30"/>';
	}else{
		echo '<img src="'.esc_url($TipidiFiles[strtolower($Estensione)]['Icona']).'" alt="'.esc_attr($TipidiFiles[strtolower($Estensione)]['Descrizione']).'" height="30" width="30"allegato/>';
	}?>
				</div>
				<div class="col-11 break-word">  				
				<?php 
				echo ($allegato->DocIntegrale!="1"?'<span class="evidenziato">'.__("Pubblicato per Estratto","albo-pretorio-considera")."</span><br />":"").'<strong>'.esc_html(esc_html__("Descrizione","albo-pretorio-considera")).'</strong>: '.esc_html(wp_strip_all_tags(($allegato->TitoloAllegato?$allegato->TitoloAllegato:basename( $allegato->Allegato)))).'</strong><br />';
				if(!is_file($allegato->Allegato) And $allegato->Note!=""){
					echo '<strong>'.esc_html(esc_html__("Allegato rimosso","albo-pretorio-considera")).'</strong>: '.esc_html($allegato->Note).'<br /><strong>';
				}else{
					echo '<strong>'.esc_html(esc_html__("Impronta","albo-pretorio-considera")).'</strong>: '.esc_html($allegato->Impronta).'<br /><strong>'.esc_html(esc_html__("Dimensione file","albo-pretorio-considera")).'</strong>: '.esc_html(ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0))."<br />";
					if (is_file($allegato->Allegato)){
						if($StatoAllegati=="all" Or $StatoAllegati=="vis"){ 
							echo '<a href="'.esc_url(ap_DaPath_a_URL($allegato->Allegato)).'" class="addstatdw noUnderLine" rel="'.get_permalink().$sep.'action=addstatall&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" title="'.esc_html__("Visualizza Allegato","albo-pretorio-considera").'" target="_blank">
							<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg></a> ';
						}
						if($StatoAllegati=="all" Or $StatoAllegati=="dwn"){
							echo htmlspecialchars_decode($TipidiFiles[strtolower($Estensione)]['Verifica']).' <a href="'.get_permalink().$sep.'action=dwnalle&amp;id='.esc_attr($allegato->IdAllegato).'&amp;idAtto='.$id.'" class="noUnderLine" title="'.esc_html__("Scarica allegato","albo-pretorio-considera").'">
							<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>';
								if($StatoAllegati=="dwn"){
									echo " <strong>".esc_html(basename( $allegato->Allegato)).'</strong> ('.esc_html(ap_Formato_Dimensione_File(is_file($allegato->Allegato)?filesize($allegato->Allegato):0)).')</a>';
								}else{
									echo "</a>";
								}		
						}
					}else{
						echo esc_html(basename( $allegato->Allegato)).' '.esc_html__("File non trovato, il file è stato cancellato o spostato!","albo-pretorio-considera");
					}
				}?>
				</div>
			</div>
<?php	}?>
		</div>
	</div>
<?php	}?>
	</div>	
	<div class="alert alert-info" role="alert">
	    <h3 class="u-text-h3"><?php esc_html_e("Informazioni","albo-pretorio-considera");?></h3>
	    <p class="u-text-p"><?php esc_html_e("L'impronta dei files è calcolata con algoritmo SHA256 al momento dell'upload","albo-pretorio-considera");?></p>
	</div>
</section>
<?php
}

function Lista_Atti($Parametri,$Categoria=0,$Numero=0,$Anno=0,$Oggetto='',$Dadata=0,$Adata=0,$Riferimento='',$Ente=-1){
	ob_start();
	switch ($Parametri['stato']){
			case 0:
				$TitoloAtti=__("Tutti gli AttiTutti gli Atti", 'albo-pretorio-considera');
				break;
			case 1:
				$TitoloAtti=__("Atti in corso di Validit&agrave;", 'albo-pretorio-considera');
				break;
			case 2:
				$TitoloAtti=__("Atti scaduti", 'albo-pretorio-considera');
				break;
			case 3:
				$TitoloAtti=__("Atti da Pubblicare", 'albo-pretorio-considera');
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
		$Da=($_REQUEST['Pag']-1)*$N_A_pp;
		$A=$N_A_pp;
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
	$titFiltri= get_option('opt_AP_LivelloTitoloFiltri');
	if ($titFiltri=='')
		$titFiltri="h4";
	$coloreAnnullati=get_option('opt_AP_ColoreAnnullati');
	$colorePari=get_option('opt_AP_ColorePari');
	$coloreDispari=get_option('opt_AP_ColoreDispari');
	if (!isset($Parametri['filtri']) Or $Parametri['filtri']=="si"){
		if(isset($Parametri['minfiltri']) And $Parametri['minfiltri']=="si"){?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					$("#headingFP").on('click',function(){
						if($("#Parametri").hasClass("show")==true)
							$("#Parametri").removeClass("show");
						else
							$("#Parametri").addClass("show");
						$("#Categorie").removeClass("show");
					});
					$("#headingC").on('click',function(){
						if($("#Categorie").hasClass("show")==true)
							$("#Categorie").removeClass("show");
						else
							$("#Categorie").addClass("show");
						$("#Parametri").removeClass("show");
					});
				});
			</script>
	<section  id="FiltroAtti">
		<div class="container shadow clearfix mb-3 pb-3">
			<<?php echo $titFiltri;?> class="u-text-h2 pt-3 pl-2"><?php esc_html_e("Filtri", 'albo-pretorio-considera');?></<?php echo $titFiltri;?>>
			<div class="row">
				<div class="col-12 col-lg-6">
					<div id="FiltriParametri" class="ml-lg-2" role="tablist">
						<div>
							<button id="headingFP" aria-expanded="false" aria-controls="Parametri" class="btn btn-primary w-100"><?php esc_html_e("Parametri", 'albo-pretorio-considera');?></button>
						</div>
						<div id="Parametri" class="collapse" role="tabpanel" aria-labelledby="headingFP" data-parent="#FiltriParametri">
							<div class="collapse-body border border-primary rounded-bottom pt-5">
								<?php echo get_FiltriParametri($Parametri['stato']);?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6">
					<div id="FiltriCategorie" class="mr-lg-2" role="tablist">
						<div>
							<button  id="headingC" aria-expanded="false" aria-controls="Categorie" class="btn btn-primary w-100"><?php esc_html_e("Categorie", 'albo-pretorio-considera');?></button>    	
						</div>
						<div id="Categorie" class="collapse" role="tabpanel" aria-labelledby="headingC" data-parent="#FiltriCategorie">
							<div class="collapse-body border border-primary rounded-bottom">
								<?php echo get_FiltriCategorie($Parametri['stato']);?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php	}else{?>
	<section  id="FiltroAtti">
		<div class="container shadow clearfix mb-3 pb-3">
			<<?php echo $titFiltri;?> class="u-text-h2 pt-3 pl-2"><?php esc_html_e("Filtri", 'albo-pretorio-considera');?></<?php echo $titFiltri;?>>
			<div class="row">
				<div class="col-12 col-lg-6">
				<div>
						<button aria-controls="Parametri" class="btn btn-primary w-100"><?php esc_html_e("Parametri", 'albo-pretorio-considera');?></button>
					</div>
					<div class="collapse-body border border-primary rounded-bottom pt-5">
						<?php echo get_FiltriParametri($Parametri['stato']);?>
					</div>
				</div>
				<div class="col-12 col-lg-6">
				<div>
						<button aria-controls="Categorie" class="btn btn-primary w-100"><?php esc_html_e("Categorie", 'albo-pretorio-considera');?></button>    	
					</div>
					<div class="collapse-body border border-primary rounded-bottom">
						<?php echo get_FiltriCategorie($Parametri['stato']);?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php				  
	}
		}
echo ' <div class="Visalbo">
<a name="dati"></a> ';
if (get_option('opt_AP_VisualizzaEnte')=='Si')
		echo '<'.$titEnte.' ><span class="titoloEnte">'.stripslashes(get_option('opt_AP_Ente')).'</span></'.$titEnte.'>';
echo '<'.$titPagina.'>'.$TitoloAtti.'</'.$titPagina.'>';
$Nav="";
$UrlSprite=get_option('opt_AP_UrlSprite');	   	
if ($TotAtti>$N_A_pp){
	    $Para='';
	    foreach ($_REQUEST as $k => $v){
			if ($k!="Pag" and $k!="vf")
				if ($Para=='')
					$Para.=$k.'='.$v;
				else
					$Para.='&amp;'.$k.'='.$v;
		}
		if ($Para=='')
			$Para="?Pag=";
		else
			$Para="?".$Para."&amp;Pag=";
		$Npag=(int)($TotAtti/$N_A_pp);
		if ($TotAtti%$N_A_pp>0){
			$Npag++;
		}
		$Nav.= '<div> 
    		<strong>'.__("N. Atti", 'albo-pretorio-considera').' '.$TotAtti.'</strong>
    		<nav class="pagination-wrapper" aria-label="Navigazione Pagine Albo">
  				<ul class="pagination">';
     	if (isset($_REQUEST['Pag']) And $_REQUEST['Pag']>1 ){
 			$Pagcur=$_REQUEST['Pag'];
			$PagPre=$Pagcur-1; 
				$Nav.= '<li class="page-item">
      <a class="page-link" href="'.$Para.$PagPre.'">
        <svg class="icon icon-primary"><use xlink:href="'.$UrlSprite.'#it-chevron-left"></use></svg>
        <span class="sr-only">Pagina precedente</span>
      </a>
    </li>';
		}else{
			$Pagcur=1;
		}
		if($Pagcur<3){
			$MInf=1;
			$MSup=($Npag<5?$Npag:5);
		}else{
			$MInf=$Pagcur-2;
			$MSup=($Pagcur+2>$Npag?$Npag:$Pagcur+2);		
		}
		for($i=$MInf;$i<$MSup+1;$i++){
			if($i==$Pagcur){
				$Nav.= '<li class="page-item"><a class="page-link" href="#" aria-current="page">
        <span class="d-inline-block d-sm-none">Pagina </span>'.$i.'</a>
        </li>';
			}else{
				$Nav.= '<li class="page-item"><a class="page-link" href="'.$Para.$i.'">'.$i.'</a></li>';
			}
		}
   		if ((isset($_REQUEST['Pag']) And $_REQUEST['Pag']<$Npag) Or (isset($_REQUEST['Pag']) And $Npag>1 And $_REQUEST['Pag']<$Npag)){
   			$PagSuc=($Pagcur==$Npag?$Npag:$Pagcur+1);
 			$Nav.= '    <li class="page-item">
      <a class="page-link" href="'.$Para.$PagSuc.'">
        <span class="sr-only">Pagina successiva</span>
        <svg class="icon icon-primary"><use xlink:href="'.$UrlSprite.'#it-chevron-right"></use></svg>
      </a>
    </li>';
		}
		$Nav.= '</ul>
		</nav>
		</div>';
	}	
echo $Nav;
$FEColsOption=get_option('opt_AP_ColonneFE',array(
									"Data"=>0,
									"Ente"=>0,
									"Riferimento"=>0,
									"Oggetto"=>0,
									"Validita"=>0,
									"Categoria"=>0,
									"Note"=>0,
									"RespProc"=>0,
									"DataOblio"=>0));
if(!is_array($FEColsOption)){
	$FEColsOption=shortcode_atts(array(
				"Data"=>0,
				"Ente"=>0,
				"Riferimento"=>0,
				"Oggetto"=>0,
				"Validita"=>0,
				"Categoria"=>0,
				"Note"=>0,
				"RespProc"=>0,
				"DataOblio"=>0), json_decode($FEColsOption,TRUE),"");
}	
echo '	<div class="tabalbo">                               
		<table class="table table-striped table-hover table-responsive-sm"> 
		<thead>
	    	<tr>
	        	<th scope="col">'.__("Prog.", 'albo-pretorio-considera').'</th>';
foreach($FEColsOption as $Opzione => $Valore){
		if($Valore==1){
			echo '			<th scope="col">'.$Opzione.'</th>';
		}
}
echo '	</tr>
	    </thead>
	    <tbody>';
	    $CeAnnullato=false;
	if ($lista){
	 	$pari=true;
		if (strpos(get_permalink(),"?")>0)
			$sep="&";
		else
			$sep="?";
		foreach($lista as $riga){
			$Link='<a href="'.esc_url(get_permalink().$sep.'action=visatto&id='.$riga->IdAtto).'"  style="text-decoration: underline;">';
			$categoria=ap_get_categoria($riga->IdCategoria);
			$cat=$categoria[0]->Nome;
			$NumeroAtto=ap_get_num_anno($riga->IdAtto);
	//		Bonifica_Url();
			$ParCella='';
			if($riga->DataAnnullamento!='0000-00-00'){
				$ParCella='style="background-color: '.$coloreAnnullati.';" title="'.__("Atto Annullato. Motivo Annullamento", 'albo-pretorio-considera').': '.$riga->MotivoAnnullamento.'"';
				$CeAnnullato=true;
			}
			echo '<tr >
			        <td '.$ParCella.'>'.$Link.esc_html($NumeroAtto).'/'.esc_html($riga->Anno) .'</a> 
					</td>';
			if ($FEColsOption['Data']==1)
				echo '
					<td '.$ParCella.'>
						'.ap_VisualizzaData($riga->Data) .'
					</td>';
			if ($FEColsOption['Ente']==1)
				echo '
					<td '.$ParCella.'>
						'.stripslashes(ap_get_ente($riga->Ente)->Nome) .'
					</td>';
			if ($FEColsOption['Riferimento']==1)
				echo '
					<td '.$ParCella.'>
						'.stripslashes($riga->Riferimento) .'
					</td>';
			if ($FEColsOption['Oggetto']==1)
				echo '			
					<td '.$ParCella.'>
						'.stripslashes($riga->Oggetto) .'
					</td>';
			if ($FEColsOption['Validita']==1)
				echo '								
					<td '.$ParCella.'>
						'.ap_VisualizzaData($riga->DataInizio) .'<br />'.ap_VisualizzaData($riga->DataFine) .'  
					</td>';
			if ($FEColsOption['Categoria']==1)
				echo '								
					<td '.$ParCella.'>
						'.$cat .'
					</td>';
			if ($FEColsOption['Note']==1)
				echo '
					<td '.$ParCella.'>
						'.stripslashes($riga->Informazioni) .'
					</td>';
			if ($FEColsOption['RespProc']==1){
				$responsabileprocedura=ap_get_responsabile($riga->RespProc);
				if(count($responsabileprocedura)>0){
					$respproc=$responsabileprocedura[0]->Cognome." ".$responsabileprocedura[0]->Nome;
					echo '
					<td '.$ParCella.'>
						'.$respproc .'
					</td>';				
				}
			}

			if ($FEColsOption['DataOblio']==1)
				echo '
					<td '.$ParCella.'>
						'.ap_VisualizzaData($riga->DataOblio) .'
					</td>';
		echo '	
				</tr>'; 
			}
	} else {
			echo '<tr>
					<td colspan="6">'.__("Nessun Atto Codificato", 'albo-pretorio-considera').'</td>
				  </tr>';
	}
	echo '
     </tbody>
    </table>';
echo '</div>';
	if ($CeAnnullato) 
		echo '<p>'.__("Le righe evidenziate con questo sfondo", 'albo-pretorio-considera').' <span style="background-color: '.$coloreAnnullati.';">&nbsp;&nbsp;&nbsp;</span> '.__("indicano Atti Annullati", 'albo-pretorio-considera').'</p>';
echo '</div><!-- /wrap -->	';
echo $Nav;
return ob_get_clean();
}
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
?>
