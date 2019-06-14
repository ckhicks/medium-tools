<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo $meta['title'] . ' - ' . $meta['description']; ?></title>
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <?php if ($error) : ?>
      <strong><?php echo $error; ?></strong>
    <?php endif; ?>

    <header>
      <figure class="logo"><?php readfile('logo.svg'); ?></figure>
      <h1>Medium Tools</h1>
      <h2 class="lead lead-xxs">A painless method for creating drafts on Medium from your canonical content source.</h2>
    </header>

    <main>
      <!-- <hr /> -->
      <?php if ($mode === 'home') : ?>
        <button id="auth" class="button button-white button-border" onclick="authMedium()">Sign In</button>
      <?php elseif ($mode === 'user') : ?>
        <div class="bio">
          <p>Author: <strong><?php echo $query['name']; ?></strong></p>
          <figure><img src="<?php echo $query['avatar']; ?>" alt="Author: <?php echo $query['name']; ?>" /></figure>
        </div>
        <input id="link" type="text" placeholder="Your Article URL" />
        <button id="submit">Create Draft</button>
      <?php elseif ($mode === 'success') : ?>
        <div>Success! Edit your draft: <a href="<?php echo $query['draft']; ?>"><?php echo $query['draft']; ?></a></div>
      <?php endif; ?>
      <?php if ($mode !== 'home') : ?>
        <br />
        <button id="reset" class="button button-white button-border button-xs" onclick="resetApp()">Start Over</button>
      <?php endif; ?>
    </main>

    <script>
      // TODO: show spinner in buttons
      // function showSpinner() {
      // var submit = document.querySelector("#submit");
      // }

      // auth with medium
      function authMedium() {
        window.location.href = "<?php echo 'https://medium.com/m/oauth/authorize?client_id=' .  $client . '&scope=basicProfile,publishPost&state=' . $state . '&response_type=code&redirect_uri=' . $redirect; ?>";
      };

      // submit post
      var submit = document.querySelector("#submit");
      if (!!submit) {
        submit.addEventListener("click", function() {
          var link = document.querySelector("#link");
          if (!!link.value) {
            window.location.href = window.location.href + '&link=' + link.value;
          }
        });
      }

      // send users back to the default url
      function resetApp() {
        window.location.href = "<?php echo $redirect; ?>";
      };
    </script>
  </body>
</html>