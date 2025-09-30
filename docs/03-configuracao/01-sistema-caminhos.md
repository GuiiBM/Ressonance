# 🛣️ Sistema de Caminhos Automático

## 🔍 O que é

O sistema de caminhos do Ressonance é uma funcionalidade **MÁGICA** que detecta automaticamente onde o projeto está instalado e corrige todos os caminhos quebrados. É como ter um GPS que sempre sabe onde você está!

## 📂 Onde está

### Arquivos Principais
- **📄 `app/config/path-checker.php`** - O cérebro do sistema
- **📄 `app/config/paths.php`** - Configurações de caminhos
- **📄 `public/assets/js/config.js`** - Configurações JavaScript
- **📄 `fix-paths.php`** - Correção manual (emergência)

## ⚙️ Como Funciona (Explicação Simples)

### 🎯 Problema que Resolve

Imagine que você tem um site em:
- Computador A: `http://localhost/Ressonance/`
- Computador B: `http://localhost/projetos/Ressonance/`
- Servidor: `http://meusite.com/musica/Ressonance/`

**SEM o sistema**: Você teria que mudar manualmente todos os caminhos em cada lugar.
**COM o sistema**: Ele detecta automaticamente e ajusta tudo sozinho!

### 🔄 Fluxo de Funcionamento

```
1. 👤 Usuário acessa o site pela primeira vez
   ↓
2. 🔍 Sistema detecta: "Onde estou instalado?"
   ↓
3. 🧠 Analisa a URL atual: "/Ressonance/Ressonance/index.php"
   ↓
4. 💡 Descobre: "Estou em /Ressonance/Ressonance/"
   ↓
5. 🔧 Corrige automaticamente:
   - paths.php → BASE_URL = '/Ressonance/Ressonance'
   - config.js → BASE_URL = '/Ressonance/Ressonance'
   - Banco de dados → URLs de imagens
   ↓
6. ✅ Tudo funciona perfeitamente!
```

## 🔧 Detalhes Técnicos

### 📄 path-checker.php - O Cérebro

```php
function checkAndFixPaths() {
    // 🕵️ Detecta onde está instalado
    $scriptName = $_SERVER['SCRIPT_NAME']; // Ex: "/Ressonance/Ressonance/index.php"
    $pathParts = explode('/', trim($scriptName, '/'));
    
    // 🔍 Procura por "Ressonance" no caminho
    foreach ($pathParts as $i => $part) {
        if (strtolower($part) === 'ressonance') {
            // 🎯 Encontrou! Monta o caminho correto
            $projectPath = '/' . implode('/', array_slice($pathParts, 0, $i + 2));
            break;
        }
    }
    
    // 🔧 Corrige os arquivos automaticamente
    // ... código de correção ...
}
```

### 📄 paths.php - Configurações Centralizadas

```php
// 🏠 Caminho base do projeto (corrigido automaticamente)
define('BASE_URL', '/Ressonance/Ressonance');

// 📦 Caminhos de assets
define('ASSETS_URL', BASE_URL . '/public/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');

// 🌐 URLs de páginas
define('PAGES_URL', BASE_URL . '/app/views/pages');
define('API_URL', BASE_URL . '/app/controllers/api');
```

### 📄 config.js - Configurações JavaScript

```javascript
// 🌐 Configurações para o frontend (gerado automaticamente)
window.APP_CONFIG = {
    API_BASE_URL: '/Ressonance/Ressonance/app/controllers/api',
    BASE_URL: '/Ressonance/Ressonance',
    IMAGE_URL: '/Ressonance/Ressonance/image.php'
};
```

## 🚀 Quando é Executado

### 🔄 Execução Automática

1. **Primeira vez**: Quando alguém acessa o site
2. **Diariamente**: Uma vez por dia para verificar mudanças
3. **Arquivo de controle**: `.paths_checked` evita execuções desnecessárias

### 📅 Sistema de Cache

