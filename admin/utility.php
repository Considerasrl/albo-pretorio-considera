<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- pagina admin di visualizzazione/redisplay: le letture di superglobali servono al rendering del form; le mutazioni avvengono negli handler di admin.php, protetti da wp_verify_nonce.
/**
 * Utility dell'albo.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- il plugin opera su tabelle custom proprie: nessuna API core equivalente, il caching non si applica alle query amministrative e di scrittura.
// phpcs:disable WordPress.DB.PreparedSQL, PluginCheck.Security.DirectDB.UnescapedDBParameter -- "Describe $Tabella" usa un identificatore di tabella interno (non input utente); prepare() non supporta gli identificatori.
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- pagina admin di utility/configurazione: l'output e' markup fisso + label i18n del plugin (__() su stringhe letterali del plugin) + output di helper che generano markup di stato; i valori dinamici da DB/utente (Oggetto, path allegati, impronta, nome tabella, $Stato, conteggi, date) sono escapati singolarmente inline (esc_html/wp_kses_post).
if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) {
	die('You are not allowed to call this page directly.');
}

$Stato="";
if (isset($_REQUEST['message']))
	if($_REQUEST['message']==80)
		$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
if (isset($_REQUEST['action'])){
	switch($_REQUEST['action']){
		case "ImpostaEnteND":
			if (!isset($_REQUEST['ImpostaEnteND'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['ImpostaEnteND'] ?? '')),'securimpostaentend')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 		
			if(!isset($_REQUEST["Ente"]) Or (isset($_REQUEST["Ente"])?intval($_REQUEST["Ente"]):0)==-1){
				$Stato=__("ATTENZIONE. Devi impostare un Ente valido","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			ap_set_ente_orfani(sanitize_text_field(wp_unslash($_REQUEST["Ente"] ?? '')));
			$NewEnte=ap_get_ente(sanitize_text_field(wp_unslash($_REQUEST["Ente"] ?? '')));
			$Stato=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Tutti gli atti con %1\$s Ente non definito sono stati assegnati all'ente %2\$s %3\$s %4\$s ","albo-pretorio-considera"),'<span style="color:red;">',"</span>","<strong>",$NewEnte->Nome,"</strong>");
			menu($Stato);
			break;	
		case "Crearobots":
			if (!isset($_REQUEST['creasic'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['creasic'] ?? '')),'creasicurezza')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 			
			ap_crearobots();
			menu();
			break;
		case "rip":
			if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'] ?? '')), 'ripubblica_atti' ) ) {
				menu(__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			}
			$AttiDaAggiornare=unserialize(wp_unslash($_GET['AttiDaAgg'] ?? ''), array('allowed_classes'=>false));
			if(!is_array($AttiDaAggiornare)){
				menu(__("ATTENZIONE. Parametro non valido, l'operazione è stata annullata","albo-pretorio-considera"));
				break;
			}
//			echo "<pre>";var_dump($AttiDaAggiornare);echo "</pre>";
			$AttiAggiornati=ap_ripubblica_atti_correnti($AttiDaAggiornare);
			if(count($AttiDaAggiornare)==$AttiAggiornati)
				$Stato="Tutti gli Atti(".$AttiAggiornati.") sono stati agiornati";
			else
				$Stato="Purtroppo si sono verficati degli errori negli aggiornamenti degli Atti. Atti da aggiornare:".count($AttiDaAggiornare)." Aggiornati:".$AttiAggiornati;
			menu($Stato);
			break;
		case "menu":
			menu(str_replace("%%br%%","<br />",sanitize_text_field(wp_unslash($_GET['stato'] ?? ''))));
			unset($_GET['action']);
			break;
		case "creafsic":
			menu(ap_NoIndexNoDirectLink(AP_BASE_DIR.get_option('opt_AP_FolderUpload')));
			unset($_POST['action']);
			break;
		case "posttrasf":
			if (!isset($_REQUEST['posttrasf'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['posttrasf'] ?? '')),'posttrasferimento')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 			
			$Msg=ap_NoIndexNoDirectLink(AP_BASE_DIR.get_option('opt_AP_FolderUpload'))."<br />";
			$Msg.=ap_allinea_allegati();
			unset($_POST['action']);
			menu($Msg);
			break;
		case "BackupData":
			if (!isset($_REQUEST['bckdata'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['bckdata'] ?? '')),'BackupDatiAlbo')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 			
			$Data=gmdate('Ymd_H_i_s');
			$nf=ap_BackupDatiFiles($Data,"",AlboBCK,TRUE);
/*			$filename=WP_CONTENT_DIR."/AlboOnLine/BackupDatiAlbo/tmp/msg.txt";
			$fpmsg = @fopen($filename, "rb");
				$Stato=fread($fpmsg,filesize($filename));
			fclose($fpmsg);
*/			$Stato=__("Backup Completato e pronto per il download","albo-pretorio-considera");
			menu($Stato);
			unset($_POST['action']);
			break;
		case "setData":
			if (!isset($_REQUEST['ripub'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['ripub'] ?? '')),'ripubblicaatti')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 	
			if (!$_REQUEST['DataInterruzione']){
				$Stato="Bisogna impostare la Data inizio di interruzione del serzio";
				menu($Stato);
			}elseif ($_REQUEST['ggInterruzione']<1){
				$Stato="Bisogna impostare il numero di giorni di interruzione del serzio";
				menu($Stato);
			}else
				menu("","1",$_REQUEST['DataInterruzione'],$_REQUEST['ggInterruzione']);
			break;
		case "verificaproc":
			if (!isset($_REQUEST['verproc'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['verproc'] ?? '')),'verificaprocedura')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 			
			TestProcedura();
			break;
		case "agghash":
			if (!isset($_REQUEST['verproc'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['verproc'] ?? '')),'verificaprocedura')) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			AggiornaHashAllegati();
			break;	
		case "creaninf":
			if (!isset($_REQUEST['rigenera'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['rigenera'] ?? '')),'rigenerasic')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 		
			ImplementaNINF();
			break;
		case "DelIPLog":
			if (!isset($_REQUEST['securdeliplog'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['securdeliplog'] ?? '')),'svuotavaloriipnelfiledilog')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			$Ris=ap_del_ip_log();
			if(is_numeric($Ris)){
				menu(sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("N° %s IP nel file di log CANCELLATI","albo-pretorio-considera"),$Ris));		
			}else{
				menu(__("Non è sono stati cancellati gli indirizzi IP nel file di log per il seguente errore: ","albo-pretorio-considera").$Ris);
			}
			break;
		case "creaTabella":
			creaTabella(sanitize_text_field(wp_unslash($_REQUEST['Tabella'] ?? '')));
			TestProcedura();
			break;
		case "creacategorie":
			CreaCategorie();
			break;
		case "svuotalog":
			if (!isset($_REQUEST['svuotalog'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['svuotalog'] ?? '')),'svuotafilelog')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 		
			$Msg=SvuotaLog(0);
			menu($Msg);
			break;
		case "puliscilog":
			if (!isset($_REQUEST['puliscilog'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['puliscilog'] ?? '')),'puliscifilelog')){
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			} 		
			$Msg=SvuotaLog(11);
			menu($Msg);
			break;
		case "ArchivioAnnoMese":
		if (!isset($_REQUEST['securarchivioannomese'])) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['securarchivioannomese'] ?? '')),'archivioannomese')) {
				$Stato=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
				menu($Stato);
				break;
			}
			$Msg=ArchivioAllegati();
			break;
		default:
			menu($Stato);
	}	
}else{
		menu($Stato);
}

