{if $pagination->totalPages > 1}
  <nav class="pagination" aria-label="Пагинация">
    {if $pagination->hasPrevious}
      <a class="pagination__link" href="?sort={$sort->value}&amp;page={$pagination->currentPage - 1}" rel="prev" aria-label="Предыдущая страница">←<span class="pagination__text"> Назад</span></a>
    {/if}

    {for $pageNumber = 1 to $pagination->totalPages}
      {if $pageNumber === $pagination->currentPage}
        <span class="pagination__link pagination__link--active" aria-current="page">{$pageNumber}</span>
      {else}
        <a class="pagination__link" href="?sort={$sort->value}&amp;page={$pageNumber}">{$pageNumber}</a>
      {/if}
    {/for}

    {if $pagination->hasNext}
      <a class="pagination__link" href="?sort={$sort->value}&amp;page={$pagination->currentPage + 1}" rel="next" aria-label="Следующая страница"><span class="pagination__text">Вперёд </span>→</a>
    {/if}
  </nav>
{/if}
