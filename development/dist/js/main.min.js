/* carousel */

if ($(window).width() > 768) {
  if (('.carousel__container').length > 0) {
    let Carousel = function Carousel() {
      const increment = 80;
      let totalImages;
      let $images;
      let $carousel;
      let carouselWidth;

      let on = function () {
        $carousel = $('.carousel__container');
        $images = $('.carousel__item');
        carouselWidth = $carousel.width();
        totalImages = $images.length;
        position();
      }

      let position = function () {
        let number;
        let currentImage = $('.carousel__container--active').index();
        let x = 0;
        let z = 0;
        let zindex;
        let scaleX = 1;
        let scaleY = 1;
        let transformOrigin;

        $images.each(function (index, element) {
          scaleX = scaleY = 0.9;
          transformOrigin = carouselWidth / 2;
          if (index < currentImage) {
            number = 1;
            zindex = index + 1;
            x = carouselWidth / 2 - increment * (currentImage - index + 1);
            z = -increment * (currentImage - index + 1);
          } else if (index > currentImage) {
            number = -1
            zindex = totalImages - index;
            x = carouselWidth / 2 + increment * (index - currentImage + 1);
            z = -increment * (index - currentImage + 1);
          } else {
            number = 0;
            zindex = totalImages;
            x = carouselWidth / 2;
            z = 1;
            scaleX = scaleY = 1;
            transformOrigin = 'initial';
          }
          $(element).css(
              {
                'transform': 'translate3d(' + calculateX(x, number, 780) + 'px, 0,' + z + 'px) scale3d(' + scaleX + ',' + scaleY + ', 1)',
                'z-index': zindex,
                'transform-origin-x': transformOrigin
              }
          );
        });
      };

      let calculateX = function (position, number, width) {
        switch (number) {
          case 1:
          case 0:
            return position - width / 2;
          case -1:
            return position - width / 2;
        }
      }

      let imageSize = function () {
        return $carousel.width() / 3;
      }

      let recalculateSizes = function () {
        carouselWidth = $carousel.width();
        position();
      }

      let clickedImage = function () {
        let activeImage = $(this);
        let activeImageNumber = $(this).index();

        $('.carousel__container--active').removeClass('carousel__container--active');
        activeImage.addClass('carousel__container--active');
        position();
      }

      let clickedDot = function () {
        let target = $(this).index();

        $('.carousel__item[data-target=' + target + ']').click();
      }

      let prevNext = function () {
        let getClass = $(this).attr('class');

        if (getClass === 'carousel__arrow carousel__arrow--right') {
          $('.carousel__container--active').next().click();
        } else {
          $('.carousel__container--active').prev().click();
        }
      }

      let addEvents = function () {
        $(window).resize(recalculateSizes);
        $(document).on('click', '.carousel__item', clickedImage);
        $(document).on('click', 'li', clickedDot);
        $(document).on('click', '.carousel__arrow', prevNext);
      }

      return {
        init: function () {
          on();
          addEvents();
        }
      };
    }();

    $(function () {
      const carousel = Carousel.init();
    })
  }
}
/* end carousel */

/* slick slider */
if ($(window).width() < 768) {
  if ($('.responsive').length > 0) {
    $('.responsive').slick({
      dots: false,
      arrows: true,
      responsive: [
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
      ]
    });
  }
}

/* init WOW */
if ($('.wow').length > 0) {
  new WOW().init();
}
if ($('.team-body').length > 0) {
  $('.team-body').slick({
    infinite: true,
    dots: false,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 2
        }
      },
      {
        breakpoint: 800,
        settings: {
          slidesToShow: 1
        }
      }
    ]
  });
}
// if ($('.team-title__typing').length > 0) {
//   function autoType(elementClass, typingSpeed) {
//     var thhis = $(elementClass);
//     thhis = thhis.find(".team-title__typing");
//     var text = thhis.text().trim().split('');
//     var amntOfChars = text.length;
//     var newString = "";
//     thhis.text("|");
//     setTimeout(function () {
//       thhis.css("opacity", 1);
//       thhis.text("");
//       for (var i = 0; i < amntOfChars; i++) {
//         (function (i, char) {
//           setTimeout(function () {
//             newString += char;
//             thhis.text(newString);
//           }, i * typingSpeed);
//         })(i + 1, text[i]);
//       }
//     }, 1500);
//   }
//
//   $(document).ready(function () {
//     autoType(".team-title", 200);
//   });
// }

