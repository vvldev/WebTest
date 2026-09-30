<article class="post-card">
  {if $post->imagePath}
    <a class="post-card__image-link" href="/post/{$post->id}" tabindex="-1">
      <img class="post-card__image" src="/{$post->imagePath}" alt="{$post->title}" width="1200" height="630" loading="lazy">
    </a>
  {/if}

  <div class="post-card__body">
    <h3 class="post-card__title">
      <a class="post-card__link" href="/post/{$post->id}">{$post->title}</a>
    </h3>
    <p class="post-card__description">{$post->description}</p>

    <div class="post-card__meta">
      <time datetime="{$post->publishedAt->format('Y-m-d')}">{$post->publishedAt->format('d.m.Y')}</time>
      <span>Просмотров: {$post->views}</span>
    </div>
  </div>
</article>
