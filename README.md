# Amazon 3D Model Parser

Small PHP tool to extract Amazon 3D model URLs from a product page.

GitHub repository name: `amazon-3d-model-parser`.

The script takes an Amazon product URL, requests the page server-side, looks for the 3D viewer link, and returns:

* the Amazon 3D viewer URL
* the related `.zip` file URL when a `physicalId` is found

## Requirements

* PHP 8.2+
* cURL enabled
* Composer

## Usage

Install dependencies:

```bash
composer install
```

Run locally:

```bash
php -S 127.0.0.1:8080
```

Open:

```txt
http://127.0.0.1:8080
```

Paste an Amazon product URL into the form, then submit.

Example Amazon URL:

```txt
https://www.amazon.fr/dp/B0CK3FLWYZ
```

## Tests

Run:

```bash
composer test
```

## Why PHP?

This tool needs to request and parse an Amazon page.

A static HTML page or GitHub Pages cannot do this reliably because browsers block cross-domain requests with CORS.

The server-side PHP request avoids this limitation.

## Notes

This tool only works when the Amazon product page contains a 3D viewer link.

If no `/view-3d` link is found, the script will return:

```txt
No 3d model found.
```

## Security

The script validates the Amazon host and only accepts `http` or `https` URLs.

The output is escaped before being displayed in the browser.

## License

MIT
