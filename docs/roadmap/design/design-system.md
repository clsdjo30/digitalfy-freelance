# Design System - Digitalfy

Basé sur l'image de référence `docs/design-reference.png`

---

## 🎨 Palette de couleurs

### Couleurs principales

```scss
// Couleurs primaires
$primary-orange: #FF5722;
$primary-orange-dark: #E64A19;
$primary-orange-light: #FF7043;

$primary-black: #1A1A1A;
$primary-gray: #333333;

// Couleurs secondaires
$secondary-cream: #FFF5F0;
$secondary-beige: #FFF8F0;

// Couleurs de base
$white: #FFFFFF;
$gray-50: #F9FAFB;
$gray-100: #F3F4F6;
$gray-200: #E5E7EB;
$gray-300: #D1D5DB;
$gray-400: #9CA3AF;
$gray-500: #6B7280;
$gray-600: #4B5563;
$gray-700: #374151;
$gray-800: #1F2937;
$gray-900: #111827;

// Dégradés
$gradient-red-orange: linear-gradient(135deg, #FF3D00 0%, #FF6B35 100%);
$gradient-orange: linear-gradient(135deg, #FF5722 0%, #FF7043 100%);
```

### Utilisation

- **Orange** : CTAs, liens, éléments interactifs, mots-clés dans titres
- **Noir** : Textes principaux, titres
- **Crème** : Fond sections alternées
- **Blanc** : Fond principal, cards

---

## ✍️ Typographie

### Familles de polices

```scss
// Import Google Fonts
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap');

$font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
$font-display: 'Poppins', 'Inter', sans-serif;
```

### Scale typographique

```scss
// Tailles
$text-xs: 0.75rem;      // 12px
$text-sm: 0.875rem;     // 14px
$text-base: 1rem;       // 16px
$text-lg: 1.125rem;     // 18px
$text-xl: 1.25rem;      // 20px
$text-2xl: 1.5rem;      // 24px
$text-3xl: 1.875rem;    // 30px
$text-4xl: 2.25rem;     // 36px
$text-5xl: 3rem;        // 48px
$text-6xl: 3.75rem;     // 60px
$text-7xl: 4.5rem;      // 72px

// Poids
$font-normal: 400;
$font-medium: 500;
$font-semibold: 600;
$font-bold: 700;
$font-extrabold: 800;

// Line heights
$leading-none: 1;
$leading-tight: 1.25;
$leading-snug: 1.375;
$leading-normal: 1.5;
$leading-relaxed: 1.625;
$leading-loose: 2;
```

### Styles de titres

```scss
h1, .h1 {
    font-family: $font-display;
    font-size: $text-4xl;
    font-weight: $font-bold;
    line-height: $leading-tight;
    color: $primary-black;
    
    @media (min-width: 768px) {
        font-size: $text-5xl;
    }
    
    @media (min-width: 1024px) {
        font-size: $text-6xl;
    }
}

h2, .h2 {
    font-family: $font-display;
    font-size: $text-3xl;
    font-weight: $font-bold;
    line-height: $leading-tight;
    
    @media (min-width: 768px) {
        font-size: $text-4xl;
    }
    
    @media (min-width: 1024px) {
        font-size: $text-5xl;
    }
}

h3, .h3 {
    font-family: $font-display;
    font-size: $text-2xl;
    font-weight: $font-semibold;
    line-height: $leading-snug;
    
    @media (min-width: 768px) {
        font-size: $text-3xl;
    }
}

body {
    font-family: $font-primary;
    font-size: $text-base;
    font-weight: $font-normal;
    line-height: $leading-relaxed;
    color: $gray-700;
}
```

### Emphasis orange dans titres

```scss
.text-orange {
    color: $primary-orange;
}

// Usage: <h1>Développeur <span class="text-orange">freelance</span> à Nîmes</h1>
```

---

## 📏 Spacing

### Échelle de spacing

