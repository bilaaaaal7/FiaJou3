/**
 * Moteur i18n centralisé de FiaJou3 (FR / EN / AR).
 *
 * Remplace lang-switch.js : dictionnaire unique dans ce fichier, persistance
 * (localStorage + cookie + base de données pour les comptes connectés),
 * bascule RTL (attributs <html> + feuille Bootstrap RTL) et document.title
 * par page.
 *
 * Pages participantes (opt-in) : connexion, inscription, mot de passe oublié,
 * dossier partenaire, paramètres.
 *   - elles chargent ce fichier dans $extraJs ;
 *   - header.php pose window.FJ_I18N (langue résolue côté serveur, état de
 *     connexion, URL du point de sauvegarde) et <body> porte
 *     data-fj-page="login|register|mdp|partenaire|parametres" ;
 *   - une vue peut enrichir le dictionnaire via window.FJ_I18N_EXTRA
 *     (cas des textes dynamiques : titre du dossier partenaire).
 *
 * Les textes sont marqués dans le HTML avec data-i18n="espace.cle".
 */
(function () {
    'use strict';

    var LANGUES = ['fr', 'en', 'ar'];
    var STORAGE_KEY = 'fiajou3_lang';
    var COOKIE_KEY = 'fiajou3_lang';
    var BOOTSTRAP_LTR = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css';
    var BOOTSTRAP_RTL = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css';

    /* Dictionnaire centralisé FR / EN / AR, espaces nommés par page.
       pageTitle alimente <title id="pageTitle"> ; la page en cours est
       identifiée par data-fj-page sur <body>. */
    var DICT = {
        fr: {
            /* ---- Connexion ---- */
            'login.title': 'Connexion',
            'login.subtitle': 'Ravis de vous revoir, connectez-vous à votre compte',
            'login.emailLabel': 'Email',
            'login.passwordLabel': 'Mot de passe',
            'login.forgotPassword': 'Mot de passe oublié ?',
            'login.submitBtn': 'Se connecter',
            'login.noAccount': 'Pas encore de compte ?',
            'login.registerLink': 'Inscrivez-vous',
            'login.orDivider': 'ou',
            'login.googleBtn': 'Continuer avec Google',
            'login.pageTitle': 'Connexion - FiaJou3',

            /* ---- Inscription ---- */
            'register.title': 'Inscription',
            'register.subtitle': 'Créez votre compte pour commencer à commander',
            'register.prenomLabel': 'Prénom',
            'register.nomLabel': 'Nom',
            'register.telephoneLabel': 'Téléphone',
            'register.villeLabel': 'Ville',
            'register.adresseLabel': 'Adresse',
            'register.emailLabel': 'Email',
            'register.passwordLabel': 'Mot de passe',
            'register.confirmationLabel': 'Confirmer le mot de passe',
            'register.submitBtn': "S'inscrire",
            'register.hasAccount': 'Vous avez déjà un compte ?',
            'register.loginLink': 'Connectez-vous',
            'register.pageTitle': 'Inscription - FiaJou3',

            /* ---- Mot de passe oublié ---- */
            'mdp.title': 'Mot de passe oublié',
            'mdp.subtitle': 'Entrez votre email pour réinitialiser votre mot de passe',
            'mdp.resetTitle': 'Nouveau mot de passe',
            'mdp.resetSubtitle': 'Choisissez un nouveau mot de passe pour votre compte',
            'mdp.emailLabel': 'Email',
            'mdp.newPasswordLabel': 'Nouveau mot de passe',
            'mdp.confirmPasswordLabel': 'Confirmer le mot de passe',
            'mdp.submitBtn': 'Envoyer le lien',
            'mdp.resetBtn': 'Réinitialiser',
            'mdp.backLogin': 'Retour à la connexion',
            'mdp.pageTitle': 'Mot de passe oublié - FiaJou3',

            /* ---- Dossier partenaire (textes statiques ; le titre est
               dynamique et fourni par la vue via window.FJ_I18N_EXTRA) ---- */
            'partenaire.subtitle': 'Complétez votre dossier de candidature',
            'partenaire.emailNote': "Le formulaire est lié à l'adresse utilisée pour demander le lien :",
            'partenaire.prenomLabel': 'Prénom',
            'partenaire.nomLabel': 'Nom',
            'partenaire.emailLabel': 'Email',
            'partenaire.emailLocked': 'Email pré-rempli à partir de votre lien — non modifiable.',
            'partenaire.telephoneLabel': 'Téléphone',
            'partenaire.villeLabel': 'Ville',
            'partenaire.adresseLabel': 'Adresse',
            'partenaire.passwordLabel': 'Mot de passe',
            'partenaire.confirmationLabel': 'Confirmer le mot de passe',
            'partenaire.submitBtn': 'Valider mon dossier',
            'partenaire.loginLink': 'Déjà un compte ? Connectez-vous',
            'partenaire.backHome': "Retour à l'accueil",
            'partenaire.errorTitle': 'Lien invalide',
            'partenaire.errorSubtitle': "Impossible d'ouvrir le formulaire.",

            /* ---- Paramètres ---- */
            'parametres.titre': 'Paramètres',
            'parametres.sousTitre': 'Gérez vos informations personnelles, votre email, votre langue et votre mot de passe.',
            'parametres.infosTitre': 'Informations personnelles',
            'parametres.prenomLabel': 'Prénom',
            'parametres.nomLabel': 'Nom',
            'parametres.telephoneLabel': 'Téléphone',
            'parametres.adresseLabel': 'Adresse',
            'parametres.villeLabel': 'Ville',
            'parametres.enregistrerInfos': 'Enregistrer les modifications',
            'parametres.emailTitre': 'Adresse email',
            'parametres.emailLabel': 'Email',
            'parametres.emailHint': 'Votre email sert à vous connecter. Il doit être unique.',
            'parametres.changerEmail': "Changer l'adresse email",
            'parametres.mdpTitre': 'Changer le mot de passe',
            'parametres.mdpActuel': 'Mot de passe actuel',
            'parametres.nouveauMdp': 'Nouveau mot de passe',
            'parametres.confirmationMdp': 'Confirmer le nouveau mot de passe',
            'parametres.changerMdp': 'Changer le mot de passe',
            'parametres.langueTitre': 'Langue / Language',
            'parametres.langueSousTitre': "Choisissez la langue de l'application. Elle est mémorisée sur votre compte.",
            'parametres.pageTitle': 'Paramètres - FiaJou3',
            'parametres.langueActuelle': 'Langue actuelle',

            /* ---- Commun ---- */
            'common.retourAccueil': "Retour à l'accueil",
            'common.ajouterPanier': 'Ajouter au panier',
            'common.consulterMenu': 'Consulter le menu',
            'common.retourMenu': 'Retour au menu',
            'common.disponible': 'Disponible',
            'common.indisponible': 'Indisponible',
            'common.cloture': 'Clôturé',
            'common.ouvert': 'Ouvert',
            'common.commandesCloturees': 'Commandes clôturées',
            'common.enregistrer': 'Enregistrer les modifications',
            'common.annuler': 'Annuler',
            'common.fermer': 'Fermer',
            'common.viderPanier': 'Vider le panier',
            'common.passerCommande': 'Passer la commande',
            'common.continuerAchats': 'Continuer mes achats',
            'common.commander': 'Commander',
            'common.sousTotal': 'Sous-total',
            'common.total': 'Total',
            'common.unite': '/ unité',
            'common.supprimer': 'Supprimer',
            'common.livraisonLe': 'Livraison le',
            'common.livraisonPrevueLe': 'Livraison prévue le',
            'common.dateLivraisonPanier': 'Date de livraison du panier :',
            'common.creezCompteCommander': 'Créez un compte pour commander',
            'common.langueSelector': 'Sélecteur de langue',

            /* ---- Jours ---- */
            'jours.lundi': 'Lundi',
            'jours.mardi': 'Mardi',
            'jours.mercredi': 'Mercredi',
            'jours.jeudi': 'Jeudi',
            'jours.vendredi': 'Vendredi',
            'jours.samedi': 'Samedi',
            'jours.dimanche': 'Dimanche',
            'jours.samediMenuLibre': 'Samedi — Menu libre',

            /* ---- Navigation (sidebar / menu profil / retour) ---- */
            'nav.accueil': 'Accueil',
            'nav.menu': 'Menu',
            'nav.menuSemaine': 'Menu de la semaine',
            'nav.mesCommandes': 'Mes commandes',
            'nav.profil': 'Profil',
            'nav.monProfil': 'Mon profil',
            'nav.tableauBord': 'Tableau de bord',
            'nav.produits': 'Produits',
            'nav.categories': 'Catégories',
            'nav.commandes': 'Commandes',
            'nav.clients': 'Clients',
            'nav.cuisiniers': 'Cuisiniers',
            'nav.livreurs': 'Livreurs',
            'nav.zones': 'Zones de livraison',
            'nav.historique': 'Historique',
            'nav.parametres': 'Paramètres',
            'nav.deconnexion': 'Déconnexion',
            'nav.notifications': 'Notifications',
            'nav.monPanier': 'Mon panier',
            'nav.ouvrirMonPanier': 'Ouvrir mon panier',
            'nav.ouvrirMenu': 'Ouvrir le menu',
            'nav.roleAdmin': 'Administrateur',
            'nav.roleClient': 'Client',
            'nav.roleCuisinier': 'Cuisinier',
            'nav.roleLivreur': 'Livreur',

            /* ---- Accueil ---- */
            'accueil.pageTitle': 'FiaJou3 — Repas faits maison',
            'accueil.navAccueil': 'Accueil',
            'accueil.navMenuSemaine': 'Menu de la semaine',
            'accueil.navAPropos': 'À propos',
            'accueil.navContact': 'Contact',
            'accueil.commander': 'Commander',
            'accueil.heroEyebrow': "Repas marocain chaud, livré à l'heure",
            'accueil.heroTitre': 'Des repas faits maison, livrés chez vous',
            'accueil.heroSousTitre': 'Commandez des plats préparés avec soin par des cuisiniers locaux et recevez-les chauds directement à votre porte, en quelques clics.',
            'accueil.consulterMenu': 'Consulter le menu',
            'accueil.commencerCommander': 'Commencer à commander',
            'accueil.offre1Titre': 'Cuisine 100% locale',
            'accueil.offre1Sous': 'Fait maison',
            'accueil.offre2Titre': 'Livraison rapide',
            'accueil.offre2Sous': "Chaud et à l'heure",
            'accueil.menuTitre': 'Menu de la semaine',
            'accueil.menuVide': "Aucun menu n'est publié pour le moment. Revenez bientôt !",
            'accueil.autresPlats': 'autre(s) plat(s)',
            'accueil.voirPlats': 'Voir les plats',
            'accueil.consulterMenuComplet': 'Consulter le menu complet',
            'accueil.creerCompte': 'Créer un compte pour commander',
            'accueil.aboutTitre': 'Qui sommes-nous',
            'accueil.aboutEnSavoirPlus': 'En savoir plus',
            'accueil.partenaireTitre': 'Rejoignez FiaJou3',
            'accueil.partenaireCuisinier': 'Devenir cuisinier partenaire',
            'accueil.partenaireLivreur': 'Devenir livreur partenaire',
            'accueil.partenaireJeMinscris': "Je m'inscris",
            'accueil.clientsTitre': 'Ce que disent nos clients',
            'accueil.clientsRegulier': 'Client(e) régulier(ère)',
            'accueil.temoignage1': 'Le tajine était exactement comme celui de ma grand-mère, livré chaud en moins de 40 minutes. Je recommande vivement !',
            'accueil.temoignage2': 'Simple, rapide et surtout de vrais plats faits maison. Le couscous du vendredi est devenu un rituel chez nous.',
            'accueil.footerContact': 'Contactez-nous',
            'accueil.footerHoraires': 'Horaires de commande',
            'accueil.footerTousLesJours': 'Tous les jours',
            'accueil.footerDroits': 'Tous droits réservés.',
            'accueil.partenaireModalTitre': 'Devenir partenaire',
            'accueil.partenaireModalSub': 'Rejoignez FiaJou3 et complétez votre dossier.',
            'accueil.partenaireModalIntro': 'Indiquez votre email : nous vous enverrons un lien sécurisé pour compléter votre dossier.',
            'accueil.partenaireModalEmail': 'Email',
            'accueil.partenaireModalAnnuler': 'Annuler',
            'accueil.partenaireModalContinuer': 'Continuer',
            'accueil.partenaireModalFermer': 'Fermer',
            'accueil.aboutTexte': "FiaJou3 met en relation des cuisiniers locaux passionnés avec des gourmands pressés. Chaque plat est préparé à la commande, comme à la maison, puis livré rapidement par nos livreurs partenaires près de chez vous.",
            'accueil.samediDesc': 'Aucun menu spécifique le samedi : choisissez librement parmi tous les plats de la semaine.',
            'accueil.partenaireCuisinierTexte': 'Partagez vos recettes faites maison et vendez vos plats à de nouveaux clients chaque semaine.',
            'accueil.partenaireLivreurTexte': 'Livrez les commandes dans votre zone et organisez vos tournées selon vos disponibilités.',
            'accueil.footerTexte': 'Des repas faits maison, préparés par des cuisiniers locaux et livrés rapidement chez vous.',
            'accueil.footerMaroc': 'Maroc',
            'accueil.partenaireModalSubCuisinier': 'Rejoignez FiaJou3 en tant que cuisinier.',
            'accueil.partenaireModalSubLivreur': 'Rejoignez FiaJou3 en tant que livreur.',
            'accueil.partenaireModalEmailInvalide': 'Veuillez saisir une adresse email valide.',
            'accueil.partenaireModalErreurGenerique': 'Une erreur est survenue. Veuillez réessayer.',

            /* ---- Page Menu ---- */
            'menu.pageTitle': 'Menu — FiaJou3',
            'menu.titre': 'Notre Menu',
            'menu.sousTitre': 'Tous nos plats, préparés avec soin',
            'menu.parJour': 'Par jour',
            'menu.tous': 'Tous',
            'menu.voir': 'Voir',
            'menu.ajouter': 'Ajouter',
            'menu.vide': 'Aucun plat disponible pour le moment.',

            /* ---- Page Menu de la semaine ---- */
            'menu_semaine.pageTitle': 'Menu de la semaine — FiaJou3',
            'menu_semaine.titre': 'Menu de la semaine',
            'menu_semaine.sousTitre': 'Une sélection de plats frais chaque jour',
            'menu_semaine.videTexte': 'Aucun plat dans le menu de la semaine pour le moment.',

            /* ---- Panier ---- */
            'panier.pageTitle': 'Mon panier — FiaJou3',
            'panier.titre': 'Mon panier',
            'panier.vide': 'Votre panier est vide.',
            'panier.ajouterPlats': 'Ajoutez des plats au menu',
            'panier.dateLivraison': 'Date de livraison',
            'panier.image': 'Image',
            'panier.nom': 'Nom',
            'panier.article': 'Article',
            'panier.quantite': 'Quantité',
            'panier.prix': 'Prix',
            'panier.actions': 'Actions',
            'panier.livraison': 'Livraison',
            'panier.gratuit': 'Gratuit',
            'panier.retirerQuantite': 'Retirer une quantité',
            'panier.ajouterQuantite': 'Ajouter une quantité',

            /* ---- Mes commandes ---- */
            'mes_commandes.pageTitle': 'Mes commandes — FiaJou3',
            'mes_commandes.titre': 'Mes commandes',
            'mes_commandes.sousTitre': "Suivez vos commandes, du panier jusqu'à la livraison.",
            'mes_commandes.vide': "Vous n'avez pas encore passé de commande.",
            'mes_commandes.commander': 'Commander maintenant',
            'mes_commandes.numero': 'Commande',
            'mes_commandes.date': 'Date',
            'mes_commandes.statut': 'Statut',
            'mes_commandes.total': 'Total',
            'mes_commandes.detail': 'Détail',
            'mes_commandes.filtrer': 'Filtrer',
            'mes_commandes.filtreToutes': 'Toutes',
            'mes_commandes.filtreEnCours': 'En cours',
            'mes_commandes.filtreLivrees': 'Livrées',
            'mes_commandes.filtreAnnulees': 'Annulées',
            'mes_commandes.dateCommande': 'Date commande',
            'mes_commandes.dateLivraison': 'Date livraison',
            'mes_commandes.heure': 'Heure',
            'mes_commandes.commentaire': 'Commentaire',
            'mes_commandes.statutEnAttente': 'En attente',
            'mes_commandes.statutConfirmee': 'Confirmée',
            'mes_commandes.statutEnPreparation': 'En préparation',
            'mes_commandes.statutPrete': 'Prête',
            'mes_commandes.statutEnLivraison': 'En livraison',
            'mes_commandes.statutLivree': 'Livrée',
            'mes_commandes.statutAnnulee': 'Annulée',

            /* ---- Profil (client) ---- */
            'profil.pageTitle': 'Mon profil — FiaJou3',
            'profil.titre': 'Mon profil',
            'profil.sousTitre': 'Gérez vos informations personnelles et votre mot de passe.',

            /* ---- Fiche produit ---- */
            'produit.disponible': 'Disponible',
            'produit.indisponible': 'Indisponible',
            'produit.ajouterPanier': 'Ajouter au panier — livraison le',
            'produit.consultationSeule': 'Consultation uniquement — hors menu de la semaine',
            'produit.indisponibleMoment': 'Indisponible pour le moment',
            'produit.erreurIndisponible': "Ce plat n'est plus disponible ou la quantité maximale (20) est atteinte.",
            'produit.erreurCloturees': 'Les commandes pour cette date sont clôturées (limite',
            'produit.erreurClotureesFin': 'la veille).',
            'produit.erreurHorsMenu': "Ce plat ne fait pas partie du menu de la semaine publié : il est disponible uniquement en consultation.",

            /* ---- Finaliser la commande ---- */
            'commander.pageTitle': 'Finaliser la commande — FiaJou3',
            'commander.titre': 'Finaliser la commande',
            'commander.vosInfos': 'Vos informations (profil)',
            'commander.infosProfil': 'Ces informations proviennent de votre profil. Pour les modifier,',
            'commander.majProfil': 'mettez à jour votre profil',
            'commander.livraisonInfo': 'Livraison 7j/7. Pour être livré un jour J, commandez au plus tard la veille à',
            'commander.livraisonInfoFin': 'Le samedi, le menu est libre : tous les plats de la semaine sont commandables.',
            'commander.dateLivraison': 'Date de livraison',
            'commander.heureLivraison': 'Heure de livraison',
            'commander.zoneLivraison': 'Zone de livraison',
            'commander.prioritaire': 'Commande prioritaire',
            'commander.oui': 'Oui',
            'commander.non': 'Non',
            'commander.pauseDebut': 'Pause — début',
            'commander.pauseFin': 'Pause — fin',
            'commander.commentaire': 'Commentaire',
            'commander.sousTotal': 'Sous-total plats',
            'commander.fraisLivraison': 'Frais de livraison',
            'commander.totalPayer': 'Total à payer',
            'commander.valider': 'Valider la commande',

            /* ---- Détail de commande ---- */
            'detail_commande.titre': 'Commande',
            'detail_commande.subtitre': 'Consultez les détails et le suivi de votre commande.',
            'detail_commande.retour': 'Retour',
            'detail_commande.infosTitre': 'Informations de la commande',
            'detail_commande.statut': 'Statut',
            'detail_commande.dateCommande': 'Date de commande',
            'detail_commande.dateLivraison': 'Date de livraison',
            'detail_commande.heureLivraison': 'Heure de livraison',
            'detail_commande.zone': 'Zone',
            'detail_commande.prioritaire': 'Prioritaire',
            'detail_commande.oui': 'Oui',
            'detail_commande.pause': 'Pause',
            'detail_commande.commentaire': 'Commentaire',
            'detail_commande.total': 'Total',
            'detail_commande.articlesTitre': 'Articles commandés',
            'detail_commande.image': 'Image',
            'detail_commande.produit': 'Produit',
            'detail_commande.categorie': 'Catégorie',
            'detail_commande.prixUnitaire': 'Prix unitaire',
            'detail_commande.quantite': 'Quantité',
            'detail_commande.sousTotal': 'Sous-total',
            'detail_commande.aucunArticle': 'Aucun article.',
            'detail_commande.chronologieTitre': 'Chronologie du statut',
            'detail_commande.changeDe': 'Changé de',
            'detail_commande.par': 'par',
            'detail_commande.aucunHistorique': 'Aucun historique de statut.',

            /* ---- Notifications ---- */
            'notifications.pageTitle': 'Notifications — FiaJou3',
            'notifications.titre': 'Notifications',
            'notifications.nonLues': 'notification(s) non lue(s).',
            'notifications.toutLu': 'Tout marquer comme lu',
            'notifications.aucuneNonLue': "Vous n'avez aucune notification non lue.",
            'notifications.aucune': 'Aucune notification.',
            'notifications.lu': 'Lu'
        },
        en: {
            'login.title': 'Login',
            'login.subtitle': 'Welcome back, please sign in to your account',
            'login.emailLabel': 'Email',
            'login.passwordLabel': 'Password',
            'login.forgotPassword': 'Forgot password?',
            'login.submitBtn': 'Sign in',
            'login.noAccount': "Don't have an account?",
            'login.registerLink': 'Sign up',
            'login.orDivider': 'or',
            'login.googleBtn': 'Continue with Google',
            'login.pageTitle': 'Login - FiaJou3',

            'register.title': 'Sign up',
            'register.subtitle': 'Create your account to start ordering',
            'register.prenomLabel': 'First name',
            'register.nomLabel': 'Last name',
            'register.telephoneLabel': 'Phone',
            'register.villeLabel': 'City',
            'register.adresseLabel': 'Address',
            'register.emailLabel': 'Email',
            'register.passwordLabel': 'Password',
            'register.confirmationLabel': 'Confirm password',
            'register.submitBtn': 'Sign up',
            'register.hasAccount': 'Already have an account?',
            'register.loginLink': 'Sign in',
            'register.pageTitle': 'Sign up - FiaJou3',

            'mdp.title': 'Forgot Password',
            'mdp.subtitle': 'Enter your email to reset your password',
            'mdp.resetTitle': 'New password',
            'mdp.resetSubtitle': 'Choose a new password for your account',
            'mdp.emailLabel': 'Email',
            'mdp.newPasswordLabel': 'New password',
            'mdp.confirmPasswordLabel': 'Confirm password',
            'mdp.submitBtn': 'Send link',
            'mdp.resetBtn': 'Reset',
            'mdp.backLogin': 'Back to login',
            'mdp.pageTitle': 'Forgot Password - FiaJou3',

            'partenaire.subtitle': 'Complete your application',
            'partenaire.emailNote': 'This form is linked to the address used to request the link:',
            'partenaire.prenomLabel': 'First name',
            'partenaire.nomLabel': 'Last name',
            'partenaire.emailLabel': 'Email',
            'partenaire.emailLocked': 'Email pre-filled from your link — not editable.',
            'partenaire.telephoneLabel': 'Phone',
            'partenaire.villeLabel': 'City',
            'partenaire.adresseLabel': 'Address',
            'partenaire.passwordLabel': 'Password',
            'partenaire.confirmationLabel': 'Confirm password',
            'partenaire.submitBtn': 'Submit my application',
            'partenaire.loginLink': 'Already have an account? Sign in',
            'partenaire.backHome': 'Back to home',
            'partenaire.errorTitle': 'Invalid link',
            'partenaire.errorSubtitle': 'Unable to open the form.',

            'parametres.titre': 'Settings',
            'parametres.sousTitre': 'Manage your personal information, email, language and password.',
            'parametres.infosTitre': 'Personal information',
            'parametres.prenomLabel': 'First name',
            'parametres.nomLabel': 'Last name',
            'parametres.telephoneLabel': 'Phone',
            'parametres.adresseLabel': 'Address',
            'parametres.villeLabel': 'City',
            'parametres.enregistrerInfos': 'Save changes',
            'parametres.emailTitre': 'Email address',
            'parametres.emailLabel': 'Email',
            'parametres.emailHint': 'Your email is used to sign in. It must be unique.',
            'parametres.changerEmail': 'Change email address',
            'parametres.mdpTitre': 'Change password',
            'parametres.mdpActuel': 'Current password',
            'parametres.nouveauMdp': 'New password',
            'parametres.confirmationMdp': 'Confirm new password',
            'parametres.changerMdp': 'Change password',
            'parametres.langueTitre': 'Language',
            'parametres.langueSousTitre': 'Choose the application language. It is saved to your account.',
            'parametres.pageTitle': 'Settings - FiaJou3',
            'parametres.langueActuelle': 'Current language',

            /* ---- Common ---- */
            'common.retourAccueil': 'Back to home',
            'common.ajouterPanier': 'Add to cart',
            'common.consulterMenu': 'Browse the menu',
            'common.retourMenu': 'Back to the menu',
            'common.disponible': 'Available',
            'common.indisponible': 'Unavailable',
            'common.cloture': 'Closed',
            'common.ouvert': 'Open',
            'common.commandesCloturees': 'Orders closed',
            'common.enregistrer': 'Save changes',
            'common.annuler': 'Cancel',
            'common.fermer': 'Close',
            'common.viderPanier': 'Clear the cart',
            'common.passerCommande': 'Place the order',
            'common.continuerAchats': 'Continue shopping',
            'common.commander': 'Order',
            'common.sousTotal': 'Subtotal',
            'common.total': 'Total',
            'common.unite': '/ unit',
            'common.supprimer': 'Remove',
            'common.livraisonLe': 'Delivery on',
            'common.livraisonPrevueLe': 'Estimated delivery on',
            'common.dateLivraisonPanier': 'Cart delivery date:',
            'common.creezCompteCommander': 'Create an account to order',
            'common.langueSelector': 'Language selector',

            /* ---- Days ---- */
            'jours.lundi': 'Monday',
            'jours.mardi': 'Tuesday',
            'jours.mercredi': 'Wednesday',
            'jours.jeudi': 'Thursday',
            'jours.vendredi': 'Friday',
            'jours.samedi': 'Saturday',
            'jours.dimanche': 'Sunday',
            'jours.samediMenuLibre': 'Saturday — Free menu',

            /* ---- Navigation (sidebar / profile menu / back) ---- */
            'nav.accueil': 'Home',
            'nav.menu': 'Menu',
            'nav.menuSemaine': 'Weekly menu',
            'nav.mesCommandes': 'My orders',
            'nav.profil': 'Profile',
            'nav.monProfil': 'My profile',
            'nav.tableauBord': 'Dashboard',
            'nav.produits': 'Products',
            'nav.categories': 'Categories',
            'nav.commandes': 'Orders',
            'nav.clients': 'Customers',
            'nav.cuisiniers': 'Cooks',
            'nav.livreurs': 'Couriers',
            'nav.zones': 'Delivery zones',
            'nav.historique': 'History',
            'nav.parametres': 'Settings',
            'nav.deconnexion': 'Log out',
            'nav.notifications': 'Notifications',
            'nav.monPanier': 'My cart',
            'nav.ouvrirMonPanier': 'Open my cart',
            'nav.ouvrirMenu': 'Open menu',
            'nav.roleAdmin': 'Administrator',
            'nav.roleClient': 'Customer',
            'nav.roleCuisinier': 'Cook',
            'nav.roleLivreur': 'Courier',

            /* ---- Home ---- */
            'accueil.pageTitle': 'FiaJou3 — Home-cooked meals',
            'accueil.navAccueil': 'Home',
            'accueil.navMenuSemaine': 'Weekly menu',
            'accueil.navAPropos': 'About',
            'accueil.navContact': 'Contact',
            'accueil.commander': 'Order now',
            'accueil.heroEyebrow': 'Hot Moroccan meals, delivered on time',
            'accueil.heroTitre': 'Home-cooked meals, delivered to your door',
            'accueil.heroSousTitre': 'Order dishes carefully prepared by local cooks and receive them hot at your door in just a few clicks.',
            'accueil.consulterMenu': 'Browse the menu',
            'accueil.commencerCommander': 'Start ordering',
            'accueil.offre1Titre': '100% local cooking',
            'accueil.offre1Sous': 'Home-made',
            'accueil.offre2Titre': 'Fast delivery',
            'accueil.offre2Sous': 'Hot and on time',
            'accueil.menuTitre': 'Weekly menu',
            'accueil.menuVide': 'No menu is published yet. Come back soon!',
            'accueil.autresPlats': 'more dish(es)',
            'accueil.voirPlats': 'View dishes',
            'accueil.consulterMenuComplet': 'Browse the full menu',
            'accueil.creerCompte': 'Create an account to order',
            'accueil.aboutTitre': 'Who we are',
            'accueil.aboutEnSavoirPlus': 'Learn more',
            'accueil.partenaireTitre': 'Join FiaJou3',
            'accueil.partenaireCuisinier': 'Become a partner cook',
            'accueil.partenaireLivreur': 'Become a partner courier',
            'accueil.partenaireJeMinscris': 'Sign me up',
            'accueil.clientsTitre': 'What our customers say',
            'accueil.clientsRegulier': 'Regular customer',
            'accueil.temoignage1': 'The tajine was exactly like my grandmother\u2019s, delivered hot in under 40 minutes. I highly recommend it!',
            'accueil.temoignage2': 'Simple, fast and above all real homemade dishes. Friday\u2019s couscous has become a ritual in our home.',
            'accueil.footerContact': 'Contact us',
            'accueil.footerHoraires': 'Ordering hours',
            'accueil.footerTousLesJours': 'Every day',
            'accueil.footerDroits': 'All rights reserved.',
            'accueil.partenaireModalTitre': 'Become a partner',
            'accueil.partenaireModalSub': 'Join FiaJou3 and complete your application.',
            'accueil.partenaireModalIntro': 'Enter your email: we will send you a secure link to complete your application.',
            'accueil.partenaireModalEmail': 'Email',
            'accueil.partenaireModalAnnuler': 'Cancel',
            'accueil.partenaireModalContinuer': 'Continue',
            'accueil.partenaireModalFermer': 'Close',
            'accueil.aboutTexte': 'FiaJou3 connects passionate local cooks with busy food lovers. Every dish is prepared to order, just like at home, then delivered quickly by our partner couriers near you.',
            'accueil.samediDesc': 'No specific menu on Saturday: freely choose from all the dishes of the week.',
            'accueil.partenaireCuisinierTexte': 'Share your home-made recipes and sell your dishes to new customers every week.',
            'accueil.partenaireLivreurTexte': 'Deliver orders in your area and organize your rounds around your availability.',
            'accueil.footerTexte': 'Home-cooked meals, prepared by local cooks and delivered quickly to you.',
            'accueil.footerMaroc': 'Morocco',
            'accueil.partenaireModalSubCuisinier': 'Join FiaJou3 as a cook.',
            'accueil.partenaireModalSubLivreur': 'Join FiaJou3 as a courier.',
            'accueil.partenaireModalEmailInvalide': 'Please enter a valid email address.',
            'accueil.partenaireModalErreurGenerique': 'Something went wrong. Please try again.',

            /* ---- Menu page ---- */
            'menu.pageTitle': 'Menu - FiaJou3',
            'menu.titre': 'Our Menu',
            'menu.sousTitre': 'All our dishes, carefully prepared',
            'menu.parJour': 'By day',
            'menu.tous': 'All',
            'menu.voir': 'View',
            'menu.ajouter': 'Add',
            'menu.vide': 'No dish available at the moment.',

            /* ---- Weekly menu page ---- */
            'menu_semaine.pageTitle': 'Weekly menu - FiaJou3',
            'menu_semaine.titre': 'Weekly menu',
            'menu_semaine.sousTitre': 'A selection of fresh dishes every day',
            'menu_semaine.videTexte': 'No dishes in the weekly menu at the moment.',

            /* ---- Cart ---- */
            'panier.pageTitle': 'My cart - FiaJou3',
            'panier.titre': 'My cart',
            'panier.vide': 'Your cart is empty.',
            'panier.ajouterPlats': 'Add dishes from the menu',
            'panier.dateLivraison': 'Delivery date',
            'panier.image': 'Image',
            'panier.nom': 'Name',
            'panier.article': 'Item',
            'panier.quantite': 'Quantity',
            'panier.prix': 'Price',
            'panier.actions': 'Actions',
            'panier.livraison': 'Delivery',
            'panier.gratuit': 'Free',
            'panier.retirerQuantite': 'Remove one',
            'panier.ajouterQuantite': 'Add one',

            /* ---- My orders ---- */
            'mes_commandes.pageTitle': 'My orders - FiaJou3',
            'mes_commandes.titre': 'My orders',
            'mes_commandes.sousTitre': 'Track your orders, from cart to delivery.',
            'mes_commandes.vide': 'You have not placed any order yet.',
            'mes_commandes.commander': 'Order now',
            'mes_commandes.numero': 'Order',
            'mes_commandes.date': 'Date',
            'mes_commandes.statut': 'Status',
            'mes_commandes.total': 'Total',
            'mes_commandes.detail': 'Details',
            'mes_commandes.filtrer': 'Filter',
            'mes_commandes.filtreToutes': 'All',
            'mes_commandes.filtreEnCours': 'In progress',
            'mes_commandes.filtreLivrees': 'Delivered',
            'mes_commandes.filtreAnnulees': 'Cancelled',
            'mes_commandes.dateCommande': 'Order date',
            'mes_commandes.dateLivraison': 'Delivery date',
            'mes_commandes.heure': 'Time',
            'mes_commandes.commentaire': 'Comment',
            'mes_commandes.statutEnAttente': 'Pending',
            'mes_commandes.statutConfirmee': 'Confirmed',
            'mes_commandes.statutEnPreparation': 'In preparation',
            'mes_commandes.statutPrete': 'Ready',
            'mes_commandes.statutEnLivraison': 'Out for delivery',
            'mes_commandes.statutLivree': 'Delivered',
            'mes_commandes.statutAnnulee': 'Cancelled',

            /* ---- Client profile ---- */
            'profil.pageTitle': 'My profile - FiaJou3',
            'profil.titre': 'My profile',
            'profil.sousTitre': 'Manage your personal information and password.',

            /* ---- Product page ---- */
            'produit.disponible': 'Available',
            'produit.indisponible': 'Unavailable',
            'produit.ajouterPanier': 'Add to cart — delivery on',
            'produit.consultationSeule': 'Viewing only — not part of the weekly menu',
            'produit.indisponibleMoment': 'Unavailable at the moment',
            'produit.erreurIndisponible': 'This dish is no longer available or the maximum quantity (20) has been reached.',
            'produit.erreurCloturees': 'Orders for this date are closed (deadline',
            'produit.erreurClotureesFin': 'the day before).',
            'produit.erreurHorsMenu': 'This dish is not part of the published weekly menu: it is available for viewing only.',

            /* ---- Place order ---- */
            'commander.pageTitle': 'Place order - FiaJou3',
            'commander.titre': 'Place your order',
            'commander.vosInfos': 'Your information (profile)',
            'commander.infosProfil': 'This information comes from your profile. To change it,',
            'commander.majProfil': 'update your profile',
            'commander.livraisonInfo': 'Delivery 7 days a week. To be delivered on a given day, order no later than',
            'commander.livraisonInfoFin': 'the day before. On Saturday, the menu is free: every dish of the week can be ordered.',
            'commander.dateLivraison': 'Delivery date',
            'commander.heureLivraison': 'Delivery time',
            'commander.zoneLivraison': 'Delivery zone',
            'commander.prioritaire': 'Priority order',
            'commander.oui': 'Yes',
            'commander.non': 'No',
            'commander.pauseDebut': 'Break — start',
            'commander.pauseFin': 'Break — end',
            'commander.commentaire': 'Comment',
            'commander.sousTotal': 'Dishes subtotal',
            'commander.fraisLivraison': 'Delivery fees',
            'commander.totalPayer': 'Total to pay',
            'commander.valider': 'Confirm the order',

            /* ---- Order details ---- */
            'detail_commande.titre': 'Order',
            'detail_commande.subtitre': 'View the details and tracking of your order.',
            'detail_commande.retour': 'Back',
            'detail_commande.infosTitre': 'Order information',
            'detail_commande.statut': 'Status',
            'detail_commande.dateCommande': 'Order date',
            'detail_commande.dateLivraison': 'Delivery date',
            'detail_commande.heureLivraison': 'Delivery time',
            'detail_commande.zone': 'Zone',
            'detail_commande.prioritaire': 'Priority',
            'detail_commande.oui': 'Yes',
            'detail_commande.pause': 'Break',
            'detail_commande.commentaire': 'Comment',
            'detail_commande.total': 'Total',
            'detail_commande.articlesTitre': 'Ordered items',
            'detail_commande.image': 'Image',
            'detail_commande.produit': 'Product',
            'detail_commande.categorie': 'Category',
            'detail_commande.prixUnitaire': 'Unit price',
            'detail_commande.quantite': 'Quantity',
            'detail_commande.sousTotal': 'Subtotal',
            'detail_commande.aucunArticle': 'No items.',
            'detail_commande.chronologieTitre': 'Status history',
            'detail_commande.changeDe': 'Changed from',
            'detail_commande.par': 'by',
            'detail_commande.aucunHistorique': 'No status history.',

            /* ---- Notifications ---- */
            'notifications.pageTitle': 'Notifications - FiaJou3',
            'notifications.titre': 'Notifications',
            'notifications.nonLues': 'unread notification(s).',
            'notifications.toutLu': 'Mark all as read',
            'notifications.aucuneNonLue': 'You have no unread notifications.',
            'notifications.aucune': 'No notifications.',
            'notifications.lu': 'Read'
        },
        ar: {
            'login.title': 'تسجيل الدخول',
            'login.subtitle': 'سعداء بعودتك، سجّل الدخول إلى حسابك',
            'login.emailLabel': 'البريد الإلكتروني',
            'login.passwordLabel': 'كلمة المرور',
            'login.forgotPassword': 'نسيت كلمة المرور؟',
            'login.submitBtn': 'تسجيل الدخول',
            'login.noAccount': 'ليس لديك حساب؟',
            'login.registerLink': 'إنشاء حساب',
            'login.orDivider': 'أو',
            'login.googleBtn': 'المتابعة باستخدام Google',
            'login.pageTitle': 'تسجيل الدخول - فياجوع',

            'register.title': 'إنشاء حساب',
            'register.subtitle': 'أنشئ حسابك لتبدأ بالطلب',
            'register.prenomLabel': 'الاسم الأول',
            'register.nomLabel': 'اسم العائلة',
            'register.telephoneLabel': 'الهاتف',
            'register.villeLabel': 'المدينة',
            'register.adresseLabel': 'العنوان',
            'register.emailLabel': 'البريد الإلكتروني',
            'register.passwordLabel': 'كلمة المرور',
            'register.confirmationLabel': 'تأكيد كلمة المرور',
            'register.submitBtn': 'إنشاء حساب',
            'register.hasAccount': 'لديك حساب بالفعل؟',
            'register.loginLink': 'سجّل الدخول',
            'register.pageTitle': 'إنشاء حساب - فياجوع',

            'mdp.title': 'نسيت كلمة المرور',
            'mdp.subtitle': 'أدخل بريدك الإلكتروني لإعادة تعيين كلمة المرور',
            'mdp.resetTitle': 'كلمة مرور جديدة',
            'mdp.resetSubtitle': 'اختر كلمة مرور جديدة لحسابك',
            'mdp.emailLabel': 'البريد الإلكتروني',
            'mdp.newPasswordLabel': 'كلمة المرور الجديدة',
            'mdp.confirmPasswordLabel': 'تأكيد كلمة المرور',
            'mdp.submitBtn': 'إرسال الرابط',
            'mdp.resetBtn': 'إعادة التعيين',
            'mdp.backLogin': 'العودة لتسجيل الدخول',
            'mdp.pageTitle': 'نسيت كلمة المرور - فياجوع',

            'partenaire.subtitle': 'أكمل طلبك',
            'partenaire.emailNote': 'يرتبط هذا النموذج بالبريد الإلكتروني المستخدم لطلب الرابط:',
            'partenaire.prenomLabel': 'الاسم الأول',
            'partenaire.nomLabel': 'اسم العائلة',
            'partenaire.emailLabel': 'البريد الإلكتروني',
            'partenaire.emailLocked': 'البريد الإلكتروني معبأ مسبقاً من الرابط — غير قابل للتعديل.',
            'partenaire.telephoneLabel': 'الهاتف',
            'partenaire.villeLabel': 'المدينة',
            'partenaire.adresseLabel': 'العنوان',
            'partenaire.passwordLabel': 'كلمة المرور',
            'partenaire.confirmationLabel': 'تأكيد كلمة المرور',
            'partenaire.submitBtn': 'إرسال الطلب',
            'partenaire.loginLink': 'لديك حساب بالفعل؟ سجّل الدخول',
            'partenaire.backHome': 'العودة للرئيسية',
            'partenaire.errorTitle': 'رابط غير صالح',
            'partenaire.errorSubtitle': 'تعذّر فتح النموذج.',

            'parametres.titre': 'الإعدادات',
            'parametres.sousTitre': 'إدارة معلوماتك الشخصية وبريدك الإلكتروني ولغتك وكلمة المرور',
            'parametres.infosTitre': 'المعلومات الشخصية',
            'parametres.prenomLabel': 'الاسم الأول',
            'parametres.nomLabel': 'اسم العائلة',
            'parametres.telephoneLabel': 'الهاتف',
            'parametres.adresseLabel': 'العنوان',
            'parametres.villeLabel': 'المدينة',
            'parametres.enregistrerInfos': 'حفظ التعديلات',
            'parametres.emailTitre': 'البريد الإلكتروني',
            'parametres.emailLabel': 'البريد الإلكتروني',
            'parametres.emailHint': 'يُستخدم بريدك الإلكتروني لتسجيل الدخول ويجب أن يكون فريدًا',
            'parametres.changerEmail': 'تغيير البريد الإلكتروني',
            'parametres.mdpTitre': 'تغيير كلمة المرور',
            'parametres.mdpActuel': 'كلمة المرور الحالية',
            'parametres.nouveauMdp': 'كلمة المرور الجديدة',
            'parametres.confirmationMdp': 'تأكيد كلمة المرور الجديدة',
            'parametres.changerMdp': 'تغيير كلمة المرور',
            'parametres.langueTitre': 'اللغة',
            'parametres.langueSousTitre': 'اختر لغة التطبيق، وسيتم حفظها في حسابك',
            'parametres.pageTitle': 'الإعدادات - فياجوع',
            'parametres.langueActuelle': 'اللغة الحالية',

            /* ---- عام ---- */
            'common.retourAccueil': 'العودة للرئيسية',
            'common.ajouterPanier': 'أضف إلى السلة',
            'common.consulterMenu': 'تصفح القائمة',
            'common.retourMenu': 'العودة إلى القائمة',
            'common.disponible': 'متوفر',
            'common.indisponible': 'غير متوفر',
            'common.cloture': 'مغلق',
            'common.ouvert': 'مفتوح',
            'common.commandesCloturees': 'الطلبات مغلقة',
            'common.enregistrer': 'حفظ التعديلات',
            'common.annuler': 'إلغاء',
            'common.fermer': 'إغلاق',
            'common.viderPanier': 'إفراغ السلة',
            'common.passerCommande': 'تأكيد الطلب',
            'common.continuerAchats': 'مواصلة التسوق',
            'common.commander': 'اطلب',
            'common.sousTotal': 'المجموع الفرعي',
            'common.total': 'المجموع',
            'common.unite': '/ الوحدة',
            'common.supprimer': 'حذف',
            'common.livraisonLe': 'التوصيل في',
            'common.livraisonPrevueLe': 'التوصيل المتوقع في',
            'common.dateLivraisonPanier': 'تاريخ توصيل السلة:',
            'common.creezCompteCommander': 'أنشئ حساباً للطلب',
            'common.langueSelector': 'اختيار اللغة',

            /* ---- أيام الأسبوع ---- */
            'jours.lundi': 'الاثنين',
            'jours.mardi': 'الثلاثاء',
            'jours.mercredi': 'الأربعاء',
            'jours.jeudi': 'الخميس',
            'jours.vendredi': 'الجمعة',
            'jours.samedi': 'السبت',
            'jours.dimanche': 'الأحد',
            'jours.samediMenuLibre': 'السبت — قائمة حرة',

            /* ---- التنقل (القائمة الجانبية / قائمة الملف / العودة) ---- */
            'nav.accueil': 'الرئيسية',
            'nav.menu': 'القائمة',
            'nav.menuSemaine': 'قائمة الأسبوع',
            'nav.mesCommandes': 'طلباتي',
            'nav.profil': 'الملف الشخصي',
            'nav.monProfil': 'ملفي الشخصي',
            'nav.tableauBord': 'لوحة التحكم',
            'nav.produits': 'المنتجات',
            'nav.categories': 'الفئات',
            'nav.commandes': 'الطلبات',
            'nav.clients': 'العملاء',
            'nav.cuisiniers': 'الطباخون',
            'nav.livreurs': 'السعاة',
            'nav.zones': 'مناطق التوصيل',
            'nav.historique': 'السجل',
            'nav.parametres': 'الإعدادات',
            'nav.deconnexion': 'تسجيل الخروج',
            'nav.notifications': 'الإشعارات',
            'nav.monPanier': 'سلتي',
            'nav.ouvrirMonPanier': 'افتح سلتي',
            'nav.ouvrirMenu': 'افتح القائمة',
            'nav.roleAdmin': 'مدير النظام',
            'nav.roleClient': 'عميل',
            'nav.roleCuisinier': 'طباخ',
            'nav.roleLivreur': 'ساعي',

            /* ---- الرئيسية ---- */
            'accueil.pageTitle': 'فياجوع — وجبات منزلية',
            'accueil.navAccueil': 'الرئيسية',
            'accueil.navMenuSemaine': 'قائمة الأسبوع',
            'accueil.navAPropos': 'من نحن',
            'accueil.navContact': 'اتصل بنا',
            'accueil.commander': 'اطلب الآن',
            'accueil.heroEyebrow': 'وجبات مغربية ساخنة، تُوصَّل في الوقت المحدد',
            'accueil.heroTitre': 'وجبات منزلية، تُوصَّل إلى باب منزلك',
            'accueil.heroSousTitre': 'اطلب أطباقاً تُحضَّر بعناية من طباخين محليين واستلمها ساخنة على بابك ببضع نقرات.',
            'accueil.consulterMenu': 'تصفح القائمة',
            'accueil.commencerCommander': 'ابدأ الطلب',
            'accueil.offre1Titre': 'مطبخ محلي 100%',
            'accueil.offre1Sous': 'منزلي الصنع',
            'accueil.offre2Titre': 'توصيل سريع',
            'accueil.offre2Sous': 'ساخن وفي الوقت',
            'accueil.menuTitre': 'قائمة الأسبوع',
            'accueil.menuVide': 'لا توجد قائمة منشورة حالياً، عُد قريباً!',
            'accueil.autresPlats': 'أطباق إضافية',
            'accueil.voirPlats': 'عرض الأطباق',
            'accueil.consulterMenuComplet': 'تصفح القائمة الكاملة',
            'accueil.creerCompte': 'أنشئ حساباً للطلب',
            'accueil.aboutTitre': 'من نحن',
            'accueil.aboutEnSavoirPlus': 'اعرف المزيد',
            'accueil.partenaireTitre': 'انضم إلى فياجوع',
            'accueil.partenaireCuisinier': 'كن طباخاً شريكاً',
            'accueil.partenaireLivreur': 'كن ساعياً شريكاً',
            'accueil.partenaireJeMinscris': 'أسجّل الآن',
            'accueil.clientsTitre': 'ماذا يقول عملاؤنا',
            'accueil.clientsRegulier': 'عميل دائم',
            'accueil.temoignage1': 'كان الطاجين تمامًا مثل الذي كانت تحضّره جدتي، وصل ساخنًا في أقل من 40 دقيقة. أنصح به بشدة!',
            'accueil.temoignage2': 'بسيط وسريع، والأهم أنه أطباق منزلية حقيقية. أصبح كسكس يوم الجمعة طقسًا مميزًا في بيتنا.',
            'accueil.footerContact': 'اتصل بنا',
            'accueil.footerHoraires': 'أوقات الطلب',
            'accueil.footerTousLesJours': 'كل يوم',
            'accueil.footerDroits': 'جميع الحقوق محفوظة.',
            'accueil.partenaireModalTitre': 'كن شريكاً',
            'accueil.partenaireModalSub': 'انضم إلى فياجوع وأكمل طلبك.',
            'accueil.partenaireModalIntro': 'أدخل بريدك الإلكتروني: سنرسل لك رابطاً آمناً لإكمال طلبك.',
            'accueil.partenaireModalEmail': 'البريد الإلكتروني',
            'accueil.partenaireModalAnnuler': 'إلغاء',
            'accueil.partenaireModalContinuer': 'متابعة',
            'accueil.partenaireModalFermer': 'إغلاق',
            'accueil.aboutTexte': 'يربط فياجوع طباخين محليين شغوفين بعشاق الطعام. يُحضّر كل طبق حسب الطلب، مثل المنزل، ثم يُوصّل بسرعة من طرف سعاة التوصيل الشركاء القريبين منك.',
            'accueil.samediDesc': 'لا توجد قائمة خاصة بيوم السبت: اختر بحرية من بين جميع أطباق الأسبوع.',
            'accueil.partenaireCuisinierTexte': 'شارك وصفاتك المنزلية وبع أطباقك لزبائن جدد كل أسبوع.',
            'accueil.partenaireLivreurTexte': 'وصّل الطلبات في منطقتك ونظّم جولاتك حسب أوقات توفرك.',
            'accueil.footerTexte': 'وجبات منزلية يحضّرها طباخون محليون وتُوصّل إليك بسرعة.',
            'accueil.footerMaroc': 'المغرب',
            'accueil.partenaireModalSubCuisinier': 'انضم إلى فياجوع كطباخ.',
            'accueil.partenaireModalSubLivreur': 'انضم إلى فياجوع كساعي توصيل.',
            'accueil.partenaireModalEmailInvalide': 'يُرجى إدخال بريد إلكتروني صحيح.',
            'accueil.partenaireModalErreurGenerique': 'حدث خطأ ما. يُرجى المحاولة مجددا.',

            /* ---- صفحة القائمة ---- */
            'menu.pageTitle': 'القائمة - فياجوع',
            'menu.titre': 'قائمتنا',
            'menu.sousTitre': 'جميع أطباقنا، محضَّرة بعناية',
            'menu.parJour': 'حسب اليوم',
            'menu.tous': 'الكل',
            'menu.voir': 'عرض',
            'menu.ajouter': 'إضافة',
            'menu.vide': 'لا يوجد طبق متاح حالياً.',

            /* ---- صفحة قائمة الأسبوع ---- */
            'menu_semaine.pageTitle': 'قائمة الأسبوع - فياجوع',
            'menu_semaine.titre': 'قائمة الأسبوع',
            'menu_semaine.sousTitre': 'تشكيلة من الأطباق الطازجة كل يوم',
            'menu_semaine.videTexte': 'لا توجد أطباق في قائمة الأسبوع حالياً.',

            /* ---- السلة ---- */
            'panier.pageTitle': 'سلتي - فياجوع',
            'panier.titre': 'سلتي',
            'panier.vide': 'سلتك فارغة.',
            'panier.ajouterPlats': 'أضف أطباقاً من القائمة',
            'panier.dateLivraison': 'تاريخ التوصيل',
            'panier.image': 'الصورة',
            'panier.nom': 'الاسم',
            'panier.article': 'الصنف',
            'panier.quantite': 'الكمية',
            'panier.prix': 'السعر',
            'panier.actions': 'الإجراءات',
            'panier.livraison': 'التوصيل',
            'panier.gratuit': 'مجاني',
            'panier.retirerQuantite': 'إنقاص كمية',
            'panier.ajouterQuantite': 'إضافة كمية',

            /* ---- طلباتي ---- */
            'mes_commandes.pageTitle': 'طلباتي - فياجوع',
            'mes_commandes.titre': 'طلباتي',
            'mes_commandes.sousTitre': 'تابع طلباتك من السلة إلى التوصيل.',
            'mes_commandes.vide': 'لم تقم بأي طلب بعد.',
            'mes_commandes.commander': 'اطلب الآن',
            'mes_commandes.numero': 'الطلب',
            'mes_commandes.date': 'التاريخ',
            'mes_commandes.statut': 'الحالة',
            'mes_commandes.total': 'المجموع',
            'mes_commandes.detail': 'التفاصيل',
            'mes_commandes.filtrer': 'تصفية',
            'mes_commandes.filtreToutes': 'الكل',
            'mes_commandes.filtreEnCours': 'قيد التنفيذ',
            'mes_commandes.filtreLivrees': 'تم تسليمها',
            'mes_commandes.filtreAnnulees': 'ملغاة',
            'mes_commandes.dateCommande': 'تاريخ الطلب',
            'mes_commandes.dateLivraison': 'تاريخ التوصيل',
            'mes_commandes.heure': 'الوقت',
            'mes_commandes.commentaire': 'تعليق',
            'mes_commandes.statutEnAttente': 'قيد الانتظار',
            'mes_commandes.statutConfirmee': 'مؤكدة',
            'mes_commandes.statutEnPreparation': 'قيد التحضير',
            'mes_commandes.statutPrete': 'جاهزة',
            'mes_commandes.statutEnLivraison': 'قيد التوصيل',
            'mes_commandes.statutLivree': 'تم التوصيل',
            'mes_commandes.statutAnnulee': 'ملغاة',

            /* ---- الملف الشخصي (عميل) ---- */
            'profil.pageTitle': 'ملفي الشخصي - فياجوع',
            'profil.titre': 'ملفي الشخصي',
            'profil.sousTitre': 'أدر معلوماتك الشخصية وكلمة المرور.',

            /* ---- صفحة الطبق ---- */
            'produit.disponible': 'متوفر',
            'produit.indisponible': 'غير متوفر',
            'produit.ajouterPanier': 'أضف إلى السلة — التوصيل في',
            'produit.consultationSeule': 'للمشاهدة فقط — خارج قائمة الأسبوع',
            'produit.indisponibleMoment': 'غير متوفر حالياً',
            'produit.erreurIndisponible': 'هذا الطبق لم يعد متوفراً أو تم بلوغ الكمية القصوى (20).',
            'produit.erreurCloturees': 'الطلبات لهذا التاريخ مغلقة (الموعد النهائي',
            'produit.erreurClotureesFin': 'في اليوم السابق).',
            'produit.erreurHorsMenu': 'هذا الطبق غير مدرج في قائمة الأسبوع المنشورة: وهو متاح للمشاهدة فقط.',

            /* ---- تأكيد الطلب ---- */
            'commander.pageTitle': 'تأكيد الطلب - فياجوع',
            'commander.titre': 'أكّد طلبك',
            'commander.vosInfos': 'معلوماتك (الملف الشخصي)',
            'commander.infosProfil': 'هذه المعلومات من ملفك الشخصي. لتعديلها،',
            'commander.majProfil': 'حدّث ملفك الشخصي',
            'commander.livraisonInfo': 'التوصيل 7 أيام في الأسبوع. ليصل طلبك في يوم معيّن، اطلب في موعد أقصاه',
            'commander.livraisonInfoFin': 'في اليوم السابق. يوم السبت، القائمة حرة: يمكن طلب جميع أطباق الأسبوع.',
            'commander.dateLivraison': 'تاريخ التوصيل',
            'commander.heureLivraison': 'وقت التوصيل',
            'commander.zoneLivraison': 'منطقة التوصيل',
            'commander.prioritaire': 'طلب ذو أولوية',
            'commander.oui': 'نعم',
            'commander.non': 'لا',
            'commander.pauseDebut': 'استراحة — البداية',
            'commander.pauseFin': 'استراحة — النهاية',
            'commander.commentaire': 'تعليق',
            'commander.sousTotal': 'المجموع الفرعي للأطباق',
            'commander.fraisLivraison': 'رسوم التوصيل',
            'commander.totalPayer': 'الإجمالي المطلوب دفعه',
            'commander.valider': 'تأكيد الطلب',

            /* ---- تفاصيل الطلب ---- */
            'detail_commande.titre': 'الطلب',
            'detail_commande.subtitre': 'عرض تفاصيل طلبك وتتبعه.',
            'detail_commande.retour': 'رجوع',
            'detail_commande.infosTitre': 'معلومات الطلب',
            'detail_commande.statut': 'الحالة',
            'detail_commande.dateCommande': 'تاريخ الطلب',
            'detail_commande.dateLivraison': 'تاريخ التوصيل',
            'detail_commande.heureLivraison': 'وقت التوصيل',
            'detail_commande.zone': 'المنطقة',
            'detail_commande.prioritaire': 'أولوية',
            'detail_commande.oui': 'نعم',
            'detail_commande.pause': 'استراحة',
            'detail_commande.commentaire': 'تعليق',
            'detail_commande.total': 'المجموع',
            'detail_commande.articlesTitre': 'الأصناف المطلوبة',
            'detail_commande.image': 'الصورة',
            'detail_commande.produit': 'المنتج',
            'detail_commande.categorie': 'الفئة',
            'detail_commande.prixUnitaire': 'سعر الوحدة',
            'detail_commande.quantite': 'الكمية',
            'detail_commande.sousTotal': 'المجموع الفرعي',
            'detail_commande.aucunArticle': 'لا توجد أصناف.',
            'detail_commande.chronologieTitre': 'سجل الحالة',
            'detail_commande.changeDe': 'تغيّرت من',
            'detail_commande.par': 'بواسطة',
            'detail_commande.aucunHistorique': 'لا يوجد سجل للحالة.',

            /* ---- الإشعارات ---- */
            'notifications.pageTitle': 'الإشعارات - فياجوع',
            'notifications.titre': 'الإشعارات',
            'notifications.nonLues': 'إشعار(ات) غير مقروءة.',
            'notifications.toutLu': 'تعليم الكل كمقروء',
            'notifications.aucuneNonLue': 'لا توجد إشعارات غير مقروءة.',
            'notifications.aucune': 'لا توجد إشعارات.',
            'notifications.lu': 'مقروء'
        }
    };

    /* Fusion du dictionnaire complété par une vue (window.FJ_I18N_EXTRA),
       ex. titres dynamiques du dossier partenaire. */
    (function fusionnerExtra() {
        var extra = window.FJ_I18N_EXTRA || null;
        if (!extra) { return; }
        for (var i = 0; i < LANGUES.length; i++) {
            var langue = LANGUES[i];
            if (extra[langue]) {
                for (var cle in extra[langue]) {
                    if (Object.prototype.hasOwnProperty.call(extra[langue], cle)) {
                        DICT[langue][cle] = extra[langue][cle];
                    }
                }
            }
        }
    })();

    var config = window.FJ_I18N || { lang: 'fr', connecte: false, url: '' };
    var langueCourante = valide(config.lang) ? config.lang : 'fr';

    function valide(lang) {
        return LANGUES.indexOf(lang) !== -1;
    }

    function pageCourante() {
        return document.body.getAttribute('data-fj-page') || '';
    }

    function traduire(lang) {
        var d = DICT[lang] || {};
        document.querySelectorAll('[data-i18n]').forEach(function (el) {
            var cle = el.getAttribute('data-i18n');
            if (cle && d[cle] !== undefined) {
                el.textContent = d[cle];
            }
        });
        document.querySelectorAll('[data-i18n-aria]').forEach(function (el) {
            var cle = el.getAttribute('data-i18n-aria');
            if (cle && d[cle] !== undefined) {
                el.setAttribute('aria-label', d[cle]);
            }
        });
        /* Indicateur « langue actuelle » (ex. carte Langue des Paramètres) :
           affiche le nom et le code de la langue courante. */
        var NOMS_LANGUES = { fr: 'Français', en: 'English', ar: 'العربية' };
        var CODES_LANGUES = { fr: 'FR', en: 'EN', ar: 'ع' };
        document.querySelectorAll('[data-i18n-lang-current]').forEach(function (el) {
            if (NOMS_LANGUES[lang]) { el.textContent = NOMS_LANGUES[lang]; }
        });
        document.querySelectorAll('[data-i18n-lang-code]').forEach(function (el) {
            if (CODES_LANGUES[lang]) { el.textContent = CODES_LANGUES[lang]; }
        });
        var cleTitre = pageCourante() + '.pageTitle';
        if (d[cleTitre]) {
            var titre = document.getElementById('pageTitle');
            if (titre) { titre.textContent = d[cleTitre]; }
        }
    }

    function mettreAJourSelecteurs(lang) {
        document.querySelectorAll('.lang-switcher button[data-lang]').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
        });
    }

    function ecrireCookie(lang) {
        try {
            var expire = new Date(Date.now() + 365 * 864e5).toUTCString();
            document.cookie = COOKIE_KEY + '=' + lang + '; expires=' + expire + '; path=/';
        } catch (e) { /* cookies indisponibles */ }
    }

    /* Persistance : localStorage + cookie ; pour un compte connecté, sauvegarde
       silencieuse en base via la route « langue ». */
    function sauvegarder(lang) {
        try { localStorage.setItem(STORAGE_KEY, lang); } catch (e) { /* ignore */ }
        ecrireCookie(lang);
        if (!config.connecte || !config.url) { return; }
        try {
            if (navigator.sendBeacon) {
                navigator.sendBeacon(config.url, new URLSearchParams({ lang: lang }));
                return;
            }
        } catch (e) { /* repli ci-dessous */ }
        var xhr = new XMLHttpRequest();
        xhr.open('POST', config.url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('lang=' + encodeURIComponent(lang));
    }

    window.setLang = function (lang) {
        if (!valide(lang)) { return; }
        langueCourante = lang;
        traduire(lang);
        document.documentElement.lang = lang;
        document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
        mettreAJourSelecteurs(lang);

        var bootstrapCss = document.getElementById('bootstrapCss');
        var cible = lang === 'ar' ? BOOTSTRAP_RTL : BOOTSTRAP_LTR;
        if (bootstrapCss && bootstrapCss.getAttribute('href') !== cible) {
            bootstrapCss.setAttribute('href', cible);
        }

        sauvegarder(lang);
    };

    /* Applique (ou ré-applique) la traduction sur les éléments [data-i18n]
       du DOM courant — utilisée après un rafraîchissement AJAX partiel
       (ex. mini-panier rechargé par commande-ajax.js) sans changer de langue. */
    window.fjI18nAppliquer = function () {
        traduire(langueCourante);
        mettreAJourSelecteurs(langueCourante);
    };

    document.addEventListener('DOMContentLoaded', function () {
        var langue = valide(config.lang) ? config.lang : 'fr';
        try { localStorage.setItem(STORAGE_KEY, langue); } catch (e) { /* ignore */ }
        ecrireCookie(langue);
        window.setLang(langue);

        document.addEventListener('click', function (e) {
            var bouton = e.target.closest('.lang-switcher button[data-lang]');
            if (bouton) {
                window.setLang(bouton.getAttribute('data-lang'));
            }
        });
    });
})();
