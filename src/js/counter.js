$(document).ready(function () {

    let result = Number($('input.product__counter_result').val());

    $('button.product__counter_minus').click(function () {
        if (result > 0) {
            $('input.product__counter_result').attr('value', --result);
        }
    });

    $('button.product__counter_plus').click(function () {
        $('input.product__counter_result').attr('value', ++result);
    });

    $('button.buttons__basket').click(function () {
        if (result === 0) {
            $.notify("Сначала добавьте товар в корзину");
        } else {
            if (result === 1) {
                $.notify("В корзину добавлено " + result + " товар", "success");
            } else if (result === 2 || result === 3 || result === 4) {
                $.notify("В корзину добавлено " + result + " товара", "success");
            } else {
                $.notify("В корзину добавлено " + result + " товаров", "success");
            }
        }
    })
});