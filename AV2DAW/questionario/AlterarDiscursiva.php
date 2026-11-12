<?php
header('Content-Type: application/json; charset=utf-8');

$arqPerguntas = 'perguntas.txt';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$achou = false;
$linhas = [];

if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $achou = true;
        }
    }
}

if($achou && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $pergunta = str_replace(';', ',', trim($_POST['pergunta'] ?? ''));
    $gabarito = str_replace(';', ',', trim($_POST['gabarito'] ?? ''));

    $novas = [];
    foreach($linhas as $linha){ // Corrigido aqui
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $linha = "{$id};{$pergunta};resposta;{$gabarito}";
        }
        $novas[] = $linha;
    }
    file_put_contents($arqPerguntas, implode("\n", $novas) . "\n", LOCK_EX);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta discursiva alterada!']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Pergunta não encontrada.']);
}
?>