```scss
$spacing-0: 0;
$spacing-1: 0.25rem;   // 4px
$spacing-2: 0.5rem;    // 8px
$spacing-3: 0.75rem;   // 12px
$spacing-4: 1rem;      // 16px
$spacing-5: 1.25rem;   // 20px
$spacing-6: 1.5rem;    // 24px
$spacing-8: 2rem;      // 32px
$spacing-10: 2.5rem;   // 40px
$spacing-12: 3rem;     // 48px
$spacing-16: 4rem;     // 64px
$spacing-20: 5rem;     // 80px
$spacing-24: 6rem;     // 96px
$spacing-32: 8rem;     // 128px
```

### Sections padding

```scss
.section {
    padding: $spacing-16 0;
    
    @media (min-width: 768px) {
        padding: $spacing-20 0;
    }
    
    @media (min-width: 1024px) {
        padding: $spacing-24 0;
    }
}
```

---

## 📐 Layout & Grid

### Container

```scss
.container {
    width: 100%;
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
    padding-left: $spacing-6;
    padding-right: $spacing-6;
    
    @media (min-width: 768px) {
        padding-left: $spacing-12;
        padding-right: $spacing-12;
    }
}
```

### Grid System

```scss
.grid {
    display: grid;
    gap: $spacing-6;
    
    @media (min-width: 768px) {
        gap: $spacing-8;
    }
}

.grid-2 {
    @extend .grid;
    grid-template-columns: repeat(1, 1fr);
    
    @media (min-width: 768px) {
        grid-template-columns: repeat(2, 1fr);
    }
}

.grid-3 {
    @extend .grid;
    grid-template-columns: repeat(1, 1fr);
    
    @media (min-width: 768px) {
        grid-template-columns: repeat(2, 1fr);
    }
    
    @media (min-width: 1024px) {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

---

## 🎭 Ombres

```scss
$shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
$shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
$shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
$shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
$shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

// Usage
.card {
    box-shadow: $shadow-md;
    
    &:hover {
        box-shadow: $shadow-lg;
    }
}
```

---

## 🔘 Border Radius

```scss
$radius-sm: 0.375rem;   // 6px
$radius-md: 0.5rem;     // 8px
$radius-lg: 0.75rem;    // 12px
$radius-xl: 1rem;       // 16px
$radius-2xl: 1.5rem;    // 24px
$radius-full: 9999px;   // Complètement arrondi

// Usage
.card {
    border-radius: $radius-xl;
}

.btn {
    border-radius: $radius-lg;
}
```

---

## 🎬 Transitions

```scss
$transition-fast: 150ms ease;
$transition-base: 200ms ease;
$transition-slow: 300ms ease;

// Usage
.btn {
    transition: all $transition-base;
    
    &:hover {
        transform: translateY(-2px);
    }
}
```

---

## 📱 Breakpoints

```scss
$breakpoint-sm: 640px;   // Mobile large
$breakpoint-md: 768px;   // Tablette
$breakpoint-lg: 1024px;  // Desktop
$breakpoint-xl: 1280px;  // Large desktop
$breakpoint-2xl: 1536px; // Extra large

// Mixins
@mixin sm {
    @media (min-width: $breakpoint-sm) {
        @content;
    }
}

@mixin md {
    @media (min-width: $breakpoint-md) {
        @content;
    }
}

@mixin lg {
    @media (min-width: $breakpoint-lg) {
        @content;
    }
}

// Usage
.hero-title {
    font-size: $text-4xl;
    
    @include md {
        font-size: $text-5xl;
    }
    
    @include lg {
        font-size: $text-6xl;
    }
}
```

---

## 📋 Exemples d'utilisation

### Hero Section

```scss
.hero {
    background: $white;
    padding: $spacing-16 0;
    
    @include lg {
        padding: $spacing-24 0;
    }
    
    &-title {
        font-size: $text-4xl;
        font-weight: $font-bold;
        line-height: $leading-tight;
        margin-bottom: $spacing-6;
        
        @include lg {
            font-size: $text-6xl;
        }
        
        .highlight {
            color: $primary-orange;
        }
    }
}
```

### Card

```scss
.card {
    background: $white;
    border-radius: $radius-xl;
    padding: $spacing-8;
    box-shadow: $shadow-md;
    transition: all $transition-base;
    
    &:hover {
        box-shadow: $shadow-xl;
        transform: translateY(-4px);
    }
}
```

---

*Document généré le 2025-11-18*
