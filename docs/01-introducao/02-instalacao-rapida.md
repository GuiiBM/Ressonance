# 🚀 Instalação Rápida - 5 Minutos

## 🎯 O que você vai fazer

Vamos colocar o Ressonance funcionando em **5 minutos**. Sério mesmo!

## 📋 Requisitos Mínimos

### 💻 **No seu computador**
- **XAMPP** (recomendado) ou WAMP
- **Navegador moderno** (Chrome, Firefox, Edge)
- **Arquivos MP3** para testar

### 🌐 **Em servidor web**
- **PHP 7.4+** 
- **MySQL 5.7+**
- **Apache** ou Nginx
- **Extensões PHP**: PDO, MySQL

## ⚡ Instalação Super Rápida

### 1️⃣ **Baixar e Extrair**
```bash
# Baixe o projeto
# Extraia para: C:\xampp\htdocs\Ressonance\
```

### 2️⃣ **Iniciar XAMPP**
```
1. Abra XAMPP Control Panel
2. Clique "Start" em Apache
3. Clique "Start" em MySQL
4. Aguarde ficarem verdes ✅
```

### 3️⃣ **Acessar no Navegador**
```
http://localhost/Ressonance/Ressonance/
```

### 4️⃣ **Pronto! 🎉**
- O banco de dados é criado automaticamente
- Todos os caminhos são configurados sozinhos
- O sistema já está funcionando!

## 🎵 Testando com Sua Primeira Música

### 1️⃣ **Colocar Arquivo MP3**
```
Copie um arquivo MP3 para:
C:\xampp\htdocs\Ressonance\Ressonance\audio\
```

### 2️⃣ **Adicionar no Sistema**
```
1. Acesse: http://localhost/Ressonance/Ressonance/admin.php
2. Adicione um artista
3. Adicione uma música
4. Selecione o arquivo MP3
```

### 3️⃣ **Tocar a Música**
```
1. Volte para: http://localhost/Ressonance/Ressonance/
2. Clique na música que você adicionou
3. Clique no botão Play ▶️
4. 🎵 Música tocando!
```

## 🔧 Configurações Automáticas

### ✅ **O que o sistema faz sozinho**
- 🗄️ Cria banco de dados `ressonance_music`
- 📊 Cria todas as tabelas necessárias
- 🛣️ Detecta caminhos automaticamente
- 🖼️ Configura sistema de imagens
- 🎵 Configura streaming de áudio

### ⚙️ **Arquivos de Configuração**
```
📄 app/config/database.php    - Conexão com banco
📄 app/config/paths.php       - Caminhos do sistema
📄 public/assets/js/config.js - Configurações JavaScript
```

## 🌐 Instalação em Servidor Web

### 📤 **Upload dos Arquivos**
```bash
# Via FTP/SFTP, envie todos os arquivos para:
/public_html/ressonance/
# ou
/var/www/html/ressonance/
```

### 🗄️ **Configurar Banco de Dados**
```php
// Edite: app/config/database.php
define('DB_HOST', 'localhost');        // Seu servidor MySQL
define('DB_USER', 'seu_usuario');      // Seu usuário MySQL
define('DB_PASS', 'sua_senha');        // Sua senha MySQL
define('DB_NAME', 'ressonance_music'); // Nome do banco
```

### 🌐 **Acessar no Navegador**
```
http://seusite.com/ressonance/
```

## 🐛 Problemas Comuns na Instalação

### ❌ **"Página não encontrada"**
**Causa**: Caminhos incorretos
**Solução**:
```
Acesse: http://localhost/Ressonance/Ressonance/fix-paths.php
Aguarde: "Caminhos verificados e corrigidos!"
```

### ❌ **"Erro na conexão com banco"**
**Causa**: MySQL não está rodando
**Solução**:
```
1. Abra XAMPP Control Panel
2. Clique "Start" em MySQL
3. Aguarde ficar verde ✅
4. Recarregue a página
```

### ❌ **"CSS não carrega"**
**Causa**: Caminhos de assets incorretos
**Solução**:
```
1. Verifique se a pasta public/assets/ existe
2. Execute: fix-paths.php
3. Limpe cache do navegador (Ctrl+F5)
```

### ❌ **"Música não toca"**
**Causa**: Arquivo não está na pasta correta
**Solução**:
```
1. Verifique se o MP3 está em: audio/
2. Teste diretamente: http://localhost/Ressonance/Ressonance/audio.php?f=sua_musica.mp3
3. Verifique se o formato é suportado (MP3, FLAC, WAV)
```

## 📱 Testando se Tudo Funciona

### ✅ **Checklist de Funcionamento**

1. **Página inicial carrega?**
   - ✅ Sim → Continue
   - ❌ Não → Verifique Apache no XAMPP

2. **CSS está aplicado?**
   - ✅ Sim → Continue  
   - ❌ Não → Execute fix-paths.php

3. **Consegue acessar admin?**
   - ✅ Sim → Continue
   - ❌ Não → Verifique MySQL no XAMPP

4. **Consegue adicionar artista?**
   - ✅ Sim → Continue
   - ❌ Não → Verifique banco de dados

5. **Música toca no player?**
   - ✅ Sim → 🎉 **TUDO FUNCIONANDO!**
   - ❌ Não → Verifique arquivo MP3

## 🎯 Estrutura Após Instalação

```
Ressonance/
├── 📁 audio/                    # 🎵 Seus arquivos MP3 aqui
├── 📁 app/                      # ⚙️ Código da aplicação
├── 📁 public/assets/            # 🎨 CSS, JS, imagens
├── 📁 docs/                     # 📚 Esta documentação
├── 📄 index.php                 # 🏠 Página inicial
├── 📄 audio.php                 # 🎵 Servidor de música
├── 📄 image.php                 # 🖼️ Servidor de imagens
└── 📄 fix-paths.php             # 🔧 Correção de caminhos
```

## 🚀 Próximos Passos

Agora que está funcionando, vamos testar:

👉 **Continue para**: [03-primeiro-uso.md](03-primeiro-uso.md)

## 🆘 Precisa de Ajuda?

### 📞 **Suporte Rápido**
- 🐛 Problemas → [Troubleshooting](../09-manutencao/01-troubleshooting.md)
- 🎵 Player não funciona → [Sistema de Áudio](../04-sistema-audio/01-servidor-audio.md)
- 🖼️ Imagens não aparecem → [Sistema de Imagens](../05-sistema-imagens/01-servidor-imagens.md)

### 💡 **Dicas**
- Sempre use **fix-paths.php** quando algo não funcionar
- Verifique o **console do navegador** (F12) para erros
- Teste URLs diretamente para diagnosticar problemas