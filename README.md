# Kirby3 WellKnown

**Requirement:** Kirby 3 (3.0 RC2 or better)


## Coffee, Beer, etc.

This started as a simple plugin. The idea was to have a nice way to set some text files that live in the `.well-known` folder. During development I had endless hours disappearing down the rabbit hole that is the specifications for the different files that could exist there. I have held back and kept this plugin deliberately simple, and hope that you do use it because providing these well-known files is a good thing to do. Under-the-hood it has some complicated code to make this really easy for the user. It also generates `/robots.txt` because I just needed an excuse to integrate with my [omz13/kirby3-xmlsitemap](https://github.com/omz13/kirby3-xmlsitemap) plugin. It also has a built-in blueprint because I wanted to waste hours of time getting this to work (and I'm still not sure why that took me some long: perhaps I have no idea what I am doing and eventually hit on the magic incantation within the code by random luck of hitting the keyboard enough times? Did Bastian fix something in Kirby? Were my MAMP settings wrong? Who cares because it now works and it is really cool. There are now no spelling mistakes in this README now that I have tweaked my code editor ([Atom](https://atom.io)) to spellcheck.

This plugin is free but if you use it in a commercial project to show your support you are welcome to:
- [make a donation 🍻](https://www.paypal.me/omz13/10) or
- [buy me ☕☕](https://buymeacoff.ee/omz13) or
- [buy a Kirby license using this affiliate link](https://a.paddle.com/v2/click/1129/36191?link=1170)

## Documentation

### Purpose

For a kirby3 site, this plugin [omz13/wellknown](https://github.com/omz13/kirby3-welllknown) allows easy configuration and provision of [well-known](https://www.iana.org/assignments/well-known-uris/well-known-uris.xhtml) and other highly-useful files (_viz._ `robots.txt`)

When would you use this plugin?

- to provide [.well-known/security.txt](https://securitytxt.org)
- to provide a dnt-policy such as [.well-known/dnt-policy](https://eff.org/dnt-policy)
- to provide [.well-known/humans.txt](http://humanstxt.org)
- to provide any other (text-based) file in the `.well-known` folder.
- to provide [/robots.txt](https://en.wikipedia.org/wiki/Robots_exclusion_standard).

The functional specification:

- The contents for a well-known file are derived from, in order of priority, an entry from a configuration file, c.f. `site/config/config.php`, or a field in `content/site.txt`.
- For `robots.txt` file, the `sitemap` content is _automatically_ generated (i.e. interacts with [omz13/kirby3-xmlsitemap](https://github.com/omz13/kirby3-xmlsitemap)) and prepended to whatever else you want.

#### Roadmap

The non-binding list of planned features and implementation notes are:

- [x] MVP
- [ ] favicon
- [x] blueprints
- [ ] debug headers only in debug mode

### Installation

Pick one of the following per your epistemological model:

- `composer require omz13/kirby3-wellknown`; the plugin will automagically appear in `site/plugins`.
- Download a zip of the latest release - [master.zip](https://github.com/omz13/kirby3-wellknown/archive/master.zip) - and copy the contents to your `site/plugins/kirby3-wellknown`.
- `git submodule add https://github.com/omz13/kirby3-wellknown.git site/plugins/kirby3-wellknown`.

### Configuration

The following mechanisms can be used to modify the plugin's behavior.

#### via `site/config/config.php`

- `omz13.wellknown.disable` - optional - default `false` - a boolean which, if `true`, disables the plugin.

- `omz13.wellknown.notfound` - optional - default `true` - a boolean which, if `true`, causes the plugin to respond with a simple `404` instead of the default kirby error page for requests to any files that are not configured.

- `omz13.wellknown.not-txt-notfound` - optional - default `true` - a boolean which, if `true`, causes the plugin to respond with a simple `404` instead of the default kirby error page for all requests to `.well-known\file.ext` where ext is not a `txt`.

- ~~`omz13.wellknown.fromSite` - optional - default `false` - a boolean which, if `true`, causes the content for a well-known file from the site file (`content/Site.txt` which is set by the panel via `blueprint/site.yml`) to be used in preference to that from the configuration file (`site/config/config.php`). Wow. That was a complicated. In other words, if this is `true`, the user can specify the content in the panel, but if false the content can only be specified in the configuration file (for those times when you do not want a user to be able to change things).~~

- `omz13.wellknown.the-XXXX` - optional - a string which provides the content for a request to `.well-known/XXXX.txt` (or `the-robots` for `/robots.txt`). Note that hyphens are ignored (e.g. `dnt-policy` would be specified as `omz13.wellknown.the-dntpolicy`). Additionally, line expansion is performed (i.e. any occurrences of `\n` will be replaced by a newline).

- `omz13.wellknown.x-ping` - optional - if `true`, then any request to `/.well-known/ping.txt` will return a very boring and vanilla response of `pong`. Why would you use this? Because it is a very sweet endpoint to test against to see if your site is alive.

#### Content fields in `content/site.txt` (via blueprint `blueprint/site.yml`)

The plugin uses the following content fields. These are all optional; if missing or empty, they are assumed to be not applicable vis-à-via their indicated functionality.

- `wellknownXXXX` - text - optional - content to provide for well-known file `XXXX.txt`. Note that hyphens are ignored (e.g. the well-known file `dnt-policy.txt` would be specified as field `wellknowndntpolicy`.)

#### Blueprints

This plugin provides the following built-in blueprints (e.g. to make adding fields into the panel's blueprint `blueprint/site.yml` easier):

- `omz13/wellknown` - to be used as an `extends` to make entering data for `robots.txt`, `security.txt`, `dnt-policy.txt`, and `humans.txt`, as easy as:

```yaml
HeadlineWellKnown:
  type: headline
  label: well-known
  numbered: false

TheWellKnown:
  extends: omz13/wellknown
```

Hint: If you want to see what is in this blueprint, look in the source code under the `blueprints` folder.

### Use

1. Configure as above.

2. Use a web browser or whatever to access the well-known files.

3. If it works, see _Coffee, Beer, etc_ above

4. If it doesn't work... file an issue and I will bang my head against the wall while I fix things; or perhaps I'll just sulk, have a cup of really stong coffee, and the got fix thing.

5. Be amazed how my README are either more funny or less funny with each push to the repo; YMMV.

#### Debug mode

If the kirby site is in debug mode:

- Page requests to any well-known files will have a header `x-omz13-wk` that contains debugging information.

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/omz13/kirby3-wellknown/issues/new).

## License

[BSD-3-Clause](https://opensource.org/licenses/BSD-3-Clause)
