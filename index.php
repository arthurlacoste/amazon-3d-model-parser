<?php

declare(strict_types=1);

use Amazon3DModelParser\AmazonPageClient;
use Amazon3DModelParser\AmazonProductUrl;
use Amazon3DModelParser\ModelLink;
use Amazon3DModelParser\ModelLinkParser;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/View.php';

$inputUrl = trim((string) ($_POST['url'] ?? ''));
$error = null;
$modelLink = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productUrl = AmazonProductUrl::fromString($inputUrl);

    if ($productUrl === null) {
        $error = 'Use a valid Amazon product URL, for example https://www.amazon.fr/dp/B0CK3FLWYZ.';
    } else {
        $result = (new AmazonPageClient())->fetch($productUrl);

        if (!$result->ok) {
            $error = 'Request failed: ' . $result->error;
        } else {
            $modelLink = (new ModelLinkParser())->parse((string) $result->body, $productUrl);
            $error = $modelLink instanceof ModelLink ? null : 'No 3D model found.';
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amazon 3D Model Parser</title>
    <link rel="stylesheet" href="assets/css/home-light.css">
</head>
<body>
    <main class="shell">
        <h1>Amazon 3D Model Parser</h1>

        <form method="post" class="parser-form">
            <label for="url">Amazon product URL</label>
            <div class="form-row">
                <input id="url" type="url" name="url" value="<?= Amazon3DModelParser\h($inputUrl) ?>" placeholder="https://www.amazon.fr/dp/B0CK3FLWYZ" required>
                <button type="submit">Get model URL</button>
            </div>
        </form>

        <?php if ($error !== null): ?>
            <section class="result error" aria-live="polite">
                <?= Amazon3DModelParser\h($error) ?>
            </section>
        <?php endif; ?>

        <?php if ($modelLink instanceof ModelLink): ?>
            <section class="result" aria-live="polite">
                <p>
                    <strong>3D Viewer</strong>
                    <a target="_blank" rel="noopener" href="<?= Amazon3DModelParser\h($modelLink->viewerUrl) ?>"><?= Amazon3DModelParser\h($modelLink->viewerUrl) ?></a>
                </p>
                <?php if ($modelLink->zipUrl !== null): ?>
                    <p>
                        <strong>Zip file</strong>
                        <a target="_blank" rel="noopener" href="<?= Amazon3DModelParser\h($modelLink->zipUrl) ?>"><?= Amazon3DModelParser\h($modelLink->zipUrl) ?></a>
                    </p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
