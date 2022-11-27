<?php 
/**
* Title: Text to Speech script with Google API
* Author: Liam Hogan <bentbot@outlook.com>
* Date: November 26, 2022
* Reference URI: https://cloud.google.com/text-to-speech
**/
/**
* Get your own TTS key from the Google Developer Console.
* URI: https://console.cloud.google.com/apis/dashboard
**/
$google_api_key = "--------- GOOGLE API KEY ---------";

// Voice Settings. Preview voices at the link in Reference URI.
$language = 'en-us';
$voice = 'en-US-Wavenet-I';
$pitch = 1;
$speakingRate = 1;

// Create Files. Output formatted as `tts_how_to.mp3`.
$prefix = 'tts_';
$files = [
	["how_to", "This is Google Text to Speech. Just modify the inputs in the PHP file and create your own custom readings."],
	["multiple_files", "Additional stanzas can be added to the file to conveniently create multiple synthesizations."]
];

// Do not modify below this line unless you know what you are doing...

foreach ($files as $key => $entry) {
	read($entry[0],$entry[1],$prefix,$google_api_key,$language,$voice,$pitch,$speakingRate);
	sleep(5);
}

function read($file,$text,$prefix,$google_api_key,$language,$voice,$pitch,$speakingRate) {
	$filename = $prefix.str_replace(' ', '_', strtolower($file)).'.mp3';
	$myfile = fopen($filename, "w") or die("Unable to open file!");
	$papi = [
		'input' => [
			'text' => $text
		],
		'voice' => [
			'languageCode' => $language,
			'name' => $voice
		],
		'audioConfig' => [
			'audioEncoding' => 'MP3',
			'pitch' => $pitch,
			'speakingRate' => $speakingRate,
		]
	];
	$url= "https://texttospeech.googleapis.com/v1/text:synthesize";
	$post = json_encode($papi);
	$ch = 	curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'X-Goog-Api-Key: '.$google_api_key,
		'Content-Type: application/json; charset=utf-8'
	]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result=curl_exec( $ch );
	curl_close( $ch );
	$content = json_decode($result);
	$base64 = $content->audioContent;
	$mp3 = base64_decode($base64);
	fwrite($myfile, $mp3);
	fclose($myfile);
	print_r($filename);

}
