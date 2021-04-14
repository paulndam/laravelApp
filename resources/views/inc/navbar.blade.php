<nav class="navbar navbar-expand-md navbar-light">
  <div class="container">
    <a class="navbar-brand" href="/">Admin</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav ml-auto right_bar">
        <li class="nav-item">
        <?php if(backpack_auth()->check()) { ?>
          <a class="nav-link" href="/admin/logout"><i class='nav-icon fa fa-user'></i>Logout</a>
        <?php } else { ?>
          <a class="nav-link" href="/login"><i class='nav-icon fa fa-user'></i>Login</a>
        <?php } ?>
        </li>
      </ul>
    </div>
  </div>
</nav>