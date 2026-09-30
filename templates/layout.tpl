<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{block name="title"}{$appName}{/block}</title>
  <meta name="description" content="{block name="meta_description"}{$appName}{/block}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <header class="header">
    <div class="container header__inner">
      <a class="header__logo" href="/"><span class="header__mark" aria-hidden="true"></span>{$appName}</a>
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
