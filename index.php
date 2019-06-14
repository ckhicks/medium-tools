<?php
// settings
$host = getenv('HTTP_HOST');
$client = "55f478b20c1c";
$secret = "9be35b4285816d96da91ee751cf1af7158168c13";
$state = "all-your-base-are-belong-to-us";
$redirect = "https://" . $host;
$headers = array('Content-Type: application/json', 'Accept: application/json', 'Accept-Charset: utf-8');
$mode = 'home';
$error = '';
$query = $_GET;

$meta = array(
  'title' => 'Medium.tools',
  'description' => 'Create drafts on Medium.com from your canonical content source (unofficial).',
  'image' => '',
);

// mode
if (!empty($query['code']) && !empty($query['state'])) {
  if ($query['state'] === $state) {
    $mode = 'auth';
  } else {
    $mode = 'error';
  }
}
if (!empty($query['id']) && !empty($query['token'])) {
  if (!empty($query['link'])) {
    $mode = 'submit';
  } else {
    $mode = 'user';
  }
}
if (!empty($query['draft'])) {
  $mode = 'success';
}

// functions
if ($mode === 'auth') {

  // get the token using the auth code
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.medium.com/v1/tokens');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    "code" => $_GET['code'],
    "client_id" => $client,
    "client_secret" => $secret,
    "grant_type" => "authorization_code",
    "redirect_uri" => $redirect
  )));
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $errors = curl_error($ch);
  $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // dirty error check
  if ($info > 199 && $info < 400) {
    $token = json_decode($response)->access_token;

    // use the token to get user details
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.medium.com/v1/me');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, array('Authorization: Bearer ' . $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $errors = curl_error($ch);
    $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($info > 199 && $info < 400) {
      $user = json_decode($response)->data;
    } else {
      $error = json_decode($response)->errors[0]->message;
    }

    $path = http_build_query(array('id' => $user->id, 'token' => $token, 'avatar' => $user->imageUrl, 'name' => $user->name));
    header("Location: https://" . $host . '?' . $path);
    exit;
  } else {
    $error = json_decode($response)->errors[0]->message;
  }

} else if ($mode === 'submit') {

  // fetch the original title
  preg_match("/<title>(.+)<\/title>/siU", file_get_contents($query['link']), $matches);
  $title = $matches[1];

  // create a post using the user id
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://api.medium.com/v1/users/' . $query['id'] . '/posts');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'title' => $title,
    'content' => '{paste your content here}<hr /><em>Originally published at <a href="' . $query['link'] . '" rel="nofollow noopener" target="_blank">' . parse_url($query['link'])['host'] . '</a>.</em>',
    'canonicalUrl' => $query['link'],
    'contentFormat' => "html",
    'publishStatus' => "draft",
    'notifyFollowers' => false
  )));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, array('Authorization: Bearer ' . $query['token'])));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $response = curl_exec($ch);
  $errors = curl_error($ch);
  $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($info > 199 && $info < 400) {
    $post = json_decode($response)->data;

    // show the success page
    $path = http_build_query(array('draft' => $post->url));
    header("Location: https://" . $host . '?' . $path);
    exit;
  } else {
    $error = json_decode($response)->errors[0]->message;
  }

}

// render
include_once('render.php');