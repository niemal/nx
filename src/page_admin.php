<?php
    /**
     * We need to mitigate these login related functions somewhere else.
     * Perhaps we should create a folder dedicated for the admin page. We shall see.
     **/


    /**
     * @param
     *  NX class object.
     *
     * @return
     *  Empty string if the user is not logged in. Username otherwise.
     **/
    function is_logged($nx)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];

        $fingerprint = hash('sha256', $nx->config['salt'] . $ip . $ua);
        $result = $nx->db->query("SELECT user, logged_at_time, logged_time FROM sessions
                                                     WHERE fingerprint='$fingerprint';");

        if( $result->num_rows > 0 && !empty($_COOKIE['session']) ) {
            $row = $result->fetch_assoc();
            $cookie = hash('sha256', $ua . $row['user'] . $nx->config['salt']);

            if( (time() - $row['logged_at_time']) < $row['logged_time'] && $_COOKIE['session'] === $cookie)
                return $row['user'];
            else {
                $nx->db->query("DELETE FROM sessions WHERE fingerprint='$fingerprint';");
                setcookie('session', "", time() - $row['logged_time'], "/");
            }
        }
        else if( !empty($_COOKIE['session']) )
            setcookie('session', "", -1, "/");
        else if( $result->num_rows > 0 ) {
            $row = $result->fetch_assoc();
            $sql = "DELETE FROM sessions WHERE fingerprint='$fingerprint';";
            $nx->db->query($sql);
        }
 
        return "";
    }


    /**
     * @param
     *  NX class object, username, password and the amount of logged-in time in seconds.
     *
     * @return
     *  Boolean indicating the outcome.
     **/
    function try_to_login($nx, $user, $pass, $logged_time)
    {
        $user = $nx->db->real_escape_string($user);
        $pass = hash('sha256', $nx->config['salt'].$pass);

        $res = $nx->db->query("SELECT id FROM admin WHERE user='$user' AND pass='$pass' LIMIT 1;");

        if ($res->num_rows === 0)
            return false;
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
            $ua = $_SERVER['HTTP_USER_AGENT'];
            $now = time();
            $fingerprint = hash('sha256', $nx->config['salt'] . $ip . $ua);

            $nx->db->query("INSERT INTO sessions (user, fingerprint, logged_time, logged_at_time)
                                 VALUES ('$user', '$fingerprint', $logged_time, $now);");
            $cookie = hash('sha256', $ua . $user . $nx->config['salt']);
            setcookie('session', $cookie, $now + $logged_time);

            return true;
        }
    }


    /**
     * @param
     *  NX class object, username.
     *
     * @return
     *  Void. It just attempts to log out the user.
     **/
    function logout($nx, $user)
    {
        $user = $nx->db->real_escape_string($user);
        setcookie("session", "", -1, "/");
        $nx->db->query("DELETE FROM sessions WHERE user='$user';");
    }

    /**
     * page_admin.php main code.
     **/
    require_once('NX.php');

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
    } else if (isset($_POST['logout'])) {
        logout($nx, $user);
        $logged = false;
    }


?>
<!DOCTYPE html><html>
<head>
<meta charset="utf-8">
    <title><?php if (!$logged) { ?>Login<?php } else { ?>Home<?php } ?> | NX</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="assets/install.css">
</head>

<body>
    <div id="layout">
        <div id="main">
            <div class="header">
                <?php if ($logged) { ?>
                <h1>Welcome home!</h1>
                <p>Pleasure to finally meet you, <?php echo $user ?>!</p>

                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="content glass">
                    <p><button type="submit" name="logout">Logout</button></p>
                </form>
                <?php } else { ?>
                <h1>Login</h1>
                <?php } ?>
            </div>

            <?php if (!$logged) { ?>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="content glass">
                <div class="article">
                    <?php if ($err['error'] === true) { ?>
                    <h2><?php echo $err['error-h2']; ?></h2>
                    <p><?php echo $err['error-text']; ?></p>
                    <?php } ?>
                </div>

                <div class="pure-form pure-form-aligned">
                    <fieldset>
                        <div class="pure-control-group">
                            <label for="user">Username</label>
                            <input required name="user" type="text" placeholder="user">
                        </div>

                        <div class="pure-control-group">
                            <label for="pass">Password</label>
                            <input required name="pass" type="password" placeholder="pass">
                        </div>

                        <p>Remember me&nbsp;&nbsp;<input type="checkbox" name="remember" value="me"></p>
                        <p><button type="submit" name="submit">Login</button></p>
                    </fieldset>
                </div>
            </form>
            <?php } ?>
        </div>
    </div>
</body>
</html>
