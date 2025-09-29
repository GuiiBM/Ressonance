# Estrutura de Includes - Ressonance

## Arquivos Modulares

### `init.php`
- Inicialização comum para todas as páginas
- Carrega configurações, autenticação e dependências
- Inicia sessão e gera token CSRF
- Instancia classe de queries do banco

### `head.php`
- Meta tags e links CSS/JS comuns
- Título dinâmico baseado na variável `$pageTitle`
- Links para FontAwesome e Google Fonts

### `header.php`
- Header comum com logo, navegação e perfil
- Usa constantes definidas em `config-common.php`
- Detecção automática de usuário logado

### `sidebar.php`
- Sidebar com navegação da biblioteca
- Detecção automática da página ativa
- Links para todas as seções principais

### `player.php`
- Player de música comum
- Seção de comentários condicional (apenas no index)
- Controles completos de reprodução

### `album-modal.php`
- Modal reutilizável para exibição de álbuns
- Estrutura padrão para carregamento dinâmico

### `scripts.php`
- Scripts JavaScript comuns
- Carregamento condicional baseado na página
- Funções do modal de álbum centralizadas

### `admin-header.php`
- Header específico para páginas administrativas
- Layout diferenciado para área admin

### `config-common.php`
- Constantes e configurações globais
- Funções utilitárias comuns
- Configurações de upload e segurança

### `database-queries.php`
- Classe centralizada para queries do banco
- Métodos organizados por funcionalidade
- Reutilização de código e manutenção facilitada

## Como Usar

### Em uma nova página:
```php
<?php
require_once 'includes/init.php';
$pageTitle = 'Nome da Página';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="content">
        <!-- Conteúdo da página -->
    </main>
    
    <?php include 'includes/player.php'; ?>
    <?php include 'includes/album-modal.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>
</html>
```

### Para usar queries do banco:
```php
// Após incluir init.php, a variável $db está disponível
$songs = $db->getAllSongs();
$albums = $db->getRecentAlbums(10);
$artists = $db->getAllArtists();
```

## Benefícios

1. **Reutilização de Código**: Componentes comuns em arquivos separados
2. **Manutenção Facilitada**: Alterações em um local afetam todas as páginas
3. **Consistência**: Interface uniforme em toda a aplicação
4. **Performance**: Carregamento condicional de scripts
5. **Organização**: Código limpo e bem estruturado
6. **Escalabilidade**: Fácil adição de novas páginas e funcionalidades