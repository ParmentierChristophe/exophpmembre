<?php
session_start();

// connexion a la bdd
try
{
   $bdd = new PDO('mysql:host=localhost;dbname=espacemembre;charset=utf8', 'root', 'simplon');
}
catch(Exception $e)
{
   die('Erreur : '.$e->getMessage());
}


// recuperer les info de l'utilisateur
if (isset($_SESSION['id'])) {
  $requser = $bdd->prepare('SELECT * FROM membres WHERE id = ?');
  $requser->execute(array($_SESSION['id']));
  $user = $requser->fetch();
// ajout du nouveau pseudo a la place de l'ancien
  if(isset($_POST['newpseudo']) AND !empty($_POST['newpseudo']) AND $_POST['newpseudo'] != $user['pseudo']){

    $newpseudo = htmlspecialchars($_POST['newpseudo']);
    $insertpseudo = $bdd->prepare('UPDATE membres SET pseudo = ? WHERE id =?');
    $insertpseudo->execute(array($newpseudo, $_SESSION['id']));
    header('Location: profil.php?id='.$_SESSION['id']);

  }
// ajout du nouveau mail a la place de l'ancien
  if(isset($_POST['newmail']) AND !empty($_POST['newmail']) AND $_POST['newmail'] != $user['mail']){

    $newmail = htmlspecialchars($_POST['newmail']);
    $insertmail = $bdd->prepare('UPDATE membres SET mail = ? WHERE id =?');
    $insertmail->execute(array($newmail, $_SESSION['id']));
    header('Location: profil.php?id='.$_SESSION['id']);

  }
// verification des nouveau mot de passe
  if(isset($_POST['newmdp1']) AND !empty($_POST['newmdp1']) AND isset($_POST['newmdp2']) AND !empty($_POST['newmdp2'])){

    $mdp1 = sha1($_POST['newmdp1']);
    $mdp2 = sha1($_POST['newmdp2']);

    if($mdp1 == $mdp2){
      $insertmdp = $bdd->prepare('UPDATE membres SET motdepasse = ? WHERE id = ?');
      $insertmdp->execute(array($mdp1, $_SESSION['id']));
      header('Location: profil.php?id='.$_SESSION['id']);

    }else {
      $msg = '<p class="erreur">Les deux mots de passes ne sont pas identiques</p>';
    }

  }


  // upload d'avatar

  if(isset($_FILES['avatar']) AND !empty($_FILES['avatar']['name'])) {
    // voir si la taille maxi depasse pas 2mo
    $tailleMax = 2097152;
    $extensionsValides = array('jpg', 'jpeg', 'gif', 'png');
    if($_FILES['avatar']['size'] <= $tailleMax) {
       $extensionUpload = strtolower(substr(strrchr($_FILES['avatar']['name'], '.'), 1));
       // voir si l'extension de fichier correspond a la var $extendFile et la mettre dans le nouveau chemin
       if(in_array($extensionUpload, $extensionsValides)) {
          $chemin = "membres/avatar/".$_SESSION['id'].".".$extensionUpload;
          $resultat = move_uploaded_file($_FILES['avatar']['tmp_name'], $chemin);
          // si tut ok envoyer l'avatar dans la bdd et rediriger vers la page de profil
          if($resultat) {
             $updateavatar = $bdd->prepare('UPDATE membres SET avatar = :avatar WHERE id = :id');
             $updateavatar->execute(array(
                'avatar' => $_SESSION['id'].".".$extensionUpload,
                'id' => $_SESSION['id']
                ));
             header('Location: profil.php?id='.$_SESSION['id']);
          } else {
             $msg = '<p class="erreur">erreur lors de l importation de votre image</p>';
          }
       } else {
          $msg = '<p class="erreur">Votre image doit etre au format jpg, jpeg, gif ou png !</p>';
       }
    } else {
       $msg = '<p class="erreur">Votre image est trop grosse (max 2mb)</p>';
    }
  }
 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Editer mon Profil</title>
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="style.css" media="screen">
  </head>
  <body>


    <div class="form">
      <h1>Edition de mon profil !</h1>

      <form action="" method="post" enctype="multipart/form-data">

        <div class="field-wrap">

            <input type="file" name="avatar" />
          </>
        </div>

      <div class="field-wrap">
          <div class="top-row">
          <label>
            Pseudo<span class="req">*</span>
          </label>
          <input type="text" name="newpseudo" required autocomplete="off" value="<?php echo $user['pseudo'] ; ?>" />
        </div>
      </div>


      <div class="field-wrap">
          <div class="top-row">
          <label>
            Mail<span class="req">*</span>
          </label>
          <input type="text" name="newmail" required autocomplete="off" value="<?php echo $user['mail'] ; ?>" />
        </div>
      </div>


      <div class="field-wrap">
          <div class="top-row">
          <label>
            Nouveau mot de passe<span class="req">*</span>
          </label>
          <input type="password" name="newmdp1" autocomplete="off"/>
        </div>
      </div>


      <div class="field-wrap">
          <div class="top-row">
          <label>
            confirmer votre mot de passe<span class="req">*</span>
          </label>
          <input type="password" name="newmdp2" autocomplete="off"/>
        </div>
      </div>


      <input type="submit" class="button button-block" value="Mettre à jour"/>



    </form>
    <?php if(isset($msg)){ echo $msg; } ?>


    </div> <!-- /form -->




    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="main.js"></script>

  </body>
</html>

<?php
}else {
  header('Location: inscription.php');
}
 ?>
