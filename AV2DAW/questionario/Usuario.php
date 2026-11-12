<?php
header('Content-Type: application/json; charset=utf-8');

$arqUsuarios = 'usuarios.txt';
$acao = $_GET['acao'] ?? $_POST['acao'] ?? 'listar';
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$usuarios = [];
if(file_exists($arqUsuarios)){
    $linhas = file($arqUsuarios, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($linhas as $linha){
        $usuarios[] = explode(';', $linha);
    }
}

function salvarUsuarios($arq, $usuarios){
    $txt = "";
    foreach($usuarios as $u){
        $txt .= implode(';', $u) . "\n";
    }
    file_put_contents($arq, $txt, LOCK_EX);
}

// Se for GET (listar)
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    $listaSegura = array_map(fn($u) => [
        'id' => (int)$u[0],
        'nome' => $u[1],
        'email' => $u[2]
    ], $usuarios);
    echo json_encode(['sucesso' => true, 'usuarios' => $listaSegura]);
    exit;
}

// Se for POST (novo, editar, excluir)
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if($acao === 'novo' || $acao === 'editar'){
        $nome = str_replace(';', ',', trim($_POST['nome'] ?? ''));
        $email = str_replace(';', ',', trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if($nome === '' || $email === ''){
            echo json_encode(['sucesso' => false, 'mensagem' => 'Informe nome e e-mail.']);
            exit;
        }

        if($acao === 'novo'){
            $novoId = 1;
            foreach($usuarios as $u){
                if((int)$u[0] >= $novoId){
                    $novoId = (int)$u[0] + 1;
                }
            }
            $usuarios[] = [$novoId, $nome, $email, password_hash($senha, PASSWORD_DEFAULT)];
            salvarUsuarios($arqUsuarios, $usuarios);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário criado!']);
            exit;
        } else {
            foreach($usuarios as $i => $u){
                if((int)$u[0] === $id){
                    $usuarios[$i][1] = $nome;
                    $usuarios[$i][2] = $email;
                    if($senha !== ''){
                        $usuarios[$i][3] = password_hash($senha, PASSWORD_DEFAULT);
                    }
                }
            }
            salvarUsuarios($arqUsuarios, $usuarios);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário atualizado!']);
            exit;
        }
    } elseif($acao === 'excluir'){
        $usuarios = array_values(array_filter($usuarios, fn($u) => (int)$u[0] !== $id));
        salvarUsuarios($arqUsuarios, $usuarios);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído!']);
        exit;
    }
}
?>