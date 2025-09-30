// Manipulador automático de imagens
window.ImageHandler = {
    // Corrigir URL de imagem
    getImageUrl: function(imagePath) {
        if (!imagePath || imagePath === 'NULL') {
            return 'https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA';
        }
        
        if (imagePath.startsWith('http') || imagePath.startsWith('data:')) {
            return imagePath;
        }
        
        if (imagePath.includes('image.php')) {
            return imagePath;
        }
        
        // Extrair apenas o nome do arquivo
        const fileName = imagePath.split('/').pop();
        return window.APP_CONFIG.BASE_URL + '/image.php?f=' + encodeURIComponent(fileName);
    },
    
    // Aplicar correção automática em todas as imagens da página
    fixAllImages: function() {
        const images = document.querySelectorAll('img[src]');
        images.forEach(img => {
            const originalSrc = img.getAttribute('src');
            const correctedSrc = this.getImageUrl(originalSrc);
            if (originalSrc !== correctedSrc) {
                img.src = correctedSrc;
            }
        });
    },
    
    // Observar novas imagens adicionadas dinamicamente
    observeNewImages: function() {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'IMG') {
                            const src = node.getAttribute('src');
                            if (src) {
                                node.src = this.getImageUrl(src);
                            }
                        }
                        
                        const images = node.querySelectorAll ? node.querySelectorAll('img[src]') : [];
                        images.forEach(img => {
                            const src = img.getAttribute('src');
                            if (src) {
                                img.src = this.getImageUrl(src);
                            }
                        });
                    }
                });
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    window.ImageHandler.fixAllImages();
    window.ImageHandler.observeNewImages();
});