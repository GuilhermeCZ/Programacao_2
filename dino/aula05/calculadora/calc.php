<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Calculadora</title>
  </head>
  <body>
<?php
  if (($_GET['func']) == 'plus') {
    $result = ($_GET['num1']) + ($_GET['num2']);
  } elseif (($_GET['func']) == 'sumit') {
    $result = ($_GET['num1']) - ($_GET['num2']);
  } elseif (($_GET['func']) == 'div') {
    $result = ($_GET['num1']) / ($_GET['num2']);
  } elseif (($_GET['func']) == 'mult') {
    $result = ($_GET['num1']) * ($_GET['num2']);
  }
?>
    resultado: <?= $result; ?>
  </body>
</html>
