# Composants UI - Digitalfy

Bibliothèque de composants réutilisables basée sur le design system

---

## 🔘 Buttons

### Variantes

```html
<!-- Primary (Orange) -->
<button class="btn btn-primary">Contactez-moi</button>

<!-- Secondary (Noir) -->
<button class="btn btn-secondary">En savoir plus</button>

<!-- Outline -->
<button class="btn btn-outline">Voir les projets</button>

<!-- Ghost -->
<button class="btn btn-ghost">Lire la suite</button>
```

### SCSS

```scss
.btn {
    display: inline-flex;
    align-items: center;
    gap: $spacing-2;
    padding: $spacing-3 $spacing-6;
    font-size: $text-base;
    font-weight: $font-semibold;
    border-radius: $radius-lg;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all $transition-base;
    text-decoration: none;
    
    &-primary {
        background: $primary-orange;
        color: $white;
        
        &:hover {
            background: $primary-orange-dark;
            transform: translateY(-2px);
            box-shadow: $shadow-lg;
        }
    }
    
    &-secondary {
        background: $primary-black;
        color: $white;
        
        &:hover {
            background: $primary-gray;
        }
    }
    
    &-outline {
        background: transparent;
        border-color: $primary-orange;
        color: $primary-orange;
        
        &:hover {
            background: $primary-orange;
            color: $white;
        }
    }
    
    &-ghost {
        background: transparent;
        color: $primary-orange;
        
        &:hover {
            background: rgba($primary-orange, 0.1);
        }
    }
    
    // Tailles
    &-sm {
        padding: $spacing-2 $spacing-4;
        font-size: $text-sm;
    }
    
    &-lg {
        padding: $spacing-4 $spacing-8;
        font-size: $text-lg;
    }
}
```

---

## 🃏 Cards

### Card service

```html
<div class="card card-service">
    <div class="card-icon">
        <i class="icon-mobile"></i>
    </div>
    <h3 class="card-title">Applications mobiles</h3>
    <p class="card-description">
        Développement d'applications iOS et Android avec React Native
    </p>
    <a href="/services/mobile" class="card-link">
        En savoir plus →
    </a>
</div>
```

### Card blog

```html
<article class="card card-blog">
    <img src="image.jpg" alt="Titre" class="card-image">
    <div class="card-content">
        <span class="card-category">SEO Local</span>
        <h3 class="card-title">Titre de l'article</h3>
        <p class="card-excerpt">Extrait de l'article...</p>
        <div class="card-meta">
            <span class="card-date">15 janvier 2025</span>
        </div>
    </div>
</article>
```

### SCSS

```scss
.card {
    background: $white;
    border-radius: $radius-xl;
    overflow: hidden;
    box-shadow: $shadow-md;
    transition: all $transition-base;
    
    &:hover {
        box-shadow: $shadow-xl;
        transform: translateY(-4px);
    }
    
    &-service {
        padding: $spacing-8;
        text-align: center;
    }
    
    &-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto $spacing-4;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba($primary-orange, 0.1);
        border-radius: $radius-lg;
        color: $primary-orange;
        font-size: $text-3xl;
    }
    
    &-title {
        font-size: $text-xl;
        font-weight: $font-semibold;
        margin-bottom: $spacing-3;
    }
    
    &-description {
        color: $gray-600;
        margin-bottom: $spacing-4;
    }
    
    &-link {
        color: $primary-orange;
        font-weight: $font-medium;
        text-decoration: none;
        
        &:hover {
            text-decoration: underline;
        }
    }
    
    &-image {
        width: 100%;
        aspect-ratio: 16 / 9;
        object-fit: cover;
    }
    
    &-content {
        padding: $spacing-6;
    }
    
    &-category {
        display: inline-block;
        padding: $spacing-1 $spacing-3;
        background: rgba($primary-orange, 0.1);
        color: $primary-orange;
        font-size: $text-sm;
        font-weight: $font-medium;
        border-radius: $radius-md;
        margin-bottom: $spacing-3;
    }
    
    &-excerpt {
        color: $gray-600;
        margin: $spacing-3 0;
    }
    
    &-meta {
        display: flex;
        align-items: center;
        gap: $spacing-4;
        font-size: $text-sm;
        color: $gray-500;
    }
}
```

