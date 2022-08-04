<?php
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
                <input type="submit" name="submit" value="Отправить">
                <input type="reset" value="Очистить">
            </div>
        </form>
    </div>
</main>
</body>
</html>

<?php
$name = $_GET['first-name'];
$email = $_GET['email'];
$date = $_GET['date'];
$sex = $_GET['sex'];
$theme = $_GET['theme-appeal'];
$text = $_GET['text-appeal'];

setcookie("name", $name);
setcookie("email", $email);
setcookie("date", $date);
setcookie("sex", $sex);

$valid_form = true;

if (isset($_GET['submit'])) {
    if (!isset($name) || !preg_match('/^[a-zA-Zа-яА-Я]+$/ui',$name)) {
        echo "Неправильно введено имя <br>";
        $valid_form = false;
    }

    if(!isset($email) || !preg_match('/\w+@\w+\.\w+/', $email)) {
        echo "Неправильно введен email(введите в формате user@email.domen) <br>";
        $valid_form = false;
    }
    // дата должна быть между 1900-01-01 и 2099-12-31, но не работает
//    if(!isset($date) || !preg_match('/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/', $date)) {
//        echo "Такой даты не может быть <br>";
//        $valid_form = false;
//    }

    if(!isset($theme) || !preg_match('/[a-zA-Zа-яА-Я0-9 ]{2,49}/', $theme)) {
        echo "Введены недопустимые символы в теме обращения <br>";
        $valid_form = false;
    }

    if(!isset($text) || !preg_match('/[a-zA-Zа-яА-Я0-9 ]{2,1024}/', $text)) {
        echo "Введены недопустимые символы в тексте обращения <br>";
        $valid_form = false;
    }

    if ($valid_form) {
        $stmt = $pdo->prepare('
            INSERT INTO clothes_shop.feedback (name, email, date, sex, theme, text)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$name, $email, $date, $sex, $theme, $text]);

        header("Location: http://localhost/");
    } else {
        echo "Форма не валидна";
    }
}
