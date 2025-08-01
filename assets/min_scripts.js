jQuery(document).ready(function($){
  // عند الضغط على الأزرار التي تحتوي على الكلاسات المحددة
  $('.submit, .whatsapp, .phone').on('click', function(e){
      
      var buttonClass = $(this).attr('class'); // جلب الكلاس
      var postId = $('body').attr('id'); // الحصول على معرف المنشور (الصفحة أو المقالة)
      
      var data = {
          action: 'track_button_clicks',
          button_class: buttonClass,
          post_id: postId // إرسال معرف المنشور (صفحة أو مقال)
      };
      
      // إجراء طلب AJAX
      $.post(ajax_object.ajax_url, data, function(response) {
        // هنا يمكنك إضافة كود لعرض النتيجة إذا أردت
      });
  });
});


function validatePhoneInput(input) {
    let value = input.value;

    // تحويل الأرقام العربية إلى أرقام إنجليزية
    let arabicToEnglishMap = {
        '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4',
        '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9'
    };

    value = value.replace(/[٠-٩]/g, function(match) {
        return arabicToEnglishMap[match];
    });

    // السماح فقط بالأرقام وعلامة "+"
    value = value.replace(/[^0-9+]/g, '');

    // التأكد من أن "+" لا يظهر إلا في بداية الرقم فقط
    if (value.includes('+')) {
        value = '+' + value.replace(/\+/g, '');
    }

    // تحديث قيمة الإدخال
    input.value = value;
}



jQuery(document).ready(function($) {

    if ($('.custom-reviews-grid').length && typeof $.fn.slick === 'function') {
        $('.custom-reviews-grid').slick({
            rtl: true,
            autoplay: true,
            accessibility: true,
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 3,
            arrows: false,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });
    }


  $('.gallery_images').slick({
    rtl: true, // تفعيل دعم RTL
    autoplay: true,
    accessibility: true, // هذه القيمة تمكن الوصول وتحسن التوافق مع ARIA
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 3,
    arrows: false, // تأكد من عدم وجود تكرار للخاصية
    responsive: [
      {
        breakpoint: 1024, // عرض الشاشة 1024 بكسل أو أقل
        settings: {
          slidesToShow: 2, // عرض شريحتين
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 768, // عرض الشاشة 768 بكسل أو أقل
        settings: {
          slidesToShow: 1, // عرض شريحة واحدة
        }
      }
    ]
  });
$('.gallry_logos').slick({
    rtl: true,
    autoplay: true,
    accessibility: true,
    dots: false,
    infinite: true,
    speed: 500,
    slidesToShow: 6, // عدد أكبر افتراضياً على الشاشات العريضة
    slidesToScroll: 1,
    centerMode: false, // إلغاء المركزية لتفادي المشاكل على الهواتف
    variableWidth: false, // إلغاء الأحجام المتغيرة لجعل التخطيط أنظف
    arrows: false,
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 4
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 3
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 2,
                arrows: false // يمكن إخفاء الأسهم في الشاشات الصغيرة
            }
        }
    ]
});


