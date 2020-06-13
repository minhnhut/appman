<?php
    $icon = $icon ?? 0;
    $size = 48;
    $x = ($icon % 3) * 48;
    $y = floor($icon / 3) * 48;
    // $x = 3*48;
    // $y = 0;
?>

<div class="app-icon" style="background-position: <?=-$x?>px <?=-$y?>px"></div>
