<script>
<?php
//JavaScriptが出力するメッセージの国際化対応
echo 'const jsMessage = {
    "Error": {
        "tagEmpty" : "' . __d('cakebooru', 'Cannot add. Tag empty.') . '",
        "tagDuplicate" : "' . __d('cakebooru', 'Cannot add. Tag duplicate.') . '",
        "tagReservedWord" : "' . __d('cakebooru', 'Cannot add. Reserved word. Reserved word:%s') . '",
        "tagDelimiterFound" : "' . __d('cakebooru', 'Cannot add. Tag delimiter found. Delimiter:[%s]') . '",
    }
}';
?>


<?php
use Cake\Core\Configure;
$reservedWords = Configure::read('TagOperatorss');
$words = '[';
foreach ($reservedWords as $v) {
    $words .= '"' . $v . '",';
}
$words .= ']';

//JavaScriptの定数を定義
echo 'const jsConsts = {
    "reservedWord": ' . $words . ',
    "tagDelimiter": "' . Configure::read('TagDelimiter') . '",
}';
?>
</script>