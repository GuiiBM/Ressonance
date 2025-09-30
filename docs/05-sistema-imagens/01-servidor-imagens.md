# 🖼️ Sistema de Imagens - Correção Automática

## 🔍 O que é

O sistema de imagens do Ressonance é um **corretor automático** que garante que todas as imagens (capas de álbuns, fotos de artistas, etc.) sejam exibidas corretamente, não importa onde estejam armazenadas ou como os caminhos mudaram. É como ter um **GPS para imagens**!

## 📂 Onde está

### Arquivos Principais
- **📄 `image.php`** - Servidor de imagens (raiz do projeto)
- **📄 `app/views/components/image-helper.php`** - Funções PHP para imagens
- **📄 `public/assets/js/image-handler.js`** - Correção automática JavaScript
- **📁 `public/assets/images/`** - Pasta principal de imagens

### Estrutura de Imagens
```
📁 public/assets/images/
├── 🖼️ logo.png
├── 🎼 album_1759027774.jpg
├── 🎵 song_1759027417.jpg
├── 🎵 song_1759027600.jpg
└── 🎵 ... outras imagens

📁 storage/uploads/images/
├── 🖼️ uploads de usuários
└── 🖼️ ... outras imagens
```

## ⚙️ Como Funciona (Explicação Simples)

### 🎯 Problema que Resolve

**Cenário Comum**:
```
❌ Imagem quebrada: /old/path/album.jpg
❌ Caminho errado: http://localhost/wrong/image.jpg
❌ Arquivo movido: ../images/moved.jpg
```

**Com o Sistema**:
```
✅ Detecta automaticamente: "Esta imagem não funciona"
✅ Procura em vários locais: public/assets/images/, storage/uploads/, etc.
✅ Corrige automaticamente: image.php?f=album.jpg
✅ Mostra placeholder se não encontrar
```

### 🔄 Fluxo de Funcionamento

```
1. 🖼️ Página tenta carregar imagem: "/broken/path/album.jpg"
   ↓
2. 🔍 Sistema detecta: "Este caminho não funciona"
   ↓
3. 🧠 JavaScript corrige: "image.php?f=album.jpg"
   ↓
4. 📂 image.php procura em várias pastas:
   - public/assets/images/album.jpg ✅
   - storage/uploads/images/album.jpg
   - images/album.jpg
   ↓
5. 🎯 Encontrou! Serve a imagem correta
   ❌ Não encontrou? Mostra placeholder
```

## 🔧 Detalhes Técnicos

### 📄 image.php - O Servidor de Imagens

```php
<?php
// 🎯 Recebe o nome da imagem
$file = $_GET['f'] ?? '';  // Ex: "album_1759027774.jpg"

// 📂 Lista de locais onde procurar
$possiblePaths = [
    'public/assets/images/' . basename($file),     // Local principal
    'storage/uploads/images/' . basename($file),   // Uploads
    'images/' . basename($file)                    // Pasta antiga
];

// 🔍 Procura em cada local
$imagePath = '';
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $imagePath = $path;  // ✅ Encontrou!
        break;
    }
}

// ❌ Não encontrou em lugar nenhum
if (!$imagePath || !file_exists($imagePath)) {
    http_response_code(404);
    exit;
}

// 🔒 Verifica se é um formato de imagem válido
$extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
$allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($extension, $allowedFormats)) {
    http_response_code(403);  // 🚫 Formato não permitido
    exit;
}

// 📡 Configura headers corretos
$contentTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$contentType = $contentTypes[$extension] ?? 'image/jpeg';

// 🚀 Otimizações de performance
header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($imagePath));
header('Cache-Control: public, max-age=31536000');  // Cache por 1 ano

// 🖼️ Envia a imagem
readfile($imagePath);
?>
```

### 🧠 image-helper.php - Funções PHP

```php
<?php
// 🛠️ Corrige URL de imagem automaticamente
function getImageUrl($imagePath) {
    // 🚫 Imagem vazia ou NULL
    if (empty($imagePath) || $imagePath === 'NULL') {
        return 'https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA';
    }
    
    // ✅ URL completa (http/https)
    if (strpos($imagePath, 'http') === 0 || strpos($imagePath, 'data:') === 0) {
        return $imagePath;
    }
    
    // ✅ Já usa o sistema de imagens
    if (strpos($imagePath, 'image.php') !== false) {
        return $imagePath;
    }
    
    // 🔧 Corrige para usar o sistema
    $fileName = basename($imagePath);  // Pega só o nome do arquivo
    return BASE_URL . '/image.php?f=' . urlencode($fileName);
}

// 🏷️ Gera tag <img> completa com correção automática
function fixImageTag($imagePath, $alt = '', $class = '', $style = '') {
    $src = getImageUrl($imagePath);
    
    // 🏗️ Monta atributos
    $attributes = [];
    if ($alt) $attributes[] = 'alt="' . htmlspecialchars($alt) . '"';
    if ($class) $attributes[] = 'class="' . htmlspecialchars($class) . '"';
    if ($style) $attributes[] = 'style="' . htmlspecialchars($style) . '"';
    
    $attributeString = implode(' ', $attributes);
    
    // 🖼️ Retorna tag completa com fallback
    return '<img src="' . htmlspecialchars($src) . '" ' . $attributeString . ' onerror="this.src=\'https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA\'">';
}
?>
```

