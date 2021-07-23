$(function () {

    $('.spinner-grow').hide();
    $('#second_admin_country').on('change', function () {
        /*const url = Routing.generate('cities', {});*/
        const $country = $(this).val();
        $.ajax({
            'url' : '/cities', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#second_admin_city').find('option:not(:first)').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#second_admin_city').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });

    });


    $('#variant_menu').on('change', function () {
        /*const url = Routing.generate('subMenus', {});*/
        const $menu = $(this).val();
        $.ajax({
            'url' : '/restaurant/ChangeSubMenus', // To change with Routing.generate later
            'data' : {
                'menu' : $menu
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#variant_subMenu').find('option').remove();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#variant_subMenu').append(new Option(p.name, p.id));
                });
            }
        });

    });

    $('#search_by_city_or_country_country').on('change', function () {
        /*const url = Routing.generate('cities', {});*/
        const $country = $(this).val();
        $.ajax({
            'url' : '/cities', // To change with Routing.generate later
            'data' : {
                'country' : $country
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#search_by_city_or_country_city').find('option').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#search_by_city_or_country_city').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });

    });
    if ($('#search_by_city_or_country_country').val()!="") {
        var $co = $('#search_by_city_or_country_country').val();
        $.ajax({
            'url' : '/cities', // To change with Routing.generate later
            'data' : {
                'country' : $co
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#search_by_city_or_country_city').find('option').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                $.each(data, function (i, p) {
                    $('#search_by_city_or_country_city').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });
    }

    if($('#product_subCategory').find('option').length == 0)
    {
        $('.subCat').hide();
    }

    $('#product_category').on('change', function () {

        const $category = $(this).val();
        $.ajax({
            'url' : '/store/subCategory', // To change with Routing.generate later
            'data' : {
                'category' : $category
            },
            'type': 'post',
            'beforeSend' : function() {
                $('#product_subCategory').find('option').remove();
                $('.spinner-border').show();
            },
            success: function (data) {
                if(data.length == 0)
                {
                    $('.subCat').hide();
                }
                else
                {
                    $('.subCat').show();
                }
                $.each(data, function (i, p) {
                    $('#product_subCategory').append(new Option(p.name, p.id));
                    $('.spinner-border').hide();
                });
            }
        });

    });



} ());