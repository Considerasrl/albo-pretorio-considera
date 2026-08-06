<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification.Missing -- pagina admin di visualizzazione/redisplay: le letture di superglobali servono al rendering del form; le mutazioni avvengono negli handler di admin.php, protetti da wp_verify_nonce.
/**
 * Gestione Allegati.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */
if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }
?>
<div class="wrap">
	<div class="HeadPage" style="margin-bottom: 30px;">
		<h2 class="wp-heading-inline">Atti</h2>
		<a href="<?php echo esc_url( site_url().'/wp-admin/admin.php?page=atti' );?>" class="add-new-h2 tornaindietro"><?php echo esc_html__("Torna indietro","albo-pretorio-considera");?></a>
		<h3><?php echo esc_html__("Associa nuovo Allegato con file precedentemente caricato","albo-pretorio-considera");?></h3>
	</div>
<div id="col-container">
	<form id="allegato" method="post" action="?page=atti" class="validate">
	<input type="hidden" name="operazione" value="associa_allegato" />
	<input type="hidden" name="action" value="memo-allegato-atto-associato" />
	<input type="hidden" name="secure" value="<?php echo esc_attr( wp_create_nonce('uploallegatoassociato') )?>" />
	<input type="hidden" name="id" value="<?php echo (isset($_REQUEST['id'])?(int)$_REQUEST['id']:0); ?>" />
<?php 
	if (isset($_REQUEST['ref']))
		echo '<input type="hidden" name="ref" value="'.esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['ref'] ) ) ).'" />';
?>	
	<table class="widefat">
	    <thead>
		<tr>
			<th colspan="3" style="text-align:center;font-size:2em;"><?php echo esc_html__("Dati Allegato","albo-pretorio-considera");?></th>
		</tr>
	    </thead>
	    <tbody id="dati-allegato">
		<tr>
			<th><?php esc_html_e("Descrizione Allegato","albo-pretorio-considera");?></th>
			<td><textarea  name="Descrizione" rows="2" cols="100" wrap="ON" maxlength="255" required></textarea></td>
		</tr>
		<tr>
			<th><?php esc_html_e("Natura File","albo-pretorio-considera");?></th>
			<td><select name="Natura" id="Natura" wrap="ON" >
				<option value="D">Documento firmato</option>
				<option value="A">Allegato</option>
			</select></td>
		</tr>
		<tr>
			<th><?php esc_html_e("Documento Integrale?","albo-pretorio-considera");?></th>
			<td><input type="checkbox" name="Integrale" value="1" id="Integrale" checked> </td>
		</tr>
		<tr>
			<th>File:</th>
			<td><?php echo ap_get_allegati_file_scollegati("Select"); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper restituisce markup <select> con opzioni già escapate ?></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="submit" id="submit" class="button" value="<?php esc_html_e("Collega Allegato","albo-pretorio-considera");?>"  />
			<input type="submit" name="annulla" id="annulla" class="button" value="<?php esc_html_e("Annulla Operazione","albo-pretorio-considera");?>" />
			</td>
		</tr>
	    </tbody>
	</table>
	</form>
</div>
</div>