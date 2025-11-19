# 🤝 Guide de Travail avec Claude Code

## 📋 Vue d'ensemble

Ce guide explique comment collaborer efficacement avec Claude dans ce chat, gérer les branches, et intégrer les modifications en toute sécurité.

---

## 🎯 Principe de fonctionnement

### Comment Claude travaille

1. **Claude crée des branches dédiées** : `claude/nom-descriptif-SESSIONID`
2. **Chaque session = une branche** : Chaque conversation a sa propre branche
3. **Claude commit et push** sur sa branche automatiquement
4. **Vous décidez** quand et comment intégrer ces changements

### Vos branches locales

- Vous travaillez sur `develop` (ou autre branche)
- Vos modifications restent séparées de celles de Claude
- Vous gardez le contrôle total

---

## 🔄 Méthodes de Travail Recommandées

### ⭐ Méthode 1 : EXAMINER PUIS INTÉGRER (Recommandée)

**La plus sûre pour éviter les erreurs**

#### Étape 1 : Récupérer les modifications de Claude
```bash
# Récupérer toutes les branches distantes
git fetch origin

# Voir la liste des branches Claude disponibles
git branch -r | grep claude/
```

#### Étape 2 : Examiner les changements AVANT de les intégrer
```bash
# Voir les fichiers modifiés par Claude
git diff develop..origin/claude/nom-de-la-branche --name-only

# Voir le détail des modifications
git diff develop..origin/claude/nom-de-la-branche

# Ou utiliser un outil visuel (plus facile)
git difftool develop..origin/claude/nom-de-la-branche
```

#### Étape 3 : Créer une branche de test (SÉCURITÉ)
```bash
# Créer une branche de test pour vérifier
git checkout -b test-integration-claude

# Merger les changements de Claude dans cette branche de test
git merge origin/claude/nom-de-la-branche

# Tester l'application
npm install  # ou composer install pour PHP
npm run dev  # ou votre commande de test
```

#### Étape 4 : Si tout est OK, intégrer dans develop
```bash
# Retourner sur develop
git checkout develop

# Merger la branche Claude (ou la branche de test)
git merge origin/claude/nom-de-la-branche

# Ou cherry-pick des commits spécifiques si besoin
git cherry-pick <commit-hash>
```

#### Étape 5 : Nettoyer (optionnel)
```bash
# Supprimer la branche de test locale
git branch -D test-integration-claude
```

---

### 🚀 Méthode 2 : CHECKOUT DIRECT (Plus rapide)

**Quand vous faites confiance aux modifications**

```bash
# Récupérer les modifications
git fetch origin

# Checkout directement sur la branche Claude
git checkout claude/nom-de-la-branche

# Examiner les fichiers localement
ls -la
cat fichier-modifie.php

# Tester l'application
npm run dev

# Si OK, merger dans develop
git checkout develop
git merge claude/nom-de-la-branche
```

---

### 🔍 Méthode 3 : REVUE PAR FICHIER (Contrôle précis)

**Pour intégrer sélectivement certains fichiers**

```bash
# Récupérer les modifications
git fetch origin

# Voir les fichiers modifiés
git diff develop..origin/claude/nom-de-la-branche --name-only

# Checkout un fichier spécifique de la branche Claude
git checkout origin/claude/nom-de-la-branche -- chemin/vers/fichier.php

# Répéter pour chaque fichier que vous voulez
# Puis commit
git add .
git commit -m "Intégration sélective des modifications de Claude"
```

---

## 📊 Commandes Utiles pour Suivre les Modifications

### Voir l'état actuel
```bash
# Votre branche actuelle
git branch

# État de votre working directory
git status

# Historique récent
git log --oneline -10
```

### Comparer avec les branches Claude
```bash
# Lister toutes les branches Claude distantes
git branch -r | grep claude/

# Voir combien de commits d'avance/retard
git rev-list --left-right --count develop...origin/claude/nom-branche

# Voir les commits de Claude
git log origin/claude/nom-branche --oneline

# Voir ce que Claude a modifié (résumé)
git diff develop..origin/claude/nom-branche --stat

# Voir les modifications détaillées
git diff develop..origin/claude/nom-branche
```

