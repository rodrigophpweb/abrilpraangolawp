<?php get_header(); ?>

<main class="container-eventos">
    <h1 class="titulo-archive">Próximos Eventos</h1>

    <div class="grid-eventos">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <article class="card-evento">
                
                <?php if (has_post_thumbnail()) : ?>
                    <div class="thumb-evento">
                        <?php the_post_thumbnail('medium_large'); ?>
                    </div>
                <?php endif; ?>

                <div class="conteudo-evento">
                    <h2><?php the_title(); ?></h2>
                    <p><?php the_excerpt(); ?></p>

                    <a href="<?php the_permalink(); ?>" class="btn-evento">
                        Ver detalhes
                    </a>
                </div>

            </article>

        <?php endwhile; endif; ?>
    </div>
</main>

<?php get_footer(); ?>