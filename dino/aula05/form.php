<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Formulario</title>
  </head>
  <body>
    <form action="index.php" method="get">
      Login: <input type="text" name="login"><br/>
      Senha: <input type="text" name="senha"><br>
      <input type="submit" value="enviar">
    </form>
  <?php
    if (isset($_GET['login']) && $_GET['login'] == 0)  {
      echo "Login falhou";
    }
  ?>
  </body>
</html>