$('.contact_us .submit, .msvh_submit-btn, .submit-button').on('click', function(event) {
    event.preventDefault();

    var form = $(this).closest('form'); 

    // التحقق من صحة النموذج قبل الإرسال
    if (!form[0].checkValidity()) {
        form[0].reportValidity(); // عرض رسائل التنبيه الافتراضية للمتصفح
        return; // إيقاف التنفيذ إذا كان هناك خطأ
    }

    var phone = form.find('input[name="phone"]').val().trim();
      
    var phonePattern = /^[+\d]+$/; // يسمح فقط بالأرقام وحرف +

    if (!phonePattern.test(phone) || phone.length < 10) {
        alert('يجب إدخال رقم هاتف صالح .');
        return; // إيقاف التنفيذ إذا كان الرقم غير صالح
    }
      
    function cleanURL(url) {
        var urlObj = new URL(url);
        var paramsToRemove = ["utm_medium", "utm_source", "utm_id", "utm_content", "utm_term", "utm_campaign", "fbclid"];
        paramsToRemove.forEach(param => urlObj.searchParams.delete(param));
        return urlObj.origin + urlObj.pathname;
    }

    var ifprshorshort = form.hasClass('prshorshort');
    var ifunitform = form.hasClass('unform');
    var timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    var pUrl = cleanURL(location.href);
    var PTitle = document.title;
    var pageTitle = PTitle + "\n -- \n" + pUrl;
    var post_id = $('body').attr('id');
    
    // جمع بيانات الفورم مع التعامل مع الحقول الجديدة
    var formData = {
        action: 'submit_contact_form', 
        // الحقول الأساسية
        name: form.find('input[name="name"]').val() || '', 
        phone: form.find('input[name="phone"]').val() || '', 
        email: form.find('input[name="email"]').val() || '', 
        message: form.find('textarea[name="message"]').val() || '', 
        
        // حقول السيليكت المختلفة
        preferred_time: form.find('select[name="preferred_time"]').val() || form.find('select[name="selc-time"]').val() || '', 
        unit_type: form.find('select[name="unit_type"]').val() || '', 
        
        // حقل السعر الجديد
        price: form.find('input[name="price"]').val() || '', 
        
        // الحقول القديمة (للتوافق مع الأنظمة القديمة)
        contact: form.find('input[name="contact[]"]:checked').map(function() {
            return $(this).val();
        }).get(),
        
        // معلومات إضافية
        is_prshorshort: ifprshorshort ? "1" : "0",
        is_unitform: ifunitform ? "1" : "0",
        timeZone: timeZone,
        pageTitle: pageTitle,
        post_id: post_id, 
    };

    // إرسال بيانات مبسطة لـ Google Form
    $.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'submit_to_google_form_action',
            name: formData.name,
            phone: formData.phone,
            email: formData.email,
            unit_type: formData.unit_type,
            price: formData.price,
            preferred_time: formData.preferred_time,
            title: PTitle,
            url: pUrl,
            zone: timeZone,
            team: ajax_object.author_name,
        },
        success: function(response) {
            console.log('Google Form submitted successfully:', response);
        },
        error: function(xhr, status, error) {
            console.log('Google Form submission error:', error);
        }
    });

    // إرسال البيانات الكاملة للسيرفر الرئيسي
    $.ajax({
        url: ajax_object.ajax_url,
        type: 'POST',
        data: formData,
        success: function(response) {
            console.log('Main form submitted successfully:', response);
            
            // مسح جميع الحقول بعد الإرسال الناجح
            form.find('input[type="text"], input[type="tel"], input[type="phone"], input[type="email"], textarea').val('');
            form.find('select').prop('selectedIndex', 0);
            form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

            // التوجيه لصفحة الشكر إذا كانت متاحة
            if (ajax_object.thank_you_url) {
                window.location.href = ajax_object.thank_you_url;
            }
        },
        error: function(xhr, status, error) {
            console.log('Main form submission error:', error);
            alert('حدث خطأ أثناء إرسال النموذج. يرجى المحاولة مرة أخرى.');
        }
    });
});
  

  $('.mobile-menu').on('click', function() {
    $('.mobilemenu').toggleClass( "active" );
  });
  
  $('.mobilemenu .menu-item-has-children > a').on('click', function(event) {
    event.preventDefault(); // منع السلوك الافتراضي للرابط
});
$('.mobilemenu .menu-item-has-children').click(function(){
	$(this).toggleClass('active')
    $(this).find('ul.sub-menu').toggleClass('active')
});

$('.flx-thx').click(function(){
  $('.flx-thx').removeClass( "active" );
  $('.aqaarop').removeClass('active');
});



$('.popubleadformover, span.closepop , .openform , .holdbrshor, .property-card').on('click', function() {
  $('.popubleadform').toggleClass('active');
});

