<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Amministrazione richieste delle singole pagine.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

// require_once(ABSPATH . 'wp-includes/pluggable.php'); 
add_action( 'init', 'albo_post');

function DownloadFile($filename){
//	echo $filename."  ".basename($filename);die();
	$fn=basename($filename);
	if(!empty($fn)){
    // Check file is exists on given path.
    if(file_exists($filename))
    {
      header('Content-Disposition: attachment; filename=' . basename($filename));
      readfile($filename); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- streaming del file in download, non gestibile da WP_Filesystem
      exit;
    }
    else
    {
      wp_die(esc_html(basename($filename))." ".esc_html__("File non trovato","albo-pretorio-considera"));
    }
 }
}

function albo_post() {
	if(!is_user_logged_in())
		return;
	if(isset($_REQUEST['action'] )){
		switch ( $_REQUEST['action'] ) {
			case "ToCsv":
				if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'] ?? '')), 'repertorio_export' ) )
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") );
				$Anno=(isset($_REQUEST['Anno'])?intval($_REQUEST['Anno']):0);
				$Testata=preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Nome Ente","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Numero Atto","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Riferimento","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Oggetto","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Data Registrazione","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Data Inizio","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Data Fine","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Data Annullamento","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Motivo Annullamento","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Richiedente","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Unità Organizzativa Responsabile","albo-pretorio-considera")).";".
				         preg_replace ('/[^a-zA-Z0-9 -]/', "",__("Responsabile del procedimento amministrativo","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Categoria","albo-pretorio-considera")).";".
				         preg_replace ("/[^a-zA-Z0-9 -]/", "",__("Informazioni","albo-pretorio-considera")).";";
				$Atti="";
				$Righe=albopc_Repertorio($Anno,FALSE);
				foreach($Righe as $Riga){
					$Atti.=stripcslashes($Riga->NomeEnte).";";
					$Atti.=$Riga->Numero.";";
					$Atti.=!is_null($Riga->Riferimento)?str_replace("  "," ",preg_replace ("[.^A-Za-z0-9 ]", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->Riferimento),TRUE)))).";":";";
					$Atti.=!is_null($Riga->Oggetto)?str_replace("  "," ", preg_replace("[.^A-Za-z0-9 ]", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->Oggetto),TRUE)))).";":";";
					$Atti.=$Riga->DataRegistrazione.";";
					$Atti.=$Riga->DataInizio.";";
					$Atti.=$Riga->DataFine.";";
					$Atti.=$Riga->DataAnnullamento.";";
					$Atti.=!is_null($Riga->MotivoAnnullamento)?str_replace("  "," ",preg_replace ("[.^A-Za-z0-9 ]", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->MotivoAnnullamento),TRUE)))).";":";";	
					$Atti.=!is_null($Riga->Richiedente)?str_replace("  "," ",preg_replace ("[.^A-Za-z0-9 ]", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->Richiedente),TRUE)))).";":";";
					$Atti.=!is_null($Riga->UnitaOrganizzativa)?str_replace("  "," ",preg_replace ("/[^a-zA-Z0-9 -]/", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->UnitaOrganizzativa),TRUE)))).";":";";
					$Atti.=!is_null($Riga->ResponsabileProcedimento)?str_replace("  "," ",preg_replace ("/[^a-zA-Z0-9 -]/", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->ResponsabileProcedimento),TRUE)))).";":";";
					$Atti.=!is_null($Riga->Categoria)?str_replace("  "," ",preg_replace ("/[^a-zA-Z0-9 -]/", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->Categoria),TRUE)))).";":";";
					$Atti.=!is_null($Riga->Informazioni)?str_replace("  "," ",preg_replace ("/[^a-zA-Z0-9 -\/ :]/", "",stripslashes(wp_strip_all_tags(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $Riga->Informazioni),TRUE)))).";\n":";";
				}	
//				echo $Atti;die();
				$Dir=str_replace("\\","/",AP_BASE_DIR.'AlboOnLine/Repertori');
				if (!is_dir ( $Dir))
					if (!wp_mkdir_p($Dir)) 
						break;
				$file_path=$Dir."/repertorio_".$Anno.".csv";
	// phpcs:disable WordPress.WP.AlternativeFunctions -- I/O su file di export/download nelle cartelle di lavoro in wp-content/uploads: streaming e download non gestibili da WP_Filesystem.
				$file = fopen($file_path, "w") or die;
				fwrite($file, $Testata."\n".$Atti);
				fclose($file);
				DownloadFile($file_path);
				break;
			case "ToXML":
				if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'] ?? '')), 'repertorio_export' ) )
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") );
				$Anno=(isset($_REQUEST['Anno'])?intval($_REQUEST['Anno']):0);
				$xml=new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes" ?><'.preg_replace ('/[^a-zA-Z0-9]/', "_",__("Repertorio","albo-pretorio-considera")).'></'.preg_replace ('/[^a-zA-Z0-9]/', "_",__("Repertorio","albo-pretorio-considera")).'>');
				$MetaData=$xml->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Meta dati","albo-pretorio-considera")));
				$MetaData->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Anno","albo-pretorio-considera")),$Anno);
				$Righe=albopc_Repertorio($Anno,FALSE);
				$Atti=$xml->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Atti","albo-pretorio-considera")));
				foreach($Righe as $Riga){
					$Atto=$Atti->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Atto","albo-pretorio-considera")));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Nome Ente","albo-pretorio-considera")), albopc_sanifica_testo($Riga->NomeEnte));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Numero Atto","albo-pretorio-considera")), albopc_sanifica_testo($Riga->Numero));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Riferimento","albo-pretorio-considera")), albopc_sanifica_testo($Riga->Riferimento));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Oggetto","albo-pretorio-considera")), albopc_sanifica_testo($Riga->Oggetto));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Data di registrazione","albo-pretorio-considera")), albopc_sanifica_testo($Riga->DataRegistrazione));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Data Inizio","albo-pretorio-considera")), albopc_sanifica_testo($Riga->DataInizio));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Data Fine","albo-pretorio-considera")), albopc_sanifica_testo($Riga->DataFine));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Data Annullamento","albo-pretorio-considera")),albopc_sanifica_testo($Riga->DataAnnullamento));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Motivo Annullamento","albo-pretorio-considera")),albopc_sanifica_testo($Riga->MotivoAnnullamento));					
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Richiedente","albo-pretorio-considera")),albopc_sanifica_testo($Riga->Richiedente));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Unita Organizzativa Responsabile","albo-pretorio-considera")),albopc_sanifica_testo($Riga->UnitaOrganizzativa));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Responsabile del procedimento amministrativo","albo-pretorio-considera")),albopc_sanifica_testo($Riga->ResponsabileProcedimento));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Categoria","albo-pretorio-considera")),albopc_sanifica_testo($Riga->Categoria));
					$riga=$Atto->addChild(preg_replace ('/[^a-zA-Z0-9]/', "_",__("Informazioni","albo-pretorio-considera")),albopc_sanifica_testo($Riga->Informazioni));
				}	
				$Dir=str_replace("\\","/",AP_BASE_DIR.'AlboOnLine/Repertori');
				if (!is_dir ( $Dir))
					if (!wp_mkdir_p($Dir)) 
						break;
				$file_path=$Dir."/repertorio_".$Anno.".xml";
				$file = fopen($file_path, "w") or die;
				fwrite($file, $xml->asXML());
				fclose($file);
				DownloadFile($file_path);
				break;
			case "ToJson":
				if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'] ?? '')), 'repertorio_export' ) )
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") );
				$Anno=(isset($_REQUEST['Anno'])?intval($_REQUEST['Anno']):0);
				$Repertorio=albopc_Repertorio($Anno,FALSE);
				$Dir=str_replace("\\","/",AP_BASE_DIR.'AlboOnLine/Repertori');
				if (!is_dir ( $Dir))
					if (!wp_mkdir_p($Dir)) 
						break;
				$file_path=$Dir."/repertorio_".$Anno.".json";
				$file = fopen($file_path, "w") or die;
				$txt = json_encode($Repertorio);
				fwrite($file, $txt);
				fclose($file);
	// phpcs:enable WordPress.WP.AlternativeFunctions
				DownloadFile($file_path);
				break;
