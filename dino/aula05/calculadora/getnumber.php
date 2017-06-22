<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Index da Calculadora</title>
  </head>
  <body>
    <form action="calc.php" method="get">
      Qual operação deseja realizar:
      <select name="func">
        <option value="plus">Adicionar</option>
        <option value="sumit">Subitrair</option>
        <option value="div">Dividir</option>
        <option value="mult">Multiplicar</option>
      </select><br/>
      Numero 1:<input type="text" name="num1"><br>
      Numero 2:<input type="text" name="num2"><br>
      <input type="submit" value="enviar">
    </form>
  </body>
</html>
