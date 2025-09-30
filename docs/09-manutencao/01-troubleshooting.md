# 🚨 Troubleshooting - Soluções Rápidas

## 🎯 Problemas Mais Comuns

Esta é sua **central de emergência**. Problemas organizados por sintoma com soluções rápidas.

---

## 🌐 **SITE NÃO FUNCIONA**

### ❌ "Página não encontrada" / "404 Not Found"

**Sintomas**:
- Página em branco
- Erro 404
- "This site can't be reached"

**Soluções**:
```bash
# 1. Verificar se Apache está rodando (XAMPP)
Abrir XAMPP Control Panel → Start Apache

# 2. Verificar URL correta
http://localhost/Ressonance/Ressonance/
# (não esqueça do duplo "Ressonance")

# 3. Corrigir caminhos automaticamente
http://localhost/Ressonance/Ressonance/fix-paths.php
```

### ❌ "Erro na conexão" / "Database connection failed"

**Sintomas**:
- "Erro na conexão: ..."
- Página carrega mas sem dados
- Erro de PDO

**Soluções**:
```bash
# 1. Verificar se MySQL está rodando (XAMPP)
Abrir XAMPP Control Panel → Start MySQL

# 2. Verificar configurações do banco
Editar: app/config/database.php
- DB_HOST: 'localhost'
- DB_USER: 'root'
- DB_PASS: '' (vazio no XAMPP)

# 3. Testar conexão manual
mysql -u root -p
# (senha vazia no XAMPP)
```

---

## 🎨 **CSS/VISUAL NÃO FUNCIONA**

### ❌ Site sem estilo / "Página feia"

**Sintomas**:
- Texto sem formatação
- Sem cores ou layout
- Parece HTML puro

**Soluções**:
```bash
# 1. Corrigir caminhos de CSS
http://localhost/Ressonance/Ressonance/fix-paths.php

# 2. Verificar se arquivo CSS existe
Verificar: public/assets/css/styles.css

# 3. Limpar cache do navegador
Ctrl + F5 (Windows)
Cmd + Shift + R (Mac)

# 4. Testar CSS diretamente
http://localhost/Ressonance/Ressonance/public/assets/css/styles.css
```

### ❌ Imagens não aparecem

**Sintomas**:
- Ícones de imagem quebrada
- Capas de álbuns não carregam
- Logo não aparece

**Soluções**:
```bash
# 1. Sistema corrige automaticamente
# (aguarde alguns segundos na página)

# 2. Forçar correção
http://localhost/Ressonance/Ressonance/fix-paths.php

# 3. Verificar se imagens existem
Verificar: public/assets/images/

# 4. Testar servidor de imagens
http://localhost/Ressonance/Ressonance/image.php?f=logo.png
```

---

## 🎵 **MÚSICA NÃO TOCA**

### ❌ Player não funciona / "Nenhuma música carregada"

**Sintomas**:
- Botão play desabilitado
- Clica em música mas nada acontece
- Player não responde

**Soluções**:
```bash
# 1. Verificar se arquivo MP3 existe
Verificar: audio/nome_da_musica.mp3

# 2. Testar servidor de áudio diretamente
http://localhost/Ressonance/Ressonance/audio.php?f=nome_da_musica.mp3

# 3. Verificar console do navegador
F12 → Console → Procurar erros em vermelho

# 4. Verificar se JavaScript carregou
F12 → Console → Digite: typeof window.playSong
# Deve retornar: "function"
```

### ❌ "Arquivo não encontrado" / "404" no áudio

**Sintomas**:
- Música carrega mas não toca
- Erro 404 no console
- "Audio not found"

**Soluções**:
```bash
# 1. Verificar nome do arquivo
# Nome no banco deve ser EXATO ao arquivo físico

# 2. Verificar localização
# Arquivo deve estar em: audio/

# 3. Verificar formato suportado
# Formatos: MP3, FLAC, WAV, OGG, M4A

# 4. Verificar permissões (Linux/Mac)
chmod 644 audio/*.mp3
```

---

## 🗄️ **BANCO DE DADOS**

### ❌ "Tabela não existe" / "Table doesn't exist"

**Sintomas**:
- Erro SQL sobre tabelas
- Dados não aparecem
- Erro ao adicionar música/artista

**Soluções**:
```bash
# 1. Forçar recriação do banco
Deletar arquivo: .paths_checked
Acessar: http://localhost/Ressonance/Ressonance/

# 2. Verificar se banco foi criado
mysql -u root -p
SHOW DATABASES;
USE ressonance_music;
SHOW TABLES;

# 3. Recriar manualmente se necessário
mysql -u root -p < database.sql
```

### ❌ Dados não aparecem / Listas vazias

