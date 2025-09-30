# 📁 Estrutura do Projeto Ressonance

## 🔍 O que é

Este documento explica como o projeto Ressonance está organizado, onde cada arquivo está localizado e qual a função de cada pasta.

## 📂 Estrutura Completa

```
Ressonance/
├── 📁 app/                          # ❤️ CORAÇÃO DA APLICAÇÃO
│   ├── 📁 config/                   # ⚙️ Configurações do sistema
│   │   ├── 📄 auth.php             # 🔐 Sistema de autenticação
│   │   ├── 📄 database.php         # 🗄️ Conexão com banco de dados
│   │   ├── 📄 init-database.php    # 🚀 Criação automática do banco
│   │   ├── 📄 paths.php            # 🛣️ Caminhos e URLs do sistema
│   │   └── 📄 path-checker.php     # 🔧 Correção automática de caminhos
│   │
│   ├── 📁 controllers/             # 🎮 Controladores (lógica de negócio)
│   │   └── 📁 api/                 # 🌐 APIs REST
│   │       ├── 📄 security.php     # 🛡️ Segurança das APIs
│   │       └── 📄 songs.php        # 🎵 Endpoints de músicas
│   │
│   ├── 📁 models/                  # 📊 Modelos de dados (futuro)
│   │
│   └── 📁 views/                   # 👁️ Interface visual
│       ├── 📁 components/          # 🧩 Peças reutilizáveis
│       │   ├── 📄 init.php         # 🚀 Inicialização de páginas
│       │   ├── 📄 head.php         # 🧠 Meta tags e CSS
│       │   ├── 📄 header.php       # 📋 Cabeçalho do site
│       │   ├── 📄 sidebar.php      # 📑 Menu lateral
│       │   ├── 📄 player.php       # 🎵 Player de música
│       │   ├── 📄 album-modal.php  # 🎼 Modal de álbuns
│       │   ├── 📄 scripts.php      # 📜 Scripts JavaScript
│       │   ├── 📄 admin-header.php # 👑 Cabeçalho administrativo
│       │   ├── 📄 config-common.php # ⚙️ Configurações comuns
│       │   ├── 📄 database-queries.php # 🗃️ Consultas centralizadas
│       │   └── 📄 image-helper.php # 🖼️ Correção de imagens
│       │
│       └── 📁 pages/               # 📄 Páginas principais
│           ├── 📄 index.php        # 🏠 Página inicial
│           ├── 📄 albums.php       # 💿 Página de álbuns
│           ├── 📄 artists.php      # 🎤 Página de artistas
│           ├── 📄 all-songs.php    # 🎵 Todas as músicas
│           └── 📄 admin.php        # 👑 Painel administrativo
│
├── 📁 public/                      # 🌐 Arquivos públicos (acessíveis via web)
│   └── 📁 assets/                  # 📦 Recursos estáticos
│       ├── 📁 css/                 # 🎨 Estilos visuais
│       │   └── 📄 styles.css       # 🎨 CSS principal
│       ├── 📁 js/                  # ⚡ JavaScript
│       │   ├── 📄 config.js        # ⚙️ Configurações JS
│       │   ├── 📄 player-core.js   # 🎵 Player principal
│       │   ├── 📄 lazy-loader.js   # 🔄 Carregamento dinâmico
│       │   ├── 📄 all-songs-loader.js # 🎵 Carregador de músicas
│       │   ├── 📄 image-handler.js # 🖼️ Manipulador de imagens
│       │   └── 📄 script.js        # 📜 Scripts gerais
│       └── 📁 images/              # 🖼️ Imagens do sistema
│           └── 📄 logo.png         # 🏷️ Logo do site
│
├── 📁 storage/                     # 💾 Armazenamento de dados
│   └── 📁 uploads/                 # 📤 Arquivos enviados
│       └── 📁 audio/               # 🎵 Arquivos de áudio
│
├── 📁 audio/                       # 🎵 Músicas (localização atual)
│
├── 📁 docs/                        # 📚 Documentação (esta pasta!)
│
└── 📄 [arquivos de entrada]        # 🚪 Pontos de entrada
    ├── 📄 index.php               # 🏠 → redireciona para app/views/pages/index.php
    ├── 📄 albums.php              # 💿 → redireciona para app/views/pages/albums.php
    ├── 📄 artists.php             # 🎤 → redireciona para app/views/pages/artists.php
    ├── 📄 all-songs.php           # 🎵 → redireciona para app/views/pages/all-songs.php
    ├── 📄 admin.php               # 👑 → redireciona para app/views/pages/admin.php
    ├── 📄 audio.php               # 🎵 Servidor de arquivos de áudio
    ├── 📄 image.php               # 🖼️ Servidor de imagens
    └── 📄 fix-paths.php           # 🔧 Correção manual de caminhos
```

