const { chromium } = require('playwright');

const productUrl = process.env.PRODUCT_URL || 'https://www.amazon.fr/IDMarket-Meuble-Phoenix-avec-tiroirs/dp/B0CK3FLWYZ/ref=sr_1_6?crid=1Q1QMCCV3S51L&dib=eyJ2IjoiMSJ9.TReINnO7rGtzG3MW2Rfqx6_BftTELNmr13RSFOjOYQsZVPLM_P7sxv_CcXglHTcX9AcxlxRVqJs_KVgvmmOJj1JW2p3mVV-JKlKzFavQ9sf7XwuZ1QzfAfiQ_C8LEh7dmWWqPNtshk1AL_Forep4jHFkW7LbIbRvKhDeek-Zxl7jxt4YDafD12fRgui63U7VwfoQwT7BV0z2lf3jjYQ4BLN52mi45_MWBigP2hIXL3uHvPtwfNWZvqM5iuTCHSLwOKoP6RxPlq-cW3hMHnJizRSd67AfuA3fQSxfYdWIvhI.Kh325VtXkstEuOAOOVp0kiVQXOPTrM7sR-oACp6S5T4&dib_tag=se&keywords=meuble+tv&qid=1782486501&sprefix=meuble%2Caps%2C316&sr=8-6';
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
