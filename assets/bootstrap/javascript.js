    getDuration = function() {
        var date1 = new Date($('#from_date').val()); 
        var date2 = new Date($('#to_date').val()); 
        
        // To calculate the time difference of two dates 
        var timediff = date2.getTime()-date1.getTime(); 
        // To calculate the no. of days between two dates 
        var days = timediff / (1000 * 3600 * 24); 
        if(days>=0) {
            $('#from_date').removeClass("invalid");
            $('#to_date').removeClass("invalid");
            $('#days').removeClass("invalid");
            $('#days').val(days+1+' days');
        } else {
            $('#from_date').addClass("invalid");
            $('#to_date').addClass("invalid");
            $('#days').addClass("invalid");
            $('#days').val('INVALID');
        }

    }

    onSubmit = function () {
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var days = $('#days').val();
        var type = $('#type').val();
        var reason = $('#reason').val();

        if(reason == "" || days == "INVALID") {
            $('#reason').addClass("invalid");
            if(days == "INVALID") {
                $('#days').addClass("invalid");
            }
        } else {
            $.ajax({
                url: 'models/actions/create.php',
                type: 'post',
                data: {
                    'from_date': from_date,
                    'to_date': to_date,
                    'type': type,
                    'reason': reason
                },
                success: function (result) {
                    if(result.error) {
                        $('#d_head').html(result.error);
                        $('#d_pop').addClass("alert alert-danger");
                        $('#d_body').html(result.msg+result.available);
                    } else {
                        $('#d_head').html('Success!');
                        $('#d_pop').addClass("alert alert-success");
                        $('#d_body').html("Your leave application has been submitted! Please wait for your superior's response!"+result.available);
                    }

                    $('#createNewApplication').modal('hide');
                    $('#dialog').modal('show');
                },
                error: function (msg) {
                    $('#createNewApplication').modal('hide');
                    $('#d_head').html('ERROR');
                    console.log(msg);
                    $('#d_body').html(msg.responseText);
                    // $('#d_body').html('An internal error has occurred while processing your request!');
                    $('#dialog').modal('show');
                }
            });
        }
    };

    withDraw = function (id) {
        $.ajax({
            url: 'models/actions/create.php',
            type: 'post',
            data: {
                'app_id': id,
                'status': 'WITHDRAWN'
            },
            success: function (result) {
                if(result.error) {
                    $('#d_head').html(result.error);
                    $('#d_body').html(result.msg);
                } else {
                    $('#d_head').html('Success!');
                    $('#d_body').html("Your leave application has been withdrawn! :(");
                }

                $('#createNewApplication').modal('hide');
                $('#dialog').modal('show');
            },
            error: function (msg) {
                $('#createNewApplication').modal('hide');
                $('#d_head').html('ERROR');
                $('#d_body').html('An internal error has occurred while processing your request!');
                $('#dialog').modal('show');
            }
        });
    };