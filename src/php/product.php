<?php
$cat_id = $_GET["cat_id"];
$id = $_GET["id"];
$page = $_GET["pg"];

$check = $pdo->prepare('
    SELECT *
    FROM section_good
    WHERE section_good.id_section = ?
    AND section_good.id_good = ? 
');

$check->execute(array($cat_id, $id));
$ok_or_not = $check->fetch();

//var_dump($ok_or_not);

if (is_null($id) && is_null($cat_id)) { ?>
    <h1>Каталог</h1>
    <div class="list-cards">
        <?php
        $stmt = $pdo->query('
            SELECT name_section, id_section,
            (SELECT COUNT(*) 
                FROM section_good 
                JOIN goods 
                ON section_good.id_good = goods.id_good 
                WHERE sections.id_section = section_good.id_section 
                AND goods.availability = true) AS count
            FROM sections
            ORDER BY count DESC;
        ');
        while ($row = $stmt->fetch()) {
            if ($row['count'] != 0) { ?>

                <div class="card card-ctg">
                    <div class="big-number"><?php echo $row['count'] ?></div>
                    <div class="caption">
                        <a href="?cat_id=<?php echo $row['id_section'] ?>&pg=1">
                            <?php echo $row['name_section'] ?>
                        </a>
                    </div>
                </div>

            <?php }
        } ?>
    </div>
<?php } elseif ($cat_id && $page && is_null($id)) {
    $stmt = $pdo->prepare('
        SELECT s.name_section, s_parent.name_section AS parent_section,
        (SELECT COUNT(*) 
            FROM section_good 
            JOIN goods 
            ON section_good.id_good = goods.id_good 
            WHERE s.id_section = section_good.id_section 
            AND goods.availability = true) AS count
        FROM sections s
        LEFT JOIN sections s_parent
        ON s_parent.id_section = s.id_parent_section
        WHERE s.id_section=?;
        ');
    $stmt->execute(array($cat_id));
    $row = $stmt->fetch();
    $count_good = $row['count']; ?>
    <div class="header">
        <?php if (is_null($row['parent_section'])) { ?>
            <h1><?php echo $row['name_section']; ?></h1>
        <?php } else { ?>
            <h1><?php echo $row['parent_section'] . "->" . $row['name_section']; ?></h1>
        <?php } ?>
        <a href="http://localhost">
            <button class="btn-back">Назад</button>
        </a>
    </div>

    <?php $stmt = $pdo->prepare('
        SELECT goods.name_good,
               goods.id_good,
	        picture.path_picture AS path, 
            picture.attribute_alt AS alt
        FROM goods
        JOIN picture
        ON goods.id_main_picture = picture.id_picture
        JOIN section_good
        ON section_good.id_good = goods.id_good
        WHERE goods.availability = true 
        AND section_good.id_section = ?
        LIMIT ?, ?
    ');
    $stmt->execute(array($cat_id, (12 * ($page - 1)), 12 * $page));
    ?>
    <div class="list-cards">
        <?php while ($row = $stmt->fetch()) { ?>
            <div class="card card-good">
                <div class="big-number">
                    <img src="<?php echo $row['path'] ?>" alt="<?php echo $row['alt'] ?>">
                </div>
                <div class="caption">
                    <a href="?cat_id=<?php echo $cat_id ?>&id=<?php echo $row['id_good'] ?>">
                        <?php echo $row['name_good'] ?>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>

    <nav class="page-nav">
        <?php
        $count_page = (int)($count_good / 12) + 1;
        for ($i = 1; $i <= $count_page; $i++) { ?>
            <a class="page-nav__item" href="?cat_id=<?php echo $cat_id ?>&pg=<?php echo $i ?>">
                <?php echo $i ?>
            </a>
        <?php } ?>
    </nav>


<?php } elseif ($ok_or_not && $id && is_null($page)) {
    // Достаем информацию товара
    $stmt = $pdo->prepare('
        SELECT
        goods.name_good as name,
        goods.id_main_section,
        picture.path_picture as main_pic,
        picture.attribute_alt as main_alt,
        goods.price_discount,
        goods.price,
        goods.price_promo,
        goods.description_good as description,
        goods.availability as avail
        FROM goods
        JOIN picture
        ON goods.id_main_picture = picture.id_picture
        WHERE goods.id_good = ?
        ');
    $stmt->execute(array($id));
    $row = $stmt->fetch();

    $stmt_cat = $pdo->prepare('
        SELECT sections.name_section, section_good.id_section
        FROM sections
        JOIN section_good
        ON sections.id_section = section_good.id_section
        WHERE section_good.id_good = ?
    ');
    $stmt_cat->execute(array($id));

    $stmt_pic = $pdo->prepare('
        SELECT picture.path_picture AS path, 
               picture.attribute_alt AS alt
        FROM picture
        JOIN good_picture
        ON picture.id_picture = good_picture.id_picture
        WHERE good_picture.id_good = ?
    ');
    $stmt_pic->execute(array($id))
    ?>
    <div class="content">
        <div class="product">
            <div class="product__pictures">
                <div class="product__list-pic">
                    <?php while ($row_pic = $stmt_pic->fetch()) { ?>
                        <img class="product__mini-pic" src="<?php echo $row_pic['path'] ?>"
                             alt="<?php echo $row_pic['alt'] ?>">
                    <?php } ?>
                </div>
                <img class="product__big-pic" src="<?php echo $row['main_pic'] ?>"
                     alt="<?php echo $row['main_alt'] ?>"/>
            </div>
            <div class="product__text-info">
                <div class="header">
                    <div class="product__title"><?php echo $row['name'] ?></div>
                    <?php if (is_null($cat_id)) { ?>
                        <a href="http://localhost/?cat_id=<?php echo $row['id_main_section'] ?>&pg=1">
                            <button class="btn-back">Назад</button>
                        </a>
                    <?php } else { ?>
                        <a href="http://localhost/?cat_id=<?php echo $cat_id ?>&pg=1">
                            <button class="btn-back">Назад</button>
                        </a>
                    <?php } ?>
                </div>
                <div class="category">
                    <?php while ($category = $stmt_cat->fetch()) { ?>
                        <a href="?cat_id=<?php echo $category['id_section'] ?>"
                           class="category__elem"><?php echo $category['name_section'] ?></a>
                    <?php } ?>
                </div>
                <div class="product__price">
                    <div class="product__price_del"><?php echo $row['price'] ?></div>
                    <div class="product__price_actual"><?php echo $row['price_discount'] ?></div>
                    <div class="product__price_promo"><?php echo $row['price_promo'] ?><span> — с промокодом</span>
                    </div>
                </div>
                <?php if ($row['avail'] == 1) { ?>
                    <div class="product__addition">
                        <div class="product__addition_badges">
                            ✔ <br>
                            ⛟
                        </div>
                        <div class="product__addition_caption">
                            В наличии в магазине <a href="#">Lamoda</a> <br>
                            Бесплатная доставка
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="product__addition">
                        <div class="product__addition_badges">
                            ✖
                        </div>
                        <div class="product__addition_caption">
                            Нет в наличии
                        </div>
                    </div>
                <?php } ?>
                <div class="product__counter">
                    <button class="product__counter_minus" name="result-minus"> -</button>
                    <input class="product__counter_result" type="text" name="counter_result" value="0" readonly>
                    <button class="product__counter_plus" name="result-plus"> +</button>
                </div>
                <div class="buttons">
                    <button name="in-basket" class="buttons__basket">Купить</button>
                    <button name="in-favorites" class="buttons__favorites">В избранное</button>
                </div>
                <div class="product__description"><?php echo $row['description'] ?></div>
                <div class="share">
                    <div class="share__caption">Поделиться:</div>
                    <div class="share__buttons">
                        <button name="share-vk" class="share__buttons_vk"></button>
                        <button name="share-google" class="share__buttons_google"></button>
                        <button name="share-facebook" class="share__buttons_facebook"></button>
                        <button name="share-twitter" class="share__buttons_twitter"></button>
                    </div>
                    <div class="triangle-left"></div>
                    <div class="share__number">123</div>
                </div>
            </div>
        </div>
    </div>
<?php } else {
    require_once "./src/php/404.php";
} ?>
