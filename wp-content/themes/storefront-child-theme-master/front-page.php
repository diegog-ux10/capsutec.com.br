<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php wp_head(); ?>
</head>
<body>
    <header>
        <nav>
            <ul>
                <a href=""><li> HOME</li></a>
            </ul>
        </nav>
    </header>
    <h1>Hola Soy <?php echo $name; ?></h1>
</body>
</html>