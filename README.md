# Ressonance - Estrutura Organizada

## Arquitetura de Pastas

```
Ressonance/
├── app/                          # Aplicação principal
│   ├── config/                   # Configurações
│   │   ├── auth.php             # Autenticação
│   │   ├── database.php         # Banco de dados
│   │   └── paths.php            # Caminhos e URLs
│   ├── controllers/             # Controladores
│   │   └── api/                 # APIs REST
│   │       ├── security.php     # Segurança
│   │       └── songs.php        # Endpoints de músicas
│   ├── models/                  # Modelos (futuro)
│   └── views/                   # Views e componentes
│       ├── components/          # Componentes reutilizáveis
│       │   ├── init.php         # Inicialização
│       │   ├── head.php         # Meta tags
│       │   ├── header.php       # Cabeçalho
│       │   ├── sidebar.php      # Barra lateral
│       │   ├── player.php       # Player de música
│       │   ├── album-modal.php  # Modal de álbuns
│       │   ├── scripts.php      # Scripts JS
│       │   ├── admin-header.php # Header admin
│       │   ├── config-common.php # Configurações comuns
│       │   └── database-queries.php # Queries centralizadas
│       └── pages/               # Páginas principais
│           ├── index.php        # Página inicial
│           ├── albums.php       # Álbuns
│           ├── artists.php      # Artistas
│           ├── all-songs.php    # Todas as músicas
│           └── admin.php        # Painel admin
├── public/                      # Arquivos públicos
│   └── assets/                  # Assets estáticos
│       ├── css/                 # Estilos
│       │   └── styles.css       # CSS principal
│       ├── js/                  # JavaScript
│       │   ├── player-core.js   # Player principal
│       │   ├── lazy-loader.js   # Carregamento dinâmico
│       │   ├── all-songs-loader.js # Loader de músicas
│       │   └── script.js        # Scripts gerais
│       └── images/              # Imagens
│           └── logo.png         # Logo do site
├── storage/                     # Armazenamento
│   └── uploads/                 # Uploads
│       └── audio/               # Arquivos de áudio
└── [arquivos de entrada]        # Redirecionamentos
    ├── index.php               # → app/views/pages/index.php
    ├── albums.php              # → app/views/pages/albums.php
    ├── artists.php             # → app/views/pages/artists.php
    ├── all-songs.php           # → app/views/pages/all-songs.php
    └── admin.php               # → app/views/pages/admin.php
```

## Benefícios da Nova Estrutura

### 1. **Separação de Responsabilidades**
- **app/**: Lógica da aplicação
- **public/**: Assets públicos
- **storage/**: Arquivos de dados

### 2. **Organização Limpa**
- Configurações centralizadas em `app/config/`
- Componentes reutilizáveis em `app/views/components/`
- Páginas organizadas em `app/views/pages/`
- Assets otimizados em `public/assets/`

### 3. **Manutenibilidade**
- Caminhos centralizados em `paths.php`
- Queries organizadas em classe única
- Componentes modulares e reutilizáveis

### 4. **Segurança**
- Arquivos sensíveis fora do diretório público
- Uploads isolados em `storage/`
- Configurações protegidas

### 5. **Performance**
- Assets otimizados e organizados
- Carregamento condicional de scripts
- Estrutura preparada para cache

## Como Usar

### Acessar o Site
- URL principal: `http://localhost/Ressonance/`
- Automaticamente redireciona para a estrutura organizada

### Desenvolvimento
- Páginas: `app/views/pages/`
- Componentes: `app/views/components/`
- Assets: `public/assets/`
- Configurações: `app/config/`

### Adicionar Nova Página
1. Criar arquivo em `app/views/pages/`
2. Incluir componentes necessários
3. Criar redirecionamento na raiz (opcional)

A estrutura mantém total compatibilidade com URLs existentes através dos redirecionamentos automáticos.