$(document).on('click', '.show-video__already-seen, .show-video__close', function () {
  $('.show-video').hide();
});

/* slick slider date */
if ($('.single-item').length > 0) {
  var countSlide = $('.management-history__table').length;

  $('.slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    infinite: false,
    initialSlide: countSlide - 1,
    fade: true,
    prevArrow: '<button type="button" class="slick-prev"></button>',
    nextArrow: '<button type="button" class="slick-next"></button>',
    asNavFor: '.slider-nav'
  });
  $('.slider-nav').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    infinite: false,
    asNavFor: '.slider-for',
    initialSlide: countSlide - 1,
    dots: false,
    arrows: false,
    focusOnSelect: true
  });
}

if ($('[data-toggle="tooltip"]').length > 0) {
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
}

if ($('.img-preview').length > 0) {
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('.img-preview').attr('src', e.target.result);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  $("#userobjectives-image_file").change(function () {
    readURL(this);
  });
}
if ($('.personal-purposeful .slider-range').length > 0) {
  var pipsSlider = document.getElementsByClassName('slider-range')[0];
  pipsSlider.setAttribute('disabled', true);
  noUiSlider.create(pipsSlider, {
    range: {
      min: 0,
      max: 100
    },
    start: $(pipsSlider).data("start"),
    behaviour: 'tap-drag',
    animate: true,
    pips: {
      mode: 'count',
      values: 5,
      stepped: true
    }
  });
}
if ($('.ProgressBar-circle').length > 0) {
  (function ($) {
    $.fn.bekeyProgressbar = function (options) {

      options = $.extend({
        animate: true,
        animateText: false
      }, options);

      var $this = $(this);

      var $progressBar = $this;
      var $progressCount = $progressBar.find('.ProgressBar-percentage--count');
      var $circle = $progressBar.find('.ProgressBar-circle');
      var percentageProgress = $progressBar.attr('data-progress');
      var percentageRemaining = (100 - percentageProgress);
      var percentageText = $progressCount.parent().attr('data-progress');

      var radius = $circle.attr('r');
      var diameter = radius * 2;
      var circumference = Math.round(Math.PI * diameter);

      var percentage = circumference * percentageRemaining / 100;

      $circle.css({
        'stroke-dasharray': circumference,
        'stroke-dashoffset': percentage
      });

      if (options.animate === true) {
        $circle.css({
          'stroke-dashoffset': circumference
        }).animate({
          'stroke-dashoffset': percentage
        }, 3000)
      }

      if (options.animateText == true) {
        $({Counter: 0}).animate(
            {Counter: percentageText},
            {
              duration: 3000,
              step: function () {
                $progressCount.text(Math.ceil(this.Counter));
              }
            });
      } else {
        $progressCount.text(percentageText);
      }
    };
  })(jQuery);
  $(window).scroll(function () {
    if (animationVision()) {
      blockAnimation();
    }
  });

  var $progressBar = $('.ProgressBar');

  function animationVision() {
    var windowBottom = $(window).scrollTop() + $(window).height();
    var block2Bottom = $progressBar.offset().top + $progressBar.height();
    return windowBottom >= block2Bottom;
  }

  function blockAnimation() {
    $(".ProgressBar--animateAll").bekeyProgressbar();
  }
}
if ($('.rates-chat').length > 0) {
  $(".ProgressBar--animateAll").bekeyProgressbar();
}
if ($('.management-history__table').length > 0) {
  $('.management-history__table td').click(function () {
    $('.management-history__table td').removeClass('active');
    $(this).addClass('active');
  })
}
if ($('.left-side').length > 0) {
  $(window).on("load", function () {
    $(".left-side").mCustomScrollbar();
  });
}

