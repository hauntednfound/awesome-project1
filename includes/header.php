<header>
    <nav>
        <ul>
            <li><a href="index.php">Mājas lapa</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Izlogoties</a></li>
            <?php else: ?>
                <li><a href="login.php">Ielogoties</a></li>
                <li><a href="register.php">Reģistrēties</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
