{extends file="layout.tpl"}

{block name="title"}{$statusCode} — {$message}{/block}

{block name="content"}
  <section class="error">
    <p class="error__code">{$statusCode}</p>
    <h1 class="error__message">{$message}</h1>
    <a class="error__link" href="/">На главную</a>
    {if $details}
      <pre class="error__details">{$details}</pre>
    {/if}
  </section>
{/block}