$(document).on('change', '.attachment_upload', function () {
  $(this).closest('.fileUpload').find('.fakeUploadLogo').attr('placeholder', this.value.substring(12));
  $(this).closest('form').find('.after_upload_message').show();
  $(window).bind("beforeunload", function() {
    return confirm("У Вас остались несохраненные данные. Вы действительно хотите покинуть страницу?");
  })
});

if ($('.settings-withdrawal__item').length > 0) {
  $('.settings-withdrawal__item--title').click(function () {

    var classnow = $(this).parents('.settings-withdrawal__item').attr('class');
    $('.settings-withdrawal__item').removeClass('active');
    if (classnow != 'settings-withdrawal__item active') {
      $(this).parents('.settings-withdrawal__item').toggleClass('active');
    } else if ($(this).parents('.settings-withdrawal__item').hasClass('disable')) {
      // console.log('yyyyyyyy');
      $('.settings-withdrawal__item--title').click(function (e) {
        e.preventDefault;
      })
    } else {
      // console.log('nnnnnnnnnnnnnn');
    }
  });
  /* add selected span in form dropdown */
  $('.settings-withdrawal__item--list__item').click(function (e) {
    e.preventDefault();
    var resultText = $(this).parents('.settings-withdrawal__item--list').prev('.settings-withdrawal__item--title');
    $('.settings-withdrawal__item--list__item').removeClass('active');
    $(this).addClass('active');
    $(this).innerHTML;
    $(resultText).text((this).innerHTML);
    $('.settings-withdrawal__item').removeClass('active');
  });

  /*hide select list on document click*/
  $(document).click(function (e) {
    if (!$(e.target).closest(".settings-withdrawal__item").length) {
      $('.settings-withdrawal__item').removeClass('active');
    }
    e.stopPropagation();
  });
}

if ($('.payments-item').length > 0) {
  $('.payments-item').click(function () {
    $('.payments-item').removeClass('active');
    $(this).addClass('active');
  });
}

if ($('.grid').length > 0) {
  function enableIsotope() {
    $('.promo-content__top').each(function (i, buttonGroup) {
      var $buttonGroup = $(buttonGroup);

      var grid = $buttonGroup.data('target');
      $(grid).isotope({
        itemSelector: '.element-item',
        percentPosition: true
      })

      $buttonGroup.on('click', 'button', function () {
        var $this = $(this);
        var filterValue = $this.attr('data-filter');
        $(grid).isotope({filter: filterValue})
        $buttonGroup.find('.is-checked').removeClass('is-checked');
        $this.addClass('is-checked');
      });
    });

    $(".promo .nav-link").click(function () {
      $(this).tab('show');
    });
    $('.promo .nav-link').on('shown.bs.tab', function (e) {
      if (e.target.hash == '#gif' || e.target.hash == '#html' || e.target.hash == '#png') {
        $('.grid').isotope({
          itemSelector: '.element-item',
          percentPosition: true
        });
      }
    });
  };
  enableIsotope();
}

if ($('.messages-item').length > 0) {
  $('.messages-item').click(function () {
    // $(this).removeClass('unread');
    $(this).parents('.messages-items').hide();
    $('.messages-read, .messages .back-arrow').show();
  });
  $('.messages .back-arrow').click(function () {
    $('.messages-items').show();
    $('.messages-read, .messages .back-arrow').hide();
  });
}

