# Links Improver

**Improve the navigability of a website** by improving your internal linking
... so **Increase your search rankings**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/piedweb/LinksImprover.svg?style=flat-square)](https://packagist.org/packages/piedweb/LinksImprover)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/piedweb/LinksImprover/run-tests?label=tests)](https://github.com/piedweb/LinksImprover/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/LinksImprover.svg?style=flat-square)](https://packagist.org/packages/piedweb/LinksImprover)

**You are not a developper and want a ready product like a wordpress plugin, see [Link Whisper](https://linkwhisper.com/ref/644/) (af link).**

## Description

From a list (raw or file) with this format :

> url,kws[separate by `,` and can use wildcard (`*`)],force (default 0),counter

(force and counter are facultatives)

The code analyse a content to extrat existing links and word count.

Then, from our list, it adds links to text from one kws suggested if :

-   the kw is in the text (for sure) (en vérifiant que le précédent tag est un <p.\*>),
-   the link does not exist yet
-   the number of link is not exceeded a number (eg: 10 links in the content) or a ratio (eg: 1 link for 50 words)

Then update link counter

If you edit a second content, I advice you to `reOrder` the link list to permit link rotation (the just created links
will go at the end of the list respecting their `force` value if it's set).

## Support this package

By speaking about and link `piedweb.com` on your website.

## Usage

### Installation

```bash
composer require piedweb/linksimprover
```

### Example

```php

include 'vendor/autoload.php';

use Piedweb\LinksImprover\LinksManager;
use Piedweb\LinksImprover\LinksImprover;

$content = '<p>My blog post content where I want to add few links to other page to get better pos on google.</p>';

$base = 'https://piedweb.com'; // my blog

// Get your page you want increment the list and on wich kw
$linksList = 'url,kws,force,counter
https://piedweb.com/,"Pied Web,Robin from Pied Web"
https://google.com/,"Google,google.com"';

$linksManager = LinksManager::load($linksList, $base);

$linksImprover = new LinksImprover($content);

$newContent = $linksImprover->improve($linksManager, 1 / 20, 'style=color:black'); // 1 link every 20 words max, it's huge :)

echo $newContent;

// Then reorder link list before submit a new content

$linksManager->reOrder();
```

Will return

```html
<p>
    My blog post content where I want to add few links to other page to get
    better pos on <a href="https://google.com" style="color:black">google</a>.
</p>
```

Other method

```php
$linksImprover->getAddedLinksCount();
$linksImprover->resetAddedLinkCount();
```

### Warning (and benefit)

It don't compare link with or without host. So you can use it to link external website...

but be careful to ManageLinks with host and having a proper content (link without host).

## Testing

```bash
composer test
```

## Credits

-   Robin from [Pied Web](https://piedweb.com)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
