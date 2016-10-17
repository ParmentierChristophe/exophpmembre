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

// FORMULAIRE D'INSCRIPTION !
if(isset($_POST['forminscription'])) {
  // securiser les données
  $pseudo = htmlspecialchars($_POST['pseudo']);
  $mail = htmlspecialchars($_POST['mail']);
  $mdp = sha1($_POST['mdp']);
  $mdp2 = sha1($_POST['mdp2']);

  if(!empty($_POST['pseudo']) AND !empty($_POST['mail']) AND !empty($_POST['mdp']) AND !empty($_POST['mdp2']))
  {
    // calculer la taille du pseudo
    $pseudolength = strlen($pseudo);
    if($pseudolength <= 255 ){
      // valider que l'on rentre bien un mail!
      if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        // voir si l'adresse mail existe deja
        $reqmail = $bdd->prepare('SELECT * FROM membres WHERE mail = ?');
        $reqmail->execute(array($mail));
        $mailexist = $reqmail->rowCount();
        if($mailexist == 0){
          // verifier si le pseudo existe deja
          $reqpseudo = $bdd->prepare('SELECT * FROM membres WHERE pseudo = ?');
          $reqpseudo->execute(array($pseudo));
          $pseudoexist = $reqpseudo->rowCount();
          if($pseudoexist == 0){

          // verifier si les 2 mots passes sont pareil
            if ($mdp == $mdp2) {
              $insertmembre = $bdd->prepare('INSERT INTO membres(pseudo, mail, motdepasse) VALUES(?, ?, ?)');
              $insertmembre ->execute(array($pseudo, $mail, $mdp));
              $erreur = '<p class="erreur">COOL TU ES INSCRIS! CONNECTE TOI! </p>';
              // header('location: index.php');
            }else {
              $erreur = '<p class="erreur">Vos mots de passes ne sont pas identiques</p>';
            }
          }else {
            $erreur = '<p class="erreur">Ce pseudo existe déjà</p>';
          }
        }else{
          $erreur = '<p class="erreur">Cette adresse Mail existe déjà</p>';
        }
      }else {
        $erreur = '<p class="erreur">Votre addresse mail n\'est pas valide</p>';
      }
    }else {
      // erreur pseudo trop long
      $erreur = '<p class="erreur">votre pseudo doit faire moins de 255 caractéres !</p>';
    }
  }else {
    $erreur=  '<p class="erreur">Tous les champs doivent etre remplis</p>';
  }
}





// FORMULAIRE DE CONNEXION !
if(isset($_POST['formconnexion'])){
  // Securisation des données
  $mailconnect = htmlspecialchars($_POST['mailconnect']);
  $mdpconnect = sha1($_POST['mdpconnect']);
  // verifie que les champs ne sont pas vide
  if (!empty($mailconnect) AND !empty($mdpconnect)) {
    // voir si l'utilisateur existe
    $requser = $bdd->prepare('SELECT * FROM membres WHERE mail = ? AND motdepasse = ?');
    $requser->execute(array($mailconnect, $mdpconnect));
    $userexist = $requser->rowCount();
    if($userexist == 1){

      // recuperer les info et rediriger vers le profil
      $userinfo = $requser->fetch();
      $_SESSION['id'] = $userinfo['id'];
      $_SESSION['pseudo'] = $userinfo['pseudo'];
      $_SESSION['mail'] = $userinfo['mail'];
      header('location: profil.php?id='.$_SESSION['id']);


    }else {
      $erreur=  '<p class="erreur">Mauvais identifiants!</p>';
    }


  }else {
    $erreur=  '<p class="erreur">Tous les champs doivent etre remplis</p>';
  }
}
 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Inscription à un site trop cool</title>
    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="style.css" media="screen">
  </head>
  <body>


    <div class="form">

          <ul class="tab-group">
            <li class="tab active"><a href="#signup">Inscription</a></li>
            <li class="tab"><a href="#login">Se Connecter</a></li>
          </ul>

          <div class="tab-content">
            <div id="signup">
              <h1>Inscription</h1>

              <form action="" method="post">

              <div class="field-wrap">
                  <div class="top-row">
                  <label>
                    Pseudo<span class="req">*</span>
                  </label>
                  <input type="text" name="pseudo" required autocomplete="off" value="<?php if(isset($pseudo)) {echo $pseudo ;} ?>" />
                </div>
              </div>

              <div class="field-wrap">
                <label>
                  Adresse E-mail<span class="req">*</span>
                </label>
                <input type="email"required autocomplete="off" name="mail" value="<?php if(isset($mail)) {echo $mail ;} ?>"/>
              </div>

              <div class="field-wrap">
                <label>
                  Mot de passe<span class="req">*</span>
                </label>
                <input type="password"required autocomplete="off" name="mdp"/>
              </div>

              <div class="field-wrap">
                <label>
                  Confirmer votre mot de passe<span class="req">*</span>
                </label>
                <input type="password"required autocomplete="off" name="mdp2"/>
              </div>


              <?php if(isset($erreur)){
                echo $erreur;
              } ?>

              <input type="submit" name="forminscription" class="button button-block" value="Inscription"/>
              </form>

            </div>










            <div id="login">
              <h1>Connexion</h1>

              <form action="" method="post">

                <div class="field-wrap">
                <label>
                  Adresse E-mail<span class="req">*</span>
                </label>
                <input type="email"required autocomplete="off" name="mailconnect"/>
              </div>

              <div class="field-wrap">
                <label>
                  Mot de passe<span class="req">*</span>
                </label>
                <input type="password"required autocomplete="off" name="mdpconnect"/>
              </div>

              <p class="forgot"><a href="#">Mot de passe perdu?</a></p>

              <input type="submit" name="formconnexion" class="button button-block" value="Connexion"/>

              </form>

            </div>

          </div><!-- tab-content -->

    </div> <!-- /form -->




    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type="text/javascript" src="main.js">

    </script>

  </body>
</html>
