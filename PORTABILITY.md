# 🚀 Guia de Portabilidade - Ressonance

## ✨ Instalação em Qualquer PC

### Método 1: Instalação Automática (Recomendado)

1. **Copiar arquivos** para qualquer servidor web
2. **Acessar**: `http://localhost/caminho/install.php`
3. **Aguardar** configuração automática
4. **Pronto!** Sistema funcionando

### Método 2: Instalação Manual

```bash
# 1. Copiar arquivos
# 2. Configurar servidor web (Apache/Nginx + MySQL)
# 3. Acessar install.php
# 4. Seguir instruções na tela
```

## 🔧 Recursos de Portabilidade

### ✅ **Detecção Automática**
- **Caminhos**: Detecta automaticamente onde está instalado
- **Banco**: Testa diferentes configurações MySQL
- **Ambiente**: Adapta-se a Windows, Linux, macOS

### ✅ **Configuração Zero**
- **Primeira execução**: Cria tudo automaticamente
- **Banco de dados**: Schema completo instalado
- **Estrutura**: Pastas criadas automaticamente

### ✅ **Compatibilidade**
- **Servidores**: Apache, Nginx, IIS
- **PHP**: 7.4+ (testado até 8.2)
- **MySQL**: 5.7+ / MariaDB 10.3+
- **Sistemas**: Windows, Linux, macOS

## 📁 Estrutura Portável

```
Ressonance/
├── install.php              # 🔧 Instalador automático
├── system-check.php         # 🔍 Verificador completo
├── fix-paths.php           # 🛠️ Corretor de caminhos
├── database/
│   ├── schema.sql          # 📊 Schema completo
│   └── init.php           # 🚀 Inicializador
├── app/config/
│   ├── environment.php     # 🌍 Detecção automática
│   └── database.php       # 💾 Configuração DB
└── [resto dos arquivos]
```

## 🎯 Cenários de Uso

### 💻 **Desenvolvimento Local**
```bash
# XAMPP/WAMP/MAMP
http://localhost/Ressonance/install.php
```

### 🌐 **Servidor Web**
```bash
# Qualquer hosting com PHP+MySQL
https://seusite.com/ressonance/install.php
```

### 🐳 **Docker**
```bash
docker-compose up
# Acessa automaticamente em localhost:8080
```

## 🔍 Verificações Disponíveis

### **install.php** - Instalador Completo
- ✅ Verifica requisitos
- ✅ Detecta ambiente
- ✅ Configura banco
- ✅ Cria estrutura
- ✅ Testa sistema

### **system-check.php** - Verificação Completa
- 🔍 Status do sistema
- 💾 Integridade do banco
- 📁 Arquivos essenciais
- 🔐 Permissões

### **fix-paths.php** - Correção Automática
- 🛠️ Corrige caminhos
- 🔄 Atualiza configurações
- 🖼️ Ajusta URLs de imagens

## 🚨 Solução de Problemas

### ❌ **Erro de Conexão MySQL**
```bash
# 1. Verificar se MySQL está rodando
# 2. Testar credenciais
# 3. Executar: install.php
```

### ❌ **Caminhos Incorretos**
```bash
# 1. Executar: fix-paths.php
# 2. Limpar cache do navegador
# 3. Verificar: system-check.php
```

### ❌ **Permissões**
```bash
# Linux/macOS
chmod -R 755 Ressonance/
chmod -R 777 Ressonance/storage/

# Windows: Dar permissão total à pasta
```

## 📋 Checklist de Portabilidade

### ✅ **Antes de Copiar**
- [ ] Fazer backup do banco (se existir)
- [ ] Copiar pasta `storage/` com músicas
- [ ] Anotar configurações especiais

### ✅ **Após Copiar**
- [ ] Executar `install.php`
- [ ] Verificar `system-check.php`
- [ ] Testar reprodução de música
- [ ] Verificar upload de arquivos

### ✅ **Configurações Especiais**
- [ ] Ajustar `max_upload_size` no PHP
- [ ] Configurar `.htaccess` se necessário
- [ ] Verificar extensões PHP

## 🎵 **Resultado Final**

Após seguir este guia, o Ressonance funcionará **identicamente** em qualquer ambiente, mantendo:

- ✅ **Todas as funcionalidades**
- ✅ **Banco de dados completo**
- ✅ **Configurações automáticas**
- ✅ **Compatibilidade total**

---

**🔗 Links Úteis:**
- [install.php](install.php) - Instalador
- [system-check.php](system-check.php) - Verificação
- [fix-paths.php](fix-paths.php) - Correção