<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Temporarily offline</title>
    <meta name="description" content="Temporarily offline.">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:500,700" rel="stylesheet">
    <style>
        * {
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            box-sizing: border-box;
            outline: none;
            user-select: none;
        }

        html {
            font-size: 1em;
        }

        body {
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            background-color: #fff;
            color: #000;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            line-height: 1.5;
            margin: 0;
            padding: 30px;
        }

        h1 {
            font-size: 50px;
            font-weight: 700;
            letter-spacing: -3px;
            margin: 0 0 1.5em 0;
        }

        h1 span {
            color: #322dd0;
        }

        p {
            margin: 0;
            font-weight: 500;
        }

        #app {
            align-items: center;
            background-color: #dfdfe2;
            display: flex;
            justify-content: center;
            min-height: calc(100vh - 60px);
            padding: 30px;
        }

        #message {
            position: relative;
            text-align: center;
        }

        #message:hover:after {
            border-radius: 50%;
            margin-left: -20%;
            margin-top: -60%;
        }

        #message:hover:before {
            border-radius: 50%;
            margin-top: -30%;
        }

        #message:after {
            background-color: #000;
            content: '';
            height: 380px;
            left: 50%;
            margin-left: -165px;
            margin-top: -190px;
            position: absolute;
            top: 50%;
            transform-origin: center;
            transform: rotate(45deg);
            transition: all .25s;
            width: 380px;
            will-change: transform;
            z-index: 1;
        }

        #message:before {
            background-color: #fff;
            content: '';
            height: 380px;
            left: 50%;
            margin-left: -190px;
            margin-top: -190px;
            position: absolute;
            top: 50%;
            transform-origin: center;
            transform: rotate(45deg);
            transition: all .35s;
            width: 380px;
            will-change: transform;
            z-index: 2;
        }

        #message > * {
            pointer-events: none;
            position: relative;
            z-index: 3;
        }

        #logo {
            bottom: 50px;
            left: 60px;
            line-height: 0;
            max-width: 75px;
            position: fixed;
        }

        #logo img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div id="app">
    <div id="message">
        <h1>TEMPORARILY <span>OFFLINE</span></h1>
        <p>SORRY FOR YOUR INCONVENIENCE</p>
        <p>PLEASE CHECK BACK LATER...</p>
    </div>
    <?php if (file_exists(get_stylesheet_directory() . '/assets/img/logo.png')) { ?>
        <div id="logo">
            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/logo.png'; ?>">
        </div>
    <?php } ?>
</div>
<script>
    setTimeout(function () {
        window.location.reload();
    }, 300000);
</script>
</body>
</html>
