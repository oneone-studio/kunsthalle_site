<?php
// include __DIR__. '/../config.inc.php';

class DownloadsController extends BaseController {

	public function handleDownloads() {
		$f = fopen('logs/download.log', 'w+');
		fwrite($f, "\nDownloadsController -> handleDownloads() called [".date('Y-m-d H:i')."]..\n\n". print_r(Input::all(), true));
		if(Input::has('ids')) {
			$CMS_DOMAIN = FILES_DOMAIN; // 'http://kunsthalle-cms.dev';
			$SITE_DOMAIN = SITE_DOMAIN; // 'http://kunsthalle-site.dev';
			$local_dir= '../public/downloads/';
			$remote_dir = $CMS_DOMAIN . '/files/downloads/';
			$files_dir = $SITE_DOMAIN .'/files/downloads/';

			$zip = new ZipArchive();
			$rnd = substr(0, 10, rand(10000, 1000000)) .time();
			$filename = 'kunsthalle_'. $rnd . '.zip';
			$_zf = fopen($local_dir.$filename, 'w+');
			$zip_file = '';

			fwrite($f, "\nlocal_file: ". $local_dir.$filename);

			$page = Page::find(Input::get('page_id'));
			$dl_files = [];

			if($zip->open($local_dir. $filename, ZipArchive::CREATE) != FALSE) {
				$data = [];
				$error_list = [];
				$ids = Input::get('ids');
                $host = CMS_HOST; // "136.243.166.47";	
                $port = CMS_PORT; // 22;
                $user = CMS_USER; // "kunsthvr_1";
                $pass = CMS_PW;   // "25XcWIqAFiVcSwAP";
                $remote_dir = '../../'.CMS_ROOT_DIR.'/public/files/downloads/';
                $inc_terms_file = false;
                fwrite($f, "\nProcessing dl..");
				foreach($ids as $id) {
					$dl = Download::find($id);
					$diata[] = $dl;
					$dl_filename = str_replace(' ', '_', $dl->filename);
	                $remote_file = $remote_dir.$dl->filename;
	                $local_file = $local_dir.$dl_filename;
	                $dl_files[] = SITE_DOMAIN . '/downloads/'.$dl_filename;	                
	                fwrite($f, "\nRemote_file: ". $remote_file ."\nLocal_file: ". $local_file."\ndl name: ".$dl_filename);

	                if(count($ids) == 1 && $dl->protected == 0) {
						header('Content-Disposition: attachment; filename="'.$dl->filename.'"');
						return Response::json(array('error' => false, 'file' => $SITE_DOMAIN.'/downloads/'.$dl->filename), 200);
	                }
	                if(copy($remote_file, $local_file)) {
						fwrite($f, "\n\nCopied file: ".$dl_filename);
	                	$zip->addFile($local_file, $dl_filename);
	                }
	                // Include protection / terms file
	                if($dl->protected == 1) {
	                	$inc_terms_file = true;
	                }	
	            }

	            if($inc_terms_file && strlen($page->dl_terms_file) > 4) {
	            	if(file_exists($remote_dir.$page->dl_terms_file)) {
	            		// if(copy($remote_dir.$page->dl_terms_file, $local_dir.$page->dl_terms_file)) {
	            		// 	$zip->addFile($local_dir.$page->dl_terms_file, $page->dl_terms_file);
	            		// }
						$zip->addFile($remote_dir.$page->dl_terms_file, $page->dl_terms_file);	            	}
	            }

				$zip->close();
				$zip_file = $SITE_DOMAIN .'/downloads/'. $filename;
				fwrite($f, "\nzip_file: ". $zip_file);

				$name = Input::has('name') ? Input::get('name') : '';
				$firm = Input::has('firm') ? Input::get('firm') : '';
				$publication_date = Input::has('publication_date') ? Input::get('publication_date') : '';

				$body = 'Name, Vorname: '. $name . '<br>'.
						'Firma / Redaktion: '. $firm . '<br>'.
						'Veröffentlichungsdatum: '. $publication_date . '<br><br>';

				if(count($data)) {
					$body .= 'Files:<br>';
					$file_list = [];
					foreach($data as $d) {
						$file_list[] = $d->filename;
					}
					$body .= implode(', ', $file_list);
				}
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= 'From: Kunsthalle Bremen <info@kunsthalle-bremen.de>' . "\r\n";

				// $rec_emails  = [ 'pfeffer@oneone-studio.com', 'pressebereich1@kunsthalle-bremen.de' ];//  'shahidm08@gmail.com']; // 'pfeffer@oneone-studio.com']; //, 'shahidm08@gmail.com'];
$rec_emails = ['shahidm08@gmail.com'];
				foreach($rec_emails as $rec_email) {
					mail($rec_email, "Bilder-Download: ". $firm .'/'. $publication_date, $body, $headers);
				}

				header('Access-Control-Allow-Origin: *');
				header("Content-Security-Policy: default-src 'self'");

				return Response::json(array('error' => false, 'zip' => $zip_file, 'dl_files' => $dl_files), 200);
			}
		}

		return Response::json(array('error' => true, 'msg' => 'Failed'), 401);
	}
}
