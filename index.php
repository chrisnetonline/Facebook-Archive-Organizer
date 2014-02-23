<?php

// Configuration.
$config = array(
	'source'                  => __DIR__.'/source',
	'destination'             => __DIR__.'/destination',
	'destination_date_format' => 'Y-m-d H.i.s',
	'facebook_date_format'    => 'l, F j, Y \a\t g:ia T',
);

// Set up required packages.
require 'vendor/autoload.php';
use Symfony\Component\DomCrawler\Crawler;

// Find name and paths to all albums.
$crawler = new Crawler();
$crawler->addContent(file_get_contents($config['source'].'/html/photos.htm'));
$crawler->filter('.block div a')->each(function(Crawler $node) use ($config) {

	// Extract album name and path.
	$album = $node->text();
	$album = trim(substr($album, 0, strrpos($album, ' - ')));
	$path  = ltrim($node->attr('href'), '/.');

	// Create destination direction.
	$destination = $config['destination'].'/'.str_replace('/', '-', $album);
	if ( ! is_dir($destination)) {
		mkdir($destination);
	}

	// Search for all photos in the album.
	$crawler = new Crawler();
	$crawler->addContent(file_get_contents($config['source'].'/'.$path));
	$crawler->filter('.block')->each(function(Crawler $node) use ($config, $destination, $path) {

		// Get uploaded timestamp.
		$date_obj = DateTime::createFromFormat($config['facebook_date_format'], $node->filter('div.meta')->text());

		// Determine if Facebook has the photo taken meta data.
		$node->filter('table.meta tr')->each(function(Crawler $node) use (&$date_obj) {
			if ($node->filter('th')->text() == 'Taken') {
				$date_obj = new DateTime();
				$date_obj->setTimestamp($node->filter('td')->text());
			}
		});

		$path_parts  = pathinfo($config['source'].'/'.$path);
		$photo_parts = pathinfo($node->filter('img')->attr('src'));

		// Prevent copying over files because of duplicate timestamps.
		$new_name = $date_obj->format($config['destination_date_format']);
		$index    = 0;
		while (file_exists($destination.'/'.$new_name.'.'.$photo_parts['extension'])) {
			++$index;
			$new_name = $date_obj->format($config['destination_date_format']).'-'.$index;
		}

		// Copy file to destination.
		copy($path_parts['dirname'].'/'.$photo_parts['basename'],
			$destination.'/'.$new_name.'.'.$photo_parts['extension']);

	});

});

echo 'Done!';
