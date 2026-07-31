# Audit de l'application Flutter « OvoPay » (`./Flutter APP/User`)

> Généré le 2026-07-28. Audit exhaustif en lecture seule de l'app Flutter (package `ovopay`, wallet/MFS mobile) et de son backend Laravel (`./core`), en préparation de la migration vers React Native. Aucun fichier Flutter ni Laravel n'a été modifié pour produire ce document.

## Sommaire

1. [Vue d'ensemble & stack détectée](#1-vue-densemble--stack-détectée)
2. [Architecture & state management](#2-architecture--state-management)
3. [Authentification & sécurité (Laravel Sanctum)](#3-authentification--sécurité-laravel-sanctum)
4. [Inventaire complet des écrans](#4-inventaire-complet-des-écrans)
5. [Composants réutilisables](#5-composants-réutilisables)
6. [Inventaire complet des appels API](#6-inventaire-complet-des-appels-api)
7. [Dépendances natives](#7-dépendances-natives)
8. [Thème / design system](#8-thème--design-system)
9. [Assets](#9-assets)
10. [Localisation / i18n](#10-localisation--i18n)
11. [Anomalies, routes mortes & points d'attention](#11-anomalies-routes-mortes--points-dattention)
12. [Implications pour la stack React Native](#12-implications-pour-la-stack-react-native)

---

## 1. Vue d'ensemble & stack détectée

| Aspect | Constat |
|---|---|
| Nom du package | `ovopay` — « Complete Cross Platform MFS Solution » (mobile financial services / e-wallet) |
| State management | **GetX** (`get: ^4.7.3`) — `GetxController` + `update()` (builder pattern), **pas** de `.obs`/`Obx` réactif trouvé dans les écrans |
| Navigation | GetX (`GetPage`/`Get.toNamed`/`Get.offAllNamed`), routes déclarées dans `lib/core/route/route.dart` (45 `GetPage`, ~30 fonctionnalités) |
| Client HTTP | `dio: ^5.9.0`, encapsulé dans un service statique unique `ApiService` (`lib/core/data/services/api_service.dart`) |
| Auth backend | **Laravel Sanctum** (token Bearer), middleware `auth:sanctum` + `token.permission:user_token` custom |
| Stockage token | `shared_preferences` (**non chiffré** — voir §11) |
| Stockage sécurisé | `flutter_secure_storage` (utilisé seulement pour le PIN et le solde en cache — usage bugué, voir §11) |
| Backend | Laravel (`./core`), routes API dans `core/routes/api/api.php`, montées sur préfixe `/api` |
| i18n | Traductions **entièrement pilotées par le backend** (pas de fichiers `.arb`/`.json` statiques) — voir §10 |
| Thème | Light-mode uniquement, couleur primaire **configurable côté serveur** — voir §8 |
| Design responsive | `flutter_screenutil` (`.w`, `.h`, `.sp`, `.r`) partout |

---

## 2. Architecture & state management

- Chaque fonctionnalité vit dans `lib/app/screens/<feature>/` avec un split `controller/` (un ou plusieurs `GetxController`) et `views/` (widgets Flutter). Les modules de type formulaire ont aussi `widgets/`/`sections/`.
- **Pattern dominant** : la quasi-totalité des écrans "métier" (12 modules d'argent) suivent un même flux en étapes :
  1. Chargement des infos/limites/charges (`GET .../create`)
  2. Sélection destinataire (utilisateur / agent / marchand / banque / organisation) + saisie montant, avec calcul de frais **client-side** répliquant la formule serveur
  3. Étape OTP optionnelle (email/SMS, `verification-process/verify/otp`)
  4. Étape PIN obligatoire (`verification-process/verify/pin`), longueur dynamique via `SharedPreferenceService.getMaxPinNumberDigit()`
  5. Dialog de succès avec ID de transaction
- **Formule de frais répétée** (~10 contrôleurs) : `total = montant*pourcentage/100 + frais_fixe`, plafonné par un `cap` sauf si `cap == -1`.
- **Formulaires dynamiques** : KYC, Bank Transfer, Education Fee et Microfinance partagent un moteur générique (`KycFormModel` / `GlobalDynamicFormController`) qui rend des champs `text`, `select`, `checkbox`, `radio`, `file`, `date/time` définis côté serveur, avec validation `required` par champ et pré-remplissage si déjà soumis.
- Longueur du PIN et du numéro de mobile sont **configurables côté serveur** (pas de valeur en dur côté client).
- Le champ OTP est un widget dédié 6 chiffres (`OTPFieldWidget`, package `pin_code_fields`).

---

## 3. Authentification & sécurité (Laravel Sanctum)

**Mécanisme confirmé : Laravel Sanctum** (`laravel/sanctum: ^4.0` dans `core/composer.json`). Les routes API mobiles (`core/routes/api/api.php`, montées avec le préfixe `/api` dans `core/bootstrap/app.php`) sont protégées par le middleware `auth:sanctum` combiné à un middleware custom `token.permission:user_token` (vérifie l'ability du personal access token). Le token est envoyé par le client comme `Authorization: Bearer <token>` (intercepteur Dio, voir `api_service.dart`).

Chaîne de middlewares métier appliquée progressivement selon l'étape d'onboarding : `mobile.verify` → `registration.complete` → `check.status` (KYC) → `module:<slug>` (feature flag par module) → `kyc`.

### Flux d'authentification bout-en-bout

1. **Entrée login/register** : `POST authentication` avec `country` + `mobile_number` (sans PIN) → le serveur envoie un OTP et crée un utilisateur non vérifié si nouveau ; avec `pin` pour un utilisateur déjà vérifié → retourne directement un token Sanctum.
2. Le token est persisté via `SharedPreferenceService.setString(accessTokenKey, token)` — **shared_preferences en clair, non chiffré**.
3. `GET authorization` — le serveur inspecte l'état utilisateur (`sv` sms vérifié, `ev` email vérifié, `tv` 2FA vérifié, complétude du profil) et retourne l'étape suivante à présenter (`sms`/`email`/`2fa`/`already_verified`/`complete_your_profile`).
4. **OTP mobile/SMS** : `POST verify-mobile` (code).
5. **Complétion du profil** (si email manquant) : `POST user-data-submit` (nom/email).
6. **OTP email** : `POST verify-email` (code), gated par `mobile.verify` + `registration.complete`.
7. **2FA** (si activé) : `POST verify-g2fa` (code).
8. **KYC** : `GET kyc-form` puis `POST kyc-submit` (multipart, champs dynamiques), gated par `mobile.verify` + `registration.complete` + `check.status`.
9. Une fois totalement vérifié → accès à `GET dashboard` etc. (dashboard/user-info explicitement `withoutMiddleware('kyc')`, mais chaque module financier applique son propre gate `kyc`).
10. **Logout** : `GET logout` — invalide le token Sanctum côté serveur ; le client vide son `SharedPreferenceService`.

La confirmation PIN/OTP générique (`verification-process/verify/pin` et `/verify/otp`) est réutilisée par les 12 modules financiers avant chaque soumission (`remark` identifie le module concerné).

---

## 4. Inventaire complet des écrans

Routes déclarées dans `RouteHelper` (`lib/core/route/route.dart`) — 45 `GetPage` au total. Regroupées ci-dessous par domaine fonctionnel.

### Bootstrap / Auth

| Écran | Fichiers (controller/vue) | État local (GetxController) | Règles de validation | Navigation | Appels API |
|---|---|---|---|---|---|
| **Splash** | `splash/controller/splash_controller.dart`, `splash/views/splash_screen.dart` | `isLoading`, `isMaintenance` | — | → onboard / login / dashboard selon état | `GeneralSettingRepo.getGeneralSetting/getLanguage/getCountryList/getModuleList` |
| **Onboard** | `onboard/controller/onboard_controller.dart`, `onboard/views/onboard_screen.dart` + `widget/onboard_body.dart` | 3 slides statiques, `currentIndex`, `PageController` | — | → login | aucun |
| **Login / Register** (écran combiné) | `auth/login/controller/login_controller.dart` (étend `BioMetricController`), `auth/login/views/login_screen.dart` ; `auth/register/controller/registration_controller.dart`, `auth/register/views/register_screen.dart` + `profile_complete_screen.dart` | Login : `countryController/mobileController/pinController`, `countryData`, `isSubmitLoading`, `errors`. Register : `otpController, pinController, cPinController, uNameController, emailController, fNameController, lNameController, addressController, stateController, zipCodeController, cityController`, timer OTP, `isOtpExpired` | Téléphone requis, longueur exacte = `getMaxMobileNumberDigit()` ; PIN requis, longueur exacte = `getMaxPinNumberDigit()` ; digits-only ; OTP non vide | → forgotPin, registration (avec `UserModel`), emailVerification/twoFactor/kyc/dashboard selon `checkUserStatusAndGoToNextStep` | `LoginRepo.loginUser/registerUser/sendAuthorizationRequest`, `RegistrationRepo.sendAuthorizationRequest/verifySmsOtp/resendVerifyCode/completeProfile` |
| **Biometric setup** | `auth/biometric/controller/biometric_controller.dart` (`BioMetricController`), `auth/biometric/setup_biometric_screen.dart` | `isDeviceSupportBiometric, isBiometricEnabled, availableBiometrics, hasFaceID, hasFingerprint, pinCodeController, isShowBioMetricAccountPinBox, isPinValidateLoading` | PIN vérifié côté serveur avant activation | toggle in-place | `BiometricRepo.checkPinOfAccount` |
| **Forgot PIN** | `auth/forget_pin/controller/forget_pin_controller.dart`, `auth/forget_pin/views/forget_pin_screen.dart` | `countryController, mobileController, otpController, pinController, cPinController`, `submitLoading, isLoading, resendLoading` | — | → login | `LoginRepo.forgetPassword/verifyForgetPassCode/resetPin` |
| **Email verification** | `auth/email_verification_page/controller/email_verification_controler.dart`, `views/email_verification_screen.dart` | `otpController`, timer/`isOtpExpired`, `submitLoading, resendLoading` | — | suite du state machine d'onboarding | `SmsEmailVerificationRepo.sendAuthorizationRequest/verify/resendVerifyCode` |
| **2FA setup & verify** | `two_factor/two_factor_setup_screen/controller/two_factor_controller.dart` + vues (`two_factor_setup_screen.dart`, `sections/two_factor_enable_section.dart`, `sections/two_factor_disable_section.dart`, `widget/enable_qr_code_widget.dart`) ; `two_factor/two_factor_verification_screen/two_factor_verification_screen.dart` | `submitLoading, currentText, twoFactorCodeModel, isLoading` | code 6 chiffres | — | `TwoFactorRepo.get2FaData/enable2fa/disable2fa/verify` |
| **KYC** | `auth/kyc/controller/kyc_controller.dart`, `auth/kyc/views/kyc_screen.dart` + `views/section/*` (text/select/radio/checkbox/date-time), `widget/already_verifed.dart`, `widget/kyc_file_item.dart`/`kyc_dynamic_file_item.dart` | `formList: List<KycFormModel>` (dynamique), `isAlreadyVerified, isAlreadyPending, isNoDataFound, pendingData, submitLoading, isLoading` | Requis par type de champ (`hasError()`), whitelist extension fichier (jpg/png/jpeg/pdf/doc/docx/csv/txt/xls/xlsx) | → dashboard | `KycRepo.getKycData/submitKycData` |

### Dashboard / Home

| Écran | Fichiers | État local | Notes |
|---|---|---|---|
| **Dashboard shell** (bottom nav) | `dashboard_screen/views/dashboard_screen.dart` | `IndexedStack` 4 onglets | Bottom nav (`GNav`/google_nav_bar) : **Home, Historique, Relevé, Profil**. Drawer (`widgets/drawer_screen.dart` + `components/drawer/user_drawer_card.dart`) : avatar/nom/tél, raccourci QR, **Logout** (`Get.offAllNamed(login)`, sans confirmation) |
| **Home** | `dashboard_screen/controller/home_controller.dart`, `views/home_screen.dart` + 6 widgets (`home_screen_appbar.dart`, `_balance_card.dart`, `_banner_card.dart`, `_kyc_status_card.dart`, `_payment_offer_list_card.dart`, `_service_menu_card.dart`, `_transaction_list_card.dart`) | `bannersList, offersList, transactionHistoryList, accountBalance, kycStatus, kycReason, isPageLoading, isLoading, isHistoryLoading, isBalanceVisible` (+ timer auto-hide 10s) | Grille de services filtrée par `SharedPreferenceService.getModuleStatusByKey(<module>)` (14 modules). API : `HomeRepo.dashboardInfo/transactionHistory`, `GeneralSettingRepo.getCountryList/getModuleList` |
| **Promotional Offers (liste)** | `dashboard_screen/controller/promotional_offers_list_controller.dart`, `views/promotional_offers_screen.dart` | `promotionalOffersList`, pagination | API : `HomeRepo.promotionalOffersList` |

### Profil / Paramètres

| Écran | Fichiers | État local | Notes |
|---|---|---|---|
| **Hub Profil & Paramètres** | `profile_and_settings_screen/views/profile_and_settings_screen.dart`, `controller/profile_controller.dart` | `firstNameController..countryController` (9 champs), `imageFile, imageUrl, user2faIsOne, userData, isLoading, isSubmitLoading, isLogOutLoading`, sous-état suppression compte (`pinController, isShowDeleteAccountPinBox, isDeleteAccountLoading`) | Point d'entrée vers 12 sous-écrans. API : `ProfileRepo.loadProfileInfo/updateProfile/logout/deleteAccount` |
| **Profile Information** (lecture seule) | `views/profile_information_screen.dart` | — | Affiche données `ProfileController` |
| **Profile Edit** | `views/profile_edit_screen.dart`, `widgets/custom_image_cropper_widget.dart`, `profile_image_with_upload_button_widget.dart` | — | Prénom/nom requis (erreurs `kFirstNameNullError`/`kLastNameNullError`) ; email/tél en lecture seule. API : `ProfileRepo.updateProfile` (multipart) |
| **Security / Change PIN** | `profile_and_settings_screen/controller/change_pin_controller.dart`, `views/security_screen.dart`, `views/change_pin_screen.dart` | `currentPassController, passController, confirmPassController`, `submitLoading, errors` | API : `ChangePasswordRepo.changePassword` |
| **Delete Account** | `views/delete_account_screen.dart` | via `ProfileController` | Suppression irréversible gated par PIN |
| **Notification Settings** | `views/notification_settings_screen.dart` | switches | Pas de controller dédié trouvé |
| **Privacy / App Preferences / Page Content** | `views/privacy_screen.dart`, `views/app_preferences_screen.dart` ; `page_content_screen/controller/page_content_controller.dart`, `views/page_content_screen.dart`, `views/maintenance_content_screen.dart` | `list: List<PagesData>, selectedIndex, selectedHtml, isLoading` | Pages CMS (Politique de confidentialité, CGU, À propos) rendues en HTML/WebView. `maintenance_content_screen` affiché en cas de maintenance. API : `PrivacyRepo.loadAppPagesData` |

### QR Code

| Écran | Fichiers | Notes |
|---|---|---|
| **My QR Code / Scan QR / QR Login** | `my_qr_code_screen/controller/my_qr_code_controller.dart`, `views/my_qr_code_screen.dart`, `views/scan_qr_code_screen.dart`, `views/qr_code_login_screen.dart` | QR **rendu côté serveur** (image téléchargée, pas de génération client). Scan résout un user/agent/merchant côté serveur puis alimente Send Money/Cash Out/Payment. QR login pour un second appareil. API : `ProfileRepo.getMyQrCodeData/downloadMyQrCodeData/scanQrCodeData/qrCodeLogin` |

### Historique / Relevés

| Écran | Fichiers | État local | Notes |
|---|---|---|---|
| **Transaction History** (onglet) | `transaction_history/controller/transaction_history_controller.dart`, `views/transaction_history_screen.dart` + widgets | `transactionHistoryList`, `searchTrxNoController`, `transactionHistoryRemarkList, selectedRemark`, `selectOrderBy, selectTrxType`, pagination | Filtre remark/ordre/type/recherche ; ~20 codes remark mappés en libellés. API : `TransactionHistoryRepo.transactionHistory` |
| **Statements** (onglet) | `statements/controller/statement_history_controller.dart`, `statement_screen.dart` + widgets | `month, monthInText, year, statementsData, isStatementLoading` | Navigation mois/année. API : `TransactionHistoryRepo.statementsHistory` |

### Notifications / FAQ / Langue / Hors-ligne

| Écran | Fichiers | Notes |
|---|---|---|
| **Notifications** | `notification_screen/controller/notification_history_controller.dart`, `notification_screen.dart` | Liste paginée. API : `NotificationRepo.notificationHistory` |
| **FAQ** | `faq/controller/faq_controller.dart`, `views/faq_screen.dart`, `widget/faq_widget.dart` | Accordéon. API : `FaqRepo.loadFaq` |
| **Langue** | `language/controller/my_language_controller.dart`, `language_screen.dart` | Change la locale + recharge les traductions serveur, `Get.clearTranslations()`/`addTranslations()`. API : `GeneralSettingRepo.getLanguage` |
| **No Internet / Maintenance** | `no_internet/no_internet_screen.dart`, `page_content_screen/views/maintenance_content_screen.dart` | Écrans statiques, sans controller |

### Support Ticket

| Écran | Fichiers | État local | Validation | API |
|---|---|---|---|---|
| **Liste des tickets** | `support_ticket/controller/support_controller.dart`, `views/support_ticket_list_screen/support_ticket_list_screen.dart`, `widget/all_ticket_list_item.dart` | `ticketList, page, nextPageUrl, isLoading` | — | `SupportRepo.getSupportTicketList` |
| **Nouveau ticket** | `support_ticket/controller/new_ticket_controller.dart`, `views/new_ticket_screen/new_ticket_screen.dart`, `section/attachment_section.dart` | `subjectController, messageController`, `priorityList/selectedPriority`, `attachmentList: List<File>` | Sujet et message requis ; pièces jointes limitées (avertissement au-delà de 4) | `SupportRepo.storeTicket` |
| **Détails ticket** | `support_ticket/controller/ticket_details_controller.dart`, `views/ticket_details_screen/ticket_details_screen.dart` + sections | `replyController, attachmentList, messageList, receivedTicketModel, submitLoading, closeLoading` | Message de réponse requis | `SupportRepo.getSingleTicket/replyTicket/closeTicket` |

### Modules financiers (pattern wizard partagé — voir §2)

| Module | Fichiers principaux | Spécificités | API |
|---|---|---|---|
| **Send Money** | `send_money_screen/controller/send_money_controller.dart` + vues (amount/contact-select/pin pages), `send_money_history_screen.dart` | Résolution destinataire par username/téléphone ou QR ; blocage auto-envoi | `SendMoneyRepo.sendMoneyInfoData/checkUserExist/sendMoneyRequest/pinVerificationRequest/sendMoneyHistory` |
| **Request Money** | `request_money_screen/controller/request_money_controller.dart` + 8 vues (amount, approve amount/pin, contact-select, historique reçu/envoyé) | Flux bidirectionnel (demander / approuver-rejeter), compteur de demandes en attente | `RequestMoneyRepo.requestMoneyInfoData/checkUserExist/requestMoneyRequest/requestMoneyApproveRequest/pinVerificationRequest/approveMoneyRequest/rejectMoneyRequest/requestMoneyByMyFriends/requestMoneyByMeHistory` |
| **Cash Out** | `cash_out_screen_screen/controller/cash_out_controller.dart` + vues | Destinataire = Agent | `CashOutRepo.cashOutInfoData/checkAgentExist/cashOutRequest/pinVerificationRequest/cashOutHistory` |
| **Make Payment** | `payment_screen/controller/payment_controller.dart` + vues | Destinataire = Marchand, champ référence | `MakePaymentRepo.makePaymentInfoData/checkMerchantExist/makePaymentRequest/pinVerificationRequest/makePaymentHistory` |
| **Gift Card** | `gift_screen/controller/gift_controller.dart` + vues (bottom-sheet, pin, purchase) | Catalogue paginé (recherche + filtres pays/catégorie), destinataire nom/email, calcul taux de change + frais | `GiftCardRepo.allProductInfoData/giftPurchaseRequest/pinVerificationRequest/giftCardHistory` |
| **Airtime Recharge** | `airtime_recharge_screen/controller/airtime_recharge_controller.dart` + vues | Cascade pays → opérateur → montants suggérés | `AirtimeRechargeRepo.airtimeRechargeInfoData/airTimeTopUpRequest/pinVerificationRequest/airtimeRechargeHistory` |
| **Bill Pay** | `bill_pay_screen/controller/bill_pay_controller.dart` + vues | Catégorie → société ; montants fixes ou libres ; sauvegarde de compte réutilisable ; téléchargement reçu PDF | `BillPayRepo.billPayInfoData/billPayRequest/pinVerificationRequest/deleteSavedAccount/billPayHistory` |
| **Bank Transfer** | `bank_transfer_screen/controller/bank_transfer_controller.dart` + vues (add bank, dynamic form, historique) | Sélection banque + formulaire dynamique (IBAN/SWIFT…) ; comptes sauvegardés réutilisables | `BankTransferRepo.bankTransferInfoData/bankTransferOneTimeRequest/pinVerificationRequest/saveBankAccountRequest/deleteBankAccount/bankTransferHistory` |
| **Education Fee** | `education_fee_screen/controller/education_fee_controller.dart` + vues | Catégorie → institut + formulaire dynamique, PDF reçu | `EducationRepo.educationInfoData/educationRequest/pinVerificationRequest/educationHistory` |
| **Donation** | `donation_screen/controller/donation_controller.dart` + vues | Recherche ONG, don anonyme (« masquer mon identité ») | `DonationRepo.donationInfoData/donationRequest/pinVerificationRequest/donationHistory` |
| **Microfinance** | `micro_finance_screen/controller/micro_finance_controller.dart` + vues | Recherche organisme prêteur | `MicroFinanceRepo.microfinanceInfoData/billPayRequest(submit)/pinVerificationRequest/microfinanceHistory` |
| **Investment** | `investment/controller/investment_controller.dart` + vues (amount/overview/pin/historique) | Liste de plans avec slider de montant + calcul du rendement en temps réel | `InvestmentRepo.investmentPlanRepo/makeInvestmentRequest/pinVerificationRequest/investmentHistoryRepo` |
| **Add Money (Dépôt)** | `add_money/controller/add_money_controller.dart` + vues, `add_money_webview/my_webview_screen.dart` + `webview_widget.dart` | **Pas d'étape PIN/OTP** — redirige vers une **WebView in-app** pour la passerelle de paiement, avec détection succès/échec par pattern d'URL | `AddMoneyRepo.getDepositMethods/insertDeposit/getDepositHistory` |
| **Virtual Cards** | `virtual_cards/controller/virtual_cards_controller.dart` + 6 vues | Créer/gérer cartes virtuelles, historique par carte, révélation numéro/CVC gated par PIN, annulation | `VirtualCardsRepo.cardInfoData/cardHolderListData/singleCardInfoData/createVirtualCardRequest/singleCardConfidentialInfoInfoData/addCardBalance/cancelVirtualCard/virtualCardAllHistory` |

### Contrôleurs globaux partagés (pas des écrans autonomes)

- `screens/global/controller/country_controller.dart` — `CountryController` (classe simple, pas GetX) : liste/filtre pays, réutilisé par Login, Forgot PIN, Airtime, Virtual Cards, Bill Pay.
- `screens/global/controller/global_dynamic_form_controller.dart` — moteur générique de formulaire dynamique (identique à KYC), pilote Bank Transfer et Education Fee, avec pré-remplissage.
- `screens/global/views/dynamic_form_widget_view.dart`, `views/widgets/country_bottom_sheet.dart` — rendu partagé des champs dynamiques et du sélecteur de pays.

---

## 5. Composants réutilisables (`lib/app/components/`)

| Dossier/fichier | Rôle |
|---|---|
| `animated_widget/expanded_widget.dart` | Animation d'expansion/réduction |
| `animation/shake_animation.dart` | Animation de secousse (erreurs de validation) |
| `annotated_region/annotated_region_widget.dart` | Couleurs de la barre de statut/navigation |
| `badges/priority_badge.dart`, `status_badge.dart` | Badges colorés (priorité ticket, statut transaction/KYC) |
| `bottom-sheet/*` (5 fichiers) | Chrome de bottom-sheet réutilisé pour tous les sélecteurs (pays, banque, société…) |
| `buttons/app_main_submit_button.dart`, `custom_elevated_button.dart`, `category_button.dart`, `hold_to_confirm_button.dart` | Boutons CTA ; `hold_to_confirm_button` = confirmation par appui long (actions critiques) |
| `card/*` (8 fichiers) | Primitives carte/liste ; `my_custom_scaffold.dart` = scaffold standard (titre + retour) utilisé sur presque tous les écrans |
| `checkbox_and_radio/*` | Primitives de formulaire |
| `chip/custom_chip.dart` | Chip filtre/catégorie |
| `column_widget/card_column.dart` | Colonne label+valeur (ex. ID transaction copiable) |
| `credit_card_ui/*` | Rendu visuel complet de carte bancaire/virtuelle (police `ocr-a`) |
| `custom_loader/custom_loader.dart` | Spinner de chargement |
| `dialog/app_dialog.dart`, `exit_dialog.dart` | Dialogs confirmation/succès (fin de chaque étape PIN) et confirmation de sortie app |
| `divider/*` | Séparateurs visuels |
| `drawer/user_drawer_card.dart` | En-tête utilisateur du drawer dashboard |
| `drop_down/my_drop_down_widget.dart` | Sélecteur déroulant générique |
| `floating_action_button/fab.dart` | Wrapper FAB |
| `image/*` | Chargeur d'image asset/SVG, image réseau avec placeholder, avatar par initiales |
| `indicator/indicator.dart` | Points d'indication de page (onboarding) |
| `loading_border/loading_border.dart` | Effet bordure skeleton/shimmer |
| `otp_field_widget/otp_field_widget.dart` | Saisie OTP 6 chiffres (`pin_code_fields`) |
| `shimmer/*` (12 fichiers) | Skeletons par type d'écran (home, cartes, gift, plans/historique investissement, tickets, politique de confidentialité, transactions, list tiles, grilles catégories) |
| `snack_bar/show_custom_snackbar.dart` | `CustomSnackBar.error/success` centralisé, utilisé partout |
| `text-field/rounded_text_field.dart` | Champ de saisie arrondi standard (label/validator/icônes), utilisé sur tous les formulaires |
| `text/*` (7 fichiers) | Primitives typographiques (default/header/header-small/label/small/expandable/bottom-sheet-header) |
| `no_data.dart` | État vide générique |
| `preview_image.dart` | Visionneuse plein écran (route `previewImageScreen`) |
| `will_pop_widget.dart` | Interception bouton retour (double-tap pour quitter / confirmation) |

---

## 6. Inventaire complet des appels API

Base URL : `Environment.MAIN_API_URL/api/`. Client : `ApiService` (Dio), intercepteur ajoute automatiquement `Authorization: Bearer <token>` (token lu via `SharedPreferenceService.getAccessToken()`). Un 401 déclenche la déconnexion (`AuthMiddleware`). Routes Laravel dans `core/routes/api/api.php`, namespace `App\Http\Controllers\Api\...`.

Légende colonnes : **Méthode & chemin** · **Payload** · **Auth** (nécessite Bearer token) · **Utilisé par (Flutter)** · **Controller@method Laravel** · **Middleware**.

### 6.1 Auth / Onboarding

| Méthode & chemin | Payload | Auth | Utilisé par | Controller@method | Middleware |
|---|---|---|---|---|---|
| `POST authentication` | `country`, `mobile_number` (+`pin` pour login) | Non | `LoginRepo.loginUser/registerUser` | `Auth\LoginController@authentication` | aucun |
| `POST login-with/qr-code/{code}` | path `code` | **Oui** (⚠ inhabituel pour un "login") | `ProfileRepo.qrCodeLogin` | `Auth\LoginController@loginWithQrCode` | `auth:sanctum`, `token.permission:user_token` |
| `POST check-token` | — | Non | **aucun appel trouvé côté Flutter** | `Auth\LoginController@checkToken` | aucun |
| `POST password/mobile` | `country`, `mobile_number` | Non | `LoginRepo.forgetPassword` | `Auth\ForgotPasswordController@sendResetCode` | aucun |
| `POST password/verify-code` | `code`, `mobile_number` | Non | `LoginRepo.verifyForgetPassCode` | `Auth\ForgotPasswordController@verifyCode` | aucun |
| `POST password/reset` | `token`, `mobile_number`, `pin`, `pin_confirmation` | Non | `LoginRepo.resetPin` | `Auth\ForgotPasswordController@reset` | aucun |
| `POST register` | `SignUpModel.toMap()` | Non | `RegistrationRepo.registerUser` (constante définie, à revérifier — voir §11) | à revérifier | — |
| `GET authorization` | — | Oui | orchestration de l'étape suivante (sms/email/2fa/kyc) | `AuthorizationController@authorization` | `auth:sanctum`, `token.permission:user_token` |
| `GET resend-verify/{type}` | path `type` (`email`/`mobile`) | Oui | `RegistrationRepo.resendVerifyCode`, `SmsEmailVerificationRepo.resendVerifyCode` | `AuthorizationController@sendVerifyCode` | idem |
| `POST verify-mobile` | `code` | Oui | `RegistrationRepo.verifySmsOtp`, `SmsEmailVerificationRepo.verify(isEmail:false)` | `AuthorizationController@mobileVerification` | idem |
| `POST verify-email` | `code` | Oui | `RegistrationRepo.verifyEmailOtp`, `SmsEmailVerificationRepo.verify(isEmail:true)` | `AuthorizationController@emailVerification` | + `mobile.verify`, `registration.complete` |
| `POST verify-g2fa` | `code` | Oui | `TwoFactorRepo.verify` | `AuthorizationController@g2faVerification` | + `mobile.verify`, `registration.complete` |
| `POST user-data-submit` | `ProfileCompletePostModel.toMap()` | Oui | `ProfileRepo.completeProfile`, `RegistrationRepo.completeProfile` | `UserController@userDataSubmit` | + `mobile.verify` |
| `GET kyc-form` | — | Oui | `KycRepo.getKycData` | `UserController@kycForm` | + `mobile.verify`, `registration.complete`, `check.status` |
| `POST kyc-submit` (multipart) | champs dynamiques + fichiers | Oui | `KycRepo.submitKycData` | `UserController@kycSubmit` | idem |
| `GET logout` | — | Oui | `ProfileRepo.logout` | `Auth\LoginController@logout` | `auth:sanctum`, `token.permission:user_token` |

### 6.2 Profil / Compte

| Méthode & chemin | Payload | Auth | Utilisé par | Controller@method | Middleware |
|---|---|---|---|---|---|
| `GET user-info` | — | Oui | `ProfileRepo.loadProfileInfo`, `AddMoneyRepo.getUserInfo` | `UserController@userInfo` | `withoutMiddleware('kyc')` |
| `POST profile-setting` (multipart) | nom, prénom, adresse, zip, état, ville, image | Oui | `ProfileRepo.updateProfile` | `UserController@submitProfile` | + `kyc` |
| `POST change-password` | `current_pin`, `pin`, `pin_confirmation` | Oui | `ChangePasswordRepo.changePassword` | `UserController@submitPassword` | + `kyc` |
| `POST pin/validate` | `pin` | Oui | `ProfileRepo.checkPinOfAccount`, `BiometricRepo.checkPinOfAccount` | `UserController@validatePin` | + `kyc` |
| `POST delete-account` | `pin` | Oui | `ProfileRepo.deleteAccount` | `UserController@deleteAccount` | + `kyc` |
| `GET get-countries` | — | Non | `ProfileRepo.getCountryList`, `GeneralSettingRepo.getCountryList` | `AppController@getCountries` | aucun |
| `POST add-device-token` | token FCM | Oui | `PushNotificationService.sendUpdatedToken` | `UserController@addDeviceToken` | `auth:sanctum`,`token.permission:user_token` |
| `POST user/exist` | `user` | Oui | `SendMoneyRepo.checkUserExist`, `RequestMoneyRepo.checkUserExist` | `UserController@checkUser` | + `kyc` |
| `POST agent/exist` | `agent` | Oui | `CashOutRepo.checkAgentExist` | `UserController@checkAgent` | + `kyc` |
| `POST merchant/exist` | `merchant` | Oui | `MakePaymentRepo.checkMerchantExist` | `UserController@checkMerchant` | + `kyc` |
| `GET limit-charge` | — | Oui | aucun appel trouvé | `UserController@trxLimit` | + `kyc` |
| `GET qr-code` | — | Oui | `ProfileRepo.getMyQrCodeData` | `UserController@qrCode` | + `kyc` |
| `POST qr-code/download` | — | Oui | `ProfileRepo.downloadMyQrCodeData` | `UserController@qrCodeDownload` | + `kyc` |
| `POST qr-code/remove` | — | Oui | aucun appel trouvé | `UserController@qrCodeRemove` | + `kyc` |
| `POST qr-code/scan` | `code` | Oui | `ProfileRepo.scanQrCodeData` | `UserController@qrCodeScan` | + `kyc` |

### 6.3 Dashboard / Transactions / Notifications

| Méthode & chemin | Payload | Auth | Utilisé par | Controller@method | Middleware |
|---|---|---|---|---|---|
| `GET dashboard` | — | Oui | `HomeRepo.dashboardInfo` | `UserController@dashboard` | `withoutMiddleware('kyc')` |
| `GET offers/list?page=` | query | Oui | `HomeRepo.promotionalOffersList` | `UserController@offers` | + `kyc` |
| `GET transactions?page&order_by&type&remark&search` | query | Oui | `HomeRepo.transactionHistory`, `TransactionHistoryRepo.transactionHistory` | `UserController@transactions` | + `kyc` |
| `GET statements?month&year` | query | Oui | `TransactionHistoryRepo.statementsHistory` | `UserController@statements` | + `kyc` |
| `GET push-notifications?page=` | query | Oui | `NotificationRepo.notificationHistory` | `UserController@pushNotifications` | + `kyc` |
| `POST push-notifications/read/{id}` | path | Oui | aucun appel trouvé | `UserController@pushNotificationsRead` | + `kyc` |
| `GET notification/settings` | — | Oui | `GeneralSettingRepo.getNotificationSettingsInfo` | `UserController@notificationSettings` | + `kyc` |
| `POST notification/settings` | `en,pn,sn,is_allow_promotional_notify` | Oui | `GeneralSettingRepo.setNotificationSettings` | `UserController@notificationSettingsUpdate` | + `kyc` |
| `POST remove/promotional/notification/image` | — | Oui | aucun appel trouvé | `UserController@removePromotionalNotificationImage` | + `kyc` |

### 6.4 Bootstrap / paramètres généraux (public)

| Méthode & chemin | Auth | Utilisé par | Controller@method |
|---|---|---|---|
| `GET general-setting` | Non | `GeneralSettingRepo.getGeneralSetting` | `AppController@generalSetting` |
| `GET get-countries` | Non | voir §6.2 | `AppController@getCountries` |
| `GET policies` | Non | `PrivacyRepo.loadAppPagesData` | `AppController@policies` |
| `GET faq` | Non | `FaqRepo.loadFaq` | `AppController@faq` |
| `GET module-setting` | Non | `GeneralSettingRepo.getModuleList` | `AppController@moduleSetting` |
| `GET language/{key}` | Non | `GeneralSettingRepo.getLanguage` | `AppController@getLanguage` |

### 6.5 2FA / Sécurité

| Méthode & chemin | Payload | Auth | Utilisé par | Controller@method |
|---|---|---|---|---|
| `GET twofactor` | — | Oui | `TwoFactorRepo.get2FaData` | `UserController@show2faForm` |
| `POST twofactor/enable` | `secret`, `code` | Oui | `TwoFactorRepo.enable2fa` | `UserController@create2fa` |
| `POST twofactor/disable` | `code` | Oui | `TwoFactorRepo.disable2fa` | `UserController@disable2fa` |

### 6.6 Verification layer partagée (PIN/OTP transaction)

| Méthode & chemin | Payload | Utilisé par (tous les modules financiers) |
|---|---|---|
| `POST verification-process/verify/otp` | `otp`, `remark` | `OtpVerificationRepo.verify` |
| `POST verification-process/verify/pin` | `pin`, `remark` | Chaque repo module a sa propre méthode `pinVerificationRequest()` pointant sur ce même endpoint : SendMoney, CashOut, RequestMoney, MakePayment, BillPay, MicroFinance, Education, Donation, AirtimeRecharge, BankTransfer, GiftCard, VirtualCards, Investment |
| `POST verification-process/verify/resend/otp` | `action_id` | `OtpVerificationRepo.resendVerifyCode` |

### 6.7 Add Money

| Méthode & chemin | Payload | Utilisé par | Controller@method | Middleware |
|---|---|---|---|---|
| `GET add-money/methods` | — | `AddMoneyRepo.getDepositMethods` | `PaymentController@methods` | `module:add_money`, `kyc` |
| `POST add-money/insert` | `amount, method_code, currency` | `AddMoneyRepo.insertDeposit` | `PaymentController@depositInsert` | `module:add_money`, `kyc` |
| `GET add-money/history?page&search` | query | `AddMoneyRepo.getDepositHistory` | `UserController@addMoneyHistory` | `kyc` |

### 6.8 Send Money

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|---|
| `GET send-money` | — | `SendMoneyController@create` | `module:send_money`, `kyc` |
| `POST send-money/store` | `amount, user, verification_type, remark` | `SendMoneyController@store` | idem |
| `GET send-money/history?page` | query | `SendMoneyController@history` | idem |
| `GET send-money/details/{id}`, `pdf/{id}` | path | non appelés côté Flutter | idem |

### 6.9 Request Money

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET request-money/create` | — | `RequestMoneyController@create` | `module:request_money`, `kyc` |
| `POST request-money/store` | `amount,user,remark,verification_type` | `RequestMoneyController@store` | idem |
| `POST request-money/received-store/{id}` | `amount,user,remark,verification_type` | `RequestMoneyController@requestStore` | idem |
| `POST request-money/reject/{id}` | — | `RequestMoneyController@rejectRequest` | idem |
| `GET request-money/history?page` | query | `RequestMoneyController@history` | idem |
| `GET request-money/received-history?page` | query | `RequestMoneyController@requestHistory` | idem |
| `GET request-money/details/{id}`, `received-details/{id}`, `pdf/{id}`, `received/pdf/{id}` | path | non appelés côté Flutter | idem |
| ⚠ `POST request/money-approve/{id}` (`RequestMoneyRepo.approveMoneyRequest`) | `pin` | **route Laravel introuvable** — voir §11 | — |

### 6.10 Cash Out

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET cash-out/create` | — | `CashOutController@create` | `module:cash_out`, `kyc` |
| `POST cash-out/store` | `amount,agent,pin,verification_type,remark` | `CashOutController@store` | idem |
| `GET cash-out/history?page` | query | `CashOutController@history` | idem |
| `GET cash-out/details/{id}`, `pdf/{id}` | path | non appelés | idem |

### 6.11 Make Payment

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET make-payment/create` | — | `MakePaymentController@create` | `module:make_payment`, `kyc` |
| `POST make-payment/store` | `amount,merchant,pin,verification_type,reference,remark` | `MakePaymentController@store` | idem |
| `GET make-payment/history?page` | query | `MakePaymentController@history` | idem — ⚠ également appelé par erreur par `InvestmentRepo.makeInvestmentHistory`, voir §11 |
| `GET make-payment/details/{id}`, `pdf/{id}` | path | non appelés | idem |

### 6.12 Bill Pay (Utility Bill)

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET utility-bill/create` | — | `PayBillController@create` | `module:utility_bill`, `kyc` |
| `POST utility-bill/store` | `company_id,user_company_id,unique_id,amount,remark,reference,verification_type,save_information,amount_id` | `PayBillController@store` | idem |
| `GET utility-bill/history?page` | query | `PayBillController@history` | idem |
| `GET utility-bill/pdf/{id}` | path | `PayBillController@pdf` | idem (téléchargement direct via `ApiService.downloadFile`) |
| `POST utility-bill/company/delete/{id}` | path | `PayBillController@deleteUserCompany` | idem |
| `POST utility-bill/company/store` | — | ⚠ route Laravel introuvable / non appelée | — |
| `GET utility-bill/get-companies` | — | `PayBillController@getCompanies` | route Laravel présente mais pas de call site direct trouvé |

### 6.13 Education Fee

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET education-fee/create` | — | `EducationFeeController@create` | `module:education_fee`, `kyc` |
| `POST education-fee/store` (multipart) | champs dynamiques + `institution_id,amount,remark,verification_type` + fichiers | `EducationFeeController@store` | idem |
| `GET education-fee/history?page` | query | `EducationFeeController@history` | idem |
| `GET education-fee/pdf/{id}` | path | `EducationFeeController@pdf` | idem |
| `GET education-fee/details/{id}` | path | non appelé | idem |

### 6.14 Microfinance

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET microfinance/create` | — | `MicrofinanceController@create` | `module:microfinance`, `kyc` |
| `POST microfinance/store` (multipart) | champs dynamiques + `ngo_id,pin,amount,remark,verification_type` + fichiers | `MicrofinanceController@store` | idem |
| `GET microfinance/history?page` | query | `MicrofinanceController@history` | idem |
| `GET microfinance/pdf/{id}` | path | `MicrofinanceController@pdf` | idem |
| `GET microfinance/details/{id}`, `form/{id}` | path | non appelés | idem |

### 6.15 Donation

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET donation/create` | — | `DonationController@create` | `module:donation`, `kyc` |
| `POST donation/store` | `charity_id,name,hide_identity,reference,amount,email,verification_type,remark` | `DonationController@store` | idem |
| `GET donation/history?page` | query | `DonationController@history` | idem |
| `GET donation/details/{id}`, `pdf/{id}` | path | non appelés | idem |

### 6.16 Airtime / Recharge mobile

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET airtime/create` | — | `AirtimeController@create` | `module:mobile_recharge`, `kyc` |
| `GET airtime/operators-by-country/{id}` | path | `AirtimeController@getOperatorByCountry` | idem |
| `POST airtime/store` | `operator,country,calling_code,amount,mobile_number,remark,verification_type` | `AirtimeController@store` | idem |
| `GET airtime/history?page` | query | `AirtimeController@history` | idem |
| `GET airtime/countries` | — | route Laravel présente, non appelée (liste pays via `get-countries` à la place) | idem |
| ⚠ `airtimeTopUpEndPoint = 'airtime/top-up'` | — | constante morte, aucune route Laravel correspondante | — |

### 6.17 Investment

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET investment/plan?page` | query | `InvestmentController@all` | `module:investment` |
| `POST investment/store` | `invest_amount,plan_id,pin,verification_type,remark` | `InvestmentController@store` | idem |
| `GET investment/history?page` | query | `InvestmentController@history` | idem (endpoint correct, utilisé par `InvestmentRepo.investmentHistoryRepo`) |
| `GET investment/show/{id}`, `details/{id}` | path | non appelés | idem |

### 6.18 Bank Transfer

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET bank-transfer/create` | — | `BankTransferController@create` | `module:bank_transfer`, `kyc` |
| `POST bank-transfer/store` (+ variante multipart) | `bank_account_id/bank_id,account_number,account_holder,amount,remark,verification_type` (+ champs/fichiers dynamiques pour virement one-time) | `BankTransferController@store` | idem |
| `POST bank-transfer/account` (multipart) | `bank_id,account_number,account_holder` + champs dynamiques | `BankTransferController@account` | idem |
| `POST bank-transfer/delete/account/{id}` | path | `BankTransferController@deleteAccount` | idem |
| `GET bank-transfer/history?page` | query | `BankTransferController@history` | idem |
| `GET bank-transfer/account-details/{id}` | path | constante définie, pas de call site trouvé | idem |
| `GET bank-transfer/details/{id}`, `pdf/{id}` | path | non appelés | idem |

### 6.19 Virtual Card

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET virtual-card/list` | — | `VirtualCardController@list` | `module:virtual_card` (**pas de `kyc`**) |
| `GET virtual-card/new` | — | `VirtualCardController@newCard` | idem |
| `GET virtual-card/view/{id}` | path | `VirtualCardController@view` | idem |
| `POST virtual-card/store` (multipart) | type détenteur, documents front/back, pays | `VirtualCardController@store` | idem |
| `POST virtual-card/add/fund/{id}` | `amount` | `VirtualCardController@addFund` | idem |
| `POST virtual-card/cancel/{id}` | path | `VirtualCardController@cancel` | idem |
| `POST virtual-card/confidential/{id}` | `pin` | `VirtualCardController@confidential` | idem |
| `GET virtual-card/transaction?page` | query | `VirtualCardController@transaction` | idem |

### 6.20 Gift Card

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET gift-card/create` | query | `GiftCardController@create` | `module:gift_card` (**pas de `kyc`**) |
| `POST gift-card/purchase/{id}` | `amount,email,name,quantity,verification_type` | `GiftCardController@purchase` | idem |
| `GET gift-card/history?page` | query | `GiftCardController@history` | idem |
| ⚠ `gift-card/pdf/{id}` | — | Laravel = `GET`, Flutter appelle en `POST asBytes:true` — **à vérifier en runtime** | idem |
| `GET gift-card/show/{id}`, `details/{id}` | path | non appelés | idem |

### 6.21 Support Ticket

| Méthode & chemin | Payload | Controller@method | Middleware |
|---|---|---|---|
| `GET community-groups` | — | non localisé dans `api.php` lu — à revérifier | — |
| `GET support/method` | — | non localisé dans `api.php` lu — à revérifier | — |
| `GET ticket?page` | query | `TicketController@supportTicket` | `kyc` |
| `POST ticket/create` (multipart) | `subject,message,priority`, `attachments[]` | `TicketController@storeSupportTicket` | idem |
| `GET ticket/view/{ticket}` | path | `TicketController@viewTicket` | idem |
| `POST ticket/reply/{id}` (multipart) | `message`, `attachments[]` | `TicketController@replyTicket` | idem |
| `POST ticket/close/{id}` | path | `TicketController@closeTicket` | idem |
| `GET ticket/download/{attachment_id}` | path | constante définie, usage réel probablement via URL brute (`supportImagePath`) plutôt que cette route | idem |

### 6.22 FAQ / Privacy — voir §6.4 (public, sans auth)

### Patterns transverses notables pour la RN

- **Formulaires multipart dynamiques** (KYC, Bank Transfer, Education Fee, Microfinance) : sérialiseur `KycFormModel`/`modelToMap` identique partout → factoriser en un seul serializer RN.
- **Confirmation PIN+OTP en 2 étapes** avant quasi tout `*/store` financier, via les endpoints partagés `verification-process/verify/pin`/`/otp` → un seul composant/hook de confirmation partagé côté RN.
- **Historique paginé** uniformément via `?page=N` (parfois `search`/`order_by`/`type`/`remark`) → un seul hook de liste paginée générique couvre ~15 écrans d'historique.
- **Gating par module** : chaque groupe de fonctionnalités financières est protégé côté Laravel par `module:<slug>` (piloté par `module-setting`) + `kyc` (sauf Virtual Card et Gift Card qui n'ont pas `kyc`) → à répliquer côté client RN via `SharedPreferenceService`/état équivalent (masquer/désactiver les entrées de menu).

---

## 7. Dépendances natives

### 7.1 Caméra / capture & recadrage d'image (`image_picker`, `profile_image_cropper`, `file_picker`)
- **Photo de profil** : `profile_and_settings_screen/views/widgets/profile_image_with_upload_button_widget.dart` — `ImagePicker` (galerie ou caméra, `imageQuality: 80`), choix de source via `CupertinoActionSheet`, puis recadrage.
- **Recadrage custom** : `widgets/custom_image_cropper_widget.dart` — wrapper `profile_image_cropper` (`ProfileImageCropper.crop`) dans une modale avec rotation gauche/droite et forme d'overlay (cercle/grille/rectangle) ; écrit le PNG recadré en fichier temporaire.
- **Upload de documents KYC** : `auth/kyc/controller/kyc_controller.dart` (`pickFile`) — `FilePicker.platform.pickFiles(type: custom, allowedExtensions: [...])`.
- **Formulaires dynamiques** : `global/controller/global_dynamic_form_controller.dart` a le même pattern `pickFile` (support tickets / formulaires génériques).
- **Virtual Cards** : `virtual_cards_controller.dart` utilise aussi `file_picker` (pièces jointes carte).
- **Utilitaire partagé** : `lib/core/utils/file_selectors.dart` (`FileSelector`) — wrapper galerie/caméra (simple/multi) et sélection de documents, bien que les écrans instancient souvent `ImagePicker`/`FilePicker` directement.

### 7.2 QR Code (`mobile_scanner`)
- **Scan** : `my_qr_code_screen/views/scan_qr_code_screen.dart` — `MobileScannerController` + overlay custom SVG. Au scan, résout le payload côté serveur (`scanQrCodeDataFromServer`) puis retourne le résultat via `Get.back(result:...)`. Utilisé pour « scan to pay » (Send Money/Cash Out/Payment) et login QR (`qr_code_login_screen.dart`).
- **Affichage de son propre QR** : `my_qr_code_screen.dart` — **pas de génération client-side** ; l'image QR est **rendue côté serveur** et récupérée par URL, avec bouton de téléchargement.

### 7.3 Authentification biométrique (`local_auth`)
- `auth/biometric/controller/biometric_controller.dart` (`BioMetricController`, classe mère de `LoginController`) — `LocalAuthentication`, détection Face ID/empreinte, `authenticate(biometricOnly:true, persistAcrossBackgrounding:true)`.
- Activation gated par re-saisie du PIN côté serveur (`checkPinOfAccount`) avant de basculer le flag local.
- État persisté en `shared_preferences` (bool, clé `is_biometric_enabled`) — **pas** en stockage sécurisé.

### 7.4 Notifications push (`firebase_messaging`, `flutter_local_notifications`)
- Service : `lib/core/data/services/push_notification_service.dart` (implémente `push_notification_interface.dart`).
- **Messages en arrière-plan** : `FirebaseMessaging.onBackgroundMessage` — chemin **silencieux/data-only**, positionne juste un flag `hasNewNotificationKey`, n'affiche **aucune** notification locale.
- **Messages au premier plan** : construction manuelle d'une notification locale via `flutter_local_notifications`, avec téléchargement de `android.imageUrl` (Dio) et rendu en `BigPictureStyleInformation` (ou `BigTextStyleInformation` en fallback).
- **Canal Android** unique : `high_importance_channel`, priorité haute, son/vibration/lumières activés, `fullScreenIntent: true`.
- **Deep-link au tap** : parsing du payload JSON (`for_app`), mais **la redirection elle-même est un stub non implémenté** (`//redirect any specific page`).
- **iOS** : options de présentation foreground (alert/badge/sound) ; permissions demandées via les APIs propres du plugin (pas `permission_handler`).
- **Enregistrement du token FCM** : `sendUserToken()`/`sendUpdatedToken()` → `POST add-device-token`.

### 7.5 Stockage sécurisé vs préférences partagées (`lib/core/data/services/shared_pref_service.dart`)
- **`flutter_secure_storage`** (Android `encryptedSharedPreferences: true`, iOS `KeychainAccessibility.unlocked`) — utilisé **seulement** pour :
  - `userPinNumber` (⚠ bug : écrit sous la clé `userPhoneNumberKey`, lu sous `userPinCodeKey` — round-trip cassé tel quel, voir §11)
  - `userBalance` (solde en cache)
- **`shared_preferences` (non chiffré)** — tout le reste, notamment : `access_token`, `access_type`, `reset_pass_token`, `user_email`, `user_name`, `user_phone_number`, `user_full_name`, `user_image_path`, `remember_me`, `is_logged_in`, `onboard_status`, `general-setting-key` (JSON), `module-setting-key` (JSON), `device-key` (token FCM), `need-tfa`, `user_id`, `new-notification-key`, `theme` (défini mais **jamais utilisé**), `token`, `country_json_data` (JSON), `country_code`, `language_image_path`, `language_code`, `language-key`, `language-list-key`, `is_biometric_enabled`, `selected-operating-country-key` (JSON).
- **Implication** : le token d'accès est stocké **en clair**, pas dans le stockage sécurisé — seuls le PIN (bugué) et le solde le sont.

### 7.6 Contacts (`fast_contacts`)
- `lib/core/data/controller/contact/contact_controller.dart` — permission `Permission.contacts`, puis `FastContacts.getAllContacts`. Filtre par longueur de numéro (configurée serveur). Sélecteur de destinataire pour Send Money.

### 7.7 Infos appareil (`device_info_plus`)
- Utilisé **uniquement** dans `lib/core/utils/util.dart` (`checkAndRequestStoragePermission`) pour brancher la logique de permission selon la version Android SDK (≥30 → `mediaLibrary`, sinon `storage`). **Pas** envoyé au backend avec login/device-token.

### 7.8 Permissions (`permission_handler`)
- `Permission.contacts`, `Permission.storage`/`Permission.mediaLibrary` (selon version Android), `Permission.photos`, `Permission.microphone`, `Permission.camera` (bulk-request avant la webview de paiement, résultat non utilisé — code mort partiel).
- Biométrie et notifications gérées par leurs propres APIs (`local_auth`, `flutter_local_notifications`/`firebase_messaging`), pas via `permission_handler`.
- **Aucun `Permission.location`** trouvé.

### 7.9 WebView in-app (`flutter_inappwebview`)
- `add_money/views/add_money_webview/my_webview_screen.dart` + `webview_widget.dart` — passerelle de paiement pour le dépôt (Add Money). Détection succès/échec par **comparaison de pattern d'URL** (pas de callback natif/deep link). Auto-accepte les permissions in-page (caméra pour scan carte éventuel). Intercepte les schémas non-http(s) pour les déléguer à `url_launcher` (ex. intents UPI, apps bancaires tierces).

### 7.10 Téléchargement/ouverture de fichiers (`open_file`, `path_provider`)
- `lib/core/di_service/download_service.dart` (`DownloadService.downloadPDF`) — télécharge un PDF (Dio, header Bearer) vers le dossier Téléchargements public Android (chemin en dur), puis l'ouvre via `OpenFile.open`. Utilisé pour reçus/relevés PDF.
- `lib/core/utils/util.dart` — helpers de répertoire de téléchargement multi-plateforme et d'ouverture de fichier générique.

### 7.11 `url_launcher`
- Liens externes des bannières/offres promotionnelles (`home_screen_banner_card.dart`, `_payment_offer_list_card.dart`, `promotional_offers_screen.dart`).
- Lien lors de la configuration 2FA (`enable_qr_code_widget.dart`).
- Gestion des schémas non standards dans la WebView Add Money.
- **Aucun** usage `tel:`/`mailto:` trouvé — uniquement des liens `http(s)` externes.

### 7.12 Géolocalisation / cartes
- **Confirmé : absente.** Aucun package (`geolocator`, `google_maps_flutter`, `location`…) dans `pubspec.yaml`, aucun usage dans le code. **Rien à porter côté RN sur ce point.**

### 7.13 Fuseaux horaires (`timezone`)
- `tz.initializeTimeZones()` au démarrage (`main.dart`), configuration de base pour `flutter_local_notifications` — aucune notification locale programmée (`zonedSchedule`) trouvée, seulement des `show()` immédiats déclenchés par FCM foreground.

---

## 8. Thème / design system

### 8.1 Couleurs (`lib/core/utils/my_color.dart`)

| Nom | Hex |
|---|---|
| `screenBGColor` | `#F8FAFC` |
| `primary` | `#2B5BEE` |
| `secondary` | `#EEBE2B` |
| `headingText` | `#1E293B` |
| `bodyText` | `#475569` |
| `dark` | `#1F2937` |
| `light` | `#F9FAFB` |
| `black` / `white` | `#000000` / `#FFFFFF` |
| `accent1` | `#77AAFF` |
| `borderColor1` / `borderColor2` | `#E2E8F0` / `#F8FAFC` |
| `information` | `#18B800` |
| `warning` | `#FFCC00` |
| `success` | `#35C75A` |
| `error` | `#EB4E3D` |
| `statusColor` | `#2B5BEE` |
| `indigoColor` | `#6366F1` |
| `goldenColor` | `#FBBF24` |
| `greenLightColor` | `#10B981` |
| `violateColor` | `#A855F7` |
| `redLightColor` | `#EF4444` |
| `skyBlueColor` | `#3B82F6` |
| `orangeColor` | `#F97316` |
| `beigeColor` | `#FFF8EA` |
| `lemonadeColor` | `#E5F9ED` |

⚠ **Important** : `getPrimaryColor()` est **dynamique** — si `Environment.IS_COLOR_FROM_INTERNET` est vrai, la couleur primaire est parsée depuis la réponse `General Setting` du backend (`baseColor`), avec fallback sur la constante statique. **La couleur de marque est donc potentiellement white-label / configurable à l'exécution** — le thème RN ne doit pas supposer une valeur figée en dur pour le primary.

### 8.2 Typographie (`lib/core/utils/text_style.dart`, `app_style.dart`)
- Police unique pour tout le texte UI : **`AlbertSans`** (fallback `sans-serif`). La police `ocr-a` n'est utilisée **que** pour l'affichage du numéro de carte virtuelle/crédit (`components/credit_card_ui/src/constants/ui_constants.dart`).
- Tailles/line-heights via `.sp` (`flutter_screenutil`), `letterSpacing` explicite par style.
- Presets nommés (`MyTextStyle`) : `appBarTitle` (20sp/600), `appBarActionButtonTextStyleTitle` (15sp/600, souligné, couleur primary), `balanceCardTextStyle` (34sp/600, blanc), `headerH1` (28sp/600), `headerH3` (22sp/600), `sectionTitle` (17sp/600), `sectionTitle2` (16sp/600), `sectionTitle3` (15sp/600), `sectionSubTitle1` (13sp/400), `sectionBodyTextStyle` (15sp/400), `sectionBodyBoldTextStyle` (15sp/600, primary), `bodyTextStyle1` (16sp/400, blanc), `bodyTextStyle2` (17sp/400), `caption1Style` (12sp/400), `caption2Style` (11sp/400), `buttonTextStyle1` (16sp/500, blanc).
- `app_style.dart` : helpers de spacing (`spaceDown`/`spaceSide`), pas de styles de texte.

### 8.3 Dimensions (`lib/core/utils/dimensions.dart`)
- Échelle de tailles de police (`fontExtraSmall` 9 → `fontHeader` 24), métriques composants (`defaultButtonH` 50, `defaultRadius` 6, `badgeRadius` 4, `cardMargin` 12, `buttonRadius` 4, `cardRadius` 8, `bottomSheetRadius` 15, `textToTextSpace` 8), échelle d'espacement (`space2`…`space100`), padding d'écran (`horizontalScreenPadding`/`verticalScreenPadding` = 15), presets de radius (`mediumRadius` 8, `largeRadius` 12, `drawerRadius` 24, `extraRadius` 16, `cardExtraRadius` 20, `radiusMax` 50, `radiusProMax` 100), `inputIconSize` 18.
- Valeurs brutes définies une fois, puis suffixées `.w`/`.h`/`.r`/`.sp` à chaque site d'usage (responsive `flutter_screenutil`).

### 8.4 Icônes / images (`my_icons.dart`, `my_images.dart`)
- Classes de constantes `static const String` (chemins d'assets), pas d'enum ni de génération d'assets automatisée. `MyIcons` centralise aussi 3 animations Lottie et le set d'icônes actif/inactif de la bottom nav.

### 8.5 Mode sombre
- **Application light-mode uniquement.** `main.dart` force `ThemeData.light()`. Aucun `ThemeMode`/`darkTheme`/branchement `isDarkMode` trouvé. Une clé `theme` existe dans les shared prefs mais n'est **jamais lue/écrite** ailleurs (vestige inutilisé). → **Rien à porter pour un dark mode ; c'est une décision de scope nouvelle si le RN doit l'introduire.**

---

## 9. Assets

| Dossier | Nombre | Exemples représentatifs |
|---|---|---|
| `assets/images/` (racine) | 8 | `balance_card_bg.png`, `card_bg_pattern.png`, `chip.png`, `nfc.png`, `visa_logo.png`, `forgot_pin_page.png`, `canceled.png`, `no_internet.png` |
| `assets/images/logo/` | 4 | `logo.png`, `logo_white.png`, `english.png`, `french.png` (icônes du sélecteur de langue, pas des variantes de logo) |
| `assets/images/onboard/` | 3 | `onboard_1.png`, `onboard_2.png`, `onboard_3.png` |
| `assets/icons/` (racine) | 51 | majoritairement `.svg` (`wallet_icon.svg`, `send_icon.svg`, `bank_transfer_icon.svg`, `scan_qr_code_icon.svg`, `2fa_icon.svg`…), quelques `.png` (`man.png`, `order.png`, `recycle.png`, `easy-to-use.png`, `help_desk.png`) |
| `assets/icons/actions/` | 10 | paires actif/inactif bottom nav : `card_active/inactive.svg`, `history_active/inactive.svg`, `home_active/inactive.svg`, `profile_active/inactive.svg`, `statement_active/inactive.svg` |
| `assets/animation/` (Lottie) | 3 | `success_lottie.json`, `error_lottie.json`, `warning_lottie.json` |
| `assets/fonts/Albert_Sans/` | 4 fichiers, 1 famille | `AlbertSans-Regular.ttf` (défaut), `AlbertSans-Bold.ttf` (700), `AlbertSans-Italic.ttf` (italique), `AlbertSans-Light.ttf` (300) |
| `assets/fonts/ocr_a/` | 1 fichier, 1 famille | `OCR-A-regular.ttf` (regular uniquement) |

---

## 10. Localisation / i18n

- `lib/core/data/controller/localization/localization_controller.dart` — singleton wrappant `Get.updateLocale`/`Get.addTranslations`. Langues : Anglais (`en_US`) et Arabe (`ar_SA`), RTL auto-détecté, police par langue (`AlbertSans` pour en, `Cairo` pour ar).
- **Les traductions ne sont pas des fichiers `.arb`/`.json` statiques embarqués** — elles sont **récupérées dynamiquement depuis le backend** (`GeneralSettingRepo.getLanguage(code)`), mises en cache (`SharedPreferenceService`, clé `languageListKey`), parsées puis injectées via `Get.addTranslations(...)` au splash et à chaque changement de langue. **Système i18n entièrement piloté par le CMS backend.**
- `lib/core/utils/my_strings.dart` (559 lignes) — catalogue maître de **toutes** les clés de traduction (`MyStrings.xxx.tr`), à répliquer 1:1 côté RN (ex. clés i18next/react-intl) puisque le backend renvoie ces mêmes clés.
- Changement de langue : `MyLanguageController` (`language_screen.dart`) liste les langues en cache, recharge le fichier de langue à la sélection, met à jour la locale + prefs persistées, vide/réinjecte les traductions GetX, puis retourne en arrière.

---

## 11. Anomalies, routes mortes & points d'attention

Ces points doivent être tranchés/validés (avec l'utilisateur ou par test runtime) **avant** d'être reproduits ou corrigés côté React Native — ne pas décider silencieusement de « corriger » un comportement qui pourrait être un choix voulu.

1. **Token d'accès en clair** : `access_token` est stocké via `shared_preferences` non chiffré, alors que `flutter_secure_storage` est disponible et utilisé pour d'autres valeurs moins sensibles (PIN, solde). Pour un porting à l'identique, utiliser AsyncStorage côté RN ; à signaler comme amélioration de sécurité potentielle si le produit le souhaite (à valider, pas à décider seul).
2. **Bug clé PIN sécurisé** : `SharedPreferenceService.setUserPinNumber()` écrit sous la clé `userPhoneNumberKey` alors que `getUserPinNumber()` lit `userPinCodeKey` — le round-trip semble cassé tel quel. À clarifier avant de porter ce mécanisme.
3. **`request/money-approve/{id}`** (`RequestMoneyRepo.approveMoneyRequest`) — chemin introuvable dans `core/routes/api/api.php` tel que lu ; la route réelle d'approbation semble être `POST request-money/received-store/{id}`. À vérifier en runtime avant portage RN.
4. **`gift-card/pdf/{id}`** — Laravel déclare `GET`, le client Flutter appelle en `POST` (`asBytes:true`). À vérifier avant de choisir la méthode côté RN.
5. **`InvestmentRepo.makeInvestmentHistory`** — pointe par erreur vers `make-payment/history` au lieu de `investment/history` ; méthode probablement morte/non utilisée (l'écran utilise `investmentHistoryRepo`, correct). Ne pas porter cette méthode buguée.
6. **Constantes/endpoints sans site d'appel côté Flutter** (à revérifier avant de les exclure définitivement du scope RN) : `airtimeTopUpEndPoint`, `utility-bill/company/store`, `check-token`, `push-notifications/read/{id}`, `remove/promotional/notification/image`, `limit-charge`, `qr-code/remove`, `bank-transfer/account-details/{id}`, `utility-bill/get-companies`, `airtime/countries`, `ticket/download/{attachment_id}`, `social-login`, ainsi que la quasi-totalité des routes `details/{id}`/`pdf/{id}`/`show/{id}` par module (probablement des vues web/admin, pas mobile).
7. **`login-with/qr-code/{code}`** exige `auth:sanctum` alors que c'est conceptuellement une action de connexion — à confirmer (ré-authentification d'un second appareil déjà connecté ?) avant de répliquer cette contrainte côté RN.
8. **Notification push et deep-link au tap** : le payload `for_app` est parsé mais la redirection n'est **pas implémentée** (stub `//redirect any specific page`) — rien de fonctionnel à porter au-delà du contrat de payload.
9. **Asymétrie foreground/background des notifications push** : en arrière-plan, seul un flag « nouvelle notification » est positionné (aucune notification locale affichée) ; au premier plan, une notification riche est construite et affichée activement. À reproduire ou à trancher intentionnellement côté RN.
10. **Constante `register`** (`RegistrationRepo.registerUser`) — le endpoint `register` n'apparaît pas explicitement dans l'extrait lu de `api.php` ; le flux réel semble passer entièrement par `authentication` (avec/sans `pin`). À revérifier avant de porter cette méthode telle quelle.
11. **`community-groups`** et **`support/method`** (utilisés par `SupportRepo`) — routes Laravel non localisées dans l'extrait de `api.php` lu ; probablement présentes mais définies ailleurs (autre fichier/contrôleur) — à re-pointer précisément avant portage.
12. **Thème sombre** : clé `theme` définie dans les shared prefs mais jamais utilisée — code mort, pas de dark mode réel dans l'app.
13. **`permissionServices()`** dans `my_webview_screen.dart` (bulk permission request avant la webview de paiement) est défini mais **jamais appelé** — code mort côté Flutter, ne pas porter tel quel sans comprendre l'intention produit.

---

## 12. Implications pour la stack React Native

Ces constats alimentent directement les champs à préciser dans `CLAUDE.md` :

- **Auth backend** : Laravel Sanctum confirmé → client API RN doit gérer un Bearer token Sanctum, avec logique de rafraîchissement/expiration à valider côté backend (Sanctum ne fournit pas nativement de refresh token — à confirmer si le token expire et comment il est renouvelé, aucun mécanisme de refresh trouvé côté Flutter, qui se contente de déconnecter sur 401).
- **State management équivalent** : Flutter utilise GetX en mode `GetxController` + `update()` (pattern impératif proche d'un store simple, pas de flux réactif complexe). Cela correspond bien à un state management léger côté RN (Zustand ou Context API) plutôt qu'à Redux — voir recommandation dans `CLAUDE.md`.
- **Modules natifs à couvrir en RN** : caméra/galerie + recadrage (photo profil, KYC), lecteur QR (scan), biométrie, push notifications riches (image + canal haute importance), stockage sécurisé (à utiliser plus largement qu'en Flutter, ou à l'identique selon décision produit), contacts, téléchargement/ouverture de PDF, WebView in-app (paiement), liens externes. **Aucune géolocalisation.**
- **Design system** : palette de couleurs figée (§8.1) + couleur primaire potentiellement dynamique (server-driven), typographie `AlbertSans` (+ `ocr-a` pour les numéros de carte), espacement/rayons standardisés, light-mode uniquement, mise à l'échelle responsive (équivalent RN : une lib type `react-native-size-matters` ou un système de tokens `PixelRatio`-aware).
- **i18n** : le RN devra répliquer le mécanisme de traductions **pilotées par le backend** (mêmes clés que `my_strings.dart`), pas un système `.arb`/i18n statique classique — c'est une contrainte forte à documenter dans le choix de librairie i18n RN (ex. i18next avec chargement dynamique de ressources).

---

*Document généré automatiquement par audit de code — toute anomalie listée en §11 doit être confirmée avec l'équipe produit/backend avant d'être reproduite, corrigée ou ignorée dans la réécriture React Native.*
</content>
