<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

return [
    'CacheMatch' => [
        // if
        'IF' => '#\{\s{0,}if.*code=\`(.*?)\`\s{0,}\}#',
        'IfElse' => '#\{\s{0,}(else if|elseif|elif).*code=\`(.*?)\`\s{0,}\}#',
        'Else' => '#\{\s{0,}else\s{0,}\}#',
        'EndIf' => '#\{\s{0,}if\s{0,}\}#',

        'ECHO' => '#\{\s{0,}\$([a-zA-Z_\x7f-\xff][a-zA-Z\[\]\'\'""0-9_\x7f-\xff]*)\s{0,}\}#',
        // function
        'FUNC' => '#\{\:([a-zA-Z_\x7f-\xff][a-zA-Z\[\]\'\'""0-9_\x7f-\xff]*)\((.*?)\)\s{0,}\}#',
        // foreach
        'LoopEachForeach' => '#\{\s{0,}(loop|foreach|each) \$(.*?)\s{0,}\}#',
        'LoopEachForeachEnd' => '#\{\s{0,}(loop|foreach|each)\s{0,}\}#',

        'Notes' => '/\{(\#|\*)(.*?)(\#|\*)\}/',
        'PHPTag' => '/\{\#\s{0,}(.*?)\s{0,}\#\}/',
        // map
        'Map' => '#\$([a-zA-Z_\x7f-\xff][a-zA-Z\[\]\'\'""0-9_\x7f-\xff]*)\.([a-zA-Z_\x7f-\xff][a-zA-Z\[\]\'\'""0-9_\x7f-\xff]*)#',
    ],
    'CacheReplace' => [
        // if
        'IF' => '<?php if (${1}) { ?>',
        'IfElse' => '<?php } else if (\\2) { ?>',
        'Else' => '<?php } else { ?>',
        'EndIf' => '<?php } ?>',

        'ECHO' => '<?php echo $\\1; ?>',
        // function
        'FUNC' => '<?php echo \\1(\\2); ?>',
        // foreach
        'LoopEachForeach' => '<?php foreach($\\2) { ?>',
        'LoopEachForeachEnd' => '<?php } ?>',

        'Notes' => '',
        'PHPTag' => '<?php \\1 ?>',
        // map
        'Map' => "$\\1['\\2']"
    ],
    'CacheTemplate' => '#\{@.*?\}#',
    'CacheTemplate1' => '#\{@(.*?)\}#',
];