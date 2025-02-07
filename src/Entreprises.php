<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Entreprises</title>
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
        <a href="accueil.php" class="pages">Accueil</a>
        <a href="offres.php" class="pages">Offres</a>
        <a href="" class="pages activer">Entreprises</a>
      </nav>
    </header>
    <div class="easter-egg">
      <img src="img/favicon.svg" alt="" class="logo" /><img
        src="img/Frame 3 (1).svg"
        alt=""
        class="logo-text"
      />
    </div>

<?php include("footer.php"); ?>

