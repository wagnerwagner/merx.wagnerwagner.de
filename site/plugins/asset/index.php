<?php

function timestampedAsset($path) {
  $file = kirby()->roots()->index() . DIRECTORY_SEPARATOR . $path;
  if (!file_exists($file)) {
    throw new Exception('The "' . $path . '" file does not exist.');
  }
  $asset = dirname($path) . '/';
  $asset .= f::name($path) . '.';
  if (option('timestampedAsset', true)) {
    $asset .= filemtime($file) . '.';
  }
  $asset .= f::extension($file);
  return url($asset);
}
