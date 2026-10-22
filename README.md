Dex : TP1
Cours : Programmation web 2
Fait par : Hind Manouzi (2262749)

**Initialisation et création du projet**
1. Copiez et déplacez le projet dans htdocs de XAMPP.
2. Ouvrir le panneau de contrôle XAMPP et démarrer les modules Apache et MySQL.
3. Dans la barre de recherche du navigateur, écrivez "http://localhost/phpmyadmin".
4. Cliquez sur "Nouvelle base de données", la nommer "DexBD" et sélectionner "utf8mb4_unicode_ci".
5. Pour initialiser la base de données, inscrire "http://localhost/OutilDex/init.php".
    - Vous devriez voir "Initilisation terminée!" s'afficher sur la page, qui vous confirmera que la base de données a été créée.
    - Revenez sur phpmyadmin. Dans la base de données "dexbd", vous verrez 4 tables (4 lignes pour cours, 15 pour créneaux, 0 pour disponiblités et 5 pour travaux).
6. Ensuite, écrivez "http://localhost/OutilDex/index.php" pour afficher la page web de DexBD.

**Son utilisation**
- Pour filtrer les cours :
    - [ ] Choisissez un cours (ou "Tous les cours") dans la barre de sélection à droite de "Cours".
    - [ ] Cliquez sur le bouton Filtrer.
    - [ ] Si le cours choisi contient des travaux ouverts, vous verrez la liste des travaux avec leurs informations s'afficher. Sinon, un message d'erreur vous indiquera que ce cours n'a pas de travaux ouverts.
- Pour voir les détails d'un travail :
    - [ ] Cliquez sur le lien "Voir le travail" qui vous enverra sur une page détaillée du travail.
    - [ ] Inscrivez votre matricule, votre nom et choisissez <ins>au moins</ins> un créneau.
    - [ ] Cliquez ensuite sur le bouton "Soumettre mes disponibilités" et vous verrez votre nom s'afficher dans la colonne "Noms" du formulaire. Vous verrez aussi "Meilleur créneau" en gras.
    - [ ] Un message de succès s'affichera pour confirmer que vos informations ont été ajoutées dans la base de données.
    - [ ] \(BONUS) "Salut, [votre nom] !" s'affichera en haut à gauche de la page.
    - [ ] Pour revenir à la liste des cours, cliquez sur "Retour à l'accueil".
- Pour modifier votre réponse :
    - [ ] Si vous voulez changer par exemple votre créneau, inscrivez votre matricule et nom et désélectionnez l'ancien créneau et sélectionnez le créneau désisé.
    - [ ] Soumettez votre disponiblité et l'ancien créneau sera remplacé par le nouveau dans la base de donnée.

**Structure du projet**
OutilDex/
|--css/
    |-- style.css
|-- index.php  
|--init.php
|--README.md
|-- travail.php
|-- includes/
    |-- connexion.php
    |-- fonctionsEssentiels.php
    |-- footer.php
    |-- header.php
