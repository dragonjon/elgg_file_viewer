<?php

/**
 * Elgg Viewer
 */
elgg_register_event_handler('init', 'system', 'elgg_file_viewer_init');

function elgg_file_viewer_init() {

	// Projekktor for Video/Audio support
	elgg_register_js('projekktor', '/mod/elgg_file_viewer/vendors/projekktor-1.2.38r332/projekktor-1.2.38r332.min.js');
	elgg_register_simplecache_view('js/elgg_file_viewer/projekktor');
	elgg_register_js('elgg.projekktor', elgg_get_simplecache_url('js', 'elgg_file_viewer/projekktor'), 'footer');

	elgg_register_css('projekktor', '/mod/elgg_file_viewer/vendors/projekktor-1.2.38r332/theme/maccaco/projekktor.style.css');

	// Syntax highlighting
	elgg_register_css('prism', elgg_get_simplecache_url('prism/themes/prism.css'));
	elgg_extend_view('prism/themes/prism.css', 'prism/plugins/line-numbers/prism-line-numbers.css');
	
	elgg_define_js('prism', [
		'src' => elgg_get_simplecache_url('prism/prism.js'),
		'exports' => 'Prism',
	]);
	elgg_define_js('prism-line-numbers', [
		'src' => elgg_get_simplecache_url('prism/plugins/line-numbers/prism-line-numbers.js'),
		'deps' => ['prism'],
	]);

	elgg_register_page_handler('projekktor', 'elgg_file_viewer_projekktor_video');
}

/**
 * Get publicly accessible URL for the file
 *
 * @param ElggFile $file
 * @return string|false
 */
function elgg_file_viewer_get_public_url($file) {

	if (!$file instanceof ElggFile) {
		return false;
	}

	return elgg_get_download_url($file, false, '+60 minutes');
}

/**
 * Get a URL to the alternative format of the video/audio file
 * @param ElggFile $file
 * @param string $format
 */
function elgg_file_viewer_get_media_url($file, $format) {

	if (!elgg_instanceof($file, 'object', 'file')) {
		return '';
	}

	if (!elgg_is_logged_in()) {
		return $file->getURL();
	}

	return elgg_normalize_url("projekktor/$file->guid/$format/media.$format");
}

/**
 * Fix mime
 * 
 * @param ElggFile $file
 * @return boolean|string
 */
function elgg_file_viewer_get_mime_type($file) {

	if (!$file instanceof ElggFile) {
		return;
	}

	return $file->detectMimeType();
}

/**
 * Serve a converted web compatible video
 * URL structure: projekktor/<guid>/<format>/
 *
 * @param array $page Page segments array
 */
function elgg_file_viewer_projekktor_video($page) {

	$enable_ffmpeg = elgg_get_plugin_setting('enable_ffmpeg', 'elgg_file_viewer');
	if ($enable_ffmpeg != 'yes') {
		return false;
	}

	$guid = elgg_extract(0, $page, null);
	$file = get_entity($guid);

	if (!elgg_instanceof($file, 'object', 'file')) {
		return false;
	}

	$info = pathinfo($file->getFilenameOnFilestore());
	$filename = $info['filename'];

	$format = elgg_extract(1, $page);

	$output = new ElggFile();
	$output->owner_guid = $file->owner_guid;
	$output->setFilename("projekktor/$file->guid/$filename.$format");

	if (!$output->size()) {
		try {
			$filestorename = $output->getFilenameOnFilestore();

			$output->open('write');
			$output->close();

			$ffmpeg_path = elgg_get_plugin_setting('ffmpeg_path', 'elgg_file_viewer');

			$FFmpeg = new FFmpeg($ffmpeg_path);
			$FFmpeg->input($file->getFilenameOnFilestore())->output($filestorename)->ready();

			elgg_log("Converting file $file->guid to $format: $FFmpeg->command", 'NOTICE');
		} catch (Exception $e) {
			elgg_log($e->getMessage(), 'ERROR');
		}
	}

	$mime = elgg_file_viewer_get_mime_type($file);
	$base_type = substr($mime, 0, strpos($mime, '/'));

	header("Pragma: public");
	header("Content-type: $base_type/$format");
	header("Content-Disposition: attachment; filename=\"$filename.$format\"");

	ob_clean();
	flush();
	readfile($output->getFilenameOnFilestore());
}