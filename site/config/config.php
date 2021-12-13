<?php
$stripeTestPublishableKey = file_get_contents('secrets/.stripeTestPublishableKey', FILE_USE_INCLUDE_PATH);
$stripeTestSecretKey = file_get_contents('secrets/.stripeTestSecretKey', FILE_USE_INCLUDE_PATH);
$paypalSandboxClientID = file_get_contents('secrets/.paypalSandboxClientID', FILE_USE_INCLUDE_PATH);
$paypalSandboxSecret = file_get_contents('secrets/.paypalSandboxSecret', FILE_USE_INCLUDE_PATH);

$stripeLivePublishableKey = file_get_contents('secrets/.stripeLivePublishableKey', FILE_USE_INCLUDE_PATH);
$stripeLiveSecretKey = file_get_contents('secrets/.stripeLiveSecretKey', FILE_USE_INCLUDE_PATH);
$paypalLiveClientID = file_get_contents('secrets/.paypalLiveClientID', FILE_USE_INCLUDE_PATH);
$paypalLiveSecret = file_get_contents('secrets/.paypalLiveSecret', FILE_USE_INCLUDE_PATH);

$merxLicense = file_get_contents('secrets/.merxLicense', FILE_USE_INCLUDE_PATH);

return [
  'github-repository' => 'https://github.com/wagnerwagner/merx',
  'timestampedAsset' => true,
  'merxPluginDirectory' => '/site/plugins/merx',
  'merx-email' => 'merx@wagnerwagner.de',
  'ww.merx.production' => true,
  'ww.merx.license' => $merxLicense,
  'ww.merx.stripe.test.publishable_key' => $stripeTestPublishableKey,
  'ww.merx.stripe.test.secret_key' => $stripeTestSecretKey,
  'ww.merx.stripe.live.publishable_key' => $stripeLivePublishableKey,
  'ww.merx.stripe.live.secret_key' => $stripeLiveSecretKey,
  'ww.merx.paypal.sandbox.clientID' => $paypalSandboxClientID,
  'ww.merx.paypal.sandbox.secret' => $paypalSandboxSecret,
  'ww.merx.paypal.live.clientID' => $paypalLiveClientID,
  'ww.merx.paypal.live.secret' => $paypalLiveSecret,
];
