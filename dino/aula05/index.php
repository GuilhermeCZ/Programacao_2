<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>index</title>
  </head>
  <body>
<?php
  if (($_GET['login'] == 'guilherme') && $_GET['senha'] == 123)  {
    header("location: ok.php?login=1");
  } else {
    header("location: form.php?login=0");
  }
?>
    </form>
  </body>
</html>
