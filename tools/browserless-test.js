const fs = require('fs/promises');
const path = require('path');
const { chromium } = require('playwright');

const invalidProductUrl = process.env.PRODUCT_URL || 'https://example.com/not-an-amazon-product';
const baseUrl = process.env.APP_URL || 'http://127.0.0.1:8080';
const screenshotsDir = path.join(process.cwd(), 'docs', 'screenshots');

const viewports = {
  desktop: { width: 1440, height: 900 },
  mobile: { width: 390, height: 844 },
};

async function assertVisible(page, selector, label) {
  const visible = await page.locator(selector).first().isVisible();

  if (!visible) {
    throw new Error(`${label} is not visible`);
  }
}

(async () => {
  await fs.mkdir(screenshotsDir, { recursive: true });

  const browser = await chromium.launch();

  for (const [name, viewport] of Object.entries(viewports)) {
    const page = await browser.newPage({ viewport });

    await page.goto(baseUrl, { waitUntil: 'networkidle' });
    await assertVisible(page, 'h1', `${name} heading`);
    await assertVisible(page, '#parser', `${name} parser panel`);
    await assertVisible(page, '#url', `${name} URL input`);
    await page.screenshot({ path: path.join(screenshotsDir, `${name}-empty.png`), fullPage: true });

    await page.fill('#url', invalidProductUrl);
    await Promise.all([
      page.waitForLoadState('networkidle'),
      page.click('button[type="submit"]'),
    ]);

    await assertVisible(page, '.result-card--error', `${name} error result`);
    await page.screenshot({ path: path.join(screenshotsDir, `${name}-error.png`), fullPage: true });

    const bodyText = await page.locator('body').innerText();
    console.log(`--- ${name} ---\n${bodyText}\n`);
    await page.close();
  }

  await browser.close();
})().catch((error) => {
  console.error(error);
  process.exit(1);
});
