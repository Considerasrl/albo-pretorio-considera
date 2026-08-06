<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * WGestione Enti.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }


function albopc_load_Data_Funzioni(){
	$TabResponsabili=get_option('opt_AP_TabResp');
	if((is_string($TabResponsabili) && (is_object(json_decode($TabResponsabili)) || is_array(json_decode($TabResponsabili))))){
		$TR=json_decode($TabResponsabili);
	}else{
		$Default='[{"ID":"RP","Funzione":"Responsabile Procedimento","Display":"Si","StaCert":"No"},{"ID":"OP","Funzione":"Gestore procedura","Display":"Si","StaCert":"No"},{"ID":"SC","Funzione":"Segretario Comunale","Display":"No","StaCert":"No"},{"ID":"RB","Funzione":"Responsabile Pubblicazione","Display":"No","StaCert":"No"},{"ID":"DR","Funzione":"Direttore dei Servizi Generali e Ammistrativi","Display":"No","StaCert":"No"}]';
		update_option('opt_AP_TabResp',$Default ); 
		$TR=json_decode($Default);
	}
?>	  
<script id="jsSourceRuoli" type="text/javascript">	  
jQuery(document).ready(function($){
	$('#GridFunzioni').appendGrid('load', [
<?php
	  foreach($TR as $Ruolo){
	  	echo "{ 'ID': '".esc_js($Ruolo->ID)."', 'funzione': '".esc_js($Ruolo->Funzione)."','visualizza': ".($Ruolo->Display=="Si" ? "true" : "false").", 'staincert': ".($Ruolo->StaCert=="Si" ? "true" : "false")." },";
		}
?>
        ]);	  
	});
</script>
<?php	  
		
}
$messages[1] = __('Elemento aggiunto.','albo-pretorio-considera');
$messages[2] = __('Elemento cancellato.','albo-pretorio-considera');
$messages[3] = __('Elemento aggiornato.','albo-pretorio-considera');
$messages[4] = __('Elemento non aggiunto.','albo-pretorio-considera');
$messages[5] = __('Elemento non aggiornato.','albo-pretorio-considera');
$messages[6] = __('Elemento non cancellato.','albo-pretorio-considera');
$messages[7] = __('Impossibile cancellare Enti che sono collegati ad Atti','albo-pretorio-considera');
$messages[80] = __("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
albopc_load_Data_Funzioni();
?>
<div id="ElaborazioneTabella" style="width: 200px;height: 200px;position: absolute;top: 50%;left: 50%; margin-top: -100px; margin-left: -100px;display:none;" >
	<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'css/images/ElaborazioneInCorso.gif' )?>" id="ElaborazioneTabella"/>
</div>
<div class="wrap nosubsub">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-media-spreadsheet" style="font-size: 1.1em;"></span> <?php esc_html_e("Tabelle","albo-pretorio-considera");?>
	</div>

	<div id="config-tabs-container" style="margin-top:20px;">
		<ul>
			<li><a href="#Conf-tab-1"><?php esc_html_e("Funzioni","albo-pretorio-considera");?></a></li>
		</ul>	 
		<div id="Conf-tab-1">

		  <form action="" method="post" id="FormFunzioni">
		  	<table id="GridFunzioni"></table>
		  	<button type="button" id="MemoFunzioni" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="dashicons dashicons-edit"></span> <?php esc_html_e("Memorizza Tabella Funzioni","albo-pretorio-considera");?></button>
		  	<button type="button" id="LoadDefaultFunzioni" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"><span class="dashicons dashicons-update"></span> <?php esc_html_e("Carica i valori di default","albo-pretorio-considera");?></button>
		  </form>
		</div>
	</div>
</div><!-- /wrap -->