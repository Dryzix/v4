$(document).on('ready', function(){

    $('form[data-task="1"]').on('submit', function(e){
        $form = $(this);
        e.preventDefault();

        $.post($(this).attr('action'), $(this).serialize())
            .done(function(data){
                var instructions = $.parseJSON(data);

                for(var i in instructions)
                {
                    readResponse(instructions[i]);
                }
            })
            .fail(function(jqXHR){
                var instructions = $.parseJSON(jqXHR.responseText);

                for(var i in instructions)
                {
                    readResponse(instructions[i]);

                }
            });
    });

    function readResponse(response){
        if(response.html != undefined){
            $(response.setTo).html(response.html);
        }
        else if(response.text != undefined){
            $(response.setTo).text(response.text);
        }
        else if(response.class != undefined){
            if(response.addTo != undefined)
            {
                $(response.addTo).addClass(response.class);
            }
            else if(response.removeTo != undefined)
            {
                $(response.removeTo).removeClass(response.class);
            }
            else
            {
                $(response.setTo).removeClass();
                $(response.setTo).addClass(response.class);
            }
        }
        else if(response.callback != undefined)
        {
            window[response.callback](response.args);
        }
        else if(response.emptyForm != undefined)
        {
            $(':input', $form)
                .not(':button, :submit, :reset, :hidden, [name="token"]')
                .val('')
                .removeAttr('checked')
                .removeAttr('selected');
        }
    }

    window.test = function test(){
        $('body').css('background', '#ECECEC');
    }
});