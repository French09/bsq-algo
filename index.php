<?php
function parsed($string)
{
    $prev = array(
        '(' => 1,
        // '2' => 1,
        '+' => 2,
        '-' => 2,
        '*' => 3,
        '/' => 3,
        '%' => 3,
    );

    // On créer trois tableau vide
    $tokenArr = [];
    $exit = [];
    $symboleArr = [];

    // Première boucle for pour formatter  à ma  guise l'expression
    // le but de cette  boucle est de prendre notre expression/string est de mettre chaque nombre/chiffre, opérateur dans un tableau
    for ($i = 0; isset($string[$i]); $i++) 
    {
        $result = "";
        if (preg_match("/\d+/", $string[$i])) // Si notre parametre contient un nombre/chiffre
        {
            $result .= $string[$i]; // Si le parametre détecté est un nombre/chiffre on l'ajoute à $result

            // Tant que le prochain élément analisé est un nombre/chiffre et qu'il est != NULL
            while (isset($string[$i + 1]) && preg_match("/[0-9.]+/", $string[$i + 1])) 
            {
                $result .= $string[++$i];
            }
            array_push($tokenArr, $result);  // Une fois que la boucle while à fini son bail, on prend $result(44,4,2,2) et on le stock dans le tableau $tokenArr
        } 
        else // Si notre parametre n'est  pas  un  nombre/chiffre
        {
            array_push($tokenArr, $string[$i]); // On stock notre "opérateur" dans un tableau: $tokenArr
        }
    }
    // var_dump($tokenArr);
    // print_r($result);

    // Deuxième boucle for pour "jouer" cette fois-ci avec notre tableau: $tokenArr *décommente la ligne 40  pour  voir a quoi ressemble $tokenArr
    // On va  trier  ce  qu'il  y  a  dans  notre  tableau $tokenArr
    for ($i = 0; $i < count($tokenArr); $i++) // On va counter le nombre d'élement dans notre tableau $tokenArr
    {
        if (preg_match("/\d+/", $tokenArr[$i])) // Si dans  notre tableau($tokenArr), on a un nombre/chiffre
        {
            array_push($exit, $tokenArr[$i]); // Alors on push ce nombre/chiffre dans notre  tableau: $exit
        } 
        else  // Si on a pas de nombre/chiffre ca veut dire  que c'est soit une '(' soit une ')'
        {
            if ($tokenArr[$i] == "(") // Si on a une '('  alors ...
            {
                array_push($symboleArr, $tokenArr[$i]);  // On push notre '(' dans notre tableau $symboleArr
            } 
            elseif ($tokenArr[$i] == ")") // Sinon si notre caractère c'est un ')' 
            {
                // var_dump($exit);
                //  On cherchera dans notre array $symboleArr le caractère ')'
                while (end($symboleArr) !== "(") // Tant qu'on a pas ')' ...
                {
                    $exit[] = array_pop($symboleArr); // on retire le dernier signe
                }
                array_pop($symboleArr); 
            } 
            else // Sinon c'est un caractère inconnu      
            {
                while (!empty($symboleArr) && $prev[$tokenArr[$i]] <= $prev[end($symboleArr)]) 
                {
                    $exit[] = array_pop($symboleArr); // On  enleve le dernier elements de symboleArr dans la exit[]
                }
                array_push($symboleArr, $tokenArr[$i]); // on  ajoute le tableauy tokenArr a notre tableau symboleArr
                // var_dump($symboleArr);
            }
        }
    }

    while (!empty($symboleArr)) 
    {
        $exit[] =  array_pop($symboleArr);
        // var_dump($exit);
    }

    return implode(" ", $exit); // On prend notre tableau puis on le met sous forme de string
}


// fonction qui va calculer la string
function calculate($value, $op1, $op2)
{
    switch ($value) 
    {
        case '+':
            $result = $op1 + $op2;
            break;
        case '-':
            $result = $op2 - $op1;
            break;
        case '*':
            $result = $op1 * $op2;
            break;
        case '/':
            $result = $op2 / $op1;
            break;
        case '%':
            $result = $op1 % $op2;
            break;
        default:
            break;
    }
    return $result;
}

// fonction qui va evaluer la string
function eval_expr($string)
{
    $string = parsed($string); // On va analiser notre expression  en utilisant la  fonction  créée:  "parsed" voir  l.02
    $result = 0;
    $stack = [];
    $elements = explode(" ", $string);  // fonction qui va exploser  notre  string  apres  analise

    // print_r($elements);  // Décomment si  tu veux voir a  quoi ressemble notre expression après analyse

    foreach ($elements as $value) 
    {
        if (preg_match('/[-+\/*%]/', $value)) // Si on détecte les caractères  suivant: "-, +, /,  *,  %" dans  notre variable $value (qui est une  clé de notre tableau) alors...
        {
            $op1 = array_pop($stack); // On enlève le dernier opérateur, /!\ il faut impérativement décommenté  la  ligne 120
            $op2 = array_pop($stack); // On enlève le dernier opérateur, /!\ il faut impérativement décommenté  la  ligne 120
             
            $result = calculate($value, $op1, $op2); // fonction qui va effectuer les opérations  
            array_push($stack, $result);  // On push le résultat dans le tableau $stack
        } 
        else // Si on ne détecte pas "-, +, /,  *,  %" dans notre notre variable $value(qui est une  clé de notre tableau)
        {
            array_push($stack, $value);  // On push le résultat dans le tableau $stack
        }

        // Je te laisse décommenté les deux ligne en dessous si tu es curieuse
        // var_dump($stack);
        // print($value);
    }

    return $result;
}

 eval_expr("44*(4*(2+2))").PHP_EOL;
// echo eval_expr("4-1").PHP_EOL;
// echo eval_expr("(2+3(4-158-7)+2)5").PHP_EOL;
// echo eval_expr("(2+3-123+2)5").PHP_EOL;
// echo eval_expr("(-1+1)").PHP_EOL;
// echo eval_expr("12+-4").PHP_EOL;
// echo eval_expr("2+35").PHP_EOL;
// echo eval_expr("(-2600(365+-2)12-4)/-30").PHP_EOL;
// echo eval_expr("365+-2").PHP_EOL;
// echo eval_expr("365-2").PHP_EOL;
// echo eval_expr("3-(5*-2)").PHP_EOL;
// echo eval_expr("5*-2").PHP_EOL;