function ArchivioAllegati(){
	if (isset($_POST["esBackup"]) And sanitize_text_field(wp_unslash($_POST["esBackup"] ?? '')) == "Si") {
		echo "<h3>".__("Creazione Backup Albo OnLine","albo-pretorio-considera")."</h3>";
		ap_BackupDatiFiles("Organizza_Archivio_Allegati_Mese_Anno",__("Modifica sistema archiviazione Allegati","albo-pretorio-considera"),AlboBCK,TRUE);
		echo "<h3>".__("Fine creazione Backup Albo OnLine","albo-pretorio-considera")."</h3>
		<p>".__("Il backup si trova nella cartella","albo-pretorio-considera").": <strong>".AlboBCK."</strong></p>";
	}
	echo "<h3>".__("Spostamento Allegati Albo OnLine","albo-pretorio-considera")."</h3>";
	ap_Move_Allegati_CartellaMeseAnno();
	echo "<h3>".__("Operazione eseguita","albo-pretorio-considera")."</h3>";
	update_option('opt_AP_FolderUploadMeseAnno',"Si" );
}
function CreaCategorie(){
echo '<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-admin-generic" style="font-size:1em;" style="font-size:1em;"></span> '.__("Creazione Categorie","albo-pretorio-considera").'
	</div>
		<div class="widefat">
			<table style="width:99%;">
				<thead>
					<tr>
						<th style="text-align:left;width:380px;">'.__("Categorie","albo-pretorio-considera").'</th>
						<th style="text-align:left;width:100px;">'.__("Stato","albo-pretorio-considera").'</th>
					</tr>
					</thead>
					<tbody>';
echo AP_CreaCategorieBase().'
					</tbody>
				</thead>
			</table>
		</div>';
}
function SvuotaLog($Tipo){
	$NumRow=ap_svuota_log($Tipo);
	if ($NumRow==0)
		return (__("Non sono state cancellate righe dal file di Log","albo-pretorio-considera"));
	else
		return(__("Log cancellato correttamente, sono state cancellate","albo-pretorio-considera")." ".$NumRow." righe");	
}
function ImplementaNINF(){
	$newPathAllegati=AP_BASE_DIR."AllegatiAttiAlboPretorio";
	ap_NoIndexNoDirectLink($newPathAllegati);
	echo'<div id="message" class="updated"> 
				<p><strong>'.__("File .htaccess e index.php necessari per il diritto all'oblio sono stati ricreati.","albo-pretorio-considera").'</strong></p>
				<p>'.__("Operazione terminata","albo-pretorio-considera").'&nbsp;&nbsp;
				<a href="'.site_url().'/wp-admin/admin.php?page=Albo_Pretorio" class="add-new-h2 tornaindietro">'.__("Torna indietro","albo-pretorio-considera").'</a>
				</p>
				</div>';
}

