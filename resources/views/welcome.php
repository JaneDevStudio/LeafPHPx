<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LeafPHP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{margin:0;background:#f6f6f6;font:14px/1.4 system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial;color:#333}
        /* ---- 顶栏 ---- */
        #top{height:56px;background:#4F5B93;display:flex;align-items:center;padding:0 20px;box-shadow:0 2px 4px rgba(0,0,0,.1)}
        #logo{width:32px;height:32px;background:#fff;border-radius:4px;display:flex;align-items:center;justify-content:center;font-weight:700;color:#4F5B93;font-size:18px}
        #top span{margin-left:12px;font-size:18px;color:#fff;letter-spacing:.5px}
        /* ---- 欢迎块 ---- */
        #welcome{max-width:600px;margin:80px auto 0;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.05);padding:48px 40px;text-align:center}
        h1{margin:0 0 16px;font-size:28px;font-weight:600}
        p{margin:0 0 24px;color:#555}
        .btn{display:inline-block;background:#4F5B93;color:#fff;padding:10px 20px;border-radius:4px;text-decoration:none;font-size:15px;transition:background .2s}
        .btn:hover{background:#3e4a75}
    </style>
</head>
<body>
<header id="top">
    <div id="logo">LP</div>
    <span>LeafPHP</span>
</header>

<main id="welcome">
    <h1>Welcome to LeafPHP</h1>
    <p>Get started by running <code>php bin/leaf make:controller Demo</code> or edit <code>app/Controllers/ExampleController.php</code>.</p>
    <a href="https://github.com/your-repo/LeafPHP" target="_blank" class="btn">View on GitHub</a>
</main>
</body>
</html>
