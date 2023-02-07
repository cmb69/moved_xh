# Moved_XH

Moved_XH facilitates to cater to pages which have been renamed. Direkt links
to those pages will normally result in a 404 Page Not Found error, but
Moved_XH makes it possible to redirect incoming requests to another page or
to flag them as gone. Appropriate information is returned that informs bots
about the change, what is particularly important regarding search engines,
which can change the URL of the page and remove it from the index respectively.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
  - [Redirects](#redirects)
  - [Gone](#gone)
  - [Placeholders](#placeholders)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Moved_XH is a plugin for [CMSimple_XH](https://www.cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, and PHP ≥ 7.1.0.

## Download

The [lastest release](https://github.com/cmb69/moved_xh/releases/latest)
is available for download on Github.

## Installation

The installation is done as with many other CMSimple\_XH plugins. See the
[CMSimple_XH Wiki](https://wiki.cmsimple-xh.org/?for-users/working-with-the-cms/plugins#id3_install-plugin)
for further details.

1. **Backup the data on your server.**
1. Unzip the distribution on your computer.
1. Upload the whole directory `moved/` to your server into the `plugins/`
   directory of CMSimple_XH.
1. Set write permissions to the subfolders `css/` and `languages/`.
1. Navigate to `Plugins` → `Moved` to check if all requirements are
   fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the Website. Select `Plugins` → `Moved`.

Localization is done under `Language`. You can translate the character
strings to your own language if there is no appropriate language file available,
or customize them according to your needs.

The look of Moved_XH can be customized under `Stylesheet`.

## Usage

Moved_XH stores its data separately for each language of the CMSimple_XH
installation in a file `moved.txt` in the respective `content/` folder.
Incoming requests to pages that do not exist and have no rule
in `moved.txt` are logged in the log file of CMSimple_XH.
The description of the log entry contains the requested page and
the referrer, if known.

The rules can be edited in the plugin administration under `Main Settings`.
Each line of the file constitutes a rule. Rules consist of one
or two so called *page URL*s. Technically speaking, a *page URL*
is the name of the first parameter of the query string, i.e.
everything between the question mark (`?`) and the first
ampersand (`&`) resp. the end of the URL, if there is no
ampersand. It is recommended to copy the *page URL*s from the address
bar of the browser instead of entering them manually, because there will be
some surprises. For instance, the *page URL* of the fictious page
[Fahrvergnügen](https://www.example.com/?Fahrvergn%C3%BCgen) is
`Fahrvergn%C3%BCgen` and not `Fahrvergnügen` as one might expect.

There are two different types of rules:

### Redirects

Redirect rules will redirect incoming requests for an old *page URL* to
a new *page URL* or an external URL (which has to be fully qualified,
i.e. starting with the protocol, e.g. `https://`). Both URLs are
seperated by an equal sign (`=`).

Some examples:

You are restructuring your site and want to move the toplevel page
*Oaks* to the second level below *Trees*, so add the following rule:

    Oaks=Trees/Oaks

You have moved the page *Oaks* to another CMSimple_XH installation,
so add the following rule:

    Oaks=https://www.example.com/trees/?Oaks

You have upgraded from an *ISO-8859-1* encoded CMSimple version
to a *UTF-8* encoded CMSimple_XH installation, and you have a
page *Fahrvergnügen*, so add the following rule:

    Fahrverg%FCgen=Fahrvergn%C3%BCgen


### Gone

Gone rules will inform visitors that a page does no longer exists.
They consist of the *page URL* of the removed page.

For example, if you have removed the page *Temporary Information*
because it is no longer needed, so add the following rule:

    Temporary_Information


### Placeholders

The old *page URL* of the rules may contain placeholders, where a
`*` matches an arbitrary amount of characters and `?`
matches a single character.  Only the first matching rule will ever be used;
others will simply be ignored for the request.  Placeholders do not offer any
new features, but are rather meant to avoid repetitions of similar rules.
For example:

You have removed the pages *Temporary Information 1* and *Temporary Information 2*,
because they are no longer needed, so you could add the following rules:

    Temporary_Information_1
    Temporary_Information_2

By using placeholders, you can simplify, though:

    Temporary_Information_?

It is also possible to use whatever has been matched by a placeholder in the
new *Page URL* of a redirect rule (i.e. on the right hand side of the
equal sign).  This is done with variables of the form `$1`,
`$2` … `$9`, where `$1` matches the first placeholder,
`$2` the second, and so forth.

For example, you are restructuring your site and want to move the toplevel page
*Oaks* to the second level below *Trees*, where *Oaks* already has three subpages.
You could add the following rules:

    Oaks:White_Oak=Trees:Oaks:White_Oak
    Oaks:Blackjack_Oak=Trees:Oaks:Blackjack_Oak
    Oaks:Blue_Oak=Trees:Oaks:Blue_Oak

By using a placeholder, you can simplify, though:

    Oaks:*=Trees:Oaks:$1

## Limitations

Moved_XH uses the
[`custom_404()` hook](https://wiki.cmsimple-xh.org/?tips-and-tricks/custom-404-page&search=custom+404),
so it does not work if the hook is alread defined. In this
case you will get a blank browser window when you browse to your website.
Either uninstall Moved_XH or remove the existing `custom_404()` hook.

Moved_XH's rules do not work when you are logged in to the CMSimple_XH
installation, as the `custom_404()` hook funktion is not called
by CMSimple_XH in this case. If you want to test the rules without logging
out, you have to use a second browser.

## Troubleshooting

Report bugs and ask for support either on [Github](https://github.com/cmb69/moved_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Moved_XH is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Moved_XH is distributed in the hope that it will be useful,
but *without any warranty*; without even the implied warranty of
*merchantibility* or *fitness for a particular purpose*.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Moved_XH.  If not, see <https://www.gnu.org/licenses/>.

© 2013-2023 Christoph M. Becker

## Credits

The plugin logo is designed by [World Media Group LLC](https://www.mymovingreviews.com/).
Many thanks for publishing this icon under a liberal license.

Many thanks to the community at the [CMSimple_XH Forum](https://www.cmsimpleforum.com/)
for tips, suggestions and testing.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
