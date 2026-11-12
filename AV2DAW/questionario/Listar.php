<?php
header('Content-Type: application/json; charset=utf-8');

$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

$perguntas = [];
$respostas = [];

if(file_exists($arqPerguntas)){
    $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        $id = (int)$cols[0];
        $perguntas[] = [
            'id' => $id,
            'pergunta' => $cols[1] ?? '',
            'gabarito' => $cols[3] ?? ''
        ];
    }
}

if(file_exists($arqRespostas)){
    $linhas = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $cols = explode(';', $linha);
        $idPergunta = (int)$cols[0];
        $respostas[$idPergunta][] = [
            'id_resposta' => (int)$cols[1],
            'texto' => $cols[2] ?? '',
            'correta' => (int)($cols[3] ?? 0)
        ];
    }
}

echo json_encode([
    'sucesso' => true,
    'perguntas' => $perguntas,
    'respostas' => $respostas
]);
?>