### ⚡ image-handler.js - Correção JavaScript

```javascript
// 🤖 Manipulador automático de imagens
window.ImageHandler = {
    // 🔧 Corrige URL de imagem
    getImageUrl: function(imagePath) {
        // 🚫 Imagem vazia
        if (!imagePath || imagePath === 'NULL') {
            return 'https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA';
        }
        
        // ✅ URL completa
        if (imagePath.startsWith('http') || imagePath.startsWith('data:')) {
            return imagePath;
        }
        
        // ✅ Já corrigida
        if (imagePath.includes('image.php')) {
            return imagePath;
        }
        
        // 🔧 Corrige automaticamente
        const fileName = imagePath.split('/').pop();  // Pega só o nome
        return window.APP_CONFIG.BASE_URL + '/image.php?f=' + encodeURIComponent(fileName);
    },
    
    // 🔄 Corrige todas as imagens da página
    fixAllImages: function() {
        const images = document.querySelectorAll('img[src]');
        images.forEach(img => {
            const originalSrc = img.getAttribute('src');
            const correctedSrc = this.getImageUrl(originalSrc);
            if (originalSrc !== correctedSrc) {
                img.src = correctedSrc;  // 🔧 Aplica correção
            }
        });
    },
    
    // 👀 Observa novas imagens adicionadas dinamicamente
    observeNewImages: function() {
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Elemento HTML
                        // 🖼️ Se é uma imagem
                        if (node.tagName === 'IMG') {
                            const src = node.getAttribute('src');
                            if (src) {
                                node.src = this.getImageUrl(src);
                            }
                        }
                        
                        // 🔍 Procura imagens dentro do elemento
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
        
        // 👀 Observa mudanças na página
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
};

// 🚀 Inicializa quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    window.ImageHandler.fixAllImages();      // Corrige imagens existentes
    window.ImageHandler.observeNewImages();  // Observa novas imagens
});
```

## 🗄️ Correção no Banco de Dados

### 🔧 Função de Correção Automática

```php
function fixImagePaths($projectPath) {
    try {
        // 🎤 Corrige imagens de artistas
        $stmt = $pdo->prepare("
            UPDATE artists 
            SET image = CONCAT(?, '/image.php?f=', SUBSTRING_INDEX(image, '/', -1)) 
            WHERE image IS NOT NULL 
            AND image != '' 
            AND image NOT LIKE '%image.php%'
        ");
        $stmt->execute([$projectPath]);
        
        // 💿 Corrige imagens de álbuns
        $stmt = $pdo->prepare("
            UPDATE albums 
            SET image = CONCAT(?, '/image.php?f=', SUBSTRING_INDEX(image, '/', -1)) 
            WHERE image IS NOT NULL 
            AND image != '' 
            AND image NOT LIKE '%image.php%'
        ");
        $stmt->execute([$projectPath]);
        
        // 🎵 Corrige imagens de músicas
        $stmt = $pdo->prepare("
            UPDATE songs 
            SET image = CONCAT(?, '/image.php?f=', SUBSTRING_INDEX(image, '/', -1)) 
            WHERE image IS NOT NULL 
            AND image != '' 
            AND image NOT LIKE '%image.php%'
        ");
        $stmt->execute([$projectPath]);
        
    } catch (Exception $e) {
        // 🤐 Ignora erros se banco não estiver configurado
    }
}
```

### 📊 Exemplo de Correção

**Antes** (caminhos quebrados):
```sql
-- Artistas
image = '/old/path/artist.jpg'
image = 'http://localhost/wrong/artist.jpg'
image = '../images/artist.jpg'

-- Álbuns  
image = '/broken/album.jpg'
image = 'public/assets/images/album.jpg'
```

**Depois** (caminhos corrigidos):
```sql
-- Artistas
image = '/Ressonance/Ressonance/image.php?f=artist.jpg'

-- Álbuns
image = '/Ressonance/Ressonance/image.php?f=album.jpg'
```

## 🎨 Placeholders Automáticos

### 🖼️ Imagem Padrão
Quando uma imagem não é encontrada, o sistema mostra automaticamente:
```
https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA
```

