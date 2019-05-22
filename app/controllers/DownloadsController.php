<?php
// include __DIR__. '/../config.inc.php';

class DownloadsController extends BaseController {

	public function handleDownloads() {
		$f = fopen('logs/download.log', 'w+');
		fwrite($f, "\nDownloadsController -> handleDownloads() called [".date('Y-m-d H:i')."]..\n\n". print_r(Input::all(), true));
		if(Input::has('ids')) {
			$CMS_DOMAIN = FILES_DOMAIN; // 'http://kunsthalle-cms.dev';
			$SITE_DOMAIN = SITE_DOMAIN; // 'http://kunsthalle-site.dev';
			$local_dir= public_path().'/downloads/';
			$remote_dir = $CMS_DOMAIN . '/files/downloads/';
			$files_dir = $SITE_DOMAIN .'/files/downloads/';

			$zip = new ZipArchive();
			$rnd = substr(0, 10, rand(10000, 1000000)) .time();
			$filename = 'kunsthalle_'. $rnd . '.zip';
			$_zf = fopen($local_dir.$filename, 'w+');
			$zip_file = '';
			$use_ssh = false;

			fwrite($f, "\nlocal_file: ". $local_dir.$filename);

			$page = Page::find(Input::get('page_id'));
			$dl_files = [];

			if($zip->open($local_dir. $filename, ZipArchive::CREATE) != FALSE) {
				$data = [];
				$error_list = [];
				$ids = Input::get('ids');

		        $host = Config::get('vars.SFTP_HOST');
		        $port = Config::get('vars.SFTP_PORT');
		        $user = Config::get('vars.SFTP_USER');
		        $pass = Config::get('vars.SFTP_PW');
		        if(!app()->isLocal()) {
			        $ssh = ssh2_connect($host);
			        // if (!$sftp_c->login($user, $pass)) {
			        if($ssh) {
				        if(ssh2_auth_password($ssh, $user, $pass)) {
				        	fwrite($f, "\nAuthenticated!");
				        } else {
				        	fwrite($f, "\nAuth failed!");
				        }
				    }
				    $use_ssh = true;
		        }	
                $remote_dir = '../../'.CMS_ROOT_DIR.'/public/files/downloads/';
        		if(!app()->isLocal()) { $remote_dir = 'public/files/downloads/'; }
                $inc_terms_file = false;
                fwrite($f, "\nProcessing dl..");
				foreach($ids as $id) {
					$dl = Download::find($id);
					$diata[] = $dl;
					$dl_filename = str_replace(' ', '_', $dl->filename);
	                $remote_file = $remote_dir.$dl->filename;
	                $local_file = $local_dir.$dl_filename;
	                fwrite($f, "\nRemote_file: ". $remote_file ."\nLocal_file: ". $local_file."\ndl name: ".$dl_filename);

	                if(count($ids) == 1 && $dl->protected == 0) {
						header('Content-Disposition: attachment; filename="'.$dl->filename.'"');
						return Response::json(array('error' => false, 'file' => $SITE_DOMAIN.'/downloads/'.$dl->filename), 200);
	                }
	                if(!$use_ssh) {
		                if(copy($remote_file, $local_file)) {
							fwrite($f, "\n\nCopied file: ".$dl_filename);
	                		$dl_files[] = SITE_DOMAIN . '/downloads/'.$dl_filename;	                
		                	$zip->addFile($local_file, $dl_filename);
		                }
	                }
	                if($use_ssh) {
		                if(ssh2_scp_recv($ssh, $remote_file, $local_file)) {
		                	fwrite($f, "\n\nCoped file using SSH\n".$remote_file . ' => '.$local_file);
	                		$dl_files[] = SITE_DOMAIN . '/downloads/'.$dl_filename;
		                	$zip->addFile($local_file, $dl_filename);
		                }
	                }
	                // Include protection / terms file
	                if($dl->protected == 1) {
	                	$inc_terms_file = true;
	                }	
	            }
				fwrite($f, "\n\ninc_terms_file ? ". $inc_terms_file."\nterms filename: ".$page->dl_terms_file);

	            if($inc_terms_file && strlen($page->dl_terms_file) > 4) {
	            	$terms_filename = str_replace(' ', '_', $page->dl_terms_file);

	            	if($ssh) {
		                if(ssh2_scp_recv($ssh, $remote_dir.$page->dl_terms_file, $local_dir.$terms_filename)) {
		                	fwrite($f, "\n\nCopied terms file using SSH\n".$remote_dir.$page->dl_terms_file . ' => '.$local_dir.$terms_filename);
		                	$zip->addFile($local_dir.$terms_filename, $terms_filename);
		                }
	            	} else {
	            		if(copy($remote_dir.$page->dl_terms_file, $local_dir.$terms_filename)) {
	            			$zip->addFile($local_dir.$terms_filename, $page->dl_terms_file);
	            		}
	            	}
	            }

				$zip->close();
				$zip_file = $SITE_DOMAIN .'/downloads/'. $filename;
				fwrite($f, "\nzip_file: ". $zip_file);

				$name = Input::has('name') ? Input::get('name') : '';
				$firm = Input::has('firm') ? Input::get('firm') : '';
				$publication_date = Input::has('publication_date') ? Input::get('publication_date') : '';

				if(trim($name) != '') {
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

					$rec_emails  = [ 'pfeffer@oneone-studio.com', 'pressebereich1@kunsthalle-bremen.de' ];//  'shahidm08@gmail.com']; // 'pfeffer@oneone-studio.com']; //, 'shahidm08@gmail.com'];
					$rec_emails = ['shahidm08@gmail.com'];
					foreach($rec_emails as $rec_email) {
						mail($rec_email, "Bilder-Download: ". $firm .'/'. $publication_date, $body, $headers);
					}
				}

				header('Access-Control-Allow-Origin: *');
				header("Content-Security-Policy: default-src 'self'");

				return Response::json(array('error' => false, 'zip' => $zip_file, 'dl_files' => $dl_files), 200);
			}
		}

		return Response::json(array('error' => true, 'msg' => 'Failed'), 401);
	}
}