/*			case "ToPdf":
				if (isset($_GET['Anno']))
					$AnnoRepertorio=$_GET['Anno'];
				else
					$AnnoRepertorio=gmdate("Y");
				$ToPdf= new albopc_cls_Repertorio("Portrait","mm","A4");
				$ToPdf->ToTable($AnnoRepertorio);
				break;			
*/			case "delete_bulk_atti":
		        if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
	            	$nonce  = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	            	$action = 'bulk-atti' ;
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		                wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") );
		        }else{
		        	wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") );
		        }
			 	$Msg=albopc_oblio_atti((isset($_GET['IdAtto'])?intval($_GET['IdAtto']):0));
			 	$location = "?page=atti&stato_atti=Eliminare&message=".urlencode($Msg);
				wp_safe_redirect( $location );
				break;
 			case "avviso_affissione-atto":
				if ( isset( $_GET['avvisoatto'] ) && ! empty( $_GET['avvisoatto'] ) ) {
		            $nonce  = filter_input( INPUT_GET, 'avvisoatto', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		            $action = 'operazioneavviso_affissione';
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		                wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti&stato_atti=Correnti") );
			 		$location = "?page=atti&stato_atti=Correnti" ;
			 		include ('stampe.php');
			 		if (is_numeric(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')))) {
 	                    StampaAtto((isset($_REQUEST['id'])?intval($_REQUEST['id']):0), 'a');
					}
					wp_die();
				}else
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti") );					
			break;
 			case "certificato_pubblicazione-atto":
				if ( isset( $_GET['certificatoatto'] ) && ! empty( $_GET['certificatoatto'] ) ) {
		            $nonce  = filter_input( INPUT_GET, 'certificatoatto', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		            $action = 'operazionecertificato_pubblicazione';
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		                wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti&stato_atti=Correnti") );
			 		$location = "?page=atti&stato_atti=Correnti" ;
			 		include ('stampe.php');
			 		if (is_numeric(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')))) {
 	                    StampaAtto((isset($_REQUEST['id'])?intval($_REQUEST['id']):0), 'c');
					}
					wp_die();
				}else
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti") );					
			break;
 			case "oblia-atto":
				if ( isset( $_GET['oatto'] ) && ! empty( $_GET['oatto'] ) ) {
		            $nonce  = filter_input( INPUT_GET, 'oatto', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		            $action = 'operazionebliaatto';
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		                wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti&stato_atti=Correnti") );
			 		$location = "?page=atti&stato_atti=Scaduti" ;
			 		if (is_numeric(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')))) {
 	                    $MessaggiRitorno=albopc_setOblioOggi((isset($_REQUEST['id'])?intval($_REQUEST['id']):0));
					}
					$location = add_query_arg( 'message',$MessaggiRitorno, $location );
					wp_safe_redirect( $location );
				}else
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti") );					
			break;	
 
 			case "elimina-atto":
				if ( isset( $_GET['cancellatto'] ) && ! empty( $_GET['cancellatto'] ) ) {
		            $nonce  = filter_input( INPUT_GET, 'cancellatto', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		            $action = 'operazionecancelaatto';
		            if ( ! wp_verify_nonce( $nonce, $action ) )
		               wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti") );
			 		$location = "?page=atti&stato_atti=Eliminare" ;
			 		$MessaggiRitorno=albopc_oblio_atti((isset($_GET['id'])?intval($_GET['id']):0));
					$location = add_query_arg( 'message',$MessaggiRitorno["Message"], $location );
					$location = add_query_arg( 'message2',$MessaggiRitorno["Message2"], $location );
					wp_safe_redirect( $location );
				}else
					wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") ,esc_html__("Problemi di sicurezza","albo-pretorio-considera"),array("back_link" => "?page=atti") );					
			break;
		case "annulla-atto":
			if (!isset($_REQUEST['annatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['annatto'] ?? '')),'annatto')){
				Go_Atti();
				break;
			} 
			if (filter_input(INPUT_POST,'submit')== 'Annulla Pubblicazione Atto'){
				if (sanitize_text_field(wp_unslash($_REQUEST['Motivo'] ?? '')) == "null") {
					$NumMsg=8;
				}else{
					$Allegati=array();
					foreach($_REQUEST as $Parametro=>$ValoreParametro){
						if(substr($Parametro,0,5)=="Alle:")
							$Allegati[]=$ValoreParametro;
					}
					$Risultato=albopc_annulla_atto((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),sanitize_textarea_field(wp_unslash($_REQUEST['Motivo'] ?? '')),$Allegati);
				}				
			}else{
				$Risultato=wp_die( esc_html__("Operazione Annullata","albo-pretorio-considera"));
			}
	 		$location = "?page=atti&stato_atti=Correnti" ;
			$location = add_query_arg( 'message', $Risultato, $location );
			wp_safe_redirect( $location );
			break;
		case "ExportBackupData":
			if (!isset($_REQUEST['exportbckdata'])) {
				Go_Utility();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['exportbckdata'] ?? '')),'EsportaBackupDatiAlbo')){
				Go_Utility();
				break;
			} 		
			DownloadFile(WP_CONTENT_DIR ."/AlboOnLine/BackupDatiAlbo/".basename(sanitize_text_field(wp_unslash($_REQUEST['elenco_Backup_Expo'] ?? ''))));
			break;
		case "delete-allegato-atto" :
			if ( ! isset( $_REQUEST['cancellaallegatoatto'] ) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cancellaallegatoatto'] ?? '')), 'deleteallegatoatto' ) )
				wp_die( esc_html__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera") );
			$location = "?page=atti" ;
			albopc_del_allegato_atto((isset($_REQUEST['idAllegato'])?intval($_REQUEST['idAllegato']):0),(isset($_REQUEST['idAtto'])?intval($_REQUEST['idAtto']):0),intval(htmlentities(sanitize_text_field(wp_unslash($_REQUEST['Allegato'] ?? '')))));
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('action'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('idAllegato'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('Allegato'), isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '');
			$location= add_query_arg( array ( 'action' => 'allegati-atto', 
									          'id' => (isset($_REQUEST['idAtto'])?intval($_REQUEST['idAtto']):0),
									          'allegatoatto'=>wp_create_nonce('gestallegatiatto')));
			wp_safe_redirect( $location );
			break;
		case 'add-responsabile':
			if (!isset($_REQUEST['responsabili'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['responsabili'] ?? '')),'elabresponsabili')){
				Go_Responsabili();
				break;
			} 	
			$location = "?page=soggetti" ;
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['resp-email'] ?? ''))) or sanitize_text_field(wp_unslash($_POST['resp-cognome'] ?? '')) == ''){
				$location = add_query_arg( 'errore', !is_email(sanitize_text_field(wp_unslash($_REQUEST['resp-email'] ?? ''))) ? 'Email non valida': "Bisogna valorizzare il Cognome del Responsabile", $location );
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg('resp-cognome', sanitize_text_field(wp_unslash($_POST['resp-cognome'] ?? '')), $location );
				$location = add_query_arg('resp-nome', sanitize_text_field(wp_unslash($_POST['resp-nome'] ?? '')), $location );
				$location = add_query_arg('resp-funzione', sanitize_text_field(wp_unslash($_POST['resp-funzione'] ?? '')), $location );
				$location = add_query_arg('resp-email', sanitize_text_field(wp_unslash($_POST['resp-email'] ?? '')), $location );
				$location = add_query_arg('resp-telefono', sanitize_text_field(wp_unslash($_POST['resp-telefono'] ?? '')), $location );
				$location = add_query_arg('resp-orario', sanitize_text_field(wp_unslash($_POST['resp-orario'] ?? '')), $location );
				$location = add_query_arg('resp-note', sanitize_text_field(wp_unslash($_POST['resp-note'] ?? '')), $location );
				$location = add_query_arg( 'action', 'add', $location );
			}
			else{
				$ret=albopc_insert_responsabile(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['resp-cognome'] ?? ''))),
											albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['resp-nome'] ?? ''))),
											albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['resp-funzione'] ?? ''))),
											albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['resp-email'] ?? ''))),
											albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['resp-telefono'] ?? ''))),
											albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['resp-orario'] ?? ''))),
											sanitize_textarea_field(wp_unslash($_POST['resp-note'] ?? '')));
				if ( !$ret && !is_wp_error( $ret ) )
					$location = add_query_arg( 'message', 1, $location );
				else
					$location = add_query_arg( 'message', 4, $location );
			}
			wp_safe_redirect( $location );
			break;
		case 'edit-responsabile':
			if (!isset($_REQUEST['modresp'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modresp'] ?? '')),'editresponsabile')){
				Go_Responsabili();
				break;
			} 		
			$location = "?page=soggetti" ;
			$location = add_query_arg( 'id', (isset($_GET['id'])?intval($_GET['id']):0), $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_safe_redirect( $location );
			break;
		case 'edit-tipofile':
			if (!isset($_REQUEST['modtipfil'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modtipfil'] ?? '')),'edittipofilee')){
				Go_Responsabili();
				break;
			} 		
			$location = "?page=tipifiles" ;
			$location = add_query_arg( 'id', albopc_sanifica_testo(sanitize_text_field(wp_unslash($_GET['id'] ?? ''))), $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_safe_redirect( $location );
			break;			
		case 'set-default':
//			var_dump($_REQUEST);die();
			if (!isset($_REQUEST['tipifiles'])) {
				Go_TipiFiles();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['tipifiles'] ?? '')),'elabtipifiles')){
				Go_TipiFiles();
				break;
			} 		
			$location = "?page=tipifiles" ;
			$TipidiFiles=array();
			$TipidiFiles["ndf"]= array("Descrizione"=>__('Tipo file non definito','albo-pretorio-considera'),"Icona"=>Albo_URL."img/notipofile.png","Verifica"=>"");
			$TipidiFiles["pdf"]= array("Descrizione"=>__('File Pdf','albo-pretorio-considera'),"Icona"=>Albo_URL."img/Pdf.png","Verifica"=>"");
			$TipidiFiles["p7m"]= array("Descrizione"=>__('File firmato digitalmente','albo-pretorio-considera'),"Icona"=>Albo_URL."img/firmato.png","Verifica"=>htmlspecialchars("<a href=\"http://vol.ca.notariato.it/\" onclick=\"window.open(this.href);return false;\">".__('Verifica firma con servizio fornito da Consiglio Nazionale del Notariato','albo-pretorio-considera')."</a>"));
			update_option('opt_AP_TipidiFiles', $TipidiFiles);
			wp_safe_redirect( $location );
			break;			
		case 'delete-tipofile':
//			var_dump($_REQUEST);die();
			if (!isset($_REQUEST['canctipfil'])) {
				Go_TipiFiles();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['canctipfil'] ?? '')),'deletetipofile')){
				Go_TipiFiles();
				break;
			} 		
			$location = "?page=tipifiles" ;
			if(albopc_delete_tipofiles(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? ''))))){
				$location = add_query_arg( 'message', 2, $location );
			}else{
				$location = add_query_arg( 'message', 6, $location );
			}
			wp_safe_redirect( $location );
			break;	
		case 'add-tipofile':
