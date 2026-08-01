# Brief design — Maquette UX complète de l'app Amana (User)

> Document autonome, à partager tel quel avec un outil/assistant de design (ex. Claude). Il contient tout le nécessaire — contexte produit, système de design, langage d'interaction, et l'inventaire complet des écrans — pour générer une maquette UX haute-fidélité de **la totalité** de l'application mobile "User", et pas seulement un sous-ensemble.

## 0. Contexte & demande

Amana est une application mobile de services financiers (wallet / fintech mobile — envoi d'argent, paiements, cartes virtuelles, épargne...), actuellement développée en Flutter, en cours de réécriture en React Native. Un premier jet de maquette UX a déjà été produit et validé pour **12 écrans représentatifs** (démarrage, connexion, accueil, historique, notifications, le parcours d'envoi d'argent en 4 étapes, profil, QR code) — il établit l'identité visuelle et le langage d'interaction ci-dessous.

**Ce qui est demandé maintenant** : étendre cette maquette à **la totalité des écrans de l'application** (~30 écrans, inventoriés en détail en section 4), en respectant strictement le même système de design et le même langage d'interaction que ceux déjà établis — pas une nouvelle direction créative, une extension fidèle et exhaustive.

Format de sortie attendu : une maquette haute-fidélité par écran (idéalement sous forme de gabarits de téléphone regroupés par parcours fonctionnel, comme le premier jet), couvrant tous les écrans listés en section 4, y compris leurs états secondaires importants (vide, chargement, erreur) quand ils sont mentionnés.

---

## 1. Identité de marque

- **Nom** : Amana
- **Positionnement** : app de services financiers mobile — confiance, simplicité, rapidité.
- **Ton visuel** : professionnel mais chaleureux, moderne, épuré. Pas ludique/enfantin, pas austère/corporate froid.
- **Logo/mark** : un simple monogramme "A" sur fond dégradé bleu (voir palette), utilisé dans un badge arrondi. Pas de logo définitif fourni — traiter comme un placeholder cohérent.

## 2. Système de design

### 2.1 Couleurs

| Rôle | Nom | Hex |
|---|---|---|
| Primaire (marque) | Amana Blue | `#2B5BEE` |
| Primaire — foncé (dégradés, texte sur clair) | Blue Ink | `#173583` |
| Secondaire / accent | Amana Gold | `#EEBE2B` |
| Accent secondaire (catégorisation) | Indigo | `#6366F1` |
| Accent secondaire (catégorisation) | Violet | `#A855F7` |
| Accent secondaire (catégorisation) | Sky Blue | `#3B82F6` |
| Accent secondaire (catégorisation) | Orange | `#F97316` |
| Fond d'écran | Screen BG | `#F8FAFC` |
| Titres / texte fort | Ink | `#1E293B` |
| Texte courant / secondaire | Slate | `#475569` |
| Bordures / séparateurs | Line | `#E2E8F0` |
| Succès | Success | `#35C75A` |
| Erreur | Error | `#EB4E3D` |
| Information | Info | `#18B800` |
| Avertissement | Warning | `#FFCC00` |

**Règle importante** : la couleur primaire (`Amana Blue`) doit être traitée comme **potentiellement configurable côté serveur** (marque blanche) dans l'app réelle — ne pas la coder en dur dans les composants si le format de sortie le permet, mais visuellement elle reste la référence par défaut pour cette maquette.

Utiliser les accents secondaires (indigo/violet/sky/orange) pour différencier visuellement les catégories dans les grilles de services et les icônes (ex. Envoyer=bleu, Demander=violet, Retrait=vert, Factures=or), pas pour des éléments de marque primaires.

### 2.2 Typographie

- **Police unique pour toute l'interface** : **Albert Sans** (Regular 400, Light 300, Bold 700, Italic). Pas de fallback système visible — c'est LA police de l'app.
- **Exception** : la police **OCR A** (monospace, style "carte bancaire") est utilisée uniquement pour l'affichage des numéros de carte virtuelle et autres identifiants façon "carte" (ex. ID de transaction). Ne jamais l'utiliser pour du texte courant.
- Échelle type (taille/graisse) :
  - Titre d'app bar : 20px / 600
  - Titre d'écran (header) : 22–28px / 600
  - Titre de section : 15–17px / 600
  - Corps de texte : 15–16px / 400
  - Légende / méta : 11–13px / 400
  - Montant en évidence (carte de solde) : 26–34px / 700
- Toute la mise à l'échelle est responsive (l'app d'origine utilise un système type `flutter_screenutil`) — penser en unités relatives, pas en pixels figés.

### 2.3 Espacement, rayons, ombres

- Grille d'espacement de base : multiples de 4 (4, 8, 12, 14, 16, 18, 24...).
- Padding d'écran horizontal standard : ~18px.
- Rayons : petits composants (badges, chips) ~8–12px ; cartes ~16–22px ; bottom sheets ~15–20px ; boutons ~14–15px.
- Ombres douces et colorées plutôt que grises neutres — ex. une carte bleue projette une ombre bleue diffuse, pas une ombre noire générique.
- Dégradés à deux tons (ex. bleu → bleu foncé, ou bleu → indigo) sur les éléments de marque forts (carte de solde, bouton primaire, écran de démarrage) ; le reste de l'UI reste plat/uni.

### 2.4 Composants de référence (déjà établis, à réutiliser partout)

- **Carte de solde** : fond dégradé bleu, montant en gros, icône œil pour masquer/afficher, chip "+ Ajouter" en overlay.
- **Grille de services** : icônes dans des badges arrondis à fond teinté pastel (couleur par catégorie), libellé court en dessous.
- **Liste de transaction** : icône de catégorie à gauche (teinte verte si entrant, rouge si sortant), nom + date, montant aligné à droite (vert `+` / rouge `−`).
- **Barre d'onglets bas (4 items)** : Accueil / Historique / Relevé / Profil, icône + libellé, item actif en bleu.
- **Champ de saisie** : rectangle arrondi, bordure fine, halo bleu au focus.
- **Bouton primaire** : pleine largeur (moins les marges), fond dégradé bleu, texte blanc gras, ombre bleue portée.
- **Bouton secondaire/ghost** : bordure fine, texte bleu, pas de fond.
- **Bannière d'alerte (ex. KYC en attente)** : fond crème/doré pâle, pastille dorée, texte court.
- **Badge de statut** : pastille colorée (vert=validé, orange=en attente, rouge=rejeté).
- **Feuille de sélection (bottom sheet)** : utilisée pour choisir pays, banque, société, contact — liste + barre de recherche en haut.
- **Clavier PIN** : grille 3×3 + options empreinte/effacer, points de progression au-dessus.
- **Écran de succès** : anneau vert avec coche, titre court, ID de transaction en police OCR A, bouton retour + lien "voir le reçu".

## 3. Langage d'interaction & animation

La première maquette a établi ces principes — à répliquer, pas à réinventer :

1. **Révélation au défilement** : les sections/écrans apparaissent en fondu + léger décalage vertical à l'entrée dans le viewport, en cascade pour les éléments d'un même groupe. Discret, jamais brusque.
2. **Fil de parcours animé** : pour les séquences à étapes explicites (ex. les 4 étapes d'un module financier), un connecteur animé (point qui voyage le long d'un trait) relie visuellement les écrans dans l'ordre — réservé aux vraies séquences, pas décoratif ailleurs.
3. **Retour au tap** : boutons, items de liste, chips ont un léger effet d'enfoncement (scale ~0.95) au tap — jamais de saut brusque.
4. **Compteurs animés** : les montants importants (solde) peuvent s'incrémenter à l'affichage plutôt qu'apparaître statiques.
5. **Confirmation animée** : l'écran de succès dessine sa coche (trait qui se trace), pas une icône statique.
6. **Pastilles vivantes** : points de notification/statut non lus pulsent doucement (jamais clignotant agressif).
7. **Respect de `prefers-reduced-motion`** : toute animation non essentielle doit avoir un équivalent statique instantané.
8. **Un seul mode clair** : l'app d'origine est **light-mode uniquement** — ne pas concevoir de variante sombre des écrans eux-mêmes (un éventuel thème sombre serait une décision de scope à part, pas un prérequis).

## 4. Inventaire complet des écrans à concevoir

> Regroupés par domaine fonctionnel. Pour chaque écran : objectif, état/contenu clé, règles UI notables. Les 12 écrans déjà maquettés dans le premier jet sont marqués **[déjà fait]** — à reproduire à l'identique dans le style déjà validé plutôt qu'à réinventer ; tous les autres sont à concevoir dans ce même style.

### 4.1 Démarrage & authentification

1. **Démarrage (Splash)** **[déjà fait]** — écran de marque plein écran, logo centré.
2. **Onboarding** **[déjà fait]** — carrousel 3 slides (illustration + titre + sous-titre), pagination à points, bouton Suivant + lien Passer.
3. **Connexion / Inscription (écran combiné)** **[Connexion déjà faite]** — bascule entre connexion (retour) et inscription (nouveau numéro) : sélecteur pays + téléphone, champ PIN (icône biométrie si activée) pour connexion ; pour inscription : mêmes champs + après soumission, étape OTP (6 chiffres, minuteur de réexpédition) puis formulaire de complétion de profil (nom, email, adresse, ville, état, code postal, PIN + confirmation PIN).
4. **Activation biométrie** — toggle actif/inactif, ré-saisie du PIN requise pour activer, détection du type disponible (Face ID / empreinte).
5. **Mot de passe/PIN oublié** — flux 3 étapes : pays+téléphone → code OTP → nouveau PIN + confirmation.
6. **Vérification email** — champ OTP 6 chiffres, minuteur, renvoi de code.
7. **Configuration 2FA (activer/désactiver)** — QR code + clé secrète à scanner, champ code à 6 chiffres ; écran séparé de désactivation (juste le code).
8. **Vérification 2FA** (au login si activé) — champ code à 6 chiffres.
9. **KYC** — formulaire **dynamique** (les champs viennent du serveur : texte, sélection, case à cocher, radio, date, upload de fichier), avec état "déjà vérifié" et état "en attente de revue" distincts, upload de documents avec aperçu.

### 4.2 Accueil & tableau de bord

10. **Coquille du tableau de bord** — conteneur avec 4 onglets bas + tiroir latéral (drawer) : avatar/nom/téléphone utilisateur, raccourci QR code, déconnexion.
11. **Accueil** **[déjà fait]** — carte de solde, bannière statut KYC, grille de services (jusqu'à ~14 modules avec "voir plus/moins"), carrousel de bannières promo, liste d'offres de paiement, transactions récentes.
12. **Liste des offres promotionnelles** — liste paginée complète (accessible depuis "voir tout" sur la bannière accueil).

### 4.3 Profil & paramètres

13. **Hub Profil & Paramètres** — écran de liens vers tous les sous-écrans ci-dessous, avec résumé profil en haut.
14. **Informations du profil** (lecture seule) — nom, email, téléphone, adresse affichés ; bouton vers édition.
15. **Modifier le profil** — formulaire prénom/nom/adresse/ville/état/code postal + photo (upload + recadrage).
16. **Sécurité** (hub) — liens vers changement de PIN et 2FA.
17. **Changer le PIN** — PIN actuel + nouveau PIN + confirmation.
18. **Supprimer le compte** — action irréversible gated par ressaisie du PIN, avertissement clair.
19. **Paramètres de notification** — interrupteurs par catégorie de notification.
20. **Confidentialité** — page de contenu statique (politique de confidentialité).
21. **Préférences de l'app** — paramètres généraux (ex. langue par défaut, autres préférences).
22. **Contenu de page générique** (CGU, À propos...) — rendu de contenu CMS avec onglets/sélecteur entre plusieurs pages.
23. **Écran de maintenance** — état plein écran affiché quand l'app est en maintenance côté serveur.

### 4.4 QR code

24. **Mon QR code** **[déjà fait]** — QR généré côté serveur (image), nom + téléphone, bouton télécharger.
25. **Scanner un QR** — vue caméra avec cadre de visée animé, résout le code scanné (utilisateur/agent/marchand) pour préremplir un envoi d'argent, un retrait ou un paiement.
26. **Connexion par QR** (sur un second appareil) — variante du scanner dédiée à l'authentification.

### 4.5 Historique & relevés

27. **Historique des transactions** **[déjà fait]** — liste paginée avec filtres (type, sens, recherche par numéro de transaction), item ouvrant une feuille de détail.
28. **Relevés (Statements)** — vue mensuelle avec navigation mois/année, résumé revenus vs dépenses, liste des transactions du mois.

### 4.6 Notifications, FAQ, support

29. **Notifications** **[déjà fait]** — liste paginée, pastille non-lu.
30. **FAQ** — accordéon de questions/réponses.
31. **Sélecteur de langue** — liste de langues avec drapeau, sélection recharge les traductions.
32. **Pas de connexion internet** — état plein écran statique avec bouton réessayer.
33. **Liste des tickets support** — liste paginée avec badge de statut/priorité.
34. **Nouveau ticket** — formulaire sujet + priorité + message + pièces jointes (multi-fichiers/images).
35. **Détails d'un ticket** — fil de messages (utilisateur/support), zone de réponse, pièces jointes, bouton clôturer.

### 4.7 Modules financiers — même motif en 4 étapes pour tous (référence : parcours "Envoyer de l'argent" déjà maquetté)

> Tous les écrans suivants suivent le motif déjà établi : **(1) sélection destinataire/option → (2) montant + détail des frais calculés en temps réel → (3) code PIN (et parfois OTP) → (4) confirmation animée avec ID de transaction**, plus un écran "Historique" dédié par module (liste paginée, même style que l'historique global). Concevoir chaque module en respectant ce motif, en n'illustrant que les écarts spécifiques listés :

36. **Envoyer de l'argent** **[les 4 étapes + succès déjà faits]** — destinataire = utilisateur (recherche par nom/tél, scan QR, ou contact du répertoire) + historique dédié.
37. **Demander de l'argent** — flux bidirectionnel : demander à quelqu'un (mêmes 4 étapes) + onglet séparé "demandes reçues" avec écran d'approbation/rejet dédié + deux historiques (envoyées/reçues) avec compteur de demandes en attente.
38. **Retrait (Cash Out)** — destinataire = un agent (recherche dédiée "agent"), sinon motif identique.
39. **Payer (Make Payment)** — destinataire = un marchand, + champ "référence/note" additionnel dans l'étape montant.
40. **Cartes cadeaux (Gift Card)** — remplace l'étape 1 par un **catalogue** (grille de produits, recherche, filtres pays/catégorie), champs destinataire nom/email/quantité, calcul taux de change + frais.
41. **Recharge mobile (Airtime)** — sélection en cascade pays → opérateur → montants suggérés (chips), calcul du montant crédité via taux de change.
42. **Payer une facture (Bill Pay)** — sélection catégorie → société (avec recherche), montant libre ou dénominations fixes, option "enregistrer ce compte" pour réutilisation, écran de gestion des comptes enregistrés (suppression), téléchargement de reçu PDF.
43. **Virement bancaire (Bank Transfer)** — sélection banque + **formulaire dynamique** (champs selon la banque : IBAN, SWIFT...), écran dédié "ajouter une nouvelle banque", comptes enregistrés réutilisables.
44. **Frais de scolarité (Education Fee)** — catégorie → institut + formulaire dynamique (ex. numéro d'étudiant), reçu PDF.
45. **Don (Donation)** — recherche d'organisation caritative, champs donateur (nom/email/note), interrupteur "masquer mon identité" (don anonyme).
46. **Microfinance** — recherche d'organisme prêteur/ONG, formulaire dynamique, motif identique sinon.
47. **Investissement** — liste de plans d'investissement (cartes extensibles), **slider** pour choisir le montant investi dans les bornes du plan, calcul du rendement/intérêt affiché en temps réel, historique séparé des investissements.
48. **Ajouter de l'argent (Add Money / Dépôt)** — **écart au motif standard** : pas d'étape OTP/PIN — après sélection méthode + montant, redirection vers une **WebView de paiement** (passerelle externe type carte bancaire ou mobile money) intégrée dans l'app, avec écran de retour succès/échec ; historique de dépôts dédié.
49. **Cartes virtuelles** — liste des cartes, création (choix type de détenteur, upload documents d'identité), vue détail d'une carte (transactions, révélation numéro/CVC gated par PIN), ajout de solde sur la carte, annulation de carte.

---

## 5. Notes transverses pour qui va concevoir ces écrans

- **Listes paginées** : Historique, Relevés, Offres, Notifications, Tickets, et chaque historique de module suivent tous le même patron visuel (liste + chargement infini) — un seul design de composant "liste paginée" à décliner partout plutôt que 15 variantes.
- **Formulaires dynamiques** : KYC, Virement bancaire, Frais de scolarité, Microfinance partagent un même moteur de rendu de champs (texte/sélection/case/radio/date/fichier) piloté par le serveur — concevoir un seul système de composants de formulaire dynamique, pas un par écran.
- **Confirmation PIN/OTP** : composant réutilisé identique dans les 12 modules financiers — un seul design à décliner.
- **États à ne pas oublier par écran de liste** : état vide (aucune donnée), état de chargement (squelette/shimmer), état d'erreur réseau.
- **Longueurs configurables** : le nombre de chiffres du PIN et du numéro de téléphone sont définis côté serveur (pas figés) — concevoir les composants pour accueillir une longueur variable.
- **Couleur de marque potentiellement dynamique** : ne pas coder la couleur primaire en dur dans les tokens si le format de livraison le permet.

---

*Ce brief est dérivé de l'audit complet du code Flutter d'origine (`audit-flutter.md`) et de la maquette UX déjà validée (12 écrans). Toute question sur le comportement exact d'un écran non détaillé ici peut être posée avant de concevoir — ne pas inventer de logique métier non mentionnée.*
