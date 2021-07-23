$(function () {

        //<editor-fold desc="City Registration">
        $('#customer_registration_country').on('change', function () {
                /*const url = Routing.generate('cities', {});*/
                const $country = $(this).val();
                $.ajax({
                        'url' : '/cities', // To change with Routing.generate later
                        'data' : {
                                'country' : $country
                        },
                        'type': 'post',
                        'beforeSend' : function() {
                                $('#customer_registration_city').find('option:not(:first)').remove();
                                $('.spinner-border').show();
                        },
                        success: function (data) {
                                $.each(data, function (i, p) {
                                        $('#customer_registration_city').append(new Option(p.name, p.id));
                                        $('.spinner-border').hide();
                                });
                        }
                });

        });

        $('#restau_registration_country').on('change', function () {
                /*const url = Routing.generate('cities', {});*/
                const $country = $(this).val();
                $.ajax({
                        'url' : '/cities', // To change with Routing.generate later
                        'data' : {
                                'country' : $country
                        },
                        'type': 'post',
                        'beforeSend' : function() {
                                $('#restau_registration_city').find('option:not(:first)').remove();
                                $('.spinner-border').show();
                        },
                        success: function (data) {
                                $.each(data, function (i, p) {
                                        $('#restau_registration_city').append(new Option(p.name, p.id));
                                        $('.spinner-border').hide();
                                });
                        }
                });

        });

        $('#chef_registration_country').on('change', function () {
                /*const url = Routing.generate('cities', {});*/
                const $country = $(this).val();
                $.ajax({
                        'url' : '/cities', // To change with Routing.generate later
                        'data' : {
                                'country' : $country
                        },
                        'type': 'post',
                        'beforeSend' : function() {
                                $('#chef_registration_city').find('option:not(:first)').remove();
                                $('.spinner-border').show();
                        },
                        success: function (data) {
                                $.each(data, function (i, p) {
                                        $('#chef_registration_city').append(new Option(p.name, p.id));
                                        $('.spinner-border').hide();
                                });
                        }
                });

        });

        $('#driver_registration_country').on('change', function () {
                /*const url = Routing.generate('cities', {});*/
                const $country = $(this).val();
                $.ajax({
                        'url' : '/cities', // To change with Routing.generate later
                        'data' : {
                                'country' : $country
                        },
                        'type': 'post',
                        'beforeSend' : function() {
                                $('#driver_registration_city').find('option:not(:first)').remove();
                                $('.spinner-border').show();
                        },
                        success: function (data) {
                                $.each(data, function (i, p) {
                                        $('#driver_registration_city').append(new Option(p.name, p.id));
                                        $('.spinner-border').hide();
                                });
                        }
                });

        });
        //</editor-fold>

        //<editor-fold desc="Search by city country">
        $('.spinner-grow').hide();
        $('#search_by_city_country').on('change', function () {
                /*const url = Routing.generate('cities', {});*/
                const $country = $(this).val();
                $.ajax({
                        'url' : '/cities', // To change with Routing.generate later
                        'data' : {
                                'country' : $country
                        },
                        'type': 'post',
                        'beforeSend' : function() {
                                $('#search_by_city_city').find('option').remove();
                                $('.spinner-grow').show();
                        },
                        success: function (data) {
                                $.each(data, function (i, p) {
                                        $('#search_by_city_city').append(new Option(p.name, p.id));
                                        $('.spinner-grow').hide();
                                });
                        }
                });

        });
        //</editor-fold>
        if ($('#search_by_city_country').val()!= "") {
                console.log("ok");
                var  $co = $('#search_by_city_country').val();
                $.ajax({
                        'url' : '/cities', // To change with Routing.generate later
                        'data' : {
                                'country' : $co
                        },
                        'type': 'post',
                        'beforeSend' : function() {
                                $('#search_by_city_city').find('option:not(:first)').remove();
                                $('.spinner-grow').show();
                        },
                        success: function (data) {
                                $.each(data, function (i, p) {
                                        $('#search_by_city_city').append(new Option(p.name, p.id));
                                        $('.spinner-grow').hide();
                                });
                        }
                });
        }

} ());

