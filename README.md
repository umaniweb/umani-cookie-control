# UmaniWeb Cookie Control

Plugin WordPress de bannière de consentement RGPD avec **Google Consent Mode v2**, granularité par catégorie de cookies, et injection de code GTM dans `<head>` / `<body>`.

---

## Fonctionnalités

- Bannière flottante configurable (couleurs, textes, position)
- **Consent Mode v2** complet : `ad_storage`, `ad_user_data`, `ad_personalization`, `analytics_storage`, `personalization_storage`, `functionality_storage`, `security_storage`
- Choix granulaire par catégorie : **Nécessaires** · **Analytiques** · **Marketing**
- Compatible **WPML** (textes multilingues)
- Injection de code arbitraire dans `<head>` et `<body>` (GTM ou autre)
- Mise à jour automatique depuis ce dépôt GitHub

---

## Installation

1. Télécharger la [dernière release](../../releases/latest) (fichier `.zip`)
2. Dans WordPress : **Extensions → Ajouter → Téléverser une extension**
3. Activer le plugin
4. Configurer dans **Cookie Consent** dans le menu admin

---

## Configuration

### 1. Code GTM (obligatoire pour activer la bannière)

**Cookie Consent → Code Insertion**

Coller le snippet GTM dans le champ `<head>`. Le plugin gère automatiquement l'initialisation du Consent Mode v2 avant ce code — inutile de l'inclure dans le snippet GTM.

> Retirer tout code GTM ou analytics existant sur le site avant de coller le nouveau snippet pour éviter les conflits.

### 2. Bannière de consentement

**Cookie Consent → Consent Banner**

La bannière n'apparaît que si :
- Le code GTM a été renseigné dans l'onglet "Code Insertion"
- Une page de politique de confidentialité est définie dans **Réglages → Vie privée**
- La case "Activer la bannière" est cochée

Options disponibles : texte, libellés des boutons, couleurs de fond / texte / bouton.

### 3. Traduction (WPML)

Avec WPML installé, les textes de la bannière peuvent être saisis par langue via le sélecteur de langue dans l'admin.

---

## Mises à jour automatiques

Le plugin se met à jour directement depuis ce dépôt GitHub, sans passer par le dépôt officiel WordPress.

### Configurer le token d'accès

**Cookie Consent → Mise à jour**

| Champ | Description |
|---|---|
| Token d'accès GitHub | Personal Access Token (PAT) avec scope `repo` |
| Nom d'utilisateur GitHub | Votre username GitHub |

Pour créer un token : **GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)** → scope `repo`.

---

## Publier une nouvelle version

### Étape 1 — Incrémenter la version

Dans `umani-cookie-control.php`, mettre à jour la ligne :

```php
 * Version: 2.x.x
```

### Étape 2 — Committer et pousser

```bash
git add -A
git commit -m "v2.x.x — description des changements"
git push origin main
```

### Étape 3 — Créer une Release sur GitHub

1. Aller dans **Releases → Create a new release**
2. **Tag** : `2.x.x` — doit correspondre **exactement** à la version dans le header du plugin
3. **Target** : `main`
4. **Titre** : `v2.x.x — Description courte`
5. **Description** : notes de version (ce texte apparaît dans la popup WordPress "Voir les détails")
6. Cliquer **Publish release** (pas "Draft", pas "Pre-release")

WordPress détectera la mise à jour dans les 12 heures. La notification apparaîtra dans **Extensions** comme pour tout plugin classique.

### Forcer la détection immédiate

Pour ne pas attendre le cache de 12h, supprimer les transients via WP-CLI :

```bash
wp transient delete umani_cc_github_release
wp transient delete umani_cc_github_auth
```

Ou via l'URL admin :

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

---

## Prérequis

- PHP 8.0+
- WordPress 5.8+
- WPML *(optionnel, pour le multilingue)*
