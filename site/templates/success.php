<?php
use Wagnerwagner\Merx\Merx;

try {
  $orderPage = merx()->completePayment($_GET);
  go($orderPage->url());
} catch (Exception $ex) {
  Merx::setMessage($ex->getMessage());
  go('buy');
}