**Sintomas**:
- Página de artistas vazia
- Nenhuma música listada
- Álbuns não aparecem

**Soluções**:
```bash
# 1. Verificar se há dados no banco
mysql -u root -p
USE ressonance_music;
SELECT COUNT(*) FROM artists;
SELECT COUNT(*) FROM songs;

# 2. Adicionar dados de teste
# Via admin: http://localhost/Ressonance/Ressonance/admin.php

# 3. Verificar relacionamentos
# Música precisa ter artista válido
# Álbum precisa ter artista válido
```

---

## ⚡ **PERFORMANCE / LENTIDÃO**

### ❌ Site muito lento

**Sintomas**:
- Páginas demoram para carregar
- Música trava ao tocar
- Interface travada

**Soluções**:
```bash
# 1. Verificar tamanho dos arquivos
# MP3 muito grandes (>50MB) podem travar

# 2. Verificar memória do PHP
# Editar php.ini:
memory_limit = 256M
upload_max_filesize = 50M

# 3. Otimizar banco de dados
mysql -u root -p
USE ressonance_music;
OPTIMIZE TABLE songs, artists, albums;

# 4. Limpar cache do navegador
Ctrl + Shift + Delete
```

---

## 🔧 **FERRAMENTAS DE DIAGNÓSTICO**

### 🔍 **Verificação Rápida**

```bash
# 1. Status dos serviços (XAMPP)
Apache: Verde ✅ / Vermelho ❌
MySQL: Verde ✅ / Vermelho ❌

# 2. Teste de conectividade
http://localhost/Ressonance/Ressonance/
# Deve carregar a página inicial

# 3. Teste do banco
http://localhost/phpmyadmin/
# Deve mostrar banco "ressonance_music"

# 4. Teste de arquivos
http://localhost/Ressonance/Ressonance/audio.php?f=teste.mp3
http://localhost/Ressonance/Ressonance/image.php?f=logo.png
```

### 🐛 **Console do Navegador (F12)**

```javascript
// Verificar se JavaScript carregou
console.log(typeof window.playSong);        // "function"
console.log(typeof window.APP_CONFIG);      // "object"

// Verificar configurações
console.log(window.APP_CONFIG);

// Testar player
window.debugPlayer && window.debugPlayer();

// Ver erros
// Aba Console → Procurar linhas vermelhas
```

### 📊 **Logs do Sistema**

```bash
# Logs do Apache (XAMPP)
C:\xampp\apache\logs\error.log

# Logs do MySQL (XAMPP)
C:\xampp\mysql\data\mysql_error.log

# Logs do PHP
# Verificar php.ini: log_errors = On
```

---

## 🆘 **EMERGÊNCIA - SITE PAROU COMPLETAMENTE**

### 🚨 **Procedimento de Emergência**

```bash
# 1. PARAR TUDO
XAMPP Control Panel → Stop All

# 2. REINICIAR SERVIÇOS
Start Apache → Aguardar verde ✅
Start MySQL → Aguardar verde ✅

# 3. CORRIGIR CAMINHOS
http://localhost/Ressonance/Ressonance/fix-paths.php

# 4. TESTAR BÁSICO
http://localhost/Ressonance/Ressonance/

# 5. SE AINDA NÃO FUNCIONAR
Verificar logs de erro
Restaurar backup se necessário
```

### 🔄 **Reset Completo**

```bash
# ⚠️ CUIDADO: Isso apaga todos os dados!

# 1. Parar serviços
XAMPP → Stop All

# 2. Deletar banco
mysql -u root -p
DROP DATABASE ressonance_music;

# 3. Deletar flag de inicialização
Deletar arquivo: .paths_checked

# 4. Reiniciar serviços
XAMPP → Start All

# 5. Acessar site (recria tudo)
http://localhost/Ressonance/Ressonance/
```

---

## 📞 **Ainda Precisa de Ajuda?**

### 🔗 **Documentação Relacionada**
- [Sistema de Áudio](../04-sistema-audio/01-servidor-audio.md) - Problemas com música
- [Sistema de Imagens](../05-sistema-imagens/01-servidor-imagens.md) - Problemas com imagens
- [Configuração de Caminhos](../03-configuracao/01-sistema-caminhos.md) - Problemas de URL

### 💡 **Dicas Gerais**
- **Sempre** tente `fix-paths.php` primeiro
- **Sempre** verifique o console do navegador (F12)
- **Sempre** teste URLs diretamente para isolar problemas
- **Sempre** verifique se Apache e MySQL estão rodando

### 🎯 **Regra de Ouro**
> "Se algo não funciona, 90% das vezes é problema de caminho. Execute fix-paths.php!"