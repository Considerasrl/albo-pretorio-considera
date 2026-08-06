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
	echo "<h2>Allegati Multipli</h2>";
	$TipiAmmessi=ap_tipiFileAmmessi(TRUE);
	$ap_exts=array();       // ["pdf","doc"]  estensioni ammesse
	$ap_accept=array();     // [".pdf",".doc"] per attributo accept
	$ap_icone=array();      // {"pdf":"icona.png"} mappa estensione->icona
	foreach ( $TipiAmmessi as $Tipo) {
		$ap_exts[]   = $Tipo["."];
		$ap_accept[] = '.'.$Tipo["."];
		$ap_icone[$Tipo["."]] = $Tipo["Icon"];
	}
?>
<form action="?page=atti" method="post" enctype="multipart/form-data">
	<input type="hidden" name="operazione" value="upload" />
	<input type="hidden" name="action" value="memo-allegati-atto" />
	<input type="hidden" name="uploallegato" value="<?php echo esc_attr( wp_create_nonce('uploadallegati') )?>" />
	<input type="hidden" name="id" value="<?php echo (isset($_REQUEST['id'])?(int)$_REQUEST['id']:0); ?>" />
<div>
  <label for="files" id="pulCar"><span class="dashicons dashicons-portfolio" style="font-size:2em;padding-right:0.5em;margin-top:-7px;"></span> <?php echo esc_html__("Seleziona gli allegati da caricare","albo-pretorio-considera");?></label>
  <input type="file" id="files" name="files[]" accept="<?php echo esc_attr( implode(',', $ap_accept) );?>" multiple>
</div>
<div class="preview">
  <p><?php echo esc_html__("Nessun file selezionato per il caricamento","albo-pretorio-considera");?></p>
</div>
<div>
  <button id="pulCar"><span class="dashicons dashicons-upload" style="font-size:2em;padding-right:0.5em;margin-top:-7px;"></span> <?php echo esc_html__("Carica","albo-pretorio-considera");?></button>
</div>
</form>
     <script>
        var input = document.querySelector('#files');
        var preview = document.querySelector('.preview');
        input.style.visibility = 'hidden';
        input.addEventListener('change', caricaDatiAllegati);
        function caricaDatiAllegati() {
          while(preview.firstChild) {
            preview.removeChild(preview.firstChild);
          }
          var curFiles = input.files;
          var list = document.createElement('ol');
          var icone = <?php echo wp_json_encode($ap_icone);?>;
	        preview.appendChild(list);
	        for(var i = 0; i < curFiles.length; i++) {
	          var icona=IconFileType(curFiles[i]);
	          var listItem = document.createElement('li');
	          listItem.className="elemento";
	          var para = document.createElement('p');
	          var des= document.createElement('input');
	          des.setAttribute("type", "text");
	          des.setAttribute("required", "");
	          des.setAttribute("name", "Descrizione["+ i.toString() +"]");
	          des.className="des";
	          
	          var LBLnatura=document.createElement('span');
	          LBLnatura.textContent = '<?php esc_html_e("Documento firmato","albo-pretorio-considera");?>  ';
	          var natura= document.createElement('input');
	          natura.setAttribute("type", "checkbox");
	          natura.setAttribute("name", "Natura["+ i.toString() +"]");
	          var LBLintegrale=document.createElement('span');
	          LBLintegrale.textContent = '<?php esc_html_e("Documento Integrale","albo-pretorio-considera");?>  ';
	          var integrale= document.createElement('input');
	          integrale.setAttribute("type", "checkbox");
	          integrale.setAttribute("name", "Integrale["+ i.toString() +"]");
	          integrale.setAttribute("name", "Integrale["+ i.toString() +"]");
	          integrale.setAttribute("checked", "");
	          
	          
	          if(validFileType(curFiles[i])) {
	            para.textContent = 'File name ' + curFiles[i].name + ', file size ' + returnFileSize(curFiles[i].size) + '.';
	            var image = document.createElement('img');
	            image.setAttribute("src", icona);
	            listItem.appendChild(image);
	            listItem.appendChild(para);
	            listItem.appendChild(des);
		        listItem.appendChild(LBLnatura);
				listItem.appendChild(natura);
		        listItem.appendChild(LBLintegrale);
				listItem.appendChild(integrale);
	          } else {
	            para.textContent = 'File name ' + curFiles[i].name + ':<?php echo esc_js(__("Tipo di file non permesso. Riprova selezionando un file con estensione diversa.","albo-pretorio-considera"));?>';
	            listItem.appendChild(para);
	          }
	          list.appendChild(listItem);
	        }
        }
        function getEstensione(filename){
			var parti=filename.split(".");
			return parti[(parti.length) - 1].toLowerCase();
		}
        var fileTypes = <?php echo wp_json_encode($ap_exts);?>;
        function validFileType(file) {
          var estensione=getEstensione(file.name);
          for(var i = 0; i < fileTypes.length; i++) {
            if(estensione.toLowerCase() === fileTypes[i].toLowerCase()) {
              return true;
            }
          }
          return false;
        }
       var icone = <?php echo wp_json_encode($ap_icone);?>;
        function IconFileType(file) {
        	var estensione=getEstensione(file.name);
            for(var i = 0; i < fileTypes.length; i++) {
            if(estensione === fileTypes[i]) {
              return icone[estensione];
            }
          }
          return false;
        }       
        function returnFileSize(number) {
          if(number < 1024) {
            return number + 'bytes';
          } else if(number > 1024 && number < 1048576) {
            return (number/1024).toFixed(1) + 'KB';
          } else if(number > 1048576) {
            return (number/1048576).toFixed(1) + 'MB';
          }
        }
     </script>