//			var_dump($_REQUEST);die();
			if (!isset($_REQUEST['tipifiles'])) {
				Go_TipiFiles();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['tipifiles'] ?? '')),'elabtipifiles')){
				Go_TipiFiles();
				break;
			} 	
			$location = "?page=tipifiles" ;
			if((!isset($_REQUEST['id']) OR sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')) == '') OR
			   (!isset($_REQUEST['descrizione']) OR sanitize_text_field(wp_unslash($_REQUEST['descrizione'] ?? '')) == '') OR 
			   (!isset($_REQUEST['icona']) OR sanitize_text_field(wp_unslash($_REQUEST['icona'] ?? '')) == '')){
				$location = add_query_arg( 'message', 8, $location );
			}else{
				if(albopc_add_tipofiles(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['descrizione'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['icona'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['verifica'] ?? ''))))){
					$location = add_query_arg( 'message', 1, $location );
				}else{
					$location = add_query_arg( 'message', 4, $location );
				}				
			}
			wp_safe_redirect( $location );
			break;	
		case 'memo-tipofile':
			if (!isset($_REQUEST['tipifiles'])) {
				Go_TipiFiles();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['tipifiles'] ?? '')),'elabtipifiles')){
				Go_TipiFiles();
				break;
			} 		
//			var_dump($_REQUEST);die();
			$location = "?page=tipifiles" ;
			if (!isset( $_REQUEST['id'] )){
				$location = add_query_arg( 'errore', __('Tipo file non definito','albo-pretorio-considera'), $location );
				$location = add_query_arg( 'message', 5, $location );
				$location = add_query_arg('Descrizione', sanitize_text_field(wp_unslash($_REQUEST['descrizione'] ?? '')), $location );
				$location = add_query_arg('Icona', sanitize_text_field(wp_unslash($_REQUEST['icona'] ?? '')), $location );
				$location = add_query_arg('Verifica', sanitize_text_field(wp_unslash($_REQUEST['verifica'] ?? '')), $location );
				$location = add_query_arg( 'action', 'edit_err', $location );
				$location = add_query_arg('id', sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')), $location );
			}else
				if (albopc_memo_tipofiles(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['id'] ?? ''))),albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['descrizione'] ?? ''))),albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['icona'] ?? ''))),albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['verifica'] ?? '')))))
					$location = add_query_arg( 'message', 3, $location );
				else
					$location = add_query_arg( 'message', 5, $location );
			wp_safe_redirect( $location );
			break;
		case 'memo-responsabile':
			if (!isset($_REQUEST['responsabili'])) {
				Go_Responsabili();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['responsabili'] ?? '')),'elabresponsabili')){
				Go_Responsabili();
				break;
			} 		
			$location = "?page=soggetti" ;
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['resp-email'] ?? '')))){
				$location = add_query_arg( 'errore', __('Email non valida','albo-pretorio-considera'), $location );
				$location = add_query_arg( 'message', 5, $location );
				$location = add_query_arg('resp-cognome', sanitize_text_field(wp_unslash($_REQUEST['resp-cognome'] ?? '')), $location );
				$location = add_query_arg('resp-nome', sanitize_text_field(wp_unslash($_REQUEST['resp-nome'] ?? '')), $location );
				$location = add_query_arg('resp-funzione', sanitize_text_field(wp_unslash($_REQUEST['resp-funzione'] ?? '')), $location );				
				$location = add_query_arg('resp-email', sanitize_text_field(wp_unslash($_REQUEST['resp-email'] ?? '')), $location );
				$location = add_query_arg('resp-telefono', sanitize_text_field(wp_unslash($_REQUEST['resp-telefono'] ?? '')), $location );
				$location = add_query_arg('resp-orario', sanitize_text_field(wp_unslash($_REQUEST['resp-orario'] ?? '')), $location );
				$location = add_query_arg('resp-note', sanitize_text_field(wp_unslash($_REQUEST['resp-note'] ?? '')), $location );
				$location = add_query_arg( 'action', 'edit_err', $location );
				$location = add_query_arg( 'id', (isset($_REQUEST['id'])?(int)$_REQUEST['id']:0), $location );
			}
			else
				if (!is_wp_error(albopc_memo_responsabile((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
														albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['resp-cognome'] ?? ''))),
														albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['resp-nome'] ?? ''))),
														albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['resp-funzione'] ?? ''))),
														albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['resp-email'] ?? ''))),
														albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['resp-telefono'] ?? ''))),
														albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['resp-orario'] ?? ''))),
														albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['resp-note'] ?? ''))))))
					$location = add_query_arg( 'message', 3, $location );
				else
					$location = add_query_arg( 'message', 5, $location );
	//		global $wpdb;
	//		echo $wpdb->last_query;exit; 
			wp_safe_redirect( $location );
			break;
		case 'delete-ente':
			if (!isset($_REQUEST['cancellaente'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cancellaente'] ?? '')),'deleteente')){
				Go_Enti();
				break;
			} 			
			$location = "?page=enti" ;
			$res=albopc_del_ente((isset($_GET['id'])?intval($_GET['id']):0));
			if (!is_array($res))
				$location = add_query_arg( 'message', 2, $location );
			else{
				if ($res['atti']>0)
					$location = add_query_arg( 'message', 7, $location );
				else
					$location = add_query_arg( 'message', 6, $location );
			}
			wp_safe_redirect( $location );
			break;
		case 'add-ente':
			if (!isset($_REQUEST['enti'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['enti'] ?? '')),'enti')){
				Go_Enti();
				break;
			} 		
			$location = "?page=enti" ;
			$errore="";
			if (sanitize_text_field(wp_unslash($_REQUEST['ente-nome'] ?? '')) == '') $errore.=__("Bisogna valorizzare il Nome dell'Ente",'albo-pretorio-considera')." <br />";
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['ente-email'] ?? '')))) $errore.=__('Email non valida','albo-pretorio-considera')." <br />"; 
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['ente-pec'] ?? '')))) $errore.="Pec non valida <br />"; 
			if (strlen($errore)>0){
				$location = add_query_arg( 'errore', $errore, $location );
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg('ente-nome', sanitize_text_field(wp_unslash($_REQUEST['ente-nome'] ?? '')), $location );
				$location = add_query_arg('ente-indirizzo', sanitize_text_field(wp_unslash($_REQUEST['ente-indirizzo'] ?? '')), $location );
				$location = add_query_arg('ente-url', sanitize_text_field(wp_unslash($_REQUEST['ente-url'] ?? '')), $location );
				$location = add_query_arg('ente-email', sanitize_text_field(wp_unslash($_REQUEST['ente-email'] ?? '')), $location );
				$location = add_query_arg('ente-pec', sanitize_text_field(wp_unslash($_REQUEST['ente-pec'] ?? '')), $location );
				$location = add_query_arg('ente-telefono', sanitize_text_field(wp_unslash($_REQUEST['ente-telefono'] ?? '')), $location );
				$location = add_query_arg('ente-fax', sanitize_text_field(wp_unslash($_REQUEST['ente-fax'] ?? '')), $location );
				$location = add_query_arg('ente-note', sanitize_text_field(wp_unslash($_REQUEST['ente-note'] ?? '')), $location );
				$location = add_query_arg( 'action', 'add', $location );
			}
			else{
				$ret=albopc_insert_ente(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-nome'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-indirizzo'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-url'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-email'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-pec'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-telefono'] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-fax'] ?? ''))),
									albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['ente-note'] ?? ''))));
				if ( !$ret && !is_wp_error( $ret ) )
					$location = add_query_arg( 'message', 1, $location );
				else
					$location = add_query_arg( 'message', 4, $location );
			}
			wp_safe_redirect( $location );
			break;
		case 'edit-ente':
			if (!isset($_REQUEST['modificaente'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modificaente'] ?? '')),'editente')){
				Go_Enti();
				break;
			} 		
			$location = "?page=enti" ;
			$location = add_query_arg( 'id', (isset($_GET['id'])?intval($_GET['id']):0), $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_safe_redirect( $location );
			break;
		case 'memo-ente':
			if (!isset($_REQUEST['enti'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['enti'] ?? '')),'enti')){
				Go_Enti();
				break;
			} 			
			$location = "?page=enti" ;
			$errore="";
			if (sanitize_text_field(wp_unslash($_REQUEST['ente-nome'] ?? '')) == '') $errore.=__("Bisogna valorizzare il Nome dell'Ente",'albo-pretorio-considera')." ";
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['ente-email'] ?? '')))) $errore.=__('Email non valida','albo-pretorio-considera')." "; 
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['ente-pec'] ?? '')))) $errore.=__('PEC non valida','albo-pretorio-considera')." "; 
			if (strlen($errore)>0){
				$location = add_query_arg( 'errore', $errore, $location );
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg('ente-nome', sanitize_text_field(wp_unslash($_REQUEST['ente-nome'] ?? '')), $location );
				$location = add_query_arg('ente-indirizzo', sanitize_text_field(wp_unslash($_REQUEST['ente-indirizzo'] ?? '')), $location );
				$location = add_query_arg('ente-url', sanitize_text_field(wp_unslash($_REQUEST['ente-url'] ?? '')), $location );
				$location = add_query_arg('ente-email', sanitize_text_field(wp_unslash($_REQUEST['ente-email'] ?? '')), $location );
				$location = add_query_arg('ente-pec', sanitize_text_field(wp_unslash($_REQUEST['ente-pec'] ?? '')), $location );
				$location = add_query_arg('ente-telefono', sanitize_text_field(wp_unslash($_REQUEST['ente-telefono'] ?? '')), $location );
				$location = add_query_arg('ente-fax', sanitize_text_field(wp_unslash($_REQUEST['ente-fax'] ?? '')), $location );
				$location = add_query_arg('ente-note', sanitize_text_field(wp_unslash($_REQUEST['ente-note'] ?? '')), $location );
				$location = add_query_arg('action', sanitize_text_field(wp_unslash($_REQUEST['action2'] ?? '')), $location );
				$location = add_query_arg('id', sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')), $location );
			}
			else
				if (!is_wp_error(albopc_memo_ente((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-nome'] ?? ''))),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-indirizzo'] ?? ''))),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-url'] ?? ''))),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-email'] ?? ''))),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-pec'] ?? ''))),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-telefono'] ?? ''))),
										albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['ente-fax'] ?? ''))),
									    sanitize_textarea_field(wp_unslash($_REQUEST['ente-note'] ?? '')))))
					$location = add_query_arg( 'message', 3, $location );
				else
					$location = add_query_arg( 'message', 5, $location );
	//		global $wpdb;
	//		echo $wpdb->last_query;exit; 
			wp_safe_redirect( $location );
			break;
		case 'delete-unitao':
			if (!isset($_REQUEST['cancellaunitao'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cancellaunitao'] ?? '')),'deleteunitao')){
				Go_Enti();
				break;
			} 			
			$location = "?page=unitao" ;
			$res=albopc_del_unitao((isset($_GET['id'])?intval($_GET['id']):0));
			if (!is_array($res))
				$location = add_query_arg( 'message', 2, $location );
			else{
				if ($res['unitao']>0)
					$location = add_query_arg( 'message', 7, $location );
				else
					$location = add_query_arg( 'message', 6, $location );
			}
			wp_safe_redirect( $location );
			break;
		case 'add-unitao':
			if (!isset($_REQUEST['unitao'])) {
				Go_Enti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['unitao'] ?? '')),'unitao')){
				Go_Enti();
				break;
			} 		
			$location = "?page=unitao" ;
			$errore="";
			if (sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'] ?? '')) == '') $errore.=__("Bisogna valorizzare il Nome dell'Unità Organizzativa",'albo-pretorio-considera')." <br />";
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['unitao-email'] ?? '')))) $errore.=__('Email non valida','albo-pretorio-considera')." <br />"; 
			if (strlen($errore)>0){
				$location = add_query_arg( 'errore', $errore, $location );
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg('unitao-nome', sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'] ?? '')), $location );
				$location = add_query_arg('unitao-indirizzo', sanitize_text_field(wp_unslash($_REQUEST['unitao-indirizzo'] ?? '')), $location );
				$location = add_query_arg('unitao-url', sanitize_text_field(wp_unslash($_REQUEST['unitao-url'] ?? '')), $location );
				$location = add_query_arg('unitao-email', sanitize_text_field(wp_unslash($_REQUEST['unitao-email'] ?? '')), $location );
				$location = add_query_arg('unitao-pec', sanitize_text_field(wp_unslash($_REQUEST['unitao-pec'] ?? '')), $location );
				$location = add_query_arg('unitao-telefono', sanitize_text_field(wp_unslash($_REQUEST['unitao-telefono'] ?? '')), $location );
				$location = add_query_arg('unitao-fax', sanitize_text_field(wp_unslash($_REQUEST['unitao-fax'] ?? '')), $location );
				$location = add_query_arg('unitao-note', sanitize_text_field(wp_unslash($_REQUEST['unitao-note'] ?? '')), $location );
				$location = add_query_arg( 'action', 'add', $location );
			}
			else{
				$ret=albopc_insert_unitao(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'] ?? ''))),
									  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-indirizzo'] ?? ''))),
									  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-url'] ?? ''))),
									  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-email'] ?? ''))),
									  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-pec'] ?? ''))),
									  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-telefono'] ?? ''))),
									  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-fax'] ?? ''))),
									  albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['unitao-note'] ?? ''))));
				if ( !$ret && !is_wp_error( $ret ) )
					$location = add_query_arg( 'message', 1, $location );
				else
					$location = add_query_arg( 'message', 4, $location );
			}
			wp_safe_redirect( $location );
			break;
		case 'edit-unitao':
			if (!isset($_REQUEST['modificaunitao'])) {
				Go_Unitao();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modificaunitao'] ?? '')),'ediunitao')){
				Go_Unitao();
				break;
			} 		
			$location = "?page=unitao" ;
			$location = add_query_arg( 'id', (isset($_GET['id'])?intval($_GET['id']):0), $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_safe_redirect( $location );
			break;
		case 'memo-unitao':
			if (!isset($_REQUEST['unitao'])) {
				Go_Unitao();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['unitao'] ?? '')),'unitao')){
				Go_Unitao();
				break;
			} 			
			$location = "?page=unitao" ;
			$errore="";
			if (sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'] ?? '')) == '') $errore.=__("Bisogna valorizzare il Nome dell'Unità Organizzativa",'albo-pretorio-considera')." ";
			if (!is_email(sanitize_text_field(wp_unslash($_REQUEST['unitao-email'] ?? '')))) $errore.=__('Email non valida','albo-pretorio-considera')." "; 
			if (strlen($errore)>0){
				$location = add_query_arg( 'errore', $errore, $location );
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg('unitao-nome', sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'] ?? '')), $location );
				$location = add_query_arg('unitao-indirizzo', sanitize_text_field(wp_unslash($_REQUEST['unitao-indirizzo'] ?? '')), $location );
				$location = add_query_arg('unitao-url', sanitize_text_field(wp_unslash($_REQUEST['unitao-url'] ?? '')), $location );
				$location = add_query_arg('unitao-email', sanitize_text_field(wp_unslash($_REQUEST['unitao-email'] ?? '')), $location );
				$location = add_query_arg('unitao-pec', sanitize_text_field(wp_unslash($_REQUEST['unitao-pec'] ?? '')), $location );
				$location = add_query_arg('unitao-telefono', sanitize_text_field(wp_unslash($_REQUEST['unitao-telefono'] ?? '')), $location );
				$location = add_query_arg('unitao-fax', sanitize_text_field(wp_unslash($_REQUEST['unitao-fax'] ?? '')), $location );
				$location = add_query_arg('unitao-note', sanitize_text_field(wp_unslash($_REQUEST['unitao-note'] ?? '')), $location );
				$location = add_query_arg('action', sanitize_text_field(wp_unslash($_REQUEST['action2'] ?? '')), $location );
				$location = add_query_arg('id', sanitize_text_field(wp_unslash($_REQUEST['id'] ?? '')), $location );
			}
			else
				if (!is_wp_error(albopc_memo_unitao((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-nome'] ?? ''))),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-indirizzo'] ?? ''))),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-url'] ?? ''))),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-email'] ?? ''))),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-pec'] ?? ''))),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-telefono'] ?? ''))),
								 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['unitao-fax'] ?? ''))),
								 albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['unitao-note'] ?? ''))))))
					$location = add_query_arg( 'message', 3, $location );
				else
					$location = add_query_arg( 'message', 5, $location );
	//		global $wpdb;
	//		echo $wpdb->last_query;exit; 
			wp_safe_redirect( $location );
			break;
		case 'add-categorie':
			if (!isset($_POST['categoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['categoria'] ?? '')),'categoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			if (sanitize_text_field(wp_unslash($_POST['cat-name'] ?? '')) == '')
				$location = add_query_arg( 'message', 9, $location );
			else{
				$ret=albopc_insert_categoria(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['cat-name'] ?? ''))),
										 (isset($_POST['cat-parente'])?intval($_POST['cat-parente']):0),
										 albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_POST['cat-descrizione'] ?? ''))),
										 (isset($_POST['cat-durata'])?intval($_POST['cat-durata']):0));
				if ( !$ret && !is_wp_error( $ret ) )
					$location = add_query_arg( 'message', 1, $location );
				else
					$location = add_query_arg( 'message', 4, $location );
			}			
			wp_safe_redirect( $location );
			break;
		case 'delete-categorie':
			if (!isset($_GET['canccategoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['canccategoria'] ?? '')),'delcategoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			$res=albopc_del_categorie((isset($_GET['id'])?intval($_GET['id']):0));
			if (!is_array($res))
				$location = add_query_arg( 'message', 2, $location );
			else{
				if ($res['atti']>0) {
					$location = add_query_arg( 'message', 8, $location );
				}else{
					if ($res['figli']>0) {
						$location = add_query_arg( 'message', 7, $location );
					}
				}
			}
			wp_safe_redirect( $location );
			break;
		case 'edit-categorie':
			if (!isset($_GET['modcategoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['modcategoria'] ?? '')),'editcategoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			$location = add_query_arg( 'id', (isset($_GET['id'])?intval($_GET['id']):0), $location );
			$location = add_query_arg( 'action', 'edit', $location );
			wp_safe_redirect( $location );
			break;
		case 'memo-categoria':
			if (!isset($_POST['categoria'])) {
				Go_Categorie();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['categoria'] ?? '')),'categoria')){
				Go_Categorie();
				break;
			} 		
			$location = "?page=categorie" ;
			if (!is_wp_error( albopc_memo_categorie((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
												albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['cat-name'] ?? ''))),
												(isset($_REQUEST['cat-parente'])?intval($_REQUEST['cat-parente']):0),
												albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_REQUEST['cat-descrizione'] ?? ''))),
												(isset($_REQUEST['cat-durata'])?intval($_REQUEST['cat-durata']):0))))
				$location = add_query_arg( 'message', 3, $location );
			else
				$location = add_query_arg( 'message', 5, $location );
			wp_safe_redirect( $location );
			break;
	 	case "delete-atto":
			if (!isset($_GET['cancellaatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['cancellaatto'] ?? '')),'deleteatto')){
				Go_Atti();
				break;
			} 		
			$location = "?page=atti&stato_atti=Nuovi" ;
			if(albopc_del_allegati_atto((isset($_GET['id'])?intval($_GET['id']):0)))
				$location = add_query_arg( 'message2',10, $location );
			else
				$location = add_query_arg( 'message2',11, $location );
			$res=albopc_del_atto((isset($_GET['id'])?intval($_GET['id']):0));
			if (!is_array($res))
				$location = add_query_arg( 'message', 2, $location );
			else{
				if ($res['allegati']>0) {
					$location = add_query_arg( 'message', 7, $location );
				}else
					$location = add_query_arg( 'message', 6, $location );
			}
			wp_safe_redirect( $location );
			break;
		case "add-atto" :
			if (!isset($_POST['nuovoatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nuovoatto'] ?? '')),'nuovoatto')){
				Go_Atti();
				break;
			} 
			if(isset($_POST['Soggetto'])){
				$Soggetti=serialize((isset($_POST['Soggetto']) && is_array($_POST['Soggetto'])) ? array_map('sanitize_text_field', wp_unslash($_POST['Soggetto'])) : array());
			}else{
				$Soggetti=serialize(array());
			}
			$location = "?page=atti&stato_atti=Nuovi" ;
			$NewIDAtto=albopc_insert_atto(sanitize_text_field(wp_unslash($_POST['Ente'] ?? '')),
								albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['Data'] ?? ''))),
			                    albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['Riferimento'] ?? ''))),
								albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_POST['Oggetto'] ?? ''))),
								albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['DataInizio'] ?? ''))),
								albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['DataFine'] ?? ''))),
								albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['DataOblio'] ?? ''))),
								albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_POST['Note'] ?? ''))),
								(isset($_POST['Categoria'])?intval($_POST['Categoria']):0),
								(isset($_POST['Responsabile'])?intval($_POST['Responsabile']):0),
								$Soggetti,
								(isset($_POST['Unitao'])?intval($_POST['Unitao']):0),
								sanitize_text_field(wp_unslash($_POST['Richiedente'] ?? '')));
			if ( is_numeric( $NewIDAtto ))
				$location = add_query_arg( 'message', 1, $location );
			else{
				$location = add_query_arg( 'message', 4, $location );
				$location = add_query_arg( 'errore', $ret , $location );		
			}
			if(is_numeric( $NewIDAtto ) And (isset($_REQUEST['newMetaName']) && is_array($_REQUEST['newMetaName']))){
				for($i=0;$i<count(is_array($_REQUEST['newMetaName']) ? wp_unslash($_REQUEST['newMetaName']) : array());$i++){
					albopc_add_attimeta($NewIDAtto,
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['newMetaName'][$i] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['newValue'][$i] ?? ''))));
				}				
			}
			wp_safe_redirect( $location );
			break;
		case "memo-atto" :
			if (!isset($_REQUEST['modificaatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modificaatto'] ?? '')),'editatto')){
				Go_Atti();
				break;
			}	
			if(isset($_POST['Soggetto'])){
				$Soggetti=serialize((isset($_POST['Soggetto']) && is_array($_POST['Soggetto'])) ? array_map('sanitize_text_field', wp_unslash($_POST['Soggetto'])) : array());
			}else{
				$Soggetti=serialize(array());
			}
			$location = "?page=atti&stato_atti=Nuovi" ;
			$ret=albopc_memo_atto((isset($_REQUEST['id'])?(int)$_REQUEST['id']:0),
							  sanitize_text_field(wp_unslash($_REQUEST['Ente'] ?? '')),
							  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['Data'] ?? ''))),
							  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['Riferimento'] ?? ''))),
							  albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_POST['Oggetto'] ?? ''))),
							  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['DataInizio'] ?? ''))),
							  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['DataFine'] ?? ''))),
							  albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['DataOblio'] ?? ''))),
							  albopc_sanifica_areatesto(sanitize_textarea_field(wp_unslash($_POST['Note'] ?? ''))),
							  (isset($_POST['Categoria'])?intval($_POST['Categoria']):0),
							  (isset($_POST['Responsabile'])?intval($_POST['Responsabile']):0),
							  $Soggetti,
							  (isset($_POST['Unitao'])?intval($_POST['Unitao']):0),
							  sanitize_text_field(wp_unslash($_POST['Richiedente'] ?? '')));
							  if ( !$ret && !is_wp_error( $ret ) )
				$location = add_query_arg( 'message', 3, $location );
			else
				$location = add_query_arg( 'message', 5, $location );
			albopc_remove_metasatto((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),((isset($_REQUEST['newMetaName']) && is_array($_REQUEST['newMetaName']))?array_map('sanitize_text_field', wp_unslash($_REQUEST['newMetaName'])):""));
			if((isset($_REQUEST['newMetaName']) && is_array($_REQUEST['newMetaName']))){
				for($i=0;$i<count(is_array($_REQUEST['newMetaName']) ? wp_unslash($_REQUEST['newMetaName']) : array());$i++){
					albopc_add_attimeta((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['newMetaName'][$i] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['newValue'][$i] ?? ''))));
				}				
			}
			wp_safe_redirect( $location );
			break;
		case "memo_metadati_atto":
			if (!isset($_REQUEST['mmda'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mmda'] ?? '')),'editmetadatiattoatto')){
				Go_Atti();
				break;
			}		
			$location = "?page=atti&stato_atti=".filter_input(INPUT_POST,"stato_atti");	
			$location = add_query_arg( 'message',12, $location );
			if((isset($_REQUEST['newMetaName']) && is_array($_REQUEST['newMetaName'])) And count(is_array($_REQUEST['newMetaName']) ? wp_unslash($_REQUEST['newMetaName']) : array())>0){
				for($i=0;$i<count(is_array($_REQUEST['newMetaName']) ? wp_unslash($_REQUEST['newMetaName']) : array());$i++){
					albopc_add_attimeta((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['newMetaName'][$i] ?? ''))),
									albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['newValue'][$i] ?? ''))));
				}				
			}
			albopc_remove_metasatto((isset($_REQUEST['id'])?intval($_REQUEST['id']):0),sanitize_text_field(wp_unslash($_REQUEST['newMetaName'] ?? '')));
			wp_safe_redirect( $location );
			break;
		case "memo-allegato-atto-associato":
			$location='?page=atti&action=allegati-atto&id='.(isset($_REQUEST['id'])?intval($_REQUEST['id']):0).'&allegatoatto='.wp_create_nonce('gestallegatiatto');

			if (!isset($_REQUEST['secure'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['secure'] ?? '')),'uploallegatoassociato')){
				Go_Atti();
				break;
			}
			if (isset($_REQUEST['annulla'])){
				wp_safe_redirect( $location );
			}else{
				$messaggio =addslashes(str_replace(" ","%20",Memo_allegato_atto_collegato()));
				if (isset($_REQUEST['ref']))
					$location = add_query_arg(
						array ( 'action' => sanitize_text_field(wp_unslash($_REQUEST['ref'] ?? '')), 
								'messaggio' => $messaggio,
								'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
								'id' => (isset($_REQUEST['id'])?intval($_REQUEST['id']):0)) , 
						$location );
				else
					$location = add_query_arg(
						array ( 'action' => 'allegati-atto', 
				                'messaggio' => $messaggio,
								'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
								'id' => (isset($_REQUEST['id'])?intval($_REQUEST['id']):0)) , 
						$location );
			}
			wp_safe_redirect( $location );
			break;
		case "memo-allegato-atto":
			$location='?page=atti&action=allegati-atto&id='.(isset($_REQUEST['id'])?intval($_REQUEST['id']):0).'&allegatoatto='.wp_create_nonce('gestallegatiatto');
			if (!isset($_REQUEST['uploallegato'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['uploallegato'] ?? '')),'uploadallegati')){
				Go_Atti();
				break;
			}
			if (isset($_REQUEST['annulla'])){
				wp_safe_redirect( $location );
			}else{
				$messaggio =addslashes(str_replace(" ","%20",Memo_allegato_atto()));
				if (isset($_REQUEST['ref']))
					$location = add_query_arg(
						array ( 'action' => sanitize_text_field(wp_unslash($_REQUEST['ref'] ?? '')), 
								'messaggio' => $messaggio,
								'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
								'id' => (isset($_REQUEST['id'])?intval($_REQUEST['id']):0)) , 
						$location );
				else
					$location = add_query_arg(
						array ( 'action' => 'allegati-atto', 
				                'messaggio' => $messaggio,
								'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
								'id' => (isset($_REQUEST['id'])?intval($_REQUEST['id']):0)) , 
						$location );
			}
			wp_safe_redirect( $location );	
			break;	
		case "memo-allegati-atto":
			$location='?page=atti&action=allegati-atto&id='.(isset($_REQUEST['id'])?intval($_REQUEST['id']):0).'&allegatoatto='.wp_create_nonce('gestallegatiatto');
			if (!isset($_REQUEST['uploallegato'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['uploallegato'] ?? '')),'uploadallegati')){
				Go_Atti();
				break;
			}
			if (isset($_REQUEST['annulla'])){
				wp_safe_redirect( $location );
			}else{
				$messaggio =addslashes(str_replace(" ","%20",Memo_allegato_atto()));
				if (isset($_REQUEST['ref']))
					$location = add_query_arg(array ( 'action' => sanitize_text_field(wp_unslash($_REQUEST['ref'] ?? '')), 
												  'messaggio' => $messaggio,
												  'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
												  'id' => (isset($_REQUEST['id'])?intval($_REQUEST['id']):0)) , $location );
				else
					$location = add_query_arg(array ( 'action' => 'allegati-atto', 
				                                  'messaggio' => $messaggio,
												  'allegatoatto'=>wp_create_nonce('gestallegatiatto'),
												  'id' => (isset($_REQUEST['id'])?intval($_REQUEST['id']):0)) , $location );
			}
			wp_safe_redirect( $location );	
			break;	
		case "update-allegato-atto":
			if (!isset($_REQUEST['modificaallegatoatto'])) {
				Go_Atti();
				break;	
			}
			if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['modificaallegatoatto'] ?? '')),'editallegatoatto')){
				Go_Atti();
				break;
			}		
			$location='?page=atti&action=allegati-atto&id='.(isset($_REQUEST['id'])?intval($_REQUEST['id']):0).'&allegatoatto='.wp_create_nonce('gestallegatiatto');
			if (sanitize_text_field(wp_unslash($_REQUEST['submit'] ?? '')) == "Annulla"){
				wp_safe_redirect( $location );
			}else{
//				var_dump($_REQUEST);wp_die();
				$ret=albopc_memo_allegato((isset($_REQUEST['idAlle'])?intval($_REQUEST['idAlle']):0),
									   albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['titolo'] ?? ''))),
									   (isset($_REQUEST['id'])?intval($_REQUEST['id']):0),
									   (isset($_REQUEST['Integrale'])?intval($_REQUEST['Integrale']):0), 
									   albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['Natura'] ?? ''))));
				if ( is_object($ret)){
					$location = add_query_arg( 'messaggio', str_replace(' ',"%20",$ret->get_error_message()), $location );	
				}
				else{
				 	$location = add_query_arg( 'messaggio', "Allegato%20Aggiornato", $location );
				}
				wp_safe_redirect( $location );		
			}
			break;
	}		
}
}
function Memo_allegato_atto_collegato(){
	if (sanitize_text_field(wp_unslash($_REQUEST["operazione"] ?? '')) == "associa_allegato"){
		if (!isset($_REQUEST['secure'])) {
			return __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
		}
		if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['secure'] ?? '')),'uploallegatoassociato')){
			return __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
		} 		
		$destination_path =AP_BASE_DIR.get_option('opt_AP_FolderUpload').'/';
		$targetfile = sanitize_text_field(wp_unslash($_REQUEST["AllegatiSpuri"] ?? ""));
		// Il file deve trovarsi dentro la cartella upload dell'Albo: si risolve
		// il path reale e si verifica il prefisso per impedire path traversal
		$real_target = realpath(str_replace("//","/",str_replace("\\","/",$targetfile)));
		$real_upload = realpath($destination_path);
		if ($real_target===FALSE || $real_upload===FALSE || strpos($real_target, $real_upload.DIRECTORY_SEPARATOR)!==0){
			return __("ATTENZIONE. Il file indicato non si trova nella cartella di upload dell'Albo, l'operazione è stata annullata","albo-pretorio-considera");
		}
		$targetfile = $real_target;
		$Impronta=albopc_insert_allegato(sanitize_textarea_field(wp_unslash($_POST['Descrizione'] ?? '')),
									 str_replace("//","/",str_replace("\\","/",$targetfile)),
									 (isset($_POST['id'])?intval($_POST['id']):0), 
									 (isset($_REQUEST['Integrale'])?intval($_REQUEST['Integrale']):0),
									 albopc_sanifica_testo(sanitize_text_field(wp_unslash($_REQUEST['Natura'] ?? ''))));
	}
	return __("File associato","albo-pretorio-considera")."%25%25br%25%25Nome: " . basename( $targetfile)." %25%25br%25%25".__("Percorso completo","albo-pretorio-considera")." : ".str_replace("//","/",str_replace("\\","/",$targetfile))." %25%25br%25%25".__("Impronta","albo-pretorio-considera")." : ".$Impronta."%25%25br%25%25".__("Documento Integrale","albo-pretorio-considera").": " .(isset($_REQUEST['Integrale'])?"Si":"No")."%25%25br%25%25".__("Natura documento","albo-pretorio-considera").": " .(sanitize_text_field(wp_unslash($_REQUEST['Natura'] ?? '')) == "D"?"Documento firmato":"Allegato");
}

