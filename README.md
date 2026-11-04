# JoomLeague site template

`tpl_joomleague` is the standalone development source for the configurable
JoomLeague site template. It is derived from the football demo presentation,
but uses a sport-neutral identity and has no dependency on demo menu IDs,
projects or content.

## Development status

Version `0.1.4-dev` is under active development. It is not deployed, published
or included in `pkg_joomleague`. The template will be added to the package only
after isolated installation, configuration, accessibility and responsive tests
are complete.

## Configuration

The template exposes Joomla-native settings for branding, colours, hero
content, layout, navigation, social links and footer content. Empty content
fields use translated sport-neutral defaults.

Run the local contract check with:

```bash
php tests/verify-template.php
```
