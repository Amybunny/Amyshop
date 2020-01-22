<footer id="footer">
    Copyright <a href=""> あみぃずまーけっと</a>. All Rights Reserved.
</footer>

<script src="js/vendor/jquery-3.4.1.min.js"></script>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
  $(function(){

    //メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    console.log(msg);
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
      $jsShowMsg.slideToggle('slow');
      setTimeout(function(){ $jsShowMsg.slideToggle('slow')}, 5000);
    }

    // テキストエリアカウント
    var $countUp = $('#js-count'),
      $countView = $('#js-count-view');
    $countUp.on('keyup', function(e){
      $countView.html($(this).val().length);
    });

    //画像ライブプレビュー
    var $dropArea1 = $('.area1');
    var $dropArea2 = $('.area2');
    var $dropArea3 = $('.area3');
    var $fileInput = $('.input-file');

    hoge($dropArea1);
    hoge($dropArea2);
    hoge($dropArea3);

    function hoge($hoge){
      $hoge.on('dragover',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','3px #ccc dashed');
      });

      $hoge.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','none');
      });

      $fileInput.on('change',function(e){
        $hoge.css('border','none');
        console.log($hoge);
        var file = this.files[0];
        $img = $(this).siblings('.prev-img');
        fileReader = new FileReader();

        fileReader.onload = function(event) {
          $img.attr('src', event.target.result).show();
          $img.css('display','block');
        };

        fileReader.readAsDataURL(file);
      });
    }

    //Ajax通信
    var $like,
        likeProductId;
    $like = $('.js-click-like') || null;
    likeProductId = $like.data('productid') || null;
    
    if(likeProductId !== undefined && likeProductId !== null){
      $like.on('click',function(){
        var $this = $(this);
        
        $.ajax({
          type:"POST",
          url:"ajaxLike.php",
          data: {productId : likeProductId}
        
        }).done(function(data){
          console.log('Ajax Success');
          $this.toggleClass('active');

        }).fail(function(msg){
          console.log('Ajax Error');
        });

      })
    }

    //画像切替
    var $switchImgSubs = $('.js-switch-img-sub'),
        $switchImgMain = $('#js-switch-img-main');
    $switchImgSubs.on('click',function(e){
      $switchImgMain.attr('src',$(this).attr('src'));
    });

    //フッターの高さ
    var $ftr = $('footer');
    if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
        $ftr.attr({'style':'position:fixed; top:' +(window.innerHeight - $ftr.outerHeight()) + 'px; width:100%'});
    }
  });
</script>
</body>
</html>