### Outils visuels (recommandé)
```bash
# GitKraken, SourceTree, ou VS Code Git Graph
# Ou en ligne de commande :
gitk develop origin/claude/nom-branche &

# Ou avec un outil interactif
git log --graph --oneline --all
```

---

## ✅ Bonnes Pratiques

### Avant de demander à Claude de travailler

1. **Commit vos modifications locales**
   ```bash
   git add .
   git commit -m "WIP: mes modifications en cours"
   ```

2. **Indiquez à Claude votre contexte**
   - "Je suis sur la branche develop"
   - "J'ai des modifications non commitées"
   - "Je veux que tu travailles sur X"

### Pendant que Claude travaille

1. **Continuez sur votre branche** (develop)
2. **Ne travaillez PAS sur la branche Claude** en même temps
3. **Laissez Claude finir** avant de récupérer ses modifications

### Après que Claude a terminé

1. **Récupérez les modifications** : `git fetch origin`
2. **EXAMINEZ les changements** : `git diff develop..origin/claude/branche`
3. **Testez dans une branche séparée** (méthode 1)
4. **Intégrez seulement si OK**

### Gestion des branches Claude

```bash
# Supprimer les anciennes branches Claude locales
git branch -D claude/ancienne-branche

# Supprimer les branches Claude distantes (après intégration)
git push origin --delete claude/ancienne-branche
```

---

## 🚨 Situations Courantes et Solutions

### "J'ai des conflits lors du merge"

```bash
# Annuler le merge
git merge --abort

# Examiner les différences
git diff develop..origin/claude/branche

# Merger avec stratégie
git merge origin/claude/branche -X theirs  # Prendre les changements de Claude
# ou
git merge origin/claude/branche -X ours    # Garder vos changements
```

### "Je veux annuler une intégration"

```bash
# Si pas encore push
git reset --hard HEAD~1

# Si déjà push (créer un commit inverse)
git revert HEAD
```

### "Je veux voir exactement ce que Claude a fait"

```bash
# Liste des fichiers modifiés avec stats
git diff develop..origin/claude/branche --stat

# Diff pour chaque fichier
git diff develop..origin/claude/branche -- fichier.php

# Voir le code avant/après côte à côte
git difftool develop..origin/claude/branche
```

### "Claude a créé plusieurs branches"

```bash
# Lister toutes les branches Claude
git branch -r | grep claude/

# Voir les dates de dernière modification
git for-each-ref --sort=-committerdate refs/remotes/origin/claude/ --format='%(committerdate:short) %(refname:short)'

# Choisir celle qui correspond à votre session actuelle
```

---

## 📝 Workflow Recommandé (Résumé)

### Cycle complet de travail

```bash
# 1. AVANT : Sauvegarder votre travail
git checkout develop
git add .
git commit -m "Sauvegarde avant Claude"

# 2. DEMANDER À CLAUDE de travailler
# (dans le chat)

# 3. APRÈS : Récupérer et examiner
git fetch origin
git diff develop..origin/claude/nouvelle-branche --stat

# 4. TESTER dans une branche dédiée
git checkout -b test-claude
git merge origin/claude/nouvelle-branche
npm run dev  # Tester !

# 5. INTÉGRER si OK
git checkout develop
git merge test-claude
git push origin develop

# 6. NETTOYER
git branch -D test-claude
```

---

## 🎓 Exemple Concret

### Situation actuelle mentionnée

Vous avez dit : *"ta branch claude/analyze-easyadmin-seo-01EE9FA89ALv4ahdkYoAUgpi est 2 commits ahead of main"*

#### Voici comment procéder :

