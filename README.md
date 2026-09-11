# UmaniWeb Cookie Control

Plugin WordPress de bannière de consentement RGPD avec **Google Consent Mode v2**, granularité par catégorie de cookies, et injection de code GTM dans `<head>` / `<body>`.

---

## Fonctionnalités

- Bannière flottante configurable (couleurs, textes, position)
- **Consent Mode v2** complet : `ad_storage`, `ad_user_data`, `ad_personalization`, `analytics_storage`, `personalization_storage`, `functionality_storage`, `security_storage`
- Choix granulaire par catégorie : **Nécessaires** · **Analytiques** · **Marketing**
- Compatible **WPML** (textes multilingues)
- Insertion automatique du tag Google depuis un simple identifiant : `GTM-XXXXXXX` (Tag Manager) ou `G-XXXXXXXXXX` (Analytics 4)
- Injection de code arbitraire dans `<head>` et `<body>` (autre solution de mesure, pixel, vérification de domaine)
- Mise à jour automatique depuis ce dépôt GitHub

---

## Prérequis

- PHP 8.0+
- WordPress 5.8+
- WPML *(optionnel, pour le multilingue)*

---

## Installation

1. Télécharger la [dernière release](../../releases/latest) (fichier `.zip`)
2. Dans WordPress : **Extensions → Ajouter → Téléverser une extension**
3. Activer le plugin
4. Configurer via le menu **Cookies** dans l'administration

> Le dossier `vendor/` (autoload Composer) est inclus dans le dépôt — aucune commande `composer install` n'est nécessaire après installation.

---

## Configuration

### 1. Tag de suivi (obligatoire pour activer la bannière)

**Cookies → Tags et code**

Deux façons de déclarer le tag, au choix :

| Méthode | Quand l'utiliser |
|---|---|
| Champ **Identifiant du tag** | Cas courant. Saisir `GTM-XXXXXXX` ou `G-XXXXXXXXXX` : le snippet correspondant est généré et inséré automatiquement, avec le `<noscript>` de Tag Manager le cas échéant. |
| Champ **Balise `<head>`** | Snippet non standard, ou solution de mesure autre que Google. Coller le code tel quel. |

Dans les deux cas, le plugin initialise le Consent Mode v2 **avant** le tag : il n'est pas nécessaire de l'inclure dans le snippet.

Le Consent Mode v2 est compris à l'identique par Tag Manager et par `gtag.js`. Un identifiant Analytics 4 seul suffit donc à faire fonctionner la bannière, sans conteneur Tag Manager.

> Supprimer tout code Tag Manager ou Analytics préexistant sur le site avant de configurer le tag, afin d'éviter un double chargement.

### 2. Bannière de consentement

**Cookies → Bannière**

Le formulaire n'apparaît que si un tag est configuré et si une page de politique de confidentialité est publiée. La bannière s'affiche en façade lorsque les trois conditions suivantes sont réunies :

- Un tag est déclaré dans **Tags et code**, par identifiant ou par snippet
- Une page de politique de confidentialité est publiée dans **Réglages > Vie privée**
- La case **Activer la bannière** est cochée

Options disponibles : texte, libellés des boutons, couleurs de fond / texte / bouton.

### 3. Traduction (WPML)

Avec WPML installé, les textes de la bannière peuvent être saisis par langue via le sélecteur de langue dans l'interface d'administration.

---

## Mises à jour automatiques

Le plugin se met à jour directement depuis ce dépôt GitHub, sans passer par le dépôt officiel WordPress.

### Configurer le token d'accès

**Cookies → Mise à jour**

| Champ | Description |
|---|---|
| Token d'accès GitHub | Personal Access Token (PAT) avec scope `repo` (voir guide ci-dessous) |
| Nom d'utilisateur GitHub | Username GitHub du propriétaire du dépôt (ex : `umaniweb`) |
| URL du serveur | Non utilisé — laisser vide |

#### Créer un Personal Access Token sur GitHub

