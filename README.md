# UmaniWeb Cookie Control

Plugin WordPress de bannière de consentement RGPD avec **Google Consent Mode v2**, granularité par catégorie de cookies, et injection de code GTM dans `<head>` / `<body>`.

---

## Fonctionnalités

- Bannière de consentement configurable (texte, libellés des boutons, couleurs)
- **Consent Mode v2** complet : `ad_storage`, `ad_user_data`, `ad_personalization`, `analytics_storage`, `personalization_storage`, `functionality_storage`, `security_storage`
- Choix granulaire par catégorie : **Nécessaires** · **Analytiques** · **Marketing**
- Compatible **WPML** (textes multilingues)
- Insertion automatique du tag Google depuis un simple identifiant : `GTM-XXXXXXX` (Tag Manager) ou `G-XXXXXXXXXX` (Analytics 4)
- Injection de code arbitraire dans `<head>` et `<body>` (autre solution de mesure, pixel, vérification de domaine)
- Mise à jour automatique depuis ce dépôt GitHub, sans configuration

---

## Prérequis

- PHP 8.0+
- WordPress 5.8+
- WPML *(optionnel, pour le multilingue)*

---

## Installation

1. Télécharger le fichier **`umani-cookie-control.zip`** de la [dernière version](../../releases/latest)
2. Dans WordPress : **Extensions → Ajouter → Téléverser une extension**, ou décompresser le fichier dans le dossier des extensions (`wp-content/plugins/`, ou `web/app/plugins/` sur Bedrock)
3. Activer le plugin
4. Configurer via le menu **Cookies** dans l'administration

Le dossier du plugin doit s'appeler exactement `umani-cookie-control`, sans quoi les mises à jour ne sont pas détectées. Le fichier `umani-cookie-control.zip` le contient déjà sous ce nom. L'archive **Source code (zip)** générée par GitHub, seule disponible pour les versions antérieures à 2.2.0, porte un nom du type `umaniweb-umani-cookie-control-<commit>` : renommer le dossier avant de l'installer.

> Le dossier `vendor/` (autoload Composer) est inclus dans le dépôt : aucune commande `composer install` n'est nécessaire après installation.

---

## Configuration

### 1. Tag de suivi (obligatoire pour activer la bannière)

**Cookies → Tags et code**

Deux façons de déclarer le tag, au choix :

| Méthode | Quand l'utiliser |
|---|---|
| Champ **Identifiant du tag** | Cas courant. Saisir `GTM-XXXXXXX` ou `G-XXXXXXXXXX` : le snippet correspondant est généré et inséré automatiquement, avec le `<noscript>` de Tag Manager le cas échéant. |
| Champ **Balise `<head>`** | Snippet non standard, ou solution de mesure autre que Google. Coller le code tel quel. |

Le champ **Balise `<body>`** reçoit le code à placer juste après l'ouverture de `<body>`.

Dans tous les cas, le plugin initialise le Consent Mode v2 **avant** le tag : il n'est pas nécessaire de l'inclure dans le snippet.

Le Consent Mode v2 est compris à l'identique par Tag Manager et par `gtag.js`. Un identifiant Analytics 4 seul suffit donc à faire fonctionner la bannière, sans conteneur Tag Manager.

> Supprimer tout code Tag Manager ou Analytics préexistant sur le site avant de configurer le tag, afin d'éviter un double chargement.

### 2. Bannière de consentement

**Cookies → Bannière**

Le formulaire n'apparaît que si un tag est configuré et si une page de politique de confidentialité est publiée. La bannière s'affiche en façade lorsque les trois conditions suivantes sont réunies :

- Un tag est déclaré dans **Tags et code**, par identifiant ou par snippet
- Une page de politique de confidentialité est publiée dans **Réglages > Vie privée**
- La case **Activer la bannière** est cochée

Options disponibles :

| Groupe | Champs |
|---|---|
| Texte | Texte de la bannière |
| Boutons | Politique, Accepter tout, Refuser, Paramétrer, Sauvegarder les préférences |
| Couleurs | Fond, texte, lien, fond du bouton, texte du bouton |

### 3. Traduction (WPML)

Avec WPML installé, les textes de la bannière peuvent être saisis par langue via le sélecteur de langue dans l'interface d'administration.

---

## Comportement en façade

Chaque catégorie accorde ses signaux Consent Mode :

| Catégorie | Signaux | Par défaut |
|---|---|---|
| Nécessaires | `security_storage`, `functionality_storage` | toujours accordés |
| Analytiques | `analytics_storage`, `personalization_storage` | refusés |
| Marketing | `ad_storage`, `ad_user_data`, `ad_personalization` | refusés |

- Les signaux par défaut sont posés avant le chargement du tag, puis mis à jour selon le choix du visiteur.
- Le choix est enregistré dans le cookie `umani_consent` (`Secure`, `SameSite=Lax`) : 365 jours si au moins une catégorie facultative est acceptée, 1 jour si tout est refusé. La bannière réapparaît donc le lendemain d'un refus total.
- Une fois le choix fait, la bannière n'est plus affichée sur les pages suivantes.
- Aucun lien ne permet pour l'instant de rouvrir les préférences : pour changer d'avis, le visiteur supprime le cookie `umani_consent` de son navigateur.

---

## Mises à jour automatiques

Le plugin se met à jour directement depuis ce dépôt GitHub, sans passer par le dépôt officiel WordPress. Aucune configuration n'est nécessaire : le dépôt étant public, la dernière version est lue sans authentification sur l'API GitHub.

- La vérification a lieu dans l'administration, et son résultat est gardé en cache 12 heures.
- La mise à jour s'installe depuis **Extensions**, comme pour tout plugin classique.
- L'API GitHub accepte 60 requêtes anonymes par heure et par adresse IP : avec le cache de 12 heures, cette limite n'est pas atteinte, même avec de nombreux sites sur un même serveur.

Depuis la version 2.2.0, la page **Cookies → Mise à jour** et ses champs (nom d'utilisateur, token, URL du serveur) n'existent plus. Les valeurs enregistrées par les versions antérieures sont supprimées de la base au premier passage dans l'administration.

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

La publication déclenche le workflow `.github/workflows/release.yml`, qui joint à la version le fichier `umani-cookie-control.zip` (dossier déjà nommé `umani-cookie-control`). Vérifier sa présence dans la page de la version une minute plus tard.

WordPress détectera la mise à jour dans les 12 heures. La notification apparaîtra dans **Extensions** comme pour tout plugin classique.

### Forcer la détection immédiate

Pour ne pas attendre le cache de 12 heures, supprimer le transient via WP-CLI :

```bash
wp transient delete umani_cc_github_release
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
├── img/                         # Icônes de la bannière
├── js/
│   ├── consent.js               # Logique consentement (vanilla JS)
│   └── admin.js                 # Scripts d'administration
├── languages/                   # Traductions (.po / .mo)
└── views/
    ├── banner.php               # Template HTML de la bannière
    └── admin/                   # Pages (tags, bannière) et notices d'administration
```