---

## 📊 Stats

```html
<div class="stats-grid">
    <div class="stat-item">
        <div class="stat-value">3000+</div>
        <div class="stat-label">Heures de code</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">15+</div>
        <div class="stat-label">Projets réalisés</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">100%</div>
        <div class="stat-label">Clients satisfaits</div>
    </div>
</div>
```

### SCSS

```scss
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: $spacing-8;
    text-align: center;
}

.stat {
    &-value {
        font-size: $text-5xl;
        font-weight: $font-extrabold;
        color: $primary-orange;
        line-height: 1;
        margin-bottom: $spacing-2;
        
        @include md {
            font-size: $text-6xl;
        }
    }
    
    &-label {
        font-size: $text-base;
        color: $gray-700;
        font-weight: $font-medium;
    }
}
```

---

## ❓ FAQ Accordion

```html
<div class="faq-section">
    <h2 class="faq-title">Questions fréquentes</h2>
    
    <div class="faq-list">
        <div class="faq-item" data-faq-item>
            <button class="faq-question" aria-expanded="false">
                <span>Question 1 ?</span>
                <i class="faq-icon">+</i>
            </button>
            <div class="faq-answer">
                <p>Réponse...</p>
            </div>
        </div>
    </div>
</div>
```

### SCSS

```scss
.faq {
    &-section {
        max-width: 800px;
        margin: 0 auto;
    }
    
    &-title {
        text-align: center;
        margin-bottom: $spacing-12;
    }
    
    &-list {
        display: flex;
        flex-direction: column;
        gap: $spacing-4;
    }
    
    &-item {
        background: $white;
        border-radius: $radius-lg;
        overflow: hidden;
        box-shadow: $shadow-sm;
        
        &.active {
            .faq-question {
                background: $primary-orange;
                color: $white;
            }
            
            .faq-answer {
                max-height: 500px;
                padding: $spacing-6;
            }
            
            .faq-icon {
                transform: rotate(45deg);
            }
        }
    }
    
    &-question {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: $spacing-6;
        background: transparent;
        border: none;
        text-align: left;
        font-size: $text-lg;
        font-weight: $font-semibold;
        cursor: pointer;
        transition: all $transition-base;
        
        &:hover {
            background: rgba($primary-orange, 0.05);
        }
    }
    
    &-icon {
        font-size: $text-2xl;
        font-weight: $font-normal;
        transition: transform $transition-base;
        font-style: normal;
    }
    
    &-answer {
        max-height: 0;
        overflow: hidden;
        transition: all $transition-slow;
        padding: 0 $spacing-6;
        
        p {
            margin: 0;
            color: $gray-700;
            line-height: $leading-relaxed;
        }
    }
}
```

### JavaScript

```javascript
// assets/app.js
document.querySelectorAll('[data-faq-item]').forEach(item => {
    const button = item.querySelector('.faq-question');
    
    button.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        
        // Fermer tous les items
        document.querySelectorAll('[data-faq-item]').forEach(i => {
            i.classList.remove('active');
            i.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
        });
        
        // Ouvrir l'item cliqué si il était fermé
        if (!isActive) {
            item.classList.add('active');
            button.setAttribute('aria-expanded', 'true');
        }
    });
});
```

---

## 🧭 Navigation

Voir [templates/partials/_navigation.html.twig] pour le template complet

### SCSS

```scss
.navbar {
    background: $white;
    box-shadow: $shadow-sm;
    position: sticky;
    top: 0;
    z-index: 1000;
    
    &-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: $spacing-4 0;
    }
    
    &-logo img {
        height: 40px;
    }
    
    &-menu {
        display: none;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: $spacing-8;
        
        @include lg {
            display: flex;
        }
        
        a {
            color: $gray-700;
            text-decoration: none;
            font-weight: $font-medium;
            transition: color $transition-base;
            
            &:hover {
                color: $primary-orange;
            }
        }
    }
    
    &-burger {
        display: flex;
        flex-direction: column;
        gap: 4px;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: $spacing-2;
        
        @include lg {
            display: none;
        }
        
        span {
            display: block;
            width: 24px;
            height: 2px;
            background: $primary-black;
            transition: all $transition-base;
        }
    }
}
```

---

*Document généré le 2025-11-18*
