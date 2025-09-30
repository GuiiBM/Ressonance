# 🖼️ Sistema de Imagens - Ressonance

## 📋 Visão Geral

O sistema de imagens do Ressonance gerencia todas as imagens do site: capas de álbuns, fotos de artistas, avatars de usuários e imagens do sistema. Inclui otimização automática, cache e correção de caminhos.

## 🎯 Funcionalidades Principais

### 🖼️ **Tipos de Imagens**
- **Capas de Álbuns** - Artwork dos álbuns
- **Fotos de Artistas** - Imagens dos músicos
- **Avatars** - Fotos de perfil dos usuários
- **Logos** - Logotipos e marcas
- **Placeholders** - Imagens padrão

### 🔄 **Processamento Automático**
- Redimensionamento inteligente
- Otimização de qualidade
- Conversão de formatos
- Geração de thumbnails
- Cache automático

### 🌐 **Entrega Otimizada**
- Servidor de imagens dedicado
- Headers HTTP otimizados
- Cache de longa duração
- Compressão automática

## 🏗️ Arquitetura do Sistema

### **Componentes Principais**

```
Sistema de Imagens/
├── image.php              # Servidor de imagens
├── image-helper.php       # Funções auxiliares
├── images/               # Diretório de imagens
├── public/assets/images/ # Imagens públicas
└── storage/uploads/      # Uploads de usuários
```

### **Fluxo de Processamento**

```
[Upload] → [Validação] → [Processamento] → [Otimização] → [Cache] → [Entrega]
    ↓          ↓             ↓              ↓           ↓        ↓
[Arquivo] → [Formato] → [Redimensionar] → [Comprimir] → [Armazenar] → [Usuário]
```

## 🔧 Configuração

### **Formatos Suportados**
```php
// Formatos de entrada
$allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

// Formatos de saída
$outputFormats = ['jpg', 'png', 'webp'];
```

### **Limites e Qualidade**
```php
// Configurações de upload
define('MAX_IMAGE_SIZE', 10 * 1024 * 1024); // 10MB
define('MAX_IMAGE_WIDTH', 2048);
define('MAX_IMAGE_HEIGHT', 2048);
define('JPEG_QUALITY', 85);
define('PNG_COMPRESSION', 6);
```

## 🖼️ Como Funciona

### **1. Servidor de Imagens (image.php)**
```php
// Receber requisição
$filename = $_GET['f'] ?? '';
$size = $_GET['s'] ?? 'original';

// Validar arquivo
if (!file_exists("images/$filename")) {
    header('Location: placeholder.jpg');
    exit;
}

// Definir headers
header('Content-Type: ' . mime_content_type($file));
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

// Entregar arquivo
readfile($file);
```

### **2. Processamento de Imagens**
```php
// Redimensionar imagem
function resizeImage($source, $width, $height, $quality = 85) {
    $info = getimagesize($source);
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
    }
    
    $newImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
    
    return $newImage;
}
```

### **3. Helper de Imagens**
```php
// Gerar URL de imagem
function getImageUrl($imagePath, $size = null) {
    if (empty($imagePath)) {
        return getPlaceholderUrl();
    }
    
    $url = BASE_URL . '/image.php?f=' . urlencode(basename($imagePath));
    if ($size) {
        $url .= '&s=' . $size;
    }
    
    return $url;
}

// Gerar tag IMG
function fixImageTag($imagePath, $alt = '', $class = '', $size = null) {
    $src = getImageUrl($imagePath, $size);
    $placeholder = getPlaceholderUrl();
    
    return "<img src=\"$src\" alt=\"$alt\" class=\"$class\" onerror=\"this.src='$placeholder'\">";
}
```

## 📐 Tamanhos e Dimensões

### **Tamanhos Padrão**

| Tipo | Dimensões | Uso |
|------|-----------|-----|
| **thumbnail** | 150x150 | Listas e grids |
| **small** | 300x300 | Cards pequenos |
| **medium** | 500x500 | Visualização normal |
| **large** | 800x800 | Visualização ampliada |
| **original** | Variável | Qualidade máxima |