$('.subform').on('click', function() {s
  $('.popubleadform').toggleClass('active');
});


$('.towitem .subform').on('click', function() {
  $('.aqaarop').addClass('active');
});


lightbox();

});







function lightbox(){
  jQuery(document).ready(function($) {
    var currentIndex = 0;
    var currentImages = [];
    
    function createLightboxHTML() {
        $('#lightbox').empty(); 
    
        var closeButton = $('<span>').addClass('close').html('<i class="fa fa-times" aria-hidden="true"></i>');
        $('#lightbox').append(closeButton);
    
        var prevButton = $('<span>').addClass('prev').html('<i class="fa fa-chevron-left" aria-hidden="true"></i>');
        $('#lightbox').append(prevButton);
    
        var mainImage = $('<img>').addClass('main-image').attr('src', currentImages[0]);
        $('#lightbox').append(mainImage);
    
        var thumbnailsContainer = $('<div>').addClass('thumbnails');
        $.each(currentImages, function(index, imageUrl) {
            var thumbnail = $('<img>').attr('src', imageUrl).attr('alt', 'Thumbnail ' + (index + 1));
            thumbnail.on('click', function() {
                openLightbox(index);
            });
            thumbnailsContainer.append(thumbnail);
        });
        $('#lightbox').append(thumbnailsContainer);
    
        var nextButton = $('<span>').addClass('next').html('<i class="fa fa-chevron-right" aria-hidden="true"></i>');
        $('#lightbox').append(nextButton);
    
        addEventListeners();
    }
    
    function openLightbox(index) {
        currentIndex = index;
        $('.main-image').attr('src', currentImages[index]);
        highlightThumbnail(index);
    }
    
    function highlightThumbnail(index) {
        $('.thumbnails img').each(function(i) {
            if (i === index) {
                $(this).attr('style', 'border : 3px solid #0866ff!important');
            } else {
                $(this).css('border', 'none');
            }
        });
    }
    
    function addEventListeners() {
        $('.close').on('click', function() {
            $('#lightbox').hide();
        });
    
        $('.prev').on('click', function() {
            currentIndex = (currentIndex === 0) ? currentImages.length - 1 : currentIndex - 1;
            $('.main-image').attr('src', currentImages[currentIndex]);
            highlightThumbnail(currentIndex);
        });
    
        $('.next').on('click', function() {
            currentIndex = (currentIndex === currentImages.length - 1) ? 0 : currentIndex + 1;
            $('.main-image').attr('src', currentImages[currentIndex]);
            highlightThumbnail(currentIndex);
        });
    
        $('#lightbox').on('click', function(e) {
            if ($(e.target).is('#lightbox')) {
                $('#lightbox').hide();
            }
        });
    }
    
    $('.gallery_images').on('click', function() {
        currentImages = lightboximg;
        createLightboxHTML(); 
        $('#lightbox').css('display', 'flex'); 
        openLightbox(0); 
    });
  });
}


jQuery(document).ready(function ($) {
    $('.accordion-header').on('click', function () {
        var $header = $(this);
        var $content = $header.next('.accordion-content');
        var isExpanded = $header.attr('aria-expanded') === 'true';

        // غلق جميع العناصر الأخرى لو عايز أكورديون واحد يفتح في نفس الوقت
        // $('.accordion-header').not($header).attr('aria-expanded', false).find('.accordion-icon').removeClass('rotated');
        // $('.accordion-content').not($content).slideUp(300);

        if (isExpanded) {
            // إغلاق العنصر الحالي
            $header.attr('aria-expanded', 'false');
            $header.find('.accordion-icon').removeClass('rotated');
            $content.slideUp(300);
        } else {
            // فتح العنصر الحالي
            $header.attr('aria-expanded', 'true');
            $header.find('.accordion-icon').addClass('rotated');
            $content.slideDown(300);
        }
    });
});

