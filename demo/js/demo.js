$(document).ready(function() {
    //initDateTimeSelectors();

    $('#submit').button();


    changeEnvironment();

    $('input.date-time').AnyTime_picker({format: "%Y-%m-%d %H:%i:%s"});

    $("#shipping_address_id").on("keyup", function(event){
        if ($(this).val() == '') {
            $('#client_order_shipping_address').fadeIn();
        } else {
            $('#client_order_shipping_address').fadeOut();
        }
    });

    $("#billing_address_id").on("keyup", function(event){
        if ($(this).val() == '') {
            $('#client_order_billing_address').fadeIn();
        } else {
            $('#client_order_billing_address').fadeOut();
        }
    });

});

function changeEnvironment()
{
    $('body').css('background-repeat', 'no-repeat');
    $('body').css('background-position', 'top right');
    $('body').css('background-attachment', 'fixed');

    $('input[name="apiToken"]').hide();
    $('input[name="secretKey"]').hide();
    $('input[name="buyerId"]').hide();
    $('#productionWarning').hide();

    var selectedEnvironment = $('#environment').val();


    //alert(selectedEnvironment);
    switch (selectedEnvironment) {
        case 'sandbox':
            $('body').css('background-image', 'url(images/sandbox-banner.png)');

            $('#sandboxApiToken').show();
            $('#sandboxSecretKey').show();
            $('#sandboxBuyerId').show();

            $('#submit').val('Submit to Sandbox');
            break;

        case 'staging':
            $('body').css('background-image', 'url(images/staging-banner.png)');

            $('#stagingApiToken').show();
            $('#stagingSecretKey').show();
            $('#stagingBuyerId').show();

            $('#submit').val('Submit to Staging');
            break;

        case 'production':
            $('body').css('background-image', 'url(images/production-banner.png)');

            $('#productionApiToken').show();
            $('#productionSecretKey').show();
            $('#productionBuyerId').show();

            $('#submit').val('Submit to Production');

            $('#productionWarning').show();
            break;
    }
}


function toggleOptions()
{
    //hideAllOptions();

    var selectedMethod = $('#apiMethod').val();
    //alert(selectedMethod);

    if (selectedMethod.indexOf("list") >= 0 || selectedMethod.indexOf("search") >= 0) {
        $('#listParameters').fadeIn();
    } else {
        $('#listParameters').fadeOut();
    }

    $('#methodInput').show();
    $('#submit').show();

    $('#methodInput div').fadeOut();
    $('.' + selectedMethod).fadeIn();

    if (selectedMethod == 'createShipment') {
        $('#APItest').attr('method', 'POST');
    } else {
        $('#APItest').attr('method', 'GET');
    }
}


function checkForm()
{
    // Set any hidden inputs to inactive to keep them from being submitted.
    // Keeps the URL cleaner
    $('input:hidden', 'fieldset').attr('disabled', true);
    $('select:hidden', 'fieldset').attr('disabled', true);

    //$('#environmentAndCredentials input:hidden').attr('disabled', true);
    return true;
}

