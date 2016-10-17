<?php
session_start();

// connexion a la bdd
try
{
   $bdd = new PDO('mysql:host=localhost;dbname=espacemembre;charset=utf8', 'root', '');
}
catch(Exception $e)
{
   die('Erreur : '.$e->getMessage());
}
// recuperer les info de l'utilisateur
if (isset($_GET['id']) AND $_GET['id'] > 0) {

  $getid = intval($_GET['id']);
  $requser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
  $requser->execute(array($getid));
  $userinfo = $requser->fetch();

 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Profil</title>
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="style.css" media="screen">
  </head>
  <body>

    <div class="form">
      <h1>Profil de <?php echo $userinfo['pseudo']; ?></h1>
    <?php if(!empty($userinfo['avatar'])){
      ?>
        <img src="membres/avatar/<?php echo $userinfo['avatar']; ?>" alt="" class="imgprofil"/>
      <?php
    } ?>
    <br>
      <p>Votre Pseudo = <?php echo $userinfo['pseudo']; ?></p>
      <p>mail = <?php echo $userinfo['mail']; ?></p>
      <?php
        if (isset($_SESSION['id']) AND $userinfo['id'] == $_SESSION['id']) {
          ?>
            <a class="linkcenter" href="editionprofil.php">Editer mon profil</a><br>
            <a class="linkcenter" href="deconnexion.php">Se deconnecter</a>

          <?php
        }
       ?>

    </div> <!-- /form -->

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="main.js"></script>

  </body>
</html>

<?php
}
 ?>
