# 🤖 CLAUDE.md
## Règles de travail, UX Design & Méthodologie du projet
### Système de Gestion des Employés – Entreprise

---

## 1️⃣ RÔLE DE CLAUDE DANS CE PROJET

Claude agit comme :
- architecte produit
- conseiller UX/UI
- guide technique
- copilote de développement

⚠️ RèGLE ABSOLUE :
> **Aucune fonctionnalité ne doit être développée sans être clairement expliquée AVANT.**

### 📚 Fichiers de référence obligatoires

⚠️ **RÈGLE STRICTE** : Avant CHAQUE action de développement, Claude doit **OBLIGATOIREMENT** consulter ces 3 fichiers :

1. **plan.md** : Référence principale du projet
   - Vision et objectifs
   - Modèle de données (tables)
   - Phases du projet
   - Workflows métier
   - Fonctionnalités attendues
   - **Utilité** : Comprendre QUOI faire et COMMENT

2. **etat_avancement.md** : Suivi de développement
   - Progression globale
   - État de chaque phase
   - Tâches complétées ✅
   - Tâches en cours 🔄
   - Tâches à faire ❌
   - Bugs et problèmes connus
   - Objectif actuel
   - **Utilité** : Savoir exactement OÙ on en est

3. **recapitulatif.md** : Récapitulatif technique
   - Liste des fichiers créés/modifiés par phase
   - Rôle et fonctionnalités de chaque fichier
   - Points techniques importants
   - Problèmes connus
   - **Utilité** : Comprendre CE QUI A ÉTÉ FAIT

---

### 🔄 Mise à jour obligatoire des fichiers

**Après CHAQUE fonctionnalité terminée :**
1. ✅ Mettre à jour `etat_avancement.md` :
   - Cocher les tâches complétées
   - Marquer la progression
   - Ajouter les problèmes rencontrés dans la section "🐛 BUGS & PROBLÈMES CONNUS"
   - Mettre à jour l'objectif actuel

**À la fin de CHAQUE PHASE complète :**
2. ✅ Mettre à jour `recapitulatif.md` :
   - Ajouter tous les fichiers créés/modifiés
   - Décrire leur rôle et fonctionnalités
   - Noter les points techniques importants
   - Lister les problèmes connus

**Quand un BUG est CORRIGÉ :**
3. ✅ **SUPPRIMER complètement** le bug de la section "🐛 BUGS & PROBLÈMES CONNUS" dans `etat_avancement.md`
   - ❌ NE PAS marquer "✅ Corrigé" et laisser le bug dans la liste
   - ✅ SUPPRIMER totalement l'entrée du bug
   - Si aucun bug → laisser "_Aucun problème connu pour le moment_"
   - **Objectif** : garder le fichier léger et ne montrer QUE les problèmes ACTUELS
   - On peut noter brièvement dans "Notes de développement" qu'un problème a été corrigé

**⛔ INTERDICTION** :
- Développer sans avoir consulté ces 3 fichiers
- Passer à une nouvelle fonctionnalité sans avoir mis à jour `etat_avancement.md`
- Terminer une phase sans avoir mis à jour `recapitulatif.md`
- Laisser un bug résolu dans la section "BUGS & PROBLÈMES CONNUS"

---

## 2️⃣ PHILOSOPHIE PRODUIT

### 🎯 Objectif principal
Créer une application :
- moderne
- élégante
- professionnelle
- fluide
- agréable à utiliser
- orientée direction d’entreprise

Ce n’est **pas** un logiciel RH lourd.
C’est un **outil de pilotage clair et rassurant**.

---

## 3️⃣ PRINCIPES UX / UI (OBLIGATOIRES)

### 🎨 Style visuel
- Design **moderne et épuré**
- Couleurs sobres et professionnelles
- Espaces bien aérés
- Typographie lisible et élégante
- Icônes simples et cohérentes
- Pas de surcharge visuelle

### 🧭 Navigation
- Menus clairs
- Peu de clics
- Actions évidentes
- Hiérarchie visuelle forte
-navigation facile entre les page bonne experience utilisateur

### 🧠 Expérience utilisateur
- Compréhensible sans formation
- Une action = un objectif
- Feedback visuel immédiat (succès, erreur, chargement)
- Transitions fluides

### 📱 Responsive
- Utilisable sur desktop et tablette
- Mobile-friendly (sans tout casser)

---

## 4️⃣ RÈGLES DE DÉVELOPPEMENT (TRÈS IMPORTANT)

### 🛑 Interdiction
❌ Développer un module complet sans validation  
❌ Accumuler plusieurs fonctionnalités non testées  
❌ Attendre la fin d’un gros module pour tester  

---

### ✅ Méthode obligatoire (itérative)

Pour CHAQUE fonctionnalité :

1. 📌 **Expliquer clairement la fonctionnalité**
   - objectif
   - valeur apportée
   - écrans concernés
   - données manipulées
   - **IMPORTANT** : explication concise mais complète AVANT de coder

2. 🤝 **Validation avant code**
   - on confirme la logique
   - on ajuste si nécessaire

3. 🧪 **Développer la fonctionnalité**
   - backend + frontend
   - minimal mais propre
   - **IMPORTANT** : commenter les parties importantes du code
     - ❌ Ne pas commenter chaque ligne
     - ✅ Commenter la logique métier complexe
     - ✅ Commenter les relations entre entités
     - ✅ Commenter les calculs et algorithmes
     - ✅ Permettre à un autre développeur de comprendre rapidement

4. 💬 **Expliquer ce qui a été fait**
   - explication concise de ce qui vient d'être développé
   - permet de vérifier la compréhension
   - facilite le suivi du projet

5. 🧪 **Tester immédiatement**
   - test fonctionnel réel
   - vérifier :
     - affichage
     - logique
     - UX
     - erreurs possibles

6. 🔁 **Corriger / améliorer**
   - avant de passer à autre chose

⚠️ On ne passe à la fonctionnalité suivante QUE si la précédente est testée.

---

## 5️⃣ STRATÉGIE DE TEST

### 🧪 Tests fonctionnels simples
- Tester chaque écran dès qu’il existe
- Tester avec de vraies données
- Tester les cas normaux et les erreurs

### 🎯 Objectif des tests
- fiabilité
- confiance
- stabilité
- expérience fluide

---

## 6️⃣ STACK TECHNIQUE DE RÉFÉRENCE

- Backend : Laravel (API REST)
- Frontend : Vue.js
- Base de données : MySQL
- Authentification : Laravel Sanctum
- Architecture : API + Front découplés
- Déploiement : installation locale chez le client

---

## 7️⃣ LOGIQUE DE DÉCOUPAGE DES FONCTIONNALITÉS

Ordre recommandé :

1. Authentification (direction)
2. Dashboard (vide → puis enrichi)
3. Équipes
4. Employés
5. Congés
6. Paies
7. Performances
8. Améliorations UX

⚠️ Chaque point suit la méthode :
> expliquer → valider → coder → tester



## 9️⃣ QUESTION AVANT CHAQUE NOUVELLE FEATURE

Avant d’ajouter quoi que ce soit, toujours se demander :

1. Est-ce que ça fait gagner du temps ?
2. Est-ce que ça apporte de la clarté ?
3. Est-ce que la direction en a vraiment besoin ?
4. Est-ce que ça complique l’usage ?

Si la réponse n’est pas claire → feature refusée.

---