function menu($Stato="",$passo=0,$Data="",$GG=0){
global $wpdb;
$Stato=sanitize_text_field($Stato);
$passo=intval($passo);
$upload_dir = wp_upload_dir();
$basedir=substr( $upload_dir['basedir'],0,strlen($upload_dir['basedir'])-19);
if (null===get_option( 'opt_AP_FolderUploadMeseAnno' ) Or get_option('opt_AP_FolderUploadMeseAnno')!="Si") {
	$dirUploadMA=TRUE;
} else {
	$dirUploadMA=FALSE;
}
echo '<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-admin-generic" style="font-size:1em;"></span> '.__("Utility","albo-pretorio-considera").'</h2>
	</div>';
if ($Stato!="") 
	echo '<div id="message" class="updated"><p>'.wp_kses_post(str_replace("%%br%%","<br />",$Stato)).'</p></div>
      <meta http-equiv="refresh" content="2;url=admin.php?page=utilityAlboP"/>';
echo '
<div id="utility-tabs-container"  style="margin-top:20px;">
					<ul>
						<li><a href="#utility-tab-1">'.__("Proroga scadenza Atti","albo-pretorio-considera").'</a></li>
						<li><a href="#utility-tab-2">'.__("Verifica procedura","albo-pretorio-considera").'</a></li>
						<li><a href="#utility-tab-3">'.__("Diritto all'Oblio","albo-pretorio-considera").'</a></li>
						<li><a href="#utility-tab-4">'.__("Pulizia file di Log","albo-pretorio-considera").'</a></li>
						<li><a href="#utility-tab-5">'.__("Backup Albo OnLine","albo-pretorio-considera").'</a></li>
						<li><a href="#utility-tab-6">'.__("Repertorio","albo-pretorio-considera").'</a></li>
						<li><a href="#utility-tab-7">GDPR</a></li>
						<li><a href="#utility-tab-8">'.__("Utility Dati","albo-pretorio-considera").'</a></li>';
						if ($dirUploadMA)
							echo '<li><a href="#utility-tab-9">'.__("Archiviazione Allegati","albo-pretorio-considera").'</a></li>';
echo '					<li><a href="#utility-tab-10">'.__("Aggiornamento Impronta","albo-pretorio-considera").'</a></li>
						</ul>
		<div id="utility-tab-1" style="margin-bottom:20px;">
				<h3 style="text-align:center;">'.__("Attenzione","albo-pretorio-considera").'!!!!!<br />
				'.__("Operazione di proroga della scadenza degli atti in corso di validità a causa di interruzione del servizio di pubblicazione","albo-pretorio-considera").'</h3>
				<p>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Questa operazione PROROGA gli atti in corso di validità con motivazione %1\$s Proroga validità Atti per interruzione del sevizio di pubblicazione %2\$sLa nuova scadenza degli atti viene proroga di un numero di giorni uguale a quello dell'interruzione. %3\$sEstratto dalle Linee Guida di Agid punto 7:%4\$sLa pubblicazione si intende soddisfatta se un documento è rimasto disponibile sul sito complessivamente per almeno dodici ore per ciascun giorno di pubblicazione.%5\$sIl periodo di pubblicazione è prorogato di un giorno per ciascun giorno di pubblicazione inferiore complessivamente a dodici ore, in base a un’attestazione del responsabile della pubblicazione o di un suo delegato.%6\$s","albo-pretorio-considera"),'<span style="font-weight: bold;font-style: italic;color:red;">','</span><br />','<br /><br /><strong><em>','</em><br />','<br />','<strong>').'</p>
				<p style="font-weight: bold;font-style: italic;color:red;">'.__("Questa è un'operazione irreversibile e modifica dati sostanziali degli Atti, si consiglia di eseguire un backup prima di procedere, per poter recuperare i dati originali in caso di errori.","albo-pretorio-considera").'</p>';
switch ($passo){
	case 0:
		echo '<form action="?page=utilityAlboP" id="ripub" method="post"  class="validate">
				<input type="hidden" name="action" value="setData" />
				<input type="hidden" name="ripub" value="'.wp_create_nonce('ripubblicaatti').'" />
				<table class="widefat" style="border: thin solid #f9f9f9;">
					<tr>
						<th style="width:10em;">'.__("Giorno di inizio Interruzione","albo-pretorio-considera").'</th>
						<td><input name="DataInterruzione" id="DataInterruzione" type="date" max="'.gmdate("Y-m-d").'"/></td>
					</tr>
					<tr>
						<th>'.__("Giorni di Interruzione","albo-pretorio-considera").'</th>
						<td><input name="ggInterruzione" id="ggInterruzione" type="number" min="1" style="width:5em;"/></td>
					</tr>
				</table>
				<input type="submit" name="submit" id="submit" class="button" value="'.__("Avvia Procedura","albo-pretorio-considera").'"  />
				</form>
				';
		break;
	case 1:
		$AData=ap_DateAdd($Data,$GG);
		$TotAtti=ap_get_all_atti(10,0,0,0,'',$Data,$AData,'',0,0,true,false,'',-1,true);
		echo'<p><span style="font-style: italic;color:green;"><strong>'.esc_html($TotAtti).'</strong> '.__("Atti in pubblicazione da data","albo-pretorio-considera").' '.esc_html(ap_VisualizzaData($Data)).' '.__("a data","albo-pretorio-considera").' '.esc_html(ap_VisualizzaData($AData)).'</span></p>';
		$atti =ap_get_all_atti(10,0,0,0,'',$Data,$AData,"Numero Desc");
		$ArrAggSca=array();
		if ( ! empty( $atti ) ) {	
			echo "<ul>";
			foreach ($atti as $a) {
//				echo "<pre>";var_dump($a);"</pre>";
				echo "<li>(".esc_html($a->IdAtto).") ".esc_html($a->Oggetto)." del ".esc_html($a->Numero)."/".esc_html($a->Anno);
				if($a->DataInizio>$Data){
					$AttoDaData=$a->DataInizio;
				}else{
					$AttoDaData=$Data;
				}
				if($a->DataFine>$AData){
					$AttoAData=$AData;
				}else{
					$AttoAData=$a->DataFine;
				}
				$NggInc=ap_datediff("d",$AttoDaData,$AttoAData);
				if($NggInc==0)
						$NggInc=1;
				$NuovaScadenza=ap_DateAdd($a->DataFine,$NggInc);
				while(ap_IsDataFestiva($NuovaScadenza)){
					$NuovaScadenza=ap_DateAdd($NuovaScadenza,1);
					$NggInc++;
				}				
				echo " (".$a->DataInizio." - ".$a->DataFine.") gg >> ".$NggInc." Nuova scadenza ".ap_DateAdd($a->DataFine,$NggInc);
				echo "</li>";
				$ArrAggSca[$a->IdAtto]=(int)$NggInc;
			}
			echo "</ul>";
			echo '<a href="'.wp_nonce_url('?page=utilityAlboP&action=rip&AttiDaAgg='.serialize($ArrAggSca),'ripubblica_atti').'" class="ripubblica" rel="'.$TotAtti.'">'.__("Ripubblica gli atti a causa dell'interruzione del servizio","albo-pretorio-considera").'</a>?';
		}
}
echo '		</div> 
		<div id="utility-tab-2" style="margin-bottom:20px;">
			<p style="font-style: italic;">
'.__("Questa procedura esegue un test generale della procedura e riporta eventuali anomalie nei dati e nelle impostazioni.","albo-pretorio-considera").'
			</p>
			<p>'.__("Operazioni eseguite","albo-pretorio-considera").':
				<ul style="font-style: italic;font-weight: bold;list-style-type: disc;margin-left:15px;">
					<li>'.__("Verifica permessi cartella di Upload degli allegati","albo-pretorio-considera").'</li>
					<li>'.__("Verifica dati del Data Base e viene riportata una breve statistica sui dati","albo-pretorio-considera").'</li>
				</ul>
			</p>
				<p style="text-align:center;font-weight: bold;">
 					<button type="button" onclick="location.href=\'?page=utilityAlboP&action=verificaproc&amp;verproc='.wp_create_nonce('verificaprocedura').'\'"> '.__("Verifica","albo-pretorio-considera").' </button>
				</p>
		</div>
		<div id="utility-tab-3" style="margin-bottom:20px;">
				<p style="text-align:left;font-style: italic;">
'.__("Questa procedura esegue le operazioni necessarie per l'allineamento dei files e delle tabelle del DataBase per mantenere il diritto all'oblio degli atti pubblicati","albo-pretorio-considera").':
					<ul style="list-style: circle inside;">
						<li>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Aggiornamento del contenuto del files %1\$s e %2\$s nella cartella","albo-pretorio-considera"),'<span style="font-weight: bold;">.htaccess</span>','<span style="font-weight: bold;">index.php</span>').' <span style="font-style: italic;font-weight: bold;"> '.AP_BASE_DIR.'AllegatiAttiAlboPretorio</span></li>
						<li>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Aggiornamento del contenuto del file %s nella cartella","albo-pretorio-considera"),'<span style="font-weight: bold;">robots.txt</span>').' <span style="font-style: italic;font-weight: bold;">'.$basedir.'</span></li>
						<li>'.__("Aggiornamento del percorso nella tabella degli allegati nel Data Base","albo-pretorio-considera").'</li>
					</ul>
				<p style="text-align:center;font-weight: bold;">
 					<button  type="button" onclick="location.href=\'?page=utilityAlboP&action=posttrasf&amp;posttrasf='.wp_create_nonce('posttrasferimento').'\'"> '.__("Avvia operazione","albo-pretorio-considera").' </button>
				</p>
		</div>
		<div id="utility-tab-4" style="margin-bottom:20px;">
			<p style="font-style: italic;font-weight: bold;">'.__("Questa procedura può cancellare una grossa quantità di dati, se non si vuole perderli si consiglia di fare un backup del DataBase o della tabella","albo-pretorio-considera").' <span style="font-style: normal;">'.$wpdb->table_name_Log.'</span>
				</p>
					<ul style="list-style: none;">
						<li>'.__("Questa procedura cancella tutte le registrazioni presenti nel file di log","albo-pretorio-considera").'&nbsp;&nbsp;
							<button  type="button" onclick="location.href=\'?page=utilityAlboP&action=svuotalog&amp;svuotalog='.wp_create_nonce('svuotafilelog').'\'"> '.__("Svuota file di Log","albo-pretorio-considera").' 
							</button>
						</li>
						<li>'.__("Questa procedura cancella tutte le registrazioni di gestione dal file di log mantenendo le statistiche di accesso","albo-pretorio-considera").'
							<button type="button" onclick="location.href=\'?page=utilityAlboP&action=puliscilog&amp;puliscilog='.wp_create_nonce('puliscifilelog').'\'"> '.__("Pulisci file di Log","albo-pretorio-considera").' 
							</button>
						</li>
					</ul>
		</div>';
//$elenco="<option value='' selected='selected'>Nessuno</option>";
$elencoExpo="";
$Dir=str_replace("\\","/",WP_CONTENT_DIR.'/AlboOnLine/BackupDatiAlbo');
if (is_dir($Dir)){
	$files_bck = scandir($Dir, 1);
	foreach($files_bck as $fileinfo) {
		if (is_file($Dir."/".$fileinfo)) {
//				$elenco.="<option value='".$fileinfo."'>".$fileinfo."</option>"; 
				$elencoExpo.="<option value='".$fileinfo."'>".$fileinfo."</option>"; 
		}
	}
}
echo '  <div id="utility-tab-5" style="margin-bottom:20px;">
				<p>
				<form action="?page=utilityAlboP" id="backup" method="post"  class="validate">
					<input type="hidden" name="action" value="BackupData" />
					<input type="hidden" name="bckdata" value="'.wp_create_nonce('BackupDatiAlbo').'" />
					'.__("Backup dei Dati","albo-pretorio-considera").':&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" id="submit" class="button" value="'.__("Avvia Backup","albo-pretorio-considera").'"  />
				</form>
				</p>
				<p>
					<form action="?page=utilityAlboP" id="exportBackup" method="post"  class="validate">
					'.__("Esporta file di Backup","albo-pretorio-considera").': 
						<input type="hidden" name="action" value="ExportBackupData" />
						<input type="hidden" name="exportbckdata" value="'.wp_create_nonce('EsportaBackupDatiAlbo').'" />
						<select name="elenco_Backup_Expo" id="elenco_Backup_Expo" >\n'
						.$elencoExpo.'
						</select>
						<input type="submit" name="submitExpo" id="submitExpo" class="button" value="'.__("Esporta Backup","albo-pretorio-considera").'"/>
					</form>
				</p>
	</div>
	<div id="utility-tab-6" style="margin-bottom:20px; height: 600px;">';

	if (isset($_GET['Anno']))
		$AnnoRepertorio=(isset($_GET['Anno'])?intval($_GET['Anno']):0);
	else
		$AnnoRepertorio=gmdate("Y");
		if (($Anni=ap_AnniAtti())!=FALSE){
			echo '<div style="display:inline">
			'.__("Repertorio","albo-pretorio-considera").' <select id="Anno" onchange="document.location.href=this.options[this.selectedIndex].value;">
				<option value="">'.__("Anno","albo-pretorio-considera").'</option>';
			foreach($Anni as $Anno){
				echo '<option value="'.admin_url().'/admin.php?page=utilityAlboP&amp;Anno='.$Anno->Anno.'#utility-tab-6">'.$Anno->Anno.'</option>';
			}
			echo '
			</select>
					<a href="'.wp_nonce_url('?page=utilityAlboP&amp;p=5&amp;Anno='.$AnnoRepertorio.'&amp;action=ToXML&amp;Anno='.$AnnoRepertorio,'repertorio_export').'">
					<img src="'.Albo_URL.'/img/XML.png" title="'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Esporta il repertorio del %s in XML","albo-pretorio-considera"),$AnnoRepertorio).'" style="vertical-align: middle;"/></a>
				<a href="'.wp_nonce_url('?page=utilityAlboP&amp;p=5&amp;Anno='.$AnnoRepertorio.'&amp;action=ToJson&amp;Anno='.$AnnoRepertorio,'repertorio_export').'">
					<img src="'.Albo_URL.'/img/Json.png" title="'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Esporta il repertorio del %s in Json","albo-pretorio-considera"),$AnnoRepertorio).'" style="vertical-align: middle;"/></a>
				<a href="'.wp_nonce_url('?page=utilityAlboP&amp;p=5&amp;Anno='.$AnnoRepertorio.'&amp;action=ToCsv&amp;Anno='.$AnnoRepertorio,'repertorio_export').'">
					<img src="'.Albo_URL.'/img/Csv.png" title="'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Esporta il repertorio del %s in csv","albo-pretorio-considera"),$AnnoRepertorio).'" style="vertical-align: middle;"/></a>
	</div>
			<h3 style="text-align:center">'.__("Repertorio Anno","albo-pretorio-considera").' '.$AnnoRepertorio.'</h3>
			<div style="overflow: scroll;height:440px;">
				<table class="widefat" id="Repertorio-anno" style="border: thin solid #f9f9f9;">
					<thead>
						<th>'.__("Ente titolare dell'Atto","albo-pretorio-considera").'</th>
						<th>'.__("Numero progressivo","albo-pretorio-considera").'</th>
						<th>'.__("Codice di Riferimento","albo-pretorio-considera").'</th>
						<th>'.__("Oggetto","albo-pretorio-considera").'</th>
						<th>'.__("Data inizio Pubblicazione","albo-pretorio-considera").'</th>
						<th>'.__("Data fine Pubblicazione","albo-pretorio-considera").'</th>
						<th>'.__("Data Annullamento","albo-pretorio-considera").'</th>
						<th>'.__("Motivo Annullamento","albo-pretorio-considera").'</th>
						<th>'.__("Richiedente","albo-pretorio-considera").'</th>
						<th>'.__("Unità Organizzativa Responsabile","albo-pretorio-considera").'</th>
						<th>'.__("Responsabile del procedimento amministrativo","albo-pretorio-considera").'</th>
						<th>'.__("Categoria","albo-pretorio-considera").'</th>
						<th>'.__("Note","albo-pretorio-considera").'</th>
					</thead>
					<tbody>';
					echo ap_Repertorio($AnnoRepertorio);
					echo '</tbody>
				</table>
			</div>';
		}
