# Phase 6 : Design & Intégration

**Durée** : 7 jours (Semaines 4-5)
**Objectif** : Intégrer le design moderne orange/noir avec illustrations 3D

---

## 📋 Vue d'ensemble

Cette phase transforme les pages fonctionnelles en site moderne et attractif basé sur l'image de référence `docs/design-reference.png`.

**Design System** complet : Voir [../design/design-system.md](../design/design-system.md)  
**Composants UI** : Voir [../design/composants-ui.md](../design/composants-ui.md)

---

## 6.1 Design System

### Palette de couleurs

```scss
$primary-orange: #FF5722;
$primary-black: #1A1A1A;
$secondary-cream: #FFF5F0;
$white: #FFFFFF;
```

### Typographie

- **Font principale** : Inter / Poppins
- **Titres** : Bold, mix noir/orange
- **Tailles** : 16px base, 48-60px pour H1

### Checklist Design System

- [ ] Créer `assets/styles/_variables.scss`
- [ ] Définir palette couleurs
- [ ] Définir échelle typographique
- [ ] Créer mixins SCSS
- [ ] Documenter dans design-system.md

---

## 6.2 Composants réutilisables

### À créer

- [ ] `_button.scss` - 4 variantes (primary, secondary, outline, ghost)
- [ ] `_card.scss` - Cards services/projets/blog
- [ ] `_stats.scss` - Chiffres + labels
- [ ] `_faq.scss` - Accordion expandable
- [ ] `_navigation.scss` - Menu desktop/mobile
- [ ] `_footer.scss` - Footer multi-colonnes

Templates Twig :
- [ ] `components/_button.html.twig`
- [ ] `components/_card.html.twig`
- [ ] `components/_stats.html.twig`
- [ ] `components/_faq.html.twig`

---

## 6.3 Illustrations & Assets

### Visuels 3D abstraits

- [ ] Hero visual principal (orange/rouge dégradé)
- [ ] 3-4 visuels pour sections alternées
- [ ] Optimisation WebP (<200KB chacun)

**Sources possibles** :
- Spline.design
- Vectary
- Blender
- Assets pré-faits (Craftwork Design)

### Icônes

- [ ] Set icônes services (SVG)
- [ ] Icônes UI (flèches, +/-, etc.)
- [ ] Icônes réseaux sociaux

---

## 6.4 Layout & Structure

### Grid System

```scss
.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    
    @media (min-width: 768px) {
        padding: 0 48px;
    }
}

.grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
```

### Sections

```scss
.section {
    padding: 64px 0;
    
    @media (min-width: 768px) {
        padding: 96px 0;
    }
}
```

### Checklist Layout

- [ ] Container responsive
- [ ] Grid system 12 colonnes
- [ ] Sections padding cohérent
- [ ] Breakpoints : 640px, 768px, 1024px, 1280px

---

## 6.5 Responsive Design

### Mobile-First Approach

```scss
// Mobile par défaut
.hero-title {
    font-size: 36px;
}

// Tablette
@media (min-width: 768px) {
    .hero-title {
        font-size: 48px;
    }
}

// Desktop
@media (min-width: 1024px) {
    .hero-title {
        font-size: 60px;
    }
}
```

### Checklist Responsive

- [ ] Navigation hamburger mobile
- [ ] Grilles adaptatives (1→2→3 colonnes)
- [ ] Images responsive avec srcset
- [ ] Touch-friendly (boutons 44px min)
- [ ] Tests iPhone/Android

---

## 6.6 Animations & Interactions

### Hover Effects

```scss
.btn {
    transition: all 0.3s ease;
    
    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }
}
```

### Checklist Animations

- [ ] Hover effects sur cards
- [ ] Transitions boutons
- [ ] Accordion FAQ (JavaScript)
- [ ] Smooth scroll
- [ ] Loading states

---

## 6.7 Webpack Encore Configuration

```javascript
// webpack.config.js
Encore
    .addEntry('app', './assets/app.js')
    .enableSassLoader()
    .enablePostCssLoader((options) => {
        options.postcssOptions = {
            plugins: [
                require('autoprefixer'),
            ],
        };
    })
    .enableVersioning(Encore.isProduction())
```

### Checklist Webpack

- [ ] Configuration SCSS
- [ ] Autoprefixer pour compatibilité
- [ ] Minification production
- [ ] Source maps développement

---

## ✅ Checklist finale Phase 6

### Design System
- [ ] Variables SCSS créées
- [ ] Palette couleurs documentée
- [ ] Typographie définie
- [ ] Mixins créés

### Composants
- [ ] Tous les composants UI créés
- [ ] Templates Twig réutilisables
- [ ] Styles SCSS modulaires

### Assets
- [ ] Illustrations 3D intégrées
- [ ] Icônes SVG
- [ ] Images optimisées WebP
- [ ] Favicon + touch icons

### Responsive
- [ ] Mobile-first OK
- [ ] Tablette OK
- [ ] Desktop OK
- [ ] Menu mobile fonctionnel

### Performance
- [ ] Assets compilés
- [ ] CSS minifié
- [ ] Images lazy loading

---

## 🚀 Prochaine étape

Passer à la [Phase 7 : SEO Technique](07-seo-technique.md)

---

*Document généré le 2025-11-18*
