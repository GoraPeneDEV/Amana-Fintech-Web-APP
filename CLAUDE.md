# Contexte du projet — Migration Flutter → React Native

Ce fichier est lu automatiquement par Claude Code au début de chaque session. Il doit être tenu à jour après chaque phase de travail. Ne le laisse jamais devenir obsolète : une info fausse ici est pire qu'une absence d'info.

## Objectif du projet

Réécrire l'application mobile Flutter en React Native, à l'identique :
- Même design (couleurs, typographie, spacing, disposition)
- Même logique métier (validations, règles, comportements)
- Même backend Laravel, **inchangé, jamais modifié**
- Aucune fonctionnalité ajoutée ou supprimée par rapport à l'app Flutter

## Emplacements

- Projet Flutter d'origine (référence, lecture seule) : `./Flutter APP/User` (package `ovopay`, app mobile financière/wallet)
- Projet Laravel (référence API, lecture seule) : `./core` (routes API mobiles dans `core/routes/api/api.php`, montées sur préfixe `/api`)
- Nouveau projet React Native (à construire) : `./app-react-native`

## Stack technique choisie pour React Native

- Framework : **React Native CLI** (bare) — confirmé par l'utilisateur le 2026-07-28 (pas Expo). Conséquence : les modules natifs (Firebase Messaging, notifications locales riches, biométrie, scanner QR, image/document picker + crop, contacts, secure storage, webview in-app, téléchargement/ouverture de fichiers) devront être intégrés via leurs équivalents RN CLI classiques avec link natif (`react-native-firebase`, `react-native-biometrics` ou `react-native-keychain` biometry, `react-native-vision-camera`/`react-native-camera-kit` pour le QR, `react-native-image-picker`, `react-native-image-crop-picker`, `react-native-contacts`, `react-native-keychain`, `react-native-webview`, etc.) — pas de config-plugins Expo.
- Navigation : React Navigation — `bottom tabs` (4 onglets : Home / Historique / Relevé / Profil, équivalent du `IndexedStack` + `GNav` Flutter) + `stack` par domaine fonctionnel (Auth, chaque module financier étant un mini-wizard en stack) + `drawer` pour le menu latéral (avatar, raccourci QR, logout) — voir `audit-flutter.md` §4 pour le détail des ~30 écrans et 45 routes GetX à reproduire.
- Gestion d'état : Flutter utilise **GetX** en mode `GetxController` + `update()` (pattern impératif simple, pas de flux réactif `.obs`/`Obx`) — pas de store global complexe. Équivalent recommandé côté RN : **Zustand** (state léger par feature, proche du pattern `GetxController` par écran) + **Context API** pour l'état transverse minimal (session utilisateur, thème). Pas besoin de Redux Toolkit vu la simplicité du state management d'origine.
- Client API : axios avec intercepteur pour l'authentification (reproduit `ApiService`/Dio de Flutter : ajout automatique `Authorization: Bearer <token>`, gestion 401 → déconnexion)
- Stockage local : AsyncStorage pour la majorité des données (à l'identique de `shared_preferences`, y compris le token d'accès — voir `audit-flutter.md` §11 point 1 sur le fait que le token n'est pas chiffré côté Flutter, à valider si on reproduit ce choix ou si on l'améliore) ; `react-native-keychain` ou `expo-secure-store` pour reproduire l'usage `flutter_secure_storage` (PIN, solde en cache — le mécanisme PIN a un bug identifié côté Flutter, voir `audit-flutter.md` §11 point 2, à clarifier avant portage)
- Backend : Laravel, communication via **Laravel Sanctum** (confirmé dans `core/composer.json` et `core/routes/api/api.php` : middleware `auth:sanctum` + `token.permission:user_token` custom, token Bearer sur toutes les routes protégées). Aucun mécanisme de refresh token identifié côté Flutter (déconnexion directe sur 401) — à confirmer côté backend avant de décider si le RN doit gérer un refresh ou reproduire ce comportement à l'identique.

## Règles impératives

