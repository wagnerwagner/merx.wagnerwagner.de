# Working on the Merx documentation

This repository is the Kirby site behind <https://merx.wagnerwagner.de>. The
plugin it documents lives in `site/plugins/merx`, as a submodule with its own
AGENTS.md.

## Merx is simple. The documentation has to look it

> Merx is designed to be as simple as possible. If you know Kirby, you'll be
> able to build a simple shop in no time.

That is the promise on the home page, and the documentation either backs it up
or quietly contradicts it. **The longer a page is, the more complicated the
software looks** — a reader who needs 800 words to switch a shop to production
concludes that switching a shop to production is hard.

So length is a cost, not a measure of effort. Before adding a paragraph, ask
what it buys the reader.

### Never use word count as a quality signal

Do not reason "this page has 96 words and that one has 628, so this one is
missing something". Pages are as long as their subject. PayPal is simpler than
Stripe, so its page is shorter — that is correct, not a gap.

A page is too thin when it leaves out something a reader needs and cannot find
elsewhere. Only that.

### Write each thing once

The reference is generated from the plugin's docblocks and is complete. A guide
page that restates it is pure weight, and it goes stale on the next release
while the reference does not.

- Do not list routes, options, methods or fields that the reference already
  lists. Link to it.
- Do not repeat a neighbouring guide page. Link to it.
- Prefer a sentence over a table. Most tables here were an enumeration of
  internals that nobody configures.

What belongs in the guide is the part reflection cannot produce: why something
works the way it does, which of several ways is the right one, and the traps.

### Except where repetition is the point

This rule is about prose a reader moves *through*. It does not apply to
surfaces a reader looks something up *in*.

The glossary is the clearest case. Somebody meets the word "pricing rule" and
wants to know what it means; an entry missing because the term has its own page
is a glossary that failed at its one job. It is an index, and completeness is
the feature — modelled on <https://getkirby.com/docs/glossary>, which likewise
defines Page, Template and Route even though each has a whole guide behind it.

Keep entries to one to three sentences and let each link onward once. Short and
complete, not short by omission.

### The shortest path first, extending it last

Order a page by what the reader is trying to do. The plain way of getting
the thing working comes first, and everything about bending it to your own
needs comes after — at the end of the page, not woven into the middle.

Somebody arriving at a page wants to be finished with it. Opening with the
model they could subclass, the option they could override or the edge case
they could hit makes an easy task read like a hard one, and they have to
wade through choices before they have anything running. The same material at
the bottom reads as "and when you need more, here is where to look".

So: page blueprint before your own page model, the default before the
option, the common case before the exception. `displaying-order` shows the
shape — template, then snippet, then "Your own order model".

### Signs a page has drifted

- A table where the rows are implementation details rather than decisions
- A section that ends up being a worse copy of a reference page
- Enumerating what a function does internally instead of what to do with it
- Explaining an option nobody has to touch

## Tone

Follow Kirby's documentation rather than Merx 1's. Measured over both, per 1000
words of prose:

| | contractions | spelled out |
| --- | --- | --- |
| getkirby.com | 3.7 | 3.4 |
| merx.wagnerwagner.de (v2) | 4.2 | 3.9 |

Roughly an even mix — contracted in walkthrough prose ("you don't have to"),
spelled out in callouts, safety instructions and definitions, where the flat
form carries weight.

- Sentence case for titles and headings. Not Title Case.
- `URL`, `Panel`, `JavaScript` — but `id`, `url` only inside code.
- US spelling: color, catalog, organization, localization.
- Typographic apostrophes (`’`), not `'`.
- One word per role: **visitor** while browsing, **customer** from checkout
  onward, **user** only for Kirby Panel accounts.
- Merx 2 stands on its own. Nothing in the docs compares it to Merx 1 except
  `/guide/upgrade-merx-1-to-2`.

## Mechanics

Content is Kirby text files under `content/`. Pages carry `Intro` (rendered
large, and used as the description in `llms.txt`), `Text`, and a `Uuid`.

**Never change or regenerate a `Uuid`.** Panel permalinks point at it. When
rewriting a page wholesale, copy the existing one across and diff before
committing.

Code blocks carry their file path in the info string:
` ```php title="/site/config/config.php" `. Always name a language before
`title`, otherwise the title is read as the language.
Callouts are GitHub alerts (`> [!CAUTION]`, then `> ` before every line). The
types are `NOTE`, `TIP`, `IMPORTANT`, `WARNING`, `CAUTION` — nothing else. An
unknown type renders as a plain blockquote.

Only `h2` reaches the table of contents (`m-toc.php` calls `toToc('h2')`), so a
section that should be navigable needs `##`.

## Verify before committing

The dev site is at <https://merx.wagnerwagner.test>. Every documentation page
is also served as Markdown by appending `.md`, and `llms.txt` indexes them all.

- Every page you touched still returns 200, and its `.md` renders
- Every internal link resolves — extract them and check, do not trust the eye
- PHP samples pass `php -l`: run `composer check-examples`, which checks every
  PHP code block of the documentation
- Code you documented actually runs. Samples in this repository have shipped
  with syntax errors, with Merx 1 APIs, and with calls that throw. Run it
  against the dev site rather than reasoning about it.
- Any fixture you created for a test is reverted
