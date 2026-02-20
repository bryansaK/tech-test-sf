# Test technique – API Événements

```bash
make start
make setup
```

---

## 🎯 Ta mission

Compléter et optimiser cette API d'événements. Le squelette est déjà en place.

---

### 📡 1. Endpoints API

L'endpoint `/events` retourne actuellement une liste brute de tous les événements.

| Endpoint | À faire |
|------|--------|
| **GET /events** | Ajouter de la pagination et du filtrage (par exemple par date à venir) | 
| **GET /events/{id}** | Créer la route détail qui retourne un événement unique par son UUID |
| **Sérialisation** | Utiliser le Serializer Symfony au lieu des tableaux manuels |

---

### ⚡ 2. Optimisation & Robustesse

Le code actuel contient des simplifications volontaires.

- **Performance** — L'endpoint liste retourne 1000 items d'un coup → Aïe !
- **Architecture** — Sortir la logique du contrôleur ?
- **Qualité** — Ajouter des tests Fonctionnels

---

### 📅 3. Bonus

Si tu es chaud :

→ Recherche full-text sur le titre/description
→ Créer une commande console pour importer des événements depuis un fichier CSV ou une API externe

---

## 📋 Critères d’évaluation

- **Code** — Respect des standards Symfony 7, typage strict
- **Architecture** — Clarté, découpage, utilisation des bons composants
- **Git** — Historique propre et messages clairs

---

## 📦 Livrable

- Lien vers le fork GitHub
- `NOTES.md` — Liste des choix techniques et optimisations effectuées
