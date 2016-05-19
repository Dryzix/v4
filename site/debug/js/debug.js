$(document).on('ready', function(){

    $body = $('body');
    $fileinfos = $('#fileinfos');

    $('#resetBtn').on('click', function(){
        reset();
        $.get('clear.php');
    });

    $.get('__debug.json')
        .done(function(data){
            $.get('lastfile.txt')
                .done(function(data){
                    $.getJSON('list.php')
                        .done(function(files){
                            load(data.debugPath, files, data);
                        });
                })
                .fail(function(){
                    $.getJSON('list.php')
                        .done(function(files){
                            load(data.debugPath, files);
                      });
               });
        })
        .fail(function(){
            alert('ALERT MISSING __debug.json');
        });

    $('ul.files').on('click', 'li a', function(e){
        e.preventDefault();
        reset();
        var url = $(this).attr('href');
        $.get('lastfile.php?name='+ url);
        $.getJSON('list.php')
            .done(function(files){
                load('', files, url);
            });
    });


    function load(debugPath, files, toLoad){
        for(var id in files){
            var file = files[id];
            if(file == toLoad || (id == 0 && toLoad === undefined)){
                $.get(file).done(function(data){
                    var re = /(.*)\.json$/;
                    var filename = $(this)[0].url.replace(re, '$1');
                    set(filename, data);
                });
            }else{
                $.get(file).done(function(data){
                    var re = /(.*)\.json$/;
                    var re2 = /\[\[_\]\]/g;
                    var filename = $(this)[0].url.replace(re, '$1');
                    filename = filename.replace(re, '$1').replace(re2, '/');
                    $('ul.files').append('<li class="list-group-item"><a href="' +  $(this)[0].url + '">' + filename + '</a></li>');
                });
            }
        }
    }

    function reset(){
        $fileinfos.find('.filename').html('');
        $fileinfos.find('.callable').html('');
        $fileinfos.find('.time').html('');
        $fileinfos.find('.tree pre').html('');
        $fileinfos.find('.sql div').html('');
        $('ul.files').html('');
    }

    function htmlEntities(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function set(filename, json){
        if($fileinfos.attr('data-displayed') == "0"){
            var re = /\[\[_\]\]/g;
            var subst = '/';
            filename = filename.replace(re, subst);
            $fileinfos.find('.filename').text(filename);

        }
        if(json.tree != undefined){
            $fileinfos.find('.tree pre').text(JSON.stringify(json.tree, null, 2));
        }
        if(json.callable != undefined){
            $fileinfos.find('.callable').text(json.callable);
        }
        if(json.time != undefined){
            $fileinfos.find('.time').text(json.time);
        }
        if(json.sql != undefined){
            for(var sql in json.sql){
                var request = json.sql[sql];
                $fileinfos.find('.sql div').append(
                    '<pre><code class="language-sql" id="req_' + sql + '">' + htmlEntities(json.sql[sql])+ '</code></pre>'
                );
                console.log($('#req_' + sql));
                Prism.highlightElement(document.getElementById('req_'+sql));
            }
        }
    }


});