1. Se connecter sur **github.com**, cliquer sur l'avatar en haut à droite, puis **Settings**
   *(paramètres du compte, pas du dépôt)*
2. Dans le menu de gauche, tout en bas : **Developer settings**
3. **Personal access tokens → Tokens (classic)**
   *(ne pas utiliser "Fine-grained tokens" — non supporté par l'Updater)*
4. Cliquer **Generate new token → Generate new token (classic)**
5. Dans le champ **Note**, saisir un nom descriptif, par exemple : `umani-cookie-control-updater`
6. Dans **Select scopes**, cocher uniquement **`repo`** (accès complet aux dépôts)
7. Cliquer **Generate token** en bas de page
8. **Copier immédiatement le token affiché** — il ne sera plus visible après avoir quitté la page

Coller ce token dans le champ **Token d'accès GitHub** de la page Mise à jour du plugin.

---

## Publier une nouvelle version

### Étape 1 — Modifier le code

Apporter les modifications souhaitées dans les fichiers du plugin.

> La commande `git commit` ne peut s'exécuter que si des fichiers ont été modifiés depuis le dernier commit. En l'absence de changements, git retourne `nothing to commit, working tree clean` — ce comportement est normal. Pour vérifier si un commit existe déjà, utiliser `git log --oneline -3`.

### Étape 2 — Incrémenter la version

Dans `umani-cookie-control.php`, mettre à jour **les deux lignes suivantes** avec la même valeur :

```php
 * Version: 2.x.x
```

```php
define('UMANI_CC_VERSION', '2.x.x');
```

> Ces deux valeurs doivent être **identiques** et correspondre exactement au tag GitHub. Le header est utilisé par WordPress pour détecter la mise à jour ; la constante sert au cache-busting des assets CSS/JS.

### Étape 3 — Committer et pousser

```bash
git add -A
git commit -m "v2.x.x — description des changements"
git push origin main
```

### Étape 4 — Créer une Release sur GitHub

1. Aller dans **Releases → Create a new release**
2. **Tag** : `2.x.x` — doit correspondre **exactement** à la version dans le header du plugin
3. **Target** : `main`
4. **Titre** : `v2.x.x — Description courte`
5. **Description** : notes de version (ce texte apparaît dans la popup WordPress "Voir les détails")
6. Cliquer **Publish release** (pas "Draft", pas "Pre-release")

WordPress détectera la mise à jour dans les 12 heures. La notification apparaîtra dans **Extensions** comme pour tout plugin classique.

### Forcer la détection immédiate

Pour ne pas attendre le cache de 12 heures, supprimer les transients via WP-CLI :

```bash
wp transient delete umani_cc_github_release
wp transient delete umani_cc_github_auth
```

Ou via l'URL d'administration :

```
/wp-admin/update-core.php?force-check=1
```

---

## Architecture

```
umani-cookie-control/
├── umani-cookie-control.php     # Bootstrap du plugin
├── app/
│   ├── Plugin.php               # Orchestrateur principal
│   ├── Admin.php                # Pages d'administration
│   ├── Front.php                # Rendu front-end
│   ├── Config/
│   │   ├── options.php          # Déclaration des options (data-driven)
│   │   └── consent-categories.php  # Catégories de consentement GTM
│   ├── Field/
│   │   └── FieldRenderer.php    # Rendu sécurisé des champs admin
│   ├── I18n/
│   │   └── I18nService.php      # Gestion WPML / langue native
│   ├── Option/
│   │   └── OptionRegistrar.php  # Enregistrement des options WordPress
│   ├── Tag/
│   │   └── TagId.php            # Validation et typage de l'identifiant Google
│   └── Updater/
│       └── Updater.php          # Mise à jour depuis GitHub
├── css/
│   └── banner.css               # Styles de la bannière
├── js/
│   ├── consent.js               # Logique consentement (vanilla JS)
│   └── admin.js                 # Scripts d'administration
└── views/
    ├── banner.php               # Template HTML de la bannière
    └── admin/                   # Templates des pages admin
```
