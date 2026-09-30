{* Sort links drop "page": a new order starts from the first page. *}
<nav class="sort" aria-label="Сортировка">
  <span class="sort__label">Сортировка:</span>
  {foreach $sortOptions as $option}
    {if $option === $sort}
      <span class="sort__link sort__link--active" aria-current="true">{$option->label()}</span>
    {else}
      <a class="sort__link" href="?sort={$option->value}">{$option->label()}</a>
    {/if}
  {/foreach}
</nav>
