<?php
/**
 * Codice che viene eseguito in fase di disinstallazione del plugin Albo Pretorio OnLine
 * @link       http://www.eduva.org
 * @since     4.8
 *
 * @package    Albo On Line
 */
		
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

// phpcs:disable WordPress.DB.DirectDatabaseQuery -- disinstallazione: rimozione delle tabelle custom del plugin, nessuna API core equivalente.

include_once(dirname (__FILE__) .'/AlboPretorioFunctions.php');			/* libreria delle funzioni */

global $wpdb,$table_prefix;

define("Albo_URL",plugin_dir_url(dirname (__FILE__).'/AlboPretorio.php'));
define("Albo_DIR",dirname (__FILE__));
define("APHomePath",substr(plugin_dir_path(__FILE__),0,strpos(plugin_dir_path(__FILE__),"wp-content")-1));
define("AlboBCK",WP_CONTENT_DIR."/AlboOnLine");

$albopc_uploads = wp_upload_dir(); 
define("AP_BASE_DIR",$albopc_uploads['basedir']."/");

/* GHSA-75h3-49jg-mw5w (punto 5): l'archivio degli atti e il registro delle
   operazioni vanno conservati dall'ente. La disinstallazione cancella i dati
   solo se l'amministratore lo ha chiesto esplicitamente nelle impostazioni
   (opt_AP_EliminaDatiDisinstallazione). Altrimenti tabelle, allegati e
   opzioni restano e il plugin si puo' reinstallare senza perdite. */
if ( get_option( 'opt_AP_EliminaDatiDisinstallazione', 'No' ) !== 'Si' ) {
	return;
}

// Backup di sicurezza
// creo copia dei dati e dei files allegati prima di disinstallare e cancellare tutto

		$albopc_Data=gmdate('Ymd_H_i_s');
		$wpdb->table_name_Atti = $wpdb->prefix . "albopretorio_atti";
		$wpdb->table_name_Attimeta = $wpdb->prefix . "albopretorio_attimeta";
		$wpdb->table_name_Categorie = $wpdb->prefix . "albopretorio_categorie";
		$wpdb->table_name_Allegati = $wpdb->prefix . "albopretorio_allegati";
		$wpdb->table_name_Log=$wpdb->prefix . "albopretorio_log";
		$wpdb->table_name_RespProc=$wpdb->prefix . "albopretorio_resprocedura";
		$wpdb->table_name_Enti=$wpdb->prefix . "albopretorio_enti";
		$wpdb->table_name_UO=$wpdb->prefix . "albopretorio_unitaorganizzative";
		$albopc_nf=albopc_BackupDatiFiles($albopc_Data);
		/* Il backup non va nella cartella pubblica dei caricamenti: finisce in
		   una cartella protetta fuori da uploads, che la disinstallazione non
		   cancella. */
		$albopc_DirBck=WP_CONTENT_DIR."/AlboOnLine-backup-disinstallazione";
		if ( ! is_dir( $albopc_DirBck ) )
			wp_mkdir_p( $albopc_DirBck );
		// phpcs:disable WordPress.WP.AlternativeFunctions -- protezione della cartella di backup durante la disinstallazione
		if ( ! is_file( $albopc_DirBck."/index.php" ) )
			@file_put_contents( $albopc_DirBck."/index.php", "<?php\n// Silence is golden.\n" );
		@file_put_contents( $albopc_DirBck."/.htaccess", "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nOrder allow,deny\nDeny from all\n</IfModule>\n" );
		$albopc_NomeBck = "BackupAlboPretorioUninstall".$albopc_Data."_".wp_generate_password( 12, false ).".zip";
		if ( ! $albopc_nf || ! @copy( $albopc_nf, $albopc_DirBck."/".$albopc_NomeBck ) ) {
			// senza un backup integro non si cancella nulla
			return;
		}
		// phpcs:enable WordPress.WP.AlternativeFunctions
// Eliminazioni capacità
		$role = get_role( 'administrator' );
		if ( !empty( $role ) ) {
        	$role->remove_cap( 'admin_albo' );
            $role->remove_cap( 'gest_atti_albo' );
        }

// Eliminazioni ruoli
        $albopc_roles_to_delete = array(
            'admin_albo',
            'gest_atti_albo');

        foreach ( $albopc_roles_to_delete as $role ) {

            $albopc_users = get_users( array( 'role' => $role ) );
            if ( count( $albopc_users ) <= 0 ) {
                remove_role( $role );
            }
        }		
		
// Eliminazione Tabelle data Base
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Atti);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Allegati);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Categorie);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Log);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_RespProc);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Enti);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_Attimeta);
		$wpdb->query("DROP TABLE IF EXISTS ".$wpdb->table_name_UO);		
// Eliminazioni Opzioni
		delete_option( 'opt_AP_Ente' );
		delete_option( 'opt_AP_NumeroProgressivo' );
		delete_option( 'opt_AP_AnnoProgressivo' );
		delete_option( 'opt_AP_NumeroProtocollo' );
		delete_option( 'opt_AP_LivelloTitoloEnte' );
		delete_option( 'opt_AP_LivelloTitoloPagina' );
		delete_option( 'opt_AP_LivelloTitoloFiltri' );
		delete_option( 'opt_AP_FolderUpload' );
		delete_option( 'opt_AP_VisualizzaEnte' );  
		delete_option( 'opt_AP_ColoreAnnullati' );  
		delete_option( 'opt_AP_ColorePari' );  
		delete_option( 'opt_AP_ColoreDispari' );  
		delete_option( 'opt_AP_EffettiTesto' );  
		delete_option( 'opt_AP_GiorniOblio' );  
		delete_option( 'opt_AP_LogAc' );  
		delete_option( 'opt_AP_LogOp' );  
		delete_option( 'opt_AP_stileTableFE' );  
		delete_option( 'opt_AP_Versione' );  
		delete_option( 'opt_AP_PAttiCor' );  
		delete_option( 'opt_AP_RestApi' );
		delete_option( 'opt_AP_RestApi_UrlEst' );
		delete_option( 'opt_AP_AutoShortcode' );
		delete_option( 'opt_AP_ColonneFE' );
		delete_option( 'opt_AP_DefaultSoggetti' );
		delete_option( 'opt_AP_OldInterfaccia' );
		delete_option( 'opt_AP_PAttiSto' );
		delete_option( 'opt_AP_PAtto' );
		delete_option( 'opt_AP_RuoliPuls' );
		delete_option( 'opt_AP_RuoliPulsGruppi' );
		delete_option( 'opt_AP_RuoliPulsVisualizzaAtto' );
		delete_option( 'opt_AP_TabResp' );
		delete_option( 'opt_AP_TipidiFiles' );
		delete_option( 'opt_AP_UpCSSNewInterface' );
		delete_option( 'opt_AP_FolderUploadMeseAnno' );
		delete_option( 'opt_AP_BootstrapItalia' );
		delete_option( 'opt_AP_StoricoPubblico' );
		delete_option( 'opt_AP_EliminaDatiDisinstallazione' );
		albopc_Rmdir(AlboBCK);
		albopc_Rmdir(AP_BASE_DIR.'AllegatiAttiAlboPretorio');
?>