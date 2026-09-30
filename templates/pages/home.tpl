{extends file="layout.tpl"}

{block name="title"}{$appName} — последние статьи по категориям{/block}
{block name="meta_description"}Последние статьи блога по категориям{/block}

{block name="content"}
  {foreach $categories as $categoryWithPosts}
    <section class="category-section">
      <header class="category-section__header">
        <h2 class="category-section__title">{$categoryWithPosts->category->name}</h2>
        <p class="category-section__description">{$categoryWithPosts->category->description}</p>
      </header>

      <div class="category-section__posts">
        {foreach $categoryWithPosts->posts as $post}
          {include file="partials/post-card.tpl" post=$post}
        {/foreach}
      </div>

      <a class="category-section__all" href="/category/{$categoryWithPosts->category->id}">Все статьи</a>
    </section>
  {foreachelse}
    <p class="category-section__empty">Статей пока нет.</p>
  {/foreach}
{/block}
