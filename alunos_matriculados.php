<?php
require '../mongo/mongo.php';

$connection = new Connection();
$manager = $connection->getConnection();

$alunosQuery = new MongoDB\Driver\Query([]);
$alunosCursor = $manager->executeQuery("guris.estudantes", $alunosQuery);
$alunos = iterator_to_array($alunosCursor);

$cursosQuery = new MongoDB\Driver\Query([]);
$cursosCursor = $manager->executeQuery("guris.cursos", $cursosQuery);
$cursos = iterator_to_array($cursosCursor);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $curso_id = $_POST['curso_id'];

    // Adicionar o aluno ao curso
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->update(
        ['_id' => new MongoDB\BSON\ObjectId($curso_id)],
        ['$addToSet' => ['estudantes_matriculados' => new MongoDB\BSON\ObjectId($aluno_id)]]
    );
    
    $manager->executeBulkWrite('guris.cursos', $bulk);
    echo "Aluno matriculado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Matrícula</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Matricular Aluno em Curso</h2>
        <form action="alunos_matriculados.php" method="POST" class="border p-4 shadow-sm rounded">
            <div class="form-group">
                <label for="aluno_id">Selecione o Aluno:</label>
                <select name="aluno_id" id="aluno_id" class="form-control" required>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?php echo (string)$aluno->_id; ?>"><?php echo $aluno->nome; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="curso_id">Selecione o Curso:</label>
                <select name="curso_id" id="curso_id" class="form-control" required>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo (string)$curso->_id; ?>"><?php echo $curso->nome_curso; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Matricular</button>
        </form>

        <h2 class="text-center my-5">Lista de Alunos Matriculados</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th>Alunos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                    <tr>
                        <td><?php echo $curso->nome_curso; ?></td>
                        <td>
                            <?php
                            if (isset($curso->estudantes_matriculados) && !empty($curso->estudantes_matriculados)) {
                                foreach ($curso->estudantes_matriculados as $aluno_id) {
                                    foreach ($alunos as $aluno) {
                                        if ((string)$aluno->_id === (string)$aluno_id) {
                                            echo $aluno->nome . "<br>";
                                            break;
                                        }
                                    }
                                }
                            } else {
                                echo "Nenhum aluno matriculado";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="cadastro_curso.php" class="btn btn-primary">Cadastrar cursos</a>
        <br>
        <a href="cadastro_estudante.php" class="btn btn-primary">Cadastrar estudantes</a>
        <br>
        <a href="alunos_matriculados.php" class="btn btn-primary">Matricular estudantes</a>
    </div>
</body>
</html>
