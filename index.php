<!DOCTYPE html>
<html>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body><table><tr><td>


  <hr>
  <h2>Corso da ricercare</h2>
<form action="#" method='post' name='datidacercare' id='datidacercare'>
<label>id corso</label><input type='text' id='idcorso' name='idcorso'>
<input type='hidden' id='azionedafare' name='azionedafare' value='seleziona'>
<button type='submit'>cerca per id</button>
</form>

<h2>Corso da ricercare per nome</h2>
<form action="#" method='post' name='datidacercarepernome' id='datidacercarepernome'>
<label>id corso</label><input type='text' id='nome_corso' name='nome_corso'>
<input type='hidden' id='azionedafare' name='azionedafare' value='cerca_per_nome'>
<button type='submit'>cerca per nome</button>
</form>





  <h2>Corso da modificare</h2>
  <form action="#" method='post' name='datidamodificare' id='datidamodificare'>
<input type='hidden' id='idcorsodamodificare' name='idcorso'>
<input type='text' id='nome_corsodamodificare' name='nome_corso' >
<input type='hidden' id='azionedafare' name='azionedafare' value='modifica'>
<button type='submit'>modifica corso</button>
</form>



  <p id='primop'>This is a paragraph.</p>
  <p id='secondop' class='nascondino'>This is another paragraph.</p>

  <button id='inviaform' class='nascondino'>Click me to send data</button>


  <div id='spaziogriglia' class='classestefano' ></div><hr>
                                </div><hr>
</body>

</html>
<?php include_once('jquery.php');?>