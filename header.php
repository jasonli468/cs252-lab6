<style>
</style>

<header class="header-basic">
<div class="header-limiter">
    <h1><a href="index.php">Hungry <span>but Indecisive</span> Boiler</a></h1>
    <nav>
        <a href="index.php">Home</a>
        <?php
            if($_SESSION['userID'])
            {
                echo "<a href='index.php'>Home</a>
                    <a href='managelocations.php'>Manage Locations</a>
                    <a href='ignoredrestaurants.php'>Ignored Restaurants</a>
                    <a href='accountsettings.php'>Account Settings</a>
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
</header>