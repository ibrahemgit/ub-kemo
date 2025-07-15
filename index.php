<?php get_header(); ?>

<main>
    <style>
        body{
            background: #f9fafb;
        }
/* عنوان القسم */ .section-title { text-align: center; font-size: 2rem; margin-bottom: 2rem; color: #333; } /* تغليف المنشورات */ .posts-wrapper { max-width: 800px; margin: 0 auto; padding: 0 20px 50px; } /* كل بوست */ .post-card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); transition: box-shadow 0.2s ease-in-out; } .post-card:hover { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); } /* عنوان البوست */ .post-title { font-size: 1.5rem; color: #222; margin-bottom: 1rem; } /* مقتطف */ .post-snippet { font-size: 1.125rem; line-height: 1.8; color: #555; } /* رابط اقرأ المزيد */ .post-footer { margin-top: 1.5rem; } .read-more { color: #007acc; font-weight: 500; text-decoration: none; transition: color 0.2s; } .read-more:hover { color: #005fa3; } /* لا توجد مقالات */ .no-posts { text-align: center; font-size: 1.25rem; color: #777; margin-top: 3rem; } /* ترقيم الصفحات */ .pagination { text-align: center; margin-top: 2rem; } .pagination .page-numbers { display: inline-block; margin: 0 5px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none; color: #333; } .pagination .current { background-color: #007acc; color: white; border-color: #007acc; } .pagination .page-numbers:hover { border-color: #007acc; color: #007acc; }
header { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08); margin-bottom: 30px;align-items: center; display: flex ; height: 100px; position: relative; justify-content: center; font-size: 28px; }

footer {
    margin-top: 30px;
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.08);
}
</style>
    <header style="text-align: center; padding: 40px 0; background-color: #fff;">
        <h1><?php bloginfo('name'); ?></h1>
    </header>
<h2 class="section-title">أحدث المنشورات</h2>

<section class="posts-wrapper">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article class="post-card">
                <h2 class="post-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p class="post-snippet">
                    <?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?>
                </p>
                <div class="post-footer">
                    <a href="<?php the_permalink(); ?>" class="read-more">اقرأ المزيد ←</a>
                </div>
            </article>
        <?php endwhile; ?>

        <div class="pagination">
            <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => 'السابق',
                    'next_text' => 'التالي',
                ]);
            ?>
        </div>
    <?php else : ?>
        <p class="no-posts">لا توجد مقالات حالياً.</p>
    <?php endif; ?>
</section>

</main>

<footer style="text-align: center; padding: 30px 0; background-color: #fff;">
    <p>جميع الحقوق محفوظة &copy; <?php echo date('Y'); ?></p>
</footer>
<?php get_footer(); ?>