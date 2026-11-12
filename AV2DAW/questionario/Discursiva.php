//Cria pergunta discursiva
<?php
header('Content-Type: application/json; charset=utf-8');

$arqPerguntas = 'perguntas.txt';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $perguntaTexto = str_replace(';', ',', trim($_POST['pergunta'] ?? ''));
    $gabaritoTexto = str_replace(';', ',', trim($_POST['gabarito'] ?? ''));

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

    $linhaPergunta = "{$novoId};{$perguntaTexto};resposta;{$gabaritoTexto}\n";
    file_put_contents($arqPerguntas, $linhaPergunta, FILE_APPEND | LOCK_EX);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Pergunta discursiva criada!']);
}
?>