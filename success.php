<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="signup.css">
    <style>
        .success-message {
            text-align: center;
            margin-top: 50px;
            font-size: 24px;
            color: green;
            font-weight: bold;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            color: white;
            background-color: #4CAF50;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .action-button:hover {
            background-color: #45a049;
        }
        .action-button.secondary {
            background-color: #008CBA;
        }
        .action-button.secondary:hover {
            background-color: #007399;
        }
    </style>
</head>
<body>
    <header>
        <a href="#" class="logo">Floward</a>
    </header>
    <div class="success-message">
        Flower added successfully!
    </div>
    <div class="button-container">
        <a href="flower.php" class="action-button">Add another flower</a>
        <a href="admin_page.php" class="action-button secondary">Return to Home</a>
    </div>
</body>
</html>