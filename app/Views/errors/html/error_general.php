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
            color: #ff6b6b;
        }
        .wrap {
            max-width: 1024px;
            margin: 5rem auto;
            padding: 2rem;
            background: #2d2d2d;
            text-align: center;
            border: 1px solid #ff6b6b;
            border-radius: 0.5rem;
            position: relative;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }
        pre {
            white-space: normal;
            margin-top: 1.5rem;
            color: #e0e0e0;
        }
        code {
            background: #1a1a1a;
            border: 1px solid #ff6b6b;
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
            color: #ff6b6b;
        }
        .error-icon {
            font-size: 4rem;
            color: #ff6b6b;
            margin-bottom: 1rem;
        }
        .redirect-link {
            margin-top: 2rem;
            padding: 0.75rem 2rem;
            background: #ff6b6b;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 0.5rem;
            display: inline-block;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid #ff6b6b;
            box-shadow: 0 4px 8px rgba(255, 107, 107, 0.4);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            letter-spacing: 0.5px;
        }
        .redirect-link:hover {
            background: #ff5252;
            border-color: #ff5252;
            box-shadow: 0 6px 12px rgba(255, 107, 107, 0.6);
            transform: translateY(-2px);
        }
        .redirect-link:active {
            transform: translateY(0);
        }
        .redirect-info {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="error-icon">✗</div>
        <h1><?= esc($title) ?></h1>

        <p>
            <?= nl2br(esc($message)) ?>
        </p>

        <?php if (!empty($redirect_url)) : ?>
            <div class="redirect-info">
                <a href="<?= esc($redirect_url, 'attr') ?>" class="redirect-link">Go Back</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
