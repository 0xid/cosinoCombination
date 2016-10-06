<?php
include_once 'Cosino.php';
$fieldsCount = (isset($_GET['fieldsCount']) ? $_GET['fieldsCount']+0:false);
$chipCount = (isset($_GET['chipCount']) ? $_GET['chipCount']+0:false);
$result = '';

if ($fieldsCount > 0 && $chipCount > 0) {
    $cosino = new Cosino($fieldsCount, $chipCount);
    $result = $cosino->getAllCombination(5);
}
?>
<form method="GET" action="">
    <label for="fieldsCount">fieldsCount:</label>
    <input type="text" value="<?= $fieldsCount ?>" name="fieldsCount">

    <label for="chipCount">chipCount:</label>
    <input type="text" value="<?= $chipCount ?>" name="chipCount">

    <input type="submit" value="Send">
</form>
<?php echo $result == '' ? '':'Файл создан: '.$result; ?>