<?php
// старт сессии
session_start();

// подключаем бд
$host = '127.0.0.1';
$db = 'clothes_shop';
$user = 'root';
$password = '';
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $password, $opt);
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="src/css/feedback.css" rel="stylesheet">
    <title>Feedback</title>
</head>
<body>

<main class="content">
    <div class="block-form">
        <div class="block-form__title">Форма обратной связи</div>
        <form class="block-form__form" method="GET" action="feedback.php">
            <div class="block-form__name">
                <label>Имя:<br>
                <input type="text" name="first-name" placeholder="Введите имя..."
                       value="<?php echo $_COOKIE['name'] ?? ""?>" required>
                </label>
            </div>
            <div class="block-form__email">
                <label>Email:<br>
                    <input type="email" name="email" placeholder="Введите email..."
                           value="<?php echo $_COOKIE['email'] ?? ""?>" required>
                </label>
            </div>
            <div class="block-form__date">
                <label>Дата рождения:<br>
                    <input type="date" name="date" required  value="<?php echo $_COOKIE['date'] ?? ""?>">
                </label>
            </div>
            <div class="block-form__sex">
                <label>Пол:<br>
                    <?php if ($_COOKIE['sex'] == "man") { ?>
                        <label>
                            <input type="radio" name="sex" value="man" required checked>
                            Мужчина
                        </label>
                        <label>
                            <input type="radio" name="sex" value="woman" required>
                            Женщина
                        </label>
                    <?php } elseif ($_COOKIE['sex'] == "woman") {?>
                        <label>
                            <input type="radio" name="sex" value="man" required >
                            Мужчина
                        </label>
                        <label>
                            <input type="radio" name="sex" value="woman" required checked>
                            Женщина
                        </label>
                    <?php } else { ?>
                        <label>
                            <input type="radio" name="sex" value="man" required >
                            Мужчина
                        </label>
                        <label>
                            <input type="radio" name="sex" value="woman" required >
                            Женщина
                        </label>
                    <?php } ?>
                </label>
            </div>
            <div class="block-form__theme-appeal">
                <label>Тема обращения:<br>
                    <input type="text" size="30" maxlength="50" name="theme-appeal"
                           placeholder="Введите тему вашего обращения" required>
                </label>
            </div>
            <div class="block-from__text-appeal">
                <label>Текст обращения:<br>
                    <textarea name="text-appeal">
                    </textarea>
                </label>
            </div>
            <div class="block-form__confirm-rules">
                <label>
                    <input type="checkbox" name="confirm-rules" required>
                    С правилами ознакомился и согласен
                </label>
            </div>
            <div class="block-form__buttons">
                <input type="submit" value="Отправить">
                <input type="reset" value="Очистить">
            </div>
        </form>
    </div>
</main>
</body>
</html>

<?php
$name = $_GET['first-name'] ?? null;
$email = $_GET['email'] ?? null;
$date = $_GET['date'] ?? null;
$sex = $_GET['sex'] ?? null;
$theme = $_GET['theme-appeal'] ?? null;
$text = $_GET['text-appeal'] ?? null;

setcookie("name", $name);
setcookie("email", $email);
setcookie("date", $date);
setcookie("sex", $sex);

$stmt = $pdo->prepare('
    INSERT INTO clothes_shop.feedback (name, email, date, sex, theme, text)
    VALUES (?, ?, ?, ?, ?, ?)
');
$stmt->execute([$name, $email, $date, $sex, $theme, $text]);

header("Location: http://localhost/");
?>