<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title></title>
    <style>
      .shade {}
      .fade {}
    </style>
  </head>
  <body>
    <?php if ($error) : ?>
      <strong><?php echo $error; ?></strong>
    <?php endif; ?>
    <?php if ($mode === 'home') : ?>
      <div><a href="https://medium.com/m/oauth/authorize?client_id=<?php echo $client; ?>&scope=basicProfile,publishPost&state=<?php echo $state; ?>&response_type=code&redirect_uri=<?php echo $redirect; ?>">Auth</a></div>
    <?php elseif ($mode === 'user') : ?>
      <div>Posting as: <?php echo $query['name']; ?></div>
      <div><img src="<?php echo $query['avatar']; ?>" /></div>
      <div><input id="link" type="text" /></div>
      <div><button id="submit">Submit</button></div>
    <?php elseif ($mode === 'success') : ?>
      <div>Success! Edit your draft: <a href="<?php echo $query['draft']; ?>"><?php echo $query['draft']; ?></a></div>
    <?php endif; ?>
    <script>
      document.querySelector("#submit").addEventListener("click", function() {
        var link = document.querySelector("#link");
        if (!!link.value) {
          window.location.href = window.location.href + '&link=' + link.value;
        }
      });
    </script>
  </body>
</html>