<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{block name="title"}{$appName}{/block}</title>
  <meta name="description" content="{block name="meta_description"}{$appName}{/block}">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <header class="header">
    <div class="container header__inner">
      <a class="header__logo" href="/">{$appName}</a>
    </div>
  </header>

  <main class="container">
    {block name="content"}{/block}
  </main>

  <footer class="footer">
    <div class="container footer__inner">{$appName}</div>
  </footer>
</body>
</html>