1. **Ne jamais modifier le dossier Laravel** pour la logique métier, les routes ou les contrôleurs — lecture seule par défaut, uniquement pour comprendre les routes API (`routes/api.php`) et les contrôleurs. **Exceptions ponctuelles validées explicitement par l'utilisateur** (voir journal des décisions pour le détail des fichiers touchés à chaque fois) :
   - `2026-07-28` — renommage de marque « OvoPay » → « Amana », nom affiché uniquement.
   - `2026-07-29` — ajout des passerelles de paiement PawaPay et InTouch dans le module Add Money, suivant le pattern déjà utilisé pour Stripe.
   - `2026-07-30` — refonte complète des templates de notification (contenu + habillage visuel de l'email), voir journal.
   Toute autre modification du dossier Laravel doit repasser par une validation explicite de l'utilisateur avant d'être effectuée.
2. **Ne plus considérer le dossier Flutter d'origine comme lecture seule.** Décision du `2026-07-30` (validée par l'utilisateur) : les deux pistes — app Flutter existante et future réécriture React Native — sont désormais menées **en parallèle**. L'app Flutter (`./Flutter APP/User`) est activement configurée/maintenue comme app de production court terme (rebranding AMANA, connexion au backend réel `fintech.gorapene.com`, Firebase propre — voir journal du 2026-07-30). Le projet React Native (`./app-react-native`) reste à créer ; l'audit et le brief design existants restent valables comme référence pour le jour où ce chantier reprendra.
3. Avant de migrer un écran, toujours relire le fichier Flutter correspondant pour ne rien oublier (états, conditions, messages d'erreur).
4. Réutiliser les composants déjà créés dans `src/components` plutôt que d'en dupliquer.
5. Respecter le thème centralisé dans `src/theme` — ne jamais coder une couleur ou une taille en dur dans un écran.
6. Après chaque lot d'écrans migré, mettre à jour la section "État d'avancement" ci-dessous.

## État d'avancement

> À mettre à jour après chaque phase. Format : `[x]` fait, `[ ]` à faire, `[~]` en cours.

### Phase 0 — Audit
- [x] `audit-flutter.md` généré (2026-07-28) — en attente de validation utilisateur

### Phase 1 — Setup
- [ ] Squelette React Native créé
- [ ] Navigation de base fonctionnelle
- [ ] Client API connecté et testé sur une route réelle
- [ ] Thème (couleurs, typo, spacing) reproduit

### Phase 2 — Écrans migrés
- [ ] Auth (splash, onboard, login/register, biométrie, forgot pin, email verification, 2FA setup/verify, KYC)
- [ ] Dashboard/Home (shell + bottom nav + drawer, home, offres promo)
- [ ] Profil/Paramètres (hub, info, edit, security/change pin, delete account, notifications settings, privacy, app preferences, page content)
- [ ] QR Code (my code, scan, login)
- [ ] Historique (transaction history, statements)
- [ ] Notifications / FAQ / Langue / No internet / Maintenance
- [ ] Support Ticket (liste, nouveau, détails)
- [ ] Modules financiers (12) : Send Money, Request Money, Cash Out, Make Payment, Gift Card, Airtime Recharge, Bill Pay, Bank Transfer, Education Fee, Donation, Microfinance, Investment
- [ ] Add Money (+ webview paiement)
- [ ] Virtual Cards

> Détail complet de chaque écran (fichiers, state, validations, navigation, API) dans `audit-flutter.md` §4.

### Phase 3 — Transverse
- [ ] Notifications push
- [ ] Upload/affichage images
- [ ] Géolocalisation
- [ ] Offline/cache
- [ ] Deep linking

### Phase 4 — QA
- [ ] Checklist QA créée
- [ ] Checklist QA validée

## Décisions techniques prises (journal)

> Ajoute une ligne ici à chaque choix technique important, pour ne pas le redemander/redécider plus tard.

- `[date]` — exemple : "State management : Zustand choisi car l'app Flutter utilisait Provider de façon simple, pas besoin de Redux."
- `2026-07-28` — Audit Flutter/Laravel complet réalisé (`audit-flutter.md`). Auth confirmée = Laravel Sanctum (pas Passport/JWT). State management Flutter confirmé = GetX impératif (`GetxController`/`update()`, pas de flux réactif) → recommandation Zustand + Context API pour le RN (à valider). Plusieurs anomalies/routes mortes détectées côté Flutter/Laravel (token non chiffré, bug clé PIN secure storage, endpoint `request/money-approve` introuvable côté Laravel, méthode `InvestmentRepo.makeInvestmentHistory` buguée, etc.) — voir `audit-flutter.md` §11, à trancher au cas par cas avant portage, ne pas corriger silencieusement.
- `2026-07-28` — Framework RN confirmé par l'utilisateur : **React Native CLI** (bare), pas Expo. Une maquette UX haute-fidélité (12 écrans clés) a été produite en artifact à partir de la palette/typo/patterns de l'audit, sous le nom provisoire « Amana » en anticipation du renommage de marque demandé par l'utilisateur — aucun code (Flutter, Laravel, RN) créé à cette étape, uniquement la maquette visuelle.
- `2026-07-28` — Renommage de marque **OvoPay → Amana** appliqué directement dans le code source Laravel (exception validée par l'utilisateur, périmètre = nom affiché uniquement) :
  - `install/database.sql` : `general_settings.site_name` (`OvoPay` → `Amana`, c'est le nom sitewide utilisé par `general-setting`/API mobile), SEO/social title, contenus CMS de la landing page (`how_to_work.content`, `cta.content`, `testimonial.element`, `faq.element`, `policy_pages.element`, `contact_us.content`) — remplacement littéral `OvoPay` → `Amana` (41 occurrences).
  - `core/app/Http/Helpers/helpers.php` — `systemDetails()['name']` (affiché uniquement sur la page admin « System Info ») : `'ovopay'` → `'Amana'`.
  - `core/resources/lang/{bn,es,fr,it,ja,pt,ru,tr}.json` — mêmes textes marketing traduits (clés ET valeurs) : `OvoPay` → `Amana` (60 occurrences/fichier), pour garder la correspondance de clé de traduction cohérente avec le contenu CMS renommé.
  - **Volontairement non touché à cette étape** (hors périmètre « nom affiché uniquement », à trancher séparément si besoin) : email `support@ovopay.com`, commentaire de nom de base de données dans le dump SQL (`-- Database: \`ovopay\``), logo/assets visuels, palette de couleurs (bleu `#2B5BEE`/or `#EEBE2B` inchangée). Le domaine `ovosolution.com` a ensuite été traité séparément le 2026-07-29 (voir entrée ci-dessous).
  - Pas de logo/couleurs changés (non demandé). Ce changement affecte le fichier d'installation (`install/database.sql`) — il ne se répercute sur une base de données déjà installée que si le site est réinstallé ou si la ligne `general_settings` est mise à jour manuellement/via l'admin.
- `2026-07-29` — Intégration de **PawaPay** et **InTouch** comme nouvelles méthodes « Add Money », en suivant exactement le pattern data-driven déjà utilisé par Stripe/MercadoPago/Flutterwave (voir blueprint établi par audit du code existant : `gateways`/`gateway_currencies` en DB + `App\Http\Controllers\Gateway\<Alias>\ProcessController` résolu dynamiquement par `Gateway\PaymentController::depositConfirm()`, aucun changement nécessaire dans les contrôleurs/routes/models partagés).
  - Fichiers créés : `app/Http/Controllers/Gateway/PawaPay/ProcessController.php`, `app/Http/Controllers/Gateway/InTouch/ProcessController.php`, vues `resources/views/templates/basic/{user,agent}/payment/{PawaPay,InTouch}.blade.php`.
  - Fichier modifié : `routes/ipn.php` (ajout des routes webhook `pawapay`/`in-touch` + routes de polling de statut `pawapay/status/{id}` et `in-touch/status/{trx}`).
  - Seed DB (`install/database.sql`) : lignes `gateways` (code 126 PawaPay, code 127 InTouch) et `gateway_currencies` (XOF pour les deux) — **désactivées par défaut** (`status = 2`) et **sans identifiants** (champs vides), à activer manuellement depuis `admin/gateway/automatic` une fois les vraies clés API saisies — aucun changement de code nécessaire pour ça, le formulaire admin est générique.
  - **Différence de UX par rapport à Stripe** : PawaPay et InTouch ne sont pas des passerelles à page hébergée (pas de redirection vers une page de paiement) — ce sont des push mobile money côté serveur (l'utilisateur confirme sur son téléphone via une invite USSD). La vue de confirmation affiche donc un écran d'attente qui interroge (`polling` JS, toutes les 4s, ~4 min max) une petite route de statut jusqu'à confirmation, puis redirige vers `success_url`/`failed_url` — c'est ce mécanisme qui permet à la WebView React Native existante (qui détecte le succès par pattern d'URL) de fonctionner sans modification.
  - **Points non vérifiés en conditions réelles, à confirmer avant mise en production** :
    - PawaPay : les codes `country`/`correspondent` par corridor (ex. opérateur Orange/MTN par pays) doivent être saisis par l'admin depuis son tableau de bord PawaPay — je n'ai pas la liste exhaustive et fiable de ces codes, donc rien n'est pré-rempli.
    - InTouch : l'URL de base utilisée (`https://apidist.gutouch.net/apidist/sec/{agency_code}/cashin`) est celle documentée pour les pays hors Tanzanie ; à confirmer/ajuster si le corridor cible est la Tanzanie (`api-tz.gutouch.net`) ou un autre pays. Le endpoint utilisé est **« Transfer » (`/cashin`)**, confirmé par l'utilisateur — les endpoints « Direct API »/« Checkout page » du portail InTouch n'ont pas pu être documentés (portail en SPA Angular résistant au scraping automatisé) et n'ont donc pas été utilisés.
    - Format exact du numéro de téléphone (`$user->mobile`) attendu par InTouch (avec/sans zéro initial) non testé en conditions réelles — normalisation défensive appliquée (`ltrim($user->mobile, '0')`) mais à vérifier avec de vraies données utilisateur.
    - Vérification de signature de callback non implémentée pour aucune des deux passerelles (PawaPay la propose en option via RFC-9421, non documentée assez précisément pour l'implémenter fiablement ; InTouch ne documente pas de mécanisme de signature) — la protection actuelle se limite à l'idempotence déjà présente dans `PaymentController::userDataUpdate()` (un webhook rejoué ne peut pas créditer deux fois), ce qui est acceptable mais pas équivalent à une vérification de signature.
- `2026-07-29` — Remplacement du domaine **ovosolution.com → amanagroupe.com** sur le site (même périmètre « nom affiché »/contenu, exception validée par l'utilisateur) : `install/database.sql` (lien de téléchargement APK dans `cta.content`, email `info@ovosolution.com` devenu l'adresse d'expédition système `general_settings.email_from`, mention `support@ovosolution.com` dans un contenu FAQ/CMS, `src` d'image logo dans un template d'email) + mêmes textes dans les 8 fichiers `core/resources/lang/{bn,es,fr,it,ja,pt,ru,tr}.json`. Remplacement littéral du domaine uniquement (pas des segments de chemin `/ovopay/...`, non demandés).
  - **À vérifier par l'utilisateur** : `info@ovosolution.com` devient l'adresse `From` réelle des emails système (`general_settings.email_from`) — si `amanagroupe.com` n'a pas de boîte `info@` ni d'enregistrements SPF/DKIM configurés, les emails sortants risquent d'échouer ou d'arriver en spam. Le `src` d'image dans le template d'email (`preview.amanagroupe.com/ovopay/demo/assets/images/logo_icon/logo.png`) pointait vers une image hébergée par le vendeur d'origine — le changement de domaine seul ne fera pas apparaître une image réelle à cette adresse sur `amanagroupe.com` sauf si ce chemin exact y est effectivement hébergé ; à remplacer par un vrai logo hébergé si besoin.
  - Volontairement non touché : `support@ovopay.com` (domaine différent, non mentionné dans cette demande), Flutter apps (`Flutter APP/{User,Agent,Merchant}` référencent aussi `ovosolution.com`/`com.ovosolution.*` dans leurs identifiants de package, config Firebase et `environment.dart` — hors périmètre car (a) la demande visait explicitement « le site web » et (b) la règle « jamais modifier le dossier Flutter » s'applique sans exception ici, contrairement à Laravel.
- `2026-07-30` — Configuration directe de l'app Flutter User (exception validée par l'utilisateur, décision de mener Flutter et React Native en parallèle — voir règle 2 ci-dessus) :
  - `lib/environment.dart` : `appName` → `AMANA`, `LIVE_API_URL`/`TEST_API_URL` → `https://fintech.gorapene.com` (vrai backend Amana, vérifié en ligne via `GET /api/general-setting` : `site_name: Amana`, `base_color: 2b5bee`, `maintenance_mode: 0`).
  - Libellé affiché : `AMANA` dans `AndroidManifest.xml` (`android:label`) et `ios/Runner/Info.plist` (`CFBundleDisplayName`).
  - Package Android / bundle iOS renommés `com.ovosolution.ovopay` → `com.amanabank.clientapp` (demandé explicitement par l'utilisateur) : `android/app/build.gradle.kts` (namespace + applicationId), `MainActivity.kt` déplacé vers `android/app/src/main/kotlin/com/amanabank/clientapp/`, `android/app/google-services.json` (`package_name`), `ios/Runner.xcodeproj/project.pbxproj` (6 occurrences `PRODUCT_BUNDLE_IDENTIFIER`), `lib/firebase_options.dart` (`iosBundleId`).
  - Firebase reconfiguré sur le vrai projet utilisateur **amana-ff5e8** (fourni par l'utilisateur, fichiers `google-services.json` Android et `GoogleService-Info.plist` iOS) : `android/app/google-services.json` remplacé intégralement, `lib/firebase_options.dart` (blocs `android` et `ios` : apiKey/appId/messagingSenderId/projectId/storageBucket), `firebase.json`, `GoogleService-Info.plist` ajouté dans `ios/Runner/` et câblé dans `project.pbxproj` (PBXFileReference + PBXBuildFile + groupe Runner + phase Resources, car pas d'Xcode disponible sur Windows pour le faire via l'IDE).
  - **Non fait, en attente** : logo/icône réels (l'utilisateur a envoyé le fichier logo AMANA dans le chat à deux reprises, mais aucune image de conversation collée ne peut être capturée comme fichier par les outils disponibles ici — il faut un chemin de fichier sur disque, comme cela a été fait pour les fichiers Firebase). Reconfiguration Firebase iOS **native** (l'app iOS elle-même reste à valider en conditions réelles, jamais buildée/testée ici, pas d'environnement macOS/Xcode disponible).
  - `flutter pub get` et `flutter analyze` passent sans erreur après tous ces changements.
- `2026-07-30` — Refonte complète des templates de notification en français, habillage visuel professionnel AMANA (exception validée explicitement par l'utilisateur, périmètre = contenu des templates + template email global, pas de logique métier) :
  - Contexte technique découvert en auditant `app/Notify/NotifyProcess.php` : le template email **global** (`general_settings.email_template`, éditable dans `admin/notification/global/email`) sert de **layout maître** — chaque template d'événement (`notification_templates.email_body`) est injecté dedans via le short-code `{{message}}`. Un seul habillage visuel (logo, couleurs, pied de page) à maintenir, pas un par événement.
  - `install/database.sql` modifié via un script Node.js dédié (tokenizer SQL maison respectant guillemets/échappements, car les lignes `INSERT` font plusieurs Ko chacune) :
    - `general_settings` : `email_template` (nouveau layout HTML — dégradé bleu Amana `#2B5BEE`→`#173583`, carte blanche arrondie, section logo, pied de page marine), `sms_template`, `push_template`, `push_title` — tous réécrits en français.
    - `notification_templates` : les **48 templates par événement** (sur 49 — `DEFAULT` volontairement inchangé car c'est un pur passe-plat `{{subject}}`/`{{message}}` sans texte à traduire) réécrits en français professionnel (`subject`, `email_body`, `sms_body`, `push_title`, `push_body`), en réutilisant exactement les mêmes short-codes que la version anglaise d'origine (vérifiés un par un depuis le contenu réel de chaque template, pas depuis la colonne `shortcodes` qui s'est révélée incomplète pour au moins un cas — `MONEY_REQUEST_REJECT` déclarait `{}` alors que `{{amount}}`/`{{from_user}}`/`{{time}}` sont bien utilisés en pratique).
    - Amélioration par rapport à l'original : plusieurs `push_title`/`push_body` qui étaient `null` dans le seed vendeur (ex. `SEND_MONEY`, `CASH_OUT`, `DONATION`, `REQUESTED_MONEY_RECEIVED`, `SEND_OTP`) ont maintenant un vrai contenu français.
    - Validation faite : re-parsing du fichier généré (49/49 lignes `notification_templates` cohérentes, 0 mismatch de colonnes), comptage `INSERT INTO`/`CREATE TABLE` identique avant/après (44 et 81, aucune autre table touchée), sauvegarde de l'original conservée (`install/database.sql.bak-<timestamp>`). Pas de serveur MySQL local disponible pour un import réel de test (WAMP arrêté) — validation limitée à la cohérence syntaxique/structurelle du fichier, pas à un import MySQL réel.
  - **Logo dans l'email** : en l'absence du fichier logo (voir point Flutter ci-dessus), le bandeau logo utilise un texte stylisé « AMANA » en CSS (`master_templates.js` conserve une version `email_template_raw` avec un point d'insertion `LOGO_IMG_PLACEHOLDER` pour swap ultérieur en `<img>` — base64 recommandé pour éviter toute dépendance à un hébergement externe non garanti). À refaire dès que le fichier logo est disponible sur disque.
  - **Précision** : `notification_templates` est une table **unique et partagée** par toute la plateforme (User + Agent + Merchant) — confirmé par la présence dans les 48 templates traités de codes clairement agent/merchant (`CASH_IN_AGENT`, `CASH_IN_COMMISSION_AGENT`, `CASH_OUT_COMMISSION_AGENT`, `CASH_OUT_TO_AGENT`, `MAKE_PAYMENT_RECEIVE`). Les notifications Agent/Merchant sont donc déjà couvertes par ce même travail, pas besoin d'un passage séparé.
