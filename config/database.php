<?php

if (RUN !== true)
    throw new Exception("Страница не существует", 404);

return 
[
    'host' => 'localhost',
    'dbname' => 'j62432737_bxstats',
    'username' => 'root',
    'password' => ''
];