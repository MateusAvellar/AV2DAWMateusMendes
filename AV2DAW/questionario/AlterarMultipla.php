<?php
header('Content-Type: application/json; charset=utf-8');

$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

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
    $alternativas = $_POST['alternativas'] ?? [];
    $corretaIndex = (int)($_POST['correta'] ?? -1);

    $novas = [];
    foreach($linhas as $linha){ // Corrigido aqui
        $cols = explode(';', $linha);
        if((int)$cols[0] === $id){
            $linha = "{$id};{$pergunta};resposta;gabarito";
        }
        $novas[] = $linha;
    }
    file_put_contents($arqPerguntas, implode("\n", $novas) . "\n", LOCK_EX);

    $outras = [];
    if(file_exists($arqRespostas)){
        $linhasResp = file($arqRespostas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($linhasResp as $linha){
            $cols = explode(';', $linha);
            if((int)$cols[0] !== $id){
                $outras[] = $linha;
            }
        }
    }

    $idResposta = 1;
    foreach($alternativas as $index => $textoAlt){
        $textoAlt = str_replace(';', ',', trim($textoAlt));
        if($textoAlt !== ''){
            $ehCorreta = ($index === $corretaIndex) ? 1 : 0;
            $outras[] = "{$id};{$idResposta};{$textoAlt};{$ehCorreta}";
            $idResposta++;
        }
    }
    file_put_contents($arqRespostas, $outras ? implode("\n", $outras) . "\n" : "", LOCK_EX);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta de múltipla escolha alterada!']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Pergunta não encontrada.']);
}
?>