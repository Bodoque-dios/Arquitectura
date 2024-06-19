<?php
?>
<html>
<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .centered-button {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form action="reset.php" method="post" class="centered-button">
        <input type="submit" value="RESET DB">
    </form>
</body>
</html>