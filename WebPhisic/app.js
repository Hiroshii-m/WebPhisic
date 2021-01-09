// 1. ウィンドウの上からjs-footerまでの高さを取得
// 2. windowの高さが1よりも小さければ、js-footerのスタイルをposition:fixedでしたに固定してあげる。

// window.addEventListener('DOMContentLoaded',
//     function(){
//         // footerのDOMを取得
//         var $footer = document.querySelector(".js-footer");
//         var footHeight = document.querySelector(".js-footer").offsetTop;
//         // windowの高さを取得
//         var windowHeight = window.innerHeight;
//         console.log(footHeight);
//         console.log(windowHeight);
//     }
// );

$(function(){
    var $footer = $('.js-footer');
    if($footer.offset().top + $footer.outerHeight() < window.innerHeight){
        $footer.attr({'style': 'position: fixed; top:' + (window.innerHeight - $footer.outerHeight()) + 'px;'});
    }
    
    // 画像を表示
    var $dropArea = $('.js-area-drop');
    var $fileInput = $('.js-area-input');
    $dropArea.on('dragover', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '3px #ccc dashed');
    });
    // ドラッグ離した時
    $dropArea.on('dragleave', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e){
        $dropArea.css('border', 'none');
        // 変数格納
        var file = this.files[0],
            $img = $(this).siblings('.js-prev-img'),
            fileReader = new FileReader();
        
        // 読み込み完了した際のイベントハンドラー
        fileReader.onload = function(e){
            $img.attr('src', e.target.result).show();
        };
        // 画像読み込み
        fileReader.readAsDataURL(file);

    });

});