### **Geração Automática**
```php
// Gerar thumbnails automaticamente
function generateThumbnails($originalPath) {
    $sizes = [
        'thumbnail' => [150, 150],
        'small' => [300, 300],
        'medium' => [500, 500],
        'large' => [800, 800]
    ];
    
    foreach ($sizes as $name => $dimensions) {
        $outputPath = "cache/{$name}_" . basename($originalPath);
        resizeImage($originalPath, $dimensions[0], $dimensions[1], 85);
        saveImage($outputPath);
    }
}
```

## 🎨 Placeholders Inteligentes

### **Tipos de Placeholder**

```php
// Placeholders por categoria
function getPlaceholderUrl($type = 'default') {
    $placeholders = [
        'album' => 'https://via.placeholder.com/300x300/8a2be2/ffffff?text=♪',
        'artist' => 'https://via.placeholder.com/300x300/4a90e2/ffffff?text=👤',
        'user' => 'https://via.placeholder.com/150x150/50c878/ffffff?text=👤',
        'default' => 'https://via.placeholder.com/300x300/cccccc/666666?text=?'
    ];
    
    return $placeholders[$type] ?? $placeholders['default'];
}
```

### **Placeholder Dinâmico**
```php
// Gerar placeholder com texto personalizado
function generatePlaceholder($width, $height, $text, $bgColor = '8a2be2', $textColor = 'ffffff') {
    return "https://via.placeholder.com/{$width}x{$height}/{$bgColor}/{$textColor}?text=" . urlencode($text);
}
```

## 🗂️ Organização de Arquivos

### **Estrutura de Diretórios**
```
images/
├── albums/           # Capas de álbuns
│   ├── album_1.jpg
│   └── album_2.png
├── artists/          # Fotos de artistas
│   ├── artist_1.jpg
│   └── artist_2.png
├── users/           # Avatars de usuários
│   ├── user_1.jpg
│   └── user_2.png
├── system/          # Imagens do sistema
│   ├── logo.png
│   └── favicon.ico
└── cache/           # Cache de thumbnails
    ├── thumb_album_1.jpg
    └── small_artist_1.jpg
```

### **Nomenclatura**
```php
// Padrão de nomes
function generateImageName($type, $id, $originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $timestamp = time();
    return "{$type}_{$id}_{$timestamp}.{$extension}";
}

// Exemplo: album_15_1640995200.jpg
```

## 🔄 Sistema de Cache

### **Cache de Thumbnails**
```php
// Verificar cache existente
function getCachedThumbnail($imagePath, $size) {
    $cacheFile = "cache/{$size}_" . basename($imagePath);
    
    if (file_exists($cacheFile)) {
        $originalTime = filemtime($imagePath);
        $cacheTime = filemtime($cacheFile);
        
        if ($cacheTime > $originalTime) {
            return $cacheFile;
        }
    }
    
    return generateThumbnail($imagePath, $size);
}
```

### **Limpeza de Cache**
```php
// Limpar cache antigo
function cleanImageCache($maxAge = 2592000) { // 30 dias
    $cacheDir = 'cache/';
    $files = glob($cacheDir . '*');
    
    foreach ($files as $file) {
        if (time() - filemtime($file) > $maxAge) {
            unlink($file);
        }
    }
}
```

## 🚀 Otimizações

### **Compressão Inteligente**
```php
// Otimizar JPEG
function optimizeJpeg($source, $destination, $quality = 85) {
    $image = imagecreatefromjpeg($source);
    
    // Remover metadados EXIF
    $cleanImage = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagecopy($cleanImage, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    
    imagejpeg($cleanImage, $destination, $quality);
    imagedestroy($image);
    imagedestroy($cleanImage);
}

// Otimizar PNG
function optimizePng($source, $destination, $compression = 6) {
    $image = imagecreatefrompng($source);
    imagesavealpha($image, true);
    imagepng($image, $destination, $compression);
    imagedestroy($image);
}
```

### **WebP Support**
```php
// Converter para WebP se suportado
function serveOptimalFormat($imagePath) {
    $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
    
    if (strpos($acceptHeader, 'image/webp') !== false) {
        $webpPath = convertToWebP($imagePath);
        if ($webpPath) {
            header('Content-Type: image/webp');
            readfile($webpPath);
            return;
        }
    }
    
    // Fallback para formato original
    header('Content-Type: ' . mime_content_type($imagePath));
    readfile($imagePath);
}
```

