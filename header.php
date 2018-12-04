<!-- Taken from https://tutorialzine.com/2015/02/freebie-7-responsive-header-templates -->

<header class="header-basic">
<div class="container">
    <div class="header-limiter">
        <h1><a href="index.php">Hungry <span>but Indecisive</span> Boiler</a></h1>
        <nav>
            <a href="index.php">Home</a>
            <?php
                // Assume sesion already started by the calling file
                if($_SESSION['userID'])
                {
                    echo "<a href='presets.php'>Presets</a>
                        <a href='blacklist.php'>Blacklist</a>
                        <a href='signout.php'>Sign Out</a>";
                }
                else
                {
                    echo "<a href='login.php'>Log In</a>
                        <a href='signup.php'>Sign Up</a>";
                }
            ?>
        </nav>
    </div>
</div>
</header>