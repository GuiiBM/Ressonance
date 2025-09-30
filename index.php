<?php
// Arquivo de entrada principal - redireciona para a estrutura organizada
// Garantir configuração na primeira execução
if (!file_exists('.paths_checked')) {
    require_once 'app/config/first-run.php';
}
header('Location: app/views/pages/index.php');
exit; ?>