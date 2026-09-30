{extends file="layout.tpl"}

{block name="title"}{$category->name} — {$appName}{/block}
{block name="meta_description"}{$category->description}{/block}

{block name="content"}
  <section class="category-page">
    <header class="category-page__header">
      <h1 class="category-page__title">{$category->name}</h1>
      <p class="category-page__description">{$category->description}</p>
    </header>

    {include file="partials/sort.tpl"}

    {if $postPage->posts}
      <div class="category-page__posts">
        {foreach $postPage->posts as $post}
          {include file="partials/post-card.tpl" post=$post}
        {/foreach}
      </div>
    {else}
      <p class="category-page__empty">В этой категории пока нет статей.</p>
    {/if}

    {include file="partials/pagination.tpl" pagination=$postPage->pagination}
  </section>
{/block}
