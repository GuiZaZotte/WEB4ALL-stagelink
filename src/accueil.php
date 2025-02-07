<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accueil</title>
    <link rel="stylesheet" href="style/variable.css" />
    <link rel="stylesheet" href="style/accueil.css" />
    <link rel="icon" href="img/favicon.svg" type="image/svg" />
  </head>
  <body>
    <header>
      <div class="first-bar">
        <div class="stagelink">StageLink</div>
        <form action="recherche.html" method="get">
          <input type="text" name="q" placeholder="Rechercher..." />
          <button type="submit"><img src="img/search.svg" alt="" /></button>
        </form>
        <div class="compte">
          <img src="img/compte.svg" alt="" />
          <div class="nom-compte">Mon Compte</div>
        </div>
      </div>
      <nav>
        <a href="" class="pages activer">Accueil</a>
        <a href="offres.php" class="pages">Offres</a>
        <a href="Entreprises.php" class="pages">Entreprises</a>
      </nav>
    </header>
    <div class="easter-egg">
      <img src="img/favicon.svg" alt="" class="logo" /><img
        src="img/Frame 3 (1).svg"
        alt=""
        class="logo-text"
      />
    </div>
    <section>
      <div class="contain">
        <div class="bloc1">
          <img src="img/accueil/iphone-screen1.png" alt="" class="iphone" />
        </div>
        <div class="contexte">
          <div class="icone">
            <img src="img/accueil/lock.svg" alt="" class="svg" />
          </div>
          <div class="titre">Authentification sécurisée</div>
          <div class="texte">
            Connexion avec gestion des rôles via email et mot de passe.
          </div>
        </div>
      </div>
      <div class="contain2">
        <div class="bloc2">
          <img src="img/accueil/Macbook.png" alt="" class="iphone" />
        </div>
        <div class="contexte">
          <div class="icone2">
            <img src="img/accueil/pencil.svg" alt="" class="svg" />
          </div>
          <div class="titre">Gestion des entreprises</div>
          <div class="texte">
            Rechercher, créer, modifier, évaluer et supprimer des entreprises.
          </div>
        </div>
      </div>
      <div class="contain">
        <div class="bloc3">
          <img src="img/accueil/offre.svg" alt="" class="iphone" />
        </div>
        <div class="contexte">
          <div class="icone3">
            <img src="img/accueil/list.svg" alt="" class="svg" />
          </div>
          <div class="titre">Gestion des offres de stage</div>
          <div class="texte">
            Publier, modifier, supprimer et consulter les statistiques des
            offres.
          </div>
        </div>
      </div>
      <div class="contain2">
        <div class="bloc4">
          <img src="img/accueil/utilisateur.svg" alt="" class="iphone2" />
        </div>
        <div class="contexte">
          <div class="icone4">
            <img src="img/accueil/compteN.svg" alt="" class="svg" />
          </div>
          <div class="titre">Gestion des utilisateurs</div>
          <div class="texte">
            Créer, modifier et suivre les comptes étudiants et pilotes.
          </div>
        </div>
      </div>
      <div class="contain">
        <div class="bloc5">
          <img src="img/accueil/notation.svg" alt="" class="iphone" />
        </div>
        <div class="contexte">
          <div class="icone5">
            <img src="img/accueil/star.svg" alt="" class="svg" />
          </div>
          <div class="titre">Candidatures et wish-list</div>
          <div class="texte">
            Postuler aux offres, ajouter/supprimer des offres favorites.
          </div>
        </div>
      </div>
    </section>
    <?php include("footer.php"); ?>