## 📱 Responsividade

### **Imagens Responsivas**
```html
<!-- Diferentes tamanhos para diferentes telas -->
<picture>
    <source media="(max-width: 480px)" srcset="image.php?f=album.jpg&s=small">
    <source media="(max-width: 768px)" srcset="image.php?f=album.jpg&s=medium">
    <img src="image.php?f=album.jpg&s=large" alt="Album Cover">
</picture>
```

### **Lazy Loading**
```javascript
// Carregamento sob demanda
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
        }
    });
});

document.querySelectorAll('img[data-src]').forEach(img => {
    imageObserver.observe(img);
});
```

## 🔧 Upload de Imagens

### **Validação de Upload**
```php
function validateImageUpload($file) {
    $errors = [];
    
    // Verificar tipo
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        $errors[] = 'Formato não suportado';
    }
    
    // Verificar tamanho
    if ($file['size'] > MAX_IMAGE_SIZE) {
        $errors[] = 'Arquivo muito grande';
    }
    
    // Verificar dimensões
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo[0] > MAX_IMAGE_WIDTH || $imageInfo[1] > MAX_IMAGE_HEIGHT) {
        $errors[] = 'Dimensões muito grandes';
    }
    
    return $errors;
}
```

### **Processamento de Upload**
```php
function processImageUpload($file, $type, $id) {
    $errors = validateImageUpload($file);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    $filename = generateImageName($type, $id, $file['name']);
    $destination = "images/{$type}s/{$filename}";
    
    // Mover arquivo
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Gerar thumbnails
        generateThumbnails($destination);
        
        // Otimizar original
        optimizeImage($destination);
        
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'errors' => ['Erro no upload']];
}
```

## 🔍 Troubleshooting

### **Problemas Comuns**

#### **Imagem não carrega**
```bash
# Verificar arquivo
ls -la images/album_1.jpg

# Verificar permissões
chmod 644 images/album_1.jpg

# Testar diretamente
curl -I http://localhost/Ressonance/image.php?f=album_1.jpg
```

#### **Qualidade ruim**
```php
// Ajustar qualidade JPEG
define('JPEG_QUALITY', 90); // Aumentar de 85 para 90

// Verificar redimensionamento
$info = getimagesize($imagePath);
echo "Dimensões: {$info[0]}x{$info[1]}";
```

#### **Cache não funciona**
```php
// Verificar headers
$headers = get_headers($imageUrl, 1);
print_r($headers);

// Limpar cache manualmente
unlink('cache/thumb_album_1.jpg');
```

## 📊 Monitoramento

### **Métricas de Imagens**
```php
// Estatísticas de uso
function getImageStats() {
    return [
        'total_images' => count(glob('images/*/*.{jpg,png,gif}', GLOB_BRACE)),
        'cache_size' => array_sum(array_map('filesize', glob('cache/*'))),
        'most_accessed' => getMostAccessedImages(),
        'storage_used' => disk_free_space('images/')
    ];
}
```

### **Logs de Acesso**
```php
// Log de requisições de imagem
function logImageAccess($filename, $size, $userAgent) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'filename' => $filename,
        'size' => $size,
        'user_agent' => $userAgent,
        'ip' => $_SERVER['REMOTE_ADDR']
    ];
    
    file_put_contents('logs/image_access.log', json_encode($logEntry) . "\n", FILE_APPEND);
}
```

## 🔮 Funcionalidades Futuras

- **CDN Integration** - Distribuição global
- **AI Image Enhancement** - Melhoria automática
- **Automatic Tagging** - Tags automáticas
- **Duplicate Detection** - Detecção de duplicatas
- **Batch Processing** - Processamento em lote
- **Advanced Filters** - Filtros e efeitos

---

**📚 Próximos Passos:**
- [Servidor de Imagens](01-servidor-imagens.md) - Configuração detalhada
- [Sistema de Áudio](../04-sistema-audio/) - Integração com capas
- [Interface](../07-interface/) - Componentes visuais