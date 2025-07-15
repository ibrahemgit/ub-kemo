<?php get_header(); ?>

<?php the_content(); ?>



<?php
$messages = get_option('notification_messages', []);
if (!empty($messages)) :
    $json_messages = json_encode(array_values($messages));
?>
    <div id="site-notification" class="site-notification" style="display:none;"></div>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const messages = <?php echo $json_messages; ?>;
    const container = document.getElementById('site-notification');

    function showNotification(msg) {
        container.textContent = msg;
        container.classList.remove('hide');
        container.classList.add('show');
        container.style.display = 'block';

        // أخفي بعد 3 ثواني بأنيميشن
        setTimeout(() => {
            container.classList.remove('show');
            container.classList.add('hide');
        }, 3000);
    }

    if (messages.length > 0) {
        setInterval(() => {
            const randomMsg = messages[Math.floor(Math.random() * messages.length)];
            showNotification(randomMsg);
        }, 10000); // كل 10 ثواني علشان 3 ثواني عرض + ثانية راحة
    }
});
    </script>

<?php endif; ?>



<?php get_footer(); ?>