function Memo_allegato_atto(){
	if (sanitize_text_field(wp_unslash($_REQUEST["operazione"] ?? '')) == "upload"){
		if (!isset($_REQUEST['uploallegato'])) {
			return __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
		}
		if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['uploallegato'] ?? '')),'uploadallegati')){
			return __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
		} 		
//		var_dump($_FILES);var_dump(sanitize_text_field(wp_unslash($_FILES["files"]['name'] ?? '')));wp_die();
		$numAllegati=count(isset($_FILES["files"]['name']) ? wp_unslash($_FILES["files"]['name']) : array());
		$retMessages=__("Allegati caricati n.","albo-pretorio-considera").$numAllegati."%25%25br%25%25";
		for ($i=0;$i<$numAllegati;$i++) {
			if (((intval(wp_unslash($_FILES["files"]["size"][$i] ?? 0)) / 1024)/1024)<1) {
				$DimFile=number_format(intval(wp_unslash($_FILES["files"]["size"][$i] ?? 0)) / 1024,2);
				$UnitM=" KB";
			}else{
				$DimFile=number_format((intval(wp_unslash($_FILES["files"]["size"][$i] ?? 0)) / 1024)/1024,2);	
				$UnitM=" MB";
			}
		    $dime= __("Dimensione","albo-pretorio-considera").": " . $DimFile . " ".$UnitM;
			if (sanitize_text_field(wp_unslash($_FILES['files']['tmp_name'][$i] ?? ''))==''){
				$messages[4]= __("File non selezionato Oppure operazione annullata","albo-pretorio-considera");
			}else{
				if (!albopc_isAllowedExtension(strtolower(sanitize_text_field(wp_unslash($_FILES["files"]["name"][$i] ?? ''))))){
					$messages= __("Tipo file non valido","albo-pretorio-considera");
				}else{
					/* upload_max_filesize puo' essere espresso in K, M o G: senza
					   convertirlo, un limite di "2G" verrebbe letto come 2 Mb. */
					$LimiteIni	= trim(ini_get('upload_max_filesize'));
					$UnitaIni	= strtoupper(substr($LimiteIni,-1));
					$LimiteMb	= (float)$LimiteIni;
					if ($UnitaIni=="G")		$LimiteMb = $LimiteMb*1024;
					elseif ($UnitaIni=="K")	$LimiteMb = $LimiteMb/1024;
					if (($DimFile>$LimiteMb) and ($UnitM==" MB")){
						$messages= sprintf(/* translators: i segnaposto sono valori dinamici (date, numeri, etichette) inseriti a runtime */ __("Il file caricato è di %1\$s Mb, il limite massimo è di %2\$s Mb","albo-pretorio-considera"),$DimFile,ini_get('upload_max_filesize'));
					}else{
					  	if (intval(wp_unslash($_FILES["files"]["error"][$i] ?? 0)) > 0){
							$messages= __("Errore","albo-pretorio-considera").": " . intval(wp_unslash($_FILES["file"]["error"][$i] ?? 0));
			    		}else{
							if (get_option( 'opt_AP_FolderUploadMeseAnno' )=="") {
								$destination_path =AP_BASE_DIR.get_option('opt_AP_FolderUpload').'/';
							}else{
								$destination_path =albopc_get_PathAllegati(isset($_POST["id"])?intval($_POST["id"]):0)."/";
							}
					   		$result = 0;
						   	$target_path = albopc_UniqueFileName($destination_path . basename(sanitize_file_name(remove_accents ( sanitize_text_field(wp_unslash($_FILES['files']['name'][$i] ?? ''))))));
							if(@move_uploaded_file($_FILES['files']['tmp_name'][$i],$target_path)){ // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- tmp_name generato dal server; move_uploaded_file valida internamente che sia un upload reale e il path NON va sanitizzato (altrimenti si rompe lo spostamento)
			    				$messages= __("File caricato","albo-pretorio-considera")."%25%25br%25%25".__("Nome","albo-pretorio-considera").": " . basename( $target_path)." %25%25br%25%25".__("Percorso completo","albo-pretorio-considera")." : ".str_replace("\\","/",$target_path);
			    				$Natura=(isset($_POST['Natura'][$i])?"D":"A");
			    				$Integrale=(isset($_POST['Integrale'][$i])?1:0);
			    				$Impronta=albopc_insert_allegato(albopc_sanifica_testo(sanitize_text_field(wp_unslash($_POST['Descrizione'][$i] ?? ''))),
															str_replace("\\","/",$target_path),(isset($_POST["id"])?intval($_POST["id"]):0),
															intval($Integrale),
															albopc_sanifica_testo($Natura));
			    				$messages.= "%25%25br%25%25".__("Impronta","albo-pretorio-considera").": " .$Impronta;
			    				$messages.= "%25%25br%25%25".__("Documento Integrale","albo-pretorio-considera").": " .(isset($_POST['Integrale'][$i])?"Si":"No");
			    				$messages.= "%25%25br%25%25".__("Natura documento","albo-pretorio-considera").": " .(isset($_POST['Natura'][$i])?"Documento firmato":"Allegato");
					   		}else{
								$messages= __("Il File non caricato","albo-pretorio-considera").": " .str_replace("\\","/",$target_path)."%25%25br%25%25 ".__("Errore","albo-pretorio-considera").":".intval(wp_unslash($_FILES['file']['error'] ?? 0));
							}
						}
			  		}
			  	}
			}
			$retMessages.=$messages.(($dime!="")?"%25%25br%25%25".($dime): "")."%25%25br%25%25";
		}	
	}
	return $retMessages;
}
function Go_Atti(){
	$location = "?page=atti&p=1" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
function Go_Enti(){
	$location = "?page=enti" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
function Go_Unitao(){
	$location = "?page=unitao" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
function Go_Categorie(){
	$location = "?page=categorie" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
function Go_Responsabili(){
	$location = "?page=soggetti" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
function Go_TipiFiles(){
	$location = "?page=tipifiles" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
function Go_Utility(){
	$location = "?page=utilityAlboP" ;
	$location = add_query_arg( 'message', 80, $location );
	wp_safe_redirect( $location );
}
?>