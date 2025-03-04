$(function(){
/* LOGIN */

    $(".login-form .form-password-toggle .input-group-text").on("click touchend",function(ev){
        $(this).find(".bx-hide").toggleClass("show");
        if($(this).find(".bx-hide").hasClass("show")){
            $(this).parent().find("input").attr("type","text");
        }else{
            $(this).parent().find("input").attr("type","password");
        }
    });

    var winWidth =  $(window).width(); 
    var winHeight = $(window).height();
    var slideWidth = 112;
    var bannersWidth = 1200;
    var gtstart = false;

    $(window).resize(function() {
        // This will execute whenever the window is resized
        winHeight = $(window).height(); // New height
        winWidth = $(window).width(); // New width
        slideWidth = $(".draggable-element .swiper-slide").find(".swiper-slide").first().width();
        bannersWidth = $(".banners-container .banners-content").first().width();
    });

    /* BANNERS */

    $(".banners-container .arrow-left").on("click touchend",function(ev){
        var pObj = $(this).parent();
        var gObj = $(pObj).find(".banners-content").first();

        var wObj = gObj.width();
        var poffset = pObj.offset().left;
        var coffset = gObj.offset().left-poffset+60;
        if (coffset + 170 >0)
            coffset = 0;
        else 
            coffset += 170;

            gObj.css("left", coffset + "px");        
    });

    $(".banners-container .arrow-right").on("click touchend",function(ev){
        var pObj = $(this).parent();
        var gObj = $(pObj).find(".banners-content").first();

        var wObj = gObj.width();
        var pWidth = pObj.width();
        var poffset = pObj.offset().left;
        var coffset = gObj.offset().left-poffset-60;
        if (wObj+coffset<=pWidth)
            coffset = -(wObj-pWidth);
        else {
            coffset -= 170;
            gObj.css("left", coffset + "px");        
        }
    });


    /* SLIDER */

    $(".draggable-element .swiper-slide").on("click touchend", function (ev) {
        if ($(this).parent().data("dragged")) {
            $(this).parent().data("dragged", false);
            ev.preventDefault();
        } else {
            var mOLeft = 102;
            var mLeft = 112;
            if(winWidth<1200){
                slideWidth = $(this).find(".swiper-slide").first().width();
            }
            var pIndex = $(this).index();
            switch (pIndex) {
                case 1:
                    $(this).parent().animate({marginLeft: '+='+ (mOLeft*2) +'px'}, 100, function () {
                        $(this).css("margin-left", "-"+mLeft+"px");
                        $(this).prepend($(this).find(".swiper-slide").last());
                        $(this).prepend($(this).find(".swiper-slide").last());
                        $(this).find(".swiper-slide").first().addClass("zero-width");
                        $(this).data("animated", true);
                        var obj = $(this);
                        setTimeout(function () {
                            obj.find(".swiper-slide").first().removeClass("zero-width");
                        }, 100);
                        setTimeout(function () {
                            obj.data("animated", false);
                        }, 400);
                    });
                    break;
                case 2:
                    $(this).parent().animate({marginLeft: '+='+mOLeft+'px'}, 200, function () {
                        $(this).css("margin-left", "-"+mLeft+"px");
                        $(this).prepend($(this).find(".swiper-slide").last());
                        $(this).find(".swiper-slide").first().addClass("zero-width");
                        $(this).data("animated", true);
                        var obj = $(this);
                        setTimeout(function () {
                            obj.find(".swiper-slide").first().removeClass("zero-width");
                        }, 100);
                        setTimeout(function () {
                            obj.data("animated", false);
                        }, 400);
                    });
                    break;
                case 3:
                    break;
                case 4:
                    $(this).parent().animate({marginLeft: '-='+mOLeft+'px'}, 200, function () {
                        $(this).css("margin-left", "-"+mLeft+"px");
                        $(this).append($(this).find(".swiper-slide").first());
                        $(this).find(".swiper-slide:nth-child(5)").addClass("zero-width");
                        $(this).data("animated", true);
                        var obj = $(this);
                        setTimeout(function () {
                            obj.find(".swiper-slide.zero-width").removeClass("zero-width");
                        }, 100);
                        setTimeout(function () {
                            obj.data("animated", false);
                        }, 400);
                    });
                    break;
                case 5:
                    $(this).parent().animate({marginLeft: '-='+(mOLeft*2)+'px'}, 100, function () {
                        $(this).css("margin-left", "-"+mLeft+"px");
                        $(this).append($(this).find(".swiper-slide").first());
                        $(this).append($(this).find(".swiper-slide").first());
                        $(this).find(".swiper-slide:nth-child(5)").addClass("zero-width");
                        $(this).data("animated", true);
                        var obj = $(this);
                        setTimeout(function () {
                            obj.find(".swiper-slide.zero-width").removeClass("zero-width");
                        }, 100);
                        setTimeout(function () {
                            obj.data("animated", false);
                        }, 400);
                    });
                    break;
            }
        }
    });

    $(".draggable-element").on("mousedown touchstart", function (ev) {
        ev.preventDefault();
        $(this).data("startDrag", true);
        $(this).data("startX", ev.pageX);
        $(this).data("startY", ev.pageY);
    });

    $(".draggable-element").on("mousemove", function (ev) {
        if ($(this).data("startDrag")) {
            $(this).data("newX", ev.pageX);
            $(this).data("newY", ev.pageY);
            var dX = ev.pageX - $(this).data("startX");
            if (dX == 0) {
                $(this).data("dragged", false);
            } else {
                $(this).data("dragged", true);
            }
            ;
            if (!$(this).data("animated"))
                if (dX > 0) {
                    if (dX >= 92) {
                        $(this).css("margin-left", "-112px");
                        $(this).prepend($(this).find(".swiper-slide").last());
                        $(this).find(".swiper-slide").first().addClass("zero-width");
                        $(this).data("startX", ev.pageX);
                        $(this).data("animated", true);
                        var obj = $(this);
                        setTimeout(function () {
                            obj.find(".swiper-slide").first().removeClass("zero-width");
                        }, 100);
                        setTimeout(function () {
                            obj.data("animated", false);
                        }, 400);
                    } else {
                        $(this).css("margin-left", (dX - 112) + "px");
                    }
                } else if (dX < 0) {
                    if (dX <= -92) {
                        $(this).css("margin-left", "-112px");
                        $(this).append($(this).find(".swiper-slide").first());
                        $(this).find(".swiper-slide:nth-child(5)").addClass("zero-width");
                        $(this).data("startX", ev.pageX);
                        $(this).data("animated", true);
                        var obj = $(this);
                        setTimeout(function () {
                            obj.find(".swiper-slide.zero-width").removeClass("zero-width");
                        }, 100);
                        setTimeout(function () {
                            obj.data("animated", false);
                        }, 400);
                    } else {
                        $(this).css("margin-left", (dX - 112) + "px");
                    }
                }
        }

    });

    $(".draggable-element").on("mouseup touchend", function (ev) {
        ev.preventDefault();
        $(this).data("startDrag", false);
        $(this).data("endX", ev.pageX);
        $(this).data("endY", ev.pageY);
        $(this).css("margin-left", "-112px");
    });

     document.addEventListener('mousemove',function(ev){
        $(".draggable-element").each(function(i,el) {
            if($(el).data("startDrag")){
                var offs = $(el).offset();
                var owidth = $(el).parent().width();
                var oheight = $(el).parent().height();
                if(
                    ev.pageX<offs.left || ev.pageX>(offs.left+owidth) ||
                        ev.pageY<offs.top || ev.pageY>(offs.top+oheight)
                ){
                    $(el).data("startDrag",false);
                    $(el).data("endX",ev.pageX);
                    $(el).data("endY",ev.pageY);                
                    $(el).css("margin-left","-112px");

                }
            }
        });
        
     });

     /* size of search */
     $(".search-text").on("focus",function(ev){
        $(this).parent().parent().addClass("expanded");
     });

     $(".search-text").on("focusout",function(ev){
        $(this).parent().parent().removeClass("expanded");
     });

     /* GALLERY */

     // init
     if ($(".gallery-container") )
     {
        $("body").append('<div class="gallery-popup"><div class="gallery-popup-container"><h3 class="popup-header"></h3><div class="gallery-close"><a href="#"><img src="/img/close_black.png"></a></div><div class="gallery-container"></div></div></div>');

     }

     $(".gallery-container .arrow-right").on("click touchend", function(ev){
        var pObj = $(".gallery-container .slide-container");
        var gObj = $(".gallery-container .slide-container .slides");
        var wObj = gObj.width();
        var poffset = pObj.offset().left;
        var coffset = gObj.offset().left-poffset;
        if (coffset + 170 >0)
            coffset = 0;
        else
            coffset += 170;

            gObj.css("left", coffset + "px");
     });

     $(".gallery-container .arrow-left").on("click touchend", function(ev){
        var pObj = $(".gallery-container .slide-container");
        var gObj = $(".gallery-container .slide-container .slides");
        var wObj = gObj.width();
        var poffset = pObj.offset().left;
        var coffset = gObj.offset().left-poffset;
        if (coffset - 170 < (-wObj + 170))
            coffset = (-wObj + 170);
        else
            coffset -= 170;
            gObj.css("left", coffset + "px");
     });

    $(".gallery-container .slide").on("click touchend",function(ev){
        var html = $(this).html();
        var bgImg = $(this).css("background-image");
        $(".gallery-container .active-slide").html("<div class=\"blur-layer\"></div>"+html).css("background-image",bgImg);
        $(".gallery-container .active-slide").attr("data-id",$(this).attr("data-id"));
    });

    $(".gallery-container .active-slide").on("touchstart",function(ev){
        gtstart = true;
        setTimeout(function(){
            gtstart=false;
        },200);
    });

    $(".gallery-container .active-slide").on("click touchend",function(ev){
        if(ev.target.nodeName=="IMG" && gtstart)
        {
            var id = $(this).attr("data-id");
            var header = $(this).attr("data-header");
            var bgImg = $(this).css("background-image");
            var slides = "<div class=\"active-slide\" data-id=\"1\" data-header=\"тест ивент\">" + $(this).parent().find(".active-slide").html() + "</div><div class=\"slide-container\">" + $(this).parent().parent().find(".slide-container").html()+"</div>";
            
            $(".gallery-popup .popup-header").text(header);
            $(".gallery-popup .gallery-container").html(slides);
            $(".gallery-popup .gallery-container .active-slide").css("background-image",bgImg);
            $(".gallery-popup").css("visibility","visible").css("opacity","1");
            $(".gallery-popup .gallery-container .arrow-right").on("click touchend", function(ev){
                var pObj = $(".gallery-popup .gallery-container .slide-container");
                var gObj = $(".gallery-popup .gallery-container .slide-container .slides");
                var wObj = gObj.width();
                var poffset = pObj.offset().left;
                var coffset = gObj.offset().left-poffset;
                if (coffset - 170 < (-wObj + 170))
                    coffset = (-wObj + 170);
                else
                    coffset -= 170;
                    gObj.css("left", coffset + "px");
             });
        
             $(".gallery-popup .gallery-container .arrow-left").on("click touchend", function(ev){
                var pObj = $(".gallery-popup .gallery-container .slide-container");
                var gObj = $(".gallery-popup .gallery-container .slide-container .slides");
                var wObj = gObj.width();
                var poffset = pObj.offset().left;
                var coffset = gObj.offset().left-poffset;
                if (coffset + 170>0)
                    coffset = 0;
                else
                    coffset += 170;
                    gObj.css("left", coffset + "px");
             });
             $(".gallery-popup .gallery-container .slide").on("click touchend",function(ev){
                var html = $(this).html();
                var bgImg = $(this).css("background-image");
                $(".gallery-popup .gallery-container .active-slide").html("<div class=\"blur-layer\"></div>" + html).css("background-image",bgImg);

//                $(".gallery-popup .gallery-container .active-slide").html(html);
                $(".gallery-popup .gallery-container .active-slide").attr("data-id",$(this).attr("data-id"));

            });

            $(".gallery-popup .gallery-close a").on("click touchend",function(ev){
                $(".gallery-popup").css("opacity","0").css("visibility","hidden");
            });
        }
    });

    $(document).on("click", ".region-toggle-arrow", function (event) {
        console.log('arrow click');
        event.stopPropagation(); // Останавливаем всплытие клика
        event.preventDefault();

        let $parentItem = $(this).closest(".region-dropdown-item");
        let currentLevel = parseInt($parentItem.data("level"), 10);
        if (isNaN(currentLevel)) return; // Если data-level не число, выходим

        let isOpen = $parentItem.toggleClass("region-open").hasClass("region-open");

        let $nextSibling = $parentItem.next();

        // Перебираем следующие элементы и скрываем/показываем их
        while ($nextSibling.length && parseInt($nextSibling.data("level"), 10) > currentLevel) {
            if (isOpen) {
                $nextSibling.removeClass("region-hidden");
            } else {
                $nextSibling.addClass("region-hidden");
            }
            $nextSibling = $nextSibling.next();
        }
    });

    $(document).ready(function () {
        $(".region-dropdown-menu").each(function () {
            let $menu = $(this);
            let rect = this.getBoundingClientRect();

            if (rect.left < 0) {
                $menu.css("left", "0px"); // Не даем уйти влево
            }

            if (rect.right > $(window).width()) {
                let offset = rect.right - $(window).width() + 10; // Оставляем небольшой отступ
                $menu.css("left", ($menu.position().left - offset) + "px");
            }
        });
    });

});