# 📘 PLAN DU PROJET
## Système de Gestion des Employés – Entreprise (Installation locale)

---

## 1️⃣ CONTEXTE & VISION

### 🎯 Objectif du projet
Créer un **système simple de gestion des employés** destiné aux **entreprises de 10 à 30 employés**, avec une organisation **mixte (bureau + terrain)**.

Le système doit permettre à la **direction uniquement** de :
- organiser les équipes
- gérer les congés
- suivre les performances
- assurer la traçabilité des paies
- avoir une vision claire sans complexité

---

### 💡 Problèmes résolus
- Manque d’organisation interne
- Absence d’historique clair (congés, paies)
- Conflits liés aux paiements
- Difficulté à évaluer les employés
- Perte de temps dans la gestion manuelle

---

### 🚫 Ce que le système NE FAIT PAS
- Pas de pointage
- Pas de géolocalisation
- Pas de biométrie
- Pas de paie RH complexe
- Pas d’accès employé
- Pas de multi-site

---

## 2️⃣ CIBLE UTILISATEUR

### 👤 Utilisateur unique
- **Direction**
  - accès total
  - aucune gestion de rôles complexe

---

## 3️⃣ STACK TECHNIQUE

### 🧱 Architecture
- Backend : Laravel (API REST)
- Frontend : Vue.js
- Base de données : MySQL
- Authentification : Laravel Sanctum
- Déploiement : installation locale (serveur ou VPS client)ou sur vercel

---

## 4️⃣ MODÈLE DE DONNÉES (TABLES)

### `users`
- id
- name
- email
- password
- role (default: 'direction')
- actif (boolean, default: true)
- created_at
- updated_at

---

### `equipes`
- id
- nom
- description
- chef_equipe_id (nullable, foreign key → employes.id)
- statut (default: 'active') - active, inactive, dissoute
- created_at
- updated_at
- deleted_at (soft delete)

---

### `employes`
- id
- nom
- prenom
- email (nullable)
- telephone (nullable)
- poste
- salaire_mensuel
- date_embauche
- date_sortie (nullable)
- statut (actif, suspendu, sorti)
- solde_conges_annuel (default: 30)
- equipe_id (foreign key → equipes.id)
- created_at
- updated_at
- deleted_at (soft delete)

---

### `conges`
- id
- employe_id (foreign key → employes.id)
- type (annuel, maladie, exceptionnel)
- date_debut
- date_fin
- nb_jours (calculé automatiquement)
- statut (en_attente, valide, refuse)
- commentaire (nullable)
- valide_par_user_id (nullable, foreign key → users.id)
- date_validation (nullable)
- created_at
- updated_at
- deleted_at (soft delete)

---

### `paies`
- id
- employe_id (foreign key → employes.id)
- mois (YYYY-MM)
- salaire_base (decimal)
- primes (decimal, default: 0)
- retenues (decimal, default: 0)
- montant_total (calculé : base + primes - retenues)
- date_paiement (nullable)
- mode_paiement (cash, virement)
- statut (paye, non_paye)
- reference_paiement (nullable) - n° virement, reçu
- commentaire (nullable)
- enregistre_par_user_id (foreign key → users.id)
- created_at
- updated_at
- deleted_at (soft delete)

---

### `performances`
- id
- employe_id (foreign key → employes.id)
- mois (YYYY-MM)
- note (1 à 5)
- commentaire (nullable)
- evalue_par_user_id (foreign key → users.id)
- created_at
- updated_at
- deleted_at (soft delete)

---

## 5️⃣ PHASES DU PROJET

---

### 🔹 PHASE 1 : INITIALISATION

#### 1. Installation du système

- Configuration base de données
- Création du compte direction

---

#### 2. Configuration de l’entreprise
- Création des équipes / services
- Paramétrage simple (modes de paiement, types de congés)

---

### 🔹 PHASE 2 : MISE EN PLACE DES DONNÉES

#### 3. Ajout des employés
- Création des fiches employés
- Affectation à une équipe
- Définition du salaire mensuel
- Statut actif

---

### 🔹 PHASE 3 : UTILISATION COURANTE

#### 4. Gestion des congés
- L’employé fait une demande (oral / papier / WhatsApp)
- La direction enregistre la demande dans le système
- Validation ou refus
- Historique conservé

**Valeur :**
- organisation
- visibilité
- traçabilité

---

#### 5. Organisation des équipes
- Vue par équipe
- Identification des employés absents (congés)
- Meilleure répartition du travail

---

### 🔹 PHASE 4 : GESTION MENSUELLE

#### 6. Suivi des performances
- En fin de mois :
  - note de 1 à 5
  - commentaire
- Historique par employé

**Valeur :**
- décisions justes
- base pour primes / sanctions

---

#### 7. Gestion des paies
- Sélection du mois
- Enregistrement des paiements :
  - payé / non payé
  - date
  - mode de paiement
- Commentaire si besoin

**Valeur :**
- preuve de paiement
- fin des conflits
- sérénité direction

---

### 🔹 PHASE 5 : CONSULTATION & DÉCISION

#### 8. Dashboard direction
Affiche :
- nombre d’employés actifs
- congés en cours
- employés non payés
- performance moyenne par équipe

---

#### 9. Fiche employé (historique)
- congés
- paies
- performances

**Valeur :**
- mémoire d’entreprise
- aide à la décision

---

## 6️⃣ WORKFLOW GLOBAL (RÉSUMÉ)

1. Installation → compte direction
2. Création équipes
3. Ajout employés
4. Gestion congés (au fil du temps)
5. Suivi équipes
6. Évaluation performances (mensuel)
7. Enregistrement paies (mensuel)
8. Consultation dashboard & historiques

---

## 7️⃣ VALEUR COMMERCIALE

> Outil simple pour organiser les équipes, suivre les congés, garder une trace des paies et prendre de meilleures décisions, sans complexité.


## 9️⃣ ÉVOLUTIONS POSSIBLES (PLUS TARD)

- Accès employé
- Notifications
- Export comptable
- Multi-sites
- Statistiques avancées

