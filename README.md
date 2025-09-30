# 🎵 Ressonance - Sistema de Streaming Musical

> **Sistema completo de streaming de música com correção automática de caminhos e configuração zero**

## 🚀 Início Rápido

1. **Instalar**: Extrair para `C:\xampp\htdocs\Ressonance\`
2. **Iniciar**: XAMPP → Start Apache + MySQL
3. **Acessar**: `http://localhost/Ressonance/Ressonance/`
4. **Pronto!** Sistema detecta primeira execução e se configura automaticamente

> ✨ **Configuração Zero**: Na primeira vez que acessar, o sistema:
> - Detecta automaticamente onde está instalado
> - Cria todas as pastas necessárias
> - Configura banco de dados
> - Ajusta todos os caminhos
> - **Funciona em qualquer máquina sem configuração manual!**

## 📚 Documentação Completa

**👉 [Acesse a documentação completa em `docs/`](docs/README.md)**

- 📖 **Iniciantes**: [01-introducao/](docs/01-introducao/) - Instalação e primeiro uso
- 🏗️ **Desenvolvedores**: [02-arquitetura/](docs/02-arquitetura/) - Como o código funciona
- 🔧 **Problemas**: [09-manutencao/01-troubleshooting.md](docs/09-manutencao/01-troubleshooting.md) - Soluções rápidas

## 🛠️ Ferramentas de Diagnóstico

- **[fix-paths.php](fix-paths.php)** - Corrige caminhos automaticamente
- **[health-check.php](health-check.php)** - Verifica saúde do sistema
- **[verify-system.php](verify-system.php)** - Verificação completa de integridade

## ✨ Principais Funcionalidades

- 🎵 **Streaming de música** (MP3, FLAC, WAV, OGG)
- 🤖 **Configuração automática** (zero configuração manual)
- 🛣️ **Correção automática de caminhos** (funciona em qualquer servidor)
- 🖼️ **Sistema de imagens** com correção automática
- 💿 **Organização por álbuns e artistas**
- 🎼 **Player completo** com controles avançados
- 📱 **Interface responsiva** e moderna

## 🏗️ Arquitetura de Pastas

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

## 🚨 Resolução de Problemas

### ❌ Site não funciona?
```bash
# 1. Verificar se Apache/MySQL estão rodando (XAMPP)
# 2. Acessar: http://localhost/Ressonance/Ressonance/fix-paths.php
# 3. Se ainda não funcionar: http://localhost/Ressonance/Ressonance/verify-system.php
```

### ❌ Música não toca?
```bash
# 1. Verificar se arquivo MP3 está em: audio/
# 2. Testar: http://localhost/Ressonance/Ressonance/audio.php?f=sua_musica.mp3
# 3. Verificar console do navegador (F12) para erros
```

### ❌ Imagens não aparecem?
```bash
# Sistema corrige automaticamente, mas se persistir:
# 1. Aguardar alguns segundos na página
# 2. Executar: fix-paths.php
# 3. Limpar cache do navegador (Ctrl+F5)
```

## 🎯 Status do Sistema

- ✅ **Primeira Execução**: Detecção e configuração automática
- ✅ **Configuração**: 100% automática (zero configuração manual)
- ✅ **Caminhos**: Correção automática implementada
- ✅ **Banco de dados**: Criação automática na primeira execução
- ✅ **Estrutura**: Criação automática de pastas essenciais
- ✅ **Documentação**: Completa e organizada
- ✅ **Testes**: Verificação de integridade implementada
- ✅ **Portabilidade**: Funciona em qualquer máquina sem ajustes

---

**📚 Para informações detalhadas, consulte a [documentação completa](docs/README.md)**