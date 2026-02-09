<?php
session_start();

// Always send no-cache headers to avoid caching of sensitive pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Handle logout before any output
if (isset($_GET['logout'])) {
	// Clear all session data
	$_SESSION = [];

	// Remove session cookie if used
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params['path'], $params['domain'],
			$params['secure'], $params['httponly']
		);
	}

	// Clear any application cookies like "remember"
	if (isset($_COOKIE['remember'])) {
		setcookie('remember', '', time() - 42000, '/');
		unset($_COOKIE['remember']);
	}

	session_destroy();

	// Redirect to the form and stop further execution
	header('Location: index.html');
	exit;
}

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$data = ($method === 'POST') ? $_POST : $_GET;
$email = $data['email'] ?? '';
$psw = $data['psw'] ?? '';
$psw_repeat = $data['psw-repeat'] ?? '';

// Save submitted credentials into session on POST
if ($method === 'POST') {
	$_SESSION['email'] = $email;
	$_SESSION['psw'] = $psw;
	$_SESSION['psw_repeat'] = $psw_repeat;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Credentials</title>
	<style>
		button { margin-right: 8px; }
	</style>
</head>
<body>
	<h1>Submitted Credentials</h1>
	<p><strong>Method:</strong> <?php echo h($method); ?></p>
	<p><strong>Email:</strong> <?php echo h($email); ?></p>
	<p><strong>Password:</strong> <?php echo h($psw); ?></p>
	<p><strong>Repeat Password:</strong> <?php echo h($psw_repeat); ?></p>

	<h2>Session</h2>
	<?php if (!empty($_SESSION['email'])): ?>
		<p>Logged in as: <?php echo h($_SESSION['email']); ?></p>
	<?php else: ?>
		<p>No active session.</p>
	<?php endif; ?>

	<p>
		<button type="button" onclick="window.history.back();">Return</button>
		<button type="button" onclick="window.location.replace('php.php?logout=1');">Log out</button>
	</p>
</body>
</html>

