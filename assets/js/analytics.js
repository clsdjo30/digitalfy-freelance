/**
 * Google Analytics - Événements personnalisés
 *
 * Ce fichier contient les fonctions pour tracker les événements personnalisés
 * avec Google Analytics GA4.
 */

// Vérifier si gtag est disponible
function isGtagAvailable() {
    return typeof window.gtag === 'function';
}

/**
 * Tracker la soumission du formulaire de contact
 * @param {string} projectType - Type de projet
 * @param {string} budget - Budget estimé
 */
export function trackContactFormSubmit(projectType, budget) {
    if (!isGtagAvailable()) return;

    window.gtag('event', 'contact_form_submit', {
        'project_type': projectType,
        'budget': budget,
        'event_category': 'engagement',
        'event_label': 'Contact Form'
    });
}

/**
 * Tracker le clic sur un CTA
 * @param {string} ctaLocation - Emplacement du CTA (hero, footer, etc.)
 * @param {string} ctaText - Texte du CTA
 */
export function trackCtaClick(ctaLocation, ctaText) {
    if (!isGtagAvailable()) return;

    window.gtag('event', 'cta_click', {
        'cta_location': ctaLocation,
        'cta_text': ctaText,
        'event_category': 'engagement',
        'event_label': ctaText
    });
}

/**
 * Tracker la lecture d'un article de blog
 * @param {string} articleTitle - Titre de l'article
 * @param {string} category - Catégorie de l'article
 */
export function trackArticleRead(articleTitle, category) {
    if (!isGtagAvailable()) return;

    window.gtag('event', 'article_read', {
        'article_title': articleTitle,
        'category': category,
        'event_category': 'engagement',
        'event_label': articleTitle
    });
}

/**
 * Tracker la consultation d'un projet
 * @param {string} projectTitle - Titre du projet
 */
export function trackProjectView(projectTitle) {
    if (!isGtagAvailable()) return;

    window.gtag('event', 'project_view', {
        'project_title': projectTitle,
        'event_category': 'engagement',
        'event_label': projectTitle
    });
}

/**
 * Tracker le clic sur un lien externe
 * @param {string} url - URL du lien
 * @param {string} text - Texte du lien
 */
export function trackOutboundLink(url, text) {
    if (!isGtagAvailable()) return;

    window.gtag('event', 'click', {
        'event_category': 'outbound',
        'event_label': url,
        'transport_type': 'beacon',
        'link_text': text
    });
}

/**
 * Tracker le scroll de la page
 * @param {number} percentage - Pourcentage de scroll (25, 50, 75, 100)
 */
export function trackScrollDepth(percentage) {
    if (!isGtagAvailable()) return;

    window.gtag('event', 'scroll', {
        'event_category': 'engagement',
        'event_label': `${percentage}%`,
        'value': percentage
    });
}

// Auto-tracking du scroll (25%, 50%, 75%, 100%)
let scrollDepthTracked = {
    25: false,
    50: false,
    75: false,
    100: false
};

window.addEventListener('scroll', () => {
    const scrollPercentage = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;

    Object.keys(scrollDepthTracked).forEach(threshold => {
        if (scrollPercentage >= threshold && !scrollDepthTracked[threshold]) {
            scrollDepthTracked[threshold] = true;
            trackScrollDepth(threshold);
        }
    });
});

// Auto-tracking des clics sur les CTA
document.addEventListener('DOMContentLoaded', () => {
    // Tracker les clics sur les boutons CTA
    const ctaButtons = document.querySelectorAll('.btn, [data-track-cta]');
    ctaButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const location = button.dataset.ctaLocation || 'unknown';
            const text = button.textContent.trim() || button.value || 'CTA Click';
            trackCtaClick(location, text);
        });
    });

    // Tracker les liens externes
    const externalLinks = document.querySelectorAll('a[href^="http"]:not([href*="digitalfy.fr"])');
    externalLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            trackOutboundLink(link.href, link.textContent.trim());
        });
    });
});