## ⚙️ Como Funciona a Organização

### 🏗️ Princípio MVC Adaptado

O projeto segue uma estrutura inspirada no padrão MVC (Model-View-Controller):

- **📁 app/controllers/**: Lógica de negócio e APIs
- **📁 app/views/**: Interface visual (HTML/PHP)
- **📁 app/models/**: Modelos de dados (preparado para futuro)

### 🧩 Sistema de Componentes

**Componentes Reutilizáveis** (`app/views/components/`):
- Cada componente tem uma função específica
- Podem ser incluídos em qualquer página
- Facilitam manutenção e consistência

**Páginas Principais** (`app/views/pages/`):
- Cada página combina vários componentes
- Estrutura limpa e organizada
- Fácil de encontrar e modificar

### 🌐 Arquivos Públicos

**Por que `public/assets/`?**
- Separação clara entre código e recursos
- Melhor segurança (código PHP não fica exposto)
- Organização profissional
- Facilita cache e CDN no futuro

### 🚪 Sistema de Redirecionamento

**Arquivos na Raiz** (index.php, albums.php, etc.):
```php
<?php
header('Location: app/views/pages/index.php');
exit;
?>
```

**Por que fazer isso?**
- Mantém URLs amigáveis (`/Ressonance/albums.php`)
- Permite migração gradual para nova estrutura
- Compatibilidade com links existentes
- Flexibilidade para mudanças futuras

## 🔧 Arquivos Especiais

### 📄 audio.php
- **Localização**: Raiz do projeto
- **Função**: Serve arquivos de música com streaming
- **Como funciona**: Recebe `?f=nome_arquivo.mp3` e entrega o áudio

### 📄 image.php
- **Localização**: Raiz do projeto
- **Função**: Serve imagens de diferentes locais
- **Como funciona**: Procura imagem em várias pastas e entrega

### 📄 fix-paths.php
- **Localização**: Raiz do projeto
- **Função**: Correção manual de caminhos
- **Quando usar**: Se algo der errado com caminhos

## 🛣️ Fluxo de Navegação

1. **Usuário acessa** `/Ressonance/index.php`
2. **Redirecionamento** para `app/views/pages/index.php`
3. **Inicialização** via `app/views/components/init.php`
4. **Carregamento** de componentes (head, header, sidebar, etc.)
5. **Renderização** da página final

## ⚠️ Pontos Importantes

### 🔒 Segurança
- Código PHP fica em `app/` (protegido)
- Apenas `public/` é acessível diretamente
- Validações em todas as entradas

### 📱 Responsividade
- CSS organizado em `public/assets/css/`
- JavaScript modular em `public/assets/js/`
- Componentes reutilizáveis

### 🔄 Manutenibilidade
- Cada função tem seu lugar específico
- Fácil de encontrar e modificar
- Documentação completa

## 🐛 Problemas Comuns

### ❌ "Página não encontrada"
- **Causa**: Caminhos incorretos
- **Solução**: Execute `/fix-paths.php`

### ❌ "CSS não carrega"
- **Causa**: BASE_URL incorreto
- **Solução**: Verifique `app/config/paths.php`

### ❌ "Imagens não aparecem"
- **Causa**: Caminhos de imagem quebrados
- **Solução**: Sistema corrige automaticamente

## 🔗 Arquivos Relacionados

- [sistema-caminhos.md](sistema-caminhos.md) - Como funcionam os caminhos
- [fluxo-aplicacao.md](fluxo-aplicacao.md) - Fluxo completo da aplicação
- [componentes-reutilizaveis.md](componentes-reutilizaveis.md) - Detalhes dos componentes