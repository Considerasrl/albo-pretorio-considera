<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Gestione Permessi.
 * @link       http://www.eduva.org
 * @since      4.8
 *
 * @package    Albo On Line
 */

if(preg_match('#' . basename(__FILE__) . '#', isset($_SERVER['PHP_SELF']) ? sanitize_text_field(wp_unslash($_SERVER['PHP_SELF'])) : '')) { die('You are not allowed to call this page directly.'); }
if (isset($_REQUEST['action']) And sanitize_text_field(wp_unslash($_REQUEST['action'] ?? '')) == "memoPermessi"){
	if (isset($_REQUEST['permessi'])){
		if (wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['permessi'] ?? '')),'gestpermessi')){
			$albopc_lista=albopc_get_users(); 
		// Azzera capacit࠵tenti di gestione ed amministrazione Albo Pretorio
			foreach($albopc_lista as $albopc_riga){
				if (!(user_can( $albopc_riga->ID, 'create_users') or user_can( $albopc_riga->ID, 'manage_network'))) {
					$albopc_users = new WP_User( $albopc_riga->ID);
					$albopc_users->remove_cap("gest_atti_albo");
					$albopc_users->remove_cap("editore_atti_albo");
					$albopc_users->remove_cap("admin_albo");
				}
			}	
		// Crea capacit࠵tenti di gestione ed amministrazione Al Pretorio in base a quanto scelto dall'Utente
			foreach($_REQUEST as $albopc_key=>$albopc_val){
				$albopc_UID=substr($albopc_key,1);
				if (is_numeric($albopc_UID)){
					$albopc_users = new WP_User($albopc_UID);
					if ($albopc_val=="Amministratore"){
						$albopc_users->add_cap("admin_albo");
						$albopc_users->add_cap("editore_atti_albo");
						$albopc_users->add_cap("gest_atti_albo");
					}
					if ($albopc_val=="Editore"){
						$albopc_users->add_cap("editore_atti_albo");
						$albopc_users->add_cap("gest_atti_albo");
					}
					if ($albopc_val=="Gestore")
						$albopc_users->add_cap("gest_atti_albo");
				}
			}
		}else{
			$albopc_Msg=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
		}
	}else{
		$albopc_Msg=__("ATTENZIONE. Rilevato potenziale pericolo di attacco informatico, l'operazione è stata annullata","albo-pretorio-considera");
	}
}

echo '<div class="wrap">
	<div class="HeadPage">
		<h2 class="wp-heading-inline"><span class="dashicons dashicons-groups" style="font-size:1em;"></span> Permessi Utente
	</div>';
if (isset($albopc_Msg)) {
	echo '<div id="message" class="updated"><p>'.esc_html($albopc_Msg).'</p></div>';
}
echo '
		<div class="postbox-container" style="margin-top:20px;">
			<div class="widefat">
			<form id="gestPermessi" method="post" action="?page=permessiAlboP"  >
			<input type="hidden" name="action" value="memoPermessi"/>
			<input type="hidden" name="permessi" value="'.esc_attr(wp_create_nonce("gestpermessi")).'" />
				<table class="widefat" style="width:100%;">
					<thead>
					<tr>
						<th>'.esc_html__("Utente","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Azzera Capacità Utente","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Capacità di Amministrare l'Albo","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Capacità di Editore dell'Albo","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Capacità di Gestire l'Albo","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Ruolo Amministratore","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Ruolo Editore","albo-pretorio-considera").'</th>
						<th>'.esc_html__("Ruolo Gestore","albo-pretorio-considera").'</th>
					</tr>
					</thead>
					<tbody>';
$albopc_lista=albopc_get_users(); 
foreach($albopc_lista as $albopc_riga){
 	$albopc_users = new WP_User( $albopc_riga->ID);
 	$albopc_Utente=false;
	if ($albopc_users->has_cap('gestore_albo') or $albopc_users->has_cap('editore_albo') or $albopc_users->has_cap('amministratore_albo'))
		$albopc_Utente=true;
 	if (!(user_can( $albopc_riga->ID, 'create_users') or user_can( $albopc_riga->ID, 'manage_network'))) {
		$albopc_Stato='';
		$albopc_StatoEditore='';
		$albopc_StatoGestore='';
		echo '<tr>
		<td>'.esc_html($albopc_riga->user_login).'</td>';
	 	if (user_can( $albopc_riga->ID, 'gest_atti_albo')){
			$albopc_Stato='';
			$albopc_StatoEditore='';
	 		$albopc_StatoGestore='checked';	
		}
	 	if (user_can( $albopc_riga->ID, 'editore_atti_albo')){
			$albopc_Stato='';
			$albopc_StatoEditore='checked';
	 		$albopc_StatoGestore='';	
		}
	 	if (user_can( $albopc_riga->ID, 'admin_albo')){
			$albopc_Stato='checked';
			$albopc_StatoEditore='';
	 		$albopc_StatoGestore='';	
		}

		if (!$albopc_Utente)
			echo '				  <td><input type="radio" value="Nullo" '.esc_attr($albopc_Stato).' name="U'.(int)$albopc_riga->ID.'" /></td>
				  <td><input type="radio" value="Amministratore" '.esc_attr($albopc_Stato).' name="U'.(int)$albopc_riga->ID.'" /></td>
				  <td><input type="radio" value="Editore" '.esc_attr($albopc_StatoEditore).' name="U'.(int)$albopc_riga->ID.'" /></td>
				  <td><input type="radio" value="Gestore" '.esc_attr($albopc_StatoGestore).' name="U'.(int)$albopc_riga->ID.'" /></td>';
		else
			echo '				  <td>&nbsp;</td>
			      <td>&nbsp;</td>
				  <td>&nbsp;</td>';
		if ($albopc_users->has_cap('amministratore_albo'))
			echo '<td>si</td>';
		else
			echo '<td>--</td>';
		if ($albopc_users->has_cap('editore_albo'))
			echo '<td>si</td>';
		else
			echo '<td>--</td>';
		if ($albopc_users->has_cap('gestore_albo'))
			echo '<td>si</td>';
		else
			echo '<td>--</td>';
		echo '	</tr>';
	}
}
echo '					</tbody>
				</table>
				
				<div style="margin-left:auto;width:140px;margin-right:auto;">
					<p>
					<input type="submit" name="memo" id="memo" class="button" value="'.esc_attr__("Memorizza Permessi","albo-pretorio-considera").'" />
					</p>
				</div>
				</form>
			</div>
		</div>
	</div>
';