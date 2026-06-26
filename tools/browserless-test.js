const { chromium } = require('playwright');

const productUrl = process.env.PRODUCT_URL || 'https://amzn.to/4eMEcU2';
const baseUrl = process.env.APP_URL || 'http://127.0.0.1:8080';

const viewports = {
  desktop: { width: 1440, height: 900 },
  mobile: { width: 390, height: 844 },
};

(async () => {
  const browser = await chromium.launch();

  for (const [name, viewport] of Object.entries(viewports)) {
    const page = await browser.newPage({ viewport });
    await page.goto(baseUrl, { waitUntil: 'networkidle' });
    await page.screenshot({ path: `docs/screenshots/${name}-empty.png`, fullPage: true });

    await page.fill('#url', productUrl);
    await Promise.all([
      page.waitForLoadState('networkidle'),
      page.click('button'),
    ]);
    await page.screenshot({ path: `docs/screenshots/${name}-result.png`, fullPage: true });

    const bodyText = await page.locator('body').innerText();
    console.log(`--- ${name} ---\n${bodyText}\n`);
    await page.close();
  }

  await browser.close();
})().catch((error) => {
  console.error(error);
  process.exit(1);
});