```php
$flagFile = $basePath . '/.paths_checked';

// ⏰ Só executa se:
// - Arquivo não existe (primeira vez)
// - Arquivo tem mais de 24 horas
if (file_exists($flagFile) && (time() - filemtime($flagFile)) < 86400) {
    return; // 🚫 Não precisa executar
}
```

## 🔧 O que é Corrigido Automaticamente

### 1. 📄 Arquivos PHP
- `paths.php` → BASE_URL correto
- Todas as páginas que usam BASE_URL

### 2. 📜 Arquivos JavaScript
- `config.js` → URLs corretas para APIs
- Todos os scripts que usam APP_CONFIG

### 3. 🗄️ Banco de Dados
- URLs de imagens de artistas
- URLs de imagens de álbuns
- URLs de imagens de músicas

### 4. 🖼️ Sistema de Imagens
- Caminhos quebrados → `image.php?f=arquivo.jpg`
- URLs antigas → URLs novas automaticamente

## 🔧 Como Usar

### 🤖 Automático (Recomendado)
Não faça nada! O sistema funciona sozinho quando alguém acessa o site.

### 🛠️ Manual (Se necessário)
1. Acesse: `http://seusite.com/Ressonance/fix-paths.php`
2. Aguarde a mensagem: "Caminhos verificados e corrigidos!"
3. Pronto!

## 🐛 Problemas Comuns e Soluções

### ❌ "CSS não carrega"
**Causa**: BASE_URL incorreto
**Solução**:
```bash
# Acesse no navegador:
http://localhost/Ressonance/fix-paths.php
```

### ❌ "JavaScript não funciona"
**Causa**: config.js com caminhos errados
**Solução**: O fix-paths.php corrige automaticamente

### ❌ "Imagens não aparecem"
**Causa**: Caminhos de imagem quebrados
**Solução**: Sistema corrige automaticamente, mas pode forçar:
```bash
# Delete o arquivo de cache:
rm .paths_checked
# Acesse qualquer página do site
```

### ❌ "APIs não respondem"
**Causa**: API_BASE_URL incorreto
**Verificar**:
1. Abra o console do navegador (F12)
2. Digite: `console.log(window.APP_CONFIG)`
3. Verifique se API_BASE_URL está correto

## ⚠️ Pontos Importantes

### 🔒 Segurança
- Sistema só executa uma vez por dia (performance)
- Não expõe informações sensíveis
- Validações em todos os caminhos

### 📱 Compatibilidade
- Funciona em qualquer servidor
- Detecta automaticamente a estrutura
- Não depende de configuração manual

### 🔄 Manutenibilidade
- Código centralizado em um arquivo
- Fácil de entender e modificar
- Logs automáticos de execução

## 🎯 Exemplos Práticos

### Cenário 1: Desenvolvimento Local
```
Instalação: C:\xampp\htdocs\Ressonance\Ressonance\
URL: http://localhost/Ressonance/Ressonance/
Resultado: BASE_URL = '/Ressonance/Ressonance'
```

### Cenário 2: Servidor de Produção
```
Instalação: /var/www/html/musica/Ressonance/
URL: http://meusite.com/musica/Ressonance/
Resultado: BASE_URL = '/musica/Ressonance'
```

### Cenário 3: Subdomínio
```
Instalação: /var/www/html/Ressonance/
URL: http://musica.meusite.com/Ressonance/
Resultado: BASE_URL = '/Ressonance'
```

## 🔗 Arquivos Relacionados

- [estrutura-projeto.md](estrutura-projeto.md) - Organização do projeto
- [sistema-imagens.md](sistema-imagens.md) - Como as imagens são corrigidas
- [configuracao-assets.md](configuracao-assets.md) - Gerenciamento de CSS/JS
- [fluxo-aplicacao.md](fluxo-aplicacao.md) - Fluxo completo da aplicação

## 💡 Dica Pro

Para desenvolvedores: Se você mover o projeto para outro local, simplesmente delete o arquivo `.paths_checked` e acesse qualquer página. O sistema se reconfigura automaticamente!