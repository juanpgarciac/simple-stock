<?php 

please()->registerCommand('go','say',fn($something) => "\n". ($something ?? 'nothing') ."\n");

return [

];