<?php
/**
 * Contenuti dei dialog TinyMCE dei pulsanti dell'editor (Albo, Gruppi Atti,
 * Vis. Atto). In precedenza erano file PHP autonomi in js/ caricati come
 * iframe che si auto-bootstrappavano wp-load.php (accesso diretto). Ora sono
 * serviti via admin-ajax.php, dove WordPress è già caricato e le capability
 * verificabili, senza accesso diretto ai file.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Intestazione HTML comune dei dialog popup TinyMCE.
 * I due script del popup TinyMCE fanno parte del core e vanno inclusi nel
 * documento dell'iframe: non sono accodabili con wp_enqueue_script.
 */
function ap_editor_dialog_head( $titolo ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( esc_html__( 'Non hai i permessi per accedere a questa risorsa.', 'albo-pretorio-considera' ) );
	}
	$charset = get_option( 'blog_charset' );
	@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . $charset );
	echo '<!DOCTYPE html><html><head>';
	echo '<title>' . esc_html( $titolo ) . '</title>';
	echo '<base target="_self" />';
	echo '<meta http-equiv="Content-Type" content="' . esc_attr( get_option( 'html_type' ) ) . '; charset=' . esc_attr( $charset ) . '" />';
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- script del popup iframe TinyMCE del core, non accodabile via wp_enqueue
	echo '<script type="text/javascript" src="' . esc_url( site_url( '/wp-includes/js/tinymce/tiny_mce_popup.js' ) ) . '"></script>';
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- script del popup iframe TinyMCE del core, non accodabile via wp_enqueue
	echo '<script type="text/javascript" src="' . esc_url( site_url( '/wp-includes/js/tinymce/utils/form_utils.js' ) ) . '"></script>';
}

/**
 * Bottoni comuni Inserisci/Annulla a fondo dialog.
 */
function ap_editor_dialog_footer() {
	?>
		<div style="float: left">
			<input type="submit" id="insert" name="insert" value="<?php esc_attr_e( 'Inserisci', 'albo-pretorio-considera' ); ?>" onclick="insertAlboShortCode();" />
		</div>
		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="<?php esc_attr_e( 'Annulla', 'albo-pretorio-considera' ); ?>" onclick="tinyMCEPopup.close();" />
		</div>
	</body>
	</html>
	<?php
}

/**
 * Dialog shortcode [Albo].
 */
function ap_ajax_editor_dialog_albo() {
	ap_editor_dialog_head( __( 'Albo OnLine', 'albo-pretorio-considera' ) );
	?>
	<script type="text/javascript">
		function init() { tinyMCEPopup.resizeToInnerSize(); }
		function insertAlboShortCode() {
			var stato   = document.getElementById('StatoAtti').value;
			var filtri  = document.getElementById('Filtri');
			var minfiltri = document.getElementById('MinFiltri');
			var per_page = document.getElementById('Per_page').value;
			var Categoriesel = "";
			var InvForm = document.forms.form;
			for (x = 0; x < InvForm.Categoria.length; x++) {
				if (InvForm.Categoria[x].selected) {
					Categoriesel += InvForm.Categoria[x].value + ",";
				}
			}
			Categoriesel = Categoriesel.substring(0, Categoriesel.length - 1);
			var tagtext = "[Albo ";
			tagtext = tagtext + " stato=\"" + stato + "\"";
			if (Categoriesel.length > 0)
				tagtext = tagtext + " cat=\"" + Categoriesel + "\"";
			if (filtri.checked)
				tagtext = tagtext + " filtri=\"si\"";
			else
				tagtext = tagtext + " filtri=\"no\"";
			if (minfiltri.checked)
				tagtext = tagtext + " minfiltri=\"no\"";
			else
				tagtext = tagtext + " minfiltri=\"si\"";
			tagtext = tagtext + " per_page=\"" + per_page + "\"";
			tagtext = tagtext + "]";
			if (window.tinyMCE) {
				window.tinyMCE.activeEditor.execCommand('mceInsertContent', 0, tagtext);
			}
			tinyMCEPopup.close();
			return;
		}
	</script>
	</head>
	<body onload="tinyMCEPopup.executeOnLoad('init();');">
	<?php $Ele_Cate = ap_get_dropdown_categorie( "Categoria", "Categoria", "", "", "Nessuna", TRUE, FALSE, TRUE ); ?>
		<div class="mceActionPanel">
			<form name="form" action="#" method="get" accept-charset="utf-8">
				<p>
					<label for="StatoAtti"><strong><?php esc_html_e( 'Stato Atti', 'albo-pretorio-considera' ); ?></strong></label>
					<select id="StatoAtti" name="StatoAtti">
						<option value="1"><?php esc_html_e( 'Atti Correnti', 'albo-pretorio-considera' ); ?></option>
						<option value="2"><?php esc_html_e( 'Atti Scaduti, Storico', 'albo-pretorio-considera' ); ?></option>
					</select>
				</p>
				<p>
					<label for="Categoria"><strong><?php esc_html_e( 'Categoria', 'albo-pretorio-considera' ); ?></strong></label>
					<?php echo $Ele_Cate; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <select> generato internamente da ap_get_dropdown_categorie ?>
				</p>
				<p>
					<label for="Filtri"><strong><?php esc_html_e( 'Visualizza Filtri', 'albo-pretorio-considera' ); ?></strong></label>
					<input type="checkbox" name="Filtri" id="Filtri" value="si"/>
				</p>
				<p>
					<label for="MinFiltri"><strong><?php esc_html_e( 'Finestra Filtri sempre visibile', 'albo-pretorio-considera' ); ?></strong></label>
					<input type="checkbox" name="MinFiltri" id="MinFiltri" value="si"/>
				</p>
				<p>
					<label for="Per_page"><strong><?php esc_html_e( 'Numero atti da visualizzare', 'albo-pretorio-considera' ); ?></strong></label>
					<input type="number" name="Per_page" id="Per_page" value="10" style="width:40px;" />
				</p>
			</form>
		</div>
	<?php
	ap_editor_dialog_footer();
	exit;
}
add_action( 'wp_ajax_ap_editor_albo', 'ap_ajax_editor_dialog_albo' );

