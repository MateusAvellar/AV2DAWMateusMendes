<?php
header('Content-Type: application/json; charset=utf-8');

$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$pergunta = null;
$linhas = [];

if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $pergunta = $cols;
        }
    }
}

if($pergunta && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $mantem = [];
    foreach($linhas as $linha){ // Corrigido aqui
        $cols = explode(';', $linha);
        if((int)$cols[0] !== $id){
            $mantem[] = $linha;
        }
    }
    file_put_contents($arqPerguntas, $mantem ? implode("\n", $mantem) . "\n" : "", LOCK_EX);

    if(file_exists($arqRespostas)){
        $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $mantem = [];
        foreach($linhasResp as $linha){
            $cols = explode(';', $linha);
            if((int)$cols[0] !== $id){
                $mantem[] = $linha;
            }
        }
        file_put_contents($arqRespostas, $mantem ? implode("\n", $mantem) . "\n" : "", LOCK_EX);
    }

    echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta excluída com sucesso!']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Pergunta não encontrada.']);
}
?>