**Características**:
- 📏 Tamanho: 160x160 pixels
- 🎨 Cor de fundo: Roxo (#8a2be2)
- 📝 Texto: Símbolo musical (♪)
- 🎯 Cor do texto: Branco (#ffffff)

### 🔄 Fallback Automático

```html
<!-- 🏷️ Tag gerada automaticamente -->
<img src="/Ressonance/image.php?f=album.jpg" 
     alt="Nome do Álbum"
     onerror="this.src='https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA'">
```

**Como funciona**:
1. Tenta carregar: `image.php?f=album.jpg`
2. Se falhar: JavaScript executa `onerror`
3. Substitui por: Placeholder automático

## 🔒 Segurança

### 🛡️ Proteções Implementadas

1. **📂 Basename Only**: Impede acesso a outras pastas
```php
$file = basename($_GET['f']);  // "../../../etc/passwd" vira "passwd"
```

2. **🎨 Formatos Permitidos**: Só imagens são aceitas
```php
$allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if (!in_array($extension, $allowedFormats)) {
    http_response_code(403);  // 🚫 Bloqueado
}
```

3. **✅ Arquivo Existe**: Verifica se realmente existe
```php
if (!file_exists($imagePath)) {
    http_response_code(404);  // 🚫 Não encontrado
}
```

4. **📏 Tamanho Limitado**: Evita arquivos muito grandes (pode ser implementado)

## 🚀 Performance

### ⚡ Otimizações

1. **💾 Cache Longo**: Imagens ficam em cache por 1 ano
```php
header('Cache-Control: public, max-age=31536000');
```

2. **📏 Content-Length**: Navegador sabe o tamanho
```php
header('Content-Length: ' . filesize($imagePath));
```

3. **🎯 Content-Type Correto**: Navegador processa mais rápido
```php
header('Content-Type: image/jpeg');
```

4. **🔍 Busca Inteligente**: Para na primeira pasta que encontrar
```php
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $imagePath = $path;
        break;  // ⏹️ Para aqui, não continua procurando
    }
}
```

## 🐛 Problemas Comuns e Soluções

### ❌ "Imagens não aparecem"
**Possíveis Causas**:
1. Arquivo não existe em nenhuma pasta
2. Formato não suportado
3. Permissões de arquivo incorretas
4. Caminhos incorretos

**Soluções**:
```bash
# 1. Verificar se arquivo existe
ls public/assets/images/nome_da_imagem.jpg

# 2. Testar diretamente no navegador
http://localhost/Ressonance/image.php?f=nome_da_imagem.jpg

# 3. Verificar permissões (Linux/Mac)
chmod 644 public/assets/images/*.jpg

# 4. Verificar console do navegador (F12)
# Procurar por erros 404 ou 403
```

### ❌ "Placeholder não aparece"
**Causa**: JavaScript não carregou
**Solução**:
```javascript
// Verificar no console (F12):
console.log(typeof window.ImageHandler);  // Deve ser 'object'
```

### ❌ "Imagens antigas ainda quebradas"
**Causa**: Cache do navegador
**Solução**:
```bash
# Forçar atualização:
Ctrl + F5 (Windows)
Cmd + Shift + R (Mac)

# Ou limpar cache do navegador
```

## 📱 Uso Prático

### 🔧 Como Usar nas Páginas

**Método 1 - Função PHP**:
```php
<!-- ✅ Recomendado -->
<?= fixImageTag($album['image'], $album['title']) ?>

<!-- Gera: -->
<img src="/Ressonance/image.php?f=album.jpg" 
     alt="Nome do Álbum" 
     onerror="this.src='...'">
```

**Método 2 - Função de URL**:
```php
<img src="<?= getImageUrl($song['image']) ?>" alt="<?= $song['title'] ?>">
```

**Método 3 - JavaScript Automático**:
```html
<!-- ⚡ Correção automática -->
<img src="/broken/path/image.jpg" alt="Imagem">
<!-- JavaScript corrige automaticamente para: -->
<!-- <img src="/Ressonance/image.php?f=image.jpg" alt="Imagem"> -->
```

## 🔗 Arquivos Relacionados

- [sistema-caminhos.md](sistema-caminhos.md) - Como os caminhos são corrigidos
- [correcao-automatica.md](correcao-automatica.md) - Sistema geral de correção
- [estrutura-projeto.md](estrutura-projeto.md) - Onde as imagens ficam
- [configuracao-assets.md](configuracao-assets.md) - Gerenciamento de assets

## 💡 Dicas Pro

### 🖼️ Para Adicionar Novas Imagens
1. Coloque na pasta `public/assets/images/`
2. Use nomes únicos: `album_123456.jpg`
3. Formatos recomendados: JPG (fotos), PNG (logos)
4. Tamanho recomendado: 300x300px para capas

### 🔧 Para Debugar Problemas
1. Teste a URL diretamente: `image.php?f=imagem.jpg`
2. Verifique se o arquivo existe fisicamente
3. Olhe o console do navegador (F12)
4. Teste com diferentes formatos

### 🚀 Para Melhor Performance
1. Use JPG para fotos (menor tamanho)
2. Use PNG para logos/ícones (melhor qualidade)
3. Otimize imagens antes do upload
4. Mantenha tamanhos consistentes