<!-- Lightbox HTML -->
<div id="lightbox"></div>


<div class='popubleadform'>
    <div class='popubleadformover'></div>
    <div class='container'>
        <div class='headflx popubform'>
            <span class='closepop'><i class="fa fa-times" aria-hidden="true"></i></span>
            <div class="contact_us">
                <div class="form-title">سجل بياناتك الان</div>
                <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" id='popubform'>
                    <div class="cusinpput">
                        <label for="name">الاسم *</label>
                        <input placeholder="الاسم" type="text" id="name" name="name" required />
                    </div>
                    <div class="cusinpput">
                        <label for="phone">رقم الهاتف *</label>
                        <input placeholder="رقم الهاتف"  oninput="validatePhoneInput(this)" type="tel" id="phone" name="phone" required />
                    </div>
                    <button class='submit' type="submit" style="background-color: #3f51b5; color: #fff;">
                        إرسال
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>



<?php wp_footer(); ?>

</body>
</html>