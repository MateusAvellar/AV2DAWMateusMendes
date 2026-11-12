//Cria pergunta de múltipla escolha
<?php
header('Content-Type: application/json; charset=utf-8');

$arqPerguntas = 'perguntas.txt';
$arqRespostas = 'respostas.txt';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $perguntaTexto = str_replace(';', ',', trim($_POST['pergunta'] ?? ''));
    $alternativas = $_POST['alternativas'] ?? [];
    $corretaIndex = (int)($_POST['correta'] ?? -1);

    if($perguntaTexto === ''){
        echo json_encode(['sucesso' => false, 'mensagem' => 'Informe a pergunta.']);
        exit;
    }

    $novoId = 1;
    if(file_exists($arqPerguntas)){
        $linhas = file($arqPerguntas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach($linhas as $linha){
            $cols = explode(';', $linha);
            if((int)$cols[0] >= $novoId){
                $novoId = (int)$cols[0] + 1;
            }
        }
    }

    $linhaPergunta = "{$novoId};{$perguntaTexto};resposta;gabarito\n";
    file_put_contents($arqPerguntas, $linhaPergunta, FILE_APPEND | LOCK_EX);

    $idResposta = 1;
    foreach($alternativas as $index => $textoAlt){
        $textoAlt = str_replace(';', ',', trim($textoAlt));
        if($textoAlt !== ''){
            $ehCorreta = ($index === $corretaIndex) ? 1 : 0;
            $linhaResposta = "{$novoId};{$idResposta};{$textoAlt};{$ehCorreta}\n";
            file_put_contents($arqRespostas, $linhaResposta, FILE_APPEND | LOCK_EX);
            $idResposta++;
        }
    }

    echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta de múltipla escolha criada!']);
}
?>