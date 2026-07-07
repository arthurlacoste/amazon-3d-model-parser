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
$hasSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($hasSubmitted) {
    $productUrl = AmazonProductUrl::fromString($inputUrl);

    if ($productUrl === null) {
        $error = 'Use a valid Amazon product URL, for example https://www.amazon.fr/dp/B0CK3FLWYZ.';
    } else {
        $result = (new AmazonPageClient())->fetch($productUrl);

        if (!$result->ok) {
            $error = 'Request failed: ' . $result->error;
        } else {
            $resolvedUrl = AmazonProductUrl::fromString((string) $result->effectiveUrl) ?? $productUrl;
            $modelLink = (new ModelLinkParser())->parse((string) $result->body, $resolvedUrl);
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
    <title>Amazon GLB Parser</title>
    <meta name="description" content="Extract Amazon 3D viewer and model archive URLs from product pages.">
    <link rel="stylesheet" href="assets/css/home-light.css?v=<?= filemtime(__DIR__ . '/assets/css/home-light.css') ?>">
</head>

<body>
    <div class="page-shell">
        <header class="topbar" aria-label="Application header">
            <nav class="topbar-nav" aria-label="Product status">
                <span>Made with ♥ by <a href="https://arthak.fr"><img class="avatar" src="https://arthak.fr/assets/img/me-arthak.webp" alt="arthak"> arthak</a></span>
            </nav>
        </header>

        <main>
            <section class="hero-copy" aria-labelledby="page-title">
                <p class="eyebrow">Single-purpose tool</p>
                <h1 id="page-title">Find Amazon 3D model links</h1>
                <p class="hero-text">
                    Paste an Amazon product page. The parser fetches the page, detects the 3D viewer data,
                    and returns the viewer URL plus the model archive when available.
                </p>

                <div class="pipeline-steps">
                    <span>Validate URL</span>
                    <span>Fetch page</span>
                    <span>Extract links</span>
                </div>
            </section>

            <section id="parser" class="app-panel" aria-label="Amazon product URL">
                <form method="post" class="parser-form">
                    <label for="url">Enter Amazon product URL</label>
                    <div class="form-row">
                        <input id="url" type="url" name="url" value="<?= Amazon3DModelParser\h($inputUrl) ?>"
                            placeholder="https://www.amazon.fr/dp/B0CK3FLWYZ" autocomplete="url" required>
                        <button type="submit">Extract links</button>
                    </div>
                    <p class="form-hint">Works best with regular product pages that include Amazon's 3D viewer.</p>
                </form>
            </section>

            <?php if ($error !== null): ?>
                <section class="result-card result-card--error" aria-live="polite">
                    <div class="result-icon" aria-hidden="true">!</div>
                    <div>
                        <p class="eyebrow">Extraction failed</p>
                        <h2>Could not extract a model link</h2>
                        <p><?= Amazon3DModelParser\h($error) ?></p>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ($modelLink instanceof ModelLink): ?>
                <section class="result-card result-card--success" aria-live="polite">
                    <div class="result-icon" aria-hidden="true">✓</div>
                    <div class="result-content">
                        <p class="eyebrow">Extraction complete</p>
                        <h2>Model links ready</h2>

                        <div class="link-result">
                            <span>3D Viewer</span>
                            <a target="_blank" rel="noopener"
                                href="<?= Amazon3DModelParser\h($modelLink->viewerUrl) ?>"><?= Amazon3DModelParser\h($modelLink->viewerUrl) ?></a>
                            <button type="button" class="copy-button"
                                data-copy="<?= Amazon3DModelParser\h($modelLink->viewerUrl) ?>">Copy</button>
                        </div>

                        <?php if ($modelLink->zipUrl !== null): ?>
                            <div class="link-result">
                                <span>ZIP archive</span>
                                <a target="_blank" rel="noopener"
                                    href="<?= Amazon3DModelParser\h($modelLink->zipUrl) ?>"><?= Amazon3DModelParser\h($modelLink->zipUrl) ?></a>
                                <button type="button" class="copy-button"
                                    data-copy="<?= Amazon3DModelParser\h($modelLink->zipUrl) ?>">Copy</button>
                            </div>
                        <?php else: ?>
                            <p class="empty-note">No ZIP archive was detected on this page.</p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="feature-grid" aria-label="App qualities">
                <article>
                    <strong>🎯 Focused</strong>
                    <p>One input, one job, no dashboard noise.</p>
                </article>
                <article>
                    <strong>📖 Readable</strong>
                    <p>Results are separated, clickable, and easy to copy.</p>
                </article>
                <article>
                    <strong>📦 Portable</strong>
                    <p>Plain PHP app. Easy to run locally or deploy.</p>
                </article>
            </section>
        </main>
    </div>

    <script src="assets/js/app.js" defer></script>
</body>

</html>