/**
 * Dialog shortcode [AlboGruppiAtti].
 */
function ap_ajax_editor_dialog_gruppi() {
	ap_editor_dialog_head( __( 'Albo OnLine gruppo atti', 'albo-pretorio-considera' ) );
	?>
	<script type="text/javascript">
		function init() { tinyMCEPopup.resizeToInnerSize(); }
		function insertAlboShortCode() {
			var titolo = document.getElementById('Titolo').value;
			var meta   = document.getElementById('listaAttiMeta').value;
			var valore = document.getElementById('Value').value;
			var tagtext = "[AlboGruppiAtti ";
			tagtext = tagtext + " titolo=\"" + titolo + "\"";
			tagtext = tagtext + " meta=\"" + meta + "\"";
			tagtext = tagtext + " valore=\"" + valore + "\"";
			tagtext = tagtext + "]";
			if (window.tinyMCE) {
				window.tinyMCE.activeEditor.execCommand('mceInsertContent', 0, tagtext);
			}
			tinyMCEPopup.close();
			return;
		}
	</script>
	</head>
	<body onload="tinyMCEPopup.executeOnLoad('init();');">
		<div class="mceActionPanel">
			<form name="form" action="#" method="get" accept-charset="utf-8">
				<p>
					<label for="Titolo"><strong><?php esc_html_e( 'Intestazione Sezione', 'albo-pretorio-considera' ); ?></strong></label><br />
					<input type="text" name="Titolo" id="Titolo" size="45">
				</p>
				<p>
					<label for="listaAttiMeta"><strong><?php esc_html_e( 'Meta Dati codificati', 'albo-pretorio-considera' ); ?></strong></label>
					<?php echo ap_get_elenco_attimeta( "Select", "listaAttiMeta", "ListaAttiMeta", "Si" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup <select> generato internamente da ap_get_elenco_attimeta ?>
				</p>
				<p>
					<label for="Value"><strong><?php esc_html_e( 'Valore Meta', 'albo-pretorio-considera' ); ?></strong></label><br />
					<input type="text" name="Value" id="Value">
				</p>
			</form>
		</div>
	<?php
	ap_editor_dialog_footer();
	exit;
}
add_action( 'wp_ajax_ap_editor_gruppi', 'ap_ajax_editor_dialog_gruppi' );

/**
 * Dialog shortcode [AlboAtto].
 */
function ap_ajax_editor_dialog_visatto() {
	ap_editor_dialog_head( __( 'Albo OnLine visualizza atto', 'albo-pretorio-considera' ) );
	?>
	<script type="text/javascript">
		function init() { tinyMCEPopup.resizeToInnerSize(); }
		function insertAlboShortCode() {
			var titolo = document.getElementById('Titolo').value;
			var numero = document.getElementById('ListaAtti').value;
			var eleAtto = numero.split("/");
			var tagtext = "[AlboAtto ";
			tagtext = tagtext + " titolo=\"" + titolo + "\"";
			tagtext = tagtext + " numero=\"" + eleAtto[0] + "\"";
			tagtext = tagtext + " anno=\"" + eleAtto[1] + "\"";
			tagtext = tagtext + "]";
			if (window.tinyMCE) {
				window.tinyMCE.activeEditor.execCommand('mceInsertContent', 0, tagtext);
			}
			tinyMCEPopup.close();
			return;
		}
	</script>
	</head>
	<body onload="tinyMCEPopup.executeOnLoad('init();');">
	<?php
	$Atti = ap_get_all_atti( 9, 0, 0, 0, '', 0, 0, $OrderBy = " Anno DESC, Numero DESC", 0, 0 );
	?>
		<div class="mceActionPanel">
			<form name="form" action="#" method="get" accept-charset="utf-8">
				<p>
					<label for="Titolo"><strong><?php esc_html_e( 'Titolo', 'albo-pretorio-considera' ); ?></strong></label><br />
					<input type="text" name="Titolo" id="Titolo" size="45">
				</p>
				<p>
					<label for="ListaAtti"><strong><?php esc_html_e( 'Atto', 'albo-pretorio-considera' ); ?></strong></label>
					<select id="ListaAtti" name="ListaAtti">
					<?php foreach ( $Atti as $Atto ) : ?>
						<option value="<?php echo esc_attr( $Atto->Numero . '/' . $Atto->Anno ); ?>"><?php echo esc_html( $Atto->Numero . '/' . $Atto->Anno . ' ' . $Atto->Riferimento ); ?></option>
					<?php endforeach; ?>
					</select>
				</p>
			</form>
		</div>
	<?php
	ap_editor_dialog_footer();
	exit;
}
add_action( 'wp_ajax_ap_editor_visatto', 'ap_ajax_editor_dialog_visatto' );
