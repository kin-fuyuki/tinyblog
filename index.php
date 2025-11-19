<?php
$p = __DIR__ . "/posts";
$posts = glob($p . "/*", GLOB_ONLYDIR);
usort($posts, function($a, $b) {
	return intval(basename($b)) <=> intval(basename($a));
}

$post = $posts[0] ?? null;
$img = null;

if ($post) {
	$images = glob($post . "/*.{png,jpg,jpeg,gif,webp}", GLOB_BRACE);
	if (!empty($images)) {
		$img = str_replace(__DIR__, '', $images[0]);
		$img = "http://" . $_SERVER['HTTP_HOST'] . $img;
	}
	$contentFile = $post . "/content.html";
	if (file_exists($contentFile)) {
		$html = file_get_contents($contentFile);
		$latestText = strip_tags($html);
		$latestText = trim($latestText);
		$latestText = mb_substr($latestText, 0, 200);
	}
}
?>

<head>
  <meta charset="utf-8">
  <title>blog</title>
  <meta name="title" content="blog" />
  <meta name="description" content="
newest post:
<?php echo htmlspecialchars($latestText, ENT_QUOTES); ?>" />
  <meta property="og:site_name" content="blog" />
  <meta property="og:url" content="http://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" />
  <meta property="og:title" content="blog" />
  <meta property="og:description" content="blog
newest post:
<?php echo htmlspecialchars($latestText, ENT_QUOTES); ?>" />
  <?php if ($img): ?>
	<meta property="og:image" content="<?= htmlspecialchars($img) ?>" />
	<meta property="og:image:width" content="1280" />
	<meta property="og:image:height" content="720" />
	<link rel="image_src" href="<?= htmlspecialchars($img) ?>" />
	<meta name="twitter:image" content="<?= htmlspecialchars($img) ?>" />
  <?php endif; ?>
  <meta property="og:type" content="website" />
  <meta name="theme-color" content="#ff00ff">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="blog" />
  <meta name="twitter:description" content="blog
newest post:
<?php echo htmlspecialchars($latestText, ENT_QUOTES); ?>" />
  <style>
	body,span {
		color:#f77;
	}
	a {
	  color: #ff00ff;
	  text-decoration: none;
	}
	a:hover {
	  color: #ff0000ff;
	  text-decoration: underline;
	}
  .post{
  z-index:3;
  background-color:#0005;
  width:60vw;
  border:1px solid #0ff;
  display:flex;
  gap:1em;
  flex-direction:column;
  align-items:center;
  }
	
  </style>
</head>




<body id="body" style="color:#ffffff; background-color:#000000;
margin:0;padding:0;height: 100%;width:100%;
">
  
<div style="display: flex; gap: 1em;flex-direction: column; align-items: center;width:100vw; justify-content: center;">

<?php
foreach ($posts as $i) {
	$stuff = $i . "/content.html";
	if (file_exists($stuff)) {
		$creation = null;

		if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
			$creation = filectime($i);
		} else {
			$ctime = trim(shell_exec("stat -c %w " . escapeshellarg($i)));
			if ($ctime !== '-' && $ctime !== '') {
				$creation = strtotime($ctime);
			}
		}

		if (!$creation) $creation = filectime($i);

		$mod = filemtime($i);

		$date = date("Y-m-d H:i:s", $creation);
		$modified = ($mod > $creation + 60);

		echo "
<span class='post'>";
		echo "<div style=\"color:#00ff00\">posted on $date";
		if ($modified) {
			$md = date("Y-m-d H:i:s", $mod);
			echo " <span style='color:#aa0'>(edited on $md)</span>";
		}
		echo "</div>\n";
		include $stuff;
		echo "</span>\n";
	}
}
?>
</div>



</body>