```bash
# 1. Récupérer cette branche
git fetch origin

# 2. Voir ce qu'elle contient
git log main..origin/claude/analyze-easyadmin-seo-01EE9FA89ALv4ahdkYoAUgpi --oneline
# Cela vous montre les 2 commits

# 3. Voir les fichiers modifiés
git diff main..origin/claude/analyze-easyadmin-seo-01EE9FA89ALv4ahdkYoAUgpi --name-only

# 4. Voir le détail des modifications
git diff main..origin/claude/analyze-easyadmin-seo-01EE9FA89ALv4ahdkYoAUgpi

# 5. Checkout pour examiner en local (SAFE)
git checkout -b review-claude-seo
git merge origin/claude/analyze-easyadmin-seo-01EE9FA89ALv4ahdkYoAUgpi

# 6. Tester votre application
# ...

# 7. Si OK, intégrer dans develop
git checkout develop
git merge review-claude-seo

# 8. Ou intégrer dans main si c'est votre branche de travail
git checkout main
git merge origin/claude/analyze-easyadmin-seo-01EE9FA89ALv4ahdkYoAUgpi
```

---

## 🛡️ Sécurité et Prévention des Erreurs

### ✅ À FAIRE

- ✅ Toujours `git fetch` avant d'examiner
- ✅ Utiliser `git diff` pour voir les changements AVANT de merger
- ✅ Tester dans une branche séparée d'abord
- ✅ Commit votre travail avant de récupérer les modifications de Claude
- ✅ Garder develop/main propre et stable

### ❌ À ÉVITER

- ❌ Ne jamais `git pull` directement sur les branches Claude
- ❌ Ne jamais merger sans avoir examiné les changements
- ❌ Ne jamais travailler directement sur une branche `claude/...`
- ❌ Ne jamais forcer push (`git push -f`) sur develop/main
- ❌ Ne jamais merger sans avoir testé

---

## 🆘 En Cas de Problème

### Si vous êtes perdu

```bash
# Voir où vous êtes
git status
git branch

# Retourner sur develop (annule les modifications non commitées)
git checkout develop

# Ou sauvegarder temporairement vos modifications
git stash
git checkout develop
git stash pop  # Pour les récupérer plus tard
```

### Si quelque chose ne va pas

```bash
# Annuler le dernier commit (garde les modifications)
git reset --soft HEAD~1

# Annuler le dernier commit (supprime les modifications)
git reset --hard HEAD~1

# Voir l'historique de vos actions
git reflog

# Retourner à un état précédent
git reset --hard <commit-hash>
```

---

## 📱 Communication avec Claude

### Informations utiles à donner

- "Je suis sur la branche `develop`"
- "J'ai des modifications non commitées sur les fichiers X, Y"
- "Je veux que tu crées une nouvelle feature pour..."
- "Peux-tu corriger le bug dans le fichier X ?"

### Questions à poser à Claude

- "Quels fichiers as-tu modifiés ?"
- "Peux-tu me résumer les changements ?"
- "Est-ce que je dois lancer des commandes spécifiques après avoir intégré tes modifications ?"
- "Y a-t-il des dépendances à installer ?"

---

## 🎯 Checklist Rapide

Avant chaque intégration :

- [ ] Mes modifications sont commitées
- [ ] J'ai fait `git fetch origin`
- [ ] J'ai examiné les modifications avec `git diff`
- [ ] J'ai testé dans une branche séparée
- [ ] Les tests passent
- [ ] L'application fonctionne correctement
- [ ] Je comprends ce qui a été modifié
- [ ] J'ai lu les messages de commit de Claude

---

## 📚 Ressources Supplémentaires

### Commandes Git essentielles
- `git fetch` : Récupère les branches distantes (SAFE)
- `git diff` : Compare les changements (SAFE)
- `git log` : Historique des commits (SAFE)
- `git merge` : Fusionne les branches (MODIFIE)
- `git checkout` : Change de branche (SAFE si commit avant)

### Outils recommandés
- **VS Code** : Extension Git Graph
- **GitKraken** : Interface visuelle complète
- **SourceTree** : Alternative gratuite
- **Sublime Merge** : Léger et rapide

---

## ✨ En Résumé

**La méthode la plus sûre :**

1. `git fetch origin` - Récupérer
2. `git diff develop..origin/claude/branche` - Examiner
3. `git checkout -b test-claude` - Créer branche de test
4. `git merge origin/claude/branche` - Merger dans test
5. Tester l'application
6. `git checkout develop && git merge test-claude` - Intégrer si OK

**Règle d'or : TOUJOURS examiner avant d'intégrer ! 🔍**

---

*Document créé le 2025-11-18 pour faciliter la collaboration avec Claude Code*