echo'	
	</div>	
	<div id="utility-tab-7" style="margin-bottom:20px; height: 600px;">
			<h3 style="text-align:center">'.__("Adeguamento DGPR","albo-pretorio-considera").'</h3>	
				<p>
					<form action="?page=utilityAlboP" id="GDPR" method="post"  class="validate">
					IP nel Log:
					<input type="hidden" name="securdeliplog" value="'.wp_create_nonce( 'svuotavaloriipnelfiledilog' ).'" />
					<input type="hidden" name="action" value="DelIPLog" />
						<input type="submit" name="DelIPLog" id="DelIPLog" class="button" value="'.__("Cancella","albo-pretorio-considera").'"  />
					</form>
				</p>
	</div>	
	<div id="utility-tab-8" style="margin-bottom:20px; height: 600px;">
			<h3 style="text-align:center">'.__("Utility Dati Atti","albo-pretorio-considera").'</h3>	
				<p>
					<form action="?page=utilityAlboP" id="Utility" method="post"  class="validate">
					'.__("Imposta Ente","albo-pretorio-considera").':  
					<input type="hidden" name="ImpostaEnteND" value="'.wp_create_nonce( 'securimpostaentend' ).'" />
					<input type="hidden" name="action" value="ImpostaEnteND" />
					'.ap_get_dropdown_enti('Ente','Ente','postform richiesto ValValue(>-1)','',0).'
					<input type="submit" name="ImpEnte" id="ImpEnte" class="button" value="Imposta"  />
					<p>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Questa procedura imposta %1\$s in tutti gli atti in cui l'Ente risulta come %2\$sEnte non definito","albo-pretorio-considera"),'<strong>'.ap_get_ente_me().'</strong>','<span style="color:red;">').'</span></p>
					</form>
				</p>				
	</div>';
	if ($dirUploadMA){
		echo '	<div id="utility-tab-9" style="margin-bottom:20px; height: 600px;">
		<h3 style="text-align:center">'.__("Utility Organizzazione cartella Upload Allegati","albo-pretorio-considera").'</h3>
		<p><span style="color:red;"><strong>'.__("Attenzione","albo-pretorio-considera").'</strong><br />
		'.__("Questa operazione richiede molto tempo, potrebbe essere interrotta prima del termine per Time Out.","albo-pretorio-considera").'<br />
		'.__("Soluzioni","albo-pretorio-considera").':
		<ul>
			<li>'.__("modificare il file php.ini parametro max_execution_time=XXX modificare inserendo il numero di secondi in genere di default sono 60","albo-pretorio-considera").'</li>
			<li>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("modificare il file %s: aggiungere il seguente comando set_time_limit(XXX)","albo-pretorio-considera"),"wp-config.php").'
			<li>'.sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("modificare il file %s: fare preventivamente una copia del file. Aggiungere la segunte direttiva: php_value max_execution_time XXX","albo-pretorio-considera"),'.htaccess').'.</li>
		</ul>
		<em>'.__("un valore possibile di XXX è 360 (6 minuti)","albo-pretorio-considera").'</em>
		</span>
		</p>
		<hr />
		<p>
			<form action="?page=utilityAlboP" id="Utility" method="post"  class="validate">
			'.__("Selezionare questa opzione per eseguire il Backup. (Se eseguite il Backup separatamente potete risparmiare tempo di esecuzione della procedura)","albo-pretorio-considera").' 
			<input type="checkbox" name="esBackup" value="Si" id="esBackup"/> <br />
			'.__("Attiva Archiviazione per Mese Anno","albo-pretorio-considera").':
			<input type="hidden" name="securarchivioannomese" value="'.wp_create_nonce( 'archivioannomese' ).'" />
			<input type="hidden" name="action" value="ArchivioAnnoMese" />
			<input type="submit" name="ArchivioAnnoMese" id="ArchivioAnnoMese" class="button" value="'.__("Attiva","albo-pretorio-considera").'"  />
			</form>
		</p>
		</div>';
	}