/* loading first slide animaion on main page */
// if ($('.first-slide').length > 0) {
//   function preLoader() {
//     for (var a = [], e = 0; e < e.length; e++) !function (t, a) {
//       var e = new Image;
//       e.onload = function () {
//         a.resolve()
//       }, e.src = t
//     }(t[e], a[e] = $.Deferred());
//     $.when.apply($, a).done(function () {
//       setTimeout(function () {
//         $(".nowebkitbrowser").length || $(window).width() < 700 ? $(".first-slide").addClass("thirty-pieces-titles") : startupSequence()
//       }, preloaderTimeout)
//     })
//   }
//
//   function sizeshards() {
//     $(".wrap").each(function () {
//       var t = .99 * $(window).width(), a = .7 * t;
//       $(this).each(function () {
//         $(this).css({width: t, height: a})
//       })
//     })
//   }
//
//   function startupSequence() {
//     $(".first-slide").addClass("start-up-seq"), introinterval = 3100, setTimeout(function () {
//       $(".intro-sequence p:nth-child(1), .intro-sequence").addClass("active"), setTimeout(function () {
//         0 == skipped && $("#animalchanger").addClass("vaquita-shards")
//       }, 2600), setTimeout(function () {
//         0 == skipped && ($(".intro-sequence p.active").addClass("active"))
//       }, introinterval)
//     }, 1400), setTimeout(function () {
//       0 == skipped && $(".first-slide").addClass("thirty-pieces-titles") && ($(".intro-sequence p.active").removeClass("active"))
//     }, 4000)
//   }
//
//   var preloaderTimeout = 2200;
//   preLoader();
//   var app;
//
//   var skipped = 0;
//
//   function detectBrowser() {
//     if (navigator.userAgent.search(/Firefox/) > 0 || navigator.userAgent.search(/Edge/) > 0) {
//       $('.first-slide').addClass('performance-boost');
//     } else {
//       $('.first-slide').removeClass('performance-boost');
//     }
//   }
//
//   $(document).ready(function () {
//     sizeshards(), detectBrowser(), setTimeout(function () {
//       $(".level-one, .loading").removeClass("hidden-startup")
//     }, 130), sizeshards()
//   });
// }

if ($('.contact-support__item--worker-info').length > 0) {
  $('.contact-support__item--worker-info').click(function () {
    $('.contact-support__item--worker-info').removeClass('active');
    $(this).addClass('active');
  });
  $('.contact-support__item--worker-info').hover(function () {
    $('.contact-support__item--worker-info').removeClass('active');
    $(this).addClass('active');
  });
}
if ($('[data-toggle="tooltip"]').length > 0) {
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
}
var scrollPosition = 0;
if ($('.client-chat').length > 0) {

  $(document).on('click', '.client-chat, .show__chat', function (e) {
    e.preventDefault();
    $('.chat-item').addClass('active');
    $(document).find('body > jdiv:not(#jivo-mouse-tracker)').hide();
    setTimeout(function () {
      // targetElement = document.querySelector("#chat_window");
      // bodyScrollLock.disableBodyScroll(targetElement);
      scrollPosition = window.pageYOffset;
      $("body").css({"position":"fixed"});

    }, 220)
  });
  $('.mobile-close').click(function () {
    // var items_list = "html, body";
    // var menu = $(document).find('.container-fluid .navbar-toggler');
    // targetElement = document.querySelector("#chat_window");
    // bodyScrollLock.enableBodyScroll(targetElement);
    // $(items_list).css({"overflow":"initial"});
    $("body").css({"position":"initial"});
    window.scrollTo(0, scrollPosition);

    $('.chat-item').removeClass('active');
    $(document).find('body > jdiv:not(#jivo-mouse-tracker)').show();
  });
}
$(document).on('click', '.chat-item__show-all', function () {
  $(this).toggleClass('active');
  $(this).parent().find('.show-answer').toggleClass('active');
});

/* counter */
if ($('.odometer').length > 0) {
  var odometer = new Odometer({
    el: $('.odometer')[0],
    value: 0,
    theme: 'minimal',
    duration: 3000
  });
  odometer.render();

  $('.odometer').text($('#odometer').data('start'));
}
/* counter end */
if ($('.navbar').length > 0) {
  $(window).scroll(function () {
    if ($('.navbar').offset().top > 0) {
      $('.navbar').addClass('smaller-header');
    } else {
      $('.navbar').removeClass('smaller-header');
    }
  });
}
