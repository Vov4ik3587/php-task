<?php
$cat_id = $_GET["cat_id"];
$id = $_GET["id"];
if (is_null($id) && is_null($cat_id)) {
?>
    <h1>Каталог</h1>
    <div class="list-cards">
        <?php
        $stmt = $pdo->query('
            SELECT name_section, id_section,
            (SELECT COUNT(*) FROM section_good WHERE sections.id_section = section_good.id_section) as count
            FROM sections
            ORDER BY count DESC
        ');
        while ($row = $stmt->fetch())
        {
            if ($row['count'] != 0) { ?>

            <div class="card">
                    <div class="big-number"><?php echo $row['count']?></div>
                    <div class="caption">
                        <a href="?cat_id=<?php echo $row['id_section']?>">
                            <?php echo $row['name_section'] ?>
                        </a>
                    </div>
            </div>

            <?php }
        } ?>
    </div>
<?php } elseif ($cat_id) {
    $stmt = $pdo->prepare('SELECT sections.name_section FROM sections WHERE id_section=?');
    $stmt->execute(array($cat_id));
    $row = $stmt->fetch(); ?>

    <h1><?php echo $row['name_section'];?></h1>

    <?php $stmt = $pdo->prepare('
        SELECT name_good, picture.path_picture
        FROM goods
        JOIN picture
        ON goods.id_main_picture = picture.id_picture
        WHERE goods.availability = true
        AND goods.id_main_section=?
    ');
    $stmt->execute(array($cat_id))?>
    <div class="list-cards">
    <?php

    while ($row = $stmt->fetch())
    { ?>
        <div class="card">
            <div class="big-number">
            </div>
            <div class="caption">
                <?php echo $row['name_good'] ?>
            </div>
        </div>
    <?php } ?>
</div>
<?php } ?>