echo '	<div id="utility-tab-10" style="margin-bottom:20px; height: 600px;">
<h3 style="text-align:center">'.__("Utility che aggiorna l'impronta HASH degli allegati","albo-pretorio-considera").'</h3>
	<p><span style="color:red;"><strong>'.__("Attenzione","albo-pretorio-considera").'</strong><br />
	'.__("Questa operazione potrebbe richiede molto tempo, potrebbe essere interrotta prima del termine per Time Out.","albo-pretorio-considera").'
	</p>
	<button type="button" onclick="location.href=\'?page=utilityAlboP&action=agghash&amp;verproc='.wp_create_nonce('verificaprocedura').'\'"> '.__("Aggiorna","albo-pretorio-considera").' </button>
	</div>
</div>';
}
	
function TestCampiTabella($Tabella,$Ripara=false){
	global $wpdb;
switch ($Tabella){
	case $wpdb->table_name_Atti:
		$Par=array("IdAtto" => array("Tipo" => "int(11)",
								     "Null" =>"NO",
									 "Key" => "PRI",
									 "Default" => "",
									 "Extra" =>"auto_increment"),
			  		"Numero" => array("Tipo" => "int(4)",
					  				  "Null" =>"NO",
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"Anno" => array("Tipo" => "int(4)", 
									"Null" =>"NO", 
									"Key" => "", 
									"Default" => "0", 
									"Extra" =>""),
					"Data" => array("Tipo" => "date", 
									"Null" =>"NO", 
									"Key" => "", 
									"Default" => "0000-00-00", 
									"Extra" =>""),
					"Riferimento" => array("Tipo" => "text", 
										   "Null" =>"No", 
										   "Key" => "", 
										   "Default" => "", 
										   "Extra" =>""),
					"Oggetto" => array("Tipo" => "text", 
									   "Null" =>"No", 
									   "Key" => "", 
									   "Default" => "", 
									   "Extra" =>""),
					"DataInizio" => array("Tipo" => "date", 
										  "Null" =>"NO", 
										  "Key" => "", 
										  "Default" => "0000-00-00", 
										  "Extra" =>""),
					"DataFine" => array("Tipo" => "date", 
										"Null" =>"YES", 
										"Key" => "", 
										"Default" => "0000-00-00", 
										"Extra" =>""),
					"Informazioni" => array("Tipo" => "text", 
											"Null" =>"NO", 
											"Key" => "", 
											"Default" => "", 
											"Extra" =>""),
					"IdCategoria" => array("Tipo" => "int(11)", 
										   "Null" =>"NO", 
										   "Key" => "", 
										   "Default" => "0", 
										   "Extra" =>""),
					"RespProc" => array("Tipo" => "int(11)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"DataAnnullamento" => array("Tipo" => "date", 
												"Null" =>"YES", 
												"Key" => "", 
												"Default" => "0000-00-00", 
												"Extra" =>""),
					"MotivoAnnullamento" => array("Tipo" => "text", 
												  "Null" =>"YES", 
												  "Key" => "", 
												  "Default" => "", 
												  "Extra" =>""),
					"Ente" => array("Tipo" => "int(11)",
									"Null" =>"NO", 
									"Key" => "", 
									"Default" => "0", 
									"Extra" =>""),
					"DataOblio" => array("Tipo" => "date", 
												"Null" =>"NO", 
												"Key" => "", 
												"Default" => "0000-00-00", 
												"Extra" =>""),
					"Soggetti" => array("Tipo" => "varchar(100)", 
												"Null" =>"NO", 
												"Key" => "", 
												"Default" => "", 
												"Extra" =>""),
					"IdUnitaOrganizzativa" => array("Tipo" => "int(11)",
												"Null" =>"NO",
												"Key" => "",
												"Default" => "0",
												"Extra" =>""),					
					"Richiedente" => array("Tipo" => "varchar(100)",
												"Null" =>"NO",
												"Key" => "",
												"Default" => "",
												"Extra" =>""));
		break;
	case $wpdb->table_name_Allegati:
		$Par=array("IdAllegato" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "TitoloAllegato" => array("Tipo" => "varchar(255)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Allegato" => array("Tipo" => "varchar(255)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"IdAtto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"TipoFile" => array("Tipo" => "varchar(6)", 
									  "Null" =>"YES", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""),
					"DocIntegrale" => array("Tipo" => "tinyint(1)",
									  "Null" =>"NO",
									  "Key" => "",
									  "Default" => "1",
									  "Extra" =>""),									  
					"Impronta" => array("Tipo" => "char(64)",
									  "Null" =>"No",
									  "Key" => "",
									  "Default" => "",
									  "Extra" =>""),
					"Natura" => array("Tipo" => "char(1)",
									  "Null" =>"No",
									  "Key" => "",
									  "Default" => "A",
									  "Extra" =>""),
					"Note" => array("Tipo" => "varchar(255)",
									  "Null" =>"Si",
									  "Key" => "",
									  "Default" => "",
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Categorie:
		$Par=array("IdCategoria" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "Nome" => array("Tipo" => "varchar(255)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Descrizione" => array("Tipo" => "varchar(255)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Genitore" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"Giorni" => array("Tipo" => "smallint(3)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Log:
		$Par=array("Data" => array("Tipo" => "timestamp", 
										 "Null" =>"NO", 
										 "Key" => "", 
										 "Default" => "CURRENT_TIMESTAMP", 
										 "Extra" =>""),
		  		   "Utente" => array("Tipo" => "varchar(60)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"IPAddress" => array("Tipo" => "varchar(16)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Oggetto" => array("Tipo" => "int(1)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"IdOggetto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"IdOggetto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"IdAtto" => array("Tipo" => "int(11)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "0", 
									  "Extra" =>""),
					"TipoOperazione" => array("Tipo" => "int(1)", 
									  "Null" =>"NO", 
									  "Key" => "", 
									  "Default" => "1", 
									  "Extra" =>""),
					"Operazione" => array("Tipo" => "text", 
									  "Null" =>"Yes", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_RespProc:
		$Par=array("IdResponsabile" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "Cognome" => array("Tipo" => "varchar(20)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
		  		   "Nome" => array("Tipo" => "varchar(20)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Email" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Telefono" => array("Tipo" => "varchar(30)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Orario" => array("Tipo" => "varchar(60)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Note" => array("Tipo" => "text", 
									  "Null" =>"YES", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""),
					"Funzione" => array("Tipo" => "char(8)", 
									  "Null" =>"YES", 
									  "Key" => "", 
									  "Default" => "RP", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Enti:
		$Par=array("IdEnte" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "Nome" => array("Tipo" => "varchar(100)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
		  		   "Indirizzo" => array("Tipo" => "varchar(150)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
					"Url" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Email" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Pec" => array("Tipo" => "varchar(100)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Telefono" => array("Tipo" => "varchar(40)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Fax" => array("Tipo" => "varchar(40)", 
										"Null" =>"NO", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""),
					"Note" => array("Tipo" => "text", 
									  "Null" =>"Yes", 
									  "Key" => "", 
									  "Default" => "", 
									  "Extra" =>""));
		break;
	case $wpdb->table_name_Attimeta:
		$Par=array("IdAttoMeta" => array("Tipo" => "int(11)", 
										 "Null" =>"NO", 
										 "Key" => "PRI", 
										 "Default" => "", 
										 "Extra" =>"auto_increment"),
		  		   "IdAtto" => array("Tipo" => "int(11)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
		  		   "Meta" => array("Tipo" => "varchar(100)", 
					 						 "Null" =>"NO", 
											 "Key" => "", 
											 "Default" => "", 
											 "Extra" =>""),
				   "Value" => array("Tipo" => "text", 
										"Null" =>"YES", 
										"Key" => "", 
										"Default" => "", 
										"Extra" =>""));
		break;
}
        $wpdb->flush();
        $result=$wpdb->get_results("Describe $Tabella");
        $Verificato=true;
        $Msg="";
//		var_dump($Tabella);
		foreach ( $result as $campo ) {
//		echo "<pre>";var_dump($campo->Field);var_dump($campo->Type);var_dump($Par[$campo->Field]["Default"]);echo "</pre>";
			if (is_string($Par[$campo->Field]["Tipo"]) And is_string($campo->Default) And strtolower($Par[$campo->Field]["Tipo"])!=strtolower($campo->Type)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;".__("Tipo DB","albo-pretorio-considera")." <strong>". $campo->Type . "</strong><br />&nbsp;&nbsp;&nbsp;".__("Tipo Originale","albo-pretorio-considera")." <strong>".$Par[$campo->Field]["Tipo"]."</strong><br />";
				$Verificato=false;
			}
			if (is_string($Par[$campo->Field]["Null"]) And is_string($campo->Default) And strtolower($Par[$campo->Field]["Null"])!=strtolower($campo->Null)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Null DB <strong>". $campo->Null . "</strong><br />&nbsp;&nbsp;&nbsp;Null ".__("Originale","albo-pretorio-considera")." <strong>".$Par[$campo->Field]["Null"]."</strong><br />";
				$Verificato=false;
			}
			if (is_string($Par[$campo->Field]["Default"]) And is_string($campo->Default) And strtolower($Par[$campo->Field]["Default"])!=strtolower($campo->Default)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Default DB <strong>". $campo->Default . "</strong><br />&nbsp;&nbsp;&nbsp;Default ".__("Originale","albo-pretorio-considera")." <strong>".$Par[$campo->Field]["Default"]."</strong><br />";
				$Verificato=false;
			}
			if (is_string($Par[$campo->Field]["Extra"]) And is_string($campo->Default) And strtolower($Par[$campo->Field]["Extra"])!=strtolower($campo->Extra)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Extra DB <strong>". $campo->Extra . "</strong><br />&nbsp;&nbsp;&nbsp;Extra ".__("Originale","albo-pretorio-considera")." <strong>".$Par[$campo->Field]["Extra"]."</strong><br />";
				$Verificato=false;
			}
			if (is_string($Par[$campo->Field]["Key"]) And is_string($campo->Default) And strtolower($Par[$campo->Field]["Key"])!=strtolower($campo->Key)){
				$Msg.= "<strong>".$campo->Field."</strong><br />&nbsp;&nbsp;&nbsp;Key DB <strong>". $campo->Key . "</strong><br />&nbsp;&nbsp;&nbsp;Key ".__("Originale","albo-pretorio-considera")." <strong>".$Par[$campo->Field]["Key"]."</strong><br />";
				$Verificato=false;
			}
		}
		if ($Verificato == True)
			$Msg.= '<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
		return $Msg;
}
function TestCongruitaDati($Tabella){
	global $wpdb;
	$Analisi="";	
	switch ($Tabella){
		case $wpdb->table_name_Atti:
		  	$Analisi.='<em>'.__("Atti","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(0,0,0,0,'', 0,0,"",0,0,true).'</strong><br />';
			$Analisi.='<em>'.__("Atti da Pubblicare","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(3,0,0,0,'', 0,0,"",0,0,true).'</strong><br />';
			$Analisi.='<em>'.__("Atti In corso di Validità","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(1,0,0,0,'', 0,0,"",0,0,true).'</strong> ';
			$Analisi.='<em> '.__("di cui Annullati","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(1,0,0,0,'', 0,0,"",0,0,true,true).'</strong><br />';
			$Analisi.='<em>'.__("Atti Scaduti","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(2,0,0,0,'', 0,0,"",0,0,true).'</strong> ';
			$Analisi.='<em> '.__("di cui Annullati","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(2,0,0,0,'', 0,0,"",0,0,true,true).'</strong><br />';
			$Analisi.='<em>'.__("Atti Oblio (da Cancellare)","albo-pretorio-considera").':</em><strong>'.ap_get_all_atti(4,0,0,0,'', 0,0,"",0,0,true).'</strong><br />';
			// Verifica Atti con Categorie Orfane
			$CategorieOrfane=ap_categorie_orfane();
			if ($CategorieOrfane){
				foreach ($CategorieOrfane as $CategoriaOrfana){
					$Analisi.=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sAtto N. %2\$s/%3\$sriporta la Categoria con Codice %4\$sNON TROVATA nella tabella Categorie %5\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$CategoriaOrfana->Numero,$CategoriaOrfana->Anno.'</strong> <em>','</em><strong>'.$CategoriaOrfana->IdCategoria.'</strong> <em>','<br />');
				}
			}
			$EntiOrfani=ap_enti_orfani();
			if ($EntiOrfani){
				foreach ($EntiOrfani as $EnteOrfano){
					$Analisi.=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sAtto N. %2\$s/%3\$sriporta l'ente con Codice %4\$sNON TROVATA nella tabella Enti %5\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$EnteOrfano->Numero,$EnteOrfano->Anno.'</strong> <em>','</em><strong>'.$EnteOrfano->Ente.'</strong> <em>','<br />');
				}
			}
			$ResponsabiliOrfani=ap_responsabili_orfani();
			if ($ResponsabiliOrfani){
				foreach ($ResponsabiliOrfani as $ResponsabileOrfano) {
					$Analisi.=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sAtto N. %2\$s/%3\$sriporta il responsabile con Codice %4\$sNON TROVATA nella tabella Responsabili %5\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$ResponsabileOrfano->Numero,$ResponsabileOrfano->Anno.'</strong> <em>','</em><strong>'.$ResponsabileOrfano->RespProc.'</strong> <em>','<br />');
				}
			}
			return $Analisi;
			break;
		case $wpdb->table_name_Allegati:
			$NumAllegati=ap_num_allegati();
			$AllegatiOrfani=ap_allegati_orfani();
			$Analisi= sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sN. Allegati %2\$s di cui orfani %3\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$NumAllegati.'</strong> <em>','</em><strong> '.count($AllegatiOrfani).'</strong> <br />');
			if (count($AllegatiOrfani)>0)
				$Analisi.="<br /><strong>".__("Allegati Orfani","albo-pretorio-considera")."</strong><br />";
			foreach ($AllegatiOrfani as $AllegatoOrfano){
				$Analisi.=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sAllegato %2\$s Associato all'Atto con id n.%3\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$AllegatoOrfano->IdAtto.'</strong> <em>','</em><strong> '.$AllegatoOrfano->IdAtto.'</strong> <br />');
			}
			return $Analisi;
			break;
		case $wpdb->table_name_Categorie:
			$NumCategorie=ap_num_categorie();
			$NumCategorieInutilizzate=ap_num_categorie_inutilizzate();
			$Categorie=ap_get_categorie();
			$UsoCategorie="";
			foreach ($Categorie as $Categoria){
				$NCategorie=ap_num_categoria_atto($Categoria->IdCategoria);
				$NCategorie=$NCategorie ? $NCategorie : 0;
				$UsoCategorie.=sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$s Presente in %2\$s Associato all'Atto con id n.%3\$s Presente in Atti%4\$s","albo-pretorio-considera"),'<em>','<em>'.$Categoria->Nome,'</em><strong>'.$NCategorie .'</strong> <em>', '</em><br />');
			}
			return sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$sCategorie codificate %2\$s di cui inutilizzate%3\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$NumCategorie.'</strong> <em>','</em><strong> '.$NumCategorieInutilizzate.'</strong> <br />'.$UsoCategorie);
			break;
		case $wpdb->table_name_Log:
			$LogPerOggetti=ap_get_Stat_Log("Oggetto");
			$Statistiche="<strong>".__("Numero record per Oggetto","albo-pretorio-considera")."</strong><br />";
			foreach ($LogPerOggetti as $LogPerOggetto){
				$Statistiche.="<em>".$LogPerOggetto->NomeOggetto." => </em><strong>".$LogPerOggetto->Numero ."</strong><br />";	
			}
			$LogPerTipoOperazioni=ap_get_Stat_Log("TipoOperazione");
			$Statistiche.="<strong>".__("Numero record per Tipo Operazione","albo-pretorio-considera")."</strong><br />";
			foreach ($LogPerTipoOperazioni as $LogPerTipoOperazione){
				$Statistiche.="<em>".$LogPerTipoOperazione->NomeTipoOperazione." => </em><strong>".$LogPerTipoOperazione->Numero ."</strong><br />";	
			}
			return $Statistiche;
			break;
		case $wpdb->table_name_RespProc:
			$NumResp=ap_num_responsabili();
			$NumResponsabiliInutilizzate=ap_num_responsabili_inutilizzati();
			$Responsabili=ap_get_responsabili();
			$UsoResponsabili="";
			foreach ($Responsabili as $Responsabile){
				$NResponsabile=ap_get_NumAttiSoggetto($Responsabile->IdResponsabile);
				$NResponsabile=$NResponsabile ? $NResponsabile : 0;
				$UsoResponsabili.="<em>".$Responsabile->Cognome." ".$Responsabile->Nome." ".__("Presente in","albo-pretorio-considera")." </em><strong>".$NResponsabile ."</strong> <em>".__("Atti","albo-pretorio-considera")."</em><br />";	
			}
			return sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$s Responsabili codificati %2\$s di cui inutilizzati%3\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$NumResp.'</strong> <em>','</em><strong> '.$NumResponsabiliInutilizzate.'</strong><br />'.$UsoResponsabili);
			break;
		case $wpdb->table_name_Enti:
			$NumEnti=ap_num_enti();
			$NumEntiInutilizzati=ap_num_enti_Inutilizzati();
			$Enti=ap_get_enti();
			$UsoEnti="";
			foreach ($Enti as $Ente){
				$NAtti=ap_num_enti_atto($Ente->IdEnte);
				$NAtti=$NAtti ? $NAtti : 0;
				$UsoEnti.="<em>".$Ente->Nome." ".__("Presente in","albo-pretorio-considera")." </em><strong>".$NAtti ."</strong> <em>".__("Atti","albo-pretorio-considera")."</em><br />";	
			}
			return sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("%1\$s Enti codificati %2\$s di cui inutilizzati%3\$s","albo-pretorio-considera"),'<em>','</em><strong>'.$NumEnti,'</em><strong>'.$NumEntiInutilizzati.'</strong><br />'.$UsoEnti);
			break;
		case $wpdb->table_name_Attimeta:
			$MetaDati=ap_get_elenco_attimeta("Array","listaAttiMeta","ListaAttiMeta","Si",0,TRUE);
			$MetaRighe="";
			foreach ($MetaDati as $MetaDato){
				$MetaRighe.="<strong>".$MetaDato->Meta."</strong> => <strong> ".$MetaDato->Value."</strong> ".__("Presente in","albo-pretorio-considera")." </em><br />";
				$Atti="";
				$AttiEstratti=ap_get_GruppiAtti($MetaDato->Meta,$MetaDato->Value);
				foreach($AttiEstratti as $AttiEstratto){
					$MetaRighe.="   (<a href='".get_admin_url()."admin.php?page=atti&action=view-atto&id=".$AttiEstratto->IdAtto."&stato_atti=Tutti'>".$AttiEstratto->IdAtto."</a>) ".$AttiEstratto->Oggetto."<br />";
				}
			}
			return "<em>".__("Meta Dati codificati","albo-pretorio-considera")." </em>".$MetaRighe; 
			break;	
	}	
}

function TestProcedura(){
	global $wpdb;
$Tabelle=array($wpdb->table_name_Atti,
			   $wpdb->table_name_Categorie,
			   $wpdb->table_name_Allegati,
			   $wpdb->table_name_Log,
			   $wpdb->table_name_RespProc,
			   $wpdb->table_name_Enti,
			   $wpdb->table_name_Attimeta);
if(is_file(AP_BASE_DIR.get_option('opt_AP_FolderUpload')."/.htaccess"))
	$ob1=TRUE;
else	
	$ob1=FALSE;
if(is_file(AP_BASE_DIR.get_option('opt_AP_FolderUpload')."/index.php"))
	$ob2=TRUE;
else	
	$ob2=FALSE;
if(is_file(APHomePath."/robots.txt"))
	$ob3=TRUE;
else	
	$ob3=FALSE;
echo '<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-admin-generic" style="font-size:1em;"></span> '.__("Analisi procedura","albo-pretorio-considera").'
		<a href="?page=utilityAlboP" class="add-new-h2">'.__("Torna indietro","albo-pretorio-considera").'</a></h2>
	</div>		
	<div class="postbox-container" style=";margin-top:20px;">
		<h3>'.__("Librerie","albo-pretorio-considera").'Librerie</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:200px;">'.__("Libreria","albo-pretorio-considera").'</th>
							<th style="text-align:left;width:50px;">'.__("Stato","albo-pretorio-considera").'</th>
							<th style="text-align:left;width:230px;">'.__("Note","albo-pretorio-considera").'</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>ZipArchive</td>
							<td>';
if (class_exists('ZipArchive')) 
 		echo'<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span></td><td>--</td>';
	else
		echo'<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span></td>
		<td>'.__("Senza questa libreria non puoi eseguire i Backup","albo-pretorio-considera").'</td>';							
echo '							
						</tr>
					</tbody>
				</table>
		</div>						
		<h3>'.__("Diritto all'OBLIO","albo-pretorio-considera").'</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:440px;">'.__("Cartella","albo-pretorio-considera").'</th>
							<th style="text-align:left;width:80px;">.htaccess</th>
							<th style="text-align:left;width:80px;">index.php</th>
							<th style="text-align:left;width:80px;">robots.txt</th>
							<th style="text-align:left;width:100px;">'.__("Operazione","albo-pretorio-considera").'</th>';
echo'
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>'.AP_BASE_DIR.get_option('opt_AP_FolderUpload').'</td>
							<td>';
if($ob1)
 		echo'<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		echo'<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';							
echo '							</td>
							<td>';
if($ob2)
 		echo'<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		echo'<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';							
echo '							</td>
			<td></td>';
//if (!$ob1 or !$ob2)
echo '							<td><a href="?page=utilityAlboP&amp;action=creaninf&amp;rigenera='.wp_create_nonce('rigenerasic').'">'.__("Rigenera","albo-pretorio-considera").'</a></td>';
echo '
						</tr>
						<tr>
							<td>'.APHomePath.'</td>
							<td></td>
							<td></td>
							<td>';
if($ob3)
 		echo'<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		echo'<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';							
echo '							</td>';
//if (!$ob3)
echo '							<td><a href="?page=utilityAlboP&amp;action=Crearobots&amp;creasic='.wp_create_nonce('creasicurezza').'">'.__("Crea","albo-pretorio-considera").'</a></td>';
echo '
						</tr>
					</tbody>
				</table>
		</div>			
		<h3>'.__("Permessi Cartella Upload","albo-pretorio-considera").'</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:380px;">'.__("Cartella","albo-pretorio-considera").'Cartella</th>
							<th style="text-align:left;width:100px;">'.__("Permessi","albo-pretorio-considera").'</th>
							<th style="text-align:left;width:100px;">'.__("Stato","albo-pretorio-considera").'</th>
						</tr>
					</thead>
					<tbody>';
$CartellaUp=str_replace("\\","/",AP_BASE_DIR.get_option('opt_AP_FolderUpload'));
$permessi=ap_get_fileperm($CartellaUp);		
$permProp=ap_get_fileperm_Gruppo($CartellaUp,"Proprietario");
if($permProp==7 Or $permProp==6 Or $permProp==3 Or $permProp==2)
 		$StatoCartella='<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		$StatoCartella='<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';
 						
echo '				<tr>
						<td>'.$CartellaUp.'</td>
						<td>'.$permessi.'</td>
						<td>'.$StatoCartella.'</td>
					</tr>
					</tbody>
				</table>
		</div>
		<h3>'.__("Permessi Cartella Servizio","albo-pretorio-considera").'</h3>
			<div class="widefat">
				<table style="width:99%;">
					<thead>
						<tr>
							<th style="text-align:left;width:380px;">'.__("Cartella","albo-pretorio-considera").'</th>
							<th style="text-align:left;width:100px;">'.__("Permessi","albo-pretorio-considera").'</th>
							<th style="text-align:left;width:100px;">'.__("Stato","albo-pretorio-considera").'</th>
						</tr>
					</thead>
					<tbody>';
$Cartella=AlboBCK;
$permessi=ap_get_fileperm($Cartella);		
$permProp=ap_get_fileperm_Gruppo($Cartella,"Proprietario");
if($permProp==7 Or $permProp==6 Or $permProp==3 Or $permProp==2)
 		$StatoCartella='<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		$StatoCartella='<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';
 						
echo '				<tr>
						<td>'.$Cartella.'</td>
						<td>'.$permessi.'</td>
						<td>'.$StatoCartella.'</td>
					</tr>';
$Cartella=AlboBCK.'/BackupDatiAlbo';
$permessi=ap_get_fileperm($Cartella);		
$permProp=ap_get_fileperm_Gruppo($Cartella,"Proprietario");
if($permProp==7 Or $permProp==6 Or $permProp==3 Or $permProp==2)
 		$StatoCartella='<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		$StatoCartella='<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';
 						
echo '				<tr>
						<td>'.$Cartella.'</td>
						<td>'.$permessi.'</td>
						<td>'.$StatoCartella.'</td>
					</tr>';
$Cartella=AlboBCK.'/OblioDatiAlbo';
$permessi=ap_get_fileperm($Cartella);		
$permProp=ap_get_fileperm_Gruppo($Cartella,"Proprietario");
if($permProp==7 Or $permProp==6 Or $permProp==3 Or $permProp==2)
 		$StatoCartella='<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		$StatoCartella='<span class="dashicons dashicons-no" style="color:red;font-size:2em;"></span>';
 						
echo '				<tr>
						<td>'.$Cartella.'</td>
						<td>'.$permessi.'</td>
						<td>'.$StatoCartella.'</td>
					</tr>
					</tbody>
				</table>
		</div>
		<div class="postbox-container" style="margin-top:20px;">
		<h3>'.__("Analisi Data Base","albo-pretorio-considera").'</h2>
	<div class="widefat">
		<table style="width:99%;">
			<thead>
				<tr>
					<th style="text-align:left;width:15%;">'.__("Tabella","albo-pretorio-considera").'</th>
					<th style="text-align:left;width:10%;">'.__("Esistenza","albo-pretorio-considera").'</th>
					<th style="text-align:left;width:25%;">'.__("Struttura","albo-pretorio-considera").'</th>
					<th style="text-align:left;width:50%;">'.__("Analisi dati","albo-pretorio-considera").'</th>
				</tr>
			</thead>
			<tbody>
';
foreach($Tabelle as $Tabella){
	$TestCampi="";
	if (ap_existTable($Tabella)) 
 		$EsisteTabella='<span class="dashicons dashicons-yes" style="color:#18b908;font-size:2em;"></span>';
	else
		$EsisteTabella='<a href="admin.php?page=utilityAlboP&action=creaTabella&Tabella='.$Tabella.'">'.__("Crea Tabella","albo-pretorio-considera").'</a>';

$TestCampi=TestCampiTabella($Tabella);
$DatiTabella=TestCongruitaDati($Tabella);
	echo'
					<tr class="first">
					<td>'.esc_html($Tabella).'</td>
					<td>'.$EsisteTabella.'</td>
					<td>'.$TestCampi.'</td>
					<td>'.$DatiTabella.'</td>
				</tr>
		';
	
}
echo'
			</tbody>
		</table>
	</div>
</div>';
}	
function AggiornaHashAllegati(){
	global $wpdb;
	$allegati=$wpdb->get_results("SELECT * FROM $wpdb->table_name_Allegati ;",ARRAY_A );
	foreach ( $allegati as $allegato) {
		if (is_file($allegato['Allegato'])){
			$Impronta=hash_file("sha256", $allegato['Allegato']);
			if ($wpdb->update($wpdb->table_name_Allegati,
				array('Impronta' => $Impronta),
				array('IdAllegato' => $allegato['IdAllegato'] ),
				array('%s'),
				array('%d'))) {
					echo'<spam style="color:green;">'.esc_html__('Aggiornamento Hash Allegato','albo-pretorio-considera').'</spam> '.esc_html($allegato['Allegato']).' <spam style="color:red;">'.esc_html($Impronta).'</spam><br />';
			}else{
				echo esc_html($allegato['Allegato']).' <spam style="color:red;">'.esc_html__('Allegato già aggiornato','albo-pretorio-considera').'</spam><br />';
			}
		}else{
			echo esc_html($allegato['Allegato']).' <spam style="color:red;">'.esc_html__('File non trovato','albo-pretorio-considera').'</spam><br />';
		}
	}
	echo '<meta http-equiv="refresh" content="4;url=admin.php?page=utilityAlboP"/>';
}		
?>
