<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../config/auth.php';

exigirLogin();

$id=(int)($_GET['id']??0);
$r=[
    'tutor_id'=>'',
    'nome'=>'',
    'especie'=>'',
    'raca'=>'',
    'nascimento'=>'',
    'sexo'=>'',
    'observacoes'=>'',
    'ativo'=>1
];

$erro='';

if($id){
    $s=$pdo->prepare("SELECT * FROM animais WHERE id=?");
    $s->execute([$id]);
    $animal=$s->fetch();

    if(!$animal){
        header('Location: index.php');
        exit;
    }

    $r=$animal;
}

$tutores=$pdo->query("SELECT id,nome FROM tutores WHERE ativo=1 ORDER BY nome")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $r=[
        'tutor_id'=>(int)($_POST['tutor_id']??0),
        'nome'=>trim($_POST['nome']??''),
        'especie'=>trim($_POST['especie']??''),
        'raca'=>trim($_POST['raca']??''),
        'nascimento'=>$_POST['nascimento']??'',
        'sexo'=>$_POST['sexo']??'',
        'observacoes'=>trim($_POST['observacoes']??''),
        'ativo'=>(int)($_POST['ativo']??1)
    ];

    if($r['tutor_id']<=0){
        $erro='Selecione um tutor válido.';
    }elseif($r['nome']===''){
        $erro='Informe o nome do animal.';
    }elseif($r['especie']===''){
        $erro='Informe a espécie do animal.';
    }elseif(!in_array($r['sexo'],['','Macho','Fêmea'],true)){
        $erro='Selecione um sexo válido.';
    }elseif($r['nascimento']!==''&&!DateTime::createFromFormat('!Y-m-d',$r['nascimento'])){
        $erro='Informe uma data de nascimento válida.';
    }else{
        $s=$pdo->prepare("SELECT id FROM tutores WHERE id=? AND ativo=1");
        $s->execute([$r['tutor_id']]);

        if(!$s->fetch()){
            $erro='O tutor selecionado não existe ou está inativo.';
        }else{
            $dados=[
                $r['tutor_id'],
                $r['nome'],
                $r['especie'],
                $r['raca'],
                $r['nascimento']?:null,
                $r['sexo'],
                $r['observacoes']
            ];

            if($id){
                $s=$pdo->prepare("UPDATE animais SET tutor_id=?,nome=?,especie=?,raca=?,nascimento=?,sexo=?,observacoes=?,ativo=? WHERE id=?");
                $s->execute([...$dados,$r['ativo'],$id]);
            }else{
                $s=$pdo->prepare("INSERT INTO animais(tutor_id,nome,especie,raca,nascimento,sexo,observacoes) VALUES(?,?,?,?,?,?,?)");
                $s->execute($dados);
            }

            header('Location: index.php');
            exit;
        }
    }
}

$titulo=$id?'Editar animal':'Novo animal';

include __DIR__.'/../includes/header.php';
?>

<h1><?=htmlspecialchars($titulo)?></h1>

<section class="panel">

<?php if($erro):?>
<div class="alert"><?=htmlspecialchars($erro)?></div>
<?php endif;?>

<form method="post">

<div class="field">
<label>Tutor</label>
<select name="tutor_id" required>
<option value="">Selecione</option>
<?php foreach($tutores as $t):?>
<option value="<?=$t['id']?>" <?=$r['tutor_id']==$t['id']?'selected':''?>>
<?=htmlspecialchars($t['nome'])?>
</option>
<?php endforeach;?>
</select>
</div>

<div class="field">
<label>Nome *</label>
<input name="nome" value="<?=htmlspecialchars($r['nome'])?>" required>
</div>

<div class="field">
<label>Espécie *</label>
<input name="especie" value="<?=htmlspecialchars($r['especie'])?>" required>
</div>

<div class="field">
<label>Raça</label>
<input name="raca" value="<?=htmlspecialchars($r['raca'])?>">
</div>

<div class="field">
<label>Data de nascimento</label>
<input type="date" name="nascimento" value="<?=htmlspecialchars($r['nascimento']??'')?>">
</div>

<div class="field">
<label>Sexo</label>
<select name="sexo">
<option value="">Selecione</option>
<option value="Macho" <?=$r['sexo']==='Macho'?'selected':''?>>Macho</option>
<option value="Fêmea" <?=$r['sexo']==='Fêmea'?'selected':''?>>Fêmea</option>
</select>
</div>

<div class="field">
<label>Observações</label>
<textarea name="observacoes"><?=htmlspecialchars($r['observacoes'])?></textarea>
</div>

<?php if($id):?>
<div class="field">
<label>Situação</label>
<select name="ativo">
<option value="1" <?=$r['ativo']?'selected':''?>>Ativo</option>
<option value="0" <?=!$r['ativo']?'selected':''?>>Inativo</option>
</select>
</div>
<?php endif;?>

<button type="submit">Salvar</button>
<a class="btn secondary" href="index.php">Cancelar</a>

</form>
</section>

<?php include __DIR__.'/../includes/footer.php';?>
