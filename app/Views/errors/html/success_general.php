<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($title) ?></title>

    <style>
        div.logo {
            height: 200px;
            width: 155px;
            display: inline-block;
            opacity: 0.08;
            position: absolute;
            top: 2rem;
            left: 50%;
            margin-left: -73px;
        }
        body {
            height: 100%;
            background: #1a1a1a;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #e0e0e0;
            font-weight: 300;
        }
        h1 {
            font-weight: lighter;
            letter-spacing: normal;
            font-size: 3rem;
            margin-top: 0;
            margin-bottom: 0;
            color: #51cf66;
        }
        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #2d2d2d;
            text-align: center;
            border: 1px solid #51cf66;
            border-radius: 0.5rem;
            position: relative;
            box-shadow: 0 4px 12px rgba(81, 207, 102, 0.3);
        }
        pre {
            white-space: normal;
            margin-top: 1.5rem;
            color: #e0e0e0;
        }
        code {
            background: #1a1a1a;
            border: 1px solid #51cf66;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: block;
            color: #e0e0e0;
        }
        p {
            margin-top: 1.5rem;
            color: #e0e0e0;
        }
        .footer {
            margin-top: 2rem;
            border-top: 1px solid #444;
            padding: 1em 2em 0 2em;
            font-size: 85%;
            color: #999;
        }
        a:active,
        a:link,
        a:visited {
            color: #51cf66;
        }
        .success-icon {
            font-size: 4rem;
            color: #51cf66;
            margin-bottom: 1rem;
        }
        .redirect-link {
            margin-top: 2rem;
            padding: 0.75rem 1.5rem;
            background: #51cf66;
            color: #fff;
            text-decoration: none;
            border-radius: 0.25rem;
            display: inline-block;
            transition: background 0.3s;
        }
        .redirect-link:hover {
            background: #40c057;
        }
        .redirect-info {
            margin-top: 1rem;
            font-size: 0.9rem;
            color: #bbb;
        }
        #countdown {
            color: #51cf66;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="success-icon">✓</div>
        <h1><?= esc($title) ?></h1>

        <p>
            <?= nl2br(esc($message)) ?>
        </p>

        <?php if (!empty($redirect_url)) : ?>
            <div class="redirect-info">
                <p>页面将在 <span id="countdown">3</span> 秒后自动跳转...</p>
                <a href="<?= esc($redirect_url, 'attr') ?>" class="redirect-link">立即跳转</a>
            </div>
            <script>
                let countdown = 3;
                const countdownElement = document.getElementById('countdown');
                const timer = setInterval(function() {
                    countdown--;
                    if (countdownElement) {
                        countdownElement.textContent = countdown;
                    }
                    if (countdown <= 0) {
                        clearInterval(timer);
                        window.location.href = '<?= esc($redirect_url, 'js') ?>';
                    }
                }, 1000);
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
