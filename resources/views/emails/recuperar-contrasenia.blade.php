<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        * {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            font-family: "Montserrat", sans-serif;
        }

        .page {
            text-align: center;
        }

        .image-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1em;
        }


        .wrapper {
            height: 10vh;
            padding: 0 1em;
            max-width: 50em;
            display: flex;
            align-items: center;
            text-align: center;
            background-color: rgba(33, 37, 41, 1) !important;
            border-radius: 2px;
        }

        .wrapper h4 {
            color: #fff;
            font-weight: 500;
            text-align: center;
            font-size: 1.3rem;
        }

        .wrapper-dark {
            margin: 0 auto 1em auto;
            display: flex;
            justify-content: center;
        }

        .email-content {
            padding: 1.3rem;
            max-width: 50em;
            text-align: left!important;
            margin: 0 auto 1em auto;

        }
       
    </style>
</head>

<body>
    <div class="page">
        <div class="image-container">
          <img src="https://i.imgur.com/GMDd8YU.png" alt="img" />
        </div>

        <div class="wrapper wrapper-dark">
            <h4>CONTRASEÑA REESTABLECIDA CORRECTAMENTE</h4>
        </div>

        <div class="email-content">
            <p>Hola <Nombre del usuario>.<p>
                        <br>
                    <p>El restablecimiento de su contraseña en nuestro
                        sistema se ha realizado correctamente.</p>
                    <br>
                    <p> A continuación, se mostrará tu nueva contraseña de
                        inicio de sesión:</p>
                    <br>
                    <p>Nueva contraseña: User123@@</p>
                    <br>
                    <p>Te recomendamos guardar esta información de
                        forma segura y confdencial.</p>
                    <br>
                    <p>Gracias.</p>

        </div>

    </div>
</body>

</html>
