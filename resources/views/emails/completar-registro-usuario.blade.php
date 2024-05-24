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
        a{
            text-decoration: none;
        }

        .page {
            text-align: center;
        }

        .image-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1em;
            margin-top: 1em;
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
            text-align: left !important;
            margin: 0 auto 1em auto;

        }
          .button-group {
            margin-top: 3em;
            display: flex;
            justify-content: center;
        }

        .btn {
            font-size: 1rem;
            padding: 0.85em 1.345em;
            font-weight: bold;
            margin: auto;
            letter-spacing: 2px;
            cursor: pointer;
            max-width: 50rem;
            background-color: #339de4;
            color: #fff;
            box-shadow: 5px 5px #000
        }

        .pop-underline {
            position: relative;
            background: none;
            border: none;
            overflow: hidden;
            color: var(--lexicon);
            transition: color 600ms ease-in-out;
        }

        .pop-underline::after {
            content: "";
            display: block;
            position: relative;
            margin-top: 0.25em;
            border-bottom: 0.25em solid blue;
            opacity: 0;
            transition: all 450ms ease-in-out;
            width: 1%;

        }

        .pop-underline:hover::after {
            opacity: 1;
            width: 100%;
        }

        .pop-underline:hover {
            color: white;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="image-container">
            <img src="https://i.imgur.com/GMDd8YU.png" alt="img" />
        </div>

        <div class="wrapper wrapper-dark">
            <h4>COMPLETA TU REGISTRO</h4>
        </div>

        <div class="email-content">
            <p>Hola {{$data['nombres']}}</p>
            <br>
            <p>Para brindarle sus credenciales correspondientes al sistema, haga clic en el siguiente botón para
                confrmar su registro:</p>
            <br>
            <div class="button-group">
                <a href="http://localhost:4200/auth/registro" target="_blank""
                    class="btn top-fill">Confirmar registro</a>
            </div>
            <br>
           
            <p>Gracias.</p>
            <br>
            <p>Nota: Después de 24 horas recibido este correo, el
                botón se deshabilitará automáticamente.</p>
            <br>

        </div>

    </div>
</body>

</html>
