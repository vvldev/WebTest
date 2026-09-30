{extends file="layout.tpl"}

{block name="title"}{$post->title} — {$appName}{/block}
{block name="meta_description"}{$post->description}{/block}

{block name="content"}
  <article class="post">
    <header class="post__header">
      <ul class="post__categories">
        {foreach $post->categories as $category}
          <li><a class="post__category" href="/category/{$category->id}">{$category->name}</a></li>
        {/foreach}
      </ul>

      <h1 class="post__title">{$post->title}</h1>
      <p class="post__description">{$post->description}</p>

      <div class="post__meta">
        <time datetime="{$post->publishedAt->format('Y-m-d')}">{$post->publishedAt->format('d.m.Y')}</time>
        <span>Просмотров: {$post->views}</span>
      </div>
    </header>

    {if $post->imagePath}
      <img class="post__image" src="/{$post->imagePath}" alt="{$post->title}" width="1200" height="630">
    {/if}

    <div class="post__content">
      {foreach $post->paragraphs as $paragraph}
        <p>{$paragraph}</p>
      {/foreach}
    </div>
  </article>

  {if $similarPosts}
    <section class="similar-posts">
      <h2 class="similar-posts__title">Похожие статьи</h2>
      <div class="similar-posts__list">
        {foreach $similarPosts as $similarPost}
          {include file="partials/post-card.tpl" post=$similarPost}
        {/foreach}
      </div>
    </section>
  {/if}
{/block}
