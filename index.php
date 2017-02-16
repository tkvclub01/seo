<?php
/*
 * Copyright 2013 Google Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

include_once __DIR__ . '/google-api-php-client/vendor/autoload.php';
include_once "google-api-php-client/examples/templates/base.php";


echo pageHeader("Service Account Access");

/************************************************
 Make an API request authenticated with a service
 account.
 ************************************************/

$client = new Google_Client();

/************************************************
 ATTENTION: Fill in these values, or make sure you
 have set the GOOGLE_APPLICATION_CREDENTIALS
 environment variable. You can get these credentials
 by creating a new Service Account in the
 API console. Be sure to store the key file
 somewhere you can get to it - though in real
 operations you'd want to make sure it wasn't
 accessible from the webserver!
 Make sure the Books API is enabled on this
 account as well, or the call will fail.
 ************************************************/

if ($credentials_file = checkServiceAccountCredentialsFile()) {
	// set the location manually
	$client->setAuthConfig($credentials_file);
} elseif (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
	// use the application default credentials
	$client->useApplicationDefaultCredentials();
} else {
	echo missingServiceAccountDetailsWarning();
	return;
}

$client->setApplicationName("DATO-SEO");
$client->setScopes(['https://www.googleapis.com/auth/books']);
$service = new Google_Service_Books($client);

/************************************************
 We're just going to make the same call as in the
 simple query as an example.
 ************************************************/
$optParams = array('filter' => 'free-ebooks');
$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);
?>

<h3>Results Of Call:</h3>
<?php foreach ($results as $item): ?>
  <?= $item['volumeInfo']['title'] ?>
  <br />
<?php endforeach ?>


<div id="content"></div>
    <script>
      function hndlr(response) {
      for (var i = 0; i < response.items.length; i++) {
        var item = response.items[i];
        // in production code, item.htmlTitle should have the HTML entities escaped.
        document.getElementById("content").innerHTML += "<br>" + item.htmlTitle;
      }
    }
    </script>
    <script src="https://www.googleapis.com/customsearch/v1?key=<span class="apiparam">2jBhlbS0Wq8R8CPsrAZBeo0t</span>&amp;cx=017576662512468239146:omuauf_lfve&amp;q=cars&amp;callback=hndlr">
    </script>

<?php pageFooter(__FILE__); ?>
