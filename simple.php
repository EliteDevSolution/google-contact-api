<?php
/*
 * Copyright 2012 Google Inc.
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

require_once './src/Google_Client.php';
session_start();

$client = new Google_Client();
$client->setApplicationName('Google Contacts PHP Sample');
$client->setScopes("http://www.google.com/m8/feeds/");
// Documentation: http://code.google.com/apis/gdata/docs/2.0/basics.html
// Visit https://code.google.com/apis/console?api=contacts to generate your
// oauth2_client_id, oauth2_client_secret, and register your oauth2_redirect_uri.
// $client->setClientId('insert_your_oauth2_client_id');
// $client->setClientSecret('insert_your_oauth2_client_secret');
// $client->setRedirectUri('insert_your_redirect_uri');
// $client->setDeveloperKey('insert_your_developer_key');

if (isset($_GET['code'])) {
  $client->authenticate();
  $_SESSION['token'] = $client->getAccessToken();
  $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
 $client->setAccessToken($_SESSION['token']);
}

if (isset($_REQUEST['logout'])) {
  unset($_SESSION['token']);
  $client->revokeToken();
}

if ($client->getAccessToken()) {
  $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full");
  $val = $client->getIo()->authenticatedRequest($req);
  $response1 = json_encode(simplexml_load_string($val->getResponseBody()));
  //print "<pre>" . print_r(json_decode($response1, true), true) . "</pre>";exit;

  // The contacts api only returns XML responses.
  $response = $val->getResponseBody();
 // print "<pre>" . print_r(json_decode($response, true), true) . "</pre>";
  $xmlContacts = simplexml_load_string($response);
  $xmlContacts->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
  $contactsArray = array();

        foreach ($xmlContacts->entry as $xmlContactsEntry) {
            $contactDetails = array();
            $contactDetails['id'] = (string) $xmlContactsEntry->id;
            $contactDetails['name'] = (string) $xmlContactsEntry->title;
            $contactDetails['content'] = (string) $xmlContactsEntry->content;

            foreach ($xmlContactsEntry->children() as $key => $value) {
                $attributes = $value->attributes();

                if ($key == 'link') {
                    if ($attributes['rel'] == 'edit') {
                        $contactDetails['editURL'] = (string) $attributes['href'];
                    } elseif ($attributes['rel'] == 'self') {
                        $contactDetails['selfURL'] = (string) $attributes['href'];
                    } elseif ($attributes['rel'] == 'http://schemas.google.com/contacts/2008/rel#edit-photo') {
                        $contactDetails['photoURL'] = (string) $attributes['href'];
                    }
                }
            }

            $contactGDNodes = $xmlContactsEntry->children('http://schemas.google.com/g/2005');
            foreach ($contactGDNodes as $key => $value) {
                switch ($key) {
                    case 'organization':
                        $contactDetails[$key]['orgName'] = (string) $value->orgName;
                        $contactDetails[$key]['orgTitle'] = (string) $value->orgTitle;
                        break;
                    case 'email':
                        $attributes = $value->attributes();
                        $emailadress = (string) $attributes['address'];
                        $emailtype = substr(strstr($attributes['rel'], '#'), 1);
                        $contactDetails[$key][] = ['type' => $emailtype, 'email' => $emailadress];
                        break;
                    case 'phoneNumber':
                        $attributes = $value->attributes();
                        //$uri = (string) $attributes['uri'];
                        $type = substr(strstr($attributes['rel'], '#'), 1);
                        //$e164 = substr(strstr($uri, ':'), 1);
                        $contactDetails[$key][] = ['type' => $type, 'number' => $value->__toString()];
                        break;
                    default:
                        $contactDetails[$key] = (string) $value;
                        break;
                }
            }
          $index = 0;
          foreach ($contactDetails as $row) {
            $index++;
            if($index == 7)
            {
               $contactDetails['realemail'] = $row[0]['email']; 
            }
          }           
              $contactsArray[] = $contactDetails;   
        }
        var_dump($contactsArray);

  // The access token may have been updated lazily.
  $_SESSION['token'] = $client->getAccessToken();
} else {
  $auth = $client->createAuthUrl();
}
if (isset($auth)) {
    print "<a class=login href='$auth'>Connect Me!</a>";
  } else {
    print "<a class=logout href='?logout'>Logout</a>";
}