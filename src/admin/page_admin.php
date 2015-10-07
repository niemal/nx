<?php
    /**
     * page_admin.php main code.
     **/
    require_once('functions.php');

    $nx = new NX();
    $user = is_logged($nx);
    $logged = !empty($user);

    $err = [
        'error' => false,
        'error-h2' => 'Error',
        'error-text' => 'Invalid credential combination.'
    ];

    if (!$logged) {
        if (isset($_POST['submit'])) {
            if (!isset($_POST['user']) || !isset($_POST['pass'])) {
                $err['error'] = true;
                $err['error-text'] = 'You forgot something.';
            } else if ( (strlen($_POST['user']) < 4 || strlen($_POST['user']) > 32) ||
                        (strlen($_POST['pass']) < 4 || strlen($_POST['pass']) > 32) ) {
                $err['error'] = true;
                $err['error-text'] = 'Both username and password legnths must not be less than 4 and not higher than 32.';
            } else {
                if (isset($_POST['remember'])) $logged_time = 9999999999;
                else                           $logged_time = 600;

                $err['error'] = !try_to_login($nx, $_POST['user'], $_POST['pass'], $logged_time);
                if (!$err['error']) {
                    $user = $_POST['user'];
                    $logged = true;
                }
            }
        }
    } else if (isset($_GET['logout'])) {
        logout($nx, $user);
        $logged = false;
    }


?>
<!DOCTYPE html><html>
<head>
<meta charset="utf-8">
    <title><?php if (!$logged) { ?>Login<?php } else { ?>Home<?php } ?> | NX</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>

<body>
    <div id="layout">

    <?php if ($logged) { ?>
        <a href="#menu" id="menuLink" class="menu-link"><span></span></a>

        <div id="menu" class="dark-glass">
            <div class="pure-menu">
                <a class="pure-menu-heading dark-glass" href="#">NX ANALYTICS</a>

                <ul class="pure-menu-list">
                    <li class="pure-menu-item"><a href="#" class="pure-menu-link">Dashboard</a></li>
                    <li class="pure-menu-item"><a href="#" class="pure-menu-link">Pretty graphs</a></li>
                    <li class="pure-menu-item"><a href="#" class="pure-menu-link">Tables and stuff</a></li>
                    <li class="pure-menu-item"><a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="pure-menu-link">Logout</a></li>
                </ul>
            </div>
        </div>

        <div id="main">
            <div class="header">
                <h1>Title</h1>
                <h2>Whatever</h2>
            </div>

            <div class="content glass">
                <div class="article">
                    <h2 class="article-h2">Title</h2>
                    <p>Welcome and stuff</p>
                </div>

                <div class="pure-g">
                    <div class="pure-u-1-4">
                        <img class="pure-img-responsive" src="http://farm8.staticflickr.com/7357/9086701425_fda3024927.jpg">
                    </div>
                    <div class="pure-u-1-4">
                        <img class="pure-img-responsive" src="http://farm3.staticflickr.com/2813/9069585985_80da8db54f.jpg">
                    </div>
                    <div class="pure-u-1-4">
                        <img class="pure-img-responsive" src="http://farm6.staticflickr.com/5456/9121446012_c1640e42d0.jpg">
                    </div>
                    <div class="pure-u-1-4">
                        <img class="pure-img-responsive" src="http://farm3.staticflickr.com/2875/9069037713_1752f5daeb.jpg">
                    </div>
                </div>
            </div>
        </div>


    <script>
        (function (window, document) {

            var layout   = document.getElementById('layout'),
                menu     = document.getElementById('menu'),
                menuLink = document.getElementById('menuLink'),
                logout   = document.getElementById('logout');

            // this is here because of old browsers
            function toggleClass(element, className) {
                var classes = element.className.split(/\s+/),
                    length = classes.length,
                    i = 0;

                for(; i < length; i++) {
                  if (classes[i] === className) {
                    classes.splice(i, 1);
                    break;
                  }
                }
                // The className is not found
                if (length === classes.length) {
                    classes.push(className);
                }

                element.className = classes.join(' ');
            }

            menuLink.onclick = function (e) {
                var active = 'active';

                e.preventDefault();
                toggleClass(layout, active);
                toggleClass(menu, active);
                toggleClass(menuLink, active);
            };
        }(this, this.document));
    </script>

        <?php } else { ?>

        <div id="main">
            <div class="header">
                <h1>Login</h1>
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" class="content glass">
                <div class="article">
                    <?php if ($err['error'] === true) { ?>
                    <h2><?php echo $err['error-h2']; ?></h2>
                    <p><?php echo $err['error-text']; ?></p>
                    <?php } ?>
                </div>

                <div class="pure-form pure-form-aligned">
                    <fieldset style="text-align: center">
                        <div class="pure-control-group">
                            <input required name="user" type="text" placeholder="Username">
                        </div>

                        <div class="pure-control-group">
                            <input required name="pass" type="password" placeholder="Password">
                        </div>

                        <div>
                            <p>Remember me&nbsp;&nbsp;<input type="checkbox" name="remember" value="me"></p>
                            <button class="pure-button button-xlarge" type="submit" name="submit">Login</button>
                        </div>
                    </fieldset>
                </div>
            </form>
        </div>

            <?php } ?>

    </div>
</body>
</html>
